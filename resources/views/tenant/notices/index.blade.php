@extends('layouts.tenant')

@section('title', 'Notices')
@section('page-title', 'Manage Notices')

@section('page-content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">All Notices</h3>
        <a href="{{ route('tenant.notices.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Post Notice
        </a>
    </div>

    <div class="divide-y divide-gray-200">
        @forelse($notices as $notice)
            <div class="px-6 py-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="badge {{ $notice->type_badge_class }}">{{ ucfirst($notice->type) }}</span>
                            @if($notice->is_published)
                                <span class="badge badge-success">Published</span>
                            @else
                                <span class="badge badge-warning">Draft</span>
                            @endif
                        </div>
                        <h4 class="font-medium text-gray-900">{{ $notice->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $notice->excerpt }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span>Posted: {{ $notice->created_at->format('M d, Y') }}</span>
                            @if($notice->course)
                                <span>Course: {{ $notice->course->title }}</span>
                            @endif
                            <span>Audience: {{ ucfirst($notice->audience) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('tenant.notices.edit', $notice) }}" class="text-gray-600 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('tenant.notices.destroy', $notice) }}" class="inline" onsubmit="return confirm('Delete this notice?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-500">
                No notices found. <a href="{{ route('tenant.notices.create') }}" class="text-blue-600">Create one</a>
            </div>
        @endforelse
    </div>

    @if($notices->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notices->links() }}
        </div>
    @endif
</div>
@endsection
