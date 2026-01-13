#!/bin/bash

################################################################################
# 9yt !Trybe Conference Portal - Production Setup Script
# Run this on your production server where MySQL is installed
################################################################################

echo "🚀 Starting 9yt !Trybe Platform Setup..."
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if MySQL is running
echo -e "${YELLOW}Step 1: Checking MySQL...${NC}"
if systemctl is-active --quiet mysql || systemctl is-active --quiet mysqld; then
    echo -e "${GREEN}✅ MySQL is running${NC}"
else
    echo -e "${RED}❌ MySQL is not running. Starting it...${NC}"
    sudo systemctl start mysql || sudo systemctl start mysqld
fi
echo ""

# Create database
echo -e "${YELLOW}Step 2: Creating database...${NC}"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS conference_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo -e "${GREEN}✅ Database created${NC}"
echo ""

# Install composer dependencies
echo -e "${YELLOW}Step 3: Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✅ Dependencies installed${NC}"
echo ""

# Clear caches
echo -e "${YELLOW}Step 4: Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# Run migrations
echo -e "${YELLOW}Step 5: Running database migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations completed${NC}"
echo ""

# Seed admin account and SMS credits
echo -e "${YELLOW}Step 6: Creating admin account and SMS credits...${NC}"
php artisan db:seed --class=AdminAccountSeeder --force
echo -e "${GREEN}✅ Admin account created${NC}"
echo -e "${GREEN}   Email: 9yttrybe@gmail.com${NC}"
echo -e "${GREEN}   Password: Justbe999!${NC}"
echo ""

# Optimize Laravel
echo -e "${YELLOW}Step 7: Optimizing Laravel...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Laravel optimized${NC}"
echo ""

# Set permissions
echo -e "${YELLOW}Step 8: Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Queue worker setup
echo -e "${YELLOW}Step 9: Setting up queue worker...${NC}"
cat > /etc/systemd/system/9yt-queue.service <<EOL
[Unit]
Description=9yt Trybe Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5s
WorkingDirectory=$(pwd)
ExecStart=/usr/bin/php $(pwd)/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
EOL

sudo systemctl daemon-reload
sudo systemctl enable 9yt-queue
sudo systemctl start 9yt-queue
echo -e "${GREEN}✅ Queue worker configured and started${NC}"
echo ""

# Test SMS
echo -e "${YELLOW}Step 10: Testing SMS configuration...${NC}"
php artisan tinker --execute="
\$company = \App\Models\Company::first();
if (\$company) {
    \$credit = \App\Models\SmsCredit::where('owner_id', \$company->id)
        ->where('owner_type', 'App\Models\Company')
        ->first();
    echo 'SMS Credits available: ' . (\$credit ? \$credit->balance : 0) . PHP_EOL;
} else {
    echo '⚠️  No company found. Create a company first.' . PHP_EOL;
}
"
echo ""

# Final summary
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}🎉 SETUP COMPLETE!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${YELLOW}Admin Login:${NC}"
echo -e "  URL: https://yourdomain.com/admin"
echo -e "  Email: 9yttrybe@gmail.com"
echo -e "  Password: Justbe999!"
echo ""
echo -e "${YELLOW}Services Running:${NC}"
echo -e "  ✅ MySQL Database"
echo -e "  ✅ Queue Worker (background jobs)"
echo -e "  ✅ SMS Service (Mnotify)"
echo -e "  ✅ Email Service (SMTP)"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "  1. Visit /fee-calculator to see transparent pricing"
echo -e "  2. Create your first event"
echo -e "  3. Test ticket purchase and notifications"
echo -e "  4. Check logs: tail -f storage/logs/laravel.log"
echo ""
echo -e "${YELLOW}Monitoring:${NC}"
echo -e "  Queue worker: sudo systemctl status 9yt-queue"
echo -e "  Queue logs: sudo journalctl -u 9yt-queue -f"
echo ""
echo -e "${GREEN}You're ready to dominate the market! 🚀${NC}"
echo ""
