# Tenant-Based Android APK Builder

Auto-generate **coaching-center branded** Android APKs for each tenant using Trusted Web Activity (TWA).

Each coaching center gets their own app:
- **App Name:** `{Coaching Name} Student App`
- **Package:** `tech.btguru.twa.{tenant-slug}`
- **Icon:** Uses tenant logo
- **APK URL:** `/downloads/{tenant-slug}/student.apk`

## Quick Start

### Build for Specific Tenant
```powershell
cd pwa-builder
.\build-tenant-apk.ps1 -TenantSlug "coaching-name" -CoachingName "Coaching Name"
```

### Build for All Tenants
```powershell
cd pwa-builder
.\build-all-tenants.ps1
# OR using Artisan:
php artisan pwa:build-all-tenants
```

### Auto-build on Deploy
Every push to `main` branch triggers automatic APK build via GitHub Actions.

## How It Works

1. **Bubblewrap** wraps your PWA in a native Android shell
2. **Trusted Web Activity** shows your PWA in a Chrome-powered full-screen view
3. No WebView - uses real Chrome with all features (push notifications, camera, etc.)

## Files Created

| File | Description |
|------|-------------|
| `app/build/outputs/apk/release/` | Installable APK file |
| `app/build/outputs/bundle/release/` | Play Store AAB bundle |

## Setup Steps

1. **Get SHA256 fingerprint** after first build:
   ```powershell
   keytool -list -v -keystore android-app/android.keystore
   ```

2. **Update assetlinks.json** with your fingerprint:
   Edit `public/.well-known/assetlinks.json`

3. **Deploy** the updated assetlinks.json

4. **Rebuild** APK with verified domain

## Deploy APK for Download

After building, copy APK to public folder:
```powershell
.\pwa-builder\deploy-apk.ps1
```

Or manually:
```powershell
copy android-app\app\build\outputs\apk\release\app-release.apk public\downloads\btguru-student.apk
```

Students download from: `https://{tenant-slug}.btguru.tech/downloads/{tenant-slug}/student.apk`

Examples:
- ABC Coaching: `https://abc.btguru.tech/downloads/abc/student.apk`
- XYZ Academy: `https://xyz.btguru.tech/downloads/xyz/student.apk`

## Customization

Edit `android-app/twa-manifest.json` after init:
- App name, icon, colors
- Splash screen
- Navigation bar color

## Publishing

### Direct APK Install
Share `btguru-student-release.apk` directly

### Google Play Store
Upload `app-release.aab` to Play Console

## Updates

When your web app updates, the APK automatically shows the new version - no rebuild needed!

To force an APK rebuild:
```powershell
npm run update
npm run build
```
