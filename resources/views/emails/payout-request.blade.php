@component('mail::message')
# New Payout Request

A payout request has been submitted by an organizer.

## Payout Details

**Payout Number:** {{ $payout->payout_number }}
**Organization:** {{ $payout->company->name }}
**Event:** {{ $payout->event->title }}
**Gross Amount:** GH₵ {{ number_format($payout->gross_amount, 2) }}
**Platform Fees:** GH₵ {{ number_format($payout->platform_fees, 2) }}
**Net Amount:** GH₵ {{ number_format($payout->net_amount, 2) }}
**Total Tickets Sold:** {{ $payout->total_tickets_sold }}
**Total Attendees:** {{ $payout->total_attendees }}

## Payment Account Details

@if($payout->paymentAccount)
**Account Type:** {{ ucfirst(str_replace('_', ' ', $payout->paymentAccount->account_type)) }}

@if($payout->paymentAccount->account_type === 'bank')
**Bank Name:** {{ $payout->paymentAccount->bank_name }}
**Account Name:** {{ $payout->paymentAccount->account_name }}
**Account Number:** {{ $payout->paymentAccount->account_number }}
@if($payout->paymentAccount->branch)
**Branch:** {{ $payout->paymentAccount->branch }}
@endif
@else
**Network:** {{ $payout->paymentAccount->mobile_money_network }}
**Mobile Money Number:** {{ $payout->paymentAccount->mobile_money_number }}
**Account Name:** {{ $payout->paymentAccount->mobile_money_name }}
@endif
@else
**Payment Account:** Not set up yet
@endif

## Organizer Contact

**Name:** {{ $payout->company->name }}
**Email:** {{ $payout->company->email }}
**Phone:** {{ $payout->company->phone ?? 'N/A' }}

@component('mail::button', ['url' => route('admin.payouts.show', $payout)])
View Full Details
@endcomponent

Please review and process this payout request at your earliest convenience.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
