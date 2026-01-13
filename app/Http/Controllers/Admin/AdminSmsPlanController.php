<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsPlan;
use Illuminate\Http\Request;

class AdminSmsPlanController extends Controller
{
    /**
     * Display SMS plans list
     */
    public function index()
    {
        $plans = SmsPlan::orderBy('sms_credits')->get();

        return view('admin.sms.plans.index', compact('plans'));
    }

    /**
     * Show form to create a plan
     */
    public function create()
    {
        return view('admin.sms.plans.create');
    }

    /**
     * Store a new plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[A-Za-z0-9\s\-]+$/',
                function ($attribute, $value, $fail) {
                    $testWords = ['test', 'dummy', 'aves', 'gfds', 'asdf', 'qwer', 'temp'];
                    $lowerValue = strtolower($value);
                    foreach ($testWords as $word) {
                        if (str_contains($lowerValue, $word)) {
                            $fail('The ' . $attribute . ' contains invalid test data. Please use a professional plan name.');
                        }
                    }
                },
            ],
            'price' => 'required|numeric|min:10',
            'sms_credits' => 'required|integer|min:50',
            'description' => 'required|string|min:10|max:255',
            'badge' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        SmsPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'sms_credits' => $request->sms_credits,
            'description' => $request->description,
            'badge' => $request->badge,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.sms.plans.index')
            ->with('success', 'SMS plan created successfully!');
    }

    /**
     * Show form to edit a plan
     */
    public function edit($id)
    {
        $plan = SmsPlan::findOrFail($id);

        return view('admin.sms.plans.edit', compact('plan'));
    }

    /**
     * Update a plan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[A-Za-z0-9\s\-]+$/',
                function ($attribute, $value, $fail) {
                    $testWords = ['test', 'dummy', 'aves', 'gfds', 'asdf', 'qwer', 'temp'];
                    $lowerValue = strtolower($value);
                    foreach ($testWords as $word) {
                        if (str_contains($lowerValue, $word)) {
                            $fail('The ' . $attribute . ' contains invalid test data. Please use a professional plan name.');
                        }
                    }
                },
            ],
            'price' => 'required|numeric|min:10',
            'sms_credits' => 'required|integer|min:50',
            'description' => 'required|string|min:10|max:255',
            'badge' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $plan = SmsPlan::findOrFail($id);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'sms_credits' => $request->sms_credits,
            'description' => $request->description,
            'badge' => $request->badge,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.sms.plans.index')
            ->with('success', 'SMS plan updated successfully!');
    }

    /**
     * Delete a plan
     */
    public function destroy($id)
    {
        $plan = SmsPlan::findOrFail($id);
        $plan->delete();

        return redirect()->route('admin.sms.plans.index')
            ->with('success', 'SMS plan deleted successfully!');
    }

    /**
     * Toggle plan active status
     */
    public function toggleStatus($id)
    {
        $plan = SmsPlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.sms.plans.index')
            ->with('success', "SMS plan {$status} successfully!");
    }
}
