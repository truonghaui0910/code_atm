<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

use App\Common\Youtube\YoutubeHelper;
use App\Http\Controllers\ApiController;
use App\Http\Models\AthenaPromoSync;
use App\Http\Models\Font;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

//use Log;
ini_set('max_execution_time', 180);
ini_set('upload_max_filesize', '200M');
Route::get('/name', function () {
//    gbak downloadfolder --sv studio-result --idx "https://drive.google.com/drive/folders/1t6RznEyyObz7Y86b5jtjht3GeqwV13Hh"
    $link = "http://automusic.win/live_file/";
    $dir = "/home/automusic.win/public_html/public/live_file/";
    $jsons = json_decode(file_get_contents("/home/automusic.win/public_html/public/live_file/name.json"));
    foreach ($jsons as $data) {
        $newName = str_replace(" ", "", $data->originalFilename);
        $oldName = str_replace("./", "", $data->path);
        rename($dir . $oldName, $dir . $newName);
        echo $link . $newName;
    }
});

Route::get('/views/{date}', function ($d) {
//    $d = "202207";
    $datas = DB::select("SELECT distinct video_id, views,views_real,insert_date FROM `campaign` WHERE status_confirm=3 and status_use =1 and campaign_id in (SELECT `id` FROM `campaign_statistics` WHERE `type` in (1,2,4,5) AND `status` IN (1,4)) and insert_date like '%$d%'");
    $count = count($datas);
    $i = 0;
    foreach ($datas as $data) {
        $i++;
        DB::statement("update athena_promo_sync set views_real = 0 where video_id = '$data->video_id'");
        $temp = AthenaPromoSync::where("video_id", $data->video_id)->where("date", "like", "%$d%")->first();
        if ($temp) {
            $temp->views_real = $data->views;
            $temp->views_real_daily = $data->views;
            $temp->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $temp->save();
            error_log("$i/$count update $data->video_id $data->views_real");
        } else {
            $insert = new AthenaPromoSync();
            $insert->date = $d . "01";
            $insert->video_id = $data->video_id;
            $insert->views_real = $data->views;
            $insert->views_real_daily = $data->views;
            $insert->likes = 0;
            $insert->dislikes = 0;
            $insert->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $insert->save();
            error_log("$i/$count insert $data->video_id $data->views_real");
        }
    }
    return $i;
});

Route::get('sso/login', 'SSOController@redirectToProvider')->name('redirectToProvider');
Route::get('sso/login/callback', 'SSOController@handleProviderCallback')->name('handleProviderCallback');

Route::group(['middleware' => 'localization', 'prefix' => Session::get('locale')], function() {

    // Authentication Routes...
    Route::get('/', 'UserController@index');
    Route::get('login', 'UserController@index')->name('login');
    Route::post('login', 'UserController@login');
    Route::get('logout', 'UserController@logout')->name('logout');

    Route::post('apilogin', 'UserController@apilogin')->name('apilogin');
    Route::get('checkToken', 'UserController@checkToken')->name('checkToken');
    Route::group(['middleware' => 'tokenCheck'], function() {
        Route::get('/api/get/channel/info', 'ApiController@getChannelInfoByChannelId')->name('getChannelInfoByChannelId');
        Route::get('getlistchannelbyuser', 'ApiController@getlistchannelbyuser')->name('getlistchannelbyuser');
        Route::get('/testtokenget', 'ApiController@index')->name('getdataimage');
        Route::post('/testtokenpost', 'ApiController@index')->name('testtokenpost');
    });

    // Registration Routes...
//    Route::get('register', 'UserController@viewRegister')->name('register');
//    Route::post('register', 'UserController@onCreateNewUser');

    Route::post('/lang', [
        'as' => 'switchLang',
        'uses' => 'LangController@postLang',
    ]);
    Route::get('/font', function () {
        $count = 0;
        $arrFont = array("title", "song_name");
        foreach ($arrFont as $data) {
            $dir = public_path() . "/rs/font/$data";
            $ffs = scandir($dir);
            if ($data == 'title') {
                $type = 1;
            } else if ($data == 'song_name') {
                $type = 2;
            }
            $r_from = '/home/automusic.win/public_html/public';
            $r_to = 'http://automusic.win';
            unset($ffs[array_search('.', $ffs, true)]);
            unset($ffs[array_search('..', $ffs, true)]);
            $temp = '';
            foreach ($ffs as $ff) {
                $temp = "$dir/" . $ff;
                $temp = str_replace($r_from, $r_to, $temp);
//                echo $temp . '<br>';
                $check = Font::where('link', $temp)->first();
                if (!$check) {
                    $font = new Font();
                    $font->type = $type;
                    $font->name = strtolower($ff);
                    $font->link = $temp;
                    $font->save();
                    $count++;
                    Log::info(json_encode($font));
                }
            }
        }
        echo 'Total:' . $count;
    });
    Route::get('/font_lyric', function () {
        $count = 0;
        $arrFont = array("lyric");
        foreach ($arrFont as $data) {
            $dir = public_path() . "/rs/font/$data";
            $ffs = scandir($dir);
            $type = 3;
            $r_from = '/home/automusic.win/public_html/public';
            $r_to = 'http://automusic.win';
            unset($ffs[array_search('.', $ffs, true)]);
            unset($ffs[array_search('..', $ffs, true)]);
            $temp = '';
            foreach ($ffs as $ff) {
                $temp = "$dir/" . $ff;
                $fonts = scandir($temp);
                unset($fonts[array_search('.', $fonts, true)]);
                unset($fonts[array_search('..', $fonts, true)]);
                if (count($fonts) < 1) {
                    continue;
                }
                $fontObj = new Font();
                $exists = 0;
                foreach ($fonts as $font) {
                    $linkFont = "$temp/$font";
                    $linkFont = str_replace($r_from, $r_to, $linkFont);
//                    Log::info($linkFont);
                    $check = Font::where('link', $linkFont)->first();
                    if (!$check) {
                        if (strpos(strtolower($font), "regular") == false) {
                            $fontObj->link_bold = $linkFont;
                        } else {
                            $fontObj->link = $linkFont;
                        }
                    } else {
                        $exists = 1;
                    }
                }
                if ($exists == 0) {
                    $fontObj->type = $type;
                    $fontObj->name = strtolower($ff);
                    $fontObj->save();
                    $count++;
                    Log::info(json_encode($fontObj));
                } else {
                    Log::info($ff . ' exists');
                }
            }
        }
        echo 'Total:' . $count;
    });
    Route::get('/bot', function () {
        return view('components.bot');
    });
    Route::get('/hx', function () {
        return view('components.pusher');
    });
    Route::get('/test', function (Request $request) {
        return App\Common\Network\RequestHelper::getDriveFiles("1InjMyulCsqRTlZ6cAKB4aoz7fWs0sEWF");
//        $img = new App\Http\Controllers\ImageUploadController();
//        return $img->createRandomImage("truongpv đsds ndsss");
//        $bom = new App\Http\Controllers\BomController();
//        $bom->reDonwnloadBomDeezer();
//        return 1;
//        //thêm user lên con 
//        $users = User::where("status",1)->where("description","=","admin")->where("role","like","%26%")->get(["user_name","name","password_plaintext"]);
//        foreach($users as $user){
//            Log::info(json_encode($user));
//            $curl = curl_init();
//            curl_setopt_array($curl, array(
//              CURLOPT_URL => 'http://152.53.37.41:8058/users',
//              CURLOPT_RETURNTRANSFER => true,
//              CURLOPT_ENCODING => '',
//              CURLOPT_MAXREDIRS => 10,
//              CURLOPT_TIMEOUT => 0,
//              CURLOPT_FOLLOWLOCATION => true,
//              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//              CURLOPT_CUSTOMREQUEST => 'POST',
//              CURLOPT_POSTFIELDS =>'[
//                {
//                "first_name": "'.$user->name.'",
//                "email": "'.$user->user_name.'@moonshots.vn",
//                "password": "'.$user->password_plaintext.'",
//                "role": "fbf37620-2504-40b0-a080-11442b03382d"
//              }
//            ]',
//              CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'Authorization: Bearer liSBfojF58Hu_B0KQAjgFhJiaIxAX0Od'
//              ),
//            ));
//
//            $response = curl_exec($curl);
//            Log::info($response);
//            curl_close($curl);
//        }
//        
//        $filePath = "/home/automusic.win/public_html/public/noclaim_save/music/download/3767-converted.mp3";
//        Storage::disk('s3')->put($filePath, file_get_contents("/home/automusic.win/public_html/public/noclaim_save/music/download/3767-converted.mp3"), 'public');
//        $url = Storage::disk('s3')->url($filePath);
//        Log::info($url);
//        return $url;
//        $datas = \App\Http\Models\CampaignStatistics::where("type", 5)->get();
//        foreach ($datas as $data) {
//            $info = [];
//            if ($data->channel_name != null) {
//                if ($data->submission_info != null) {
//                    $subxx = json_decode($data->submission_info);
//                    if (count($subxx) == 1) {
//                        $accountInfo = App\Http\Models\AccountInfo::where("chanel_name", $data->channel_name)->first();
//                        $user = App\Common\Utils::userCode2userName($accountInfo->user_name);
//                        $sub = (object) [
//                                    "user" => $user,
//                                    "channel_id" => $accountInfo->chanel_id,
//                                    "channel_name" => $accountInfo->chanel_name,
//                                    "money" => $data->money,
//                        ];
//                        $info[] = $sub;
//                        $data->submission_info = json_encode($info);
//                        $data->save();
//                    }else{
//                        Log::info("$data->id");
//                    }
//                }
//            }
//        }
//        return 1;
//        event(new App\Events\ChatEvent(\App\User::where("user_name",'sangmusic')->first(),['truongpv'],'1265 The Northern Belle - Treat Yourself Better', "video goes public tonight may 17 9pm"));
        // <editor-fold defaultstate="collapsed" desc="fix commit video claim">
//$channels = ['UC4xT7Hl8HbwI1u9SkHPuiRw'];
//        $uploads = Upload::where("status", 5)->where("type", "studio_moon")->whereIn("channel_id",$channels)
//                        ->where("create_time", ">=", "2023/11/28 00:00:00")
//                        ->where("log", "<>", "")->orderBy("id","desc")->get();
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
        // <editor-fold defaultstate="collapsed" desc="tiktok playlist">
//        $datas =  TiktokHelper::getPlaylistData("6585417670143773442");
//       $musicList =  json_decode($datas)->music_list;
//       foreach($musicList as $music){
//           if(!empty($music->artists[0]->nick_name)){
//               $artist = $music->artists[0]->nick_name;
//           }
//           if(!empty($music->cover_large->url_list[0])){
//               $cover = $music->cover_large->url_list[0];
//           }
//           if(!empty($music->play_url->uri)){
//               $directUrl = $music->play_url->uri;
//           }
//           if(!empty($music->title)){
//               $songName = $music->title;
//           }
//           return "$artist $songName $directUrl $cover";
//           
//       }
// </editor-fold>
//                $topic = "TOPIC";
//
//                    if (Cache::has($topic)) {
//                        $campaigns = Cache::get($topic);
//                    }else{
//                        $campaigns = CampaignStatistics::where("id", 15)->get(["id", "status"]);
//                        Cache::put($topic, $campaigns, 1);
//                    }
//                    return $campaigns;
//        $numberThreadInit = 30;
//        $threadId = $request->t;
//        $datas = App\Http\Models\AccountInfo::where(DB::raw("id % $numberThreadInit"), $threadId)->where("user_name", 'mailold_1691682704')->get();
//        $countS = 0;
//        $countF = 0;
//        $i=0;
//        $total = count($datas);
//        foreach ($datas as $data) {
//            $i++;
//            $input = array("gmail" => $data->note);
//            $res = App\Common\Network\RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
//
//            if ($res != null) {
//                $countS++;
//                error_log("thread-$threadId $i/$total $data->note exists");
//            } else {
//                $countF++;
//                $data->del_status = 1;
//                $data->save();
//                error_log("thread-$threadId $i/$total $data->note not found");
//            }
//        }
//        return "s=$countS,f=$countF";
//        $input = (object) [
//                    "title" => "songtest",
//                    "artist" => "artisttest",
//                    "deezer_artist_id" => 0,
//                    "lyric" => "",
//                    "url_128" => "https://automusic.win/noclaim_save/music/download/1013-converted.mp3",
//                    "lyric_sync" => "lyric",
//                    "track_id" => null,
//                    "lyric_langs" => [],
//                    "user_name" => "test",
//                    "notes" => null,
//        ];
//        $result = \App\Common\Network\RequestHelper::callAPI2("POST", "https://cdn.soundhex.com/api/v1/timestamp/", $input);
//        Log::info(json_encode($result));
//        return $result;
//        $data = App\Http\Models\AccountInfo::find(312232);
//       return \App\Common\Utils::slugify($data->chanel_name);
//        $views = 150;
//             VideoDaily::where("video_id", "bWB3mEWCuPo")->update(["views" => $views,"wake_views"=> DB::raw("$views - wake_views")]);
//        return App\Http\Models\MusicHexa::where("type",2)->where("status",1)->where("genre","LOFI")->inRandomOrder()->take(5)->get(["id","genre as topic","youtube_link","local_link","title","artist"]);
//        return App\Common\Youtube\WakeupHelper::checkEfficiencyWakeup("PLgnqf7-aXcIgfID543m8pkdu_5zfnWR24");
        // <editor-fold defaultstate="collapsed" desc="Scan channel dung api">
//        $channels = \App\Http\Models\AccountInfo::where("api_status", 2)->get();
//        $channels = \App\Http\Models\AccountInfo::where("id", 288546)->get();
//        foreach ($channels as $channel) {
////
//            \App\Common\Youtube\YoutubeHelper::updateMetaOfChannelNew($channel);
//        }
// </editor-fold>
//        return \App\Common\Youtube\YoutubeHelper::getPlaylistHtml("PLcgiEI-zn4bonNGpS2s7V6mzmq-mJhz5p",1);
//        return \App\Common\Youtube\YoutubeHelper::getVideoInfoHtmlDesktop("1dOuxdtP50o");
        // <editor-fold defaultstate="collapsed" desc="tao list wakup tu list video tren kenh">
//        $lstVideo = [];
//        $videos = App\Http\Models\VideoDaily::where("channel_id", "UC4E2UGq8Rgr4a-Ey-y50T_Q")->where("status", 1)->get();
//        foreach ($videos as $video) {
//            $isClaim = false;
//            if (App\Common\Utils::containString($video->claims, "HEXACORP LTD")) {
//                $isClaim = true;
//            }
//            $temp = (object) [
//                        "video_id" => $video->video_id,
//                        "is_claim" => $isClaim,
//                        "view" => $video->views,
//                        "view_incr" => 0,
//                        "view_incr_perc" => 0,
//                        "child_connect" => null,
//                        "child_connect_times" => 0,
//            ];
//            $lstVideo[] = $temp;
//        }
//        \App\Common\Utils::write("save/listwakeup_req.txt", json_encode($lstVideo));
//        $data = \App\Common\Youtube\WakeupHelper::createList($lstVideo, 2, 20);
//        \App\Common\Utils::write("save/listwakeup_res.txt", json_encode($data));
// </editor-fold>
//        return \App\Common\Youtube\YoutubeHelper::processLink("$request->link");
//        return YoutubeHelper::getVideoInfoHtmlDesktop("UqEqXkvHVO0", 1);
//        return \App\Common\Youtube\YoutubeHelper::getInfoComment("saGYMhApaH8", 1);
//        return \App\Common\Youtube\YoutubeHelper::getListShortVideo("https://www.youtube.com/@SoiTuyetAirsoft/shorts", 1);
//        return \App\Common\Youtube\YoutubeHelper::getVideoInfoV2("q3hLunpUo4A");
//        return \App\Common\Youtube\YoutubeHelper::getChannelInfoV2("UCBepjMYiqtq_u4ZFOEcEBzA", 1);
//        return \App\Common\Youtube\YoutubeHelper::getChannelInfoV3("UC6ie_mNW-VxSN_egDPVP6Ow", 1);
//        return \App\Common\Youtube\YoutubeHelper::getPlaylistInfoV3("PLtPLi5VJstGiXYJxuLFpiVAExvfp5Z3zg");
//        return \App\Common\Youtube\YoutubeHelper::getListVideoFromPlaylistV2("UUXbZ-b7Zz55wR36x99iYCUg",1,0);
//        return \App\Common\Youtube\YoutubeHelper::getListPLaylistWakeupHappy("UCqc1z_jcp5bXGV8MK2BQCgw");
//        return \App\Common\Youtube\YoutubeHelper::searchVideoByApi("play date");
//        return \App\Common\Youtube\YoutubeHelper::getPlaylist("UU3Oso6DaiHJYNI4yXRvW_Xw", 10);
//        return \App\Common\Youtube\YoutubeHelper::getPlaylistAll("UUdeKPEiW0risJGzMkArsdzw");
//        return \App\Common\Youtube\YoutubeHelper::getPlaylistRD("09R8_2nJtjg");
//        return \App\Http\Controllers\ApiController::exportGenre();
    });
    Route::group(['middleware' => 'adminLogin'], function() {
        Route::get('/studio/drive', 'StudioController@index')->name('studioDriveIndex');
        Route::get('/studio/drive/update', 'StudioController@update')->name('studioDriveUpdate');
        Route::get('/loadChannels', 'StudioController@loadChannels')->name('loadChannels');

        //2024/03/17 tính tiền claim version 2
        Route::get('/listClaimMontly', 'ClaimController@listClaimMontly')->name('listClaimMontly');
        Route::get('/listClaimMontlyDetail', 'ClaimController@listClaimMontlyDetail')->name('listClaimMontlyDetail');
        Route::get('/claimMontlyDistributor', 'ClaimController@claimMontlyDistributor')->name('claimMontlyDistributor');

        //2024/03/04 import social asset
        Route::get('/listSongNotMapping', 'ClaimController@listSongNotMapping')->name('listSongNotMapping');
        Route::post('/saveSocialMappingSong', 'ClaimController@saveSocialMappingSong')->name('saveSocialMappingSong');


        //calendar
        Route::get('/calendar/get', 'CalendarController@getTasks')->name('getTasks');
        Route::get('/calendar/getRepeatTasks', 'CalendarController@getRepeatTasks')->name('getRepeatTasks');
        Route::post('/calendar/createTask', 'CalendarController@createOrUpdateTasks')->name('createOrUpdateTasks');
        Route::get('/calendar/findTask', 'CalendarController@findTask')->name('findTask');
        Route::post('/calendar/deleteTask', 'CalendarController@deleteTask')->name('deleteTask');
        Route::post('/calendar/createJob', 'CalendarController@createOrUpdateJobs')->name('createOrUpdateJobs');
        Route::post('/calendar/deleteJob', 'CalendarController@deleteJob')->name('deleteJob');
        Route::get('/calendar/updateJobDetail', 'CalendarController@updateJobDetail')->name('updateJobDetail');
        Route::post('/calendar/cloneJob', 'CalendarController@cloneJob')->name('cloneJob');
        Route::get('/calendar/getJobs', 'CalendarController@getJobs')->name('getJobs');
        Route::get('/calendar/listJobs', 'CalendarController@listJobs')->name('listJobs');
        Route::get('/calendar/findJob', 'CalendarController@findJob')->name('findJob');
        Route::get('/calendar/getHistory', 'CalendarController@getHistory')->name('getHistory');
        Route::get('/calendar/getComments', 'CalendarController@getComments')->name('getComments');
        Route::post('/calendar/createComment', 'CalendarController@createComment')->name('createComment');
        Route::post('/calendar/deleteComment', 'CalendarController@deleteComment')->name('deleteComment');
        Route::post('/calendar/updateComment', 'CalendarController@updateComment')->name('updateComment');
        Route::get('/calendar/checklist/get', 'CalendarController@getChecklist')->name('getChecklist');
        Route::get('/calendar/job/statistics', 'CalendarController@getJobStatistics')->name('getJobStatistics');

        Route::get('/calendar/notify/update', 'CalendarController@updateCalendarNotify')->name('updateCalendarNotify');
        Route::get('/calendar/getUserMention', 'CalendarController@getUserMention')->name('getUserMention');

        Route::get('/calendar/statistics', 'CalendarController@statistics')->name('statistics');


        //2023/07/13 dash.360promo.net invoice 
//        Route::post('/360promo/addInvoice', 'Promo360Controller@addInvoice')->name('addInvoice');
//        Route::get('/360promo/listInvoice', 'Promo360Controller@listInvoice')->name('listInvoice');
//        Route::get('/360promo/approveInvoice', 'Promo360Controller@approveInvoice')->name('approveInvoice');
        //2023/06/13 
        Route::get('/getComments', 'CampaignController@getComments')->name('getComments');
        Route::post('/importCommnents', 'CampaignController@importCommnents')->name('importCommnents');


        //2023/03/09 bitly
        Route::get('/bitly', 'BitlyController@index')->name('bitly');
        Route::post('/bitly', 'BitlyController@store')->name('bitlyStore');
        Route::put('/bitly', 'BitlyController@update')->name('bitlyUpdate');
        Route::get('/getChartMonth', 'BitlyController@getChartMonth')->name('getChartMonth');
        Route::get('/getChartMonthDetail', 'BitlyController@getChartMonthDetail')->name('getChartMonthDetail');
        Route::get('/getChartUser', 'BitlyController@getChartUser')->name('getChartUser');

        Route::get('/shorts', 'ShortsController@index')->name('shorts');
        Route::post('/shortsStore', 'ShortsController@store')->name('shortsStore');
        Route::get('/shortUpdate', 'ShortsController@shortUpdate')->name('shortUpdate');
        Route::get('/help', 'HelpController@help')->name('help');
        Route::get('/redirect/add/api', 'HelpController@redirectAddApi')->name('redirectAddApi');


        Route::post('/labelgridReleaseImage', 'ClaimController@labelgridReleaseImage')->name('labelgridReleaseImage');
        Route::post('labelgrid-image-upload', ['as' => 'image.label-upload.post', 'uses' => 'ImageUploadController@imageUploadLabelGrid']);
        Route::post('/labelgridAddTrack', 'ClaimController@labelgridAddTrack')->name('labelgridAddTrack');
        Route::post('/labelgridAddRelease', 'ClaimController@labelgridAddRelease')->name('labelgridAddRelease');
        Route::post('/labelgridAddArtist', 'ClaimController@labelgridAddArtist')->name('labelgridAddArtist');
        Route::post('/addClaim', 'ClaimController@addClaim')->name('addClaim');
        Route::get('/claim', 'ClaimController@index')->name('claim');
        Route::get('/boomFinishMakeLyric', 'BomController@boomFinishMakeLyric')->name('boomFinishMakeLyric');
        Route::get('/boomMakeLyric', 'BomController@boomMakeLyric')->name('boomMakeLyric');
        Route::get('/boomSync', 'BomController@boomSync')->name('boomSync');
        Route::get('/boomRemove', 'BomController@boomRemove')->name('boomRemove');
        Route::post('/boomStore', 'BomController@store')->name('store');
        Route::get('/boom', 'BomController@index')->name('bom');
        Route::get('/boom/{id}', 'BomController@find')->name('find');
        Route::get('/noclaim', 'NoclaimController@index')->name('noclaim');
        Route::get('/getOvertonePlaylist', 'BomController@getOvertonePlaylist')->name('getOvertonePlaylist');
        Route::get('/getOverTonePlaylistId', 'BomController@getOverTonePlaylistId')->name('getOverTonePlaylistId');
        Route::post('/noclaimStore', 'BomController@storeLocalSong')->name('storeLocalSong');
        Route::get('/noclaimMakeLyric', 'NoclaimController@noclaimMakeLyric')->name('noclaimMakeLyric');
        Route::get('/noclaimFinishMakeLyric', 'NoclaimController@noclaimFinishMakeLyric')->name('noclaimFinishMakeLyric');
        Route::get('/noclaimRemove', 'NoclaimController@noclaimRemove')->name('noclaimRemove');
        Route::get('/boom/group/add', 'BomController@find')->name('find');
        Route::get('/addNewGroup', 'BomController@addNewGroup')->name('addNewGroup');
        Route::post('/updateGroup', 'BomController@updateGroup')->name('updateGroup');
        Route::get('/addBomToGroup', 'BomController@addBomToGroup')->name('addBomToGroup');
        Route::get('/addPriority', 'BomController@addPriority')->name('addPriority');

        Route::get('/album', 'BomController@indexAlbum')->name('indexAlbum');
        Route::post('/addAlbum', 'BomController@addAlbum')->name('addAlbum');
        Route::get('/albumListArtist', 'BomController@albumListArtist')->name('albumListArtist');
        Route::post('/albumAddArtist', 'BomController@albumAddArtist')->name('albumAddArtist');
        Route::get('/getSongsForRelease', 'BomController@getSongsForRelease')->name('getSongsForRelease');
        Route::get('/getListAlbum', 'BomController@getListAlbum')->name('getListAlbum');
        Route::get('/addSongToAlbum', 'BomController@addSongToAlbum')->name('addSongToAlbum');
        Route::get('/deleteSongFromAlbum', 'BomController@deleteSongFromAlbum')->name('deleteSongFromAlbum');
        Route::get('/getAlbum', 'BomController@getAlbum')->name('getAlbum');
        Route::get('/getSongsByAlbum', 'BomController@getSongsByAlbum')->name('getSongsByAlbum');
        Route::get('/sendAlbumToSalad', 'BomController@sendAlbumToSalad')->name('sendAlbumToSalad');
        Route::get('/updateAlbumReleaseDate', 'BomController@updateAlbumReleaseDate')->name('updateAlbumReleaseDate');
        Route::get('/scanAlbum', 'BomController@scanAlbum')->name('scanAlbum');

        Route::get('/intros', 'IntroController@index')->name('intros');
        Route::post('/introsStore', 'IntroController@store')->name('introsStore');
        Route::get('/introsFind', 'IntroController@find')->name('introsFind');
        Route::get('/branding', 'BrandingController@index')->name('branding');
        Route::get('/brandingFind', 'BrandingController@find')->name('find');
        Route::post('/brandingStore', 'BrandingController@store')->name('store');
        Route::post('/brandingUpdate', 'BrandingController@update')->name('update');
        Route::post('/brandingSaveAboutSection', 'BrandingController@brandingSaveAboutSection')->name('brandingSaveAboutSection');
        Route::get('/exportBoom', 'BomController@exportBoom')->name('exportBoom');
        Route::get('/exportReportGeo', 'CampaignController@exportReportGeo')->name('exportReportGeo');
        Route::get('/exportScanArtist', 'CampaignController@exportScanArtist')->name('exportScanArtist');
        Route::get('/deleteProgressScan', 'CampaignController@deleteProgressScan')->name('deleteProgressScan');
        Route::get('/getProgressScan', 'CampaignController@getProgressScan')->name('getProgressScan');
        Route::post('/makeCommandScanVideo', 'CampaignController@makeCommandScanVideo')->name('makeCommandScanVideo');
        Route::get('/submitGenreTarget', 'CampaignController@submitGenreTarget')->name('submitGenreTarget');

        Route::get('/deleteVideosManual', 'DashboardController@deleteVideosManual')->name('deleteVideosManual');
        Route::get('/deletePlaylistManual', 'DashboardController@deletePlaylistManual')->name('deletePlaylistManual');
        Route::get('/getListPlaylistChannel', 'DashboardController@getListPlaylistChannel')->name('getListPlaylistChannel');
        Route::get('/getNotification', 'NotificationController@getNotification')->name('getNotification');
        Route::get('/finishPromoTask', 'DashboardController@finishPromoTask')->name('finishPromoTask');
        Route::get('/loadSubgenre', 'ApiController@loadSubgenre')->name('loadSubgenre');
        Route::get('/loadChannelInfo', 'ChannelManagementController@loadChannelInfo')->name('loadChannelInfo');
        Route::post('/channelAssign', 'CampaignController@channelAssign')->name('channelAssign');
        Route::get('/getListChannelManualPromos', 'CampaignController@getListChannelManualPromos')->name('getListChannelManualPromos');
        Route::get('/crossPostDelete', 'DashboardController@crossPostDelete')->name('crossPostDelete');
        Route::get('/crossPostReport', 'DashboardController@crossPostReport')->name('crossPostReport');
        Route::get('/update/deezerArtistsId', 'LyricConfigController@updateDeezerArtistsId')->name('updateDeezerArtistsId');
        Route::get('/lyricAssign', 'CampaignController@lyricAssign')->name('lyricAssign');
        Route::get('/lyricGetListUser', 'CampaignController@lyricGetListUser')->name('lyricGetListUser');
        Route::post('promo-upload', ['as' => 'promo.upload.post', 'uses' => 'ImageUploadController@promoUploadPost']);
        Route::get('/strikeStatus', 'DashboardController@strikeStatus')->name('strikeStatus');
        Route::post('/saveChannelInfo', 'ChannelManagementController@saveChannelInfo')->name('saveChannelInfo');
        Route::get('/autochannel', 'DashboardController@autochannel')->name('autochannel');
        Route::get('/listChannelAuto', 'ChannelManagementController@listChannelAuto')->name('listChannelAuto');
        Route::get('/randomAboutSection', 'ChannelManagementController@randomAboutSection')->name('randomAboutSection');
        Route::post('/saveBrandChannel', 'ChannelManagementController@saveBrandChannel')->name('saveBrandChannel');
        Route::get('/promoWake', 'DashboardController@promoWake')->name('promoWake');
        Route::get('/ajaxSyncAthena/{id}', 'ChannelManagementController@ajaxSyncAthena')->name('ajaxSyncAthena');
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

        Route::get('/profile', 'ProfileController@index')->name('profile');
        Route::get('/musicmanagement', 'MusicManagementController@index')->name('musicmanagement');


        Route::get('/ajaxGetLog', 'MusicManagementController@ajaxGetLog')->name('ajaxGetLog');
        Route::get('/reportBlock', 'LyricConfigController@reportBlock')->name('reportBlock');
        Route::get('/markSuggest', 'LyricConfigController@markSuggest')->name('markSuggest');
        Route::get('/cartSong', 'LyricConfigController@cartSong')->name('cartSong');
        Route::get('/updateNote', 'LyricConfigController@updateNote')->name('updateNote');
        Route::get('/channelmanagement', 'ChannelManagementController@index')->name('channelmanagement');
        Route::get('/channelmanagement/v2', 'ChannelManagementController@index')->name('channelmanagement');
        Route::get('/channel/epid', 'ChannelManagementController@epidIndex')->name('epidIndex');
        Route::get('/channel/epid/status', 'ChannelManagementController@epidStatus')->name('epidStatus');
        Route::get('/ajaxRecheckChannel/{id}', 'ChannelManagementController@ajaxRecheckChannel')->name('ajaxRecheckChannel');
        Route::get('/goLogin', 'ChannelManagementController@goLogin')->name('goLogin');
        Route::get('/getCodeLogin', 'ChannelManagementController@getCodeLogin')->name('getCodeLogin');
        Route::get('/getCodeRecovery', 'ChannelManagementController@getCodeRecovery')->name('getCodeRecovery');
        Route::get('/getDataChart', 'ChannelManagementController@getDataChart')->name('getDataChart');
        Route::get('/getDataCharts', 'ChannelManagementController@getDataCharts')->name('getDataCharts');

        Route::get('/genEmailInfo', 'ChannelManagementController@genEmailInfo')->name('genEmailInfo');
        Route::get('/createEmail', 'ChannelManagementController@createEmail')->name('createEmail');
        //add channel vào email đã có sẵn
        Route::get('/addChannel', 'ChannelManagementController@addChannel')->name('addChannel');
        Route::get('/getCardEndscreen', 'ChannelManagementController@getCardEndscreen')->name('getCardEndscreen');
        Route::post('/addCardEndscreen', 'ChannelManagementController@addCardEndscreen')->name('addCardEndscreen');
        Route::get('/insertOtpKey', 'ChannelManagementController@insertOtpKey')->name('insertOtpKey');
        Route::get('/getTags', 'ChannelManagementController@getTags')->name('getTags');
        Route::get('/channelTag', 'ChannelManagementController@channelTag')->name('channelTag');
        Route::get('/deleteTag', 'ChannelManagementController@deleteTag')->name('deleteTag');
        Route::get('/updateChannelId', 'ChannelManagementController@updateChannelId')->name('updateChannelId');
        Route::get('/syncAvatar', 'ChannelManagementController@syncAvatar')->name('syncAvatar');
        Route::post('/addmusic', 'MusicConfigController@store')->name('addmusic');
        Route::post('/addlyric', 'LyricConfigController@store')->name('addlyric');
        Route::get('/getdataimage', 'MusicConfigController@ajax')->name('getdataimage');
        Route::get('/ajaxPreviewImage', 'MusicConfigController@ajaxPreviewImage')->name('ajaxPreviewImage');
        Route::get('/ajaxPreviewLyric', 'LyricConfigController@ajaxPreviewLyric')->name('ajaxPreviewLyric');
        Route::get('/ajaxGetArtistsFromTopic', 'MusicConfigController@ajaxGetArtistsFromTopic')->name('ajaxGetArtistsFromTopic');
        Route::get('/ajaxGetSongsFromArtists', 'MusicConfigController@ajaxGetSongsFromArtists')->name('ajaxGetSongsFromArtists');
        Route::get('/ajaxGetArtistsFromName', 'MusicConfigController@ajaxGetArtistsFromName')->name('ajaxGetArtistsFromName');
        Route::get('/ajaxGetGroupChannel', 'GroupChannelController@ajaxGetGroupChannel')->name('ajaxGetGroupChannel');
        Route::get('/ajaxAddGroupChannel', 'GroupChannelController@ajaxAddGroupChannel')->name('ajaxAddGroupChannel');
        Route::get('/ajaxDelGroupChannel', 'GroupChannelController@ajaxDelGroupChannel')->name('ajaxDelGroupChannel');
        Route::get('/ajaxListGroupChannel', 'GroupChannelController@ajaxListGroupChannel')->name('ajaxListGroupChannel');
        Route::get('/ajaxUpdateGroupChannel', 'GroupChannelController@update')->name('update');
        Route::post('/ajaxChannel', 'ChannelManagementController@ajaxChannel')->name('ajaxChannel');
        Route::get('/vipRender', 'ChannelManagementController@vipRender')->name('vipRender');
        Route::get('/ajaxUpdateMusicConfig', 'MusicConfigController@ajaxUpdateMusicConfig')->name('ajaxUpdateMusicConfig');
        Route::post('image-upload', ['as' => 'image.upload.post', 'uses' => 'ImageUploadController@imageUploadPost']);
        Route::get('/image_from_drive', 'ImageUploadController@downloadDrive')->name('downloadDrive');
        Route::get('/showlyricdownload', 'LyricConfigController@show')->name('showlyricdownload');
        Route::get('/lyricdownload', 'LyricConfigController@download')->name('lyricdownload');
        Route::get('/lyricdownloadbydeezerid', 'LyricConfigController@downloadbydeezer')->name('lyricdownloadbydeezerid');
        Route::get('/videoclaim', 'Z1VideoClaimController@index')->name('videoclaim');
        Route::post('/addvideoclaim', 'Z1VideoClaimController@ajax')->name('addvideoclaim');
        Route::get('/scanvideoclaim', 'Z1VideoClaimController@scan')->name('scanvideoclaim');
        Route::get('/getlistvideo', 'DashboardController@getlistvideo')->name('getlistvideo');
        Route::get('/getvideochart', 'DashboardController@getvideochart')->name('getvideochart');
        Route::get('/getChartTotalDailyViews', 'DashboardController@getChartTotalDailyViews')->name('getChartTotalDailyViews');
        Route::get('/getChartTotalChannelsCount', 'DashboardController@getChartTotalChannelsCount')->name('getChartTotalChannelsCount');
        Route::get('/getvideochartdaily', 'DashboardController@getvideochartdaily')->name('getvideochartdaily');
        Route::get('/getTasksByChannelId', 'DashboardController@getTasksByChannelId')->name('getTasksByChannelId');

        Route::get('/updatePositionWakeCampaign', 'CampaignController@updatePositionWakeCampaign')->name('updatePositionWakeCampaign');
        Route::get('/campaign', 'CampaignController@index')->name('campaign');
        Route::get('/campaign2', 'CampaignController@index2')->name('campaign2');
        Route::get('/update/adsviews', 'CampaignController@adsViews')->name('adsViews');
        Route::get('/boomvip', 'CampaignController@boomvip')->name('boomvip');
        Route::get('/tableau', 'TableauController@index')->name('index');
        Route::get('/tableauExportPdf', 'TableauController@exportPdf')->name('exportPdf');
        Route::get('/tableauExportCsv', 'TableauController@exportCsv')->name('exportCsv');
        Route::get('/runSync', 'TableauController@runSync')->name('runSync');
        Route::post('/addcampaign', 'CampaignController@addcampaign')->name('addcampaign');
        Route::get('/detailcampaign', 'CampaignController@detailcampaign')->name('detailcampaign');
        Route::get('/downloadVideoInfo', 'CampaignController@downloadVideoInfo')->name('downloadVideoInfo');
        Route::get('/getcampaign', 'CampaignController@getcampaign')->name('getcampaign');
        Route::get('/getCampaignChart', 'CampaignController@getCampaignChart')->name('getCampaignChart');
        Route::get('/getcampaignstatistics', 'CampaignController@getcampaignstatistics')->name('getcampaignstatistics');
        Route::get('/campaignstatus', 'CampaignController@campaignstatus')->name('campaignstatus');
        Route::get('/downloadListvideo', 'CampaignController@downloadListvideo')->name('downloadListvideo');
        Route::get('/downloadReportCampaign', 'CampaignController@downloadReportCampaign')->name('downloadReportCampaign');
        Route::get('/downloadReportClientCampaign', 'CampaignController@downloadReportClientCampaign')->name('downloadReportClientCampaign');
        Route::get('/downloadListvideosRevshareClient', 'CampaignController@downloadListvideosRevshareClient')->name('downloadListvideosRevshareClient');
        Route::get('/getCheckList', 'CampaignController@getCheckList')->name('getCheckList');
        Route::get('/addCheckList', 'CampaignController@addCheckList')->name('addCheckList');
        Route::post('/campaign2/submissionPercent', 'CampaignController@submissionPercent')->name('submissionPercent');

        Route::get('/cover', 'CoverController@index')->name('cover');
        Route::post('/addcover', 'CoverController@addcampaign')->name('addcampaign');

        Route::get('/tracker', 'TrackerController@index')->name('tracker');
        Route::post('/addtracker', 'TrackerController@addtracker')->name('addtracker');

        Route::post('/updateTask', 'DashboardController@updateTask')->name('updateTask');

        Route::post('/addWakeup', 'DashboardController@addWakeup')->name('addWakeup');
        Route::post('/confirmPromo', 'DashboardController@confirmPromo')->name('confirmPromo');
        Route::post('/confirmPromoFilter', 'DashboardController@confirmPromoFilter')->name('confirmPromoFilter');
        Route::post('/confirmChannel', 'DashboardController@confirmChannel')->name('confirmChannel');

        Route::get('/refreshAutoWake', 'DashboardController@refreshAutoWake')->name('refreshAutoWake');
        Route::get('/reSynVideo', 'DashboardController@reSynVideo')->name('reSynVideo');
        Route::get('/scanViewPromoCampaign', 'CampaignController@scanViewPromoCampaign')->name('scanViewPromoCampaign');
        Route::get('/reportCampaignUser', 'CampaignController@reportCampaignUser')->name('reportCampaignUser');
        Route::get('/reportCampaignRevenue', 'CampaignController@reportCampaignRevenue')->name('reportCampaignRevenue');
        Route::get('/getReportPeriodRevDetail', 'CampaignController@getReportPeriodRevDetail')->name('getReportPeriodRevDetail');
        Route::get('/getReportUserRevDetail', 'CampaignController@getReportUserRevDetail')->name('getReportUserRevDetail');
        Route::get('/reportClaimRevenue', 'ClaimController@reportClaimRevenue')->name('reportClaimRevenue');
        Route::get('/getClaimReportRevDetail', 'ClaimController@getClaimReportRevDetail')->name('getClaimReportRevDetail');
        Route::get('/changeStatusPay', 'ClaimController@changeStatusPay')->name('changeStatusPay');
        Route::get('/getClaimReportUserRevDetail', 'ClaimController@getClaimReportUserRevDetail')->name('getClaimReportUserRevDetail');
        Route::get('/videoinfo', 'CampaignController@videoInfo')->name('videoInfo');
        Route::post('/checkImportReport', 'ClaimController@checkImportReport')->name('checkImportReport');

        //notification

        Route::get('/notiUpdateRead', 'NotificationController@notiUpdateRead')->name('notiUpdateRead');
        Route::get('/readAllNotify', 'NotificationController@readAllNotify')->name('readAllNotify');
        Route::get('/notiUpdateStatus', 'NotificationController@notiUpdateStatus')->name('notiUpdateStatus');

        //calendar
        Route::get('/calendar', 'CalendarController@index')->name('calendarIndex');

        //moonshot report

        Route::get('/money', 'MoneyController@index')->name('money');

        Route::get('/getReportAll', 'MoneyController@getReportAll')->name('getReportAll');
        Route::get('/getReportSummary', 'MoneyController@getReportSummary')->name('getReportSummary');
        Route::get('/epidMonthlyDetail', 'MoneyController@epidMonthlyDetail')->name('epidMonthlyDetail');
        Route::get('/epidUserDetail', 'MoneyController@epidUserDetail')->name('epidUserDetail');
        Route::get('/epidSummary', 'MoneyController@epidSummary')->name('epidSummary');

//        //2023/05/29 api getMonthlyViewGroupByUserOrDistributor
//        Route::get('/getMonthlyClaimViews', 'ClaimController@getMonthlyClaimViewsGroupByUserOrDistributor')->name('getMonthlyClaimViewsGroupByUserOrDistributor');
        Route::group(['middleware' => 'musicAdminCheck'], function() {
            //2023/05/19 360promo
            Route::post('/360promo/addEmail', 'Promo360Controller@addEmail')->name('addEmail');
            Route::get('/360promo', 'Promo360Controller@index2')->name('promoIndex');
            Route::get('/360promoUpdate', 'Promo360Controller@updateCustomer')->name('updateCustomer');
            //2023/06/26 dash.360promo.net
            Route::get('/360promo/getInfoUser', 'Promo360Controller@getInfoUser')->name('getInfoUser');
            Route::get('/360promo/resetPass', 'Promo360Controller@resetPass')->name('resetPass');
            //2024/08/28
            Route::get('/360promo2', 'Promo360Controller@index2')->name('promoIndex2');
            Route::get('/360promo2/loadCustomer', 'Promo360Controller@loadCustomer')->name('loadCustomer');
            Route::post('/360promo2/addBudget', 'Promo360Controller@addBudget')->name('addBudget');
            Route::post('/360promo2/submitInvoice', 'Promo360Controller@submitInvoice')->name('submitInvoice');
            Route::get('/360promo2/loadInvoice', 'Promo360Controller@loadInvoice')->name('loadInvoice');
            Route::get('/360promo2/confirmInvoice', 'Promo360Controller@confirmInvoice')->name('confirmInvoice');
            Route::post('/360promo2/activeCampaign', 'Promo360Controller@activeCampaign')->name('activeCampaign');
        });
        Route::group(['middleware' => 'supperAdminCheck'], function() {
            Route::get('/mooncoin', 'MoneyController@mooncoinIndex')->name('mooncoinIndex');
            Route::get('/api/mooncoins', 'MoneyController@getMooncoins');
            Route::post('/addMooncoin', 'MoneyController@addMooncoin');
            Route::post('/addDescMooncoin', 'MoneyController@addDescMooncoin');
            Route::get('/getDescMooncoin', 'MoneyController@getDescMooncoin');
            Route::get('/financial', 'MoneyController@index')->name('financial');
            Route::get('/getMsReport', 'MoneyController@getMsReport')->name('getMsReport');
            Route::get('/findMsReport', 'MoneyController@findMsReport')->name('findMsReport');
            Route::get('/updateMoneyChart', 'MoneyController@updateMoneyChart')->name('updateMoneyChart');
            Route::post('/saveMsReport', 'MoneyController@saveMsReport')->name('saveMsReport');
            Route::post('/deleteMsReport', 'MoneyController@deleteMsReport')->name('deleteMsReport');
            Route::get('/channelConfig', 'ChannelManagementController@channelConfig')->name('channelConfig');
            Route::get('/radioConfig', 'RadioController@index')->name('radioConfig');
            Route::get('/musicsource', 'MusicSourceController@index')->name('musicsource');
            Route::post('/musicsourcestore', 'MusicSourceController@store')->name('musicsourcestore');
        });
        Route::group(['middleware' => 'task'], function() {
            Route::get('/chartmetricSpotifyPlaylist', 'LyricConfigController@chartmetricSpotifyPlaylist')->name('chartmetricSpotifyPlaylist');
            Route::get('/musicconfig', 'MusicConfigController@index')->name('musicconfig');
            Route::get('/lyricconfig', 'LyricConfigController@index')->name('lyricconfig');
            Route::get('/musicspotify', 'LyricConfigController@musicspotify')->name('musicspotify');
            Route::get('/spotifycharts', 'LyricConfigController@spotifycharts')->name('spotifycharts');
            Route::get('/musictiktok', 'LyricConfigController@musictiktok')->name('musictiktok');
            Route::get('/tiktokcharts', 'LyricConfigController@tiktokcharts')->name('tiktokcharts');
            Route::get('/exportTiktokCharts', 'LyricConfigController@exportTiktokCharts')->name('exportTiktokCharts');
            Route::get('/getHubsByChannelId', 'LyricConfigController@getHubsByChannelId')->name('getHubsByChannelId');
            Route::get('/autoMakeLyricHub', 'LyricConfigController@autoMakeLyricHub')->name('autoMakeLyricHub');
        });
    });

    //2024/06/12 đưa ra ngoài để ko lưu last_activity
    Route::get('/getNotify', 'NotificationController@getNotify')->name('getNotify');
    Route::get('/calendar/notify/list', 'CalendarController@listCalendarNotify')->name('listCalendarNotify');

    //2023/05/29 api getMonthlyViewGroupByUserOrDistributor
    Route::get('/getMonthlyClaimViews', 'ClaimController@getMonthlyClaimViewsGroupByUserOrDistributor')->name('getMonthlyClaimViewsGroupByUserOrDistributor');
    //đồng bộ music friday để tạo playlist
    Route::get('/syncmusicfriday', 'ApiController@syncmusicfriday')->name('syncmusicfriday');
    //kiểm tra số lượng upload của các kênh music kpi
    Route::get('/scanchannelmusic', 'ApiController@scanchannelmusic')->name('scanchannelmusic');
    Route::get('/roolBackScanChannelMusic', 'ApiController@roolBackScanChannelMusic')->name('roolBackScanChannelMusic');
    Route::get('/roolBackScanViewsChannelMusic', 'ApiController@roolBackScanViewsChannelMusic')->name('roolBackScanViewsChannelMusic');
    Route::get('/scanLabel', 'ApiController@scanLabel')->name('scanLabel');
    Route::get('/msg', 'ApiController@msg')->name('msg');
    Route::get('/msghx', 'ApiController@msghx')->name('msghx');
    //scan channel sub theo gio
    Route::get('/scanSubHour', 'ApiController@scanSubHour')->name('scanSubHour');

    //kiem tra view kenh z1 theo user theo date
    Route::get('/checkViewsByDate', 'Z1VideoClaimController@checkViewsByDate')->name('checkViewsByDate');

    Route::get('/scanCampaign', 'CampaignController@scanCampaign2')->name('scanCampaign2');
    Route::get('/caculateCampaign', 'CampaignController@caculateCampaign')->name('caculateCampaign');
//    Route::post('/zip', 'ApiController@getZipfile')->name('zip');
    //tổng hợp dữ liệu cho card và endscreen
    Route::get('/statisticsCardEnscreen', 'CampaignController@statisticsCardEnscreen')->name('statisticsCardEnscreen');

    Route::get('/scanchannelmusiclicences', 'ApiController@scanchannelmusiclicences')->name('scanchannelmusiclicences');

    Route::get('/reScanLabelForNewUpload', 'ApiController@reScanLabelForNewUpload')->name('reScanLabelForNewUpload');

    Route::get('/roolBackScanVideoClaim', 'Z1VideoClaimController@roolBackScanVideoClaim')->name('roolBackScanVideoClaim');

    Route::get("channel/scan/{thread_id}", function($thread_id) {
        $api = new ApiController();
        $api->scanChannel($thread_id);
    });

    //import spotify playlist vao db để crawl
    Route::get('/import/spotify/playlist', 'ApiController@importSpotifyPlaylist')->name('importSpotifyPlaylist');

    //quan ly task
    Route::get('/checkTask', 'DashboardController@checkTask')->name('checkTask');
    Route::get('/notify', 'DashboardController@notify')->name('notify');

    //run wakeup

    Route::get('/checkRunWakeup', 'DashboardController@checkRunWakeup')->name('checkRunWakeup');
    Route::get('/updateAutoWakeup', 'DashboardController@updateAutoWakeup')->name('updateAutoWakeup');
    Route::get('/checkVideoWakeup', 'DashboardController@checkVideoWakeup')->name('checkVideoWakeup');
    Route::get('/makeCommandCheckWakeup', 'DashboardController@makeCommandCheckWakeup')->name('makeCommandCheckWakeup');
    Route::get('/checkVideoWakeup2', 'DashboardController@checkVideoWakeup2')->name('checkVideoWakeup2');
    Route::get('/promosWakeupVideos', 'DashboardController@promosWakeupVideos')->name('promosWakeupVideos');

    //check info playlist
    Route::get('/playlist/info', 'ApiController@checkInfoPlaylist')->name('checkInfoPlaylist');

    //thêm api vào hệ thống
    Route::get('/api/import', function() {
        return view('layouts.api');
    });

    //scan ngày upload video cho kênh lyric
    Route::get('/channel/video/date/publish', 'ApiController@channelVideoDatePublish')->name('channelVideoDatePublish');

    //canh bao kenh ko upload video
    Route::get('/channelUploadAlert', 'ApiController@channelUploadAlert')->name('channelUploadAlert');

    //api get video upload theo day để upload lên athena
    Route::get('/channel/video/date/publish/get', 'ApiController@channelVideoDatePublishGet')->name('channelVideoDatePublishGet');

    //api promo campaign athena
    Route::get('/promo/campaign', 'ApiController@promoCampaign')->name('promoCampaign');
    Route::get('/syncPromoVideo', 'ApiController@syncPromoVideo')->name('syncPromoVideo');

    //api channel information
    Route::get('/channel/information', 'ApiController@channelInformation')->name('channelInformation');

    //api quan ly claim
    Route::get('/api/claim/topic', 'ApiController@getClaimTopic')->name('getClaimTopic');
    Route::get('/api/claim/music', 'ApiController@getMusicClaimByTopic')->name('getMusicClaimByTopic');
    Route::get('/api/claim/import', 'ApiController@importMusicClaim')->name('importMusicClaim');

    //sys channel athena
    Route::get('/api/sync/channel/athena', 'ApiController@syncChannelAthena')->name('syncChannelAthena');
    Route::get('/api/sync/promo/athena', 'ApiController@syncPromosAthena')->name('syncPromosAthena');
    Route::get('/api/sync/tableau/athena', 'TableauController@syncTableau')->name('syncTableau');
    Route::get('/api/sync/tableau/athena2', 'TableauController@syncTableau2')->name('syncTableau2');
    Route::get('/api/caculate/channel/athena', 'ApiController@caculateDataAthena')->name('caculateDataAthena');

    //
    Route::get('/api/bas/upload/music', 'ApiController@autoUploadStudio')->name('autoUploadStudio');
    Route::get('/api/check/bas/upload/music', 'ApiController@checkAutoUploadStudio')->name('checkAutoUploadStudio');
    Route::get('/api/orfium/retry/upload/music', 'ApiController@retryApiAutoUploadStudio')->name('retryApiAutoUploadStudio');

    //thêm video promos cho channel để check vị trí wakeup, đồng thời thêm vào campaign
    Route::get('/api/add/promos/by/list', 'ApiController@addPromosByList')->name('addPromosByList');

    //thêm lệnh brand lên bas


    Route::get('/fixCountVideo', 'ApiController@fixCountVideo')->name('fixCountVideo');

    //tìm video trên youtube bằng keyword
    Route::get('/searchVideoByApi', 'ApiController@searchVideoByApi')->name('searchVideoByApi');
    //lấy danh sach các video trong bằng playlist_id
    Route::get('/getListVideoPlaylist', 'ApiController@getListVideoPlaylist')->name('getListVideoPlaylist');

    //scan claim cho video mới upload
    Route::get('/scanClaim', 'ApiController@scanClaim')->name('scanClaim');
    Route::get('/scanX', 'ApiController@scanX')->name('scanX');

    //scan claim co dieu kien
    Route::get('/scanClaimByCondition', 'ApiController@scanClaimByCondition')->name('scanClaimByCondition');
    Route::get('/callScanClaimByCondition', 'ApiController@callScanClaimByCondition')->name('callScanClaimByCondition');

    //add video mới upload vào campaign
    Route::get('/updatePromosToCampaign', 'ApiController@updatePromosToCampaign')->name('updatePromosToCampaign');

    //api check fuck artists label
    Route::get('/api/get/ignore/artists', 'ApiController@getFuckArtists')->name('getFuckArtists');
    //api get fuck artists label
    Route::get('/api/get/list/artists', 'ApiController@getListFuckArtists')->name('getListFuckArtists');

    //api delete video trên kênh dùng api
    Route::post('/api/video/delete', 'ApiController@deleteVideo')->name('deleteVideo');

    //xoa anter email 
    Route::get('/api/email/delete', 'ApiController@deleteEmail')->name('deleteEmail');

    //scan crosspost
    Route::get('/scanCrospost', 'ApiController@scanCrospost')->name('scanCrospost');

    //create/add api orfium
    Route::get('/add/orfium/api', 'ApiController@addOrfiumApi')->name('addOrfiumApi');
    Route::get('/create/orfium/api', 'ApiController@createOrfiumApi')->name('createOrfiumApi');

    //Claim
    Route::get('/getDeezerArtistId/{deezerId}', 'CampaignController@getDeezerArtistId')->name('getDeezerArtistId');
    Route::get('/finish/lyric/task', 'CampaignController@finishLyricTask')->name('finishLyricTask');

    //promos job
    Route::get('/makeJobPromos', 'CampaignController@makeJobPromos')->name('makeJobPromos');

    //make notification
    Route::get('/makeNotification', 'NotificationController@makeNotification')->name('makeNotification');

    //api delete playlist
    Route::get('/deletePlaylist', 'ApiController@deletePlaylist')->name('deletePlaylist');

    Route::get('/restart', 'ApiController@restartApache2')->name('restartApache2');

    //api get list genre
    Route::get('/genres/list', 'BomController@genresList')->name('genresList');
    Route::get('/groups/list', 'BomController@groupsList')->name('groupsList');
    Route::get('/genres/count/vip', 'BomController@genresCountVip')->name('genresCountVip');
    //api get song bom by genre
    Route::get('/genres/songs', 'BomController@genresSongs')->name('genresSongs');
    Route::get('/noclaim/songs', 'NoclaimController@noclaimSongs')->name('noclaimSongs');
    //update đếm số lượng sử dụng bom
    Route::get('/bom/update', 'BomController@bomUpdate')->name('bomUpdate');
    Route::get('/bom/social', 'BomController@getSocial')->name('getSocial');

    ////ham fix lỗi Failed to parse Content-Range header cho client
    Route::get('/fixClientHeader', 'ApiController@fixClientHeader')->name('fixClientHeader');

    //lay bai hat promos theo deezer artists id cho music  3 claim
    Route::get('/getSongByDeezerArtistId', 'ApiController@getSongByDeezerArtistId')->name('getSongByDeezerArtistId');
    Route::get('/getSongByGroup', 'ApiController@getSongByGroup')->name('getSongByGroup');
    Route::get('/cutPtmomosMusic', 'CampaignController@cutPtmomosMusic')->name('cutPtmomosMusic');

    //get thông tin api hacked
    Route::get('/getApiInfo', 'ApiController@getApiInfo')->name('getApiInfo');
    //add api 
    Route::post('/api/add/channel', 'ApiController@addChannel')->name('addChannel');

    //đổi profile id khi trên automail
    Route::get('/updateGologinId', 'ChannelManagementController@updateGologinId')->name('updateGologinId');

    //bass new
    Route::get('/scanJobBassNew', 'ChannelManagementController@scanJobBassNew')->name('scanJobBassNew');
    Route::get('/getHashP', 'ChannelManagementController@getPassword')->name('getPassword');
    Route::get('/getCodeByMail', 'ChannelManagementController@getCodeByMail')->name('getCodeByMail');

    //sync cookie run 1 time/day 
    Route::get('/moonShotsSyncCookie', 'MoonshotsController@moonShotsSyncCookie')->name('moonShotsSyncCookie');

    //hệ thống lấy view realtime qua moonshots
    Route::get('/moonshots/channel', 'MoonshotsController@moonShotsChannel')->name('moonShotsChannel');
    Route::post('/moonshots/analytic/update', 'MoonshotsController@moonshotsAnalyticUpdate')->name('moonshotsAnalyticUpdate');
    Route::post('/moonshots/alarm/add', 'MoonshotsController@moonshotsAlarmAdd')->name('moonshotsAlarmAdd');

    //moonshot
    Route::get('/apiRun', 'MoonshotsController@apiRun')->name('apiRun');
    Route::get('/brandRun', 'MoonshotsController@brandRun')->name('brandRun');
    Route::get('/uploadRun', 'MoonshotsController@uploadRun')->name('uploadRun');
    Route::get('/wakeupRun', 'MoonshotsController@wakeupRun')->name('wakeupRun');
    Route::get('/cardRun', 'MoonshotsController@cardRun')->name('cardRun');

    Route::post('/callback/login', 'CallbackController@callbackLogin')->name('callbackLogin');
    Route::post('/callback/api', 'CallbackController@callbackMakeApi')->name('callbackMakeApi');
    Route::post('/callback/brand', 'CallbackController@callbackBrand')->name('callbackBrand');
    Route::post('/callback/brandnew', 'CallbackController@callbackBrandNew')->name('callbackBrandNew');
    Route::post('/callback/upload', 'CallbackController@callbackUpload')->name('callbackUpload');
    Route::post('/callback/wakeup', 'CallbackController@callbackWakeup')->name('callbackWakeup');
    Route::post('/callback/card', 'CallbackController@callbackCard')->name('callbackCard');
    Route::post('/callback/comment', 'CallbackController@callbackComment')->name('callbackComment');
    Route::post('/callback/commentauto', 'CallbackController@callbackCommentAuto')->name('callbackCommentAuto');
    Route::post('/callback/channel/create', 'CallbackController@callbackChannelCreate')->name('callbackChannelCreate');
    Route::post('/callback/fake', 'CallbackController@callbackFake')->name('callbackFake');
    Route::post('/callback/pass/change', 'CallbackController@callbackPassChange')->name('callbackPassChange');
    Route::post('/callback/info/change', 'CallbackController@callbackInfoChange')->name('callbackInfoChange');
    Route::post('/callback/sync_cookie', 'CallbackController@callbackSyncCookie')->name('callbackSyncCookie');
    Route::post('/callback/upload/claim', 'CallbackController@callbackUploadClaim')->name('callbackUploadClaim');
    Route::post('/callback/channel/comment', 'CallbackController@callbackChannelCommment')->name('callbackChannelCommment');

    //2023/04/19 fake callback
    Route::get('/fakecallback', 'ApiController@fakeCallback')->name('fakeCallback');
    Route::get('/claimDirect', 'ApiController@claimDirect')->name('claimDirect');
    Route::get('/syncCookieByVideoId', 'ApiController@syncCookieByVideoId')->name('syncCookieByVideoId');

    Route::get('/api/make/brand/check', 'ApiController@checkMakeBrand')->name('checkMakeBrand');
    Route::get('/api/check/make/api', 'ApiController@checkRunMakeApi')->name('checkRunMakeApi');

    //download bai hat noclaim
    Route::get('/noclaimSync', 'BomController@noclaimSync')->name('noclaimSync');

    //brand kênh di comment dạo
    Route::get('/brandCommentChannel', 'BrandingController@brandCommentChannel')->name('brandCommentChannel');
    Route::get('/brandCommentChannelNew', 'BrandingController@brandCommentChannelNew')->name('brandCommentChannelNew');
    Route::get('/replaceHandle', 'BrandingController@replaceHandle')->name('replaceHandle');

    //chạy autocomment campaign
    Route::get('/runComment', 'CampaignController@runComment')->name('runComment');
    Route::get('/checkBrandSuccess', 'BrandingController@checkBrandSuccess')->name('checkBrandSuccess');

    //get
    Route::get('/api/intro/topic', 'IntroController@getIntroTopic')->name('getIntroTopic');
    Route::get('/api/intro/video', 'IntroController@getIntroVideo')->name('getIntroVideo');

    //scan comment lấy channel name
    Route::get('/scanComment', 'ApiController@scanComment')->name('scanComment');

    Route::get('/processBanner', 'ApiController@processBanner')->name('processBanner');

    //sync shorts video
    Route::get('/shorts/sync', 'ShortsController@shortsSync')->name('shortsSync');
    Route::get('/shorts/info', 'ShortsController@shortsInfo')->name('shortsInfo');
    Route::get('/api/shorts/topics', 'ShortsController@listTopic')->name('listTopic');
    Route::get('/api/shorts/videos', 'ShortsController@listVideoByTopics')->name('listVideoByTopics');
    Route::get('/shorts/get', 'ShortsController@shortsGet')->name('shortsGet');

    //import claim

    Route::get('/checkClaim', 'ClaimController@checkClaim')->name('checkClaim');

    //change info mail
    Route::get('/runMail', 'ApiController@runMail')->name('runMail');

    //bitly craw
    Route::get('/bitly/craw/links', 'BitlyController@crawBitlyLink')->name('crawBitlyLink');
    Route::get('/bitly/craw/stats/daily', 'BitlyController@crawStats')->name('crawStats');
    Route::get('/bitly/craw/stats/monthly', 'BitlyController@crawStatsMonth')->name('crawStatsMonth');

    //2023/05/25 moonaz download count craw
    Route::get('/moonaz/craw/stats/monthly', 'BitlyController@crawMoonaz')->name('crawMoonaz');

    //2023/06/15 autocomment
    Route::get('/autoComments', 'CampaignController@autoComments')->name('autoComments');

    //2023/06/23 360promo
    Route::get('/api/campaign/add', 'Promo360Controller@addCampaign')->name('addCampaign');
    Route::get('/api/user/get', 'Promo360Controller@addCampaign')->name('addCampaign');
    Route::get('/api/syncCampaign', 'Promo360Controller@syncCampaign')->name('syncCampaign');
    Route::get('/api/getProgressCampaign', 'Promo360Controller@getProgressCampaign')->name('getProgressCampaign');

    Route::get('/fixErrViews', 'CampaignController@fixErrViews')->name('fixErrViews');

    Route::get('/caculateMoney', 'MoneyController@caculateMoney')->name('caculateMoney');

    //download lai anh brand da xoa
    Route::get('/downloadBrand', 'BrandingController@downloadBrand')->name('downloadBrand');

    //moonseo
    Route::get('/moonseo/group/list', 'MoonseoController@listGroup')->name('listGroup');
    Route::get('/moonseo/music/list', 'MoonseoController@listMusic')->name('listMusic');
    Route::get('/moonseo/music/load', 'MoonseoController@loadMusic')->name('loadMusic');
    Route::get('/moonseo/gmail/load', 'MoonseoController@getProfileIdByGmail')->name('getProfileIdByGmail');
    Route::get('/moonseo/tiktok/profile/add', 'MoonseoController@tiktokProfileAdd')->name('tiktokProfileAdd');

    //tiktok playlist crawl
    Route::get('/tiktok/playlist/crawl', 'TiktokController@crawlPlaylist')->name('crawlPlaylist');
    Route::get('/tiktok/playlist/get', 'TiktokController@getPlaylist')->name('getPlaylist');
    Route::get('/tiktok/playlist/list', 'TiktokController@getListPlaylist')->name('getListPlaylist');

    //notification
    Route::get('/notification/get', 'NotificationController@crawlNotification')->name('crawlNotification');
    Route::post('/moonseo/notification/add', 'NotificationController@addNotify')->name('addNotify');

    //cache data claim
    Route::get('/claim/data/cache/add', 'ClaimController@cacheAddGetMonthlyClaimViewsGroupByUserOrDistributor')->name('cacheGetMonthlyClaimViewsGroupByUserOrDistributor');
    Route::get('/claim/data/cache/load', 'ClaimController@cacheLoadGetMonthlyClaimViewsGroupByUserOrDistributor')->name('cacheGetMonthlyClaimViewsGroupByUserOrDistributor');
    Route::get('/claim/data/cache/clear', 'ClaimController@cacheClearGetMonthlyClaimViewsGroupByUserOrDistributor')->name('cacheClearGetMonthlyClaimViewsGroupByUserOrDistributor');

    //cache data claim rev
    Route::get('/claim/revenue/cache/add', 'ClaimController@cacheAddRevenueClaim')->name('cacheAddRevenueClaim');


    //code chay 1 lan, viet cac ham dung 1 lan
    Route::get('/code/run', 'ApiController@test')->name('test');

    //api for fat guy
    Route::get('/claim/list', 'ApiController@faListClaim')->name('faListClaim');
    Route::get('/claim/detail', 'ApiController@fatClaimDetail')->name('fatClaimDetail');
    Route::get('/claim/views', 'ApiController@fatClaimViewMonth')->name('fatClaimViewMonth');

    //tính toán claim
    Route::get('/claim/caculate', 'ClaimController@caculateClaims')->name('caculateClaims');

    Route::get('/makeMp4', 'ClaimController@makeMp4')->name('makeMp4');

    Route::get('/studio/result', 'MoonseoController@viewStudioResult')->name('viewStudioResult');

    //lưu chữ vĩnh viễn file studio result để live stream
    Route::post('/studio/drive/save', 'StudioController@studioDriveSave')->name('studioDriveSave');
    Route::get('/studio/drive/sync', 'StudioController@downloadAndUpload')->name('studioDriveDownloadAndUpload');

    Route::get('/calendar/getUserDropdown', 'CalendarController@getUserDropdown')->name('getUserDropdown');

    //2024/07/24 lưu trữ nhạc LD
    Route::post('/ld/save', 'BomController@ldSave')->name('ldSave');
    Route::post('/ld/buy', 'BomController@ldBuy')->name('ldBuy');
    Route::get('/ld/channels', 'BomController@ldChannels')->name('ldChannels');

    //2024/09/13 them nhac youtube tu studio
    Route::post('/bom/spotify/save', 'BomController@addYoutubeFromStudio')->name('addYoutubeFromStudio');
    Route::get('/bom/spotify/get', 'BomController@getYoutubeFromSpotify')->name('getYoutubeFromSpotify');

    //hash_pass
    Route::get('/generateHashForChannel', 'ApiController@generateHashForChannel')->name('generateHashForChannel');
    //2024/09/24 otp key
    Route::get('/getOtp', 'ChannelManagementController@getOtp')->name('getOtp');
    Route::get('/autoChangePass', 'ChannelManagementController@autoChangePass')->name('autoChangePass');

    Route::get('/getChanelByHandle', 'ClaimController@getChanelByHandle')->name('getChanelByHandle');

    Route::get('/videoinfo/{video_id}', function ($video_id) {
        return App\Common\Youtube\YoutubeHelper::getVideoInfoByYoutubeDlp($video_id);
    });

    //get deezer from spotify
    Route::get('/getDeezerFromSpotify', 'BomController@getDeezerFromSpotify')->name('getDeezerFromSpotify');


    Route::get('/claim/artist/list', 'BomController@notDistinctArtist')->name('notDistinctArtist');
    Route::get('/viewsLyricPromo', 'MoneyController@viewsLyricPromo')->name('viewsLyricPromo');

    //2025/02/12 api chạy trả lời comment
    Route::get('/getChannelForComment', 'ChannelManagementController@getChannelForComment')->name('getChannelForComment');


    //chức năng check claim distributor
    Route::get('/checkClaimDistributor', 'ClaimController@checkClaimDistributor')->name('checkClaimDistributor');

    //download lại nhạc deezer bị lỗi
    Route::get('/reDonwnloadBomDeezer', 'BomController@reDonwnloadBomDeezer')->name('reDonwnloadBomDeezer');


    Route::get('/convertWav', 'BomController@convertWav')->name('convertWav');
    Route::post('/album/status/update', 'BomController@updateDistroAlbumStatus')->name('updateDistroAlbumStatus');
    
    //tính thưởng cho kênh epid mới
    Route::get('/channel/epid/rewards', 'ChannelManagementController@epidRewards')->name('epidRewards');
    
    Route::get('/getChannelByHash', 'ChannelManagementController@getChannelByHash')->name('getChannelByHash');
});



