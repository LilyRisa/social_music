<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\post;

class category extends Model
{
    use HasFactory;

    public function children(){
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id' );
    }
    public function Post(){
        return $this->belongsToMany(post::class, 'category_post', 'category_id', 'post_id');
    }
    public function Post_primary(){
        return $this->hasMany(post::class);
    }
}
