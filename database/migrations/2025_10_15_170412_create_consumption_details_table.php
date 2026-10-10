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
        Schema::create('consumption_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('v_no');
            $table->unsignedBigInteger('item_id');
            $table->string('qty')->default(0);
            $table->string('rate')->default(0);
            $table->string('amount')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumption_details');
    }
};
