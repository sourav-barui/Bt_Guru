<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Details – BT Guru Registration</title>
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
.step-dot .num{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;background:#e5e7eb;color:#6b7280;transition:all .3s;}
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
.form-input{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;transition:border .2s,box-shadow .2s;outline:none;}
.form-input:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1);}
.form-input.is-error{border-color:#ef4444;}
.error-msg{font-size:12px;color:#ef4444;margin-top:4px;}
.form-hint{font-size:12px;color:#9ca3af;margin-top:4px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
@media(max-width:520px){.form-row,.form-row-3{grid-template-columns:1fr;}}
.btn-primary{background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;padding:13px 28px;border-radius:12px;font-size:15px;font-weight:700;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;}
.btn-secondary{background:#f3f4f6;color:#374151;padding:13px 22px;border-radius:12px;font-size:15px;font-weight:600;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-group{display:flex;align-items:center;justify-content:space-between;margin-top:8px;gap:12px;}
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
            <ul class="steps-bar">
                @foreach([[1,'Coaching'],[2,'Contact'],[3,'Account'],[4,'Review'],[5,'Verify']] as [$n,$label])
                <li class="step-dot {{ 2==$n?'active':(2>$n?'done':'') }}">
                    <span class="num">@if(2>$n)<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else{{$n}}@endif</span>
                    <span class="hidden sm:block">{{$label}}</span>
                </li>
                @endforeach
            </ul>
            <div class="reg-form">
                @if($errors->any())
                <div class="alert-error"><ul style="margin:0;padding-left:16px;">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
                @endif

                <h2 class="reg-title">Contact Details</h2>
                <p class="reg-subtitle">Step 2 of 5 — How can students & parents reach you?</p>

                <form method="POST" action="{{ route('register.step2', $reg->token) }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Official Email Address <span class="req">*</span></label>
                        <input type="email" name="email" class="form-input {{ $errors->has('email')?'is-error':'' }}"
                               value="{{ old('email', $reg->get('email')) }}" placeholder="info@youracademy.com">
                        @error('email')<p class="error-msg">{{ $message }}</p>@enderror
                        <p class="form-hint">Used for official communications. Must be unique.</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Primary Phone <span class="req">*</span></label>
                            <input type="text" name="phone" class="form-input {{ $errors->has('phone')?'is-error':'' }}"
                                   value="{{ old('phone', $reg->get('phone')) }}" placeholder="+91 98765 43210">
                            @error('phone')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alternate Phone <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
                            <input type="text" name="phone_alt" class="form-input"
                                   value="{{ old('phone_alt', $reg->get('phone_alt')) }}" placeholder="+91 91234 56789">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Street / Building Address <span class="req">*</span></label>
                        <textarea name="address" class="form-input {{ $errors->has('address')?'is-error':'' }}" rows="2"
                                  placeholder="Building no., street name, area…">{{ old('address', $reg->get('address')) }}</textarea>
                        @error('address')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label">City <span class="req">*</span></label>
                            <input type="text" name="city" class="form-input {{ $errors->has('city')?'is-error':'' }}"
                                   value="{{ old('city', $reg->get('city')) }}" placeholder="Kolkata">
                            @error('city')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">State <span class="req">*</span></label>
                            <select name="state" class="form-input {{ $errors->has('state')?'is-error':'' }}">
                                <option value="">Select</option>
                                @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu & Kashmir','Ladakh','Puducherry','Chandigarh','Other'] as $s)
                                <option value="{{ $s }}" {{ old('state',$reg->get('state'))==$s?'selected':'' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('state')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pincode <span class="req">*</span></label>
                            <input type="text" name="pincode" class="form-input {{ $errors->has('pincode')?'is-error':'' }}"
                                   value="{{ old('pincode', $reg->get('pincode')) }}" placeholder="700001" maxlength="10">
                            @error('pincode')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="btn-group">
                        <a href="{{ route('tenant.register') }}" class="btn-secondary">← Back</a>
                        <button type="submit" class="btn-primary">Continue <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
