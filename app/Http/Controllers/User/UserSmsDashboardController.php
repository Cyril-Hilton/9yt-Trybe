<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SmsCampaign;
use App\Models\SmsMessage;
use App\Services\Sms\SmsService;
use Illuminate\Support\Facades\Auth;

class UserSmsDashboardController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $user = Auth::user();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($user);

        // Get recent campaigns
        $recentCampaigns = SmsCampaign::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->latest()
            ->take(5)
            ->get();

        // Get campaign statistics
        $totalCampaigns = SmsCampaign::where('owner_id', $user->id)
            ->where('owner_type', get_class($user))
            ->count();

        $totalMessagesSent = SmsMessage::whereHas('campaign', function ($query) use ($user) {
            $query->where('owner_id', $user->id)
                ->where('owner_type', get_class($user));
        })->count();

        $deliveredMessages = SmsMessage::whereHas('campaign', function ($query) use ($user) {
            $query->where('owner_id', $user->id)
                ->where('owner_type', get_class($user));
        })->where('status', 'delivered')->count();

        $failedMessages = SmsMessage::whereHas('campaign', function ($query) use ($user) {
            $query->where('owner_id', $user->id)
                ->where('owner_type', get_class($user));
        })->where('status', 'failed')->count();

        return view('user.sms.dashboard', compact(
            'creditBalance',
            'recentCampaigns',
            'totalCampaigns',
            'totalMessagesSent',
            'deliveredMessages',
            'failedMessages'
        ));
    }
}
