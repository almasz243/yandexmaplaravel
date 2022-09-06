<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class register extends Controller
{
    public function save(Request $request){
        if(Auth::check()){
            return redirect(route('user.private'));
        }
        if (Auth::viaRemember())
        {
            return redirect(route('user.private'));
        }
        $validateFields = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if(User::where('email', $validateFields['email'])->exists()){
            return redirect(route('user.register'))->withErrors([
                'email' => 'Такой email уже существует!'
            ]);
        }
        $user = User::create($validateFields);
        if($user){
            Auth::login($user);
            return redirect()->to(route('user.private'));

        }

        return redirect(route('user.login'))->withErrors([
            'formError' => 'Произошла ошибка при сохранении пользователя'
        ]);
    }
}
