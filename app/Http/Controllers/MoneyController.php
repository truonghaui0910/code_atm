<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Events\ChatEvent;
use App\Http\Models\AccountInfo;
use App\Http\Models\CampaignClaimRevStatus;
use App\Http\Models\MooncoinContent;
use App\Http\Models\MooncoinValues;
use App\Http\Models\MSReports;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function response;
use function view;

class MoneyController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.index|request=' . json_encode($request->all()));
        $default = MSReports::where("del_status", 0)->orderBy("period", "desc")->first(["period"]);
        $year = substr($default->period, 0, 4);
        $month = ltrim(substr($default->period, 4, 2), '0'); // Loại bỏ số 0 ở đầu (nếu có)
        return view('components.money', [
            'month_select' => $this->genMonthSelect(),
            'month_select_v2' => $this->genMonthSelectV2(),
            'vendorIn' => $this->loadVendor('revenue'),
            'vendorOut' => $this->loadVendor('expenses'),
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function getMsReport() {
        $datas = MSReports::where("del_status", 0)->orderBy("id", "desc")->get();
//        Log::info(json_encode($datas));
        foreach ($datas as $data) {
            if ($data->money_in != null) {
                $data->money_in = "$" . number_format($data->money_in, 0, ".", ",");
            }
            if ($data->money_out != null) {
                $data->money_out = "$" . number_format($data->money_out, 0, ".", ",");
            }
            $data->period = gmdate("M-Y", strtotime($data->period . "01"));
        }
        return $datas;
    }

    public function findMsReport(Request $request) {
        return MSReports::find($request->ms_id);
    }

    public function saveMsReport(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.saveMsReport|request=' . json_encode($request->all()));
        if ($request->amount == null) {
            return response()->json(["status" => "error", "message" => "Amount cannot be empty"]);
        }
//        $time = strtotime($request->create_date);
        if ($request->ms_id != null) {
            $report = MSReports::find($request->ms_id);
        } else {
            //nếu là thêm mới thì check xem vendor đó đã có khoản thu trong tháng đó chưa, nếu có rồi thì báo lỗi
            if ($request->payment_type == "in") {
                $check = MSReports::where("period", $request->period)->where("vendor", $request->vendor_in)->where("del_status", 0)->first();
                if ($check) {
                    $mString = DateTime::createFromFormat('Ym', $request->period)->format('M-Y');
                    return response()->json(["status" => "error", "message" => "This vendor had $mString income added by $check->username. ID=$check->id"]);
                }
            }
            $report = new MSReports();
        }
        $report->period = $request->period;
        $amount = $request->amount;
        if ($request->currency == 'vnd') {
            $amount = $request->amount / $request->rate;
        }
        if ($request->payment_type == 'out') {
            $report->money_out = $amount;
            $report->vendor = $request->vendor_out;
        } else {
            $report->money_in = $amount;
            $report->vendor = $request->vendor_in;
        }
        $report->note = $request->money_note;
        $report->username = $user->user_name;
        $report->created_date = gmdate("Y/m/d", time() + 7 * 3600);
        $report->created_time = gmdate("H:i:s", time() + 7 * 3600);
        $report->save();
        return response()->json(["status" => "success", "message" => "success"]);
    }

    public function deleteMsReport(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.deleteMsReport|request=' . json_encode($request->all()));
        MSReports::where("id", $request->ms_id)->update(["del_status" => 1]);
        return response()->json(["status" => "success", "message" => "success"]);
    }

    public function updateMoneyChart(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.updateMoneyChart|request=' . json_encode($request->all()));
        if (!$request->is_supper_admin) {
            return response()->json([]);
        }
        $salary = (object) [
                    "chrismusic" => 0,
                    "jamesmusic" => 0,
                    "hoadev" => 0,
                    "sangmusic" => 0,
                    "truongpv" => 0,
        ];
        $stacks = [];
        //tổng số tiền thu nhập
        $totalIn = 0;
        $totalInPer = 0;
        $totalInPerState = 0;
        //tổng số tiền chi tiêu
        $totalOut = 0;
        $totalOutPer = 0;
        $totalOutPerState = 0;

        $totalProfit = 0;
        //số tiền lãi tính % so với tháng hoặc năm trước
        $profitPer = 0;
        //mũi tên chỉ tăng hoặc giảm 1=>tăng,0:giảm
        $profitPerState = 0;
        $profitPerTooltip = "";
        //số tiền còn lại sau khi từ tất cả
        $balance = 0;

        $filterWhere = "";
        $filterYear = "";
        $year = $request->year;
        $month = $request->month;
        if ($month < 10) {
            $month = "0$month";
        }
        $financi = [];
        $financiLast = [];
        if ($request->type == "year") {
            $filterWhere = " and period like '%$year%'";
            $filterYear = $filterWhere;
            $lastYear = $year - 1;
            $filterLastWhere = "and period like '%$lastYear%'";
            $financi = DB::select("SELECT LEFT(period, 4) AS period_year, COUNT(*) AS count,sum(money_in) as m_in,sum(money_out) as m_out FROM ms_reports where del_status =0 $filterWhere GROUP BY period_year");
            $financiLast = DB::select("SELECT LEFT(period, 4) AS period_year, COUNT(*) AS count,sum(money_in) as m_in,sum(money_out) as m_out FROM ms_reports where del_status =0 $filterLastWhere GROUP BY period_year");
            $profitPerTooltip = "Compare with $lastYear";
        } elseif ($request->type == "month") {
            $filterPeriod = "$year$month";

            $filterWhere = " and period = '$filterPeriod'";
            $financi = DB::select("select period,sum(money_in) as m_in,sum(money_out) as m_out from ms_reports where del_status = 0 $filterWhere group by period order by period asc");
            //filter cho biểu đồ cột, biểu đồ này sẽ lấy dữ liệu của cả năm kể cả chọn filter là tháng
            $filterYear = " and period like '%$year%'";
            //lấy dữ liệu tháng trước tháng truyền vào để so sánh độ tăng trưởng
            $date = DateTime::createFromFormat('Ym', $filterPeriod);
            $date->modify('-1 month');
            $previousYearMonth = $date->format('Ym');
            $filterLastWhere = " and period = '$previousYearMonth'";
            $financiLast = DB::select("select period,sum(money_in) as m_in,sum(money_out) as m_out from ms_reports where del_status = 0 $filterLastWhere group by period order by period asc");
            $profitPerTooltip = "Compare with $previousYearMonth";
        } elseif ($request->type == "all") {
            $financi = DB::select("select sum(money_in) as m_in,sum(money_out) as m_out from ms_reports where del_status = 0");
        }
//        $currentPeriod = gmdate("Ym", time() + 7 * 3600);
        //dữ liệu dùng khi hover vào biểu đồ cột, sẽ được lấy theo năm
        $moneyTypeIns = DB::select("select period,vendor,sum(money_in) as m_in,sum(money_out) as m_out from ms_reports where del_status = 0 $filterYear and vendor is not null group by period,vendor order by m_in desc,m_out desc");
        //dữ liệu dùng cho biểu đồ cột
        $moneyIns = DB::select("select period,sum(money_in) as m_in,sum(money_out) as m_out from ms_reports where del_status = 0 $filterYear group by period order by period asc");
        //dữ liệu dùng cho biểu đồ pie
        $moneyTypeGroup = DB::select("select vendor,sum(money_in) as m_in from ms_reports where del_status = 0 $filterWhere and vendor is not null and money_in > 0 group by vendor");


        foreach ($moneyIns as $in) {
            $detail = [];
            $detailOut = [];
            foreach ($moneyTypeIns as $typeIn) {
                if (!in_array($typeIn->vendor, $stacks)) {
                    if ($typeIn->m_in > 0) {
                        $stacks[] = $typeIn->vendor;
                    }
                }
                if ($typeIn->period == $in->period) {
                    if ($typeIn->m_in > 0) {
                        $detail[] = "$typeIn->vendor: $" . number_format($typeIn->m_in, 0, '.', ',');
                    }
                    if ($typeIn->m_out > 0) {
                        $detailOut[] = "$typeIn->vendor: $" . number_format($typeIn->m_out, 0, '.', ',');
                    }
                }
            }
            $in->detail = $detail;
            usort($detailOut, function($a, $b) {
                return strcmp($a, $b);
            });
            $in->detail_out = $detailOut;

            //tính lương nhiều tháng
            $in->profit = 0;
            if ($in->m_in > $in->m_out) {
                $in->profit = $in->m_in - $in->m_out;
            }
            $in->chrismusic = round(0.1 * $in->profit);
            $in->jamesmusic = round(0.07 * $in->profit);
            $in->hoadev = round(0.07 * $in->profit);
            $in->sangmusic = round(0.07 * $in->profit);
            $in->truongpv = round(0.07 * $in->profit);
            $in->balance = round($in->profit - (0.38 * $in->profit));
        }


        if (count($financi) > 0) {
            $totalIn = !empty($financi[0]->m_in) ? $financi[0]->m_in : 0;
            $lastTotalIn = !empty($financiLast[0]->m_in) ? $financiLast[0]->m_in : 0;

            if ($lastTotalIn > 0) {
                $totalInPer = round(($totalIn - $lastTotalIn) / $lastTotalIn * 100, 1);
            }
            if ($totalIn > $lastTotalIn) {
                $totalInPerState = 1;
            }

            $totalOut = !empty($financi[0]->m_out) ? $financi[0]->m_out : 0;
            $lastTotalOut = !empty($financiLast[0]->m_out) ? $financiLast[0]->m_out : 0;

            if ($lastTotalOut > 0) {
                $totalOutPer = round(($totalOut - $lastTotalOut) / $lastTotalOut * 100, 1);
            }
            if ($totalOut > $lastTotalOut) {
                $totalOutPerState = 1;
            }

            $totalProfit = $totalIn - $totalOut;
            $lastTotalProfit = $lastTotalIn - $lastTotalOut;
            //so so sánh với tháng hoặc năm trước

            if ($lastTotalProfit > 0) {
                $profitPer = round(($totalProfit - $lastTotalProfit) / $lastTotalProfit * 100, 1);
            }

            if ($totalProfit > $lastTotalProfit) {
                $profitPerState = 1;
            }

            //tính lương cho admin
            if ($totalProfit > 0) {
                $salary->chrismusic = round(0.1 * $totalProfit);
                $salary->jamesmusic = round(0.07 * $totalProfit);
                $salary->hoadev = round(0.07 * $totalProfit);
                $salary->sangmusic = round(0.07 * $totalProfit);
                $salary->truongpv = round(0.07 * $totalProfit);
                $balance = round($totalProfit - (0.38 * $totalProfit));
            }
        }


        return response()->json([
                    "bar_charts" => $moneyIns,
                    "pie_charts" => $moneyTypeGroup,
                    "money_in" => $totalIn,
                    "money_in_per" => $totalInPer,
                    "money_in_per_state" => $totalInPerState,
                    "money_out" => $totalOut,
                    "money_out_per" => $totalOutPer,
                    "money_out_per_state" => $totalOutPerState,
                    "profit" => $totalProfit,
                    "profit_per" => $profitPer,
                    "profit_per_state" => $profitPerState,
                    "profit_per_tooltip" => $profitPerTooltip,
                    "balance" => $balance,
                    "salary" => $salary,
                    "moneyTypeIns" => $moneyTypeIns
//                    "currRemain" => $totalProfit,
//                    "currRemainPer" => $profitPer,
//                    "currRemainState" => $profitPerState,
//                    "currIn" => $currIn,
//                    "currInPer" => $currInPer,
//                    "currInState" => $currInState,
//                    "currOut" => $currOut,
//                    "currOutPer" => $currOutPer,
//                    "currOutState" => $currOutState,
        ]);
    }

    public function caculateMoney(Request $request) {
        $campaign = new CampaignController();
        $promos = $campaign->reportCampaignRevenue($request);
//        Log::info(json_encode($promos['report']));
//        foreach ($promos['report'] as $data) {
//            $report = MSReports::where("del_status", 0)->where("period", $data->period)->where("vendor", "promo")->first();
//            if (!$report) {
//                $report = new MSReports();
//            }
//            $report->period = $data->period;
//            $report->created_date = gmdate("Y-m-d", time() + 7 * 3600);
//            $report->created_time = gmdate("H:i:s", time() + 7 * 3600);
//            $report->vendor = "promo";
//            $report->money_in = $data->amount;
//            $report->money_out = $data->spent + $data->payment;
//            $report->save();
//        }
//        //2024/08/07 bỏ claim để nhập tay
//        $claimController = new ClaimController();
//        $claims = $claimController->reportClaimRevenue($request);
//        foreach ($claims['report'] as $data) {
//            $report = MSReports::where("period", $data->period)->where("vendor", "claim")->first();
//            if (!$report) {
//                $report = new MSReports();
//            }
//            $report->period = $data->period;
//            $report->created_date = gmdate("Y-m-d", time() + 7 * 3600);
//            $report->created_time = gmdate("H:i:s", time() + 7 * 3600);
//            $report->vendor = "claim";
//            $report->money_in = $data->profit;
//            $report->money_out = $data->bass_revenue + $data->dev_revenue;
//            $report->save();
//        }
//        $bitlyController = new BitlyController();
//        $bitlys = $bitlyController->getChartMonth($request);
//        foreach ($bitlys['report'] as $data) {
//            $report = MSReports::where("del_status", 0)->where("period", $data->period)->where("vendor", "moonaz")->first();
//            if (!$report) {
//                $report = new MSReports();
//            }
//            $report->period = $data->period;
//            $report->created_date = gmdate("Y-m-d", time() + 7 * 3600);
//            $report->created_time = gmdate("H:i:s", time() + 7 * 3600);
//            $report->vendor = "moonaz";
//            //chỉ tính tiền moonaz, tiền bitly trích từ tiền moonaz
//            $report->money_in = $data->moonaz_full_amount;
//            //chia cho devteam 10%,basstean 10%
//            $report->money_out = $data->amount + $data->moonaz_amount * 2;
//            $report->save();
//        }
//        //tính tiền epic
//        $epics = DB::select("select period,sum(revenue) as revenue from campaign_claim_rev where type = 4 group by period");
//        foreach ($epics as $data) {
//            $report = MSReports::where("period", $data->period)->where("vendor", "epidemic")->first();
//            if (!$report) {
//                $report = new MSReports();
//            }
//            $report->period = $data->period;
//            $report->created_date = gmdate("Y-m-d", time() + 7 * 3600);
//            $report->created_time = gmdate("H:i:s", time() + 7 * 3600);
//            $report->vendor = "epidemic";
//            $report->money_in = $data->revenue;
//            //chia cho bassteam 20%
//            $report->money_out = $data->revenue * 0.2;
//            $report->save();
//        }
    }

    public function getReportAll(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.getReportAll|request=' . json_encode($request->all()));

        //promo
        $campaign = new CampaignController();
        $promos = $campaign->reportCampaignRevenue($request);

        //claim
        $claimController = new ClaimController();
        $claims = $claimController->listClaimMontly($request);

        //claim adrew
        $request["type"] = 5;
        $claimsAdrev = $claimController->listClaimMontly($request);

        //claim indiy
        $request["type"] = 6;
        $claimsIndiy = $claimController->listClaimMontly($request);

        //bitly/moonaz
        $bitlyController = new BitlyController();
        $bitlys = $bitlyController->getChartMonth($request);

        //epid
        //đổi lấy thẳng từ campaign_claim_rev vì MSReports lưu revenue gốc ko có cost,MSReports sẽ được nhập tay
        $datas = DB::select("select period,sum(revenue) as revenue from campaign_claim_rev where type = 4 group by period");
        $listPays = CampaignClaimRevStatus::where("rev_type", 4)->get();
        $epids = [];
        $tmps = [];
        foreach ($datas as $data) {
            $paid = 0;
            $show = 0;
            foreach ($listPays as $pay) {
                if ($pay->period == $data->period) {
                    $paid = $pay->status;
                    $show = $pay->status_show;
                    break;
                }
            }

            $tmp = (object) [
                        "period" => $data->period,
                        "period_text" => date("M-Y", strtotime($data->period . "01")),
                        "revenue" => $data->revenue,
                        "profit" => $data->revenue - $data->revenue * 0.2,
                        "bass_revenue" => $data->revenue * 0.2,
                        "paid" => $paid,
                        "show" => $show
            ];
            if ($request->is_supper_admin) {
                $epids[] = $tmp;
            } else {
                if ($tmp->show == 1) {
                    $epids[] = (object) [
                                "period" => $tmp->period,
                                "period_text" => $tmp->period_text,
                                "revenue" => 0,
                                "profit" => 0,
                                "bass_revenue" => $data->revenue * 0.2,
                                "paid" => $paid,
                                "show" => $show];
                }
            }
        }
        usort($epids, function($a, $b) {
            return $a->period < $b->period;
        });
        return response()->json([
                    "promos" => $promos['report'],
                    "claims" => $claims['report'],
                    "claims_adrev" => $claimsAdrev['report'],
                    "claims_indiy" => $claimsIndiy['report'],
                    "moonaz" => $bitlys['report'],
                    "epids" => $epids
        ]);
    }

    public function getReportSummary(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.getReportSummary|request=' . json_encode($request->all()));

        $campaign = new CampaignController();
        $claimController = new ClaimController();
        $bitlyController = new BitlyController();

        $detailPromos = $campaign->getReportPeriodRevDetail($request);
        $detailClaims = $claimController->getClaimReportRevDetail($request);
        $detailBitlys = $bitlyController->getChartMonthDetail($request);
//        if ($request->is_supper_admin) {
        foreach ($detailPromos['users'] as $pr) {
//            Log::info(json_encode($detailPromos['users']));
            $pr->summary = 0;

            //tính promo
            $summary = $pr->kpi;

            //tính claim
            foreach ($detailClaims['users'] as $cl) {
                if ($cl->user_name == $pr->user_name) {
                    $summary += $cl->money;
                }
            }

            //tính bitly
            foreach ($detailBitlys['report'] as $bl) {
                if ($bl->username == $pr->user_name) {
                    $summary += $bl->kpi;
                }
            }
            //tính epid
            $detailEpid = DB::select("select owner as user_name,sum(revenue * 0.2) as money from campaign_claim_rev where type = 4 and period = $request->period group by owner order by sum(revenue) desc ");
            foreach ($detailEpid as $data) {
                if ($pr->user_name == $data->user_name) {
                    $summary += $data->money;
                }
            }
            $pr->summary = $summary;
        }
        $results = json_decode(json_encode($detailPromos['users']));
        usort($results, function($a, $b) {
            return $a->summary < $b->summary;
        });
        return response()->json([
                    "summary" => $results,
        ]);
//        } else {
//            
//        }
    }

    public function epidMonthlyDetail(Request $request) {
        $datas = DB::select("select owner as user_name,sum(revenue * 0.2) as money from campaign_claim_rev where type = 4 and period = $request->period group by owner order by sum(revenue) desc ");
        return response()->json([
                    "status" => "success",
                    "epids" => $datas
        ]);
    }

    public function epidUserDetail(Request $request) {
        $user = Auth::user();
        $datas = DB::select("select owner as user_name,campaign_id as channel_id,revenue * 0.2 as money,revenue as rev,view as views from campaign_claim_rev where type = 4 and period = ? and owner = ? order by revenue desc ", [$request->period, $request->username]);
        foreach ($datas as $data) {
            $account = AccountInfo::where("chanel_id", $data->channel_id)->first();
            $data->channel_name = $account->chanel_name;
            $data->rpm = 0;
            if ($data->views > 0) {
                $data->rpm = round($data->rev / $data->views * 2 * 1000, 2);
            }
        }
        //2025/03/03 epid không cho xem deltail của người khác
        if ($user->user_name != $request->username && !$request->is_supper_admin) {
            $datas = [];
        }
        return response()->json([
                    "status" => "success",
                    "epids" => $datas
        ]);
    }

    //tổng kết số tiền epid 
    public function epidSummary(Request $request) {
        if (!$request->is_supper_admin) {
            return [];
        }
        $users = User::where("status", 1)->where("role", "like", "%26%")->pluck("user_name");
        $results = DB::table('campaign_claim_rev')
                ->select('period', 'owner', DB::raw('ROUND(SUM(revenue) * 0.2) as revenue'))
                ->where('type', '=', '4')
                ->whereIn('owner', $users)
                ->groupBy('period', 'owner')
                ->orderBy('period')
                ->orderBy('owner')
                ->get();
        return response()->json($results);
    }

    //hàm tính view lyric promo theo tháng trong 1 năm đê tổng kết điểm mooncoi
    public function viewsLyricPromo(Request $request) {
        $year = gmdate("Y", time()) - 1;
        if (isset($request->year)) {
            $year = $request->year;
        }
        $results = DB::select("SELECT 
                    u.username,
                    DATE_FORMAT(v.date, '%Y-%m') AS month,
                    SUM(v.views_real_daily) AS total_views
                FROM 
                    (
                        SELECT 
                            username, video_id
                        FROM 
                            campaign
                        WHERE 
                            status_confirm = '3'  
                            AND video_type = '2'
                            AND insert_date >= '$year" . "0101' 
                            AND insert_date <= '$year" . "1231'
                            AND campaign_id IN (
                                SELECT id
                                FROM campaign_statistics
                                WHERE type = '1'
                            )
                            AND username in (select user_name from users where status =1 and role like '%26%')
                        GROUP BY 
                            username,video_id
                        HAVING 
                            COUNT(*) = 1
                    ) u
                JOIN 
                    athena_promo_sync v ON u.video_id = v.video_id
                WHERE 
                    YEAR(v.date) = $year
                GROUP BY 
                    u.username, DATE_FORMAT(v.date, '%Y-%m')
                ORDER BY 
                    u.username, month");
        return response()->json($results);
    }

    public function mooncoinIndex(Request $request) {
        $user = Auth::user();
        $users = $this->genListUserForMoveChannel($user, $request, 1, 3, 0, 0, 1, 0);
        return view('components.mooncoin', [
            "users" => $users
        ]);
    }

    public function getMooncoins(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.getMooncoins|request=' . json_encode($request->all()));
        $year = $request->input('year');
        DB::enableQueryLog();
        $monthlyData = DB::table('mooncoin_values')
                ->join('mooncoin_content', 'mooncoin_values.content_id', '=', 'mooncoin_content.id')
                ->select(
                        'mooncoin_values.username', 'mooncoin_values.mooncoin_value', 'mooncoin_values.month', 'mooncoin_values.year','mooncoin_values.created', 'mooncoin_content.content_description'
                )
                ->when($year, function ($query) use ($year) {
                    return $query->where('mooncoin_values.year', $year);
                })
                ->orderByDesc('mooncoin_values.year')
                ->orderByDesc('mooncoin_values.month')
                ->orderByDesc('mooncoin_values.id')
                ->get()
                ->map(function ($item) {
            // Chuyển đổi số tháng thành tên tháng
            $item->month = date('F', mktime(0, 0, 0, $item->month, 1));
            return $item;
        });

        // Query tổng giá trị mooncoin theo năm
        $yearlyTotals = DB::table('mooncoin_values')
                ->select(
                        'username', DB::raw('SUM(mooncoin_value) as total_mooncoin'), 'year'
                )
                ->when($year, function ($query) use ($year) {
                    return $query->where('year', $year);
                })
                ->groupBy('username', 'year')
                ->orderByDesc(DB::raw('SUM(mooncoin_value)'))
                ->get();
        $chartLabel = [];
        $chartData = [];
        $datasets = [];
        foreach ($yearlyTotals as $data) {
            $chartLabel[] = $data->username;
            $chartData[] = $data->total_mooncoin;
        }
        $datasets[] = (object) [
                    "currency" => "",
                    "label" => "MoonCoin",
                    "data" => $chartData,
                    "fill" => false,
                    "borderColor" => "#039cfd",
                    "backgroundColor" => "#039cfd",
                    "borderWidth" => 1
        ];
        return response()->json([
                    'monthly_data' => $monthlyData,
                    'yearly_totals' => $yearlyTotals,
                    'chart_label' => $chartLabel,
                    'datasets' => $datasets,
        ]);
    }

    public function getDescMooncoin(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.getDescMooncoin|request=' . json_encode($request->all()));
        DB::enableQueryLog();
//        $data = MooncoinContent::where("status", 1)->get();
//        $query = DB::table('campaign_jobs as cj')->where("status",1);
        $month = $request->month;
        $year = $request->year;
        $data = DB::table('mooncoin_content as mc')
                ->whereNotExists(function ($query) use ($month, $year) {
                    $query->select(DB::raw(1))
                    ->from('mooncoin_values as mv')
                    ->whereRaw('mv.content_id = mc.id')
                    ->where('mv.month', $month)
                    ->where('mv.year', $year);
                })
                ->get();

        return response()->json($data);
    }

    public function addDescMooncoin(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.addDescMooncoin|request=' . json_encode($request->all()));
        DB::enableQueryLog();
        if (!$request->is_supper_admin) {
            return response()->json(['status' => "error", 'message' => "You are not an admin"]);
        }
        if (!isset($request->moon_coin)) {
            return response()->json(['status' => "error", 'message' => "You have to enter moon coin"]);
        }
        if (!isset($request->moon_coin_desc)) {
            return response()->json(['status' => "error", 'message' => "you must enter moon desc"]);
        }
        $data = new MooncoinContent();
        $data->content_description = trim($request->moon_coin_desc);
        $data->moon_value = $request->moon_coin;
        $data->save();
        return response()->json(['status' => "success", 'message' => "Success"]);
    }

    public function addMooncoin(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MoneyController.addMooncoin|request=' . json_encode($request->all()));
        DB::enableQueryLog();
        if (!$request->is_supper_admin) {
            return response()->json(['status' => "error", 'message' => "You are not an admin"]);
        }

        if (!isset($request->user)) {
            return response()->json(['status' => "error", 'message' => "You must select user"]);
        }
        $moon = MooncoinContent::where("id", $request->moon_desc)->first();
        if ($moon) {
            if ($moon->multiple == 0) {
                $mValue = MooncoinValues::where("month", $request->moon_month)->where("year", $request->moon_year)->where("content_id", $request->moon_desc)->where("status", 1)->first();
                if ($mValue) {
                    return response()->json(['status' => "error", 'message' => "This mooncoin has been released for $mValue->username"]);
                }
            }
            $data = new MooncoinValues();
            $data->created_by = $user->user_name;
            $data->username = $request->user;
            $data->mooncoin_value = $moon->moon_value;
            $data->content_id = $request->moon_desc;
            $data->month = $request->moon_month;
            $data->year = $request->moon_year;
            $data->created = Utils::timeToStringGmT7(time());
            $data->save();
            $rewardUser = User::where("user_name", $request->user)->first();
            $mess = "$moon->content_description";
            $noti = (object) [
                        "type" => 5,
                        "job_id" => $data->id,
                        "title" => "$rewardUser->name được thưởng $moon->moon_value mooncoin",
                        "message" => $mess,
                        "comment" => null,
                        "redirect" => "",
                        "noti_id" => uniqid(),
                        "name" => $rewardUser->name,
                        "position" => "Được tặng thưởng $moon->moon_value mooncoin",
                        "content" => $mess,
                        "avatar" => "https://automusic.win/images/avatar/$rewardUser->user_name.jpg",
            ];
            $activeUser = User::where("status",1)->where("role","like","%26%")->pluck("user_name");
            event(new ChatEvent($user, ["truongpv"], $noti));
            return response()->json(['status' => "success", 'message' => "Success"]);
        }
        return response()->json(['status' => "error", 'message' => "Not found Moon Description"]);
    }

}
