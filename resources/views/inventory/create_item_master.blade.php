@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-0">Add Item</h4>
                    <small class="text-muted">Register a new item in Item Master</small>
                </div>
                <a href="{{ route('inventory.itemmaster.list') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back to Items
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('inventory.itemmaster') }}" method="POST">
                @csrf

                <!-- Hidden item_type to maintain ERP logic compatibility -->
                <input type="hidden" name="item_type" id="item_type_hidden" value="{{ old('item_type') }}">

                <div class="row">

                    <!-- 1. Auto-generate ID -->
                    <div class="col-md-6 mb-3">
                        <label for="seq_number" class="form-label fw-semibold">Auto-generate ID</label>
                        <input type="text" id="seq_number" name="seq_number"
                            class="form-control bg-light"
                            value="{{ old('seq_number', $nextSeq) }}" readonly>
                        <small class="text-muted">System assigned code</small>
                    </div>

                    <!-- 2. Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', old('item_code')) }}"
                            placeholder="Enter item name" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 3. Urdu Name -->
                    <div class="col-md-6 mb-3">
                        <label for="urdu_title" class="form-label fw-semibold">Urdu Name</label>
                        <input type="text" id="urdu_title" name="urdu_title"
                            class="form-control @error('urdu_title') is-invalid @enderror"
                            value="{{ old('urdu_title') }}"
                            placeholder="اردو نام درج کریں" dir="rtl">
                        @error('urdu_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 4. Type -->
                    <div class="col-md-6 mb-3">
                        <label for="type_id" class="form-label fw-semibold">
                            Type <span class="text-danger">*</span>
                        </label>
                        <select name="type_id" id="type_id"
                            class="form-select @error('type_id') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            @foreach ($itemtypes as $itemtype)
                                <option value="{{ $itemtype->id }}" data-type="{{ $itemtype->item_type }}"
                                    {{ old('type_id') == $itemtype->id ? 'selected' : '' }}>
                                    {{ $itemtype->type_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 5. Unit -->
                    <div class="col-md-6 mb-3">
                        <label for="unit_id" class="form-label fw-semibold">
                            Unit <span class="text-danger">*</span>
                        </label>
                        <select name="unit_id" id="unit_id"
                            class="form-select @error('unit_id') is-invalid @enderror" required>
                            <option value="">Select Unit</option>
                            @foreach ($itemUnits as $itemUnit)
                                <option value="{{ $itemUnit->id }}"
                                    {{ old('unit_id') == $itemUnit->id ? 'selected' : '' }}>
                                    {{ $itemUnit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 6. Rate -->
                    <div class="col-md-6 mb-3">
                        <label for="rate" class="form-label fw-semibold">Rate</label>
                        <input type="number" id="rate" name="rate"
                            class="form-control @error('rate') is-invalid @enderror"
                            value="{{ old('rate', old('sale_rate', old('purchase'))) }}"
                            placeholder="Enter rate" step="any" min="0">
                        @error('rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('inventory.itemmaster.list') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-check"></i> Save Item
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function syncItemType() {
            var selectedType = $('#type_id').find(':selected').data('type') || '';
            $('#item_type_hidden').val(selectedType);
        }

        $('#type_id').on('change', syncItemType);
        syncItemType();
    });
</script>
@endsection