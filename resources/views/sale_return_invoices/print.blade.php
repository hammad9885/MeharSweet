<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sale Return #{{ $master->v_no }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body { font-size: 13px; }
        .invoice-box {
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px auto;
            width: 800px;
            background: #fff;
        }
        .table th, .table td { vertical-align: middle !important; }
        .text-right { text-align: right; }
        .signature-area {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .signature {
            width: 30%;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; width: 100%; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="text-center mb-4">
            <h4><strong>Sale Return </strong></h4>
            <p>Voucher #: <strong>{{ $rmaster->v_no }}</strong></p>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <h6><strong>Customer Details</strong></h6>
                @if($customer)
                    <p>
                        <strong>{{ $customer->name }}</strong><br>
                        {{ $customer->address ?? '' }}<br>
                        {{ $customer->phone ?? '' }}
                    </p>
                @endif
            </div>
            {{-- {{ $master->gate_pass_no }} --}}
            <div class="col-6 text-end">
                <h6><strong>Date:</strong> {{ $rmaster->v_date }}</h6>
                <h6><strong>Gate Pass No:</strong> {{ $rmaster->gate_pass_no ??"N/A" }}</h6>
                <h6><strong>Prepared By:</strong> {{ auth()->user()->name }}</h6>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Exclusive</th>
                    <th>Tax %</th>
                    <th>Tax Amt</th>
                    <th>Inclusive</th>
                    <th>Discount %</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalExclusive = $totalTax = $totalInclusive=$gtot = 0; @endphp
                @foreach($items as $index => $item)
                    @php
                        $totalExclusive += $item->exclusive;
                        $totalTax += $item->tax_amount;
                        $totalInclusive += $item->inclusive;
                        $discount=$item->discount;
                        $tInc=$item->inclusive;
                        $total=$tInc-$discount;
                        $gtot += $total;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item->product_name ?? 'N/A' }}</td>
                        <td>{{ number_format($item->qty, 2) }}</td>
                        <td>{{ number_format($item->rate, 2) }}</td>
                        <td class="text-end">{{ number_format($item->exclusive, 2) }}</td>
                        <td class="text-end">{{ number_format($item->tax_rate, 2) }}</td>
                        <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($item->inclusive, 2) }}</td>
                         <td class="text-end">{{ number_format($item->discount_perc, 2) }}</td>
                         <td class="text-end">{{ number_format($total, 2) }}</td>
                         
                    </tr>
                @endforeach
            </tbody>                                                        
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Subtotal:</th>
                    <th class="text-end">{{ number_format($totalExclusive, 2) }}</th>
                    <th></th>
                    <th class="text-end">{{ number_format($totalTax, 2) }}</th>
                    <th class="text-end">{{ number_format($totalInclusive, 2) }}</th>
                    <td class="text-end"></td>
                          <td class="text-end">{{ number_format($gtot, 2)}}</td>
                </tr>
                <tr>
                    <th colspan="9" class="text-end">Grand Discount:</th>
                    <th class="text-end">{{ number_format($rmaster->discount ?? 0, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="9" class="text-end">Grand Total:</th>
                    <th class="text-end">{{ number_format($rmaster->grand_total, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="signature-area">
            <div class="signature">Prepared By<br>{{ auth()->user()->name }}</div>
            <div class="signature">Checked By</div>
            <div class="signature">Approved By</div>
        </div>

        <div class="text-center no-print mt-4">
            <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
            <a href="{{ route('sale_return_invoices.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>
</html>
