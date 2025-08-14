<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call the AdminSeeder
        $this->call(AdminSeeder::class);
    }
}