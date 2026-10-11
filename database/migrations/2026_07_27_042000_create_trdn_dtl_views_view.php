<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW trn_dtl_views AS
            SELECT
                t.id AS id,
                t.account_id AS aid,
                t.date AS v_date,
                t.v_no AS v_no,
                t.v_type COLLATE utf8mb4_unicode_ci AS v_type,
                t.description COLLATE utf8mb4_unicode_ci AS descr,
                CAST(t.debit AS CHAR(255) CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci AS debit,
                CAST(t.credit AS CHAR(255) CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci AS credit,
                t.cid AS cid
            FROM trn_dtl AS t
            WHERE t.v_type != 'OB'
            UNION ALL
            SELECT
                NULL AS id,
                CAST(ob.aid AS UNSIGNED INTEGER) AS aid,
                ob.op_date AS v_date,
                1 AS v_no,
                'OPN' COLLATE utf8mb4_unicode_ci AS v_type,
                'Opening Balance' COLLATE utf8mb4_unicode_ci AS descr,
                CAST(ob.debit AS CHAR(255) CHARACTER SET utf8mb4) AS debit,
                CAST(ob.credit AS CHAR(255) CHARACTER SET utf8mb4) AS credit,
                CAST(ob.cid AS UNSIGNED INTEGER) AS cid
            FROM opening_bakance AS ob
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS trn_dtl_views');
    }
};