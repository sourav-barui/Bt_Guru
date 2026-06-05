@extends('layouts.tenant')

@section('title', 'BTLive Attendance - ' . $liveClass->title)
@section('page-title', 'Attendance Report')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tenant.btlive.teacher_room', $liveClass) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Classroom
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $liveClass->title }}</h1>
            <p class="text-gray-500">{{ $liveClass->course?->title ?? 'N/A' }}</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="exportAttendance()" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </button>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Enrolled</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_enrolled'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Present</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_present'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Avg Duration</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['average_duration_minutes'] }}m</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Attendance Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['attendance_percentage'] }}%</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Attendance Records</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Joined At</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Left At</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Duration</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Device</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendance as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                        {{ $record->student ? strtoupper(substr($record->student->name, 0, 1)) : '?' }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $record->student?->name ?? $record->display_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $record->student?->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $record->joined_at->format('M d, h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($record->left_at)
                                    {{ $record->left_at->format('M d, h:i A') }}
                                @else
                                    <span class="text-green-600 font-medium">Still in class</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($record->duration_seconds > 0)
                                    {{ floor($record->duration_seconds / 60) }} min
                                @elseif($record->joined_at && !$record->left_at)
                                    {{ floor(now()->diffInSeconds($record->joined_at) / 60) }} min (ongoing)
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    @switch($record->device_type)
                                        @case('mobile')
                                            📱 {{ $record->os }}
                                            @break
                                        @case('tablet')
                                            📲 {{ $record->os }}
                                            @break
                                        @default
                                            💻 {{ $record->os }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($record->was_kicked)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Removed
                                    </span>
                                    @if($record->kick_reason)
                                        <p class="text-xs text-gray-500 mt-1">{{ $record->kick_reason }}</p>
                                    @endif
                                @elseif($record->duration_seconds > ($liveClass->duration_minutes * 60 * 0.5))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Present
                                    </span>
                                @elseif($record->duration_seconds > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Partial
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Joined
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                No attendance records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendance->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendance->links() }}
            </div>
        @endif
    </div>
    
    <!-- Summary Note -->
    <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-900 font-medium">Attendance Guidelines</p>
                <p class="text-sm text-blue-700 mt-1">
                    Students are marked <strong>Present</strong> if they attended for more than 50% of the class duration.
                    <strong>Partial</strong> attendance is given for less than 50% but more than 5 minutes.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function exportAttendance() {
    // Simple CSV export
    const rows = [
        ['Student Name', 'Email', 'Joined At', 'Left At', 'Duration (min)', 'Device', 'OS', 'Status'],
        @json($attendance->map(fn($r) => [
            $r->student?->name ?? $r->display_name ?? 'Unknown',
            $r->student?->email ?? 'N/A',
            $r->joined_at->format('Y-m-d H:i:s'),
            $r->left_at?->format('Y-m-d H:i:s') ?? 'In Progress',
            floor($r->duration_seconds / 60),
            $r->device_type,
            $r->os,
            $r->was_kicked ? 'Removed' : ($r->duration_seconds > 0 ? 'Present' : 'Joined')
        ]))
    ];
    
    const csv = rows.flat().map(row => row.join(',')).join('\n');
    const blob = new Blob([csv], {type: 'text/csv'});
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'attendance-{{ $liveClass->id }}-{{ now()->format("Y-m-d") }}.csv';
    a.click();
}
</script>
@endsection
