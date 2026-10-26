<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriverApiController extends Controller
{
    // 1. Login Driver & Generate Token
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial login salah.'
            ], 401);
        }

        $user = Auth::user();
        if (!$user->hasRole('driver')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Pengguna bukan akun Driver Logistik.'
            ], 403);
        }

        $token = $user->createToken('driver_mobile_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $token,
            'driver' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'vehicle_number' => $user->vehicle_number,
                'phone_number' => $user->phone_number,
            ]
        ]);
    }

    // 2. Daftar Tugas Pengiriman Driver
    public function getShipments()
    {
        $driver = auth()->user();
        $shipments = Shipment::with([
            'allocation.order.wasteType',
            'allocation.koperasiAnak',
            'allocation.order.pengepul'
        ])
        ->where('driver_id', $driver->id)
        ->latest()
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $shipments
        ]);
    }

    // 3. Detail Penugasan Pengiriman
    public function getShipmentDetail($id)
    {
        $shipment = Shipment::with([
            'allocation.order.wasteType',
            'allocation.koperasiAnak',
            'allocation.order.pengepul'
        ])
        ->where('driver_id', auth()->id())
        ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $shipment
        ]);
    }

    // 4. Update Status Perjalanan (picking_up / delivering)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:picking_up,delivering',
        ]);

        $shipment = Shipment::where('driver_id', auth()->id())->findOrFail($id);

        if ($request->status === 'picking_up') {
            $shipment->update([
                'status' => 'picking_up',
                'pickup_time' => now(),
            ]);
            $shipment->allocation->update(['status' => 'picked_up']);
        } elseif ($request->status === 'delivering') {
            $shipment->update(['status' => 'delivering']);
            $shipment->allocation->order->update(['status' => 'in_transit']);
        }

        ActivityLog::record(
            'API_SHIPMENT_UPDATED',
            "Driver API update status pengiriman #{$shipment->shipment_number} menjadi {$request->status}"
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Status pengiriman berhasil diperbarui.',
            'data' => $shipment
        ]);
    }

    // 5. Upload Foto Bukti Pengiriman (POD)
    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'proof_of_delivery' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $shipment = Shipment::where('driver_id', auth()->id())->findOrFail($id);

        if ($request->hasFile('proof_of_delivery')) {
            if ($shipment->proof_of_delivery) {
                Storage::disk('public')->delete($shipment->proof_of_delivery);
            }

            $path = $request->file('proof_of_delivery')->store('proofs', 'public');

            $shipment->update([
                'proof_of_delivery' => $path,
                'delivered_time' => now(),
                'status' => 'completed',
            ]);

            $shipment->allocation->update(['status' => 'delivered']);

            // Cek apakah seluruh alokasi order telah selesai
            $order = $shipment->allocation->order;
            if ($order->allocations->every(fn($a) => $a->status === 'delivered')) {
                $order->update(['status' => 'completed']);
            }

            ActivityLog::record(
                'API_PROOF_UPLOADED',
                "Driver API mengunggah foto bukti serah terima untuk pengiriman #{$shipment->shipment_number}"
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pengiriman berhasil diunggah. Tugas selesai!',
                'proof_url' => asset('storage/' . $path),
                'data' => $shipment
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan.'], 400);
    }
}
