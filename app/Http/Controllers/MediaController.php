<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\video;
use App\Traits\ImageUpload;
use App\Helpers\FileTool;

class MediaController extends Controller
{
    //
    use ImageUpload;

    public function get($slug, Request $request){
        $video = video::where('slug', $slug)->first();
        if(!$video){
            return response()->json(['message' => 'Media Not Found'], 404);
        }
        return FileTool::getfile($video->asset, $video->type, $request->server('HTTP_RANGE', false));
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
        return response()->json(['data' => $data]);
    }

    public function upload_img(Request $request){
        // $data = $request->file('file');
        $data = $request->all();
        $validator = Validator::make($data, [
              'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' =>false, 'message' => $validator->errors()->first()], 200);
        }
        $filePath = $this->UserImageUpload($request->file('file'));
        return response()->json(['data' => $filePath]);
    }

    public function images($slug){
        return FileTool::getImg($slug);
    }
}
