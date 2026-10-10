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
                        <li class="breadcrumb-item active">Bank/Cash Reports</li>
                    </ol>
                </div>
                <h3 class="page-title">Bank/Cash Reports</h3>
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
                        <form action="{{ route('bank_cash.reports') }}" method="GET" class="form-inline"
                            id="search-form">
                            <div class="row">
                                <!-- Start Date -->
                                <div class="form-group col-xl-2">
                                    <label for="start_date" class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ request('start_date') }}">
                                </div>
                                <!-- End Date -->
                                <div class="form-group col-xl-2">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ request('end_date') }}">
                                </div>
                                <!-- Party Dropdown -->
                                <div class="form-group col-xl-3">
                                    <label for="party_name" class="sr-only">Party Name</label>
                                    <select name="party_name" class="form-control select2" data-toggle="select2"
                                        id="party_name">
                                        <option value="">All Parties</option>
                                        @foreach($allParties as $party)
                                        <option value="{{ $party }}" {{ request('party_name')==$party ? 'selected' : ''
                                            }}>
                                            {{ $party }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Voucher No Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="product_type" class="sr-only">Type</label>
                                    <select name="product_type" class="form-control select2" data-toggle="select2"
                                        id="product_type">
                                        <option value="">Select</option>
                                        <option value="Bank" {{ request('product_type')=='Bank' ? 'selected' : '' }}>
                                            Bank</option>
                                        <option value="Cash" {{ request('product_type')=='Cash' ? 'selected' : '' }}>
                                            Cash</option>
                                        <option value="JV" {{ request('product_type')=='JV' ? 'selected' : '' }}>
                                            JV</option>
                                        <option value="Che" {{ request('product_type')=='Chq' ? 'selected' : '' }}>
                                            Chq</option>
                                    </select>
                                </div>

                                <!-- Show and Add New Buttons -->
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    <a href="{{ route('bank_cash.reports') }}" class="btn btn-secondary">Reset</a>
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

                                <!-- Bank Details Table -->
                                <table id="combined-data-table-bank"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Bank Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $bank_total = 0;
                                        $grouped_bank = [];

                                        // Group by account_title, sum credits, and find latest date
                                        foreach($bank_sales as $sale) {
                                        if (!isset($grouped_bank[$sale->account_title])) {
                                        $grouped_bank[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_bank[$sale->account_title]['total'] += $sale->credit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_bank[$sale->account_title]['latest_date'])) {
                                        $grouped_bank[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $bank_total += $sale->credit;
                                        }
                                        @endphp

                                        @foreach($grouped_bank as $account => $data)
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
                                            <th>{{ number_format((float)$bank_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Cash Billing Details Table -->
                                <table id="combined-data-table-cash"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Cash Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $cash_total = 0;
                                        $grouped_cash = [];

                                        // Group by account_title, sum credits, and find latest date
                                        foreach($cash_sales as $sale) {
                                        if (!isset($grouped_cash[$sale->account_title])) {
                                        $grouped_cash[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_cash[$sale->account_title]['total'] += $sale->credit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_cash[$sale->account_title]['latest_date'])) {
                                        $grouped_cash[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $cash_total += $sale->credit;
                                        }
                                        @endphp

                                        @foreach($grouped_cash as $account => $data)
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
                                            <th>{{ number_format((float)$cash_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>


                                <!-- jv Billing Details Table -->
                                <table id="combined-data-table-jv"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>JV Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $jv_total = 0;
                                        $grouped_jv = [];

                                        // Group by account_title, sum credits, and find latest date
                                        foreach($jv_sales as $sale) {
                                        if (!isset($grouped_jv[$sale->account_title])) {
                                        $grouped_jv[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_jv[$sale->account_title]['total'] += $sale->credit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_jv[$sale->account_title]['latest_date'])) {
                                        $grouped_jv[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $jv_total += $sale->credit;
                                        }
                                        @endphp

                                        @foreach($grouped_jv as $account => $data)
                                        @if((float)$data['total'] != 0.00)
                                        <tr>
                                            <td>{{ $data['latest_date'] }}</td>
                                            <td>{{ $account }}</td>
                                            <td>{{ number_format((float)$data['total'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th>{{ number_format((float)$jv_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- chq Billing Details Table -->
                                <table id="combined-data-table-chq"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Cheque Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $chq_total = 0;
                                        $grouped_chq = [];

                                        // Group by account_title, sum debits, and find latest date
                                        foreach($chq_sales as $sale) {
                                        if (!isset($grouped_chq[$sale->account_title])) {
                                        $grouped_chq[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_chq[$sale->account_title]['total'] += $sale->debit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_chq[$sale->account_title]['latest_date'])) {
                                        $grouped_chq[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $chq_total += $sale->debit;
                                        }
                                        @endphp

                                        @foreach($grouped_chq as $account => $data)
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
                                            <th>{{ number_format((float)$chq_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Grand Total -->
                                <div style="margin-top: 20px; text-align: right;">
                                    <h4>Final Grand Total: {{ number_format((float)($bank_total + $cash_total +
                                        $jv_total + $chq_total ), 2, '.',
                                        ',') }}</h4>
                                </div>

                                @elseif($productType == 'Bank')
                                <!-- Bank Details Table -->
                                <table id="combined-data-table-bank"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Bank Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $bank_total = 0;
                                        $grouped_bank = [];

                                        // Group by account_title, sum credits, and find latest date
                                        foreach($bank_sales as $sale) {
                                        if (!isset($grouped_bank[$sale->account_title])) {
                                        $grouped_bank[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_bank[$sale->account_title]['total'] += $sale->credit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_bank[$sale->account_title]['latest_date'])) {
                                        $grouped_bank[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $bank_total += $sale->credit;
                                        }
                                        @endphp

                                        @foreach($grouped_bank as $account => $data)
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
                                            <th>{{ number_format((float)$bank_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                @elseif($productType == 'Cash')
                                <!-- Cash Details Table -->
                                <table id="combined-data-table-cash"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Cash Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $cash_total = 0;
                                        $grouped_cash = [];

                                        // Group by account_title, sum credits, and find latest date
                                        foreach($cash_sales as $sale) {
                                        if (!isset($grouped_cash[$sale->account_title])) {
                                        $grouped_cash[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_cash[$sale->account_title]['total'] += $sale->credit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_cash[$sale->account_title]['latest_date'])) {
                                        $grouped_cash[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $cash_total += $sale->credit;
                                        }
                                        @endphp

                                        @foreach($grouped_cash as $account => $data)
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
                                            <th>{{ number_format((float)$cash_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                @elseif($productType == 'JV')
                                <table id="combined-data-table-jv"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>JV Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $jv_total = 0;
                                        $grouped_jv = [];

                                        // Group by account_title, sum credits, and find latest date
                                        foreach($jv_sales as $sale) {
                                        if (!isset($grouped_jv[$sale->account_title])) {
                                        $grouped_jv[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_jv[$sale->account_title]['total'] += $sale->credit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_jv[$sale->account_title]['latest_date'])) {
                                        $grouped_jv[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $jv_total += $sale->credit;
                                        }
                                        @endphp

                                        @foreach($grouped_jv as $account => $data)
                                        @if((float)$data['total'] != 0.00)
                                        <tr>
                                            <td>{{ $data['latest_date'] }}</td>
                                            <td>{{ $account }}</td>
                                            <td>{{ number_format((float)$data['total'], 2, '.', ',') }}</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: right;">Grand Total:</th>
                                            <th>{{ number_format((float)$jv_total, 2, '.', ',') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                @elseif($productType == 'Chq')
                                <table id="combined-data-table-chq"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Cheque Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Party</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $chq_total = 0;
                                        $grouped_chq = [];

                                        // Group by account_title, sum debits, and find latest date
                                        foreach($chq_sales as $sale) {
                                        if (!isset($grouped_chq[$sale->account_title])) {
                                        $grouped_chq[$sale->account_title] = [
                                        'latest_date' => $sale->date,
                                        'total' => 0
                                        ];
                                        }
                                        $grouped_chq[$sale->account_title]['total'] += $sale->debit;
                                        // Update latest date if current sale is newer
                                        if (strtotime($sale->date) >
                                        strtotime($grouped_chq[$sale->account_title]['latest_date'])) {
                                        $grouped_chq[$sale->account_title]['latest_date'] = $sale->date;
                                        }
                                        $chq_total += $sale->debit;
                                        }
                                        @endphp

                                        @foreach($grouped_chq as $account => $data)
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
                                            <th>{{ number_format((float)$chq_total, 2, '.', ',') }}</th>
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
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    //   document.getElementById('start_date').value = formatDate(firstDayOfMonth);
    document.getElementById('end_date').value = formatDate(today);
    
    function printTable() {
    // Get the current page title
    const pageTitle = document.querySelector('.page-title').innerText;

    // Get search criteria
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const productType = document.getElementById('product_type').value;
    const partyName = document.getElementById('party_name').value;
    const partyText = partyName ? document.querySelector(`#party_name option[value="${partyName}"]`).text : 'All Parties';

    // Store original content
    const originalContents = document.body.innerHTML;

    // Create print content
    let printContent = `
    <html>
        <head>
            <title>${pageTitle}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 10px;
                    font-size: 12px;
                }
                .print-header {
                    margin-bottom: 10px;
                    padding-bottom: 5px;
                    border-bottom: 1px solid #ddd;
                }
                .print-header h2 {
                    margin: 0 0 5px 0;
                    font-size: 16px;
                }
                .print-details {
                    margin-bottom: 10px;
                }
                .print-details div {
                    margin: 2px 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                    font-size: 12px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 2px !important;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
                .section-title {
                    font-weight: bold;
                    margin: 10px 0 5px 0;
                    font-size: 14px;
                }
                .grand-total {
                    font-weight: bold;
                    text-align: right;
                    margin-top: 10px;
                    font-size: 13px;
                }
                @page {
                    size: auto;
                    margin: 5mm;
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h2>${pageTitle}</h2>
                <div class="print-details">
                    <div><strong>Date Range:</strong> ${startDate} to ${endDate}</div>
                    <div><strong>Party:</strong> ${partyText}</div>
    `;

    if (productType) {
        const typeText = document.querySelector(`#product_type option[value="${productType}"]`).text;
        printContent += `<div><strong>Type:</strong> ${typeText}</div>`;
    }

    printContent += `</div></div>`;

    // If no specific product type is selected, print all tables
    if (!productType) {
        // Bank Details Table
        const bankTable = document.getElementById('combined-data-table-bank');
        if (bankTable) {
            printContent += `<div class="section-title">Bank Details</div>`;
            printContent += bankTable.outerHTML;
        }

        // Cash Details Table
        const cashTable = document.getElementById('combined-data-table-cash');
        if (cashTable) {
            printContent += `<div class="section-title">Cash Details</div>`;
            printContent += cashTable.outerHTML;
        }

        // JV Details Table
        const jvTable = document.getElementById('combined-data-table-jv');
        if (jvTable) {
            printContent += `<div class="section-title">JV Details</div>`;
            printContent += jvTable.outerHTML;
        }

        // Cheque Details Table
        const chqTable = document.getElementById('combined-data-table-chq');
        if (chqTable) {
            printContent += `<div class="section-title">Cheque Details</div>`;
            printContent += chqTable.outerHTML;
        }

        // Add grand total if tables exist
        let bankTotal = 0, cashTotal = 0, jvTotal = 0, chqTotal = 0;

        if (bankTable) {
            bankTotal = parseFloat(bankTable.querySelector('tfoot th:last-child').textContent.replace(/,/g, '')) || 0;
        }
        if (cashTable) {
            cashTotal = parseFloat(cashTable.querySelector('tfoot th:last-child').textContent.replace(/,/g, '')) || 0;
        }
        if (jvTable) {
            jvTotal = parseFloat(jvTable.querySelector('tfoot th:last-child').textContent.replace(/,/g, '')) || 0;
        }
        if (chqTable) {
            chqTotal = parseFloat(chqTable.querySelector('tfoot th:last-child').textContent.replace(/,/g, '')) || 0;
        }

        const grandTotal = bankTotal + cashTotal + jvTotal + chqTotal;
        printContent += `<div class="grand-total">Final Grand Total: ${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>`;
    } else {
        // Print specific table based on product type
        let table, title;
        switch (productType) {
            case 'Bank':
                table = document.getElementById('combined-data-table-bank');
                title = 'Bank Details';
                break;
            case 'Cash':
                table = document.getElementById('combined-data-table-cash');
                title = 'Cash Details';
                break;
            case 'JV':
                table = document.getElementById('combined-data-table-jv');
                title = 'JV Details';
                break;
            case 'Cheque':
                table = document.getElementById('combined-data-table-chq');
                title = 'Cheque Details';
                break;
        }

        if (table) {
            printContent += `<div class="section-title">${title}</div>`;
            printContent += table.outerHTML;
        }
    }

    printContent += `</body></html>`;

    // Replace body content with print content
    document.body.innerHTML = printContent;

    // Print and then restore original content
    setTimeout(function () {
        window.print();
        document.body.innerHTML = originalContents;
    }, 200);
}
</script>
@endsection