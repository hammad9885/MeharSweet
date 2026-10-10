@extends('layouts.app')

@section('content')
    <style>
        /* Only input row needs custom height — everything else inherits from theme */
        #inputRow td {
            padding: 6px 8px;
        }
        #inputRow .form-control {
            height: 32px;
            font-size: 13px;
            padding: 4px 8px;
        }
        #journalVoucherTable td,
        #journalVoucherTable th {
            vertical-align: middle;
            padding: 5px 8px;
        }
        #journalVoucherTable tfoot td {
            font-weight: 700;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">ProBox</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Vouchers</a></li>
                        <li class="breadcrumb-item active">Journal Voucher</li>
                    </ol>
                </div>
                <h4 class="page-title">Journal Voucher</h4>
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="voucherForm" action="{{ route('journal_voucher.store') }}" method="POST">
                        @csrf
                        <input type="hidden" id="invoice_type" name="v_type" value="JV">

                        {{-- Date --}}
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="entryDate" class="form-label fw-bold">Date</label>
                                <input type="date" id="entryDate" class="form-control" name="voucher_date" required>
                            </div>
                        </div>

                        {{-- ===== Combined Table: Entries + Input Row + Total ===== --}}
                        <div class="table-responsive">
                            <table class="table table-bordered" id="journalVoucherTable">
                                <thead>
                                    <tr>
                                        <th style="width:5%; text-align:center;">#</th>
                                        <th style="width:28%;">Account Title</th>
                                        <th style="width:30%;">Description</th>
                                        <th style="width:14%; text-align:right;">Debit Amount</th>
                                        <th style="width:14%; text-align:right;">Credit Amount</th>
                                        <th style="width:9%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="entryTableBody">
                                    {{-- Dynamic entry rows go here --}}
                                </tbody>

                                {{-- ===== Input Row (inside table, above total) ===== --}}
                                <tbody>
                                    <tr id="inputRow">
                                        <td class="text-center text-muted" style="font-size:13px;">+</td>

                                        {{-- Account Title --}}
                                        <td>
                                            <select id="accountTitle" class="form-control select2" data-toggle="select2">
                                                <option value="">Select Account</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->title }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- Description --}}
                                        <td>
                                            <input type="text" id="description" class="form-control"
                                                placeholder="Enter Description">
                                        </td>

                                        {{-- Debit --}}
                                        <td>
                                            <input type="number" step="any" id="debitAmount"
                                                class="form-control text-end" placeholder="0.00" min="0">
                                        </td>

                                        {{-- Credit --}}
                                        <td>
                                            <input type="number" step="any" id="creditAmount"
                                                class="form-control text-end" placeholder="0.00" min="0">
                                        </td>

                                        {{-- Add Button --}}
                                        <td class="text-center">
                                            <button type="button" id="addEntry" class="btn btn-primary btn-sm w-100" style="white-space:nowrap;">
                                                <i class="uil-plus"></i> Add Entry
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>

                                {{-- ===== Total Row ===== --}}
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end pe-3">Total:</td>
                                        <td class="text-end pe-2">
                                            <span id="debitTotal">0.00</span>
                                            <input type="hidden" id="totalDebitInput" name="total_debit" value="0">
                                        </td>
                                        <td class="text-end pe-2">
                                            <span id="creditTotal">0.00</span>
                                            <input type="hidden" id="totalCreditInput" name="total_credit" value="0">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-3 text-end">
                            <button type="submit" id="submitVoucher" class="btn btn-success px-4">
                                <i class="uil-check"></i> Submit Voucher
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Set today's date
            document.getElementById('entryDate').value = new Date().toISOString().split('T')[0];

            const entryTableBody    = document.getElementById('entryTableBody');
            const debitTotalSpan    = document.getElementById('debitTotal');
            const creditTotalSpan   = document.getElementById('creditTotal');
            const accountTitle      = document.getElementById('accountTitle');
            const debitAmountField  = document.getElementById('debitAmount');
            const creditAmountField = document.getElementById('creditAmount');
            const descriptionField  = document.getElementById('description');
            const addEntryButton    = document.getElementById('addEntry');
            const submitVoucher     = document.getElementById('submitVoucher');
            const totalDebitInput   = document.getElementById('totalDebitInput');
            const totalCreditInput  = document.getElementById('totalCreditInput');

            let entryCount = 0;

            // ---- Recalculate totals ----
            function calculateTotal() {
                let debitTotal  = 0;
                let creditTotal = 0;
                document.querySelectorAll('.entry-debit').forEach(f  => debitTotal  += parseFloat(f.value)  || 0);
                document.querySelectorAll('.entry-credit').forEach(f => creditTotal += parseFloat(f.value) || 0);
                debitTotalSpan.textContent  = debitTotal.toFixed(2);
                creditTotalSpan.textContent = creditTotal.toFixed(2);
                totalDebitInput.value  = debitTotal;
                totalCreditInput.value = creditTotal;
            }

            // ---- Add entry row ----
            function addEntry() {
                if (!accountTitle.value) {
                    alert('Please select an Account Title.');
                    return;
                }

                const selectedOption   = accountTitle.options[accountTitle.selectedIndex];
                const accountTitleText = selectedOption.text;
                const accountIdValue   = selectedOption.value;
                const debitAmount      = parseFloat(debitAmountField.value)  || 0;
                const creditAmount     = parseFloat(creditAmountField.value) || 0;
                const descValue        = descriptionField.value.trim();

                if (debitAmount <= 0 && creditAmount <= 0) {
                    alert('Please enter a valid Debit or Credit amount.');
                    return;
                }
                if (debitAmount > 0 && creditAmount > 0) {
                    alert('Enter amount in either Debit or Credit — not both.');
                    return;
                }

                entryCount++;
                const debitDisplay  = debitAmount  > 0 ? debitAmount.toFixed(2)  : '0.00';
                const creditDisplay = creditAmount > 0 ? creditAmount.toFixed(2) : '0.00';

                const row = `
                    <tr id="entryRow${entryCount}">
                        <td class="text-center">${entryCount}</td>
                        <td>
                            <input type="hidden" name="entry_account_title[]" value="${accountIdValue}">
                            ${accountTitleText}
                        </td>
                        <td>
                            <input type="hidden" name="entry_description[]" value="${descValue}">
                            ${descValue || '<span class="text-muted">—</span>'}
                        </td>
                        <td class="text-end pe-2">
                            <input type="hidden" name="entry_debit[]" class="entry-debit" value="${debitAmount > 0 ? debitAmount : 0}">
                            ${debitDisplay}
                        </td>
                        <td class="text-end pe-2">
                            <input type="hidden" name="entry_credit[]" class="entry-credit" value="${creditAmount > 0 ? creditAmount : 0}">
                            ${creditDisplay}
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-entry w-100" style="white-space:nowrap;">
                                <i class="uil-trash"></i> Remove
                            </button>
                        </td>
                    </tr>`;

                entryTableBody.insertAdjacentHTML('beforeend', row);

                // Reset input row
                debitAmountField.value  = '';
                creditAmountField.value = '';
                descriptionField.value  = '';
                debitAmountField.disabled  = false;
                creditAmountField.disabled = false;
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#accountTitle').val('').trigger('change');
                } else {
                    accountTitle.value = '';
                }

                calculateTotal();
                accountTitle.focus();
            }

            addEntryButton.addEventListener('click', addEntry);

            // Enter key triggers add in description/amount fields
            [descriptionField, debitAmountField, creditAmountField].forEach(input => {
                input && input.addEventListener('keydown', e => {
                    if (e.key === 'Enter') { e.preventDefault(); addEntry(); }
                });
            });

            // Mutual exclusion: Debit <-> Credit
            debitAmountField.addEventListener('input', function () {
                creditAmountField.disabled = !!this.value;
                if (!this.value) creditAmountField.disabled = false;
            });
            creditAmountField.addEventListener('input', function () {
                debitAmountField.disabled = !!this.value;
                if (!this.value) debitAmountField.disabled = false;
            });

            // Remove row and re-index
            entryTableBody.addEventListener('click', function (e) {
                const btn = e.target.closest('.remove-entry');
                if (btn) {
                    btn.closest('tr').remove();
                    calculateTotal();
                    entryTableBody.querySelectorAll('tr').forEach((r, i) => {
                        r.cells[0].textContent = i + 1;
                    });
                    entryCount = entryTableBody.querySelectorAll('tr').length;
                }
            });

            // Submit validation
            document.getElementById('voucherForm').addEventListener('submit', function (e) {
                if (entryCount === 0) {
                    alert('Please add at least one entry before submitting.');
                    e.preventDefault(); return;
                }
                const dt = parseFloat(totalDebitInput.value)  || 0;
                const ct = parseFloat(totalCreditInput.value) || 0;
                if (Math.abs(dt - ct) > 0.01) {
                    alert('Total Debit and Credit must be equal.');
                    e.preventDefault(); return;
                }
                if (submitVoucher.disabled) { e.preventDefault(); return; }
                submitVoucher.disabled = true;
                submitVoucher.innerHTML = '<i class="uil-spinner-alt"></i> Submitting...';
            });
        });
    </script>
@endsection
