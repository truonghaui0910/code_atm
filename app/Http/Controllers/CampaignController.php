<?php

namespace App\Http\Controllers;

use App\Common\Logger;
use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\AthenaPromoSync;
use App\Http\Models\Bom;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignChecklists;
use App\Http\Models\CampaignClaimRevStatus;
use App\Http\Models\CampaignComment;
use App\Http\Models\CampaignCommentAuto;
use App\Http\Models\CampaignJobs;
use App\Http\Models\CampaignStaCardEnd;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\CampaignTasks;
use App\Http\Models\Genre;
use App\Http\Models\LockProcess;
use App\Http\Models\Notification;
use App\Http\Models\Tasks;
use App\Http\Models\VideoDaily;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;
use function view;

class CampaignController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.index|request=' . json_encode($request->all()));
        $status = 1;
        $startTime = time();
        if (isset($request->status)) {
            $status = $request->status;
        }
        if ($status != 3) {
            $datas = CampaignStatistics::where("status", $status)->whereIn("type", [1, 4])->get();
        } else {
            $datas = CampaignStatistics::whereIn("type", [1, 4])->get();
        }
//        $temp = DB::select("select export_date from campaign_card order by id desc limit 1");
//        $exportDate = $temp[0]->export_date;
        foreach ($datas as $data) {
//            $data->cards = DB::select("select sum(card_teaser_clicks) as card_teaser_clicks,sum(per_card_teaser_clicks) as per_card_teaser_clicks,sum(card_clicks) as card_clicks,sum(per_card_clicks) as per_card_clicks
//            from campaign_card where video_id in (select video_id from campaign where campaign_id =$data->id ) and report_type =1 ");
//            $data->endscreens = DB::select("select sum(es_shown) as es_shown,sum(es_clicks) as es_clicks,sum(per_es) as per_es 
//            from campaign_endscreen where video_id in (select video_id from campaign where campaign_id =$data->id ) and report_type =1 ");
            $data->cards = DB::select("select card_teaser_clicks,per_card_teaser_clicks,card_clicks,per_card_clicks
            from campaign_sta_card_end where campaign_id= $data->id");
            $data->endscreens = DB::select("select es_shown,es_clicks,per_es 
            from campaign_sta_card_end where campaign_id= $data->id");
//            $data->exportDate = $exportDate;
            $tooltipCard = "Card Teaser Clicks : 0";
            $cardClick = 0;
            $cardTeaserClicks = 0;
            $tooltipEns = "End screen elements shown : 0";
            $esShown = 0;
            $esClicks = 0;
            if (count($data->cards) > 0) {
                $cardClick = $data->cards[0]->card_clicks;
                $cardTeaserClicks = $data->cards[0]->card_teaser_clicks;
                $cardTeaserClicks = $cardTeaserClicks != null ? $cardTeaserClicks : 0;
                $tooltipCard = "Card Teaser Clicks : $cardTeaserClicks";
            }
            if (count($data->endscreens) > 0) {
                $esClicks = $data->endscreens[0]->es_clicks;
                $esShown = $data->endscreens[0]->es_shown;
                $esShown = $esShown != null ? $esShown : 0;
                $tooltipEns = "End screen elements shown : $esShown";
            }
            $data->tooltipCard = $tooltipCard;
            $data->cardClick = $cardClick;
            $data->cardTeaserClicks = $cardTeaserClicks;
            $data->tooltipEns = $tooltipEns;
            $data->esShown = $esShown;
            $data->esClicks = $esClicks;
        }

        $listCampaign = DB::select("select campaign_name from campaign_statistics where type = 1 and status = 1");
//        $campaignType = DB::select("select sum(IF(campaign_type = 'premium', 1, 0)) as premium ,sum(IF(campaign_type = 'regular', 1, 0)) as regular,sum(IF(campaign_type = 'medium', 1, 0)) as medium from campaign_statistics where status =1 ");
        $genres = DB::select("select distinct genre from  campaign_statistics where type in(1,4) and status = $status");
        $campaignType = DB::select("select genre, count(*) as total from  campaign_statistics where type in(1,4) and status = $status group by genre");
//        foreach ($genres as $genre) {
//            $data = array("premium" => 0, "medium" => 0, "regular" => 0);
//            foreach ($campaignType as $cam) {
//                if ($genre->genre == $cam->genre) {
//                    if ($cam->campaign_type == 'premium') {
//                        $data['premium'] = $cam->total;
//                    } else if ($cam->campaign_type == 'medium') {
//                        $data['medium'] = $cam->total;
//                    } else if ($cam->campaign_type == 'regular') {
//                        $data['regular'] = $cam->total;
//                    }
//                }
//                $genre->data = (object) $data;
//            }
//        }
//        Log::info(json_encode($genres));
//        Log:info(DB::getQueryLog());
        Log::info("Time:" . (time() - $startTime));
        return view('components.campaign', [
            'datas' => $datas,
            'listCampaign' => $listCampaign,
            'genres' => $genres,
            'request' => $request,
            'status' => $this->genStatusCampaign($request),
            'group_channel_search' => $this->loadGroupChannelForSeach($request),
            'channel_genre' => $this->loadChannelGenre($request),
            'channel_subgenre' => $this->loadChannelSubGenre($request),
            'channel_tags' => $this->loadChannelTags($request)
        ]);
    }

    public function index2(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.index2|request=' . json_encode($request->all()));
        $status = [1, 2];
        $startTime = time();
        $queries = [];
        $statusArr = [1, 2];
        if (isset($request->status)) {
            $status = $request->status;
            if ($status == 0) {
                $statusArr = [0];
            } else if ($status == 1) {
                $statusArr = [1, 2];
            } else if ($status == 2) {
                $statusArr = [2];
            } else if ($status == 3) {
                $statusArr = [1, 2, 3, 4, 0];
            } else if ($status == 4) {
                $statusArr = [4];
            }
        }
        $datas = CampaignStatistics::whereIn("status", $statusArr)->whereIn("type", [1, 4, 5]);


        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            $request['sort'] = 'id';
            $request['direction'] = 'desc';
            $queries['sort'] = 'id';
            $queries['direction'] = 'desc';
        }
        $datas = $datas->sortable()->paginate(1000)->appends($queries);
//         Log:info(DB::getQueryLog());
        $countSubmission = 0;
        $countPromo = 0;
        $money = 0;
        //đếm số lượng video lyric > 1000 view cho mỗi campaign
        $lyricCounts = DB::select("select campaign_id, count(video_id) as lyric1000 from campaign where video_type =2 and views >=1000 and  campaign_id in (select id from campaign_statistics where status =1 and type in (1,4)) group by campaign_id");
        foreach ($datas as $data) {
            //2023/12/07 add email dash
            $data->artis_info_email = null;
            if ($data->artist_info != null) {
                $artInfo = json_decode($data->artist_info);
                $data->artis_info_email = !empty($artInfo->email) ? $artInfo->email : null;
            }

            //2023/05/09 cắt bớt tên claim
            $data->campaign_name_short = $data->campaign_name;
            if (strlen($data->campaign_name) > 50) {
                $data->campaign_name_short = substr_replace($data->campaign_name, "...", 60, 100);
            }
            $data->cards = DB::select("select card_teaser_clicks,per_card_teaser_clicks,card_clicks,per_card_clicks
            from campaign_sta_card_end where campaign_id= $data->id");
            $data->endscreens = DB::select("select es_shown,es_clicks,per_es 
            from campaign_sta_card_end where campaign_id= $data->id");
//            $data->exportDate = $exportDate;
            $tooltipCard = "Card Teaser Clicks : 0";
            $cardClick = 0;
            $cardTeaserClicks = 0;
            $tooltipEns = "End screen elements shown : 0";
            $esShown = 0;
            $esClicks = 0;
            if (count($data->cards) > 0) {
                $cardClick = $data->cards[0]->card_clicks;
                $cardTeaserClicks = $data->cards[0]->card_teaser_clicks;
                $cardTeaserClicks = $cardTeaserClicks != null ? $cardTeaserClicks : 0;
                $tooltipCard = "Card Teaser Clicks : $cardTeaserClicks";
            }
            if (count($data->endscreens) > 0) {
                $esClicks = $data->endscreens[0]->es_clicks;
                $esShown = $data->endscreens[0]->es_shown;
                $esShown = $esShown != null ? $esShown : 0;
                $tooltipEns = "End screen elements shown : $esShown";
            }
            $data->tooltipCard = $tooltipCard;
            $data->cardClick = $cardClick;
            $data->cardTeaserClicks = $cardTeaserClicks;
            $data->tooltipEns = $tooltipEns;
            $data->esShown = $esShown;
            $data->esClicks = $esClicks;

            //tính toán target
            $data->targetView = [];
            $curr = time();
            $startDate = strtotime($data->campaign_start_date);
            $startDateString = gmdate("Y/m/d", $startDate);
//            $colors = ["bg-info", "bg-success", "bg-warning", "btn-t", "btn-s", "bg-danger"];
            $colors = ["bg-danger", "btn-s", "bg-warning", "bg-success", "bg-info", "btn-t"];
            if ($data->target != null && $data->target != "") {
                $targetArr = explode(",", $data->target);
                $detail = json_decode($data->views_detail);
                $targetView = [];
                for ($i = 0; $i < count($targetArr); $i++) {
                    if (!empty($detail->$i)) {
                        $v = $detail->$i;
                        $color = $colors[$i];
                        $totalTarget = Utils::shortNumber2Number($targetArr[$i]);

                        if ($totalTarget > 0) {
                            $percent = round($v / $totalTarget * 100, 2);
                        } else {
                            $percent = 100;
                        }
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

                        if ($i < 4) {
                            $name = "Week" . ($i + 1);
                            $run = ($startDate + ($i + 1) * 7 * 86400);
                            //tính target của week, bỏ qua month
                            $targetWeek = $totalTarget;
                            $progressWeek = $totalTarget - $v;
                        } else {
                            $run = ($startDate + $data->duration * 30 * 86400);
                            $name = "Month$data->duration";
                            $targetWeek = 0;
                            $progressWeek = 0;
                        }
                        $endDateString = gmdate("Y/m/d", $run);
                        $dayLeft = (($run - $curr) / 86400) + 1;
                        if ($dayLeft <= 0) {
                            $dayLeft = 0;
                            $dayLeftText = "";
                        } else if ($dayLeft <= 2) {
                            $dayLeftText = "- " . round($dayLeft * 24) . " hours left";
                        } else {
                            $dayLeftText = "- " . round($dayLeft, 1) . " days left";
                        }

                        $targetView[] = (object) [
                                    "i" => $i,
                                    "views" => number_format($v, 0, '.', ','),
                                    "target" => number_format($totalTarget, 0, '.', ','),
                                    "progress" => $totalTarget - $v,
                                    "target_number" => $totalTarget,
                                    "progressWeek" => $progressWeek,
                                    "targetWeek" => $targetWeek,
                                    "color" => $color,
                                    "percent" => $percent,
                                    "dayLeftText" => $dayLeftText,
                                    "dayLeft" => $dayLeft,
                                    "name" => $name,
                                    "width" => $percent,
                                    "start" => $startDateString,
                                    "end" => $endDateString,
                        ];
                    }
                }
                $data->targetView = $targetView;
                if (count($targetView) > 0) {
                    $data->curr_dayleft = $targetView[count($targetView) - 1]->dayLeft;
                    $data->curr_percent = $targetView[count($targetView) - 1]->width;
                }
            }

            //đếm tổng tiền của campaign
            $money += $data->money;
            //đếm số lượng video lyric > 1000 view cho mỗi campaign
            foreach ($lyricCounts as $lyricCount) {
                if ($data->id == $lyricCount->campaign_id) {
                    $data->lyric1000 = $lyricCount->lyric1000;
                    break;
                }
            }
            //đếm số promo
            if ($data->type == 1) {
                $countPromo++;
            }

            //đếm số revshare
            if ($data->type == 5) {
                $countSubmission++;
            }

            //dữ liệu cho vẽ progress campaign, official, money
            $data->progress_mix = null;
            $data->progress_official = null;
            $data->progress_official_like = null;
            $data->progress_official_cmt = null;
            $data->progress_official_sub = null;
            $data->progress_money = null;
            $data->official_alert = 0;
            if ($data->money != null) {
                if ($data->money > 0) {
                    $percent = round($data->amount_paid / $data->money * 100, 2);
                } else {
                    $percent = 100;
                }
                $color = $this->genColor($percent);
                $progressMoney = (object) [
                            "name" => "Amount",
                            "achieved" => $data->amount_paid,
                            "target" => $data->money,
                            "color" => $color,
                            "percent" => $percent
                ];
                $data->progress_money = $progressMoney;
            }
            $data->crypto_view_run = 0;
            $data->adsense_view_run = 0;
            if ($data->official != null && $data->official_data != null) {
                //tính cả official_video_versions
                $oVersionViews = 0;
                if ($data->release_info != null) {
                    $ri = json_decode($data->release_info);
                    if (!empty($ri->official_video_versions)) {
                        foreach ($ri->official_video_versions as $oVersion) {
                            if (!empty($oVersion->views)) {
                                $oVersionViews += $oVersion->views;
                            }
                        }
                    }
                }
                $official_target = json_decode($data->official);
                $official_data = json_decode($data->official_data);
                $current = $official_data->view;
                $achieved = $official_data->view - $official_target->start_view - $oVersionViews;
                $target = Utils::shortNumber2Number($official_target->target_view);
                $totalTarget = $target + $official_target->start_view;
                $data->crypto_view_run = $official_target->crypto_view_run;
                $data->adsense_view_run = $official_target->adsense_view_run;
                if ($target > 0) {
                    $percent = round($achieved / $target * 100, 2);
                    if ($percent >= 100) {
                        //nếu trạng thái là đã chạy xong rồi thì ko thông báo
                        if ($official_target->crypto_view_run != 2 && $official_target->adsense_view_run != 2) {
                            $data->official_alert = 1;
                        }
                    }
                } else {
                    $percent = 0;
                }
                $color = $this->genColor($percent);
                $progressOfficial = (object) [
                            "name" => "Official Views",
                            "achieved" => number_format($achieved, 0, '.', ','),
                            "target" => number_format($target, 0, '.', ','),
                            "current" => number_format($current, 0, '.', ','),
                            "target_number" => number_format($totalTarget, 0, '.', ','),
                            "color" => $color,
                            "percent" => $percent
                ];
                $data->progress_official = $progressOfficial;

                //like
                $currentLike = $official_data->like;
                $achievedLike = $official_data->like - $official_target->start_like;
                $targetLike = $official_target->target_like;

                $totalTargetLike = $targetLike + $official_target->start_like;
                if ($targetLike > 0) {
                    $percentLike = round($achievedLike / $targetLike * 100, 2);
                    if ($percentLike >= 100) {
                        if ($official_target->crypto_like_run != 2) {
                            $data->official_alert = 1;
                        }
                    }
                } else {
                    $percentLike = 0;
                }
                $colorLike = $this->genColor($percentLike);
                $progressOfficialLike = (object) [
                            "name" => "Official Likes",
                            "achieved" => number_format($achievedLike, 0, '.', ','),
                            "target" => number_format($targetLike, 0, '.', ','),
                            "current" => number_format($currentLike, 0, '.', ','),
                            "target_number" => number_format($totalTargetLike, 0, '.', ','),
                            "color" => $colorLike,
                            "percent" => $percentLike
                ];
                $data->progress_official_like = $progressOfficialLike;

                //cmt
                $currentCmt = $official_data->comment;
                $achievedCmt = $official_data->comment - $official_target->start_cmt;
                $targetCmt = $official_target->target_cmt;
                $totalTargetCmt = $targetCmt + $official_target->start_cmt;
                if ($targetCmt > 0) {
                    $percentCmt = round($achievedCmt / $targetCmt * 100, 2);
                    if ($percentCmt >= 100) {
                        if ($official_target->crypto_cmt_run != 2) {
                            $data->official_alert = 1;
                        }
                    }
                } else {
                    $percentCmt = 0;
                }
                $colorCmt = $this->genColor($percentCmt);
                $progressOfficialCmt = (object) [
                            "name" => "Official Cmt",
                            "achieved" => number_format($achievedCmt, 0, '.', ','),
                            "target" => number_format($targetCmt, 0, '.', ','),
                            "current" => number_format($currentCmt, 0, '.', ','),
                            "target_number" => number_format($totalTargetCmt, 0, '.', ','),
                            "color" => $colorCmt,
                            "percent" => $percentCmt
                ];
                $data->progress_official_cmt = $progressOfficialCmt;

                //cảnh báo cần chỉnh sửa cấu hình chạy comment
                $data->comment_setting_alert = 0;
                if ($official_target->crypto_cmt_run == 1 && ($official_target->crypto_cmt_content == "")) {
                    $data->comment_setting_alert = 1;
                }

                //subs
                $currentSub = $official_data->sub;
                $achievedSub = $official_data->sub - $official_target->start_sub;
                $targetSub = $official_target->target_sub;
                $totalTargetSub = $targetSub + $official_target->start_sub;
                if ($targetSub > 0) {
                    $percentSub = round($achievedSub / $targetSub * 100, 2);
                    if ($percentSub >= 100) {
                        if ($official_target->crypto_sub_run != 2) {
                            $data->official_alert = 1;
                        }
                    }
                } else {
                    $percentSub = 0;
                }
                $colorSub = $this->genColor($percentSub);
                $progressOfficialSub = (object) [
                            "name" => "Official Subs",
                            "achieved" => number_format($achievedSub, 0, '.', ','),
                            "target" => number_format($targetSub, 0, '.', ','),
                            "current" => number_format($currentSub, 0, '.', ','),
                            "target_number" => number_format($totalTargetSub, 0, '.', ','),
                            "color" => $colorSub,
                            "percent" => $percentSub
                ];
                $data->progress_official_sub = $progressOfficialSub;
            } else {
                $data->progress_official = '';
                $data->progress_official_like = '';
                $data->progress_official_cmt = '';
                $data->progress_official_sub = '';
            }


            if (!Utils::containString($data->target, ",")) {
                $id = 0;
                $startDate = strtotime($data->campaign_start_date);
                $startDateString = gmdate("Y/m/d", $startDate);
                $run = ($startDate + $data->duration * 30 * 86400);
                $endDateString = gmdate("Y/m/d", $run);
                $dayLeft = (($run - $curr) / 86400) + 1;
                if ($dayLeft <= 0) {
                    $dayLeft = 0;
                    $dayLeftText = "";
                } else if ($dayLeft <= 2) {
                    $dayLeftText = "- " . round($dayLeft * 24) . " hours left";
                } else {
                    $dayLeftText = "- " . round($dayLeft, 1) . " days left";
                }
                $current = 0;
                if ($data->views_detail != null) {
                    $current = !empty(json_decode($data->views_detail)->$id) ? json_decode($data->views_detail)->$id : 0;
                }
                $totalTarget = Utils::shortNumber2Number($data->target);
                if ($totalTarget > 0) {
                    $percent = round($current / $totalTarget * 100, 2);
                } else {
                    $percent = 0;
                }
                $color = $this->genColor($percent);
                $progressMix = (object) [
                            "name" => "Mix-Lyric Views",
                            "views" => number_format($current, 0, '.', ','),
                            "target" => number_format($totalTarget, 0, '.', ','),
                            "color" => $color,
                            "percent" => $percent,
                            "dayLeftText" => $dayLeftText,
                            "dayLeft" => $dayLeft,
                            "start" => $startDateString,
                            "end" => $endDateString,
                ];
                $data->progress_mix = $progressMix;
            }

            //2023/08/30 check list progress
            $data->progress_checklist = null;
            if ($data->check_lists != null) {
                $checkListArr = json_decode($data->check_lists);
                $done = 0;
                $percent = 0;
//                $totalCheckList = count($checkListArr);
                $totalCheckList = 0;
                foreach ($checkListArr as $cl) {
                    if ($cl->is_finish == 1 || $cl->result != "") {
                        $done++;
                    }
                    if (isset($cl->is_job)) {
                        if ($cl->is_job == '1') {
                            $totalCheckList++;
                        }
                    }
                }
                if ($totalCheckList > 0) {
                    $percent = round($done / $totalCheckList * 100, 2);
                }
                $color = $this->genColor($percent);
                $progressChecklist = (object) [
                            "name" => "Check List",
                            "achieved" => $done,
                            "target" => $totalCheckList,
                            "color" => $color,
                            "percent" => $percent,
                ];
                $data->progress_checklist = $progressChecklist;
            }
            //2023/09/08 báo campaign chuẩn bị đến ngày chạy, T7=6,CN=0,T2=1 báo vào thứ 6, còn lại báo trước 1 ngày.
            $campaignStart = strtotime($data->campaign_start_date);
            $weekDate = date("w", $campaignStart);
            $rangeAlert = 86400;
            if ($weekDate == 0) {
                $rangeAlert = 2 * 86400;
            } else if ($weekDate == 6) {
                $rangeAlert = 1 * 86400;
            } else if ($weekDate == 1) {
                $rangeAlert = 3 * 86400;
            }
            $data->is_alert = 0;
            if ($campaignStart > time() && $campaignStart - time() <= $rangeAlert) {
                $data->is_alert = 1;
            }

            //xử lý note
            $data->some_note = null;
            if ($data->short_text != null) {
                $st = json_decode($data->short_text);
                $data->some_note = !empty($st->some_note) ? $st->some_note : null;
            }
        }
//        $campaignType = DB::select("select sum(IF(campaign_type = 'premium', 1, 0)) as premium ,sum(IF(campaign_type = 'regular', 1, 0)) as regular,sum(IF(campaign_type = 'medium', 1, 0)) as medium from campaign_statistics where status =1 ");
        $genres = DB::select("select genre,count(*) as total from  campaign_statistics where type in(1,4) and status in (" . implode(",", $statusArr) . ") group by  genre");
        $monday = strtotime('monday this week');
        $listGenres = DB::select("select id,name,target from genre where status =1 and type =1 and parent_id =0");
        $listDate = [];
        for ($i = 1; $i <= 7; $i++) {
            $listDate[] = date("m/d/Y", $monday);
            $monday = $monday + 86400;
        }

        foreach ($genres as $genre) {
            $genre->progressTotal = 0;
            $genre->targetTotal = 0;
            $genre->targetWeek = 0;
            $genre->progressWeek = 0;
            $genre->victor_target = 0;
            $genre->genre_id = 0;
            foreach ($datas as $data) {
                if ($genre->genre == $data->genre) {
                    if (count($data->targetView) > 0) {
                        // tính ra số view cần phải kéo của campaign đến thời điểm hiện tại
                        $targetDetail = $data->targetView[count($data->targetView) - 1];
                        $proTotal = $targetDetail->progress;
                        if ($proTotal < 0) {
                            $proTotal = 0;
                        }
                        $genre->targetWeek = $genre->targetWeek + $targetDetail->targetWeek;
                        $genre->progressWeek = $genre->progressWeek + $targetDetail->progressWeek;
                        $genre->targetTotal = $genre->targetTotal + $targetDetail->target_number;
                        $genre->progressTotal = $genre->progressTotal + $proTotal;
                    }
                }
            }
            foreach ($listGenres as $g) {
                if ($genre->genre == $g->name) {
                    $genre->victor_target = $g->target;
                    $genre->genre_id = $g->id;
                }
            }
        }
//        Log::info(json_encode($genres));
//        
        //list active campaign promo
        $promoClaims = CampaignStatistics::where("status", 1)->orderBy("id", "desc")->get(["id", "type", "campaign_name", "artist", "song_name", "genre"]);
        $commentConfig = CampaignCommentAuto::where("status", 1)->get();
        $videosComment = DB::select("select campaign_id,count(*) as videos from campaign where status_confirm = 3 and video_type in (2,5) and views >=10000 and campaign_id in (select id from campaign_statistics where status =1) group by campaign_id");
        foreach ($commentConfig as $config) {
            $config->remain = count(explode("@;@", str_replace(array("\r\n", "\n"), "@;@", $config->comments)));
        }
//        Log::info(json_encode($commentConfig));
        foreach ($promoClaims as $proCam) {
            $proCam->cam_type = " - ";
            if ($proCam->type == 1) {
                $proCam->cam_type = "[PROMO] - ";
            } else if ($proCam->type == 2) {
                if (Utils::containString($proCam->campaign_name, "COVER")) {
                    $proCam->cam_type = "[COVER] - ";
                } else if (Utils::containString($proCam->campaign_name, "ELECTRONIC")) {
                    $proCam->cam_type = "[ELECTRONIC] - ";
                } else if (Utils::containString($proCam->campaign_name, "ORIGINAL")) {
                    $proCam->cam_type = "[ORIGINAL] - ";
                }
            } else if ($proCam->type == 5) {
                $proCam->cam_type = "[SUBMISSION] - ";
            }
            $proCam->remain_comment = 0;
            foreach ($commentConfig as $config) {
                if ($config->campaign_id == $proCam->id) {
                    $proCam->remain_comment = $config->remain;
                    break;
                }
            }
            $proCam->video_comment = 0;
            foreach ($videosComment as $video) {
                if ($video->campaign_id == $proCam->id) {
                    $proCam->video_comment = $video->videos;
                    break;
                }
            }
        }

        $mainChannels = $this->loadMainChannelsDatas();
        $dataChannels = [];
        foreach ($mainChannels as $channel) {
            $tmp = (object) [
                        "user" => Utils::getUserFromUserCode($channel->user_name),
                        "channel_name" => $channel->chanel_name
            ];
            $dataChannels[] = $tmp;
        }
        return view('components.campaign2', [
            'promoClaims' => $promoClaims,
            'datas' => $datas,
            'genres' => $genres,
            'money' => $money,
            'count_promo' => $countPromo,
            'countSubmission' => $countSubmission,
            'request' => $request,
            'status' => $this->genStatusCampaign($request),
            'group_channel_search' => $this->loadGroupChannelForSeach($request),
            'channel_genre' => $this->loadChannelGenre($request),
            'channel_subgenre' => $this->loadChannelSubGenre($request),
            'channel_tags' => $this->loadChannelTags($request),
            'month_select' => $this->genMonthSelect(),
            'dataChannels' => json_encode($dataChannels)
        ]);
    }

    public function boomvip(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.boomvip|request=' . json_encode($request->all()));
        $startTime = time();
        $month = 1;
        if (isset($request->month)) {
            $month = $request->month;
        }
        $t = time() - $month * 30 * 86400;
        if (in_array("20", explode(",", $user->role)) || in_array("1", explode(",", $user->role))) {
            $datas = AccountInfo::whereIn("is_music_channel", [1, 2])->whereIn("is_boomvip", [1, 2])->select(DB::raw("id,substring_index(user_name,'_',1) as username,user_name,channel_genre,chanel_id,chanel_name,view_count,subscriber_count,boomvip_time,is_boomvip"));

            //vẫn lấy kênh bomvip đã bị loại khỏi list
            $t = time() - $month * 30 * 86400;

            $cams = DB::select("select distinct c.video_id,c.channel_id,c.username,c.views,c.is_boomvip,c.is_bommix,c.is_claim  from (select b.campaign_id,b.username,b.channel_id, b.video_id,b.views,b.is_bommix,a.is_boomvip,b.is_claim from accountinfo a ,campaign b where (a.is_boomvip = 1 or (a.is_boomvip = 2 and a.boomvip_time is not null and a.boomvip_time >= $t)) and a.chanel_id = b.channel_id and b.status_confirm = 3 and video_type <> 1 and b.campaign_id in (select id from campaign_statistics where type in (1,2,4,5) and status =1)) c");
        } else {
            $datas = AccountInfo::where('user_name', $user->user_code)->whereIn("is_music_channel", [1, 2])->whereIn("is_boomvip", [1, 2])->select(DB::raw("id,substring_index(user_name,'_',1) as username,user_name,channel_genre,chanel_id,chanel_name,view_count,subscriber_count,boomvip_time,is_boomvip"));
            $cams = DB::select("select distinct c.video_id, c.channel_id,c.username,c.views,c.is_boomvip,c.is_bommix,c.is_claim  from (select b.campaign_id,b.username,b.channel_id, b.video_id,b.views,b.is_bommix,a.is_boomvip,b.is_claim from accountinfo a ,campaign b where a.user_name = '$user->user_code' and (a.is_boomvip = 1 or (a.is_boomvip = 2 and a.boomvip_time is not null and a.boomvip_time >= $t)) and a.chanel_id = b.channel_id and b.status_confirm = 3 and video_type <> 1 and b.campaign_id in (select id from campaign_statistics where type in (1,2,4,5) and status =1)) c");
        }
        if (isset($request->c5) && $request->c5 != "-1") {
            $datas = $datas->where('user_name', $request->c5);
            $queries['user_name'] = $request->c5;
        }
        if (isset($request->c1) && $request->c1 != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('chanel_name', 'like', '%' . $request->c1 . '%')->orWhere('id', $request->c1)->orWhere('note', 'like', '%' . $request->c1 . '%')->orWhere('chanel_id', 'like', '%' . $request->c1 . '%');
                if (Utils::containString($request->c1, ",")) {
                    $arrayChannel = explode(',', $request->c1);
                    $q->orWhereIn("chanel_id", $arrayChannel)->orWhereIn("id", $arrayChannel);
                }
            });
            $queries['c1'] = $request->c1;
        }
        if (isset($request->channel_genre) && $request->channel_genre != "-1") {
            $datas = $datas->where('channel_genre', $request->channel_genre);
            $queries['channel_genre'] = $request->channel_genre;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'id';
            $request['order'] = 'asc';
            $queries['sort'] = 'id';
            $queries['order'] = 'asc';
        }
        $datas = $datas->sortable()->paginate(1000)->appends($queries);
        $listUsers = [];
        $listUsersObjects = [];
        $listGenres = [];
//        $startTime = time();
        foreach ($datas as $data) {
            if (!in_array($data->channel_genre, $listGenres)) {
                $listGenres[] = $data->channel_genre;
            }
            if (!in_array($data->username, $listUsers)) {
                $listUsers[] = $data->username;
            }
        }

        foreach ($listUsers as $u) {
            $viewPromosTotal = 0;
            $countPromosTotal = 0;
            $viewPromosBoommix = 0;
            $countPromosBoommix = 0;
            $viewTotal = 0;
            $countVideo = 0;
            $countChannel = 0;
            $countChannelRemove = 0;
            $countAllVideo = 0;
            $countAllChannel = 0;
            $countAllChannelRemove = 0;
            foreach ($cams as $cam) {
                if ($u == $cam->username) {
                    $countVideo++;
                    $countPromosTotal = $countPromosTotal + 1;
                    $viewPromosTotal = $viewPromosTotal + $cam->views;
                    if ($cam->is_bommix == 1) {
                        $viewPromosBoommix = $viewPromosBoommix + $cam->views;
                        $countPromosBoommix = $countPromosBoommix + 1;
                    }
                }
            }
            foreach ($datas as $data) {
                if ($data->is_boomvip == 1) {
                    $countAllChannel++;
                } else {
                    $countAllChannelRemove++;
                }
                if ($u == $data->username) {
                    $viewTotal = $viewTotal + $data->view_count;
                    if ($data->is_boomvip == 1) {
                        $countChannel++;
                    } else {
                        $countChannelRemove++;
                    }
                }
            }
            $listUsersObjects[] = (object) [
                        "username" => $u,
                        "view_promo_bommix" => $viewPromosBoommix,
                        "view_promo_total" => $viewPromosTotal,
                        "count_promo_bommix" => $countPromosBoommix,
                        "count_promo_total" => $countPromosTotal,
                        "view_total" => $viewTotal,
                        "count_video" => $countVideo,
                        "count_channel" => $countChannel,
                        "count_channel_remove" => $countChannelRemove,
            ];
        }

        $allViewPromosTotal = 0;
        $allViewTotal = 0;
        foreach ($datas as $data) {
            $allViewTotal = $allViewTotal + $data->view_count;
            if (!in_array($data->channel_genre, $listGenres)) {
                $listGenres[] = $data->channel_genre;
            }
            if (!in_array($data->username, $listUsers)) {
                $listUsers[] = $data->username;
            }

            $viewPromosTotal = 0;
            $countPromosTotal = 0;
            $viewPromosBoommix = 0;
            $countPromosBoommix = 0;
            $viewClaimBoommix = 0;
            $countClaimBoommix = 0;
            foreach ($cams as $cam) {
                if ($data->chanel_id == $cam->channel_id) {
                    $countPromosTotal = $countPromosTotal + 1;
                    $viewPromosTotal = $viewPromosTotal + $cam->views;
                    if ($cam->is_bommix == 1) {
                        $viewPromosBoommix = $viewPromosBoommix + $cam->views;
                        $countPromosBoommix = $countPromosBoommix + 1;
                    }
                    if ($cam->is_claim == 1) {
                        $viewClaimBoommix = $viewClaimBoommix + $cam->views;
                        $countClaimBoommix = $countClaimBoommix + 1;
                    }
                }
            }
            $data->view_claim_bommix = $viewClaimBoommix;
            $data->count_claim_bommix = $countClaimBoommix;
            $data->view_promo_bommix = $viewPromosBoommix;
            $data->view_promo_total = $viewPromosTotal;
            $data->count_promo_bommix = $countPromosBoommix;
            $data->count_promo_total = $countPromosTotal;
        }

        foreach ($cams as $cam) {
            $countAllVideo++;
            $allViewPromosTotal = $allViewPromosTotal + $cam->views;
        }
        if (!isset($request->c5) || $request->c5 == "-1") {
            $allObject = (object) [
                        "username" => "All",
                        "view_promo_total" => $allViewPromosTotal,
                        "view_total" => $allViewTotal,
                        "count_video" => $countAllVideo,
                        "count_channel" => $countAllChannel,
                        "count_channel_remove" => $countAllChannelRemove,
            ];
            array_unshift($listUsersObjects, $allObject);
        }
//        Log::info(DB::getQueryLog());
//        Log::info("Time:" . (time() - $startTime));
        return view('components.bomvip', [
            'datas' => $datas,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'listUser' => $this->genListUserForMoveChannel($user, $request, 2, 2),
            "genres" => $this->loadChannelGenre($request),
            "listUsersObjects" => $listUsersObjects,
        ]);
    }

    public function detailcampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.detailcampaign|request=' . json_encode($request->all()));
        $condition = "";
        $camStatic = CampaignStatistics::where("id", $request->id)->first();
        if (in_array("20", explode(",", $user->role)) || in_array("1", explode(",", $user->role))) {
            $datas = Campaign::where('campaign_id', $request->id)->where("status_confirm", 3)->orderBy("publish_date", "desc")->get();
            //2024/09/20 tài khoản a giang được quền xem hết video đê thêm views ads với promo, sau này nhiều user thì sẽ chuyển thành role
        } else if ($user->user_name == 'quocgiangmusic' && $camStatic->type == 1) {
            $datas = Campaign::where('campaign_id', $request->id)->where("status_confirm", 3)->orderBy("publish_date", "desc")->get();
        } else {
            $condition = " and username='$user->user_name' ";
            $datas = Campaign::where('campaign_id', $request->id)->where("username", $user->user_name)->where("status_use", 1)->where("status_confirm", 3)->orderBy("views", "desc")->get();
        }
//        $cards = DB::select("select video_id,sum(card_teaser_clicks) as card_teaser_clicks,sum(per_card_teaser_clicks) as per_card_teaser_clicks,sum(card_clicks) as card_clicks,sum(per_card_clicks) as per_card_clicks
// from campaign_card where video_id in (select video_id from campaign where campaign_id =$request->id and status_confirm =3 $condition)  and report_type =1 group by video_id");
//        $endscreens = DB::select("select video_id,sum(es_shown) as es_shown,sum(es_clicks) as es_clicks,sum(per_es) as per_es
//from campaign_endscreen where video_id in (select video_id from campaign where campaign_id =$request->id and status_confirm =3 $condition) and report_type =1 group by video_id");

        foreach ($datas as $data) {
            $data->card_teaser_clicks = 0;
            $data->card_clicks = 0;
            $data->es_shown = 0;
            $data->es_clicks = 0;
//            foreach ($cards as $card) {
//                if ($card->video_id == $data->video_id) {
//                    $data->card_teaser_clicks = $card->card_teaser_clicks;
//                    $data->card_clicks = $card->card_clicks;
//                    break;
//                }
//            }
//            foreach ($endscreens as $endscreen) {
//                if ($endscreen->video_id == $data->video_id) {
//                    $data->es_shown = $endscreen->es_shown;
//                    $data->es_clicks = $endscreen->es_clicks;
//                    break;
//                }
//            }
        }
//        Log::info(json_encode($datas));
//        return $datas;
        return response()->json(["datas" => $datas, "month" => $this->genMonthSelect()]);
    }

    public function addcampaign(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $i = 0;
        $curr = time();
        Log::info($user->user_name . '|CampaignController.addcampaign|request=' . json_encode($request->all()));
        $listCampaign = DB::select("select campaign_name from campaign_statistics where type = 1 and status = 1");
        $option = "<option value=''>--Select--</option>";
        foreach ($listCampaign as $campaign) {
            $option .= "<option value='$campaign->campaign_name'>$campaign->campaign_name</option>";
        }
        if ($request->campaign_name == null || $request->campaign_name == "") {
            return array("status" => "error", "content" => "Campaign can not be empty", "option" => $option, "contentedit" => "Success");
        }
//        if ($request->deezer_artist_id == "" || $request->deezer_artist_id == 0) {
//            return array("status" => "error", "content" => "Deezer Artists ID invalid", "option" => $option, "contentedit" => "Success");
//        }
        if ($request->cam_id == null) {
            $checkCampaign = CampaignStatistics::where("campaign_name", $request->campaign_name)->first();
            if (!$checkCampaign) {
                $campaignStatistics = new CampaignStatistics();
                $type = 1;
                if (isset($request->cam_type)) {
                    $type = $request->cam_type;
                }
                $campaignStatistics->type = $type;
                if ($request->status != 3) {
                    $campaignStatistics->status = $request->status;
                }
                if ($type == 4) {
                    if ($request->revshare_client != "-1") {
                        $campaignStatistics->revshare_client = $request->revshare_client;
                    } else {
                        return array("status" => "error", "content" => "You have to choose Revshare Client", "option" => $option, "contentedit" => "Success");
                    }
                } else {
                    $campaignStatistics->revshare_client = null;
                }
                if ($type == 1 || $type == 5) {
                    if ($request->money == null) {
                        return array("status" => "error", "content" => "You have to enter money", "option" => $option, "contentedit" => "Success");
                    } else {
                        $campaignStatistics->money = trim($request->money);
//                        if ($campaignStatistics->money < 10) {
//                            return array("status" => "error", "content" => "Money must be greater than or equal to 10", "option" => $option, "contentedit" => "Success");
//                        }
                    }
                    $amoutPaid = 0;
                    if ($request->amount_paid != null) {
                        $amoutPaid = trim($request->amount_paid);
                    }
                    $campaignStatistics->amount_paid = $amoutPaid;
                }
                $campaignStatistics->bass_percent = $request->bass_percent;
                $campaignStatistics->channel_name = $request->channelName;
                $campaignStatistics->campaign_name = $request->campaign_name;
                $campaignStatistics->campaign_type = $request->campaign_type;
                $campaignStatistics->campaign_start_date = $request->campaign_start_date;
                $campaignStatistics->period = $request->period;
                $campaignStatistics->campaign_start_time = $request->campaign_start_time;

                $campaignStatistics->genre = $request->genre;
                $campaignStatistics->label = $request->label;
                $campaignStatistics->artist = $request->artist;
                $campaignStatistics->song_name = $request->songname;

                $campaignStatistics->artists_channel = $request->artists_channel;
                $campaignStatistics->artists_playlist = $request->artists_playlist;
                $campaignStatistics->artists_social = $request->artists_social;
                $campaignStatistics->official_video = $request->official_video;
                $campaignStatistics->start_views_official = $request->start_views_official;
                $info = YoutubeHelper::processLink($request->official_video);
                if ($info["type"] == 3) {
                    $video_id = $info["data"];
                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                    if ($videoInfo["status"] == 0) {
                        for ($t = 0; $t < 5; $t++) {
                            error_log("addcampaign Retry $video_id");
                            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                            if ($videoInfo["status"] == 1) {
                                break;
                            }
                        }
                    }

                    //thêm thông tin chạy official
                    $official = (object) [
                                "video_id" => isset($videoInfo["video_id"]) ? $videoInfo["video_id"] : "",
                                "start_view" => $request->start_view_official,
                                "start_like" => $request->start_like_official,
                                "start_cmt" => $request->start_cmt_official,
                                "start_sub" => $request->start_sub_official,
                                "target_view" => $request->target_view,
                                "target_like" => $request->target_like,
                                "target_cmt" => $request->target_cmt,
                                "target_sub" => $request->target_sub,
                                "crypto_usd" => $request->crypto_usd,
                                "crypto_usd_last" => $request->crypto_usd_last,
                                "crypto_view" => $request->crypto_view,
                                "crypto_view_last" => $request->crypto_view_last,
                                "crypto_view_run" => $request->crypto_view_run,
                                "crypto_like" => $request->crypto_like,
                                "crypto_like_last" => $request->crypto_like_last,
                                "crypto_like_run" => $request->crypto_like_run,
                                "crypto_sub" => $request->crypto_sub,
                                "crypto_sub_last" => $request->crypto_sub_last,
                                "crypto_sub_run" => $request->crypto_sub_run,
                                "crypto_cmt" => $request->crypto_cmt,
                                "crypto_cmt_last" => $request->crypto_cmt_last,
                                "crypto_cmt_run" => $request->crypto_cmt_run,
                                "crypto_cmt_link" => $request->crypto_cmt_link,
                                "crypto_cmt_content" => $request->crypto_cmt_content,
//                                "crypto_cmt_content_finish" => null,
                                "cmt_schedule" => $request->cmt_schedule,
                                "cmt_number" => $request->cmt_number,
                                "adsense_usd" => $request->adsense_usd,
                                "adsense_view" => $request->adsense_view,
                                "adsense_view_run" => $request->adsense_view_run,
                                "facebook_usd" => $request->facebook_usd
                    ];
                    $campaignStatistics->official = json_encode($official);
                }
//                else {
//                    return array("status" => "error", "content" => "Official Video invalid", "option" => $option, "contentedit" => "Success");
//                }

                $campaignStatistics->bitly_url = $request->bitly_url;
                $campaignStatistics->guest_artist_1 = $request->guest_artist_1;
                $campaignStatistics->guest_artist_2 = $request->guest_artist_2;
                $campaignStatistics->guest_artist_3 = $request->guest_artist_3;

                $campaignStatistics->promo_keywords = $request->promo_keywords;
                $campaignStatistics->homepage_video_update = $request->homepage_video_update;
                if ($campaignStatistics->audio_url != $request->audio_url) {
                    $campaignStatistics->audio_url = $request->audio_url;
                    $campaignStatistics->audio_url_cut = null;
                }

                $campaignStatistics->lyrics = $request->lyrics;
                $campaignStatistics->deezer_artist_id = $request->deezer_artist_id;

                $campaignStatistics->views_detail = '{"0":1}';
                $campaignStatistics->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                $campaignStatistics->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
//                if (isset($request->target)) {
//                    $campaignStatistics->target = strtoupper($request->target);
//                }
                if (isset($request->target_total)) {
                    $campaignStatistics->target = strtoupper($request->target_total);
                }
                if (isset($request->campaign_duration)) {
                    $campaignStatistics->duration = $request->campaign_duration;
                }
                if ($type == 1 || $type == 4) {
                    $campaignStatistics->seller = $request->seller;
                }
                $campaignStatistics->tier = $request->tier;
                if (Utils::containString($request->spotify_id, "spotify.com")) {
                    if (preg_match("/https:\/\/open.spotify.com\/track\/(\w+)/", $request->spotify_id, $re)) {
                        $spotifyId = $re[1];
                    }
                } else {
                    $spotifyId = trim($request->spotify_id);
                }
                $campaignStatistics->spotify_id = $spotifyId;
                $campaignStatistics->save();
                $campaignId = $campaignStatistics->id;
                if (isset($videoInfo)) {
                    if ($videoInfo["status"]) {
                        $campaign = new Campaign();
                        $campaign->username = $user->user_name;
                        $campaign->campaign_id = $campaignId;
                        $campaign->campaign_name = $request->campaign_name;
                        $campaign->channel_id = $videoInfo["channelId"];
                        $campaign->channel_name = $videoInfo["channelName"];
                        $campaign->video_type = 1;
                        $campaign->video_id = $video_id;
                        $campaign->video_title = $videoInfo["title"];
                        $campaign->views_detail = '[]';
                        $campaign->status = $videoInfo["status"];
                        $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                        $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                        $campaign->publish_date = $videoInfo["publish_date"];
                        $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                        $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                        $campaign->status_confirm = 3;
                        $campaign->save();
                    }
                }
//                else {
//                    return array("status" => "error", "content" => "Can not found info of Official video", "option" => $option, "contentedit" => "Success");
//                }
            } else {
                return array("status" => "error", "content" => "Campaign name already exists", "option" => $option, "contentedit" => "Success");
            }
        } else {
            $campaignStatistics = CampaignStatistics::find($request->cam_id);
            if ($campaignStatistics) {
                if (isset($request->cam_type)) {
                    $campaignStatistics->type = $request->cam_type;
                }
                if ($request->cam_type == 4) {
                    if ($request->revshare_client != "-1") {
                        $campaignStatistics->revshare_client = $request->revshare_client;
                    } else {
                        return array("status" => "error", "content" => "You have to choose Revshare Client", "option" => $option, "contentedit" => "Success");
                    }
                } else {
                    $campaignStatistics->revshare_client = null;
                }
                if ($request->cam_type == 1 || $request->cam_type == 5) {
                    if ($request->money == null) {
                        $money = 0;
//                        return array("status" => "error", "content" => "You have to enter money", "option" => $option, "contentedit" => "Success");
                    } else {
                        $money = trim($request->money);
//                        if ($money < 10) {
//                            return array("status" => "error", "content" => "Money must be greater than or equal to 10", "option" => $option, "contentedit" => "Success");
//                        }
                    }
                    if ($money != $campaignStatistics->money) {
                        $campaignStatistics->log = $campaignStatistics->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name change money from  $campaignStatistics->money to $request->money";
                        $campaignStatistics->money = $money;
                    }
                    $amountPaid = 0;
                    if ($request->amount_paid != null) {
                        $amountPaid = trim($request->amount_paid);
                    }
                    if ($amountPaid != $campaignStatistics->amount_paid) {
                        $campaignStatistics->log = $campaignStatistics->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name change amount_paid from  $campaignStatistics->amount_paid to $request->amount_paid";
                        $campaignStatistics->amount_paid = $amountPaid;
                    }
                }
                $campaignStatistics->bass_percent = $request->bass_percent;
                $campaignStatistics->channel_name = $request->channelName;
                $lastCampaignName = $campaignStatistics->campaign_name;
                $campaignStatistics->campaign_name = $request->campaign_name;
//                $campaignStatistics->campaign_type = $request->campaign_type;
                $campaignStatistics->campaign_start_date = $request->campaign_start_date;
                $campaignStatistics->period = $request->period;
                $campaignStatistics->campaign_start_time = $request->campaign_start_time;
                if ($request->genre != "-1" && $request->genre != "") {
                    $campaignStatistics->genre = strtoupper($request->genre);
                }
                $campaignStatistics->label = $request->label;
                $campaignStatistics->artist = $request->artist;
                $campaignStatistics->song_name = $request->songname;

                $campaignStatistics->artists_channel = $request->artists_channel;
                $campaignStatistics->artists_playlist = $request->artists_playlist;
                $campaignStatistics->artists_social = $request->artists_social;
                $campaignStatistics->desc_video = $request->desc_video;
                $campaignStatistics->start_views_official = $request->start_views_official;
                if ($campaignStatistics->official != null) {
                    $official = (array) json_decode($campaignStatistics->official);
                } else {
                    $official = [];
                }
                if ($campaignStatistics->official_video != $request->official_video) {
                    //2023/12/19 không lưu official_video, để đồng bộ trên dash về
//                    $campaignStatistics->official_video = $request->official_video;
                    $info = YoutubeHelper::processLink($request->official_video);
                    if ($info["type"] == 3) {
                        $video_id = $info["data"];
                        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                        if ($videoInfo["status"] == 0) {
                            for ($t = 0; $t < 15; $t++) {
                                error_log("addcampaign Retry $video_id");
                                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                                if ($videoInfo["status"] == 1) {
                                    break;
                                }
                            }
                        }
                        $official['video_id'] = $videoInfo['video_id'];
                        if (isset($videoInfo)) {
                            if ($videoInfo["status"]) {
                                $campaign = Campaign::where("campaign_id", $request->cam_id)->where("video_type", 1)->first();
                                if (!$campaign) {
                                    $campaign = new Campaign();
                                }
                                $campaign->username = $user->user_name;
                                $campaign->campaign_id = $request->cam_id;
                                $campaign->campaign_name = $request->campaign_name;
                                $campaign->channel_id = $videoInfo["channelId"];
                                $campaign->channel_name = $videoInfo["channelName"];
                                $campaign->video_type = 1;
                                $campaign->video_id = $videoInfo['video_id'];
                                $campaign->video_title = $videoInfo["title"];
                                $campaign->views_detail = '[]';
                                $campaign->status = $videoInfo["status"];
                                $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                                $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                                $campaign->publish_date = $videoInfo["publish_date"];
                                $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                                $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                                $campaign->status_confirm = 3;
                                $campaign->save();
                            }
                        }
                    }
//                else {
//                    return array("status" => "error", "content" => "Official Video invalid", "option" => $option, "contentedit" => "Success");
//                }
                }

                $official["start_view"] = $request->start_view_official;
                $official["start_like"] = $request->start_like_official;
                $official["start_cmt"] = $request->start_cmt_official;
                $official["start_sub"] = $request->start_sub_official;
                $official["target_view"] = $request->target_view;
                $official["target_like"] = $request->target_like;
                $official["target_cmt"] = $request->target_cmt;
                $official["target_sub"] = $request->target_sub;
                $official["crypto_usd"] = $request->crypto_usd;
                $official["crypto_usd_last"] = $request->crypto_usd_last;
                $official["crypto_view"] = $request->crypto_view;
                $official["crypto_view_last"] = $request->crypto_view_last;
                $official["crypto_view_run"] = $request->crypto_view_run;
                $official["crypto_like"] = $request->crypto_like;
                $official["crypto_like_last"] = $request->crypto_like_last;
                $official["crypto_like_run"] = $request->crypto_like_run;
                $official["crypto_sub"] = $request->crypto_sub;
                $official["crypto_sub_last"] = $request->crypto_sub_last;
                $official["crypto_sub_run"] = $request->crypto_sub_run;
                $official["crypto_cmt"] = $request->crypto_cmt;
                $official["crypto_cmt_last"] = $request->crypto_cmt_last;
                $official["crypto_cmt_run"] = $request->crypto_cmt_run;
                $official["crypto_group_comment"] = $request->crypto_group_comment;
                $official["crypto_cmt_link"] = $request->crypto_cmt_link;
                $official["crypto_cmt_content"] = $request->crypto_cmt_content;
//                $official["crypto_cmt_content_finish"] = $request->crypto_cmt_content_finish;
                $official["cmt_schedule"] = $request->cmt_schedule;
                $official["cmt_number"] = $request->cmt_number;

                $official["adsense_usd"] = $request->adsense_usd;
                $official["adsense_view"] = $request->adsense_view;
                $official["adsense_view_run"] = $request->adsense_view_run;
                $official["facebook_usd"] = $request->facebook_usd;
                $campaignStatistics->official = json_encode((object) $official);
                $campaignStatistics->bitly_url = $request->bitly_url;
                $campaignStatistics->guest_artist_1 = $request->guest_artist_1;
                $campaignStatistics->guest_artist_2 = $request->guest_artist_2;
                $campaignStatistics->guest_artist_3 = $request->guest_artist_3;

                $campaignStatistics->promo_keywords = $request->promo_keywords;
                $campaignStatistics->homepage_video_update = $request->homepage_video_update;
                if ($request->audio_url != $campaignStatistics->audio_url) {
                    $campaignStatistics->audio_url = $request->audio_url;
                    $campaignStatistics->audio_url_cut = null;
                }
                $campaignStatistics->lyrics = $request->lyrics;
                $campaignStatistics->deezer_artist_id = $request->deezer_artist_id;
//                if (isset($request->target)) {
//                    $campaignStatistics->target = strtoupper($request->target);
//                }
                if (isset($request->target_total)) {
                    $campaignStatistics->target = strtoupper($request->target_total);
                }
                if (isset($request->campaign_duration)) {
                    $campaignStatistics->duration = $request->campaign_duration;
                }
                if ($request->status != 3) {
                    $campaignStatistics->status = $request->status;
                    if ($request->status == 0) {
                        $campaignStatistics->log = $campaignStatistics->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name change status to $request->status";
                        //đóng notification
                        $tasksId = Tasks::where("type", 12)->where("campaign_id", $campaignStatistics->id)->pluck("id")->toArray();
                        $log = gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " stop campaign $campaignStatistics->id, sysntem change status=3";
                        Notification::whereIn("noti_id", $tasksId)->update(["status" => 3, "log" => DB::raw("CONCAT(log,'$log')")]);
                    }
                }
                if ($request->status == 0 || $request->status == 2) {
                    Campaign::where("campaign_id", $request->id)->update(["status_use" => 0]);
                } else {
                    Campaign::where("campaign_id", $request->id)->update(["status_use" => 1]);
                }
                if ($request->cam_type == 1 || $request->cam_type == 4) {
                    $campaignStatistics->seller = $request->seller;
                }
                $campaignStatistics->tier = $request->tier;
                if (Utils::containString($request->spotify_id, "spotify.com")) {
                    if (preg_match("/https:\/\/open.spotify.com\/track\/(\w+)/", $request->spotify_id, $re)) {
                        $spotifyId = $re[1];
                    }
                } else {
                    $spotifyId = trim($request->spotify_id);
                }
                if (isset($spotifyId)) {
                    $campaignStatistics->spotify_id = $spotifyId;
                }

                if ($campaignStatistics->release_info != null) {
                    $releaseInfo = (array) json_decode($campaignStatistics->release_info);
                } else {
                    $releaseInfo = [];
                }
                if (isset($request->official_video_version) && count($request->official_video_version) > 0) {
                    foreach ($releaseInfo["official_video_versions"] as $offV) {
                        foreach ($request->official_video_version as $id => $ovv) {
                            if ($offV->link == $ovv) {
                                $offV->views = $request->start_view_official_version[$id];
                                break;
                            }
                        }
                    }
                    $campaignStatistics->release_info = json_encode((object) $releaseInfo);
                }
                if ($request->press_release != null) {
                    $releaseInfo["press_release"] = $request->press_release;
                    $campaignStatistics->release_info = json_encode((object) $releaseInfo);
                }
                if ($request->smart_link != null) {
                    $releaseInfo["smart_link"] = $request->smart_link;
                    $campaignStatistics->release_info = json_encode((object) $releaseInfo);
                }

                //xử lí note checklists
                if ($request->cl_notes != null) {
                    $checklist = $campaignStatistics->check_lists;
                    if ($checklist != null) {
                        $checklist = json_decode($checklist);
                        foreach ($checklist as $cl) {
                            if ($cl->key == 'cl_notes') {

                                $cl->result = $request->cl_notes;
                            }
                        }
                    }
//                    Log::info(json_encode($checklist));
                    $campaignStatistics->check_lists = json_encode($checklist);
                }
                //2024/06/03 bỏ update checklist, chỉ cho update từ calendar
//                $keys = CampaignChecklists::where("status", 1)->get();
//                $checkLists = [];
//                foreach ($keys as $k) {
//                    $key = $k->key;
//                    if ($k->type == "checkbox") {
//                        if (isset($request->$key)) {
//                            $checkLists[$key] = 1;
//                        } else {
//                            $checkLists[$key] = 0;
//                        }
//                    } else {
//                        if ($request->$key == null) {
//                            $checkLists[$key] = "";
//                        } else {
//                            $checkLists[$key] = $request->$key;
//                        }
//                    }
//                }
//                $checkList = (object) $checkLists;
//                $campaignStatistics->check_lists = json_encode($checkList);
                $campaignStatistics->short_text = json_encode((object) [
                            "some_note" => $request->some_note,
                            "hashtags" => $request->hashtags,
                            "content_drive" => $request->content_drive,
                            "hook" => $request->hook,
                ]);
                $campaignStatistics->save();
                if ($lastCampaignName != $request->campaign_name) {
                    Campaign::where("campaign_id", $request->cam_id)->update(["campaign_name" => $request->campaign_name, "is_athena" => 0]);
                }
            }
        }

        return array("status" => "success", "content" => "Success", "option" => $option, "contentedit" => "Success");
    }

    public function addVideosForCampaign(Request $request) {
        $user = Auth::user();
        $array_videos = array();
        $curr = time();
        Log::info($user->user_name . '|CampaignController.addVideosForCampaign|request=' . json_encode($request->all()));
        //quet lai tat ca video nao quet info sit
        if ($request->list_video == null || $request->list_video == "") {
//                return array("status" => "danger", "content" => "You must enter list video");

            $dataCampaigns = Campaign::where("video_title", "")->get();
//                $dataCampaigns = Campaign::where("channel_id", "")->where("status", 1)->get();
//                Log::info(DB::getQueryLog());
            $total = count($dataCampaigns);
            error_log("rescan " . $total);
            foreach ($dataCampaigns as $cam) {
                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($cam->video_id);
                if ($videoInfo["status"] == 0) {
                    for ($t = 0; $t < 5; $t++) {
                        error_log("rescan Retry $cam->video_id");
                        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($cam->video_id);
                        error_log(json_encode($videoInfo));
                        if ($videoInfo["status"] == 1) {
                            break;
                        }
                    }
                }
                $i++;
                error_log("rescan $i/$total $cam->video_id " . json_encode($videoInfo));
                if ($videoInfo["status"] == 1) {
                    $cam->channel_id = $videoInfo["channelId"];
                    $cam->channel_name = $videoInfo["channelName"];
                    $cam->video_title = $videoInfo["title"];
                    $cam->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                    $cam->publish_date = $videoInfo["publish_date"];
                    $cam->status = $videoInfo["status"];
                    $cam->save();
                } else {
                    $cam->status = $videoInfo["status"];
                    $cam->save();
                }
            }
//                $total = count($dataCampaigns);
//                Log:info(DB::getQueryLog());
//                foreach ($dataCampaigns as $dataCampaign) {
//                $array_videos[] = "https://www.youtube.com/watch?v=$dataCampaign->video_id";
//            }
        } else {
            $temp = str_replace(array("\r\n", "\n", " ", ","), "@;@", $request->list_video);
            $array_videos = explode("@;@", $temp);
            $total = count($array_videos);
        }
        foreach ($array_videos as $video) {
            $video = str_replace(",", "", $video);
            $info = YoutubeHelper::processLink($video);
            if ($info["type"] == 3) {
                $video_id = $info["data"];
                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                if ($videoInfo["status"] == 0) {
                    for ($t = 0; $t < 15; $t++) {
                        error_log("Retry $video_id");
                        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                        error_log(json_encode($videoInfo));
                        if ($videoInfo["status"] == 1) {
                            break;
                        }
                    }
                }
                $check = Campaign::where("video_id", $video_id)->where("campaign_id", $campaignId)->first();
                $i++;
                error_log("addcampaign $i/$total $video_id " . $videoInfo["channelId"] . ' ' . $videoInfo["title"]);
                if (!$check) {
                    $campaign = new Campaign();
                    $campaign->username = $user->user_name;
                    $campaign->campaign_id = $campaignId;
                    $campaign->campaign_name = $request->campaign_name;
                    $campaign->channel_id = $videoInfo["channelId"];
                    $campaign->channel_name = $videoInfo["channelName"];
                    $campaign->video_type = $request->video_type;
                    $campaign->video_id = $video_id;
                    $campaign->video_title = $videoInfo["title"];
                    $campaign->views_detail = '[]';
                    $campaign->status = $videoInfo["status"];
                    $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                    $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                    $campaign->publish_date = $videoInfo["publish_date"];
                    $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                    $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                    $campaign->status_confirm = 3;
                    $campaign->save();
                } else {
                    $check->username = $user->user_name;
                    $check->channel_id = $videoInfo["channelId"];
                    $check->channel_name = $videoInfo["channelName"];
                    $check->video_title = $videoInfo["title"];
                    $check->status = $videoInfo["status"];
                    $check->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                    $check->publish_date = $videoInfo["publish_date"];
                    $last = explode(" ", $check->create_time);
                    $lastDate = explode("/", $last[0]);
                    $check->insert_date = $lastDate[2] . $lastDate[0] . $lastDate[1];
                    $check->insert_time = $last[1];
                    $check->save();
                    Campaign::where("video_id", $video_id)->update(["channel_id" => $videoInfo["channelId"],
                        "channel_name" => $videoInfo["channelName"], "video_title" => $videoInfo["title"],
                        "publish_date" => $videoInfo["publish_date"], "insert_date" => $check->insert_date,
                        "insert_time" => $check->insert_time]);
                }
            }
        }
    }

    public function getcampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getcampaign|request=' . json_encode($request->all()));
        $datas = Campaign::where("id", $request->id)->first(["id", "campaign_name", "video_id", "video_title", "views_detail"]);
        $chart = DB::select("select date,views_real_daily as daily,likes as daily_like, dislikes as daily_dislike from athena_promo_sync where video_id = '$datas->video_id' order by date");
        $datas->views_detail = json_encode($chart);
        return $datas;
    }

    public function getCampaignChart(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getCampaignChart|request=' . json_encode($request->all()));
        $type = 5;
        if (isset($request->video_type)) {
            $type = $request->video_type;
        }
        if ($type == 'all') {
            $whereTye = "";
        } else {
            $whereTye = "and video_type =$type";
        }
//        $range = $request->range_chart;
//        $date = 0;
//        if ($range != 0) {
//            $date = date("Ymd", strtotime("-$range day"));
//        }
        $startDate = '20220101';
        $endDate = date("Ymd", time());
        if (isset($request->start)) {
            $startDate = $request->start;
        }
        if (isset($request->end)) {
            $endDate = $request->end;
        }
        $ids = [0];
        if (isset($request->ids)) {
            $ids = $request->ids;
        }
        $campaigns = CampaignStatistics::whereIn("id", $ids)->get(["id", "campaign_name"]);

        if (count($ids) > 0) {
            foreach ($campaigns as $campaign) {
                $temps = DB::select("select date,sum(views) as views from athena_promo_sync
                                            where date >= '$startDate' and date <= '$endDate' and video_id in (select video_id from campaign where campaign_id = $campaign->id
                                            and status_confirm = 3 $whereTye) group by date order by date");
                $campaign->data = $temps;
            }
        }
        return array("campaigns" => $campaigns);
    }

    public function getcampaignstatistics(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getcampaignstatistics|request=' . json_encode($request->all()));
        $data = CampaignStatistics::where("id", $request->id)->first();

        // <editor-fold defaultstate="collapsed" desc="submission_info">
        if ($data->type == 5 && $data->submission_info == null && $data->channel_name != null) {
            $accountInfo = AccountInfo::where("chanel_name", $data->channel_name)->first();
            $info = [];
            if ($accountInfo) {
                $user = Utils::userCode2userName($accountInfo->user_name);
                $sub = (object) [
                            "user" => $user,
                            "channel_name" => $accountInfo->chanel_name,
                            "money" => $data->money,
                ];
                $info[] = $sub;
                $data->submission_info = json_encode($info);
                $data->save();
            }
        }
        // </editor-fold>

        $keys = CampaignChecklists::where("status", 1)->where("is_fulfillment", 1)->orderBy("location_order")->orderBy("job_order")->get();
        $locations = [];
//        $jobs = [];
        $tasks = CampaignTasks::where("campaign_id", $request->id)->where("del_status", 0)->pluck("id");
        $jobs = CampaignJobs::whereIn("task_id", $tasks)->where("del_status", 0)->whereNotNull("job_code")->get();
        foreach ($keys as $k) {
            if (!in_array($k->location, $locations)) {
                $locations[] = $k->location;
            }
        }
        // <editor-fold defaultstate="collapsed" desc="v1">
//        //checklist v1
//        if ($data->check_lists == null) {
//            $checkLists = [];
//            foreach ($keys as $key) {
//                if ($key->type == "checkbox") {
//                    $checkLists[$key->key] = 0;
//                } else {
//                    $checkLists[$key->key] = "";
//                }
//            }
//            $checkList = (object) $checkLists;
//            $data->check_lists = json_encode($checkList);
//            $data->save();
//        } else {
//            $checkList = json_decode($data->check_lists);
//        }
//
//        $html = "";
//        foreach ($locations as $location) {
//            $html .= "<div class='row'>
//                        <div class='col-sm-12'>
//                            <div class='card-box border-thin'>
//                                <h4 class='m-t-0 m-b-20 header-title'><b>" . strtoupper($location) . "</b></h4>
//                                    <div class='todoapp'>";
//            foreach ($keys as $k) {
//                if ($location == $k->location) {
//                    $checked = '';
//                    $val = '';
//                    $key = $k->key;
//                    $value = $k->name;
//                    $type = $k->type;
//                    $tooltipData = $k->tooltip;
//                    if (!empty($checkList->$key) && $checkList->$key == 1) {
//                        $checked = "checked";
//                    }
//                    if (!empty($checkList->$key) && $checkList->$key != "") {
//                        $val = $checkList->$key;
//                    }
//
//                    if ($type == "checkbox") {
//                        $html .= "<div class='col-md-6'>";
//                        $html .= "  <div class='checkbox checkbox-primary'>";
//                        $html .= "      <input id='$key' class='$key' type='checkbox' name='$key' $checked>";
//                        $html .= "          <label for='$key'>";
//                        $html .= "              $value";
//                        $html .= "          </label>";
//                        if ($tooltipData != "") {
//                            $html .= "<i class='m-l-5 fa fa-question-circle-o fulfillment-tooltip' data-html='true'
//                                        data-toggle='tooltip' data-placement='top'
//                                        title='$tooltipData'></i>";
//                        }
//                        $html .= "  </div>";
//                        $html .= "</div>";
//                    } else if ($type == "textbox") {
//                        $html .= "<div class='col-md-6'>";
//                        $html .= "    <div class='form-group row'>";
//                        $html .= "        <label class='col-12 col-form-label'>$value</label>";
//                        if ($tooltipData != "") {
//                            $html .= "<i class='m-l-5 fa fa-question-circle-o' data-html='true'
//                                        data-toggle='tooltip' data-placement='top'
//                                        title='$tooltipData'></i>";
//                        }
//                        $html .= "            <div class='col-12'>";
//                        $html .= "                <input id='$key' type='text' name='$key' class='form-control' value='$val'>";
//                        $html .= "            </div>";
//                        $html .= "   </div>";
//                        $html .= "</div>";
//                    } else if ($type == "textarea") {
//                        $html .= "<textarea id='$key' name='$key' class='form-control' rows='5' spellcheck='false' style='line-height: 1.25;height: 100px'>$val</textarea>";
//                    }
//                }
//            }
//
//            $html .= "</div></div></div></div>";
//        }
// </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="v2">
        //checklist v2
        $checkListColumn = "check_lists";
        $checkList = [];
        if ($data->$checkListColumn == null || $data->$checkListColumn == "[]") {

            $checkLists = $this->makeChecklistJson($keys);
            $data->$checkListColumn = json_encode($checkLists);
            $data->save();
            $checkList = $checkLists;
        } else {
            $checkList = json_decode($data->$checkListColumn);
        }

        $html2 = "";
        foreach ($locations as $location) {
            $html2 .= "<div class='row'>
                        <div class='col-sm-12'>
                            <div class='card-box border-thin'>
                                <h4 class='m-t-0 m-b-20 header-title'><b>" . strtoupper($location) . "</b></h4>
                                    <div class='todoapp'>";
            foreach ($keys as $k) {
                //tính toán phần job_detail
                if ($location == $k->location) {
                    $jobDetail = "";
                    foreach ($checkList as $clValue) {
                        if ($k->key == $clValue->key) {
                            if (count($clValue->job_detail) > 0) {
                                $jobDetails = $clValue->job_detail;
                                foreach ($jobDetails as $detail) {
//                                    if ($detail->type == "checkbox") {
//                                        $checked = "";
//                                        if ($detail->is_finish) {
//                                            $checked = "checked";
//                                        }
//                                        $jobDetail .= "<div class='d-flex align-items-center m-l-30'>
//                                        <div class='checkbox checkbox-primary d-flex align-items-center checkbox-rounded custom-after'>
//                                            <input class='checkbox-multi' type='checkbox' value='1' name='$detail->id' $checked disabled>
//                                            <label class='m-b-18 p-l-0'></label>
//                                        </div>
//                                        <p class='w-100 m-0 truncate'>$detail->name</p>
//                                    </div>";
//                                    } else
                                    $extraHtml = '';
                                    if (isset($detail->extra)) {
                                        foreach ($detail->extra as $extra) {

                                            $extraHtml .= "<input class='form-control form-control-sm m-t-5' value='$extra->result' type='text' name='$extra->id' disabled>";
                                        }
                                    }
                                    if ($detail->type == "textbox") {
                                        $jobDetail .= "<div class='form-group m-l-30'>
                                                <span class='font-13'>" . ucwords($detail->name) . "</span>
                                                <input class='form-control form-control-sm' value='$detail->result' type='text' name='$detail->id' disabled>
                                                    $extraHtml
                                            </div>";
                                    }
                                }
                            }


                            $checked = '';
                            $val = '';
                            $key = $clValue->key;
                            $value = $k->name;
                            $type = $k->type;
                            $tooltipData = $k->tooltip;

                            //kiểm tra xem checklist này đã đc tạo job nào trên calendar chưa
//                            if (empty($clValue->job_id)) {
//                                if (count($jobs) == 0) {
//                                    $tasks = CampaignTasks::where("campaign_id", $request->id)->where("del_status", 0)->pluck("id");
//                                    $jobs = CampaignJobs::whereIn("task_id", $tasks)->where("del_status", 0)->whereNotNull("job_code")->get();
//                                }
                            $jobId = null;
                            foreach ($jobs as $job) {
                                if ($job->job_code == $clValue->key) {
                                    $clValue->job_id = $job->id;
                                    $jobId = $job->id;
                                    break;
                                }
                            }
//                            }
//                            $jobId = !empty($clValue->job_id) ? $clValue->job_id : null;
                            $a = "";
                            if ($jobId != null) {
                                //đánh dấu là đã tạo job trên calendar
                                $clValue->is_job = 1;
                                $a = "<a target='_blank' href='/calendar?jid=$jobId'>$value</a>";
                                //có job đc tạo bên calendar thì mới được tính
                                if ($clValue->is_finish) {
                                    $checked = "checked";
                                }
                                if ($clValue->result != "") {
                                    $val = $clValue->result;
                                }
                            } else {
                                $clValue->is_job = 0;
                                $clValue->is_finish = 0;
//                                $clValue->result = "";
                                $a = $value;
                            }

                            if ($type == "checkbox") {
                                $html2 .= "<div class='col-md-6'>";
                                $html2 .= "  <div class='checkbox checkbox-primary'>";
                                $html2 .= "      <input id='$key' class='$key' type='checkbox' name='$key' $checked disabled>";
                                $html2 .= "          <label for='$key'>";
                                $html2 .= "              $a";
                                $html2 .= "          </label>";
                                if ($tooltipData != "") {
                                    $html2 .= "<i class='m-l-5 fa fa-question-circle-o fulfillment-tooltip' data-html='true'
                                        data-toggle='tooltip' data-placement='top'
                                        title='$tooltipData'></i>";
                                }
                                $html2 .= $jobDetail;
                                $html2 .= "  </div>";
                                $html2 .= "</div>";
                            } else if ($type == "textbox") {
                                $html2 .= "<div class='col-md-6'>";
                                $html2 .= "    <div class='form-group row'>";
                                $html2 .= "        <label class='col-12 col-form-label'>$value</label>";
                                if ($tooltipData != "") {
                                    $html2 .= "<i class='m-l-5 fa fa-question-circle-o' data-html='true'
                                        data-toggle='tooltip' data-placement='top'
                                        title='$tooltipData'></i>";
                                }
                                $html2 .= "            <div class='col-12'>";
                                $html2 .= "                <input id='$key' type='text' name='$key' class='form-control' value='$val'> disabled";
                                $html2 .= "            </div>";
                                $html2 .= "   </div>";
                                $html2 .= "</div>";
                            } else if ($type == "textarea") {
                                $html2 .= "<textarea id='$key' name='$key' class='form-control' rows='5' spellcheck='false' style='line-height: 1.25;height: 100px'>$clValue->result</textarea>";
                            }
                            break;
                        }
                    }
                }
            }

            $html2 .= "</div></div></div></div>";
        }
        $data->$checkListColumn = json_encode($checkList);
        $data->save();
// // </editor-fold>

        $data->check_list_html = $html2;
        $viralVideoHtml = "";
        $picPressHtml = "";
        if ($data->release_info != null) {
            //viral videos
            $re = json_decode($data->release_info);
            if (!empty($re->viral_video)) {
                $virals = json_decode($re->viral_video);
                if (count($virals) > 0) {
                    foreach ($virals as $viral) {
                        $viralVideoHtml .= "<div class='col-md-8'><span class='m-l-10'>$viral->url</span></div>
                                        <div class='col-md-4 text-right'><span class='m-l-10'>$viral->suggested_text</span></div>";
                    }
                }
            }
            //picpress
            $in = json_decode($data->release_info);
            if (!empty($in->pic_press)) {
                $picPress = json_decode($in->pic_press);
                if (count($picPress) > 0) {
                    foreach ($picPress as $pic) {
                        $picPressHtml .= "<div class='col-md-4 thumb-xl member-thumb m-b-10 center-block'>
                                        <h6 class='m-b-5'>Press Pics</h6>
                                        <a target='_blank' href='$pic'><img src='$pic' class='img-thumbnail' style='width: 150px'></a>
                                    </div>";
                    }
                }
            }
        }
        $data->viral_video_html = $viralVideoHtml;
        $data->pic_press_html = $picPressHtml;


        $startDate = strtotime($data->campaign_start_date);
        $data->start_date_edit = date("Y-m-d", $startDate);
        $numberComment = 0;
        $numberFinishedComment = 0;
        if ($data->official != null) {
            $official = json_decode($data->official);
            if (!empty($official->crypto_cmt_content)) {
                $temp = str_replace(array("\r\n", "\n"), "@;@", $official->crypto_cmt_content);
                $arrayCmt = explode("@;@", $temp);
                $numberComment = count($arrayCmt);
            }
            if (!empty($official->crypto_cmt_content_finish)) {
                $temp = str_replace(array("\r\n", "\n"), "@;@", $official->crypto_cmt_content_finish);
                $arrayCmt = explode("@;@", $temp);
                $numberFinishedComment = Utils::countInArray("[success]", $arrayCmt);
            }
        }
        $data->number_comment = $numberComment;
        $data->number_comment_finish = $numberFinishedComment;
        return $data;
    }

    public function scanCampaign(Request $request) {
        $listActiveCam = CampaignStatistics::where("status", 1)->whereIn("type", [1, 2, 4, 5])->get(["id"]);
        $arrayActiveCam = [];
        foreach ($listActiveCam as $ac) {
            $arrayActiveCam[] = $ac->id;
        }
        $numberThreadInit = 20;
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }
        if (isset($request->video_id)) {
            $datas = Campaign::whereIn("video_id", $request->video_id)->where("status_use", 1)->where("status", 1)->get();
        } else {
            $datas = Campaign::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status_use", 1)->whereIn("campaign_id", $arrayActiveCam)->where("status_confirm", 3)->get();
//            $datas = Campaign::where("status_use", 1)->where("status", 1)->whereIn("campaign_id", [316])->where("status_confirm", 3)->get();
        }
        if (isset($request->date)) {
            $date = $request->date;
        } else {
            $date = gmdate("m-d-Y", time() + 7 * 3600);
        }
        $i = 0;
        $total = count($datas);
        foreach ($datas as $data) {
            $info = YoutubeHelper::getVideoInfoHtmlDesktop($data->video_id);
            if ($info["status"] == 0) {
                for ($t = 0; $t < 15; $t++) {
                    error_log("Retry $data->video_id");
                    $info = YoutubeHelper::getVideoInfoHtmlDesktop($data->video_id);
                    if ($info["status"] == 1) {
                        break;
                    }
                }
            }
            if ($data->channel_id == null || $data->channel_id == "") {
                $data->channel_id = $info["channelId"];
            }
            if ($data->channel_name == null || $data->channel_name == "") {
                $data->channel_name = $info["channelName"];
            }
            if ($data->video_title == null || $data->video_title == "") {
                $data->video_title = $info["song_name"];
            }
            $inc_views = $info["view"] - $data->views;
            if ($inc_views < 0 || $inc_views == $info["view"]) {
                $inc_views = 0;
            }

            $inc_like = $info["like"] - $data->like;
            if ($inc_like < 0 || $inc_like == $info["like"]) {
                $inc_like = 0;
            }
            $inc_dislike = $info["dislike"] - $data->dislike;
            if ($inc_dislike < 0 || $inc_dislike == $info["dislike"]) {
                $inc_dislike = 0;
            }
            $inc_comment = $info["comment"] - $data->comment;
            if ($inc_comment < 0 || $inc_comment == $info["comment"]) {
                $inc_comment = 0;
            }
            $per_inc_views = $data->daily_views != 0 ? (($inc_views - $data->daily_views) / $data->daily_views * 100) : 0;
            $per_inc_like = $data->daily_like != 0 ? (($inc_like - $data->daily_like) / $data->daily_like * 100) : 0;
            $per_inc_dislike = $data->daily_dislike != 0 ? (($inc_dislike - $data->daily_dislike) / $data->daily_dislike * 100) : 0;
            $per_inc_comment = $data->daily_comment != 0 ? (($inc_comment - $data->daily_comment) / $data->daily_comment * 100) : 0;
            $views_detail = $data->views_detail != null ? json_decode($data->views_detail) : [];
            if (count($views_detail) > 90) {
                array_shift($views_detail);
            }
            $flag = 0;
            foreach ($views_detail as $detail) {
                if ($detail->date == $date) {
                    $flag = 1;
                    $detail->views = $info["view"];
                    $detail->like = $info["like"];
                    $detail->dislike = $info["dislike"];
                    $detail->comment = $info["comment"];
                    $detail->daily = $inc_views;
                    $detail->daily_like = $inc_like;
                    $detail->daily_dislike = $inc_dislike;
                    $detail->daily_comment = $inc_comment;
                }
            }
            if ($flag == 0) {
                $views_log = (object) [
                            'date' => $date,
                            'views' => $info["view"],
                            'like' => $info["like"],
                            'dislike' => $info["dislike"],
                            'comment' => $info["comment"],
                            'daily' => $inc_views,
                            'daily_like' => $inc_like,
                            'daily_dislike' => $inc_dislike,
                            'daily_comment' => $inc_comment,
                ];
                $views_detail[] = $views_log;
            }
            $data->last_daily_meta = json_encode((object) [
                        'views' => $data->daily_views,
                        'like' => $data->daily_like,
                        'dislike' => $data->daily_dislike,
                        'comment' => $data->daily_comment,
            ]);
            $data->views = $info["view"];
            $data->like = $info["like"];
            $data->dislike = $info["dislike"];
            $data->comment = $info["comment"];
            $data->daily_views = $inc_views;
            $data->daily_like = $inc_like;
            $data->daily_dislike = $inc_dislike;
            $data->daily_comment = $inc_comment;
            $data->per_daily_views = $per_inc_views;
            $data->per_daily_like = $per_inc_like;
            $data->per_daily_dislike = $per_inc_dislike;
            $data->per_daily_comment = $per_inc_comment;
            $data->views_detail = json_encode($views_detail);
            $data->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
            $data->status = $info["status"];
            $data->publish_date = $info["publish_date"];
            if ($info["title"] != "" && $info["title"] != null) {
                if ($info["title"] != $data->video_title) {
                    $data->video_title = $info["title"];
                }
            }
            $i++;
            $data->save();
            error_log("scanCampaign-$threadId $date $i/$total $data->video_id   " . $info["status"] . ' ' . $info["view"]);
        }
//        if (!isset($request->video_id)) {
//            $campaignAll = DB::select("select campaign_id,campaign_name,sum(views) as views,sum(daily_views) as daily,
//                                        SUM(IF(video_type = 1, views, 0)) as views_official,
//                                        SUM(IF(video_type = 1, daily_views, 0)) as daily_oficial,
//                                        SUM(IF(video_type = 2, views, 0)) as views_lyric,
//                                        SUM(IF(video_type = 2, daily_views, 0)) as daily_lyric,
//                                        SUM(IF(video_type = 3, views, 0)) as views_tiktok,
//                                        SUM(IF(video_type = 3, daily_views, 0)) as daily_tiktok,
//                                        SUM(IF(video_type = 4, views, 0)) as views_lyric_video,
//                                        SUM(IF(video_type = 4, daily_views, 0)) as daily_lyric_video,
//                                        SUM(IF(video_type = 5, views, 0)) as views_compi,
//                                        SUM(IF(video_type = 6, views, 0)) as views_short,
//                                        SUM(IF(video_type = 5, daily_views, 0)) as daily_compi 
//                                        from campaign group by campaign_id,campaign_name");
////            Log::info(json_encode($campaignAll));
//            foreach ($campaignAll as $data) {
//                $temp = CampaignStatistics::where("id", $data->campaign_id)->first();
//                if ($temp) {
//                    if ($temp->views_detail != null) {
//                        $views_detail = json_decode($temp->views_detail);
//                    } else {
//                        $views_detail = array();
//                    }
//                    if (count($views_detail) > 90) {
//                        array_shift($views_detail);
//                    }
//                    $flag = 0;
//                    foreach ($views_detail as $detail) {
//                        if ($detail->date == $date) {
//                            $flag = 1;
//                            $detail->views = intval($data->views);
//                            $detail->daily = intval($data->daily);
//                            $detail->oficial = intval($data->daily_oficial);
//                            $detail->lyric = intval($data->daily_lyric);
//                            $detail->tiktok = intval($data->daily_tiktok);
//                            $detail->lyric_video = intval($data->daily_lyric_video);
//                            $detail->compi = intval($data->daily_compi);
//                            $detail->short = intval($data->views_short);
//                        }
//                    }
//                    if ($flag == 0) {
//                        $views_log = (object) [
//                                    'date' => $date,
//                                    'views' => intval($data->views),
//                                    'daily' => intval($data->daily),
//                                    'oficial' => intval($data->daily_oficial),
//                                    'lyric' => intval($data->daily_lyric),
//                                    'tiktok' => intval($data->daily_tiktok),
//                                    'lyric_video' => intval($data->daily_lyric_video),
//                                    'compi' => intval($data->daily_compi),
//                                    'short' => intval($data->views_short),
//                        ];
//                        $views_detail[] = $views_log;
//                    }
//                    $temp->views_detail = json_encode($views_detail);
//                    $temp->views = intval($data->views);
//                    $temp->daily_views = intval($data->daily);
//                    $temp->views_official = intval($data->views_official);
//                    $temp->daily_views_official = intval($data->daily_oficial);
//                    $temp->views_lyric = intval($data->views_lyric);
//                    $temp->daily_views_lyric = intval($data->daily_lyric);
//                    $temp->views_tiktok = intval($data->views_tiktok);
//                    $temp->daily_views_tiktok = intval($data->daily_tiktok);
//                    $temp->views_lyric_video = intval($data->views_lyric_video);
//                    $temp->daily_lyric_video = intval($data->daily_lyric_video);
//                    $temp->views_compi = intval($data->views_compi);
//                    $temp->daily_compi = intval($data->daily_compi);
//                    $temp->views_short = intval($data->views_short);
//                    $temp->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
//                    $temp->save();
//                } else {
//                    $views_detail = array();
//                    $views_log = (object) [
//                                'date' => $date,
//                                'views' => intval($data->views),
//                                'daily' => 0,
//                                'oficial' => 0,
//                                'lyric' => 0,
//                                'tiktok' => 0,
//                                'lyric_video' => 0,
//                                'compi' => 0,
//                                'short' => 0,
//                    ];
//                    $views_detail[] = $views_log;
//                    if (count($views_detail) > 30) {
//                        array_shift($views_detail);
//                    }
//                    $campaignStatistics = new CampaignStatistics();
//                    $campaignStatistics->campaign_name = $data->campaign_name;
//                    $campaignStatistics->views = $data->views;
//                    $campaignStatistics->daily_views = $data->daily;
//                    $campaignStatistics->views_official = $data->views_official;
//                    $campaignStatistics->daily_views_official = $data->daily_oficial;
//                    $campaignStatistics->views_lyric = $data->views_lyric;
//                    $campaignStatistics->daily_views_lyric = $data->daily_lyric;
//                    $campaignStatistics->views_tiktok = $data->views_tiktok;
//                    $campaignStatistics->daily_views_tiktok = $data->daily_tiktok;
//                    $campaignStatistics->views_lyric_video = $data->views_lyric_video;
//                    $campaignStatistics->daily_lyric_video = $data->daily_lyric_video;
//                    $campaignStatistics->views_compi = $data->views_compi;
//                    $campaignStatistics->daily_compi = $data->daily_compi;
//                    $campaignStatistics->views_short = $data->views_short;
//                    $campaignStatistics->views_detail = json_encode($views_detail);
//                    $campaignStatistics->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
//                    $campaignStatistics->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
//                    $campaignStatistics->save();
//                }
//            }
//        }
    }

    public function caculateCampaign() {
        $count = 0;
//        $campaignAll = DB::select("select campaign_id,campaign_name,sum(views) as views,sum(daily_views) as daily,
//                                        SUM(IF(video_type = 1, views, 0)) as views_official,
//                                        SUM(IF(video_type = 1, daily_views, 0)) as daily_oficial,
//                                        SUM(IF(video_type = 2, views, 0)) as views_lyric,
//                                        SUM(IF(video_type = 2, daily_views, 0)) as daily_lyric,
//                                        SUM(IF(video_type = 3, views, 0)) as views_tiktok,
//                                        SUM(IF(video_type = 3, daily_views, 0)) as daily_tiktok,
//                                        SUM(IF(video_type = 4, views, 0)) as views_lyric_video,
//                                        SUM(IF(video_type = 4, daily_views, 0)) as daily_lyric_video,
//                                        SUM(IF(video_type = 5, views, 0)) as views_compi,
//                                        SUM(IF(video_type = 6, views, 0)) as views_short,
//                                        SUM(IF(video_type = 5, daily_views, 0)) as daily_compi 
//                                        from campaign group by campaign_id,campaign_name");
        $campaignAll = DB::select("select a.campaign_id,a.campaign_name,sum(a.views) as views,sum(a.daily_views) as daily,
                                        SUM(IF(a.video_type = 1, a.views, 0)) as views_official,
                                        SUM(IF(a.video_type = 1, a.like, 0)) as like_official,
                                        SUM(IF(a.video_type = 1, a.comment, 0)) as comment_official,
                                        SUM(IF(a.video_type = 1, a.subs, 0)) as sub_official,
                                        SUM(IF(a.video_type = 1, a.daily_views, 0)) as daily_oficial,
                                        SUM(IF(a.video_type = 2, a.views, 0)) as views_lyric,
                                        SUM(IF(a.video_type = 2, a.daily_views, 0)) as daily_lyric,
                                        SUM(IF(a.video_type = 3, a.views, 0)) as views_tiktok,
                                        SUM(IF(a.video_type = 3, a.daily_views, 0)) as daily_tiktok,
                                        SUM(IF(a.video_type = 4, a.views, 0)) as views_lyric_video,
                                        SUM(IF(a.video_type = 4, a.daily_views, 0)) as daily_lyric_video,
                                        SUM(IF(a.video_type = 5, a.views, 0)) as views_compi,
                                        SUM(IF(a.video_type = 6, a.views, 0)) as views_short,
                                        SUM(IF(a.video_type = 5, a.daily_views, 0)) as daily_compi 
                                        from campaign a,campaign_statistics b where a.campaign_id = b.id and b.status in(1,2,4) and a.status_confirm =3 group by campaign_id,campaign_name");
//            Log::info(json_encode($campaignAll));
        $curr = time();
        Log::info("caculateCampaign " . count($campaignAll));
        foreach ($campaignAll as $data) {
            $temp = CampaignStatistics::where("id", $data->campaign_id)->whereIn("status", [1, 2, 4])->first();
            if ($temp) {
                $views = $temp->views_compi + $temp->views_lyric + $temp->views_short;
                $start = strtotime($temp->campaign_start_date);
                $count++;
                $temp->views = $views;
                $temp->daily_views = intval($data->daily);
                $temp->views_official = intval($data->views_official);
                $temp->daily_views_official = intval($data->daily_oficial);
                $temp->views_lyric = intval($data->views_lyric);
                $temp->daily_views_lyric = intval($data->daily_lyric);
                $temp->views_tiktok = intval($data->views_tiktok);
                $temp->daily_views_tiktok = intval($data->daily_tiktok);
                $temp->views_lyric_video = intval($data->views_lyric_video);
                $temp->daily_lyric_video = intval($data->daily_lyric_video);
                $temp->views_compi = intval($data->views_compi);
                $temp->daily_compi = intval($data->daily_compi);
                $temp->views_short = intval($data->views_short);
                $temp->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
                //tính toán view theo tuần 1, tháng 1, tháng 2...
                $viewsDetail = json_decode($temp->views_detail != null ? $temp->views_detail : json_encode((object) []));
                $runningDay = round(($curr - $start) / 86400);
                //set 0 view thành 1 view để hiện processbar
                if ($views == 0) {
                    $views = 1;
                }

//                if ($runningDay >= 0 && $runningDay <= 7) {
//                    $k = 0;
//                } else if ($runningDay > 7 && $runningDay <= 14) {
//                    $k = 1;
//                } else if ($runningDay > 14 && $runningDay <= 21) {
//                    $k = 2;
//                } else if ($runningDay > 21 && $runningDay <= 28) {
//                    $k = 3;
//                } else if ($runningDay > 28) {
//                    $k = 4;
//                }
                $k = 0;
                $viewsDetail->$k = $views;
                $temp->views_detail = json_encode($viewsDetail);
                if ($temp->official_data == null) {
                    $officialData = (object) [
                                "view" => intval($data->views_official),
                                "like" => intval($data->like_official),
                                "comment" => intval($data->comment_official),
                                "sub" => intval($data->sub_official),
                    ];
                } else {
                    $officialData = json_decode($temp->official_data);
                    $officialData->view = intval($data->views_official);
                    $officialData->like = intval($data->like_official);
                    $officialData->comment = intval($data->comment_official);
                    $officialData->sub = intval($data->sub_official);
                }
                $temp->official_data = json_encode($officialData);
                $temp->save();
            }
        }
//        return $count;
    }

    public function campaignstatus(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.campaignstatus|request=' . json_encode($request->all()));
        $campaign = CampaignStatistics::find($request->id);
        if ($campaign) {
            $status = $campaign->status;
            if ($status == 2) {
                $status = 1;
            } else if ($status == 1) {
                $status = 0;
            } else if ($status == 0) {
                $status = 2;
            }
            $campaign->status = $status;
            $log = $campaign->log;
            $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name change status to $status";
            $campaign->log = $log;
            $campaign->save();
            //update nhung video thuoc campain da hoàn thành dể hệ thống không scan nữa
            Campaign::where("campaign_id", $request->id)->update(["status_use" => $status]);

            return array("status" => "success", "content" => "success", "newStatus" => $status);
        }
        return array("status" => "danger", "content" => "Not found campaign");
    }

    public function downloadListvideo(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.downloadListvideo|request=' . json_encode($request->all()));
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        try {
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=campaign_' . $request->id . '_' . date('Ymd') . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];

            $lists = Campaign::where('campaign_id', $request->id)->where("status_confirm", 3)
                    ->where("video_type", "<>", 1)->orderBy("views", "desc")
                    ->get(['channel_name', 'video_id', 'views', 'video_type', 'publish_date']);
            if ($request->month != "-1") {
                $videoIds = $lists->pluck("video_id");
                Log::info(implode("','", $videoIds->toArray()));

                $views = DB::select("SELECT video_id, sum(views_real_daily) as views
                                    FROM `athena_promo_sync`
                                    WHERE `video_id` in ('" . implode("','", $videoIds->toArray()) . "')
                                    and date like '%$request->month%'
                                    group by video_id");
                Log::info(json_encode($views));
            }
            foreach ($lists as $list) {
                if ($request->month != "-1") {
                    $list->views = 0;
                    foreach ($views as $data) {
                        if ($data->video_id == $list->video_id) {
                            $list->views = $data->views;
                            break;
                        }
                    }
                }
                $list->channel_name = $list->channel_name;
                $list->video_id = "https://www.youtube.com/watch?v=" . $list->video_id;
                $list->publish_date = gmdate("Y/m/d", $list->publish_date);
                $videoType = "";
                if ($list->video_type == 5) {
                    $videoType = "Mix";
                } else if ($list->video_type == 2) {
                    $videoType = "Lyric";
                } else if ($list->video_type == 6) {
                    $videoType = "Short";
                } else if ($list->video_type == 1) {
                    $videoType = "Official";
                }
                $list->video_type = $videoType;
            }
            $lists = $lists->toArray();
            $title = array('Channel', 'VideoID', 'Views', 'Type', 'Published');
//            array_unshift($list, array_keys($title));
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

    public function getCheckList(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getCheckList|request=' . json_encode($request->all()));
        $datas = CampaignStatistics::find($request->id);
        $checkList = json_decode($datas->check_lists);
        Log::info($datas->check_lists);
        $arrKey = ["lv", "fc", "al", "dl", "ic", "lp", "ec", "qt", "es", "sa"];
        $arrayName = ["Lyric videos",
            "Featured Content",
            "Add lyric videos to channel's playlist",
            "Description linking to original videos in 10 Videos",
            "Info Card (as described in the Campaign Push Paper)",
            "Lyric video gets spot in Legacy Campaign Playlist",
            "Engaging comment under lyric video",
            "Queue Top 1 or 2 Spots",
            "End Screens (as described in the Campaign Push Paper)",
            "Spot in Autoplaylist"];
        $html = "";
        for ($i = 0; $i < count($arrKey); $i++) {
            $checked = '';
            $key = $arrKey[$i];
            $value = $arrayName[$i];
            if ($checkList->$key == 1) {
                $checked = "checked";
            }
            $html .= "<div class='col-md-12'>";
            $html .= "  <div class='checkbox checkbox-primary'>";
            $html .= "      <input id='$key' class='$key' type='checkbox' name='$key' $checked>";
            $html .= "          <label for='$key'>";
            $html .= "              $value";
            $html .= "          </label>";
            $html .= "  </div>";
            $html .= "</div>";
        }
        return $html;
    }

    public function addCheckList(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.addCheckList|request=' . json_encode($request->all()));
        $listcheck = '';

        $arrKeys = ["lv" => 0, "fc" => 0, "al" => 0, "dl" => 0, "ic" => 0, "lp" => 0, "ec" => 0, "qt" => 0, "es" => 0, "sa" => 0];
        $arrKey = ["lv", "fc", "al", "dl", "ic", "lp", "ec", "qt", "es", "sa"];
        foreach ($arrKeys as $key => $data) {
            $value = 0;
            if (isset($request->$key)) {
                $value = 1;
            }
            $listcheck .= "\"$key\":$value,";
        }
        $listcheck = rtrim($listcheck, ",");
        $listcheck = "{" . $listcheck . "}";
        $check = CampaignStatistics::where("id", $request->campaign_id)->first();
        $check->check_lists = $listcheck;
        $check->save();

        return array("status" => "success", "content" => "Success");
    }

    public function downloadVideoInfo(Request $request) {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=video_' . date('Ymd') . '.csv',
            'Expires' => '0',
            'Pragma' => 'public'
        ];
        $cam = Campaign::where("id", $request->id)->first();
        $datas = json_decode($cam->views_detail);
        $csv = "Video,Title\n";
        $csv .= "https://www.youtube.com/watch?v=$cam->video_id,$cam->video_title\n";
        $csv .= ",,Date,Daily views,Daily like\n";
        foreach ($datas as $data) {
            $csv .= ",,$data->date,$data->daily,$data->daily_like\n";
        }
        $file = storage_path("campaign/$cam->video_id.csv");
        $csv_handler = fopen($file, 'w');
        fwrite($csv_handler, $csv);
        fclose($csv_handler);
        return response()->download($file, "$cam->video_id.csv", $headers);
    }

    public function statisticsCardEnscreen() {
        $listCampaign = CampaignStatistics::where("type", 1)->where("status", 1)->get();
        error_log("statisticsCardEnscreen " . count($listCampaign));
        foreach ($listCampaign as $data) {
            $dataCards = DB::select("select $data->id as campaign_id,sum(card_teaser_clicks) as card_teaser_clicks,sum(per_card_teaser_clicks) as per_card_teaser_clicks,sum(card_clicks) as card_clicks,sum(per_card_clicks) as per_card_clicks
            from campaign_card where video_id in (select video_id from campaign where campaign_id =$data->id ) and report_type =1 ");
            $dataEndscreens = DB::select("select $data->id as campaign_id,sum(es_shown) as es_shown,sum(es_clicks) as es_clicks,sum(per_es) as per_es 
            from campaign_endscreen where video_id in (select video_id from campaign where campaign_id =$data->id ) and report_type =1 ");
            foreach ($dataCards as $card) {
                $check = CampaignStaCardEnd::where("campaign_id", $card->campaign_id)->first();
                if (!$check) {
                    $check = new CampaignStaCardEnd();
                    $check->campaign_id = $card->campaign_id;
                    $check->last_update_time = gmdate("d-m-Y H:i:s", time() + 7 * 3600);
                }
                $check->card_teaser_clicks = $card->card_teaser_clicks;
                $check->per_card_teaser_clicks = $card->per_card_teaser_clicks;
                $check->card_clicks = $card->card_clicks;
                $check->per_card_clicks = $card->per_card_clicks;
                $check->last_update_time = gmdate("d-m-Y H:i:s", time() + 7 * 3600);
                $check->save();
            }
            foreach ($dataEndscreens as $card) {
                $check = CampaignStaCardEnd::where("campaign_id", $card->campaign_id)->first();
                if (!$check) {
                    $check = new CampaignStaCardEnd();
                    $check->campaign_id = $card->campaign_id;
                    $check->last_update_time = gmdate("d-m-Y H:i:s", time() + 7 * 3600);
                }
                $check->es_shown = $card->es_shown;
                $check->es_clicks = $card->es_clicks;
                $check->per_es = $card->per_es;
                $check->last_update_time = gmdate("d-m-Y H:i:s", time() + 7 * 3600);
                $check->save();
            }
        }
    }

    //lấy danh sách user để assign make lyric
    public function lyricGetListUser(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.lyricGetListUser|request=' . json_encode($request->all()));
        $camId = $request->id;
        $tasks = Tasks::where("type", 5)->where("campaign_id", $camId)->where("task_status", 0)->get();
        $users = User::where("role", "like", "%16%")->where("status", 1)->get();
        $html = "<input type='hidden' name='lyric_cam_id' value='$camId'>";
        foreach ($users as $user) {
            $checked = "";
            foreach ($tasks as $task) {
                if ($user->user_name == $task->username) {
                    $checked = "checked";
                    break;
                }
            }
            $html .= " <div class='col-md-12'>";
            $html .= "  <div class='radio radio-success'>";
            $html .= "      <input id='$user->user_name' class='$user->user_name' type='radio' name='user_name' value='$user->user_name' $checked>";
            $html .= "          <label for='$user->user_name'>";
            $html .= "              $user->user_name";
            $html .= "          </label>";
            $html .= "  </div>";
            $html .= "</div>";
        }
        //2024/03/08 sang confirm thêm mục ko assign lyric
        $html .= " <div class='col-md-12'>";
        $html .= "  <div class='radio radio-success'>";
        $html .= "      <input id='not_assign' class='not_assign' type='radio' name='user_name' value='not_assign' >";
        $html .= "          <label for='not_assign'>";
        $html .= "              not_assign";
        $html .= "          </label>";
        $html .= "  </div>";
        $html .= "</div>";
        return $html;
    }

    //tạo task make lyric cho user
    public function lyricAssign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.lyricAssign|request=' . json_encode($request->all()));
        if (!isset($request->user_name)) {
            return array("status" => "error", "content" => "Please choose a manager");
        }
        $curentDate = gmdate("Y-m-d", time() + (7 * 3600));
        $curentTime = gmdate("H:i:s", time() + (7 * 3600));
        if ($request->user_name == "not_assign") {
            $data = CampaignStatistics::where("id", $request->lyric_cam_id)->first();
            if ($data) {
                $data->lyric_timestamp_id = "1";
                $data->save();
                return array("status" => "success", "content" => "Success");
            }
            return array("status" => "error", "content" => "Not found $request->lyric_cam_id");
        }
        $check = Tasks::where("username", $request->user_name)->where("campaign_id", $request->lyric_cam_id)->where("type", 5)->first();
        if (!$check) {
            $tasks = new Tasks();
            $tasks->username = $request->user_name;
            $tasks->type = 5;
            $tasks->date = $curentDate;
            $tasks->time = $curentTime;
            $tasks->create_time = time();
            $tasks->campaign_id = $request->lyric_cam_id;
            $tasks->task_status = 0;
            $tasks->content = "Make Lyric Timestamp";
            $tasks->save();
            return array("status" => "success", "content" => "Success");
        }
        return array("status" => "error", "content" => "The task ($request->lyric_cam_id) for $request->user_name already exists");
    }

    //update trạng thái sau khi làm xong lyric 
    public function finishLyricTask(Request $request) {
        Log::info('|CampaignController.finishLyricTask|request=' . json_encode($request->all()));
        if ($request->camId == "" || $request->lyricId == "") {
            return 0;
        }
        if (isset($request->type) && $request->type == 'noclaim') {
            $noclaim = Bom::where("id", $request->camId)->first();
            $curr = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            if ($noclaim) {
                $noclaim->lyric = 1;
                $noclaim->local_id = $request->lyricId;
                $log = $noclaim->log;
                $log .= PHP_EOL . "$curr finished make lyric";
                $noclaim->log = $log;
                $noclaim->save();
            }
            return 1;
        }
        $result = CampaignStatistics::where("id", $request->camId)->update(["lyric_timestamp_id" => $request->lyricId]);
        $task = Tasks::where("campaign_id", $request->camId)->where("type", 5)->first();
        if ($task) {
            $task->task_status = 3;
            $task->update_time = gmdate("Y-m-d H:i:s", time() + (7 * 3600));
            $task->save();
            $notify = Notification::where("noti_id", $task->id)->first();
            if ($notify) {
                $notify->status = 3;
                $notify->action_time = time();
                $notify->log = $notify->log . Utils::timeToStringGmT7(time()) . " finished make lyric campaign $request->camId,system change status=3" . PHP_EOL;
                $notify->save();
            }
        }
        return $result;
    }

    //lấy danh sách manual channel dể giao việc promos
    public function getListChannelManualPromos(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.getListChannelManualPromos|request=' . json_encode($request->all()));
//        $camp = CampaignStatistics::find($request->id);
////        if ($camp->genre == "POP") {
////            $channels = AccountInfo::where("is_music_channel", 1)->whereIn("channel_genre", ["POP", "COUNTRY"])->where("is_promos_lyric", 1)->get(["id", "user_name", "chanel_id", "chanel_name", "channel_genre"]);
////        } else if ($camp->genre == "RAP") {
////            $channels = AccountInfo::where("is_music_channel", 1)->whereIn("channel_genre", ["POP","RAP"])->where("is_promos_lyric", 1)->get(["id", "user_name", "chanel_id", "chanel_name", "channel_genre"]);
////        } else {
////            $channels = AccountInfo::where("is_music_channel", 1)->where("channel_genre", $camp->genre)->where("is_promos_lyric", 1)->get(["id", "user_name", "chanel_id", "chanel_name", "channel_genre"]);
////        }
//        $genre = $camp->genre;
//        if (isset($request->genre)) {
//            $genre = $request->genre;
//        }
//        $channels = AccountInfo::where("is_music_channel", 1)->where("channel_genre", $genre)->where("is_promos_lyric", 1)->get(["id", "user_name", "chanel_id", "chanel_name", "channel_genre", "channel_subgenre"]);
//        $tasks = Tasks::where("type", 6)->where("campaign_id", $request->id)->where("task_status", 0)->get();
//        foreach ($channels as $channel) {
//            $pos = strripos($channel->user_name, '_');
//            $username = substr($channel->user_name, 0, $pos);
//            $channel->user = $username;
//            $checked = "";
//            foreach ($tasks as $task) {
//                if ($channel->chanel_id == $task->channel_id) {
//                    $checked = "checked";
//                    break;
//                }
//            }
//            $channel->checked = $checked;
//        }
//        $listPromos = CampaignStatistics::where("status", 1)->where("genre", $camp->genre)->get(["id", "campaign_name", "genre"]);
//        
        //2023/12/19 chuyển sáng assign thẳng cho user, ko cần chọn kênh nữa
        $listUser = User::where("role", "like", "%16%")->where("status", 1)->orderBy("user_name")->get(["id", "user_name"]);
        $tasks = Tasks::where("type", '<>', 5)->where("campaign_id", $request->id)->get();

        foreach ($listUser as $us) {
            $us->lyric_video = 0;
            $us->promo_mix = 0;
            $us->start_campaign = "<span class='badge badge-danger'>Not yet started</span>";
            foreach ($tasks as $task) {
                if ($us->user_name == $task->username) {
                    if ($task->type == 6) {
                        $us->lyric_video = $us->lyric_video + 1;
                    }
                    if ($task->type == 9) {
                        $us->promo_mix = $us->promo_mix + 1;
                    }
                    if ($task->type == 12) {
                        $us->start_campaign = "<span class='badge badge-success'>Started</span>";
                    }
                }
            }
        }
        return array("users" => $listUser);
    }

    //tạo task make video promos cho manager
    public function channelAssign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.channelAssign|request=' . json_encode($request->all()));
        $users = $request->chkChannelAll;
        if (empty($users)) {
            return array("status" => "error", "content" => "Please choose some user");
        }
        $camIds = $request->channel_cam_id;
        if ($request->type == 12) {
            $name = "Start campaign";
        } elseif ($request->type == 9) {
            $name = "Promos mix";
        } elseif ($request->type == 6) {
            $name = "Lyric video";
        }

        $curentDate = gmdate("Y-m-d", time() + (7 * 3600));
        $curentTime = gmdate("H:i:s", time() + (7 * 3600));
        $count = 0;
        foreach ($users as $u) {
//          chỉ check trung vs type = 12
            if ($request->type == 12) {
                $check = Tasks::where("username", $u)->where("campaign_id", $camIds)->where("type", $request->type)->where("task_status", 0)->first();
            } else {
                $check = false;
            }
            if (!$check) {
                $count++;
                $tasks = new Tasks();
                $tasks->username = $u;
                $tasks->type = $request->type;
                $tasks->date = $curentDate;
                $tasks->time = $curentTime;
                $tasks->create_time = time();
                $tasks->campaign_id = $camIds;
                $tasks->task_status = 0;
                $tasks->content = $name;
                $tasks->save();
            }
        }
        return array("status" => "success", "content" => "Success $count");
    }

    //hàm chuyển video autopromos từ cross_post vào campaign
    public function crossPostToPromos() {
        
    }

    //hàm tạo job promos cho manager
    public function makeJobPromos() {
        $datas = CampaignStatistics::where("type", 1)->where("status", 1)->whereNotNull("lyric_timestamp_id")->get();
        $time = time();
        $curentDate = gmdate("Y-m-d", $time + (7 * 3600));
        $curentTime = gmdate("H:i:s", $time + (7 * 3600));
        $listTaskType = [7, 8];

        $currDay = gmdate("D", $time + (7 * 3600));

        foreach ($datas as $data) {
            $startTime = Utils::stringToTimeGmT7($data->campaign_start_date);
            $days = floor(($time - $startTime) / 86400);
            Log::info("system makeJobPromos $data->id $data->campaign_start_date $startTime " . $days);
            //chỉ lấy những kênh mà đc assign làm bài lyric
            $tasksCheck = Tasks::where("type", 6)->where("campaign_id", $data->id)->get();
////            $channels = AccountInfo::where("is_music_channel", 1)->where("channel_genre", $data->genre)->where("is_promos_246", 1)->get();
//            //video 3 claim,mix artists sau 3 ngày
//            if ($days == 6) {
//                foreach ($tasksCheck as $task) {
//                    foreach ($listTaskType as $taskType) {
//                        $check = Tasks::where("campaign_id", $data->id)->where("channel_id", $task->channel_id)->where("type", $taskType)->where("date", $curentDate)->first();
//                        if (!$check) {
//                            $tasks = new Tasks();
//                            $tasks->username = $task->username;
//                            $tasks->channel_id = $task->channel_id;
//                            $tasks->channel_name = $task->channel_name;
//                            $tasks->type = $taskType;
//                            $tasks->date = $curentDate;
//                            $tasks->time = $curentTime;
//                            $tasks->create_time = $time;
//                            $tasks->campaign_id = $data->id;
//                            $tasks->task_status = 0;
//                            if ($taskType == 7) {
//                                $tasks->content = "3 Claims video";
//                            } elseif ($taskType == 8) {
//                                $tasks->content = "Artists mix video";
//                            }
//                            $tasks->save();
//                        }
//                    }
//                }
//            }
            //cứ 7 ngày lại thông báo cho james vào assign promo mix
            for ($i = 1; $i < 100; $i++) {
                if (($days * $i) % 7 == 0) {
                    $check = Tasks::where("username", "jamesmusic")->where("type", 11)->where("date", $curentDate)->first();
                    if (!$check) {
                        $tasks = new Tasks();
                        $tasks->username = "jamesmusic";
                        $tasks->channel_id = null;
                        $tasks->channel_name = null;
                        $tasks->type = 11;
                        $tasks->date = $curentDate;
                        $tasks->time = $curentTime;
                        $tasks->create_time = $time;
                        $tasks->campaign_id = $data->id;
                        $tasks->task_status = 0;
                        $tasks->content = "assign promos mix";
                        $tasks->save();
                    }
                    break;
                }
            }


//            //2,4,6 promos mix
//            $dates = ["Mon", "Wed", "Fri"];
//            foreach ($dates as $date) {
//                if ($date == $currDay) {
//                    foreach ($channels as $channel) {
//                        $check = Tasks::where("campaign_id", $data->id)->where("channel_id", $channel->channel_id)->where("type", 9)->where("date", $curentDate)->first();
//                        if (!$check) {
//                            $pos = strripos($channel->user_name, '_');
//                            $username = substr($channel->user_name, 0, $pos);
//                            $tasks = new Tasks();
//                            $tasks->username = $username;
//                            $tasks->channel_id = $channel->chanel_id;
//                            $tasks->channel_name = $channel->chanel_name;
//                            $tasks->type = 9;
//                            $tasks->date = $curentDate;
//                            $tasks->time = $curentTime;
//                            $tasks->create_time = $time;
//                            $tasks->campaign_id = $data->id;
//                            $tasks->task_status = 0;
//                            $tasks->save();
//                        }
//                    }
//                }
//            }
            //tao task update homepage
//            if ($data->homepage_video_update == 1) {
//                $channels = AccountInfo::where("is_music_channel", 1)->where("channel_genre", $data->genre)->where("is_promos_lyric", 1)->get();
//                foreach ($channels as $channel) {
//                    $check = Tasks::where("campaign_id", $data->id)->where("channel_id", $channel->chanel_id)->where("type", 10)->first();
//                    if (!$check) {
//                        $pos = strripos($channel->user_name, '_');
//                        $username = substr($channel->user_name, 0, $pos);
//                        $tasks = new Tasks();
//                        $tasks->username = $username;
//                        $tasks->channel_id = $channel->chanel_id;
//                        $tasks->channel_name = $channel->chanel_name;
//                        $tasks->type = 10;
//                        $tasks->date = $curentDate;
//                        $tasks->time = $curentTime;
//                        $tasks->create_time = $time;
//                        $tasks->campaign_id = $data->id;
//                        $tasks->task_status = 0;
//                        $tasks->content = "Update homepage videos";
//                        $tasks->save();
//                    }
//                }
//            }
        }
    }

    //update positon wakeup cho campaign
    public function updatePositionWakeCampaign(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.updatePositionWakeCampaign|request=' . json_encode($request->all()));
        if (in_array("20", explode(",", $user->role)) || in_array("1", explode(",", $user->role))) {
            $genres = DB::select("select distinct genre from campaign_statistics where status =1 and type =1");
            $listId = implode(",", $request->chk_position);
            $campaigns = CampaignStatistics::whereIn("id", $request->chk_position)->orderByRaw("FIELD(id, $listId)")->get();
            foreach ($genres as $genre) {
                $position = 2;
                foreach ($campaigns as $campaign) {
                    Log::info("$genre->genre => $campaign->id");
                    if ($campaign->genre == $genre->genre) {
                        $campaign->wake_position = $position;
                        $position = $position + 2;
                        $campaign->save();
                    }
                }
            }
            return array("status", "success");
        }
        return array("status", "error");
    }

    //tạo lệnh scan video theo artists
    public function makeCommandScanVideo(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.makeCommandScanVideo|request=' . json_encode($request->all()));
        $process = LockProcess::where("type", 2)->whereIn("status", [0, 1])->get();
        if (count($process) > 0) {
            return array("status" => "error", "message" => "Process exists, wait for done");
        }
        if (!isset($request->artists) && !isset($request->song_name) && !isset($request->channel)) {
            return array("status" => "error", "message" => "Artists or Song Name or Channel can not be empty");
        }

        $artists = trim($request->artists);
        $songName = trim($request->song_name);
        $channelId = trim($request->channel);
        $videos = VideoDaily::whereRaw("1=1");
        if ($artists != null && $artists != "") {
            $videos->where("artists", "like", "%$artists%")->orWhere("video_title", "like", "%$artists%");
        }
        if ($songName != null && $songName != "") {
            $videos->orWhere("songs", "like", "%$songName%")->orWhere("video_title", "like", "%$artists%");
        }
        if (isset($request->channel) && $request->channel != null) {
            $videos->where("channel_id", "$channelId");
        }
        $videos = $videos->get();
        if (count($videos) == 0) {
            return array("status" => "error", "message" => "Not found videos");
        }
        for ($i = 0; $i < 5; $i++) {
            $insert = new LockProcess();
            $insert->type = 2;
            $insert->process = "scan-view-artists-$i";
            $insert->status = 0;
            $insert->time = time();
            $insert->condition = json_encode((object) [
                        "artists" => $artists,
                        "song_name" => $songName,
                        "channel_id" => $channelId
            ]);
            $insert->save();
        }
        return array("status" => "success", "message" => "Success");
    }

    //lấy thông tin tiến độ scan artists
    public function getProgressScan(Request $request) {
//        $user = Auth::user();
//        Log::info($user->user_name . '|CampaignController.getProgressScan|request=' . json_encode($request->all()));
        $per = 0;
        $data = DB::select("select ceil(sum(current) / sum(total) * 100) as percent,count(id) as thread from  lockprocess where type =2 and status =1");
        if ($data[0]->percent == null) {
            $per = 0;
        } else {
            $per = $data[0]->percent;
        }
        return array("thread" => $data[0]->thread, "percent" => $per);
    }

    //delete process scan artists
    public function deleteProgressScan(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.deleteProgressScan|request=' . json_encode($request->all()));
        $datas = LockProcess::where("type", 2)->get();
        $arrayPid = [];
        foreach ($datas as $data) {
            $arrayPid[] = $data->pid;
        }
        $pids = implode(" ", $arrayPid);
        Log::info("kill -9 $pids");
        shell_exec("kill -9 $pids");
        $result = LockProcess::where("type", 2)->delete();
        return array("status" => "success", "message" => "success $result");
    }

    //xuất file scan artist
    public function exportScanArtist(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.exportScanArtist|request=' . json_encode($request->all()));
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30);
        try {
            $artists = $request->scan_artists;
            $songName = $request->scan_songname;
            $artistsFileName = str_replace(" ", "_", $artists);
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=' . $artistsFileName . '_' . date('Ymd') . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];


            $videos = VideoDaily::whereRaw("1=1");
            if ($artists != null && $artists != "") {
                $videos->where("artists", "like", "%$artists%")->orWhere("video_title", "like", "%$artists%");
            }
            if ($songName != null && $songName != "") {
                $videos->orWhere("songs", "like", "%$songName%")->orWhere("video_title", "like", "%$artists%");
            }
            $sums = $videos->sum("views");
            $videos = $videos->orderBy("views", "desc")->get(["video_id", "video_title", "views"]);

            $lists = $videos->toArray();
            $title = array('VideoID', 'Video Title', 'Views', "Total Views: $sums");
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

    //xuất file report vi tri dia ly
    public function exportReportGeo(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.exportReportGeo|request=' . json_encode($request->all()));
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 500000);
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $temp = str_replace(array("\r\n", "\n", " ", ","), "@;@", $request->list_video);
            $arrayVideos = explode("@;@", $temp);
            $condition = "and video_id in ('" . implode("','", $arrayVideos) . "')";
            $query = "SELECT video_id,country_code,sum(views) as views FROM channel_basic_a2 where date >= '$startDate' and date <= '$endDate' $condition group by video_id,country_code order by views desc";
            $query2 = "SELECT video_id,gender,age_group,count(*) as total,sum(views_percentage) as views_percentage FROM channel_demographics_a1 where date >= '$startDate' and date <= '$endDate' $condition group by  video_id,gender,age_group";
            Log::info("exportReportGeo $query");
            Log::info("exportReportGeo $query2");
            $datas = YoutubeHelper::queryAthena("victor_channel", $query);
            $datas2 = YoutubeHelper::queryAthena("victor_channel", $query2);
            $listGender = ["MALE", "FEMALE"];
            $listAge = ["AGE_18_24", "AGE_25_34"];
            $listsByCountry = [];
            $listsByGender = [];
            $listsByAge = [];
            $listTotal = [];
//            $datas = json_decode('[[{"VarCharValue":"video_id"},{"VarCharValue":"country_code"},{"VarCharValue":"views"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PH"},{"VarCharValue":"80365"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PH"},{"VarCharValue":"60902"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ID"},{"VarCharValue":"49077"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"US"},{"VarCharValue":"39684"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"US"},{"VarCharValue":"35266"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MY"},{"VarCharValue":"34950"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IN"},{"VarCharValue":"30686"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PH"},{"VarCharValue":"30276"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"DE"},{"VarCharValue":"24354"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ID"},{"VarCharValue":"21741"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IN"},{"VarCharValue":"21332"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"PH"},{"VarCharValue":"20341"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GB"},{"VarCharValue":"19750"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"ID"},{"VarCharValue":"18674"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PH"},{"VarCharValue":"17021"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BD"},{"VarCharValue":"14978"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GB"},{"VarCharValue":"14940"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"US"},{"VarCharValue":"14281"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PH"},{"VarCharValue":"13751"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PK"},{"VarCharValue":"12655"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"US"},{"VarCharValue":"11226"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MY"},{"VarCharValue":"10987"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TH"},{"VarCharValue":"10130"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PH"},{"VarCharValue":"9706"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CA"},{"VarCharValue":"9678"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"DE"},{"VarCharValue":"9667"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"ID"},{"VarCharValue":"9576"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"VN"},{"VarCharValue":"9525"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BD"},{"VarCharValue":"9159"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FR"},{"VarCharValue":"9084"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MM"},{"VarCharValue":"8643"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BR"},{"VarCharValue":"8574"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"US"},{"VarCharValue":"8421"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NP"},{"VarCharValue":"8181"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PL"},{"VarCharValue":"7972"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"PH"},{"VarCharValue":"7400"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PK"},{"VarCharValue":"7387"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AU"},{"VarCharValue":"7036"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"US"},{"VarCharValue":"6443"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FR"},{"VarCharValue":"6248"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MY"},{"VarCharValue":"6095"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CA"},{"VarCharValue":"6010"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"PH"},{"VarCharValue":"5993"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MM"},{"VarCharValue":"5936"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GB"},{"VarCharValue":"5843"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CZ"},{"VarCharValue":"5746"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"KH"},{"VarCharValue":"5723"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SA"},{"VarCharValue":"5629"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ZA"},{"VarCharValue":"5583"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"ID"},{"VarCharValue":"5550"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"ID"},{"VarCharValue":"5535"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IT"},{"VarCharValue":"5500"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"US"},{"VarCharValue":"5492"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"TH"},{"VarCharValue":"5455"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AU"},{"VarCharValue":"5436"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"ID"},{"VarCharValue":"5377"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TR"},{"VarCharValue":"5357"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"TH"},{"VarCharValue":"5180"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BR"},{"VarCharValue":"4971"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NP"},{"VarCharValue":"4943"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"ID"},{"VarCharValue":"4923"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TH"},{"VarCharValue":"4813"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GB"},{"VarCharValue":"4793"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ZA"},{"VarCharValue":"4686"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"US"},{"VarCharValue":"4676"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"UZ"},{"VarCharValue":"4664"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"IN"},{"VarCharValue":"4640"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AE"},{"VarCharValue":"4611"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BR"},{"VarCharValue":"4527"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LK"},{"VarCharValue":"4521"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PL"},{"VarCharValue":"4255"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NL"},{"VarCharValue":"4194"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GR"},{"VarCharValue":"4173"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"IN"},{"VarCharValue":"4145"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"TW"},{"VarCharValue":"4073"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"IN"},{"VarCharValue":"4058"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SG"},{"VarCharValue":"4043"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"DE"},{"VarCharValue":"4007"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GB"},{"VarCharValue":"4000"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"KH"},{"VarCharValue":"3851"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MA"},{"VarCharValue":"3727"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"DE"},{"VarCharValue":"3534"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GB"},{"VarCharValue":"3525"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MY"},{"VarCharValue":"3439"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"KZ"},{"VarCharValue":"3367"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PL"},{"VarCharValue":"3363"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AT"},{"VarCharValue":"3262"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"VN"},{"VarCharValue":"3246"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"VN"},{"VarCharValue":"3215"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SA"},{"VarCharValue":"3209"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GB"},{"VarCharValue":"3141"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"DE"},{"VarCharValue":"3139"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"BR"},{"VarCharValue":"3130"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"DE"},{"VarCharValue":"3105"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MY"},{"VarCharValue":"3081"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BE"},{"VarCharValue":"3051"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"RU"},{"VarCharValue":"3044"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GE"},{"VarCharValue":"3039"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SE"},{"VarCharValue":"3025"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MX"},{"VarCharValue":"3010"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AE"},{"VarCharValue":"2999"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"RO"},{"VarCharValue":"2961"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BR"},{"VarCharValue":"2931"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"KE"},{"VarCharValue":"2901"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PL"},{"VarCharValue":"2901"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"TH"},{"VarCharValue":"2894"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"EG"},{"VarCharValue":"2874"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LK"},{"VarCharValue":"2862"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"PL"},{"VarCharValue":"2808"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"DE"},{"VarCharValue":"2748"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MX"},{"VarCharValue":"2746"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"DZ"},{"VarCharValue":"2733"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"VN"},{"VarCharValue":"2721"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CZ"},{"VarCharValue":"2665"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CH"},{"VarCharValue":"2587"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"UZ"},{"VarCharValue":"2576"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FR"},{"VarCharValue":"2566"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MA"},{"VarCharValue":"2556"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CA"},{"VarCharValue":"2537"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IT"},{"VarCharValue":"2524"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NZ"},{"VarCharValue":"2513"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AU"},{"VarCharValue":"2502"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GB"},{"VarCharValue":"2501"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GR"},{"VarCharValue":"2464"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"DK"},{"VarCharValue":"2418"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"DE"},{"VarCharValue":"2373"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MY"},{"VarCharValue":"2369"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"TH"},{"VarCharValue":"2365"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"VN"},{"VarCharValue":"2356"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BR"},{"VarCharValue":"2326"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"HU"},{"VarCharValue":"2323"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AZ"},{"VarCharValue":"2312"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FR"},{"VarCharValue":"2302"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"ID"},{"VarCharValue":"2289"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IE"},{"VarCharValue":"2278"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MX"},{"VarCharValue":"2253"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IQ"},{"VarCharValue":"2241"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"IN"},{"VarCharValue":"2238"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CA"},{"VarCharValue":"2173"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IL"},{"VarCharValue":"2156"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MY"},{"VarCharValue":"2148"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TR"},{"VarCharValue":"2134"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"JM"},{"VarCharValue":"2132"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SK"},{"VarCharValue":"2123"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BR"},{"VarCharValue":"2109"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"TH"},{"VarCharValue":"2077"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PL"},{"VarCharValue":"2074"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FR"},{"VarCharValue":"2065"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NL"},{"VarCharValue":"2029"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BD"},{"VarCharValue":"2024"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MX"},{"VarCharValue":"2003"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PL"},{"VarCharValue":"1988"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"DZ"},{"VarCharValue":"1981"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PT"},{"VarCharValue":"1938"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"PL"},{"VarCharValue":"1935"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"BR"},{"VarCharValue":"1933"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MX"},{"VarCharValue":"1922"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TN"},{"VarCharValue":"1908"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NZ"},{"VarCharValue":"1888"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SG"},{"VarCharValue":"1862"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ES"},{"VarCharValue":"1852"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MM"},{"VarCharValue":"1846"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"RU"},{"VarCharValue":"1834"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BE"},{"VarCharValue":"1830"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"TW"},{"VarCharValue":"1792"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"RO"},{"VarCharValue":"1776"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CA"},{"VarCharValue":"1759"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"US"},{"VarCharValue":"1744"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"IT"},{"VarCharValue":"1739"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LB"},{"VarCharValue":"1681"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"RO"},{"VarCharValue":"1661"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FR"},{"VarCharValue":"1633"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GE"},{"VarCharValue":"1628"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FI"},{"VarCharValue":"1612"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MN"},{"VarCharValue":"1612"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"KZ"},{"VarCharValue":"1607"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MY"},{"VarCharValue":"1591"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AU"},{"VarCharValue":"1568"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FR"},{"VarCharValue":"1567"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"KE"},{"VarCharValue":"1561"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AT"},{"VarCharValue":"1552"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"UA"},{"VarCharValue":"1550"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"IT"},{"VarCharValue":"1549"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"EG"},{"VarCharValue":"1542"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IQ"},{"VarCharValue":"1530"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"KZ"},{"VarCharValue":"1507"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"HU"},{"VarCharValue":"1493"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NL"},{"VarCharValue":"1476"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TW"},{"VarCharValue":"1469"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"KR"},{"VarCharValue":"1461"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PK"},{"VarCharValue":"1442"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FR"},{"VarCharValue":"1441"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"JP"},{"VarCharValue":"1441"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"IT"},{"VarCharValue":"1417"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AM"},{"VarCharValue":"1411"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NO"},{"VarCharValue":"1402"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SG"},{"VarCharValue":"1385"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IE"},{"VarCharValue":"1383"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"RO"},{"VarCharValue":"1383"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TN"},{"VarCharValue":"1381"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CA"},{"VarCharValue":"1379"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SK"},{"VarCharValue":"1374"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LT"},{"VarCharValue":"1363"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"RO"},{"VarCharValue":"1355"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CZ"},{"VarCharValue":"1354"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BG"},{"VarCharValue":"1354"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"JM"},{"VarCharValue":"1334"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CZ"},{"VarCharValue":"1324"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CO"},{"VarCharValue":"1324"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MX"},{"VarCharValue":"1313"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BN"},{"VarCharValue":"1273"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AU"},{"VarCharValue":"1268"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"IT"},{"VarCharValue":"1257"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"TW"},{"VarCharValue":"1253"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ET"},{"VarCharValue":"1253"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"ZA"},{"VarCharValue":"1252"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"VN"},{"VarCharValue":"1250"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"ES"},{"VarCharValue":"1246"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MX"},{"VarCharValue":"1244"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"TH"},{"VarCharValue":"1232"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"NP"},{"VarCharValue":"1219"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"ZA"},{"VarCharValue":"1218"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"RU"},{"VarCharValue":"1217"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SE"},{"VarCharValue":"1213"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AR"},{"VarCharValue":"1194"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"TR"},{"VarCharValue":"1189"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CA"},{"VarCharValue":"1189"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GR"},{"VarCharValue":"1181"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IL"},{"VarCharValue":"1172"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MU"},{"VarCharValue":"1156"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"RS"},{"VarCharValue":"1154"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"IT"},{"VarCharValue":"1148"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AU"},{"VarCharValue":"1146"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TL"},{"VarCharValue":"1140"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"QA"},{"VarCharValue":"1137"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CH"},{"VarCharValue":"1136"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CO"},{"VarCharValue":"1133"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"VN"},{"VarCharValue":"1132"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"TR"},{"VarCharValue":"1119"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AL"},{"VarCharValue":"1091"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"TR"},{"VarCharValue":"1088"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AU"},{"VarCharValue":"1087"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"ES"},{"VarCharValue":"1086"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PE"},{"VarCharValue":"1084"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"IL"},{"VarCharValue":"1067"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PE"},{"VarCharValue":"1061"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SA"},{"VarCharValue":"1052"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"KW"},{"VarCharValue":"1049"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ES"},{"VarCharValue":"1042"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"NL"},{"VarCharValue":"1042"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"ES"},{"VarCharValue":"1042"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CZ"},{"VarCharValue":"1041"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TT"},{"VarCharValue":"1027"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CZ"},{"VarCharValue":"1012"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"KE"},{"VarCharValue":"997"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"HU"},{"VarCharValue":"996"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"IL"},{"VarCharValue":"990"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"KH"},{"VarCharValue":"978"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"IT"},{"VarCharValue":"974"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"JO"},{"VarCharValue":"974"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"IL"},{"VarCharValue":"973"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CA"},{"VarCharValue":"967"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"RO"},{"VarCharValue":"953"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MN"},{"VarCharValue":"952"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ET"},{"VarCharValue":"949"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"DK"},{"VarCharValue":"939"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"TR"},{"VarCharValue":"925"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"HK"},{"VarCharValue":"923"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AR"},{"VarCharValue":"921"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"RO"},{"VarCharValue":"915"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"RU"},{"VarCharValue":"908"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MA"},{"VarCharValue":"908"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"JP"},{"VarCharValue":"906"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NG"},{"VarCharValue":"904"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BE"},{"VarCharValue":"904"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AZ"},{"VarCharValue":"903"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"HR"},{"VarCharValue":"900"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CZ"},{"VarCharValue":"897"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"NL"},{"VarCharValue":"896"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"AU"},{"VarCharValue":"896"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LV"},{"VarCharValue":"894"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"ES"},{"VarCharValue":"893"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"NL"},{"VarCharValue":"887"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LB"},{"VarCharValue":"881"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NP"},{"VarCharValue":"869"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PT"},{"VarCharValue":"867"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"KR"},{"VarCharValue":"866"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"JP"},{"VarCharValue":"863"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"KZ"},{"VarCharValue":"853"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SI"},{"VarCharValue":"847"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"HU"},{"VarCharValue":"844"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"ZA"},{"VarCharValue":"843"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"JP"},{"VarCharValue":"840"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BE"},{"VarCharValue":"836"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MD"},{"VarCharValue":"832"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"ZA"},{"VarCharValue":"832"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PE"},{"VarCharValue":"819"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"TH"},{"VarCharValue":"818"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TT"},{"VarCharValue":"817"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FI"},{"VarCharValue":"816"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"RU"},{"VarCharValue":"809"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"HU"},{"VarCharValue":"807"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"NL"},{"VarCharValue":"806"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CZ"},{"VarCharValue":"806"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MY"},{"VarCharValue":"805"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"QA"},{"VarCharValue":"800"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GY"},{"VarCharValue":"798"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"UA"},{"VarCharValue":"782"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AE"},{"VarCharValue":"781"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"SA"},{"VarCharValue":"774"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AL"},{"VarCharValue":"771"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"ES"},{"VarCharValue":"771"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"IN"},{"VarCharValue":"761"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"SG"},{"VarCharValue":"757"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"OM"},{"VarCharValue":"756"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TW"},{"VarCharValue":"755"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AR"},{"VarCharValue":"754"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"HU"},{"VarCharValue":"747"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"AL"},{"VarCharValue":"746"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AE"},{"VarCharValue":"743"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AL"},{"VarCharValue":"742"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"IL"},{"VarCharValue":"741"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"VN"},{"VarCharValue":"740"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"TR"},{"VarCharValue":"738"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"JM"},{"VarCharValue":"737"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AM"},{"VarCharValue":"733"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MX"},{"VarCharValue":"732"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"RO"},{"VarCharValue":"731"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CO"},{"VarCharValue":"731"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"HK"},{"VarCharValue":"725"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"PE"},{"VarCharValue":"712"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"RU"},{"VarCharValue":"711"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"NL"},{"VarCharValue":"709"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AL"},{"VarCharValue":"705"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LT"},{"VarCharValue":"700"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ZZ"},{"VarCharValue":"694"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NO"},{"VarCharValue":"688"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"KR"},{"VarCharValue":"688"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LA"},{"VarCharValue":"686"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PE"},{"VarCharValue":"679"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PT"},{"VarCharValue":"673"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BE"},{"VarCharValue":"671"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PT"},{"VarCharValue":"671"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CO"},{"VarCharValue":"667"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CY"},{"VarCharValue":"664"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GE"},{"VarCharValue":"662"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"RS"},{"VarCharValue":"659"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"IL"},{"VarCharValue":"654"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"KW"},{"VarCharValue":"653"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LK"},{"VarCharValue":"652"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"UA"},{"VarCharValue":"648"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AR"},{"VarCharValue":"645"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"ES"},{"VarCharValue":"645"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TL"},{"VarCharValue":"638"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"JO"},{"VarCharValue":"634"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NG"},{"VarCharValue":"632"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AT"},{"VarCharValue":"631"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CL"},{"VarCharValue":"630"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MU"},{"VarCharValue":"629"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"KG"},{"VarCharValue":"628"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"KG"},{"VarCharValue":"616"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"HK"},{"VarCharValue":"615"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"KZ"},{"VarCharValue":"612"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"SG"},{"VarCharValue":"609"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BG"},{"VarCharValue":"609"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"HU"},{"VarCharValue":"605"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"ZA"},{"VarCharValue":"600"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GT"},{"VarCharValue":"598"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BH"},{"VarCharValue":"598"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"IN"},{"VarCharValue":"595"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"TR"},{"VarCharValue":"593"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"TW"},{"VarCharValue":"591"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"KH"},{"VarCharValue":"591"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"SK"},{"VarCharValue":"589"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AT"},{"VarCharValue":"584"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"HU"},{"VarCharValue":"584"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"UZ"},{"VarCharValue":"583"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BE"},{"VarCharValue":"582"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SE"},{"VarCharValue":"582"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AT"},{"VarCharValue":"582"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PT"},{"VarCharValue":"581"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CH"},{"VarCharValue":"572"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CL"},{"VarCharValue":"570"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MK"},{"VarCharValue":"570"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"BE"},{"VarCharValue":"564"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"UZ"},{"VarCharValue":"561"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"DZ"},{"VarCharValue":"553"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SG"},{"VarCharValue":"545"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BN"},{"VarCharValue":"543"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MD"},{"VarCharValue":"542"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SK"},{"VarCharValue":"541"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SK"},{"VarCharValue":"540"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AT"},{"VarCharValue":"526"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FJ"},{"VarCharValue":"526"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MM"},{"VarCharValue":"523"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"NP"},{"VarCharValue":"522"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CO"},{"VarCharValue":"522"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AL"},{"VarCharValue":"522"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AE"},{"VarCharValue":"522"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LV"},{"VarCharValue":"520"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BA"},{"VarCharValue":"520"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SI"},{"VarCharValue":"516"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"HR"},{"VarCharValue":"509"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AL"},{"VarCharValue":"507"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AR"},{"VarCharValue":"505"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"IE"},{"VarCharValue":"505"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"RU"},{"VarCharValue":"504"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"PT"},{"VarCharValue":"504"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SA"},{"VarCharValue":"504"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"IL"},{"VarCharValue":"498"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"TN"},{"VarCharValue":"493"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"IN"},{"VarCharValue":"493"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"EG"},{"VarCharValue":"487"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"SK"},{"VarCharValue":"485"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"RU"},{"VarCharValue":"485"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AT"},{"VarCharValue":"484"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"UA"},{"VarCharValue":"483"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"EC"},{"VarCharValue":"483"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SI"},{"VarCharValue":"481"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"DK"},{"VarCharValue":"479"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GE"},{"VarCharValue":"479"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CL"},{"VarCharValue":"477"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"LT"},{"VarCharValue":"476"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SR"},{"VarCharValue":"476"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AR"},{"VarCharValue":"475"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MA"},{"VarCharValue":"474"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"IQ"},{"VarCharValue":"472"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GR"},{"VarCharValue":"471"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GY"},{"VarCharValue":"469"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CH"},{"VarCharValue":"468"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FJ"},{"VarCharValue":"468"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"IE"},{"VarCharValue":"467"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PT"},{"VarCharValue":"466"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CH"},{"VarCharValue":"465"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NZ"},{"VarCharValue":"464"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GR"},{"VarCharValue":"463"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CO"},{"VarCharValue":"463"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"BE"},{"VarCharValue":"461"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"DK"},{"VarCharValue":"458"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"KR"},{"VarCharValue":"456"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AE"},{"VarCharValue":"455"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"VN"},{"VarCharValue":"449"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GR"},{"VarCharValue":"449"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"JP"},{"VarCharValue":"447"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"KR"},{"VarCharValue":"445"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"KZ"},{"VarCharValue":"439"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AZ"},{"VarCharValue":"438"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"EC"},{"VarCharValue":"436"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"HK"},{"VarCharValue":"435"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PE"},{"VarCharValue":"433"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"ZA"},{"VarCharValue":"433"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"PE"},{"VarCharValue":"429"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"AT"},{"VarCharValue":"429"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LA"},{"VarCharValue":"429"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ZZ"},{"VarCharValue":"429"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MV"},{"VarCharValue":"426"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"AR"},{"VarCharValue":"426"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"SI"},{"VarCharValue":"425"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"KE"},{"VarCharValue":"424"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"HK"},{"VarCharValue":"423"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"KE"},{"VarCharValue":"421"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"UA"},{"VarCharValue":"421"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"LK"},{"VarCharValue":"419"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"KR"},{"VarCharValue":"419"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"SG"},{"VarCharValue":"419"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BO"},{"VarCharValue":"418"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"EE"},{"VarCharValue":"415"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"SK"},{"VarCharValue":"411"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"DZ"},{"VarCharValue":"408"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BD"},{"VarCharValue":"407"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"TW"},{"VarCharValue":"403"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SY"},{"VarCharValue":"402"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"TW"},{"VarCharValue":"401"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"IE"},{"VarCharValue":"400"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BO"},{"VarCharValue":"399"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"SA"},{"VarCharValue":"398"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"LT"},{"VarCharValue":"397"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"KH"},{"VarCharValue":"396"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CH"},{"VarCharValue":"395"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MD"},{"VarCharValue":"395"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"LT"},{"VarCharValue":"394"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AM"},{"VarCharValue":"394"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"SA"},{"VarCharValue":"392"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"RU"},{"VarCharValue":"391"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GT"},{"VarCharValue":"390"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"CA"},{"VarCharValue":"386"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GH"},{"VarCharValue":"385"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"UA"},{"VarCharValue":"385"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PG"},{"VarCharValue":"382"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BA"},{"VarCharValue":"382"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"PT"},{"VarCharValue":"380"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GR"},{"VarCharValue":"377"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"JP"},{"VarCharValue":"374"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"SK"},{"VarCharValue":"373"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"KH"},{"VarCharValue":"372"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GB"},{"VarCharValue":"370"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"KZ"},{"VarCharValue":"368"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"OM"},{"VarCharValue":"367"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"UA"},{"VarCharValue":"367"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"NZ"},{"VarCharValue":"366"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BT"},{"VarCharValue":"366"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SR"},{"VarCharValue":"365"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BH"},{"VarCharValue":"364"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"HT"},{"VarCharValue":"359"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"JP"},{"VarCharValue":"356"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"SI"},{"VarCharValue":"354"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SE"},{"VarCharValue":"353"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"NP"},{"VarCharValue":"353"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"NO"},{"VarCharValue":"351"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CH"},{"VarCharValue":"350"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CY"},{"VarCharValue":"350"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AR"},{"VarCharValue":"348"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FI"},{"VarCharValue":"346"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"KG"},{"VarCharValue":"344"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CL"},{"VarCharValue":"342"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LT"},{"VarCharValue":"341"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"HN"},{"VarCharValue":"340"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"ET"},{"VarCharValue":"339"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"KH"},{"VarCharValue":"337"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"IE"},{"VarCharValue":"337"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"EC"},{"VarCharValue":"335"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"KR"},{"VarCharValue":"335"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CH"},{"VarCharValue":"335"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"TT"},{"VarCharValue":"334"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"SG"},{"VarCharValue":"333"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AE"},{"VarCharValue":"326"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"SI"},{"VarCharValue":"326"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MA"},{"VarCharValue":"326"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BZ"},{"VarCharValue":"326"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MN"},{"VarCharValue":"325"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CL"},{"VarCharValue":"324"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MK"},{"VarCharValue":"324"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NA"},{"VarCharValue":"324"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"DE"},{"VarCharValue":"324"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"AU"},{"VarCharValue":"320"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LB"},{"VarCharValue":"319"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BG"},{"VarCharValue":"319"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GE"},{"VarCharValue":"318"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"JP"},{"VarCharValue":"313"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"HR"},{"VarCharValue":"312"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"DO"},{"VarCharValue":"312"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"ZZ"},{"VarCharValue":"312"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"RS"},{"VarCharValue":"312"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IR"},{"VarCharValue":"311"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MV"},{"VarCharValue":"309"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GT"},{"VarCharValue":"308"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GH"},{"VarCharValue":"307"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MN"},{"VarCharValue":"305"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"EC"},{"VarCharValue":"304"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"IE"},{"VarCharValue":"303"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TZ"},{"VarCharValue":"301"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"KE"},{"VarCharValue":"300"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"SA"},{"VarCharValue":"299"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"JO"},{"VarCharValue":"294"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"RS"},{"VarCharValue":"293"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"LT"},{"VarCharValue":"293"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"DK"},{"VarCharValue":"292"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"LT"},{"VarCharValue":"290"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ZM"},{"VarCharValue":"288"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"TW"},{"VarCharValue":"288"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"HR"},{"VarCharValue":"287"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CO"},{"VarCharValue":"287"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"SV"},{"VarCharValue":"287"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MA"},{"VarCharValue":"286"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GR"},{"VarCharValue":"286"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"NZ"},{"VarCharValue":"281"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MM"},{"VarCharValue":"281"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PE"},{"VarCharValue":"277"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"KZ"},{"VarCharValue":"276"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"HT"},{"VarCharValue":"276"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"EC"},{"VarCharValue":"276"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"UA"},{"VarCharValue":"275"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CL"},{"VarCharValue":"273"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ZM"},{"VarCharValue":"273"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"KR"},{"VarCharValue":"272"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ZW"},{"VarCharValue":"271"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"TN"},{"VarCharValue":"269"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"KZ"},{"VarCharValue":"268"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"AZ"},{"VarCharValue":"268"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"UG"},{"VarCharValue":"266"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MM"},{"VarCharValue":"263"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MN"},{"VarCharValue":"263"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MT"},{"VarCharValue":"263"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SY"},{"VarCharValue":"262"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"UG"},{"VarCharValue":"262"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BY"},{"VarCharValue":"261"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PG"},{"VarCharValue":"261"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"RS"},{"VarCharValue":"261"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BG"},{"VarCharValue":"261"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MM"},{"VarCharValue":"261"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MD"},{"VarCharValue":"258"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"VE"},{"VarCharValue":"257"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BW"},{"VarCharValue":"256"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PR"},{"VarCharValue":"255"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LU"},{"VarCharValue":"254"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"SE"},{"VarCharValue":"254"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MD"},{"VarCharValue":"253"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"HR"},{"VarCharValue":"253"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MD"},{"VarCharValue":"252"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"TN"},{"VarCharValue":"251"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"SI"},{"VarCharValue":"251"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"RE"},{"VarCharValue":"251"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"DK"},{"VarCharValue":"250"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GY"},{"VarCharValue":"248"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"WS"},{"VarCharValue":"248"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MT"},{"VarCharValue":"247"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BN"},{"VarCharValue":"244"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CL"},{"VarCharValue":"243"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"HK"},{"VarCharValue":"242"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GE"},{"VarCharValue":"240"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AM"},{"VarCharValue":"240"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"KG"},{"VarCharValue":"240"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PS"},{"VarCharValue":"240"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"BO"},{"VarCharValue":"239"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NO"},{"VarCharValue":"239"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"IE"},{"VarCharValue":"239"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"LV"},{"VarCharValue":"238"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BO"},{"VarCharValue":"233"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SI"},{"VarCharValue":"233"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BG"},{"VarCharValue":"233"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AF"},{"VarCharValue":"233"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"KW"},{"VarCharValue":"233"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BD"},{"VarCharValue":"232"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"WS"},{"VarCharValue":"232"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"BG"},{"VarCharValue":"232"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SO"},{"VarCharValue":"230"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"DK"},{"VarCharValue":"228"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"HR"},{"VarCharValue":"228"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BT"},{"VarCharValue":"228"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TZ"},{"VarCharValue":"227"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MU"},{"VarCharValue":"226"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PY"},{"VarCharValue":"226"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PS"},{"VarCharValue":"225"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CR"},{"VarCharValue":"223"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SD"},{"VarCharValue":"223"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NA"},{"VarCharValue":"223"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"VE"},{"VarCharValue":"222"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"ZZ"},{"VarCharValue":"222"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PF"},{"VarCharValue":"221"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"EC"},{"VarCharValue":"219"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"UZ"},{"VarCharValue":"217"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LY"},{"VarCharValue":"217"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"SE"},{"VarCharValue":"216"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LY"},{"VarCharValue":"215"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"ZZ"},{"VarCharValue":"215"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GT"},{"VarCharValue":"211"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CR"},{"VarCharValue":"211"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"NZ"},{"VarCharValue":"211"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GT"},{"VarCharValue":"210"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MA"},{"VarCharValue":"209"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"JP"},{"VarCharValue":"208"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"QA"},{"VarCharValue":"206"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CO"},{"VarCharValue":"206"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"VE"},{"VarCharValue":"206"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MG"},{"VarCharValue":"205"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"HK"},{"VarCharValue":"205"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PA"},{"VarCharValue":"202"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SO"},{"VarCharValue":"202"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"AL"},{"VarCharValue":"200"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"DO"},{"VarCharValue":"199"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"EC"},{"VarCharValue":"199"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"KG"},{"VarCharValue":"199"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GE"},{"VarCharValue":"198"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"HR"},{"VarCharValue":"198"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LA"},{"VarCharValue":"197"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MU"},{"VarCharValue":"196"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BO"},{"VarCharValue":"196"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"HN"},{"VarCharValue":"196"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"EE"},{"VarCharValue":"196"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"RS"},{"VarCharValue":"195"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"RS"},{"VarCharValue":"194"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"DK"},{"VarCharValue":"194"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GT"},{"VarCharValue":"193"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CL"},{"VarCharValue":"191"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ZW"},{"VarCharValue":"191"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CR"},{"VarCharValue":"190"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"KE"},{"VarCharValue":"189"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BG"},{"VarCharValue":"189"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"LV"},{"VarCharValue":"188"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"JM"},{"VarCharValue":"186"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SR"},{"VarCharValue":"186"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AF"},{"VarCharValue":"186"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SV"},{"VarCharValue":"184"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"ZZ"},{"VarCharValue":"183"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"KG"},{"VarCharValue":"183"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"NG"},{"VarCharValue":"180"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"DZ"},{"VarCharValue":"178"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BW"},{"VarCharValue":"178"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LV"},{"VarCharValue":"178"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AM"},{"VarCharValue":"177"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BA"},{"VarCharValue":"177"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"ZZ"},{"VarCharValue":"176"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BB"},{"VarCharValue":"176"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"TT"},{"VarCharValue":"176"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"BS"},{"VarCharValue":"176"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FR"},{"VarCharValue":"176"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"HN"},{"VarCharValue":"176"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"BR"},{"VarCharValue":"175"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SN"},{"VarCharValue":"175"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CY"},{"VarCharValue":"175"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"DO"},{"VarCharValue":"173"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IR"},{"VarCharValue":"173"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"UA"},{"VarCharValue":"173"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"AE"},{"VarCharValue":"172"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SD"},{"VarCharValue":"171"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"DZ"},{"VarCharValue":"169"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BZ"},{"VarCharValue":"169"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GE"},{"VarCharValue":"169"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"SG"},{"VarCharValue":"168"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SV"},{"VarCharValue":"168"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"KR"},{"VarCharValue":"168"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PY"},{"VarCharValue":"168"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BN"},{"VarCharValue":"168"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"EE"},{"VarCharValue":"167"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"ZZ"},{"VarCharValue":"167"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PK"},{"VarCharValue":"166"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"LK"},{"VarCharValue":"166"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GU"},{"VarCharValue":"165"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"QA"},{"VarCharValue":"165"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"LV"},{"VarCharValue":"165"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"DZ"},{"VarCharValue":"162"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"HR"},{"VarCharValue":"160"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"PY"},{"VarCharValue":"160"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"NO"},{"VarCharValue":"160"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"NZ"},{"VarCharValue":"158"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"PL"},{"VarCharValue":"156"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"UZ"},{"VarCharValue":"156"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"TO"},{"VarCharValue":"156"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"BG"},{"VarCharValue":"154"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MN"},{"VarCharValue":"154"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"BO"},{"VarCharValue":"154"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MM"},{"VarCharValue":"153"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"HK"},{"VarCharValue":"153"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"ME"},{"VarCharValue":"153"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"EG"},{"VarCharValue":"152"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PR"},{"VarCharValue":"152"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MX"},{"VarCharValue":"152"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"PY"},{"VarCharValue":"151"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"LV"},{"VarCharValue":"150"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"KH"},{"VarCharValue":"149"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MA"},{"VarCharValue":"148"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"TN"},{"VarCharValue":"148"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"RE"},{"VarCharValue":"147"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"RW"},{"VarCharValue":"146"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"NO"},{"VarCharValue":"145"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"EE"},{"VarCharValue":"144"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MD"},{"VarCharValue":"143"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BO"},{"VarCharValue":"143"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PK"},{"VarCharValue":"142"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PF"},{"VarCharValue":"142"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"SE"},{"VarCharValue":"141"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"DO"},{"VarCharValue":"140"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FI"},{"VarCharValue":"140"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CR"},{"VarCharValue":"140"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MU"},{"VarCharValue":"138"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"NP"},{"VarCharValue":"138"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"VE"},{"VarCharValue":"137"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"TT"},{"VarCharValue":"136"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"AE"},{"VarCharValue":"136"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"TN"},{"VarCharValue":"135"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BD"},{"VarCharValue":"135"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MK"},{"VarCharValue":"134"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"RS"},{"VarCharValue":"134"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BS"},{"VarCharValue":"132"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"EG"},{"VarCharValue":"132"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MG"},{"VarCharValue":"132"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MO"},{"VarCharValue":"131"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"SV"},{"VarCharValue":"131"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CI"},{"VarCharValue":"131"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GT"},{"VarCharValue":"130"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"HN"},{"VarCharValue":"130"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"DO"},{"VarCharValue":"129"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"PA"},{"VarCharValue":"129"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"VE"},{"VarCharValue":"129"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BY"},{"VarCharValue":"127"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"NO"},{"VarCharValue":"127"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"VE"},{"VarCharValue":"127"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MZ"},{"VarCharValue":"127"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LU"},{"VarCharValue":"125"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CM"},{"VarCharValue":"125"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AO"},{"VarCharValue":"125"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"NP"},{"VarCharValue":"124"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CR"},{"VarCharValue":"124"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"NZ"},{"VarCharValue":"123"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"EC"},{"VarCharValue":"123"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"ET"},{"VarCharValue":"123"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"RW"},{"VarCharValue":"122"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"VE"},{"VarCharValue":"122"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FI"},{"VarCharValue":"121"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SN"},{"VarCharValue":"121"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"KH"},{"VarCharValue":"120"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"QA"},{"VarCharValue":"118"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"TT"},{"VarCharValue":"118"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MO"},{"VarCharValue":"118"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"KE"},{"VarCharValue":"117"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BO"},{"VarCharValue":"117"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"OM"},{"VarCharValue":"116"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"KG"},{"VarCharValue":"116"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"HT"},{"VarCharValue":"115"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GD"},{"VarCharValue":"115"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"IT"},{"VarCharValue":"115"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"TL"},{"VarCharValue":"115"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BB"},{"VarCharValue":"115"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MN"},{"VarCharValue":"115"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"UZ"},{"VarCharValue":"115"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MD"},{"VarCharValue":"115"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MT"},{"VarCharValue":"114"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SL"},{"VarCharValue":"113"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BN"},{"VarCharValue":"113"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NC"},{"VarCharValue":"112"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"LV"},{"VarCharValue":"112"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"UY"},{"VarCharValue":"112"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"KG"},{"VarCharValue":"112"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"AZ"},{"VarCharValue":"112"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"SA"},{"VarCharValue":"112"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"EE"},{"VarCharValue":"112"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CN"},{"VarCharValue":"111"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MM"},{"VarCharValue":"111"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"DZ"},{"VarCharValue":"109"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"LA"},{"VarCharValue":"109"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CR"},{"VarCharValue":"109"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"JO"},{"VarCharValue":"108"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"HN"},{"VarCharValue":"107"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"CY"},{"VarCharValue":"107"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"HK"},{"VarCharValue":"107"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MV"},{"VarCharValue":"107"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"TN"},{"VarCharValue":"106"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"LC"},{"VarCharValue":"106"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FI"},{"VarCharValue":"106"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CY"},{"VarCharValue":"105"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"IS"},{"VarCharValue":"104"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"HN"},{"VarCharValue":"104"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"ES"},{"VarCharValue":"104"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"BY"},{"VarCharValue":"104"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"SE"},{"VarCharValue":"104"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AM"},{"VarCharValue":"103"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"NI"},{"VarCharValue":"103"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"KW"},{"VarCharValue":"103"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"IQ"},{"VarCharValue":"103"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"UY"},{"VarCharValue":"102"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FI"},{"VarCharValue":"102"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"HN"},{"VarCharValue":"102"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"JO"},{"VarCharValue":"101"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"PA"},{"VarCharValue":"101"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"ME"},{"VarCharValue":"101"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"DO"},{"VarCharValue":"100"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MK"},{"VarCharValue":"99"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"JM"},{"VarCharValue":"99"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"LB"},{"VarCharValue":"99"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PA"},{"VarCharValue":"98"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MT"},{"VarCharValue":"98"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"IQ"},{"VarCharValue":"98"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"AS"},{"VarCharValue":"98"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"JM"},{"VarCharValue":"98"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"KW"},{"VarCharValue":"98"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"BY"},{"VarCharValue":"98"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CM"},{"VarCharValue":"97"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"PY"},{"VarCharValue":"97"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"YE"},{"VarCharValue":"96"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BH"},{"VarCharValue":"95"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"YE"},{"VarCharValue":"95"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"NI"},{"VarCharValue":"95"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"EE"},{"VarCharValue":"95"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NA"},{"VarCharValue":"95"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"NO"},{"VarCharValue":"94"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PG"},{"VarCharValue":"94"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BA"},{"VarCharValue":"92"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GU"},{"VarCharValue":"92"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"NP"},{"VarCharValue":"92"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"CY"},{"VarCharValue":"92"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GY"},{"VarCharValue":"92"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MK"},{"VarCharValue":"92"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SY"},{"VarCharValue":"92"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FJ"},{"VarCharValue":"92"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"SV"},{"VarCharValue":"91"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"PY"},{"VarCharValue":"90"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MO"},{"VarCharValue":"89"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PK"},{"VarCharValue":"89"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CI"},{"VarCharValue":"88"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PY"},{"VarCharValue":"88"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SV"},{"VarCharValue":"88"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CW"},{"VarCharValue":"88"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AM"},{"VarCharValue":"87"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"SV"},{"VarCharValue":"87"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MK"},{"VarCharValue":"87"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"LK"},{"VarCharValue":"86"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"ET"},{"VarCharValue":"86"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"EG"},{"VarCharValue":"86"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"BN"},{"VarCharValue":"85"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"SB"},{"VarCharValue":"85"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GA"},{"VarCharValue":"85"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"LA"},{"VarCharValue":"84"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MT"},{"VarCharValue":"84"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MU"},{"VarCharValue":"84"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"TJ"},{"VarCharValue":"84"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"WS"},{"VarCharValue":"84"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"EE"},{"VarCharValue":"83"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MN"},{"VarCharValue":"83"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GT"},{"VarCharValue":"83"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"JO"},{"VarCharValue":"82"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"LA"},{"VarCharValue":"82"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"LB"},{"VarCharValue":"81"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"IR"},{"VarCharValue":"80"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"TT"},{"VarCharValue":"80"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CR"},{"VarCharValue":"79"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"RE"},{"VarCharValue":"79"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"RW"},{"VarCharValue":"78"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GD"},{"VarCharValue":"78"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"VE"},{"VarCharValue":"78"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SL"},{"VarCharValue":"77"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"NL"},{"VarCharValue":"77"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"TR"},{"VarCharValue":"77"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AO"},{"VarCharValue":"76"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"VC"},{"VarCharValue":"76"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"IS"},{"VarCharValue":"75"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"CY"},{"VarCharValue":"75"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"NA"},{"VarCharValue":"74"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"EE"},{"VarCharValue":"74"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"TT"},{"VarCharValue":"74"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"AZ"},{"VarCharValue":"74"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"NC"},{"VarCharValue":"74"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NI"},{"VarCharValue":"74"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AS"},{"VarCharValue":"73"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GH"},{"VarCharValue":"73"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BA"},{"VarCharValue":"73"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"KW"},{"VarCharValue":"72"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"PE"},{"VarCharValue":"72"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"PA"},{"VarCharValue":"71"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"QA"},{"VarCharValue":"71"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"PA"},{"VarCharValue":"71"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FI"},{"VarCharValue":"71"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"EG"},{"VarCharValue":"71"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BW"},{"VarCharValue":"71"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"BN"},{"VarCharValue":"71"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"LC"},{"VarCharValue":"71"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FJ"},{"VarCharValue":"70"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"QA"},{"VarCharValue":"70"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MO"},{"VarCharValue":"70"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"NG"},{"VarCharValue":"70"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MZ"},{"VarCharValue":"70"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BZ"},{"VarCharValue":"70"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"BY"},{"VarCharValue":"70"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MU"},{"VarCharValue":"70"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BT"},{"VarCharValue":"70"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"RO"},{"VarCharValue":"70"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"LB"},{"VarCharValue":"70"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MK"},{"VarCharValue":"69"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MR"},{"VarCharValue":"69"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MT"},{"VarCharValue":"69"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BY"},{"VarCharValue":"68"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BW"},{"VarCharValue":"68"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"UY"},{"VarCharValue":"68"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"ZM"},{"VarCharValue":"68"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"BA"},{"VarCharValue":"68"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"ZM"},{"VarCharValue":"67"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"CY"},{"VarCharValue":"67"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"HN"},{"VarCharValue":"66"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"AR"},{"VarCharValue":"66"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"CN"},{"VarCharValue":"66"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MG"},{"VarCharValue":"66"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"CR"},{"VarCharValue":"66"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"UZ"},{"VarCharValue":"65"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"SB"},{"VarCharValue":"65"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"UY"},{"VarCharValue":"65"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"OM"},{"VarCharValue":"64"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FJ"},{"VarCharValue":"64"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GL"},{"VarCharValue":"64"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"IQ"},{"VarCharValue":"64"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MR"},{"VarCharValue":"64"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"CN"},{"VarCharValue":"64"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BN"},{"VarCharValue":"64"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LY"},{"VarCharValue":"63"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"KG"},{"VarCharValue":"63"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"DM"},{"VarCharValue":"63"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"ET"},{"VarCharValue":"63"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"CD"},{"VarCharValue":"63"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"NA"},{"VarCharValue":"63"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MQ"},{"VarCharValue":"63"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GP"},{"VarCharValue":"62"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"BY"},{"VarCharValue":"61"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"AM"},{"VarCharValue":"61"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GA"},{"VarCharValue":"61"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BZ"},{"VarCharValue":"61"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GU"},{"VarCharValue":"61"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GM"},{"VarCharValue":"60"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PA"},{"VarCharValue":"59"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"PR"},{"VarCharValue":"59"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"LU"},{"VarCharValue":"59"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"SV"},{"VarCharValue":"59"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"BB"},{"VarCharValue":"59"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"NZ"},{"VarCharValue":"58"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"KW"},{"VarCharValue":"58"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GY"},{"VarCharValue":"58"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"BW"},{"VarCharValue":"58"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"AG"},{"VarCharValue":"58"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"EG"},{"VarCharValue":"58"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"SR"},{"VarCharValue":"58"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"AZ"},{"VarCharValue":"58"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GY"},{"VarCharValue":"57"}]]');
            foreach ($arrayVideos as $id => $video) {
                $totalViewByCountry = 0;

                $count = 0;
                foreach ($datas as $index => $data) {

                    if ($index > 0) {
                        $videoId = $data[0]->VarCharValue;
                        $countryCode = $data[1]->VarCharValue;
                        $views = $data[2]->VarCharValue;
                        if ($video == $videoId) {
                            $count++;
                            $totalViewByCountry = $totalViewByCountry + $views;
                            if ($count <= 10) {
                                $listsByCountry[] = (object) ["video_id" => $videoId, "country" => $countryCode, "views" => $views];
                            }
                        }
                    }
                }
                $totalViewPercent = 0;
//                $datas2 = json_decode('[[{"VarCharValue":"video_id"},{"VarCharValue":"gender"},{"VarCharValue":"age_group"},{"VarCharValue":"total"},{"VarCharValue":"views_percentage"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"965"},{"VarCharValue":"139.0651944812622"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"85"},{"VarCharValue":"9.218048132039321"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"38"},{"VarCharValue":"22.610778706194814"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"431"},{"VarCharValue":"20.136600219472285"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"2619"},{"VarCharValue":"421.6123376328335"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"52"},{"VarCharValue":"21.39977449286726"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"194"},{"VarCharValue":"29.310729434703774"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"1724"},{"VarCharValue":"180.69901326745278"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"87"},{"VarCharValue":"28.111155766758955"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"397"},{"VarCharValue":"356.1164467726579"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"2694"},{"VarCharValue":"440.7609639469968"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"858"},{"VarCharValue":"301.29990790431253"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"53"},{"VarCharValue":"4.8406321888147"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"165"},{"VarCharValue":"17.41653402475273"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"90"},{"VarCharValue":"1.3175531677103727"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"445"},{"VarCharValue":"42.28648762823969"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"15"},{"VarCharValue":"1.2480849012402198"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"2232"},{"VarCharValue":"430.7146635880719"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"121"},{"VarCharValue":"28.57090492110722"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"800"},{"VarCharValue":"104.60224830311073"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"102"},{"VarCharValue":"5.103096738553482"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"209"},{"VarCharValue":"30.039684438605345"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"253"},{"VarCharValue":"16.46124748307336"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"1733"},{"VarCharValue":"1825.6450382801534"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"350"},{"VarCharValue":"159.97151429877036"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"230"},{"VarCharValue":"27.903787090859293"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"3615"},{"VarCharValue":"844.9652649348668"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"744"},{"VarCharValue":"745.3109047730866"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"437"},{"VarCharValue":"117.44952555583029"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"204"},{"VarCharValue":"17.20701347249081"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"692"},{"VarCharValue":"405.0326476306356"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"3028"},{"VarCharValue":"1800.3881663025002"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2024"},{"VarCharValue":"988.380831212748"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"540"},{"VarCharValue":"77.8720619747606"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"895"},{"VarCharValue":"987.1068769378561"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1386"},{"VarCharValue":"352.0047985288119"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"38"},{"VarCharValue":"2.484295925503549"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"42"},{"VarCharValue":"19.34234401593948"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"608"},{"VarCharValue":"302.4402824410751"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"60"},{"VarCharValue":"15.624602223972278"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"163"},{"VarCharValue":"40.07734419064983"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2084"},{"VarCharValue":"1116.441126281631"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"265"},{"VarCharValue":"42.111285681074996"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2746"},{"VarCharValue":"1331.103797412514"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"793"},{"VarCharValue":"899.7892279453553"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"1009"},{"VarCharValue":"314.5917347345357"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"9"},{"VarCharValue":"1.736324243596859"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"138"},{"VarCharValue":"16.978742989549666"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"3962"},{"VarCharValue":"1142.7475492733981"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"645"},{"VarCharValue":"32.578278645032036"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2318"},{"VarCharValue":"623.2125026776582"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"3"},{"VarCharValue":"5.13284347519468"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"1185"},{"VarCharValue":"180.1390182903565"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"185"},{"VarCharValue":"23.07100407329939"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"1878"},{"VarCharValue":"871.1298469625502"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2040"},{"VarCharValue":"2477.0928763271536"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"1890"},{"VarCharValue":"767.7677836398998"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"180"},{"VarCharValue":"5.8702190393590605"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"16"},{"VarCharValue":"9.502174740160234"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"582"},{"VarCharValue":"148.2936171938204"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"43"},{"VarCharValue":"0.5490104497907543"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"146"},{"VarCharValue":"53.54360295399631"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1637"},{"VarCharValue":"325.48081590893406"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"10"},{"VarCharValue":"3.9206516165702716"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"107"},{"VarCharValue":"3.5211208726491376"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"256"},{"VarCharValue":"35.56955661420919"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"378"},{"VarCharValue":"32.019995080608005"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"8"},{"VarCharValue":"1.97810334157003"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"67"},{"VarCharValue":"15.990437875657479"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"46"},{"VarCharValue":"1.4733537379438213"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"558"},{"VarCharValue":"167.4757573613836"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"600"},{"VarCharValue":"144.17293401080536"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"310"},{"VarCharValue":"116.22140792376031"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2525"},{"VarCharValue":"771.6935443992313"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"1953"},{"VarCharValue":"786.232047125957"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"982"},{"VarCharValue":"131.8632127722706"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"61"},{"VarCharValue":"20.4954099719214"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"32"},{"VarCharValue":"10.031914677721756"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"135"},{"VarCharValue":"13.740330034957019"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"23"},{"VarCharValue":"2.5169672134496235"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2394"},{"VarCharValue":"1346.519797990734"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"154"},{"VarCharValue":"19.363249383980406"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"3328"},{"VarCharValue":"1020.0798886206004"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2549"},{"VarCharValue":"995.6968519420876"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"188"},{"VarCharValue":"25.906263389350837"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1150"},{"VarCharValue":"355.77053439464487"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"559"},{"VarCharValue":"23.64192207376712"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"4057"},{"VarCharValue":"1316.3631007991232"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"105"},{"VarCharValue":"24.0128030718153"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"228"},{"VarCharValue":"31.654621678534873"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"1574"},{"VarCharValue":"148.62258218872458"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"278"},{"VarCharValue":"29.63488247348825"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2628"},{"VarCharValue":"940.0973020527662"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"250"},{"VarCharValue":"26.1636602021656"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"166"},{"VarCharValue":"23.127335964127816"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"302"},{"VarCharValue":"288.3431130342162"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2479"},{"VarCharValue":"846.2526944433516"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"1215"},{"VarCharValue":"185.60779122487628"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"315"},{"VarCharValue":"11.626440795680473"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"4092"},{"VarCharValue":"1229.5022606464097"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"17"},{"VarCharValue":"2.4008464602167896"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"400"},{"VarCharValue":"29.827824138510852"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"360"},{"VarCharValue":"106.24666855382974"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"3135"},{"VarCharValue":"1560.0879243965237"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"555"},{"VarCharValue":"25.074621540784612"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"17"},{"VarCharValue":"13.112861065451728"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"120"},{"VarCharValue":"16.95338517710959"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"589"},{"VarCharValue":"669.7251497015387"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"152"},{"VarCharValue":"31.29553692277693"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"1035"},{"VarCharValue":"1172.8038976565053"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"94"},{"VarCharValue":"63.18767204155478"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1713"},{"VarCharValue":"373.6931648354492"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2465"},{"VarCharValue":"1122.6850350805005"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"3505"},{"VarCharValue":"1064.267585546941"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"998"},{"VarCharValue":"257.3885627018034"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"720"},{"VarCharValue":"37.65252058897087"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"153"},{"VarCharValue":"18.33521224348315"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"217"},{"VarCharValue":"22.424074063196205"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"406"},{"VarCharValue":"103.76798153447004"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"81"},{"VarCharValue":"13.084089250698005"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"226"},{"VarCharValue":"40.53338607370442"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"261"},{"VarCharValue":"209.13198446986883"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"842"},{"VarCharValue":"54.95348231669372"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"552"},{"VarCharValue":"89.25203909566675"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"32"},{"VarCharValue":"5.387281011271062"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"650"},{"VarCharValue":"103.79575230432525"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"509"},{"VarCharValue":"39.29882104119357"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"29"},{"VarCharValue":"0.4065109034481205"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"24"},{"VarCharValue":"5.6844340006957195"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1168"},{"VarCharValue":"388.0139157613313"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"277"},{"VarCharValue":"11.547706815316756"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1812"},{"VarCharValue":"324.9012519666997"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"61"},{"VarCharValue":"5.097958063884072"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"13"},{"VarCharValue":"2.0937818030493"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"199"},{"VarCharValue":"5.633346786532074"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"65"},{"VarCharValue":"43.45467164518982"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"3798"},{"VarCharValue":"1162.619401360714"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"688"},{"VarCharValue":"141.4695159415975"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"209"},{"VarCharValue":"26.45783023105234"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"85"},{"VarCharValue":"10.201660879097705"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"159"},{"VarCharValue":"62.889449853092756"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"430"},{"VarCharValue":"70.8752656302544"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"73"},{"VarCharValue":"7.264221659704757"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"26"},{"VarCharValue":"1.100501347996554"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"187"},{"VarCharValue":"63.71622246476944"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"22"},{"VarCharValue":"7.8911683377629736"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2471"},{"VarCharValue":"1543.208942810878"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"54"},{"VarCharValue":"10.266819859273632"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"184"},{"VarCharValue":"9.001823396217809"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"22"},{"VarCharValue":"14.26782003033798"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"926"},{"VarCharValue":"301.6487414485149"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"2069"},{"VarCharValue":"1060.9088022739015"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"62"},{"VarCharValue":"7.019202299454507"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"68"},{"VarCharValue":"42.251289202192105"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"767"},{"VarCharValue":"505.52662315431195"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2263"},{"VarCharValue":"653.2416461034617"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"2000"},{"VarCharValue":"267.8162983152763"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"45"},{"VarCharValue":"6.89313903262445"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"62"},{"VarCharValue":"16.196269675068528"}],[{"VarCharValue":"DwCArmZGF6Y"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"1022"},{"VarCharValue":"172.97653303704843"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"290"},{"VarCharValue":"31.07325334278207"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"740"},{"VarCharValue":"116.43995134619512"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"16"},{"VarCharValue":"5.00098120725848"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1147"},{"VarCharValue":"260.51044313991224"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"172"},{"VarCharValue":"16.840289645486756"}],[{"VarCharValue":"kFU_wus2FqQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"1043"},{"VarCharValue":"141.78964334685705"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"901"},{"VarCharValue":"315.47176975566606"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"91"},{"VarCharValue":"19.24453764670671"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2624"},{"VarCharValue":"1026.1852849699446"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"2330"},{"VarCharValue":"829.5157555861111"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"1052"},{"VarCharValue":"317.8738199448335"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"1803"},{"VarCharValue":"260.2297221584606"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"143"},{"VarCharValue":"24.52747564273292"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"128"},{"VarCharValue":"14.405128265194895"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"166"},{"VarCharValue":"33.314870580308394"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_18_24"},{"VarCharValue":"735"},{"VarCharValue":"474.3536410231114"}],[{"VarCharValue":"W3o7h4NOV9E"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"16"},{"VarCharValue":"1.9238730943592126"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"325"},{"VarCharValue":"37.9162432578282"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1255"},{"VarCharValue":"274.85963357392876"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"1454"},{"VarCharValue":"346.84812086541757"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_45_54"},{"VarCharValue":"273"},{"VarCharValue":"256.9291946859138"}],[{"VarCharValue":"1mYan6ZV2Ss"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"530"},{"VarCharValue":"114.1571728084404"}],[{"VarCharValue":"2Qafit4vreQ"},{"VarCharValue":"FEMALE"},{"VarCharValue":"AGE_25_34"},{"VarCharValue":"3453"},{"VarCharValue":"744.0100120117891"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_65_"},{"VarCharValue":"48"},{"VarCharValue":"39.79104000748412"}],[{"VarCharValue":"h2HAwhyXP5w"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"116"},{"VarCharValue":"39.11293201276597"}],[{"VarCharValue":"W259f-Jk34M"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_13_17"},{"VarCharValue":"405"},{"VarCharValue":"110.30443108621532"}],[{"VarCharValue":"U5iGKVP8l38"},{"VarCharValue":"MALE"},{"VarCharValue":"AGE_35_44"},{"VarCharValue":"508"},{"VarCharValue":"458.75637575996893"}],[{"VarCharValue":"0Gad7y2F1Co"},{"VarCharValue":"GENDER_OTHER"},{"VarCharValue":"AGE_55_64"},{"VarCharValue":"10"},{"VarCharValue":"2.489419845186329"}]]');
                foreach ($listGender as $iGen => $g) {
                    $viewPercentGender = 0;
                    foreach ($datas2 as $index2 => $data2) {
                        if ($index2 > 0) {
                            $videoId = $data2[0]->VarCharValue;
                            $gender = $data2[1]->VarCharValue;
                            $viewPercent = $data2[4]->VarCharValue;
                            if ($video == $videoId) {
                                if ($iGen == 0) {
                                    $totalViewPercent = $totalViewPercent + $viewPercent;
                                }
                                if ($gender == $g) {
                                    $viewPercentGender = $viewPercentGender + $viewPercent;
                                }
                            }
                        }
                    }
                    $listsByGender[] = (object) ["video_id" => $video, "gender" => $g, "view_percent" => $viewPercentGender];
                }
                foreach ($listAge as $iAge => $ag) {
                    $viewPercentAge = 0;
                    foreach ($datas2 as $index2 => $data2) {
                        if ($index2 > 0) {
                            $videoId = $data2[0]->VarCharValue;
                            $age = $data2[2]->VarCharValue;
                            $viewPercent = $data2[4]->VarCharValue;
                            if ($video == $videoId) {
                                if ($age == $ag) {
                                    $viewPercentAge = $viewPercentAge + $viewPercent;
                                }
                            }
                        }
                    }
                    $listsByAge[] = (object) ["video_id" => $video, "age" => $ag, "view_percent" => $viewPercentAge];
                }
                $listTotal[] = (object) ["video_id" => $video, "total" => $totalViewByCountry, "totalViewPercent" => $totalViewPercent];
            }
            Log::info("Genre: " . json_encode($listsByGender));
            Log::info("Age: " . json_encode($listsByAge));
            Log::info(json_encode($listTotal));
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=report_geo_' . $startDate . '_' . $endDate . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];
            $colGender = implode(",", $listGender);
            $colAge = implode(",", $listAge);
            $csv = "VideoId,Location,Views,$colGender,$colAge\n";
            foreach ($listTotal as $total) {
                $videoId = $total->video_id;
                $genderAge = "";
                foreach ($listsByGender as $gender) {
                    if ($videoId == $gender->video_id) {
                        $genderAge .= round(($gender->view_percent / $total->totalViewPercent * 100), 2) . "%,";
                    }
                }
                foreach ($listsByAge as $age) {
                    if ($videoId == $age->video_id) {
                        $genderAge .= round(($age->view_percent / $total->totalViewPercent * 100), 2) . "%,";
                    }
                }
                $csv .= "$videoId,,,$genderAge\n";
                foreach ($listsByCountry as $list) {
                    if ($videoId == $list->video_id) {
                        $country = $list->country;
                        $csv .= ",$country," . round(($list->views / $total->total * 100), 2) . "%\n";
                    }
                }
            }
            $fileName = "report_geo_$startDate" . "_" . "$endDate.csv";
            $file = storage_path("campaign/$fileName");
            $csv_handler = fopen($file, 'w');
            fwrite($csv_handler, $csv);
            fclose($csv_handler);
//            return array("status"=>"success","message"=>$fileName);
            return response()->download($file, $fileName, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    //scan lai view promo campain de ket thuc
    public function scanViewPromoCampaign(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.scanViewPromoCampaign|request=' . json_encode($request->all()));
        if (in_array("20", explode(",", $user->role)) || in_array("1", explode(",", $user->role))) {
            if ($request->id == 0) {
                $promos = CampaignStatistics::whereIn("type", [1, 2, 4, 5])->where("status", 1)->get(["id"]);
                $proId = [];
                foreach ($promos as $pro) {
                    $proId[] = $pro->id;
                }
                $campaigns = Campaign::whereIn("campaign_id", $proId)->where("status_confirm", 3)->get();
            } else {
                $campaigns = Campaign::where("campaign_id", $request->id)->whereIn("video_type", [1, 2, 5, 6])->where("status_confirm", 3)->get();
            }
        } else {
            $campaigns = Campaign::where("campaign_id", $request->id)->where("username", $user->user_name)->where("status_confirm", 3)->get();
        }

        $total = count($campaigns);
        Log::info("scanViewPromoCampaign:" . $total);
        $i = 0;
//        foreach ($campaigns as $campaign) {
//            $i++;
//            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($campaign->video_id);
//            if ($videoInfo["status"] == 0) {
//                for ($t = 0; $t < 5; $t++) {
//                    error_log("scanViewPromoCampaign Retry $campaign->video_id");
//                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($campaign->video_id);
//                    if ($videoInfo["status"] == 1) {
//                        break;
//                    }
//                }
//            }
//            if ($videoInfo["status"] == 1) {
//                $campaign->update_time = gmdate("m/d/Y H:i:s", time() + 7 * 3600);
//                $campaign->views = $videoInfo["view"];
//                $campaign->status = $videoInfo["status"];
//                $campaign->save();
//            } else {
//                $campaign->status = $videoInfo["status"];
//                $campaign->save();
//            }
//            error_log("scanViewPromoCampaign $i/$total $campaign->video_id status=" . $videoInfo["status"] . " views=" . $videoInfo["view"]);
//        }
        $listVideoId = [];
        $listVideoIdAlive = [];
        $count = 0;
        foreach ($campaigns as $index => $campaign) {
//            $campaign->views =0;
//            $campaign->save();
            $listVideoId[] = trim($campaign->video_id);
//            Log::info(count($listVideoId) . " - " . $index . " - " . ($total - 1));
            if (count($listVideoId) >= 45 || $index == ($total - 1)) {
                $jData = YoutubeHelper::getStatics(implode(",", $listVideoId));
                error_log("|$user->user_name|CampaignController.scanViewPromoCampaign|getStatics=" . count($listVideoId) . '/' . $index);
                if (isset($jData)) {
                    $items = !empty($jData->items) ? $jData->items : [];
//                    error_log("|$user->user_name|CampaignController.scanViewPromoCampaign|count=" .count($items));
                    foreach ($items as $item) {
                        $vidID = $item->id;
                        $listVideoIdAlive[] = $vidID;
                        $views = !empty($item->statistics->viewCount) ? $item->statistics->viewCount : 0;
                        $likes = !empty($item->statistics->likeCount) ? $item->statistics->likeCount : 0;
                        $date = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                        if ($views > 0) {
                            Campaign::where("video_id", $vidID)->update(["views" => $views, "like" => $likes, "update_time" => $date]);
                        }
                        $count++;
                    }
                } else {
                    error_log("|$user->user_name|CampaignController.scanViewPromoCampaign|not found result");
                }
                $listvideoDead = array_diff($listVideoId, $listVideoIdAlive);
                Campaign::whereIn("video_id", $listvideoDead)->update(["views_real" => 0, "status" => 0]);
                $listVideoId = [];
                $listVideoIdAlive = [];
            }
        }
        DB::statement("UPDATE athena_tableau a INNER JOIN campaign b ON a.video_id= b.video_id SET a.views_real= b.views");
        $x = RequestHelper::fetchWithoutResponseURL("http://automusic.win/caculateCampaign");
        return array("status" => "success", "message" => "Successfully scanned $count videos");
    }

    //report promos theo user
    public function reportCampaignUser(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.reportCampaignUser|request=' . json_encode($request->all()));
        if (in_array("20", explode(",", $user->role)) || in_array("1", explode(",", $user->role))) {

            $condition = "";
        } else {
            $condition = "and username ='$user->user_name'";
        }
        $startDate = '20220101';
        $endDate = date("Ymd", time());
        if (isset($request->start)) {
            $startDate = $request->start;
        }
        if (isset($request->end)) {
            $endDate = $request->end;
        }
        $campaign = CampaignStatistics::where("id", $request->id)->first(["campaign_name"]);
        $datas = DB::select("select username, video_type,
                            CASE WHEN video_type ='2' THEN 'Lyric'
                            WHEN video_type ='5' THEN 'Mix'
                            WHEN video_type ='6' THEN 'Short' 
                            END as video_type_text ,
                            count(video_id) as videos,
                            sum(views) as views from campaign where campaign_id = $request->id
                            and video_type in (2,5,6) and status_confirm = 3 $condition group by username,video_type
                            order by username
                            ");
        $dataBulks = DB::select("select a.username,video_type,sum(b.views) as bulk_views,sum(b.views_real_daily) as real_views from (select username,video_id,video_type from campaign where campaign_id = $request->id and status_confirm =3 and video_type in (2,5,6) and views <> 0) a
                                    left join (select video_id,views,views_real_daily from athena_promo_sync where date >=$startDate and date <=$endDate) b
                                    on a.video_id = b.video_id
                                    group by a.username,a.video_type");
        foreach ($datas as $data) {
            foreach ($dataBulks as $bulk) {
                if ($data->username == $bulk->username && $data->video_type == $bulk->video_type) {
                    $data->bulk_views = $bulk->bulk_views;
                    $data->real_views = $bulk->real_views;
                }
            }
        }
        return array("status" => "success", "report" => $datas, "name" => $campaign->campaign_name);
    }

    //báo cáo revenue theo tổng tháng
    public function reportCampaignRevenue(Request $request) {
        $user = Auth::user();
        $uName = "system";
        if ($user) {
            $uName = $user->user_name;
        }
        Log::info("$uName|CampaignController.reportCampaignRevenue|request=" . json_encode($request->all()));
        $status = 1;
        $datas = DB::select("select period,count(id) as count,sum(money) as amount,sum(amount_paid) as paid from campaign_statistics where period is not null group by period order by period desc");
        $campaigns = CampaignStatistics::whereNotNull("period")->whereIn("type", [1, 5])->orderBy("period", "desc")->get();
        $listPays = CampaignClaimRevStatus::where("rev_type", 1)->get();
        foreach ($datas as $data) {
            $data->is_paid = 0;
            $data->is_show = 0;
            foreach ($listPays as $pay) {
                if ($pay->period == $data->period) {
                    $data->is_paid = $pay->status;
                    $data->is_show = $pay->status_show;
                    break;
                }
            }
            $data->period_text = date("M-Y", strtotime($data->period . "01"));
            $data->debt = $data->amount - $data->paid;
            $totalSpent = 0;
            $data->bassteam_money = 0;
            $data->seller_money = 0;
            $data->submission_money = 0;
            $data->amount_submission = 0;

            foreach ($campaigns as $campaign) {
                $submission_money = 0;
                $bassteam_money = 0;
                $seller_money = 0;
                $bassPercent = 0;
                $sellerPercent = 0;
                $profit = $campaign->money;
                if ($campaign->period == $data->period) {
                    //tính chi phí theo từng tháng
                    if ($campaign->official != null) {
                        $temp = json_decode($campaign->official);
                        $totalSpent += ($temp->crypto_usd + $temp->adsense_usd + $temp->facebook_usd);
                        $profit = $campaign->money - ($temp->crypto_usd + $temp->adsense_usd + $temp->facebook_usd);
                    }
                    if ($campaign->type == 1) {
                        $bassPercent = 5;
                        $id = 0;
                        $current = 0;
                        if ($campaign->views_detail != null) {
                            $current = !empty(json_decode($campaign->views_detail)->$id) ? json_decode($campaign->views_detail)->$id : 0;
                        }
                        $totalTarget = Utils::shortNumber2Number($campaign->target);
                        if ($totalTarget > 0) {
                            $percent = round($current / $totalTarget * 100, 2);
                        } else {
                            $percent = 100;
                        }
                        if ($percent >= 80) {
                            $bassPercent = 10;
                        }
                        if ($campaign->bass_percent != 0) {
                            $bassPercent = $campaign->bass_percent;
                            if ($campaign->bass_percent == -1) {
                                $bassPercent = 0;
                            }
                        }
//                        $bassteam_money = ($bassPercent * $profit / 100);
                        $bassteam_money = ($bassPercent * $campaign->money / 100);
                        $data->bassteam_money += $bassteam_money;

                        $sellerPercent = 5;
                        if ($campaign->seller != "JAMES" && $campaign->seller != null) {
                            $sellerPercent = 10;
                        }
                        $seller_money = ($sellerPercent * $profit / 100);
                        $data->seller_money += $seller_money;
                    } elseif ($campaign->type == 5) {
                        $submissionPercent = 30;
                        if ($campaign->bass_percent != 0) {
                            $submissionPercent = $campaign->bass_percent;
                        }
                        $submission_money = $submissionPercent * $profit / 100;
                        $data->submission_money += $submission_money;
                        $data->amount_submission += $campaign->money;
                    }
//                    $t = $bassteam_money + $seller_money + $submission_money;
//                    Log::info("$campaign->period $campaign->id $campaign->type $campaign->seller money=$campaign->money - profit=$profit - bassPer=$bassPercent - bassMoney=$bassteam_money - sellPer=$sellerPercent - sellMoney=$seller_money - sub=$submission_money 0 payment=$t");
//                    Log::info("$campaign->period $campaign->id $campaign->type $t");
                }
            }
            $data->spent = $totalSpent;
            $data->payment = $data->bassteam_money + $data->seller_money + $data->submission_money;
            $data->curr_profit = ($data->paid - $data->spent - $data->payment);
            $data->last_profit = $data->amount - $data->spent - $data->payment;
        }
        return array("status" => "success", "report" => $datas, "cam" => $campaigns);
    }

    //báo cáo user theo tháng
    public function getReportPeriodRevDetail(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.getReportPeriodRevDetail|request=' . json_encode($request->all()));
        $status = 1;
//        $userViews = DB::select("select username,sum(case when video_type = 5 then round(views/2) else views end) as views_money, SUM(IF(video_type = 2, views, 0)) as views_lyric,SUM(IF(video_type = 5, views, 0)) as views_mix,SUM(IF(video_type = 6, views, 0)) as views_short from campaign where video_type <> 1 and status_confirm = 3 and campaign_id in ( select id from campaign_statistics where period=$request->period and status = $status) group by username");
        $userCampaignViews = DB::select("select username,campaign_id,sum(case when video_type = 5 then round(views/5) else views end) as views_money,  sum(ads_views) as ads_views,SUM(IF(video_type = 2, views, 0)) as views_lyric,SUM(IF(video_type = 5, views, 0)) as views_mix,SUM(IF(video_type = 6, views, 0)) as views_short from campaign where video_type <> 1 and status_confirm = 3 and campaign_id in ( select id from campaign_statistics where period= ? and type in (1,5) ) group by username, campaign_id", [$request->period]);
        $campaignViews = DB::select("select campaign_id,sum(case when video_type = 5 then round(views/5) else views end) as views_money, sum(ads_views) as ads_views, SUM(IF(video_type = 2, views, 0)) as views_lyric,SUM(IF(video_type = 5, views, 0)) as views_mix,SUM(IF(video_type = 6, views, 0)) as views_short from campaign where video_type <> 1 and status_confirm = 3 and campaign_id in ( select id from campaign_statistics where period= ?  and type in (1,5)) group by campaign_id", [$request->period]);
        $campaigns = CampaignStatistics::where("period", $request->period)->whereIn("type", [1, 5])->get();
        $listUsers = User::where("status", 1)->whereNotIn("user_name", ['truongpv', 'hoadev', 'sangboom'])->whereRaw(DB::raw("(role like '%16%' or role like '%24%' or role like '%25%')"))->get(["user_name", DB::raw("CASE WHEN role like '%16%' THEN 'BASS' WHEN role like '%24%' THEN 'DEVTOP' WHEN role like '%25%' THEN 'SELLER' END as role")]);
//        Log::info(DB::getQueryLog());
        $totalProfit = 0;
        $totalBass = 0;
        $totalDev = 0;
        $totalSeller = 0;
        $totalSubmission = 0;
        foreach ($campaigns as $campaign) {
            $adsViews = 0;
            foreach ($campaignViews as $cView) {
                if ($cView->campaign_id == $campaign->id) {
                    $adsViews = $cView->ads_views;
                    break;
                }
            }
            $bassSubmissionPercent = $campaign->bass_percent != 0 ? $campaign->bass_percent : 30;
            $campaign->period_text = date("M-Y", strtotime($request->period . "01"));
            $spent = 0;
            if ($campaign->official != null) {
                $temp = json_decode($campaign->official);
                $spent = ($temp->crypto_usd + $temp->adsense_usd + $temp->facebook_usd);
            }
            $campaign->profit = $campaign->money - $spent;
            $totalProfit += $campaign->profit;
            //tính tiền cho bassteam
            $bassteamMoney = 0;
            if ($campaign->type == 1) {
                $bassPercent = 5;
                $id = 0;
                $current = 0;
                if ($campaign->views_detail != null) {
                    $current = !empty(json_decode($campaign->views_detail)->$id) ? json_decode($campaign->views_detail)->$id : 0;
                }
                $current = $current - $adsViews;
                $totalTarget = Utils::shortNumber2Number($campaign->target);
                if ($totalTarget > 0) {
                    $percent = round($current / $totalTarget * 100, 2);
                } else {
                    $percent = 100;
                }
                if ($percent >= 80) {
                    $bassPercent = 10;
                }
                //2023/02/01 không tính theo lợi nhuận mà tính theo doanh tu với promo
//                $bassteamMoney = ($bassPercent * $campaign->profit / 100);
                if ($campaign->bass_percent != 0) {
                    $bassPercent = $campaign->bass_percent;
                    //2024/01/03 set = -1 thì kho pay tiền
                    if ($campaign->bass_percent == -1) {
                        $bassPercent = 0;
                    }
                }
                $bassteamMoney = ($bassPercent * $campaign->money / 100);
                $campaign->bassteam_money = (95 * $bassteamMoney / 100);
//                Log::info("bassteam_money $campaign->id $campaign->bassteam_money");
                $totalBass += $campaign->bassteam_money;

                //tính tiền cho darrel = 5% nếu ko trực tiếp bán or 10% profit nếu trực tiếp bán
                $sellerPercent = 5;
                if ($campaign->seller != "JAMES" && $campaign->seller != "360PROMO" && $campaign->seller != null) {
                    $sellerPercent = 10;
                }
                $campaign->seller_money = ($sellerPercent * $campaign->profit / 100);
                $totalSeller += $campaign->seller_money;
                //tính tiền cho devteam = 5% bassteam
                $campaign->devteam_money = (5 * $bassteamMoney / 100);
                $totalDev += $campaign->devteam_money;
            } elseif ($campaign->type == 5) {
//                $task = Tasks::where("campaign_id", $campaign->id)->where("type", 5)->first();
//                $campaign->submission_owner = "";
//                if ($task) {
//                    $campaign->submission_owner = $task->username;
//                } else {
//                    $accInfo = AccountInfo::where("chanel_name", $campaign->channel_name)->first();
//                    if ($accInfo) {
//                        $pos = strripos($accInfo->user_name, '_');
//                        $temp = substr($accInfo->user_name, 0, $pos);
//                        $campaign->submission_owner = $temp;
//                    }
//                }
                $campaign->submission_owner = [];
                if ($campaign->submission_info != null) {
                    $campaign->submission_owner = json_decode($campaign->submission_info);
                }
                $totalSubmission += $bassSubmissionPercent * $campaign->profit / 100;
            }


            $payment = [];
            $m = 0;
            foreach ($userCampaignViews as $userCampaignView) {

                if ($campaign->id == $userCampaignView->campaign_id) {
                    $money = 0;
                    foreach ($campaignViews as $camView) {
                        if ($userCampaignView->campaign_id == $camView->campaign_id) {
                            if ($camView->views_money > 0) {
                                $money = $userCampaignView->views_money / $camView->views_money * $campaign->bassteam_money;
                            }
                        }
                    }
//                    Log::info("$userCampaignView->campaign_id $userCampaignView->username ");
                    $payment[] = ["us" => $userCampaignView->username, "campaign_id" => $campaign->id, "money" => $money, "views_money" => $userCampaignView->views_money, "campaign_views" => $camView->views_money, "views_mix" => $userCampaignView->views_mix, "views_lyric" => $userCampaignView->views_lyric, "views_short" => $userCampaignView->views_short];
                    $m += $money;
                }
            }
            $campaign->payment = $payment;
//            Log::info("$campaign->id $m");
        }
//        $totalViewMoneyUser = 0;
//        foreach ($userViews as $userView) {
//            $totalViewMoneyUser += $userView->views_money;
//        }
        foreach ($listUsers as $us) {
            $us->kpi = 0;
            $us->period = $request->period;
            if ($us->role == "BASS") {
                foreach ($campaigns as $campaign) {
                    //% của Submission
                    $bassSubmissionPercent = $campaign->bass_percent != 0 ? $campaign->bass_percent : 30;
//                    Log::info($campaign->id);
                    if ($campaign->type == 1) {
                        $payments = $campaign->payment;
                        foreach ($payments as $payment) {
                            if (!empty($payment['us'])) {
                                if ($payment['us'] == $us->user_name) {
                                    $us->kpi += $payment['money'];
                                }
                            }
                        }
                    } elseif ($campaign->type == 5) {
                        //30% cho bassteam với submission campaign
//                        if ($campaign->submission_owner == $us->user_name) {
//                            $us->kpi += $bassSubmissionPercent * $campaign->profit / 100;
//                        }
                        foreach ($campaign->submission_owner as $own) {
                            if ($own->user == $us->user_name) {
                                $us->kpi += ($campaign->money != 0 ? ($own->money / $campaign->money) : 0) * ($bassSubmissionPercent * $campaign->profit / 100);
                            }
                        }
                    }
                }
                $us->kpi = round($us->kpi, 1);
            }

            if ($us->role == "SELLER") {
                $us->kpi = round($totalSeller, 1);
            }
            //devtop dc 50% devteam, devteam dc 5% bass team
            if ($us->role == "DEVTOP") {
                $us->kpi = round(($totalDev / 2), 1);
            }
        }

        Log::info("totalProfit=$totalProfit totalBass=$totalBass totalDev=$totalDev totalSeller=$totalSeller totalSubmission=$totalSubmission");
        return array("status" => "success", "campaigns" => $campaigns, "users" => $listUsers);
    }

    //báo cáo chi tiết theo user
    public function getReportUserRevDetail(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.getReportUserRevDetail|request=' . json_encode($request->all()));
        $status = 1;
        $userCampaignViews = DB::select("select username,campaign_id,sum(case when video_type = 5 then round(views/5) else views end) as views_money, sum(ads_views) as ads_views, SUM(IF(video_type = 2, views, 0)) as views_lyric,SUM(IF(video_type = 5, views, 0)) as views_mix,SUM(IF(video_type = 6, views, 0)) as views_short from campaign where video_type <> 1 and status_confirm = 3 and username= ? and campaign_id in ( select id from campaign_statistics where period= ?  and type in (1,5)) group by username, campaign_id", [$request->username, $request->period]);
        $campaignViews = DB::select("select campaign_id,sum(case when video_type = 5 then round(views/5) else views end) as views_money, sum(ads_views) as ads_views, SUM(IF(video_type = 2, views, 0)) as views_lyric,SUM(IF(video_type = 5, views, 0)) as views_mix,SUM(IF(video_type = 6, views, 0)) as views_short from campaign where video_type <> 1 and status_confirm = 3 and campaign_id in ( select id from campaign_statistics where period= ?  and type in (1,5)) group by campaign_id", [$request->period]);
        $campaigns = CampaignStatistics::where("period", $request->period)->get();
        $camType = ["1" => "PROMOS", "2" => "CLAIM", "4" => "REVSHARE", "5" => "SUBMISSION"];
//        Log::info(DB::getQueryLog());
        $totalProfit = 0;
        $totalBass = 0;

        foreach ($campaigns as $campaign) {
            $adsViews = 0;
            foreach ($campaignViews as $cView) {
                if ($cView->campaign_id == $campaign->id) {
                    $adsViews = $cView->ads_views;
                    break;
                }
            }

            $campaign->period_text = date("M-Y", strtotime($request->period . "01"));
            $spent = 0;
            if ($campaign->official != null) {
                $temp = json_decode($campaign->official);
                $spent = ($temp->crypto_usd + $temp->adsense_usd + $temp->facebook_usd);
            }
            $campaign->profit = $campaign->money - $spent;
            $totalProfit += $campaign->profit;
            //tính tiền cho bassteam
            $bassteamMoney = 0;
            if ($campaign->type == 1) {
                $bassPercent = 5;
                $id = 0;
                $current = 0;
                if ($campaign->views_detail != null) {
                    $current = !empty(json_decode($campaign->views_detail)->$id) ? json_decode($campaign->views_detail)->$id : 0;
                }
                $current = $current - $adsViews;
                $totalTarget = Utils::shortNumber2Number($campaign->target);
                if ($totalTarget > 0) {
                    $percent = round($current / $totalTarget * 100, 2);
                } else {
                    $percent = 100;
                }
                if ($percent >= 80) {
                    $bassPercent = 10;
                }
                if ($campaign->bass_percent != 0) {
                    $bassPercent = $campaign->bass_percent;
                    //2024/01/03 set = -1 thì kho pay tiền
                    if ($campaign->bass_percent == -1) {
                        $bassPercent = 0;
                    }
                }
//                $bassteamMoney = ($bassPercent * $campaign->profit / 100);
                $bassteamMoney = ($bassPercent * $campaign->money / 100);
                $campaign->bassteam_money = (95 * $bassteamMoney / 100);
                $totalBass += $campaign->bassteam_money;
            } elseif ($campaign->type == 5) {
//                $task = Tasks::where("campaign_id", $campaign->id)->where("type", 5)->first();
//                $campaign->submission_owner = "";
//                if ($task) {
//                    $campaign->submission_owner = $task->username;
//                } else {
//                    $accInfo = AccountInfo::where("chanel_name", $campaign->channel_name)->first();
//                    if ($accInfo) {
//                        $pos = strripos($accInfo->user_name, '_');
//                        $temp = substr($accInfo->user_name, 0, $pos);
//                        $campaign->submission_owner = $temp;
//                    }
//                }
                $campaign->submission_owner = [];
                if ($campaign->submission_info != null) {
                    $campaign->submission_owner = json_decode($campaign->submission_info);
                }
            }

            foreach ($campaignViews as $campaignView) {
                if ($campaign->id == $campaignView->campaign_id) {
                    $campaign->v_money = $campaignView->views_money;
                    $campaign->v_mix = $campaignView->views_mix;
                    $campaign->v_lyric = $campaignView->views_lyric;
                    $campaign->v_short = $campaignView->views_short;
                }
            }
        }
        $camTypeMoney = (object) ["PROMOS" => 0, "SUBMISSION" => 0];
//        foreach ($userCampaignViews as $usCampaignView) {
//            foreach ($campaigns as $campaign) {
////                $bassSubmissionPercent = $campaign->bass_percent != 0 ? $campaign->bass_percent : 30;
//                if ($usCampaignView->campaign_id == $campaign->id && $campaign->type == 1) {
////                    if ($campaign->type == 1) {
//                    $usCampaignView->money = round($usCampaignView->views_money / $campaign->v_money * $campaign->bassteam_money, 1);
//                    $camTypeMoney->PROMOS += ($usCampaignView->views_money / $campaign->v_money * $campaign->bassteam_money);
////                    }
////                    elseif ($campaign->type == 5) {
////                        $usCampaignView->money = 0;
////                        foreach ($campaign->submission_owner as $own) {
////                            if ($own->user == $request->username) {
////                                $mo = ($campaign->money != 0 ? ($own->money / $campaign->money) : 0) * ($bassSubmissionPercent * $campaign->profit / 100);
////                                $usCampaignView->money = round($mo, 1);
////                                $camTypeMoney->SUBMISSION += $mo;
////                            }
////                        }
////                    }
//                    $usCampaignView->views_money_total = $campaign->v_money;
//                    $usCampaignView->genre = $campaign->genre;
//                    $usCampaignView->type = $camType["$campaign->type"];
//                }
//            }
//        }
        $detailCampaignByUser = [];
        //2024/07/15 thêm dữ liệu submission
        foreach ($campaigns as $campaign) {
            //thêm views nếu có
            $views_money = 0;
            $ads_views = 0;
            $views_lyric = 0;
            $views_mix = 0;
            $views_short = 0;
            $views_money_total = 0;
            foreach ($userCampaignViews as $usCampaignView) {
                if ($usCampaignView->campaign_id == $campaign->id) {
                    $views_money = $usCampaignView->views_money;
                    $ads_views = $usCampaignView->ads_views;
                    $views_lyric = $usCampaignView->views_lyric;
                    $views_mix = $usCampaignView->views_mix;
                    $views_short = $usCampaignView->views_short;
                    $views_money_total = $campaign->v_money;
                    break;
                }
            }
            $moneyForBass = 0;
            if ($campaign->type == 1) {
                if ($campaign->v_money != 0) {
                    $moneyForBass = round($views_money / $campaign->v_money * $campaign->bassteam_money, 1);
                    $camTypeMoney->PROMOS += ($views_money / $campaign->v_money * $campaign->bassteam_money);
                }
            } elseif ($campaign->type == 5) {
                $bassSubmissionPercent = $campaign->bass_percent != 0 ? $campaign->bass_percent : 30;
                foreach ($campaign->submission_owner as $own) {
                    if ($own->user == $request->username) {
                        $mo = ($campaign->money != 0 ? ($own->money / $campaign->money) : 0) * ($bassSubmissionPercent * $campaign->profit / 100);
//                        $moneyForBass = round($mo, 1);
                        $moneyForBass += $mo;
                        $camTypeMoney->SUBMISSION += $mo;
                    }
                }
            }
            if ($moneyForBass > 0) {
                $detailCampaignByUser[] = (object) [
                            "username" => $request->username,
                            "campaign_id" => $campaign->id,
                            "views_money" => $views_money,
                            "ads_views" => $ads_views,
                            "views_lyric" => $views_lyric,
                            "views_mix" => $views_mix,
                            "views_short" => $views_short,
                            "views_money_total" => $views_money_total,
                            "money" => round($moneyForBass,1),
                            "genre" => $campaign->genre,
                            "type" => $camType["$campaign->type"],
                ];
            }
        }
        return array("status" => "success", "campaigns" => $campaigns, "users" => $detailCampaignByUser, "camTypeMoney" => $camTypeMoney);
    }

    //download report promos theo ngày
    public function downloadReportCampaign(Request $request) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30000);
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.downloadReportCampaign|request=' . json_encode($request->all()));
        try {
            $startDate = '20220101';
            if (isset($request->start)) {
                $startDate = $request->start;
            }

            $endDate = date("Ymd", time());
            if (isset($request->end)) {
                $endDate = $request->end;
            }
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=campaign_' . $startDate . '_' . $endDate . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];
            $campaigns = CampaignStatistics::where("status", 1)->whereIn("type", [1, 4])->get(["id", "campaign_name", "genre", "campaign_start_date as date_start"]);

            foreach ($campaigns as $campaign) {
                $temps = DB::select("select sum(views) as views,max(date) as max_date from athena_promo_sync
                                            where date >= '$startDate' and date<= '$endDate' and video_id in (select video_id from campaign where campaign_id = $campaign->id
                                            and status_confirm = 3)");
                $campaign->date_start = gmdate('Y/m/d', strtotime($campaign->date_start));
                if (count($temps) > 0) {
                    $campaign->min_date = Utils::convertToViewDate($startDate);
                    $campaign->max_date = Utils::convertToViewDate($temps[0]->max_date);
                    $campaign->views = $temps[0]->views;
                }
            }

            $campaigns = $campaigns->toArray();
            if (count($campaigns) > 0) {
                $title = array('Id', 'Campaign Name', 'Genre', 'Start Date', 'Report Date From', 'Report Date To', 'Total Views');
                array_unshift($campaigns, $title);
                $callback = function() use ($campaigns) {
                    $FH = fopen('php://output', 'w');
                    foreach ($campaigns as $row) {
                        fputcsv($FH, $row);
                    }
                    fclose($FH);
                };
                return response()->stream($callback, 200, $headers);
            }
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    //download report views revshare client
    public function downloadReportClientCampaign(Request $request) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30000);
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.downloadReportClientCampaign|request=' . json_encode($request->all()));
        try {
            $startDate = '20220101';
            if (isset($request->start)) {
                $startDate = $request->start;
            }

            $endDate = date("Ymd", time());
            if (isset($request->end)) {
                $endDate = $request->end;
            }
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=campaign_' . $startDate . '_' . $endDate . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];

//            $campaigns = DB::select("select upper(revshare_client) as client,sum(views) as views from campaign_statistics where type =4 and status =1 and revshare_client is not null group by revshare_client");
            $campaigns = DB::table('campaign_statistics')->whereNotNull("revshare_client")->where("type", 4)->where("status", 1)
                    ->select(DB::raw('upper(revshare_client) as client'), DB::raw('sum(views) as views'))
                    ->groupBy('revshare_client')
                    ->get();
            $csv = "Revshare Client,Views\n";
            foreach ($campaigns as $campaign) {
                $csv .= "$campaign->client,$campaign->views\n";
            }

            $fileName = "report_client.csv";
            $file = storage_path("campaign/$fileName");
            $csv_handler = fopen($file, 'w');
            fwrite($csv_handler, $csv);
            fclose($csv_handler);
//            return array("status"=>"success","message"=>$fileName);
            return response()->download($file, $fileName, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    //download report views revshare client
    public function downloadListvideosRevshareClient(Request $request) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30000);
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.downloadListvideosRevshareClient|request=' . json_encode($request->all()));
        try {
            $startDate = '20220101';
            if (isset($request->start)) {
                $startDate = $request->start;
            }

            $endDate = date("Ymd", time());
            if (isset($request->end)) {
                $endDate = $request->end;
            }
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=campaign_' . $startDate . '_' . $endDate . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];

            $campaigns = DB::select("select distinct video_id,views from campaign where status_confirm  =3 and video_type <> 1 and campaign_id in(select id from campaign_statistics where revshare_client = '$request->rev_client' and type =4 and status =1 ) order by views desc");

            $title = "List video of Revshare Client," . strtoupper($request->rev_client) . ",Total View:";
            $csv = "Video Id,Views\n";
            $views = 0;
            foreach ($campaigns as $campaign) {
                $csv .= "$campaign->video_id,$campaign->views\n";
                $views = $views + $campaign->views;
            }
            $title .= "$views\n";
            $csv = $title . $csv;
            $fileName = "report_videos_client_$request->rev_client.csv";
            $file = storage_path("campaign/$fileName");
            $csv_handler = fopen($file, 'w');
            fwrite($csv_handler, $csv);
            fclose($csv_handler);
//            return array("status"=>"success","message"=>$fileName);
            return response()->download($file, $fileName, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    public function submitGenreTarget(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.submitGenreTarget|request=' . json_encode($request->all()));
        if (in_array("20", explode(",", $user->role)) || in_array("1", explode(",", $user->role))) {

            $data = Genre::find($request->id);
            if ($data) {
                $data->target = Utils::shortNumber2Number($request->genre_target);
                $data->save();
            } else {
                return array("status" => "error", "message" => "Not found info");
            }
        } else {
            return array("status" => "error", "message" => "You do not have permission");
        }


        return array("status" => "success", "message" => "success");
    }

    public function cutPtmomosMusic(Request $request) {
        //cut 30s
        $datas = CampaignStatistics::whereIn("status", [1, 2])->whereIn("type", [1, 2, 4, 5])->whereNull("audio_url_cut")->whereNotNull("audio_url")->get();
        $path = "/home/automusic.win/public_html/public/";
        foreach ($datas as $data) {
            //download về autolusic nếu là link ngoài
            if (!Utils::containString($data->audio_url, "automusic.win")) {
                $fileRoot = $data->audio_url;
                $fileCut = ($data->type == 1 ? "promos" : "claims") . "/music/cut/$data->id" . "_" . time() . ".mp3";
                $fileOut = $path . $fileCut;
            } else {
                $audioFile = str_replace("http://automusic.win/", "", $data->audio_url);
                $fileCut = str_replace("/download", "/cut", $audioFile);
                $fileCut = str_replace(".wav", ".mp3", $fileCut);
                $fileCut = str_replace(".m4a", ".mp3", $fileCut);
                $fileRoot = $path . $audioFile;
                $fileOut = $path . $fileCut;
            }
            $cmd = "sudo ffmpeg -i $fileRoot -t 0:0:30 -b:a 128000 -ar 44100 -c:a mp3 $fileOut";
            shell_exec($cmd);
            Log::info("cutPtmomosMusic-$data->id $cmd");
            $data->audio_url_cut = "http://automusic.win/$fileCut";
            $data->save();
        }
        //convert wav -> mp3
//        $datas2 = CampaignStatistics::whereIn("status", [1,2])->whereIn("type", [1, 2, 4, 5])->where("audio_url", "like", "%.wav%")->orWhere("audio_url", "like", "%.m4a%")->get(["id", "audio_url"]);
        foreach ($datas as $data2) {
            //chỉ xử lỹ những bài đc upload lên automusic
            if (Utils::containString($data->audio_url, "automusic.win")) {
                $audioFile2 = str_replace("http://automusic.win/", "", $data2->audio_url);
                $path2 = "/home/automusic.win/public_html/public/";
//            $fileConvert = str_replace("/download", "/cut", $audioFile);
                $fileConvert = str_replace(".WAV", ".mp3", $audioFile2);
                $fileConvert = str_replace(".wav", ".mp3", $fileConvert);
                $fileConvert = str_replace(".m4a", ".mp3", $fileConvert);
                $fileConvert = str_replace(".M4A", ".mp3", $fileConvert);
                $cmd2 = 'sudo ffmpeg -i "' . $path2 . $audioFile2 . '" -b:a 128000 -ar 44100 -c:a mp3 "' . $path2 . $fileConvert . '"';
                shell_exec($cmd2);
                Log::info("cutPtmomosMusic-$data2->id $cmd2");
//            unlink("$path$audioFile");
                $data2->audio_url = "http://automusic.win/$fileConvert";
                $data2->save();
            }
        }
        return array("cut" => count($datas), "convert" => count($datas));
    }

    public function getDeezerArtistId($deezerId) {
//        Log::info('|CampaignController.getDeezerArtistId|deezer_id=' . $deezerId);
        if (!isset($deezerId)) {
            return "{}";
        }
        $data = CampaignStatistics::where("deezer_id", $deezerId)->first(["deezer_artist_id", "deezer_id"]);
        if (!$data) {
            return "{}";
        }
        return json_encode($data);
    }

    public function scanCampaign2(Request $request) {
        $startTime = time();
        $threadId = 0;
        if (isset($request->threadId)) {
            $threadId = $request->threadId;
        }
        if ($threadId == 0) {
            ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Start scanCampaign2");
        }
        $listActiveCam = CampaignStatistics::whereIn("status", [1, 4])->whereIn("type", [1, 2, 4, 5])->get(["id"]);
        $arrayActiveCam = [];
        foreach ($listActiveCam as $ac) {
            $arrayActiveCam[] = $ac->id;
        }
        $numberThreadInit = 1;
        if (isset($request->video_id)) {
            $datas = Campaign::whereIn("video_id", $request->video_id)->where("status_use", 1)->where("status", 1)->get();
        } else {
//            $datas = Campaign::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status_use", 1)->whereIn("campaign_id", $arrayActiveCam)->where("status_confirm", 3)->get();
            $datas = DB::select("SELECT DISTINCT video_id,views,views_real,video_title,is_bommix FROM `campaign` WHERE id % $numberThreadInit = $threadId and status_confirm in (1,3) and status_use = 1 and campaign_id in (select id from campaign_statistics where status in(1,4))");
        }
        if (isset($request->date)) {
            $date = $request->date;
        } else {
            $date = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        }
        $scanDate = gmdate("Ymd", time() + 7 * 3600);
        $total = count($datas);
        $listVideoId = [];
        $count = 0;
        $success = 0;
        $listVideoIdAlive = [];
        foreach ($datas as $index => $campaign) {
//            $campaign->views =0;
//            $campaign->save();
            $listVideoId[] = trim($campaign->video_id);
//            Log::info(count($listVideoId) . " - " . $index . " - " . ($total - 1));
            if (count($listVideoId) >= 45 || $index == ($total - 1)) {
                $jData = YoutubeHelper::getStatics(implode(",", $listVideoId));
                error_log("|scanCampaign2|getStatics=" . count($listVideoId) . '/' . $index);
                if (isset($jData)) {
                    $items = !empty($jData->items) ? $jData->items : [];
//                    error_log("|$user->user_name|CampaignController.scanViewPromoCampaign|count=" .count($items));
                    foreach ($items as $item) {
                        $vidID = $item->id;
                        $listVideoIdAlive[] = $vidID;
                        $views = !empty($item->statistics->viewCount) ? $item->statistics->viewCount : 0;
                        $likes = !empty($item->statistics->likeCount) ? $item->statistics->likeCount : 0;
                        $daily = 0;
                        foreach ($datas as $tmp) {
                            if ($vidID == $tmp->video_id) {
                                if ($views > 0) {
                                    $daily = $views - $tmp->views_real;
                                    if ($daily < 0) {
                                        $daily = 0;
                                    }
                                    Campaign::where("video_id", $vidID)->update(["views_real" => $views, "views" => $views, "like" => $likes, "update_time" => $date]);
                                    $check = AthenaPromoSync::where("date", $scanDate)->where("video_id", $vidID)->first();
                                    if (!$check) {
                                        $insert = new AthenaPromoSync();
                                        $insert->date = $scanDate;
                                        $insert->video_id = $vidID;
                                        $insert->views_real = $views;
                                        $insert->views_real_daily = $daily;
                                        $insert->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                        $insert->save();
                                    } else {
                                        $check->views_real = $views;
                                        $check->views_real_daily = $daily;
                                        $check->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                        $check->save();
                                    }
                                    $success++;
                                }
                                break;
                            }
                        }
                        $count++;
                        error_log("scanCampaign-$threadId $date $count/$total $vidID total=$views,daily=$daily");
                    }
                } else {
                    error_log("|scanCampaign2|not found result");
                }
                //những video không tồn tại trong $listVideoIdAlive là video dead
                $listVideoIdDead = array_diff($listVideoId, $listVideoIdAlive);
                Campaign::whereIn("video_id", $listVideoIdDead)->update(["views_real" => 0, "status" => 0]);
                $listVideoId = [];
                $listVideoIdAlive = [];
            }
        }


        //2023/04/17 sang confirm check thay tieu de cho các video mix
        $bommixs = Campaign::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status_use", 1)->where('video_type', 5)->where("is_changed_title", 0)->whereIn("campaign_id", $arrayActiveCam)->where("status_confirm", 1)->get();
        //kiểm tra thay đổi tiêu đề của video boommix
        error_log("scanCampaign2 check boomix title " . count($bommixs));
        foreach ($bommixs as $bommix) {
            $info = YoutubeHelper::getVideoInfoHtmlDesktop($bommix->video_id);
            if ($info["status"] == 0) {
                for ($t = 0; $t < 5; $t++) {
                    error_log("scanCampaign2 Retry $bommix->video_id");
                    $info = YoutubeHelper::getVideoInfoHtmlDesktop($bommix->video_id);
                    if ($info["status"] == 1) {
                        break;
                    }
                }
            }
            if ($info["status"] == 1 && $info["title"] != "") {
                if ($bommix->video_title != $info["title"]) {
                    error_log("scanCampaign2 changed title $bommix->video_id $bommix->video_title**" . $info["title"]);
                    Campaign::where("video_id", $bommix->video_id)->update(["is_changed_title" => 1, "video_title" => $info["title"]]);
                }
            }
        }



        //scan thông tin video official
        $officials = Campaign::where(DB::raw("id % $numberThreadInit"), $threadId)->where("status_use", 1)->where('video_type', 1)->whereIn("campaign_id", $arrayActiveCam)->where("status_confirm", 3)->get();
        error_log("scanCampaign2 check official " . count($officials));
        foreach ($officials as $data) {
            $info = YoutubeHelper::getVideoInfoHtmlDesktop($data->video_id);
            if ($info["status"] == 0) {
                for ($t = 0; $t < 5; $t++) {
                    error_log("Retry $data->video_id");
                    $info = YoutubeHelper::getVideoInfoHtmlDesktop($data->video_id);
                    if ($info["status"] == 1) {
                        break;
                    }
                }
            }
            if ($info["status"] == 1) {
                $data->comment = $info["comment"];
                $data->subs = $info["channel_sub"];
                $data->save();
            }
        }
        $endTime = time() - $startTime;
        if ($threadId == 0) {
            ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Fisnish scanCampaign2 " . round($endTime / 60, 3) . " minutes, success=$success/$total");
        }
    }

    //ham get thong tin video
    public function videoInfo(Request $request) {
        $data = YoutubeHelper::processLink($request->link);
        Log::info(json_encode($data));
        if ($data['type'] == 3) {
            return YoutubeHelper::getVideoInfoHtmlDesktop($data['data']);
        }
        return [];
    }

    //ham chay run autocomment
    public function runComment() {
        $separate = Config::get('config.separate_text');
        $datas = CampaignStatistics::where("status", 1)->get();
        $curr = time();
        foreach ($datas as $data) {
            if ($data->official == null) {
                continue;
            }
            $official = json_decode($data->official);

            if (!empty($official->crypto_cmt_run) && $official->crypto_cmt_run == 1) {
                if (!empty($official->crypto_cmt) && $official->crypto_cmt > 0) {
                    //đếm số comment đã chạy
                    if (empty($official->crypto_cmt_content_finish)) {
                        $official->crypto_cmt_content_finish = null;
                        $arrayFinish = [];
                        $countCmt = 0;
                    } else {
                        $temp = str_replace(array("\r\n", "\n"), $separate, $official->crypto_cmt_content_finish);
                        $arrayFinish = explode($separate, $temp);
                        $countCmt = Utils::countInArray("[success]", $arrayFinish);
                    }

                    //kiểm tra xem đến giờ chạy chưa
                    if (empty($official->cmt_schedule) || empty($official->cmt_number)) {
                        error_log("runComment $data->id chưa cấu hình");
                        continue;
                    }
                    if (!empty($official->cmt_last_run) && $official->cmt_last_run > 0) {
                        //tính ra thời gian để dc chạy 1 cmt, vd: 24 cmt chạy trong 1 ngày =>1 cmt phải cách nhau ít nhất 1h
                        $timePerCmt = $official->cmt_schedule * 86400 / $official->cmt_number;
                        if (($official->cmt_last_run + $timePerCmt) > time()) {
                            error_log("runComment $data->id chua den gio chay, gio chay la." . gmdate("Y/m/d H:i:s", ($official->cmt_last_run + $timePerCmt) + 7 * 3600));
                            continue;
                        }
                    }
                    //nếu số comment cần chạy lớn hơn số cmt đã chạy thì mới tiếp tục
                    if ($official->crypto_cmt > $countCmt && $official->crypto_cmt_content != null && $official->crypto_cmt_content != "") {
                        $temp2 = str_replace(array("\r\n", "\n"), $separate, $official->crypto_cmt_content);
                        $arrayAvaiable = explode($separate, $temp2);
                        if (count($arrayAvaiable) > 0) {
                            //lấy nội dung cần comment
                            $text = $arrayAvaiable[0];
                            //lấy video official
//                            $campaign = Campaign::where("campaign_id", $data->id)->where("video_type", 1)->first();
                            $videoComment = $official->crypto_cmt_link;
                            //lấy kênh
                            $channels = AccountInfo::where("user_name", "autocomment_1669193469")->where("channel_genre", $data->genre)->where("is_rebrand", 5)->orderByRaw('RAND()')->where("status", 1)->get();
                            if (count($channels) == 0) {
                                error_log("runComment not found any gmail $data->genre");
                                continue;
                            }

                            $gmail = null;
                            $groupComment = null;
                            if (!empty($official->group_comment) && $official->group_comment != "") {
                                $groupComment = $official->group_comment;
                            }
                            foreach ($channels as $channel) {
                                $check = CampaignComment::where("gmail", $channel->note)->where("video_id", $videoComment);
                                if ($groupComment) {
                                    $check = $check->where("group_comment", $groupComment);
                                }
                                $check = $check->first();
                                if (!$check) {
                                    $gmail = $channel->note;
                                }
                            }

                            if ($gmail == null) {
                                error_log("runComment $data->id gmail=null");
                                continue;
                            }

                            if ($videoComment != null && $videoComment != "") {
                                $taskLists = [];
                                $login = (object) [
                                            "script_name" => "profile",
                                            "func_name" => "login",
                                            "params" => []
                                ];

                                $paramsComment = [];
                                $listParamComment = ["description" => $text, "video_source" => $videoComment, "category" => ""];
                                foreach ($listParamComment as $key => $value) {
                                    $param = (object) [
                                                "name" => $key,
                                                "type" => "string",
                                                "value" => $value
                                    ];
                                    $paramsComment[] = $param;
                                }
                                $pinComment = (object) [
                                            "script_name" => "upload",
                                            "func_name" => "comment",
                                            "params" => $paramsComment
                                ];
                                $taskLists[] = $login;
                                $taskLists[] = $pinComment;


                                $req = (object) [
                                            "gmail" => $gmail,
                                            "task_list" => json_encode($taskLists),
                                            "run_time" => 0,
                                            "type" => 610,
                                            "studio_id" => $data->id,
                                            "piority" => 30,
                                            "call_back" => "http://automusic.win/callback/comment"
                                ];
//                                    Log::info("runComment COMMENT req:" . json_encode($req));
                                Logger::logUpload("runComment COMMENT req:" . json_encode($req));
                                $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
//                                    Log::info("runComment COMMENT res:" . json_encode($bas));
                                Logger::logUpload("runComment COMMENT res:" . json_encode($bas));
////                                    //lưu lại thông tin đã comment
//                                    array_unshift($cmtMail, $gmail);
//                                    $official->cmt_mail = $cmtMail;
                                //lưu lại thông tin lệnh comment
                                $insert = new CampaignComment();
                                $insert->campaign_id = $data->id;
                                $insert->video_id = $videoComment;
                                $insert->gmail = $gmail;
                                $insert->job_id = $bas->job_id;
                                $insert->status = 1;
                                $insert->created = Utils::timeToStringGmT7($curr);
                                $insert->content = $text;
                                if ($groupComment) {
                                    $insert->group_comment = $groupComment;
                                }
                                $insert->save();

                                $official->cmt_last_run = time();
                                $official->job_id = $bas->job_id;
                                $data->official = json_encode($official);
                                $data->save();
                            }
                        }
                    } else {
                        error_log("runComment $data->id not found comment content");
                    }
                }
            }
        }
    }

    //2023/06/09 auto multy comment
    public function getComments() {
        $data = CampaignComment::where("type", 2)->orderBy("id", "desc")->get(["id", "campaign_id", "video_id", "gmail", "job_id", "status", "last_run", "content", DB::raw("CASE WHEN status=4 THEN 'Error' WHEN status=5 THEN 'Success' WHEN status=1 THEN 'Running' END as status_text")]);
//        Log::info(json_encode($data));
        return $data;
    }

    public function importCommnents(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.importCommnents|request=' . json_encode($request->all()));
        $separate = Config::get('config.separate_text');
        $temp = str_replace(array("\r\n", "\n"), $separate, $request->comments);
        $comments = [];
        $arrayComments = explode($separate, $temp);
        foreach ($arrayComments as $tmp) {
            if (!in_array($tmp, $comments)) {
                $comments[] = $tmp;
            }
        }
        $campaignIds = $request->comment_campaign;
        $i = 0;
        $group = uniqid();
        foreach ($campaignIds as $cam) {
            $data = CampaignCommentAuto::where("campaign_id", $cam)->first();
            if (!$data) {
                $data = new CampaignCommentAuto();
            }
            $data->campaign_id = $cam;
            $data->group = $group;
            $data->schedule = $request->comment_schedule;
            $data->number = $request->comment_number;
            $data->status = $request->comment_status;
            $data->comments = implode(PHP_EOL, $comments);
            $data->last_update = Utils::timeToStringGmT7(time());
            $data->save();
            $i++;
        }
        return array("status" => "success", "message" => "Success $i");
    }

    // tự động chạy comment các video lyric > 10k view cho promo
    public function autoComments() {
        $separate = Config::get('config.separate_text');
        $datas = CampaignCommentAuto::where("status", 1)->get();
        foreach ($datas as $data) {
            //lấy dữ liệu mới
            $config = CampaignCommentAuto::where("campaign_id", $data->campaign_id)->first();
            $arrayAvaiable = explode($separate, str_replace(array("\r\n", "\n"), $separate, $config->comments));
            if (count($arrayAvaiable) == 0) {
                error_log("autorunComments  no commnent");
                continue;
            }
            $text = $arrayAvaiable[0];
            if ($text == null || $text == "") {
//                error_log("autorunComments commnent empty");
                continue;
            }
            $videoComment = null;
            $camIdComment = null;
            $videos = Campaign::where("status", 1)->whereIn("video_type", [2, 5])->where("views_real", ">=", 10000)->where("status_confirm", 3)->where("campaign_id", $data->campaign_id)->orderByRaw('RAND()')->get();
            foreach ($videos as $video) {
                $timePerCmt = $data->schedule * 86400 / $data->number;
                if (($video->last_time_comment + $timePerCmt) < time()) {
                    $videoComment = $video->video_id;
                    $camIdComment = $video->campaign_id;
                    $video->last_time_comment = time();
                    $video->save();
                    break;
                }
            }
            if ($videoComment == null) {
                error_log("autorunComment not found video_id of $data->campaign_id");
                continue;
            }
            $campaignStatic = CampaignStatistics::find($camIdComment);
            $channels = AccountInfo::where("user_name", "autocomment_1669193469")->where("channel_genre", $campaignStatic->genre)->where("is_rebrand", 5)->orderByRaw('RAND()')->where("status", 1)->get();
            if (count($channels) == 0) {
                error_log("autorunComment not found any gmail $campaignStatic->genre");
                continue;
            }

            $checkGmails = CampaignComment::where("campaign_id", $data->campaign_id)->where("video_id", $videoComment)->get();
            if (count($checkGmails) == 0) {
                foreach ($channels as $channel) {
                    $ex = 0;
                    foreach ($checkGmails as $mail) {
                        if ($mail->gmail == $channel->note) {
                            $ex = 1;
                            break;
                        }
                    }
                    if (!$ex) {
                        $gmail = $channel->note;
                        break;
                    } else {
                        //ko tìm thấy email để comment
                        $gmail = null;
                    }
                }
            } else {
                $gmail = $channels[0]->note;
            }

            if ($gmail == null) {
                error_log("autorunComment $data->id gmail=null");
                continue;
            }

            $taskLists = [];
            $login = (object) [
                        "script_name" => "profile",
                        "func_name" => "login",
                        "params" => []
            ];

            $paramsComment = [];
            $listParamComment = ["description" => $text, "video_source" => $videoComment, "category" => ""];
            foreach ($listParamComment as $key => $value) {
                $param = (object) [
                            "name" => $key,
                            "type" => "string",
                            "value" => $value
                ];
                $paramsComment[] = $param;
            }
            $pinComment = (object) [
                        "script_name" => "upload",
                        "func_name" => "comment",
                        "params" => $paramsComment
            ];
            $taskLists[] = $login;
            $taskLists[] = $pinComment;


            $req = (object) [
                        "gmail" => $gmail,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => 610,
                        "studio_id" => $data->id,
                        "piority" => 30,
                        "call_back" => "http://automusic.win/callback/commentauto"
            ];
//                                    Log::info("runComment COMMENT req:" . json_encode($req));
            Logger::logUpload("autorunComment COMMENT req:" . json_encode($req));
            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
//                                    Log::info("runComment COMMENT res:" . json_encode($bas));
            Logger::logUpload("autorunComment COMMENT res:" . json_encode($bas));
////                                    //lưu lại thông tin đã comment
//                                    array_unshift($cmtMail, $gmail);
//                                    $official->cmt_mail = $cmtMail;
            //lưu lại thông tin lệnh comment
            $insert = new CampaignComment();
            $insert->type = 2;
            $insert->campaign_id = $data->campaign_id;
            $insert->video_id = $videoComment;
            $insert->gmail = $gmail;
            $insert->job_id = $bas->job_id;
            $insert->status = 1;
            $insert->created = Utils::timeToStringGmT7(time());
            $insert->content = $text;
            $insert->save();

            //cập nhật thông tin bảng auto
            $data->last_run = time();
            array_shift($arrayAvaiable);
            CampaignCommentAuto::where("group", $data->group)->update(["comments" => implode("\r\n", $arrayAvaiable)]);
            $data->save();
        }
    }

    //fix những trường hợp views tổng =  views daily => khi sum(views_daily) thì bị double views lên.
    //trường hợp video có view rồi mới add vào hệ thống thì cho phép điều đó xảy ra
    //còn không thì phải tính lại views daily
    public function fixErrViews() {
        $datas = DB::select("select * from athena_promo_sync where views_real = views_real_daily and views_real > 0");
        foreach ($datas as $data) {
            $check = AthenaPromoSync::where("video_id", $data->video_id)->first();
            if ($check->date != $data->date) {
                $dayBefore = gmdate('Ymd', strtotime($data->date) - 86400);
                $video = AthenaPromoSync::where("video_id", $data->video_id)->where("date", $dayBefore)->first();
                if ($video) {
                    $inc = $data->views_real - $video->views_real;
                } else {
                    $inc = 0;
                }
                AthenaPromoSync::where("video_id", $data->video_id)->where("date", $data->date)->update(["views_real_daily" => $inc]);
                Logger::logUpload("fixErrViews $data->date $data->video_id");
            }
        }
    }

    public function adsViews(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CampaignController.adsViews|request=' . json_encode($request->all()));
        $video = Campaign::where("id", $request->id)->first();
        if ($video) {
            $views = $request->ads_views;
            $views = str_replace(",", "", $views);
            $update = Campaign::where("video_id", $video->video_id)->update(["ads_views" => $views]);
            return response()->json(["status" => "success", "message" => "Success $update"]);
        }
        return response()->json(["status" => "error", "message" => "Not found $request->id"]);
    }

    //update submission info để chia %
    public function submissionPercent(Request $request) {
        $user = Auth::user();
        Log::info('|' . $user->user_name . '|CampaignController.submissionPercent|request=' . json_encode($request->all()));
        if ($request->is_admin_music) {

            $campaign = CampaignStatistics::where("id", $request->cam_id)->first();
            if ($campaign) {
                if ($request->data != null) {
                    $campaign->submission_info = json_encode($request->data);
                    $campaign->log = Utils::timeToStringGmT7(time()) . " $user->user_name update submission_info " . PHP_EOL . $campaign->log;
                    $campaign->save();
                    return response()->json(["status" => "success", "message" => "Success"]);
                }
            }
        } else {
            return response()->json(["status" => "error", "message" => "You do not have permission"]);
        }
    }

}
