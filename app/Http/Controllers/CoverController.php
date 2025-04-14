<?php

namespace App\Http\Controllers;

use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class CoverController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.index|request=' . json_encode($request->all()));
        $status = 1;
        if (isset($request->status)) {
            $status = $request->status;
        }
        if ($status != 3) {
            $datas = CampaignStatistics::where("status", $status)->where("type", 2)->get();
        } else {
            $datas = CampaignStatistics::where("type", 2)->get();
        }

        $listCampaign = DB::select("select distinct campaign_name from campaign");
        $campaignType = DB::select("select sum(IF(campaign_type = 'premium', 1, 0)) as premium ,sum(IF(campaign_type = 'regular', 1, 0)) as regular,sum(IF(campaign_type = 'medium', 1, 0)) as medium from campaign_statistics where status =1 ");
        return view('components.cover', [
            'datas' => $datas,
            'listCampaign' => $listCampaign,
            'campaignType' => $campaignType,
            'request' => $request,
            'status' => $this->genStatusCampaign($request)
        ]);
    }

    public function detailcampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.detailcampaign');
        $datas = Campaign::where('campaign_name', $request->campaign_name)->get();
        return $datas;
    }

    public function addcampaign(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $i = 0;
        $array_videos = array();
        Log::info($user->user_name . '|CampaignController.addcampaign|request=' . json_encode($request->all()));
        if ($request->cam_id == null) {
            $checkCampaign = CampaignStatistics::where("campaign_name", $request->campaign_name)->first();
            if (!$checkCampaign) {
                $campaignStatistics = new CampaignStatistics();
                $campaignStatistics->type = 2;
                $campaignStatistics->campaign_name = $request->campaign_name;
                $campaignStatistics->campaign_type = $request->campaign_type;
                $campaignStatistics->views_detail = json_encode((object) array());
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
                        for ($t = 0; $t < 5; $t++) {
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
                        $campage->campaign_name = $request->campaign_name;
                        $campage->channel_name = $videoInfo["channelName"];
                        $campage->video_type = $request->video_type;
                        $campage->video_id = $video_id;
                        $campage->video_title = $videoInfo["title"];
                        $campage->views_detail = '[]';
                        $campage->status = $videoInfo["status"];
                        $campage->create_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                        $campage->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                        $campage->publish_date = $videoInfo["publish_date"];
//                        $campage->views = $videoInfo["view"];
//                        $campage->like = $videoInfo["like"];
//                        $campage->dislike = $videoInfo["dislike"];
//                        $campage->comment = $videoInfo["comment"];
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
                $checkStatistics->campaign_name = $request->campaign_name;
                $checkStatistics->campaign_start_date = $request->campaign_start_date;
                $checkStatistics->artists_channel = $request->artists_channel;
                $checkStatistics->genre = strtoupper($request->genre);
                $checkStatistics->label = $request->label;
                $checkStatistics->save();
            }
        }



        $listCampaign = DB::select("select distinct campaign_name from campaign");
        $option = "<option value=''>--Select--</option>";
        foreach ($listCampaign as $campaign) {
            $option .= "<option value='$campaign->campaign_name'>$campaign->campaign_name</option>";
        }
        return array("status" => "success", "content" => "Success $i videos", "option" => $option, "contentedit" => "Success");
    }

    public function getcampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getcampaign|request=' . json_encode($request->all()));
        $datas = Campaign::where("id", $request->id)->first(["id", "campaign_name", "views_detail"]);
        return $datas;
    }

    public function getcampaignstatistics(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getcampaignstatistics|request=' . json_encode($request->all()));
        $datas = CampaignStatistics::where("id", $request->id)->first(["id", "campaign_name", "views_detail"]);
        return $datas;
    }

    public function scanCampaign(Request $request) {
        if (isset($request->video_id)) {
            $datas = Campaign::where("video_id", $request->video_id)->where("status_use", 1)->get();
        } else {
            $datas = Campaign::where("status_use", 1)->whereIn("video_type", [4, 5, 6])->get();
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
                for ($t = 0; $t < 10; $t++) {
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
            error_log("Cover $date $i/$total $data->video_id   " . $info["status"] . ' ' . $info["view"]);
        }
        if (!isset($request->video_id)) {
            $campaignAll = DB::select("select campaign_name,sum(views) as views,SUM(IF(video_type = 1, views, 0)) as views_official,sum(daily_views) as daily,SUM(IF(video_type = 1, daily_views, 0)) as daily_oficial,SUM(IF(video_type = 2, daily_views, 0)) as daily_lyric,SUM(IF(video_type = 3, daily_views, 0)) as daily_tiktok from campaign group by campaign_name");
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
                            $detail->views = $data->views;
                            $detail->daily = $data->daily;
                            $detail->oficial = $data->daily_oficial;
                            $detail->lyric = $data->daily_lyric;
                            $detail->tiktok = $data->daily_tiktok;
                        }
                    }
                    if ($flag == 0) {
                        $views_log = (object) [
                                    'date' => $date,
                                    'views' => $data->views,
                                    'daily' => $data->daily,
                                    'oficial' => $data->daily_oficial,
                                    'lyric' => $data->daily_lyric,
                                    'tiktok' => $data->daily_tiktok,
                        ];
                        $views_detail[] = $views_log;
                    }
                    $temp->views_detail = json_encode($views_detail);
                    $temp->views = $data->views;
                    $temp->views_official = $data->views_official;
                    $temp->daily_views = $data->daily;
                    $temp->daily_views_official = $data->daily_oficial;
                    $temp->daily_views_lyric = $data->daily_lyric;
                    $temp->daily_views_tiktok = $data->daily_tiktok;
                    $temp->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                    $temp->save();
                } else {
                    $views_detail = array();
                    $views_log = (object) [
                                'date' => $date,
                                'views' => $data->views,
                                'daily' => 0,
                                'oficial' => 0,
                                'lyric' => 0,
                                'tiktok' => 0,
                    ];
                    $views_detail[] = $views_log;
                    $campaignStatistics = new CampaignStatistics();
                    $campaignStatistics->campaign_name = $data->campaign_name;
                    $campaignStatistics->views = $data->views;
                    $campaignStatistics->views_official = $data->views_official;
                    $campaignStatistics->daily_views = $data->daily;
                    $campaignStatistics->daily_views_official = $data->daily_oficial;
                    $campaignStatistics->daily_views_lyric = $data->daily_lyric;
                    $campaignStatistics->daily_views_tiktok = $data->daily_tiktok;
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
        Log::info($user->user_name . '|CampaignController.campaignstatus|request=' . json_encode($request->all()));
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
