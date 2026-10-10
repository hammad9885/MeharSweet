<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private $modules = [
        // Accounts
        'Level1',
        'Level2',
        'Chart Of Account',
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
        // Sale
        'Sale Invoice',
        'Sale Order',
        'Delivery Challan (SOR)',
        'Raw Sale Invoice',
        'Raw Return Invoice',
        'Sale Return',
        'Customer',
        'Customer Category',
        'Item Wise Sale',
        'Category Wise Sale',
        'Customer Wise Sale',
        'Brand Wise Sale',
        'Transport Company',
        'Reference Name',
        'Sale Report',
        'Contractor Lot Posting',
        // Purchase
        'Purchase Invoice',
        'Purchase Return',
        'Supplier',
        'Supplier Wise Report',
        'Item Wise Purchase',
        'Category Wise Purchase',
        'Brand Wise Purchase',
        'Contractor',
        'Purchase Report',
        // Manufacturing
        'Consumption',
        'Production',
        'Raw Material Report',
        'Finished Product Report',
        // Raw Material
        'Item Category',
        'Item Units',
        'Item Registration',
        'Brand Name',
        // Finished Product
        'Product Groups',
        'Product Master',
        'Mold Registration',
        'Barcode Generate',
        // Stock Management
        'Sale Type',
        'Stock Adjustment',
        'Opening Stock',
        'Stock In',
        'Stock Issue',
        'Stock Value',
        'Stock Report',
        'Store Wise Report',
    ];

    public function up(): void
    {
        foreach ($this->modules as $name) {
            Module::firstOrCreate(['name' => $name]);
        }
    }

    public function down(): void
    {
        // Records created here may already exist; leave them in place on rollback.
    }
};
