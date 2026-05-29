@extends('layouts.tenant')
@section('title', 'Payment Requests')
@section('page-title', 'Payment Requests')

@section('page-content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold text-amber-700">{{ $stats['pending'] }}</p>
            <p class="text-xs text-amber-600 font-semibold mt-1">Pending</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold text-green-700">{{ $stats['approved'] }}</p>
            <p class="text-xs text-green-600 font-semibold mt-1">Approved</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold text-red-700">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-red-600 font-semibold mt-1">Rejected</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold text-indigo-700">₹{{ number_format($stats['total_collected']) }}</p>
            <p class="text-xs text-indigo-600 font-semibold mt-1">Total Collected</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-input py-2">
                    <option value="">All Status</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="form-label">Course</label>
                <select name="course_id" class="form-input py-2">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary py-2 px-4">Filter</button>
            <a href="{{ route('tenant.payments.index') }}" class="btn-secondary py-2 px-4">Reset</a>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 font-medium text-sm">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Payment Requests</h3>
            <span class="text-sm text-gray-500">{{ $payments->total() }} total</span>
        </div>

        @if($payments->count())
        <div class="divide-y divide-gray-50">
            @foreach($payments as $payment)
            <div class="p-5">
                <div class="flex flex-col md:flex-row md:items-start gap-4">

                    {{-- Left: student + course info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-1">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 text-sm flex-shrink-0">
                                {{ strtoupper(substr($payment->student->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $payment->student->name }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->student->email }}</p>
                            </div>
                        </div>
                        <div class="ml-12 space-y-1">
                            <p class="text-sm font-medium text-gray-800">
                                @if($payment->book)
                                    {{ $payment->book->title }}
                                @elseif($payment->course)
                                    {{ $payment->course->title }}
                                @else
                                    —
                                @endif
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full
                                    @php $pt = $payment->payment_type; @endphp
                                    {{ $pt === 'enrollment' ? 'bg-indigo-100 text-indigo-700' : ($pt === 'monthly' ? 'bg-blue-100 text-blue-700' : ($pt === 'book_purchase' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700')) }}">
                                    {{ $payment->payment_type_label }}
                                </span>
                                @if($payment->month_label)
                                    <span class="text-xs text-gray-500 font-medium">{{ $payment->month_label }}</span>
                                @endif
                                @if($payment->reference_number)
                                    <span class="text-xs text-gray-400">Ref: {{ $payment->reference_number }}</span>
                                @endif
                            </div>
                            @if($payment->note)
                                <p class="text-xs text-gray-500 italic">{{ $payment->note }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Middle: amount + screenshot --}}
                    <div class="text-center md:text-right flex-shrink-0">
                        <p class="text-2xl font-extrabold text-gray-900">₹{{ number_format($payment->amount) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $payment->created_at->format('d M Y, h:i A') }}</p>
                        @if($payment->screenshot)
                            <a href="{{ Storage::url($payment->screenshot) }}" target="_blank" class="text-xs text-indigo-600 underline font-semibold mt-1 block">View Receipt</a>
                        @endif
                    </div>

                    {{-- Right: status + actions --}}
                    <div class="flex-shrink-0 flex flex-col gap-2 items-end min-w-36">
                        @if($payment->status === 'pending')
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pending
                            </span>
                            {{-- Approve --}}
                            <form action="{{ route('tenant.payments.approve', $payment) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-xs font-bold py-1.5 px-3 rounded-lg bg-green-600 text-white hover:bg-green-700">
                                    ✓ Approve
                                </button>
                            </form>
                            {{-- Reject --}}
                            <form action="{{ route('tenant.payments.reject', $payment) }}" method="POST" class="w-full" id="reject-form-{{ $payment->id }}">
                                @csrf
                                <input type="hidden" name="admin_remark" id="remark-{{ $payment->id }}">
                                <button type="button" class="w-full text-xs font-bold py-1.5 px-3 rounded-lg bg-red-100 text-red-700 hover:bg-red-200"
                                    onclick="
                                        var r = prompt('Reason for rejection (required):');
                                        if(r){ document.getElementById('remark-{{ $payment->id }}').value=r; document.getElementById('reject-form-{{ $payment->id }}').submit(); }
                                    ">
                                    ✕ Reject
                                </button>
                            </form>
                        @elseif($payment->status === 'approved')
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-green-100 text-green-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Approved
                            </span>
                            @if($payment->admin_remark)
                                <p class="text-xs text-gray-400 text-right">{{ $payment->admin_remark }}</p>
                            @endif
                            {{-- Enroll button if no enrollment exists --}}
                            @if(!$payment->enrollment_id)
                                <form action="{{ route('tenant.payments.enroll', $payment) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-xs font-bold py-1.5 px-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Enroll Student
                                    </button>
                                </form>
                            @endif
                            {{-- Rewind button to reset to pending --}}
                            <form action="{{ route('tenant.payments.rewind', $payment) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-xs font-bold py-1.5 px-3 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 flex items-center justify-center gap-1" onclick="return confirm('Reset this payment to pending status?');">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    Rewind to Pending
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-red-100 text-red-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                                Rejected
                            </span>
                            @if($payment->admin_remark)
                                <p class="text-xs text-red-400 text-right max-w-36">{{ $payment->admin_remark }}</p>
                            @endif
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $payments->withQueryString()->links() }}
        </div>
        @else
        <div class="p-12 text-center text-gray-400">
            <p class="text-lg font-medium">No payment requests found</p>
        </div>
        @endif
    </div>

</div>
@endsection
