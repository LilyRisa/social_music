<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserFollow extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0; $i < 10; $i++){
            DB::insert('insert into user_follow (user_curent, user_target) values (?, ?)', [User::all()->random()->id, User::all()->random()->id]);
        }
    }
}
