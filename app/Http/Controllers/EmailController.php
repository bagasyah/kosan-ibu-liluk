<?php

namespace App\Http\Controllers;

use App\Mail\ExampleMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\SendEmail;

class EmailController extends Controller
{
    public function sendEmail($id)
    {
        $user = User::findOrFail($id);

        $data = [
            'title' => 'Informasi Penting',
            'message' => 'Ini adalah pesan penting untuk Anda.'
        ];

        Mail::to($user->email)->send(new SendEmail($data));

        return redirect()->back()->with('success', 'Email berhasil dikirim!');
    }
}
