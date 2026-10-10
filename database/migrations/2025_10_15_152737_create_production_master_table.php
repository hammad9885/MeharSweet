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
        Schema::create('production_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('v_no');
            $table->string('date');
            $table->string('descr');
            $table->string('amount');
            $table->unsignedBigInteger('cid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_master');
    }
};
