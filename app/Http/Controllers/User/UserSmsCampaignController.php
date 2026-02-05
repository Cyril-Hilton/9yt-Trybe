<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SmsCampaign;
use App\Models\SmsSenderId;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserSmsCampaignController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display campaigns list
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = SmsCampaign::where('owner_id', $user->id)->where('owner_type', get_class($user));

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->latest()->paginate(15);

        return view('user.sms.campaigns.index', compact('campaigns'));
    }

    /**
     * Show form to send SMS (Excel or Instant messaging)
     */
    public function create()
    {
        $user = Auth::user();

        // Get approved sender IDs for this user only (exclude admin-owned sender IDs)
        $senderIds = SmsSenderId::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->where('owner_type', '!=', 'App\\Models\\Admin') // Exclude admin sender IDs like "9yt Trybe"
            ->where('status', 'approved')
            ->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($user);

        // Get contact groups
        $groups = \App\Models\SmsContact::getUniqueGroups($user->id, get_class($user));

        // Get all contacts for selection
        $contacts = \App\Models\SmsContact::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->orderBy('name')
            ->get();

        return view('user.sms.campaigns.create', compact('senderIds', 'creditBalance', 'groups', 'contacts'));
    }

    /**
     * Send SMS (handles both Excel and Instant messaging)
     */
    public function send(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'sender_id' => 'nullable|string',
            'recipient_type' => 'required|in:manual,contacts,group,excel',
            'recipients' => 'required_if:recipient_type,manual|nullable|string',
            'contact_ids' => 'required_if:recipient_type,contacts|nullable|array',
            'contact_ids.*' => 'exists:sms_contacts,id',
            'group' => 'required_if:recipient_type,group|nullable|string',
            'excel_file' => 'required_if:recipient_type,excel|nullable|file|mimes:xlsx,xls,csv',
            'schedule_type' => 'required|in:now,later',
            'scheduled_at' => 'required_if:schedule_type,later|nullable|date|after:now',
        ]);

        $user = Auth::user();

        // Handle Excel messaging with column placeholders differently
        if ($request->recipient_type === 'excel') {
            return $this->handleExcelMessaging($request, $user);
        }

        // Prepare recipients based on messaging type
        $recipients = $this->prepareRecipients($request);

        if (empty($recipients)) {
            return back()->withInput()->with('error', 'No valid recipients found.');
        }

        // Send now or schedule
        if ($request->schedule_type === 'later') {
            $result = $this->smsService->scheduleSms(
                $user,
                $recipients,
                $request->message,
                $request->scheduled_at,
                $request->sender_id,
                $request->campaign_name
            );

            if ($result['success']) {
                return redirect()->route('user.sms.campaigns.show', $result['campaign_id'])
                    ->with('success', 'SMS campaign scheduled successfully for ' . $request->scheduled_at);
            }
        } else {
            $result = $this->smsService->sendBulkSms(
                $user,
                $recipients,
                $request->message,
                $request->sender_id,
                $request->campaign_name
            );

            // Always redirect to campaign show page (even if failed) so user can see detailed error
            if (isset($result['campaign_id'])) {
                if ($result['success']) {
                    return redirect()->route('user.sms.campaigns.show', $result['campaign_id'])
                        ->with('success', "SMS sent successfully! Sent to {$result['sent_count']} recipients. Credits used: {$result['credits_used']}");
                } else {
                    return redirect()->route('user.sms.campaigns.show', $result['campaign_id'])
                        ->with('error', $result['error'] ?? 'SMS sending failed. Check the detailed error information below.');
                }
            }
        }

        // Only reached if no campaign was created (e.g., insufficient credits, validation error)
        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'Failed to send SMS. Please try again.');
    }

    /**
     * Show campaign details
     */
    public function show($id)
    {
        $user = Auth::user();

        $campaign = SmsCampaign::where('owner_id', $user->id)->where('owner_type', get_class($user))
            ->with(['messages' => function ($query) {
                $query->latest();
            }])
            ->findOrFail($id);

        // Get delivery statistics
        $deliveryStats = [
            'pending' => $campaign->messages()->where('status', 'pending')->count(),
            'submitted' => $campaign->messages()->where('status', 'submitted')->count(),
            'delivered' => $campaign->messages()->where('status', 'delivered')->count(),
            'failed' => $campaign->messages()->where('status', 'failed')->count(),
            'rejected' => $campaign->messages()->where('status', 'rejected')->count(),
            'expired' => $campaign->messages()->where('status', 'expired')->count(),
        ];

        return view('user.sms.campaigns.show', compact('campaign', 'deliveryStats'));
    }

    /**
     * Show resend form for a campaign
     */
    public function resend($id)
    {
        $user = Auth::user();

        $campaign = SmsCampaign::where('owner_id', $user->id)->where('owner_type', get_class($user))
            ->findOrFail($id);

        // Get recipients from campaign messages
        $recipients = $campaign->messages()->pluck('recipient')->toArray();
        $recipientsText = implode(', ', $recipients);

        // Get approved sender IDs for this user only (exclude admin-owned sender IDs)
        $senderIds = SmsSenderId::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->where('owner_type', '!=', 'App\\Models\\Admin') // Exclude admin sender IDs like "9yt Trybe"
            ->where('status', 'approved')
            ->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($user);
        $creditBalanceValue = $creditBalance->balance ?? 0;

        // Get contact groups
        $groups = \App\Models\SmsContact::getUniqueGroups($user->id, get_class($user));

        // Get all contacts for selection (in case they want to add more)
        $contacts = \App\Models\SmsContact::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->orderBy('name')
            ->get();

        return view('user.sms.campaigns.resend', compact('campaign', 'recipientsText', 'senderIds', 'creditBalance', 'creditBalanceValue', 'groups', 'contacts'));
    }

    /**
     * Cancel a scheduled campaign
     */
    public function cancel($id)
    {
        $user = Auth::user();

        $campaign = SmsCampaign::where('owner_id', $user->id)->where('owner_type', get_class($user))
            ->where('status', 'scheduled')
            ->findOrFail($id);

        $campaign->update(['status' => 'cancelled']);

        // Refund credits
        $creditBalance = $this->smsService->getCreditBalance($user);
        $creditBalance->addCredits($campaign->credits_used);

        return redirect()->route('user.sms.campaigns.index')
            ->with('success', 'Campaign cancelled successfully. Credits have been refunded.');
    }

    /**
     * Delete a campaign
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $campaign = SmsCampaign::where('owner_id', $user->id)->where('owner_type', get_class($user))->findOrFail($id);

        $campaign->delete();

        return redirect()->route('user.sms.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Prepare recipients array based on recipient type
     */
    protected function prepareRecipients(Request $request): array
    {
        $user = Auth::user();

        switch ($request->recipient_type) {
            case 'manual':
                // Parse phone numbers from text input
                return $this->smsService->parseContactsFromText($request->recipients);

            case 'contacts':
                // Get phone numbers from selected contacts
                return \App\Models\SmsContact::where('owner_id', $user->id)
                    ->where('owner_type', get_class($user))
                    ->whereIn('id', $request->contact_ids ?? [])
                    ->pluck('phone_number')
                    ->toArray();

            case 'group':
                // Get phone numbers from group
                return \App\Models\SmsContact::where('owner_id', $user->id)
                    ->where('owner_type', get_class($user))
                    ->where('group', $request->group)
                    ->pluck('phone_number')
                    ->toArray();

            case 'excel':
                // Process Excel file - returns array of phone numbers from column 2
                return $this->processExcelFile($request->file('excel_file'));

            default:
                return [];
        }
    }

    /**
     * Process Excel file and extract phone numbers from column 2
     * Also stores row data for message personalization
     */
    protected function processExcelFile($file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $phoneNumbers = [];

            // Skip header row (index 0) and process data rows
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Column 2 (index 1) must contain phone number
                if (isset($row[1]) && !empty($row[1])) {
                    $phoneNumber = trim($row[1]);
                    if (!empty($phoneNumber)) {
                        $phoneNumbers[] = $phoneNumber;
                    }
                }
            }

            return $phoneNumbers;
        } catch (\Exception $e) {
            \Log::error('Excel file processing error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Handle Excel messaging with column placeholders
     * Example: "Dear [1], your event is on [3]" where [1] = column 1, [3] = column 3
     * Phone numbers can be in ANY column - system auto-detects them
     */
    protected function handleExcelMessaging(Request $request, $user)
    {
        try {
            $file = $request->file('excel_file');
            $messageTemplate = $request->message;

            // Load Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $recipients = [];
            $personalizedMessages = [];

            // Skip header row (index 0) and process data rows
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Auto-detect phone number in ANY column
                $phoneNumber = $this->extractPhoneNumberFromRow($row);

                if (!$phoneNumber) {
                    continue; // Skip rows without valid phone numbers
                }

                // Replace all column placeholders [1], [2], [3], etc. with actual values
                $personalizedMessage = $messageTemplate;
                foreach ($row as $columnIndex => $columnValue) {
                    $placeholder = '[' . ($columnIndex + 1) . ']'; // [1] for column 0, [2] for column 1, etc.
                    $personalizedMessage = str_replace($placeholder, $columnValue ?? '', $personalizedMessage);
                }

                $recipients[] = $phoneNumber;
                $personalizedMessages[$phoneNumber] = $personalizedMessage;
            }

            if (empty($recipients)) {
                return back()->withInput()->with('error', 'No valid phone numbers found in Excel file. Please ensure at least one column contains phone numbers (10+ digits).');
            }

            // Send personalized messages
            if ($request->schedule_type === 'later') {
                return back()->withInput()->with('error', 'Scheduled sending is not supported for Excel uploads with placeholders. Please send immediately.');
            } else {
                // Send personalized SMS to each recipient
                $result = $this->smsService->sendPersonalizedBulkSms(
                    $user,
                    $recipients,
                    $personalizedMessages,
                    $request->sender_id,
                    $request->campaign_name
                );

                if (isset($result['campaign_id'])) {
                    if ($result['success']) {
                        return redirect()->route('user.sms.campaigns.show', $result['campaign_id'])
                            ->with('success', "Personalized SMS sent successfully! Sent to {$result['sent_count']} recipients. Credits used: {$result['credits_used']}");
                    } else {
                        return redirect()->route('user.sms.campaigns.show', $result['campaign_id'])
                            ->with('error', $result['error'] ?? 'SMS sending failed. Check the detailed error information below.');
                    }
                }
            }

            return back()->withInput()->with('error', 'Failed to send SMS. Please try again.');

        } catch (\Exception $e) {
            \Log::error('Excel messaging error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error processing Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Extract phone number from any column in a row
     * Looks for values that contain 9+ digits
     */
    protected function extractPhoneNumberFromRow(array $row): ?string
    {
        foreach ($row as $value) {
            if (empty($value)) {
                continue;
            }

            $cleaned = trim($value);

            // Remove common phone number formatting characters
            $digitsOnly = preg_replace('/[^0-9]/', '', $cleaned);

            // Check if it looks like a phone number (9+ digits)
            if (strlen($digitsOnly) >= 9) {
                return $cleaned; // Return original format (might include +, spaces, etc.)
            }
        }

        return null; // No phone number found in this row
    }
}
