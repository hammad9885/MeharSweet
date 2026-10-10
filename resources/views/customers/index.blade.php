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
                    <h4 class="page-title">Customers</h4>
                </div>
            </div>
        </div>

    <form method="GET" class="row g-2 align-items-center mb-3">
        <div class="col-auto">
            <select name="search" class="form-control select2" style="width:400px;">
                <option value="">-- Search by Name --</option>
                @foreach($customerNames as $name)
                    <option value="{{ $name }}" {{ request('search') == $name ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-success">Search</button>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <a href="{{ route('customers.create') }}" class="btn btn-primary">Add customer</a>
     @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    
    @endif
    @if (session('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    
    @endif
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Transport Company</th>
                <th>Name</th>
                <th>Urdu Title</th>
                <th>City</th>
                <th>Mobile</th>
                
                
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                 <td>{{ $loop->iteration }}</td>
                <td>{{ $customer->company_title }}</td>
                <td>{{ $customer->name }}</td>
                <td dir="rtl" class="text-end">{{ $customer->urdu_title }}</td>
                <td>{{ $customer->city }}</td>
                <td>{{ $customer->mobile }}</td>
                
                
                <td>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this customer?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ placeholder: '-- Search by Name --', width: '100%', allowClear: true });
    });
</script>
