# BT Guru - Multi-Tenant Coaching Centre SaaS Platform

A modern, scalable, production-ready Coaching Centre SaaS Platform built with Laravel 12+.

## Features

### Multi-Tenant Architecture
- **Subdomain-based routing** - Each tenant gets their own subdomain (e.g., `futureacademy.btguru.in`)
- **Custom domain support** - Tenants can connect their own domain
- **Complete tenant isolation** - Secure data separation between tenants
- **Scalable architecture** - Designed for thousands of tenants

### Role-Based Access Control
- **Super Admin** - Platform owner with full control
- **Tenant Admin** - Coaching centre owner with tenant-specific access
- **Teacher** - Course-specific access with limited permissions
- **Student** - Mobile-first student portal for course access

### Core Modules
- **Course Management** - Create, manage, and assign teachers to courses
- **Student Enrollment** - Admission workflow with payment status tracking
- **Teacher Management** - Assign multiple teachers to courses
- **Fee Management** - Track payments and outstanding balances
- **Notices System** - Post announcements for students and teachers
- **Responsive Design** - Mobile-first approach for all user types

### Technical Stack
- **Laravel 12+** - Modern PHP framework
- **PHP 8.3+** - Latest PHP version
- **MySQL** - Primary database
- **Tailwind CSS** - Utility-first CSS framework
- **Spatie Laravel Permission** - Role-based access control
- **Blade + Alpine.js** - Frontend templating and reactivity

## Installation

### Requirements
- PHP 8.3 or higher
- MySQL 8.0 or higher
- Composer
- Node.js 18+ and NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   cd c:\xampp\htdocs\Bt_Guru
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bt_guru
   DB_USERNAME=root
   DB_PASSWORD=
   
   CENTRAL_DOMAIN=btguru.test
   ADMIN_SUBDOMAIN=admin
   ```

6. **Create database**
   ```sql
   CREATE DATABASE bt_guru CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

7. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

8. **Build assets**
   ```bash
   npm run build
   ```

9. **Setup storage link**
   ```bash
   php artisan storage:link
   ```

## Local Development with XAMPP

### Windows Hosts Configuration
Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1  btguru.test
127.0.0.1  admin.btguru.test
127.0.0.1  futureacademy.btguru.test
127.0.0.1  *.btguru.test
```

### Apache Virtual Hosts
Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/Bt_Guru/public"
    ServerName btguru.test
    ServerAlias *.btguru.test
    <Directory "C:/xampp/htdocs/Bt_Guru/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Restart Apache after configuration.


## URL Structure

### Central Domain (btguru.test)
- `/` - Landing page
- `/register` - Tenant registration
- `/about`, `/pricing`, `/contact` - Information pages

### Admin Subdomain (admin.btguru.test)
- `/login` - Super Admin login
- `/dashboard` - Super Admin dashboard
- `/tenants` - Tenant management
- `/domains` - Custom domain management

### Tenant Subdomains (*.btguru.test)
- `/` - Tenant landing page
- `/login` - Admin/Teacher login
- `/student/login` - Student login
- `/student/register` - Student registration
- `/dashboard` - Tenant Admin dashboard
- `/admin/courses`, `/admin/teachers`, `/admin/students` - Management pages
- `/teacher/dashboard` - Teacher dashboard
- `/student/dashboard` - Student dashboard

## Architecture

### Multi-Tenant Data Isolation
All models use `tenant_id` for automatic scoping:
- Users (with role-based access)
- Courses
- Enrollments
- Notices

### Middleware
- `TenantMiddleware` - Identifies tenant from subdomain
- `DomainMiddleware` - Enforces domain restrictions
- `RoleMiddleware` - Role-based access control
- `SuperAdminMiddleware` - Super admin access only

### Models with Tenant Scoping
```php
// Automatic tenant scoping on all queries
User::where(...) // Automatically adds tenant_id scope
```

## Future Features (Placeholders Ready)

The following features are prepared with placeholder UI:
- Live Classes integration
- Online Exams module
- Attendance tracking
- WhatsApp Notifications
- SMS Gateway
- Online Payment Gateway
- Mobile App API
- AI Features
- Study Materials library
- Video Classes
- Certificate generation

## Security

- CSRF protection on all forms
- Password hashing (Bcrypt)
- Tenant isolation at middleware level
- Role-based authorization with policies
- Secure authentication flows

## Development Commands

```bash
# Run development server
php artisan serve

# Watch assets
npm run dev

# Run tests
php artisan test

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## License

This project is proprietary software. All rights reserved.

## Support

For support and inquiries, contact: support@btguru.in
Then access your platform:

