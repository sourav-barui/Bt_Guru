@extends('layouts.admin')

@section('title', $tenant->coaching_name)
@section('page-title', 'Tenant Details')

@section('page-content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Tenants
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="text-blue-700 font-bold text-lg">{{ strtoupper(substr($tenant->coaching_name, 0, 2)) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->coaching_name }}</h1>
                    <p class="text-sm text-gray-500">{{ $tenant->subdomain }}.btguru.test</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($tenant->status === 'active')
                <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Suspend {{ $tenant->coaching_name }}?')" class="btn-warning inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Suspend
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-success inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Activate
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="inline" onsubmit="return confirm('Permanently delete {{ $tenant->coaching_name }}? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ $tenant->users->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Courses</p>
            <p class="text-2xl font-bold text-gray-900">{{ $tenant->courses->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Enrollments</p>
            <p class="text-2xl font-bold text-gray-900">{{ $tenant->enrollments->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Status</p>
            <span class="badge {{ $tenant->status === 'active' ? 'badge-success' : ($tenant->status === 'pending' ? 'badge-warning' : 'badge-danger') }} mt-1 inline-block">{{ ucfirst($tenant->status) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Tenant Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Tenant Information</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Coaching Name</span>
                    <span class="font-medium text-gray-900">{{ $tenant->coaching_name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Subdomain</span>
                    <a href="http://{{ $tenant->subdomain }}.btguru.test" target="_blank" class="font-medium text-blue-600 hover:text-blue-700">{{ $tenant->subdomain }}.btguru.test ↗</a>
                </div>
                @if($tenant->custom_domain)
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Custom Domain</span>
                    <a href="https://{{ $tenant->custom_domain }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-700">{{ $tenant->custom_domain }} ↗</a>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Email</span>
                    <span class="font-medium text-gray-900">{{ $tenant->email }}</span>
                </div>
                @if($tenant->phone)
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Phone</span>
                    <span class="font-medium text-gray-900">{{ $tenant->phone }}</span>
                </div>
                @endif
                @if($tenant->address)
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Address</span>
                    <span class="font-medium text-gray-900 text-right max-w-xs">{{ $tenant->address }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Created</span>
                    <span class="font-medium text-gray-900">{{ $tenant->created_at->format('d M Y, h:i A') }}</span>
                </div>
                @if($tenant->expires_at)
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Expires</span>
                    <span class="font-medium {{ $tenant->expires_at->isPast() ? 'text-red-600' : ($tenant->expires_at->diffInDays() <= 30 ? 'text-orange-600' : 'text-gray-900') }}">
                        {{ $tenant->expires_at->format('d M Y') }}
                        @if($tenant->expires_at->isPast()) (Expired) @elseif($tenant->expires_at->diffInDays() <= 30) ({{ $tenant->expires_at->diffInDays() }} days left) @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Users Breakdown -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Users</h3>
            @php
                $admins   = $tenant->users->filter(fn($u) => $u->hasRole('tenant_admin'));
                $teachers = $tenant->users->filter(fn($u) => $u->hasRole('teacher'));
                $students = $tenant->users->filter(fn($u) => $u->hasRole('student'));
            @endphp
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="text-sm font-medium text-blue-800">Admins</span>
                    </div>
                    <span class="font-bold text-blue-700">{{ $admins->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="text-sm font-medium text-purple-800">Teachers</span>
                    </div>
                    <span class="font-bold text-purple-700">{{ $teachers->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-sm font-medium text-green-800">Students</span>
                    </div>
                    <span class="font-bold text-green-700">{{ $students->count() }}</span>
                </div>
            </div>

            <h3 class="text-base font-semibold text-gray-900 mt-6 mb-3">Recent Users</h3>
            <div class="space-y-2">
                @foreach($tenant->users->sortByDesc('created_at')->take(5) as $user)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <span class="text-gray-800">{{ $user->name }}</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->role_display_name }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Courses List -->
    @if($tenant->courses->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Courses ({{ $tenant->courses->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Title</th>
                        <th class="px-4 py-3 text-center">Enrollments</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tenant->courses->sortByDesc('created_at')->take(10) as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $course->title }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $tenant->enrollments->where('course_id', $course->id)->count() }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge {{ $course->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($course->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ $course->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
