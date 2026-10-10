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
                        <li class="breadcrumb-item active">Purchase Invoice</li>
                    </ol>
                </div>
                <h3 class="page-title">General Billing</h3>
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

    <!-- Show Form -->
    <div class="row">
        <div class="card mt-2">
            <div class="card-body">
                <div class="tab-content">
                    <div class="col-12">
                        <form action="{{ route('general_billing.report') }}" method="GET" class="form-inline" id="search-form">
    <div class="row">
        <!-- Start Date -->
        <div class="form-group col-xl-2">
            <label for="start_date" class="sr-only">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <!-- End Date -->
        <div class="form-group col-xl-2">
            <label for="end_date" class="sr-only">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <!-- Status Dropdown -->
        <div class="form-group col-xl-2">
            <label for="account_title" class="sr-only">Status</label>
            <select name="status" class="form-control select2">
                <option value="">All</option>
                <option value="official" {{ request('status') == 'official' ? 'selected' : '' }}>Official</option>
                <option value="unofficial" {{ request('status') == 'unofficial' ? 'selected' : '' }}>Unofficial</option>
            </select>
        </div>
        
        <div class="form-group col-xl-2">
            <label class="sr-only">Billing Number</label>
            <select name="billing_no" class="form-control select2" data-toggle="select2">
                <option value="">Select</option>
                @foreach($generalBillings->unique('party_no') as $billing)
                    <option value="{{ $billing->party_no ?? 'N/A' }}" {{ request('billing_no') == ($billing->party_no ?? 'N/A') ? 'selected' : '' }}>
                        {{ $billing->party_no ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group col-xl-2">
            <label class="sr-only">Product Type</label>
            <select name="product_type" class="form-control select2" data-toggle="select2">
                <option value="">Select</option>
                @foreach($generalBillings->unique('product_type') as $billing)
                    <option value="{{ $billing->product_type ?? 'N/A' }}" {{ request('product_type') == ($billing->product_type ?? 'N/A') ? 'selected' : '' }}>
                        {{ $billing->product_type ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group col-xl-2">
            <label class="sr-only">Party</label>
            <select name="party_name" class="form-control select2" data-toggle="select2">
                <option value="">Select</option>
                @foreach($generalBillings->unique('party_name') as $billing)
                    <option value="{{ $billing->party_name ?? 'N/A' }}" {{ request('party_name') == ($billing->party_name ?? 'N/A') ? 'selected' : '' }}>
                        {{ $billing->party_name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Show Previous Balance Radio Buttons -->
        <div class="form-group col-xl-2">
            <label class="form-label">Show Previous Balance?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="show_prev_balance" id="showPrevYes" value="1" {{ request('show_prev_balance', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="showPrevYes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="show_prev_balance" id="showPrevNo" value="0" {{ request('show_prev_balance', '1') == '0' ? 'checked' : '' }}>
                <label class="form-check-label" for="showPrevNo">No</label>
            </div>
        </div>
        
        <!-- Show and Add New Buttons -->
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Show</button>
            <a class="btn btn-success" href="{{ route('general_billing.list') }}" role="button" onclick="return checkPermission()">Add New</a>
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
                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print
                    Table</button>
                <!-- First Table -->
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="col-12">
                                <table id="combined-data-table" class="table table-striped dt-responsive nowrap w-100">
                                    <h3>General Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Billing No</th>
                                            <th>Party</th>
                                            <th>Item</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @foreach ($generalBillings as $general)
                                        <tr>
            <td>{{ \Carbon\Carbon::parse($general->updated_at)->format('Y-m-d') ?? 'N/A' }}</td>
            <td>{{ $general->party_type ?? 'N/A' }}-{{ $general->party_no ?? 'N/A' }} </td>
            <td>{{ $general->party_name ?? 'N/A' }} </td>
            <td>{{ $general->item_name ?? 'N/A' }} </td>
            <td>{{ $general->amount ?? 'N/A' }} </td>

            <td class="no-print">
   
    <form action="{{ route('general_billing.destroy', $general->id) }}" method="POST" style="display:inline-block;" onclick="return checkPermissionDel()">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this transaction?')">Delete</button>
    </form>
</td>
            
            
            
            
            
            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- Second Table -->
                                
                                
                                
                                <table id="print-data-table" class="table table-striped dt-responsive nowrap w-100" style="display: none;">
                                    <div style="display: none;">
    <h2>{{ request('status') == 'official' ? 'Printing Cell' : 'Rizwan Packages' }}</h2>
    <!-- Remove the direct access to updated_at on the collection -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
    <h3 style="margin: 0;">Date: {{ $generalBillings->first() ? \Carbon\Carbon::parse($generalBillings->first()->updated_at)->format('Y-m-d') : 'N/A' }}</h3>
    <h3 style="margin: 0;">Billing No: {{ $generalBillings->first()->party_no ?? 'N/A' }}</h3>
</div>
    <h3>Name: {{ $generalBillings->first()->party_name  ?? 'N/A'  }}</h3>
    
    </div>
    <thead>
        <tr>
            <th>Date</th>
            <th>V No</th>
            <th>Poduct Type</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Freight</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
    @php
        $grandTotal = 0; // Initialize grand total variable
    @endphp
    @foreach ($generalBillings as $general)
    <tr>
        <td>{{ \Carbon\Carbon::parse($general->updated_at)->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $general->v_no ?? 'N/A' }} </td>
        <td>{{ $general->product_type ?? 'N/A' }} </td>
        <td>{{ $general->item_name ?? 'N/A' }} </td>
        <td>{{ $general->qty ?? 'N/A' }} </td>
        <td>{{ $general->rate ?? 'N/A' }} </td>
        <td>{{ $general->freight ?? 'N/A' }} </td>
        <td>{{ $general->amount ?? 'N/A' }} </td>
    </tr>
    @php
        $grandTotal += $general->amount ?? 0; // Add each amount to grand total
    @endphp
    @endforeach
    <tr>
        <td colspan="7" style="text-align: right;"><strong>Grand Total:</strong></td>
        <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
    </tr>
</tbody>
    
<tfoot>
<tr>
            <td colspan="3" style="text-align: right; font-weight: bold;">Amount of this Month: </td>
            <td colspan="5" style="text-align: left; font-weight: bold;">{{ number_format($grandTotal, 2)  ?? 'N/A' }}</td>
        </tr>
        @if(request('show_prev_balance', '1') == '1')
        <tr>
            <td colspan="3" style="text-align: right; font-weight: bold;">Previous Balance: </td>
            <td colspan="5" style="text-align: left;font-weight: bold;">
    {{ $generalBillings->first() ? number_format($generalBillings->first()->pre_bal, 2) : 'N/A' }}
</td>
        </tr>
        <tr>
    <td colspan="3" style="text-align: right; font-weight: bold;">Total: </td>
    <td colspan="5" style="text-align: left; font-weight: bold;">
        @if($generalBillings->first())
            {{ number_format($generalBillings->first()->pre_bal + $grandTotal, 2) }}
        @else
            N/A
        @endif
    </td>
</tr>
        @else
        <tr>
            <td colspan="3" style="text-align: right; font-weight: bold;">Total: </td>
            <td colspan="5" style="text-align: left; font-weight: bold;">{{ number_format($grandTotal, 2) }}</td>
        </tr>
        @endif
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
                ->where('app_name', 'generalbilling')
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
                ->where('app_name', 'generalbilling')
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
                ->where('app_name', 'generalbilling')
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
    // Show the elements to be printed
    const printHeader = document.querySelector('div[style="display: none;"]');
    const printTable = document.getElementById('print-data-table');
    printHeader.style.display = 'block';
    printTable.style.display = 'table';

    // Get content for printing
    const headerContent = printHeader.outerHTML;
    const tableContent = printTable.outerHTML;
    const originalContents = document.body.innerHTML;

    // Replace body content with the header and table content for printing
    document.body.innerHTML = `
        <html>
            <head>
                <title>Print Table</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        margin: 20px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 6px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    h2, h3 {
                        margin: 5px 0;
                    }
                    .text-right {
                        text-align: right;
                    }
                    .text-left {
                        text-align: left;
                    }
                
                    .flex-between {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                </style>
            </head>
            <body>
                ${headerContent}
                ${tableContent}
            </body>
        </html>
    `;

    // Trigger print dialog
    window.print();

    // Restore original content and hide the elements again
    document.body.innerHTML = originalContents;
    printHeader.style.display = 'none';
    printTable.style.display = 'none';

    // Reattach event listeners or reload the page if needed
    window.location.reload();
}

  const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('end_date').value = today;

</script>



@endsection