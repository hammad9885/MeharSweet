<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_master', function (Blueprint $table) {
            $table->string('ref_no')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->decimal('freight', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('purchase_master', function (Blueprint $table) {
            $table->dropColumn(['ref_no', 'remarks', 'contractor_id', 'freight', 'net_amount']);
        });
    }
};
