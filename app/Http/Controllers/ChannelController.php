<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Models\Channel30;
use App\Http\Models\Channel5;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Log;

class ChannelController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index5(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|index5|request=' . json_encode($request->all()));
        $datas = Channel5::where('del_status', 0);
        $queries = [];
        $limit = 10;
        if (isset($request->limit)) {
            if ($request->limit <= 200 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->status) && $request->status != '-1') {
            $datas = $datas->where('status', $request->status);
            $queries['status'] = $request->status;
        }

        if (isset($request->channel_name) && $request->channel_name != '') {
            $datas = $datas->where('channel_name', 'like', '%' . $request->channel_name . '%');
            $queries['channel_name'] = $request->channel_name;
        }
        if (isset($request->txt_sub) && $request->txt_sub != '') {
            if (isset($request->cbb_sub)) {
                $queries['cbb_sub'] = $request->cbb_sub;
                $queries['txt_sub'] = $request->txt_sub;
                $datas = $datas->where('subscribes', $request->cbb_sub, $request->txt_sub);
            }
        }
        if (isset($request->txt_view) && $request->txt_view != '') {
            if (isset($request->cbb_view)) {
                $queries['cbb_view'] = $request->cbb_view;
                $queries['txt_view'] = $request->txt_view;
                $datas = $datas->where('views', $request->cbb_view, $request->txt_view);
            }
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'status';
            $request['order'] = 'desc';
            $queries['sort'] = 'status';
            $queries['order'] = 'desc';
        }

        $datas = $datas->sortable()->paginate($limit)->appends($queries);
//        Log::info($datas);
        return view('components.channel5', ['datas' => $datas,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request)
        ]);
    }

    public function index30(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|index30|request=' . json_encode($request->all()));
        $datas = Channel30::whereRaw('1=1');
        $queries = [];
        $limit = 10;
        if (isset($request->limit)) {
            if ($request->limit <= 200 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->status) && $request->status != '-1') {
            $datas = $datas->where('status', $request->status);
            $queries['status'] = $request->status;
        }

        if (isset($request->channel_name) && $request->channel_name != '') {
            $datas = $datas->where('channel_name', 'like', '%' . $request->channel_name . '%');
            $queries['channel_name'] = $request->channel_name;
        }
        if (isset($request->txt_sub) && $request->txt_sub != '') {
            if (isset($request->cbb_sub)) {
                $queries['cbb_sub'] = $request->cbb_sub;
                $queries['txt_sub'] = $request->txt_sub;
                $datas = $datas->where('subscribes', $request->cbb_sub, $request->txt_sub);
            }
        }
        if (isset($request->txt_view) && $request->txt_view != '') {
            if (isset($request->cbb_view)) {
                $queries['cbb_view'] = $request->cbb_view;
                $queries['txt_view'] = $request->txt_view;
                $datas = $datas->where('views', $request->cbb_view, $request->txt_view);
            }
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'status';
            $request['order'] = 'desc';
            $queries['sort'] = 'status';
            $queries['order'] = 'desc';
        }

        $datas = $datas->sortable()->paginate($limit)->appends($queries);
//        Log::info($datas);
        return view('components.channel30', ['datas' => $datas,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request)
        ]);
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ChannelController->store|request=' . json_encode($request->all()));
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

    public function show($id) {
        //
    }

    public function edit($id) {
        //
    }

    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        //
    }

    public function download30(Request $request) {
        Logger:info("download30");
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        try {
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=channel30_'.date('Ymd').'.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];

            $list = Channel30::get(['user_name','channel_id'])->toArray();
            $title = array('Team','Channel ID');
//            array_unshift($list, array_keys($title));
            array_unshift($list, $title);

            $callback = function() use ($list) {
                $FH = fopen('php://output', 'w');
                foreach ($list as $row) {
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    public function download5(Request $request) {
        Logger:info("download5");
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        try {
            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=channel5_'.date('Ymd').'.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];

            $list = Channel5::get(['user_name','channel_id'])->toArray();
            $title = array('Team','Channel ID');
//            array_unshift($list, array_keys($title));
            array_unshift($list, $title);

            $callback = function() use ($list) {
                $FH = fopen('php://output', 'w');
                foreach ($list as $row) {
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

}
