<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Email Verification – BT Guru Registration</title>
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
.reg-card{background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.10);width:100%;max-width:480px;overflow:hidden;}
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
.reg-title{font-size:22px;font-weight:800;color:#111827;margin:0 0 4px;text-align:center;}
.reg-subtitle{font-size:14px;color:#6b7280;margin:0 0 28px;text-align:center;line-height:1.6;}
.email-badge{background:#f3e8ff;border-radius:10px;padding:10px 16px;text-align:center;font-size:14px;color:#7c3aed;font-weight:700;margin-bottom:28px;word-break:break-all;}
.otp-wrap{display:flex;gap:10px;justify-content:center;margin:0 0 8px;}
.otp-digit{width:54px;height:64px;border:2px solid #e5e7eb;border-radius:12px;text-align:center;font-size:28px;font-weight:700;color:#7c3aed;outline:none;transition:border .2s,box-shadow .2s;}
.otp-digit:focus{border-color:#7c3aed;box-shadow:0 0 0 4px rgba(124,58,237,.12);}
.otp-digit.filled{border-color:#7c3aed;background:#faf5ff;}
.otp-digit.error-digit{border-color:#ef4444 !important;background:#fef2f2;}
.btn-primary{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;padding:14px;border-radius:12px;font-size:15px;font-weight:700;border:none;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:20px;}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;}
.resend-wrap{text-align:center;margin-top:16px;}
.resend-btn{background:none;border:none;color:#7c3aed;font-size:13px;font-weight:600;cursor:pointer;padding:6px;}
.resend-btn:disabled{color:#9ca3af;cursor:default;}
.timer{font-size:13px;color:#6b7280;margin-left:4px;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;text-align:center;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;text-align:center;}
.verify-icon{width:72px;height:72px;background:linear-gradient(135deg,#f3e8ff,#e9d5ff);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;}
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
                <li class="step-dot {{ 5==$n?'active':(5>$n?'done':'') }}">
                    <span class="num">@if(5>$n)<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else{{$n}}@endif</span>
                    <span class="hidden sm:block">{{$label}}</span>
                </li>
                @endforeach
            </ul>

            <div class="reg-form">
                @if(session('success'))
                <div class="alert-success">✓ {{ session('success') }}</div>
                @endif
                @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
                @endif

                <div class="verify-icon">
                    <svg width="36" height="36" fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h2 class="reg-title">Verify Your Email</h2>
                <p class="reg-subtitle">Step 5 of 5 — We've sent a 6-digit OTP to</p>

                <div class="email-badge">{{ $reg->get('admin_email') }}</div>

                <form method="POST" action="{{ route('register.verify.post', $reg->token) }}" id="otpForm">
                    @csrf

                    {{-- Hidden real input for form submission --}}
                    <input type="hidden" name="otp" id="otpValue">

                    <p style="text-align:center;font-size:13px;color:#6b7280;margin-bottom:12px;font-weight:600;">Enter OTP</p>
                    <div class="otp-wrap" id="otpBoxes">
                        @for($i = 0; $i < 6; $i++)
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]"
                               id="otp{{ $i }}" data-idx="{{ $i }}" autocomplete="off">
                        @endfor
                    </div>

                    <p style="text-align:center;font-size:11px;color:#9ca3af;margin-top:6px;">Valid for 15 minutes</p>

                    <button type="submit" class="btn-primary" id="verifyBtn" disabled>
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verify & Complete Registration
                    </button>
                </form>

                <div class="resend-wrap">
                    <form method="POST" action="{{ route('register.resend_otp', $reg->token) }}" id="resendForm" style="display:inline;">
                        @csrf
                        <button type="submit" class="resend-btn" id="resendBtn" disabled>
                            Resend OTP <span class="timer" id="timer">(60s)</span>
                        </button>
                    </form>
                </div>

                <div style="text-align:center;margin-top:20px;">
                    <a href="{{ route('register.review', $reg->token) }}" style="font-size:13px;color:#6b7280;">← Go back & change email</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// OTP boxes
const digits = Array.from(document.querySelectorAll('.otp-digit'));
const otpValue = document.getElementById('otpValue');
const verifyBtn = document.getElementById('verifyBtn');

function collectOtp() {
    return digits.map(d => d.value).join('');
}

function updateSubmit() {
    const val = collectOtp();
    otpValue.value = val;
    verifyBtn.disabled = val.length < 6;
}

digits.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
        const val = e.target.value.replace(/\D/g, '');
        e.target.value = val.slice(0, 1);
        if (val && idx < 5) digits[idx + 1].focus();
        e.target.classList.toggle('filled', !!val);
        updateSubmit();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace') {
            if (!input.value && idx > 0) {
                digits[idx - 1].focus();
                digits[idx - 1].value = '';
                digits[idx - 1].classList.remove('filled');
                updateSubmit();
            }
        }
        if (e.key === 'ArrowLeft' && idx > 0) digits[idx - 1].focus();
        if (e.key === 'ArrowRight' && idx < 5) digits[idx + 1].focus();
    });

    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        text.split('').slice(0, 6).forEach((ch, i) => {
            if (digits[i]) {
                digits[i].value = ch;
                digits[i].classList.add('filled');
            }
        });
        updateSubmit();
        const next = Math.min(text.length, 5);
        digits[next].focus();
    });
});

// Mark error digits if validation failed
@if($errors->has('otp'))
digits.forEach(d => d.classList.add('error-digit'));
@endif

// Countdown resend timer
let seconds = 60;
const timerEl = document.getElementById('timer');
const resendBtn = document.getElementById('resendBtn');

const interval = setInterval(() => {
    seconds--;
    timerEl.textContent = '(' + seconds + 's)';
    if (seconds <= 0) {
        clearInterval(interval);
        timerEl.textContent = '';
        resendBtn.disabled = false;
    }
}, 1000);

// Focus first digit
digits[0].focus();
</script>
</body>
</html>
