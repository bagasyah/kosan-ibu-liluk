<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\Payment;
use App\Models\Indekos;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalPengeluaran = Pengeluaran::sum('jumlah_uang');
        $totalPemasukan = Payment::where('status', 'Selesai')->sum('price');
        $totalPenghasilan = $totalPemasukan - $totalPengeluaran;

        $indekosData = Indekos::selectRaw('DATE(created_at) as date, COUNT(*) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $labels = $indekosData->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        $data = $indekosData->pluck('total');

        return view('admin.dashboard', compact('totalPengeluaran', 'totalPemasukan', 'totalPenghasilan', 'labels', 'data'));
    }
}
