<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adj_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('v_no');
            $table->date('v_date');
            $table->string('prepared_by');
            $table->unsignedBigInteger('cid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adj_masters');
    }
};
