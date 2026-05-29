@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-28">
                <a href="/" class="flex items-center">
                    <img src="/images/logo.png" alt="BT Guru" class="h-24 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-20 h-20 bg-blue-600 rounded-xl flex items-center justify-center hidden">
                        <span class="text-white font-bold text-2xl">BT</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="#features" class="text-gray-600 hover:text-gray-900">Features</a>
                    <a href="#pricing" class="text-gray-600 hover:text-gray-900">Pricing</a>
                    <a href="#contact" class="text-gray-600 hover:text-gray-900">Contact</a>
                    <a href="http://admin.{{ config('app.central_domain') }}/login" class="text-blue-600 hover:text-blue-700 font-medium">Admin Login</a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button type="button" id="mobile-menu-toggle" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100" aria-label="Toggle menu">
                    <svg id="menu-icon" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="close-icon" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-100">
                <div class="py-4 space-y-3">
                    <a href="#features" class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">Features</a>
                    <a href="#pricing" class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">Pricing</a>
                    <a href="#contact" class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">Contact</a>
                    <a href="http://admin.{{ config('app.central_domain') }}/login" class="block px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg font-medium">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');

            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    </script>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                        Manage Your<br>
                        <span class="text-blue-600">Coaching Centre</span><br>
                        with Ease
                    </h1>
                    <p class="mt-6 text-lg text-gray-600 max-w-lg">
                        BT Guru is a complete SaaS platform for coaching centres. Manage students, teachers, courses, fees, and more - all in one place.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('tenant.register') }}" class="btn-primary px-8 py-3 text-lg">
                            Get Started Free
                        </a>
                        <a href="#features" class="btn-secondary px-8 py-3 text-lg">
                            Learn More
                        </a>
                    </div>
                    <div class="mt-8 flex items-center gap-6 text-sm text-gray-500">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Free 14-day trial
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            No credit card required
                        </span>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8">
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold">FA</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Future Academy</h3>
                                    <p class="text-sm text-gray-500">Dashboard</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-lg font-bold text-blue-600">248</p>
                                    <p class="text-xs text-gray-500">Students</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-lg font-bold text-green-600">12</p>
                                    <p class="text-xs text-gray-500">Courses</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-lg font-bold text-purple-600">8</p>
                                    <p class="text-xs text-gray-500">Teachers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Everything You Need</h2>
                <p class="mt-4 text-lg text-gray-600">Complete solution for managing your coaching centre</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Student Management</h3>
                    <p class="text-gray-600 text-sm">Manage student admissions, enrollments, and track their progress effortlessly.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Teacher Management</h3>
                    <p class="text-gray-600 text-sm">Assign teachers to courses, track their schedule, and manage their workload.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Course Management</h3>
                    <p class="text-gray-600 text-sm">Create courses, set fees, duration, and manage all course-related activities.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Fee Management</h3>
                    <p class="text-gray-600 text-sm">Track fee payments, send reminders, and manage financial records.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Notices & Communication</h3>
                    <p class="text-gray-600 text-sm">Post notices, send announcements to students and teachers instantly.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Reports & Analytics</h3>
                    <p class="text-gray-600 text-sm">Generate reports on admissions, fees, attendance, and performance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Transform Your Coaching Centre?</h2>
            <p class="text-blue-100 text-lg mb-8">Join thousands of coaching centres already using BT Guru</p>
            <a href="{{ route('tenant.register') }}" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-blue-50 transition-colors">
                Start Your Free Trial
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" alt="BT Guru" class="h-12 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hidden">
                        <span class="text-white font-bold text-sm">BT</span>
                    </div>
                    <span class="text-xl font-bold text-white">BT Guru</span>
                </div>
                <p class="text-sm">&copy; {{ date('Y') }} BT Guru. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
@endsection
