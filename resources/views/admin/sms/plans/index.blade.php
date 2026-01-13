@extends('layouts.admin')

@section('title', 'SMS Plans Management')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        SMS Plans Management
                    </h1>
                    <p class="mt-2 text-gray-600">Manage SMS credit packages and pricing</p>
                </div>
                <a href="{{ route('admin.sms.plans.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Plan
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Plans</p>
                        <p class="text-3xl font-black text-gray-900">{{ $plans->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border-2 border-green-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Active Plans</p>
                        <p class="text-3xl font-black text-green-600">{{ $plans->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border-2 border-yellow-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Inactive Plans</p>
                        <p class="text-3xl font-black text-yellow-600">{{ $plans->where('is_active', false)->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border-2 border-purple-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Featured Plans</p>
                        <p class="text-3xl font-black text-purple-600">{{ $plans->whereNotNull('badge')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($plans->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No SMS Plans Yet</h3>
                <p class="text-gray-600 mb-6">Create your first SMS plan to get started</p>
                <a href="{{ route('admin.sms.plans.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create First Plan
                </a>
            </div>
        @else
            <!-- Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                    <div class="relative bg-white rounded-2xl shadow-xl border-2 {{ $plan->is_active ? 'border-indigo-200' : 'border-gray-200 opacity-75' }} overflow-hidden group hover:shadow-2xl transition-all duration-200">
                        <!-- Badge -->
                        @if($plan->badge)
                            <div class="absolute top-0 right-0 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl">
                                {{ $plan->badge }}
                            </div>
                        @endif

                        <!-- Status Indicator -->
                        <div class="absolute top-4 left-4">
                            @if($plan->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <div class="pt-16 px-6 pb-6">
                            <!-- Plan Name -->
                            <h3 class="text-xl font-black text-gray-900 mb-2">{{ $plan->name }}</h3>

                            @if($plan->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $plan->description }}</p>
                            @endif

                            <!-- Credits -->
                            <div class="mb-4">
                                <p class="text-4xl font-black text-indigo-600">{{ number_format($plan->sms_credits) }}</p>
                                <p class="text-sm text-gray-500">SMS Credits</p>
                            </div>

                            <!-- Price -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <p class="text-3xl font-black text-gray-900">
                                    GH₵ {{ number_format($plan->price, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    GH₵ {{ number_format($plan->price / $plan->sms_credits, 4) }} per credit
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2">
                                <a href="{{ route('admin.sms.plans.edit', $plan) }}" class="block w-full text-center px-4 py-2 bg-indigo-100 text-indigo-700 font-bold rounded-lg hover:bg-indigo-200 transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Plan
                                </a>

                                <form method="POST" action="{{ route('admin.sms.plans.toggle-status', $plan) }}" class="inline-block w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full px-4 py-2 {{ $plan->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} font-bold rounded-lg transition-all duration-200">
                                        @if($plan->is_active)
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Deactivate
                                        @else
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Activate
                                        @endif
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.sms.plans.destroy', $plan) }}" onsubmit="return confirm('Are you sure you want to delete this plan? This action cannot be undone.')" class="inline-block w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition-all duration-200">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
