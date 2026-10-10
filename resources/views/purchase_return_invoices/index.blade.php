@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                            <li class="breadcrumb-item active">Data Tables</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Purchase Return </h4>
                </div>
            </div>
        </div>
    <a href="{{ route('purchase_return_invoices.create') }}" class="btn btn-primary mb-3">Create New Invoice</a>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('purchase_return_invoices.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="voucher_no" class="form-control" placeholder="Voucher No" value="{{ request('voucher_no') }}">
            </div>
            <div class="col-md-3">
                <select name="supplier_id" class="form-control">
                    <option value="">Select Supplier</option>
                    @foreach(App\Models\Supplier::where('cid',auth()->user()->cid)->get() as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="item_id" class="form-control">
                    <option value="">Select Item</option>
                    @foreach(App\Models\ItemMaster::where('cid',auth()->user()->cid)->get() as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-success">Filter</button>
                <a href="{{ route('purchase_return_invoices.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Voucher No</th>
                <th>Supplier</th>
                <th>Total Items</th>
                <th>Grand Total</th>
                <th>Discount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $master)
            <tr>
                <td>{{ $master->v_no }}</td>
                <td>{{ $master->items->first()->supplier->name ?? '' }}</td>
                <td>{{ $master->items->count() }}</td>
                <td>{{ number_format($master->grand_total, 2) }}</td>
                <td>{{ number_format($master->discount, 2) }}</td>
                <td>
                    <a href="{{ route('purchase_return_invoices.edit', $master->v_no) }}" class="btn btn-sm btn-warning">Edit</a>
                           <a href="{{ route('purchase_return_invoices.print', $master->v_no) }}" class="btn btn-sm btn-outline-primary" target="_blank">
    <i class="bi bi-printer"></i> Print
</a>
                    <form action="{{ route('purchase_return_invoices.destroy', $master->v_no) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach                                                                                                                                                 
        </tbody>
    </table>
</div>
@endsection
