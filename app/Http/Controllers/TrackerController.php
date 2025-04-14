<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\Z1VideoClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class TrackerController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.index|request=' . json_encode($request->all()));
        $datas = CampaignStatistics::where("type", 3)->get();
        Log::info(json_encode($datas));
        return view('components.tracker', [
            'datas' => $datas,
            'request' => $request
        ]);
    }

    public function detailcampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.detailcampaign');
        $datas = Campaign::where('campaign_id', $request->id)->get();
        return $datas;
    }

    public function addtracker(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $i = 0;
        $array_videos = array();
        Log::info($user->user_name . '|TrackerController.addcampaign|request=' . json_encode($request->all()));
        if ($request->cam_id == null) {
            $checkCampaign = CampaignStatistics::where("campaign_name", $request->tracking_group)->first();
            if (!$checkCampaign) {
                $campaignStatistics = new CampaignStatistics();
                $campaignStatistics->type = 3;
                $campaignStatistics->campaign_name = $request->tracking_group;
                $campaignStatistics->genre = strtoupper($request->genre);
                $campaignStatistics->views_detail = json_encode(array());
                $campaignStatistics->create_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                $campaignStatistics->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                $campaignStatistics->save();
                $campaignId = $campaignStatistics->id;
            } else {
                $campaignId = $checkCampaign->id;
            }

            //quet lai tat ca video nao quet info sit
            if ($request->list_video == null || $request->list_video == "") {
//            return array("status" => "danger", "content" => "You must enter list video");
                $dataCampaigns = Campaign::where("channel_name", "")->orWhere('video_title', "")->orWhere('publish_date', 0)->get();
                foreach ($dataCampaigns as $dataCampaign) {
                    $array_videos[] = "https://www.youtube.com/watch?v=$dataCampaign->video_id";
                }
            } else {
                $temp = str_replace(array("\r\n", "\n", " "), "@,@", $request->list_video);
                $array_videos = explode("@,@", $temp);
            }
            foreach ($array_videos as $video) {
                $info = YoutubeHelper::processLink($video);
                if ($info["type"] == 3) {
                    $video_id = $info["data"];
                    $videoInfo = YoutubeHelper::getVideoInfoV2($video_id);
                    if ($videoInfo["status"] == 0) {
                        for ($t = 0; $t < 15; $t++) {
                            error_log("Retry $video_id");
                            $videoInfo = YoutubeHelper::getVideoInfoV2($video_id);
                            error_log(json_encode($videoInfo));
                            if ($videoInfo["status"] == 1) {
                                break;
                            }
                        }
                    }
                    $check = Campaign::where("video_id", $video_id)->first();
                    $i++;
                    if (!$check) {
                        $campage = new Campaign();
                        $campage->campaign_id = $campaignId;
                        $campage->campaign_name = $request->tracking_group;
                        $campage->channel_name = $videoInfo["channelName"];
                        $campage->video_type = $request->video_type;
                        $campage->video_id = $video_id;
                        $campage->video_title = $videoInfo["title"];
                        $campage->views_detail = '[]';
                        $campage->status = $videoInfo["status"];
                        $campage->create_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                        $campage->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                        $campage->publish_date = $videoInfo["publish_date"];
                        $campage->save();
                    } else {
                        $check->channel_name = $videoInfo["channelName"];
                        $check->video_title = $videoInfo["title"];
                        $check->status = $videoInfo["status"];
                        $check->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                        $check->publish_date = $videoInfo["publish_date"];
                        $check->save();
                    }
                }
            }
        } else {
            $checkStatistics = CampaignStatistics::find($request->cam_id);
            if ($checkStatistics) {
                $checkStatistics->campaign_name = $request->tracking_group;
                $checkStatistics->campaign_start_date = $request->campaign_start_date;
                $checkStatistics->genre = strtoupper($request->genre);
                $checkStatistics->label = $request->label;
                $checkStatistics->save();
            }
        }


        return array("status" => "success", "content" => "Success $i videos", "contentedit" => "Success");
    }

    public function getcampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.getcampaign|request=' . json_encode($request->all()));
        $datas = Campaign::where("id", $request->id)->first(["id", "campaign_name", "views_detail"]);
        return $datas;
    }

    public function getcampaignstatistics(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.getcampaignstatistics|request=' . json_encode($request->all()));
        $datas = CampaignStatistics::where("id", $request->id)->first(["id", "campaign_name", "views_detail", "campaign_type", "campaign_start_date", "genre", "label", "artists_channel"]);
        return $datas;
    }

    public function scanCampaign(Request $request) {
        if (isset($request->video_id)) {
            $datas = Campaign::whereIn("video_id", $request->video_id)->where("status_use", 1)->get();
        } else {
            $datas = Campaign::where("status_use", 1)->get();
        }
        if (isset($request->date)) {
            $date = $request->date;
        } else {
            $date = gmdate("m-d-Y", time() + 7 * 3600);
        }
        $i = 0;
        $total = count($datas);
        foreach ($datas as $data) {
            $info = YoutubeHelper::getVideoInfoV2($data->video_id);
            if ($info["status"] == 0) {
                for ($t = 0; $t < 15; $t++) {
                    error_log("Retry $data->video_id");
                    $info = YoutubeHelper::getVideoInfoV2($data->video_id);
                    if ($info["status"] == 1) {
                        break;
                    }
                }
            }
            $inc_views = $info["view"] - $data->views;
            if ($inc_views < 0 || $inc_views == $info["view"]) {
                $inc_views = 0;
            }

            $inc_like = $info["like"] - $data->like;
            if ($inc_like < 0 || $inc_like == $info["like"]) {
                $inc_like = 0;
            }
            $inc_dislike = $info["dislike"] - $data->dislike;
            if ($inc_dislike < 0 || $inc_dislike == $info["dislike"]) {
                $inc_dislike = 0;
            }
            $inc_comment = $info["comment"] - $data->comment;
            if ($inc_comment < 0 || $inc_comment == $info["comment"]) {
                $inc_comment = 0;
            }
            $per_inc_views = $data->daily_views != 0 ? (($inc_views - $data->daily_views) / $data->daily_views * 100) : 0;
            $per_inc_like = $data->daily_like != 0 ? (($inc_like - $data->daily_like) / $data->daily_like * 100) : 0;
            $per_inc_dislike = $data->daily_dislike != 0 ? (($inc_dislike - $data->daily_dislike) / $data->daily_dislike * 100) : 0;
            $per_inc_comment = $data->daily_comment != 0 ? (($inc_comment - $data->daily_comment) / $data->daily_comment * 100) : 0;
            $views_detail = json_decode($data->views_detail);
            $flag = 0;
            foreach ($views_detail as $detail) {
                if ($detail->date == $date) {
                    $flag = 1;
                    $detail->views = $info["view"];
                    $detail->like = $info["like"];
                    $detail->dislike = $info["dislike"];
                    $detail->comment = $info["comment"];
                    $detail->daily = $inc_views;
                    $detail->daily_like = $inc_like;
                    $detail->daily_dislike = $inc_dislike;
                    $detail->daily_comment = $inc_comment;
                }
            }
            if ($flag == 0) {
                $views_log = (object) [
                            'date' => $date,
                            'views' => $info["view"],
                            'like' => $info["like"],
                            'dislike' => $info["dislike"],
                            'comment' => $info["comment"],
                            'daily' => $inc_views,
                            'daily_like' => $inc_like,
                            'daily_dislike' => $inc_dislike,
                            'daily_comment' => $inc_comment,
                ];
                $views_detail[] = $views_log;
            }
            $data->last_daily_meta = json_encode((object) [
                        'views' => $data->daily_views,
                        'like' => $data->daily_like,
                        'dislike' => $data->daily_dislike,
                        'comment' => $data->daily_comment,
            ]);
            $data->views = $info["view"];
            $data->like = $info["like"];
            $data->dislike = $info["dislike"];
            $data->comment = $info["comment"];
            $data->daily_views = $inc_views;
            $data->daily_like = $inc_like;
            $data->daily_dislike = $inc_dislike;
            $data->daily_comment = $inc_comment;
            $data->per_daily_views = $per_inc_views;
            $data->per_daily_like = $per_inc_like;
            $data->per_daily_dislike = $per_inc_dislike;
            $data->per_daily_comment = $per_inc_comment;
            $data->views_detail = json_encode($views_detail);
            $data->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
            $data->status = $info["status"];
            $data->publish_date = $info["publish_date"];

            $i++;
            $data->save();
            error_log("Campaign $date $i/$total $data->video_id   " . $info["status"] . ' ' . $info["view"]);
        }
        if (!isset($request->video_id)) {
            $campaignAll = DB::select("select campaign_name,sum(views) as views,sum(daily_views) as daily,
SUM(IF(video_type = 1, views, 0)) as views_official,
SUM(IF(video_type = 1, daily_views, 0)) as daily_oficial,
SUM(IF(video_type = 2, views, 0)) as views_lyric,
SUM(IF(video_type = 2, daily_views, 0)) as daily_lyric,
SUM(IF(video_type = 3, views, 0)) as views_tiktok,
SUM(IF(video_type = 3, daily_views, 0)) as daily_tiktok,
SUM(IF(video_type = 4, views, 0)) as views_lyric_video,
SUM(IF(video_type = 4, daily_views, 0)) as daily_lyric_video,
SUM(IF(video_type = 5, views, 0)) as views_compi,
SUM(IF(video_type = 5, daily_views, 0)) as daily_compi 
from campaign group by campaign_name");
//            Log::info(json_encode($campaignAll));
            foreach ($campaignAll as $data) {
                $temp = CampaignStatistics::where("campaign_name", $data->campaign_name)->first();
                if ($temp) {
                    if ($temp->views_detail != null) {
                        $views_detail = json_decode($temp->views_detail);
                    } else {
                        $views_detail = array();
                    }
                    $flag = 0;
                    foreach ($views_detail as $detail) {
                        if ($detail->date == $date) {
                            $flag = 1;
                            $detail->views = intval($data->views);
                            $detail->daily = intval($data->daily);
                            $detail->oficial = intval($data->daily_oficial);
                            $detail->lyric = intval($data->daily_lyric);
                            $detail->tiktok = intval($data->daily_tiktok);
                            $detail->lyric_video = intval($data->daily_lyric_video);
                            $detail->compi = intval($data->daily_compi);
                        }
                    }
                    if ($flag == 0) {
                        $views_log = (object) [
                                    'date' => $date,
                                    'views' => intval($data->views),
                                    'daily' => intval($data->daily),
                                    'oficial' => intval($data->daily_oficial),
                                    'lyric' => intval($data->daily_lyric),
                                    'tiktok' => intval($data->daily_tiktok),
                                    'lyric_video' => intval($data->daily_lyric_video),
                                    'compi' => intval($data->daily_compi),
                        ];
                        $views_detail[] = $views_log;
                    }
                    $temp->views_detail = json_encode($views_detail);
                    $temp->views = intval($data->views);
                    $temp->daily_views = intval($data->daily);
                    $temp->views_official = intval($data->views_official);
                    $temp->daily_views_official = intval($data->daily_oficial);
                    $temp->views_lyric = intval($data->views_lyric);
                    $temp->daily_views_lyric = intval($data->daily_lyric);
                    $temp->views_tiktok = intval($data->views_tiktok);
                    $temp->daily_views_tiktok = intval($data->daily_tiktok);
                    $temp->views_lyric_video = intval($data->views_lyric_video);
                    $temp->daily_lyric_video = intval($data->daily_lyric_video);
                    $temp->views_compi = intval($data->views_compi);
                    $temp->daily_compi = intval($data->daily_compi);
                    $temp->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                    $temp->save();
                } else {
                    $views_detail = array();
                    $views_log = (object) [
                                'date' => $date,
                                'views' => intval($data->views),
                                'daily' => 0,
                                'oficial' => 0,
                                'lyric' => 0,
                                'tiktok' => 0,
                                'lyric_video' => 0,
                                'compi' => 0,
                    ];
                    $views_detail[] = $views_log;
                    $campaignStatistics = new CampaignStatistics();
                    $campaignStatistics->campaign_name = $data->campaign_name;
                    $campaignStatistics->views = $data->views;
                    $campaignStatistics->daily_views = $data->daily;
                    $campaignStatistics->views_official = $data->views_official;
                    $campaignStatistics->daily_views_official = $data->daily_oficial;
                    $campaignStatistics->views_lyric = $data->views_lyric;
                    $campaignStatistics->daily_views_lyric = $data->daily_lyric;
                    $campaignStatistics->views_tiktok = $data->views_tiktok;
                    $campaignStatistics->daily_views_tiktok = $data->daily_tiktok;
                    $campaignStatistics->views_lyric_video = $data->views_lyric_video;
                    $campaignStatistics->daily_lyric_video = $data->daily_lyric_video;
                    $campaignStatistics->views_compi = $data->views_compi;
                    $campaignStatistics->daily_compi = $data->daily_compi;
                    $campaignStatistics->views_detail = json_encode($views_detail);
                    $campaignStatistics->create_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                    $campaignStatistics->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                    $campaignStatistics->save();
                }
            }
        }
    }

    public function campaignstatus(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.campaignstatus|request=' . json_encode($request->all()));
        $campaign = CampaignStatistics::find($request->id);
        if ($campaign) {
            $status = $campaign->status;
            if ($status == 2) {
                $status = 1;
            } else if ($status == 1) {
                $status = 0;
            } else if ($status == 0) {
                $status = 2;
            }
            $campaign->status = $status;
            $campaign->save();
            return array("status" => "success", "content" => "success", "newStatus" => $status);
        }
        return array("status" => "danger", "content" => "Not found campaign");
    }

    public function downloadListvideo(Request $request) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        try {
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=campaign_' . $request->id . '_' . date('Ymd') . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];
            $cam = CampaignStatistics::find($request->id);
            if ($cam) {
                $lists = Campaign::where('campaign_name', $cam->campaign_name)->get(['video_id']);
                foreach ($lists as $list) {
                    $list->video_id = "https://www.youtube.com/watch?v=" . $list->video_id;
                }
                $lists = $lists->toArray();
                $title = array('VideoID');
//            array_unshift($list, array_keys($title));
                array_unshift($lists, $title);

                $callback = function() use ($lists) {
                    $FH = fopen('php://output', 'w');
                    foreach ($lists as $row) {
                        fputcsv($FH, $row);
                    }
                    fclose($FH);
                };
                return response()->stream($callback, 200, $headers);
            }
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    public function getCheckList(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.getCheckList|request=' . json_encode($request->all()));
        $datas = CampaignStatistics::find($request->id);
        $checkList = json_decode($datas->check_lists);
        Log::info($datas->check_lists);
        $arrKey = ["lv", "fc", "al", "dl", "ic", "lp", "ec", "qt", "es", "sa"];
        $arrayName = ["Lyric videos",
            "Featured Content",
            "Add lyric videos to channel's playlist",
            "Description linking to original videos in 10 Videos",
            "Info Card (as described in the Campaign Push Paper)",
            "Lyric video gets spot in Legacy Campaign Playlist",
            "Engaging comment under lyric video",
            "Queue Top 1 or 2 Spots",
            "End Screens (as described in the Campaign Push Paper)",
            "Spot in Autoplaylist"];
        $html = "";
        for ($i = 0; $i < count($arrKey); $i++) {
            $checked = '';
            $key = $arrKey[$i];
            $value = $arrayName[$i];
            if ($checkList->$key == 1) {
                $checked = "checked";
            }
            $html .= "<div class='col-md-12'>";
            $html .= "  <div class='checkbox checkbox-primary'>";
            $html .= "      <input id='$key' class='$key' type='checkbox' name='$key' $checked>";
            $html .= "          <label for='$key'>";
            $html .= "              $value";
            $html .= "          </label>";
            $html .= "  </div>";
            $html .= "</div>";
        }
        return $html;
    }

    public function addCheckList(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TrackerController.addCheckList|request=' . json_encode($request->all()));
        $listcheck = '';

        $arrKeys = ["lv" => 0, "fc" => 0, "al" => 0, "dl" => 0, "ic" => 0, "lp" => 0, "ec" => 0, "qt" => 0, "es" => 0, "sa" => 0];
        $arrKey = ["lv", "fc", "al", "dl", "ic", "lp", "ec", "qt", "es", "sa"];
        foreach ($arrKeys as $key => $data) {
            $value = 0;
            if (isset($request->$key)) {
                $value = 1;
            }
            $listcheck .= "\"$key\":$value,";
        }
        $listcheck = rtrim($listcheck, ",");
        $listcheck = "{" . $listcheck . "}";
        $check = CampaignStatistics::where("id", $request->campaign_id)->first();
        $check->check_lists = $listcheck;
        $check->save();

        return array("status" => "success", "content" => "Success");
    }

    public function checkViewsByDate(Request $request) {

        $datas = Z1VideoClaim::where("user_name", $request->user_name)->get();
        Log::info(count($datas));
        $total = 0;
        foreach ($datas as $data) {
            if ($data->views_logs != null) {
                $viewLogs = json_decode($data->views_logs);
                foreach ($viewLogs as $viewLog) {
                    if ($viewLog->date = $request->date) {
                        $total += $request->views;
                        break;
                    }
                }
            }
        }
        return $total;
    }

}
