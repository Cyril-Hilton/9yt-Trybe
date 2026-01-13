@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-6">About 9yt !Trybe</h1>
            <div class="w-24 h-1 bg-gradient-to-r from-cyan-600 to-blue-600 mx-auto mb-8"></div>
        </div>

        <!-- Mission Statement -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 md:p-12 mb-12 border border-gray-200 dark:border-gray-700">
            <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 leading-relaxed mb-6">
                9yt !Trybe is not just a brand; it's a movement. We are a collective of nocturnal enthusiasts
                who believe in creating authentic, high-energy experiences. Our mission is to foster a vibrant,
                inclusive community where music, storytelling, and friendship unite under the midnight skies.
            </p>
            <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
                We are the ones who don't just attend events—we define them. Our name, <span class="font-bold text-cyan-600 dark:text-cyan-400">9yt !Trybe</span>, is a statement of being. We don't try to be; we are. We're the heart of the party, the soul of the night, and the creators of unforgettable memories.
            </p>
        </div>

        <!-- Leadership Team -->
        <div class="mb-12">
            <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-white mb-12">Meet the Leadership</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- CEO -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:scale-105 transition-transform duration-300">
                    <div class="aspect-square bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                        <div class="w-32 h-32 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Cyril Hilton Wemegah</h3>
                        <p class="text-cyan-600 dark:text-cyan-400 font-semibold">CEO & Creative Director</p>
                    </div>
                </div>

                <!-- Lead Designer -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:scale-105 transition-transform duration-300">
                    <div class="aspect-square bg-gradient-to-br from-cyan-500 to-cyan-500 flex items-center justify-center">
                        <div class="w-32 h-32 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Curtis Banor</h3>
                        <p class="text-cyan-600 dark:text-cyan-400 font-semibold">Lead Designer & Co Creative Director</p>
                    </div>
                </div>

                <!-- Admin -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:scale-105 transition-transform duration-300">
                    <div class="aspect-square bg-gradient-to-br from-pink-500 to-red-600 flex items-center justify-center">
                        <div class="w-32 h-32 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Daniella Obeng Yeboah Asamoah</h3>
                        <p class="text-pink-600 dark:text-pink-400 font-semibold">Admin</p>
                    </div>
                </div>

                <!-- COO -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:scale-105 transition-transform duration-300">
                    <div class="aspect-square bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                        <div class="w-32 h-32 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nana Kwame Wae Adu-Asare</h3>
                        <p class="text-blue-600 dark:text-blue-400 font-semibold">Chief Operations Officer</p>
                    </div>
                </div>

                <!-- Senior Software Engineer -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:scale-105 transition-transform duration-300">
                    <div class="aspect-square bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                        <div class="w-32 h-32 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Kenedy Dzigbenyo</h3>
                        <p class="text-green-600 dark:text-green-400 font-semibold">Senior Software Engineer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
