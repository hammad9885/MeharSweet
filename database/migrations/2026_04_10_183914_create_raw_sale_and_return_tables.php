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
        // Raw Sale Master
        Schema::create('raw_sale_master', function (Blueprint $table) {
            $table->id();
            $table->integer('v_no');
            $table->string('gate_pass_no')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->date('v_date')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('cid'); // Company ID
            $table->timestamps();
        });

        // Raw Sale Invoices (Details)
        Schema::create('raw_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('v_no');
            $table->string('customer_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->decimal('qty', 15, 2);
            $table->decimal('rate', 15, 2);
            $table->decimal('exclusive', 15, 2)->default(0);
            $table->decimal('tax_rate', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('inclusive', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('discount_perc', 15, 2)->default(0);
            $table->string('cid');
            $table->timestamps();
        });

        // Raw Sale Return Masters
        Schema::create('raw_sale_return_masters', function (Blueprint $table) {
            $table->id();
            $table->integer('v_no');
            $table->string('gate_pass_no')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->date('v_date')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('cid');
            $table->timestamps();
        });

        // Raw Sale Return Invoices (Details)
        Schema::create('raw_sale_return_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('v_no');
            $table->string('customer_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->decimal('qty', 15, 2);
            $table->decimal('rate', 15, 2);
            $table->decimal('exclusive', 15, 2)->default(0);
            $table->decimal('tax_rate', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('inclusive', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('discount_perc', 15, 2)->default(0);
            $table->string('cid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_sale_master');
        Schema::dropIfExists('raw_sale_invoices');
        Schema::dropIfExists('raw_sale_return_masters');
        Schema::dropIfExists('raw_sale_return_invoices');
    }
};
