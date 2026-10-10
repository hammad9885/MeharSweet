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
        Schema::table('item_masters', function (Blueprint $table) {
            $columns = ['mold_id', 'min_qty', 'unit_qty', 'is_plastic'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('item_masters', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('item_masters', 'mold_id')) {
                $table->unsignedBigInteger('mold_id')->nullable();
            }
            if (!Schema::hasColumn('item_masters', 'min_qty')) {
                $table->integer('min_qty')->nullable();
            }
            if (!Schema::hasColumn('item_masters', 'unit_qty')) {
                $table->integer('unit_qty')->nullable();
            }
            if (!Schema::hasColumn('item_masters', 'is_plastic')) {
                $table->boolean('is_plastic')->default(false);
            }
        });
    }
};