@extends('layouts.app')

@section('content')
    <div class="container">
        @php
            $sumqty = 0;
            $sumDis = 0;
            $sumtotal = 0;
            $sumRqty = 0;
            $sumRDis = 0;
            $sumRtotal = 0;
        @endphp
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">


                    <h4>Purchase Wise Report</h4>
                </div>
            </div>
        </div>


        <form method="GET" action="{{ route('PurchaseWise.report') }}" class="mb-4 row">
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

        </form>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-success">Show</button>
            <a href="{{ route('PurchaseWise.report') }}" class="btn btn-secondary">Reset</a>
        </div>
        <button class="btn btn-warning" onclick="printTable()" style="margin-top: 20px;">
            Print
        </button>
        @php

            $allqty = 0;
            $allAmount = 0;
            $allDiscount = 0;
            $allNet = 0;
            $allRqty = 0;
            $allRAmount = 0;
            $allRDiscount = 0;
            $allRNet = 0;
        @endphp
        <div id="detailreport">
            <!-- 🧾 DETAILED VIEW -->
            <h4>Purchase Invoice</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Supplier</th>
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
                                $sumDis += $row->discount;
                                $amount = $row->rate * $row->qty;
                                $taxamountAmount = ($amount * $row->tax_rate) / 100;
                                $ttamount = $amount + $taxamountAmount;
                                $sumtotal += $ttamount;
                            }
                        }
                        if ($return->count() > 0) {
                            foreach ($return as $row) {
                                $sumRqty += $row->qty;
                                $sumRDis += $row->discount;
                                $amount = ($row->rate ) * $row->qty;
                                $taxamountAmount = ($amount * $row->tax_rate) / 100;
                                $ttamount = $amount + $taxamountAmount;
                                $sumRtotal += $ttamount;
                            }
                        }
                    @endphp
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ 'PIN-' . $row->v_no }}</td>
                            <td>{{ date('d-m-Y', strtotime($row->master->v_date)) }}</td>
                            <td>{{ $row->supplier->name ?? '' }}</td>
                            <td>{{ $row->qty }}</td>
                            <td>{{ number_format($row->rate, 2) }}</td>
                            @php
                                $amount = $row->rate * $row->qty;
                                $taxamountAmount = ($amount * $row->tax_rate) / 100;
                            @endphp
                            <td>{{ number_format($taxamountAmount, 2) }}</td>
                            @php
                                $totalDiscounted = $row->rate * $row->qty + $taxamountAmount;
                            @endphp
                            <td>{{ number_format($totalDiscounted, 2) }}</td>

                            <td>{{ number_format($row->discount, 2) }}</td>


                            <td>{{ number_format($totalDiscounted - $row->discount, 2) }}</td>
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

            <h4>Purchase Return</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Supplier</th>
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
                            <td>{{ 'PRN-' . $row->v_no }}</td>
                            <td>{{ date('d-m-Y', strtotime($row->master->v_date)) }}</td>
                            <td>{{ $row->supplier->name ?? '' }}</td>
                            <td>{{ $row->qty }}</td>
                            <td>{{ number_format($row->rate, 2) }}</td>
                            @php
                                $amount = $row->rate * $row->qty;
                                $taxamountAmount = ($amount * $row->tax_rate) / 100;
                            @endphp
                            <td>{{ number_format($taxamountAmount, 2) }}</td>
                            @php
                                $totalDiscounted = $row->rate * $row->qty + $taxamountAmount;
                            @endphp
                            <td>{{ number_format($totalDiscounted, 2) }}</td>

                            <td>{{ number_format($row->discount, 2) }}</td>


                            <td>{{ number_format($totalDiscounted - $row->discount, 2) }}</td>
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

            printContents = document.querySelector('#detailreport').outerHTML;
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
