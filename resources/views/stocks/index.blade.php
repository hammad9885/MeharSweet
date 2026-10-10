@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Stock Summary Report</h3>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form action="{{ route('stocks.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="item_id">Select Item</label>
                            <select name="item_id" id="item_id" class="form-control">
                                <option value="">All Items</option>
                                @foreach($allitems as $item)
                                    <option value="{{ $item->id }}" 
                                        {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->item_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                   value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Stock Table -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2">Item Code</th>
                            <th colspan="2" class="text-center">Opening Stock</th>
                            <th colspan="2" class="text-center">Net Purchase</th>
                            <th colspan="2" class="text-center">Adjustment</th>
                            <th colspan="2" class="text-center">Net Sale</th>
                            <th colspan="2" class="text-center">Production</th>
                            <th colspan="2" class="text-center">Consumed</th>
                            <th colspan="2" class="text-center">Closing Stock</th>
                        </tr>
                        <tr>
                            <th>Qty</th><th>Amt</th>
                            <th>Qty</th><th>Amt</th>
                            <th>Qty</th><th>Amt</th>
                            <th>Qty</th><th>Amt</th>
                            <th>Qty</th><th>Amt</th>
                            <th>Qty</th><th>Amt</th>
                            <th>Qty</th><th>Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $stock)
                            <tr>
                                <td>{{ $stock['item_code'] }}</td>

                                <!-- Opening -->
                                <td class="text-right">{{ $stock['opening_qty'] }}</td>
                                <td class="text-right">{{ number_format($stock['opening_amount'], 2) }}</td>

                                <!-- Net Purchase (Purchase - Purchase Return) -->
                                @php
                                    $net_purchase_qty = $stock['purchase_qty'] + $stock['purchase_return_qty'];
                                    $net_purchase_amt = $stock['purchase_amount'] + $stock['purchase_return_amount'];
                                @endphp
                                <td class="text-right">{{ $net_purchase_qty }}</td>
                                <td class="text-right">{{ number_format($net_purchase_amt, 2) }}</td>

                                <!-- Adjustment -->
                                <td class="text-right">{{ $stock['adjustment_qty'] }}</td>
                                <td class="text-right">{{ number_format($stock['adjustment_amount'], 2) }}</td>

                                <!-- Net Sale (Sale - Sale Return) -->
                                @php
                                    $net_sale_qty = $stock['sale_qty'] + $stock['sale_return_qty']; // if sale return included in data, subtract here
                                    $net_sale_amt = $stock['sale_amount'] +$stock['sale_return_amount'];
                                @endphp
                                <td class="text-right">{{ $net_sale_qty }}</td>
                                <td class="text-right">{{ number_format($net_sale_amt, 2) }}</td>

                                <!-- Production -->
                                <td class="text-right">{{ $stock['prod_qty'] }}</td>
                                <td class="text-right">{{ number_format($stock['prod_amount'], 2) }}</td>

                                <!-- Consumed -->
                                <td class="text-right">{{ $stock['consumed_qty'] }}</td>
                                <td class="text-right">{{ number_format($stock['consumed_amount'], 2) }}</td>

                                <!-- Closing Stock -->
                                <td class="text-right">{{ $stock['closing_qty'] }}</td>
                                <td class="text-right">{{ number_format($stock['closing_amt'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">No stock records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection