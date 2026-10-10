@extends('layouts.app')
@section('content')
<div class="container">


   
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
                    <h4 class="page-title">Consumption Records</h4>
                </div>
            </div>
        </div>

    <form action="{{ route('consumption.index') }}" method="GET" id="search-form">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="item_id" class="form-label">Product</label>
                <select name="item_id" class="form-select select2" data-toggle="select2">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ request()->get('item_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Show</button>
                <a class="btn btn-success" href="{{ route('consumption.create') }}" onclick="return checkPermission()">Add New</a>
            </div>
        </div>
    </form>
 @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered" style="margin-top: 50px">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Product Qty</th>
                <th>Qty per Ctn</th>
                <th>Cartons in Lot</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consumptions as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $products[$c->item_id]->product_name ?? $c->item_id }}</td>
                    <td>{{ $c->qty }}</td>
                    <td>{{ $c->qty_per_carton }}</td>
                    <td>{{ $c->cartons_in_lot }}</td>
                    <td>
                        <a href="{{ route('consumption.edit', $c->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <a href="{{ route('consumption.print', $c->id) }}" class="btn btn-sm btn-info" target="_blank">Print</a>
                        <form action="{{ route('consumption.destroy', $c->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
