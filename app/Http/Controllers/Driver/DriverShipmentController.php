<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriverShipmentController extends Controller
{
    // Update Status Penjemputan / Perjalanan
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:picking_up,delivering',
        ]);

        $shipment = Shipment::where('driver_id', Auth::id())->findOrFail($id);

        if ($request->status === 'picking_up') {
            $shipment->update([
                'status' => 'picking_up',
                'pickup_time' => now(),
            ]);
            $shipment->allocation->update(['status' => 'picked_up']);

            ActivityLog::record(
                'SHIPMENT_PICKING_UP',
                "Driver '" . Auth::user()->name . "' mulai menjemput limbah untuk tugas #" . $shipment->shipment_number
            );
        } elseif ($request->status === 'delivering') {
            $shipment->update([
                'status' => 'delivering',
            ]);
            $shipment->allocation->order->update(['status' => 'in_transit']);

            ActivityLog::record(
                'SHIPMENT_DELIVERING',
                "Driver '" . Auth::user()->name . "' sedang mengantar pesanan #" . $shipment->allocation->order->order_number . " menuju lokasi tujuan"
            );
        }

        return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    // Unggah Bukti Pengiriman (Proof of Delivery) & Selesaikan Tugas
    public function completeShipment(Request $request, $id)
    {
        $request->validate([
            'proof_of_delivery' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $shipment = Shipment::where('driver_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('proof_of_delivery')) {
            // Hapus file lama jika ada
            if ($shipment->proof_of_delivery) {
                Storage::disk('public')->delete($shipment->proof_of_delivery);
            }

            $path = $request->file('proof_of_delivery')->store('proofs', 'public');

            $shipment->update([
                'proof_of_delivery' => $path,
                'delivered_time' => now(),
                'status' => 'completed',
            ]);

            // Update status alokasi
            $shipment->allocation->update(['status' => 'delivered']);

            // Cek apakah seluruh alokasi pada order ini telah selesai
            $order = $shipment->allocation->order;
            $allDelivered = $order->allocations->every(fn($alloc) => $alloc->status === 'delivered');
            if ($allDelivered) {
                $order->update(['status' => 'completed']);
            }

            ActivityLog::record(
                'PROOF_UPLOADED',
                "Driver '" . Auth::user()->name . "' mengunggah foto bukti serah terima untuk pengiriman #" . $shipment->shipment_number
            );
        }

        return redirect()->back()->with('success', 'Bukti pengiriman berhasil diunggah. Tugas logistik selesai!');
    }
}
