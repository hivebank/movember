# FormFlow - Simple PHP Setup

## 🚀 Super Simple Setup

This is a **plug-and-play** PHP application. No build steps, no npm, no Node.js needed!

### Requirements

- PHP 8.2+
- MongoDB
- Apache2 (already configured)
- Composer

### Installation

```bash
# 1. Clone the repo
git clone https://github.com/hivebank/movember.git
cd movember

# 2. Install PHP dependencies
cd backend
composer install

# 3. Setup environment
cp .env.example .env
# Edit .env with your settings

# 4. Setup MongoDB
cd ..
mongosh < setup-mongodb.js

# 5. Point Apache DocumentRoot to this folder
# DocumentRoot /path/to/movember

# Done! Visit your domain
```

### File Structure

```
movember/
├── index.php           # Landing page
├── login.php           # Login page
├── register.php        # Registration page
├── dashboard.php       # Dashboard
├── pricing.php         # Pricing page
├── logout.php          # Logout handler
├── backend/            # API and business logic
│   ├── public/         # API entry point
│   └── src/            # Controllers, Models, Services
├── includes/           # Shared PHP includes
│   ├── config.php      # Configuration
│   ├── header.php      # Header template
│   └── footer.php      # Footer template
├── assets/             # CSS, JS, images
│   ├── css/style.css   # Styles
│   └── js/app.js       # JavaScript
└── .htaccess           # Apache rewrite rules
```

### Apache Configuration

Your existing Apache config should work! Just point DocumentRoot to this folder:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/movember

    <Directory /path/to/movember>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Backend API

The backend API is in `/backend/public/api/`

Examples:
- `POST /backend/public/api/auth/login` - Login
- `GET /backend/public/api/forms` - List forms
- `POST /backend/public/api/forms` - Create form

The PHP pages use jQuery AJAX to call these APIs.

### Features

✅ User registration and login
✅ Dashboard with form management
✅ Beautiful, responsive design
✅ MongoDB integration
✅ Stripe subscription support
✅ No build step required
✅ Works immediately after git clone

### Development

No special development server needed! Just:

1. Point Apache to this directory
2. Edit PHP files
3. Refresh browser
4. That's it!

### For Full Documentation

See:
- `ARCHITECTURE.md` - System architecture
- `DEPLOYMENT.md` - Production deployment
- `TESTING_GUIDE.md` - Detailed testing guide

---

**That's it! Just plain PHP, no complexity.** 🎉
