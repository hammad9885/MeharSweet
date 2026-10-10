<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('groups')->insert([
            ['title' => 'Capital','group_code' => '01'],
            ['title' => 'Liabilities','group_code' => '02'],
            ['title' => 'Assets','group_code' => '03'],
            ['title' => 'Revenue','group_code' => '04'],
            ['title' => 'Expenses','group_code' => '05'],
        ]);
    }
}
