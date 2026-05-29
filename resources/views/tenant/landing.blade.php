@extends('layouts.app')

@section('title', $currentTenant->coaching_name ?? 'Welcome')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    @if($currentTenant->logo)
                        <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" class="w-10 h-10 rounded object-cover">
                    @else
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($currentTenant->coaching_name, 0, 2) }}</span>
                        </div>
                    @endif
                    <span class="text-xl font-semibold text-gray-900">{{ $currentTenant->coaching_name }}</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('student.login') }}" class="text-gray-600 hover:text-gray-900">Student Login</a>
                    <a href="{{ route('tenant.login') }}" class="btn-primary">Admin/Teacher Login</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6">
                Welcome to {{ $currentTenant->coaching_name }}
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-8">
                {{ $currentTenant->address }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('student.register') }}" class="btn-success px-8 py-3 text-lg">
                    Register as Student
                </a>
                <a href="{{ route('tenant.login') }}" class="btn-primary px-8 py-3 text-lg">
                    Teacher Portal
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Quality Education</h3>
                    <p class="text-gray-600">Expert teachers and comprehensive courses</p>
                </div>

                <div>
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Track Progress</h3>
                    <p class="text-gray-600">Monitor your learning journey</p>
                </div>

                <div>
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Expert Teachers</h3>
                    <p class="text-gray-600">Learn from experienced educators</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Contact Us</h2>
            <p class="text-gray-600 mb-2">{{ $currentTenant->email }}</p>
            <p class="text-gray-600">{{ $currentTenant->phone }}</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} {{ $currentTenant->coaching_name }}. All rights reserved.</p>
            <p class="mt-2 text-sm flex items-center justify-center gap-2">
                Powered by
                <img src="/images/logo.png" alt="BT Guru" class="h-6 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span class="font-semibold hidden">BT Guru</span>
            </p>
        </div>
    </footer>
</div>
@endsection
