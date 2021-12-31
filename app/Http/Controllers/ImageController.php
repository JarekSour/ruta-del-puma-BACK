<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.imgur.com/3/image',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('image' => $request->image, 'album'=>$request->album),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID '.env('CLIENT_ID')
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $json = json_decode($response);
        $hash = $json->data->deletehash;
        $link = $json->data->link;

        if($request->avatar){
            DB::table('imagen')
                ->where('ID_ALBUM', $request->album)
                ->where('IS_AVATAR', true)
                ->delete();
        }

        DB::table('imagen')->insertGetId([
            'ID_ALBUM' => $request->album,
            'URL_IMAGEN' => $link,
            'HASH' => $hash,
            'IS_AVATAR' => $request->avatar
        ]);

        return response()->json(['status'=>true, 'data'=>$json]);
    }

    public function deleteImage(Request $request)
    {
        DB::table('imagen')
            ->where('ID_ALBUM', $request->album_hash)
            ->where('HASH', $request->image_hash)
            ->delete();

        return response()->json(['status' => true]);
    }

    public function createAlbum($title)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.imgur.com/3/album',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('title' => urlencode($title) ,'privacy' => 'private'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Client-ID '.env('CLIENT_ID')
            )
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getAlbum(Request $request)
    {
        $response = DB::table('imagen')->where('ID_ALBUM', $request->hash)->get();
        return response()->json(['status' => true, 'data' => $response]);
    }

    public function getAvatar($hash){
        $response = DB::table('imagen')
                        ->where('ID_ALBUM', $hash)
                        ->where('IS_AVATAR', true)
                        ->first();

        return $response;
    }
}
