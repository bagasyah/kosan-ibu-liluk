<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Indekos;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $kamars = Kamar::all();
        return view('createakun', compact('kamars'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
            'kamar_id' => 'required|exists:kamars,id',
            'indekos_id' => 'required|exists:indekos,id',
        ]);

        // Ambil nama indekos berdasarkan indekos_id
        $indekos = Indekos::find($request->indekos_id);

        User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'kamar_id' => $request->kamar_id,
            'indekos_id' => $request->indekos_id,
            'nama_indekos' => $indekos->nama, // Simpan nama indekos ke kolom nama_indekos
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function dashboard()
    {
        return view('user.dashboard'); // Pastikan view ini ada
    }

    public function akun()
    {
        return view('user.akun-user'); // Pastikan view ini ada
    }
    public function pembayaran()
    {
        return view('user.pembayaran-user'); // Pastikan view ini ada
    }
    public function pengaduan()
    {
        return view('user.pengaduan-user'); // Pastikan view ini ada
    }
    // Tambahkan method lain sesuai kebutuhan (edit, update, delete, dll.)
}
