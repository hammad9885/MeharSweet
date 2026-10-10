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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                            <li class="breadcrumb-item active">Data Tables</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Registered Items</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
    <div class="card">
        <div class="card-body">
            <div class="tab-content">
                <div class="col-12">
                    <form action="{{ route('inventory.itemmaster.list') }}" method="GET"
                        class="row align-items-end" id="search-form">

                        <div class="form-group col-md-4">
                            <label for="item_code" class="sr-only">Item Title</label>
                            <select name="item_code" id="item_code" class="form-control select2"
                                data-toggle="select2">
                                <option value="">Select Item Title</option>
                                @foreach ($items as $code => $item_code)
                                    <option value="{{ $code }}"
                                        {{ request('item_code') == $code ? 'selected' : '' }}>
                                        {{ $item_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="type_id" class="sr-only">Item Type</label>
                            <select name="type_id" id="type_id" class="form-control select2"
                                data-toggle="select2">
                                <option value="">Select Type</option>
                                @foreach ($itemTypes as $id => $title)
                                    <option value="{{ $id }}" {{ request('type_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Show
                            </button>
                            <a href="{{ route('inventory.itemmaster.list') }}"
                                class="btn btn-secondary">
                                Clear
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="row">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                    <strong>Success - </strong> {{ session('success') }}
                </div>
            @endif
            <div class="col-12">

                <div class="card mt-2">

                    <div class="card-body">
                        <a href="{{ route('inventory.create.itemmaster') }}">
                            <button type="button" class="btn btn-primary" onclick="return checkPermission()">Add
                                Item</button>
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
                        <!-- Print Button -->

                        <div class="tab-content">
                            <div class="tab-pane show active" id="basic-datatable-preview">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Title</th>
                                            <th>Item Type</th>
                                            <th>Purchase Rate</th>
                                            <th>Sale Rate</th>
                                            <th>Unit</th>
                                            <th class="no-print">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemmasters as $itemmaster)
                                            <tr>
                                                <td>{{ $itemmaster->seq_number }}</td>
                                                <td>{{ $itemmaster->item_code }}</td>
                                                <td>{{ $itemmaster->itemtypes?->type_title ?? 'N/A' }}</td>
                                                <td>{{ $itemmaster->purchase }}</td>
                                                <td>{{ $itemmaster->sale_rate }}</td>
                                                <td>{{ $itemmaster->itemunits?->name ?? 'N/A' }}</td>
                                                <td class="no-print">
                                                    <div class="d-flex">
                                                        <a
                                                            href="{{ route('inventory.itemmaster.edit', $itemmaster->id) }}">
                                                            <button type="button" class="btn btn-primary"
                                                                onclick="return checkPermissionEdit()">Edit</button>
                                                        </a>

                                                        {{-- <button type="button" class="btn btn-danger">Print Table</button> --}}
                                                        <form
                                                            action="{{ route('inventory.itemmaster.destroy', $itemmaster->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this Item Type?');">
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
                            </div> <!-- end preview-->
                        </div> <!-- end tab-content-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>

    <!-- Print Function -->
    <script>
        function checkPermission() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ItemRegistration')
                        ->first();
                    $canAdd = $userRights && $userRights->add == 1;
                }
            @endphp

            if (!@json($canAdd)) {
                alert('You do not have Permission to Add');
                return false; // Prevent the default action (navigation)
            }
            return true; // Allow navigation
        }


        function checkPermissionEdit() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ItemRegistration')
                        ->first();
                    $canAdd = $userRights && $userRights->edit == 1;
                }
            @endphp

            if (!@json($canAdd)) {
                alert('You do not have Permission to Edit');
                return false; // Prevent the default action (navigation)
            }
            return true; // Allow navigation
        }

        function checkPermissionDel() {




            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ItemRegistration')
                        ->first();
                    $canAdd = $userRights && $userRights->del == 1;
                }
            @endphp
            if (!@json($canAdd)) {
                alert('You do not have Permission to Delete');
                return false; // Prevent the default action (navigation)
            }
            return true; // Allow navigation
        }



        function printTable() {
            // Hide elements with 'no-print' class
            const elementsToHide = document.querySelectorAll('.no-print');
            elementsToHide.forEach(el => el.style.display = 'none');

            const printContents = document.getElementById('basic-datatable').outerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 8px;
                            }
                            th {
                                background-color: #f2f2f2;
                                text-align: left;
                            }
                            .no-print{
                                background-color: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;

            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(); // Reload to restore the original page content
        }
    </script>
@endsection
