@extends('layouts.admin')

@section('page-title', 'System Settings')

@section('page-content')

{{-- Status alerts --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl text-sm font-medium">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl text-sm font-medium">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── LEFT: SMTP Form ─────────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-6">

        <form method="POST" action="{{ route('admin.settings.update') }}" id="smtpForm">
            @csrf

            {{-- Card: Email Driver --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-content-center flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Email Configuration</h3>
                        <p class="text-xs text-gray-500">Used for OTP verification emails and tenant notification emails</p>
                    </div>

                    {{-- Live status badge --}}
                    @if(\App\Models\SystemSetting::isMailConfigured())
                    <span class="ml-auto inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Configured
                    </span>
                    @else
                    <span class="ml-auto inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Not Configured
                    </span>
                    @endif
                </div>

                <div class="p-6 space-y-5">

                    {{-- Driver + Encryption row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Mail Driver <span class="text-red-500">*</span>
                            </label>
                            <select name="mail_driver" id="mailDriver"
                                    class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    onchange="toggleSmtpFields()">
                                @foreach(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'log' => 'Log (Testing)'] as $val => $label)
                                <option value="{{ $val }}" {{ ($settings['mail_driver'] ?? 'smtp') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Encryption</label>
                            <select name="mail_encryption"
                                    class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="starttls" {{ ($settings['mail_encryption'] ?? '') == 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                                <option value="" {{ ($settings['mail_encryption'] ?? '') === '' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>

                    {{-- SMTP fields (hidden for non-SMTP drivers) --}}
                    <div id="smtpFields">
                        {{-- Host + Port --}}
                        <div class="grid grid-cols-3 gap-4 mb-5">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    SMTP Host <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="mail_host"
                                       value="{{ $settings['mail_host'] ?? '' }}"
                                       placeholder="smtp.gmail.com"
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('mail_host') border-red-400 @enderror">
                                @error('mail_host')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Port <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="mail_port"
                                       value="{{ $settings['mail_port'] ?? '587' }}"
                                       placeholder="587"
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('mail_port') border-red-400 @enderror">
                                @error('mail_port')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Username + Password --}}
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    SMTP Username <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="mail_username"
                                       value="{{ $settings['mail_username'] ?? '' }}"
                                       placeholder="your@email.com"
                                       autocomplete="off"
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('mail_username') border-red-400 @enderror">
                                @error('mail_username')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    SMTP Password
                                    @if(!empty($settings['mail_password']))
                                    <span class="text-green-600 text-xs font-normal ml-1">✓ saved</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="password" name="mail_password" id="mailPassword"
                                           placeholder="{{ !empty($settings['mail_password']) ? '(leave blank to keep current)' : 'App password or SMTP password' }}"
                                           autocomplete="new-password"
                                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" onclick="togglePwd()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- From Address + From Name --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                From Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="mail_from_address"
                                   value="{{ $settings['mail_from_address'] ?? '' }}"
                                   placeholder="noreply@btguru.in"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('mail_from_address') border-red-400 @enderror">
                            @error('mail_from_address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                From Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="mail_from_name"
                                   value="{{ $settings['mail_from_name'] ?? 'BT Guru' }}"
                                   placeholder="BT Guru"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('mail_from_name') border-red-400 @enderror">
                            @error('mail_from_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            ['Gmail','smtp.gmail.com','587','tls'],
                            ['Zoho','smtp.zoho.in','587','tls'],
                            ['Outlook','smtp-mail.outlook.com','587','starttls'],
                            ['Mailgun','smtp.mailgun.org','587','tls'],
                            ['SES','email-smtp.us-east-1.amazonaws.com','587','tls'],
                        ] as [$name,$host,$port,$enc])
                        <button type="button"
                                onclick="fillPreset('{{ $host }}','{{ $port }}','{{ $enc }}')"
                                class="text-xs px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:border-indigo-400 hover:text-indigo-600 transition font-medium">
                            {{ $name }}
                        </button>
                        @endforeach
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Settings
                    </button>
                </div>
            </div>

        </form>

        {{-- ── Diagnose + Test Email ─────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Connection Diagnostics & Test Email</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Diagnose connection issues step-by-step, then send a test email</p>
                </div>
            </div>
            <div class="p-6 space-y-4">

                {{-- Diagnose button --}}
                <div>
                    <button type="button" id="diagnoseBtn" onclick="runDiagnose()"
                            class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 text-amber-700 hover:bg-amber-100 text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Diagnose SMTP Connection
                    </button>
                    <p class="text-xs text-gray-400 mt-1.5">Tests DNS, TCP connection, TLS handshake and authentication using saved settings</p>
                </div>

                {{-- Diagnose result panel --}}
                <div id="diagnoseResult" class="hidden">
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div id="diagnoseHeader" class="px-4 py-2.5 text-xs font-bold flex items-center gap-2"></div>
                        <ul id="diagnoseSteps" class="divide-y divide-gray-100 text-sm"></ul>
                        <div id="diagnoseAdvice" class="hidden px-4 py-3 bg-amber-50 border-t border-amber-100 text-xs text-amber-800"></div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Send test email --}}
                @if(\App\Models\SystemSetting::isMailConfigured())
                <form method="POST" action="{{ route('admin.settings.test_mail') }}">
                    @csrf
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Send test email to</label>
                    <div class="flex gap-3">
                        <input type="email" name="test_email"
                               value="{{ old('test_email', \App\Models\SystemSetting::get('mail_from_address')) }}"
                               placeholder="recipient@example.com"
                               class="flex-1 border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-white border border-indigo-300 text-indigo-600 hover:bg-indigo-50 text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send Test
                        </button>
                    </div>
                    @error('test_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </form>
                @else
                <p class="text-sm text-gray-400">Save SMTP settings first to enable test email.</p>
                @endif

            </div>
        </div>

    </div>

    {{-- ── RIGHT: Info + Current Config ───────────────────────────── --}}
    <div class="space-y-5">

        {{-- Current config summary --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h4 class="text-sm font-bold text-gray-800">Current Configuration</h4>
            </div>
            <div class="p-5 space-y-3 text-sm">
                @php $keys = ['mail_driver','mail_host','mail_port','mail_username','mail_encryption','mail_from_address','mail_from_name']; @endphp
                @foreach($keys as $k)
                <div class="flex items-start gap-2">
                    <span class="text-gray-400 min-w-[110px] text-xs pt-0.5">{{ str_replace('mail_','',ucwords(str_replace('_',' ',$k))) }}</span>
                    <span class="font-semibold text-gray-800 break-all text-xs">
                        {{ !empty($settings[$k]) ? $settings[$k] : '—' }}
                    </span>
                </div>
                @endforeach
                <div class="flex items-start gap-2">
                    <span class="text-gray-400 min-w-[110px] text-xs pt-0.5">Password</span>
                    <span class="font-semibold text-gray-800 text-xs">
                        {{ !empty($settings['mail_password']) ? '••••••••' : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Where is this used --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h4 class="text-sm font-bold text-gray-800">Where this email is used</h4>
            </div>
            <ul class="p-5 space-y-3">
                @foreach([
                    ['Registration OTP','6-digit OTP sent to new coaching admin during onboarding','indigo'],
                    ['Welcome Email','Sent after successful registration confirming details','green'],
                    ['Tenant Activation','Notify tenant when super admin activates their account','blue'],
                ] as [$title,$desc,$color])
                <li class="flex gap-3">
                    <div class="w-7 h-7 rounded-lg bg-{{ $color }}-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $title }}</p>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Common SMTP tips --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <p class="text-sm font-bold text-amber-900 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tips
            </p>
            <ul class="text-xs text-amber-800 space-y-1.5 list-disc list-inside leading-relaxed">
                <li>For Gmail, use an <strong>App Password</strong> (not your account password). Enable 2FA first.</li>
                <li>For Zoho, use port <strong>465 (SSL)</strong> or <strong>587 (TLS)</strong>.</li>
                <li>For Mailgun, use your Mailgun SMTP credentials from the dashboard.</li>
                <li>Use <strong>Log</strong> driver for local testing — emails go to <code>storage/logs/laravel.log</code>.</li>
            </ul>
        </div>

    </div>
</div>

@push('scripts')
<script>
function toggleSmtpFields() {
    const driver = document.getElementById('mailDriver').value;
    const fields = document.getElementById('smtpFields');
    fields.style.display = (driver === 'log' || driver === 'sendmail') ? 'none' : 'block';
}

function fillPreset(host, port, enc) {
    document.querySelector('[name="mail_host"]').value = host;
    document.querySelector('[name="mail_port"]').value = port;
    const encSel = document.querySelector('[name="mail_encryption"]');
    for (let opt of encSel.options) {
        opt.selected = opt.value === enc;
    }
}

function togglePwd() {
    const pwd = document.getElementById('mailPassword');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}

async function runDiagnose() {
    const btn = document.getElementById('diagnoseBtn');
    const result = document.getElementById('diagnoseResult');
    const header = document.getElementById('diagnoseHeader');
    const stepsList = document.getElementById('diagnoseSteps');
    const advice = document.getElementById('diagnoseAdvice');

    // Collect current form values
    const host       = document.querySelector('[name="mail_host"]')?.value?.trim();
    const port       = document.querySelector('[name="mail_port"]')?.value?.trim();
    const username   = document.querySelector('[name="mail_username"]')?.value?.trim();
    const password   = document.querySelector('[name="mail_password"]')?.value?.trim();
    const encryption = document.querySelector('[name="mail_encryption"]')?.value?.trim();

    if (!host || !port || !username) {
        alert('Please fill in SMTP Host, Port and Username before diagnosing.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Running...';
    result.classList.remove('hidden');
    stepsList.innerHTML = '';
    advice.classList.add('hidden');
    header.className = 'px-4 py-2.5 text-xs font-bold flex items-center gap-2 bg-gray-50 text-gray-600';
    header.innerHTML = 'Testing connection…';

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch('{{ route("admin.settings.diagnose_mail") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ host, port: parseInt(port), username, password, encryption })
        });
        const data = await res.json();

        // Render steps
        (data.steps || []).forEach(step => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-3 px-4 py-2.5';
            li.innerHTML = `
                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center ${step.ok ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                    ${step.ok
                        ? '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                        : '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>'
                    }
                </span>
                <div>
                    <p class="text-sm font-semibold ${step.ok ? 'text-gray-800' : 'text-red-700'}">${step.label}</p>
                    <p class="text-xs text-gray-500 leading-relaxed mt-0.5">${step.detail}</p>
                </div>`;
            stepsList.appendChild(li);
        });

        if (data.ok) {
            header.className = 'px-4 py-2.5 text-xs font-bold flex items-center gap-2 bg-green-50 text-green-700';
            header.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> All checks passed — SMTP is correctly configured';
        } else {
            header.className = 'px-4 py-2.5 text-xs font-bold flex items-center gap-2 bg-red-50 text-red-700';
            header.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Connection issue detected — see details below';

            // Show 535-specific advice
            const lastStep = (data.steps || []).slice(-1)[0];
            if (lastStep && lastStep.detail && lastStep.detail.includes('535')) {
                advice.classList.remove('hidden');
                advice.innerHTML = `<strong>535 Authentication Failed — common fixes:</strong><ul class="mt-1.5 space-y-1 list-disc list-inside">
                    <li>For <strong>cPanel / Webhosting</strong>: Login to cPanel → Email Accounts → click <em>Connect Devices</em> next to <code>${username}</code> to get the exact host and port.</li>
                    <li>The password must be the <strong>mailbox password</strong> set in cPanel, not your cPanel login password.</li>
                    <li>For <strong>Gmail</strong>: Enable 2FA → create an App Password at <a href="https://myaccount.google.com/apppasswords" target="_blank" class="underline">myaccount.google.com/apppasswords</a>.</li>
                    <li>Try changing port: <button type="button" onclick="fillPreset(document.querySelector('[name=mail_host]').value,'465','ssl')" class="underline">465 / SSL</button> or <button type="button" onclick="fillPreset(document.querySelector('[name=mail_host]').value,'587','tls')" class="underline">587 / TLS</button>.</li>
                </ul>`;
            }
        }
    } catch (e) {
        header.className = 'px-4 py-2.5 text-xs font-bold flex items-center gap-2 bg-red-50 text-red-700';
        header.innerHTML = 'Request failed: ' + e.message;
    }

    btn.disabled = false;
    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> Diagnose Again';
}

// Init
toggleSmtpFields();
</script>
@endpush

@endsection
