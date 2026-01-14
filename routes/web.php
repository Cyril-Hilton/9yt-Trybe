<?php

use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Company\DashboardController;
use App\Http\Controllers\Company\ConferenceController;
use App\Http\Controllers\Company\RegistrationController;
use App\Http\Controllers\Company\ExportController;
use App\Http\Controllers\Public\RegistrationFormController;
use App\Http\Controllers\Public\PublicSurveyController;
use App\Http\Controllers\Company\SurveyController;
use App\Http\Controllers\Company\SurveyBuilderController;
use App\Http\Controllers\Company\SurveyResponseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\FormBuilderController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSmsPlanController;
use App\Http\Controllers\Admin\AdminSmsController;
use App\Http\Controllers\Company\SmsDashboardController;
use App\Http\Controllers\Company\SmsWalletController;
use App\Http\Controllers\Company\SmsCampaignController;
use App\Http\Controllers\Company\SmsContactController;
use App\Http\Controllers\Company\SmsSenderIdController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\Auth\SocialAuthController;

// OAuth Social Login Routes (Google, Microsoft/Outlook, Yahoo)
Route::prefix('auth')->name('auth.social.')->group(function () {
    Route::get('/{provider}/redirect/{guard?}', [SocialAuthController::class, 'redirect'])
        ->name('redirect')
        ->where('provider', 'google|microsoft|yahoo')
        ->where('guard', 'web|company|admin');

    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->name('callback')
        ->where('provider', 'google|microsoft|yahoo');
});

// Home
Route::get('/', [App\Http\Controllers\Public\EventController::class, 'home'])->name('home');

// SEO - Sitemap for search engines
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Global Search
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/search/quick', [App\Http\Controllers\SearchController::class, 'quickSearch'])->name('search.quick');

// News (Fashion, Lifestyle, Entertainment)
Route::get('/news', [App\Http\Controllers\Public\NewsController::class, 'index'])->name('news.index');
Route::get('/api/news', [App\Http\Controllers\Public\NewsController::class, 'index'])->name('api.news.index');

// Chat Routes (Available for all users - guest, authenticated, company)
Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.send');
Route::get('/chat/history', [App\Http\Controllers\ChatController::class, 'history'])->name('chat.history');
Route::post('/chat/{id}/read', [App\Http\Controllers\ChatController::class, 'markRead'])->name('chat.read');

// Holiday API Endpoints
Route::prefix('api/holidays')->group(function () {
    Route::get('/detect-country', [App\Http\Controllers\Api\HolidayController::class, 'detectCountry'])->name('api.holidays.detect-country');
    Route::get('/countries', [App\Http\Controllers\Api\HolidayController::class, 'getCountries'])->name('api.holidays.countries');
    Route::post('/check-date', [App\Http\Controllers\Api\HolidayController::class, 'checkDate'])->name('api.holidays.check-date');
    Route::get('/upcoming', [App\Http\Controllers\Api\HolidayController::class, 'getUpcoming'])->name('api.holidays.upcoming');
    Route::get('/year', [App\Http\Controllers\Api\HolidayController::class, 'getYearHolidays'])->name('api.holidays.year');
});

// Nearby Venues API (Google Places Integration)
Route::get('/api/nearby-venues', [App\Http\Controllers\NearbyVenuesController::class, 'searchNearby'])->name('api.nearby.venues');
Route::get('/api/venue-photo/{photoReference}', [App\Http\Controllers\NearbyVenuesController::class, 'getPhoto'])->name('api.venue.photo');
Route::get('/api/get-location', [App\Http\Controllers\NearbyVenuesController::class, 'getLocationFromIP'])->name('api.get.location');

// Legal Pages
Route::get('/terms-and-conditions', [LegalController::class, 'termsAndConditions'])->name('legal.terms');
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/disclaimer', [LegalController::class, 'disclaimer'])->name('legal.disclaimer');
Route::get('/cookie-policy', [LegalController::class, 'cookiePolicy'])->name('legal.cookies');
Route::get('/refund-policy', [LegalController::class, 'refundPolicy'])->name('legal.refund');

// Fee Calculator (Transparency Feature - show fees upfront vs competitors)
Route::get('/fee-calculator', [App\Http\Controllers\Public\FeeCalculatorController::class, 'index'])
    ->name('fee-calculator.index');
Route::post('/fee-calculator/calculate', [App\Http\Controllers\Public\FeeCalculatorController::class, 'calculate'])
    ->name('fee-calculator.calculate');

// Public Registration Routes
Route::get('/register/{slug}', [RegistrationFormController::class, 'show'])
    ->name('public.form');
Route::post('/register/{slug}', [RegistrationFormController::class, 'submit'])
    ->name('public.submit');
Route::get('/thank-you', [RegistrationFormController::class, 'thankYou'])
    ->name('thank-you');
Route::get('/conferences/{slug}', function (string $slug) {
    return redirect()->route('public.form', $slug, 301);
});

// Public Survey Routes
Route::get('/survey/{slug}', [PublicSurveyController::class, 'show'])
    ->name('survey.show');
Route::post('/survey/{slug}', [PublicSurveyController::class, 'submit'])
    ->name('survey.submit');
Route::get('/survey/{slug}/thank-you', [PublicSurveyController::class, 'thankYou'])
    ->name('survey.thank-you');
Route::get('/survey/{slug}/already-submitted', [PublicSurveyController::class, 'alreadySubmitted'])
    ->name('survey.already-submitted');
Route::get('/surveys/{slug}', function (string $slug) {
    return redirect()->route('survey.show', $slug, 301);
});

// Public Event Routes
Route::get('/events', [App\Http\Controllers\Public\EventController::class, 'index'])
    ->name('events.index');
Route::get('/events/calendar', [App\Http\Controllers\Public\EventController::class, 'calendar'])
    ->name('events.calendar');
Route::get('/categories/{slug}', [App\Http\Controllers\Public\EventController::class, 'category'])
    ->name('categories.show');
Route::get('/events/{slug}', [App\Http\Controllers\Public\EventController::class, 'show'])
    ->name('events.show');
Route::post('/events/{slug}/like', [App\Http\Controllers\Public\EventController::class, 'like'])
    ->name('events.like');
Route::post('/events/{event}/follow-organization', [App\Http\Controllers\Public\EventController::class, 'followOrganization'])
    ->name('events.follow-organization');

// Public Organizer Routes
Route::get('/organizers', [App\Http\Controllers\Public\OrganizerController::class, 'index'])
    ->name('organizers.index');
Route::get('/organizers/{slug}', [App\Http\Controllers\Public\OrganizerController::class, 'show'])
    ->name('organizers.show');

// Public Poll Routes
Route::get('/polls', [App\Http\Controllers\Public\PollController::class, 'index'])
    ->name('polls.index');
Route::get('/polls/{slug}', [App\Http\Controllers\Public\PollController::class, 'show'])
    ->name('polls.show');

// Event Ticket Purchase & Checkout (REQUIRES LOGIN - No guest checkout allowed!)
Route::get('/events/{slug}/checkout', [App\Http\Controllers\Public\EventCheckoutController::class, 'show'])
    ->middleware('auth') // Must be logged in to checkout
    ->name('events.checkout');
Route::post('/events/{slug}/checkout', [App\Http\Controllers\Public\EventCheckoutController::class, 'processOrder'])
    ->middleware(['auth', 'throttle:10,1']) // Must be logged in + rate limiting
    ->name('events.checkout.process');
Route::get('/events/orders/{orderNumber}', [App\Http\Controllers\Public\EventCheckoutController::class, 'confirmation'])
    ->name('events.order.confirmation');
Route::get('/events/payment/callback', [App\Http\Controllers\Public\EventCheckoutController::class, 'paymentCallback'])
    ->middleware('throttle:60,1') // 60 callbacks per minute (Paystack retries)
    ->name('events.payment.callback');

// Gallery Routes
Route::get('/gallery', [App\Http\Controllers\Public\GalleryController::class, 'index'])
    ->name('gallery.index');

// Shop Routes
Route::get('/shop', [App\Http\Controllers\Public\ShopController::class, 'index'])
    ->name('shop.index');
Route::get('/cart', [App\Http\Controllers\Public\ShopController::class, 'cart'])
    ->name('shop.cart');
Route::post('/shop/{product}/add-to-cart', [App\Http\Controllers\Public\ShopController::class, 'addToCart'])
    ->name('shop.add-to-cart');
Route::patch('/cart/{cartItem}', [App\Http\Controllers\Public\ShopController::class, 'updateCartItem'])
    ->name('shop.cart.update');
Route::delete('/cart/{cartItem}', [App\Http\Controllers\Public\ShopController::class, 'removeFromCart'])
    ->name('shop.cart.remove');

// Shop Checkout & Orders (REQUIRES LOGIN - No guest checkout allowed!)
Route::get('/shop/checkout', [App\Http\Controllers\Public\ShopCheckoutController::class, 'show'])
    ->middleware('auth') // Must be logged in to checkout
    ->name('shop.checkout');
Route::post('/shop/checkout', [App\Http\Controllers\Public\ShopCheckoutController::class, 'process'])
    ->middleware(['auth', 'throttle:10,1']) // Must be logged in + rate limiting
    ->name('shop.checkout.process');
Route::get('/shop/payment/callback', [App\Http\Controllers\Public\ShopCheckoutController::class, 'paymentCallback'])
    ->middleware('throttle:60,1') // 60 callbacks per minute
    ->name('shop.payment.callback');
Route::get('/shop/orders/{orderNumber}', [App\Http\Controllers\Public\ShopCheckoutController::class, 'confirmation'])
    ->name('shop.order.confirmation');

// Shop Product Detail (MUST come after specific routes like /shop/checkout)
Route::get('/shop/{product}', [App\Http\Controllers\Public\ShopController::class, 'show'])
    ->name('shop.show');

// Jobs/Portfolio Routes
Route::get('/jobs', [App\Http\Controllers\Public\JobsController::class, 'index'])
    ->name('jobs.index');
Route::get('/jobs/create', [App\Http\Controllers\Public\JobsController::class, 'create'])
    ->name('jobs.create');
Route::post('/jobs', [App\Http\Controllers\Public\JobsController::class, 'store'])
    ->name('jobs.store');

// Team Routes
Route::get('/team', [App\Http\Controllers\Public\TeamController::class, 'index'])
    ->name('team.index');
Route::post('/team', [App\Http\Controllers\Public\TeamController::class, 'store'])
    ->name('team.store');

// Static Pages
Route::get('/about', [App\Http\Controllers\Public\PageController::class, 'about'])
    ->name('about');
Route::get('/contact', [App\Http\Controllers\Public\PageController::class, 'contact'])
    ->name('contact');
Route::post('/contact', [App\Http\Controllers\Public\PageController::class, 'submitContact'])
    ->name('contact.submit');

// User Authentication Routes (for ticket buyers/attendees)
Route::prefix('user')->name('user.')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/register', [App\Http\Controllers\Auth\UserAuthController::class, 'showRegisterForm'])
            ->name('register');
        Route::post('/register', [App\Http\Controllers\Auth\UserAuthController::class, 'register'])
            ->middleware('throttle:5,1'); // 5 registrations per minute

        Route::get('/login', [App\Http\Controllers\Auth\UserAuthController::class, 'showLoginForm'])
            ->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\UserAuthController::class, 'login'])
            ->middleware('throttle:5,1'); // 5 login attempts per minute

        // OTP Login Routes
        Route::post('/send-otp', [App\Http\Controllers\User\UserOTPController::class, 'sendOTP'])
            ->name('send-otp')
            ->middleware('throttle:5,1'); // 5 OTP requests per minute
        Route::post('/verify-otp', [App\Http\Controllers\User\UserOTPController::class, 'verifyOTP'])
            ->name('verify-otp')
            ->middleware('throttle:5,1'); // 5 verification attempts per minute
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Auth\UserAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/dashboard', [App\Http\Controllers\User\UserDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/tickets', [App\Http\Controllers\User\UserDashboardController::class, 'tickets'])
            ->name('tickets');
        Route::get('/tickets/{order}', [App\Http\Controllers\User\UserDashboardController::class, 'ticketDetails'])
            ->name('tickets.show');

        // User SMS Routes
        Route::prefix('sms')->name('sms.')->group(function () {
            // Dashboard
            Route::get('/', [App\Http\Controllers\User\UserSmsDashboardController::class, 'index'])
                ->name('dashboard');

            // Campaigns (Excel & Instant Messaging)
            Route::get('/campaigns', [App\Http\Controllers\User\UserSmsCampaignController::class, 'index'])
                ->name('campaigns.index');
            Route::get('/campaigns/create', [App\Http\Controllers\User\UserSmsCampaignController::class, 'create'])
                ->name('campaigns.create');
            Route::post('/campaigns/send', [App\Http\Controllers\User\UserSmsCampaignController::class, 'send'])
                ->middleware('throttle:10,1') // 10 requests per minute
                ->name('campaigns.send');
            Route::get('/campaigns/{id}', [App\Http\Controllers\User\UserSmsCampaignController::class, 'show'])
                ->name('campaigns.show');
            Route::get('/campaigns/{id}/resend', [App\Http\Controllers\User\UserSmsCampaignController::class, 'resend'])
                ->name('campaigns.resend');
            Route::post('/campaigns/{id}/cancel', [App\Http\Controllers\User\UserSmsCampaignController::class, 'cancel'])
                ->name('campaigns.cancel');
            Route::delete('/campaigns/{id}', [App\Http\Controllers\User\UserSmsCampaignController::class, 'destroy'])
                ->name('campaigns.destroy');

            // Contacts
            Route::get('/contacts', [App\Http\Controllers\User\UserSmsContactController::class, 'index'])
                ->name('contacts.index');
            Route::get('/contacts/create', [App\Http\Controllers\User\UserSmsContactController::class, 'create'])
                ->name('contacts.create');
            Route::post('/contacts', [App\Http\Controllers\User\UserSmsContactController::class, 'store'])
                ->name('contacts.store');
            Route::get('/contacts/bulk-upload', [App\Http\Controllers\User\UserSmsContactController::class, 'bulkCreate'])
                ->name('contacts.bulk-upload');
            Route::post('/contacts/bulk-upload', [App\Http\Controllers\User\UserSmsContactController::class, 'bulkStore'])
                ->name('contacts.bulk-store');
            Route::get('/contacts/download-sample', [App\Http\Controllers\User\UserSmsContactController::class, 'downloadSample'])
                ->name('contacts.download-sample');
            Route::delete('/contacts/{id}', [App\Http\Controllers\User\UserSmsContactController::class, 'destroy'])
                ->name('contacts.destroy');

            // Sender IDs
            Route::get('/sender-ids', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'index'])
                ->name('sender-ids.index');
            Route::get('/sender-ids/create', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'create'])
                ->name('sender-ids.create');
            Route::post('/sender-ids', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'store'])
                ->name('sender-ids.store');
            Route::get('/sender-ids/{id}/edit', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'edit'])
                ->name('sender-ids.edit');
            Route::put('/sender-ids/{id}', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'update'])
                ->name('sender-ids.update');
            Route::post('/sender-ids/{id}/set-default', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'setDefault'])
                ->name('sender-ids.set-default');
            Route::delete('/sender-ids/{id}', [App\Http\Controllers\User\UserSmsSenderIdController::class, 'destroy'])
                ->name('sender-ids.destroy');

            // Wallet & Credit Purchase
            Route::get('/wallet', [App\Http\Controllers\User\UserSmsWalletController::class, 'index'])
                ->name('wallet.index');
            Route::post('/wallet/purchase', [App\Http\Controllers\User\UserSmsWalletController::class, 'initializePayment'])
                ->name('wallet.purchase');
            Route::get('/wallet/payment/callback', [App\Http\Controllers\User\UserSmsWalletController::class, 'handlePaymentCallback'])
                ->name('payment.callback');
            Route::get('/transactions/{id}', [App\Http\Controllers\User\UserSmsWalletController::class, 'showTransaction'])
                ->name('transactions.show');
        });
    });
});

// Password Reset Routes (Laravel requires these specific route names)
Route::middleware('guest')->group(function () {
    Route::get('/user/forgot-password', [App\Http\Controllers\Auth\UserAuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('/user/forgot-password', [App\Http\Controllers\Auth\UserAuthController::class, 'sendResetLink'])
        ->name('password.email');
    Route::get('/user/reset-password/{token}', [App\Http\Controllers\Auth\UserAuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    Route::post('/user/reset-password', [App\Http\Controllers\Auth\UserAuthController::class, 'resetPassword'])
        ->name('password.update');
});

// Organization Password Reset Routes
Route::middleware('guest:company')->group(function () {
    Route::get('/organization/forgot-password', [App\Http\Controllers\Auth\CompanyAuthController::class, 'showForgotPasswordForm'])
        ->name('organization.password.request');
    Route::post('/organization/forgot-password', [App\Http\Controllers\Auth\CompanyAuthController::class, 'sendResetLink'])
        ->name('organization.password.email');
    Route::get('/organization/reset-password/{token}', [App\Http\Controllers\Auth\CompanyAuthController::class, 'showResetPasswordForm'])
        ->name('organization.password.reset');
    Route::post('/organization/reset-password', [App\Http\Controllers\Auth\CompanyAuthController::class, 'resetPassword'])
        ->name('organization.password.update');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/user/verify-email', function () {
        return view('user.auth.verify-email');
    })->name('verification.notice');

    Route::get('/user/verify-email/{id}/{hash}', function (Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', 'Your email has been verified!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/user/resend-verification', function (Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Role Switching Routes
Route::get('/switch-to-organizer', [App\Http\Controllers\Auth\RoleSwitchController::class, 'switchToOrganizer'])
    ->middleware('auth')
    ->name('switch.to.organizer');

Route::get('/switch-to-user', [App\Http\Controllers\Auth\RoleSwitchController::class, 'switchToUser'])
    ->middleware('auth:company')
    ->name('switch.to.user');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
            ->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:3,1'); // 3 admin login attempts per minute (stricter)

        // OTP Login Routes
        Route::post('/send-otp', [App\Http\Controllers\Admin\AdminOTPController::class, 'sendOTP'])
            ->name('send-otp')
            ->middleware('throttle:3,1'); // 3 OTP requests per minute (stricter for admin)
        Route::post('/verify-otp', [App\Http\Controllers\Admin\AdminOTPController::class, 'verifyOTP'])
            ->name('verify-otp')
            ->middleware('throttle:3,1'); // 3 verification attempts per minute (stricter for admin)
    });

    // Authenticated routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Company Management
        Route::resource('companies', AdminCompanyController::class);
        Route::post('/companies/{company}/suspend', [AdminCompanyController::class, 'suspend'])
            ->name('companies.suspend');
        Route::post('/companies/{company}/unsuspend', [AdminCompanyController::class, 'unsuspend'])
            ->name('companies.unsuspend');

        // Admin User Management
        Route::resource('admins', AdminUserController::class);

        // SMS Plan Management
        Route::resource('sms/plans', AdminSmsPlanController::class)->names([
            'index' => 'sms.plans.index',
            'create' => 'sms.plans.create',
            'store' => 'sms.plans.store',
            'edit' => 'sms.plans.edit',
            'update' => 'sms.plans.update',
            'destroy' => 'sms.plans.destroy',
        ]);
        Route::match(['post', 'patch'], '/sms/plans/{id}/toggle-status', [AdminSmsPlanController::class, 'toggleStatus'])
            ->name('sms.plans.toggle-status');

        // SMS Sender ID Management
        Route::get('/sms/sender-ids', [AdminSmsController::class, 'senderIdRequests'])
            ->name('sms.sender-ids');
        Route::post('/sms/sender-ids/{id}/approve', [AdminSmsController::class, 'approveSenderId'])
            ->name('sms.sender-ids.approve');
        Route::post('/sms/sender-ids/{id}/reject', [AdminSmsController::class, 'rejectSenderId'])
            ->name('sms.sender-ids.reject');

        // Chat Management
        Route::get('/chat', [App\Http\Controllers\Admin\ChatController::class, 'index'])
            ->name('chat.index');
        Route::get('/chat/messages', [App\Http\Controllers\Admin\ChatController::class, 'getMessages'])
            ->name('chat.messages');
        Route::post('/chat/{id}/reply', [App\Http\Controllers\Admin\ChatController::class, 'reply'])
            ->name('chat.reply');
        Route::post('/chat/{id}/read', [App\Http\Controllers\Admin\ChatController::class, 'markRead'])
            ->name('chat.mark-read');
        Route::post('/chat/{id}/close', [App\Http\Controllers\Admin\ChatController::class, 'close'])
            ->name('chat.close');
        Route::get('/chat/unread-count', [App\Http\Controllers\Admin\ChatController::class, 'unreadCount'])
            ->name('chat.unread-count');

        // Manual Credit Addition
        Route::get('/sms/add-credits', [AdminSmsController::class, 'showAddCredits'])
            ->name('sms.add-credits');
        Route::post('/sms/add-credits', [AdminSmsController::class, 'addManualCredits'])
            ->name('sms.add-credits.store');

        // Admin SMS Campaigns
        Route::get('/sms/dashboard', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'dashboard'])
            ->name('sms.dashboard');
        Route::get('/sms/send-single', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'createSingle'])
            ->name('sms.send-single');
        Route::post('/sms/send-single', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'sendSingle'])
            ->middleware('throttle:10,1') // 10 requests per minute
            ->name('sms.send-single.store');
        Route::get('/sms/send-bulk', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'createBulk'])
            ->name('sms.send-bulk');
        Route::post('/sms/send-bulk', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'sendBulk'])
            ->middleware('throttle:10,1') // 10 requests per minute
            ->name('sms.send-bulk.store');
        Route::get('/sms/campaigns', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'index'])
            ->name('sms.campaigns.index');
        Route::get('/sms/campaigns/{id}', [App\Http\Controllers\Admin\AdminSmsCampaignController::class, 'show'])
            ->name('sms.campaigns.show');

        // Admin SMS Contacts Database
        Route::get('/sms-contacts', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'index'])
            ->name('sms-contacts.index');
        Route::get('/sms-contacts/create', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'create'])
            ->name('sms-contacts.create');
        Route::post('/sms-contacts', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'store'])
            ->name('sms-contacts.store');
        Route::get('/sms-contacts/bulk-upload', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'bulkCreate'])
            ->name('sms-contacts.bulk-upload');
        Route::post('/sms-contacts/bulk-upload', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'bulkStore'])
            ->name('sms-contacts.bulk-upload.store');
        Route::get('/sms-contacts/sample-download', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'downloadSample'])
            ->name('sms-contacts.sample-download');
        Route::delete('/sms-contacts/{id}', [App\Http\Controllers\Admin\AdminSmsContactController::class, 'destroy'])
            ->name('sms-contacts.destroy');

        // Complementary Tickets Management
        Route::get('/complementary-tickets', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'index'])
            ->name('complementary-tickets.index');
        Route::get('/complementary-tickets/create', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'create'])
            ->name('complementary-tickets.create');
        Route::post('/complementary-tickets', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'store'])
            ->name('complementary-tickets.store');
        Route::get('/complementary-tickets/bulk-create', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'bulkCreate'])
            ->name('complementary-tickets.bulk-create');
        Route::post('/complementary-tickets/bulk-store', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'bulkStore'])
            ->name('complementary-tickets.bulk-store');
        Route::get('/complementary-tickets/search-events', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'searchEvents'])
            ->name('complementary-tickets.search-events');
        Route::post('/complementary-tickets/{id}/toggle-visibility', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'toggleVisibility'])
            ->name('complementary-tickets.toggle-visibility');
        Route::post('/complementary-tickets/{id}/cancel', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'cancel'])
            ->name('complementary-tickets.cancel');
        Route::post('/events/{eventId}/toggle-complementary-visibility', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'toggleEventVisibility'])
            ->name('events.toggle-complementary-visibility');
        Route::get('/complementary-tickets/template-download', [App\Http\Controllers\Admin\ComplementaryTicketController::class, 'downloadTemplate'])
            ->name('complementary-tickets.template-download');

        // Event Management & Approval
        Route::get('/events', [App\Http\Controllers\Admin\AdminEventController::class, 'index'])
            ->name('events.index');
        Route::get('/events/create', [App\Http\Controllers\Admin\AdminEventController::class, 'create'])
            ->name('events.create');
        Route::post('/events', [App\Http\Controllers\Admin\AdminEventController::class, 'store'])
            ->name('events.store');
        Route::get('/events/{event}', [App\Http\Controllers\Admin\AdminEventController::class, 'show'])
            ->name('events.show');
        Route::get('/events/{event}/edit', [App\Http\Controllers\Admin\AdminEventController::class, 'edit'])
            ->name('events.edit');
        Route::put('/events/{event}', [App\Http\Controllers\Admin\AdminEventController::class, 'update'])
            ->name('events.update');
        Route::post('/events/{event}/approve', [App\Http\Controllers\Admin\AdminEventController::class, 'approve'])
            ->name('events.approve');
        Route::post('/events/{event}/reject', [App\Http\Controllers\Admin\AdminEventController::class, 'reject'])
            ->name('events.reject');
        Route::delete('/events/{event}', [App\Http\Controllers\Admin\AdminEventController::class, 'destroy'])
            ->name('events.destroy');

        // Shop Management & Approval
        Route::get('/shop', [App\Http\Controllers\Admin\AdminShopController::class, 'index'])
            ->name('shop.index');
        Route::get('/shop/create', [App\Http\Controllers\Admin\AdminShopController::class, 'create'])
            ->name('shop.create');
        Route::post('/shop', [App\Http\Controllers\Admin\AdminShopController::class, 'store'])
            ->name('shop.store');
        Route::get('/shop/{product}/edit', [App\Http\Controllers\Admin\AdminShopController::class, 'edit'])
            ->name('shop.edit');
        Route::put('/shop/{product}', [App\Http\Controllers\Admin\AdminShopController::class, 'update'])
            ->name('shop.update');
        Route::post('/shop/{product}/approve', [App\Http\Controllers\Admin\AdminShopController::class, 'approve'])
            ->name('shop.approve');
        Route::post('/shop/{product}/reject', [App\Http\Controllers\Admin\AdminShopController::class, 'reject'])
            ->name('shop.reject');
        Route::post('/shop/{product}/toggle-active', [App\Http\Controllers\Admin\AdminShopController::class, 'toggleActive'])
            ->name('shop.toggle-active');
        Route::delete('/shop/{product}', [App\Http\Controllers\Admin\AdminShopController::class, 'destroy'])
            ->name('shop.destroy');

        // Shop Order Management
        Route::get('/shop-orders', [App\Http\Controllers\Admin\AdminShopOrderController::class, 'index'])
            ->name('shop-orders.index');
        Route::get('/shop-orders/{order}', [App\Http\Controllers\Admin\AdminShopOrderController::class, 'show'])
            ->name('shop-orders.show');
        Route::post('/shop-orders/{order}/update-status', [App\Http\Controllers\Admin\AdminShopOrderController::class, 'updateStatus'])
            ->name('shop-orders.update-status');
        Route::post('/shop-orders/{order}/update-payment', [App\Http\Controllers\Admin\AdminShopOrderController::class, 'updatePaymentStatus'])
            ->name('shop-orders.update-payment');
        Route::post('/shop-orders/{order}/add-notes', [App\Http\Controllers\Admin\AdminShopOrderController::class, 'addNotes'])
            ->name('shop-orders.add-notes');
        Route::delete('/shop-orders/{order}', [App\Http\Controllers\Admin\AdminShopOrderController::class, 'destroy'])
            ->name('shop-orders.destroy');

        // Job Portfolio Management & Approval
        Route::get('/jobs/create', [App\Http\Controllers\Admin\AdminJobsController::class, 'create'])
            ->name('jobs.create');
        Route::post('/jobs', [App\Http\Controllers\Admin\AdminJobsController::class, 'store'])
            ->name('jobs.store');
        Route::get('/jobs', [App\Http\Controllers\Admin\AdminJobsController::class, 'index'])
            ->name('jobs.index');
        Route::post('/jobs/{portfolio}/approve', [App\Http\Controllers\Admin\AdminJobsController::class, 'approve'])
            ->name('jobs.approve');
        Route::post('/jobs/{portfolio}/reject', [App\Http\Controllers\Admin\AdminJobsController::class, 'reject'])
            ->name('jobs.reject');
        Route::delete('/jobs/{portfolio}', [App\Http\Controllers\Admin\AdminJobsController::class, 'destroy'])
            ->name('jobs.destroy');

        // Team Member Management & Approval
        Route::get('/team/create', [App\Http\Controllers\Admin\AdminTeamController::class, 'create'])
            ->name('team.create');
        Route::post('/team', [App\Http\Controllers\Admin\AdminTeamController::class, 'store'])
            ->name('team.store');
        Route::get('/team', [App\Http\Controllers\Admin\AdminTeamController::class, 'index'])
            ->name('team.index');
        Route::post('/team/{member}/approve', [App\Http\Controllers\Admin\AdminTeamController::class, 'approve'])
            ->name('team.approve');
        Route::post('/team/{member}/reject', [App\Http\Controllers\Admin\AdminTeamController::class, 'reject'])
            ->name('team.reject');
        Route::delete('/team/{member}', [App\Http\Controllers\Admin\AdminTeamController::class, 'destroy'])
            ->name('team.destroy');

        // Gallery Management
        Route::get('/gallery', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryIndex'])
            ->name('gallery.index');
        Route::get('/gallery/create', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryCreate'])
            ->name('gallery.create');
        Route::post('/gallery', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryStore'])
            ->name('gallery.store');
        Route::get('/gallery/{image}/edit', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryEdit'])
            ->name('gallery.edit');
        Route::put('/gallery/{image}', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryUpdate'])
            ->name('gallery.update');
        Route::post('/gallery/{image}/toggle-active', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryToggleActive'])
            ->name('gallery.toggle-active');
        Route::delete('/gallery/{image}', [App\Http\Controllers\Admin\AdminGalleryController::class, 'galleryDestroy'])
            ->name('gallery.destroy');

        // Magazine Management
        Route::get('/magazine', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineIndex'])
            ->name('magazine.index');
        Route::get('/magazine/create', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineCreate'])
            ->name('magazine.create');
        Route::post('/magazine', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineStore'])
            ->name('magazine.store');
        Route::get('/magazine/{image}/edit', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineEdit'])
            ->name('magazine.edit');
        Route::put('/magazine/{image}', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineUpdate'])
            ->name('magazine.update');
        Route::post('/magazine/{image}/toggle-active', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineToggleActive'])
            ->name('magazine.toggle-active');
        Route::delete('/magazine/{image}', [App\Http\Controllers\Admin\AdminGalleryController::class, 'magazineDestroy'])
            ->name('magazine.destroy');

        // News Articles Management
        Route::resource('articles', App\Http\Controllers\Admin\AdminArticleController::class);

        // Contact Messages
        Route::get('/contact-messages', [App\Http\Controllers\Admin\AdminContactController::class, 'index'])
            ->name('contact.index');
        Route::get('/contact-messages/{message}', [App\Http\Controllers\Admin\AdminContactController::class, 'show'])
            ->name('contact.show');
        Route::post('/contact-messages/{message}/mark-read', [App\Http\Controllers\Admin\AdminContactController::class, 'markAsRead'])
            ->name('contact.mark-read');
        Route::post('/contact-messages/{message}/mark-unread', [App\Http\Controllers\Admin\AdminContactController::class, 'markAsUnread'])
            ->name('contact.mark-unread');
        Route::delete('/contact-messages/{message}', [App\Http\Controllers\Admin\AdminContactController::class, 'destroy'])
            ->name('contact.destroy');
        Route::post('/contact-messages/bulk-delete', [App\Http\Controllers\Admin\AdminContactController::class, 'bulkDelete'])
            ->name('contact.bulk-delete');
        Route::post('/contact-messages/bulk-mark-read', [App\Http\Controllers\Admin\AdminContactController::class, 'bulkMarkAsRead'])
            ->name('contact.bulk-mark-read');

        // Platform Settings & Fee Configuration
        Route::get('/settings', [App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])
            ->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])
            ->name('settings.update');

        // Payout Management
        Route::get('/payouts', [App\Http\Controllers\Admin\PayoutController::class, 'index'])
            ->name('payouts.index');
        Route::get('/payouts/{payout}', [App\Http\Controllers\Admin\PayoutController::class, 'show'])
            ->name('payouts.show');
        Route::post('/payouts/{payout}/process', [App\Http\Controllers\Admin\PayoutController::class, 'process'])
            ->name('payouts.process');
        Route::post('/payouts/{payout}/complete', [App\Http\Controllers\Admin\PayoutController::class, 'complete'])
            ->name('payouts.complete');
        Route::post('/payouts/{payout}/fail', [App\Http\Controllers\Admin\PayoutController::class, 'fail'])
            ->name('payouts.fail');

        // Polls & Voting Management
        Route::get('/polls', [App\Http\Controllers\Admin\AdminPollController::class, 'index'])
            ->name('polls.index');
        Route::get('/polls/create', [App\Http\Controllers\Admin\AdminPollController::class, 'create'])
            ->name('polls.create');
        Route::post('/polls', [App\Http\Controllers\Admin\AdminPollController::class, 'store'])
            ->name('polls.store');
        Route::get('/polls/{poll}', [App\Http\Controllers\Admin\AdminPollController::class, 'show'])
            ->name('polls.show');
        Route::get('/polls/{poll}/edit', [App\Http\Controllers\Admin\AdminPollController::class, 'edit'])
            ->name('polls.edit');
        Route::put('/polls/{poll}', [App\Http\Controllers\Admin\AdminPollController::class, 'update'])
            ->name('polls.update');
        Route::post('/polls/{poll}/publish', [App\Http\Controllers\Admin\AdminPollController::class, 'publish'])
            ->name('polls.publish');
        Route::post('/polls/{poll}/close', [App\Http\Controllers\Admin\AdminPollController::class, 'close'])
            ->name('polls.close');
        Route::post('/polls/{poll}/contestants', [App\Http\Controllers\Admin\AdminPollController::class, 'addContestant'])
            ->name('polls.contestants.add');
        Route::delete('/polls/{poll}/contestants/{contestant}', [App\Http\Controllers\Admin\AdminPollController::class, 'removeContestant'])
            ->name('polls.contestants.remove');
        Route::post('/polls/{poll}/approve', [App\Http\Controllers\Admin\AdminPollController::class, 'approve'])
            ->name('polls.approve');
        Route::post('/polls/{poll}/suspend', [App\Http\Controllers\Admin\AdminPollController::class, 'suspend'])
            ->name('polls.suspend');
        Route::post('/polls/{poll}/reactivate', [App\Http\Controllers\Admin\AdminPollController::class, 'reactivate'])
            ->name('polls.reactivate');
        Route::delete('/polls/{poll}', [App\Http\Controllers\Admin\AdminPollController::class, 'destroy'])
            ->name('polls.destroy');

        // Surveys & Forms Management
        Route::get('/surveys', [App\Http\Controllers\Admin\AdminSurveyController::class, 'index'])
            ->name('surveys.index');
        Route::get('/surveys/create', [App\Http\Controllers\Admin\AdminSurveyController::class, 'create'])
            ->name('surveys.create');
        Route::post('/surveys', [App\Http\Controllers\Admin\AdminSurveyController::class, 'store'])
            ->name('surveys.store');
        Route::get('/surveys/{survey}', [App\Http\Controllers\Admin\AdminSurveyController::class, 'show'])
            ->name('surveys.show');
        Route::get('/surveys/{survey}/edit', [App\Http\Controllers\Admin\AdminSurveyController::class, 'edit'])
            ->name('surveys.edit');
        Route::put('/surveys/{survey}', [App\Http\Controllers\Admin\AdminSurveyController::class, 'update'])
            ->name('surveys.update');
        Route::delete('/surveys/{survey}', [App\Http\Controllers\Admin\AdminSurveyController::class, 'destroy'])
            ->name('surveys.destroy');
        Route::post('/surveys/{survey}/duplicate', [App\Http\Controllers\Admin\AdminSurveyController::class, 'duplicate'])
            ->name('surveys.duplicate');

        // Survey Builder
        Route::prefix('/surveys/{survey}/builder')->name('surveys.builder.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AdminSurveyBuilderController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\AdminSurveyBuilderController::class, 'store'])->name('store');
            Route::put('/questions/{question}', [App\Http\Controllers\Admin\AdminSurveyBuilderController::class, 'update'])->name('update');
            Route::delete('/questions/{question}', [App\Http\Controllers\Admin\AdminSurveyBuilderController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [App\Http\Controllers\Admin\AdminSurveyBuilderController::class, 'reorder'])->name('reorder');
            Route::post('/questions/{question}/duplicate', [App\Http\Controllers\Admin\AdminSurveyBuilderController::class, 'duplicate'])->name('duplicate');
        });

        // Survey Responses
        Route::get('/surveys/{survey}/responses', [App\Http\Controllers\Admin\AdminSurveyResponseController::class, 'index'])
            ->name('surveys.responses.index');
        Route::get('/surveys/{survey}/responses/{response}', [App\Http\Controllers\Admin\AdminSurveyResponseController::class, 'show'])
            ->name('surveys.responses.show');
        Route::delete('/surveys/{survey}/responses/{response}', [App\Http\Controllers\Admin\AdminSurveyResponseController::class, 'destroy'])
            ->name('surveys.responses.destroy');
        Route::post('/surveys/{survey}/responses/bulk-delete', [App\Http\Controllers\Admin\AdminSurveyResponseController::class, 'bulkDelete'])
            ->name('surveys.responses.bulk-delete');
        Route::get('/surveys/{survey}/responses/export', [App\Http\Controllers\Admin\AdminSurveyResponseController::class, 'export'])
            ->name('surveys.responses.export');
    });
});

// Organization Authentication Routes
Route::prefix('organization')->name('organization.')->group(function () {
    // Guest routes
    Route::middleware('guest:company')->group(function () {
        Route::get('/register', [CompanyAuthController::class, 'showRegisterForm'])
            ->name('register');
        Route::post('/register', [CompanyAuthController::class, 'register'])
            ->middleware('throttle:5,1'); // 5 registrations per minute

        Route::get('/login', [CompanyAuthController::class, 'showLoginForm'])
            ->name('login');
        Route::post('/login', [CompanyAuthController::class, 'login'])
            ->middleware('throttle:5,1'); // 5 login attempts per minute

        // OTP Login Routes
        Route::post('/send-otp', [App\Http\Controllers\Company\CompanyOTPController::class, 'sendOTP'])
            ->name('send-otp')
            ->middleware('throttle:5,1'); // 5 OTP requests per minute
        Route::post('/verify-otp', [App\Http\Controllers\Company\CompanyOTPController::class, 'verifyOTP'])
            ->name('verify-otp')
            ->middleware('throttle:5,1'); // 5 verification attempts per minute
    });

    // Authenticated routes
    Route::middleware('auth:company')->group(function () {
        Route::post('/logout', [CompanyAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Profile Routes
        Route::get('/profile', [DashboardController::class, 'editProfile'])
            ->name('profile.edit');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])
            ->name('profile.update');

        Route::resource('conferences', ConferenceController::class);
        Route::post('/conferences/{conference}/duplicate', [ConferenceController::class, 'duplicate'])
            ->name('conferences.duplicate');

        // ADD THESE ROUTES FOR IMAGE REMOVAL
        Route::delete('/conferences/{conference}/remove-logo', [ConferenceController::class, 'removeLogo'])
            ->name('conferences.remove-logo');
        Route::delete('/conferences/{conference}/remove-header', [ConferenceController::class, 'removeHeader'])
            ->name('conferences.remove-header');

        // Form Builder Routes
        Route::prefix('conferences/{conference}/form-builder')->name('conferences.form-builder.')->group(function () {
            Route::get('/', [FormBuilderController::class, 'index'])->name('index');
            Route::post('/', [FormBuilderController::class, 'store'])->name('store');
            Route::put('/fields/{field}', [FormBuilderController::class, 'update'])->name('update');
            Route::delete('/fields/{field}', [FormBuilderController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [FormBuilderController::class, 'reorder'])->name('reorder');
        });

        Route::get('/conferences/{conference}/registrations', [RegistrationController::class, 'index'])
            ->name('conferences.registrations.index');
        Route::get('/conferences/{conference}/registrations/{registration}', [RegistrationController::class, 'show'])
            ->name('conferences.registrations.show');
            
        Route::post('/conferences/{conference}/registrations/{registration}/mark-attendance', [RegistrationController::class, 'markAttendance'])
            ->name('conferences.registrations.mark-attendance');
        Route::post('/conferences/{conference}/registrations/bulk-mark-attendance', [RegistrationController::class, 'bulkMarkAttendance'])
            ->name('conferences.registrations.bulk-mark-attendance');
        Route::delete('/conferences/{conference}/registrations/{registration}', [RegistrationController::class, 'destroy'])
            ->name('conferences.registrations.destroy');

        Route::get('/conferences/{conference}/bulk-email', [RegistrationController::class, 'showBulkEmailForm'])
            ->name('conferences.bulk-email');
        Route::post('/conferences/{conference}/bulk-email', [RegistrationController::class, 'sendBulkEmail'])
            ->name('conferences.send-bulk-email');

        Route::get('/conferences/{conference}/export/{format}', [ExportController::class, 'export'])
            ->name('conferences.export')
            ->where('format', 'pdf|csv|excel|xlsx');

        // Survey Routes
        Route::resource('surveys', SurveyController::class);
        Route::post('/surveys/{survey}/duplicate', [SurveyController::class, 'duplicate'])
            ->name('surveys.duplicate');

        // Survey Builder Routes
        Route::prefix('surveys/{survey}/builder')->name('surveys.builder.')->group(function () {
            Route::get('/', [SurveyBuilderController::class, 'index'])->name('index');
            Route::post('/', [SurveyBuilderController::class, 'store'])->name('store');
            Route::put('/questions/{question}', [SurveyBuilderController::class, 'update'])->name('update');
            Route::delete('/questions/{question}', [SurveyBuilderController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [SurveyBuilderController::class, 'reorder'])->name('reorder');
            Route::post('/questions/{question}/duplicate', [SurveyBuilderController::class, 'duplicate'])->name('duplicate');
        });

        // Survey Response Routes
        Route::get('/surveys/{survey}/responses', [SurveyResponseController::class, 'index'])
            ->name('surveys.responses.index');
        Route::get('/surveys/{survey}/responses/{response}', [SurveyResponseController::class, 'show'])
            ->name('surveys.responses.show');
        Route::delete('/surveys/{survey}/responses/{response}', [SurveyResponseController::class, 'destroy'])
            ->name('surveys.responses.destroy');
        Route::post('/surveys/{survey}/responses/bulk-delete', [SurveyResponseController::class, 'bulkDelete'])
            ->name('surveys.responses.bulk-delete');
        Route::get('/surveys/{survey}/responses/export', [SurveyResponseController::class, 'export'])
            ->name('surveys.responses.export');

        // SMS Routes
        Route::prefix('sms')->name('sms.')->group(function () {
            // Dashboard
            Route::get('/', [SmsDashboardController::class, 'index'])
                ->name('dashboard');

            // Wallet & Credit Purchase
            Route::get('/wallet', [SmsWalletController::class, 'index'])
                ->name('wallet.index');
            Route::post('/wallet/purchase', [SmsWalletController::class, 'initializePayment'])
                ->name('wallet.purchase');
            Route::get('/payment/callback', [SmsWalletController::class, 'handlePaymentCallback'])
                ->name('payment.callback');
            Route::get('/transactions/{id}', [SmsWalletController::class, 'showTransaction'])
                ->name('transactions.show');

            // Campaigns
            Route::get('/campaigns', [SmsCampaignController::class, 'index'])
                ->name('campaigns.index');
            Route::get('/campaigns/send-single', [SmsCampaignController::class, 'createSingle'])
                ->name('campaigns.send-single');
            Route::post('/campaigns/send-single', [SmsCampaignController::class, 'sendSingle'])
                ->middleware('throttle:10,1') // 10 requests per minute
                ->name('campaigns.send-single.store');
            Route::get('/campaigns/send-bulk', [SmsCampaignController::class, 'createBulk'])
                ->name('campaigns.send-bulk');
            Route::post('/campaigns/send-bulk', [SmsCampaignController::class, 'sendBulk'])
                ->middleware('throttle:10,1') // 10 requests per minute
                ->name('campaigns.send-bulk.store');
            Route::get('/campaigns/{id}', [SmsCampaignController::class, 'show'])
                ->name('campaigns.show');
            Route::get('/campaigns/{id}/resend', [SmsCampaignController::class, 'resend'])
                ->name('campaigns.resend');
            Route::post('/campaigns/{id}/cancel', [SmsCampaignController::class, 'cancel'])
                ->name('campaigns.cancel');
            Route::delete('/campaigns/{id}', [SmsCampaignController::class, 'destroy'])
                ->name('campaigns.destroy');

            // Contacts
            Route::get('/contacts', [SmsContactController::class, 'index'])
                ->name('contacts.index');
            Route::get('/contacts/create', [SmsContactController::class, 'create'])
                ->name('contacts.create');
            Route::post('/contacts', [SmsContactController::class, 'store'])
                ->name('contacts.store');
            Route::get('/contacts/{id}/edit', [SmsContactController::class, 'edit'])
                ->name('contacts.edit');
            Route::put('/contacts/{id}', [SmsContactController::class, 'update'])
                ->name('contacts.update');
            Route::delete('/contacts/{id}', [SmsContactController::class, 'destroy'])
                ->name('contacts.destroy');
            Route::get('/contacts/import', [SmsContactController::class, 'showImport'])
                ->name('contacts.import');
            Route::post('/contacts/import', [SmsContactController::class, 'import'])
                ->name('contacts.import.store');
            Route::post('/contacts/bulk-delete', [SmsContactController::class, 'bulkDelete'])
                ->name('contacts.bulk-delete');
            Route::get('/contacts/sample-csv', [SmsContactController::class, 'downloadSample'])
                ->name('contacts.sample-csv');

            // Sender IDs
            Route::get('/sender-ids', [SmsSenderIdController::class, 'index'])
                ->name('sender-ids.index');
            Route::get('/sender-ids/create', [SmsSenderIdController::class, 'create'])
                ->name('sender-ids.create');
            Route::post('/sender-ids', [SmsSenderIdController::class, 'store'])
                ->name('sender-ids.store');
            Route::get('/sender-ids/{id}/edit', [SmsSenderIdController::class, 'edit'])
                ->name('sender-ids.edit');
            Route::put('/sender-ids/{id}', [SmsSenderIdController::class, 'update'])
                ->name('sender-ids.update');
            Route::post('/sender-ids/{id}/set-default', [SmsSenderIdController::class, 'setDefault'])
                ->name('sender-ids.set-default');
            Route::delete('/sender-ids/{id}', [SmsSenderIdController::class, 'destroy'])
                ->name('sender-ids.destroy');
        });

        // Event Management Routes
        Route::resource('events', App\Http\Controllers\Company\EventController::class);
        Route::post('/events/{event}/duplicate', [App\Http\Controllers\Company\EventController::class, 'duplicate'])
            ->name('events.duplicate');
        Route::post('/events/{event}/publish', [App\Http\Controllers\Company\EventController::class, 'publish'])
            ->name('events.publish');

        // Event Attendees & Check-in
        Route::get('/events/{event}/attendees', [App\Http\Controllers\Company\EventAttendeeController::class, 'index'])
            ->name('events.attendees.index');
        Route::post('/events/{event}/attendees/{attendee}/check-in', [App\Http\Controllers\Company\EventAttendeeController::class, 'checkIn'])
            ->name('events.attendees.check-in');
        Route::get('/events/{event}/attendees/export', [App\Http\Controllers\Company\EventAttendeeController::class, 'export'])
            ->name('events.attendees.export');

        // Event Analytics & Reporting
        Route::get('/events/{event}/analytics', [App\Http\Controllers\Company\EventAnalyticsController::class, 'show'])
            ->name('events.analytics');

        // Finance - Payouts, Invoices, Bank Accounts
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/payouts', [App\Http\Controllers\Company\FinanceController::class, 'payouts'])
                ->name('payouts');
            Route::get('/invoices', [App\Http\Controllers\Company\FinanceController::class, 'invoices'])
                ->name('invoices');
            Route::get('/bank-accounts', [App\Http\Controllers\Company\FinanceController::class, 'bankAccounts'])
                ->name('bank-accounts');
            Route::post('/bank-accounts', [App\Http\Controllers\Company\FinanceController::class, 'storeBankAccount'])
                ->name('bank-accounts.store');
            Route::put('/bank-accounts/{account}', [App\Http\Controllers\Company\FinanceController::class, 'updateBankAccount'])
                ->name('bank-accounts.update');
            Route::delete('/bank-accounts/{account}', [App\Http\Controllers\Company\FinanceController::class, 'deleteBankAccount'])
                ->name('bank-accounts.delete');
            Route::post('/bank-accounts/{account}/set-default', [App\Http\Controllers\Company\FinanceController::class, 'setDefaultAccount'])
                ->name('bank-accounts.set-default');
        });

        // Event Payout Management
        Route::get('/payouts', [App\Http\Controllers\Company\PayoutController::class, 'index'])
            ->name('payouts.index');
        Route::get('/payouts/{payout}', [App\Http\Controllers\Company\PayoutController::class, 'show'])
            ->name('payouts.show');
        Route::get('/payouts/{payout}/setup', [App\Http\Controllers\Company\PayoutController::class, 'setup'])
            ->name('payouts.setup');
        Route::post('/payouts/{payout}/setup', [App\Http\Controllers\Company\PayoutController::class, 'storePaymentAccount'])
            ->name('payouts.store-account');
        Route::post('/payouts/{payout}/request', [App\Http\Controllers\Company\PayoutController::class, 'requestPayout'])
            ->name('payouts.request');

        // Organization Settings
        Route::get('/organization/settings', [App\Http\Controllers\Company\OrganizationSettingsController::class, 'index'])
            ->name('organization.settings');
        Route::put('/organization/settings', [App\Http\Controllers\Company\OrganizationSettingsController::class, 'update'])
            ->name('organization.settings.update');

        // Polls & Voting/Pageant Management
        Route::resource('polls', App\Http\Controllers\Company\PollController::class);
        Route::post('/polls/{poll}/publish', [App\Http\Controllers\Company\PollController::class, 'publish'])
            ->name('polls.publish');
        Route::post('/polls/{poll}/close', [App\Http\Controllers\Company\PollController::class, 'close'])
            ->name('polls.close');
        Route::post('/polls/{poll}/contestants', [App\Http\Controllers\Company\PollController::class, 'addContestant'])
            ->name('polls.contestants.add');
        Route::delete('/polls/{poll}/contestants/{contestant}', [App\Http\Controllers\Company\PollController::class, 'removeContestant'])
            ->name('polls.contestants.remove');

        // Ticket Scanning & Verification
        Route::get('/events/{event}/scanner', [App\Http\Controllers\TicketVerificationController::class, 'showScanner'])
            ->name('events.scanner');
        Route::post('/tickets/verify', [App\Http\Controllers\TicketVerificationController::class, 'verify'])
            ->name('tickets.verify');
        Route::get('/events/{event}/check-in-activity', [App\Http\Controllers\TicketVerificationController::class, 'getActivity'])
            ->name('events.check-in-activity');

        // Staff/Attendant Management
        Route::resource('staff', App\Http\Controllers\Organization\StaffController::class);
        Route::patch('/staff/{staff}/suspend', [App\Http\Controllers\Organization\StaffController::class, 'suspend'])
            ->name('staff.suspend');
        Route::patch('/staff/{staff}/activate', [App\Http\Controllers\Organization\StaffController::class, 'activate'])
            ->name('staff.activate');
    });
});

// Staff/Attendant Routes (Phone OTP Login)
Route::prefix('staff')->name('staff.')->group(function () {
    // Guest routes
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [App\Http\Controllers\Staff\StaffAuthController::class, 'showLoginForm'])
            ->name('login');
        Route::post('/send-otp', [App\Http\Controllers\Staff\StaffAuthController::class, 'sendOTP'])
            ->name('send-otp')
            ->middleware('throttle:5,1'); // 5 OTP requests per minute
        Route::post('/verify-otp', [App\Http\Controllers\Staff\StaffAuthController::class, 'verifyOTP'])
            ->name('verify-otp')
            ->middleware('throttle:5,1'); // 5 verification attempts per minute
    });

    // Authenticated routes
    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Staff\StaffAuthController::class, 'logout'])
            ->name('logout');
        Route::get('/scanner', [App\Http\Controllers\TicketVerificationController::class, 'showStaffScanner'])
            ->name('scanner');
        Route::post('/verify-ticket', [App\Http\Controllers\TicketVerificationController::class, 'verify'])
            ->name('verify-ticket');
    });
});

// API Routes for AJAX requests (Staff Scanner)
Route::prefix('api/staff')->middleware('auth:staff')->group(function () {
    Route::post('/verify-ticket', [App\Http\Controllers\TicketVerificationController::class, 'verify']);
});
