<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumption_masters', function (Blueprint $table) {
            $table->integer('qty_per_carton')->nullable()->after('lot_qty');
            $table->integer('cartons_in_lot')->nullable()->after('qty_per_carton');
        });
    }

    public function down(): void
    {
        Schema::table('consumption_masters', function (Blueprint $table) {
            $table->dropColumn(['qty_per_carton', 'cartons_in_lot']);
        });
    }
};
