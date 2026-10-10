<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Module::firstOrCreate(['name' => 'Stock In']);
    }

    public function down(): void
    {
        Module::where('name', 'Stock In')->delete();
    }
};