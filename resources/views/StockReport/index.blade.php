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
                        <li class="breadcrumb-item active">Raw Material Stock Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Raw Material Stock Report</h4>
            </div>
        </div>
    </div>
    <!-- Filter Form -->
    <form method="GET" action="{{ route('current') }}" class="mb-4">
       
        <div class="row">
           
        
            <div class="col-md-3">
                <label for="">Item Name</label>
                <select name="item_id" class="form-control">
                    <option value="">Select Item</option>
                    @foreach(App\Models\ItemMaster::where('cid',auth()->user()->cid)->where('item_type','rp')->get() as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_code }}</option>
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
        <div class="row mt-2">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-success">Filter</button>
                <a href="{{ route('current') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            @if(count($items) > 0)
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Item Code</th>
                            <th>Opening Qty</th>
                            <th>Purchase Qty</th>
                            <th>Purchase Return Qty</th>
                            <th>Consumed Qty</th>
                            <th>Adjustment</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $itemId => $data)
                            <!-- Skip items with empty or invalid item_id -->
                            @if($itemId != "")
                                <tr>
                                    <td>{{ $data['item_code'] }}</td>
                                    <td>{{ $data['opening_qty'] }}</td>
                                    <td>{{ $data['purchase_qty'] }}</td>
                                    <td>{{ $data['purchase_return_qty'] }}</td>
                                    <td>{{ $data['consumed_qty'] }}</td>
                                    <td>{{ $data['adjustment_qty'] }}</td>
                                    <td>{{ $data['current_stock'] }}</td>
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