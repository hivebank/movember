#!/bin/bash

# FormFlow - One-Command Setup Script
# Run this after git clone to set up everything automatically

set -e  # Exit on error

echo "🚀 FormFlow Setup Starting..."
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Install Composer Dependencies
echo -e "${BLUE}📦 Step 1/3: Installing backend dependencies...${NC}"
cd backend
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer not found. Please install Composer first:${NC}"
    echo "   curl -sS https://getcomposer.org/installer | php"
    echo "   sudo mv composer.phar /usr/local/bin/composer"
    exit 1
fi

composer install --no-dev --optimize-autoloader
cd ..
echo -e "${GREEN}✓ Backend dependencies installed${NC}"
echo ""

# Step 2: Setup MongoDB
echo -e "${BLUE}📊 Step 2/3: Setting up MongoDB database...${NC}"
if ! command -v mongosh &> /dev/null; then
    echo -e "${RED}❌ MongoDB shell (mongosh) not found.${NC}"
    echo "   Please install MongoDB first:"
    echo "   https://www.mongodb.com/docs/manual/installation/"
    exit 1
fi

# Check if MongoDB is running
if ! mongosh --eval "db.runCommand({ ping: 1 })" > /dev/null 2>&1; then
    echo -e "${YELLOW}⚠️  MongoDB is not running. Attempting to start...${NC}"
    if command -v systemctl &> /dev/null; then
        sudo systemctl start mongod || true
        sleep 2
    fi

    # Check again
    if ! mongosh --eval "db.runCommand({ ping: 1 })" > /dev/null 2>&1; then
        echo -e "${RED}❌ Could not connect to MongoDB.${NC}"
        echo "   Please start MongoDB manually:"
        echo "   sudo systemctl start mongod"
        exit 1
    fi
fi

mongosh < setup-mongodb.js > /dev/null 2>&1 || true
echo -e "${GREEN}✓ MongoDB database created${NC}"
echo ""

# Step 3: Create Admin User
echo -e "${BLUE}👤 Step 3/3: Creating admin user...${NC}"
mongosh formflow < create-admin-user.js > /dev/null 2>&1 || true
echo -e "${GREEN}✓ Admin user created${NC}"
echo ""

# Success message
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✨ FormFlow Setup Complete!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${BLUE}📋 Next Steps:${NC}"
echo ""
echo "  1. Point Apache DocumentRoot to this folder:"
echo "     $(pwd)"
echo ""
echo "  2. Make sure Apache mod_rewrite is enabled:"
echo "     sudo a2enmod rewrite"
echo "     sudo systemctl restart apache2"
echo ""
echo "  3. Visit your site and login with:"
echo -e "     Email:    ${YELLOW}admin@admin.com${NC}"
echo -e "     Password: ${YELLOW}admin${NC}"
echo ""
echo -e "${BLUE}🔍 To verify your setup, visit:${NC}"
echo "     http://yourdomain.com/setup-check.php"
echo ""
echo -e "${GREEN}🎉 Happy form building!${NC}"
echo ""
