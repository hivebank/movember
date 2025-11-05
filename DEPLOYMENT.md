# FormFlow Deployment Guide

## Prerequisites

- Ubuntu 20.04+ or CentOS 8+
- Apache 2.4+
- PHP 8.2+
- MongoDB 6.0+
- Node.js 18+
- Composer
- SSL Certificate (Let's Encrypt recommended)

## Step 1: System Setup

### Install Required Packages

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install PHP 8.2 and extensions
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-mongodb php8.2-mbstring php8.2-xml php8.2-curl -y

# Install MongoDB
wget -qO - https://www.mongodb.org/static/pgp/server-6.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
sudo apt update
sudo apt install -y mongodb-org
sudo systemctl start mongod
sudo systemctl enable mongod

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Enable Apache Modules

```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod proxy_fcgi
sudo a2enmod ssl
sudo systemctl restart apache2
```

## Step 2: MongoDB Setup

```bash
# Run MongoDB setup script
mongosh < setup-mongodb.js

# Create MongoDB user (optional but recommended)
mongosh
> use formflow
> db.createUser({
    user: "formflow_user",
    pwd: "your_secure_password",
    roles: [{ role: "readWrite", db: "formflow" }]
  })
> exit
```

## Step 3: Backend Setup

```bash
# Clone or copy project to web directory
sudo mkdir -p /var/www/formflow
cd /var/www/formflow

# Copy backend files
sudo cp -r backend /var/www/formflow/

# Install PHP dependencies
cd /var/www/formflow/backend
composer install --optimize-autoloader --no-dev

# Configure environment
cp .env.example .env
nano .env  # Edit with your configuration

# Set permissions
sudo chown -R www-data:www-data /var/www/formflow/backend
sudo chmod -R 755 /var/www/formflow/backend
```

### Backend .env Configuration

```env
# Database
MONGODB_URI=mongodb://formflow_user:your_secure_password@localhost:27017
MONGODB_DATABASE=formflow

# JWT - Generate a strong secret key
JWT_SECRET=$(openssl rand -base64 32)
JWT_EXPIRATION=3600
JWT_REFRESH_EXPIRATION=2592000

# Stripe - Get from https://dashboard.stripe.com/apikeys
STRIPE_SECRET_KEY=sk_live_your_secret_key
STRIPE_PUBLISHABLE_KEY=pk_live_your_publishable_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# Stripe Price IDs - Create in Stripe Dashboard
STRIPE_PRICE_STARTER=price_starter_id
STRIPE_PRICE_PRO=price_pro_id
STRIPE_PRICE_ENTERPRISE=price_enterprise_id

# Email
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=user@example.com
SMTP_PASSWORD=your_smtp_password
SMTP_FROM=noreply@yourdomain.com

# App
APP_URL=https://yourdomain.com
APP_ENV=production
APP_DEBUG=false

# CORS
CORS_ALLOWED_ORIGINS=https://yourdomain.com

# Rate Limiting
RATE_LIMIT_REQUESTS=100
RATE_LIMIT_WINDOW=60
```

## Step 4: Frontend Setup

```bash
# Install frontend dependencies
cd /var/www/formflow/frontend
npm install

# Configure environment
cp .env.example .env
nano .env  # Edit with your configuration
```

### Frontend .env Configuration

```env
VITE_API_URL=https://yourdomain.com/api
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_publishable_key
```

### Build Frontend

```bash
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/formflow/frontend/dist
sudo chmod -R 755 /var/www/formflow/frontend/dist
```

## Step 5: Apache Configuration

```bash
# Copy Apache config
sudo cp apache-config.conf /etc/apache2/sites-available/formflow.conf

# Edit configuration
sudo nano /etc/apache2/sites-available/formflow.conf
# Update ServerName to your domain

# Enable site
sudo a2ensite formflow.conf
sudo systemctl reload apache2
```

## Step 6: SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is set up automatically
# Test renewal:
sudo certbot renew --dry-run
```

## Step 7: Stripe Configuration

### Create Products and Prices

1. Go to https://dashboard.stripe.com/products
2. Create products for each plan (Starter, Pro, Enterprise)
3. Add recurring prices (monthly)
4. Copy the price IDs to your backend .env file

### Setup Webhook

1. Go to https://dashboard.stripe.com/webhooks
2. Add endpoint: `https://yourdomain.com/api/webhooks/stripe`
3. Select events:
   - `checkout.session.completed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
4. Copy the webhook signing secret to your .env file

## Step 8: Security Hardening

### Firewall Configuration

```bash
# Configure UFW
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### PHP Security

```bash
# Edit PHP configuration
sudo nano /etc/php/8.2/fpm/php.ini

# Set these values:
expose_php = Off
display_errors = Off
log_errors = On
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 30
memory_limit = 256M
```

### MongoDB Security

```bash
# Edit MongoDB config
sudo nano /etc/mongod.conf

# Enable authentication:
security:
  authorization: enabled

# Bind to localhost only:
net:
  bindIp: 127.0.0.1

# Restart MongoDB
sudo systemctl restart mongod
```

## Step 9: Monitoring and Logging

### Setup Log Rotation

```bash
sudo nano /etc/logrotate.d/formflow
```

Add:
```
/var/log/apache2/formflow-*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        systemctl reload apache2 > /dev/null
    endscript
}
```

### Monitor Application

```bash
# Check Apache logs
sudo tail -f /var/log/apache2/formflow-error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Check MongoDB logs
sudo tail -f /var/log/mongodb/mongod.log
```

## Step 10: Backup Strategy

### MongoDB Backup Script

```bash
#!/bin/bash
# save as /usr/local/bin/backup-formflow.sh

BACKUP_DIR="/backup/formflow"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup MongoDB
mongodump --db formflow --out $BACKUP_DIR/mongodb_$DATE

# Compress backup
tar -czf $BACKUP_DIR/formflow_$DATE.tar.gz $BACKUP_DIR/mongodb_$DATE
rm -rf $BACKUP_DIR/mongodb_$DATE

# Delete backups older than 7 days
find $BACKUP_DIR -name "formflow_*.tar.gz" -mtime +7 -delete

echo "Backup completed: formflow_$DATE.tar.gz"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-formflow.sh

# Add to crontab for daily backup at 2 AM
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-formflow.sh
```

## Performance Optimization

### Enable PHP OpCache

```bash
sudo nano /etc/php/8.2/fpm/conf.d/10-opcache.ini
```

Add:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### MongoDB Optimization

Ensure indexes are created:
```bash
mongosh formflow < setup-mongodb.js
```

### Apache Tuning

```bash
sudo nano /etc/apache2/mods-enabled/mpm_prefork.conf
```

Adjust based on your server resources:
```apache
<IfModule mpm_prefork_module>
    StartServers             5
    MinSpareServers          5
    MaxSpareServers         10
    MaxRequestWorkers      150
    MaxConnectionsPerChild   0
</IfModule>
```

## Troubleshooting

### Check Services Status

```bash
sudo systemctl status apache2
sudo systemctl status php8.2-fpm
sudo systemctl status mongod
```

### Common Issues

**Issue: 500 Internal Server Error**
- Check Apache error logs: `sudo tail -f /var/log/apache2/formflow-error.log`
- Check PHP-FPM is running: `sudo systemctl status php8.2-fpm`
- Verify file permissions on backend directory

**Issue: MongoDB Connection Failed**
- Check MongoDB is running: `sudo systemctl status mongod`
- Verify connection string in .env
- Check MongoDB authentication settings

**Issue: Stripe Webhooks Not Working**
- Verify webhook URL is accessible publicly
- Check webhook signing secret in .env
- Review Stripe webhook logs in dashboard

## Updating the Application

```bash
# Backend update
cd /var/www/formflow/backend
git pull  # or copy new files
composer install --optimize-autoloader --no-dev

# Frontend update
cd /var/www/formflow/frontend
git pull  # or copy new files
npm install
npm run build

# Restart services
sudo systemctl reload apache2
sudo systemctl restart php8.2-fpm
```

## Support

For issues and questions:
- Review logs in `/var/log/apache2/`
- Check MongoDB logs in `/var/log/mongodb/`
- Verify all environment variables are set correctly
