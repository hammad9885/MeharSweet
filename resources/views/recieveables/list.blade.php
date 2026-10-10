@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <style>
            /* Only make table header and footer slightly grey; remove zebra striping */
            .table thead th { background-color: #f2f2f2; }
            .table tfoot td { background-color: #f2f2f2; }
        </style>
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                
                <h4 class="page-title">Receivables</h4>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Show Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <form action="{{ route('recieveables.list') }}" method="GET" class="form-inline" id="search-form">
                        <div class="row">
                            <div class="form-group col-xl-3">
                                <label for="end_date" class="sr-only">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-xl-3">
                                <button type="submit" class="btn show-btn mb-2 mt-3">Show</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ledger Table -->
    @if(request('end_date'))
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print Table</button>
                    <div class="card border-0 shadow-none">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane show active" id="basic-datatable-preview">
                                    <h5>End Date: {{ request('end_date') }}</h5>
                                    <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>A/C Code</th>
                                                <th>Account Title</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ledger-tbody">
                                            @php 
                                                $totalDebit = 0; 
                                                $totalCredit = 0; 
                                            @endphp
                                            @foreach ($balances as $account)
                                                <tr>
                                                    <td>{{ $account->acc_codes->full_code ?? 'N/A' }}</td>
                                                    <td>{{ $account->title }}</td>
                                                    <td class="running-total">
                                                        @if ($account->balance >= 0)
                                                            {{ number_format($account->balance, 2) }} Dr
                                                            @php $totalDebit += $account->balance; @endphp
                                                        @else
                                                            {{ number_format(abs($account->balance), 2) }} Cr
                                                            @php $totalCredit += abs($account->balance); @endphp
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-end" colspan="2">Total:</th>
                                                <th>{{ number_format($totalDebit, 2) }} Dr</th>
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
    @endif
</div>

@php
    $company = auth()->user()->company;
@endphp

<script>
    const companyName = "{{ $company->name ?? '' }}";
    const companyAddress = `{{ str_replace("\n", ' ', $company->address ?? '') }}`;
    const companyPhone = "{{ $company->phone ?? '' }}";

    // Convert a yyyy-mm-dd or dd-mm-yyyy string into dd-mm-yyyy for display
    function formatDisplayDate(dateStr) {
        if (!dateStr) return 'N/A';
        dateStr = dateStr.trim();
        const parts = dateStr.split('-');
        const monthNames = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        if (parts.length === 3) {
            let day, month, year;
            // If format is yyyy-mm-dd
            if (parts[0].length === 4) {
                year = parts[0];
                month = parts[1];
                day = parts[2];
            } else {
                // assume dd-mm-yyyy
                day = parts[0];
                month = parts[1];
                year = parts[2];
            }

            const mIndex = parseInt(month, 10) - 1;
            const monthName = monthNames[mIndex] || month;
            return `${parseInt(day, 10)} ${monthName} ${year}`;
        }

        return dateStr;
    }

    function printTable() {
        const originalContents = document.body.innerHTML;

        // Prefer visible h5 End Date; fallback to input value
        const rawEnd = document.querySelector('.col-12 h5')?.innerText || document.getElementById('end_date')?.value || '';
        // rawEnd might be like "End Date: 2025-11-20" -> extract date portion
        const endMatch = rawEnd.match(/(\d{4}-\d{2}-\d{2})$/);
        const rawEndDate = endMatch ? endMatch[1] : rawEnd;
        const displayEnd = formatDisplayDate(rawEndDate);

        const tableContent = document.getElementById('basic-datatable').outerHTML;

        document.body.innerHTML = `
        <html>
            <head>
                <title>Print Receivables</title>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; text-align: left; padding:8px; }
                    th { background-color: #f2f2f2; }
                    h3, h4 { margin: 10px 0; }
                    .company-header { margin-bottom: 12px; display:flex; flex-direction:column; }
                    .company-name { font-weight: bold; font-size: 18px; }
                    .company-address { max-width: 300px; word-wrap: break-word; }
                    .print-meta { margin-top:6px; display:flex; gap:8px; align-items:center; }
                    .print-meta .label { font-weight:700; }
                </style>
            </head>
            <body>
                
                <h3 style="text-align:center;">Receivables</h3>
                <h5 style="text-align:center;"><span style="font-weight:bold;"></span> ${displayEnd}</h5>
                ${tableContent}
            </body>
        </html>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>
@endsection
