<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_issue_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('inv_no');
            $table->date('date');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->string('lot_no')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_issue_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('inv_no');
            $table->unsignedBigInteger('product_id');
            $table->string('qty')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_issue_details');
        Schema::dropIfExists('stock_issue_masters');
    }
};
