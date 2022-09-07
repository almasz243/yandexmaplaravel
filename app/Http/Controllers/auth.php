<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class auth extends Controller
{
    public function login(Request $request){
        if(\Illuminate\Support\Facades\Auth::check()  OR Auth::viaRemember()){
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
    public function save(Request $request){
        if(\Illuminate\Support\Facades\Auth::check() OR Auth::viaRemember()){
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
