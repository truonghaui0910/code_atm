<?php

namespace App\Http\Middleware;

use App\Http\Models\Notification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function redirect;
use function view;
use Log;

class AdminLoginMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $currenturl = $request->getUri();
        if (Auth::check()) {
            $user = Auth::user();
            view()->share("user_login", Auth::user());
            $is_admin_music = 0;
            $is_supper_admin = 0;
            $is_admin_calendar = 0;
            if (in_array('1', explode(",", $user->role)) || in_array('20', explode(",", $user->role))) {
                $is_admin_music = 1;
            }
            if(in_array('1', explode(",", $user->role))){
                $is_supper_admin = 1;
            }
            if(in_array('27', explode(",", $user->role))){
                $is_admin_calendar = 1;
            }
            view()->share("is_admin_music", $is_admin_music);
            view()->share("is_supper_admin", $is_supper_admin);
            view()->share("is_admin_calendar", $is_admin_calendar);
            $request['is_supper_admin'] = $is_supper_admin;
            $request['is_admin_music'] = $is_admin_music;
            $request['is_admin_calendar'] = $is_admin_calendar;
//            DB::enableQueryLog();
//            $notify = Notification::where('status', 1)->where('start_date', '<', time())->where('end_date', '>', time())->where('user_id', Auth::user()->id)->orderBy('start_date', 'desc')->limit(5)->get();
//            $notifyAll = Notification::where('status', 1)->where('start_date', '<', time())->where('end_date', '>', time())->where('type', 1)->get();
//            view()->share("notify", $notify);
//            view()->share("notifyAll", $notifyAll);
//            Log::info(DB::getQueryLog());
            if ($user->status == 0) {
                Auth::logout();
                return redirect('login')->with("message", 'Your account has been locked');
            }
            if (in_array('11', explode(",", Auth::user()->role)) || in_array('20', explode(",", Auth::user()->role)) || in_array('21', explode(",", Auth::user()->role)) || in_array('1', explode(",", Auth::user()->role))) {
            $user->last_activity = time();
            $user->save();
            return $next($request);
            }
        } else {
            return redirect('login')->with(['redirect' =>  $currenturl]);
        }
    }

}
