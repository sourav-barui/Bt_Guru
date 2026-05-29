@extends('layouts.tenant')

@section('title', 'Create Student')
@section('page-title', 'Add New Student')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.students.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="form-input" placeholder="Student's full name" required>
                </div>

                <div>
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="form-input" placeholder="student@example.com" required>
                </div>

                <div>
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                           class="form-input" placeholder="+91-9876543210">
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

                @php
                    $courses = \App\Models\Course::where('tenant_id', Auth::user()->tenant_id)
                        ->where('status', 'active')
                        ->get();
                @endphp

                @if($courses->count() > 0)
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <label for="course_id" class="form-label">Initial Course Enrollment (Optional)</label>
                        <select id="course_id" name="course_id" class="form-input">
                            <option value="">-- Select a course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} (₹{{ number_format($course->fees) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="btn-primary">
                    Create Student
                </button>
                <a href="{{ route('tenant.students.index') }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
