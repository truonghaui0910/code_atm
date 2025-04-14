<?php

namespace App\Http\Controllers;

use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\AthenaTableau;
use App\Http\Models\AthenaTableauReport;
use App\Http\Models\CampaignStatistics;
use FPDI as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use TheSeer\Tokenizer\Exception;

class TableauController extends Controller {

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|TableauController.index|request=' . json_encode($request->all()));
        $startTime = time();
        $queries = [];
        $lastSync = "";
        if (in_array("1", explode(",", $user->role))) {
            $promos = DB::select("select distinct promo_id,promo_name from athena_tableau order by promo_id desc");
        } else {
            $promos = DB::select("select distinct promo_id,promo_name from athena_tableau where promo_id not in (select id from campaign_statistics where type = 2) order by promo_id desc");
        }
        if (count($promos) > 0) {
            $listPromo = $this->loadCampaignTableau($request, $promos);
            $promoId = $promos[0]->promo_id;
            if (isset($request->promo_id)) {
                $promoId = $request->promo_id;
            }

            $lastReport = AthenaTableauReport::where('username', $user->user_name)->where("promo_id", $promoId);
            if (isset($request->date_caculate)) {
                $lastReport = $lastReport->where("create_date", $request->date_caculate);
            }
            $lastReport = $lastReport->take(1)->orderBy("id", "desc")->get();

            $datas = AthenaTableau::where("promo_id", $promoId);
//            if (isset($request->video_type) && $request->video_type != '-1') {
//                $datas = $datas->where("video_type", $request->video_type);
//            }
            $minimumView = 1000;
            if (isset($request->minimum_view)) {
                $minimumView = $request->minimum_view;
            } else {
                $request['minimum_view'] = $minimumView;
            }
            $datas = $datas->where("views", ">=", $minimumView);

            if (isset($request->sort)) {
                $queries['sort'] = $request->sort;
                if (isset($request->order)) {
                    $queries['order'] = $request->order;
                }
            } else {
                //set mặc định sẽ search theo id asc
                $request['sort'] = 'views';
                $request['direction'] = 'desc';
                $queries['sort'] = 'views';
                $queries['direction'] = 'desc';
            }
            $datas = $datas->sortable()->paginate(5000)->appends($queries);
            $countMix = 0;
            $countLyric = 0;
            foreach ($datas as $data) {
                if (strlen($data->video_title) > 80) {
                    $data->video_title_short = substr($data->video_title, 0, 80) . '...';
                } else {
                    $data->video_title_short = $data->video_title;
                }
                if ($data->video_type == 2) {
                    $countLyric++;
                } else if ($data->video_type == 5) {
                    $countMix++;
                }
            }
            $promoStats = DB::select("select promo_id,promo_name,max(update_time) as update_time, sum(views_athena) as views_athena,sum(views) as views, 
                                count(id) as videos,sum(watch_time_hours) as watch_time_hours,
                                sum(suggested_traffic_views) as suggested_traffic_views,
                                SUM(IF(video_type = 2, views, 0)) as views_lyric,
                                SUM(IF(video_type = 5, views, 0)) as views_mix,
                                SUM(IF(video_type = 2, views_athena, 0)) as views_lyric_athena,
                                SUM(IF(video_type = 5, views_athena, 0)) as views_mix_athena,
                                SUM(IF(video_type = 2, watch_time_hours, 0)) as watch_time_hours_lyric,
                                SUM(IF(video_type = 5, watch_time_hours, 0)) as watch_time_hours_mix,
                                SUM(IF(video_type = 2, suggested_traffic_views, 0)) as suggested_traffic_views_lyric,
                                SUM(IF(video_type = 5, suggested_traffic_views, 0)) as suggested_traffic_views_mix
                                from athena_tableau where promo_id = $promoId and views >= $minimumView  group by promo_id,promo_name ");
//            Log:info(DB::getQueryLog());
            $lastSync = "";
            if (count($promoStats) > 0) {
                $promoStats[0]->count_lyric = $countLyric;
                $promoStats[0]->count_mix = $countMix;
                $lastSync = "(Last Sync " . $promoStats[0]->update_time . " GMT+07:00)";
            }
            Log::info("Time:" . (time() - $startTime));
            return view('components.tableau', [
                'datas' => $datas,
                'request' => $request,
                'promoStats' => $promoStats,
                'listPromosOption' => $listPromo,
                'lastSync' => $lastSync,
                'lastReport' => $lastReport,
                'listDate' => $this->loadListDateTableauReport($request),
            ]);
        } else {
            return view('components.tableau', [
                'datas' => [],
                'request' => $request,
                'promoStats' => [],
                'listPromosOption' => "",
                'lastSync' => $lastSync,
                'lastReport' => [],
                'listDate' => '',
            ]);
        }
    }

    public function exportPdf(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TableauController.exportPdf|request=' . json_encode($request->all()));
        $promoId = $request->promo_id;
        $minimumView = 1000;
        if (isset($request->minimum_view)) {
            $minimumView = $request->minimum_view;
        }
        $dateExport = gmdate("m/d", time() + $user->timezone * 3600);
        $promoStats = DB::select("select promo_id,promo_name,sum(views_athena) as views_athena,sum(views) as views, 
                                count(id) as videos,sum(watch_time_hours) as watch_time_hours,
                                sum(suggested_traffic_views) as suggested_traffic_views,
                                SUM(IF(video_type = 2, views, 0)) as views_lyric,
                                SUM(IF(video_type = 5, views, 0)) as views_mix,
                                SUM(IF(video_type = 2, views_athena, 0)) as views_lyric_athena,
                                SUM(IF(video_type = 5, views_athena, 0)) as views_mix_athena,
                                SUM(IF(video_type = 2, watch_time_hours, 0)) as watch_time_hours_lyric,
                                SUM(IF(video_type = 5, watch_time_hours, 0)) as watch_time_hours_mix,
                                SUM(IF(video_type = 2, suggested_traffic_views, 0)) as suggested_traffic_views_lyric,
                                SUM(IF(video_type = 5, suggested_traffic_views, 0)) as suggested_traffic_views_mix
                                from athena_tableau where promo_id = $promoId and views >= $minimumView group by promo_id,promo_name ");
        $campaign = CampaignStatistics::where("id", $promoId)->first();
        $songName = $campaign->song_name;
        $company = $request->template;
//        DUNN DEAL PR | ORFIUM
        $datas = AthenaTableau::where("promo_id", $promoId)->where("views", ">=", $minimumView)->orderBy("views", "desc")->get();
        $countMix = 0;
        $countLyric = 0;

        //lấy thumb 3 video làm ảnh, 
        $thumb1 = "";
        $thumbLyric = "";
        $thumbMix = "";
        if (isset($request->art_thumb_mix) && count($request->art_thumb_mix) > 0) {
            $thumbMix = $request->art_thumb_mix[0];
        }
        Log::info("thumbMix $thumbMix");
        if (isset($request->art_thumb_lyric) && count($request->art_thumb_lyric) > 1) {
            $thumb1 = $request->art_thumb_lyric[0];
            $thumbLyric = $request->art_thumb_lyric[1];
        }
        foreach ($datas as $data) {
            if ($data->video_type == 2) {
                $countLyric++;
                if ($thumb1 == "" && Utils::urlExists("https://i.ytimg.com/vi/$data->video_id/hq720.jpg")) {
                    $thumb1 = $data->video_id;
                    continue;
                }
                if ($thumbLyric == "" && Utils::urlExists("https://i.ytimg.com/vi/$data->video_id/hq720.jpg") && $data->video_id != $thumb1) {
                    $thumbLyric = $data->video_id;
                }
            } else if ($data->video_type == 5) {
                $countMix++;
                if ($thumbMix == "" && Utils::urlExists("https://i.ytimg.com/vi/$data->video_id/hq720.jpg") && $data->video_id != $thumb1) {
                    $thumbMix = $data->video_id;
                }
            }
        }
        Log::info("thumbMix $thumbMix");
        //xử lý dữ liệu
        if (count($promoStats) > 0) {
            $campaignName = $campaign->campaign_name;
            $videosTotal = number_format($promoStats[0]->videos, 0, '.', ',');
            $viewTotal = number_format($promoStats[0]->views, 0, '.', ',');
            $viewMix = number_format($promoStats[0]->views_mix, 0, '.', ',');
            $viewLyric = number_format($promoStats[0]->views_lyric, 0, '.', ',');
            $videosMix = number_format($countMix, 0, '.', ',');
            $videosLyric = number_format($countLyric, 0, '.', ',');
            $watch_time_hours_lyric = number_format($promoStats[0]->watch_time_hours_lyric, 0, '.', ',');
            $watch_time_hours_mix = number_format($promoStats[0]->watch_time_hours_mix, 0, '.', ',');
            $suggested_views_percent_lyric = 0;
            $suggested_views_percent_mix = 0;
            if ($promoStats[0]->views_lyric_athena > 0) {
                $suggested_views_percent_lyric = round($promoStats[0]->suggested_traffic_views_lyric / $promoStats[0]->views_lyric_athena * 100);
            }
            if ($promoStats[0]->views_mix_athena > 0) {
                $suggested_views_percent_mix = round($promoStats[0]->suggested_traffic_views_mix / $promoStats[0]->views_mix_athena * 100);
            }
        } else {
            return array("message" => "Not found promo $promoId");
        }
        $lastReport = null;
        if ($request->date_caculate != "") {
            $lastReport = AthenaTableauReport::where('username', $user->user_name)->where("promo_id", $promoId)->where("create_date", $request->date_caculate)->orderBy("id", "desc")->first();
        }
        $tableauReport = new AthenaTableauReport();
        $tableauReport->username = $user->user_name;
        $tableauReport->type = $request->type;
        $tableauReport->promo_id = $promoId;
        $tableauReport->promo_name = $campaignName;
        $tableauReport->videos = $videosTotal;
        $tableauReport->videos_lyric = $countLyric;
        $tableauReport->videos_mix = $countMix;
        $tableauReport->views = $promoStats[0]->views;
        $tableauReport->views_athena = $promoStats[0]->views_athena;
        $tableauReport->views_lyric = $promoStats[0]->views_lyric;
        $tableauReport->views_lyric_athena = $promoStats[0]->views_lyric_athena;
        $tableauReport->views_mix = $promoStats[0]->views_mix;
        $tableauReport->views_mix_athena = $promoStats[0]->views_mix_athena;
        $tableauReport->suggested_traffic_views = $promoStats[0]->suggested_traffic_views;
        $tableauReport->suggested_traffic_views_lyric = $promoStats[0]->suggested_traffic_views_lyric;
        $tableauReport->suggested_traffic_views_mix = $promoStats[0]->suggested_traffic_views_mix;
        $tableauReport->watch_time_hours = $promoStats[0]->watch_time_hours;
        $tableauReport->watch_time_hours_lyric = $promoStats[0]->watch_time_hours_lyric;
        $tableauReport->watch_time_hours_mix = $promoStats[0]->watch_time_hours_mix;
        $tableauReport->create_date = gmdate("Y/m/d", time() + $user->timezone * 3600);
        $tableauReport->create_time = gmdate("H:i:s", time() + $user->timezone * 3600);
        $tableauReport->save();
        if ($request->type == 1) {
            $percentLyricViewUp = 0;
            $percentLyricWatchTimeUp = 0;
            $percentMixViewUp = 0;
            $percentMixWatchTimeUp = 0;
            $lastDate = null;

            if ($lastReport != null && $lastReport) {
                $lastDate = strtotime("$lastReport->create_date $lastReport->create_time GMT+07:00");
                if ($lastReport->views_lyric > 0) {
                    $percentLyricViewUp = round(($promoStats[0]->views_lyric - $lastReport->views_lyric) / $lastReport->views_lyric * 100);
                }
                if ($lastReport->watch_time_hours_lyric > 0) {
                    $percentLyricWatchTimeUp = round(($promoStats[0]->watch_time_hours_lyric - $lastReport->watch_time_hours_lyric) / $lastReport->watch_time_hours_lyric * 100);
                }
                if ($lastReport->views_mix > 0) {
                    $percentMixViewUp = round(($promoStats[0]->views_mix - $lastReport->views_mix) / $lastReport->views_mix * 100);
                }
                if ($lastReport->watch_time_hours_mix > 0) {
                    $percentMixWatchTimeUp = round(($promoStats[0]->watch_time_hours_mix - $lastReport->watch_time_hours_mix) / $lastReport->watch_time_hours_mix * 100);
                }
            }
            // <editor-fold defaultstate="collapsed" desc="Campaign update report">
            $pdf = new PDF('l');
            $pdf->setSourceFile('promos/' . strtolower($company) . '_campaign_update.pdf');

            //page1
            // Import the first page from the PDF and add to dynamic PDF
            $tpl = $pdf->importPage(1);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);
            // Set the default font to use
            $pdf->SetFont('Helvetica');
            // adding a Cell using:
            // $pdf->Cell( $width, $height, $text, $border, $fill, $align);
//            $pdf->SetFontSize('20'); // set font size
            $pdf->SetFont('Helvetica', '', 20);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(15, 97); // set the position of the box
            $pdf->Cell(0, 10, $campaignName, 0, 0, 'C'); // add the text, align to Center of cell

            $pdf->SetXY(15, 109);
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->Cell(0, 10, "$dateExport Promotional Campaign Update", 0, 0, 'C');


            //===================================page2
            $tpl = $pdf->importPage(2);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);

            //set number page
            $pdf->SetFont('Helvetica', '', 14);
            $pdf->SetFontSize('14');
            $pdf->SetXY(6, 5);
            $pdf->Cell(0, 10, '01', 0, 0, 'L');

            //set text title
            $pdf->SetTextColor(178, 183, 183);
            $pdf->SetFontSize('14');
            $pdf->SetXY(25, 5);
            $pdf->Cell(0, 10, "$company + $campaignName $dateExport", 0, 0, 'L');

            //format font text detail
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFontSize('10');
            //set Lyric Video Views
            $pdf->SetXY(22, 49);
            $pdf->MultiCell(100, 5, "$viewLyric Total Views across $videosLyric videos\nUp $percentLyricViewUp% from previous report", 0, 'L');
            //set Lyric Video Watch Time
            $textView = "$watch_time_hours_lyric Total Hours of Watch Time\nUp $percentLyricWatchTimeUp% from previous report";
            $pdf->SetXY(22, 72);
            $pdf->MultiCell(100, 5, $textView, 0, 'L');
            //set top Traffic Source
            $pdf->SetXY(22, 94);
            $pdf->Cell(0, 10, "$suggested_views_percent_lyric% of Views coming from the Suggested Video Feed.", 0, 0, 'L');
            $pdf->SetXY(22, 104);
            $pdf->MultiCell(80, 5, "This Indicates that we are converting viewers on other videos into viewers of your content and are not cannibalizing your search traffic.", 0, 'L');
            if (Utils::urlExists("https://i.ytimg.com/vi/$thumbLyric/hq720.jpg")) {
                $pdf->Image("https://i.ytimg.com/vi/$thumbLyric/hq720.jpg", 127, 28, 156, 88, 'JPG', "https://www.youtube.com/watch?v=$thumbLyric");
            }

            //===================================page3
            $tpl = $pdf->importPage(3);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);

            //set number page
            $pdf->SetFontSize('14');
            $pdf->SetXY(6, 5);
            $pdf->Cell(0, 10, '02', 0, 0, 'L');

            //set text title
            $pdf->SetTextColor(178, 183, 183);
            $pdf->SetFontSize('14');
            $pdf->SetXY(25, 5);
            $pdf->Cell(0, 10, "$company + $campaignName $dateExport", 0, 0, 'L');

            //format font text detail
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFontSize('10');
            //set mix Video Views
            $pdf->SetXY(22, 43);
            $pdf->MultiCell(100, 5, "$viewMix Total Views across $videosMix videos\nUp $percentMixViewUp% from previous report", 0, 'L');
            $pdf->SetXY(22, 56);
            $pdf->MultiCell(90, 5, "Promotional Mixes contain curated songs, including \"$campaign->song_name\", played back-to-back in playlist style format.", 0, 'L');
            //set text Mix Video Watch Time
            $pdf->SetXY(22, 79);
            $pdf->MultiCell(100, 5, "$watch_time_hours_mix Total Hours of Watch Time\nUp $percentMixWatchTimeUp% from previous report", 0, 'L');
            //set text Top Traffic Source
            $pdf->SetXY(22, 100);
            $pdf->Cell(0, 10, "$suggested_views_percent_mix% of Views coming from the Suggested Video Feed.", 0, 0, 'L');
            $pdf->SetXY(22, 110);
            $pdf->MultiCell(90, 4, "This Indicates that we are converting viewers on other videos into viewers of your content and are not cannibalizing your search traffic", 0, 'L');
            if (Utils::urlExists("https://i.ytimg.com/vi/$thumbMix/hq720.jpg")) {
                $pdf->Image("https://i.ytimg.com/vi/$thumbMix/hq720.jpg", 127, 28, 156, 88, 'JPG', "https://www.youtube.com/watch?v=$thumbMix");
            }

            // render PDF to browser
            $pdf->Output('D', "$company + $campaign->artist - _$campaign->song_name" . "_ Campaign Update.pdf", true);
            // </editor-fold>
        } else {
            // <editor-fold defaultstate="collapsed" desc="Wrap report">
            $pdf = new PDF('l');
            $pdf->setSourceFile('promos/' . strtolower($company) . '_wrap_report.pdf');

            //page1
            // Import the first page from the PDF and add to dynamic PDF
            $tpl = $pdf->importPage(1);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);
            // Set the default font to use
            $pdf->SetFont('Helvetica');
            // adding a Cell using:
            // $pdf->Cell( $width, $height, $text, $border, $fill, $align);
//            $pdf->SetFontSize('20'); // set font size
            $pdf->SetFont('Helvetica', '', 17);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(15, 97); // set the position of the box
            $pdf->Cell(0, 10, "$campaignName $dateExport", 0, 0, 'C'); // add the text, align to Center of cell

            $pdf->SetXY(15, 109); // set the position of the box
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->Cell(0, 10, "$dateExport Promotional Campaign Wrap Report", 0, 0, 'C');
            //===================================page2
            $tpl = $pdf->importPage(2);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);

            //set number page
            $pdf->SetFont('Helvetica', '', 14);
            $pdf->SetFontSize('14');
            $pdf->SetXY(6, 5);
            $pdf->Cell(0, 10, '01', 0, 0, 'L');

            //set text title
            $pdf->SetTextColor(178, 183, 183);
            $pdf->SetFontSize('14');
            $pdf->SetXY(25, 5);
            $pdf->Cell(0, 10, "$company + $campaignName $dateExport", 0, 0, 'L');

            //format font text detail
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFontSize('10');
            //set text overview
            $pdf->SetXY(22, 47);
            $pdf->Cell(0, 10, "$viewTotal Total Views across $videosTotal videos", 0, 0, 'L');
            //set text views
            $textView = "Promo Lyric Videos: $viewLyric views\nPromo Mix Videos: $viewMix views";
            $pdf->SetXY(22, 72);
            $pdf->MultiCell(100, 5, $textView, 0, 'L');
            //set text video
            $textVideo = "Promo Lyric Videos: $videosLyric\nPromo Mix Videos: $videosMix";
            $pdf->SetXY(22, 98);
            $pdf->MultiCell(100, 5, $textVideo, 0, 'L');
            if (Utils::urlExists("https://i.ytimg.com/vi/$thumb1/hq720.jpg")) {
                $pdf->Image("https://i.ytimg.com/vi/$thumb1/hq720.jpg", 127, 28, 156, 88, 'JPG', "https://www.youtube.com/watch?v=$thumb1");
            }

            //===================================page3
            $tpl = $pdf->importPage(3);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);

            //set number page
            $pdf->SetFontSize('14');
            $pdf->SetXY(6, 5);
            $pdf->Cell(0, 10, '02', 0, 0, 'L');

            //set text title
            $pdf->SetTextColor(178, 183, 183);
            $pdf->SetFontSize('14');
            $pdf->SetXY(25, 5);
            $pdf->Cell(0, 10, "$company + $campaignName $dateExport", 0, 0, 'L');

            //format font text detail
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFontSize('10');
            //set lyric Video Views
            $pdf->SetXY(22, 47);
            $pdf->Cell(0, 10, "$viewLyric Total Views across $videosLyric videos", 0, 0, 'L');
            //set text lyric Video Watch Time
            $pdf->SetXY(22, 71);
            $pdf->Cell(0, 10, "$watch_time_hours_lyric Total Hours of Watch Time", 0, 0, 'L');
            //set text Top Traffic Source
            $pdf->SetXY(22, 94);
            $pdf->Cell(0, 10, "$suggested_views_percent_lyric% of Views coming from the Suggested Video Feed", 0, 0, 'L');
            //set 
            $textVideo = "This Indicates that we are converting viewers on other videos into viewers of your content and are not cannibalizing your search traffic.";
            $pdf->SetXY(22, 104);
            $pdf->MultiCell(80, 5, $textVideo, 0, 'L');
            if (Utils::urlExists("https://i.ytimg.com/vi/$thumbLyric/hq720.jpg")) {
                $pdf->Image("https://i.ytimg.com/vi/$thumbLyric/hq720.jpg", 127, 28, 156, 88, 'JPG', "https://www.youtube.com/watch?v=$thumbLyric");
            }


            //===================================page4
            $tpl = $pdf->importPage(4);
            $specs = $pdf->getTemplateSize($tpl);
            $pdf->AddPage("L", array($specs['w'], $specs['h']));
            $pdf->useTemplate($tpl);

            //set number page
            $pdf->SetFontSize('14');
            $pdf->SetXY(6, 5);
            $pdf->Cell(0, 10, '03', 0, 0, 'L');

            //set text title
            $pdf->SetTextColor(178, 183, 183);
            $pdf->SetFontSize('14');
            $pdf->SetXY(25, 5);
            $pdf->Cell(0, 10, "$company + $campaignName $dateExport", 0, 0, 'L');

            //format font text detail
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFontSize('10');

            //set Promotional Mix Videos
            $textPromotionalMix = 'Promotional Mixes contain curated songs,including this song, played back-to-back in playlist style format.';
            $pdf->SetXY(22, 46);
            $pdf->MultiCell(90, 5, $textPromotionalMix, 0, 'L');

            //set text Mix Video Views
            $textMixVideoView = "$viewMix Total Views across $videosMix mix videos that contain \"$songName\"";
            $pdf->SetXY(22, 77);
            $pdf->MultiCell(90, 5, $textMixVideoView, 0, 'L');

            //set text Mix Video Views
            $textMixVideoWatchTime = "$watch_time_hours_mix Total Hours of Watch Time across $videosMix mix videos that contain \"$songName\"";
            $pdf->SetXY(22, 102);
            $pdf->MultiCell(90, 5, $textMixVideoWatchTime, 0, 'L');
            if (Utils::urlExists("https://i.ytimg.com/vi/$thumbMix/hq720.jpg")) {
                Log::info("vao $thumbMix");
//            $pdf->Image($url, $x, $y, $w, $h, $type, $openUrl);
                $pdf->Image("https://i.ytimg.com/vi/$thumbMix/hq720.jpg", 127, 28, 156, 88, 'JPG', "https://www.youtube.com/watch?v=$thumbMix");
            }
            // render PDF to browser
            $pdf->Output('D', "$company + $campaign->artist - _$campaign->song_name" . "_ Campaign Wrap Report.pdf", true);
            // </editor-fold> 
        }
    }

    public function exportCsv(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TableauController.exportCsv|request=' . json_encode($request->all()));
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30);
        try {

            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=campaign_' . " $request->promo_id _" . date('Ymd') . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];


            $datas = AthenaTableau::where("promo_id", $request->promo_id)->where("views", ">=", $request->minimum_view)
                            ->orderBy("views", "desc")->get(["video_id", "video_title",
                DB::raw("CASE WHEN video_type ='5' THEN 'mix' WHEN video_type ='2' THEN 'lyric' END as video_type"),
                "channel_name", "views", "views_athena", "watch_time_hours", "suggested_traffic_views",
                DB::raw("if(views_athena>0,round(suggested_traffic_views/views_athena *100 ),0) as percent")]);


            $lists = $datas->toArray();
            $title = array('VideoId', 'VideoTitle', 'Type', "Channel", "Views", "Bulk View", "Watch Time(h)", "Suggested Views", "% Suggested");
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

    public function runSync(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|TableauController.runSync|request=' . json_encode($request->all()));
        RequestHelper::fetchWithoutResponseURL("http://automusic.win/api/sync/tableau/athena");
        return array("status" => "success");
    }

    //tổng kết dữ liệu promo từ bảng athena_promo_sync sang campaign
    public function syncTableau() {
//        $locker = new Locker(9892);
//        $locker->lock();
        $startTime = time();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 72000);
        $query = "SELECT promo.campaign_id,
                promo.campaign_name,
                promo.video_id,
                promo.channel_id,
                promo.video_title,
                promo.channel_name,
                SUM(stats.views) AS views,
                SUM(stats.watch_time_minutes) / 60 AS watch_time_hours,
                SUM(ts.traffic_views) AS suggested_traffic_views
                FROM (SELECT campaign_id,
                                        campaign_name,
                                        video_id,
                                        channel_id,
                                        video_title,
                                        channel_name
                                FROM promovideos
                                WHERE TRUE
                                        and status = 1
                        ) AS promo
                        LEFT JOIN (
                                SELECT video_id,
                                        channel_id,
                                        SUM(views) AS views,
                                        SUM(watch_time_minutes) AS watch_time_minutes
                                FROM videobasic
                                WHERE TRUE
                                GROUP BY video_id,
                                        channel_id
                        ) AS stats ON promo.video_id = stats.video_id
                        AND promo.channel_id = stats.channel_id
                        LEFT JOIN (
                                SELECT video_id,
                                        channel_id,
                                        SUM(views) AS traffic_views
                                FROM channeltrafficsourcea2
                                WHERE TRUE
                                GROUP BY video_id,
                                        channel_id
                        ) AS ts ON promo.video_id = ts.video_id
                        AND promo.channel_id = ts.channel_id
                WHERE TRUE
                GROUP BY promo.campaign_id,
                        promo.campaign_name,
                        promo.video_id,
                        promo.channel_id,
                        promo.video_title,
                        promo.channel_name";

        error_log("syncTableau QR: $query");
        $input = ["query" => $query];
        $datas = RequestHelper::callAPI("POST", "http://plla.autoseo.win/query", $input);
//        Utils::write("tab.txt", json_encode($datas));
        error_log("syncTableau RS: " . count($datas));
//        if (count($datas) > 1) {
//            DB::statement("delete from athena_tableau");
//        }
        foreach ($datas as $data) {
            $insert = AthenaTableau::where("promo_id", $data->campaign_id)->where("video_id", $data->video_id)->first();
            if (!$insert) {
                $insert = new AthenaTableau();
                $insert->promo_id = $data->campaign_id;
                $insert->promo_name = $data->campaign_name;
                $insert->video_id = $data->video_id;
                $insert->video_title = $data->video_title;
                $insert->channel_id = $data->channel_id;
                $insert->channel_name = $data->channel_name;
            }
            $insert->views_athena = $data->views;
            $insert->watch_time_hours = $data->watch_time_hours;
            $insert->suggested_traffic_views = $data->suggested_traffic_views;
            $insert->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $insert->save();
//                error_log("syncTableau " . $data->video_id);
        }
        DB::statement("UPDATE athena_tableau a INNER JOIN campaign b ON a.video_id= b.video_id SET a.views= b.views,a.video_title = b.video_title,a.video_type = b.video_type");
        DB::statement("UPDATE athena_tableau a INNER JOIN campaign_statistics b ON a.promo_id= b.id SET a.promo_name= b.campaign_name");
        $time = time() - $startTime;
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Finish syncTableau " . round($time / 60) . ' minutes');
        Log::info("Finish syncTableau");
        return count($datas);
    }

    //tổng kết dữ liệu promo từ bảng athena_promo_sync sang campaign
    public function syncTableau2() {
//        $locker = new Locker(9892);
//        $locker->lock();
        $startTime = time();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 72000);
        $query = "SELECT campaign_id,
                        campaign_name,
                        video_id,
                        channel_id,
                        video_title,
                        channel_name,
                        views,
                        0 AS watch_time_hours,
                        0 AS suggested_traffic_views
                 FROM campaign
                 WHERE status_confirm = 3
                   AND video_type <> 1
                   AND campaign_id in
                     (SELECT id
                      FROM campaign_statistics
                      WHERE status = 1
                        AND TYPE in (1,5))";

        $datas = DB::select($query);
        foreach ($datas as $data) {
            $insert = AthenaTableau::where("promo_id", $data->campaign_id)->where("video_id", $data->video_id)->first();
            if (!$insert) {
                $insert = new AthenaTableau();
                $insert->promo_id = $data->campaign_id;
                $insert->promo_name = $data->campaign_name;
                $insert->video_id = $data->video_id;
                $insert->video_title = $data->video_title;
                $insert->channel_id = $data->channel_id;
                $insert->channel_name = $data->channel_name;
            }
            $insert->views_athena = $data->views;
            $insert->watch_time_hours = $data->watch_time_hours;
            $insert->suggested_traffic_views = $data->suggested_traffic_views;
            $insert->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $insert->save();
//                error_log("syncTableau " . $data->video_id);
        }
        DB::statement("UPDATE athena_tableau a INNER JOIN campaign b ON a.video_id= b.video_id SET a.views= b.views,a.video_title = b.video_title,a.video_type = b.video_type");
        DB::statement("UPDATE athena_tableau a INNER JOIN campaign_statistics b ON a.promo_id= b.id SET a.promo_name= b.campaign_name");
        $time = time() - $startTime;
        ProxyHelper::get("https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=Finish syncTableau " . round($time / 60) . ' minutes');
        Log::info("Finish syncTableau");
        return count($datas);
    }

}
