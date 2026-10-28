<?php

namespace App\Http\Controllers\KoperasiInduk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAllocation;
use App\Mail\AllocationNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderAllocationController extends Controller
{
    public function allocate(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Validasi: Cegah alokasi jika status order tidak valid
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat dialokasikan.');
        }

        $request->validate([
            'allocations' => 'required|array|min:1',
            'allocations.*.koperasi_anak_id' => 'required|exists:users,id',
            'allocations.*.allocated_quantity' => 'required|numeric|min:0.1',
        ]);

        // Validasi Total Kuantitas Alokasi vs Kuantitas Order
        $totalAllocatedQuantity = array_sum(array_column($request->allocations, 'allocated_quantity'));

        if ($totalAllocatedQuantity > $order->quantity) {
            return redirect()->back()->with('error', 'Total kuantitas alokasi (' . $totalAllocatedQuantity . ' kg) melebihi jumlah pesanan (' . $order->quantity . ' kg).');
        }

        DB::transaction(function () use ($order, $request) {
            foreach ($request->allocations as $allocationData) {
                if ($allocationData['allocated_quantity'] > 0) {
                    $allocation = OrderAllocation::create([
                        'order_id' => $order->id,
                        'koperasi_anak_id' => $allocationData['koperasi_anak_id'],
                        'allocated_quantity' => $allocationData['allocated_quantity'],
                        'status' => 'assigned',
                    ]);

                    // Memuat relasi koperasiAnak secara eksplisit untuk efisiensi
                    $allocation->load('koperasiAnak');

                    if ($allocation->koperasiAnak && $allocation->koperasiAnak->email) {
                        Mail::to($allocation->koperasiAnak->email)->queue(new AllocationNotificationMail($allocation));
                    }
                }
            }

            $order->update(['status' => 'allocated']);
        });

        return redirect()->back()->with('success', 'Pesanan berhasil dialokasikan dan notifikasi email telah masuk ke antrean (queue).');
    }
}
