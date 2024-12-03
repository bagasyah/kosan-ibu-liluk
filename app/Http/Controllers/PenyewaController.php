<?php

namespace App\Http\Controllers;

use App\Models\Indekos;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index($indekosId)
    {
        $indekos = Indekos::findOrFail($indekosId);
        
        $query = User::whereHas('kamar', function ($query) use ($indekosId) {
            $query->where('indekos_id', $indekosId);
        })->where('role', 'user') // Filter berdasarkan role
          ->with(['kamar', 'payments']);

        if ($search = request('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $penyewa = $query->get()->sortBy(function($user) {
            return $user->kamar->no_kamar ?? PHP_INT_MAX; // Mengurutkan berdasarkan nomor kamar
        });

        return view('admin.indekos.datapenyewa', compact('indekos', 'penyewa'));
    }

    public function sendEmail($indekosId, $id)
    {
        try {
            // Cari pengguna berdasarkan id dan pastikan mereka terkait dengan indekos yang benar
            $penyewa = User::whereHas('kamar', function ($query) use ($indekosId) {
                $query->where('indekos_id', $indekosId);
            })->findOrFail($id);

            // Logika pengiriman email
            // Contoh: Mail::to($penyewa->email)->send(new YourMailableClass());

            return redirect()->route('datapenyewa.index', $indekosId)->with('success', 'Email berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->route('datapenyewa.index', $indekosId)->with('error', 'Email gagal dikirim.');
        }
    }
}
