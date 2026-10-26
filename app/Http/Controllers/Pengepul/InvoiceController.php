<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    // Tampilkan / Unduh PDF Invoice Digital
    public function downloadInvoice($orderId)
    {
        $order = Order::with(['pengepul', 'wasteType', 'allocations.koperasiAnak'])
            ->where('pengepul_id', Auth::id())
            ->findOrFail($orderId);

        $pdf = Pdf::loadView('pengepul.invoice_pdf', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_' . $order->order_number . '.pdf');
    }
}
