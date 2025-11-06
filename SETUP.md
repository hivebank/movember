# FormFlow - Quick Setup Guide

## ✅ Everything is Fixed and Ready!

This is now a **zero-build, plug-and-play PHP application** with modern UI.

---

## 🚀 Setup on Your Server

### Step 1: Clone the Repo

```bash
git clone https://github.com/hivebank/movember.git
cd movember
```

### Step 2: Install Backend Dependencies

```bash
cd backend
composer install
cp .env.example .env
```

Edit `.env` and set at minimum:
```env
JWT_SECRET=your-random-secret-key-here
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=formflow
```

### Step 3: Setup MongoDB

```bash
cd ..
mongosh < setup-mongodb.js
```

### Step 4: Create Admin User

```bash
mongosh formflow < create-admin-user.js
```

This creates:
- **Email**: `admin@admin.com`
- **Password**: `admin`

### Step 5: Configure Apache

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

### Step 6: Visit Your Site!

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

### "Login not found"
- Make sure Apache DocumentRoot points to `/path/to/movember`
- Check `.htaccess` exists in the root folder

### "API call failed"
- Check backend is accessible at `yourdomain.com/backend/public/api/health`
- Verify MongoDB is running: `sudo systemctl status mongod`
- Check `.env` file has correct settings

### "CSS not loading"
- Check `assets/css/style.css` exists
- Tailwind loads from CDN (internet required)
- Check Apache has AllowOverride All

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
