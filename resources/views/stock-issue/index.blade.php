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
                            <li class="breadcrumb-item active">Stock Issue</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Stock Issue</h4>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <a href="{{ route('stock-issue.create') }}" class="btn btn-primary mb-3">Create New Stock Issue</a>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('stock-issue.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="inv_no">Inv No</label>
                    <input type="text" name="inv_no" class="form-control" placeholder="Inv No" value="{{ request('inv_no') }}">
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
                    <label for="contractor_id">Contractor</label>
                    <select name="contractor_id" class="form-control">
                        <option value="">Select Contractor</option>
                        @foreach ($contractors as $contractor)
                            <option value="{{ $contractor->id }}" {{ request('contractor_id') == $contractor->id ? 'selected' : '' }}>{{ $contractor->title }}</option>
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
                    <a href="{{ route('stock-issue.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Inv No</th>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Contractor</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($masters as $master)
                    <tr>
                        <td>{{ $master->inv_no }}</td>
                        <td>{{ date('d/m/Y', strtotime($master->date)) }}</td>
                        <td>{{ $master->product->product_name ?? ' ' }}</td>
                        <td>{{ $master->contractor->title ?? ' ' }}</td>
                        <td>{{ $master->details->sum('qty') }}</td>
                        <td>
                            <a href="{{ route('stock-issue.edit', $master->id) }}" class="btn btn-sm btn-warning" style="width: 80px">Edit</a>
                            <form action="{{ route('stock-issue.destroy', $master->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" style="width: 80px" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No Stock Issue entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
