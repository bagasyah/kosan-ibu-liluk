<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class DashboardUserController extends Controller
{
    public function index()
    {
        // Ambil 5 data pembayaran terbaru untuk pengguna yang sedang login
        $payments = Payment::where('user_id', Auth::id())
                           ->latest()
                           ->take(5)
                           ->get();

        $batasPembayaranTerlama = Payment::where('user_id', Auth::id())
                                         ->where('status', 'Belum Dibayar')
                                         ->orderBy('batas_pembayaran', 'asc')
                                         ->first();

        return view('user.dashboard', compact('payments', 'batasPembayaranTerlama'));
    }

    public function editPhoto()
    {
        return view('user.edit-photo');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('foto')) {
            $fileName = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public', $fileName);
            $user->foto = $fileName;
            $user->save();
        }

        return redirect()->route('user.dashboard')->with('success', 'Foto berhasil diperbarui.');
    }
}
