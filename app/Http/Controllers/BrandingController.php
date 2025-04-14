<?php

namespace App\Http\Controllers;

use App\Common\Logger;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\BrandManager;
use App\Http\Models\ChannelComment;
use App\Http\Models\RebrandChannel;
use App\Http\Models\RebrandChannelCmd;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class BrandingController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();

        Log::info($user->user_name . '|BrandingController.index|request=' . json_encode($request->all()));
        $arrayRole = explode(",", $user->role);
        $monday = strtotime('monday this week');
        $mondayText = date("Ymd", $monday);
        $sunday = $monday + 6 * 86400;
        $sundayText = date("Ymd", $sunday);
//        $listDate = [];
//        for ($i = 1; $i <= 7; $i++) {
//            $listDate[] = date("Ymd", $monday);
//            $monday = $monday + 86400;
//        }
//        Log::info(json_encode($listDate));
        if (in_array('23', $arrayRole)) {
            $datas = BrandManager::where("designer", $user->user_name);
            if (!isset($request->status_design) || $request->status_design == "-1") {
                $request['status_design'] = 0;
            }
            $workeds = DB::select("select manager,count(*) as total from rebrand_manager where status_design = 1 and design_date >= $mondayText and design_date <= $sundayText group by manager");
            $workedDetails = DB::select("select manager,design_date,count(*) as total from rebrand_manager where designer='$user->user_name' and status_design = 1 and design_date >= $mondayText and design_date <= $sundayText group by manager,design_date");
        } else {
            if (in_array('1', $arrayRole) || in_array('20', $arrayRole)) {
                $datas = BrandManager::whereRaw("1=1");
                $workeds = DB::select("select manager,count(*) as total from rebrand_manager where status_design = 1 and design_date >= $mondayText and design_date <= $sundayText group by manager");
                $workedDetails = DB::select("select manager,design_date,count(*) as total from rebrand_manager where status_design = 1 and design_date >= $mondayText and design_date <= $sundayText group by manager,design_date");
            } else {
                $datas = BrandManager::where("manager", $user->user_name);
                $workeds = DB::select("select manager,count(*) as total from rebrand_manager where manager='$user->user_name' and status_design = 1 and design_date >= $mondayText and design_date <= $sundayText group by manager");
                $workedDetails = DB::select("select manager,design_date,count(*) as total from rebrand_manager where manager='$user->user_name' and status_design = 1 and design_date >= $mondayText and design_date <= $sundayText group by manager,design_date");
            }
        }
        $queries = [];
        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->c5) && $request->c5 != "-1") {
            $datas = $datas->where('manager', $request->c5);
            $queries['manager'] = $request->c5;
        }
        if (isset($request->channel_genre) && $request->channel_genre != "-1") {
            $datas = $datas->where('genre', $request->channel_genre);
            $queries['channel_genre'] = $request->channel_genre;
        }
        if (isset($request->status_design) && $request->status_design != "-1") {
            $datas = $datas->where('status_design', $request->status_design);
            $queries['status_design'] = $request->status_design;
        }
        if (isset($request->status_use_brand) && $request->status_use_brand != "-1") {
            $datas = $datas->where('status_use', $request->status_use_brand);
            $queries['status_use_brand'] = $request->status_use_brand;
        }
        if (isset($request->status_brand) && $request->status_brand != "-1") {
            $datas = $datas->where('status_brand', $request->status_brand);
            $queries['status_brand'] = $request->status_brand;
        }
        if (isset($request->channel_name) && $request->channel_name != "") {
            $datas = $datas->where('channel_name', "like", "%$request->channel_name%");
            $queries['name'] = $request->channel_name;
        }

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo id asc
            $request['sort'] = 'id';
            $request['direction'] = 'desc';
            $queries['sort'] = 'id';
            $queries['direction'] = 'desc';
        }

        $datas = $datas->sortable()->paginate($limit)->appends($queries);

        foreach ($datas as $data) {
            if ($data->sync == 0) {
                $data->lyric_disable = "disabled";
                $data->cross_disable = "disabled";
            }
            if ($data->lyric == 0) {
                $data->cross_disable = "disabled";
            }
        }
//        Log::info(DB::getQueryLog());
        return view('components.branding', ["datas" => $datas,
            'request' => $request,
            'limit' => $limit,
            'designer' => $this->loadDesigner($request),
            'statusBrand' => $this->genStatusBrand($request),
            'statusUseBrand' => $this->genStatusUseBrand($request),
            'statusDesign' => $this->genStatusDesign($request),
            'limitSelectbox' => $this->genLimit($request),
            "genres" => $this->loadChannelGenre($request),
            'channelSubGenre' => $this->loadChannelSubGenre($request),
            'listUser' => $this->genListUserForMoveChannel($user, $request, 1, 2),
            'workeds' => $workeds,
            'workedDetails' => $workedDetails,
            'monday' => $monday,
            'sunday' => $sunday,
        ]);
    }

    public function find(Request $request) {
        $data = BrandManager::where("id", $request->brand_id)->first();
        return response()->json($data);
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BrandingController.store|request=' . json_encode($request->all()));
        if ($request->genre == '-1') {
            return response()->json(array("status" => "error", "message" => "Please select the Genre"));
        }
        if ($request->channel_name == null) {
            return response()->json(array("status" => "error", "message" => "Channel name is invalid"));
        }
        if ($request->style == null) {
            return response()->json(array("status" => "error", "message" => "Style is invalid"));
        }
        if ($request->designer == '-1') {
            return response()->json(array("status" => "error", "message" => "Designer is invalid"));
        }
        $subg = "";
        if (isset($request->channel_subgenre)) {
            $subGenre = $request->channel_subgenre;
            if (count($subGenre) == 0) {
                return response()->json(array("status" => "error", "message" => "Please select the Sub Genre"));
            } else {
                $subg = strtoupper(implode(",", $subGenre));
            }
        }

        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if (isset($request->brand_id)) {
            $brand = BrandManager::where("id", $request->brand_id)->first();
            $brand->log = $brand->log . PHP_EOL . "$curr $user->user_name update to the system";
        } else {
            $check = BrandManager::where("channel_name", $request->channel_name)->first();
            if ($check) {
                return response()->json(array("status" => "error", "message" => "Channel Name is exists"));
            }
            $brand = new BrandManager();
            $brand->create_time = $curr;
            $brand->log = "$curr $user->user_name added to the system";
            $brand->manager = $user->user_name;
        }
        $brand->designer = $request->designer;
        $brand->genre = $request->genre;
        $brand->sub_genre = $subg;
        $brand->channel_name = trim($request->channel_name);
        $brand->style = $request->style;
        $brand->save();

        return response()->json(array("status" => "success", "message" => "Success"));
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BrandingController.update|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if ($request->avatar == null) {
            return response()->json(array("status" => "error", "message" => "Avatar is invalid"));
        }
        if (!Utils::containString($request->avatar, "https://drive.google.com/file")) {
            return response()->json(array("status" => "error", "message" => "Avatar link must have format https://drive.google.com/file/..."));
        }
        if ($request->banner == null) {
            return response()->json(array("status" => "error", "message" => "Banner is invalid"));
        }
        if (!Utils::containString($request->banner, "https://drive.google.com/file")) {
            return response()->json(array("status" => "error", "message" => "Banner link must have format https://drive.google.com/file/..."));
        }
        if ($request->logo == null) {
            return response()->json(array("status" => "error", "message" => "Logo is invalid"));
        }
        if (!Utils::containString($request->logo, "https://drive.google.com/file")) {
            return response()->json(array("status" => "error", "message" => "Logo link must have format https://drive.google.com/file/..."));
        }
        $data = BrandManager::where("id", $request->brand_id)->first();
        if ($data) {
            $path = "/home/automusic.win/public_html/public/brand_image";
            $avatarName = "$data->manager-$data->designer-$data->id-avatar-" . time() . '.png';
            $cmdAvatar = "sudo gbak downloadsv --sv studio --idx $request->avatar --path $path/$avatarName";
            shell_exec($cmdAvatar);
            $check = glob("$path/$avatarName");
            if (count($check) == 0 || filesize("$path/$avatarName") == 0) {
                return response()->json(array("status" => "error", "message" => "Fail to download avatar"));
            }
            $bannerName = "$data->manager-$data->designer-$data->id-banner-" . time() . '.png';
            $cmdBanner = "sudo gbak downloadsv --sv studio --idx $request->banner --path $path/$bannerName";
            shell_exec($cmdBanner);
            $check = glob("$path/$bannerName");
            if (count($check) == 0 || filesize("$path/$bannerName") == 0) {
                return response()->json(array("status" => "error", "message" => "Fail to download banner"));
            }

            $logoName = "$data->manager-$data->designer-$data->id-logo-" . time() . '.png';
            $cmdLogo = "sudo gbak downloadsv --sv studio --idx $request->logo --path $path/$logoName";
            shell_exec($cmdLogo);
            $check = glob("$path/$logoName");
            if (count($check) == 0 || filesize("$path/$logoName") == 0) {
                return response()->json(array("status" => "error", "message" => "Fail to download logo"));
            }
            $data->drive_avatar = $request->avatar;
            $data->drive_banner = $request->banner;
            $data->drive_logo = $request->logo;
            $data->local_avatar = $avatarName;
            $data->local_banner = $bannerName;
            $data->local_logo = $logoName;
            $data->update_time = $curr;
            $log = $data->log;
            $log .= PHP_EOL . "$curr $user->user_name submit avatar and banner";
            $data->log = $log;
            $data->status_design = 1;
            $data->design_date = gmdate("Ymd", time() + 7 * 3600);
            $data->save();
            //thông báo cho manager
            $manager = User::where("user_name", $data->manager)->first();
            $info = explode("@@", $manager->telegram_id);
            if (count($info) >= 2) {
//                $message = "$data->designer đã hoàn thành avatar và banner cho kênh <b>$data->channel_name</b>";
//                $url = "https://api.telegram.org/bot$info[0]/sendMessage?chat_id=-$info[1]&text=$message&parse_mode=html";
//                ProxyHelper::get($url, 2);
            }
            return response()->json(array("status" => "success", "message" => "Success"));
        } else {
            return response()->json(array("status" => "error", "message" => "Not found info of $request->brand_id"));
        }
    }

    public function brandingSaveAboutSection(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BrandingController.brandingSaveAboutSection|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if ($request->genre == '-1') {
            return response()->json(array("status" => "error", "message" => "Please select the Genre"));
        }
        $sections = $request->about_section;
        $count = 0;
        foreach ($sections as $section) {
            if ($section != null && $section != "") {
                $count++;
                $insert = new RebrandChannel();
                $insert->topic = $request->genre;
                $insert->type = "about_section";
                $insert->content = $section;
                $insert->save();
            }
        }
        return response()->json(array("status" => "success", "message" => "Success $count about section"));
    }

    //lưu brand cho kênh đi comment dạo,avatar banner james upload
    public function brandCommentChannel() {
        $names = RebrandChannel::where("type", "POP")->where("topic", "COMMENT")->first();
        $nameArr = json_decode($names->content);
        $channels = AccountInfo::where("user_name", 'autocomment_1669193469')->take(100)->where("is_rebrand", 0)->where("group_channel_id", 987)->get();
        $avatars = array_diff(scandir("/home/automusic.win/public_html/public/brand_comment_pop"), array('..', '.'));
        $index = 0;
        $permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
        foreach ($avatars as $avatar) {
            $channelName = trim($nameArr[$index]);
//            $handle = strtolower(str_replace(" ", "", $channelName)) . substr(str_shuffle($permitted_chars), 0, 1);
            $handle = "";
            $linkAvatar = "https://automusic.win/brand_comment_pop/$avatar";
            Log::info($channels[$index]->chanel_id . "  $linkAvatar");
            $rebrand = new RebrandChannelCmd();
            $rebrand->channel_id = $channels[$index]->chanel_id;
            $rebrand->gmail = $channels[$index]->note;
            $rebrand->ip_email = "165.22.105.138";
            $rebrand->private = 1;
            $rebrand->language = "en";
            $rebrand->category = "CREATOR_VIDEO_CATEGORY_MUSIC";
            $rebrand->country = "US";
            $rebrand->keyword = "";
            $rebrand->first_name = $channelName;
            $rebrand->last_name = $handle;
            $rebrand->link_profile = $linkAvatar;
            $rebrand->link_banner = "";
            $rebrand->about_section = "";
            $rebrand->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $rebrand->last_update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $rebrand->status = 99;
            $rebrand->save();
            $channels[$index]->is_rebrand = 1;
            $channels[$index]->save();
            $index++;
        }
    }

    //brand mail mới,chưa có trong hệ thống, avatar banner di craw từ youtube,
    public function brandCommentChannelNew(Request $request) {
        $datas = ChannelComment::where("status", 0)->whereNull("gmail")->where("avatar_direct", "<>", "")->where("subs", "<", 1000)->where("genre", $request->genre)->take($request->number)->get();

        $index = 0;
        $total = count($datas);
        $permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
        foreach ($datas as $data) {
            $index++;
            $channelName = $data->channel_name;
            //không truyền tên channel vào
//            $channelName = "";

            $accountInfo = AccountInfo::where("user_name", "autocomment_run_1669193469")->where("is_rebrand", 0)->where("channel_genre", $request->genre)->first();
//                $accountInfo = AccountInfo::where("user_name", "kakalot_emailtrang_1680517703")->where("is_rebrand", 0)->first();
//                $accountInfo = AccountInfo::where("user_name", "laphong_emailtrang_1680247568")->where("is_rebrand", 0)->first();
            if (!$accountInfo) {
                error_log("Not found channel");
                return;
            }
            $accountInfo->is_rebrand = 1;
            $accountInfo->chanel_id = $data->id;
            $accountInfo->chanel_name = $channelName;
            $accountInfo->channel_genre = $request->genre;
            $accountInfo->save();
            $data->gmail = $accountInfo->note;

//            $handle = strtolower(str_replace(" ", "", $channelName)) . substr(str_shuffle($permitted_chars), 0, 1);
            $handle = "";
            $script_name = 'upload';
            $func_name = 'brand';
            $type = 65;
//            
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
            $language = "en";
            $private = "";
            $category = "CREATOR_VIDEO_CATEGORY_MUSIC";
            $country = "US";
            $keyword = "";
            $link_profile = $data->avatar_direct;
            $link_banner = $data->banner_direct;
            $description = "";

            $listParams = ["language" => $language, "category" => $category, "country" => $country,
                "keyword" => $keyword, "first_name" => $channelName, "last_name" => $handle,
                "link_profile" => $link_profile, "link_banner" => $link_banner, "description" => $description, "video_source" => $private];
            $listLink = ['link_profile', 'link_banner'];
            $params = [];

            foreach ($listParams as $key => $value) {

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

                $params[] = $param;
            }

            $brand = (object) [
                        "script_name" => $script_name,
                        "func_name" => $func_name,
                        "params" => $params
            ];

            $taskLists[] = $login;
            $taskLists[] = $createChannel;
            //thêm hoặc ko thêm avatar banner
            $taskLists[] = $brand;

            $req = (object) [
                        "gmail" => $data->gmail,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => $type,
                        "studio_id" => $data->id,
                        "piority" => 50,
//                        "is_proxy6" => 1,
//                        "is_bot" => 1,
                        "call_back" => "http://automusic.win/callback/brandnew"
            ];
//            error_log(json_encode($req));
//            Utils::write("brand.txt", json_encode($req));
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            error_log("makeBrandNew $request->genre : $index/$total " . json_encode($bas));
            $data->status = 1;

            if ($bas->mess == "ok") {
                $data->bas_id = $bas->job_id;
            }
            $accountInfo->bas_new_id = $bas->job_id;
            $accountInfo->save();
            $data->handle_name = $handle;
            $data->last_update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $data->save();
        }
    }

    public function checkBrandSuccess() {
        $channels = AccountInfo::where("channel_genre", "RAP")->where("user_name", "autocomment_1669193469")->get();
        $result = "";
        foreach ($channels as $index => $channel) {
            $check = YoutubeHelper::getChannelInfoV2($channel->chanel_id, 1);
            error_log($index . " " . (strtolower(trim($channel->chanel_name))) . " <> " . (strtolower(trim($check['channelName']))));
            if (!empty($check['channelName'])) {
                if (strcmp(strtolower(trim($channel->chanel_name)), strtolower(trim($check['channelName']))) != 0) {
                    $result .= $channel->note . PHP_EOL;
                    error_log("WRONG:" . $channel->note);
                }
            }
        }

        Utils::write("mail.txt", $result);
    }

    //thay handle
    public function replaceHandle() {
        $permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
        $datas = AccountInfo::where("user_name", 'autocomment_1669193469')->where("message", 'laphong')->get();
        foreach ($datas as $data) {
            $avatar = ChannelComment::where("channel_id_brand", $data->chanel_id)->whereNotNull("avatar_direct")->first();
            $channelId = $data->chanel_id;
            $rebrand = new RebrandChannelCmd();
            $rebrand->channel_id = $channelId;
            $rebrand->gmail = $data->note;
            $rebrand->ip_email = "165.22.105.138";
            $rebrand->private = 0;
            $rebrand->language = "en";
            $rebrand->category = "CREATOR_VIDEO_CATEGORY_MUSIC";
            $rebrand->country = "US";
            $rebrand->keyword = "";
            $rebrand->first_name = $data->chanel_name;
            $handle = Utils::slugify($data->chanel_name) . substr(str_shuffle($permitted_chars), 0, 1);
            if (strlen($handle) <= 8) {
                $handle .= rand(9999, 99999);
            }
            $rebrand->last_name = $handle;
            $rebrand->link_profile = $avatar->avatar_direct;
            $rebrand->link_banner = "";
            $rebrand->about_section = "";
            $rebrand->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $rebrand->last_update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $rebrand->status = 9;
            $rebrand->save();
            $data->is_rebrand = 1;
            $data->save();
        }
    }

    public function downloadBrand() {
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $datas = BrandManager::orderBy("id", "desc")->get();
        $total = count($datas);
        $i = 0;
        foreach ($datas as $data) {
            $i++;
            $path = "/home/automusic.win/public_html/public/brand_image";
            $avatarName = "$data->manager-$data->designer-$data->id-avatar-" . time() . '.png';
            $cmdAvatar = "sudo gbak downloadsv --sv studio --idx $data->drive_avatar --path $path/$avatarName";
            shell_exec($cmdAvatar);
            $check = glob("$path/$avatarName");
            if (count($check) == 0 || filesize("$path/$avatarName") == 0) {
                error_log("cant not download drive_avatar $data->id");
            }
            $bannerName = "$data->manager-$data->designer-$data->id-banner-" . time() . '.png';
            $cmdBanner = "sudo gbak downloadsv --sv studio --idx $data->drive_banner --path $path/$bannerName";
            shell_exec($cmdBanner);
            $check = glob("$path/$bannerName");
            if (count($check) == 0 || filesize("$path/$bannerName") == 0) {
                error_log("cant not download drive_banner $data->id");
            }

            $logoName = "$data->manager-$data->designer-$data->id-logo-" . time() . '.png';
            $cmdLogo = "sudo gbak downloadsv --sv studio --idx $data->drive_logo --path $path/$logoName";
            shell_exec($cmdLogo);
            $check = glob("$path/$logoName");
            if (count($check) == 0 || filesize("$path/$logoName") == 0) {
                error_log("cant not download drive_logo $data->id");
            }
            $data->local_avatar = $avatarName;
            $data->local_banner = $bannerName;
            $data->local_logo = $logoName;
            $data->update_time = $curr;
            $log = $data->log;
            $log .= PHP_EOL . "$curr download again";
            $data->log = $log;
            $data->save();
            error_log("downloadBrand $i/$total");
        }
    }

}
