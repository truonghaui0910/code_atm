<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use App\Common\Network\ClientHelper;
use App\Common\Utils;
use Log;

set_time_limit(0);

class RequestHelper {

    public static function postClient($urlClient, $data) {

        $json_response = null;
        try {
            $content = json_encode($data);
            $curl = curl_init($urlClient);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 400);
            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
        } catch (Exception $ex) {
            
        }
        return $json_response;
    }

    public static function get($url, $UAType = 1) {
        /* $UAType
         * 0: nothing
         * 1: mobile
         * 2: desktop
         * 3: google bot
         */
        $urlClient = ClientHelper::getUniqueClient();
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/proxy/get/";
            $data = array();
            $data["url"] = $url;
            $data["ua_type"] = $UAType;
            $res = self::postClient($urlClient, $data);
        }
        return $res;
    }

    public static function getWithIp($url, $urlClient, $UAType = 1) {
        /* $UAType
         * 0: nothing
         * 1: mobile
         * 2: desktop
         * 3: google bot
         */
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/proxy/get/";
            $data = array();
            $data["url"] = $url;
            $data["ua_type"] = $UAType;
            $res = self::postClient($urlClient, $data);
        }
        //error_log($res);
        return $res;
    }

    public static function search($query, $country, $tbm = '') {
        $urlClient = ClientHelper::getUniqueClient();
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/search/";
            $data = array();
            $data["query"] = $query;
            $data["tld"] = $country;
            $data["tbm"] = $tbm;
            $res = self::postClient($urlClient, $data);
        }
        return $res;
    }

    public static function searchWithIp($query, $country, $urlClient) {
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/search/";
            $data = array();
            $data["query"] = $query;
            $data["tld"] = $country;
            $data["tbm"] = "";
            $res = self::postClient($urlClient, $data);
        }
        return $res;
    }

    public static function playlist($link, $data) {
        $urlClient = ClientHelper::getUniqueClient();
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/playlist/$link";
            $res = self::postClient($urlClient, $data);
        }
        return $res;
    }

    public static function blogger($link, $data) {
        $urlClient = ClientHelper::getUniqueClient();
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/blogger/$link";
            $res = self::postClient($urlClient, $data);
        }
        return $res;
    }

    public static function getUrl($url, $is_token = 0) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($is_token == 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Token bb1131f893f91f1bf5461285b26c0b622d21a37e"));
        }
        curl_setopt($ch, CURLOPT_HEADER, 0); // return headers 0 no 1 yes
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return page 1:yes
        curl_setopt($ch, CURLOPT_TIMEOUT, 20); // http request timeout 20 seconds
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects, need this if the url changes
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2); //if http server gives redirection responce
        #curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt"); // cookies storage / here the changes have been made
        #curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // false for https
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        #curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function postRequest($url, $data) {
        $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200 && $status != 201) {
            Log::info("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        //$response = json_decode($json_response, true);
        return $json_response;
    }

    public static function getRequest($url) {
//        $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 600000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, false);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200 && $status != 201) {
            Log::info("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        //$response = json_decode($json_response, true);
        return $json_response;
    }

    public static function putRequest($url, $data) {
        $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200 && $status != 201) {
            Log::info("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        //$response = json_decode($json_response, true);
        return $json_response;
    }

    public static function delRequest($url, $data) {
        $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200 && $status != 201 && $status != 204) {
            Log::info("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        //$response = json_decode($json_response, true);
        return $json_response;
    }

    public static function callAPI($method, $url, $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 3600000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json", "Authorization: Token 04911317e4764c85b51f8e302c86afe30d07e967"));

        switch ($method) {
            case "GET":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        $response = curl_exec($curl);
//        Log::info($response);
        $datas = json_decode($response);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Check the HTTP Status code
        switch ($httpCode) {
            case 200:
                $error_status = "200: Success";
                return ($datas);
            case 201:
                $error_status = "201: Success";
                return ($datas);
            case 401:
                $error_status = "401: Success";
                return ($datas);
            case 404:
                $error_status = "404: API Not found";
                break;
            case 500:
                $error_status = "500: servers replied with an error.";
                break;
            case 502:
                $error_status = "502: servers may be down or being upgraded. Hopefully they'll be OK soon!";
                break;
            case 503:
                $error_status = "503: service unavailable. Hopefully they'll be OK soon!";
                break;
            default:
                $error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);
                break;
        }
        curl_close($curl);

        die;
    }

    public static function callAPI2($method, $url, $data, $header = array("Content-type: application/json", "platform: AutoWin")) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        switch ($method) {
            case "GET":
//                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "PATCH":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
                break;
            case "OPTIONS":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "OPTIONS");
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        $response = curl_exec($curl);
//        Log::info("response=".$response);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $datas = json_decode($response);
//        Log::info($httpCode);
//        Log::info(curl_error($curl));
        // Check the HTTP Status code
        switch ($httpCode) {
            case 200:
                $error_status = "200: Success";
                return ($datas);
            case 201:
                $error_status = "201: Success";
                return ($datas);
            case 401:
                $error_status = "401: Success";
                return ($datas);
            case 404:
                $error_status = "404: API Not found";
                break;
            case 500:
                $error_status = "500: servers replied with an error.";
                return "";
            case 502:
                $error_status = "502: servers may be down or being upgraded. Hopefully they'll be OK soon!";
                break;
            case 503:
                $error_status = "503: service unavailable. Hopefully they'll be OK soon!";
                break;
            default:
                $error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);
                break;
        }
        curl_close($curl);

        die;
    }

    public static function socialApi($method, $url, $data, $header = array("Content-type: application/json")) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        if ($response != null) {
            $datas = json_decode($response);
            return ($datas);
        }
        return null;
    }

    public static function getGologin($url, $gmail) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("platform: autowin", "gmail:$gmail"));
        curl_setopt($curl, CURLOPT_POST, false);
        $json_response = curl_exec($curl);
        curl_close($curl);
        return $json_response;
    }

    //goi API không cần trả về kết quả
    public static function fetchWithoutResponseURL($url) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT_MS => 2000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Accept-Language: en-US"
            ),
        ));
        curl_exec($curl);
        curl_close($curl);
    }

    public static function labelgridImageUpload($image) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.labelgrid.com/api/v3/artists/new/photo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('filename' => new \CURLFILE($image)),
//            $file->file = "@File.txt;filename=file;type=text/plain";
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1MTAyMDNiYy03MDk4LTQ4NDgtYWEyMC01YjBhYTg5Y2EyMDAiLCJqdGkiOiI3YTVmMWI2ZTg3YzY0MmQ5NWE2MWU3YjFiZTA2ZGEzM2NmNjI0N2JjMTYwNmY4ZGYyMTJhODRmOTlkNjQxMDcxODVkZGM4NGUxYTJmZTMyMSIsImlhdCI6MTY1NDE1NjA5NS4zMzA4OTQ5NDcwNTIwMDE5NTMxMjUsIm5iZiI6MTY1NDE1NjA5NS4zMzA5MDA5MDc1MTY0Nzk0OTIxODc1LCJleHAiOjE5Njk3NzUyOTUuMjUyMjk5MDcwMzU4Mjc2MzY3MTg3NSwic3ViIjoiMTQzIiwic2NvcGVzIjpbInVzZXIudmlldy1jYXRhbG9nIiwidXNlci5nYXRlLXVzZSJdfQ.F8WBZmWETwEmSFumMKbFpCHtJReUjmsk6HQPBX-QWzVmTXHL6W3HbnXG-flaVWT044aRoYJ3wzZgH1abUshFVCKK4cID8fC5o69A1626GkIp2Iy8OTHvasHswAR-NFensKGIMEOYxD-NGs7rxZqwXeNX_LINlHXEu5aWPoKWSvznUP9VrEKB0ehCo50kwW9QbrAW_r7UzNynOaE29KQ493H4wRPvF2nztoykQjb3E-jqVkZxsNEuLNaEfwV1PpRVClF66PWRhg1Drd42WjWlcPAm6fVrrNiXF9rwRGepKZDNAORjOqGQt3VPeT5SzglJjpt-CkgNAFpsTzAp2cjNQa_g6gYVdYm5eO6ypj1RXZ3-R6jDV2EuTCww8HPGILc_jkE9XOP5eXIQXZyWPLCh4znNbk2pusan_m-jEq4zvyX9uJ-Cd9VNEjdj7AiHnJ7q1nWWHlrbgTfgpNPK0bE4K1rxUvANTKFCs1FRpsvSdMewNeYDB0yGcDeN7CNtnLVbmNNmrXvH_0ONF8H1KZmjTP2VGfiglIMNHpmukkSukkE43AcLSJSsIf5jFCLRWFx_x-3Fbj4127zOK8aqgX_Ya3lb_ellKlsZpXz3Vx5-S_fDKKNCAynBDI1MDutvV9-GkM9yuw4hKaiZ56fJshUJKYC4vO2IstLMOlJyBbRpn2U'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function labelgridArtist($data) {
        $curl = curl_init();
        $input = '{"artistName":"' . $data->artistName . '","fullName":null,"aliases":null,"email":null,"location":null,"photo":null,"photo_tmp":"' . $data->photo_tmp . '","djSupport":null,"bioShort":"' . $data->bioShort . '","bioFull":"' . $data->bioFull . '","website":null,"soundcloudId":null,"soundcloudUrl":null,"soundcloudWhitelisted":null,"soundcloudAvatarUrl":null,"facebookUrl":null,"youtubeUrl":"' . $data->youtubeUrl . '","twitterUrl":null,"shopUrl":null,"beatportUrl":null,"junoUrl":null,"itunesUrl":null,"instagramUrl":null,"spotifyUrl":null,"spotifyUri":null,"members":null,"affiliations":null,"extendedMetadata":{"artistName":{"languages":{"en":{"isoCode":"en","value":"' . $data->artistName . '"},"ar":{"isoCode":"ar","value":null,"phonetic":null,"phoneticRequired":false},"yue-Hant":{"isoCode":"yue-Hant","value":null,"phonetic":null,"phoneticRequired":false},"zh-Hans":{"isoCode":"zh-Hans","value":null,"phonetic":null,"phoneticRequired":false},"zh-Hant":{"isoCode":"zh-Hant","value":null,"phonetic":null,"phoneticRequired":false},"da":{"isoCode":"da","value":null},"nl":{"isoCode":"nl","value":null},"et":{"isoCode":"et","value":null},"fr":{"isoCode":"fr","value":null,"phonetic":null,"phoneticRequired":false},"de":{"isoCode":"de","value":null,"phonetic":null,"phoneticRequired":false},"he":{"isoCode":"he","value":null,"phonetic":null,"phoneticRequired":false},"it":{"isoCode":"it","value":null,"phonetic":null,"phoneticRequired":false},"ja-Jpan":{"isoCode":"ja-Jpan","value":null,"phonetic":null,"phoneticRequired":true},"ko":{"isoCode":"ko","value":null,"phonetic":null,"phoneticRequired":false},"es":{"isoCode":"es","value":null,"phonetic":null,"phoneticRequired":false},"es-419":{"isoCode":"es-419","value":null,"phonetic":null,"phoneticRequired":false},"th":{"isoCode":"th","value":null},"mg":{"isoCode":"mg","value":null,"phonetic":null,"phoneticRequired":false},"pcm":{"isoCode":"pcm","value":null,"phonetic":null,"phoneticRequired":false},"hi":{"isoCode":"hi","value":null,"phonetic":null,"phoneticRequired":false},"vi":{"isoCode":"vi","value":null,"phonetic":null,"phoneticRequired":false},"pt":{"isoCode":"pt","value":null,"phonetic":null,"phoneticRequired":false},"lg":{"isoCode":"lg","value":null,"phonetic":null,"phoneticRequired":false},"mk":{"isoCode":"mk","value":null,"phonetic":null,"phoneticRequired":false},"ru":{"isoCode":"ru","value":null,"phonetic":null,"phoneticRequired":false},"la":{"isoCode":"la","value":null}}}},"extendedMembers":[]}';
        error_log("labelgridArtist " . $input);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.labelgrid.com/api/v3/artists',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
            CURLOPT_HTTPHEADER => array(
                "authorization: $data->authorization",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function labelgridRelease($data) {
        $curl = curl_init();
        $input = '{"$id":"$uid1","id":"$uid1","displayArtist":"","title":"' . $data->title . '","labelId":2337,"cat":"' . $data->cat . '","descriptionLong":"' . $data->descriptionLong . '","descriptionShort":"' . $data->descriptionShort . '","backCover":"","genre1Id":296,"genre2Id":207,"physicallyReleased":"","titleType":"single","titlePricing":"","publisherYear":"' . $data->publisherYear . '","publisherName":"SoundHex","copyrightYear":"' . $data->copyrightYear . '","copyrightName":"SoundHex","priceTier":"","siteStatus":"public","releaseDate":"' . $data->releaseDate . '","promoDateStart":"","promoDateEnd":"","addictechId":"","preferredLocalization":"es","dateLastTranscoded":"","dateLastEdited":"1","dateImageUpdated":"","bclink":"","freeDownload":"","fbLikeUrl":"","permUrl":"","explicit":"","allowiTunesPreorders":"on","editorUserIds":"","promoterUserIds":"","artistUserIds":"","distributionTerritories":"","artists":[{"artistId":' . $data->artistId . ',"releaseId":"$uid1","ArtisticRole":"MainArtist","position":0}],"cline":"' . $data->cline . '","pline":"' . $data->pline . '"}';
        error_log("labelgridRelease " . $input);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.labelgrid.com/api/v3/releases',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
            CURLOPT_HTTPHEADER => array(
                "authorization: $data->authorization",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function labelgridTrack($data) {
        $curl = curl_init();
        $contributors = json_encode($data->contributors);
        $input = '{"$id":"$uid10","id":"$uid10","releaseId":' . $data->releaseId . ',"cat":"","artist":"' . $data->artist . '","artistTypes":"Primary Artist","displayArtist":"","songwriters":"","songwriterTypes":"","mixVersion":"","remixer":"","title":"' . $data->title . '","genre1Id":296,"genre2Id":null,"genre3Id":null,"tags":"","trackNum":1,"explicit":false,"albumOnly":false,"freeDownload":"","distributionTerritories":"","excludedTerritories":"","ownerId":"","isMix":"","altISRCs":"","publisherYear":"' . $data->publisherYear . '","publisherName":"SoundHex","copyrightYear":"' . $data->copyrightYear . '","copyrightName":"SoundHex","release":null,"owner":null,"cline":"' . $data->cline . '","pline":"' . $data->pline . '","publishers":[{"value":"SoundHex","name":"SoundHex"}],"contributors":' . $contributors . ',"lyrics":"' . $data->lyrics . '","artists":[{"artistId":' . $data->artistId . ',"trackId":"$uid10","artistName":"' . $data->artist . '","DisplayArtistRole":"","ArtisticRole":"MainArtist","extendedMembers":[],"position":0,"track":null,"artist":null,"$isDirty":false,"$isNew":true}],"$isDirty":true,"$isNew":false}';
        $temp = json_decode($input);
        error_log("labelgridTrack " . $input);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.labelgrid.com/api/v3/tracks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
            CURLOPT_HTTPHEADER => array(
                "authorization: $data->authorization",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function labelgridGetPresignedPut($data) {
        $curl = curl_init();
        $input = '{"filePath":"' . $data->img . '","contentType":"' . $data->contentType . '","operation":"putObject"}';
        error_log("labelgridGetPresignedPut " . $input);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.labelgrid.com/api/v3/getPresignedPut',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
            CURLOPT_HTTPHEADER => array(
                "authorization: $data->authorization",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function labelgridPutFile($url, $path, $contentType) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => file_get_contents($path),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: $contentType"
            ),
        ));

        curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        error_log("labelgridPutFile " . $httpCode);
        curl_close($curl);
        return $httpCode;
    }

    public static function labelgridFetchS3FileImage($data) {
        $curl = curl_init();
        $input = '{"releaseId":' . $data->releaseId . ',"filename":"","status":"complete","percentage":0,"bucket":"tmp-lgdz","objectKey":"' . $data->objectKey . '","context":{"field":"frontCover","recordId":' . $data->releaseId . ',"storeName":"Releases"},"$isDirty":true,"$isNew":false}';
        error_log("labelgridFetchS3FileImage " . $input);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.labelgrid.com/api/v3/releases/' . $data->releaseId . '/fetchS3File',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
            CURLOPT_HTTPHEADER => array(
                "authorization: $data->authorization",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function labelgridFetchS3FileAudio($data) {
        $curl = curl_init();
        $input = '{"trackId":57245,"filename":"too-young-take45-dirty-good.wav","status":"complete","percentage":0,"bucket":"tmp-lgdz","objectKey":"' . $data->objectKey . '","context":{"field":"fileWAV","recordId":57245,"storeName":"Tracks"},"$isDirty":true,"$isNew":false}';
        error_log("labelgridArtist " . $input);
        curl_setopt_array($curl, array(
            CURLOPT_URL => ' https://api.labelgrid.com/api/v3/tracks/57245/fetchS3File',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
//            CURLOPT_HTTPHEADER => array(
//                'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1MTAyMDNiYy03MDk4LTQ4NDgtYWEyMC01YjBhYTg5Y2EyMDAiLCJqdGkiOiI3YTVmMWI2ZTg3YzY0MmQ5NWE2MWU3YjFiZTA2ZGEzM2NmNjI0N2JjMTYwNmY4ZGYyMTJhODRmOTlkNjQxMDcxODVkZGM4NGUxYTJmZTMyMSIsImlhdCI6MTY1NDE1NjA5NS4zMzA4OTQ5NDcwNTIwMDE5NTMxMjUsIm5iZiI6MTY1NDE1NjA5NS4zMzA5MDA5MDc1MTY0Nzk0OTIxODc1LCJleHAiOjE5Njk3NzUyOTUuMjUyMjk5MDcwMzU4Mjc2MzY3MTg3NSwic3ViIjoiMTQzIiwic2NvcGVzIjpbInVzZXIudmlldy1jYXRhbG9nIiwidXNlci5nYXRlLXVzZSJdfQ.F8WBZmWETwEmSFumMKbFpCHtJReUjmsk6HQPBX-QWzVmTXHL6W3HbnXG-flaVWT044aRoYJ3wzZgH1abUshFVCKK4cID8fC5o69A1626GkIp2Iy8OTHvasHswAR-NFensKGIMEOYxD-NGs7rxZqwXeNX_LINlHXEu5aWPoKWSvznUP9VrEKB0ehCo50kwW9QbrAW_r7UzNynOaE29KQ493H4wRPvF2nztoykQjb3E-jqVkZxsNEuLNaEfwV1PpRVClF66PWRhg1Drd42WjWlcPAm6fVrrNiXF9rwRGepKZDNAORjOqGQt3VPeT5SzglJjpt-CkgNAFpsTzAp2cjNQa_g6gYVdYm5eO6ypj1RXZ3-R6jDV2EuTCww8HPGILc_jkE9XOP5eXIQXZyWPLCh4znNbk2pusan_m-jEq4zvyX9uJ-Cd9VNEjdj7AiHnJ7q1nWWHlrbgTfgpNPK0bE4K1rxUvANTKFCs1FRpsvSdMewNeYDB0yGcDeN7CNtnLVbmNNmrXvH_0ONF8H1KZmjTP2VGfiglIMNHpmukkSukkE43AcLSJSsIf5jFCLRWFx_x-3Fbj4127zOK8aqgX_Ya3lb_ellKlsZpXz3Vx5-S_fDKKNCAynBDI1MDutvV9-GkM9yuw4hKaiZ56fJshUJKYC4vO2IstLMOlJyBbRpn2U',
//                'content-type: application/json'
//            ),
            CURLOPT_HTTPHEADER => array(
                "authorization: $data->authorization",
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function getDriveFiles($folderId) {
        $key = ["AIzaSyB0QH1MoohfCLR746NU5hNffzGPMDMAAxQ"];
        $url = "https://www.googleapis.com/drive/v3/files?q='$folderId'+in+parents&key=" . $key[rand(0, count($key) - 1)];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
//        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $data = json_decode($response);

        return response()->json($data);
    }

}

?>
