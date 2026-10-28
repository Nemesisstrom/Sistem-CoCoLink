<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // READ: Menampilkan daftar pengguna dengan pencarian & filter role
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('super_admin.users.index', compact('users', 'roles'));
    }

    // CREATE: Menyimpan pengguna baru & kirim email verifikasi otomatis
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password'     => 'required|string|min:8',
            'role'         => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        // Memicu pengiriman email verifikasi otomatis
        event(new Registered($user));

        // Catat Audit Log
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'CREATE_USER',
            'description' => "Menambahkan pengguna baru: {$user->name} ({$user->email}) dengan role {$request->role} dan mengirimkan email verifikasi.",
        ]);

        return redirect()->back()->with('success', "Pengguna {$user->name} berhasil ditambahkan. Email verifikasi telah dikirim otomatis.");
    }

    // UPDATE: Memperbarui data pengguna & catat ActivityLog
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'role'         => 'required|exists:roles,name',
            'password'     => 'nullable|string|min:8',
        ]);

        $user->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        // Catat Audit Log
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'UPDATE_USER',
            'description' => "Memperbarui data pengguna: {$user->name} ({$user->email}).",
        ]);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // DELETE: Menghapus pengguna & catat ActivityLog
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        // Catat Audit Log
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'DELETE_USER',
            'description' => "Menghapus pengguna: {$userName} ({$userEmail}).",
        ]);

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }

    // Ekspor Daftar Pengguna ke PDF
    public function exportPdf()
    {
        $users = User::with('roles')->latest()->get();
        $pdf = Pdf::loadView('super_admin.users.pdf', compact('users'));

        // Catat Log
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'EXPORT_USERS_PDF',
            'description' => 'Mengunduh laporan data pengguna format PDF.',
        ]);

        return $pdf->download('Daftar_Pengguna_CoCoLink_' . now()->format('Ymd_His') . '.pdf');
    }

    // Ekspor Daftar Pengguna ke Excel (CSV)
    public function exportExcel()
    {
        $fileName = 'Daftar_Pengguna_CoCoLink_' . now()->format('Ymd_His') . '.csv';
        $users = User::with('roles')->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Lengkap', 'Email', 'No Telepon', 'Role', 'Tanggal Terdaftar']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone_number ?? '-',
                    $user->roles->pluck('name')->first() ?? 'No Role',
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-',
                ]);
            }
            fclose($file);
        };

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'EXPORT_USERS_EXCEL',
            'description' => 'Mengunduh laporan data pengguna format Excel/CSV.',
        ]);

        return response()->stream($callback, 200, $headers);
    }
}
