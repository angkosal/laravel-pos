<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email'=>'admin@gmail.com',
            'password' => bcrypt('admin123')
        ]);
    }
}
