<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use Log;

class EpidemicSoundHelper {

    public static function getPlaylistData($playlistID, $csrftoken = 'UEn8liAmiXuw8KBqSCiZHB838CPMrIJb', $sessionid = '2hheb6zt336jgokzmnpcdtkbnms3ste3') {
        $ch = curl_init();
        $offset = 0;
        $results = [];
        $isNext = True;
        if(\App\Common\Utils::containString($playlistID, "https://www.epidemicsound.com/saved/")){
        $playlistID = str_replace("https://www.epidemicsound.com/saved/", "", $playlistID);
            
        }
        if(\App\Common\Utils::containString($playlistID, "https://www.epidemicsound.com/playlist/")){
        $playlistID = str_replace("https://www.epidemicsound.com/playlist/", "", $playlistID);
            
        }
        
        $playlistID = str_replace("/", "", $playlistID);

        Log::info("playlistID $playlistID");
        while ($isNext) {
            $isNext = False;

            $url = "https://www.epidemicsound.com/api/saved/playlists/$playlistID/entries/?include_tracks=1&limit=50&offset=$offset";
            Log::info($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Host: www.epidemicsound.com',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/111.0',
                'accept: application/json',
                'accept-language: vi-VN,vi;q=0.8,en-US;q=0.5,en;q=0.3',
                "referer: https://www.epidemicsound.com/saved/$playlistID/",
                "x-csrftoken: $csrftoken",
                'x-requested-with: XMLHttpRequest',
                'content-type: application/json',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'te: trailers',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=$csrftoken; sessionid=$sessionid;");

            $response = curl_exec($ch);
            Log::info("RE:".$response);
            
            $obj = json_decode($response);
            if(!empty($obj->detail)){
                return $obj;
            }
            
            if (array_key_exists("next_offset", $obj)) {
                $next_offset = $obj->next_offset;
                if (isset($next_offset)) {
                    $offset = $next_offset;
                    $isNext = True;
                }
            }
//            Log::info("Result:".json_encode($obj->results));
            $results = array_merge($results, $obj->results);
        }
        curl_close($ch);
        return $results;
    }
    
    public static function getOvertoneData() {
        $curl = curl_init();
//        https://overtone-studios.disco.ac/api/client_library/1939628463/my_playlist/
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://overtone-studios.disco.ac/api/client_library/1939628463/my_playlist/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'accept-language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5',
                'content-type: application/json',
                'cookie: csrftoken=xmRr5As4poKnlQBH0sMndDEaqVDxvNiJ; disco_businesses="[]"; sessionid=xj4riolvtdu73t26argypaxhgdk1ykbd; mp_d041f61e9c4961af3cff17d999ae1966_mixpanel=%7B%22distinct_id%22%3A%20%2239856-2499543%22%2C%22%24device_id%22%3A%20%221926b13e3141ac1b-0af37013df14c2-26001051-1fa400-1926b13e3141ac1b%22%2C%22%24user_id%22%3A%20%2239856-2499543%22%2C%22%24initial_referrer%22%3A%20%22%24direct%22%2C%22%24initial_referring_domain%22%3A%20%22%24direct%22%7D; disco_businesses="[]"; csrftoken=OKIVOsvMDZ8TCgokZ9SSfRFp7lb21XBc; sessionid=xj4riolvtdu73t26argypaxhgdk1ykbd',
                'priority: u=1, i',
                'referer: https://overtone-studios.disco.ac/cat/1939628463/my-music',
                'sec-ch-ua: "Google Chrome";v="129", "Not=A?Brand";v="8", "Chromium";v="129"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36'
            ),
        ));
        $result = null;
        $response = curl_exec($curl);
        if ($response != null) {
            $result = json_decode($response);
        }
        curl_close($curl);
        return $result;
    }    

}
