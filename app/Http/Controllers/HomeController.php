<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\kamar;
use App\Models\Indekos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function homepage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Arahkan pengguna berdasarkan peran
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'user') {
            return redirect()->route('user.dashboard');
        }
    }
    public function createakun()
    {
        $data = User::get();
        $kamars = kamar::all();
        $indekos = Indekos::all();

        // Ambil ID kamar yang sudah digunakan
        $usedKamarIds = User::pluck('kamar_id')->toArray();

        // Filter kamar yang tidak digunakan
        $availableKamars = $kamars->whereNotIn('id', $usedKamarIds);

        return view('createakun', compact('data', 'availableKamars', 'indekos'));
    }
    public function kelolaakun(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('kamar', function ($q) use ($search) {
                      $q->where('no_kamar', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('kamar.indekos', function ($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%');
                  });
        }

        $data = $query->get();

        return view('kelolaakun', compact('data'));
    }
    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'nama' => 'required|string',
            'no_telp' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:admin,user',
        ];

        if ($request->role === 'user') {
            $rules['indekos_id'] = 'required|exists:indekos,id';
            $rules['kamar_id'] = 'required|exists:kamars,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Gagal menambahkan akun. Silakan periksa kembali input Anda.');
        }

        try {
            $data = [
                'name' => $request->nama,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'active',
            ];

            if ($request->role === 'user') {
                $indekos = Indekos::find($request->indekos_id);
                $data['indekos_id'] = $request->indekos_id;
                $data['nama_indekos'] = $indekos->nama;
                $data['kamar_id'] = $request->kamar_id;
            }

            User::create($data);

            return redirect()->route('kelolaakun')->with('success', 'Akun berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Gagal menambahkan akun: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan akun. Silakan coba lagi.');
        }
    }
    public function editakun(Request $request, $id)
    {
        $data = User::find($id);
        $indekos = Indekos::all();

        // Ambil ID kamar yang sudah digunakan
        $usedKamarIds = User::pluck('kamar_id')->toArray();

        // Filter kamar yang tidak digunakan
        $availableKamars = Kamar::where('indekos_id', $data->indekos_id)
                                ->whereNotIn('id', $usedKamarIds)
                                ->orWhere('id', $data->kamar_id) // Tambahkan kamar yang sedang digunakan oleh user ini
                                ->get();

        return view('editakun', compact('data', 'availableKamars', 'indekos'));
    }
    public function update(Request $request, $id)
    {
        $rules = [
            'email' => 'required|email|unique:users,email,' . $id,
            'nama' => 'required|string',
            'no_telp' => 'required|string',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,non active',
        ];

        // Tambahkan validasi kamar_id hanya jika role adalah user
        if ($request->role === 'user') {
            $rules['kamar_id'] = 'required|exists:kamars,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Gagal memperbarui akun. Silakan periksa kembali input Anda.');
        }

        try {
            $user = User::findOrFail($id);
            $user->name = $request->nama;
            $user->email = $request->email;
            $user->no_telp = $request->no_telp;
            $user->role = $request->role;

            // Cek perubahan status
            if ($user->status !== $request->status) {
                $user->status = $request->status;

                // Jika status berubah menjadi non active, ubah status kamar menjadi "Tidak Terisi"
                if ($request->status === 'non active' && $user->kamar_id) {
                    $kamar = Kamar::find($user->kamar_id);
                    if ($kamar) {
                        $kamar->status = 'Tidak Terisi';
                        $kamar->save();
                    }
                }

                // Jika status berubah menjadi active, ubah status kamar menjadi "Terisi"
                if ($request->status === 'active' && $user->kamar_id) {
                    $kamar = Kamar::find($user->kamar_id);
                    if ($kamar) {
                        $kamar->status = 'Terisi';
                        $kamar->save();
                    }
                }
            }

            // Simpan kamar_id hanya jika role adalah user
            if ($request->role === 'user') {
                $user->kamar_id = $request->kamar_id;
            } else {
                $user->kamar_id = null; // Set kamar_id ke null jika role bukan user
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return redirect()->route('kelolaakun')->with('success', 'Akun berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Gagal memperbarui akun: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui akun. Silakan coba lagi.');
        }
    }
    public function delete(Request $request,$id)
    {
        $data = User::find($id);
        if($data){
            $data->delete();
        }
        return redirect()->route('kelolaakun');
    }
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Cek status pengguna
            if (Auth::user()->status === 'non active') {
                Auth::logout();
                return back()->withErrors([
                    'name' => 'Akun Anda sudah tidak aktif lagi. Silakan hubungi administrator.',
                ])->onlyInput('name');
            }

            // Arahkan pengguna berdasarkan peran
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role === 'user') {
                return redirect()->route('user.dashboard');
            }
        }

        return back()->withErrors([
            'name' => 'username atau password yang Anda masukkan salah.',
            'password' => 'password yang Anda masukkan salah.',
        ])->onlyInput('name');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function getKamars($indekosId)
    {
        $kamars = Kamar::where('indekos_id', $indekosId)->get();
        return response()->json($kamars);
    }

    public function getAvailableKamars($indekosId)
    {
        $kamars = Kamar::where('indekos_id', $indekosId)
                       ->where('status', 'Tidak Terisi') // Hanya ambil kamar yang statusnya "Tidak Terisi"
                       ->get();
        return response()->json($kamars);
    }
}
