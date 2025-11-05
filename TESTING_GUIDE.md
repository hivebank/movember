# 🚀 FormFlow - Quick Start & Testing Guide

## ✅ Current Status

Your FormFlow application has been set up at: **`/home/user/movember`**

### What's Running Now:
- ✅ **Frontend**: http://localhost:3001 (Vue.js development server)
- ⚠️ **Backend**: Not fully functional (requires MongoDB extension)

---

## 📋 What You Can Test Now

### 1. View the Frontend UI

Open your browser and visit: **http://localhost:3001**

You can explore:
- **Landing Page** - Marketing homepage with features
- **Pricing Page** - View subscription tiers
- **Login/Register Pages** - UI for authentication (won't work without backend)

### 2. Explore the Codebase

Navigate to `/home/user/movember` and explore:

```bash
cd /home/user/movember

# View project structure
tree -L 2 -I 'node_modules|vendor'

# Explore frontend components
ls -la frontend/src/views/
ls -la frontend/src/components/

# Explore backend code
ls -la backend/src/Controllers/
ls -la backend/src/Models/
```

---

## 🛠️ Full Setup (To Make Backend Work)

To get the full application working with backend API, you need to install MongoDB and the PHP MongoDB extension:

### Step 1: Install MongoDB

```bash
# Install MongoDB (Ubuntu/Debian)
wget -qO - https://www.mongodb.org/static/pgp/server-6.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
sudo apt update
sudo apt install -y mongodb-org

# Start MongoDB
sudo systemctl start mongod
sudo systemctl enable mongod

# Verify MongoDB is running
sudo systemctl status mongod
```

### Step 2: Install PHP MongoDB Extension

```bash
# Install PECL (if not installed)
sudo apt install php-pear php8.4-dev

# Install MongoDB extension
sudo pecl install mongodb

# Enable the extension
echo "extension=mongodb.so" | sudo tee /etc/php/8.4/cli/conf.d/20-mongodb.ini
echo "extension=mongodb.so" | sudo tee /etc/php/8.4/fpm/conf.d/20-mongodb.ini

# Verify installation
php -m | grep mongodb
```

### Step 3: Setup MongoDB Database

```bash
cd /home/user/movember

# Run the setup script
mongosh < setup-mongodb.js
```

### Step 4: Start the Backend Server

```bash
cd /home/user/movember/backend
php -S localhost:8000 -t public
```

### Step 5: Update Frontend API URL (if needed)

If frontend is on a different port, update `/home/user/movember/frontend/.env`:
```env
VITE_API_URL=http://localhost:8000/api
```

Then restart frontend:
```bash
cd /home/user/movember/frontend
npm run dev
```

---

## 🧪 Testing the Full Application

Once MongoDB is set up and both servers are running:

### 1. **Register a New User**
- Visit: http://localhost:3001/register
- Create an account with email and password
- You'll be redirected to the dashboard

### 2. **Create a Form**
- Click "Create New Form"
- Use drag-and-drop to add fields:
  - Text, Email, Number
  - Textarea, Select, Radio
  - Checkboxes, Date picker
- Configure field settings (label, placeholder, required)
- Click "Publish" to make it live

### 3. **Share and Submit Form**
- Get the public form URL (e.g., `/f/your-form-slug`)
- Open in incognito/new browser
- Fill out and submit the form

### 4. **View Responses**
- Return to dashboard
- Click "Responses" on your form
- View submitted data and analytics

### 5. **Test Subscription Flow**
- Go to Pricing page
- Click "Subscribe" on any paid plan
- **Note**: Requires valid Stripe keys in backend `.env`

---

## 📂 Project Structure Overview

```
/home/user/movember/
├── backend/                    # PHP API Server
│   ├── config/                # Configuration files
│   │   ├── database.php       # MongoDB connection
│   │   ├── jwt.php            # JWT settings
│   │   └── stripe.php         # Stripe & plans
│   ├── src/
│   │   ├── Controllers/       # API endpoints
│   │   │   ├── AuthController.php      # Login/register
│   │   │   ├── FormController.php      # Form CRUD
│   │   │   ├── ResponseController.php  # Submissions
│   │   │   └── SubscriptionController.php # Payments
│   │   ├── Models/            # Data models
│   │   │   ├── User.php
│   │   │   ├── Form.php
│   │   │   ├── Response.php
│   │   │   └── Subscription.php
│   │   ├── Middleware/        # Security & auth
│   │   └── Services/          # Business logic
│   └── public/index.php       # Entry point
│
├── frontend/                   # Vue.js App
│   ├── src/
│   │   ├── views/             # Page components
│   │   │   ├── Home.vue       # Landing page
│   │   │   ├── Login.vue      # Login page
│   │   │   ├── Register.vue   # Registration
│   │   │   ├── Dashboard.vue  # User dashboard
│   │   │   ├── FormBuilder.vue # Drag-and-drop builder
│   │   │   ├── FormView.vue   # Public form display
│   │   │   ├── Responses.vue  # View submissions
│   │   │   ├── Pricing.vue    # Pricing page
│   │   │   └── Settings.vue   # User settings
│   │   ├── components/        # Reusable components
│   │   ├── stores/            # State management
│   │   ├── router/            # Vue Router
│   │   └── services/          # API client
│   └── tailwind.config.js     # Styling config
│
├── ARCHITECTURE.md             # System design docs
├── DEPLOYMENT.md               # Production deployment
└── setup-mongodb.js            # DB initialization
```

---

## 🔑 Key Features Implemented

### Backend Features
✅ JWT Authentication with refresh tokens
✅ Complete REST API (Forms, Responses, Subscriptions)
✅ MongoDB integration with optimized queries
✅ Stripe payment integration
✅ Rate limiting & CORS protection
✅ Email notifications (configurable)
✅ Webhook handling for Stripe events
✅ Role-based access control

### Frontend Features
✅ Modern Vue 3 with Composition API
✅ Responsive design (mobile-first)
✅ Drag-and-drop form builder
✅ 8 field types supported
✅ Real-time form preview
✅ Analytics dashboard
✅ Subscription management UI
✅ Form sharing (public URLs)

### Form Field Types
1. **Text** - Single line input
2. **Email** - Email validation
3. **Number** - Numeric input
4. **Textarea** - Multi-line text
5. **Select** - Dropdown menu
6. **Radio** - Single choice
7. **Checkbox** - Multiple choices
8. **Date** - Date picker

---

## 🔧 Configuration Files

### Backend Environment (`/backend/.env`)
```env
# Already configured with secure JWT secret
JWT_SECRET=iU9QO4j0jUphQXCgXv0BikIFnLdWT3SrBS3ytJYlysA=

# MongoDB (default settings)
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=formflow

# Stripe (add your keys for payment testing)
STRIPE_SECRET_KEY=sk_test_your_key
STRIPE_PUBLISHABLE_KEY=pk_test_your_key
```

### Frontend Environment (`/frontend/.env`)
```env
# API endpoint
VITE_API_URL=http://localhost:8000/api

# Stripe public key (for payment forms)
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_your_key
```

---

## 📊 Subscription Plans

| Plan | Price | Forms | Responses/mo |
|------|-------|-------|--------------|
| Free | $0 | 3 | 100 |
| Starter | $19 | 25 | 1,000 |
| Pro | $49 | Unlimited | 10,000 |
| Enterprise | $199 | Unlimited | Unlimited |

To test subscriptions, you need:
1. Stripe account (free at stripe.com)
2. Create products in Stripe Dashboard
3. Add price IDs to backend `.env`

---

## 🐛 Troubleshooting

### Frontend Issues

**Problem**: Frontend won't start
```bash
cd /home/user/movember/frontend
rm -rf node_modules package-lock.json
npm install
npm run dev
```

**Problem**: API calls fail (CORS errors)
- Check backend `.env` has correct CORS_ALLOWED_ORIGINS
- Ensure backend is running on port 8000

### Backend Issues

**Problem**: MongoDB connection error
```bash
# Check if MongoDB is running
sudo systemctl status mongod

# Check if extension is loaded
php -m | grep mongodb
```

**Problem**: Class not found errors
```bash
cd /home/user/movember/backend
composer dump-autoload
```

---

## 📚 API Testing (Without Frontend)

You can test the API directly using curl:

```bash
# Health check
curl http://localhost:8000/health

# Register user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "name": "Test User"
  }'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

---

## 🚀 Next Steps

1. **Install MongoDB** (see instructions above)
2. **Test the application** locally
3. **Setup Stripe** (optional, for payments)
4. **Deploy to production** (see DEPLOYMENT.md)

For production deployment with Apache, SSL, and full security:
- See `DEPLOYMENT.md` for complete guide
- Use `apache-config.conf` for Apache setup
- Configure MongoDB with authentication

---

## 📞 Support

- **Architecture**: See `ARCHITECTURE.md`
- **Deployment**: See `DEPLOYMENT.md`
- **Code Location**: `/home/user/movember`
- **Frontend URL**: http://localhost:3001
- **Backend URL**: http://localhost:8000 (when running)

---

**Happy Testing! 🎉**
