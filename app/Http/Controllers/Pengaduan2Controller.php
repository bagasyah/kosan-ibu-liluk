<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Pengaduan2Controller extends Controller
{
    public function index(Request $request)
    {
        $query = Pengaduan::with(['user', 'user.kamar'])
            ->where('status', 'Selesai');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pelaporan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pelaporan', '<=', $request->end_date);
        }

        $pengaduan = $query->get();

        return view('admin.riwayat-pengaduan-admin', compact('pengaduan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'foto_akhir' => 'required|image|max:2048',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);

        if ($request->hasFile('foto_akhir')) {
            $fileName = time() . '.' . $request->foto_akhir->extension();
            $request->foto_akhir->storeAs('public', $fileName);
            $pengaduan->foto_akhir = $fileName;
        }

        $pengaduan->status = 'Selesai';
        $pengaduan->tanggal_perbaikan = Carbon::now();

        $pengaduan->save();

        return redirect()->route('admin.pengaduan')->with('success', 'Pengaduan berhasil diperbarui.');
    }

    public function riwayat(Request $request)
    {
        // Validasi tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            if ($endDate->lt($startDate)) {
                return redirect()->back()->withErrors(['error' => 'Urutan tanggal yang dimasukkan salah. Tanggal akhir tidak boleh lebih awal dari tanggal mulai.']);
            }
        }

        $query = Pengaduan::with(['user', 'user.kamar'])
            ->where('status', 'Selesai');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pelaporan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pelaporan', '<=', $request->end_date);
        }

        $pengaduan = $query->get();

        return view('admin.riwayat-pengaduan-admin', compact('pengaduan'));
    }
}
