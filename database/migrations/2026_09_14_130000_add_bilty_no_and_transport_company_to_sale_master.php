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
        Schema::table('sale_master', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_master', 'bilty_no')) {
                $table->string('bilty_no')->nullable()->after('dc_vno');
            }
            if (!Schema::hasColumn('sale_master', 'transport_company_id')) {
                $table->unsignedBigInteger('transport_company_id')->nullable()->after('bilty_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_master', function (Blueprint $table) {
            if (Schema::hasColumn('sale_master', 'transport_company_id')) {
                $table->dropColumn('transport_company_id');
            }
            if (Schema::hasColumn('sale_master', 'bilty_no')) {
                $table->dropColumn('bilty_no');
            }
        });
    }
};
