<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\video;

use App\Helpers\FileTool;

class MediaController extends Controller
{
    //

    public function get($slug, Request $request){
        $video = video::where('slug', $slug)->first();
        if(!$video){
            return response()->json(['message' => 'Media Not Found'], 404);
        }
        return FileTool::getfile($video->asset, $video->type, $request->server('HTTP_RANGE', false));
    }
}
