<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use \App\User;

class TokenMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $token = $request->header('aux');
        if (!isset($token)) {
            return response()->json(['message' => 'Token expired'], 403);
        }
        $is_admin_music = 0;
        $user = User::where("token", $token)->where("expire_token", ">", time())->first();
        if ($user) {
            if (in_array('1', explode(",", $user->role)) || in_array('20', explode(",", $user->role))) {
                $is_admin_music = 1;
            }
            $request['is_admin_music'] = $is_admin_music;
            $request["username"] = $user->user_name;
            $request["usercode"] = $user->user_code;
            return $next($request);
        } else {
            return response()->json(['message' => 'Token expired'], 403);
        }
    }

}
