<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $adm = DB::table('admin')
                    ->where('EMAIL', '=', $request->email)
                    ->first();

        if ($adm) {
            if(is_null($adm->NAME)){
                DB::table('admin')
                ->where('ID_ADMIN', $adm->ID_ADMIN)
                ->update([
                    'NAME' => $request->name,
                    'PHOTOURL' => $request->photoUrl,
                    'TOKEN' => $request->authToken
                ]);
            }

            return response()->json(['status'=>true]);

        } else {
            return response()->json(['status'=>false, 'error'=>'not_register']);
        }
    }
}
