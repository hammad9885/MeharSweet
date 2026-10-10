@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <style>
            /* Only make table header and footer slightly grey; remove zebra striping */
            .table thead th { background-color: #f2f2f2; }
            .table tfoot td { background-color: #f2f2f2; }
        </style>
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Cash Book</h4>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row d-print-none">
            <div class="col-12">
                <form action="{{ route('cashbooks.list') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-xl-4">
                        <label for="account_title" class="form-label">Select Account</label>
                        <select name="account_title" id="account_title" class="form-control select2" data-toggle="select2">
                            <option value="">-- Choose Account --</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ isset($accountId) && $accountId == $account->id ? 'selected' : '' }}>
                                    {{ $account->title ?? ($account->account_title ?? ($account->name ?? 'Account ' . $account->id)) }} . {{ $account->full_code ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-3">
                        <label for="start_date" class="form-label">From Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}" required>
                    </div>

                    <div class="col-xl-3">
                        <label for="end_date" class="form-label">To Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}" required>
                    </div>

                    <div class="col-xl-2 d-flex">
                        <button type="submit" class="btn btn-success me-2">Show</button>
                        <a href="{{ route('cashbooks.list') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        @if (!empty($accountId))
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between mb-3">
                        <button type="button" class="btn btn-secondary d-print-none" onclick="printTable()">Print
                            Cashbook</button>
                    </div>

                    <!-- summary section -->
                    <div id="cashbook-table">
                        <div id="summary-section">
                        <div class="mb-4 summary-section" style="width: 300px; font-size:14px;">
                            <div class="mb-1 d-flex justify-content-between">
                                <strong>Opening Balance:</strong>
                                <span>{{ number_format(abs($openingBalance ?? 0), 2) }}
                                    {{ ($openingBalance ?? 0) < 0 ? 'Cr' : 'Dr' }}</span>
                            </div>
                            <div class="mb-1 d-flex justify-content-between">
                                <strong>Cash Received:</strong>
                                <span>{{ number_format($totalDebit ?? 0, 2) }}</span>
                            </div>
                            <div class="mb-1 d-flex justify-content-between">
                                <strong>Cash Paid:</strong>
                                <span>{{ number_format($totalCredit ?? 0, 2) }}</span>
                            </div>
                            <div class="mb-1 d-flex justify-content-between">
                                <strong>Closing Balance:</strong>
                                <span>{{ number_format($closingBalance ?? 0, 2) }}</span>
                            </div>
                        </div>
                        </div>
                        <!-- summary section -->

                        @php
                            $rows = collect($trndtls);
                            $receipts = $rows->filter(fn($r) => isset($r->debit) && $r->debit > 0);
                            $payments = $rows->filter(fn($r) => isset($r->credit) && $r->credit > 0);
                        @endphp

                        <!-- RECEIPTS -->
                        <h5 class="mt-4">PAYMENTS</h5>
                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Voucher #</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $r)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($r->v_date ?? ($r->date ?? now()))->format('d M Y') }}
                                        </td>
                                        <td>{{ $r->v_type . '-' . $r->v_no ?? '-' }}</td>
                                        <td>{{  $r->accounts->title.' - '.$r->description ?? ($r->narration ?? '-') }}</td>
                                        <td class="text-end">{{ number_format($r->debit ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No receipts found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- PAYMENTS -->
                        <h5 class="mt-4">RECEIPTS</h5>
                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Voucher #</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $p)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($p->v_date ?? ($p->date ?? now()))->format('d M Y') }}
                                        </td>
                                        <td>{{ $p->v_type . '-' . $p->v_no ?? '-' }}</td>
                                        <td>{{ $p->accounts->title.' - '.$p->description ?? ($p->narration ?? '-') }}</td>
                                        <td class="text-end">{{ number_format($p->credit ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No payments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- PRINT FUNCTION -->
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
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
                return dateStr;
            }
            return dateStr;
        }

        function printTable() {
            // Prefer visible h5 End Date; fallback to input value
            const displayStart =
                "{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y') : '' }}";
            const displayEnd =
                "{{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y') : '' }}";

            const tableWrapper = document.getElementById('cashbook-table');
            if (!tableWrapper) return window.print();

            // Grab the summary block and remove it from the main content to avoid duplication
            const summaryElem = document.getElementById('summary-section');
            const summaryHtml = summaryElem ? summaryElem.innerHTML : '';
            let tableContent = tableWrapper.innerHTML;
            if (summaryElem) {
                tableContent = tableContent.replace(summaryElem.outerHTML, '');
            }

            const style = `
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; text-align: left; padding:8px; }
            th { background-color: #f2f2f2; }
            body { font-family: Arial, Helvetica, sans-serif; color: #000; }
            .company-header { margin-bottom: 12px; }
            .company-name { font-weight: bold; font-size: 18px; }
            .company-address { max-width: 600px; word-wrap: break-word; }
            /* Summary layout: titles left, values right */
            .summary-section { width: 300px; font-size:14px; }
            .summary-section > div { display: flex; justify-content: space-between; align-items: center; }
            .summary-section > div > strong { display: block; text-align: left; flex: 1 1 auto; }
            .summary-section > div > span { display: block; text-align: right; min-width: 90px; }
            @media print { .d-print-none{ display: none !important; } }
        `;

            const headerHtml = `
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                    <div>
                        <h4 style="margin:0;">Cash Book</h4>
                        <h6 style="margin:0; font-weight:normal;"><span style="font-weight:bold;">Period:</span> ${displayStart} ${displayStart && displayEnd ? ' - ' : ''} ${displayEnd}</h6>
                    </div>
                    <div>${summaryHtml}</div>
                </div>
            `;

            const printWindow = window.open('', '_blank', 'width=900,height=700');
            printWindow.document.write(`<!doctype html><html><head><title>Cashbook Print</title><style>${style}</style></head><body>`);
            printWindow.document.write(`<div class="company-header"><div class="company-name">${companyName}</div><div class="company-address">${companyAddress}</div><div><b>Ph:</b> ${companyPhone}</div></div>`);

            // Render header with title on the left and summary on the right
            printWindow.document.write(headerHtml);

            // Then render the remaining table content (without the summary)
            printWindow.document.write(tableContent);

            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            // Give browser a moment to render
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>
@endsection
