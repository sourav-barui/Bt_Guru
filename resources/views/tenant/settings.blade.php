@extends('layouts.tenant')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('page-content')
@php
    $s = $tenant->settings ?? [];
    $activeTab = session('active_tab', request('tab', 'system'));
@endphp

<div class="max-w-3xl">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 font-medium text-sm mb-5 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-red-700 text-sm mb-5">
        <ul class="list-disc ml-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6" id="settings-tabs">
        @foreach([
            ['system',  'System',       'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['contact', 'Contact',      'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
            ['social',  'Social Media', 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
            ['payment', 'Payment',      'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['email',   'Email',        'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['whatsapp','WhatsApp',     'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a9.987 9.987 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z M12 2a10 10 0 100 20A10 10 0 0012 2z'],
            ['branding','Branding',       'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
        ] as [$tab, $label, $path])
        <button type="button" onclick="switchTab('{{ $tab }}')"
            id="tab-btn-{{ $tab }}"
            class="tab-btn flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-sm font-medium transition-all
                   {{ $activeTab === $tab ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"></path></svg>
            <span class="hidden sm:inline">{{ $label }}</span>
        </button>
        @endforeach
    </div>

    <form action="{{ route('tenant.settings.update') }}" method="POST" id="settings-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="active_tab" id="active_tab_input" value="{{ $activeTab }}">

        {{-- ===== SYSTEM SETTINGS ===== --}}
        <div id="tab-system" class="tab-panel {{ $activeTab !== 'system' ? 'hidden' : '' }} space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Coaching Centre Identity</h3>
                        <p class="text-xs text-gray-500">Basic information shown across the platform</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Coaching Centre Name</label>
                            <input type="text" name="coaching_name" class="form-input"
                                   value="{{ old('coaching_name', $tenant->coaching_name) }}"
                                   placeholder="e.g. Future Academy">
                            <p class="text-xs text-gray-400 mt-1">Displayed in header & student portal</p>
                        </div>
                        <div>
                            <label class="form-label">Tagline / Slogan</label>
                            <input type="text" name="tagline" class="form-input"
                                   value="{{ old('tagline', $s['tagline'] ?? '') }}"
                                   placeholder="e.g. Empowering Students Since 2020">
                        </div>
                        <div>
                            <label class="form-label">Official Email</label>
                            <input type="email" name="email" class="form-input"
                                   value="{{ old('email', $tenant->email) }}"
                                   placeholder="admin@youracademy.com">
                        </div>
                        <div>
                            <label class="form-label">Website URL</label>
                            <input type="url" name="website" class="form-input"
                                   value="{{ old('website', $s['website'] ?? '') }}"
                                   placeholder="https://www.youracademy.com">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save System Settings</button>
            </div>
        </div>

        {{-- ===== CONTACT DETAILS ===== --}}
        <div id="tab-contact" class="tab-panel {{ $activeTab !== 'contact' ? 'hidden' : '' }} space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Contact Details</h3>
                        <p class="text-xs text-gray-500">Shown to students for support & payment queries</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Primary Phone / WhatsApp</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <input type="text" name="phone" class="form-input pl-9"
                                       value="{{ old('phone', $tenant->phone) }}"
                                       placeholder="+91 9876543210">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Alternate Phone</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <input type="text" name="phone_alt" class="form-input pl-9"
                                       value="{{ old('phone_alt', $s['phone_alt'] ?? '') }}"
                                       placeholder="+91 9876543211">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Street Address</label>
                        <textarea name="address" rows="2" class="form-input"
                                  placeholder="e.g. 123, MG Road, Near Bus Stand">{{ old('address', $tenant->address) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-input"
                                   value="{{ old('city', $s['city'] ?? '') }}"
                                   placeholder="e.g. Jaipur">
                        </div>
                        <div>
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-input"
                                   value="{{ old('state', $s['state'] ?? '') }}"
                                   placeholder="e.g. Rajasthan">
                        </div>
                        <div>
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-input"
                                   value="{{ old('pincode', $s['pincode'] ?? '') }}"
                                   placeholder="e.g. 302001">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save Contact Details</button>
            </div>
        </div>

        {{-- ===== SOCIAL MEDIA ===== --}}
        <div id="tab-social" class="tab-panel {{ $activeTab !== 'social' ? 'hidden' : '' }} space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-pink-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Social Media & Channels</h3>
                        <p class="text-xs text-gray-500">Links shown in student portal footer & about page</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">

                    @php
                    $socials = [
                        ['facebook',  'Facebook',  'https://facebook.com/youracademy', '#1877F2',
                         'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'],
                        ['instagram', 'Instagram', 'https://instagram.com/youracademy', '#E1306C',
                         'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01M6.5 19.5h11a3 3 0 003-3v-11a3 3 0 00-3-3h-11a3 3 0 00-3 3v11a3 3 0 003 3z'],
                        ['youtube',   'YouTube',   'https://youtube.com/@youracademy',  '#FF0000',
                         'M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z'],
                        ['telegram',  'Telegram Channel', 't.me/youracademy or @handle', '#2CA5E0',
                         'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8l-1.7 8.02c-.12.57-.46.71-.93.44l-2.58-1.9-1.24 1.19c-.14.14-.25.25-.51.25l.18-2.62 4.72-4.27c.2-.18-.04-.28-.32-.1L7.46 14.98l-2.52-.79c-.55-.17-.56-.55.11-.81l9.85-3.8c.46-.17.86.11.74.82z'],
                        ['whatsapp',  'WhatsApp',  '+91 9876543210 (number only)', '#25D366',
                         'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z M12 2a10 10 0 100 20A10 10 0 0012 2z'],
                        ['twitter',   'Twitter / X', 'https://x.com/youracademy',     '#000000',
                         'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
                        ['linkedin',  'LinkedIn',  'https://linkedin.com/company/youracademy', '#0A66C2',
                         'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'],
                    ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($socials as [$key, $label, $placeholder, $color, $iconPath])
                        <div>
                            <label class="form-label flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:{{ $color }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path></svg>
                                {{ $label }}
                            </label>
                            <input type="{{ in_array($key, ['whatsapp','telegram']) ? 'text' : 'url' }}"
                                   name="{{ $key }}"
                                   class="form-input"
                                   value="{{ old($key, $s[$key] ?? '') }}"
                                   placeholder="{{ $placeholder }}">
                        </div>
                        @endforeach
                    </div>

                    @php
                    $hasSocial = collect(['facebook','instagram','youtube','telegram','whatsapp','twitter','linkedin'])->filter(fn($k) => !empty($s[$k]))->count();
                    @endphp
                    @if($hasSocial)
                    <div class="mt-2 pt-4 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-500 mb-2">Active Links</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['facebook'=>'#1877F2','instagram'=>'#E1306C','youtube'=>'#FF0000','telegram'=>'#2CA5E0','whatsapp'=>'#25D366','twitter'=>'#000','linkedin'=>'#0A66C2'] as $k => $c)
                            @if(!empty($s[$k]))
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-white text-xs font-medium" style="background:{{ $c }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ ucfirst($k) }}
                            </span>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save Social Links</button>
            </div>
        </div>

        {{-- ===== EMAIL CONFIG ===== --}}
        <div id="tab-email" class="tab-panel {{ $activeTab !== 'email' ? 'hidden' : '' }} space-y-6">

            {{-- SMTP Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Email / SMTP Configuration</h3>
                        <p class="text-xs text-gray-500">Notifications to students & teachers will be sent from this email</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-xs text-yellow-800">
                        ⚠ Leave blank to use the platform default email. Only configure if you have your own SMTP/email service.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Mail Driver</label>
                            <select name="mail_driver" class="form-input">
                                <option value="">— Use Platform Default —</option>
                                <option value="smtp" {{ old('mail_driver', $s['mail_driver'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ old('mail_driver', $s['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ old('mail_driver', $s['mail_driver'] ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ old('mail_driver', $s['mail_driver'] ?? '') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                <option value="log" {{ old('mail_driver', $s['mail_driver'] ?? '') === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Encryption</label>
                            <select name="mail_encryption" class="form-input">
                                <option value="tls" {{ old('mail_encryption', $s['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                <option value="ssl" {{ old('mail_encryption', $s['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ old('mail_encryption', $s['mail_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="mail_host" class="form-input"
                                   value="{{ old('mail_host', $s['mail_host'] ?? '') }}"
                                   placeholder="smtp.gmail.com">
                            <p class="text-xs text-gray-400 mt-1">e.g. smtp.gmail.com · smtp.zoho.com · mail.yourdomain.com</p>
                        </div>
                        <div>
                            <label class="form-label">SMTP Port</label>
                            <input type="number" name="mail_port" class="form-input"
                                   value="{{ old('mail_port', $s['mail_port'] ?? '587') }}"
                                   placeholder="587">
                            <p class="text-xs text-gray-400 mt-1">TLS → 587 · SSL → 465 · None → 25</p>
                        </div>
                        <div>
                            <label class="form-label">Username / Email</label>
                            <input type="text" name="mail_username" class="form-input"
                                   value="{{ old('mail_username', $s['mail_username'] ?? '') }}"
                                   placeholder="notifications@youracademy.com"
                                   autocomplete="off">
                        </div>
                        <div>
                            <label class="form-label">Password / App Password</label>
                            <div class="relative">
                                <input type="password" name="mail_password" id="mail_password" class="form-input pr-10"
                                       value=""
                                       placeholder="{{ !empty($s['mail_password']) ? '••••••••••••' : 'Enter password' }}"
                                       autocomplete="new-password">
                                <button type="button" onclick="togglePwd('mail_password')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                            @if(!empty($s['mail_password']))
                            <p class="text-xs text-green-600 mt-1">✓ Password saved. Leave blank to keep existing.</p>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">From Address (Sender Identity)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">From Email Address</label>
                                <input type="email" name="mail_from_address" class="form-input"
                                       value="{{ old('mail_from_address', $s['mail_from_address'] ?? '') }}"
                                       placeholder="noreply@youracademy.com">
                            </div>
                            <div>
                                <label class="form-label">From Name</label>
                                <input type="text" name="mail_from_name" class="form-input"
                                       value="{{ old('mail_from_name', $s['mail_from_name'] ?? $tenant->coaching_name) }}"
                                       placeholder="{{ $tenant->coaching_name }}">
                            </div>
                        </div>
                    </div>

                    {{-- Quick presets --}}
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-medium text-gray-500 mb-2">Quick Presets</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="applyPreset('smtp.gmail.com','587','tls')" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50 transition">Gmail</button>
                            <button type="button" onclick="applyPreset('smtp.zoho.com','587','tls')" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50 transition">Zoho Mail</button>
                            <button type="button" onclick="applyPreset('smtp.office365.com','587','tls')" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50 transition">Outlook / Office 365</button>
                            <button type="button" onclick="applyPreset('smtp.mailgun.org','587','tls')" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50 transition">Mailgun</button>
                            <button type="button" onclick="applyPreset('email-smtp.us-east-1.amazonaws.com','587','tls')" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50 transition">Amazon SES</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save Email Config</button>
            </div>
        </div>

        {{-- ===== WHATSAPP API ===== --}}
        <div id="tab-whatsapp" class="tab-panel {{ $activeTab !== 'whatsapp' ? 'hidden' : '' }} space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#dcfce7">
                        <svg class="w-5 h-5" style="color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a9.987 9.987 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z M12 2a10 10 0 100 20A10 10 0 0012 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">WhatsApp API Configuration</h3>
                        <p class="text-xs text-gray-500">Send notifications via WhatsApp to students & teachers</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-xs text-green-800">
                        Configure your WhatsApp Business API provider to send automated messages. Supports Twilio, WATI, UltraMsg and custom providers.
                    </div>

                    <div>
                        <label class="form-label">Provider</label>
                        <select name="wa_provider" id="wa_provider" class="form-input" onchange="updateWaHints()">
                            <option value="">— Disabled —</option>
                            <option value="twilio" {{ old('wa_provider', $s['wa_provider'] ?? '') === 'twilio' ? 'selected' : '' }}>Twilio (WhatsApp Business)</option>
                            <option value="wati" {{ old('wa_provider', $s['wa_provider'] ?? '') === 'wati' ? 'selected' : '' }}>WATI</option>
                            <option value="ultramsg" {{ old('wa_provider', $s['wa_provider'] ?? '') === 'ultramsg' ? 'selected' : '' }}>UltraMsg</option>
                            <option value="custom" {{ old('wa_provider', $s['wa_provider'] ?? '') === 'custom' ? 'selected' : '' }}>Custom / Other</option>
                        </select>
                    </div>

                    <div id="wa_fields" class="space-y-4 {{ empty($s['wa_provider']) ? 'hidden' : '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">API URL / Endpoint</label>
                                <input type="url" name="wa_api_url" class="form-input"
                                       value="{{ old('wa_api_url', $s['wa_api_url'] ?? '') }}"
                                       placeholder="https://api.ultramsg.com/instance123/messages/chat">
                                <p class="text-xs text-gray-400 mt-1" id="wa_url_hint"></p>
                            </div>
                            <div>
                                <label class="form-label">From Number</label>
                                <input type="text" name="wa_from_number" class="form-input"
                                       value="{{ old('wa_from_number', $s['wa_from_number'] ?? '') }}"
                                       placeholder="+919876543210">
                                <p class="text-xs text-gray-400 mt-1">WhatsApp Business number with country code</p>
                            </div>
                            <div>
                                <label class="form-label">Instance ID</label>
                                <input type="text" name="wa_instance_id" class="form-input"
                                       value="{{ old('wa_instance_id', $s['wa_instance_id'] ?? '') }}"
                                       placeholder="instance123">
                                <p class="text-xs text-gray-400 mt-1" id="wa_instance_hint">Your API instance / account SID</p>
                            </div>
                            <div>
                                <label class="form-label">API Key / Auth Token</label>
                                <div class="relative">
                                    <input type="password" name="wa_api_key" id="wa_api_key" class="form-input pr-10"
                                           value="{{ old('wa_api_key', $s['wa_api_key'] ?? '') }}"
                                           placeholder="{{ !empty($s['wa_api_key']) ? '••••••••••••' : 'Enter API key' }}"
                                           autocomplete="off">
                                    <button type="button" onclick="togglePwd('wa_api_key')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                @if(!empty($s['wa_api_key']))
                                <p class="text-xs text-green-600 mt-1">✓ API key saved.</p>
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <label class="form-label">Access Token <span class="text-gray-400 text-xs">(WATI / some providers)</span></label>
                                <div class="relative">
                                    <input type="password" name="wa_token" id="wa_token" class="form-input pr-10"
                                           value="{{ old('wa_token', $s['wa_token'] ?? '') }}"
                                           placeholder="Bearer token or OAuth token"
                                           autocomplete="off">
                                    <button type="button" onclick="togglePwd('wa_token')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                @if(!empty($s['wa_token']))
                                <p class="text-xs text-green-600 mt-1">✓ Token saved.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Provider help cards --}}
                    <div id="wa_help" class="border-t border-gray-100 pt-4 {{ empty($s['wa_provider']) ? 'hidden' : '' }}">
                        <p class="text-xs font-medium text-gray-500 mb-2">Provider Guide</p>
                        <div id="wa_help_twilio" class="hidden bg-blue-50 rounded-lg p-3 text-xs text-blue-800 space-y-1">
                            <p class="font-semibold">Twilio</p>
                            <p>• Instance ID = Account SID &nbsp;|&nbsp; API Key = Auth Token</p>
                            <p>• API URL: <code>https://api.twilio.com/2010-04-01/Accounts/{SID}/Messages.json</code></p>
                            <p>• From Number: <code>whatsapp:+14155238886</code></p>
                        </div>
                        <div id="wa_help_wati" class="hidden bg-purple-50 rounded-lg p-3 text-xs text-purple-800 space-y-1">
                            <p class="font-semibold">WATI</p>
                            <p>• Use the Bearer Token from your WATI dashboard</p>
                            <p>• API URL: <code>https://live-mt-server.wati.io/{INSTANCE}/api/v1/sendSessionMessage/{phone}</code></p>
                        </div>
                        <div id="wa_help_ultramsg" class="hidden bg-green-50 rounded-lg p-3 text-xs text-green-800 space-y-1">
                            <p class="font-semibold">UltraMsg</p>
                            <p>• Instance ID = your instance name &nbsp;|&nbsp; API Key = token from dashboard</p>
                            <p>• API URL: <code>https://api.ultramsg.com/{INSTANCE}/messages/chat</code></p>
                        </div>
                        <div id="wa_help_custom" class="hidden bg-gray-50 rounded-lg p-3 text-xs text-gray-700 space-y-1">
                            <p class="font-semibold">Custom Provider</p>
                            <p>Enter your API endpoint, credentials, and from number manually.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save WhatsApp Config</button>
            </div>
        </div>

        {{-- ===== BRANDING ===== --}}
        <div id="tab-branding" class="tab-panel {{ $activeTab !== 'branding' ? 'hidden' : '' }} space-y-6">

            {{-- Portal Title --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Portal Title</h3>
                        <p class="text-xs text-gray-500">Title shown in browser tab and PWA</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Website Title</label>
                        <input type="text" name="portal_title" class="form-input"
                               value="{{ old('portal_title', $s['portal_title'] ?? '') }}"
                               placeholder="e.g. Future Academy - Student Portal">
                        <p class="text-xs text-gray-400 mt-1">Appears in browser tab and PWA install prompt</p>
                    </div>
                </div>
            </div>

            {{-- Logo & Icons --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-pink-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Logos & Icons</h3>
                        <p class="text-xs text-gray-500">Upload branding assets for your portal</p>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    
                    {{-- Main Logo --}}
                    <div>
                        <label class="form-label">Main Logo</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden border border-gray-200">
                                @if($tenant->logo)
                                    <img src="{{ Storage::url($tenant->logo) }}" alt="Logo" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-400 mt-1">Recommended: 400x100px, PNG/SVG with transparent background</p>
                                @if($tenant->logo)
                                    <p class="text-xs text-green-600 mt-1">✓ Logo uploaded. Upload new to replace.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Portal Icon (Favicon) --}}
                    <div class="border-t border-gray-100 pt-4">
                        <label class="form-label">Portal Icon / Favicon</label>
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden border border-gray-200">
                                @if($tenant->portal_icon)
                                    <img src="{{ Storage::url($tenant->portal_icon) }}" alt="Portal Icon" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="portal_icon" accept="image/*,.ico" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                                <p class="text-xs text-gray-400 mt-1">Recommended: 64x64px or 32x32px, PNG/ICO/SVG</p>
                                @if($tenant->portal_icon)
                                    <p class="text-xs text-green-600 mt-1">✓ Portal icon uploaded. Upload new to replace.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- PWA Icon --}}
                    <div class="border-t border-gray-100 pt-4">
                        <label class="form-label">PWA App Icon</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden border border-gray-200">
                                @if($tenant->pwa_icon)
                                    <img src="{{ Storage::url($tenant->pwa_icon) }}" alt="PWA Icon" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="pwa_icon" accept="image/png,image/svg+xml" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="text-xs text-gray-400 mt-1"><strong>Required:</strong> 512x512px PNG or SVG. This appears on the user's home screen.</p>
                                @if($tenant->pwa_icon)
                                    <p class="text-xs text-green-600 mt-1">✓ PWA icon uploaded. Upload new to replace.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save Branding</button>
            </div>
        </div>

        {{-- ===== PAYMENT ===== --}}
        <div id="tab-payment" class="tab-panel {{ $activeTab !== 'payment' ? 'hidden' : '' }} space-y-6">

            {{-- UPI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">UPI Payment Details</h3>
                        <p class="text-xs text-gray-500">Students will see this when submitting payment</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">UPI ID <span class="text-red-500">*</span></label>
                            <input type="text" name="upi_id" class="form-input"
                                   value="{{ old('upi_id', $s['upi_id'] ?? '') }}"
                                   placeholder="yourname@okaxis / yourname@ybl">
                            <p class="text-xs text-gray-400 mt-1">e.g. academy@okicici · 9876543210@paytm</p>
                        </div>
                        <div>
                            <label class="form-label">UPI Display Name</label>
                            <input type="text" name="upi_name" class="form-input"
                                   value="{{ old('upi_name', $s['upi_name'] ?? $tenant->coaching_name) }}"
                                   placeholder="{{ $tenant->coaching_name }}">
                            <p class="text-xs text-gray-400 mt-1">Name shown on UPI payment screen</p>
                        </div>
                    </div>

                    @if(!empty($s['upi_id']))
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-indigo-800">Active UPI ID</p>
                            <p class="text-base font-bold text-indigo-700 font-mono">{{ $s['upi_id'] }}</p>
                            <p class="text-xs text-indigo-500">Students can pay directly using this ID</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Bank --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Bank Account Details</h3>
                        <p class="text-xs text-gray-500">Optional — shown as alternate payment method</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="bank_holder" class="form-input"
                                   value="{{ old('bank_holder', $s['bank_holder'] ?? '') }}"
                                   placeholder="Name on bank account">
                        </div>
                        <div>
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-input"
                                   value="{{ old('bank_name', $s['bank_name'] ?? '') }}"
                                   placeholder="e.g. SBI, HDFC, ICICI">
                        </div>
                        <div>
                            <label class="form-label">Account Number</label>
                            <input type="text" name="bank_account" class="form-input"
                                   value="{{ old('bank_account', $s['bank_account'] ?? '') }}"
                                   placeholder="Account number">
                        </div>
                        <div>
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="bank_ifsc" class="form-input"
                                   value="{{ old('bank_ifsc', $s['bank_ifsc'] ?? '') }}"
                                   placeholder="e.g. SBIN0001234">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-6 py-2.5">Save Payment Details</button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
        b.classList.add('text-gray-500');
    });
    document.getElementById('tab-' + tab).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + tab);
    btn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    btn.classList.remove('text-gray-500');
    document.getElementById('active_tab_input').value = tab;
    history.replaceState(null, '', '?tab=' + tab);
}

function togglePwd(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

function applyPreset(host, port, enc) {
    document.querySelector('[name="mail_host"]').value = host;
    document.querySelector('[name="mail_port"]').value = port;
    document.querySelector('[name="mail_encryption"]').value = enc;
}

const waHints = {
    twilio:   { url: 'https://api.twilio.com/2010-04-01/Accounts/{SID}/Messages.json', instance: 'Account SID' },
    wati:     { url: 'https://live-mt-server.wati.io/{INSTANCE}/api/v1/sendSessionMessage/{phone}', instance: 'WATI instance ID' },
    ultramsg: { url: 'https://api.ultramsg.com/{INSTANCE}/messages/chat', instance: 'UltraMsg instance name' },
    custom:   { url: 'Your custom API endpoint', instance: 'Instance / Account ID' },
};

function updateWaHints() {
    const p = document.getElementById('wa_provider').value;
    const fields = document.getElementById('wa_fields');
    const help   = document.getElementById('wa_help');
    const helpDivs = ['twilio','wati','ultramsg','custom'];
    helpDivs.forEach(k => document.getElementById('wa_help_' + k).classList.add('hidden'));
    if (p) {
        fields.classList.remove('hidden');
        help.classList.remove('hidden');
        document.getElementById('wa_help_' + p)?.classList.remove('hidden');
        if (waHints[p]) {
            document.getElementById('wa_url_hint').textContent = waHints[p].url;
            document.getElementById('wa_instance_hint').textContent = waHints[p].instance;
        }
    } else {
        fields.classList.add('hidden');
        help.classList.add('hidden');
    }
}
document.addEventListener('DOMContentLoaded', updateWaHints);
</script>
@endpush
@endsection
