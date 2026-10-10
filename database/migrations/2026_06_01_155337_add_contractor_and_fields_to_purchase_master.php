<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_master', function (Blueprint $table) {
            $table->string('ref_no')->nullable()->after('gate_pass_no');
            $table->text('remarks')->nullable()->after('ref_no');
            $table->unsignedBigInteger('contractor_id')->nullable()->after('supplier_id');
            $table->decimal('freight', 12, 2)->default(0)->after('discount');
            $table->decimal('net_amount', 12, 2)->default(0)->after('freight');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_master', function (Blueprint $table) {
            $table->dropColumn(['ref_no', 'remarks', 'contractor_id', 'freight', 'net_amount']);
        });
    }
};
