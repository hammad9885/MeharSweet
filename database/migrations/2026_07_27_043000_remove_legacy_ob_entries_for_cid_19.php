<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('trn_dtl')
            ->whereIn('id', [790, 791])
            ->where('cid', 19)
            ->where('v_type', 'OB')
            ->delete();
    }

    public function down(): void
    {
        DB::table('trn_dtl')->insert([
            [
                'id' => 790,
                'v_type' => 'OB',
                'v_no' => '1',
                'date' => '2026-07-25',
                'description' => ' ',
                'debit' => '111',
                'credit' => '0',
                'status' => 'N',
                'preparedby' => 'Zam Zam Toys',
                'account_id' => 330,
                'cash_id' => null,
                'r_id' => null,
                'pre_bal' => null,
                'file_id' => null,
                'cid' => 19,
                'created_at' => '2026-07-25 14:41:08',
                'updated_at' => '2026-07-25 14:41:08',
            ],
            [
                'id' => 791,
                'v_type' => 'OB',
                'v_no' => '1',
                'date' => '2026-07-25',
                'description' => ' ',
                'debit' => '0',
                'credit' => '111',
                'status' => 'N',
                'preparedby' => 'Zam Zam Toys',
                'account_id' => 302,
                'cash_id' => null,
                'r_id' => null,
                'pre_bal' => null,
                'file_id' => null,
                'cid' => 19,
                'created_at' => '2026-07-25 14:41:08',
                'updated_at' => '2026-07-25 14:41:08',
            ],
        ]);
    }
};
