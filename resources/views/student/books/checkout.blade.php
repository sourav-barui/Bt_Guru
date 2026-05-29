@extends('layouts.student_mobile')

@section('title', 'Buy Book')

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
.pay-card-header { padding: 16px 20px; background: linear-gradient(135deg, #7c3aed, #db2777); }
.pay-card-header h2 { font-size: 15px; font-weight: 700; color: #fff; }
.pay-card-header p  { font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 2px; }
.pay-card-body { padding: 20px; }
.pay-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
.pay-input  { width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 14px; color: #111827; outline: none; box-sizing: border-box; transition: border 0.2s; }
.pay-input:focus { border-color: #7c3aed; }
.pay-field { margin-bottom: 16px; }
.pay-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.upload-box { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; }
.upload-box:hover { border-color: #7c3aed; background: #f5f3ff; }
.upload-box svg { width: 28px; height: 28px; stroke: #9ca3af; margin: 0 auto 6px; display: block; }
.upload-box p { font-size: 13px; color: #6b7280; }
.upload-box small { font-size: 11px; color: #9ca3af; }
.submit-btn { display: block; width: 100%; padding: 14px; background: linear-gradient(135deg, #7c3aed, #db2777); color: #fff; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(124,58,237,0.4); }
.alert-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: #dc2626; }
.fee-preview { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #86efac; border-radius: 12px; padding: 14px; margin-bottom: 16px; }
.fee-preview-amount { font-size: 24px; font-weight: 800; color: #16a34a; }
.fee-preview-label  { font-size: 12px; color: #15803d; font-weight: 600; }
.upi-card { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%); border-radius: 18px; padding: 20px; margin-bottom: 20px; color: #fff; position: relative; overflow: hidden; }
.upi-label  { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; color: rgba(255,255,255,0.6); text-transform: uppercase; margin-bottom: 6px; }
.upi-id     { font-size: 18px; font-weight: 800; color: #fff; font-family: monospace; word-break: break-all; margin-bottom: 4px; }
.upi-name   { font-size: 12px; color: rgba(255,255,255,0.65); margin-bottom: 16px; }
.upi-btn    { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #1a1a2e; border-radius: 10px; padding: 10px 18px; font-size: 13px; font-weight: 800; text-decoration: none; cursor: pointer; border: none; }
</style>
@endpush

@section('mobile-content')
<div class="pay-page">
    <p class="pay-heading">Buy Book</p>
    <p class="pay-sub">Submit payment details for admin verification</p>

    @if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    @if($upiId)
    <div class="upi-card">
        <div style="position:relative;z-index:1;">
            <div class="upi-label">Pay Via UPI</div>
            <div class="upi-id">{{ $upiId }}</div>
            <div class="upi-name">{{ $upiName }}</div>
            <a href="upi://pay?pa={{ urlencode($upiId) }}&pn={{ urlencode($upiName) }}&am={{ $totalAmount }}&cu=INR" class="upi-btn">Pay with UPI</a>
        </div>
    </div>
    @endif

    @if($bankAccount)
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:14px;padding:14px 16px;margin-bottom:20px;">
        <p style="font-size:11px;font-weight:700;color:#16a34a;letter-spacing:1px;text-transform:uppercase;margin-bottom:10px;">Bank Transfer Details</p>
        @if($bankHolder)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #dcfce7;"><span style="font-size:11px;color:#6b7280;font-weight:600;">Account Holder</span><span style="font-size:13px;color:#111827;font-weight:700;">{{ $bankHolder }}</span></div>
        @endif
        @if($bankName)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #dcfce7;"><span style="font-size:11px;color:#6b7280;font-weight:600;">Bank</span><span style="font-size:13px;color:#111827;font-weight:700;">{{ $bankName }}</span></div>
        @endif
        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #dcfce7;"><span style="font-size:11px;color:#6b7280;font-weight:600;">Account No.</span><span style="font-size:13px;color:#111827;font-weight:700;">{{ $bankAccount }}</span></div>
        @if($bankIfsc)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;"><span style="font-size:11px;color:#6b7280;font-weight:600;">IFSC</span><span style="font-size:13px;color:#111827;font-weight:700;">{{ $bankIfsc }}</span></div>
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
            <h2>Payment Request</h2>
            <p>{{ $book->title }} — {{ ucfirst($orderType) }}</p>
        </div>
        <div class="pay-card-body">
            <form action="{{ route('student.books.purchase', $book) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_type" value="{{ $orderType }}">

                <div class="fee-preview">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div class="fee-preview-amount">₹{{ number_format($totalAmount, 2) }}</div>
                            <div class="fee-preview-label">Total Amount</div>
                        </div>
                        <div style="text-align:right;">
                            @if($pdfPrice > 0)
                                <p style="font-size:12px;color:#374151;">PDF: ₹{{ number_format($pdfPrice, 2) }}</p>
                            @endif
                            @if($physicalPrice > 0)
                                <p style="font-size:12px;color:#374151;">Physical: ₹{{ number_format($physicalPrice, 2) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if(in_array($orderType, ['physical', 'both']))
                <div class="pay-field">
                    <label class="pay-label" for="delivery_address">Delivery Address <span style="color:#dc2626;">*</span></label>
                    <textarea id="delivery_address" name="delivery_address" rows="2" class="pay-input" required>{{ old('delivery_address') }}</textarea>
                </div>
                <div class="pay-field">
                    <label class="pay-label" for="delivery_phone">Contact Phone <span style="color:#dc2626;">*</span></label>
                    <input type="tel" id="delivery_phone" name="delivery_phone" value="{{ old('delivery_phone') }}" class="pay-input" required>
                </div>
                @endif

                <div class="pay-field">
                    <label class="pay-label" for="reference_number">Transaction / Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" class="pay-input" value="{{ old('reference_number') }}" placeholder="UTR / UPI Ref / Receipt No.">
                    <p class="pay-hint">UPI transaction ID, bank reference, or receipt number</p>
                </div>

                <div class="pay-field">
                    <label class="pay-label">Payment Screenshot / Receipt</label>
                    <div class="upload-box" onclick="document.getElementById('screenshot').click()">
                        <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <p id="upload-label">Tap to upload screenshot</p>
                        <small>JPG, PNG, WebP · Max 3MB</small>
                    </div>
                    <input type="file" name="screenshot" id="screenshot" accept="image/*" style="display:none" onchange="document.getElementById('upload-label').textContent = this.files[0]?.name || 'Tap to upload screenshot'">
                </div>

                <div class="pay-field">
                    <label class="pay-label" for="note">Note (optional)</label>
                    <textarea name="note" id="note" class="pay-input" rows="2" placeholder="Any additional info...">{{ old('note') }}</textarea>
                </div>

                <button type="submit" class="submit-btn">Submit Payment Request</button>
            </form>
        </div>
    </div>

</div>
@endsection
