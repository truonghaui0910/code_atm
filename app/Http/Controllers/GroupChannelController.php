<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use \App\Http\Models\GroupChannel;
use App\Http\Models\AccountInfo;
use Log;

class GroupChannelController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|GroupChannelController->store|request=' . json_encode($request->all()));
        $result = "";
        $countSuccess = 0;
        $countFail = 0;
        if (isset($request->channel)) {
            $txtSource = str_replace(array("\r\n", "\n"), "@,@", $request->channel);
            $split = explode("@,@", $txtSource);
            $result = count($split);
            for ($i = 0; $i < count($split); $i++) {
//                Log::info($split[$i]);
                $datas = explode(",", $split[$i]);
                $countData = count($datas);
                $channelId = "";
                $channelName = "";
                $email = "";
                if ($countData < 2) {
                    $countFail++;
                    continue;
                } else {
                    if ($countData == 2) {
                        $channelId = $datas[0];
                        $channelName = $datas[1];
                    } else if ($countData == 3) {
                        $channelId = $datas[0];
                        $channelName = $datas[1];
                        $email = $datas[2];
                    }
                    try {
                        $channel = new Channel();
                        $channel->channel_id = trim($channelId);
                        $channel->channel_name = trim($channelName);
                        $channel->email = trim($email);
                        $channel->save();
                        $countSuccess ++;
                    } catch (QueryException $ex) {
                        Log::info("Error " . $ex->getMessage());
                        $countFail++;
                        if ($countSuccess > 0) {
                            $countSuccess--;
                        }
                    }
                }
            }
        }
        return array("Import success $countSuccess, fail $countFail");
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|GroupChannelController->update|request=' . json_encode($request->all()));
        $data = GroupChannel::where("user_name", $user->user_name)->where("id", $request->id);
        if (isset($request->group_name)) {
            $data = $data->update(["group_name" => trim($request->group_name)]);
            return response()->json($data);
        }
        //danh sách hiển thị của admin rất nhiều => update sẽ làm sai thứ tự
        if (!$request->is_admin_music) {
            if (isset($request->order)) {
                $order = $request->order;
                foreach ($order as $index => $id) {
                    GroupChannel::where('id', $id)->update(['order_id' => $index + 1]);
                }
                return 1;
            }
        }
    }

    public function ajaxGetGroupChannel(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|GroupChannelController->ajaxGetGroupChannel|request=' . json_encode($request->all()));
        $datas = GroupChannel::where('user_name', $user->user_name)->get();
//        Log::info(json_encode($datas));
        return $datas;
    }

    public function ajaxAddGroupChannel(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|postAddGroupChannel|request=' . json_encode($request->all()));
        $status = "error";
        $content = array();
        $dataInserted = array();
        try {

            if (!isset($request['groupName']) || $request['groupName'] == null || $request['groupName'] == '') {
                array_push($content, "Group name is empty");
            }

            if (mb_strlen(trim($request['groupName']), 'UTF-8') > 50) {
                array_push($content, "Group Name maximum length is 50 characters");
            }

            $isUpdate = 0;
            $checkExists = GroupChannel::where('user_name', $user->user_name)->where('group_name', $request['groupName'])->get();
            if (count($checkExists) != 0) {
                array_push($content, "Dupticate group channel " . $request['groupName']);
            }
            //nếu đã tồn tại ID thì là update
            if (isset($request['id'])) {
                $checkUpdate = GroupChannel::where('id', $request['id'])->where('user_name', $user->user_name)->first();
                if (isset($checkUpdate)) {
                    $isUpdate = 1;
                }
            }
            $groupResult = [];
            if (count($content) != 0) {
                $status = "error";
            } else {
                $status = "success";
                if ($isUpdate == 1) {
                    $groupChannel = $checkUpdate;
                    $groupChannel->group_name = trim($request['groupName']);
                    $groupChannel->save();
                    Log::info('UPDATE ' . $groupChannel);
                } else {
                    $listData = explode(',', $request['groupName']);
                    
                    foreach ($listData as $data) {
                        $groupChannel = new GroupChannel();
                        $groupChannel->order_id = 1;
                        $groupChannel->user_name = $user->user_name;
                        $groupChannel->user_code = $user->user_code;
                        $groupChannel->group_name = trim($data);
                        $groupChannel->save();
                        $groupResult[] = $groupChannel;
                    }
                }
                array_push($content, "Success");
            }
        } catch (\Exception $exc) {
            Log::info($exc->getTraceAsString());
            $status = "error";
            array_push($content, trans('label.message.error'));
        }
        return array('status' => $status, 'content' => $content, "groups" => $groupResult);
    }

    public function ajaxDelGroupChannel(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|GroupChannelController->ajaxDelGroupChannel|request=' . json_encode($request->all()));
        $status = "error";
        $content = array();
        try {
            if (!isset($request->id)) {
                array_push($content, "Not found ID");
                return array('status' => $status, 'content' => $content);
            }
            if ($request->is_admin_music) {
                $data = GroupChannel::where("id", $request->id)->first();
            } else {
                $data = GroupChannel::where('user_name', $user->user_name)->where("id", $request->id)->first();
            }
            if (!$data) {
                array_push($content, "Not found this group channel");
                return array('status' => $status, 'content' => $content);
            }
            if (count($content) == 0) {
                $reult = AccountInfo::where('group_channel_id', $request->id)->update(['group_channel_id' => 0]);
                $data->delete();
                array_push($content, "Success");
                return array('status' => "success", 'content' => $content);
            }
        } catch (QueryException $ex) {
            array_push($content, $ex->getMessage());
            return array('status' => $status, 'content' => $content);
        }
    }

    public function ajaxListGroupChannel(Request $request) {
        $user = Auth::user();
        if ($request->is_admin_music) {
            $datas = GroupChannel::all();
        } else {
            $datas = GroupChannel::whereRaw("user_name = '$user->user_name' or user_name = ''")->orderByRaw('ISNULL(order_id), order_id asc')->get();
        }
        $grId = [];
        foreach ($datas as $data) {
            $grId[] = $data->id;
        }
        $groups = AccountInfo::select('group_channel_id', DB::raw('COALESCE(COUNT(*), 0) as channels'))->whereIn("group_channel_id", $grId)->where("del_status", 0)->groupBy('group_channel_id')->get();
        foreach ($datas as $data) {
            $data->channels = 0;
            foreach ($groups as $group) {
                if ($group->group_channel_id == $data->id) {
                    $data->channels = $group->channels;
                    break;
                }
            }
        }
        return response()->json($datas);
    }

}
