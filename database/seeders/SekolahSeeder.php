<?php

// database/seeders/SekolahSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\SekolahFactory;

class SekolahSeeder extends Seeder
{
public function run(): void
{
    \App\Models\Sekolah::factory()->count(30)->create();
}

}