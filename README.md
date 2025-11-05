# FormFlow - Modern SaaS Form Builder

A complete, production-ready SaaS solution for creating beautiful step-by-step forms similar to Typeform. Built with modern technologies and best practices.

![FormFlow](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green)
![MongoDB](https://img.shields.io/badge/MongoDB-6.0+-green)

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
- **PHP 8.2+** with Slim Framework 4
- **MongoDB** - NoSQL database
- **Stripe API** - Payment processing
- **JWT** - Authentication
- **Apache 2.4+** - Web server

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vite** - Lightning-fast build tool
- **Pinia** - State management
- **TailwindCSS** - Utility-first CSS
- **Axios** - HTTP client

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- MongoDB 6.0+
- Node.js 18+
- Composer
- Apache 2.4+

### Backend Setup

```bash
# Install dependencies
cd backend
composer install

# Configure environment
cp .env.example .env
nano .env  # Edit with your settings

# Setup MongoDB
mongosh < ../setup-mongodb.js
```

### Frontend Setup

```bash
# Install dependencies
cd frontend
npm install

# Configure environment
cp .env.example .env
nano .env  # Edit with your settings

# Run development server
npm run dev
```

### Start Development

```bash
# Backend (from backend directory)
php -S localhost:8000 -t public

# Frontend (from frontend directory)
npm run dev
```

Visit http://localhost:3000 to see the application!

## 📦 Project Structure

```
formflow/
├── backend/                # PHP Backend
│   ├── config/            # Configuration files
│   ├── public/            # Public entry point
│   ├── src/
│   │   ├── Controllers/   # API controllers
│   │   ├── Models/        # Data models
│   │   ├── Middleware/    # Custom middleware
│   │   ├── Services/      # Business logic services
│   │   └── Database/      # Database connection
│   └── composer.json
│
├── frontend/              # Vue.js Frontend
│   ├── src/
│   │   ├── components/    # Vue components
│   │   ├── views/         # Page views
│   │   ├── stores/        # Pinia stores
│   │   ├── router/        # Vue Router
│   │   └── services/      # API services
│   └── package.json
│
├── ARCHITECTURE.md        # System architecture docs
├── DEPLOYMENT.md          # Deployment guide
├── setup-mongodb.js       # MongoDB setup script
└── apache-config.conf     # Apache configuration
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

### Environment Variables

**Backend (.env)**
```env
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=formflow
JWT_SECRET=your-secret-key
STRIPE_SECRET_KEY=sk_test_xxx
STRIPE_PUBLISHABLE_KEY=pk_test_xxx
```

**Frontend (.env)**
```env
VITE_API_URL=http://localhost:8000/api
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_xxx
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
