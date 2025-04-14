<?php

namespace App\Http\Controllers;

use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Z1VideoClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class Z1VideoClaimController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|Z1VideoClaimController.index');
        if (in_array('1', explode(",", $user->role))) {
            $datas = Z1VideoClaim::whereRaw("1=1");
        } else {
            $datas = Z1VideoClaim::where("user_name", $user->user_name);
        }
        $queries = [];
        $limit = 10;
        if (isset($request->limit)) {
            if ($request->limit <= 200 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);

        $statistics = DB::select("select user_name,date_scan, sum(views) as views,sum(inc_views) as inc_views from z1_video_claim group by user_name,date_scan");
        return view('components.videoclaim', [
            'datas' => $datas,
            'statistics' => $statistics,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request)
        ]);
    }

    public function ajax(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|Z1VideoClaimController.ajax|request=' . json_encode($request->all()));
        if ($request->list_video == null || $request->list_video == "") {
            return array("status" => "danger", "content" => "You must enter playlist");
        }
        $i = 0;
        $temp = str_replace(array("\r\n", "\n", " "), "@,@", $request->list_video);
        $array_videos = explode("@,@", $temp);
        foreach ($array_videos as $video) {
            $info = YoutubeHelper::processLink($video);
            if ($info["type"] == 3) {
                $video_id = $info["data"];
                $check = Z1VideoClaim::where("video_id", $video_id)->first();
                if (!$check) {
                    $z1VideoClaim = new Z1VideoClaim();
                    $z1VideoClaim->user_name = $request->user_name;
                    $z1VideoClaim->create_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                    $z1VideoClaim->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                    $z1VideoClaim->video_id = $video_id;
                    $z1VideoClaim->views_logs = '[]';
                    $z1VideoClaim->save();
                    $i++;
                }
            }
        }
        return array("status" => "success", "content" => "Success $i videos");
    }

    public function scan(Request $request) {
        if ($request->command == 1) {
            $datas = Z1VideoClaim::all();
        } else {
            $datas = Z1VideoClaim::where("status", 0)->get();
        }
        $date = gmdate("m-d-Y", time() + 7 * 3600);
        $i = 0;
        $j = 0;
        $total = count($datas);
        foreach ($datas as $data) {
            $check = Z1VideoClaim::where("video_id", $data->video_id)->first();
            $info = YoutubeHelper::getVideoInfoV2($data->video_id);
            if ($info["status"] == 0) {
                for ($t = 0; $t < 5; $t++) {
                    error_log("Z1VideoClaim.scan retry $data->video_id");
                    $info = YoutubeHelper::getVideoInfoV2($data->video_id);
                    if ($info["status"] == 1) {
                        break;
                    }
                }
            }

            if ($check) {
                $inc_views = $info["view"] - $check->views;
                if ($inc_views < 0) {
                    $inc_views = 0;
                }
                $views_logs = json_decode($check->views_logs);
                $flag = 0;
                foreach ($views_logs as $log) {
                    if ($log->date == $date) {
                        $flag = 1;
                        $log->views = $info["view"];
                    }
                }
                if ($flag == 0) {
                    $views_log = (object) [
                                'date' => $date,
                                'views' => $info["view"]
                    ];
                    $views_logs[] = $views_log;
                }
                $check->inc_views = $inc_views;
                $check->views = $info["view"];
                $check->date_scan = $date;
                $check->views_logs = json_encode($views_logs);
                $check->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                $check->status = $info["status"];
                $i++;
                $check->save();
                if ($info["status"] == 0) {
                    $j++;
                }
                error_log("Z1VideoClaimController.scan $i/$total $data->video_id   " . $info["status"] . ' views=' . $info["view"]);
            }
        }
        return array("status" => "success", "content" => "Success $i videos, $j videos die");
    }

    //xóa 1 ngày đã scan rồi để scan lại
    public function roolBackScanVideoClaim() {
        $i = 0;
        //ngày muốn xóa
        $dateRoll = '11-30-2020';
        //ngày trước đó
        $dateGetBack = '10-06-2020';
        $datas = Z1VideoClaim::where("views",0)->get();
        $total = count($datas);
        foreach ($datas as $data) {
            $i++;
            $viewsLogs = array();
            if ($data->views_logs != null) {
                $viewsLogs = json_decode($data->views_logs);
                $data->date_scan = $dateGetBack;
                $data->save();
                if (count($viewsLogs) > 0) {
                    foreach ($viewsLogs as $index => $viewsLog) {

                        if ($viewsLog->date == $dateGetBack) {
                            $data->views = $viewsLog->views;
                            $data->save();
                        }
                        if ($viewsLog->date == $dateRoll) {
                            unset($viewsLogs[$index]);
                            $data->views_logs = json_encode($viewsLogs);
                            $data->save();
                        }
                    }
                }
            }
            error_log("roolBackScanVideoClaim $i/$total $data->video_id");
        }
    }

    public function checkViewsByDate(Request $request) {

        $datas = Z1VideoClaim::where("user_name", $request->user_name)->get();
        Log::info(count($datas));
        $total = 0;
        foreach ($datas as $data) {
            if ($data->views_logs != null) {
                $viewLogs = json_decode($data->views_logs);
                foreach ($viewLogs as $viewLog) {
                    if ($viewLog->date == $request->date) {
                        $total = $total + $request->views;
                        break;
                    }
                }
            }
        }
        return $total;
    }

}
