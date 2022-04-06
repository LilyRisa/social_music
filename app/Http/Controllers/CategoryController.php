<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;

class CategoryController extends Controller
{
    public function get(){
        if(isset($_GET['ls'])){
            if($_GET['ls'] == 'parent'){
                $cate = category::whereNull('parent_id')->get();
                return response()->json(['count' => $cate->count(),'data' => $cate]);
            }

            if($_GET['ls'] == 'child'){
                $cate = category::whereNotNull('parent_id')->get();
                return response()->json(['count' => $cate->count(),'data' => $cate]);
            }

            return response()->json(['status' =>false, 'message' => 'Incorrect parameter'], 200);
        }
        $arr =[];
        $cate = category::all();
        return response()->json(['count' => $cate->count(),'data' => $cate]);

    }
}
