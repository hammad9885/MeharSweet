<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('product_masters', 'rate')) {
            Schema::table('product_masters', function (Blueprint $table) {
                $table->decimal('rate', 15, 2)->nullable()->after('cost_price');
            });

            // Copy existing cost_price to rate
            DB::statement("UPDATE product_masters SET rate = cost_price WHERE cost_price IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_masters', 'rate')) {
            Schema::table('product_masters', function (Blueprint $table) {
                $table->dropColumn('rate');
            });
        }
    }
};
