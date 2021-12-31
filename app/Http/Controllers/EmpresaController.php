<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function newEmpresa(Request $request)
    {
        $this->validate($request, [
            'ID_RESPONSABLE' => 'required',
            'NAME_RESPONSABLE' => 'required',
            'EMAIL_RESPONSABLE' => 'required',
            'NAME' => 'required',
            'ADRESS' => 'required',
            'TELEPHONE' => 'required',
            'EMAIL' => 'required',
            'DESCRIPTION' => 'required',
            'LATLON' => 'required',
            'ID_PLAN' => 'required',
            'METHOD' => 'required',
            'CATEGORIA'=> 'required',
            'ETIQUETA'=> 'required'
        ]);

        $ID_RESPONSABLE = $request->ID_RESPONSABLE;

        $ID_EMPRESA = DB::table('empresa')->insertGetId([
                'ID_RESPONSABLE'=> $ID_RESPONSABLE,
                'NAME'=>$request->NAME,
                'ADRESS'=> $request->ADRESS,
                'TELEPHONE'=> $request->TELEPHONE,
                'EMAIL'=> $request->EMAIL,
                'DESCRIPTION'=> $request->DESCRIPTION,
                'LATLON'=> $request->LATLON
            ]);

        foreach ($request->CATEGORIA as $valor) {
            DB::table('categoria')->insert([
                'ID_EMPRESA' => $ID_EMPRESA,
                'NAME' => $valor
            ]);
        }

        foreach ($request->ETIQUETA as $valor) {
            DB::table('etiqueta')->insert([
                'ID_EMPRESA' => $ID_EMPRESA,
                'NAME' => $valor
            ]);
        }

        $res_createAlbum = app('App\Http\Controllers\ImageController')->createAlbum($request->NAME);

        if (json_decode($res_createAlbum)->success) {
            $res_initSuscripcion = app('App\Http\Controllers\SuscripcionController')->initSuscripcion($ID_EMPRESA, $request->ID_PLAN, $request->METHOD);

            DB::table('album')->insertGetId([
                    'ID_EMPRESA' => $ID_EMPRESA,
                    'IMGUR_ID' => json_decode($res_createAlbum)->data->id,
                    'IMGUR_DELETEHASH' => json_decode($res_createAlbum)->data->deletehash
                ]);

            return response()->json(['status'=>true, 'data'=>json_decode($res_createAlbum)->data->deletehash]);
        } else {
            return response()->json(['status'=>false, 'error' => 'create_album']);
        }
    }

    public function getEmpresas(Request $request)
    {
        $response = DB::table('empresa')->get();

        foreach ($response as $item) {
            $item->RESPONSABLE = DB::table('responsable')->where('ID_RESPONSABLE', $item->ID_RESPONSABLE)->get();
            $item->SUSCRIPCION = DB::table('suscripcion')->where('ID_EMPRESA', $item->ID_EMPRESA)->get();
            $item->ALBUM = DB::table('album')->where('ID_EMPRESA', $item->ID_EMPRESA)->get();
            $item->CATEGORIA = DB::table('categoria')->where('ID_EMPRESA', $item->ID_EMPRESA)->get();
            $item->ETIQUETA = DB::table('etiqueta')->where('ID_EMPRESA', $item->ID_EMPRESA)->get();
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function APPgetEmpresas(Request $request)
    {
        $categorias = DB::table('categoria')
            ->where('NAME', $request->NAME)
            ->inRandomOrder()
            ->get();

        $empresas = [];

        foreach ($categorias as $item) {
            $empresa = DB::table('empresa')
                ->where('ID_EMPRESA', $item->ID_EMPRESA)
                ->first();

            $suscripcion = DB::table('suscripcion')
                ->where('ID_EMPRESA', $item->ID_EMPRESA)
                ->where('STATUS', 'pagado')
                ->get()
                ->last();

            if (!is_null($suscripcion)) {
                $end = $suscripcion->DATE_END;
                $today = date("Y-m-d");

                if ($today <= $end) {

                    $album = DB::table('album')->where('ID_EMPRESA', $item->ID_EMPRESA)->first();
                    $empresa->AVATAR= app('App\Http\Controllers\ImageController')->getAvatar($album->IMGUR_DELETEHASH);
                    $empresa->ETIQUETA = DB::table('etiqueta')->where('ID_EMPRESA', $item->ID_EMPRESA)->get();
                    $empresa->PUNTUACION = DB::table('comentario')->where('ID_EMPRESA', $item->ID_EMPRESA)->avg('SCORE');
                    array_push($empresas, $empresa);
                }
            }
        }

        return response()->json(['status' => true, 'data' => $empresas]);
    }

    public function getEmpresa(Request $request)
    {
        $response = DB::table('empresa')
                        ->where('ID_EMPRESA', $request->ID_EMPRESA)
                        ->first();

        $response->RESPONSABLE = DB::table('responsable')->where('ID_RESPONSABLE', $response->ID_RESPONSABLE)->get();
        $response->SUSCRIPCION = DB::table('suscripcion')->where('ID_EMPRESA', $response->ID_EMPRESA)->orderBy('DATE_END', 'desc')->get();
        $response->ALBUM = DB::table('album')->where('ID_EMPRESA', $response->ID_EMPRESA)->get();
        $response->CATEGORIA = DB::table('categoria')->where('ID_EMPRESA', $response->ID_EMPRESA)->get();
        $response->ETIQUETA = DB::table('etiqueta')->where('ID_EMPRESA', $response->ID_EMPRESA)->get();

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function getResponsable(Request $request)
    {
        $response = DB::table('responsable')->get();

        foreach ($response as $item) {
            $item->EMPRESA = DB::table('empresa')->where('ID_RESPONSABLE', $item->ID_RESPONSABLE)->get();
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function updateEmpresa(Request $request)
    {
        $this->validate($request, [
            'ID_EMPRESA' => 'required',
            'NAME' => 'required',
            'ADRESS' => 'required',
            'TELEPHONE' => 'required',
            'EMAIL' => 'required',
            'DESCRIPTION' => 'required',
            'LATLON' => 'required',
            'METHOD' => 'required',
            'CATEGORIA'=> 'required',
            'ETIQUETA'=> 'required'
        ]);

        DB::table('empresa')
            ->where('ID_EMPRESA', $request->ID_EMPRESA)
            ->update([
                'NAME' => $request->NAME,
                'ADRESS' => $request->ADRESS,
                'TELEPHONE' => $request->TELEPHONE,
                'EMAIL' => $request->EMAIL,
                'DESCRIPTION' => $request->DESCRIPTION,
                'LATLON' => $request->LATLON
            ]);

        DB::table('categoria')
            ->where('ID_EMPRESA', $request->ID_EMPRESA)
            ->delete();
        DB::table('etiqueta')
            ->where('ID_EMPRESA', $request->ID_EMPRESA)
            ->delete();

        foreach ($request->CATEGORIA as $valor) {
            DB::table('categoria')->insert([
                'ID_EMPRESA' => $request->ID_EMPRESA,
                'NAME' => $valor
            ]);
        }

        foreach ($request->ETIQUETA as $valor) {
            DB::table('etiqueta')->insert([
                'ID_EMPRESA' => $request->ID_EMPRESA,
                'NAME' => $valor
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function APPgetComentarios(Request $request){
        $response = DB::table('comentario')
                        ->join('usuario', 'comentario.ID_USUARIO', '=', 'usuario.ID_USUARIO')
                        ->where('ID_EMPRESA', $request->ID_EMPRESA)
                        ->select('comentario.MESSAGE', 'comentario.DATE', 'comentario.SCORE', 'usuario.NAME')
                        ->orderBy('comentario.ID_COMENTARIO', 'desc')
                        ->simplePaginate(5);

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function APPsendComentario(Request $request){
        $this->validate($request, [
            'ID_USUARIO' => 'required',
            'ID_EMPRESA' => 'required',
            'SCORE' => 'required'
        ]);

        $date = date("Y-m-d H:i:s", time());

        DB::table('comentario')->insertGetId([
            'ID_USUARIO' => $request->ID_USUARIO,
            'ID_EMPRESA' => $request->ID_EMPRESA,
            'MESSAGE' => $request->MESSAGE,
            'DATE' => $date,
            'SCORE' => $request->SCORE
        ]);

        return response()->json(['status' => true, 'data' =>$date]);
    }
}
