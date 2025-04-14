<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WakeupHelper
 *
 * @author Hoa Bui
 */

namespace App\Common\Youtube;

use App\Http\Models\VideoDaily;
use Illuminate\Support\Facades\DB;
use Log;

class WakeupHelper {

    //put your code here
    //json object:
    /*
     * video_id
     * is_claim
     * viewa
     * view_incr
     * view_incr_perc
     * child_connect
     * child_connect_times
     */
    private static function getStatics($ids) {

        $jData = null;
        try {
            $curl = curl_init();
            $url = "https://youtube.googleapis.com/youtube/v3/videos?part=statistics&id=$ids&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc";
//            error_log($url);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);

            $jData = json_decode($response);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        } finally {
            curl_close($curl);
        }
        return $jData;
    }

    private static function parseCacheData(&$cache50) {
        $arrIds = [];
        foreach ($cache50 as $vc) {
            $arrIds[] = $vc->video_id;
        }
        $jData = self::getStatics(implode(",", $arrIds));
        if (isset($jData)) {
            $items = !empty($jData->items) ? $jData->items : [];
            foreach ($items as $item) {
//                Log::info(json_encode($item));
                $vidID = $item->id;
                $views = !empty($item->statistics->viewCount) ? $item->statistics->viewCount : 0;
                VideoDaily::where("video_id", $vidID)->update(["wake_views" => $views]);
                foreach ($cache50 as $vc1) {
                    if ($vc1->video_id == $vidID) {
                        $viewNew = $views;
                        $vc1->view_incr = $viewNew - $vc1->view;
                        $vc1->view_incr_perc = $vc1->view > 0 ? ((($viewNew - $vc1->view) * 100) / $vc1->view) : 0;
                        $vc1->view = $viewNew;
                        break;
                    }
                }
            }
        }
    }

    private static function syncYoutubeData(&$lstVideo, $type = 3) {
        $cache50 = [];
        $cnt = 0;
        foreach ($lstVideo as $vid) {
            $cnt += 1;
            if ($cnt >= 50) {
                self::parseCacheData($cache50);
                $cnt = 0;
                $cache50 = [];
            }
            $cache50[] = $vid;
        }
        if (count($cache50) > 0) {
            self::parseCacheData($cache50);
        }
//        \App\Common\Utils::write("save/getStatics.txt", json_encode($lstVideo));
        //order
        switch ($type) {
            case 1:
                usort($lstVideo, function($a, $b) {
                    return $a->view < $b->view;
                });
                break;
            case 2:
                usort($lstVideo, function($a, $b) {
                    return $a->view_incr < $b->view_incr;
                });
                break;
            case 3:
                usort($lstVideo, function($a, $b) {
                    return $a->view_incr_perc < $b->view_incr_perc;
                });
                break;
        }
    }

    public static function createList(&$lstVideo, $type, $maxCnt = 20) {
        #wakeup by view 1
        #wakeup by view incr 2
        #wakeup by view incr perc 3
        self::syncYoutubeData($lstVideo, $type);
        $arrClaimAll = [];
        $arrLyric = [];
        $arrChildConnect = [];
        $arrAllVidId = [];
        foreach ($lstVideo as $vid) {
            if ($vid->view > 0) {
                $arrAllVidId[] = $vid->video_id;
                if ($vid->is_claim) {
                    $arrClaimAll[] = $vid->video_id;
                } else {
                    $arrLyric[] = $vid;
                    $child_connect = $vid->child_connect;
                    if (!isset($child_connect)) {
                        $arrChildConnect[] = $child_connect;
                    }
                }
            }
        }
        $arrClaim = array_diff($arrClaimAll, $arrChildConnect);
        $arrRs = [];
        foreach ($arrLyric as $vid) {
            if (count($arrRs) >= $maxCnt) {
                break;
            }
            $arrRs[] = $vid->video_id;
            $child_connect = $vid->child_connect;
            if (!isset($child_connect)) {
                $vidIDChild = array_shift($arrClaim);
                if (isset($vidIDChild)) {
                    $vid->child_connect = $vidIDChild;
                    $vid->child_connect_times = 0;
                    $arrRs[] = $vid->child_connect;
                } else {
                    
                }
            } else {
                $vid->child_connect_times += 1;
                $arrRs[] = $vid->child_connect;
            }
        }
        if (count($arrRs) < 1) {
            $arrRs = array_slice($arrAllVidId, 0, $maxCnt);
        }
        return $arrRs;
    }

    //hàm check hiệu quả của playlist wakeup
    public static function checkEfficiencyWakeup($playlistId) {
        $result = [];
        $playlist = YoutubeHelper::getPlaylist($playlistId, 0);
        $videoIds = $playlist['list_video_id'];
        $videoNames = $playlist['list_video_name'];
        $total = count($videoIds);
        if ($total == 0) {
            for ($i = 1; $i <= 10; $i++) {
                error_log("checkEfficiencyWakeup retry get video of $playlistId");
                $playlist = YoutubeHelper::getPlaylist($playlistId, 0);
                $videoIds = $playlist['list_video_id'];
                $videoNames = $playlist['list_video_name'];
                $total = count($videoIds);
                if ($total > 0) {
                    break;
                }
            }
            error_log("checkEfficiencyWakeup $playlistId $total");
            return $result;
        }
        error_log("checkEfficiencyWakeup $playlistId $total");
        for ($i = 0; $i < $total - 1; $i++) {
            $videoId = $videoIds[$i];
            $videoName = $videoNames[$i];

            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($videoId);
            if ($videoInfo["status"] == 0) {
                for ($t = 0; $t < 15; $t++) {
                    error_log("checkEfficiencyWakeup Retry $playlistId $videoId");
                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($videoId);
                    if ($videoInfo["status"] == 1) {
                        break;
                    }
                }
            }
            $isWake = 0;
            if ($videoInfo["next_video"] == $videoIds[$i + 1]) {
                $isWake = 1;
            }
            $result[] = (object) [
                        "video_id" => $videoId,
                        "next_video" => $videoInfo["next_video"],
                        "wakeup" => $isWake,
                        "title" => $videoName
            ];
        }
        return $result;
    }

}
