<?php

namespace App\Http\Controllers;

use App\Common\Network\EpidemicSoundHelper;
use App\Common\Network\RequestHelper;
use App\Common\Process\ProcessUtils;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Bom;
use App\Http\Models\BomGroups;
use App\Http\Models\Noclaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use TheSeer\Tokenizer\Exception;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function response;
use function view;

class NoclaimController extends Controller {

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|NoclaimController.index|request=' . json_encode($request->all()));
        $datas = Noclaim::where("status", 1);
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
            $request['sort'] = 'last_used';
            $request['order'] = 'asc';
            $queries['sort'] = 'last_used';
            $queries['order'] = 'asc';
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
        return view('components.noclaim', ["datas" => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            "genres" => $this->loadChannelGenre($request),
            'channelSubGenre' => $this->loadChannelSubGenre($request),
        ]);
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

 

    public function noclaimRemove(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|NoclaimController.noclaimRemove|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $noclaim = Noclaim::where("id", $request->boom_id)->first();
        if (!$request->is_admin_music) {
            if ($user->user_name != $noclaim->username) {
                return array("status" => "error", "message" => "You do not have permission to do this action");
            }
        }
        if ($noclaim) {
            $noclaim->status = 0;
            $log = $noclaim->log;
            $log .= PHP_EOL . "$curr $user->user_name removed";
            $noclaim->log = $log;
            $noclaim->save();
            return array("status" => "success", "message" => "Success", "boom" => $noclaim);
        }
        return array("status" => "error", "message" => "Remove failed");
    }

    public function noclaimMakeLyric(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|NoclaimController.boomMakeLyric|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $noclaim = Noclaim::where("id", $request->boom_id)->first();
        if ($noclaim) {
            $log = $noclaim->log;
            $log .= PHP_EOL . "$curr $user->user_name request make lyric";
            $noclaim->log = $log;
            $noclaim->lyric = 2;
            $noclaim->save();

            return array("status" => "success", "url" => $noclaim->direct_link,
                "username" => $user->user_name,
                "title" => $noclaim->song_name,
                "artist" => $noclaim->artist,
                "id" => $noclaim->id);
        }
        return array("status" => "error", "message" => "Remove failed");
    }

    public function noclaimFinishMakeLyric(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|NoclaimController.noclaimFinishMakeLyric|request=' . json_encode($request->all()));
        $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $noclaim = Noclaim::where("id", $request->boom_id)->first();
        if ($noclaim) {
            $noclaim->lyric = 1;
            $log = $noclaim->log;
            $log .= PHP_EOL . "$curr $user->user_name finished make lyric";
            $noclaim->log = $log;
            $noclaim->save();
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

    //danh sach bai hat bom theo genre
    public function noclaimSongs(Request $request) {
        $bom = Noclaim::where("genre", $request->genre)->where("status", 1)->where("lyric", 1)->where("sync", 1)->orderByRaw('RAND()')->get(["id", "genre", "song_name", "artist", "deezer_artist_id", "deezer_id", "local_id", "social"]);
        return $bom;
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
                $log = $bom->log;
                $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $request->username made video from studio";
                $bom->log = $log;
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

}
