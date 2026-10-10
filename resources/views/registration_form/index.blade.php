@extends('layouts.app') @section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">Hyper</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">Tables</a>
                            </li>
                            <li class="breadcrumb-item active">Data Tables</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Product Registration</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->





        <div class="row">
            <div class="card mt-2">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="col-12">
                            <form action="{{ route('registration_form.reports') }}" method="GET" class="form-inline" id="search-form">
    <div class="row">
        <!-- Start Date -->
        <div class="form-group col-xl-2">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request()->get('start_date') }}">
        </div>

        <!-- End Date -->
        <div class="form-group col-xl-2">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request()->get('end_date') }}">
        </div>
<!-- Party (Account) Dropdown -->
<div class="form-group col-xl-2">
    <label for="account" class="form-label">Party</label>
    <select name="account" class="form-control select2"  data-toggle="select2" id="account">
        <option value="">Select a Party</option>
        @foreach($accounts as $product)
            <option value="{{ $product->aid }}" {{ request()->get('account') == $product->aid ? 'selected' : '' }}>
                {{ $product->account->title ?? 'Select a Party' }} <!-- Display account title -->
            </option>
        @endforeach
    </select>
</div>

<!-- Country Dropdown -->
<div class="form-group col-xl-2">
    <label for="country" class="form-label">Country</label>
    <select name="country" class="form-control select2" id="country" data-toggle="select2">
        <option value="">Select a Country</option>
        @foreach($countries as $product)
            <option value="{{ $product->country_id }}" {{ request()->get('country') == $product->country_id ? 'selected' : '' }}>
                {{ $product->country->country_name ?? 'No Country' }} <!-- Display country name -->
            </option>
        @endforeach
    </select>
</div>

<!-- Product Name Dropdown -->
<div class="form-group col-xl-2">
    <label for="productName" class="form-label">Product Name</label>
    <select id="productName" class="form-control select2" name="productName" data-toggle="select2">
        <option value="">Select a Product</option>
        @foreach($productNames as $product)
            <option value="{{ $product->prod_name }}" {{ request('productName') == $product->prod_name ? 'selected' : '' }}>
                {{ $product->prod_name }} <!-- Display product name -->
            </option>
        @endforeach
    </select>
</div>





        <!-- Submit Button -->
        <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-primary">Show</button>
                                        <a href="{{ route('registration_form.list') }}">
                                            <button type="button" class="btn btn-success"  onclick="return checkPermission()" >Add Item</button>
                                        </a>
                                    </div>
    </div>
</form>


                            <div class="row">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show"
                                        role="alert">
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                        <strong>Success - </strong> {{ session('success') }}
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="card mt-2">
                                        <div class="card-body">
                                            <button type="button" class="btn btn-secondary" onclick="printTable()">
                                                Print Table
                                            </button>
                                          
                                            <div class="tab-content">
                                                <div class="tab-pane show active" id="basic-datatable-preview">
                                                    <div style="overflow-x: auto;">
    <table id="basic-datatable"
           class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>SR</th>
                <th class="no-print">Actions</th>
                <th>Date</th>
                <th>Product Name</th>
                <th>Product Type</th>
                <th>Party</th>
                <th>Country</th>
                <th>Item</th>
                <th>Grammage</th>
                <th>Length</th>
                <th>Width</th>
                <th>Product Rate</th>
                <th>Img</th>
                <th>Description</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="no-print">
                        <div class="d-flex">
                            <a href="{{ route('registration_form.edit', $product->id) }}">
                                <button type="button"
                                        class="btn btn-primary" onclick="return checkPermissionEdit()"> 
                                    Edit
                                </button>
                            </a>
                            <form action="{{ route('registration_form.destroy', $product->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger" onclick="return checkPermissionDel()">Delete</button>
                            </form>
                        </div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($product->updated_at)->format('m/d/Y h:i A') }}</td>
                    <td>{{ $product->prod_name }}</td>
                    <td>{{ $product->product_type ?? 'N/A' }}</td>
                    <td>{{ $product->account->title ?? 'No Account' }}</td>
                    <td>{{ $product->country->country_name ?? 'No Country' }}</td>
                    <td>{{ $product->items->item_code ?? 'No Items' }}</td>
                    <td>{{ $product->grammage }}</td>
                    <td>{{ $product->length }}</td>
                    <td>{{ $product->width }}</td>
                    <td>{{ $product->rate }}</td>
                    <td>
                        @if (!empty($product->file_path))
                            <a href="{{ asset('/storage/' . $product->file_path) }}" target="_blank">
                                <p>Img</p>
                            </a>
                        @else
                            <p>No Img</p>
                        @endif
                    </td>
                    <td>{{ $product->descr }}</td>
                    
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                                                </div>
                                                <!-- end preview-->
                                            </div>
                                            <!-- end tab-content-->
                                        </div>
                                        <!-- end card body-->
                                    </div>
                                    <!-- end card -->
                                </div>
                                <!-- end col-->
                            </div>
                            <!-- end row-->
                        </div>

                        <!-- Print Function -->
                        <script>
                       function checkPermission() {
        @php
        $isAdmin = auth()->user()->is_admin;
        $canAdd = true;

        if ($isAdmin == 0) {
            $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                ->where('app_name', 'productRegistrations')
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
                ->where('app_name', 'productRegistrations')
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
                ->where('app_name', 'productRegistrations')
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
                                const elementsToHide = document.querySelectorAll(".no-print");
                                elementsToHide.forEach((el) => (el.style.display = "none"));

                                const printContents = document.getElementById("basic-datatable").outerHTML;
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
