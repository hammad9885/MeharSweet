<?php

namespace Database\Seeders;

use App\Models\Level1;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Level1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level1::create([
            'title' => 'hj',
            'group_id' => '2',
            'level1_code' => '001',  // Ensure that this value is being passed
        ]);
    }
}
