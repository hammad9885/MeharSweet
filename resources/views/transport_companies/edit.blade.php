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
                        <li class="breadcrumb-item"><a href="{{ route('transport_companies.index') }}">Transport Companies</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Transport Company</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('transport_companies.update', $transportCompany->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Transport Company</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $transportCompany->name }}" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('transport_companies.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
