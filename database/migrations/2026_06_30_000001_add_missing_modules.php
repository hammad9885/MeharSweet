<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private $modules = [
        // Sale modules
        'Sale Order',
        'Delivery Challan (SOR)',
        'Raw Sale Invoice',
        'Raw Return Invoice',
        'Customer Category',
        'Category Wise Sale',
        'Sale Report',
        // Purchase modules
        'Category Wise Purchase',
        'Supplier Wise Report',
        // Manufacturing modules
        'Consumption',
        'Production',
        'Raw Material Report',
        'Finished Product Report',
        // Product/Stock modules
        'Product Groups',
        'Product Master',
        'Mold Registration',
        'Sale Type',
        'Stock Value',
        // Brand
        'Brand Wise Sale',
        'Brand Wise Purchase',
        'Store Wise Report',
        'Barcode Generate',
    ];

    public function up(): void
    {
        foreach ($this->modules as $name) {
            Module::firstOrCreate(['name' => $name]);
        }
    }

    public function down(): void
    {
        Module::whereIn('name', $this->modules)->delete();
    }
};
