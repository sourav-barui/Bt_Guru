@extends('layouts.app')

@section('title', 'Student Registration')

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
            <p class="text-gray-600 mt-1">Student Registration</p>
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

            <form method="POST" action="{{ route('student.register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="form-input" placeholder="Your full name" required autofocus>
                </div>

                <div>
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="form-input" placeholder="your@email.com" required>
                </div>

                <div>
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                           class="form-input" placeholder="+91-9876543210" required>
                </div>

                @php
                    $courses = \App\Models\Course::where('tenant_id', $currentTenant?->id)
                        ->where('status', 'active')
                        ->get();
                @endphp
                
                @if($courses->count() > 0)
                    <div>
                        <label for="course_id" class="form-label">Select Course (Optional)</label>
                        <select id="course_id" name="course_id" class="form-input">
                            <option value="">-- Choose a course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} (₹{{ number_format($course->fees) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password" 
                           class="form-input" placeholder="Min 8 characters" required>
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="form-input" placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn-success w-full py-3">
                    Register
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <span class="text-gray-500">Already registered?</span>
                <a href="{{ route('student.login') }}" class="text-green-600 hover:text-green-700 font-medium ml-1">Login here</a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-8">
            Powered by <span class="font-semibold">BT Guru</span>
        </p>
    </div>
</div>
@endsection
