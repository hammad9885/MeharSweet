@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h4 class="page-title">Ledger</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="col-12">
                            <form action="{{ route('ledger.list') }}" method="GET" class="form-inline col-xl-12"
                                id="search-form">
                                <div class="row">
                                    <div class="form-group col-xl-3">
                                        <label for="start_date" class="sr-only">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request()->get('start_date') }}">
                                    </div>
                                    <div class="form-group col-xl-3">
                                        <label for="end_date" class="sr-only">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request()->get('end_date') }}">
                                    </div>
                                    <div class="form-group col-xl-3">
                                        <label for="account_title" class="sr-only">Account Title</label>
                                        <select name="account_title" id="account_title" class="form-control select2"
                                            data-toggle="select2">
                                            <option value="">Select Account</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->acc_id }}"
                                                    {{ isset($accountId) && $accountId == $customer->acc_id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->acc_id }}"
                                                    {{ isset($accountId) && $accountId == $supplier->acc_id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
@foreach ($contractors as $contractor)
    @php
        $cAccId = $contractor->acc_id ?? $contractor->id;
        $cName = $contractor->name ?? $contractor->title;
    @endphp
    <option value="{{ $cAccId }}" {{ isset($accountId) && $accountId == $cAccId ? 'selected' : '' }}>
        {{ $cName }}
    </option>
@endforeach
@foreach ($accounts as $account)
    <option value="{{ $account->id }}" {{ isset($accountId) && $accountId == $account->id ? 'selected' : '' }}>
        {{ $account->title }}
    </option>
@endforeach
                                        </select>
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
        </div>
        <div class="row">
            <div class="card border-0 shadow-none">
                <div class="card-body">

                    <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print</button>
                    <div class="tab-content">
                        <div id="ledger">
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                   <h4 style="display: inline-block; margin-left: 20px;">
                                    <strong>Name:</strong>
                                    <span id="display-party-name">
@php
            $selectedAccId = request()->get('account_title');
            $displayName = 'Select Account';
            if ($selectedAccId) {
                $selectedCustomer = $customers->firstWhere('acc_id', $selectedAccId);
                $selectedSupplier = $suppliers->firstWhere('acc_id', $selectedAccId);
                $selectedContractor = $contractors->firstWhere('acc_id', $selectedAccId) 
                    ?: $contractors->firstWhere('id', $selectedAccId);
                $selectedGeneralAcc = $accounts->firstWhere('id', $selectedAccId);

                if ($selectedCustomer) {
                    $displayName = $selectedCustomer->name;
                } elseif ($selectedSupplier) {
                    $displayName = $selectedSupplier->name;
                } elseif ($selectedContractor) {
                    $displayName = $selectedContractor->name ?? $selectedContractor->title;
                } elseif ($selectedGeneralAcc) {
                    $displayName = $selectedGeneralAcc->title;
                } else {
                    $fallbackAcc = \App\Models\AccountMaster::find($selectedAccId);
                    if ($fallbackAcc) {
                        $displayName = $fallbackAcc->title;
                    } else {
                        $displayName = 'N/A';
                    }
                }
            }
            @endphp
            {{ $displayName }}
                                        </span>
                                    </h4>
                                </div>
                            </div>
                            <table id="basic-datatable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Voucher</th>
                                        <th>Description</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Running Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = $openingBalance;
                                        $viewTotalDebit = 0;
                                        $viewTotalCredit = 0;
                                    @endphp
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Opening Balance</strong></td>
                                        <td>
                                            @if ($balance < 0)
                                                {{ number_format(abs($balance), 2) }} Dr
                                            @elseif($balance > 0)
                                                {{ number_format($balance, 2) }} Cr
                                            @else
                                                {{ number_format(0, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach ($trndtls as $trn)
                                        @php
                                            $rowDebit = $trn->debit;
                                            $rowCredit = $trn->credit;
                                            
                                            // Display logic per voucher type (user mapping)
                                            $displayDebit = 0;
                                            $displayCredit = 0;
                                            
                                            switch ($trn->v_type) {
                                                case 'PIN':  // Purchase = Debit
                                                    $displayDebit = $rowDebit ?: $rowCredit;
                                                    break;
                                                case 'SIN':  // Sale = Credit
                                                    $displayCredit = $rowCredit ?: $rowDebit;
                                                    break;
                                                case 'BPV':  // BPV = Credit
                                                    $displayCredit = $rowCredit ?: $rowDebit;
                                                    break;
                                                case 'CPV':  // CPV = Credit
                                                    $displayCredit = $rowCredit ?: $rowDebit;
                                                    break;
                                                case 'BRV':  // BRV = Debit
                                                    $displayDebit = $rowDebit ?: $rowCredit;
                                                    break;
                                                case 'CRV':  // CRV = Debit
                                                    $displayDebit = $rowDebit ?: $rowCredit;
                                                    break;
                                                case 'CLP':  // Contractor Lot Posting = Debit
                                                case 'PLP':  // Packing Lot Posting = Debit
                                                case 'CON':  // Consumption = Debit
                                                    $displayDebit = $rowDebit;
                                                    $displayCredit = 0;
                                                    break;
                                                case 'OPN':  // Opening Balance = as stored
                                                    $displayDebit = $rowDebit;
                                                    $displayCredit = $rowCredit;
                                                    break;
                                                default:    // Other types = as stored
                                                    $displayDebit = $rowDebit;
                                                    $displayCredit = $rowCredit;
                                                    break;
                                            }
                                            
                                            $balance += $displayCredit - $displayDebit;
                                            $viewTotalDebit += $displayDebit;
                                            $viewTotalCredit += $displayCredit;
                                        @endphp
                                        <tr>
                                            <td>{{ $trn->v_date ? date('d-m-Y', strtotime(datetime: $trn->v_date)) : '' }}</td>
                                            <td>
                                                @php
                                                    $routeId = $trn->r_id ?? $trn->v_no;
                                                    $displayVoucherNo = $trn->v_no ?? $trn->r_id;
                                                    $printRoute = null;

                                                    if ($trn->v_type == 'PIN') {
                                                        $printRoute = route('purchase_invoices.print', $routeId);
                                                    } elseif ($trn->v_type == 'SIN') {
                                                        $printRoute = route('sale_invoices.print', $routeId);
                                                    } elseif ($trn->v_type == 'SRN') {
                                                        $printRoute = route('sale_return_invoices.print', $routeId);
                                                    } elseif ($trn->v_type == 'PRN') {
                                                        $printRoute = route('purchase_return_invoices.print', $routeId);
                                                    } elseif ($trn->v_type == 'CLP') {
                                                        $printRoute = route('contractor-lot-posting.edit', $routeId);
                                                    } elseif ($trn->v_type == 'PLP') {
                                                        $printRoute = route('packing-lot-posting.edit', $routeId);
                                                    } elseif ($trn->v_type == 'CON') {
                                                        $printRoute = route('consumption.edit', $routeId);
                                                    } else {
                                                        $printRoute = route('printInvoice', [$routeId, $trn->v_type]);
                                                    }
                                                @endphp

                                                @if($printRoute)
                                                    <a href="{{ $printRoute }}" target="blank">{{ $trn->v_type . '-' . $displayVoucherNo }}</a>
                                                @else
                                                    {{ $trn->v_type . '-' . $displayVoucherNo }}
                                                @endif
                                            </td>
                                            <td>{{ $trn->descr?? 'N/A'}}</td>

                                            <td>{{ number_format($displayDebit, 2) }}</td>
                                            <td>{{ number_format($displayCredit, 2) }}</td>
                                            <td>
                                                @if ($balance < 0)
                                                    {{ number_format(abs($balance), 2) }} Dr
                                                @elseif($balance > 0)
                                                    {{ number_format($balance, 2) }} Cr
                                                @else
                                                    {{ number_format(0, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <th colspan="3" class="text-end">Totals</th>
                                        <th>{{ number_format($viewTotalDebit, 2) }}</th>
                                        <th>{{ number_format($viewTotalCredit, 2) }}</th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Closing Balance</strong></td>
                                        <td>
                                            @if ($balance < 0)
                                                {{ number_format(abs($balance), 2) }} Dr
                                            @elseif($balance > 0)
                                                {{ number_format($balance, 2) }} Cr
                                            @else
                                                {{ number_format(0, 2) }}
                                            @endif
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

    @php
        $company = auth()->user()->company;
    @endphp
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

    <script>
        const companyName = "{{ $company->name ?? '' }}";
        const companyAddress = `{{ str_replace("\n", ' ', $company->address ?? '') }}`;
        const companyPhone = "{{ $company->phone ?? '' }}";

        const today = new Date();
        const endDate = formatDate(today);
        const startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));

        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');

        if (!startInput.value) startInput.value = startDate;
        if (!endInput.value) endInput.value = endDate;

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
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
            const ledgerDiv = document.getElementById('ledger');
            const originalContents = document.body.innerHTML;
            const rawDisplayStart = document.getElementById('display-start-date')?.innerText || document.getElementById('start_date')?.value || '';
            const rawDisplayEnd = document.getElementById('display-end-date')?.innerText || document.getElementById('end_date')?.value || '';
            const displayStart = formatDisplayDate(rawDisplayStart);
            const displayEnd = formatDisplayDate(rawDisplayEnd);
            const partyName = document.getElementById('display-party-name')?.innerText || 'N/A';
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = ledgerDiv.innerHTML;
            const nameElem = tempDiv.querySelector('#display-party-name');
            if (nameElem) {
                const wrapper = nameElem.closest('h4,div');
                if (wrapper) wrapper.remove();
                else nameElem.remove();
            }
            const printContents = tempDiv.innerHTML;

            document.body.innerHTML = `
        <html>
            <head>
                <title>Print Ledger</title>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; text-align: left; }
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
                <div class="company-header">
                    <div class="company-name">${companyName}</div>
                    <div class="company-address">${companyAddress}</div>
                    <div><b>Ph:</b> ${companyPhone}</div>
                    </div>
                    <h4 style="text-align:left;">Ledger</h4>
                    <h6 style="text-align:left;"><span style="font-weight:bold;">Start Date:</span> ${displayStart}</h6>
                    <h6 style="text-align:left;"><span style="font-weight:bold;">End Date:</span> ${displayEnd}</h6>
                    <div class="print-meta"><span class="label">Name:</span><span class="value">${partyName}</span></div>
                ${printContents}
            </body>
        </html>
    `;

            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }

        function downloadPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(14);
            doc.text("Ledger Details", 14, 15);

            const rawStart = document.getElementById("display-start-date")?.innerText || document.getElementById('start_date')?.value || '';
            const rawEnd = document.getElementById("display-end-date")?.innerText || document.getElementById('end_date')?.value || '';
            const startDate = formatDisplayDate(rawStart);
            const endDate = formatDisplayDate(rawEnd);
            const partyName = document.getElementById("display-party-name")?.innerText || "N/A";

            let y = 22;
            doc.setFontSize(12);
            if (companyName) {
                doc.text(companyName, 14, y);
                y += 6;
                if (companyAddress) {
                    doc.setFontSize(10);
                    doc.text(companyAddress, 14, y);
                    y += 6;
                }
                if (companyPhone) {
                    doc.setFontSize(10);
                    doc.text(`Ph: ${companyPhone}`, 14, y);
                    y += 6;
                }
            }

            doc.setFontSize(10);
            doc.text(`Start Date: ${startDate}`, 14, y + 4);
            doc.text(`End Date: ${endDate}`, 100, y + 4);
            doc.text(`Name: ${partyName}`, 14, y + 10);

            doc.autoTable({
                html: '#basic-datatable',
                startY: y + 16,
                styles: {
                    fontSize: 9,
                    cellPadding: 2
                },
                headStyles: {
                    fillColor: [52, 73, 94],
                    textColor: 255
                },
            });

            // sanitize filename
            const safeParty = partyName.replace(/[^a-z0-9_-]/gi, '_') || 'ledger';
            const safeStart = startDate.replace(/\s+/g, '_');
            const safeEnd = endDate.replace(/\s+/g, '_');
            const fileName = `ledger_${safeParty}_${safeStart}_to_${safeEnd}.pdf`;
            doc.save(fileName);
        }
    </script>
@endsection
