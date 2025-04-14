<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Logger;
use App\Common\Network\GoogleOtp;
use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\BrandManager;
use App\Http\Models\CardEndsCommand;
use App\Http\Models\CardEndsConfig;
use App\Http\Models\ChannelTags;
use App\Http\Models\GroupChannel;
use App\Http\Models\MoonshotsStats;
use App\Http\Models\RebrandChannel;
use App\Http\Models\RebrandChannelCmd;
use App\Http\Models\Strikes;
use App\Http\Models\Tasks;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ChannelManagementController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        $views = "components.channelmanagement2";
//        $views = "components.channelmanagement";
//        if ($request->is('channelmanagement/v2')) {
//            $views = "components.channelmanagement2";
//        }
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info("$user->user_name |$views.index|request=" . json_encode($request->all()));
        $curr = time();
        if ($request->is_admin_music || $request->is_supper_admin) {
            $datas = AccountInfo::where('del_status', 0);
            $listUser = $this->genListUserForMoveChannel($user, $request, 2);
        } else {
            $datas = AccountInfo::where('user_name', $user->user_code)->where('del_status', 0);
            $listUser = [];
        }

        $gmailCountsTmp = clone $datas;
        $gmailCounts = $gmailCountsTmp->select('note')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('note')
                ->having('total', '>', 1)
                ->get();
        $queries = [];

        $errorCountTmp = clone $datas;
        $errorCountChangeInfo = $errorCountTmp->where("last_change_pass", 4)->count();

        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        } else {
            $request->limit = 30;
        }
        if (isset($request->c1) && $request->c1 != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('chanel_name', 'like', '%' . $request->c1 . '%')->orWhere('id', $request->c1)->orWhere('note', 'like', '%' . $request->c1 . '%')->orWhere('chanel_id', 'like', '%' . $request->c1 . '%');
                if (Utils::containString($request->c1, ",")) {
                    $c1 = explode(',', $request->c1);
                    $arrayChannel = [];
                    foreach ($c1 as $arr) {
                        if ($arr != "") {
                            $arrayChannel[] = trim($arr);
                        }
                    }
                    $q->orWhereIn("chanel_id", $arrayChannel)->orWhereIn("id", $arrayChannel)->orWhereIn("note", $arrayChannel);
                }
            });
            $queries['c1'] = $request->c1;
        }
        if (isset($request->c2) && $request->c2 != '-1') {
            $datas = $datas->where('status', $request->c2);
            $queries['c2'] = $request->c2;
        }
        if (isset($request->c3) && $request->c3 != '-1') {
            $datas = $datas->where('group_channel_id', $request->c3);
            $queries['c3'] = $request->c3;
        } else {
            //gán giá trị mặc định cho group
            $request->c3 = -1;
        }

        if (isset($request->c4) && $request->c4 != '-1') {
            $datas = $datas->where('api_status', $request->c4);
            $queries['c4'] = $request->c4;
        }
        if (isset($request->c5) && $request->c5 != '-1') {
            $datas = $datas->where('user_name', $request->c5);
            $queries['c5'] = $request->c5;
        }
        if (isset($request->c6) && $request->c6 != '-1') {
            $datas = $datas->where('is_music_channel', $request->c6);
            $queries['c6'] = $request->c6;
        }
        if (isset($request->c7) && $request->c7 != '-1') {
            $datas = $datas->where('upload_type', $request->c7);
            $queries['c7'] = $request->c7;
        }
        if (isset($request->c8) && $request->c8 != '-1') {
            $datas = $datas->where('wakeup_type', $request->c8);
            $queries['c8'] = $request->c8;
        }
        if (isset($request->brand) && $request->brand != '-1') {
            $datas = $datas->where('is_rebrand', $request->brand);
            $queries['brand'] = $request->brand;
        }
        if (isset($request->studio) && $request->studio != '-1') {
            $datas = $datas->where('is_automusic_v2', $request->studio);
            $queries['studio'] = $request->studio;
        }
        if (isset($request->bas_new_status) && $request->bas_new_status != '-1') {
            $datas = $datas->where('bas_new_status', $request->bas_new_status);
            $queries['bas_new_status'] = $request->bas_new_status;
        }
        if (isset($request->statusHub) && $request->statusHub != '-1') {
            $datas = $datas->where('turn_off_hub', $request->statusHub);
            $queries['statusHub'] = $request->statusHub;
        }
        if (isset($request->channel_genre) && $request->channel_genre != '-1') {
            $datas = $datas->where('channel_genre', $request->channel_genre);
            $queries['channel_genre'] = $request->channel_genre;
        }
        if (isset($request->level) && $request->level != '-1') {
            $datas = $datas->where('level', $request->level);
            $queries['level'] = $request->level;
        }
        if (isset($request->is_sync) && $request->is_sync != '-1') {
            $datas = $datas->where('is_sync', $request->is_sync);
            $queries['is_sync'] = $request->is_sync;
        }
        if (isset($request->tags) && count($request->tags) > 0) {

//            $datas = $datas->where(function($q) use ($request) {
//                foreach ($request->tags as $tag) {
//                    $q->orWhere('tags', 'like', '%' . $tag . '%');
//                }
//            });
            foreach ($request->tags as $tag) {
                $datas = $datas->whereRaw('FIND_IN_SET(?, tags) > 0', [$tag]);
            }


            $queries['tags'] = $request->tags;
        }
        if (isset($request->other_filter) && $request->other_filter != '-1') {
            if ($request->other_filter == 1) {
                $datas = $datas->whereNotNull('gologin');
            } else if ($request->other_filter == 2) {
                $datas = $datas->whereNull('gologin');
            } else if ($request->other_filter == 3) {
                $datas = $datas->where('version', 2);
            }
            $queries['other_filter'] = $request->other_filter;
        }
        if (isset($request->version) && $request->version != '-1') {
            $datas = $datas->where('version', $request->version);
            $queries['version'] = $request->version;
        }
        if (isset($request->sub_tracking) && $request->sub_tracking != '-1') {
            $datas = $datas->where('sub_tracking', $request->sub_tracking);
            $queries['sub_tracking'] = $request->sub_tracking;
        }
        if (isset($request->is_changeinfo) && $request->is_changeinfo != '-1') {
            $text = "change info fail";
            if ($request->is_changeinfo == 1) {
                $text = "change info success";
            }
            $datas = $datas->where('log', 'like', "%$text%");
            $queries['is_changeinfo'] = $request->is_changeinfo;
        }

        if (isset($request->is_add_otp) && $request->is_add_otp != '-1') {
            $datas = $datas->where('is_add_otp', $request->is_add_otp)->whereNotNull("otp_key");
            $queries['is_add_otp'] = $request->is_add_otp;
        }

        //lọc những kênh out of date moonshots_stat
        $request['moonshot_stat'] = '';
        if (isset($request->outofdate_moonshot_stat)) {
            $datas = $datas->whereNotNull('gologin')->whereRaw("$curr - next_time_moon >= 86400");
            $request['moonshot_stat'] = 'checked';
        }

        if (isset($request->gmail_log) && $request->gmail_log != '') {
            $datas = $datas->where('log', 'like', "%$request->gmail_log%");
            $queries['gmail_log'] = $request->gmail_log;
        }
        if (isset($request->status_cmt) && $request->status_cmt != '-1') {
            $datas = $datas->where('status_cmt', $request->status_cmt);
            $queries['status_cmt'] = $request->status_cmt;
        }
        if (isset($request->is_change_info_error)) {
            $datas = $datas->where('last_change_pass', 4);
            $queries['is_change_info_error'] = $request->is_change_info_error;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->direction)) {
                $queries['direction'] = $request->direction;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'increasing';
            $request['direction'] = 'desc';
            $queries['sort'] = 'increasing';
            $queries['direction'] = 'desc';
        }




        $datas = $datas->sortable()->paginate($limit)->appends($queries);
//        $cards = DB::select("select a.channel_id, count(*) as total from (select channel_id,video_id,count(distinct video_id) from  card_ends_config group by channel_id,video_id) a group by a.channel_id");
        foreach ($datas as $data) {
            $data->tags_array = explode(",", $data->tags);
            $data->is_card = 0;
//            foreach ($cards as $card) {
//                if ($data->chanel_id == $card->channel_id) {
//                    $data->is_card = 1;
//                    $data->count_card = $card->total;
//                }
//            }
            $data->inc_percent = 0;
            if ($data->intro_outro != null) {
                $tmp = json_decode($data->intro_outro);
                $data->inc_percent = $tmp->daily_inc_per;
                $data->inc_time = !empty($tmp->daily_scan_time) ? Utils::calcTimeText($tmp->daily_scan_time) : "";
            }
        }

        //list ra những kênh ko đc update moonshots_stat trong 2 tiếng
        $stats = null;

        if ($request->is_admin_music) {
            $stats = AccountInfo::where("is_sync", 1)->whereIn("is_music_channel", [1, 2])->whereNotNull("gologin")->whereRaw("$curr - next_time_moon >= 86400")->count();
        }

//        Log::info(DB::getQueryLog());


        return view($views, ['datas' => $datas,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            'status' => $this->genStatusChannel($request),
            'statusCmt' => $this->genStatusComment($request),
            'group_channel_search' => $this->loadGroupChannelForSeach($request),
            'statusApi' => $this->genStatusApiAthena($request),
            'basNewStatus' => $this->genStatusBassNew($request),
            'channelManageType' => $this->genChannelManageType($request),
            'channelUploadType' => $this->genChannelUploadType($request),
            'channelWakeupType' => $this->genChannelWakeupType($request),
            'channelType' => $this->loadChannelType($request),
            'channelGenre' => $this->loadChannelGenre($request),
            'channelTags' => $this->loadChannelTags($request, 1),
            'channelSubGenre' => $this->loadChannelSubGenre($request),
            'listGroupChannel' => $this->loadGroupChannel(),
            'brand' => $this->genIsReband($request),
            'studio' => $this->genStudioStatus($request),
            'statusHubs' => $this->genStatusHubs($request),
            'isSync' => $this->genIsSync($request),
            'subTracking' => $this->genSubTracking($request),
            'level' => $this->genLevel($request),
            'listusercode' => $listUser,
            'brandingChannel' => $this->loadBrandingChannel($request),
            'email' => $this->loadMail(),
            'isChangeInfo' => $this->genIsChangeInfo($request),
            'isUpdateOtp' => $this->genIsUpdateOpt($request),
            'stats' => $stats,
            'gmailCounts' => $gmailCounts,
            'errorCountChangeInfo' => $errorCountChangeInfo,
        ]);
    }

    public function epidIndex(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name |indexEpid|request=" . json_encode($request->all()));
        if ($request->is_admin_music || $request->is_supper_admin) {
            $datas = AccountInfo::where('del_status', 0)->whereNotNull("epid_status")->orderBy("epid_time", "desc");
            $listUser = $this->genListUserForMoveChannel($user, $request, 2);
        } else {
            $datas = AccountInfo::where('user_name', $user->user_code)->where('del_status', 0)->whereNotNull("epid_status")->orderBy("epid_time", "desc");
            $listUser = [];
        }
        $limit = 30;
        $queries = [];

        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        } else {
            $request->limit = 30;
        }
        if (isset($request->c1) && $request->c1 != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('chanel_name', 'like', '%' . $request->c1 . '%')->orWhere('id', $request->c1)->orWhere('note', 'like', '%' . $request->c1 . '%')->orWhere('chanel_id', 'like', '%' . $request->c1 . '%');
                if (Utils::containString($request->c1, ",")) {
                    $c1 = explode(',', $request->c1);
                    $arrayChannel = [];
                    foreach ($c1 as $arr) {
                        if ($arr != "") {
                            $arrayChannel[] = trim($arr);
                        }
                    }
                    $q->orWhereIn("chanel_id", $arrayChannel)->orWhereIn("id", $arrayChannel)->orWhereIn("note", $arrayChannel);
                }
            });
            $queries['c1'] = $request->c1;
        }
        if (isset($request->c5) && $request->c5 != '-1') {
            $datas = $datas->where('user_name', $request->c5);
            $queries['c5'] = $request->c5;
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);
        foreach ($datas as $data) {
            $data->view_pending = $data->getExtraValue("view_pending") ?: 0;
            $data->view_approved = $data->getExtraValue("view_approved") ?: 0;
            $data->sub_pending = $data->getExtraValue("sub_pending") ?: 0;
            $data->sub_approved = $data->getExtraValue("sub_approved") ?: 0;
            $data->reward_name = "KPI not calculated yet";
            $data->reward_money = 0;
            $data->reward_mooncoin = 0;
            $data->reward_color = "text-warning";
            $rewards = $data->getExtraValue("rewards") ?: null;
            Log::info(json_encode($rewards));
            if ($rewards != null) {
                $data->reward_color = "text-danger";
                $data->reward_mooncoin = $rewards['moon_coin'];
                $data->reward_name = $rewards['name'];
                $data->reward_money = $rewards['cash'];
            }
            if ($data->reward_money > 0) {
                $data->reward_color = "text-success";
            }
            $data->inc_percent = 0;
            if ($data->intro_outro != null) {
                $tmp = json_decode($data->intro_outro);
                $data->inc_percent = $tmp->daily_inc_per;
                $data->inc_time = !empty($tmp->daily_scan_time) ? Utils::calcTimeText($tmp->daily_scan_time) : "";
            }
        }
        return view("components.channel_epid", [
            'datas' => $datas,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            'status' => $this->genStatusChannel($request),
            'listusercode' => $listUser,
        ]);
    }

    public function epidStatus(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|epidStatus|request=" . json_encode($request->all()));
        $status = AccountInfo::STATUS_PENDING;
        if ($request->is_admin_music) {
            if (isset($request->status)) {
                $status = $request->status;
            }
            $channel = AccountInfo::where("id", $request->id)->first();
        } else {
            $channel = AccountInfo::where("id", $request->id)->whereNull("epid_status")->where("user_name", $user->user_code)->first();
        }
        if (!$channel) {
            return response()->json(["status" => "success", "error" => "Not found info"]);
        }
        $channel->epid_status = $status;
        if ($status == AccountInfo::STATUS_PENDING || $status == AccountInfo::STATUS_EPID_APPROVED) {
            $channel->epid_time = time();
            $channel->setExtraValue("view_$status", $channel->view_count);
            $channel->setExtraValue("sub_$status", $channel->subscriber_count);
        }
        $statusHistory = $channel->getExtraValue('epid_status_history', []);
        $statusHistory[] = [
            'user_name' => $user->user_name,
            'status' => $status,
            'timestamp' => time(),
            'date' => Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')->toDateTimeString(),
            'note' => ""
        ];
        $channel->setExtraValue('epid_status_history', $statusHistory);
        $channel->save();


        return response()->json(["status" => "success", "message" => "Success"]);
    }

    function ajaxRecheckChannel($id) {
        $user = Auth::user();
        Log::info($user->user_name . '|ajaxRecheckChannel|id=' . $id);
//        $status;
//        $content = array();
////        $accountReup = AccountReup::where('user_name', $user->user_name)->first();
//        try {
//            
//            if (!isset($id)) {
//                array_push($content, trans('label.message.notHasId'));
//            } else {
//                //kiểm tra xem id có thuộc user đang đăng nhập ko
//                $data = AccountInfo::find($id);
//                $views = $data->view_count;
//                $subs = $data->subscriber_count;
//                if ($data != null) {
//                    if (!in_array('20', explode(",", $user->role))) {
//                        if ($data->user_name != $user->user_code) {
//                            array_push($content, trans('label.message.notHasRole'));
//                        }
//                    }
//                    //kiểm tra del_status 
////                    if ($data->del_status == 1) {
////                        array_push($content, trans('label.message.deletedChannel'));
////                    }
//                } else {
//                    array_push($content, trans('label.message.notHasData'));
//                }
//            }
//
//            if (count($content) != 0) {
//                $status = "error";
//            } else {
//                $status = "success";
//                //kiểm tra xem kênh còn sống hay chết
//                $dataInfo = YoutubeHelper::getChannelInfoV2($data->chanel_id);
//                if ($dataInfo["status"] == 1) {
//                    //kênh con sống
//                    array_push($content, trans('label.message.channelAlive'));
//                    $data->status = 1;
//                    if ($dataInfo["channelName"] != $data->chanel_name && $dataInfo["channelName"] != "") {
//                        $data->chanel_name = $dataInfo["channelName"];
//                    }
//                    if ($dataInfo["handle"] != $data->handle && $dataInfo["handle"] != "") {
//                        $data->handle = $dataInfo["handle"];
//                    }
//                    $data->view_count = $dataInfo["views"];
//                    $data->subscriber_count = $dataInfo["subscribers"];
////                    Log::info(json_encode($dataInfo));
//
//                    $thumb = "";
//                    if ($data->channel_clickup == null) {
////                    $thumbItem = \App\Common\Network\ProxyHelper::get("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$data->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
//                        $thumbItem = ProxyHelper::get("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$data->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyB0QH1MoohfCLR746NU5hNffzGPMDMAAxQ");
////                    $thumbItem =  RequestHelper::getUrl("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$data->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
//                        if ($thumbItem != null && $thumbItem != "") {
//                            $items = json_decode($thumbItem);
//                            if (!empty($items->items[0]->snippet->thumbnails->medium->url)) {
//                                $thumb = $items->items[0]->snippet->thumbnails->medium->url;
//                                $data->channel_clickup = $thumb;
//                            }
//                        }
//                    }
//
//                    $data->save();
//                } elseif ($dataInfo["status"] == 0) {
//                    $data->increasing = 0;
//                    $data->status = 0;
//                    $data->save();
//                    $status = "error";
//                    array_push($content, trans('label.message.channelDied'));
//                } elseif ($dataInfo["status"] == 2) {
//                    $status = "error";
//                    array_push($content, "Got error when scan channel");
//                }
//            }
//            
//            
//           if (count($content) != 0) {
//               return array('status' => $status, 'content' => $content);
//           }
//           
//           $scan = new ChannelScanner();
//           
//           
//            
//            
//        } catch (Exception $exc) {
//            Log::info($exc->getTraceAsString());
//            $status = "error";
//            array_push($content, trans('label.message.error'));
//        }
//        return array('status' => $status, 'content' => $content);

        if (in_array('20', explode(",", $user->role))) {
            $channel = AccountInfo::where("id", $id)->first();
        } else {
            $channel = AccountInfo::where("id", $id)->where("user_name", $user->user_code)->first();
        }
        if (!$channel) {
            return response()->json(["status" => "error", "message" => "Not found info"]);
        }
        $scan = new ChannelScanner();
        $channelData = $scan->getChannelInfo($channel);
        if ($channelData['scanStatus'] == 2) {
            return response()->json(["status" => "error", "message" => "System busy, try again later"]);
        }
        if ($channelData['scanStatus'] == 0) {
            return response()->json(["status" => "error", "message" => "Channel is dead"]);
        }
        // Cập nhật thông tin lượt xem
        $viewsData = $scan->updateViewsData($channelData, true);

        // Cập nhật thống kê và trạng thái
        $statsData = $scan->updateChannelStats($viewsData, true);

        $growthPercentage = 0;
        if (isset($statsData['stats']->daily_inc_per)) {
            $growthPercentage = $statsData['stats']->daily_inc_per;
        }
        return response()->json([
                    "status" => "success",
                    "message" => "Channel is active. Updated views: {$statsData['currentViews']}, subscribers: {$statsData['currentSub']}, views increase: {$viewsData['currentData']->increasing}",
                    "data" => [
                        "channel_name" => $statsData['channel']->chanel_name,
                        "channel_id" => $statsData['channel']->chanel_id,
                        "views" => $statsData['currentViews'],
                        "subscribers" => $statsData['currentSub'],
                        "views_increase" => $viewsData['currentData']->increasing,
                        "growth_percentage" => $growthPercentage,
                        "last_checked" => "Just now"
                    ]
        ]);
    }

    function ajaxChannel(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . 'ChannelManagementController.ajaxChannel|request=' . json_encode($request->all()));
        $status = "error";
        $content = array();
        try {
            if (!isset($request->chkChannelAll)) {
                array_push($content, "You have to choose channel");
                return array('status' => $status, 'content' => $content);
            } else {
                $listChannelIds = $request->chkChannelAll;
            }
            $log = "";
            if ($request->action == 1) {
                //move to group
                if (!isset($request->action_group_channel) || $request->action_group_channel == '-1') {
                    array_push($content, "You have to choose Group Channel");
                    return array('status' => $status, 'content' => $content);
                }
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name move to group $request->action_group_channel";
                $result = AccountInfo::where('user_name', $user->user_code)->whereIn('id', $listChannelIds)->update(['group_channel_id' => $request->action_group_channel,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                $status = "success";
                array_push($content, str_replace(':values', $result, "Add to group success :values channels"));
                return array('status' => $status, 'content' => $content, "reload" => 1);
            } else if ($request->action == 2) {
                //move channel
                if (!in_array('20', explode(",", $user->role))) {
                    array_push($content, 'You are not allowed to use these functions');
                    return array('status' => $status, 'content' => $content);
                }
                if ($request->action_user == "-1") {
                    array_push($content, "You have to choose user");
                    return array('status' => $status, 'content' => $content);
                }

                $userMove = User::where("user_code", $request->action_user)->first();
                if (!$userMove) {
                    array_push($content, "User is not exists");
                    return array('status' => $status, 'content' => $content);
                }
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name move to user $userMove->user_name";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['user_name' => $request->action_user,
                    'emp_code' => $userMove->customer_id,
                    'expire_get_pass' => time() + 5 * 3600,
                    'group_channel_id' => 0,
                    'wakeup_type' => 2,
                    'is_automusic_v2' => 1,
                    'confirm_time' => gmdate("Ymd", time()),
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Move success :values channels"));

                //thay đổi template trên studio
                foreach ($listChannelIds as $idChannel) {
                    $temp = AccountInfo::where("id", $idChannel)->first();
                    $pos = strripos($request->action_user, '_');
                    $us = substr($request->action_user, 0, $pos);
                    $res = RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/template/user/update", ["channel_id" => $temp->chanel_id, "user_name" => $us]);
                    Log::info("ajaxChannel move channel :$temp->chanel_id to user $us " . json_encode($res));
                }
                return array('status' => "success", 'content' => $content, "reload" => 1);
            } else if ($request->action == 3) {
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name check views";
                $channels = AccountInfo::whereIn("id", $listChannelIds)->get();
                foreach ($channels as $channel) {
                    $channel->log = $channel->log . $log;
                    $dataInfo = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
                    if ($dataInfo["status"] == 1) {
                        $channel->status = 1;
                        $channel->chanel_name = $dataInfo["channelName"];
                        $channel->view_count = $dataInfo["views"];
                        $channel->subscriber_count = $dataInfo["subscribers"];
                        $thumb = "";
//                        $thumbItem = \App\Common\Network\ProxyHelper::get("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channel->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
                        if ($channel->channel_clickup == null) {
                            $thumbItem = ProxyHelper::get("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channel->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyB0QH1MoohfCLR746NU5hNffzGPMDMAAxQ");
//                        $thumbItem = RequestHelper::getUrl("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channel->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
                            if ($thumbItem != null && $thumbItem != "") {
                                $items = json_decode($thumbItem);
                                if (!empty($items->items[0]->snippet->thumbnails->medium->url)) {
                                    $thumb = $items->items[0]->snippet->thumbnails->medium->url;
                                    $channel->channel_clickup = $thumb;
//                                Log::info($thumb);
                                }
                            }
                        }
                    } elseif ($dataInfo["status"] == 0) {
                        $channel->status = 0;
                    }
                    $channel->save();
                }
                array_push($content, "Finish check " . count($channels) . " channels");
                return array('status' => 'success', 'content' => $content, "reload" => 1);
            } else if ($request->action == 4) {
                //kênh show trên musics v3
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_automusic_v2=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_automusic_v2' => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 31) {
                //kênh hide trên musics v3
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_automusic_v2=0";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_automusic_v2' => 0,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 5) {
                //kênh manual
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_music_channel=1,upload_type=1,wakeup_type=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_music_channel' => 1,
                    "upload_type" => 1, "wakeup_type" => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);

                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 6) {
                //kênh auto
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_music_channel=2,upload_type=2,wakeup_type=2";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_music_channel' => 2,
                    "upload_type" => 2, "wakeup_type" => 2,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 7) {
                //kênh upload manual
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set upload_type=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['upload_type' => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 8) {
                //kênh upload auto, dung bas hoac api
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set upload_type=2";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['upload_type' => 2,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 9) {
                //kênh chạy wakup manual
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set wakeup_type=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['wakeup_type' => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 10) {
                //kênh chạy wakup auto
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set wakeup_type=2";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['wakeup_type' => 2,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 11) {
                //kênh disable cross
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_cross_post=0";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_cross_post' => 0,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 12) {
                //kênh endable cross
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_cross_post=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_cross_post' => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 13) {
                //kênh enable promos lyric
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_promos_lyric=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_promos_lyric' => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 14) {
                //kênh disable promos lyric
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_promos_lyric=0";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_promos_lyric' => 0,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 15) {
                //encable promos mix
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_promos_246=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_promos_246' => 1,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 16) {
                //disable promos mix
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_promos_246=0";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['is_promos_246' => 0,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 17) {
                //config
//                if ($request->channel_type == '-1') {
//                    return response()->json(array("status" => "error", "message" => "Please select the Channel Type"));
//                }
//                if ($request->channel_genre == '-1') {
//                    return response()->json(array("status" => "error", "message" => "Please select the Channel Genre"));
//                }
//                $channelSubgenre = $request->channel_subgenre;
//                if (count($channelSubgenre) == 0) {
//                    return response()->json(array("status" => "error", "message" => "Please select the Channel SubGenre"));
//                }
                $result = 0;
                if (isset($request->config_channel_id) && $request->config_channel_id != "") {
                    $result = 1;
                    $configChannelId = trim($request->config_channel_id);
                    if (count($listChannelIds) > 1) {
                        array_push($content, "You can only choose 1 channel");
                        return array('status' => 'error', 'content' => $content);
                    }
                    $ch = Utils::extractYoutubeChannelId($configChannelId);
                    if ($ch == null) {
                        array_push($content, "Channel Id is invalid");
                        return array('status' => 'error', 'content' => $content);
                    }
                    $channel = AccountInfo::whereIn('id', $listChannelIds)->first();

                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set channel_id=$ch";
                    $channel->log = $channel->log . PHP_EOL . $log;
                    $channel->chanel_id = $ch;
                    $channel->save();
                    array_push($content, str_replace(':values', $result, "Success :values channels"));
                }
                if (isset($request->channel_genre) && $request->channel_genre != "-1") {
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set channel_genre=$request->channel_genre";
                    $result = AccountInfo::whereIn('id', $listChannelIds)->update([
                        "channel_genre" => $request->channel_genre,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
                    array_push($content, str_replace(':values', $result, "Success :values channels"));
                }
                if (isset($request->upload_alert) && $request->upload_alert != "-1") {
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set upload_alert=$request->upload_alert";
                    $result = AccountInfo::whereIn('id', $listChannelIds)->update([
                        "upload_alert" => $request->upload_alert,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
                    array_push($content, str_replace(':values', $result, "Success :values channels"));
                }
                if (isset($request->tags) && count($request->tags) > 0) {
                    $tags = strtoupper(implode(",", $request->tags));
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set tags=$tags";
                    $result = AccountInfo::whereIn('id', $listChannelIds)->update([
//                    'channel_type' => $request->channel_type,
//                    "channel_genre" => $request->channel_genre,
//                    "channel_subgenre" => strtoupper(implode(",", $request->channel_subgenre)),
                        "tags" => $tags,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
                    array_push($content, str_replace(':values', $result, "Success :values channels"));
                }
                if (isset($request->otp_key) && $request->otp_key != "") {
                    $result = 1;
                    $optKey = trim($request->otp_key);
                    $optKey = str_replace(" ", "", $optKey);
                    if (count($listChannelIds) > 1) {
                        array_push($content, "You can only choose 1 channel");
                        return array('status' => 'error', 'content' => $content);
                    }
                    $channel = AccountInfo::whereIn('id', $listChannelIds)->first();
                    $channel->otp_key = $optKey;

                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set otp_key=$optKey";
                    $channel->log = $channel->log . PHP_EOL . $log;
                    $channel->save();

                    $input = array("gmail" => $channel->note, "otp_key" => $optKey);
                    RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input);
                    array_push($content, str_replace(':values', $result, "Set opt success :values channels"));
                }
                if (isset($request->profile_name) && $request->profile_name != "") {
                    $result = 1;
                    $profileName = trim($request->profile_name);
                    if (count($listChannelIds) > 1) {
                        array_push($content, "You can only choose 1 channel");
                    }
                    $channel = AccountInfo::whereIn('id', $listChannelIds)->first();

                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set moonshots profile name=$profileName";
                    $channel->log = $channel->log . PHP_EOL . $log;
                    $channel->save();

                    $input = array("id" => $channel->gologin, "name" => $profileName);
                    RequestHelper::callAPI2("POST", "http://profile.autoseo.win/profile-tmp/update/name", $input);
                    array_push($content, str_replace(':values', $result, "Success :values channels"));
                }
                if (isset($request->expire_get_pass) && $request->expire_get_pass != "-1") {
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set expire_get_pass +$request->expire_get_pass day";
                    $result = AccountInfo::whereIn('id', $listChannelIds)->update([
                        "expire_get_pass" => time() + $request->expire_get_pass * 86400,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
                    array_push($content, str_replace(':values', $result, "Set success :values channels"));
                }
                if (isset($request->version) && $request->version != "-1") {
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set version=$request->version";
                    $result = AccountInfo::whereIn('id', $listChannelIds)->update([
                        "version" => $request->version,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
                    array_push($content, str_replace(':values', $result, "Set success :values channels"));
                }
                if (isset($request->sub_tracking) && $request->sub_tracking != "-1") {
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set sub_tracking=$request->sub_tracking";
                    $result = AccountInfo::whereIn('id', $listChannelIds)->update([
                        "sub_tracking" => $request->sub_tracking,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
                    array_push($content, str_replace(':values', $result, "Set success :values channels"));
                }
                if (isset($request->remove_error) && $request->remove_error) {
                    $dataChannels = AccountInfo::whereIn('id', $listChannelIds)->get();
                    $count = 0;
                    $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name remove error log";
                    foreach ($dataChannels as $account) {
                        $count++;
                        $currentLog = $account->log;
                        $newLog = preg_replace('/.*change info fail.*\n?/', '', $currentLog);
                        $newLog = $newLog . $log;
                        $account->message = null;
                        $account->last_change_pass = 0;
                        $account->log = $newLog;
                        $account->save();
                    }
                    array_push($content, str_replace(':values', $count, "Set success :values channels"));
                }
            } else if ($request->action == 18) {
                //kênh upload auto, dung api
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $count = 0;
                $status = "success";
                foreach ($channels as $channel) {
                    if ($channel->channel_code_orfium == null && $channel->channel_code_camta == null && $channel->channel_code_adobe == null) {
                        $status = "error";
                        $us = Utils::userCode2userName($channel->user_name);
                        array_push($content, "$us $channel->chanel_name have to add Orfium API or Googla API");
                        continue;
                    }
                    $log = $channel->log;
                    $log .= PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set upload_type=2,version=2";
                    $channel->log = $log;
                    $channel->upload_type = 2;
                    $channel->version = 2;
                    $channel->save();
                    $count++;
                }

//                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['upload_type' => 2, "version" => 2,
//                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $count, "Set success :values channels"));
                return array('status' => $status, 'content' => $content);
            } else if ($request->action == 19) {
                //set time vip render cho kênh
                $vipTime = time() + $request->vip_day * 86400;
                $vipTimeText = gmdate("Y-m-d H:i:s", $vipTime + 7 * 3600);
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set expire_vip_render=$vipTimeText";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['expire_vip_render' => $vipTime,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 20) {
                //turn off hubs
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $count = 0;
                $status = "success";
                foreach ($channels as $channel) {
                    $result = RequestHelper::getRequest("http://api-magicframe.automusic.win/hub/update-status-by-channel/$channel->chanel_id/3");
                    if ($result == "ok") {
                        $log = $channel->log;
                        $log .= PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name turn off hub";
                        $channel->log = $log;
                        $channel->upload_alert = 0;
                        $channel->turn_off_hub = 1;
                        $channel->save();
                        $count++;
                        //2025/02/27 off tự động upload
                        RequestHelper::getRequest("http://api-magicframe.automusic.win/job/auto-upload/stop-by-channel/$channel->chanel_id");
                    }
                }
                array_push($content, str_replace(':values', $count, "Set success :values channels"));
            } else if ($request->action == 21) {
                //turn on hubs
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $count = 0;
                $status = "success";
                foreach ($channels as $channel) {
                    RequestHelper::callAPI2("GET", "http://api-magicframe.automusic.win/hub/update-status-by-channel/$channel->chanel_id/1", []);
                    $log = $channel->log;
                    $log .= PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name turn on hub";
                    $channel->log = $log;
                    $channel->upload_alert = 1;
                    $channel->turn_off_hub = 0;
                    $channel->save();
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Set success :values channels"));
            } else if ($request->action == 22) {
                //set kenh boom vip
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_boomvip=1,upload_alert=0, expire_get_pass " . (time() + 30 * 86400);
                $result = AccountInfo::whereIn('id', $listChannelIds)->where("is_boomvip", "<>", 1)->update(['is_boomvip' => 1, 'boomvip_time' => time(), 'upload_alert' => 0, 'expire_get_pass' => time() + 30 * 86400,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 23) {
                //set bỏ kenh boom vip
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set is_boomvip=2,upload_alert=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->where("is_boomvip", 1)->update(['is_boomvip' => 2, 'upload_alert' => 1, 'expire_get_pass' => 0,
                    "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Set success :values channels"));
            } else if ($request->action == 24) {
                //autologin
                $count = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                Log::info("autologin: " . count($channels));
                $client = $request->client;
                foreach ($channels as $channel) {
                    if ($channel->note == null) {
                        continue;
                    }
                    //gọi api để set proxy đăng nhập tại vn để tạo api,tạo xong thì update lại
                    //2023/03/29 hòa confirm tạm thời ko dùng prozy_v6, nếu dùng thì truyền vào proxy6
                    if ($client == "dev2-new") {
                        $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/1/no_proxy";
                        $bas2 = RequestHelper::callAPI("GET", $url2, []);
                        $is_proxy6 = 0;
                    } else {
                        $url = "http://bas.reupnet.info/profile/assign/group-auto/$channel->note/$client/no_proxy";
                        $bas2 = RequestHelper::callAPI("GET", $url, []);
                        $is_proxy6 = 0;
                    }

                    if (!empty($bas2->ip)) {
                        $channel->ip_server = $bas2->ip;
                        $channel->save();
                    }
                    $taskLists = [];
                    $login = (object) [
                                "script_name" => "profile",
                                "func_name" => "login",
                                "params" => []
                    ];
                    $commit = (object) [
                                "script_name" => "profile",
                                "func_name" => "profile_commit",
                                "params" => []
                    ];
//                    $api = (object) [
//                                "script_name" => "api",
//                                "func_name" => "create",
//                                "params" => []
//                    ];
                    $changeInfo = (object) [
                                "script_name" => "profile",
                                "func_name" => "change_info",
                                "params" => []
                    ];

                    $taskLists[] = $login;
                    $taskLists[] = $commit;
//                    $taskLists[] = $api;
                    //2023/07/17 sang confirm them doi mk
//                    $taskLists[] = $changeInfo;

                    $req = (object) [
                                "gmail" => $channel->note,
                                "task_list" => json_encode($taskLists),
                                "studio_id" => $channel->id,
                                "run_time" => 0,
                                "type" => 60,
                                "piority" => 50,
                                "proxy" => $request->proxy,
                                //2023/03/02 hoa confirm them is_proxy6
                                "is_proxy6" => $is_proxy6,
                                "call_back" => "http://automusic.win/callback/login",
                    ];
                    Log::info("Login bas new $channel->note" . json_encode($req));
                    $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                    if (!empty($res->job_id)) {
                        $jobId = $res->job_id;
                        $channel->bas_new_id = $jobId;
                        $channel->bas_new_status = 1;
                        $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name run auto login";
                        $channel->save();
                        $count++;
                    }
                }
                array_push($content, str_replace(':values', $count, "Set success :values channels"));
            } else if ($request->action == 25) {
                //chay api moonshots
                $count = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                foreach ($channels as $channel) {
//                    if ($channel->gologin != null) {
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name run make api";
                    $channel->api_status = 0;
                    $channel->save();
                    $count++;
//                    }
                }
                array_push($content, str_replace(':values', $count, "Success :values channels"));
            } else if ($request->action == 26) {
                //chay api moonshots
                $count = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                foreach ($channels as $channel) {
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name clear profile_id moonshots";
                    $channel->gologin = null;
                    $channel->save();
                    $input2 = array("gmail" => $channel->note, "profile_id" => "");
                    RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input2);
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success :values channels"));
            } else if ($request->action == 27) {
                $count = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $client = $request->client_27;
                foreach ($channels as $channel) {
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set ip $client";
                    $channel->save();
//                    $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/1";
//                    RequestHelper::callAPI("GET", $url2, []);

                    if ($client == "dev2-new") {
                        $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/1/no_proxy";
                        $bas2 = RequestHelper::callAPI("GET", $url2, []);
                    } else {
                        $url = "http://bas.reupnet.info/profile/assign/group-auto/$channel->note/$client/no_proxy";
                        $bas2 = RequestHelper::callAPI("GET", $url, []);
                    }
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success :values channels"));
            } else if ($request->action == 28) {
                //sync cookie
                $count = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                foreach ($channels as $channel) {
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name sync cookie";
                    $cb = "http://automusic.win/callback/sync_cookie?us=$user->user_name";
                    $this->syncCookie($channel, $cb);
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success :values channels"));
            } else if ($request->action == 29) {
                //xoa profile tren bas
                $count = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                foreach ($channels as $channel) {
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name clear profile_id bas";
                    $channel->save();
                    $url = "http://bas.reupnet.info/profile/remove/$channel->note";
                    $bas2 = RequestHelper::callAPI("GET", $url, []);
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success :values channels"));
            } else if ($request->action == 30) {
                //tạo kênh mới
                $count = 0;
                $index = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $total = count($channels);
                foreach ($channels as $channel) {
                    $index++;
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name create channel bas";
                    $channel->save();

                    //đổi sang ip dev2_new
                    $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/1/no_proxy";
                    $bas2 = RequestHelper::callAPI("GET", $url2, []);

                    //tạo lệnh tạo kênh trống
                    $handle = "";
                    $channelName = "";
                    $taskLists = [];
                    $login = (object) [
                                "script_name" => "profile",
                                "func_name" => "login",
                                "params" => []
                    ];
                    $createChannel = (object) [
                                "script_name" => "upload",
                                "func_name" => "create_channel",
                                "params" => [
                                    (object) [
                                        "name" => "first_name",
                                        "type" => "string",
                                        "value" => $channelName
                                    ],
                                    (object) [
                                        "name" => "last_name",
                                        "type" => "string",
                                        "value" => $handle
                                    ]
                                ]
                    ];
                    $changeInfo = (object) [
                                "script_name" => "profile",
                                "func_name" => "change_info",
                                "params" => []
                    ];
                    $profileCommit = (object) [
                                "script_name" => "profile",
                                "func_name" => "profile_commit",
                                "params" => []
                    ];
                    $taskLists[] = $login;
                    $taskLists[] = $createChannel;
                    //2023/06/21 sang confirm tạm thời bỏ changeinfo
//                    $taskLists[] = $changeInfo;
                    $taskLists[] = $profileCommit;

                    $req = (object) [
                                "gmail" => $channel->note,
                                "task_list" => json_encode($taskLists),
                                "run_time" => 0,
                                "type" => 65,
                                "studio_id" => $channel->id,
                                "piority" => 50,
                                "call_back" => "http://automusic.win/callback/channel/create"
                    ];

                    $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                    error_log("createChannel $request->genre : $index/$total " . json_encode($bas));
                    $channel->bas_new_status = 1;
                    $channel->bas_new_id = $bas->job_id;
//                    $channel->message = 'kenh can doi mat khau';
                    $channel->save();
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success :values channels"));
            } else if ($request->action == 32) {
                //đổi pass
                $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
                $count = 0;
                $index = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $total = count($channels);
                $time = time();
                foreach ($channels as $channel) {
                    //đã đổi pass 30 ngày rồi
                    if ($channel->last_change_pass == null || $channel->last_change_pass < $thirtyDaysAgo) {

                        $this->sendCommandChangePass($channel, $time);
                        $time = (60 * 30) + $time;
                        $count++;
                    }
                }
                array_push($content, str_replace(':values', $count, "Success send command :values channels"));
            } else if ($request->action == 33) {
                //đổi pass
                $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
                $count = 0;
                $index = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $total = count($channels);
                foreach ($channels as $channel) {
                    $this->sendCommandAuth($channel);
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success send command :values channels"));
            } else if ($request->action == 34) {
                //thêm kênh lên social
                $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
                $count = 0;
                $index = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $total = count($channels);
                foreach ($channels as $channel) {

                    $this->addChannelToCommentSystem($channel);
                    $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name add to social";
                    $channel->save();
                    $count++;
                }
                array_push($content, str_replace(':values', $count, "Success add :values channels"));
            } else if ($request->action == 35) {
                //đổi info
                $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
                $count = 0;
                $index = 0;
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $total = count($channels);
                $time = time();
                foreach ($channels as $channel) {
                    //đã đổi info + pass 30 ngày rồi
                    if ($channel->last_change_pass == null || $channel->last_change_pass < $thirtyDaysAgo) {
                        //nếu ip là dev2_new thì phải đặt time cách nhau 30 phút,tránh bị spam
                        $this->sendCommandChangeInfo($channel, 0);
                        $time = (60 * 30) + $time;
                        $count++;
                    }
                }
                array_push($content, str_replace(':values', $count, "Success send command :values channels"));
            } else if ($request->action == 36) {
                //set comment manual
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set status_cmt=0";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['status_cmt' => 0, "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Success :values channels"));
            } else if ($request->action == 37) {
                //set comment manual
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set status_cmt=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['status_cmt' => 1, "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Success :values channels"));
            } else if ($request->action == 38) {
                //delete channel
                $channels = AccountInfo::whereIn('id', $listChannelIds)->get();
                $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name set del_status=1";
                $result = AccountInfo::whereIn('id', $listChannelIds)->update(['del_status' => 1, "log" => DB::raw("CONCAT(log,'$log')")]);
                array_push($content, str_replace(':values', $result, "Success :values channels"));
            }
//            Log::info(DB::getQueryLog());
            //chuyển kênh sang channel_bass để tính tiền
            $date = gmdate("Ym", time() + 7 * 3600);
            if ($request->action == 5 || $request->action == 6) {
                DB::insert("INSERT ignore INTO channel_bass (team, user_name, user_code, channel_id,channel_name, status, subscribes, videos, views, increasing, 
email, next_time_scan, del_status, channel_create_date, client, period, source) 
select 'bassteam','bassteam', user_name,chanel_id,chanel_name,status,subscriber_count,video_count,view_count,
increasing,note,0,del_status,0,1,$date,1 from accountinfo where is_music_channel in (1,2) and chanel_id <> 'UCRoI_0geJx6HuUQZzvlUZqw'");
            }
        } catch (QueryException $exc) {
            Log::info($exc->getTraceAsString());
            $status = "error";
            array_push($content, trans('label.message.error'));
        }
//        Log::info(DB::getQueryLog());
        return array('status' => 'success', 'content' => $content);
    }

    function channelConfig(Request $request) {
        $offset = 0;
        $listArtistsImg = DB::select('select artists  from image where type = 2 group by artists order by artists');
        $listImg = DB::select('select id,link  from image where type = 2 limit 15 offset ' . $offset);
        return view('components.channelconfig', [
            'list_image' => $listImg,
            'list_artists_img' => $listArtistsImg,
        ]);
    }

    function ajaxSyncAthena($id) {
        $user = Auth::user();
        Log::info($user->user_name . '|ajaxSyncAthena|id=' . $id);
        try {
            if (!isset($id)) {
                return array('status' => "error", 'content' => trans('label.message.notHasId'));
            }
            //kiểm tra xem id có thuộc user đang đăng nhập ko
            $data = AccountInfo::find($id);

            if ($data != null) {
                if (!in_array("1", explode(",", $user->role))) {
                    if ($data->user_name != $user->user_code) {
                        return array('status' => "error", 'content' => trans('label.message.notHasRole'));
                    }
                }
            } else {
                return array('status' => "error", 'content' => trans('label.message.notHasData'));
            }
            if ($data->is_sync == 1) {
                $data->is_sync = 0;
                $data->is_music_channel = 3;
//                $data->is_automusic_v2 = 0;
//                $data->del_status = 1;
                $btnText = "Sync";
                $btnColor = "success";
                $btnTooltip = "Sync channel to athena";
                $btnIcon = "upload";
                RequestHelper::callAPI2("POST", "http://plla.autoseo.win/channel/sync", ["channel_id" => $data->chanel_id, "is_sync" => 0, "is_music_channel" => 3]);
                RequestHelper::callAPI2("GET", "http://api-magicframe.automusic.win/hub/update-status-by-channel/$data->chanel_id/3", []);
            } else if ($data->is_sync == 0 || $data->is_sync == 3) {
                $data->is_music_channel = 2;
                $data->is_sync = 2;
                $data->is_automusic_v2 = 1;
                $btnText = "UnSync";
                $btnColor = "warning";
                $btnTooltip = "UnSync channel from athena";
                $btnIcon = "download";
                if ($data->channel_genre == null || $data->channel_genre == "") {
                    return array('status' => 'error', 'content' => "Update Channel Genre and Channel Subgenre First");
                }
            } else if ($data->is_sync == 2) {
                return array('status' => 'error', 'content' => "Wait for admin accept");
            }
            $log = $data->log;
            $log .= PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name confirmed sync $data->is_sync";
            $data->log = $log;
            $data->save();
        } catch (Exception $exc) {
            Log::info($exc->getTraceAsString());
            return array('status' => 'error', 'content' => trans('label.message.error'));
        }
        return array('status' => "success", 'content' => "Success", "btnText" => $btnText, "btnColor" => $btnColor, "btnTooltip" => $btnTooltip, "btnIcon" => $btnIcon);
    }

    public function saveBrandChannel(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|saveBrandChannel|request=' . json_encode($request->all()));
//        if ($request->channel_type == '-1') {
//            return response()->json(array("status" => "error", "message" => "Please select the Channel Type"));
//        }
//        if ($request->channel_genre == '-1') {
//            return response()->json(array("status" => "error", "message" => "Please select the Channel Genre"));
//        }
        $channel = AccountInfo::find($request->idBrand);
        if ($channel) {
            $gmail = ($channel->gmail == null ? $channel->note : $channel->gmail);
            $channelId = $channel->chanel_id;
            $ipEmail = "165.22.105.138";
            $rebrand = new RebrandChannelCmd();
            $rebrand->channel_id = $channelId;
            $rebrand->gmail = $gmail;
            $rebrand->ip_email = $ipEmail;
            $rebrand->private = $request->private == "on" ? 1 : 0;
            $rebrand->language = $request->language;
            $rebrand->category = $request->category;
            $rebrand->country = $request->country;
            $rebrand->keyword = $request->keyword;
            $rebrand->first_name = $request->firstName;
            $rebrand->last_name = $request->lastName;
            $rebrand->link_profile = $request->profile;
            $rebrand->link_banner = $request->banner;
            $rebrand->about_section = $request->about_section;
            $rebrand->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $rebrand->last_update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $rebrand->status = 0;
            $rebrand->save();
            $channel->is_rebrand = 1;
            $channel->save();

            //cập nhật status cho autobrand
            if ($request->brand_type == 2) {
                Log::info(base64_decode($request->brand_select));
                $data = explode("@;@", base64_decode($request->brand_select));
                $branding = BrandManager::where("id", $data[0])->first();
                $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                if ($branding) {
                    $branding->status_use = 1;
                    $log = $branding->log;
                    $log .= PHP_EOL . "$curr $user->user_name use this brand for channel $channelId";
                    $branding->log = $log;
                    $branding->save();
                }
            }
            //cộng số lượng about_section đã sử dụng
            if ($request->aboutSectionId != "") {
                $rebrandChannel = RebrandChannel::where("id", $request->aboutSectionId)->first();
                if ($rebrandChannel) {
                    $rebrandChannel->count = $rebrandChannel->count + 1;
                    $rebrandChannel->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                    $rebrandChannel->save();
                }
            }
        } else {
            return response()->json(array("status" => "error", "message" => "Not found channel"));
        }
        return response()->json(array("status" => "success", "message" => "Rebrand success"));
    }

    public function loadChannelInfo(Request $request) {
        $user = Auth::user();
        Log::info("|$user->user_name|loadChannelInfo|request=" . json_encode($request->all()));
        $accountInfo = AccountInfo::find($request->id);
        $temp = "<option value='-1'>" . trans('label.value.select') . "</option>";
        $brandOwner = "<option value=''>" . trans('label.value.select') . "</option>";
        $genres = $temp;
        $subgenre = $temp;
        $arrBranUs = ["hiepmusic" => "Hiep Design", "giangmusic" => "Giang Design"];
        $brandUsers = $accountInfo->brand_user != null ? explode(",", $accountInfo->brand_user) : [];

        foreach ($arrBranUs as $index => $data) {
            if (count($brandUsers) > 0) {
                foreach ($brandUsers as $brandUser) {
                    if ($brandUser == $index) {
                        $brandOwner .= "<option selected value='$index'>$data</option>";
                    } else {
                        $brandOwner .= "<option  value='$index'>$data</option>";
                    }
                }
            } else {
                $brandOwner .= "<option  value='$index'>$data</option>";
            }
        }

        $channelType = $temp;
        $channelTypeAll = DB::select("select distinct upper(channel_type) as channel_type from accountinfo where channel_type is not null and is_music_channel in(1,2)");
        foreach ($channelTypeAll as $data) {
            if ($data->channel_type == $accountInfo->channel_type) {
                $channelType .= "<option selected value='$data->channel_type'>$data->channel_type</option>";
            } else {
                $channelType .= "<option value='$data->channel_type'>$data->channel_type</option>";
            }
        }
        return array("channelType" => $channelType, "genre" => $genres, "subgenre" => $subgenre, "brandOwner" => $brandOwner);
    }

    public function saveChannelInfo(Request $request) {
        $user = Auth::user();
        Log::info("|$user->user_name|saveChannelInfo|request=" . json_encode($request->all()));
        if ($request->channel_type == '-1') {
            return response()->json(array("status" => "error", "message" => "Please select the Channel Type"));
        }
        if ($request->channel_genre == '-1') {
            return response()->json(array("status" => "error", "message" => "Please select the Channel Genre"));
        }
        $channelSubgenre = $request->channel_subgenre;
        if (count($channelSubgenre) == 0) {
            return response()->json(array("status" => "error", "message" => "Please select the Channel SubGenre"));
        }
        $channel = AccountInfo::find($request->idBrand);
        if ($channel) {
            $channel->brand_user = $request->brand_user;
            $channel->channel_type = strtoupper($request->channel_type);
            $channel->channel_genre = strtoupper($request->channel_genre);
            if (isset($request->channel_subgenre)) {
                $channel->channel_subgenre = strtoupper(implode(",", $request->channel_subgenre));
            }
            if (isset($request->tags)) {
                $channel->tags = strtoupper(implode(",", $request->tags));
            }
            $channel->save();
        } else {
            return response()->json(array("status" => "error", "message" => "Not found channel"));
        }
        return response()->json(array("status" => "success", "message" => "Set success"));
    }

    public function vipRender(Request $request) {
        $user = Auth::user();
        Log::info("|$user->user_name|vipRender|request=" . json_encode($request->all()));
        $response = RequestHelper::getRequest("http://api-magicframe.automusic.win/job/run-by-channel/$request->channel_id");
        return response()->json(array("status" => "success", "message" => "Success $response"));
    }

    public function randomAboutSection(Request $request) {
        return RebrandChannel::where("type", "about_section")->where("status", 1)->where("topic", $request->genre)->take(1)->inRandomOrder()->get();
    }

    public function goLogin(Request $request) {
        $user = Auth::user();
        Log::info("|$user->user_name|goLogin|request=" . json_encode($request->all()));
        if ($request->is_admin_music) {
            $accountInfo = AccountInfo::where("hash_pass", $request->hash)->first(['id', 'gologin', 'note', 'hash_pass']);
        } else {
            $accountInfo = AccountInfo::where("hash_pass", $request->hash)->where("user_name", $user->user_code)->first(['id', 'gologin', 'note', 'hash_pass']);
        }
        if (!$accountInfo) {
            return array("status" => "error");
        }
        //tự động assign sang ip mới
//        $url = "http://bas.reupnet.info/profile/assign/group-auto/$accountInfo->note/linux_bas_v2/no_proxy";
//        RequestHelper::callAPI("GET", $url, []);
        if ($accountInfo->gologin == null) {
            //kiểm tra trên automail đã có profile_id chưa,nếu có rồi thì đồng bộ về
            $input = array("gmail" => $accountInfo->note);
            $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
            if (!empty($mail->profile_id) && $mail->profile_id != null && $mail->profile_id != "") {
                $accountInfo->gologin = $mail->profile_id;
                $accountInfo->save();
            } else {
                $id = RequestHelper::getGologin("http://profile.autoseo.win/profile-tmp/get-avail", $accountInfo->note);
                $accountInfo->gologin = $id;
                $accountInfo->save();
                //update to automail
                $input2 = array("gmail" => $accountInfo->note, "profile_id" => $id);
                RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input2);
                //chuyển profile  sang ip mới
                $url2 = "http://bas.reupnet.info/profile/assign/manual/$accountInfo->note/2/no_proxy";
                RequestHelper::callAPI("GET", $url2, []);
            }
        }
        return $accountInfo;
    }

    public function getCodeLogin(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|getCodeLogin|request=" . json_encode($request->all()));
        if ($request->hash == null) {
            return array("status" => "error");
        }
        if ($request->is_admin_music) {
            $accountInfo = AccountInfo::where("hash_pass", $request->hash)->first();
        } else {
            $accountInfo = AccountInfo::where("hash_pass", $request->hash)->where("user_name", $user->user_code)->first();
        }

        if ($accountInfo) {
//            $url = "http://127.0.0.1:5701/otp/get?key=$accountInfo->otp_key";
//            $temp = RequestHelper::getUrl($url, 0);
//            Log::info("getCodeLogin $url $temp");
//            $code = json_decode($temp);
//            return array("status" => "success", "data" => $code->code);
            $code = GoogleOtp::getOtpCode($accountInfo->otp_key);
            Log::info("$user->user_name|getCodeLogin|$code");
            return array("status" => "success", "data" => $code);
        } else {
            return array("status" => "error");
        }
    }

    //lấy code từ mail recovery
    function getCodeRecovery(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|ChannelManagementController.getCodeRecovery|request=" . json_encode($request->all()));
        if ($request->hash == null) {
            return array("status" => "error");
        }
        if ($request->is_admin_music) {
            $accountInfo = AccountInfo::where("hash_pass", $request->hash)->first();
        } else {
            $accountInfo = AccountInfo::where("hash_pass", $request->hash)->where("user_name", $user->user_code)->first();
        }

        if ($accountInfo) {
            $input = array("gmail" => $accountInfo->note);
            $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
            Log::info(json_encode($mail));
            $reco = $mail->recovery_email;
            Log::info($reco);
            $data = RequestHelper::callAPI2("GET", "http://gmail.69hot.info/manager/google-reco/verify-mess/$reco/$accountInfo->note", []);
            Log::info(json_encode($data));
            $code = "Not Found";
            if (preg_match('/>(\d{6})</', $data->body, $match)) {
                $code = $match[1];
            }
            return array("status" => "success", "data" => $code);
        } else {
            return array("status" => "error");
        }
    }

    public function getCodeByMail(Request $request) {
        Log::info("|getCodeByMail|request=" . json_encode($request->all()));
        $platform = $request->header('platform');
        if ($platform != "autowin") {
            return ["status" => "error", "message" => "Wrong system!"];
        }
        $accountInfo = AccountInfo::where("note", $request->gmail)->first();
        if ($accountInfo) {
//            $temp = RequestHelper::getUrl("http://127.0.0.1:5701/otp/get?key=$accountInfo->otp_key", 0);
//            $code = json_decode($temp);
//            return array("status" => "success", "data" => $code->code);
            $code = GoogleOtp::getOtpCode($accountInfo->otp_key);
            return array("status" => "success", "data" => $code);
        } else {
            return array("status" => "error", "message" => "not found");
        }
    }

    public function insertOtpKey(Request $request) {
        $user = Auth::user();
        Log::info("|$user->user_name|insertOtpKey|request=" . json_encode($request->all()));
        if (!$request->is_admin_music) {
            $accountInfo = AccountInfo::where("id", $request->id)->where("user_name", $user->user_code)->first();
            if (!$accountInfo) {
                return array("status" => "error", "message" => "Not found data");
            }
        } else {

            $accountInfo = AccountInfo::where("id", $request->id)->first();
        }
//        $temp = RequestHelper::getUrl("http://127.0.0.1:5701/otp/get?key=$accountInfo->otp_key", 0);
//        $code = null;
//        if ($temp != null) {
//            $code = json_decode($temp);
//        }

        $otpKey = str_replace(" ", "", $request->otpkey);
        if ($otpKey == "") {
            return array("status" => "error", "message" => "OPT Key is invalid");
        }
        $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name insert otp key->change ip to foreign";
        $code = GoogleOtp::getOtpCode($otpKey);
        Log::info("code " . $code);

        //2024/10/30 update trên tất cả các kênh có chung email
        AccountInfo::where('note', $accountInfo->note)->update(['otp_key' => $otpKey, "is_add_otp" => 1, "log" => DB::raw("CONCAT(log,'$log')")]);
        $input = array("gmail" => $accountInfo->note, "otp_key" => $otpKey);
        RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input);
        //chuyển sang ip nước ngoài sau khi insert otp key
        $url = "http://bas.reupnet.info/profile/assign/group-auto/$accountInfo->note/linux_bas_v2/no_proxy";
        RequestHelper::callAPI("GET", $url, []);
//        return array("status" => "success", "data" => !empty($code->code) ? $code->code : "Error");
        return array("status" => "success", "data" => $code);
    }

    public function updateChannelId(Request $request) {
        $user = Auth::user();
        Log::info("|$user->user_name|updateChannelId|request=" . json_encode($request->all()));
        if ($request->is_admin_music) {
            $accountInfo = AccountInfo::find($request->id);
        } else {
            $accountInfo = AccountInfo::where("id", $request->id)->where("user_name", $user->user_code)->first();
        }
        if (!$accountInfo) {
            return array("status" => "error", "message" => "Not found data");
        }
        if (isset($request->channel_id)) {
            $chanelId = str_replace("https://www.youtube.com/channel/", "", $request->channel_id);
            $accountInfo->chanel_id = $chanelId;
            $accountInfo->log = $accountInfo->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name update channel_id=$chanelId";
        }
        if (isset($request->delete)) {
            $accountInfo->del_status = 1;
            $accountInfo->log = $accountInfo->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name update del_status=1";
        }
        $accountInfo->save();

        return array("status" => "success", "message" => "Success");
    }

    public function updateGologinId(Request $request) {
        Log::info("|updateGologinId|request=" . json_encode($request->all()));
        $accountInfo = AccountInfo::where("note", $request->gmail)->first();
        if (!$accountInfo) {
            return array("status" => "error", "data" => "Not found $request->gmail");
        }
        $id = RequestHelper::getGologin("http://profile.autoseo.win/profile-tmp/get-avail", $request->gmail);
        AccountInfo::where("note", $request->gmail)->update(["gologin" => $id]);
        //update to automail
        $input = array("gmail" => $request->gmail, "profile_id" => $id);
        RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input);
        return array("status" => "success", "data" => $id);
    }

    public function syncAvatar(Request $request) {
        $thumb = "";
        $data = AccountInfo::where("id", $request->id)->first();
        if ($data) {
            $thumbItem = ProxyHelper::get("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$data->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyB0QH1MoohfCLR746NU5hNffzGPMDMAAxQ");
            Log::info($thumbItem);
            $items = json_decode($thumbItem);
            Log::info($items->items[0]->snippet->thumbnails->medium->url);
            if (!empty($items->items[0]->snippet->thumbnails->medium->url)) {
                $thumb = $items->items[0]->snippet->thumbnails->medium->url;
                Log::info($thumb);
                $data->channel_clickup = $thumb;
                $data->save();
                return response()->json(["status" => "success", "thumb" => $thumb]);
            }
        }
        return response()->json(["status" => "error", "thumb" => $thumb]);
    }

    //scan job auto login moonshots
    public function scanJobBassNew() {
        $locker = new Locker(567);
        $locker->lock();
        $channels = AccountInfo::where("bas_new_status", 1)->whereNotNull("bas_new_id")->get();
        foreach ($channels as $id => $channel) {
            Logger::logUpload("scanJobBassNew$id http://bas.reupnet.info/job/load/$channel->bas_new_id");
            $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$channel->bas_new_id", []);
//            Log::info(json_encode($res));
            if ($res != null && $res != "" && !empty($res->status)) {
                if ($channel->gologin == null) {
                    if ($res->type == 60) {
                        $input = array("gmail" => $channel->note);
                        $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
                        if (!empty($mail->profile_id)) {
                            $channel->gologin = $mail->profile_id;
                        }
                    }
                }
                $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($res->result)));
                if ($res->status == 4) {
                    $channel->bas_new_status = 4;
                    foreach ($results as $result) {
                        $temp = json_decode($result);
                        if (Utils::containString($result, "login")) {
                            $log = $temp->result;
                            if ($log != "true") {
                                $channel->gmail_log = $log;
                                break;
                            }
                        }
                        if (Utils::containString($result, "create_api")) {
                            $log = $temp->result;
                            $channel->gmail_log = $log;
                            break;
                        }
                    }
                } else if ($res->status == 5) {
                    $channel->bas_new_status = 4;
                    foreach ($results as $result) {
                        $temp = json_decode($result);
                        if (Utils::containString($result, "login")) {
                            $log = $temp->result;
                            if ($log == "true") {
                                $channel->bas_new_status = 5;
                            }
                        }
                    }
                    //2023/03/29 sang confirm login xong ko chuyển sang nước ngoài, đợi đến khi add otp key thì chuyển
                    //assigon lại ip proxy nếu tạo api thành công
//                    $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/2";
//                    RequestHelper::callAPI("GET", $url2, []);
                }
            } else {
                if ($res->id == 0) {
                    $channel->bas_new_status = 4;
                    $channel->save();
                    continue;
                }
            }
            $channel->save();
        }
    }

    public function getPassword(Request $request) {
        Log::info("|getPassword|request=" . json_encode($request->all()));
//        $gmail = $request->gmail;

        $channel = AccountInfo::where("hash_pass", $request->hash_id)->first();
//        if (!$channel) {
//            $split = explode("@", $request->gmail);
//            $gmail = $split[0];
//
//            $channel = AccountInfo::where("note", $gmail)->first();
//            if (!$channel) {
//                return array("status" => 0, "long" => "Wrong email", "short" => "Wrong");
//            }
//        }
//        if ($channel->expire_get_pass < time()) {
//            return array("status" => 0, "long" => "Ask admin to get password", "short" => "Timeout");
//        }
        if ($channel) {
            $gmail = $channel->note;
            $input = array("gmail" => $gmail);
            $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
            if (!empty($mail->pass_word)) {
                return array("status" => 1, "pass" => $mail->pass_word, "reco" => $mail->recovery_email);
            }
        }
        return array("status" => 0, "long" => "Wrong email", "short" => "Wrong");
    }

    public function updateTimeGetChartDataMoonShots(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|updateTimeGetChartDataMoonShots|request=" . json_encode($request->all()));
    }

    public function getDataChart(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|getDataChart|request=" . json_encode($request->all()));
        if (!$request->is_admin_music) {
            $check = AccountInfo::where("chanel_id", $request->channel_id)->where("user_name", $user->user_code)->first();
        } else {
            $check = AccountInfo::where("chanel_id", $request->channel_id)->first();
        }
        if (!$check) {
            return ["status" => "error"];
        }
        $thumb = $check->channel_clickup;

        $lastSync = "N/A";
//        $moonshots = MoonshotsStats::where("channel_id", $request->channel_id)->first();
//        if ($moonshots) {
////            $lastSync = "$moonshots->updated_date $moonshots->updated_time";
//            $lastSyncTime = strtotime("$moonshots->updated_date $moonshots->updated_time -7 hours");
//            $lastSync = Utils::calcTimeText($lastSyncTime) ;
//            if ($moonshots->data == null) {
//                return ["data48hour" => [], "data60minutes" => []];
//            }
//            $data = json_decode($moonshots->data);
//
//        }
        //nếu mà thời gian lastsync quá 10 phút thì  sẽ lấy trực tiếp
//            if($lastSyncTime < time() - (10*60)){
        $cmd = "python3.9 /home/tools/check_channel.py $check->hash_pass";
        $rp = trim(shell_exec($cmd));
//        Utils::write("chart1.txt", $rp);
        $message = "";
        $status = "success";
        if (!empty($rp)) {
            $response = json_decode($rp);
            if ($response->success == true) {
                $data = $response->analytics;
            }
            if (isset($response->message)) {
                $message = $response->message;
                $status = "error";
            }
        }
        $lastSync = Utils::calcTimeText(time());
//            }
//        $temp = file_get_contents("data2.json");
//        $data = json_decode($temp);
        $dates = [];
        $datesText = [];
        $dates2 = [];
        $dates2Text = [];
        $values = [];
        $values2 = [];
        $data48hour = [];
        $data60minutes = [];
        $total48 = 0;
        $total60 = 0;
        $level = 0;
        $viewRate6 = 0;
        $viewRate42 = 0;
        $viewAvg = 0;
        $subs = 0;
        $maxViewTopVideoHour = 0;
        $maxViewTopVideoMinute = 0;

        $hourlyVideos = [];
        $hourlyTimes = [];
        $hourlyViews = [];
        $minutelyVideos = [];
        $minutelyTimes = [];
        $minutelyViews = [];

        $topVideos = [];
        $topVideosMeta = [];
        if (!empty($data->results)) {
            foreach ($data->results as $result) {
                if (!empty($result->key)) {
                    if ($result->key == "0__CUMULATIVE_SUBSCRIBERS_KEY") {
                        if (!empty($result->value->getCards->cards[0]->cumulativeSubscribersCardData->lifetimeTotal)) {
                            $subs = $result->value->getCards->cards[0]->cumulativeSubscribersCardData->lifetimeTotal;
                        }
                    }
                    if ($result->key == "0__ENTITY_HOURLY_VIEWS") {
                        if (!empty($result->value->resultTable->dimensionColumns[0]->timestamps->values)) {
                            $dates = $result->value->resultTable->dimensionColumns[0]->timestamps->values;
                        }
                        if (!empty($result->value->resultTable->metricColumns[0]->counts->values)) {
                            $values = $result->value->resultTable->metricColumns[0]->counts->values;
                        }
                        foreach ($dates as $date) {
                            $datesText[] = gmdate("Y/m/d H:i:s", $date / 1000 + 7 * 3600);
                        }
                        foreach ($values as $value) {
                            $total48 += $value;
                        }
                        $data48hour = ["label" => $datesText, "value" => $values, "total48" => $total48];
                        continue;
                    }
                    if ($result->key == "0__ENTITY_MINUTELY_VIEWS") {
                        if (!empty($result->value->resultTable->dimensionColumns[0]->timestamps->values)) {
                            $dates2 = $result->value->resultTable->dimensionColumns[0]->timestamps->values;
                            foreach ($dates2 as $date) {
                                $dates2Text[] = gmdate("Y/m/d H:i:s", $date / 1000 + 7 * 3600);
                            }
                        }
                        if (!empty($result->value->resultTable->metricColumns[0]->counts->values)) {
                            $values2 = $result->value->resultTable->metricColumns[0]->counts->values;
                            foreach ($values2 as $value) {
                                $total60 += $value;
                            }
                        }
                        $data60minutes = ["label" => $dates2Text, "value" => $values2, "total60" => $total60];
                    }

                    if ($result->key == "0__HOURLY_PER_VIDEO") {
                        if (!empty($result->value->resultTable->dimensionColumns[0]->strings->values)) {
                            $hourlyVideos = $result->value->resultTable->dimensionColumns[0]->strings->values;
                        }
                        if (!empty($result->value->resultTable->dimensionColumns[1]->timestamps->values)) {
                            $hourlyTimes = $result->value->resultTable->dimensionColumns[1]->timestamps->values;
                        }
                        if (!empty($result->value->resultTable->metricColumns[0]->counts->values)) {
                            $hourlyViews = $result->value->resultTable->metricColumns[0]->counts->values;
                        }
                    }
                    if ($result->key == "0__MINUTELY_PER_VIDEO") {
                        if (!empty($result->value->resultTable->dimensionColumns[0]->strings->values)) {
                            $minutelyVideos = $result->value->resultTable->dimensionColumns[0]->strings->values;
                        }
                        if (!empty($result->value->resultTable->dimensionColumns[1]->timestamps->values)) {
                            $minutelyTimes = $result->value->resultTable->dimensionColumns[1]->timestamps->values;
                        }
                        if (!empty($result->value->resultTable->metricColumns[0]->counts->values)) {
                            $minutelyViews = $result->value->resultTable->metricColumns[0]->counts->values;
                        }
                    }
                    if ($result->key == "0__TOP_VIDEO_META") {
                        if (!empty($result->value->getCreatorVideos->videos)) {
                            $topVideosMeta = $result->value->getCreatorVideos->videos;
                            foreach ($topVideosMeta as $topVideoMeeta) {
                                $listTimesHour = [];
                                $listViewsHour = [];
                                $totalViewsHour = 0;
                                $listTimesMinute = [];
                                $listViewsMinute = [];
                                $totalViewsMinute = 0;
                                foreach ($hourlyVideos as $i => $hourlyVideo) {
                                    if ($topVideoMeeta->videoId == $hourlyVideo) {
                                        $listTimesHour[] = $hourlyTimes[$i];
                                        $listViewsHour[] = $hourlyViews[$i];
                                        $totalViewsHour += $hourlyViews[$i];
                                        if (count($listTimesHour) == 48) {
                                            if ($totalViewsHour > $maxViewTopVideoHour) {
                                                $maxViewTopVideoHour = $totalViewsHour;
                                            }
                                            break;
                                        }
                                    }
                                }
                                foreach ($minutelyVideos as $i => $minutelyVideo) {
                                    if ($topVideoMeeta->videoId == $minutelyVideo) {
                                        $listTimesMinute[] = $minutelyTimes[$i];
                                        $listViewsMinute[] = $minutelyViews[$i];
                                        $totalViewsMinute += $minutelyViews[$i];
                                        if (count($listTimesMinute) == 60) {
                                            if ($totalViewsMinute > $maxViewTopVideoMinute) {
                                                $maxViewTopVideoMinute = $totalViewsMinute;
                                            }
                                            break;
                                        }
                                    }
                                }
                                $topVideos[] = (object) [
                                            "video_id" => $topVideoMeeta->videoId,
                                            "video_title" => $topVideoMeeta->title,
                                            "published" => gmdate("Y/m/d", $topVideoMeeta->timePublishedSeconds),
                                            "times_hour" => $listTimesHour,
                                            "views_hour" => $listViewsHour,
                                            "total_view_hour" => $totalViewsHour,
                                            "times_minute" => $listTimesMinute,
                                            "views_minute" => $listViewsMinute,
                                            "total_view_minute" => $totalViewsMinute,
                                ];
                            }
                        }
                    }
                }
            }
            if (!empty($data->storm_data)) {
                if (!empty($data->storm_data->storm_level)) {
                    if (is_numeric($data->storm_data->storm_level)) {
                        $level = round($data->storm_data->storm_level, 2);
                    }
                }
                if (!empty($data->storm_data->view_rate_6)) {
                    if (is_numeric($data->storm_data->view_rate_6)) {
                        $viewRate6 = round($data->storm_data->view_rate_6, 2);
                    }
                }
                if (!empty($data->storm_data->view_rate_42)) {
                    if (is_numeric($data->storm_data->view_rate_42)) {
                        $viewRate42 = round($data->storm_data->view_rate_42, 2);
                    }
                }
                if (!empty($data->storm_data->view_avg)) {
                    if (is_numeric($data->storm_data->view_avg)) {
                        $viewAvg = round($data->storm_data->view_avg, 2);
                    }
                }
            }
        }
        return ["channel_thumb" => $thumb,
            "data48hour" => $data48hour,
            "data60minutes" => $data60minutes,
            "subs" => $subs,
            "level" => $level,
            "viewRate6" => $viewRate6,
            "viewRate42" => $viewRate42,
            "viewAvg" => $viewAvg,
            "topVideos" => $topVideos,
            "maxViewTopVideoHour" => $maxViewTopVideoHour,
            "maxViewTopVideoMinute" => $maxViewTopVideoMinute,
            "last_sync" => $lastSync,
            "status" => $status,
            "message" => $message];
    }

    public function addCardEndscreen(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . 'ChannelManagementController.addCardEndscreen|request=' . json_encode($request->all()));
        $ids = $request->chkChannelAll;
        if (count($ids) == 0) {
            return array("status" => "error", "message" => "You have to choose channel");
        }

        //xử lý dữ liệu card
        $infoCardEdit = (object) [
                    "infoCardEdit" => (object) [
                        "infoCards" => []
                    ],
                    "externalVideoId" => "@@video_id"
        ];
        $infoCards = [];
        $cards = [];
        if ((!isset($request->video_link_card) || count($request->video_link_card) == 0 ) && (!isset($request->playlist_link_card) || count($request->playlist_link_card) == 0) && (!isset($request->channel_link_card) || count($request->channel_link_card) == 0)) {
            return array("status" => "error", "message" => "You have to config card or endscreen");
        }
        $infoCardEntityId = 0;
        if (isset($request->video_link_card) && count($request->video_link_card) > 0) {
            foreach ($request->video_link_card as $index => $vidCard) {
                if (Utils::containString($vidCard, "youtube.com")) {
                    $temp = YoutubeHelper::processLink($vidCard);
                    if ($temp["type"] != 3) {
                        return array("status" => "error", "message" => "Video in Video Card is invalid");
                    }
                    $promoLink = $temp["data"];
                } else {
                    $promoLink = $vidCard;
                }
                $time = $request->video_time[$index];
                $timeArr = explode(":", $time);
                if (count($timeArr) != 3) {
                    return array("status" => "error", "message" => "Time is invalid");
                }
                $cardMessage = !empty($request->video_message_card[$index]) ? $request->video_message_card[$index] : "";
                $cardIntro = !empty($request->video_message_card[$index]) ? $request->video_message_card[$index] : "";
                $cards[] = (object) [
                            "link_type" => 1,
                            "promo_link" => $promoLink,
                            "custom_message" => $cardMessage,
                            "intro_content" => $cardIntro,
                            "appear_time" => $time,
                ];
                //phút:giây:khung hình, 1s = 24 khung hình
                $teaserStartMs = $timeArr[0] * 60 * 1000 + $timeArr[1] * 1000 + round($timeArr[2] / 24 * 1000);
                $infoCards[] = (object) [
                            "infoCardEntityId" => "new-addition-" . $infoCardEntityId++,
                            "videoId" => "@@video_id",
                            "teaserStartMs" => $teaserStartMs,
                            "customMessage" => $cardMessage,
                            "teaserText" => $cardIntro,
                            "videoInfoCard" => (object) ["videoId" => $promoLink],
                ];
            }
        }
        if (isset($request->playlist_link_card) && count($request->playlist_link_card) > 0) {
            foreach ($request->playlist_link_card as $index => $vidCard) {
                if (Utils::containString($vidCard, "youtube.com")) {
                    $temp = YoutubeHelper::processLink($vidCard);
                    if ($temp["type"] != 1) {
                        return array("status" => "error", "message" => "Playlist is invalid");
                    }
                    $promoLink = $temp["data"];
                } else {
                    $promoLink = $vidCard;
                }
                $time = $request->playlist_time[$index];
                $timeArr = explode(":", $time);
                if (count($timeArr) != 3) {
                    return array("status" => "error", "message" => "Time in Playlist Card is invalid");
                }
                $cardMessage = !empty($request->playlist_message_card[$index]) ? $request->playlist_message_card[$index] : "";
                $cardIntro = !empty($request->playlist_intro_card[$index]) ? $request->playlist_intro_card[$index] : "";
                $cards[] = (object) [
                            "link_type" => 2,
                            "promo_link" => $promoLink,
                            "custom_message" => $cardMessage,
                            "intro_content" => $cardIntro,
                            "appear_time" => $time,
                ];
                //phút:giây:khung hình, 1s = 24 khung hình
                $teaserStartMs = $timeArr[0] * 60 * 1000 + $timeArr[1] * 1000 + round($timeArr[2] / 24 * 1000);
                $infoCards[] = (object) [
                            "infoCardEntityId" => "new-addition-" . $infoCardEntityId++,
                            "videoId" => "@@video_id",
                            "teaserStartMs" => $teaserStartMs,
                            "customMessage" => $cardMessage,
                            "teaserText" => $cardIntro,
                            "playlistInfoCard" => (object) ["fullPlaylistId" => $promoLink],
                ];
            }
        }
        if (isset($request->channel_link_card) && count($request->channel_link_card) > 0) {
            foreach ($request->channel_link_card as $index => $vidCard) {
                if (Utils::containString($vidCard, "youtube.com")) {
                    $temp = YoutubeHelper::processLink($vidCard);
                    if ($temp["type"] != 2) {
                        return array("status" => "error", "message" => "Channel is invalid");
                    }
                    $promoLink = $temp["data"];
                } else {
                    $promoLink = $vidCard;
                }
                $time = $request->playlist_time[$index];
                $timeArr = explode(":", $time);
                if (count($timeArr) != 3) {
                    return array("status" => "error", "message" => "Time in Channel Card is invalid");
                }
                if ($request->channel_message_card[$index] == null || $request->channel_intro_card[$index] == null) {
                    return array("status" => "error", "message" => "Custom message and intro content in Channel Card can not be empty");
                }
                $cardMessage = $request->channel_message_card[$index];
                $cardIntro = $request->channel_intro_card[$index];
                $cards[] = (object) [
                            "link_type" => 3,
                            "promo_link" => $promoLink,
                            "custom_message" => $cardMessage,
                            "intro_content" => $cardIntro,
                            "appear_time" => $time,
                ];
                //phút:giây:khung hình, 1s = 24 khung hình
                $teaserStartMs = $timeArr[0] * 60 * 1000 + $timeArr[1] * 1000 + round($timeArr[2] / 24 * 1000);
                $infoCards[] = (object) [
                            "infoCardEntityId" => "new-addition-" . $infoCardEntityId++,
                            "videoId" => "@@video_id",
                            "teaserStartMs" => $teaserStartMs,
                            "customMessage" => $cardMessage,
                            "teaserText" => $cardIntro,
                            "collaboratorInfoCard" => (object) ["channelId" => $promoLink],
                ];
            }
        }
        $infoCardEdit->infoCardEdit->infoCards = $infoCards;
        Log::info(json_encode($cards));
        Log::info(json_encode($infoCardEdit));

        //xử lý dữ liệu endscreen
        $endscreenEdit = (object) [
                    "endscreenEdit" => (object) [
                        "endscreen" => (object) [
                            "elements" => [],
                            "encryptedVideoId" => "@@video_id",
                            "startMs" => "@@start_ms"
                        ],
                    ],
                    "externalVideoId" => "@@video_id"
        ];

        //video
        $videoEndsPromo = "";
        if ($request->video_type_ends == 1) {
            $videoTypeEndscreen = "VIDEO_TYPE_RECENT_UPLOAD";
        } else if ($request->video_type_ends == 2) {
            $videoTypeEndscreen = "VIDEO_TYPE_BEST_FOR_VIEWER";
        } else if ($request->video_type_ends == 3) {
            $videoTypeEndscreen = "VIDEO_TYPE_FIXED";
            if (Utils::containString(trim($request->video_link_endscreen), "youtube.com")) {
                $temp = YoutubeHelper::processLink(trim($request->video_link_endscreen));
                if ($temp["type"] != 3) {
                    return array("status" => "error", "message" => "Video in endscreen is invalid");
                }
                $videoEndsPromo = $temp["data"];
            } else {
                $videoEndsPromo = trim($request->video_link_endscreen);
            }
        }
        //playlist
        if (Utils::containString(trim($request->playlist_link_endscreen), "youtube.com")) {
            $temp = YoutubeHelper::processLink(trim($request->playlist_link_endscreen));
            if ($temp["type"] != 1) {
                return array("status" => "error", "message" => "Playlist in endscreen is invalid");
            }
            $playlistEndsPromo = $temp["data"];
        } else {
            $playlistEndsPromo = trim($request->playlist_link_endscreen);
        }

        $endscreens = [];
        $endscreens[] = (object) [
                    "link_type" => 1,
                    "promo_link" => $videoEndsPromo,
        ];
        $endscreens[] = (object) [
                    "link_type" => 2,
                    "promo_link" => $playlistEndsPromo,
        ];
        //template
        if ($request->template_encscreen == "tpl1") {
            $channelPos = (object) [
                        "left" => 0.78070176,
                        "width" => 0.1543859649122807,
                        "top" => 0.34968847,
                        "offsetMs" => 0,
                        "durationMs" => 20000,
                        "channelEndscreenElement" => (object) [
                            "channelId" => "@@channel_id_promo",
                            "isSubscribe" => true
                        ]
            ];
            $videoPos = (object) [
                        "left" => 0.02280701754385965,
                        "width" => 0.32280701754385965,
                        "top" => 0.1308411214953271,
                        "offsetMs" => 0,
                        "durationMs" => 20000,
                        "videoEndscreenElement" => (object) [
                            "encryptedVideoId" => $videoEndsPromo,
                            "videoType" => $videoTypeEndscreen,
                            "playbackStartMs" => 0
                        ]
            ];
            $playlistPos = (object) [
                        "left" => 0.02280701754385965,
                        "width" => 0.32280701754385965,
                        "top" => 0.5249221183800623,
                        "offsetMs" => 0,
                        "durationMs" => 20000,
                        "playlistEndscreenElement" => (object) [
                            "playlistId" => $playlistEndsPromo
                        ]
            ];
        } else {
            $channelPos = (object) [
                        "left" => 0.4228070175438597,
                        "width" => 0.1543859649122807,
                        "top" => 0.5490654,
                        "offsetMs" => 0,
                        "durationMs" => 20000,
                        "channelEndscreenElement" => (object) [
                            "channelId" => "@@channel_id_promo",
                            "isSubscribe" => true
                        ]
            ];
            $videoPos = (object) [
                        "left" => 0.02280701754385965,
                        "width" => 0.32280701754385965,
                        "top" => 0.5245450073782588,
                        "offsetMs" => 0,
                        "durationMs" => 20000,
                        "videoEndscreenElement" => (object) [
                            "encryptedVideoId" => $videoEndsPromo,
                            "videoType" => $videoTypeEndscreen,
                            "playbackStartMs" => 0
                        ]
            ];
            $playlistPos = (object) [
                        "left" => 0.6543859649122806,
                        "width" => 0.32280701754385965,
                        "top" => 0.5249221183800623,
                        "offsetMs" => 0,
                        "durationMs" => 20000,
                        "playlistEndscreenElement" => (object) [
                            "playlistId" => $playlistEndsPromo
                        ]
            ];
        }

        $elements[] = $channelPos;
        $elements[] = $videoPos;
        $elements[] = $playlistPos;

        $endscreenEdit->endscreenEdit->endscreen->elements = $elements;

        Log::info(json_encode($endscreenEdit));

        $listId = implode(",", $ids);
        $countChannelSuccess = 0;
        $countChannelFail = 0;
        $countVideoCardSuccess = 0;
        $countVideoCardFail = 0;
        $countVideoEndsSuccess = 0;
        $countVideoEndsFail = 0;

        $datas = DB::select("select a.channel_id,b.note as gmail,a.data from moonshots_stats a, accountinfo b where a.channel_id = b.chanel_id and b.id in ($listId)");
        foreach ($datas as $moonshot) {

            if ($moonshot->data == null) {
                $countChannelFail++;
                continue;
            }
            $data = json_decode($moonshot->data);
            if (!empty($data->results)) {
                foreach ($data->results as $result) {
                    if (!empty($result->key)) {
                        if ($result->key == "0__TOP_VIDEO_META") {
                            if (!empty($result->value->getCreatorVideos->videos)) {
                                $topVideosMeta = $result->value->getCreatorVideos->videos;
                                if (count($topVideosMeta) > 0) {
                                    foreach ($topVideosMeta as $topVideoMeta) {
                                        $videoCardEnd = $topVideoMeta->videoId;
                                        $viewsCardEnd = $topVideoMeta->metrics->viewCount;
                                        //tao payload card

                                        $payloadCard = str_replace("@@video_id", $videoCardEnd, json_encode($infoCardEdit));
                                        $payloadEnds = str_replace("@@video_id", $videoCardEnd, json_encode($endscreenEdit));
                                        $payloadEnds = str_replace("@@channel_id_promo", $moonshot->channel_id, $payloadEnds);
                                        $cardCommand = new CardEndsCommand();
                                        $cardCommand->username = $user->user_name;
                                        $cardCommand->gmail = $moonshot->gmail;
                                        $cardCommand->channel_id = $moonshot->channel_id;
                                        $cardCommand->video_id = $videoCardEnd;
                                        $cardCommand->payload_card = $payloadCard;
                                        $cardCommand->playload_ends = $payloadEnds;
                                        $cardCommand->created = time();
                                        $cardCommand->created_text = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                        $cardCommand->save();

                                        //thêm dữ liệu card de quan ly

                                        foreach ($cards as $card) {
                                            $countVideoCardSuccess++;
                                            $check = CardEndsConfig::where("video_id", $videoCardEnd)->where("type", 1)->first();
                                            if (!$check) {
                                                $cardEndInsert = new CardEndsConfig();
                                                $cardCommand->username = $user->user_name;
                                                $cardEndInsert->type = 1;
                                                $cardEndInsert->link_type = $card->link_type;
                                                $cardEndInsert->promo_link = $card->promo_link;
                                                $cardEndInsert->custom_message = $card->custom_message;
                                                $cardEndInsert->intro_content = $card->intro_content;
                                                $cardEndInsert->appear_time = $card->appear_time;
                                                $cardEndInsert->channel_id = $moonshot->channel_id;
                                                $cardEndInsert->video_id = $videoCardEnd;
                                                $cardEndInsert->views = $viewsCardEnd;
                                                $cardEndInsert->created = time();
                                                $cardEndInsert->created_text = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                                $cardEndInsert->command_id = $cardCommand->id;
                                                $cardEndInsert->save();
                                            } else {
                                                $check->link_type = $card->link_type;
                                                $check->promo_link = $card->promo_link;
                                                $check->custom_message = $card->custom_message;
                                                $check->intro_content = $card->intro_content;
                                                $check->appear_time = $card->appear_time;
                                                $check->command_id = $cardCommand->id;
                                                $check->save();
                                            }
                                        }
                                        //them dữ liệu enscreen
                                        foreach ($endscreens as $endscreen) {
                                            $vidPromoEnd = $endscreen->promo_link;
                                            if ($vidPromoEnd == "") {
                                                $vidPromoEnd = $videoTypeEndscreen;
                                                $check = false;
                                            } else {
                                                $check = CardEndsConfig::where("video_id", $videoCardEnd)->where("type", 2)->first();
                                            }
                                            if (!$check) {
                                                $cardEndInsert = new CardEndsConfig();
                                                $cardEndInsert->type = 2;
                                                $cardEndInsert->link_type = $endscreen->link_type;
                                                $cardEndInsert->promo_link = $vidPromoEnd;
                                                $cardEndInsert->channel_id = $moonshot->channel_id;
                                                $cardEndInsert->video_id = $videoCardEnd;
                                                $cardEndInsert->views = $viewsCardEnd;
                                                $cardEndInsert->created = time();
                                                $cardEndInsert->created_text = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                                $cardEndInsert->command_id = $cardCommand->id;
                                                $cardEndInsert->save();
                                                $countVideoEndsSuccess++;
                                            } else {
                                                $check->link_type = $endscreen->link_type;
                                                $check->promo_link = $vidPromoEnd;
                                                $check->save();
                                            }
                                        }

//                                        break;
                                    }
                                    $countChannelSuccess++;
                                } else {
                                    $countChannelFail++;
                                }
                            }
                        }
                    }
                }
            }

//            break;
        }
        return array("status" => "success", "message" => "Success channel=$countChannelSuccess, Fail channel=$countChannelFail, success video card=$countVideoCardSuccess, fail video card=$countVideoCardFail,success video ends=$countVideoEndsSuccess, fail video ends=$countVideoEndsFail");
    }

    public function getCardEndscreen(Request $request) {
        $datas = DB::select("select type,link_type,promo_link,count(*) as total from card_ends_config group by type,link_type,promo_link");
        return $datas;
    }

    //list channel auto
    public function listChannelAuto(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|listChannelAuto|request=" . json_encode($request->all()));
        $curr = time();
        $curentDate = gmdate("d-m-Y", time() + (7 * 3600));
        $dateUpload = time() - 86400 * 2;
        $userExcept = array("truongpv_1515486846", "star_effect_1601628590", "victorteam_1533526611");
        if (in_array('20', explode(",", $user->role)) || in_array('21', explode(",", $user->role))) {
            $total = AccountInfo::whereIn("is_music_channel", [2, 1])
                            ->where("is_sync", 1)
                            ->where("del_status", 0)->count();
            $channels = AccountInfo::whereIn("is_music_channel", [2, 1])
                            ->where("is_sync", 1)
                            ->where("del_status", 0)->offset($request->start)->limit($request->length)->get();
            $groupChannels = GroupChannel::orderBy('id', 'desc')->get();
            $uploads = DB::select("select a.*,max(b.date) as last_upload from (select user_name,chanel_id,chanel_name,subscriber_count as subs,view_count as views from accountinfo accountinfo where is_music_channel in (1,2) and is_sync =1 and upload_alert=1 ) a 
                                left join (select channel_id,video_id,date from video_daily) b
                                on a.chanel_id =b.channel_id
                                group by a.chanel_id having max(b.date) < " . gmdate("Ymd", $dateUpload) . " or max(b.date) is null order by max(b.date)");
        } else {
            $total = AccountInfo::whereIn("is_music_channel", [2, 1])
                            ->where("user_name", $user->user_code)
                            ->where("is_sync", 1)
                            ->where("del_status", 0)->count();
            $channels = AccountInfo::whereIn("is_music_channel", [2, 1])
                            ->where("user_name", $user->user_code)
                            ->where("is_sync", 1)
                            ->where("del_status", 0)->offset($request->start)->limit($request->length)->get();
            $uploads = DB::select("select a.*,max(b.date) as last_upload from (select user_name,chanel_id,chanel_name,subscriber_count as subs,view_count as views from accountinfo accountinfo where is_music_channel in (1,2) and is_sync =1 and upload_alert=1 and $condition) a 
                                left join (select channel_id,video_id,date from video_daily) b
                                on a.chanel_id =b.channel_id
                                group by a.chanel_id having max(b.date) < " . gmdate("Ymd", $dateUpload) . " or max(b.date) is null order by max(b.date)");
            $groupChannels = GroupChannel::where("user_name", $user->user_name)->orderBy('id', 'desc')->get();
        }
        $autoWakeupsTask = Tasks::where("date", $curentDate)->where("type", 4)->orderBy("id", "desc")->get();
        $fuckLabelArtists = DB::select("select * from fuck_label_artists");
        $countError = 0;

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
            $channel->video_daily = 0;
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
        return response()->json((object) ["recordsFiltered" => $total, "recordsTotal" => $total, "data" => $channels]);
    }

    //2024/09/24 hàm lấy otp dùng cho đổi mk
    public function getOtp(Request $request) {
        Log::info("getOtp|request=" . json_encode($request->all()));
        if ($request->hash_id == null) {
            return array("status" => "error");
        }
        $accountInfo = AccountInfo::where("hash_pass", $request->hash_id)->first();
        if ($accountInfo) {
            $temp = RequestHelper::getUrl("http://127.0.0.1:5701/otp/get?key=$accountInfo->otp_key", 0);
            $code = json_decode($temp);
            return array("status" => "success", "data" => $code->code);
        } else {
            return array("status" => "error");
        }
    }

    //đổi password, chạy 1 tháng 1 lần
    public function autoChangePass() {
        DB::enableQueryLog();
        $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
        $channels = AccountInfo::whereNotNull("note")->whereNotNull("gologin")->whereNotNull("otp_key")
                        ->where("status", 1)->whereRaw("(last_change_pass is null or (last_change_pass <> 0 and last_change_pass < $thirtyDaysAgo))")->take(500)->get();
//        Log::info(DB::getQueryLog());
        $time = time();
        foreach ($channels as $channel) {
            $this->sendCommandChangePass($channel, $time);
            $time = (60 * 30) + $time;
        }
        return count($channels);
    }

    public function sendCommandChangePass($channel, $runTime = 0) {
        $taskLists = [];
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $params = [];
        $params[] = (object) [
                    "name" => "hash_id",
                    "type" => "string",
                    "value" => $channel->hash_pass
        ];

        $changePass = (object) [
                    "script_name" => "profile",
                    "func_name" => "change_pass",
                    "params" => $params
        ];

        $taskLists[] = $login;
        $taskLists[] = $changePass;
        $req = (object) [
                    "gmail" => $channel->note,
                    "task_list" => json_encode($taskLists),
                    "studio_id" => $channel->id,
                    "run_time" => $runTime,
                    "type" => 45,
                    "piority" => 50,
                    "call_back" => "http://automusic.win/callback/pass/change",
        ];
        Log::info("change Pass $channel->note" . json_encode($req));
        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        if (!empty($res->job_id)) {
            $input = array("gmail" => $channel->note);
            $oldPass = "";
            $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
            if (!empty($mail->pass_word)) {
                $oldPass = $mail->pass_word;
            }
            $jobId = $res->job_id;
            $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " change pass job_id=$jobId old_pass=$oldPass";
            $channel->save();
        }
    }

    //hàm thay đổi thông tin email, bao gồm cả pass
    public function sendCommandChangeInfo($channel, $runTime = 0) {
        $taskLists = [];
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $params = [];
//        $params[] = (object) [
//                    "name" => "hash_id",
//                    "type" => "string",
//                    "value" => $channel->hash_pass
//        ];
//        $changePass = (object) [
//                    "script_name" => "profile",
//                    "func_name" => "change_pass",
//                    "params" => $params
//        ];

        $changeInfo = (object) [
                    "script_name" => "profile",
                    "func_name" => "change_info",
                    "params" => []
        ];

        $taskLists[] = $login;
//        $taskLists[] = $changePass;
        $taskLists[] = $changeInfo;
        $req = (object) [
                    "gmail" => $channel->note,
                    "task_list" => json_encode($taskLists),
                    "studio_id" => $channel->id,
                    "run_time" => $runTime,
                    "type" => 47,
                    "piority" => 50,
                    "call_back" => "http://automusic.win/callback/info/change",
        ];
        Log::info("change info $channel->note" . json_encode($req));
        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        if (!empty($res->job_id)) {
            $input = array("gmail" => $channel->note);
            $oldPass = "";
            $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
            if (!empty($mail->pass_word)) {
                $oldPass = $mail->pass_word;
            }
            $jobId = $res->job_id;
            $channel->log = $channel->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " change info job_id=$jobId old_pass=$oldPass";
            $channel->save();
        }
    }

    public function sendCommandAuth($channel) {
        $taskLists = [];
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];


        $oauth = (object) [
                    "script_name" => "api",
                    "func_name" => "yt_device_oauth",
                    "params" => []
        ];

        $taskLists[] = $login;
        $taskLists[] = $oauth;
        $req = (object) [
                    "gmail" => $channel->note,
                    "task_list" => json_encode($taskLists),
                    "studio_id" => $channel->id,
                    "run_time" => 0,
                    "type" => 46,
                    "piority" => 10,
                    "call_back" => "",
        ];
        Log::info("change yt_device_oauth $channel->note" . json_encode($req));
        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
    }

    //thêm kênh vào hệ thống vào email đã tồn tại
    public function addChannel(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ChannelManagementController.addChannel|request=' . json_encode($request->all()));
        if (Utils::containString($request->add_channel_id, "@")) {
            $channelId = "@" . explode("@", $request->add_channel_id)[1];
        } elseif (Utils::containString($request->add_channel_id, "/channel/")) {
            $channelId = explode("/channel/", $request->add_channel_id)[1];
        } else {
            $channelId = $request->add_channel_id;
        }
        if ($channelId == null) {
            return response()->json(array("status" => "error", "message" => "ChannelId is invalid"));
        }
        if (!isset($request->select_mail) || $request->select_mail == "") {
            return response()->json(array("status" => "error", "message" => "Email is invalid"));
        }
        Log::info("channel " . $channelId);
        $data = YoutubeHelper::getChannelInfoV2($channelId);
        Log::info(json_encode($data));
        if ($data["status"] == 0) {
            return response()->json(array("status" => "error", "message" => "Channel dead"));
        }
        $check = AccountInfo::where("chanel_id", $data["channelId"])->first();
        if ($check) {
            return response()->json(array("status" => "error", "message" => "Channel Id exists"));
        }
        $oldMail = AccountInfo::where("note", $request->select_mail)->where("user_name", $user->user_code)->first();
        if (!$oldMail) {
            return response()->json(array("status" => "error", "message" => "Mail is invalid"));
        }
        $groupChannel = 0;
        if (isset($request->group_channel) && $request->group_channel != "-1") {
            $groupChannel = $request->group_channel;
        }
        $account = new AccountInfo();
        $account->user_name = $user->user_code;
        $account->chanel_id = $data["channelId"];
        $account->handle = $data["handle"];
        $account->chanel_name = $data["channelName"];
        $account->note = $request->select_mail;
        $account->gmail = $request->select_mail;
        $hash = Utils::generateRandomHash(8);
        $account->hash_pass = md5($hash . $account->chanel_id);
        $account->log = gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name added,insert otp key";
        $account->emp_code = $user->customer_id;
        $account->create_time = time();
        $account->gologin = $oldMail->gologin;
        $account->otp_key = $oldMail->otp_key;
        $account->is_add_otp = 1;
        $account->is_automusic_v2 = 1;
        $account->group_channel_id = $groupChannel;
        $account->save();
        $url = "http://bas.reupnet.info/profile/assign/group-auto/$account->chanel_id/linux_bas_v2/no_proxy";
        RequestHelper::callAPI("GET", $url, []);
        return response()->json(array("status" => "success", "message" => "Success"));
    }

    function genEmailInfo() {
        $string = shell_exec("/home/tools/env/bin/faker --lang=en_US profile");
        Log::info($string);
        if ($string != null) {

            $string = str_replace("'", '"', $string);

            // Chuyển đổi Decimal
            $string = preg_replace('/Decimal\("([0-9.-]+)"\)/', '$1', $string);

            // Chuyển datetime.date
            $string = preg_replace('/datetime\.date\((\d+), (\d+), (\d+)\)/', '"$1-$2-$3"', $string);

            // Chuyển đổi dấu ngoặc đơn thành ngoặc vuông trong current_location
            $string = preg_replace('/\(\s*([^,]+),\s*([^,]+)\s*\)/', '[$1, $2]', $string);
            Log::info($string);
            // Chuyển đổi chuỗi JSON
            $info = json_decode($string);
//            $info = json_decode(trim($result));
            Log::info(json_encode($info));
            $info->pass = Utils::generateRandomString(16);
            $info->birthday = Utils::randomBirthday();
            $dobArr = explode("-", $info->birthday);
            $id = rand(0, 2);
            $acc = AccountInfo::where("user_name", "recovery_email_1730865536")->orderByRaw('RAND()')->first(["note"]);
            $info->recovery = Utils::genRecovery($acc->note);
            $lastFullEmail = $info->username . Utils::getFirstNameLowercase($info->name) . $dobArr[$id];
            $info->last_full_email = $lastFullEmail;
            return response()->json($info);
        }
    }

    function createEmail(Request $request) {

        $user = Auth::user();
        Log::info($user->user_name . '|ChannelManagementController.createEmail|request=' . json_encode($request->all()));
        if (isset($request->email_id)) {
            $data = AccountInfo::where("id", $request->email_id)->where("user_name", $user->user_code)->first();
            $data->log = $data->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name updated";
            if (!$data) {
                return response()->json(["status" => "error", "message" => "Not found"]);
            }
        } else {
            $data = new AccountInfo();
            $data->user_name = $user->user_code;
            $hash = Utils::generateRandomHash(8);
            $data->hash_pass = md5($hash . $data->chanel_id);
            $data->log = gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " $user->user_name created";
            $data->emp_code = $user->customer_id;
            $data->create_time = time();
        }
        $data->chanel_id = '@' . trim($request->fake_email);
        $data->chanel_name = trim($request->fake_email);
        $data->note = trim($request->fake_email);
        $data->gmail = trim($request->fake_email);
        $data->reco_email = trim($request->fake_recovery);
        $data->pass_word = trim($request->fake_pass);
        $data->save();
        $dob = trim($request->fake_birth);
        $dobArr = explode("-", $dob);
        if (count($dobArr) != 3) {
            return response()->json(["status" => "error", "message" => "Birth Date is invalid"]);
        }
        if (isset($request->automail_id)) {
            $input = array(
                "id" => $request->automail_id,
                "gmail" => trim($request->fake_email),
                "userCreate" => $user->user_name,
                "passWord" => trim($request->fake_pass),
                "firstName" => trim($request->fake_name),
                "recoveryEmail" => trim($request->fake_recovery),
                "phoneNumber" => trim($request->fake_phone),
                "birthDay" => $dobArr[2],
                "birthMonth" => $dobArr[1],
                "birthYear" => $dobArr[0]
            );
        } else {
            $input = array(
                "gmail" => trim($request->fake_email),
                "userCreate" => $user->user_name,
                "passWord" => trim($request->fake_pass),
                "firstName" => trim($request->fake_name),
                "recoveryEmail" => trim($request->fake_recovery),
                "phoneNumber" => trim($request->fake_phone),
                "birthDay" => $dobArr[2],
                "birthMonth" => $dobArr[1],
                "birthYear" => $dobArr[0]
            );
        }
        $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/create/", $input);
        if (!empty($mail->id)) {
            //dùng tạm cột api_job_id để lưu id của bảng mailinfo trên automail
            $data->api_job_id = $mail->id;
            $data->save();
        }
        return response()->json(["status" => "success", "message" => "success", "data" => $data]);
    }

    function openEmail(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ChannelManagementController.createEmail|request=' . json_encode($request->all()));
    }

    function upadteEmail(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ChannelManagementController.createEmail|request=' . json_encode($request->all()));
    }

    //thêm kênh lên hệ thống comment
    function addChannelToCommentSystem($channel) {
        $u = User::where("user_code", $channel->user_name)->first();
        if ($u->social_id == null) {
            Log::info("social_id  $u->social_id ");
            return 0;
        }
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer liSBfojF58Hu_B0KQAjgFhJiaIxAX0Od'
        );
        $check = RequestHelper::callAPI2("GET", "https://social.automusic.win/items/owned_channels?filter[channel_id][_eq]=$channel->chanel_id", [], $header);
        Log::info("cehck: " . json_encode($check));
        $data = (object) [
                    "email" => $channel->note,
                    "handle" => $channel->handle,
                    "channel_name" => $channel->chanel_name,
                    "channel_cover" => $channel->channel_clickup,
                    "user_id" => $u->social_id,
        ];
        if (!empty($check->data[0]->id)) {
            $id = $check->data[0]->id;
            Log::info("data update " . json_encode($data));
            $update = RequestHelper::socialApi("PATCH", "https://social.automusic.win/items/owned_channels/$id", $data, $header);
            Log::info("update " . json_encode($update));


//            $curl = curl_init();
//
//            curl_setopt_array($curl, array(
//              CURLOPT_URL => 'https://social.automusic.win/items/owned_channels/'.$id,
//              CURLOPT_RETURNTRANSFER => true,
//              CURLOPT_ENCODING => '',
//              CURLOPT_MAXREDIRS => 10,
//              CURLOPT_TIMEOUT => 0,
//              CURLOPT_FOLLOWLOCATION => true,
//              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//              CURLOPT_CUSTOMREQUEST => 'PATCH',
//              CURLOPT_POSTFIELDS =>'{
//                "email": "chuasung99994@gmail.com",
//                "handle": "@jamess1234h",
//                "channel_name": "Sung Chua",
//                "channel_cover": "https:\\/\\/yt3.ggpht.com\\/ytc\\/AIdro_ln-9HYXgytSDFQbDRDb_rgH6bzytH6hE2dknWeVZrk6K8go1x19Eeds3jB779sjKtoDw=s240-c-k-c0x00ffffff-no-rj",
//                "user_id": "cb4004e1-e082-4beb-a251-0566d7cee4dd"
//            }',
//              CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer liSBfojF58Hu_B0KQAjgFhJiaIxAX0Od',
//                'Content-Type: application/json'
//              ),
//            ));
//
//            $response = curl_exec($curl);
//
//            curl_close($curl);
        } else {
            $data->channel_id = $channel->chanel_id;
            Log::info("data insert" . json_encode($data));
            $update = RequestHelper::callAPI2("POST", "https://social.automusic.win/items/owned_channels/", $data, $header);
            Log::info("insert " . json_encode($update));
        }
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://social.automusic.win/items/owned_channels',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => '{"email":"' . $channel->note . '","handle":"' . $channel->handle . '", "channel_name": "' . $channel->chanel_name . '", "channel_id": "' . $channel->chanel_id . '", "channel_cover": "' . $channel->channel_clickup . '", "user_id": "' . $u->social_id . '"}',
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'Authorization: Bearer liSBfojF58Hu_B0KQAjgFhJiaIxAX0Od'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);
//        return $response;
        return 1;
    }

    //sync cookie
    function syncCookie($channel, $callback) {
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $syncCookie = (object) [
                    "script_name" => "upload",
                    "func_name" => "sync_cookie",
                    "params" => []
        ];
        $taskLists = [];
        $taskLists[] = $login;
        $taskLists[] = $syncCookie;

        $req = (object) [
                    "gmail" => $channel->note,
                    "studio_id" => $channel->id,
                    "task_list" => json_encode($taskLists),
                    "run_time" => 0,
                    "type" => 80,
                    "piority" => 10,
                    "call_back" => $callback
        ];
        $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        if ($bas->mess == "ok") {
            return 1;
        }
        return 0;
    }

    //hàm lấy danh sách kênh cần trả lời comment
    public function getChannelForComment(Request $request) {
        $platform = $request->header('platform');
        if ($platform != "AutoWin") {
            return ["message" => "Wrong system!"];
        }
        $locker = new Locker(67899);
        $locker->lock();

        $limit = 50;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }
        $datas = AccountInfo::whereNotNull("otp_key")
                        ->where("del_status", 0)
                        ->where("status", 1)
                        ->whereIn("status_cmt", [1, 4, 5])
                        ->where(function ($query) {
                            $query->whereNull("next_time_cmt")
                            ->orWhere("next_time_cmt", "<=", time());
                        })
                        ->take($limit)->get(["id", "handle", "note as gmail", "chanel_id as channel_id", "hash_pass"]);
        AccountInfo::whereNotNull("otp_key")
                ->where("del_status", 0)
                ->where("status", 1)
                ->whereIn("status_cmt", [1, 4, 5])
                ->where(function ($query) {
                    $query->whereNull("next_time_cmt")
                    ->orWhere("next_time_cmt", "<=", time());
                })
                ->take($limit)->update(["next_time_cmt" => time() + rand(3600 * 9, 3600 * 12)]);
//        if ($data) {
//            $data->status_cmt = 1;
//            $data->next_time_cmt = time() + 86400;
//            $data->save();
//            Logger::logUpload("moonShotsChannel $data->id");
//            return response()->json($datas);
//        }
        return response()->json($datas);
    }

    //update tag for channel
    public function channelTag(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ChannelManagementController.channelTag|request=' . json_encode($request->all()));
        $tag = trim($request->tag_name);
        $action = $request->action;
        if ($request->is_admin_music) {
            $account = AccountInfo::find($request->id);
        } else {
            $account = AccountInfo::where("user_name", $user->user_code)->where("id", $request->id)->first();
        }
        if (!$account) {
            return response()->json([
                        'status' => "error",
                        'message' => "Not found $request->id"
            ]);
        }
        $currentTags = $account->tags ? explode(',', $account->tags) : [];
        $tagLower = strtolower($tag);
        $tagUpper = strtoupper($tag);
        $currentTagsLower = array_map('strtolower', $currentTags);

        if ($action == "add") {
            if ($tag == null) {
                return response()->json([
                            'status' => "error",
                            'message' => 'Tag can not be empty'
                ]);
            }

            $length = strlen($tag);
            if ($length < 3) {
                return response()->json([
                            'status' => "error",
                            'message' => 'Tag must be longer than 2 characters'
                ]);
            }

            if ($length > 20) {
                return response()->json([
                            'status' => "error",
                            'message' => 'Tag must not exceed 20 characters'
                ]);
            }
            // Kiểm tra ký tự hợp lệ
            if (!preg_match('/^[a-zA-Z0-9 _-]+$/', $tag)) {
                return response()->json([
                            'status' => "error",
                            'message' => 'Tag is invalid. Only letters, numbers, underscore (_), and hyphen (-) are allowed'
                ]);
            }

            if (in_array($tagLower, $currentTagsLower)) {
                return response()->json([
                            'status' => "error",
                            'message' => "Tag '$tag' already exists in the record"
                ]);
            }

            $currentTags[] = $tagUpper; // Lưu tag dưới dạng uppercase
            $newTags = implode(',', $currentTags);
            $message = "Tag '$tag' added successfully";

            // lưu sang channel_tags
            $checkTag = ChannelTags::where("username", $user->user_name)->where("tag", $tagUpper)->where("status", 1)->first();
            if (!$checkTag) {
                $insert = new ChannelTags();
                $insert->username = $user->user_name;
                $insert->tag = $tagUpper;
                $insert->channel_count = 1;
                $insert->created = Utils::timeToStringGmT7(time());
                $insert->save();
            } else {
                $checkTag->channel_count = $checkTag->channel_count + 1;
                $checkTag->save();
            }
        } elseif ($action == "delete") {
            $key = array_search($tagLower, $currentTagsLower);
            if ($key === false) {
                return response()->json([
                            'status' => "error",
                            'message' => "Tag '$tag' does not exist in the record"
                ]);
            }
            unset($currentTags[$key]);
            $newTags = implode(',', array_values($currentTags));
            $message = "Tag '$tag' deleted successfully";
            $checkTag = ChannelTags::where("username", $user->user_name)->where("tag", $tagUpper)->where("status", 1)->first();
            if ($checkTag) {
                $checkTag->channel_count = ($checkTag->channel_count > 1) ? ($checkTag->channel_count - 1) : 0;
                $checkTag->save();
            }
        } else {
            return response()->json([
                        'status' => "error",
                        'message' => "Invalid action '$action'."
            ]);
        }

        $account->tags = $newTags;
        $account->save();
        return response()->json([
                    'status' => "success",
                    'message' => $message,
                    'tags' => $newTags
        ]);
    }

    //lấy danh sách tag cho chức năng thêm tag vào kênh
    public function getTags(Request $request) {
        $user = Auth::user();
        $tags = ChannelTags::where("status", 1)->whereIn("username", ["system", $user->user_name])->orderby("id", "desc")->get(["username", "tag", "channel_count"]);
        return response()->json([
                    'status' => 'success',
                    'tags' => $tags
        ]);
    }

    public function deleteTag(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ChannelManagementController.deleteTag|request=' . json_encode($request->all()));
        $tag = $request->tag_name;
//        return response()->json([
//                    'status' => 'success',
//                    'message' => "Tag '$tag' deleted successfully"
//        ]);
        return response()->json([
                    'status' => 'error',
                    'message' => "This function is being developed"
        ]);
    }

    public function epidRewards() {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $startOfDay = $thirtyDaysAgo->copy()->startOfDay();
        $endOfDay = $thirtyDaysAgo->copy()->endOfDay();
        $datas = AccountInfo::where("epid_status", "approved")
                        ->whereBetween("epid_time", [$startOfDay->timestamp, $endOfDay->timestamp])->get();
        foreach ($datas as $data) {
            Log::info("epidRewards $data->id");
            $data->calculateRewards();
        }
    }

    public function getChannelByHash(Request $request) {
        $account = AccountInfo::where("hash_pass", $request->hash_pass)->first(["note as gmail", "chanel_id as channel_id"]);
        return response()->json($account);
    }

    public function getDataCharts(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|ChannelManagementController.getDataCharts|request=" . json_encode($request->all()));
        // Lấy danh sách channel_id từ request
        $channelIds = [];
        if ($request->has('ids')) {
            try {
                $channelIds = json_decode($request->input('ids'), true);
            } catch (\Exception $e) {
                Log::error('Error decoding channel IDs: ' . $e->getMessage());
            }
        }
        
        if (empty($channelIds)) {
            return response()->json([
                        'status' => 'error',
                        'message' => 'No channel IDs provided'
            ]);
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Cho Nginx
        // Vô hiệu hóa bộ đệm PHP
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
        if ($request->is_admin_music) {
            $channels = AccountInfo::whereIn("id", $channelIds)->take(10)
                    ->orderByRaw('FIELD(id, ' . implode(',', $channelIds) . ')')->get();
        } else {
            $channels = AccountInfo::whereIn("id", $channelIds)
                    ->where("user_name", $user->user_code)
                    ->take(10)
                    ->orderByRaw('FIELD(id, ' . implode(',', $channelIds) . ')')
                    ->get();
        }
        if ($channels->isEmpty()) {
            return response()->json([
                        'status' => 'error',
                        'message' => 'No channels found'
            ]);
        }
        foreach ($channels as $channel) {
            $channelId = $channel->id;
            $dataChart = $this->parseYoutubeChart($channel);
            try {
                $responseData = [
                    'status' => $dataChart['status'],
                    'message' => $dataChart['message'],
                    'channel_id' => $channelId,
                    'channel_name' => $channel->chanel_name,
                    'channel_thumb' => $channel->channel_clickup,
                    'subs' => $channel->subscriber_count,
                    'views' => $channel->view_count,
                    'data48hour' => $dataChart['data48hour'],
                    'data60minutes' => $dataChart['data60minutes']
                ];


                echo $this->sseEvent($responseData);
                if (ob_get_length() > 0) {
                    ob_flush();
                }
                flush();
                // Tạm dừng để không gây quá tải
                usleep(200000); // 200ms
            } catch (\Exception $e) {
                Log::error('Error processing channel ' . $channelId . ': ' . $e->getMessage());
                echo $this->sseEvent([
                    'status' => 'error',
                    'message' => 'Error processing channel ID: ' . $channel->chanel_name
                ]);
                if (ob_get_length() > 0) {
                    ob_flush();
                }
                flush();
            }
        }

        // Gửi sự kiện hoàn thành
        echo $this->sseEvent([
            'status' => 'complete',
            'message' => 'All channels processed'
        ]);
        if (ob_get_length() > 0) {
            ob_flush();
        }
        flush();

        exit();
    }

    private function sseEvent($data) {
        return "data: " . json_encode($data) . "\n\n";
    }

    private function parseYoutubeChart($channel) {
        $data48hour = [];
        $data60minutes = [];
        $total48 = 0;
        $total60 = 0;
        $cmd = "python3.9 /home/tools/check_channel.py $channel->hash_pass";
//        $cmd = "C:\\Users\\truon\\AppData\\Local\\Programs\\Python\\Python39\\python.exe D:\\WORK\\TEAM\\autowin\\Python\\check_channel.py $channel->hash_pass";
//        Log::info($cmd);
        $rp = trim(shell_exec($cmd));
//         Log::info($rp);
        if (!empty($rp)) {
            $response = json_decode($rp);
            if (isset($response->message)) {
                return [
                    'status' => 'error',
                    'message' => $response->message,
                    'data48hour' => $data48hour,
                    'data60minutes' => $data60minutes
                ];
            }
            if (isset($response->success) && $response->success == true) {
                $data = $response->analytics;
                if (empty($data->results)) {
                    return [
                        'status' => 'error',
                        'message' => "Not found results of channel $channel->chanel_name",
                        'data48hour' => $data48hour,
                        'data60minutes' => $data60minutes
                    ];
                }
                foreach ($data->results as $result) {
                    if (!empty($result->key)) {
                        if ($result->key == "0__ENTITY_HOURLY_VIEWS") {
                            if (!empty($result->value->resultTable->dimensionColumns[0]->timestamps->values)) {
                                $dates = $result->value->resultTable->dimensionColumns[0]->timestamps->values;
                            }
                            if (!empty($result->value->resultTable->metricColumns[0]->counts->values)) {
                                $values = $result->value->resultTable->metricColumns[0]->counts->values;
                            }
                            foreach ($dates as $date) {
                                $datesText[] = gmdate("Y/m/d H:i:s", $date / 1000 + 7 * 3600);
                            }
                            foreach ($values as $value) {
                                $total48 += $value;
                            }
                            $data48hour = ["label" => $datesText, "value" => $values, "total48" => $total48];
                        }
                        if ($result->key == "0__ENTITY_MINUTELY_VIEWS") {
                            if (!empty($result->value->resultTable->dimensionColumns[0]->timestamps->values)) {
                                $dates2 = $result->value->resultTable->dimensionColumns[0]->timestamps->values;
                                foreach ($dates2 as $date) {
                                    $dates2Text[] = gmdate("Y/m/d H:i:s", $date / 1000 + 7 * 3600);
                                }
                            }
                            if (!empty($result->value->resultTable->metricColumns[0]->counts->values)) {
                                $values2 = $result->value->resultTable->metricColumns[0]->counts->values;
                                foreach ($values2 as $value) {
                                    $total60 += $value;
                                }
                            }
                            $data60minutes = ["label" => $dates2Text, "value" => $values2, "total60" => $total60];
                        }
                    }
                }
            }
        }
        return [
            "status" => 'success',
            "message" => "success",
            "data48hour" => $data48hour,
            "data60minutes" => $data60minutes
        ];
    }

    //2025/04/03 tiến trình tự dộng change info sau 2 tuần, tính từ thời điểm chuyển kênh, confirm_time (ngày chuyển kênh)
}
