<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\WasteTypeController;
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

        // Mapping role pengguna ke route dashboard tujuan
        $dashboards = [
            'super_admin'    => 'super-admin.dashboard',
            'koperasi_induk' => 'koperasi-induk.dashboard',
            'pengepul'       => 'pengepul.dashboard',
            'koperasi_anak'  => 'koperasi-anak.dashboard',
            'driver'         => 'driver.dashboard',
        ];

        foreach ($dashboards as $role => $routeName) {
            if ($user->hasRole($role)) {
                return redirect()->route($routeName);
            }
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

    // Rute Verifikasi Email Wajib (Wajib bernama 'verification.verify' untuk pemicu notifikasi VerifyEmail)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'Email Anda telah berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    // ------------------------------------------------------------------------
    // Role: Super Admin Platform
    // ------------------------------------------------------------------------
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

        // Rute pembaruan role yang dipanggil oleh dashboard.blade.php
        Route::patch('/users/{id}/role', [SuperAdminDashboardController::class, 'updateUserRole'])->name('users.updateRole');

        // Ekspor Data User
        Route::get('/users/export/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');
        Route::get('/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');

        // CRUD Resource Manajemen Pengguna
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

        Route::match(['GET', 'POST'], '/users/{id}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');

        // CRUD Jenis Limbah (Waste Types)
        Route::resource('waste-types', WasteTypeController::class)->except(['create', 'edit', 'show']);

        // Audit Logs / Activity Trail Halaman Visual & Export
        Route::get('/audit-logs', [SuperAdminDashboardController::class, 'auditLogs'])->name('audit-logs.index');
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

        // Penugasan Driver & Input Stok Harian
        Route::post('/allocations/{id}/assign-driver', [SupplyController::class, 'assignDriver'])->name('allocations.assign-driver');

        // Pencatatan Stok Harian
        Route::post('/daily-stock', [SupplyController::class, 'storeDailyStock'])->name('daily-stock.store');
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
