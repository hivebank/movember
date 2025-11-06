# FormFlow - Modern SaaS Form Builder

A complete, production-ready SaaS solution for creating beautiful step-by-step forms similar to Typeform. **Zero build complexity** - just clone and run!

![FormFlow](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple)
![MongoDB](https://img.shields.io/badge/MongoDB-6.0+-green)
![Apache](https://img.shields.io/badge/Apache-2.4+-red)

## 🎯 What Makes This Special?

- ✅ **Zero Build Steps** - No npm, no webpack, no build process
- ✅ **Clone and Go** - Just `git clone`, configure, and it works
- ✅ **Modern UI** - Tailwind CSS from CDN for beautiful, responsive design
- ✅ **Simple & Maintainable** - Plain PHP files you can understand and modify
- ✅ **Production Ready** - Full authentication, subscriptions, and API

## ✨ Features

### For Form Creators
- 🎨 **Drag-and-Drop Form Builder** - Intuitive interface to create forms in minutes
- 📱 **Responsive Design** - Forms look great on all devices
- 💳 **Payment Integration** - Collect payments via Stripe
- 📊 **Analytics Dashboard** - Track views, responses, and completion rates
- 🎯 **Custom Branding** - Remove branding on Pro+ plans
- 📧 **Email Notifications** - Get notified of new submissions
- 🔗 **Custom Redirects** - Redirect users after form submission

### For Form Respondents
- ✅ **Clean UI/UX** - Beautiful, distraction-free form experience
- 📝 **Multiple Field Types** - Text, email, number, select, checkboxes, dates, and more
- 💾 **Auto-save Progress** - Never lose form data
- 🔒 **Secure** - Built with security best practices

### Technical Features
- 🔐 **JWT Authentication** - Secure, stateless authentication
- 💎 **Subscription Management** - Full Stripe integration for recurring payments
- 🚀 **RESTful API** - Well-documented API endpoints
- 📦 **NoSQL Database** - Flexible MongoDB schema
- 🛡️ **Security Hardened** - Rate limiting, CORS, input validation
- 📈 **Scalable Architecture** - Built to handle growth

## 📋 Tech Stack

### Backend
- **PHP 8.0+** with Slim Framework 4
- **MongoDB** - NoSQL database
- **Stripe API** - Payment processing
- **JWT** - Authentication
- **Apache 2.4+** - Web server
- **Composer** - PHP dependency management

### Frontend
- **Plain PHP** - No build step required!
- **Tailwind CSS** - From CDN, no compilation needed
- **jQuery** - For AJAX calls to backend
- **Modern CSS** - Custom styles for professional look

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+ (with curl, json, mbstring, mongodb extensions)
- MongoDB 6.0+
- Composer
- Apache 2.4+ (with mod_rewrite enabled)
- **NO Node.js required!** 🎉

### One-Command Setup!

```bash
# Clone and setup:
git clone https://github.com/hivebank/movember.git
cd movember
./setup.sh
```

**Done!** The script automatically installs everything. Then just point Apache to the folder!

**Manual setup:**
```bash
git clone https://github.com/hivebank/movember.git
cd movember
cd backend && composer install && cd ..
mongosh < setup-mongodb.js
mongosh formflow < create-admin-user.js
```

### Quick Verification

After setup, visit `http://yourdomain.com/setup-check.php` - this will automatically check:
- ✅ PHP version and extensions
- ✅ Backend API accessibility
- ✅ MongoDB connection
- ✅ Admin user exists
- ✅ Login works

### Default Login
- **Email:** admin@admin.com
- **Password:** admin

🎉 That's it! No build steps, no npm install, no webpack!

See [SETUP.md](SETUP.md) for detailed instructions.

## 📦 Project Structure

```
movember/
├── index.php               # Landing page
├── login.php              # Login page
├── register.php           # Registration page
├── dashboard.php          # User dashboard
├── pricing.php            # Pricing plans
├── logout.php             # Logout handler
│
├── includes/              # Shared PHP code
│   ├── config.php         # Configuration & API helper
│   ├── header.php         # Header & navigation
│   └── footer.php         # Footer & scripts
│
├── assets/                # Static assets
│   ├── css/
│   │   └── style.css      # Custom styles
│   └── js/
│       └── app.js         # JavaScript utilities
│
├── backend/               # PHP Backend API
│   ├── config/            # Configuration files
│   ├── public/            # Public entry point
│   │   ├── api/           # API endpoints
│   │   └── index.php      # API router
│   ├── src/
│   │   ├── Controllers/   # API controllers
│   │   ├── Models/        # Data models
│   │   ├── Middleware/    # Custom middleware
│   │   └── Database/      # Database connection
│   └── composer.json
│
├── setup-check.php        # Setup verification tool
├── create-admin-user.js   # Admin user creation script
├── setup-mongodb.js       # MongoDB setup script
├── SETUP.md               # Setup instructions
├── ARCHITECTURE.md        # System architecture docs
├── DEPLOYMENT.md          # Deployment guide
└── .htaccess              # Apache rewrite rules
```

## 💰 Subscription Plans

| Feature | Free | Starter ($19/mo) | Pro ($49/mo) | Enterprise ($199/mo) |
|---------|------|------------------|--------------|----------------------|
| Forms | 3 | 25 | Unlimited | Unlimited |
| Responses/month | 100 | 1,000 | 10,000 | Unlimited |
| File uploads | ❌ | ✅ | ✅ | ✅ |
| Custom branding | ❌ | ❌ | ✅ | ✅ |
| Payment collection | ❌ | ✅ | ✅ | ✅ |
| Advanced analytics | ❌ | ❌ | ✅ | ✅ |
| API access | ❌ | ❌ | ✅ | ✅ |
| Team collaboration | ❌ | ❌ | ❌ | ✅ |

## 🔧 Configuration

### Zero Configuration Required!

The app works out of the box with sensible defaults in `backend/config/config.php`:
- MongoDB: `mongodb://localhost:27017` (database: `formflow`)
- JWT Secret: Auto-generated secure secret
- Stripe: Optional (empty by default)
- Email: Optional (logs to error_log in production mode)

**Want to customize?** Just edit `backend/config/config.php` - no .env file needed!

```php
return [
    'database' => [
        'uri' => 'mongodb://localhost:27017',
        'database' => 'formflow',
    ],
    'jwt' => [
        'secret' => 'your-custom-secret', // Auto-generated by default
        'expiration' => 3600,
    ],
    // ... edit directly, no .env needed!
];
```

### Stripe Setup

1. Create a Stripe account at https://stripe.com
2. Get your API keys from the dashboard
3. Create products for each subscription plan
4. Configure webhook endpoint: `https://yourdomain.com/api/webhooks/stripe`
5. Add webhook secret to backend .env

## 🚢 Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for comprehensive deployment instructions including:
- Server setup and requirements
- SSL certificate configuration
- Production optimizations
- Security hardening
- Backup strategies
- Monitoring and logging

Quick deployment checklist:
1. ✅ Setup server with required software
2. ✅ Configure MongoDB with authentication
3. ✅ Install and configure backend
4. ✅ Build and deploy frontend
5. ✅ Setup Apache with SSL
6. ✅ Configure Stripe webhooks
7. ✅ Setup automated backups
8. ✅ Enable monitoring

## 📚 API Documentation

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `GET /api/auth/me` - Get current user

### Forms
- `GET /api/forms` - List all forms
- `POST /api/forms` - Create new form
- `GET /api/forms/:id` - Get form details
- `PUT /api/forms/:id` - Update form
- `DELETE /api/forms/:id` - Delete form
- `GET /api/forms/:slug/public` - Get public form

### Responses
- `POST /api/forms/:slug/submit` - Submit form response
- `GET /api/forms/:id/responses` - Get form responses
- `GET /api/forms/:id/analytics` - Get form analytics

### Subscription
- `GET /api/subscription/plans` - Get available plans
- `POST /api/subscription/create` - Create subscription
- `POST /api/subscription/cancel` - Cancel subscription

See [ARCHITECTURE.md](ARCHITECTURE.md) for complete API documentation.

## 🔒 Security Features

- ✅ JWT-based authentication
- ✅ Password hashing with bcrypt
- ✅ CORS protection
- ✅ Rate limiting
- ✅ Input validation and sanitization
- ✅ XSS prevention
- ✅ CSRF protection
- ✅ Secure headers
- ✅ Stripe webhook signature verification

## 📝 License

This project is licensed under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 💬 Support

For support, please open an issue on GitHub.

## 🎯 Roadmap

- [ ] File upload support
- [ ] Advanced conditional logic
- [ ] Multi-language support
- [ ] Webhooks for form submissions
- [ ] Integration with third-party services (Zapier, etc.)
- [ ] Advanced analytics and reporting
- [ ] Team collaboration features
- [ ] Custom domain support
- [ ] White-label solution

---

**Built with ❤️ using PHP, Vue.js, and MongoDB**
