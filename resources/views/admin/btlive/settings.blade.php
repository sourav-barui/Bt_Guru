@extends('layouts.admin')

@section('title', 'BTLive Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">BTLive Settings</h1>
            <p class="text-gray-600 mt-1">Configure live class recording and video settings</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Storage Stats Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recording Storage Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $storageStats['file_count'] }}</p>
                <p class="text-xs text-blue-600/70">Recorded Files</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $storageStats['total_size_formatted'] }}</p>
                <p class="text-xs text-green-600/70">Total Storage Used</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-purple-600">{{ $storageStats['storage_exists'] ? '✓' : '✗' }}</p>
                <p class="text-xs text-purple-600/70">Storage Path Exists</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-orange-600">{{ $storageStats['writable'] ? '✓' : '✗' }}</p>
                <p class="text-xs text-orange-600/70">Writable</p>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <strong>Storage Path:</strong> {{ $storageStats['storage_path'] }}
        </div>
        <div class="mt-4 flex gap-2">
            <form action="{{ route('admin.btlive.cleanup') }}" method="POST" class="inline">
                @csrf
                <input type="number" name="days" value="30" class="w-20 px-2 py-1 border rounded text-sm" min="1" max="365">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm" onclick="return confirm('Delete recordings older than specified days?')">
                    Cleanup Old Recordings
                </button>
            </form>
            <button onclick="testS3Connection()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Test S3 Connection
            </button>
        </div>
    </div>

    <form action="{{ route('admin.btlive.settings.update') }}" method="POST">
        @csrf

        <!-- Jitsi Configuration -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Jitsi Meet Configuration</h2>
                <p class="text-sm text-gray-500">Configure your Jitsi server settings</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jitsi Domain</label>
                    <input type="url" name="jitsi_domain" value="{{ $settings['jitsi_domain'] }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-500 mt-1">Example: meet.yourdomain.com</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">App ID</label>
                    <input type="text" name="jitsi_app_id" value="{{ $settings['jitsi_app_id'] }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">App Secret</label>
                    <input type="password" name="jitsi_app_secret" value="{{ $settings['jitsi_app_secret'] }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Leave blank to keep current">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current secret</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Webhook Secret</label>
                    <input type="password" name="webhook_secret" value="{{ $settings['webhook_secret'] }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Leave blank to keep current">
                </div>
            </div>
        </div>

        <!-- Recording Settings -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Recording Settings</h2>
                <p class="text-sm text-gray-500">Configure how live class recordings are handled</p>
            </div>
            <div class="p-6 space-y-6">
                <!-- Enable Recording -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="recording_enabled" id="recording_enabled" value="1" {{ $settings['recording_enabled'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="recording_enabled" class="font-medium text-gray-700">Enable Recording</label>
                </div>

                <!-- Auto Start Recording -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="auto_start_recording" id="auto_start_recording" value="1" {{ $settings['auto_start_recording'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="auto_start_recording" class="font-medium text-gray-700">Auto-Start Recording When Class Begins</label>
                </div>

                <!-- Recording Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recording Format</label>
                    <select name="recording_format" class="w-full md:w-1/3 px-3 py-2 border rounded-lg">
                        <option value="mp4" {{ $settings['recording_format'] == 'mp4' ? 'selected' : '' }}>MP4</option>
                        <option value="webm" {{ $settings['recording_format'] == 'webm' ? 'selected' : '' }}>WebM</option>
                    </select>
                </div>

                <!-- Max Duration -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Recording Duration (minutes)</label>
                        <input type="number" name="max_recording_duration" value="{{ $settings['max_recording_duration'] }}" min="10" max="480" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Cleanup After (days)</label>
                        <input type="number" name="auto_cleanup_days" value="{{ $settings['auto_cleanup_days'] }}" min="1" max="365" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Storage Per Tenant (GB)</label>
                        <input type="number" name="max_storage_per_tenant" value="{{ $settings['max_storage_per_tenant'] }}" min="1" max="500" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Local Storage -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Local Storage</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="local_storage_enabled" id="local_storage_enabled" value="1" {{ $settings['local_storage_enabled'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="local_storage_enabled" class="font-medium text-gray-700">Enable Local Storage</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Local Storage Path</label>
                    <input type="text" name="local_storage_path" value="{{ $settings['local_storage_path'] }}" class="w-full px-3 py-2 border rounded-lg font-mono text-sm">
                    <p class="text-xs text-gray-500 mt-1">Server path where recordings will be saved</p>
                </div>
            </div>
        </div>

        <!-- S3 Storage -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">S3 / Cloud Storage</h2>
                <p class="text-sm text-gray-500">Configure Amazon S3 or compatible storage (MinIO, DigitalOcean Spaces, etc.)</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="s3_enabled" id="s3_enabled" value="1" {{ $settings['s3_enabled'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="s3_enabled" class="font-medium text-gray-700">Enable S3 Storage</label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">S3 Bucket Name</label>
                        <input type="text" name="s3_bucket" value="{{ $settings['s3_bucket'] }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">S3 Region</label>
                        <input type="text" name="s3_region" value="{{ $settings['s3_region'] }}" class="w-full px-3 py-2 border rounded-lg" placeholder="us-east-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Access Key ID</label>
                        <input type="text" name="s3_access_key" value="{{ $settings['s3_access_key'] }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secret Access Key</label>
                        <input type="password" name="s3_secret_key" value="{{ $settings['s3_secret_key'] }}" class="w-full px-3 py-2 border rounded-lg" placeholder="Leave blank to keep current">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">S3 Endpoint (Optional - for MinIO, DigitalOcean Spaces)</label>
                        <input type="url" name="s3_endpoint" value="{{ $settings['s3_endpoint'] }}" class="w-full px-3 py-2 border rounded-lg" placeholder="https://s3.amazonaws.com">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for standard AWS S3</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Quality -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Video Quality Settings</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teacher Video Resolution</label>
                    <select name="teacher_resolution" class="w-full px-3 py-2 border rounded-lg">
                        <option value="180" {{ $settings['teacher_resolution'] == 180 ? 'selected' : '' }}>180p (Low)</option>
                        <option value="360" {{ $settings['teacher_resolution'] == 360 ? 'selected' : '' }}>360p (Standard)</option>
                        <option value="480" {{ $settings['teacher_resolution'] == 480 ? 'selected' : '' }}>480p (SD)</option>
                        <option value="720" {{ $settings['teacher_resolution'] == 720 ? 'selected' : '' }}>720p (HD)</option>
                        <option value="1080" {{ $settings['teacher_resolution'] == 1080 ? 'selected' : '' }}>1080p (Full HD)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teacher Video FPS</label>
                    <select name="teacher_fps" class="w-full px-3 py-2 border rounded-lg">
                        <option value="15" {{ $settings['teacher_fps'] == 15 ? 'selected' : '' }}>15 FPS (Low Bandwidth)</option>
                        <option value="24" {{ $settings['teacher_fps'] == 24 ? 'selected' : '' }}>24 FPS (Cinematic)</option>
                        <option value="30" {{ $settings['teacher_fps'] == 30 ? 'selected' : '' }}>30 FPS (Standard)</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="enable_simulcast" id="enable_simulcast" value="1" {{ $settings['enable_simulcast'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded mr-2">
                    <label for="enable_simulcast" class="font-medium text-gray-700">Enable Simulcast</label>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Security Settings</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="require_jwt" id="require_jwt" value="1" {{ $settings['require_jwt'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="require_jwt" class="font-medium text-gray-700">Require JWT Authentication</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="enable_lobby" id="enable_lobby" value="1" {{ $settings['enable_lobby'] ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="enable_lobby" class="font-medium text-gray-700">Enable Waiting Room / Lobby</label>
                </div>
            </div>
        </div>

        <!-- System Limits -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">System Limits</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants Per Class</label>
                    <input type="number" name="max_participants" value="{{ $settings['max_participants'] }}" min="10" max="10000" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Concurrent Teachers</label>
                    <input type="number" name="max_teachers_live" value="{{ $settings['max_teachers_live'] }}" min="1" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold">
                Save All Settings
            </button>
        </div>
    </form>
</div>

<script>
function testS3Connection() {
    fetch('{{ route('admin.btlive.test-s3') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ S3 Connection Successful!\nFiles found: ' + data.files_found);
        } else {
            alert('✗ S3 Connection Failed:\n' + data.error);
        }
    })
    .catch(error => {
        alert('Error testing S3: ' + error.message);
    });
}
</script>
@endsection
