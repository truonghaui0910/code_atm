<?php

namespace App\Http\Controllers;

use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\MusicSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;


class MusicSourceController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MusicSourceController.index');

        return view('components.musicsource', [
//            'list_channel' => $lstChannel,
        ]);
    }

    public function ajax(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|MusicSourceController.ajax|request=' . json_encode($request->all()));
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MusicSourceController.store|request=' . json_encode($request->all()));
        $content = array();
        $separate = Config::get('config.separate_text');
        try {

            if (!isset($request->link)) {
                array_push($content, trans('label.messageEnterLink'));
                return array('status' => "danger", 'content' => "Cưng phải nhập link");
            }
            if (!isset($request->genre)) {
                array_push($content, trans('label.messageEnterLink'));
                return array('status' => "danger", 'content' => "Cưng phải nhập genre");
            }
            if (!isset($request->country)) {
                array_push($content, trans('label.messageEnterLink'));
                return array('status' => "danger", 'content' => "Cưng phải nhập genre");
            }
            $genre = $request->genre;
            $country = $request->country;
            $txtSource = str_replace(array("\r\n", "\n"), $separate, trim($request->link));
            $arraySource = explode($separate, $txtSource);
            $link_type = 1;
            $song_type = 1;
            foreach ($arraySource as $source) {
                $pos = strpos($source, 'spotify.com');
                if ($pos === false) {
                    $link_type = 1;
                    $check = YoutubeHelper::processLink($source);
                    if ($check['type'] == '1') {
                        //playlist
                        $song_type = 1;
                    } else if ($check['type'] == '2') {
                        //channel
                        $song_type = 3;
                    } else if ($check['type'] == '3') {
                        //video
                        $song_type = 2;
                    }
                } else {
                    $link_type = 2;
                    $pos2 = strpos($source, 'spotify.com/playlist');
                    if ($pos2 != false) {
                        $song_type = 1;
                    }
                    $pos3 = strpos($source, 'spotify.com/track');
                    if ($pos3 != false) {
                        $song_type = 2;
                    }
                }
                $check = MusicSource::where("link", $request->link)->first();
                if (!$check) {
                    $musicSource = new MusicSource();
                    $musicSource->genre = $genre;
                    $musicSource->country = $country;
                    $musicSource->link = $source;
                    $musicSource->link_type = $link_type;
                    $musicSource->song_type = $song_type;
                    $musicSource->create_time = date("Y-m-d H:i:s", time());
                    $musicSource->save();
                    Log::info(json_encode($musicSource));
                } else {
                    error_log("$request->link exists");
                }
            }
            error_log("total " . count($arraySource) . " link");
            return array('status' => "success", 'content' => "Thêm thành công " . count($arraySource) . " link");
        } catch (\QueryException $exc) {
            Log::info("LOI INSERT===== " . $exc);

            array_push($content, trans('label.message.error'));
            return array('status' => "danger", 'content' => $content);
        }
    }

    public function show($id) {
        //
    }

    public function edit($id) {
        //
    }

    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        //
    }

}
