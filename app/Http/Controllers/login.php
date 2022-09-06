<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class login extends Controller
{
    public function login(Request $request){
        if(Auth::check()){
            return redirect(route('user.private'));
        }
        if (Auth::viaRemember())
        {
            return redirect(route('user.private'));
        }
        $formFields = $request->only(['email', 'password']);
        $remember = $request->input('remember');
        if(Auth::attempt($formFields,$remember)){
            return redirect()->intended(route('user.private'));
        }
        return redirect(route('user.login'))->withErrors([
            'email'=> 'Не удалось авторизироваться'
        ]);
    }
}
