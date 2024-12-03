<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PengaduanBaruNotification;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index()
    {
        // Ambil data pengaduan untuk pengguna yang sedang login dengan relasi
        $pengaduan = Pengaduan::with(['user.kamar'])->where('user_id', Auth::id())->get();

        return view('user.pengaduan-user', compact('pengaduan'));
    }

    public function adminIndex()
    {
        // Ambil semua pengaduan dengan relasi user dan kamar
        $pengaduan = Pengaduan::with(['user.kamar'])->get();

        return view('admin.pengaduan-admin', compact('pengaduan'));
    }

    // Metode untuk menyimpan pengaduan
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'masalah' => 'required|string',
            'foto' => 'nullable|image|max:2048', // Validasi foto
        ]);

        // Menyimpan pengaduan
        $pengaduan = new Pengaduan();
        $pengaduan->user_id = auth()->id();
        $pengaduan->tanggal_pelaporan = now(); // Isi tanggal pelaporan dengan tanggal saat ini
        $pengaduan->masalah = $request->masalah;

        // Menyimpan foto jika ada
        if ($request->hasFile('foto')) {
            $fileName = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public', $fileName);
            $pengaduan->foto = $fileName;
        }

        $pengaduan->status = 'Pending'; // Status default
        $pengaduan->save(); // Menyimpan ke database

        // Kirim notifikasi ke semua admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PengaduanBaruNotification($pengaduan));
        }

        return redirect()->route('user.pengaduan')->with('success', 'Pengaduan berhasil dibuat.');
    }
}
