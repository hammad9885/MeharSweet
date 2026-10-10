<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sale_order_master', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_name_id')->nullable()->after('party_ref');
            $table->foreign('ref_name_id')->references('id')->on('reference_names')->onDelete('set null');
        });

        Schema::table('sale_master', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_name_id')->nullable()->after('dc_vno');
            $table->foreign('ref_name_id')->references('id')->on('reference_names')->onDelete('set null');
        });

        Schema::table('delivery_challan_master', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_name_id')->nullable()->after('ref_no');
            $table->foreign('ref_name_id')->references('id')->on('reference_names')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('delivery_challan_master', function (Blueprint $table) {
            $table->dropForeign(['ref_name_id']);
            $table->dropColumn('ref_name_id');
        });

        Schema::table('sale_master', function (Blueprint $table) {
            $table->dropForeign(['ref_name_id']);
            $table->dropColumn('ref_name_id');
        });

        Schema::table('sale_order_master', function (Blueprint $table) {
            $table->dropForeign(['ref_name_id']);
            $table->dropColumn('ref_name_id');
        });
    }
};
