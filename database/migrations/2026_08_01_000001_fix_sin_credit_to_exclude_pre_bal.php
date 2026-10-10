<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("UPDATE trn_dtl SET credit = credit - COALESCE(pre_bal, 0) WHERE v_type = 'SIN' AND pre_bal IS NOT NULL AND pre_bal != 0");
    }

    public function down()
    {
    }
};
