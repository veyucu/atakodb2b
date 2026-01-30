<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CariEkstreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\PlasiyerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Plasiyer & Admin Routes (customer selection - no middleware check)
Route::middleware(['auth'])->prefix('plasiyer')->name('plasiyer.')->group(function () {
    Route::get('/select-customer', [PlasiyerController::class, 'selectCustomer'])->name('selectCustomer');
    Route::post('/set-customer', [PlasiyerController::class, 'setCustomer'])->name('setCustomer');
    Route::post('/clear-customer', [PlasiyerController::class, 'clearCustomer'])->name('clearCustomer');
});

// Order History Routes (for all authenticated users)
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/my-orders', [OrderHistoryController::class, 'index'])->name('history');
    Route::get('/{order}/detail', [OrderHistoryController::class, 'show'])->name('detail');
});

// Cari Ekstre (Account Statement) Route
Route::middleware(['auth'])->group(function () {
    Route::get('/cari-ekstre', [CariEkstreController::class, 'index'])->name('cari.ekstre');
    Route::get('/cari-ekstre/fatura-detay', [CariEkstreController::class, 'getFaturaDetay'])->name('cari.fatura.detay');
});

// Public/Customer Routes (requires authentication and customer selection for plasiyer)
Route::middleware(['auth', 'customer.selected'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    Route::get('/grup/{grup}', [HomeController::class, 'filterByGroup'])->name('filter.group');
    Route::get('/muadil-products/{muadilKodu}', [HomeController::class, 'getMuadilProducts'])->name('muadil.products');

    // Product Detail Route
    Route::get('/product/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('product.show');
    Route::get('/product/{id}/modal', [App\Http\Controllers\ProductController::class, 'showModal'])->name('product.modal');

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{id}', [CartController::class, 'update'])->name('update');
        Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    });

    // Order success page
    Route::get('/order/success/{order}', [CartController::class, 'orderSuccess'])->name('order.success');
});

// Admin Routes
Route::middleware(['auth', 'user.type:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Slider Management
    Route::resource('sliders', SliderController::class);

    // Product Management
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/quick-update', [ProductController::class, 'quickUpdate'])->name('products.quickUpdate');
    Route::delete('products/{product}/delete-image', [ProductController::class, 'deleteImage'])->name('products.deleteImage');

    // User Management
    Route::resource('users', UserController::class);

    // Order Management
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Site Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/logo', [\App\Http\Controllers\Admin\SiteSettingController::class, 'deleteLogo'])->name('settings.deleteLogo');
});

// Plasiyer Routes
Route::middleware(['auth', 'user.type:plasiyer,admin'])->prefix('plasiyer')->name('plasiyer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\PlasiyerController::class, 'dashboard'])->name('dashboard');
    Route::get('/customer-activities', [\App\Http\Controllers\CustomerActivityController::class, 'index'])->name('customerActivities');
});

// Activity Logging Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/activity/campaign', [\App\Http\Controllers\CustomerActivityController::class, 'logCampaignView'])->name('activity.campaign');
});


