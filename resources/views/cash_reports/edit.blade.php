@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Edit Cash Receipt</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Cash Receipt</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-none">
                    <div class="card-body">
                        <form id="voucherForm" action="{{ route('cash.update', $voucher->first()->v_no) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="v_type" value="CRV">
                            <input type="hidden" name="invoice_number" value="{{ $voucher->first()->v_no }}">
                            <input type="hidden" id="totalAmount" name="total_amount">

                            <div class="d-flex gap-2 mb-3">
                                <button type="button" id="addEntry" class="btn show-btn btn-sm">Add Entry</button>
                                <button type="submit" class="btn btn-success btn-sm">Update Entries</button>
                            </div>

                            <!-- Global Date and Cash Updater -->
                            <div class="mt-3 mb-2 d-flex gap-3">
                                <div style="width: 320px;">
                                    <label for="globalDate" class="form-label fw-bold">Edit Dates:</label>
                                    <input type="date" id="globalDate" 
                                        class="form-control">
                                </div>

                                <div style="width: 320px;">
                                    <label for="globalCash" class="form-label fw-bold">Edit Cash:</label>
                                    <select id="globalCash" class="form-control select2">
                                        <option value="">Select Cash Account</option>
                                        @foreach ($accountMasters as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <h3 class="mt-3">Voucher <span id="invoiceDisplay">{{ $voucher->first()->v_no }}</span></h3>

                            <!-- Entries Table -->
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="entriesTable">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th width="10%">A/C Code</th>
                                            <th class="d-none">Date</th>
                                            <th class="d-none">Cash</th>
                                            <th width="30%">Account Title</th>
                                            <th width="10%">Description</th>
                                            <th width="10%">Amount</th>
                                            <th>IMG</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="entriesBody">
                                        @foreach ($voucher as $entry)
                                            <tr>
                                                <td class="sr-no">{{ $loop->iteration }}</td>
                                                <td>{{ $entry->acc_codes->full_code ?? 'N/A' }}</td>
                                                <td class="d-none">
                                                    <input type="hidden" class="entry-date"
                                                        name="entries[{{ $entry->id }}][date]"
                                                        value="{{ $entry->date }}">
                                                    <input type="hidden" name="entries[{{ $entry->id }}][id]"
                                                        value="{{ $entry->id }}">
                                                </td>
                                                <td class="d-none">
                                                    <input type="hidden" class="cash-hidden"
                                                        name="entries[{{ $entry->id }}][cash]"
                                                        value="{{ $entry->cash_id }}">
                                                </td>
                                                <td>
                                                    <select class="form-control select2"
                                                        name="entries[{{ $entry->id }}][account]">
@foreach ($accounts as $account)
                                                            <option value="{{ $account->id }}"
                                                                @selected($entry->account_id == $account->id)
                                                            {{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="entries[{{ $entry->id }}][description]"
                                                        class="form-control" value="{{ $entry->description }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                        name="entries[{{ $entry->id }}][credit]" class="form-control"
                                                        value="{{ $entry->credit }}">
                                                </td>
                                                <td>

                                                    <input type="file" id="uploadFile" class="form-control"
                                                        name="entries[{{ $entry->id }}][file]">

                                                    <p>Current: <img src="{{ asset('storage/' . $entry->file_id) }}"
                                                            width="80">
                            </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-entry"
                                    data-id="{{ $entry->id }}"
                                    onclick="return confirm('Are you sure you want to delete this transaction?')">Delete</button>
                            </td>
                            </tr>
                            @endforeach
                            </tbody>
                            </table>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- CSRF Token for JS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.select2').select2({
                width: '100%'
            });

            // Tab key: focus the dropdown's search box whenever it opens
            $(document).on('select2:open', function(e) {
                const $sel = $(e.target);
                setTimeout(function() {
                    const $search = $sel.data('select2').dropdown.$search;
                    if ($search.length) {
                        $search.focus();
                    }
                }, 100);
            });

            // Tab key: auto-open the dropdown when focus reaches a select2 field
            $(document).on('focus', '.select2-selection--single', function(e) {
                const $select = $(this).closest('.select2-container').siblings('select.select2').first();
                if ($select.length && $select.data('select2') && !$select.data('select2').isOpen()) {
                    $select.select2('open');
                }
            });

            const addEntryButton = document.getElementById('addEntry');
            const entriesTable = document.getElementById('entriesBody');
            const globalDateInput = document.getElementById('globalDate');
            const globalCashInput = document.getElementById('globalCash');

            // Prefill global controls with first row's values
            const firstDateInput = entriesTable.querySelector('.entry-date');
            const firstCashInput = entriesTable.querySelector('.cash-hidden');
            if (firstDateInput) {
                globalDateInput.value = firstDateInput.value;
            }
            if (firstCashInput) {
                globalCashInput.value = firstCashInput.value;
                $('#globalCash').trigger('change');
            }

            // Global date sync
            globalDateInput.addEventListener('change', function() {
                const newDate = this.value;
                document.querySelectorAll('.entry-date').forEach(input => {
                    input.value = newDate;
                });
            });

            // Global cash sync
            $('#globalCash').on('change', function() {
                const newCashId = this.value;

                document.querySelectorAll('.entry-cash').forEach(select => {
                    $(select).val(newCashId).trigger('change');
                });

                document.querySelectorAll('.cash-hidden').forEach(input => {
                    input.value = newCashId;
                });
            });

            // Add new entry
            addEntryButton.addEventListener('click', function() {
                const entryKey = 'new_' + Date.now();
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
        <td class="sr-no"></td>
        <td>N/A</td>
        <td class="d-none">
            <input type="hidden" class="entry-date" name="entries[${entryKey}][date]" value="${globalDateInput.value || ''}">
        </td>
        <td class="d-none">
            <input type="hidden" class="cash-hidden" name="entries[${entryKey}][cash]" value="${globalCashInput.value || ''}">
        </td>
        <td>
            <select class="form-control select2" name="entries[${entryKey}][account]">
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="entries[${entryKey}][description]" class="form-control"></td>
        <td><input type="number" step="0.01" name="entries[${entryKey}][credit]" class="form-control" value="0"></td>
             <td> <input type="file" id="uploadFile" class="form-control" name="entries[${entryKey}][file]"></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-entry" onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button></td>
    `;
                entriesTable.appendChild(newRow);

                // âœ… re-init Select2 for this row
                $(newRow).find('.select2').select2({
                    width: '100%'
                });

                updateSrNo();
                bindRemove(newRow);
                updateTotalAmount();
            });

            // Remove entry handler
            function bindRemove(row) {
                row.querySelector('.remove-entry').addEventListener('click', function() {
                    const btn = this;
                    const entryId = btn.getAttribute('data-id');
                    if (entryId) {
                        if (confirm('Are you sure you want to delete this entry?')) {
                            fetch(`/cash-delete/${entryId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                            }).then(() => {
                                row.remove();
                                updateSrNo();
                                updateTotalAmount();
                            }).catch(() => alert('Failed to delete entry.'));
                        }
                    } else {
                        row.remove();
                        updateSrNo();
                        updateTotalAmount();
                    }
                });
            }

            // Update serial numbers
            function updateSrNo() {
                Array.from(entriesTable.rows).forEach((r, i) => {
                    r.querySelector('.sr-no').textContent = i + 1;
                });
            }

            // Update total amount
            function updateTotalAmount() {
                let sum = 0;
                entriesTable.querySelectorAll('input[name*="[credit]"]').forEach(el => {
                    sum += parseFloat(el.value) || 0;
                });
                document.getElementById('totalAmount').value = sum.toFixed(2);
            }

            // Attach remove to existing rows
            entriesTable.querySelectorAll('.remove-entry').forEach(btn => {
                bindRemove(btn.closest('tr'));
            });

            // Ensure all dates readonly
            document.querySelectorAll('.entry-date').forEach(input => {
                input.readOnly = true;
            });

            updateSrNo();
            updateTotalAmount();
        });
    </script>
@endsection
