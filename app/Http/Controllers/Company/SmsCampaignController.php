<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SmsCampaign;
use App\Models\SmsContact;
use App\Models\SmsSenderId;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsCampaignController extends Controller
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
        $company = Auth::guard('company')->user();

        $query = SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company));

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

        return view('company.sms.campaigns.index', compact('campaigns'));
    }

    /**
     * Show form to send single SMS
     */
    public function createSingle()
    {
        $company = Auth::guard('company')->user();

        // Get approved sender IDs for this company only (exclude admin-owned sender IDs)
        $senderIds = SmsSenderId::where('owner_id', $company->id)
            ->where('owner_type', get_class($company))
            ->where('owner_type', '!=', 'App\\Models\\Admin') // Exclude admin sender IDs like "9yt Trybe"
            ->where('status', 'approved')
            ->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($company);

        return view('company.sms.campaigns.send-single', compact('senderIds', 'creditBalance'));
    }

    /**
     * Send single SMS
     */
    public function sendSingle(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:1000',
            'sender_id' => 'nullable|string',
        ]);

        $company = Auth::guard('company')->user();

        // Parse phone numbers (comma-separated)
        $phoneNumbers = array_map('trim', explode(',', $request->phone_number));
        $phoneNumbers = array_filter($phoneNumbers); // Remove empty values

        if (count($phoneNumbers) === 0) {
            return back()
                ->withInput()
                ->with('error', 'Please provide at least one valid phone number.');
        }

        // If single number, use sendSingleSms
        if (count($phoneNumbers) === 1) {
            $result = $this->smsService->sendSingleSms(
                $company,
                $phoneNumbers[0],
                $request->message,
                $request->sender_id
            );

            if ($result['success']) {
                return redirect()->route('organization.sms.campaigns.show', $result['campaign_id'])
                    ->with('success', 'SMS sent successfully! Credits used: ' . $result['credits_used']);
            }

            $errorMessage = $result['error'] ?? 'Failed to send SMS. Please try again.';
            return back()
                ->withInput()
                ->with('error', 'Failed to send SMS: ' . $errorMessage);
        }

        // Multiple numbers - use bulk SMS
        $result = $this->smsService->sendBulkSms(
            $company,
            $phoneNumbers,
            $request->message,
            $request->sender_id,
            'Quick Send to ' . count($phoneNumbers) . ' recipients'
        );

        if ($result['success']) {
            return redirect()->route('organization.sms.campaigns.show', $result['campaign_id'])
                ->with('success', 'SMS sent to ' . count($phoneNumbers) . ' recipients! Credits used: ' . $result['credits_used']);
        }

        $errorMessage = $result['error'] ?? 'Failed to send SMS. Please try again.';
        return back()
            ->withInput()
            ->with('error', 'Failed to send SMS: ' . $errorMessage);
    }

    /**
     * Show form to send bulk SMS
     */
    public function createBulk()
    {
        $company = Auth::guard('company')->user();

        // Get approved sender IDs for this company only (exclude admin-owned sender IDs)
        $senderIds = SmsSenderId::where('owner_id', $company->id)
            ->where('owner_type', get_class($company))
            ->where('owner_type', '!=', 'App\\Models\\Admin') // Exclude admin sender IDs like "9yt Trybe"
            ->where('status', 'approved')
            ->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($company);

        // Get contact groups
        $groups = SmsContact::getUniqueGroups($company->id, get_class($company));

        // Get company conferences with registration count
        $conferences = \App\Models\Conference::where('company_id', $company->id)
            ->withCount('registrations')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('company.sms.campaigns.send-bulk', compact('senderIds', 'creditBalance', 'groups', 'conferences'));
    }

    /**
     * Send bulk SMS
     */
    public function sendBulk(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'sender_id' => 'nullable|string',
            'recipient_type' => 'required|in:manual,contacts,group,conference,use_our_contacts',
            'recipients' => 'required_if:recipient_type,manual|nullable|string',
            'contact_ids' => 'required_if:recipient_type,contacts|nullable|array',
            'contact_ids.*' => 'exists:sms_contacts,id',
            'group' => 'required_if:recipient_type,group|nullable|string',
            'conference_id' => 'required_if:recipient_type,conference|nullable|exists:conferences,id',
            'attendance_filter' => 'required_if:recipient_type,conference|nullable|in:all,online,in_person',
            'target_recipient_count' => 'required_if:recipient_type,use_our_contacts|nullable|integer|min:1',
            'schedule_type' => 'required|in:now,later',
            'scheduled_at' => 'required_if:schedule_type,later|nullable|date|after:now',
        ]);

        $company = Auth::guard('company')->user();

        if ($request->recipient_type === 'use_our_contacts') {
            $targetCount = (int) $request->target_recipient_count;
            $creditsPerSms = $this->smsService->calculateCredits($request->message);
            $totalCreditsNeeded = $creditsPerSms * $targetCount;

            $creditBalance = $this->smsService->getCreditBalance($company);
            if ($creditBalance->balance < $totalCreditsNeeded) {
                return back()->withInput()->with('error', 'Insufficient SMS credits. Please purchase more credits.');
            }

            $campaign = SmsCampaign::create([
                'owner_id' => $company->id,
                'owner_type' => get_class($company),
                'name' => $request->campaign_name,
                'message' => $request->message,
                'sender_id' => $request->sender_id,
                'type' => 'bulk',
                'status' => 'pending_approval',
                'approval_status' => 'pending',
                'requires_contacts' => true,
                'target_recipient_count' => $targetCount,
                'contact_fee_per_recipient' => 1.00,
                'total_contact_fee' => $targetCount * 1.00,
                'total_sms_cost' => 0,
                'total_amount' => $targetCount * 1.00,
                'payment_status' => 'pending',
                'total_recipients' => $targetCount,
                'total_sent' => 0,
                'credits_used' => $totalCreditsNeeded,
            ]);

            // Reserve credits
            $creditBalance->deductCredits($totalCreditsNeeded);

            return redirect()->route('organization.sms.campaigns.show', $campaign->id)
                ->with('success', 'Campaign submitted for approval. We will contact you once it is approved.');
        }

        // Prepare recipients based on type
        $recipients = $this->prepareRecipients($company, $request);

        if (empty($recipients)) {
            return back()->withInput()->with('error', 'No valid recipients found.');
        }

        // Send now or schedule
        if ($request->schedule_type === 'later') {
            $result = $this->smsService->scheduleSms(
                $company,
                $recipients,
                $request->message,
                $request->scheduled_at,
                $request->sender_id,
                $request->campaign_name
            );

            if ($result['success']) {
                return redirect()->route('organization.sms.campaigns.show', $result['campaign_id'])
                    ->with('success', 'SMS campaign scheduled successfully for ' . $request->scheduled_at);
            }
        } else {
            $result = $this->smsService->sendBulkSms(
                $company,
                $recipients,
                $request->message,
                $request->sender_id,
                $request->campaign_name
            );

            // Always redirect to campaign show page (even if failed) so user can see detailed error
            if (isset($result['campaign_id'])) {
                if ($result['success']) {
                    return redirect()->route('organization.sms.campaigns.show', $result['campaign_id'])
                        ->with('success', "Bulk SMS sent successfully! Sent to {$result['sent_count']} recipients. Credits used: {$result['credits_used']}");
                } else {
                    return redirect()->route('organization.sms.campaigns.show', $result['campaign_id'])
                        ->with('error', $result['error'] ?? 'SMS sending failed. Check the detailed error information below.');
                }
            }
        }

        // Only reached if no campaign was created (e.g., insufficient credits, validation error)
        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'Failed to send bulk SMS. Please try again.');
    }

    /**
     * Show campaign details
     */
    public function show($id)
    {
        $company = Auth::guard('company')->user();

        $campaign = SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company))
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

        return view('company.sms.campaigns.show', compact('campaign', 'deliveryStats'));
    }

    /**
     * Show resend form for a campaign
     */
    public function resend($id)
    {
        $company = Auth::guard('company')->user();

        $campaign = SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->findOrFail($id);

        // Get recipients from campaign messages
        $recipients = $campaign->messages()->pluck('recipient')->toArray();
        $recipientsText = implode(', ', $recipients);

        // Get approved sender IDs for this company only (exclude admin-owned sender IDs)
        $senderIds = SmsSenderId::where('owner_id', $company->id)
            ->where('owner_type', get_class($company))
            ->where('owner_type', '!=', 'App\\Models\\Admin') // Exclude admin sender IDs like "9yt Trybe"
            ->where('status', 'approved')
            ->get();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($company);

        return view('company.sms.campaigns.resend', compact('campaign', 'recipientsText', 'senderIds', 'creditBalance'));
    }

    /**
     * Cancel a scheduled campaign
     */
    public function cancel($id)
    {
        $company = Auth::guard('company')->user();

        $campaign = SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('status', 'scheduled')
            ->findOrFail($id);

        $campaign->update(['status' => 'cancelled']);

        // Refund credits
        $creditBalance = $this->smsService->getCreditBalance($company);
        $creditBalance->addCredits($campaign->credits_used);

        return redirect()->route('organization.sms.campaigns.index')
            ->with('success', 'Campaign cancelled successfully. Credits have been refunded.');
    }

    /**
     * Delete a campaign
     */
    public function destroy($id)
    {
        $company = Auth::guard('company')->user();

        $campaign = SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company))->findOrFail($id);

        $campaign->delete();

        return redirect()->route('organization.sms.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Prepare recipients array based on recipient type
     */
    protected function prepareRecipients(Company $company, Request $request): array
    {
        switch ($request->recipient_type) {
            case 'manual':
                // Parse phone numbers from text input
                return $this->smsService->parseContactsFromText($request->recipients);

            case 'contacts':
                // Get phone numbers from selected contacts
                return SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))
                    ->whereIn('id', $request->contact_ids)
                    ->pluck('phone_number')
                    ->toArray();

            case 'group':
                // Get phone numbers from group
                return SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))
                    ->where('group', $request->group)
                    ->pluck('phone_number')
                    ->toArray();

            case 'conference':
                // Get phone numbers from conference registrations
                $query = \App\Models\Registration::where('conference_id', $request->conference_id)
                    ->whereNotNull('phone')
                    ->where('phone', '!=', '');

                // Apply attendance filter
                if ($request->attendance_filter === 'online') {
                    $query->where('attendance_type', 'online');
                } elseif ($request->attendance_filter === 'in_person') {
                    $query->where('attendance_type', 'in_person');
                }
                // 'all' means no filter

                return $query->pluck('phone')
                    ->filter() // Remove null/empty values
                    ->unique() // Remove duplicates
                    ->values() // Reset array keys
                    ->toArray();

            default:
                return [];
        }
    }
}
