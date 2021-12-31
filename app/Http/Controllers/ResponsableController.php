<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{

    public function getResponsable(Request $request){
        $response = DB::table('responsable')
                        ->get();

        foreach($response as $item){
            $item->EMPRESA = DB::table('empresa')->where('ID_RESPONSABLE', $item->ID_RESPONSABLE)->get();
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function updateResponsable(Request $request){

        try{
            DB::table('responsable')
                ->where('ID_RESPONSABLE', $request->id_responsable)
                ->update([
                    'EMAIL' => $request->email
            ]);

            return response()->json(['status' => true, 'data'=>$request->email]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'error'=>'edit_responsable']);
        }

    }

    public function addResponsable(Request $request){

        $this->validate($request, [
            'NAME' => 'required',
            'EMAIL' => 'required'
        ]);

        try{
            $ID_RESPONSABLE = DB::table('responsable')->insertGetId([
                'NAME' => $request->NAME,
                'EMAIL' => $request->EMAIL
            ]);

            return response()->json(['status'=>true, 'data'=> $ID_RESPONSABLE]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error'=>'add_responsable']);
        }
    }
}
