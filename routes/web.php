<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Seller;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════
// PUBLIC ROUTES
// ══════════════════════════════════════
Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/produk', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');
Route::get('/kebijakan', [PageController::class, 'policy'])->name('pages.policy');

// Toko publik
Route::get('/toko', [StoreController::class, 'index'])->name('stores.index');
Route::get('/toko/{store:slug}', [StoreController::class, 'show'])->name('stores.show');

// ══════════════════════════════════════
// CART (boleh tanpa login)
// ══════════════════════════════════════
Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');

// ══════════════════════════════════════
// PAYMENT WEBHOOK (tanpa auth)
// ══════════════════════════════════════
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// ══════════════════════════════════════
// AUTH REQUIRED
// ══════════════════════════════════════
Route::middleware('auth')->group(function () {

    // Payment redirect
    Route::get('/payment/finish',   [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error',    [PaymentController::class, 'error'])->name('payment.error');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/kota', [CheckoutController::class, 'getCities'])->name('checkout.cities');
    Route::post('/checkout/ongkir', [CheckoutController::class, 'calculateShipping'])->name('checkout.shipping');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/sukses/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/ulasan', [OrderController::class, 'storeReview'])->name('orders.review');
    Route::post('/pesanan/{order}/batalkan', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Daftar seller
    Route::get('/seller/daftar', [Seller\RegisterController::class, 'create'])->name('seller.register');
    Route::post('/seller/daftar', [Seller\RegisterController::class, 'store'])->name('seller.register.store');

    // ── SELLER DASHBOARD ──
    Route::prefix('seller')->name('seller.')->middleware('seller')->group(function () {
        Route::get('/', [Seller\DashboardController::class, 'index'])->name('dashboard');

        // Produk seller
        Route::resource('products', Seller\ProductController::class);
        Route::delete('products/images/{image}', [Seller\ProductController::class, 'deleteImage'])
            ->name('products.image.delete');

        // Pesanan seller
        Route::get('orders', [Seller\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Seller\OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [Seller\OrderController::class, 'update'])->name('orders.update');

        // Keuangan
        Route::get('keuangan', [Seller\FinanceController::class, 'index'])->name('finance');
        Route::post('withdraw', [Seller\FinanceController::class, 'withdraw'])->name('withdraw');

        // Profil toko
        Route::get('profil', [Seller\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profil', [Seller\ProfileController::class, 'update'])->name('profile.update');
    });

    // ── ADMIN PANEL ──
    // Menggunakan middleware 'admin' yang sudah ada
    // (role admin & superadmin keduanya bisa akses)
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        // Dashboard
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Produk
        Route::resource('products', Admin\ProductController::class);
        Route::delete('products/images/{image}', [Admin\ProductController::class, 'deleteImage'])
            ->name('products.image.delete');

        // Pesanan
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');
        Route::delete('orders/{order}', [Admin\OrderController::class, 'destroy'])->name('orders.destroy');

        // Kategori & Brand
        Route::resource('categories', Admin\CategoryController::class);
        Route::resource('brands', Admin\BrandController::class);

        // Laporan & Ulasan
        Route::get('laporan', [Admin\ReportController::class, 'index'])->name('reports');
        Route::resource('reviews', Admin\ReviewController::class)->only(['index', 'update', 'destroy']);

        // Marketplace — Kelola Toko
        Route::get('stores', [Admin\StoreController::class, 'index'])->name('stores.index');
        Route::put('stores/{store}/verify', [Admin\StoreController::class, 'verify'])->name('stores.verify');
        Route::put('stores/{store}/suspend', [Admin\StoreController::class, 'suspend'])->name('stores.suspend');

        // Marketplace — Pencairan Dana
        Route::get('withdrawals', [Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::put('withdrawals/{withdrawal}/process', [Admin\WithdrawalController::class, 'process'])
            ->name('withdrawals.process');
    });
});

require __DIR__.'/auth.php';