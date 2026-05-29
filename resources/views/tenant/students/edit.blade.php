@extends('layouts.tenant')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

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

        <form method="POST" action="{{ route('tenant.students.update', $student) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" 
                           class="form-input" placeholder="Student's full name" required>
                </div>

                <div>
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" 
                           class="form-input" placeholder="student@example.com" required>
                </div>

                <div>
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $student->phone) }}" 
                           class="form-input" placeholder="+91-9876543210">
                </div>

                <div>
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-input">
                        <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <label class="form-label">Change Password (optional)</label>
                    <p class="text-sm text-gray-500 mb-2">Leave blank to keep current password</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password" 
                                   class="form-input" placeholder="Min 8 characters">
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="form-input" placeholder="Confirm password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="btn-primary">
                    Update Student
                </button>
                <a href="{{ route('tenant.students.index') }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
