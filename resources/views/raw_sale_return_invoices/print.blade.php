@extends('layouts.app')

@section('content')
<div class="container bg-white p-5" id="printableArea">
    <div class="row mb-4">
        <div class="col-6">
            <h3>Raw Sale Return Invoice</h3>
            <p><strong>Voucher No:</strong> {{ $rmaster->v_no }}</p>
            <p><strong>Date:</strong> {{ $rmaster->v_date }}</p>
        </div>
        <div class="col-6 text-end">
            <h4>Customer Details</h4>
            <p><strong>Name:</strong> {{ $customer->name ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $customer->address ?? '' }}</p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Exclusive</th>
                <th>Tax %</th>
                <th>Tax Amt</th>
                <th>Disc %</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                @php
                    $lineTotal = $item->inclusive - $item->discount;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item->item_code ?? 'N/A' }}</td>
                    <td>{{ $item->item->item_name ?? '' }}</td>
                    <td>{{ number_format($item->qty, 2) }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td>{{ number_format($item->exclusive, 2) }}</td>
                    <td>{{ $item->tax_rate }}%</td>
                    <td>{{ number_format($item->tax_amount, 2) }}</td>
                    <td>{{ $item->discount_perc }}%</td>
                    <td>{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="9" class="text-end">Subtotal:</th>
                <td>{{ number_format($rmaster->grand_total + $rmaster->discount, 2) }}</td>
            </tr>
            <tr>
                <th colspan="9" class="text-end">Grand Discount:</th>
                <td>{{ number_format($rmaster->discount, 2) }}</td>
            </tr>
            <tr>
                <th colspan="9" class="text-end">Grand Total:</th>
                <td><strong>{{ number_format($rmaster->grand_total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-5 row">
        <div class="col-4 border-top text-center pt-2">Prepared By</div>
        <div class="col-4"></div>
        <div class="col-4 border-top text-center pt-2">Authorized Signature</div>
    </div>
</div>

<div class="container mt-3 no-print">
    <button onclick="window.print()" class="btn btn-primary">Print Now</button>
    <a href="{{ route('raw_sale_return_invoices.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<style>
    @media print {
        .no-print, .navbar, .leftside-menu, .footer {
            display: none !important;
        }
        .content-page {
            margin-left: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endsection
