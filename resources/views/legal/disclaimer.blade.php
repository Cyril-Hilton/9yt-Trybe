@extends('layouts.app')

@section('title', 'Disclaimer - 9yt !Trybe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-900 dark:via-blue-900/10 dark:to-purple-900/10 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 gradient-text">Disclaimer</h1>
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
                <a href="#general" class="text-blue-600 dark:text-blue-400 hover:underline">→ General</a>
                <a href="#advice" class="text-blue-600 dark:text-blue-400 hover:underline">→ Professional Advice</a>
                <a href="#events" class="text-blue-600 dark:text-blue-400 hover:underline">→ Event Information</a>
                <a href="#products" class="text-blue-600 dark:text-blue-400 hover:underline">→ Products</a>
                <a href="#pricing" class="text-blue-600 dark:text-blue-400 hover:underline">→ Pricing & Payment</a>
                <a href="#sms" class="text-blue-600 dark:text-blue-400 hover:underline">→ SMS Services</a>
                <a href="#technical" class="text-blue-600 dark:text-blue-400 hover:underline">→ Technical</a>
                <a href="#thirdparty" class="text-blue-600 dark:text-blue-400 hover:underline">→ Third-Party Links</a>
                <a href="#usercontent" class="text-blue-600 dark:text-blue-400 hover:underline">→ User Content</a>
                <a href="#financial" class="text-blue-600 dark:text-blue-400 hover:underline">→ Financial</a>
                <a href="#safety" class="text-blue-600 dark:text-blue-400 hover:underline">→ Health & Safety</a>
                <a href="#age" class="text-blue-600 dark:text-blue-400 hover:underline">→ Age Restrictions</a>
                <a href="#geographic" class="text-blue-600 dark:text-blue-400 hover:underline">→ Geographic</a>
                <a href="#forcemajeure" class="text-blue-600 dark:text-blue-400 hover:underline">→ Force Majeure</a>
                <a href="#currency" class="text-blue-600 dark:text-blue-400 hover:underline">→ Currency</a>
                <a href="#tax" class="text-blue-600 dark:text-blue-400 hover:underline">→ Tax</a>
                <a href="#translation" class="text-blue-600 dark:text-blue-400 hover:underline">→ Translation</a>
                <a href="#testimonials" class="text-blue-600 dark:text-blue-400 hover:underline">→ Testimonials</a>
                <a href="#changes" class="text-blue-600 dark:text-blue-400 hover:underline">→ Service Changes</a>
                <a href="#liability" class="text-blue-600 dark:text-blue-400 hover:underline">→ Liability</a>
                <a href="#risk" class="text-blue-600 dark:text-blue-400 hover:underline">→ Assumption of Risk</a>
                <a href="#jurisdiction" class="text-blue-600 dark:text-blue-400 hover:underline">→ Jurisdiction</a>
                <a href="#entire" class="text-blue-600 dark:text-blue-400 hover:underline">→ Entire Disclaimer</a>
                <a href="#contact" class="text-blue-600 dark:text-blue-400 hover:underline">→ Contact</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 legal-content">
                <div class="prose max-w-none text-gray-800 dark:text-gray-100 dark:prose-invert">
                <h2 id="general">1. GENERAL DISCLAIMER</h2>
                <p>The information provided by 9yt !Trybe ("we", "us", or "our") on this Platform is for general informational purposes only. All information is provided in good faith, however we make no representation or warranty of any kind, express or implied, regarding the accuracy, adequacy, validity, reliability, availability, or completeness of any information on the Platform.</p>

                <h2 id="advice">2. NO PROFESSIONAL ADVICE</h2>
                <p>The Platform is not a substitute for professional advice. You should not:</p>
                <ul>
                    <li>Rely on information on the Platform as a basis for making financial, legal, medical, or other professional decisions</li>
                    <li>Take any action based solely on the information provided without seeking appropriate professional advice</li>
                    <li>Consider the Platform a replacement for professional consultation</li>
                </ul>

                <h2 id="events">3. EVENT INFORMATION</h2>

                <h3>3.1 No Responsibility for Event Content</h3>
                <p>We disclaim all responsibility and liability for:</p>
                <ul>
                    <li>Accuracy of event descriptions, dates, times, and locations</li>
                    <li>Quality, safety, or legality of events listed</li>
                    <li>Conduct of Event Organizers or attendees</li>
                    <li>Cancellation, postponement, or modification of events</li>
                    <li>Event outcomes or experiences</li>
                </ul>

                <h3>3.2 Third-Party Events</h3>
                <p>Events are organized by independent third parties. We:</p>
                <ul>
                    <li>Do not endorse any specific event or organizer</li>
                    <li>Are not responsible for event execution</li>
                    <li>Cannot guarantee event quality or safety</li>
                    <li>Do not verify organizer credentials unless explicitly stated</li>
                </ul>

                <h3>3.3 External Events</h3>
                <p>For external events (those with ticketing on external platforms):</p>
                <ul>
                    <li>We merely provide information and redirect links</li>
                    <li>Transactions occur on third-party platforms</li>
                    <li>We have no control over external ticketing systems</li>
                    <li>Refunds and customer service are handled by the external platform</li>
                </ul>

                <h2 id="products">4. PRODUCT INFORMATION (SHOP)</h2>
                <p>Regarding products sold in our Shop:</p>
                <ul>
                    <li>Images may not accurately reflect actual product appearance</li>
                    <li>Colors may vary due to screen settings</li>
                    <li>Sizes may vary from standard measurements</li>
                    <li>Stock availability is not guaranteed until order confirmation</li>
                    <li>We reserve the right to discontinue products without notice</li>
                </ul>

                <h2 id="pricing">5. PRICING AND PAYMENT</h2>
                <p>We disclaim liability for:</p>
                <ul>
                    <li>Pricing errors or omissions</li>
                    <li>Currency conversion fluctuations</li>
                    <li>Payment processing delays or failures</li>
                    <li>Third-party payment gateway issues</li>
                    <li>Bank charges or transaction fees imposed by your financial institution</li>
                </ul>

                <h2 id="sms">6. SMS SERVICES</h2>

                <h3>6.1 Delivery Disclaimers</h3>
                <p>Regarding SMS services:</p>
                <ul>
                    <li>We cannot guarantee 100% delivery success</li>
                    <li>Delivery times may vary based on network conditions</li>
                    <li>Some networks may block or delay messages</li>
                    <li>Recipient devices must be active and able to receive SMS</li>
                    <li>International SMS may experience longer delays</li>
                </ul>

                <h3>6.2 Content Responsibility</h3>
                <p>You are solely responsible for:</p>
                <ul>
                    <li>SMS message content</li>
                    <li>Obtaining recipient consent</li>
                    <li>Compliance with anti-spam laws</li>
                    <li>Accuracy of recipient phone numbers</li>
                </ul>

                <h3>6.3 Third-Party SMS Providers</h3>
                <p>We use Mnotify (local) and Twilio (international). We disclaim liability for:</p>
                <ul>
                    <li>Their service availability or performance</li>
                    <li>Their data handling practices</li>
                    <li>Their pricing changes</li>
                    <li>Their terms of service</li>
                </ul>

                <h2 id="technical">7. TECHNICAL DISCLAIMERS</h2>

                <h3>7.1 Platform Availability</h3>
                <p>We do not guarantee:</p>
                <ul>
                    <li>Uninterrupted access to the Platform</li>
                    <li>Error-free operation</li>
                    <li>Virus-free environment</li>
                    <li>Compatibility with all devices or browsers</li>
                    <li>Continuity of any particular feature</li>
                </ul>

                <h3>7.2 Data Loss</h3>
                <p>We are not responsible for:</p>
                <ul>
                    <li>Loss of data due to technical failures</li>
                    <li>Data corruption or damage</li>
                    <li>Interruptions in service</li>
                    <li>User error or misuse</li>
                </ul>

                <h2 id="thirdparty">8. THIRD-PARTY LINKS AND CONTENT</h2>
                <p>Our Platform may contain links to third-party websites or services. We disclaim all responsibility for:</p>
                <ul>
                    <li>Content on linked websites</li>
                    <li>Privacy practices of third parties</li>
                    <li>Availability of external links</li>
                    <li>Accuracy of third-party information</li>
                    <li>Security of third-party sites</li>
                </ul>

                <h2 id="usercontent">9. USER-GENERATED CONTENT</h2>
                <p>Regarding content posted by users (reviews, comments, event descriptions):</p>
                <ul>
                    <li>We do not verify accuracy or truthfulness</li>
                    <li>Views expressed are those of the authors, not 9yt !Trybe</li>
                    <li>We do not endorse user opinions or statements</li>
                    <li>We are not responsible for offensive or inappropriate content</li>
                    <li>Moderation efforts are on a best-effort basis</li>
                </ul>

                <h2 id="financial">10. FINANCIAL AND INVESTMENT DISCLAIMER</h2>
                <p>Information on the Platform is not financial advice. We disclaim all responsibility for:</p>
                <ul>
                    <li>Investment decisions based on Platform information</li>
                    <li>Financial losses from event participation</li>
                    <li>Business decisions made using Platform data</li>
                    <li>Revenue forecasts or projections</li>
                </ul>

                <h2 id="safety">11. HEALTH AND SAFETY</h2>
                <p>Regarding event safety:</p>
                <ul>
                    <li>We do not verify safety measures at events</li>
                    <li>Event Organizers are responsible for health and safety compliance</li>
                    <li>Attendees assume all risks of event participation</li>
                    <li>We recommend checking event safety protocols before attending</li>
                </ul>

                <h2 id="age">12. AGE RESTRICTIONS</h2>
                <p>We disclaim liability for:</p>
                <ul>
                    <li>Age verification accuracy</li>
                    <li>Minors accessing age-restricted content</li>
                    <li>Events that violate age restrictions</li>
                </ul>
                <p>Parents/guardians are responsible for monitoring minor's use of the Platform.</p>

                <h2 id="geographic">13. GEOGRAPHIC RESTRICTIONS</h2>
                <p>Our Platform is based in Ghana. We do not guarantee:</p>
                <ul>
                    <li>Compliance with laws outside Ghana</li>
                    <li>Service availability in all countries</li>
                    <li>Accuracy of location-based features</li>
                </ul>

                <h2 id="forcemajeure">14. FORCE MAJEURE</h2>
                <p>We are not liable for failures or delays caused by circumstances beyond our reasonable control, including:</p>
                <ul>
                    <li>Natural disasters (earthquakes, floods, fires)</li>
                    <li>War, terrorism, civil unrest</li>
                    <li>Pandemics or health emergencies</li>
                    <li>Government actions or regulations</li>
                    <li>Internet or telecommunications failures</li>
                    <li>Power outages</li>
                    <li>Strikes or labor disputes</li>
                </ul>

                <h2 id="currency">15. CURRENCY AND EXCHANGE RATES</h2>
                <p>All prices are listed in Ghana Cedis (GH₵) unless otherwise stated. We disclaim liability for:</p>
                <ul>
                    <li>Currency conversion errors</li>
                    <li>Exchange rate fluctuations</li>
                    <li>Bank or payment processor conversion fees</li>
                </ul>

                <h2 id="tax">16. TAX RESPONSIBILITY</h2>
                <p>Users are responsible for:</p>
                <ul>
                    <li>Determining their tax obligations</li>
                    <li>Paying applicable taxes on purchases</li>
                    <li>Reporting income (for Event Organizers)</li>
                    <li>Compliance with tax laws</li>
                </ul>

                <h2 id="translation">17. TRANSLATION DISCLAIMER</h2>
                <p>If the Platform is available in multiple languages:</p>
                <ul>
                    <li>The English version is authoritative</li>
                    <li>Translations are provided for convenience only</li>
                    <li>We do not guarantee translation accuracy</li>
                </ul>

                <h2 id="testimonials">18. TESTIMONIALS AND REVIEWS</h2>
                <p>Testimonials and reviews reflect individual experiences. We disclaim liability for:</p>
                <ul>
                    <li>Accuracy of testimonials</li>
                    <li>Representativeness of reviews</li>
                    <li>Guarantee of similar results</li>
                </ul>

                <h2 id="changes">19. CHANGES TO SERVICES</h2>
                <p>We reserve the right to:</p>
                <ul>
                    <li>Modify or discontinue services without notice</li>
                    <li>Change features or functionality</li>
                    <li>Update pricing structures</li>
                    <li>Remove content or accounts</li>
                </ul>

                <h2 id="liability">20. LIMITATION OF LIABILITY</h2>
                <p><strong>UNDER NO CIRCUMSTANCES SHALL 9YT !TRYBE, ITS OFFICERS, DIRECTORS, EMPLOYEES, OR AGENTS BE LIABLE FOR ANY:</strong></p>
                <ul>
                    <li>Direct, indirect, incidental, special, or consequential damages</li>
                    <li>Loss of profits, revenue, data, or goodwill</li>
                    <li>Business interruption</li>
                    <li>Personal injury or property damage</li>
                    <li>Emotional distress</li>
                </ul>
                <p><strong>ARISING FROM OR RELATED TO YOUR USE OF THE PLATFORM, EVEN IF WE HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</strong></p>

                <h2 id="risk">21. ASSUMPTION OF RISK</h2>
                <p>By using the Platform, you acknowledge and agree that:</p>
                <ul>
                    <li>You use the Platform at your own risk</li>
                    <li>You are solely responsible for verifying event information</li>
                    <li>You assume all risks associated with online transactions</li>
                    <li>You are responsible for data backup and security</li>
                    <li>You understand that internet use involves inherent security risks</li>
                </ul>

                <h2 id="jurisdiction">22. JURISDICTION-SPECIFIC DISCLAIMERS</h2>

                <h3>22.1 Ghana</h3>
                <p>This disclaimer complies with Ghana's Consumer Protection Act and other applicable laws. However, statutory consumer rights cannot be excluded.</p>

                <h3>22.2 International Users</h3>
                <p>If you access the Platform from outside Ghana:</p>
                <ul>
                    <li>You do so at your own initiative</li>
                    <li>You are responsible for compliance with local laws</li>
                    <li>Ghana law governs any disputes</li>
                </ul>

                <h2 id="entire">23. ENTIRE DISCLAIMER</h2>
                <p>This disclaimer should be read in conjunction with our:</p>
                <ul>
                    <li><a href="{{ route('legal.terms') }}" class="text-cyan-600 hover:text-cyan-700 dark:text-cyan-400">Terms and Conditions</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="text-cyan-600 hover:text-cyan-700 dark:text-cyan-400">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.refund') }}" class="text-cyan-600 hover:text-cyan-700 dark:text-cyan-400">Refund Policy</a></li>
                </ul>

                <h2 id="contact">24. CONTACT</h2>
                <p>For questions about this disclaimer:</p>
                <ul>
                    <li><strong>Email:</strong> 9yttrybe@gmail.com</li>
                    <li><strong>Phone:</strong> 0545566524 / 0267825223</li>
                </ul>

                <div class="mt-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-800 dark:text-red-200">
                        <strong>IMPORTANT:</strong> By using this Platform, you acknowledge that you have read, understood, and agree to be bound by this Disclaimer. If you do not agree with any part of this Disclaimer, you must not use the Platform.
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
