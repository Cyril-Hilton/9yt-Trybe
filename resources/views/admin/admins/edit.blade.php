@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Admin Users
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl border-4 border-indigo-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <h3 class="text-2xl font-bold text-white">Edit Admin User</h3>
            <p class="text-indigo-100 text-sm mt-1">Update admin account details</p>
        </div>

        <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" required value="{{ old('name', $admin->name) }}"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required value="{{ old('email', $admin->email) }}"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <input type="password" name="password"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Role *</label>
                <select name="role" required
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role', $admin->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       {{ old('is_active', $admin->is_active) ? 'checked' : '' }}
                       class="h-5 w-5 rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                <label for="is_active" class="ml-3 text-sm font-semibold text-gray-700">
                    Account is active
                </label>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.admins.index') }}"
                   class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-medium hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-8 py-4 rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 font-bold">
                    Update Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
