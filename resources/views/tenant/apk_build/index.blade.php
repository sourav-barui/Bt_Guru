@extends('layouts.tenant')

@section('title', 'Mobile App Build - ' . $tenant->coaching_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">📱 Mobile App Builder</h1>
        <p class="text-gray-600 mt-2">Build and manage your branded Android app for students</p>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4 {{ $apkExists ? 'border-green-500' : 'border-yellow-500' }}">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">APK Status</h2>
                <p class="text-gray-600 mt-1">
                    @if($apkExists)
                        ✅ <span class="text-green-600 font-medium">APK is ready for download</span>
                    @else
                        ⚠️ <span class="text-yellow-600 font-medium">APK not built yet</span>
                    @endif
                </p>
                @if($buildInfo && isset($buildInfo['updated_at']))
                    <p class="text-sm text-gray-500 mt-2">
                        Last updated: {{ \Carbon\Carbon::parse($buildInfo['updated_at'])->diffForHumans() }}
                    </p>
                @endif
            </div>
            <div class="text-right">
                @if($apkExists)
                    <a href="{{ $apkUrl }}" download 
                       class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download APK
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Build Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">🔨 Build App</h2>
            
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-medium text-blue-800 mb-2">What gets built?</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Branded with your coaching name: <strong>{{ $tenant->coaching_name }}</strong></li>
                        <li>• Package name: <code>tech.btguru.twa.{{ $tenant->subdomain }}</code></li>
                        <li>• Your logo and theme colors</li>
                        <li>• Direct access to your student portal</li>
                    </ul>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4">
                    <h3 class="font-medium text-yellow-800 mb-2">⚠️ Before Building</h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Ensure your logo is uploaded in Settings</li>
                        <li>• Build takes 5-10 minutes</li>
                        <li>• Keep this page open during build</li>
                    </ul>
                </div>

                <button id="buildBtn" 
                        class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center"
                        {{ $buildInfo['status'] ?? '' === 'building' ? 'disabled' : '' }}>
                    <span id="buildBtnText">
                        @if(($buildInfo['status'] ?? '') === 'building')
                            ⏳ Building in Progress...
                        @else
                            🚀 Build APK Now
                        @endif
                    </span>
                    <svg id="buildSpinner" class="animate-spin ml-2 h-5 w-5 {{ ($buildInfo['status'] ?? '') === 'building' ? '' : 'hidden' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <div id="buildStatus" class="hidden mt-4 p-4 rounded-lg"></div>
            </div>
        </div>

        <!-- Instructions Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Instructions</h2>
            
            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-semibold">1</div>
                    <div>
                        <h3 class="font-medium text-gray-800">Download the APK</h3>
                        <p class="text-sm text-gray-600 mt-1">Click "Build APK Now" and wait for build to complete. Then download the APK file.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-semibold">2</div>
                    <div>
                        <h3 class="font-medium text-gray-800">Test Installation</h3>
                        <p class="text-sm text-gray-600 mt-1">Install APK on Android device:</p>
                        <ul class="text-sm text-gray-600 mt-1 list-disc list-inside">
                            <li>Enable "Install from Unknown Sources" in Settings</li>
                            <li>Transfer APK to device and tap to install</li>
                            <li>Verify app icon and name appear correctly</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-semibold">3</div>
                    <div>
                        <h3 class="font-medium text-gray-800">Google Play Store (Optional)</h3>
                        <p class="text-sm text-gray-600 mt-1">To publish on Play Store:</p>
                        <ul class="text-sm text-gray-600 mt-1 list-disc list-inside">
                            <li>Create Google Play Developer account ($25 one-time)</li>
                            <li>Generate signed AAB (App Bundle) - Contact support</li>
                            <li>Upload to Play Console</li>
                            <li>Fill store listing with screenshots</li>
                            <li>Submit for review (1-3 days)</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-semibold">4</div>
                    <div>
                        <h3 class="font-medium text-gray-800">Share with Students</h3>
                        <p class="text-sm text-gray-600 mt-1">Share download link:</p>
                        <code class="block mt-1 p-2 bg-gray-100 rounded text-xs break-all">
                            https://{{ $tenant->subdomain }}.{{ config('app.base_domain') }}/student/download-app
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-8 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">❓ Frequently Asked Questions</h2>
        
        <div class="space-y-4">
            <details class="group">
                <summary class="flex justify-between items-center cursor-pointer py-2 font-medium text-gray-700 hover:text-purple-600">
                    What is an APK?
                    <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <p class="text-gray-600 text-sm mt-2 pl-4">APK (Android Package Kit) is the file format for Android apps. Students can download and install it directly on their Android phones.</p>
            </details>

            <details class="group">
                <summary class="flex justify-between items-center cursor-pointer py-2 font-medium text-gray-700 hover:text-purple-600">
                    Why isn't the app on Play Store automatically?
                    <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <p class="text-gray-600 text-sm mt-2 pl-4">Each tenant needs their own Play Store account to publish. Google requires a one-time $25 developer fee and verification process. We provide the APK file, you handle publishing.</p>
            </details>

            <details class="group">
                <summary class="flex justify-between items-center cursor-pointer py-2 font-medium text-gray-700 hover:text-purple-600">
                    Can iOS (iPhone) users install this?
                    <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <p class="text-gray-600 text-sm mt-2 pl-4">This is an Android-only APK. iPhone users can still use the web app by visiting your URL in Safari and adding to Home Screen. iOS apps require separate Apple Developer account ($99/year).</p>
            </details>

            <details class="group">
                <summary class="flex justify-between items-center cursor-pointer py-2 font-medium text-gray-700 hover:text-purple-600">
                    How do I update the app?
                    <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <p class="text-gray-600 text-sm mt-2 pl-4">The app is a "Progressive Web App" wrapper - it auto-updates when you change your portal. Students don't need to reinstall for content updates. Only rebuild APK if you change logo/app name.</p>
            </details>
        </div>
    </div>
</div>

<script>
document.getElementById('buildBtn').addEventListener('click', async function() {
    const btn = this;
    const btnText = document.getElementById('buildBtnText');
    const spinner = document.getElementById('buildSpinner');
    const statusDiv = document.getElementById('buildStatus');
    
    // Disable button
    btn.disabled = true;
    btnText.textContent = '⏳ Building... This may take 5-10 minutes';
    spinner.classList.remove('hidden');
    statusDiv.classList.remove('hidden');
    statusDiv.className = 'mt-4 p-4 rounded-lg bg-blue-50 text-blue-700';
    statusDiv.textContent = 'Build started... Please wait.';
    
    try {
        const response = await fetch('{{ route("tenant.apk.build") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            statusDiv.className = 'mt-4 p-4 rounded-lg bg-green-50 text-green-700';
            statusDiv.innerHTML = `
                ✅ ${data.message}<br>
                <a href="${data.download_url}" download class="inline-block mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Download APK
                </a>
            `;
            btnText.textContent = '🔄 Build Again';
        } else if (data.status === 'in_progress') {
            statusDiv.className = 'mt-4 p-4 rounded-lg bg-yellow-50 text-yellow-700';
            statusDiv.textContent = data.message;
            btnText.textContent = '🚀 Build APK Now';
        } else {
            statusDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 text-red-700';
            statusDiv.textContent = '❌ ' + data.message;
            btnText.textContent = '🚀 Retry Build';
        }
    } catch (error) {
        statusDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 text-red-700';
        statusDiv.textContent = '❌ Error: ' + error.message;
        btnText.textContent = '🚀 Retry Build';
    } finally {
        btn.disabled = false;
        spinner.classList.add('hidden');
    }
});
</script>
@endsection
