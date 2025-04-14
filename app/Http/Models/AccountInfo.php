<?php

namespace App\Http\Models;

use App\Common\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Log;

class AccountInfo extends Model {

    use Sortable;

    protected $table = "accountinfo";
    public $timestamps = false;
//    protected $fillable = [ 'title' ];
    public $sortable = ['confirm_time', 'sub_percent', 'id', 'chanel_name', 'note', 'video_count',
        'subscriber_count', 'view_count', 'increasing', 'status', 'cool_down', 'last_execute_time',
        'number_video_success', 'chanel_create_date', 'status_oauth', 'group_channel_id', 'user_name',
        'channel_type', 'channel_genre', 'status_upload', 'boomvip_time', 'confirm_time'];
    protected $casts = [
        'epid_extra_data' => 'array'
    ];

    const STATUS_PENDING = 'pending';              // Đang chờ admin xem xét
    const STATUS_SENT_EPID = 'sent_epid';    // Đã gửi lên EPID
    const STATUS_EPID_APPROVED = 'approved';  // EPID đã duyệt
    const STATUS_REJECTED = 'rejected';  // EPID từ chối
    const STATUS_EPID_OFF = 'off';  // EPID từ chối

    public function getEpidStatusText() {
        $statusLabels = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT_EPID => 'Sent Epid',
            self::STATUS_EPID_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_EPID_OFF => 'Off'
        ];

        return $statusLabels[$this->epid_status] ?? 'Không xác định';
    }

    public function getExtraValue($key, $default = null) {
        if (is_null($this->epid_extra_data)) {
            return $default;
        }
        return array_key_exists($key, $this->epid_extra_data) ? $this->epid_extra_data[$key] : $default;
    }

    public function setExtraValue($key, $value) {
        $extraData = $this->epid_extra_data ?: [];
        $extraData[$key] = $value;
        $this->epid_extra_data = $extraData;
        return $this;
    }

    public static function updateEpidStatus($id, $status, $reason = null) {
        $channel = self::where('id', $id)->first();
        if (!$channel) {
            return false;
        }

        $channel->epid_status = $status;
        $extraData = $channel->epid_extra_data ?: [];

        if ($status == self::STATUS_PENDING || $status == self::STATUS_EPID_APPROVED) {
            $channel->epid_time = time();
            //ghi nhận viewsub thời điểm hiện tại
            $view = $channel->view_count;
            $sub = $channel->subscriber_count;
            $extraData["view_$status"] = $view;
            $extraData["sub_$status"] = $sub;
        }

        // If reason is provided, add it to the extra data
        if ($reason) {
            $extraData['status_reason'] = $reason;
        }
        $channel->epid_extra_data = $extraData;

        return $channel->save();
    }

    public function calculateRewards() {
        if ($this->epid_status != self::STATUS_EPID_APPROVED) {
            return false;
        }
        $extraData = $this->epid_extra_data ?: [];
        if (isset($extraData['rewards'])) {
            return false;
        }
        $moonCoinReward = 0;
        $cashReward = 0;
        DB::enableQueryLog();
        $views = $this->view_count;
        $subscribers = $this->subscriber_count;
        $inc_view = $views - $this->getExtraValue("view_approved");
//        $inc_sub = $subscribers - $this->getExtraValue("sub_approved");
        $moonCoin = MooncoinContent::where("status", 1)->where("type", "bom_epid")->where("views", "<=", $inc_view)->orderBy("moon_value", "desc")->first();
        Log::info("$this->chanel_id mooncoin " . json_encode($moonCoin));
        $moonId = 0;
        $moonName = "KPI is not achieved";
        if ($moonCoin) {
            $moonId = $moonCoin->id;
            $moonName = $moonCoin->content_description;
            $moonCoinReward = $moonCoin->moon_value;
            $cashReward = $moonCoin->money;
            $mValue = new MooncoinValues();
            $mValue->username = Utils::userCode2userName($this->user_name);
            $mValue->content_id = $moonCoin->id;
            $mValue->mooncoin_value = $moonCoin->moon_value;
            $mValue->month = gmdate("m", time());
            $mValue->year = gmdate("Y", time());
            $mValue->created_by = "system";
            $mValue->created = Utils::timeToStringGmT7(time());
            $mValue->save();
        }
        $rewardInfo = [
            'id' => $moonId,
            'name' => $moonName,
            'moon_coin' => $moonCoinReward,
            'cash' => $cashReward,
            'timestamp' => time(),
            'time' => Utils::timeToStringGmT7(time()),
            'subs' => $subscribers,
            'views' => $views
        ];

        // Mark rewards as given
        $extraData['rewards'] = $rewardInfo;
        $extraData['rewards_given'] = false;
        $this->epid_extra_data = $extraData;
        $this->save();
//        Log::info(DB::getQueryLog());
    }

}
