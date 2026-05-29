@extends('layouts.admin')

@section('title', 'Edit ' . $tenant->coaching_name)
@section('page-title', 'Edit Tenant')

@section('page-content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Tenant
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-700 font-bold">{{ strtoupper(substr($tenant->coaching_name, 0, 2)) }}</span>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ $tenant->coaching_name }}</h3>
                <p class="text-xs text-gray-400">{{ $tenant->subdomain }}.btguru.test</p>
            </div>
        </div>

        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coaching Name <span class="text-red-500">*</span></label>
                <input type="text" name="coaching_name" value="{{ old('coaching_name', $tenant->coaching_name) }}" required class="form-input w-full">
                @error('coaching_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subdomain</label>
                <div class="flex items-center">
                    <input type="text" value="{{ $tenant->subdomain }}" disabled class="form-input rounded-r-none w-48 bg-gray-50 text-gray-500">
                    <span class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-500">.btguru.test</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Subdomain cannot be changed after creation.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="form-input w-full">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="form-input w-full">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="form-input w-full">{{ old('address', $tenant->address) }}</textarea>
                @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expires_at" value="{{ old('expires_at', $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '') }}" class="form-input w-full">
                <p class="text-xs text-gray-400 mt-1">Leave blank for no expiry.</p>
                @error('expires_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-input w-full">
                    <option value="pending" {{ old('status', $tenant->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Changes
                </button>
                <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
