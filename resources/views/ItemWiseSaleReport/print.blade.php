<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Wise Sale Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f4f4f4;
        }

        .container {
            padding: 20px;
        }

        /* Fixed header for each printed page */
        @media print {
            @page {
                margin-top: 140px;
                margin-bottom: 60px;
            }

            .print-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 120px;
                text-align: center;
                background: #fff;
                border-bottom: 2px solid #000;
                padding: 10px;
            }

            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 12px;
                border-top: 1px solid #000;
                background: #fff;
                padding: 5px;
            }

            .print-footer::after {
                content: "Page " counter(page);
            }

            .container {
                margin-top: 20px;
            }
        }

        .company-logo {
            max-height: 60px;
            margin-bottom: 5px;
        }

        .company-details {
            text-align: center;
            font-size: 14px;
            line-height: 1.4;
        }

        .report-title {
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- 🔹 Print Header -->
    <div class="print-header">
        <div class="company-details">
            @if(!empty(Auth::user()->company?->logo))
                <img src="{{ asset('storage/' . Auth::user()->company?->logo) }}" class="company-logo" alt="Company Logo"><br>
            @endif
            <strong>{{ Auth::user()->company->name ?? 'Company Name' }}</strong><br>
            {{ Auth::user()->company->address ?? '' }}<br>
            <small>
                Phone: {{ Auth::user()->company->phone ?? 'N/A' }} |
                Email: {{ Auth::user()->company->email ?? 'N/A' }}
            </small>
            <div class="report-title">Item Wise Sale Report</div>
            <div><small>Date: {{ now()->format('d-m-Y') }}</small></div>
        </div>
    </div>

    <div class="container">
        @if($summarize)
            <!-- ✅ Summary Report -->
            <h3 style="text-align:center; margin-bottom:10px;">Item Wise Sale Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Sale qty</th>
                        <th>Return qty</th>
                        <th>Net qty</th>
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
                    @foreach($report as $row)
                        <tr>
                            <td style="text-align: left;">{{ $row['item'] }}</td>
                            <td>{{ $row['sale_qty'] }}</td>
                            <td>{{ $row['return_qty'] }}</td>
                            <td>{{ $row['net_qty'] }}</td>
                            <td>{{ number_format($row['sale_amount'], 2) }}</td>
                            <td>{{ number_format($row['return_amount'], 2) }}</td>
                            <td>{{ number_format($row['net_amount'], 2) }}</td>
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
                    <tr style="font-weight:bold;">
                        <td>Total</td>
                        <td>{{ $totSaleqty }}</td>
                        <td>{{ $totReturnqty }}</td>
                        <td>{{ $totNetqty }}</td>
                        <td>{{ number_format($totSaleAmt, 2) }}</td>
                        <td>{{ number_format($totReturnAmt, 2) }}</td>
                        <td>{{ number_format($totNetAmt, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <!-- 🧾 Detailed Report -->
            <h3 style="text-align:center; margin-bottom:10px;">Detailed Item Sale Report</h3>

            <h4>Sale Invoice</h4>
            <table>
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ 'SIN-'.$row->v_no }}</td>
                             <td>{{ date('d-m-Y', strtotime($row->master->v_date)) }}</td>
                    
                            <td>{{ $row->customer->name ?? '' }}</td>
                            <td>{{ $row->qty }}</td>
                            <td>{{ number_format($row->rate, 2) }}</td>
                            <td>{{ number_format($row->discount, 2) }}</td>
                            <td>{{ number_format(($row->rate - $row->discount) * $row->qty, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <br>

            <h4>Sale Return</h4>
            <table>
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return as $row)
                        <tr>
                            <td>{{ 'SRN-'.$row->v_no }}</td>
                          <td>{{ date('d-m-Y', strtotime($row->master->v_date)) }}</td>
                            <td>{{ $row->customer->name ?? '' }}</td>
                            <td>{{ $row->qty }}</td>
                            <td>{{ number_format($row->rate, 2) }}</td>
                            <td>{{ number_format($row->discount, 2) }}</td>
                            <td>{{ number_format(($row->rate - $row->discount) * $row->qty, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- 🔹 Print Footer -->
    <div class="print-footer">
        Printed on: {{ now()->format('d-m-Y h:i A') }}
    </div>

</body>
</html>
