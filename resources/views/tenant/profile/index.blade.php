@extends('layouts.tenant')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('page-content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Profile Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Profile Information</h3>
            <p class="text-sm text-gray-500 mt-1">Update your account's profile information and email address.</p>
        </div>

        <form method="POST" action="{{ route('tenant.profile.update') }}" class="p-6 space-y-4">
            @csrf

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $admin->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', $admin->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       value="{{ old('phone', $admin->phone) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Change Password</h3>
            <p class="text-sm text-gray-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form method="POST" action="{{ route('tenant.profile.password') }}" class="p-6 space-y-4">
            @csrf

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                       required>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                       required>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Change Password
                </button>
            </div>
        </form>
    </div>

    <!-- Account Info -->
    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
        <h4 class="font-medium text-gray-900 mb-4">Account Information</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Member Since:</span>
                <span class="ml-2 text-gray-700">{{ $admin->created_at->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Role:</span>
                <span class="ml-2 text-gray-700">Administrator</span>
            </div>
            <div>
                <span class="text-gray-500">Last Updated:</span>
                <span class="ml-2 text-gray-700">{{ $admin->updated_at->diffForHumans() }}</span>
            </div>
            @if($admin->email_verified_at)
                <div>
                    <span class="text-gray-500">Email Status:</span>
                    <span class="ml-2 text-green-600 flex items-center gap-1 inline-flex">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Verified
                    </span>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
