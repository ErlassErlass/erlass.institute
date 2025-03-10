<?php

// database/seeders/SekolahSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\SekolahFactory;

class SekolahSeeder extends Seeder
{
    public function run()
    {
        // Generate 10 sekolah records
        Sekolah::factory()->count(10)->create();
    }
}