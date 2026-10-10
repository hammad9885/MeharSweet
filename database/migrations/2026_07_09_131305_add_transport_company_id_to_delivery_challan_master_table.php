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
        Schema::table('delivery_challan_master', function (Blueprint $table) {
            $table->unsignedBigInteger('transport_company_id')->nullable();
            $table->foreign('transport_company_id')->references('id')->on('transport_companies')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_challan_master', function (Blueprint $table) {
            $table->dropForeign(['transport_company_id']);
            $table->dropColumn('transport_company_id');
        });
    }
};
