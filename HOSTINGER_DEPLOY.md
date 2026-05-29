# BT Guru – Hostinger Deployment Guide (btguru.tech)

## What You Need
- Hostinger account with btguru.tech domain connected
- Any shared hosting plan (Business recommended) OR a VPS

---

## PART 1 — Hostinger hPanel Setup

### 1. Create a MySQL Database
1. Login to hPanel → **Databases** → **MySQL Databases**
2. Create database: `u123456789_btguru` (use your actual prefix)
3. Create user + password, assign ALL PRIVILEGES
4. Note down: DB name, DB user, DB password

### 2. Set PHP Version to 8.3
hPanel → **Advanced** → **PHP Configuration** → select **PHP 8.3**

Enable these extensions (most are on by default):
- `pdo_mysql` ✓
- `mbstring` ✓
- `openssl` ✓
- `fileinfo` ✓
- `tokenizer` ✓
- `xml` ✓
- `ctype` ✓
- `json` ✓
- `bcmath` ✓
- `curl` ✓

### 3. Add Wildcard Subdomain
hPanel → **Domains** → **Subdomains**
- Add subdomain: `*` (asterisk)
- Point to: `public_html` (same as main domain)

> This allows `admin.btguru.tech`, `futureacademy.btguru.tech` etc. to all work.

### 4. Add Wildcard SSL
hPanel → **SSL** → **Let's Encrypt**
- Install for: `btguru.tech` AND `*.btguru.tech`
- If wildcard SSL is not available on your plan, install for individual subdomains manually.

---

## PART 2 — Upload the Project

### Option A: File Manager (No SSH) — Easiest for Testing

1. **Zip the project** on your PC — exclude these folders:
   - `node_modules/`
   - `.git/`
   - `storage/logs/*.log`

2. **Upload the zip** to `public_html/` via hPanel File Manager

3. **Extract** it — you'll get `public_html/Bt_Guru/` (or rename to `btguru/`)

4. **Copy `hostinger_root.htaccess`** → rename it to `.htaccess` and place it in `public_html/`
   - This redirects all traffic into `public/`

5. Your folder structure should be:
   ```
   public_html/
   ├── .htaccess          ← the hostinger_root.htaccess renamed
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── public/            ← Laravel's public folder
   │   ├── index.php
   │   └── .htaccess
   ├── resources/
   ├── routes/
   ├── storage/
   └── vendor/
   ```

### Option B: File Manager — Cleanest Structure

Upload ONLY the contents of `public/` into `public_html/`, then:
1. Edit `public_html/index.php` — change the two paths:
   ```php
   // Change these two lines:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // To point to where you uploaded the rest of the app:
   require __DIR__.'/btguru/vendor/autoload.php';
   $app = require_once __DIR__.'/btguru/bootstrap/app.php';
   ```
2. Upload the rest of the project (everything except `public/`) to a folder named `btguru/` in `public_html/`

---

## PART 3 — Configure .env

1. In File Manager, open `public_html/.env` (or create from `.env.hostinger`)
2. Fill in:

```env
APP_NAME="BT Guru"
APP_ENV=production
APP_KEY=                         ← generate this (see step below)
APP_DEBUG=false
APP_URL=https://btguru.tech
APP_DOMAIN=btguru.tech

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_btguru   ← your actual DB name
DB_USERNAME=u123456789_btguru   ← your actual DB user
DB_PASSWORD=YOUR_DB_PASSWORD

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

CENTRAL_DOMAIN=btguru.tech
ADMIN_SUBDOMAIN=admin
TENANT_SUBDOMAIN_SUFFIX=.btguru.tech
```

---

## PART 4 — Run Artisan Commands

### If you have SSH access (VPS or Business plan):
```bash
cd public_html
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### If NO SSH (shared hosting File Manager only):
Create a temporary file `public_html/setup.php`:

```php
<?php
// DELETE THIS FILE IMMEDIATELY AFTER USE
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";

// Generate key
$kernel->call('key:generate');
echo "KEY: " . $kernel->output();

// Run migrations
$kernel->call('migrate', ['--force' => true]);
echo "MIGRATE: " . $kernel->output();

// Storage link
$kernel->call('storage:link');
echo "STORAGE: " . $kernel->output();

echo "DONE. DELETE THIS FILE NOW!";
echo "</pre>";
```

Visit `https://btguru.tech/setup.php` once → **delete it immediately after**.

---

## PART 5 — Storage Permissions

In File Manager, set these folder permissions to **755**:
- `storage/`
- `storage/app/`
- `storage/framework/`
- `storage/logs/`
- `bootstrap/cache/`

---

## PART 6 — Verify Everything Works

| URL | Expected |
|-----|----------|
| `https://btguru.tech` | Landing page |
| `https://admin.btguru.tech/login` | Super admin login |
| `https://btguru.tech/register` | Registration wizard step 1 |
| `https://futureacademy.btguru.tech/login` | Tenant login (after seeding) |

---

## Troubleshooting

**500 Error** → Check `storage/logs/laravel.log`, set `APP_DEBUG=true` temporarily

**Subdomain not working** → Wildcard subdomain `*` not added in hPanel, or DNS not propagated yet (wait up to 24h)

**Class not found** → Run `composer dump-autoload` via SSH or re-upload `vendor/`

**Session/cookie issues** → Set `SESSION_DOMAIN=.btguru.tech` in `.env` so cookies work across all subdomains

---

## Important: SESSION_DOMAIN

Add this to `.env` — **critical for subdomain cookie sharing**:
```env
SESSION_DOMAIN=.btguru.tech
```
