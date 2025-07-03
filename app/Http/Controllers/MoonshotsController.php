<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Logger;
use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Process\ProcessUtils;
use App\Common\Utils;
use App\Common\Youtube\WakeupHelper;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\ApiManager;
use App\Http\Models\AutoWakeupHappy;
use App\Http\Models\CardEndsCommand;
use App\Http\Models\CrossPost;
use App\Http\Models\MoonshotsStats;
use App\Http\Models\RebrandChannelCmd;
use App\Http\Models\Upload;
use App\Http\Models\VideoDaily;
use App\User;
use Illuminate\Http\Request;
use Log;

class MoonshotsController extends Controller {

    public function moonShotsSyncCookie(Request $request) {
        if (isset($request->data)) {
            $datas = AccountInfo::whereNotNull("gologin")->where("chanel_id", trim($request->data))->orWhere("note", trim($request->data))->get();
        } else {
            $datas = AccountInfo::whereNotNull("gologin")->where("is_sync", 1)->where("turn_off_hub", 0)->orderByRaw("rand()")->get();
        }
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
        foreach ($datas as $data) {
            $req = (object) [
                        "gmail" => $data->note,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => 80,
                        "piority" => 80,
            ];
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            Logger::logUpload("moonShotsSyncCookie:$data->note " . json_encode($bas));
        }
    }

    public function moonShotsChannel(Request $request) {
//        Log::info("moonShotsChannel");
//        return "{}";
        $platform = $request->header('platform');
        if ($platform != "AutoWin") {
            return ["message" => "Wrong system!"];
        }
        $locker = new Locker(6789);
        $locker->lock();
        $data = AccountInfo::whereIn("is_sync", [1, 2])->where("sub_tracking", 1)->where("next_time_moon", "<", time())->whereNotNull("gologin")->orderByRaw('RAND()')->first(["id", "note as gmail", "chanel_id as channel_id"]);
        if ($data) {
            $data->next_time_moon = time() + 3600;
            $data->save();
            Logger::logUpload("moonShotsChannel $data->id");
            return $data;
        }
        return "{}";
    }

    public function moonshotsAnalyticUpdate(Request $request) {
//        Log::info('|MoonshotsController.moonshotsAnalyticUpdate|request=' . json_encode($request->all()));
//        Log::info('|MoonshotsController.moonshotsAnalyticUpdate|request=' . $request->channel_id);
        $data = MoonshotsStats::where("channel_id", $request->channel_id)->first();
        if (!$data) {
            $data = new MoonshotsStats();
            $data->channel_id = $request->channel_id;
        }
        $channel = AccountInfo::where("chanel_id", $request->channel_id)->first();
        if ($data->gmail == null) {
            $data->gmail = $channel->note;
        }
        $data->updated_date = gmdate("Y/m/d", time() + 7 * 3600);
        $data->updated_time = gmdate("H:i:s", time() + 7 * 3600);
        $data->data = $request->data;
        $data->save();
        return 1;
    }

    public function moonshotsAlarmAdd(Request $request) {
        Logger::logUpload('MoonshotsController.moonshotsAlarmAdd|request=' . json_encode($request->all()));

        $channel = AccountInfo::where("chanel_id", $request->channel_id)->first();
        $channel->level = $request->level;
        $channel->save();
        $moonShots = MoonshotsStats::where("channel_id", $request->channel_id)->first();
        if (!$moonShots) {
            $moonShots = new MoonshotsStats();
            $moonShots->channel_id = $request->channel_id;
            $moonShots->updated_date = gmdate("Y/m/d", time() + 7 * 3600);
            $moonShots->updated_time = gmdate("H:i:s", time() + 7 * 3600);
            $moonShots->save();
        }
        $moonShots->last_level = $request->level;
        $moonShots->log .= ($moonShots->log != null ? PHP_EOL : "") . "[" . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . "]" . "[$request->level] $request->message";
        $explo = explode(PHP_EOL, $moonShots->log);
        if (count($explo) > 168) {
            array_shift($explo);
        }
        $moonShots->log = implode(PHP_EOL, $explo);
//        $moonShots->log = "[" . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . "]" . "[$request->level] $request->message";
        $moonShots->save();
        if ($request->level == "INFO") {
            $user = User::where("user_code", $channel->user_name)->first();
            $info = explode("@@", $user->telegram_id);
            if (count($info) >= 2) {
                //kênh đã add vào boomvip thì ko báo 2: boomvip off
                if ($channel->is_boomvip == 0 || $channel->is_boomvip == 2) {
                    $message = "$user->user_name có kênh $channel->chanel_name chuẩn bị nổ";
                    $message = urlencode($message);
                    $url = "https://api.telegram.org/bot$info[0]/sendMessage?chat_id=-$info[1]&text=$message&parse_mode=html";
                    Log::info("moonshotsAlarmAdd|$url");
                    ProxyHelper::get($url);
                }
            }
        }
        return 1;
    }

    //gửi lệnh tạo api lên bas
    public function apiRun() {

        $processName = "api-run";
        if (ProcessUtils::isfreeProcess($processName, 30)) {
            ProcessUtils::lockProcess($processName);
            $channels = AccountInfo::where("api_status", 0)->get();
            $total = count($channels);
            error_log("runMakeAp: $total");
            foreach ($channels as $channel) {
//                $url = "http://bas.reupnet.info/profile/assign/group-auto/$channel->note/linux_bas_v2/no_proxy";
//                RequestHelper::callAPI("GET", $url, []);
//                $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/1/no_proxy";
//                RequestHelper::callAPI("GET", $url2, []);
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
                $api = (object) [
                            "script_name" => "api",
                            "func_name" => "create",
                            "params" => []
                ];
                $taskLists[] = $login;
                $taskLists[] = $commit;
                $taskLists[] = $api;
                $req = (object) [
                            "gmail" => $channel->note,
                            "task_list" => json_encode($taskLists),
                            "run_time" => 0,
                            "type" => 66,
                            "piority" => 80,
                            "call_back" => "http://automusic.win/callback/api",
                            //2023/03/02 hoa confirm them is_proxy6
                            "is_proxy6" => 1,
                ];
                $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                error_log("runMakeApi $channel->note $channel->chanel_id " . json_encode($res));
                if ($res->mess == "ok") {
                    $channel->api_status = 1;
                    $channel->api_job_id = $res->job_id;
                    $channel->save();
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }

    //tạo lênh chạy brand lên bas
    public function brandRun() {

        $processName = "brand-run";
        if (!ProcessUtils::isfreeProcess($processName, 20)) {
            Logger::logUpload("$processName is locked");
            return 1;
        }
        ProcessUtils::lockProcess($processName);
        $datas = RebrandChannelCmd::where("status", 0)->where("retry", "<", 3)->get();
        Logger::logUpload("makeBrand " . count($datas));
        foreach ($datas as $data) {
            $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();
            //kiểm tra xem kênh đã login moonshots chưa
//            $isMoonshots = 0;
//            $script_name = 'reup';
//            $func_name = 'rebrand';
//            $type = 7;
//            if ($channel->gologin != null) {
            $isMoonshots = 1;
            $script_name = 'upload';
            $func_name = 'brand';
            $type = 67;
//            }
            $taskLists = [];
            $login = (object) [
                        "script_name" => "profile",
                        "func_name" => "login",
                        "params" => []
            ];
            $language = $data->language;
            $private = $data->private == 1 ? "PRIVATE" : "";
            $category = $data->category;
            $country = $data->country;
            $keyword = $data->keyword;
            $first_name = $data->first_name;
            $last_name = $data->last_name;
            $link_profile = $data->link_profile;
            $link_banner = $data->link_banner;
            $description = $data->about_section;
            $listParams = ["language" => $language, "category" => $category, "country" => $country,
                "keyword" => $keyword, "first_name" => $first_name, "last_name" => $last_name,
                "link_profile" => $link_profile, "link_banner" => $link_banner, "description" => $description,
                "video_source" => $private, "handle" => $channel->handle];
            $listLink = ['link_profile', 'link_banner'];
            $params = [];

            foreach ($listParams as $key => $value) {
                if ($isMoonshots) {
                    if (in_array($key, $listLink) && $value != "") {
                        $param = (object) [
                                    "name" => $key,
                                    "type" => "file",
                                    "value" => $value
                        ];
                    } else {
                        $param = (object) [
                                    "name" => $key,
                                    "type" => "string",
                                    "value" => $value
                        ];
                    }
                } else {
                    $param = (object) [
                                "name" => $key,
                                "type" => "string",
                                "value" => $value
                    ];
                }
                $params[] = $param;
            }
            $params[] = (object) [
                        "name" => "channel_id",
                        "type" => "string",
                        "value" => $channel->chanel_id,
            ];
            $reupload = (object) [
                        "script_name" => $script_name,
                        "func_name" => $func_name,
                        "params" => $params
            ];

            $taskLists[] = $login;
            $taskLists[] = $reupload;

            $req = (object) [
                        "gmail" => $data->gmail,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => $type,
                        "studio_id" => $channel->id,
                        "piority" => 50,
                        "call_back" => "http://automusic.win/callback/brand"
            ];
//            error_log(json_encode($req));
//            Utils::write("brand.txt", json_encode($req));
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            Logger::logUpload("makeBrand: " . json_encode($bas));
            $data->status = 1;
            $data->retry = $data->retry + 1;
            if ($bas->mess == "ok") {
                $data->bas_id = $bas->job_id;
            }
            $data->last_update_time = gmdate("d/m/Y H:i:s", time() + 7 * 3600);
            $data->save();
        }
        ProcessUtils::unLockProcess($processName);
    }

    //auto upload từ studio.automusic.win (v2)
    public function uploadRun() {
//        $locker = new Locker(987);
//        $locker->lock();
        $processName = "upload-run";
        if (ProcessUtils::isfreeProcess($processName, 30)) {
            ProcessUtils::lockProcess($processName);
            $response = RequestHelper::callAPI2("GET", "http://api-magicframe.automusic.win/job/auto-upload/get", []);
            Logger::logUpload("autoUploadStudio:count " . count($response));
            if (count($response) > 0) {
                foreach ($response as $res) {
                    if ($res->success == 0) {
                        Logger::logUpload("autoUploadStudio:$res->id success=0");
                        continue;
                    }
//                    Logger::logUpload("autoUploadStudio:$res->id " . $res->result);
//                    Logger::logUpload("autoUploadStudio:$res->id " . json_encode($res));
//                    Log::info("autoUploadStudio:$res->id " . $res->result);
//                    Log::info("autoUploadStudio:$res->id " . json_encode($res));
                    Logger::logUpload("autoUploadStudio:id=$res->id $res->channel_id");
                    $meta = json_decode($res->reup_config);
                    $language = "en-US";
                    $category = "10";
                    $files = explode(";;", $res->result);
                    if ($res->success == 1 && count($files) >= 2) {
                        $link_video = "gdrive;;resource2@soundhex.com;;" . $files[0];
                        $link_thumbnail = "gdrive;;resource2@soundhex.com;;" . $files[1];
                        if (Utils::containString($res->result, "https")) {
                            $link_video = $files[0];
                            $link_thumbnail = $files[1];
                        }
                    } else {
                        //trường hợp render fail
                        continue;
                    }
                    $title = !empty($meta->title) ? $meta->title : "";
                    $description = !empty($meta->description) ? $meta->description : "";
                    $tag = !empty($meta->tags) ? $meta->tags : "";
                    $location = !empty($meta->location) ? $meta->location : "";
                    $compiler = '[]';
                    $video_source = "";
                    $video_ext = "mp4";

                    $channel = AccountInfo::where("chanel_id", $res->channel_id)->where("upload_type", 2)->first();
                    if (!$channel || $channel->note == null || $channel->note == "") {
                        $req = [
                            "id" => $res->id,
                            "status" => 4,
                            "upload_log" => "Not found channel or it is manual channel",
                            "upload_status" => 4
                        ];
                        Logger::logUpload("autoUploadStudio:$res->id Not found channel or it is manual channel");
                        RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                        continue;
                    }
                    if (Utils::containString($res->result, "None")) {
                        $req = [
                            "id" => $res->id,
                            "status" => 4,
                            "upload_log" => "Result is none",
                            "upload_status" => 4
                        ];
                        Logger::logUpload("autoUploadStudio:$res->id Result is none");
                        RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                        continue;
                    }



                    $isMoonshots = 1;
                    $script_name = 'upload';
                    $func_name = 'upload';
                    $type = 68;
                    $name = "studio_moon";
                    if ($channel->gologin == null) {
                        $req = [
                            "id" => $res->id,
                            "status" => 4,
                            "upload_log" => "Channel has not been moved to moonshots",
                            "upload_status" => 4
                        ];
                        Logger::logUpload("autoUploadStudio:$res->id Channel has not been moved to moonshots");
                        RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                        continue;
                    }
                    //2024/12/16 sang confirm check nếu trong ngày kênh đã upload 3 video thì set runtime = schedule - 1h 
                    $runTime = 0;
                    if (empty($meta->schedule) || $meta->schedule == null || $meta->schedule == '{}') {
                        $schedule = "";
                    } else {
                        $temp = $meta->schedule;
                        if ($isMoonshots) {
                            $timeSec = strtotime("$temp->date $temp->time $temp->time_zone");
                            $temp->time_sec = $timeSec;
                            $resCheckUp = RequestHelper::getRequest("http://bas.reupnet.info/job/check/upload-today/$channel->note");
                            $todayUpload = json_decode($resCheckUp);
                            if (!empty($todayUpload->cnt) && $todayUpload->cnt >= 3) {
                                $runTime = $timeSec - (60 * 60);
                            }
                        }
                        $schedule = json_encode($temp);
                    }

                    $taskListsTiktok = [];
                    $login = (object) [
                                "script_name" => "profile",
                                "func_name" => "login",
                                "params" => []
                    ];

                    //upload tiktok
                    if (!empty($meta->tiktok_auto_upload) && $meta->tiktok_auto_upload) {
                        $tiktokTitle = $meta->tiktok_title;
                        $tiktokDescription = $meta->tiktok_desc;
                        $tiktokDuration = "$meta->tiktok_duration_hours:$meta->tiktok_duration_minutes:$meta->tiktok_duration_seconds";
                        $tiktokTranspose = $meta->tiktok_transpose;
                        $startTime = !empty($meta->tiktok_start_time_hours) ? "$meta->tiktok_start_time_hours:$meta->tiktok_start_time_minutes:$meta->tiktok_start_time_seconds" : "0:0:30";
                        $listParamsTiktok = ["link_video" => $link_video, "title" => $tiktokTitle, "description" => $tiktokDescription,
                            "duration" => $tiktokDuration, "start_time" => $startTime, "transpose" => $tiktokTranspose];
                        $paramsTiktok = [];
                        foreach ($listParamsTiktok as $key => $value) {
                            $paramTiktok = (object) [
                                        "name" => $key,
                                        "type" => "string",
                                        "value" => $value
                            ];
                            $paramsTiktok[] = $paramTiktok;
                        }

                        $tikTokReupload = (object) [
                                    "script_name" => "tiktok",
                                    "func_name" => "upload",
                                    "params" => $paramsTiktok
                        ];
                        $taskListsTiktok[] = $login;
                        $taskListsTiktok[] = $tikTokReupload;
                        $req = (object) [
                                    "gmail" => $meta->tiktok_account,
                                    "task_list" => json_encode($taskListsTiktok),
                                    "run_time" => 0,
                                    "type" => 17,
                                    "studio_id" => $res->id,
                                    "piority" => 80,
                        ];
                        Logger::logUpload("autoUploadStudio:$res->id tiktok " . json_encode($req));
                        $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                        Logger::logUpload("autoUploadStudio:$res->id tiktok " . json_encode($bas));
                        $upload = new Upload();
                        $upload->type = "tiktok";
                        $upload->source_id = $res->id;
                        $upload->retry = 1;
                        if ($bas->mess == "ok") {
                            $upload->bas_id = $bas->job_id;
                        }
                        $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                        $upload->save();
                        Logger::logUpload("autoUploadStudio:$res->id " . json_encode($upload));
//                    continue;
                    }

                    //kênh chạy bằng api
                    if ($channel->version == 2) {
                        $currDate = gmdate("Ymd", time() + 7 * 3600);
                        //giới hạn số video được upload mỗi ngày của kênh
                        if ($channel->confirm != null) {
                            $dailyUpload = json_decode($channel->confirm);

                            if ($currDate != $dailyUpload->date) {
                                $dailyUpload->date = $currDate;
                                $dailyUpload->uploaded = 0;
                            } else if ((($channel->is_boomvip == 0 || $channel->is_boomvip == 2) && $dailyUpload->uploaded >= 5) || ($channel->is_boomvip == 1 && $dailyUpload->uploaded >= 15)) {
                                Logger::logUpload("autoUploadStudio api_$channel->api_type studioId = $res->id $currDate uploaded $dailyUpload->uploaded videos");
                                RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", ["id" => $res->id, "status" => 4, "upload_status" => 4, "upload_log" => "Uploaded over $dailyUpload->uploaded a day"]);
                                $upload = new Upload();
                                $upload->type = "api_$channel->api_type";
                                $upload->source_id = $res->id;
                                $upload->status = 7;
                                $upload->log = "over $dailyUpload->uploaded a day";
                                $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $upload->next_time_scan = time() + 86400;
                                $upload->save();
                                continue;
                            }
                        } else {
                            $dailyUpload = (object) [
                                        "date" => $currDate,
                                        "uploaded" => 1
                            ];
                        }

                        //upload bằng api orfium
                        if ($channel->api_type == 2) {
                            $url_redirect = "https://videomanager.orfium.com/auth";
                            $client_id = "841180336951-3hk8i45arjftt6rmskftvc518nurlv1u.apps.googleusercontent.com";
                            $client_secret = "YMmwipN2NaUAYgch6recQ8zf";
                            $channelCode = $channel->channel_code_orfium;

//                            //sử dụng 33 điểm
//                            $check = RequestHelper::callAPI("GET", "https://autoseo.win/api/check/quota/33", []);
//                            if ($check == 0) {
//                                Logger::logUpload("autoUploadStudio:$res->id out of quota API");
//                                RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", ["id" => $res->id, "status" => 4, "upload_status" => 4, "upload_log" => "Waitting for api quota"]);
//                                $upload = new Upload();
//                                $upload->type = "api";
//                                $upload->source_id = $res->id;
//                                $upload->status = 7;
//                                $upload->log = "out of quota API";
//                                $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
//                                $upload->next_time_scan = time() + 86400;
//                                $upload->save();
//                                continue;
//                            }
                        } else if ($channel->api_type == 3) {
                            //upload bằng api camtasia
                            $url_redirect = "http://127.0.0.1:64980";
                            $client_id = "419933649938-kmi7m4gqamh7htlv5vr7hamq73j71c7h.apps.googleusercontent.com";
                            $client_secret = "7N7FTB37hK6dh6kSKoe2Q08K";
                            $channelCode = $channel->channel_code_camta;
                        } else if ($channel->api_type == 4) {
                            //upload bằng api adobe
                            $url_redirect = "https://oobe.adobe.com";
                            $client_id = "18481039836-hj9vcetv2vu599uhnfn2nrada9pck659.apps.googleusercontent.com";
                            $client_secret = "sFOf-BkaM6JfWjiN2EQtzDfo";
                            $channelCode = $channel->channel_code_adobe;
                        } else if ($channel->api_type == 1) {
                            $req = [
                                "id" => $res->id,
                                "status" => 4,
                                "upload_log" => "You have to add the channel to API Googla or API Orfium",
                                "upload_status" => 4
                            ];
                            RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                            $upload = new Upload();
                            $upload->type = "api_$channel->api_type";
                            $upload->source_id = $res->id;
                            $upload->status = 4;
                            $upload->log = "Add googla or api orfium first";
                            $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                            $upload->save();
                            continue;
                        } else if ($channel->api_type > 4) {
                            $apiManager = ApiManager::where("type", $channel->api_type)->where("status", 1)->first();
                            if (!$apiManager || $channel->channel_code_all == null || $channel->channel_code_all == '[]') {
                                $req = [
                                    "id" => $res->id,
                                    "status" => 4,
                                    "upload_log" => "Not found api_type=$channel->api_type",
                                    "upload_status" => 4
                                ];
                                RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                                $upload = new Upload();
                                $upload->type = "api_$channel->api_type";
                                $upload->source_id = $res->id;
                                $upload->status = 4;
                                $upload->log = "Not found api_type=$channel->api_type";
                                $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $upload->save();
                                continue;
                            }
                            $url_redirect = $apiManager->redirect_uri;
                            $client_id = $apiManager->client_id;
                            $client_secret = $apiManager->client_secret;
                            $channelCodeTemps = json_decode($channel->channel_code_all);
                            foreach ($channelCodeTemps as $cCode) {
                                if ($cCode->type == $channel->api_type) {
                                    $channelCode = $cCode->channel_code;
                                    break;
                                }
                            }
                        }
                        $apiUpload = (object) [
                                    "gmail" => $channel->note,
                                    "url" => $link_video,
                                    "status" => "public",
                                    "thumb_url" => $link_thumbnail,
                                    "title" => $title,
                                    "description" => $description,
                                    "tags" => $tag,
                                    "default_language" => "en",
                                    "url_redirect" => $url_redirect,
                                    "client_id" => $client_id,
                                    "client_secret" => $client_secret,
                                    "channel_code" => $channelCode
                        ];
//                    Utils::write("apiupload.txt", json_encode($apiUpload));
//                    $ips = ["65.21.108.148", "5.161.67.99", "65.108.164.119"];
                        $ips = ["65.21.108.148", "5.161.67.99", "65.108.164.119"];
                        $ip = $ips[rand(0, count($ips) - 1)];
                        Logger::logUpload("autoUploadStudio:$res->id api_$channel->api_type: ip=$ip " . json_encode($apiUpload));
                        $uploadResult = RequestHelper::callAPI("POST", "http://" . $ip . "/pll/video/insert/", $apiUpload);
                        Logger::logUpload("autoUploadStudio:$res->id api_$channel->api_type: " . json_encode($uploadResult));
                        $upload = new Upload();
                        $upload->type = "api_$channel->api_type-$ip";
                        $upload->channel_id = $channel->chanel_id;
                        $upload->source_id = $res->id;
                        $upload->retry = 0;
                        if ($uploadResult->status == 1) {
                            $upload->log = $uploadResult->video_id;
                            $upload->status = 3;

                            //upload thành công thì cộng số video vào số lượng upload trong ngày
                            if ($channel->confirm == null) {
                                $channel->confirm = json_encode((object) [
                                            "date" => $currDate,
                                            "uploaded" => 1,
                                ]);
                            } else {
                                $dailyUpload->uploaded = $dailyUpload->uploaded + 1;
                                $channel->confirm = json_encode($dailyUpload);
                            }
                            $channel->save();
                        } else {
                            $upload->log = $uploadResult->error_message;
                            $upload->status = 4;
                            //retry nếu upload fail
                            if (Utils::containString($uploadResult->error_message, "quotaExceeded") && $channel->channel_code_adobe != null) {
//                            $url_redirect = "https://oobe.adobe.com";
//                            $client_id = "18481039836-hj9vcetv2vu599uhnfn2nrada9pck659.apps.googleusercontent.com";
//                            $client_secret = "sFOf-BkaM6JfWjiN2EQtzDfo";
//                            $channelCode = $channel->channel_code_adobe;
//                            $upload->type = "api_4_retry-$ip";
                                sleep(30);
                                $url_redirect = "https://videomanager.orfium.com/auth";
                                $client_id = "841180336951-3hk8i45arjftt6rmskftvc518nurlv1u.apps.googleusercontent.com";
                                $client_secret = "YMmwipN2NaUAYgch6recQ8zf";
                                $channelCode = $channel->channel_code_orfium;
                                $upload->type = "api_2_retry-$ip";
                                $apiUpload = (object) [
                                            "gmail" => $channel->note,
                                            "url" => $link_video,
                                            "status" => "public",
                                            "thumb_url" => $link_thumbnail,
                                            "title" => $title,
                                            "description" => $description,
                                            "tags" => $tag,
                                            "default_language" => "en",
                                            "url_redirect" => $url_redirect,
                                            "client_id" => $client_id,
                                            "client_secret" => $client_secret,
                                            "channel_code" => $channelCode
                                ];
                                Logger::logUpload("autoUploadStudio:$res->id api_2_retry: ip=$ip " . json_encode($apiUpload));
//                            Logger::logUpload("autoUploadStudio:$res->id api_4_retry: ip=$ip " . json_encode($apiUpload));
                                $uploadResult = RequestHelper::callAPI("POST", "http://" . $ip . "/pll/video/insert/", $apiUpload);
//                            Logger::logUpload("autoUploadStudio:$res->id api_4_retry: " . json_encode($uploadResult));
                                Logger::logUpload("autoUploadStudio:$res->id api_2_retry: " . json_encode($uploadResult));
                                if ($uploadResult->status == 1) {
                                    $upload->log = $uploadResult->video_id;
                                    $upload->status = 3;
                                    $upload->retry = 1;
                                    //upload thành công thì cộng số video vào số lượng upload trong ngày
                                    if ($channel->confirm == null) {
                                        $channel->confirm = json_encode((object) [
                                                    "date" => $currDate,
                                                    "uploaded" => 1,
                                        ]);
                                    } else {
                                        $dailyUpload->uploaded = $dailyUpload->uploaded + 1;
                                        $channel->confirm = json_encode($dailyUpload);
                                    }
                                    $channel->save();
                                }
                            }
                        }
                        $upload->retry = $upload->retry + 1;
                        $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                        $upload->save();

                        $req = [
                            "id" => $upload->source_id,
                            "status" => 5,
                            "upload_log" => $upload->log,
                            "upload_status" => $upload->status
                        ];
                        RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                        Logger::logUpload("autoUploadStudio:$res->id api_$channel->api_type update to studio " . json_encode($req));

                        //check xem có phải lệnh cross post không, nếu là cross post thì update vào bảng cross_post
                        $crossPost = CrossPost::where("job_id", $upload->source_id)->first();
                        if ($crossPost) {
                            $crossPost->video_id = $upload->log;
                            $crossPost->save();
                        }
                        continue;
                    }


                    $listLink = ['link_video', 'link_thumbnail'];
                    $listJson = ['schedule'];
                    $listParams = ["language" => $language, "category" => $category, "link_video" => $link_video,
                        "link_thumbnail" => $link_thumbnail, "title" => $title, "description" => $description,
                        "tag" => $tag, "location" => $location, "compiler" => $compiler,
                        "video_source" => $video_source, "video_ext" => $video_ext, "schedule" => $schedule];
                    $params = [];
                    foreach ($listParams as $key => $value) {
                        if ($isMoonshots) {

                            if (in_array($key, $listLink)) {
                                $param = (object) [
                                            "name" => $key,
                                            "type" => "file",
                                            "value" => $value
                                ];
                            } else if (in_array($key, $listJson)) {
                                $t = "json";
                                if ($value == "") {
                                    $t = "string";
                                }
                                $param = (object) [
                                            "name" => $key,
                                            "type" => $t,
                                            "value" => $value
                                ];
                            } else {
                                $param = (object) [
                                            "name" => $key,
                                            "type" => "string",
                                            "value" => $value
                                ];
                            }
                        } else {
                            $param = (object) [
                                        "name" => $key,
                                        "type" => "string",
                                        "value" => $value
                            ];
                        }
                        $params[] = $param;
                    }
                    $params[] = (object) [
                                "name" => "channel_id",
                                "type" => "string",
                                "value" => $channel->chanel_id,
                    ];
                    //2024/10/07 nếu là user check claim thì sử dụng callback chỗ tùng
                    $callBack = "http://automusic.win/callback/upload";
                    if ($channel->user_name == 'check_claim_1728285682') {
                        $callBack = "https://distro.360promo.fm/api/upload/callback";
                        $param3 = (object) [
                                    "name" => "visibility",
                                    "type" => "string",
                                    "value" => "unlisted"];
                        $params[] = $param3;
                    }
                    //2024/10/28 thêm param public_x
                    if (!empty($meta->public_x)) {
                        $param4 = (object) [
                                    "name" => "public_x",
                                    "type" => "string",
                                    "value" => $meta->public_x];
                        $params[] = $param4;
                    }

                    //thêm handle để phân biệt channel khi upload
                    $param5 = (object) [
                                "name" => "handle",
                                "type" => "string",
                                "value" => $channel->handle];
                    $params[] = $param5;

                    $reupload = (object) [
                                "script_name" => $script_name,
                                "func_name" => $func_name,
                                "params" => $params
                    ];
                    $taskLists = [];
                    $taskLists[] = $login;
                    $taskLists[] = $reupload;
                    $req = (object) [
                                "gmail" => $channel->note,
                                "task_list" => json_encode($taskLists),
                                "run_time" => $runTime,
                                "type" => $type,
                                "studio_id" => $res->id,
                                "piority" => 30,
                                "call_back" => $callBack
                    ];
                    Logger::logUpload("autoUploadStudio:$res->id " . json_encode($req));
                    $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                    Logger::logUpload("autoUploadStudio:$res->id " . json_encode($bas));
                    $upload = new Upload();
                    $upload->type = $name;
                    $upload->channel_id = $channel->chanel_id;
                    $upload->source_id = $res->id;
                    $upload->retry = 1;
                    if ($bas->mess == "ok") {
                        $upload->bas_id = $bas->job_id;
                    }
                    $upload->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                    $upload->save();
                    Logger::logUpload("autoUploadStudio:$res->id: " . json_encode($upload));
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            Logger::logUpload("$processName is locked");
        }
    }

    //thực hiện chạy autowakeup new (dùng list video của kênh)
    public function wakeupRun() {
//        $locker = new Locker(11);
//        $locker->lock();
        $processName = "wakeup-run";
        if (!ProcessUtils::isfreeProcess($processName, 30)) {
            Logger::logUpload("$processName is locked");
            return 1;
        }
        ProcessUtils::lockProcess($processName);
        $curr = time();
        $autoWake = AutoWakeupHappy::where("status", 0)->where("next_time_run", "<=", $curr)->take(50)->get();
        foreach ($autoWake as $wake) {
            if ($wake->gmail == null) {
                $wake->last_excute_time_text = gmdate("Y-m-d H:i:s", time() + (7 * 3600));
                $wake->status = 8;
                $wake->save();
                continue;
            }
            $lstVideo = [];
            $list = [];
            if ($wake->source_type == 1) {
                $listVideoUpload = YoutubeHelper::getPlaylist($wake->playlist_source, 100);
                $listVideoIds = $listVideoUpload['list_video_id'];
                if (count($listVideoIds) == 0) {
                    for ($t = 0; $t < 5; $t++) {
                        error_log("wakeupRun $wake->id retry getPlaylistAll");
                        $listVideoUpload = YoutubeHelper::getPlaylist($wake->playlist_source, 100);
                        if (count($listVideoIds) > 0) {
                            $listVideoIds = $listVideoUpload['list_video_id'];
                            break;
                        }
                    }
                }
                if (count($listVideoIds) > 0) {
                    $list = $listVideoIds;
                }
            } elseif ($wake->source_type == 2) {
                if ($wake->videos_list_source != null) {
                    $list = json_decode($wake->videos_list_source);
                }
            } elseif ($wake->source_type == 3) {
                $videos = VideoDaily::where("channel_id", $wake->channel_id)->where("status", 1)->get();
                foreach ($videos as $video) {
                    $isClaim = false;
                    if (Utils::containString($video->claims, "HEXACORP LTD")) {
                        $isClaim = true;
                    }
                    $temp = (object) [
                                "video_id" => $video->video_id,
                                "is_claim" => $isClaim,
                                "view" => $video->wake_views,
                                "view_incr" => 0,
                                "view_incr_perc" => 0,
                                "child_connect" => null,
                                "child_connect_times" => 0,
                    ];
                    $lstVideo[] = $temp;
                }
                //25/10/2021 sang confirm fix cung number video = 20
                $numVideo = $wake->number_videos;

                $temp = WakeupHelper::createList($lstVideo, $wake->sort, $numVideo);
                //xóa những  video chet
//                $list = [];
//                foreach ($tmps as $tmp) {
//                    Log::info(json_encode($tmp));
//                    if ($tmp->view > 0) {
//                        $list[] = $tmp;
//                    }
//                }
                //validate list video, xóa param
                foreach ($temp as $v) {
                    $v = preg_replace('/\?.*/', '', $v);
                }
                //xóa trùng
                $list = array_values(array_unique($temp));
                if (count($list) < 5) {
                    Logger::logUpload("wakeupRun: $wake->id list video < 5 => stop");
                    $wake->log = "List video < 5";
                    $wake->status = 7;
                    $wake->last_excute_time_text = gmdate("Y-m-d H:i:s", time() + (7 * 3600));
                    $wake->save();
                    continue;
                }
            }

//            //xử lý danh sách ưu tiên thêm vào vị trí 2,4,6,8.. nếu có
            if ($wake->priority_promo_list != null && $wake->priority_promo_list != "" && $wake->priority_promo_list != '[]') {

                $listPromos = json_decode($wake->priority_promo_list);
                $k = 0;
                for ($i = 0; $i < count($list); $i++) {
                    if ($i % 2 != 0) {
                        if ($k < count($listPromos)) {
                            $list[$i] = $listPromos[$k];
                            $k++;
                        }
                    }
                }
            }


//            Log::info("wakeupRun: $wake->id " . json_encode($list));
            // <editor-fold defaultstate="collapsed" desc="MOONSHOTS">
            $taskLists = [];
            $login = (object) [
                        "script_name" => "profile",
                        "func_name" => "login",
                        "params" => []
            ];
            $createPlaylist = (object) [
                        "script_name" => "playlist",
                        "func_name" => "wakeup",
                        "params" => [
                            (object) [
                                "name" => "title",
                                "type" => "string",
                                "value" => $wake->title,
                            ],
                            (object) [
                                "name" => "description",
                                "type" => "string",
                                "value" => "",
                            ],
                            (object) [
                                "name" => "video_id1",
                                "type" => "json",
                                "value" => json_encode($list),
                            ],
                            (object) [
                                "name" => "playlist_id",
                                "type" => "string",
                                "value" => $wake->playlist_id,
                            ]
                        ]
            ];

            $taskLists[] = $login;
            $taskLists[] = $createPlaylist;
            $req = (object) [
                        "gmail" => $wake->gmail,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => 63,
                        "piority" => 10,
                        "studio_id" => $wake->id,
                        "call_back" => "http://automusic.win/callback/wakeup"
            ];
            Logger::logUpload("wakeupRun $wake->id" . json_encode($req));
            $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            if (!empty($res->job_id)) {
                $jobId = $res->job_id;
                $wake->job_id = $jobId;
                $wake->last_excute_time_text = gmdate("Y-m-d H:i:s", time() + (7 * 3600));
                $wake->status = 1;
                $wake->save();
                Logger::logUpload("wakeupRun wakeId=$wake->id jobId=$jobId");
            } else {
                Logger::logUpload("wakeupRun wakeId=$wake->id jobId=null");
            }
            // </editor-fold>
        }
        ProcessUtils::unLockProcess($processName);
    }

    public function cardRun() {
//        $locker = new Locker(1234);
//        $locker->lock();
        $processName = "card-run";
        if (!ProcessUtils::isfreeProcess($processName, 10)) {
            Logger::logUpload("$processName is locked");
            return 1;
        }
        ProcessUtils::lockProcess($processName);
        $datas = CardEndsCommand::where("status", 0)->take(50)->get();
        foreach ($datas as $data) {
            //auto set endcard
            $taskLists = [];
            $paramsCard = [];
            $paramsEndScreen = [];
            $param1 = (object) [
                        "name" => "headers",
                        "type" => "json",
                        "value" => "{}",
            ];
            $param2 = (object) [
                        "name" => "params",
                        "type" => "json",
                        "value" => "{}",
            ];
            $param3 = (object) [
                        "name" => "method",
                        "type" => "string",
                        "value" => "POST",
            ];
            $param4 = (object) [
                        "name" => "response_check",
                        "type" => "string",
                        "value" => "EDIT_EXECUTION_STATUS_DONE",
            ];
            $param5 = (object) [
                        "name" => "url",
                        "type" => "string",
                        "value" => "https://studio.youtube.com/youtubei/v1/video_editor/edit_video?alt=json&key=AIzaSyBUPetSUmoZL-OhlxA7wSac5XinrygCqMo",
            ];
            $param6 = (object) [
                        "name" => "get_start_ms",
                        "type" => "func",
                        "value" => json_encode((object) ["video_id" => $data->video_id]),
            ];

            $paramsCard[] = $param1;
            $paramsCard[] = $param2;
            $paramsCard[] = $param3;
            $paramsCard[] = $param4;
            $paramsCard[] = $param5;
            $paramsCard[] = (object) [
                        "name" => "payload",
                        "type" => "json",
                        "value" => $data->payload_card
            ];
            $taskListCard = (object) [
                        "script_name" => "request",
                        "func_name" => "edit_card",
                        "params" => $paramsCard
            ];
            $taskLists[] = $taskListCard;


            $paramsEndScreen[] = $param1;
            $paramsEndScreen[] = $param2;
            $paramsEndScreen[] = $param3;
            $paramsEndScreen[] = $param4;
            $paramsEndScreen[] = $param5;
            $paramsEndScreen[] = $param6;
            $paramsEndScreen[] = (object) [
                        "name" => "payload",
                        "type" => "json",
                        "value" => $data->playload_ends
            ];
            $taskListEndScreen = (object) [
                        "script_name" => "request",
                        "func_name" => "edit_endscreen",
                        "params" => $paramsEndScreen
            ];
            $taskLists[] = $taskListEndScreen;



            if (count($taskLists) > 0) {
                $req = (object) [
                            "gmail" => $data->gmail,
                            "task_list" => json_encode($taskLists),
                            "run_time" => time(),
                            "type" => 613,
                            "piority" => 80,
                            "studio_id" => $data->id,
                            "call_back" => "http://automusic.win/callback/card"
                ];
//                Logger::logUpload("callbackUpload CARD_ENDSCREEN req:" . json_encode($req));
                $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                Logger::logUpload("callbackUpload CARD_ENDSCREEN res:" . json_encode($bas));

                if ($bas->mess == "ok") {
                    $data->job_id = $bas->job_id;
                    $data->status = 1;
                    $data->save();
                }
            }
        }
        ProcessUtils::unLockProcess($processName);
    }

}
