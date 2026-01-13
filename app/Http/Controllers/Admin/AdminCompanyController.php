<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query()->withCount(['conferences', 'surveys']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'suspended') {
                $query->where('is_suspended', true);
            } elseif ($request->status === 'active') {
                $query->where('is_suspended', false);
            }
        }

        $companies = $query->latest()->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:companies'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::defaults()],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully!');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:companies,email,' . $company->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company updated successfully!');
    }

    public function destroy(Company $company)
    {
        $companyName = $company->name;
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', "Company '{$companyName}' deleted successfully!");
    }

    public function suspend(Request $request, Company $company)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $company->suspend($request->reason);

        return back()->with('success', 'Company suspended successfully!');
    }

    public function unsuspend(Company $company)
    {
        $company->unsuspend();

        return back()->with('success', 'Company suspension lifted successfully!');
    }

    public function show(Company $company)
    {
        $company->load(['conferences', 'surveys']);

        $stats = [
            'total_conferences' => $company->conferences()->count(),
            'total_surveys' => $company->surveys()->count(),
            'total_registrations' => $company->registrations()->count(),
        ];

        return view('admin.companies.show', compact('company', 'stats'));
    }
}
