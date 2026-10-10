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
                <h3 class="page-title">Wastage Sale Report</h3>
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
        <div class="card">
            <div class="card-body">
                <div class="tab-content">
                    <div class="col-12">
                        <form action="{{ route('wastage.reports') }}" method="GET" class="form-inline col-xl-6"
                            id="search-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
                <!-- Initially Hidden Date Section -->
             <div id="print-dates" style="display: none;">
    <p><strong>Start Date:</strong> {{ $startDate ?? 'N/A' }}</p>
    <p><strong>End Date:</strong> {{ $endDate ?? 'N/A' }}</p>
</div>

                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print Table</button>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="col-12">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <h4 class="page-title">Wastage Report Summary</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Account Title</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wastageData as $data)
                                            <tr>
                                                <td>{{ $data->latest_date }}</td>
                                                <td>{{ $data->item_code }}</td>
                                                <td>{{ number_format($data->total_sum, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Grand Total:</th>
                                            <th id="grand-total">0.00</th>
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
    // Get today's date
   const today = new Date();
const endDate = formatDate(today);
const startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));

const startInput = document.getElementById('start_date');
const endInput = document.getElementById('end_date');

if (!startInput.value) {
    startInput.value = startDate;
}
if (!endInput.value) {
    endInput.value = endDate;
}

    // Helper function to format the date as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function printTable() {
        // Show the date section before printing
        document.getElementById('print-dates').style.display = 'block';

        const headingContent = document.querySelector('h4').outerHTML;
        const printDates = document.getElementById('print-dates').outerHTML;
        const printContents = document.getElementById('basic-datatable').outerHTML;
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
                    </style>
                </head>
                <body>
                    ${headingContent}
                    ${printDates}
                    ${printContents}
                </body>
            </html>
        `;

        window.print();

        // Restore the original page content after printing
        document.body.innerHTML = originalContents;
        window.location.reload();
    }

    document.addEventListener("DOMContentLoaded", function () {
        let total = 0;
        document.querySelectorAll("tbody tr").forEach(row => {
            let amount = parseFloat(row.cells[2].innerText.replace(/,/g, '')) || 0;
            total += amount;
        });
        document.getElementById("grand-total").innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    });
</script>
@endsection