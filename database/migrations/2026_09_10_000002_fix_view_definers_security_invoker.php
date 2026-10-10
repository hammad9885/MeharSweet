<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure definer users exist in local MySQL if permissions allow
        try {
            DB::statement("CREATE USER IF NOT EXISTS 'erplive_zamzamtoys'@'localhost'");
            DB::statement("CREATE USER IF NOT EXISTS 'erplive'@'localhost'");
        } catch (\Throwable $e) {
            // ignore if current db user doesn't have CREATE USER privilege
        }

        // Recreate trn_dtl_views with SQL SECURITY INVOKER
        DB::statement("
            CREATE OR REPLACE SQL SECURITY INVOKER VIEW `trn_dtl_views` AS
            SELECT
                `t`.`id` AS `id`,
                CAST(`t`.`account_id` AS UNSIGNED INTEGER) AS `aid`,
                `t`.`date` AS `v_date`,
                `t`.`v_no` AS `v_no`,
                `t`.`r_id` AS `r_id`,
                `t`.`v_type` COLLATE utf8mb4_unicode_ci AS `v_type`,
                `t`.`description` COLLATE utf8mb4_unicode_ci AS `descr`,
                CAST(`t`.`debit` AS CHAR(255) CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci AS `debit`,
                CAST(`t`.`credit` AS CHAR(255) CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci AS `credit`,
                CAST(`t`.`cid` AS UNSIGNED INTEGER) AS `cid`
            FROM `trn_dtl` AS `t`
            WHERE `t`.`v_type` != 'OB'
            UNION ALL
            SELECT
                NULL AS `id`,
                CAST(`ob`.`aid` AS UNSIGNED INTEGER) AS `aid`,
                `ob`.`op_date` AS `v_date`,
                1 AS `v_no`,
                NULL AS `r_id`,
                'OPN' COLLATE utf8mb4_unicode_ci AS `v_type`,
                'Opening Balance' COLLATE utf8mb4_unicode_ci AS `descr`,
                CAST(`ob`.`debit` AS CHAR(255) CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci AS `debit`,
                CAST(`ob`.`credit` AS CHAR(255) CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci AS `credit`,
                CAST(`ob`.`cid` AS UNSIGNED INTEGER) AS `cid`
            FROM `opening_bakance` AS `ob`
        ");

        // Recreate specific_item with SQL SECURITY INVOKER
        DB::statement("
            CREATE OR REPLACE SQL SECURITY INVOKER VIEW `specific_item` AS
            SELECT
                `os`.`item_id` AS `item_id`,
                STR_TO_DATE(`os`.`v_date`, '%Y-%m-%d') AS `v_date`,
                SUM(`os`.`qty`) AS `opn_qty`,
                SUM(`os`.`qty` * `os`.`rate`) AS `opn_amt`,
                0 AS `pur_qty`, 0 AS `pur_amt`,
                0 AS `pret_qty`, 0 AS `pret_amt`,
                0 AS `sal_qty`, 0 AS `sal_amt`,
                0 AS `sret_qty`, 0 AS `sret_amt`,
                0 AS `con_qty`, 0 AS `con_amt`,
                0 AS `adj_qty`, 0 AS `adj_amt`,
                0 AS `raw_qty`, 0 AS `raw_amt`,
                `os`.`cid` AS `cid`
            FROM `opening_stock` `os`
            GROUP BY `os`.`item_id`, `os`.`v_date`, `os`.`cid`
            UNION ALL
            SELECT
                `pi`.`item_id` AS `item_id`,
                STR_TO_DATE(`pm`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0,
                SUM(`pi`.`qty`) AS `pur_qty`,
                SUM(`pi`.`qty` * `pi`.`rate`) AS `pur_amt`,
                0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                `pm`.`cid` AS `cid`
            FROM (`purchase_invoices` `pi` JOIN `purchase_master` `pm` ON (`pi`.`v_no` = `pm`.`v_no`))
            GROUP BY `pi`.`item_id`, `pm`.`v_date`, `pm`.`cid`
            UNION ALL
            SELECT
                `pri`.`item_id` AS `item_id`,
                STR_TO_DATE(`prm`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0, 0, 0,
                SUM(`pri`.`qty`) AS `pret_qty`,
                SUM(`pri`.`qty` * `pri`.`rate`) AS `pret_amt`,
                0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                `prm`.`cid` AS `cid`
            FROM (`purchase_return_invoice` `pri` JOIN `purchase_return_master` `prm` ON (`pri`.`v_no` = `prm`.`v_no`))
            GROUP BY `pri`.`item_id`, `prm`.`v_date`, `prm`.`cid`
            UNION ALL
            SELECT
                `si`.`item_id` AS `item_id`,
                STR_TO_DATE(`sm`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0, 0, 0, 0, 0,
                SUM(`si`.`qty`) AS `sal_qty`,
                SUM(`si`.`qty` * `si`.`rate`) AS `sal_amt`,
                0, 0, 0, 0, 0, 0, 0, 0,
                `sm`.`cid` AS `cid`
            FROM (`sale_invoices` `si` JOIN `sale_master` `sm` ON (`si`.`v_no` = `sm`.`v_no`))
            GROUP BY `si`.`item_id`, `sm`.`v_date`, `sm`.`cid`
            UNION ALL
            SELECT
                `sri`.`item_id` AS `item_id`,
                STR_TO_DATE(`srm`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0, 0, 0, 0, 0, 0, 0,
                SUM(`sri`.`qty`) AS `sret_qty`,
                SUM(`sri`.`qty` * `sri`.`rate`) AS `sret_amt`,
                0, 0, 0, 0, 0, 0,
                `srm`.`cid` AS `cid`
            FROM (`sale_return_invoices` `sri` JOIN `sale_return_masters` `srm` ON (`sri`.`v_no` = `srm`.`v_no`))
            GROUP BY `sri`.`item_id`, `srm`.`v_date`, `srm`.`cid`
            UNION ALL
            SELECT
                `pd`.`item_id` AS `item_id`,
                STR_TO_DATE(`pm`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                SUM(`pd`.`qty`) AS `con_qty`,
                SUM(`pd`.`qty` * `pd`.`rate`) AS `con_amt`,
                0, 0, 0, 0,
                `pm`.`cid` AS `cid`
            FROM (`production_detail` `pd` JOIN `production_master` `pm` ON (`pd`.`v_no` = `pm`.`v_no`))
            GROUP BY `pd`.`item_id`, `pm`.`v_date`, `pm`.`cid`
            UNION ALL
            SELECT
                `sad`.`item_id` AS `item_id`,
                STR_TO_DATE(`sam`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                SUM(`sad`.`qty`) AS `adj_qty`,
                SUM(`sad`.`qty` * `sad`.`rate`) AS `adj_amt`,
                0, 0,
                `sam`.`cid` AS `cid`
            FROM (`stock_adj_details` `sad` JOIN `stock_adj_masters` `sam` ON (`sad`.`v_no` = `sam`.`v_no`))
            GROUP BY `sad`.`item_id`, `sam`.`v_date`, `sam`.`cid`
            UNION ALL
            SELECT
                `pc`.`rp_item_id` AS `item_id`,
                STR_TO_DATE(`pm`.`v_date`, '%Y-%m-%d') AS `v_date`,
                0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                SUM(`pc`.`qty`) AS `raw_qty`,
                SUM(`pc`.`qty` * `pc`.`rate`) AS `raw_amt`,
                `pm`.`cid` AS `cid`
            FROM (`production_consumed` `pc` JOIN `production_master` `pm` ON (`pc`.`v_no` = `pm`.`v_no`))
            GROUP BY `pc`.`rp_item_id`, `pm`.`v_date`, `pm`.`cid`
        ");

        // Recreate stock_qty with SQL SECURITY INVOKER
        DB::statement("
            CREATE OR REPLACE SQL SECURITY INVOKER VIEW `stock_qty` AS
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, -`sin`.`qty` AS `qty`, -(COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0)) AS `total_amount`, 'sale' AS `source`, `sin`.`cid` AS `cid`
            FROM (`sale_invoices` `sin` LEFT JOIN `sale_master` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
            UNION ALL
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, `sin`.`qty` AS `qty`, COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0) AS `total_amount`, 'sale return' AS `source`, `sin`.`cid` AS `cid`
            FROM (`sale_return_invoices` `sin` LEFT JOIN `sale_return_masters` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
            UNION ALL
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, `sin`.`qty` AS `qty`, COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0) AS `total_amount`, 'purchase' AS `source`, `sin`.`cid` AS `cid`
            FROM (`purchase_invoices` `sin` LEFT JOIN `purchase_master` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
            UNION ALL
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, -`sin`.`qty` AS `qty`, -(COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0)) AS `total_amount`, 'purchase return' AS `source`, `sin`.`cid` AS `cid`
            FROM (`purchase_return_invoice` `sin` LEFT JOIN `purchase_return_master` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
            UNION ALL
            SELECT `sin`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, `sin`.`qty` AS `qty`, `sin`.`rate` * `sin`.`qty` AS `total_amount`, 'opening' AS `source`, `sin`.`cid` AS `cid`
            FROM `opening_stock` `sin`
            UNION ALL
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, `sin`.`qty` AS `qty`, `sin`.`rate` * `sin`.`qty` AS `total_amount`, 'adjustment' AS `source`, `sin`.`cid` AS `cid`
            FROM (`stock_adj_details` `sin` LEFT JOIN `stock_adj_masters` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
            UNION ALL
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`item_id` AS `item_id`, `sin`.`qty` AS `qty`, `sin`.`rate` * `sin`.`qty` AS `total_amount`, 'production' AS `source`, `sin`.`cid` AS `cid`
            FROM (`production_detail` `sin` LEFT JOIN `production_master` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
            UNION ALL
            SELECT `sim`.`v_date` AS `v_date`, `sin`.`rp_item_id` AS `item_id`, -`sin`.`qty` AS `qty`, -(`sin`.`rate` * `sin`.`qty`) AS `total_amount`, 'consumed' AS `source`, `sin`.`cid` AS `cid`
            FROM (`production_consumed` `sin` LEFT JOIN `production_master` `sim` ON (`sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`))
        ");
    }

    public function down(): void
    {
    }
};
