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
            if (!Schema::hasColumn('sale_master', 'freight')) {
                $table->decimal('freight', 15, 2)->default(0)->nullable()->after('discount');
            }
        });

        Schema::table('delivery_challan_master', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_challan_master', 'freight')) {
                $table->decimal('freight', 15, 2)->default(0)->nullable()->after('qty_per_carton');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_challan_master', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_challan_master', 'freight')) {
                $table->dropColumn('freight');
            }
        });

        Schema::table('sale_master', function (Blueprint $table) {
            if (Schema::hasColumn('sale_master', 'freight')) {
                $table->dropColumn('freight');
            }
        });
    }
};
