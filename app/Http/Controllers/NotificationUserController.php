<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationUserController extends Controller
{
    public function index()
    {
        // Ambil notifikasi pengguna yang sedang login
        $notifications = Auth::user()->notifications;

        return view('user.notification-user', compact('notifications'));
    }

    public function markAsRead($id)
    {
        // Temukan notifikasi berdasarkan ID dan tandai sebagai dibaca
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->route('user.notifications')->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }
}