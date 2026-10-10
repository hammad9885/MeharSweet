<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_in_masters', function (Blueprint $table) {
            $table->string('dala_no')->nullable()->after('ref_name_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_in_masters', function (Blueprint $table) {
            $table->dropColumn('dala_no');
        });
    }
};
