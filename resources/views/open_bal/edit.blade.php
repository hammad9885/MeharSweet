@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Namo</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                        <li class="breadcrumb-item active">Data Tables</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Opening Balance</h4>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('open_bal.update') }}" method="POST" id="editVoucherForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="entryDate" class="form-label">Opening Date</label>
                            <input type="date" id="entryDate" class="form-control" name="voucher_date" value="{{ $entries->first()->op_date ?? '' }}" required>
                        </div>
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>A/C Code</th>
                                    <th>Account Title</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="entryTableBody">
                                @foreach ($entries as $index => $entry)
                                <tr data-row-index="{{ $index }}">
                                    <td class="row-number">{{ $loop->iteration }}</td>
                                    <td class="ac-code-cell">{{ optional($entry->account)->full_code ?? 'N/A' }}</td>
                                    <td>
                                        <input type="hidden" name="entries[{{ $index }}][id]" value="{{ $entry->id }}">
                                        <select name="entries[{{ $index }}][account_title]" class="form-control select2" data-toggle="select2" required>
                                            <option value="">Select Account</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" data-code="{{ $account->full_code ?? '' }}" {{ $entry->aid == $account->id ? 'selected' : '' }}>{{ $account->title }} . {{ $account->full_code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="entries[{{ $index }}][debit]" class="form-control entry-debit" value="{{ $entry->debit }}" min="0" step="0.01"></td>
                                    <td><input type="number" name="entries[{{ $index }}][credit]" class="form-control entry-credit" value="{{ $entry->credit }}" min="0" step="0.01"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this, {{ $entry->id }})" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary" onclick="addEntryRow()">Add Entry</button>
                        <button type="submit" class="btn btn-success">Update Voucher</button>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label>Total Debit:</label>
                                <input type="text" id="debitTotal" class="form-control" value="0" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Total Credit:</label>
                                <input type="text" id="creditTotal" class="form-control" value="0" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let rowCounter = {{ count($entries) }};

    function calculateTotal() {
        let debitTotal = 0;
        let creditTotal = 0;
        
        document.querySelectorAll('.entry-debit').forEach(function(input) {
            debitTotal += parseFloat(input.value) || 0;
        });
        
        document.querySelectorAll('.entry-credit').forEach(function(input) {
            creditTotal += parseFloat(input.value) || 0;
        });
        
        document.getElementById('debitTotal').value = debitTotal.toFixed(2);
        document.getElementById('creditTotal').value = creditTotal.toFixed(2);
    }

    function toggleDebitCredit(e) {
        const row = e.target.closest('tr');
        if (!row) return;
        
        const debitInput = row.querySelector('.entry-debit');
        const creditInput = row.querySelector('.entry-credit');
        
        if (e.target === debitInput && parseFloat(debitInput.value) > 0) {
            creditInput.value = '0';
            creditInput.disabled = true;
        } else if (e.target === creditInput && parseFloat(creditInput.value) > 0) {
            debitInput.value = '0';
            debitInput.disabled = true;
        } else {
            debitInput.disabled = false;
            creditInput.disabled = false;
        }
        
        calculateTotal();
    }

    function updateRowNumbers() {
        document.querySelectorAll('#entryTableBody tr').forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    function deleteRow(btn, entryId) {
        const row = btn.closest('tr');
        
        if (entryId) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_entry_ids[]';
            input.value = entryId;
            document.getElementById('editVoucherForm').appendChild(input);
        }
        
        row.remove();
        updateRowNumbers();
        calculateTotal();
    }

    function addEntryRow() {
        const tbody = document.getElementById('entryTableBody');
        const newIndex = rowCounter++;
        const row = document.createElement('tr');
        row.setAttribute('data-row-index', newIndex);
        row.innerHTML = `
            <td class="row-number">${tbody.children.length + 1}</td>
            <td class="ac-code-cell">N/A</td>
            <td>
                <input type="hidden" name="entries[${newIndex}][id]" value="">
                <select name="entries[${newIndex}][account_title]" class="form-control select2" data-toggle="select2" required>
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" data-code="{{ $account->full_code ?? '' }}">{{ $account->title }} . {{ $account->full_code }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="entries[${newIndex}][debit]" class="form-control entry-debit" value="0" min="0" step="0.01"></td>
            <td><input type="number" name="entries[${newIndex}][credit]" class="form-control entry-credit" value="0" min="0" step="0.01"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">Delete</button></td>
        `;

        tbody.appendChild(row);
        updateRowNumbers();

        $(row).find('.select2').select2({
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: 'Select an option',
            allowClear: true
        });

        row.querySelector('.entry-debit').addEventListener('input', toggleDebitCredit);
        row.querySelector('.entry-credit').addEventListener('input', toggleDebitCredit);

        const acctSelect = row.querySelector('select[name="entries['+newIndex+'][account_title]"]');
        if (acctSelect) {
            acctSelect.addEventListener('change', function(e) {
                const selectedOpt = e.target.selectedOptions[0];
                const codeCell = row.querySelector('.ac-code-cell');
                codeCell.textContent = selectedOpt?.dataset?.code || 'N/A';
            });
        }
    }

    document.getElementById('editVoucherForm').addEventListener('submit', function(e) {
        const debit = parseFloat(document.getElementById('debitTotal').value) || 0;
        const credit = parseFloat(document.getElementById('creditTotal').value) || 0;
        
        if (debit !== credit) {
            alert('Total debit and credit must be equal.');
            e.preventDefault();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
        document.querySelectorAll('.entry-debit, .entry-credit').forEach(input => {
            input.addEventListener('input', toggleDebitCredit);
        });
        document.querySelectorAll('#entryTableBody select[name$="[account_title]"]').forEach(function(selectEl) {
            const row = selectEl.closest('tr');
            const selected = selectEl.selectedOptions[0];
            if (row && selected) {
                const codeCell = row.querySelector('.ac-code-cell');
                if (codeCell) codeCell.textContent = selected.dataset.code || 'N/A';
            }
            selectEl.addEventListener('change', function(e) {
                const selectedOpt = e.target.selectedOptions[0];
                const codeCell = row.querySelector('.ac-code-cell');
                if (codeCell) codeCell.textContent = selectedOpt?.dataset?.code || 'N/A';
            });
        });
    });
</script>
@endsection
