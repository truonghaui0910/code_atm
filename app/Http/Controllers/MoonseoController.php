<?php

namespace App\Http\Controllers;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\AccountInfo;
use App\Http\Models\Bom;
use App\Http\Models\CampaignStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use function response;

class MoonseoController extends Controller {

    public function listGroup(Request $request) {
        $datas = [];
        if ($request->type == 'normal') {
            $datas = DB::select("SELECT g.name as value, concat('BOOM__',g.name) as name, 
                                SUM(case when m.priority = 1 then 1 else 0 end) as vip,
                                SUM(case when m.priority = 0 then 1 else 0 end) as normal,
                                COUNT(m.genre) AS total
                                FROM genre g 
                                LEFT JOIN bom m ON g.name = m.genre
                                and m.status =1 and m.lyric =1 and m.sync=1 and direct_link is not null
                                where g.type =1 and g.status =1
                                GROUP BY g.name");

            $tiktoks = DB::select("SELECT concat('TIKTOK__',g.playlist_id) as value, concat('TIKTOK__',g.cate) as name, 
                                0 as vip,
                                COUNT(m.playlist_id) AS normal,
                                COUNT(m.playlist_id) AS total
                                FROM tiktok_playlist g 
                                LEFT JOIN tiktok_playlist_songs m ON g.playlist_id = m.playlist_id
                                where g.status =1
                                GROUP BY g.playlist_id,g.cate");

            foreach ($tiktoks as $tiktok) {
                $datas[] = $tiktok;
            }
        } else {
            //2024/03/05 bổ sung trường hợp lấy dữ liệu mapping vs social
            //$request->type = target lấy promo, claim bình thường
            //$request->type = target__facebook lấy promo, claim có fb_music_asset not null

            $socialMusicAsset = "";
            if (Utils::containString($request->type, "__")) {
                $tmp = explode("__", $request->type);
                $type = $tmp[0];
                $key = $tmp[1];
                if ($key == "facebook") {
                    $socialMusicAsset = "and fb_music_asset is not null";
                } elseif ($key == "tiktok") {
                    $socialMusicAsset = "and tt_music_asset is not null";
                }
            } else {
                $type = $request->type;
            }
            if ($type == 'target') {
                $promos = DB::select("select genre,count(*) as number from campaign_statistics where status =1 $socialMusicAsset and type in(1,4) group by genre order by custom_genre");
                foreach ($promos as $promo) {
                    $datas[] = (object) ["value" => "PROMOS_" . "$promo->genre", "name" => "PROMOS_" . "$promo->genre", "total" => $promo->number];
                }
                $claims = DB::select("select concat(custom_genre,'__',genre) as topic,count(*) as number from campaign_statistics where status =1 $socialMusicAsset and type = 2 and custom_genre is not null group by custom_genre,genre order by custom_genre");
                foreach ($claims as $claim) {
                    $datas[] = (object) ["value" => "CLAIMS__" . "$claim->topic", "name" => "CLAIMS__" . "$claim->topic", "total" => $claim->number];
                }
            }
        }
        return response()->json($datas);
    }

    public function listMusic(Request $request) {
//        $request->type == 'normal'
//        $request->type == 'target'
        $datas = [];
        $temps = [];
        DB::enableQueryLog();
        if ($request->type == 'normal') {
            $limit = "";
            if (Utils::containString($request->group, "TIKTOK__")) {
                if (isset($request->number)) {
                    $limit = "limit $request->number";
                }

                $playlistId = str_replace("TIKTOK__", "", $request->group);
                $datas = DB::select("select concat('TIKTOK__',id) as `id`, concat('TIKTOK__',playlist_id) as `group`, "
                                . "`direct_link` as `audio_url`, `direct_link` as `audio_short_url`, `song_name` as `title`, "
                                . "`artist`,cover_image,'m4a' as audio_ext,'tktok' as source_type from `tiktok_playlist_songs` where `playlist_id` = '$playlistId' order by rand() $limit");
            } else {
                $datas = Bom::where("genre", $request->group)
                        ->whereNotNull("direct_link")
                        ->where("status", 1)
                        ->where("lyric", 1)
                        ->where("sync", 1)
                        ->orderByRaw("rand()");
                if (isset($request->number)) {
                    $datas = $datas->take($request->number);
                }
                $datas = $datas->get(["id",
                    "genre as group",
                    "direct_link as audio_url",
                    "direct_link as audio_short_url",
                    "song_name as title",
                    "artist", DB::raw("'boom' as source_type")]);
            }
        } else {
            $socialMusicAsset = "";
            if (Utils::containString($request->type, "__")) {
                $tmp = explode("__", $request->type);
                $type = $tmp[0];
                $key = $tmp[1];
                if ($key == "facebook") {
                    $socialMusicAsset = "fb_music_asset";
                }
                if ($key == "tiktok") {
                    $socialMusicAsset = "tt_music_asset";
                }
            } else {
                $type = $request->type;
            }
            if ($type == 'target') {
                $sourceType = "promo";
                if (Utils::containString($request->group, "PROMOS_")) {
                    $topic = explode("_", $request->group);
                    $genre = $topic[1];

                    $temps = CampaignStatistics::whereIn("type", [1, 4])->where("status", 1)
                            ->where("genre", $genre)
                            ->whereNotNull("audio_url")->whereNotNull("lyric_timestamp_id")
                            ->orderByRaw('RAND()');
                    if (isset($request->number)) {
                        $temps = $temps->take($request->number);
                    }
                    if ($socialMusicAsset != "") {
                        $temps = $temps->whereNotNull($socialMusicAsset);
                    }
                    $temps = $temps->get();
                    $sourceType = "promo";
                } elseif (Utils::containString($request->group, "CLAIMS__")) {
                    $topic = str_replace("CLAIMS__", "", $request->group);
//                    $topic = explode("_", $topic);
//                    $pos = strripos($topic[1], '_');
//                    $customGenre = substr($topic[1], 0, $pos);
//                    $genre = substr($topic[1], $pos + 1, strlen($topic[1]) - 1);

                    $topic = explode("__", $topic);
                    $customGenre = !empty($topic[0]) ? $topic[0] : "";
                    $genre = !empty($topic[1]) ? $topic[1] : "";
                    Log::info("$customGenre $genre");
                    $temps = CampaignStatistics::where("status", 1)->where("type", 2)
                            ->where("custom_genre", $customGenre)
                            ->where("genre", $genre)
                            ->whereNotNull("lyric_timestamp_id");
                    if (isset($request->number)) {
                        $temps = $temps->take($request->number);
                    }
                    if ($socialMusicAsset != "") {
                        $temps = $temps->whereNotNull($socialMusicAsset);
                    }
                    $temps = $temps->get();
                    $sourceType = "claim";
                }
                foreach ($temps as $temp) {
                    $short = $temp->audio_url;
                    if (!empty($temp->audio_short_url)) {
                        $short = $temp->audio_short_url;
                    }
                    $datas[] = (object) [
                                "id" => $temp->id,
                                "group" => $temp->genre,
                                "audio_url" => $temp->audio_url,
                                //sau này làm thêm cột short_url thì thay thế vào, nếu = null thì lấy audio url
                                "audio_short_url" => $short,
                                "title" => $temp->song_name,
                                "artist" => $temp->artist,
                                "source_type" => $sourceType,
                                $socialMusicAsset => $temp->$socialMusicAsset
                    ];
                }
            }
        }
//        Log::info(DB::getQueryLog());
        return response()->json($datas);
    }

    public function loadMusic(Request $request) {
        $data = null;
        if ($request->type == 'normal') {
            if (Utils::containString($request->id, "TIKTOK__")) {
                $id = str_replace("TIKTOK__", "", $request->id);
                $data = DB::select("select concat('TIKTOK__',id) as `id`, concat('TIKTOK__',playlist_id) as `group`, "
                                . "`direct_link` as `audio_url`, `direct_link` as `audio_short_url`, `song_name` as `title`, "
                                . "`artist`,cover_image,'m4a' as audio_ext from `tiktok_playlist_songs` where `id` = $id");
            } else {
                $data = Bom::where("id", $request->id)
                        ->whereNotNull("direct_link")
                        ->where("status", 1)
                        ->where("lyric", 1)
                        ->where("sync", 1)
                        ->orderByRaw("rand()")
                        ->get(["id",
                    "genre as group",
                    "direct_link as audio_url",
                    "direct_link as audio_short_url",
                    "song_name as title",
                    "artist", DB::raw("'mp3' as audio_ext")]);
            }
        } else {
            $socialMusicAsset = "";
            if (Utils::containString($request->type, "__")) {
                $tmp = explode("__", $request->type);
                $type = $tmp[0];
                $key = $tmp[1];
                if ($key == "facebook") {
                    $socialMusicAsset = "fb_music_asset";
                } elseif ($key == "tiktok") {
                    $socialMusicAsset = "tt_music_asset";
                }
            } else {
                $type = $request->type;
            }
            if ($type == 'target') {
                $get = "'claim_promo' as source_type";
                if ($socialMusicAsset != "") {
                    $get = "'claim_promo' as source_type,$socialMusicAsset";
                }
                $data = CampaignStatistics::where("id", $request->id)
                        ->get(["id",
                    "genre as group",
                    "audio_url",
                    "audio_url as audio_short_url",
                    "song_name as title",
                    "artist", DB::raw($get)]);
            }
        }
        return response()->json($data);
    }

    public function getProfileIdByGmail(Request $request) {
//        Log::info('MoonseoController.getProfileidByGmail|request=' . json_encode($request->all()));
        if (!isset($request->gmail)) {
            return null;
        }
        $data = AccountInfo::where("note", $request->gmail)->first(["note as gmail", "gologin as profile_id"]);
        return $data;
    }

    public function viewStudioResult(Request $request) {
        $data = RequestHelper::callAPI2("GET", "http://api-magicframe.automusic.win/job/loadaaa/$request->id", []);
        if ($data->id != 0) {
            if ($data->result != null) {
                return view("layouts.studioresult", ["result" => "https://drive.google.com/file/d/" . explode(";;", $data->result)[0]]);
            } else {
                return view('layouts.studioresult', ["result" => "No result found"]);
            }
        } else {
            return view("layouts.studioresult", ["result" => "No data"]);
        }
    }

    //thêm accountinfo => autuomusic, mailinfo =>automail, tạo moonshot profileId ,gửi lệnh bass qr login
    public function tiktokProfileAdd(Request $request) {
        Log::info('MoonseoController.tiktokProfileAdd|request=' . json_encode($request->all()));
        //thêm vào accountinfo
        $accountInfo = AccountInfo::where("note", $request->account)->first();
        if (!$accountInfo) {
//            return response()->json(["status" => "error", "message" => "$request->account exists", "profile_id" => "profile..."]);
            $accountInfo = new AccountInfo();
        }
        $user = "tiktok_profile_1515486846";
        if (isset($request->username)) {
            $us = User::where("user_name", trim($request->username))->first();
            if (!$us) {
                return response()->json(["status" => "error", "message" => "Not found username"]);
            }
            $user = $us->user_code;
        }
        $accountInfo->user_name = $user;
        $accountInfo->chanel_id = $request->account;
        $accountInfo->chanel_name = $request->account;
        $accountInfo->note = $request->account;
        $accountInfo->gmail = $request->account;
        $accountInfo->emp_code = "teamvictor123456789";
        $accountInfo->save();

        //kiểm tra xem đã có trên automail chưa, nếu chưa có thì thêm vào
        $input = array("gmail" => $accountInfo->note);
        $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
        Log::info("mail get:" . json_encode($mail));
        if (!$mail) {
            //thêm vào automail
            $input = array(
                "gmail" => $accountInfo->note,
                "userCreate" => "tiktok_profile",
                "userUse" => "tiktok_profile"
            );
//            $addMail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/add/", $input);
            $addMail = RequestHelper::postRequest("http://165.22.105.138/automail/api/mail/add/", $input);
//            Log::info("mail add:".json_encode($addMail));
//            if ($addMail != 1) {
//                return response()->json(["status" => "error", "message" => "Add to automail fail", "profile_id" => "profile..."]);
//            }
        } else {
            //nếu có thông tin thì kiểm tra xem có profile_id ko
            if (!empty($mail->profile_id) && $mail->profile_id != null && $mail->profile_id != "") {
                $accountInfo->gologin = $mail->profile_id;
                $accountInfo->save();
            }
        }
        if ($accountInfo->gologin == null) {
            $id = RequestHelper::getGologin("http://profile.autoseo.win/profile-tmp/get-avail", $accountInfo->note);
            $accountInfo->gologin = $id;
            $accountInfo->save();
            //update to automail
            $input2 = array("gmail" => $accountInfo->note, "profile_id" => $id);
            RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input2);
        }

        $taskLists = [];
        $login = (object) [
                    "script_name" => "tiktok",
                    "func_name" => "qrlogin",
                    "params" => []
        ];
        $commit = (object) [
                    "script_name" => "profile",
                    "func_name" => "profile_commit",
                    "params" => []
        ];
        $taskLists[] = $login;
        $taskLists[] = $commit;
        $req = (object) [
                    "gmail" => $accountInfo->note,
                    "task_list" => json_encode($taskLists),
                    "studio_id" => $accountInfo->id,
                    "run_time" => 0,
                    "type" => 70,
                    "piority" => 1,
                    "call_back" => "https://moonseo.app/api/social/tiktok/qrcode",
        ];

        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        $jobId = null;
        if (!empty($res->job_id)) {
            $jobId = $res->job_id;
        } else {
            return response()->json(["status" => "error", "message" => "Add bas job fail", "profile_id" => $accountInfo->gologin, "bas_id" => $jobId]);
        }

        return response()->json(["status" => "success", "profile_id" => $accountInfo->gologin, "bas_id" => $jobId]);
    }

}
