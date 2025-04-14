<?php

namespace App\Http\Middleware;

use App\Http\Models\AccountInfo;
use App\Http\Models\Campaign;
use App\Http\Models\Tasks;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use function redirect;
use function view;

class Task {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = Auth::user();
        view()->share("user_login", $user);
        $curentDate = gmdate("d-m-Y", time() + (7 * 3600));
        $day = gmdate("D", time() + (7 * 3600));
        $except = ["Fri", "Sat", "Sun"];
        $message = array();
        if (in_array($day, $except) || in_array('20', explode(",", $user->role))) {
            return $next($request);
        }

        //kiểm tra xem đã update playlist và pincoment chưa.
        $checks = Tasks::where("date", $curentDate)->where("username", $user->user_name)->get();
        Log::info(json_encode($checks));
        if (count($checks) >= 2) {
            $count = 0;
            foreach ($checks as $check) {
                if ($check->content == "playlist" || $check->content == "comment") {
                    $count++;
                }
            }
//            if ($count < 2) {
//                array_push($message, "You haven't pin comment and update playlist");
//            }
        }


        //kiểm tra xem có promos nào chưa confirm ko
        $countPromo = Campaign::where("username", $user->user_name)->where("status_confirm", 2)->count();
        if ($countPromo > 0) {
            array_push($message, "you have $countPromo promo videos to confirm");
        }

        //kiểm tra xem có kênh nào chưa confirm
        $accounts = AccountInfo::where("user_name", $user->user_code)->where("is_sync", 3)->get();
        foreach ($accounts as $account) {
            array_push($message, "You have to complete the admin request before sync on channel <a target='__blank' href='http://automusic.win/channelmanagement?c1=$account->chanel_id&c3=-1&c6=-1&limit=10'>$account->chanel_name</a>");
        }

        if (count($message) == 0) {
            return $next($request);
        }
        return $next($request);
//        Log::info("Task $user->user_name " . json_encode($message));
//        return redirect()->secure('dashboard')->with("message", $message);
//        return redirect('dashboard')->with("message", $message);
    }

}
