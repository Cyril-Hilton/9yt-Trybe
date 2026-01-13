<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\SmsCampaign;
use App\Models\SmsMessage;
use App\Models\SmsSenderId;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsDashboardController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display the SMS dashboard
     */
    public function index()
    {
        $company = Auth::guard('company')->user();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($company);

        // Get statistics
        $stats = [
            'total_campaigns' => SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company))->count(),
            'total_sent' => SmsMessage::where('owner_id', $company->id)->where('owner_type', get_class($company))
                ->whereIn('status', ['submitted', 'delivered'])
                ->count(),
            'total_delivered' => SmsMessage::where('owner_id', $company->id)->where('owner_type', get_class($company))
                ->where('status', 'delivered')
                ->count(),
            'total_failed' => SmsMessage::where('owner_id', $company->id)->where('owner_type', get_class($company))
                ->where('status', 'failed')
                ->count(),
            'credits_balance' => $creditBalance->balance,
            'total_purchased' => $creditBalance->total_purchased,
            'total_used' => $creditBalance->total_used,
        ];

        // Get recent campaigns
        $recentCampaigns = SmsCampaign::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->latest()
            ->take(5)
            ->get();

        // Get approved sender IDs count
        $approvedSenderIds = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('status', 'approved')
            ->count();

        // Get pending sender IDs count
        $pendingSenderIds = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('status', 'pending')
            ->count();

        return view('company.sms.dashboard', compact(
            'stats',
            'recentCampaigns',
            'approvedSenderIds',
            'pendingSenderIds'
        ));
    }
}
