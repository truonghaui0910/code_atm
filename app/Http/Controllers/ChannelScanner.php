<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\AccountInfoViews;
use App\Http\Models\Notification;
use Illuminate\Support\Facades\DB;
use Log;



/**
 * Class ChannelScanner - Lớp quản lý việc quét kênh YouTube
 */
class ChannelScanner extends Controller{

    /**
     * Khởi tạo quá trình quét kênh
     * 
     * @param object $request Request chứa thông tin threadId
     * @return array Thông tin về thread và danh sách kênh cần quét
     */
    public function initScan($request) {
        error_log(__FUNCTION__);
        $startTime = time();
        $numberThreadInit = 10;
        $threadId = 0;
        
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }
        
        // Khóa thread để đảm bảo không bị chồng chéo
        for ($i = 0; $i < $numberThreadInit; $i++) {
            if ($i == $threadId) {
                $locker = new Locker(intval("9989$i"));
                $locker->lock();
            }
        }
        
        // Gửi thông báo bắt đầu quét
        RequestHelper::getUrl("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Start scan channel 6 floor thread-$threadId", 0);
        
        // Lấy danh sách kênh cần quét
        $channels = AccountInfo::where(DB::raw("id % $numberThreadInit"), $threadId)
            ->whereNotNull("otp_key")
            ->where("del_status", 0)
            ->get();
            
        return [
            'startTime' => $startTime,
            'threadId' => $threadId,
            'channels' => $channels,
            'numberThreadInit' => $numberThreadInit
        ];
    }
    
    /**
     * Lấy thông tin của một kênh từ YouTube
     * 
     * @param object $channel Thông tin kênh từ database
     * @return array Thông tin đã cập nhật và trạng thái quét
     */
    public function getChannelInfo($channel) {
        $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
        // Thử lại tối đa 5 lần nếu có lỗi
        if ($infoChannel["status"] == 2) {
            for ($t = 0; $t < 5; $t++) {
                error_log("getChannelInfo retry $channel->chanel_id");
                $infoChannel = YoutubeHelper::getChannelInfoV2($channel->chanel_id);
                if ($infoChannel["status"] == 1) {
                    break;
                }
            }
        }
//        Log::info(json_encode($infoChannel));
        
        // Cập nhật thông tin kênh nếu có sẵn
        $updatedChannel = clone $channel;
        if ($infoChannel["status"] == 1) {
            if ($infoChannel["channelName"] != null && $infoChannel["channelName"] != '') {
                $updatedChannel->chanel_name = $infoChannel["channelName"];
            }
            if ($infoChannel["handle"] != null && $infoChannel["handle"] != '') {
                $updatedChannel->handle = $infoChannel["handle"];
            }
        }
        
        $currentViews = intval($infoChannel["views"]);
        $currentSub = intval($infoChannel["subscribers"]);
        
        // Xác định trạng thái kênh
        $status = $infoChannel["status"];
        $countScanDie = 0;
        if ($status == 0) {
            $status = 1;
            $countScanDie = 1;
        } elseif ($status == 2) {
            // status = 2 => bị lỗi không scan được chứ ko phải là kênh die
            $status = 1;
        }
        
        return [
            'channel' => $updatedChannel,
            'currentViews' => $currentViews,
            'currentSub' => $currentSub,
            'status' => $status,
            'countScanDie' => $countScanDie,
            'scanStatus' => $infoChannel["status"]
        ];
    }
    
    /**
     * Cập nhật thông tin lượt xem của kênh
     * 
     * @param array $channelData Thông tin kênh và lượt xem hiện tại
     * @return array Thông tin đã cập nhật bao gồm dữ liệu cho ngày hiện tại
     */
    public function updateViewsData($channelData,$forceUpdate = false) {
        $channel = $channelData['channel'];
        $currentViews = $channelData['currentViews'];
        $currentSub = $channelData['currentSub'];
        
        $currday = gmdate("Ymd", time() + 7 * 3600);
        
        // Lấy thời gian lưu gần so với ngày hiện tại nhất để so sánh tính ra view tăng
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
        
        // Cập nhật hoặc tạo mới bản ghi ngày hiện tại
        $currentData = AccountInfoViews::where("date", $currday)
            ->where("channel_id", $channel->chanel_id)
            ->first();
            
        if (!$currentData) {
            $currentData = new AccountInfoViews();
            $currentData->date = $currday;
            $currentData->channel_id = $channel->chanel_id;
            $currentData->first_views = $currentViews;
            $currentData->current_views = $currentViews;
            $currentData->increasing = $lastViews == 0 ? 0 : ($currentViews - $lastViews);
            $currentData->log_views = gmdate("H:i:s", time() + 7 * 3600) . " $currentViews views, $currentData->increasing inc, $currentSub sub";
            $currentData->created = Utils::timeToStringGmT7(time());
            $currentData->updated = time();
            $currentData->updated_text = Utils::timeToStringGmT7(time());
            $currentData->save();
        } else {
            $currentData->current_views = $currentViews;
            $currentData->increasing = $lastViews == 0 ? 0 : (($currentViews - $lastViews));
            $currentData->updated = time();
            $currentData->updated_text = Utils::timeToStringGmT7(time());
            $currentData->log_views = gmdate("H:i:s", time() + 7 * 3600) . " $currentViews views, $currentData->increasing inc, $currentSub sub" . PHP_EOL . $currentData->log_views;
            $currentData->save();
        }
        
        // 2024/02/12 sáng confirm nếu views tăng mà > 0 thì mới update vào increasing
        if ($currentData->increasing > 0 || $forceUpdate) {
            $channel->increasing = $currentData->increasing;
        }
        
        return [
            'channel' => $channel,
            'currentData' => $currentData,
            'lastInc' => $lastInc,
            'currentViews' => $currentViews,
            'currentSub' => $currentSub,
            'status' => $channelData['status'],
            'countScanDie' => $channelData['countScanDie'],
            'scanStatus' => $channelData['scanStatus']
        ];
    }
    
    /**
     * Cập nhật thống kê và trạng thái kênh
     * 
     * @param array $viewsData Dữ liệu về lượt xem của kênh
     * @return array Thông tin kênh đã cập nhật
     */
    public function updateChannelStats($viewsData,$forceUpdate = false) {
        $channel = $viewsData['channel'];
        $currentData = $viewsData['currentData'];
        $lastInc = $viewsData['lastInc'];
        $countScanDie = $viewsData['countScanDie'];
        $scanStatus = $viewsData['scanStatus'];
        $currentViews = $viewsData['currentViews'];
        $currentSub = $viewsData['currentSub'];
        
        // Cập nhật thống kê trong trường intro_outro
        if ($channel->intro_outro != null) {
            $stats = json_decode($channel->intro_outro);
            // 2024/02/12 sáng confirm nếu views tăng mà > 0 thì mới update
            if ($currentData->increasing > 0 || $forceUpdate) {
                if ($lastInc == 0) {
                    $stats->daily_inc_per = 0;
                } else {
                    $stats->daily_inc_per = round((($currentData->increasing - $lastInc) / $lastInc) * 100);
                }
                $stats->daily_inc_view = $currentData->increasing;
                $stats->last_inc_view = $lastInc;
            }
            $stats->daily_scan_time = time();
            if ($scanStatus == 1) {
                $stats->count_scan_die = 0;
            } else {
                $stats->count_scan_die = isset($stats->count_scan_die) ? $stats->count_scan_die + $countScanDie : $countScanDie;
            }
        } else {
            $stats = (object) [
                "daily_inc_view" => $currentData->increasing,
                "last_inc_view" => $lastInc,
                "daily_inc_per" => 0,
                "daily_scan_time" => time(),
                "count_scan_die" => $countScanDie,
            ];
        }
        $channel->intro_outro = json_encode($stats);
        
        // 3 lần scan báo kênh die thì set = die thật
        if ($stats->count_scan_die >= 3) {
            $channel->status = 0;
        } else {
            $channel->status = $viewsData['status'];
        }
        $channel->view_count = $currentViews;
        $channel->subscriber_count = $currentSub;
        $channel->save();
        return [
            'channel' => $channel,
            'stats' => $stats,
            'currentViews' => $viewsData['currentViews'],
            'currentSub' => $viewsData['currentSub']
        ];
    }
    
    /**
     * Tạo thông báo cho kênh có tăng trưởng đáng kể
     * 
     * @param array $statsData Dữ liệu thống kê của kênh
     * @return object Kênh đã cập nhật
     */
    public function createChannelNotification($statsData) {
        $channel = $statsData['channel'];
        $stats = $statsData['stats'];
        
        // Đưa ra thông báo nếu tăng trưởng đáng kể
        if ($stats->daily_inc_per >= 50 && $stats->daily_inc_view >= 5000) {
            // Không thông báo các kênh mà đã có thông báo chưa hết hạn
            $notify = Notification::where('noti_id', $channel->id)
                ->where("end_date", ">", time())
                ->first();
                
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
        
        return $channel;
    }
    
    /**
     * Hoàn thiện cập nhật kênh và lưu vào database
     * 
     * @param object $channel Đối tượng kênh đã cập nhật
     * @param int $currentViews Lượt xem hiện tại
     * @param int $currentSub Số người đăng ký hiện tại
     * @param int $threadId ID của thread hiện tại
     * @return object Kênh đã cập nhật
     */
    public function finalizeChannelUpdate($channel, $currentViews, $currentSub, $threadId) {
        $channel->view_count = $currentViews;
        $channel->subscriber_count = $currentSub;
        $channel->gmail_log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . ' scanchannelmusic' . $threadId;
        
        // Thêm link avatar nếu chưa có
        if (!Utils::containString($channel->channel_clickup, "https://yt3.ggpht.com/")) {
            $thumb = "";
            $thumbItem = RequestHelper::getUrl("https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channel->chanel_id&fields=items/snippet/thumbnails&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
            if ($thumbItem != null && $thumbItem != "") {
                $items = json_decode($thumbItem);
                if (!empty($items->items[0]->snippet->thumbnails->medium->url)) {
                    $thumb = $items->items[0]->snippet->thumbnails->medium->url;
                    $channel->channel_clickup = $thumb;
                }
            }
        }
        
        $channel->save();
        return $channel;
    }
    
    /**
     * Hàm chính để quét tất cả các kênh và cập nhật thông tin
     * 
     * @param object $request Request chứa thông tin threadId
     * @return void
     */
    public function scanAllChannels($request) {
        $scanData = $this->initScan($request);
        $startTime = $scanData['startTime'];
        $threadId = $scanData['threadId'];
        $channels = $scanData['channels'];
        
        $j = 0;
        $total = count($channels);
        foreach ($channels as $channel) {
            $j++;
            
            // Lấy thông tin kênh từ YouTube
            $channelData = $this->getChannelInfo($channel);
            
            // Cập nhật thông tin lượt xem
            $viewsData = $this->updateViewsData($channelData);
            
            // Cập nhật thống kê và trạng thái
            $statsData = $this->updateChannelStats($viewsData);
            
            // Tạo thông báo nếu cần
            $updatedChannel = $this->createChannelNotification($statsData);
            
            // Hoàn thiện cập nhật
            $finalChannel = $this->finalizeChannelUpdate(
                $updatedChannel, 
                $statsData['currentViews'], 
                $statsData['currentSub'], 
                $threadId
            );
            
            error_log("scanchannelmusic-$threadId $j/$total $finalChannel->chanel_id sub={$statsData['currentSub']} v={$statsData['currentViews']} inc={$viewsData['currentData']->increasing} ");
        }
        
        // Gửi thông báo hoàn thành
        $totalTime = time() - $startTime;
        RequestHelper::getUrl("https://api.telegram.org/bot1219728711:AAFhXltFn829Vuw1Lf20JLnR4dIkeKSH3kc/sendMessage?chat_id=-400738833&text=Finish scan channel 6 floor thread-$threadId $totalTime s,$total channels ", 0);
    }
}
