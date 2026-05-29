@extends('layouts.app')

@section('title', 'Register Your Coaching Centre')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="w-full max-w-2xl">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white font-bold text-2xl">BT</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Register Your Coaching Centre</h1>
            <p class="text-gray-600 mt-1">Start your journey with BT Guru</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.register') }}" class="space-y-6">
                @csrf

                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Coaching Centre Details</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="coaching_name" class="form-label">Coaching Centre Name *</label>
                            <input type="text" id="coaching_name" name="coaching_name" value="{{ old('coaching_name') }}" 
                                   class="form-input" placeholder="e.g., Future Academy" required>
                        </div>

                        <div>
                            <label for="subdomain" class="form-label">Subdomain *</label>
                            <div class="flex items-center">
                                <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" 
                                       class="form-input rounded-r-none" placeholder="yourname" required>
                                <span class="px-3 py-2.5 bg-gray-100 border border-l-0 border-gray-200 rounded-r-lg text-gray-500 text-sm">
                                    .btguru.in
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">This will be your unique URL</p>
                        </div>

                        <div>
                            <label for="email" class="form-label">Centre Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                   class="form-input" placeholder="info@example.com" required>
                        </div>

                        <div>
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                                   class="form-input" placeholder="+91-9876543210" required>
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" rows="2" 
                                      class="form-input" placeholder="Your coaching centre address">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Account Details</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="admin_name" class="form-label">Admin Name *</label>
                            <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" 
                                   class="form-input" placeholder="Full name" required>
                        </div>

                        <div>
                            <label for="admin_email" class="form-label">Admin Email *</label>
                            <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" 
                                   class="form-input" placeholder="admin@example.com" required>
                        </div>

                        <div>
                            <label for="admin_phone" class="form-label">Admin Phone *</label>
                            <input type="tel" id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}" 
                                   class="form-input" placeholder="+91-9876543210" required>
                        </div>

                        <div>
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" 
                                   class="form-input" placeholder="Min 8 characters" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="form-input" placeholder="Confirm password" required>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-blue-600 rounded border-gray-300" required>
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="#" class="text-blue-600">Terms of Service</a> and <a href="#" class="text-blue-600">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full py-3">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <span class="text-gray-500">Already have an account?</span>
                <a href="{{ route('tenant.login') }}" class="text-blue-600 hover:text-blue-700 font-medium ml-1">Login here</a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-8">
            &copy; {{ date('Y') }} BT Guru. All rights reserved.
        </p>
    </div>
</div>
@endsection
