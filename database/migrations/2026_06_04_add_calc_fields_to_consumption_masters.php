<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consumption_masters', function (Blueprint $table) {
            $table->decimal('calc_qty', 15, 2)->nullable()->after('target_qty');
            $table->string('unit', 255)->nullable()->after('calc_qty');
            $table->decimal('material_rate', 15, 2)->nullable()->after('unit');
            $table->decimal('mat_amount', 15, 2)->nullable()->after('material_rate');
            $table->string('hatt', 255)->nullable()->after('mat_amount');
        });
    }

    public function down()
    {
        Schema::table('consumption_masters', function (Blueprint $table) {
            $table->dropColumn(['calc_qty', 'unit', 'material_rate', 'mat_amount', 'hatt']);
        });
    }
};
