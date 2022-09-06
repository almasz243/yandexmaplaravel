<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class edit extends Controller
{
    public function edit(Request $request){
        if($request['name'] == '' OR $request['latitude'] == '' OR $request['longitude'] == ''){
            return redirect(route('user.private'))->withErrors([
                'name' => 'Поле не должно быть пустым!',
            ]);
        }
        $validateFields = $request->validate([
            'latitude' => 'numeric',
            'longitude' => 'numeric'
        ]);
        $id = $request->input('id');
        $name = $request->input('name');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        DB::table('posts')->where('id', $id)->update(['name' => $name,'latitude' => $latitude, 'longitude' => $longitude]);
        return back();
    }
}
