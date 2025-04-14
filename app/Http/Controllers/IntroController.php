<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Http\Models\AccountInfo;
use App\Http\Models\BrandManager;
use App\Http\Models\Intro;
use App\Http\Models\RebrandChannel;
use App\Http\Models\RebrandChannelCmd;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class IntroController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();

        Log::info($user->user_name . '|IntroController.index|request=' . json_encode($request->all()));
        $arrayRole = explode(",", $user->role);
        $monday = strtotime('monday this week');
        $mondayText = date("Ymd", $monday);
        $sunday = $monday + 6 * 86400;
        $sundayText = date("Ymd", $sunday);

        if (in_array('1', $arrayRole) || in_array('20', $arrayRole) || in_array('23', $arrayRole)) {
            $datas = Intro::whereRaw("1=1");
        } else {
            $datas = Intro::where("username", $user->user_name);
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
            $datas = $datas->where('username', $request->c5);
            $queries['username'] = $request->c5;
        }

        if (isset($request->intro_name) && $request->intro_name != "") {
            $datas = $datas->where('intro_name', "like", "%$request->intro_name%");
            $queries['intro_name'] = $request->intro_name;
        }
        if (isset($request->channel_name) && $request->channel_name != "") {
            $datas = $datas->where('channel_name', "like", "%$request->channel_name%");
            $queries['channel_name'] = $request->channel_name;
        }

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo id asc
            $request['sort'] = 'id';
            $request['order'] = 'asc';
            $queries['sort'] = 'id';
            $queries['order'] = 'asc';
        }

        $datas = $datas->sortable()->paginate($limit)->appends($queries);

//        Log::info(DB::getQueryLog());
        return view('components.intro', ["datas" => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'listUser' => $this->genListUserForMoveChannel($user, $request, 1, 2),
            'channels' => $this->loadListChannel($request)
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|IntroController.store|request=' . json_encode($request->all()));

        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if ($request->intro_id == null) {
            $intro = new Intro();
            $intro->create_time = $curr;
            $intro->log = "$curr $user->user_name added to the system";
        } else {
            $intro = Intro::where("id", $request->intro_id)->first();
            if (!$intro) {
                return array("status" => "error", "message" => "Not found intro  $request->intro_id");
            }
            $intro->log = "$curr $user->user_name updated";
        }
        if ($request->intro_link != null) {
            $driveId = Utils::getDriveID($request->intro_link);
            $intro->intro_link = "gdrive;;resource@soundhex.com;;$driveId";
        }
        $intro->username = $user->user_name;
        $intro->app = $request->app;
        $intro->channel_type = $request->channel_type;
        if ($request->channel_type == "SINGLE") {
            $intro->channel_id = $request->channel_id;
            $channel = AccountInfo::where("chanel_id", $request->channel_id)->first();
            $intro->channel_name = $channel->chanel_name;
        }
        $intro->intro_type = $request->intro_type;
        $intro->video_type = $request->video_type;
        $intro->intro_name = $request->intro_name;
        $intro->intro_thumb = $request->intro_thumb;
        $intro->save();
        return response()->json(array("status" => "success", "message" => "Success"));
    }

    public function find(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|IntroController.find|request=' . json_encode($request->all()));
        $intro = Intro::find($request->id);
        if ($intro->intro_link != null) {
            $temp = explode(";;", $intro->intro_link);
            if (count($temp) == 3) {
                $intro->intro_link = "https://drive.google.com/file/d/" . $temp[2] . "/view?usp=share_link";
            }
        }
        return $intro;
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

    public function getIntroTopic(Request $request) {
        $where = "where status=1";
        if (isset($request->username)) {
            $where .= " and username='$request->username'";
        }
        $app = DB::select("SELECT app from intro $where group by app");
        $channelType = DB::select("select channel_type from intro $where group by channel_type");
        $channelId = DB::select("select channel_id,channel_name from intro $where group by channel_id,channel_name");
        $introType = DB::select("select intro_type from intro $where group by intro_type");
        $videoType = DB::select("select video_type from intro $where group by video_type");
        return array("app" => $app, "channel_type" => $channelType, "intro_type" => $introType, "video_type" => $videoType, "channel_id" => $channelId);
    }

    public function getIntroVideo(Request $request) {
        $datas = Intro::whereRaw("1=1")->where("status", 1);
        if (isset($request->username)) {
            $datas = $datas->where("username", $request->username);
        }
        if (isset($request->app)) {
            $datas = $datas->where("app", $request->app);
        }
        if (isset($request->channel_type)) {
            $datas = $datas->where("channel_type", $request->channel_type);
            if ($request->channel_type == "SINGLE") {
                if (isset($request->channel_id)) {
                    $datas = $datas->where("channel_id", $request->channel_id);
                }
            }
        }
        if (isset($request->intro_type)) {
            $datas = $datas->where("intro_type", $request->intro_type);
        }
        if (isset($request->video_type)) {
            $datas = $datas->where("video_type", $request->video_type);
        }
        $datas = $datas->get(['username', 'app', 'channel_type', 'channel_id', 'channel_name', 'intro_type', 'video_type', 'intro_thumb', 'intro_link', 'intro_name']);
        return $datas;
    }

}
