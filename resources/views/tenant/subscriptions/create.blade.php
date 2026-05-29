@extends('layouts.tenant')

@section('title', 'Add Subscription')
@section('page-title', 'Add Monthly Subscription')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.subscriptions.store') }}">
            @csrf
            <div class="space-y-4">

                {{-- Course --}}
                <div>
                    <label class="form-label">Course (Monthly) *</label>
                    <select name="course_id" id="course_id" class="form-input" required>
                        <option value="">Select a monthly course</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}"
                                data-monthly="{{ $c->fees }}"
                                data-past="{{ $c->past_month_fee }}"
                                {{ old('course_id', $selectedCourse?->id) == $c->id ? 'selected' : '' }}>
                                {{ $c->title }} — ₹{{ number_format($c->fees,2) }}/mo
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Student --}}
                <div>
                    <label class="form-label">Student *</label>
                    <select name="student_id" class="form-input" required>
                        <option value="">Select student</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type --}}
                <div>
                    <label class="form-label">Subscription Type *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="current" class="sr-only sub-type-radio"
                                   {{ old('type', 'current') === 'current' ? 'checked' : '' }}>
                            <div class="sub-type-card border-2 rounded-xl p-4 text-center transition-all
                                        {{ old('type', 'current') === 'current' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <div class="text-2xl mb-1">🔄</div>
                                <p class="font-bold text-sm {{ old('type','current')==='current' ? 'text-blue-700' : 'text-gray-600' }}">Current Month</p>
                                <p class="text-xs mt-1 {{ old('type','current')==='current' ? 'text-blue-500' : 'text-gray-400' }}">Access from today +30 days</p>
                                <p id="current-fee-label" class="text-xs font-bold mt-2 text-blue-600"></p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="past" class="sr-only sub-type-radio"
                                   {{ old('type') === 'past' ? 'checked' : '' }}>
                            <div class="sub-type-card border-2 rounded-xl p-4 text-center transition-all
                                        {{ old('type') === 'past' ? 'border-amber-500 bg-amber-50' : 'border-gray-200' }}">
                                <div class="text-2xl mb-1">📅</div>
                                <p class="font-bold text-sm {{ old('type')==='past' ? 'text-amber-700' : 'text-gray-600' }}">Past Month</p>
                                <p class="text-xs mt-1 {{ old('type')==='past' ? 'text-amber-500' : 'text-gray-400' }}">Access a previous 30-day window</p>
                                <p id="past-fee-label" class="text-xs font-bold mt-2 text-amber-600"></p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Access Start Date --}}
                <div>
                    <label class="form-label">Access Start Date *</label>
                    <input type="date" name="access_start" class="form-input" value="{{ old('access_start', now()->toDateString()) }}" required>
                    <p class="text-xs text-gray-500 mt-1">Access window = start date + 29 days (30 days total)</p>
                </div>

                {{-- Fee Paid --}}
                <div>
                    <label class="form-label">Fee Amount (₹) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-semibold">₹</span>
                        <input type="number" name="fee_paid" id="fee_paid" class="form-input pl-7"
                               value="{{ old('fee_paid', 0) }}" min="0" step="0.01" required>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div>
                    <label class="form-label">Payment Status *</label>
                    <select name="payment_status" class="form-input" required>
                        <option value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'selected' : '' }}>✓ Paid</option>
                        <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    </select>
                </div>

                {{-- Remarks --}}
                <div>
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-input" rows="2" placeholder="Optional note">{{ old('remarks') }}</textarea>
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary">Add Subscription</button>
                <a href="{{ route('tenant.subscriptions.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const courseSelect = document.getElementById('course_id');
    const feeInput = document.getElementById('fee_paid');
    const currentLabel = document.getElementById('current-fee-label');
    const pastLabel = document.getElementById('past-fee-label');
    const typeRadios = document.querySelectorAll('.sub-type-radio');

    function updateFeeFromCourse() {
        const opt = courseSelect.options[courseSelect.selectedIndex];
        const monthly = opt ? opt.dataset.monthly : 0;
        const past    = opt ? opt.dataset.past    : 0;
        currentLabel.textContent = monthly ? '₹' + parseFloat(monthly).toFixed(2) + '/month' : '';
        pastLabel.textContent    = past    ? '₹' + parseFloat(past).toFixed(2)    + '/month' : '';
        autoFillFee(monthly, past);
    }

    function autoFillFee(monthly, past) {
        const selectedType = document.querySelector('.sub-type-radio:checked');
        if (!selectedType) return;
        if (selectedType.value === 'current') {
            feeInput.value = monthly ? parseFloat(monthly).toFixed(2) : 0;
        } else {
            feeInput.value = past ? parseFloat(past).toFixed(2) : 0;
        }
    }

    function updateTypeCards() {
        typeRadios.forEach(function (radio) {
            const card = radio.parentElement.querySelector('.sub-type-card');
            const title = card.querySelectorAll('p')[0];
            const sub   = card.querySelectorAll('p')[1];
            const isPast = radio.value === 'past';
            const color  = isPast ? 'amber' : 'blue';
            if (radio.checked) {
                card.className = `sub-type-card border-2 rounded-xl p-4 text-center transition-all border-${color}-500 bg-${color}-50`;
                title.className = `font-bold text-sm text-${color}-700`;
                sub.className   = `text-xs mt-1 text-${color}-500`;
            } else {
                card.className = 'sub-type-card border-2 rounded-xl p-4 text-center transition-all border-gray-200';
                title.className = 'font-bold text-sm text-gray-600';
                sub.className   = 'text-xs mt-1 text-gray-400';
            }
        });
    }

    courseSelect.addEventListener('change', updateFeeFromCourse);
    typeRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateTypeCards();
            const opt = courseSelect.options[courseSelect.selectedIndex];
            autoFillFee(opt ? opt.dataset.monthly : 0, opt ? opt.dataset.past : 0);
        });
    });

    // Init
    updateFeeFromCourse();
});
</script>
@endsection
