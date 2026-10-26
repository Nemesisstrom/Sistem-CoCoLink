<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderCreatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    // Buat Pesanan Baru dari Pengepul / Pembeli Industri
    public function store(Request $request)
    {
        $request->validate([
            'waste_type_id' => 'required|exists:waste_types,id',
            'quantity' => 'required|numeric|min:0.1',
            'price_per_unit' => 'required|numeric|min:0',
            'delivery_address' => 'required|string',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-' . sprintf("%03d", Order::count() + 1),
            'pengepul_id' => Auth::id(),
            'waste_type_id' => $request->waste_type_id,
            'quantity' => $request->quantity,
            'price_per_unit' => $request->price_per_unit,
            'total_amount' => $request->quantity * $request->price_per_unit,
            'delivery_address' => $request->delivery_address,
            'status' => 'pending',
        ]);

        // Record Audit Trail
        ActivityLog::record(
            'ORDER_CREATED',
            "Pembeli Industri '" . Auth::user()->name . "' membuat pesanan baru " . $order->order_number . " sebanyak " . $order->quantity . " " . $order->wasteType->unit
        );

        // Send Notification to Koperasi Induk
        $koperasiIndukUsers = User::role('koperasi_induk')->get();
        Notification::send($koperasiIndukUsers, new NewOrderCreatedNotification($order));

        return redirect()->back()->with('success', 'Pesanan berhasil dibuat dan menunggu alokasi kuota dari Koperasi Induk.');
    }

    // Konfirmasi Penerimaan Barang oleh Pembeli / Pengepul
    public function confirmReceipt(Request $request,$id)
    {
        $order = Order::where('pengepul_id', Auth::id())->findOrFail($id);

        $order->update([
            'status' => 'completed',
        ]);

        // Record Audit Trail
        ActivityLog::record(
            'ORDER_CONFIRMED',
            "Pembeli Industri '" . Auth::user()->name . "' telah mengonfirmasi penerimaan barang untuk order " . $order->order_number
        );

        return redirect()->back()->with('success', 'Penerimaan barang berhasil dikonfirmasi. Transaksi selesai!');
    }

    // Unduh Invoice PDF Resmi Transaksi
    public function downloadInvoice($id)
    {
        $order = Order::with(['pengepul', 'wasteType', 'allocations.koperasiAnak', 'allocations.shipment.driver'])
            ->where('pengepul_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('pengepul.invoice_pdf', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_' .$order->order_number . '.pdf');
    }
}
