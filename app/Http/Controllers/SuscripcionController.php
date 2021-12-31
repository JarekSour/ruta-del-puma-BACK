<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function initSuscripcion($ID_EMPRESA, $ID_PLAN, $METHOD)
    {
        $date = date('Y-m-d');

        DB::table('suscripcion')->insertGetId([
            'ID_EMPRESA'=> $ID_EMPRESA,
            'ID_PLAN'=> $ID_PLAN,
            'METHOD'=> $METHOD,
            'DATE_START'=> $date,
            'DATE_END'=> date('Y-m-d', strtotime($date." +1 month")),
            'STATUS'=>'pendiente'
        ]);
    }

    public function addPlan(Request $request)
    {
        $this->validate($request, [
            'NAME' => 'required',
            'MEMBERSHIP' => 'required',
            'MONTHLY' => 'required',
            'LIMIT_IMAGE' => 'required',
            'LIMIT_CHAR' => 'required'
        ]);

        try {
            DB::table('plan')->insertGetId([
                'NAME' => $request->NAME,
                'MEMBERSHIP' => $request->MEMBERSHIP,
                'MONTHLY' => $request->MONTHLY,
                'LIMIT_IMAGE' => $request->LIMIT_IMAGE,
                'LIMIT_CHAR' => $request->LIMIT_CHAR,
                'STATUS' => true
            ]);

            return response()->json(['status'=>true]);
        } catch (Exception $e) {
            return response()->json(['status'=>false, 'error'=>'add_plan']);
        }
    }

    public function updateSuscripcion(Request $request){

        DB::table('suscripcion')
            ->where('ID_SUSCRIPCION', $request->ID_SUSCRIPCION)
            ->update([
                'STATUS' => 'pagado',
            ]);

        return response()->json(['status'=>true]);
    }

    public function addSuscripcion(Request $request)
    {
        $date = date('Y-m-d');

        $ID_SUSCRIPCION = DB::table('suscripcion')->insertGetId([
            'ID_EMPRESA'=>$request->ID_EMPRESA,
            'ID_PLAN'=>$request->ID_PLAN,
            'METHOD'=>$request->METHOD,
            'DATE_START'=> $date,
            'DATE_END'=> date('Y-m-d', strtotime($date." +1 month")),
            'STATUS'=>'pendiente'
        ]);

        return response()->json(['status'=>true, 'data'=>$ID_SUSCRIPCION]);
    }
}
