<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverApiController;

/*
|--------------------------------------------------------------------------
| API Routes - CoCoLink Mobile & External Integrations
|--------------------------------------------------------------------------
*/

// Public Endpoint: Login Driver Mobile App
Route::post('/driver/login', [DriverApiController::class, 'login']);

// Protected Endpoints (Memerlukan Token Sanctum & Role Driver)
Route::middleware(['auth:sanctum', 'role:driver'])->prefix('driver')->group(function () {

    // Auth & Profil Driver
    Route::get('/profile', function () {
        return response()->json([
            'status' => 'success',
            'data' => auth()->user()
        ]);
    });

    // Manajemen Tugas Pengiriman (Shipments)
    Route::get('/shipments', [DriverApiController::class, 'getShipments']);
    Route::get('/shipments/{id}', [DriverApiController::class, 'getShipmentDetail']);

    // Pembaruan Status Penjemputan & Pengantaran (picking_up / delivering)
    Route::patch('/shipments/{id}/status', [DriverApiController::class, 'updateStatus']);

    // Unggah Foto Bukti Pengiriman (Proof of Delivery / POD)
    Route::post('/shipments/{id}/proof', [DriverApiController::class, 'uploadProof']);

});
