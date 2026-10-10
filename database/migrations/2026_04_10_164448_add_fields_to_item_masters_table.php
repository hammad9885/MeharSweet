<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('item_masters', 'urdu_title')) {
                $table->string('urdu_title')->nullable()->after('item_type');
            }
            if (!Schema::hasColumn('item_masters', 'seq_number')) {
                $table->string('seq_number')->nullable()->after('urdu_title');
            }
            if (!Schema::hasColumn('item_masters', 'set_qty')) {
                $table->integer('set_qty')->nullable()->after('seq_number');
            }
            if (!Schema::hasColumn('item_masters', 'auto_load_on_consumption')) {
                $table->boolean('auto_load_on_consumption')->default(false)->after('set_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_masters', function (Blueprint $table) {
            $columns = ['urdu_title', 'seq_number', 'set_qty', 'auto_load_on_consumption'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('item_masters', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};