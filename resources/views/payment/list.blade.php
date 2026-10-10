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
                            <li class="breadcrumb-item active">Form Elements</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Cash Payment</h4>
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




                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">



                                    <form id="voucherForm" action="{{ route('payment.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        <div class="col-xl-6">

                                            @csrf
                                            <!-- Image Upload Field -->
                                            <!-- Upload Image Section -->

                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="CPV" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number"
                                                required>

                                            <!-- Add a hidden input for total amount -->
                                            <input type="hidden" id="totalAmount" name="total_amount" value="0">
                                            <!-- Hidden inputs for locked values -->
                                            <input type="hidden" id="lockedDateInput" name="locked_date" value="">
                                            <input type="hidden" id="lockedCashInput" name="locked_cash" value="">

                                            <!-- Other fields for voucher entry -->
                                            <div class="mb-3">
                                                <label for="entryDate" class="form-label">Date</label>
                                                <input type="date" id="entryDate" class="form-control"
                                                    name="voucher_date" required>
                                            </div>
                                            {{-- <div class="mb-3">
                                            <label for="entryCash" class="form-label">Cash</label>
                                            <input type="text" id="entryCash" class="form-control">
                                        </div> --}}
                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Cash</label>
                                                <select name="account" class="form-control select2" data-toggle="select2"
                                                    id="entryCash">
                                                    <option value="">Select</option>
                                                    @foreach ($accountMasters as $account)
                                                        <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Account Title</label>
                                                <select name="account" id="entryParty" class="form-control select2"
                                                    data-toggle="select2">
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
                                            <button type="button" id="addEntry" class="btn show-btn">Add
                                                Entry</button>
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
                                    {{-- <h1 id="totalAmount">Total Amount: 0.00</h1> --}}






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

        // Set the value of the input field to the current date
        document.getElementById('entryDate').value = today;
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('uploadFile');
            const imagePreview = document.getElementById('imagePreview');
            const fileNamePreview = document.getElementById('fileNamePreview');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const removeFileButton = document.getElementById('removeFile');

            // Handle file preview
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const fileType = file.type;
                    if (fileType.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block'; // Show the image preview
                            fileNamePreview.style.display = 'none'; // Hide the file name for images
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.style.display = 'none'; // Hide image preview
                        fileNamePreview.textContent = file.name; // Display file name
                        fileNamePreview.style.display = 'block'; // Show the file name
                    }
                    filePreviewContainer.style.display = 'block'; // Show the preview container
                }
            });

            // Remove file preview
            removeFileButton.addEventListener('click', function() {
                fileInput.value = ''; // Clear the input field
                imagePreview.src = ''; // Clear the image preview
                fileNamePreview.textContent = ''; // Clear the file name preview
                filePreviewContainer.style.display = 'none'; // Hide the preview container
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const entriesTable = document.getElementById('entriesBody');
            const addEntryButton = document.getElementById('addEntry');
            const totalAmountInput = document.getElementById('totalAmount');
            const totalAmountDisplay = document.getElementById('totalAmountDisplay');
            const invoiceInput = document.getElementById('invoice'); // Hidden invoice input field
            let invoiceCounter = 1; // Start the counter at 1
            let totalAmount = 0; // Initialize total amount to 0
            let lockedDate = null;
            let lockedCashValue = null;
            let lockedCashText = null;

            // Set the initial invoice number to the hidden input
            invoiceInput.value = invoiceCounter;

            // Add entry to the table
            addEntryButton.addEventListener('click', function() {
                const date = document.getElementById('entryDate').value;
                const cash = document.getElementById('entryCash');
                let selectedCash = cash.options[cash.selectedIndex];
                const selectedCashOption = selectedCash.text
                const selectedCashBank = selectedCash.value
                const party = document.getElementById('entryParty');
                let selectedOption = party.options[party.selectedIndex];
                const selectedParty = selectedOption.text
                const selectedAccountParty = selectedOption.value
                const descriptionField = document.getElementById('entryDescription');
                const selectedDescription = descriptionField.options[descriptionField.selectedIndex].text;
                const description = selectedDescription;
                const amount = parseFloat(document.getElementById('entryAmount')
                    .value); // Parse amount as float
                const invoiceType = document.getElementById('invoice_type').value;

                // Use locked values if fields are disabled
                const finalDate = document.getElementById('entryDate').disabled ? lockedDate : date;
                const finalCashValue = document.getElementById('entryCash').disabled ? lockedCashValue :
                    selectedCashBank;
                const finalCashText = document.getElementById('entryCash').disabled ? lockedCashText :
                    selectedCashOption;

                // Ensure all fields are filled before adding the entry
                if (!finalDate || isNaN(amount)) {
                    alert('Please fill all fields.');
                    return;
                }
                if (!finalCashValue) {
                    alert('Cash Is Required');
                    return;
                }
                if (!party.value) {
                    alert('Account Is Required');
                    return;
                }
                // Automatically set the invoice number for each entry
                const invoiceNumber = invoiceCounter++;

                // Update the hidden invoice number field
                invoiceInput.value = invoiceNumber;

                // Create a new row for the entry
                const newRow = document.createElement('tr');

                // Add columns for the new row
                newRow.innerHTML = `
                    <td>${invoiceNumber}</td>
                    <td class='entry-date'>${finalDate}</td>
                    <td>${finalCashText}</td>
                    <td>${selectedParty}</td>
                    <td>${description}</td>
                    <td>${amount.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-danger delete-entry" onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button>
                        <input type="hidden" name="entries[${Date.now()}][date]" value="${finalDate}">
                        <input type="hidden" name="entries[${Date.now()}][cash]" value="${finalCashValue}">
                        <input type="hidden" name="entries[${Date.now()}][account]" value="${selectedAccountParty}">
                        <input type="hidden" name="entries[${Date.now()}][description]" value="${description}">
                        <input type="hidden" name="entries[${Date.now()}][debit]" value="${amount.toFixed(2)}">

                        <input type="hidden" name="entries[${Date.now()}][v_type]" value="${invoiceType}">
                    </td>
                `;

                // Append the new row to the table
                entriesTable.appendChild(newRow);

                // Store locked values and disable fields after first entry
                if (!lockedDate) {
                    lockedDate = date;
                    lockedCashValue = selectedCashBank;
                    lockedCashText = selectedCashOption;
                    // Store in hidden inputs for form submission
                    document.getElementById('lockedDateInput').value = date;
                    document.getElementById('lockedCashInput').value = selectedCashBank;
                }
                document.getElementById('entryDate').disabled = true;
                document.getElementById('entryCash').disabled = true;

                // Add amount to the total
                totalAmount += amount;
                totalAmountDisplay.textContent = totalAmount.toFixed(2);
                totalAmountInput.value = totalAmount.toFixed(2);


                // Clear input fields after adding the entry (except locked ones)
                if (!document.getElementById('entryCash').disabled) {
                    document.getElementById('entryCash').value = '';
                }
                // Always clear Account Title, Description, and Amount
                $('#entryParty').val('').trigger('change'); // Clear Select2 dropdown
                $('#entryDescription').val('').trigger('change'); // Clear Select2 dropdown
                document.getElementById('entryAmount').value = '';

                // Delete entry functionality
                newRow.querySelector('.delete-entry').addEventListener('click', function() {
                    newRow.remove();

                    // Subtract amount from total when deleting
                    totalAmount -= amount;
                    totalAmountDisplay.textContent = totalAmount.toFixed(2);
                    totalAmountInput.value = totalAmount.toFixed(2);

                    // Re-enable date and cash fields if no entries remain
                    if (entriesTable.children.length === 0) {
                        document.getElementById('entryDate').disabled = false;
                        document.getElementById('entryCash').disabled = false;
                        document.getElementById('lockedDateInput').value = '';
                        document.getElementById('lockedCashInput').value = '';
                        lockedDate = null;
                        lockedCashValue = null;
                        lockedCashText = null;
                    }
                });
            });
            // Update all rows' dates when voucher date changes
            entryDate.addEventListener('change', function() {
                const newDate = this.value;
                document.querySelectorAll('#entriesBody .entry-date').forEach(cell => {
                    cell.textContent = newDate;
                });
            });

            // Form submission handler
            document.getElementById('voucherForm').addEventListener('submit', function(e) {
                // Re-enable disabled fields temporarily for submission
                const dateField = document.getElementById('entryDate');
                const cashField = document.getElementById('entryCash');

                if (dateField.disabled) {
                    dateField.disabled = false;
                }
                if (cashField.disabled) {
                    cashField.disabled = false;
                }
            });
        })
    </script>
@endsection
