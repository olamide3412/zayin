<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TestimonyController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\QuoteController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\CommerceController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\SolarCalculatorController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\MaintenancePlanController as AdminPlanController;
use App\Http\Controllers\Admin\StateFeeController as AdminStateFeeController;
use App\Http\Controllers\Admin\SolarCalcSettingController as AdminSolarCalcSettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Public Routes (SSR & SEO Optimized) ─────────────────────────────────────
Route::get('/', HomeController::class)->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.store');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/maintenance-plans', [PageController::class, 'maintenancePlans'])->name('plans.index');

// Blog & Testimonials Public Routes
Route::get('/blog', [BlogController::class, 'publicIndex'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'publicShow'])->name('blog.show');
Route::get('/testimonials', [TestimonyController::class, 'publicIndex'])->name('testimonials.index');
Route::post('/testimonials', [TestimonyController::class, 'publicStore'])->name('testimonials.store');

// Legal pages
Route::get('/privacy-policy', fn() => app(PageController::class)->legal('privacy-policy'))->name('pages.privacy');
Route::get('/terms', fn() => app(PageController::class)->legal('terms'))->name('pages.terms');
Route::get('/warranty', fn() => app(PageController::class)->legal('warranty'))->name('pages.warranty');
Route::get('/refund-policy', fn() => app(PageController::class)->legal('refund-policy'))->name('pages.refund');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Solar Load Calculator (Public, SSR, Indexable & API endpoints)
Route::get('/solar-calculator', [SolarCalculatorController::class, 'index'])->name('solar.index');
Route::post('/solar-calculator/calculate', [SolarCalculatorController::class, 'calculate'])->name('solar.calculate')->middleware('throttle:60,1');
Route::post('/solar-calculator/save', [SolarCalculatorController::class, 'save'])->name('solar.save')->middleware('throttle:60,1');
Route::get('/solar-calculator/r/{uuid}', [SolarCalculatorController::class, 'showShared'])->name('solar.shared');
Route::get('/solar-calculator/r/{uuid}/pdf', [SolarCalculatorController::class, 'downloadPdf'])->name('solar.pdf')->middleware('throttle:30,1');

// Packages / Shop
Route::get('/packages', [ProductController::class, 'index'])->name('packages.index');
Route::get('/packages/{category}', [ProductController::class, 'category'])->name('packages.category');
Route::get('/packages/{category}/{slug}', [ProductController::class, 'show'])->name('packages.show');

// Projects & Video Proof
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

// Quotes & Bookings
Route::get('/request-quote', [QuoteController::class, 'create'])->name('quotes.create');
Route::get('/quote', [QuoteController::class, 'create'])->name('quote.create');
Route::post('/request-quote', [QuoteController::class, 'store'])->name('quotes.store');
Route::post('/quote', [QuoteController::class, 'store'])->name('quote.store');

Route::get('/book-installation', [BookingController::class, 'create'])->name('bookings.create');
Route::get('/book', [BookingController::class, 'create'])->name('booking.create');
Route::post('/book-installation', [BookingController::class, 'store'])->name('bookings.store');
Route::post('/book', [BookingController::class, 'store'])->name('booking.store');

// Contact alias
Route::post('/contact/submit', [PageController::class, 'submitContact'])->name('contact.submit');

// Commerce & Checkout
Route::get('/cart', [CommerceController::class, 'showCart'])->name('cart.show');
Route::post('/cart/items', [CommerceController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/items/{id}', [CommerceController::class, 'updateCartItem'])->name('cart.update');
Route::delete('/cart/items/{id}', [CommerceController::class, 'removeCartItem'])->name('cart.remove');
Route::get('/checkout', [CommerceController::class, 'checkout'])->name('checkout.show');
Route::post('/checkout', [CommerceController::class, 'processCheckout'])->name('checkout.process');
Route::get('/orders/{number}/track', [CommerceController::class, 'trackOrder'])->name('orders.track');
Route::get('/orders/{number}/bank-transfer', [CommerceController::class, 'bankTransfer'])->name('orders.bank_transfer');
Route::post('/orders/{number}/proof', [CommerceController::class, 'uploadProof'])->name('orders.upload_proof');

// Sitemaps & robots
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLinkRequestForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard & Staff Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // ── Image Upload ─────────────────────────────────────────────────────────
    Route::post('/admin/upload-image', [ImageUploadController::class, 'store'])->name('admin.upload-image');
    Route::post('/admin/upload-media', [ImageUploadController::class, 'uploadMedia'])->name('admin.upload-media');

    // ── Admin Blog ───────────────────────────────────────────────────────────
    Route::get('/admin/blog', [BlogController::class, 'index'])->name('admin.blog.index');
    Route::get('/admin/blog/create', [BlogController::class, 'create'])->name('admin.blog.create');
    Route::post('/admin/blog', [BlogController::class, 'store'])->name('admin.blog.store');
    Route::get('/admin/blog/{blog}/edit', [BlogController::class, 'edit'])->name('admin.blog.edit');
    Route::put('/admin/blog/{blog}', [BlogController::class, 'update'])->name('admin.blog.update');
    Route::delete('/admin/blog/{blog}', [BlogController::class, 'destroy'])->name('admin.blog.destroy');

    // ── Admin Testimonials ───────────────────────────────────────────────────
    Route::get('/admin/testimonials', [TestimonyController::class, 'index'])->name('admin.testimonials.index');
    Route::patch('/admin/testimonials/{testimony}/approve', [TestimonyController::class, 'approve'])->name('admin.testimonials.approve');
    Route::patch('/admin/testimonials/{testimony}/reject', [TestimonyController::class, 'reject'])->name('admin.testimonials.reject');
    Route::delete('/admin/testimonials/{testimony}', [TestimonyController::class, 'destroy'])->name('admin.testimonials.destroy');

    // ── Admin News & Media ───────────────────────────────────────────────────
    Route::get('/admin/news', [NewsController::class, 'index'])->name('admin.news.index');
    Route::get('/admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
    Route::post('/admin/news', [NewsController::class, 'store'])->name('admin.news.store');
    Route::get('/admin/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
    Route::put('/admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
    Route::delete('/admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');

    // ── Admin Partners ───────────────────────────────────────────────────────
    Route::get('/admin/partners', [\App\Http\Controllers\PartnerController::class, 'index'])->name('admin.partners.index');
    Route::get('/admin/partners/create', [\App\Http\Controllers\PartnerController::class, 'create'])->name('admin.partners.create');
    Route::post('/admin/partners', [\App\Http\Controllers\PartnerController::class, 'store'])->name('admin.partners.store');
    Route::get('/admin/partners/{partner}/edit', [\App\Http\Controllers\PartnerController::class, 'edit'])->name('admin.partners.edit');
    Route::put('/admin/partners/{partner}', [\App\Http\Controllers\PartnerController::class, 'update'])->name('admin.partners.update');
    Route::delete('/admin/partners/{partner}', [\App\Http\Controllers\PartnerController::class, 'destroy'])->name('admin.partners.destroy');

    // ── Admin Settings ───────────────────────────────────────────────────────
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // ── Admin Profile ────────────────────────────────────────────────────────
    Route::get('/admin/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');

    // ── Admin Products & Pricing ──────────────────────────────────────────────
    Route::get('/admin/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::patch('/admin/products/{product}/quick-update', [AdminProductController::class, 'quickUpdate'])->name('admin.products.quick-update');
    Route::delete('/admin/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

    // ── Admin Maintenance Plans & Pricing ─────────────────────────────────────
    Route::get('/admin/maintenance-plans', [AdminPlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/admin/maintenance-plans/create', [AdminPlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/admin/maintenance-plans', [AdminPlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/admin/maintenance-plans/{plan}/edit', [AdminPlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('/admin/maintenance-plans/{plan}', [AdminPlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('/admin/maintenance-plans/{plan}', [AdminPlanController::class, 'destroy'])->name('admin.plans.destroy');

    // ── Admin Regional Delivery & Install Fees ────────────────────────────────
    Route::get('/admin/delivery-fees', [AdminStateFeeController::class, 'index'])->name('admin.state-fees.index');
    Route::patch('/admin/delivery-fees/{state}', [AdminStateFeeController::class, 'update'])->name('admin.state-fees.update');
    Route::post('/admin/delivery-fees/bulk', [AdminStateFeeController::class, 'bulkUpdate'])->name('admin.state-fees.bulk');

    // ── Admin Solar Calculator Settings & Presets ────────────────────────────
    Route::get('/admin/solar-calculator-settings', [AdminSolarCalcSettingController::class, 'index'])->name('admin.solar-settings.index');
    Route::post('/admin/solar-calculator-settings', [AdminSolarCalcSettingController::class, 'update'])->name('admin.solar-settings.update');
    Route::patch('/admin/solar-calculator-settings/presets/{preset}', [AdminSolarCalcSettingController::class, 'updatePreset'])->name('admin.solar-settings.update-preset');
});

