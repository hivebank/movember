# FormFlow - Zero-Setup Guide

## ✅ Git Clone and Play!

This is a **zero-setup, plug-and-play PHP application** with modern UI. No .env files, no configuration needed!

---

## 🚀 One-Command Setup

### The Easy Way (Recommended)

```bash
# Clone the repo
git clone https://github.com/hivebank/movember.git
cd movember

# Run the automatic setup script
./setup.sh
```

**Done!** The script automatically:
- ✅ Installs backend dependencies
- ✅ Creates MongoDB database
- ✅ Creates admin user (admin@admin.com / admin)

No .env file needed. Works out of the box! 🎉

---

### Manual Setup (If You Prefer)

<details>
<summary>Click to expand manual steps</summary>

### Step 2: Install Backend Dependencies

```bash
cd backend
composer install
cd ..
```

**That's it!** No .env file needed. Works out of the box! 🎉

(Optional: Edit `backend/config/config.php` if you want to customize MongoDB URI, JWT secret, or Stripe settings)

### Step 3: Setup MongoDB

```bash
cd ..
mongosh < setup-mongodb.js
```

### Step 4: Create Admin User

```bash
mongosh formflow < create-admin-user.js
```

Admin credentials:
- **Email**: `admin@admin.com`
- **Password**: `admin`

</details>

---

## 🔧 Configure Apache

Point your Apache DocumentRoot to the movember folder:

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

Restart Apache:
```bash
sudo systemctl restart apache2
```

### Verify Setup

Visit `http://yourdomain.com/setup-check.php` to automatically verify your installation.

⚠️ **Delete setup-check.php after setup is complete for security!**

### Visit Your Site!

Go to `http://yourdomain.com` and login with:
- **Email**: `admin@admin.com`
- **Password**: `admin`

---

## 📂 What You Get

```
movember/
├── index.php          # Landing page ✨
├── login.php          # Login (working!)
├── register.php       # Register new users
├── dashboard.php      # User dashboard
├── pricing.php        # Pricing page
├── logout.php         # Logout
├── backend/           # API (PHP/MongoDB)
│   └── public/api/    # REST API endpoints
├── assets/            # CSS & JavaScript
├── includes/          # Shared PHP code
└── create-admin-user.js  # Admin user script
```

---

## 🎨 Features

### Modern UI
- ✅ Tailwind CSS from CDN (no build needed)
- ✅ Responsive design
- ✅ Clean, professional look
- ✅ jQuery for AJAX calls

### Backend API
- ✅ User authentication (JWT)
- ✅ Form CRUD operations
- ✅ Response collection
- ✅ Subscription management
- ✅ Stripe integration ready

### Zero Build Complexity
- ✅ No npm install needed
- ✅ No build step
- ✅ No Node.js on server
- ✅ Just PHP files that work

---

## 🔧 How It Works

1. **Frontend**: Plain PHP files with Tailwind CSS (CDN)
2. **AJAX**: jQuery calls the backend API
3. **Backend**: REST API in `/backend/public/api/`
4. **Database**: MongoDB for flexible data storage
5. **Auth**: Session-based with JWT tokens

---

## 🧪 Testing

After setup:

1. Visit your site
2. Login with `admin@admin.com` / `admin`
3. You'll see the dashboard
4. Create forms, manage responses, etc.

---

## 🐛 Troubleshooting

### Quick Diagnosis Tool

Visit `http://yourdomain.com/setup-check.php` for an automated diagnosis of your setup. This will check:
- PHP version and extensions
- Backend API accessibility
- MongoDB connection
- Admin user existence
- Login functionality

### Common Issues

**"Login not found"**
- Make sure Apache DocumentRoot points to `/path/to/movember`
- Check `.htaccess` exists in the root folder
- Ensure mod_rewrite is enabled: `sudo a2enmod rewrite`

**"API call failed"**
- Check backend is accessible at `yourdomain.com/backend/api/health`
- Verify MongoDB is running: `sudo systemctl status mongod`
- Check `backend/config/config.php` if you customized MongoDB settings
- Check composer dependencies are installed: `cd backend && composer install`

**"CSS not loading"**
- Check `assets/css/style.css` exists
- Tailwind loads from CDN (internet required)
- Check Apache has AllowOverride All

**"Cannot connect to MongoDB"**
- Install MongoDB extension: `sudo pecl install mongodb`
- Add to php.ini: `extension=mongodb.so`
- Restart Apache: `sudo systemctl restart apache2`

---

## 📝 Default Login

After running `create-admin-user.js`:

**Email**: `admin@admin.com`
**Password**: `admin`

**⚠️ Change this password after first login!**

---

## 🎯 Next Steps

1. Login and explore the dashboard
2. Create your first form
3. Customize the design in `assets/css/style.css`
4. Add more features as needed

Everything is simple, maintainable PHP code!

---

**Questions?** Check the other docs:
- `ARCHITECTURE.md` - System architecture
- `DEPLOYMENT.md` - Production deployment
- `README.md` - Project overview
