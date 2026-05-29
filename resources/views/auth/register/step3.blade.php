<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Account – BT Guru Registration</title>
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
.reg-subtitle{font-size:14px;color:#6b7280;margin:0 0 28px;}
.form-group{margin-bottom:20px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-label .req{color:#ef4444;margin-left:2px;}
.form-input{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;transition:border .2s;outline:none;}
.form-input:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1);}
.form-input.is-error{border-color:#ef4444;}
.error-msg{font-size:12px;color:#ef4444;margin-top:4px;}
.form-hint{font-size:12px;color:#9ca3af;margin-top:4px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:520px){.form-row{grid-template-columns:1fr;}}
.pwd-wrap{position:relative;}
.pwd-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;}
.pwd-strength{height:4px;border-radius:4px;margin-top:8px;transition:all .3s;background:#e5e7eb;}
.pwd-strength-bar{height:100%;border-radius:4px;width:0;transition:all .3s;}
.pwd-checks{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;}
.pwd-check{font-size:11px;padding:3px 8px;border-radius:20px;background:#f3f4f6;color:#9ca3af;transition:all .2s;}
.pwd-check.ok{background:#dcfce7;color:#16a34a;}
.btn-primary{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;padding:13px 28px;border-radius:12px;font-size:15px;font-weight:700;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;}
.btn-secondary{background:#f3f4f6;color:#374151;padding:13px 22px;border-radius:12px;font-size:15px;font-weight:600;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-group{display:flex;align-items:center;justify-content:space-between;margin-top:8px;gap:12px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
.info-box{background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:12px 16px;font-size:13px;color:#0369a1;margin-bottom:24px;display:flex;gap:10px;align-items:flex-start;}
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
                <li class="step-dot {{ 3==$n?'active':(3>$n?'done':'') }}">
                    <span class="num">@if(3>$n)<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else{{$n}}@endif</span>
                    <span class="hidden sm:block">{{$label}}</span>
                </li>
                @endforeach
            </ul>
            <div class="reg-form">
                @if($errors->any())
                <div class="alert-error"><ul style="margin:0;padding-left:16px;">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
                @endif

                <h2 class="reg-title">Admin Account Setup</h2>
                <p class="reg-subtitle">Step 3 of 5 — Create the primary administrator account for <strong>{{ $reg->get('coaching_name') }}</strong></p>

                <div class="info-box">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>This account will be the <strong>Tenant Admin</strong> — full control over courses, students, teachers, fees, and settings.</span>
                </div>

                <form method="POST" action="{{ route('register.step3', $reg->token) }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="admin_name" class="form-input {{ $errors->has('admin_name')?'is-error':'' }}"
                                   value="{{ old('admin_name', $reg->get('admin_name')) }}" placeholder="Your full name">
                            @error('admin_name')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mobile Number <span class="req">*</span></label>
                            <input type="text" name="admin_phone" class="form-input {{ $errors->has('admin_phone')?'is-error':'' }}"
                                   value="{{ old('admin_phone', $reg->get('admin_phone')) }}" placeholder="+91 98765 43210">
                            @error('admin_phone')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address <span class="req">*</span></label>
                        <input type="email" name="admin_email" class="form-input {{ $errors->has('admin_email')?'is-error':'' }}"
                               value="{{ old('admin_email', $reg->get('admin_email')) }}" placeholder="you@example.com">
                        @error('admin_email')<p class="error-msg">{{ $message }}</p>@enderror
                        <p class="form-hint">OTP for email verification will be sent here.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password <span class="req">*</span></label>
                        <div class="pwd-wrap">
                            <input type="password" name="password" id="pwd" class="form-input {{ $errors->has('password')?'is-error':'' }}"
                                   placeholder="Create a strong password" autocomplete="new-password">
                            <button type="button" class="pwd-toggle" onclick="togglePwd('pwd','eyePwd')">
                                <svg id="eyePwd" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <div class="pwd-strength"><div class="pwd-strength-bar" id="pwdBar"></div></div>
                        <div class="pwd-checks">
                            <span class="pwd-check" id="ck-len">8+ chars</span>
                            <span class="pwd-check" id="ck-upper">Uppercase</span>
                            <span class="pwd-check" id="ck-lower">Lowercase</span>
                            <span class="pwd-check" id="ck-num">Number</span>
                        </div>
                        @error('password')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password <span class="req">*</span></label>
                        <div class="pwd-wrap">
                            <input type="password" name="password_confirmation" id="pwd2" class="form-input"
                                   placeholder="Repeat your password" autocomplete="new-password">
                            <button type="button" class="pwd-toggle" onclick="togglePwd('pwd2','eyePwd2')">
                                <svg id="eyePwd2" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <p class="error-msg" id="pwdMismatch" style="display:none;">Passwords do not match.</p>
                    </div>

                    <div class="btn-group">
                        <a href="{{ route('register.step2', $reg->token) }}" class="btn-secondary">← Back</a>
                        <button type="submit" class="btn-primary" id="submitBtn">Continue <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function togglePwd(id, eyeId) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

const pwd = document.getElementById('pwd');
const pwd2 = document.getElementById('pwd2');
const bar = document.getElementById('pwdBar');
const colors = ['#ef4444','#f97316','#eab308','#22c55e'];

pwd.addEventListener('input', () => {
    const v = pwd.value;
    const len = v.length >= 8;
    const upper = /[A-Z]/.test(v);
    const lower = /[a-z]/.test(v);
    const num = /\d/.test(v);
    const score = [len, upper, lower, num].filter(Boolean).length;

    document.getElementById('ck-len').className = 'pwd-check' + (len ? ' ok' : '');
    document.getElementById('ck-upper').className = 'pwd-check' + (upper ? ' ok' : '');
    document.getElementById('ck-lower').className = 'pwd-check' + (lower ? ' ok' : '');
    document.getElementById('ck-num').className = 'pwd-check' + (num ? ' ok' : '');

    bar.style.width = (score * 25) + '%';
    bar.style.background = colors[score - 1] || '#e5e7eb';
});

pwd2.addEventListener('input', () => {
    const mismatch = document.getElementById('pwdMismatch');
    mismatch.style.display = pwd2.value && pwd.value !== pwd2.value ? 'block' : 'none';
});
</script>
</body>
</html>
