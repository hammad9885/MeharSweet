<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_order_master', function (Blueprint $table) {
            $table->string('party_ref', 255)->nullable()->after('prepared_by');
        });
    }

    public function down(): void
    {
        Schema::table('sale_order_master', function (Blueprint $table) {
            $table->dropColumn('party_ref');
        });
    }
};
