@extends('layouts.app')
@section('content')
 

    <div class="container">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    
                    <h4 class="page-title">Journal Voucher</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

    

        <!-- Show Form -->
        <div class="row">
            <div class="col-12">
                <div class="p-3">

                    <form action="{{ route('journal_voucher.reports') }}" method="GET" id="search-form">
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

                            

                           

                            {{-- Buttons (in same row as Description) --}}
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn show-btn">Show</button>
                                <a class="btn btn-success" href="{{ route('journal_voucher.list') }}"
                                    onclick="return checkPermission()">Add New</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="p-3">
                    <div id="print-header" style="display:none;">
                        <h3>Journal Voucher Details</h3>
                        
                    </div>

                    <button type="button" class="btn btn-secondary mb-2" style="width: 100px;" onclick="printTable()">Print
                        Table</button>

                    <h5>Start Date: {{ request()->get('start_date') ?? 'N/A' }} | End Date:
                        {{ request()->get('end_date') ?? date('Y-m-d') }}</h5>

                    <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>V. No</th>
                                <th>A/C Code</th>
                                <th>Account Title</th>
                                <th>Description</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                
                                {{-- <th class="no-print">Status</th> --}}
                                <th class="no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedByVNo = $trndtls->groupBy(function ($item) {
                                    return $item->v_type . '-' . $item->v_no;
                                });
                                $grandDebit = 0;
                                $grandCredit = 0;
                            @endphp

                            @foreach ($groupedByVNo as $vNo => $items)
                                @php
                                    $vTotalDebit = 0;
                                    $vTotalCredit = 0;
                                @endphp

                                @foreach ($items as $trndtl)
                                    @php
                                        $vTotalDebit += $trndtl->debit;
                                        $vTotalCredit += $trndtl->credit;
                                        $grandDebit += $trndtl->debit;
                                        $grandCredit += $trndtl->credit;
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trndtl->date)->format('d-m-Y') }}</td>
                                        <td>{{ $trndtl->v_type }}-{{ $trndtl->v_no }}</td>
                                        <td>{{ $trndtl->acc_codes->full_code ?? 'N/A' }}</td>
                                        <td>{{ $trndtl->accounts->title ?? 'N/A' }}</td>
                                        <td>{{ $trndtl->description ?? 'N/A' }}</td>
                                        <td>{{ number_format($trndtl->debit, 2) }}</td>
                                        <td>{{ number_format($trndtl->credit, 2) }}</td>
                                        
                                        {{-- <td class="no-print">
                                            <input type="checkbox" class="status-checkbox" data-id="{{ $trndtl->id }}"
                                                {{ $trndtl->status == 'official' ? 'checked' : '' }}>
                                        </td> --}}
                                        <td class="no-print">
                                            @php
                                                $level = App\Models\Module::where('name', 'Journal Voucher')->first();

                                                $update = App\Models\UserPermission::where('module_id', $level->id)
                                                    ->where('update', 1)
                                                    ->where('cid', auth()->user()->cid);
                                                $delete = App\Models\UserPermission::where('module_id', $level->id)
                                                    ->where('delete', 1)
                                                    ->where('cid', auth()->user()->cid);
                                            @endphp
                                            @if ($update->exists())
                                                <a href="{{ route('journal_voucher.edit', $trndtl->v_no) }}"
                                                    class="btn btn-warning btn-sm"
                                                    onclick="return checkPermissionEdit()">Edit</a>
                                            @endif
                                            @if ($delete->exists())
                                                <form action="{{ route('journal_voucher.destroy', $trndtl->v_no) }}"
                                                    method="POST" style="display:inline-block;"
                                                    onclick="return checkPermissionDel()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete the entire voucher JV-{{ $trndtl->v_no }}?')">Delete
                                                        Voucher</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Voucher-wise total (print-only) --}}
                                <tr class="print-only" style="background-color: #f9f9f9; font-weight: bold; display: none;">
                                    <td colspan="4">TOTAL:</td>
                                    <td>{{ number_format($vTotalDebit, 2) }}</td>
                                    <td>{{ number_format($vTotalCredit, 2) }}</td>
                                    
                                </tr>
                            @endforeach

                            {{-- Grand total (print-only) --}}
                            <tr class="print-only" style="background-color: #e8e8e8; font-weight: bold; font-size: 1.1em; display: none;">
                                <td colspan="4">GRAND TOTAL:</td>
                                <td>{{ number_format($grandDebit, 2) }}</td>
                                <td>{{ number_format($grandCredit, 2) }}</td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === Set today's date in End Date input only if not already set ===
        document.addEventListener("DOMContentLoaded", function() {
            const endDateInput = document.getElementById('end_date');
            if (!endDateInput.value) {
                endDateInput.value = new Date().toISOString().split('T')[0];
            }
        });

        // === Print Table Function (must be global) ===
        function printTable() {
            // Hide elements with 'no-print' class
            const elementsToHide = document.querySelectorAll('.no-print');
            elementsToHide.forEach(el => el.style.display = 'none');

            // Show elements with 'print-only' class if any
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
                $first = $trndtls->first();
            @endphp

            // Replace body content with the company header, headings and table HTML for printing
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

        // === Status Checkbox AJAX Update ===
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".status-checkbox").forEach(function(checkbox) {
                checkbox.addEventListener("change", function() {
                    let id = this.dataset.id;
                    let isChecked = this.checked ? 'official' : 'unofficial';

                    fetch(`/journal-voucher/update-status/${id}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute("content"),
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                status: isChecked
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log(`Status updated: ${data.status}`);
                            } else {
                                alert("Failed to update status!");
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert("Error updating status.");
                        });
                });
            });
        });
    </script>
@endsection
