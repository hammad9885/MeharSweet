<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_masters', function (Blueprint $table) {
    $table->id();

    $table->string('seq_number')->nullable();
    $table->string('urdu_title')->nullable();
    $table->integer('min_qty')->nullable();
    $table->unsignedBigInteger('cid');

    // ✅ FIX 1: item type as foreign key
    $table->foreignId('type_id')
          ->nullable()
          ->constrained('item_types') // table name
          ->nullOnDelete();

    // ✅ FIX 2: unit as foreign key
    $table->foreignId('unit_id')
          ->nullable()
          ->constrained('units') // ⚠️ make sure this table exists
          ->nullOnDelete();

    // Other fields
    $table->string('product_name')->nullable();
    $table->string('stroke_wt')->nullable();
    $table->integer('stroke')->nullable();
    $table->string('lot_no_sale')->nullable();
    $table->string('lot_no_purchase')->nullable();
    $table->boolean('finished')->default(false);
    $table->integer('dye_no')->nullable();

    // Group relation
    $table->foreignId('group_id')
          ->nullable()
          ->constrained('product_groups')
          ->nullOnDelete();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('product_masters');
    }
};