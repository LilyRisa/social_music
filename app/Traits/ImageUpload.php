<?php
namespace App\Traits;
 
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
 
trait ImageUpload
{
    public function UserImageUpload($query) // Taking input image as parameter
    {
        $url = $query->store('public/images');
        $url = explode('/',$url);
        $url = end($url);
        return ['slug' => $url, 'url' => url('/').'/image/'.$url]; // Just return image
    }
}