@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-0">Edit Supplier</h4>
                    <small class="text-muted">Update supplier information</small>
                </div>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back to Suppliers
                </a>
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

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <!-- 1. Auto-generate ID -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Auto-generate ID</label>
                        <input type="text" name="seq_number"
                            value="{{ $supplier->seq_number ?? $supplier->id }}"
                            class="form-control bg-light" readonly>
                        <small class="text-muted">System assigned ID</small>
                    </div>

                    <!-- 2. Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                            value="{{ old('name', $supplier->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter supplier name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 3. Urdu Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Urdu Name</label>
                        <input type="text" name="urdu_title"
                            value="{{ old('urdu_title', $supplier->urdu_title ?: (preg_match('/[\x{0600}-\x{06FF}]/u', $supplier->mobile ?? '') ? $supplier->mobile : '')) }}"
                            class="form-control @error('urdu_title') is-invalid @enderror"
                            placeholder="اردو نام درج کریں" dir="rtl">
                        @error('urdu_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 4. Number -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Number</label>
                        <input type="text" name="mobile"
                            value="{{ old('mobile', preg_match('/[\x{0600}-\x{06FF}]/u', $supplier->mobile ?? '') ? '' : $supplier->mobile) }}"
                            class="form-control @error('mobile') is-invalid @enderror"
                            placeholder="Enter phone/mobile number">
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 5. City -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">City</label>
                        <input type="text" name="city"
                            value="{{ old('city', $supplier->city) }}"
                            class="form-control @error('city') is-invalid @enderror"
                            placeholder="Enter city">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save"></i> Update Supplier
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection