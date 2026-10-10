@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Production Voucher</h4>
            <button class="btn btn-outline-primary" onclick="window.print()">🖨️ Print</button>
        </div>

        <table class="table table-borderless mb-4">
            <tr>
                <th>Voucher No:</th>
                <td>{{ $production->v_no }}</td>
                <th>Date:</th>
                <td>{{ \Carbon\Carbon::parse($production->date)->format('d-M-Y') }}</td>
            </tr>
            <tr>
                <th>Description:</th>
                <td colspan="3">{{ $production->descr ?? '-' }}</td>
            </tr>
            <tr>
                <th>Total Amount:</th>
                <td colspan="3"><strong>{{ number_format($production->amount, 2) }}</strong></td>
            </tr>
        </table>

        <h5 class="fw-bold mb-3">Production Details</h5>
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($production->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->item->item_code ?? 'N/A' }}</td>
                        <td>{{ $detail->qty }}</td>
                        <td>{{ number_format($detail->rate, 2) }}</td>
                        <td>{{ number_format($detail->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No details found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-end mt-4">
            <strong>Printed on:</strong> {{ now()->format('d-M-Y H:i A') }}
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, nav, footer, header { display: none !important; }
        body { background: white !important; }
        .card { box-shadow: none !important; border: none; }
    }
</style>
@endsection
