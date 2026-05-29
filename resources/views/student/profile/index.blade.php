@extends('layouts.student_mobile')

@section('title', 'My Profile')

@section('mobile-content')
<!-- Header -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%);">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Account Settings</p>
            <h1 class="text-2xl font-bold text-white">My Profile</h1>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Profile Card -->
<div class="px-4 -mt-6 mb-4">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex flex-col items-center">
            <!-- Avatar -->
            <div class="relative mb-4">
                <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-violet-100 bg-gray-100 flex items-center justify-center">
                    @if($student->avatar)
                        <img src="{{ Storage::url($student->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    @endif
                </div>
                <label for="avatar-input" class="absolute bottom-0 right-0 w-8 h-8 bg-violet-600 rounded-full flex items-center justify-center cursor-pointer shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </label>
            </div>

            <!-- Email Display -->
            <p class="text-sm text-gray-500">{{ $student->email }}</p>
        </div>
    </div>
</div>

<!-- Update Profile Form -->
<div class="px-4 mb-4">
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Profile
        </h2>

        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-violet-500 focus:ring-2 focus:ring-violet-200 outline-none transition-all"
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone', $student->phone) }}" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-violet-500 focus:ring-2 focus:ring-violet-200 outline-none transition-all"
                       placeholder="Enter phone number">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl font-bold text-white shadow-lg"
                    style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                Save Changes
            </button>
        </form>
    </div>
</div>

<!-- Change Password -->
<div class="px-4 mb-4">
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Change Password
        </h2>

        <form method="POST" action="{{ route('student.profile.password') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all"
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all"
                       required minlength="8">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all"
                       required>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl font-bold text-white shadow-lg"
                    style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                Update Password
            </button>
        </form>
    </div>
</div>

<!-- Delete Account -->
<div class="px-4 mb-20">
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-red-100">
        <h2 class="text-lg font-bold text-red-600 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete Account
        </h2>

        <p class="text-sm text-gray-600 mb-4">
            Warning: This action cannot be undone. All your data will be permanently deleted.
        </p>

        <button onclick="showDeleteModal()" class="w-full py-3 rounded-xl font-bold text-red-600 border-2 border-red-200 hover:bg-red-50 transition-all">
            Delete My Account
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Delete Account?</h3>
            <p class="text-sm text-gray-500 mt-2">This cannot be undone. Enter your password to confirm.</p>
        </div>

        <form method="POST" action="{{ route('student.profile.delete') }}" onsubmit="return confirm('Are you absolutely sure?')">
            @csrf

            <div class="mb-4">
                <input type="password" name="password" placeholder="Enter your password"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all"
                       required>
            </div>

            <div class="mb-4">
                <input type="text" name="confirm_delete" placeholder="Type DELETE to confirm"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all"
                       required>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="hideDeleteModal()" 
                        class="flex-1 py-3 rounded-xl font-bold text-gray-600 bg-gray-100">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-3 rounded-xl font-bold text-white bg-red-600">
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>
@endsection
