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
        Schema::create('production_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('v_no');
            $table->unsignedBigInteger('item_id');
            $table->string('qty');
            $table->string('rate');
            $table->string('amount');
            $table->string('cid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_detail');
    }
};
