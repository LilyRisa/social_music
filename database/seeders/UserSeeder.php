<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('vi_VN');

        for($i=0; $i < 30; $i++){
            $user = new User();
            $user->user_name = $faker->regexify('[A-Za-z0-9]{6}').uniqid();
            $user->email = $faker->email;
            $user->full_name = $faker->name;
            $user->address = $faker->address;
            $user->birthday = $faker->date($format = 'Y-m-d', $max = 'now');
            $user->phone = $faker->phoneNumber;
            $user->password = Hash::make($faker->password);
            $user->save();
        }
    }
}
