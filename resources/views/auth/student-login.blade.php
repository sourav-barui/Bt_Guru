@extends('layouts.app')

@section('title', 'Student Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-emerald-100 p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            @if(isset($currentTenant) && $currentTenant->logo)
                <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" class="w-16 h-16 rounded object-cover mx-auto mb-4">
            @else
                <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($currentTenant) ? $currentTenant->coaching_name : 'BT Guru' }}
            </h1>
            <p class="text-gray-600 mt-1">Student Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="form-input @error('email') border-red-500 @enderror" 
                           placeholder="your@email.com" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" 
                           class="form-input @error('password') border-red-500 @enderror" 
                           placeholder="••••••••" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-green-600 rounded border-gray-300">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                    <a href="{{ route('student.password.request') }}" class="text-sm text-green-600 hover:text-green-700">Forgot password?</a>
                </div>

                <button type="submit" class="btn-success w-full py-3">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <span class="text-gray-500">New student?</span>
                <a href="{{ route('student.register') }}" class="text-green-600 hover:text-green-700 font-medium ml-1">Register here</a>
            </div>
        </div>

        <!-- Mobile App Download -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500 mb-3">Get the mobile app</p>
            <div class="flex justify-center gap-3">
                <!-- App Store Badge -->
                <div class="bg-gray-800 text-white px-4 py-3 rounded-xl flex items-center gap-3 opacity-60">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.21-1.98 1.07-3.11-1.05.05-2.31.7-3.06 1.55-.68.78-1.28 2.02-1.12 3.13 1.19.09 2.41-.61 3.11-1.57z"/>
                    </svg>
                    <div class="text-left">
                        <p class="text-[10px] leading-none text-gray-400">Download on the</p>
                        <p class="text-sm font-semibold leading-tight">App Store</p>
                    </div>
                </div>
                <!-- Play Store Badge -->
                <div class="bg-gray-800 text-white px-4 py-3 rounded-xl flex items-center gap-3 opacity-60">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 20.5v-17c0-.83.67-1.5 1.5-1.5.33 0 .64.1.9.29l14.15 8.5c.58.35.77 1.1.42 1.68-.1.17-.25.31-.42.42l-14.15 8.5c-.48.29-1.09.13-1.38-.35-.13-.21-.2-.46-.2-.71l.18-.03zm1.5-15.25v13.5l11.03-6.75L4.5 5.25z"/>
                    </svg>
                    <div class="text-left">
                        <p class="text-[10px] leading-none text-gray-400">Get it on</p>
                        <p class="text-sm font-semibold leading-tight">Google Play</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-8 flex items-center justify-center gap-3">
            Powered by
            <img src="/images/logo.png" alt="BT Guru" class="h-10 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <span class="font-semibold hidden">BT Guru</span>
        </p>
    </div>
</div>
@endsection
