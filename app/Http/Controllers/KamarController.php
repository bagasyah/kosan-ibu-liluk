<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kamar;
use App\Models\Indekos;
use App\Models\Fasilitas;

class KamarController extends Controller
{
    public function viewkamar(){
        $kamar = Kamar::all();
        return view('admin.indekos.kamar', compact('kamar'));
    }
    public function getCategory(Request $request)
    {
        $fasilitas_id = [];
        if ($search = $request->name) {
            $fasilitas_id = Fasilitas::where('nama_fasilitas', 'LIKE', "%{$search}%")->get();
        }
        return response()->json($fasilitas_id);
    }
    public function index($indekosId)
    {
        $indekos = Indekos::findOrFail($indekosId);
        $query = Kamar::with('users')
                      ->where('indekos_id', $indekosId);

        if ($search = request('search')) {
            $query->where('no_kamar', 'LIKE', "%{$search}%");
        }

        $kamars = $query->orderBy('no_kamar', 'asc')->get();
        $fasilitas = Fasilitas::all();

        // Perbarui status kamar berdasarkan pengguna
        foreach ($kamars as $kamar) {
            $isOccupied = false;
            foreach ($kamar->users as $user) {
                if ($user->status === 'active') {
                    $isOccupied = true;
                    break;
                }
            }

            if ($isOccupied) {
                $kamar->status = 'Terisi';
            } else {
                $kamar->status = 'Tidak Terisi';
            }
            $kamar->save();
        }

        return view('admin.indekos.kamar', compact('indekos', 'kamars', 'fasilitas'));
    }
    
    public function store(Request $request, $indekosId)
    {
        $request->validate([
            'no_kamar' => 'required|string|max:255|unique:kamars,no_kamar,NULL,id,indekos_id,' . $indekosId,
            'harga' => 'required|string',
            'fasilitas_id' => 'required|array',
        ]);

        $indekos = Indekos::findOrFail($indekosId);

        $harga = str_replace('.', '', $request->harga);

        $kamars = new Kamar([
            'no_kamar' => $request->no_kamar,
            'status' => 'Tidak Terisi',
            'harga' => $harga,
            'fasilitas_id' => $request->fasilitas_id ? implode(',', $request->fasilitas_id) : null,
        ]);

        $indekos->kamars()->save($kamars);
        return redirect()->route('kamar.index', ['indekosId' => $indekosId])
            ->with('success', 'Kamar berhasil ditambahkan');
    }

    public function edit($indekosId, $kamarId)
    {
        $indekos = Indekos::findOrFail($indekosId);
        $kamar = Kamar::findOrFail($kamarId);
        $fasilitas = Fasilitas::all();

        return view('admin.indekos.edit_kamar', compact('indekos', 'kamar', 'fasilitas'));
    }

    public function update(Request $request, $indekosId, $kamarId)
    {
        $request->validate([
            'no_kamar' => 'required|string|max:255|unique:kamars,no_kamar,' . $kamarId . ',id,indekos_id,' . $indekosId,
            'harga' => 'required|numeric',
            'fasilitas_id' => 'required|array',
            'status' => 'required|string|in:Terisi,Tidak Terisi',
        ]);

        $kamar = Kamar::findOrFail($kamarId);
        $kamar->update([
            'no_kamar' => $request->no_kamar,
            'harga' => $request->harga,
            'fasilitas_id' => $request->fasilitas_id ? implode(',', $request->fasilitas_id) : null,
            'status' => $request->status,
        ]);

        return redirect()->route('kamar.index', ['indekosId' => $indekosId])
                     ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy($indekosId, $kamarId)
    {
        $kamar = Kamar::findOrFail($kamarId);
        $kamar->delete();

        return redirect()->route('kamar.index', ['indekosId' => $indekosId])
            ->with('success', 'Kamar berhasil dihapus');
    }
}
