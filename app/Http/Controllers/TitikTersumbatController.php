<?php

namespace App\Http\Controllers;

use App\TitikTersumbat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TitikTersumbatController extends Controller
{
    public function index(){
        return TitikTersumbat::select(
            'id',
            'id_admin',
            'nama_jalan',
            'foto', 
            DB::Raw('ST_AsGeoJSON(geometry) as geometry')
        )->get();
    }

    public function show($id){
        return TitikTersumbat::select(
            'id',
            'id_admin',
            'nama_jalan',
            'foto', 
            DB::Raw('ST_AsGeoJSON(geometry) as geometry')
        )->where('id',$id)->first();;
    }

    public function create(request $request){
        
        $validated = $request->validate([
            'nama_jalan' => 'required',
            'geometry' => 'required',
            'foto'=> 'required',
        ]);

        $validated['id_admin'] = auth()->user()->id;
        $validated['geometry'] = DB::Raw("ST_GeomFromGeoJSON('".$request->geometry."')");

        $data = TitikTersumbat::create($validated);

        $data->geometry = json_decode($request->geometry);

        return response()->json(["message" => "Data Added Successfully!", "data" => $data],201);
    }

    public function update(request $request, $id){
        
        $validated = $request->validate([
            'nama_jalan' => 'required',
            'geometry' => 'required',
            'foto'=> 'required',
        ]);

        $data = TitikTersumbat::find($id);
        $data->id_admin = auth()->user()->id;
        $data->geometry = DB::Raw("ST_GeomFromGeoJSON('".$request->geometry."')");
        $data->nama_jalan = $request->nama_jalan;
        $data->foto = $request->foto;
        $data->save();
        
        $data->geometry = json_decode($request->geometry);

        return response()->json(["message" => "Data Updated Successfully!", "data" => $data],200);
    }

    public function delete($id){
        $data = TitikTersumbat::find($id);
        $data->delete();

        return response()->json(null,204);
    }
}
