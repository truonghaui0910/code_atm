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
use App\User;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Validator;

class BomController extends Controller {

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

        if (isset($request->name) && $request->name != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('bom.song_name', 'like', '%' . $request->name . '%')->orWhere('bom.id', $request->name);
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

        // Thêm mới
        if ($request->bom_id == null) {
            if ($request->deezerId != null) {
                $listDeezerId = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->deezerId)));
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

                    // Kiểm tra xem bài hát đã tồn tại chưa
                    $check = Bom::where("deezer_id", $deezerId)->where("status", 1)->first();
                    if ($check) {
                        $listAllSong[] = $check->id;
                        $fail++;
                        $result->status = 2;
                        $result->bom_id = $check->id;
                        $results[] = $result;
                        continue;
                    }

                    // Lấy thông tin cơ bản từ API
                    $trackInfo = $this->getBasicTrackInfo($deezerId);

                    // Tạo bản ghi Bom mới với giá trị mặc định
                    $bom = new Bom();
                    $bom->username = $user->user_name;
                    $bom->genre = $request->genre;
                    $bom->song_name = $trackInfo['song_name'] ?? "";
                    $bom->deezer_id = $deezerId;
                    $bom->local_id = $localId;
                    $bom->artist = $trackInfo['artist'] ?? "";
                    $bom->social = $request->social;
                    $bom->deezer_artist_id = $request->deezerArtistId;
                    $bom->created = $curr;
                    $bom->created_timestamp = time();
                    $bom->count = 0;
                    $bom->lyric = $trackInfo['lyric'] ?? 0;
                    $bom->sync = $trackInfo['sync'] ?? 0;
                    $bom->isrc = $trackInfo['isrc'] ?? null;
                    $bom->status = 1;
                    $bom->log = "$curr $user->user_name added to the system";
                    $bom->save();


                    $success++;
                    $result->status = 1;
                    $result->bom_id = $bom->id;
                    $result->song_name = $bom->song_name;
                    $result->artist = $bom->artist;
                    $result->is_lyric = $bom->lyric;
                    $results[] = $result;
                    $listAllSong[] = $bom->id;
                }

                // Thêm vào các nhóm
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
                // Xử lý thêm bài hát local (giữ nguyên code)
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
                // Thêm vào group 
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
            // Xử lý chỉnh sửa (giữ nguyên code cũ)
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

    // Hàm mới để lấy thông tin cơ bản của track
    private function getBasicTrackInfo($deezerId) {
        $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$deezerId");
        $trackInfo = [
            'isrc' => null,
            'song_name' => null,
            'artist' => null,
            'lyric' => 0,
            'sync' => 0
        ];

        if ($trackRes != null && $trackRes != "") {
            $track = json_decode($trackRes);
            if ($track->count > 0 && !empty($track->results[0])) {
                $trackInfo['isrc'] = $track->results[0]->isrc;
                $trackInfo['song_name'] = $track->results[0]->title;
                $trackInfo['artist'] = $track->results[0]->artist;
                if ($track->results[0]->lyric_sync != "" && $track->results[0]->lyric_sync != "null" && $track->results[0]->lyric_sync != null) {
                    $trackInfo['lyric'] = 1;
                }
                if ($track->results[0]->url_128 != "") {
                    $trackInfo['sync'] = 1;
                }
            }
        }

        return $trackInfo;
    }

    // Thêm hàm mới để xử lý tải xuống trong background
    public function processDeezerDownload(Bom $bom) {
        $deezerId = $bom->deezer_id;

        if (empty($deezerId)) {
            error_log("noclaimSync Cannot process download for Bom ID: {$bom->id} - No Deezer ID");
            return ['success' => false, 'message' => 'No Deezer ID'];
        }
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);

        // Kiểm tra nếu đã đồng bộ rồi thì không cần thực hiện lại
        if ($bom->sync == 1) {
            return ['success' => true, 'message' => 'Already synced'];
        }

        // Thực hiện tải xuống
        error_log("noclaimSync Downloading Deezer track: http://source.automusic.win/deezer/track/get/$deezerId");
        $res = RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$deezerId");
        error_log("noclaimSync Download result: $res");

        // Lấy thông tin bài hát sau khi tải xuống
        $trackInfo = $this->getBasicTrackInfo($deezerId);

        // Cập nhật thông tin bài hát trong database nếu có dữ liệu
        if ($trackInfo['song_name'] !== null) {
            $bom->isrc = $trackInfo['isrc'];
            $bom->song_name = $trackInfo['song_name'];
            $bom->artist = $trackInfo['artist'];
            $bom->lyric = $trackInfo['lyric'];
            $bom->sync = $trackInfo['sync'];
            $bom->log = $bom->log . PHP_EOL . "$curr Downloaded and synced";
            $bom->save();
            return [
                'success' => true,
                'message' => 'Synced successfully',
                'track_info' => $trackInfo
            ];
        } else {
            // Cập nhật số lần thử lại nếu không lấy được thông tin
            $bom->retry = ($bom->retry ?? 0) + 1;
            $bom->log = $bom->log . PHP_EOL . "$curr Download attempt failed - retry count: {$bom->retry}";
            $bom->save();

            error_log("noclaimSync Failed download for Bom ID: {$bom->id}, Deezer ID: $deezerId");
            return [
                'success' => false,
                'message' => 'Failed to get track information',
                'retry_count' => $bom->retry
            ];
        }
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
                $boom->artist = $response->title;
                $boom->song_name = $response->artist;
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
            $lyricText = "Your lyric here...";
            if ($boom->lyric_text != null && $boom->lyric_text != "") {
                $lyricText = urlencode($boom->lyric_text);
            }
            //2025/05/29 bỏ cạch cạch theo deezer id, chuyển hết thành local id dể tự động lưu local_id về bom
            $url = $boom->direct_link;
            $songName = urlencode($boom->song_name);
            $artist = urlencode($boom->artist);
            $bomId = $boom->id;
            if ($boom->deezer_id != null) {
                $temp = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$boom->deezer_id");
                $data = json_decode($temp);
                $url = $data->results[0]->url_128;
            }
            return array("status" => "success",
                "type" => "direct",
                "lyric" => $lyricText,
                "url" => $url,
                "username" => $user->user_name,
                "title" => $songName,
                "artist" => $artist,
                "id" => $bomId);

//            if ($boom->deezer_id != null) {
//                $temp = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id=$boom->deezer_id");
//                $data = json_decode($temp);
//                $url = $data->results[0]->url_128;
//                return array("status" => "success", 
//                    "type" => "deezer", 
//                    "id" => $data->results[0]->id, 
//                    "cam_id" => $boom->id, 
//                    "url" => $data->results[0]->url_128, 
//                    "username" => $user->user_name);
//            } else {
//                return array("status" => "success",
//                    "type" => "direct",
//                    "lyric" => $lyricText,
//                    "url" => $url,
//                    "username" => $user->user_name,
//                    "title" => $songName,
//                    "artist" => $artist,
//                    "id" => $bomId);
//            }
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

    public function downloadSongs(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.downloadSongs|request=' . json_encode($request->all()));

        try {
            $bomIds = $request->bom_array;
            if (empty($bomIds)) {
                return response()->json(['status' => 'error', 'message' => 'No songs selected']);
            }

            $songs = Bom::whereIn('id', $bomIds)->where('status', 1)->get();
            if ($songs->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'No valid songs found']);
            }

            $songInfos = [];
            $totalSongs = count($songs);

            foreach ($songs as $index => $song) {
                $downloadUrl = null;
                $trackInfo = null;

                // Check if song has direct_link first
                if (!empty($song->direct_link)) {
                    $downloadUrl = $song->direct_link;
                    $trackInfo = [
                        'id' => $song->id,
                        'song_name' => $song->song_name,
                        'artist' => $song->artist
                    ];
                    Log::info("Using direct_link for song {$song->id}: {$downloadUrl}");
                } else if (!empty($song->deezer_id)) {
                    // First try to get from timestamp API (faster)
                    $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id={$song->deezer_id}");
                    if ($trackRes) {
                        $track = json_decode($trackRes);
                        if ($track->count > 0 && !empty($track->results[0]->url_128)) {
                            $downloadUrl = $track->results[0]->url_128;
                            $trackInfo = [
                                'id' => $song->id,
                                'song_name' => $track->results[0]->title,
                                'artist' => $track->results[0]->artist
                            ];
                            Log::info("Got download URL from timestamp API for song {$song->id}: {$downloadUrl}");
                        }
                    }

                    // If timestamp API doesn't have url_128, call deezer API
                    if (!$downloadUrl) {
                        Log::info("Calling Deezer API for song {$song->id}, deezer_id: {$song->deezer_id}");
                        $deezerResponse = RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/{$song->deezer_id}");

                        if ($deezerResponse) {
                            // Try timestamp API again after deezer call
                            $trackRes = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?deezer_id={$song->deezer_id}");
                            if ($trackRes) {
                                $track = json_decode($trackRes);
                                if ($track->count > 0 && !empty($track->results[0]->url_128)) {
                                    $downloadUrl = $track->results[0]->url_128;
                                    $trackInfo = [
                                        'id' => $song->id,
                                        'song_name' => $track->results[0]->title,
                                        'artist' => $track->results[0]->artist
                                    ];
                                    Log::info("Got download URL after deezer call for song {$song->id}: {$downloadUrl}");
                                }
                            }
                        }
                    }
                }
                if ($downloadUrl && $trackInfo) {
                    $filename = Utils::sanitizeFilename($trackInfo['id'] . '-' . $trackInfo['artist'] . '-' . $trackInfo['song_name'] . '.mp3');
                    if (Utils::containString($downloadUrl, "http://")) {
                        $dataUrl = [
                            'url' => $downloadUrl,
                            'filename' => $filename
                        ];
                        $encodedData = base64_encode(json_encode($dataUrl));
                        $downloadUrl = "/proxy-download/$encodedData";
                    }
                    $songInfos[] = [
                        'id' => $trackInfo['id'],
                        'url' => $downloadUrl,
                        'filename' => $filename,
                        'song_name' => $trackInfo['song_name'],
                        'artist' => $trackInfo['artist']
                    ];
                } else {
                    Log::error("No download URL found for song {$song->id}");
                }
            }

            if (empty($songInfos)) {
                return response()->json(['status' => 'error', 'message' => 'No songs could be processed for download']);
            }

            return response()->json([
                        'status' => 'success',
                        'message' => 'Songs ready for download',
                        'songs' => $songInfos,
                        'total' => count($songInfos)
            ]);
        } catch (Exception $e) {
            Log::error('Download songs error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Download failed: ' . $e->getMessage()]);
        }
    }

    public function proxyDownload($encodedData) {
        try {
            // Decode URL
            $data = json_decode(base64_decode($encodedData), true);

            if (!$data || !isset($data['url']) || !isset($data['filename'])) {
                abort(400, 'Invalid data');
            }

            $httpUrl = $data['url'];
            $fileName = $data['filename'];

            Log::info("Downloading: $httpUrl, fileName: $fileName");



            // Thêm context options để xử lý redirects và headers
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ],
                    'follow_location' => true,
                    'max_redirects' => 5,
                    'timeout' => 30
                ]
            ]);

            $fileContent = file_get_contents($httpUrl, false, $context);

            if ($fileContent === false) {
                Log::error("Failed to download file from: $httpUrl");
                abort(404, 'File not found or cannot be downloaded');
            }

            return response($fileContent)
                            ->header('Content-Type', 'audio/mpeg')
                            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '.mp3"')
                            ->header('Content-Length', strlen($fileContent));
        } catch (Exception $e) {
            Log::error("Proxy download error: " . $e->getMessage());
            abort(500, 'Download failed');
        }
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
        } catch (Exception2 $ex) {
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
                        $folders = RequestHelper::getAllDriveFiles($driveInfo->drive_id);
                        Log::info("folder " . json_encode($folders));
                        $listMp3 = [];
                        foreach ($folders as $file) {
                            if ($file->kind == "drive#file" && Utils::containString($file->mimeType, "audio")) {
                                $listMp3[] = $file->id;
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
                    $customSongNames = [];
                    if ($request->list_artist != null) {
                        $customsArtistName = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_artist)));
                    }
                    if ($request->list_song_name != null) {
                        $customSongNames = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->list_song_name)));
                    }
                    if (isset($request->chk_keep_name)) {
                        if (count($customsArtistName) == 0) {
                            return response()->json(array("status" => "error", "message" => "List artist can not be empty"));
                        }
                        if (count($customSongNames) < count($workIds)) {
                            return response()->json(array("status" => "error", "message" => "Number of song name must be > or equal to " . count($workIds)));
                        }
                    }

                    foreach ($workIds as $indx => $work) {
                        $random_artist = $work->clip->display_name;
                        $songName = $work->clip->title;
                        if (isset($work->clip->audio_url)) {
                            if (isset($request->chk_keep_name)) {
                                if (count($customsArtistName) > 0) {
                                    $random_key = array_rand($customsArtistName);
                                    $random_artist = $customsArtistName[$random_key];
                                }
                                if (count($customSongNames) > 0) {
                                    $songName = $customSongNames[$indx];
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
                if ($sourceType == "OVERTONE" || $sourceType == "UDIO" || $sourceType == "SUNO") {
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
                    if (isset($request->chk_lyric_later)) {
                        $noclaim->lyric = 3;
                        $noclaim->lyric_text = "...";
                    }
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
                    //đồng bộ lyric SUNO
                    if ($sourceType == "SUNO") {
                        $lyric = $this->getSunoLyrics($noclaim->song_id);
                        if ($lyric != "") {
                            $noclaim->is_sync_lyric = 1;
                            $noclaim->lyric_text = $lyric;
                            $noclaim->save();
                        }
                    }
                } else {
                    $fail++;
                    $check->log = $check->log . PHP_EOL . "$curr $user->user_name dupticate";
//                    $check->song_name = $data->song_name;
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

    //tiến trình download local
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
                        //2025/04/25 ko tạo lyric fake cho bài có lyric = 2, còn lại là tạo fake hết
                        if ($data->lyric == 3) {
                            $data->lyric = 0;
                        } else {
                            $data->lyric = 1;
                        }
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

        $deezerTracks = Bom::where("status", 1)
                ->where("sync", 0)
                ->whereNotNull("deezer_id")
                ->where("retry", "<=", 3)
                ->get();

        error_log("noclaimSync: Processing Deezer tracks = " . count($deezerTracks));

        foreach ($deezerTracks as $bom) {
            // Sử dụng hàm processDeezerDownload để xử lý
            $result = $this->processDeezerDownload($bom);
            error_log("noclaimSync: Processed Deezer track for Bom ID {$bom->id}, result: " . ($result['success'] ? 'success' : 'failed'));

            // Thêm delay giữa các request để tránh quá tải server
            usleep(200000); // 200ms
        }

        ProcessUtils::unLockProcess($processName);
    }

    public function convertWav() {
        $processName = "convert-wav";
        if (ProcessUtils::isfreeProcess($processName, 10)) {
            ProcessUtils::lockProcess($processName);
            $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $boms = Bom::where("status", 1)->where("sync", 1)->whereNotNull("direct_link")->whereNull("direct_wav")->where("is_releasable", 1)->take(20)->get();
            $total = count($boms);
            $index = 0;
            error_log("convertWav total=$total");
            foreach ($boms as $bom) {
                $index++;
                $cmdConvert = "sudo ffmpeg -i \"$bom->direct_link\" -acodec pcm_s24le -ar 44100 $bom->id.wav";
                $saved_converted = "$bom->id.wav";

                error_log("convertWav $index/$total $cmdConvert");
                shell_exec($cmdConvert);
                if (count(glob($saved_converted)) > 0) {
                    //kiểm tra trên shazam
                    $shazam = $this->recognizeSong($saved_converted);
                    error_log("convertWav $index/$total shazam" . json_encode($shazam));
                    if ($shazam["exists"] == 1) {
                        $bom->log .= PHP_EOL . "$curr this song exists " . json_encode($shazam);
                        $bom->is_releasable = 2;
                        $bom->save();
                        unlink($saved_converted);
                        continue;
                    }

                    $cdnCmd = "gbak upload-r2 --input $saved_converted --server automusic-audio";
                    error_log("convertWav $index/$total $bom->id upload cdn cmd $cdnCmd");
                    $ul = shell_exec($cdnCmd);
                    error_log("convertWav $index/$total $bom->id upload cdn result $ul");
                    if ($ul != null && $ul != "" && trim($ul) != "None" && Utils::containString(trim($ul), "http")) {
                        $direct = trim($ul);
                        if (file_exists($saved_converted)) {
                            unlink($saved_converted);
                        }
                        $bom->direct_wav = $direct;
                        $bom->save();
                    }
                    error_log("noclaimSync $index/$total $bom->id finish");
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }

    function recognizeSong($mp3FilePath) {
        $pythonScript = '/home/tools/shazam_recognizer.py';
        $cmd = escapeshellcmd("python3.9 $pythonScript " . escapeshellarg($mp3FilePath));
        $output = shell_exec($cmd);
        $result = json_decode($output, true);
        // Kết quả mặc định
        $response = [
            "exists" => 0,
            "title" => "",
            "artist" => "",
            "album" => "",
            "label" => "",
            "release_date" => "",
            "genre" => ""
        ];

        if (!empty($result['matches']) && isset($result['track'])) {
            $track = $result['track'];

            $meta = [];
            if (isset($track['sections'])) {
                foreach ($track['sections'] as $section) {
                    if ($section['type'] === 'SONG' && isset($section['metadata'])) {
                        foreach ($section['metadata'] as $item) {
                            $meta[$item['title']] = $item['text'];
                        }
                    }
                }
            }

            $response['exists'] = 1;
            $response['title'] = $track['title'] ?? '';
            $response['artist'] = $track['subtitle'] ?? '';
            $response['album'] = $meta['Album'] ?? '';
            $response['label'] = $meta['Label'] ?? '';
            $response['release_date'] = $meta['Released'] ?? '';
            $response['genre'] = $track['genres']['primary'] ?? '';
        }

        return $response;
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

    public function getSunoLyrics($song_id) {
        // Gửi request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://suno.com/song/{$song_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: vi,en-US;q=0.9,en;q=0.8',
                'dnt: 1',
                'next-router-state-tree: %5B%22%22%2C%7B%22children%22%3A%5B%22(root)%22%2C%7B%22children%22%3A%5B%22song%22%2C%7B%22children%22%3A%5B%5B%22slug%22%2C%22' . $song_id . '%22%2C%22d%22%5D%2C%7B%22children%22%3A%5B%22__PAGE__%22%2C%7B%7D%5D%7D%5D%7D%5D%7D%2Cnull%2Cnull%2Ctrue%5D%7D%2Cnull%2Cnull%5D',
                'priority: u=1, i',
                'rsc: 1',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ),
        ));

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $http_code !== 200) {
            return false;
        }

        error_log("Response length: " . strlen($response));

        $lyrics_raw = "";
        if (preg_match('/(\d+):T[^,]*,(.+?)(?=\d+:\[)/s', $response, $matches)) {
            $lyrics_raw = $matches[2];
            return trim($lyrics_raw);
        }

        if (preg_match('/"prompt":"([^"]*(?:\\.[^"]*)*)"/', $response, $matches)) {
            $lyrics_raw = $matches[1];
            // Decode escape sequences
            $lyrics = json_decode('"' . $lyrics_raw . '"');
            if ($lyrics) {
                return $lyrics;
            }
        }
        return false;
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
        $isEdit = $request->input('edit_mode') == '1';
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

        if (!$request->is_admin_music && $request->has('releaseDate') && !empty($request->releaseDate)) {
            $releaseDate = new DateTime($request->releaseDate);
            $minDate = new DateTime();
            $minDate->modify('+7 days');

            if ($releaseDate < $minDate) {
                return response()->json([
                            "status" => "error",
                            "message" => "The release date must be at least 7 days from today."
                ]);
            }
        }
        $artist = BomArtist::find($request->artist);
        if (!$artist) {
            return response()->json([
                        "status" => "error",
                        "message" => "Not found Artist"
            ]);
        }
        $instruments = [];
        if (!empty($request->instruments)) {
            $instruments = explode(",", $request->instruments);
        }


        if ($isEdit) {
            $album = BomAlbum::where("id", $request->album_id)->first();
            $album->genre_id = $request->genre;
            $album->genre_name = $request->genreText;
            $album->album_name = trim($request->title);
            if ($album->artist_id != $artist->id) {
                $album->artist_id = $artist->id;
                $album->artist = $artist->artist_name;
                Bom::where("album_id", $album->id)->update(["artist" => $album->artist]);
            }
            $album->release_date = $request->releaseDate ?? null;
            $album->instruments = json_encode($instruments);
            if (isset($request->uploaded_image_url)) {
                $album->album_cover = $request->uploaded_image_url;
            }
            $album->save();
            return response()->json([
                        'status' => "success",
                        'message' => 'Album updated successfully',
                        'album' => $album
            ]);
        } else {


//            if (!$request->hasFile('albumCover')) {
//                return response()->json([
//                            "status" => "error",
//                            "message" => "The Album Cover is required."
//                ]);
//            }
//            $image = $request->file('albumCover');
//            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
//            if (!in_array($image->getMimeType(), $allowedMimes)) {
//                return response()->json([
//                            "status" => "error",
//                            "message" => "The Album Cover must be a valid image (jpeg, png, jpg, gif)."
//                ]);
//            }
//
//            list($width, $height) = getimagesize($image->getPathname());
//            if ($width < 1400 || $height < 1400) {
//                return response()->json([
//                            "status" => "error",
//                            "message" => "The Album Cover dimensions must be at least 1400x1400 pixels."
//                ]);
//            }
//            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
//            $pathPublic = public_path("images/album_covers/");
//            $fullPathFile = "$pathPublic$imageName";
//            $image->move($pathPublic, $imageName);
//            $cdnCmd = "gbak upload-r2 --input $fullPathFile --server automusic-image";
//            Log::info("addAlbum upload cdn cmd $cdnCmd");
//            $ul = shell_exec($cdnCmd);
//            Log::info("addAlbum upload cdn result $ul");
//            if ($ul != null && $ul != "") {
//                $direct = trim($ul);
//                Log::info($direct);
//                unlink($fullPathFile);
            $album = new BomAlbum();
            $album->username = $user->user_name;
            $album->genre_id = $request->genre;
            $album->genre_name = $request->genreText;
            $album->album_name = trim($request->title);
            $album->artist_id = $artist->id;
            $album->artist = $artist->artist_name;
            $album->desc = $request->description ?? null;
            $album->release_date = $request->releaseDate ?? null;
//                $album->album_cover = $direct;
            $album->album_cover = $request->uploaded_image_url;
            $album->created = Utils::timeToStringGmT7(time());
            $album->instruments = json_encode($instruments);
            $album->save();
            return response()->json([
                        'status' => "success",
                        'message' => 'Album created successfully',
                        'album' => $album
            ]);
//            } else {
//                return response()->json([
//                            'status' => "error",
//                            'message' => 'Upload Album Cover fail'
//                ]);
//            }
        }
    }

    public function sendAlbumToSalad(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BomController.sendAlbumToSalad|request=' . json_encode($request->all()));
        $api = "https://distro.360promo.fm";
        $youtubeClaim = 0;
//        $api = "https://3a7c-2402-800-62d1-95c6-a8b4-f88f-1e83-dbd9.ngrok-free.app";
        $albumDb = BomAlbum::where("id", $request->id)->first();
        if (!$albumDb) {
            return response()->json(['status' => "error", 'message' => 'Not found album']);
        }
        //kiểm tra artist_id
        $artistDb = BomArtist::find($albumDb->artist_id);
        if (!$artistDb) {
            return response()->json(['status' => "error", 'message' => 'Not found artist']);
        }
        //kiểm tra xem có active youtube claim không
        if ($artistDb->youtube_claim == 1) {
            $youtubeClaim = 1;
        }
        $year = $year = substr($albumDb->release_date, 0, 4);
        $bomTracks = Bom::where("album_id", $request->id)->orderBy("order_id")->get();
        $format = "Album";
        if (count($bomTracks) == 0) {
            return response()->json(['status' => "error", 'message' => 'You need to add songs to the album before releasing it']);
        }


        $releaseDate = new DateTime($albumDb->release_date);
        $minDate = new DateTime();
        $minDate->modify('+7 days');
        if (!$request->is_admin_music) {
            if ($releaseDate < $minDate) {
                return response()->json([
                            "status" => "error",
                            "message" => "The release date must be at least 7 days from today."
                ]);
            }
        }


        if (!$request->is_admin_music) {
            $albumDb->is_released = 1;
            $albumDb->updated = Utils::timeToStringGmT7(time());
            $albumDb->save();
            return response()->json(['status' => "success", 'message' => 'Request successful, please wait for admin confirmation']);
        }

        //đếm xem số lượng isrc còn đủ dùng không
        $isrcCount = RequestHelper::callAPI("GET", "$api/api/salad/isrc/get/count", []);
        if (!empty($isrcCount->isrcCount)) {
            Log::info("remaining isrc=" . $isrcCount->isrcCount);
            if (count($bomTracks) > $isrcCount->isrcCount) {
                return response()->json(['status' => "success", 'message' => 'Not enough ISRC, please wait for the system to synchronize more']);
            }
        } else {
            Log::info("can not count isrc");
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
        $participants = [
                [
                "id" => null,
                "name" => $albumDb->artist,
                "role" => "Main Artist",
                "sort_order" => 0
            ],
                [
                "id" => null,
                "name" => $albumDb->artist,
                "role" => "Composer",
                "sort_order" => 1
            ],
                [
                "id" => null,
                "name" => $albumDb->artist,
                "role" => "Songwriter",
                "sort_order" => 2
            ],
                [
                "id" => "119",
                "name" => "SoundHex",
                "role" => "Producer",
                "sort_order" => 3
            ],
                [
                "id" => null,
                "name" => $albumDb->artist,
                "role" => "Performer",
                "sort_order" => 4
            ]
        ];
        if (!empty($albumDb->instruments)) {
            foreach (json_decode($albumDb->instruments) as $idx => $ins) {
                $idxs = $idx + 5;
                $participants[] = [
                    "id" => null,
                    "name" => $albumDb->artist,
                    "role" => $ins,
                    "sort_order" => $idxs
                ];
            }
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
                    "participants" => $participants,
                    "cover_image" => $albumDb->album_cover
        ];

        $tracks = [];

        foreach ($bomTracks as $index => $track) {

            $index++;
            if (empty($track->song_name)) {
                return response()->json(['status' => "error", 'message' => 'Song name is empty']);
            }
            if (empty($track->artist)) {
                return response()->json(['status' => "error", 'message' => 'Artist name is empty']);
            }
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
                        "participants" => $participants,
                        "audio_url" => $track->direct_wav
            ];
            $tracks[] = $temp;
        }

        $album->tracks = $tracks;
        Log::info(json_encode($album));
        $input = (object) [
                    "user_id" => "b8de06b2-db04-429e-b956-9a03867006d1",
                    "release" => $album,
                    "youtube_claim" => $youtubeClaim
        ];
        $res = RequestHelper::callAPI("POST", "$api/api/release/add", $input);
        Log::info("sendAlbumToSalad res " . json_encode($res));
        if (isset($res->data->id)) {
            $albumDb->distro_release_id = $res->data->id;
            $albumDb->updated = Utils::timeToStringGmT7(time());
            $albumDb->is_released = 2;
            $albumDb->youtube_claim = $youtubeClaim;
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
                ->whereNotNull("song_name")
                ->where("song_name", "<>", "")
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
        Log::info("$user->user_name|BomController.getListAlbum|request=" . json_encode($request->all()));



        $albumId = $request->id;
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 12); // 12 items per page for grid
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');

        // Giới hạn per_page
        if ($perPage > 100) {
            $perPage = 100;
        }
        if ($perPage < 1) {
            $perPage = 12;
        }

        $query = DB::table('bom_albums as a')
                ->leftJoin('bom as b', 'a.id', '=', 'b.album_id')
                ->leftJoin('bom_artists as ar', 'a.artist', '=', 'ar.artist_name')
                ->select(
                        'a.id', 'a.username', 'a.album_name as name', 'a.artist', 'a.desc as description', 'a.is_released as distributed', 'a.album_cover as coverImg', 'a.release_date as releaseDate', 'a.genre_name as genre', 'ar.artist_total_streams', 'ar.last_update', 'ar.youtube_claim as artist_youtube_claim', 'a.youtube_claim', 'a.distro_release_date', DB::raw('GROUP_CONCAT(b.id) as songs')
                )
                ->groupBy(
                'a.id', 'a.username', 'a.album_name', 'a.desc', 'a.is_released', 'a.album_cover', 'a.release_date', 'a.genre_name', 'a.artist', 'ar.artist_total_streams', 'ar.last_update', 'ar.youtube_claim', 'a.youtube_claim', 'a.distro_release_date'
        );


        $query->where('a.status', 1);
        $countQuery = DB::table('bom_albums as a')->where('a.status', 1);
        // Nếu có id cụ thể thì chỉ lấy album đó
        if (!empty($albumId)) {
            $query->where('a.id', $albumId);
            $countQuery->where('a.id', $albumId);
        }

        // Phân quyền
        if (!$request->is_admin_music) {
            $query->where('a.username', $user->user_name);
            $countQuery->where('a.username', $user->user_name);
        }

        // Xử lý search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('a.album_name', 'like', '%' . $search . '%')
                        ->orWhere('a.artist', 'like', '%' . $search . '%')
                        ->orWhere('a.genre_name', 'like', '%' . $search . '%')
                        ->orWhere('a.username', 'like', '%' . $search . '%')
                        ->orWhere('a.id', 'like', '%' . $search . '%');
            });
            $countQuery->where(function($q) use ($search) {
                $q->where('a.album_name', 'like', '%' . $search . '%')
                        ->orWhere('a.artist', 'like', '%' . $search . '%')
                        ->orWhere('a.genre_name', 'like', '%' . $search . '%')
                        ->orWhere('a.username', 'like', '%' . $search . '%')
                        ->orWhere('a.id', 'like', '%' . $search . '%');
            });
        }

        // Xử lý status filter
        if ($status !== 'all') {
            switch ($status) {
                case 'not-distributed':
                    $query->where('a.is_released', 0);
                    $countQuery->where('a.is_released', 0);
                    break;
                case 'pending':
                    $query->where('a.is_released', 1);
                    $countQuery->where('a.is_released', 1);
                    break;
                case 'distributing':
                    $query->where('a.is_released', 2);
                    $countQuery->where('a.is_released', 2);
                    break;
                case 'distributed':
                    $query->where('a.is_released', 3);
                    $countQuery->where('a.is_released', 3);
                    break;
                case 'error':
                    $query->where('a.is_released', 4);
                    $countQuery->where('a.is_released', 4);
                    break;
                case 'online':
                    $query->where('a.is_released', 5);
                    $countQuery->where('a.is_released', 5);
                    break;
            }
        }

        // Sắp xếp
        $isAdmin = $request->is_admin_music;
        if ($status === 'all') {
            $query->orderBy('ar.artist_total_streams', 'desc');
            $countQuery->orderBy('ar.artist_total_streams', 'desc');
        } else if (!$isAdmin) {
            $query->orderBy('a.id', 'desc');
            $countQuery->orderBy('a.id', 'desc');
        } else {
            $query->orderBy('a.release_date', 'asc');
            $countQuery->orderBy('a.release_date', 'asc');
        }

        $total = $countQuery->count();

//        Log::info("getListAlbum Debug", [
//            'user' => $user->user_name,
//            'is_admin_music' => $request->is_admin_music,
//            'search' => $request->get('search'),
//            'status' => $request->get('status'),
//            'total_from_query' => $total,
//            'total_from_status_counts' => $this->getAlbumStatusCounts($request)['all']
//        ]);

        DB::enableQueryLog();

        $albums = $query->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();
        $queries = DB::getQueryLog();
//        Log::info('SQL Queries:', $queries);
        // Xử lý dữ liệu như cũ
        $albums = $albums->map(function ($album) {
            $album->songs = $album->songs ? explode(',', $album->songs) : [];

            // Tính toán thời gian "time ago"
            if ($album->last_update) {
                try {
                    $lastUpdate = new DateTime($album->last_update, new DateTimeZone('Asia/Ho_Chi_Minh'));
                    $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
                    $interval = $now->diff($lastUpdate);

                    if ($interval->d > 0) {
                        $album->last_update_ago = $interval->d . 'd ago';
                    } elseif ($interval->h > 0) {
                        $album->last_update_ago = $interval->h . 'h ago';
                    } elseif ($interval->i > 0) {
                        $album->last_update_ago = $interval->i . 'm ago';
                    } else {
                        $album->last_update_ago = 'just now';
                    }
                } catch (Exception $e) {
                    $album->last_update_ago = 'unknown';
                }
            } else {
                $album->last_update_ago = 'never updated';
            }

            return $album;
        });

        // Tính toán status counts
        $statusCounts = $this->getAlbumStatusCounts($request);

        return response()->json([
                    'data' => $albums,
                    'pagination' => [
                        'current_page' => (int) $page,
                        'per_page' => (int) $perPage,
                        'total' => $total,
                        'last_page' => ceil($total / $perPage),
                        'from' => (($page - 1) * $perPage) + 1,
                        'to' => min($page * $perPage, $total)
                    ],
                    'status_counts' => $statusCounts
        ]);
    }

    private function getAlbumStatusCounts(Request $request) {
        $user = Auth::user();

        $query = DB::table('bom_albums as a')
                ->select('a.is_released', DB::raw('COUNT(*) as count'))
                ->where('a.status', 1);

        if (!$request->is_admin_music) {
            $query->where('a.username', $user->user_name);
        }

        // Nếu có search thì apply cùng điều kiện
        if (!empty($request->get('search'))) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('a.album_name', 'like', '%' . $search . '%')
                        ->orWhere('a.artist', 'like', '%' . $search . '%')
                        ->orWhere('a.genre_name', 'like', '%' . $search . '%')
                        ->orWhere('a.username', 'like', '%' . $search . '%')
                        ->orWhere('a.id', 'like', '%' . $search . '%');
            });
        }

        $statusData = $query->groupBy('a.is_released')->get();

        $counts = [
            'all' => 0,
            'not-distributed' => 0,
            'pending' => 0,
            'distributing' => 0,
            'distributed' => 0,
            'error' => 0,
            'online' => 0
        ];

        foreach ($statusData as $item) {
            $counts['all'] += $item->count;

            switch ($item->is_released) {
                case 0: $counts['not-distributed'] += $item->count;
                    break;
                case 1: $counts['pending'] += $item->count;
                    break;
                case 2: $counts['distributing'] += $item->count;
                    break;
                case 3: $counts['distributed'] += $item->count;
                    break;
                case 4: $counts['error'] += $item->count;
                    break;
                case 5: $counts['online'] += $item->count;
                    break;
            }
        }

        return $counts;
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
                ->whereNotNull("direct_wav")
                ->orderBy("order_id");
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

    public function updateSongOrder(Request $request) {
        // Validate the request data
        $validator = Validator::make($request->all(), [
                    'album_id' => 'required|integer',
                    'song_order' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                        'status' => 'error',
                        'message' => $validator->errors()->first()
            ]);
        }

        // Parse the song order JSON
        $songOrder = json_decode($request->song_order, true);

        if (!is_array($songOrder) || empty($songOrder)) {
            return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid song order data'
            ]);
        }

        try {
            // Get the album
            $album = DB::table('bom_albums')->where('id', $request->album_id)->first();

            // Check if album exists
            if (!$album) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Album not found'
                ]);
            }

            // Check if the album is already distributed
            if ($album->is_released == 1) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Cannot update song order for a distributed album'
                ]);
            }

            // Begin transaction
            DB::beginTransaction();

            // Update each song's order
            foreach ($songOrder as $item) {
                if (isset($item['song_id']) && isset($item['order_id'])) {
                    // Update the order_id in the song_album table
                    DB::table('bom')
                            ->where('id', $item['song_id'])
                            ->where('album_id', $request->album_id)
                            ->update(['order_id' => $item['order_id']]);
                }
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                        'status' => 'success',
                        'message' => 'Song order updated successfully'
            ]);
        } catch (Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return response()->json([
                        'status' => 'error',
                        'message' => 'An error occurred while updating song order: ' . $e->getMessage()
            ]);
        }
    }

    public function addSongsToAlbum(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BomController.addSongsToAlbum|request=" . json_encode($request->all()));

        // Validate input - hỗ trợ cả song_ids array và song_id single
        $validator = Validator::make($request->all(), [
                    'album_id' => 'required|integer'
        ]);

        // Xử lý song_ids - hỗ trợ cả trường hợp gửi 1 song hoặc nhiều songs
        $songIds = [];
        if ($request->has('song_ids') && is_array($request->song_ids)) {
            $songIds = $request->song_ids;
        } elseif ($request->has('song_id')) {
            $songIds = [$request->song_id];
        } else {
            return response()->json([
                        "status" => "error",
                        "message" => "song_ids or song_id is required"
            ]);
        }

        if (empty($songIds)) {
            return response()->json([
                        "status" => "error",
                        "message" => "At least one song must be selected"
            ]);
        }

        // Validate song IDs are integers
        foreach ($songIds as $songId) {
            if (!is_numeric($songId)) {
                return response()->json([
                            "status" => "error",
                            "message" => "Invalid song ID format"
                ]);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                        "status" => "error",
                        "message" => $validator->errors()->first()
            ]);
        }

        $albumId = $request->album_id;

        // Kiểm tra album tồn tại và chưa được release
        $album = BomAlbum::where("id", $albumId)
                ->where("is_released", 0)
                ->first();
        if (!$album) {
            return response()->json([
                        "status" => "error",
                        "message" => "Album does not exist or has already been released"
            ]);
        }

        // Lấy tất cả bài hát theo song_ids và kiểm tra quyền sở hữu
        $songs = Bom::whereIn("id", $songIds)
                ->where("username", $user->user_name)
                ->whereNull("album_id") // Chỉ lấy bài hát chưa có album
                ->get();

        if ($songs->count() !== count($songIds)) {
            return response()->json([
                        "status" => "error",
                        "message" => "Some songs do not exist, do not belong to you, or are already in an album"
            ]);
        }

        // Lấy danh sách tên bài hát hiện tại trong album này
        $existingSongNames = Bom::where("album_id", $albumId)
                ->pluck("song_name")
                ->map(function($name) {
                    return strtolower(trim($name));
                })
                ->toArray();

        // Validate trùng tên trong request hiện tại
        $requestSongNames = $songs->pluck("song_name")
                ->map(function($name) {
                    return strtolower(trim($name));
                })
                ->toArray();

        // Kiểm tra trùng tên trong chính request này
        $duplicatesInRequest = array_diff_assoc($requestSongNames, array_unique($requestSongNames));
        if (!empty($duplicatesInRequest)) {
            $duplicateNames = [];
            $processedNames = [];

            foreach ($songs as $song) {
                $normalizedName = strtolower(trim($song->song_name));
                if (in_array($normalizedName, $processedNames)) {
                    $duplicateNames[] = $song->song_name;
                }
                $processedNames[] = $normalizedName;
            }

            return response()->json([
                        "status" => "error",
                        "message" => "Duplicate song names in request: " . implode(", ", array_unique($duplicateNames))
            ]);
        }

        // Validate trùng tên với bài hát đã có trong album này
        $conflictingSongs = array_intersect($requestSongNames, $existingSongNames);
        if (!empty($conflictingSongs)) {
            // Lấy tên bài hát gốc (không lowercase) để hiển thị
            $conflictingOriginalNames = $songs->filter(function($song) use ($conflictingSongs) {
                        return in_array(strtolower(trim($song->song_name)), $conflictingSongs);
                    })->pluck("song_name")->toArray();

            return response()->json([
                        "status" => "error",
                        "message" => "These song names already exist in this album: " . implode(", ", $conflictingOriginalNames)
            ]);
        }

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            $successCount = 0;
            $addedSongs = [];

            foreach ($songs as $song) {
                // Cập nhật bài hát
                $song->artist = $album->artist;  // Cập nhật artist theo album
                $song->album_id = $albumId;
                $song->save();

                $successCount++;
                $addedSongs[] = [
                    'id' => $song->id,
                    'song_name' => $song->song_name,
                    'artist' => $song->artist
                ];
            }

            DB::commit();

            return response()->json([
                        "status" => "success",
                        "message" => "Successfully added {$successCount} songs to album",
                        "data" => [
                            "added_songs" => $addedSongs,
                            "count" => $successCount
                        ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error adding songs to album: " . $e->getMessage());

            return response()->json([
                        "status" => "error",
                        "message" => "An error occurred while adding songs to album"
            ]);
        }
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
        Log::info("$user->user_name|BomController.albumAddArtist|request=" . json_encode($request->all()));
        if (!isset($request->artist_name)) {
            return response()->json(["status" => "success", "message" => "Artist can not be empty"]);
        }
        $artist_name = trim($request->artist_name);
        $edit_album_id = $request->edit_album_id;
        if ($edit_album_id) {
            $bomArtist = BomArtist::find($request->artist_id);
            if (!$bomArtist) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Album not found'
                ]);
            }

            $bomArtist->artist_name = $artist_name;
            $bomArtist->save();
            //sửa tên bài artist ở bảng bom_artits
            BomAlbum::where("id", $edit_album_id)->update(["artist" => $artist_name]);
            Bom::where("album_id", $edit_album_id)->update(["artist" => $artist_name]);
            return response()->json([
                        'status' => 'success',
                        'message' => 'Artist name updated successfully'
            ]);
        } else {
            $data = BomArtist::where("artist_name", $artist_name)->first();
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
            $releaseDate = new DateTime($request->release_date);
            $minDate = new DateTime();
            $minDate->modify('+7 days');

            if ($releaseDate < $minDate) {
                return response()->json([
                            "status" => "error",
                            "message" => "The release date must be at least 7 days from today."
                ]);
            }
            $album->release_date = $request->release_date;
        }

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
        $album->distro_status = $request->status;
        $album->distro_log = $request->log;
        if ($request->status == 5) {
            $album->distro_release_date = gmdate("Y-m-d", time());
            $album->is_released = 3;
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
                            //có spotify thì chuyển trạng thái thành online
                            $album->is_released = 5;
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
        } catch (Exception $e) {
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

    //kiểm tra xem artist đã có trên spotify chưa
    public function checkAlbumArtist(Request $request) {
        try {
            $artistName = $request->input('artist_name');

            // Basic validation
            if (empty(trim($artistName))) {
                return response()->json(['status' => 'invalid', 'message' => 'Artist name cannot be empty']);
            }

            if (strlen($artistName) < 2) {
                return response()->json(['status' => 'invalid', 'message' => 'Artist name is too short']);
            }

            if (preg_match('/[<>\/\\\\]/', $artistName)) {
                return response()->json(['status' => 'invalid', 'message' => 'Artist name contains invalid characters']);
            }

            // Call Spotify API to check if artist exists
            $encodedArtistName = urlencode($artistName);
            $url = "http://source.automusic.win/spotify/search/custom/{$encodedArtistName}/artist";

            $response = file_get_contents($url);
            $data = json_decode($response, true);

            // Check if artist exists on Spotify
            if (isset($data['artists']) && isset($data['artists']['items'])) {
                foreach ($data['artists']['items'] as $artist) {
                    if (strtolower($artist['name']) === strtolower($artistName)) {
                        return response()->json([
                                    'status' => 'invalid',
                                    'message' => 'This artist already exists on Spotify.'
                        ]);
                    }
                }
            }

            // If we get here, the artist name is valid
            return response()->json(['status' => 'valid', 'message' => 'Artist name is valid']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error checking artist: ' . $e->getMessage()]);
        }
    }

    public function getOrCreateArtist(Request $request) {
        Log::info("BomController.getOrCreateArtist|request=" . json_encode($request->all()));

        try {
            // Validate input
            if (!isset($request->username) || empty(trim($request->username))) {
                return response()->json([
                            "status" => "error",
                            "message" => "Username cannot be empty",
                            "data" => null
                ]);
            }

            if (!isset($request->artist_name) || empty(trim($request->artist_name))) {
                return response()->json([
                            "status" => "error",
                            "message" => "Artist name cannot be empty",
                            "data" => null
                ]);
            }

            $username = trim($request->username);
            $artist_name = trim($request->artist_name);

            // Artist name validation từ checkAlbumArtist
            if (strlen($artist_name) < 2) {
                return response()->json([
                            "status" => "error",
                            "message" => "Artist name is too short",
                            "data" => null
                ]);
            }

            if (preg_match('/[<>\/\\\\]/', $artist_name)) {
                return response()->json([
                            "status" => "error",
                            "message" => "Artist name contains invalid characters",
                            "data" => null
                ]);
            }

            // Call Spotify API to check if artist exists
            $encodedArtistName = urlencode($artist_name);
            $url = "http://source.automusic.win/spotify/search/custom/{$encodedArtistName}/artist";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            // Check if artist exists on Spotify
            if (isset($data['artists']) && isset($data['artists']['items'])) {
                foreach ($data['artists']['items'] as $artist) {
                    if (strtolower($artist['name']) === strtolower($artist_name)) {
                        return response()->json([
                                    "status" => "error",
                                    "message" => "This artist already exists on Spotify.",
                                    "data" => null
                        ]);
                    }
                }
            }

            // Kiểm tra xem artist đã tồn tại trong database chưa
            $existingArtist = BomArtist::where("artist_name", $artist_name)->first();

            if ($existingArtist) {
                // Nếu đã có, trả về thông tin đầy đủ
                return response()->json([
                            "status" => "success",
                            "message" => "Artist found",
                            "data" => [
                                "id" => $existingArtist->id,
                                "username" => $existingArtist->username,
                                "artist_name" => $existingArtist->artist_name,
                                "created" => $existingArtist->created
                            ]
                ]);
            } else {
                // Nếu chưa có, tạo mới
                $newArtist = new BomArtist();
                $newArtist->username = $username;
                $newArtist->artist_name = $artist_name;
                $newArtist->created = Utils::timeToStringGmT7(time());
                $newArtist->save();

                return response()->json([
                            "status" => "success",
                            "message" => "Artist created successfully",
                            "data" => [
                                "id" => $newArtist->id,
                                "username" => $newArtist->username,
                                "artist_name" => $newArtist->artist_name,
                                "created" => $newArtist->created
                            ]
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                        "status" => "error",
                        "message" => "Error processing artist: " . $e->getMessage(),
                        "data" => null
            ]);
        }
    }

    // tiến trình quét spotify link của album đã distribute
    public function scanDistributedAlbum() {
        try {
            // Lấy tất cả album có trạng thái Distributed (is_released = 3)
            $albums = BomAlbum::where("status", 1)
                    ->where("is_released", 3) // Trạng thái Distributed
                    ->where("release_date", "<=", now()->format('Y-m-d')) // Ngày hiện tại >= release_date
                    ->where("distro_status", 5)
                    ->get();

            if ($albums->isEmpty()) {
                return response()->json([
                            'success' => false,
                            'message' => 'No distributed albums found'
                                ], 404);
            }

            $totalAlbums = count($albums);
            $albumsUpdated = 0;
            $totalSongsScanned = 0;
            $totalSongsUpdated = 0;
            $results = [];

            // Xử lý từng album
            foreach ($albums as $album) {
                $albumResult = [
                    'album_id' => $album->id,
                    'album_title' => $album->title,
                    'songs_scanned' => 0,
                    'songs_updated' => 0,
                    'album_updated' => false
                ];
                error_log("scanDistributedAlbum $album->id");
                // Lấy tất cả bài hát trong album chưa có spotify_id
                $songs = Bom::where("album_id", $album->id)->whereNull("spotify_id")->get();
                $albumResult['songs_scanned'] = count($songs);
                $totalSongsScanned += count($songs);

                if (count($songs) > 0) {
                    $isUpdateAlbum = false;
                    $songsUpdated = 0;

                    // Xử lý từng bài hát trong album
                    foreach ($songs as $song) {
                        // Thực hiện quét Spotify bằng ISRC
                        if ($song->isrc != null) {
                            $spotifyInfo = $this->searchSpotify($song->isrc);

                            // Cập nhật spotify_id cho bài hát nếu tìm thấy
                            if ($spotifyInfo && isset($spotifyInfo["track_id"])) {
                                $song->spotify_id = $spotifyInfo["track_id"];
                                $song->log = $song->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " system update spotify_id=$song->spotify_id";
                                $song->save();
                                $songsUpdated++;

                                // Cập nhật thông tin Spotify cho album (chỉ lần đầu tiên)
                                if (!$isUpdateAlbum) {
                                    $album->spotify_info = json_encode((object) [
                                                "album_id" => $spotifyInfo["album_id"] ?? null,
                                                "artist_id" => $spotifyInfo["artist_id"] ?? null
                                    ]);

                                    // Chuyển trạng thái thành Online khi tìm thấy Spotify
                                    $album->is_released = 5;
                                    $album->save();
                                    $isUpdateAlbum = true;
                                    $albumsUpdated++;
                                }
                            }
                        }

                        // Thêm delay nhỏ để tránh rate limit
                        usleep(100000); // 100ms
                    }

                    $albumResult['songs_updated'] = $songsUpdated;
                    $albumResult['album_updated'] = $isUpdateAlbum;
                    $totalSongsUpdated += $songsUpdated;
                }

                $results[] = $albumResult;

                // Delay giữa các album để tránh overload
                usleep(200000); // 200ms
            }

            return response()->json([
                        'success' => true,
                        'message' => 'Distributed albums scan completed successfully',
                        'summary' => [
                            'total_albums_processed' => $totalAlbums,
                            'albums_updated_to_online' => $albumsUpdated,
                            'total_songs_scanned' => $totalSongsScanned,
                            'total_songs_updated' => $totalSongsUpdated
                        ],
                        'details' => $results
            ]);
        } catch (Exception $e) {
            Log::error('Distributed albums Spotify scan error: ' . $e->getMessage());

            return response()->json([
                        'success' => false,
                        'message' => 'Error scanning distributed albums: ' . $e->getMessage()
                            ], 500);
        }
    }

    // tiến trình quét view của nghệ sỹ đã distribute
    public function updateArtistStreams() {
        try {
            error_log("updateArtistStreams: Starting artist streams update...");

            // Lấy tất cả artist unique từ bảng bom_albums
            $artists = DB::table('bom_albums')
                    ->select('artist')
                    ->where('status', 1)
                    ->whereNotNull('artist')
                    ->where('artist', '!=', '')
                    ->distinct()
                    ->pluck('artist')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

            if (empty($artists)) {
                error_log("updateArtistStreams: No artists found to update.");
                return;
            }

            error_log("updateArtistStreams: Found " . count($artists) . " artists to update.");

            // Chia thành các batch để tránh URL quá dài
            $batches = array_chunk($artists, 30);
            $updatedCount = 0;

            foreach ($batches as $batchIndex => $batch) {
//                error_log("updateArtistStreams: Processing batch " . ($batchIndex + 1) . "/" . count($batches) . "...");

                try {
                    sort($batch); // Sắp xếp để đảm bảo thứ tự nhất quán
                    // Gọi API lấy tổng streams cho batch artists này
                    $apiUrl = 'https://distro.360promo.fm/api/artists/total-streams?names=' . urlencode(implode(',', $batch));
                    $apiResult = RequestHelper::callAPI2('GET', $apiUrl, []);

                    // Update last_update cho tất cả artist trong batch này
                    $currentTime = Utils::timeToStringGmT7(time());
                    foreach ($batch as $artistName) {
                        $artist = BomArtist::where('artist_name', $artistName)->first();

                        if ($artist) {
                            $artist->last_update = $currentTime;

                            // Nếu có trong API result thì update streams, không thì giữ nguyên
                            if (is_array($apiResult)) {
                                foreach ($apiResult as $item) {
                                    if (isset($item->artist) && isset($item->total_streams) && $item->artist === $artistName) {
                                        $artist->artist_total_streams = $item->total_streams;
//                                        error_log("updateArtistStreams: Updated {$artistName} - {$item->total_streams} streams");
                                        break;
                                    }
                                }
                            }

                            $artist->save();
                            $updatedCount++;
                        }
                    }

                    // Nghỉ 1 giây giữa các batch để tránh spam API
                    sleep(1);
                } catch (Exception $e) {
                    error_log("updateArtistStreams: Error processing batch " . ($batchIndex + 1) . ": " . $e->getMessage());
                    continue;
                }
            }

            error_log("updateArtistStreams: Artist streams update completed. Updated: {$updatedCount} artists.");
        } catch (Exception $e) {
            error_log("updateArtistStreams: Error updating artist streams: " . $e->getMessage());
        }
    }

    public function syncSunoLyric() {
        $processName = "syncSunoLyric";
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $datas = Bom::where("status", 1)->where("is_sync_lyric", 0)->where("source_type", "SUNO")->get();
            $i = 0;
            $total = count($datas);
            foreach ($datas as $data) {
                $hasLyric = "fail";
                $i++;
                $data->is_sync_lyric = 1;
                $lyric = $this->getSunoLyrics($data->song_id);
                if ($lyric != "") {
                    $data->lyric_text = $lyric;
                    $hasLyric = "succces";
                }
                $data->save();
                error_log("$i/$total syncSunoLyric $data->id $hasLyric");
                usleep(200000);
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }

    public function musicToText() {
        $processName = "musicToText";
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $datas = Bom::where("status", 1)
                            ->where("is_sync_lyric", 1)
                            ->where("source_type", "SUNO")
                            ->where("is_releasable", 1)
                            ->whereNotNull("lyric_text")
                            ->whereNull("lyric_raw")->take(2000)
                            ->orderBy("id", "desc")->get();
//        $datas = Bom::where("id",24042)->get();
            $total = count($datas);
            $i = 0;
            foreach ($datas as $data) {
                $timeStart = time();
                $i++;
                error_log("musicToText $i/$total $data->id");

                $input = (object) [
                            "file_url" => $data->source_id
                ];
                $lyricRaw = null;
                if ($data->lyric_raw == null || $data->lyric_raw == "" || $data->lyric_raw == "null") {
                    $lyricRaw = RequestHelper::callAPI2("POST", "http://ai.moonshots.vn/api/music-to-text", $input, array("Content-type: application/json", "platform: AutoWin"), 300000);
                } else {
//                error_log("data->lyric_raw ".$data->lyric_raw);
                    $lyricRaw = json_decode($data->lyric_raw);
                }

                if (empty($lyricRaw)) {
                    error_log("musicToText continue");
                    continue;
                }
                $data->lyric_raw = json_encode($lyricRaw);
                $data->save();
                error_log("musicToText $i/$total $data->id finished time=" . (time() - $timeStart) . "s");
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }

    public function makeLyricTimestamp() {
        $processName = "makeLyricTimestamp";
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $datas = Bom::where("status", 1)
                            ->where("is_sync_lyric", 1)
                            ->where("source_type", "SUNO")
                            ->where("is_releasable", 1)
                            ->whereNotNull("lyric_raw")
                            ->whereNull("lyric_pro")->take(500)
                            ->orderBy("id", "desc")->get();
//        $datas = Bom::where("id",24042)->get();
            $total = count($datas);
            $i = 0;
            foreach ($datas as $data) {
                $timeStart = time();
                $i++;
                error_log("makeLyricTimestamp $i/$total $data->id");

                $input = (object) [
                            "file_url" => $data->source_id
                ];
                $lyricRaw = null;
                if ($data->lyric_raw == null || $data->lyric_raw == "" || $data->lyric_raw == "null") {
                    $lyricRaw = RequestHelper::callAPI2("POST", "http://ai.moonshots.vn/api/music-to-text", $input, array("Content-type: application/json", "platform: AutoWin"), 300000);
                    $data->lyric_raw = json_encode($lyricRaw);
                    $data->save();
                    error_log("makeLyricTimestamp $i/$total $data->id saved lyric_raw");
                } else {
//                error_log("data->lyric_raw ".$data->lyric_raw);
                    $lyricRaw = json_decode($data->lyric_raw);
                }
//            Log::info("rs" . json_encode($result));
                $input2 = (object) [
                            "lyrics_raw" => $lyricRaw,
                            "lyrics_text" => $data->lyric_text
                ];
//            error_log(json_encode($lyricRaw));
                if (empty($lyricRaw)) {
                    error_log("makeLyricTimestamp continue");
                    continue;
                }

                $time = time();

                error_log("makeLyricTimestamp $i/$total $data->id call http://ai.moonshots.vn/api/lyrics-pro");
                $result2 = RequestHelper::callAPI2("POST", "http://ai.moonshots.vn/api/lyrics-pro", $input2, array("Content-type: application/json", "platform: AutoWin"), 300000);
                $data->lyric_pro = json_encode($result2);
                $data->save();
                error_log("makeLyricTimestamp $i/$total $data->id saved lyric_pro time=" . (time() - $time) . "s");
                if (isset($data->local_id) && isset($result2->lyricSync)) {
                    error_log("makeLyricTimestamp $i/$total $data->id saved lyric_text to https://cdn.soundhex.com/api/v1/timestamp/$data->local_id");
                    $lyricSyncText = json_encode($result2->lyricSync);
                    $lyricText = "";
                    foreach ($result2->lyricSync as $line) {
                        $lyricText .= $line->line . PHP_EOL;
                    }
                    $dataCdn = (object) [
                                "lyric" => $lyricText,
                                "lyric_sync" => $lyricSyncText,
                                "id" => $data->local_id
                    ];
//                error_log("dataCdn " . json_encode($dataCdn));
                    $rs = RequestHelper::callAPI2("PUT", "https://cdn.soundhex.com/api/v1/timestamp/$data->local_id/", $dataCdn, array('Content-Type: application/json'), 10000);
                    if (isset($rs->id)) {
                        $data->is_real_lyric = 1;
                        $data->save();
                    }
//                error_log("rs Cnd " . json_encode($rs));
                }
                $data->log = $data->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " system made lyric_pro";
                $data->save();
                error_log("makeLyricTimestamp $i/$total $data->id finished time=" . (time() - $timeStart) . "s");
//            return $data->lyric_pro;
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }

    public function getLyrics(Request $request) {
        try {
            $user = Auth::user();
            Log::info($user->user_name . '|BomController.getLyrics|request=' . json_encode($request->all()));

            // Validate input
            $validator = Validator::make($request->all(), [
                        'page_url' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed: ' . json_encode($validator->errors()));
                return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid URL provided'
                                ], 400);
            }

            $pageUrl = $request->input('page_url');

            // Ensure URL is from letras.com
            if (strpos($pageUrl, 'letras.com') === false) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'URL must be from letras.com'
                                ], 400);
            }

            // Remove trailing slash and add mais_acessadas.html
            $pageUrl = rtrim($pageUrl, '/') . '/mais_acessadas.html';

            // Prepare API request data
            $apiData = (object) [
                        'page_url' => $pageUrl
            ];

            // Call API using curl
            $apiUrl = 'http://152.53.83.116:8000/scrape/';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '', // Cho phép tất cả encoding
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($apiData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: text/plain, */*',
                    'Accept-Charset: UTF-8'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($curlError) {
                Log::error('Curl error: ' . $curlError);
                return response()->json([
                            'status' => 'error',
                            'message' => 'Connection error: ' . $curlError
                                ], 500);
            }

            if ($httpCode == 200 && $response !== false) {
                // Đảm bảo response là UTF-8
                if (!mb_check_encoding($response, 'UTF-8')) {
                    // Nếu không phải UTF-8, thử convert từ các encoding phổ biến
                    $encodings = ['ISO-8859-1', 'Windows-1252', 'ASCII'];
                    foreach ($encodings as $encoding) {
                        if (mb_check_encoding($response, $encoding)) {
                            $response = mb_convert_encoding($response, 'UTF-8', $encoding);
                            break;
                        }
                    }
                }

                // Làm sạch text - loại bỏ các ký tự điều khiển không cần thiết
                $response = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $response);

                Log::info('Lyrics retrieved successfully, length: ' . strlen($response));

                // Return JSON response với encoding UTF-8
                return response()->json([
                            'status' => 'success',
                            'lyrics' => $response,
                            'message' => 'Lyrics retrieved successfully'
                                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                Log::error("API call failed - HTTP Code: $httpCode");
                return response()->json([
                            'status' => 'error',
                            'message' => "Failed to retrieve lyrics. HTTP Code: $httpCode"
                                ], 500);
            }
        } catch (Exception $e) {
            Log::error('Error in getLyrics: ' . $e->getMessage() . ' | Line: ' . $e->getLine());

            return response()->json([
                        'status' => 'error',
                        'message' => 'An error occurred: ' . $e->getMessage()
                            ], 500);
        }
    }

    public function getArtistList(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BomController.getArtistList|request=" . json_encode($request->all()));

        try {
            $query = BomArtist::query();

            // Filter theo tên nghệ sỹ
            if ($request->has('artist_name') && !empty($request->artist_name)) {
                $query->where('artist_name', 'like', '%' . $request->artist_name . '%');
            }

            // Filter theo trạng thái youtube_claim
            if ($request->has('youtube_claim') && $request->youtube_claim !== '' && $request->youtube_claim !== null) {
                $youtubeClaim = $request->youtube_claim;
                // Chỉ filter khi có giá trị cụ thể (0 hoặc 1)
                if (in_array($youtubeClaim, ['0', '1', 0, 1])) {
                    $query->where('youtube_claim', (int) $youtubeClaim);
                }
            }

            // Sắp xếp
            $sortField = $request->get('sort', 'id');
            $sortDirection = $request->get('direction', 'desc');

            // Validate sort fields để tránh SQL injection
            $allowedSortFields = ['artist_name', 'artist_total_streams', 'id', 'username', 'created'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'id';
            }

            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $query->orderBy($sortField, $sortDirection);

            // Phân trang
            $perPage = $request->get('per_page', 20);
            if ($perPage > 100) {
                $perPage = 100; // Giới hạn tối đa
            }

            $artists = $query->paginate($perPage);

            // Format lại dữ liệu trả về
            $data = [
                'data' => $artists->items(),
                'current_page' => $artists->currentPage(),
                'last_page' => $artists->lastPage(),
                'per_page' => $artists->perPage(),
                'total' => $artists->total(),
                'from' => $artists->firstItem(),
                'to' => $artists->lastItem()
            ];

            return response()->json([
                        'status' => 'success',
                        'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error("$user->user_name|BomController.getArtistList|Error: " . $e->getMessage());
            return response()->json([
                        'status' => 'error',
                        'message' => 'Error retrieving artist list: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cập nhật trạng thái youtube_claim của artist
     */
    public function updateArtistYoutubeClaim(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BomController.updateArtistYoutubeClaim|request=" . json_encode($request->all()));

        try {
            if (!$request->has('artist_id') || !$request->has('youtube_claim')) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Artist ID and youtube_claim status are required'
                ]);
            }

            $artist = BomArtist::find($request->artist_id);
            if (!$artist) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Artist not found'
                ]);
            }

            // Validate youtube_claim value (should be 0 or 1)
            $youtubeClaim = $request->youtube_claim;
            if (!in_array($youtubeClaim, [0, 1, '0', '1'])) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid youtube_claim value'
                ]);
            }

            $artist->youtube_claim = (int) $youtubeClaim;
            $artist->save();

            return response()->json([
                        'status' => 'success',
                        'message' => 'Artist youtube claim status updated successfully',
                        'data' => [
                            'id' => $artist->id,
                            'artist_name' => $artist->artist_name,
                            'youtube_claim' => $artist->youtube_claim
                        ]
            ]);
        } catch (Exception $e) {
            Log::error("$user->user_name|BomController.updateArtistYoutubeClaim|Error: " . $e->getMessage());
            return response()->json([
                        'status' => 'error',
                        'message' => 'Error updating artist: ' . $e->getMessage()
            ]);
        }
    }

    public function syncToSoundhex() {
        $processName = "sync_soundhex";
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            // Lấy albums với điều kiện is_released = 5, spotify_info is not null và chưa sync hoặc sync lỗi
            $albums = BomAlbum::where('is_released', 5)
                    ->whereNotNull('spotify_info')
                    ->where('status', 1)
                    ->whereIn('is_soundhex', [0]) // Lấy albums chưa sync (0) hoặc sync lỗi (4)
                    ->get();
            error_log("syncToSoundhex: " . count($albums));
            $results = [];
            $errors = [];
            $i = 0;
            $total = count($albums);
            foreach ($albums as $album) {
                $i++;
                $syncResult = $this->syncSingleAlbumToSoundhex($album);
                error_log("syncToSoundhex: $i/$total $album->id $album->album_name");
                if ($syncResult['success']) {
                    $results[] = $syncResult['message'];
                } else {
                    $errors = array_merge($errors, $syncResult['errors']);
                }
            }

            ProcessUtils::unLockProcess($processName);
            $rs = [
                'status' => 'success',
                'message' => 'Sync to SoundHex completed',
                'results' => $results,
                'errors' => $errors,
                'total_albums' => count($albums),
                'synced' => count($results),
                'failed' => count($errors)
            ];
//            Log::info("syncToSoundhex result " . json_encode($rs));
            return response()->json($rs);
        } else {
            error_log("$processName is locked");
        }
    }

    /**
     * Sync một album duy nhất lên SoundHex
     * @param BomAlbum $album
     * @return array ['success' => bool, 'message' => string, 'errors' => array]
     */
    public function syncSingleAlbumToSoundhex($album) {
        try {
            Log::info("BomController.syncSingleAlbumToSoundhex|album_id=" . $album->id);
            $userAlbum = User::where("user_name", "$album->username")->first();
            $user_id = $userAlbum->soundhex_id;
            // Đánh dấu album đang sync
            $album->is_soundhex = 1;
            $album->save();

            // Định nghĩa domain và headers cho tất cả API calls
            $soundhexDomain = 'https://dev.soundhex.com';
//            $soundhexDomain = 'https://1c083d1d-baaf-4188-aa2e-0ebb35dc9970-00-6xjglm3f1xxb.sisko.replit.dev';
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer soundhex_webhook_secret_2025'
            ];

            // Validation trước khi gọi API
            $validationErrors = [];

            // Kiểm tra spotify_info
            $spotifyInfo = json_decode($album->spotify_info, true);
            if (!$spotifyInfo) {
                $validationErrors[] = "Invalid spotify_info";
            }

            // Kiểm tra thông tin album cơ bản
            if (empty($album->album_name)) {
                $validationErrors[] = "Album name is empty";
            }

            if (empty($album->artist)) {
                $validationErrors[] = "Artist name is empty";
            }

            if (empty($album->genre_name)) {
                $validationErrors[] = "Genre name is empty";
            }

            // Kiểm tra spotify_id của artist
            $artistSpotifyId = $spotifyInfo['artist_id'] ?? null;
            if (!$artistSpotifyId) {
                $validationErrors[] = "No artist spotify_id in spotify_info";
            }

            // Kiểm tra spotify_id của album
            $albumSpotifyId = $spotifyInfo['album_id'] ?? null;
            if (!$albumSpotifyId) {
                $validationErrors[] = "No album spotify_id in spotify_info";
            }

            // Kiểm tra tracks
            $tracks = Bom::where('album_id', $album->id)
                    ->where('status', 1)
                    ->orderBy('order_id')
                    ->get();

            if (count($tracks) == 0) {
                $validationErrors[] = "No tracks found";
            }

            // Kiểm tra tracks có thông tin cần thiết
            foreach ($tracks as $track) {
                if (empty($track->song_name)) {
                    $validationErrors[] = "Track {$track->id} has empty song_name";
                }
                if (empty($track->direct_link)) {
                    $validationErrors[] = "Track {$track->id} ({$track->song_name}) has no direct_link";
                }
            }

            // Nếu có lỗi validation, đánh dấu lỗi và log
            if (!empty($validationErrors)) {
                $errorMessage = "[STEP: Validation] Album {$album->id} validation failed: " . implode(", ", $validationErrors);
                $this->setSoundhexError($album, $errorMessage);

                return [
                    'success' => false,
                    'message' => '',
                    'errors' => [$errorMessage]
                ];
            }

            // Gọi API để tạo/lấy artist
            Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|STEP: Creating Artist");
            $artistData = [
                'user_id' => $user_id,
                'name' => $album->artist,
                'spotify_id' => $artistSpotifyId,
                'profile_image_url' => $album->album_cover ?? 'https://automusic.win/images/default-avatar.png'
            ];

            $artistResponse = RequestHelper::callAPI2('POST', $soundhexDomain . '/api/sync/artist', $artistData, $headers);
            Log::info("artistResponse: " . json_encode($artistResponse));
            if (!$artistResponse) {
                $errorMessage = "[STEP: Create Artist] Failed to create artist for album {$album->id}: No response from API";
                $this->setSoundhexError($album, $errorMessage);

                return [
                    'success' => false,
                    'message' => '',
                    'errors' => [$errorMessage]
                ];
            }

            $artistId = $artistResponse->id ?? null;

            if (!$artistId) {
                $errorMessage = "[STEP: Create Artist] Failed to get artist id for album {$album->id}. Response: " . json_encode($artistResponse);
                $this->setSoundhexError($album, $errorMessage);

                return [
                    'success' => false,
                    'message' => '',
                    'errors' => [$errorMessage]
                ];
            }

            Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|Artist created successfully with ID: {$artistId}");

            // Gọi API để tạo/lấy album
            Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|STEP: Creating Album");
            $albumData = [
                'user_id' => $user_id,
                'title' => $album->album_name,
                'spotify_id' => $albumSpotifyId,
                'artist_id' => $artistId,
                'cover_image_url' => $album->album_cover ?? 'https://automusic.win/images/default-avatar.png',
                'release_date' => $album->release_date ?? date('Y-m-d'),
            ];

            $albumResponse = RequestHelper::callAPI2('POST', $soundhexDomain . '/api/sync/album', $albumData, $headers);
            Log::info("albumResponse: " . json_encode($albumResponse));

            if (!$albumResponse) {
                $errorMessage = "[STEP: Create Album] Failed to create album {$album->id}: No response from API";
                $this->setSoundhexError($album, $errorMessage);

                return [
                    'success' => false,
                    'message' => '',
                    'errors' => [$errorMessage]
                ];
            }

            $albumId = $albumResponse->id ?? null;

            if (!$albumId) {
                $errorMessage = "[STEP: Create Album] Failed to get album id for album {$album->id}. Response: " . json_encode($albumResponse);
                $this->setSoundhexError($album, $errorMessage);

                return [
                    'success' => false,
                    'message' => '',
                    'errors' => [$errorMessage]
                ];
            }

            // Update soundhex_url nếu có custom_url
            if (isset($albumResponse->custom_url) && !empty($albumResponse->custom_url)) {
                $soundhexUrl = '/album/' . $albumResponse->custom_url;
                $album->soundhex_url = $soundhexUrl;
                $album->save();
                Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|Album URL saved: {$soundhexUrl}");
            }

            Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|Album created successfully with ID: {$albumId}");

            $trackErrors = [];
            $syncedTracks = 0;

            Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|STEP: Creating Tracks. Total tracks: " . count($tracks));

            foreach ($tracks as $index => $track) {
                $trackNum = $index + 1;
                Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|Creating track {$trackNum}/{" . count($tracks) . "}: {$track->song_name}");

                // Gọi API để tạo/lấy track
                $trackData = [
                    'user_id' => $user_id,
                    'title' => $track->song_name,
                    'spotify_id' => $track->spotify_id ?? '', // Spotify ID của track từ bảng bom
                    'artist_id' => $artistId,
                    'album_id' => $albumId,
                    'duration' => $track->duration_ms ? intval($track->duration_ms / 1000) : 180, // Convert ms to seconds
                    'file_url' => $track->direct_link ?? '',
                    'genre_name' => $album->genre_name, // Sử dụng genre_name từ album
                    'isrc' => $track->isrc ?? '',
                    'preview_url' => $track->direct_link ?? '',
                ];

                $trackResponse = RequestHelper::callAPI2('POST', $soundhexDomain . '/api/sync/track', $trackData, $headers);
//                    Log::info("trackResponse for track {$track->id}: ".json_encode($trackResponse));

                if (!$trackResponse) {
                    $trackErrors[] = "[STEP: Create Track] Track {$trackNum}/" . count($tracks) . " - Failed to create track {$track->id} ({$track->song_name}): No response from API";
                } else {
                    // Kiểm tra response có thành công không
                    if (isset($trackResponse->id) || isset($trackResponse->status) && $trackResponse->status === 'success') {
                        $syncedTracks++;
                        Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|Track {$trackNum} created successfully");
                    } else {
                        $trackErrors[] = "[STEP: Create Track] Track {$trackNum}/{" . count($tracks) . "} - Failed to create track {$track->id} ({$track->song_name}): Invalid response data. Response: " . json_encode($trackResponse);
                    }
                }

                // Thêm delay nhỏ để tránh spam API
                usleep(100000); // 100ms
            }

            // Đánh dấu album đã sync thành công
            $album->is_soundhex = 3;
            $album->save();

            Log::info("syncSingleAlbumToSoundhex|album_id={$album->id}|COMPLETED: Album synced successfully. Total tracks synced: {$syncedTracks}/" . count($tracks));

            // Nếu có track errors, log chúng vào distro_log nhưng vẫn coi là thành công
            if (!empty($trackErrors)) {
                $currentTime = gmdate("Y-m-d H:i:s", time() + 7 * 3600);
                $trackErrorMessage = "[STEP: Create Track] Some tracks failed: " . implode("; ", $trackErrors);

                if (empty($album->distro_log)) {
                    $album->distro_log = "$currentTime [SoundHex Sync Warning] $trackErrorMessage";
                } else {
                    $album->distro_log .= "\n$currentTime [SoundHex Sync Warning] $trackErrorMessage";
                }
                $album->save();

                Log::warning("syncSingleAlbumToSoundhex|album_id={$album->id}|Some tracks failed: " . implode("; ", $trackErrors));
            }

            return [
                'success' => true,
                'message' => "Successfully synced album {$album->id} ({$album->album_name}) - {$syncedTracks} tracks",
                'errors' => $trackErrors
            ];
        } catch (Exception $e) {
            $errorMessage = "[STEP: Exception] Error syncing album {$album->id}: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine();
            $this->setSoundhexError($album, $errorMessage);

            Log::error("syncSingleAlbumToSoundhex exception for album {$album->id}: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
            return [
                'success' => false,
                'message' => '',
                'errors' => [$errorMessage]
            ];
        }
    }

    /**
     * Đánh dấu album sync lỗi và append log vào distro_log
     * @param BomAlbum $album
     * @param string $errorMessage
     */
    private function setSoundhexError($album, $errorMessage) {
        $currentTime = gmdate("Y-m-d H:i:s", time() + 7 * 3600);

        // Đánh dấu sync lỗi
        $album->is_soundhex = 4;

        // Append log vào distro_log
        if (empty($album->distro_log)) {
            $album->distro_log = "$currentTime [SoundHex Sync Error] $errorMessage";
        } else {
            $album->distro_log .= "\n$currentTime [SoundHex Sync Error] $errorMessage";
        }

        $album->save();

        Log::error("setSoundhexError for album {$album->id}: $errorMessage");
    }

}
