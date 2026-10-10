@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit General Delivery Challan</h4>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif
    
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="voucherForm" action="{{ route('general_delivery_challan.update', $deliveryChallan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-6">
                            <input type="hidden" id="invoice_type" name="v_type" value="GDC" readonly>

                            <!-- Date Field -->
                            <div class="mb-3">
    <label for="entryDate" class="form-label">Date</label>
    <input type="date" id="entryDate" class="form-control" name="date" value="{{ \Carbon\Carbon::parse($deliveryChallan->updated_at)->format('Y-m-d') }}" readonly>
</div>

                            <div class="mb-3">
                                <label for="preparedBy" class="form-label">Prepared By</label>
                                <input type="text" id="preparedBy" class="form-control" name="prepared_by"
                                    value="{{ $deliveryChallan->prepared_by }}" readonly>
                            </div>

                            <!-- Delivery Challan -->
                            <div class="mb-3">
                                <label for="gjs_no" class="form-label">GJS No</label>
                                <input type="text" id="gjs_no" class="form-control" name="gjs_no"
                                    value="{{ $deliveryChallan->gjs_no }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="party_name" class="form-label">Party Name</label>
                                <input type="text" id="party_name" class="form-control" name="party_name" 
                                    value="{{ $deliveryChallan->party_name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="product_type" class="form-label">Product Type</label>
                                <input type="text" id="product_type" class="form-control" name="product_type" 
                                    value="{{ $deliveryChallan->product_type }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="item_name" class="form-label">Item Name</label>
                                <input type="text" id="item_name" class="form-control" name="item_name" 
                                    value="{{ $deliveryChallan->item_name }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="qty" class="form-label">Qty</label>
                                <input type="text" id="qty" class="form-control" name="qty" 
                                    value="{{ $deliveryChallan->qty }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="text" id="rate" class="form-control" name="rate" 
                                    value="{{ $deliveryChallan->rate }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="freight" class="form-label">Freight</label>
                                <input type="number" id="freight" class="form-control" name="freight" 
                                    value="{{ $deliveryChallan->freight }}" required>
                            </div>

                            <button type="submit" class="btn btn-success">Update Voucher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection