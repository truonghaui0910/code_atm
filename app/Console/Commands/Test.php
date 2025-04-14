<?php

namespace App\Console\Commands;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\AccountInfo;
use App\Http\Models\AthenaPromoSync;
use App\Http\Models\Bom;
use App\Http\Models\CampaignStatistics;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class Test extends Command {

    /**
     * The name and signature of the console command.
     *
     * cam = campaign_id1,campaign_id2
     * dis = Orchard, Indiy...
     * channels = channel_id1,channel_id2
     */
    protected $signature = 'app:test  {--cam=} {--dis=} {--channels=} {--videos=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        set_time_limit(0);
        Log::info("run commnad");
//        $this->testThread();
        // <editor-fold defaultstate="collapsed" desc="Fake callback">
        // //        $uploads = Upload::where("status", 5)->where("type", "studio_moon")
//                        ->where("create_time", ">=", "2023/11/28 00:00:00")
//                        ->where("log", "<>", "")->where("bas_id","<",7993097)->orderBy("id","desc")->get();
//        $total = count($uploads);
//        $i = 0;
//        foreach ($uploads as $upload) {
//            $i++;
//            error_log("run commnad $i/$total $upload->bas_id");
//            $call = new CallbackController();
//            $request = new Request();
//            $request->type = "upload";
//            $request->bas_id = $upload->bas_id;
//            $call->callbackFake($request);
//        }
// </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Lưu lại views ngày 2024/11/14">
//        $channels = \App\Http\Models\AccountInfo::whereIn("is_music_channel", [1, 2])->where("del_status",0)->get();
//        foreach ($channels as $index => $channel) {
//            $views = new \App\Http\Models\AccountInfoViews();
//            $views->date = "20241117";
//            $views->channel_id = $channel->chanel_id;
//            $views->first_views = $channel->view_count;
//            $views->increasing = $channel->increasing;
//            $views->current_views = $channel->view_count;
//            $views->created = \App\Common\Utils::timeToStringGmT7(time());
//            $views->updated = time();
//            $views->updated_text = \App\Common\Utils::timeToStringGmT7(time());
//            $views->save();
//        }
//        Log::info("Total $index");
// </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="fix views">
//fix AthenaPromoSync bị xóa khi xóa bọn SME, chia views theo % số tiền nhận được
//        $totalAmount = DB::table('campaign_claim_rev')->where("period", ">=", "202403")->where("campaign_id", 844)->sum('revenue');
//        $moneys = CampaignClaimRev::where("period", ">=", "202403")->where("campaign_id", 844)->get();
//        foreach ($moneys as $money) {
//            $money->percent = ($money->revenue / $totalAmount);
//            error_log("$money->period percent $money->percent");
//        }
//        $datas = DB::select("SELECT `video_id`, `views`, `insert_date`, `username`, `id` FROM `campaign` WHERE `campaign_id` in (844) AND `insert_date` < '20240701' AND `insert_date` >= '20240601'");
//        error_log("totalAmount $totalAmount");
//        error_log(json_encode($moneys));
//        foreach ($datas as $data) {
//            $percent = 0;
//
//            for ($i = 3; $i <= 7; $i++) {
//                $d = "0" . $i;
//                if ($i >= 10) {
//                    $d = $i;
//                }
//                $month = "2024$d";
//                $date = "2024$d" . "01";
//                foreach ($moneys as $money) {
//                    if ($money->period == $month) {
//                        $percent = $money->percent;
//                        break;
//                    }
//                }
//                $check = AthenaPromoSync::where("video_id", $data->video_id)->where("date", $date)->first();
//                error_log("$data->video_id $data->views $percent " . round($data->views * $percent));
//                if (!$check) {
//                    $check = new AthenaPromoSync();
//                    $check->views_real_daily = round($data->views * $percent);
//                    $check->date = $date;
//                    $check->video_id = $data->video_id;
//                    $check->create_time = Utils::timeToStringGmT7(time(), "/");
//                    $check->save();
////                    error_log(json_encode($check));
//                } else {
//                    $check->views_real_daily = round($data->views * $percent);
//                    $check->create_time = Utils::timeToStringGmT7(time(), "/");
//                    $check->save();
//                    error_log("$check->video_id dupticate");
//                }
//            }
////            break;
//        }
// </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="đồng bộ lại handle lên social">
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://social.automusic.win/items/owned_channels?limit=1000',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'GET',
//            CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer liSBfojF58Hu_B0KQAjgFhJiaIxAX0Od'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//        curl_close($curl);
//        $results = json_decode($response);
//        foreach ($results->data as $result) {
//            error_log("$result->id $result->channel_id $result->user_id");
//            $acc = AccountInfo::where("chanel_id", $result->channel_id)->first();
//            if ($acc) {
//                $data = (object) [
//                            "id" => $result->id,
//                            "handle" => $acc->handle,
//                ];
//                $social = new SocialController();
//                $social->updateChannel($data);
//                error_log("updated");
//            }
//        }
// </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="lấy view video tự youtube">
        $cam = $this->option('cam');
        $dis = $this->option('dis');
        $channels = $this->option('channels');
        $videos = $this->option('videos');
        $this->scanClaimViewMonth($dis, $cam, $channels, $videos);
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="thêm user lên code.automusic.win">
        //        $this->addUserToCode();
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="download lại nhạc deezer bị lỗi">
//        $this->reDonwnloadBomDeezer();
        // </editor-fold>
    }

    //
    public function scanClaimViewMonth($dis, $cam, $channels, $videos) {
        DB::enableQueryLog();
        $campaignIds = [];
        $campaigns = [];
        $query = DB::table('campaign')->where("video_type", "<>", 1)->where("views", ">=", 0)->where("status_confirm", 3)->orderBy("views", "desc");
        if (isset($dis)) {
            $disArr = explode(",", $dis);
            $campaigns = CampaignStatistics::whereIn("distributor", $disArr)->whereIn("status", [1, 4])->pluck("id");
            error_log(json_encode($campaigns));
            $campaignIds = $campaigns;
        } elseif (isset($cam)) {
            $campaignIdArr = explode(",", $cam);
            $campaignIds[] = $campaignIdArr;
        }
        if (count($campaigns) > 0) {
            $query->whereIn("campaign_id", $campaignIds);
        }
        if (isset($channels)) {
            error_log($channels);
            $channelArr = explode(",", $channels);
            $query->whereIn("channel_id", $channelArr);
        }
        if (isset($videos)) {
            error_log($videos);
            $videoArr = explode(",", $videos);
            $query->whereIn("video_id", $videoArr);
        }
        $datas = $query->get();
        error_log(json_encode(DB::getQueryLog()));
        $countVideoAll = count($datas);
        error_log("total all video: " . $countVideoAll);
        $seenVideoIds = [];
        $uniqueDatas = [];
        foreach ($datas as $data) {
            if (!in_array($data->video_id, $seenVideoIds)) {
                $uniqueDatas[] = $data; // Thêm phần tử vào danh sách kết quả
                $seenVideoIds[] = $data->video_id; // Đánh dấu video_id đã gặp
            }
        }
        $i = 0;
        $today = gmdate("Ymd", time());
        $errorChannel = [];
        $countVideo = count($uniqueDatas);
        error_log("total unix video: " . $countVideo);
        foreach ($uniqueDatas as $data) {
            $i++;
//            error_log("run $i/$countVideo $data->video_id $data->channel_id $data->views");
            $viewsData = [];
            if (!in_array($data->channel_id, $errorChannel)) {
                $channel = AccountInfo::where("chanel_id", $data->channel_id)->first(["note"]);
                $fromDate = $data->insert_date;
                if ($data->publish_date != null) {
                    $fromDate = date("Ymd", $data->publish_date);
                }

                $cmd = "/home/tools/env/bin/python /home/tools/CopyRight.py check_views_month_video $channel->note $data->video_id $fromDate $today";
                error_log("$i/$countVideo " . $cmd);
                $meta = shell_exec($cmd);
                if ($meta != null) {
                    $json = json_decode($meta);
                    if (!empty($json->error)) {
                        $errorChannel[] = $data->channel_id;
                        Utils::write("error_channel.txt", implode(",", $errorChannel));
                        continue;
                    }
                    if (isset($json->cards)) {
                        foreach ($json->cards as $card) {
                            if (isset($card->keyMetricCardData->keyMetricTabs)) {
                                foreach ($card->keyMetricCardData->keyMetricTabs as $metric) {
                                    if (isset($metric->primaryContent->metric) && $metric->primaryContent->metric == "EXTERNAL_VIEWS") {
                                        if (isset($metric->primaryContent->mainSeries->datums)) {
                                            $viewsData = $metric->primaryContent->mainSeries->datums;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $totalViews = 0;
            foreach ($viewsData as $view) {
                if ($view->y > 0) {
                    $totalViews = $totalViews + $view->y;
                    $date = date('Ymd', $view->x / 1000);
                    $check = AthenaPromoSync::where("date", $date)->where("video_id", $data->video_id)->first();
//                    error_log("$date $data->video_id $view->y");
                    if (!$check) {
                        $check = new AthenaPromoSync();
                        $check->date = $date;
                        $check->video_id = $data->video_id;
                    }
                    $check->views_real_daily = $view->y;
                    $check->create_time = Utils::timeToStringGmT7(time(), "/");
                    $check->save();
                }
            }
            error_log("$i/$countVideo finish $data->video_id $data->channel_id $data->channel_name $totalViews/$data->views " . count($viewsData) . " days");
        }
        error_log("error_channel: " . implode(",", $errorChannel));
    }

    //thêm user lên trang code.automusic.win
    public function addUserToCode() {
        $users = User::where("status", 1)->where("role", "like", "%26%")->get(["user_name", "name", "password_plaintext"]);
        foreach ($users as $user) {
            Log::info(json_encode($user));
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://code.automusic.win/users',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '[
                {
                "first_name": "' . $user->name . '",
                "email": "' . $user->user_name . '@moonshots.vn",
                "password": "' . $user->password_plaintext . '",
                "role": "ab3b3bd7-f59e-49ab-bb5c-37751a042ce1"
              }
            ]',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer s8od59lyT9_g4EBO4Tp_yGxmn3DJYqCg'
                ),
            ));

            $response = curl_exec($curl);
            Log::info($response);
            curl_close($curl);
        }
    }

    public function testThread() {
        $dataArray = [
            (object) ['id' => 1, 'name' => 'Object1'],
            (object) ['id' => 2, 'name' => 'Object2'],
            (object) ['id' => 3, 'name' => 'Object3'],
            (object) ['id' => 4, 'name' => 'Object4'],
            (object) ['id' => 5, 'name' => 'Object5'],
            (object) ['id' => 6, 'name' => 'Object6'],
            (object) ['id' => 7, 'name' => 'Object7'],
            (object) ['id' => 8, 'name' => 'Object8'],
            (object) ['id' => 9, 'name' => 'Object9'],
            (object) ['id' => 10, 'name' => 'Object10'],
            (object) ['id' => 11, 'name' => 'Object10'],
            (object) ['id' => 12, 'name' => 'Object10'],
            (object) ['id' => 18, 'name' => 'Object10'],
            (object) ['id' => 29, 'name' => 'Object10'],
            (object) ['id' => 31, 'name' => 'Object10'],
            (object) ['id' => 35, 'name' => 'Object10'],
            (object) ['id' => 45, 'name' => 'Object10'],
        ];

        $numThreads = 3; // Số lượng thread
        $chunks = array_chunk($dataArray, ceil(count($dataArray) / $numThreads)); // Chia công việc thành các phần

        $children = [];

        foreach ($chunks as $threadId => $chunk) {
            $pid = pcntl_fork(); // Tạo process con

            if ($pid === -1) {
                error_log("Không thể tạo process con.");
                return;
            } elseif ($pid === 0) {
                // Đây là process con
                $total = count($chunk); // Tổng số phần tử của thread này
                foreach ($chunk as $index => $data) {
                    $current = $index + 1; // Đánh số thứ tự cho từng phần tử (bắt đầu từ 1)
                    error_log("Thread {$threadId} đang xử lý {$current}/{$total} (ID: {$data->id})\n");
                }

                exit(0); // Kết thúc process con
            } else {
                // Đây là process cha
                $children[] = $pid; // Lưu PID của process con
            }
        }

        // Chờ các process con hoàn thành
        foreach ($children as $child) {
            pcntl_waitpid($child, $status);
        }

        $this->info('Tất cả các thread đã xử lý xong.');
    }

    //tải lại những bài deezer id bị dowload lỗi
    public function reDonwnloadBomDeezer() {
        $datas = Bom::whereNotNull("deezer_id")->where("sync", 0)->take(3000)->orderBy("id", "desc")->get();
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
            //check bài hát dã đươc download về hệ thống chưa
            if ($isSync == 0) {
                RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$deezerId");
            }

            $trackRes = RequestHelper::getRequest("http://54.39.49.17:8031/api/tracks/?deezer_id=$deezerId");
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

            if ($songTitle != null && $songArtist != null) {
                $check = Bom::where("deezer_id", $deezerId)->where("status", 1)->first();
                if ($check) {
                    $check->lyric = $isLyric;
                    $check->sync = $isSync;
                    $check->song_name = $songTitle;
                    $check->artist = $songArtist;
                    $check->log = $check->log . PHP_EOL . "$curr resync";
                    $check->save();
                    error_log("$index/$total $data->id $deezerId $url128");
                }
            }
        }
    }

}
