@component('mail::message')
# Congratulations on Your Successful Event! 🎉

Hi **{{ $company->name }}**,

Your event **{{ $event->title }}** has concluded, and we're thrilled to share the amazing results with you!

## Event Performance Summary

<table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Event Date</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
            {{ $event->start_date->format('M d, Y') }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Tickets Sold</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
            {{ number_format($payout->total_tickets_sold) }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Total Attendees</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
            {{ number_format($payout->total_attendees) }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Gross Revenue</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
            GH₵{{ number_format($payout->gross_amount, 2) }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee;">Platform Commission (4%)</td>
        <td style="padding: 12px 8px; border-bottom: 1px solid #eee; text-align: right; color: #666;">
            -GH₵{{ number_format($payout->platform_fees, 2) }}
        </td>
    </tr>
    <tr style="background: #f0fdf4;">
        <td style="padding: 16px 8px; font-size: 18px; font-weight: bold;">💰 Net Payout</td>
        <td style="padding: 16px 8px; text-align: right; font-size: 22px; font-weight: bold; color: #10b981;">
            GH₵{{ number_format($payout->net_amount, 2) }}
        </td>
    </tr>
</table>

---

## Next Step: Receive Your Earnings

To receive your payout of **GH₵{{ number_format($payout->net_amount, 2) }}**, please set up your payment details by choosing one of the following:

✅ **Mobile Money** (MTN, Airtel/AT, Telecel)
✅ **Bank Transfer**

@component('mail::button', ['url' => $setupUrl, 'color' => 'success'])
Set Up Payment Details
@endcomponent

Once you've provided your payment information, our team will process your payout within **2-3 business days**.

---

## Why Choose 9yt !Trybe?

We offer the **lowest commission rate in Ghana** at only **4%** - compared to 5-7.5% from competitors. This means **you keep more** of your hard-earned revenue!

Thank you for choosing **9yt !Trybe** for your event. We look forward to hosting your next event!

@component('mail::panel')
**Need Help?** If you have any questions about your payout, feel free to reach out to our support team.
@endcomponent

Best regards,
**The 9yt !Trybe Team**

<small style="color: #666;">
Payout Number: {{ $payout->payout_number }}
</small>

@endcomponent
