<?php

namespace App\Http\Controllers;

use App\Common\Logger;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignClaimRev;
use App\Http\Models\CampaignClaimRevStatus;
use App\Http\Models\CampaignClaimRevTmp;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\ClaimsViews;
use App\Http\Models\ClaimsViewsTmp;
use App\Http\Models\LabelgridArtist;
use App\Http\Models\LabelgridRelease;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Log;
use Validator;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function public_path;
use function response;
use function view;

class ClaimController extends Controller {

    private $authorization = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1MTAyMDNiYy03MDk4LTQ4NDgtYWEyMC01YjBhYTg5Y2EyMDAiLCJqdGkiOiI3YTVmMWI2ZTg3YzY0MmQ5NWE2MWU3YjFiZTA2ZGEzM2NmNjI0N2JjMTYwNmY4ZGYyMTJhODRmOTlkNjQxMDcxODVkZGM4NGUxYTJmZTMyMSIsImlhdCI6MTY1NDE1NjA5NS4zMzA4OTQ5NDcwNTIwMDE5NTMxMjUsIm5iZiI6MTY1NDE1NjA5NS4zMzA5MDA5MDc1MTY0Nzk0OTIxODc1LCJleHAiOjE5Njk3NzUyOTUuMjUyMjk5MDcwMzU4Mjc2MzY3MTg3NSwic3ViIjoiMTQzIiwic2NvcGVzIjpbInVzZXIudmlldy1jYXRhbG9nIiwidXNlci5nYXRlLXVzZSJdfQ.F8WBZmWETwEmSFumMKbFpCHtJReUjmsk6HQPBX-QWzVmTXHL6W3HbnXG-flaVWT044aRoYJ3wzZgH1abUshFVCKK4cID8fC5o69A1626GkIp2Iy8OTHvasHswAR-NFensKGIMEOYxD-NGs7rxZqwXeNX_LINlHXEu5aWPoKWSvznUP9VrEKB0ehCo50kwW9QbrAW_r7UzNynOaE29KQ493H4wRPvF2nztoykQjb3E-jqVkZxsNEuLNaEfwV1PpRVClF66PWRhg1Drd42WjWlcPAm6fVrrNiXF9rwRGepKZDNAORjOqGQt3VPeT5SzglJjpt-CkgNAFpsTzAp2cjNQa_g6gYVdYm5eO6ypj1RXZ3-R6jDV2EuTCww8HPGILc_jkE9XOP5eXIQXZyWPLCh4znNbk2pusan_m-jEq4zvyX9uJ-Cd9VNEjdj7AiHnJ7q1nWWHlrbgTfgpNPK0bE4K1rxUvANTKFCs1FRpsvSdMewNeYDB0yGcDeN7CNtnLVbmNNmrXvH_0ONF8H1KZmjTP2VGfiglIMNHpmukkSukkE43AcLSJSsIf5jFCLRWFx_x-3Fbj4127zOK8aqgX_Ya3lb_ellKlsZpXz3Vx5-S_fDKKNCAynBDI1MDutvV9-GkM9yuw4hKaiZ56fJshUJKYC4vO2IstLMOlJyBbRpn2U";

    public function __construct() {
//        $this->middleware('auth');
    }

    public function getAuthorization() {
        return $this->authorization;
    }

    public function index(Request $request) {
        ini_set('max_execution_time', '3000');
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.index|request=' . json_encode($request->all()));
        $status = 1;
        $statuss = [];
        $startTime = time();
        if (isset($request->status)) {
            $status = $request->status;
        }
        $statuss[] = $status;
        if ($status == 1) {
            $statuss[] = 4;
            $statuss[] = 2;
        }

        if ($status != 3) {
            $datas = CampaignStatistics::whereIn("status", $statuss)->where("type", 2)->orderBy("id", "desc");
        } else {
            $datas = CampaignStatistics::where("type", 2)->orderBy("id", "desc");
        }

        if ($request->is_admin_music) {
            $datas = $datas->get();
        } else {
            $datas = $datas->where("username", $user->user_name)->get();
//            $datas = $datas->where(function ($query) use ($user) {
//                        $query->where('username', $user->user_name)
//                                ->orWhereNull('username');
//                    })->get();
        }

        //đếm số lương video của mỗi claim
        $currPeriod = gmdate("Ym", time() + 7 * 3600);
//        if (Cache::has("cache_count_videos")) {
//            $countVideos = Cache::get("cache_count_videos");
//        } else {
        $countVideos = DB::select("select campaign_id, count(video_id) as count_video from campaign where video_type <> 1 and status_confirm=3 and campaign_id in (select id from campaign_statistics where status in(1,2,4) and type in (2)) group by campaign_id");
//            Cache::put("cache_count_videos", $countVideos, 720);
//        }
////        Cache::forget("cache_count_videos_current");
//        if (Cache::has("cache_count_videos_current")) {
//            $countVideosCurrentMonth = Cache::get("cache_count_videos_current");
//        } else {
        $countVideosCurrentMonth = DB::select("select campaign_id, count(video_id) as count_video from campaign where video_type <> 1 and status_confirm=3 and insert_date like '%$currPeriod%' and campaign_id in (select id from campaign_statistics where status in(1,2,4) and type in (2)) group by campaign_id");
//            Cache::put("cache_count_videos_current", $countVideosCurrentMonth, 720);
//        }
        $distributors = DB::select("SELECT distributor,id FROM campaign_statistics WHERE status IN (1,4) AND type = '2'");
        $listDistributor = [];
        foreach ($distributors as $dis) {
            if (!in_array($dis->distributor, $listDistributor)) {
                $listDistributor[] = $dis->distributor;
            }
        }

        // <editor-fold defaultstate="collapsed" desc="2023/04/14 báo cáo view theo tháng theo user">
//        Cache::forget("monthly_claim_views_current");
//        $cacheKey = "monthly_claim_views_current";
//        if (Cache::has($cacheKey)) {
//            $listVideos = Cache::get($cacheKey);
//        } else {
//            $listVideos = DB::select("select username,campaign_id,video_type,video_id,number_claim from campaign where video_type <> 1 and status_confirm =3 
//                                    and campaign_id in (SELECT id FROM `campaign_statistics` WHERE `type` = '2' and status in (1,4))
//                                    group by username,campaign_id,video_type,video_id,number_claim");
//
//
//            //tìm danh sách các ngày đàu tháng tính từ period truyền vào
//            $months = [];
//            $months25 = [];
//            $curMonth = gmdate("m", time());
//            $curYear = gmdate("Y", time());
//            $curNextMonth = $curMonth + 1;
//            $lastRage = $curNextMonth - 1;
//            for ($i = $curNextMonth; $i >= $lastRage; $i--) {
////            Log::info($i);
//                $m = $i;
//                $m25 = $i - 1;
//                $name = $curMonth - $i;
//
//                $y = $curYear;
//                if ($i <= 0) {
//                    $m = 12 + $i;
//                    $m25 = 12 + $i;
//                    $y = $curYear - 1;
//                }
//                if ($m < 10) {
//                    $m = "0$m";
//                    $m25 = "0$m25";
//                }
//                if ($name == -1) {
//                    $var = "monthPlus1";
//                } else {
//                    $var = "month$name";
//                }
//                $months[] = "\"$var\":\"$y$m" . "01\"";
//                $months25[] = "\"$var\":\"$y$m25" . "25\"";
//            }
//
//            $temp = "{" . implode(",", $months) . "}";
//            $temp25 = "{" . implode(",", $months25) . "}";
//            $monthJson = json_decode($temp);
//            $monthJson25 = json_decode($temp25);
//
//
//
//            $viewMonth_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date >= $monthJson->month0 and date < $monthJson->monthPlus1 group by video_id");
//            $viewMonth25_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date >= $monthJson25->month0 and date < $monthJson25->monthPlus1 group by video_id");
//            $viewMonths = [$viewMonth_0];
//            $viewMonths25 = [$viewMonth25_0];
//            foreach ($viewMonths as $index => $vm) {
//                $listCheck = [];
//                $listCheck25 = [];
//                foreach ($listVideos as $video) {
//                    $key_views_money = "views_money_$index";
//                    $key_views_total = "views_total_$index";
//                    $key_views_mix = "views_mix_$index";
//                    $key_views_lyric = "views_lyric_$index";
//                    $key_views_short = "views_short_$index";
//
//                    $key_views_money25 = "views_money25_$index";
//                    $key_views_total25 = "views_total25_$index";
//                    $key_views_mix25 = "views_mix25_$index";
//                    $key_views_lyric25 = "views_lyric25_$index";
//                    $key_views_short25 = "views_short25_$index";
//
//                    $video->$key_views_money = 0;
//                    $video->$key_views_total = 0;
//                    $video->$key_views_mix = 0;
//                    $video->$key_views_lyric = 0;
//                    $video->$key_views_short = 0;
//
//                    $video->$key_views_money25 = 0;
//                    $video->$key_views_total25 = 0;
//                    $video->$key_views_mix25 = 0;
//                    $video->$key_views_lyric25 = 0;
//                    $video->$key_views_short25 = 0;
//                    foreach ($vm as $view) {
//                        if ($video->video_id == $view->video_id) {
//                            $video->$key_views_total = $view->views;
//                            if ($video->number_claim == 0 || $video->number_claim == null) {
//                                $video->$key_views_money = $view->views;
//                            } else {
//                                $video->$key_views_money = round(1 / $video->number_claim * $view->views);
//                            }
//                            if ($video->video_type == 2) {
//                                $video->$key_views_lyric = $view->views;
//                            } else if ($video->video_type == 5) {
//                                $video->$key_views_mix = $view->views;
//                            } else if ($video->video_type == 6) {
//                                $video->$key_views_short = $view->views;
//                            }
//                            $listCheck[] = $video->video_id;
//                        }
//                    }
//                    foreach ($viewMonths25[$index] as $view) {
//                        if ($video->video_id == $view->video_id) {
//                            $video->$key_views_total25 = $view->views;
//                            if ($video->number_claim == 0 || $video->number_claim == null) {
//                                $video->$key_views_money25 = $view->views;
//                            } else {
//                                $video->$key_views_money25 = round(1 / $video->number_claim * $view->views);
//                            }
//                            if ($video->video_type == 2) {
//                                $video->$key_views_lyric25 = $view->views;
//                            } else if ($video->video_type == 5) {
//                                $video->$key_views_mix25 = $view->views;
//                            } else if ($video->video_type == 6) {
//                                $video->$key_views_short25 = $view->views;
//                            }
//                            $listCheck25[] = $video->video_id;
//                        }
//                    }
//
//
//                    //thêm thông tin distributor vào listvideo
//                    foreach ($distributors as $dis) {
//                        if ($video->campaign_id == $dis->id) {
//                            $video->distributor = $dis->distributor;
//                            break;
//                        }
//                    }
//                }
//            }
//            Cache::put($cacheKey, $listVideos, 1440);
//        }
        // </editor-fold>
        $st2 = time();
//        Log::info(count($listVideos));
        $i = 0;
        $listClaimViewsMonth = [];
        if (Cache::has("cache_list_claim_with_views_month")) {
            $listClaimViewsMonth = Cache::get("cache_list_claim_with_views_month");
        }
        foreach ($datas as $data) {
            $i++;
            //2023/05/31 views claim theo tháng
            $data->monthly_view_total = 0;
            $data->monthly_views_mix = 0;
            $data->monthly_views_lyric = 0;
            $data->monthly_views_short = 0;
//            foreach ($listVideos as $video) {
//                if ($data->id == $video->campaign_id) {
//                    $data->monthly_view_total += $data->distributor == "AdRev" ? $video->views_total25_0 : $video->views_total_0;
//                    $data->monthly_views_mix += $data->distributor == "AdRev" ? $video->views_mix25_0 : $video->views_mix_0;
//                    $data->monthly_views_lyric += $data->distributor == "AdRev" ? $video->views_lyric25_0 : $video->views_lyric_0;
//                    $data->monthly_views_short += $data->distributor == "AdRev" ? $video->views_short25_0 : $video->views_short_0;
//                }
//            }

            if (!empty($listClaimViewsMonth)) {
                $k = 0;
                foreach ($listClaimViewsMonth as $vMonth) {
                    $k++;
                    if ($data->id == $vMonth->id) {
                        $data->monthly_view_total = $vMonth->monthly_view_total;
                        $data->monthly_views_mix = $vMonth->monthly_views_mix;
                        $data->monthly_views_lyric = $vMonth->monthly_views_lyric;
                        $data->monthly_views_short = $vMonth->monthly_views_short;
                        unset($vMonth);
                        break;
                    }
//                    Log::info("$i/$k");
                }
//                Log::info("$i " . count($listClaimViewsMonth));
            }


            $data->monthly_count_videos = 0;
            foreach ($countVideosCurrentMonth as $countVideo) {
                if ($data->id == $countVideo->campaign_id) {
                    $data->monthly_count_videos = $countVideo->count_video;
                    break;
                }
            }
            $data->count_videos = 0;
            foreach ($countVideos as $countVideo) {
                if ($data->id == $countVideo->campaign_id) {
                    $data->count_videos = $countVideo->count_video;
                    break;
                }
            }

            //2023/04/14 cắt bớt tên claim
            $data->campaign_name_short = $data->campaign_name;
            if (strlen($data->campaign_name) > 50) {
                $data->campaign_name_short = substr_replace($data->campaign_name, "...", 60, 100);
            }
            $data->pre_name = "";
            if (Utils::containString($data->campaign_name, "[ORIGINAL]")) {
                $data->pre_name = "ORIGINAL";
            } elseif (Utils::containString($data->campaign_name, "[COVER]")) {
                $data->pre_name = "COVER";
            } elseif (Utils::containString($data->campaign_name, "[ELECTRONIC]")) {
                $data->pre_name = "ELECTRONIC";
            }
            //dữ liệu cho vẽ progress campaign, official, money
            $data->progress_mix = null;
            $data->progress_official = null;
            $data->progress_official_like = null;
            $data->progress_official_cmt = null;
            $data->progress_official_sub = null;
            $data->official_alert = 0;

            $data->crypto_view_run = 0;
            $data->adsense_view_run = 0;

            if ($data->official != null && $data->official_data != null) {

                $official_target = json_decode($data->official);
                $official_data = json_decode($data->official_data);
                $current = $official_data->view;
                $achieved = $official_data->view - $official_target->start_view;
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
                $current = 0;
                if ($data->views_detail != null) {
//                    $current = !empty(json_decode($data->views_detail)->$id) ? json_decode($data->views_detail)->$id : 0;
                    //lấy views của tháng hiện tại
                    $current = $data->monthly_views_mix + $data->monthly_views_lyric + $data->monthly_views_short;
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
                ];
                $data->progress_mix = $progressMix;
            }

            //2025/02/22 hiển thị YT distibutor
            $data->yt_distributor = "Not_Check_Yet";
            if ($data->short_text != null) {
                $st = json_decode($data->short_text);
                if (!isset($st->video_id) || $st->video_id == null) {
                    $data->yt_distributor = "Not_Check_Yet";
                } else {
                    if ($st->distributor == null) {
                        $data->yt_distributor = "No_Claim";
                    } else {
                        $data->yt_distributor = $st->distributor;
                    }
                }
            }
        }

        Log::info("for: " . (time() - $st2));
        $gridArtist = LabelgridArtist::all();
        $gridRelease = LabelgridRelease::whereNotNull("release_id")->get();
//        Log:info(DB::getQueryLog());
        Log::info("ClaimController.Index Time:" . (time() - $startTime));
        $defaultReleaseDate = date("Y-m-d");


        return view('components.claim', [
            'monthSelect' => $this->genMonthSelectV2(),
            'listDistributor' => $this->loadDistributor(),
//            'distributorViews' => $distributorViewsResult,
//            'listMonth' => $listMonth,
            'datas' => $datas,
//            'listCampaign' => $listCampaign,
//            'genres' => $genres,
            'request' => $request,
            'status' => $this->genStatusCampaign($request),
            'group_channel_search' => $this->loadGroupChannelForSeach($request),
            'channel_genre' => $this->loadChannelGenre($request),
            'channel_subgenre' => $this->loadChannelSubGenre($request),
            'channel_tags' => $this->loadChannelTags($request),
            'labelGridArtist' => $this->loadLabelGridArtist($request),
            'labelGridRelease' => $this->loadLabelGridReleases($request),
            'defaultReleaseDate' => $defaultReleaseDate,
            'gridArtists' => $gridArtist,
            'gridReleases' => $gridRelease,
            'listUser' => $this->genListUserForMoveChannel($user, $request, 1, 1)
        ]);
    }

    public function addClaim(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $i = 0;
        $curr = time();
        Log::info($user->user_name . '|ClaimController.addClaim|request=' . json_encode($request->all()));
//        $listCampaign = DB::select("select campaign_name from campaign_statistics where type = 2 and status = 1");
        if (!$request->is_admin_music) {
            return response()->json(['status' => "error", 'message' => "You are not an admin"]);
        }
        if ($request->campaign_name == null || $request->campaign_name == "") {
            return array("status" => "error", "content" => "Campaign can not be empty", "contentedit" => "Success");
        }
//        if ($request->deezer_artist_id == "" || $request->deezer_artist_id == 0) {
//            return array("status" => "error", "content" => "Deezer Artists ID invalid", "contentedit" => "Success");
//        }
        if ($request->cam_id == null) {
            $checkCampaign = CampaignStatistics::where("campaign_name", $request->campaign_name)->first();
            if (!$checkCampaign) {
                $campaignStatistics = new CampaignStatistics();
                $campaignStatistics->type = 2;
                if ($request->status != 3) {
                    $campaignStatistics->status = $request->status;
                }
                $campaignStatistics->campaign_name = $request->campaign_name;
                if ($request->username != "-1") {
                    $campaignStatistics->username = $request->username;
                }
//                $campaignStatistics->campaign_start_date = gmdate("Y/m/d", $curr + 7 * 3600);
                $campaignStatistics->campaign_start_date = $request->campaign_start_date;
                $campaignStatistics->campaign_start_time = "00:00:00";

                $campaignStatistics->genre = strtoupper($request->genre);
                $campaignStatistics->custom_genre = $request->custom_genre;
                $campaignStatistics->artist = $request->artist;
                $campaignStatistics->song_name = $request->songname;
                $campaignStatistics->distributor = $request->distributor;

                $campaignStatistics->official_video = trim($request->official_video);
                $info = YoutubeHelper::processLink($campaignStatistics->official_video);
                if ($info["type"] == 3) {
                    $video_id = $info["data"];
                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                    if ($videoInfo["status"] == 0) {
                        for ($t = 0; $t < 15; $t++) {
                            error_log("addclaim Retry $video_id");
                            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                            if ($videoInfo["status"] == 1) {
                                break;
                            }
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
                $campaignStatistics->bitly_url = $request->bitly_url;
                $campaignStatistics->audio_url = $request->audio_url;
                $campaignStatistics->lyrics = $request->lyrics;
                $campaignStatistics->deezer_artist_id = $request->deezer_artist_id;
                $campaignStatistics->deezer_id = $request->deezer_id;
                $campaignStatistics->isrc = $request->isrc;
                $campaignStatistics->upc = $request->upc;
                $campaignStatistics->promo_keywords = $request->promo_keywords;
                $campaignStatistics->artist_percent = $request->artist_percent;
                $campaignStatistics->bass_percent = $request->bass_percent;
                $campaignStatistics->tax_percent = $request->tax_percent;
                $campaignStatistics->tier = $request->tier;
                $campaignStatistics->log = Utils::timeToStringGmT7(time()) . " $user->user_name added";
                if (Utils::containString($request->spotify_id, "spotify.com")) {
                    if (preg_match("/https:\/\/open.spotify.com\/track\/(\w+)/", $request->spotify_id, $re)) {
                        $spotifyId = $re[1];
                    }
                } else {
                    $spotifyId = trim($request->spotify_id);
                }
                $campaignStatistics->spotify_id = $spotifyId;
                $campaignStatistics->artists_social = $request->artists_social;

                $campaignStatistics->views_detail = json_encode((object) array());
                $campaignStatistics->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                $campaignStatistics->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                if ($request->deezer_id != 0) {
                    $isLyric = 0;
                    $isSync = 0;
                    //check bai hat da co lyric va da download mp3 ve system chua
                    $temp = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?format=json&check_lyric=$request->deezer_id");
                    $checkLyric = json_decode($temp);
                    if (!empty($checkLyric->lyric_status)) {
                        $isLyric = $checkLyric->lyric_status;
                    }
                    if (!empty($checkLyric->song_status)) {
                        $isSync = $checkLyric->song_status;
                    }
                    //nếu chưa được down thì thực hiện download

                    if ($isSync == 0) {
                        Log::info("http://source.automusic.win/deezer/track/get/$request->deezer_id");
                        $res = RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$request->deezer_id");
                        Log::info("store $res");

                        if ($res != null && $res != "") {
                            $response = json_decode($res);
                            $isSync = 1;
                            if ($response->lyric_sync != "") {
                                $isLyric = 1;
                                $campaignStatistics->lyric_timestamp_id = $response->id;
                            }
                            if (!empty($response->url_128) && $response->url_128 != "") {
                                $campaignStatistics->audio_url = $response->url_128;
                            }
                        }
                    }
                }
                if (isset($request->target_total)) {
                    $campaignStatistics->target = strtoupper($request->target_total);
                }
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
            } else {
                return array("status" => "error", "content" => "Claim campaign name already exists", "contentedit" => "Success");
            }
        } else {
            $campaignStatistics = CampaignStatistics::find($request->cam_id);
            if ($campaignStatistics) {

                $campaignStatistics->campaign_name = $request->campaign_name;
                $campaignStatistics->campaign_start_date = $request->campaign_start_date;
                $campaignStatistics->username = $request->username;
                if ($request->genre != "-1" && $request->genre != "") {
                    $campaignStatistics->genre = strtoupper($request->genre);
                }
                $campaignStatistics->custom_genre = $request->custom_genre;
                $campaignStatistics->artist = $request->artist;
                $campaignStatistics->song_name = $request->songname;
                $campaignStatistics->distributor = $request->distributor;
                if ($campaignStatistics->official != null) {
                    $official = (array) json_decode($campaignStatistics->official);
                } else {
                    $official = [];
                }
                if ($campaignStatistics->official_video != $request->official_video && $request->official_video != null && $request->official_video != "") {
                    $campaignStatistics->official_video = $request->official_video;
                    $info = YoutubeHelper::processLink($request->official_video);
                    if ($info["type"] == 3) {
                        $video_id = $info["data"];
                        $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                        if ($videoInfo["status"] == 0) {
                            for ($t = 0; $t < 15; $t++) {
                                error_log("editclaim Retry $video_id");
                                $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                                if ($videoInfo["status"] == 1) {
                                    break;
                                }
                            }
                        }
                        Log::info(json_encode($videoInfo));
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
                                $campaign->views = $videoInfo['view'];
                                $campaign->views_real = $videoInfo['view'];
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
//                        $check = Campaign::where("video_type", 1)->where("campaign_id", $request->cam_id)->first();
//                        if (!$check) {
//                            $campaign = new Campaign();
//                            $campaign->username = $user->user_name;
//                            $campaign->campaign_id = $request->cam_id;
//                            $campaign->campaign_name = $request->campaign_name;
//                            $campaign->channel_id = $videoInfo["channelId"];
//                            $campaign->channel_name = $videoInfo["channelName"];
//                            $campaign->video_type = 1;
//                            $campaign->video_id = $video_id;
//                            $campaign->video_title = $videoInfo["title"];
//                            $campaign->views_detail = '[]';
//                            $campaign->status = $videoInfo["status"];
//                            $campaign->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
//                            $campaign->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
//                            $campaign->publish_date = $videoInfo["publish_date"];
//                            $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
//                            $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
//                            $campaign->status_confirm = 3;
//                            $campaign->log = gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name added";
//                            $campaign->save();
//                        } else {
//                            $check->log = $check->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name change from $check->video_id to $video_id";
//                            $check->video_id;
//                            $check->video_title = $videoInfo["title"];
//                            $check->channel_id = $videoInfo["channelId"];
//                            $check->channel_name = $videoInfo["channelName"];
//                            $check->status = $videoInfo["status"];
//                            $check->save();
//                        }
                    }
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
                if ($campaignStatistics->audio_url != $request->audio_url) {
                    $campaignStatistics->audio_url = $request->audio_url;
                    $campaignStatistics->audio_url_cut = null;
                }
                $campaignStatistics->lyrics = $request->lyrics;
                $campaignStatistics->deezer_artist_id = $request->deezer_artist_id;
                $campaignStatistics->artists_social = $request->artists_social;
                $campaignStatistics->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                $campaignStatistics->isrc = $request->isrc;
                $campaignStatistics->upc = $request->upc;
                $campaignStatistics->promo_keywords = $request->promo_keywords;
                $campaignStatistics->artist_percent = $request->artist_percent;
                $campaignStatistics->bass_percent = $request->bass_percent;
                $campaignStatistics->tax_percent = $request->tax_percent;
                $campaignStatistics->tier = $request->tier;
                if (isset($request->target_total)) {
                    $campaignStatistics->target = strtoupper($request->target_total);
                }
                if (Utils::containString($request->spotify_id, "spotify.com")) {
                    if (preg_match("/https:\/\/open.spotify.com\/track\/(\w+)/", $request->spotify_id, $re)) {
                        $spotifyId = $re[1];
                    }
                } else {
                    $spotifyId = trim($request->spotify_id);
                }
                $campaignStatistics->spotify_id = $spotifyId;
                if ($request->deezer_id != $campaignStatistics->deezer_id) {
                    if ($request->deezer_id != 0) {
                        $campaignStatistics->deezer_id = $request->deezer_id;
                        $isLyric = 0;
                        $isSync = 0;
                        //check bai hat da co lyric va da download mp3 ve system chua
                        $temp = RequestHelper::getRequest("http://54.39.49.17:6132/api/tracks/?format=json&check_lyric=$request->deezer_id");
                        $checkLyric = json_decode($temp);
                        if (!empty($checkLyric->lyric_status)) {
                            $isLyric = $checkLyric->lyric_status;
                        }
                        if (!empty($checkLyric->song_status)) {
                            $isSync = $checkLyric->song_status;
                        }
                        //nếu chưa được down thì thực hiện download
                        Log::info("http://source.automusic.win/deezer/track/get/$request->deezer_id");
                        $res = RequestHelper::getRequest("http://source.automusic.win/deezer/track/get/$request->deezer_id");
                        Log::info("store $res");

                        if ($res != null && $res != "") {
                            $response = json_decode($res);
                            $isSync = 1;
                            if ($response->lyric_sync != "") {
                                $isLyric = 1;
                                $campaignStatistics->lyric_timestamp_id = $response->id;
                            }
                            if (!empty($response->url_128) && $response->url_128 != "") {
                                $campaignStatistics->audio_url = $response->url_128;
                            }
                        }
                    }
                }

                if ($request->status != 3) {
                    $campaignStatistics->status = $request->status;
                    $campaignStatistics->log = $campaignStatistics->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name change status to $request->status";
                }
                if ($request->status == 0 || $request->status == 2) {
                    Campaign::where("campaign_id", $request->id)->update(["status_use" => 0]);
                }
                $campaignStatistics->save();
                Campaign::where("campaign_id", $request->cam_id)->update(["campaign_name" => $request->campaign_name, "is_athena" => 0]);
            }
        }

        return array("status" => "success", "content" => "Success", "contentedit" => "Success");
    }

    public function labelgridAddArtist(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.labelgridAddArtist|request=' . json_encode($request->all()));
        if ($request->artistName == "") {
            return array("status" => "error", "content" => "Artist Name is invalid");
        }
        $artistName = trim($request->artistName);
        if ($request->photo_tmp == "" || $request->photo_tmp == null) {
            return array("status" => "error", "content" => "Upload photos first");
        }
        $photo_tmp = $request->photo_tmp;
        if ($request->artistName == "") {
            return array("status" => "error", "content" => "Artist Name is invalid");
        }
        if ($request->youtubeUrl == "" || !Utils::containString($request->youtubeUrl, "youtube.com")) {
            return array("status" => "error", "content" => "Youtube Url is invalid");
        }
        $youtubeUrl = trim($request->youtubeUrl);

        if ($request->bioShort == "") {
            return array("status" => "error", "content" => "Bio Short is invalid");
        }
        $bioShort = trim($request->bioShort);

        if ($request->bioFull == "") {
            return array("status" => "error", "content" => "Bio Full is invalid");
        }
        $bioFull = trim($request->bioFull);

        $image = RequestHelper::labelgridImageUpload("/home/automusic.win/public_html/public/$photo_tmp");
        $fileNamePhoto = "";
        if ($image != null && $image != "") {
            $fileNamePhoto = json_decode($image)->filename;
        }

        Log::info("labelgridAddArtist $image");
        if ($fileNamePhoto == "") {
            return array("status" => "error", "content" => "Upload photos failed");
        }
        $data = (object) [
                    "artistName" => $artistName,
                    "photo_tmp" => $fileNamePhoto,
                    "youtubeUrl" => $youtubeUrl,
                    "bioShort" => $bioShort,
                    "bioFull" => $bioFull,
                    "authorization" => $this->getAuthorization()
        ];
        $check = LabelgridArtist::where("artist_name", $artistName)->first();
        if ($check) {
            return array("status" => "error", "content" => "Artist is already exists");
        }
        $labelgridArtist = new LabelgridArtist();
        $labelgridArtist->username = $user->user_name;
        $labelgridArtist->artist_name = $artistName;
        $labelgridArtist->photos = $photo_tmp;
        $labelgridArtist->youtube_url = $youtubeUrl;
        $labelgridArtist->bio_short = $bioShort;
        $labelgridArtist->bio_full = $bioFull;
        $labelgridArtist->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);

        Log::info("labelgridAddArtist " . json_encode($data));
        $result = RequestHelper::labelgridArtist($data);
        if ($result != null && $result != "") {
            $output = json_decode($result);
            if (!empty($output->error)) {
                return array("status" => "error", "content" => $output->error);
            }
            $labelGridId = $output->id;
            $labelgridArtist->artist_id = $labelGridId;
            $labelgridArtist->save();
        }
        Log::info("labelgridAddArtist " . json_encode($result));
        return array("status" => "success", "content" => "Success");
    }

    public function labelgridAddRelease(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.labelgridAddRelease|request=' . json_encode($request->all()));

        if ($request->title == "") {
            return array("status" => "error", "content" => "Title Url is invalid");
        }
        $title = trim($request->title);

        if ($request->releaseDate == "") {
            return array("status" => "error", "content" => "Release Date is invalid");
        }
        $releaseDate = trim($request->releaseDate);

        if ($request->descriptionShort == "") {
            return array("status" => "error", "content" => "Description Short is invalid");
        }
        $descriptionShort = trim($request->descriptionShort);

        if ($request->descriptionLong == "") {
            return array("status" => "error", "content" => "Description Long is invalid");
        }
        $descriptionLong = trim($request->descriptionLong);
        $year = date("Y", time());

        $labelgridRelease = new LabelgridRelease();
        $labelgridRelease->artist_id = $request->artist;
        $labelgridRelease->title = $title;
        $labelgridRelease->release_date = $releaseDate;
        $labelgridRelease->description_short = $descriptionShort;
        $labelgridRelease->description_long = $descriptionLong;
        $labelgridRelease->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $labelgridRelease->save();
        $catalog = "SHX";
        $index = DB::select("select max(id) as number from labelgrid_release");
        $catNum = $index[0]->number;
        if ($catNum < 10) {
            $catalog .= "000$catNum";
        } else if ($catNum < 100) {
            $catalog .= "00$catNum";
        } else if ($catNum < 1000) {
            $catalog .= "0$catNum";
        } else if ($catNum < 10000) {
            $catalog .= "$catNum";
        } else {
            return array("status" => "error", "content" => "Over catalogue number > 9999");
        }
        $data = (object) [
                    "artistId" => $request->artist,
                    "cat" => $catalog,
                    "title" => $title,
                    "descriptionShort" => $descriptionShort,
                    "descriptionLong" => $descriptionLong,
                    "publisherYear" => $year,
                    "copyrightYear" => $year,
                    "releaseDate" => $releaseDate,
                    "cline" => "$year SoundHex",
                    "pline" => "$year SoundHex",
                    "year" => $year,
                    "authorization" => $this->getAuthorization(),
        ];



        $result = RequestHelper::labelgridRelease($data);
        error_log("labelgridAddRelease $result");
        if ($result != null && $result != "") {
            $output = json_decode($result);
            if (!empty($output->error)) {
                return array("status" => "error", "content" => $output->error);
            }
            $labelGridId = json_decode($result)->id;
            $labelgridRelease->release_id = $labelGridId;
            $labelgridRelease->save();
        }
        return array("status" => "success", "content" => "Success");
    }

    public function labelgridReleaseImage(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.labelgridReleaseImage|request=' . json_encode($request->all()));
        if ($request->release_image_3000 == null) {
            return array("status" => "error", "content" => "Image is invalid");
        }
        $release = LabelgridRelease::where("release_id", $request->release_id)->first();
        if (!$release) {
            return array("status" => "error", "content" => "Not found $request->release_id");
        }
        $img = str_replace("/labelgrid/", "", $request->release_image_3000);
        $data = (object) [
                    "img" => $img,
                    "contentType" => "image/jpeg",
                    "authorization" => $this->getAuthorization(),
        ];
        $res = RequestHelper::labelgridGetPresignedPut($data);
        if ($res == null || $res == "") {
            return array("status" => "error", "content" => "Presigned error");
        }
        Utils::write("labelgridGetPresignedPut.text", $res);
        $responsePut = json_decode($res);
        $code = RequestHelper::labelgridPutFile($responsePut->url, "/home/automusic.win/public_html/public/$request->release_image_3000", $responsePut->params->ContentType);
        if ($code != 200) {
            return array("status" => "error", "content" => "Put image fail");
        }
        $data2 = (object) [
                    "releaseId" => $request->release_id,
                    "objectKey" => $responsePut->params->Key,
                    "authorization" => $this->getAuthorization(),
        ];
        $res2 = RequestHelper::labelgridFetchS3FileImage($data2);
        Utils::write("labelgridFetchS3FileImage.text", $res2);
        if ($res2 == null || $res2 == "") {
            return array("status" => "error", "content" => "FetchS3File error");
        }
        $responseFetch = json_decode($res2);
        if (!empty($responseFetch->error)) {
            return array("status" => "error", "content" => $responseFetch->error);
        }
        $release->image_local = $request->release_image_3000;
        $release->image_s3 = $responseFetch->location;
        $release->save();
        return array("status" => "success", "content" => "Success");
    }

    public function labelgridAddTrack(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.labelgridAddTrack|request=' . json_encode($request->all()));
        $labelGridArtist = LabelgridArtist::where("artist_id", $request->artist_id)->first();
        if (!$labelGridArtist) {
            return array("status" => "error", "content" => "Artist is not exists");
        }
        $labelGridRelease = LabelgridRelease::where("release_id", $request->release_id)->first();
        if (!$labelGridRelease) {
            return array("status" => "error", "content" => "Release is not exists");
        }
        if ($request->title == "") {
            return array("status" => "error", "content" => "Title is invalid");
        }
        $title = trim($request->title);
        $year = date("Y", time());

        $contris = $request->contri;
        if ($contris[0] == null) {
            return array("status" => "error", "content" => "Contributors cannot be empty");
        }
        $contriData = [];
        foreach ($contris as $contri) {
            $temp = json_decode($contri);
            if ($temp->party_name == "") {
                return array("status" => "error", "content" => "Party Name cannot be empty");
            }
            if (count($temp->indirect_roles) == 0) {
                return array("status" => "error", "content" => "Roles cannot be empty");
            }
            $temp->direct_roles = [];
            $temp->instrument_type = null;
            $temp->display_credits = null;
            $contriData[] = $temp;
        }
        $temps = (object) [
                    "data" => $contriData
        ];

        Log::info(json_encode($temps));
        $data = (object) [
                    "artistId" => $request->artist_id,
                    "artist" => $labelGridArtist->artist_name,
                    "releaseId" => $request->release_id,
                    "title" => $title,
                    "publisherYear" => $year,
                    "copyrightYear" => $year,
                    "cline" => "$year SoundHex",
                    "pline" => "$year SoundHex",
                    "year" => $year,
                    "authorization" => $this->getAuthorization(),
                    "contributors" => $temps,
                    "lyrics" => $request->lyrics
        ];



        $result = RequestHelper::labelgridTrack($data);
        Log::info("labelgridAddTrack $result");
        if ($result != null && $result != "") {
            $output = json_decode($result);
            if (!empty($output->error)) {
                return array("status" => "error", "content" => $output->error);
            }
//            $labelGridId = json_decode($result)->id;
//            $labelgridRelease->release_id = $labelGridId;
//            $labelgridRelease->save();
        }
        return array("status" => "success", "content" => "Success");
    }

    public function getChanelByHandle(Request $request) {
        $platform = $request->header('platform');
        if ($platform != "AutoMusic") {
            return ["message" => "Wrong system!"];
        }
        Log::info('|getChanelByHandle.index|request=' . json_encode($request->all()));
        $channelId = null;
        if (Utils::containString($request->handle, "@")) {
            $channelId = "@" . explode("@", $request->handle)[1];
        } elseif (Utils::containString($request->handle, "/c/")) {
            $channelId = "/c/" . explode("/c/", $request->handle)[1];
        } elseif (Utils::containString($request->handle, "/channel/")) {
            $channelId = explode("/channel/", $request->handle)[1];
        }

        if ($channelId != null) {
            $data = YoutubeHelper::getChannelInfoV2($channelId);
            return $data;
        }
        return 0;
    }

    //ham check claim
    public function checkClaim(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $ids = [];
        $cams = CampaignStatistics::whereIn("status", [1, 4])->get();
        foreach ($cams as $cam) {
            $ids[] = $cam->id;
        }
        if (isset($request->id)) {
            $datas = Campaign::where("id", $request->id)->get();
        } elseif (isset($request->channel_id)) {
            $datas = Campaign::where("channel_id", $request->channel_id)->where("log_claim", "like", "%Expired Cookies%")->get();
        } elseif (isset($request->cookie)) {

            $channel = AccountInfo::where("chanel_id", $request->cookie)->first(["id", "note"]);
            $ch = new ChannelManagementController();
            $cb = "http://automusic.win/callback/sync_cookie?us=$user->user_name";
            $rs = $ch->syncCookie($channel, $cb);
            if ($rs == 1) {
                return response()->json(["status" => "success", "message" => "Send command success, wait for cookies to be synchronized"]);
            }
            return response()->json(["status" => "success", "message" => "Sync cookie fail"]);
        } else {
            $datas = Campaign::where("video_type", "<>", 1)->where("channel_id", "<>", "")->whereIn("campaign_id", $ids)->whereNull("number_claim")->orWhere("log_claim", "like", "%Precheck not Done%")->get();
        }
//        Log::info(json_encode($datas));
        $countMatch = 0;
        $total = (count($datas));
        $i = 0;
//        Log::info(DB::getQueryLog());
        foreach ($datas as $data) {
            $i++;
            $accountInfo = AccountInfo::where("chanel_id", $data->channel_id)->first();
            if (!$accountInfo) {
                continue;
            }
            $campaignStatic = CampaignStatistics::find($data->campaign_id);
            if (!$campaignStatic) {
                continue;
            }

//            Log::info(json_encode($accountInfo));
//            Log::info(json_encode($data));
//            $link = "http://65.109.3.200:5002/copyright/check/SelsevkHaleyz/FtXhR8GGG5A";
            $link = "http://65.109.3.200:5002/copyright/check/$accountInfo->note/$data->video_id";
            error_log("$data->id $link");
            $match = 0;
            $result = RequestHelper::callAPI2("GET", $link, array());
            if ($result != "") {
                foreach ($result->claims as $claim) {
                    if (!empty($claim->asset->metadata->soundRecording->title) && !empty($claim->asset->metadata->soundRecording->artists[0])) {
                        if ($campaignStatic->asset_id == null) {
                            error_log("$data->id " . $claim->asset->metadata->soundRecording->title . " - " . $claim->asset->metadata->soundRecording->artists[0]);
                            if ((strtoupper($claim->asset->metadata->soundRecording->title) == strtoupper($campaignStatic->song_name) || Utils::containString(strtoupper($claim->asset->metadata->soundRecording->title), strtoupper($campaignStatic->song_name))) && (strtoupper($claim->asset->metadata->soundRecording->artists[0]) == strtoupper($campaignStatic->artist))) {
                                $match = 1;
//                            $data->status_confirm = 100;
                                $data->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $data->log = $data->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " system confirm 3";
                                $data->is_match_claim = 1;
                                $data->save();
                                $campaignStatic->asset_id = $claim->assetId;
                                $campaignStatic->save();
                                $countMatch++;
                            }
                        } else {
                            if ($campaignStatic->asset_id == $claim->assetId) {
                                $match = 1;
                                $data->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                                $data->is_match_claim = 1;
                                $data->log = $data->log . PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " system confirm 3";
                                $data->save();
                                $countMatch++;
                            }
                        }
                    }
                }
                error_log("checkClaim $i/$total $data->id claim=" . count($result->claims) . " match=$match");
                $data->number_claim = count($result->claims);
                $data->log_claim = $result->mess;
                $data->save();
                $message = $match == 0 ? "Not Match ($data->number_claim claim)" : "Matched";
            } else {
                $message = "Video Dead";
                $data->number_claim = 0;
                $data->log_claim = "Video Dead";
                $data->save();
                error_log("checkClaim $data->id empty");
            }
            if (isset($request->id)) {
                return response()->json(["status" => $match == 0 ? "error" : "success", "message" => $message]);
            }
        }

        if (isset($request->channel_id)) {
            return response()->json(["status" => "success", "message" => "Match $countMatch/$total"]);
        }
    }

    //cập nhật trạng thái thanh toán
    public function changeStatusPay(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.changeStatusPay|request=' . json_encode($request->all()));
        if (!$request->is_supper_admin) {
            return response()->json(["status" => "error", "message" => "This function is for admins"]);
        }
        $data = CampaignClaimRevStatus::where("period", $request->period)->where("rev_type", $request->rev_type)->first();
        if (!$data) {
            $data = new CampaignClaimRevStatus();
            $data->period = $request->period;
            $data->rev_type = $request->rev_type;
            if ($request->value == 'pay') {
                $data->status = 1;
            }
            if ($request->value == 'show') {
                $data->status_show = 1;
            }
            $data->save();
        } else {
            if ($request->value == 'pay') {
                $data->status = $data->status == 1 ? 0 : 1;
            }
            if ($request->value == 'show') {
                $data->status_show = $data->status_show == 1 ? 0 : 1;
            }
            $data->save();
        }
        return response()->json(["status" => "success"]);
    }

    // <editor-fold defaultstate="collapsed" desc="remove">
//    //2023/02/13 chuc nang bao cao report claim
//    public function reportClaimRevenue(Request $request) {
//        if ($request->is_admin_music) {
//            $condition = "1=1";
//        } else {
//            $condition = "status =1";
//        }
////        $datas = DB::select("select period,count(id) as claim,sum(revenue) * 0.7 as revenue from campaign_claim_rev where type=1 $condition group by period order by period desc");
////        foreach ($datas as $data) {
////            $data->period_text = date("M-Y", strtotime($data->period . "01"));
////        }
//
//        $revenues = CampaignClaimRev::where("type", 1)->whereRaw($condition)->get();
//        $listActiveCampaign = CampaignStatistics::where("type", 2)->get(["id", "artist_percent", "bass_percent", "tax_percent", "cost_percent"]);
//        $listPays = CampaignClaimRevStatus::where("rev_type", 2)->get();
//        $listPeriod = [];
//        $periods = [];
//        foreach ($revenues as $rev) {
//            //trừ thuế
//
//            $rev->period_text = date("M-Y", strtotime($rev->period . "01"));
//
//            if (!in_array($rev->period, $listPeriod)) {
//                $listPeriod[] = $rev->period;
//            }
//            foreach ($listActiveCampaign as $cam) {
//                if ($rev->campaign_id == $cam->id) {
//                    $rev->tax_percent = $cam->tax_percent;
//                    $rev->bass_percent = $cam->bass_percent;
//                    $rev->artist_percent = $cam->artist_percent;
//                    $rev->cost_percent = $cam->cost_percent;
//
//                    $tax = $rev->tax_percent * $rev->revenue / 100;
//                    $afterTax = $rev->revenue - $tax;
//                    $artist = $rev->artist_percent * $afterTax / 100;
//                    $cost = $rev->cost_percent * $afterTax / 100;
//                    $rev->profit = $rev->revenue - ($tax + $artist + $cost);
////                    $afterTax = (100 - $rev->tax_percent) * $rev->revenue / 100;
////                    //profit sau khi trả ca sỹ
////                    $rev->profit = (100 - $rev->artist_percent) * $afterTax / 100;
//                    //bassteam sẽ nhận dc 95% của 30% profit
//                    $rev->bass_money = 0.95 * $rev->profit * $cam->bass_percent / 100;
//                    //devteam sẽ nhận dc 5% của 30% của profit
//                    $rev->dev_money = 0.05 * $rev->profit * $cam->bass_percent / 100;
//                }
//            }
//        }
//        foreach ($listPeriod as $period) {
//            $claim = 0;
//            $revenue = 0;
//            $profit = 0;
//            $bass_money = 0;
//            $dev_money = 0;
//            $paid = 0;
//            $show = 1;
//            foreach ($revenues as $rev) {
//                if ($period == $rev->period) {
//                    $claim++;
//                    $revenue += $rev->revenue;
//                    $profit += $rev->profit;
//                    $bass_money += $rev->bass_money;
//                    $dev_money += $rev->dev_money;
//                }
//            }
//            foreach ($listPays as $pay) {
//                if ($pay->period == $period) {
//                    $paid = $pay->status;
//                    if (!$request->is_admin_music) {
//                        $show = $pay->status_show;
//                    }
//                    break;
//                }
//            }
//            $periods[] = (object) [
//                        "period" => $period,
//                        "period_text" => date("M-Y", strtotime($period . "01")),
//                        "claim" => $claim,
//                        "revenue" => $revenue,
//                        "profit" => $profit,
//                        "bass_revenue" => $bass_money,
//                        "dev_revenue" => $dev_money,
//                        "paid" => $paid,
//                        "show" => $show
//            ];
//        }
//
//        usort($periods, function($a, $b) {
//            return $a->period < $b->period;
//        });
//
//        return array("status" => "success", "report" => $periods);
//    }
//
//    public function getClaimReportRevDetail(Request $request) {
//        $user = Auth::user();
//        Log::info("$user->user_name|ClaimController.getClaimReportUserRevDetail|request=" . json_encode($request->all()));
//        $cacheKey = "claim_rev_$request->period";
//        $listUsers = [];
//        if (Cache::has($cacheKey)) {
//            $listUsers = Cache::get($cacheKey);
//        }
//        $listCamaignObjectForUser = [];
//        if (Cache::has("claim_rev_detail_$request->period")) {
//            $listUsersDetailAll = Cache::get("claim_rev_detail_$request->period");
////            Log::info("listUsersDetailAll" . json_encode($listUsersDetailAll));
//            if (isset($request->username)) {
//                $listCamaignObjectForUser = $listUsersDetailAll["$request->username"];
//            }
//        }
//
//        return array("status" => "success", "users" => $listUsers, "campaigns" => $listCamaignObjectForUser);
//    }
//        //2023/05/29 api lấy views tháng theo user, distributor
//    public function getMonthlyClaimViewsGroupByUserOrDistributor(Request $request) {
//        ini_set('max_execution_time', 0);
//        $startTime = time();
//        DB::enableQueryLog();
//        $user = Auth::user();
//        if ($user == null) {
//            $uName = "system";
//        } else {
//            $uName = $user->user_name;
//        }
//        Log::info(getmypid() . "|" . $uName . '|ClaimController.getMonthlyClaimViewsGroupByUserOrDistributor|request=' . json_encode($request->all()));
//        $listMonth = [];
//        $cacheKey = "monthly_claim_views_$request->period";
////        Cache::forget($cacheKey);
////        if (Cache::has($cacheKey)) {
////            $listMonth = Cache::get($cacheKey);
////        } else {
//
//        $countVideosByMonth = DB::select("select d.username,SUBSTRING(d.insert_date, 1, 6) as insert_month,count(*) as videos from 
//                                            (select distinct a.video_id, a.username ,a.insert_date from campaign a
//                                            where a.video_type <> 1 and a.status_confirm =3 
//                                            and a.campaign_id in (SELECT id FROM `campaign_statistics` WHERE `type` = 2 and status in (1,4))) d
//                                            group by d.username,insert_month");
//        $listUsers = User::where("status", 1)->whereRaw(DB::raw("(role like '%16%')"))->get(["user_name"]);
//        $listVideos = DB::select("select username,campaign_id,video_type,video_id,number_claim,SUBSTRING(insert_date, 1, 6) as insert_month from campaign where video_type <> 1 and status_confirm =3                           
//and campaign_id in (SELECT id FROM `campaign_statistics` WHERE `type` = 2 and status in (1,4))
//                                    group by username,campaign_id,video_type,video_id,number_claim,insert_date");
//
//        $distributors = DB::select("SELECT distributor,id FROM campaign_statistics WHERE status IN (1,4) AND type = '2'");
//        $revenues = DB::select("select period,distributor, sum(revenue) as revenue from campaign_claim_rev where type =1 group by period,distributor");
//        $listDistributor = [];
//        foreach ($distributors as $dis) {
//            if (!in_array($dis->distributor, $listDistributor)) {
//                $listDistributor[] = $dis->distributor;
//            }
//        }
//
//        $currentPeriod = time();
//
//        if (isset($request->period)) {
//            $currentPeriod = strtotime($request->period . "01");
//        }
//
//        //tìm danh sách các ngày đàu tháng tính từ period truyền vào
//        $months = [];
//        $months25 = [];
//        $curMonth = gmdate("m", $currentPeriod);
//        $curYear = gmdate("Y", $currentPeriod);
//        $curNextMonth = $curMonth + 1;
//        $lastRage = $curNextMonth - 3;
//
//        for ($i = $curNextMonth; $i >= $lastRage; $i--) {
//            $m = $i;
//            $m25 = $i - 1;
//            $name = $curMonth - $i;
//
//            $y = $curYear;
//            $y25 = $curYear;
//            if ($m <= 0) {
//                $m = 12 + $m;
//                $y = $curYear - 1;
//            } else if ($m > 12) {
//                $m = $m - 12;
//                $y = $curYear + 1;
//            }
//            if ($m25 <= 0) {
//                $m25 = 12 + $m25;
//                $y25 = $curYear - 1;
//            }
//            if ($m < 10) {
//                $m = "0$m";
//            }
//            if ($m25 < 10) {
//                $m25 = "0$m25";
//            }
//            if ($name == -1) {
//                $var = "monthPlus1";
//            } else {
//                $var = "month$name";
//            }
//            $months[] = "\"$var\":\"$y$m" . "01\"";
//            $months25[] = "\"$var\":\"$y25$m25" . "25\"";
//        }
//
//        $temp = "{" . implode(",", $months) . "}";
//        $temp25 = "{" . implode(",", $months25) . "}";
////        Log::info($request->period);
////            Log::info($temp);
////        Log::info($temp25);
//        //lấy view từ  01 -> 01
//        $monthJson = json_decode($temp);
//        $viewMonth_2 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson->month2 and date <= $monthJson->month1 group by video_id");
//        $viewMonth_1 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson->month1 and date <= $monthJson->month0 group by video_id");
//        $viewMonth_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson->month0 and date <= $monthJson->monthPlus1 group by video_id");
//
//        //lấy view từ 25 -> 25
//        $monthJson25 = json_decode($temp25);
//        $viewMonth25_2 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson25->month2 and date <= $monthJson25->month1 group by video_id");
//        $viewMonth25_1 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson25->month1 and date <= $monthJson25->month0 group by video_id");
//        $viewMonth25_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson25->month0 and date <= $monthJson25->monthPlus1 group by video_id");
//        $viewMonths = [$viewMonth_0, $viewMonth_1, $viewMonth_2];
//        $viewMonths25 = [$viewMonth25_0, $viewMonth25_1, $viewMonth25_2];
////        Log::info(DB::getQueryLog());
//        Log::info(getmypid() . "|" . $uName . '|ClaimController.getMonthlyClaimViewsGroupByUserOrDistributor|loadDB=' . (time() - $startTime));
//        $countviewMonth_0 = count($viewMonth_0);
//        $countviewMonth_1 = count($viewMonth_1);
//        $countviewMonth_2 = count($viewMonth_2);
//        $countListVideos = count($listVideos);
//        //thêm views của các tháng vào listvideos
//        $start1 = time();
//        foreach ($viewMonths as $index => $vm) {
//            $listCheck = [];
//            $listCheck25 = [];
//            foreach ($listVideos as $video) {
//                $key_views_money_dis = "views_money_dis_$index";
//                $key_views_total_dis = "views_total_dis_$index";
//
//                $key_views_money = "views_money_$index";
//                $key_views_total = "views_total_$index";
//                $key_views_mix = "views_mix_$index";
//                $key_views_lyric = "views_lyric_$index";
//                $key_views_short = "views_short_$index";
//
//
//                $key_views_money25_dis = "views_money25_dis_$index";
//                $key_views_total25_dis = "views_total25_dis_$index";
//
//                $key_views_money25 = "views_money25_$index";
//                $key_views_total25 = "views_total25_$index";
//                $key_views_mix25 = "views_mix25_$index";
//                $key_views_lyric25 = "views_lyric25_$index";
//                $key_views_short25 = "views_short25_$index";
//
//
//                $video->$key_views_money_dis = 0;
//                $video->$key_views_total_dis = 0;
//
//                $video->$key_views_money = 0;
//                $video->$key_views_total = 0;
//                $video->$key_views_mix = 0;
//                $video->$key_views_lyric = 0;
//                $video->$key_views_short = 0;
//
//                $video->$key_views_money25_dis = 0;
//                $video->$key_views_total25_dis = 0;
//
//                $video->$key_views_money25 = 0;
//                $video->$key_views_total25 = 0;
//                $video->$key_views_mix25 = 0;
//                $video->$key_views_lyric25 = 0;
//                $video->$key_views_short25 = 0;
//                foreach ($vm as $view) {
//                    if ($video->video_id == $view->video_id) {
//                        //lấy views để tổng hợp cho user
//                        if (!in_array($video->video_id, $listCheck)) {
//                            $video->$key_views_total = $view->views;
//
//                            if ($video->video_type == 2) {
//                                $video->$key_views_lyric = $view->views;
//                            } else if ($video->video_type == 5) {
//                                $video->$key_views_mix = $view->views;
//                            } else if ($video->video_type == 6) {
//                                $video->$key_views_short = $view->views;
//                            }
//                            $listCheck[] = $video->video_id;
//                        }
//                        //trong listvideo trùng, views money phải tính hết kể cả video trùng nhưng khác campaign_id, views total chỉ tính 1 video duy nhất
//                        if ($video->number_claim == 0 || $video->number_claim == null) {
//                            $video->$key_views_money = $view->views;
//                        } else {
//                            $video->$key_views_money = round(1 / $video->number_claim * $view->views);
//                        }
//                        //lấy views để tổng hợp cho distributor
//                        $video->$key_views_total_dis = $view->views;
//                        if ($video->number_claim == 0 || $video->number_claim == null) {
//                            $video->$key_views_money_dis = $view->views;
//                        } else {
//                            $video->$key_views_money_dis = round(1 / $video->number_claim * $view->views);
//                        }
//                    }
//                }
//                foreach ($viewMonths25[$index] as $view) {
//                    if ($video->video_id == $view->video_id) {
//                        //lấy views để tổng hợp cho user
//                        if (in_array($video->video_id, $listCheck25)) {
//                            $video->$key_views_total25 = $view->views;
//
//                            if ($video->video_type == 2) {
//                                $video->$key_views_lyric25 = $view->views;
//                            } else if ($video->video_type == 5) {
//                                $video->$key_views_mix25 = $view->views;
//                            } else if ($video->video_type == 6) {
//                                $video->$key_views_short25 = $view->views;
//                            }
//                            $listCheck25[] = $video->video_id;
//                        }
//                        if ($video->number_claim == 0 || $video->number_claim == null) {
//                            $video->$key_views_money25 = $view->views;
//                        } else {
//                            $video->$key_views_money25 = round(1 / $video->number_claim * $view->views);
//                        }
//
//                        //lấy views để tổng hợp cho distributor
//                        $video->$key_views_total25_dis = $view->views;
//                        if ($video->number_claim == 0 || $video->number_claim == null) {
//                            $video->$key_views_money25_dis = $view->views;
//                        } else {
//                            $video->$key_views_total25_dis = round(1 / $video->number_claim * $view->views);
//                        }
//                    }
//                }
//
//
//                //thêm thông tin distributor vào listvideo
//                foreach ($distributors as $dis) {
//                    if ($video->campaign_id == $dis->id) {
//                        $video->distributor = $dis->distributor;
//                        break;
//                    }
//                }
//            }
//        }
//        Log::info(getmypid() . "|" . $uName . '|ClaimController.getMonthlyClaimViewsGroupByUserOrDistributor|for $viewMonths ,$listVideos=' . "$countListVideos,vm0=$countviewMonth_0,vm1=$countviewMonth_1,vm2=$countviewMonth_2," . 'time=' . (time() - $start1));
//        // <editor-fold defaultstate="collapsed" desc="debug">
////        Utils::write("list_videos_claim.txt", json_encode($listVideos));
////            $listLog = [];
////            foreach ($listVideos as $video) {
////                if ($video->username == 'thuymusic' && $video->insert_month == '202308') {
////                    $listLog[] = $video;
////                }
////            }
////            //log check
////            usort($listLog, function($a, $b) {
////                return $a->views_total_0 < $b->views_total_0;
////            });
////            Utils::write("list_videos_claim_log.txt", json_encode($listLog));
//// </editor-fold>
//        //thêm views của của các tháng vào listuser,thêm listuser vào listmonth
//        $start2 = time();
//        for ($index = 0; $index < 3; $index++) {
//            $key_views_money_dis = "views_money_dis_$index";
//            $key_views_total_dis = "views_total_dis_$index";
//
//            $key_views_money = "views_money_$index";
//            $key_views_total = "views_total_$index";
//            $key_views_mix = "views_mix_$index";
//            $key_views_lyric = "views_lyric_$index";
//            $key_views_short = "views_short_$index";
//
//            $key_views_money25_dis = "views_money25_dis_$index";
//            $key_views_total25_dis = "views_total25_dis_$index";
//
//            $key_views_money25 = "views_money25_$index";
//            $key_views_total25 = "views_total25_$index";
//            $key_views_mix25 = "views_mix25_$index";
//            $key_views_lyric25 = "views_lyric25_$index";
//            $key_views_short25 = "views_short25_$index";
//
//            $key_month = "month" . $index;
//            $currMonth = gmdate("Ym", strtotime($monthJson->$key_month));
//
//            $viewTotalUserSum = 0;
//            $videoTotalUserSum = 0;
//            foreach ($listUsers as $manager) {
////                $manager->period = gmdate("Y/m", strtotime("-$index months"));
//                $manager->views_money = 0;
//                $manager->views_total = 0;
//                $manager->views_mix = 0;
//                $manager->views_lyric = 0;
//                $manager->views_short = 0;
//                $listCheck = [];
//                foreach ($listVideos as $video) {
////                    if ($manager->user_name == $video->username && !in_array($video->video_id, $listCheck)) {
//                    if ($manager->user_name == $video->username) {
//                        $viewTotalUserSum += $video->$key_views_total;
//                        $manager->views_money += $video->$key_views_money;
//                        $manager->views_total += $video->$key_views_total;
//                        $manager->views_mix += $video->$key_views_mix;
//                        $manager->views_lyric += $video->$key_views_lyric;
//                        $manager->views_short += $video->$key_views_short;
////                        $listCheck[] = $video->video_id;
//                    }
//                }
//                //đếm số video dc upload trong 1 tháng
//                $videoSum = 0;
//                foreach ($countVideosByMonth as $countVideo) {
//                    if ($currMonth == $countVideo->insert_month && $manager->user_name == $countVideo->username) {
//                        $videoSum = $countVideo->videos;
//                        $videoTotalUserSum += $countVideo->videos;
//                    }
//                }
//                $manage[] = (object) [
//                            "object" => $manager->user_name,
//                            "views_money" => $manager->views_money,
//                            "views_total" => $manager->views_total,
//                            "views_mix" => $manager->views_mix,
//                            "views_lyric" => $manager->views_lyric,
//                            "views_short" => $manager->views_short,
//                            "videos" => $videoSum
//                ];
//            }
//
//
//            //distributor
//            $viewTotalDisSum = 0;
//            $revenueDisSum = 0;
//            $profitDisSum = 0;
//            $bassDisSum = 0;
//            foreach ($listDistributor as $data) {
//                $viewMoneyDis = 0;
//                $viewTotalDis = 0;
//                $numberVideoDis = 0;
//                $revenueDis = 0;
//                $profitDis = 0;
//                $bassDis = 0;
//
//                foreach ($listVideos as $video) {
//                    if ($data == $video->distributor) {
////                        Log::info(json_encode($video));
//                        $viewTotalDisSum += $data == "AdRev" ? $video->$key_views_total25_dis : $video->$key_views_total_dis;
//                        $viewMoneyDis += $data == "AdRev" ? $video->$key_views_money25_dis : $video->$key_views_money_dis;
//                        $viewTotalDis += $data == "AdRev" ? $video->$key_views_total25_dis : $video->$key_views_total_dis;
////                        if ($video->video_id == "GwyOa_3WktY") {
////                            Log::info("$index $data $video->video_id " . $video->$key_views_total);
////                        }
//                        if ($currMonth == $video->insert_month) {
//                            $numberVideoDis++;
//                        }
//                    }
//                }
//                $percents[] = (object) ["name" => "Orchard", "tax" => 30, "artist" => 20, "cost" => 10, "bass" => 30];
//                $percents[] = (object) ["name" => "AdRev", "tax" => 30, "artist" => 0, "cost" => 20, "bass" => 30];
//                $percents[] = (object) ["name" => "Cygnus", "tax" => 0, "artist" => 35, "cost" => 10, "bass" => 30];
//                $percents[] = (object) ["name" => "Tunecore_Heddo", "tax" => 0, "artist" => 30, "cost" => 10, "bass" => 30];
//                $percents[] = (object) ["name" => "Indiy", "tax" => 0, "artist" => 30, "cost" => 10, "bass" => 30];
//
//
//                foreach ($revenues as $rev) {
//                    if ($currMonth == $rev->period && $data == $rev->distributor) {
//                        $revenueDis = $rev->revenue;
//                        foreach ($percents as $percent) {
//
//                            if ($percent->name == $rev->distributor) {
//                                $tax = $percent->tax * $rev->revenue / 100;
//                                $afterTax = $rev->revenue - $tax;
//                                $artist = $percent->artist * $afterTax / 100;
//                                $cost = $percent->cost * $afterTax / 100;
//                                $profitDis = $rev->revenue - ($tax + $artist + $cost);
//                                $bassDis = $percent->bass * $profitDis / 100;
//                                break;
//                            }
//                        }
//                        break;
//                    }
//                }
//
//                $revenueDisSum += $revenueDis;
//                $profitDisSum += $profitDis;
//                $bassDisSum += $bassDis;
//                $distributorViews[] = (object) [
//                            "object" => $data,
//                            "views_money" => $viewMoneyDis,
//                            "views_total" => $viewTotalDis,
//                            "videos" => $numberVideoDis,
//                            "revenue" => $revenueDis,
//                            "profit" => $profitDis,
//                            "bass" => $bassDis
//                ];
//            }
//            $listMonth[] = (object) [
//                        "period" => gmdate("Ym", strtotime($monthJson->$key_month)),
//                        "month" => gmdate("M-Y", strtotime($monthJson->$key_month)),
//                        "user" => $manage,
//                        "distributor" => $distributorViews,
//                        "distributor_views_sum" => $viewTotalDisSum,
//                        "user_views_sum" => $viewTotalUserSum,
//                        "user_video_sum" => $videoTotalUserSum,
//                        "revenue_sum" => $revenueDisSum,
//                        "profit_sum" => $profitDisSum,
//                        "bass_sum" => $bassDisSum,
//            ];
//            $manage = [];
//            $distributorViews = [];
//        }
//        Log::info(getmypid() . "|" . $uName . '|ClaimController.getMonthlyClaimViewsGroupByUserOrDistributor|for list3month ' . (time() - $start2));
//
//        //ắp xếp listusers trong listmonth
//        foreach ($listMonth as $month) {
//            $d = $month->user;
//
//
//            usort($d, function($a, $b) {
//                return $a->views_total < $b->views_total;
//            });
//            $month->user = $d;
//            $month->cache = Utils::timeToStringGmT7(time());
//        }
//
//        //lưu dữ liệu vào cache 5 tháng
//        Cache::put($cacheKey, $listMonth, 12960000);
//        Log::info(getmypid() . "|" . $uName . '|ClaimController.getMonthlyClaimViewsGroupByUserOrDistributor|cached ' . $cacheKey . ' ' . (time() - $startTime));
////        }
//
//
//        return array("list_month" => $listMonth);
//    }
//
//    //2023/12/23 tự động cache dữ liệu getMonthlyClaimViewsGroupByUserOrDistributor
//    public function cacheAddGetMonthlyClaimViewsGroupByUserOrDistributor(Request $request) {
//        $currentPeriod = time();
//        if (isset($request->period)) {
//            $currentPeriod = strtotime($request->period . "01");
//        }
//
//        // <editor-fold defaultstate="collapsed" desc="cache monthly views by user and distributor">
//
//        $months1 = [];
//        $curMonth1 = gmdate("m", $currentPeriod);
//        $curYear1 = gmdate("Y", $currentPeriod);
//        $y = (int) $curYear1;
//        $lastRage1 = (int) $curMonth1 - 8;
//        for ($i = (int) $curMonth1; $i >= $lastRage1; $i--) {
//            $m = (int) $i;
//            if ($m <= 0) {
//                $m = 12 + $m;
//                $y = $curYear1 - 1;
//            } else if ($m > 12) {
//                $m = $m - 12;
//                $y = $curYear1 + 1;
//            }
//            if ($m == 0) {
//                $m = '01';
//            } else {
//                if ($m < 10) {
//                    $m = "0$m";
//                }
//            }
//
//            $months1[] = "$y$m";
//        }
//
//        foreach ($months1 as $month) {
//            $req = new Request();
//            $req["period"] = $month;
//            $this->getMonthlyClaimViewsGroupByUserOrDistributor($req);
//        }
//        Log::info("finish cache views by user " . (time() - $currentPeriod) . "s " . json_encode($months1));
//// </editor-fold>
//        // <editor-fold defaultstate="collapsed" desc="cache montly views, monthly count video by claim">
//        $datas = CampaignStatistics::where("type", 2)->orderBy("id", "desc")->get();
//        $distributors = DB::select("SELECT distributor,id FROM campaign_statistics WHERE status IN (1,4) AND type = '2'");
//        $listDistributor = [];
//        foreach ($distributors as $dis) {
//            if (!in_array($dis->distributor, $listDistributor)) {
//                $listDistributor[] = $dis->distributor;
//            }
//        }
//
//        $listVideos = DB::select("select username,campaign_id,video_type,video_id,number_claim from campaign where video_type <> 1 and status_confirm =3 
//                                    and campaign_id in (SELECT id FROM `campaign_statistics` WHERE `type` = '2' and status in (1,4))
//                                    group by username,campaign_id,video_type,video_id,number_claim");
//
//
//        //tìm danh sách các ngày đàu tháng tính từ period truyền vào
//        $months = [];
//        $months25 = [];
//        $curMonth = gmdate("m", time());
//        $curYear = gmdate("Y", time());
//        $curNextMonth = $curMonth + 1;
//        $lastRage = $curNextMonth - 1;
//        for ($i = $curNextMonth; $i >= $lastRage; $i--) {
////            Log::info($i);
//            $m = $i;
//            $m25 = $i - 1;
//            $name = $curMonth - $i;
//
//            $y = $curYear;
//            if ($i <= 0) {
//                $m = 12 + $i;
//                $m25 = 12 + $i;
//                $y = $curYear - 1;
//            }
//            if ($m < 10) {
//                $m = "0$m";
//                $m25 = "0$m25";
//            }
//            if ($name == -1) {
//                $var = "monthPlus1";
//            } else {
//                $var = "month$name";
//            }
//            $months[] = "\"$var\":\"$y$m" . "01\"";
//            $months25[] = "\"$var\":\"$y$m25" . "25\"";
//        }
//
//        $temp = "{" . implode(",", $months) . "}";
//        $temp25 = "{" . implode(",", $months25) . "}";
//        $monthJson = json_decode($temp);
//        $monthJson25 = json_decode($temp25);
//
//
//
//        $viewMonth_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date >= $monthJson->month0 and date < $monthJson->monthPlus1 group by video_id");
//        $viewMonth25_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date >= $monthJson25->month0 and date < $monthJson25->monthPlus1 group by video_id");
//        $viewMonths = [$viewMonth_0];
//        $viewMonths25 = [$viewMonth25_0];
//        foreach ($viewMonths as $index => $vm) {
//            $listCheck = [];
//            $listCheck25 = [];
//            foreach ($listVideos as $video) {
//                $key_views_money = "views_money_$index";
//                $key_views_total = "views_total_$index";
//                $key_views_mix = "views_mix_$index";
//                $key_views_lyric = "views_lyric_$index";
//                $key_views_short = "views_short_$index";
//
//                $key_views_money25 = "views_money25_$index";
//                $key_views_total25 = "views_total25_$index";
//                $key_views_mix25 = "views_mix25_$index";
//                $key_views_lyric25 = "views_lyric25_$index";
//                $key_views_short25 = "views_short25_$index";
//
//                $video->$key_views_money = 0;
//                $video->$key_views_total = 0;
//                $video->$key_views_mix = 0;
//                $video->$key_views_lyric = 0;
//                $video->$key_views_short = 0;
//
//                $video->$key_views_money25 = 0;
//                $video->$key_views_total25 = 0;
//                $video->$key_views_mix25 = 0;
//                $video->$key_views_lyric25 = 0;
//                $video->$key_views_short25 = 0;
//                foreach ($vm as $view) {
//                    if ($video->video_id == $view->video_id
////                                && !in_array($video->video_id, $listCheck)
//                    ) {
//                        $video->$key_views_total = $view->views;
//                        if ($video->number_claim == 0 || $video->number_claim == null) {
//                            $video->$key_views_money = $view->views;
//                        } else {
//                            $video->$key_views_money = round(1 / $video->number_claim * $view->views);
//                        }
//                        if ($video->video_type == 2) {
//                            $video->$key_views_lyric = $view->views;
//                        } else if ($video->video_type == 5) {
//                            $video->$key_views_mix = $view->views;
//                        } else if ($video->video_type == 6) {
//                            $video->$key_views_short = $view->views;
//                        }
//                        $listCheck[] = $video->video_id;
//                    }
//                }
//                foreach ($viewMonths25[$index] as $view) {
//                    if ($video->video_id == $view->video_id
////                                && !in_array($video->video_id, $listCheck25)
//                    ) {
//                        $video->$key_views_total25 = $view->views;
//                        if ($video->number_claim == 0 || $video->number_claim == null) {
//                            $video->$key_views_money25 = $view->views;
//                        } else {
//                            $video->$key_views_money25 = round(1 / $video->number_claim * $view->views);
//                        }
//                        if ($video->video_type == 2) {
//                            $video->$key_views_lyric25 = $view->views;
//                        } else if ($video->video_type == 5) {
//                            $video->$key_views_mix25 = $view->views;
//                        } else if ($video->video_type == 6) {
//                            $video->$key_views_short25 = $view->views;
//                        }
//                        $listCheck25[] = $video->video_id;
//                    }
//                }
//
//
//                //thêm thông tin distributor vào listvideo
//                foreach ($distributors as $dis) {
//                    if ($video->campaign_id == $dis->id) {
//                        $video->distributor = $dis->distributor;
//                        break;
//                    }
//                }
//            }
//        }
//
//        foreach ($datas as $data) {
//
//            //2023/05/31 views claim theo tháng
//            $data->monthly_view_total = 0;
//            $data->monthly_views_mix = 0;
//            $data->monthly_views_lyric = 0;
//            $data->monthly_views_short = 0;
//            foreach ($listVideos as $video) {
//                if ($data->id == $video->campaign_id) {
//                    $data->monthly_view_total += $data->distributor == "AdRev" ? $video->views_total25_0 : $video->views_total_0;
//                    $data->monthly_views_mix += $data->distributor == "AdRev" ? $video->views_mix25_0 : $video->views_mix_0;
//                    $data->monthly_views_lyric += $data->distributor == "AdRev" ? $video->views_lyric25_0 : $video->views_lyric_0;
//                    $data->monthly_views_short += $data->distributor == "AdRev" ? $video->views_short25_0 : $video->views_short_0;
//                }
//            }
//        }
//
//        Cache::put("cache_list_claim_with_views_month", $datas, 5000);
//        Log::info("finish cache views by claim " . (time() - $currentPeriod) . "s");
//
//// // </editor-fold>
//        return 1;
//    }
//
//    public function cacheLoadGetMonthlyClaimViewsGroupByUserOrDistributor(Request $request) {
//        $listMonth = [];
//        $cacheKey = "monthly_claim_views_$request->period";
//        if (Cache::has($cacheKey)) {
//            $listMonth = Cache::get($cacheKey);
//        }
//        return array("list_month" => $listMonth);
//    }
//
//    public function cacheClearGetMonthlyClaimViewsGroupByUserOrDistributor(Request $request) {
//        $cacheKey = "monthly_claim_views_$request->period";
//        Cache::forget($cacheKey);
//        return 1;
//    }
//
//    //2023/01/15 cache báo cáo claim
//    public function cacheAddRevenueClaim(Request $request) {
//        ini_set('max_execution_time', 0);
//        Log::info("cacheAddRevenueClaim|pid=" . getmypid() . "|request=" . json_encode($request->all()));
//
//        $startTime = time();
//        $listUsers = User::where("status", 1)->whereRaw(DB::raw("(role like '%16%' or role like '%24%')"))->get(["user_name", DB::raw("CASE WHEN role like '%16%' THEN 'BASS' WHEN role like '%24%' THEN 'DEVTOP' END as role")]);
////                                    and custom_genre not like '%black%' 
//        $listVideos = DB::select("select username,campaign_id,video_type,video_id,number_claim from campaign where video_type <> 1 and status_confirm =3 
//                                    and campaign_id in (SELECT id FROM `campaign_statistics` WHERE `type` = '2' and status in (1,4) 
//                                    )
//                                    group by username,campaign_id,video_type,video_id,number_claim ");
//        $distributors = DB::select("SELECT distributor,id FROM campaign_statistics WHERE status IN (1,4) AND type = '2'");
//        $listDistributor = [];
//        foreach ($distributors as $dis) {
//            if (!in_array($dis->distributor, $listDistributor)) {
//                $listDistributor[] = $dis->distributor;
//            }
//        }
//
//        //từ period truyền vào,tính ra ngày đầu và ngày cuối lấy view, 202305 -> 20230501-20230601 và 20230425-20230525
//        $currentPeriod = time();
//        $period = gmdate("Ym", time());
//        if (isset($request->period)) {
//            $currentPeriod = strtotime($request->period . "01");
//            $period = $request->period;
//        }
//        Cache::forget("claim_rev_detail_$period");
//        $curMonth = gmdate("m", $currentPeriod);
//        $curYear = gmdate("Y", $currentPeriod);
//        $months = [];
//        $months25 = [];
//        $curNextMonth = $curMonth + 1;
//        $lastRage = $curNextMonth - 1;
//        for ($i = $curNextMonth; $i >= $lastRage; $i--) {
//            $m = $i;
//            $m25 = $i - 1;
//            $name = $curMonth - $i;
//
//            $y = $curYear;
//            $y25 = $curYear;
//            if ($m <= 0) {
//                $m = 12 + $m;
//                $y = $curYear - 1;
//            } else if ($m > 12) {
//                $m = $m - 12;
//                $y = $curYear + 1;
//            }
//            if ($m25 <= 0) {
//                $m25 = 12 + $m25;
//                $y25 = $curYear - 1;
//            }
//            if ($m < 10) {
//                $m = "0$m";
//            }
//            if ($m25 < 10) {
//                $m25 = "0$m25";
//            }
//            if ($name == -1) {
//                $var = "monthPlus1";
//            } else {
//                $var = "month$name";
//            }
//            $months[] = "\"$var\":\"$y$m" . "01\"";
//            $months25[] = "\"$var\":\"$y25$m25" . "25\"";
//        }
//
//        $temp = "{" . implode(",", $months) . "}";
//        $temp25 = "{" . implode(",", $months25) . "}";
//        $monthJson = json_decode($temp);
//        $monthJson25 = json_decode($temp25);
////        Log::info(json_encode($monthJson));
////        Log::info(json_encode($monthJson25));
//        $viewMonth_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson->month0 and date <= $monthJson->monthPlus1 group by video_id");
//        $viewMonth25_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $monthJson25->month0 and date <= $monthJson25->monthPlus1 group by video_id");
//
//        $listCampaignId = [];
//        $listCampaignObject = [];
//        foreach ($listVideos as $video) {
//            if (!in_array($video->campaign_id, $listCampaignId)) {
//                $listCampaignId[] = $video->campaign_id;
//                $listCampaignObject[] = (object) ["campaign_id" => $video->campaign_id];
//            }
//            $key_views_money = "views_money";
//            $key_views_total = "views_total";
//            $key_views_mix = "views_mix";
//            $key_views_lyric = "views_lyric";
//            $key_views_short = "views_short";
//
//            $key_views_money25 = "views_money25";
//            $key_views_total25 = "views_total25";
//            $key_views_mix25 = "views_mix25";
//            $key_views_lyric25 = "views_lyric25";
//            $key_views_short25 = "views_short25";
//
//            $video->$key_views_money = 0;
//            $video->$key_views_total = 0;
//            $video->$key_views_mix = 0;
//            $video->$key_views_lyric = 0;
//            $video->$key_views_short = 0;
//
//            $video->$key_views_money25 = 0;
//            $video->$key_views_total25 = 0;
//            $video->$key_views_mix25 = 0;
//            $video->$key_views_lyric25 = 0;
//            $video->$key_views_short25 = 0;
//            foreach ($viewMonth_0 as $view) {
//                if ($video->video_id == $view->video_id) {
//                    $video->$key_views_total = $view->views;
//                    if ($video->number_claim == 0 || $video->number_claim == null) {
//                        $video->$key_views_money = $view->views;
//                    } else {
//                        $video->$key_views_money = round(1 / $video->number_claim * $view->views);
//                    }
//                    if ($video->video_type == 2) {
//                        $video->$key_views_lyric = $view->views;
//                    } else if ($video->video_type == 5) {
//                        $video->$key_views_mix = $view->views;
//                    } else if ($video->video_type == 6) {
//                        $video->$key_views_short = $view->views;
//                    }
//                }
//            }
//            //view từ ngày 25 - 25  dành cho distributor AdRev(labelgrid)
//            foreach ($viewMonth25_0 as $view) {
//                if ($video->video_id == $view->video_id) {
//                    $video->$key_views_total25 = $view->views;
//                    if ($video->number_claim == 0 || $video->number_claim == null) {
//                        $video->$key_views_money25 = $view->views;
//                    } else {
//                        $video->$key_views_money25 = round(1 / $video->number_claim * $view->views);
//                    }
//                    if ($video->video_type == 2) {
//                        $video->$key_views_lyric25 = $view->views;
//                    } else if ($video->video_type == 5) {
//                        $video->$key_views_mix25 = $view->views;
//                    } else if ($video->video_type == 6) {
//                        $video->$key_views_short25 = $view->views;
//                    }
//                }
//            }
//
//
//            //thêm thông tin distributor vào listvideo
//            foreach ($distributors as $dis) {
//                if ($video->campaign_id == $dis->id) {
//                    $video->distributor = $dis->distributor;
//                    break;
//                }
//            }
//        }
//
//        //tính các khoản cần pay
//        $revenue = CampaignClaimRev::where("period", $period)->where("type", 1)->get();
//        $listActiveCampaign = CampaignStatistics::where("type", 2)->get(["id", "artist_percent", "bass_percent", "tax_percent", "cost_percent"]);
//        foreach ($revenue as $rev) {
//            //trừ thuế 30%
////            $rev->profit = $rev->revenue * 70 / 100;
//            foreach ($listActiveCampaign as $cam) {
//                if ($rev->campaign_id == $cam->id) {
//                    $rev->tax_percent = $cam->tax_percent;
//                    $rev->bass_percent = $cam->bass_percent;
//                    $rev->artist_percent = $cam->artist_percent;
//                    $rev->cost_percent = $cam->cost_percent;
//
//                    $tax = $rev->tax_percent * $rev->revenue / 100;
//                    $afterTax = $rev->revenue - $tax;
//                    $artist = $rev->artist_percent * $afterTax / 100;
//                    $cost = $rev->cost_percent * $afterTax / 100;
//                    $rev->profit = $rev->revenue - ($tax + $artist + $cost);
//
//                    //bassteam sẽ nhận dc 95% của 30% profit
//                    $rev->bass_money = 0.95 * $rev->profit * $cam->bass_percent / 100;
//                    //devteam sẽ nhận dc 5% của 30% của profit
//                    $rev->dev_money = 0.05 * $rev->profit * $cam->bass_percent / 100;
//                }
//            }
//        }
//
//        //tính tổng view_money,view tổng của từng campaign
//        foreach ($listCampaignObject as $cam) {
////            $cam->username = $request->username;
//            $cam->views_money = 0;
//            $cam->views_lyric = 0;
//            $cam->views_mix = 0;
//            $cam->views_short = 0;
//            $cam->views_total = 0;
//            foreach ($listVideos as $video) {
//                if ($cam->campaign_id == $video->campaign_id) {
//                    $cam->views_money += $video->distributor == "AdRev" ? $video->views_money25 : $video->views_money;
//                    $cam->views_lyric += $video->distributor == "AdRev" ? $video->views_lyric25 : $video->views_lyric;
//                    $cam->views_mix += $video->distributor == "AdRev" ? $video->views_mix25 : $video->views_mix;
//                    $cam->views_short += $video->distributor == "AdRev" ? $video->views_short25 : $video->views_short;
//                    $cam->views_total += $video->distributor == "AdRev" ? $video->views_total25 : $video->views_total;
//                }
//            }
//        }
//
//
//        foreach ($listVideos as $video) {
//            //view money chính xác
//            $video->views_money_real = 0;
//            //tính % money được nhận dựa vào số lượng view_money / tổng view_money
//            $video->views_per = 0;
//            $video->campaign_view = 0;
//            foreach ($listCampaignObject as $cam) {
//                if ($video->campaign_id == $cam->campaign_id) {
//                    if ($cam->views_money != 0) {
//                        $video->views_money_real = $video->distributor == "AdRev" ? $video->views_money25 : $video->views_money;
//                        //% views của mỗi video / tổng views của 1 bài claim
//                        $video->views_per = ($video->distributor == "AdRev" ? $video->views_money25 : $video->views_money) / $cam->views_money;
//                        //lưu lại tổng views money của 1 bài claim trên mỗi video
//                        $video->views_money_campaign_real = $cam->views_money;
//                    }
//                }
//            }
//            //lưu lại số tiền sẽ nhận được của mỗi campaign
//            $video->total_money = 0;
//            $video->bass_money = 0;
//            $video->dev_money = 0;
//            foreach ($revenue as $rev) {
//                if ($video->campaign_id == $rev->campaign_id) {
//                    //số tiền bassteam sẽ được nhận
//                    $video->bass_money = $rev->bass_money;
//                    //số tiền devteam sẽ được nhận
//                    $video->dev_money = $rev->dev_money;
//                    //tổng số tiền sẽ chi trả cho bassteam và devteam của mỗi campaign
//                    $video->total_money = $rev->bass_money + $rev->dev_money;
//                }
//            }
//        }
////        usort($listVideos, function($a, $b) {
////                return $a->views_total < $b->views_total;
////            });
////        $test = [];
////        $test2 = [];
////        foreach ($listVideos as $video) {
////            if($video->username=='ketmusic' && $video->campaign_id==776){
////                if($video->distributor=='AdRev'){
////                    $test[]=$video;
////                }else{
////                    $test2[]=$video;
////                }
////            }
////        }
////         Utils::write("listvideo.txt", json_encode($listVideos));
////         Utils::write("listvideo1.txt", json_encode($test));
////         Utils::write("listvideo2.txt", json_encode($test2));
//
//        foreach ($listUsers as $manager) {
//            $manager->period = $period;
//            $manager->views_money = 0;
//            $manager->money = 0;
//            if ($manager->role == "BASS") {
//                foreach ($listVideos as $video) {
//                    if ($manager->user_name == $video->username) {
//                        $manager->views_money += $video->views_money_real;
//                        $manager->money += $video->views_per * $video->bass_money;
//                    }
//                }
//            } else if ($manager->role == "DEVTOP") {
//                foreach ($revenue as $rev) {
//                    $manager->money += $rev->dev_money / 2;
//                }
//            }
//        }
//
//        Cache::forever("claim_rev_$period", $listUsers);
//        Log::info("cacheAddRevenueClaim finish cache claim_rev_$period " . (time() - $startTime) . "s");
//
//        $startTime2 = time();
//        $listUserDetail = [];
//        foreach ($listUsers as $user) {
//            //chi tiết theo user
//            $listCamaignObjectForUser = [];
////            $listCamaignObjectForUser = $listCampaignObject;
//            foreach ($listCampaignObject as $index => $cam) {
//                $obj = (object) [
//                            "username" => $user->user_name,
//                            "views_money_user" => 0,
//                            "views_money_user_per" => 0,
//                            "views_lyric_user" => 0,
//                            "views_lyric_user_per" => 0,
//                            "views_mix_user" => 0,
//                            "views_mix_user_per" => 0,
//                            "views_short_user" => 0,
//                            "views_short_user_per" => 0,
//                            "views_total_user" => 0,
//                            "views_total_user_per" => 0,
//                            "money" => 0,
//                            "total_money" => 0,
//                ];
//
////                $cam->views_money_user = 0;
////                $cam->views_money_user_per = 0;
////                $cam->views_lyric_user = 0;
////                $cam->views_lyric_user_per = 0;
////                $cam->views_mix_user = 0;
////                $cam->views_mix_user_per = 0;
////                $cam->views_short_user = 0;
////                $cam->views_short_user_per = 0;
////                $cam->views_total_user = 0;
////                $cam->views_total_user_per = 0;
////                $cam->money = 0;
////                $cam->total_money = 0;
//                //cộng tổng view_money,money thừ list video
//                foreach ($listVideos as $video) {
//                    if ($cam->campaign_id == $video->campaign_id && $video->username == $user->user_name) {
//                        //views theo user
//                        $obj->campaign_id = $cam->campaign_id;
//                        $obj->views_money_user += $video->distributor == "AdRev" ? $video->views_money25 : $video->views_money;
//                        $obj->views_lyric_user += $video->distributor == "AdRev" ? $video->views_lyric25 : $video->views_lyric;
//                        $obj->views_mix_user += $video->distributor == "AdRev" ? $video->views_mix25 : $video->views_mix;
//                        $obj->views_short_user += $video->distributor == "AdRev" ? $video->views_short25 : $video->views_short;
//                        $obj->views_total_user += $video->distributor == "AdRev" ? $video->views_total25 : $video->views_total;
//                        $obj->money += $video->views_per * $video->bass_money;
//                        $obj->total_money = $video->bass_money;
//                    }
//                }
//                //tính % các loại view
//                $obj->views_money_user_per = $cam->views_money > 0 ? round($obj->views_money_user / $cam->views_money * 100) : 0;
//                $obj->views_mix_user_per = $cam->views_mix > 0 ? round($obj->views_mix_user / $cam->views_mix * 100) : 0;
//                $obj->views_lyric_user_per = $cam->views_lyric > 0 ? round($obj->views_lyric_user / $cam->views_lyric * 100) : 0;
//                $obj->views_short_user_per = $cam->views_short > 0 ? round($obj->views_short_user / $cam->views_short * 100) : 0;
//                $obj->views_total_user_per = $cam->views_total > 0 ? round($obj->views_total_user / $cam->views_total * 100) : 0;
//
//                $obj->views_money = $cam->views_money;
//                $obj->views_mix = $cam->views_mix;
//                $obj->views_lyric = $cam->views_lyric;
//                $obj->views_short = $cam->views_short;
//                $obj->views_total = $cam->views_total;
////                if ($cam->views_total_user == 0) {
////                    unset($listCamaignObjectForUser[$index]);
////                }
//                if ($obj->views_total_user > 0) {
//                    $listCamaignObjectForUser[] = $obj;
//                }
//            }
//            usort($listCamaignObjectForUser, function($a, $b) {
//                return $a->money < $b->money;
//            });
//            $listUserDetail["$user->user_name"] = $listCamaignObjectForUser;
//            Cache::forever("claim_rev_detail_$period", $listUserDetail);
//        }
//
//        Log::info("cacheAddRevenueClaim finish cache claim_rev_$period all user " . (time() - $startTime2) . "s");
//    }
// // </editor-fold>
    //chức năng import báo cáo claim vào db
    public function checkImportReport(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ClaimController.checkImportReport|request=' . json_encode($request->all()));
        if ($request->distributor == -1) {
            return array("status" => "error", "message" => "You have to choose a distributor");
        }
        $results = [];
        $errors = [];
        $checkNumber = 0;
        $reportDatas = [];
        $arrayDistributorImport = ["Tunecore_Heddo", "Indiy", "Orchard_Indiy", "51st_State", "AdRev", "Orchard", "Cygnus"];
        //xử lý từng loại báo cáo
        if (in_array($request->distributor, $arrayDistributorImport)) {
            $validate = Validator::make($request->all(), ['report_file' => "required|file"], ["report_file.required" => "Choose a file"]);
            Log::info($validate->errors()->all());
            if ($validate->passes()) {
                if ($request->hasFile('report_file')) {
                    $uploadName = "$user->user_name-$request->distributor-$request->period-" . time() . ".csv";
                    $request->file('report_file')->move(public_path("claims/reports/"), "$uploadName");
                    $csvFile = public_path("claims/reports/$uploadName");
                    Log::info($csvFile);
                    DB::statement("delete from campaign_claim_rev_tmp");
                    $row = 1;
                    $succcess = 0;
                    if ($request->distributor == "Tunecore_Heddo") {
//                        $platformIndex = -1;
//                        $platformText = "";
//                        $revenueIndex = 6;
//                        $songIndex = 9;
//                        $artistIndex = 12;
//                        $rate = 1;
//                        $isrcIndex = -1;
//                        $periodIndex = -1;
                        $platformIndex = -1;
                        $platformText = "";
                        $revenueIndex = 13;
                        $songIndex = 5;
                        $artistIndex = 4;
                        $rate = 1;
                        $isrcIndex = 7;
                        $periodIndex = -1;
                    } else if ($request->distributor == "Orchard") {
                        $platformIndex = -1;
                        $platformText = "";
                        $revenueIndex = 12;
                        $songIndex = 0;
                        $artistIndex = 2;
                        $rate = 1;
                        $isrcIndex = -1;
                        $periodIndex = -1;
                    } else if (
                            $request->distributor == "Indiy" || $request->distributor == "Orchard_Indiy"
//                            || $request->distributor == "51st_State"
                    ) {
//                        $platformIndex = 8;
//                        $platformText = ["YouTube Content ID"];
//                        if ($request->owner == 'sang') {
//                            $revenueIndex = 15;
//                        } else {
//                            //lấy 70% của cột After Commission nếu là báo cáo của fat, báo cáo của sáng lấy 100%
//                            $revenueIndex = 14;
//                        }
//                        $songIndex = 3;
//                        $artistIndex = 2;
//                        $rates = Utils::textArea2Array($request->report_rate);
//
//                        $isrcIndex = 6;
//                        $periodIndex = 0;

                        $platformIndex = 7;
                        $platformText = ["YouTube Content ID"];
                        $revenueIndex = 9;
                        $songIndex = 4;
                        $artistIndex = 3;
                        $rates = Utils::textArea2Array($request->report_rate);
                        $isrcIndex = 2;
                        $periodIndex = 0;
                    } else if ($request->distributor == "51st_State") {
//                        $platformIndex = 1;
//                        $platformText = ["YouTube - Youtube Ads Revenue", "YouTube - Subscription"];
//                        $revenueIndex = 70;
//                        $songIndex = 6;
//                        $artistIndex = 9;
//                        $rates = Utils::textArea2Array($request->report_rate);
//                        $isrcIndex = 17;
//                        $periodIndex = 2;
                        $platformIndex = 15;
                        $platformText = ["YouTube Content ID"];
                        $revenueIndex = 17;
                        $songIndex = 11;
                        $artistIndex = 10;
                        $rates = Utils::textArea2Array($request->report_rate);
                        $isrcIndex = 9;
                        $periodIndex = 0;
                    } else if ($request->distributor == "AdRev") {
                        $platformIndex = 5;
                        $platformText = ["YouTube-CIDAR"];
                        $revenueIndex = 24;
                        $songIndex = 14;
                        $artistIndex = 12;
                        $rate = 1;
                        $isrcIndex = 11;
                        $periodIndex = 1;
                    } else if ($request->distributor == "Cygnus") {
//                        $platformIndex = 1;
//                        $platformText = ["YouTube CiD", "YouTube Music"];
//                        $revenueIndex = 12;
//                        $songIndex = 7;
//                        $artistIndex = 8;
//                        $rate = $request->report_rate;
//                        $isrcIndex = 6;
//                        $periodIndex = -1;
                        $platformIndex = 1;
                        $platformText = ["YouTube CiD", "YouTube Music"];
                        $revenueIndex = 9;
                        $songIndex = 5;
                        $artistIndex = 6;
                        $rate = $request->report_rate;
                        $isrcIndex = 4;
                        $periodIndex = -1;
                    }
                    if (($handle = fopen($csvFile, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle)) !== FALSE) {
                            $row++;
                            if ($row > 2) {
//                                if ($row > 10) {
//                                    break;
//                                }
                                //chỉ lấy  những dòng có platformText
                                if ($platformIndex != -1 && !in_array($data[$platformIndex], $platformText)) {
                                    continue;
                                }
                                if ($isrcIndex == -1) {
                                    $isrc = null;
                                } else {
                                    $isrc = $data[$isrcIndex];
                                }
                                $period = $request->period;
                                if ($periodIndex != -1) {
                                    $periodCsv = gmdate("Ym", strtotime($data[$periodIndex]));
                                    if ($periodCsv == null || $period == "") {
                                        continue;
                                    }
                                    //nếu ngày trong csv khác ngày cần lấy thì bỏ qua
                                    if ($period != $periodCsv) {
                                        continue;
                                    }
                                }
                                $succcess++;
//                                error_log("line $row $succcess $isrc $data[$artistIndex] $data[$songIndex] $data[$revenueIndex] $period $periodCsv ".strtotime($data[$periodIndex]));
                                $reve = str_replace(",", "", $data[$revenueIndex]);

                                if ($request->distributor == "Indiy" || $request->distributor == "Orchard_Indiy" || $request->distributor == "51st_State") {
//                                    if ($request->owner != 'sang') {
//                                        $reve = 70 * $reve / 100;
//                                    }
                                    //lấy theo list rate theo tháng: 202307=0.6666
                                    $rate = 0.68445;
                                    if (count($rates) > 0) {
                                        foreach ($rates as $r) {
                                            if (Utils::containString($r, $period)) {
                                                $rate = substr($r, strpos($r, '=') + 1, strlen($r));
                                            }
                                        }
                                    }
                                }
                                try {
                                    CampaignClaimRevTmp::create([
                                        'period' => $period,
                                        'artist' => $data[$artistIndex],
                                        'song_name' => $data[$songIndex],
                                        'revenue' => $reve * $rate,
                                        'isrc' => $isrc
                                    ]);
                                } catch (QueryException $ex) {
                                    error_log("Loi insertdb " . $row . '= ' . $data[$artistIndex]);
                                }
                            }
                        }
                        fclose($handle);
                    }
                    if ($request->distributor == "Indiy" || $request->distributor == "Orchard_Indiy" || $request->distributor == "51st_State" || $request->distributor == "AdRev") {
                        $reportDatas = DB::select("select period,isrc,sum(revenue) as revenue from campaign_claim_rev_tmp group by period,isrc order by revenue desc");
                    } else {
                        $reportDatas = DB::select("select period,artist,song_name,isrc,sum(revenue) as revenue from campaign_claim_rev_tmp group by period,artist,song_name,isrc order by revenue desc");
                    }
                    $total = count($reportDatas);
                }
            } else {
                return array("status" => "error", "message" => "Error upload file");
            }
        }
//        else if ($request->distributor == "Orchard") {
//
//            $reports = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->report)));
//            $total = count($reports);
//            if ($total < 1) {
//                return array("status" => "error", "message" => "Not found Data");
//            }
//
//            foreach ($reports as $report) {
//                $data = explode("@;@", str_replace(array("\t"), "@;@", trim($report)));
//                $artist = stripslashes($data[0]);
//                $songName = stripslashes($data[1]);
//                $revenue = str_replace(",", "", $data[2]);
//                $reportDatas[] = (object) [
//                            "period" => $request->period,
//                            "artist" => $artist,
//                            "song_name" => $songName,
//                            "revenue" => $revenue,
//                            "isrc" => null,
//                            "distributor_owner" => isset($request->owner) ? $request->owner : 'hoa'
//                ];
//            }
//        }

        foreach ($reportDatas as $report) {

            $result = [
                "period" => $report->period,
                "period_text" => gmdate("M-Y", strtotime($report->period . "01")),
                "campaign_id" => "",
                "artist" => !empty($report->artist) ? $report->artist : "",
                "song_name" => !empty($report->song_name) ? $report->song_name : "",
                "isrc" => $report->isrc,
                "revenue" => $report->revenue,
                "status" => "Fail",
                "color" => "color-red",
                "distributor_owner" => isset($request->owner) ? $request->owner : 'hoa',
            ];
            if ($report->isrc == null) {
                $check = CampaignStatistics::where("type", 2)->whereIn("status", [1, 4])->where("claim_artist", $report->artist)->where("claim_song_name", $report->song_name)->first(["id", "claim_artist as artist", "claim_song_name as song_name"]);
            } else {
                $check = CampaignStatistics::where("type", 2)->whereIn("status", [1, 4])->where("isrc", $report->isrc)->first(["id", "artist", "song_name"]);
            }
            if ($check) {
                if ($result["artist"] == "") {
                    $result["artist"] = $check->artist;
                }
                if ($result["song_name"] == "") {
                    $result["song_name"] = $check->song_name;
                }
                $result["campaign_id"] = $check->id;
                $result["status"] = "Success";
                $result["color"] = "color-green";
                $checkNumber++;
            } else {
                $errors[] = (object) $result;
            }
            $results[] = (object) $result;
        }
//        if ($checkNumber != $total) {
//            return array("status" => "error", "check" => "$checkNumber/$total", "results" => $results, "errors" => $errors);
//        }
        usort($results, function($a, $b) {
            return $a->status > $b->status;
        });
        foreach ($results as $data) {
            if ($data->campaign_id == "") {
                continue;
            }
            $check = CampaignClaimRev::where("period", $data->period)->where("campaign_id", $data->campaign_id)->first();
            if (!$check) {
                $check = new CampaignClaimRev();
            }
            $check->type = 1;
            $check->period = $data->period;
            $check->distributor = $request->distributor;
            $check->owner = $data->distributor_owner;
            $check->campaign_id = $data->campaign_id;
            $check->artist = $data->artist;
            $check->song_name = $data->song_name;
            $check->isrc = $data->isrc;
            //lấy bài có nhiều tiền nhất trong báo cáo nếu trùng bài hát
            if ($data->revenue >= $check->revenue) {
                $check->revenue = $data->revenue;
            }
            $check->created = Utils::timeToStringGmT7(time());
            $check->save();
        }
        return array("status" => "success", "check" => "$checkNumber/$total", "results" => $results, "errors" => $errors);
    }

    //2024/03/04 list những bài hát chưa đc mapping social
    public function listSongNotMapping(Request $request) {
        Log::info('ClaimController.listSongNotMapping|request=' . json_encode($request->all()));
        $key = "fb_music_asset";
        if ($request->platform == 'facebook') {
            $key = "fb_music_asset";
        } elseif ($request->platform == 'tiktok') {
            $key = "tt_music_asset";
        }
        $type = 2;
        if (isset($request->campaign_type)) {
            $type = $request->campaign_type;
        }
        $datas = CampaignStatistics::where("type", $type)->where("status", 1)->whereNull("$key")->get(["id", "artist", "song_name"]);
        return $datas;
    }

    public function saveSocialMappingSong(Request $request) {
        Log::info('ClaimController.saveSocialMappingSong|request=' . json_encode($request->all()));
        $data = CampaignStatistics::find($request->song);
        if (!$data) {
            return array("status" => "error", "message" => "Not found song $request->song");
        }
        $key = "fb_music_asset";
        if ($request->platform == 'facebook') {
            $key = "fb_music_asset";
        }
        if ($request->platform == 'tiktok') {
            $key = "tt_music_asset";
        }
        $data->$key = trim($request->social_asset);
        $data->save();
        return array("status" => "success", "message" => "Success", "data" => $data);
    }

    //2024/03/14 tính toán claim
    public function caculateClaims(Request $request) {
        $pid = getmypid();
        $tele = "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=";
        ini_set('max_execution_time', 0);
        $message = "caculateClaims|pid=$pid|request=" . json_encode($request->all());
        Log::info($message);
        RequestHelper::callAPI("GET", $tele . urlencode($message), []);
        $startTime = time();
        $periodTime = time();
        $period = gmdate("Ym", time());
        if (isset($request->period)) {
            $periodTime = strtotime($request->period . "01");
            $period = $request->period;
        }
        $periodMonth = gmdate("m", $periodTime);
        $periodYear = gmdate("Y", $periodTime);
        //tính thời gian để lấy views ,vd: period= 202305 => 20230501-20230601 và 20230425-20230525
        $periodMonthNext = $periodMonth + 1;
        $periodYearNext = $periodYear;
        if ($periodMonth == 12) {
            $periodMonthNext = 1;
            $periodYearNext = $periodYearNext + 1;
        }
        if ($periodMonthNext < 10) {
            $periodMonthNext = "0$periodMonthNext";
        }
        $dateFrom = "$periodYear$periodMonth" . "01";
        $dateTo = "$periodYearNext$periodMonthNext" . "01";

        $periodMonth25 = $periodMonth - 1;
        $periodYear25 = $periodYear;
        $periodMonthNext25 = $periodMonth;
        $periodYearNext25 = $periodYear;

        if ($periodMonthNext25 == 1) {
            $periodMonth25 = 12;
            $periodYear25 = $periodYear25 - 1;
        }
        if ($periodMonth25 < 10) {
            $periodMonth25 = "0$periodMonth25";
        }
        $dateFrom25 = "$periodYear25$periodMonth25" . "25";
        $dateTo25 = "$periodYearNext25$periodMonthNext25" . "25";

        //2024/11/01 thêm param distributor, để chạy riêng cho từng distributor, mỗi distributor có thời gian nhận tiền khác nhau
        $distributor = "";
        if (isset($request->distributor)) {
            if ($request->distributor == 'Indiy') {
                $distributor = " and distributor in ('51st_State','Indiy','Orchard_Indiy','AdRev_Indiy')";
            } else {
                $distributor = " and distributor = '$request->distributor'";
            }
        }
        $countVideos = DB::select("select d.username,SUBSTRING(d.insert_date, 1, 6) as insert_month,d.campaign_id,
                                            count(*) as videos from 
                                            (select distinct a.video_id, a.username ,a.insert_date,a.campaign_id
                                            from campaign a 
                                            where a.video_type <> 1 
                                            and a.status_confirm =3 
                                            and SUBSTRING(insert_date, 1, 6) = $period                                            
                                            and a.campaign_id in 
                                            (SELECT id FROM campaign_statistics WHERE type = 2 and status in (1,4)  $distributor)) d
                                            group by d.username,insert_month,campaign_id");

//        $claims = CampaignStatistics::where("type", 2)->whereIn("status", [1, 4])->orderBy("id", "desc")->get();
        $claims = DB::select("select * from campaign_statistics where type = 2 and status in (1,4) $distributor");
        $users = User::where("status", 1)->whereRaw(DB::raw("(role like '%16%' or role like '%24%')"))->get(["user_name", DB::raw("CASE WHEN role like '%16%' THEN 'BASS' WHEN role like '%24%' THEN 'DEVTOP' END as role")]);
        $videos = DB::select("select username,campaign_id,video_type,video_id,number_claim,is_match_claim  from campaign where video_type <> 1 and status_confirm =3 
                                    and campaign_id in (SELECT id FROM `campaign_statistics` WHERE `type` = '2' and status in (1,4) $distributor) 
                                    group by username,campaign_id,video_type,video_id,number_claim,is_match_claim ");
        Log::info("$dateFrom $dateTo");
        $viewMonth_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date >= $dateFrom and date < $dateTo group by video_id");
        $viewMonth25_0 = DB::select("SELECT video_id,sum(views_real_daily) as views FROM `athena_promo_sync` WHERE date > $dateFrom25 and date <= $dateTo25 group by video_id");

        $distributors = [
            'Orchard' => [
                'tax_percent' => 30,
                'artist_percent' => 20,
                'cost_percent' => 30,
                'bass_percent' => 30
            ],
            'AdRev' => [
                'tax_percent' => 30,
                'artist_percent' => 0,
                'cost_percent' => 20,
                'bass_percent' => 30
            ],
            'Cygnus' => [
                'tax_percent' => 0,
                'artist_percent' => 35,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'Tunecore_Heddo' => [
                'tax_percent' => 0,
                'artist_percent' => 30,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'Indiy' => [
                'tax_percent' => 0,
                'artist_percent' => 50,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'SME' => [
                'tax_percent' => 0,
                'artist_percent' => 0,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'Orchard_UrbanoVibe' => [
                'tax_percent' => 30,
                'artist_percent' => 50,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            '51st_State' => [
                'tax_percent' => 15,
                'artist_percent' => 50,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'WMG/Sparta' => [
                'tax_percent' => 20,
                'artist_percent' => 40,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'AdRev_Indiy' => [
                'tax_percent' => 0,
                'artist_percent' => 50,
                'cost_percent' => 10,
                'bass_percent' => 30
            ],
            'Orchard_Indiy' => [
                'tax_percent' => 0,
                'artist_percent' => 50,
                'cost_percent' => 10,
                'bass_percent' => 30
            ]
        ];

        $revenue = CampaignClaimRev::where("period", $period)->where("type", 1)->get();

        foreach ($revenue as $rev) {
            foreach ($claims as $claim) {
                if ($rev->campaign_id == $claim->id) {

                    $distributorName = $claim->distributor;
                    if (isset($distributors[$distributorName])) {
                        $distributorConfig = $distributors[$distributorName];
                        $rev->tax_percent = $distributorConfig['tax_percent'];
                        $rev->artist_percent = $distributorConfig['artist_percent'];
                        $rev->cost_percent = $distributorConfig['cost_percent'];
                        $rev->bass_percent = $distributorConfig['bass_percent'];
                    } else {
                        // Nếu không tìm thấy, sử dụng giá trị từ claim
                        $rev->tax_percent = $claim->tax_percent;
                        $rev->bass_percent = $claim->bass_percent;
                        $rev->artist_percent = $claim->artist_percent;
                        $rev->cost_percent = $claim->cost_percent;
                    }
                    //từ tháng 3/2024 tăng cost lên 30%
                    if ($claim->distributor == "Orchard" && $request->period >= '202403') {
                        $rev->cost_percent = 30;
                    }

                    $tax = $rev->tax_percent * $rev->revenue / 100;
                    $afterTax = $rev->revenue - $tax;
                    $artist = $rev->artist_percent * $afterTax / 100;
                    $cost = $rev->cost_percent * $afterTax / 100;
                    //số tiền thực nhận
                    $rev->received = $rev->revenue - ($tax + $artist);
                    //lợi nhuận để tính % chi trả cho bassteam
                    $rev->profit = $rev->revenue - ($tax + $artist + $cost);
                    //số tiền phải trả thuế
                    $rev->tax = $tax;
                    //số tiền phải trả nghệ sỹ
                    $rev->artist = $artist;
                    //chi phí khác
                    $rev->cost = $cost;
                    //bassteam sẽ nhận dc 95% của 30% profit
                    $rev->bass_money = 0.95 * $rev->profit * $rev->bass_percent / 100;
                    //devteam sẽ nhận dc 5% của 30% của profit
                    $rev->dev_money = 0.05 * $rev->profit * $rev->bass_percent / 100;
                    //lợi nhuận của admin, dc tính cả cost vào
                    $rev->admin_profit = $rev->revenue - ($tax + $artist + $rev->bass_money + $rev->dev_money);
                }
            }
        }
        Log::info(json_encode($revenue));
        $message = "caculateClaims|pid=$pid|period=$period|finish prepare data " . (time() - $startTime);
        Log::info($message);
        RequestHelper::callAPI("GET", $tele . urlencode($message), []);
        $startTime2 = time();

        DB::statement("delete from claims_views_tmp where 1=1 $distributor");
        foreach ($videos as $video) {
//            //tìm distributor cho video
            $video->distributor = "";
            $video->artist = "";
            $video->song_name = "";
            foreach ($claims as $claim) {
                if ($claim->id == $video->campaign_id) {
                    $video->distributor = $claim->distributor;
                    $video->artist = $claim->artist;
                    $video->song_name = $claim->song_name;
                    break;
                }
            }
            //tim role
            $video->role = "";
            foreach ($users as $user) {
                if ($user->user_name == $video->username) {
                    $video->role = $user->role;
                    break;
                }
            }

            $key_views_money = "views_money";
            $key_views_total = "views_total";
            $key_views_mix = "views_mix";
            $key_views_lyric = "views_lyric";
            $key_views_short = "views_short";

            $video->$key_views_money = 0;
            $video->$key_views_total = 0;
            $video->$key_views_mix = 0;
            $video->$key_views_lyric = 0;
            $video->$key_views_short = 0;

            if ($video->distributor == "AdRev") {
                //view từ ngày 25 - 25  dành cho distributor AdRev(labelgrid)
                $viewMonthReal = $viewMonth25_0;
            } else {
                $viewMonthReal = $viewMonth_0;
            }

            foreach ($viewMonthReal as $view) {
                if ($video->video_id == $view->video_id) {
                    $video->$key_views_total = $view->views;
                    //ko nhận claim thì ko dc tính view money
                    if ($video->number_claim != 0 && $video->number_claim != null && $video->is_match_claim) {
                        $video->$key_views_money = round(1 / $video->number_claim * $view->views);
                    }
                    if ($video->video_type == 2) {
                        $video->$key_views_lyric = $view->views;
                    } else if ($video->video_type == 5) {
                        $video->$key_views_mix = $view->views;
                    } else if ($video->video_type == 6) {
                        $video->$key_views_short = $view->views;
                    }
                    break;
                }
            }

            $insert = new ClaimsViewsTmp();
            $insert->username = $video->username;
            $insert->campaign_id = $video->campaign_id;
            $insert->video_type = $video->video_type;
            $insert->video_id = $video->video_id;
            $insert->is_match_claim = $video->is_match_claim;
            $insert->number_claim = $video->number_claim;
            $insert->views_money = $video->views_money;
            $insert->views_total = $video->views_total;
            $insert->views_mix = $video->views_mix;
            $insert->views_lyric = $video->views_lyric;
            $insert->views_short = $video->views_short;
            $insert->distributor = $video->distributor;
            $insert->artist = $video->artist;
            $insert->song_name = $video->song_name;
            $insert->role = $video->role;
            $insert->created = Utils::timeToStringGmT7(time());
            $insert->save();
////            DB::insert("insert into claims_views_tmp (username,campaign_id,video_type,"
////                    . "video_id,is_match_claim,number_claim,"
////                    . "views_money,views_total,views_mix,views_lyric,views_short) "
////                    . "values('$video->username',$video->campaign_id,$video->video_type,"
////                    . "'$video->video_id',$video->is_match_claim," . ($video->number_claim == null ? "NULL" : $video->number_claim) . ","
////                    . "$video->views_money,$video->views_total,$video->views_mix,$video->views_lyric,$video->views_short)");
        }
        $message = "caculateClaims|pid=$pid|period=$period,videos=" . count($videos) . ',viewmonth=' . count($viewMonth_0) . " time=" . (time() - $startTime2);
        Log::info($message);
        RequestHelper::callAPI("GET", $tele . urlencode($message), []);

        //tổng kết dữ liệu vào bảng claims_views. tính toán view khi tổng kết theo bài claim (ko distinct video_id)
        DB::statement("delete from claims_views where period = $period $distributor");
        DB::statement("insert ignore into claims_views 
                        (period,username,role,campaign_id,distributor,artist,song_name,
                        claim_view_money,claim_view_mix,claim_view_lyric,claim_view_short) 
                        select $period,username,role,campaign_id,distributor,artist,song_name,
                        SUM(views_money) as views_money,
                        SUM(views_mix) as views_mix,
                        SUM(views_lyric) as views_lyric,
                        SUM(views_short) as views_short
                        from claims_views_tmp where 1=1 $distributor group by 
                        username,role,campaign_id,distributor,artist,song_name");

        //tính số views tổng mỗi claim để tính % views, dùng để tính số tiền nhận đc mỗi của mỗi claim cho mỗi user
        $totalClaimViews = DB::select("select campaign_id,sum(claim_view_money) as claim_view_money_total,
                                        sum(claim_view_mix) as claim_view_mix_total,
                                        sum(claim_view_lyric) as claim_view_lyric_total,
                                        sum(claim_view_short) as claim_view_short_total
                                        from claims_views where period=$period $distributor group by campaign_id
                                        having claim_view_money_total > 0");
        //tính số views group by username
        $userViews = DB::select("select a.users as username, 
                                sum(a.views_money) as views_money,
                                sum(a.views_total) as views_total,
                                sum(a.views_mix) as views_mix,
                                sum(a.views_lyric) as views_lyric
                                from (SELECT  username as users,video_id, username,
                                MAX(views_money) AS views_money,
                                MAX(views_total) as views_total,
                                MAX(views_mix) as views_mix,
                                MAX(views_lyric) as views_lyric
                                FROM claims_views_tmp where views_total > 0 $distributor
                                GROUP BY username,video_id) a
                                group by a.users");
        //tính tiền mỗi claim
        if (isset($request->distributor)) {
            if ($request->distributor == 'Indiy') {
                $distributors = ['51st_State', 'Indiy', 'Orchard_Indiy', 'AdRev_Indiy'];
            } else {
                $distributors = [];
                $distributors[] = $request->distributor;
            }
            $claimsViews = ClaimsViews::where("period", $period)->whereIn("distributor", $distributors)->get();
        } else {
            $claimsViews = ClaimsViews::where("period", $period)->get();
        }
        foreach ($claimsViews as $claimView) {
            $claimView->created = Utils::timeToStringGmT7(time());
            $claimView->save();
            $claimView->revenue = 0;
            $claimView->bass_money = 0;
            $claimView->dev_money = 0;
            foreach ($revenue as $rev) {
                if ($rev->period == $period && $rev->campaign_id == $claimView->campaign_id) {
                    $claimView->revenue = $rev->revenue;
                    $claimView->bass_money = $rev->bass_money;
                    $claimView->dev_money = $rev->dev_money;
                    $claimView->profit = $rev->admin_profit;
                    $claimView->received = $rev->received;
                    $claimView->save();
//                    Log::info(json_encode($claimView));
                    break;
                }
            }

            //tính số view total của mỗi campaign
            foreach ($totalClaimViews as $totalClaimView) {
                if ($totalClaimView->campaign_id == $claimView->campaign_id) {
                    $claimView->claim_view_money_total = $totalClaimView->claim_view_money_total;
                    $claimView->claim_view_mix_total = $totalClaimView->claim_view_mix_total;
                    $claimView->claim_view_lyric_total = $totalClaimView->claim_view_lyric_total;
                    $claimView->claim_view_short_total = $totalClaimView->claim_view_short_total;
                    $claimView->save();
                    break;
                }
            }

            //đếm số video
            foreach ($countVideos as $count) {
                if ($claimView->username == $count->username && $count->campaign_id == $claimView->campaign_id) {
                    $claimView->videos = $count->videos;
                    $claimView->save();
                    break;
                }
            }

            //tính views distinct, dùng cho thống kê theo user
            foreach ($userViews as $userView) {
                if ($claimView->username == $userView->username) {
                    $claimView->user_view_money = $userView->views_money;
                    $claimView->user_view_mix = $userView->views_mix;
                    $claimView->user_view_lyric = $userView->views_lyric;
                    $claimView->user_view_total = $userView->views_total;
                    $claimView->save();
                    break;
                }
            }
        }

        $message = "caculateClaims|pid=$pid|period=$period,timeAll=" . (time() - $startTime);
        Log::info($message);
        RequestHelper::callAPI("GET", $tele . urlencode($message), []);
    }

    //2024/03/14 hàm lấy danh sách tiền claim hàng tháng
    public function listClaimMontly(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|ClaimController.listClaimMontly|request=" . json_encode($request->all()));
        $condition = " and distributor = 'Orchard' ";
        $revType = 2;
        if (isset($request->type)) {
            if ($request->type == 5) {
                $revType = 5;
                $condition = " and distributor = 'AdRev' ";
            } elseif ($request->type == 6) {
                $revType = 6;
                $condition = " and distributor in ('Orchard_Indiy','51st_State','Indiy','AdRev_Indiy')";
            }
        }
        if ($request->is_supper_admin) {
            $datas = DB::select("SELECT a.period,SUM(if(a.revenue is not null, 1, 0)) AS claim ,sum(a.revenue) as revenue,
                            sum(a.received) as received,sum(a.profit) as profit, SUM(a.bass_money) as bass_money, SUM(a.dev_money) as dev_money
                            FROM (select distinct campaign_id,period,revenue,received,profit,bass_money,dev_money from claims_views where 1=1 $condition) a
                            group by a.period");
        } else {
            $datas = DB::select("SELECT a.period,SUM(if(a.revenue is not null, 1, 0)) AS claim ,0 as revenue,
                            0 as received,0 as profit,SUM(a.bass_money) as bass_money, SUM(a.dev_money) as dev_money
                            FROM (select distinct campaign_id,period,revenue,received,profit,bass_money,dev_money from claims_views where 1=1 $condition) a
                            group by a.period");
        }
        $listPays = CampaignClaimRevStatus::where("rev_type", $revType)->get();
        $periods = [];
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
            if ($request->is_supper_admin || $show == 1) {
                $periods[] = (object) [
                            "period" => $data->period,
                            "period_text" => date("M-Y", strtotime($data->period . "01")),
                            "claim" => $data->claim,
                            "revenue" => $data->revenue,
                            "profit" => $data->profit,
                            "bass_revenue" => $data->bass_money,
                            "dev_revenue" => $data->dev_money,
                            "paid" => $paid,
                            "show" => $show
                ];
            }
        }

        usort($periods, function($a, $b) {
            return $a->period < $b->period;
        });

        return array("status" => "success", "report" => $periods);
    }

    //2024/03/14 hàm trả về số tiền mỗi user được nhận
    public function listClaimMontlyDetail(Request $request) {

        $user = Auth::user();
        Log::info("$user->user_name|ClaimController.listClaimMontlyDetail|request=" . json_encode($request->all()));
        $condition = " and distributor = 'Orchard' ";
        if (isset($request->type)) {
            if ($request->type == 5) {
                $condition = " and distributor = 'AdRev' ";
            } elseif ($request->type == 6) {
                $condition = " and distributor in ('Orchard_Indiy','51st_State','Indiy','AdRev_Indiy')";
            }
        }

        //kiểm tra nếu mà tháng đó chưa show thì ko trả về dữ liệu
        if (!$request->is_supper_admin) {
            $listPays = CampaignClaimRevStatus::where("rev_type", $request->type)->get();
            $show = 0;
            foreach ($listPays as $pay) {
                if ($pay->period == $request->period) {
                    $show = $pay->status_show;
                    break;
                }
            }
            if ($show == 0) {
                return array("status" => "error", "users" => [], "campaigns" => [], "message" => "Not found");
            }
        }

        $devUser = DB::select("SELECT a.period,SUM(a.dev_money) as dev_money
                            FROM (select distinct campaign_id,period,revenue,received,profit,bass_money,dev_money from claims_views where period= ? $condition) a
                            group by a.period", [$request->period]);

        $dataUser = DB::select("select username as user_name,role,
                                sum(claim_view_money) as views_money,
                                sum((claim_view_money / claim_view_money_total * bass_money)) as money  
                                from claims_views where period = ? and claim_view_money_total > 0 $condition
                                group by username,role order by money desc", [$request->period]);

        $dataUser[] = (object) ["user_name" => "tungtt",
                    "role" => "DEV",
                    "views_money" => 0,
                    "money" => !empty($devUser[0]->dev_money) ? $devUser[0]->dev_money / 2 : 0];
        $dataUserDetail = DB::select("select username,campaign_id,
                                        claim_view_money as views_money_user ,
                                        claim_view_money_total as views_money ,
                                        round(claim_view_money / claim_view_money_total *100) as views_money_user_per,
                                        claim_view_mix as views_mix_user,
                                        claim_view_mix_total as views_mix,
                                        round(claim_view_mix / claim_view_mix_total *100) as views_mix_user_per,
                                        claim_view_lyric as views_lyric_user,
                                        claim_view_lyric_total as views_lyric,
                                        round(claim_view_lyric / claim_view_lyric_total *100) as views_lyric_user_per,
                                        (claim_view_mix+claim_view_lyric) as  views_total_user,
                                        (claim_view_mix_total + claim_view_lyric_total ) as views_total,
                                        round((claim_view_mix+claim_view_lyric) / (claim_view_mix_total + claim_view_lyric_total) *100) as  views_total_user_per, 
                                        (claim_view_money / claim_view_money_total * bass_money ) as money,
                                        bass_money as total_money
                                        from claims_views where period = ?
                                        and username= ? and claim_view_money_total > 0 $condition order by money desc", [$request->period, $request->username]);
        $dataUserDetailOther = DB::select("select username,campaign_id,
                                        claim_view_money as views_money_user ,
                                        claim_view_money_total as views_money ,
                                        round(claim_view_money / claim_view_money_total *100) as views_money_user_per,
                                        claim_view_mix as views_mix_user,
                                        claim_view_mix_total as views_mix,
                                        round(claim_view_mix / claim_view_mix_total *100) as views_mix_user_per,
                                        claim_view_lyric as views_lyric_user,
                                        claim_view_lyric_total as views_lyric,
                                        round(claim_view_lyric / claim_view_lyric_total *100) as views_lyric_user_per,
                                        (claim_view_mix+claim_view_lyric) as  views_total_user,
                                        (claim_view_mix_total + claim_view_lyric_total ) as views_total,
                                        round((claim_view_mix+claim_view_lyric) / (claim_view_mix_total + claim_view_lyric_total) *100) as  views_total_user_per, 
                                        (claim_view_money / claim_view_money_total * bass_money ) as money,
                                        bass_money as total_money
                                        from claims_views where period = ?
                                        and username <> ? and claim_view_money_total > 0 $condition order by money desc", [$request->period, $request->username]);

        foreach ($dataUserDetail as $detail) {

            $tooltip = [];
            foreach ($dataUserDetailOther as $detailOther) {
                if ($detail->campaign_id == $detailOther->campaign_id) {
                    $tooltip[] = (object) [
                                "username" => $detailOther->username,
                                "views_money" => $detailOther->views_money_user,
                                "views_money_per" => $detailOther->views_money_user_per,
                                "money" => $detailOther->money,
                    ];
                }
                $detail->tooltip = $tooltip;
            }
        }
        return array("status" => "success", "users" => $dataUser, "campaigns" => $dataUserDetail);
    }

    //2024/03/14 hàm trả về thống kê view theo user và distributor trong 3 tháng tính từ tháng truyền vào
    public function claimMontlyDistributor(Request $request) {
        $currentPeriod = time();

        if (isset($request->period)) {
            $currentPeriod = strtotime($request->period . "01");
        }

        //tìm danh sách các ngày đàu tháng tính từ period truyền vào
        $months = [];
        $curMonth = gmdate("m", $currentPeriod);
        $curYear = gmdate("Y", $currentPeriod);
        $curNextMonth = $curMonth + 1;
        $lastRage = $curNextMonth - 3;

        for ($i = $curMonth; $i >= $lastRage; $i--) {
            $m = (int) $i;
            $y = $curYear;
            if ($m <= 0) {
                $m = 12 + $m;
                $y = $curYear - 1;
            } else if ($m > 12) {
                $m = $m - 12;
                $y = $curYear + 1;
            }

            if ($m < 10) {
                $m = "0$m";
            }


            $months[] = "$y$m";
        }
        $periods = implode(",", $months);
        $dataUsers = DB::select("SELECT period, username, 
                            max(user_view_money) as views_money,
                            max(user_view_total) as views_total,
                            sum(videos) as videos
                            FROM `claims_views`
                            WHERE `period`  in ($periods) 
                            group by period, username");
        $dataDistributor = DB::select("SELECT a.period,
                                        a.distributor,
                                        sum(a.views_money) AS views_money,
                                        sum(a.views_total) AS views_total,
                                        sum(a.videos) AS videos,
                                        sum(a.revenue) AS revenue,
                                        sum(a.received) AS received,
                                        sum(a.profit) AS profit,
                                        sum(a.bass) AS bass
                                 FROM
                                   (SELECT period,
                                           distributor,
                                           campaign_id,
                                           (claim_view_money_total) AS views_money,
                                           ((claim_view_mix_total) + (claim_view_lyric_total)) AS views_total,
                                           sum(videos) AS videos,
                                           MAX(revenue) AS revenue,
                                           MAX(received) AS received,
                                           MAX(profit) AS profit,
                                           (MAX(bass_money) + MAX(dev_money)) AS bass
                                    FROM claims_views
                                    WHERE period in ($periods)
                                      AND claim_view_money_total > 0
                                    GROUP BY period,
                                             distributor,
                                             campaign_id,
                                             claim_view_money_total, (claim_view_mix_total + claim_view_lyric_total)) a
                                 GROUP BY period,
                                          distributor");

        $listMonth = [];
        foreach ($months as $month) {
            $user = [];
            $distributor = [];
            $viewTotalDisSum = 0;
            $viewTotalUserSum = 0;
            $videoTotalUserSum = 0;
            $revenueDisSum = 0;
            $receivedDisSum = 0;
            $profitDisSum = 0;
            $bassDisSum = 0;
            foreach ($dataUsers as $data) {
                if ($month == $data->period) {
                    $viewTotalUserSum += $data->views_total;
                    $videoTotalUserSum += $data->videos;
                    $user[] = (object) [
                                "object" => $data->username,
                                "views_money" => $data->views_money,
                                "views_total" => $data->views_total,
                                "videos" => $data->videos];
                }
            }
            usort($user, function($a, $b) {
                return $a->views_total < $b->views_total;
            });
            foreach ($dataDistributor as $data) {
                if ($month == $data->period) {
                    $viewTotalDisSum += $data->views_total;
                    $revenueDisSum += $data->revenue;
                    $receivedDisSum += $data->received;
                    $profitDisSum += $data->profit;
                    $bassDisSum += $data->bass;
                    $distributor[] = (object) [
                                "object" => $data->distributor,
                                "views_money" => $data->views_money,
                                "views_total" => $data->views_total,
                                "videos" => $data->videos,
                                "revenue" => $data->revenue,
                                "received" => $data->received,
                                "profit" => $data->profit,
                                "bass" => $data->bass
                    ];
                }
            }
            usort($distributor, function($a, $b) {
                return $a->revenue < $b->revenue;
            });
            $listMonth[] = (object) [
                        "period" => $month,
                        "month" => gmdate("M-Y", strtotime($month . "01")),
                        "user" => $user,
                        "distributor" => $distributor,
                        "distributor_views_sum" => $viewTotalDisSum,
                        "user_views_sum" => $viewTotalUserSum,
                        "user_video_sum" => $videoTotalUserSum,
                        "revenue_sum" => $revenueDisSum,
                        "received_sum" => $receivedDisSum,
                        "profit_sum" => $profitDisSum,
                        "bass_sum" => $bassDisSum
            ];


//            $month->cache = Utils::timeToStringGmT7(time());
//            $listMonth[] = (object) [
//                        "period" => gmdate("Ym", strtotime("$month" . "01")),
//                        "month" => gmdate("M-Y", strtotime("$month" . "01")),
//                        "user" => $manage,
//                        "distributor" => $distributorViews,
//                        "distributor_views_sum" => $viewTotalDisSum,
//                        "user_views_sum" => $viewTotalUserSum,
//                        "user_video_sum" => $videoTotalUserSum,
//                        "revenue_sum" => $revenueDisSum,
//                        "profit_sum" => $profitDisSum,
//                        "bass_sum" => $bassDisSum,
//            ];
        }

        return response()->json($listMonth);
    }

    //2024/04/09 hàm tạo file mp4 để check claim
    public function makeMp4(Request $request) {
        $ids = explode(",", $request->ids);
        $claims = CampaignStatistics::whereIn("id", $ids)->get();
        $rp = [];
        foreach ($claims as $claim) {
            Log::info($claim->audio_url);
            Log::info("wget $claim->audio_url --no-check-certificate --output-document /home/automusic.win/public_html/public/claims/music/dash/mp3/$claim->id.mp3");
            shell_exec("wget $claim->audio_url --no-check-certificate --output-document /home/automusic.win/public_html/public/claims/music/dash/mp3/$claim->id.mp3");
            $rp[] = (object) [
                        "link" => "https://automusic.win/claims/music/dash/mp3/$claim->id.mp3",
                        "patch" => "/home/automusic.win/public_html/public/claims/music/dash/mp3/$claim->id.mp3"
            ];
        }
        return response()->json($rp);
    }

    //2025/02/13 chức năng kiểm tra claim của distributor nào và lấy asset_id
    public function checkClaimDistributor(Request $request) {
        Log::info('ClaimController.checkClaimDistributor|request=' . json_encode($request->all()));
        $claim = CampaignStatistics::where("id", $request->id)->first();
        //first=0: kiểm tra lại xem có dữ liệu cũ ko thì check lại video, first=1: làm lại từ đầu,bao gồm make lại video
        if ($request->remake == 0) {
            //kiểm tra xem đã có thông tin claim trong short_text chưa
            if ($claim->short_text != null) {
                $shortText = json_decode($claim->short_text);
                if (isset($shortText->video_id) && $shortText->video_id != null && $shortText->video_id != "" && isset($shortText->gmail) && $shortText->gmail != null && $shortText->gmail != "") {
                    $cmd = "sudo /home/tools/env/bin/python /home/tools/CopyRight.py check_copyright2 $shortText->gmail $shortText->video_id";
                    $rs = shell_exec($cmd);
                    if ($rs != null && $rs != "") {
                        $info = json_decode($rs);
                        $title = null;
                        $artists = [];
                        $assetId = null;
                        $distributor = null;
                        if (isset($info->receivedClaims[0]->asset->metadata->soundRecording->title)) {
                            $title = $info->receivedClaims[0]->asset->metadata->soundRecording->title;
                        }
                        if (isset($info->receivedClaims[0]->asset->metadata->soundRecording->title)) {
                            $artists = $info->receivedClaims[0]->asset->metadata->soundRecording->artists;
                        }
                        if (isset($info->receivedClaims[0]->assetId)) {
                            $assetId = $info->receivedClaims[0]->assetId;
                        }
                        if (isset($info->contentOwners[0]->displayName)) {
                            $distributor = $info->contentOwners[0]->displayName;
                        }
                    }

                    $shortText->title = $title;
                    $shortText->artists = $artists;
                    $shortText->assetId = $assetId;
                    $shortText->distributor = $distributor;
                    $claim->short_text = json_encode($shortText);
                    if ($assetId != null) {
                        $claim->asset_id = $assetId;
                    }
                    $claim->save();
                    $status = "success";
                    if ($artists == []) {
                        $status = "error";
                    }
                    return response()->json(["status" => $status, "reload" => 0, "message" => "finished", "rs" => $rs, "cmd" => $cmd]);
                }
            }
        }
        $root = "/home/automusic.win/public_html/public/check_claim";

        $audio = "$root/$claim->id.mp3";
        $video = "$root/$claim->id.mp4";
        $thumb = "$root/$claim->id.png";

        //nếu không tồn tại file mp4 hoặc là yêu cầu làm mới lại từ đầu
        if (count(glob($video)) <= 0 || $request->remake == 1) {
            if ($claim->short_text != null) {
                $shortText = json_decode($claim->short_text);
                $shortText->video_id = null;
                $shortText->gmail = null;
                $shortText->title = null;
                $shortText->artists = null;
                $shortText->assetId = null;
                $shortText->distributor = null;
                $claim->short_text = json_encode($shortText);
                $claim->save();
            }
            shell_exec("wget $claim->audio_url --no-check-certificate --output-document $audio");
            if (count(glob($audio)) <= 0) {
                return response()->json(["status" => "error", "message" => "No music found"]);
            }
            //tạo file ảnh
            $img = new ImageUploadController();
            $imgInfo = $img->createRandomImage($claim->campaign_name);
            rename($imgInfo->file_path, $thumb);
            $command = "sudo ffmpeg -y -loop 1 -i \"$thumb\" -i \"$audio\" -c:v libx264 -tune stillimage -c:a aac -b:a 192k -pix_fmt yuv420p -shortest \"$video\"";
            Log::info($command);
            shell_exec($command);
        }


        $uploadChannel = $this->getUploadChannel();
        if ($uploadChannel == null) {
            return response()->json(["status" => "error", "message" => "No channel for upload"]);
        }
        //upload

        $language = "en-US";
        $category = "10";
        $link_video = asset("check_claim/$claim->id.mp4");
        $link_thumbnail = asset("check_claim/$claim->id.png");

        $title = $request->id;
        $description = "";
        $tag = "";
        $location = "";
        $compiler = '[]';
        $video_source = "";
        $video_ext = "mp4";
        $schedule = "";

        $login = (object) [
                    "script_name" => "profile",
                    "func_name" => "login",
                    "params" => []
        ];
        $listLink = ['link_video', 'link_thumbnail'];
        $listJson = ['schedule'];
        $listParams = ["language" => $language, "category" => $category, "link_video" => $link_video,
            "link_thumbnail" => $link_thumbnail, "title" => $title, "description" => $description,
            "tag" => $tag, "location" => $location, "compiler" => $compiler,
            "video_source" => $video_source, "video_ext" => $video_ext, "schedule" => $schedule];
        $params = [];
        foreach ($listParams as $key => $value) {
            if (in_array($key, $listLink)) {
                $param = (object) [
                            "name" => $key,
                            "type" => "file",
                            "value" => $value
                ];
            } else if (in_array($key, $listJson)) {
                $t = "json";
                if ($value == "") {
                    $t = "string";
                }
                $param = (object) [
                            "name" => $key,
                            "type" => $t,
                            "value" => $value
                ];
            } else {
                $param = (object) [
                            "name" => $key,
                            "type" => "string",
                            "value" => $value
                ];
            }

            $params[] = $param;
        }
        $callBack = "http://automusic.win/callback/upload/claim?us=$request->us";
        $param3 = (object) [
                    "name" => "visibility",
                    "type" => "string",
                    "value" => "unlisted"];
        $param4 = (object) [
                    "name" => "handle",
                    "type" => "string",
                    "value" => $uploadChannel->handle];
        $params[] = $param3;
        $params[] = $param4;

        $reupload = (object) [
                    "script_name" => "upload",
                    "func_name" => "upload",
                    "params" => $params
        ];
        $taskLists = [];
        $taskLists[] = $login;
        $taskLists[] = $reupload;
        $req = (object) [
                    "gmail" => $uploadChannel->note,
                    "task_list" => json_encode($taskLists),
                    "run_time" => 0,
                    "type" => 68,
                    "studio_id" => $request->id,
                    "piority" => 30,
                    "call_back" => $callBack
        ];
        Logger::logUpload("checkClaimDistributor:$request->id " . json_encode($req));
        $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
        Logger::logUpload("checkClaimDistributor:$request->id " . json_encode($bas));
        return response()->json(["status" => "success", "reload" => 0, "message" => "uploading", "job_id" => $bas->job_id]);
    }

    //lấy kênh để up claim để test distributor
    function getUploadChannel() {
        $channels = AccountInfo::where("group_channel_id", 2338)
                ->orderBy("id", "desc")
                ->get(["id", "note", "handle"]);
//        $channels = AccountInfo::where("group_channel_id", 2473)
//                ->orderBy("id", "desc")
//                ->get(["id", "note", "handle"]);
//        Cache::forget('channel_usage');
        // Lấy số lần sử dụng từ cache
        $usage = Cache::get('channel_usage', []);
        Log::info("channel_usage " . json_encode($usage));

        // Lọc các kênh chưa đạt giới hạn 5 lần
        $filteredChannels = $channels->filter(function ($channel) use ($usage) {
            return (!isset($usage[$channel->note]) || $usage[$channel->note] < 6);
        });

        // Sắp xếp danh sách kênh theo số lần sử dụng, ưu tiên kênh ít được sử dụng nhất
        $sortedChannels = $filteredChannels->sortBy(function ($channel) use ($usage) {
            return $usage[$channel->note] ?? 0; // Mặc định là 0 nếu chưa từng được sử dụng
        });

        // Lấy kênh đầu tiên trong danh sách đã sắp xếp
        $selectedChannel = $sortedChannels->first();

        if ($selectedChannel) {
            // Cập nhật số lần sử dụng
            $usage[$selectedChannel->note] = isset($usage[$selectedChannel->note]) ? $usage[$selectedChannel->note] + 1 : 1;

            Cache::put('channel_usage', $usage, now()->addDay()); // Lưu vào cache với TTL 1 ngày
        }

        Log::info("selectedChannel " . json_encode($selectedChannel));
        return $selectedChannel;
    }

    //2025/02/22 xóa dữ liệu distributor để kiểm tra lại mới
    public function removeLastDistributorInfo(Request $request) {
        Log::info('ClaimController.removeLastDistributorInfo|request=' . json_encode($request->all()));
    }

}
