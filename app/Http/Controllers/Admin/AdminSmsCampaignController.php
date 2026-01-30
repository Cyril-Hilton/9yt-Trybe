<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Models\SmsCampaign;
use App\Models\SmsContact;
use App\Models\SmsSenderId;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AdminSmsCampaignController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * SMS Dashboard
     */
    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();

        // Get admin's SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($admin);

        // Get recent campaigns
        $recentCampaigns = SmsCampaign::where('owner_id', $admin->id)
            ->where('owner_type', get_class($admin))
            ->latest()
            ->take(10)
            ->get();

        // Stats
        $stats = [
            'total_campaigns' => SmsCampaign::where('owner_id', $admin->id)->where('owner_type', get_class($admin))->count(),
            'total_sent' => SmsCampaign::where('owner_id', $admin->id)->where('owner_type', get_class($admin))->sum('total_sent'),
            'credits_available' => $creditBalance->balance,
            'total_users' => User::count(),
            'total_organizers' => Company::count(),
        ];

        return view('admin.sms.dashboard', compact('creditBalance', 'recentCampaigns', 'stats'));
    }

    /**
     * Show form to send single SMS
     */
    public function createSingle()
    {
        $admin = Auth::guard('admin')->user();

        // Get all sender IDs (admins can see all)
        $senderIds = SmsSenderId::where('status', 'approved')->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($admin);

        return view('admin.sms.send-single', compact('senderIds', 'creditBalance'));
    }

    /**
     * Send single SMS
     */
    public function sendSingle(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:1000',
            'sender_id' => 'required|string', // Required for admin - can type any sender ID
        ]);

        $admin = Auth::guard('admin')->user();

        $result = $this->smsService->sendSingleSms(
            $admin,
            $request->phone_number,
            $request->message,
            $request->sender_id
        );

        if ($result['success']) {
            return redirect()->route('admin.sms.campaigns.show', $result['campaign_id'])
                ->with('success', 'SMS sent successfully! Credits used: ' . $result['credits_used']);
        }

        return back()
            ->withInput()
            ->with('error', 'Failed to send SMS: ' . ($result['error'] ?? 'Please try again.'));
    }

    /**
     * Show form to send bulk SMS
     */
    public function createBulk()
    {
        $admin = Auth::guard('admin')->user();

        // Get all sender IDs (admins can see all)
        $senderIds = SmsSenderId::where('status', 'approved')->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($admin);

        $totalUsers = User::count();
        $totalOrganizers = Company::count();

        // Get counts for recipient selection
        $stats = [
            'total_users' => $totalUsers,
            'total_organizers' => $totalOrganizers,
            'total_contacts' => $totalUsers + $totalOrganizers,
        ];

        $users = User::orderBy('name')->get();
        $organizers = Company::orderBy('name')->get();

        return view('admin.sms.send-bulk', compact(
            'senderIds',
            'creditBalance',
            'stats',
            'users',
            'organizers',
            'totalUsers',
            'totalOrganizers'
        ));
    }

    /**
     * Send bulk SMS
     */
    public function sendBulk(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'sender_id' => 'required|string', // Required for admin - can type any sender ID
            'recipient_type' => 'required|in:manual,all,users,organizers,custom,excel',
            'recipients' => 'required_if:recipient_type,manual|nullable|string',
            'user_ids' => 'required_if:recipient_type,custom|nullable|array',
            'organizer_ids' => 'required_if:recipient_type,custom|nullable|array',
            'excel_file' => 'required_if:recipient_type,excel|nullable|file|mimes:xlsx,xls,csv',
            'schedule_type' => 'required|in:now,later',
            'scheduled_at' => 'required_if:schedule_type,later|nullable|date|after:now',
        ]);

        $admin = Auth::guard('admin')->user();

        // Prepare recipients based on type
        $recipients = $this->prepareRecipients($admin, $request);

        if (empty($recipients)) {
            return back()->withInput()->with('error', 'No valid recipients found.');
        }

        // Send now or schedule
        if ($request->schedule_type === 'later') {
            $result = $this->smsService->scheduleSms(
                $admin,
                $recipients,
                $request->message,
                $request->scheduled_at,
                $request->sender_id,
                $request->campaign_name
            );

            if ($result['success']) {
                return redirect()->route('admin.sms.campaigns.show', $result['campaign_id'])
                    ->with('success', 'SMS campaign scheduled successfully for ' . $request->scheduled_at);
            }
        } else {
            $result = $this->smsService->sendBulkSms(
                $admin,
                $recipients,
                $request->message,
                $request->sender_id,
                $request->campaign_name
            );

            if ($result['success']) {
                return redirect()->route('admin.sms.campaigns.show', $result['campaign_id'])
                    ->with('success', "Bulk SMS sent successfully! Sent to {$result['sent_count']} recipients. Credits used: {$result['credits_used']}");
            }
        }

        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'Failed to send bulk SMS. Please try again.');
    }

    /**
     * Show campaign details
     */
    public function show($id)
    {
        $admin = Auth::guard('admin')->user();

        $campaign = SmsCampaign::where('owner_id', $admin->id)
            ->where('owner_type', get_class($admin))
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

        return view('admin.sms.show', compact('campaign', 'deliveryStats'));
    }

    /**
     * List all campaigns
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $query = SmsCampaign::where('owner_id', $admin->id)->where('owner_type', get_class($admin));

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->latest()->paginate(15);

        return view('admin.sms.campaigns', compact('campaigns'));
    }

    /**
     * Prepare recipients array based on recipient type
     */
    protected function prepareRecipients(Admin $admin, Request $request): array
    {
        switch ($request->recipient_type) {
            case 'manual':
                // Parse phone numbers from text input
                return $this->smsService->parseContactsFromText($request->recipients);

            case 'all':
                // Get all users and organizers
                $userPhones = User::whereNotNull('phone')->pluck('phone')->toArray();
                $orgPhones = Company::whereNotNull('phone')->pluck('phone')->toArray();
                return array_merge($userPhones, $orgPhones);

            case 'users':
                // Get all users only
                return User::whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->pluck('phone')
                    ->toArray();

            case 'organizers':
                // Get all organizers only
                return Company::whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->pluck('phone')
                    ->toArray();

            case 'custom':
                // Get specific users and organizers
                $phones = [];

                if ($request->filled('user_ids')) {
                    $userPhones = User::whereIn('id', $request->user_ids)
                        ->whereNotNull('phone')
                        ->pluck('phone')
                        ->toArray();
                    $phones = array_merge($phones, $userPhones);
                }

                if ($request->filled('organizer_ids')) {
                    $orgPhones = Company::whereIn('id', $request->organizer_ids)
                        ->whereNotNull('phone')
                        ->pluck('phone')
                        ->toArray();
                    $phones = array_merge($phones, $orgPhones);
                }

                return $phones;

            case 'excel':
                // Import from Excel
                if ($request->hasFile('excel_file')) {
                    return $this->importFromExcel($request->file('excel_file'));
                }
                return [];

            default:
                return [];
        }
    }

    /**
     * Import phone numbers from Excel file
     */
    protected function importFromExcel($file): array
    {
        try {
            $data = Excel::toArray([], $file);
            $phones = [];

            foreach ($data[0] as $row) {
                // Assuming phone numbers are in the first column
                if (isset($row[0]) && !empty($row[0])) {
                    $phone = preg_replace('/[^\d+]/', '', $row[0]);
                    if (!empty($phone)) {
                        $phones[] = $phone;
                    }
                }
            }

            return array_unique($phones);
        } catch (\Exception $e) {
            return [];
        }
    }
}
