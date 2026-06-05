#!/bin/bash
# Build tenant-specific Android APK (Linux/Mac version)
# Usage: ./build-tenant-apk.sh <tenant-slug> <coaching-name> [domain] [theme-color]

set -e

TENANT_SLUG=${1:-}
COACHING_NAME=${2:-}
DOMAIN=${3:-btguru.tech}
THEME_COLOR=${4:-#7C3AED}

if [ -z "$TENANT_SLUG" ] || [ -z "$COACHING_NAME" ]; then
    echo "Usage: $0 <tenant-slug> <coaching-name> [domain] [theme-color]"
    exit 1
fi

# Generate package name
PACKAGE_NAME="tech.btguru.twa.${TENANT_SLUG//-/_}"
MANIFEST_URL="https://${TENANT_SLUG}.${DOMAIN}/manifest.json"
TENANT_DOMAIN="${TENANT_SLUG}.${DOMAIN}"

echo "Building APK for: $COACHING_NAME"
echo "Package: $PACKAGE_NAME"
echo "Domain: $TENANT_DOMAIN"

# Check dependencies
if ! command -v bubblewrap &> /dev/null; then
    echo "Installing Bubblewrap CLI..."
    npm install -g @bubblewrap/cli
fi

# Create tenant-specific output directory
OUTPUT_DIR="android-apps/$TENANT_SLUG"
mkdir -p "$OUTPUT_DIR"
cd "$OUTPUT_DIR"

# Create TWA manifest directly (non-interactive)
if [ ! -f "twa-manifest.json" ]; then
    echo "Creating TWA manifest for $COACHING_NAME..."
    
    cat > twa-manifest.json << EOF
{
  "packageId": "$PACKAGE_NAME",
  "host": "$TENANT_DOMAIN",
  "name": "$COACHING_NAME",
  "launcherName": "$COACHING_NAME",
  "display": "standalone",
  "themeColor": "$THEME_COLOR",
  "navigationColor": "$THEME_COLOR",
  "navigationColorDark": "$THEME_COLOR",
  "navigationDividerColor": "$THEME_COLOR",
  "backgroundColor": "$THEME_COLOR",
  "enableNotifications": true,
  "shortcuts": [],
  "iconPath": "app/src/main/res/mipmap-xxxhdpi/ic_launcher.png",
  "maskableIconPath": "app/src/main/res/mipmap-xxxhdpi/ic_launcher_foreground.png",
  "monochromeIconPath": "app/src/main/res/mipmap-xxxhdpi/ic_launcher_foreground.png",
  "appVersion": "1.0.0",
  "appVersionCode": 1,
  "fullScopeUrl": "https://$TENANT_DOMAIN",
  "minSdkVersion": 19,
  "targetSdkVersion": 34,
  "compileSdkVersion": 34,
  "gradleVersion": "8.0.0",
  "androidBuildToolsVersion": "34.0.0",
  "isChromeOSOnly": false,
  "isMetaQuest": false,
  "shareTarget": {},
  "generatorApp": "bubblewrap-cli",
  "webManifestUrl": "$MANIFEST_URL"
}
EOF
    
    # Download manifest and icons
    echo "Downloading manifest from $MANIFEST_URL..."
    curl -s "$MANIFEST_URL" -o manifest.json || echo "Warning: Could not download manifest"
    
    # Create build.gradle with JDK path
    mkdir -p app
    cat > app/build.gradle << 'GRADLEEOF'
plugins {
    id 'com.android.application'
}

android {
    namespace 'com.google.androidbrowserhelper'
    compileSdkVersion 34
    
    defaultConfig {
        minSdkVersion 19
        targetSdkVersion 34
        versionCode 1
        versionName "1.0.0"
    }
    
    compileOptions {
        sourceCompatibility JavaVersion.VERSION_17
        targetCompatibility JavaVersion.VERSION_17
    }
}

dependencies {
    implementation 'com.google.androidbrowserhelper:androidbrowserhelper:2.5.0'
}
GRADLEEOF
    
    echo "✅ TWA project created for $COACHING_NAME"
else
    echo "Updating existing TWA project..."
fi

# Build APK
echo "Building APK for $COACHING_NAME..."
bubblewrap build

# Rename output files with tenant slug
APK_SOURCE="app/build/outputs/apk/release/app-release.apk"
APK_DEST="app/build/outputs/apk/release/${TENANT_SLUG}-student.apk"
AAB_SOURCE="app/build/outputs/bundle/release/app-release.aab"
AAB_DEST="app/build/outputs/bundle/release/${TENANT_SLUG}-student.aab"

if [ -f "$APK_SOURCE" ]; then
    cp "$APK_SOURCE" "$APK_DEST"
    echo "✅ APK: $APK_DEST"
fi

if [ -f "$AAB_SOURCE" ]; then
    cp "$AAB_SOURCE" "$AAB_DEST"
    echo "✅ AAB: $AAB_DEST"
fi

echo "✅ Build complete for $COACHING_NAME!"
