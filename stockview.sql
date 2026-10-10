SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    - `sin`.`qty` AS `qty`,
    -(
        COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0)
    ) AS `total_amount`,
    'sale' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `sale_invoices` `sin`
    LEFT JOIN`sale_master` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )
UNION ALL
SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    `sin`.`qty` AS `qty`,
    COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0) AS `total_amount`,
    'sale return' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `sale_return_invoices` `sin`
    LEFT JOIN`sale_return_masters` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )
UNION ALL
SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    `sin`.`qty` AS `qty`,
    COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0) AS `total_amount`,
    'purchase' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `purchase_invoices` `sin`
    LEFT JOIN`purchase_master` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )
UNION ALL
SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    - `sin`.`qty` AS `qty`,
    -(
        COALESCE(`sin`.`inclusive`, 0) - COALESCE(`sin`.`discount`, 0)
    ) AS `total_amount`,
    'purchase return' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `purchase_return_invoice` `sin`
    LEFT JOIN`purchase_return_master` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )
UNION ALL
SELECT
    `sin`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    `sin`.`qty` AS `qty`,
    `sin`.`rate` * `sin`.`qty` AS `total_amount`,
    'opening' AS `source`,
    `sin`.`cid` AS `cid`
FROM
   `opening_stock` `sin`
UNION ALL
SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    `sin`.`qty` AS `qty`,
    `sin`.`rate` * `sin`.`qty` AS `total_amount`,
    'adjustment' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `stock_adj_details` `sin`
    LEFT JOIN`stock_adj_masters` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )
UNION ALL
SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`item_id` AS `item_id`,
    `sin`.`qty` AS `qty`,
    `sin`.`rate` * `sin`.`qty` AS `total_amount`,
    'production' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `production_detail` `sin`
    LEFT JOIN`production_master` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )
UNION ALL
SELECT
    `sim`.`v_date` AS `v_date`,
    `sin`.`rp_item_id` AS `item_id`,
    - `sin`.`qty` AS `qty`,
    -(`sin`.`rate` * `sin`.`qty`) AS `total_amount`,
    'consumed' AS `source`,
    `sin`.`cid` AS `cid`
FROM
    (
       `production_consumed` `sin`
    LEFT JOIN`production_master` `sim`
    ON
        (
            `sim`.`v_no` = `sin`.`v_no` AND `sim`.`cid` = `sin`.`cid`
        )
    )