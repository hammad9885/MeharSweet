@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">
            <i class="bi bi-pencil-square me-2"></i> Edit Stock Entry
        </h3>
        <a href="{{ route('report_stock.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Card Form -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('report_stock.update', $stock->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                            $selectedType = old('product_type', $stock->product_type ?? '');
                        @endphp
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ $selectedType == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- First Row -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Item Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="item_code" value="{{ old('item_code', $stock->item_code) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Length</label>
                        <input type="text" class="form-control" name="length" value="{{ old('length', $stock->length) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Width</label>
                        <input type="text" class="form-control" name="width" value="{{ old('width', $stock->width) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Grammage</label>
                        <input type="text" class="form-control" name="grammage" value="{{ old('grammage', $stock->grammage) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Weights</label>
                        <input type="text" class="form-control" name="weights" value="{{ old('weights', $stock->weights) }}">
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Country <span class="text-danger" id="country_required" style="display:none;">*</span></label>
                        <input type="text" class="form-control" name="country" value="{{ old('country', $stock->country) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Size <span class="text-danger" id="size_required" style="display:none;">*</span></label>
                        <input type="text" class="form-control" name="size" value="{{ old('size', $stock->size) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="qty" value="{{ old('qty', $stock->qty) }}" required>
                    </div>
                </div>

                <!-- Third Row -->
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lamination Type <span class="text-danger" id="lamination_type_required" style="display:none;">*</span></label>
                        <input type="text" class="form-control" name="item_name_lamination" value="{{ old('item_name_lamination', $stock->item_name_lamination) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Corrugation Type <span class="text-danger" id="corrugation_type_required" style="display:none;">*</span></label>
                        <input type="text" class="form-control" name="item_name_corrugation" value="{{ old('item_name_corrugation', $stock->item_name_corrugation) }}">
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Update
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
    const fields = [
        'product_name_required',
        'country_required',
        'size_required',
        'lamination_type_required',
        'corrugation_type_required',
        'shipper_item_required',
        'dye_item_required'
    ];
    fields.forEach(f => {
        if (document.getElementById(f)) {
            document.getElementById(f).style.display = 'none';
        }
    });

    if(type === 'Purchase Plate') {
        document.getElementById('product_name_required')?.style.display = '';
        document.getElementById('country_required')?.style.display = '';
    }
    if(type === 'Lamination Purchase') {
        document.getElementById('lamination_type_required')?.style.display = '';
        document.getElementById('size_required')?.style.display = '';
    }
    if(type === 'Corrugation Purchase') {
        document.getElementById('corrugation_type_required')?.style.display = '';
        document.getElementById('size_required')?.style.display = '';
    }
    if(type === 'Shipper Purchase') {
        document.getElementById('shipper_item_required')?.style.display = '';
    }
    if(type === 'Dye Purchase') {
        document.getElementById('dye_item_required')?.style.display = '';
    }
}
window.onload = showFields;
</script>
@endsection
