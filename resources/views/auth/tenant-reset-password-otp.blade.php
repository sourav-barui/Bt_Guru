@extends('layouts.app')

@section('title', 'Reset Password')

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

        <!-- Reset Password Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Reset Password</h2>
                <p class="text-gray-500 text-sm mt-1">Create a new password for your account</p>
            </div>

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.password.update.otp') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password"
                           class="form-input @error('password') border-red-500 @enderror"
                           placeholder="••••••••" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-input"
                           placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-primary w-full py-3">
                    Reset Password
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="{{ route('tenant.login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
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
