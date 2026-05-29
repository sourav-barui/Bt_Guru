@extends('layouts.admin')

@section('title', 'Add Tenant')
@section('page-title', 'Add New Tenant')

@section('page-content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Tenants
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Tenant Details</h3>
            <p class="text-sm text-gray-500 mt-1">Create a new coaching centre on the platform.</p>
        </div>
        <form action="{{ route('admin.tenants.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coaching Name <span class="text-red-500">*</span></label>
                <input type="text" name="coaching_name" value="{{ old('coaching_name') }}" required class="form-input w-full" placeholder="e.g. Future Academy">
                @error('coaching_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subdomain <span class="text-red-500">*</span></label>
                <div class="flex items-center">
                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain') }}" required class="form-input rounded-r-none w-48" placeholder="futureacademy">
                    <span class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-500">.btguru.test</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Only lowercase letters, numbers and hyphens.</p>
                @error('subdomain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-input w-full" placeholder="admin@futureacademy.com">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-input w-full" placeholder="+91 98765 43210">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="form-input w-full" placeholder="Full address...">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="form-input w-full">
                <p class="text-xs text-gray-400 mt-1">Leave blank for no expiry.</p>
                @error('expires_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-input w-full">
                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Create Tenant
                </button>
                <a href="{{ route('admin.tenants.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('[name="coaching_name"]').addEventListener('input', function() {
    const sub = document.getElementById('subdomain');
    if (!sub.dataset.edited) {
        sub.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
});
document.getElementById('subdomain').addEventListener('input', function() {
    this.dataset.edited = '1';
});
</script>
@endpush
@endsection
