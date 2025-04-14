<?php

namespace App\Http\Controllers;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\Notification;
use App\Http\Models\Tasks;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class NotificationController extends Controller {

    public function crawlNotification() {
        //lấy từ 360promo
        $datas = RequestHelper::callAPI2("GET", "https://dash.360promo.net/api/notification/get", []);
        foreach ($datas as $data) {
            $notify = Notification::where("noti_id", $data->noti_id)->where("group", $data->noti_group)->first();
            if (!$notify) {
                $notify = new Notification();
                $notify->platform = $data->noti_platform;
                $notify->group = $data->noti_group;
                $notify->email = $data->noti_email;
                $notify->noti_id = $data->noti_id;
                $notify->role = 1;
                $notify->create_time = Utils::timeToStringGmT7(time());
                if ($data->noti_group == "campaign") {
                    $notify->type = 'notify';
                    $notify->action_type = "read";
                    $notify->content = "$data->noti_email, campaign '$data->campaign_name' not pay yet";
                } elseif ($data->noti_group == "release") {
                    $notify->type = 'notify';
                    $notify->action_type = "read";
                    $notify->content = "$data->noti_email release '$data->song_name' no campaign";
                } elseif ($data->noti_group == "invoice") {
                    $notify->type = 'task';
                    $notify->action_type = "redirect";
                    $notify->action = "/360promo2?email=$data->noti_email";
                    $notify->content = "$data->noti_email has campaign '$data->campaign_name' no invoice";
                }
                $notify->save();
            }
        }
        //lấy từ automusic, 
        $tasks = Tasks::where("task_status", 0)->whereIn("type", [5, 6, 9, 12])->get();
        foreach ($tasks as $task) {
            $notify = Notification::where("noti_id", $task->id)->first();
            $actionType = "read";
            $action = null;
            if ($task->type == 5) {
                $group = 'lyric_timestamp';
                $actionType = "redirect";
                $data = CampaignStatistics::where("id", $task->campaign_id)->first();
                $action = "http://lyric.automusic.win/?username=$task->username" .
                        "&audio_url=" . urlencode($data->audio_url) .
                        "&artist=" . urlencode($data->artist) .
                        "&title=" . urlencode($data->song_name) .
                        "&deezer_art_id=" . $data->deezer_artist_id .
                        "&lyric=" . urlencode($data->lyrics) .
                        "&cam_id=$data->id";
                $content = "Make lyric timestamp for campain $task->campaign_id";
            } elseif ($task->type == 6) {
                $group = 'lyric_video';
                $actionType = "redirect";
                $action = "/campaign2?filter_id=$task->campaign_id";
                $content = "Make lyric video for campain $task->campaign_id";
            } elseif ($task->type == 9) {
                $group = 'promo_mix';
                $actionType = "redirect";
                $action = "/campaign2?filter_id=$task->campaign_id";
                $content = "Make Promo mix video for campain $task->campaign_id";
            } elseif ($task->type == 12) {
                $actionType = "redirect";
                $group = 'start_campaign';
                $action = "/campaign2?filter_id=$task->campaign_id";
                $content = "Start campaign $task->campaign_id";
            }
            if (!$notify) {
                $notify = new Notification();
                $notify->create_time = Utils::timeToStringGmT7(time());
                $notify->platform = 'automusic';
                $notify->group = $group;
                $notify->email = '';
                $notify->noti_id = $task->id;
                $notify->role = 20;
                $notify->username = $task->username;
                $notify->type = 'task_auto_done';
                $notify->content = $content;
                $notify->action_type = $actionType;
            }
            $notify->action = $action;
            $notify->save();
        }
    }

    public function getNotify(Request $request) {
        DB::enableQueryLog();
        $user = Auth()->user();
        if (!Auth::check()) {
            return;
        }
        // Lấy thời gian hiện tại ở GMT+0 và trừ đi 3 tháng
        $now = Carbon::now('UTC');
        $threeMonthsAgo = $now->subMonths(3);

// Chuyển đổi thời gian này thành GMT+7 để so sánh với create_time
        $threeMonthsAgoGmt7 = $threeMonthsAgo->setTimezone('Asia/Bangkok'); // GMT+7
//        Log::info($user->user_name . '|NotificationController.getNotify|request=' . json_encode($request->all()));
        $roles = explode(",", $user->role);
        $result = [];
        $count = 0;
        $groupAll = ["campaign", "release", "invoice", "lyric_timestamp", "lyric_video", "promo_mix", "start_campaign", "crosspost", "channel"];
        $groupColor = ["text-info", "text-warning", "text-purple", "text-success", "text-pink", "text-danger", "text-primary", "", "text-warning"];
        //show những notify trạng thái 0,3 mặc định
        $status = ["0", "2"];
        if (isset($request->status)) {
            $status = $request->status;
        }
        $groupFilter = ["lyric_timestamp", "lyric_video", "promo_mix", "start_campaign", "crosspost", "channel"];
        if ($request->is_admin_music) {
            $groupFilter = ["campaign", "release", "invoice", "lyric_timestamp", "lyric_video", "promo_mix", "start_campaign", "channel"];
        }
        if (isset($request->group)) {
            $groupFilter = $request->group;
        }

        $notis = Notification::whereIn("status", $status)->where("del_status", 0)->whereIn("group", $groupFilter)->orderBy("id", "desc")->where('create_time', '>=', $threeMonthsAgoGmt7)->get();
        foreach ($notis as $noti) {
            $noti->group_color = 'text-info';

            //mỗi group có 1 màu tương ứng
            foreach ($groupAll as $index => $g) {
                if ($noti->group == $g) {
                    $noti->group_color = $groupColor[$index];
                }
            }
            $noti->unread = "unread";
            if ($noti->type == "notify") {
                $noti->status_badge = "";
                if ($noti->status == 1) {
                    $noti->unread = "";
                }
            } else {
                if ($noti->status == 0) {
                    $noti->status_badge = "<span class='badge badge-danger cur-poiter'>New</span>";
                    $noti->unread = "unread";
                }

                if ($noti->status == 2) {
                    $noti->status_badge = "<span class='badge badge-warning cur-poiter'>Processing</span>";
                    $noti->unread = "";
                }
                if ($noti->status == 3) {
                    $noti->status_badge = "<span  class='badge badge-success cur-poiter'>Done</span>";
                    $noti->unread = "";
                }
            }

            $created = strtotime("$noti->create_time GMT+7");
            $noti->created = Utils::calcTimeText($created);
            $show = 0;
            $notiRole = explode(",", $noti->role);

            //show cho những user có cùng role
            foreach ($notiRole as $notiR) {
                if (in_array($notiR, $roles)) {
                    $show = 1;
                    break;
                }
            }

            //show cho nhưng user có cùng username
            if ($user->user_name == $noti->username) {
                $show = 1;
            }
            if ($show) {
                if ($noti->status == 0) {
                    $count++;
                }
                $result[] = $noti;
            }
        }
//        Log::info(DB::getQueryLog());
        return response()->json(["noti" => $result, "count" => $count]);
    }

    public function notiUpdateRead(Request $request) {
        $user = Auth::user();
        $noti = Notification::where("id", $request->id)->first();
        if ($noti) {
            if ($noti->status == 0) {
                $noti->action_time = time();
            }
            if ($noti->type == 'notify') {
                $noti->status = 1;
            } else {
                $noti->status = 2;
            }
            $noti->log = $noti->log . Utils::timeToStringGmT7(time()) . " $user->user_name read notify" . PHP_EOL;
            $noti->save();
        }
        return response()->json(["status" => "success", "redirect" => $noti->action]);
    }

    public function notiUpdateStatus(Request $request) {
        $user = Auth::user();
        $noti = Notification::where("id", $request->id)->first();
        if ($noti) {
            if ($noti->type == 'task') {
                if ($noti->status == 0) {
                    $status = 2;
                } elseif ($noti->status == 2) {
                    $status = 3;
                } else {
                    $status = 0;
                }
            } elseif ($noti->type == 'task_auto_done') {
                //không cho chuyển thành trạng thái done
                if ($noti->status == 0) {
                    $status = 2;
                } else {
                    $status = 0;
                }
            }

            $noti->status = $status;
            $noti->log = $noti->log . Utils::timeToStringGmT7(time()) . " $user->user_name change status = $status" . PHP_EOL;
            $noti->save();
        }
        return response()->json(["status" => "success"]);
    }

    public function addNotify(Request $request) {
        $check = Notification::where("noti_id", $request->sd_id)->first();
        if (!$check) {
            $platform = 'moonseo';
            $group = 'crosspost';
            $type = 'task';
            $action_type = 'redirect';
            $content = "$request->source_name just released a new video";
            $action = "https://moonseo.app/admin/campaigns/$request->campaign_id/edit?sd_id=$request->sd_id";
            $email = "";
            if (isset($request->platform)) {
                $platform = $request->platform;
            }
            if (isset($request->group)) {
                $group = $request->group;
            }
            if (isset($request->type)) {
                $type = $request->type;
            }
            if (isset($request->content)) {
                $content = $request->content;
            }
            if (isset($request->action)) {
                $action = $request->action;
            }
            if (isset($request->email)) {
                $email = $request->email;
            }
            if (isset($request->action_type)) {
                $action_type = $request->action_type;
            }

            $insert = new Notification();
            $insert->platform = $platform;
            $insert->group = $group;
            $insert->email = $email;
            $insert->noti_id = $request->sd_id;
            $insert->role = 1;
            $insert->username = $request->username;
            $insert->create_time = Utils::timeToStringGmT7(time());
            $insert->type = $type;
            $insert->content = $content;
            $insert->action_type = $action_type;
            $insert->action = $action;
            $insert->save();
        }
    }

    public function readAllNotify(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . 'NotificationController.readAllNotify|request=' . json_encode($request->all()));
        $groupFilter = ["campaign", "release"];
        if (isset($request->group)) {
            $groupFilter = $request->group;
        }

        $log = Utils::timeToStringGmT7(time()) . " $user->user_name read all notify" . PHP_EOL;
        $notify = Notification::whereIn("group", $groupFilter)->where("type", "<>", "task_auto_done")->where("status", 0)->update(["status" => 1, "log" => $log]);
        return $notify;
    }

}
