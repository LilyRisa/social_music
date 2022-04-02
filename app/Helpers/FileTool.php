<?php

namespace App\Helpers;
use Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image;
class FileTool{
    public static function sluggify($url)
    {
        # Prep string with some basic normalization
        $url = strtolower($url);
        $url = strip_tags($url);
        $url = stripslashes($url);
        $url = html_entity_decode($url);
    
        # Remove quotes (can't, etc.)
        $url = str_replace('\'', '', $url);
    
        # Replace non-alpha numeric with hyphens
        $match = '/[^a-z0-9]+/';
        $replace = '-';
        $url = preg_replace($match, $replace, $url);
    
        $url = trim($url, '-');
    
        return $url;
    }

    public static function getfile($filename, $types, $range){
        $file = explode('/', $filename);
        $size = Storage::disk('local')->size('public/files/'.$file[2]);
        $file = Storage::disk('local')->get('public/files/'.$file[2]);
        $path = Storage::path('public/files');
        $stream = fopen(base_path().'/storage/app/'.$filename, "r");
        if($types == 'audio'){
            $type = 'audio/mp3';
        }else{
            $type = 'video/mp4';
        }
        
        $start = 0;
        $length = $size;
        $status = 200;

        $headers = ['Content-Type' => $type, 'Content-Length' => $size, 'Accept-Ranges' => 'bytes'];

        if (false !== $range) {
            list($param, $range) = explode('=', $range);
            if (strtolower(trim($param)) !== 'bytes') {
            header('HTTP/1.1 400 Invalid Request');
            exit;
            }
            list($from, $to) = explode('-', $range);
            if ($from === '') {
            $end = $size - 1;
            $start = $end - intval($from);
            } elseif ($to === '') {
            $start = intval($from);
            $end = $size - 1;
            } else {
            $start = intval($from);
            $end = intval($to);
            }
            $length = $end - $start + 1;
            $status = 206;
            $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $size);
        }

        return response()->stream(function() use ($stream, $start, $length) {
            fseek($stream, $start, SEEK_SET);
            echo fread($stream, $length);
            fclose($stream);
            }, $status, $headers);
    }

    public function getImg($filename){
        $file = Storage::disk('local')->get('public/images/'.$filename);
        $path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path .= 'public/images/'.$filename;
        $ext = explode('.', $filename);
        $ext = end($ext);
        $img = \Image::make($file);
    	return response()->make($img->encode($img->mime()), 200, array('Content-Type' => $img->mime(),'Cache-Control'=>'max-age=86400, public'));
        return response()->file($path,['Content-type' => 'image/jpeg']);

    }
}