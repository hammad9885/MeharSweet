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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Products</a></li>
                            <li class="breadcrumb-item active">Product Registration</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Product Registration</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Success - </strong> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Error - </strong> {{ session('error') }}
                </div>
            @endif

            <div class="col-12">
                <form method="GET" action="{{ route('product.master.list') }}" class="row mb-3">
                    <div class="col-md-3">
                        <select name="product_name" class="form-control select2-search">
                            <option value="">-- Select Product --</option>
                            @foreach ($productMasters as $p)
                                <option value="{{ $p->product_name }}" {{ request('product_name') == $p->product_name ? 'selected' : '' }}>{{ $p->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success">Search</button>
                        <a href="{{ route('product.master.list') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
                <a href="{{ route('product.master.create') }}">
                    <button type="button" class="btn btn-primary" onclick="return checkPermission()">Add Item</button>
                </a>
                <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>

                <div class="card mt-2">
                    <div class="card-body">
                        <h4 class="header-title">Product Registration List</h4>

                        <div class="tab-content">
                            <div class="tab-pane show active" id="basic-datatable-preview">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Type</th>
                                            <th>Seq No</th>
                                            <th>Unit</th>
                                            <th>Min Qty</th>
                                            <th>Rate</th>
                                            <th>Stroke</th>
                                            <th>Dye No</th>
                                            <th>Group</th>
                                            <th>Finished</th>
                                            <th class="no-print">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($productMasters as $product)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $product->product_name }}</td>
                                                <td>{{ $product->getRelation('type')?->type_title ?? $product->type ?? '' }}</td>
                                                <td>{{ $product->seq_number }}</td>
                                                <td>{{ $product->getRelation('unit')?->name ?? $product->unit ?? '' }}</td>
                                                <td>{{ $product->min_qty }}</td>
                                                <td>{{ $product->cost_price }}</td>
                                                <td>{{ $product->stroke }}</td>
                                                <td>{{ $product->dye_no }}</td>
                                                <td>{{ $product->group->name ?? '-' }}</td>
                                                <td>
                                                    @if ($product->finished)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td class="no-print">
                                                    <div class="d-flex">
                                                        <a href="{{ route('product.master.edit', $product->id) }}">
                                                            <button type="button" class="btn btn-primary"
                                                                onclick="return checkPermissionEdit()">Edit</button>
                                                        </a>

                                                        <form action="{{ route('product.master.destroy', $product->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this Product?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger"
                                                                style="margin-left: 2px;"
                                                                onclick="return checkPermissionDel()">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end preview -->
                        </div> <!-- end tab-content -->

                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div> <!-- end row -->

    </div>

    <script>
        function checkPermission() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;
                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ProductMaster')
                        ->first();
                    $canAdd = $userRights && $userRights->add == 1;
                }
            @endphp
            if (!@json($canAdd)) {
                alert('You do not have Permission to Add');
                return false;
            }
            return true;
        }

        function checkPermissionEdit() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canEdit = true;
                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ProductMaster')
                        ->first();
                    $canEdit = $userRights && $userRights->edit == 1;
                }
            @endphp
            if (!@json($canEdit)) {
                alert('You do not have Permission to Edit');
                return false;
            }
            return true;
        }

        function checkPermissionDel() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canDel = true;
                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ProductMaster')
                        ->first();
                    $canDel = $userRights && $userRights->del == 1;
                }
            @endphp
            if (!@json($canDel)) {
                alert('You do not have Permission to Delete');
                return false;
            }
            return true;
        }

        function printTable() {
            const elementsToHide = document.querySelectorAll('.no-print');
            elementsToHide.forEach(el => el.style.display = 'none');

            const printContents = document.getElementById('basic-datatable').outerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = `
                        <html>
                            <head>
                                <title>Product Registration</title>
                                <style>
                                    table { width: 100%; border-collapse: collapse; }
                                    th, td { border: 1px solid #ddd; padding: 8px; }
                                    th { background-color: #f2f2f2; text-align: left; }
                                </style>
                            </head>
                            <body>${printContents}</body>
                        </html>
                    `;

            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    .select2-container--default .select2-selection--single { height: calc(1.5em + 0.9rem + 2px); border: 1px solid #dee2e6; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + 0.9rem); padding-left: 0.9rem; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.select2-search').select2({ placeholder: '-- Select Product --', width: '100%' });
    });
    </script>
@endsection