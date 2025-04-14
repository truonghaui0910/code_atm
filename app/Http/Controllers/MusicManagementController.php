<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\MusicConfig;
use App\Http\Models\AccountInfo;
use Illuminate\Support\Facades\DB;
use Log;

class MusicManagementController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|MusicManagementController.index|request=' . json_encode($request->all()));
        $datas = MusicConfig::where('user_name', $user->user_name);
        $queries = [];

        $limit = 10;
        if (isset($request->limit)) {
            if ($request->limit <= 200 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }

//        if (isset($request->cName)) {
//            $datas = $datas->where('channel_name', 'like', '%' . $request->cName . '%');
//            $queries['cName'] = $request->cName;
//        }
//
//        if (isset($request->cha)) {
//            if ($request->cha != '-1') {
//                $datas = $datas->where('channel_id', $request->cha);
//                $queries['cha'] = $request->cha;
//            }
//        }
        if (isset($request->c1) && $request->c1 != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('channel_name', 'like', '%' . $request->c1 . '%')->orWhere('channel_id', 'like', '%' . $request->c1 . '%');
            });
            $queries['c1'] = $request->c1;
        }
        if (isset($request->c2)) {
            if ($request->c2 != '-1') {
                $datas = $datas->where('status', $request->c2);
                $queries['c2'] = $request->c2;
            }
        }

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo id asc
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);
        foreach ($datas as $data) {
            $accountInfo = AccountInfo::where('chanel_id', $data->channel_id)->first();
            if (isset($accountInfo)) {
                $data->note = $accountInfo->note;
                $data->group_channel_id = $accountInfo->group_channel_id;
            }
//            Log::info($accountInfo);
        }
//        Log::info(json_encode($datas));
//        list($lstChannel, $countData) = $this->loadChannelForSearchReup($request);
        $status = $this->genStatusMusic($request);
        $limitSelectbox = $this->genLimit($request);
//        Log::info(DB::getQueryLog());
        return view('components.musicmanagement', ['datas' => $datas,
//            'list_channel' => $lstChannel,
//            'list_channel_size' => $countData,
            'request' => $request,
            'status' => $status,
            'limitSelectbox' => $limitSelectbox,
            'limit' => $limit]);
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        //
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

    public function ajaxGetLog(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|MusicManagementController.ajaxGetLog|id=' . json_encode($request->all()));
        $music = MusicConfig::find($request->id);
//        Log::info(json_encode($music));
        if (!$music) {
            return array('status' => 'dannger', 'message' => 'Id not match');
        }
        if ($music->user_name != $user->user_name) {
            return array('status' => 'dannger', 'message' => 'You are not allowed to view this id');
        }
        if ($music->log == null || $music->log == '') {
            return array('status' => 'dannger', 'message' => 'No log to view');
        }
        $arrlog = json_decode($music->log);
        Log::info($music->log);
        return array('status' => 'success', 'message' => $arrlog);
    }

}
