<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplementaryTicket;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventTicket;
use App\Models\Admin;
use App\Services\NotificationService;
use App\Services\TicketGeneratorService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ComplementaryTicketController extends Controller
{
    protected NotificationService $notificationService;
    protected TicketGeneratorService $ticketGenerator;
    protected QrCodeService $qrCodeService;

    public function __construct(
        NotificationService $notificationService,
        TicketGeneratorService $ticketGenerator,
        QrCodeService $qrCodeService
    ) {
        $this->notificationService = $notificationService;
        $this->ticketGenerator = $ticketGenerator;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display list of all complementary tickets
     */
    public function index(Request $request)
    {
        $query = ComplementaryTicket::with(['event', 'issuedBy'])
            ->latest();

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by purpose
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        // Search by recipient
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('ticket_reference', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => ComplementaryTicket::sum('quantity'),
            'active' => ComplementaryTicket::where('status', 'active')->sum('quantity'),
            'used' => ComplementaryTicket::where('status', 'used')->sum('quantity'),
            'cancelled' => ComplementaryTicket::where('status', 'cancelled')->sum('quantity'),
        ];

        // Get events for filter
        $events = Event::whereNull('company_id') // Only platform events
            ->orWhereNotNull('company_id') // All events on platform
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.complementary-tickets.index', compact('tickets', 'stats', 'events'));
    }

    /**
     * Show form to create single complementary ticket
     */
    public function create()
    {
        return view('admin.complementary-tickets.create');
    }

    /**
     * Search for events (AJAX endpoint)
     */
    public function searchEvents(Request $request)
    {
        try {
            $search = $request->get('q', '');

            // Start query - exclude external events and load tickets
            $query = Event::where('is_external', false)
                ->with(['company:id,name', 'tickets' => function($q) {
                    $q->where('is_active', true);
                }]);

            // If search query provided, filter by title
            if (!empty($search)) {
                $query->where('title', 'like', "%{$search}%")
                    ->orderByRaw("CASE WHEN title LIKE ? THEN 1 ELSE 2 END", ["{$search}%"]); // Prioritize starts-with matches
            }

            // Order by date and get results
            $events = $query->orderBy('start_date', 'desc')
                ->get(['id', 'title', 'company_id', 'start_date']);

            $results = $events->map(function ($event) {
                // Get ticket prices from event_tickets table
                $generalPrice = 0;
                $vipPrice = 0;

                if ($event->tickets && $event->tickets->count() > 0) {
                    // Try to find tickets by name (General, VIP)
                    $generalTicket = $event->tickets->first(function($ticket) {
                        return stripos($ticket->name, 'general') !== false || stripos($ticket->name, 'regular') !== false;
                    });

                    $vipTicket = $event->tickets->first(function($ticket) {
                        return stripos($ticket->name, 'vip') !== false || stripos($ticket->name, 'premium') !== false;
                    });

                    // If specific tickets not found, use min and max prices
                    if (!$generalTicket && !$vipTicket) {
                        $generalPrice = $event->tickets->min('price') ?? 0;
                        $vipPrice = $event->tickets->max('price') ?? 0;
                    } else {
                        $generalPrice = $generalTicket ? $generalTicket->price : 0;
                        $vipPrice = $vipTicket ? $vipTicket->price : 0;
                    }
                }

                return [
                    'id' => $event->id,
                    'name' => $event->title,
                    'date' => $event->start_date ? $event->start_date->format('M d, Y') : 'Date TBA',
                    'organizer' => $event->company ? $event->company->name : 'Platform Event',
                    'general_price' => $generalPrice,
                    'vip_price' => $vipPrice,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Event search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Store single complementary ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_email' => 'required|email|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'ticket_type' => 'required|in:general,vip',
            'quantity' => 'required|integer|min:1|max:10',
            'purpose' => 'nullable|in:media,promoter,volunteer,influencer,student,sponsor,staff,other',
            'notes' => 'nullable|string|max:500',
            'visible_to_organizer' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $admin = Auth::guard('admin')->user();
            $event = Event::findOrFail($request->event_id);

            $eventTicket = $this->findEventTicket($event, $request->ticket_type);

            if (!$eventTicket) {
                throw new \Exception('No active tickets available for this event');
            }

            $originalPrice = $eventTicket->price;

            // Create complementary ticket record
            $ticket = ComplementaryTicket::create([
                'event_id' => $request->event_id,
                'issued_by' => $admin->id,
                'recipient_name' => $request->recipient_name,
                'recipient_email' => $request->recipient_email,
                'recipient_phone' => $request->recipient_phone,
                'ticket_type' => $request->ticket_type,
                'original_price' => $originalPrice,
                'quantity' => $request->quantity,
                'purpose' => $request->purpose,
                'notes' => $request->notes,
                'visible_to_organizer' => $request->has('visible_to_organizer'),
                'status' => 'active',
            ]);

            // Create EventAttendee for each quantity
            for ($i = 0; $i < $request->quantity; $i++) {
                // Generate unique ticket code
                $ticketCode = $this->ticketGenerator->generateTicketCode();

                // Create attendee
                $attendee = EventAttendee::create([
                    'event_id' => $event->id,
                    'event_order_id' => null, // No order for complementary tickets
                    'event_ticket_id' => $eventTicket->id,
                    'attendee_name' => $request->recipient_name,
                    'attendee_email' => $request->recipient_email,
                    'attendee_phone' => $request->recipient_phone,
                    'ticket_code' => $ticketCode,
                    'price_paid' => 0, // Complementary = free
                    'status' => 'valid',
                ]);

                // Generate QR code
                try {
                    $qrCodePath = $this->qrCodeService->generateTicketQrCode($ticketCode, $attendee->id);
                    $attendee->update(['qr_code_path' => $qrCodePath]);
                } catch (\Exception $e) {
                    Log::error('QR code generation failed: ' . $e->getMessage());
                }

                $sendSms = $i === 0;
                $result = $this->notificationService->sendComplementaryTicketNotifications($attendee, $sendSms);

                // Log notification results
                if (!$result['email_sent']) {
                    Log::warning("Complementary ticket email not sent for ticket {$ticket->id}");
                }
                if ($sendSms && !$result['sms_sent'] && !empty($request->recipient_phone)) {
                    Log::warning("Complementary ticket SMS not sent for ticket {$ticket->id}");
                }
            }

            DB::commit();

            return redirect()->route('admin.complementary-tickets.index')
                ->with('success', "Complementary ticket issued successfully to {$request->recipient_name}!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create complementary ticket', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return back()->withInput()
                ->with('error', 'Failed to issue complementary ticket: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk upload form
     */
    public function bulkCreate()
    {
        return view('admin.complementary-tickets.bulk-upload');
    }

    /**
     * Process bulk Excel upload
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'purpose' => 'nullable|in:media,promoter,volunteer,influencer,student,sponsor,staff,other',
            'visible_to_organizer' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $admin = Auth::guard('admin')->user();
            $event = Event::findOrFail($request->event_id);
            $file = $request->file('excel_file');

            // Load spreadsheet (detect CSV delimiter when needed)
            $extension = strtolower($file->getClientOriginalExtension() ?? '');
            if (in_array($extension, ['csv', 'txt'], true)) {
                $delimiter = $this->detectCsvDelimiter($file->getPathname());
                $reader = new Csv();
                $reader->setDelimiter($delimiter);
                $reader->setEnclosure('"');
                $reader->setEscapeCharacter('\\');
                $reader->setInputEncoding('UTF-8');
                $spreadsheet = $reader->load($file->getPathname());
            } else {
                $spreadsheet = IOFactory::load($file->getPathname());
            }
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $successCount = 0;
            $totalTicketsIssued = 0;
            $errors = [];

            $headerRowLooksLikeData = false;
            if (!empty($rows[0])) {
                foreach ($rows[0] as $value) {
                    $valueString = strtolower((string) $value);
                    if (str_contains($valueString, '@') || preg_match('/^[0-9]+$/', trim($valueString))) {
                        $headerRowLooksLikeData = true;
                        break;
                    }
                }
            }

            $header = $headerRowLooksLikeData ? [] : array_map(fn($value) => $this->normalizeHeader($value), $rows[0] ?? []);
            $columnMap = [
                'name' => $this->resolveHeaderIndex($header, ['name', 'full name', 'recipient name', 'attendee name']),
                'email' => $this->resolveHeaderIndex($header, ['email', 'recipient email', 'attendee email']),
                'phone' => $this->resolveHeaderIndex($header, ['phone', 'phone number', 'recipient phone', 'attendee phone']),
                'ticket_type' => $this->resolveHeaderIndex($header, [
                    'ticket type',
                    'tickettype',
                    'ticket_type',
                    'type',
                    'ticket',
                    'ticket category',
                    'category',
                    'ticket level',
                    'level',
                ]),
                'quantity' => $this->resolveHeaderIndex($header, [
                    'quantity',
                    'qty',
                    'tickets',
                    'ticket quantity',
                    'ticket qty',
                    'ticketqty',
                    'ticketquantity',
                    'no of tickets',
                    'no of ticket',
                    'nooftickets',
                    'noofticket',
                    'number of tickets',
                    'number of ticket',
                    'numberoftickets',
                    'numberofticket',
                    'ticket count',
                    'ticketcount',
                    'tickets count',
                    'ticketscount',
                    'count',
                    'total',
                    'totaltickets',
                    'amount',
                    'num tickets',
                    'num of tickets',
                    'numtickets',
                    'numoftickets',
                ]),
                'notes' => $this->resolveHeaderIndex($header, ['notes', 'note', 'remarks', 'remark']),
            ];

            $rowsToProcess = $headerRowLooksLikeData ? $rows : array_slice($rows, 1);
            $startingRowNumber = $headerRowLooksLikeData ? 1 : 2;

            foreach ($rowsToProcess as $index => $row) {
                $rowNumber = $index + $startingRowNumber;

                try {
                    $name = $this->getRowValue($row, $columnMap['name'], 0);
                    $email = $this->getRowValue($row, $columnMap['email'], 1);
                    $phone = $this->getRowValue($row, $columnMap['phone'], 2);
                    $ticketTypeRaw = $this->getRowValue($row, $columnMap['ticket_type'], 3);
                    [$ticketType, $ticketTypeValid] = $this->parseTicketType($ticketTypeRaw);
                    $quantityRaw = $this->getRowValue($row, $columnMap['quantity'], 4);
                    $quantity = $this->normalizeQuantity($quantityRaw);
                    $notes = $this->getRowValue($row, $columnMap['notes'], 5);

                    $quantityRawString = trim((string) $quantityRaw);
                    $quantityHasDigits = $quantityRawString !== '' && preg_match('/\d+/', $quantityRawString);
                    if (!$quantityHasDigits) {
                        $quantityGuess = $this->guessQuantityFromRow($row, array_filter([
                            $columnMap['name'],
                            $columnMap['email'],
                            $columnMap['phone'],
                            $columnMap['ticket_type'],
                            $columnMap['notes'],
                            $columnMap['quantity'],
                        ], fn ($value) => $value !== null));

                        if ($quantityGuess !== null) {
                            $quantity = $quantityGuess;
                        }
                    }

                    if ($quantity < 1 || $quantity > 10) {
                        $quantityGuess = $this->guessQuantityFromRow($row, array_filter([
                            $columnMap['name'],
                            $columnMap['email'],
                            $columnMap['phone'],
                            $columnMap['ticket_type'],
                            $columnMap['notes'],
                            $columnMap['quantity'],
                        ], fn ($value) => $value !== null));

                        if ($quantityGuess !== null) {
                            $quantity = $quantityGuess;
                        }
                    }

                    // Validate row data
                    if (empty($name) || empty($email)) {
                        $errors[] = "Row {$rowNumber}: Name and email are required";
                        continue;
                    }

                    if (!$ticketTypeValid) {
                        $errors[] = "Row {$rowNumber}: Invalid ticket type '{$ticketTypeRaw}'. Use 'general' or 'vip'";
                        continue;
                    }

                    if ($quantity < 1 || $quantity > 10) {
                        $errors[] = "Row {$rowNumber}: Quantity must be between 1 and 10";
                        continue;
                    }

                    $eventTicket = $this->findEventTicket($event, $ticketType);

                    if (!$eventTicket) {
                        $errors[] = "Row {$rowNumber}: No active tickets available for this event";
                        continue;
                    }

                    $originalPrice = $eventTicket->price;

                    // Create ticket
                    $ticket = ComplementaryTicket::create([
                        'event_id' => $request->event_id,
                        'issued_by' => $admin->id,
                        'recipient_name' => $name,
                        'recipient_email' => $email,
                        'recipient_phone' => $phone ?: null,
                        'ticket_type' => $ticketType,
                        'original_price' => $originalPrice,
                        'quantity' => $quantity,
                        'purpose' => $request->purpose,
                        'notes' => $notes,
                        'visible_to_organizer' => $request->has('visible_to_organizer'),
                        'status' => 'active',
                    ]);

                    // Create EventAttendee for each quantity
                    for ($i = 0; $i < $quantity; $i++) {
                        $ticketCode = $this->ticketGenerator->generateTicketCode();

                        $attendee = EventAttendee::create([
                            'event_id' => $event->id,
                            'event_order_id' => null, // No order for complementary tickets
                            'event_ticket_id' => $eventTicket->id,
                            'attendee_name' => $name,
                            'attendee_email' => $email,
                            'attendee_phone' => $phone ?: null,
                            'ticket_code' => $ticketCode,
                            'price_paid' => 0,
                            'status' => 'valid',
                        ]);

                        // Generate QR code
                        try {
                            $qrCodePath = $this->qrCodeService->generateTicketQrCode($ticketCode, $attendee->id);
                            $attendee->update(['qr_code_path' => $qrCodePath]);
                        } catch (\Exception $e) {
                            Log::error('QR code generation failed: ' . $e->getMessage());
                        }

                        $this->notificationService->sendComplementaryTicketNotifications($attendee, $i === 0);
                    }

                    $successCount++;
                    $totalTicketsIssued += $quantity;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    Log::error("Bulk upload error on row {$rowNumber}", [
                        'error' => $e->getMessage(),
                        'row' => $row,
                    ]);
                }
            }

            DB::commit();

            $message = "{$totalTicketsIssued} complementary tickets issued across {$successCount} row(s)!";
            if (!empty($errors)) {
                $message .= " However, " . count($errors) . " rows had errors.";
            }

            return redirect()->route('admin.complementary-tickets.index')
                ->with('success', $message)
                ->with('errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk upload failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Bulk upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Toggle visibility for organizer
     */
    public function toggleVisibility(Request $request, $id)
    {
        $ticket = ComplementaryTicket::findOrFail($id);

        $ticket->update([
            'visible_to_organizer' => !$ticket->visible_to_organizer,
        ]);

        $status = $ticket->visible_to_organizer ? 'visible' : 'hidden';
        return back()->with('success', "Ticket visibility changed to {$status} for organizer");
    }

    /**
     * Toggle global visibility for an event
     */
    public function toggleEventVisibility(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $event->update([
            'show_complementary_stats' => !$event->show_complementary_stats,
        ]);

        $status = $event->show_complementary_stats ? 'visible' : 'hidden';

        return back()->with('success', "Complementary stats are now {$status} for {$event->title}");
    }

    /**
     * Cancel a complementary ticket
     */
    public function cancel($id)
    {
        $ticket = ComplementaryTicket::findOrFail($id);
        $ticket->cancel();

        return back()->with('success', 'Complementary ticket cancelled successfully');
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="complementary_tickets_template.csv"',
        ];

        $csvData = "Name,Email,Phone,Ticket Type,Quantity,Notes\n";
        $csvData .= "John Doe,john@example.com,0241234567,general,1,Media representative\n";
        $csvData .= "Jane Smith,jane@example.com,0551234567,vip,2,Event sponsor\n";

        return response($csvData, 200, $headers);
    }

    private function normalizeHeader($value): string
    {
        $value = strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9]+/', '', $value);
        return $value ?? '';
    }

    private function resolveHeaderIndex(array $headers, array $aliases): ?int
    {
        $normalizedAliases = array_map(fn($alias) => $this->normalizeHeader($alias), $aliases);

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }
            foreach ($normalizedAliases as $alias) {
                if ($alias === '') {
                    continue;
                }
                if ($header === $alias || str_contains($header, $alias) || str_contains($alias, $header)) {
                    return $index;
                }
            }
        }

        return null;
    }

    private function getRowValue(array $row, ?int $index, int $fallbackIndex)
    {
        $value = null;

        if ($index !== null && array_key_exists($index, $row)) {
            $value = $row[$index];
        } elseif (array_key_exists($fallbackIndex, $row)) {
            $value = $row[$fallbackIndex];
        }

        return is_string($value) ? trim($value) : $value;
    }

    private function parseTicketType($raw): array
    {
        $rawValue = trim((string) $raw);
        if ($rawValue === '') {
            return ['general', true];
        }

        $normalized = preg_replace('/[^a-z]/', '', strtolower($rawValue));
        if (str_contains($normalized, 'vip') || str_contains($normalized, 'premium')) {
            return ['vip', true];
        }

        if (str_contains($normalized, 'general') || str_contains($normalized, 'regular') || str_contains($normalized, 'standard') || str_contains($normalized, 'basic')) {
            return ['general', true];
        }

        return ['general', false];
    }

    private function normalizeQuantity($raw): int
    {
        if ($raw === null || $raw === '') {
            return 1;
        }

        if (is_numeric($raw)) {
            return (int) round((float) $raw);
        }

        $rawString = trim((string) $raw);
        $normalizedNumber = str_replace(',', '.', $rawString);
        if (preg_match('/^\d+(\.\d+)?$/', $normalizedNumber)) {
            return (int) round((float) $normalizedNumber);
        }

        $digits = preg_replace('/[^0-9]/', '', $rawString);
        if ($digits === '') {
            return 1;
        }

        return (int) $digits;
    }

    private function guessQuantityFromRow(array $row, array $excludeIndexes): ?int
    {
        foreach ($row as $index => $value) {
            if (in_array($index, $excludeIndexes, true)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            if (is_numeric($value)) {
                $intValue = (int) $value;
                if ($intValue >= 1 && $intValue <= 10) {
                    return $intValue;
                }
                continue;
            }

            $valueString = trim((string) $value);
            if ($valueString === '' || str_contains($valueString, '@')) {
                continue;
            }

            if (preg_match('/^\d{1,2}$/', $valueString)) {
                $intValue = (int) $valueString;
                if ($intValue >= 1 && $intValue <= 10) {
                    return $intValue;
                }
            }

            if (!preg_match('/\d{3,}/', $valueString) && preg_match('/\d{1,2}/', $valueString, $matches)) {
                $intValue = (int) $matches[0];
                if ($intValue >= 1 && $intValue <= 10) {
                    return $intValue;
                }
            }
        }

        return null;
    }

    private function normalizeTicketName(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($name));
    }

    private function detectCsvDelimiter(string $path): string
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return ',';
        }

        $line = fgets($handle);
        fclose($handle);

        if ($line === false) {
            return ',';
        }

        $delimiters = [
            ',' => substr_count($line, ','),
            "\t" => substr_count($line, "\t"),
            ';' => substr_count($line, ';'),
            '|' => substr_count($line, '|'),
        ];

        arsort($delimiters);
        $bestDelimiter = array_key_first($delimiters);

        if ($bestDelimiter === null || $delimiters[$bestDelimiter] === 0) {
            return ',';
        }

        return $bestDelimiter;
    }

    private function findEventTicket(Event $event, string $ticketType)
    {
        $tickets = $event->tickets()->where('is_active', true)->get();

        if ($tickets->isEmpty()) {
            return null;
        }

        $match = $tickets->first(function ($ticket) use ($ticketType) {
            $normalized = $this->normalizeTicketName($ticket->name);
            if ($ticketType === 'vip') {
                return str_contains($normalized, 'vip') || str_contains($normalized, 'premium');
            }

            return str_contains($normalized, 'general') || str_contains($normalized, 'regular') || str_contains($normalized, 'standard');
        });

        if ($match) {
            return $match;
        }

        return $ticketType === 'vip'
            ? $tickets->sortByDesc('price')->first()
            : $tickets->sortBy('price')->first();
    }
}
