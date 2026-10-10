<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private $modules = [
        'Transport Company',
        'Reference Name',
    ];

    public function up(): void
    {
        foreach ($this->modules as $name) {
            Module::firstOrCreate(['name' => $name]);
        }
    }

    public function down(): void
    {
        Module::whereIn('name', $this->modules)->delete();
    }
};