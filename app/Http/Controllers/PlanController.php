<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    public function getPlanes(Request $request){
        $response = DB::table('plan')->get();

        foreach($response as $item){
            $item->STATUS = $item->STATUS == 1 ? true : false;
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function updateStatusPlan(Request $request){

        try{

            $status = $request->status == true ? 1 : 0;
            DB::table('plan')
                ->where('ID_PLAN', $request->id_plan)
                ->update([
                    'STATUS' => $status
            ]);

            return response()->json(['status' => true, 'data'=>$request->status]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'error'=>'edit_plan']);
        }

    }

    public function addPlan(Request $request){

        $this->validate($request, [
            'NAME' => 'required',
            'MEMBERSHIP' => 'required',
            'MONTHLY' => 'required',
            'LIMIT_IMAGE' => 'required',
            'LIMIT_CHAR' => 'required'
        ]);

        try{
            $ID_PLAN = DB::table('plan')->insertGetId([
                'NAME' => $request->NAME,
                'MEMBERSHIP' => $request->MEMBERSHIP,
                'MONTHLY' => $request->MONTHLY,
                'LIMIT_IMAGE' => $request->LIMIT_IMAGE,
                'LIMIT_CHAR' => $request->LIMIT_CHAR,
                'STATUS' => true
            ]);

            return response()->json(['status'=>true, 'data'=> $ID_PLAN]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'error'=>'add_plan']);
        }
    }
}
