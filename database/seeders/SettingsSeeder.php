<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['key' => 'app_name', 'value' => 'Laravel-POS'],
            ['key' => 'currency_symbol', 'value' => '$'],
        ];

        foreach ($data as $value) {
            if(!Setting::where('key', $value['key'])->first()) {
                Setting::create($value);
            }
        }
    }
}
