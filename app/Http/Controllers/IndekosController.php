<?php

namespace App\Http\Controllers;

use App\Models\Indekos;
use App\Models\Kamar;
use App\Models\User;
use Illuminate\Http\Request;

class IndekosController extends Controller
{
    public function index()
    {
        $indekos = Indekos::with(['kamars.users'])->get();

        // Hitung jumlah kamar dan penghuni untuk setiap indekos
        foreach ($indekos as $kos) {
            $kos->jumlah_kamar = $kos->kamars->count();
            $kos->jumlah_penghuni = $kos->kamars->sum(function($kamar) {
                return $kamar->users->count();
            });
        }

        return view('admin.indekos', compact('indekos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
        ]);

        Indekos::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('indekos.index')->with('success', 'Indekos berhasil ditambahkan');
    }

    public function edit($id)
    {
        $indekos = Indekos::findOrFail($id);
        return view('admin.edit_indekos', compact('indekos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
        ]);

        $indekos = Indekos::findOrFail($id);
        $indekos->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('indekos.index')->with('success', 'Indekos berhasil diperbarui');
    }

    public function destroy(Indekos $indeko)
    {
        $indeko->delete();

        return redirect()->route('indekos.index')->with('success', 'Indekos berhasil dihapus');
    }

    public function detail($id)
    {
        $indekos = Indekos::findOrFail($id);
        return view('admin.indekos_detail', compact('indekos'));
    }

    public function show($id)
    {
        $indekos = Indekos::with(['kamars' => function($query) {
            $query->orderBy('no_kamar', 'asc');
        }])->findOrFail($id);

        // Hitung jumlah kamar dan penghuni
        $jumlahKamar = $indekos->kamars->count();
        $jumlahPenghuni = $indekos->kamars->sum(function($kamar) {
            return $kamar->users->count();
        });

        return view('admin.indekos_detail', compact('indekos', 'jumlahKamar', 'jumlahPenghuni'));
    }
}
