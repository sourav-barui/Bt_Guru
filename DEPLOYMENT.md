# Bt_Guru Deployment Documentation

## Table of Contents
1. [Server Overview](#server-overview)
2. [Initial Setup](#initial-setup)
3. [Git & SSH Configuration](#git--ssh-configuration)
4. [Docker & Dokploy Setup](#docker--dokploy-setup)
5. [Nginx Configuration](#nginx-configuration)
6. [SSL Certificates](#ssl-certificates)
7. [Database](#database)
8. [Environment Variables](#environment-variables)
9. [Auto-Fix Scripts](#auto-fix-scripts)
10. [Adding New Tenants](#adding-new-tenants)
11. [Updating the Application](#updating-the-application)
12. [Troubleshooting](#troubleshooting)
13. [Cloudflare Migration (Recommended)](#cloudflare-migration-recommended)

---

## Server Overview

| Item | Value |
|------|-------|
| **Server IP** | `145.223.19.77` |
| **Provider** | Dokploy (Docker-based PaaS) |
| **OS** | Ubuntu (host) |
| **App Domain** | `btguru.tech` |
| **Admin Domain** | `admin.btguru.tech` |
| **Tenant Domains** | `*.btguru.tech` (wildcard) |
| **App Port** | `8080` (host) → `80` (container) |

---

## Initial Setup

### 1. Connect to Server

```bash
ssh root@145.223.19.77
```

### 2. Install Docker & Docker Compose (if not present)

```bash
# Dokploy usually pre-installs these, but verify:
docker --version
docker-compose --version
```

### 3. Dokploy Panel

Access Dokploy UI at: `http://145.223.19.77:3000`

---

## Git & SSH Configuration

### 1. Add SSH Key to GitHub

Generate SSH key on server (if not exists):

```bash
ssh-keygen -t ed25519 -C "dokploy@btguru.tech"
cat ~/.ssh/id_ed25519.pub
```

Copy the output and add to: **GitHub → Settings → SSH and GPG keys → New SSH key**

### 2. Test GitHub Connection

```bash
ssh -T git@github.com
# Expected: Hi username! You've successfully authenticated...
```

### 3. Clone Repository (or configure Dokploy)

In Dokploy UI:
1. Create new **Application**
2. Source: GitHub
3. Repository: `your-username/Bt_Guru`
4. Branch: `main`

### 4. GitHub Webhook (Auto-deploy)

1. In Dokploy → Application → Webhooks
2. Copy the webhook URL
3. GitHub → Repository → Settings → Webhooks → Add webhook
4. Paste Dokploy webhook URL
5. Content type: `application/json`
6. Events: **Just the push event**

Now every push to `main` auto-deploys.

---

## Docker & Dokploy Setup

### Port Mapping (Critical)

Docker Swarm routing mesh had issues on this server. We use `mode=host` for port binding.

**Current working setup:**
- Host port: `8080`
- Container port: `80`
- Mode: `host`

### Auto-Fix Cron Job

A cron job runs every minute to ensure port `8080` stays mapped:

```bash
# Script location: /usr/local/bin/ensure-btguru-port.sh
# Cron entry:
* * * * * /usr/local/bin/ensure-btguru-port.sh
```

To verify it's working:

```bash
crontab -l
ss -tlnp | grep :8080
```

### Dockerfile Requirements

Ensure these are in your `Dockerfile`:

```dockerfile
# Install Node.js for Vite build
RUN apk add --no-cache nodejs npm

# Build assets
COPY . .
RUN npm install && npm run build

# Production .env
RUN cat > .env <<EOF
APP_ENV=production
APP_URL=https://btguru.tech
APP_KEY=base64:4XUYkqQw2E6rSifjMPitm5VYnvB8VePOM2DMBQykpaE=
DB_HOST=btguru-btgurudb-red7t9
DB_DATABASE=bt_guru
DB_USERNAME=bt_guru
DB_PASSWORD=jJpEplhZcgbMzVQDhXzN
SESSION_DOMAIN=.btguru.tech
CENTRAL_DOMAIN=btguru.tech
ADMIN_SUBDOMAIN=admin
EOF
```

### Important: No Route Caching

The `docker/start.sh` must **NOT** include `php artisan route:cache` (breaks conditional subdomain routes):

```bash
# docker/start.sh
php artisan config:cache
php artisan view:cache
echo "SKIPPED route:cache (incompatible with conditional subdomain routes)"
php artisan storage:link
```

---

## Nginx Configuration

### Host Nginx (Reverse Proxy)

File: `/etc/nginx/sites-available/btguru`

```nginx
server {
    listen 443 ssl;
    server_name btguru.tech admin.btguru.tech *.btguru.tech;

    ssl_certificate /etc/letsencrypt/live/btguru.tech/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/btguru.tech/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

# Redirect www to non-www
server {
    listen 443 ssl;
    server_name www.btguru.tech;
    ssl_certificate /etc/letsencrypt/live/btguru.tech/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/btguru.tech/privkey.pem;
    return 301 https://btguru.tech$request_uri;
}

# HTTP → HTTPS redirect
server {
    listen 80;
    server_name btguru.tech www.btguru.tech admin.btguru.tech *.btguru.tech;
    return 301 https://$host$request_uri;
}
```

### Container Nginx

File: `docker/nginx.conf`

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param HTTPS $http_x_forwarded_proto;  # For HTTPS detection
        include fastcgi_params;
    }
}
```

### HTTPS Detection in Laravel

File: `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

---

## SSL Certificates

### Current: Let's Encrypt (Manual Per Subdomain)

**Problem:** Each new subdomain requires manual certbot command.

**Initial setup:**
```bash
certbot --nginx -d btguru.tech -d www.btguru.tech -d admin.btguru.tech -d futureacademy.btguru.tech --expand --non-interactive
```

**Adding a new tenant subdomain:**
```bash
certbot --nginx -d "newtenant.btguru.tech" --expand --non-interactive
nginx -s reload
```

**Check certificates:**
```bash
certbot certificates
```

**Auto-renewal:**
```bash
systemctl status certbot.timer
```

### Recommended: Cloudflare (Automatic for All Subdomains)

See [Cloudflare Migration section](#cloudflare-migration-recommended) below.

---

## Database

### MySQL Container

| Setting | Value |
|---------|-------|
| Host | `btguru-btgurudb-red7t9` (Docker service name) |
| Port | `3306` |
| Database | `bt_guru` |
| Username | `bt_guru` |
| Password | `jJpEplhZcgbMzVQDhXzN` |

### Import SQL Dump

```bash
# Copy dump to container
docker cp bt_guru.sql btguru-btgurudb-red7t9.1.xxxxx:/tmp/

# Import
docker exec -i btguru-btgurudb-red7t9.1.xxxxx mysql -u bt_guru -pjJpEplhZcgbMzVQDhXzN bt_guru < /tmp/bt_guru.sql
```

### Tinker Commands

```bash
docker exec -it btguru-btguruapp-ftabnk.1.xxxxx php artisan tinker

# Check tenants
Tenant::all();
Tenant::where('subdomain', 'futureacademy')->first();
```

---

## Environment Variables

### Production .env (inside container)

```env
APP_NAME="Bt Guru"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://btguru.tech
APP_KEY=base64:4XUYkqQw2E6rSifjMPitm5VYnvB8VePOM2DMBQykpaE=

DB_CONNECTION=mysql
DB_HOST=btguru-btgurudb-red7t9
DB_PORT=3306
DB_DATABASE=bt_guru
DB_USERNAME=bt_guru
DB_PASSWORD=jJpEplhZcgbMzVQDhXzN

SESSION_DOMAIN=.btguru.tech
SESSION_SECURE_COOKIE=true

CENTRAL_DOMAIN=btguru.tech
ADMIN_SUBDOMAIN=admin
```

### Important: Subdomain Routing Variables

```env
CENTRAL_DOMAIN=btguru.tech        # Root domain for central routes
ADMIN_SUBDOMAIN=admin             # Admin panel subdomain
```

---

## Auto-Fix Scripts

### Port Fix Script

**File:** `/usr/local/bin/ensure-btguru-port.sh`

```bash
#!/bin/bash
if ! ss -tlnp | grep -q ':8080'; then
    docker service update --publish-add published=8080,target=80,mode=host btguru-btguruapp-ftabnk >/dev/null 2>&1
fi
```

**Installed via cron:**
```bash
* * * * * /usr/local/bin/ensure-btguru-port.sh
```

This ensures port 8080 is always mapped after redeploys.

---

## Adding New Tenants

### 1. Create Tenant in Application

Use the registration system in your Laravel app.

### 2. DNS (if not using wildcard)

With wildcard A record (`*.btguru.tech → 145.223.19.77`), no DNS changes needed.

### 3. SSL (if using Let's Encrypt)

```bash
certbot --nginx -d "tenantname.btguru.tech" --expand --non-interactive
nginx -s reload
```

### 4. Test URLs

| URL | Expected Result |
|-----|----------------|
| `http://tenantname.btguru.tech` | Landing page |
| `https://tenantname.btguru.tech/login` | Login page (if SSL added) |

### 5. Default Tenant Credentials

| Role | Email | Password |
|------|-------|----------|
| Tenant Admin | `admin@tenantdomain.com` | `TenantAdmin@123` |
| Teacher | `sarah@tenantdomain.com` | `Teacher@123` |

---

## Updating the Application

### Method 1: Git Push (Recommended)

```bash
# Local development
git add .
git commit -m "Update: ..."
git push origin main
```

Dokploy auto-deploys via webhook.

### Method 2: Manual Deploy in Dokploy

1. Dokploy UI → Application → Deploy

### Method 3: Force Redeploy via SSH

```bash
docker service update --force btguru-btguruapp-ftabnk
```

### After Deploy: Clear Caches

```bash
docker exec btguru-btguruapp-ftabnk.1.xxxxx php artisan optimize:clear
docker exec btguru-btguruapp-ftabnk.1.xxxxx php artisan config:cache
docker exec btguru-btguruapp-ftabnk.1.xxxxx php artisan view:cache
```

### Port Auto-Recovery

After redeploy, wait **60 seconds** for the cron job to restore port 8080, or run:

```bash
/usr/local/bin/ensure-btguru-port.sh
```

---

## Troubleshooting

### 504 Gateway Timeout

**Cause:** Docker Swarm routing mesh not working.

**Fix:** Port mapping uses `mode=host`. Wait 60 seconds for cron job, or run manually:

```bash
/usr/local/bin/ensure-btguru-port.sh
```

### 404 Not Found (Wrong Domain Detection)

**Cause:** Nginx not passing `Host` header correctly.

**Fix:** Ensure container Nginx config has correct `fastcgi_param` and Laravel uses `Request::getHost()`.

### CSS/Assets Not Loading (Mixed Content)

**Cause:** Laravel generating HTTP URLs instead of HTTPS.

**Fix:** Check `app/Providers/AppServiceProvider.php`:

```php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

### "Not Secure" on New Tenant

**Cause:** New subdomain not in Let's Encrypt certificate.

**Fix:**
```bash
certbot --nginx -d "newtenant.btguru.tech" --expand --non-interactive
nginx -s reload
```

### Database Connection Error

```bash
# Check DB container is running
docker ps | grep btgurudb

# Check logs
docker logs btguru-btgurudb-red7t9.1.xxxxx

# Test connection from app container
docker exec btguru-btguruapp-ftabnk.1.xxxxx php artisan db:monitor
```

### Redirect to Hostinger CDN

**Cause:** DNS ALIAS records overriding wildcard A record.

**Fix:** Delete all ALIAS records in Hostinger DNS, keep only:
- A record: `@ → 145.223.19.77`
- A record: `admin → 145.223.19.77`
- A record: `* → 145.223.19.77`

---

## Cloudflare Migration (Recommended)

Cloudflare provides **free automatic SSL for all subdomains**, including future ones.

### Benefits

- ✅ Automatic SSL for `*.btguru.tech`
- ✅ No certbot commands needed
- ✅ New tenants get HTTPS instantly
- ✅ DDoS protection and CDN caching
- ✅ No manual domain additions

### Steps

#### 1. Sign Up

Go to [cloudflare.com](https://cloudflare.com) and create an account.

#### 2. Add Your Domain

- Click **Add a Site**
- Enter: `btguru.tech`
- Select **Free Plan**

#### 3. Review DNS Records

Cloudflare scans existing DNS. Ensure these records exist:

| Type | Name | Content | Proxy Status |
|------|------|---------|-------------|
| A | `@` | `145.223.19.77` | 🟠 Proxied |
| A | `admin` | `145.223.19.77` | 🟠 Proxied |
| A | `*` | `145.223.19.77` | 🟠 Proxied |

Click **Continue**.

#### 4. Change Nameservers at Hostinger

Cloudflare gives you 2 nameservers (e.g.):
- `lara.ns.cloudflare.com`
- `greg.ns.cloudflare.com`

In Hostinger:
1. Go to **Domains** → `btguru.tech`
2. Find **Nameservers** or **DNS/Nameservers**
3. Replace Hostinger's nameservers with Cloudflare's
4. Save

#### 5. Wait for Propagation

Usually **5-30 minutes**. Check status in Cloudflare dashboard.

#### 6. Configure SSL/TLS

In Cloudflare dashboard:
1. Go to **SSL/TLS** → **Overview**
2. Set encryption mode to **Full (strict)**

#### 7. Update Server Nginx (Remove SSL)

On your server, remove Let's Encrypt and simplify Nginx:

```bash
# Delete certbot certificate
certbot delete --cert-name btguru.tech

# Update Nginx config
cat > /etc/nginx/sites-available/btguru << 'EOF'
server {
    listen 80;
    server_name btguru.tech www.btguru.tech admin.btguru.tech *.btguru.tech;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF

nginx -t && systemctl reload nginx
```

#### 8. Update Laravel .env

```env
# Cloudflare handles HTTPS, but Laravel should still force HTTPS
APP_URL=https://btguru.tech
```

#### 9. Test

| URL | Expected |
|-----|----------|
| `https://btguru.tech` | ✅ Works |
| `https://admin.btguru.tech/login` | ✅ Works |
| `https://futureacademy.btguru.tech/login` | ✅ Works |
| `https://newtenant.btguru.tech` | ✅ Works automatically |

#### 10. Remove Certbot Auto-Renewal

```bash
systemctl stop certbot.timer
systemctl disable certbot.timer
```

---

## Important Files Summary

| File | Purpose |
|------|---------|
| `/etc/nginx/sites-available/btguru` | Host Nginx reverse proxy config |
| `/usr/local/bin/ensure-btguru-port.sh` | Auto-fix Docker port mapping |
| `/etc/crontab` or `crontab -l` | Cron job running every minute |
| `docker/nginx.conf` | Container Nginx config |
| `docker/start.sh` | Container startup script |
| `app/Providers/AppServiceProvider.php` | Laravel HTTPS forcing |
| `routes/web.php` | Subdomain routing logic |

---

## Quick Reference Commands

```bash
# SSH into server
ssh root@145.223.19.77

# Check port mapping
ss -tlnp | grep :8080

# Fix port mapping manually
docker service update --publish-add published=8080,target=80,mode=host btguru-btguruapp-ftabnk

# Check containers
docker ps

# View app logs
docker logs --tail 50 btguru-btguruapp-ftabnk.1.xxxxx

# Laravel tinker
docker exec -it btguru-btguruapp-ftabnk.1.xxxxx php artisan tinker

# Clear caches
docker exec btguru-btguruapp-ftabnk.1.xxxxx php artisan optimize:clear

# Add SSL for new subdomain
certbot --nginx -d "newtenant.btguru.tech" --expand --non-interactive

# Reload Nginx
nginx -s reload

# Check SSL certificates
certbot certificates
```

---

**Last Updated:** May 30, 2026
**Server:** 145.223.19.77
**Status:** Production Ready
