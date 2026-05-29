@extends('layouts.admin')

@section('title', 'Edit Domain')
@section('page-title', 'Edit Custom Domain')

@section('page-content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('admin.domains.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Domains
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-700 font-bold text-sm">{{ strtoupper(substr($tenant->coaching_name, 0, 2)) }}</span>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ $tenant->coaching_name }}</h3>
                <p class="text-xs text-gray-400">Updating custom domain</p>
            </div>
        </div>

        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.domains.update', $tenant) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenant</label>
                <input type="text" value="{{ $tenant->coaching_name }} ({{ $tenant->subdomain }})" disabled class="form-input w-full bg-gray-50 text-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Domain <span class="text-red-500">*</span></label>
                <input type="text" name="custom_domain" value="{{ old('custom_domain', $tenant->custom_domain) }}" required class="form-input w-full" placeholder="academy.example.com">
                <p class="text-xs text-gray-400 mt-1">Enter the domain without http:// or https://</p>
                @error('custom_domain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Update Domain
                </button>
                <form method="POST" action="{{ route('admin.domains.destroy', $tenant) }}" class="inline" onsubmit="return confirm('Remove custom domain from {{ $tenant->coaching_name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Remove Domain
                    </button>
                </form>
                <a href="{{ route('admin.domains.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
