<?php
namespace App\Helpers;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
// use Exception;

class ActionUser{
    public static function user_follow($username)
    {
        $user = JWTAuth::user();
        if($username == $user->user_name){
            return false;
        }
        $target = User::where('user_name', $username)->first();
        if(!$target){
            return false;
        }
        try{
            DB::insert('insert into user_follow (user_curent, user_target) values (?, ?)', [$user->id, $target->id]);
            return true;
        }catch(Exception $e){
            return false;
        }
        
    }

    public static function list_follow($pre = 0){
        $user = JWTAuth::user();
        try{
            if($pre){
                $list_id = DB::table('user_follow')->where('user_curent', '=', $user->id)->get();
            }else{
                $list_id = DB::table('user_follow')->where('user_target', '=', $user->id)->get();
            }
            
            $arr = [];
            foreach($list_id as $item){
                if($pre){
                    $arr[] = $item->user_target;
                }else{
                    $arr[] = $item->user_curent;
                }
            }
            $list_user = User::whereIn('id', $arr)->get()->makeHidden(['id','email_verified_at','api_token','created_at','updated_at']);
            return $list_user;
        }catch(Exception $e){
            return false;
        }
    }

    public function unfollow($username){
        $user = User::where('user_name', $username)->first();
        $get = DB::table('user_follow')->where('user_curent', (JWTAuth::user())->id)->where('user_target', $user->id)->get();
        if(empty($get)){
            return false;
        }
        try{
            DB::table('user_follow')->where('user_curent', (JWTAuth::user())->id)->where('user_target', $user->id)->delete();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}