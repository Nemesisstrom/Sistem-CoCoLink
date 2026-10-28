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
        $orders = $this->buildFilterQuery($request)->latest()->paginate(25);

        return view('koperasi_induk.laporan', compact('orders'));
    }

    // Export PDF Laporan Transaksi
    public function downloadPdf(Request $request)
    {
        $query = $this->buildFilterQuery($request)->latest();

        // Mengambil data terpilih untuk pembuatan PDF
        $orders = $query->get();
        $totalVolume = $orders->sum('quantity');
        $totalNominal = $orders->sum('total_amount');

        $pdf = Pdf::loadView('koperasi_induk.pdf_laporan', compact('orders', 'totalVolume', 'totalNominal'))
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true])
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Sinergi_Limbah_CoCoLink_' . now()->format('Ymd_His') . '.pdf');
    }

    // Private helper function untuk reusable query filter
    private function buildFilterQuery(Request $request)
    {
        $query = Order::with([
            'pengepul:id,name,email',
            'wasteType:id,name',
            'allocations.koperasiAnak:id,name',
            'allocations.shipment.driver:id,name'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }
}
