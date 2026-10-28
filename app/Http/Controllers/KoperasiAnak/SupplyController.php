<?php

namespace App\Http\Controllers\KoperasiAnak;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\OrderAllocation;
use App\Models\Shipment;
use App\Models\DailyStock;
use App\Models\WasteType;
use App\Mail\DriverAssignedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

        ActivityLog::record(
            'STOCK_READY',
            "Koperasi Anak '" . Auth::user()->name . "' telah menyiapkan pasokan " . $allocation->order->wasteType->name . " sebanyak " . $allocation->allocated_quantity . " " . $allocation->order->wasteType->unit . " untuk order " . $allocation->order->order_number
        );

        return redirect()->back()->with('success', 'Status pasokan berhasil diperbarui menjadi SIAP DIJEMPUT.');
    }

    // Penugasan Driver untuk Alokasi yang Siap Dikirim
    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id'     => 'required|exists:users,id',
            'delivery_date' => 'required|date|after_or_equal:today',
        ]);

        $allocation = OrderAllocation::where('koperasi_anak_id', Auth::id())->findOrFail($id);

        DB::transaction(function () use ($allocation, $request) {
            $shipment = Shipment::create([
                'order_allocation_id' => $allocation->id,
                'driver_id'           => $request->driver_id,
                'status'              => 'assigned',
                'delivery_date'       => $request->delivery_date,
            ]);

            $allocation->update(['status' => 'ready']);

            // Load relasi untuk keperluan email
            $shipment->load(['driver', 'allocation.koperasiAnak', 'allocation.order.pengepul', 'allocation.order.wasteType']);

            if ($shipment->driver && $shipment->driver->email) {
                Mail::to($shipment->driver->email)->queue(new DriverAssignedMail($shipment));
            }

            ActivityLog::record(
                'ASSIGN_DRIVER',
                "Koperasi Anak '" . Auth::user()->name . "' menugaskan driver " . $shipment->driver->name . " untuk pengiriman alokasi order " . $allocation->order->order_number
            );
        });

        return redirect()->back()->with('success', 'Driver berhasil ditugaskan dan email notifikasi telah dikirim.');
    }

    // Input Pencatatan Pasokan Stok Harian
    public function storeDailyStock(Request $request)
    {
        $request->validate([
            'waste_type_id' => 'required|exists:waste_types,id',
            'stock_in'      => 'required|numeric|min:0.1',
            'entry_date'    => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        $userId = Auth::id();

        $lastStock = DailyStock::where('koperasi_anak_id', $userId)
            ->where('waste_type_id', $request->waste_type_id)
            ->latest('entry_date')
            ->first();

        $previousBalance = $lastStock ? $lastStock->current_stock : 0;
        $newBalance = $previousBalance + $request->stock_in;

        DailyStock::create([
            'koperasi_anak_id' => $userId,
            'waste_type_id'    => $request->waste_type_id,
            'stock_in'         => $request->stock_in,
            'stock_out'        => 0,
            'current_stock'    => $newBalance,
            'entry_date'       => $request->entry_date,
            'notes'            => $request->notes,
        ]);

        $wasteType = WasteType::find($request->waste_type_id);

        ActivityLog::record(
            'DAILY_STOCK_ADD',
            "Koperasi Anak '" . Auth::user()->name . "' mencatat pasokan masuk " . $wasteType->name . " sebanyak " . $request->stock_in . " " . $wasteType->unit
        );

        return redirect()->back()->with('success', 'Catatan pasokan stok harian berhasil disimpan.');
    }
}
