@extends('layouts.tenant')

@section('title', 'Subscriptions')
@section('page-title', 'Monthly Subscriptions')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Manage student access windows for monthly courses</p>
        </div>
        <a href="{{ route('tenant.subscriptions.create') }}" class="btn-primary">
            + Add Subscription
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 flex gap-3 flex-wrap">
        <select name="course_id" class="form-input w-auto">
            <option value="">All Courses</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
        </select>
        <select name="student_id" class="form-input w-auto">
            <option value="">All Students</option>
            @foreach($students as $s)
                <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary">Filter</button>
        <a href="{{ route('tenant.subscriptions.index') }}" class="btn-secondary">Reset</a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Student</th>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Course</th>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Type</th>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Access Window</th>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Fee Paid</th>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-600 text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $sub->student->name }}</div>
                        <div class="text-xs text-gray-500">{{ $sub->student->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $sub->course->title }}</td>
                    <td class="px-4 py-3">
                        @if($sub->type === 'past')
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">📅 Past Month</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">🔄 Current</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700 text-xs">
                        {{ $sub->access_start->format('d M Y') }} –<br>{{ $sub->access_end->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">₹{{ number_format($sub->fee_paid, 2) }}</td>
                    <td class="px-4 py-3">
                        @if($sub->payment_status === 'paid')
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">✓ Paid</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            @if($sub->payment_status === 'pending')
                            <form method="POST" action="{{ route('tenant.subscriptions.updateStatus', $sub) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="payment_status" value="paid">
                                <button type="submit" class="text-xs text-green-600 hover:underline font-medium">Mark Paid</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('tenant.subscriptions.destroy', $sub) }}"
                                  onsubmit="return confirm('Remove this subscription?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline font-medium">Remove</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                        No subscriptions found. <a href="{{ route('tenant.subscriptions.create') }}" class="text-violet-600 hover:underline">Add one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $subscriptions->links() }}</div>
</div>
@endsection
