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
                        <li class="breadcrumb-item active">Daily Statement</li>
                    </ol>
                </div>
                <h4 class="page-title">Daily Statement</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Show Form -->
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="tab-content">
                    <div class="col-12">
                        <form action="{{ route('daily_statement.reports') }}" method="GET" class="form-inline col-xl-12"
                            id="search-form">
                            <div class="row">
                              
                                <div class="form-group col-xl-3">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $endDate}}">
                                </div>

                                <div class="col-xl-3">
                                    <button type="submit" class="btn btn-primary mb-2 mt-3">Show</button>
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
        <div class="card mt-2">
            <div class="card-body">
                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print Table</button>
                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTablesec()">1st Page</button>
                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTableone()">2nd Page</button>
                <div class="card mt-2">
                    <div class="card-body">
                         
                        <div class="tab-content">
                            <div id="ledger">

                                <div style="overflow-x: auto; width: 100%;">
                                    @php
                                    // Filter and sort accounts alphabetically (same as before)
                                    $filteredLevel7Accounts = $level7Accounts->filter(function($account) use
                                    ($level7Transactions) {
                                    $transaction = $level7Transactions->firstWhere('aid', $account->id);
                                    return $transaction && $transaction->balance != 0;
                                    })->sortBy('title');

                                    $filteredLevel4Accounts = $level4Accounts->filter(function($account) use
                                    ($level4Transactions) {
                                    $transaction = $level4Transactions->firstWhere('aid', $account->id);
                                    return $transaction && $transaction->balance != 0;
                                    })->sortBy('title');

                                    $filteredLevel5Accounts = $level5Accounts->filter(function($account) use
                                    ($level5Transactions) {
                                    $transaction = $level5Transactions->firstWhere('aid', $account->id);
                                    return $transaction && $transaction->balance != 0;
                                    })->sortBy('title');

                                    $filteredLevel6Accounts = $level6Accounts->filter(function($account) use
                                    ($level6Transactions) {
                                    $transaction = $level6Transactions->firstWhere('aid', $account->id);
                                    return $transaction && $transaction->balance != 0;
                                    })->sortBy('title');

                                    $filteredLevel14Accounts = $level14Accounts->filter(function($account) use
                                    ($level14Transactions) {
                                    $transaction = $level14Transactions->firstWhere('aid', $account->id);
                                    return $transaction && $transaction->balance != 0;
                                    })->sortBy('title');

                                    $filteredPendingCheques = $pendingCheques->filter(function($cheque) {
                                    return $cheque->chq_amt != 0;
                                    })->sortBy(function($cheque) {
                                    $account = \App\Models\AccountMaster::find($cheque->aid);
                                    return $account ? $account->title : '';
                                    });

                                    // Find the max count for each section (same as before)
                                    $maxCountSection1 = max(
                                    ceil($filteredLevel7Accounts->count() / 2),
                                    ceil($filteredLevel4Accounts->count() / 2)
                                    );

                                    $maxCountSection2 = max(
                                    ceil($filteredLevel5Accounts->count() / 2),
                                    $filteredLevel6Accounts->count(),
                                    $filteredPendingCheques->count(),
                                    $filteredLevel14Accounts->count()
                                    );
                                    @endphp

                                    <style>
                                        .small-font-table {
                                            font-size: 14px;
                                        }

                                        .small-font-table th,
                                        .small-font-table td {
                                            padding: 4px 8px;
                                        }

                                        .table-section {
                                            margin-bottom: 30px;
                                        }

                                        .section-title {
                                            font-weight: bold;
                                            margin-bottom: 10px;
                                            font-size: 14px;
                                        }
                                    </style>

                                    <!-- Section 1: Customers and Suppliers -->
                                    <div class="table-section no-printone">
                                        <div class="section-title" style="font-size: 20px;">Daily Statement</div>
                                        <div>
    <h5>
        End Date: <span>{{ request()->get('end_date') ?? date('d-m-Y') }}</span>
    </h5>
</div>
                                        <table id="customers-suppliers-table"
                                            class="table dt-responsive nowrap w-100 small-font-table clean-table">
                                            <thead>
                                                <tr>
                                                    <th class="table-primary" style="display: none;">Date</th>
                                                    <th class="table-primary">Customer</th>
                                                    <th class="table-primary">Balance</th>
                                                    <th class="table-primary" style="display: none;">Date</th>
                                                    <th class="table-primary">Customer</th>
                                                    <th class="table-primary">Balance</th>
                                                    <th class="table-warning" style="display: none;">Date</th>
                                                    <th class="table-warning">Supplier</th>
                                                    <th class="table-warning">Balance</th>
                                                    <th class="table-warning" style="display: none;">Date</th>
                                                    <th class="table-warning">Supplier</th>
                                                    <th class="table-warning">Balance</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < $maxCountSection1; $i++) <tr>
                                                    <!-- Customer Column 1 -->
                                                    <td class="table-primary" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel7Accounts->values()->get($i*2);
                                                        $transaction = $account ? $level7Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        if ($transaction && property_exists($transaction,
                                                        'last_transaction_date') && $transaction->last_transaction_date)
                                                        {
                                                        echo
                                                        \Carbon\Carbon::parse($transaction->last_transaction_date)->format('Y-m-d');
                                                        } else {
                                                        echo '';
                                                        }
                                                        @endphp
                                                    </td>
                                                    <td class="table-primary">{{
                                                        $filteredLevel7Accounts->values()->get($i*2)->title ?? '' }}
                                                    </td>
                                                    <td class="table-primary">
                                                        @php
                                                        $account = $filteredLevel7Accounts->values()->get($i*2);
                                                        $transaction = $account ? $level7Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>


                                                    <!-- Customer Column 2 -->
                                                    <td class="table-primary" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel7Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level7Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        if ($transaction && property_exists($transaction,
                                                        'last_transaction_date') && $transaction->last_transaction_date)
                                                        {
                                                        echo
                                                        \Carbon\Carbon::parse($transaction->last_transaction_date)->format('Y-m-d');
                                                        } else {
                                                        echo '';
                                                        }
                                                        @endphp
                                                    </td>
                                                    <td class="table-primary">{{
                                                        $filteredLevel7Accounts->values()->get($i*2+1)->title ?? '' }}
                                                    </td>
                                                    <td class="table-primary">
                                                        @php
                                                        $account = $filteredLevel7Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level7Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>


                                                    <!-- Supplier Column 1 -->
                                                    <td class="table-warning" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel4Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level4Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        if ($transaction && property_exists($transaction,
                                                        'last_transaction_date') && $transaction->last_transaction_date)
                                                        {
                                                        echo
                                                        \Carbon\Carbon::parse($transaction->last_transaction_date)->format('Y-m-d');
                                                        } else {
                                                        echo '';
                                                        }
                                                        @endphp
                                                    </td>
                                                    <td class="table-warning">
                                                        {{ $filteredLevel4Accounts->values()->get($i*2)->title ?? '' }}
                                                    </td>
                                                    <td class="table-warning">
                                                        @php
                                                        $account = $filteredLevel4Accounts->values()->get($i*2);
                                                        $transaction = $account ? $level4Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>



                                                    <!-- Supplier Column 2 -->
                                                    <td class="table-warning" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel4Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level4Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        if ($transaction && property_exists($transaction,
                                                        'last_transaction_date') && $transaction->last_transaction_date)
                                                        {
                                                        echo
                                                        \Carbon\Carbon::parse($transaction->last_transaction_date)->format('Y-m-d');
                                                        } else {
                                                        echo '';
                                                        }
                                                        @endphp
                                                    </td>
                                                    <td class="table-warning">
                                                        {{ $filteredLevel4Accounts->values()->get($i*2+1)->title ?? ''
                                                        }}
                                                    </td>
                                                    <td class="table-warning">
                                                        @php
                                                        $account = $filteredLevel4Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level4Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>

                                                    </tr>
                                                    @endfor

                                                    <!-- Grand Total Row -->
                                                    <tr>
                                                        <td class="table-primary" colspan="2"><strong>G.Total:</strong>
                                                        </td>
                                                        <td class="table-primary" colspan="2">{{
                                                            number_format($level7Transactions->sum('balance'), 2) }}
                                                        </td>
                                                        <td class="table-warning" colspan="2"><strong>G.Total:</strong>
                                                        </td>
                                                        <td class="table-warning" colspan="2">
                                                            {{ number_format($level4Transactions->sum('balance'), 2) }}
                                                        </td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Section 2: Banks, Cash, Pending Checks, Current Assets -->
                                    <div class="table-section no-printsec">
                                        <table id="bank-cash-table"
                                            class="table dt-responsive nowrap w-100 small-font-table clean-table">
                                            <thead>
                                                <tr>
                                                    <th class="table-danger" style="display: none;">Date</th>
                                                    <th class="table-danger">Bank</th>
                                                    <th class="table-danger">Balance</th>

                                                    <th class="table-danger" style="display: none;">Date</th>
                                                    <th class="table-danger">Bank</th>
                                                    <th class="table-danger">Balance</th>

                                                    <th class="table-info" style="display: none;">Date</th>
                                                    <th class="table-info">Cash</th>
                                                    <th class="table-info">Balance</th>

                                                    <th class="table-secondary" style="display: none;">Date</th>
                                                    <th class="table-secondary">P.Check</th>
                                                    <th class="table-secondary">Balance</th>

                                                    <th class="table-success" style="display: none;">Date</th>
                                                    <th class="table-success">Current Assets</th>
                                                    <th class="table-success">Balance</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < $maxCountSection2; $i++) <tr>
                                                    <!-- Bank Column 1 -->
                                                    <td class="table-danger" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel5Accounts->values()->get($i*2);
                                                        $transaction = $account ? $level5Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction && $transaction->last_transaction_date
                                                        ?
                                                        \Carbon\Carbon::parse($transaction->last_transaction_date)->format('Y-m-d')
                                                        : '';
                                                        @endphp
                                                    </td>
                                                    <td class="table-danger">{{
                                                        $filteredLevel5Accounts->values()->get($i*2)->title ?? '' }}
                                                    </td>
                                                    <td class="table-danger">
                                                        @php
                                                        $account = $filteredLevel5Accounts->values()->get($i*2);
                                                        $transaction = $account ? $level5Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>


                                                    <!-- Bank Column 2 -->
                                                    <td class="table-danger" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel5Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level5Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction && $transaction->last_transaction_date
                                                        ?
                                                        \Carbon\Carbon::parse($transaction->last_transaction_date)->format('Y-m-d')
                                                        : '';
                                                        @endphp
                                                    </td>
                                                    <td class="table-danger">{{
                                                        $filteredLevel5Accounts->values()->get($i*2+1)->title ?? '' }}
                                                    </td>
                                                    <td class="table-danger">
                                                        @php
                                                        $account = $filteredLevel5Accounts->values()->get($i*2+1);
                                                        $transaction = $account ? $level5Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>


                                                    <!-- Cash -->
                                                    <td class="table-info" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel6Accounts->values()->get($i);
                                                        $transaction = $account ? $level6Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? ($transaction->last_transaction_date ?? '')
                                                        : '';
                                                        @endphp
                                                    </td>
                                                    <td class="table-info">
                                                        {{ $filteredLevel6Accounts->values()->get($i)->title ?? '' }}
                                                    </td>
                                                    <td class="table-info">
                                                        @php
                                                        $account = $filteredLevel6Accounts->values()->get($i);
                                                        $transaction = $account ? $level6Transactions->firstWhere('aid',
                                                        $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>


                                                    <!-- Pending Check -->
                                                    <td class="table-secondary" style="display: none;">
                                                        @php
                                                        $cheque = $filteredPendingCheques->values()->get($i);
                                                        echo $cheque ? $cheque->last_transaction_date : '';
                                                        @endphp
                                                    </td>
                                                    <td class="table-secondary">
                                                        @php
                                                        $cheque = $filteredPendingCheques->values()->get($i);
                                                        $account = $cheque ?
                                                        \App\Models\AccountMaster::find($cheque->aid) : null;
                                                        echo $account->title ?? '';
                                                        @endphp
                                                    </td>
                                                    <td class="table-secondary">
                                                        @php
                                                        $cheque = $filteredPendingCheques->values()->get($i);
                                                        echo $cheque ? number_format($cheque->chq_amt, 2) : '';
                                                        @endphp
                                                    </td>



                                                    <!-- Current Assets -->
                                                    <td class="table-success" style="display: none;">
                                                        @php
                                                        $account = $filteredLevel14Accounts->values()->get($i);
                                                        $transaction = $account ?
                                                        $level14Transactions->firstWhere('aid', $account->id) : null;
                                                        echo $transaction ? ($transaction->last_transaction_date ??
                                                        'N/A') : '';
                                                        @endphp
                                                    </td>
                                                    <td class="table-success">{{
                                                        $filteredLevel14Accounts->values()->get($i)->title ?? '' }}</td>
                                                    <td class="table-success">
                                                        @php
                                                        $account = $filteredLevel14Accounts->values()->get($i);
                                                        $transaction = $account ?
                                                        $level14Transactions->firstWhere('aid', $account->id) : null;
                                                        echo $transaction ? number_format($transaction->balance, 2) :
                                                        '';
                                                        @endphp
                                                    </td>

                                                    </tr>
                                                    @endfor

                                                    <!-- Grand Total Row -->
                                                    <tr>
                                                        <td class="table-danger" colspan="2"><strong>G.Total:</strong>
                                                        </td>
                                                        <td class="table-danger" colspan="2">
                                                            {{ number_format($level5Transactions->sum('balance'), 2) }}
                                                        </td>
                                                        <td class="table-info" ><strong>G.Total:</strong>
                                                        </td>
                                                        <td class="table-info">
                                                            {{ number_format($level6Transactions->sum('balance'), 2) }}
                                                        </td>
                                                        <td class="table-secondary" >
                                                            <strong>G.Total:</strong></td>
                                                        <td class="table-secondary">
                                                            {{ number_format($pendingCheques->sum('chq_amt'), 2) }}
                                                        </td>
                                                        <td class="table-success"><strong>G.Total:</strong>
                                                        </td>
                                                        <td class="table-success">
                                                            {{ number_format($level14Transactions->sum('balance'), 2) }}
                                                        </td>
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
    </div>
</div>
<script>
  const today = new Date();
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    const endInput = document.getElementById('end_date');



    if (endInput && !endInput.value) {
        endInput.valueAsDate = today;
    }

    function printTable() {
        const elementsToHide = document.querySelectorAll('.no-print');
    elementsToHide.forEach(el => el.style.display = 'none');
        const printContents = document.getElementById('ledger').outerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                         @page {
                        size: A4 landscape;
                    }
                         body {
                        font-family: Arial, sans-serif;
                        font-size: 12px; /* Small font size */
                    }
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
                            .no-print{
                        background-color: #f2f2f2;
                    }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // Reload to restore the original page content
    }
    
    function printTableone() {
        const elementsToHide = document.querySelectorAll('.no-printone');
    elementsToHide.forEach(el => el.style.display = 'none');
        const printContents = document.getElementById('ledger').outerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                         @page {
                        size: A4 landscape;
                    }
                         body {
                        font-family: Arial, sans-serif;
                        font-size: 12px; /* Small font size */
                    }
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
                            .no-print{
                        background-color: #f2f2f2;
                    }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // Reload to restore the original page content
    }
    
    function printTablesec() {
        const elementsToHide = document.querySelectorAll('.no-printsec');
    elementsToHide.forEach(el => el.style.display = 'none');
        const printContents = document.getElementById('ledger').outerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                         @page {
                        size: A4 landscape;
                    }
                         body {
                        font-family: Arial, sans-serif;
                        font-size: 12px; /* Small font size */
                    }
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
                            .no-print{
                        background-color: #f2f2f2;
                    }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // Reload to restore the original page content
    }
</script>
@endsection