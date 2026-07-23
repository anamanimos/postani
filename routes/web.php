<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

// Public route - redirect to login
Route::get('/', fn() => redirect()->route('login'));

// All authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Master Data
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('products', ProductController::class);
    Route::get('/products/search/json', [ProductController::class, 'search'])->name('products.search');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    
    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/price-history', [PurchaseController::class, 'getPriceHistory'])->name('purchases.price-history');
    
    // Sales
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::patch('/sales/{sale}/update-date', [SaleController::class, 'updateDate'])->name('sales.update-date');
    
    // Payments
    Route::get('/payments/suppliers', [PaymentController::class, 'supplierPayments'])->name('payments.suppliers');
    Route::post('/payments/suppliers', [PaymentController::class, 'storeSupplierPayment'])->name('payments.suppliers.store');
    Route::get('/payments/customers', [PaymentController::class, 'customerPayments'])->name('payments.customers');
    Route::post('/payments/customers', [PaymentController::class, 'storeCustomerPayment'])->name('payments.customers.store');
    
    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
    Route::get('/reports/debts', [ReportController::class, 'debts'])->name('reports.debts');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Cash Transactions
    Route::resource('cash-transactions', CashTransactionController::class);

    // Galleries
    Route::resource('galleries', GalleryController::class)->only(['index', 'store', 'destroy']);
    Route::get('/api/galleries', [GalleryController::class, 'apiIndex'])->name('api.galleries');
    Route::post('/galleries/{gallery}/labels', [GalleryController::class, 'updateLabels'])->name('galleries.update-labels');
    
    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
