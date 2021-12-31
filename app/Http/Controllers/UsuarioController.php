<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function APPlogin(Request $request)
    {
        $usr = DB::table('usuario')
                    ->where('USER_ID_GOOGLE', '=', $request->userID)
                    ->first();

        if ($usr) {
            return response()->json(['status'=>true, 'data'=>$usr->ID_USUARIO]);
        } else {

            $ID_USR = DB::table('usuario')->insertGetId([
                'USER_ID_GOOGLE' => $request->userID,
                'NAME' => $request->NAME,
                'EMAIL' => $request->EMAIL,
                'IMG_URL' => $request->IMG_URL
            ]);

            return response()->json(['status'=>true, 'data'=>$ID_USR]);
        }
    }
}
