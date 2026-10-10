<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_in_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('inv_no');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('qty')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_in_details');
    }
};
