<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adj_details', function (Blueprint $table) {
            $table->id();
            $table->string('v_no'); // foreign key link to master
            $table->unsignedBigInteger('item_id');
            $table->string('qty')->default(0);
            $table->string('rate')->default(0);
            $table->string('amount')->default(0);
            $table->unsignedBigInteger('cid');
            $table->timestamps();

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adj_details');
    }
};

