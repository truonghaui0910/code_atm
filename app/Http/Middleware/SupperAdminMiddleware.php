<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function redirect;

class SupperAdminMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $user = Auth::user();
        $arrayRole =  explode(",", $user->role);
        if (in_array('1', $arrayRole)) {
            $user->last_activity = time();
            $user->save();
            return $next($request);
        } else {
            return redirect('login');
        }
    }

}
