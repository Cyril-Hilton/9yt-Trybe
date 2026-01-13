<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\AdminSenderIdNotification;
use App\Models\Admin;
use App\Models\SmsSenderId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SmsSenderIdController extends Controller
{
    /**
     * Display sender IDs list
     */
    public function index()
    {
        $company = Auth::guard('company')->user();

        // Only show sender IDs that belong to this specific company
        // Exclude any admin-owned sender IDs (like "9yt Trybe" which is admin-only)
        $senderIds = SmsSenderId::where('owner_id', $company->id)
            ->where('owner_type', get_class($company))
            ->where('owner_type', '!=', 'App\\Models\\Admin') // Exclude admin sender IDs
            ->with('reviewedByAdmin')
            ->latest()
            ->paginate(15);

        return view('company.sms.sender-ids.index', compact('senderIds'));
    }

    /**
     * Show form to create a sender ID
     */
    public function create()
    {
        return view('company.sms.sender-ids.create');
    }

    /**
     * Store a new sender ID request
     */
    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => [
                'required',
                'string',
                'max:11', // Mnotify requirement: max 11 characters (allows spaces like "9yt Trybe")
                'regex:/^[A-Za-z0-9\s]+$/', // Alphanumeric and spaces only
            ],
            'purpose' => 'required|string|max:500',
        ]);

        $company = Auth::guard('company')->user();

        // Check if sender ID already exists for this company
        $existing = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('sender_id', strtoupper($request->sender_id))
            ->exists();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'You have already requested this Sender ID.');
        }

        $senderId = SmsSenderId::create([
            'owner_id' => $company->id,
            'owner_type' => get_class($company),
            'sender_id' => strtoupper($request->sender_id),
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        // Send email notification to all active admins
        $admins = Admin::where('is_active', true)->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new AdminSenderIdNotification($senderId));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error('Failed to send admin notification email: ' . $e->getMessage());
            }
        }

        return redirect()->route('organization.sms.sender-ids.index')
            ->with('success', 'Sender ID request submitted successfully! It will be reviewed by the admin.');
    }

    /**
     * Show form to edit a sender ID
     */
    public function edit($id)
    {
        $company = Auth::guard('company')->user();

        $senderId = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->whereIn('status', ['pending', 'rejected'])
            ->findOrFail($id);

        return view('company.sms.sender-ids.edit', compact('senderId'));
    }

    /**
     * Update a sender ID request
     */
    public function update(Request $request, $id)
    {
        $company = Auth::guard('company')->user();

        $senderId = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->whereIn('status', ['pending', 'rejected'])
            ->findOrFail($id);

        $request->validate([
            'sender_id' => [
                'required',
                'string',
                'max:11', // Mnotify requirement: max 11 characters (allows spaces like "9yt Trybe")
                'regex:/^[A-Za-z0-9\s]+$/', // Alphanumeric and spaces only
            ],
            'purpose' => 'required|string|max:500',
        ]);

        // Check if sender ID already exists for this company (excluding current record)
        $existing = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('sender_id', strtoupper($request->sender_id))
            ->where('id', '!=', $id)
            ->exists();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'You have already requested this Sender ID.');
        }

        $senderId->update([
            'sender_id' => strtoupper($request->sender_id),
            'purpose' => $request->purpose,
            'status' => 'pending', // Reset to pending for re-review
            'reviewed_at' => null,
            'reviewed_by' => null,
            'rejection_reason' => null,
        ]);

        // Send email notification to all active admins
        $admins = Admin::where('is_active', true)->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new AdminSenderIdNotification($senderId));
            } catch (\Exception $e) {
                \Log::error('Failed to send admin notification email: ' . $e->getMessage());
            }
        }

        return redirect()->route('organization.sms.sender-ids.index')
            ->with('success', 'Sender ID updated and submitted for review!');
    }

    /**
     * Set a sender ID as default
     */
    public function setDefault($id)
    {
        $company = Auth::guard('company')->user();

        $senderId = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('status', 'approved')
            ->findOrFail($id);

        $senderId->setAsDefault();

        return redirect()->route('organization.sms.sender-ids.index')
            ->with('success', 'Default Sender ID updated successfully!');
    }

    /**
     * Delete a sender ID
     */
    public function destroy($id)
    {
        $company = Auth::guard('company')->user();

        $senderId = SmsSenderId::where('owner_id', $company->id)->where('owner_type', get_class($company))->findOrFail($id);

        // Don't allow deletion of approved default sender ID
        if ($senderId->status === 'approved' && $senderId->is_default) {
            return back()->with('error', 'Cannot delete the default Sender ID. Please set another Sender ID as default first.');
        }

        $senderId->delete();

        return redirect()->route('organization.sms.sender-ids.index')
            ->with('success', 'Sender ID deleted successfully!');
    }
}
