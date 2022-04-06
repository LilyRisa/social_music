<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;

class CategoryController extends Controller
{
    public function get(){
        $cate = category::whereNull('parent_id')->get();
        return response()->json(['count' => $cate->count(),'data' => $cate]);
    }
}
