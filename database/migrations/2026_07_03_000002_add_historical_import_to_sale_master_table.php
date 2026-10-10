<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_master', function (Blueprint $table) {
            $table->boolean('historical_import')->default(false)->after('dc_vno');
        });
    }

    public function down(): void
    {
        Schema::table('sale_master', function (Blueprint $table) {
            $table->dropColumn('historical_import');
        });
    }
};
