<?php

namespace App\Console\Commands;

use App\Models\Customers;
use App\Models\ProductMaster;
use App\Models\SaleInvoices;
use App\Models\SaleMasters;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportHistoricalRates extends Command
{
    protected $signature = 'app:import-historical-rates {--clear : Clear existing records first}';

    protected $description = 'Import historical rates from CSV';

    public function handle()
    {
        $user = 1; // Default CID
        $isClear = $this->option('clear');

        if ($isClear) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            TRNDTL::where('v_type', 'SIN')->where('cid', $user)->delete();
            SaleInvoices::where('cid', $user)->delete();
            SaleMasters::where('cid', $user)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('All existing records cleared successfully.');
        }

        $filePath = storage_path('uploads/historical_rates.csv');
        
        if (!file_exists($filePath)) {
            $this->error("CSV file not found at: $filePath");
            return 1;
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);

        // Normalize headers
        $header = array_map(function ($h) {
            return strtoupper(trim($h));
        }, $header);

        $required = ['ACC_TITLE', 'ITEM_TITLE', 'RATE'];
        foreach ($required as $r) {
            if (!in_array($r, $header)) {
                fclose($handle);
                $this->error("Missing required column: $r. Found: " . implode(', ', $header));
                return 1;
            }
        }

        $vnoIdx = array_search('VNO', $header);
        $accIdx = array_search('ACC_TITLE', $header);
        $itemIdx = array_search('ITEM_TITLE', $header);
        $rateIdx = array_search('RATE', $header);

        $matched = 0;
        $skipped = [];
        $rowsByVno = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($header)) continue;

            $accTitle = trim($row[$accIdx] ?? '');
            $itemTitle = trim($row[$itemIdx] ?? '');
            $rate = floatval($row[$rateIdx] ?? 0);
            $vno = $vnoIdx !== false ? trim($row[$vnoIdx]) : null;

            if (empty($accTitle) || empty($itemTitle)) continue;

            // Match customer by name
            $customer = Customers::where('cid', $user)
                ->where(DB::raw('LOWER(name)'), strtolower($accTitle))
                ->first();

            if (!$customer) {
                // Fallback: try with TRIM
                $customer = Customers::where('cid', $user)
                    ->where(DB::raw('LOWER(TRIM(name))'), strtolower(trim($accTitle)))
                    ->first();
            }

            if (!$customer) {
                $skipped[] = "Row VNO=$vno: Customer \"$accTitle\" not found";
                continue;
            }

            // Match product by name
            $product = ProductMaster::where('cid', $user)
                ->where(DB::raw('LOWER(TRIM(product_name))'), strtolower($itemTitle))
                ->first();

            if (!$product) {
                $skipped[] = "Row VNO=$vno: Product \"$itemTitle\" not found";
                continue;
            }

            $key = $vno ?: uniqid('vno_');
            $rowsByVno[$key][] = [
                'customer' => $customer,
                'product' => $product,
                'rate' => $rate,
            ];
            $matched++;
        }
        fclose($handle);

        if ($matched === 0) {
            $this->error('No rows could be matched. Check your customer and product names.');
            foreach ($skipped as $msg) {
                $this->warn($msg);
            }
            return 1;
        }

        // Get max existing v_no to avoid conflicts
        $maxVno = SaleMasters::where('cid', $user)->max('v_no');
        $nextVno = $maxVno ? $maxVno + 1 : 1;

        $importedMasters = 0;
        $importedDetails = 0;

        DB::transaction(function () use ($rowsByVno, $user, &$nextVno, &$importedMasters, &$importedDetails) {
            $erpParams = ErpParam::where('cid', $user)->first();

            foreach ($rowsByVno as $vnoKey => $items) {
                $customer = $items[0]['customer'];
                $totalRate = array_sum(array_column($items, 'rate'));
                $grandTotal = $totalRate; // qty=1 per item

                $v_no = $nextVno++;

                SaleMasters::create([
                    'v_no' => $v_no,
                    'v_date' => now()->format('Y-m-d'),
                    'customer_id' => $customer->acc_id,
                    'grand_total' => $grandTotal,
                    'discount' => 0,
                    'cid' => $user,
                ]);

                foreach ($items as $item) {
                    $rate = $item['rate'];
                    $exclusive = $rate; // qty=1

                    SaleInvoices::create([
                        'v_no' => $v_no,
                        'customer_id' => $customer->acc_id,
                        'item_id' => $item['product']->id,
                        'qty' => 1,
                        'rate' => $rate,
                        'exclusive' => $exclusive,
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                        'inclusive' => $exclusive,
                        'discount' => 0,
                        'discount_perc' => 0,
                        'cid' => $user,
                    ]);

                    $importedDetails++;
                }

                // Create TRNDTL entry
                if ($erpParams && $erpParams->saleCreditAccount()) {
                    $maxTrn = TRNDTL::where('v_type', 'SIN')->where('cid', $user)->max('v_no');
                    $trnVno = $maxTrn ? $maxTrn + 1 : 1;

                    TRNDTL::create([
                        'v_no' => $trnVno,
                        'account_id' => $customer->acc_id,
                        'cash_id' => $erpParams->saleCreditAccount,
                        'credit' => $grandTotal,
                        'debit' => 0,
                        'date' => now()->format('Y-m-d'),
                        'v_type' => 'SIN',
                        'preparedby' => Auth::user()->name ?? 'System',
                        'r_id' => $v_no,
                        'cid' => $user,
                    ]);
                }

                $importedMasters++;
            }
        });

        $this->info("Imported $importedMasters sale invoices ($importedDetails detail rows) successfully.");
        if (count($skipped) > 0) {
            $this->warn(count($skipped) . " rows were skipped:");
            foreach ($skipped as $msg) {
                $this->warn("  - $msg");
            }
        }

        return 0;
    }
}