<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_challan_master', function (Blueprint $table) {
            $table->id();
            $table->string('v_no')->unique();
            $table->date('v_date');
            $table->unsignedBigInteger('customer_id');
            $table->string('prepared_by');
            $table->string('ref_no')->nullable();
            $table->string('bilty_no')->nullable();
            $table->string('transport_company')->nullable();
            $table->string('cid');
            $table->timestamps();
        });

        Schema::create('delivery_challan_detail', function (Blueprint $table) {
            $table->id();
            $table->string('v_no');
            $table->string('sale_order_vno');
            $table->unsignedBigInteger('item_id');
            $table->decimal('available_qty', 15, 2);
            $table->decimal('load_qty', 15, 2);
            $table->string('cid');
            $table->timestamps();

            $table->foreign('v_no')->references('v_no')->on('delivery_challan_master')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_challan_detail');
        Schema::dropIfExists('delivery_challan_master');
    }
};
