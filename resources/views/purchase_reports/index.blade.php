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
                        <li class="breadcrumb-item active">Purchase Boxboard</li>
                    </ol>
                </div>
                <h3 class="page-title">Purchase Boxboard</h3>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
            aria-label="Close"></button>
        {{ session('success') }}
    </div>
@endif
    <!-- Show Form -->
    <div class="row">
        <div class="card mt-2">
            <div class="card-body">
                <div class="tab-content">
                    <div class="col-6">
                        <form action="{{ route('payment_invoice.reports') }}" method="GET" class="form-inline" id="search-form">
                            <div class="row">
                                <div class="form-group col-xl-4">
                                    <label for="start_date" class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request()->get('start_date') }}">
                                </div>
                                <div class="form-group col-xl-4">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request()->get('end_date') }}">
                                </div>
                                <div class="form-group col-xl-4">
                                    <label for="account_title" class="sr-only">Status</label>
                                    <select name="status" class="form-control select2"
                                        >
                                        <option value="">All</option>

                                        <option value="official" {{ $status == 'official' ? 'selected' : '' }}>Official</option>
                                        <option value="unofficial" {{ $status == 'unofficial' ? 'selected' : '' }}>Unofficial</option>

                                    </select>

                                </div>
                                
                                 <div class="form-group col-xl-4 mt-2">
                                <label for="v_no" class="sr-only">Voucher Number</label>
                                <select name="v_no" class="form-control select2" data-toggle="select2">
                                    <option value="">Select Voucher</option>
                                    @foreach($vNo as $vNo)
                                        <option value="{{ $vNo }}" {{ request()->get('v_no') == $vNo ? 'selected' : '' }}>
                                            {{ $vNo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group col-xl-4 mt-2">
    <label for="account_id" class="sr-only">Account Title</label>
    <select name="account_id" class="form-control select2" data-toggle="select2">
        <option value="">Select Account</option>
        @foreach($accountId as $id => $title)
            <option value="{{ $id }}" {{ request()->get('account_id') == $id ? 'selected' : '' }}>
                {{ $title }}
            </option>
        @endforeach
    </select>
</div>


                            
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    <a class="btn btn-success" href="{{ route('payment_invoice.list') }}" role="button" onclick="return checkPermission()">Add New</a>
                                </div>
                                
                            </div>
                        </form>
                    </div>
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
                             
                               <table id="combined-data-table" class="table table-striped dt-responsive nowrap w-100" style="font-size: 14px;">
    <h4>Purchase Boxboard Details</h4>
    <thead>
        <tr>
            <th class="no-print">Date</th>
            <th class="no-print">V. No</th>
            <th>Account Title</th>
            <th>Item</th>
            <th>Width</th>
            <th>Length</th>
            <th>Grammage</th>
            <th>Qty</th>
            <th>Rates</th>
            <th>Weights</th>
            <th>Amount</th>
            <th class="no-print">Freight</th>
            <th class="no-print">Status</th>
            <th class="no-print">Actions</th>
        </tr>
    </thead>
    <tbody>
            @php
                $totalQty = 0;
                $totalAmount = 0;
            @endphp
            @foreach($trndtl as $data)
                @php
                    $qty = $data->purchasedetails->qty ?? 0;
                    $amount = $data->purchasedetails->amount ?? 0;
                    $totalQty += is_numeric($qty) ? $qty : 0;
                    $totalAmount += is_numeric($amount) ? $amount : 0;
                @endphp
            <tr>
                <td class="no-print">{{  \Carbon\Carbon::parse($data->date)->format('d-m-Y')  }}</td>
                <td class="no-print">{{ $data->v_type }}-{{ $data->v_no }}</td>
                <td>{{ $data->accounts->title ?? 'N/A'}}</td>
                <td>{{ $data->purchasedetails->items->item_code ?? 'N/A'}}</td>
                <td>{{ $data->purchasedetails->width ?? 'N/A' }}</td>
                <td>{{ $data->purchasedetails->lenght ?? 'N/A' }}</td>
                <td>{{ $data->purchasedetails->grammage ?? 'N/A' }}</td>
                <td>{{ $data->purchasedetails->qty ?? 'N/A' }}</td>
                <td>{{ $data->purchasedetails->rate ?? 'N/A' }}</td>
                <td>{{ $data->purchasedetails->total_wt ?? 'N/A'  }}</td>
                <td>{{ $data->purchasedetails->amount ?? 'N/A' }}</td>
                <td class="no-print">{{ $data->purchasedetails->freight?? 'N/A' }}</td>
                <td class="no-print">
                    <input type="checkbox"
                           class="status-checkbox"
                           data-id="{{ $data->id }}"
                           {{ $data->status == 'official' ? 'checked' : '' }}>
                </td>
                <td class="no-print">
        <form action="{{ route('purchase_details.delete', ['id' => $data->id]) }}" method="POST" style="display:inline;" class="delete-entry-form" data-entry-id="{{ $data->id }}" onsubmit="return false;">
            @csrf
            @method('DELETE')
            <span class="badge bg-danger" style="cursor:pointer;" onclick="confirmDeleteAjax(this, {{ $data->id }})">Delete</span>
        </form>
        <a href="{{ route('purchase_details.edit', ['v_no' => $data->v_no]) }}" class="badge bg-warning text-dark mt-1" style="cursor:pointer;" onclick="return checkPermissionEdit()">Edit</a>
        <a href="{{ route('purchase_details.editBoxboard', ['v_no' => $data->v_no]) }}" class="badge bg-primary mt-1" style="cursor:pointer;">Freight</a>
    </td>
            </tr>
            @endforeach
            <tr>
    <td colspan="8" style="text-align: right; font-weight: bold;">Total Quantity:</td>
    <td style="font-weight: bold;">{{ number_format($totalQty, 2) }}</td>
</tr>
<tr>
    <td colspan="8" style="text-align: right; font-weight: bold;">Total Amount:</td>
    <td style="font-weight: bold;">{{ number_format($totalAmount, 2) }}</td>
</tr>

    </tbody>
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
                ->where('app_name', 'Boxboard')
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
                ->where('app_name', 'Boxboard')
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
                ->where('app_name', 'Boxboard')
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

    // Ensure the <h5> element contains the correct date and v_no
    const h5Element = document.querySelector('h5');
    if (h5Element) {
        h5Element.innerHTML = `
            <span style="flex: 1; font-size: 14px;">
                Date: {{ $trndtl->isNotEmpty() ? \Carbon\Carbon::parse($trndtl->first()->created_at)->format('d-m-Y') : 'N/A' }}
            </span>
            <span style="margin-left: auto; font-size: 14px;">
              Voucher No: {{ $trndtl->isNotEmpty() ? $trndtl->first()->v_type : 'N/A' }}-{{ $trndtl->isNotEmpty() ? $trndtl->first()->v_no : 'N/A' }}
            </span>
        `;

        // Add Flexbox styling to the h5 element
        h5Element.style.display = 'flex';
        h5Element.style.justifyContent = 'space-between';
        h5Element.style.alignItems = 'center';
    }

    // Get the heading, sub-heading, and table content you want to print
    const headingContent = document.querySelector('h4').outerHTML;
    const subHeadingContent = document.querySelector('h5').outerHTML; // Get the <h5> content
    const tableContent = document.getElementById('combined-data-table').outerHTML;

    // Create a div to display the freight value on the right side (only if freight > 0)
 
    
const freightContent = `
@php
    // Safely get freight details
    $freightDetails = $trndtl->pluck('purchasedetails')
        ->filter()
        ->filter(function ($detail) {
            return ($detail->freight ?? 0) > 0;
        });

    // Calculate total quantities (fix the where clause)
    $totalQtys = $freightDetails->sum('qty');
    // OR if you need filtered by voucher:
    // $totalQtys = $freightDetails->where('voucher_no', $specificVoucherNo)->sum('qty');
@endphp

@if($freightDetails->isNotEmpty())
<div style="display: flex; justify-content: flex-end; margin-top: 10px; font-size: 14px;">
    <strong>Freight:</strong>
    @foreach($freightDetails as $detail)
        @if($detail->freight_type == 'Bilty')
            Bilty {{ $detail->freight }}<br>
        @elseif($detail->freight_type == 'Per Piece')
            Per Piece {{ $totalQtys }} * {{ $totalQtys > 0 ? ($detail->freight / $totalQtys) : 0 }} = {{ $detail->freight }}<br>
        @endif
    @endforeach
</div>
@endif
`;

    const originalContents = document.body.innerHTML;

    // Replace body content with the heading, sub-heading, table, and freight HTML for printing
    document.body.innerHTML = `
        <html>
            <head>
                <title>Print Table</title>
                
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px; /* Adjusted font size */
                        margin: 0; /* Remove default body margin */
                        padding: 0; /* Remove default body padding */
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 0; /* Remove table margin */
                        padding: 0; /* Remove table padding */
                    }

                    th, td {
                        border: 1px solid #ddd;
                        padding: 2px; /* Reduced cell padding */
                    }

                    th {
                        background-color: #f2f2f2;
                        text-align: left;
                    }

                    .no-print {
                        display: none;
                    }

                    /* Print-specific styles */
                    @media print {
                        @page {
                            margin: 20px; /* Set margin to 20px on all sides */
                        }

                        body {
                            margin: 0; /* Ensure no additional body margin */
                            padding: 0; /* Ensure no additional body padding */
                        }
                    }
                </style>
            </head>
            <body>
                ${headingContent}
                ${subHeadingContent}
                ${tableContent}
                ${freightContent}
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
        if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
            button.parentElement.submit();
        }
    }

    function confirmDeleteAjax(button, entryId) {
    if (confirm('Are you sure you want to delete this transaction?')) {
        var form = button.closest('form');
        var row = button.closest('tr');
        var formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                row.remove();
            } else {
                return response.json().then(data => { throw data; });
            }
        })
        .catch(error => {
            alert('Error deleting entry.');
        });
    }
}

    const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('end_date').value = today;
</script>
@endsection
