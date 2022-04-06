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
use Illuminate\Support\Facades\Hash;
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
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 200);
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
            'status' => true,
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
                	'status' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'status' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }

 		//Token created, return with success response and jwt token
        return response()->json([
            'status' => true,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'token' => $token,
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => true,
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
                'status' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'status' => false,
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

    public function unfollow($username){
        return response()->json(['status' => ActionUser::unfollow($username)]);
    }

    public function update_profile(Request $request){

        try{
            $user = JWTAuth::user();
            $user = User::find($user->id);
            $user->full_name = $request->input('full_name') != null ? $request->input('full_name') : null;
            $user->address = $request->input('address') != null ? $request->input('address') : null;
            $user->birthday = $request->input('birthday') != null ? $request->input('birthday') : null;
            $user->phone = $request->input('phone') != null ? $request->input('phone') : null;
            if($request->input('password') != null){
                if($request->input('password') == $request->input('password_confirm')){
                    $user->password = Hash::make($request->input('password'));
                }
            }
            $user->save();
            return response()->json(['status' => true]);
        }catch(Exception $e){
            return response()->json(['status' => false, 'message' => $e]);
        }

        
    }
}
