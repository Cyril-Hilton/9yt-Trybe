<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 3px solid #06b6d4;
        }
        .header h1 {
            color: #0e7490;
            margin: 0 0 8px 0;
            font-size: 28px;
        }
        .success-icon {
            width: 64px;
            height: 64px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .success-icon::after {
            content: "✓";
            color: white;
            font-size: 36px;
            font-weight: bold;
        }
        .order-info {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .order-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-info td {
            padding: 8px 0;
        }
        .order-info td:first-child {
            font-weight: 600;
            color: #0369a1;
            width: 140px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #0e7490;
            margin: 24px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        .customer-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .detail-box {
            background-color: #f9fafb;
            padding: 16px;
            border-radius: 8px;
        }
        .detail-box h3 {
            margin: 0 0 12px 0;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
        }
        .detail-box p {
            margin: 4px 0;
            color: #6b7280;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .item-name {
            font-weight: 600;
            color: #111827;
        }
        .item-details {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }
        .totals {
            margin: 24px 0;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 0;
        }
        .totals td:last-child {
            text-align: right;
            font-weight: 600;
        }
        .totals .total-row {
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
        }
        .totals .total-row td {
            font-size: 20px;
            font-weight: 700;
            color: #0e7490;
            padding-top: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 12px 0;
            color: #1e40af;
            font-size: 16px;
        }
        .info-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .info-box li {
            color: #1e3a8a;
            margin: 6px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(to right, #0891b2, #2563eb);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            background: linear-gradient(to right, #0e7490, #1d4ed8);
        }
        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #0891b2;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
            .customer-details {
                grid-template-columns: 1fr;
            }
            .items-table {
                font-size: 14px;
            }
            .items-table th,
            .items-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon"></div>
            <h1>Order Confirmed!</h1>
            <p style="color: #6b7280; font-size: 16px; margin: 0;">Thank you for your purchase</p>
        </div>

        <p>Dear {{ $order->customer_name }},</p>
        <p>We're excited to confirm that we've received your order and are processing it right away!</p>

        <div class="order-info">
            <table>
                <tr>
                    <td>Order Number:</td>
                    <td><strong>{{ $order->order_number }}</strong></td>
                </tr>
                <tr>
                    <td>Order Date:</td>
                    <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                <tr>
                    <td>Payment Status:</td>
                    <td>
                        <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Payment Method:</td>
                    <td>{{ $order->payment_method === 'cash_on_delivery' ? 'Cash on Delivery' : 'Card Payment (Paystack)' }}</td>
                </tr>
            </table>
        </div>

        <h2 class="section-title">Customer & Shipping Details</h2>
        <div class="customer-details">
            <div class="detail-box">
                <h3>Customer Information</h3>
                <p>{{ $order->customer_name }}</p>
                <p>{{ $order->customer_email }}</p>
                <p>{{ $order->customer_phone }}</p>
            </div>
            <div class="detail-box">
                <h3>Shipping Address</h3>
                <p>{{ $order->shipping_address }}</p>
                <p>{{ $order->city }}@if($order->region), {{ $order->region }}@endif</p>
            </div>
        </div>

        <h2 class="section-title">Order Items</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        <div class="item-details">
                            @if($item->size)Size: {{ $item->size }} @endif
                            @if($item->color)• Color: {{ $item->color }}@endif
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">GH₵{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;"><strong>GH₵{{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>GH₵{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Shipping:</td>
                    <td>
                        @if($order->shipping_fee > 0)
                            GH₵{{ number_format($order->shipping_fee, 2) }}
                        @else
                            <span style="color: #10b981; font-weight: 700;">FREE</span>
                        @endif
                    </td>
                </tr>
                <tr class="total-row">
                    <td>Total:</td>
                    <td>GH₵{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($order->notes)
        <div class="detail-box" style="margin: 20px 0;">
            <h3>Order Notes</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <div class="info-box">
            <h3>What's Next?</h3>
            <ul>
                <li>We'll send you an update when your order ships</li>
                <li>Track your order status in your account dashboard</li>
                <li>Expected delivery: 3-7 business days</li>
                @if($order->payment_method === 'cash_on_delivery')
                <li><strong>Please have exact cash ready upon delivery</strong></li>
                @endif
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('shop.order.confirmation', $order->order_number) }}" class="button">
                View Order Details
            </a>
        </div>

        <div class="footer">
            <p>Need help? Contact us at <a href="mailto:support@9yttrybe.com">support@9yttrybe.com</a></p>
            <p style="margin-top: 16px;">&copy; {{ date('Y') }} 9yt !Trybe. All rights reserved.</p>
            <p style="margin-top: 8px;">
                <a href="{{ route('shop.index') }}">Continue Shopping</a> |
                <a href="{{ route('home') }}">Visit Our Website</a>
            </p>
        </div>
    </div>
</body>
</html>
