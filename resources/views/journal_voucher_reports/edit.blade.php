@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Journal Voucher</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

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

                    <form action="{{ route('journal_voucher.update', $voucher->first()->v_no) }}" method="POST" id="editVoucherForm">
                        @csrf
                        @method('PUT')

                        <!-- Global Voucher Date -->
                        <div class="mb-3">
                            <label for="entryDate" class="form-label">Voucher Date</label>
                            <input type="date" id="entryDate"  class="form-control" name="voucher_date" 
                                   value="{{ $voucher->first()->date ?? '' }}" required>
                        </div>

                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="d-none">Date</th>
                                    <th>A/C Code</th>
                                    <th>Account Title</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="entryTableBody">
                                @foreach ($voucher as $index => $entry)
                                <tr data-row-index="{{ $index }}">
                                    <td class="row-number">{{ $loop->iteration }}</td>
                                    <td class="d-none">
                                        <input type="hidden" name="entries[{{ $index }}][date]" class="entry-date" 
                                               value="{{ $entry->date }}">
                                    </td>
                                    <td>{{ $entry->acc_codes->full_code ?? 'N/A' }}</td>
                                    
                                    <td>
                                        <input type="hidden" name="entries[{{ $index }}][id]" value="{{ $entry->id }}">
                                        <select name="entries[{{ $index }}][account_title]" class="form-control select2" data-toggle="select2" required>
                                            <option value="">Select Account</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" 
                                                    {{ $entry->account_id == $account->id ? 'selected' : '' }}>
                                                    {{ $account->title }} . {{ $account->full_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="entries[{{ $index }}][debit]" 
                                               class="form-control entry-debit" 
                                               value="{{ $entry->debit }}" min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="number" name="entries[{{ $index }}][credit]" 
                                               class="form-control entry-credit" 
                                               value="{{ $entry->credit }}" min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="text" name="entries[{{ $index }}][description]" 
                                               class="form-control" value="{{ $entry->description }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteRow(this, {{ $entry->id }})" onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button>
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

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

</div>

<script>
    let rowCounter = {{ count($voucher) }};

    // --- Calculate Totals ---
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

    // --- Toggle Debit/Credit (disable one when other has value) ---
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

    // --- Update Row Numbers ---
    function updateRowNumbers() {
        document.querySelectorAll('#entryTableBody tr').forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    // --- Delete Row ---
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

    // --- Add New Row ---
    function addEntryRow() {
        const tbody = document.getElementById('entryTableBody');
        const newIndex = rowCounter++;
        const voucherDate = document.getElementById('entryDate').value || '{{ date("Y-m-d") }}';
        
        const row = document.createElement('tr');
        row.setAttribute('data-row-index', newIndex);
        row.innerHTML = `
            <td class="row-number">${tbody.children.length + 1}</td>
            <td class="d-none">
                <input type="hidden" name="entries[${newIndex}][date]" class="entry-date" 
                       value="${voucherDate}">
            </td>
            <td>N/A</td>
            <td>
                <input type="hidden" name="entries[${newIndex}][id]" value="">
                <select name="entries[${newIndex}][account_title]" class="form-control select2" data-toggle="select2" required>
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->title }} . {{ $account->full_code }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="entries[${newIndex}][debit]" 
                       class="form-control entry-debit" value="0" min="0" step="0.01">
            </td>
            <td>
                <input type="number" name="entries[${newIndex}][credit]" 
                       class="form-control entry-credit" value="0" min="0" step="0.01">
            </td>
            <td>
                <input type="text" name="entries[${newIndex}][description]" class="form-control" value="">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)" onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button>
            </td>
        `;
        
        tbody.appendChild(row);
        updateRowNumbers();
        
        // Initialize Select2 on new select element
        $(row).find('.select2').select2({
        width: '100%',
                theme: 'bootstrap-5',
                placeholder: $(this).attr('placeholder') || 'Select an option',
                allowClear: true
        });;
        
        // Add event listeners to new row
        row.querySelector('.entry-debit').addEventListener('input', toggleDebitCredit);
        row.querySelector('.entry-credit').addEventListener('input', toggleDebitCredit);
    }

    // --- Sync all entry dates with voucher date ---
    document.getElementById('entryDate').addEventListener('change', function() {
        const newDate = this.value;
        document.querySelectorAll('#entryTableBody input[name*="[date]"]').forEach(function(dateInput) {
            dateInput.value = newDate;
        });
    });

    // --- Initialize date synchronization on page load ---
    function initializeDateSync() {
        const voucherDate = document.getElementById('entryDate').value;
        if (voucherDate) {
            document.querySelectorAll('#entryTableBody input[name*="[date]"]').forEach(function(dateInput) {
                dateInput.value = voucherDate;
            });
        }
    }

    // --- Form validation before submit ---
    document.getElementById('editVoucherForm').addEventListener('submit', function(e) {
        const debit = parseFloat(document.getElementById('debitTotal').value) || 0;
        const credit = parseFloat(document.getElementById('creditTotal').value) || 0;
        
        if (Math.abs(debit - credit) > 0.01) {
            alert('Total debit and credit must be equal.');
            e.preventDefault();
        }
    });

    // --- Initialize on page load ---
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
        initializeDateSync();
        
        // Add event listeners to existing rows
        document.querySelectorAll('.entry-debit, .entry-credit').forEach(input => {
            input.addEventListener('input', toggleDebitCredit);
        });
    });
</script>
@endsection