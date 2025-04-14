<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Network\TiktokHelper;
use App\Common\Utils;
use App\Http\Models\TiktokPlaylist;
use App\Http\Models\TiktokPlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiktokController extends Controller {

    public function crawlPlaylist() {
        $locker = new Locker(1209);
        $locker->lock();
        $count= 0;
        $playlists = TiktokPlaylist::where("status", 1)->get();
        foreach ($playlists as $playlist) {
            $playlistData = TiktokHelper::getPlaylistData($playlist->playlist_id);
            if ($playlistData != null && $playlistData != "") {
                $temps = json_decode($playlistData);
                $musicList = !empty($temps->music_list) ? $temps->music_list : [];
                if (count($musicList) > 0) {
                    //xóa list cũ
                    TiktokPlaylistSong::where("playlist_id", $playlist->playlist_id)->delete();
                    foreach ($musicList as $music) {
                        if (!empty($music->artists[0]->nick_name)) {
                            $artist = $music->artists[0]->nick_name;
                        }
                        if (!empty($music->cover_large->url_list[0])) {
                            $cover = $music->cover_large->url_list[0];
                        }
                        if (!empty($music->play_url->uri)) {
                            $directLink = $music->play_url->uri;
                        }
                        if (!empty($music->title)) {
                            $songName = $music->title;
                        }
                        $insert = new TiktokPlaylistSong();
                        $insert->playlist_id = $playlist->playlist_id;
                        $insert->playlist_name = $playlist->cate;
                        $insert->song_name = $songName;
                        $insert->artist = $artist;
                        $insert->cover_image = $cover;
                        $insert->direct_link = $directLink;
                        $insert->created = Utils::timeToStringGmT7(time());
                        $insert->save();
                        $count++;
                    }
                }
            }
        }
        return $count;
    }
    
    public function getListPlaylist(){
        $datas  = TiktokPlaylist::where("status",1)->pluck("cate","playlist_id")->toArray();
        return $datas;
    }

    public function getPlaylist(Request $request) {
        $datas = TiktokPlaylistSong::whereRaw("1=1");
        if (isset($request->playlist_id)) {
            $datas = $datas->where("playlist_id", $request->playlist_id);
        }
        $datas = $datas->get();
        return response()->json($datas);
    }

}
