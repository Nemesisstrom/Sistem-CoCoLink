<?php

namespace App\Http\Controllers\KoperasiInduk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Halaman Pratinjau & Filter Laporan
    public function index(Request $request)
    {
        $query = Order::with(['pengepul', 'wasteType', 'allocations.koperasiAnak', 'allocations.shipment.driver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $orders = $query->latest()->get();

        return view('koperasi_induk.laporan', compact('orders'));
    }

    // Export PDF Laporan Transaksi
    public function downloadPdf(Request $request)
    {
        $query = Order::with(['pengepul', 'wasteType', 'allocations.koperasiAnak', 'allocations.shipment.driver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $orders = $query->latest()->get();
        $totalVolume = $orders->sum('quantity');
        $totalNominal = $orders->sum('total_amount');

        $pdf = Pdf::loadView('koperasi_induk.pdf_laporan', compact('orders', 'totalVolume', 'totalNominal'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Sinergi_Limbah_CoCoLink_' . date('Ymd_His') . '.pdf');
    }
}
