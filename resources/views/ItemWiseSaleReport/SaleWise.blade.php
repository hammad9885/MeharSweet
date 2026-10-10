@extends('layouts.app')
@section('styles')
    <!-- It's recommended to place these in your main layout file -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    @php
        $sumqty = 0;
        $sumDis = 0;
        $sumtotal = 0;
        $sumRqty = 0;
        $sumRDis = 0;
        $sumRtotal = 0;
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    
                     <h4 class="page-title">Item Wise Sale Report</h4>
                </div>
            </div>
        </div>
       

        <!-- Filter Form - Hidden during print -->
        <div class="no-print">
            <form method="GET" action="{{ route('ItemReportbysale.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3 d-flex flex-column justify-content-end mb-2">
                        <label for="item_id" class="form-label mb-1">Item</label>
                        <select name="item_id" class="form-control select2">
                            <option value="">All Items</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ str_replace('\\', '/', $item->item_code) . '-' . $item->urdu_title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex flex-column justify-content-end mb-2">
                        <label class="form-label mb-1">Start Date</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ request('start_date', now()->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3 d-flex flex-column justify-content-end mb-2">
                        <label class="form-label mb-1">End Date</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                </div>

                <input type="hidden" name="summarize" id="summarize" value="0">

                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-success" onclick="document.getElementById('summarize').value = 0">
                        Show
                    </button>
                    <button type="submit" class="btn btn-secondary"
                        onclick="document.getElementById('summarize').value = 1">
                        Summary
                    </button>
                    <a href="{{ route('ItemReportbysale.index') }}" class="btn btn-danger">Reset</a>
                </div>
            </form>
            <button class="btn btn-warning" onclick="printTable()">
                Print
            </button>

        </div>

        <!-- Report Date Range (Visible in print) -->
        @if (request('start_date') || request('end_date'))
            <div class="mb-3 text-center">
                <strong>Period:</strong>
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : 'Start' }}
                -
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : 'End' }}
            </div>
        @endif

        @if ($summarize)
            <!-- ✅ SUMMARY VIEW -->
            <div id="summaryreport">
                <h4>Item Wise Sale Summary</h4>
                <table class="table table-bordered text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>Item</th>
                            <th>Sale Qty</th>
                            <th>Return Qty</th>
                            <th>Net Qty</th>
                            <th>Sale Amount</th>
                            <th>Return Amount</th>
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totSaleqty = $totReturnqty = $totNetqty = 0;
                            $totSaleAmt = $totReturnAmt = $totNetAmt = 0;
                        @endphp
                        @foreach ($report as $row)
                            <tr>
                                <td style="text-align: left;">{{ $row['item'] }}</td>
                                <td>{{ $row['sale_qty'] }}</td>
                                <td>{{ $row['return_qty'] }}</td>
                                <td>{{ $row['net_qty'] }}</td>
                                <td>{{ number_format($row['sale_amount'], 0) }}</td>
                                <td>{{ number_format($row['return_amount'], 0) }}</td>
                                <td>{{ number_format($row['net_amount'], 0) }}</td>
                            </tr>
                            @php
                                $totSaleqty += $row['sale_qty'];
                                $totReturnqty += $row['return_qty'];
                                $totNetqty += $row['net_qty'];
                                $totSaleAmt += $row['sale_amount'];
                                $totReturnAmt += $row['return_amount'];
                                $totNetAmt += $row['net_amount'];
                            @endphp
                        @endforeach
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td>{{ $totSaleqty }}</td>
                            <td>{{ $totReturnqty }}</td>
                            <td>{{ $totNetqty }}</td>
                            <td>{{ number_format($totSaleAmt, 0) }}</td>
                            <td>{{ number_format($totReturnAmt, 0) }}</td>
                            <td>{{ number_format($totNetAmt, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div id="detailreport">
                <!-- 🧾 DETAILED VIEW -->
                <h4>Sale Invoice</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Voucher No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Tax</th>
                            <th>Inclusive</th>
                            <th>Discount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            if ($data->count() > 0) {
                                foreach ($data as $row) {
                                    $sumqty += $row->qty;
                                    $sumDis += $row->discount;;
                                    $amount = $row->rate  * $row->qty;
                                    $taxamountAmount = ($amount * $row->tax_rate) / 100;
                                    $ttamount = $amount + $taxamountAmount;
                                    $finalAmt=$ttamount-$row->discount??0;
                                    $sumtotal += $finalAmt;
                                }
                            }
                            if ($return->count() > 0) {
                                foreach ($return as $row) {
                                    $sumRqty += $row->qty;
                                    $sumRDis += $row->discount;
                                    $amount = ($row->rate ) * $row->qty;
                                    $taxamountAmount = ($amount * $row->tax_rate) / 100;
                                    $ttamount = $amount + $taxamountAmount;
                                    $finalAmt=$ttamount-$row->discount??0;
                                    $sumRtotal += $finalAmt;
                                }
                            }
                        @endphp
                        @foreach ($data as $row)
                            <tr>
                                <td>{{ 'SIN-' . $row->v_no }}</td>
                                <td>{{ date('d-m-Y', strtotime($row->master->v_date)) }}</td>
                                <td>{{ $row->customer->name ?? '' }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ number_format($row->rate, 2) }}</td>
                                @php
                                    $amount = ($row->rate  * $row->qty);
                                    $taxamountAmount = ($amount * $row->tax_rate) / 100;
                                @endphp
                                <td>{{ number_format($taxamountAmount, 2) }}</td>
                                @php
                                    $totalDiscounted = $row->rate * $row->qty + $taxamountAmount;
                                @endphp
                                <td>{{ number_format($totalDiscounted, 2) }}</td>
                                
                                <td>{{ number_format($row->discount, 2) }}</td>


                                <td>{{ number_format(($totalDiscounted-$row->discount), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Total</td>
                            <td>{{ $sumqty }}</td>
                            <td></td>
                            <td colspan="3" class="text-end">{{ number_format($sumDis, 2) }}</td>
                            <td >{{ number_format($sumtotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <h4>Sale Return</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                             <th>Voucher No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Tax</th>
                            <th>Inclusive</th>
                            <th>Discount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($return as $row)
                            <tr>
                                <td>{{ 'SRN-' . $row->v_no }}</td>
                                <td>{{ date('d-m-Y', strtotime($row->master->v_date)) }}</td>
                                <td>{{ $row->customer->name ?? '' }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ number_format($row->rate, 2) }}</td>
                                @php
                                    $amount = ($row->rate  * $row->qty);
                                    $taxamountAmount = ($amount * $row->tax_rate) / 100;
                                @endphp
                                <td>{{ number_format($taxamountAmount, 2) }}</td>
                                @php
                                    $totalDiscounted = $row->rate * $row->qty + $taxamountAmount;
                                @endphp
                                <td>{{ number_format($totalDiscounted, 2) }}</td>
                                
                                <td>{{ number_format($row->discount, 2) }}</td>


                                <td>{{ number_format(($totalDiscounted-$row->discount), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Total</td>
                            <td>{{ $sumRqty }}</td>
                            <td></td>
                            <td colspan="3" class="text-end">{{ number_format($sumRDis, 2) }}</td>
                            <td >{{ number_format($sumRtotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Net Total Section -->
                <div class="mt-12 p-3 border">
                    <h4>Net Total</h4>
                    <div class="row text-center">

                        <div class="col-md-4">
                            <h6>Net Amount</h6>
                            <h5>
                                {{ number_format($sumtotal - $sumRtotal, 2) }}
                            </h5>
                        </div>
                        <div class="col-md-4">
                            <h6>Net Discount</h6>
                            <h5>
                                {{ number_format($sumDis - $sumRDis, 2) }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Print Footer -->
        <div class="mt-4 text-center no-print">
            <small class="text-muted">Report generated on: {{ date('d/m/Y H:i:s') }}</small>
        </div>
    </div>

    <script>
        function printTable() {
            // Save original contents before modifying
            const originalContents = document.body.innerHTML;

            const companyName = "{{ Auth::user()->company->name ?? 'Company Name' }}";
            const companyAddress = `{!! addslashes(str_replace(["\r", "\n"], ['', '<br>'], Auth::user()->company->address ?? 'N/A')) !!}`;

            const companyEmail = "{{ Auth::user()->company->email ?? 'N/A' }}";
            const companyLogo =
                "{{ Auth::user()->company?->logo ? asset('storage/' . Auth::user()->company?->logo) : '' }}";

            let printContents;
            if ({{ $summarize ? 'true' : 'false' }}) {
                printContents = document.querySelector('#summaryreport').outerHTML;
            } else {
                printContents = document.querySelector('#detailreport').outerHTML;
            }

            document.body.innerHTML = `
        <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; color: #333; }
                    .print-header { margin-bottom: 20px; padding-bottom: 15px; }
                    .company-logo { max-width: 150px; max-height: 80px; }
                    .company-name { font-size: 24px; font-weight: bold; }
                    .company-details { font-size: 18px; margin-top: 10px; }
                    .print-title { text-align: center; font-size: 20px; font-weight: bold; margin: 20px 0; text-transform: uppercase; }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <table width="100%">
                        <tr>
                            <td width="20%">
                                ${companyLogo ? `<img class="company-logo" src="${companyLogo}" alt="Company Logo">` : ''}
                            </td>
                            <td width="80%" style="padding-left: 20px;">
                                <div class="company-name">${companyName}</div>
                                <div class="company-details" style="width: 400px; word-wrap: break-word; line-height: 1.3;">
                                    ${companyAddress}<br>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                ${printContents}
            </body>
        </html>
    `;

            window.print();

            // Restore original content
            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
