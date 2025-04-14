<?php

namespace App\Http\Controllers;

use App\Common\Process\ProcessUtils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Bom;
use App\Http\Models\ShortsDownload;
use App\Http\Models\ShortsTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ShortsController extends Controller {

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|ShortsController.index|request=' . json_encode($request->all()));
        if ($request->is_admin_music || $request->is_supper_admin) {
            $datas = ShortsDownload::whereRaw("1=1")->where("del_status", 0);
        } else {
            $datas = ShortsDownload::where("username", $user->user_name)->where("del_status", 0);
        }
        $queries = [];
        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->channel_genre) && $request->channel_genre != "-1") {
            $datas = $datas->where('genre', $request->channel_genre);
            $queries['channel_genre'] = $request->channel_genre;
        }
        if (isset($request->name) && $request->name != "") {
            $datas = $datas->where('song_name', "like", "%$request->name%");
            $queries['name'] = $request->name;
        }
        if (isset($request->artist) && $request->artist != "") {
            $datas = $datas->where('artist', "like", "%$request->artist%");
            $queries['artist'] = $request->artist;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo id desc
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $sources = DB::select(" select source_url,count(*) as total from shorts_download where status <> 4 and del_status = 0 and source_url in (select source_url from shorts_topic where username = '$user->user_name') group by source_url");
        $topicsSource = ShortsTopic::where("username", $user->user_name)->get(["topic", "source_url"]);
        foreach ($topicsSource as $data) {
            $data->total = 0;
            foreach ($sources as $source) {
                if ($data->source_url == $source->source_url) {
                    $data->total += $source->total;
                }
            }
        }
        $userTopics = ShortsTopic::where("username", $user->user_name)->groupBy('topic')->get(["topic"]);
        foreach ($userTopics as $data) {
            $data->total = 0;
            foreach ($topicsSource as $topic) {
                if ($data->topic == $topic->topic) {
                    $data->total += $topic->total;
                }
            }
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);


//        Log::info(DB::getQueryLog());
        return view('components.shorts', ["datas" => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'topicSelect' => $this->loadTopicShorts($request),
            'userTopics' => $userTopics,
            "genres" => $this->loadChannelGenre($request)]);
    }

    public function find($id) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.find|request=' . $id);
        $bom = Bom::find($id);
        if (!$bom) {
            return response()->json(array("status" => "error", "message" => "Not found Bom Id $id"));
        }
        return $bom;
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ShortsController.store|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);


//        if ($request->shorts == null) {
//            return response()->json(array("status" => "error", "message" => "Shorts is invalid"));
//        }

        if ($request->topic == null) {
            return response()->json(array("status" => "error", "message" => "Topic is invalid"));
        }
        $topic = strtoupper(trim($request->topic));
        $topic = str_replace(" ", "_", $topic);
        $checkTopic = ShortsTopic::where("topic", $topic)->first();
        if ($checkTopic && $user->user_name != $checkTopic->username) {
            return response()->json(array("status" => "error", "message" => "This topic is exists, please choose other name"));
        }
        $txtSource = str_replace(array("\r\n", "\n"), "@;@", trim($request->shorts));
        $arraySource = explode("@;@", $txtSource);
        $count = 0;
        $countVideo = 0;
        $errorLink = "";
        foreach ($arraySource as $link) {
            $source = ShortsTopic::where("source_url", $link)->first();
            if (!$source || $source->username != $user->user_name) {
                $shortsTopic = new ShortsTopic();
                $shortsTopic->username = $user->user_name;
                $shortsTopic->source_url = $link;
                $shortsTopic->topic = $topic;
                $shortsTopic->save();
                $errorLink .= "$link has been added to your account,";
                continue;
            }
//            $checkSource = ShortsDownload::where("source_url", $link)->first();
//            if ($checkSource) {
////                DB::enableQueryLog();
////                ShortsDownload::where("source_url", $link)->update(["share_user" => DB::raw("CONCAT(share_user,'$user->user_name')")]);
//                Log::info(DB::getQueryLog());
//                $errorLink .= "$link belong to $checkSource->username, ";
//                continue;
//            }
            $count++;
            $datas = YoutubeHelper::getListShortVideo($link);
            foreach ($datas as $data) {
                $check = ShortsDownload::where("video_id", $data->video_id)->first();
                if (!$check) {
                    $countVideo++;
                    $insert = new ShortsDownload();
                    $insert->username = $user->user_name;
                    $insert->topic = $topic;
                    $insert->source_url = $link;
                    $insert->video_id = $data->video_id;
                    $insert->video_title = $data->title;
                    $insert->video_thumb = $data->thumb;
                    $insert->create_time = $curr;
                    $insert->views = $data->views;
                    $insert->share_user = '';
                    $insert->video_des = '';
                    $insert->video_tag = '';
                    $insert->save();
                }
            }
        }


        return response()->json(array("status" => "success", "message" => "Success $count link, $countVideo videos, $errorLink"));
    }

    public function shortsSync(Request $request) {
        $processName = "shorts-sync-$request->t";
        if (!ProcessUtils::isfreeProcess($processName)) {
            error_log("$processName is locked");
            return "shorts-sync-$request->t running";
        }
        ProcessUtils::lockProcess($processName);
        $numberThread = 5;
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $topicRunning = [];
        DB::enableQueryLog();
        $temps = DB::select("select distinct topic from shorts_download where status =1");
        foreach ($temps as $temp) {
            $topicRunning[] = $temp->topic;
        }
        $count = ShortsDownload::where("status", 0)->whereNotIn("topic", $topicRunning)->count();
        $shorts = ShortsDownload::where(DB::raw("id % $numberThread"), $request->t)->where("status", 0)->where("del_status", 0);
        if ($count > 0) {
            $shorts = $shorts->whereNotIn("topic", $topicRunning);
        }
        $shorts = $shorts->take(5)->get();
//        Log::info(DB::getQueryLog());
        $root = "/home/automusic.win/public_html/public/temp";
        foreach ($shorts as $short) {
            $start = time();
            $short->status = 1;
            $short->update_time = gmdate("Y/m/d H:i:s", $start + 7 * 3600);
            $short->save();
            $downloadTo = "$root/$short->video_id.webm";
            $thumb = "$root/$short->video_id.jpg";
            $check1 = glob($downloadTo);
            if (count($check1) == 0) {
//                $cmd = "sudo youtube-dl  --proxy http://5.161.128.46:5566 --write-thumbnail --no-mtime --no-playlist  --no-part  --ffmpeg-location /xcxcx/ffmpeg -f mp4 -o $downloadTo $short->video_id";
//                $cmd = "yt-dlp -f mp4 -o $downloadTo $short->video_id";
                $cmd = "yt-dlp -f bv+ba/b -o $downloadTo $short->video_id";
                error_log("$processName $cmd");
                shell_exec($cmd);
            }
            $check = glob($downloadTo);
            if (count($check) > 0) {
                $short->video_size = filesize($downloadTo);
                $short->log = $short->log . PHP_EOL . "$curr download success " . (time() - $start) . "s";
                $short->status = 3;
                $short->save();
                //upload to drive
                $upload = "gbak uploada-sv --server youtube-short --input $downloadTo";
                $result = shell_exec($upload);
                $short->drive_url = str_replace("['", "", trim($result));
                $short->drive_url = trim(str_replace("']", "", $short->drive_url));
                error_log("$processName $short->video_id drive " . $short->drive_url);
                if ($short->drive_url != null) {
                    $short->status = 5;
                }
                $short->save();
                //upload thumb
                if (count(glob($thumb)) > 0) {
                    $result2 = shell_exec("gbak uploada-sv --server youtube-short --input $thumb");
                    $short->drive_img_url = str_replace("['", "", trim($result2));
                    $short->drive_img_url = trim(str_replace("']", "", $short->drive_img_url));
                    $short->save();
                }
            } else {
                $short->log = $short->log . PHP_EOL . "$curr download fail";
                $short->status = 4;
                $short->save();
            }
        }
        ProcessUtils::unLockProcess($processName);
    }

    public function shortUpdate(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ShortsController.shortUpdate|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $short = ShortsDownload::where("id", $request->id)->first();
        if ($short) {

            if ($request->type=='retry') {
                $short->scan = 0;
                $short->status = 0;
                $short->log = $short->log . PHP_EOL . "$curr $user->user_name set status=0";
            } else {
                $short->del_status = 1;
                $log = $short->log;
                $log .= PHP_EOL . "$curr $user->user_name removed";
                $short->log = $log;
            }
            $short->save();
            return array("status" => "success", "message" => "Success", "data" => $short);
        }
        return array("status" => "error", "message" => "Failed");
    }

    public function shortsGet(Request $request) {
        return ShortsDownload::where("video_id", $request->video_id)->first(["id", "video_id", "video_title", "drive_url", "drive_img_url", "video_des", "video_tag"]);
    }

    public function shortsInfo() {
        $processName = "shorts-info";
        if (!ProcessUtils::isfreeProcess($processName)) {
            error_log("$processName is locked");
            return "shorts-info running";
        }
        ProcessUtils::lockProcess($processName);
        $datas = ShortsDownload::where("scan", 0)->get();
        $total = count($datas);
        $i = 0;

        foreach ($datas as $data) {
            $i++;
            $cmd = "youtube-dl --proxy http://5.161.128.46:5566 --skip-download --print-json $data->video_id";
            $temp = shell_exec($cmd);
            $json = json_decode($temp);
            if (!empty($json->description)) {
                $data->video_des = $json->description;
            }
            $data->video_tag = "";
            if (!empty($json->tags)) {
                $data->video_tag = implode(",", $json->tags);
            }
            if (!empty($json->duration)) {
                $data->video_duration = $json->duration;
            }
            error_log("shortsInfo $i/$total $data->id $cmd $data->video_duration");
            $data->scan = 1;
            $data->log = $data->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " scan info";
            $data->save();
        }
        ProcessUtils::unLockProcess($processName);
    }

    public function listTopic(Request $request) {
//        return DB::select("select topic,count(*) as total from shorts_download where status <> 4 and (username='$request->username' or share_user like '%$request->username%') group by topic");
        $sources = DB::select(" select source_url,count(*) as total from shorts_download where status <> 4 and del_status = 0 and source_url in (select source_url from shorts_topic where username = '$request->username') group by source_url");
        $topicsSource = ShortsTopic::where("username", $request->username)->get(["topic", "source_url"]);
        foreach ($topicsSource as $data) {
            $data->total = 0;
            foreach ($sources as $source) {
                if ($data->source_url == $source->source_url) {
                    $data->total += $source->total;
                }
            }
        }
        $userTopics = ShortsTopic::where("username", $request->username)->groupBy('topic')->get(["topic"]);
        foreach ($userTopics as $data) {
            $data->total = 0;
            foreach ($topicsSource as $topic) {
                if ($data->topic == $topic->topic) {
                    $data->total += $topic->total;
                }
            }
        }
        return $userTopics;
    }

    public function listVideoByTopics(Request $request) {
        Log::info('ShortsController.listVideoByTopics|request=' . json_encode($request->all()));
        $limit = 20;
        $page = 0;
//        DB::enableQueryLog();
        if (isset($request->limit)) {
            $limit = $request->limit;
        }
        if (isset($request->offset)) {
            $page = $request->offset;
        }
        $topics = [];
        if (is_array($request->topic)) {
            $topics = $request->topic;
        } else {
            $topics[] = $request->topic;
        }
        $tp = implode("','", $topics);

//        $datas =  ShortsDownload::whereIn("topic", $topics)->where("del_status", 0)->where("status", "<>", 4)->offset($offset)->limit($limit)->get(["video_id"]);

        $datas =  DB::select("select video_id from shorts_download where status <> 4 and del_status =0 and source_url in (select source_url from shorts_topic where topic in('$tp')) limit $limit offset ".($page * $limit));
//        Log::info(DB::getQueryLog());
        return $datas;
    }

}
