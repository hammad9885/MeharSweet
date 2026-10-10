@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Trial Balance</h4>
                </div>
            </div>
        </div>
    @php
    $totalDebit = 0;
    $totalCredit = 0;
    @endphp
        <!-- Filter -->
        <div class="row d-print-none"> <!-- 👈 hides filter in print -->
            <div class="col-12">
                <div class="card mt-2">
                    <div class="card-body">
                        <form action="{{ route('trialbalance.summarized') }}" method="GET" class="form-inline">
                            <div class="row align-items-end">
    <div class="form-group col-xl-3">
        <label for="end_date" class="sr-only">End Date</label>
        <input type="date" class="form-control" id="end_date" name="end_date"
            value="{{ request('end_date') }}">
    </div>
    <div class="form-group col-xl-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">Show</button>
        <button onclick="printReport()" class="btn btn-success">Print</button>
    </div>
</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trial Balance Table -->
        @if ($balances->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card mt-2">
                        <div class="card-body">

                            <!-- Print Button -->


                            <!-- Header shown only in print -->
                            <div id="print-header" class="text-center mb-4 d-none d-print-block">
                                <h3><strong>Trial Balance</strong></h3>
                                <p><em>As of {{ request('end_date') ?? date('Y-m-d') }}</em></p>
                                <p><em>Printed on: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</em></p>
                            </div>

                            <h5 class="d-print-none">Trial Balance</h5>
                            <div id="print-area">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Title</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($balances as $item)
             @php
                            $totalDebit += $item->total_debit - $item->total_credit > 0 ? $item->total_debit - $item->total_credit : 0;
                            $totalCredit += $item->total_debit - $item->total_credit < 0 ? abs($item->total_debit - $item->total_credit) : 0;
             @endphp
                <tr>
                    <td>{{ $item->account_code }}</td>
                    <td>{{ $item->title }}</td>

                    <td class="text-end">
                       
                        {{ $item->total_debit - $item->total_credit > 0 ? number_format($item->total_debit - $item->total_credit, 2) : '' }}
                    </td>

                    <td class="text-end">
                        {{ $item->total_debit - $item->total_credit < 0 ? number_format(abs($item->total_debit - $item->total_credit), 2) : '' }}
                    </td>
                </tr>
            @endforeach

            <tr class="fw-bold">
                <td colspan="2" class="text-end">Total</td>
                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div> <!-- NOW it's correct -->


                        </div>
                    </div>
                </div>
            </div>
        @else
            <p class="text-muted">No data found for the selected filters for Trial Balance.</p>
        @endif

    </div>

    <!-- JS for Print -->

    <script>
        function printReport() {
            const table = document.getElementById('print-area').outerHTML;
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
                        font-size: 14px;
                    }
                    th {
                        background-color: #f2f2f2;
                        text-align: left;
                        font-weight: bold;
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
                            
                            <td style="width: 80%; padding-left: 20px;">
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
