# 🚀 Quick Deploy Guide

## Zero-Build Deployment (Apache + PHP already configured)

If your server already has Apache2 + PHP + MongoDB configured:

```bash
# 1. Clone the repository
git clone https://github.com/hivebank/movember.git
cd movember

# 2. Install PHP dependencies
cd backend
composer install

# 3. Setup environment
cp .env.example .env
# Edit .env - set JWT_SECRET, MongoDB URI, Stripe keys

# 4. Setup database
mongosh < ../setup-mongodb.js

# 5. Point Apache to the repository
# Set DocumentRoot to: /path/to/movember/frontend/dist
# See apache-config.conf for full example

# Done! The frontend is pre-built and ready to serve.
```

## Apache VirtualHost Configuration

```apache
<VirtualHost *:80>
    ServerName yourdomain.com

    # Frontend - Pre-built static files
    DocumentRoot /path/to/movember/frontend/dist

    <Directory /path/to/movember/frontend/dist>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Handle Vue Router
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>

    # Backend API
    Alias /api /path/to/movember/backend/public

    <Directory /path/to/movember/backend/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## That's it!

No Node.js required on production server. The `frontend/dist/` folder contains all pre-built static files ready to serve with Apache.

For development or rebuilding the frontend, see `TESTING_GUIDE.md`.
