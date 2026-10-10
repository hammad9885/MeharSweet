@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h4 class="page-title">Opening Balance</h4>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Success - </strong> {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="voucherForm" action="{{ route('bank.store') }}" method="POST">
                            <div class="col-6">


                                @csrf

                                <!-- Date field -->
                                <div class="mb-3">
                                    <label for="entryDate" class="form-label">Opening Date</label>
                                    <input type="date" id="entryDate" class="form-control" name="date" required>
                                </div>
                                {{-- <input type="hidden" id="invoice_type" class="form-control" name="v_type" value="JV"
                                required readonly> --}}
                                <!-- Account Title Dropdown -->
                                <div class="mb-3">
                                    <label for="accountTitle" class="form-label">Account Title</label>
                                    <select id="accountTitle" class="form-control" data-toggle="select2" name="account_id">
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Debit and Credit Amounts -->
                                <div class="mb-3">
                                    <label for="debitAmount" class="form-label">Debit Amount</label>
                                    <input type="number" id="debitAmount" class="form-control" name="debit"
                                        placeholder="Enter Debit">
                                </div>
                                <div class="mb-3">
                                    <label for="creditAmount" class="form-label">Credit Amount</label>
                                    <input type="number" id="creditAmount" class="form-control" name="credit"
                                        placeholder="Enter Credit">
                                </div>

                                <!-- Add Entry Button -->
                                <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
                                <button type="submit" id="submitVoucher" class="btn btn-success">Submit
                                    Voucher</button>
                            </div>
                                <!-- Entry Table -->
                                <table class="table table-bordered mt-4">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Account Title</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="entryTableBody">
                                        <!-- Entries will appear here -->
                                    </tbody>
                                </table>

                                <!-- Totals -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Total Debit:</label>
                                        <input type="text" id="debitTotal" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Total Credit:</label>
                                        <input type="text" id="creditTotal" class="form-control" readonly>
                                    </div>
                                </div>

                                <!-- Hidden inputs for total debit and total credit -->
                                <input type="hidden" id="totalDebitInput" name="total_debit">
                                <input type="hidden" id="totalCreditInput" name="total_credit">

                                <!-- Submit Button -->

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
         const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('entryDate').value = today;
        document.addEventListener('DOMContentLoaded', function() {
            let entryTableBody = document.getElementById('entryTableBody');
            let debitTotalField = document.getElementById('debitTotal');
            let creditTotalField = document.getElementById('creditTotal');
            let accountTitle = document.getElementById('accountTitle');
            let debitAmountField = document.getElementById('debitAmount');
            let creditAmountField = document.getElementById('creditAmount');
            let entryDate = document.getElementById('entryDate');
            let addEntryButton = document.getElementById('addEntry');
            let submitVoucher = document.getElementById('submitVoucher');

            let totalDebitInput = document.getElementById('totalDebitInput');
            let totalCreditInput = document.getElementById('totalCreditInput');

            let entryCount = 0;

            // Function to calculate the total amounts for debit and credit
            function calculateTotal() {
                let debitTotal = 0;
                let creditTotal = 0;

                document.querySelectorAll('.entry-debit').forEach(function(debitField) {
                    debitTotal += parseFloat(debitField.value) || 0;
                });

                document.querySelectorAll('.entry-credit').forEach(function(creditField) {
                    creditTotal += parseFloat(creditField.value) || 0;
                });

                debitTotalField.value = debitTotal;
                creditTotalField.value = creditTotal;

                // Update hidden input fields for form submission
                totalDebitInput.value = debitTotal;
                totalCreditInput.value = creditTotal;
            }

            // Event listener to add a new entry to the table
            // Event listener to add a new entry to the table
            addEntryButton.addEventListener('click', function() {
                let selectedOption = accountTitle.options[accountTitle.selectedIndex];
                let accountTitleValue = selectedOption.text; // Account title text
                let accountIdValue = selectedOption.value; // Account ID

                let debitAmount = parseFloat(debitAmountField.value);
                let creditAmount = parseFloat(creditAmountField.value);
                let entryDateValue = entryDate.value; // Get the value of the entry date

                // Check for validation
                if (!entryDateValue) {
                    alert('Please select a date.');
                    return;
                }
                if (!accountTitle.value) {
                    alert('Please select an Account Title.');
                    return;
                }
                if (!accountTitleValue || (debitAmount <= 0 && creditAmount <= 0)) {
                    alert('Please fill all fields and enter a valid amount for debit or credit.');
                    return;
                }

                // Ensure at least one of debit or credit is provided
                if (isNaN(debitAmount) && isNaN(creditAmount)) {
                    alert('Please enter a valid amount for either Debit or Credit.');
                    return;
                }

                // Only one of debit or credit should have a value
                if (debitAmount > 0 && creditAmount > 0) {
                    alert('You can only enter an amount in either Debit or Credit, not both.');
                    return;
                }

                // Add new entry row for debit or credit
                entryCount++;
                let debitValue = debitAmount > 0 ? debitAmount : '';
                let creditValue = creditAmount > 0 ? creditAmount : '';

                let html = `
        <tr id="entryRow${entryCount}">
            <td>${entryCount}</td>
            <td>
                <input type="hidden" name="entry_date[]" value="${entryDateValue}"> <!-- Add hidden input for date -->
                <span>${entryDateValue}</span> <!-- Display the date -->
            </td>
            <td>
                <input type="hidden" name="entry_account_title[]" value="${accountIdValue}">
                <span>${accountTitleValue}</span>
            </td>
            <td>
                <input type="hidden" name="entry_debit[]" class="entry-debit" value="${debitValue}">
                <span>${debitValue}</span>
            </td>
            <td>
                <input type="hidden" name="entry_credit[]" class="entry-credit" value="${creditValue}">
                <span>${creditValue}</span>
            </td>
            <td><button type="button" class="btn btn-danger remove-entry">Remove</button></td>
        </tr>
    `;

                entryTableBody.insertAdjacentHTML('beforeend', html);

                // Disable the date field after the first entry is added
                if (entryCount === 1) {
                    entryDate.disabled = true;
                }

                // Reset fields and re-enable both input fields
                accountTitle.value = '';
                debitAmountField.value = '';
                creditAmountField.value = '';
                debitAmountField.disabled = false;
                creditAmountField.disabled = false;

                calculateTotal();
            });


            // Disable one field if the other is filled
            debitAmountField.addEventListener('input', function() {
                if (debitAmountField.value) {
                    creditAmountField.disabled = true;
                } else {
                    creditAmountField.disabled = false;
                }
            });

            creditAmountField.addEventListener('input', function() {
                if (creditAmountField.value) {
                    debitAmountField.disabled = true;
                } else {
                    debitAmountField.disabled = false;
                }
            });

            // Event delegation to handle the removal of entries
            entryTableBody.addEventListener('click', function(event) {
                if (event.target && event.target.classList.contains('remove-entry')) {
                    let row = event.target.closest('tr');
                    row.remove();
                    calculateTotal();
                    entryCount--; 
                    if (entryCount === 0) {
                        entryDate.disabled = false;
                    }
                }
            });

            
            submitVoucher.addEventListener('click', function(event) {
                let allEntriesValid = true;
                let debitTotal = parseFloat(debitTotalField.value) || 0;
                let creditTotal = parseFloat(creditTotalField.value) || 0;

                // Check that there is at least one entry
                if (entryCount === 0) {
                    alert('Please add at least one entry before submitting the voucher.');
                    event.preventDefault();
                    return;
                }

                // Check that all fields are filled properly (either debit or credit)
                document.querySelectorAll('tr[id^="entryRow"]').forEach(function(row) {
                    let debitValue = row.querySelector('.entry-debit').value;
                    let creditValue = row.querySelector('.entry-credit').value;

                    if (!debitValue && !creditValue) {
                        allEntriesValid = false;
                    }
                });

                if (!allEntriesValid) {
                    alert('Please ensure that all entries have valid debit or credit values.');
                    event.preventDefault(); // Stop form submission
                    return;
                }

                // Check that total debit equals total credit
                if (debitTotal !== creditTotal) {
                    alert('The total debit and credit amounts must be equal.');
                    event.preventDefault(); // Stop form submission
                    return;
                }
            });
        });
    </script>
@endsection
