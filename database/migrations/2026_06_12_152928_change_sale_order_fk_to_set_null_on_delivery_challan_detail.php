<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropForeignKeyIfExists($table, $constraint)
    {
        $exists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = ?", [$table, $constraint]);

        if (!empty($exists)) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }

    public function up(): void
    {
        $this->dropForeignKeyIfExists('delivery_challan_detail', 'delivery_challan_detail_sale_order_vno_foreign');
        DB::statement('ALTER TABLE delivery_challan_detail MODIFY COLUMN sale_order_vno VARCHAR(255) NULL');
        DB::statement('ALTER TABLE delivery_challan_detail ADD CONSTRAINT delivery_challan_detail_sale_order_vno_foreign FOREIGN KEY (sale_order_vno) REFERENCES sale_order_master(v_no) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $this->dropForeignKeyIfExists('delivery_challan_detail', 'delivery_challan_detail_sale_order_vno_foreign');
        DB::statement('ALTER TABLE delivery_challan_detail MODIFY COLUMN sale_order_vno VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE delivery_challan_detail ADD CONSTRAINT delivery_challan_detail_sale_order_vno_foreign FOREIGN KEY (sale_order_vno) REFERENCES sale_order_master(v_no) ON DELETE RESTRICT');
    }
};