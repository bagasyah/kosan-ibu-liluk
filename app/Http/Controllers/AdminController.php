<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Indekos;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        return view('admin.dashboard', compact('userCount'));
    }

    public function indekos()
    {
        return redirect()->route('indekos.index');
    }
}