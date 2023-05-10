<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [   
    ['name' => 'Afghanistan', 'country_code' => '93'],
    ['name' => 'Armenia', 'country_code' => '374'],
    ['name' => 'Azerbaijan', 'country_code' => '994'],
    ['name' => 'Bahrain', 'country_code' => '973'],
    ['name' => 'Bangladesh', 'country_code' => '880'],
    ['name' => 'Bhutan', 'country_code' => '975'],
    ['name' => 'Brunei', 'country_code' => '673'],
    ['name' => 'Cambodia', 'country_code' => '855'],
    ['name' => 'China', 'country_code' => '86'],
    ['name' => 'Cyprus', 'country_code' => '357'],
    ['name' => 'Georgia', 'country_code' => '995'],
    ['name' => 'India', 'country_code' => '91'],
    ['name' => 'Indonesia', 'country_code' => '62'],
    ['name' => 'Iran', 'country_code' => '98'],
    ['name' => 'Iraq', 'country_code' => '964'],
    ['name' => 'Israel', 'country_code' => '972'],
    ['name' => 'Japan', 'country_code' => '81'],
    ['name' => 'Jordan', 'country_code' => '962'],
    ['name' => 'Kazakhstan', 'country_code' => '7'],
    ['name' => 'Korea', 'country_code' => '82'],
    // Add more countries here...
];


        DB::table('countries')->insert($countries);
    }
}
