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
                <h3 class="page-title">Expense Report</h3>
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
                        <form action="{{ route('expense.reports') }}" method="GET" class="form-inline col-xl-6"
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
                                    <label for="status" class="sr-only">Status</label>
                                    <select name="status" class="form-control select2">
                                        <option value="">All</option>
                                        <option value="official" {{ request()->get('status') == 'official' ? 'selected'
                                            : '' }}>Official</option>
                                        <option value="unofficial" {{ request()->get('status') == 'unofficial' ?
                                            'selected' : '' }}>Unofficial</option>
                                    </select>
                                </div>
                                
                                <div class="form-group col-xl-4 mt-2">
    <label for="level2_title">Select Title</label>
    <select name="level2_title" id="level2_title" class="form-control select2" data-toggle="select2">
        <option value="">All</option>
        @foreach ($level2Titles as $title)
            <option value="{{ $title }}" {{ request('level2_title') == $title ? 'selected' : '' }}>
                {{ $title }}
            </option>
        @endforeach
    </select>
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
    <p><strong>Start Date:</strong> {{ request()->get('start_date', 'N/A') }} | <strong>End Date:</strong> {{ request()->get('end_date', 'N/A') }}</p>
</div>

                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print
                    Table</button>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="col-12">
 <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
    <h4 class="page-title">Expense Report Summary</h4>
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $grandTotal = 0;
            $groups = [];
            
            // First group all items by LEVEL2_TITLE
            foreach($result as $row) {
                $groupKey = $row->LEVEL2_TITLE;
                if (!isset($groups[$groupKey])) {
                    $groups[$groupKey] = [
                        'items' => [],
                        'total' => 0,
                        'latest_date' => $row->latest_date
                    ];
                }
                $groups[$groupKey]['items'][] = $row;
                $groups[$groupKey]['total'] += $row->total_amount;
                $grandTotal += $row->total_amount;
                
                // Keep track of the latest date for this group
                if (strtotime($row->latest_date) > strtotime($groups[$groupKey]['latest_date'])) {
                    $groups[$groupKey]['latest_date'] = $row->latest_date;
                }
            }
            
            // Sort groups alphabetically by LEVEL2_TITLE
            ksort($groups);
            
            // Now display each group
            foreach($groups as $groupTitle => $groupData) {
        @endphp
                <tr class="group-header">
                    <td>{{ \Carbon\Carbon::parse($groupData['latest_date'])->format('Y-m-d') }}</td>
                    <td><strong>{{ $groupTitle }}</strong></td>
                    <td><strong>{{ number_format($groupData['total'], 2) }}</strong></td>
                </tr>
                
                @foreach($groupData['items'] as $index => $item)
                <tr class="sub-item">
                    <td></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $index+1 }}. {{ $item->account_title }}</td>
                    <td>{{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
        @php
            }
        @endphp
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-right">Grand Total:</th>
            <th>{{ number_format($grandTotal, 2) }}</th>
        </tr>
    </tfoot>
</table>

<style>
    .group-header {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .sub-item {
        background-color: #ffffff;
    }
    .sub-item td:first-child {
        border-left: 1px solid #dee2e6;
    }
</style>
</div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
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
                        padding: 2px !important; /* Remove padding completely */
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
            let amount = parseFloat(row.cells[3].innerText.replace(/,/g, '')) || 0;
            total += amount;
        });
        document.getElementById("grand-total").innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    });
</script>
@endsection