<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::latest()->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:admin,super_admin'],
        ]);

        Admin::create($validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user created successfully!');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'in:admin,super_admin'],
            'is_active' => ['boolean'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user updated successfully!');
    }

    public function destroy(Admin $admin)
    {
        // Prevent deleting yourself
        if ($admin->id === auth()->guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting the last super admin
        if ($admin->isSuperAdmin() && Admin::where('role', 'super_admin')->count() === 1) {
            return back()->with('error', 'Cannot delete the last super admin!');
        }

        $adminName = $admin->name;
        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', "Admin '{$adminName}' deleted successfully!");
    }
}
