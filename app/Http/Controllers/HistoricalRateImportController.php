<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\ProductMaster;
use App\Models\SaleInvoices;
use App\Models\SaleMasters;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoricalRateImportController extends Controller
{
    public function index()
    {
        return view('historical_rates.import');
    }

    public function clear(Request $request)
    {
        $user = Auth::user()->cid;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TRNDTL::where('v_type', 'SIN')->where('cid', $user)->delete();
        SaleInvoices::where('cid', $user)->delete();
        SaleMasters::where('cid', $user)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'All existing records cleared successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx',
        ]);

        $user = Auth::user()->cid;
        $isClear = $request->input('clear', false);

        if ($isClear) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            TRNDTL::where('v_type', 'SIN')->where('cid', $user)->delete();
            SaleInvoices::where('cid', $user)->delete();
            SaleMasters::where('cid', $user)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('success', 'All existing records cleared successfully.');
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        // Normalize headers
        $header = array_map(function ($h) {
            return strtoupper(trim($h));
        }, $header);

        $required = ['ACC_TITLE', 'ITEM_TITLE', 'RATE'];
        foreach ($required as $r) {
            if (!in_array($r, $header)) {
                fclose($handle);
                return back()->withErrors(['csv_file' => "Missing required column: $r. Found: " . implode(', ', $header)]);
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

            // Normalize customer name for matching
            $customerNameForQuery = strtolower(trim($accTitle));
            
            // Try multiple matching strategies
            $customer = Customers::where('cid', $user)
                ->where(function($q) use ($customerNameForQuery) {
                    $q->where(DB::raw('LOWER(TRIM(name))'), $customerNameForQuery)
                      ->orWhere(DB::raw('LOWER(name)'), $customerNameForQuery);
                })
                ->first();

            if (!$customer) {
                // Try partial match as fallback
                $customer = Customers::where('cid', $user)
                    ->where('name', 'like', '%' . $accTitle . '%')
                    ->first();
            }
            
            if (!$customer) {
                $skipped[] = "Row VNO=$vno: Customer \"$accTitle\" not found. Available: " . 
                    Customers::where('cid', $user)->pluck('name')->implode(', ');
                continue;
            }

            // Match product by name - flexible matching like customer
            $itemTitleForQuery = strtolower(trim($itemTitle));
            
            $product = ProductMaster::where('cid', $user)
                ->where(function($q) use ($itemTitleForQuery) {
                    $q->where(DB::raw('LOWER(TRIM(product_name))'), $itemTitleForQuery)
                      ->orWhere(DB::raw('LOWER(product_name)'), $itemTitleForQuery);
                })
                ->first();

            if (!$product) {
                // Try partial match as fallback
                $product = ProductMaster::where('cid', $user)
                    ->where('product_name', 'like', '%' . $itemTitle . '%')
                    ->first();
            }
            
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
            return back()->with('error', 'No rows could be matched. Check your customer and product names.')
                ->with('skipped', $skipped);
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
                $grandTotal = $totalRate; // qty=1 per item, so total = sum of rates

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
                        'preparedby' => Auth::user()->name,
                        'r_id' => $v_no,
                        'cid' => $user,
                    ]);
                }

                $importedMasters++;
            }
        });

        $message = "Imported $importedMasters sale invoices ($importedDetails detail rows) successfully.";
        if (count($skipped) > 0) {
            $message .= " " . count($skipped) . " rows were skipped.";
        }

        return back()->with('success', $message)
            ->with('skipped', $skipped)
            ->with('importedMasters', $importedMasters)
            ->with('importedDetails', $importedDetails);
    }
}
