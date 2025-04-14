<?php

namespace App\Http\Controllers;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function mb_strlen;
use function response;
use function view;

class Promo360Controller extends Controller {

    private $domain = "https://dash.360promo.net";

//    private $domain = "http://localhost:8004";

    public function updateCustomer(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.updateCustomer|request=' . json_encode($request->all()));
        $id = $request->id;
        $type = $request->type;
        $datas = RequestHelper::callAPI2("POST", "https://dash.360promo.net/api/user/update", ["id" => $id, "type" => $type, "status_sent" => $request->status_sent]);
        if ($datas == 1) {
            return array("status" => "success", "message" => "Success");
        } else {
            return array("status" => "error", "message" => "Not found");
        }
    }

    public function syncCampaign() {
        $curr = time();
        $datas = RequestHelper::callAPI2("GET", "https://dash.360promo.net/api/campaign/sync", []);
//        Log::info(json_encode($datas));
        foreach ($datas as $data) {
            error_log("syncCampaign $data->cam_id");
            $role = json_decode($data->role);
            $type = 1;
            if (in_array("CLAIM", $role)) {
                $type = 2;
                $distributor = $data->distributor;
            } else if ((in_array("SUBMISSION", $role) || $data->submission_channel != null) && $data->submission_channel != 'null') {
                $type = 5;
            }
            if ($data->is_promo) {
                $type = 1;
            }
            if ($data->ref_id != null) {
                $campaign = CampaignStatistics::where("id", $data->ref_id)->first();
                if (!$campaign) {
                    $campaign = new CampaignStatistics();
                } else {
                    //nếu đổi tên thì update trong bảng campaign nữa
                    $newName = "$data->artist - $data->song_name";
                    if ($newName != $campaign->campaign_name) {
                        Campaign::where("campaign_id", $data->ref_id)->update(["campaign_name" => $newName]);
                    }
                }
            } else {
                $campaign = new CampaignStatistics();
                //2024/11/26 trường hợp thêm mới thì mới set giá trị submission_info
                if ($type == 5) {
                    if ($data->submission_channel != null) {
                        $array = array_values(array_filter(explode(',', $data->submission_channel)));
                        $acc = AccountInfo::whereIn("chanel_id", $array)->get(["user_name", "chanel_name"]);
                        $chans = [];
                        $mo = $campaign->money;
                        $info = [];
                        foreach ($acc as $d) {
                            $chans[] = $d->chanel_name;
                            $user = Utils::userCode2userName($d->user_name);
                            $sub = (object) [
                                        "user" => $user,
                                        "channel_name" => $d->chanel_name,
                                        "money" => $mo,
                            ];
                            $info[] = $sub;
                            $mo = 0;
                        }
                        $campaign->submission_info = json_encode($info);
                        $campaign->channel_name = implode(',', $chans);
                    }
                }
            }
            $campaign->status = $data->status;
            $pressRelease = "";

            $distributor = "";
            //tên gọi nhớ
            $name = $data->distributor;
            if ($name == null) {
                $name = $data->customer_name;
            }
            if ($campaign->id == null) {
                $campaign->target = "10M";

                $campaign->views_detail = '{"0":1}';
                $official = (object) [
                            "video_id" => "",
                            "start_view" => 0,
                            "start_like" => 0,
                            "start_cmt" => 0,
                            "start_sub" => 0,
                            "target_view" => $data->target_official != null ? $data->target_official : 0,
                            "target_like" => 0,
                            "target_cmt" => 0,
                            "target_sub" => 0,
                            "crypto_usd" => 0,
                            "crypto_usd_last" => 0,
                            "crypto_view" => 0,
                            "crypto_view_last" => 0,
                            "crypto_view_run" => 0,
                            "crypto_like" => 0,
                            "crypto_like_last" => 0,
                            "crypto_like_run" => 0,
                            "crypto_sub" => 0,
                            "crypto_sub_last" => 0,
                            "crypto_sub_run" => 0,
                            "crypto_cmt" => 0,
                            "crypto_cmt_last" => 0,
                            "crypto_cmt_run" => 0,
                            "crypto_cmt_link" => "",
                            "crypto_cmt_content" => "",
                            "cmt_schedule" => "",
                            "cmt_number" => 0,
                            "adsense_usd" => 0,
                            "adsense_view" => 0,
                            "adsense_view_run" => 0,
                            "facebook_usd" => 0
                ];

                $campaign->official = json_encode($official);
                $campaign->duration = 1;
                $campaign->seller = "360PROMO";
                $campaign->tier = 2;
                $campaign->dash_id = $data->cam_id;
//                $campaign->official_video = $data->official_video_url;
                $campaign->official_data = '{"view":0,"like":0,"comment":0,"sub":0}';
                $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
            } else {
                $relInfo = $campaign->release_info != null ? json_decode($campaign->release_info) : null;
                if ($relInfo) {
                    $pressRelease = $relInfo->press_release;
                }
                $official = $campaign->official != null ? json_decode($campaign->official) : null;
                if ($official) {
                    $official->target_view = $data->target_official != null ? $data->target_official : 0;
                }
                $campaign->official = json_encode($official);
            }
            //update metadata
            $artistInfo = (object) [
                        "email" => $data->email,
                        "picture" => $data->picture,
                        "name" => $data->name,
                        "hometown" => $data->hometown,
                        "city" => $data->city,
                        "describe_yourself" => $data->describe_yourself,
                        "style_music" => $data->style_music,
                        "bio" => $data->bio,
                        "related" => Utils::array2String($data->related),
                        "similar" => Utils::array2String($data->similar),
                        "biggest_media_coverage" => Utils::array2String($data->biggest_media_coverage),
                        "popular_songs" => Utils::array2String($data->popular_songs),
                        "biggest_collaborators" => Utils::array2String($data->biggest_collaborators),
                        "publishing_company" => $data->publishing_company,
            ];
            $campaign->artist_info = json_encode($artistInfo);
            $guestArtist = "";
            $moreMainArtist = "";
            $listGuest = [];
            if ($data->artists != null) {
                foreach (json_decode($data->artists) as $art) {
                    $guestArtist .= $art->url . PHP_EOL;
                    if (!empty($art->type) && $art->type == 'main' && $art->name != $data->artist) {
                        $moreMainArtist .= " - $art->name";
                    }
                    if (!empty($art->type) && $art->type == 'featured') {
                        $listGuest[] = $art->name;
                    }
                }
            }
            $campaign->guest_artist_1 = null;
            $campaign->guest_artist_2 = null;
            $campaign->guest_artist_3 = null;
            $ft = "";
            if (count($listGuest) > 0) {
                foreach ($listGuest as $idx => $guest) {
                    $idxp = $idx + 1;
                    $guestKey = "guest_artist_$idxp";
                    $campaign->$guestKey = $guest;
                }
                //thêm guest vào songname
                $guestString = implode(", ", $listGuest);
                $ft = "ft. $guestString";
            }

            //danh sách các bài official thứ 2, thứ 3
            $listOfficialVideo = [];
            $listOfficialVideoVersion = $data->official_video_versions != null ? json_decode($data->official_video_versions) : [];
            foreach ($listOfficialVideoVersion as $officialVersion) {
                if ($officialVersion->link != null && $officialVersion->link != "") {
                    $listOfficialVideo[] = $officialVersion->link;
                }
            }
            if ($data->official_video_url != null) {
                $campaign->official_video = $data->official_video_url;
                $listOfficialVideo[] = $data->official_video_url;
            }


            $releaseInfo = (object) [
                        "song_quote" => $data->song_quote,
                        "smart_link" => $data->smart_link,
                        "font" => $data->font,
                        "producer" => Utils::array2String($data->producer),
                        "producer_collaborators" => Utils::array2String($data->producer_collaborators),
                        "guest_artists" => $guestArtist,
                        "album_guests" => Utils::array2String($data->album_guests),
                        "engagement" => $data->engagement,
                        "official_is_video_clean" => $data->special_notes,
                        "official_video_url" => $data->official_video_url,
                        "official_video_versions" => $listOfficialVideoVersion,
                        "official_director" => $data->official_director,
                        "official_intended_release_date" => $data->official_intended_release_date,
                        "official_shoot_pics" => $data->official_shoot_pics,
                        "lyric_video_bg" => $data->lyric_video_bg,
                        "album_name" => $data->album_name,
                        "album_release_date" => $data->album_release_date,
                        "album_quote" => $data->album_quote,
                        "album_guests" => Utils::array2String($data->album_guests),
                        "album_producers" => Utils::array2String($data->album_producers),
                        "album_stream_link" => $data->album_stream_link,
                        "album_previous_name" => $data->album_previous_name,
                        "album_upc" => $data->album_upc,
                        "viral_video" => $data->viral_video,
                        "pic_single" => $data->pic_single,
                        "pic_album" => $data->pic_album,
                        "pic_logo" => $data->pic_logo,
                        "pic_press" => $data->pic_press,
                        "pic_main_profile" => $data->pic_main_profile,
                        "pic_banner" => $data->pic_banner,
                        "press_release" => $pressRelease,
                        "audio_clean" => $data->audio_clean,
                        "audio_dirty" => $data->audio_dirty,
                        "audio_instrumental" => $data->audio_instrumental,
                        "audio_acapella" => $data->audio_acapella,
            ];
            $campaign->release_info = json_encode($releaseInfo);
            $campaign->period = gmdate("Ym", strtotime($data->start_date));
            $campaign->campaign_start_date = $data->start_date;
            $campaign->genre = strtoupper($data->genre);
            $campaign->label = $data->record_label;
            $campaign->distributor = $distributor;
            $campaign->artist = $data->artist . " $ft";
            $campaign->song_name = $data->song_name;
            $campaign->isrc = $data->isrc;
            $campaign->upc = $data->album_upc;

            $campaign->artists_channel = $data->yt_channel;
            $campaign->artists_playlist = null;
            //2024/01/26 sáng confirm thêm link official và link spotify lên đầu trong artists_social nếu có
            //2024/01/26 sang tung confirm check 2 trường yt_official_video và official_video_url, ưu tiên official_video_url
            $artistsSocial = "";
            if ($data->smart_link != null) {
                $artistsSocial .= "♫ Stream: $data->smart_link" . PHP_EOL . PHP_EOL;
            }


//            if ($data->official_video_url != null && $data->official_video_url != "") {
//                $listOfficialVideo[] = $data->official_video_url;
//                $artistsSocial .= "Check out the official video: $data->official_video_url" . PHP_EOL;
//                $campaign->official_video = $data->official_video_url;
//            }
//            if ($data->spotify_song_id != null) {
//                $artistsSocial .= "Stream on Spotify: https://open.spotify.com/track/$data->spotify_song_id" . PHP_EOL;
//            }

            $tmpSocials = $data->socials != null ? json_decode($data->socials) : [];
            $artistsSocial .= "► Follow $data->artist" . PHP_EOL;
            $socialFilter = ["youtube", "instagram", "tiktok", "spotify", "facebook", "twitter"];

            foreach ($tmpSocials as $social) {
                if (Utils::containArrayString($social->url, $socialFilter)) {
                    $artistsSocial .= $social->url . PHP_EOL;
                }
            }
            $campaign->artists_social = $artistsSocial;

            $campaign->bitly_url = $data->smart_link;

            $campaign->homepage_video_update = 0;
            $campaign->audio_url = $data->audio_dirty;
            if ($campaign->audio_url == null) {
                $campaign->audio_url = $data->audio_clean;
            }
            $campaign->audio_url_cut = null;

            $campaign->lyrics = $data->lyric;
            $campaign->deezer_artist_id = 1;
            if ($data->deezer_artist_id != null) {
                $campaign->deezer_artist_id = $data->deezer_artist_id;
            }


            $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);

            $campaign->target = $data->target_video;
            $campaign->deezer_id = $data->deezer_song_id;
            if ($data->spotify_song_id != null) {
                $campaign->spotify_id = $data->spotify_song_id;
            }
            $campaign->campaign_name = "$data->artist - $data->song_name";
            $campaign->money = $data->amount;
            $campaign->amount_paid = $data->amount;
            $campaign->type = $type;

            $campaign->campaign_type = $name;
            $campaign->save();

            $campaignId = $campaign->id;

            error_log(json_encode($listOfficialVideo));
            foreach ($listOfficialVideo as $offi) {
//                error_log("offical $offi");
                $info = YoutubeHelper::processLink($offi);
                if ($info["type"] == 3) {
                    $video_id = $info["data"];
                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                    if ($videoInfo["status"] == 0) {
                        for ($t = 0; $t < 5; $t++) {
                            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                            if ($videoInfo["status"] == 1) {
                                break;
                            }
                        }
                    }
                }
//                error_log(json_encode($info));
                error_log(json_encode($videoInfo));
                if (isset($videoInfo)) {
                    foreach ($listOfficialVideoVersion as $officialVersion) {
                        if ($officialVersion->link == $offi) {
                            $officialVersion->views = $videoInfo['view'];
                            break;
                        }
                    }
                    if ($videoInfo["status"] && $info["data"]) {
                        $officialVideo = Campaign::where("campaign_id", $campaignId)->where("video_type", 1)->where("video_id", $info["data"])->first();
                        if (!$officialVideo) {
                            $officialVideo = new Campaign();
                        }
                        $officialVideo->username = '360promo';
                        $officialVideo->campaign_id = $campaignId;
                        $officialVideo->campaign_name = $campaign->campaign_name;
                        $officialVideo->channel_id = $videoInfo["channelId"];
                        $officialVideo->channel_name = $videoInfo["channelName"];
                        $officialVideo->video_type = 1;
                        $officialVideo->video_id = $video_id;
                        $officialVideo->video_title = $videoInfo["title"];
                        $officialVideo->views_detail = '[]';
                        $officialVideo->status = $videoInfo["status"];
                        $officialVideo->create_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                        $officialVideo->update_time = gmdate("m/d/Y H:i:s", $curr + 7 * 3600);
                        $officialVideo->publish_date = $videoInfo["publish_date"];
                        $officialVideo->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                        $officialVideo->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                        $officialVideo->status_confirm = 3;
                        $officialVideo->save();
                    }
                }
            }
            $releaseInfo->official_video_versions = $listOfficialVideoVersion;
            $campaign->release_info = json_encode($releaseInfo);
            $campaign->save();
            //gui id sang dash de update
            $datas = RequestHelper::callAPI2("POST", "https://dash.360promo.net/api/campaign/update", ["ref_id" => $campaign->id, "cam_id" => $data->cam_id]);
        }
    }

    public function getProgressCampaign(Request $request) {
        Log::info('|Promo360Controller.getProgressCampaign|request=' . json_encode($request->all()));
        $arrIds = [];
        if (isset($request->ids)) {
            $arrIds = explode(",", $request->ids);
        }
        $campaigns = CampaignStatistics::whereIn("dash_id", $arrIds)->get(["id", "dash_id", "views_detail", "target", "official_data", "official", "campaign_start_date", "duration"]);
        if (count($campaigns) == 0) {
            return response()->json(array("status" => "error", "campaigns" => []));
        }
        foreach ($campaigns as $data) {
            $data->progress_mix = null;
            $data->progress_official = null;
            $curr = time();
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
                    $dayLeftText = round($dayLeft * 24) . " hours left";
                } else {
                    $dayLeftText = round($dayLeft, 1) . " days left";
                }
                $current = 0;
                if ($data->views_detail != null) {
                    $current = !empty(json_decode($data->views_detail)->$id) ? json_decode($data->views_detail)->$id : 0;
                }
                $totalTarget = Utils::shortNumber2Number($data->target);
                if ($totalTarget > 0) {
                    $percent = round($current / $totalTarget * 100);
                } else {
                    $percent = 0;
                }
                //giá trị thanh progress bar. ko dc vượt quá 100%
                $percentProgress = $percent;
                if ($percent > 100) {
                    $percentProgress = 100;
                }
                $color = $this->genColor360($percent);
                //tính % số ngày còn lại để đưa vào progress bar (chưa chạy = 0%)
                $targetDay = $data->duration * 30;
                if ($dayLeft > $targetDay) {
                    $datePercent = 0;
                } else {
                    $datePercent = round($dayLeft / $targetDay * 100);
                }
                $progressMix = (object) [
                            "name" => "Mix-Lyric Views",
                            "views" => $current,
                            "target" => $totalTarget,
                            "color" => $color,
                            "percent" => $percent,
                            "percent_progress" => $percentProgress,
                            "dayLeftText" => $dayLeftText,
                            "dayLeft" => $dayLeft,
                            "start" => $startDateString,
                            "end" => $endDateString,
                            "datePercent" => 100 - $datePercent
                ];
                $data->progress_mix = $progressMix;
            }

            if ($data->official != null && $data->official_data != null) {
                $official_target = json_decode($data->official);
                $official_data = json_decode($data->official_data);
                $current = $official_data->view;
                $achieved = $official_data->view - $official_target->start_view;
                $target = Utils::shortNumber2Number($official_target->target_view);
                $totalTarget = $target + $official_target->start_view;
                if ($target > 0) {
                    $percent = round($achieved / $target * 100);
                } else {
                    $percent = 0;
                }
                $percentProgress = $percent;
                if ($percent > 100) {
                    $percentProgress = 100;
                }
                $color = $this->genColor360($percent);
                $progressOfficial = (object) [
                            "name" => "Official Views",
                            "achieved" => $achieved,
                            "target" => $target,
                            "current" => $current,
                            "target_number" => $totalTarget,
                            "color" => $color,
                            "percent" => $percent,
                            "percent_progress" => $percentProgress,
                ];
                $data->progress_official = $progressOfficial;
            }
            $data->official = null;
            $data->official_data = null;
            $data->views_detail = null;
        }

        return response()->json(array("status" => "success", "campaigns" => $campaigns));
    }

    public function addEmail(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.addEmail|request=' . json_encode($request->all()));
        $instagram = "";
        $link_music = "";
        if (isset($request->instagram)) {
            $instagram = $request->instagram;
        }
        if (isset($request->link_music)) {
            $link_music = $request->link_music;
        }
        if (!filter_var(trim($request->email), FILTER_VALIDATE_EMAIL)) {
            return array("status" => "error", "message" => "Email invalid");
        }
//        $data = RequestHelper::callAPI2("POST", "https://360promo.net/sendmusic", [
//                    "email" => $request->email,
//                    "source" => "automusic",
//                    "name" => $instagram,
//                    "link" => $link_music,
//        ]);
        $dash = RequestHelper::callAPI2("POST", "https://dash.360promo.net/api/user/reg", [
                    "email" => $request->email,
                    "name" => $request->us_name,
                    "roles" => $request->role,
                    "instagram" => $instagram,
                    "link" => $link_music,
                    "source" => "automusic",
        ]);
        return array("status" => "success", "message" => "Success", "user" => $dash);
    }

    public function getInfoUser(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.getInfoUser|request=' . json_encode($request->all()));
        $datas = null;
        if (isset($request->us_id)) {
            $datas = RequestHelper::callAPI2("POST", "https://dash.360promo.net/api/user/find", ["us_id" => $request->us_id]);
        } elseif (isset($request->email)) {
            $datas = RequestHelper::callAPI2("POST", "https://dash.360promo.net/api/user/find", ["email" => $request->email]);
        }
        return response()->json($datas);
    }

    public function resetPass(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.resetPass|request=' . json_encode($request->all()));
        $email = $request->email;
        $datas = RequestHelper::callAPI2("POST", "https://dash.360promo.net/api/user/repass", ["email" => $email]);
        return response()->json($datas);
    }

    //2024/08/29 new function
    public function index2(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|PromoController->index");
        $limit = 10;
//        $datas = RequestHelper::callAPI2("GET", "https://360promo.net/customer/get", []);
//        $lists = $datas->data;
        $datas = RequestHelper::callAPI2("GET", "$this->domain/api/user/list", []);
        $campaigns = RequestHelper::callAPI2("GET", "$this->domain/api/campaign/count", []);
        $lists = $datas->users;
        $sum = count($lists);
        $budgetEmail = $campaigns->budget_email;
        $widgetInfo = $campaigns->widget_info;
        $charts = $campaigns->charts;

        foreach ($lists as $data) {
            $data->created = gmdate("Y-m-d", strtotime($data->created_at) + 7 * 3600) . "<br>" . gmdate("H:i:s", strtotime($data->created_at) + 7 * 3600);
            $data->date_add = gmdate("Y-m", strtotime($data->created_at) + 7 * 3600);
            $data->campaign = 0;
            $data->invoice = 0;
            $data->amount_paid = 0;
            $data->amount_total = 0;
            $data->is_debit = 0;
            $data->budget_debit = 0;
            $data->campaign_debit = 0;
            $data->total_debit = 0;
            $data->create_invoice_status = 0;
            $data->total_debit = 0;

            $data->total_budget = 0;
            $data->used_amount = 0;
            $data->used_campaigns = 0;
            $data->total_paid = 0;
            $data->remaining_campaigns = 0;
            $data->outstanding_amount = 0;
            $data->usedPercentage = 0;
            $data->paidPercentage = 0;

            foreach ($budgetEmail as $budget) {
                $budget->last_payment_time = null;
                if ($budget->email == $data->email) {
                    $data->total_budget = $budget->total_budget;
                    $data->outstanding_amount = $budget->outstanding_amount;
                    $data->total_paid = $budget->total_paid;
                    $data->used_amount = $budget->used_amount;
                    $data->used_campaigns = $budget->used_campaigns;
                    $data->remaining_campaigns = $budget->remaining_campaigns;
                    if ($data->total_budget != 0) {
                        $data->usedPercentage = round($data->used_amount / $data->total_budget * 100, 1);
                        $data->paidPercentage = round($data->total_paid / $data->total_budget * 100, 1);
                    }
                    if ($data->outstanding_amount > 0) {
                        $data->is_debit = 1;
                    }
                    $budget->last_payment_time = $data->last_payment_time;
                    break;
                }
            }

//            $data->total_debit = $data->budget_debit + $data->campaign_debit;
//            $data->create_invoice_status = $data->invoice < $data->campaign ? 1 : 0;
        }
        usort($budgetEmail, function($a, $b) {
            return $a->outstanding_amount < $b->outstanding_amount;
        });
        $label = [];
        $dt = [];
        $datasets = [];

        foreach ($charts as $chart) {
            $label[] = gmdate("M-Y", strtotime($chart->period . "01"));
            $dt[] = $chart->total_amount;
        }
        $datasets[] = (object) [
                    "currency" => "$",
                    "label" => "Revenue",
                    "data" => $dt,
                    "fill" => false,
                    "borderColor" => "#039cfd",
                    "backgroundColor" => "#039cfd",
                    "borderWidth" => 1
        ];
        $dataChart = (object) [
                    "label" => $label,
                    "datasets" => $datasets,
        ];
        return view("components.promoV2", [
            "datas" => $lists,
            "sum" => $sum,
            "channels" => $this->loadMainChannels(),
            "channelsData" => $this->loadMainChannelsDatas(),
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            'method' => $this->loadPaymentMethod(),
            'widgetInfo' => $widgetInfo,
            'charts' => $charts,
            'budgetEmail' => $budgetEmail,
            'data_chart' => $dataChart
        ]);
    }

    public function loadCustomer(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.loadCustomer|request=' . json_encode($request->all()));
        $artists = RequestHelper::callAPI2("POST", "$this->domain/api/artist/list", ["email" => $request->email]);
        $budgets = RequestHelper::callAPI2("POST", "$this->domain/api/budget/get", ["email" => $request->email]);
        $user = RequestHelper::callAPI2("POST", "$this->domain/api/user/find", ["email" => $request->email]);
        $campaigns = RequestHelper::callAPI2("POST", "$this->domain/api/campaign/list", ["email" => $request->email]);
        $campaignsTransaction = RequestHelper::callAPI2("POST", "$this->domain/api/campaign2/transaction", ["email" => $request->email]);
        $artistOptionBudget = "";
        $artistOptionInvoice = "";
        $poNumber = gmdate("mish");
        $budgetsArtist = $budgets->budget_artist;
        foreach ($artists as $artist) {
            $info1 = "$0 - 0 campaign";
            $info2 = "";
            foreach ($budgetsArtist as $bArt) {
                if ($bArt->artist_id == $artist->id) {
                    $info1 = "$$bArt->remaining_amount - $bArt->remaining_campaigns campaign";
                    $info2 = "Debit $$bArt->outstanding_amount";
                    break;
                }
            }
            $artistOptionBudget .= "<option  data-content=\"<img class='rounded-circle img-cover m-r-5 disp-inline h-40px w-40px mw-40px' src='$artist->picture' onerror='this.onerror=null;this.src=&quot;images/default-avatar.png&quot;;' > <b>$artist->name </b> <span class='color-g font-12 font-italic'>$info1</span>\" value='$artist->id'></option>";
            $artistOptionInvoice .= "<option  data-content=\"<img class='rounded-circle img-cover m-r-5 disp-inline h-40px w-40px mw-40px' src='$artist->picture' onerror='this.onerror=null;this.src=&quot;images/default-avatar.png&quot;;' > <b>$artist->name </b> <span class='color-red font-12 font-italic'>$info2</span>\" value='$artist->id'></option>";
        }
        return response()->json(array(
                    "status" => "success",
                    "artists" => $artists,
                    "artistOptionBudget" => $artistOptionBudget,
                    "artistOptionInvoice" => $artistOptionInvoice,
                    "budget" => $budgets,
                    "po_number" => $poNumber,
                    "user" => $user,
                    "campaigns" => $campaigns->datas,
                    "campaign_trans" => $campaignsTransaction
        ));
    }

    public function addBudget(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.addBudget|request=' . json_encode($request->all()));
        Log::info($request->budget_desc);
        $data = RequestHelper::callAPI2("POST", $this->domain . "/api/budget/add", [
                    "email" => $request->curr_email,
                    "artist_id" => $request->artist,
                    "username" => Auth::user()->user_name,
                    "budget_amount" => $request->budget_amount,
                    "campaign_number" => $request->campaign_number,
                    "budget_desc" => $request->budget_desc,
        ]);
        return response()->json($data);
    }

    public function submitInvoice(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.submitInvoice|request=' . json_encode($request->all()));

        if (mb_strlen(trim($request->invoice_content), 'UTF-8') > 100) {
            return array("status" => "danger", "message" => "Description up to 100 characters");
        }

        $data = RequestHelper::callAPI2("POST", $this->domain . "/api/invoice2/add", [
                    "email" => $request->curr_email,
                    "invoice_id" => $request->invoice_id,
                    "artist_id" => $request->invoice_artist,
                    "amount" => $request->invoice_amount,
                    "description" => $request->invoice_content,
                    "payment_method" => $request->payment_method,
                    "username" => Auth::user()->user_name,
                    "po_number" => $request->po_number,
                    "company_name" => $request->company_name,
                    "customer_address" => $request->customer_address,
                    "customer_name" => $request->customer_name,
                    "customer_email" => $request->customer_email,
                    "is_save_info" => $request->is_save_info
        ]);

        if (empty($data->invoice->invoice_id)) {
            return response()->json($data);
        }
        $paymentLink = "$this->domain/payment/v2?invoice_id=" . $data->invoice->invoice_id;
        return response()->json(array("status" => "success", "invoice" => $data->invoice, "message" => $paymentLink));
    }

    public function loadInvoice(Request $request) {
        $invoices = RequestHelper::callAPI2("POST", "$this->domain/api/invoice2/list", ["email" => $request->email]);
        return response()->json(["status" => "success", "invoices" => $invoices]);
    }

    public function confirmInvoice(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.confirmInvoice|request=' . json_encode($request->all()));
        $datas = RequestHelper::callAPI2(
                        "POST", "$this->domain/api/invoice2/confirm", ["invoice_id" => $request->invoice_id, "username" => Auth::user()->user_name]
        );
        Log::info(json_encode($datas));
        return response()->json($datas);
    }

    public function activeCampaign(Request $request) {
        Log::info(Auth::user()->user_name . '|Promo360Controller.submitCampaign|request=' . json_encode($request->all()));
        if ($request->campaign_type == 'sub' && $request->channel_id == null) {
            return response()->json(array("status" => "error", "message" => "Choose one submission channel"));
        }

        $data = RequestHelper::callAPI2("POST", $this->domain . "/api/campaign2/active", [
                    "email" => $request->curr_email,
                    "campaign_id" => $request->campaign_id,
                    "amount" => $request->campaign_amount,
                    "youtube_music" => $request->invoice_youtube_music,
                    "official_video" => $request->invoice_official_video,
                    "campaign_type" => $request->campaign_type,
                    "channel_id" => $request->channel_id,
                    "username" => Auth::user()->user_name,
        ]);
        return response()->json($data);
    }

}
