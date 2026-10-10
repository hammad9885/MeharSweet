<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DELETE FROM delivery_challan_detail WHERE sale_order_vno NOT IN (SELECT v_no FROM sale_order_master)");
        DB::statement("DELETE FROM delivery_challan_master WHERE v_no NOT IN (SELECT DISTINCT v_no FROM delivery_challan_detail)");

        Schema::table('delivery_challan_detail', function (Blueprint $table) {
            $table->foreign('sale_order_vno')
                  ->references('v_no')
                  ->on('sale_order_master')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_challan_detail', function (Blueprint $table) {
            $table->dropForeign(['sale_order_vno']);
        });
    }
};
