<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Checkout;
use Illuminate\Http\Request;
use PDF;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('user_id', auth()->id())->get(); // Ambil pembayaran untuk pengguna yang sedang login
        
        return view('user.pembayaran-user', compact('payments'));
    }

    public function checkout(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Set status pembayaran menjadi "Pending"
        $payment->status = 'Pending';
        $payment->save();

        // Buat entri baru di tabel checkouts
        Checkout::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'price' => $payment->price,
        ]);

        // Hapus entri dari tabel payments jika diperlukan
        $payment->delete();

        return response()->json(['success' => 'Payment moved to checkout successfully.']);
    }

    public function updatePaymentStatus(Request $request)
    {
        $payment = Payment::find($request->payment_id);

        if (!$payment) {
            return response()->json(['error' => 'Pembayaran tidak ditemukan'], 404);
        }

        $payment->status = 'Selesai';
        $payment->save();

        return response()->json(['success' => 'Status pembayaran diperbarui']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string', // Validasi input status
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update([
            'status' => $request->input('status'), // Hanya memperbarui status
        ]);

        // Pastikan bahwa Payment memiliki kolom indekos_id
        $indekosId = $payment->indekos_id;

        if (!$indekosId) {
            return redirect()->back()->with('error', 'Indekos ID tidak ditemukan.');
        }

        return redirect()->route('pemasukan.index', ['indekosId' => $indekosId])->with('success', 'Status pembayaran berhasil diperbarui');
    }

    public function downloadPdf($id)
    {
        $payment = Payment::findOrFail($id);

        $pdf = PDF::loadView('pdf.pembayaran', compact('payment'));

        return $pdf->download('pembayaran_' . $payment->id . '.pdf');
    }
}
