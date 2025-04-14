<?php

namespace App\Http\Controllers;

use App\Common\Process\ProcessUtils;
use App\Common\Utils;
use App\Events\ChatEvent;
use App\Http\Models\StudioDriveSave;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function auth;
use function event;

class StudioController extends Controller {

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();

        Log::info($user->user_name . '|StudioController.index|request=' . json_encode($request->all()));
        if ($request->is_admin_music) {
            $datas = StudioDriveSave::where("del_status", 0);
        } else {
            $datas = StudioDriveSave::where("del_status", 0)->where("username", $user->user_name);
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
            $data->drive_video_id = "";

            if ($data->drive_video != "None" && $data->drive_video != null) {
                $data->drive_video_id = explode(";;", $data->drive_video)[2];
            }
            $data->drive_thumb_id = "";
            if ($data->drive_thumb != "None" && $data->drive_thumb != null) {
                $data->drive_thumb_id = explode(";;", $data->drive_thumb)[2];
            }
        }

//        Log::info(DB::getQueryLog());

        return view('components.studio', ["datas" => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'listUser' => $this->genListUserForMoveChannel($user, $request, 1, 2),
        ]);
    }

    public function update(Request $request) {
        Log::info('ApiController.update|request=' . json_encode($request->all()));
        StudioDriveSave::where("id", $request->id)->update(["del_status" => 1]);
        return response()->json(["status" => "success", "message" => "Success"]);
    }

    public function studioDriveSave(Request $request) {
        Log::info('ApiController.studioDriveSave|request=' . json_encode($request->all()));
        if (isset($request->import_type)) {
            if ($request->channel_name == null) {
                return response()->json(["status" => "error", "message" => "Channel Name cannot be empty"]);
            }
            if ($request->drive_links == null) {
                return response()->json(["status" => "error", "message" => "Drive Link cannot be empty"]);
            }
            $drives = Utils::textArea2Array($request->drive_links);
            $success = 0;
            $fail = 0;
            foreach ($drives as $drive) {
                $driveID = Utils::getDriveID($drive);
                if ($driveID == null) {
                    $driveID = trim($drive);
                }
                $check = StudioDriveSave::where("drive_video_source", $driveID)->where("del_status", 0)->first();
                if ($check) {
                    $fail++;
                    continue;
                }
                $insert = new StudioDriveSave();
                $insert->username = auth()->user()->user_name;
                $insert->channel_name = trim($request->channel_name);
                $insert->drive_video_source = $driveID;
                $insert->created = Utils::timeToStringGmT7(time());
                $insert->save();
                $success++;
            }
            return response()->json(["status" => "success", "message" => "Success $success, Fail $fail. Please wait for upload"]);
        } else {
            if (!isset($request->result) || $request->result == null || $request->result == "") {
                return response()->json(["status" => "error", "message" => "Result cannot be empty"]);
            }
            $check = StudioDriveSave::where("studio_result", $request->result)->where("del_status", 0)->first();
            if ($check) {
                return response()->json(["status" => "error", "message" => "Result already exists"]);
            }
            $split = explode(";;", $request->result);
            if (count($split) != 2) {
                return response()->json(["status" => "error", "message" => "Result is invalid"]);
            }
            $insert = new StudioDriveSave();
            $insert->username = $request->username;
            $insert->channel_id = $request->channel_id;
            $insert->channel_name = $request->channel_name;
            $insert->studio_result = $request->result;
            $insert->status = 0;
            $insert->drive_video_source = $split[0];
//        $data1 = (object) ["url" => "https://drive.google.com/file/d/$video/view"];
//        $thumb = $split[1];
//        $data2 = (object) ["url" => "https://drive.google.com/file/d/$thumb/view"];
//        $result1 = RequestHelper::callAPI2("POST", 'http://driver.69hot.info/persis-drive/add', $data1);
//        Log::info("result1:" . json_encode($result1));
//        if ($result1->status == 2) {
//            $insert->drive_video = $result1->leech_drive;
//        } else {
//            return response()->json(["status" => "error", "message" => "Upload video to drive fail"]);
//        }
//        $result2 = RequestHelper::callAPI2("POST", 'http://driver.69hot.info/persis-drive/add', $data2);
//        Log::info("result2:" . json_encode($result2));
//        if ($result2->status == 2) {
//            $insert->drive_thumb = $result2->leech_drive;
//        } else {
//            return response()->json(["status" => "error", "message" => "Upload thumb to drive fail"]);
//        }
            $insert->created = Utils::timeToStringGmT7(time());
            $insert->save();
        }

        return response()->json(["status" => "success", "message" => "Success, please wait for upload"]);
    }

    public function downloadAndUpload() {
        $processName = "archive-drive";
        if (ProcessUtils::isfreeProcess($processName, 30)) {
            ProcessUtils::lockProcess($processName);
            $datas = StudioDriveSave::where("del_status", 0)->where("status", 0)->whereNotNull("drive_video_source")->get();
            foreach ($datas as $data) {
                $data->status = 1;
                $data->updated = Utils::timeToStringGmT7(time());
                $data->save();
                $cmd = "gbak download-with-name --sv studio-result --idx \"$data->drive_video_source\" --dic /tmp";
                error_log("Download Cmd: $cmd");
                $donwload = shell_exec($cmd);
                error_log("downloaded: $donwload");
                $file_path = trim($donwload);
                if ($file_path == "None" || $file_path == null) {
                    $data->status = 2;
                    $data->updated = Utils::timeToStringGmT7(time());
                    $data->save();
                    continue;
                }
                $cmd2 = "gbak uploada-email --email hoa@soundhex.com --input \"$file_path\"";
                error_log("Upload Cmd: $cmd2");
                $upload = shell_exec($cmd2);
                error_log("Uploaded: $upload");
                $cmd3 = "rm -rf $file_path";
                shell_exec($cmd3);
                $data->drive_video = trim($upload);
                if ($data->drive_video == null || $data->drive_video == "" || $data->drive_video == "None") {
                    $data->status = 2;
                } else {
                    $data->status = 3;
                }
                $data->updated = Utils::timeToStringGmT7(time());
                $data->save();
                if ($data->status == 3) {
                    $notify = (object) [
                                "type" => 0,
                                "job_id" => $data->id,
                                "title" => "Drive Archive",
                                "message" => "Archive success id $data->id",
                                "comment" => null,
                                "redirect" => "/studio/drive?id=$data->id",
                                "noti_id" => uniqid()
                    ];
                    $user = User::where("user_name", $data->username)->first();
                    event(new ChatEvent($user, [$data->username], $notify));
                }
            }

            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }
    
    public function loadChannels(Request $request){
        $user =Auth::user();
        $datas = $this->loadMainChannelsJsons($request,$user->user_code);
        return $datas;
    }

}
