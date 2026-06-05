#!/bin/bash
# Check if files are deployed on Dokploy VPS
# Run this on your VPS via SSH

echo "=== Checking Deployment Files ==="
echo ""

echo "1. Checking student_mobile.blade.php..."
if grep -q "Download App" /var/www/html/resources/views/layouts/student_mobile.blade.php 2>/dev/null; then
    echo "✅ Download button FOUND in menu"
else
    echo "❌ Download button NOT FOUND"
fi

echo ""
echo "2. Checking download.blade.php..."
if [ -f "/var/www/html/resources/views/student/download.blade.php" ]; then
    echo "✅ Download page EXISTS"
else
    echo "❌ Download page MISSING"
fi

echo ""
echo "3. Checking download directory..."
ls -la /var/www/html/public/downloads/ 2>/dev/null || echo "❌ Downloads directory not found"

echo ""
echo "4. Checking web routes..."
if grep -q "download-app" /var/www/html/routes/web.php 2>/dev/null; then
    echo "✅ Download route EXISTS"
else
    echo "❌ Download route MISSING"
fi

echo ""
echo "5. Checking StudentController for email..."
if grep -q "sendWelcomeEmail" /var/www/html/app/Http/Controllers/Tenant/StudentController.php 2>/dev/null; then
    echo "✅ Welcome email code EXISTS"
else
    echo "❌ Welcome email code MISSING"
fi

echo ""
echo "6. Last Git commit on server:"
cd /var/www/html && git log -1 --oneline 2>/dev/null || echo "Not a git repo or no commits"

echo ""
echo "=== Check Complete ==="
