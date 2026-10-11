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
                            <li class="breadcrumb-item active">Edit Bank Receipt</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Bank Receipt</h4>
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
                        <form id="voucherForm" action="{{ route('bank_recipt.update', $voucher->first()->v_no) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="v_type" value="BRV">
                            <input type="hidden" name="invoice_number" value="{{ $voucher->first()->v_no }}">
                            <input type="hidden" id="totalAmount" name="total_amount">

                            <div class="d-flex gap-2 mt-2">
                                <button type="button" id="addEntry" class="btn show-btn btn-sm">Add Entry</button>
                                <button type="submit" class="btn btn-success btn-sm">Update Entries</button>
                            </div>

                            <h3 class="mt-4">Voucher <span id="invoiceDisplay">{{ $voucher->first()->v_no }}</span></h3>

                            <!-- Global Override Controls -->
                            <div class="mt-3 mb-2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="globalDate" class="form-label fw-bold">Date:</label>
                                        <input type="date" id="globalDate" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="globalBank" class="form-label fw-bold">Bank:</label>
                                        <select id="globalBank" class="form-control select2">
                                            <option value="">Select Bank</option>
                                            @foreach ($accountMasters as $account)
                                                <option value="{{ $account->id }}"
                                                    {{ $voucher->first()->cash_id == $account->id ? 'selected' : '' }}>
                                                    {{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="entriesTable">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th width="10%">A/C Code</th>
                                            <th class="d-none">Date</th>
                                            <th class="d-none">Bank</th>
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
                                                    <input type="hidden" class="entry-bank-hidden"
                                                        name="entries[{{ $entry->id }}][cash]"
                                                        value="{{ $entry->cash_id }}">
                                                </td>
                                                <td>
                                                    <select class="form-control select2"
                                                        name="entries[{{ $entry->id }}][account]">
                                                        @foreach ($accounts as $account)
                                                            <option value="{{ $account->id }}"
                                                                @selected($entry->account_id == $account->id)>
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
                                    data-id="{{ $entry->id }}">Delete</button>
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
            const globalBankInput = document.getElementById('globalBank');

            // Set global controls to first row values (use hidden inputs)
            const firstDateInput = entriesTable.querySelector('.entry-date');
            const firstBankHidden = entriesTable.querySelector('.entry-bank-hidden');

            if (firstDateInput) {
                globalDateInput.value = firstDateInput.value;
            }
            if (firstBankHidden) {
                globalBankInput.value = firstBankHidden.value;
            }

            // Update all dates when global date changes
            globalDateInput.addEventListener('change', function() {
                const newDate = this.value;
                entriesTable.querySelectorAll('.entry-date').forEach(dateInput => {
                    dateInput.value = newDate;
                });
            });

            // Update all banks when global bank changes (write to hidden inputs only)
            $('#globalBank').on('change', function() {
                const newBankId = this.value;
                document.querySelectorAll('.entry-bank-hidden').forEach(input => {
                    input.value = newBankId;
                });
            });

            // Add new row
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
                <input type="hidden" class="entry-bank-hidden" name="entries[${entryKey}][cash]" value="${globalBankInput.value || ''}">
            </td>
            <td>
                <select class="form-control select2" name="entries[${entryKey}][account]">
                    <option value="">Select</option>
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
                updateSrNo();
                bindRemove(newRow);

                // Set bank hidden value for new row
                const newBankHidden = newRow.querySelector('.entry-bank-hidden');
                if (newBankHidden && globalBankInput.value) {
                    newBankHidden.value = globalBankInput.value;
                }

                // Re-init select2
                $(newRow).find('.select2').select2({
                    width: '100%'
                });
            });

            // Remove entry handler
            function bindRemove(row) {
                row.querySelector('.remove-entry').addEventListener('click', function() {
                    const entryId = this.getAttribute('data-id');
                    if (entryId) {
                        if (confirm('Delete this entry?')) {
                            fetch(`/bank_recipt-delete/${entryId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                            }).then(() => {
                                row.remove();
                                updateSrNo();
                            }).catch(() => alert('Failed to delete entry.'));
                        }
                    } else {
                        row.remove();
                        updateSrNo();
                    }
                });
            }

            // Update Sr numbers
            function updateSrNo() {
                Array.from(entriesTable.rows).forEach((r, i) => {
                    r.querySelector('.sr-no').textContent = i + 1;
                });
            }

            // Attach remove to existing rows
            entriesTable.querySelectorAll('.remove-entry').forEach(btn => {
                bindRemove(btn.closest('tr'));
            });

            updateSrNo();
        });
    </script>
@endsection
