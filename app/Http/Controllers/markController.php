<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class markController extends Controller
{
    public function post( Request $request){
        $validateFields = $request->validate([
            'name' => 'required',
            'latitude' => 'numeric|required',
            'longitude' => 'numeric|required'
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
    public function edit(Request $request){
        if(Auth::user()->id == $request->input('userid')){
            $validateFields = $request->validate([
                'name' => 'required',
                'latitude' => 'numeric|required',
                'longitude' => 'numeric|required'
            ]);
            $id = $request->input('id');
            $name = $request->input('name');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            DB::table('posts')->where('id', $id)->update(['name' => $name,'latitude' => $latitude, 'longitude' => $longitude]);
            return redirect(route('user.private'))->with('message', 'Успешно!');
        }else{
            return back()->withErrors(['userid'=> 'Не удалось авторизироваться']);;
        }
    }
    public function delete(Request $request){
        if(Auth::user()->id == $request->input('userid')){
            $id = $request->input('id');
            DB::delete('delete from public.posts where id = '.$id);
            return back();
        }else{
            return back()->withErrors(['userid'=> 'Не удалось авторизироваться']);;
        }

    }
    public function get(Request $request){
        $results = DB::select('select * from public.posts where userid = :id',['id' => Auth::user()->id]);
        return view('private', ['results' => $results]);
    }
}
