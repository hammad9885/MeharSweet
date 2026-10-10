<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packing_lot_posting_masters') && !Schema::hasColumn('packing_lot_posting_masters', 'deleted_at')) {
            Schema::table('packing_lot_posting_masters', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('packing_lot_posting_masters') && Schema::hasColumn('packing_lot_posting_masters', 'deleted_at')) {
            Schema::table('packing_lot_posting_masters', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
