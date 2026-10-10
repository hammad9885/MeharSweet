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
                <h4 class="page-title">Add Suppliers</h4>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Sequence Number</label>
                        <input type="text" name="seq_number" value="{{ $next_id }}" class="form-control" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Order Number</label>
                        <input type="text" name="order_number" value="{{ old('order_number') }}" class="form-control @error('order_number') is-invalid @enderror">
                        @error('order_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>CNIC</label>
                        <input type="text" name="cnic" value="{{ old('cnic') }}" class="form-control @error('cnic') is-invalid @enderror">
                        @error('cnic')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Urdu Title</label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}" placeholder="Enter Urdu title" class="form-control @error('mobile') is-invalid @enderror">
                        @error('mobile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-control @error('city') is-invalid @enderror">
                        @error('city')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror">
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>NTN</label>
                        <input type="text" name="ntn" value="{{ old('ntn') }}" class="form-control @error('ntn') is-invalid @enderror">
                        @error('ntn')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection