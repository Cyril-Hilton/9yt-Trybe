<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\SmsContact;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SmsContactController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display contacts list
     */
    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();

        $query = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company));

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $contacts = $query->latest()->paginate(20);

        // Get all unique groups
        $groups = SmsContact::getUniqueGroups($company->id, get_class($company));

        return view('company.sms.contacts.index', compact('contacts', 'groups'));
    }

    /**
     * Show form to create a contact
     */
    public function create()
    {
        $company = Auth::guard('company')->user();
        $groups = SmsContact::getUniqueGroups($company->id, get_class($company));

        return view('company.sms.contacts.create', compact('groups'));
    }

    /**
     * Store a new contact
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'group' => 'nullable|string|max:255',
        ]);

        $company = Auth::guard('company')->user();

        // Check if contact already exists
        $existing = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('phone_number', $request->phone_number)
            ->exists();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'A contact with this phone number already exists.');
        }

        SmsContact::create([
            'owner_id' => $company->id,
            'owner_type' => get_class($company),
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'email' => $request->email,
            'group' => $request->group,
        ]);

        return redirect()->route('organization.sms.contacts.index')
            ->with('success', 'Contact added successfully!');
    }

    /**
     * Show form to edit a contact
     */
    public function edit($id)
    {
        $company = Auth::guard('company')->user();

        $contact = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))->findOrFail($id);
        $groups = SmsContact::getUniqueGroups($company->id, get_class($company));

        return view('company.sms.contacts.edit', compact('contact', 'groups'));
    }

    /**
     * Update a contact
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'group' => 'nullable|string|max:255',
        ]);

        $company = Auth::guard('company')->user();

        $contact = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))->findOrFail($id);

        // Check if phone number is taken by another contact
        $existing = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->where('phone_number', $request->phone_number)
            ->where('id', '!=', $id)
            ->exists();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'Another contact with this phone number already exists.');
        }

        $contact->update([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'email' => $request->email,
            'group' => $request->group,
        ]);

        return redirect()->route('organization.sms.contacts.index')
            ->with('success', 'Contact updated successfully!');
    }

    /**
     * Delete a contact
     */
    public function destroy($id)
    {
        $company = Auth::guard('company')->user();

        $contact = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))->findOrFail($id);
        $contact->delete();

        return redirect()->route('organization.sms.contacts.index')
            ->with('success', 'Contact deleted successfully!');
    }

    /**
     * Show bulk import form
     */
    public function showImport()
    {
        $company = Auth::guard('company')->user();
        $groups = SmsContact::getUniqueGroups($company->id, get_class($company));

        return view('company.sms.contacts.import', compact('groups'));
    }

    /**
     * Import contacts from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_type' => 'required|in:csv,text',
            'csv_file' => 'required_if:import_type,csv|file|mimes:csv,txt',
            'contacts_text' => 'required_if:import_type,text|string',
            'group' => 'nullable|string|max:255',
        ]);

        $company = Auth::guard('company')->user();
        $contactsData = [];

        if ($request->import_type === 'csv') {
            // Parse CSV file
            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), 'r');

            $header = fgetcsv($handle); // Skip header row
            while (($row = fgetcsv($handle)) !== false) {
                if (!empty($row[0])) { // Phone number is required
                    $contactsData[] = [
                        'phone_number' => $row[0],
                        'name' => $row[1] ?? null,
                        'email' => $row[2] ?? null,
                        'group' => $request->group ?? ($row[3] ?? null),
                    ];
                }
            }
            fclose($handle);
        } else {
            // Parse text input
            $phoneNumbers = $this->smsService->parseContactsFromText($request->contacts_text);

            foreach ($phoneNumbers as $phone) {
                $contactsData[] = [
                    'phone_number' => $phone,
                    'name' => null,
                    'email' => null,
                    'group' => $request->group,
                ];
            }
        }

        if (empty($contactsData)) {
            return back()->with('error', 'No valid contacts found to import.');
        }

        // Import contacts
        $result = $this->smsService->importContacts($company, $contactsData);

        if ($result['success']) {
            $message = "Import completed! Imported: {$result['imported']}, Skipped: {$result['skipped']}";
            return redirect()->route('organization.sms.contacts.index')->with('success', $message);
        }

        return back()->with('error', $result['error'] ?? 'Failed to import contacts.');
    }

    /**
     * Delete multiple contacts
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:sms_contacts,id',
        ]);

        $company = Auth::guard('company')->user();

        $deleted = SmsContact::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->whereIn('id', $request->contact_ids)
            ->delete();

        return redirect()->route('organization.sms.contacts.index')
            ->with('success', "{$deleted} contact(s) deleted successfully!");
    }

    /**
     * Download sample CSV
     */
    public function downloadSample()
    {
        $csv = "Phone Number,Name,Email,Group\n";
        $csv .= "233241234567,John Doe,john@example.com,Customers\n";
        $csv .= "233551234567,Jane Smith,jane@example.com,Customers\n";
        $csv .= "233201234567,Bob Johnson,bob@example.com,Staff\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="contacts_sample.csv"');
    }
}
