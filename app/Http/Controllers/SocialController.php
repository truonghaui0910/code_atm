<?php

namespace App\Http\Controllers;

use Log;
use function GuzzleHttp\json_encode;

class SocialController extends Controller {

    public function updateChannel($data) {
        Log::info(json_encode($data));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://social.automusic.win/items/owned_channels/$data->id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer liSBfojF58Hu_B0KQAjgFhJiaIxAX0Od',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        Log::info($response);
        curl_close($curl);
        return $response;
    }
    
}
