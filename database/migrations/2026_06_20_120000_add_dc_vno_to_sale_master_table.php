<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_master', function (Blueprint $table) {
            $table->string('dc_vno')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sale_master', function (Blueprint $table) {
            $table->dropColumn('dc_vno');
        });
    }
};
