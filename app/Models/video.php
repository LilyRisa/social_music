<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\post;

class video extends Model
{
    use HasFactory;

    protected $appends = ['url'];

    public function posts()
    {
        return $this->hasMany(post::class);
    }

    public function getUrlAttribute(){
        return $this->attributes['url'] = url('/').'/media/'.$this->attributes['slug'];
    }
}
