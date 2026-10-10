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
                    <h3 class="page-title">Office Cash</h3>
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
            <div class="card">
                <div class="card-body">




                    <div class="tab-content">
                        <div class="col-12">
                            <form action="{{ route('office_cash.reports') }}" method="GET" class="form-inline col-xl-6"
                                id="search-form">
                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label for="start_date" class="sr-only">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request()->get('start_date') }}">
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="end_date" class="sr-only">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request()->get('end_date') }}">
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
                                    @foreach($vNoList as $vNo)
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
        @foreach($accountIdList as $id)
            @php
                $account = \App\Models\AccountMaster::find($id);
            @endphp
            <option value="{{ $id }}" {{ request()->get('account_id') == $id ? 'selected' : '' }}>
                {{ $account ? $account->title : 'Unknown Account' }}
            </option>
        @endforeach
    </select>
</div>


                                    <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    <a class="btn btn-success" href="{{ route('office_cash.list') }}" onclick="return checkPermission()" role="button">Add New</a>
                                </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Ledger Table -->
        <div class="row">
            <div class="card">
                <div class="card-body">


                    <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print Table</button>
                    <div class="card mt-2">
                        <div class="card-body">

                    <div class="tab-content">
                        <div class="col-12">
                         

                            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                <h4 class="page-title">Office Cash Detail</h4>
                                   <h5>Start Date: {{ request()->get('start_date') ?? 'N/A' }} | End Date: {{ request()->get('end_date') ?? date('Y-m-d') }}</h5>
                                 
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>V. No</th>
                                        <th>Cash</th>
                                        <th>Account Title</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th class="no-print">Image</th>
                                        <th class="no-print">Status</th>
                                        <th class="no-print">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                               
                                    @foreach ($trndtls as $trndtl)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($trndtl->date)->format('d-m-Y') }}</td>
                                            <td>{{ $trndtl->v_type }}-{{ $trndtl->v_no }}</td>
                                            <td>{{ $trndtl->cashes->title }}</td>
                                            <td>{{ $trndtl->accounts->title }}</td> <!-- Assuming account relation -->
                                            <td>{{ $trndtl->description }}</td>
                                            <td>{{ number_format($trndtl->debit, 2) }}</td>
                                            <td class="no-print">
                                                                        @if (!empty($trndtl->file_id))
                                                                            <a href="{{ asset('/storage/' . $trndtl->file_id) }}"
                                                                                target="_blank">
                                                                                <p>Img</p>
                                                                            </a>
                                                                        @else
                                                                            <p>No Img</p>
                                                                        @endif
                                                                    </td>
                                            <td class="no-print">
                                                <input type="checkbox"
                                                       class="status-checkbox"
                                                       data-id="{{ $trndtl->id }}"
                                                       {{ $trndtl->status == 'official' ? 'checked' : '' }}>
                                            </td>
                                            <td class="no-print">

                                                <!-- Edit button -->
                                                <a href="{{ route('office_cash.edit', $trndtl->v_no) }}"
                                                    class="btn btn-warning btn-sm" onclick="return checkPermissionEdit()">Edit</a>
                                            
                                                <!-- Delete button (use form for method spoofing) -->
                                                <form action="{{ route('office_cash.delete', $trndtl->id) }}"
                                                    method="POST" style="display:inline-block;" onclick="return checkPermissionDel()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this transaction?')">Delete</button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
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
                ->where('app_name', 'officeCash')
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
                ->where('app_name', 'officeCash')
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
                ->where('app_name', 'officeCash')
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
    
    
    
        const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('end_date').value = today;
function printTable() {
    // Hide elements with 'no-print' class
    const elementsToHide = document.querySelectorAll('.no-print');
    elementsToHide.forEach(el => el.style.display = 'none');

    // Get all headings (both h4 and h5) and table content
    const headings = document.querySelectorAll('.col-12 h4, .col-12 h5');
    let headingsContent = '';
    headings.forEach(heading => {
        headingsContent += heading.outerHTML;
    });
    
    const tableContent = document.getElementById('basic-datatable').outerHTML;
    const originalContents = document.body.innerHTML;

    // Replace body content with the headings and table HTML for printing
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
                    .no-print {
                        display: none;
                    }
                    h4, h5 {
                        margin: 5px 0;
                    }
                </style>
            </head>
            <body>
                ${headingsContent}
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
    </script>
@endsection
