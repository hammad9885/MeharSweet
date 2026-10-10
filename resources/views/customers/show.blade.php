@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Customer Details</h1>
    <p><strong>Name:</strong> {{ $customer->name }}</p>
    <p><strong>City:</strong> {{ $customer->city }}</p>
    <p><strong>Address:</strong> {{ $customer->address }}</p>
    <p><strong>Remark:</strong> {{ $customer->Remark }}</p>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection