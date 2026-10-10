@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Customers</h2>
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
                <th>City</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Account</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                 <td>{{ $loop->iteration }}</td>
                <td>{{ $customer->city }}</td>
                <td>{{ $customer->mobile }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->accountMaster->name ?? '-' }}</td>
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
