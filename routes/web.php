<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Middleware\Authenticate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::name('user.')->group(function (){
    Route::name('private')->group(function (){
        Route::view('/private','private')->name('private')->middleware('auth');
        Route::get('/private', function () {
            $results = DB::select('select * from public.posts where userid = :id',['id' => Auth::user()->id]);
            return view('private', ['results' => $results]);
        })->middleware('auth');
    });
    Route::get('/login', function (){
        if(Auth::check()){
            return redirect(route('user.private'));
        }
        return view ('login');
    })->name('login');
    Route::post('/login', [\App\Http\Controllers\login::class, 'login']);
    Route::get('/logout', function (){
        Auth::logout();
        return redirect('login');
    })->name('logout');
    Route::get('/register', function (){
        if(Auth::check()){
            return redirect(route('user.private'));
        }
       return view('register');
    })->name('register');
    Route::post('/register', [\App\Http\Controllers\register::class, 'save']);
    Route::post('/posts',[\App\Http\Controllers\posts::class,'post'])->name('post');
    Route::get('/edit', [\App\Http\Controllers\edit::class, 'edit'])->name('edit');
    Route::get('/delete', function( Request $request){
        $id = $request->input('id');
        $result = DB::delete('delete from public.posts where id = '.$id);
        return back();
    })->name('delete');
});
