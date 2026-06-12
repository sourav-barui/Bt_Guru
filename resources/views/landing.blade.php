@extends('layouts.app')

@section('title', 'BT Guru - Transform Your Coaching Centre Management')

@section('content')
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    slate: {
                        950: '#020617',
                        900: '#0f172a',
                        800: '#1e293b',
                        700: '#334155',
                    }
                }
            }
        }
    }
</script>

<!-- Modern Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/25 group-hover:shadow-orange-500/40 transition-all">
                    <span class="text-white font-bold text-lg">BT</span>
                </div>
                <span class="text-gray-900 font-bold text-xl tracking-tight">Guru</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="#features" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">Features</a>
                <a href="#solutions" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">Solutions</a>
                <a href="#pricing" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">Pricing</a>
                <a href="#about" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">About</a>
            </div>

            <!-- CTA Buttons -->
            <div class="hidden lg:flex items-center gap-4">
                <a href="http://admin.{{ config('app.central_domain') }}/login" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">Sign In</a>
                <a href="{{ route('tenant.register') }}" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40">
                    Start Free Trial
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button id="mobile-menu-toggle" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="lg:hidden hidden border-t border-gray-200 py-4">
            <div class="flex flex-col gap-3">
                <a href="#features" class="text-gray-600 hover:text-gray-900 py-2 text-sm font-medium">Features</a>
                <a href="#solutions" class="text-gray-600 hover:text-gray-900 py-2 text-sm font-medium">Solutions</a>
                <a href="#pricing" class="text-gray-600 hover:text-gray-900 py-2 text-sm font-medium">Pricing</a>
                <a href="#about" class="text-gray-600 hover:text-gray-900 py-2 text-sm font-medium">About</a>
                <hr class="border-gray-200 my-2">
                <a href="http://admin.{{ config('app.central_domain') }}/login" class="text-gray-600 hover:text-gray-900 py-2 text-sm font-medium">Sign In</a>
                <a href="{{ route('tenant.register') }}" class="bg-gradient-to-r from-orange-500 to-red-600 text-white px-4 py-3 rounded-lg text-sm font-semibold text-center">
                    Start Free Trial
                </a>
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
<section class="relative min-h-screen pt-32 lg:pt-40 pb-20 overflow-hidden bg-slate-950">
    <!-- Background gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800"></div>
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-orange-600/20 via-transparent to-transparent"></div>
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-red-600/10 via-transparent to-transparent"></div>
    
    <!-- Animated grid pattern -->
    <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-2 mb-6">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="text-orange-400 text-sm font-medium">Trusted by 500+ Coaching Centres</span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                    Transform Your
                    <span class="bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">Coaching Centre</span>
                    Management
                </h1>
                
                <p class="mt-6 text-lg text-slate-400 max-w-xl mx-auto lg:mx-0">
                    The complete SaaS platform for coaching centres. Manage students, teachers, courses, fees, and live classes—all in one powerful dashboard.
                </p>
                
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('tenant.register') }}" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all shadow-xl shadow-orange-500/30 hover:shadow-orange-500/50 inline-flex items-center justify-center gap-2">
                        Get Started Free
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="#demo" class="bg-slate-800 hover:bg-slate-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all border border-slate-700 inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        Watch Demo
                    </a>
                </div>
                
                <div class="mt-10 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm text-slate-500">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        14-day free trial
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        No credit card required
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        24/7 Support
                    </span>
                </div>
            </div>
            
            <!-- Dashboard Preview -->
            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl opacity-30 blur-2xl"></div>
                <div class="relative bg-slate-900 rounded-2xl p-6 border border-slate-800 shadow-2xl">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="flex-1 text-center">
                            <span class="text-slate-500 text-xs">Future Academy Dashboard</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
                                <p class="text-2xl font-bold text-orange-400">1,248</p>
                                <p class="text-xs text-slate-400 mt-1">Students</p>
                                <div class="mt-2 flex items-center gap-1 text-emerald-400 text-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    <span>+12% this month</span>
                                </div>
                            </div>
                            <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
                                <p class="text-2xl font-bold text-blue-400">42</p>
                                <p class="text-xs text-slate-400 mt-1">Courses</p>
                                <div class="mt-2 flex items-center gap-1 text-emerald-400 text-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    <span>+5 new</span>
                                </div>
                            </div>
                            <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
                                <p class="text-2xl font-bold text-purple-400">18</p>
                                <p class="text-xs text-slate-400 mt-1">Teachers</p>
                                <div class="mt-2 flex items-center gap-1 text-emerald-400 text-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    <span>2 online</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-slate-300 font-medium">Recent Activity</span>
                                <span class="text-xs text-orange-400">View All</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center">
                                        <span class="text-orange-400 text-xs">+3</span>
                                    </div>
                                    <span class="text-slate-300">New student registrations</span>
                                    <span class="text-slate-500 text-xs ml-auto">2m ago</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <span class="text-blue-400 text-xs">$</span>
                                    </div>
                                    <span class="text-slate-300">Fee payment received</span>
                                    <span class="text-slate-500 text-xs ml-auto">15m ago</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                                        <span class="text-purple-400 text-xs">L</span>
                                    </div>
                                    <span class="text-slate-300">Live class started</span>
                                    <span class="text-slate-500 text-xs ml-auto">1h ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="relative bg-slate-900 border-y border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-4xl lg:text-5xl font-bold text-white">500+</p>
                <p class="mt-2 text-slate-400 text-sm">Active Coaching Centres</p>
            </div>
            <div>
                <p class="text-4xl lg:text-5xl font-bold text-orange-400">50K+</p>
                <p class="mt-2 text-slate-400 text-sm">Students Managed</p>
            </div>
            <div>
                <p class="text-4xl lg:text-5xl font-bold text-blue-400">1M+</p>
                <p class="mt-2 text-slate-400 text-sm">Classes Conducted</p>
            </div>
            <div>
                <p class="text-4xl lg:text-5xl font-bold text-purple-400">99.9%</p>
                <p class="mt-2 text-slate-400 text-sm">Uptime SLA</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="relative py-24 lg:py-32 bg-slate-950">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900 to-slate-950"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <span class="inline-block text-orange-400 text-sm font-semibold tracking-wider uppercase mb-4">Features</span>
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Everything you need to succeed</h2>
            <p class="text-lg text-slate-400">Powerful tools designed specifically for coaching centre management—from admissions to analytics.</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Feature Cards -->
            <div class="group bg-slate-900/50 rounded-2xl p-8 border border-slate-800 hover:border-orange-500/50 transition-all hover:bg-slate-900">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Student Management</h3>
                <p class="text-slate-400 leading-relaxed">Complete student lifecycle from admissions to certificates.</p>
            </div>

            <div class="group bg-slate-900/50 rounded-2xl p-8 border border-slate-800 hover:border-blue-500/50 transition-all hover:bg-slate-900">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Live Classes</h3>
                <p class="text-slate-400 leading-relaxed">Built-in video conferencing with whiteboard and recording.</p>
            </div>

            <div class="group bg-slate-900/50 rounded-2xl p-8 border border-slate-800 hover:border-purple-500/50 transition-all hover:bg-slate-900">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Fee Management</h3>
                <p class="text-slate-400 leading-relaxed">Automated fee tracking, payments, and financial reports.</p>
            </div>

            <div class="group bg-slate-900/50 rounded-2xl p-8 border border-slate-800 hover:border-emerald-500/50 transition-all hover:bg-slate-900">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Exams & Assessment</h3>
                <p class="text-slate-400 leading-relaxed">Online exams with auto-grading and detailed analytics.</p>
            </div>

            <div class="group bg-slate-900/50 rounded-2xl p-8 border border-slate-800 hover:border-yellow-500/50 transition-all hover:bg-slate-900">
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500/20 to-orange-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Communication</h3>
                <p class="text-slate-400 leading-relaxed">Messaging, announcements, and SMS notifications.</p>
            </div>

            <div class="group bg-slate-900/50 rounded-2xl p-8 border border-slate-800 hover:border-cyan-500/50 transition-all hover:bg-slate-900">
                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Analytics</h3>
                <p class="text-slate-400 leading-relaxed">Comprehensive dashboards and performance insights.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="relative py-24 bg-slate-950 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800"></div>
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-orange-600/10 via-transparent to-transparent"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Ready to transform your coaching centre?</h2>
        <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto">Join 500+ coaching centres already using BT Guru to streamline operations and grow their business.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('tenant.register') }}" class="bg-white text-slate-900 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all shadow-xl inline-flex items-center justify-center gap-2">
                Start Free Trial
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="#demo" class="bg-slate-800 text-white border border-slate-700 px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-700 transition-all inline-flex items-center justify-center">
                Schedule Demo
            </a>
        </div>
        <p class="mt-6 text-slate-400 text-sm">No credit card required • 14-day free trial • Cancel anytime</p>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white border-t border-gray-200 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold">BT</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Guru</span>
            </div>
            <div class="flex items-center gap-8 text-sm text-gray-600">
                <a href="#features" class="hover:text-gray-900 transition-colors">Features</a>
                <a href="#pricing" class="hover:text-gray-900 transition-colors">Pricing</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Privacy</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Terms</a>
            </div>
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} BT Guru. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection
