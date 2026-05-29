@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('page-content')

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
@endif

<!-- Top Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.tenants.index') }}" class="stat-card hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Tenants</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>
        <div class="mt-3 flex gap-3 text-xs">
            <span class="inline-flex items-center gap-1 text-green-600"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>{{ $stats['active_tenants'] }} Active</span>
            <span class="inline-flex items-center gap-1 text-yellow-600"><span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span>{{ $stats['pending_tenants'] }} Pending</span>
            <span class="inline-flex items-center gap-1 text-red-500"><span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>{{ $stats['suspended_tenants'] }} Suspended</span>
        </div>
    </a>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Students</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>
        <div class="mt-3 text-xs text-gray-400">{{ $stats['total_teachers'] }} teachers · {{ $stats['total_enrollments'] }} enrollments</div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Courses</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_courses'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>
        <div class="mt-3 text-xs text-gray-400">{{ $stats['new_tenants_month'] }} new tenants this month</div>
    </div>

    <a href="{{ route('admin.domains.index') }}" class="stat-card hover:shadow-md transition-shadow group {{ $stats['expiring_soon'] > 0 ? 'border-orange-300 bg-orange-50' : '' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Custom Domains</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $stats['active_domains'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            </div>
        </div>
        @if($stats['expiring_soon'] > 0)
        <div class="mt-3 text-xs text-orange-600 font-medium">⚠ {{ $stats['expiring_soon'] }} expiring within 30 days</div>
        @else
        <div class="mt-3 text-xs text-gray-400">Active custom domains</div>
        @endif
    </a>
</div>

<!-- Main Content Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Recent Tenants Table -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Recent Tenants</h3>
            <a href="{{ route('admin.tenants.create') }}" class="btn-primary text-sm">+ Add Tenant</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Coaching Centre</th>
                        <th class="px-4 py-3 text-left">Subdomain</th>
                        <th class="px-4 py-3 text-center">Users</th>
                        <th class="px-4 py-3 text-center">Courses</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTenants as $tenant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
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
                        <td class="px-4 py-3">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tenant->subdomain }}</code>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $tenant->users_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $tenant->courses_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge {{ $tenant->status === 'active' ? 'badge-success' : ($tenant->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">{{ ucfirst($tenant->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @if($tenant->status === 'active')
                                <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-yellow-500 hover:text-yellow-700 hover:bg-yellow-50 rounded" title="Suspend" onclick="return confirm('Suspend {{ $tenant->coaching_name }}?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded" title="Activate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No tenants yet. <a href="{{ route('admin.tenants.create') }}" class="text-blue-600">Create one</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-100 text-center">
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all tenants →</a>
        </div>
    </div>

    <!-- Sidebar: Quick Actions + Expiring -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('admin.tenants.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 transition-colors group">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div><p class="text-sm font-medium text-gray-900">Add New Tenant</p><p class="text-xs text-gray-400">Create coaching centre</p></div>
                </a>
                <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </div>
                    <div><p class="text-sm font-medium text-gray-900">All Tenants</p><p class="text-xs text-gray-400">View & manage all tenants</p></div>
                </a>
                <a href="{{ route('admin.domains.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-orange-50 transition-colors group">
                    <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <div><p class="text-sm font-medium text-gray-900">Manage Domains</p><p class="text-xs text-gray-400">Custom domain mappings</p></div>
                </a>
                <a href="{{ route('admin.domains.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-orange-50 transition-colors group">
                    <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div><p class="text-sm font-medium text-gray-900">Add Custom Domain</p><p class="text-xs text-gray-400">Map domain to tenant</p></div>
                </a>
            </div>
        </div>

        <!-- Expiring Soon -->
        @if($expiringTenants->count() > 0)
        <div class="bg-orange-50 rounded-xl border border-orange-200">
            <div class="px-5 py-4 border-b border-orange-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-sm font-semibold text-orange-800">Expiring Soon</h3>
            </div>
            <div class="p-3 space-y-2">
                @foreach($expiringTenants as $t)
                <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-orange-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $t->coaching_name }}</p>
                        <p class="text-xs text-orange-600">Expires {{ $t->expires_at->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('admin.tenants.edit', $t) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Renew</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Platform Overview -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-5 text-white">
            <h3 class="text-sm font-semibold mb-3 opacity-90">Platform Overview</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold">{{ $stats['total_courses'] }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">Courses</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold">{{ $stats['total_enrollments'] }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">Enrollments</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold">{{ $stats['total_teachers'] }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">Teachers</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold">{{ $stats['new_tenants_month'] }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">New This Month</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Growth Bar Chart -->
@if($monthlyGrowth->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Tenant Growth (Last 6 Months)</h3>
    @php
        $maxCount = $monthlyGrowth->max('count') ?: 1;
        $months = ['', 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    @endphp
    <div class="flex items-end gap-3 h-32">
        @foreach($monthlyGrowth as $row)
        <div class="flex-1 flex flex-col items-center gap-1">
            <span class="text-xs font-semibold text-gray-700">{{ $row->count }}</span>
            <div class="w-full bg-blue-500 rounded-t" style="height: {{ max(4, ($row->count / $maxCount) * 96) }}px"></div>
            <span class="text-xs text-gray-400">{{ $months[$row->month] }}'{{ substr($row->year, 2) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
