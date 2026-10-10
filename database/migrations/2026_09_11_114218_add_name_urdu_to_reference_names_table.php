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
        Schema::table('reference_names', function (Blueprint $table) {
            if (!Schema::hasColumn('reference_names', 'name_urdu')) {
                $table->string('name_urdu')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reference_names', function (Blueprint $table) {
            if (Schema::hasColumn('reference_names', 'name_urdu')) {
                $table->dropColumn('name_urdu');
            }
        });
    }
};
