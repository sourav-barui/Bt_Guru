@extends('layouts.admin')

@section('title', 'Domains')
@section('page-title', 'Custom Domains')

@section('page-content')

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-gray-900">Custom Domain Mappings</h3>
            <p class="text-sm text-gray-500 mt-0.5">Map custom domains to tenant coaching centres.</p>
        </div>
        <a href="{{ route('admin.domains.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Domain
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Tenant</th>
                    <th>Subdomain</th>
                    <th>Custom Domain</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($domains as $tenant)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-700 font-bold text-xs">{{ strtoupper(substr($tenant->coaching_name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $tenant->coaching_name }}</p>
                                <p class="text-xs text-gray-400">{{ $tenant->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="http://{{ $tenant->subdomain }}.btguru.test" target="_blank" class="text-sm text-blue-600 hover:text-blue-700">
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $tenant->subdomain }}.btguru.test</code>
                        </a>
                    </td>
                    <td>
                        @if($tenant->custom_domain)
                        <a href="https://{{ $tenant->custom_domain }}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                            {{ $tenant->custom_domain }} ↗
                        </a>
                        @else
                        <span class="text-sm text-gray-400">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $tenant->status === 'active' ? 'badge-success' : ($tenant->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.domains.edit', $tenant) }}" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded" title="Edit Domain">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.domains.verify', $tenant) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded" title="Verify DNS">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.domains.destroy', $tenant) }}" class="inline" onsubmit="return confirm('Remove custom domain from {{ $tenant->coaching_name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded" title="Remove Domain">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                        No custom domains configured.
                        <a href="{{ route('admin.domains.create') }}" class="text-blue-600 hover:text-blue-700 ml-1">Add one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($domains->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $domains->links() }}
    </div>
    @endif
</div>

<!-- DNS Instructions -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-5">
    <h4 class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        DNS Setup Instructions
    </h4>
    <p class="text-sm text-blue-700">To point a custom domain to this platform, add a <strong>CNAME record</strong> pointing to your server's hostname, or an <strong>A record</strong> pointing to your server's IP address.</p>
</div>
@endsection
