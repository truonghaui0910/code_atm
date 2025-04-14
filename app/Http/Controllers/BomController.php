<?php

namespace App\Http\Controllers;

use App\Common\Network\EpidemicSoundHelper;
use App\Common\Network\RequestHelper;
use App\Common\Process\ProcessUtils;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Bom;
use App\Http\Models\BomAlbum;
use App\Http\Models\BomArtist;
use App\Http\Models\BomGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use TheSeer\Tokenizer\Exception;

class BomController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.index|request=' . json_encode($request->all()));
        $datas = Bom::leftJoin('bom_albums', 'bom.album_id', '=', 'bom_albums.id')
                ->select('bom.*', 'bom_albums.album_name', 'bom_albums.album_cover')
                ->where("bom.status", 1);
        $queries = [];
        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        $listSong = [];
        $listVip = [];
        $listNormal = [];
        $bomGroup = null;
        if (isset($request->group_bom_filter) && $request->group_bom_filter != "-1") {
            $bomGroup = BomGroups::where("id", $request->group_bom_filter)->where("del_status", 0)->first();
            $listSong = [];
            $listVip = [];
            if ($bomGroup) {
                if ($bomGroup->list_priority != null) {
                    $listVip = json_decode($bomGroup->list_priority);
                }
                if ($bomGroup->list_song != null) {
                    $listSong = json_decode($bomGroup->list_song);
                }
                if (empty($listSong) && empty($listVip)) {
                    $datas = $datas->where('bom.id', 0);
                    $queries['group_bom_filter'] = $request->group_bom_filter;
                } else {
                    $listNormal = array_diff($listSong, $listVip);
                    if (count($listSong) > 0) {
                        $datas = $datas->whereIn('bom.id', $listSong);
                        $queries['group_bom_filter'] = $request->group_bom_filter;
                    }
                }
            }
        }
        if (isset($request->channel_genre) && $request->channel_genre != "-1") {
            $datas = $datas->where('bom.genre', $request->channel_genre);
            $queries['channel_genre'] = $request->channel_genre;
        }
//        if (isset($request->name) && $request->name != "") {
//            $datas = $datas->where('song_name', "like", "%$request->name%");
//            $queries['name'] = $request->name;
//        }
        if (isset($request->name) && $request->name != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('bom.song_name', 'like', '%' . $request->name . '%')->orWhere('id', $request->name);
                if (Utils::containString($request->name, ",")) {
                    $c1 = explode(',', $request->name);
                    $arrayId = [];
                    foreach ($c1 as $arr) {
                        if ($arr != "") {
                            $arrayId[] = trim($arr);
                        }
                    }
                    $q->orWhereIn("bom.id", $arrayId);
                }
            });
            $queries['song_name'] = $request->song_name;
        }
        if (isset($request->artist) && $request->artist != "") {
            $datas = $datas->where('bom.artist', "like", "%$request->artist%");
            $queries['artist'] = $request->artist;
        }
        if (isset($request->c5) && $request->c5 != '-1') {
            $datas = $datas->where('bom.username', $request->c5);
            $queries['c5'] = $request->c5;
        }
        if (isset($request->is_lyric) && $request->is_lyric != "-1") {
            $datas = $datas->where('bom.lyric', $request->is_lyric);
            $queries['is_lyric'] = $request->is_lyric;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->direction)) {
                $queries['direction'] = $request->direction;
            }
        } else {

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

            if (strlen($data->song_name) > 40) {
                $data->song_name = substr_replace($data->song_name, "...", 50, 100);
            }
            if (strlen($data->artist) > 40) {
                $data->artist = substr_replace($data->artist, "...", 50, 100);
            }
            $data->is_vip_group = 0;
            foreach ($listVip as $vip) {
                if ($data->id == $vip) {
                    $data->is_vip_group = 1;
                }
            }
        }
        $countPriority = DB::select("select count(*) as total from bom where priority=1 and status =1");
//        Log::info(DB::getQueryLog());
//        $listUser = $this->genListUserForMoveChannel($user, $request, 2);
        return view('components.bom', ["datas" => $datas,
            'selected_group' => $bomGroup,
            'selected_all' => $listSong,
            'selected_vip' => $listVip,
            'selected_normal' => $listNormal,
            'request' => $request,
            'limit' => $limit,
            'countPriority' => $countPriority[0]->total,
            'limitSelectbox' => $this->genLimit($request),
            'groupBom' => $this->loadGroupBom($request),
            'vipStatus' => $this->genVipStatusGroupBom($request),
            'isLyric' => $this->genIsLyric($request),
            'list_users' => $this->genListUserForMoveChannel($user, $request, 1, 3),
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
        Log::info($user->user_name . '|BomController.store|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if ($request->genre == '-1') {
            return response()->json(array("status" => "error", "message" => "Please select the Genre"));
        }
        if ($request->deezerId == null && $request->localId == null) {
            return response()->json(array("status" => "error", "message" => "Enter Deezer Link/Id or Local Id"));
        }
        if ($request->deezerId != null && $request->localId != null) {
            return response()->json(array("status" => "error", "message" => "You can only enter the Deezer Link/Id or the Local Id"));
        }

        $listAllSong = [];
        $localId = null;
        $isrc = null;
        $deezerId = null;
        $results = [];
        //thêm mới
        if ($request->bom_id == null) {
            if ($request->deezerId != null) {

                $listDeezerId = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->deezerId)));

//                if (count($listDeezerId) == 1) {
//                    if ($request->songName == null) {
//                        return response()->json(array("status" => "error", "message" => "Song Name is invalid"));
//                    }
//                    if ($request->artist == null) {
//                        return response()->json(array("status" => "error", "message" => "Artist is invalid"));
//                    }
//                }
                $fail = 0;
                $success = 0;
                foreach ($listDeezerId as $dzid) {
                    $result = (object) [
                                "status" => 2,
                                "deezer" => $dzid,
                                "song_name" => "",
                                "artist" => "",
                                "is_lyric" => 0
                    ];
                    $deezerId = null;
                    if (is_numeric($dzid)) {
                        $deezerId = $dzid;
                    } else {
                        if (!Utils::containString($dzid, "deezer.com")) {
                            $fail++;
                            $result->status = 3;
                            $results[] = $result;
                            continue;
                        }
                        preg_match("/track\/(\d+)/", $dzid, $matches);
                        if (count($matches) == 2) {
                            $deezerId = $matches[1];
                        }
                        if ($deezerId == null) {
                            $result->status = 3;
                            $results[] = $result;
                            $fail++;
                            continue;
                        }
                    }
//                    Log::info("deezerId $deezerId");
//                if (is_numeric($request->deezerId)) {
//                    $deezerId = $request->deezerId;
//                } else {
//                    if (!Utils::containString($request->deezerId, "deezer.com")) {
//                        return response()->json(array("status" => "error", "message" => "You must enter deezer link"));
//                    }
//                    preg_match("/track\/(\d+)/", $request->deezerId, $matches);
//                    if (count($matches) == 2) {
//                        $deezerId = $matches[1];
//                    }
//                    if ($deezerId == null) {
//                        return response()->json(array("status" => "error", "message" => "Deezer link is invalid"));
//                    }
//                }
                    $isLyric = 0;
                    $isSync = 0;
                    $isrc = null;
                    $songTitle = null;
                    $songArtist = null;
//                    //check bai hat da co lyric va da download mp3 ve system chua
//                    $temp = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?format=json&check_lyric=$deezerId");
//                    $checkLyric = json_decode($temp);
//                    if (!empty($checkLyric->lyric_status)) {
//                        $isLyric = $checkLyric->lyric_status;
//                    }
//                    if (!empty($checkLyric->song_status)) {
//                        $isSync = $checkLyric->song_status;
//                    }
                    //check bài hát dã đươc download về hệ thống chưa
                    if ($isSync == 0) {
                        //nếu chưa được down thì thực hiện download
                        Log::info("http://source.automusic.win/deezer/track/get/$deezerId");
                        $res = RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$deezerId");
                        Log::info("download $res");

//                        if ($res != null && $res != "") {
//                            $response = json_decode($res);
//                            $isrc = $response->isrc;
//                            $songTitle = $response->title;
//                            $songArtist = $response->artist;
//                            $isSync = 1;
//                            if ($response->lyric_sync != "" && $response->lyric_sync != "null" && $response->lyric_sync != null) {
//                                $isLyric = 1;
//                            }
//                        } else {
//                            continue;
//                        }
                    }

                    $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$deezerId");
//                    $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$deezerId");
                    if ($trackRes != null && $trackRes != "") {
                        $track = json_decode($trackRes);
                        if ($track->count > 0) {
                            if (!empty($track->results[0])) {
                                $isrc = $track->results[0]->isrc;
                                $songTitle = $track->results[0]->title;
                                $songArtist = $track->results[0]->artist;
                                if ($track->results[0]->lyric_sync != "" && $track->results[0]->lyric_sync != "null" && $track->results[0]->lyric_sync != null) {
                                    $isLyric = 1;
                                }
                                if ($track->results[0]->url_128 != "") {
                                    $isSync = 1;
                                }
                            }
                        }
                    }
                    Log::info("$deezerId $isrc $songTitle $songArtist");
                    $result->song_name = $songTitle;
                    $result->artist = $songArtist;
                    $result->is_lyric = $isLyric;
                    if ($songTitle == null || $songArtist == null) {
                        $result->status = 3;
                    }
                    $check = Bom::where("deezer_id", $deezerId)->where("status", 1)->first();
                    if ($check) {
                        $check->song_name = $songTitle;
                        $check->artist = $songArtist;
                        $check->isrc = $isrc;
                        $check->save();
                        $listAllSong[] = $check->id;
                        $fail++;
                        $result->status = 2;
                        $result->bom_id = $check->id;
                        $results[] = $result;
                        continue;
                    }
                    $bom = new Bom();
                    $bom->username = $user->user_name;
                    $bom->genre = $request->genre;
                    $bom->song_name = $songTitle;
                    $bom->deezer_id = $deezerId;
                    $bom->local_id = $localId;
                    $bom->artist = $songArtist;
                    $bom->social = $request->social;
                    $bom->deezer_artist_id = $request->deezerArtistId;
                    $bom->created = $curr;
                    $bom->created_timestamp = time();
                    $bom->count = 0;
                    $bom->lyric = $isLyric;
                    $bom->sync = $isSync;
                    $bom->isrc = $isrc;
                    $bom->status = 1;
                    $bom->log = "$curr $user->user_name added to the system";
                    $bom->save();
                    $success++;
                    $result->status = 1;
                    $result->bom_id = $bom->id;
                    $results[] = $result;
                    $listAllSong[] = $bom->id;
                }
                //thêm vào group 
                $bomGroups = BomGroups::whereIn("id", $request->local_group)->get();
                foreach ($bomGroups as $bGroup) {
                    $listSong = [];
                    if ($bGroup->list_song != null) {
                        $listSong = json_decode($bGroup->list_song);
                    }
                    $mergedArray = array_merge($listSong, $listAllSong);
                    $uniqueArray = array_unique($mergedArray);
                    $uniqueArray = array_values($uniqueArray);
                    $bGroup->list_song = json_encode($uniqueArray);
                    $bGroup->save();
                }
                return response()->json(array("status" => "success", "message" => "Success $success", "results" => $results));
            } else if ($request->localId != null) {
                if ($request->songName == null) {
                    return response()->json(array("status" => "error", "message" => "Song Name is invalid"));
                }
                if ($request->artist == null) {
                    return response()->json(array("status" => "error", "message" => "Artist is invalid"));
                }
                $isLyric = 1;
                $isSync = 1;
                $localId = trim($request->localId);
                $check = Bom::where("local_id", $localId)->first();
                if (!$check) {
                    $bom = new Bom();
                    $bom->username = $user->user_name;
                    $bom->genre = $request->genre;
                    $bom->song_name = $request->songName;
                    $bom->deezer_id = $deezerId;
                    $bom->local_id = $localId;
                    $bom->artist = $request->artist;
                    $bom->social = $request->social;
                    $bom->deezer_artist_id = $request->deezerArtistId;
                    $bom->created = $curr;
                    $bom->created_timestamp = time();
                    $bom->count = 0;
                    $bom->lyric = $isLyric;
                    $bom->sync = $isSync;
                    $bom->isrc = $isrc;
                    $bom->status = 1;
                    $bom->log = "$curr $user->user_name added to the system";
                    $bom->save();
                    $listAllSong[] = $bom->id;
                } else {
                    return response()->json(array("status" => "error", "message" => "The song " . ($deezerId != null ? $deezerId : $localId) . " was added to the system by $check->username at $check->created"));
                }
                //thêm vào group 
                $bomGroups = BomGroups::whereIn("id", $request->local_group)->get();
                foreach ($bomGroups as $bGroup) {
                    $listSong = [];
                    if ($bGroup->list_song != null) {
                        $listSong = json_decode($bGroup->list_song);
                    }
                    $mergedArray = array_merge($listSong, $listAllSong);
                    $uniqueArray = array_unique($mergedArray);
                    $uniqueArray = array_values($uniqueArray);
                    $bGroup->list_song = json_encode($uniqueArray);
                    $bGroup->save();
                }
            }
        } else {
            //edit
            $bom = Bom::find($request->bom_id);
            if (!$bom) {
                return response()->json(array("status" => "error", "message" => "Not found Bom Id $request->bom_id"));
            }
            if ($bom->album_id != null) {
                return response()->json(array("status" => "error", "message" => "This song has been distributed, you cannot edit it."));
            }
            $deezerId = null;
            if ($request->deezerId != null) {
                if (is_numeric($request->deezerId)) {
                    $deezerId = $request->deezerId;
                } else {
                    if (!Utils::containString($request->deezerId, "deezer.com")) {
                        return response()->json(array("status" => "error", "message" => "You must enter deezer link"));
                    }
                    preg_match("/track\/(\d+)/", $request->deezerId, $matches);
                    if (count($matches) == 2) {
                        $deezerId = $matches[1];
                    }
                    if ($deezerId == null) {
                        return response()->json(array("status" => "error", "message" => "Deezer link is invalid"));
                    }
                }
            }
            if ($request->songName == null) {
                return response()->json(array("status" => "error", "message" => "Song Name is invalid"));
            }
            if ($request->artist == null) {
                return response()->json(array("status" => "error", "message" => "Artist is invalid"));
            }
            $songName = trim($request->songName);
            $artist = trim($request->artist);
            $localId = trim($request->localId);
            //2024/11/15 update bài hát mà có localid hoăc deezerid thì set lyric ok, sync ok
            if (isset($request->localId) || isset($request->deezerId)) {
                if (trim($request->localId) != $bom->local_id || $bom->deezer_id != $deezerId) {
                    $bom->sync = 1;
                    $bom->lyric = 1;
                }
            }
            $bom->local_id = $localId;
            $bom->deezer_id = $deezerId;
            $bom->genre = $request->genre;
            $bom->song_name = $songName;
            $bom->artist = $artist;
            $bom->social = $request->social;
            $bom->log = $bom->log . PHP_EOL . "$curr $user->user_name update to the system";
            //2024/12/05 update thông tin lên timestamp
            $header = array(
                'Authorization: Token 783667efd4d78e99c9b38de66eca82c5246cf6ee',
                'Content-Type: application/json'
            );

            if (isset($request->deezerId)) {
                //lấy thông tin bài deezer trên hệ thống timestamp
                $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$deezerId");
                if ($trackRes != null && $trackRes != "") {
                    $track = json_decode($trackRes);
                    if ($track->count > 0) {
                        if (!empty($track->results[0])) {
                            $timestampId = $track->results[0]->id;
                            $data = (object) [
                                        "title" => $songName,
                                        "title_short" => $songName,
                                        "artist" => $artist,
                                        "deezer_id" => $deezerId,
                                        "id" => $timestampId
                            ];
                            RequestHelper::callAPI2("PUT", "http://54.39.49.17:6132/api/tracks/$timestampId/", $data, $header);
                        }
                    }
                }
            }
            if (isset($request->localId)) {
                $data = (object) [
                            "title" => $songName,
                            "artist" => $artist,
                            "id" => $localId
                ];
                RequestHelper::callAPI2("PUT", "https://cdn.soundhex.com/api/v1/timestamp/$localId/", $data, array('Content-Type: application/json'));
            }
            $bom->save();
        }
        return response()->json(array("status" => "success", "message" => "Success"));
    }

    public function boomSync(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.boomSync|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $boom = Bom::where("id", $request->boom_id)->first();
        if ($boom) {
            $res = RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$boom->deezer_id");
            Log::info("res $res");
            if ($res != null && $res != "") {
                $response = json_decode($res);
                $isrc = $response->isrc;
                if ($response->lyric_sync != "") {
                    $boom->lyric = 1;
                }
                $boom->sync = 1;
                $boom->isrc = $isrc;
                $log = $boom->log;
                $log .= PHP_EOL . "$curr $user->user_name sync";
                $boom->log = $log;
                $boom->save();
                return array("status" => "success", "message" => "Success");
            }
            return array("status" => "error", "message" => "Sync failed");
        }
        return array("status" => "error", "message" => "Not found this song on system");
    }

    public function boomRemove(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.boomRemove|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);

        //set vip
        if ($request->type == 'change_vip_genre') {
            $boom = Bom::where("id", $request->boom_id)->first();
            $boom->priority = $boom->priority == 1 ? 0 : 1;
            $boom->log = $boom->log . PHP_EOL . "$curr $user->user_name set priority=$boom->priority";
            $boom->save();
            return array("status" => "success", "message" => "Success", "is_vip" => $boom->priority, "is_reload" => 0);
        }
        if ($request->type == 'change_vip_group') {
            $isVip = 0;
            $bomGroup = BomGroups::where("id", $request->group_id)->first();
            if ($bomGroup->list_priority != null) {
                $vips = json_decode($bomGroup->list_priority);
            } else {
                $vips = [];
            }
            if (in_array($request->boom_id, $vips)) {
                //xóa nếu đã tồn tại
                $temps = array_diff($vips, [$request->boom_id]);
                $vips = array_values($temps);
                $isVip = 0;
            } else {
                $vips[] = (int) $request->boom_id;
                $isVip = 1;
            }
            $bomGroup->list_priority = json_encode($vips);
            $bomGroup->save();
            return array("status" => "success", "message" => "Success", "is_vip" => $isVip, "is_reload" => 0);
        }
        if ($request->type == 'remove_from_group') {
            $bomGroup = BomGroups::where("id", $request->group_id)->first();
            if ($bomGroup->list_priority != null) {
                $vips = json_decode($bomGroup->list_priority);
            } else {
                $vips = [];
            }
            if ($bomGroup->list_song != null) {
                $songs = json_decode($bomGroup->list_song);
            } else {
                $songs = [];
            }
            if (in_array($request->boom_id, $vips)) {
                $temps = array_diff($vips, [$request->boom_id]);
                $vips = array_values($temps);
            }
            if (in_array($request->boom_id, $songs)) {
                $temps = array_diff($songs, [$request->boom_id]);
                $songs = array_values($temps);
            }
            $bomGroup->list_song = json_encode($songs);
            $bomGroup->list_priority = json_encode($vips);
            $bomGroup->save();
            return array("status" => "success", "message" => "Success", "is_reload" => 1);
        }
        if ($request->type == 'delete_group') {
            $bomGroup = BomGroups::where("id", $request->group_id)->first();
            $bomGroup->del_status = 1;
            $bomGroup->save();
            return array("status" => "success", "message" => "Success", "is_reload" => 1);
        }
        if ($request->type == 'delete_song') {
            if ($request->mutiple) {
                //xóa nhiều bài
//                $bomId = $request->chkBomAll;
                $bomId = $request->bom_array;
                Log::info("xoas nhieu bai");
                Log::info(json_encode($request->bom_array));
            } else {
                Log::info("xoas 1 bai");
                $bomId = [];
                $bomId[] = $request->boom_id;
            }
            if ($request->is_admin_music) {
                $booms = Bom::whereIn("id", $bomId)->get();
            } else {
                $booms = Bom::whereIn("id", $bomId)->where("username", $user->user_name)->get();
            }
            Log::info(json_encode($booms));
            $count = 0;
            foreach ($booms as $boom) {
                $boom->status = 0;
                $log = $boom->log;
                $log .= PHP_EOL . "$curr $user->user_name removed";
                $boom->log = $log;
                $boom->save();
                $count++;
                //xóa trong các group
                $groups = BomGroups::where("del_status", 0)->get();
                foreach ($groups as $group) {
                    $listSong = $group->list_song != null ? json_decode($group->list_song) : [];
                    $listSong = array_diff($listSong, [$boom->id]);
                    $listSong = array_values($listSong);
                    $listPriority = $group->list_priority != null ? json_decode($group->list_priority) : [];
                    $listPriority = array_diff($listPriority, [$boom->id]);
                    $listPriority = array_values($listPriority);
                    $group->list_song = json_encode($listSong);
                    $group->list_priority = json_encode($listPriority);
                    $group->save();
                }
            }
            return array("status" => "success", "message" => "Success $count", "is_reload" => 1);
        }
        if ($request->type == 'set_releasable') {
            if ($request->mutiple) {
                $bomId = $request->bom_array;
            } else {
                $bomId = [];
                $bomId[] = $request->boom_id;
            }
            if ($request->is_admin_music) {
                $booms = Bom::whereIn("id", $bomId)->get();
            } else {
                $booms = Bom::whereIn("id", $bomId)->where("username", $user->user_name)->get();
            }
            $count = 0;
            foreach ($booms as $boom) {
                $boom->is_releasable = 1;
                $log = $boom->log;
                $log .= PHP_EOL . "$curr $user->user_name is_releasable=1";
                $boom->log = $log;
                $boom->save();
                $count++;
            }
            return array("status" => "success", "message" => "Success $count", "is_reload" => 1);
        }
//            if (isset($request->priority)) {
//                //set vip genre
//                if ($request->group_id == '-1') {
//                    $boom->priority = $boom->priority == 1 ? 0 : 1;
//                    $boom->log = $boom->log . PHP_EOL . "$curr $user->user_name set priority=$boom->priority";
//                } else {
//                    //set vip group
//                    $bomGroup = BomGroups::where("id", $request->group_id)->first();
//                    if ($bomGroup->list_priority != null) {
//                        $vips = json_decode($bomGroup->list_priority);
//                    } else {
//                        $vips = [];
//                    }
//                    if (in_array($request->boom_id, $vips)) {
//                        //xóa nếu đã tồn tại
//                        $temps = array_diff($vips, [$request->boom_id]);
//                        $vips = array_values($temps);
//                    } else {
//                        $vips[] = $request->boom_id;
//                    }
//                    $bomGroup->list_priority = json_encode($vips);
//                    $bomGroup->save();
//                }
//            } else {
//                //xóa bài hát 
//                $boom->status = 0;
//                $log = $boom->log;
//                $log .= PHP_EOL . "$curr $user->user_name removed";
//                $boom->log = $log;
//            }
//            $boom->save();
//            return array("status" => "success", "message" => "Success", "boom" => $boom);
//        
//        return array("status" => "error", "message" => "Not found Boom");
    }

    public function boomMakeLyric(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.boomMakeLyric|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $boom = Bom::where("id", $request->boom_id)->first();
        if ($boom) {
            $log = $boom->log;
            $log .= PHP_EOL . "$curr $user->user_name request make lyric";
            $boom->log = $log;
            $boom->lyric = 2;
            $boom->save();
            $lyricText = "";
            if ($boom->lyric_text != null && $boom->lyric_text != "") {
                $lyricText = urlencode($boom->lyric_text);
            }
            if ($boom->deezer_id != null) {
                $temp = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$boom->deezer_id");
                $data = json_decode($temp);
                return array("status" => "success", "type" => "deezer", "id" => $data->results[0]->id, "url" => $data->results[0]->url_128, "username" => $user->user_name);
            } else {
                return array("status" => "success",
                    "type" => "direct",
                    "lyric" => $lyricText,
                    "url" => $boom->direct_link,
                    "username" => $user->user_name,
                    "title" => urlencode($boom->song_name),
                    "artist" => urlencode($boom->artist),
                    "id" => $boom->id);
            }
        }
        return array("status" => "error", "message" => "Remove failed");
    }

    public function boomFinishMakeLyric(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.boomFinishMakeLyric|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $boom = Bom::where("id", $request->boom_id)->first();
        if ($boom) {
            $boom->lyric = 1;
            $log = $boom->log;
            $log .= PHP_EOL . "$curr $user->user_name finished make lyric";
            $boom->log = $log;
            $boom->save();
            return array("status" => "success", "message" => "Success");
        }
        return array("status" => "error", "message" => "Failed");
    }

    public function exportBoom(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.exportBoom|request=' . json_encode($request->all()));
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30);
        try {

            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=boom_' . date('Ymd') . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];


            $videos = Bom::where("status", 1)->get(["genre", "artist", "song_name", "deezer_artist_id", "deezer_id"]);


            $lists = $videos->toArray();
            $title = array('Genre', 'Artist', 'Song Name', "Deezer Artist Id", "Deezer Id");
            array_unshift($lists, $title);

            $callback = function() use ($lists) {
                $FH = fopen('php://output', 'w');
                foreach ($lists as $row) {
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };
            return response()->stream($callback, 200, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    //danh sach genres cua bom music
    public function genresList() {
        $datas = DB::select("SELECT g.name, 
                                SUM(case when m.priority = 1 then 1 else 0 end) as vip,
                                SUM(case when m.priority = 0 then 1 else 0 end) as normal,
                                COUNT(m.genre) AS number
                                FROM genre g 
                                LEFT JOIN bom m ON g.name = m.genre
                                and m.status =1 and m.lyric =1 and m.sync=1
                                where g.type =1 and g.status =1
                                GROUP BY g.name");
        return $datas;
    }

    //đếm số bài ưu tiên trong topic
    public function genresCountVip(Request $request) {
        if (Utils::containString($request->genre, "GROUP__")) {
            $param = explode("__", $request->genre);
            $grId = $param[1];
            $data = BomGroups::where("id", $grId)->first();
            $listVip = $data->list_priority != null ? json_decode($data->list_priority) : [];
            return count($listVip);
        } else {
            if (Utils::containString($request->genre, "__")) {
                $param = explode("__", $request->genre);
                $genre = $param[0];
            } else {
                $genre = trim($request->genre);
            }
            return Bom::where("genre", $genre)->where("status", 1)->where("lyric", 1)->where("sync", 1)->where("priority", 1)->count();
        }
    }

    //danh sach bai hat bom theo genre
    //2023/03/06 hòa confirm trả về list vip đứng trước+ list normal đung sau
    public function genresSongs(Request $request) {
        DB::enableQueryLog();
        //2023/04/27 bổ sung group
        if (Utils::containString($request->genre, "GROUP__")) {
            //GROUP__1
            //GROUP__1__PRIORITY
            //GROUP__1__VIEW__DESC
            $param = explode("__", $request->genre);
            $grId = $param[1];
            $bomGroup = BomGroups::where("id", $grId)->first();
            $listAllId = $bomGroup->list_song != null ? json_decode($bomGroup->list_song) : [];
            $listVipId = $bomGroup->list_priority != null ? json_decode($bomGroup->list_priority) : [];
            $listNormalId = [];

            if (count($listAllId) == 0 && count($listVipId) == 0) {
                return [];
            }
            $tmps = array_diff($listAllId, $listVipId);
            foreach ($tmps as $tmp) {
                $listNormalId[] = $tmp;
            }
            //2024/11/13 nếu gọi từ studio thì ko order by rand()
            if (isset($request->is_front)) {
                $order = "id desc";
            } else {
                $order = "RAND()";
            }
            $listVip = Bom::whereIn("id", $listVipId)->where("status", 1)->where("sync", 1)->orderByRaw($order)->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
            if (count($param) == 3) {
                //chỉ lấy priority
                return $listVip;
            } else if (count($param) == 4) {
                $order = $param[2] . ' ' . $param[3];
            }
            $listNormal = Bom::whereIn("id", $listNormalId)->whereNotIn("id", $listVipId)->where("status", 1)->where("sync", 1)->orderByRaw($order)->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
//            Log::info(DB::getQueryLog());
            $result = [];
            if (count($listVip) > 0) {
                foreach ($listVip as $data) {
                    $result[] = $data;
                }
            }
            if (count($listNormal) > 0) {
                foreach ($listNormal as $data) {
                    $result[] = $data;
                }
            }
//            Log::info("VIP " . json_encode($listVip));
//            Log::info("NOR " . json_encode($listNormal));
            return $result;
        } else {
            if (Utils::containString($request->genre, "__")) {
                $param = explode("__", $request->genre);
                if (Utils::containString($request->genre, "PRIORITY")) {
                    return Bom::where("genre", $param[0])->where("status", 1)->where("sync", 1)->where("priority", 1)->orderByRaw('RAND()')->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
                } else {
                    $genre = $param[0];
                    $order = "priority desc," . $param[1] . ' ' . $param[2];
//                $bom = Bom::where("genre", $param[0])->where("status", 1)->where("lyric", 1)->where("sync", 1)->orderByRaw("priority desc,".$param[1] . ' ' . $param[2])->get(["id", "genre","priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social","count"]);
                }
            } else {
                $genre = $request->genre;
                $order = "priority desc,RAND()";
//            $bom = Bom::where("genre", $request->genre)->where("status", 1)->where("lyric", 1)->where("sync", 1)->orderByRaw('RAND()')->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social","count"]);
            }
            $listNormal = Bom::where("genre", $genre)->where("status", 1)->where("sync", 1)->orderByRaw($order)->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
            return $listNormal;
        }
    }

    //2025/02/27 check kết quả danh sách nếu trùng 2 bài đầu vs lần trước thì random lại
    public function genresSongs2(Request $request) {
        DB::enableQueryLog();
        //2023/04/27 bổ sung group
        if (Utils::containString($request->genre, "GROUP__")) {
            //GROUP__1
            //GROUP__1__PRIORITY
            //GROUP__1__VIEW__DESC
            $param = explode("__", $request->genre);
            $grId = $param[1];
            $bomGroup = BomGroups::where("id", $grId)->first();
            $listAllId = $bomGroup->list_song != null ? json_decode($bomGroup->list_song) : [];
            $listVipId = $bomGroup->list_priority != null ? json_decode($bomGroup->list_priority) : [];
            $listNormalId = [];

            if (count($listAllId) == 0 && count($listVipId) == 0) {
                return [];
            }
            $tmps = array_diff($listAllId, $listVipId);
            foreach ($tmps as $tmp) {
                $listNormalId[] = $tmp;
            }
            //2024/11/13 nếu gọi từ studio thì ko order by rand()
            if (isset($request->is_front)) {
                $order = "id desc";
            } else {
                $order = "RAND()";
            }
            $listVip = Bom::whereIn("id", $listVipId)->where("status", 1)->where("sync", 1)->orderByRaw($order)->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
            if (count($param) == 3) {
                //chỉ lấy priority
                return $listVip;
            } else if (count($param) == 4) {
                $order = $param[2] . ' ' . $param[3];
            }
            $listNormal = Bom::whereIn("id", $listNormalId)->whereNotIn("id", $listVipId)->where("status", 1)->where("sync", 1)->orderByRaw($order)->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
//            Log::info(DB::getQueryLog());
            $result = [];
            if (count($listVip) > 0) {
                foreach ($listVip as $data) {
                    $result[] = $data;
                }
            }
            if (count($listNormal) > 0) {
                foreach ($listNormal as $data) {
                    $result[] = $data;
                }
            }
//            Log::info("VIP " . json_encode($listVip));
//            Log::info("NOR " . json_encode($listNormal));
            return $result;
        } else {
            if (Utils::containString($request->genre, "__")) {
                $param = explode("__", $request->genre);
                if (Utils::containString($request->genre, "PRIORITY")) {
                    return Bom::where("genre", $param[0])->where("status", 1)->where("sync", 1)->where("priority", 1)->orderByRaw('RAND()')->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
                } else {
                    $genre = $param[0];
                    $order = "priority desc," . $param[1] . ' ' . $param[2];
//                $bom = Bom::where("genre", $param[0])->where("status", 1)->where("lyric", 1)->where("sync", 1)->orderByRaw("priority desc,".$param[1] . ' ' . $param[2])->get(["id", "genre","priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social","count"]);
                }
            } else {
                $genre = $request->genre;
                $order = "priority desc,RAND()";
//            $bom = Bom::where("genre", $request->genre)->where("status", 1)->where("lyric", 1)->where("sync", 1)->orderByRaw('RAND()')->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social","count"]);
            }
            $listNormal = Bom::where("genre", $genre)->where("status", 1)->where("sync", 1)->orderByRaw($order)->get(["id", "genre", "priority", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social", "count", "duration_ms", "direct_link", "source_id", DB::raw("'boom' as source_type")]);
            return $listNormal;
        }
    }

    //update count bom khi su dung
    public function bomUpdate(Request $request) {
        $deezerIds = explode(",", $request->deezer_ids);
        $count = 0;
        foreach ($deezerIds as $deezerId) {
            $bom = Bom::where("deezer_id", $deezerId)->orWhere("local_id", $deezerId)->first();
            if ($bom) {
                $bom->user_used = $request->username;
                $bom->last_used = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $bom->count = $bom->count + 1;
//                $log = $bom->log;
//                $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $request->username made video from studio";
//                $bom->log = $log;
                $bom->save();
                $count++;
            }
        }
        return $count;
    }

    public function getSocial(Request $request) {
        $data = Bom::where("song_name", 'like', "%$request->song_name%")->where("status", 1)->where("lyric", 1)->where("sync", 1)->first(["genre", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social"]);
        if (!$data) {
            return 0;
        }
        return $data;
    }

    //2023/04/27 boomdb chia theo group
    //danh sách group theo user
    public function groupsList(Request $request) {
        if (isset($request->user)) {
            $user = $request->user;
        } else {
            $user = Auth::user()->user_name;
        }
        $datas = BomGroups::where("username", $user)->where("del_status", 0)->orderBy("id", "desc")->get();
        foreach ($datas as $data) {
            $listSong = $data->list_song != null ? json_decode($data->list_song) : [];
            $listVip = $data->list_priority != null ? json_decode($data->list_priority) : [];
            $number = count($listSong);
            $data->vip = count($listVip);
            $data->normal = $number - count($listVip);
            $data->number = $number;
//            if ($number > 0) {
//                $count = DB::select("select SUM(case when priority = 1 then 1 else 0 end) as vip,
//                                            SUM(case when priority = 0 then 1 else 0 end) as normal
//                                                from bom where id in (" . implode(",", $listSong) . ")");
//                $data->vip = $count[0]->vip;
//                $data->normal = $count[0]->normal;
//            }
        }
        return $datas;
    }

    public function addBomToGroup(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.addBomToGroup|request=' . json_encode($request->all()));
        $bomGroup = BomGroups::where("id", $request->group_id)->first();
        if ($bomGroup) {
            $listSong = [];
            if ($bomGroup->list_song != null) {
                $listSong = json_decode($bomGroup->list_song);
            }

            //thêm 1 bài vào group
            if ($request->type == 0) {
                if (in_array($request->boom_add_id, $listSong)) {
                    $temps = array_diff($listSong, [$request->boom_add_id]);
                    $listSong = [];
                    foreach ($temps as $temp) {
                        $listSong[] = $temp;
                    }
                } else {
                    $listSong[] = (int) $request->boom_add_id;
                }
                $bomGroup->list_song = json_encode($listSong);
                $bomGroup->save();
            } else {
                if (!isset($request->chkBomAll) || count($request->chkBomAll) == 0) {
                    return array("status" => "error", "message" => "Please choose the song");
                }
                if (isset($request->is_remove)) {
                    //xóa nhiều bài hát ra khỏi group
                    $result = array_values(array_diff($listSong, $request->chkBomAll));
                    $bomGroup->list_song = json_encode($result);
                    $bomGroup->save();
                } else {
                    //thêm nhiều bài vào 1 group
                    $tmpResult = [];
                    $temps = array_merge($listSong, $request->chkBomAll);
                    foreach ($temps as $temp) {
                        if (!in_array((int) $temp, $tmpResult)) {
                            $tmpResult[] = (int) $temp;
                        }
                    }
                    $bomGroup->list_song = json_encode($tmpResult);
                    $bomGroup->save();
                }
            }
        }
        return array("status" => "success", "message" => "Success");
    }

    public function addPriority(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.addPriority|request=' . json_encode($request->all()));
        $bomGroup = BomGroups::where("id", $request->group_id)->first();
        $isVip = 0;
        if ($bomGroup) {
            $listSong = [];
            $listVip = [];
            if ($bomGroup->list_song != null) {
                $listSong = json_decode($bomGroup->list_song);
            }
            if ($bomGroup->list_priority != null) {
                $listVip = json_decode($bomGroup->list_priority);
            }

            //thêm 1 bài vào group
            if ($request->type == 0) {
                if (!in_array($request->boom_add_id, $listSong)) {
                    return array("status" => "error", "message" => "You have to add the song to the group first");
                } else {
                    if (in_array($request->boom_add_id, $listVip)) {
                        $temps = array_diff($listVip, [$request->boom_add_id]);
                        $listVip = [];
                        foreach ($temps as $temp) {
                            $listVip[] = $temp;
                        }
                    } else {
                        $isVip = 1;
                        $listVip[] = (int) $request->boom_add_id;
                    }
                    $bomGroup->list_priority = json_encode($listVip);
                    $bomGroup->save();
                }
            } else {
                //thêm nhiều bài vào 1 lúc
                if (count($request->chkBomAll) == 0) {
                    return array("status" => "error", "message" => "Choose some song please");
                }
                $tmpResult = [];
                $temps = array_merge($listVip, $request->chkBomAll);
                foreach ($temps as $temp) {
                    if (!in_array((int) $temp, $tmpResult)) {
                        $tmpResult[] = (int) $temp;
                    }
                }
                $bomGroup->list_priority = json_encode($tmpResult);
                $bomGroup->save();
            }
        }
        return array("status" => "success", "message" => "Success", "is_vip" => $isVip);
    }

    public function addNewGroup(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.addNewGroup|request=' . json_encode($request->all()));
        if (trim($request->group_name) == null) {
            return array("status" => "error", "message" => "Group name is empty");
        }

        $isEdit = 0;
        if (isset($request->bom_gr_id)) {
            $data = BomGroups::where("id", $request->bom_gr_id)->where("username", $user->user_name)->where("del_status", 0)->first();
            if (!$data) {
                return array("status" => "error", "message" => "Not exists");
            }
            $data->name = trim($request->group_name);
            $data->description = $request->group_des;
            $data->save();
            $isEdit = 1;
        } else {
            $check = BomGroups::where("name", trim($request->group_name))->where("username", $user->user_name)->where("del_status", 0)->first();
            if ($check) {
                return array("status" => "error", "message" => "Group Name is already exists");
            }
            $data = new BomGroups();
            $data->username = $user->user_name;
            $data->name = trim($request->group_name);
            $data->description = $request->group_des;
            $data->save();
        }
        return array("status" => "success", "message" => "Success", "data" => $data, "is_edit" => $isEdit);
    }

    public function updateGroup(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.updateGroup|request=' . json_encode($request->all()));
        if (trim($request->group_name) == null) {
            return array("status" => "error", "message" => "Group name is empty");
        }
        $check = BomGroups::where("name", trim($request->group_name))->where("username", $user->user_name)->where("del_status", 0)->first();
        if ($check) {
            return array("status" => "error", "message" => "Group Name is already exists");
        }
        $data = BomGroups::where("id", $request->id)->where("username", $user->user_name)->where("del_status", 0)->first();
        if ($data) {
            $data->name = trim($request->group_name);
            if (isset($request->group_des)) {
                $data->description = $request->group_des;
            }
            $data->save();
            return array("status" => "success", "message" => "Success", "data" => $data);
        }
        return array("status" => "error", "message" => "Group Name is already exists");
    }

    public function ldSave(Request $request) {
        Log::info("BomController.ldSave|request=" . json_encode($request->all()));

        return array("status" => "success", "message" => "Success");
    }

    public function ldBuy(Request $request) {
        Log::info("BomController.ldBuy|request=" . json_encode($request->all()));
        return array("status" => "success", "message" => "Success");
    }

    public function ldChannels() {
        return response()->json($this->loadMainChannelsDatas());
    }

    //lưu bài local
    public function storeLocalSong(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.storeLocalSong|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if ($request->genre == '-1') {
            return response()->json(array("status" => "error", "message" => "Please select the Genre"));
        }
        if (!isset($request->local_group)) {
            return response()->json(array("status" => "error", "message" => "Please select the Group"));
        }

        if (Utils::containString($request->source_link, "drive.google.com/file") || Utils::containString($request->source_link, "youtu")) {
            if ($request->songName == null) {
                return response()->json(array("status" => "error", "message" => "Song Name is invalid"));
            }
            if ($request->artist == null) {
                return response()->json(array("status" => "error", "message" => "Artist is invalid"));
            }
        }
        if (!isset($request->chk_overtone)) {
            if ($request->source_link == null) {
                return response()->json(array("status" => "error", "message" => "Source Link is invalid"));
            }
        }

        //thêm mới
        $videoIds = [];
        $sourceType = null;
        if ($request->noclaim_id == null) {
            if (isset($request->chk_overtone)) {
                if (!isset($request->overtone_playlist_id)) {
                    return response()->json(array("status" => "error", "message" => "You must select overtone playlist"));
                }
                $sourceType = "OVERTONE";
                $playlists = EpidemicSoundHelper::getOvertoneData();
                foreach ($playlists as $playlist) {
                    if ($playlist->id == $request->overtone_playlist_id) {
                        $tracks = !empty($playlist->tracks) ? $playlist->tracks : [];
                        foreach ($tracks as $track) {
                            $videoIds[] = (object) [
                                        "song_id" => $track->id,
                                        "source_id" => $track->public_download_url2,
                                        "song_name" => $track->name,
                                        "artist" => $track->artist
                            ];
                        }
                    }
                }
//                $playlists = $this->getOverToneSongUrl($request->overtone_playlist_id);
//                if (!empty($playlists->results)) {
//                    foreach ($playlists->results as $track) {
//                        $videoIds[] = (object) [
//                                    "song_id" => $track->id,
//                                    "source_id" => $track->public_download_url2,
//                                    "song_name" => $track->name,
//                                    "artist" => $track->artist
//                        ];
//                    }
//                }
            } else {
                if (Utils::containString($request->source_link, "youtu")) {
                    $videoInfo = YoutubeHelper::processLink($request->source_link);
                    if ($videoInfo['type'] != 3) {
                        return response()->json(array("status" => "error", "message" => "Youtube/Drive Link is invalid"));
                    }
                    $videoId = $videoInfo['data'];
                    $videoIds[] = (object) [
                                "source_id" => $videoId,
                                "song_name" => trim($request->songName),
                                "artist" => trim($request->artist)
                    ];
                    $sourceType = "YOUTUBE";
                } else if (Utils::containString($request->source_link, "drive")) {
                    //kiểm tra xem là file hay folder
                    $driveInfo = Utils::getDriveID2(trim($request->source_link));
                    Log::info(json_encode($driveInfo));
                    if ($driveInfo->type == "file") {
                        $videoIds[] = (object) [
                                    "source_id" => $driveInfo->drive_id,
                                    "song_name" => trim($request->songName),
                                    "artist" => trim($request->artist)
                        ];
                        Log::info("videoIds file " . json_encode($videoIds));
                    } elseif ($driveInfo->type == "folder") {
                        $folders = RequestHelper::getDriveFiles($driveInfo->drive_id);
                        Log::info("folder " . json_encode($folders));
                        $listMp3 = [];
                        if (isset($folders->original->files)) {
                            foreach ($folders->original->files as $file) {
//                                Log::info(json_encode($file));
                                if ($file->kind == "drive#file" && Utils::containString($file->mimeType, "audio")) {
                                    $listMp3[] = $file->id;
                                }
                            }
                        }
                        if (count($listMp3) == 0) {
                            return response()->json(array("status" => "error", "message" => "Drive folder is empty"));
                        }
                        $customSongNames = [];
                        $customsArtistName = [];
                        if ($request->list_song_name != null) {
                            $customSongNames = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_song_name)));
                        }
                        if ($request->list_artist != null) {
                            $customsArtistName = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_artist)));
                        }
//                        Log::info(json_encode($customSongNames));
//                        Log::info(json_encode($customsArtistName));
                        //kiểm tra xem danh sách tên nghệ sỹ, tên bài hát có đủ số lượng không
                        if (count($customSongNames) < count($listMp3)) {
                            return response()->json(array("status" => "error", "message" => "Number of Song Name must be > or equal to " . count($listMp3)));
                        }
                        //nếu số lượng aritst > 1 => có nhiều ca sỹ thì phải nhập đủ sộ lượng ca sý
                        //nếu = 1 thì chỉ lấy 1 ca sỹ đó cho tất cả bài hát
                        if (count($customsArtistName) < count($listMp3) && count($customsArtistName) > 1) {
                            return response()->json(array("status" => "error", "message" => "Number of Artist must be > or equal to " . count($listMp3)));
                        }
                        if (count($customsArtistName) == 0) {
                            return response()->json(array("status" => "error", "message" => "Artist list cannot be empty"));
                        }
                        foreach ($listMp3 as $idx => $mp3) {
                            if (count($customsArtistName) == 1) {
                                $artstsNameCustom = trim($customsArtistName[0]);
                            } else {
                                $artstsNameCustom = trim($customsArtistName[$idx]);
                            }
                            $videoIds[] = (object) [
                                        "source_id" => $mp3,
                                        "song_name" => trim($customSongNames[$idx]),
                                        "artist" => $artstsNameCustom
                            ];
                        }
                        Log::info("videoIds folder " . json_encode($videoIds));
                    }
                    $sourceType = "DRIVE";
                } else if (Utils::containString($request->source_link, "epidemicsound.com")) {
                    $datas = EpidemicSoundHelper::getPlaylistData(trim($request->source_link));
                    if (!empty($datas->detail)) {
                        $message = $datas->detail;
                        if (Utils::containString($datas->detail, "Authentication")) {
                            $message = "Hết phiên làm việc epidemicsound, hãy liên hệ Mr.Trường để xử lý";
                        }
                        return response()->json(array("status" => "error", "message" => $message));
                    }
                    $sourceType = "EPID";
                    foreach ($datas as $data) {
                        Log::info(json_encode($data));
                        $videoIds[] = (object) [
                                    "song_id" => !empty($data->id) ? $data->id : null,
                                    "source_id" => !empty($data->track->stems->full->lqMp3Url) ? $data->track->stems->full->lqMp3Url : "",
//                                  
                                    "song_name" => !empty($data->track->title) ? $data->track->title : "",
                                    "artist" => !empty($data->track->creatives->mainArtists[0]->name) ? $data->track->creatives->mainArtists[0]->name : ""
                        ];
                    }
                } else if (Utils::containString($request->source_link, "universalmusicforcreators.com")) {
                    $sourceType = "UNIVERSAL";
                    if (Utils::containString(trim($request->source_link), "https://www.universalmusicforcreators.com/library/playlists/")) {
                        $playlistId = str_replace("https://www.universalmusicforcreators.com/library/playlists/", "", trim($request->source_link));
                        $workIds = $this->getUniversalSongId($playlistId);
                        Log::info(json_encode($workIds));
                        if (!empty($workIds->statusCode) && $workIds->statusCode == '401') {
                            return response()->json(array("status" => "error", "message" => $workIds->message));
                        }
                        if (!empty($workIds->data)) {
                            $workIdStr = "";
                            $workIdArr = [];
                            $totalUni = count($workIds->data);
                            foreach ($workIds->data as $idx => $work) {
                                $workIdArr[] = $work->id;
                                if ($idx == $totalUni - 1) {
                                    $workIdStr .= "$work->id";
                                } else {
                                    $workIdStr .= "$work->id+";
                                }
                            }
                            Log::info($workIdStr);
                            $tmp = $this->getUniversalSongUrl($workIdStr);
                            if (!empty($tmp->docs)) {
                                $universals = $tmp->docs;
                                foreach ($universals as $universal) {
                                    if (in_array($universal->bestWorkAudioId, $workIdArr)) {
                                        Log::info("bestWorkAudioId $universal->bestWorkAudioId");
                                        if (!empty($universal->workAudio)) {
                                            foreach ($universal->workAudio as $workAudio) {
                                                if (!empty($workAudio->workAudioId)) {
                                                    if (in_array($workAudio->workAudioId, $workIdArr)) {
                                                        Log::info("workAudioId " . $workAudio->workAudioId);
                                                        if (!empty($workAudio->audioFilePathMp3)) {
                                                            $videoIds[] = (object) [
                                                                        "source_id" => !empty($workAudio->audioFilePathMp3) ? $workAudio->audioFilePathMp3 : "",
                                                                        "song_name" => !empty($universal->trackTitle) ? $universal->trackTitle : "",
                                                                        "artist" => !empty($universal->composers[0]->name) ? $universal->composers[0]->name : ""
                                                            ];
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            Log::info("empty getUniversalSongId $playlistId");
                        }
                    } else {
                        return response()->json(array("status" => "error", "message" => "Universal link is invalid"));
                    }
                } else if (Utils::containString($request->source_link, "udio.com")) {
                    $sourceType = "UDIO";
                    $listSongId = [];
                    $playlistId = str_replace("https://www.udio.com/playlists/", "", trim($request->source_link));
                    $workIds = $this->getUdioPlaylistId($playlistId);

                    if (!empty($workIds->playlists[0]->song_list)) {
                        //lấy song_list nếu có dữ liệu
                        $listSongId = $workIds->playlists[0]->song_list;
                    } else {
                        if (!empty($workIds->playlists[0]->id)) {
                            $dataSong = $this->getUdioSongId($workIds->playlists[0]->id);
                            if (!empty($dataSong->data)) {
                                foreach ($dataSong->data as $dtSong) {
                                    $listSongId[] = $dtSong->id;
                                }
                            }
                        }
                    }
                    if (empty($listSongId)) {
                        return response()->json(array("status" => "error", "message" => "Not found song id"));
                    }
                    $customSongNames = [];
                    $customsArtistName = [];
                    if ($request->list_song_name != null) {
                        $customSongNames = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_song_name)));
                    }
                    if ($request->list_artist != null) {

                        $customsArtistName = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_artist)));
                    }

                    if (isset($request->chk_keep_name)) {
                        $songNameCount = count($customSongNames);
                        if (count($customsArtistName) == 0 && $songNameCount < count($listSongId)) {
                            if (count($customsArtistName) == 0) {
                                return response()->json(array("status" => "error", "message" => "List artist can not be empty"));
                            }
                            if ($songNameCount < count($listSongId)) {
                                return response()->json(array("status" => "error", "message" => "Number of song name must be > or equal to " . count($listSongId)));
                            }
                        }
                    }


                    $listSong = $this->getUdioSongUrl(implode(",", $listSongId));
                    if (empty($listSong->songs)) {
                        return response()->json(array("status" => "error", "message" => "List song is empty"));
                    }
                    foreach ($listSong->songs as $idx => $song) {
                        $random_artist = !empty($song->artist) ? $song->artist : "";
                        $songName = !empty($song->title) ? $song->title : "";
                        if (isset($request->chk_keep_name)) {
                            if (count($customsArtistName) > 0) {
                                $random_key = array_rand($customsArtistName);
                                $random_artist = $customsArtistName[$random_key];
                            }
                            if (count($customSongNames) > 0) {
                                $songName = $customSongNames[$idx];
                            }
                        }
                        $videoIds[] = (object) [
                                    "song_id" => $song->id,
                                    "source_id" => !empty($song->song_path) ? $song->song_path : "",
                                    "song_name" => $songName,
                                    "artist" => $random_artist,
                                    "lyrics" => !empty($song->lyrics) ? $song->lyrics : "",
                        ];
                    }
                } else if (Utils::containString($request->source_link, "suno.com")) {
                    $sourceType = "SUNO";
                    $playlistId = str_replace("https://suno.com/playlist/", "", trim($request->source_link));
                    $workIds = $this->fetchAllSunoSongs($playlistId);
                    $customsArtistName = [];

                    if ($request->list_artist != null) {
                        $customsArtistName = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_artist)));
                    }

                    if (isset($request->chk_keep_name)) {
                        if (count($customsArtistName) == 0) {
                            return response()->json(array("status" => "error", "message" => "List artist can not be empty"));
                        }
                    }

                    foreach ($workIds as $work) {
                        $random_artist = $work->clip->display_name;
                        $songName = $work->clip->title;
                        if (isset($work->clip->audio_url)) {
                            if (isset($request->chk_keep_name)) {
                                if (count($customsArtistName) > 0) {
                                    $random_key = array_rand($customsArtistName);
                                    $random_artist = $customsArtistName[$random_key];
                                }
                            }
                            $videoIds[] = (object) [
                                        "song_id" => $work->clip->id,
                                        "source_id" => $work->clip->audio_url,
                                        "song_name" => $songName,
                                        "artist" => $random_artist
                            ];
                        }
                    }
                } else {
                    //cho phép thêm link direct vào
                    $videoIds[] = (object) [
                                "song_id" => trim($request->source_link),
                                "source_id" => trim($request->source_link),
                                "song_name" => trim($request->songName),
                                "artist" => trim($request->artist)
                    ];
                    $sourceType = "DIRECT";
//                    return response()->json(array("status" => "error", "message" => "Enter youtube or drive link or epidemicsound or Universal link"));
                }
            }
            $success = 0;
            $fail = 0;
//            Log::info(json_encode($videoIds));
            $listAllSong = [];
            foreach ($videoIds as $data) {
                $data->status = 0;
                if ($sourceType == "OVERTONE" || $sourceType == "UDIO") {
                    $check = Bom::where("song_id", $data->song_id)->where("status", 1)->first();
                } else {
                    $check = Bom::where("source_id", $data->source_id)->where("status", 1)->first();
                }
                if (!$check) {
                    $success++;
                    $noclaim = new Bom();
                    $noclaim->username = $user->user_name;
                    $noclaim->genre = $request->genre;
                    $noclaim->source_type = $sourceType;
                    $noclaim->song_name = $data->song_name;
                    if (!empty($data->song_id)) {
                        $noclaim->song_id = $data->song_id;
                    }
                    $noclaim->source_id = $data->source_id;
                    $noclaim->lyric = 0;
                    $noclaim->sync = 0;
                    $noclaim->artist = $data->artist;
                    $noclaim->created = $curr;
                    $noclaim->created_timestamp = time();
                    $noclaim->count = 0;
                    $noclaim->status = 1;
                    $noclaim->log = "$curr $user->user_name added to the system";
                    if (!empty($data->lyrics)) {
                        $noclaim->lyric_text = $data->lyrics;
                    }
                    $noclaim->save();
                    $data->status = 1;
                    $data->user = $user->user_name;
                    $data->genre = $request->genre;
                    $data->id = $noclaim->id;
                } else {
                    $fail++;
                    $check->log = $check->log . PHP_EOL . "$curr $user->user_name dupticate";
                    $check->save();
                    $data->status = 2;
                    $data->user = $check->username;
                    $data->genre = $check->genre;
                    $data->id = $check->id;
                }
                $listAllSong[] = $data->id;
            }
            Log::info("all song: " . count($listAllSong));
            //thêm vào group 
            $bomGroups = BomGroups::whereIn("id", $request->local_group)->get();
            foreach ($bomGroups as $bGroup) {
                $listSong = [];
                if ($bGroup->list_song != null) {
                    $listSong = json_decode($bGroup->list_song);
                }
                $mergedArray = array_merge($listSong, $listAllSong);
                $uniqueArray = array_unique($mergedArray);
                $uniqueArray = array_values($uniqueArray);
                Log::info("$bGroup->name " . count($listSong) . " " . count($mergedArray) . " " . count($uniqueArray));
                $bGroup->list_song = json_encode($uniqueArray);
                $bGroup->save();
            }
            return response()->json(array("status" => "success", "message" => "success $success, fail $fail", "playlist" => $videoIds));
        } else {
            //edit
            $noclaim = Bom::find($request->noclaim_id);
            if (!$noclaim) {
                return response()->json(array("status" => "error", "message" => "Not found Bom Id $request->noclaim_id"));
            }
            $noclaim->genre = $request->genre;
            $noclaim->song_name = $request->songName;
            $noclaim->artist = $request->artist;
            $noclaim->social = $request->social;
            $noclaim->log = $noclaim->log . PHP_EOL . "$curr $user->user_name update to the system";
            $noclaim->save();
        }
        return response()->json(array("status" => "success", "message" => "Success"));
    }

    //tiến trình download nhạc
    public function noclaimSync() {
        $processName = "noclaim-sync";
        if (!ProcessUtils::isfreeProcess($processName)) {
            error_log("$processName is locked");
            return 1;
        }
        ProcessUtils::lockProcess($processName);
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $datas = Bom::where("status", 1)->where("sync", 0)->whereNotNull("source_id")->where("retry", "<=", 5)->get();
        error_log("noclaimSync total=" . count($datas));
        $path = "/home/automusic.win/public_html/public/noclaim_save/music/download";
        foreach ($datas as $data) {
            $saved_converted = "$path/$data->id-converted.mp3";
            $direct = "https://automusic.win/noclaim_save/music/download/$data->id-converted.mp3";
//            $direct = "https://automusic.win/noclaim_save/music/download/$data->source_id.mp3";
            if ($data->source_type == "EPID" || $data->source_type == "UNIVERSAL" || $data->source_type == "OVERTONE" || $data->source_type == "UDIO" || $data->source_type == "ARTLIST_IO" || $data->source_type == "SUNO" || $data->source_type == "DIRECT") {
                $saved = "$path/$data->id.mp3";
                $cmd = "sudo wget --no-check-certificate \"$data->source_id\" -O $saved";
            } else if ($data->source_type == "DRIVE") {
                $saved = "$path/$data->source_id.mp3";
                $cmd = "sudo gbak downloadsv --sv studio --idx https://drive.google.com/file/d/$data->source_id/view?usp=sharing --path $saved";
            } else {
                $saved = "$path/$data->source_id.m4a";
//                $cmd = "sudo youtube-dl --proxy dunndealpr:0ddf2c-02b7b2-7c80d6-6958a3-468cdb@usa.rotating.proxyrack.net:333 -x --audio-format mp3 -o $saved https://www.youtube.com/watch?v=$data->source_id";
                shell_exec("/home/tools/env/bin/python /home/tools/get-cookies.py");
                $cmd = "sudo yt-dlp --cookies cookies.txt -f m4a -o $saved \"https://www.youtube.com/watch?v=$data->source_id\"";
            }

            //check xem đã tồn tại file chưa
            $check = glob($saved);
            if (count($check) <= 0) {
                error_log("noclaimSync $data->id $cmd");
                shell_exec($cmd);
            }

//            $direct = "https://automusic.win/noclaim_save/music/download/$data->source_id-converted.mp3";
            $check = glob($saved);
            if (count($check) > 0) {
                error_log("noclaimSync $data->id downloaded");
                //kiểm tra xem file đã có download về chưa,nếu tồn tại rồi mà là epidemicsound thì đẩy lên timestime lyric
                if (count(glob($saved_converted)) <= 0) {
                    $cmdConvert = 'sudo ffmpeg -i "' . $saved . '" -b:a 128000 -ar 44100 -c:a mp3 "' . $saved_converted . '"';
                    error_log("noclaimSync $data->id $cmdConvert");
                    shell_exec($cmdConvert);
                }
                $durationMs = 0;
                //lấy duration
                $cmdDurration = "ffprobe -v error -show_entries stream=codec_name,codec_type,sample_rate,bit_rate,duration -of json $saved";
                $resultDuration = shell_exec($cmdDurration);
                if ($resultDuration != null) {
                    $tmp = json_decode($resultDuration);
                    if (!empty($tmp->streams[0]->duration)) {
                        $durationMs = ceil($tmp->streams[0]->duration * 1000);
                    }
                }
                //upload lên cdn
                if (count(glob($saved_converted)) > 0) {
                    //xóa file đã download
                    error_log("noclaimSync $data->id delete $saved");
                    unlink($saved);
                    $cdnCmd = "gbak upload-r2 --input $saved_converted --server automusic-audio";
                    error_log("noclaimSync $data->id upload cdn cmd $cdnCmd");
                    $ul = shell_exec($cdnCmd);
                    error_log("noclaimSync $data->id upload cdn result $ul");
                    if ($ul != null && $ul != "" && $ul != "None") {
                        $direct = trim($ul);
                        unlink($saved_converted);

                        $data->direct_link = $direct;
                        $data->sync = 1;
                        //2023/03/30 set lyric=1 để thỏa mãn điều kiện truy vấn, thực tế bài này ko có lyric
//                //2024/10/25 nếu bài nào có lyric_text thì set lyric = 0 để người dùng tự cạch cạch
//                if ($data->lyric_text != null && $data->lyric_text != "") {
//                    $data->lyric = 0;
//                } else {
//                    $data->lyric = 1;
//                }
                        $data->lyric = 1;
                        $log = $data->log;
                        $log .= PHP_EOL . "$curr synced";
                        $data->log = $log;
                        $data->duration_ms = $durationMs;
                        $data->save();
                        //lưu dữ liệu lên timestamp lyric nếu bài đó ko có lyric_text
                        if ($data->lyric == 1) {
                            $input = [
                                "title" => $data->song_name,
                                "artist" => $data->artist,
                                "deezer_artist_id" => $data->id,
                                "lyric" => "",
                                "url_128" => $direct,
                                "lyric_sync" => '["lyric"]',
                                "track_id" => null,
                                "lyric_langs" => [],
                                "user_name" => $data->username,
                                "notes" => null,
                            ];
//                Log::info("noclaim-sync input ".json_encode($input));
                            $result = (object) [];
                            $tmp = RequestHelper::postRequest("http://cdn.soundhex.com/api/v1/timestamp/", $input);
//                Log::info("noclaim-sync " . $tmp);
                            if ($tmp != null && $tmp != "") {
                                $result = json_decode($tmp);
                            }

                            if (!empty($result->link)) {
                                error_log("noclaimSync $data->id timestamp $result->link");
                                $localId = str_replace("http://automusic.win/lyricconfig?type=local&data=", "", $result->link);
                                $data->local_id = $localId;
                                $data->save();
                            }
                        }
                    } else {
                        $data->log .= PHP_EOL . "$curr upload cdn fail";
                        $data->retry = $data->retry + 1;
                    }
                } else {
                    $data->log .= PHP_EOL . "$curr not found $saved_converted";
                    $data->retry = $data->retry + 1;
                }
            } else {
                $data->log .= PHP_EOL . "$curr download fail";
                $data->retry = $data->retry + 1;
            }
            $data->save();
            error_log("noclaimSync $data->id finish");
        }
        ProcessUtils::unLockProcess($processName);
    }

    public function convertWav() {
        $boms = Bom::where("status", 1)->where("sync", 1)->whereNotNull("direct_link")->whereNull("direct_wav")->where("is_releasable", 1)->get();
        $total = count($boms);
        foreach ($boms as $index => $bom) {
            $cmdConvert = "sudo ffmpeg -i \"$bom->direct_link\" -acodec pcm_s24le -ar 44100 $bom->id.wav";
            $saved_converted = "$bom->id.wav";

            error_log("convertWav $index/$total $cmdConvert");
            shell_exec($cmdConvert);
            if (count(glob($saved_converted)) > 0) {
                $cdnCmd = "gbak upload-r2 --input $saved_converted --server automusic-audio";
                error_log("convertWav $index/$total $bom->id upload cdn cmd $cdnCmd");
                $ul = shell_exec($cdnCmd);
                error_log("convertWav $index/$total $bom->id upload cdn result $ul");
                if ($ul != null && $ul != "") {
                    $direct = trim($ul);
                    unlink($saved_converted);
                    $bom->direct_wav = $direct;
                    $bom->save();
                }
                error_log("noclaimSync $index/$total $bom->id finish");
            }
        }
    }

    public function getUniversalSongId($playlist) {
        $curl = curl_init();
        //lấy auth_token để thêm vào Authorization
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://playlists.api.universalproductionmusic.com/sites/42/playlists/$playlist/edits?pageIndex=1&pageSize=200",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json, text/plain, */*',
                'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
                'Authorization: Bearer UxrbDA2HahfQQViRmyPjiC6Yk0092x2PpThaqtfxmjmDHu_k3uySSybKZPHvnfVbVaYqneOqxOsSdkktUUIWlWxCuAj5N3NvQ0C76AB2L4BBrrHYwqKhV1uuHwpv56_rXIanu1BblLPzSlaGP2mhVBUOUSp4FvA1-pDOxjXsUXtcam5HngiI2oNS3ea8F_mQPIwG_d8NNxkX_rpnx-a1tq9yoay1_6p6Cadvc2-GiErqN9JXxb_BLLH3eVsz0soQLhh4ArqZuZoZe_oxqFYClFGboppdij5Eiw7cnFEGteZWcd_1W1ZfgnjwSq7ALZ00bU2vf1j12t9LL83N9UwPuqoOn4_XM3Fv1bh-niMc1Y4imm3yVtw9b6DJK9dtCyrsZivlAfifyOJiu1rAmhajnvaf3-HELrAdc23vVs81P3dlf-cxNCYpljzgdALIHEDkeuuYWw',
                'Connection: keep-alive',
                'Origin: https://www.universalmusicforcreators.com',
                'Referer: https://www.universalmusicforcreators.com/',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: cross-site',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36',
                'sec-ch-ua: "Not)A;Brand";v="99", "Google Chrome";v="127", "Chromium";v="127"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'umg-application-name: UPM Websites',
                'umg-company-id: 6082',
                'umg-location-code: GBR',
                'umg-user-id: 20322',
                'Cookie: incap_ses_299_2877800=V/9pfUXkNVM+ZrplBkMmBDfLq2YAAAAA0cVq/wndBdu2Dt2ghm+55Q==; incap_ses_299_3077688=3SQOMYdniw797bxlBkMmBB3Pq2YAAAAAlx1sNojMkiRnIag37PckHQ==; nlbi_2877800=P2sbBQE2NBt8JCjSiIQthgAAAABexOlV7G7e5V5YSJfZMBIn; nlbi_3077688=xHgqb6uNP2vU0b1bwR2D3QAAAAClGriSu7XAWgf4W0T3Afs1; visid_incap_2877800=c/JnJcjaRS2Fcbz9aCTQ+Nhkq2YAAAAAQUIPAAAAAADyUCP633ggeqjvrw7l2pYV; visid_incap_3077688=VzO9bb4oSbCvtPY/OpozBsljq2YAAAAAQUIPAAAAAADAMuebpBQUqlklwpNq95C3'
            ),
        ));
        $songIds = null;
        $response = curl_exec($curl);
        curl_close($curl);
        if ($response != null) {
            $songIds = json_decode($response);
        }
        Log::info("getUniversalSongId $response");
        return $songIds;
    }

    public function getUniversalSongUrl($songIds) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://search.api.universalproductionmusic.com/v2/uppm_work/42/1?q=editIds%3A%28$songIds%29&fq=-locationRestrictions%3A%28GBR%29&sort=workId+asc&rows=2000&start=0",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json, text/plain, */*',
                'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
                'Connection: keep-alive',
                'Origin: https://www.universalmusicforcreators.com',
                'Referer: https://www.universalmusicforcreators.com/',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: cross-site',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36',
                'sec-ch-ua: "Not)A;Brand";v="99", "Google Chrome";v="127", "Chromium";v="127"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'Cookie: incap_ses_299_2877800=V/9pfUXkNVM+ZrplBkMmBDfLq2YAAAAA0cVq/wndBdu2Dt2ghm+55Q==; incap_ses_299_3077688=89M3D3A98TiaH7plBkMmBNPKq2YAAAAA6PvlygX82i7PYsQG9qT1pg==; nlbi_2877800=P2sbBQE2NBt8JCjSiIQthgAAAABexOlV7G7e5V5YSJfZMBIn; nlbi_3077688=xHgqb6uNP2vU0b1bwR2D3QAAAAClGriSu7XAWgf4W0T3Afs1; visid_incap_2877800=c/JnJcjaRS2Fcbz9aCTQ+Nhkq2YAAAAAQUIPAAAAAADyUCP633ggeqjvrw7l2pYV; visid_incap_3077688=VzO9bb4oSbCvtPY/OpozBsljq2YAAAAAQUIPAAAAAADAMuebpBQUqlklwpNq95C3'
            ),
        ));
        $datas = null;
        $response = curl_exec($curl);
        Log::info("getUniversalSongUrl: $response");
        if ($response != null) {
            $datas = json_decode($response);
        }

        curl_close($curl);
        return $datas;
    }

    //2024/09/13 hàm thêm link youtube từ studio
    public function addYoutubeFromStudio(Request $request) {
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        if ($request->spotify_id == null || $request->spotify_id == "") {
            return response()->json(["status" => "error", "message" => "Spotify is invalid"]);
        }
        if ($request->youtube_id == null || $request->youtube_id == "") {
            return response()->json(["status" => "error", "message" => "Youtube is invalid"]);
        }
        $check = Bom::where("spotify_id", $request->spotify_id)->where("source_id", $request->youtube_id)->first(["id", "source_id", "direct_link", "sync"]);
        if (!$check) {
            $noclaim = new Bom();
            $noclaim->username = $request->username;
            $noclaim->spotify_id = trim($request->spotify_id);
//                    $noclaim->genre = $request->genre;
            $noclaim->source_type = "YOUTUBE";
            $noclaim->song_name = trim($request->song_name);
            $noclaim->artist = trim($request->artist);
            $noclaim->source_id = $request->youtube_id;
            $noclaim->lyric = 0;
            $noclaim->sync = 0;
            $noclaim->created = $curr;
            $noclaim->created_timestamp = time();
            $noclaim->count = 0;
            $noclaim->status = 1;
            $noclaim->duration_ms = $request->duration_ms;
            $noclaim->genre = $request->genre;
            $noclaim->log = "$curr $request->user_name added to the system from studio";
            $noclaim->save();
            $check = (object) [
                        "id" => $noclaim->id,
                        "username" => $noclaim->username,
                        "source_id" => $noclaim->source_id,
                        "direct_link" => "https://automusic.win/noclaim_save/music/download/$noclaim->source_id-converted.mp3",
                        "sync" => $noclaim->sync
            ];
            return response()->json(["status" => "success", "message" => "Success", "data" => $check]);
        } else {
            return response()->json(["status" => "error", "message" => "Youtube id is exists"]);
        }
    }

    public function getYoutubeFromSpotify(Request $request) {
        $datas = Bom::where("spotify_id", $request->spotify_id)->where("source_type", "YOUTUBE")->get(["id", "username", "source_id", "direct_link", "sync"]);
        return response()->json(["status" => "success", "data" => $datas]);
    }

    //2024/10/23 import nhạc AI
    public function getUdioPlaylistId($playlistId) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.udio.com/api/playlists?id=$playlistId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json, text/plain, */*',
                'accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
                'cookie: _ga=GA1.1.1368723580.1729657921; _tt_enable_cookie=1; _ttp=f-g2oX2o6awIl3wqOkQD25omdCM; _gcl_au=1.1.1700262677.1729657921; _rdt_uuid=1729657921260.5d3242b6-ca47-4d71-97b9-0ccb8c677656; _ga_RF4WWQM7BF=GS1.1.1729670578.2.1.1729671122.58.0.191893517',
                'priority: u=1, i',
                'referer: https://www.udio.com/playlists/9Z5WhfqwTdSfXj6SRdTdE3',
                'sec-ch-ua: "Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
            ),
        ));
        $playlist = null;
        $response = curl_exec($curl);

        curl_close($curl);
        if ($response != null) {
            $playlist = json_decode($response);
        }
        Log::info("getUdioPlaylistId $response");
        return $playlist;
    }

    public function getUdioSongId($playlist) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.udio.com/api/songs/search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"searchQuery":{"sort":"playlist","playlistId":"' . $playlist . '"},"pageSize":100,"pageParam":0,"readOnly":true}',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json, text/plain, */*',
                'accept-language: en-US,en;q=0.9,vi-VN;q=0.8,vi;q=0.7',
                'baggage: sentry-environment=production,sentry-release=49452d2492c946188c8b548982ec7d2eaa597cc4,sentry-public_key=1dbee0ad22c14f97ee922e8b6d478b55,sentry-trace_id=489330d6477c4b75bc929e1fb1b79321,sentry-sampled=false,sentry-sample_rand=0.8602976596285834,sentry-sample_rate=0.01',
                'content-type: application/json',
                'origin: https://www.udio.com',
                'priority: u=1, i',
                'referer: https://www.udio.com/playlists/sp4VPcna5ejmXTP15P1hGd',
                'sec-ch-ua: "Chromium";v="134", "Not:A-Brand";v="24", "Google Chrome";v="134"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'sentry-trace: 489330d6477c4b75bc929e1fb1b79321-a74892473dc70edd-0',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36',
                'Cookie: x-anon-id=722916e0-da82-4153-a06a-083090010a60; _ga=GA1.1.1052433240.1744184289; _gcl_au=1.1.132282905.1744184289; _fbp=fb.1.1744184289761.797004890570786492; __stripe_mid=7ed55e5f-3320-4df0-9562-8f82c62668649cb855; __stripe_sid=5e0bdd0c-0107-4892-a279-8d212f94caf6d2871e; _ga_RF4WWQM7BF=GS1.1.1744184288.1.1.1744184564.58.0.1145335526'
            ),
        ));
        $songIds = null;
        $response = curl_exec($curl);

        curl_close($curl);
        if ($response != null) {
            $songIds = json_decode($response);
        }
        Log::info("getUdioSongUrl $response");
        return $songIds;
    }

    public function getUdioSongUrl($songIds) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.udio.com/api/songs?songIds=$songIds",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json, text/plain, */*',
                'accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
                'cookie: _ga=GA1.1.1368723580.1729657921; _tt_enable_cookie=1; _ttp=f-g2oX2o6awIl3wqOkQD25omdCM; _gcl_au=1.1.1700262677.1729657921; _rdt_uuid=1729657921260.5d3242b6-ca47-4d71-97b9-0ccb8c677656; _ga_RF4WWQM7BF=GS1.1.1729670578.2.1.1729671122.58.0.191893517',
                'priority: u=1, i',
                'sec-ch-ua: "Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
            ),
        ));
        $datas = null;
        $response = curl_exec($curl);
        Log::info("getUdioSongUrl: $response");
        if ($response != null) {
            $datas = json_decode($response);
        }

        curl_close($curl);
        return $datas;
    }

    //2025/03/07 lấy nhạc từ suno.com
    public function getSunoSongId($url) {
        $curl = curl_init();
        error_log("getSunoSongId $url");
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: en-US,en;q=0.9,vi-VN;q=0.8,vi;q=0.7',
                'affiliate-id: undefined',
                'authorization: Bearer null',
                'browser-token: {"token":"eyJ0aW1lc3RhbXAiOjE3NDEyODQ2NDAyMDZ9"}',
                'device-id: b2cf0f63-fea3-4684-bc42-9662bb094bde',
                'origin: https://suno.com',
                'priority: u=1, i',
                'referer: https://suno.com/',
                'sec-ch-ua: "Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-site',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36'
            ),
        ));
        $songIds = null;
        $response = curl_exec($curl);

        curl_close($curl);
        if ($response != null) {
            $songIds = json_decode($response);
        }
        return $songIds;
    }

    public function fetchAllSunoSongs($playlistId) {
        $allSongs = [];
        $page = 1;

        do {
            $url = "https://studio-api.prod.suno.com/api/playlist/$playlistId?page=" . $page;
            $responses = $this->getSunoSongId($url);
            if ($responses != null) {
                if (isset($responses->playlist_clips) && count($responses->playlist_clips) > 0) {
                    $allSongs = array_merge($allSongs, $responses->playlist_clips);
                } else {
                    error_log("break");
                    break;
                }
            } else {
                error_log("break");
                break;
            }
            $total = $responses->num_total_results;
            $page++;
        } while (count($allSongs) < $total);

        return $allSongs;
    }

    //sử dụng my-playlist
    public function getOvertonePlaylist() {
        $user = Auth::user();
        $datas = EpidemicSoundHelper::getOvertoneData();
        $option = "";
        if ($datas != null) {
            foreach ($datas as $data) {
                $tracks = !empty($data->tracks) ? count($data->tracks) : 0;
                if (Utils::containString(strtolower($data->name), $user->user_name)) {
                    $option .= "<option value='$data->id'  data-content='$data->name <span class=\"font-12 text-muted font-italic\">$tracks tracks</span>'></option>";
                }
            }
        }
        return $option;
    }

    //sử dụng danh sách playlist có sẵn
    public function getOverTonePlaylistId() {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://overtone-studios.disco.ac/api/client_library/1939628463/search/?type=playlist&playlist_type=playlist&return_tracks_artworks=1&count=100&sort=%5B%22is_featured%22%5D',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'accept-language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5',
                'content-type: application/json',
                'cookie: csrftoken=xmRr5As4poKnlQBH0sMndDEaqVDxvNiJ; disco_businesses="[]"; sessionid=xj4riolvtdu73t26argypaxhgdk1ykbd; _gid=GA1.2.804087499.1730177726; _ga_DF9C2DP1WJ=GS1.1.1730184261.2.1.1730184261.0.0.0; _ga=GA1.2.496700773.1730177725; mp_d041f61e9c4961af3cff17d999ae1966_mixpanel=%7B%22distinct_id%22%3A%20%2239856-2499543%22%2C%22%24device_id%22%3A%20%221926b13e3141ac1b-0af37013df14c2-26001051-1fa400-1926b13e3141ac1b%22%2C%22%24user_id%22%3A%20%2239856-2499543%22%2C%22%24initial_referrer%22%3A%20%22%24direct%22%2C%22%24initial_referring_domain%22%3A%20%22%24direct%22%7D; disco_businesses="[]"; csrftoken=OKIVOsvMDZ8TCgokZ9SSfRFp7lb21XBc; sessionid=xj4riolvtdu73t26argypaxhgdk1ykbd',
                'priority: u=1, i',
                'referer: https://overtone-studios.disco.ac/cat/1939628463/playlists',
                'sec-ch-ua: "Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
            ),
        ));
        $response = curl_exec($curl);

        $option = "";
        curl_close($curl);
        if ($response != null) {
            $tmp = json_decode($response);
            if ($tmp != null) {
                if (!empty($tmp->results->playlists)) {
                    $datas = $tmp->results->playlists;
                    foreach ($datas as $data) {
                        $option .= "<option value='$data->id'  data-content='$data->name <span class=\"font-12 text-muted font-italic\">$data->track_count tracks</span>'></option>";
                    }
                }
            }
            return $option;
        }
    }

    public function getOverToneSongUrl($playlistId) {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://overtone-studios.disco.ac/api/client_library/1939628463/playlist/$playlistId/track/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'accept-language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5',
                'content-type: application/json',
                'cookie: csrftoken=xmRr5As4poKnlQBH0sMndDEaqVDxvNiJ; disco_businesses="[]"; sessionid=xj4riolvtdu73t26argypaxhgdk1ykbd; _gid=GA1.2.804087499.1730177726; _ga=GA1.2.496700773.1730177725; _ga_DF9C2DP1WJ=GS1.1.1730184261.2.1.1730185248.0.0.0; mp_d041f61e9c4961af3cff17d999ae1966_mixpanel=%7B%22distinct_id%22%3A%20%2239856-2499543%22%2C%22%24device_id%22%3A%20%221926b13e3141ac1b-0af37013df14c2-26001051-1fa400-1926b13e3141ac1b%22%2C%22%24user_id%22%3A%20%2239856-2499543%22%2C%22%24initial_referrer%22%3A%20%22%24direct%22%2C%22%24initial_referring_domain%22%3A%20%22%24direct%22%7D; disco_businesses="[]"; csrftoken=OKIVOsvMDZ8TCgokZ9SSfRFp7lb21XBc; sessionid=xj4riolvtdu73t26argypaxhgdk1ykbd',
                'priority: u=1, i',
                'referer: https://overtone-studios.disco.ac/cat/1939628463/playlists/19241031',
                'sec-ch-ua: "Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
            ),
        ));
        $result = null;
        $response = curl_exec($curl);

        curl_close($curl);

        if ($response != null) {
            $result = json_decode($response);
        }
        return $result;
    }

    public function getDeezerFromSpotify(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.getDeezerFromSpotify|request=' . json_encode($request->all()));
        $result = [];
        $error = [];
        $total = 0;
        if ($request->sp_id != null) {
            $playlistId = Utils::getSpotifyIdFromUrl($request->sp_id);
            if ($playlistId == null) {
                return response()->json(array("status" => "error", "message" => "Spotify Id is invalid"));
            }
            $rp = RequestHelper::callAPI2("GET", "http://source.automusic.win/spotify/deezer-checking/$playlistId", []);
//            Log::info($rp);
            if ($rp != null) {
                $datas = $rp;
                foreach ($datas as $data) {
                    $total++;
                    if (!empty($data->track->external_ids->isrc)) {
                        Log::info("https://api.deezer.com/track/isrc:" . $data->track->external_ids->isrc);
                        $song = RequestHelper::callAPI2("GET", "https://api.deezer.com/track/isrc:" . $data->track->external_ids->isrc, []);
                        if ($song != null && !empty($song->id)) {
                            $result[] = $song->id;
                        } else {
                            $error[] = (object) [
                                        "artist" => $data->track->artists[0]->name,
                                        "song_name" => $data->track->name,
                                        "id" => $data->track->id,
                            ];
                        }
                    } else {
                        $error[] = (object) [
                                    "artist" => $data->track->artists[0]->name,
                                    "song_name" => $data->track->name,
                                    "id" => $data->track->id,
                        ];
                    }
                }
            }
        }
        return response()->json(["result" => $result, "total" => $total, "error" => $error]);
    }

    //function đưa ra danh sách các ca sỹ claim của mình, để ko distince khi make music
    public function notDistinctArtist() {
        $artists = [];
        $datas = DB::select("SELECT distinct `artist`
                    FROM `campaign_statistics`
                    WHERE `type` = '2' AND `status` IN (1,4)
                    and artist is not null");
        $datas2 = DB::select("SELECT distinct `artist`
                            FROM `bom`
                            WHERE `source_type` IN ('OVERTONE','EPID') AND `status` = '1' and artist <> ''");
        foreach ($datas as $data) {
            $artists[] = $data;
        }
        foreach ($datas2 as $data) {
            $artists[] = $data;
        }
        return response()->json($artists);
    }

    //tải lại bài deezer bị lỗi download
    public function reDonwnloadBomDeezer() {
        $datas = Bom::whereNotNull("deezer_id")->where("status", 1)->whereRaw("log NOT LIKE '%resync_all%'")->orderBy("id", "desc")->get();
        $total = count($datas);
        foreach ($datas as $index => $data) {
            $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $deezerId = $data->deezer_id;
            $isLyric = 0;
            $isSync = 0;
            $isrc = null;
            $url128 = "";
            $songTitle = null;
            $songArtist = null;

            $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$deezerId");
            if ($trackRes != null && $trackRes != "") {
                $track = json_decode($trackRes);
                if ($track->count > 0) {
                    if (!empty($track->results[0])) {
                        $isrc = $track->results[0]->isrc;
                        $songTitle = $track->results[0]->title;
                        $songArtist = $track->results[0]->artist;
                        if ($track->results[0]->lyric_sync != "" && $track->results[0]->lyric_sync != "null" && $track->results[0]->lyric_sync != null) {
                            $isLyric = 1;
                        }
                        if ($track->results[0]->url_128 != "") {
                            $isSync = 1;
                            $url128 = $track->results[0]->url_128;
                        }
                    }
                }
            }
            error_log("reDonwnloadBomDeezer $deezerId  isSync=$isSync");
            //check bài hát dã đươc download về hệ thống chưa
            if ($isSync == 0) {
                RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$deezerId");
                $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$deezerId");
                if ($trackRes != null && $trackRes != "") {
                    $track = json_decode($trackRes);
                    if ($track->count > 0) {
                        if (!empty($track->results[0])) {
                            $isrc = $track->results[0]->isrc;
                            $songTitle = $track->results[0]->title;
                            $songArtist = $track->results[0]->artist;
                            if ($track->results[0]->lyric_sync != "" && $track->results[0]->lyric_sync != "null" && $track->results[0]->lyric_sync != null) {
                                $isLyric = 1;
                            }
                            if ($track->results[0]->url_128 != "") {
                                $isSync = 1;
                                $url128 = $track->results[0]->url_128;
                            }
                        }
                    }
                }
            }


            if ($songTitle != null && $songArtist != null) {
                $check = Bom::where("deezer_id", $deezerId)->where("status", 1)->first();
                if ($check) {
                    $check->lyric = $isLyric;
                    $check->sync = $isSync;
                    $check->log = $check->log . PHP_EOL . "$curr resync_all2";
                    $check->song_name = $songTitle;
                    $check->artist = $songArtist;
                    $check->save();
                    error_log("reDonwnloadBomDeezer $index/$total $data->id $deezerId $url128");
                }
            }
        }
    }

    public function addAlbum(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.addAlbum|request=' . json_encode($request->all()));
        if (!$request->has('title') || empty($request->title)) {
            return response()->json([
                        "status" => "error",
                        "message" => "The title field is required."
            ]);
        }

        if (!$request->has('artist') || empty($request->artist)) {
            return response()->json([
                        "status" => "error",
                        "message" => "The artist field is required."
            ]);
        }

        if (!$request->has('genre') || empty($request->genre)) {
            return response()->json([
                        "status" => "error",
                        "message" => "The genre field is required."
            ]);
        }

        if ($request->has('releaseDate') && !empty($request->releaseDate)) {
            $releaseDate = new \DateTime($request->releaseDate);
            $minDate = new \DateTime();
            $minDate->modify('+7 days');

            if ($releaseDate < $minDate) {
                return response()->json([
                            "status" => "error",
                            "message" => "The release date must be at least 7 days from today."
                ]);
            }
        }

        if (!$request->hasFile('albumCover')) {
            return response()->json([
                        "status" => "error",
                        "message" => "The Album Cover is required."
            ]);
        }

        $image = $request->file('albumCover');
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($image->getMimeType(), $allowedMimes)) {
            return response()->json([
                        "status" => "error",
                        "message" => "The Album Cover must be a valid image (jpeg, png, jpg, gif)."
            ]);
        }

        list($width, $height) = getimagesize($image->getPathname());
        if ($width < 1400 || $height < 1400) {
            return response()->json([
                        "status" => "error",
                        "message" => "The Album Cover dimensions must be at least 1400x1400 pixels."
            ]);
        }

        $artist = BomArtist::find($request->artist);
        if (!$artist) {
            return response()->json([
                        "status" => "error",
                        "message" => "Not found Artist"
            ]);
        }

        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $pathPublic = public_path("images/album_covers/");
        $fullPathFile = "$pathPublic$imageName";
        $image->move($pathPublic, $imageName);
        $cdnCmd = "gbak upload-r2 --input $fullPathFile --server automusic-image";
        Log::info("addAlbum upload cdn cmd $cdnCmd");
        $ul = shell_exec($cdnCmd);
        Log::info("addAlbum upload cdn result $ul");
        if ($ul != null && $ul != "") {
            $direct = trim($ul);
            Log::info($direct);
            unlink($fullPathFile);
            $album = new BomAlbum();
            $album->username = $user->user_name;
            $album->genre_id = $request->genre;
            $album->genre_name = $request->genreText;
            $album->album_name = trim($request->title);
            $album->artist_id = $artist->id;
            $album->artist = $artist->artist_name;
            $album->desc = $request->description ?? null;
            $album->release_date = $request->releaseDate ?? null;
            $album->album_cover = $direct;
            $album->created = Utils::timeToStringGmT7(time());
            $album->save();
            return response()->json([
                        'status' => "success",
                        'message' => 'Album created successfully',
                        'album' => $album
            ]);
        } else {
            return response()->json([
                        'status' => "error",
                        'message' => 'Upload Album Cover fail'
            ]);
        }
    }

    public function sendAlbumToSalad(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.sendAlbumToSalad|request=' . json_encode($request->all()));
        $api = "https://distro.360promo.fm";
//        $api = "https://3a7c-2402-800-62d1-95c6-a8b4-f88f-1e83-dbd9.ngrok-free.app";
        $albumDb = BomAlbum::where("id", $request->id)->first();
        if (!$albumDb) {
            return response()->json(['status' => "error", 'message' => 'Not found album']);
        }
        $year = $year = substr($albumDb->release_date, 0, 4);
        $bomTracks = Bom::where("album_id", $request->id)->get();
        $format = "Album";
        if (count($bomTracks) == 0) {
            return response()->json(['status' => "error", 'message' => 'You need to add songs to the album before releasing it']);
        }


        $releaseDate = new \DateTime($albumDb->release_date);
        $minDate = new \DateTime();
        $minDate->modify('+7 days');

        if ($releaseDate < $minDate) {
            return response()->json([
                        "status" => "error",
                        "message" => "The release date must be at least 7 days from today."
            ]);
        }


        if (!$request->is_admin_music) {
            $albumDb->is_released = 2;
            $albumDb->updated = Utils::timeToStringGmT7(time());
            $albumDb->save();
            return response()->json(['status' => "success", 'message' => 'Request successful, please wait for admin confirmation']);
        }
        if (count($bomTracks) == 1) {
            $format = "Single";
        }
        if ($albumDb->upc == null) {
            $upcRp = RequestHelper::callAPI("GET", "$api/api/salad/upc/get", []);
            if ($upcRp->upc == null) {
                return response()->json(['status' => "error", 'message' => 'Can not get UPC']);
            }
            $upc = $upcRp->upc->value;
            $albumDb->upc = $upc;
            $albumDb->save();
        } else {
            $upc = $albumDb->upc;
        }
        $album = (object) [
                    "format" => $format,
                    "upc" => $upc,
                    "title" => $albumDb->album_name,
                    "display_artist" => $albumDb->artist,
                    "label" => "SoundHex",
                    "labelId" => "15",
                    "compilation" => false,
                    "release_date" => $albumDb->release_date,
                    "publishing_info" => ["year" => $year, "owner" => "SoundHex"],
                    "copyright_info" => ["year" => $year, "owner" => "SoundHex"],
                    "rights_holders" => "SoundHex",
                    "original_release_date" => "$albumDb->release_date 00:00:00",
                    "advisory" => "None",
                    "audio_language" => "English",
                    "metadata_language" => "English",
                    "primary_genre" => $albumDb->genre_id,
                    "secondary_genre" => null,
                    "participants" => [
                            [
                            "id" => null,
                            "name" => $albumDb->artist,
                            "role" => "Main Artist",
                            "sort_order" => 0
                        ],
                            [
                            "id" => "119",
                            "name" => "SoundHex",
                            "role" => "Producer",
                            "sort_order" => 1
                        ],
                            [
                            "id" => null,
                            "name" => $albumDb->artist,
                            "role" => "Composer",
                            "sort_order" => 2
                        ]
                    ],
                    "cover_image" => $albumDb->album_cover
        ];

        $tracks = [];

        foreach ($bomTracks as $index => $track) {

            $index++;
            if ($track->isrc == null) {
                $isrcRp = RequestHelper::callAPI("GET", "$api/api/salad/isrc/get", []);
                Log::info(json_encode($isrcRp));
                if ($isrcRp->isrc == null) {
                    return response()->json(['status' => "error", 'message' => 'Can not get ISRC']);
                }
                $isrc = $isrcRp->isrc->value;
                $track->isrc = $isrc;
                $track->save();
            } else {
                $isrc = $track->isrc;
            }
            Log::info("isrc $isrc");
            $temp = (object) [
                        "tracknum" => $index,
                        "title" => $track->song_name,
                        "display_artist" => $track->artist,
                        "isrc" => $isrc,
                        "length" => round($track->duration_ms / 1000),
                        "publishing_info" => ["year" => $year, "owner" => "SoundHex"],
                        "copyright_info" => ["year" => $year, "owner" => "SoundHex"],
                        "rights_holders" => "SoundHex",
                        "advisory" => "None",
                        "audio_language" => "English",
                        "primary_genre" => $albumDb->genre_id,
                        "secondary_genre" => null,
                        "participants" => [
                                [
                                "id" => null,
                                "name" => $albumDb->artist,
                                "role" => "Main Artist",
                                "sort_order" => 0
                            ],
                                [
                                "id" => "119",
                                "name" => "SoundHex",
                                "role" => "Producer",
                                "sort_order" => 1
                            ],
                                [
                                "id" => null,
                                "name" => $albumDb->artist,
                                "role" => "Composer",
                                "sort_order" => 2
                            ]
                        ],
                        "audio_url" => $track->direct_wav
            ];
            $tracks[] = $temp;
        }

        $album->tracks = $tracks;
        Log::info(json_encode($album));
        $input = (object) [
                    "user_id" => "b8de06b2-db04-429e-b956-9a03867006d1",
                    "release" => $album
        ];
        $res = RequestHelper::callAPI("POST", "$api/api/release/add", $input);
        Log::info("res " . json_encode($res));
        if (isset($res->data->id)) {
            $albumDb->distro_release_id = $res->data->id;
            $albumDb->updated = Utils::timeToStringGmT7(time());
            $albumDb->is_released = 1;
            $albumDb->save();
            return response()->json(["status" => "success", "message" => "Success"]);
        } else {
            return response()->json(["status" => "error", "message" => "There was an error releasing the song."]);
        }
    }

    public function getSongsForRelease(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $songs = Bom::where("is_releasable", 1)
                ->where("status", 1)
                ->where("sync", 1)
                ->where("username", $user->user_name)
                ->whereNotNull("direct_wav")
                ->whereNull("album_id");
//        if (!$request->is_admin_music) {
//            $songs = $songs->where("username", $user->user_name);
//        }
        if (isset($request->group_id) && $request->group_id != "-1") {
            $group = BomGroups::where("id", $request->group_id)->first();
            $ids = $group->list_song != null ? json_decode($group->list_song) : [];
//            if (count($ids) > 0) {
            $songs = $songs->whereIn("id", $ids);
//            }
        }

        $songs = $songs->get([
            "id",
            "genre",
            "song_name as title",
            "artist as artist",
            DB::raw("CONCAT(LPAD(FLOOR(round(duration_ms/1000)/ 60), 2, '0'), ':', LPAD(round(duration_ms/1000)% 60, 2, '0')) AS duration"),
            "album_id as albumId",
            "direct_wav as audioUrl"
        ]);
//        Log::info(DB::getQueryLog());
        return response()->json($songs);
    }

    public function getListAlbum(Request $request) {
        $user = Auth::user();
        DB::enableQueryLog();
        $albumId = $request->id;

        $query = DB::table('bom_albums as a')
                ->leftJoin('bom as b', 'a.id', '=', 'b.album_id')
                ->select(
                        'a.id', 'a.album_name as name', 'a.artist', 'a.desc as description', 'a.is_released as distributed', 'a.album_cover as coverImg', 'a.release_date as releaseDate', 'a.genre_name as genre', DB::raw('GROUP_CONCAT(b.id) as songs')
                )
                ->groupBy('a.id', 'a.album_name', 'a.desc', 'a.is_released', 'a.album_cover', 'a.release_date', 'a.genre_name', 'a.artist');
        $query->where('a.status', 1);
        // Nếu có id thì chỉ lấy album đó
        if (!empty($albumId)) {
            $query->where('a.id', $albumId);
        }
        if (!$request->is_admin_music) {
            $query->where('a.username', $user->user_name);
        }

        $albums = $query->get();
//        Log::info(DB::getQueryLog());
        // Chuyển danh sách bài hát từ chuỗi sang mảng
        $albums = $albums->map(function ($album) {
            $album->songs = $album->songs ? explode(',', $album->songs) : [];
            return $album;
        });

        return response()->json($albums);
    }

    public function getAlbum(Request $request) {
        $user = Auth::user();
        if ($request->is_admin_music) {
            $album = BomAlbum::where("id", $request->id)->first();
        } else {
            $album = BomAlbum::where("id", $request->id)->where("username", $user->user_name)->first();
        }
        return response()->json($album);
    }

    public function getSongsByAlbum(Request $request) {
        $user = Auth::user();
        $songs = Bom::where("is_releasable", 1)
                ->where("status", 1)
                ->where("sync", 1)
                ->where("album_id", $request->id)
                ->whereNotNull("direct_wav");
        $songs = $songs->get([
            "id",
            "genre",
            "song_name as title",
            "artist as artist",
            "spotify_id",
            DB::raw("CONCAT(LPAD(FLOOR(round(duration_ms/1000)/ 60), 2, '0'), ':', LPAD(round(duration_ms/1000)% 60, 2, '0')) AS duration"),
            "album_id as albumId",
            "direct_link as audioUrl"
        ]);
        return response()->json($songs);
    }

    public function addSongToAlbum(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BomController.addSongToAlbum|request=" . json_encode($request->all()));
        $song = Bom::where("id", $request->song_id)->where("username", $user->user_name)->first();
        if (!$song) {
            return response()->json(["status" => "error", "message" => "Song is not exists"]);
        }
        $album = BomAlbum::where("id", $request->album_id)->where("is_released", 0)->first();
        if (!$album) {
            return response()->json(["status" => "error", "message" => "Album is not exists"]);
        }
        $song->album_id = $request->album_id;
        $song->save();
        return response()->json(["status" => "success", "message" => "Success"]);
    }

    public function deleteSongFromAlbum(Request $request) {
        $username = 'truongpv';
        Log::info('|BomController.deleteSongFromAlbum|request=' . json_encode($request->all()));
        $song = Bom::where("id", $request->song_id)->first();
        if (!$song) {
            return response()->json(["status" => "error", "message" => "Not found song"]);
        }
        $album = BomAlbum::where("id", $song->album_id)->where("is_released", 0)->first();
        if (!$album) {
            return response()->json(["status" => "error", "message" => "Album is not exists or released"]);
        }
        $song->album_id = null;
        $song->save();
        return response()->json(["status" => "success", "message" => "Success"]);
    }

    public function indexAlbum(Request $request) {
        return view('components.album', []);
    }

    public function albumListArtist(Request $request) {
        $user = Auth::user();
        $datas = BomArtist::where("username", $user->user_name)->orderBy("id", "desc")->get();
        return response()->json($datas);
    }

    public function albumAddArtist(Request $request) {
        $user = Auth::user();
        if (!isset($request->artist_name)) {
            return response()->json(["status" => "success", "message" => "Artist can not be empty"]);
        }
        $data = BomArtist::where("artist_name", trim($request->artist_name))->first();
        if (!$data) {
            $data = new BomArtist();
            $data->username = $user->user_name;
            $data->artist_name = trim($request->artist_name);
            $data->created = Utils::timeToStringGmT7(time());
            $data->save();
            return response()->json(["status" => "success", "message" => "Success"]);
        }
        return response()->json(["status" => "error", "message" => "Artist is already exists"]);
    }

    public function updateAlbumReleaseDate(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BomController.updateAlbumReleaseDate|request=" . json_encode($request->all()));
        if ($request->is_admin_music) {
            $album = BomAlbum::where("id", $request->album_id)->first();
        } else {
            $album = BomAlbum::where("id", $request->album_id)->where("username", $user->user_name)->first();
        }
        if (!$album) {
            return response()->json(["status" => "error", "message" => "Not found album"]);
        }

        if ($request->has('release_date') && !empty($request->release_date)) {
            $releaseDate = new \DateTime($request->release_date);
            $minDate = new \DateTime();
            $minDate->modify('+7 days');

            if ($releaseDate < $minDate) {
                return response()->json([
                            "status" => "error",
                            "message" => "The release date must be at least 7 days from today."
                ]);
            }
        }

        $album->release_date = $request->release_date;
        $album->save();
        return response()->json(["status" => "success", "message" => "Release date updated successfully"]);
    }

    //hàm update trạng thái của album sau khi đã distribute thành còng trên distro
    public function updateDistroAlbumStatus(Request $request) {
        Log::info("BomController.updateDistroAlbumStatus|request=" . json_encode($request->all()));
        $album = BomAlbum::where("distro_release_id", $request->release_id)->first();
        if (!$album) {
            return response()->json(["status" => "error", "message" => "Not found album"]);
        }
        $album->status = $request->status;
        $album->distro_log = $request->log;
        if ($album->status == 5) {
            $album->distro_release_date = gmdate("Y-m-d", time());
        }
        $album->save();
        return response()->json(["status" => "success", "message" => "Updated successfully"]);
    }

    //hàm trả về kết quả scan 1 cách lần lươnt
    public function scanAlbum(Request $request) {
        // Tắt output buffering
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Tắt các tính năng buffering khác của PHP
        ini_set('output_buffering', 'off');
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);

        // Thiết lập header cần thiết cho SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Cho Nginx
        // Đảm bảo không có buffer nào hoạt động
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }

        // Tăng timeout nếu cần
        set_time_limit(0);

        try {
            //kiểm tra album
            $album = BomAlbum::where("status", 1)->where("id", $request->id)->first();
            if (!$album) {
                echo "event: error\n";
                echo "data: " . json_encode(['message' => 'Not found album']) . "\n\n";
                flush();
                exit();
            }
            // Lấy tất cả bài hát trong album
            $songs = Bom::where("album_id", $request->id)->whereNull("spotify_id")->get();
            $totalSongs = count($songs);

            if ($totalSongs == 0) {
                echo "event: error\n";
                echo "data: " . json_encode(['message' => 'No songs scanned']) . "\n\n";
                flush();
                exit();
            }

            // Gửi thông tin bắt đầu scan
            echo "event: start\n";
            echo "data: " . json_encode(['totalSongs' => $totalSongs]) . "\n\n";
            flush();

            // Đảm bảo dữ liệu được gửi ngay lập tức
            usleep(100000); // Đợi 100ms
            // Xử lý từng bài hát
            $isUpdateAlbum = 0;
            foreach ($songs as $index => $song) {
                // Thực hiện quét Spotify
                if ($song->isrc != null) {
                    $spotifyInfo = $this->searchSpotify($song->isrc);
                    // Cập nhật spotify_id cho bài hát
                    if ($spotifyInfo) {
                        $song->spotify_id = $spotifyInfo["track_id"];
                        $song->save();
                        if (!$isUpdateAlbum) {
                            $album->spotify_info = json_encode((object) [
                                        "album_id" => $spotifyInfo["album_id"],
                                        "artist_id" => $spotifyInfo["artist_id"]
                            ]);
                            $album->save();
                        }
                    }
                }


                // Cập nhật tiến trình
                $processedCount = $index + 1;
                $progress = round(($processedCount / $totalSongs) * 100);

                // Gửi dữ liệu tiến trình
                echo "event: progress\n";
                echo "data: " . json_encode([
                    'progress' => $progress,
                    'processed' => $processedCount,
                    'total' => $totalSongs,
                    'song' => $song->title,
                    'spotifyId' => $spotifyInfo["track_id"] ?: null
                ]) . "\n\n";
                flush();

                // Đợi một chút để đảm bảo dữ liệu được gửi
                usleep(200000); // 200ms
            }

            // Gửi sự kiện hoàn thành
            echo "event: complete\n";
            echo "data: " . json_encode([
                'message' => 'Spotify scan completed successfully',
                'songsUpdated' => $totalSongs
            ]) . "\n\n";
            flush();
        } catch (\Exception $e) {
            Log::error('Spotify scan error: ' . $e->getMessage());

            echo "event: error\n";
            echo "data: " . json_encode([
                'message' => 'Error scanning album: ' . $e->getMessage()
            ]) . "\n\n";
            flush();
        }

        exit(); // Đảm bảo không có nội dung nào khác được gửi
    }

    private function searchSpotify($isrc) {
        Log::info("searchSpotify http://source.automusic.win/spotify/search/track/isrc/$isrc");
        $data = RequestHelper::callAPI2("GET", "http://source.automusic.win/spotify/search/track/isrc/$isrc", []);
        if (isset($data->tracks)) {
            // Kiểm tra total và items
            if (isset($data->tracks->total) && $data->tracks->total > 0 &&
                    isset($data->tracks->items) && !empty($data->tracks->items)) {
                $track = $data->tracks->items[0];
                // Lấy Spotify ID từ bài hát đầu tiên tìm thấy
                if (isset($track->id)) {
                    $result = [
                        'track_id' => $track->id,
                        'album_id' => null,
                        'artist_id' => null,
                    ];

                    // Lấy thông tin album
                    if (isset($track->album) && isset($track->album->id)) {
                        $result['album_id'] = $track->album->id;
                    }

                    // Lấy thông tin artist đầu tiên
                    if (isset($track->artists) && !empty($track->artists) && isset($track->artists[0]->id)) {
                        $result['artist_id'] = $track->artists[0]->id;
                    }

                    return $result;
                }
            }
        }
        return null;
    }

}
