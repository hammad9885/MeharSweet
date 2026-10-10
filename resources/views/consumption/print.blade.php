<!DOCTYPE html>
<html>
<head>
    <title>Consumption Voucher #{{ $consumption->v_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #fff;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #000;
        }

        @media print {
            .no-print { display: none; }
            body { margin: 10mm; }
        }

        .voucher-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .voucher-header h3 {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-weight: bold;
        }

        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            padding: 0 50px;
        }

        .signature {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <div class="no-print mb-3 text-end">
            <button class="btn btn-primary" onclick="window.print()">🖨 Print</button>
            <a href="{{ route('consumption.index') }}" class="btn btn-secondary">⬅ Back</a>
        </div>

        <div class="voucher-header">
            <h3>Consumption Voucher</h3>
            <p><strong>Voucher No:</strong> {{ $consumption->v_no }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($consumption->date)->format('d-M-Y') }}</p>
        </div>

        <div class="mb-4">
            <p><strong>Product:</strong> {{ $consumption->item->item_code ?? 'N/A' }}</p>
            <p><strong>Quantity:</strong> {{ $consumption->qty }}</p>
            <p><strong>Cost Price:</strong> {{ number_format($consumption->cost_price, 2) }}</p>
            <p><strong>Description:</strong> {{ $consumption->descr ?? '-' }}</p>
        </div>

        <table>
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Raw Material</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; $totalAmount = 0; @endphp
                @foreach($consumption->details as $detail)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $detail->item[0]->item_code ?? 'N/A' }}</td>
                        <td>{{ $detail->qty ?? 0 }}</td>
                        <td>{{ number_format($detail->rate ?? 0, 2) }}</td>
                        <td>{{ number_format($detail->amount ?? 0, 2) }}</td>
                         @php
                             $totalAmount += $detail->amount ?? 0;
                         @endphp
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th>
                       {{ number_format($consumption->amount, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <div class="footer mt-3">
            Total Consumption Value: {{ number_format($consumption->amount, 2) }}
        </div>

        <div class="signature-area mt-5">
            <div class="signature">Prepared By</div>
            <div class="signature">Checked By</div>
            <div class="signature">Approved By</div>
        </div>
    </div>
</body>
</html>
