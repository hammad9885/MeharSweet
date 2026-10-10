<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Invoice #{{ $master->v_no }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-size: 13px; }
        .invoice-box { padding:20px; border:1px solid #ddd; margin:20px auto; width:800px; background:#fff; }
        .table th, .table td { vertical-align:middle !important; }
        .text-right { text-align:right; }
        .signature-area { margin-top:60px; display:flex; justify-content:space-between; text-align:center; }
        .signature { width:30%; border-top:1px solid #000; padding-top:5px; font-weight:bold; }
        @media print { .no-print { display:none; } .invoice-box { border:none; width:100%; margin:0; } }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="text-center mb-4">
            <h4><strong>Purchase Invoice</strong></h4>
            <p>Voucher #: <strong>{{ $master->v_no }}</strong></p>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <h6><strong>Supplier Details</strong></h6>
                @if($supplier)
                    <p><strong>{{ $supplier->name }}</strong><br>{{ $supplier->address ?? '' }}<br>{{ $supplier->phone ?? '' }}</p>
                @endif
                <p><strong>Ref No:</strong> {{ $master->ref_no ?? 'N/A' }}</p>
                <p><strong>Remarks:</strong> {{ $master->remarks ?? 'N/A' }}</p>
            </div>
            <div class="col-6 text-end">
                <h6><strong>Date:</strong> {{ $master->v_date }}</h6>
                <h6><strong>Gate Pass No:</strong> {{ $master->gate_pass_no ?? 'N/A' }}</h6>
                <h6><strong>Prepared By:</strong> {{ auth()->user()->name }}</h6>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Lot No</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach($items as $index => $item)
                    @php $totalAmount += $item->exclusive; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item->item_code ?? 'N/A' }}</td>
                        <td>{{ $item->lot_no ?? '-' }}</td>
                        <td>{{ number_format($item->qty, 2) }}</td>
                        <td>{{ number_format($item->rate, 2) }}</td>
                        <td class="text-end">{{ number_format($item->exclusive, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="5" class="text-end">Subtotal:</th><th class="text-end">{{ number_format($totalAmount, 2) }}</th></tr>
                <tr><th colspan="5" class="text-end">Grand Discount:</th><th class="text-end">{{ number_format($master->discount ?? 0, 2) }}</th></tr>
                <tr><th colspan="5" class="text-end">Grand Total:</th><th class="text-end">{{ number_format($master->grand_total, 2) }}</th></tr>
                <tr><th colspan="5" class="text-end">Freight:</th><th class="text-end">{{ number_format($master->freight ?? 0, 2) }}</th></tr>
                <tr><th colspan="5" class="text-end">Net Amount:</th><th class="text-end">{{ number_format($master->net_amount, 2) }}</th></tr>
            </tfoot>
        </table>

        <div class="signature-area">
            <div class="signature">Prepared By<br>{{ auth()->user()->name }}</div>
            <div class="signature">Checked By</div>
            <div class="signature">Approved By</div>
        </div>

        <div class="text-center no-print mt-4">
            <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
            <a href="{{ route('purchase_invoices.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>
</html>
