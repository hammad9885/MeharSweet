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
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Sequence Number</label>
                        <input type="text" name="seq_number"
                            value="{{ $supplier->id }}"
                            class="form-control" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Order Number</label>
                        <input type="text" name="order_number"
                            value="{{ old('order_number', $supplier->order_number) }}"
                            class="form-control @error('order_number') is-invalid @enderror"
                            placeholder="Enter order number">
                        @error('order_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                            value="{{ old('name', $supplier->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter supplier name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Urdu Title <span class="text-danger">*</span></label>
                        <input type="text" name="mobile"
                            value="{{ old('mobile', $supplier->mobile) }}"
                            class="form-control @error('mobile') is-invalid @enderror"
                            placeholder="Enter Urdu title" required>
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email"
                            value="{{ old('email', $supplier->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">City</label>
                        <input type="text" name="city"
                            value="{{ old('city', $supplier->city) }}"
                            class="form-control"
                            placeholder="Enter city">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" rows="2"
                            class="form-control"
                            placeholder="Enter full address">{{ old('address', $supplier->address) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">NTN</label>
                        <input type="text" name="ntn"
                            value="{{ old('ntn', $supplier->ntn) }}"
                            class="form-control @error('ntn') is-invalid @enderror"
                            placeholder="Enter NTN">
                        @error('ntn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">CNIC</label>
                        <input type="text" name="cnic"
                            value="{{ old('cnic', $supplier->cnic) }}"
                            class="form-control @error('cnic') is-invalid @enderror"
                            placeholder="Enter CNIC">
                        @error('cnic')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-light me-2">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save"></i> Update Supplier
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection