@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Sale</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ReferenceNames.index') }}">Reference Names</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Reference Name</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ReferenceNames.update', $referenceName->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Reference Name English <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $referenceName->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name_urdu" class="form-label fw-bold">Reference Name Urdu</label>
                                <input type="text" name="name_urdu" id="name_urdu" class="form-control" dir="rtl" value="{{ old('name_urdu', $referenceName->name_urdu) }}">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('ReferenceNames.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
