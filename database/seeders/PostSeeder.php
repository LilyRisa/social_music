<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\post;
use App\Models\category;
use App\Models\video;
use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('vi_VN');
        for($i=0; $i < random_int(10,10); $i++){
            $post = new post();
            $post->category_primary_id = category::all()->random()->id;
            $post->user_id = User::all()->random()->id;
            $post->title = $faker->text($maxNbChars = 200);
            $post->keyword = implode(',',$faker->words($nb = random_int(3,10), $asText = false));
            $post->description = $faker->text($maxNbChars = 200);
            $post->thumb = $faker->url;
            $post->type_asset = 'music';
            $post->content = $faker->randomHtml(4,10);
            $post->asset_id = video::all()->random()->id;
            $post->save();
        }
    }
}
