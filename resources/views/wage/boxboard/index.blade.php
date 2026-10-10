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
                        <li class="breadcrumb-item active">Boxboard Department</li>
                    </ol>
                </div>
                <h3 class="page-title">Boxboard Department</h3>
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
                        <form action="{{ route('boxboard_wage.report') }}" method="GET" class="form-inline" id="search-form">
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
        
    
        <!-- Show and Add New Buttons -->
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Show</button>
            <a class="btn btn-success" href="{{ route('boxboard_wage.list') }}" role="button" onclick="return checkPermission()">Add New</a>
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
                                    <h3>Boxboard Department Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Salary No</th>
                                            <th>Employee</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    @php
        $processedEntries = []; // Array to track processed entries
    @endphp
    
    @foreach ($wageBoxboards as $general)
        @php
            // Create a unique key for each entry (adjust based on what makes an entry unique)
            $entryKey = $general->b_no . '-' . $general->employee_name . '-' . $general->total_amount;
            
            // Skip if this entry has already been processed
            if (in_array($entryKey, $processedEntries)) {
                continue;
            }
            
            // Add to processed entries
            $processedEntries[] = $entryKey;
        @endphp
        
        <tr>
            <td>{{ \Carbon\Carbon::parse($general->updated_at)->format('Y-m-d') ?? 'N/A' }}</td>
            <td>{{ $general->v_type ?? 'N/A' }}-{{ $general->b_no ?? 'N/A' }}</td>
            <td>{{ $general->employee_name ?? 'N/A' }}</td>
            <td>{{ $general->total_amount ?? 'N/A' }}</td>
            <td class="no-print">
                <form action="{{ route('boxboard_wage.destroy', $general->id) }}" method="POST" style="display:inline-block;" onclick="return confirm('Are you sure you want to delete this transaction?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"    onclick="return checkPermissionDel()">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
                                </table>
                                <!-- Second Table -->
                                
                                
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
                ->where('app_name', 'boxboardcalculator')
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
                ->where('app_name', 'boxboardcalculator')
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
                ->where('app_name', 'boxboardcalculator')
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