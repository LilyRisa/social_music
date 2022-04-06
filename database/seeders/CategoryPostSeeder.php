<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\category;
use App\Models\post;
use Illuminate\Support\Facades\DB;

class CategoryPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0; $i < random_int(20,30); $i++){
            DB::insert('INSERT INTO category_post(category_id, post_id) values (? , ?)', [2,post::all()->random()->id]);
        }
    }
}
