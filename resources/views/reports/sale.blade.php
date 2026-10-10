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
                        <li class="breadcrumb-item active">Sale Reports</li>
                    </ol>
                </div>
                <h3 class="page-title">Sale Reports</h3>
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
                        <form action="{{ route('sale.reports') }}" method="GET" class="form-inline" id="search-form">
                            <div class="row">
                                <!-- Start Date -->
                                <div class="form-group col-xl-2">
                                    <label for="start_date" class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ $startDate }}">
                                </div>
                                <!-- End Date -->
                                <div class="form-group col-xl-2">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $endDate}}">
                                </div>
                                <!-- Voucher No Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="product_type" class="sr-only">Sale Type</label>
                                    <select name="product_type" class="form-control select2" data-toggle="select2"
                                        id="product_type">
                                        <option value="">Select</option>
                                        <option value="CBill" {{ request('product_type')=='CBill' ? 'selected' : '' }}>
                                            Confectionery Billing</option>
                                        <option value="PBill" {{ request('product_type')=='PBill' ? 'selected' : '' }}>
                                            Pharmaceutical Billing</option>
                                        <option value="GBill" {{ request('product_type')=='GBill' ? 'selected' : '' }}>
                                            General Billing</option>
                                    </select>
                                </div>

                                <!-- Show and Add New Buttons -->
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    <a href="{{ route('sale.reports') }}" class="btn btn-secondary">Reset</a>
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
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="col-12">
                                @if(empty($productType))

                                <!-- Confectionery Billing Details Table -->
                                <table id="combined-data-table-cbill"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Confectionery Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $confect_total = 0;
                                        $grouped_confect = [];

                                        // Group by account_title, sum debits, and find latest date
                                        foreach($confect_sales as $sale) {
                                        if (!isset($grouped_confect[$sale->account_title])) {
                                        $grouped_confect[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_confect[$sale->account_title]['total'] += $sale->debit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_confect[$sale->account_title]['latest_date'])) {
                                        $grouped_confect[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $confect_total += $sale->debit;
                                        }
                                        @endphp

                                        @foreach($grouped_confect as $account => $data)
                                        <tr>
                                            <td>{{ $data['latest_date'] }}</td>
                                            <td>{{ $account }}</td>
                                            <td>{{ number_format((float)$data['total'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th>{{ number_format((float)$confect_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Pharmaceutical Billing Details Table -->
                                <table id="combined-data-table-pbill"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Pharmaceutical Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $pharma_total = 0;
                                        $grouped_pharma = [];

                                        // Group by account_title, sum debits, and find latest date
                                        foreach($pharma_sales as $sale) {
                                        if (!isset($grouped_pharma[$sale->account_title])) {
                                        $grouped_pharma[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_pharma[$sale->account_title]['total'] += $sale->debit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_pharma[$sale->account_title]['latest_date'])) {
                                        $grouped_pharma[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $pharma_total += $sale->debit;
                                        }
                                        @endphp

                                        @foreach($grouped_pharma as $account => $data)
                                        <tr>
                                            <td>{{ $data['latest_date'] }}</td>
                                            <td>{{ $account }}</td>
                                            <td>{{ number_format((float)$data['total'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th>{{ number_format((float)$pharma_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>



                                <!-- General Billing Details Table -->
                                <table id="combined-data-table-gbill"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>General Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $general_total = 0;
                                        $grouped_general = [];

                                        // Group by account_title, sum debits, and find latest date
                                        foreach($general_sales as $sale) {
                                        if (!isset($grouped_general[$sale->account_title])) {
                                        $grouped_general[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_general[$sale->account_title]['total'] += $sale->debit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_general[$sale->account_title]['latest_date'])) {
                                        $grouped_general[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $general_total += $sale->debit;
                                        }
                                        @endphp

                                        @foreach($grouped_general as $account => $data)
                                        <tr>
                                            <td>{{ $data['latest_date'] }}</td>
                                            <td>{{ $account }}</td>
                                            <td>{{ number_format((float)$data['total'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th>{{ number_format((float)$general_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Grand Total -->
                                <div style="margin-top: 20px; text-align: right;">
                                    <h4>Final Grand Total: {{ number_format((float)($confect_total + $pharma_total +
                                        $general_total), 2, '.', ',') }}</h4>
                                </div>

                                @elseif($productType == 'CBill')
                                <!-- Confectionery Billing Details Table -->
                                <table id="combined-data-table-cbill"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Confectionery Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $confect_total = 0; @endphp
                                        @foreach($confect_sales as $sale)
                                        <tr>
                                            <td>{{ $sale->date }}</td>
                                            <td>{{ $sale->account_title }}</td>
                                            <td> {{ number_format((float)$sale->debit, 2, '.', ',') }}</td>
                                        </tr>
                                        @php $confect_total += $sale->debit; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th> {{ number_format((float)$confect_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                @elseif($productType == 'PBill')
                                <!-- Pharmaceutical Billing Details Table -->
                                <table id="combined-data-table-pbill"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Pharmaceutical Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $pharma_total = 0; @endphp
                                        @foreach($pharma_sales as $sale)
                                        <tr>
                                            <td>{{ $sale->date }}</td>
                                            <td>{{ $sale->account_title }}</td>
                                            <td>{{ number_format((float)$sale->debit, 2, '.', ',') }}</td>
                                        </tr>
                                        @php $pharma_total += $sale->debit; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th> {{ number_format((float)$pharma_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>







                                @elseif($productType == 'GBill')
                                <!-- General Billing Details Table -->
                                <table id="combined-data-table-pbill"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>General Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $general_total = 0;
                                        $grouped_general = [];

                                        // Group by account_title, sum debits, and find latest date
                                        foreach($general_sales as $sale) {
                                        if (!isset($grouped_general[$sale->account_title])) {
                                        $grouped_general[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_general[$sale->account_title]['total'] += $sale->debit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_general[$sale->account_title]['latest_date'])) {
                                        $grouped_general[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $general_total += $sale->debit;
                                        }
                                        @endphp

                                        @foreach($grouped_general as $account => $data)
                                        <tr>
                                            <td>{{ $data['latest_date'] }}</td>
                                            <td>{{ $account }}</td>
                                            <td>{{ number_format((float)$data['total'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th>{{ number_format((float)$general_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                @endif
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
    const defaultEndDate = formatDate(today);
    const defaultStartDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));

    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    // Only apply JS default dates if server values are empty
    if (startInput && !startInput.value) {
        startInput.value = defaultStartDate;
    }
    if (endInput && !endInput.value) {
        endInput.value = defaultEndDate;
    }
  function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }


function printTable() {
        // Get the HTML content to print
        let printContent = '';
        
        // Check which tables are visible based on product type
        if (document.getElementById('combined-data-table-cbill')) {
            printContent += document.getElementById('combined-data-table-cbill').outerHTML;
        }
        if (document.getElementById('combined-data-table-pbill')) {
            printContent += document.getElementById('combined-data-table-pbill').outerHTML;
        }
        if (document.getElementById('combined-data-table-gbill')) {
            printContent += document.getElementById('combined-data-table-gbill').outerHTML;
        }

        // Get the grand total if it exists
        const grandTotalElement = document.querySelector('h4');
        if (grandTotalElement) {
            printContent += grandTotalElement.outerHTML;
        }

        // Create a new window for printing
        let printWindow = window.open('', '_blank');
        
        // Write the HTML content to the new window
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Sale Report</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    h3, h4 { margin: 10px 0; }
                    .no-print { display: none; }
                </style>
            </head>
            <body>
                <h2>Sale Report</h2>
                ${printContent}
                <script>
                    window.onload = function() {
                        window.print();
                        window.close();
                    };
                <\/script>
            </body>
            </html>
        `);
        
        printWindow.document.close();
    }
 
</script>
@endsection