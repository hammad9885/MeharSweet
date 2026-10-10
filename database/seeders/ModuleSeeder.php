<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $modules = [

            // ACCOUNTS
            'Level1',
            'Level2',
            'Chart of Account',
            'Cash Payment',
            'Cash Receipt',
            'Bank Payment',
            'Bank Receipt',
            'Journal Voucher',
            'Opening Balance',
            'Ledger',
            'Cash Book',
            'Payables',
            'Recieveable',
            'Trial Balance',

            // SALES
            'Sale Invoice',
            'Sale Return',
            'Customer',
            'Item Wise Sale',
            'Category Wise Sale',
            'Customer Wise Sale',
            'Sale Report',
            'Contractor Lot Posting',

            // PURCHASE
            'Purchase Invoice',
            'Purchase Return',
            'Item Wise Purchase',
            'Category Wise Purchase',
            'Supplier',
            'Supplier Wise Report',
            'Purchase Report',

            // MANUFACTURING
            'Consumption',
            'Production',

            // INVENTORY
            'Item Category',
            'Item Units',
            'Sale Type',
            'Item Registration',
            'Stock Adjustment',
            'Opening Stock',
            'Raw Material Report',
            'Finished Product Report',
            'Stock Value',

        ];

        foreach ($modules as $module) {
            Module::firstOrCreate([
                'name' => $module
            ]);
        }
    }
}