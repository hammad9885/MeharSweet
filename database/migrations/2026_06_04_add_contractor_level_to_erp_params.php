<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('erp_params', function (Blueprint $table) {
            $table->unsignedBigInteger('contractor_level')->nullable()->after('customer_level');
        });
    }

    public function down()
    {
        Schema::table('erp_params', function (Blueprint $table) {
            $table->dropColumn('contractor_level');
        });
    }
};
