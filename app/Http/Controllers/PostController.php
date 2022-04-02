<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\post;
use App\Models\User;


class PostController extends Controller
{
    public function list_post(){
        if(isset($_GET['username'])){
            $user = User::where('user_name', $_GET['username'])->first();
            if($user == null){
                return response()->json(['status' => false,'messenges' => 'Username not found !']);
            }
            $post = post::with(['user', 'Category_primary', 'Category', 'video'])->where('user_id', $user->id)->get();
            return response()->json(['count' => $post->count(),'data' => $post]);
        }
        $post = post::with(['user', 'Category_primary', 'Category', 'video'])->where('user_id', (JWTAuth::user())->id)->get();
        return response()->json(['count' => $post->count(),'data' => $post]);
    }

    public function post(Request $request){

        $data = $request->all();

        $validator = Validator::make($data, [
            'category_primary_id' => 'required|integer',
            'title' => 'required|string',
            'keyword' => 'required|string',
            'thumb' => 'required|string',
            'type_asset' => 'required|string'

        ]);
        $data['user_id'] = (JWTAuth::user())->id;
        $data['category_primary_id'] = (int) $data['category_primary_id'];
        if ($validator->fails()) {
            return response()->json(['status' =>false, 'message' => $validator->errors()->first()], 200);
        }
        try{
            $post = new post();
            $post->category_primary_id = $data['category_primary_id'];
            $post->user_id = $data['user_id'];
            $post->title = $data['title'];
            $post->keyword = $data['keyword'];
            $post->description = isset($data['description']) ? $data['description'] : null;
            $post->thumb = $data['thumb'];
            $post->type_asset = $data['type_asset'];
            $post->content = isset($data['content']) ? $data['content'] : null;
            $post->asset_id  = isset($data['asset_id']) ? $data['asset_id'] : null;
            $post->save();

            if(isset($data['category'])){
                foreach($data['category'] as $item){
                    $post->Category()->attach($item);
                }
            }
            return response()->json(['status' => true, 'message' => 'successfully!']);
        }catch(Exception $e){
            return response()->json(['status' => false, 'message' => $e]);
        }
    }

    public function update(Request $request, $id){
        $post = post::with('user')->where('id', $id)->first();
        if($post->user->id != (JWTAuth::user())->id){
            return response()->json(['status' => false, 'message' => 'you have no authority!']);
        }
        try{
            $post->title = $request->input('title') != null ? $request->input('title') : $post->title;
            $post->keyword = $request->input('keyword') != null ? $request->input('keyword') : $post->keyword;
            $post->description = $request->input('description') != null ? $request->input('description') : $post->description;
            $post->thumb = $request->input('thumb') != null ? $request->input('thumb') : $post->thumb;
            $post->content = $request->input('content') != null ? $request->input('content') : $post->content;
            $post->save();
            return response()->json(['status' => true]);
        }catch(Exception $e){
            return response()->json(['status' => false, 'message' => $e]);
        }
        
    }
}
