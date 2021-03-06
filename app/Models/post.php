<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\category;
use App\Traits\ImageUpload;

class post extends Model
{
    use HasFactory,ImageUpload;

    protected $appends = ['thumb_url'];

    protected $fillable = [
        'category_primary_id ', 'user_id', 'title', 'keyword', 'description', 'type_asset', 'content', 'asset_id', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Category_primary(){
        return $this->belongsTo(category::class);
    }

    public function Category(){
        return $this->belongsToMany(category::class, 'category_post', 'post_id', 'category_id');
    }
    public function video()
    {
        return $this->belongsTo(video::class,'asset_id');
    }
    public function getThumbUrlAttribute(){
        return $this->attributes['thumb_url'] = $this->getImageUrl($this->attributes['thumb']);
    }
}
