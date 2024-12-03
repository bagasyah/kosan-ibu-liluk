<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indekos;
use App\Models\Payment;
use App\Models\Pengeluaran;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class RiwayatKeuanganController extends Controller
{
    public function index(Request $request, $indekosId)
    {
        $indekos = Indekos::findOrFail($indekosId);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validasi tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            if ($endDate->lt($startDate)) {
                return redirect()->back()->withErrors(['error' => 'Urutan tanggal yang dimasukkan salah. Tanggal akhir tidak boleh lebih awal dari tanggal mulai.']);
            }
        }

        $payments = Payment::whereHas('user.kamar', function($query) use ($indekosId) {
            $query->where('indekos_id', $indekosId);
        })->where('status', 'Selesai')
          ->when($search, function($query, $search) {
              return $query->whereHas('user', function($query) use ($search) {
                  $query->where('name', 'like', "%{$search}%");
              });
          })
          ->when($startDate, function($query, $startDate) {
              return $query->where('tanggal_bayar', '>=', $startDate);
          })
          ->when($endDate, function($query, $endDate) {
              return $query->where('tanggal_bayar', '<=', $endDate);
          })
          ->orderBy('tanggal_bayar', 'desc')
          ->with(['user.kamar' => function($query) use ($indekosId) {
              $query->where('indekos_id', $indekosId);
          }])->get();

        $pengeluarans = Pengeluaran::where('indekos_id', $indekosId)
                                   ->where('status', 'Selesai')
                                   ->when($search, function($query, $search) {
                                       return $query->where('nama', 'like', "%{$search}%");
                                   })
                                   ->when($startDate, function($query, $startDate) {
                                       return $query->where('tanggal', '>=', $startDate);
                                   })
                                   ->when($endDate, function($query, $endDate) {
                                       return $query->where('tanggal', '<=', $endDate);
                                   })
                                   ->orderBy('tanggal', 'desc')
                                   ->get();

        $riwayats = collect();

        if ($payments->isNotEmpty()) {
            $riwayats = $riwayats->merge($payments->map(function($payment) {
                return [
                    'nama' => $payment->user->name,
                    'no_kamar' => $payment->user->kamar->no_kamar ?? '-',
                    'jenis' => 'Pemasukan',
                    'tanggal_bayar' => $payment->tanggal_bayar,
                    'jumlah_uang' => $payment->price,
                    'status' => $payment->status,
                ];
            }));
        }

        if ($pengeluarans->isNotEmpty()) {
            $riwayats = $riwayats->merge($pengeluarans->map(function($pengeluaran) {
                return [
                    'nama' => $pengeluaran->nama,
                    'no_kamar' => '-',
                    'jenis' => 'Pengeluaran',
                    'tanggal_bayar' => $pengeluaran->tanggal,
                    'jumlah_uang' => $pengeluaran->jumlah_uang,
                    'status' => $pengeluaran->status,
                ];
            }));
        }

        $totalPemasukan = $riwayats->where('jenis', 'Pemasukan')->sum('jumlah_uang');
        $totalPengeluaran = $riwayats->where('jenis', 'Pengeluaran')->sum('jumlah_uang');
        $totalJumlahUang = $totalPemasukan - $totalPengeluaran;

        return view('admin.indekos.riwayat_keuangan', compact('riwayats', 'indekos', 'totalJumlahUang', 'totalPemasukan', 'totalPengeluaran'));
    }

    public function export($indekosId)
    {
        // Ambil data payments yang terkait dengan indekosId dan status "Selesai"
        $payments = Payment::whereHas('user.kamar', function($query) use ($indekosId) {
            $query->where('indekos_id', $indekosId);
        })->where('status', 'Selesai')
          ->with(['user.kamar' => function($query) use ($indekosId) {
              $query->where('indekos_id', $indekosId);
          }])->get();

        // Ambil data pengeluaran yang terkait dengan indekosId dan status "Selesai"
        $pengeluarans = Pengeluaran::where('indekos_id', $indekosId)
                                   ->where('status', 'Selesai')
                                   ->get();

        // Gabungkan data payments dan pengeluaran
        $riwayats = collect();

        if ($payments->isNotEmpty()) {
            $riwayats = $riwayats->merge($payments->map(function($payment) {
                return [
                    'nama' => $payment->user->name,
                    'no_kamar' => $payment->user->kamar->no_kamar ?? '-',
                    'jenis' => 'Penyewa',
                    'tanggal_bayar' => $payment->tanggal_bayar,
                    'jumlah_uang' => $payment->price,
                    'status' => $payment->status,
                ];
            }));
        }

        if ($pengeluarans->isNotEmpty()) {
            $riwayats = $riwayats->merge($pengeluarans->map(function($pengeluaran) {
                return [
                    'nama' => $pengeluaran->nama,
                    'no_kamar' => '-',
                    'jenis' => 'Pengeluaran',
                    'tanggal_bayar' => $pengeluaran->tanggal,
                    'jumlah_uang' => $pengeluaran->jumlah_uang,
                    'status' => $pengeluaran->status,
                ];
            }));
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'NAMA');
        $sheet->setCellValue('B1', 'No Kamar');
        $sheet->setCellValue('C1', 'Jenis');
        $sheet->setCellValue('D1', 'Tanggal Bayar');
        $sheet->setCellValue('E1', 'Jumlah Uang');
        $sheet->setCellValue('F1', 'Status');

        // Isi data
        $row = 2;
        foreach ($riwayats as $riwayat) {
            $sheet->setCellValue('A' . $row, $riwayat['nama']);
            $sheet->setCellValue('B' . $row, $riwayat['no_kamar']);
            $sheet->setCellValue('C' . $row, $riwayat['jenis']);
            $sheet->setCellValue('D' . $row, $riwayat['tanggal_bayar']);
            $sheet->setCellValue('E' . $row, $riwayat['jumlah_uang']);
            $sheet->setCellValue('F' . $row, $riwayat['status']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'riwayat_keuangan.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
