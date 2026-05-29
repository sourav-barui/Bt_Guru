<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review & Confirm – BT Guru Registration</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*{box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f3f4f6;min-height:100vh;}
.reg-wrap{min-height:100vh;display:flex;flex-direction:column;}
.reg-header{background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:20px 24px;display:flex;align-items:center;gap:12px;}
.reg-logo{width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;color:#fff;}
.reg-brand{font-weight:700;font-size:20px;color:#fff;}
.reg-brand span{opacity:.7;font-size:13px;font-weight:400;display:block;}
.reg-body{flex:1;display:flex;align-items:flex-start;justify-content:center;padding:32px 16px 48px;}
.reg-card{background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.10);width:100%;max-width:640px;overflow:hidden;}
.steps-bar{display:flex;padding:0;margin:0;list-style:none;border-bottom:1px solid #e5e7eb;}
.step-dot{flex:1;display:flex;flex-direction:column;align-items:center;padding:18px 8px 14px;position:relative;font-size:11px;font-weight:600;color:#9ca3af;gap:6px;}
.step-dot::after{content:'';position:absolute;right:0;top:50%;transform:translateY(-50%);width:1px;height:60%;background:#e5e7eb;}
.step-dot:last-child::after{display:none;}
.step-dot .num{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;background:#e5e7eb;color:#6b7280;}
.step-dot.done .num{background:#7c3aed;color:#fff;}
.step-dot.done{color:#7c3aed;}
.step-dot.active .num{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;box-shadow:0 0 0 4px rgba(124,58,237,.15);}
.step-dot.active{color:#7c3aed;}
.reg-form{padding:32px;}
.reg-title{font-size:22px;font-weight:800;color:#111827;margin:0 0 4px;}
.reg-subtitle{font-size:14px;color:#6b7280;margin:0 0 24px;}
.review-section{background:#f9fafb;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;margin-bottom:16px;position:relative;}
.review-section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
.review-section-head h4{font-size:12px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.7px;margin:0;display:flex;align-items:center;gap:6px;}
.review-edit{font-size:12px;color:#6b7280;text-decoration:none;font-weight:600;padding:4px 10px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;}
.review-edit:hover{background:#f3f4f6;}
.review-row{display:flex;gap:8px;font-size:13px;margin-bottom:8px;line-height:1.5;}
.review-row:last-child{margin-bottom:0;}
.review-key{color:#6b7280;min-width:130px;flex-shrink:0;}
.review-val{color:#111827;font-weight:600;word-break:break-all;}
.divider{height:1px;background:#e5e7eb;margin:20px 0;}
.terms-box{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px;margin-bottom:24px;font-size:13px;color:#92400e;}
.terms-check{display:flex;align-items:flex-start;gap:10px;font-size:13px;color:#374151;margin-top:12px;}
.terms-check input{margin-top:2px;width:16px;height:16px;accent-color:#7c3aed;flex-shrink:0;}
.btn-primary{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;padding:14px 32px;border-radius:12px;font-size:15px;font-weight:700;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:10px;}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;}
.btn-secondary{background:#f3f4f6;color:#374151;padding:13px 22px;border-radius:12px;font-size:15px;font-weight:600;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-group{display:flex;align-items:center;justify-content:space-between;gap:12px;}
.preview-url{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:12px;padding:14px 18px;text-align:center;margin-bottom:20px;}
.preview-url p{margin:0;font-size:12px;color:#166534;font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.preview-url a{font-size:18px;font-weight:800;color:#15803d;word-break:break-all;}
</style>
</head>
<body>
<div class="reg-wrap">
    <div class="reg-header">
        <div class="reg-logo">BG</div>
        <div class="reg-brand">BT Guru <span>Register Your Coaching Centre</span></div>
    </div>
    <div class="reg-body">
        <div class="reg-card">
            <ul class="steps-bar">
                @foreach([[1,'Coaching'],[2,'Contact'],[3,'Account'],[4,'Review'],[5,'Verify']] as [$n,$label])
                <li class="step-dot {{ 4==$n?'active':(4>$n?'done':'') }}">
                    <span class="num">@if(4>$n)<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else{{$n}}@endif</span>
                    <span class="hidden sm:block">{{$label}}</span>
                </li>
                @endforeach
            </ul>
            <div class="reg-form">
                <h2 class="reg-title">Review Your Details</h2>
                <p class="reg-subtitle">Step 4 of 5 — Please verify everything before submitting</p>

                {{-- Your URL preview --}}
                <div class="preview-url">
                    <p>Your coaching portal will be at</p>
                    <a>{{ $reg->get('subdomain') }}.{{ config('app.central_domain') }}</a>
                </div>

                {{-- Section 1: Coaching Info --}}
                <div class="review-section">
                    <div class="review-section-head">
                        <h4>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Coaching Info
                        </h4>
                        <a href="{{ route('tenant.register') }}" class="review-edit">Edit</a>
                    </div>
                    <div class="review-row"><span class="review-key">Centre Name</span><span class="review-val">{{ $reg->get('coaching_name') }}</span></div>
                    <div class="review-row"><span class="review-key">Subdomain</span><span class="review-val">{{ $reg->get('subdomain') }}</span></div>
                    <div class="review-row"><span class="review-key">Type</span><span class="review-val">{{ $reg->get('coaching_type') }}</span></div>
                    @if($reg->get('tagline'))
                    <div class="review-row"><span class="review-key">Tagline</span><span class="review-val">{{ $reg->get('tagline') }}</span></div>
                    @endif
                    @if($reg->get('website'))
                    <div class="review-row"><span class="review-key">Website</span><span class="review-val">{{ $reg->get('website') }}</span></div>
                    @endif
                </div>

                {{-- Section 2: Contact --}}
                <div class="review-section">
                    <div class="review-section-head">
                        <h4>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Contact Details
                        </h4>
                        <a href="{{ route('register.step2', $reg->token) }}" class="review-edit">Edit</a>
                    </div>
                    <div class="review-row"><span class="review-key">Email</span><span class="review-val">{{ $reg->get('email') }}</span></div>
                    <div class="review-row"><span class="review-key">Primary Phone</span><span class="review-val">{{ $reg->get('phone') }}</span></div>
                    @if($reg->get('phone_alt'))
                    <div class="review-row"><span class="review-key">Alt Phone</span><span class="review-val">{{ $reg->get('phone_alt') }}</span></div>
                    @endif
                    <div class="review-row"><span class="review-key">Address</span><span class="review-val">{{ $reg->get('address') }}</span></div>
                    <div class="review-row"><span class="review-key">City / State</span><span class="review-val">{{ $reg->get('city') }}, {{ $reg->get('state') }} – {{ $reg->get('pincode') }}</span></div>
                </div>

                {{-- Section 3: Admin Account --}}
                <div class="review-section">
                    <div class="review-section-head">
                        <h4>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Admin Account
                        </h4>
                        <a href="{{ route('register.step3', $reg->token) }}" class="review-edit">Edit</a>
                    </div>
                    <div class="review-row"><span class="review-key">Name</span><span class="review-val">{{ $reg->get('admin_name') }}</span></div>
                    <div class="review-row"><span class="review-key">Email</span><span class="review-val">{{ $reg->get('admin_email') }}</span></div>
                    <div class="review-row"><span class="review-key">Phone</span><span class="review-val">{{ $reg->get('admin_phone') }}</span></div>
                    <div class="review-row"><span class="review-key">Password</span><span class="review-val" style="letter-spacing:3px;">••••••••</span></div>
                </div>

                {{-- Terms --}}
                <div class="terms-box">
                    <p style="margin:0 0 4px;font-weight:700;">Before you submit:</p>
                    <ul style="margin:6px 0 0;padding-left:18px;line-height:1.7;">
                        <li>Your registration will be reviewed by BT Guru team.</li>
                        <li>Access will be granted once approved (usually within 24 hours).</li>
                        <li>An OTP will be sent to <strong>{{ $reg->get('admin_email') }}</strong> to verify your email.</li>
                    </ul>
                    <div class="terms-check">
                        <input type="checkbox" id="agreeTerms">
                        <label for="agreeTerms">I confirm all details are correct and agree to the <a href="#" style="color:#92400e;font-weight:700;">Terms of Service</a>.</label>
                    </div>
                </div>

                <form method="POST" action="{{ route('register.review', $reg->token) }}">
                    @csrf
                    <div class="btn-group">
                        <a href="{{ route('register.step3', $reg->token) }}" class="btn-secondary">← Back</a>
                        <button type="submit" class="btn-primary" id="submitBtn" disabled>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Send Verification OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('agreeTerms').addEventListener('change', function() {
    document.getElementById('submitBtn').disabled = !this.checked;
});
</script>
</body>
</html>
