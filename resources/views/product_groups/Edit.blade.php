@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('product-groups.index') }}">Product Groups</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Product Group</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Success/Error Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <form action="{{ route('product-groups.update', $productGroup->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="mb-3">
                                                <label for="group_name" class="form-label">Product Group Name</label>
                                                <input type="text" 
                                                       id="group_name" 
                                                       class="form-control @error('group_name') is-invalid @enderror" 
                                                       name="group_name"
                                                       value="{{ old('group_name', $productGroup->group_name) }}" 
                                                       placeholder="Enter Product Group Name" 
                                                       required>
                                                @error('group_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="mdi mdi-content-save me-1"></i> Update
                                                </button>
                                                <a href="{{ route('product-groups.index') }}" class="btn btn-secondary">
                                                    <i class="mdi mdi-arrow-left me-1"></i> Back to List
                                                </a>
                                            </div>
                                        </form>

                                    </div> <!-- end col -->

                                </div>
                                <!-- end row-->
                            </div> <!-- end preview-->

                        </div> <!-- end tab-content-->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

    </div>
@endsection