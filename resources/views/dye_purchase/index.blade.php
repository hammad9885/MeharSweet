@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Softix</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Purchase Invoice</li>
                    </ol>
                 </div>
                <h3 class="page-title">Dye Purchase</h3>
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
                    <div class="col-6">
                        <form action="{{ route('dye_purchases.reports') }}" method="GET" class="form-inline"
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
                                    <select name="status" class="form-control select2">
                                        <option value="">All</option>

                                        <option value="official" {{ $status=='official' ? 'selected' : '' }}>Official
                                        </option>
                                        <option value="unofficial" {{ $status=='unofficial' ? 'selected' : '' }}>
                                            Unofficial</option>

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
                            
                    


                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    <a class="btn btn-success" href="{{ route('dye_purchase.list') }}" role="button"
                                        onclick="return checkPermission()">Add New</a>
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
                    
                    <div id="print-header" style="display:none;">
    <h3>Wastage Sale Details</h3>
    <h5>Start Date: <span id="display-start-date">{{ request()->get('start_date') ?? 'N/A' }}</span></h5>
    <h5>End Date: <span id="display-end-date">{{ request()->get('end_date') ?? date('Y-m-d') }}</span></h5>
</div>
                    <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print
                        Table</button>
                    <div class="card mt-2">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="col-12">
                                    <!--<h4>Transaction and Purchase Details</h4>-->
                                    <table id="combined-data-table"
                                        class="table table-striped dt-responsive nowrap w-100">
                                        <h4>Dye Purchase Details</h4>
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>V.No</th>
                                                <th class="no-print">Prepared By</th>
                                                <th>Account Title</th>
                                                <th>Item Title</th>
                                                <th>Qty</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Freight</th>
                                                <th class="no-print">Img</th>
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
                                                    $qty = $data->dyepurchases->qty ?? 0;
                                                    $amount = $data->credit ?? 0;
                                                    $totalQty += is_numeric($qty) ? $qty : 0;
                                                    $totalAmount += is_numeric($amount) ? $amount : 0;
                                                @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                                <td>{{ $data->v_type }}-{{ $data->v_no }}</td>
                                                <td class="no-print">{{ $data->preparedby ?? 'N/A' }}</td>
                                                <td>{{ $data->accounts->title ?? 'N/A' }}</td>
                                                <td>{{ $data->dyepurchases->items->item_code ?? 'N/A' }}</td>
                                                <td>{{ $data->dyepurchases->qty ?? 'N/A' }}</td>
                                                <td>{{ $data->description ?? 'N/A' }}</td>
                                                <td>{{ $data->credit ?? 'N/A' }}</td>
                                                <td>{{ $data->dyepurchases->freight ?? 'N/A' }}</td>
                                                <td class="no-print">>
                                                    @if (!empty($data->dyepurchases->file_path))
                                                    <a href="{{ asset('/storage/' . $data->dyepurchases->file_path) }}"
                                                        target="_blank">
                                                        <p>YES</p>
                                                    </a>
                                                    @else
                                                    <p>NO</p>
                                                    @endif
                                                </td>
                                                <td class="no-print">
                                                    <input type="checkbox" class="status-checkbox"
                                                        data-id="{{ $data->id }}" {{ $data->status == 'official' ?
                                                    'checked' : '' }}>
                                                </td>
                                                <td class="no-print">
                                                    <form
                                                        action="{{ route('dye_purchase.delete', ['id' => $data->id]) }}"
                                                        method="POST" style="display:inline;"
                                                        onclick="return checkPermissionDel()">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete(this)">Delete</button>
                                                    </form>
                                                    <a href="{{ route('dye_purchase.edit', ['v_no' => $data->v_no]) }}"
                                                        class="btn btn-warning btn-sm"
                                                        onclick="return checkPermissionEdit()">Edit</a>
                                                        
                                                    <a href="{{ route('dye_purchase.editDye', ['v_no' => $data->v_no]) }}" class="btn btn-primary btn-sm mt-1" >Freight</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="7" style="text-align: right; font-weight: bold;">Total Quantity:</td>
                                                <td style="font-weight: bold;">{{ number_format($totalQty, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" style="text-align: right; font-weight: bold;">Total Amount:</td>
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
            let canAdd = @json(auth()->user()->is_admin == 1 || (\App\Models\Right::where('user_id', auth()->user()->id)->where('app_name', 'dyePurchase')->first()->add ?? 0) == 1);
            if (!canAdd) {
                alert('You do not have Permission to Add');
                return false;
            }
            return true;
        }

        function checkPermissionEdit() {
            let canEdit = @json(auth()->user()->is_admin == 1 || (\App\Models\Right::where('user_id', auth()->user()->id)->where('app_name', 'dyePurchase')->first()->edit ?? 0) == 1);
            if (!canEdit) {
                alert('You do not have Permission to Edit');
                return false;
            }
            return true;
        }

        function checkPermissionDel() {
            let canDelete = @json(auth()->user()->is_admin == 1 || (\App\Models\Right::where('user_id', auth()->user()->id)->where('app_name', 'dyePurchase')->first()->del ?? 0) == 1);
            if (!canDelete) {
                alert('You do not have Permission to Delete');
                return false;
            }
            return true;
        }


        function confirmDelete(button) {
            if (confirm('Are you sure you want to delete this record from both tables? This action cannot be undone.')) {
                button.parentElement.submit();
            }
        }

// Function to format the date as YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Get today's date
const today = new Date();

// Format today's date
const endDate = formatDate(today);

// Set the value of the input element with ID 'end_date'
document.getElementById('end_date').value = endDate;

function printTable() {
    // Get the HTML content to print
    var printContents = document.getElementById('print-header').innerHTML;
    
    // Add Bootstrap print styles with full screen adjustments
    printContents += `
        <style>
            @media print {
                body {
                    font-family: Arial, sans-serif;
                    padding: 0;
                    margin: 0;
                }
                .table {
                    width: 100%;
                    margin-bottom: 0;
                    color: #212529;
                    border-collapse: collapse;
                    font-size: 12px;
                }
                .table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid #dee2e6;
                    background-color: #f8f9fa !important;
                }
                .table td, .table th {
                    padding: 0.5rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                }
                .no-print, .no-print * {
                    display: none !important;
                }
                .table-striped tbody tr:nth-of-type(odd) {
                    background-color: rgba(0, 0, 0, 0.05);
                }
                .table-responsive {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    margin: 0;
                }
                h3 {
                    color: #343a40;
                    margin: 10px 0;
                    font-size: 18px;
                }
                h5 {
                    color: #6c757d;
                    margin: 8px 0;
                    font-size: 14px;
                }
                @page {
                    size: auto;  /* auto is the initial value */
                    margin: 0mm; /* this affects the margin in the printer settings */
                }
            }
        </style>
    `;
    
    // Get the table HTML
    var table = document.getElementById('combined-data-table').cloneNode(true);
    
    // Remove action buttons and status checkboxes from the cloned table
    var noPrintElements = table.querySelectorAll('.no-print');
    noPrintElements.forEach(function(element) {
        element.remove();
    });
    
    // Add the responsive wrapper and table to print contents
    printContents += '<div class="table-responsive">' + table.outerHTML + '</div>';
    
    // Create a new window for printing
    var originalContents = document.body.innerHTML;
    var printWindow = window.open('', '', 'height=1000,width=1200');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Wastage Sale Report</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body style="margin: 0; padding: 10px;">
                ${printContents}
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"><\/script>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for Bootstrap CSS to load before printing
    printWindow.onload = function() {
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}
    </script>
@endsection
