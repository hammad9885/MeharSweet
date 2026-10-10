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
        Schema::create('production_consumed', function (Blueprint $table) {
            $table->id();
            $table->string('v_no');
            $table->unsignedBigInteger('fp_item_id');
            $table->unsignedBigInteger('rp_item_id');
            $table->unsignedBigInteger('cid');
            $table->string('qty');
            $table->string('rate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_consumed');
    }
};
