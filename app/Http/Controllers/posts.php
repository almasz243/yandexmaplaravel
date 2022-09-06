<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class posts extends Controller
{
    public function post( Request $request){
        if($request['name'] == '' OR $request['latitude'] == '' OR $request['longitude'] == ''){
            return redirect(route('user.private'))->withErrors([
                'name' => 'Поле не должно быть пустым!',
            ]);
        }
        $validateFields = $request->validate([
            'latitude' => 'numeric',
            'longitude' => 'numeric'
        ]);
        $name = $request->input('name');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $user = Auth::user()->id;

        $data=array('name'=>$name,"latitude"=>$latitude,"longitude"=>$longitude,"userid" =>$user,"created_at" =>  date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),);
        DB::table('posts')->insert($data);
        return redirect(route('user.private'))->with('message', 'Успешно!');
    }
}
