@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <h4 class="page-title">Trial Balance </h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('trialbalance.list') }}" method="GET" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" 
                           class="form-control" 
                           name="start_date"
                           value="{{ $startDate ?? '' }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" 
                           class="form-control" 
                           name="end_date"
                           value="{{ $endDate ?? '' }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        Show
                    </button>
                     <button type="button" class="btn btn-success" onclick="printReport()">
                        Print
                    </button>
                </div>

                <div class="col-md-3 d-flex align-items-end justify-content-end">
                   
                </div>

            </form>
        </div>
    </div>

    @if(isset($trialBalance) && count($trialBalance) > 0)

    <div class="card shadow-sm">
        <div class="card-body">

            <div id="print-header" class="text-center mb-3 d-none">
                <h3>Trial Balance Report</h3>
                <h5>{{ $startDate }} to {{ $endDate }}</h5>
                <hr>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="trialBalanceTable">
                    <thead>
                        <tr>
                            <th rowspan="2" width="20%">A/c Code</th>
                            <th rowspan="2" width="20%">A/c Title</th>

                            <th colspan="2" class="text-center">Opening</th>
                            <th colspan="2" class="text-center">Transaction</th>
                            <th colspan="2" class="text-center">Closing</th>
                        </tr>
                        <tr>
                            <th>Debit</th>
                            <th>Credit</th>

                            <th>Debit</th>
                            <th>Credit</th>

                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                    </thead>

                    <tbody>

@php 
    $currentGroup = null;
    $groupTotals = [
        'opening_debit' => 0,
        'opening_credit' => 0,
        'trans_debit' => 0,
        'trans_credit' => 0,
        'closing_debit' => 0,
        'closing_credit' => 0,
    ];
    $grandTotals = [
        // 'opening_debit' => 0,
        // 'opening_credit' => 0,
        // 'trans_debit' => 0,
        // 'trans_credit' => 0,
        'closing_debit' => 0,
        'closing_credit' => 0,
    ];
@endphp

@foreach($trialBalance as $acc)

    {{-- Detect Group --}}
    @php 
        $groupCode = substr($acc['account_code'], 0, 2); 
        $groupName = [
            '01' => 'Capital',
            '02' => 'Liabilities',
            '03' => 'Assets',
            '04' => 'Revenue',
            '05' => 'Expenses'
        ][$groupCode] ?? 'Other';
    @endphp

    {{-- If group changed, print previous totals --}}
    @if($currentGroup !== null && $currentGroup !== $groupName)
        <tr class="fw-bold">
            <td colspan="2" class="text-end">Total {{ $currentGroup }}</td>

            <td class="text-end">{{ number_format($groupTotals['opening_debit'], 2) }}</td>
            <td class="text-end">{{ number_format($groupTotals['opening_credit'], 2) }}</td>

            <td class="text-end">{{ number_format($groupTotals['trans_debit'], 2) }}</td>
            <td class="text-end">{{ number_format($groupTotals['trans_credit'], 2) }}</td>

            <td class="text-end">{{ number_format($groupTotals['closing_debit'], 2) }}</td>
            <td class="text-end">{{ number_format($groupTotals['closing_credit'], 2) }}</td>
        </tr>

        {{-- Reset totals --}}
        @php 
            $groupTotals = [
                'opening_debit' => 0,
                'opening_credit' => 0,
                'trans_debit' => 0,
                'trans_credit' => 0,
                'closing_debit' => 0,
                'closing_credit' => 0,
            ];
        @endphp
    @endif

    {{-- New group header --}}
    @if($currentGroup !== $groupName)
       
        @php $currentGroup = $groupName; @endphp
    @endif

    {{-- Account Row --}}
    <tr>
        <td>{{ $acc['account_code'] }}</td>
        <td>{{ $acc['account_title'] }}</td>

        <td class="text-end">{{ $acc['opening_debit'] ? number_format($acc['opening_debit'], 2) : '' }}</td>
        <td class="text-end">{{ $acc['opening_credit'] ? number_format($acc['opening_credit'], 2) : '' }}</td>

        <td class="text-end">{{ $acc['trans_debit'] ? number_format($acc['trans_debit'], 2) : '' }}</td>
        <td class="text-end">{{ $acc['trans_credit'] ? number_format($acc['trans_credit'], 2) : '' }}</td>

        <td class="text-end">{{ $acc['closing_debit'] ? number_format($acc['closing_debit'], 2) : '' }}</td>
        <td class="text-end">{{ $acc['closing_credit'] ? number_format($acc['closing_credit'], 2) : '' }}</td>
    </tr>

    {{-- Accumulate totals --}}
    @php
        $groupTotals['opening_debit'] += $acc['opening_debit'];
        $groupTotals['opening_credit'] += $acc['opening_credit'];
        $groupTotals['trans_debit'] += $acc['trans_debit'];
        $groupTotals['trans_credit'] += $acc['trans_credit'];
        $groupTotals['closing_debit'] += $acc['closing_debit'];
        $groupTotals['closing_credit'] += $acc['closing_credit'];
        
        // $grandTotals['opening_debit'] += $acc['opening_debit'];
        // $grandTotals['opening_credit'] += $acc['opening_credit'];
        // $grandTotals['trans_debit'] += $acc['trans_debit'];
        // $grandTotals['trans_credit'] += $acc['trans_credit'];
        $grandTotals['closing_debit'] += $acc['closing_debit'];
        $grandTotals['closing_credit'] += $acc['closing_credit'];
    @endphp

@endforeach

{{-- Print last group total --}}
<tr class="fw-bold">
    <td colspan="2" class="text-end">Total {{ $currentGroup }}</td>

    <td class="text-end">{{ number_format($groupTotals['opening_debit'], 2) }}</td>
    <td class="text-end">{{ number_format($groupTotals['opening_credit'], 2) }}</td>

    <td class="text-end">{{ number_format($groupTotals['trans_debit'], 2) }}</td>
    <td class="text-end">{{ number_format($groupTotals['trans_credit'], 2) }}</td>

    <td class="text-end">{{ number_format($groupTotals['closing_debit'], 2) }}</td>
    <td class="text-end">{{ number_format($groupTotals['closing_credit'], 2) }}</td>
</tr>

{{-- Grand Totals --}}
<tr class="fw-bold bg-light">
    <td colspan="6" class="text-end">GRAND CLOSING TOTAL</td>

    {{-- <td class="text-end">{{ number_format($grandTotals['opening_debit'], 2) }}</td>
    <td class="text-end">{{ number_format($grandTotals['opening_credit'], 2) }}</td>

    <td class="text-end">{{ number_format($grandTotals['trans_debit'], 2) }}</td>
    <td class="text-end">{{ number_format($grandTotals['trans_credit'], 2) }}</td> --}}

    <td class="text-end">{{ number_format($grandTotals['closing_debit'], 2) }}</td>
    <td class="text-end">{{ number_format($grandTotals['closing_credit'], 2) }}</td>
</tr>

</tbody>
</table>
</div>

</div>
</div>

@else
<div class="alert alert-info mt-3 text-center">
    No data found for selected date range.
</div>
@endif

</div>

<script>
function printReport() {
    const table = document.getElementById('trialBalanceTable').outerHTML;
    const header = document.getElementById('print-header').outerHTML;
 // Get company details (you'll need to pass these from your controller)
   
    const companyAddress = `{!! addslashes(str_replace(["\r", "\n"], ['', '<br>'], Auth::user()->company->address ?? 'N/A')) !!}`;

    const companyPhone = "{{ Auth::user()->company->phone ?? 'N/A' }}";
    const companyEmail = "{{ Auth::user()->company->email ?? 'N/A' }}";
            const companyName = "{{ Auth::user()->company->name ?? 'N/A' }}";
    const companyLogo = "{{ Auth::user()->company?->logo ? asset('storage/' . Auth::user()->company?->logo) : '' }}";

    let win = window.open('', '_blank');
    win.document.write(`
        <html>
            <head>
                <title>Trial Balance Report</title>
               <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        font-size: 14px; 
                        margin: 20px; 
                        color: #333; 
                    }
                    .print-header { 
                        margin-bottom: 20px; 
                        border-bottom: 2px solid #333; 
                        padding-bottom: 15px; 
                    }
                    .company-logo { 
                        max-width: 150px; 
                        max-height: 80px; 
                    }
                    .company-name { 
                        font-size: 24px; 
                        font-weight: bold; 
                        margin-bottom: 5px; 
                    }
                    .company-details { 
                        font-size: 12px; 
                        margin-top: 10px; 
                    }
                    .print-title { 
                        text-align: center; 
                        font-size: 20px; 
                        font-weight: bold; 
                        margin: 20px 0; 
                        text-transform: uppercase; 
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px 12px;
                        font-size: 12px;
                        text-align: center;
                    }
                    th {
                        background-color: #f2f2f2;
                        text-align: left;
                        font-weight: bold;
                         text-align: center;
                    }
                    .no-print {
                        display: none;
                    }
                    .header-table {
                        width: 100%;
                        margin-bottom: 20px;
                    }
                    .header-table td {
                        border: none;
                        vertical-align: top;
                    }
                    .watermark {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        opacity: 0.06;
                        z-index: 0;
                        max-width: 60%;
                        max-height: 60%;
                        pointer-events: none;
                        filter: grayscale(100%);
                    }
                    @media print {
                        .watermark { opacity: 0.08; }
                        body { margin: 15px; }
                    }
                </style>
            </head>
            <body>
              ${companyLogo ? `<img class="watermark" src="${companyLogo}" alt="Watermark">` : ''}
                
                <div class="print-header">
                    <table class="header-table">
                        <tr>
                            
                            <td style="width: 80%; padding-left: 20px; text-align: left !important;">
                                <div class="company-name">${companyName}</div>
                                <div class="company-details">
                                    <strong>Address:</strong> ${companyAddress}<br>
                                    <strong>Ph:</strong> ${companyPhone}<br>
                                    <strong>Email:</strong> ${companyEmail}<br><br>
                                    <strong style="font-size: 18px;">Trail Balance</strong><br>
                                    <strong>Print Date:</strong> ${new Date().toLocaleDateString()}
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                  
                ${table}
            </body>
        </html>
    `);
    win.document.close();
    win.print();
}
</script>

@endsection
