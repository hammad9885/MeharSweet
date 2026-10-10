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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Product Groups</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Product Groups List</h4>
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

                        <!-- Add New Button -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <a href="{{ route('product-groups.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus-circle me-1"></i> Add New Product Group
                                </a>
                            </div>
                        </div>

                        <!-- Search Filter -->
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <form action="{{ route('product-groups.index') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="group_name"
                                            placeholder="Search by Group Name..." value="{{ request('group_name') }}">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="mdi mdi-magnify"></i> Search
                                        </button>
                                        @if(request('group_name'))
                                            <a href="{{ route('product-groups.index') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh"></i> Clear
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Product Groups Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Group Name</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productGroups as $index => $group)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $group->group_name }}</td>
                                            <td>{{ $group->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <a href="{{ route('product-groups.edit', $group->id) }}"
                                                    class="btn btn-sm btn-info" title="Edit">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </a>

                                                <form action="{{ route('product-groups.destroy', $group->id) }}" method="POST"
                                                    style="display:inline-block;"
                                                    onsubmit="return confirm('Are you sure you want to delete this product group?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No Product Groups found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

    </div>
@endsection