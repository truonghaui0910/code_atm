<?php

namespace App\Http\Controllers;

use App\Common\Logger;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Events\ChatEvent;
use App\Http\Models\AccountInfo;
use App\Http\Models\AutoWakeupHappy;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignComment;
use App\Http\Models\CampaignCommentAuto;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\CardEndsCommand;
use App\Http\Models\ChannelComment;
use App\Http\Models\CrossPost;
use App\Http\Models\Notification;
use App\Http\Models\RebrandChannelCmd;
use App\Http\Models\Strikes;
use App\Http\Models\Upload;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;

class CallbackController extends Controller {

    //hàm parse lỗi bass
    function extractBASErrors($input) {
        // Khởi tạo mảng lưu kết quả lỗi
        $errors = [];

        if ($input === null || $input === "") {
            return [
                    [
                    'script_name' => 'unknown',
                    'func_name' => 'unknown',
                    'error_message' => 'No result'
                ]
            ];
        }
        // Kiểm tra xem đầu vào có nhiều dòng không
        if (strpos($input, "\n") !== false) {
            // Trường hợp nhiều dòng - tách thành từng dòng
            $lines = explode("\n", $input);
        } else {
            // Trường hợp một dòng
            $lines = [$input];
        }

        // Xử lý từng dòng
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Phân tích JSON
            $data = json_decode($line);

            // Kiểm tra nếu parse thất bại
            if ($data === null) {
                continue; // Bỏ qua dòng không phải JSON hợp lệ
            }

            // Kiểm tra xem có lỗi không
            if (isset($data->result) && strpos($data->result, 'WAS_ERROR:') === 0) {
                // Trích xuất thông báo lỗi (bỏ qua "WAS_ERROR:")
                $errorMessage = substr($data->result, 10);

                // Thêm vào danh sách lỗi
                $errors[] = [
                    'script_name' => $data->script_name,
                    'func_name' => $data->func_name,
                    'error_message' => $errorMessage
                ];
            }
        }

        return $errors;
    }

    //2034/04/17 callback create channel
    public function callbackChannelCreate(Request $request) {
        Logger::logUpload("CallbackController.callbackChannelCreate|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $data = AccountInfo::where("bas_new_id", $request->id)->first();
        $channelId = null;
        if ($data) {
            $data->bas_new_status = $request->status;
            $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
            foreach ($results as $result) {
                $temp = json_decode($result);
                if (Utils::containString($result, "create_channel")) {
                    if (!Utils::containString($temp->result, "None") && !Utils::containString($temp->result, "WAS")) {
                        $channelId = $temp->result;
                    }
                }
            }
            if ($channelId != null) {
                $data->chanel_id = $channelId;
                $data->chanel_name = $channelId;
            }
            if ($request->status != 5) {
                $data->gmail_log = $request->result;
            }
            $data->save();
        }
    }

    //2023/03/29 callback login
    public function callbackLogin(Request $request) {
//        Logger::logUpload('|CallbackController.callbackMakeApi|request=' . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackLogin|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
//            Log::info(json_encode($res));
        if ($request != null && $request != "" && !empty($request->status)) {
            $channel = AccountInfo::where("id", $request->studio_id)->first();
            if (!$channel) {
                Logger::logUpload("CallbackController.callbackLogin|not found gmail $request->gmail on automusic");
                return 0;
            }
            if ($channel->gologin == null) {
                if ($request->type == 60) {
                    $input = array("gmail" => $channel->note);
                    $mail = RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/get/", $input);
                    if (!empty($mail->profile_id)) {
                        $channel->gologin = $mail->profile_id;
                    }
                }
            }
            $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
            if ($request->status == 4) {
                $channel->bas_new_status = 4;
                foreach ($results as $result) {
                    $temp = json_decode($result);
                    if (Utils::containString($result, "login")) {
                        $log = $temp->result;
                        if ($log != "true") {
                            $channel->gmail_log = $log;
                            break;
                        }
                    }
                    if (Utils::containString($result, "create_api")) {
                        $log = $temp->result;
                        $channel->gmail_log = $log;
                        break;
                    }
                }
            } else if ($request->status == 5) {
                $channel->bas_new_status = 4;
                foreach ($results as $result) {
                    $temp = json_decode($result);
                    if (Utils::containString($result, "login")) {
                        $log = $temp->result;
                        if ($log == "true") {
                            $channel->bas_new_status = 5;
                        }
                    }
                }
                //2023/03/29 sang confirm login xong ko chuyển sang nước ngoài, đợi đến khi add otp key thì chuyển
                //assigon lại ip proxy nếu tạo api thành công
//                    $url2 = "http://bas.reupnet.info/profile/assign/manual/$channel->note/2/no_proxy";
//                    RequestHelper::callAPI("GET", $url2, []);
            }
        } else {
            if ($request->id == 0) {
                $channel->bas_new_status = 4;
                $channel->save();
            }
        }
        $channel->save();
    }

    //2023/06/15 callback auto comment
    public function callbackCommentAuto(Request $request) {
        Log::info("CallbackController.callbackCommentAuto|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $separate = Config::get('config.separate_text');
        if ($request != null && $request != "" && !empty($request->status)) {
            $commentConfig = CampaignCommentAuto::where("id", $request->studio_id)->first();
//            $campaignStat = CampaignStatistics::where("id", $commentConfig->campaign_id)->first();
            $commentLog = CampaignComment::where("job_id", $request->id)->first();
            $commentLog->status = $request->status;
            $commentLog->last_run = Utils::timeToStringGmT7(time());
            $commentLog->save();
            if ($request->status == 5) {
                $campaign = Campaign::where("video_id", $commentLog->video_id)->update(["last_time_comment" => time()]);
                $status = "success";
            } else {
                $status = "error";
                $campaign = Campaign::where("video_id", $commentLog->video_id)->update(["last_time_comment" => 0]);
            }

            $taskList = json_decode($request->task_list);
            $text = $taskList[1]->params[0]->value;

            $temp = str_replace(array("\r\n", "\n"), $separate, $commentConfig->comments_finish);
            $arrayFinish = explode($separate, $temp);
            $temp2 = str_replace(array("\r\n", "\n"), $separate, $commentConfig->comments);
            $arrayAvaiable = explode($separate, $temp2);
            $videoComment = $commentLog->video_id;
            //lưu lại thông tin đã comment
            array_unshift($arrayFinish, "[" . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . "] [$videoComment] [$request->id] [$status] " . $text);
            if ($request->status != 5) {
                //không hoàn thành thì đẩy lại comment để chạy tiếp
                array_unshift($arrayAvaiable, $text);
            }
//            CampaignComment::where("job_id", $request->id)->update(["status" => $request->status, "last_run" => Utils::timeToStringGmT7(time())]);
            $commentConfig->comments_finish = implode("\r\n", $arrayFinish);
            $commentConfig->comments = implode("\r\n", $arrayAvaiable);
            $commentConfig->save();
        }
    }

    //2023/02/28 callback comment
    public function callbackComment(Request $request) {
        Log::info("CallbackController.callbackComment|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $separate = Config::get('config.separate_text');
        if ($request != null && $request != "" && !empty($request->status)) {
            $campaign = CampaignStatistics::where("id", $request->studio_id)->first();
            $official = json_decode($campaign->official);
            if ($request->status == 5) {
                $status = "success";
                $official->cmt_last_run = time();
            } else {
                $status = "error";
                $official->cmt_last_run = 0;
            }
            $taskList = json_decode($request->task_list);
            $text = $taskList[1]->params[0]->value;

            $temp = str_replace(array("\r\n", "\n"), $separate, $official->crypto_cmt_content_finish);
            $arrayFinish = explode($separate, $temp);
            $temp2 = str_replace(array("\r\n", "\n"), $separate, $official->crypto_cmt_content);
            $arrayAvaiable = explode($separate, $temp2);
            $videoComment = $official->crypto_cmt_link;
            //lưu lại thông tin đã comment
            array_unshift($arrayFinish, "[" . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . "] [$videoComment] [$request->id] [$status] " . $text);
            if ($request->status == 5) {
                array_shift($arrayAvaiable);
            }
            CampaignComment::where("job_id", $request->id)->update(["status" => $request->status, "last_run" => Utils::timeToStringGmT7(time())]);
            $official->crypto_cmt_content_finish = implode("\r\n", $arrayFinish);
            $official->crypto_cmt_content = implode("\r\n", $arrayAvaiable);
            $campaign->official = json_encode($official);
            $campaign->save();
        }
    }

    public function callbackMakeApi(Request $request) {
//        Logger::logUpload('|CallbackController.callbackMakeApi|request=' . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackMakeApi|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        //2024/10/29 khi nào chạy thì phải sủa update cho nhiều bản ghi vi 1 email nhiều kênh
        if ($request != null && $request != "" && !empty($request->status)) {
            if ($request->status == 4) {
                AccountInfo::where("note", $request->gmail)->update(["api_status" => $request->status]);
                $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
                foreach ($results as $result) {
                    $temp = json_decode($result);
                    if (Utils::containString($result, "login")) {
                        $log = $temp->result;
                        if ($log != "true") {
                            $channel->gmail_log = $log;
                            AccountInfo::where("note", $request->gmail)->update(["gmail_log" => $log]);
                            break;
                        }
                    }
                    if (Utils::containString($result, "create_api")) {
                        $log = $temp->result;
                        $channel->gmail_log = $log;
                        AccountInfo::where("note", $request->gmail)->update(["gmail_log" => $log]);
                        break;
                    }
                }
            } else if ($request->status == 5) {
                AccountInfo::where("note", $request->gmail)->update(["api_status" => 2]);
            }
            $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " callbackMakeApi set api_status=$request->status";
            AccountInfo::where("note", $request->gmail)->update(["log" => DB::raw("CONCAT(log,'$log')")]);
        }
    }

    public function callbackBrand(Request $request) {
//        Logger::logUpload('|CallbackController.callbackBrand|request=' . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackBrand|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $data = RebrandChannelCmd::where("bas_id", $request->id)->first();
        if ($request->status == 4 || $request->status == 5) {
            $data->status = $request->status;
            $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
            foreach ($results as $result) {
//                        Log::info($result);
                $temp = json_decode($result);
                if (Utils::containString($result, "login")) {
                    $log = $temp->result;
                    if ($log != "true") {
                        $data->log = $log;
                        if (Utils::containString($log, "ERR_CONNECTION_CLOSED")) {
                            $data->status = 0;
                            $data->log = $log . " runing again";
                            $update = [
                                "id" => $request->id,
                                "status" => 0
                            ];
                            RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", $update);
                            $data->retry = $data->retry + 1;
                        }
                        break;
                    }
                }
                if (Utils::containString($result, "rebrand") || Utils::containString($result, "brand")) {
                    $log = $temp->result;
                    $data->log = $log;
                    if (Utils::containString($log, "ERR_CONNECTION_CLOSED")) {
                        $data->status = 0;
                        $data->log = $log . " runing again";
                        $update = [
                            "id" => $request->id,
                            "status" => 0
                        ];
                        RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", $update);
                        $data->retry = $data->retry + 1;
                    }
                    break;
                }
            }
            $data->last_update_time = gmdate("d/m/Y H:i:s", time() + 7 * 3600);
            $data->save();
            $accountInfo = AccountInfo::where("chanel_id", $data->channel_id)->first();
            if ($accountInfo) {
                if ($request->status == 5) {
                    $accountInfo->chanel_name = $data->first_name;
                }
                if ($request->status != 0) {
                    $accountInfo->is_rebrand = $data->status;
                    $accountInfo->log = $accountInfo->log . PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " set is_rebrand=$accountInfo->is_rebrand";
                }
                $accountInfo->gmail_log = $data->log;
                $accountInfo->save();
            }
        }
    }

    public function callbackBrandNew(Request $request) {
        Log::info('|CallbackController.callbackBrandNew|request=' . json_encode($request->all()));
        $data = ChannelComment::where("bas_id", $request->id)->first();
        $channelId = "";
        if ($data) {
            $data->status = $request->status;

            $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
            foreach ($results as $result) {
                $temp = json_decode($result);
                if (Utils::containString($result, "create_channel")) {
                    if (!Utils::containString($temp->result, "None") && !Utils::containString($temp->result, "WAS")) {
                        $channelId = $temp->result;
                    }
                }
            }
            $data->channel_id_brand = $channelId;
            if ($request->status != 5) {
                $data->log = $request->result;
            }
            $data->save();
            $accountInfo = AccountInfo::where("chanel_id", $data->channel_id_brand)->first();
            if ($accountInfo) {

                $accountInfo->create_time = time();
                if ($channelId != "") {
                    $accountInfo->chanel_id = $channelId;
                }
                $accountInfo->chanel_name = $data->channel_name;
                $accountInfo->is_rebrand = $request->status;
                $accountInfo->save();
            }
        }
    }

    public function callbackUpload(Request $request) {
//        Logger::logUpload('|CallbackController.callbackUpload|request=' . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackUpload|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $currDate = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $upload = Upload::where("bas_id", $request->id)->orderBy("id", "desc")->first();
        $channel = AccountInfo::where("chanel_id", $upload->channel_id)->first();
        if (!$channel) {
            $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $upload->log = "Not found channel info";
            $upload->status = 6;
            $upload->save();
            return 0;
        }
        //kiểm tra xem kênh đã login moonshots chưa
        if ($channel->gologin == null) {
            return 0;
        }
        $script_name = 'upload';
        $func_name = 'comment';
        $type = 610;
        $name = "studio_comment_moon";

        if ($request->result == null || $request->result == "") {
            $upload->status = 4;
            $upload->log = "Error extention return no result";
            $upload->update_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $upload->save();
            $req = [
                "id" => $upload->source_id,
                "status" => 4,
                "upload_log" => $upload->log,
                "upload_status" => 4
            ];
            RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
            Logger::logUpload("callbackUpload update to studio " . json_encode($req));
            return 0;
        }

        $videoId = "";
        $upload->status = $request->status;
        $upload->log = "";
        $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
        foreach ($results as $result) {
            $temp = json_decode($result);
            if (Utils::containString($result, "login")) {
                $log = $temp->result;
                if ($log != "true") {
                    $upload->log = $log;
                    if (Utils::containString($log, "ERR_CONNECTION_CLOSED") || Utils::containString($log, "Failed to get proxy ip") || Utils::containString($log, "ERR_CERT_COMMON_NAME_INVALID")) {
                        if ($upload->retry < 3) {
                            $upload->status = 0;
                            $upload->log = $log . " runing again";
                            RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", ["id" => $request->id, "status" => 0]);
                            $upload->retry = $upload->retry + 1;
                        }
                    }
                    break;
                }
            }
            if (Utils::containString($result, "reupload") || Utils::containString($result, "upload")) {
                if (Utils::containString($log, "ERR_CONNECTION_CLOSED") || Utils::containString($log, "Failed to get proxy ip") || Utils::containString($log, "ERR_CERT_COMMON_NAME_INVALID")) {
                    if ($upload->retry < 3) {
                        $upload->status = 0;
                        $upload->log = $log . " runing again";
                        RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/status", ["id" => $request->id, "status" => 0]);
                        $upload->retry = $upload->retry + 1;
                    }
                    break;
                }
                $t = time();
                //lệnh bị ăn gậy
                if (Utils::containString($log, "Copyright strikes")) {
                    //nếu có gậy trong 10 ngày gần nhất thì ko thêm vào strikes nữa, vi có thể là bị strike rôi nhưng hệ thống vẫn lên lệnh upload
                    $check = Strikes::whereRaw("$t - date < 10 *86400 ")->where("channel_id", $channel->chanel_id)->first();
                    if (!$check) {
                        $strikes = new Strikes();
                        $strikes->date = strtotime($request->created);
                        $strikes->date_text = $request->created;
                        $strikes->type = 1;
                        $strikes->channel_id = $channel->chanel_id;
                        $strikes->channel_name = $channel->chanel_name;
                        $strikes->gmail = $request->gmail;
                        $strikes->strike = "Copyright strikes";
                        $strikes->save();
                        $channel->strikes = "This channel got Copyright strike";
                        $channel->save();
                    }
                    break;
                } else if (Utils::containString($log, "Community strikes")) {
                    $check = Strikes::whereRaw("$t - date < 3 *86400 ")->where("channel_id", $channel->chanel_id)->first();
                    if (!$check) {
                        $strikes = new Strikes();
                        $strikes->date = strtotime($request->created);
                        $strikes->date_text = $request->created;
                        $strikes->type = 2;
                        $strikes->channel_id = $channel->chanel_id;
                        $strikes->channel_name = $channel->chanel_name;
                        $strikes->gmail = $request->gmail;
                        $strikes->strike = "Community strikes";
                        $strikes->save();
                        $channel->strikes = "This channel got Community strike";
                        $channel->save();
                    }
                    break;
                }

                if ($request->status == 5) {
                    $videoId = $temp->result;
                    $upload->log = $videoId;
                } else {
                    $log = $temp->result;
                    $upload->log = $log;
                }
                //upload tiktok
                if (Utils::containString($result, "tiktok")) {
                    $log = $temp->result;
                    $upload->log = $log;
                }
            }
        }
        if ($upload->type == "studio_moon") {
            $req = [
                "id" => $upload->source_id,
                "status" => 5,
                "upload_log" => $upload->log != "" ? $upload->log : "Error extention",
                "upload_status" => $request->status == 5 ? 3 : 4,
            ];
            RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/job/auto-upload/update", $req);
            Logger::logUpload("callbackUpload update to studio " . json_encode($req));
        }
        $upload->update_time = $currDate;
        $upload->save();

        if ($request->status == 5 && $upload->type == "studio_moon" && $videoId != "" && $videoId != null) {
            //tạo lệnh auto comment nếu upload thành công
            $curr = time();
            $studio = RequestHelper::callAPI("GET", "http://api-magicframe.automusic.win/job/load/$upload->source_id", []);
            Logger::logUpload("UPload thanh cong $studio->id status=$studio->status auto_submit=$studio->auto_submit");
            $meta = json_decode($studio->reup_config);
            $title = $meta->title;
            $accountInfo = AccountInfo::where("chanel_id", $studio->channel_id)->first();
            $isBommix = 0;
            if (!empty($meta->boommix_update_title) && $meta->boommix_update_title) {
                $isBommix = 1;
                Log::info("callbackUpload $studio->id is_boommix");
            }
            //auto submit, 
            if ($studio->auto_submit == 1) {
                if ($studio->crosspost_user != null) {
                    $crossPost = CrossPost::where("job_id", $upload->source_id)->first();
                    $campaignId = $crossPost->campaign_id;
                    $campaignStatic = CampaignStatistics::find($campaignId);
                    $campaign = new Campaign();
                    $campaign->campaign_id = $campaignId;
                    $campaign->username = $studio->user_name;
                    $campaign->campaign_name = $campaignStatic->campaign_name;
                    $campaign->channel_id = $accountInfo->chanel_id;
                    $campaign->channel_name = $accountInfo->chanel_name;
                    $campaign->video_type = 2;
                    $campaign->video_id = trim($videoId);
                    $campaign->video_title = $title;
                    $campaign->views_detail = '[]';
                    $campaign->status = 1;
                    $campaign->is_bommix = $isBommix;
                    $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                    $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                    $campaign->publish_date = time();
                    $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                    $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                    $campaign->status_confirm = 1;
                    $log = gmdate("Y/m/d H:i:s", $curr + 7 * 3600) . " auto submit";
                    $campaign->log = $log;
                    $campaign->save();
                    Logger::logUpload("auto submit lyric $request->id $videoId");
                } else {
                    $cts = json_decode($studio->content);
//                    $keys = ["claim_music", "claim2_music", "promo_music", "promo2_music", "claims", "promos"];
                    //2023/06/07 xóa key claim_music,promo_music
                    $keys = ["claims", "promos"];
                    foreach ($cts as $ct) {
                        if (!empty($ct->comp_data)) {
                            foreach ($keys as $key) {
                                $listId = [];
                                if (!empty($ct->comp_data->$key[0])) {
                                    $dataPromoClaim = $ct->comp_data->$key;
                                    foreach ($dataPromoClaim as $claim) {
                                        if ($key == "claims" || $key == "promos") {
                                            if (count($claim->musics) > 0) {
                                                foreach ($claim->musics as $c) {
                                                    $listId[] = $c->id;
                                                }
                                            }
                                        } else {
                                            $listId[] = $claim->id;
                                        }
                                        if (count($listId) == 0) {
                                            continue;
                                        }
                                        foreach ($listId as $claimId) {
                                            $check = Campaign::where("video_id", $videoId)->where("campaign_id", $claimId)->first();
                                            if (!$check) {
                                                $campaignStatic = CampaignStatistics::find($claimId);
                                                $campaign = new Campaign();
                                                $campaign->campaign_id = $claimId;
                                                $campaign->username = $studio->user_name;
                                                $campaign->campaign_name = $campaignStatic->campaign_name;
                                                $campaign->channel_id = $accountInfo->chanel_id;
                                                $campaign->channel_name = $accountInfo->chanel_name;
                                                $campaign->video_type = 5;
                                                $campaign->video_id = trim($videoId);
                                                $campaign->video_title = $title;
                                                $campaign->views_detail = '[]';
                                                $campaign->status = 1;
                                                $campaign->is_bommix = $isBommix;
                                                $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                                $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                                $campaign->publish_date = time();
                                                $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                                                $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                                                $campaign->status_confirm = 1;
                                                $log = gmdate("Y/m/d H:i:s", $curr + 7 * 3600) . " auto submit";
                                                $campaign->log = $log;
                                                if (Utils::containString($key, "claim")) {
                                                    $campaign->is_claim = 1;
                                                }
                                                $campaign->save();
                                                Logger::logUpload("auto submit mix $request->id $videoId $claimId");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $autoType = ['claim', 'promo'];
                if ($studio->music_data != null) {
                    $musicDatas = json_decode($studio->music_data);
                    foreach ($musicDatas as $mix) {
                        if (!empty($mix->automusic_type)) {
                            if (!empty($mix->automusic_type) && $mix->automusic_type != null && in_array($mix->automusic_type, $autoType)) {
                                $check = Campaign::where("video_id", $videoId)->where("campaign_id", $mix->automusic_id)->first();
                                if (!$check) {
                                    $campaignStatic = CampaignStatistics::find($mix->automusic_id);
                                    $campaign = new Campaign();
                                    $campaign->campaign_id = $mix->automusic_id;
                                    $campaign->username = $studio->user_name;
                                    $campaign->campaign_name = $campaignStatic->campaign_name;
                                    $campaign->channel_id = $accountInfo->chanel_id;
                                    $campaign->channel_name = $accountInfo->chanel_name;
                                    $campaign->video_type = 5;
                                    $campaign->video_id = trim($videoId);
                                    $campaign->video_title = $title;
                                    $campaign->views_detail = '[]';
                                    $campaign->status = 1;
                                    $campaign->is_bommix = $isBommix;
                                    $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                    $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                    $campaign->publish_date = time();
                                    $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                                    $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                                    $campaign->status_confirm = 1;
                                    $log = gmdate("Y/m/d H:i:s", $curr + 7 * 3600) . " auto submit music_data";
                                    $campaign->log = $log;
                                    if ($mix->automusic_type == 'claim') {
                                        $campaign->is_claim = 1;
                                    }
                                    $campaign->save();
                                    Logger::logUpload("auto submit music_data $request->id $videoId $mix->automusic_id");
                                }
                            }
                        }
                    }
                } else {
                    $cts = json_decode($studio->content);
                    foreach ($cts as $ct) {
                        if (!empty($ct->mix_data)) {
                            foreach ($ct->mix_data as $mix) {
                                if (!empty($mix->automusic_type) && $mix->automusic_type != null && in_array($mix->automusic_type, $autoType)) {
                                    $check = Campaign::where("video_id", $videoId)->where("campaign_id", $mix->automusic_id)->first();
                                    if (!$check) {
                                        $campaignStatic = CampaignStatistics::find($mix->automusic_id);
                                        $campaign = new Campaign();
                                        $campaign->campaign_id = $mix->automusic_id;
                                        $campaign->username = $studio->user_name;
                                        $campaign->campaign_name = $campaignStatic->campaign_name;
                                        $campaign->channel_id = $accountInfo->chanel_id;
                                        $campaign->channel_name = $accountInfo->chanel_name;
                                        $campaign->video_type = 5;
                                        $campaign->video_id = trim($videoId);
                                        $campaign->video_title = $title;
                                        $campaign->views_detail = '[]';
                                        $campaign->status = 1;
                                        $campaign->is_bommix = $isBommix;
                                        $campaign->create_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                        $campaign->update_time = gmdate("Y/m/d H:i:s", $curr + 7 * 3600);
                                        $campaign->publish_date = time();
                                        $campaign->insert_date = gmdate("Ymd", $curr + 7 * 3600);
                                        $campaign->insert_time = gmdate("H:i:s", $curr + 7 * 3600);
                                        $campaign->status_confirm = 1;
                                        $log = gmdate("Y/m/d H:i:s", $curr + 7 * 3600) . " auto submit mix_data";
                                        $campaign->log = $log;
                                        if ($mix->automusic_type == 'claim') {
                                            $campaign->is_claim = 1;
                                        }
                                        $campaign->save();
                                        Logger::logUpload("auto submit mix_data $request->id $videoId $mix->automusic_id");
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //auto comment

            $autoComment = false;
            if (!empty($meta->auto_comment)) {
                $autoComment = $meta->auto_comment;
            }
            //nếu fake thì ko chạy commnent
            if (isset($request->fake)) {
                $autoComment = false;
            }
            if ($autoComment) {
                $comment = "";
                if (!empty($meta->comment)) {
                    $comment = $meta->comment;
                }
                $isPinComment = false;
                if (!empty($meta->is_pin_comment)) {
                    $isPinComment = $meta->is_pin_comment;
                }
                $schedule = null;
                if (!empty($meta->schedule)) {
                    $schedule = $meta->schedule;
                }

                $taskLists = [];
                $login = (object) [
                            "script_name" => "profile",
                            "func_name" => "login",
                            "params" => []
                ];

                $paramsComment = [];
                $listParamComment = ["description" => $comment, "video_source" => $upload->log, "category" => $isPinComment ? "PIN" : ""];
                foreach ($listParamComment as $key => $value) {
                    $param = (object) [
                                "name" => $key,
                                "type" => "string",
                                "value" => $value
                    ];
                    $paramsComment[] = $param;
                }
                $pinComment = (object) [
                            "script_name" => $script_name,
                            "func_name" => $func_name,
                            "params" => $paramsComment
                ];
                $taskLists[] = $login;
                $taskLists[] = $pinComment;

                //tính giờ chạy comment
                $runTime = 0;
                if ($schedule == null || $schedule == '{}') {
                    $runTime = time();
                } else {
                    $runTime = strtotime("$schedule->date $schedule->time $schedule->time_zone");
                }

                $req = (object) [
                            "gmail" => $request->gmail,
                            "task_list" => json_encode($taskLists),
                            "run_time" => $runTime + 3600,
                            "type" => $type,
                            "studio_id" => $upload->source_id,
                            "piority" => 30
                ];
                Logger::logUpload("callbackUpload COMMENT req:" . json_encode($req));
                $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                Logger::logUpload("callbackUpload COMMENT res:" . json_encode($bas));
                $uploadStd = new Upload();
                $uploadStd->type = $name;
                $uploadStd->status = 1;
                $uploadStd->source_id = $upload->source_id;
                $uploadStd->channel_id = $studio->channel_id;
                if ($bas->mess == "ok") {
                    $uploadStd->bas_id = $bas->job_id;
                }
                $uploadStd->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $uploadStd->save();
            }
        }

        if ($videoId != null && $videoId != "" && $request->status == 5) {
            //auto set endcard
            $taskLists = [];
            $paramsCard = [];
            $paramsEndScreen = [];
            $param1 = (object) [
                        "name" => "headers",
                        "type" => "json",
                        "value" => "{}",
            ];
            $param2 = (object) [
                        "name" => "params",
                        "type" => "json",
                        "value" => "{}",
            ];
            $param3 = (object) [
                        "name" => "method",
                        "type" => "string",
                        "value" => "POST",
            ];
            $param4 = (object) [
                        "name" => "response_check",
                        "type" => "string",
                        "value" => "EDIT_EXECUTION_STATUS_DONE",
            ];
            $param5 = (object) [
                        "name" => "url",
                        "type" => "string",
                        "value" => "https://studio.youtube.com/youtubei/v1/video_editor/edit_video?alt=json&key=AIzaSyBUPetSUmoZL-OhlxA7wSac5XinrygCqMo",
            ];
            $param6 = (object) [
                        "name" => "get_start_ms",
                        "type" => "func",
                        "value" => json_encode((object) ["video_id" => $upload->log]),
            ];
            if (!empty($meta->auto_card) && $meta->auto_card) {
                if (!empty($meta->payload_card)) {
                    $paramsCard[] = $param1;
                    $paramsCard[] = $param2;
                    $paramsCard[] = $param3;
                    $paramsCard[] = $param4;
                    $paramsCard[] = $param5;
                    $paramsCard[] = $param6;
                    $infoCardsString = str_replace("@@video_id", $upload->log, json_encode($meta->payload_card));
                    $paramsCard[] = (object) [
                                "name" => "payload",
                                "type" => "json",
                                "value" => ($infoCardsString)
                    ];
                    $taskListCard = (object) [
                                "script_name" => "request",
                                "func_name" => "edit_card",
                                "params" => $paramsCard
                    ];
                    $taskLists[] = $taskListCard;
                }
            }
            if (!empty($meta->auto_endscreen) && $meta->auto_endscreen) {
                if (!empty($meta->payload_endscreen)) {
                    $paramsEndScreen[] = $param1;
                    $paramsEndScreen[] = $param2;
                    $paramsEndScreen[] = $param3;
                    $paramsEndScreen[] = $param4;
                    $paramsEndScreen[] = $param5;
                    $paramsEndScreen[] = $param6;
                    $infoEndScreenString = str_replace("@@video_id", $upload->log, json_encode($meta->payload_endscreen));
                    $paramsEndScreen[] = (object) [
                                "name" => "payload",
                                "type" => "json",
                                "value" => $infoEndScreenString
                    ];
                    $taskListEndScreen = (object) [
                                "script_name" => "request",
                                "func_name" => "edit_card",
                                "params" => $paramsEndScreen
                    ];
                    $taskLists[] = $taskListEndScreen;
                }
            }

            if (count($taskLists) > 0) {
                $req = (object) [
                            "gmail" => $request->gmail,
                            "task_list" => json_encode($taskLists),
                            "run_time" => time() + 1800,
                            "type" => 613,
                            "studio_id" => $upload->source_id,
                            "piority" => 80
                ];
                Logger::logUpload("callbackUpload CARD_ENDSCREEN req:" . json_encode($req));
                $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                Logger::logUpload("callbackUpload CARD_ENDSCREEN res:" . json_encode($bas));
                $uploadStd = new Upload();
                $uploadStd->type = "studio_moon_card";
                $uploadStd->status = 1;
                $uploadStd->source_id = $upload->source_id;
                $uploadStd->channel_id = $studio->channel_id;
                if ($bas->mess == "ok") {
                    $uploadStd->bas_id = $bas->job_id;
                }
                $uploadStd->create_time = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $uploadStd->save();
            }
        }

        //check xem có phải lệnh cross post không, nếu là cross post thì update vào bảng cross_post
        $crossPost = CrossPost::where("job_id", $upload->source_id)->first();
        if ($crossPost) {
            $crossPost->video_id = $upload->log;
            $crossPost->save();
        }
        return 1;
    }

    public function callbackWakeup(Request $request) {
//        Logger::logUpload('|CallbackController.callbackWakeup|request=' . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackWakeup|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
        $newPllId = null;
        $curr = time();
        $curentDate = gmdate("Y-m-d", $curr + (7 * 3600));
        $curentTime = gmdate("H:i:s", $curr + (7 * 3600));
        foreach ($results as $result) {
            if (Utils::containString($result, "wakeup")) {
                $temp = json_decode($result);
//                        Log::info("checkRunWakeup $data->job_id $temp->result");
                if (!Utils::containString($temp->result, "None") && $temp->result != "" && !Utils::containString($temp->result, "UNDEFINED")) {
                    $newPllId = $temp->result;
                    if (Utils::containString($newPllId, "WAS_ERROR")) {
                        $tmps = explode(":", $temp->result);
                        if (count($tmps) == 3) {
                            $newPllId = $tmps[1];
                        }
                    }
                }
            }
        }
        $data = AutoWakeupHappy::where("job_id", $request->id)->first();
        $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();

        if ($request->status == 5) {
            if ($data->wakeup_type == 2) {
                $wakeup = new AutoWakeupHappy();
                $wakeup->username = $data->username;
                $wakeup->channel_id = $data->channel_id;
                $wakeup->gmail = $data->gmail;
                $wakeup->playlist_source = $data->playlist_source;
                $wakeup->playlist_id = $newPllId;
                $wakeup->title = $data->title;
                $wakeup->next_time_run = $curr + 3600 * 6;
                $wakeup->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                $wakeup->create_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                $wakeup->wakeup_type = $data->wakeup_type;
                $wakeup->source_type = $data->source_type;
                $wakeup->sort = $data->sort;
                $wakeup->number_videos = $data->number_videos;
                $wakeup->priority_promo_list = $data->priority_promo_list;
                $wakeup->videos_list_source = $data->videos_list_source;
                $wakeup->channel_name = $data->channel_name;
                $wakeup->save();
                $data->status = $request->status;
                $data->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                $data->save();
            } else {
                //lệnh tạo playlist thường sẽ ko tạo ra lệnh chờ mới nữa
                $data->status = $request->status;
                $data->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
                $data->save();
            }
        } else if ($request->status == 4) {
            $data->status = 4;
            $data->log = $request->result;
            $data->last_excute_time_text = gmdate("Y-m-d H:i:s", $curr + (7 * 3600));
            if ($newPllId != null) {
                $data->playlist_id = $newPllId;
            }
            //cho chạy lại nếu ERR_CONNECTION_CLOSED
            if (Utils::containString(json_encode($results), "WAS_ERROR:ERR_CONNECTION_CLOSED")) {
                $data->status = 0;
                $data->next_time_run = 10;
            }
            $data->save();
        }
    }

    public function callbackCard(Request $request) {
//        Log::info("|cardEndscreenCallBack|request=" . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackCard|request=id=$request->id,gmail=$request->gmail,status=$request->status");
        $data = CardEndsCommand::where("id", $request->studio_id)->first();
        $data->status = $request->status;
        $data->log = $request->log;
        $data->save();
    }

    //2023/12/11 fake callback dung de fix du lieu hoac test
    public function callbackFake(Request $request) {
        Log::info("|callbackFake|request=" . json_encode($request->all()));
        $basIds = explode(",", $request->bas_id);
        foreach ($basIds as $basId) {
            $res = RequestHelper::callAPI("GET", "http://bas.reupnet.info/job/load/$basId", []);
            Log::info(json_encode($res));
            if ($res->id != 0) {
                $rq = new Request();
                $rq->fake = 1;
                $rq->id = $res->id;
                $rq->studio_id = $res->studio_id;
                $rq->gmail = $res->gmail;
                $rq->status = $res->status;
                $rq->result = $res->result;
                $rq->created = $res->created;
                $rq->task_list = $res->task_list;
                $call = new CallbackController();
                if ($request->type == 'upload') {
                    $call->callbackUpload($rq);
                }if ($request->type == 'upload_claim') {
                    $rq->us = "truongpv";
                    $call->callbackUploadClaim($rq);
                } elseif ($request->type == 'comment') {
                    $call->callbackComment($rq);
                } elseif ($request->type == 'wakeup') {
                    $call->callbackWakeup($rq);
                } elseif ($request->type == 'change_pass') {
                    $call->callbackPassChange($rq);
                } elseif ($request->type == 'change_info') {
                    $call->callbackInfoChange($rq);
                } elseif ($request->type == 'sync_cookie') {
                    $rq->us = "truongpv";
                    $call->callbackSyncCookie($rq);
                }
            }
        }
    }

    public function callbackPassChange(Request $request) {
        Logger::logUpload("CallbackController.callbackPassChange|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $tele = "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=";
        $message = "callbackPassChange|job_id=$request->id,ref_id=$request->studio_id,gmail= $request->gmail ,status=$request->status";

        if ($request->status == 5) {
            $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
            foreach ($results as $result) {
                $temp = json_decode($result);
                if (Utils::containString($result, "change_pass")) {
                    $rs = explode(";;", $temp->result);
                    if (count($rs) == 2) {
                        $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " change pass success job_id=$request->id oldpass: " . $rs[0] . " newpass: " . $rs[1];
                        $accountInfo = AccountInfo::where("note", $request->gmail)->update(['last_change_pass' => time(),"message"=>"Change pass success", "log" => DB::raw("CONCAT(log,'$log')")]);
                        $input = array("gmail" => $request->gmail, "passWord" => $rs[1]);
                        RequestHelper::callAPI2("POST", "http://165.22.105.138/automail/api/mail/update/", $input);
                        $message = "success $message";
                        RequestHelper::callAPI("GET", $tele . urlencode($message), []);
                        return 1;
                    }
                }
            }
        } else if($request->status == 4){
            $error = $this->extractBASErrors($request->result);
            $logMessage = "Change Pass Error\n" . $error[0]["error_message"];
            $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " change fail fail job_id=$request->id";
            $accountInfo = AccountInfo::where("note", $request->gmail)->update(['last_change_pass' => 4, "message" => $logMessage, "log" => DB::raw("CONCAT(log,'$log')")]);
            $message = "fail $message";
            RequestHelper::callAPI("GET", $tele . urlencode($message), []);
            return 0;
        }
    }

    public function callbackInfoChange(Request $request) {
        Logger::logUpload("CallbackController.callbackInfoChange|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        $tele = "https://api.telegram.org/bot1224443390:AAHgF0sXuDDqU7bSqBSzILQgTVkx672aQZU/sendMessage?chat_id=-475912250&text=";
        $message = "callbackInfoChange|job_id=$request->id,ref_id=$request->studio_id,gmail= $request->gmail ,status=$request->status";

        if ($request->status == 5) {
            $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " change info success job_id=$request->id";
            AccountInfo::where("note", $request->gmail)->update(['last_change_pass' => time(), "log" => DB::raw("CONCAT(log,'$log')")]);
            //đổi text từ change info fail thành fail change info để search những trường hợp fail  = từ khóa change info fail
            AccountInfo::where("note", $request->gmail)->update(['last_change_pass' => time(), "message" => "Change info success",'log' => DB::raw("REPLACE(log, 'change info fail', 'fail change info')")]);
            RequestHelper::callAPI("GET", $tele . urlencode($message), []);
            return 1;
        } else if($request->status == 4) {
            $error = $this->extractBASErrors($request->result);
            $logMessage = "Change Info Error\n" . $error[0]["error_message"];
            $log = PHP_EOL . gmdate("Y-m-d H:i:s", time() + 7 * 3600) . " change info fail job_id=$request->id";
            $accountInfo = AccountInfo::where("note", $request->gmail)->update(['last_change_pass' => 4, "message" => $logMessage, "log" => DB::raw("CONCAT(log,'$log')")]);
            $message = "fail $message";
            RequestHelper::callAPI("GET", $tele . urlencode($message), []);
            return 0;
        }
    }

    public function callbackSyncCookie(Request $request) {
        Logger::logUpload("CallbackController.callbackSyncCookie|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        if (isset($request->us)) {
            $user = User::where("user_name", $request->us)->first();
            $channel = AccountInfo::where("note", $request->gmail)->first();
            $data = (object) [
                        "type" => 0,
                        "job_id" => $request->studio_id,
                        "title" => "Sync Cookie",
                        "message" => "$channel->chanel_name -> " . ($request->status == 5 ? "success" : "fail"),
                        "comment" => null,
                        "redirect" => "/channelmanagement?c1=$request->gmail",
                        "noti_id" => uniqid()
            ];
            event(new ChatEvent($user, [$request->us], $data));
        }
        return 0;
    }

    //upload để test claim distributor
    public function callbackUploadClaim(Request $request) {
//        Logger::logUpload('|CallbackController.callbackUpload|request=' . json_encode($request->all()));
        Logger::logUpload("CallbackController.callbackUploadClaim|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status,us=$request->us");
        $currDate = gmdate("Y/m/d H:i:s", time() + 7 * 3600);

        if ($request->result == null || $request->result == "") {
            return 0;
        }

        $videoId = "";
        $message = "";
        $campaignId = $request->studio_id;
        $results = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->result)));
        foreach ($results as $result) {
            $temp = json_decode($result);
            if (Utils::containString($result, "reupload") || Utils::containString($result, "upload")) {
                if ($request->status == 5) {
                    $videoId = $temp->result;
                } else {
                    $message = "$request->gmail $temp->result";
                }
            }
        }


        if ($request->status == 5 && $videoId != "" && $videoId != null) {
            Log::info("callbackUploadClaim $campaignId $videoId");
            sleep(20);
            //kiểm tra claim và distributor 
            $cmd = "sudo /home/tools/env/bin/python /home/tools/CopyRight.py check_copyright2 $request->gmail $videoId";
            $rs = shell_exec($cmd);
            Log::info("callbackUploadClaim $campaignId " . $cmd);
            Log::info("callbackUploadClaim $campaignId " . $rs);
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
            //parsing campaignId
//            $data = json_decode($request->task_list, true);
//            foreach ($data as $item) {
//                if (isset($item['params'])) {
//                    foreach ($item['params'] as $param) {
//                        if ($param['name'] === 'title') {
//                            $campaignId = $param['value'];
//                            break 2; // Thoát khỏi cả hai vòng lặp
//                        }
//                    }
//                }
//            }

            $campaign = CampaignStatistics::where("id", $campaignId)->first();
            $campaignName = "";
            if ($campaign) {
                $campaignName = $campaign->campaign_name;
                if ($assetId != null) {
                    $campaign->asset_id = $assetId;
                    $campaign->save();
                }

                if ($campaign->short_text == null) {
                    $shortText = (object) [];
                } else {
                    $shortText = json_decode($campaign->short_text);
                }
                $shortText->video_id = $videoId;
                $shortText->gmail = $request->gmail;
                $shortText->title = $title;
                $shortText->artists = $artists;
                $shortText->assetId = $assetId;
                $shortText->distributor = $distributor;
                $shortText->time_gmt7 = Utils::timeToStringGmT7(time());
                $shortText->time = time();
                $campaign->short_text = json_encode($shortText);
                $campaign->save();
            }
            $message = "#$campaignId $campaignName belong to $distributor";

            //xóa resource
//            unlink("/home/automusic.win/public_html/public/check_claim/$campaignId.mp4");
            $png = "/home/automusic.win/public_html/public/check_claim/$campaignId.png";
            $mp3 = "/home/automusic.win/public_html/public/check_claim/$campaignId.mp3";
            if (count(glob($png)) > 0) {
                unlink($png);
            }
            if (count(glob($mp3)) > 0) {
                unlink($mp3);
            }
        }
        //thông báo
        if (isset($request->us)) {
            $user = User::where("user_name", $request->us)->first();
            $data = (object) [
                        "type" => 4,
                        "job_id" => $campaignId,
                        "title" => "Check Claim",
                        "message" => $message,
                        "comment" => null,
                        "redirect" => "/claim?filter_id=$campaignId",
                        "noti_id" => uniqid()
            ];
            event(new ChatEvent($user, [$request->us], $data));
            $notify = new Notification();
            $notify->platform = "automusic";
            $notify->group = "start_campaign";
            $notify->email = null;
            $notify->noti_id = $request->id;
            $notify->role = 20;
            $notify->create_time = Utils::timeToStringGmT7(time());
            $notify->type = 'notify';
            $notify->action_type = "read";
            $notify->action = "/claim?filter_id=$campaignId";
            $notify->content = $message;
            $notify->username = $request->us;
            $notify->start_date = time();
            $notify->end_date = time() + 30 * 86400;
            $notify->save();
        }

        return 1;
    }

    //cập nhật trạng thái comment 
    public function callbackChannelCommment(Request $request) {
        Logger::logUpload('CallbackController.callbackChannelCommment|request=' . json_encode($request->all()));
        $count = AccountInfo::where("id", $request->studio_id)->update(["status_cmt" => $request->status]);

        if ($count == 1) {
            return response()->json(["status" => "success"]);
        }
        return response()->json(["status" => "error"]);
    }

}
