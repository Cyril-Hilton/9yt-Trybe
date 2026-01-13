@extends('layouts.app')

@section('title', 'Cookie Policy - 9yt !Trybe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-900 dark:via-blue-900/10 dark:to-purple-900/10 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 gradient-text">Cookie Policy</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Last Updated: {{ date('F d, Y') }}</p>
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
                <a href="#what" class="text-blue-600 dark:text-blue-400 hover:underline">→ What Are Cookies?</a>
                <a href="#usage" class="text-blue-600 dark:text-blue-400 hover:underline">→ How We Use Cookies</a>
                <a href="#types" class="text-blue-600 dark:text-blue-400 hover:underline">→ Types of Cookies</a>
                <a href="#thirdparty" class="text-blue-600 dark:text-blue-400 hover:underline">→ Third-Party Cookies</a>
                <a href="#managing" class="text-blue-600 dark:text-blue-400 hover:underline">→ Managing Cookies</a>
                <a href="#duration" class="text-blue-600 dark:text-blue-400 hover:underline">→ Cookie Duration</a>
                <a href="#updates" class="text-blue-600 dark:text-blue-400 hover:underline">→ Policy Updates</a>
                <a href="#contact" class="text-blue-600 dark:text-blue-400 hover:underline">→ Contact Us</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 legal-content">

                <!-- Section 1 -->
                <div id="what" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">WHAT ARE COOKIES?</h2>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed pl-11 mb-4">Cookies are small text files stored on your device when you visit our Platform. They help us provide you with a better experience by remembering your preferences and understanding how you use our service.</p>
                </div>

                <!-- Section 2 -->
                <div id="usage" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">HOW WE USE COOKIES</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We use cookies for:</p>
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Essential Functions:</strong> Login sessions, shopping cart, security</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Performance:</strong> Understanding how users interact with our Platform</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Functionality:</strong> Remembering your preferences (dark mode, language)</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Analytics:</strong> Tracking visitor statistics</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Section 3 -->
                <div id="types" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">3</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">TYPES OF COOKIES WE USE</h2>
                    </div>
                    <div class="pl-11 space-y-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">3.1 Essential Cookies (Always Active)</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">These cookies are necessary for the Platform to function:</p>
                            <ul class="space-y-1 text-gray-700 dark:text-gray-300">
                                <li>• <strong>Session Cookies:</strong> Keep you logged in</li>
                                <li>• <strong>Security Cookies:</strong> Protect against fraud</li>
                                <li>• <strong>Shopping Cart:</strong> Remember items in your cart</li>
                                <li>• <strong>CSRF Protection:</strong> Prevent cross-site request forgery</li>
                            </ul>
                        </div>

                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">3.2 Performance Cookies</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">Help us understand Platform usage:</p>
                            <ul class="space-y-1 text-gray-700 dark:text-gray-300">
                                <li>• Page views and navigation patterns</li>
                                <li>• Error tracking</li>
                                <li>• Load times and performance metrics</li>
                            </ul>
                        </div>

                        <div class="border-l-4 border-green-500 pl-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">3.3 Functional Cookies</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">Remember your preferences:</p>
                            <ul class="space-y-1 text-gray-700 dark:text-gray-300">
                                <li>• Dark mode preference</li>
                                <li>• Language selection</li>
                                <li>• Location settings</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 4 -->
                <div id="thirdparty" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">4</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">THIRD-PARTY COOKIES</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We may use third-party services that set cookies:</p>
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <li>• <strong>Payment Processors:</strong> Paystack for secure transactions</li>
                            <li>• <strong>Analytics:</strong> Usage statistics and insights</li>
                            <li>• <strong>Social Media:</strong> Share buttons (if applicable)</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 5 -->
                <div id="managing" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">5</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">MANAGING COOKIES</h2>
                    </div>
                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">5.1 Browser Settings</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">You can control cookies through your browser settings:</p>
                            <ul class="space-y-1 text-gray-700 dark:text-gray-300">
                                <li>• <strong>Chrome:</strong> Settings → Privacy and Security → Cookies</li>
                                <li>• <strong>Firefox:</strong> Options → Privacy & Security → Cookies</li>
                                <li>• <strong>Safari:</strong> Preferences → Privacy → Cookies</li>
                                <li>• <strong>Edge:</strong> Settings → Privacy → Cookies</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border-l-4 border-yellow-500">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">5.2 Disabling Cookies</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">You can disable cookies, but this may affect Platform functionality:</p>
                            <ul class="space-y-1 text-gray-700 dark:text-gray-300">
                                <li>⚠️ You may not be able to log in</li>
                                <li>⚠️ Shopping cart may not work</li>
                                <li>⚠️ Preferences won't be saved</li>
                                <li>⚠️ Some features may be unavailable</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 6 -->
                <div id="duration" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">6</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">COOKIE DURATION</h2>
                    </div>
                    <div class="pl-11">
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <li>• <strong>Session Cookies:</strong> Deleted when you close your browser</li>
                            <li>• <strong>Persistent Cookies:</strong> Remain for a set period (usually 1 year)</li>
                            <li>• <strong>Authentication:</strong> Cleared on logout</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 7 -->
                <div id="updates" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">7</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">UPDATES TO THIS POLICY</h2>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed pl-11">We may update this Cookie Policy. Check the "Last Updated" date for changes.</p>
                </div>

                <!-- Section 8 -->
                <div id="contact" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">8</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">CONTACT US</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">Questions about cookies? Contact us:</p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <strong>Email:</strong>&nbsp;9yttrybe@gmail.com
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <strong>Phone:</strong>&nbsp;0545566524 / 0267825223
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection
