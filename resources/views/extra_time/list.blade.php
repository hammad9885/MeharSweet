@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Form Elements</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Add Bonus Type</h4>
                </div>
            </div>
        </div>
        @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif
@if ($errors->any())
            <div class="alert alert-danger">
                <ul>
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
                                    <div class="col-lg-6">
                                        <form action="{{ route('extra_time.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="type_title" class="form-label">Bonus Type</label>
        <input type="text" id="type_title" class="form-control" name="type_title"
               value="{{ old('type_title') }}" placeholder="Bonus Type" required>
    </div>
    
     <div class="mb-3">
        <label for="rate" class="form-label">Rate</label>
        <input type="number" id="rate" class="form-control" name="rate"
               value="{{ old('rate') }}" placeholder="Rate" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
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
