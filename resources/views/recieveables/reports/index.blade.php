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
                        <li class="breadcrumb-item active">Purchase Reports</li>
                    </ol>
                </div>
                <h3 class="page-title">Purchase Reports</h3>
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
                                <table id="combined-data-table" class="table table-striped dt-responsive nowrap w-100">
                                    <h4>Purchase Boxboard Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Account Title</th>
                                            <th>Item</th>
                                            <th>Width</th>
                                            <th>Length</th>
                                            <th>Grammage</th>
                                            <th>Qty</th>
                                            <th>Rates</th>
                                            <th>Weights</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trndtl as $data)
                                        <tr>
                                             <td>{{  \Carbon\Carbon::parse($data->date)->format('d-m-Y')  }}</td>
                                            <td>{{ $data->v_type }}-{{ $data->v_no }}</td>
                                            <td>{{ $data->accounts->title ?? 'N/A'}}</td>
                                            <td>{{ $data->purchasedetails->items->item_code ?? 'N/A' }}</td>
                                            <td>{{ $data->purchasedetails->width }}</td>
                                            <td>{{ $data->purchasedetails->lenght }}</td>
                                            <td>{{ $data->purchasedetails->grammage }}</td>
                                            <td>{{ $data->purchasedetails->qty }}</td>
                                            <td>{{ $data->purchasedetails->rate }}</td>
                                            <td>{{ $data->purchasedetails->total_wt }}</td>
                                            <td >{{ $data->purchasedetails->amount }}</td>
                                          
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <table id="combined-data-table" class="table table-striped dt-responsive nowrap w-100">
                <h4>Purchase Return Details</h4>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>V. No</th>
                        <th>Account</th>
                        <th>Item</th>
                        <th>Width</th>
                        <th>Length</th>
                        <th>Grammage</th>
                        <th>Weights</th>
                        <th>Qty</th>
                        <th>Rates</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trndtl1 as $data)
                    <tr>
                        <td>{{  \Carbon\Carbon::parse($data->date)->format('d-m-Y')  }}</td>
                        <td>{{ $data->v_type }}-{{ $data->v_no }}</td>
                        <td>{{ $data->accounts->title  ?? 'N/A' }}</td>
                        <td>{{ $data->purchasereturns->items->item_code ?? 'N/A'}}</td>
                        <td>{{ $data->purchasereturns->width ?? 'N/A'}}</td>
                        <td>{{ $data->purchasereturns->lenght ?? 'N/A' }}</td>
                        <td>{{ $data->purchasereturns->grammage ?? 'N/A' }}</td>
                        <td>{{ $data->purchasereturns->total_wt ?? 'N/A' }}</td>
                        <td>{{ $data->purchasereturns->qty ?? 'N/A' }}</td>
                        <td>{{ $data->purchasereturns->rate ?? 'N/A' }}</td>
                        <td>{{ $data->purchasereturns->amount ?? 'N/A' }}</td>
                        




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
function printTable() {
    // Hide elements with 'no-print' class
    const elementsToHide = document.querySelectorAll('.no-print');
    elementsToHide.forEach(el => el.style.display = 'none');

    // Get the heading and table content you want to print
    const headingContent = document.querySelector('h4').outerHTML;
    const tableContent = document.getElementById('combined-data-table').outerHTML;
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
                        padding: 8px;
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
                ${headingContent}
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

    const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('start_date').value = today;
document.getElementById('end_date').value = today;
</script>
@endsection
