<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_lot_posting_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('voucher_no');
            $table->date('date');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('prepared_by')->nullable();
            $table->text('remarks')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('prev_lot_no')->nullable();
            $table->decimal('prev_lot_amount', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('contractor_lot_posting_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('voucher_no');
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('qty')->default(0);
            $table->string('unit')->nullable();
            $table->unsignedBigInteger('mold_id')->nullable();
            $table->string('mold_qty')->default(0);
            $table->decimal('wages', 15, 2)->nullable();
            $table->decimal('wages_amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_lot_posting_details');
        Schema::dropIfExists('contractor_lot_posting_masters');
    }
};