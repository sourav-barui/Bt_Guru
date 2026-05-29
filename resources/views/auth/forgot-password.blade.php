@extends('layouts.app')

@section('title', 'Forgot Password')

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
                    </svg>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">Forgot Password</h1>
            <p class="text-gray-600 mt-1">Enter your email to receive OTP</p>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.otp.send') }}" class="space-y-6">
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

                <button type="submit" class="btn-success w-full py-3">
                    Send OTP
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="{{ route('student.login') }}" class="text-green-600 hover:text-green-700 font-medium">
                    ← Back to Login
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            Powered by <span class="font-semibold">BT Guru</span>
        </p>
    </div>
</div>
@endsection
