<?php

namespace App\Http\Controllers;
use App\Models\Indekos;
use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;

class PemasukanController extends Controller
{
    public function index($indekosId, Request $request)
    {
        // Validasi tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            if ($endDate->lt($startDate)) {
                return redirect()->back()->withErrors(['error' => 'Urutan tanggal yang dimasukkan salah. Tanggal akhir tidak boleh lebih awal dari tanggal mulai.']);
            }
        }

        $indekos = Indekos::findOrFail($indekosId);

        $query = Payment::with(['user', 'user.kamar'])
            ->whereHas('user.kamar', function ($q) use ($indekosId) {
                $q->where('indekos_id', $indekosId);
            });

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_bayar', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_bayar', '<=', $request->end_date);
        }

        $payments = $query->get();

        return view('admin.indekos.pemasukan', compact('indekos', 'payments'));
    }
}