<?php

namespace App\Http\Controllers\KoperasiAnak;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\OrderAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplyController extends Controller
{
    // Konfirmasi Penerimaan / Penolakan Alokasi dari Koperasi Induk
    public function respondAllocation(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $allocation = OrderAllocation::where('koperasi_anak_id', Auth::id())->findOrFail($id);
        $statusBaru = $request->status;

        $allocation->update([
            'status' => $statusBaru,
        ]);

        $statusText = $statusBaru === 'accepted' ? 'Menerima' : 'Menolak';

        // Catat ke Audit Trail Log
        ActivityLog::record(
            'ALLOCATION_' . strtoupper($statusBaru),
            "Koperasi Anak '" . Auth::user()->name . "' {$statusText} alokasi pesanan " . $allocation->order->order_number . " sebanyak " . $allocation->allocated_quantity . " " . $allocation->order->wasteType->unit
        );

        return redirect()->back()->with('success', "Berhasil {$statusText} alokasi pesanan.");
    }

    // Update Status Kesiapan Pasokan Barang di Gudang (Siap Diambil Driver)
    public function updateReadyStatus(Request $request, $id)
    {
        $allocation = OrderAllocation::where('koperasi_anak_id', Auth::id())->findOrFail($id);

        $allocation->update([
            'status' => 'ready',
        ]);

        // Catat ke Audit Trail Log
        ActivityLog::record(
            'STOCK_READY',
            "Koperasi Anak '" . Auth::user()->name . "' telah menyiapkan pasokan " . $allocation->order->wasteType->name . " sebanyak " . $allocation->allocated_quantity . " " . $allocation->order->wasteType->unit . " untuk order " . $allocation->order->order_number
        );

        return redirect()->back()->with('success', 'Status pasokan berhasil diperbarui menjadi SIAP DIJEMPUT.');
    }
}
