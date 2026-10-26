<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Dashboard Koperasi Induk
    public function koperasiIndukDashboard()
    {
        $orders = Order::with(['pengepul', 'wasteType', 'allocations.koperasiAnak'])->latest()->get();
        $koperasiAnakList = User::role('koperasi_anak')->get();

        return view('koperasi_induk.dashboard', compact('orders', 'koperasiAnakList'));
    }

    // Dashboard Pengepul
    public function pengepulDashboard()
    {
        $myOrders = Order::with(['wasteType', 'allocations.koperasiAnak'])
            ->where('pengepul_id', Auth::id())
            ->latest()
            ->get();

        $wasteTypes = WasteType::all();

        return view('pengepul.dashboard', compact('myOrders', 'wasteTypes'));
    }

    // Dashboard Koperasi Anak
    public function koperasiAnakDashboard()
    {
        $user = Auth::user();
        $allocations = $user->allocations()->with(['order.wasteType', 'order.pengepul', 'shipment.driver'])->latest()->get();

        return view('koperasi_anak.dashboard', compact('allocations'));
    }

    // Dashboard Driver
    public function driverDashboard()
    {
        $user = Auth::user();
        $shipments = $user->shipments()->with(['allocation.order.wasteType', 'allocation.koperasiAnak', 'allocation.order.pengepul'])->latest()->get();

        return view('driver.dashboard', compact('shipments'));
    }
}
