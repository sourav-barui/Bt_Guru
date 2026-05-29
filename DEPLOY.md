# Dokploy Deployment Guide

## Prerequisites
- Git repository pushed to GitHub/GitLab
- Dokploy server (Ubuntu 24.04) ready
- Domain pointing to your Dokploy server

## Files Overview

| File | Purpose |
|------|---------|
| `Dockerfile` | Production-ready PHP 8.4 + Nginx + Supervisor image |
| `docker-compose.yml` | Multi-service: app + queue worker + scheduler |
| `docker/nginx.conf` | Nginx server block |
| `docker/supervisord.conf` | Supervisor for single-container mode |
| `docker/start.sh` | Container startup (cache, migrate, link) |
| `docker/php.ini` | Production PHP limits |
| `docker/opcache.ini` | OPcache performance tuning |
| `.dockerignore` | Excludes dev files from build |

## Deployment Steps

### 1. Push code with Docker config

```bash
git add .
git commit -m "Add Dokploy deployment config"
git push origin main
```

### 2. Create Application in Dokploy

1. Go to **Projects → Add Application**
2. Source: **Git Provider** (connect GitHub/GitLab)
3. Select repository and branch `main`
4. Build type: **`docker-compose.yml`**
5. Click **Create**

### 3. Add Environment Variables

In Dokploy → Your App → Environment:

```env
APP_NAME=Bt_Guru
APP_ENV=production
APP_DEBUG=false
APP_KEY=          # Run: php artisan key:generate locally, paste value
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=          # Fill after creating MySQL service in Dokploy
DB_PORT=3306
DB_DATABASE=bt_guru
DB_USERNAME=bt_guru
DB_PASSWORD=      # Strong password

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
RAZORPAY_KEY_ID=
RAZORPAY_KEY_SECRET=
```

### 4. Create MySQL Database Service

1. Dokploy → Services → **MySQL**
2. Database name: `bt_guru`
3. User: `bt_guru`
4. Set password
5. Link to your application (Dokploy sets `DB_HOST` automatically)

### 5. Domain & SSL

1. Your App → Domains → Add domain
2. Enter: `yourdomain.com`
3. Enable **HTTPS** (Let's Encrypt)
4. Dokploy auto-configures Traefik reverse proxy + SSL

### 6. Deploy

Click **Deploy** in Dokploy. First build may take 3-5 minutes.

### 7. First Run (Post-Deploy)

Go to Dokploy → Your App → **Exec / Console**:

```bash
php artisan key:generate --show
# Copy the key to APP_KEY env var, then restart app

php artisan migrate --force
php artisan storage:link
```

Restart the app after setting `APP_KEY`.

## Architecture

```
Dokploy (Traefik + SSL)
├── App Service (Port 80)
│   ├── Nginx
│   ├── PHP-FPM
│   └── Laravel OPcached
├── Queue Worker (background jobs)
├── Scheduler (cron every minute)
└── MySQL Service (linked)
```

## Updating

Just push to `main` — Dokploy auto-rebuilds on new commits (if auto-deploy is enabled).

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Build fails | Check Dokploy build logs; usually missing `.env` or `APP_KEY` |
| 502 Bad Gateway | App still starting; wait 30s. Check `docker logs` |
| Queue not processing | Ensure `QUEUE_CONNECTION=database` or add Redis service |
| Storage 404 | Run `php artisan storage:link` via console |
| Health check fails | Route `/up` exists (Laravel 12 native); verify app boots |
