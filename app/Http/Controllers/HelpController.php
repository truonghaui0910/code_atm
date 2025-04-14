<?php

namespace App\Http\Controllers;

use App\Http\Models\ApiManager;
use Log;

class HelpController extends Controller {

    public function help() {
        return view('components.help', []);
    }

    public function redirectAddApi() {
        $data = ApiManager::where("status", 1)->where("type", ">=", 5)->orderBy("count")->first(["net","count"]);
        Log::info(json_encode($data));
        return $data;
        
    }

}
