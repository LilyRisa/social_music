<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use App\Models\User;

class UserBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = JWTAuth::user();
        $user = User::find($user->id);
        // dd($user->banned);
        // dd(date("Y-m-d H:i:s"));
        $time_user = \DateTime::createFromFormat("Y-m-d H:i:s", $user->banned);
        $time_now = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        
        if($time_now <  $time_user){
            $time_compare = date_diff($time_user, $time_now);
            return response()->json(['status' => false, 'messenges' => 'Your account is banned, please try again after '.$time_compare->days.' day and '.$time_compare->h.' hour']);
        }
        return $next($request);
    }
}
