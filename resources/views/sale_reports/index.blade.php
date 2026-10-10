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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Confectionery</li>
                        </ol>
                    </div>
                    <h3 class="page-title no-print">Pharmaceutical</h3>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('error') }}
    </div>
@endif
        <!-- Show Form -->
        <div class="row">
            <div class="card mt-2">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="col-12">
                            <form action="{{ route('delivery_challan.reports') }}" method="GET" class="form-inline" id="search-form">
    <div class="row">
        <!-- Start Date -->
        <div class="form-group col-xl-2">
            <label for="start_date" class="sr-only">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date"
                   value="{{ request()->get('start_date') }}">
        </div>

        <!-- End Date -->
        <div class="form-group col-xl-2">
            <label for="end_date" class="sr-only">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date"
                   value="{{ request()->get('end_date') }}">
        </div>

         <div class="form-group col-xl-2">
                                    <label for="account_title" class="sr-only">Status</label>
                                    <select name="status" class="form-control select2">
                                        <option value="">All</option>

                                        <option value="official" {{ $status == 'official' ? 'selected' : '' }}>Official</option>
                                        <option value="unofficial" {{ $status == 'unofficial' ? 'selected' : '' }}>Unofficial</option>

                                    </select>

                                </div>
                                
                                 <div class="form-group col-xl-2">
                                <label for="v_no" class="sr-only">Voucher Number</label>
                                <select name="v_no" class="form-control select2" data-toggle="select2">
                                    <option value="">Select Voucher</option>
                                    @foreach($vNoList as $vNo)
                                        <option value="{{ $vNo }}" {{ request()->get('v_no') == $vNo ? 'selected' : '' }}>
                                            {{ $vNo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
<!-- P.O -->
        


 <div class="form-group col-xl-2">
    <label for="batch_no" class="sr-only">Batch No</label>
    <select name="batch_no" class="form-control select2" data-toggle="select2" id="batch_no">
        <option value="">Select Batch No</option>
        @foreach ($poNumbers as $poNumber)
            <option value="{{ $poNumber }}" 
                    {{ request()->get('batch_no') == $poNumber ? 'selected' : '' }}>
                {{ $poNumber }}
            </option>
        @endforeach
    </select>
</div>
        <!-- Item Title -->
        <!-- Item Title -->
<div class="form-group col-xl-2">
    <label for="itemTitle" class="sr-only">Item Type</label>
    <select name="item" class="form-control select2" data-toggle="select2" id="itemTitle">
        <option value="">Select Item</option>
        @foreach ($items as $item)
            <option value="{{ $item->id }}" 
                    {{ request()->get('item') == $item->id ? 'selected' : '' }}>
                {{ $item->type_title }}
            </option>
        @endforeach
    </select>
</div>
<br><br>
<div class="form-group col-xl-2 mt-1">
    <label for="accountTitle" class="sr-only">Account Title</label>
    <select name="account" class="form-control select2" data-toggle="select2" id="accountTitle">
        <option value="">Select Account</option>
        @foreach ($accounts as $account)
            <option value="{{ $account->id }}" 
                    {{ request()->get('account') == $account->id ? 'selected' : '' }}>
                {{ $account->title }}
            </option>
        @endforeach
    </select>
</div>

        <!-- Submit Button -->
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Show</button>
            <a class="btn btn-success" href="{{ route('delivery_challan.list') }}" role="button" onclick="return checkPermission()" >Add New</a>
        </div>
    </div>
</form>


                        </div>
                    </div>
                  </div>
        </div>

        <!-- Combined Data Table -->
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print Table</button>
                    <div class="card mt-2">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="col-12">
                                    <!--<h4>Transaction and Purchase Details</h4>-->
                                    <div style="width: 100%; overflow-x: auto;">
    <table id="combined-data-tables" class="table dt-responsive nowrap w-100 small-font-table">
        <h4 class="no-print">Delivery Challan Details</h4>
        <thead>
            <tr>
                <th>Date</th>
                <th>V.No</th>
                <th>Account Title</th>
                <th>Product Name</th>
                <th>Batch No</th>
                <th>Box</th>
                <th>Pack Qty</th>
                <th>Total</th>
                <th>Freight</th>
                <th class="no-print">Status</th>
                <th class="no-print">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trndtl as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                    <td>{{ $data->v_type }}-{{ $data->v_no }}</td>
                    <td>{{ $data->accounts->title ?? 'N/A' }}</td>
                    <td>{{ $data->deliveryDetails->products->prod_name ?? 'N/A' }}</td>
                    <td>{{ $data->deliveryDetails->batch_no ?? 'N/A' }}</td>
                    <td>{{ $data->deliveryDetails->box ?? 'N/A' }}</td>
                    <td>{{ $data->deliveryDetails->pack_qty ?? 'N/A' }}</td>
                    <td>{{ $data->deliveryDetails->total ?? 'N/A' }}</td>
                    <td>{{ $data->deliveryDetails->freight ?? 'N/A' }}</td>
                    <td class="no-print" style="display:none;">{{ $data->deliveryDetails->itemType->type_title ?? 'N/A' }}</td>
                    <td class="no-print">
                        <input type="checkbox" class="status-checkbox"
                            data-id="{{ $data->id }}"
                            {{ $data->status == 'official' ? 'checked' : '' }}>
                    </td>
                    <td class="no-print">
                        <form
                            action="{{ route('delivery_challan.delete', ['id' => $data->id]) }}"
                            method="POST" style="display:inline;" onclick="return checkPermissionDel()">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="confirmDelete(this)">Delete</button>
                        </form>

                        <a href="{{ route('delivery_challan.edit', ['v_no' => $data->v_no]) }}"
                            class="btn btn-warning btn-sm" onclick="return checkPermissionEdit()">Edit</a>
                            
                        <a href="{{ route('delivery_challan.editDel', ['v_no' => $data->v_no]) }}"
                            class="btn btn-primary btn-sm mt-1">Freight</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
                                    
                                  <table class="table table-striped dt-responsive nowrap w-100 print-table show-in-print" style="display: none;">
    <div class="show-in-prints" style="display: none;">
        <h2 >Delivery Challan Details</h2><br>
<div style="display: flex; justify-content: space-between;">
    
    <!-- Display unique dates on the right -->
    <h4 style="margin: 0;">Date: {{ $trndtl->unique('date')->pluck('date')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d-m-Y');
    })->implode(', ') }}</h4>
    <!-- Display unique account titles on the left -->
    <h4 style="margin: 0;">V.No: {{ $trndtl->unique('v_no')->pluck('v_no')->implode(', ') }}</h4>

    
</div>




<!-- Display unique account titles -->
<h4>Name: {{ $trndtl->unique('accounts.title')->pluck('accounts.title')->implode(', ') }}</h4><br>

    
    </div>
    <thead>
        <tr>
            <th>Sr.No</th>
            <th colspan="2"  style="width: 35%;">Product Name</th>
            <th colspan="2" style="width: 15%;">Batch No</th>
            <th colspan="2" style="width: 15%;">Box</th>
            <th colspan="2" style="width: 15%;">Pack Qty</th>
            <th colspan="2"style="width: 15%;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($trndtl as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td colspan="2">{{ $data->deliveryDetails->products->prod_name ?? 'N/A' }}</td>
                <td colspan="2">{{ $data->deliveryDetails->batch_no ?? 'N/A' }}</td>
                <td colspan="2">{{ $data->deliveryDetails->box ?? 'N/A' }}</td>
                <td colspan="2">{{ $data->deliveryDetails->pack_qty ?? 'N/A' }}</td>
                <td colspan="2">{{ $data->deliveryDetails->total ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
     <tfoot>
    <tr>
        <td colspan="9" style="text-align: right;"><strong>Total Boxes:</strong></td>
        <td colspan="2">
            {{ $trndtl->sum(function($item) { 
                return $item->deliveryDetails->box ?? 0; 
            }) }}
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: right;"><strong>Grand Total:</strong></td>
        <td colspan="2">
            {{ $trndtl->sum(function($item) { 
                return $item->deliveryDetails->total ?? 0; 
            }) }}
        </td>
    </tr>
</tfoot>
</table>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    
    
    
 function checkPermission() {
        @php
        $isAdmin = auth()->user()->is_admin;
        $canAdd = true;

        if ($isAdmin == 0) {
            $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                ->where('app_name', 'pharmaceuticaldelivery')
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
                ->where('app_name', 'pharmaceuticaldelivery')
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
                ->where('app_name', 'pharmaceuticaldelivery')
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
    // Select the element with the 'show-in-print' class
    const table = document.querySelector('.show-in-print');
    const tables = document.querySelector('.show-in-prints');

    // Temporarily show the table for printing
    table.style.display = 'block';
    tables.style.display = 'block';

    // Hide elements with 'no-print' class
    const elementsToHide = document.querySelectorAll('.no-print');
    elementsToHide.forEach(el => el.style.display = 'none');

    // Get the heading and table content you want to print
    const headingContent = document.querySelector('h4').outerHTML;
    const headingContents = document.querySelector('h3').outerHTML;
    const tableContent = table.outerHTML;
    const tableContents = tables.outerHTML;
    const originalContents = document.body.innerHTML;

    // Replace body content with the heading and table HTML for printing
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
                        padding: 10px;
                    }
                    th {
                        background-color: #f2f2f2;
                        text-align: left;
                    }
                    .no-print {
                        display: none;
                    }
                </style>
            </head>
            <body>
                ${headingContents}
                ${headingContent}
                ${tableContents}
                ${tableContent}
            </body>
        </html>
    `;

    // Trigger print dialog
    window.print();

    // Restore the original page content after printing
    document.body.innerHTML = originalContents;

    // Reattach event listeners or reload the page if needed
    window.location.reload();
}


        function confirmDelete(button) {
            if (confirm('Are you sure you want to delete this record from both tables? This action cannot be undone.')) {
                button.parentElement.submit();
            }
        }

          const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('end_date').value = today;
    </script>
@endsection
