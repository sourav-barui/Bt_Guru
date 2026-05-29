@extends('layouts.tenant')

@section('title', 'Create Course')
@section('page-title', 'Create New Course')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.courses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="title" class="form-label">Course Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
                           class="form-input" placeholder="e.g., Mathematics Mastery" required>
                </div>

                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              class="form-input" placeholder="Course description">{{ old('description') }}</textarea>
                </div>

                <!-- Fees Type Selector -->
                <div>
                    <label class="form-label">Fees Type *</label>
                    <div class="grid grid-cols-2 gap-3" id="fees-type-selector">
                        <label class="fees-type-option cursor-pointer">
                            <input type="radio" name="fees_type" value="one_time" class="sr-only fees-type-radio"
                                   {{ old('fees_type', 'one_time') == 'one_time' ? 'checked' : '' }}>
                            <div class="fees-type-card border-2 rounded-xl p-4 text-center transition-all duration-200
                                        {{ old('fees_type', 'one_time') == 'one_time' ? 'border-violet-500 bg-violet-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                <div class="w-10 h-10 rounded-full mx-auto mb-2 flex items-center justify-center
                                            {{ old('fees_type', 'one_time') == 'one_time' ? 'bg-violet-100' : 'bg-gray-100' }}">
                                    <svg class="w-5 h-5 {{ old('fees_type', 'one_time') == 'one_time' ? 'text-violet-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="font-700 text-sm font-bold {{ old('fees_type', 'one_time') == 'one_time' ? 'text-violet-700' : 'text-gray-600' }}">One Time</p>
                                <p class="text-xs mt-1 {{ old('fees_type', 'one_time') == 'one_time' ? 'text-violet-500' : 'text-gray-400' }}">Single payment to enroll</p>
                            </div>
                        </label>
                        <label class="fees-type-option cursor-pointer">
                            <input type="radio" name="fees_type" value="monthly" class="sr-only fees-type-radio"
                                   {{ old('fees_type') == 'monthly' ? 'checked' : '' }}>
                            <div class="fees-type-card border-2 rounded-xl p-4 text-center transition-all duration-200
                                        {{ old('fees_type') == 'monthly' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                <div class="w-10 h-10 rounded-full mx-auto mb-2 flex items-center justify-center
                                            {{ old('fees_type') == 'monthly' ? 'bg-blue-100' : 'bg-gray-100' }}">
                                    <svg class="w-5 h-5 {{ old('fees_type') == 'monthly' ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="font-700 text-sm font-bold {{ old('fees_type') == 'monthly' ? 'text-blue-700' : 'text-gray-600' }}">Monthly</p>
                                <p class="text-xs mt-1 {{ old('fees_type') == 'monthly' ? 'text-blue-500' : 'text-gray-400' }}">Recurring monthly fee</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Fees Amount -->
                <div id="fees-amount-box" class="rounded-xl p-4 border {{ old('fees_type') == 'monthly' ? 'bg-blue-50 border-blue-200' : 'bg-violet-50 border-violet-200' }}">
                    <div class="flex items-center gap-2 mb-3">
                        <span id="fees-badge" class="text-xs font-bold px-2 py-1 rounded-full {{ old('fees_type') == 'monthly' ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700' }}">
                            {{ old('fees_type') == 'monthly' ? '📅 Monthly Fee' : '💳 One-Time Fee' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="fees" class="form-label">Monthly Fee (₹) *</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-semibold">₹</span>
                                <input type="number" id="fees" name="fees" value="{{ old('fees', 0) }}"
                                       class="form-input pl-7" placeholder="0" min="0" step="0.01" required>
                            </div>
                            <p id="fees-help" class="text-xs mt-1 {{ old('fees_type') == 'monthly' ? 'text-blue-500' : 'text-violet-500' }}">
                                {{ old('fees_type') == 'monthly' ? 'Charged every month' : 'Charged once at enrollment' }}
                            </p>
                        </div>
                        <div>
                            <label for="duration" class="form-label">Duration <span class="text-xs text-gray-400 font-normal">(auto-calculated)</span></label>
                            <input type="text" id="duration" name="duration" value="{{ old('duration') }}"
                                   class="form-input bg-gray-50 text-gray-600" placeholder="Set start & end date" readonly>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div>
                            <label for="start_date" class="form-label">Course Start Date</label>
                            <input type="date" id="start_date" name="start_date"
                                   value="{{ old('start_date') }}" class="form-input">
                        </div>
                        <div>
                            <label for="end_date" class="form-label">Course End Date</label>
                            <input type="date" id="end_date" name="end_date"
                                   value="{{ old('end_date') }}" class="form-input">
                        </div>
                    </div>
                    <p id="duration-preview" class="text-xs text-violet-600 font-semibold mt-1 hidden"></p>
                    <!-- Past Month Fee (monthly only) -->
                    <div id="past-month-fee-box" class="mt-3 pt-3 border-t border-blue-200" style="{{ old('fees_type') == 'monthly' ? '' : 'display:none' }}">
                        <label for="past_month_fee" class="form-label">Past Month Access Fee (₹)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-semibold">₹</span>
                            <input type="number" id="past_month_fee" name="past_month_fee" value="{{ old('past_month_fee', 0) }}"
                                   class="form-input pl-7" placeholder="0" min="0" step="0.01">
                        </div>
                        <p class="text-xs text-blue-500 mt-1">Fee charged per past month if student wants to access older content</p>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Duration auto-calculation
                    const startInput    = document.getElementById('start_date');
                    const endInput      = document.getElementById('end_date');
                    const durationInput = document.getElementById('duration');
                    const preview       = document.getElementById('duration-preview');

                    function calcDuration() {
                        if (!startInput.value || !endInput.value) return;
                        const s = new Date(startInput.value);
                        const e = new Date(endInput.value);
                        if (e < s) { preview.textContent = '⚠ End date must be after start date'; preview.classList.remove('hidden'); return; }
                        let months = (e.getFullYear() - s.getFullYear()) * 12 + (e.getMonth() - s.getMonth());
                        const tempDate = new Date(s);
                        tempDate.setMonth(tempDate.getMonth() + months);
                        let days = Math.round((e - tempDate) / (1000 * 60 * 60 * 24));
                        let label = '';
                        if (months > 0) label += months + ' month' + (months !== 1 ? 's' : '');
                        if (days > 0)   label += (label ? ' ' : '') + days + ' day' + (days !== 1 ? 's' : '');
                        if (!label)     label = '0 days';
                        durationInput.value = label;
                        preview.textContent = '✓ Duration: ' + label;
                        preview.classList.remove('hidden');
                    }

                    startInput.addEventListener('change', calcDuration);
                    endInput.addEventListener('change', calcDuration);

                    const radios = document.querySelectorAll('.fees-type-radio');
                    const box = document.getElementById('fees-amount-box');
                    const badge = document.getElementById('fees-badge');
                    const help = document.getElementById('fees-help');
                    const feesLabel = document.querySelector('label[for="fees"]');
                    const pastBox = document.getElementById('past-month-fee-box');

                    radios.forEach(function(radio) {
                        radio.addEventListener('change', function() {
                            const isMonthly = this.value === 'monthly';
                            document.querySelectorAll('.fees-type-option').forEach(function(opt) {
                                const card = opt.querySelector('.fees-type-card');
                                const inp = opt.querySelector('input');
                                const icon = opt.querySelector('svg');
                                const title = opt.querySelector('p:nth-child(2)');
                                const sub = opt.querySelector('p:nth-child(3)');
                                const iconWrap = opt.querySelector('.w-10');
                                const selected = inp.checked;
                                const monthly = inp.value === 'monthly';
                                const color = monthly ? 'blue' : 'violet';
                                card.className = 'fees-type-card border-2 rounded-xl p-4 text-center transition-all duration-200 ' +
                                    (selected ? `border-${color}-500 bg-${color}-50` : 'border-gray-200 bg-white hover:border-gray-300');
                                iconWrap.className = 'w-10 h-10 rounded-full mx-auto mb-2 flex items-center justify-center ' +
                                    (selected ? `bg-${color}-100` : 'bg-gray-100');
                                icon.className = 'w-5 h-5 ' + (selected ? `text-${color}-600` : 'text-gray-400');
                                title.className = 'font-700 text-sm font-bold ' + (selected ? `text-${color}-700` : 'text-gray-600');
                                sub.className = 'text-xs mt-1 ' + (selected ? `text-${color}-500` : 'text-gray-400');
                            });
                            box.className = 'rounded-xl p-4 border ' + (isMonthly ? 'bg-blue-50 border-blue-200' : 'bg-violet-50 border-violet-200');
                            badge.className = 'text-xs font-bold px-2 py-1 rounded-full ' + (isMonthly ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700');
                            badge.textContent = isMonthly ? '📅 Monthly Fee' : '💳 One-Time Fee';
                            help.className = 'text-xs mt-1 ' + (isMonthly ? 'text-blue-500' : 'text-violet-500');
                            help.textContent = isMonthly ? 'Charged every month' : 'Charged once at enrollment';
                            feesLabel.textContent = isMonthly ? 'Monthly Fee (₹) *' : 'Amount (₹) *';
                            pastBox.style.display = isMonthly ? '' : 'none';
                        });
                    });
                });
                </script>

                <div>
                    <label for="thumbnail" class="form-label">Course Thumbnail</label>
                    <input type="file" id="thumbnail" name="thumbnail" 
                           class="form-input" accept="image/*">
                    <p class="text-xs text-gray-500 mt-1">Max 2MB. Recommended size: 400x300px</p>
                </div>

                <div>
                    <label class="form-label">Assign Teachers</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @forelse($teachers as $teacher)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="teachers[]" value="{{ $teacher->id }}" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       {{ in_array($teacher->id, old('teachers', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $teacher->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">No teachers available. <a href="{{ route('tenant.teachers.create') }}" class="text-blue-600">Add teachers</a></p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-input">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="btn-primary">
                    Create Course
                </button>
                <a href="{{ route('tenant.courses.index') }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
