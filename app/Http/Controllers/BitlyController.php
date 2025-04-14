<?php

namespace App\Http\Controllers;

use App\Common\Network\BitlyHelper;
use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\Bitly;
use App\Http\Models\BitlyGroup;
use App\Http\Models\BitlyStats;
use App\Http\Models\CampaignClaimRev;
use App\Http\Models\CampaignClaimRevStatus;
use App\Http\Models\Moonaz;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function view;

class BitlyController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BitlyController->index");
        if ($request->is_admin_music) {
            $bitlys = Bitly::where("status", 1);
        } else {
            $bitlys = Bitly::where("status", 1)->where("username", $user->user_name);
        }
        $sum = $bitlys->sum('clicked');

        $limit = 10;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
            }
        }
        if (isset($request->custom) && $request->custom != null) {
            $bitlys = $bitlys->where("custom_bitlinks", "like", "%$request->custom%");
        }
        if (isset($request->group_bit) && $request->group_bit != -1) {
            $bitlys = $bitlys->where("group_bitlink", $request->group_bit);
            $queries["group_bit"] = $request->group_bit;
        }
        $queries = [];
//        $queries['sort'] = 'id';
//        $queries['direction'] = 'desc';

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'id';
            $request['direction'] = 'desc';
            $queries['sort'] = 'id';
            $queries['direction'] = 'desc';
        }

        $bitlys = $bitlys->sortable()->paginate($limit)->appends($queries);

        return view("components.bitly", [
            "bitlys" => $bitlys,
            "sum" => $sum,
//            "stats" => $this->getStatsBitly($request),
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            'groupBit' => $this->loadGroupBitly($request),
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BitlyController.store|request=' . json_encode($request->all()));
//        return array("status" => "success", "message" => "success", "data" => Bitly::find(1067));
        if ($request->long_url == null || $request->long_url == "") {
            return array("status" => "error", "message" => "Long Url can not be empty");
        }
        if ($user->bitly_token == null) {
            return array("status" => "error", "message" => "Not found bitly token");
        }

//        $groups = BitlyHelper::crawGroups($user->bitly_token);
//        if (empty($groups->groups) || count($groups->groups) == 0) {
//            return array("status" => "error", "message" => "Not found group, please check your token");
//        }
//        $guid = $groups->groups->guid;
        $longUrl = trim($request->long_url);
        $title = "";
        if (isset($request->title)) {
            $title = trim($request->title);
        }
        $data = (object) [
                    "long_url" => $longUrl,
//                    "group_guid" => $guid,
                    "domain" => "bit.ly",
                    "title" => $title
        ];

        $result = BitlyHelper::create($user->bitly_token, $data);
        Log::info("result:" . json_encode($result));
        if (!empty($result->message)) {
            return array("status" => "error", "message" => "Create link fail: $result->message");
        }
        $bitlyId = str_replace("bit.ly/", "", $result->id);
        $customBitlink = $bitlyId;
        $created = gmdate("Y-m-d", time());
        $check = Bitly::where("bitly_id")->first();
        if ($check) {
            return array("status" => "error", "message" => "Bitly link exists");
        }
        $bitly = new Bitly();
        $bitly->username = $user->user_name;
        $bitly->bitly_id = $bitlyId;
        $bitly->custom_bitlinks = $bitlyId;
        $bitly->created = $created;
        $bitly->title = $title;
        $bitly->long_url = $longUrl;
        $bitly->save();
        if ($request->custom_bitlink != null && $request->custom_bitlink != "") {
            $customBitlink = trim($request->custom_bitlink);
            $data2 = (object) [
                        "custom_bitlink" => "bit.ly/$customBitlink",
                        "bitlink_id" => "bit.ly/$bitlyId"
            ];
            $result2 = BitlyHelper::createCustom($user->bitly_token, $data2);
            Log::info("result2:" . json_encode($result2));
            if (!empty($result2->message)) {
                return array("status" => "error", "message" => "Create link success, create custom back-half fail: $result2->message");
            }
            $bitly->custom_bitlinks = $customBitlink;
            $bitly->save();
        }
        return array("status" => "success", "message" => "success", "data" => $bitly);
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|BitlyController.update|request=' . json_encode($request->all()));
        if ($request->is_admin_music) {
            $bitly = Bitly::where("id", $request->id)->first();
        } else {
            $bitly = Bitly::where("username", $user->user_name)->where("id", $request->id)->first();
        }
        if (!$bitly) {
            return array("status" => "error", "message" => "Not found $request->id");
        }
        $bitly->status = $request->status;
        $bitly->save();
        return array("status" => "success", "message" => "success");
    }

    //lấy dữ liệu báo cao money
    public function getChartMonth(Request $request) {
        $user = Auth::user();
        $uName = "system";
        if ($user) {
            $uName = $user->user_name;
        }
        Log::info("$uName|BitlyController.getChartMonth|request=" . json_encode($request->all()));
        $userExcept = ["sangmusic"];
        $userExceptText = "'" . implode("','", $userExcept) . "'";
        $datas = DB::select("SELECT concat(year,month) as period,count(id) as count,sum(clicked) as clicked FROM `bitly_stats` where group_bitlink='moonaz' and username not in ($userExceptText) group by year,month order by concat(year,month) desc");
        $bitlyRev = CampaignClaimRev::where("type", 2)->get();
        $datas2 = DB::select("select period,sum(download) as download from moonaz where username not in($userExceptText) group by period order by period desc");
        $moonazRev = CampaignClaimRev::where("type", 3)->get();
        $listPays = CampaignClaimRevStatus::where("rev_type", 3)->get();
        foreach ($datas2 as $data) {
            $data->amount = 0;
            foreach ($moonazRev as $rev) {
                if ($rev->period == $data->period) {
                    $data->amount = $rev->revenue;
                    break;
                }
            }
        }
//        Log::info(json_encode($datas2));
//        Log::info(json_encode($moonazRev));
        foreach ($datas as $data) {
            $data->amount = 0;
            $data->bitly_full_amount = 0;
            $data->moonaz_full_amount = 0;
            $data->moonaz_download = 0;
            $data->moonaz_amount = 0;
            $data->period_text = date("M-Y", strtotime($data->period . "01"));
            $data->show = 0;
            $data->paid = 0;
            foreach ($listPays as $pay) {
                if ($pay->period == $data->period) {
                    $data->paid = $pay->status;
                    $data->show = $pay->status_show;
                    break;
                }
            }
            foreach ($bitlyRev as $rev) {
                if ($rev->period == $data->period) {
                    //tiền bitly thì lấy số nhập vào
                    $data->amount = $rev->revenue;
                    $data->bitly_full_amount = $rev->revenue * 10;
                    break;
                }
            }
            foreach ($datas2 as $moonaz) {
                if ($data->period == $moonaz->period) {
                    $data->moonaz_download = $moonaz->download;
                    //tiền moonaz thì lấy 10% cho bass team, 10% devteam
                    $data->moonaz_amount = $moonaz->amount * 0.1;
                    $data->moonaz_full_amount = $moonaz->amount;
                    break;
                }
            }
        }
        return array("status" => "success", "report" => $datas);
    }

    public function getChartMonthDetail(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BitlyController.getChartMonthDetail|request=" . json_encode($request->all()));
        $userExcept = ["sangmusic"];
        $userExceptText = "'" . implode("','", $userExcept) . "'";
        $year = substr($request->period, 0, 4);
        $month = substr($request->period, 4, 2);
        $datas = DB::select("SELECT username,count(id) as count,sum(clicked) as clicked FROM bitly_stats where group_bitlink='moonaz' and username not in ($userExceptText) and year = '$year' and month = '$month' group by username order by clicked desc");
        $datas2 = DB::select("select username,sum(download) as download from moonaz where username not in('sangmusic') and period='$request->period' group by username order by download desc");
        if (count($datas) == 0) {
            return array("status" => "success", "report" => $datas, "period" => "");
        }
        $bitlyRev = CampaignClaimRev::where("type", 2)->where("period", $request->period)->first();
        $moonazRev = CampaignClaimRev::where("type", 3)->where("period", $request->period)->first();
        $rev = 0;
        if ($bitlyRev) {
            $rev = $bitlyRev->revenue;
        }
        $revMoonaz = 0;
        if ($moonazRev) {
            //bassteam và tung đc nhận 10% tổng doanh thu
            $revMoonaz = $moonazRev->revenue * 0.1;
        }

        $totalClick = 0;
        foreach ($datas as $data) {
            $totalClick += $data->clicked;
//            $data->click_percent = 0;
//            $data->kpi = 0;
//            $data->total_click = $totalClick;
        }
        //tính tổng số download
        $totalDownload = 0;
        foreach ($datas2 as $data) {
            $totalDownload += $data->download;
        }
        //tính số tiền được nhận moonaz của mỗi user
        foreach ($datas2 as $data) {
            $data->download_percent = 0;
            $data->moonaz_amount = 0;
            if ($totalDownload != 0) {
                $data->download_percent = round($data->download / $totalDownload * 100, 1);
                $data->moonaz_amount = round($data->download / $totalDownload * $revMoonaz, 1);
            }
            $data->total_rev_moonaz = $revMoonaz;
            $data->total_download = $totalDownload;
            $data->period = $request->period;
        }


        foreach ($datas as $data) {
            $data->click_percent = 0;
            $data->kpi = 0;
            $data->total_rev = 0;
            $data->moonaz_download = 0;
            $data->moonaz_amount = 0;
            if ($totalClick != 0) {
                $data->click_percent = round($data->clicked / $totalClick * 100, 1);
                $data->kpi = round($data->clicked / $totalClick * $rev, 1);
            }
            $data->total_rev = $rev;
            $data->total_click = $totalClick;
            $data->period = $request->period;
            foreach ($datas2 as $moonaz) {
                if ($data->username == $moonaz->username) {
                    $data->moonaz_download = $moonaz->download;
                    //tiền moonaz thì lấy 10% cho bass team, 10% devteam
                    $data->moonaz_amount = $moonaz->moonaz_amount;
                    break;
                }
            }
        }
        //thêm tung vao list, tung dc nhận 10% tổng doanh thu
        $datas[] = (object) [
                    "username" => "tungtt",
                    "count" => 0,
                    "clicked" => 0,
                    "kpi" => 0,
                    "moonaz_download" => 0,
                    "download_percent" => 0,
                    "moonaz_amount" => $revMoonaz,
                    "total_rev_moonaz" => round($revMoonaz),
                    "total_download" => 0,
                    "period" => $request->period,
        ];
        $periodText = date("M-Y", strtotime($request->period . "01"));

        //dữ liệu chi tiết từng user
        $customBitlink = [];
        if (isset($request->username)) {
            //dữ liệu bitly
            $bitLinks = Bitly::where("username", $request->username)->where("status", 1)->get();
            $bitlyClick = BitlyStats::where("year", $year)->where("month", $month)->whereNotIn("username", $userExcept)->where("group_bitlink", "moonaz")->sum("clicked");
            $customBitlink = DB::select("SELECT custom_bitlinks,sum(clicked) as clicked FROM bitly_stats where group_bitlink='moonaz' and username = '$request->username' and year = '$year' and month = '$month' group by custom_bitlinks order by clicked desc");
            foreach ($customBitlink as $data) {
                $data->kpi = 0;
                if ($bitlyClick != 0) {
                    $data->kpi = round($data->clicked / $bitlyClick * $rev, 1);
                }
                $data->username = $request->username;
                $data->type = "bitly";
                foreach ($bitLinks as $link) {
                    if ($data->custom_bitlinks == $link->custom_bitlinks) {
                        $data->created = $link->created;
                        break;
                    }
                }
            }
            //dữ liệu moonaz
            foreach ($datas as $data) {
                if ($data->username == $request->username) {
                    $customBitlink[] = (object) [
                                "username" => $request->username,
                                "created" => "",
                                "type" => "moonaz",
                                "custom_bitlinks" => "moonaz",
                                "clicked" => $data->moonaz_download,
                                "kpi" => $data->moonaz_amount,
                    ];
                }
            }
        }
        return array("status" => "success", "report" => $datas, "period" => $periodText, "reportUser" => $customBitlink, "username" => $request->username);
    }

    public function getChartUser(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|BitlyController.getChartUser|request=" . json_encode($request->all()));
        $userExcept = ["sangmusic"];
        $year = substr($request->period, 0, 4);
        $month = substr($request->period, 4, 2);
        $bitLinks = Bitly::where("username", $request->username)->where("status", 1)->get();
        $bitlyClick = BitlyStats::where("year", $year)->where("month", $month)->whereNotIn("username", $userExcept)->where("group_bitlink", "moonaz")->sum("clicked");
        $datas = DB::select("SELECT custom_bitlinks,sum(clicked) as clicked FROM bitly_stats where group_bitlink='moonaz' and username = '$request->username' and year = '$year' and month = '$month' group by custom_bitlinks order by clicked desc");
        $revenue = CampaignClaimRev::where("type", 2)->where("period", $request->period)->first();
        $rev = 0;
        if ($revenue) {
            $rev = $revenue->revenue;
        }
        foreach ($datas as $data) {
            $data->kpi = 0;
            if ($bitlyClick != 0) {
                $data->kpi = round($data->clicked / $bitlyClick * $rev, 1);
            }
            $data->username = $request->username;
            foreach ($bitLinks as $link) {
                if ($data->custom_bitlinks == $link->custom_bitlinks) {
                    $data->created = $link->created;
                    break;
                }
            }
        }

        return array("status" => "success", "report" => $datas, "username" => $request->username);
    }

    //hàm đồng bộ danh sách link bitky theo token
    public function crawBitlyLink() {
        $users = User::whereNotNull("bitly_token")->get();
        $bitlyGroup = BitlyGroup::where("status", 1)->get();
        foreach ($users as $user) {
            $groups = BitlyHelper::crawGroups($user->bitly_token);
            if (!empty($groups->groups)) {
                foreach ($groups->groups as $group) {
                    $links = BitlyHelper::crawLinks($user->bitly_token, $group->guid);
//                    Utils::write("bitly.txt", json_encode($links));
                    foreach ($links as $index => $link) {
                        if (count($link->custom_bitlinks) == 0) {
                            continue;
                        }
                        $created = explode("T", $link->created_at)[0];
                        $bitlyId = str_replace("bit.ly/", "", $link->id);
                        $longUrl = $link->long_url;
                        $title = !empty($link->title) ? $link->title : null;
                        $groupLocal = null;
                        foreach ($bitlyGroup as $gr) {
                            $arrayNeedFind = json_decode($gr->name);
                            if (Utils::containArrayString($longUrl, $arrayNeedFind)) {
                                $groupLocal = $gr->code;
                                break;
                            }
                        }
                        foreach ($link->custom_bitlinks as $customLink) {
                            $tmp = str_replace("https://bit.ly/", "", $customLink);
                            $customeBitlink = str_replace("http://bit.ly/", "", $tmp);
                            error_log("$user->user_name $index $customeBitlink $created $groupLocal $longUrl");
                            $bitly = Bitly::where("custom_bitlinks", $customeBitlink)->where("username", $user->user_name)->where("bitly_id", $bitlyId)->first();
                            //có phân biệt chữ hoa chữ thường
                            if (!$bitly || ($bitly && $bitly->custom_bitlinks != $customeBitlink)) {
                                $bitly = new Bitly();
                                $bitly->group_bitlink = $groupLocal;
                                $bitly->username = $user->user_name;
                                $bitly->bitly_id = $bitlyId;
                                $bitly->custom_bitlinks = $customeBitlink;
                                $bitly->title = $title != null ? $title : $customeBitlink;
                                $bitly->long_url = $longUrl;
                                $bitly->created = $created;
                                $bitly->save();
                            } else {
                                $bitly->group_bitlink = $groupLocal;
                                $bitly->title = $title != null ? $title : $customeBitlink;
                                $bitly->save();
                            }
                        }
                    }
                }
            }
        }
    }

    //hàm đồng bộ dữ liệu click theo ngày của từng link bitly
    public function crawStats() {
        DB::enableQueryLog();
        $startTime = time();
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Start craw bitly");
        $today = gmdate("Y-m-d", $startTime);
        $datas = Bitly::where("status", 1)->get();
//        Log::info(DB::getQueryLog());
        $count = 0;
        foreach ($datas as $data) {
            $user = User::where("user_name", $data->username)->first(["bitly_token"]);
            if ($user->bitly_token == null) {
                error_log("craw_bitly $data->username token null");
                continue;
            }
            $curr = time();
            if ($data->last_craw == null && $data->created != null) {
                //craw từ ngày đầu tiên đến hiện tại
                $lastCraw = strtotime($data->created);
                $date = gmdate("Y-m-d", $lastCraw);
            } else {
                //craw ngày cuối cùng craw - 3 đến ngày hiện tại
                $lastCraw = strtotime($data->last_craw) - 3 * 86400;
                $date = gmdate("Y-m-d", $lastCraw);
            }

            while (true) {
                if ($lastCraw >= $curr) {
                    error_log("craw_bitly $data->username link=$data->custom_bitlinks, date=$date break");
                    break;
                } else {
                    $count++;
                    $date = gmdate("Y-m-d", $lastCraw);
                    $y = gmdate("Y", $lastCraw);
                    $m = gmdate("m", $lastCraw);
                    $d = gmdate("d", $lastCraw);
                    $craw = BitlyHelper::crawStats($user->bitly_token, $date, $data->custom_bitlinks);
                    error_log("craw_bitly $data->username, link=$data->custom_bitlinks, date=$date,error=" . (!empty($craw->errors) ? json_encode($craw->errors) : ""));
                    if (empty($craw->errors) && !empty($craw->units) && $craw->units == 1) {
                        $bitlyStats = BitlyStats::where("custom_bitlink_id", $data->id)->where("date", $date)->first();
                        if (!$bitlyStats) {
                            $bitlyStats = new BitlyStats();
                            $bitlyStats->username = $data->username;
                            $bitlyStats->group_bitlink = $data->group_bitlink;
                            $bitlyStats->bitly_id = $data->bitly_id;
                            $bitlyStats->custom_bitlink_id = $data->id;
                            $bitlyStats->custom_bitlinks = $data->custom_bitlinks;
                            $bitlyStats->year = $y;
                            $bitlyStats->month = $m;
                            $bitlyStats->day = $d;
                            $bitlyStats->date = $date;
                            $bitlyStats->clicked = $craw->total_clicks;
                            $bitlyStats->update_time = Utils::timeToStringGmT7(time());
                            $bitlyStats->save();
                        } else {
                            $bitlyStats->clicked = $craw->total_clicks;
                            $bitlyStats->update_time = Utils::timeToStringGmT7(time());
                            $bitlyStats->save();
                        }
                    }
                }
                $lastCraw = $lastCraw + 86400;
                if ($count > 50) {
                    $count = 0;
                    error_log("craw_bitly sleep 5s");
                    sleep(5);
                }
            }
            $clicked = BitlyStats::where("custom_bitlink_id", $data->id)->selectRaw("sum(clicked) as clicked")->first();
            $data->clicked = $clicked->clicked != null ? $clicked->clicked : 0;
            $data->last_craw = $date;
            $data->last_craw_time = gmdate("H:i:s", time() + 7 * 3600);
            $data->save();
        }
        $endTime = time() - $startTime;
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Fisnish craw bitly " . round($endTime / 60, 3) . " minutes");
        return "finished";
    }

    //hàm dồng bộ dữ liệu click theo tháng của từng link
    public function crawStatsMonth(Request $request) {
        $datas = Bitly::where("status", 1)->get();
        foreach ($datas as $data) {
            $user = User::where("user_name", $data->username)->first(["bitly_token"]);
            if ($user->bitly_token == null) {
                error_log("craw_bitly $data->username token null");
                continue;
            }
            $lastCraw = strtotime($request->date);
            $date = gmdate("Y-m-d", $lastCraw);
            $y = gmdate("Y", $lastCraw);
            $m = gmdate("m", $lastCraw);
            $d = gmdate("d", $lastCraw);
            $craw = BitlyHelper::crawStatsMotnh($user->bitly_token, $date, $data->custom_bitlinks);
            error_log("craw_bitly $data->username, check=" . !empty($craw->units) . " link=$data->custom_bitlinks, date=$date,result=" . (json_encode($craw)));
            if (empty($craw->errors)) {
                $bitlyStats = BitlyStats::where("custom_bitlink_id", $data->id)->where("date", $date)->first();
                if (!$bitlyStats) {
                    $bitlyStats = new BitlyStats();
                    $bitlyStats->username = $data->username;
                    $bitlyStats->group_bitlink = $data->group_bitlink;
                    $bitlyStats->bitly_id = $data->bitly_id;
                    $bitlyStats->custom_bitlink_id = $data->id;
                    $bitlyStats->custom_bitlinks = $data->custom_bitlinks;
                    $bitlyStats->year = $y;
                    $bitlyStats->month = $m;
                    $bitlyStats->day = $d;
                    $bitlyStats->date = $date;
                    $bitlyStats->clicked = $craw->total_clicks;
                    $bitlyStats->update_time = Utils::timeToStringGmT7(time());
                    $bitlyStats->save();
                } else {
                    $bitlyStats->clicked = $craw->total_clicks;
                    $bitlyStats->update_time = Utils::timeToStringGmT7(time());
                    $bitlyStats->save();
                }
            } else {
                error_log("$date $data->custom_bitlinks not insert");
            }
        }
    }

    //2023/05/23 craw moonaz download
    public function crawMoonaz(Request $request) {

        $date = gmdate("Ym", time());
        if (isset($request->date)) {
            $date = $request->date;
        }
        Log::info("crawMoonaz https://moonaz.net/api/download/report/xxx/$date/1/10");
        $datas = RequestHelper::callAPI2("GET", "https://moonaz.net/api/download/report/xxx/$date/1/50", []);
//        $datas = RequestHelper::getRequest("http://moonaz.net/api/download/report/xxx/$date/1/10");
//        $datas = file_get_contents("http://moonaz.net/api/download/report/xxx/$date/1/10");
//        Log::info($datas);
//        Log::info("data: " . count($datas));
        foreach ($datas as $data) {
            $moonaz = Moonaz::where("username", $data->nickname)->where("period", $date)->first();
            if (!$moonaz) {
                $moonaz = new Moonaz();
                $moonaz->username = $data->nickname;
                $moonaz->download = $data->month_download;
                $moonaz->type = $data->type;
                $moonaz->created = Utils::timeToStringGmT7(time());
                $moonaz->period = $data->period;
                $moonaz->save();
            }
        }
    }

}
