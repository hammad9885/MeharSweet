@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Finished Product  Stock Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Finished Product Stock Report</h4>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body border rounded">
            <form method="GET" action="{{ route('currentp') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label><b>Item</b></label>
                        <select name="item_id" class="form-control select2">
                            <option value="">Select Item</option>
                            @foreach($allItems as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                <label for="">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                 <label for="">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-success">Filter</button>
                        <a href="{{ route('currentp') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            @if(count($items)>0)
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Item Code</th>
                            <th>Opening Qty</th>
                            <th>Sale Qty</th>
                            <th>Production</th>
                            <th>Sale Return Qty</th>
                            <th>Adjustment </th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- {{ dd($items) }} --}}
                        @foreach($items as $item)
                        {{-- {{ dd($item) }} --}}
                         @if($item != "")
                            <tr>
                                <td>{{ $item['item_code'] }}</td>
                                <td>{{ $item['opening_qty'] }}</td>
                                <td>{{ $item['sale_qty'] }}</td>
                                <td>{{ $item['production_qty'] }}</td>
                                <td>{{ $item['sale_return_qty'] }}</td>
                                <td>{{ $item['adjustment_qty'] }}</td>
                                <td><strong>{{ $item['current_stock']}}</strong></td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info text-center mb-0">
                    No records found. Please select an item or adjust filters.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection