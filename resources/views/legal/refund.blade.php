@extends('layouts.app')

@section('title', 'Refund Policy - 9yt !Trybe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-900 dark:via-blue-900/10 dark:to-purple-900/10 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 gradient-text">Refund Policy</h1>
            <p class="text-lg text-gray-600 dark:text-gray-200">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <!-- Quick Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border-t-4 border-blue-500">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Quick Navigation
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <a href="#general" class="text-blue-600 dark:text-blue-400 hover:underline">→ General Policy</a>
                <a href="#tickets" class="text-blue-600 dark:text-blue-400 hover:underline">→ Event Tickets</a>
                <a href="#products" class="text-blue-600 dark:text-blue-400 hover:underline">→ Shop Products</a>
                <a href="#sms" class="text-blue-600 dark:text-blue-400 hover:underline">→ SMS Credits</a>
                <a href="#processing" class="text-blue-600 dark:text-blue-400 hover:underline">→ Refund Processing</a>
                <a href="#fees" class="text-blue-600 dark:text-blue-400 hover:underline">→ Service Fees</a>
                <a href="#chargebacks" class="text-blue-600 dark:text-blue-400 hover:underline">→ Chargebacks</a>
                <a href="#currency" class="text-blue-600 dark:text-blue-400 hover:underline">→ Currency</a>
                <a href="#tax" class="text-blue-600 dark:text-blue-400 hover:underline">→ Tax Refunds</a>
                <a href="#exceptions" class="text-blue-600 dark:text-blue-400 hover:underline">→ Exceptions</a>
                <a href="#denial" class="text-blue-600 dark:text-blue-400 hover:underline">→ Denial</a>
                <a href="#appeal" class="text-blue-600 dark:text-blue-400 hover:underline">→ Appeal Process</a>
                <a href="#modifications" class="text-blue-600 dark:text-blue-400 hover:underline">→ Modifications</a>
                <a href="#contact" class="text-blue-600 dark:text-blue-400 hover:underline">→ Contact Us</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 legal-content">
                <div class="prose max-w-none text-gray-800 dark:text-gray-100 dark:prose-invert">
                <h2 id="general">1. GENERAL REFUND POLICY</h2>
                <p>At 9yt !Trybe, we strive for customer satisfaction. This Refund Policy outlines the conditions under which refunds may be issued for tickets, products, and services purchased through our Platform.</p>

                <h2 id="tickets">2. EVENT TICKET REFUNDS</h2>

                <h3>2.1 Event Organizer Authority</h3>
                <p><strong>Important:</strong> Refund policies for event tickets are set by individual Event Organizers. We act only as a platform facilitating ticket sales. Each event may have different refund terms.</p>

                <h3>2.2 Event Cancellation by Organizer</h3>
                <p>If an Event Organizer cancels an event:</p>
                <ul>
                    <li>Full refund is typically issued to attendees</li>
                    <li>Refund processing time: 5-14 business days</li>
                    <li>Refunds are issued to the original payment method</li>
                    <li>Platform service fees may or may not be refunded (check event-specific terms)</li>
                </ul>

                <h3>2.3 Event Postponement</h3>
                <p>If an event is postponed to a new date:</p>
                <ul>
                    <li>Tickets typically remain valid for the new date</li>
                    <li>Refunds may be available if you cannot attend the new date</li>
                    <li>Contact the Event Organizer directly for postponement refund requests</li>
                    <li>Refund eligibility depends on the organizer's policy</li>
                </ul>

                <h3>2.4 Voluntary Cancellation by Attendee</h3>
                <p>If you wish to cancel your ticket purchase:</p>
                <ul>
                    <li>Check the event's specific refund policy before requesting a refund</li>
                    <li>Some events offer full refunds up to a certain date</li>
                    <li>Some events offer partial refunds (minus service fees)</li>
                    <li>Some events have a no-refund policy</li>
                    <li>Refund deadlines are set by Event Organizers</li>
                </ul>

                <h3>2.5 No-Show Policy</h3>
                <p>If you fail to attend an event:</p>
                <ul>
                    <li>Tickets are generally non-refundable for no-shows</li>
                    <li>Exceptions may apply (check event-specific policy)</li>
                    <li>Contact the Event Organizer as soon as possible</li>
                </ul>

                <h3>2.6 External Events</h3>
                <p>For events hosted on external ticketing platforms:</p>
                <ul>
                    <li>Refunds are governed by the external platform's policy</li>
                    <li>We have no control over external platform refunds</li>
                    <li>Contact the external platform directly for refund requests</li>
                </ul>

                <h2 id="products">3. SHOP PRODUCT REFUNDS</h2>

                <h3>3.1 Eligibility for Product Refunds</h3>
                <p>You may be eligible for a refund if:</p>
                <ul>
                    <li>The product is defective or damaged</li>
                    <li>The wrong product was delivered</li>
                    <li>The product significantly differs from the description</li>
                    <li>The product was not delivered within the promised timeframe</li>
                </ul>

                <h3>3.2 Refund Request Window</h3>
                <p>Product refund requests must be made within:</p>
                <ul>
                    <li><strong>14 days</strong> of delivery for defective/wrong items</li>
                    <li><strong>7 days</strong> for voluntary returns (if applicable)</li>
                    <li>Contact us immediately for non-delivery issues</li>
                </ul>

                <h3>3.3 Conditions for Product Returns</h3>
                <p>To qualify for a refund:</p>
                <ul>
                    <li>Products must be unused and in original condition</li>
                    <li>Original packaging must be intact</li>
                    <li>Tags and labels must not be removed</li>
                    <li>Proof of purchase (order number/receipt) required</li>
                    <li>Photos of defects/damage may be required</li>
                </ul>

                <h3>3.4 Non-Refundable Products</h3>
                <p>The following are generally non-refundable:</p>
                <ul>
                    <li>Customized or personalized items</li>
                    <li>Perishable goods</li>
                    <li>Intimate items (for hygiene reasons)</li>
                    <li>Digital downloads (once accessed)</li>
                    <li>Sale or clearance items (unless defective)</li>
                </ul>

                <h3>3.5 Return Shipping</h3>
                <p>Return shipping costs:</p>
                <ul>
                    <li>We cover shipping for defective/wrong items</li>
                    <li>Customer covers shipping for voluntary returns</li>
                    <li>Use tracked shipping methods for returns</li>
                    <li>Keep proof of shipment</li>
                </ul>

                <h2 id="sms">4. SMS CREDITS REFUND</h2>

                <h3>4.1 Non-Refundable SMS Credits</h3>
                <p><strong>SMS credits are generally non-refundable</strong> once purchased because:</p>
                <ul>
                    <li>Third-party SMS providers (Mnotify, Twilio) do not offer refunds</li>
                    <li>Credits are immediately available for use upon purchase</li>
                    <li>System resources are allocated upon credit purchase</li>
                </ul>

                <h3>4.2 Exceptions for SMS Refunds</h3>
                <p>Refunds may be considered in rare cases:</p>
                <ul>
                    <li>System error resulted in double charge</li>
                    <li>Payment was processed but credits were not delivered</li>
                    <li>Significant technical failure prevented credit use</li>
                </ul>

                <h3>4.3 Failed SMS Delivery</h3>
                <p>If SMS messages fail to deliver due to:</p>
                <ul>
                    <li><strong>Our fault:</strong> Credits may be refunded or re-credited</li>
                    <li><strong>Network issues:</strong> No refund (third-party responsibility)</li>
                    <li><strong>Invalid numbers:</strong> No refund (user error)</li>
                    <li><strong>Blocked by recipient:</strong> No refund</li>
                </ul>

                <h2 id="processing">5. REFUND PROCESSING</h2>

                <h3>5.1 How to Request a Refund</h3>
                <p>To request a refund:</p>
                <ul>
                    <li><strong>Email:</strong> 9yttrybe@gmail.com</li>
                    <li><strong>Phone:</strong> 0545566524 / 0267825223</li>
                    <li>Include: Order number, reason for refund, supporting evidence</li>
                </ul>

                <h3>5.2 Refund Processing Time</h3>
                <ul>
                    <li><strong>Review Period:</strong> 2-5 business days</li>
                    <li><strong>Approval Notification:</strong> Email confirmation</li>
                    <li><strong>Refund Processing:</strong> 5-14 business days</li>
                    <li><strong>Bank Processing:</strong> Additional 3-7 business days</li>
                </ul>

                <h3>5.3 Refund Method</h3>
                <p>Refunds are issued to:</p>
                <ul>
                    <li>The original payment method used for purchase</li>
                    <li>The same account/card that made the payment</li>
                    <li>Alternative methods may be used in exceptional cases</li>
                </ul>

                <h3>5.4 Partial Refunds</h3>
                <p>Partial refunds may be issued if:</p>
                <ul>
                    <li>Products are returned in less than perfect condition</li>
                    <li>Service fees are non-refundable</li>
                    <li>Processing fees are deducted</li>
                    <li>The Event Organizer's policy specifies partial refunds</li>
                </ul>

                <h2 id="fees">6. SERVICE FEES</h2>

                <h3>6.1 Platform Service Fees</h3>
                <p>Service fees charged by 9yt !Trybe are:</p>
                <ul>
                    <li>Generally non-refundable</li>
                    <li>Cover platform costs, payment processing, and support</li>
                    <li>May be refunded in cases of event cancellation by organizer</li>
                    <li>Clearly displayed before purchase confirmation</li>
                </ul>

                <h3>6.2 Payment Processing Fees</h3>
                <p>Fees charged by payment processors (Paystack, etc.):</p>
                <ul>
                    <li>Are non-refundable by us</li>
                    <li>Are third-party charges beyond our control</li>
                    <li>May be deducted from your refund amount</li>
                </ul>

                <h2 id="chargebacks">7. CHARGEBACKS AND DISPUTES</h2>

                <h3>7.1 Contact Us First</h3>
                <p>Before initiating a chargeback with your bank:</p>
                <ul>
                    <li>Contact us to resolve the issue</li>
                    <li>We are committed to fair resolutions</li>
                    <li>Chargebacks can result in account suspension</li>
                    <li>Disputed transactions may be investigated</li>
                </ul>

                <h3>7.2 Fraudulent Chargebacks</h3>
                <p>If you initiate a chargeback for a legitimate purchase:</p>
                <ul>
                    <li>Your account may be suspended or terminated</li>
                    <li>We may pursue recovery of funds</li>
                    <li>Legal action may be taken for fraud</li>
                </ul>

                <h2 id="currency">8. CURRENCY AND CONVERSION</h2>
                <p>Refunds are issued in Ghana Cedis (GH₵):</p>
                <ul>
                    <li>Exchange rate fluctuations may affect refund amounts</li>
                    <li>We are not responsible for currency conversion losses</li>
                    <li>Bank conversion fees may apply</li>
                </ul>

                <h2 id="tax">9. TAX REFUNDS</h2>
                <p>Regarding taxes on refunded purchases:</p>
                <ul>
                    <li>Taxes paid will be included in the refund</li>
                    <li>You are responsible for tax implications in your jurisdiction</li>
                    <li>Consult a tax professional if needed</li>
                </ul>

                <h2 id="exceptions">10. EXCEPTIONS AND SPECIAL CIRCUMSTANCES</h2>

                <h3>10.1 Force Majeure</h3>
                <p>In cases of force majeure (pandemics, natural disasters, etc.):</p>
                <ul>
                    <li>Normal refund policies may be adjusted</li>
                    <li>Refund processing may be delayed</li>
                    <li>We will communicate any changes</li>
                </ul>

                <h3>10.2 Legal Requirements</h3>
                <p>We comply with:</p>
                <ul>
                    <li>Ghana Consumer Protection laws</li>
                    <li>International consumer rights standards</li>
                    <li>Statutory rights that cannot be waived</li>
                </ul>

                <h2 id="denial">11. DENIAL OF REFUND</h2>
                <p>We may deny a refund request if:</p>
                <ul>
                    <li>The request is outside the refund window</li>
                    <li>Products were used or damaged by the customer</li>
                    <li>No proof of purchase is provided</li>
                    <li>The claim is fraudulent</li>
                    <li>Terms and Conditions were violated</li>
                </ul>

                <h2 id="appeal">12. APPEAL PROCESS</h2>
                <p>If your refund request is denied:</p>
                <ul>
                    <li>You will receive a written explanation</li>
                    <li>You may appeal the decision within 14 days</li>
                    <li>Provide additional evidence or information</li>
                    <li>Final decision will be communicated in writing</li>
                </ul>

                <h2 id="modifications">13. MODIFICATIONS TO THIS POLICY</h2>
                <p>We reserve the right to modify this Refund Policy at any time. Changes will be effective upon posting on the Platform.</p>

                <h2 id="contact">14. CONTACT FOR REFUNDS</h2>
                <p>For refund-related inquiries:</p>
                <ul>
                    <li><strong>Email:</strong> 9yttrybe@gmail.com</li>
                    <li><strong>Phone:</strong> 0545566524 / 0267825223</li>
                    <li><strong>Subject Line:</strong> "Refund Request - [Order Number]"</li>
                </ul>

                <div class="mt-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        <strong>Tip:</strong> Always check the specific refund policy for events before purchasing tickets. Each Event Organizer may have different terms. For shop products, carefully review product descriptions and take photos upon delivery to document any issues.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </a>
        </div>
    </div>
</div>
<style>
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.legal-content h2 {
    scroll-margin-top: 80px;
}

.legal-content h3 {
    scroll-margin-top: 80px;
}
</style>
@endsection
