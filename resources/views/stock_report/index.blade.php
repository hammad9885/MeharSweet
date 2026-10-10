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
                            <li class="breadcrumb-item active">Stock Report</li>
                        </ol>
                    </div>
                    <h3 class="page-title no-print">Stock Report</h3>
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
                            <form action="{{ route('stock_report.reports') }}" method="GET" class="form-inline" id="search-form">
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

         
                            
        <!-- Submit Button -->
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Show</button>
            <a class="btn btn-success" href="{{ route('stock_report.list') }}" role="button" onclick="return checkPermission()" >Add New</a>
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
                                    <table id="combined-data-tables" class="table table-striped dt-responsive nowrap w-100">
    <h4 class="no-print">Stock Reports</h4>
    <thead>
        <tr>
            <th>V.No</th>
            <th>Date</th>
            <th>Consumed Item Title</th>
            <th>Consumed Quantity</th>
            <th>Produced Item Title</th>
            <th>Produced Quantity</th>
        </tr>
    </thead>
 <tbody>
    @foreach ($prodCons as $prodCon)
        @php
            // Find the matching produced item for this consumed item
            $prodPro = $prodPros->where('stock_report_id', $prodCon->stock_report_id)->first();
        @endphp
        <tr>
            <td>{{ $prodCon->stock_report_id }}</td>
            <td>{{ $prodCon->created_at->format('d-m-Y') }}</td>

            <td>{{ $prodCon->itemMaster->item_code ?? 'No item_code' }}</td>
            <td>{{ $prodCon->cquantity }}</td>

            <!-- Display the matching produced item data -->
            <td>{{ $prodPro ? $prodPro->itemMaster->item_code ?? 'No item_code' : 'No produced item' }}</td>
            <td>{{ $prodPro ? $prodPro->pquantity : 'No produced quantity' }}</td>
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

        
    </script>
@endsection
