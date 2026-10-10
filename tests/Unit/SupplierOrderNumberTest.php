<?php

namespace Tests\Unit;

use App\Models\Supplier;
use Tests\TestCase;

class SupplierOrderNumberTest extends TestCase
{
    public function test_supplier_order_numbers_are_normalized_and_sorted(): void
    {
        $suppliers = collect([
            (object) ['order_number' => '13Alii'],
            (object) ['order_number' => '24Aliisss'],
            (object) ['order_number' => '3-Al Miraj Traders'],
            (object) ['order_number' => '4-Nimra Plastic'],
            (object) ['order_number' => '5-Hasmii Traders'],
            (object) ['order_number' => null],
        ]);

        $sorted = Supplier::sortByOrderNumber($suppliers);
        $normalized = $sorted->map(fn ($supplier) => Supplier::normalizeOrderNumber($supplier->order_number))->filter()->values()->all();

        $this->assertSame(['3', '4', '5', '13', '24'], $normalized);
    }
}
