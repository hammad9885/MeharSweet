@extends('layouts.app')
@section('content')  
    <div class="container-fluid">

        
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
                            <li class="breadcrumb-item active">Edit Product Registration</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Product Registration</h4>
                </div>
            </div>
        </div>
        

        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Success - </strong> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Error - </strong> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <form action="{{ route('product.master.update', $productMaster->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Product Name</label>
                                                    <input type="text" name="product_name" class="form-control"
                                                        value="{{ old('product_name', $productMaster->product_name) }}">
                                                    @error('product_name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select name="type_id" class="form-control">
                                                        <option value="">-- Select Type --</option>
                                                        @foreach ($types as $id => $name)
                                                            <option value="{{ $id }}"
                                                                {{ old('type_id', $productMaster->type_id) == $id ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('type_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                          

                                            <div class="row">
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Unit</label>
                                                    <select name="unit_id" class="form-control">
                                                        <option value="">-- Select Unit --</option>
                                                        @foreach ($units as $id => $name)
                                                            <option value="{{ $id }}"
                                                                {{ old('unit_id', $productMaster->unit_id) == $id ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('unit_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Min Qty</label>
                                                    <input type="number" name="min_qty" class="form-control"
                                                        value="{{ old('min_qty', $productMaster->min_qty) }}">
                                                    @error('min_qty')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                        

                                            <div class="row">
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Lot No (Sale)</label>
                                                    <input type="text" name="lot_no_sale" class="form-control"
                                                        value="{{ old('lot_no_sale', $productMaster->lot_no_sale) }}">
                                                    @error('lot_no_sale')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Lot No (Purchase)</label>
                                                    <input type="text" name="lot_no_purchase" class="form-control"
                                                        value="{{ old('lot_no_purchase', $productMaster->lot_no_purchase) }}">
                                                    @error('lot_no_purchase')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                             

                                            <div class="row">
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Rate</label>
                                                    <input type="number" name="cost_price" class="form-control" step="any" min="0"
                                                        value="{{ old('cost_price', old('rate', $productMaster->rate ?? $productMaster->cost_price)) }}">
                                                    @error('cost_price')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                    @error('rate')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label d-block">Finished</label>
                                                    <div class="form-check form-check-inline mt-1">
                                                        <input class="form-check-input" type="checkbox" name="finished"
                                                            id="finished" value="1"
                                                            {{ old('finished', $productMaster->finished) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="finished">Yes</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="{{ route('product.master.list') }}" class="btn btn-secondary ms-1">Cancel</a>
                                        </form>
                                    </div> 
                                </div>
                            </div>
                        </div> 
                    </div> 
                </div> 
            </div>
        </div>
    </div>
@endsection