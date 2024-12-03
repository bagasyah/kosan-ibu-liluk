<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = Fasilitas::all();

        return view('admin.indekos.fasilitas', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
        ]);

        Fasilitas::create([
            'nama_fasilitas' => $request->nama_fasilitas,
        ]);

        return redirect()->route('fasilitasuser.index')->with('success', 'Fasilitas berhasil ditambahkan.');
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

        return redirect()->route('fasilitasuser.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->delete();

        return redirect()->route('fasilitasuser.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}