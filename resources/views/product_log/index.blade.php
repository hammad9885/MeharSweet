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
                        <li class="breadcrumb-item active">Product Log</li>
                    </ol>
                </div>
                <h3 class="page-title">Product Log</h3>
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
                    <div class="col-12">
                        <form action="{{ route('product_log.report') }}" method="GET" class="form-inline"
                            id="search-form">
                            <div class="row">
                                <div class="form-group col-xl-2">
    <label for="start_date" class="form-label">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date"
        value="{{ request()->get('start_date', '') }}">
</div>

<div class="form-group col-xl-2">
    <label for="end_date" class="form-label">End Date</label>
    <input type="date" class="form-control" id="end_date" name="end_date"
        value="{{ request()->get('end_date', '') }}">
</div>

                               <div class="form-group col-xl-2">
    <label for="prod_name" class="form-label">Product Name</label>
    <select id="prod_name" class="form-control" name="prod_name">
        <option value="">Select Product</option>
        @foreach ($productNames as $productName)
            <option value="{{ $productName }}" 
                {{ request()->get('prod_name') == $productName ? 'selected' : '' }}>
                {{ $productName }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group col-xl-2">
    <label for="action" class="form-label">Action</label>
    <select id="action" class="form-control" name="action">
        <option value="">Select Action</option>
        @foreach ($actions as $action)
            <option value="{{ $action }}" 
                {{ request()->get('action') == $action ? 'selected' : '' }}>
                {{ $action }}
            </option>
        @endforeach
    </select>
</div>


                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    
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
                                <!--<h4>Transaction and Purchase Details</h4>-->
                                <table id="combined-data-table" class="table table-striped dt-responsive nowrap w-100">
                                    <h4>Product Log Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Sr</th>
                                            <th>Date</th>
                                            <th>Product Name</th>
                                            <th>Old Rate</th>
                                            <th>New Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                @foreach($productLogs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                         <td>{{ \Carbon\Carbon::parse($log->updated_at)->format('m/d/Y h:i A') }}</td>
                        <td>{{ $log->prod_name }}</td>
                        <td>{{ $log->old_rate }}</td>
                        <td>{{ $log->new_rate }}</td>
                        <td>{{ $log->action }}</td>
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

    function confirmDelete(button) {
        if (confirm('Are you sure you want to delete this record from both tables? This action cannot be undone.')) {
            button.parentElement.submit();
        }
    }

    
</script>
@endsection