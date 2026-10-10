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
        Schema::create('molds', function (Blueprint $table) {
            $table->id();
        $table->string('mold_id');
        $table->string('mold_name');
        $table->decimal('weight', 10, 2)->nullable();
        $table->decimal('wages', 10, 2)->nullable();
        $table->decimal('expense', 10, 2)->nullable();
        $table->decimal('total', 10, 2)->nullable();
        $table->text('remarks')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molds');
    }
};
