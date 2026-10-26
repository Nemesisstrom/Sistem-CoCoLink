<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\KoperasiInduk\OrderAllocationController;
use App\Http\Controllers\KoperasiInduk\LaporanController;
use App\Http\Controllers\KoperasiAnak\SupplyController;
use App\Http\Controllers\Pengepul\OrderController;
use App\Http\Controllers\Driver\DriverShipmentController;

/*
|--------------------------------------------------------------------------
| Web Routes - Platform Web CoCoLink
|--------------------------------------------------------------------------
*/

// 1. Rute Halaman Utama (Pilihan Redirect Berdasarkan Role)
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return redirect()->route('super-admin.dashboard');
        } elseif ($user->hasRole('koperasi_induk')) {
            return redirect()->route('koperasi-induk.dashboard');
        } elseif ($user->hasRole('pengepul')) {
            return redirect()->route('pengepul.dashboard');
        } elseif ($user->hasRole('koperasi_anak')) {
            return redirect()->route('koperasi-anak.dashboard');
        } elseif ($user->hasRole('driver')) {
            return redirect()->route('driver.dashboard');
        }
    }
    return redirect()->route('login');
});

// 2. Guest Routes (Autentikasi Pengunjung)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 3. Protected Routes (Akses Pengguna Terautentikasi)
Route::middleware('auth')->group(function () {

    // Process Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ------------------------------------------------------------------------
    // Role: Super Admin Platform
    // ------------------------------------------------------------------------
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/users/{id}/role', [SuperAdminDashboardController::class, 'updateUserRole'])->name('users.updateRole');
        Route::get('/audit-logs/export', [SuperAdminDashboardController::class, 'exportAuditLogs'])->name('audit-logs.export');
    });

    // ------------------------------------------------------------------------
    // Role: Admin Koperasi Induk (Central Ops)
    // ------------------------------------------------------------------------
    Route::middleware(['role:koperasi_induk'])->prefix('koperasi-induk')->name('koperasi-induk.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'koperasiIndukDashboard'])->name('dashboard');
        Route::post('/orders/{orderId}/allocate', [OrderAllocationController::class, 'allocate'])->name('orders.allocate');

        // Fitur Laporan Transaksi & Export PDF
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'downloadPdf'])->name('laporan.pdf');
    });

    // ------------------------------------------------------------------------
    // Role: Pembeli Industri / Pengepul
    // ------------------------------------------------------------------------
    Route::middleware(['role:pengepul'])->prefix('pengepul')->name('pengepul.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengepulDashboard'])->name('dashboard');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{id}/confirm', [OrderController::class, 'confirmReceipt'])->name('orders.confirm');
        Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    });

    // ------------------------------------------------------------------------
    // Role: Koperasi Anak (Penyedia Pasokan)
    // ------------------------------------------------------------------------
    Route::middleware(['role:koperasi_anak'])->prefix('koperasi-anak')->name('koperasi-anak.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'koperasiAnakDashboard'])->name('dashboard');

        // Aksi Terima/Tolak Alokasi & Tandai Stok Siap
        Route::patch('/allocations/{id}/respond', [SupplyController::class, 'respondAllocation'])->name('allocations.respond');
        Route::patch('/allocations/{id}/ready', [SupplyController::class, 'updateReadyStatus'])->name('allocations.ready');
    });

    // ------------------------------------------------------------------------
    // Role: Driver Logistik (Mitra Transportasi)
    // ------------------------------------------------------------------------
    Route::middleware(['role:driver'])->prefix('driver')->name('driver.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'driverDashboard'])->name('dashboard');
        Route::patch('/shipments/{id}/status', [DriverShipmentController::class, 'updateStatus'])->name('shipments.updateStatus');
        Route::post('/shipments/{id}/complete', [DriverShipmentController::class, 'completeShipment'])->name('shipments.complete');
    });

});
