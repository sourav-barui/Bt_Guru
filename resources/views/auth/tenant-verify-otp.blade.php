@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            @if(isset($currentTenant) && $currentTenant->logo)
                <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" class="w-16 h-16 rounded object-cover mx-auto mb-4">
            @else
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <span class="text-white font-bold text-2xl">
                        {{ isset($currentTenant) ? substr($currentTenant->coaching_name, 0, 2) : 'BT' }}
                    </span>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($currentTenant) ? $currentTenant->coaching_name : 'BT Guru' }}
            </h1>
            <p class="text-gray-600 mt-1">Admin & Teacher Portal</p>
        </div>

        <!-- OTP Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Verify OTP</h2>
                <p class="text-gray-500 text-sm mt-1">Enter the 6-digit code sent to your email</p>
            </div>

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

            <form method="POST" action="{{ route('tenant.password.otp.check') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" id="otp" name="otp"
                           class="form-input @error('otp') border-red-500 @enderror text-center text-2xl tracking-widest"
                           placeholder="000000" maxlength="6" required autofocus
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    @error('otp')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full py-3">
                    Verify OTP
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500 mb-2">Didn't receive the code?</p>
                <a href="{{ route('tenant.password.request') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                    Request new OTP
                </a>
            </div>

            <div class="mt-4 text-center text-sm">
                <a href="{{ route('tenant.login') }}" class="text-gray-500 hover:text-gray-700">
                    ← Back to Login
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-8">
            Powered by <span class="font-semibold">BT Guru</span>
        </p>
    </div>
</div>
@endsection
