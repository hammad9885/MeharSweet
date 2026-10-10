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
        Schema::table('consumption_masters', function (Blueprint $table) {
            $table->string('item_title')->nullable()->after('item_id');
            $table->unsignedBigInteger('mold_id')->nullable();
            $table->decimal('mold_qty', 15, 2)->nullable();
            $table->string('machine_capacity')->nullable();
            $table->string('stroke')->nullable();
            $table->decimal('grams', 15, 3)->nullable();
            $table->unsignedBigInteger('contractor_account_id')->nullable();
            $table->decimal('wages_rate', 15, 2)->nullable();
            $table->decimal('wages_amount', 15, 2)->nullable();
            $table->string('lot_no')->nullable();
            $table->decimal('lot_qty', 15, 2)->nullable();
            $table->decimal('target_qty', 15, 2)->nullable();
            $table->decimal('sale_rate', 15, 2)->nullable();
            $table->decimal('packing_rate', 15, 2)->nullable();
            $table->decimal('carton_rate', 15, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('prepared_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_masters', function (Blueprint $table) {
            $table->dropColumn([
                'item_title', 'mold_id', 'mold_qty', 'machine_capacity', 'stroke', 'grams',
                'contractor_account_id', 'wages_rate', 'wages_amount', 'lot_no', 'lot_qty',
                'target_qty', 'sale_rate', 'packing_rate', 'carton_rate', 'remarks', 'prepared_by'
            ]);
        });
    }
};
