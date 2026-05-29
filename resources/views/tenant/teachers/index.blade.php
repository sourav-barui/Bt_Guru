@extends('layouts.tenant')

@section('title', 'Teachers')
@section('page-title', 'Manage Teachers')

@section('page-content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">All Teachers</h3>
        <a href="{{ route('tenant.teachers.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Teacher
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Teacher</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Courses</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($teachers as $teacher)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-sm">{{ substr($teacher->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $teacher->name }}</p>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-sm text-gray-600">{{ $teacher->email }}</td>
                        <td class="text-sm text-gray-600">{{ $teacher->phone ?? '-' }}</td>
                        <td class="text-sm text-gray-600">{{ $teacher->courses()->count() }}</td>
                        <td>
                            <span class="badge {{ $teacher->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($teacher->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tenant.teachers.edit', $teacher) }}" class="text-gray-600 hover:text-gray-700" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('tenant.teachers.destroy', $teacher) }}" class="inline" onsubmit="return confirm('Delete this teacher?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No teachers found. <a href="{{ route('tenant.teachers.create') }}" class="text-blue-600">Add one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($teachers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $teachers->links() }}
        </div>
    @endif
</div>
@endsection
