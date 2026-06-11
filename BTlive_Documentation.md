# BTlive System Documentation

## Overview
BTlive is a Laravel-based multi-tenant live class system with video conferencing capabilities.

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER BROWSER                             │
│              (HTTPS: ecchapuron.btguru.tech)                     │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CLOUDFLARE TUNNEL                             │
│         (Routes traffic to server without public IP)             │
│              meet.btguru.tech → 145.223.19.77                  │
└────────────────────┬────────────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
┌──────────────────┐    ┌──────────────────┐
│   LARAVEL APP    │    │   LIVEKIT SERVER │
│  (XAMPP/Apache)  │    │    (Docker)      │
│  Port: 80/443    │    │  Port: 7880-7882 │
│                  │    │                  │
│  Teacher/Student │    │  WebRTC Video    │
│  Management      │    │  Conferencing    │
└────────┬─────────┘    └──────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    MYSQL DATABASE (XAMPP)                        │
│  - Schools/Teachers                                              │
│  - Students                                                     │
│  - Live Classes                                                 │
│  - Attendance Records                                           │
└─────────────────────────────────────────────────────────────────┘
```

## Components

### 1. Laravel Application (btguru)
- **Location**: `c:\xampp\htdocs\Bt_Guru`
- **Web Server**: Apache (XAMPP)
- **Domain**: `ecchapuron.btguru.tech`
- **PHP Version**: 8.x
- **Database**: MySQL (XAMPP)

#### Key Features:
- Multi-tenant school management
- Teacher room creation (`teacher_room.blade.php`)
- Student joining interface
- Live class scheduling
- Attendance tracking
- Invoice generation

#### Key Files:
- `resources/views/btlive/teacher_room.blade.php` - Teacher interface with video
- `resources/views/btlive/student_room.blade.php` - Student interface
- `routes/web.php` - Route definitions
- `app/Models/` - Database models

### 2. Video Conferencing (LiveKit)
- **Location**: `/root/livekit` (Server)
- **Docker Container**: `livekit-livekit-1`
- **Ports**:
  - `7880` - HTTP API/WebSocket
  - `7881` - WebRTC TCP
  - `7882/udp` - WebRTC UDP
- **Domain**: `meet.btguru.tech`

#### Docker Configuration:
```yaml
version: "3.8"
services:
  livekit:
    image: livekit/livekit-server:latest
    command: --dev --bind 0.0.0.0
    ports:
      - "7880:7880"
      - "7881:7881"
      - "7882:7882/udp"
    environment:
      LIVEKIT_KEYS: "APIKEY: secret123456789"
```

#### API Credentials:
- **API Key**: `APIKEY`
- **API Secret**: `secret123456789`

### 3. Cloudflare Tunnel
- **Purpose**: Expose server to internet without public IP
- **Container**: `cloudflared`
- **Token**: `eyJhIjoiMGFiMTkxOTg0ZGUyYzcxOGIwNWM2MjY0ZmUzZTljNTQiLCJ0IjoiYTM2Y2JkNjUtYjMzMC00MzkyLTlkNjAtZThlZjRjZjI3NmM3IiwicyI6IlltWmxPVEpqWWpRdFl6VTFNaTAwWmpneUxXRm1aV0V0WmprNE5EUmxNV0ppTVRWa1pEazBaamMxTlRZdFltUmhNUzAwTjJFekxXSTRaR0l0TnpFM01tTTNaVFZqT1RVNCJ9`
- **Dashboard**: https://one.dash.cloudflare.com/

#### Tunnel Configuration:
```
meet.btguru.tech → http://172.19.0.2:7880 (LiveKit)
```

## Network Architecture

### Server Details:
- **IP**: `145.223.19.77`
- **OS**: Ubuntu/Linux
- **Docker Networks**:
  - `livekit_default` - LiveKit isolated network

### Port Mappings:
| Service | Host Port | Container Port | Protocol |
|---------|-----------|----------------|----------|
| LiveKit API | 7880 | 7880 | TCP |
| LiveKit WebRTC TCP | 7881 | 7881 | TCP |
| LiveKit WebRTC UDP | 7882 | 7882 | UDP |

### Firewall Rules:
```bash
ufw allow 7880/tcp
ufw allow 7881/tcp
ufw allow 7882/udp
```

## Data Flow

### Teacher Creates Room:
1. Teacher logs into Laravel app
2. Creates new live class
3. System generates room URL with LiveKit token
4. Teacher joins via `teacher_room.blade.php`

### Student Joins Room:
1. Student receives room link
2. Opens `student_room.blade.php`
3. Enters name and joins
4. System authenticates and connects to LiveKit
5. Video/audio stream established

## LiveKit Integration in Laravel

### JavaScript SDK Usage:
```javascript
// Connect to LiveKit
const wsUrl = 'wss://meet.btguru.tech';
const token = '{{ $livekitToken }}';

const room = new LivekitClient.Room();
await room.connect(wsUrl, token);
```

### Token Generation (Server-side):
```php
// Generate LiveKit access token
$apiKey = 'APIKEY';
$apiSecret = 'secret123456789';

// Create token for user
$token = \Livekit\AccessToken::create($apiKey, $apiSecret)
    ->setIdentity($userId)
    ->setName($userName)
    ->addGrant(['roomJoin' => true, 'room' => $roomName])
    ->toJwt();
```

## File Structure

### Laravel Views:
```
resources/views/btlive/
├── teacher_room.blade.php      # Teacher video interface
├── student_room.blade.php      # Student video interface
├── teacher_list.blade.php      # Teacher management
├── student_list.blade.php      # Student management
├── live_class.blade.php        # Class scheduling
└── index.blade.php             # Dashboard
```

### Key Routes (routes/web.php):
```php
Route::get('/btlive', [BtliveController::class, 'index']);
Route::get('/btlive/teacher-room/{id}', [BtliveController::class, 'teacherRoom']);
Route::get('/btlive/student-room/{id}', [BtliveController::class, 'studentRoom']);
```

## Troubleshooting Guide

### Issue: 522 Error (Cloudflare)
**Cause**: Tunnel can't reach origin server
**Fix**:
```bash
# Check LiveKit is running
docker ps | grep livekit

# Check tunnel logs
docker logs cloudflared | tail -20

# Update Cloudflare dashboard with correct IP
docker inspect livekit-livekit-1 --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}'
```

### Issue: Port Already in Use
**Fix**:
```bash
# Find and kill process using port
ss -tlnp | grep :7880
kill -9 <PID>

# Or use different port in docker-compose.yml
```

### Issue: Docker Container Restart Loop
**Fix**:
```bash
# Check logs
docker logs <container-name>

# Clear config and restart
rm -rf ~/.jitsi-meet-cfg/*  # For Jitsi
docker compose down && docker compose up -d
```

### Issue: Cross-Origin Errors in Browser
**Fix**: Update `teacher_room.blade.php`:
```javascript
// Use postMessage instead of direct iframe access
window.addEventListener('message', (event) => {
    if (event.origin !== 'https://meet.btguru.tech') return;
    // Handle messages
});
```

## Scaling for Production (100+ tenants, 5000 users)

### Current Limitations:
- Single LiveKit instance: ~500 concurrent users
- Single server architecture

### Recommended Production Setup:
1. **Multiple LiveKit Servers**: Deploy 10+ instances with load balancer
2. **Redis**: For presence and session management
3. **Separate Database Server**: MySQL/PostgreSQL cluster
4. **CDN**: CloudFlare for static assets
5. **Monitoring**: Prometheus + Grafana

### LiveKit Cluster Architecture:
```
Load Balancer (HAProxy/NGINX)
    ├── LiveKit Node 1 (7880)
    ├── LiveKit Node 2 (7880)
    ├── LiveKit Node 3 (7880)
    └── ...
```

## Deployment Commands

### Start Fresh (LiveKit):
```bash
# 1. Cleanup
cd /root/livekit && docker compose down
docker stop cloudflared && docker rm cloudflared
docker system prune -f

# 2. Start LiveKit
cd /root/livekit
docker compose up -d

# 3. Get IP and start tunnel
LIVEKIT_IP=$(docker inspect livekit-livekit-1 --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' | awk '{print $1}')
docker run -d --name cloudflared --network livekit_default cloudflare/cloudflared:latest tunnel --no-autoupdate run --token <TOKEN> --url http://livekit-livekit-1:7880
```

### Laravel App (XAMPP):
```bash
# Ensure XAMPP services running
sudo /opt/lampp/lampp start

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Security Considerations

1. **LiveKit Token**: Generate on server, never expose API secret to client
2. **Room Names**: Use UUIDs or hashed IDs to prevent guessing
3. **Authentication**: Verify user session before generating tokens
4. **HTTPS**: Always use HTTPS for WebRTC (required by browsers)
5. **Rate Limiting**: Implement on token generation endpoint

## API Endpoints

### Laravel Backend:
- `GET /api/livekit/token?room={room}&user={user}` - Generate LiveKit token
- `POST /api/live-class/create` - Create new live class
- `GET /api/attendance/{class_id}` - Get attendance list

### LiveKit Server:
- `GET /` - Health check (returns "OK")
- WebSocket `/` - Client connections
- `/twirp/livekit.RoomService/` - Management API

## Environment Variables

### Laravel (.env):
```env
LIVEKIT_API_KEY=APIKEY
LIVEKIT_API_SECRET=secret123456789
LIVEKIT_URL=wss://meet.btguru.tech
```

### LiveKit Docker:
See `/root/livekit/docker-compose.yml`

## Backup and Recovery

### Database:
```bash
# MySQL backup
mysqldump -u root -p btguru > backup_$(date +%Y%m%d).sql
```

### LiveKit Config:
```bash
# Backup docker-compose
cp /root/livekit/docker-compose.yml /backup/
```

## Support Contacts

- **Server Admin**: root@srv1367539
- **Cloudflare Dashboard**: https://one.dash.cloudflare.com/
- **LiveKit Docs**: https://docs.livekit.io/

---

**Last Updated**: June 8, 2026
**Version**: 1.0
**Author**: System Administrator
