# FormFlow - SaaS Form Builder Architecture

## Tech Stack

### Backend
- **PHP 8.2+** with Composer for dependency management
- **Slim Framework 4** - Modern PHP micro-framework for RESTful APIs
- **MongoDB** - NoSQL database for flexible form schemas
- **Apache 2.4+** with mod_rewrite

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vite** - Modern build tool
- **Pinia** - State management
- **TailwindCSS** - Utility-first CSS framework
- **Vue Draggable** - Drag and drop functionality
- **Axios** - HTTP client

### Payment Processing
- **Stripe API** - Subscription management and payments

### Authentication
- **JWT (JSON Web Tokens)** - Stateless authentication

## System Architecture

```
┌─────────────────┐
│   Client/User   │
└────────┬────────┘
         │
    ┌────▼─────┐
    │  Apache  │
    └────┬─────┘
         │
    ┌────▼──────────────────────────┐
    │    Frontend (Vue.js SPA)      │
    │  - Form Builder Interface     │
    │  - Form Renderer              │
    │  - Analytics Dashboard        │
    └────┬──────────────────────────┘
         │ REST API
    ┌────▼──────────────────────────┐
    │    Backend (PHP/Slim)         │
    │  - Authentication             │
    │  - Form CRUD API              │
    │  - Response Collection API    │
    │  - Stripe Webhook Handler     │
    └────┬──────────┬───────────────┘
         │          │
    ┌────▼─────┐  ┌▼────────────┐
    │ MongoDB  │  │  Stripe API │
    └──────────┘  └─────────────┘
```

## Database Schema (MongoDB Collections)

### users
```json
{
  "_id": "ObjectId",
  "email": "string",
  "password": "string (hashed)",
  "name": "string",
  "company": "string",
  "subscription": {
    "plan": "free|starter|pro|enterprise",
    "status": "active|cancelled|past_due",
    "stripeCustomerId": "string",
    "stripeSubscriptionId": "string",
    "currentPeriodEnd": "Date"
  },
  "createdAt": "Date",
  "updatedAt": "Date"
}
```

### forms
```json
{
  "_id": "ObjectId",
  "userId": "ObjectId",
  "title": "string",
  "description": "string",
  "slug": "string (unique)",
  "fields": [
    {
      "id": "string",
      "type": "text|email|number|select|radio|checkbox|textarea|date|rating|file",
      "label": "string",
      "placeholder": "string",
      "required": "boolean",
      "validation": {},
      "options": ["array"], // for select, radio, checkbox
      "order": "number",
      "settings": {}
    }
  ],
  "settings": {
    "theme": "string",
    "submitText": "string",
    "redirectUrl": "string",
    "requirePayment": "boolean",
    "paymentAmount": "number",
    "notifications": {
      "email": "string",
      "sendCopy": "boolean"
    }
  },
  "status": "draft|published|archived",
  "views": "number",
  "submissions": "number",
  "createdAt": "Date",
  "updatedAt": "Date"
}
```

### responses
```json
{
  "_id": "ObjectId",
  "formId": "ObjectId",
  "userId": "ObjectId",
  "answers": [
    {
      "fieldId": "string",
      "value": "mixed",
      "label": "string"
    }
  ],
  "metadata": {
    "ipAddress": "string",
    "userAgent": "string",
    "referrer": "string",
    "completionTime": "number (seconds)",
    "paymentStatus": "pending|completed|failed",
    "stripePaymentId": "string"
  },
  "submittedAt": "Date"
}
```

### subscriptions
```json
{
  "_id": "ObjectId",
  "userId": "ObjectId",
  "stripeSubscriptionId": "string",
  "stripePriceId": "string",
  "status": "active|cancelled|past_due|unpaid",
  "currentPeriodStart": "Date",
  "currentPeriodEnd": "Date",
  "cancelAtPeriodEnd": "boolean",
  "createdAt": "Date",
  "updatedAt": "Date"
}
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user
- `POST /api/auth/refresh` - Refresh JWT token
- `GET /api/auth/me` - Get current user

### Forms
- `GET /api/forms` - List all forms (user's forms)
- `POST /api/forms` - Create new form
- `GET /api/forms/:id` - Get form details
- `PUT /api/forms/:id` - Update form
- `DELETE /api/forms/:id` - Delete form
- `GET /api/forms/:slug/public` - Get public form (for rendering)
- `POST /api/forms/:slug/submit` - Submit form response

### Responses
- `GET /api/forms/:id/responses` - Get all responses for a form
- `GET /api/responses/:id` - Get single response
- `DELETE /api/responses/:id` - Delete response
- `GET /api/forms/:id/analytics` - Get form analytics

### Subscription/Payments
- `GET /api/subscription/plans` - Get available plans
- `POST /api/subscription/create` - Create subscription
- `POST /api/subscription/cancel` - Cancel subscription
- `POST /api/subscription/update` - Update subscription
- `GET /api/subscription/status` - Get subscription status
- `POST /api/webhooks/stripe` - Stripe webhook handler

### User
- `GET /api/user/profile` - Get user profile
- `PUT /api/user/profile` - Update user profile
- `PUT /api/user/password` - Change password

## Frontend Structure

```
frontend/
├── src/
│   ├── components/
│   │   ├── builder/
│   │   │   ├── FormBuilder.vue
│   │   │   ├── FieldEditor.vue
│   │   │   ├── FieldLibrary.vue
│   │   │   └── PreviewPanel.vue
│   │   ├── renderer/
│   │   │   ├── FormRenderer.vue
│   │   │   └── fields/
│   │   │       ├── TextField.vue
│   │   │       ├── EmailField.vue
│   │   │       ├── SelectField.vue
│   │   │       └── ... (other field types)
│   │   ├── dashboard/
│   │   │   ├── FormList.vue
│   │   │   ├── Analytics.vue
│   │   │   └── ResponsesTable.vue
│   │   └── common/
│   │       ├── Navbar.vue
│   │       └── Modal.vue
│   ├── views/
│   │   ├── Login.vue
│   │   ├── Register.vue
│   │   ├── Dashboard.vue
│   │   ├── FormBuilder.vue
│   │   ├── FormView.vue
│   │   ├── Responses.vue
│   │   ├── Settings.vue
│   │   └── Pricing.vue
│   ├── stores/
│   │   ├── auth.js
│   │   ├── forms.js
│   │   └── subscription.js
│   ├── router/
│   │   └── index.js
│   ├── services/
│   │   ├── api.js
│   │   └── stripe.js
│   └── App.vue
├── public/
└── package.json
```

## Backend Structure

```
backend/
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── FormController.php
│   │   ├── ResponseController.php
│   │   ├── SubscriptionController.php
│   │   └── WebhookController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Form.php
│   │   ├── Response.php
│   │   └── Subscription.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── CorsMiddleware.php
│   │   └── RateLimitMiddleware.php
│   ├── Services/
│   │   ├── StripeService.php
│   │   ├── EmailService.php
│   │   └── ValidationService.php
│   ├── Database/
│   │   └── MongoDB.php
│   └── routes.php
├── config/
│   ├── database.php
│   ├── stripe.php
│   └── jwt.php
├── public/
│   └── index.php
├── .htaccess
└── composer.json
```

## Subscription Plans

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

## Security Features

1. **Authentication**: JWT tokens with refresh mechanism
2. **Password Security**: Bcrypt hashing with salt
3. **Input Validation**: Server-side validation for all inputs
4. **Rate Limiting**: Prevent abuse of API endpoints
5. **CORS**: Proper CORS headers configuration
6. **SQL/NoSQL Injection**: Parameterized queries and sanitization
7. **XSS Prevention**: Output encoding and CSP headers
8. **HTTPS**: Force HTTPS in production
9. **Stripe Webhook Signature**: Verify webhook authenticity

## Deployment

### Requirements
- Ubuntu 20.04+ or CentOS 8+
- Apache 2.4+
- PHP 8.2+
- MongoDB 6.0+
- Node.js 18+ (for building frontend)
- SSL Certificate (Let's Encrypt)

### Environment Variables
```
# Database
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=formflow

# JWT
JWT_SECRET=your-secret-key
JWT_EXPIRATION=3600

# Stripe
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_PUBLISHABLE_KEY=pk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Email
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=user@example.com
SMTP_PASSWORD=password

# App
APP_URL=https://yourdomain.com
APP_ENV=production
```
