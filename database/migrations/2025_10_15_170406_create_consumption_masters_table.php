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
        Schema::create('consumption_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('v_no');
            $table->date('date');
            $table->string('descr')->nullable();
            $table->string('amount')->default(0);
            $table->string('qty')->default(0);
            $table->string('cost_price')->default(0);
            $table->string('tamount')->default(0);
            $table->string('is_expired')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumption_masters');
    }
};
