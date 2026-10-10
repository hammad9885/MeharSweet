<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_order_master', function (Blueprint $table) {
            $table->id();
            $table->string('v_no')->unique();
            $table->date('v_date');
            $table->string('prepared_by');
            $table->unsignedBigInteger('customer_id');
            $table->string('cid');
            $table->timestamps();
        });

        Schema::create('sale_order_detail', function (Blueprint $table) {
            $table->id();
            $table->string('v_no');
            $table->unsignedBigInteger('item_id');
            $table->decimal('qty', 15, 2);
            $table->decimal('rate', 15, 2);
            $table->string('cid');
            $table->timestamps();

            $table->foreign('v_no')->references('v_no')->on('sale_order_master')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_order_detail');
        Schema::dropIfExists('sale_order_master');
    }
};
