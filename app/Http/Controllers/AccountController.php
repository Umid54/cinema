<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Личный кабинет
     */
    public function index()
    {
        return view('account.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * 👤 Профиль — просто переход в ЛК
     */
    public function profile()
    {
        return redirect()->route('account.index');
    }
}
