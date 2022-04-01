<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use App\Models\video;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helpers\ActionUser;
use App\Helpers\FileTool;


class UserApiController extends Controller
{
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('user_name', 'full_name', 'email', 'birthday', 'password');
        $validator = Validator::make($data, [
            'user_name' => 'required|string|unique:users',
            'full_name' => 'required|string',
            'birthday' => 'required|date',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
        	'user_name' => $request->user_name,
        	'full_name' => $request->full_name,
        	'birthday' => $request->birthday,
        	'email' => $request->email,
        	'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }

 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'token' => $token,
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'success' => true,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'token' => JWTAuth::refresh(),
        ]);
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 200);
        }

		//Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {

        $user = JWTAuth::user();

        return response()->json(['user' => $user]);
    }

    public function follow($username){
        $follow = ActionUser::user_follow($username);
        return response()->json(['status' => $follow]);
    }

    public function list_follow(){
        return response()->json(['count' => (ActionUser::list_follow(1))->count(),'data' => ActionUser::list_follow(1)]);
    }

    public function follow_me(){
        return response()->json(['count' => (ActionUser::list_follow(0))->count(), 'data' => ActionUser::list_follow(0)]);
    }

    public function upload(Request $request){


        if(!empty($request->file('file')->getClientOriginalExtension()) && ($request->file('file')->getClientOriginalExtension() == 'mp3' || $request->file('file')->getClientOriginalExtension() == 'ogg' || $request->file('file')->getClientOriginalExtension() == 'wav')){
            $type = 'audio';
        }else if($request->file('file')->getClientOriginalExtension() == 'avi' || $request->file('file')->getClientOriginalExtension() == 'flv' || $request->file('file')->getClientOriginalExtension() == 'mp4'){
            $type =  'video';
        }else{
            return response()->json(['status' => false]);
        }
        $slug = FileTool::sluggify($request->file('file')->getClientOriginalName()).time().uniqid(rand());
        $path = $request->file('file')->store('public/files');

        $save = new video;
        $save->slug = $slug;
        $save->asset = $path;
        $save->type = $type;
        $save->save();
        $data = video::find($save->id);
        $data->slug = url('/').'/media/'.$data->slug;
        return response()->json(['data' => $data]);
    }
}
