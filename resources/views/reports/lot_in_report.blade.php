@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Stock Management</a></li>
                            <li class="breadcrumb-item active">Lot In Report</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Lot In Report</h4>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('lot-in-report.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="product_id">Product</label>
                    <select name="product_id" class="form-control">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="lot_no">Lot No</label>
                    <input type="text" name="lot_no" class="form-control" placeholder="Lot No" value="{{ request('lot_no') }}">
                </div>
                <div class="col-md-3">
                    <label for="contractor_id">Contractor</label>
                    <select name="contractor_id" class="form-control">
                        <option value="">Select Contractor</option>
                        @foreach ($contractors as $contractor)
                            <option value="{{ $contractor->id }}" {{ request('contractor_id') == $contractor->id ? 'selected' : '' }}>{{ $contractor->title ?? $contractor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', '2026-01-01') }}">
                </div>
                <div class="col-md-2">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="{{ route('lot-in-report.index') }}" class="btn btn-secondary">Reset</a>
                    <a href="{{ route('lot-in-report.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary ms-1">
                        Print Report
                    </a>
                </div>
            </div>
        </form>

        @php
            $uniqueCartons = array_unique(array_filter(array_column($reportData, 'cartons_in_lot')));
            $cartonsDisplay = count($uniqueCartons) > 0 ? implode(', ', $uniqueCartons) : '0';
            $totalPieces = array_sum(array_column($reportData, 'pieces'));
            $uniqueQtyPerCtn = array_unique(array_filter(array_column($reportData, 'qty_per_carton')));
            $qtyPerCtnDisplay = count($uniqueQtyPerCtn) > 0 ? implode(', ', $uniqueQtyPerCtn) : '0';
            $selectedProd = $products->firstWhere('id', request('product_id'));
            if ($selectedProd) {
                $itemTitleDisplay = $selectedProd->product_name;
            } else {
                $uniqueTitles = array_unique(array_filter(array_column($reportData, 'item_title')));
                $itemTitleDisplay = count($uniqueTitles) > 0 ? implode(', ', $uniqueTitles) : 'All Items';
            }
        @endphp

        <!-- Summary Text Boxes -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Item Title</label>
                <input type="text" class="form-control" readonly value="{{ $itemTitleDisplay }}">
            </div>
            <div class="col-md-3">
                <label>Cartons in Lot</label>
                <input type="text" class="form-control" readonly value="{{ $cartonsDisplay }}">
            </div>
            <div class="col-md-3">
                <label>Qty per Ctn</label>
                <input type="text" class="form-control" readonly value="{{ $qtyPerCtnDisplay }}">
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>V No</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Contractor</th>
                    <th>Lot No</th>
                    <th>Qty</th>
                    <th>Pieces</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQty = 0;
                @endphp
                @forelse ($reportData as $row)
                    @php
                        $totalQty += $row['stock_qty'];
                    @endphp
                    <tr>
                        <td>{{ $row['v_no'] }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['item_title'] }}</td>
                        <td>{{ $row['contractor'] }}</td>
                        <td>{{ $row['lot_no'] }}</td>
                        <td>{{ number_format($row['stock_qty']) }}</td>
                        <td>{{ number_format($row['pieces']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No Lot In entries found.</td>
                    </tr>
                @endforelse
                @if(count($reportData) > 0)
                    <tr>
                        <td colspan="5" style="text-align: right; font-weight: bold;">Total</td>
                        <td style="font-weight: bold;">{{ number_format($totalQty) }}</td>
                        <td style="font-weight: bold;">{{ number_format($totalPieces) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
