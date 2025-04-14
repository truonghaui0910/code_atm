<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DeezerHelper
 *
 * @author bonni
 */

namespace App\Common\Youtube;
use App\Common\Youtube\VideoInfo;

class DeezerHelper {

    //put your code here
    public static function parseSource($url) {
        $arrRegex = array();
        $arrRegex["track"] = "/deezer.com\/\w+\/track\/(?<id>\d+)/";
        $arrRegex["playlist"] = "/deezer.com\/\w+\/playlist\/(?<id>\d+)/";
        $arrRegex["album"] = "/deezer.com\/\w+\/album\/(?<id>\d+)/";
        $arrRegex["artist"] = "/deezer.com\/\w+\/artist\/(?<id>\d+)/";
        foreach ($arrRegex as $key => $regex) {
            if (preg_match($regex, $url, $match) == 1) {
                return array($key, $match['id']);
            }
        }
        return array("error", 0);
    }

    public static function processSource($arrSource) {
        $arrResult = array();
        switch ($arrSource[0]) {
            case "artist":
                try {
                    $res = file_get_contents("http://api.deezer.com/artist/" . $arrSource[1] . "/top?limit=50");
                    $obj = json_decode($res);
                    $arrData = $obj->data;
                    foreach ($arrData as $track) {
                        $vidInfo = new VideoInfo($track->id, $track->title_short);
                        $vidInfo->type = "deezer";
                        $vidInfo->length = $track->duration;
                        $vidInfo->duration = $track->duration;
                        $vidInfo->artist = trim($track->artist->name);
                        $arrResult[] = $vidInfo;
                    }
                } catch (Exception $ex) {
                    
                }
                break;
            case "album":
                try {
                    $res = file_get_contents("http://api.deezer.com/album/" . $arrSource[1] . "/tracks");
                    $obj = json_decode($res);
                    $arrData = $obj->data;
                    foreach ($arrData as $track) {
                        $vidInfo = new VideoInfo($track->id, $track->title_short);
                        $vidInfo->type = "deezer";
                        $vidInfo->length = $track->duration;
                        $vidInfo->duration = $track->duration;
                        $vidInfo->artist = $track->artist->name;
                        $arrResult[] = $vidInfo;
                    }
                } catch (Exception $ex) {
                    
                }
                break;
            case "playlist":
                try {
                    $res = file_get_contents("http://api.deezer.com/playlist/" . $arrSource[1] . "/tracks");
                    $obj = json_decode($res);
                    $arrData = $obj->data;
                    foreach ($arrData as $track) {
                        $vidInfo = new VideoInfo($track->id, $track->title_short);
                        $vidInfo->type = "deezer";
                        $vidInfo->length = $track->duration;
                        $vidInfo->duration = $track->duration;
                        $vidInfo->artist = $track->artist->name;
                        $arrResult[] = $vidInfo;
                    }
                } catch (Exception $ex) {
                    
                }
                break;
            case "track":
                try {
                    $res = file_get_contents("http://api.deezer.com/track/" . $arrSource[1]);
                    $obj = json_decode($res);
                    $vidInfo = new VideoInfo($obj->id, $obj->title_short);
                    $vidInfo->type = "deezer";
                    $vidInfo->length = $obj->duration;
                    $vidInfo->duration = $obj->duration;
                    $vidInfo->artist = $obj->artist->name;
                    $arrResult[] = $vidInfo;
                } catch (Exception $ex) {
                    
                }
                break;
            case "error":
                break;
        }
        return $arrResult;
    }

    public static function getTrackByLink($url) {
        return self::processSource(self::parseSource($url));
    }

}
