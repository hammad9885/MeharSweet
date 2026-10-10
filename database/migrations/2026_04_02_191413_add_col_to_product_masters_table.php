<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('product_masters', 'unit_id')) {
                $table->string('unit_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_masters', function (Blueprint $table) {
            if (Schema::hasColumn('product_masters', 'unit_id')) {
                $table->dropColumn('unit_id');
            }
        });
    }
};
