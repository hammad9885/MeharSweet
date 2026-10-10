@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Softix</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                        <li class="breadcrumb-item active">Edit Cash Payment</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Cash Payment</h4>
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
            <div class="card">
                <div class="card-body">
                    <form id="voucherForm" action="{{ route('payment.update', $voucher->first()->v_no) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="v_type" value="CPV">
                        <input type="hidden" name="invoice_number">
                        <input type="hidden" id="totalAmount" name="total_amount">

                        <div class="d-flex gap-2 mt-2">
                            <button type="button" id="addEntry" class="btn btn-primary btn-sm">Add Entry</button>
                            <button type="submit" class="btn btn-success btn-sm">Submit</button>
                        </div>


                        <!-- Global Date Updater -->
                        <div class="mt-3 mb-2">
                            <label for="globalDate" class="form-label fw-bold">Edit Dates:</label>
                            <input type="date" id="globalDate" class="form-control" style="max-width: 250px; display: inline-block;">
                        </div>

                        <h3 class="mt-4">Voucher <span id="invoiceDisplay"></span></h3>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered" id="entriesTable">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Date</th>
                                        <th>Cash</th>
                                        <th>Account Title</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="entriesBody">
                                    @foreach ($voucher as $entry)
                                        <tr>
                                            <td class="sr-no">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="date" class="form-control entry-date" name="entries[{{ $entry->id }}][date]" value="{{ $entry->date }}" readonly>
                                                <input type="hidden" name="entries[{{ $entry->id }}][id]" value="{{ $entry->id }}">
                                            </td>
                                            <td>
                                                <select class="form-control select2" name="entries[{{ $entry->id }}][cash]">
                                                    @foreach ($accountMasters as $account)
                                                        <option value="{{ $account->id }}" @selected($entry->cash_id == $account->id)>
                                                            {{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control select2" name="entries[{{ $entry->id }}][account]">
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}" @selected($entry->account_id == $account->id)>
                                                            {{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="entries[{{ $entry->id }}][description]" class="form-control" value="{{ $entry->description }}">
                                            </td>
                                            <td>
                                                <input type="number" name="entries[{{ $entry->id }}][debit]" class="form-control" value="{{ $entry->debit }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-entry" data-id="{{ $entry->id }}">Delete</button>
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
document.addEventListener('DOMContentLoaded', function () {
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

    // Set global date to first entry's date if available
    const firstDateInput = entriesTable.querySelector('.entry-date');
    if (firstDateInput) {
        globalDateInput.value = firstDateInput.value;
    }

    // When global date changes, update all entry dates
    globalDateInput.addEventListener('change', function () {
        const newDate = this.value;
        document.querySelectorAll('.entry-date').forEach(input => {
            input.value = newDate;
        });
    });

    addEntryButton.addEventListener('click', function () {
        const entryKey = 'new_' + Date.now();
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="sr-no"></td>
            <td>
                <input type="date" class="form-control entry-date" name="entries[${entryKey}][date]" value="${globalDateInput.value || ''}" readonly>
            </td>
            <td>
                <select class="form-control select2" name="entries[${entryKey}][cash]">
@foreach ($accountMasters as $account)
                                                            {{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}
                                                    @endforeach
                </select>
            </td>
            <td>
                <select class="form-control select2" name="entries[${entryKey}][account]">
@foreach ($accounts as $account)
                                                            {{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}
                                                    @endforeach
                </select>
            </td>
            <td><input type="text" name="entries[${entryKey}][description]" class="form-control"></td>
            <td><input type="number" name="entries[${entryKey}][debit]" class="form-control" value="0"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-entry">Delete</button></td>
        `;
        entriesTable.appendChild(newRow);
         // ✅ re-init Select2 for this row
    $(newRow).find('.select2').select2({ width: '100%' });
        updateSrNo();
        bindRemove(newRow);
        // Set the date field to readonly and sync with global date
        const dateInput = newRow.querySelector('.entry-date');
        if (dateInput) {
            dateInput.readOnly = true;
            if (globalDateInput.value) {
                dateInput.value = globalDateInput.value;
            }
        }
    });

    function bindRemove(row) {
        row.querySelector('.remove-entry').addEventListener('click', function () {
            const btn = this;
            const entryId = btn.getAttribute('data-id');
            if (entryId) {
                if (confirm('Are you sure you want to delete this entry?')) {
                    fetch(`/payment-delete/${entryId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

    function updateSrNo() {
        Array.from(entriesTable.rows).forEach((r, i) => {
            r.querySelector('.sr-no').textContent = i + 1;
        });
    }

    // Attach remove to existing rows
    entriesTable.querySelectorAll('.remove-entry').forEach(btn => {
        bindRemove(btn.closest('tr'));
    });

    // Make sure all entry date fields are readonly (in case of browser autofill)
    document.querySelectorAll('.entry-date').forEach(input => {
        input.readOnly = true;
    });

    updateSrNo();
});
</script>
@endsection
