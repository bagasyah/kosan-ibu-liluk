<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Fasilitas::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_fasilitas', 'like', '%' . $search . '%');
        }

        $fasilitas = $query->get();

        return view('admin.fasilitas_admin', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
        ]);

        Fasilitas::create([
            'nama_fasilitas' => $request->nama_fasilitas,
        ]);

        return redirect()->route('fasilitas.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
        ]);

        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->update([
            'nama_fasilitas' => $request->nama_fasilitas,
        ]);

        return redirect()->route('fasilitas.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->delete();

        return redirect()->route('fasilitas.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}
