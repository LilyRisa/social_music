<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\category;
use Faker\Factory;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('vi_VN');

        for($i=0; $i < 10; $i++){
            $category = new category();
            $category->category = $faker->text(12);
            $category->description = $faker->realText($maxNbChars = 200, $indexSize = 2);
            $category->thumb = $faker->url;
            $category->save();
        }

        for($i=0; $i < 30; $i++){
            $category = new category();
            $category->parent_id = category::whereNull('parent_id')->get()->random()->id;
            $category->category = $faker->text(12);
            $category->description = $faker->realText($maxNbChars = 200, $indexSize = 2);
            $category->thumb = $faker->url;
            $category->save();
        }
    }
}
