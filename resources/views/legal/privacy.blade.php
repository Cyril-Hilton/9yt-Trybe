@extends('layouts.app')

@section('title', 'Privacy Policy - 9yt !Trybe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-900 dark:via-blue-900/10 dark:to-purple-900/10 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 gradient-text">Privacy Policy</h1>
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
                <a href="#introduction" class="text-blue-600 dark:text-blue-400 hover:underline">→ Introduction</a>
                <a href="#collection" class="text-blue-600 dark:text-blue-400 hover:underline">→ Data Collection</a>
                <a href="#usage" class="text-blue-600 dark:text-blue-400 hover:underline">→ How We Use Data</a>
                <a href="#sharing" class="text-blue-600 dark:text-blue-400 hover:underline">→ Data Sharing</a>
                <a href="#rights" class="text-blue-600 dark:text-blue-400 hover:underline">→ Your Rights</a>
                <a href="#security" class="text-blue-600 dark:text-blue-400 hover:underline">→ Security</a>
                <a href="#retention" class="text-blue-600 dark:text-blue-400 hover:underline">→ Data Retention</a>
                <a href="#cookies" class="text-blue-600 dark:text-blue-400 hover:underline">→ Cookies</a>
                <a href="#contact" class="text-blue-600 dark:text-blue-400 hover:underline">→ Contact Us</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 legal-content">
                <!-- Section 1 -->
                <div id="introduction" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">INTRODUCTION</h2>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed pl-11 mb-4">9yt !Trybe ("we", "us", "our") is committed to protecting your privacy and personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Platform.</p>

                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">This policy complies with:</p>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Ghana Data Protection Act, 2012 (Act 843)</strong></p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Electronic Communications Act, 2008 (Act 775)</strong></p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>GDPR</strong> (General Data Protection Regulation) principles</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>International data protection standards</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div id="controller" class="mb-10 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/10 dark:to-pink-900/10 rounded-lg p-6">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">DATA CONTROLLER INFORMATION</h2>
                    </div>
                    <div class="pl-11 space-y-4">
                        <p class="text-gray-700 dark:text-gray-300"><strong>Data Controller:</strong> 9yt !Trybe</p>
                        <div>
                            <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Contact:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Email: 9yttrybe@gmail.com</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Phone: 0545566524 / 0267825223</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Location: Ghana</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div id="collection" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-full flex items-center justify-center font-bold mr-3">3</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">INFORMATION WE COLLECT</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">3.1 Personal Information You Provide</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Account Information:</strong> Name, email address, phone number, password</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Profile Information:</strong> Profile photo, bio, social media links</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Payment Information:</strong> Billing address, payment method details (processed by third-party payment processors)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Event Information:</strong> Event details, venue information, ticket preferences</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Communication Data:</strong> Messages, support tickets, feedback</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Identity Verification:</strong> Government ID (when required for event organizers)</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">3.2 Information Collected Automatically</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Device Information:</strong> IP address, browser type, device type, operating system</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Usage Data:</strong> Pages viewed, time spent, clicks, navigation patterns</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Location Data:</strong> General location (city, region) based on IP address</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Cookies and Tracking:</strong> Session data, preferences, analytics data</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Transaction Data:</strong> Purchase history, ticket orders, shop orders</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">3.3 Information from Third Parties</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Payment Processors:</strong> Transaction status, payment verification</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Social Media:</strong> Public profile information (if you connect social accounts)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Analytics Providers:</strong> Usage statistics, demographic information</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4 -->
                <div id="legal" class="mb-10 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/10 dark:to-blue-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-cyan-100 dark:bg-cyan-900 text-cyan-600 dark:text-cyan-300 rounded-full flex items-center justify-center font-bold mr-3">4</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">LEGAL BASIS FOR PROCESSING (GDPR)</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">We process your data based on:</p>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Consent:</strong> You have given clear consent for us to process your data</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Contract:</strong> Processing is necessary to fulfill our contract with you</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Legal Obligation:</strong> We must process data to comply with the law</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Legitimate Interests:</strong> Processing is in our or a third party's legitimate interests</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5 -->
                <div id="usage" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-full flex items-center justify-center font-bold mr-3">5</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">HOW WE USE YOUR INFORMATION</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">5.1 Core Services</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Create and manage your account</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Process ticket purchases and shop orders</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Facilitate event registrations</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Send order confirmations and tickets</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Provide customer support</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Process payments and refunds</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">5.2 Communication</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Send transactional emails (order confirmations, password resets)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Send event updates and reminders</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Send marketing communications (with your consent)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Respond to inquiries and support requests</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Send SMS notifications (for event updates, order status)</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">5.3 Platform Improvement</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Analyze usage patterns and trends</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Improve user experience and features</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Conduct research and analytics</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Develop new products and services</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Detect and prevent fraud</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">5.4 Legal and Security</h3>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Comply with legal obligations</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Enforce our Terms and Conditions</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Protect against fraud and abuse</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Resolve disputes</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Ensure platform security</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 6 -->
                <div id="sms" class="mb-10 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/10 dark:to-teal-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-300 rounded-full flex items-center justify-center font-bold mr-3">6</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">SMS DATA COLLECTION AND USAGE</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">6.1 Bulk SMS Services</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-3">When you use our SMS services:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">We collect recipient phone numbers and message content</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">We track SMS delivery status and campaign performance</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">We store sender IDs for approval and fraud prevention</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Local SMS uses Mnotify (Ghana-based, subject to their privacy policy)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">International SMS uses Twilio (subject to their privacy policy)</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-300 mb-2">6.2 SMS Consent</h3>
                            <p class="text-sm text-yellow-800 dark:text-yellow-400">You must obtain proper consent from recipients before sending marketing SMS. We are not responsible for your compliance with consent requirements.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 7 -->
                <div id="sharing" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300 rounded-full flex items-center justify-center font-bold mr-3">7</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">DATA SHARING AND DISCLOSURE</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">7.1 Service Providers</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-3">We share data with trusted third parties who help us operate:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Payment Processors:</strong> Paystack (for payment processing)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>SMS Providers:</strong> Mnotify (local), Twilio (international)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Cloud Hosting:</strong> Server and database hosting providers</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Email Services:</strong> Transactional and marketing email providers</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Analytics:</strong> Website analytics and tracking services</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">7.2 Event Organizers</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">If you purchase tickets, we share your name, email, and phone number with the Event Organizer for event management purposes.</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">7.3 Legal Requirements</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-3">We may disclose your information if required to:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Comply with legal processes or government requests</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Enforce our Terms and Conditions</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Protect our rights, property, or safety</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Prevent fraud or illegal activities</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Cooperate with law enforcement</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">7.4 Business Transfers</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">In the event of a merger, acquisition, or sale of assets, your data may be transferred to the acquiring entity.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 8 -->
                <div id="retention" class="mb-10 bg-gradient-to-r from-pink-50 to-rose-50 dark:from-pink-900/10 dark:to-rose-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-pink-100 dark:bg-pink-900 text-pink-600 dark:text-pink-300 rounded-full flex items-center justify-center font-bold mr-3">8</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">DATA RETENTION</h2>
                    </div>
                    <div class="pl-11 space-y-4">
                        <div>
                            <p class="text-gray-700 dark:text-gray-300 mb-3">We retain your data for as long as necessary to:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Provide our services</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Comply with legal obligations (e.g., tax records for 7 years)</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Resolve disputes and enforce agreements</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Prevent fraud</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-2 border-pink-200 dark:border-pink-700">
                            <p class="text-gray-700 dark:text-gray-300 font-semibold mb-3">Specific Retention Periods:</p>
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-700 dark:text-gray-300"><strong>Account Data:</strong> Until account deletion + 30 days</p>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Transaction Records:</strong> 7 years (tax compliance)</p>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Marketing Emails:</strong> Until you unsubscribe + 30 days</p>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Cookies:</strong> As specified in our Cookie Policy</p>
                                <p class="text-gray-700 dark:text-gray-300"><strong>SMS Records:</strong> 2 years for compliance</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 9 -->
                <div id="rights" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-violet-100 dark:bg-violet-900 text-violet-600 dark:text-violet-300 rounded-full flex items-center justify-center font-bold mr-3">9</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">YOUR DATA PROTECTION RIGHTS</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">9.1 Rights Under Ghana Data Protection Act</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-3">You have the right to:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Access:</strong> Request a copy of your personal data</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Rectification:</strong> Correct inaccurate or incomplete data</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Erasure:</strong> Request deletion of your data</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Restriction:</strong> Limit how we use your data</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Portability:</strong> Receive your data in structured format</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Object:</strong> Object to processing</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Withdraw Consent:</strong> Withdraw consent anytime</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Lodge Complaints:</strong> File complaint with authorities</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-violet-50 dark:bg-violet-900/20 border-l-4 border-violet-400 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-violet-900 dark:text-violet-300 mb-2">9.2 How to Exercise Your Rights</h3>
                            <p class="text-sm text-violet-800 dark:text-violet-400 mb-2">To exercise these rights, contact us at:</p>
                            <div class="space-y-1 text-sm">
                                <p class="text-violet-800 dark:text-violet-400">📧 Email: 9yttrybe@gmail.com</p>
                                <p class="text-violet-800 dark:text-violet-400">📞 Phone: 0545566524 / 0267825223</p>
                            </div>
                            <p class="text-xs text-violet-700 dark:text-violet-500 mt-3">We will respond within 30 days as required by law.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 10 -->
                <div id="security" class="mb-10 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/10 dark:to-orange-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 rounded-full flex items-center justify-center font-bold mr-3">10</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">DATA SECURITY</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">10.1 Security Measures</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-3">We implement industry-standard security measures:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Encryption:</strong> SSL/TLS encryption for data in transit</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Access Controls:</strong> Role-based access to data</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Authentication:</strong> Secure password hashing and storage</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Monitoring:</strong> Regular security audits and monitoring</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Backups:</strong> Regular encrypted backups</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Staff Training:</strong> Data protection training for employees</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">10.2 Payment Security</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">We do NOT store credit card information. All payments are processed securely through PCI-compliant third-party processors (Paystack).</p>
                        </div>

                        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-900 dark:text-red-300 mb-2">10.3 Data Breach Notification</h3>
                            <p class="text-sm text-red-800 dark:text-red-400">In the event of a data breach, we will notify affected users and the Data Protection Commission within 72 hours as required by law.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 11 -->
                <div id="transfers" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-teal-100 dark:bg-teal-900 text-teal-600 dark:text-teal-300 rounded-full flex items-center justify-center font-bold mr-3">11</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">INTERNATIONAL DATA TRANSFERS</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">Your data may be transferred to and stored in countries outside Ghana. We ensure adequate protection through:</p>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Standard contractual clauses</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Privacy Shield frameworks (where applicable)</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Adequate data protection safeguards</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 12 -->
                <div id="children" class="mb-10 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/10 dark:to-yellow-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-300 rounded-full flex items-center justify-center font-bold mr-3">12</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">CHILDREN'S PRIVACY</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-3">Our Platform is not directed to children under 18. We do not knowingly collect data from children. If we discover we have collected data from a child, we will delete it immediately.</p>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">If you believe a child has provided us with personal information, please contact us.</p>
                    </div>
                </div>

                <!-- Section 13 -->
                <div id="cookies" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-sky-100 dark:bg-sky-900 text-sky-600 dark:text-sky-300 rounded-full flex items-center justify-center font-bold mr-3">13</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">COOKIES AND TRACKING TECHNOLOGIES</h2>
                    </div>
                    <div class="pl-11 space-y-4">
                        <p class="text-gray-700 dark:text-gray-300">We use cookies and similar technologies. Please see our <a href="{{ route('legal.cookies') }}" class="text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 underline">Cookie Policy</a> for details.</p>

                        <div class="bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">13.1 Types of Cookies</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Essential:</strong> Required for platform functionality</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Performance:</strong> Analytics and usage tracking</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Functional:</strong> Remember your preferences</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Marketing:</strong> Personalized advertising (with consent)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 14 -->
                <div id="links" class="mb-10 bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-800/50 dark:to-slate-800/50 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center font-bold mr-3">14</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">THIRD-PARTY LINKS</h2>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed pl-11">Our Platform may contain links to third-party websites. We are not responsible for their privacy practices. Please review their privacy policies.</p>
                </div>

                <!-- Section 15 -->
                <div id="marketing" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-lime-100 dark:bg-lime-900 text-lime-600 dark:text-lime-300 rounded-full flex items-center justify-center font-bold mr-3">15</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">MARKETING COMMUNICATIONS</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">15.1 Consent</h3>
                            <p class="text-gray-700 dark:text-gray-300">We will only send marketing communications with your explicit consent.</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">15.2 Opt-Out</h3>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">You can unsubscribe from marketing emails by:</p>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-lime-600 dark:text-lime-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Clicking "unsubscribe" in any marketing email</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-lime-600 dark:text-lime-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Updating your account preferences</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-lime-600 dark:text-lime-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300">Contacting us directly</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-lime-50 dark:bg-lime-900/20 border-l-4 border-lime-400 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-lime-900 dark:text-lime-300 mb-2">15.3 Transactional Emails</h3>
                            <p class="text-sm text-lime-800 dark:text-lime-400">You cannot opt out of transactional emails (order confirmations, password resets) as they are essential for service delivery.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 16 -->
                <div id="roles" class="mb-10 bg-gradient-to-r from-fuchsia-50 to-purple-50 dark:from-fuchsia-900/10 dark:to-purple-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-fuchsia-100 dark:bg-fuchsia-900 text-fuchsia-600 dark:text-fuchsia-300 rounded-full flex items-center justify-center font-bold mr-3">16</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">DATA CONTROLLER VS DATA PROCESSOR</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">We act as:</p>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-fuchsia-600 dark:text-fuchsia-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Data Controller:</strong> For our platform operations, user accounts, and transactions</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-fuchsia-600 dark:text-fuchsia-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300"><strong>Data Processor:</strong> For Event Organizers using our SMS services (they remain the Data Controller for their attendee data)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 17 -->
                <div id="automated" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-rose-100 dark:bg-rose-900 text-rose-600 dark:text-rose-300 rounded-full flex items-center justify-center font-bold mr-3">17</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">AUTOMATED DECISION-MAKING</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">We may use automated systems for:</p>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Fraud detection</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Event recommendations</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Spam filtering</p>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mt-4">You have the right to object to automated decision-making that produces legal effects.</p>
                    </div>
                </div>

                <!-- Section 18 -->
                <div id="changes" class="mb-10 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center font-bold mr-3">18</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">CHANGES TO THIS PRIVACY POLICY</h2>
                    </div>
                    <div class="pl-11">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">We may update this Privacy Policy from time to time. We will notify you of significant changes by:</p>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Email notification</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Platform notification</p>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300">Updating the "Last Updated" date</p>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mt-4">Continued use of the Platform after changes constitutes acceptance of the updated policy.</p>
                    </div>
                </div>

                <!-- Section 19 -->
                <div id="contact" class="mb-10 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-cyan-100 dark:bg-cyan-900 text-cyan-600 dark:text-cyan-300 rounded-full flex items-center justify-center font-bold mr-3">19</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">CONTACT AND COMPLAINTS</h2>
                    </div>

                    <div class="pl-11 space-y-6">
                        <div class="bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-cyan-900 dark:text-cyan-300 mb-4">19.1 Contact Us</h3>
                            <p class="text-sm text-cyan-800 dark:text-cyan-400 mb-3">For privacy-related questions or to exercise your rights:</p>
                            <div class="space-y-2">
                                <p class="text-sm text-cyan-900 dark:text-cyan-300"><strong>📧 Email:</strong> 9yttrybe@gmail.com</p>
                                <p class="text-sm text-cyan-900 dark:text-cyan-300"><strong>📞 Phone:</strong> 0545566524 / 0267825223</p>
                                <p class="text-sm text-cyan-900 dark:text-cyan-300"><strong>📍 Address:</strong> Ghana</p>
                            </div>
                        </div>

                        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-300 mb-4">19.2 Data Protection Commission (Ghana)</h3>
                            <p class="text-sm text-orange-800 dark:text-orange-400 mb-3">You have the right to lodge a complaint with:</p>
                            <div class="space-y-2">
                                <p class="text-sm text-orange-900 dark:text-orange-300"><strong>Data Protection Commission</strong></p>
                                <p class="text-sm text-orange-900 dark:text-orange-300">📧 Email: info@dataprotection.org.gh</p>
                                <p class="text-sm text-orange-900 dark:text-orange-300">📞 Phone: +233 (0)302 971 745</p>
                                <p class="text-sm text-orange-900 dark:text-orange-300">🌐 Website: www.dataprotection.org.gh</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 20 -->
                <div id="acknowledgment" class="mb-10 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-lg p-6 scroll-mt-20">
                    <div class="flex items-start mb-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-full flex items-center justify-center font-bold mr-3">20</span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">ACKNOWLEDGMENT</h2>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed pl-11">By using our Platform, you acknowledge that you have read and understood this Privacy Policy and consent to the collection, use, and disclosure of your information as described herein.</p>
                </div>

                <!-- Final Notice -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-blue-900 dark:text-blue-300 mb-1">Your Privacy Matters</p>
                            <p class="text-sm text-blue-800 dark:text-blue-200">We are committed to protecting your personal data and respecting your privacy rights. If you have any questions or concerns, please don't hesitate to contact us.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
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
