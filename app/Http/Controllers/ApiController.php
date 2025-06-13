<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Logger;
use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Process\ProcessUtils;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\AccountInfoViews;
use App\Http\Models\ApiManager;
use App\Http\Models\AthenaDataSync;
use App\Http\Models\AthenaPromoSync;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignCard;
use App\Http\Models\CampaignEndScreen;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\ChannelComment;
use App\Http\Models\CrossPost;
use App\Http\Models\FuckLabelArtists;
use App\Http\Models\LockProcess;
use App\Http\Models\MusicHexa;
use App\Http\Models\Notification;
use App\Http\Models\RebrandChannelCmd;
use App\Http\Models\Spotifymusic;
use App\Http\Models\SpotifyMusicPlaylist;
use App\Http\Models\Strikes;
use App\Http\Models\Upload;
use App\Http\Models\VideoDaily;
use App\Http\Models\VideoInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function response;
use function storage_path;

class ApiController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    function addChannel(Request $request) {
        Log::info('|addChannel|request=' . json_encode($request->all()));
        $channel = AccountInfo::where("chanel_id", $request->channel_id)->first();
        $time = date("Ymd", time() + 7 * 3600);
        $api_type = 0;
        if (isset($request->api_type)) {
            $api_type = $request->api_type;
        }
        $apiManager = ApiManager::where("type", $api_type)->first();
        $newCode = [];
        $objectCode = (object) [
                    "name" => $apiManager->net,
                    "type" => $api_type,
                    "time" => $time,
                    "channel_code" => $request->channel_code
        ];
        if ($channel) {
            if ($channel->channel_code_all == null) {
                $channel->channel_code_all = '[]';
            }
            $channelCodeAll = json_decode($channel->channel_code_all);
            foreach ($channelCodeAll as $code) {
                if ($code->type != $api_type) {
                    $newCode[] = $code;
                }
            }

            $newCode[] = $objectCode;
            $channel->channel_code_all = json_encode($newCode);
            $channel->api_type = $api_type;
            $channel->save();
        } else {
            $insert = new AccountInfo();
            $insert->gmail = $request->gmail;
            $insert->channel_id = $request->channel_id;
            $insert->channel_name = $request->channel_name;
            $channel->channel_code_all = json_encode($newCode);
            $insert->api_type = $api_type;
            $insert->save();
        }
        $apiManager->count = $apiManager->count + 1;
        $apiManager->save();
//        $url = "http://autoseo.win/add/orfium/api";
//        $req = [
//            "channel_id" => $request->channel_id,
//            "$channelCodeType" => $request->channel_code,
//            "api_type" => $api_type
//        ];
//        RequestHelper::callAPI("GET", $url, $req);
    }

    public function syncmusicfriday() {
        $locker = new Locker(999);
        $locker->lock();
        error_log(__FUNCTION__);
        $datas = Spotifymusic::where("status_playlist", 0)->limit(500)->get();
        $i = 0;
//        error_log("syncmusicfriday: ".count($datas));
        if (count($datas) > 0) {
            foreach ($datas as $data) {
                $url = "https://autoseo.win/syncmusicfriday";
                $req = array(
                    "isrc" => $data->isrc,
                    "name" => $data->name,
                    "artists" => $data->artists,
                );
                $response = RequestHelper::callAPI("POST", $url, $req);
                $i++;
                if ($response == "1") {
                    error_log($i . '/' . count($datas) . " sync success  $data->isrc");
                } else {
                    error_log($i . '/' . count($datas) . " sync fail $data->isrc");
                }
                $data->status_playlist = 1;
                $data->save();
            }
            RequestHelper::get("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Sync Music Friday " . count($datas) . " songs");
        } else {
//            error_log("no data music friday");
        }
    }

    public function scanchannelmusic(Request $request) {

        error_log(__FUNCTION__);
        $startTime = time();
        $numberThreadInit = 10;
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }
        for ($i = 0; $i < $numberThreadInit; $i++) {
            if ($i == $threadId) {
                $locker = new Locker(intval("998$i"));
                $locker->lock();
            }
        }
        RequestHelper::getUrl("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Start scan channel 6 floor thread-$threadId", 0);
        $channels = AccountInfo::where(DB::raw("id % $numberThreadInit"), $threadId)->whereNotNull("otp_key")->where("del_status", 0)->get();
//        $channels = AccountInfo::where(DB::raw("id % $numberThreadInit"), $threadId)->whereIn("is_music_channel", [1, 2])->where("del_status", 0)->get();
//        $channels = AccountInfo::where("id", 319038)->get();
//        $channels = AccountInfo::where("tags", "like", "%MAIN%")->orWhere("tags", "like", "%BIG%")->get();
//        $channels = AccountInfo::where(DB::raw("id % $numberThreadInit"), $threadId)->whereNotIn("is_music_channel", [1, 2])->where("emp_code","5sCtGqqJxSfUKOKW1497079684")->get();

        $curr = time();
        $j = 0;
        $total = count($channels);
        foreach ($channels as $channel) {
            $j++;
            $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
//            Log::info(json_encode($infoChannel));
            if ($infoChannel["status"] == 2) {
                for ($t = 0; $t < 5; $t++) {
                    error_log("scanchannelmusic retry $channel->chanel_id");
                    $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
                    if ($infoChannel["status"] == 1) {
                        break;
                    }
                }
            }
            if ($infoChannel["status"] == 1) {
                if ($infoChannel["channelName"] != null && $infoChannel["channelName"] != '') {
                    $channel->chanel_name = $infoChannel["channelName"];
                }
                if ($infoChannel["handle"] != null && $infoChannel["handle"] != '') {
                    $channel->handle = $infoChannel["handle"];
                }
            }
            $currentViews = intval($infoChannel["views"]);
            $currentSub = intval($infoChannel["subscribers"]);

            $status = $infoChannel["status"];
            //2025/01/13 nếu scan mà kênh die thì đánh dấu lại,3 lần thì báo kênh die thật
            $countScanDie = 0;
            if ($status == 0) {
                $status = 1;
                $countScanDie = 1;
            } elseif ($status == 2) {
                //status = 2 => bị lỗi không scan được chứ ko phải là kênh die
                $status = 1;
            }


            $currday = gmdate("Ymd", time() + 7 * 3600);
            //lấy thời gian luu gần so với ngày hiện tại nhất để so sánh tính ra view tăng
            $lastData = DB::table('accountinfo_views')
                    ->where('channel_id', $channel->chanel_id)
                    ->where('date', '<', $currday)
                    ->orderBy('date', 'desc')
                    ->first();
            $lastViews = 0;
            $lastInc = 0;
            if ($lastData) {
                $lastViews = $lastData->current_views;
                $lastInc = $lastData->increasing;
            }
//            error_log("$lastData->date lastInc=$lastViews lastInc=$lastInc");

            $cunrrentData = AccountInfoViews::where("date", $currday)->where("channel_id", $channel->chanel_id)->first();
            if (!$cunrrentData) {
                $cunrrentData = new AccountInfoViews();
                $cunrrentData->date = $currday;
                $cunrrentData->channel_id = $channel->chanel_id;
                $cunrrentData->first_views = $currentViews;
                $cunrrentData->current_views = $currentViews;
                $cunrrentData->increasing = $lastViews == 0 ? 0 : ($currentViews - $lastViews);
                $cunrrentData->log_views = gmdate("H:i:s", time() + 7 * 3600) . " $currentViews views, $cunrrentData->increasing inc, $currentSub sub";
                $cunrrentData->created = Utils::timeToStringGmT7(time());
                $cunrrentData->updated = time();
                $cunrrentData->updated_text = Utils::timeToStringGmT7(time());
                $cunrrentData->save();
            } else {
                $cunrrentData->current_views = $currentViews;
                $cunrrentData->increasing = $lastViews == 0 ? 0 : (($currentViews - $lastViews));
                $cunrrentData->updated = time();
                $cunrrentData->updated_text = Utils::timeToStringGmT7(time());
                $cunrrentData->log_views = gmdate("H:i:s", time() + 7 * 3600) . " $currentViews views, $cunrrentData->increasing inc, $currentSub sub" . PHP_EOL . $cunrrentData->log_views;
                $cunrrentData->save();
            }
            //2024/02/12 sáng confirm nếu views tăng mà  =0 thì ko update vào increasing, để hiện kết quả của hôm trước.
            if ($cunrrentData->increasing > 0) {
                $channel->increasing = $cunrrentData->increasing;
            }

            //so sánh với increasing của ngày hôm trước
            if ($channel->intro_outro != null) {
                $stats = json_decode($channel->intro_outro);
                //2024/02/12 sáng confirm nếu views tăng mà = 0 thì ko update vào increasing, để hiện kết quả của hôm trước.
                if ($cunrrentData->increasing > 0) {
                    if ($lastInc == 0) {
                        $stats->daily_inc_per = 0;
                    } else {
                        $stats->daily_inc_per = round((($cunrrentData->increasing - $lastInc) / $lastInc) * 100);
                    }
                    $stats->daily_inc_view = $cunrrentData->increasing;
                    $stats->last_inc_view = $lastInc;
                }
                $stats->daily_scan_time = time();
                if ($infoChannel["status"] == 1) {
                    $stats->count_scan_die = 0;
                } else {
                    $stats->count_scan_die = isset($stats->count_scan_die) ? $stats->count_scan_die + $countScanDie : $countScanDie;
                }
            } else {
                $stats = (object) [
                            "daily_inc_view" => $cunrrentData->increasing,
                            "last_inc_view" => $lastInc,
                            "daily_inc_per" => 0,
                            "daily_scan_time" => time(),
                            "count_scan_die" => $countScanDie,
                ];
            }
            $channel->intro_outro = json_encode($stats);


            //đưa ra thông báo
            if ($stats->daily_inc_per >= 50 && $stats->daily_inc_view >= 5000) {
                //không thông báo các kênh mà đã có thông báo chưa hết hạn
                $notify = Notification::where('noti_id', $channel->id)->where("end_date", ">", time())->first();
                if (!$notify) {
                    $notify = new Notification();
                    $notify->platform = "automusic";
                    $notify->group = "channel";
                    $notify->email = "";
                    $notify->noti_id = $channel->id;
                    $notify->start_date = time();
                    $notify->end_date = time() + 3 * 86400;
                    $notify->role = 20;
                    $pos = strripos($channel->user_name, '_');
                    $temp = substr($channel->user_name, 0, $pos);
                    $notify->username = $temp;
                    $notify->create_time = Utils::timeToStringGmT7(time());
                    $notify->type = 'notify';
                    $notify->action_type = "read";

                    $notify->content = "Channel $channel->chanel_name has increased by " . number_format($channel->increasing, 0, '.', ',') . " views";
                    $notify->action = "/channelmanagement?c1=$channel->id";
                    $notify->save();
                }
            }
//            //scan so luong playlist
//            $playlists = YoutubeHelper::getListPLaylistWakeupHappy($channel->chanel_id);
//            if (count($playlists) > 0) {
//                $channel->playlist_count = count($playlists);
//            }
            //3 lần scan báo kênh die thì set = die thật
            if ($stats->count_scan_die >= 3) {
                $channel->status = 0;
            } else {
                $channel->status = $status;
            }
            $channel->view_count = $currentViews;
            $channel->subscriber_count = $currentSub;
            $channel->gmail_log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . ' scanchannelmusic' . $threadId;
            //thêm link avatar
            if (!Utils::containString($channel->channel_clickup, "https://yt3.ggpht.com/")) {
                $thumb = "";
                $thumbItem = RequestHelper::getUrl("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channel->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyAnfl6tyoZukXLFuZMoqd20dpZlUk1y0J8");
                if ($thumbItem != null && $thumbItem != "") {
                    $items = json_decode($thumbItem);
                    if (!empty($items->items[0]->snippet->thumbnails->medium->url)) {
                        $thumb = $items->items[0]->snippet->thumbnails->medium->url;
                        $channel->channel_clickup = $thumb;
                    }
                }
            }
            $channel->save();
            error_log("scanchannelmusic-$threadId $j/$total $channel->chanel_id sub=$currentSub v=$currentViews inc=$cunrrentData->increasing ");
        }
        $totalTime = time() - $startTime;
        RequestHelper::getUrl("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Finish scan channel 6 floor thread-$threadId $totalTime s,$total channels ", 0);
    }

    //xóa 1 ngày đã scan count video rồi để scan lại
    public function roolBackScanChannelMusic() {
        $dateDelete = '04-19-2021';
        $dateGetBack = '04-18-2021';
        $channels = AccountInfo::where("is_music_channel", 1)->where("video_count", 0)->get();
        foreach ($channels as $channel) {
            $countVideos = array();
            if ($channel->count_video != null) {
                $countVideos = json_decode($channel->count_video);

                if (count($countVideos) > 0) {
                    foreach ($countVideos as $index => $countVideo) {

                        if ($countVideo->time == $dateGetBack) {
                            $channel->video_count = $countVideo->count;
                            $channel->save();
                        }
                        if ($countVideo->time == $dateDelete) {
                            unset($countVideos[$index]);
                            $channel->count_video = json_encode($countVideos);
                            $channel->save();
                        }
                    }
                }
            }
        }
    }

    //xóa 1 ngày đã scan view rồi để scan lại
    public function roolBackScanViewsChannelMusic() {
        $dateDelete = '04-19-2021';
        $dateGetBack = '04-18-2021';
        $channels = AccountInfo::where("is_music_channel", 1)->where("increasing", '<=', 0)->get();
        foreach ($channels as $channel) {
            $viewDetails = array();
            if ($channel->view_detail != null) {
                $viewDetails = json_decode($channel->view_detail);

                if (count($viewDetails) > 0) {
                    foreach ($viewDetails as $index => $viewDetail) {
//                        Log::info(gmdate("m-d-Y", $viewDetail->time + 7 *3600));
                        $check = gmdate("m-d-Y", $viewDetail->time + 7 * 3600);
                        if ($check == $dateGetBack) {
                            $channel->increasing = $viewDetail->daily;
                            $channel->view_count = $viewDetail->view;
                            $channel->save();
                        }
                        if ($check == $dateDelete) {
                            unset($viewDetails[$index]);
                            $channel->view_detail = json_encode($viewDetails);
                            $channel->save();
                        }
                    }
                }
            }
        }
    }

    //hàm lấy list kênh theo user dùng cho studio.automusic.win
    public function getlistchannelbyuser(Request $request) {
//        DB::enableQueryLog();
        Log::info('ApiController.getlistchannelbyuser|request=' . json_encode($request->all()));
        $username = $request->username;
        $user = User::where("user_name", $username)->first();
        $arrayRole = explode(",", $user->role);
        if ($user) {
            if (in_array('19', $arrayRole)) {

                $datas = AccountInfo::where("is_automusic_v2", 1)->get(["user_name", "chanel_id", "chanel_name", "note", "version", "tags"]);
//            Log::info(DB::getQueryLog());
            } else {
                $datas = AccountInfo::where("user_name", $user->user_code)->where("is_automusic_v2", 1)->get(["user_name", "chanel_id", "chanel_name", "note", "version", "tags"]);
            }
//            Log::info(json_encode($datas));
            return $datas;
        }

        return array();
    }

    //dùng cho studio
    public function getChannelInfoByChannelId(Request $request) {
        Log::info('ApiController.getChannelInfoByChannelId|request=' . json_encode($request->all()));
//        DB::enableQueryLog();
        if ($request->is_admin_music) {
            $result = DB::table('accountinfo')
                    ->join('users', 'accountinfo.user_name', '=', 'users.user_code')
                    ->where('accountinfo.chanel_id', '=', $request->channel_id)
                    ->select('accountinfo.chanel_name', 'accountinfo.note', 'users.social_id')
                    ->first();
        } else {
            $result = DB::table('accountinfo')
                    ->join('users', 'accountinfo.user_name', '=', 'users.user_code')
                    ->where('accountinfo.chanel_id', '=', $request->channel_id)
                    ->where('accountinfo.user_name', '=', $request->usercode)
                    ->select('accountinfo.chanel_name', 'accountinfo.note', 'users.social_id')
                    ->first();
        }
//        Log::info(DB::getQueryLog());
        return response()->json($result);
    }

    public function getZipfile(Request $request) {
        DB::enableQueryLog();
        Log::info('getZipfile|request=' . json_encode($request->all()));
        $channel_id = $request->channel_id;
        if (Utils::containString($channel_id, "?")) {
            $channel_id = explode("?", $channel_id)[0];
        }
        Log::info("getZipfile $channel_id");
        $export_date = $request->export_date;
        $zip_contents = base64_decode($request->zip);
        $file = $request->type . '_' . uniqid() . '.zip';
        file_put_contents($file, $zip_contents);
        $zip = zip_open($file);
        if ($zip) {
            while ($zip_entry = zip_read($zip)) {
                $name = zip_entry_name($zip_entry);
                Log::info("Name: " . zip_entry_name($zip_entry) . "");
                if ($request->type == 'card' || $request->type == 'card_type') {
                    if ($name == 'Table data.csv' || $name == 'Dữ liệu trong bảng.csv') {
                        // Open directory entry for reading
                        if (zip_entry_open($zip, $zip_entry)) {
                            $contents = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            $row = str_getcsv($contents, "\n");
//                        Log::info(json_encode($row));
                            $length = count($row);
                            for ($i = 0; $i < $length; $i++) {
                                if ($i >= 2) {
                                    $data = str_getcsv($row[$i], ",");
                                    if (!empty($data[1]) && $data[1] > 0) {
                                        $video_id = null;
                                        $report_type = 1;
                                        $temp = explode(".", $data[0]);
                                        if (count($temp) > 1) {
                                            $video_id = $temp[1];
                                            $card_id = $temp[0];
                                            $card = CampaignCard::where("export_date", $export_date)->where("video_id", $video_id)->where("card_id", $card_id)->where("report_type", 1)->first();
                                        } else {
                                            $report_type = 2;
                                            $card_id = $temp[0];

                                            $card = CampaignCard::where("export_date", $export_date)->where("card_id", $card_id)->where("report_type", 2)->first();
                                        }
                                        if ($card) {
                                            $card->card_teaser_clicks = $data[1] != "" ? $data[1] : 0;
                                            $card->per_card_teaser_clicks = $data[2] != "" ? $data[2] : 0;
                                            $card->card_clicks = $data[3] != "" ? $data[3] : 0;
                                            $card->per_card_clicks = $data[4] != "" ? $data[4] : 0;
                                            $card->save();
                                        } else {
                                            $card = new CampaignCard();
                                            $card->export_date = $export_date;
                                            $card->channel_id = $channel_id;
                                            $card->report_type = $report_type;
                                            $card->video_id = $video_id;
                                            $card->card_id = $card_id;
                                            $card->card_teaser_clicks = $data[1] != "" ? $data[1] : 0;
                                            $card->per_card_teaser_clicks = $data[2] != "" ? $data[2] : 0;
                                            $card->card_clicks = $data[3] != "" ? $data[3] : 0;
                                            $card->per_card_clicks = $data[4] != "" ? $data[4] : 0;
                                            $card->save();
//                                            Log::info(DB::getQueryLog());
                                        }
                                    }
                                }
                            }
                            zip_entry_close($zip_entry);
                        }
                    }
                } else if ($request->type == 'end_screen' || $request->type == 'end_screen_type') {
                    if ($name == 'Table data.csv' || $name == 'Dữ liệu trong bảng.csv') {
                        // Open directory entry for reading
                        if (zip_entry_open($zip, $zip_entry)) {
                            $contents = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            $row = str_getcsv($contents, "\n");
//                        Log::info(json_encode($row));
                            $length = count($row);
                            for ($i = 0; $i < $length; $i++) {
                                if ($i >= 2) {
                                    $data = str_getcsv($row[$i], ",");
                                    if (!empty($data[1]) && $data[1] > 0) {
                                        $video_id = null;
                                        $report_type = 1;
                                        $temp = explode(".", $data[0]);
                                        if (count($temp) > 1) {
                                            $video_id = $temp[1];
                                            $es_id = $temp[0];
                                            $card = CampaignEndScreen::where("export_date", $export_date)->where("video_id", $video_id)->where("es_id", $es_id)->where("report_type", 1)->first();
                                        } else {
                                            $report_type = 2;
                                            $es_id = $temp[0];
                                            $card = CampaignEndScreen::where("export_date", $export_date)->where("es_id", $es_id)->where("report_type", 2)->first();
                                        }
                                        if ($card) {
                                            $card->es_shown = $data[1] != "" ? $data[1] : 0;
                                            $card->es_clicks = $data[2] != "" ? $data[2] : 0;
                                            $card->per_es = $data[3] != "" ? $data[3] : 0;
                                            $card->save();
                                        } else {
                                            $card = new CampaignEndScreen();
                                            $card->export_date = $export_date;
                                            $card->channel_id = $channel_id;
                                            $card->report_type = $report_type;
                                            $card->video_id = $video_id;
                                            $card->es_id = $es_id;
                                            $card->es_shown = $data[1] != "" ? $data[1] : 0;
                                            $card->es_clicks = $data[2] != "" ? $data[2] : 0;
                                            $card->per_es = $data[3] != "" ? $data[3] : 0;
                                            $card->save();
                                        }
                                    }
                                }
                            }
                            zip_entry_close($zip_entry);
                        }
                    }
                }
            }
            zip_close($zip);
        }
        unlink($file);
        return 1;
    }

    //scan fuck label
    public function scanchannelmusiclicences() {
        $channels = AccountInfo::where("is_music_channel", 1)->whereIn('chanel_id', ["UCQvrGUzh4OmJWS9bQBzknbg"])->get();

        $countChannel = count($channels);
        $i = 0;
        foreach ($channels as $channel) {
            $i++;
//            $listData = YoutubeHelper::getPlaylistAll(substr_replace($channel->chanel_id, "UU", 0, 2));
//            $listVideos = $listData['list_video_id'];
            // <editor-fold defaultstate="collapsed" desc="Scan label cho video campaign">
            $listVideos = [];
            $temps = DB::select("select video_id from campaign where video_type = 2 and  status = 1");
            foreach ($temps as $temp) {
                $listVideos[] = $temp->video_id;
            }

            // </editor-fold>

            $countVideo = count($listVideos);
            $j = 0;
            foreach ($listVideos as $video_id) {
                $j++;
                $videoInfo = YoutubeHelper::getVideoInfoV2($video_id);
                if ($videoInfo["status"] == 0) {
                    for ($t = 0; $t < 5; $t++) {
                        error_log("scanchannelmusiclicences Retry $video_id");
                        $videoInfo = YoutubeHelper::getVideoInfoV2($video_id);
                        error_log(json_encode($videoInfo));
                        if ($videoInfo["status"] == 1) {
                            break;
                        }
                    }
                }
                $check = VideoInfo::where("video_id", $video_id)->first();
                if ($check) {
                    $check->channel_name = $videoInfo["channelName"];
                    $check->video_id = $video_id;
                    $check->channel_id = $channel->chanel_id;
                    $check->title = $videoInfo["title"];
                    $check->length = $videoInfo["length"];
                    $check->like = $videoInfo["like"];
                    $check->dislike = $videoInfo["dislike"];
                    $check->view = $videoInfo["view"];
                    $check->publish_date = gmdate("m-d-Y", $videoInfo["publish_date"] + 7 * 3600);
                    $check->song_name = $videoInfo["song_name"];
                    $check->artists = $videoInfo["artists"];
                    $check->album = $videoInfo["album"];
                    $check->license = $videoInfo["license"];
                    $check->writers = $videoInfo["writers"];
                    $check->comment = $videoInfo["comment"];
                    $check->song_count = $videoInfo["countSong"];
                    $check->date_scan = gmdate("m-d-Y", time() + 7 * 3600);
                    $check->save();
                } else {

                    $video = new VideoInfo();
                    $video->channel_id = $channel->chanel_id;
                    $video->channel_name = $videoInfo["channelName"];
                    $video->video_id = $video_id;
                    $video->title = $videoInfo["title"];
                    $video->length = $videoInfo["length"];
                    $video->like = $videoInfo["like"];
                    $video->dislike = $videoInfo["dislike"];
                    $video->view = $videoInfo["view"];
                    $video->publish_date = gmdate("m-d-Y", $videoInfo["publish_date"] + 7 * 3600);
                    $video->song_name = $videoInfo["song_name"];
                    $video->artists = $videoInfo["artists"];
                    $video->album = $videoInfo["album"];
                    $video->license = $videoInfo["license"];
                    $video->writers = $videoInfo["writers"];
                    $video->comment = $videoInfo["comment"];
                    $video->song_count = $videoInfo["countSong"];
                    $video->date_scan = gmdate("m-d-Y", time() + 7 * 3600);
                    $video->save();
                }
                error_log("scanchannelmusiclicences $i/$countChannel $j/$countVideo $channel->chanel_id $video_id");
            }
        }
    }

    //fix scan label cho video upload daily
    public function reScanLabelForNewUpload() {
        $channels = AccountInfo::where("is_music_channel", 1)->get();
        $i = 0;
        foreach ($channels as $channel) {
            $i++;
            $lastUploadLabels = json_decode($channel->last_upload_label);
//            error_log("FIRST: ".json_encode($lastUploadLabels));
            foreach ($lastUploadLabels as $upload) {
//                error_log("BEFORE: ".json_encode($upload));
                if ($upload->title == "" || $upload->label == "" || $upload->artist == "") {
                    $videoInfo = YoutubeHelper::getVideoInfoV2($upload->video_id);
                    if ($videoInfo["status"] == 0) {
                        for ($t = 0; $t < 10; $t++) {
                            error_log("reScanLabelForNewUpload retry $upload->video_id");
                            $videoInfo = YoutubeHelper::getVideoInfoV2($upload->video_id);
                            if ($videoInfo["status"] == 1) {
                                break;
                            }
                        }
                    }
                    $upload->title = $videoInfo["title"];
                    $upload->label = $videoInfo["license"];
                    $upload->artist = $videoInfo["artists"];
                    $campaign = Campaign::where("video_id", $upload->video_id)->first();
//                    is_promo,0:chờ confirm,1:đã confirm,2:reject
                    if (!$campaign) {
                        $upload->is_promo = 0;
                    }
                }
            }
            error_log("$i TOTAL: " . json_encode($lastUploadLabels));
            $channel->last_upload_label = json_encode($lastUploadLabels);
            $channel->save();
        }
    }

    //scan views subs cho tất cả các kênh
    public function scanChannel($threadId) {
        $numberThread = 1;
        $curr = time();
        $i = 0;

//        $jobs = AccountInfo::where(DB::raw("id % $numberThread"), $threadId)->whereIn("id", [236785,236788,236791,236796,236806,299192])->get(["id", "chanel_id", "chanel_name", "increasing", "view_count", "subscriber_count", "view_detail", "status"]);
//        $jobs = AccountInfo::where(DB::raw("id % $numberThread"), $threadId)->where("status", 1)->where('is_music_channel', 0)->get(["id", "chanel_id", "chanel_name", "increasing", "view_count", "subscriber_count", "view_detail", "status"]);
//        $jobs = AccountInfo::where("id",280371)->get(["id", "chanel_id","chanel_name","increasing","view_count","subscriber_count","view_detail","status"]);
        $jobs = AccountInfo::whereIn("is_music_channel", [2, 1])->get(["id", "chanel_id", "chanel_name", "increasing", "view_count", "subscriber_count", "view_detail", "status"]);
        $total = count($jobs);
        foreach ($jobs as $channel) {
            $i++;
            $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
            if ($infoChannel["status"] == 0) {
                for ($t = 0; $t < 2; $t++) {
                    error_log("scanChannel_$threadId retry $channel->chanel_id");
                    $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
                    if ($infoChannel["status"] == 1) {
                        break;
                    }
                }
            }
            if ($infoChannel["status"] == 1) {
                if ($infoChannel["channelName"] != null && $infoChannel["channelName"] != '') {
                    $channel->chanel_name = $infoChannel["channelName"];
                }
//                $channel->increasing = intval($infoChannel["views"]) - intval($channel->view_count);
                $channel->view_count = intval($infoChannel["views"]);
                $channel->subscriber_count = intval($infoChannel["subscribers"]);
//                $viewDetailTmp = $channel->view_detail;
//                if (isset($viewDetailTmp)) {
//                    $viewDetails = json_decode($viewDetailTmp);
//                } else {
//                    $viewDetails = array();
//                }
//                if (count($viewDetails) > 30) {
//                    array_shift($viewDetails);
//                }
//                //add view detail
//                $arrTmpView = array();
//                $arrTmpView["time"] = $curr;
//                $arrTmpView["view"] = intval($infoChannel["views"]);
//                $arrTmpView["daily"] = $channel->increasing >= 0 ? $channel->increasing : 0;
//                $viewDetails[] = $arrTmpView;
//                $channel->view_detail = json_encode($viewDetails);
//            } else {
//                $channel->increasing = 0;
            }
//            $channel->status = $infoChannel["status"];
            $channel->save();
            error_log("$i/$total scanChannel_$threadId $channel->chanel_id " . json_encode($infoChannel));
        }
    }

    //scan label cho music channel, chạy vào 6h sáng hằng ngày
    public function scanLabel() {
        $locker = new Locker(997);
        $locker->lock();
        $channels = AccountInfo::where("is_music_channel", 1)->get();
        $i = 0;
        $total = count($channels);
        foreach ($channels as $channel) {
            $i++;
            if ($channel->last_upload_label != null && $channel->last_upload_label != "") {
                $uploadeds = json_decode($channel->last_upload_label);
                foreach ($uploadeds as $uploaded) {
                    if ($uploaded->label == "" || $uploaded->artist == "") {
                        $videoInfo = YoutubeHelper::getVideoInfoV2($uploaded->video_id);
                        if ($videoInfo["status"] == 0) {
                            for ($t = 0; $t < 15; $t++) {
                                error_log("scanLabel retry $uploaded->video_id");
                                $videoInfo = YoutubeHelper::getVideoInfoV2($uploaded->video_id);
                                if ($videoInfo["status"] == 1) {
                                    break;
                                }
                            }
                        }
                        $uploaded->title = $videoInfo["title"];
                        $uploaded->label = $videoInfo["license"];
                        $uploaded->artist = $videoInfo["artists"];
                    }
                }
                $channel->last_upload_label = json_encode($uploadeds);
                $channel->save();
                error_log("scanLabel $i/$total $channel->chanel_id");
            }
        }
    }

    //import spotify playlist để crawl nhạc cho menu spotify music
    public function importSpotifyPlaylist() {
        $playlists = [];

        foreach ($playlists as $playlist) {
            $check = SpotifyMusicPlaylist::where("playlist_id", $playlist)->first();
            if (!$check) {
                $insert = new SpotifyMusicPlaylist();
                $insert->playlist_id = $playlist;
                $insert->create_time = gmdate("d/m/Y", time() + 7 * 3600);
                $insert->update_time = gmdate("d/m/Y", time() + 7 * 3600);
                $insert->save();
            }
        }
    }

    //export genre
    public static function exportGenre() {
        $datas = Spotifymusic::whereNotNull("art_genre")->where("art_genre", "<>", "")->get();
        $genres = [];
        foreach ($datas as $data) {
            $arrTemp = explode(",", $data->art_genre);
            foreach ($arrTemp as $temp) {
                if (!in_array($temp, $genres)) {
                    $genres[] = $temp;
                }
            }
        }
        Log::info(json_encode($genres));

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=genres.csv',
            'Expires' => '0',
            'Pragma' => 'public'
        ];
        $csv = "Genre\n";
        foreach ($genres as $genre) {
            $csv .= "$genre\n";
        }

        $file_path40 = storage_path('genres.csv');
        $csv_handler40 = fopen($file_path40, 'w');
        fwrite($csv_handler40, $csv);
        fclose($csv_handler40);
        return response()->download($file_path40, "genres.csv", $headers);
    }

    //check info playlist
    public static function checkInfoPlaylist(Request $request) {
        $info = YoutubeHelper::processLink($request->playlistId);
        if ($info["type"] != 1) {
            return array("status" => 0, "message" => "Playlist invalid");
        }
        $playlistId = $info["data"];
        $data = YoutubeHelper::getPlaylistInfoV3($playlistId);
        if ($data["status"] == 2) {
            return array("status" => 0, "message" => "Playlist dead or not exists");
        }
        return array("status" => 1, "message" => $data);
    }

    //scan ngày upload video cho kênh lyric upload lên athena
    public function channelVideoDatePublish(Request $request) {

        $numberThreadInit = 2;
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }
        for ($i = 0; $i < $numberThreadInit; $i++) {
            if ($i == $threadId) {
                $locker = new Locker(intval("996$i"));
                $locker->lock();
            }
        }
//        RequestHelper::getUrl("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Start scan channelVideoDatePublish", 0);
        $channels = AccountInfo::where(DB::raw("id % $numberThreadInit"), $threadId)->whereIn("is_music_channel", [1, 2])->where("is_sync", 1)->get();
//        $channels = AccountInfo::where("chanel_id", "UCl753MBJ74eIjUdQBd0Q46w")->where("is_sync",1)->get();
        $curr = time();
        $j = 0;
        $total = count($channels);
        foreach ($channels as $channel) {
            $channel_id = substr_replace($channel->chanel_id, "UU", 0, 2);
            $j++;
            $lists = YoutubeHelper::getPlaylist($channel_id, 100);
            $listVideoTitle = $lists['list_video_name'];
            $listVideoId = $lists['list_video_id'];
            $listVideoDate = $lists['list_date'];
            $n = 0;

            if (count($listVideoId) == 0) {
                for ($t = 0; $t < 5; $t++) {
                    error_log("channelVideoDatePublish retry getPlaylist $channel_id");
                    $lists = YoutubeHelper::getPlaylist($channel_id, 100);
                    $listVideoTitle = $lists['list_video_name'];
                    $listVideoId = $lists['list_video_id'];
                    $listVideoDate = $lists['list_date'];
                    if (count($listVideoId) > 0) {
                        break;
                    }
                }
            }
            $totalVideo = count($listVideoId);
            error_log("channelVideoDatePublish $channel->chanel_id $totalVideo");
            for ($i = 0; $i < $totalVideo; $i++) {
                $n++;
                $check = VideoDaily::where("video_id", $listVideoId[$i])->first();
                if (!$check) {
                    $videoDaily = new VideoDaily();
                    $videoDaily->channel_id = $channel->chanel_id;
                    $videoDaily->video_id = $listVideoId[$i];
                    $videoDaily->video_title = $listVideoTitle[$i];
                    $videoDaily->date = $listVideoDate[$i];
                    $videoDaily->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                    $videoDaily->save();
                    error_log("channelVideoDatePublish $j/$total channel=$channel->chanel_id $n/$totalVideo videos $channel->chanel_name $videoDaily->video_id $videoDaily->date");
                } else {
                    error_log("channelVideoDatePublish $j/$total channel=$channel->chanel_id $n/$totalVideo videos $channel->chanel_name $check->video_id $check->date exists");
                    $check->video_title = $listVideoTitle[$i];
                    $check->save();
                }
            }
        }
        error_log("channelVideoDatePublish finish " . (time() - $curr));
        RequestHelper::getUrl("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Finish scan channelVideoDatePublish " . (time() - $curr) . 's', 0);
    }

    //cảnh bảo kênh không upload video
    public function channelUploadAlert() {
        $date = time() - 86400 * 2;
        $datas = DB::select("select a.*,max(b.date) as last_upload from (select user_name,chanel_id,chanel_name,subscriber_count as subs,view_count as views from accountinfo accountinfo where is_music_channel in (1,2) and is_sync =1 and upload_alert=1) a 
                                left join (select channel_id,video_id,date from video_daily) b
                                on a.chanel_id =b.channel_id
                                group by a.chanel_id having max(b.date) < " . gmdate("Ymd", $date) . " or max(b.date) is null order by max(b.date)");
        if (count($datas) > 0) {
            foreach ($datas as $data) {
                if ($data->last_upload != null) {
                    $user = User::where("user_code", $data->user_name)->first();
                    $info = explode("@@", $user->telegram_id);
                    if (count($info) >= 2) {
                        $lastUploadTime = strtotime($data->last_upload);
                        $timeAgo = round((time() - $lastUploadTime) / 86400 - 1) . ' days ago';
                        $message = "Kênh <a href='https://www.youtube.com/channel/$data->chanel_id'><b>$data->chanel_name</b></a> lần cuối upload ngày " . Utils::convertToViewDate($data->last_upload) . " ($timeAgo)";
                        $url = "https://api.telegram.org/bot$info[0]/sendMessage?chat_id=-$info[1]&text=$message&parse_mode=html";
//                        $url = "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=$message&parse_mode=html";
                        ProxyHelper::get($url, 2);
                    }
                }
            }
        }
    }

    //scan claim information cho video mới upload
    public function scanClaim(Request $request) {
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }

        $processName = "scanClaim-$threadId";
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
//        $locker = new \App\Common\Locker(99);
//        Log::info($locker->getLock());
//        $locker->lock();
            $curr = time();
            DB::enableQueryLog();
            $numberThreadInit = 5;
//        $promos = CampaignStatistics::where("type", 1)->where("status", 1)->whereNotNull("artist")->whereNotNull("song_name")->get();
//        $videos = VideoDaily::where("id",50216	)->get();
//        $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status", 1)->get();
//            $videos = VideoDaily::where("date", 9999)->get();
//        $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("artists","like" ,"%Lalo Ebratt%")->get();
//        $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("artists","like" ,"%Maximillian%")->get();
//        $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("artists","like" ,"%Alex Rose%")->get();
//        $videos = VideoDaily::where("channel_id","UCflJM8UCR-71WuAh8cNomUw")->get();
            $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status_info", "0")->orderBy("date", "desc")->get();
            if (isset($request->channel_id)) {
                $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status_info", "0")->where("channel_id", $request->channel_id)->orderBy("date", "desc")->get();
            }
//            $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->whereIn("status_info", [0, 2])->where("retry", ">", 0)
//                            ->whereRaw("(" . time() . " - UNIX_TIMESTAMP(CONVERT_TZ(STR_TO_DATE(scan_time,'%Y/%m/%d %H:%i:%s'),'+07:00', 'SYSTEM')) > 86400 or scan_time is null)")
//                            ->orderBy("channel_id", "desc")->get();
            $total = count($videos);
            error_log("scanClaim-$threadId $total videos");
            foreach ($videos as $index => $video) {
                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video->video_id);
                if ($videoInfo["status"] == 0) {
                    for ($t = 0; $t < 10; $t++) {
                        error_log("scanClaim retry $video->video_id");
                        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video->video_id);
                        if ($videoInfo["status"] == 1) {
                            break;
                        }
                    }
                }
                $scanTime = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                $statusInfo = 1;
                if ($videoInfo["song_name"] == '[]') {
                    $statusInfo = 2;
                }
                if ($video->status_info != 1) {
                    $duration = intval($videoInfo["length"]);
                    if ($video->songs == null || $video->songs == '[]') {
                        $video->songs = $videoInfo["song_name"];
                    }
                    if ($video->artists == null || $video->artists == '[]') {
                        $video->artists = $videoInfo["artists"];
                    }
                    if ($video->claims == null || $video->claims == '[]') {
                        $video->claims = $videoInfo["license"];
                    }
                    $video->duration = $duration;
                }
//                $video->retry = $video->retry - 1;
                $video->status_info = $statusInfo;
                $video->scan_time = $scanTime;
                $video->status = $videoInfo["status"];
                $video->views = $videoInfo["view"];
                $video->likes = $videoInfo["like"];
                $video->dislikes = $videoInfo["dislike"];
                $video->video_title = $videoInfo["title"];
                $video->save();
                error_log("$index/$total scanClaim-$request->threadId $video->channel_id $video->video_id " . $videoInfo["view"] . " " . $videoInfo["song_name"]);
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
        return 0;
//        Log:info(DB::getQueryLog());
    }

    //hàm scan xem video còn sống hay chết
    public function scanX(Request $request) {
        $date = gmdate("Y/m/d", time() + 7 * 3600);
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }
        $numberThreadInit = 2;
        $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status", "1")->where("wake_views", 0)->orderBy("date", "desc")->get();
        $total = count($videos);
        $count = 0;
        foreach ($videos as $index => $video) {
            $video->scan_time = $date;
            $video->save();
            $listVideoId[] = trim($video->video_id);
//            Log::info(count($listVideoId) . " - " . $index . " - " . ($total - 1));
            if (count($listVideoId) >= 45 || $index == ($total - 1)) {
                $jData = YoutubeHelper::getStatics(implode(",", $listVideoId));
                error_log("scanX-$threadId|getStatics|$index/$total " . count($listVideoId));
                if (isset($jData)) {
                    $items = !empty($jData->items) ? $jData->items : [];
//                    error_log("|$user->user_name|DashboardController.scanViewPromoCampaign|count=" .count($items));
                    foreach ($items as $item) {
                        $vidID = $item->id;
                        $views = !empty($item->statistics->viewCount) ? $item->statistics->viewCount : 0;
                        $likes = !empty($item->statistics->likeCount) ? $item->statistics->likeCount : 0;

                        if ($views > 0) {
                            VideoDaily::where("video_id", $vidID)->update(["views" => $views, "wake_views" => $views, "likes" => $likes, "scan_time" => $date, "status" => 1]);
                        }
                        $count++;
                    }
                } else {
                    error_log("scanX-$threadId|not found result");
                }
                $listVideoId = [];
            }
        }
    }

    public function callScanClaimByCondition() {
        $process = LockProcess::where("type", 2)->where("status", 0)->get();
        foreach ($process as $index => $data) {
            Log::info("http://automusic.win/scanClaimByCondition?id=$data->id&threadId=$index");
            RequestHelper::fetchWithoutResponseURL("http://automusic.win/scanClaimByCondition?id=$data->id&threadId=$index");
        }
    }

    //scan claim, view theo artists, song name
    public function scanClaimByCondition(Request $request) {
        DB::enableQueryLog();
        error_log('|ApiController.scanClaimByCondition|request=' . json_encode($request->all()));
        $PID = getmypid();
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }

//        $processName = "scanClaim-$threadId";
        $process = LockProcess::where("type", 2)->where("status", 0)->where("id", $request->id)->first();
        if ($process) {
            $process->status = 1;
            $process->pid = $PID;
            $process->save();
            $condition = json_decode($process->condition);
            $artists = $condition->artists;
            $songName = $condition->song_name;
            $channelId = $condition->channel_id;
            $curr = time();
            $numberThreadInit = 5;
            $videos = VideoDaily::where(DB::raw("id % $numberThreadInit"), $threadId)->whereRaw("1=1");
            if ($artists != null && $artists != "") {
                $videos->where("artists", "like", "%$artists%")->orWhere("video_title", "like", "%$artists%");
            }
            if ($songName != null && $songName != "") {
                $videos->orWhere("songs", "like", "%$songName%")->orWhere("video_title", "like", "%$artists%");
            }
            if ($channelId != null) {
                $videos->where("channel_id", "$channelId");
            }
            $videos = $videos->get();
            Log::info(DB::getQueryLog());
            $total = count($videos);
            $process->total = $total;
            $process->save();
            error_log("scanClaim-$request->threadId $total");
            $count = 0;
            foreach ($videos as $video) {
                $count++;
                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video->video_id);
                if ($videoInfo["status"] == 0) {
                    for ($t = 0; $t < 10; $t++) {
                        error_log("scanClaim retry $video->video_id");
                        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video->video_id);
                        if ($videoInfo["status"] == 1) {
                            break;
                        }
                    }
                }
                $scanTime = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                $statusInfo = 1;
                if ($videoInfo["song_name"] == '[]') {
                    $statusInfo = 2;
                }
                $duration = intval($videoInfo["length"]);
                if ($video->status_info != 1) {
                    if ($video->songs == null || $video->songs == '[]') {
                        $video->songs = $videoInfo["song_name"];
                    }
                    if ($video->artists == null || $video->artists == '[]') {
                        $video->artists = $videoInfo["artists"];
                    }
                    if ($video->claims == null || $video->claims == '[]') {
                        $video->claims = $videoInfo["license"];
                    }
                }
                $video->duration = $duration;
                $video->status_info = $statusInfo;
                $video->scan_time = $scanTime;
                $video->status = $videoInfo["status"];
                $video->views = $videoInfo["view"];
                $video->likes = $videoInfo["like"];
                $video->dislikes = $videoInfo["dislike"];
                $video->video_title = $videoInfo["title"];
                $video->save();
                $process->current = $process->current + 1;
                $process->save();
                error_log("scanClaimByCondition $count/$total $video->channel_id $video->video_id " . $videoInfo["view"] . " " . $videoInfo["song_name"]);
            }
        }
    }

    //api get video upload daily cho athena
    public function channelVideoDatePublishGet(Request $request) {
        DB::enableQueryLog();
        Log::info('channelVideoDatePublishGet|request=' . json_encode($request->all()));
        $listDate = [];
        $result = [];
        $temp = DB::select("select distinct date from video_daily where status_athena = 0 and  channel_id = '$request->channel_id'");
        foreach ($temp as $t) {
            $listDate[] = $t->date;
        }
        if (count($listDate) > 0) {
            $datas = VideoDaily::whereIn("date", $listDate)->where("channel_id", $request->channel_id);
            $result = $datas->get(["channel_id", "video_id", "date"]);
            if (count($result) > 0) {
                $datas->update(["status_athena" => 1]);
            }
        }
//        Log:info(DB::getQueryLog());
        return $result;
    }

    //hàm thêm video hệ thống scan được vào campaign
    public function updatePromosToCampaign() {
        $locker = new Locker(993);
        $locker->lock();
        error_log(__FUNCTION__);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);
        $dateToGet = gmdate("Ymd", time() - 60 * 86400);
//        $promos = CampaignStatistics::where("type", 1)->where("id", 280)->whereNotNull("artist")->whereNotNull("song_name")->get();
        $promos = CampaignStatistics::whereIn("type", [1, 2, 4, 5])->where("status", 1)->whereNotNull("artist")->whereNotNull("song_name")->get();
        $datas = VideoDaily::where("date", ">=", $dateToGet)->where("status_info", 1)->where('is_campaign', 0)->get();
        $total = count($datas);
        $curr = time();
        foreach ($datas as $index => $data) {
            //check video mới nhất xem có video nào thuộc campaign mà chưa được add thì add vào
            $listSongName = json_decode($data->songs);
            $listArtist = json_decode($data->artists);
            $duration = intval($data->duration);

            $videoType = 2;
            if ($duration <= 90) {
                $videoType = 6;
            } else if ($duration > 90 && $duration <= 8 * 60) {
                $videoType = 2;
            } else {
                $videoType = 5;
            }
            error_log("updatePromosToCampaign $index/$total $data->video_id checking");
            if ($videoType == 5) {
                foreach ($promos as $promo) {

                    if (in_array($promo->song_name, $listSongName) && in_array($promo->artist, $listArtist)) {
                        $checkCam = Campaign::where("video_id", $data->video_id)->where("campaign_id", $promo->id)->first();
                        if (!$checkCam) {
                            $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();
                            $campaign = new Campaign();
                            $pos = strripos($channel->user_name, '_');
                            $manager = substr($channel->user_name, 0, $pos);
                            //nếu mà là claim thì chỉ thêm vào campaign có username = username make video.
                            if ($promo->type == 2 && $manager != $promo->username) {
                                continue;
                            }
                            $campaign->username = $manager;
                            $campaign->campaign_id = $promo->id;
                            $campaign->campaign_name = $promo->campaign_name;
                            $campaign->channel_id = $data->channel_id;
                            $campaign->channel_name = $channel->chanel_name;
                            $campaign->video_type = $videoType;
                            $campaign->video_id = $data->video_id;
                            $campaign->video_title = $data->video_title;
                            $campaign->views_detail = '[]';
                            $campaign->status = $data->status;
                            $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                            $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                            $campaign->publish_date = strtotime($data->date);
                            $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                            $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                            $campaign->log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " updatePromosToCampaign System added";
                            $campaign->status_confirm = $videoType == 5 ? 3 : 1;
                            $campaign->save();
                            error_log("updatePromosToCampaign $index/$total $data->video_id auto add campaign to campaign $promo->id");
                            $data->is_campaign = 1;
                            $data->save();
                        } else {
                            $log = $campaign->log;
                            $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " updatePromosToCampaign exists on system";
                            $campaign->log = $log;
                            $campaign->save();
                        }
                    } else {
                        $data->is_campaign = 1;
                        $data->save();
                    }
                }
            }
        }
    }

    //api lay campaign upload lên athena
    public function promoCampaign(Request $request) {
        $locker = new Locker(992);
        $locker->lock();
        error_log(__FUNCTION__);
        Log::info('promoCampaign|request=' . json_encode($request->all()));
        $condition = "";
        if (isset($request->date)) {
            $condition = " and a.insert_date in(" . implode(",", $request->date) . ")";
        }
        $videos = DB::select("select a.id,a.insert_date as date,b.id as promo_id,b.campaign_name as promo_name,
                    CASE WHEN a.video_type='2' THEN 'lyric' 
                    WHEN a.video_type='5' THEN 'mix'
                    WHEN a.video_type='6' THEN 'short'
                    END as video_type,a.video_id,a.video_title,a.channel_id,a.channel_name
                    from campaign a, campaign_statistics b 
                    where a.campaign_id= b.id and b.type in (1,2,4,5) 
                    and a.video_type <> 1 and a.video_type <> 3 and status_use = 1 and status_confirm = 3 and channel_id !=''
                    and insert_date in (select distinct insert_date from campaign a, campaign_statistics b where a.campaign_id = b.id and b.type in (1,2,4,5) and a.is_athena = 0 and a.status_use = 1 and a.video_type <> 1 and a.video_type <> 3 and a.status_confirm = 3 and channel_id <> '')");

        $officials = DB::select("select campaign_id as promo_id,video_id as promo_video_id,video_title as promo_video_name from campaign where video_type = 1 and status_use = 1 group by campaign_id,video_id,video_title");
        $result = [];
        $i = 0;
        $off = count($officials);
        foreach ($videos as $video) {
            $i++;
            foreach ($officials as $official) {
                $promoVideoId = "";
                $promoVideoName = "";
                if ($video->promo_id == $official->promo_id) {
                    $promoVideoId = $official->promo_video_id;
                    $promoVideoName = $official->promo_video_name;
//                    error_log("$i $video->video_id $official->promo_id");
                    break;
                }
            }
            $temp = (object) [
                        "date" => $video->date,
                        "promo_id" => $video->promo_id,
                        "promo_name" => $video->promo_name,
                        "promo_video_id" => $promoVideoId,
                        "promo_video_name" => $promoVideoName,
                        "video_type" => $video->video_type,
                        "video_id" => $video->video_id,
                        "video_title" => $video->video_title,
                        "channel_id" => $video->channel_id,
                        "channel_name" => $video->channel_name
            ];
            Campaign::where("id", $video->id)->update(["is_athena" => 1]);
            $result[] = $temp;
        }
        Log::info("promoCampaign Total: " . count($videos) . " result:" . count($result));
        return $result;
    }

    //api lấy thông tin kênh lyric tâng 6
    public function channelInformation(Request $request) {
        $datas = AccountInfo::whereIn("is_music_channel", [1, 2])->get(["channel_code_orfium", "user_name", "chanel_id", "chanel_name", "channel_genre", "channel_type", "chanel_create_date", "note", "client_id", "client_secret", "chanel_code", "group_channel_id", "is_music_channel", "is_sync", "api_type", "api_bulk_type"]);
        foreach ($datas as $data) {
            if ($data->api_bulk_type == 2 || $data->api_bulk_type == 3) {
                $data->chanel_code = $data->channel_code_orfium;
                $data->client_id = "841180336951-3hk8i45arjftt6rmskftvc518nurlv1u.apps.googleusercontent.com";
                $data->client_secret = "YMmwipN2NaUAYgch6recQ8zf";
            }
        }
        return $datas;
    }

    //trả về danh sách topic claim
    public function getClaimTopic(Request $request) {
        $conditon = "";
        if (isset($request->username)) {
            $conditon = "and (username = '$request->username' or username is null)";
        }
        //topicType = PROMOS/CLAIM
        $topicType = "CLAIM";
        if (isset($request->type)) {
            $topicType = $request->type;
        }

        //mix, lyric, outro
        $claimType = "mix";
        if (isset($request->claim_type)) {
            $claimType = $request->claim_type;
        }
        if ($topicType == "PROMOS") {
            $genres = DB::select("select genre from campaign_statistics where status =1 and type in(1,4) group by genre order by custom_genre");
            foreach ($genres as $genre) {
                $datas[] = (object) ["topic" => "PROMOS_" . "$genre->genre"];
            }
            $datas[] = (object) ["topic" => "NO_PROMO"];
        } else if ($topicType == "CLAIM") {
            if ($claimType == "mix") {
                //mix được dùng hết các bài claim
                $datas = DB::select("select concat(custom_genre,'_',genre) as topic from campaign_statistics where status =1 and type =2 $conditon group by custom_genre,genre order by custom_genre");
            } else if ($claimType == "lyric") {
                //lyric chỉ được dùng bài BLACK_GOOD,WHITE_CLAIM
                $datas = DB::select("select concat(custom_genre,'_',genre) as topic from campaign_statistics where status =1 and type =2 and custom_genre in ('BLACK_GOOD','WHITE_CLAIM') $conditon group by custom_genre,genre order by custom_genre");
            } else if ($claimType == "outro") {
                //outro chỉ được dùng bài BLACK_AUDIO
                $datas = DB::select("select concat(custom_genre,'_',genre) as topic from campaign_statistics where status =1 and type =2 and custom_genre in ('BLACK_AUDIO') $conditon group by custom_genre,genre order by custom_genre");
            }
            $datas[] = (object) ["topic" => "NO_CLAIM"];
//            $datas[] = (object) ["topic" => "CLAIM_SME_EASY|CLAIM_WMG_EASY"];
        }

        return $datas;
    }

    //lấy nhạc claim theo topic
    public function getMusicClaimByTopic(Request $request) {
        $time = time();
//        Log::info('getMusicClaimByTopic|request=' . json_encode($request->all()));
        DB::enableQueryLog();
        $number = 500;
        if (isset($request->number)) {
            $number = $request->number;
        }
        $type = "CLAIM";
        if (isset($request->type)) {
            $type = $request->type;
        }

        if (Utils::containString($request->topic, "PROMOS")) {
            $topic = explode("_", $request->topic);
            $datas = [];
            $temps = CampaignStatistics::whereIn("type", [1, 4, 5])->where("status", 1)
                            ->whereRaw($request->tier == 1 ? DB::raw("(genre = '" . $topic[1] . "' or tier=1)") : DB::raw("(genre = '" . $topic[1] . "')"))
                            ->whereNotNull("audio_url")->whereNotNull("lyric_timestamp_id")
                            ->orderBy("campaign_start_date", "desc")->take($number)->get();
//            Log::info(DB::getQueryLog());
            foreach ($temps as $temp) {
                $temp->count = $temp->count + 1;
                $temp->save();
                $temp->views_percent = 0;
                if ($temp->target != null && $temp->target != "" && $temp->target != 0) {
                    $temp->views_percent = round($temp->views / Utils::shortNumber2Number($temp->target) * 100);
                }
                //2024/04/17 sáng confirm order by views_percent asc và views >120% thì sẽ ko hiện ra nữa
                if ($temp->views_percent < 120) {
                    $datas[] = (object) [
                                "id" => $temp->id,
                                "topic" => $request->topic,
                                "youtube_link" => $temp->audio_url,
                                "local_link" => $temp->audio_url,
                                "title" => $temp->song_name,
                                "artist" => $temp->artist,
                                "lyric_timestamp_id" => $temp->lyric_timestamp_id,
                                "views" => $temp->views,
                                "spotify_id" => $temp->spotify_id,
                                "tier" => $temp->tier,
                                "source_type" => "promo",
                                "views_percent" => $temp->views_percent,
                                "label" => $temp->campaign_type
                    ];
                }
            }
            usort($datas, function($a, $b) {
                return $a->views_percent > $b->views_percent;
            });
            return $datas;
        } else if ($request->topic == "NO_CLAIM") {
            return array();
        }


        if (Utils::containString($request->topic, "CLAIM_SME") || Utils::containString($request->topic, "CLAIM_WMG")) {
            $limitWeek = 9999999;
            $limitAll = 9999999;
        } elseif (Utils::containString($request->topic, "BLACK")) {
            $limitWeek = 100;
            $limitAll = 2000;
        } else {
            $limitWeek = 50;
            $limitAll = 1000;
        }



        $datas = CampaignStatistics::where("status", 1)->where("type", 2)->whereNotNull("lyric_timestamp_id");

        //lấy nhạc ở nhiều custom genre khách nhau
        if (Utils::containString($request->topic, "|")) {
            $cus = explode("|", $request->topic);

            $datas = $datas->where(function($q) use ($cus) {
                foreach ($cus as $tmp) {
                    $pos = strripos($tmp, '_');
                    $customGenre = substr($tmp, 0, $pos);
                    $genre = substr($tmp, $pos + 1, strlen($tmp) - 1);
                    $q->orWhere('custom_genre', $customGenre);
                }
            });
        } else {
            $pos = strripos($request->topic, '_');
            $customGenre = substr($request->topic, 0, $pos);
            $genre = substr($request->topic, $pos + 1, strlen($request->topic) - 1);
            $datas = $datas->where("custom_genre", $customGenre);
            //nếu tier  = 1 thì không phân bienj genre
            $datas = $datas->whereRaw($request->tier == 1 ? DB::raw("(genre = '$genre' or tier=1)") : DB::raw("(genre = '$genre')"));
        }




        if (isset($request->username) && $request->username != "") {
//            $datas = $datas->whereRaw("(username = '$request->username' or username is null)");
            $datas = $datas->where("username", $request->username);
        }

        //kiểm tra giới hạn tuần đã quá 1 tuần chưa, nếu quá thì reset
        $checks = $datas->get();
//        Log::info(DB::getQueryLog());
        foreach ($checks as $check) {
            if ($check->last_reset_time == null) {
                $check->last_reset_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $check->save();
            }
            if (strtotime("$check->last_reset_time GMT+7") + 7 * 84600 < time()) {
                $check->last_reset_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $check->count_week = 0;
                $check->save();
            }
        }

        $currentMonth = gmdate("Ym", time() + 7 * 3600);

        $datas = $datas->where("count_week", "<", $limitWeek)
                        ->where("count", "<", $limitAll)
//                        ->inRandomOrder()
                        ->take($number)
                        ->select(DB::raw("distributor,id,campaign_name,"
                                        . "concat(custom_genre,'_',genre) as topic,"
                                        . "'' as youtube_link, audio_url as local_link,"
                                        . "song_name as title,artist,0 as duration_ms,"
                                        . "count,count_week,$limitWeek as limit_week,"
                                        . "lyric_timestamp_id,deezer_id,spotify_id,views,tier"))->get();

        $ids = [];
        foreach ($datas as $d) {
            $ids[] = $d->id;
        }
        $campaigns = [];
        $cacheKey = "cache_list_claim_with_views_month";
        if (Cache::has($cacheKey)) {
            $campaigns = Cache::get($cacheKey);
        }
        $results = [];
        foreach ($datas as $data) {

            if (!isset($request->is_front)) {
                $data->count = $data->count + 1;
                $data->count_week = $data->count_week + 1;
                $data->save();
            }
            $data->source_type = "claim";
            $data->pre_name = $data->distributor;
//            if (Utils::containString($data->campaign_name, "[ORIGINAL]")) {
//                $data->pre_name = "ORI";
//            } elseif (Utils::containString($data->campaign_name, "[COVER]")) {
//                $data->pre_name = "COVER";
//            } elseif (Utils::containString($data->campaign_name, "[ELECTRONIC]")) {
//                $data->pre_name = "ELEC";
//            }
//            $data->title = "[$data->pre_name" . "] $data->title";
            $data->views_total = $data->views;
            $data->views = 0;
            foreach ($campaigns as $campaign) {
                if ($data->id == $campaign->id) {
                    $data->views += ($campaign->monthly_views_mix + $campaign->monthly_views_lyric + $campaign->monthly_views_short);
                }
            }
            if (Utils::containString($request->topic, "CLAIM_SME") || Utils::containString($request->topic, "CLAIM_WMG")) {
                //ko hiện tittle trên 
                $data->ignore_title = 1;
                if (!isset($request->is_front)) {
                    $data->lyric_sync = "[{\"milliseconds\":1,\"line\":\"...\"}]";
                    $data->title = ".";
                }
            }
            $results[] = $data;
        }
        usort($results, function($a, $b) {
            return $a->views > $b->views;
        });
//        Log::info(DB::getQueryLog());
//        Log::info("Time: " . (time() - $time));
        return $results;
    }

    //thêm danh sách claim vào db bằng 
    public function importMusicClaim(Request $request) {
        $datas = YoutubeHelper::getPlaylistHtml($request->playlist, 1);
        $videosId = $datas['list_video_id'];
        $videosName = $datas['list_video_name'];
        foreach ($videosId as $index => $videoId) {
            Log::info("importMusicClaim $request->topic $videoId");
            $check = MusicHexa::where("youtube_link", "https://www.youtube.com/watch?v=$videoId")->first();
            if (!$check) {
                $musicName = str_replace(" ", "-", $videosName[$index]);
                $musicHexa = new MusicHexa();
                $musicHexa->youtube_link = "https://www.youtube.com/watch?v=$videoId";
                $musicHexa->local_link = "http://51.75.243.130/music/hexa/$request->topic/$musicName.mp3";
                $musicHexa->title = $videosName[$index];
                $musicHexa->genre = $request->topic;
                $musicHexa->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $musicHexa->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $musicHexa->type = 3;
                $musicHexa->save();
                Log::info(json_encode($musicHexa));
            }
        }
    }

    //check xem lệch tạo api đã xong chưa
    public function checkRunMakeApi() {
        $processName = "make-api-check";
        if (ProcessUtils::isfreeProcess($processName, 30)) {
            ProcessUtils::lockProcess($processName);
            $channels = AccountInfo::where("api_status", 1)->whereNotNull("api_job_id")->get();
            foreach ($channels as $channel) {
                Logger::logUpload("checkRunMakeApi http://bas.reupnet.info/job/load/$channel->api_job_id");
                $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$channel->api_job_id", []);
                if ($res != null && $res != "" && !empty($res->status)) {

                    if ($res->status == 4) {
                        $channel->api_status = $res->status;
                        $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($res->result)));
                        foreach ($results as $result) {
                            $temp = json_decode($result);
                            if (Utils::containString($result, "login")) {
                                $log = $temp->result;
                                if ($log != "true") {
                                    $channel->gmail_log = $log;
                                    break;
                                }
                            }
                            if (Utils::containString($result, "create_api")) {
                                $log = $temp->result;
                                $channel->gmail_log = $log;
                                break;
                            }
                        }
                        $channel->save();
                    } else if ($res->status == 5) {
                        //assigon lại ip proxy nếu tạo api thành công
//                    $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/2";
//                    $bas2 = RequestHelper::callAPI("GET", $url2, []);
//                    Logger::logUpload("checkRunMakeApi $url2 " . json_encode($bas2));
                        $channel->api_status = 2;
                        $channel->save();
                    }
                } else {
                    if ($res->id == 0) {
                        $channel->api_status = 5;
                        $channel->save();
                        continue;
                    }
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("$processName is locked");
        }
    }

    //đồng bộ video_id promo lên playlist_analytics
    public function syncPromoVideo() {
        $sql = "select a.id,b.id as campaign_id,b.campaign_name,a.video_id,a.video_title,a.video_type,b.type as campaign_type,a.channel_id, a.channel_name
                from campaign a, campaign_statistics b 
                where a.campaign_id= b.id and b.type in (1,2,4,5) and b.status =1
                and a.video_type in(2,5,6) and status_use = 1 
                and a.status_confirm = 3 and a.channel_id !='' and a.is_athena in(0)";
        $videos = DB::select($sql);
        $update = "update campaign set is_athena =1 where is_athena =0 and video_type in (2,5,6) 
                   and status_confirm =3 and channel_id !='' and campaign_id in (select id from campaign_statistics where type in (1,2,4,5) and status = 1 )";
        DB::statement($update);
        $campaigns = CampaignStatistics::whereIn("type", [1, 2, 4, 5])->where("status", 1)->get(["id"]);

        return array("campaigns" => $campaigns, "videos" => $videos);
    }

    //đồng bộ dữ liệu channel từ athena
    public function syncChannelAthena() {
//        $locker = new Locker(990);
//        $locker->lock();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $startTime = time();
        $listDate = [];
        $result = [];
        $time = time();
        for ($i = 1; $i <= 10; $i++) {
            $listDate[] = "'" . date("Ymd", $time) . "'";
            $result[date("Ymd", $time)] = 0;
            $time = $time - 86400;
        }
        $condition = "where date in (" . implode(",", $listDate) . ")";
        $input = ["query" => "select * from channelbasic $condition "];
        $response = RequestHelper::callAPI2("POST", "http://plla.autoseo.win/query", $input);
//        $res = json_decode($response);
        foreach ($response as $data) {
            $check = AthenaDataSync::where("date", $data->date)->where("channel_id", $data->channel_id)->first();
            if (!$check) {
                $insert = new AthenaDataSync();
                $insert->date = $data->date;
                $insert->channel_id = $data->channel_id;
                $insert->views = $data->views;
                $insert->likes = $data->likes;
                $insert->dislikes = $data->dislikes;
                $insert->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $insert->save();
                $result[$data->date] = $result[$data->date] + 1;
            }
        }
        $message = "Finish syncChannelAthena: ";
        foreach ($result as $index => $data) {
            $message .= "$index=>$data, ";
        }
        $endTime = time() - $startTime;
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=" . $message . '  ' . round($endTime / 60, 3) . ' minutes');
    }

    //đồng bộ dữ liệu promo từ athena
    public function syncPromosAthena() {
//        $locker = new Locker(989);
//        $locker->lock();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        error_log("Start syncPromosAthena");
        $listDate = [];
        $startTime = time();
        $time = time();
        for ($i = 1; $i <= 8; $i++) {
            $listDate[] = "'" . date("Ymd", $time) . "'";
            $time = $time - 86400;
        }
//        $condition = "and date in (" . implode(",", $listDate) . ")";
        $condition = "and date >= '20220801' ";
//        $condition = "";
//        $campaignStatistics = CampaignStatistics::whereIn("type", [1, 2, 4, 5])->where("status", 1)->get(["id"]);
//        $listIds = [];
//        foreach ($campaignStatistics as $camId) {
//            $listIds[] = $camId->id;
//        }
        $query = "select * from videobasic where video_id in (select video_id from promovideos where status =1) $condition";
        $input = ["query" => $query];
        $datas = RequestHelper::callAPI("POST", "http://plla.autoseo.win/query", $input);


        error_log("syncPromosAthena RS: " . count($datas));

        foreach ($datas as $data) {
//                Log::info("$date $videoId $views $likes $dislikes");
            $insert = AthenaPromoSync::where("date", $data->date)->where("video_id", $data->video_id)->first();
            if (!$insert) {
                $insert = new AthenaPromoSync();
                $insert->date = $data->date;
                $insert->video_id = $data->video_id;
                $insert->views = $data->views;
                $insert->likes = $data->likes;
                $insert->dislikes = $data->dislikes;
                $insert->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $insert->save();
            } else {
                $insert->views = $data->views;
                $insert->likes = $data->likes;
                $insert->dislikes = $data->dislikes;
//                $insert->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $insert->save();
            }
        }
        $endTime = time() - $startTime;
        error_log("Finish syncPromosAthena $endTime");
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Finish syncPromosAthena " . round($endTime / 60, 3) . ' minutes');
        return count($datas);
    }

    //tổng kết dữ liệu channel từ bảng athena_sync sang bảng accountinfo
    public function caculateDataAthena(Request $request) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 7200);
        $totalDb = AccountInfo::whereIn("is_music_channel", [1, 2])->where("is_sync", 1)->count();
        if (isset($request->date)) {
            $date = $request->date;
            $datas = DB::select("select date,channel_id,views from athena_data_sync where status = 1 and date = $request->date");
        } else {
            $date = AthenaDataSync::max('date');
        }
        $datas = DB::select("select date,channel_id,views from athena_data_sync where status = 1 and date = $date");
        if (count($datas) / $totalDb * 100 < 75) {
            $maxTime = strtotime($date);
            $date = date("Ymd", $maxTime - 86400);
            $datas = DB::select("select date,channel_id,views from athena_data_sync where status = 1 and date = $date");
        }
        $totalAthen = count($datas);
        if ($totalAthen / $totalDb * 100 >= 75) {
            $count = 0;
            AccountInfo::whereIn("is_music_channel", [1, 2])->update(["increasing" => 0]);
            foreach ($datas as $index => $data) {
                $channel = AccountInfo::where("chanel_id", $data->channel_id)->whereIn("is_music_channel", [1, 2])->first();
                if ($channel) {
                    $channel->increasing = $data->views;
                    $channel->save();
                    $count++;
//                $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
//                if ($infoChannel["status"] == 0) {
//                    for ($t = 0; $t < 10; $t++) {
//                        error_log("caculateDataAthena retry $channel->chanel_id");
//                        $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
//                        if ($infoChannel["status"] == 1) {
//                            break;
//                        }
//                    }
//                }
//                $viewDetailTmp = $channel->view_detail;
//                $per = 0;
//                if (isset($viewDetailTmp)) {
//                    $viewDetails = json_decode($viewDetailTmp);
////                    $last = $viewDetails[count($viewDetails) - 1];
//
//                    if ($channel->increasing != 0) {
//                        $per = round(($data->views - $channel->increasing) / $channel->increasing * 100, 0);
//                    }
//                } else {
//                    $viewDetails = array();
//                }
//                if (count($viewDetails) > 30) {
//                    array_shift($viewDetails);
//                }
//                error_log("caculateDataAthena $index/$total $data->date $data->channel_id $channel->chanel_name $data->views $per");
//                $flag = 0;
//
//                foreach ($viewDetails as $detail) {
//
//                    if (!empty($detail->date)) {
//                        if ($detail->date == $data->date) {
//                            $flag = 1;
//                            $detail->subs = intval($infoChannel["subscribes"]);
//                            $detail->view = intval($infoChannel["views"]);
//                            $detail->daily = $data->views;
//                            $detail->per = $per;
//                        }
//                    }
//                }
//                if ($flag == 0) {
//                    //add view detail
//                    $arrTmpView = array();
//                    $arrTmpView["date"] = $data->date;
//                    $arrTmpView["subs"] = intval($infoChannel["subscribes"]);
//                    $arrTmpView["view"] = intval($infoChannel["views"]);
//                    $arrTmpView["daily"] = $data->views;
//                    $arrTmpView["per"] = $per;
//                    $viewDetails[] = $arrTmpView;
//                }
//
//
//                
//                $channel->daily_change = $per;
//                $channel->view_detail = json_encode($viewDetails);
//                $channel->view_count = intval($infoChannel["views"]);
//                $channel->subscriber_count = intval($infoChannel["subscribes"]);
//                $channel->status = $infoChannel["status"];
//                $channel->save();
                }
            }
            return "success $date $totalAthen/$totalDb: " . round($totalAthen / $totalDb * 100) . "%";
        }
        return "fail $date $totalAthen/$totalDb: " . round($totalAthen / $totalDb * 100) . "%";
    }

    //upload api het quota, thực hiện  retry đến khi có quota
    public function retryApiAutoUploadStudio() {
//        $locker = new Locker(986);
//        $locker->lock();
        $processName = "retryApiAutoUploadStudio";
        if (ProcessUtils::isfreeProcess($processName, 20)) {
            ProcessUtils::lockProcess($processName);
            $datas = Upload::where("status", 7)->take(3)->where("next_time_scan", "<=", time())->get();
            foreach ($datas as $data) {
                $res = RequestHelper::callAPI("GET", "http://api-magicframe.automusic.win/job/loadaaa/$data->source_id", []);
                Logger::logUpload("retryApiAutoUploadStudio: " . $res->id);
                //sử dụng 33 điểm
                if (empty($res->reup_config)) {
                    continue;
                }
                $meta = !json_decode($res->reup_config);
                $link_video = "gdrive;;resource2@soundhex.com;;" . explode(";;", $res->result)[0];
                $link_thumbnail = "gdrive;;resource2@soundhex.com;;" . explode(";;", $res->result)[1];
                $title = $meta->title;
                $description = $meta->description;
                $tag = $meta->tags;

                $channel = AccountInfo::where("chanel_id", $res->channel_id)->first();
                if (!$channel || $channel->note == null || $channel->note == "") {
                    $req = [
                        "id" => $res->id,
                        "status" => 4,
                        "upload_log" => "Not found email on upload system",
                        "upload_status" => 4
                    ];
                    RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                    continue;
                }
                $data->channel_id = $channel->chanel_id;
                if ($channel->api_type == 2) {
                    $check = RequestHelper::callAPI("GET", "https://autoseo.win/api/check/quota/33", []);
                    if ($check == 0) {
                        Logger::logUpload("retryApiAutoUploadStudio studioId = $res->id out of quota API");
//              RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", ["id" => $res->id, "status" => 3]);
                        $data->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                        $data->save();
                        continue;
                    }

                    $url_redirect = "https://videomanager.orfium.com/auth";
                    $client_id = "841180336951-3hk8i45arjftt6rmskftvc518nurlv1u.apps.googleusercontent.com";
                    $client_secret = "YMmwipN2NaUAYgch6recQ8zf";
                    $channelCode = $channel->channel_code_orfium;
                } else if ($channel->api_type == 3) {
                    $url_redirect = "http://127.0.0.1:64980";
                    $client_id = "419933649938-kmi7m4gqamh7htlv5vr7hamq73j71c7h.apps.googleusercontent.com";
                    $client_secret = "7N7FTB37hK6dh6kSKoe2Q08K";
                    $channelCode = $channel->channel_code_camta;
                } else
                if ($channel->api_type == 4) {
                    //upload bằng api adobe
                    $url_redirect = "https://oobe.adobe.com";
                    $client_id = "18481039836-hj9vcetv2vu599uhnfn2nrada9pck659.apps.googleusercontent.com";
                    $client_secret = "sFOf-BkaM6JfWjiN2EQtzDfo";
                    $channelCode = $channel->channel_code_adobe;
                }
                $apiUpload = (object) [
                            "gmail" => $channel->note,
                            "url" => $link_video,
                            "status" => "public",
                            "thumb_url" => $link_thumbnail,
                            "title" => $title,
                            "description" => $description,
                            "tags" => $tag,
                            "default_language" => "en",
                            "url_redirect" => $url_redirect,
                            "client_id" => $client_id,
                            "client_secret" => $client_secret,
                            "channel_code" => $channelCode
                ];
//            Logger::logUpload("retryApiAutoUploadStudio api_$channel->api_type: " . json_encode($apiUpload));
////                    Utils::write("apiupload.txt", json_encode($apiUpload));
//            $uploadResult = RequestHelper::callAPI("POST", "http://65.21.108.148/pll/video/insert/", $apiUpload);
//            Logger::logUpload("retryApiAutoUploadStudio api_$channel->api_type: " . json_encode($uploadResult));


                $ips = ["65.21.108.148", "5.161.67.99", "65.108.164.119"];
                $ip = $ips[rand(0, count($ips) - 1)];
                Logger::logUpload("retryApiAutoUploadStudio api_$channel->api_type: ip=$ip " . json_encode($apiUpload));
                $uploadResult = RequestHelper::callAPI("POST", "http://" . $ip . "/pll/video/insert/", $apiUpload);
                Logger::logUpload("retryApiAutoUploadStudio api_$channel->api_type: ip=$ip " . json_encode($uploadResult));
                if ($uploadResult->status == 1) {
                    $data->log = $uploadResult->video_id;
                    $data->status = 3;
                } else {
                    $data->log = $uploadResult->error_message;
                    $data->status = 4;
                }
                $data->retry = $data->retry + 1;
                $data->save();

                $req = [
                    "id" => $data->source_id,
                    "status" => 5,
                    "upload_log" => $data->log,
                    "upload_status" => $data->status
                ];
                RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                Logger::logUpload("retryApiAutoUploadStudio api_$channel->api_type: use api update to studio " . json_encode($req));

                //check xem có phải lệnh cross post không, nếu là cross post thì update vào bảng cross_post
                $crossPost = CrossPost::where("job_id", $data->source_id)->first();
                if ($crossPost) {
                    $crossPost->video_id = $data->log;
                    $crossPost->save();
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            Logger::logUpload("$processName is locked");
        }
    }

    //check autoupload tu studio.automusic.win
    public function checkAutoUploadStudio() {
//        $locker = new Locker(985);
//        $locker->lock();
        $processName = "checkAutoUploadStudio";
        if (ProcessUtils::isfreeProcess($processName, 30)) {
            ProcessUtils::lockProcess($processName);
            $uploads = Upload::where("status", 0)->whereNotNull("bas_id")->where("retry", "<", 3)->where("next_time_scan", "<=", time())->orderBy("id", "desc")->take(3000)->get();
            $total = count($uploads);
            Logger::logUpload("checkAutoUploadStudio $total");
            foreach ($uploads as $index => $upload) {
                Logger::logUpload("$index/$total checkAutoUploadStudio http://bas.reupnet.info/job/load/$upload->bas_id => $upload->source_id $upload->type");
                $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$upload->bas_id", []);
                if ($res->id != 0) {
                    $channel = AccountInfo::where("chanel_id", $upload->channel_id)->first();
                    if (!$channel) {
                        $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                        $upload->log = "Not found channel info";
                        $upload->status = 6;
                        $upload->save();
                        continue;
                    }
                    $videoId = "";
                    //kiểm tra xem kênh đã login moonshots chưa

                    if ($channel->gologin == null) {
                        continue;
                    }
                    $script_name = 'upload';
                    $func_name = 'comment';
                    $type = 610;
                    $name = "studio_comment_moon";
                    $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($res->result)));
                    if ($res->status == 4) {
                        $upload->status = $res->status;
                        foreach ($results as $result) {
                            if ($result != null && $result != "") {
                                $temp = json_decode($result);
                                if (Utils::containString($result, "login")) {
                                    $log = $temp->result;
                                    if ($log != "true") {
                                        $upload->log = $log;
                                        if (Utils::containString($log, "ERR_CONNECTION_CLOSED") || Utils::containString($log, "Failed to get proxy ip") || Utils::containString($log, "ERR_CERT_COMMON_NAME_INVALID")) {
                                            $upload->status = 0;
                                            $upload->log = $log . " runing again";
                                            $update = [
                                                "id" => $res->id,
                                                "status" => 0
                                            ];
                                            RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", $update);
                                            $upload->retry = $upload->retry + 1;
                                        }
                                        break;
                                    }
                                }
                                if (Utils::containString($result, "reupload") || Utils::containString($result, "upload")) {
                                    $log = $temp->result;
                                    $upload->log = $log;
                                    if (Utils::containString($log, "ERR_CONNECTION_CLOSED") || Utils::containString($log, "Failed to get proxy ip") || Utils::containString($log, "ERR_CERT_COMMON_NAME_INVALID")) {
                                        $upload->status = 0;
                                        $upload->log = $log . " runing again";
                                        $update = [
                                            "id" => $res->id,
                                            "status" => 0
                                        ];
                                        RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", $update);
                                        $upload->retry = $upload->retry + 1;
                                    }
                                    $currDate = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                    $t = time();
                                    //lệnh bị ăn gậy
                                    if (Utils::containString($log, "Copyright strikes")) {
                                        //nếu có gậy trong 3 ngày gần nhất thì ko thêm vào strikes nữa, vi có thể là bị strike rôi nhưng hệ thống vẫn lên lệnh upload
                                        $check = Strikes::whereRaw("$t - date < 3 *86400 ")->where("channel_id", $channel->chanel_id)->first();
                                        if (!$check) {
                                            $strikes = new Strikes();
                                            $strikes->date = strtotime($res->created);
                                            $strikes->date_text = $res->created;
                                            $strikes->type = 1;
                                            $strikes->channel_id = $channel->chanel_id;
                                            $strikes->channel_name = $channel->chanel_name;
                                            $strikes->gmail = $res->gmail;
                                            $strikes->strike = "Copyright strikes";
                                            $strikes->save();
                                            $channel->strikes = "This channel got Copyright strike";
                                            $channel->save();
                                        }
                                    } else if (Utils::containString($log, "Community strikes")) {
                                        $check = Strikes::whereRaw("$t - date < 3 *86400 ")->where("channel_id", $channel->chanel_id)->first();
                                        if (!$check) {
                                            $strikes = new Strikes();
                                            $strikes->date = strtotime($res->created);
                                            $strikes->date_text = $res->created;
                                            $strikes->type = 2;
                                            $strikes->channel_id = $channel->chanel_id;
                                            $strikes->channel_name = $channel->chanel_name;
                                            $strikes->gmail = $res->gmail;
                                            $strikes->strike = "Community strikes";
                                            $strikes->save();
                                            $channel->strikes = "This channel got Community strike";
                                            $channel->save();
                                        }
                                    }
                                    break;
                                }
                                //upload tiktok
                                if (Utils::containString($result, "tiktok")) {
                                    $log = $temp->result;
                                    $upload->log = $log;
                                }
                            }
                        }
                        $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                        $upload->save();
                        if ($upload->type == "studio" || $upload->type == "studio_moon") {
                            $req = [
                                "id" => $upload->source_id,
                                "status" => 5,
                                "upload_log" => $upload->log,
                                "upload_status" => $res->status
                            ];
                            RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                            Logger::logUpload("checkAutoUploadStudio update to studio " . json_encode($req));
                        }
                    } else if ($res->status == 3 || $res->status == 2 || $res->status == 1 || $res->status == 0) {

                        if ($upload->next_time_scan < time()) {
                            $upload->next_time_scan = time();
                        }
                        if ($upload->type == "studio_comment") {
                            $upload->next_time_scan = $upload->next_time_scan + 3600;
                        } else {
                            $upload->next_time_scan = $upload->next_time_scan + 3 * 60;
                        }
                        $upload->save();
                    } else if ($res->status == 5) {
                        Logger::logUpload("$res->id result: $res->result");
                        if ($res->result == null || $res->result == "") {
                            $upload->status = 4;
                            $upload->log = "Error extention no result";
                            $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                            $upload->save();
                            $req = [
                                "id" => $upload->source_id,
                                "status" => 4,
                                "upload_log" => $upload->log,
                                "upload_status" => 4
                            ];
                            RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                            Logger::logUpload("checkAutoUploadStudio update to studio " . json_encode($req));
                            continue;
                        }
                        foreach ($results as $result) {
                            $temp = json_decode($result);
                            if (Utils::containString($result, "reupload") || Utils::containString($result, "upload")) {
                                $videoId = $temp->result;
                            }
                            $stt = 5;
                            if ($videoId != null && $videoId != "") {
                                $upload->status = $stt;
                                $upload->log = $videoId;
                                $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $upload->save();
                            } else {
                                $stt = 4;
                                $upload->status = $stt;
                                $upload->log = "Error extention";
                                $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $upload->save();
                            }
                            if ($upload->type == "studio" || $upload->type == "studio_moon") {
                                $req = [
                                    "id" => $upload->source_id,
                                    "status" => 5,
                                    "upload_log" => $upload->log,
                                    "upload_status" => $stt
                                ];
                                RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
                                Logger::logUpload("checkAutoUploadStudio update to studio " . json_encode($req));
                            }
                        }

                        if (($upload->type == "studio" || $upload->type == "studio_moon") && $videoId != "" && $videoId != null) {
                            //tạo lệnh auto comment nếu upload thành công
                            Logger::logUpload("UPload thanh cong $res->id $res->status");
                            $studio = RequestHelper::callAPI("GET", "http://api-magicframe.automusic.win/job/loadaaa/$upload->source_id", []);

                            $meta = json_decode($studio->reup_config);

                            $autoComment = false;
                            if (!empty($meta->auto_comment)) {
                                $autoComment = $meta->auto_comment;
                            }

                            if ($autoComment) {
                                $comment = "";
                                if (!empty($meta->comment)) {
                                    $comment = $meta->comment;
                                }
                                $isPinComment = false;
                                if (!empty($meta->is_pin_comment)) {
                                    $isPinComment = $meta->is_pin_comment;
                                }

                                if ($meta->schedule == null) {
                                    $schedule = null;
                                } else {
                                    $schedule = $meta->schedule;
                                }

                                $taskLists = [];
                                $login = (object) [
                                            "script_name" => "profile",
                                            "func_name" => "login",
                                            "params" => []
                                ];

                                $paramsComment = [];
                                $listParamComment = ["description" => $comment, "video_source" => $upload->log, "category" => $isPinComment ? "PIN" : ""];
                                foreach ($listParamComment as $key => $value) {
                                    $param = (object) [
                                                "name" => $key,
                                                "type" => "string",
                                                "value" => $value
                                    ];
                                    $paramsComment[] = $param;
                                }

                                //thêm handle để phân biệt channel khi upload
                                $param5 = (object) [
                                            "name" => "handle",
                                            "type" => "string",
                                            "value" => $channel->handle];
                                $paramsComment[] = $param5;
                                $pinComment = (object) [
                                            "script_name" => $script_name,
                                            "func_name" => $func_name,
                                            "params" => $paramsComment
                                ];
                                $taskLists[] = $login;
                                $taskLists[] = $pinComment;

                                //tính giờ chạy comment
                                $runTime = 0;
                                if ($schedule == null || $schedule == '{}') {
                                    $runTime = time();
                                } else {
                                    $runTime = strtotime("$schedule->date $schedule->time $schedule->time_zone");
                                }

                                $req = (object) [
                                            "gmail" => $res->gmail,
                                            "task_list" => json_encode($taskLists),
                                            "run_time" => $runTime + 3600,
                                            "type" => $type,
                                            "studio_id" => $upload->source_id,
                                            "piority" => 30
                                ];
                                Logger::logUpload("checkAutoUploadStudio COMMENT req:" . json_encode($req));
                                $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                                Logger::logUpload("checkAutoUploadStudio COMMENT res:" . json_encode($bas));
                                $uploadStd = new Upload();
                                $uploadStd->type = $name;
                                $uploadStd->status = 1;
                                $uploadStd->source_id = $upload->source_id;
                                $uploadStd->channel_id = $studio->channel_id;
                                if ($bas->mess == "ok") {
                                    $uploadStd->bas_id = $bas->job_id;
                                }
                                $uploadStd->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $uploadStd->save();
                            }
                        }

                        if ($videoId != null && $videoId != "") {
                            //auto set endcard
                            $taskLists = [];
                            $paramsCard = [];
                            $paramsEndScreen = [];
                            $param1 = (object) [
                                        "name" => "headers",
                                        "type" => "json",
                                        "value" => "{}",
                            ];
                            $param2 = (object) [
                                        "name" => "params",
                                        "type" => "json",
                                        "value" => "{}",
                            ];
                            $param3 = (object) [
                                        "name" => "method",
                                        "type" => "string",
                                        "value" => "POST",
                            ];
                            $param4 = (object) [
                                        "name" => "response_check",
                                        "type" => "string",
                                        "value" => "EDIT_EXECUTION_STATUS_DONE",
                            ];
                            $param5 = (object) [
                                        "name" => "url",
                                        "type" => "string",
                                        "value" => "https://studio.youtube.com/youtubei/v1/video_editor/edit_video?alt=json&key=AIzaSyBUPetSUmoZL-OhlxA7wSac5XinrygCqMo",
                            ];
                            $param6 = (object) [
                                        "name" => "get_start_ms",
                                        "type" => "func",
                                        "value" => json_encode((object) ["video_id" => $upload->log]),
                            ];
                            if (!empty($meta->auto_card) && $meta->auto_card) {
                                if (!empty($meta->payload_card)) {
                                    $paramsCard[] = $param1;
                                    $paramsCard[] = $param2;
                                    $paramsCard[] = $param3;
                                    $paramsCard[] = $param4;
                                    $paramsCard[] = $param5;
                                    $paramsCard[] = $param6;
                                    $infoCardsString = str_replace("@@video_id", $upload->log, json_encode($meta->payload_card));
                                    $paramsCard[] = (object) [
                                                "name" => "payload",
                                                "type" => "json",
                                                "value" => ($infoCardsString)
                                    ];
                                    $taskListCard = (object) [
                                                "script_name" => "request",
                                                "func_name" => "edit_card",
                                                "params" => $paramsCard
                                    ];
                                    $taskLists[] = $taskListCard;
                                }
                            }
                            if (!empty($meta->auto_endscreen) && $meta->auto_endscreen) {
                                if (!empty($meta->payload_endscreen)) {
                                    $paramsEndScreen[] = $param1;
                                    $paramsEndScreen[] = $param2;
                                    $paramsEndScreen[] = $param3;
                                    $paramsEndScreen[] = $param4;
                                    $paramsEndScreen[] = $param5;
                                    $paramsEndScreen[] = $param6;
                                    $infoEndScreenString = str_replace("@@video_id", $upload->log, json_encode($meta->payload_endscreen));
                                    $paramsEndScreen[] = (object) [
                                                "name" => "payload",
                                                "type" => "json",
                                                "value" => $infoEndScreenString
                                    ];
                                    $taskListEndScreen = (object) [
                                                "script_name" => "request",
                                                "func_name" => "edit_card",
                                                "params" => $paramsEndScreen
                                    ];
                                    $taskLists[] = $taskListEndScreen;
                                }
                            }

                            if (count($taskLists) > 0) {
                                $req = (object) [
                                            "gmail" => $res->gmail,
                                            "task_list" => json_encode($taskLists),
                                            "run_time" => time() + 1800,
                                            "type" => 613,
                                            "studio_id" => $upload->source_id,
                                            "piority" => 80
                                ];
                                Logger::logUpload("checkAutoUploadStudio CARD_ENDSCREEN req:" . json_encode($req));
                                $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                                Logger::logUpload("checkAutoUploadStudio CARD_ENDSCREEN res:" . json_encode($bas));
                                $uploadStd = new Upload();
                                $uploadStd->type = "studio_moon_card";
                                $uploadStd->status = 1;
                                $uploadStd->source_id = $upload->source_id;
                                $uploadStd->channel_id = $studio->channel_id;
                                if ($bas->mess == "ok") {
                                    $uploadStd->bas_id = $bas->job_id;
                                }
                                $uploadStd->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $uploadStd->save();
                            }
                        }

                        //check xem có phải lệnh cross post không, nếu là cross post thì update vào bảng cross_post
                        $crossPost = CrossPost::where("job_id", $upload->source_id)->first();
                        if ($crossPost) {
                            $crossPost->video_id = $upload->log;
                            $crossPost->save();
                        }
                    }
                } else {
                    Logger::logUpload("checkAutoUploadStudio resonse null $upload->bas_id");
                    $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                    $upload->log = "Not found bas info";
                    $upload->status = 6;
                    $upload->save();
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            Logger::logUpload("$processName is locked");
        }
    }

//    //thêm promo_videos vào accountinfo để check vị trí wakeup và đồng thời thêm vào campaign
//    public function addPromosByList() {
//        $videos = ["MVAdUS6DIgk"];
//        $promos = CampaignStatistics::where("type", 1)->where("status", 1)->whereNotNull("artist")->whereNotNull("song_name")->get();
//        foreach ($videos as $videoId) {
//
//            for ($t = 0; $t < 15; $t++) {
//                error_log("addPromosByList Retry $videoId");
//                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($videoId);
//                error_log(json_encode($videoInfo));
//                if ($videoInfo["status"] == 1) {
//                    $channelId = $videoInfo["channelId"];
//                    $accountInfo = AccountInfo::where("chanel_id", $channelId)->first();
//                    if ($accountInfo) {
//                        $promoVideo = $accountInfo->promo_videos;
//                        if ($promoVideo != null) {
//                            $array = explode(",", $promoVideo);
//                            array_push($array, $videoId);
//                            $temp = array_unique($array);
//                            $accountInfo->promo_videos = implode(",", $temp);
//                        } else {
//                            $accountInfo->promo_videos = $videoId;
//                        }
//                        $accountInfo->save();
//                        error_log("addPromosByList $promoVideo => $accountInfo->promo_videos");
//
//                        //check video mới nhất xem có video nào thuộc campaign mà chưa được add thì add vào
//
//                        $pos = strripos($accountInfo->user_name, '_');
//                        $manager = substr($accountInfo->user_name, 0, $pos);
//                        $listSongName = json_decode($videoInfo["song_name"]);
//                        $listArtist = json_decode($videoInfo["artists"]);
//                        $listLicenses = json_decode($videoInfo["license"]);
//                        $duration = intval($videoInfo["length"]);
//                        $curr = time();
//                        $videoType = 2;
//                        if ($duration <= 90) {
//                            $videoType = 6;
//                        } else if ($duration > 90 && $duration <= 8 * 60) {
//                            $videoType = 2;
//                        } else {
//                            $videoType = 5;
//                        }
//                        foreach ($promos as $promo) {
//                            $checkCam = Campaign::where("video_id", $videoId)->where("campaign_id", $promo->id)->first();
//                            if (!$checkCam) {
//                                if (in_array($promo->song_name, $listSongName) && in_array($promo->artist, $listArtist)) {
//                                    $campaign = new Campaign();
//                                    $campaign->username = $manager;
//                                    $campaign->campaign_id = $promo->id;
//                                    $campaign->campaign_name = $promo->campaign_name;
//                                    $campaign->channel_id = $videoInfo["channelId"];
//                                    $campaign->channel_name = $videoInfo["channelName"];
//                                    $campaign->video_type = $videoType;
//                                    $campaign->video_id = $videoId;
//                                    $campaign->video_title = $videoInfo["title"];
//                                    $campaign->views_detail = '[]';
//                                    $campaign->status = $videoInfo["status"];
//                                    $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
//                                    $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
//                                    $campaign->publish_date = $videoInfo["publish_date"];
//                                    $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
//                                    $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
//                                    $campaign->log = "$campaign->create_time System added";
//                                    $campaign->status_confirm = 1;
//                                    $campaign->save();
//                                    error_log("addPromosByList auto add campaign $videoId to campaign $promo->id");
//                                }
//                            } else {
//                                error_log("addPromosByList $videoId exists on campaign $checkCam->id");
//                            }
//                        }
//                    } else {
//                        error_log("addPromosByList $videoId is not belong any channel");
//                    }
//                    break;
//                }
//            }
//        }
//    }
    //check xem da brand xong chưa
    public function checkMakeBrand() {
        $locker = new Locker(983);
        $locker->lock();
        $datas = RebrandChannelCmd::where("status", 1)->get();
        foreach ($datas as $data) {

            $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$data->bas_id", []);
//            Log::info(json_encode($res));
            if ($res->id != 0) {
                if ($res->status == 4 || $res->status == 5) {
                    $data->status = $res->status;
                    $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($res->result)));
                    foreach ($results as $result) {
//                        Log::info($result);
                        $temp = json_decode($result);
                        if (Utils::containString($result, "login")) {
                            $log = $temp->result;
                            if ($log != "true") {
                                $data->log = $log;
                                if (Utils::containString($log, "ERR_CONNECTION_CLOSED")) {
                                    $data->status = 0;
                                    $data->log = $log . " runing again";
                                    $update = [
                                        "id" => $res->id,
                                        "status" => 0
                                    ];
                                    RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", $update);
                                    $data->retry = $data->retry + 1;
                                }
                                break;
                            }
                        }
                        if (Utils::containString($result, "rebrand") || Utils::containString($result, "brand")) {
                            $log = $temp->result;
                            $data->log = $log;
                            if (Utils::containString($log, "ERR_CONNECTION_CLOSED")) {
                                $data->status = 0;
                                $data->log = $log . " runing again";
                                $update = [
                                    "id" => $res->id,
                                    "status" => 0
                                ];
                                RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", $update);
                                $data->retry = $data->retry + 1;
                            }
                            break;
                        }
                    }
                    $data->last_update_time = gmdate("d/m/Y H:i:s", time() + 7 * 3600);
                    $data->save();
                    $accountInfo = AccountInfo::where("chanel_id", $data->channel_id)->first();
                    if ($accountInfo) {
                        if ($res->status == 5) {
                            $accountInfo->chanel_name = $data->first_name;
                        }
                        if ($res->status != 0) {
                            $accountInfo->is_rebrand = $data->status;
                        }
                        $accountInfo->gmail_log = $data->log;
                        $accountInfo->save();
                    }
                }
                Log::info("checkMakeBrand http://bas.reupnet.info/job/load/$data->bas_id => $data->source_id status=$res->status");
            } else {
                $data->status = 3;
                $data->save();
            }
        }
    }

    //sửa lỗi đếm số lượng video upload hàng ngày
    public function fixCountVideo() {
        $channels = AccountInfo::where("is_music_channel", 1)->get();
        foreach ($channels as $channel) {
            $count_video = json_decode($channel->count_video);
            for ($i = count($count_video) - 1; $i > 0; $i--) {
                $b = $count_video[$i]->count;
                $a = $count_video[$i - 1]->count;
                $daily = ($b - $a > 0 ? ($b - $a) : 0);
                $count_video[$i]->count_daily = $daily;
            }
            Log::info(json_encode($count_video));
            $channel->count_video = json_encode($count_video);
            $channel->save();
        }
    }

    //tim song tren youtube bang ten bai hat
    public function searchVideoByApi(Request $request) {
//        Log::info('|searchVideoByApi|request=' . json_encode($request->all()));
        return YoutubeHelper::searchVideoByApi($request->keyword);
    }

    function msg(Request $request) {
//        Log::info('|msg|request=' . json_encode($request->all()));
        $msg = $request->msg;
        if ($msg == null || $msg == "") {
            $msg = "Error";
        }
        if ($request->type == 1) {
            //m
            $msgsEx = ['cho', 'cut'];
            foreach ($msgsEx as $tem) {
                if (Utils::containString($msg, $tem)) {
                    return array("status" => "error", "message" => "Không được chửi láo");
                }
            }
            RequestHelper::callAPI("GET", "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=" . urlencode($msg), []);
        } else {


            RequestHelper::callAPI("GET", "https://api.telegram.org/bot8011589399:AAG24EFuLCvEom4027CuOqIUVpm0J3Jf6Ak/sendMessage?chat_id=-4555657860&text=" . urlencode($msg), []);
        }
        return array("status" => "success", "message" => "Success");
    }

    function msghx(Request $request) {
//        Log::info('|msg|request=' . json_encode($request->all()));
        $msg = $request->msg;
        if ($msg == null || $msg == "") {
            $msg = "Error";
        }
        if ($request->type == 1) {
            //m
            RequestHelper::callAPI("GET", "https://api.telegram.org/bot5660679827:AAFBeMTXmzTqLro9HLYPBMCnV2eBTcNjzVQ/sendMessage?chat_id=-534277835&text=System restart", []);
        } else {
//            RequestHelper::callAPI("GET","https://api.telegram.org/bot1147415414:AAFzIyRfOOPwncVCfVhAwCYNGXAHGhwkUJI/sendMessage?chat_id=-405810846&text=$msg",[]);
        }
        return array("status" => "Success");
    }

    //lấy list videos bằng playlist_id
    public function getListVideoPlaylist(Request $request) {
        return YoutubeHelper::getPlaylist($request->playlist);
    }

    //kiểm tra xem artists có trong list fuck không
    public function getFuckArtists(Request $request) {
        Log::info('|ApiController.getFuckArtists|request=' . json_encode($request->all()));
//        DB::enableQueryLog();
        $datas = [];
        if (isset($request->deezer_artist_id) && $request->deezer_artist_id != "") {
            $datas = FuckLabelArtists::where("deezer_artists_id", $request->deezer_artist_id)->where("status", 1)->get();

            if (count($datas) > 0) {
                return 0;
            }
        }
        if (isset($request->spotify_artists_id) && $request->spotify_artists_id != "") {
            $datas = FuckLabelArtists::where("spotify_artists_id", $request->spotify_artists_id)->where("status", 1)->get();

            if (count($datas) > 0) {
                return 0;
            }
        }

        return 1;
    }

    //trả về list artists không được upload 
    public function getListFuckArtists() {
        $datas = FuckLabelArtists::where("status", 1)->where("type", 2)->get();
        return $datas;
    }

    public function deleteVideo(Request $request) {
        Log::info('|deleteVideo.index|request=' . json_encode($request->all()));
        $datas = json_decode($request->data);
        foreach ($datas as $data) {
            $channel = Accountinfo::where("chanel_id", $data->channel_id)->first();
            return YoutubeHelper::deleteVideo($channel, $data->video_id);
        }
    }

    //xoa alteremail cho kenh da tao api thanh cong
    public function deleteEmail(Request $request) {
        Log::info('|deleteVideo.index|request=' . json_encode($request->all()));
//        $datas = AccountInfo::where("api_status", 2)->whereNotNull("note")->where("version", 1)->where("is_music_channel", 2)->where("chanel_id","UCvAwMTPVjRChwUjEc9KA58w")->get();
        $datas = AccountInfo::whereIn("api_job_id", [193156, 193158, 193159])->get();
        $taskList = '[{"script_name":"profile","func_name":"login","params":[]},{"script_name":"profile","func_name":"remove_email","params":[]}]';
        foreach ($datas as $index => $data) {
            $req = (object) [
                        "gmail" => $data->note,
                        "task_list" => $taskList,
                        "run_time" => 0,
                        "type" => 13,
                        "piority" => 80
            ];
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            error_log("deleteEmail: $data->note $bas->job_id");
//            if ($index > 5) {
//                break;
//            }
        }
    }

    //scan views crosspost
    public function scanCrospost(Request $request) {
        $curr = time();
        if (isset($request->date)) {
            $date = $request->date;
        } else {
            $date = gmdate("Y-m-d", $curr + 7 * 3600);
        }
        $datas = CrossPost::whereNotNull("video_id")->where("status", 1)->get();
        $total = count($datas);

        $listVideoId = [];
        $count = 0;
        foreach ($datas as $index => $data) {
            $listVideoId[] = trim($data->video_id);
            if (count($listVideoId) >= 50 || $index == ($total - 1)) {
                $jData = YoutubeHelper::getStatics(implode(",", $listVideoId));
                error_log("|scanCrospost $count |getStatics=" . count($listVideoId));
                if (isset($jData)) {
                    $items = $jData->items;
                    foreach ($items as $item) {
                        $vidID = $item->id;
                        $views = !empty($item->statistics->viewCount) ? $item->statistics->viewCount : 0;
                        $likes = !empty($item->statistics->likeCount) ? $item->statistics->likeCount : 0;
                        $date = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                        if ($views > 0) {
                            $cross = CrossPost::where("video_id", $vidID)->first();
                            $daily = ($views - $cross->views);
                            $cross->views = $views;
                            $cross->daily_views = $daily;
                            $cross->likes = $likes;
                            $cross->save();
                        }
                        $count++;
                    }
                }
                $listVideoId = [];
            }
        }
    }

    //assign api orfium để download bulk
    public function createOrfiumApi() {
        $channels = AccountInfo::whereIn("id", [298956])->get();
        Log::info("createOrfiumApi " . count($channels));
        foreach ($channels as $channel) {
            $taskLists = '[{"script_name":"profile","func_name":"login","params":[]},{"script_name":"api","func_name":"orfium_auth","params":[]}]';
            $req = (object) [
                        "gmail" => $channel->note,
                        "task_list" => $taskLists,
                        "run_time" => 0,
                        "type" => 12,
                        "piority" => 80
            ];
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            error_log("createOrfiumApi: $channel->note " . json_encode($bas));
            if ($bas->mess == "ok") {
                $channel->api_job_id = $bas->job_id;
                $channel->save();
            }
        }
    }

    //add api orfium
    public function addOrfiumApi(Request $request) {
        Log::info('|addOrfiumApi|request=' . json_encode($request->all()));
        $date = gmdate("Y-m-d H:i:s", time() + 7 * 3600);
        if ($request->api_type == 2) {
            $log = PHP_EOL . "$date add api orrfium";
            return AccountInfo::where("chanel_id", $request->channel_id)->update(["channel_code_orfium" => $request->channel_code_orfium,
                        "api_type" => $request->api_type,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
        } else if ($request->api_type == 3) {
            $log = PHP_EOL . "$date add api camta";
            return AccountInfo::where("chanel_id", $request->channel_id)->update(["channel_code_camta" => $request->channel_code_camta,
                        "api_type" => $request->api_type,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
        } else if ($request->api_type == 4) {
            $log = PHP_EOL . "$date add api adobe";
            return AccountInfo::where("chanel_id", $request->channel_id)->update(["channel_code_adobe" => $request->channel_code_adobe,
                        "api_type" => $request->api_type,
                        "log" => DB::raw("CONCAT(log,'$log')")]);
        }
        return 0;
    }

    //lấy danh sách subgenren bằng genre
    public function loadSubgenre(Request $request) {
        return $this->loadChannelSubGenre($request);
    }

    //check so luong sub thay doi theo gio, tinh ra %
    //check 3h/lần, 
    public function scanSubHour() {
        error_log(__FUNCTION__);
        $locker = new Locker(982);
        $locker->lock();
        $channels = AccountInfo::whereIn("is_music_channel", [2, 1])->where("turn_off_hub", 0)->get();
        $i = 0;
        $total = count($channels);
        foreach ($channels as $channel) {
            $i++;
            $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
            if ($infoChannel["status"] == 0) {
                for ($t = 0; $t < 2; $t++) {
                    error_log("scanSubHour retry $channel->chanel_id");
                    $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
                    if ($infoChannel["status"] == 1) {
                        break;
                    }
                }
            }
            if ($infoChannel["status"] == 1) {
                $oldSub = $channel->subscriber_count;
                $newSub = intval($infoChannel["subscribers"]);
                $hourSub = $newSub - $oldSub;
                if ($oldSub != 0) {
                    $channel->sub_percent = round(($newSub - $oldSub) / $oldSub * 100, 2);
                }
                $channel->view_count = intval($infoChannel["views"]);
                $channel->subscriber_count = intval($infoChannel["subscribers"]);
                $channel->save();
                //thong bao cho ng dung
                if (($channel->subscriber_count <= 10000 && $channel->subscriber_count >= 500 && $channel->sub_percent > 100) || $hourSub >= 500) {
                    //kênh đã add vào boomvip thì ko báo
                    if ($channel->is_boomvip == 0 || $channel->is_boomvip == 2) {
                        $user = User::where("user_code", $channel->user_name)->first();
                        $info = explode("@@", $user->telegram_id);
                        if (count($info) >= 2) {
                            $message = "$user->user_name có kênh $channel->chanel_name tăng $hourSub subs ($channel->sub_percent%), hãy kiểm tra";
                            $url = "https://api.telegram.org/bot$info[0]/sendMessage?chat_id=-$info[1]&text=$message";
                            ProxyHelper::get($url, 2);
                        }
                    }
                }
                error_log("scanSubHour $i/$total $channel->chanel_id subs=$channel->subscriber_count per=$channel->sub_percent");
            }
        }
        return 1;
    }

    //api xóa playlist thừa trên kênh
    public function deletePlaylist(Request $request) {
        if ($request->channel_id == "" || $request->playlist_id == "") {
            return 0;
        }
        $channel = AccountInfo::where("chanel_id", $request->channel_id)->first();
        if (!$channel) {
            return 0;
        }
        $taskLists = [];
        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $taskLists[] = $login;
        $delPlaylist = (object) [
                    "script_name" => "playlist",
                    "func_name" => "del_playlist",
                    "params" => [
                        (object) [
                            "name" => "playlist_id",
                            "type" => "string",
                            "value" => $request->playlist_id,
                        ]
                    ]
        ];
        $taskLists[] = $delPlaylist;
        $req = (object) [
                    "gmail" => $channel->note,
                    "task_list" => json_encode($taskLists),
                    "run_time" => 0,
                    "type" => 3,
                    "piority" => 10
        ];
        Log::info("delete playlist " . json_encode($req));
        $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        Log::info("delete playlist " . json_encode($res));
        return 1;
    }

    public function restartApache2() {
        $count = shell_exec("ps aux | grep apache | wc -l");
        $number = (trim($count));
//        RequestHelper::callAPI2("GET", "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Apache2 $number", []);
        if ($count >= 220) {
            $countPro = LockProcess::where("status", 1)->update(["status" => 0]);
            Log::info("restartApache2=$number update process = $countPro");
            RequestHelper::callAPI2("GET", "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Restarted Apache2 $number", []);
            shell_exec("sudo service apache2 restart");
        }
        return 1;
    }

    //ham fix lỗi Failed to parse Content-Range header
    public function fixClientHeader() {
        $ips = ["65.108.164.119", "65.21.108.148", "5.161.67.99"];
        foreach ($ips as $ip) {
            $fix = RequestHelper::getRequest("http://$ip/pll/fix");
//            Log::info("fixClientHeader $ip $fix");
        }
    }

    //hàm lấy danh sách bài hat trong campaign đang active theo deezer_artist_id
    public function getSongByDeezerArtistId(Request $request) {
        $limit = 3;
        if (isset($request->number)) {
            $limit = $request->number;
        }
        $datas = DB::select("select id,deezer_artist_id,song_name,distributor,audio_url,audio_url_cut from campaign_statistics where status =1 and type in(1,2,4,5) and audio_url_cut is not null and deezer_artist_id=$request->deezer_artist_id limit $limit");
        $count = count($datas);
        if ($count == 0) {
            return [];
        }
        if ($datas[0]->distributor != null) {
            if ($count < $limit) {
                //nếu chưa đủ số lượng cần lấy ,lấy thêm bài hát cùng distributor cho đủ số lượng
                $check = DB::select("select count(*) as total from campaign_statistics where status =1 and type in(1,2,4,5) and audio_url_cut is not null and deezer_artist_id=$request->deezer_artist_id and distributor='" . $datas[0]->distributor . "'");
                Log::info("getSongByDeezerArtistId " . $check[0]->total);
                if ($check[0]->total > 0) {
                    $listId = [];
                    foreach ($datas as $data) {
                        $listId[] = $data->id;
                    }
                    $ids = implode(",", $listId);
                    $limit = $limit - $count;
                    $temp = DB::select("select id,deezer_artist_id,song_name,distributor,audio_url,audio_url_cut from campaign_statistics where id not in ($ids) and status =1 and type in(1,2,4,5) and audio_url_cut is not null and distributor='" . $datas[0]->distributor . "' order by rand() limit $limit");
                    $datas = array_merge($datas, $temp);
                }
            }
        }
        return $datas;
    }

    public function getSongByGroup(Request $request) {
        $limit = 2;
        if (isset($request->number)) {
            $limit = $request->number;
        }
        $datas = DB::select("select id,deezer_artist_id,song_name,revshare_client,audio_url,audio_url_cut,group_music from campaign_statistics where status =1 and type in(1,4) and audio_url_cut is not null and group_music='$request->group' order by rand() limit $limit");
        $count = count($datas);
        if ($count == 0) {
            return [];
        }
        return $datas;
    }

    public function getApiInfo(Request $request) {
        return ApiManager::where("net", $request->net)->first();
    }

    //scan tên kênh từ comment
    public function scanComment() {
        $videoLatin = ["S8ZUB9z8OGA", "LxOTsiV4tkQ", "ARWg160eaX4", "TmKh7lAwnBI", "p38WgakuYDo", "w2C6RhQBYlg", "Nk8C9FdCdJQ", "10EX-_h4pYc", "Cr8K88UcO0s", "saGYMhApaH8", "doLMt10ytHY", "UWV41yEiGq0"];
        $videoPop = ["b1kbLwvqugk", "e-ORhEE9VVg", "-0uRcwk818Y", "b7QlX3yR2xs", "Odh9ddPUkEY", "tollGa3S0o8", "aXzVF3XeS8M", "nfWlot6h_JM", "QcIy9NiNbmo", "IdneKLhsWOQ", "-CmadmM5cOk"];
        $videoRap = ["xvZqHgFz51I", "3V8aen7Flhs", "bVp6RCNIwjk", "oGSBfE3VUns", "nQ1mmVG-G1g", "Z9AnasYjtvg", "Dc6QAUUkuZM", "G3u6hBw2JFk", "xyMdPAEjT-0", "NvKjO-8DusE", "ALimx-H8C6s", "jWu_GqHxmWQ"];
        $videos['LATIN'] = $videoLatin;
        $videos['POP'] = $videoPop;
        $videos['RAP'] = $videoRap;
        $genre = "RAP";
        foreach ($videos["$genre"] as $index => $video) {
            $datas = YoutubeHelper::getInfoComment($video);
            error_log("scanComment $index " . count($datas));
            foreach ($datas as $data) {
                $insert = ChannelComment::where("channel_name", $data->name)->first();
                if (!$insert) {
                    $insert = new ChannelComment();
                    $insert->genre = $genre;
                    $insert->video_id = $video;
                    $insert->channel_name = $data->name;
                    $insert->channel_id = $data->channel_id;
                    $insert->avatar_youtube = $data->thumb;
                    $insert->save();
                }
            }
        }
    }

    public function processBanner(Request $request) {
        $genre = "";
        if (isset($request->genre)) {
            $genre = $request->genre;
        }
        $datas = ChannelComment::whereNull("avatar_direct")->where("genre", $genre)->take($request->number)->where("subs", "<", 1000)->get();
//        $datas = ChannelComment::whereNull("banner_direct")->where("subs", "<", 1000)->where("status",4)->get();
        $count = count($datas);
        $i = 0;
        foreach ($datas as $data) {
            $i++;
            $info = YoutubeHelper::getChannelInfoV2($data->channel_id);
            if (isset($info["status"]) && $info["status"] == 1) {
                $data->subs = $info["subscribers"];
                $data->avatar_youtube = $info["avatar"];
                $data->banner_youtube = $info["banner"];
                $data->save();
            }
            try {
                if ($data->subs < 1000) {
                    if ($data->banner_youtube != "") {
                        $filename = "new_channel/banner_$data->id.jpg";
                        copy($data->banner_youtube, $filename);
                        $data->banner_direct = "https://automusic.win/$filename";
                    } else {
                        $data->banner_direct = "";
                    }
                    if ($data->avatar_youtube != "") {
                        $filename = "new_channel/avatar_$data->id.jpg";
                        copy($data->avatar_youtube, $filename);
                        $data->avatar_direct = "https://automusic.win/$filename";
                    } else {
                        $data->avatar_direct = "";
                    }
                    $data->save();
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }

            error_log("processBanner $genre $i/$count $data->id banner" . ($data->banner_direct == "" ? "->fail" : "->success") . " avatar" . ($data->avatar_direct == "" ? "->fail" : "->success"));
        }
    }

    //chay đổi thông tin mail mới,
    public function runMail() {
        $mails = AccountInfo::where("user_name", "kakalot_emailtrang_run_1682410448")->whereNull("bas_new_id")->take(130)->get();
//        $mails = AccountInfo::where("user_name", "autowin_nguyencuong_pre_turn1")->whereNull("bas_new_id")->take(1)->get();
        $total = count($mails);
        $i = 0;
        foreach ($mails as $mail) {
            $i++;
            $login = (object) [
                        "script_name" => "profile",
                        "func_name" => "login",
                        "params" => []
            ];
            $profileCommit = (object) [
                        "script_name" => "profile",
                        "func_name" => "profile_commit",
                        "params" => []
            ];
            $changeInfo = (object) [
                        "script_name" => "profile",
                        "func_name" => "change_info",
                        "params" => []
            ];
            $taskLists = [];
            $taskLists[] = $login;
            $taskLists[] = $profileCommit;
            $taskLists[] = $changeInfo;
            $req = (object) [
                        "gmail" => $mail->note,
                        "studio_id" => $mail->id,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => 666,
                        "studio_id" => 0,
                        "piority" => 50,
//                        "is_proxy6" => 1,
//                        "is_bot" => 1,
                        "call_back" => ""
            ];
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            error_log("runMail $i/$total " . json_encode($bas));

            $mail->bas_new_id = $bas->job_id;
            $mail->save();
        }
    }

    //giả callback để test hàm callback
    public function fakeCallback(Request $request) {
        $ids = explode(",", $request->ids);
        $callback = $request->callback;
        foreach ($ids as $id) {
            $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$id", []);
            if ($res != null && $res != "" && !empty($res->status)) {
                RequestHelper::callAPI2("POST", $callback, $res);
            }
        }
    }

    //kiểm tra claim direct
    public function claimDirect(Request $request) {
        $campaign = Campaign::where("video_id", $request->video_id)->first();
        if ($campaign) {
            $account = AccountInfo::where("chanel_id", $campaign->channel_id)->first(["note"]);
            $link = "http://65.109.3.200:5002/copyright/check/$account->note/$campaign->video_id";
            $result = RequestHelper::callAPI2("GET", $link, array());
            $claims = [];
            if (isset($result->claims)) {
                if (count($result->claims) > 0) {
                    foreach ($result->claims as $data) {
                        $meta = (object) [];
                        if (isset($data->asset->metadata->soundRecording->title)) {
                            $meta->title = $data->asset->metadata->soundRecording->title;
                        }
                        if (isset($data->asset->metadata->soundRecording->artists)) {
                            $meta->artists = $data->asset->metadata->soundRecording->artists;
                        }
                        if (isset($data->assetId)) {
                            $meta->assetId = $data->assetId;
                        }
                        $claims[] = $meta;
                    }
                    return json_encode($claims);
                }
            }
//            Log::info(json_encode($result)) ;
            return json_encode($result);
        }
        return 0;
    }

    public function syncCookieByVideoId(Request $request) {
        $campaign = Campaign::where("video_id", $request->video_id)->first();
        $us = "truongpv";
        if (isset($request->user)) {
            $us = $request->user;
        }
        if ($campaign) {
            $channel = AccountInfo::where("chanel_id", $campaign->channel_id)->first(["id", "note"]);
            $ch = new ChannelManagementController();
            $cb = "http://automusic.win/callback/sync_cookie?us=$us";
            return $ch->syncCookie($channel, $cb);
        }
        return 0;
    }

    //sync tiktok playlist
    public function syncTiktokPlaylist() {
        
    }

    public function test() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $datas = AccountInfo::where("is_sync", 1)->where("status", 1)->where("subscriber_count", ">=", 5000)->orderBy("subscriber_count", "desc")->get(["id", "user_name", "chanel_id", "chanel_name", "note", "view_count", "subscriber_count"]);
        error_log("test Total:" . count($datas));
        $csv = "id,username,channel_id,channel_name,gmail,views,subs\n";
        foreach ($datas as $data) {
            $channel_id = substr_replace($data->chanel_id, "UU", 0, 2);
            $tmp = YoutubeHelper::getPlaylist($channel_id, 20);
            $videos = $tmp["list_video_id"];
            $claim = 0;
            foreach ($videos as $video) {
                $check = Campaign::where("video_id", $video)->first();
                error_log("test: $video");
                if ($check) {
                    $claim = 1;
                }
            }
            if (!$claim) {
                $csv .= "$data->id,$data->user_name,$data->chanel_id,$data->chanel_name,$data->note,$data->view_count,$data->subscriber_count\n";
            }
        }
        $file = storage_path("channel_no_claim.csv");
        $csv_handler = fopen($file, 'w');
        fwrite($csv_handler, $csv);
        fclose($csv_handler);
        error_log("test finished");
    }

    //api cho fat guy Indiy
    public function faListClaim() {
        Log::info("ApiController.faListClaim");
        $datas = CampaignStatistics::whereIn("distributor", ["51st_State", "Orchard_Indiy", "Indiy", "AdRev_Indiy"])->orderBy("id", "desc")->get(["id as campaign_id", "campaign_name", "distributor", "views", "genre", "label", "official_video", "audio_url", "lyrics"]);
        return response()->json(["status" => "success", "total" => count($datas), "data" => $datas]);
    }

    public function fatClaimDetail(Request $request) {
        Log::info('ApiController.fatClaimDetail|request=' . json_encode($request->all()));
        $claims = CampaignStatistics::whereIn("distributor", ["51st_State", "Orchard_Indiy", "Indiy", "AdRev_Indiy"])->pluck("id")->toArray();
        if (!in_array($request->id, $claims)) {
            return response()->json(["status" => "error", "data" => "Not found"]);
        }
        $datas = Campaign::where("campaign_id", $request->id)->where("video_type", "<>", 1)->where("status_confirm", 3)->orderBy("views", "desc")->get(["campaign_id", "campaign_name", "video_id", "video_title", "views"]);

        return response()->json(["status" => "success", "total" => count($datas), "data" => $datas]);
    }

    public function fatClaimViewMonth(Request $request) {
        Log::info('ApiController.fatClaimViewMonth|request=' . json_encode($request->all()));
        if (!isset($request->period) || !isset($request->campaign_id)) {
            return response()->json(["status" => "error", "data" => "Not found"]);
        }
        $datas = DB::select("select video_id,sum(views_real_daily) as views from athena_promo_sync where date like '%$request->period%' and video_id in (select video_id from campaign where campaign_id = $request->campaign_id and video_type <> 1 and status_confirm = 3) group by video_id order by views desc");
        $total = 0;
        foreach ($datas as $data) {
            $total += $data->views;
        }
        return response()->json(["total" => $total, "videos" => $datas]);
    }

    public function generateHashForChannel() {
        Log::info("generateHashForChannel " . getmypid());
        $time = time();
        $datas = AccountInfo::whereNull("hash_pass")->get(["id", 'chanel_id', 'gologin', "hash_pass"]);
        $total = count($datas);
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Start change hash $total");
        foreach ($datas as $data) {
            $hash = Utils::generateRandomHash(8);
            $data->hash_pass = md5($hash . $data->chanel_id);
            $data->save();
        }
        Log::info("generateHashForChannel finish");
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Finish change hash " . (time() - $time) . 's');
    }

    public function spotifyGetArtistAlbums($id) {
        // API 1: Lấy access token
        $token_url = 'http://source.automusic.win/spotify/token/get';

        $curl1 = curl_init();
        curl_setopt_array($curl1, array(
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $token_response = curl_exec($curl1);
        curl_close($curl1);

        // Kiểm tra lỗi khi gọi API token
        if ($token_response === false) {
            return response()->json(['error' => 'Không thể lấy access token từ API'], 500);
        }

        // Decode JSON response từ API token
        $token_data = json_decode($token_response, true);

        if (!isset($token_data['access_token'])) {
            return response()->json(['error' => 'Access token không tồn tại trong response'], 500);
        }

        $access_token = $token_data['access_token'];

        // API 2: Lấy danh sách album của artist
        $albums_url = "https://api.spotify.com/v1/artists/{$id}/albums?limit=50";

        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $albums_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$access_token}"
            ),
        ));

        $albums_response = curl_exec($curl2);
        $albums_http_code = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
        curl_close($curl2);

        // Kiểm tra lỗi khi gọi Spotify API
        if ($albums_response === false) {
            return response()->json(['error' => 'Không thể lấy danh sách album từ Spotify API'], 500);
        }

        // Trả về kết quả nguyên xi từ Spotify API với header Content-Type phù hợp
        return response($albums_response, $albums_http_code)
                        ->header('Content-Type', 'application/json');
    }

    public function spotifyGetAlbum($id) {
        // API 1: Lấy access token
        $token_url = 'http://source.automusic.win/spotify/token/get';

        $curl1 = curl_init();
        curl_setopt_array($curl1, array(
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $token_response = curl_exec($curl1);
        curl_close($curl1);

        // Kiểm tra lỗi khi gọi API token
        if ($token_response === false) {
            return response()->json(['error' => 'Không thể lấy access token từ API'], 500);
        }

        // Decode JSON response từ API token
        $token_data = json_decode($token_response, true);

        if (!isset($token_data['access_token'])) {
            return response()->json(['error' => 'Access token không tồn tại trong response'], 500);
        }

        $access_token = $token_data['access_token'];

        // API 2: Lấy thông tin album
        $album_url = "https://api.spotify.com/v1/albums/{$id}";

        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $album_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$access_token}"
            ),
        ));

        $album_response = curl_exec($curl2);
        $album_http_code = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
        curl_close($curl2);

        // Kiểm tra lỗi khi gọi Spotify API
        if ($album_response === false) {
            return response()->json(['error' => 'Không thể lấy thông tin album từ Spotify API'], 500);
        }

        // Trả về kết quả nguyên xi từ Spotify API với header Content-Type phù hợp
        return response($album_response, $album_http_code)
                        ->header('Content-Type', 'application/json');
    }

    public function spotifyGetPlaylist($id) {
        // API 1: Lấy access token
        $token_url = 'http://source.automusic.win/spotify/token/get';

        $curl1 = curl_init();
        curl_setopt_array($curl1, array(
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $token_response = curl_exec($curl1);
        curl_close($curl1);

        // Kiểm tra lỗi khi gọi API token
        if ($token_response === false) {
            return response()->json(['error' => 'Không thể lấy access token từ API'], 500);
        }

        // Decode JSON response từ API token
        $token_data = json_decode($token_response, true);

        if (!isset($token_data['access_token'])) {
            return response()->json(['error' => 'Access token không tồn tại trong response'], 500);
        }

        $access_token = $token_data['access_token'];

        // API 2: Lấy thông tin playlist
        $playlist_url = "https://api.spotify.com/v1/playlists/{$id}";

        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $playlist_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$access_token}"
            ),
        ));

        $playlist_response = curl_exec($curl2);
        $playlist_http_code = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
        curl_close($curl2);

        // Kiểm tra lỗi khi gọi Spotify API
        if ($playlist_response === false) {
            return response()->json(['error' => 'Không thể lấy thông tin playlist từ Spotify API'], 500);
        }

        // Trả về kết quả nguyên xi từ Spotify API với header Content-Type phù hợp
        return response($playlist_response, $playlist_http_code)
                        ->header('Content-Type', 'application/json');
    }

}
