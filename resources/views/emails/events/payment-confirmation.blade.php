@component('mail::message')
# Payment Processed Successfully! ✅

Hi **{{ $company->name }}**,

Great news! Your payout for **{{ $event->title }}** has been successfully processed.

## Payment Details

<table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
    <tr style="background: #f0fdf4;">
        <td style="padding: 16px 8px; font-size: 18px; font-weight: bold;">💵 Amount Sent</td>
        <td style="padding: 16px 8px; text-align: right; font-size: 22px; font-weight: bold; color: #10b981;">
            GH₵{{ number_format($payout->net_amount, 2) }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Payment Method</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
            @if($paymentAccount && $paymentAccount->isMobileMoney())
                📱 Mobile Money ({{ $paymentAccount->mobile_money_network }})
            @elseif($paymentAccount && $paymentAccount->isBankAccount())
                🏦 Bank Transfer ({{ $paymentAccount->bank_name }})
            @else
                Payment Account
            @endif
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Account Details</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right;">
            {{ $paymentAccount ? $paymentAccount->display_name : 'N/A' }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Transaction Reference</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; font-family: monospace; font-weight: bold;">
            {{ $payout->payout_reference ?? 'N/A' }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Date Processed</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right;">
            {{ $payout->completed_at ? $payout->completed_at->format('M d, Y \a\t h:i A') : 'N/A' }}
        </td>
    </tr>
</table>

@if($paymentAccount && $paymentAccount->isMobileMoney())
@component('mail::panel')
**📱 Mobile Money Note:** Please check your {{ $paymentAccount->mobile_money_network }} wallet for the payment. It may take a few minutes to reflect in your account.
@endcomponent
@elseif($paymentAccount && $paymentAccount->isBankAccount())
@component('mail::panel')
**🏦 Bank Transfer Note:** Bank transfers typically reflect within 24 hours during business days. Please check your {{ $paymentAccount->bank_name }} account.
@endcomponent
@endif

@component('mail::button', ['url' => $detailsUrl])
View Payment Details
@endcomponent

---

## Event Summary

- **Event:** {{ $event->title }}
- **Date:** {{ $event->start_date->format('M d, Y') }}
- **Tickets Sold:** {{ number_format($payout->total_tickets_sold) }}
- **Attendees:** {{ number_format($payout->total_attendees) }}
- **Gross Revenue:** GH₵{{ number_format($payout->gross_amount, 2) }}

---

## Host Another Event?

Thank you for being part of the **9yt !Trybe** community. We're excited to see what event you'll create next!

@component('mail::button', ['url' => route('organization.events.create'), 'color' => 'primary'])
Host Another Event
@endcomponent

---

Best regards,
**The 9yt !Trybe Team**

<small style="color: #666;">
Payout Number: {{ $payout->payout_number }}<br>
If you have any questions about this payment, please contact our support team.
</small>

@endcomponent
