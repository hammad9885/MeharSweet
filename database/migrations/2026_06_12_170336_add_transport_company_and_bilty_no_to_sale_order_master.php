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
        Schema::table('sale_order_master', function (Blueprint $table) {
            $table->string('transport_company', 255)->nullable()->after('party_ref');
            $table->string('bilty_no', 255)->nullable()->after('transport_company');
        });
    }

    public function down(): void
    {
        Schema::table('sale_order_master', function (Blueprint $table) {
            $table->dropColumn(['transport_company', 'bilty_no']);
        });
    }
};
