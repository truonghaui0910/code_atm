<?php

namespace App\Http\Controllers;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\AccountInfo;
use App\Http\Models\BomGroups;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\Categorydata;
use App\Http\Models\Genre;
use App\Http\Models\GroupChannel;
use App\Http\Models\Languagehelper;
use App\Http\Models\Locationdata;
use App\Http\Models\MSVendor;
use App\Http\Models\MusicConfig;
use App\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function trans;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    function __construct() {
        
    }

    protected function loadCategory(Request $request) {
        $datas = Categorydata::all();
        $lstOption = "<option value='1' selected>" . trans('label.valueDefault') . "</option>";
        foreach ($datas as $data) {
            $lstOption .= "<option value='" . $data->category_code . "'>" . $data->category_name . "</option>";
        }

        return $lstOption;
    }

    protected function loadLocation(Request $request) {
        $datas = Locationdata::orderBy('location_name')->get();
        $lstOption = "<option " . ($request->location_data == -1 ? 'selected' : '') . "value='1' selected>" . trans('label.valueDefault') . "</option>";
        foreach ($datas as $data) {
            $lstOption .= "<option " . ($request->location_data == $data->location_data ? 'selected' : '') . "value='" . $data->location_data . "'>" . $data->location_name . "</option>";
        }
        return $lstOption;
    }

    protected function loadLanguage() {
        $datas = Languagehelper::all();
        $lstOption = "<option value='1' selected>" . trans('label.valueDefault') . "</option>";
        foreach ($datas as $data) {
            $lstOption .= "<option value='" . $data->code . "'>" . $data->name . "</option>";
        }
        return $lstOption;
    }

    protected function loadDataForReupConfig(Request $request) {
//        $separate = Config::get('config.separate_text');
        $user = Auth::user();

        $lstChanel = "<option  value='-1'>" . trans('label.value.select') . "</option>";

        //lấy user_code từ user_name
//        $userCode = DB::select("SELECT user_code FROM accountreup WHERE user_name='" . $user->user_name . "' and status = 1");
        //lấy tất cả các kênh dã được add vào user
        $dataAll = DB::select("SELECT chanel_id,chanel_name,note,group_channel_id FROM accountinfo WHERE user_name='" . $user->user_code . "' and status=1 and del_status =0 order by group_channel_id");

        //các kênh đã được sử dụng
        $dataUse = DB::select("SELECT channel_id FROM autoreup WHERE user_name='" . $user->user_name . "' and status in(0,1,2,10,11,12,13)");

        //lấy danh sách group_channel
        $groupChannel = $this->loadGroupChannel();

        //lấy lệnh đã được chọn theo id,dùng cho edit
        $id = 0;
        if (isset($request->id)) {
            $id = $request->id;
        }
        $musicConfig = new MusicConfig();
        if ($id > 0) {
            $musicConfig = MusicConfig::where('id', $id)->where('user_name', $user->user_name)->first();
        }

        //danh sách kênh
        $i = 0;
        foreach ($dataAll as $data) {
            $flag = 1;
            foreach ($dataUse as $check) {
                if ($data->chanel_id == $check->channel_id) {
                    $flag = 0;
                }
            }
            if ($flag == 1) {
                $groupName = '';
                foreach ($groupChannel as $group) {
                    if ($group->id == $data->group_channel_id) {
                        $groupName = $group->group_name . ' | ';
                    }
                }
                $selected = '';
                if ($musicConfig->channel_id == $data->chanel_id) {
                    $selected = 'selected';
                }
                $lstChanel .= "<option $selected value='" . $data->chanel_id . "'>" . $groupName . $data->chanel_name . "</option>";
                $i++;
//            Log::info($data->note);
            }
        }

        //danh sách music_type
        $arr_music_type = array("Single", "Mix");
        $list_music_type = '';
        for ($i = 1; $i <= 2; $i++) {
            $selected = '';
            if ($musicConfig->type == $i) {
                $selected = 'selected';
            }
            $list_music_type .= "<option $selected value='" . $i . "'>" . $arr_music_type[$i - 1] . "</option>";
        }

        //danh sách source_type
        $arr_source_type = array("Youtube & Deezer", "Database");
        $list_source_type = '';
        for ($i = 1; $i <= 2; $i++) {
            $selected = '';
            if ($musicConfig->source_type == $i) {
                $selected = 'selected';
            }
            $list_source_type .= "<option $selected value='" . $i . "'>" . $arr_source_type[$i - 1] . "</option>";
        }

        //source
        $source_text = "";
        $prefix = "";
        if ($musicConfig->source != null && $musicConfig->source != '') {
            $arraySource = json_decode($musicConfig->source);
            foreach ($arraySource as $s) {
                if ($s->type == 0 || $s->type == 7) {
                    $prefix = "";
                } else if ($s->type == 1) {
                    $prefix = "https://www.youtube.com/playlist?list=";
                } else if ($s->type == 2) {
                    $prefix = "https://www.youtube.com/channel/";
                } else if ($s->type == 3) {
                    $prefix = "https://www.youtube.com/watch?v=";
                }

                foreach ($s->data as $link) {
                    $source_text .= $prefix . $link . PHP_EOL;
                }
            }
        }
        //sort
        $list_sort = '';
        $selected = '';
        $sortArray = json_decode($musicConfig->sort);

        $value = ["view", "like", "dislike", "pd"];
        $label = ["Views", "Likes", "Dislikes", "Public Date"];
        $ck_sort_video = 0;
        $sort_order_selected = 'desc';
        foreach ($value as $key => $temp) {
            if ($musicConfig->sort != null) {
                if ($sortArray->$temp != null) {
                    $selected = 'selected';
                    $ck_sort_video = 1;
                    $sort_order_selected = $sortArray->$temp;
                }
            }
            $list_sort .= "<option $selected value='" . $temp . "'>" . $label[$key] . "</option>";
            $selected = '';
        }
        $list_order = '';
        $value = ["desc", "asc"];
        $label = ["Descending", "Ascending"];
        foreach ($value as $key => $temp) {
            if ($temp == $sort_order_selected) {
                $selected = 'selected';
            }
            $list_order .= "<option $selected value='" . $temp . "'>" . $label[$key] . "</option>";
            $selected = '';
        }

        return array($lstChanel, $i, $musicConfig, $id, $list_music_type,
            $list_source_type, $source_text, $ck_sort_video, $list_sort, $list_order);
    }

    protected function loadDataImage(Request $request) {
        $offset = 0;
        $sql = '';
        if (isset($request->bg_type)) {
            $sql = "select topic,artists from image where type = $request->bg_type group by topic,artists";
        }
        if (isset($request->datas)) {
            $sql = "select id,topic,link  from image where type = $request->bg_type and " . (($request->bg_type == 1 || $request->bg_type == 6) ? " topic " : " artists ") . "in ('" . implode("','", $request->datas) . "') and type <> 3 and type <> 5";
        }
        if (isset($request->offset)) {
            $offset = $request->offset;
            $sql .= ' limit 30 offset ' . $offset;
        }
//        Log::info($sql);
        return DB::select($sql);
    }

    protected function genStatusMusic(Request $request) {
        $value = array('-1', '0', '1', '2', '3', '4', '10');
        $label = array(trans('label.value.select'), "New", "Scanned", "Running", "Stopped", "Error", "Wait");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c2) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusChannel(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), trans('label.value.dead'), trans('label.value.normal'));
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c2) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusComment(Request $request) {
        $value = array('-1', '0', '1', 4, 5);
        $label = array(trans('label.value.select'), "Off", "Auto", "Error", "Success");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status_cmt) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function loadGroupChannel() {
        $user = Auth::user();
        if (in_array('1', explode(",", $user->role))) {
            $data = GroupChannel::all();
        } else {

            $data = GroupChannel::where("user_name", $user->user_name)->orderBy('id', 'desc')->get();
        }
        return $data;
    }

    protected function loadGroupChannelForSeach(Request $request) {
        $user = Auth::user();
        $option = '';
        if (in_array('20', explode(",", $user->role))) {
            $datas = GroupChannel::all();
        } else {
            $datas = GroupChannel::where("user_name", $user->user_name)->orderBy('id', 'desc')->get();
        }
        $req_id = '-1';
        if (isset($request->c3)) {
            $req_id = $request->c3;
        }
        if ($req_id == '-1') {
            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
        } else {
            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
        }
        if ($req_id == 0) {
            $option .= "<option selected value='0'>NO_GROUP</option>";
        } else {
            $option .= "<option  value='0'>NO_GROUP</option>";
        }
        foreach ($datas as $data) {
            $usname = "$data->user_name | ";
            if ($data->id == $req_id) {
                $option .= "<option  selected value='$data->id'>$data->group_name</option>";
            } else {
                $option .= "<option  value='$data->id'>$data->group_name</option>";
            }
        }
        return $option;
    }

    protected function genLimit(Request $request) {
        $value = array('10', '20', '30','40', '50', '100', '200', '500', '1000');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->limit) {
                $option .= "<option  selected value='$value[$i]'>$value[$i] per page</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$value[$i] per page</option>";
            }
        }
        return $option;
    }

    protected function genStatusMusicSpotify(Request $request) {
        $value = array('-1', '1', '0');
        $label = array(trans('label.value.select'), "Yes", "No",);
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c2) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genSpotifyPlaylistType(Request $request) {
        $datas = DB::select("select type from spotifymusicplaylist group by type");
        $option = "";
        if ($request->c3 == '-1') {
            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
        } else {
            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
        }
        foreach ($datas as $data) {
            if ($data->type == $request->c3) {
                $option .= "<option  selected value='$data->type'>$data->type</option>";
            } else {
                $option .= "<option  value='$data->type'>$data->type</option>";
            }
        }
        return $option;
    }

    protected function genStatusCampaign(Request $request) {
        $value = array('1', '0', '2', '4', '3');
        $label = array("Active", "Finished", "Upcoming", 'Pause', "All");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    //lay danh sach outro
    protected function genOutro(User $user) {
        if (in_array('1', explode(",", $user->role))) {
            $sql = "select mang,url from contentmusic b where b.content_period =4 and b.status =1 and b.mang in (select a.topic from claim_topic a where a.status =1 and a.topic_type =4)";
        } else {
            $sql = "select mang,url from contentmusic b where b.content_period =4 and b.status =1 and b.mang in (select a.topic from claim_topic a where a.status =1 and a.topic_type =4 and a.share_type = 1 or (a.share_type = 2 and a.share_user like '%$user->user_name%'))";
        }
        $db = DB::select($sql);
//        $option = "<option  value='-1'>Select</option>";
        $option = "";
//        Log::info(json_encode($db));
        foreach ($db as $data) {
//            $option .= "<option data-img-src='http://51.75.243.130/resize.php?filename=".str_replace(".avi", ".jpg", $data->url)."' value='$data->url'>$data->mang</option>";
            $option .= "<option data-img-src='" . str_replace(".avi", ".jpg", $data->url) . "' value='$data->url'>$data->mang</option>";
        }
//        Log::info($option);
        return $option;
    }

    protected function genStatusApiAthena(Request $request) {
        $value = array('-1', '-2', '0', '1', '2', '4');
        $label = array(trans('label.value.select'), 'Chưa đặt lệnh', 'Đã đặt lệnh tạo API', 'Đang tạo API', 'Tạo API thành công', 'Tạo API lỗi');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c4) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusBassNew(Request $request) {
        $value = array('-1', '0', '1', '5', '4');
        $label = array(trans('label.value.select'), 'Chưa đặt lệnh', 'Đang chạy', 'Thành công', 'Lỗi');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->bas_new_status) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genChannelManageType(Request $request) {
        $value = array('-1', '1', '2');
        $label = array(trans('label.value.select'), 'Manual', 'Auto');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c6) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genChannelUploadType(Request $request) {
        $value = array('-1', '1', '2');
        $label = array(trans('label.value.select'), 'Manual', 'Auto');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c7) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genChannelWakeupType(Request $request) {
        $value = array('-1', '1', '2');
        $label = array(trans('label.value.select'), 'Manual', 'Auto');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->c8) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genListUserForMoveChannel(User $user, Request $request, $type = 1, $musicUser = 0, $showSelect = 1, $selectedAll = 0, $isImage = 0, $isCountJob = 0) {
        //type =1: show user_name ở value
        //type= 2: show user_code ở value
//      $musicUser =1 chỉ show user music
//      $musicUser =2 chỉ show user role 16
//      $musicUser =3 chỉ show user role 26 (calendar)
        //showSelect = 1 show tùy chọn select đầu tiên
        //showSelect = 0 ko show tùy chọn select đầu tiên
        //selectedAll = 1 tự động chọn hết các option
        //selectedAll = 0 ko tự động chọn hết các option
        //$isImage = 0 không show ảnh đại diện ở đầu        
        //$isImage = 1 show ảnh đại diện ở đầu     
        //$isCountJob = 1 đếm số lượng job đang có trên calendar

        if ($request->is_admin_music || $request->is_admin_calendar) {
            if ($musicUser == 0) {
                $datas = User::all();
            } else if ($musicUser == 1) {
                $datas = User::where("role", "like", "%11%")->where("status", 1)->orderBy("user_name")->get();
            } elseif ($musicUser == 2) {
                $datas = User::where("role", "like", "%16%")->where("status", 1)->orderBy("user_name")->get();
            } elseif ($musicUser == 3) {
                $datas = User::where("role", "like", "%26%")->where("status", 1)->orderBy("user_name")->get();
            }
        } else if (in_array('23', explode(",", $user->role))) {
            if ($musicUser == 2) {
                $datas = User::where("role", "like", "%16%")->where("status", 1)->orderBy("user_name")->get();
            } else {
                $datas = User::where("user_name", \Auth::user()->username)->get();
            }
        } else {
            $datas = User::where("user_name", \Auth::user()->username)->get();
        }
        if ($showSelect) {
            $lstOption = "<option " . (($request->c5 == -1 || !isset($request->c5)) ? 'selected ' : '') . "value='-1' >" . trans('label.value.select') . "</option>";
        } else {
            $lstOption = "";
        }
        if ($isCountJob) {
            $jobs = DB::select("SELECT username, count(id) as job_count
                                FROM `campaign_jobs`
                                WHERE `status` != '3' AND `del_status` = '0' and job_group is null
                                group by username");
        }
        foreach ($datas as $data) {
            $jobText = "";
            if ($isCountJob) {
                foreach ($jobs as $job) {
                    if ($job->username == $data->user_name) {
                        $jobText = '<span class=\'font-12 text-muted font-italic\'>' . ($job->job_count == 1 ? "1 job - " : "$job->job_count jobs - ") . '</span>';
                        break;
                    }
                }
            }
            $lastOnline = "";
            if ($isImage) {
                $lastOnline = " <span class='font-12 text-muted font-italic'>" . Utils::calcTimeText($data->last_activity) . "</span>";
                $label = "$data->user_name $jobText$lastOnline";
            } else {
                $label = $data->user_name;
                if ($data->description != '') {
                    $label = $data->user_name . ' | ' . $data->description;
                }
            }
            if ($selectedAll == 0) {
                $selected = '';
                if ($request->c5 == ($type == 2 ? $data->user_code : $data->user_name)) {
                    $selected = 'selected';
                }
            } else {
                $selected = 'selected';
            }
            if (!$isImage) {
                $lstOption .= "<option $selected value='" . ($type == 1 ? $data->user_name : $data->user_code) . "'>" . $label . "</option>";
            } else {
                $lstOption .= "<option $selected  data-content=\"<img class='rounded-circle img-cover m-r-5 disp-inline h-40px w-40px mw-40px' src='images/avatar/$data->user_name.jpg' onerror='this.onerror=null;this.src=&quot;images/default-avatar.png&quot;;' > $label\" value='" . ($type == 1 ? $data->user_name : $data->user_code) . "'></option>";
            }
        }
        return $lstOption;
    }

    protected function genDateSelect(Request $request) {
        $maxDay = gmdate("d", time() + (7 * 3600) - 86400);
        $mothYear = gmdate("Y-m", time() + (7 * 3600) - 86400);
        $option = '<option value="-1">Select</option>';
        for ($i = $maxDay; $i > 0; $i--) {
            $selected = '';
            $d = $i;
            if ($i < 10) {
                $d = '0' . $i;
            }
            $date = "$mothYear-$d";
            if ($date == $request->date) {
                $selected = "selected";
            }
            $option .= "<option $selected value'$date'>$date</option>";
        }
        return $option;
    }

    protected function genMonthSelect() {
        $option = '<option value="-1">Select</option>';
        for ($i = 12; $i >= 0; $i--) {
            $date_str = gmdate('M-Y', strtotime("- $i months"));
            $date_number = gmdate('Ym', strtotime("- $i months"));
            if ($i == 0) {
                $option .= "<option selected value=$date_number>" . $date_str . "</option>";
            } else {
                $option .= "<option value=$date_number>" . $date_str . "</option>";
            }
        }
        for ($i = 1; $i < 12; $i++) {
            $date_str = gmdate('M-Y', strtotime("+ $i months"));
            $date_number = gmdate('Ym', strtotime("+ $i months"));
            $option .= "<option value=$date_number>" . $date_str . "</option>";
        }
        return $option;
    }

    protected function genMonthSelectV2() {

        $months = [];
        $curMonth = gmdate("m", time());
        $curYear = gmdate("Y", time());
        $lastRage = $curMonth - 12;
        for ($i = (int) $curMonth; $i >= $lastRage; $i--) {
            $m = $i;
            $y = $curYear;
            if ($i <= 0) {
                $m = 12 + $i;
                $y = $curYear - 1;
            }
            if ($m < 10) {
                $m = "0$m";
            }

            $months[] = "$y$m";
        }
//        Log::info(json_encode($months));
        $option = '';
        foreach ($months as $month) {
            $monthStr = gmdate('M-Y', strtotime($month . "01"));
            $option .= "<option value=$month>$monthStr</option>";
        }
        return $option;
    }

    protected function genDateSelectWeek(Request $request) {

        $listSunday = [];
        $lastMonth = date("m") - 1;
        $nextMonth = date("m") + 1;
        $begin = new DateTime("2022-$lastMonth-01");
        $end = new DateTime("2022-$nextMonth-01");
        while ($begin <= $end) { // Loop will work begin to the end date 
            if ($begin->format("D") == "Sun") { //Check that the day is Sunday here
                $listSunday[] = $begin->format("Y-m-d");
            }
            $begin->modify('+1 day');
        }

        $option = '<option value="-1">Select</option>';
        for ($i = count($listSunday) - 1; $i >= 0; $i--) {
            $selected = '';
            $sun = $listSunday[$i];
            if ($sun == $request->date) {
                $selected = "selected";
            }
            $option .= "<option $selected value'$sun'>$sun</option>";
        }
        return $option;
    }

    protected function genCountryChartmetric(Request $request) {
        $country = DB::select("SELECT code,name FROM `plcountry` WHERE `code` IN ('us','gb','ca','mx','au','jp','es','fr','de','kr','br','in','id','it','nl','ph','ru','se','th','tr','vn') order by field(code,'us','gb','ca','mx','au','jp','es','fr','de','kr','br','in','id','it','nl','ph','ru','se','th','tr','vn')");
        $option = '';
//        $option = '<option value="-1">Select</option>';
        foreach ($country as $co) {
            if ($co->code == $request->country) {
                $option .= "<option  selected value='$co->code'>$co->name</option>";
            } else {
                $option .= "<option  value='$co->code'>$co->name</option>";
            }
        }
        return $option;
    }

    protected function loadChannelGenre(Request $request) {
        $option = '';
//        $datas = DB::select("select distinct(channel_genre) as channel_genre from accountinfo where channel_genre is not null and is_music_channel in(1,2) and is_automusic_v2 =1");
        $datas = DB::select("select name as channel_genre from genre where type =1 and status =1 order by order_id asc");
        $req = '-1';
        if (isset($request->channel_genre)) {
            $req = $request->channel_genre;
        }
        if ($req == '-1') {
            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
        } else {
            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
        }
        foreach ($datas as $data) {
            if ($data->channel_genre == $req) {
                $option .= "<option  selected value='$data->channel_genre'>$data->channel_genre</option>";
            } else {
                $option .= "<option  value='$data->channel_genre'>$data->channel_genre</option>";
            }
        }
        return $option;
    }

    protected function loadChannelSubGenre(Request $request) {
        $option = '';
        $genre = '-1';
        $condition = '';
        if (isset($request->channel_genre)) {
            $genre = $request->channel_genre;
        }
        $req = '-1';

        if (isset($request->channel_subgenre)) {
            $req = $request->channel_subgenre;
        }
        if ($genre != "-1") {
            $gen = Genre::where("type", 1)->where("name", $genre)->first();
            $condition = "and parent_id = $gen->id";
        }

//        $datas = DB::select("select distinct(channel_genre) as channel_genre from accountinfo where channel_genre is not null and is_music_channel in(1,2) and is_automusic_v2 =1");
        $datas = DB::select("select name as channel_subgenre from genre where type =2 and status =1 $condition order by name");


//        if ($req == '-1') {
//            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
//        } else {
//            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
//        }
        foreach ($datas as $data) {
            if ($data->channel_subgenre == $req) {
                $option .= "<option  selected value='$data->channel_subgenre'>$data->channel_subgenre</option>";
            } else {
                $option .= "<option  value='$data->channel_subgenre'>$data->channel_subgenre</option>";
            }
        }
        return $option;
    }

    protected function loadChannelTags(Request $request, $isSearch = 0) {
        $user = Auth::user();
        $option = '';
//        $datas = DB::select("select distinct(channel_genre) as channel_genre from accountinfo where channel_genre is not null and is_music_channel in(1,2) and is_automusic_v2 =1");
        $datas = DB::select("select tag as tags from channel_tags where username in ('system','$user->user_name') and status =1 order by id desc");
        $req = [];
        if (isset($request->tags)) {
            $req = $request->tags;
        }
//        if ($req == '-1') {
//            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
//        } else {
//            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
//        }
        foreach ($datas as $data) {
            if (in_array($data->tags, $req)) {
                $option .= "<option  selected value='$data->tags'>$data->tags</option>";
            } else {
                $option .= "<option  value='$data->tags'>$data->tags</option>";
            }
        }
        return $option;
    }

    protected function loadMonth() {
        $months = ["01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec"];
        $currMoth = gmdate("m", time());
        $option = "";
        foreach ($months as $key => $month) {
            if ($currMoth == $key) {
                $option .= "<option  selected value='$key'>$month</option>";
            } else {
                $option .= "<option  value='$key'>$month</option>";
            }
        }
        return $option;
    }

    protected function loadChannelType(Request $request) {
        $option = '';
        $datas = DB::select("select distinct channel_type from accountinfo where channel_type is not null and is_music_channel in(1,2)");

        $req = '-1';
        if (isset($request->channel_type)) {
            $req = $request->channel_type;
        }
        if ($req == '-1') {
            $option .= "<option selected value='-1'>Channel Type</option>";
        } else {
            $option .= "<option value='-1'>Channel Type</option>";
        }
        foreach ($datas as $data) {
            if ($data->channel_type == $req) {
                $option .= "<option  selected value='$data->channel_type'>$data->channel_type</option>";
            } else {
                $option .= "<option  value='$data->channel_type'>$data->channel_type</option>";
            }
        }
        return $option;
    }

    protected function genIsReband(Request $request) {
        $value = array('-1', '0', '1', '5', '4');
        $label = array(trans('label.value.select'), "Not Rebrand", "Branding", "Brand Success", "Brand Error");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->brand) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStudioStatus(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "Hide", "Show");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->studio) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusHubs(Request $request) {
        $value = array('-1', '1', '0');
        $label = array(trans('label.value.select'), "Hub is off", "Hub is on");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->statusHub) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function loadDesigner(Request $request) {
        $option = '';
//        $datas = DB::select("select distinct(channel_genre) as channel_genre from accountinfo where channel_genre is not null and is_music_channel in(1,2) and is_automusic_v2 =1");
        $datas = DB::select("select user_name from users where role like '%23%' and status =1");
        $req = '-1';
        if (isset($request->designer)) {
            $req = $request->designer;
        }
        if ($req == '-1') {
            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
        } else {
            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
        }
        foreach ($datas as $data) {
            if ($data->user_name == $req) {
                $option .= "<option  selected value='$data->user_name'>$data->user_name</option>";
            } else {
                $option .= "<option  value='$data->user_name'>$data->user_name</option>";
            }
        }
        return $option;
    }

    protected function genStatusBrand(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "Offline", "Online");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status_brand) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusUseBrand(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "Unused", "Used");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status_use_brand) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusDesign(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "New", "Finished design");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status_design) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function loadBrandingChannel(Request $request) {
        $user = Auth::user();
        $option = '';
        $datas = DB::select("select * from rebrand_manager where status_design =1 and status_use =0 and manager = '$user->user_name'");

        $req = '-1';
        if (isset($request->brand_id)) {
            $req = $request->brand_id;
        }
        if ($req == '-1') {
            $option .= "<option selected value='-1'>Select</option>";
        } else {
            $option .= "<option value='-1'>Select</option>";
        }
        foreach ($datas as $data) {
            $base64 = base64_encode("$data->id@;@$data->manager@;@$data->designer@;@$data->channel_name@;@$data->genre@;@https://automusic.win/brand_image/$data->local_avatar@;@https://automusic.win/brand_image/$data->local_banner@;@$data->sub_genre");
            if ($data->id == $req) {
                $option .= "<option  selected value='$base64'>$data->genre - $data->channel_name</option>";
            } else {
                $option .= "<option  value='$base64'>$data->genre - $data->channel_name</option>";
            }
        }
        return $option;
    }

    protected function loadCampaignTableau(Request $request, $listPromos) {
        $option = '';
        $req = '-1';
        if (isset($request->promo_id)) {
            $req = $request->promo_id;
        }
        foreach ($listPromos as $data) {
            if ($data->promo_id == $req) {
                $option .= "<option  selected value='$data->promo_id'>$data->promo_id : $data->promo_name</option>";
            } else {
                $option .= "<option  value='$data->promo_id'>$data->promo_id : $data->promo_name</option>";
            }
        }
        return $option;
    }

    protected function loadListDateTableauReport(Request $request) {
        $user = Auth::user();
        $option = '';
        $promoId = '-1';
        if (isset($request->promo_id)) {
            $promoId = $request->promo_id;
        }
        $listDate = DB::select("select distinct create_date from athena_tableau_report where promo_id = $promoId and username =  '$user->user_name' order by create_date desc");
        foreach ($listDate as $data) {
            if ($data->create_date == $request->date_caculate) {
                $option .= "<option selected value='$data->create_date'>$data->create_date</option>";
            } else {

                $option .= "<option  value='$data->create_date'>$data->create_date</option>";
            }
        }
        return $option;
    }

    protected function loadLabelGridArtist(Request $request) {
        $option = '';
        $datas = DB::select("select artist_id,artist_name from labelgrid_artist where artist_id is not null");
        foreach ($datas as $data) {
            $option .= "<option  value='$data->artist_id'>$data->artist_name</option>";
        }
        return $option;
    }

    protected function loadLabelGridReleases(Request $request) {
        $option = '';
        $datas = DB::select("select release_id,title from labelgrid_release where release_id is not null");
        foreach ($datas as $data) {
            $option .= "<option  value='$data->release_id'>$data->title</option>";
        }
        return $option;
    }

    protected function genLevel(Request $request) {
        $value = array('-1', 'INFO', 'WARNING', 'ERROR');
        $label = array(trans('label.value.select'), "STORMING", "WARNING", "ERROR");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->level) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genColor($percent) {
        $colors = ["bg-danger", "btn-s", "bg-warning", "bg-success", "bg-info", "btn-t"];
        if ($percent < 50) {
            $color = $colors[0];
        } else if ($percent >= 50 && $percent < 70) {
            $color = $colors[1];
        } else if ($percent >= 70 && $percent < 95) {
            $color = $colors[2];
        } else if ($percent >= 95 && $percent < 120) {
            $color = $colors[3];
        } else if ($percent >= 120) {
            $color = $colors[5];
        }
        return $color;
    }

    protected function genColor360($percent) {
        $colors = ["text-red-500", "text-pink-600", "text-amber-400", "text-360promo", "text-blue-500", "text-violet-600"];
        if ($percent < 50) {
            $color = $colors[0];
        } else if ($percent >= 50 && $percent < 70) {
            $color = $colors[1];
        } else if ($percent >= 70 && $percent < 95) {
            $color = $colors[2];
        } else if ($percent >= 95 && $percent < 120) {
            $color = $colors[3];
        } else if ($percent >= 120) {
            $color = $colors[5];
        }
        return $color;
    }

    protected function loadListChannel(Request $request) {
        $user = Auth::user();
        $channels = AccountInfo::where("user_name", $user->user_code)->where("status", 1)->where("del_status", 0)->get();
        $option = "<option value='-1'>Select</option>";
        foreach ($channels as $channel) {
            $option .= "<option value='$channel->chanel_id'>$channel->chanel_name</option>";
        }
        return $option;
    }

    protected function loadTopicShorts(Request $request) {
        $user = Auth::user();
        $datas = DB::select("select topic from shorts_topic where username='$user->user_name' group by topic");
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $data) {
            $option .= "<option value='$data->topic'>$data->topic</option>";
        }
        return $option;
    }

    protected function genIsSync(Request $request) {
        $value = array('-1', '1', '0', '2', '3');
        $label = array(trans('label.value.select'), "Synced", "No sync", "Admin Check", "User Check");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->is_sync) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genSubTracking(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "No", "Yes");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->sub_tracking) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function loadGroupBitly(Request $request) {
        $option = '';
//        $datas = DB::select("select distinct(channel_genre) as channel_genre from accountinfo where channel_genre is not null and is_music_channel in(1,2) and is_automusic_v2 =1");
        $datas = DB::select("select code,name from bitly_group where status =1");
        $req = '-1';
        if (isset($request->group_bit)) {
            $req = $request->group_bit;
        }
        if ($req == '-1') {
            $option .= "<option selected value='-1'>" . trans('label.value.select') . "</option>";
        } else {
            $option .= "<option value='-1'>" . trans('label.value.select') . "</option>";
        }
        foreach ($datas as $data) {
            if ($data->code == $req) {
                $option .= "<option  selected value='$data->code'>$data->code</option>";
            } else {
                $option .= "<option  value='$data->code'>$data->code</option>";
            }
        }
        return $option;
    }

    //lấy dữ liệu vẽ biểu đồ bitly
    protected function getStatsBitly(Request $request) {
        $user = Auth::user();
        $currY = gmdate("Y", time());
        $currM = gmdate("m", time());
        $condidion = " year=$currY and month=$currM";
        if ($request->is_admin_music) {
            $stats = DB::select("SELECT username,concat(year,'-',month) as months,sum(clicked) as clicked FROM `bitly_stats` where $condidion group by username,year,month order by month,username order by months,username");
        } else {
            $stats = DB::select("SELECT username,concat(year,'-',month) as months,sum(clicked) as clicked FROM `bitly_stats` where username = '$user->user_name' and $condidion group by username,year,month order by months,username");
        }
        $data = [];
        $labels = [];
        $datasets = [];
        foreach ($stats as $stat) {
            $data[] = $stat->clicked;
            $labels[] = $stat->username;
            $datasets[] = (object) [label => "Click this month",
                        data => $data,
                        fill => false,
                        backgroundColor => '#2fa5cb',
                        borderColor => '#2fa5cb',
                        borderWidth => 1];
        }
        return array("labels" => $labels, "datasets" => $datasets);
    }

    protected function loadGroupBom(Request $request) {
        $user = Auth::user();
        $datas = BomGroups::where("username", $user->user_name)->where("del_status", 0)->orderBy("id", "desc")->get();
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
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $data) {
            if ($request->group_bom_filter == $data->id) {
                $option .= "<option selected value='$data->id'>$data->id $data->name (vip:$data->vip,normal:$data->normal)</option>";
            } else {
                $option .= "<option value='$data->id'>$data->id $data->name (vip: $data->vip, normal: $data->normal)</option>";
            }
        }
        return $option;
    }

    protected function genVipStatusGroupBom(Request $request) {
        $user = Auth::user();
        $datas = ["1" => "Vip", "2" => "Normal"];
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $index => $data) {
            if ($request->is_vip == $index) {
                $option .= "<option selected value='$index'>$data </option>";
            } else {
                $option .= "<option value='$index'>$data</option>";
            }
        }
        return $option;
    }

    protected function genIsLyric(Request $request) {
        $user = Auth::user();
        $datas = ["0" => "No lyrics", "1" => "Has lyrics"];
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $index => $data) {
            if (isset($request->is_lyric) && $request->is_lyric == $index) {
                $option .= "<option selected value='$index'>$data </option>";
            } else {
                $option .= "<option value='$index'>$data</option>";
            }
        }
        return $option;
    }

    //202306/08 load distributor
    protected function loadDistributor() {
        $datas = CampaignStatistics::where("type", 2)->whereIn("status", [1, 4])->whereNotNull("distributor")->orderBy("distributor", "asc")->groupBy('distributor')->get(["distributor"]);
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $data) {
            $option .= "<option value='$data->distributor'>$data->distributor</option>";
        }
        return $option;
    }

    //2023/09/19 load vendor
    protected function loadVendor($type) {
        $datas = MSVendor::where("status", 1)->where("type", $type)->get();
        $option = "";
        foreach ($datas as $data) {
            $subText = "";
            if ($data->description != null) {
                $subText = $data->description;
            }
            $option .= "<option data-subtext='$subText' value='$data->name'>$data->name</option>";
        }
        return $option;
    }

    //2023/09/22 load main channel
    protected function loadMainChannels() {
        $datas = AccountInfo::where("tags", "like", "%MAIN_CHANNEL%")->get();
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $data) {
            $option .= "<option value='$data->chanel_id'>$data->chanel_name</option>";
        }
        return $option;
    }

    protected function loadMainChannelsDatas() {
        $datas = AccountInfo::where("status", 1)->whereRaw("(tags like '%MAIN_CHANNEL%' or tags like '%BIG%')")->orderBy("subscriber_count", "desc")->get(["id", 'user_name', 'chanel_id', 'chanel_name', 'subscriber_count', 'view_count', 'channel_clickup']);
        return $datas;
    }

    protected function loadMainChannelsJsons($request, $userCode) {
        $channelList = [];
        $mainChannels = $this->loadMainChannelsDatas();

        foreach ($mainChannels as $channel) {
            $obj = (object) [
                        "name" => $channel->chanel_name,
                        "avatar" => $channel->channel_clickup,
                        "subscribers" => $channel->subscriber_count,
            ];
            if (!$request->is_admin_music) {
                if ($channel->user_name == $userCode) {
                    $channelList[] = $obj;
                }
            } else {
                $channelList[] = $obj;
            }
        }
        return $channelList;
    }

    protected function loadPaymentMethod() {
        $datas = RequestHelper::callAPI2("GET", "https://dash.360promo.net/api/paymentmethod/list", []);
        $option = "<option value='-1'>Select</option>";
        foreach ($datas as $data) {
            $option .= "<option value='$data->code'>$data->name</option>";
        }
        return $option;
    }

    protected function loadCampaign() {
        $campaigns = CampaignStatistics::whereIn("type", [1, 5])->where("status", 1)->orderBy("id", "desc")->get(["id", "campaign_name"]);
        $option = "<option  value=''>Select a Campaign</option>";
        foreach ($campaigns as $campaign) {
            $option .= "<option  value='$campaign->id'>#$campaign->id $campaign->campaign_name</option>";
        }
        return $option;
    }

    protected function getTagUser() {
        $highlightUser = [];
        $listUser = User::where("role", "like", "%26%")->where("status", 1)->orderBy("user_name")->pluck("user_name");
        foreach ($listUser as $high) {
            $highlightUser[] = $high;
        }
        return $highlightUser;
    }

    //tìm trong đoạn văn bản xem có được tag username vào không
    public function findTagUsername($text) {
        $result = [];
        $tagUsers = self::getTagUser();
        foreach ($tagUsers as $tag) {
            if (Utils::containString($text, $tag)) {
                $result[] = str_replace("@", "", $tag);
            }
        }
        return $result;
    }

    //tạo json checklist 
    protected function makeChecklistJson($checkListTemplate) {
        $checkListCampaign = [];
        foreach ($checkListTemplate as $key) {
            $jobDetail = [];
            if ($key->job_detail != null) {
                $details = json_decode($key->job_detail);
                foreach ($details as $index => $detail) {
                    $button = null;
                    if (!empty($detail->button)) {
                        $button = $detail->button;
                    }
                    $tmp = (object) [
                                "id" => "$key->key$index",
                                "type" => $detail->type,
                                "name" => $detail->name,
                                "result" => null,
                                "is_finish" => 0
                    ];
                    if ($button != null) {
                        $tmp->button = $detail->button;
                    }
                    $jobDetail[] = $tmp;
                }
            }
            $checkListCampaign[] = (object) [
                        "key" => $key->key,
                        "type" => $key->type,
                        "is_finish" => 0,
                        "result" => "",
                        "job_detail" => $jobDetail
            ];
        }
        return $checkListCampaign;
    }

    //tính đánh giá nhân viên
    protected function getRating($percentage) {
        if ($percentage >= 90) {
            return 5; // Xuất sắc
        } elseif ($percentage >= 70) {
            return 4; // Tốt
        } elseif ($percentage >= 50) {
            return 3; // Khá
        } elseif ($percentage >= 30) {
            return 2; // Trung bình
        } else {
            return 1; // Kém
        }
    }

    protected function getRatingLabel($rating) {
        switch ($rating) {
            case 5: return 'Excellent';
            case 4: return 'Good';
            case 3: return 'Fair';
            case 2: return 'Average';
            case 1: return 'Poor';
            default: return 'Unknown';
        }
    }

    protected function getWorkingDays($year, $month = null) {
        $startDate = new DateTime("$year-01-01");
        $endDate = new DateTime("$year-12-31");

        if ($month !== null) {
            $startDate = new DateTime("$year-$month-01");
            if ($month == date('n') && $year == date('Y')) {
                $endDate = new DateTime(); // Today's date for the current month
            } else {
                $endDate = new DateTime($startDate->format('Y-m-t')); // Last day of the month
            }
        }

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day')); // Include end date

        $workdays = 0;
        foreach ($dateRange as $date) {
            if ($date->format('N') < 6) { // Exclude Saturdays and Sundays
                $workdays++;
            }
        }
        return $workdays;
    }

    protected function loadMail() {
        $user = Auth::user();
        $mails = DB::select("select note as email, count(*) as total from accountinfo where del_status = 0 and user_name= ? group by note", [$user->user_code]);
        $option = "<option  value=''>Select a Email</option>";
        foreach ($mails as $mail) {
            $option .= "<option  data-content=\"$mail->email <span class='text-muted font-13'>($mail->total channel)</span>\" value='$mail->email'></option>";
        }
        return $option;
    }

    protected function genIsChangeInfo(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "Change Info Fail", "Change Info Success");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->is_changeinfo) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genIsUpdateOpt(Request $request) {
        $value = array('-1', '0');
        $label = array(trans('label.value.select'), "Need Update OTP");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->is_add_otp) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

}
