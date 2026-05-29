@extends('layouts.student_mobile')
@section('title', 'Submit Payment')

@php
    $tenantSettings = auth()->user()->tenant->settings ?? [];
    $upiId   = $tenantSettings['upi_id']   ?? null;
    $upiName = $tenantSettings['upi_name'] ?? auth()->user()->tenant->coaching_name;
    $bankName    = $tenantSettings['bank_name']    ?? null;
    $bankAccount = $tenantSettings['bank_account'] ?? null;
    $bankIfsc    = $tenantSettings['bank_ifsc']    ?? null;
    $bankHolder  = $tenantSettings['bank_holder']  ?? null;
@endphp

@push('styles')
<style>
.pay-page { padding-bottom: 32px; }
.pay-heading { font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 4px; }
.pay-sub { font-size: 13px; color: #6b7280; margin-bottom: 20px; }

.pay-card { background: #fff; border-radius: 18px; box-shadow: 0 2px 14px rgba(0,0,0,0.07); border: 1px solid #f0f0f0; overflow: hidden; }
.pay-card-header { padding: 16px 20px; background: linear-gradient(135deg, #6366f1, #4f46e5); }
.pay-card-header h2 { font-size: 15px; font-weight: 700; color: #fff; }
.pay-card-header p  { font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 2px; }
.pay-card-body { padding: 20px; }

.pay-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
.pay-input  { width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 14px; color: #111827; outline: none; box-sizing: border-box; transition: border 0.2s; }
.pay-input:focus { border-color: #6366f1; }
.pay-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 36px; }
.pay-field { margin-bottom: 16px; }
.pay-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.pay-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }

.type-tabs { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 16px; }
.type-tab { border: 2px solid #e5e7eb; border-radius: 10px; padding: 10px 6px; text-align: center; cursor: pointer; transition: all 0.2s; }
.type-tab input[type=radio] { display: none; }
.type-tab-label { font-size: 12px; font-weight: 700; color: #6b7280; display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; }
.type-tab-label svg { width: 20px; height: 20px; stroke: #9ca3af; }
.type-tab.active-enrollment { border-color: #6366f1; background: #eef2ff; }
.type-tab.active-enrollment .type-tab-label { color: #4f46e5; }
.type-tab.active-enrollment svg { stroke: #4f46e5; }
.type-tab.active-monthly { border-color: #0ea5e9; background: #eff6ff; }
.type-tab.active-monthly .type-tab-label { color: #0369a1; }
.type-tab.active-monthly svg { stroke: #0369a1; }
.type-tab.active-past_month { border-color: #f59e0b; background: #fffbeb; }
.type-tab.active-past_month .type-tab-label { color: #b45309; }
.type-tab.active-past_month svg { stroke: #b45309; }

.month-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px; margin-bottom: 16px; display: none; }
.month-box.show { display: block; }

.past-months-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px; margin-bottom: 16px; }
.past-months-box.show { display: block !important; }

.month-checkbox-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 6px;
    cursor: pointer;
    transition: all 0.2s;
}
.month-checkbox-item:hover {
    background: #fef3c7;
    border-color: #f59e0b;
}
.month-checkbox-item.selected {
    background: #d1fae5;
    border-color: #10b981;
}
.month-checkbox-item.paid {
    opacity: 0.5;
    background: #f3f4f6;
    cursor: not-allowed;
}
.month-checkbox-left {
    display: flex;
    align-items: center;
    gap: 8px;
}
.month-checkbox-label {
    font-size: 13px;
    font-weight: 500;
    color: #374151;
}
.month-checkbox-item.selected .month-checkbox-label {
    color: #047857;
    font-weight: 600;
}
.month-price {
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
}

.fee-preview { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #86efac; border-radius: 12px; padding: 14px; margin-bottom: 16px; display: none; }
.fee-preview.show { display: block; }
.fee-preview-amount { font-size: 24px; font-weight: 800; color: #16a34a; }
.fee-preview-label  { font-size: 12px; color: #15803d; font-weight: 600; }

.upload-box { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; }
.upload-box:hover { border-color: #6366f1; background: #f5f3ff; }
.upload-box svg { width: 28px; height: 28px; stroke: #9ca3af; margin: 0 auto 6px; display: block; }
.upload-box p { font-size: 13px; color: #6b7280; }
.upload-box small { font-size: 11px; color: #9ca3af; }

.submit-btn { display: block; width: 100%; padding: 14px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.4); }

.alert-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: #dc2626; }

/* UPI card */
.upi-card { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%); border-radius: 18px; padding: 20px; margin-bottom: 20px; color: #fff; position: relative; overflow: hidden; }
.upi-card::before { content: ''; position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%; }
.upi-card::after  { content: ''; position: absolute; bottom: -40px; left: 20px; width: 90px; height: 90px; background: rgba(255,255,255,0.04); border-radius: 50%; }
.upi-label  { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; color: rgba(255,255,255,0.6); text-transform: uppercase; margin-bottom: 6px; }
.upi-id     { font-size: 18px; font-weight: 800; color: #fff; font-family: monospace; word-break: break-all; margin-bottom: 4px; }
.upi-name   { font-size: 12px; color: rgba(255,255,255,0.65); margin-bottom: 16px; }
.upi-btn    { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #1a1a2e; border-radius: 10px; padding: 10px 18px; font-size: 13px; font-weight: 800; text-decoration: none; cursor: pointer; border: none; }
.upi-btn svg { width: 20px; height: 20px; }
.upi-copy-btn { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.12); color: #fff; border-radius: 10px; padding: 10px 14px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; margin-left: 8px; }
.upi-copy-btn svg { width: 14px; height: 14px; stroke: #fff; }
.bank-card { background: #f0fdf4; border: 1px solid #86efac; border-radius: 14px; padding: 14px 16px; margin-bottom: 20px; }
.bank-row  { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px solid #dcfce7; }
.bank-row:last-child { border-bottom: none; }
.bank-key  { font-size: 11px; color: #6b7280; font-weight: 600; }
.bank-val  { font-size: 13px; color: #111827; font-weight: 700; font-family: monospace; }
</style>
@endpush

@section('mobile-content')
<div class="pay-page">

    <p class="pay-heading">Submit Payment</p>
    <p class="pay-sub">Fill in your payment details and upload proof of payment</p>

    @if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    {{-- UPI Payment Info Card --}}
    @if($upiId)
    <div class="upi-card">
        <div style="position:relative;z-index:1;">
            <div class="upi-label">Pay Via UPI</div>
            <div class="upi-id" id="upi-id-text">{{ $upiId }}</div>
            <div class="upi-name">{{ $upiName }}</div>
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                {{-- Pay with UPI deep-link button --}}
                <a id="upi-pay-btn" href="upi://pay?pa={{ urlencode($upiId) }}&pn={{ urlencode($upiName) }}&cu=INR" class="upi-btn">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="48" height="48" rx="10" fill="#fff"/>
                        <path d="M14 24L24 10L34 24" stroke="#6366f1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 34L24 20L34 34" stroke="#0ea5e9" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pay with UPI
                </a>
                {{-- Copy UPI ID --}}
                <button type="button" class="upi-copy-btn" onclick="copyUpi()">
                    <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span id="copy-label">Copy ID</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Bank Account Info --}}
    @if($bankAccount)
    <div class="bank-card">
        <p style="font-size:11px;font-weight:700;color:#16a34a;letter-spacing:1px;text-transform:uppercase;margin-bottom:10px;">Bank Transfer Details</p>
        @if($bankHolder)
        <div class="bank-row"><span class="bank-key">Account Holder</span><span class="bank-val">{{ $bankHolder }}</span></div>
        @endif
        @if($bankName)
        <div class="bank-row"><span class="bank-key">Bank</span><span class="bank-val">{{ $bankName }}</span></div>
        @endif
        <div class="bank-row"><span class="bank-key">Account No.</span><span class="bank-val">{{ $bankAccount }}</span></div>
        @if($bankIfsc)
        <div class="bank-row"><span class="bank-key">IFSC</span><span class="bank-val">{{ $bankIfsc }}</span></div>
        @endif
    </div>
    @endif

    @if(!$upiId && !$bankAccount)
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:14px;padding:14px;margin-bottom:20px;font-size:13px;color:#b45309;">
        <strong>Note:</strong> Contact administration for payment details before submitting.
    </div>
    @endif

    <div class="pay-card">
        <div class="pay-card-header">
            <h2>New Payment Request</h2>
            <p>Your request will be reviewed and approved by the admin</p>
        </div>
        <div class="pay-card-body">
            <form action="{{ route('student.payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Payment Type --}}
                <div class="pay-field">
                    <label class="pay-label">Payment Type</label>
                    <div class="type-tabs" id="type-tabs">
                        <div class="type-tab {{ old('payment_type', $payType) === 'enrollment' ? 'active-enrollment' : '' }}" id="tab-enrollment" data-enrollment-tab>
                            <label class="type-tab-label" for="type_enrollment">
                                <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Enrollment
                            </label>
                            <input type="radio" id="type_enrollment" name="payment_type" value="enrollment" {{ old('payment_type', $payType) === 'enrollment' ? 'checked' : '' }}>
                        </div>
                        <div class="type-tab {{ old('payment_type', $payType) === 'monthly' ? 'active-monthly' : '' }}" id="tab-monthly">
                            <label class="type-tab-label" for="type_monthly">
                                <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Monthly
                            </label>
                            <input type="radio" id="type_monthly" name="payment_type" value="monthly" {{ old('payment_type', $payType) === 'monthly' ? 'checked' : '' }}>
                        </div>
                        <div class="type-tab {{ old('payment_type', $payType) === 'past_month' ? 'active-past_month' : '' }}" id="tab-past_month">
                            <label class="type-tab-label" for="type_past_month">
                                <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Past Month
                            </label>
                            <input type="radio" id="type_past_month" name="payment_type" value="past_month" {{ old('payment_type', $payType) === 'past_month' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                {{-- Course --}}
                <div class="pay-field">
                    <label class="pay-label" for="course_id">Course</label>
                    <select name="course_id" id="course_id" class="pay-input pay-select" required>
                        <option value="">— Select Course —</option>
                        @foreach($allCourses as $course)
                            <option value="{{ $course->id }}"
                                data-fees="{{ $course->fees }}"
                                data-past-fee="{{ $course->past_month_fee }}"
                                data-type="{{ $course->fees_type }}"
                                {{ old('course_id', $selectedCourse?->id) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                                ({{ $course->fees_type === 'monthly' ? '₹'.number_format($course->fees).'/mo' : '₹'.number_format($course->fees).' one-time' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fee preview --}}
                <div class="fee-preview" id="fee-preview">
                    <div class="fee-preview-amount" id="fee-preview-amount">₹0</div>
                    <div class="fee-preview-label" id="fee-preview-label">Suggested amount</div>
                </div>

                {{-- Amount --}}
                <div class="pay-field">
                    <label class="pay-label" for="amount">Amount Paid (₹)</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="number" name="amount" id="amount" class="pay-input" value="{{ old('amount') }}" min="1" step="0.01" placeholder="0.00" required style="flex:1;">
                        @if($upiId)
                        <a id="upi-inline-btn"
                           href="upi://pay?pa={{ urlencode($upiId) }}&pn={{ urlencode($upiName) }}&cu=INR"
                           style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#1a1a2e,#0f3460);color:#fff;border-radius:10px;padding:10px 14px;font-size:12px;font-weight:800;text-decoration:none;white-space:nowrap;flex-shrink:0;">
                            <svg style="width:18px;height:18px;" viewBox="0 0 48 48" fill="none">
                                <rect width="48" height="48" rx="10" fill="rgba(255,255,255,0.12)"/>
                                <path d="M14 24L24 10L34 24" stroke="#a5b4fc" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 34L24 20L34 34" stroke="#38bdf8" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Pay with UPI
                        </a>
                        @endif
                    </div>
                    <p class="pay-hint">Enter amount then tap <strong>Pay with UPI</strong> to open your UPI app</p>
                </div>

                {{-- 30-Day Access Info (for monthly) --}}
                <div class="month-box" id="month-box">
                    <p style="font-size:13px;font-weight:700;color:#047857;margin-bottom:8px;">Pay for 30-day access</p>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:12px;">Get full access to all course content for 30 days from payment approval date.</p>
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px;display:flex;align-items:center;gap:10px;">
                        <svg style="width:20px;height:20px;color:#22c55e;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span style="font-size:12px;color:#166534;">Access starts from today and expires after 30 days</span>
                    </div>
                    {{-- Hidden fields for backend compatibility --}}
                    <input type="hidden" name="month_number" id="month_number" value="{{ now()->month }}">
                    <input type="hidden" name="year_number" id="year_number" value="{{ now()->year }}">
                </div>

                {{-- Past Months Selection (for past_month) --}}
                <div class="past-months-box" id="past-months-box" style="display:none;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px;margin-bottom:16px;">
                    <p style="font-size:13px;font-weight:700;color:#b45309;margin-bottom:4px;">Select Past Months to Unlock</p>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:12px;">Choose the months you want to access. Each month costs the same as monthly fee.</p>
                    
                    <div id="past-months-list" style="max-height:200px;overflow-y:auto;margin-bottom:12px;">
                        {{-- Past months will be populated by JavaScript based on course selection --}}
                    </div>
                    
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:12px;color:#166534;font-weight:600;">Selected: <span id="selected-months-count">0</span> months</span>
                        <span style="font-size:14px;color:#047857;font-weight:700;" id="calculated-amount">₹0</span>
                    </div>
                    
                    <input type="hidden" name="month_number" id="past_month_number" value="">
                    <input type="hidden" name="year_number" id="past_year_number" value="">
                </div>

                {{-- Reference --}}
                <div class="pay-field">
                    <label class="pay-label" for="reference_number">Transaction / Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" class="pay-input" value="{{ old('reference_number') }}" placeholder="UTR / UPI Ref / Receipt No.">
                    <p class="pay-hint">UPI transaction ID, bank reference, or receipt number</p>
                </div>

                {{-- Screenshot --}}
                <div class="pay-field">
                    <label class="pay-label">Payment Screenshot / Receipt</label>
                    <div class="upload-box" onclick="document.getElementById('screenshot').click()">
                        <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <p id="upload-label">Tap to upload screenshot</p>
                        <small>JPG, PNG, WebP · Max 3MB</small>
                    </div>
                    <input type="file" name="screenshot" id="screenshot" accept="image/*" style="display:none" onchange="document.getElementById('upload-label').textContent = this.files[0]?.name || 'Tap to upload screenshot'">
                </div>

                {{-- Note --}}
                <div class="pay-field">
                    <label class="pay-label" for="note">Note (optional)</label>
                    <textarea name="note" id="note" class="pay-input" rows="2" placeholder="Any additional info...">{{ old('note') }}</textarea>
                </div>

                <button type="submit" class="submit-btn">Submit Payment Request</button>
            </form>
        </div>
    </div>

</div>

<script>
// Enrollment data with past months passed from controller
const enrollmentsData = @json($enrollmentsWithMonths ?? []);

document.addEventListener('DOMContentLoaded', function () {
    const radios    = document.querySelectorAll('input[name="payment_type"]');
    const tabs      = { enrollment: document.getElementById('tab-enrollment'), monthly: document.getElementById('tab-monthly'), past_month: document.getElementById('tab-past_month') };
    const monthBox  = document.getElementById('month-box');
    const pastMonthsBox = document.getElementById('past-months-box');
    const pastMonthsList = document.getElementById('past-months-list');
    const selectedCountEl = document.getElementById('selected-months-count');
    const calculatedAmountEl = document.getElementById('calculated-amount');
    const preview   = document.getElementById('fee-preview');
    const previewAmt= document.getElementById('fee-preview-amount');
    const previewLbl= document.getElementById('fee-preview-label');
    const courseSel = document.getElementById('course_id');
    const amountInp = document.getElementById('amount');
    const typeTabsContainer = document.getElementById('type-tabs');
    const enrollmentTab = document.querySelector('[data-enrollment-tab]');
    
    let selectedMonths = [];
    let currentCourseFee = 0;
    let currentPastFee = 0;

    function updateEnrollmentTabVisibility() {
        const opt = courseSel.options[courseSel.selectedIndex];
        if (!opt || !opt.value) return;
        const courseType = opt.dataset.type;
        if (courseType === 'monthly') {
            enrollmentTab.style.display = 'none';
            // If enrollment was selected, switch to monthly
            if (document.getElementById('type_enrollment').checked) {
                document.getElementById('type_monthly').checked = true;
                updateTabs('monthly');
            }
        } else {
            enrollmentTab.style.display = '';
        }
        // Adjust grid columns based on visible tabs
        const visibleTabs = Object.values(tabs).filter(t => t.style.display !== 'none').length;
        typeTabsContainer.style.gridTemplateColumns = `repeat(${visibleTabs}, 1fr)`;
    }

    function getSelectedType() {
        return document.querySelector('input[name="payment_type"]:checked')?.value || 'enrollment';
    }

    function updateTabs(type) {
        Object.keys(tabs).forEach(k => {
            tabs[k].className = 'type-tab' + (k === type ? ' active-' + k : '');
        });
        
        // Show/hide appropriate boxes
        if (type === 'monthly') {
            monthBox.className = 'month-box show';
            pastMonthsBox.style.display = 'none';
            pastMonthsBox.classList.remove('show');
        } else if (type === 'past_month') {
            monthBox.className = 'month-box';
            monthBox.style.display = 'none';
            pastMonthsBox.style.display = 'block';
            pastMonthsBox.classList.add('show');
            populatePastMonths();
        } else {
            monthBox.className = 'month-box';
            pastMonthsBox.style.display = 'none';
            pastMonthsBox.classList.remove('show');
        }
        
        updatePreview();
    }

    function populatePastMonths() {
        const courseId = parseInt(courseSel.value);
        if (!courseId) {
            pastMonthsList.innerHTML = '<p style="text-align:center;color:#9ca3af;padding:20px;">Select a course to see available months</p>';
            return;
        }

        const enrollment = enrollmentsData.find(e => e.course_id === courseId);
        if (!enrollment || !enrollment.past_months || enrollment.past_months.length === 0) {
            pastMonthsList.innerHTML = '<p style="text-align:center;color:#9ca3af;padding:20px;">No past months available</p>';
            return;
        }

        selectedMonths = [];
        pastMonthsList.innerHTML = enrollment.past_months.map(month => {
            const isPaid = month.is_paid;
            return `
                <div class="month-checkbox-item ${isPaid ? 'paid' : ''}" 
                     data-month-key="${month.month_key}" 
                     data-year="${month.year}" 
                     data-month="${month.month}"
                     ${isPaid ? '' : 'onclick="toggleMonth(this)"'}>
                    <div class="month-checkbox-left">
                        <svg class="month-check-icon" style="width:18px;height:18px;color:${isPaid ? '#9ca3af' : '#d1d5db'};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="month-checkbox-label">${month.month_name}</span>
                        ${isPaid ? '<span style="font-size:10px;color:#10b981;font-weight:600;margin-left:4px;">(Paid)</span>' : ''}
                    </div>
                    <span class="month-price">₹${currentPastFee.toLocaleString('en-IN')}</span>
                </div>
            `;
        }).join('');
        
        updatePastMonthsSummary();
    }

    window.toggleMonth = function(element) {
        const monthKey = element.dataset.monthKey;
        const isSelected = element.classList.contains('selected');
        
        if (isSelected) {
            element.classList.remove('selected');
            element.querySelector('.month-check-icon').style.color = '#d1d5db';
            selectedMonths = selectedMonths.filter(m => m !== monthKey);
        } else {
            element.classList.add('selected');
            element.querySelector('.month-check-icon').style.color = '#10b981';
            selectedMonths.push(monthKey);
        }
        
        updatePastMonthsSummary();
        
        // Update hidden inputs for form submission
        if (selectedMonths.length > 0) {
            const [firstYear, firstMonth] = selectedMonths[0].split('-');
            document.getElementById('past_month_number').value = firstMonth;
            document.getElementById('past_year_number').value = firstYear;
        }
    };

    function updatePastMonthsSummary() {
        const count = selectedMonths.length;
        const total = count * currentPastFee;
        
        selectedCountEl.textContent = count;
        calculatedAmountEl.textContent = '₹' + total.toLocaleString('en-IN');
        
        // Update amount input with calculated total (always for past_month)
        amountInp.value = total;
        
        // Update preview
        previewAmt.textContent = '₹' + total.toLocaleString('en-IN');
        previewLbl.textContent = count + ' month' + (count !== 1 ? 's' : '') + ' — calculated amount';
        preview.className = 'fee-preview show';
    }

    function updatePreview() {
        const opt = courseSel.options[courseSel.selectedIndex];
        if (!opt || !opt.value) { preview.className = 'fee-preview'; return; }
        const type     = getSelectedType();
        const fees     = parseFloat(opt.dataset.fees) || 0;
        const pastFee  = parseFloat(opt.dataset.pastFee) || 0;
        const cType    = opt.dataset.type;
        
        currentCourseFee = fees;
        currentPastFee = pastFee || fees; // Use pastFee if set, otherwise fallback to fees
        
        let amount = 0, label = '';
        if (type === 'enrollment') { 
            amount = fees; 
            label = cType === 'monthly' ? 'Monthly fee (1st month)' : 'One-time enrollment fee'; 
        }
        else if (type === 'monthly') { 
            amount = fees; 
            label = 'Monthly fee'; 
        }
        else if (type === 'past_month') { 
            // For past_month, show calculated amount based on selected months using past month fee
            const count = selectedMonths.length;
            amount = count * currentPastFee;
            label = count + ' month' + (count !== 1 ? 's' : '') + ' selected @ ₹' + currentPastFee.toLocaleString('en-IN') + '/mo';
            
            // Refresh past months list with updated prices
            if (pastMonthsBox.classList.contains('show')) {
                populatePastMonths();
            }
        }
        
        if (type !== 'past_month') {
            previewAmt.textContent = '₹' + amount.toLocaleString('en-IN');
            previewLbl.textContent = label + ' — suggested amount';
            amountInp.value = amount > 0 ? amount : '';
            preview.className = 'fee-preview show';
            amountInp.readOnly = false;
            amountInp.style.backgroundColor = '';
        } else {
            // For past_month, show base rate info when no months selected yet
            previewAmt.textContent = '₹' + currentPastFee.toLocaleString('en-IN');
            previewLbl.textContent = 'Past month fee per month — select months below';
            preview.className = 'fee-preview show';
            // Make amount read-only for past_month (calculated automatically)
            amountInp.readOnly = true;
            amountInp.style.backgroundColor = '#f3f4f6';
            // Set amount to calculated total or 0 if no months selected
            const count = selectedMonths.length;
            amountInp.value = count * currentPastFee;
        }
    }

    radios.forEach(r => r.addEventListener('change', () => updateTabs(r.value)));
    courseSel.addEventListener('change', () => { 
        updatePreview(); 
        updateEnrollmentTabVisibility();
        if (getSelectedType() === 'past_month') {
            populatePastMonths();
        }
    });

    // Init
    updateTabs(getSelectedType());
    if (courseSel.value) { 
        updatePreview(); 
        updateEnrollmentTabVisibility(); 
        if (getSelectedType() === 'past_month') {
            populatePastMonths();
        }
    }

    // Make tab divs clickable
    Object.keys(tabs).forEach(k => {
        tabs[k].addEventListener('click', function () {
            document.getElementById('type_' + k).checked = true;
            updateTabs(k);
        });
    });

    // Update UPI deep-link amount when amount field changes
    const upiBtn       = document.getElementById('upi-pay-btn');
    const upiInlineBtn = document.getElementById('upi-inline-btn');
    const upiBase = upiBtn       ? upiBtn.getAttribute('href')       : null;
    const upiInlineBase = upiInlineBtn ? upiInlineBtn.getAttribute('href') : null;

    amountInp.addEventListener('input', function () {
        const amt = parseFloat(this.value) || 0;
        const suffix = amt > 0 ? '&am=' + amt : '';
        if (upiBtn)       upiBtn.href       = upiBase       + suffix;
        if (upiInlineBtn) upiInlineBtn.href = upiInlineBase + suffix;
    });
    
    // Add hidden inputs for past months on form submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (getSelectedType() === 'past_month' && selectedMonths.length > 0) {
            // Remove any existing past_months inputs
            form.querySelectorAll('input[name="past_months[]"]').forEach(el => el.remove());
            
            // Add selected months as hidden inputs
            selectedMonths.forEach(monthKey => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'past_months[]';
                input.value = monthKey;
                form.appendChild(input);
            });
        }
    });
});

function copyUpi() {
    const text = document.getElementById('upi-id-text')?.textContent?.trim();
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const lbl = document.getElementById('copy-label');
        lbl.textContent = 'Copied!';
        setTimeout(() => lbl.textContent = 'Copy ID', 2000);
    });
}
</script>
@endsection
