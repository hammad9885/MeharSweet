@extends('layouts.app')
@section('content')
    <div class="container">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h3 class="page-title">Bank Payment</h3>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        <!-- Show Form -->
        <div class="row">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="col-12">
                            <form action="{{ route('bank_payment.reports') }}" method="GET" id="search-form">
                                <div class="row g-2 align-items-end">

                                    {{-- Start Date --}}
                                    <div class="col-md-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request()->get('start_date') }}">
                                    </div>

                                    {{-- End Date --}}
                                    <div class="col-md-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request()->get('end_date') }}">
                                    </div>

                                    

                                    {{-- Voucher Number --}}
                                    <div class="col-md-3">
                                        <label for="v_no" class="form-label">Voucher Number</label>
                                        <select name="v_no" class="form-select select2" data-toggle="select2">
                                            <option value="">Select Voucher</option>
                                            @foreach ($vNoList as $vNo)
                                                <option value="{{ $vNo }}"
                                                    {{ request()->get('v_no') == $vNo ? 'selected' : '' }}>
                                                    {{ $vNo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Account Title --}}
                                    <div class="col-md-3">
                                        <label for="account_id" class="form-label">Account Title</label>
                                        <select name="account_id" class="form-select select2" data-toggle="select2">
                                            <option value="">Select Account</option>
                                            @foreach ($accountIdList as $id)
                                                @php
                                                    $account = \App\Models\AccountMaster::find($id);
                                                @endphp
                                                <option value="{{ $id }}"
                                                    {{ request()->get('account_id') == $id ? 'selected' : '' }}>
                                                    {{ $account ? $account->title : 'Unknown Account' }} . {{ $account->full_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn show-btn">Show</button>
                                        <a class="btn btn-success" href="{{ route('bank_payment.list') }}">
                                            Add New
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Ledger Table -->

        <div class="mt-2">
            <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
        </div>
        <div class="row">
            <div class="tab-content">
                <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>V. No</th>
                            <th>Account Code</th>
                            <th>Account Title</th>
                            <th>Amount</th>
                            <th class="no-print">Image</th>
                            <th class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @include('components.pop_up') --}}

                        @php
                            $groupedByVNo = $trndtls->groupBy(function ($item) {
                                return $item->v_type . '-' . $item->v_no;
                            });
                            $grandTotal = 0;
                        @endphp

                        @foreach ($groupedByVNo as $vNo => $items)
                            @php
                                $vNoTotal = 0;
                            @endphp
                            @foreach ($items as $trndtl)
                                @php
                                    $vNoTotal += $trndtl->debit;
                                    $grandTotal += $trndtl->debit;
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($trndtl->date)->format('d-m-Y') }}</td>
                                    <td>{{ $trndtl->v_type }}-{{ $trndtl->v_no }}</td>
                                    <td>{{ $trndtl->acc_codes->full_code ?? 'N/A' }}</td>
                                    <td>{{ $trndtl->accounts->title }}</td>
                                    <td>{{ number_format($trndtl->debit, 2) }}</td>
                                    <td class="no-print">
                                        @if (!empty($trndtl->file_id))
                                            <a href="{{ asset('storage/' . $trndtl->file_id) }}" target="_blank">
                                                <p>Img</p>
                                            </a>
                                        @else
                                            <p>No Img</p>
                                        @endif
                                    </td>
                                    <td class="no-print">
                                        <!-- Edit button -->
                                        <a href="{{ route('bank_payment.edit', $trndtl->v_no) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        
                                        <form action="{{ route('bank_payment.delete', $trndtl->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this transaction?')">Delete</button>
                                        </form>

                                        <a href="{{ route('printInvoice', ['id' => $trndtl->v_no, 'type' => $trndtl->v_type]) }}"
                                            target="_blank" class="btn show-btn btn-sm">
                                            Print Invoice
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="no-print" style="background-color: #f9f9f9; font-weight: bold; display: none;">
                                <td colspan="3"></td>
                                <td colspan="1" style="text-align: right;">Total</td>
                                <td>{{ number_format($vNoTotal, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="print-only" style="background-color: #e8e8e8; font-weight: bold; font-size: 1.1em; display: none;">
                            <td colspan="3"></td>
                            <td colspan="1" style="text-align: right;">GRAND TOTAL</td>
                            <td>{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        



        const today = new Date().toISOString().split('T')[0];

        // Set the value of the input field to the current date
        document.getElementById('end_date').value = today;


        function printTable() {
            // Hide elements with 'no-print' class
            const elementsToHide = document.querySelectorAll('.no-print');
            elementsToHide.forEach(el => el.style.display = 'none');

            // Show elements with 'print-only' class
            const elementsToShow = document.querySelectorAll('.print-only');
            elementsToShow.forEach(el => el.style.display = 'table-row');

            // Get all headings (both h4 and h5) and table content
            const headings = document.querySelectorAll('.col-12 h4, .col-12 h5');
            let headingsContent = '';
            headings.forEach(heading => {
                headingsContent += heading.outerHTML;
            });

            const tableContent = document.getElementById('basic-datatable').outerHTML;
            const originalContents = document.body.innerHTML;
             @php
                $company = auth()->user()->company;
            @endphp

            // Replace body content with the headings and table HTML for printing
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
                    .no-print {
                        display: none;
                    }
                    .print-only {
                        display: table-row !important;
                    }
                    h4, h5 {
                        margin: 5px 0;
                    }
                    .company-header {
                                margin-bottom: 20px;
                            }
                            .company-name {
                                font-weight: bold;
                                font-size: 18px;
                                margin-bottom: 5px;
                            }
                            .company-address {
                                word-wrap: break-word;
                                white-space: normal;
                                max-width: 300px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="company-header">
                            <div class="company-name">{{ $company->name ?? '' }}</div>
                            <div class="company-address">{{ $company->address ?? '' }}</div>
                            <div><b>Ph:</b> {{ $company->phone ?? '' }}</div>
                        </div>
                        <h4 style="text-align:left;">List of Bank Payments</h4>
                        <h5 style="text-align:left; font-weight:bold;">
    Bank:
    <span style="font-weight: normal;">{{ $trndtl->cashes->title ?? 'N/A' }}</span>
</h5>
                        
                ${headingsContent}
                ${tableContent}
            </body>
        </html>
    `;

            // Trigger print dialog
            window.print();

            // Restore the original page content after printing
            document.body.innerHTML = originalContents;

            // Reattach event listeners or reload the page if needed
            window.location.reload();
        }
    </script>
@endsection
