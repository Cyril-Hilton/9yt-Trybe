@extends('layouts.app')

@section('title', 'Fee Comparison - 9yt !Trybe vs Competitors')
@section('meta_description', 'Compare 9yt !Trybe fees with Eventbrite and Ticketmaster.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">Fee Comparison</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Transparent pricing comparisons across common ticket prices.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
                <h2 class="text-xl font-bold text-white">Ticket Price Comparison</h2>
            </div>

            @if(!empty($comparisons))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ticket Price</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">9yt Trybe Fees</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">9yt Customer Pays</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Eventbrite Fees</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ticketmaster Fees</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Savings vs Eventbrite</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($comparisons as $comparison)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        GHS {{ number_format($comparison['ticket_price'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        GHS {{ number_format($comparison['9yt_trybe']['fees'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                        GHS {{ number_format($comparison['9yt_trybe']['customer_pays'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        GHS {{ number_format($comparison['eventbrite']['fees'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        GHS {{ number_format($comparison['ticketmaster']['fees'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-indigo-600">
                                        GHS {{ number_format($comparison['savings_vs_eventbrite'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 4h6m-6-9h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No comparison data</h3>
                    <p class="text-gray-600 dark:text-gray-400">Try again later.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
