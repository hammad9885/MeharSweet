<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_order_detail', function (Blueprint $table) {
            $table->decimal('original_qty', 15, 2)->nullable()->after('qty');
        });

        DB::statement('UPDATE sale_order_detail SET original_qty = qty WHERE original_qty IS NULL');
    }

    public function down(): void
    {
        Schema::table('sale_order_detail', function (Blueprint $table) {
            $table->dropColumn('original_qty');
        });
    }
};
