<?php

namespace App\Http\Controllers;

use App\Models\Indekos;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    public function index($indekosId, Request $request)
    {
        $indekos = Indekos::findOrFail($indekosId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            if ($endDate->lt($startDate)) {
                return redirect()->back()->withErrors(['error' => 'Urutan tanggal yang dimasukkan salah. Tanggal akhir tidak boleh lebih awal dari tanggal mulai.']);
            }
        }

        $query = Pengeluaran::where('indekos_id', $indekosId);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $pengeluarans = $query->get();

        return view('admin.indekos.pengeluaran', compact('indekos', 'pengeluarans'));
    }

    public function store(Request $request, $indekosId)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah_uang' => 'required|numeric',
        ]);

        Pengeluaran::create([
            'indekos_id' => $indekosId,
            'nama' => $request->nama,
            'jenis' => 'Pengeluaran',
            'tanggal' => $request->tanggal,
            'jumlah_uang' => $request->jumlah_uang,
            'status' => 'Selesai',
        ]);

        return redirect()->route('pengeluaran.index', $indekosId)->with('success', 'Pengeluaran berhasil ditambahkan.');
    }
}
