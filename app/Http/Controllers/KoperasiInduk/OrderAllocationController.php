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
        $request->validate([
            'allocations' => 'required|array',
            'allocations.*.koperasi_anak_id' => 'required|exists:users,id',
            'allocations.*.allocated_quantity' => 'required|numeric|min:0.1',
        ]);

        $order = Order::findOrFail($orderId);

        DB::transaction(function () use ($order, $request) {
            foreach ($request->allocations as $allocationData) {
                if ($allocationData['allocated_quantity'] > 0) {
                    $allocation = OrderAllocation::create([
                        'order_id' => $order->id,
                        'koperasi_anak_id' => $allocationData['koperasi_anak_id'],
                        'allocated_quantity' => $allocationData['allocated_quantity'],
                        'status' => 'assigned',
                    ]);

                    // Kirim Email Otomatis ke Koperasi Anak penerima alokasi
                    Mail::to($allocation->koperasiAnak->email)->queue(new AllocationNotificationMail($allocation));
                }
            }

            $order->update(['status' => 'allocated']);
        });

        return redirect()->back()->with('success', 'Pesanan berhasil dialokasikan dan notifikasi email dikirim ke Koperasi Anak.');
    }
}
