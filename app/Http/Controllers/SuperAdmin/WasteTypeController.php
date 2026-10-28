<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WasteType;
use Illuminate\Http\Request;

class WasteTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = WasteType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        $wasteTypes = $query->latest()->paginate(10);

        return view('super_admin.waste_types.index', compact('wasteTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|unique:waste_types,code|max:50',
            'name'        => 'required|string|max:255',
            'price_per_kg'=> 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $wasteType = WasteType::create($request->all());

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'CREATE_WASTE_TYPE',
            'description' => "Menambahkan jenis limbah baru: {$wasteType->name} ({$wasteType->code}).",
        ]);

        return redirect()->back()->with('success', 'Jenis limbah berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $wasteType = WasteType::findOrFail($id);

        $request->validate([
            'code'        => 'required|string|max:50|unique:waste_types,code,' . $wasteType->id,
            'name'        => 'required|string|max:255',
            'price_per_kg'=> 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $wasteType->update($request->all());

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'UPDATE_WASTE_TYPE',
            'description' => "Memperbarui jenis limbah: {$wasteType->name} ({$wasteType->code}).",
        ]);

        return redirect()->back()->with('success', 'Data jenis limbah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $wasteType = WasteType::findOrFail($id);
        $name = $wasteType->name;

        $wasteType->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'DELETE_WASTE_TYPE',
            'description' => "Menghapus jenis limbah: {$name}.",
        ]);

        return redirect()->back()->with('success', 'Jenis limbah berhasil dihapus.');
    }
}
