# 9yt !Trybe Conference Portal - System Guide & Setup Manual

**Version:** 1.0
**Last Updated:** December 21, 2025
**For:** Developers, System Administrators, and Platform Owners

---

## Table of Contents

1. [Quick Start Guide](#quick-start-guide)
2. [Google Maps API Setup](#google-maps-api-setup)
3. [Mobile Money Integration](#mobile-money-integration)
4. [SMS Delivery Setup](#sms-delivery-setup)
5. [Event Management System](#event-management-system)
6. [Production Deployment](#production-deployment)
7. [Troubleshooting](#troubleshooting)

---

## Quick Start Guide

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 18+ (optional, for frontend assets)
- Git

### Installation Steps

**1. Clone Repository**

```bash
git clone https://github.com/your-repo/conference-portal.git
cd conference-portal
```

**2. Install Dependencies**

```bash
composer install
npm install && npm run build  # Optional
```

**3. Environment Configuration**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure Database**

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conference_portal
DB_USERNAME=root
DB_PASSWORD=your_password
```

**5. Run Migrations & Seeders**

```bash
php artisan migrate --seed
php artisan db:seed --class=AdminAccountSeeder
```

**6. Create Storage Link**

```bash
php artisan storage:link
```

**7. Start Development Server**

```bash
php artisan serve
```

Visit: `http://localhost:8000`

### Default Admin Credentials

```
Email: 9yttrybe@gmail.com
Password: Justbe999!
Login URL: http://localhost:8000/admin
```

**⚠️ IMPORTANT:** Change the admin password immediately in production!

---

## Google Maps API Setup

The platform uses Google Maps for venue location autocomplete and map display during event creation.

### Features Implemented

- **Google Places Autocomplete** - Search addresses as you type
- **Interactive Map** - Visual location selection
- **Draggable Marker** - Fine-tune venue location
- **Auto-filled Coordinates** - Latitude and longitude
- **Reverse Geocoding** - Get address from coordinates

### Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Note your project name/ID

### Step 2: Enable Required APIs

In Google Cloud Console, go to **APIs & Services** > **Library** and enable:

- **Maps JavaScript API** (for displaying maps)
- **Places API** (for address autocomplete)
- **Geocoding API** (for reverse geocoding)

### Step 3: Create API Key

1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **API Key**
3. Copy the generated API key
4. Click **Restrict Key** (recommended for security)

### Step 4: Restrict API Key (Security)

**Application Restrictions:**
- Choose **HTTP referrers (web sites)**
- Add allowed domains:
  - `localhost:8000/*` (development)
  - `yourdomain.com/*` (production)
  - `*.yourdomain.com/*` (subdomains)

**API Restrictions:**
- Select **Restrict key**
- Choose only:
  - Maps JavaScript API
  - Places API
  - Geocoding API

### Step 5: Add to Application

Edit `.env` file:

```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

Clear config cache:

```bash
php artisan config:clear
```

### Step 6: Enable Billing

Google Maps requires a billing account:

1. Go to **Billing** in Google Cloud Console
2. Link a billing account
3. **Don't worry:** Google provides **$200 free credit per month**

### Usage Costs

- **$200 free credit monthly** (covers ~28,000 map loads)
- After free credit:
  - Maps JavaScript API: ~$7 per 1,000 loads
  - Places Autocomplete: ~$17 per 1,000 requests

**For most small-medium platforms, you'll stay within the free tier.**

### Testing

1. Navigate to `/company/events/create`
2. Select "Venue" as location type
3. Start typing an address in **Venue Address** field
4. Select from dropdown suggestions
5. Map should update with a marker
6. Latitude and longitude auto-filled
7. Drag marker to fine-tune location

### Troubleshooting

**Map not showing:**
- Check API key in `.env`
- Verify all 3 APIs are enabled
- Check browser console for errors
- Ensure billing is enabled

**"This page can't load Google Maps correctly":**
- Usually means billing not enabled
- Go to Google Cloud Console → Enable billing

**Autocomplete not working:**
- Verify Places API is enabled
- Check API restrictions aren't blocking
- Ensure domain is in allowed referrers

---

## Mobile Money Integration

**Why It Matters:** 80% of Ghanaians use mobile money. This is your killer advantage over Eventbrite and Ticketmaster!

### Supported Payment Methods

- ✅ MTN Mobile Money (MoMo)
- ✅ Vodafone Cash
- ✅ AirtelTigo Money
- ✅ Visa/Mastercard
- ✅ Bank transfers
- ✅ USSD codes

### Step 1: Paystack Account Setup

1. **Sign up at [Paystack.com](https://paystack.com)**
   - Business name, email, phone
   - Verify email address

2. **Complete Business Verification**
   - Navigate to **Settings** > **Business Details**
   - Upload documents:
     - Business registration certificate
     - Director's ID card
     - Proof of address
     - Bank account details
   - Approval time: 24-48 hours

3. **Enable Mobile Money**
   - Go to **Settings** > **Preferences** > **Payment Channels**
   - Enable **Mobile Money** checkbox
   - Enable **MTN Mobile Money**
   - Enable **Vodafone Cash**
   - Enable **AirtelTigo Money**
   - Click **Save Changes**

### Step 2: Get API Keys

1. Go to **Settings** > **API Keys & Webhooks**
2. Copy both keys:
   - **Test Public Key** (pk_test_...)
   - **Test Secret Key** (sk_test_...)
   - **Live Public Key** (pk_live_...) - After approval
   - **Live Secret Key** (sk_live_...) - After approval

### Step 3: Configure Application

Edit `.env` file:

```env
# Test Mode (for development)
PAYSTACK_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxxxxxxxxxx

# Live Mode (for production - after approval)
# PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxx
# PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxx
```

**⚠️ IMPORTANT:**
- Use test keys during development
- Switch to live keys ONLY after business verification
- NEVER commit live keys to Git!

### Step 4: Test Mobile Money

**Test Mode Numbers:**

| Provider | Phone Number | PIN | OTP |
|----------|-------------|-----|-----|
| MTN MoMo | 0241234567 | Any 4 digits | 123456 |
| Vodafone Cash | 0501234567 | Any 4 digits | 123456 |
| AirtelTigo | 0261234567 | Any 4 digits | 123456 |

**Test Flow:**

1. Create test event with tickets
2. Go to checkout page
3. Select "Mobile Money" as payment method
4. Enter test phone number (from table above)
5. Complete "payment" (auto-succeeds in test mode)
6. Verify ticket is issued
7. Check email/SMS delivery

### Payment Fees

**Paystack Standard Fees:**

| Payment Method | Fee |
|----------------|-----|
| Local Cards | 1.5% + GH₵0.50 (capped at GH₵2,000) |
| International Cards | 3.9% + GH₵0.50 |
| MTN MoMo | 1.0% |
| Vodafone Cash | 1.0% |
| AirtelTigo | 1.0% |

**Your Platform:**
- Platform fee: 4%
- Paystack fee: ~1.5%
- **Total: ~5.5%** (still 30-40% cheaper than competitors!)

### Customer Journey (Mobile Money)

1. Customer selects tickets → Checkout
2. Enters contact info (name, email, phone)
3. Paystack page shows payment options
4. Customer selects "MTN Mobile Money"
5. Enters phone number (e.g., 024 123 4567)
6. Receives phone prompt: "Dial *170# to approve"
7. Enters MoMo PIN
8. Payment confirmed instantly! ⚡
9. Ticket delivered via email + SMS

**Time to complete: ~30 seconds** (faster than cards!)

---

## SMS Delivery Setup

Send ticket confirmations, event reminders, and campaign messages via SMS.

### Provider: Mnotify

The platform integrates with **Mnotify.com** for SMS delivery.

### Step 1: Create Mnotify Account

1. Visit [Mnotify.com](https://www.mnotify.com)
2. Sign up for an account
3. Verify your email and phone

### Step 2: Get API Key

1. Login to Mnotify dashboard
2. Navigate to **API Settings** or **Integration**
3. Copy your **API Key** (looks like: `mno_...`)

### Step 3: Register Sender ID

1. In Mnotify dashboard, go to **Sender IDs**
2. Click **Register New Sender ID**
3. Enter desired sender ID (e.g., "9YTTRYBE" or "TRYBE")
   - Max 11 characters
   - No spaces allowed
   - Alphanumeric only
4. Submit for approval
5. **Approval time:** 24-48 hours in Ghana

### Step 4: Buy SMS Credits

1. Go to **Buy Credits** or **Wallet**
2. Purchase SMS credits:
   - GH₵50 = ~300 SMS
   - GH₵100 = ~650 SMS
   - GH₵500 = ~3,500 SMS
3. Credits reflect immediately in your account

### Step 5: Configure Application

Edit `.env` file:

```env
# Before (won't work)
MNOTIFY_API_KEY=fhf
MNOTIFY_SENDER_ID=MNOTIFY

# After (will work)
MNOTIFY_API_KEY=your_real_mnotify_api_key_here
MNOTIFY_SENDER_ID=9YTTRYBE
```

Clear config cache:

```bash
php artisan config:clear
```

### Step 6: Initialize Platform SMS Credits

The platform tracks SMS credits in the database for Company ID 1 (platform owner).

Run the admin seeder to add 1000 free credits for testing:

```bash
php artisan db:seed --class=AdminAccountSeeder
```

This creates:
- Admin account (9yttrybe@gmail.com)
- Platform company (ID: 1)
- 1000 SMS credits for testing

### Testing SMS Delivery

Open Laravel Tinker:

```bash
php artisan tinker
```

Send test SMS:

```php
$company = \App\Models\Company::first();
$smsService = app(\App\Services\Sms\SmsService::class);

$result = $smsService->sendSingleSms(
    $company,
    '0244123456',  // Replace with your phone number
    'Test SMS from 9yt Trybe platform!',
    '9YTTRYBE'
);

dd($result);
// Check if 'success' => true
```

### How SMS Works

**When a ticket is purchased:**

1. **Email** sent immediately (queued)
2. **SMS** sent via NotificationService:
   - Checks platform SMS credits (Company #1)
   - Formats phone number (0244... → 233244...)
   - Sends via Mnotify API
   - Deducts credits from platform balance
   - Logs success/failure

**Phone Number Formatting:**

The system auto-formats Ghana numbers:

| Input | Formatted Output |
|-------|------------------|
| 0244123456 | 233244123456 |
| +233244123456 | 233244123456 |
| 244123456 | 233244123456 |

### SMS Cost Calculation

- **1 SMS** = up to 160 characters (GSM encoding)
- **1 SMS** = up to 70 characters (Unicode/emoji)
- **Long messages** split into multiple:
  - 161-306 chars = 2 SMS
  - 307-459 chars = 3 SMS

**Current ticket SMS:** ~250 characters = **2 SMS credits per ticket**

### Troubleshooting

**"Insufficient SMS credits":**

```bash
# Solution: Run seeder to add credits
php artisan db:seed --class=AdminAccountSeeder
```

**"Failed to send SMS":**

1. Check API key is valid (not placeholder "fhf")
2. Verify Sender ID is approved
3. Check Mnotify account has credits
4. Clear config: `php artisan config:clear`

**SMS not received:**

1. Check phone number format (233244...)
2. Verify Mnotify credits not depleted
3. Check `storage/logs/laravel.log` for errors
4. Test with different number

**Queue not processing:**

```bash
# Start queue worker
php artisan queue:work

# Or use cron (shared hosting)
* * * * * cd /path-to-app && php artisan queue:work --stop-when-empty
```

---

## Event Management System

Complete Eventbrite clone with Ghana-specific features.

### Core Features

**Public Features:**
- Browse events (search, filter, sort)
- Calendar view
- Event detail pages with Google Maps
- Guest checkout (no registration required)
- QR code tickets via email/SMS
- Like events and follow organizers

**Organizer Dashboard:**
- Create events with rich media (images, videos)
- Multiple ticket types and sections
- Attendee management with QR check-in
- Analytics dashboard (views, sales, conversion)
- Financial tracking (payouts, invoices)
- Bank/mobile money account management
- Conference tools (surveys, forms)
- SMS campaigns to attendees

**Admin Panel:**
- Review and approve/reject events
- Configure platform fees dynamically
- Manage SMS credits
- Platform-wide analytics

### Creating an Event (Organizer Guide)

**Step 1: Navigate to Events**

Login as company → `/company/events` → Click **"Create Event"**

**Step 2: Basic Information**

- **Event Title** (required)
- **Summary** - Short description for listings
- **Banner Image** - 900×370px recommended, max 5MB
- **Event Type** - Single or Recurring

**Step 3: Date & Time**

- **Start Date & Time**
- **End Date & Time**
- Timezone automatically set to Africa/Accra

**Step 4: Location**

Choose one:

- **Venue** - Physical location
  - Use Google Maps autocomplete
  - Enter full address
  - Latitude/longitude auto-filled
  - Drag marker to adjust
- **Online** - Virtual event
  - Platform (Zoom, Google Meet, Teams)
  - Meeting link/URL
- **To Be Announced** - TBA

**Step 5: Overview**

Rich text editor with formatting:
- Bold, italic, underline
- Numbered/bulleted lists
- Links
- Headings

**Step 6: Media**

- **Additional Images** - Event gallery (multiple uploads)
- **Videos** - YouTube or Vimeo URLs only

**Step 7: Good to Know**

- Age restriction (16+, 18+, 21+, All ages)
- Door time (when doors open)
- Parking information

**Step 8: FAQs**

Add frequently asked questions:
- Click **"Add FAQ"**
- Enter question and answer
- Can add multiple FAQs

**Step 9: Fee Bearer**

Choose who pays platform fees:
- **Attendee Pays** - Fees added to ticket price (recommended)
- **Organizer Absorbs** - Fees deducted from payout

**Step 10: Publish**

- **Save as Draft** - Not visible to public
- **Create & Publish** - Submit for admin approval

### Creating Tickets

After creating event:

1. Go to event details page
2. Click **"Manage Tickets"**
3. Create ticket section (e.g., "General Admission", "VIP")
4. Add ticket types to section:
   - **Name** (e.g., "Early Bird", "Regular")
   - **Price** (or free)
   - **Quantity** (unlimited or limited)
   - **Sales Period** (start/end dates)
   - **Min/Max per Order**
5. Save ticket

### Event Approval Workflow

```
Draft → Publish → Pending (awaits admin) → Approved ✓ (goes live)
                                        ↓
                                    Rejected ✗ (with reason)
```

**Admin Review:**

1. Admin logs in → `/admin/events`
2. Views pending events
3. Clicks on event to review details
4. Either:
   - **Approve** - Event goes live immediately
   - **Reject** - Must provide reason (email sent to organizer)

### Ticket Purchase Flow (Customer)

1. Browse events → Click event
2. View event details
3. Click **"Get Tickets"** button
4. Modal opens with ticket selection
5. Choose quantities → **"Continue to Checkout"**
6. Fill contact information (name, email, phone)
7. Review order summary (shows all fees)
8. Click **"Proceed to Payment"**
9. Redirected to Paystack
10. Choose payment method (card/mobile money)
11. Complete payment
12. Redirected to confirmation page
13. Receive email with QR code tickets
14. Receive SMS with ticket codes

### Attendee Check-In

**At Event Entrance:**

1. Organizer logs in on tablet/phone
2. Goes to `/company/events/{event}/attendees`
3. Scans QR code or enters 6-digit code
4. Clicks **"Check In"** button
5. Attendee marked as checked in
6. Timestamp recorded

**Check-In Status:**

- ✅ **Checked In** - Green badge
- ⏳ **Not Checked In** - Gray badge

### Financial Management

**Viewing Payouts:**

1. Navigate to `/company/finance/payouts`
2. See summary:
   - Total Earned
   - Pending Payout
   - Completed Payout
3. Payout table shows:
   - Event name
   - Gross amount
   - Platform fee (2.8%)
   - Net amount
   - Payment account
   - Status (Pending/Processing/Completed)

**Payout Calculation:**

```
Gross Amount:    GH₵10,000 (total ticket sales)
Platform Fee:    GH₵280 (2.8%)
Net Payout:      GH₵9,720
```

**Payment Accounts:**

Add bank or mobile money accounts:

1. Go to `/company/finance/bank-accounts`
2. Click **"Add Payment Account"**
3. Choose type:
   - **Bank Account** - 22 Ghanaian banks
   - **Mobile Money** - MTN/Vodafone/AirtelTigo
4. Enter account details
5. Set as default (for payouts)

### Analytics Dashboard

Navigate to `/company/events/{event}/analytics`

**Metrics Shown:**

- **Total Views** (with unique viewers)
- **Total Likes**
- **Tickets Sold** (with order count)
- **Total Revenue**
- Order status breakdown
- Check-in statistics
- Engagement rates
- Ticket sales by type
- Sales trend chart (last 30 days)
- Traffic sources

---

## Production Deployment

### Server Requirements

**Minimum Specs:**
- PHP 8.2+
- 2 CPU cores
- 4GB RAM
- 20GB SSD storage
- MySQL 8.0+ or PostgreSQL 13+

**Recommended Specs:**
- PHP 8.2+
- 4 CPU cores
- 8GB RAM
- 50GB SSD storage
- Redis for caching/queues

### Step 1: Server Setup

**Ubuntu 22.04 Example:**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath

# Install MySQL
sudo apt install mysql-server
sudo mysql_secure_installation

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install nginx

# Install Certbot (SSL)
sudo apt install certbot python3-certbot-nginx
```

### Step 2: Clone & Configure

```bash
# Clone repository
cd /var/www
git clone https://github.com/your-repo/conference-portal.git
cd conference-portal

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Copy environment file
cp .env.example .env
php artisan key:generate
```

### Step 3: Configure .env (Production)

```env
APP_NAME="9yt !Trybe"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conference_portal_prod
DB_USERNAME=your_db_user
DB_PASSWORD=strong_password_here

# Paystack (LIVE keys)
PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxx

# Mnotify (LIVE)
MNOTIFY_API_KEY=your_live_mnotify_key
MNOTIFY_SENDER_ID=9YTTRYBE

# Google Maps
GOOGLE_MAPS_API_KEY=your_google_maps_key

# Mail (use production SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.postmarkapp.com  # Or SendGrid, Mailgun
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="9yt !Trybe"

# Redis (if using)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue (use redis or database)
QUEUE_CONNECTION=redis  # or database

# Cache
CACHE_DRIVER=redis  # or file

# Session
SESSION_DRIVER=redis  # or database
```

### Step 4: Database Migration

```bash
# Create database
mysql -u root -p
CREATE DATABASE conference_portal_prod;
CREATE USER 'portal_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON conference_portal_prod.* TO 'portal_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# Seed admin account
php artisan db:seed --class=AdminAccountSeeder --force
```

### Step 5: Nginx Configuration

Create `/etc/nginx/sites-available/conference-portal`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/conference-portal/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/conference-portal /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 6: SSL Certificate

```bash
# Get Let's Encrypt SSL
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (certbot creates cron job automatically)
sudo certbot renew --dry-run
```

### Step 7: Optimize Laravel

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 8: Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/conference-portal/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/conference-portal/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Step 9: Cron Jobs

Add to crontab (`sudo crontab -e -u www-data`):

```cron
* * * * * cd /var/www/conference-portal && php artisan schedule:run >> /dev/null 2>&1
```

### Step 10: Firewall

```bash
# UFW firewall
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

### Step 11: Monitoring

**Install Laravel Telescope (Dev Only):**

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Production Monitoring:**

Use external services:
- **Application:** Bugsnag, Sentry, or Flare
- **Server:** New Relic, Datadog
- **Uptime:** UptimeRobot, Pingdom

---

## Troubleshooting

### Common Issues & Solutions

**Issue: "500 Internal Server Error"**

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx error log
sudo tail -f /var/log/nginx/error.log

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Issue: "Class not found"**

```bash
# Regenerate autoload files
composer dump-autoload
```

**Issue: "SQLSTATE connection refused"**

```bash
# Check MySQL is running
sudo systemctl status mysql

# Check credentials in .env
DB_HOST=127.0.0.1  # NOT localhost
DB_PORT=3306
DB_DATABASE=correct_db_name
DB_USERNAME=correct_username
DB_PASSWORD=correct_password

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

**Issue: "Queue not processing"**

```bash
# Check queue worker status
sudo supervisorctl status laravel-worker:*

# Restart worker
sudo supervisorctl restart laravel-worker:*

# Check worker logs
tail -f storage/logs/worker.log

# Process queue manually (testing)
php artisan queue:work --tries=1 --timeout=90
```

**Issue: "Storage link not working"**

```bash
# Remove old link
rm public/storage

# Recreate link
php artisan storage:link

# Verify
ls -la public/storage
```

**Issue: "Paystack callback not working"**

```bash
# Check webhook URL is accessible
curl https://yourdomain.com/events/payment/callback

# Check Paystack webhook settings
# URL should be: https://yourdomain.com/events/payment/callback

# Check logs
tail -f storage/logs/laravel.log | grep -i paystack
```

**Issue: "SMS not sending"**

```bash
# Check Mnotify API key is correct
php artisan tinker
config('services.mnotify.api_key');  # Should not be 'fhf'

# Check SMS credits
$company = \App\Models\Company::find(1);
$company->smsCredits()->first()->balance;  # Should be > 0

# Test SMS manually
$smsService = app(\App\Services\Sms\SmsService::class);
$result = $smsService->sendSingleSms($company, '0244123456', 'Test', '9YTTRYBE');
dd($result);
```

**Issue: "Images not displaying"**

```bash
# Check storage link exists
ls -la public/storage

# Recreate if missing
php artisan storage:link

# Check file permissions
sudo chmod -R 775 storage/app/public
sudo chown -R www-data:www-data storage
```

**Issue: "High server load"**

```bash
# Check for N+1 queries (install Telescope in dev)
composer require laravel/telescope --dev
php artisan telescope:install

# Add eager loading to high-traffic controllers
Event::with(['tickets', 'images', 'company'])->get();

# Enable Redis caching
# Update .env:
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Optimize queries
php artisan optimize
```

---

## Support & Resources

### Official Documentation

- **Laravel:** https://laravel.com/docs
- **Paystack:** https://paystack.com/docs
- **Google Maps:** https://developers.google.com/maps
- **Mnotify:** https://www.mnotify.com/docs

### Platform Admin

- **Email:** 9yttrybe@gmail.com
- **All support requests sent to this email**

### Useful Commands

```bash
# View logs in real-time
tail -f storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Check queue status
php artisan queue:work --once  # Process one job

# Check scheduled tasks
php artisan schedule:list

# Database backup
php artisan backup:run  # If spatie/laravel-backup installed

# Check system health
php artisan about
```

### Performance Tips

1. **Enable OPcache** (PHP 8.2)
2. **Use Redis** for cache/sessions/queues
3. **Enable gzip compression** in Nginx
4. **Optimize images** before upload
5. **Use CDN** for static assets (future)
6. **Add database indexes** on high-query columns
7. **Eager load relationships** to prevent N+1
8. **Cache config/routes/views** in production

---

## Automation, AI & SEO Workflow

### Scheduled Tasks
- A Windows task named `9ytTrybeSchedule` now calls `run-schedule.bat` every minute so `php artisan schedule:run` executes the hourly/daily commands defined in `routes/console.php` (news cache refresh, scheduled SMS, AI SEO/blog/enrichment/digest jobs, etc.). Keep `run-schedule.bat` at the repo root and verify the job with `schtasks /Query /TN 9ytTrybeSchedule`.
- Check `storage/logs/laravel.log` after each run for warnings; our local attempts to run `php artisan seo:refresh --only-missing --limit=80 --days=90` and `php artisan ai:enrich-content --only-missing --limit=40` timed out, so let the scheduler run them with enough time or execute them via an SSH/PowerShell session that can stay alive for longer than five minutes.

### IndexNow & Sitemap Submission
- The IndexNow key file now lives at `public/indexnow.txt`; keep `INDEXNOW_KEY_LOCATION=https://yourdomain.com/indexnow.txt` and `INDEXNOW_HOST` aligned with `APP_URL` inside `.env`/`.env.example`. Observers submit URLs automatically when events/polls/articles are approved.
- Keep the sitemap current (`php artisan sitemap:generate` or your own script) and submit it through Google Search Console and Bing Webmaster Tools; `app/Services/SEO/IndexNowService` simultaneously posts the URL list, so monitor `laravel.log` for success/failure entries.

### Manual AI/SEO Checks
- `php artisan blog:generate-ai --auto-publish` writes the daily how-to/what's-on posts defined by `AI_BLOG_HOW_TO_TOPICS` and `AI_BLOG_WHATS_ON_REGIONS`. Run it before a marketing push when you need extra content.
- `php artisan ai:growth-digest`, `ai:growth-organizer-tips`, and `ai:growth-social-snippets` create weekly digests, organizer tips, and social snippets—trigger them manually if immediate deliverables are required.
- After the AI jobs run, rerun the SEO refresh and enrichment commands so event/organizer/product tags, FAQs, and metadata stay fresh.

---

**Guide Version:** 1.0
**Last Updated:** December 21, 2025
**For Questions:** 9yttrybe@gmail.com
