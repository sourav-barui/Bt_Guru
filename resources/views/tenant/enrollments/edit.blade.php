@extends('layouts.tenant')

@section('title', 'Edit Enrollment')
@section('page-title', 'Edit Admission')

@section('page-content')
@php
    $isMonthly   = $enrollment->course->fees_type === 'monthly';
    $payRequests = $enrollment->paymentRequests()->with('reviewer')->latest()->get();
    $totalReceived = $payRequests->where('status','approved')->sum('amount');
    $pendingCount  = $payRequests->where('status','pending')->count();
@endphp

<div class="max-w-4xl space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 font-medium text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- Top info strip --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-wrap gap-5 items-center">
        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center font-extrabold text-indigo-700 text-lg flex-shrink-0">
            {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 text-base">{{ $enrollment->student->name }}</p>
            <p class="text-sm text-gray-500">{{ $enrollment->student->email }}</p>
        </div>
        <div class="text-center px-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Course</p>
            <p class="font-semibold text-gray-800 text-sm mt-0.5">{{ $enrollment->course->title }}</p>
        </div>
        <div class="text-center px-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Fee Type</p>
            @if($isMonthly)
                <span class="inline-flex items-center gap-1 mt-1 text-xs font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Monthly · ₹{{ number_format($enrollment->course->fees) }}/mo
                </span>
            @else
                <span class="inline-flex items-center gap-1 mt-1 text-xs font-bold px-2.5 py-1 rounded-full bg-violet-100 text-violet-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    One-Time · ₹{{ number_format($enrollment->course->fees) }}
                </span>
            @endif
        </div>
        @if($enrollment->enrolled_at)
        <div class="text-center px-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Enrolled</p>
            <p class="font-semibold text-gray-700 text-sm mt-0.5">{{ $enrollment->enrolled_at->format('d M Y') }}</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Left: Edit form --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Enrollment Settings</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('tenant.enrollments.update', $enrollment) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="student_id" value="{{ $enrollment->student_id }}">

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Enrollment Status</label>
                            <select name="enrollment_status" class="form-input">
                                @foreach(['pending','approved','active','completed','rejected','dropped'] as $s)
                                    <option value="{{ $s }}" {{ old('enrollment_status', $enrollment->enrollment_status) == $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-input">
                                @foreach(['pending','partial','completed','refunded'] as $s)
                                    <option value="{{ $s }}" {{ old('payment_status', $enrollment->payment_status) == $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(!$isMonthly)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Fees Paid (₹)</label>
                                <input type="number" name="fees_paid"
                                       value="{{ old('fees_paid', $enrollment->fees_paid) }}"
                                       class="form-input" step="0.01" min="0">
                            </div>
                            <div>
                                <label class="form-label">Total Fees (₹)</label>
                                <input type="number" name="fees_total"
                                       value="{{ old('fees_total', $enrollment->fees_total) }}"
                                       class="form-input" step="0.01" min="0">
                            </div>
                        </div>
                        @else
                        {{-- Monthly: show read-only total received --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">Total Received (Monthly)</p>
                            <p class="text-2xl font-extrabold text-blue-800">₹{{ number_format($totalReceived) }}</p>
                            <p class="text-xs text-blue-500 mt-0.5">from {{ $payRequests->where('status','approved')->count() }} approved payment(s)</p>
                        </div>
                        @endif

                        <div>
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" rows="2"
                                      class="form-input" placeholder="Any additional notes">{{ old('remarks', $enrollment->remarks) }}</textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-5">
                        <button type="submit" class="btn-primary">Save Changes</button>
                        <a href="{{ route('tenant.enrollments.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Payment history + manual add --}}
        <div class="space-y-5">

            {{-- Manual payment entry --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Add Manual Payment</h3>
                    <span class="text-xs text-gray-400">Admin override</span>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.enrollments.addPayment', $enrollment) }}">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">Amount (₹)</label>
                                <input type="number" name="amount" class="form-input" min="1" step="0.01" placeholder="0.00" required>
                            </div>
                            @if($isMonthly)
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Month</label>
                                    <select name="month_number" class="form-input">
                                        <option value="">— Month —</option>
                                        @foreach(range(1,12) as $m)
                                            <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Year</label>
                                    <select name="year_number" class="form-input">
                                        @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                            <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div>
                                <label class="form-label">Reference / Note</label>
                                <input type="text" name="reference_number" class="form-input" placeholder="Receipt / transaction ref">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full mt-4">+ Add Payment</button>
                    </form>
                </div>
            </div>

            {{-- Payment request history --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Payment History</h3>
                    @if($pendingCount > 0)
                        <a href="{{ route('tenant.payments.index', ['course_id' => $enrollment->course_id]) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-100 rounded-lg px-2.5 py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $pendingCount }} pending
                        </a>
                    @endif
                </div>
                @if($payRequests->count())
                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                    @foreach($payRequests as $pr)
                    <div class="px-5 py-3 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-gray-800">₹{{ number_format($pr->amount) }}</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    {{ $pr->status === 'approved' ? 'bg-green-100 text-green-700' : ($pr->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($pr->status) }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    {{ $pr->payment_type === 'enrollment' ? 'bg-indigo-100 text-indigo-700' : ($pr->payment_type === 'monthly' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $pr->payment_type_label }}
                                </span>
                                @if($pr->month_label)
                                    <span class="text-xs text-gray-400">{{ $pr->month_label }}</span>
                                @endif
                            </div>
                            @if($pr->reference_number)
                                <p class="text-xs text-gray-400 mt-0.5">Ref: {{ $pr->reference_number }}</p>
                            @endif
                            @if($pr->admin_remark)
                                <p class="text-xs text-gray-400 italic mt-0.5">{{ $pr->admin_remark }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400">{{ $pr->created_at->format('d M Y') }}</p>
                            @if($pr->screenshot)
                                <a href="{{ Storage::url($pr->screenshot) }}" target="_blank"
                                   class="text-xs text-indigo-500 underline font-semibold">Receipt</a>
                            @endif
                            @if($pr->status === 'pending')
                            <div class="flex gap-1 mt-1 justify-end">
                                <form method="POST" action="{{ route('tenant.payments.approve', $pr) }}">
                                    @csrf
                                    <button class="text-xs font-bold px-2 py-0.5 rounded bg-green-600 text-white">✓</button>
                                </form>
                                <form method="POST" action="{{ route('tenant.payments.reject', $pr) }}" id="rej-{{ $pr->id }}">
                                    @csrf
                                    <input type="hidden" name="admin_remark" id="rem-{{ $pr->id }}">
                                    <button type="button" class="text-xs font-bold px-2 py-0.5 rounded bg-red-100 text-red-700"
                                        onclick="var r=prompt('Rejection reason:');if(r){document.getElementById('rem-{{ $pr->id }}').value=r;document.getElementById('rej-{{ $pr->id }}').submit();}">✕</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No payment records yet.</div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
