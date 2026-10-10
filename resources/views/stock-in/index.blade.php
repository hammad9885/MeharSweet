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
                            <li class="breadcrumb-item active">Stock In</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Stock In</h4>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <a href="{{ route('stock-in.create') }}" class="btn btn-primary mb-3">Create New Stock In</a>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('stock-in.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="inv_no">Inv No</label>
                    <input type="text" name="inv_no" class="form-control" placeholder="Inv No" value="{{ request('inv_no') }}">
                </div>
                <div class="col-md-3">
                    <label for="ref_name_id">Reference Name</label>
                    <select name="ref_name_id" class="form-control">
                        <option value="">Select Reference Name</option>
                        @foreach ($referenceNames as $rn)
                            <option value="{{ $rn->id }}" {{ request('ref_name_id') == $rn->id ? 'selected' : '' }}>{{ $rn->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="product_id">Product</label>
                    <select name="product_id" class="form-control">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date">Start Date</label>
                    <input type="date" name="startDate" class="form-control" value="{{ request('startDate') }}">
                </div>
                <div class="col-md-3">
                    <label for="date">End Date</label>
                    <input type="date" name="endDate" class="form-control" value="{{ request('endDate') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Inv No</th>
                    <th>Date</th>
                    <th>Reference Name</th>
                    <th>Total Items</th>
                    <th>Total Qty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($masters as $master)
                    <tr>
                        <td>{{ $master->inv_no }}</td>
                        <td>{{ date('d/m/Y', strtotime($master->date)) }}</td>
                        <td>{{ $master->refName->name ?? ' ' }}</td>
                        <td>{{ $master->details->count() }}</td>
                        <td>{{ $master->details->sum('qty') }}</td>
                        <td>
                            <a href="{{ route('stock-in.edit', $master->id) }}" class="btn btn-sm btn-warning" style="width: 80px">Edit</a>
                            <a href="{{ route('stock-in.print', $master->id) }}" class="btn btn-sm btn-outline-primary" style="width: 80px" target="_blank">
                                <i class="bi bi-printer"></i> Print
                            </a>
                            <form action="{{ route('stock-in.destroy', $master->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" style="width: 80px" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No Stock In entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
