@extends('layouts.tenant')

@section('title', 'Live Class Recordings - ' . $liveClass->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Live Class Recordings</h1>
            <p class="text-gray-600 mt-1">{{ $liveClass->title }} - {{ $liveClass->course?->name ?? 'N/A' }}</p>
        </div>
        <a href="{{ route('tenant.live_classes.index', $liveClass->course_id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to Live Classes
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($recordings->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <p class="text-lg font-medium">No recordings found</p>
                <p class="text-sm mt-1">Recordings will appear here after the live class ends.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recording</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recordings as $recording)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $recording->file_name }}</div>
                            <div class="text-sm text-gray-500">{{ $recording->created_at->format('M d, Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($recording->duration)
                                {{ floor($recording->duration / 60) }}:{{ str_pad($recording->duration % 60, 2, '0', STR_PAD_LEFT) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($recording->file_size)
                                {{ round($recording->file_size / 1048576, 2) }} MB
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($recording->is_approved)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @elseif($recording->status === 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending Approval
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($recording->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($recording->approvedBy)
                                {{ $recording->approvedBy->name }}
                                <div class="text-xs text-gray-400">{{ $recording->approved_at->format('M d, Y') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($recording->status === 'completed')
                                    @if(!$recording->is_approved)
                                        <form action="{{ route('tenant.btlive.recordings.approve', ['recording' => $recording->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('tenant.btlive.recordings.reject', ['recording' => $recording->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tenant.btlive.recordings.download', ['recording' => $recording->id]) }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs"
                                       target="_blank">
                                        Download
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
