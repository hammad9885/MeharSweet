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
        Schema::table('consumption_details', function (Blueprint $table) {
            $table->unsignedBigInteger('mold_id')->nullable()->after('item_id');
            $table->string('machine_capacity')->nullable()->after('mold_id');
            $table->string('stroke')->nullable()->after('machine_capacity');
            $table->decimal('grams', 15, 3)->nullable()->after('stroke');
            $table->decimal('calc_qty', 15, 2)->nullable()->after('grams');
            $table->string('unit', 255)->nullable()->after('calc_qty');
            $table->unsignedBigInteger('contractor_account_id')->nullable()->after('unit');
            $table->decimal('material_rate', 15, 2)->nullable()->after('contractor_account_id');
            $table->decimal('mat_amount', 15, 2)->nullable()->after('material_rate');
            $table->string('hatt', 255)->nullable()->after('mat_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_details', function (Blueprint $table) {
            $table->dropColumn([
                'mold_id', 'machine_capacity', 'stroke', 'grams', 'calc_qty',
                'unit', 'contractor_account_id', 'material_rate', 'mat_amount', 'hatt'
            ]);
        });
    }
};
