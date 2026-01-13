<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Services\FeeCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationSettingsController extends Controller
{
    protected FeeCalculatorService $feeCalculator;

    public function __construct(FeeCalculatorService $feeCalculator)
    {
        $this->feeCalculator = $feeCalculator;
    }

    public function index()
    {
        $company = Auth::guard('company')->user();

        // Get fee summary from platform settings
        $feeSummary = $this->feeCalculator->getFeeSummary();

        // Get organization stats
        $stats = [
            'total_events' => $company->events()->count(),
            'approved_events' => $company->events()->approved()->count(),
            'pending_events' => $company->events()->pending()->count(),
            'total_followers' => $company->followers()->count(),
            'total_revenue' => $company->events()->sum('total_revenue'),
        ];

        return view('company.organization.settings', compact('feeSummary', 'stats'));
    }

    public function update(Request $request)
    {
        $company = Auth::guard('company')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'logo' => 'nullable|image',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);

        return back()->with('success', 'Organization settings updated successfully!');
    }
}
