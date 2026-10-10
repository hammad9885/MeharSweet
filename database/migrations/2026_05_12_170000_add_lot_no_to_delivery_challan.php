<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_challan_detail', function (Blueprint $table) {
            $table->integer('lot_no')->nullable()->after('load_qty');
        });

        Schema::table('delivery_challan_master', function (Blueprint $table) {
            $table->integer('qty_per_carton')->nullable()->after('transport_company');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_challan_detail', function (Blueprint $table) {
            $table->dropColumn('lot_no');
        });

        Schema::table('delivery_challan_master', function (Blueprint $table) {
            $table->dropColumn('qty_per_carton');
        });
    }
};
