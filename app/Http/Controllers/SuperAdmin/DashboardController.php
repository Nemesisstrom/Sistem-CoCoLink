<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalCompletedOrders = Order::where('status', 'completed')->count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');

        $users = User::with('roles')->latest()->paginate(10);
        $roles = Role::all();
        $wasteTypes = WasteType::all();

        // Data Grafik: Aktivitas 6 Bulan Terakhir
        $monthlyActivity = ActivityLog::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month', 'month_name')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = $monthlyActivity->pluck('month_name');
        $chartData = $monthlyActivity->pluck('total');

        // Query Audit Trail Log dengan Filter Sidebar
        $logQuery = ActivityLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $logQuery->where(function($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $logQuery->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $activityLogs = $logQuery->latest()->take(20)->get();

        return view('super_admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalCompletedOrders',
            'totalRevenue',
            'users',
            'roles',
            'wasteTypes',
            'activityLogs',
            'chartLabels',
            'chartData'
        ));
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->getRoleNames()->first() ?? 'No Role';
        $user->syncRoles([$request->role]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'ROLE_UPDATED',
            'description' => "Memperbarui role pengguna '{$user->name}' dari '{$oldRole}' menjadi '{$request->role}'."
        ]);

        return redirect()->back()->with('success', "Role untuk user {$user->name} berhasil diperbarui.");
    }

    public function auditLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(15);

        return view('super_admin.audit_logs.index', compact('logs'));
    }

    public function exportAuditLogs(Request $request)
    {
        $fileName = 'Audit_Logs_CoCoLink_' . now()->format('Ymd_His') . '.csv';
        $logs = ActivityLog::with('user')->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Log', 'Waktu', 'Pengguna', 'Aksi', 'Deskripsi']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                    $log->user?->name ?? 'Sistem',
                    $log->action,
                    $log->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
