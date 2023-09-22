<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [   
            ['name' => 'admin', 'email' => 'admin@gmail.com','password' => bcrypt('12345678'),'role' => 'admin'],
            // Add more countries here...
        ];
        DB::table('users')->insert($users);
    }
}
