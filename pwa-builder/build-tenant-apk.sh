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

# Initialize TWA project (only first time)
if [ ! -f "twa-manifest.json" ]; then
    echo "Initializing TWA project for $COACHING_NAME..."
    
    # Create init answers
    printf '%s\n%s\n%s\n%s\n%s\n%s\nn\n' \
        "$COACHING_NAME" \
        "$PACKAGE_NAME" \
        "$COACHING_NAME Student App" \
        "https://$TENANT_DOMAIN" \
        "$TENANT_DOMAIN" \
        "$TENANT_DOMAIN" | bubblewrap init --manifest "$MANIFEST_URL" --directory .
else
    echo "Updating existing TWA project..."
    bubblewrap update
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
