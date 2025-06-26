<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Logger;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\WakeupHelper;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\AutoWakeupHappy;
use App\Http\Models\BrandManager;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\CrossPost;
use App\Http\Models\Notification;
use App\Http\Models\Notify;
use App\Http\Models\Strikes;
use App\Http\Models\Tasks;
use App\Http\Models\VideoDaily;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class DashboardController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $curr = time();
        Log::info($user->user_name . '|DashboardController->index');
        $condition = "user_name = '$user->user_code'";
        $curentDate = gmdate("Y-m-d", time() + (7 * 3600));
        $listErrorApi = [];
        $listWakeManual = [];
        if (in_array('20', explode(",", $user->role)) || in_array('21', explode(",", $user->role))) {
            $channels = AccountInfo::where("is_music_channel", 1);
//            $condition = "user_name in (select distinct user_code from users where user_code  not in ('hoadev_1492490931', 'truongpv_1515486846', 'star_effect_1601628590', 'victorteam_1533526611')) ";
            $condition = "1=1";
            $listWakeManual = AccountInfo::where("wakeup_type", 1)->whereIn("is_music_channel", [1, 2])->get();
            $promosCheck = Campaign::whereIn("status_confirm", [1, 2])->orderBy("id", "desc");
            $bitlys = \App\Http\Models\Bitly::where("status", 1);
        } else {
            $channels = AccountInfo::where("is_music_channel", 1)->where("user_name", $user->user_code);
            $listWakeManual = AccountInfo::where("user_name", $user->user_code)->whereIn("is_music_channel", [1, 2])->where("wakeup_type", 1)->get();
            $promosCheck = Campaign::whereIn("status_confirm", [1, 2])->where("username", $user->user_name)->orderBy("id", "desc");
            $bitlys = \App\Http\Models\Bitly::where("status", 1)->where("username", $user->user_name);
        }
        $generalDataUsser = DB::select("select count(*) as total_channel,COALESCE(sum(view_count),0) as total_view,COALESCE(sum(subscriber_count),0) as total_sub,
        COALESCE(sum(video_count),0) as total_video,COALESCE(sum(increasing),0) as total_increasing, max(update_time) as date from accountinfo
        where $condition and is_music_channel in ( 1,2)");

        $wakeTotals = DB::select("select username, count(*) as total from tasks where date = '$curentDate' group by username");

        $listMusicAccount = DB::select("select a.user_name,count(a.user_name) as channel, sum(a.view_count) as view,sum(a.subscriber_count) as sub,sum(a.video_count) as video,sum(a.increasing) as increasing from accountinfo a
                                            where  1=1 
                                            and a.status = 1 and a.del_status = 0 and is_music_channel =1
                                            group by a.user_name order by sum(a.increasing) desc");
        $userExcept = array("hoadev_1492490931", "truongpv_1515486846", "star_effect_1601628590", "victorteam_1533526611");

        foreach ($listMusicAccount as $index => $data) {
            if (in_array($data->user_name, $userExcept)) {
                unset($listMusicAccount[$index]);
                continue;
            }
            $pos = strripos($data->user_name, '_');
            $temp = substr($data->user_name, 0, $pos);
            $data->total_wake = 0;
            $data->user_name = str_replace(array("t5musickpi", "music", "lc94"), array("", "", ""), $temp);
            foreach ($wakeTotals as $task) {
                if ($task->username == $temp) {
                    $data->total_wake = $task->total;
                    break;
                }
            }
        }
        //danh sách kênh sắp xếp theo view tăng
//        $listChannelTop = DB::select("select user_name,chanel_id as channel_id,chanel_name as channel_name,view_count as views,subscriber_count as subs,increasing from accountinfo  where user_name in (select distinct user_code from users where role like '%11%' or role like '%20%') and status = 1 and del_status = 0 and is_music_channel = 1 order by increasing desc limit 20");
//        foreach ($listChannelTop as $data) {
//            $pos = strripos($data->user_name, '_');
//            $temp = substr($data->user_name, 0, $pos);
//            $data->user_name = str_replace(array("t5musickpi", "music", "lc94"), array("", "", ""), $temp);
//        }
        $date = gmdate("D", time() + (7 * 3600) - 86400);
//        Log::info($date);
//        $channels = AccountInfo::where("is_music_channel", 1)->orderBy("user_name")->get(["id", "user_name", "chanel_id", "chanel_name", "count_video", "limit", "limit_weekend", "channel_type", "channel_genre", "channel_clickup", "status_upload"]);
        $queries = [];
//        $queries['sort'] = 'user_name';
//        $queries['order'] = 'asc';

        if (isset($request->channel_type) && $request->channel_type != '-1') {
            $channels = $channels->where('channel_type', $request->channel_type);
            $queries['channel_type'] = $request->channel_type;
        }

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
//            $request['sort'] = 'user_name';
//            $request['order'] = 'asc';
//            $queries['sort'] = 'user_name';
//            $queries['order'] = 'asc';
        }
        $channels = $channels->sortable()->paginate(100)->appends($queries);
//        Log:info(DB::getQueryLog());
        $fuckLabelArtists = DB::select("select * from fuck_label_artists");
        $countError = 0;

        if (in_array('20', explode(",", $user->role))) {
            $wakeupsTask = Tasks::where("date", $curentDate)->where("type", 2)->orderBy("id", "desc")->get();
//            $wakeupsTask = DB::select("select username,channel_id,channel_name,date,max(create_time) as create_time_last,content from tasks where type =2 and date = '$curentDate' group by username,channel_id,channel_name,date,content order by create_time_last desc ");
            $mixs = Tasks::where("username", $user->user_name)->where("create_time", ">=", time() - 7 * 86400)->where("type", 3)->orderBy("id", "desc")->get();
        } else {
            $wakeupsTask = Tasks::where("username", $user->user_name)->where("date", $curentDate)->where("type", 2)->orderBy("id", "desc")->get();
            $mixs = Tasks::where("username", $user->user_name)->where("create_time", ">=", time() - 7 * 86400)->where("type", 3)->orderBy("id", "desc")->get();
        }



        //đếm số lượng wakeup theo kênh theo tháng
        //total wakeup
        $timeGet = time() - 3 * 86400;
        $firstDayOfTheMonth = strtotime(date('Y-m-1', $timeGet));
        $lastDayOfTheMonth = strtotime(date('Y-m-t', $timeGet));
        $totalWakeupTasks = DB::select("select channel_id,count(*) as total from tasks where type = 2 and create_time >= $firstDayOfTheMonth and create_time < $lastDayOfTheMonth group by  channel_id ");
        $autoWakeup = AutoWakeupHappy::whereIn("status", [0, 1])->orderBy("id", "desc")->get();
        //lấy số lượng video upload hàng ngày tù bảng video_daily
        $dateGet = gmdate("Ymd", time() + (7 * 3600) - 86400);
        $uploaded = DB::select("SELECT date,channel_id, count(*) as video_daily FROM `video_daily` WHERE  `date` = '$dateGet' group by date,channel_id");

        foreach ($channels as $index => $channel) {
            if (in_array($channel->user_name, $userExcept)) {
                unset($channels[$index]);
                continue;
            }
            $channel->time = "N/A";
            $channel->count_daily = 0;
            $pos = strripos($channel->user_name, '_');
            $temp = substr($channel->user_name, 0, $pos);
            $channel->user_name = str_replace(array("t5musickpi", "music", "lc94", "doan"), array("", "", "", "ket"), $temp);
            if ($date == "Sat" || $date == "Sun") {
                $channel->limit = $channel->limit_weekend;
//                Log::info($channel->limit);
            }
            if ($channel->count_video != null) {
                $count_video = json_decode($channel->count_video);
                $last = $count_video[count($count_video) - 1];
                $time = $last->time;
                $count_daily = $last->count_daily;
                $count = $last->count;
                $channel->time = $time;
                $channel->count = $count;
                $channel->count_daily = $count_daily;
            }
            $channel->checkUpload = "";
            $flag = 0;
            if ($channel->last_upload_label != null && $channel->last_upload_label != "") {
                $lastUploadLabels = json_decode($channel->last_upload_label);
                foreach ($fuckLabelArtists as $fuck) {
                    foreach ($lastUploadLabels as $lastUploadLabel) {
                        if (Utils::containString($lastUploadLabel->artist, $fuck->name) || Utils::containString($lastUploadLabel->label, $fuck->name)) {
                            $channel->checkUpload = "check-upload";
                            $flag = 1;
                            break;
                        }
                    }
                }
                if ($flag == 1) {
                    $countError++;
                }
            }

            //xử lý wakeup happy task
            $channel->last_wake_time = "Not done yet";
            $channel->next_wake_time = null;
            $channel->check_wake = "check-upload";
            $channel->wake_percent = 0;
            $channel->wake_total = 0;
            foreach ($wakeupsTask as $wakeup) {
                if ($channel->chanel_id == $wakeup->channel_id) {
                    $channel->last_wake_time = Utils::calcTimeText($wakeup->create_time);
                    $channel->check_time = $wakeup->check_time;
                    $channel->playlist_wake_id = $wakeup->content;
                    if ($wakeup->create_time + (6 * 3600) >= time()) {
                        $channel->check_wake = "";
                    }

                    if ($wakeup->wakeup_result != null) {
                        $wakeupResults = json_decode($wakeup->wakeup_result);
//                        Log::info(json_encode($wakeupResults));
//                        $wakeupResults = explode("@;@", $wakeup->wakeup_result);
                        $total = count($wakeupResults);
//                        Log::info($total);
                        $success = 0;
                        foreach ($wakeupResults as $result) {
                            if ($result != null) {
                                if ($result->wakeup == 1) {
                                    $success++;
                                }
                            }
                        }
                        if ($total == 0) {
                            $channel->wake_percent = 0;
                        } else {
                            $channel->wake_percent = round($success / $total * 100, 0);
                        }
                        $channel->wake_video_list = $wakeupResults;
                        $channel->task_id = $wakeup->id;
                    } else {
                        if ($wakeup->job_check_id != null) {
                            $channel->wake_percent = 0;
                        }
                    }
                    break;
                }
            }
            foreach ($totalWakeupTasks as $totaWake) {
                if ($channel->chanel_id == $totaWake->channel_id) {
                    $channel->wake_total = $totaWake->total;
                    break;
                }
            }
            //xử lý auto wakeup happy
            foreach ($autoWakeup as $wakeup) {
                if ($channel->chanel_id == $wakeup->channel_id) {
                    $channel->last_auto_wake_time = Utils::calcTimeText(strtotime($wakeup->last_excute_time_text) - (7 * 3600));
                    $channel->next_auto_wake_time = gmdate("m/d/Y H:i:s", $wakeup->next_time_run + (7 * 3600));
                    break;
                }
            }

            //đếm số lượng video upload hàng ngày của từng kênh
            foreach ($uploaded as $up) {
                if ($up->channel_id == $channel->chanel_id) {
                    $channel->video_daily = $up->video_daily;
                }
            }
        }

        $tasks = Tasks::where("username", $user->user_name)->where("date", $curentDate)->where("type", 1)->get();
//        Log::info(json_encode($tasks));
//        $listTasks = []; ["playlist" => "UPDATED PLAYLIST", "comment" => "PINED COMMENT", "cards" => "CARDS", "screen" => "ENDSCREEN"];
        $listTasks[] = (object) ["code" => "playlist", "name" => "UPDATED PLAYLIST", "status" => "", "note" => "(Incomplete)"];
        $listTasks[] = (object) ["code" => "comment", "name" => "PINNED COMMENT", "status" => "", "note" => "(Incomplete)"];
        $listTasks[] = (object) ["code" => "cards", "name" => "CARDS", "status" => "", "note" => "(Incomplete)"];
        $listTasks[] = (object) ["code" => "screen", "name" => "ENDSCREEN", "status" => "", "note" => "(Incomplete)"];
        foreach ($listTasks as $listTask) {
            foreach ($tasks as $task) {
                if ($listTask->code == $task->content) {
                    $listTask->status = "disabled checked";
                    $listTask->note = "";
                    break;
                }
            }
        }


//        Log::info(json_encode($wakeups));
        $current = gmdate("m/d/Y H:i:s", time() + (7 * 3600));
        $claims = CampaignStatistics::where("type", 2)->whereIn("status", [1, 4])->get();
        $promos = CampaignStatistics::whereIn("status", [1, 2])->orderBy("genre")->orderBy("wake_position")->get();

        $taskLyrics = Tasks::whereIn("type", [5, 6, 7, 8, 9, 10])->whereIn("task_status", [0, 1])->orderBy("id", "desc")->get();
        foreach ($taskLyrics as $taskLyric) {
            if ($taskLyric->type == 9) {
                $songName = "";
                $artists = "";
                $camIds = explode(",", $taskLyric->campaign_id);
                foreach ($promos as $pro) {
                    if (in_array($pro->id, $camIds)) {
                        $songName .= $pro->song_name . "<br>";
                        $artists .= $pro->artist . "<br>";
                    }
                }
            }
            foreach ($promos as $pro) {
                if ($taskLyric->type == 9) {
                    if (Utils::containString($taskLyric->campaign_id, $pro->id)) {
                        $taskLyric->song_name = $songName;
                        $taskLyric->artist = $artists;
                        $taskLyric->audio_url = $pro->audio_url;
                        $taskLyric->lyric_url = $pro->lyric_url;
                        $taskLyric->campaign_start_date = $pro->campaign_start_date;
                        $taskLyric->cam_id = $pro->id;
                        $taskLyric->lyrics = $pro->lyrics;
                        $taskLyric->deezer_artist_id = $pro->deezer_artist_id;
                        $taskLyric->artists_channel = $pro->artists_channel;
                        $taskLyric->artists_social = $pro->artists_social;
                        $taskLyric->official_video = $pro->official_video;
                        $taskLyric->bitly_url = $pro->bitly_url;
                    }
                } else {
                    if ($taskLyric->campaign_id == $pro->id) {
                        $taskLyric->artist = $pro->artist;
                        $taskLyric->song_name = $pro->song_name;
                        $taskLyric->audio_url = $pro->audio_url;
                        $taskLyric->lyric_url = $pro->lyric_url;
                        $taskLyric->campaign_start_date = $pro->campaign_start_date;
                        $taskLyric->cam_id = $pro->id;
                        $taskLyric->lyrics = $pro->lyrics;
                        $taskLyric->deezer_artist_id = $pro->deezer_artist_id;
                        $taskLyric->artists_channel = $pro->artists_channel;
                        $taskLyric->artists_social = $pro->artists_social;
                        $taskLyric->official_video = $pro->official_video;
                        $taskLyric->bitly_url = $pro->bitly_url;
                    }
                }

//                Log::info(" $taskLyric->campaign_id -  $pro->id ");
//                if ($taskLyric->campaign_id == $pro->id || Utils::containString($taskLyric->campaign_id, $pro->id)) {
////                if ($taskLyric->campaign_id == $pro->id) {
//                    if ($taskLyric->type == 9) {
//                        $taskLyric->song_name = $songName;
//                        $taskLyric->artist = $artists;
//                    } else {
//                        $taskLyric->artist = $pro->artist;
//                        $taskLyric->song_name = $pro->song_name;
//                        $taskLyric->audio_url = $pro->audio_url;
//                        $taskLyric->lyric_url = $pro->lyric_url;
//                        $taskLyric->campaign_start_date = $pro->campaign_start_date;
//                        $taskLyric->cam_id = $pro->id;
//                        $taskLyric->lyrics = $pro->lyrics;
//                        $taskLyric->deezer_artist_id = $pro->deezer_artist_id;
//                        $taskLyric->artists_channel = $pro->artists_channel;
//                        $taskLyric->artists_social = $pro->artists_social;
//                        $taskLyric->official_video = $pro->official_video;
//                        $taskLyric->bitly_url = $pro->bitly_url;
//                    }
//                }
            }
        }



        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            $request['sort'] = 'id';
            $request['direction'] = 'desc';
            $queries['sort'] = 'id';
            $queries['direction'] = 'desc';
        }
        $promosCheck = $promosCheck->sortable()->paginate(3000)->appends($queries);
        $campaignStatic = CampaignStatistics::whereIn("type", [1, 2, 5])->whereIn("status", [1, 2, 4])->get();
        foreach ($promosCheck as $pro) {
            $pro->username_short = str_replace("music", "", $pro->username);
            $pro->distributor = "";
            if ($pro->is_match_claim) {
                $pro->match_claim = 'MATCHED';
            } else {
                $pro->match_claim = "NOT_MATCH";
            }
            if ($pro->log_claim == null || Utils::containString($pro->log_claim, "VIDEO_COPYRIGHT")) {
                $pro->log_claim = "";
            }
            $pro->alert_change_title = "";
            if ($pro->is_bommix == 1) {
                $pro->is_bommix_text = "BOOMMIX";
                if (time() - $pro->publish_date > (7 * 86400)) {
                    $pro->alert_change_title = "color-red";
                }
            } else {
                $pro->is_bommix_text = "NOT_A_BOOMMIX ";
            }
            if ($pro->is_changed_title == 1) {
                $pro->is_changed_title_text = "CHANGED";
            } else {
                $pro->is_changed_title_text = "NOT_CHANGE";
            }
            if (Utils::containString($pro->campaign_name, "[COVER]")) {
                $pro->cam_type = "COVER";
            } else if (Utils::containString($pro->campaign_name, "[ORIGINAL]")) {
                $pro->cam_type = "ORIGINAL";
            } else if (Utils::containString($pro->campaign_name, "[ELECTRONIC]")) {
                $pro->cam_type = "ELECTRONIC";
            } else {
                foreach ($campaignStatic as $c) {
                    if ($pro->campaign_id == $c->id) {
                        if ($c->type == 1) {
                            $pro->cam_type = "PROMO";
                        } else if ($c->type == 5) {
                            $pro->cam_type = "SUBMISSION";
                        } else if ($c->type == 2) {
                            $pro->cam_type = "CLAIM";
                        }

                        break;
                    }
                }
            }
            foreach ($campaignStatic as $c) {
                if ($pro->campaign_id == $c->id) {
                    $pro->distributor = $c->distributor;
                    break;
                }
            }
            if ($pro->publish_date != 0) {
                $pro->public = Utils::calcTimeText($pro->publish_date);
            } else {
                $pro->public = "N/A";
            }
        }

//        $promosWakes = DB::select("select * from campaign where " . time() . " - UNIX_TIMESTAMP(CONVERT_TZ(STR_TO_DATE(create_time,'%Y/%m/%d %H:%i:%s'),'+07:00', 'SYSTEM')) < 172800 and username = '$user->user_name'");
        $channelConfirmSync = AccountInfo::whereIn("is_sync", [2, 3])->get();
        $channelConfirmSyncCount = DB::select("select user_name, is_sync,count(*) as total from accountinfo where is_sync in (2,3) group by user_name, is_sync");
        $arrUser = [];
        foreach ($channelConfirmSyncCount as $syncCount) {
            if (!in_array($syncCount->user_name, $arrUser)) {
                $arrUser[] = $syncCount->user_name;
            }
        }
        $resultSync = [];
        foreach ($arrUser as $us) {
            $tmp = ["user_name" => $us, "admin_check" => 0, "user_check" => 0];
            foreach ($channelConfirmSyncCount as $syncCount) {
                if ($us == $syncCount->user_name) {
                    if ($syncCount->is_sync == 2) {
                        $tmp["admin_check"] = $syncCount->total;
                    }
                    if ($syncCount->is_sync == 3) {
                        $tmp["user_check"] = $syncCount->total;
                    }
                }
            }
            $resultSync[] = (object) $tmp;
        }

//        Log:info(DB::getQueryLog());
//        Log::info((time()- $curr));
        return view('components.dashboard', [
//            'statisticsUser' => $statisticsUser,
//            'groupChart' => $groupChart,
//            'generalDataAdmin' => $generalDataAdmin,
//            'listChannelTop' => $listChannelTop,
            'limitSelectbox' => $this->genLimit($request),
            'fuckLabelArtists' => $fuckLabelArtists,
            'generalDataUser' => $generalDataUsser,
            'listMusicAccount' => $listMusicAccount,
            'channels' => $channels,
            'countError' => $countError,
            'listTasks' => $listTasks,
            'wakeups' => $wakeupsTask,
            'mixs' => $mixs,
            'current' => $current,
            'autoWakeup' => $autoWakeup,
            'claims' => $claims,
            'promos' => $promos,
            'promosCheck' => $promosCheck,
//            'promosWakes' => $promosWakes,
            'listErrorApi' => $listErrorApi,
            'channelConfirmSync' => $channelConfirmSync,
            'channelConfirmSyncCount' => $resultSync,
            'months' => $this->loadMonth(),
            'channelType' => $this->loadChannelType($request),
            'taskLyrics' => $taskLyrics,
            'listWakeManual' => $listWakeManual,
            'listUser' => $this->genListUserForMoveChannel($user, $request, 2, 1),
        ]);
    }

    public function autochannel(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController->autochannel');
        $current = gmdate("m/d/Y H:i:s", time() + (7 * 3600));
        $curentDate = gmdate("d-m-Y", time() + (7 * 3600));
        $curr = time();
        $dateUpload = time() - 86400 * 2;
        $condition = "user_name = '$user->user_code'";
        if (in_array('20', explode(",", $user->role)) || in_array('21', explode(",", $user->role))) {
            $uploads = DB::select("select a.*,max(b.date) as last_upload from (select user_name,chanel_id,chanel_name,subscriber_count as subs,view_count as views from accountinfo accountinfo where is_music_channel in (1,2) and is_sync =1 and upload_alert=1 ) a 
                                left join (select channel_id,video_id,date from video_daily) b
                                on a.chanel_id =b.channel_id
                                group by a.chanel_id having max(b.date) < " . gmdate("Ymd", $dateUpload) . " or max(b.date) is null order by max(b.date)");
            $channels = AccountInfo::whereIn("is_music_channel", [2, 1])->where("is_sync", 1)->where("del_status", 0);
//            $condition = "user_name in (select distinct user_code from users where user_code  not in ('hoadev_1492490931', 'truongpv_1515486846', 'star_effect_1601628590', 'victorteam_1533526611')) ";
            $condition = "1=1";
            $crossPosts = CrossPost::whereRaw("1=1")->where("del_status", 0);
            $groupChannels = \App\Http\Models\GroupChannel::orderBy('id', 'desc')->get();
        } else {
            $channels = AccountInfo::whereIn("is_music_channel", [2, 1])->where("user_name", $user->user_code)->where("is_sync", 1)->where("del_status", 0);
            $crossPosts = CrossPost::where("user_owner", $user->user_name)->where("del_status", 0);
            $uploads = DB::select("select a.*,max(b.date) as last_upload from (select user_name,chanel_id,chanel_name,subscriber_count as subs,view_count as views from accountinfo accountinfo where is_music_channel in (1,2) and is_sync =1 and upload_alert=1 and $condition) a 
                                left join (select channel_id,video_id,date from video_daily) b
                                on a.chanel_id =b.channel_id
                                group by a.chanel_id having max(b.date) < " . gmdate("Ymd", $dateUpload) . " or max(b.date) is null order by max(b.date)");
            $groupChannels = \App\Http\Models\GroupChannel::where("user_name", $user->user_name)->orderBy('id', 'desc')->get();
        }
        $autoWakeupsTask = Tasks::where("date", $curentDate)->where("type", 4)->orderBy("id", "desc")->get();
        $generalDataUsser = DB::select("select count(*) as total_channel,COALESCE(sum(view_count),0) as total_view,COALESCE(sum(subscriber_count),0) as total_sub,
        COALESCE(sum(video_count),0) as total_video,COALESCE(sum(increasing),0) as total_increasing, max(update_time) as date from accountinfo
        where $condition and is_music_channel = 2");

//        }

        $queries = [];

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            $request['sort'] = 'sub_percent';
            $request['direction'] = 'desc';
            $queries['sort'] = 'sub_percent';
            $queries['direction'] = 'desc';
        }
        $channels = $channels->sortable()->paginate(5000)->appends($queries);
//        Log:info(DB::getQueryLog());
        $fuckLabelArtists = DB::select("select * from fuck_label_artists");
        $countError = 0;
        $userExcept = array("truongpv_1515486846", "star_effect_1601628590", "victorteam_1533526611");
        //ngày cuối tuần thì sẽ lấy limit khác
        $date = gmdate("D", time() + (7 * 3600) - 86400);
        //lấy số lượng video upload hàng ngày tù bảng video_daily
        $dateGet = gmdate("Ymd", time() + (7 * 3600) - 86400);
        $uploaded = DB::select("SELECT date,channel_id, count(*) as video_daily FROM `video_daily` WHERE  `date` = '$dateGet' group by date,channel_id");
        $autoWakeup = DB::select("select id,status,channel_id,job_id,last_excute_time_text,log,next_time_run from auto_wakeup_happy where status in (0,1,4,6,7,8) order by id desc");
        //lấy những kênh bị strike
        $strikes = Strikes::whereRaw("$curr - date < 90 * 86400")->get();
        $arrStrike = [];
        foreach ($strikes as $strike) {
            $arrStrike[] = $strike->channel_id;
        }
        $uniques = array_unique($arrStrike);
        $resultAllStrikes = [];
        foreach ($uniques as $temp) {
            $resultChannelStrike = [];
            foreach ($strikes as $strike) {
                if ($temp == $strike->channel_id) {
                    $temp2 = (object) [
                                "id" => $strike->id,
                                "date_text" => $strike->date_text,
                                "strike_name" => "$strike->strike on $strike->date_text",
                                "type" => $strike->type,
                                "opacity" => $strike->status == 0 ? 1 : 0.5
                    ];
                    $resultChannelStrike[] = $temp2;
                }
            }
            $temp3 = (object) [
                        "channel_id" => $temp,
                        "data" => $resultChannelStrike
            ];
            $resultAllStrikes[] = $temp3;
        }

        foreach ($channels as $index => $channel) {
            if (in_array($channel->user_name, $userExcept)) {
                unset($channels[$index]);
                continue;
            }
            $channel->time = "N/A";
            $channel->count_daily = 0;
            $pos = strripos($channel->user_name, '_');
            $temp = substr($channel->user_name, 0, $pos);
            $channel->user_name = str_replace(array("t5musickpi", "music", "lc94", "doan"), array("", "", "", "ket"), $temp);
            if ($date == "Sat" || $date == "Sun") {
                $channel->limit = $channel->limit_weekend;
//                Log::info($channel->limit);
            }
            if ($channel->count_video != null) {
                $count_video = json_decode($channel->count_video);
                $last = $count_video[count($count_video) - 1];
                $time = $last->time;
                $count_daily = $last->count_daily;
                $count = $last->count;
                $channel->time = $time;
                $channel->count = $count;
                $channel->count_daily = $count_daily;
            }
            foreach ($uploaded as $up) {
                if ($up->channel_id == $channel->chanel_id) {
                    $channel->video_daily = $up->video_daily;
                }
            }
            $channel->checkUpload = "";
            $flag = 0;
            if ($channel->last_upload_label != null && $channel->last_upload_label != "") {
                $lastUploadLabels = json_decode($channel->last_upload_label);
                foreach ($fuckLabelArtists as $fuck) {
                    foreach ($lastUploadLabels as $lastUploadLabel) {
                        if (Utils::containString($lastUploadLabel->artist, $fuck->name) || Utils::containString($lastUploadLabel->label, $fuck->name)) {
                            $channel->checkUpload = "check-upload";
                            $flag = 1;
                            break;
                        }
                    }
                }
                if ($flag == 1) {
                    $countError++;
                }
            }

            //check xem channel đã tạo lênh autowakeup chưa
            $channel->autowake = 0;
            $channel->wake_percent = 0;
            $channel->wake_error = "";
            $channel->wake_job_id = "";
            $channel->wake_status_text = "NOT WAKEUP";
            foreach ($autoWakeup as $wake) {
                $channel->wake_job_id = "$wake->job_id";
                if ($wake->channel_id == $channel->chanel_id) {
                    $channel->autowake = 1;
                    $channel->wake_id = $wake->id;
                    $channel->wake_status = $wake->status;
                    $channel->wake_log = $wake->log;
                    $channel->next_time_run = $wake->next_time_run == 1 ? time() : $wake->next_time_run;
                    $channel->wake_last_excute = $wake->last_excute_time_text;
                    if ($wake->status == 0) {
                        $channel->wake_status_text = "WAITING";
                    } elseif ($wake->status == 1) {
                        $channel->wake_status_text = "RUNNING";
                    } elseif ($wake->status == 4) {
                        $channel->wake_status_text = "ERROR";
                    } elseif ($wake->status == 6) {
                        $channel->wake_status_text = "STOP BY USER";
                    } elseif ($wake->status == 7) {
                        $channel->wake_status_text = "LIST VIDEOS < 5";
                    } elseif ($wake->status == 8) {
                        $channel->wake_status_text = "NOT FOUND GMAIL";
                    } else {
                        $channel->wake_status_text = "NOT WAKEUP";
                    }
                    break;
                }
            }

            //check % hieu qua cua autowakeup
            foreach ($autoWakeupsTask as $autoW) {
                if ($autoW->channel_id == $channel->chanel_id && $channel->wake_percent == 0) {
                    $channel->wake_percent = $autoW->wakeup_percent;
                    break;
                }
            }

            //check kênh bị strikes
            $channel->strike_data = [];
            foreach ($resultAllStrikes as $strike) {
                if ($strike->channel_id == $channel->chanel_id) {
                    $channel->strike_data = $strike->data;
                    break;
                }
            }

            //check kênh upload
            foreach ($uploads as $upload) {
                $channel->last_time_upload = "";
                if ($channel->chanel_id == $upload->chanel_id) {
                    if ($upload->last_upload == null) {
                        $channel->last_time_upload = "NOT UPLOAD";
                    } else {
                        $lastUploadTime = strtotime($upload->last_upload);
                        $channel->last_time_upload = round((time() - $lastUploadTime) / 86400 - 1) . ' days ago';
                    }
                    break;
                }
            }
        }


        $queries2 = [];
        $limit = 50;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries2['limit'] = $request->limit;
            }
        }
        if (isset($request->sort)) {
            $queries2['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries2['order'] = $request->order;
            }
        } else {
            $request['sort'] = 'views';
            $request['order'] = 'desc';
            $queries2['sort'] = 'views';
            $queries2['order'] = 'desc';
        }
        $currFrom = gmdate("Y-m-d", time() + (7 * 3600) - (30 * 86400));
        $currTo = gmdate("Y-m-d", time() + (7 * 3600));
//        $crossPosts;
        $crossPosts = $crossPosts->where("status", 1)->whereNotNull("video_id")->sortable()->paginate($limit)->appends($queries2);
//        Log:info(DB::getQueryLog());
//        Log::info((time() - $curr));
        return view('components.channelauto', [
            'fuckLabelArtists' => $fuckLabelArtists,
            'generalDataUser' => $generalDataUsser,
            'channels' => $channels,
            'countError' => $countError,
            'current' => $current,
            'crossPosts' => $crossPosts,
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            'currFrom' => $currFrom,
            'currTo' => $currTo,
            'groupChannels' => $groupChannels,
        ]);
    }

    public function getlistvideo(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.getlistvideo|request=' . json_encode($request->all()));
        $channel = AccountInfo::where("chanel_id", $request->link)->first();
        $lastUpload = array();
        $result = array();
        if ($channel) {
            $lastUpload = $channel->last_upload_label;
            if (isset($lastUpload)) {
                $lastUpload = json_decode($lastUpload);
            }
            $fuckLabelArtists = DB::select("select * from fuck_label_artists");
            foreach ($lastUpload as $upload) {
                Log::info($upload->video_id);
                $object = [
                    "video_id" => $upload->video_id,
                    "title" => $upload->title,
                    "label" => $upload->label,
                    "artist" => $upload->artist,
                    "colorLabel" => '',
                    "colorArtist" => '',
                ];


                foreach ($fuckLabelArtists as $fuck) {
                    if (Utils::containString($upload->label, $fuck->name)) {
                        $object['colorLabel'] = "color-red";
                    }
                    if (Utils::containString($upload->artist, $fuck->name)) {
                        $object['colorArtist'] = "color-red";
                    }
                }
                $result[] = (object) $object;
            }
        }
        return $result;
//        return YoutubeHelper::getListVideoFromPlaylistV2(substr_replace($request->link, "UU", 0, 2), 1, $request->daily);
//        $datas = YoutubeHelper::getPlaylist(substr_replace($request->link, "UU", 0, 2), $request->daily);
//        $listVideo = $datas['list_video_id'];
//        $listArtists = array();
//        $listLabel = array();
//        $listColorArtist = array();
//        $listColorLabel = array();
//        foreach ($listVideo as $data) {
//            $videoInfo = YoutubeHelper::getVideoInfoV2($data);
//            if ($videoInfo["status"] == 0) {
//                for ($t = 0; $t < 5; $t++) {
//                    error_log("scanchannelmusiclicences Retry $data");
//                    $videoInfo = YoutubeHelper::getVideoInfoV2($data);
//                    error_log(json_encode($videoInfo));
//                    if ($videoInfo["status"] == 1) {
//                        break;
//                    }
//                }
//            }
//            $fuckLabelArtists = DB::select("select * from fuck_label_artists");
//            $colorArtist = "";
//            $colorLabel = "";
//            foreach ($fuckLabelArtists as $fuck) {
//                if (Utils::containString($videoInfo["license"], $fuck->name)) {
//                    $colorLabel = "color-red";
//                }
//                if (Utils::containString($videoInfo["artists"], $fuck->name)) {
//                    $colorArtist = "color-red";
//                }
//            }
//            array_push($listArtists, $videoInfo["artists"]);
//            array_push($listLabel, $videoInfo["license"]);
//            array_push($listColorArtist, $colorArtist);
//            array_push($listColorLabel, $colorLabel);
//        }
//        $datas['artist'] = $listArtists;
//        $datas['label'] = $listLabel;
//        $datas['colorArtist'] = $listColorArtist;
//        $datas['colorLabel'] = $listColorLabel;
//        return $datas;
    }

    public function getvideochart(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.getvideochart|request=' . json_encode($request->all()));
        $datas = AccountInfo::where("id", $request->id)->first(["id", "chanel_id", "chanel_name", "count_video", "view_detail", "limit", "limit_weekend"]);
        $uploadeds = DB::select("SELECT date,channel_id, count(*) as video_daily FROM `video_daily` WHERE  `channel_id` = '$datas->chanel_id' group by date,channel_id");

        $countVideos = json_decode($datas->count_video);
        foreach ($countVideos as $countVideo) {
            $time = strtotime(str_replace("-", "/", $countVideo->time));
            foreach ($uploadeds as $upload) {
                $timeM = strtotime($upload->date);
                $countVideo->video_daily = 0;
                if ($time == $timeM) {
                    $countVideo->video_daily = $upload->video_daily;
                    break;
                }
            }
        }
        $datas->count_video = json_encode($countVideos);
//        $uploadeds = array_slice($uploadeds, -15);
//        $videos = [];
//        foreach($uploadeds as $uploaded){
//            $video = (object)[
//                "time"=>$uploaded->date,
//                "video_daily"=>$uploaded->video_daily
//            ];
//            $videos[] = $video;
//        }
//        $datas->count_video = json_encode($videos);
        return $datas;
    }

    public function getChartTotalDailyViews(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.getChartTotalDailyViews|request=' . json_encode($request->all()));
        $startDate = date("Ymd", strtotime("-28 day"));
        $endDate = date("Ymd", time());
        if (isset($request->start)) {
            $startDate = $request->start;
        }
        if (isset($request->end)) {
            $endDate = $request->end;
        }

        $datas = DB::select("select date,sum(views) as views,count(distinct(channel_id)) as channels from  athena_data_sync where date >= '$startDate' and date <= '$endDate' group by date");
        $datasPromos = DB::select("select date,sum(views) as views from athena_promo_sync where date >= '$startDate' and date <= '$endDate' group by date");
        $pros = DB::select("select date,sum(views) as views from athena_promo_sync where date >= '$startDate' and date <= '$endDate' and video_id in (select video_id from campaign where campaign_id in (select id from campaign_statistics where status =1 and type = 1) and status_confirm = 3) group by date order by date");
        $revs = DB::select("select date,sum(views) as views from athena_promo_sync where date >= '$startDate' and date <= '$endDate' and video_id in (select video_id from campaign where campaign_id in (select id from campaign_statistics where status =1 and type = 4) and status_confirm = 3) group by date order by date");
        $claims = DB::select("select date,sum(views) as views from athena_promo_sync where date >= '$startDate' and date <= '$endDate' and video_id in (select video_id from campaign where campaign_id in (select id from campaign_statistics where status =1 and type = 2) and status_confirm = 3) group by date order by date;");
        $total = 0;
        $totalPromo = 0;
        foreach ($datas as $data) {
            $detail = [];
            $total += $data->views;
            foreach ($datasPromos as $promo) {
                if ($data->date == $promo->date && $data->views != 0) {
                    $detail[] = "Monetize:" . number_format($promo->views, 0, '.', ',') . " - " . round($promo->views / $data->views * 100) . "%";
                    break;
                }
            }
            $data->detail = $detail;
        }
        foreach ($datasPromos as $promo) {
            $totalPromo += $promo->views;
            $detail = [];
            if (count($pros) > 0) {
                foreach ($pros as $pro) {
                    if ($promo->date == $pro->date && $promo->views != 0) {
                        $detail[] = "Promos:" . number_format($pro->views, 0, '.', ',') . " - " . round($pro->views / $promo->views * 100) . "%";
                        break;
                    }
                }
            }
            if (count($revs) > 0) {
                foreach ($revs as $rev) {
                    if ($promo->date == $rev->date && $promo->views != 0) {
                        $detail[] = "Revshares:" . number_format($rev->views, 0, '.', ',') . " - " . round($rev->views / $promo->views * 100) . "%";
                    }
                }
            }
            if (count($claims) > 0) {
                foreach ($claims as $claim) {
                    if ($promo->date == $claim->date && $promo->views != 0) {
                        $detail[] = "Claims:" . number_format($claim->views, 0, '.', ',') . " - " . round($claim->views / $promo->views * 100) . "%";
                        break;
                    }
                }
            }
            $promo->detail = $detail;
        }
        return array("data" => $datas, "total" => $total, "totalPromo" => $totalPromo, "data_promo" => $datasPromos);
    }

    public function getChartTotalChannelsCount(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.getChartTotalChannelsCount|request=' . json_encode($request->all()));
//        $startDate = date("Ymd", strtotime("-28 day"));
        $startDate = '20220322';
        $endDate = date("Ymd", time());
        if (isset($request->start)) {
            $startDate = $request->start;
        }
        if (isset($request->end)) {
            $endDate = $request->end;
        }

        $userCondition = "";
        if ($request->user != "-1") {
            $userCondition = "and user_name='$request->user'";
        }
        $usernames = [];
        $listUser = User::where("status", 1)->where("role", "like", "%16%")->get(["user_name", "user_code"]);
        foreach ($listUser as $u) {
            $usernames[] = $u->user_code;
        }
        $listU = implode("','", $usernames);
        $datas = DB::select("select confirm_time as date,count(id) as channels from  accountinfo where confirm_time >= '$startDate' and confirm_time <= '$endDate' $userCondition and user_name in('$listU') group by confirm_time");
        $datasUsers = DB::select("select confirm_time as date,user_name ,count(id) as channels from accountinfo where confirm_time >= '$startDate' and confirm_time <= '$endDate' $userCondition and user_name in('$listU') group by confirm_time,user_name");
        $datasUsersTotal = DB::select("select user_name ,count(id) as channels from accountinfo where confirm_time >= '$startDate' and confirm_time <= '$endDate' $userCondition and user_name in('$listU') group by user_name order by user_name");
        $total = 0;
        foreach ($datas as $data) {
            $total += $data->channels;
            $detail = [];
            foreach ($datasUsers as $dataUser) {
                if ($data->date == $dataUser->date) {
                    foreach ($listUser as $us) {
                        if ($dataUser->user_name == $us->user_code) {
                            $us->channels = $dataUser->channels;
                            break;
                        }
                    }
                }
            }
            foreach ($listUser as $us) {
                $detail[] = "$us->user_name : " . (!empty($us->channels) ? $us->channels : 0);
                $us->channels = 0;
            }
            $data->detail = $detail;
        }
        $charts[] = array("data" => $datas, "total" => $total);

        $channelsByUser = [];
        foreach ($datasUsersTotal as $dataUserTotal) {
            foreach ($listUser as $u) {
                if ($dataUserTotal->user_name == $u->user_code) {
                    $channelsByUser[] = (object) [
                                'user_name' => $u->user_name,
                                'channels' => $dataUserTotal->channels
                    ];
                    break;
                }
            }
        }
        return array("charts" => $charts, "data_user" => $channelsByUser);
    }

    public function getvideochartdaily(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.getvideochart|request=' . json_encode($request->all()));
        $startDate = '20220301';
        $endDate = date("Ymd", time());
        if (isset($request->start)) {
            $startDate = $request->start;
        }
        if (isset($request->end)) {
            $endDate = $request->end;
        }
        $datas = AccountInfo::where("id", $request->id)->first(["id", "chanel_id", "chanel_name", "count_video", "view_detail", "limit", "limit_weekend"]);
        $charts = DB::select("select date as time,views as daily from athena_data_sync where channel_id = '$datas->chanel_id' and date >= '$startDate' and date <= '$endDate' order by date");
        $datas->view_detail = json_encode($charts);
        return $datas;
    }

    public function updateTask(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.updateTask|request=' . json_encode($request->all()));
        $curentDate = gmdate("Y-m-d", time() + (7 * 3600));
        $curentTime = gmdate("H:i:s", time() + (7 * 3600));
        if ($request->type == 1 || $request->type == 2) {
            $check = Tasks::where("username", $user->user_name)->where("type", 1)->where("date", $curentDate)->where("content", $request->content)->first();
            if (!$check) {
                $tasks = new Tasks();
                $tasks->username = $user->user_name;
                $tasks->type = $request->type;
                $tasks->date = $curentDate;
                $tasks->time = $curentTime;
                $tasks->content = $request->content;
                if ($request->type == 2) {
                    $channelInfo = AccountInfo::where("chanel_id", $request->channel)->first();
                    if ($channelInfo) {
                        $tasks->channel_id = $channelInfo->chanel_id;
                        $tasks->channel_name = $channelInfo->chanel_name;
                        $info = YoutubeHelper::processLink($request->content);
                        if ($info["type"] != 1) {
                            return array("status" => 0, "message" => "Playlist invalid");
                        }
                        $tasks->content = $info["data"];
                        //kiểm tra xem link đã được add vào hệ thống chưa
                        $check = Tasks::where("content", $info["data"])->first();
                        if ($check) {
                            return array("status" => 0, "message" => "This playlist is already on the system");
                        }

//                        //thêm job để check wakeup có hiệu quả không
//                        $taskList = '[{"script_name":"playlist","func_name":"check_wakeup","params":[{"name":"playlist_id","type":"string","value":"' . $info["data"] . '"}]}]';
//                        $req = (object) [
//                                    "gmail" => $channelInfo->note,
//                                    "task_list" => $taskList,
//                                    "run_time" => 0,
//                                    "type" => 5
//                        ];
//                        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
//                        $jobId = $res->job_id;
//                        $tasks->job_check_id = $jobId;
//                        $tasks->save();
                        //kiểm tra va update trạng thái của task notify cũ
                        Tasks::where("username", $user->user_name)->where("type", 2)
                                ->where("channel_id", $channelInfo->chanel_id)->where("notify", 0)->update(["notify" => 2]);

                        Tasks::where("username", $user->user_name)->where("type", 2)->where("id", "<>", $tasks->id)
                                ->where("channel_id", $channelInfo->chanel_id)->update(["wakeup_status" => 2]);
                    }
                }
                $tasks->create_time = time();
                $tasks->save();
                return array("status" => 1, "message" => "Successfull");
            }
            return array("status" => 0, "message" => "Data exists");
        } else if ($request->type == 3) {
            $isCampaign = 0;
            if ($request->is_claim == "on") {
                $isCampaign = 1;
            }
            $is_bommix = 0;
            if ($request->is_bommix == "on") {
                $is_bommix = 1;
            }
            $txtSource = str_replace(array("\r\n", "\n"), "@;@", trim($request->mix_link));
            $arraySource = explode("@;@", $txtSource);
            if (count($arraySource) == 0) {
                return array("status" => 0, "message" => "Link can not be empty");
            }
//            Log::info(json_encode($arraySource));
            $count = 0;
            $i = 0;
            $total = count($arraySource);

            //lấy dữ liệu campaignstatic
            if ($isCampaign == 1) {
                if ($request->submit_type == 2) {
                    if (empty($request->mix_claim) && empty($request->mix_promo)) {
                        return array("status" => 0, "message" => "Choose claims or promos");
                    }
                    $listClaimId = [];
                    $listPromoId = [];
                    if (!empty($request->mix_claim)) {
                        $isPromo = 0;
                        $listClaimId = $request->mix_claim;
                    }
                    if (!empty($request->mix_promo)) {
                        $listPromoId = $request->mix_promo;
                    }
                    $mix_campaign_id = array_merge($listClaimId, $listPromoId);

                    if (count($mix_campaign_id) == 0) {
                        return array("status" => 0, "message" => "Choose claims or promos or both");
                    }
                    $campaignStatics = CampaignStatistics::whereIn("id", $mix_campaign_id)->get();
                    if (count($campaignStatics) != count($mix_campaign_id)) {
                        return array("status" => 0, "message" => "Not found info of claims or promo");
                    }
                } else {
                    $campaignStatics = CampaignStatistics::whereIn("status", [1,4])->get();
//                    Log::info("autosubmit: " . count($campaignStatics));
                }


                foreach ($arraySource as $mixLink) {
                    $i++;
                    $info = YoutubeHelper::processLink($mixLink);
                    Log::info(" YoutubeHelper::processLink" . json_encode($info));
                    if ($info["type"] != 3) {
//                return array("status" => 0, "message" => "Mix link invalid");
                        continue;
                    }
                    $video_id = $info["data"];

                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                    if ($videoInfo["status"] == 0) {
                        for ($t = 0; $t < 5; $t++) {
                            error_log("Retry $video_id");
                            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
//                        error_log(json_encode($videoInfo));
                            if ($videoInfo["status"] == 1) {
                                break;
                            }
                        }
                    }
                    //1: là promos,0 :là claim
                    $isPromo = 1;


                    if ($request->submit_type == 2) {
                        if ($isCampaign == 1) {
//                        if (count($request->mix_claim) == 0 && count($request->mix_promo) == 0) {
////                        return array("status" => 0, "message" => "Choose claims or promos");
//                            continue;
//                        }
//                        $listClaimId = [];
//                        $listPromoId = [];
//                        if (count($request->mix_claim) > 0) {
//                            $isPromo = 0;
//                            $listClaimId = $request->mix_claim;
//                        }
//                        if (count($request->mix_promo) > 0) {
//                            $listPromoId = $request->mix_promo;
//                        }
//                        $mix_campaign_id = array_merge($listClaimId, $listPromoId);
//
//                        if (count($mix_campaign_id) == 0) {
//                            return array("status" => 0, "message" => "Choose claims or promos or both");
//                        }
//                        $campaignStatics = CampaignStatistics::whereIn("id", $mix_campaign_id)->get();
//                        if (count($campaignStatics) != count($mix_campaign_id)) {
////                        return array("status" => 0, "message" => "Not found info of claims or promo");
//                            continue;
//                        }


                            foreach ($campaignStatics as $campaignStatic) {
                                $check = Campaign::where("video_id", $video_id)->where("campaign_id", $campaignStatic->id)->first();
                                if ($check) {
                                    $log = $check->log;
                                    $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name update from mix form";
                                    $check->log = $log;
                                    $check->is_bommix = $is_bommix;
                                    $check->video_type = $request->link_type;
                                    if ($isPromo == 0) {
                                        $check->is_claim = 1;
                                    }
                                    $check->save();
                                    continue;
                                }
                                $curr = time();
                                $campaign = new Campaign();
                                $campaign->campaign_id = $campaignStatic->id;
                                $campaign->username = $user->user_name;
                                $campaign->is_bommix = $is_bommix;
                                $campaign->campaign_name = $campaignStatic->campaign_name;
                                $campaign->channel_id = $videoInfo["channelId"];
                                $campaign->channel_name = $videoInfo["channelName"];
                                $campaign->video_type = $request->link_type;
                                $campaign->video_id = trim($video_id);
                                $campaign->video_title = $videoInfo["title"];
                                $campaign->views_detail = '[]';
                                $campaign->status = $videoInfo["status"];
                                $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                $campaign->publish_date = $videoInfo["publish_date"];
                                $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                                $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
//                        $campaign->status_confirm = $request->link_type == 5 ? 3 : 1;
                                $campaign->status_confirm = 1;
                                $log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name add new from mix form";
                                $campaign->log = $log;
                                if ($isPromo == 0) {
                                    $campaign->is_claim = 1;
                                }
                                $campaign->save();
                                $count++;
                                //tự đông đóng task và đóng notification
                                //2024/03/26 sang confirm, campaign làm lyric thì submit lyric mới auto finish notification
//                                        campaign ko làm lyric thì submit mix hoặc lyric thì đều auto finish notification
                                if ($request->link_type == 2 || $request->link_type == 5) {
                                    //kiểm tra xem campaign có làm lyric ko
                                    $isLyricMake = Tasks::where("username", $user->user_name)->where("type", 5)->where("campaign_id", $campaignStatic->id)->first();
                                    $taskDone = null;
                                    if (($request->link_type == 2 && $isLyricMake) || !$isLyricMake) {
                                        //ưu tiên tìm task start promo
                                        $taskDone = Tasks::where("username", $user->user_name)->where("type", 12)->where("campaign_id", $campaignStatic->id)->where("task_status", 0)->first();
                                    }
                                    //tìm task lyric hoặc mix theo link_type
                                    if (!$taskDone) {
                                        $taskDone = Tasks::where("username", $user->user_name)->where("type", $request->link_type == 2 ? 6 : 9)->where("campaign_id", $campaignStatic->id)->where("task_status", 0)->first();
                                    }
                                    if ($taskDone) {
                                        $taskDone->task_status = 3;
                                        $taskDone->update_time = gmdate("Y-m-d H:i:s", time() + 7 * 3600);
                                        $taskDone->save();
                                        $notify = Notification::where("noti_id", $taskDone->id)->where("status", 0)->first();
                                        if ($notify) {
                                            if ($taskDone->type == 12) {
                                                //chuyển sáng processing nếu là task start_campaign, task start_campaign sẽ sang done khi stop campaign
                                                $notify->status = 2;
                                            } else {
                                                $notify->status = 3;
                                            }
                                            $notify->log = $notify->log . Utils::timeToStringGmT7(time()) . " $user->user_name submit auto video $video_id, system change status=$notify->status" . PHP_EOL;
                                            $notify->action_time = time();
                                            $notify->save();
                                        }
                                    }
                                }

                                $tasks = new Tasks();
                                $tasks->username = $user->user_name;
                                $tasks->campaign_id = $campaignStatic->id;
                                $tasks->type = $request->type;
                                $tasks->date = $curentDate;
                                $tasks->time = $curentTime;
                                $tasks->content = trim($video_id);
                                $tasks->create_time = time();
                                $tasks->save();
                            }
                        }
                    } else {
                        //submit auto
//                    $campaignStatics = CampaignStatistics::where("status", 1)->get();
                        $channelId = $videoInfo["channelId"];
                        $accountInfo = AccountInfo::where("chanel_id", $channelId)->first();
                        if (!$accountInfo) {
                            continue;
                        }
                        $channelName = $videoInfo["channelName"];
                        Log::info("http://65.109.3.200:5002/copyright/check/$accountInfo->note/$video_id");
                        $result = RequestHelper::callAPI2("GET", "http://65.109.3.200:5002/copyright/check/$accountInfo->note/$video_id", array());
                        if ($result != "") {
                            $match = 0;
                            foreach ($result->claims as $claim) {
                                foreach ($campaignStatics as $campaignStatic) {
                                    if ($claim->assetId == $campaignStatic->asset_id) {
                                        $match = 1;
                                        $check = Campaign::where("video_id", $video_id)->where("campaign_id", $campaignStatic->id)->first();
                                        if ($check) {
                                            $check->log = $check->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name update auto from mix form";
                                            $check->is_bommix = $is_bommix;
                                            $check->is_match_claim = 1;
                                            $check->video_type = $request->link_type;
                                            $check->number_claim = count($result->claims);
                                            $check->log_claim = $result->mess;
                                            if ($isPromo == 0) {
                                                $check->is_claim = 1;
                                            }
                                            $count++;
                                            $check->save();
                                            continue;
                                        }
                                        $curr = time();
                                        $campaign = new Campaign();
                                        $campaign->campaign_id = $campaignStatic->id;
                                        $campaign->username = Utils::getUserFromUserCode($accountInfo->user_name);
                                        $campaign->is_bommix = $is_bommix;
                                        $campaign->campaign_name = $campaignStatic->campaign_name;
                                        $campaign->channel_id = $channelId;
                                        $campaign->channel_name = $channelName;
                                        $campaign->video_type = $request->link_type;
                                        $campaign->video_id = trim($video_id);
                                        $campaign->video_title = $videoInfo["title"];
                                        $campaign->views_detail = '[]';
                                        $campaign->status = $videoInfo["status"];
                                        $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                        $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                        $campaign->publish_date = $videoInfo["publish_date"];
                                        $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                                        $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                                        $campaign->status_confirm = 1;
                                        $campaign->is_match_claim = 1;
                                        $log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name add new auto from mix form";
                                        $campaign->log = $log;
                                        $campaign->log = $campaign->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " system confirm 3";
                                        if ($isPromo == 0) {
                                            $campaign->is_claim = 1;
                                        }
                                        $campaign->number_claim = count($result->claims);
                                        $campaign->log_claim = $result->mess;
                                        $campaign->save();
                                        $count++;
                                        //tự đông đóng task và đóng notification
                                        //2024/03/26 sang confirm, campaign làm lyric thì submit lyric mới auto finish notification
//                                        campaign ko làm lyric thì submit mix hoặc lyric thì đều auto finish notification
                                        if ($request->link_type == 2 || $request->link_type == 5) {
                                            //kiểm tra xem campaign có làm lyric ko
                                            $isLyricMake = Tasks::where("username", $user->user_name)->where("type", 5)->where("campaign_id", $campaignStatic->id)->first();
                                            $taskDone = null;
                                            if (($request->link_type == 2 && $isLyricMake) || !$isLyricMake) {
                                                //ưu tiên tìm task start promo
                                                $taskDone = Tasks::where("username", $user->user_name)->where("type", 12)->where("campaign_id", $campaignStatic->id)->where("task_status", 0)->first();
                                            }

                                            //tìm task lyric hoặc mix theo link_type
                                            if (!$taskDone) {
                                                $taskDone = Tasks::where("username", $user->user_name)->where("type", $request->link_type == 2 ? 6 : 9)->where("campaign_id", $campaignStatic->id)->where("task_status", 0)->first();
                                            }
                                            if ($taskDone) {
                                                $taskDone->task_status = 3;
                                                $taskDone->update_time = gmdate("Y-m-d H:i:s", time() + 7 * 3600);
                                                $taskDone->save();
                                                $notify = Notification::where("noti_id", $taskDone->id)->where("status", 0)->first();
                                                if ($notify) {
                                                    if ($taskDone->type == 12) {
                                                        //chuyển sáng processing nếu là task start_campaign, task start_campaign sẽ sang done khi stop campaign
                                                        $notify->status = 2;
                                                    } else {
                                                        $notify->status = 3;
                                                    }
                                                    $notify->log = $notify->log . Utils::timeToStringGmT7(time()) . " $user->user_name submit auto video $video_id, system change status=$notify->status" . PHP_EOL;
                                                    $notify->action_time = time();
                                                    $notify->save();
                                                }
                                            }
                                        }

                                        $tasks = new Tasks();
                                        $tasks->username = $user->user_name;
                                        $tasks->campaign_id = $campaignStatic->id;
                                        $tasks->type = $request->type;
                                        $tasks->date = $curentDate;
                                        $tasks->time = $curentTime;
                                        $tasks->content = trim($video_id);
                                        $tasks->create_time = time();
                                        $tasks->save();
                                    }
                                }
                            }
                            Log::info("DashboardController.updateTask $i/$total $video_id claim=" . count($result->claims) . " match=$match");
                        }
                    }
                }
                return array("status" => 1, "message" => "Success $count");
            }
        }
    }

    public function checkTask() {
        $tasks = Tasks::where("type", 2)->where("create_time", "<=", (time() - 6 * 3600))->where("notify", 0)->get();
        foreach ($tasks as $task) {
            $notify = new Notify();
            $notify->username = $task->username;
            $notify->message = "$notify->username đã đến giờ tạo wakeup happy cho kênh $task->channel_name";
            $notify->create_time = gmdate("d-m-Y H:i:s", time() + (7 * 3600));
            $notify->save();
            $task->notify = 1;
            $task->save();
        }
    }

    public function notify() {
        $datas = Notify::where("status", 0)->get();
        foreach ($datas as $data) {
            $user = User::where("user_name", $data->username)->first();
            Log::info(json_encode($user));
            $info = explode("@@", $user->telegram_id);
            if (count($info) >= 2) {
                RequestHelper::get("https://api.telegram.org/bot$info[0]/sendMessage?chat_id=-$info[1]&text=$data->message");
                $data->status = 1;
                $data->update_time = gmdate("d-m-Y H:i:s", time() + (7 * 3600));
                $data->save();
            }
        }
    }

    //tạo lệnh chạy autowakeup
    public function addWakeup(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.addWakeup|request=' . json_encode($request->all()));
        $curr = time();
        $numberVideo = 20;
        $wakeupSort = 2;
        $priorityPromoList = [];
        $videoListSource = [];
        $channel = AccountInfo::find($request->id_channel);
        if (!$channel) {
            return array("status" => "error", "message" => "Not found channel $request->id_channel");
        }
        if ($request->title == null || $request->title == "") {
            return array("status" => "error", "message" => "Title invalid");
        }
        $wakeup = new AutoWakeupHappy();
        $wakeup->username = $user->user_name;
        $wakeup->channel_id = $channel->chanel_id;
        $wakeup->channel_name = $channel->chanel_name;
        $wakeup->gmail = $channel->note != null ? $channel->note : $channel->gmail;
        $wakeup->title = trim($request->title);
//        1:playlist normal,2 : playlist wakeup
        $wakeupType = $request->wakeup_type;
        $sourceType = $request->source_wakeup_type;
        $wakeup->wakeup_type = $wakeupType;
        $wakeup->source_type = $sourceType;
        $wakeup->next_time_run = strtotime($request->run_time) - (7 * 3600);
        $wakeup->last_excute_time_text = gmdate("Y/m/d H:i:s", $curr + (7 * 3600));
        $wakeup->create_time_text = gmdate("Y/m/d H:i:s", $curr + (7 * 3600));
        $playlistSource = "";
        if ($sourceType == 1) {
            $info = YoutubeHelper::processLink($request->playlist_source);
            if ($info["type"] != 1) {
                return array("status" => "error", "message" => "Playlist invalid");
            }
            $playlistSource = $info["data"];
            $wakeup->playlist_source = $playlistSource;
        } elseif ($sourceType == 2) {
            if (isset($request->videos_list_source) && $request->videos_list_source != "") {
                $temp = str_replace(array("\r\n", "\n", " ", ","), "@;@", $request->videos_list_source);
                $array_videos = explode("@;@", $temp);
                if (count($array_videos) > 0) {
                    foreach ($array_videos as $video) {
                        $infoVideo = YoutubeHelper::processLink($video);
                        if ($infoVideo["type"] == 3) {
                            $videoId = $infoVideo["data"];
                            $videoListSource[] = $videoId;
                        }
                    }
                }
            }
            $wakeup->videos_list_source = json_encode($videoListSource);
        } elseif ($sourceType == 3) {
            if (isset($request->wakeup_sort)) {
                $wakeupSort = $request->wakeup_sort;
            }
            if (isset($request->number_videos)) {
                $numberVideo = $request->number_videos;
            }
            $wakeup->sort = $wakeupSort;
            $wakeup->number_videos = $numberVideo;
        }

        if (isset($request->priority_promo_list) && $request->priority_promo_list != "") {
            $temp = str_replace(array("\r\n", "\n", " ", ","), "@;@", $request->priority_promo_list);
            $array_videos = explode("@;@", $temp);
            if (count($array_videos) > 0) {
                foreach ($array_videos as $video) {
                    $infoVideo = YoutubeHelper::processLink($video);
                    if ($infoVideo["type"] == 3) {
                        $videoId = $infoVideo["data"];
                        $priorityPromoList[] = $videoId;
                    }
                }
            }
            $wakeup->priority_promo_list = json_encode($priorityPromoList);
        }
        //update trạng thái của lệnh autowake up, khi có lệnh autowakeup mới đè lên
        if ($wakeupType == 2) {
            AutoWakeupHappy::where("channel_id", $channel->chanel_id)->where("wakeup_type", 2)->whereIn("status", [0, 1])->update(["status" => 6, "last_excute_time_text" => gmdate("Y-m-d H:i:s", $curr + (7 * 3600)), "log" => "Stop on update new command"]);
        }
        $wakeup->save();
        return array("status" => "success", "message" => "Success");
    }

    //ham check autowakeup chay xong chua.
    public function checkRunWakeup() {
        $locker = new Locker(10);
        $locker->lock();
        $curr = time();
        $curentDate = gmdate("Y-m-d", $curr + (7 * 3600));
        $curentTime = gmdate("H:i:s", $curr + (7 * 3600));
        $datas = AutoWakeupHappy::where("next_time_run", "<=", $curr)->where("status", 1)->get();
        foreach ($datas as $data) {
//            Log::info("checkRunWakeup $data->job_id");
            $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$data->job_id", []);
            if ($res != null && $res != "" && !empty($res->status)) {
                $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($res->result)));
                $newPllId = null;
                foreach ($results as $result) {
                    if (Utils::containString($result, "wakeup")) {
                        $temp = json_decode($result);
//                        Log::info("checkRunWakeup $data->job_id $temp->result");
                        if (!Utils::containString($temp->result, "None") && $temp->result != "") {
                            $newPllId = $temp->result;
                            if (Utils::containString($newPllId, "WAS_ERROR")) {
                                $tmps = explode(":", $temp->result);
                                if (count($tmps) == 3) {
                                    $newPllId = $tmps[1];
                                }
                            }
                        }
                    }
                }
                $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();
                $st = 3;
                if ($channel->gologin != null) {
                    $st = 5;
                }

                if ($res->status == $st) {
                    if ($data->wakeup_type != 4) {
                        $wakeup = new AutoWakeupHappy();
                        $wakeup->username = $data->username;
                        $wakeup->channel_id = $data->channel_id;
                        $wakeup->gmail = $data->gmail;
                        $wakeup->playlist_id = $newPllId;
                        $wakeup->title = $data->title;
                        $wakeup->next_time_run = $curr + 3600 * 6;
                        $wakeup->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                        $wakeup->create_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                        $wakeup->wakeup_type = $data->wakeup_type;
                        $wakeup->sort = $data->sort;
                        $wakeup->number_videos = $data->number_videos;
                        $wakeup->priority_promo_list = $data->priority_promo_list;
                        $wakeup->channel_name = $data->channel_name;
                        $wakeup->save();
                        $data->status = 2;
                        $data->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                        $data->save();

                        //type trong bang task,2:manual wakeup, 4:auto wakeup
                        $taskType = 2;
                        if ($data->wakeup_type == 2) {
                            $taskType = 4;
                        }

                        //update những lệnh trước để nó ko scan 2 tiếng/lần nữa
                        Tasks::where("username", $data->username)->where("type", $taskType)->where("date", $curentDate)
                                ->where("channel_id", $data->channel_id)->update(["wakeup_status" => 2]);

                        //thêm vào task để quản lý
                        $tasks = new Tasks;
                        $tasks->username = $data->username;
                        $tasks->type = $taskType;
                        $tasks->date = $curentDate;
                        $tasks->time = $curentTime;
                        $tasks->channel_id = $data->channel_id;
                        if ($channel) {
                            $tasks->channel_name = $channel->chanel_name;
                        }
                        $tasks->create_time = $curr;
                        $tasks->content = $newPllId;


//                    //thêm job để check wakeup có hiệu quả không
//                    $taskList = '[{"script_name":"playlist","func_name":"check_wakeup","params":[{"name":"playlist_id","type":"string","value":"' . $newPllId . '"}]}]';
//                    $req = (object) [
//                                "gmail" => $data->gmail,
//                                "task_list" => $taskList,
//                                "run_time" => 0,
//                                "type" => 5
//                    ];
//                    $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
//                    $jobId = $res->job_id;
//                    $tasks->job_check_id = $jobId;
                        $tasks->save();

                        //kiểm tra va update trạng thái của task cũ nếu chưa được notify
                        Tasks::where("username", $data->username)->where("type", $taskType)->where("date", $curentDate)
                                ->where("channel_id", $channel->chanel_id)->where("notify", 0)->update(["notify" => 2]);

//                        return array("status" => 1, "message" => "Success");
                    } else {
                        //lệnh tạo playlist thường sẽ ko tạo ra lệnh chờ mới nữa
                        $data->status = 2;
                        $data->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                        $data->save();
                    }
                } else if ($res->status == 4) {
                    $data->status = 4;
                    $data->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                    if ($newPllId != null) {
                        $data->playlist_id = $newPllId;
                    }
                    //cho chạy lại nếu ERR_CONNECTION_CLOSED
                    if (Utils::containString(json_encode($results), "WAS_ERROR:ERR_CONNECTION_CLOSED")) {
                        $data->status = 0;
                        $data->next_time_run = 10;
                    }
                    $data->save();
                }
            }
        }
    }

    //ham check xem wake chay co hieu qua ko  (dùng bas)
    public function checkVideoWakeup() {
        $datas = Tasks::where("wakeup_status", 0)->whereIn("type", [2, 4])->whereNotNull("job_check_id")->get();
        foreach ($datas as $data) {
            $chart = [];
//            Log::info($data->content);
            $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$data->job_check_id", []);
            if ($res != null && $res != "" && !empty($res->status)) {
//            Log::info("checkVideoWakeup $data->job_check_id $res->status");

                if ($res->status == 3) {
                    $results = explode("@#@", str_replace(array("\r\n", "\n"), "@#@", trim($res->result)));
                    $listVideo = [];
                    //check video promo
//                    $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();
                    $promos = DB::select("select a.video_id from campaign a, campaign_statistics b where a.campaign_id = b.id and b.status =1 and b.type =1 and a.channel_id = '$data->channel_id' group by a.video_id");
//                    $promos = [];
//                    if ($channel) {
//                        $promos = explode(",", $channel->promo_videos);
//                    }
                    foreach ($results as $result) {
                        if (Utils::containString($result, "check_wakeup")) {
//                        Log::info("result: " . $result);
                            $jsonResult = json_decode($result);
                            $dataInfo = $jsonResult->result;
                            $temps = explode("@^@", $dataInfo);
                            $success = 0;
                            foreach ($temps as $in => $temp) {
                                if ($temp != null && $temp != "") {
//                                Log::info("temp: " . $temp);
                                    $info = explode("@;@", $temp);
                                    if (count($info) == 3) {
                                        $obj = (object) [
                                                    "video_id" => $info[1],
                                                    "wakeup" => $info[2],
                                                    "title" => $info[0]
                                        ];
                                        $listVideo[] = $obj;
                                        //đếm số lượng success
                                        if ($info[2] == 1) {
                                            $success++;
                                        }
                                    }
                                }
                            }
                            //kiểm tra vị trí các bài promos trong playlist wakeup
                            foreach ($listVideo as $index => $video) {
                                if (count($promos) > 0) {
                                    foreach ($promos as $promo) {
                                        if ($promo->video_id == $video->video_id) {
                                            $preWake = 0;
                                            if ($index != 0) {
                                                $preWake = $listVideo[$index - 1]->wakeup;
                                            }
                                            $array = (object) [
                                                        "video_id" => $video->video_id,
                                                        "wakeup" => $preWake,
                                                        "position" => $index + 1,
                                                        "title" => $video->title
                                            ];
                                            $chart[] = $array;
                                        }
                                    }
                                }
                            }
                            $data->check_time = gmdate("d-m H:i", time() + (7 * 3600));
                            $data->next_check_time = time() + 2 * 3600;
                            $data->wakeup_result = json_encode($listVideo);
                            $data->wakeup_status = 1;

                            //tính % success
                            $total = count($listVideo);
                            if ($total > 0) {
                                $data->wakeup_percent = round($success / $total * 100, 0);
                            }
                            $data->position = json_encode($chart);
                            $data->save();
                        }
                    }
                }
            }
        }
    }

    //ham check xem wake chay co hieu qua ko (dùng html)
    public function checkVideoWakeup2() {
        $locker = new Locker(525);
        $locker->lock();
        $curentDate = gmdate("Y-m-d", time() + (7 * 3600));
        $datas = Tasks::where("date", $curentDate)->whereIn("wakeup_status", [0, 1])->whereIn("type", [2])->where("next_check_time", "<=", time())->take(5)->get();
        foreach ($datas as $index => $data) {
            $totalRun = count($datas);
            error_log("checkVideoWakeup $index/$totalRun $data->id $data->content");
            $data->next_check_time = time() + 2 * 3600;
            $listVideo = WakeupHelper::checkEfficiencyWakeup($data->content);
            if (count($listVideo) == 0) {
                $data->wakeup_result = '[]';
                $data->check_time = gmdate("d-m H:i", time() + (7 * 3600));
                $data->save();
                continue;
            }
            $chart = [];
            $promos = DB::select("select a.video_id from campaign a, campaign_statistics b where a.campaign_id = b.id and b.status =1 and b.type =1 and a.channel_id = '$data->channel_id' group by a.video_id");
            $success = 0;

            //kiểm tra vị trí các bài promos trong playlist wakeup
            foreach ($listVideo as $index => $video) {
                if ($video->wakeup == 1) {
                    $success++;
                }
                if (count($promos) > 0) {
                    foreach ($promos as $promo) {
                        if ($promo->video_id == $video->video_id) {
                            $preWake = 0;
                            if ($index != 0) {
                                $preWake = $listVideo[$index - 1]->wakeup;
                            }
                            $array = (object) [
                                        "video_id" => $video->video_id,
                                        "wakeup" => $preWake,
                                        "position" => $index + 1,
                                        "title" => $video->title
                            ];
                            $chart[] = $array;
                        }
                    }
                }
            }
            //tính % success
            $total = count($listVideo);
            if ($total > 0) {
                $data->wakeup_percent = round($success / $total * 100, 0);
            }
            $data->check_time = gmdate("d-m H:i", time() + (7 * 3600));
            $data->next_check_time = time() + 2 * 3600;
            $data->wakeup_result = json_encode($listVideo);
            $data->wakeup_status = 1;
            $data->position = json_encode($chart);
            $data->save();
        }
    }

    //ham tạo lệch check wakeup 2 tiếng 1 lần
    public function makeCommandCheckWakeup() {
        $curentDate = gmdate("d-m-Y", time() + (7 * 3600));
        $datas = Tasks::where("date", $curentDate)->where("wakeup_status", 1)->where("type", 2)->whereNotNull("job_check_id")->where("next_check_time", "<=", time())->get();
        foreach ($datas as $data) {
            Log::info("makeCommandCheckWakeup " . $data->id);
            $channelInfo = AccountInfo::where("chanel_id", $data->channel_id)->first();
            $taskList = '[{"script_name":"playlist","func_name":"check_wakeup","params":[{"name":"playlist_id","type":"string","value":"' . $data->content . '"}]}]';
            $req = (object) [
                        "gmail" => $channelInfo->note,
                        "task_list" => $taskList,
                        "run_time" => 0,
                        "type" => 5,
                        "piority" => 80
            ];
            $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            $jobId = $res->job_id;
            $data->job_check_id = $jobId;
            $data->next_check_time = time() + 2 * 3600;
            $data->wakeup_status = 0;
            $data->save();
        }
    }

    //ham get task theo channel_id de ve bieu do
    public function getTasksByChannelId(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.getTasksByChannelId|request=' . json_encode($request->all()));
        //tháng trước
//        $firstDayOfMonth = strtotime('08/01/2021');
        //tháng này

        $firstDayOfMonth = strtotime(date("01-$request->month-Y"));

        $datas = DB::select("SELECT date,count(*) as total FROM `tasks` WHERE `type` = '2' and create_time >= $firstDayOfMonth AND `channel_id` = '$request->channel_id' group by  date order by id");
        $avgWake = DB::select("SELECT AVG(`wakeup_percent`) as avg FROM `tasks`  WHERE `type` = '2' and create_time >= $firstDayOfMonth AND `channel_id` = '$request->channel_id' AND `wakeup_percent` IS NOT NULL");
        $totalTask = count($datas);
        $maked = 0;
        $avgPercent = $avgWake[0]->avg != null ? $avgWake[0]->avg : 0;
        if ($totalTask == 0) {
            return array("status" => 0, "chart_data" => [], "maked" => $maked, "avgPercent" => $avgPercent);
        }
        foreach ($datas as $data) {
            if ($data->total >= 4) {
                $maked++;
            }
        }
        return array("status" => 1, "chart_data" => $datas, "maked" => $maked, "avgPercent" => round($avgPercent));
    }

    public function updateAutoWakeup(Request $request) {
        Log::info('DashboardController.updateAutoWakeup|request=' . json_encode($request->all()));
        return AutoWakeupHappy::where("job_id", $request->job_id)->update(['next_time_run' => $request->time, 'status' => $request->status, 'playlist_id' => $request->playlist_id]);
    }

    //lay thong tin wakeup cua promos video
    public function promosWakeupVideos(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.promosWakeupVideos|request=' . json_encode($request->all()));
        $firstDayOfMonth = strtotime(date('Y-m-01'));
        $tasks = Tasks::where("type", 2)->where("create_time", ">=", $firstDayOfMonth)->where("channel_id", $request->channel_id)->get();
        $channel = AccountInfo::where("chanel_id", $request->channel_id)->first();
        $promos = DB::select("select a.video_id from campaign a, campaign_statistics b where a.campaign_id = b.id and b.status =1 and b.type =1 and a.channel_id = '$request->channel_id' group by a.video_id");
//        $promos = [];
//        if ($channel) {
//            $promos = explode(",", $channel->promo_videos);
//        }

        $promoDataTotal = [];
        if (count($promos) > 0) {
            foreach ($promos as $promo) {
//                Log::info($promo->video_id);
                $promoVideo = [];
                foreach ($tasks as $task) {
                    $position = $task->position;

                    if ($position != null && $position != '[]') {
                        $positionArr = json_decode($position);
                        foreach ($positionArr as $pos) {
//                            Log::info(json_encode($pos));
                            if ($promo->video_id == $pos->video_id) {
                                $pos->date = "$task->date";
                                $promoVideo[] = $pos;
                            }
                        }
                    }
                }
                if (count($promoVideo) > 0) {
                    $promoDataTotal[] = $promoVideo;
                }
            }
        }

        return array("channel_name" => $channel->chanel_name, "data" => $promoDataTotal);
    }

    //xác nhận của admin rằng bài hát promo ok
    public function confirmPromo(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.confirmPromo|request=' . json_encode($request->all()));
        //confirm all
        if ($request->status == 100) {
            $datas = Campaign::where("status_confirm", 1)->where("channel_id", "<>", "")->get();
            foreach ($datas as $tmp) {
                $log = $tmp->log;
                $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name confirmed 3";
                $tmp->log = $log;
                $tmp->status_confirm = 3;
                $tmp->save();
                if ($tmp->task_id != null) {
                    Tasks::where("id", $tmp->task_id)->update(["task_status" => 3, "update_time" => gmdate("Y-m-d H:i:s", time() + 7 * 3600)]);
                }
            }
            return array("status" => "success", "message" => "Success " . count($datas));
        }
        $data = Campaign::where("id", $request->id)->first();
        if ($data) {
            if ($request->status == 2) {
                if ($request->mess == null) {
                    return array("status" => "error", "message" => "Message cannot be empty");
                }
            }

            $data->status_confirm = $request->status;
            $data->message = $request->mess;
            $log = $data->log;
            $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name confirmed $request->status";
            $data->log = $log;
            $data->save();
            if ($data->task_id != null) {
                $taskStatus = 3;
                if ($data->status_confirm == 4) {
                    $taskStatus = 0;
                }
                Tasks::where("id", $data->task_id)->update(["task_status" => $taskStatus, "update_time" => gmdate("Y-m-d H:i:s", time() + 7 * 3600)]);
            }
        } else {
            return array("status" => "error", "message" => "Not found info");
        }
//        Log::info(DB::getQueryLog());
        return array("status" => "success", "message" => "Success");
    }

    public function confirmPromoFilter(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.confirmPromoFilter|request=' . json_encode($request->all()));
        //confirm filter
        if (isset($request->boommix) && isset($request->campaign_type) && isset($request->changed_title) && isset($request->log_claim) && isset($request->match_claim) && isset($request->manager)) {
            return array("status" => "error", "message" => "You have to choose filter condition");
        }
        $datas = Campaign::where("status_confirm", 1);
        if (isset($request->manager) && count($request->manager) > 0) {
            $manager = [];
            foreach ($request->manager as $data) {
                $manager[] = $data . "music";
            }
            $datas = $datas->whereIn("username", $manager);
        }
        if (isset($request->boommix) && count($request->boommix) > 0) {
            $temp = implode(",", $request->boommix);
            $temp = str_replace("NOT_A_BOOMMIX", 0, $temp);
            $temp = str_replace("BOOMMIX", 1, $temp);
            $bommix = explode(",", $temp);
            $datas = $datas->whereIn("is_bommix", $bommix);
        }
        if (isset($request->match_claim) && count($request->match_claim) > 0) {
            $temp = implode(",", $request->match_claim);
            $temp = str_replace("NOT_MATCH", 0, $temp);
            $temp = str_replace("MATCHED", 1, $temp);
            $match = explode(",", $temp);
            $datas = $datas->whereIn("is_match_claim", $match);
        }

        if (isset($request->campaign_type) && count($request->campaign_type) > 0) {
            Log::info(json_encode($request->campaign_type));
            $claims = [];
            $promoType = [];
            $promoIds = [];
            foreach ($request->campaign_type as $data) {
                if ($data == 'COVER' || $data == 'ORIGINAL' || $data == 'ELECTRONIC') {
                    $claims[] = "[$data]";
                } else if ($data == 'PROMO') {
                    $promoType[] = 1;
                } else if ($data == 'SUBMISSION') {
                    $promoType[] = 5;
                } else if ($data == 'CLAIM') {
                    $promoType[] = 2;
                }
            }
            //lấy danh sách promo,submission
            if (count($promoType) > 0) {
                $listPromos = CampaignStatistics::whereIn("type", $promoType)->whereIn("status", [1, 2, 4])->get(["id"]);
                foreach ($listPromos as $data) {
                    $promoIds[] = $data->id;
                }
            }
            if (count($claims) > 0) {
                $datas = $datas->where(function($q) use ($claims, $promoIds) {
                    foreach ($claims as $index => $data) {
                        if ($index == 0) {
                            $q->where('campaign_name', 'like', '%' . $data . '%');
                        } else {
                            $q = $q->orWhere('campaign_name', 'like', '%' . $data . '%');
                        }
                    }
                    if (count($promoIds) > 0) {
                        $q = $q->orWhereIn("campaign_id", $promoIds);
                    }
                });
            } else {
                if (count($promoIds) > 0) {
                    $datas = $datas->WhereIn("campaign_id", $promoIds);
                }
            }
        }
        if (isset($request->changed_title) && count($request->changed_title) > 0) {
            $temp = implode(",", $request->changed_title);
            $temp = str_replace("NOT_CHANGE", 0, $temp);
            $temp = str_replace("CHANGED", 1, $temp);
            $change = explode(",", $temp);
            $datas = $datas->whereIn("is_changed_title", $change);
        }

        if (isset($request->log_claim) && count($request->log_claim) > 0) {
            Log::info(json_encode($request->log_claim));
        }

        if (isset($request->distributor) && count($request->distributor) > 0) {
            $campId = CampaignStatistics::whereIn("distributor", $request->distributor)->whereIn("status", [1, 2, 4])->pluck("id")->toArray();
            $datas = $datas->whereIn("campaign_id", $campId);
        }
        $datas = $datas->get();
//        Log::info("count:" . count($datas));
//        Log::info(DB::getQueryLog());
        foreach ($datas as $tmp) {
            $log = $tmp->log;
            $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name filter confirmed 3";
            $tmp->log = $log;
            $tmp->status_confirm = 3;
            $tmp->save();
            if ($tmp->task_id != null) {
                Tasks::where("id", $tmp->task_id)->update(["task_status" => 3, "update_time" => gmdate("Y-m-d H:i:s", time() + 7 * 3600)]);
            }
        }
        return array("status" => "success", "message" => "Success " . count($datas), "campaigns" => $datas);
    }

    //xac nhan kenh ok, se day du lieu len athena
    public function confirmChannel(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.confirmChannel|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $data = AccountInfo::where("id", $request->id)->first();
        if ($data) {
            if ($request->status == 3) {
                if (!isset($request->mess) || $request->mess == "") {
                    return array("status" => "error", "message" => "Message cannot be empty");
                }
            }
            if ($request->status == 1) {
                $data->confirm_time = gmdate("Ymd", time() + 7 * 3600);
                //cập nhật trạng thái trên Brand manager
                $brand = BrandManager::where("channel_name", $data->chanel_name)->where("status_use", 1)->where("status_brand", 0)->first();
                if ($brand) {
                    $brand->status_brand = 1;
                    $brand->channel_id = $data->chanel_id;
                    $log = $brand->log;
                    $log .= PHP_EOL . "$curr $user->user_name change status_brand = 1";
                    $brand->log = $log;
                    $brand->save();
                }
            }
            $data->is_sync = $request->status;
            if (isset($request->mess)) {
                $data->message = $request->mess;
            }
            $log = $data->log;
            $log .= PHP_EOL . $curr . " $user->user_name channel confirmed $request->status";
            $data->log = $log;
            $data->save();
        }
        return array("status" => "success", "message" => "Success");
    }

//    //update video promo dùng wakeup happy hay không
//    public function promoWake(Request $request) {
//        $user = Auth::user();
//        Log::info('|' . $user->user_name . '|DashboardController.promoWake|request=' . json_encode($request->all()));
//        $campaign = Campaign::find($request->id);
//        if ($campaign) {
//            $campaign->is_wakeup = $request->status;
//            $log = $campaign->log;
//            $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name channge is_wakeup to $request->status";
//            $campaign->log = $log;
//            $campaign->save();
//            $accountInfo = AccountInfo::where("chanel_id", $campaign->channel_id)->first();
//            if ($accountInfo) {
//                $promoVideo = $accountInfo->promo_videos;
//                if ($promoVideo != null) {
//                    $array = explode(",", $promoVideo);
//                    //thêm mới promos wakeup
//                    if ($request->status == 1) {
//                        array_push($array, $campaign->video_id);
//                    } else {
//                        //xóa promo_wakeup
//                        foreach ($array as $index => $data) {
//                            if ($data == $campaign->video_id) {
//                                unset($array[$index]);
//                                continue;
//                            }
//                        }
//                    }
//                    $temp = array_unique($array);
//                    $accountInfo->promo_videos = implode(",", $temp);
//                } else {
//                    $accountInfo->promo_videos = $campaign->video_id;
//                }
//                $accountInfo->save();
//            }
//
//            return array("status" => "success", "message" => "Success");
//        } else {
//            return array("status" => "success", "message" => "Not found $request->id");
//        }
//    }

    //chay lai autowakeup bị lỗi
    public function refreshAutoWake(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.refreshAutoWake|request=' . json_encode($request->all()));
        $wake = AutoWakeupHappy::where("id", $request->id)->first(["id", "channel_id", "status", "next_time_run", "job_id", "last_excute_time_text"]);
        $wake->status = 0;
        $wake->next_time_run = 1;
        $wake->log = "";
        $wake->save();
        return array("status" => "success", "message" => "Success", "wake" => $wake);
    }

    //scan lai video bi scan loi
    public function reSynVideo(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|DashboardController.reSynVideo|request=' . json_encode($request->all()));
        if ($request->id == 0) {
            $campaigns = Campaign::where("status_confirm", 1)->get();
        } else {
            $campaigns = Campaign::where("id", $request->id)->get();
        }
        $i = 0;
        foreach ($campaigns as $campaign) {
            $i++;
            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($campaign->video_id);
            if ($videoInfo["status"] == 0) {
                for ($t = 0; $t < 5; $t++) {
                    error_log("reSynVideo Retry $campaign->video_id");
                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($campaign->video_id);
                    error_log("reSynVideo Retry $campaign->video_id " . json_encode($videoInfo));
                    if ($videoInfo["status"] == 1) {
                        break;
                    }
                }
            }
            error_log("reSynVideo $campaign->video_id " . json_encode($videoInfo));
            if ($videoInfo["status"] == 1) {
                $duration = intval($videoInfo["length"]);

                $videoType = 0;
                if ($duration != 0) {
                    if ($duration <= 90) {
                        $videoType = 6;
                    } else if ($duration > 90 && $duration <= 8 * 60) {
                        $videoType = 2;
                    } else {
                        $videoType = 5;
                    }
                }
                $campaign->is_athena = 0;
                if ($campaign->video_type != 1 && $campaign->video_type != 2 && $campaign->video_type != 5) {
                    $campaign->video_type = $videoType;
                }
                if ($videoInfo["channelId"] != null && $videoInfo["channelId"] != "") {
                    $campaign->channel_id = $videoInfo["channelId"];
                }
                if ($videoInfo["channelName"] != null && $videoInfo["channelName"] != "") {
                    $campaign->channel_name = $videoInfo["channelName"];
                }
                if ($videoInfo["title"] != null && $videoInfo["title"] != "") {
                    $campaign->video_title = $videoInfo["title"];
                }
                if ($campaign->username == null || $campaign->username == "") {
                    $channel = AccountInfo::where("chanel_id", $videoInfo["channelId"])->first();
                    if ($channel) {
                        $pos = strripos($channel->user_name, '_');
                        $username = substr($channel->user_name, 0, $pos);
                        $campaign->username = $username;
                    }
                }
                $campaign->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                $campaign->publish_date = $videoInfo["publish_date"];
                $campaign->status = $videoInfo["status"];
                $campaign->views = $videoInfo["view"];
                $campaign->like = $videoInfo["like"];
                $campaign->dislike = $videoInfo["dislike"];
                $campaign->save();
            } else {
                $campaign->status = $videoInfo["status"];
                $campaign->save();
            }
        }
        return array("status" => "success", "message" => "Success $i");
    }

    //thay đổi trạng thái xử lý strikes
    public function strikeStatus(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.strikeStatus|request=' . json_encode($request->all()));
        $strike = Strikes::find($request->id);
        if ($strike) {
            $status = $strike->status;
            $opacity = 1;
            if ($status == 0) {
                $status = 1;
                $opacity = 0.5;
            } else {
                $status = 0;
                $opacity = 1;
            }
            $strike->status = $status;
            $strike->save();
            return array("status" => "success", "content" => "success", "newStatus" => $status, "opacity" => $opacity);
        }
        return array("status" => "danger", "content" => "Not found campaign");
    }

    //report cho cross_post
    public function crossPostReport(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.crossPostReport|request=' . json_encode($request->all()));
        try {
            $from = ($request->from);
            $to = ($request->to);
            $upStatus = $request->up_status;
            $conditionUp = "";
            if ($upStatus != 2) {
                if ($upStatus == 1) {
                    $conditionUp = " and video_id is not null ";
                } else {
                    $conditionUp = " and video_id is null ";
                }
            }
            $fromWhere = "and create_time >= '$from 00:00:00'";
            $toWhere = "";
            if ($from != $to) {
                $toWhere = "and create_time <= '$to 00:00:00'";
            }

            $sql = "select user_make, count(distinct channel_id) as channels,count(distinct track_id) as tracks,
                count(video_id) as videos,sum(views) as views from cross_post
                where 1=1
                $fromWhere
                $toWhere
                $conditionUp  
                group by user_make order by sum(views) desc";
            $data = DB::select($sql);
            return array("stauts" => "success", "report" => $data);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
            return array("stauts" => "error", "channel" => [], "track" => []);
        }
    }

    //cross_post delete video bị gậy
    public function crossPostDelete(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.crossPostDelete|request=' . json_encode($request->all()));
        try {
            if ($request->type == 1) {
                //xoa tất cả
                $datas = CrossPost::where("track_id", $request->data)->get();
            } else {
                //xóa 1 bài
                $datas = CrossPost::where("id", $request->data)->get();
            }
            foreach ($datas as $data) {
                if ($data->video_id != null) {
                    $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();
                    if ($channel->version == 1) {
                        $videos = [];
                        $videos[] = $data->video_id;
                        $taskList = '[{"script_name":"profile","func_name":"login","params":[]},{"script_name":"reup","func_name":"del_video","params":[{"name":"video_source","type":"string","value":' . json_encode($videos) . '}]}]';
                        $req = (object) [
                                    "gmail" => $channel->note,
                                    "task_list" => $taskList,
                                    "run_time" => 0,
                                    "type" => 9,
                                    "piority" => 10
                        ];
                        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                        $jobId = $res->job_id;
                        $data->job_del_id = $jobId;
                    } else if ($channel->version == 2) {
                        YoutubeHelper::deleteVideo($channel, $data->video_id);
                    }
                    $data->update_time = Utils::timeToStringGmT7(time());
                    $data->del_status = 1;
                    $data->save();
                }
            }

            return array("stauts" => "success", "message" => "Success");
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
            return array("stauts" => "error", "message" => "Success");
        }
    }

    //hàm hoàn thành công việc promos
    public function finishPromoTask(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.finishPromoTask|request=' . json_encode($request->all()));
        //task update homepage video
        if ($request->task_type == 10) {
            Tasks::where("id", $request->task_id)->update(["task_status" => 3, "update_time" => gmdate("Y/m/d H:i:s", time() + 7 * 3600)]);
            Notification::where("task_id", $request->task_id)->update(["status" => 1, "update_time" => time(), "update_time_text" => Utils::timeToStringGmT7(time())]);
            return array("status" => "success", "message" => "Success");
        }
        //reject task
        if ($request->action == 0) {
            if ($request->link == null || $request->link == "") {
                return array("status" => "error", "message" => "Put the reason to textbox");
            }
            Notification::where("task_id", $request->task_id)->update(["status" => 1, "update_time" => time(), "update_time_text" => Utils::timeToStringGmT7(time())]);
            Tasks::where("id", $request->task_id)->update(["task_status" => 2, "update_time" => gmdate("Y/m/d H:i:s", time() + 7 * 3600), "content" => $request->link]);
            return array("status" => "success", "message" => "Success");
        }
        $info = YoutubeHelper::processLink($request->link);
        if ($info["type"] != 3) {
            return array("status" => "error", "message" => "Link invalid");
        }

        $videoId = $info["data"];
        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($videoId);
        if ($videoInfo["status"] == 0) {
            for ($t = 0; $t < 5; $t++) {
                Log::info("finishPromoTask Retry $videoId");
                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($videoId);
                if ($videoInfo["status"] == 1) {
                    break;
                }
            }
        }

        $listCamId = explode(",", $request->cam_id);
        $campaignStatics = CampaignStatistics::whereIn("id", $listCamId)->get();
        if (count($campaignStatics) == 0) {
            return array("status" => "error", "message" => "Campaign $request->cam_id is not found");
        }

        $duration = intval($videoInfo["length"]);

        $videoType = 0;
        if ($duration != 0) {
            if ($duration <= 90) {
                $videoType = 6;
            } else if ($duration > 90 && $duration <= 8 * 60) {
                $videoType = 2;
            } else {
                $videoType = 5;
            }
        }

        Log::info('finishPromoTask: ' . json_encode($videoInfo));
        foreach ($campaignStatics as $campaignStatic) {
            $check = Campaign::where("video_id", $videoId)->where("campaign_id", $campaignStatic->id)->first();
            if ($check) {
                $log = $check->log;
                $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name update from task finish form";
                $check->log = $log;
                if ($check->status_confirm == 3) {
                    Tasks::where("id", $request->task_id)->update(["task_status" => 3, "update_time" => gmdate("Y/m/d H:i:s", time() + 7 * 3600), "content" => $videoId]);
                } else if ($check->status_confirm == 1 || $check->status_confirm == 4) {
                    Tasks::where("id", $request->task_id)->update(["task_status" => 1, "update_time" => gmdate("Y/m/d H:i:s", time() + 7 * 3600), "content" => $videoId]);
                    $check->status_confirm = 1;
                    $check->save();
                }
                $check->task_id = $request->task_id;
                $check->save();
                Notification::where("task_id", $request->task_id)->update(["status" => 1, "update_time" => time(), "update_time_text" => Utils::timeToStringGmT7(time())]);
            } else {
                $curr = time();
                $campaign = new Campaign();
                $campaign->campaign_id = $campaignStatic->id;
                $campaign->username = $user->user_name;
                $campaign->is_claim = 0;
                $campaign->campaign_name = $campaignStatic->campaign_name;
                $campaign->channel_id = $videoInfo["channelId"];
                $campaign->channel_name = $videoInfo["channelName"];
                $campaign->video_type = $videoType;
                $campaign->video_id = $videoId;
                $campaign->video_title = $videoInfo["title"];
                $campaign->views_detail = '[]';
                $campaign->status = $videoInfo["status"];
                $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                $campaign->publish_date = $videoInfo["publish_date"];
                $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                $campaign->status_confirm = 1;
                $campaign->task_id = $request->task_id;
                $log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name add new from task finish form";
                $campaign->log = $log;
                $campaign->save();
                Tasks::where("id", $request->task_id)->update(["task_status" => 1, "update_time" => gmdate("Y/m/d H:i:s", time() + 7 * 3600), "content" => $videoId]);
                Notification::where("task_id", $request->task_id)->update(["status" => 1, "update_time" => time(), "update_time_text" => Utils::timeToStringGmT7(time())]);
            }
        }

        return array("status" => "success", "message" => "Success");
    }

    //hàm lấy danh sách playlist trên kênh
    public function getListPlaylistChannel(Request $request) {
        $channel = AccountInfo::find($request->id);
        return YoutubeHelper::getListPLaylistWakeupHappy($channel->chanel_id);
    }

    //delete playlist
    public function deletePlaylistManual(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.deletePlaylistManual|request=' . json_encode($request->all()));
        if ($request->idChannel == "" || $request->playlistId == "") {
            return array("status" => "error", "message" => "Not enough info to delete");
        }
        $channel = AccountInfo::find($request->idChannel);
        if (!$channel) {
            return array("status" => "error", "message" => "Not found channel $request->idChannel");
        }
        $taskLists = [];
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $taskLists[] = $login;
        $delPlaylist = (object) [
                    "script_name" => "playlist",
                    "func_name" => "del_playlist",
                    "params" => [
                        (object) [
                            "name" => "playlist_id",
                            "type" => "string",
                            "value" => $request->playlistId,
                        ]
                    ]
        ];
        $taskLists[] = $delPlaylist;
        $req = (object) [
                    "gmail" => $channel->note,
                    "task_list" => json_encode($taskLists),
                    "run_time" => 0,
                    "type" => 631,
                    "piority" => 10
        ];

        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        Log::info($user->user_name . '|DashboardController.deletePlaylistManual|jobId|' . $res->job_id);
        return array("status" => "success", "message" => "Success $res->job_id");
    }

    //delete deleteVideos
    public function deleteVideosManual(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController.deleteVideosManual|request=' . json_encode($request->all()));
        $channel = AccountInfo::find($request->idChannel);
        if (!$channel) {
            return array("status" => "error", "message" => "Not found channel $request->idChannel");
        }
        $taskLists = [];
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $taskLists[] = $login;
        $delVideo = (object) [
                    "script_name" => "upload",
                    "func_name" => "del_with_filter",
                    "params" => [
                        (object) [
                            "name" => "views",
                            "type" => "string",
                            "value" => $request->views_delete,
                        ], (object) [
                            "name" => "order",
                            "type" => "string",
                            "value" => "VIDEO_ORDER_DISPLAY_TIME_ASC",
                        ]
                    ]
        ];
        $taskLists[] = $delVideo;
        $req = (object) [
                    "gmail" => $channel->note,
                    "task_list" => json_encode($taskLists),
                    "run_time" => 0,
                    "type" => 691,
                    "piority" => 10
        ];

        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        Log::info($user->user_name . '|DashboardController.deleteVideosManual|jobId|' . $res->job_id);
        return array("status" => "success", "message" => "Success $res->job_id");
    }

}
