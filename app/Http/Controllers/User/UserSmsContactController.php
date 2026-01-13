<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SmsContact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserSmsContactController extends Controller
{
    /**
     * Display list of user contacts
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = SmsContact::where('owner_id', $user->id)
            ->where('owner_type', get_class($user));

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $contacts = $query->latest()->paginate(20);
        $groups = SmsContact::getUniqueGroups($user->id, get_class($user));
        $groupsCount = count($groups);

        return view('user.sms.contacts.index', compact('contacts', 'groups', 'groupsCount'));
    }

    /**
     * Show form to create single contact
     */
    public function create()
    {
        return view('user.sms.contacts.create');
    }

    /**
     * Store a single contact
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'group' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check for duplicate
        $exists = SmsContact::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->where('phone_number', $request->phone_number)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'This phone number already exists in your contacts database.');
        }

        SmsContact::create([
            'owner_id' => $user->id,
            'owner_type' => get_class($user),
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'email' => $request->email,
            'group' => $request->group,
            'notes' => $request->notes,
        ]);

        return redirect()->route('user.sms.contacts.index')
            ->with('success', 'Contact added successfully!');
    }

    /**
     * Show bulk upload form
     */
    public function bulkCreate()
    {
        return view('user.sms.contacts.bulk-upload');
    }

    /**
     * Process bulk Excel upload
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'group' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $file = $request->file('excel_file');

        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $imported = 0;
            $skipped = 0;
            $errors = [];

            // Skip header row if it exists
            $startRow = 0;
            if (count($rows) > 0 && !is_numeric($rows[0][0])) {
                $startRow = 1;
            }

            // Get existing phone numbers for this user
            $existingNumbers = SmsContact::where('owner_id', $user->id)
                ->where('owner_type', get_class($user))
                ->pluck('phone_number')
                ->toArray();

            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Skip empty rows
                if (empty($row[0]) || trim($row[0]) === '') {
                    continue;
                }

                $phoneNumber = trim($row[0]);
                $name = isset($row[1]) ? trim($row[1]) : null;
                $email = isset($row[2]) ? trim($row[2]) : null;
                $groupName = $request->group ?: (isset($row[3]) ? trim($row[3]) : null);

                // Check if phone number already exists (skip duplicates)
                if (in_array($phoneNumber, $existingNumbers)) {
                    $skipped++;
                    continue;
                }

                // Validate phone number format (basic validation)
                if (strlen($phoneNumber) < 10 || strlen($phoneNumber) > 20) {
                    $errors[] = "Row " . ($i + 1) . ": Invalid phone number format - {$phoneNumber}";
                    continue;
                }

                // Create the contact
                SmsContact::create([
                    'owner_id' => $user->id,
                    'owner_type' => get_class($user),
                    'phone_number' => $phoneNumber,
                    'name' => $name,
                    'email' => $email,
                    'group' => $groupName,
                ]);

                // Add to existing numbers to prevent duplicates within the same file
                $existingNumbers[] = $phoneNumber;
                $imported++;
            }

            $message = "Bulk upload completed! Imported: {$imported} contacts.";
            if ($skipped > 0) {
                $message .= " Skipped: {$skipped} duplicates.";
            }

            if (count($errors) > 0) {
                $message .= " Errors: " . count($errors) . " rows had issues.";
                session()->flash('upload_errors', $errors);
            }

            return redirect()->route('user.sms.contacts.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to process Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Delete a contact
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $contact = SmsContact::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->findOrFail($id);

        $contact->delete();

        return back()->with('success', 'Contact deleted successfully!');
    }

    /**
     * Download sample Excel template
     */
    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sms-contacts-sample.csv"',
        ];

        $columns = ['Phone Number', 'Name', 'Email', 'Group'];

        $sampleData = [
            ['0241234567', 'John Doe', 'john@example.com', 'Customers'],
            ['0551234567', 'Jane Smith', 'jane@example.com', 'VIP'],
            ['0201234567', 'Bob Johnson', 'bob@example.com', 'Customers'],
        ];

        $callback = function() use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
