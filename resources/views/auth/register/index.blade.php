<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Register Your Coaching – BT Guru</title>
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
/* Step Progress */
.steps-bar{display:flex;padding:0;margin:0;list-style:none;border-bottom:1px solid #e5e7eb;}
.step-dot{flex:1;display:flex;flex-direction:column;align-items:center;padding:18px 8px 14px;position:relative;font-size:11px;font-weight:600;color:#9ca3af;gap:6px;}
.step-dot::after{content:'';position:absolute;right:0;top:50%;transform:translateY(-50%);width:1px;height:60%;background:#e5e7eb;}
.step-dot:last-child::after{display:none;}
.step-dot .num{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;background:#e5e7eb;color:#6b7280;transition:all .3s;}
.step-dot.done .num{background:#7c3aed;color:#fff;}
.step-dot.done{color:#7c3aed;}
.step-dot.active .num{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;box-shadow:0 0 0 4px rgba(124,58,237,.15);}
.step-dot.active{color:#7c3aed;}
/* Form */
.reg-form{padding:32px;}
.reg-title{font-size:22px;font-weight:800;color:#111827;margin:0 0 4px;}
.reg-subtitle{font-size:14px;color:#6b7280;margin:0 0 28px;}
.form-group{margin-bottom:20px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-label .req{color:#ef4444;margin-left:2px;}
.form-input{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;transition:border .2s,box-shadow .2s;outline:none;}
.form-input:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1);}
.form-input.error{border-color:#ef4444;}
.error-msg{font-size:12px;color:#ef4444;margin-top:4px;}
.form-hint{font-size:12px;color:#9ca3af;margin-top:4px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:520px){.form-row{grid-template-columns:1fr;}}
.btn-primary{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;padding:13px 28px;border-radius:12px;font-size:15px;font-weight:700;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:opacity .2s;}
.btn-primary:hover{opacity:.9;}
.btn-secondary{background:#f3f4f6;color:#374151;padding:13px 22px;border-radius:12px;font-size:15px;font-weight:600;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-secondary:hover{background:#e5e7eb;}
.btn-group{display:flex;align-items:center;justify-content:space-between;margin-top:8px;gap:12px;}
/* Subdomain checker */
.subdomain-wrap{display:flex;align-items:center;border:1.5px solid #e5e7eb;border-radius:10px;overflow:hidden;transition:border .2s;}
.subdomain-wrap:focus-within{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1);}
.subdomain-input{flex:1;padding:11px 14px;border:none;outline:none;font-size:14px;min-width:0;}
.subdomain-suffix{padding:11px 14px;background:#f3f4f6;font-size:13px;color:#6b7280;border-left:1px solid #e5e7eb;white-space:nowrap;}
.subdomain-status{display:flex;align-items:center;gap:4px;font-size:12px;font-weight:600;margin-top:5px;}
.subdomain-status.ok{color:#16a34a;}
.subdomain-status.err{color:#ef4444;}
/* Review card */
.review-section{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:18px 20px;margin-bottom:16px;}
.review-section h4{font-size:13px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.6px;margin:0 0 12px;}
.review-row{display:flex;gap:8px;margin-bottom:8px;font-size:13px;}
.review-row:last-child{margin-bottom:0;}
.review-key{color:#6b7280;min-width:140px;flex-shrink:0;}
.review-val{color:#111827;font-weight:600;word-break:break-all;}
/* OTP */
.otp-wrap{display:flex;gap:10px;justify-content:center;margin:20px 0;}
.otp-digit{width:52px;height:60px;border:2px solid #e5e7eb;border-radius:12px;text-align:center;font-size:26px;font-weight:700;color:#7c3aed;outline:none;transition:border .2s;}
.otp-digit:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.15);}
.otp-hidden{position:absolute;opacity:0;pointer-events:none;}
/* Success banner */
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
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

            <!-- Step Progress Bar -->
            <ul class="steps-bar">
                @foreach([[1,'Coaching'],[2,'Contact'],[3,'Account'],[4,'Review'],[5,'Verify']] as [$n,$label])
                <li class="step-dot {{ 1==$n?'active':(1>$n?'done':'') }}">
                    <span class="num">@if(1>$n)<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else{{$n}}@endif</span>
                    <span class="hidden sm:block">{{ $label }}</span>
                </li>
                @endforeach
            </ul>

            <div class="reg-form">

                @if(session('success'))
                <div class="alert-success">✓ {{ session('success') }}</div>
                @endif

                @if($errors->any())
                <div class="alert-error">
                    <ul style="margin:0;padding-left:16px;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <!-- STEP 1 CONTENT -->
                <h2 class="reg-title">Tell us about your Coaching Centre</h2>
                <p class="reg-subtitle">Step 1 of 5 — Basic information</p>

                <form method="POST" action="{{ route('register.step1') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Coaching Centre Name <span class="req">*</span></label>
                        <input type="text" name="coaching_name" class="form-input {{ $errors->has('coaching_name') ? 'error' : '' }}"
                               value="{{ old('coaching_name') }}" placeholder="e.g. Future Academy" autofocus>
                        @error('coaching_name')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Your URL / Subdomain <span class="req">*</span></label>
                        <div class="subdomain-wrap {{ $errors->has('subdomain') ? 'error' : '' }}" id="sdWrap">
                            <input type="text" name="subdomain" id="subdomain" class="subdomain-input"
                                   value="{{ old('subdomain') }}" placeholder="futureacademy"
                                   autocomplete="off" maxlength="50">
                            <span class="subdomain-suffix">.{{ config('app.central_domain') }}</span>
                        </div>
                        <div class="subdomain-status" id="sdStatus" style="display:none;"></div>
                        @error('subdomain')<p class="error-msg">{{ $message }}</p>@enderror
                        <p class="form-hint">Only letters, numbers, hyphens. This cannot be changed later.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Coaching Type <span class="req">*</span></label>
                        <select name="coaching_type" class="form-input {{ $errors->has('coaching_type') ? 'error' : '' }}">
                            <option value="">-- Select Type --</option>
                            @foreach([
                                'School Tuition','College Coaching','Competitive Exam (IIT/JEE/NEET)',
                                'Government Exam (SSC/UPSC/Banking)','Language Institute','Music / Arts / Sports',
                                'Skill Development','IT / Coding','Pre-School / Playgroup','Other',
                            ] as $type)
                            <option value="{{ $type }}" {{ old('coaching_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('coaching_type')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tagline / Slogan <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
                        <input type="text" name="tagline" class="form-input" value="{{ old('tagline') }}"
                               placeholder="e.g. Empowering students to achieve more">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Website <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
                        <input type="url" name="website" class="form-input" value="{{ old('website') }}"
                               placeholder="https://youracademy.com">
                    </div>

                    <div class="btn-group">
                        <a href="{{ route('home') }}" class="btn-secondary">← Back to Home</a>
                        <button type="submit" class="btn-primary">
                            Continue <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// Subdomain live check
let sdTimer;
const sdInput = document.getElementById('subdomain');
const sdStatus = document.getElementById('sdStatus');
const sdWrap = document.getElementById('sdWrap');

function slugify(v) {
    return v.toLowerCase().replace(/[^a-z0-9\-_]/g, '').replace(/^[\-_]+|[\-_]+$/g, '');
}

sdInput.addEventListener('input', () => {
    const raw = sdInput.value;
    const slug = slugify(raw);
    if (slug !== raw) sdInput.value = slug;

    clearTimeout(sdTimer);
    sdStatus.style.display = 'none';
    if (slug.length < 3) return;

    sdTimer = setTimeout(() => {
        sdStatus.style.display = 'flex';
        sdStatus.className = 'subdomain-status';
        sdStatus.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Checking…';

        fetch('{{ route("register.check_subdomain") }}?subdomain=' + encodeURIComponent(slug))
            .then(r => r.json())
            .then(d => {
                sdStatus.style.display = 'flex';
                sdStatus.className = 'subdomain-status ' + (d.available ? 'ok' : 'err');
                sdStatus.innerHTML = d.available
                    ? '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> ' + d.message
                    : '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> ' + d.message;
            });
    }, 500);
});
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
</body>
</html>
