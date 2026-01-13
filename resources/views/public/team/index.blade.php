@extends('layouts.app')

@section('title', 'Meet the Team - Join the Trybe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <!-- Meet the Team Section -->
    @if($teamMembers->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-6">Meet the Trybe</h1>
            <div class="w-24 h-1 bg-gradient-to-r from-cyan-600 to-blue-600 mx-auto mb-8"></div>
            <p class="text-xl text-gray-600 dark:text-gray-400">The passionate people behind 9yt !Trybe</p>
        </div>

        @foreach(['Staff', 'Volunteer', 'Intern'] as $role)
            @if($teamMembers->has($role))
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">{{ $role }}s</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($teamMembers[$role] as $member)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $member->full_name }}</h3>
                            <p class="text-cyan-600 dark:text-cyan-400 font-semibold">{{ $member->title }}</p>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">{{ $member->job_description }}</p>

                        <div class="space-y-2 mb-4">
                            @if($member->portfolio_link)
                            <a href="{{ $member->portfolio_link }}" target="_blank" rel="noopener" class="flex items-center text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Portfolio
                            </a>
                            @endif

                            @if($member->socials)
                            <p class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                                </svg>
                                {{ $member->socials }}
                            </p>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="mailto:{{ $member->email }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Contact
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    <!-- Join the Team Section -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-6">Join the Trybe</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-cyan-600 to-blue-600 mx-auto mb-8"></div>
            <p class="text-xl text-gray-600 dark:text-gray-400">Ready to be part of the energy? Sign up as a volunteer or a staff member below.</p>
        </div>

        @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-green-800 dark:text-green-200 text-center font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        <form action="{{ route('team.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Event Coordinator, Marketing Specialist" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role *</label>
                    <select name="role" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select a Role</option>
                        <option value="Volunteer">Volunteer</option>
                        <option value="Staff">Staff</option>
                        <option value="Intern">Intern</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Job Description *</label>
                    <textarea name="job_description" required rows="4" placeholder="Tell us about your experience and what you'd like to contribute..." class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Portfolio Link (Optional)</label>
                    <input type="url" name="portfolio_link" placeholder="https://" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Number *</label>
                    <input type="tel" name="contact_number" required placeholder="0XX XXX XXXX" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Socials (Optional)</label>
                    <input type="text" name="socials" placeholder="e.g., @9yttrybe" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-bold hover:from-cyan-700 hover:to-blue-700 transition shadow-lg text-lg">
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
