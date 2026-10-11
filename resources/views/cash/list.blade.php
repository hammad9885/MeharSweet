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
                            <li class="breadcrumb-item active">Form Elements</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Cash Receipt</h4>
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
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-none">
                    <div class="card-body">

                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">

                                    <form id="voucherForm" action="{{ route('cash.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        <div class="col-xl-6">

                                            @csrf
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="CRV" required readonly>
                                            <input type="hidden" id="invoice" class="form-control"
                                                name="invoice_number" required>

                                            <!-- Add a hidden input for total amount -->
                                            <input type="hidden" id="totalAmount" name="total_amount" value="0">

                                            <!-- âœ… Voucher date (only once for whole voucher) -->
                                            <div class="mb-3">
                                                <label for="entryDate" class="form-label">Voucher Date</label>
                                                <input type="date" id="entryDate"  name="voucher_date"
                                                    class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label for="entryCash" class="form-label">Cash</label>
                                                <select id="entryCash" class="form-control select2" data-toggle="select2">
                                                    <option value="">Select</option>
                                                    @foreach ($accountMasters as $account)
                                                        <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Account Title</label>
                                                <select id="entryParty" class="form-control select2" data-toggle="select2">
                                                    <option value="">Select</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="entryDescription" class="form-label">Description</label>
                                                <select id="entryDescription" class="form-control select2" data-toggle="select2">
                                                    <option value="">Select</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="entryAmount" class="form-label">Amount</label>
                                                <input type="number" id="entryAmount" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label for="uploadFile" class="form-label">Upload File</label>
                                                <input type="file" id="uploadFile" class="form-control" name="file">
                                                <div id="filePreviewContainer" class="mt-2" style="display:none;">
                                                    <img id="imagePreview" src="" alt="Image Preview"
                                                        style="max-width: 150px; max-height: 150px; display:none;">
                                                    <span id="fileNamePreview" style="font-size:14px;"></span>
                                                    <button type="button" id="removeFile"
                                                        class="btn btn-sm btn-danger">X</button>
                                                </div>
                                            </div>
                                            <button type="button" id="addEntry" class="btn show-btn">Add Entry</button>
                                            <button type="submit" class="btn btn-success">Submit Voucher</button>
                                        </div>

                                        <!-- Display Invoice Number -->
                                        <h3 class="mt-4">Voucher <span id="invoiceDisplay"></span></h3>

                                        <!-- Entries Table -->
                                        <div class="col-lg-12">
                                            <table class="table mt-4" id="entriesTable">
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
                                                    <!-- Entries will appear here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Total Amount Display -->
                                        <h4 class="text-end">Total Amount: <span id="totalAmountDisplay">0</span></h4>

                                    </form>

                                </div>
                                <!-- end row-->
                            </div> <!-- end preview-->
                        </div> <!-- end tab-content-->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

    </div>
    <script>
        (function() {
            function whenReady() {
                if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
                    setTimeout(whenReady, 30);
                    return;
                }
                jQuery(document).ready(function($) {
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
                });
            }
            whenReady();
        })();
    </script>
    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('entryDate').value = today;

        document.addEventListener('DOMContentLoaded', function() {
            const voucherForm = document.getElementById('voucherForm');

            // File preview handling
            const fileInput = document.getElementById('uploadFile');
            const imagePreview = document.getElementById('imagePreview');
            const fileNamePreview = document.getElementById('fileNamePreview');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const removeFileButton = document.getElementById('removeFile');

            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const fileType = file.type;
                    if (fileType.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                            fileNamePreview.style.display = 'none';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.style.display = 'none';
                        fileNamePreview.textContent = file.name;
                        fileNamePreview.style.display = 'block';
                    }
                    filePreviewContainer.style.display = 'block';
                }
            });

            removeFileButton.addEventListener('click', function() {
                fileInput.value = '';
                imagePreview.src = '';
                fileNamePreview.textContent = '';
                filePreviewContainer.style.display = 'none';
            });

            // Entry handling
            const entriesTable = document.getElementById('entriesBody');
            const addEntryButton = document.getElementById('addEntry');
            const totalAmountInput = document.getElementById('totalAmount');
            const totalAmountDisplay = document.getElementById('totalAmountDisplay');
            const invoiceInput = document.getElementById('invoice');
            let invoiceCounter = 1;
            let totalAmount = 0;

            // Track locked fields
            let locked = {
                date: false,
                cash: false
            };
            let lockedDate, lockedCashValue, lockedCashText;
            let lockedDateInput, lockedCashInput;

            invoiceInput.value = invoiceCounter;

            addEntryButton.addEventListener('click', function() {
                console.log("as")
                const dateField = document.getElementById('entryDate');
                const cashField = document.getElementById('entryCash');
                const partyField = document.getElementById('entryParty');
                const descriptionField = document.getElementById('entryDescription');
                const description = descriptionField.options[descriptionField.selectedIndex].text;
                const amount = parseFloat(document.getElementById('entryAmount').value);
                const invoiceType = document.getElementById('invoice_type').value;

                if (!dateField.value || isNaN(amount)) {
                    alert('Please fill all fields.');
                    return;
                }
                if (!cashField.value) {
                    alert('Cash is required.');
                    return;
                }
                if (!partyField.value) {
                    alert('Account is required.');
                    return;
                }

                const selectedCashText = cashField.options[cashField.selectedIndex].text;
                const selectedPartyText = partyField.options[partyField.selectedIndex].text;

                const invoiceNumber = invoiceCounter++;
                invoiceInput.value = invoiceNumber;
                const entryKey = Date.now();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${invoiceNumber}</td>
                    <td class="entry-date">${dateField.value}</td>
                    <td>${selectedCashText}</td>
                    <td>${selectedPartyText}</td>
                    <td>${description}</td>
                    <td>${amount.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-danger delete-entry" onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button>
                        <input type="hidden" name="entries[${entryKey}][date]" value="${dateField.value}">
                        <input type="hidden" name="entries[${entryKey}][cash]" value="${cashField.value}">
                        <input type="hidden" name="entries[${entryKey}][account]" value="${partyField.value}">
                        <input type="hidden" name="entries[${entryKey}][description]" value="${description}">
                        <input type="hidden" name="entries[${entryKey}][credit]" value="${amount.toFixed(2)}">
                        <input type="hidden" name="entries[${entryKey}][v_type]" value="${invoiceType}">
                    </td>
                `;
                entriesTable.appendChild(newRow);

                totalAmount += amount;
                totalAmountDisplay.textContent = totalAmount.toFixed(2);
                totalAmountInput.value = totalAmount.toFixed(2);

                // Lock date and cash fields after first entry
                if (!locked.date) {
                    lockedDate = dateField.value;
                    dateField.disabled = true;
                    lockedDateInput = document.createElement('input');
                    lockedDateInput.type = 'hidden';
                    lockedDateInput.name = 'voucher_date';
                    lockedDateInput.value = lockedDate;
                    lockedDateInput.id = 'lockedDate';
                    voucherForm.appendChild(lockedDateInput);
                    locked.date = true;
                }
                
                if (!locked.cash) {
                    lockedCashValue = cashField.value;
                    lockedCashText = selectedCashText;
                    cashField.disabled = true;
                    lockedCashInput = document.createElement('input');
                    lockedCashInput.type = 'hidden';
                    lockedCashInput.name = 'cash';
                    lockedCashInput.value = lockedCashValue;
                    lockedCashInput.id = 'lockedCash';
                    voucherForm.appendChild(lockedCashInput);
                    locked.cash = true;
                }

                // Clear only account, description, and amount fields
                document.getElementById('entryParty').value = '';
                document.getElementById('entryDescription').value = '';
                document.getElementById('entryAmount').value = '';
                $('#entryParty').trigger('change');
                $('#entryDescription').trigger('change');

                // Delete functionality
                newRow.querySelector('.delete-entry').addEventListener('click', function() {
                    newRow.remove();
                    totalAmount -= amount;
                    totalAmountDisplay.textContent = totalAmount.toFixed(2);
                    totalAmountInput.value = totalAmount.toFixed(2);

                    if (entriesTable.rows.length === 0) {
                        // Unlock date and cash fields if no entries remain
                        dateField.disabled = false;
                        cashField.disabled = false;
                        if (lockedDateInput) lockedDateInput.remove();
                        if (lockedCashInput) lockedCashInput.remove();
                        locked = { date: false, cash: false };
                    }
                });
            });
            // Update all entry dates when voucher date changes (only if not locked)
            document.getElementById('entryDate').addEventListener('change', function () {
                if (!locked.date) return;
                const newDate = this.value;
                lockedDate = newDate;
                if (lockedDateInput) lockedDateInput.value = newDate;
                document.querySelectorAll('#voucherForm .entry-date').forEach(cell => {
                    cell.textContent = newDate;
                });
                document.querySelectorAll('input[name*="[date]"]').forEach(input => {
                    input.value = newDate;
                });
            });
            
            // Re-enable fields before form submission
            voucherForm.addEventListener('submit', function() {
                if (locked.date) dateField.disabled = false;
                if (locked.cash) cashField.disabled = false;
            });
        });
    </script>
@endsection
