@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sale</a></li>
                        <li class="breadcrumb-item active">Sale Orders</li>
                    </ol>
                </div>
                <h4 class="page-title">Sale Orders <span class="badge bg-primary">{{ $totalOrders }}</span></h4>
            </div>
        </div>
    </div>

    <a href="{{ route('sale_order.create') }}" class="btn btn-primary mb-3">Create New Sale Order</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('sale_order.index') }}" class="mb-3" id="filterForm">
        <div class="row">
            <div class="col-md-3">
                <label>Voucher No</label>
                <input type="text" name="voucher_no" class="form-control" placeholder="Search by Voucher No" value="{{ request('voucher_no') }}">
            </div>
            <div class="col-md-3">
                <label>Customer</label>
                <select name="customer_id" class="form-control">
                    <option value="">All Customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->acc_id }}" {{ request('customer_id') == $customer->acc_id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Item</label>
                <select name="item_id" class="form-control select2-item">
                    <option value="">All Items</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ request('item_id') == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Start Date</label>
                <input type="date" name="startDate" class="form-control" value="{{ request('startDate', $startDate ?? now()->subMonths(2)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label>End Date</label>
                <input type="date" name="endDate" class="form-control" value="{{ request('endDate', $endDate ?? now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-12 mt-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success me-1">Search</button>
                <a href="{{ route('sale_order.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if ($selectedItemName && $totalItemQty > 0)
        <div class="alert alert-info">
            Total <strong>{{ $selectedItemName }}</strong> qty across filtered results: <strong>{{ $totalItemQty }}</strong>
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Voucher No</th>
                <th>Date</th>
                <th style="width: 200px">Customer</th>
                <th>Party Ref</th>
                <th>Items</th>
                <th style="width: 100px">DCs</th>
                <th style="width: 100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->v_no }}</td>
                <td>{{ $order->v_date }}</td>
                <td style=" font-size: 20px; font-weight:bold;">{{ $order->customer->name ?? 'N/A' }} @if($order->customer && $order->customer->urdu_title)<br><span dir="rtl" class="text-muted">{{ $order->customer->urdu_title }}</span>@endif</td>
                <td style="font-size: 20px; font-weight:bold;">
                    @if ($order->referenceName)
                        {{ $order->referenceName->name }}
                        @if ($order->referenceName->name_urdu)
                            <br><span dir="rtl" class="text-muted">{{ $order->referenceName->name_urdu }}</span>
                        @endif
                    @else
                        {{ $order->party_ref ?? '-' }}
                    @endif
                </td>
                <td>
                    <table class="table table-sm table-borderless mb-0">
                        @foreach ($order->details as $detail)
                        @php
                            $dcTotal = ($dcTotals[$order->v_no] ?? collect())->firstWhere('item_id', $detail->item_id);
                            $dcQty = $dcTotal ? $dcTotal->total_dc : 0;
                        @endphp
                        <tr>
                            <td style=" font-size: 20px; font-weight:bold;">{{ $detail->item->product_name ?? 'N/A' }}</td>
                            <td style=" font-size: 20px; font-weight:bold;">Rem: {{ $detail->qty + 0 }}</td>
                            <td style=" font-size: 20px; font-weight:bold;">DC: {{ $dcQty + 0 }}</td>
                        </tr>
                        @endforeach
                    </table>
                </td>
                <td>
                    @php $dcNums = $dcRefs[$order->v_no] ?? collect(); @endphp
                    @if ($dcNums->isNotEmpty())
                        {{ $dcNums->implode(', ') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('sale_order.edit', $order->id) }}" class="btn btn-sm btn-warning mb-1" style="width: 80px">Edit</a>
                    <a href="{{ route('sale_order.print', $order->id) }}" class="btn btn-sm btn-info mb-1" style="width: 80px" target="_blank">Print</a>
                    <a href="{{ route('sale_order_dc.create', ['customer_id' => $order->customer_id, 'sor_vno' => $order->v_no]) }}" class="btn btn-sm btn-success" style="width: 80px">DC</a>
                    <form action="{{ route('sale_order.destroy', $order->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" style="width: 80px">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single { height: calc(1.5em + 0.9rem + 2px); border: 1px solid #dee2e6; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + 0.9rem); padding-left: 0.9rem; }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-item').select2({ placeholder: "All Items", width: '100%', allowClear: true });

    $('.select2-item').on('change', function() {
        $('#filterForm').submit();
    });
});
</script>
@endsection
