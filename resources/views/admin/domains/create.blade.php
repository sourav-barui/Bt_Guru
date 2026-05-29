@extends('layouts.admin')

@section('title', 'Add Custom Domain')
@section('page-title', 'Add Custom Domain')

@section('page-content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('admin.domains.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Domains
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Map Custom Domain</h3>
            <p class="text-sm text-gray-500 mt-1">Assign a custom domain to an existing tenant.</p>
        </div>

        <form action="{{ route('admin.domains.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenant <span class="text-red-500">*</span></label>
                <select name="tenant_id" required class="form-input w-full">
                    <option value="">— Select Tenant —</option>
                    @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->coaching_name }} ({{ $tenant->subdomain }})
                    </option>
                    @endforeach
                </select>
                @error('tenant_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Domain <span class="text-red-500">*</span></label>
                <input type="text" name="custom_domain" value="{{ old('custom_domain') }}" required class="form-input w-full" placeholder="academy.example.com">
                <p class="text-xs text-gray-400 mt-1">Enter the domain without http:// or https://</p>
                @error('custom_domain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-700">
                <p class="font-medium mb-1">Before adding the domain:</p>
                <ul class="list-disc ml-4 space-y-1 text-xs">
                    <li>Create a CNAME or A record in your DNS provider</li>
                    <li>Point it to your server's hostname or IP</li>
                    <li>DNS propagation may take up to 48 hours</li>
                </ul>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Add Domain
                </button>
                <a href="{{ route('admin.domains.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
