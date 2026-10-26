<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Http\Request;
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

        // Query Audit Trail Log dengan Filter
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
            'activityLogs'
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

        ActivityLog::record(
            'ROLE_UPDATED',
            "Memperbarui role pengguna '{$user->name}' dari '{$oldRole}' menjadi '{$request->role}'."
        );

        return redirect()->back()->with('success', "Role untuk user {$user->name} berhasil diperbarui.");
    }

    // Ekspor Audit Log ke CSV / Excel
    public function exportAuditLogs(Request $request)
    {
        $fileName = 'Audit_Trail_CoCoLink_' . date('Ymd_His') . '.csv';

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

        $logs = $logQuery->latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Log', 'Waktu Kejadian', 'Aksi Log', 'Keterangan / Deskripsi', 'Nama Pengguna', 'IP Address'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM untuk dukungan UTF-8 pada Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('d-m-Y H:i:s'),
                    $log->action,
                    $log->description,
                    $log->user->name ?? 'System Automated',
                    $log->ip_address ?? '127.0.0.1',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
