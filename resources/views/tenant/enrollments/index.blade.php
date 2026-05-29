@extends('layouts.tenant')

@section('title', 'Admissions')
@section('page-title', 'Manage Admissions')

@section('page-content')
<div class="space-y-5">

    {{-- Status summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
           class="rounded-xl p-4 text-center border {{ !request('status') ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-indigo-300' }}">
            <p class="text-2xl font-extrabold">{{ array_sum($statusCounts) }}</p>
            <p class="text-xs font-semibold mt-1 opacity-80">All</p>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
           class="rounded-xl p-4 text-center border {{ request('status') === 'pending' ? 'bg-amber-500 border-amber-500 text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-amber-300' }}">
            <p class="text-2xl font-extrabold">{{ $statusCounts['pending'] }}</p>
            <p class="text-xs font-semibold mt-1 opacity-80">Pending</p>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}"
           class="rounded-xl p-4 text-center border {{ request('status') === 'active' ? 'bg-green-600 border-green-600 text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-green-300' }}">
            <p class="text-2xl font-extrabold">{{ $statusCounts['active'] }}</p>
            <p class="text-xs font-semibold mt-1 opacity-80">Active</p>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
           class="rounded-xl p-4 text-center border {{ request('status') === 'rejected' ? 'bg-red-500 border-red-500 text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-red-300' }}">
            <p class="text-2xl font-extrabold">{{ $statusCounts['rejected'] }}</p>
            <p class="text-xs font-semibold mt-1 opacity-80">Rejected</p>
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end w-full">
            {{-- Search --}}
            <div class="flex-1 min-w-56">
                <label class="form-label">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-input pl-9 py-2"
                           placeholder="Name, email or course…"
                           autocomplete="off">
                </div>
            </div>
            {{-- Payment filter --}}
            <div>
                <label class="form-label">Payment</label>
                <select name="payment" class="form-input py-2">
                    <option value="">All Payments</option>
                    <option value="pending"   {{ request('payment') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="partial"   {{ request('payment') === 'partial'   ? 'selected' : '' }}>Partial</option>
                    <option value="completed" {{ request('payment') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary py-2 px-4">Search</button>
                <a href="{{ route('tenant.enrollments.index') }}" class="btn-secondary py-2 px-4">Reset</a>
            </div>
            <div class="ml-auto flex items-end">
                <a href="{{ route('tenant.enrollments.create') }}" class="btn-primary py-2 px-4">+ New Enrollment</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 font-medium text-sm">{{ session('success') }}</div>
    @endif

    {{-- Enrollments list --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Enrollments</h3>
            <span class="text-sm text-gray-400">{{ $enrollments->total() }} total</span>
        </div>

        @if($enrollments->count())
        <div class="divide-y divide-gray-50">
            @foreach($enrollments as $enrollment)
            @php
                $isMonthly       = $enrollment->course->fees_type === 'monthly';
                $pendingPayReqs  = $enrollment->paymentRequests->where('status', 'pending')->count();
                $approvedPayReqs = $enrollment->paymentRequests->where('status', 'approved')->count();
                $totalReceived   = $enrollment->paymentRequests->where('status', 'approved')->sum('amount');
            @endphp
            <div class="p-5">
                <div class="flex flex-col lg:flex-row lg:items-start gap-4">

                    {{-- Student + Course --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 text-sm flex-shrink-0">
                                {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900">{{ $enrollment->student->name }}</p>
                                <p class="text-xs text-gray-500">{{ $enrollment->student->email }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="text-sm font-medium text-gray-700">{{ $enrollment->course->title }}</span>
                                    {{-- Fee type badge --}}
                                    @if($isMonthly)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Monthly ₹{{ number_format($enrollment->course->fees) }}/mo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            One-Time ₹{{ number_format($enrollment->course->fees) }}
                                        </span>
                                    @endif
                                    {{-- Enrolled date --}}
                                    @if($enrollment->enrolled_at)
                                        <span class="text-xs text-gray-400">Enrolled {{ $enrollment->enrolled_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment info --}}
                    <div class="flex-shrink-0 min-w-48">
                        @if($isMonthly)
                            {{-- Monthly: show total received via approved payment requests --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                                <p class="text-xs font-bold text-blue-600 mb-1 uppercase tracking-wide">Monthly Payments</p>
                                <p class="text-xl font-extrabold text-blue-800">₹{{ number_format($totalReceived) }}</p>
                                <p class="text-xs text-blue-500">total received ({{ $approvedPayReqs }} payment{{ $approvedPayReqs != 1 ? 's' : '' }})</p>
                                @if($pendingPayReqs > 0)
                                    <a href="{{ route('tenant.payments.index', ['course_id' => $enrollment->course_id]) }}"
                                       class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-amber-700 bg-amber-100 rounded-lg px-2 py-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $pendingPayReqs }} pending request{{ $pendingPayReqs != 1 ? 's' : '' }}
                                    </a>
                                @endif
                            </div>
                        @else
                            {{-- One-time: show paid / total progress bar --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                                <p class="text-xs font-bold text-gray-500 mb-1 uppercase tracking-wide">One-Time Fee</p>
                                <div class="flex items-baseline gap-1 mb-1">
                                    <span class="text-xl font-extrabold text-gray-800">₹{{ number_format($enrollment->fees_paid) }}</span>
                                    <span class="text-sm text-gray-400">/ ₹{{ number_format($enrollment->fees_total) }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $enrollment->isPaymentCompleted() ? 'bg-green-500' : 'bg-orange-400' }}"
                                         style="width: {{ $enrollment->payment_percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ number_format($enrollment->payment_percentage, 0) }}% paid</p>
                                @if($pendingPayReqs > 0)
                                    <a href="{{ route('tenant.payments.index', ['course_id' => $enrollment->course_id]) }}"
                                       class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-amber-700 bg-amber-100 rounded-lg px-2 py-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $pendingPayReqs }} pending request{{ $pendingPayReqs != 1 ? 's' : '' }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Status + Actions --}}
                    <div class="flex-shrink-0 flex flex-col gap-2 items-end">
                        {{-- Enrollment status --}}
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-full
                            {{ match($enrollment->enrollment_status) {
                                'active'    => 'bg-green-100 text-green-700',
                                'approved'  => 'bg-sky-100 text-sky-700',
                                'pending'   => 'bg-amber-100 text-amber-700',
                                'rejected'  => 'bg-red-100 text-red-700',
                                'completed' => 'bg-indigo-100 text-indigo-700',
                                default     => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ ucfirst($enrollment->enrollment_status) }}
                        </span>
                        {{-- Payment status --}}
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full
                            {{ match($enrollment->payment_status) {
                                'completed' => 'bg-green-50 text-green-600 border border-green-200',
                                'partial'   => 'bg-blue-50 text-blue-600 border border-blue-200',
                                'pending'   => 'bg-orange-50 text-orange-600 border border-orange-200',
                                'refunded'  => 'bg-red-50 text-red-600 border border-red-200',
                                default     => 'bg-gray-50 text-gray-500 border border-gray-200',
                            } }}">
                            {{ $isMonthly ? 'Pay: ' : '' }}{{ ucfirst($enrollment->payment_status) }}
                        </span>
                        {{-- Actions --}}
                        <div class="flex items-center gap-1.5 mt-1">
                            @if($enrollment->enrollment_status === 'pending')
                                <form method="POST" action="{{ route('tenant.enrollments.approve', $enrollment) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold py-1.5 px-3 rounded-lg bg-green-600 text-white hover:bg-green-700" title="Approve">
                                        ✓ Approve
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('tenant.enrollments.show', $enrollment) }}"
                               class="text-xs font-bold py-1.5 px-3 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                                View
                            </a>
                            <a href="{{ route('tenant.enrollments.edit', $enrollment) }}"
                               class="text-xs font-bold py-1.5 px-3 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                Edit
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $enrollments->withQueryString()->links() }}
        </div>
        @else
        <div class="p-12 text-center text-gray-400">
            <p class="text-base font-medium">No enrollments found</p>
        </div>
        @endif
    </div>

</div>
@endsection
