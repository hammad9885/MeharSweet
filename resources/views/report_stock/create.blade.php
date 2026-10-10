@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">
            <i class="bi bi-box-seam me-2"></i> Create Stock Entry
        </h3>
        <a href="{{ route('report_stock.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Card Form -->
    <div class="card shadow-sm border-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>There were some problems with your input:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-body">
            <form action="{{ route('report_stock.store') }}" method="POST">
                @csrf
                
                <!-- Product Type -->
                <div class="mb-3">
                    <label for="product_type" class="form-label fw-semibold">
                        Product Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="product_type" name="product_type" required onchange="showFields()">
                        <option value="">-- Select Product Type --</option>
                        @php
                            $types = config('app.product_types', [
                                'Purchase Boxboard',
                                'Purchase Plate',
                                'Glue Purchase',
                                'Ink Purchase',
                                'Lamination Purchase',
                                'Corrugation Purchase',
                                'Shipper Purchase',
                                'Dye Purchase'
                            ]);
                        @endphp
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('product_type', request('product_type')) == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 1 -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="item_code" value="{{ old('item_code') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Length</label>
                        <input type="text" class="form-control" name="length" value="{{ old('length') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Width</label>
                        <input type="text" class="form-control" name="width" value="{{ old('width') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Grammage</label>
                        <input type="text" class="form-control" name="grammage" value="{{ old('grammage') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Weights</label>
                        <input type="text" class="form-control" name="weights" value="{{ old('weights') }}">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Country 
                            <span class="text-danger" id="country_required" style="display:none;">*</span>
                        </label>
                        <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Size 
                            <span class="text-danger" id="size_required" style="display:none;">*</span>
                        </label>
                        <input type="text" class="form-control" name="size" value="{{ old('size') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="qty" value="{{ old('qty') }}" required>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lamination Type 
                            <span class="text-danger" id="lamination_type_required" style="display:none;">*</span>
                        </label>
                        <input type="text" class="form-control" name="item_name_lamination" value="{{ old('item_name_lamination') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Corrugation Type 
                            <span class="text-danger" id="corrugation_type_required" style="display:none;">*</span>
                        </label>
                        <input type="text" class="form-control" name="item_name_corrugation" value="{{ old('item_name_corrugation') }}">
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Create
                    </button>
                    <a href="{{ route('report_stock.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script for Dynamic Required Fields -->
<script>
function showFields() {
    let type = document.getElementById('product_type').value;
    const fields = ['country_required', 'size_required', 'lamination_type_required', 'corrugation_type_required'];
    
    // Hide all required marks first
    fields.forEach(f => {
        if (document.getElementById(f)) {
            document.getElementById(f).style.display = 'none';
        }
    });

    // Show based on product type
    if (type === 'Purchase Plate') {
        document.getElementById('country_required').style.display = '';
    }
    if (type === 'Lamination Purchase') {
        document.getElementById('lamination_type_required').style.display = '';
        document.getElementById('size_required').style.display = '';
    }
    if (type === 'Corrugation Purchase') {
        document.getElementById('corrugation_type_required').style.display = '';
        document.getElementById('size_required').style.display = '';
    }
}
window.onload = showFields;
</script>
@endsection
