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
                    <h4 class="page-title">Bank Receipt</h4>

                </div>
                <h4 class="page-title">Bank Receipt</h4>
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <form id="voucherForm" action="{{ route('bank_recipt.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" id="invoice_type" name="v_type" value="BPV" required readonly>
                            <input type="hidden" id="invoice" name="invoice_number" required>
                            <input type="hidden" id="totalAmount" name="total_amount" value="0">

                            <div class="col-xl-6">
                                <!-- Voucher Date -->
                                <div class="mb-3">
                                    <label for="entryDate" class="form-label">Date</label>
                                    <input type="date" id="entryDate" name="voucher_date" class="form-control">
                                </div>

                                <!-- Bank -->
                                <div class="mb-3">
                                    <label for="entryCash" class="form-label">Bank</label>
                                    <select name="account" class="form-control select2" data-toggle="select2" id="entryCash">
                                        <option value="">Select</option>
                                        @foreach ($accountMasters as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Account Title -->
                                <div class="mb-3">
                                    <label for="mainAccountTitle" class="form-label">Account Title</label>
                                    <select name="main_account" id="mainAccountTitle" class="form-control select2" data-toggle="select2">
                                        <option value="">Select</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="entryDescription" class="form-label">Description</label>
                                    <select id="entryDescription" class="form-control select2" data-toggle="select2">
                                        <option value="">Select</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}{{ isset($customerUrduTitles[$account->id]) ? ' - ' . $customerUrduTitles[$account->id] : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div class="mb-3">
                                    <label for="entryAmount" class="form-label">Amount</label>
                                    <input type="number" id="entryAmount" class="form-control">
                                </div>

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="uploadFile" class="form-label">Upload File</label>
                                    <input type="file" id="uploadFile" class="form-control" name="file">
                                    <div id="filePreviewContainer" class="mt-2" style="display:none;">
                                        <img id="imagePreview" src="" alt="Image Preview"
                                             style="max-width: 150px; max-height: 150px; display:none;">
                                        <span id="fileNamePreview" style="font-size:14px;"></span>
                                        <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
                                    </div>
                                </div>

                                <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
                                <button type="submit" class="btn btn-success">Submit Voucher</button>
                            </div>

                            <!-- Invoice Display -->
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
                                    <!-- Rows will be added here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Total -->
                            <h4 class="text-end">Total Amount: <span id="totalAmountDisplay">0</span></h4>
                        </form>

                    </div>
                </div>
            </div>
        </div>


                </div>
            </div>
        </div>
    </div>

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

            // Default Date
            const entryDate = document.getElementById('entryDate');
            if (!entryDate.value) {
                entryDate.value = new Date().toISOString().split('T')[0];
            }

            // File preview
            const fileInput = document.getElementById('uploadFile');
            const imagePreview = document.getElementById('imagePreview');
            const fileNamePreview = document.getElementById('fileNamePreview');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const removeFileButton = document.getElementById('removeFile');

            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
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
            removeFileButton.addEventListener('click', function () {
                fileInput.value = '';
                imagePreview.src = '';
                fileNamePreview.textContent = '';
                filePreviewContainer.style.display = 'none';
            });

            // Entries logic
            const entriesTable = document.getElementById('entriesBody');
            const addEntryButton = document.getElementById('addEntry');
            const totalAmountInput = document.getElementById('totalAmount');
            const totalAmountDisplay = document.getElementById('totalAmountDisplay');
            const invoiceInput = document.getElementById('invoice');
            let invoiceCounter = 1;
            let totalAmount = 0;

            invoiceInput.value = invoiceCounter;

            addEntryButton.addEventListener('click', function () {
                const date = entryDate.value;
                const cash = document.getElementById('entryCash');
                const mainAccount = document.getElementById('mainAccountTitle');
                const descriptionFld = document.getElementById('entryDescription');
                const description = descriptionFld.options[descriptionFld.selectedIndex].text;
                const amount = parseFloat(document.getElementById('entryAmount').value);
                const invoiceType = document.getElementById('invoice_type').value;

                if (!date || isNaN(amount)) {
                    alert('Please fill all fields.');
                    return;
                }
                if (!cash.value) {
                    alert('Bank is required');
                    return;
                }
                if (!mainAccount.value) {
                    alert('Account Title is required');
                    return;
                }

                const selectedCashOption = cash.options[cash.selectedIndex].text;
                const selectedCashBank = cash.value;
                const selectedParty = mainAccount.options[mainAccount.selectedIndex].text;
                const selectedAccountParty = mainAccount.value;

                const invoiceNumber = invoiceCounter++;
                invoiceInput.value = invoiceNumber;
                const uniqueKey = Date.now();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${invoiceNumber}</td>
                    <td class="entry-date">${date}</td>
                    <td>${selectedCashOption}</td>
                    <td>${selectedParty}</td>
                    <td>${description}</td>
                    <td>${amount.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-danger delete-entry">Delete</button>
                        <input type="hidden" name="entries[${uniqueKey}][cash]" value="${selectedCashBank}">
                        <input type="hidden" name="entries[${uniqueKey}][account]" value="${selectedAccountParty}">
                        <input type="hidden" name="entries[${uniqueKey}][description]" value="${description}">
                        <input type="hidden" name="entries[${uniqueKey}][credit]" value="${amount.toFixed(2)}">
                        <input type="hidden" name="entries[${uniqueKey}][v_type]" value="${invoiceType}">
                    </td>
                `;
                entriesTable.appendChild(newRow);

                totalAmount += amount;
                totalAmountDisplay.textContent = totalAmount.toFixed(2);
                totalAmountInput.value = totalAmount.toFixed(2);

                // Clear input fields (except date/bank/account)
                $('#entryDescription').val('').trigger('change');
                document.getElementById('entryAmount').value = '';

                // Delete row
                newRow.querySelector('.delete-entry').addEventListener('click', function () {
                    newRow.remove();
                    totalAmount -= amount;
                    totalAmountDisplay.textContent = totalAmount.toFixed(2);
                    totalAmountInput.value = totalAmount.toFixed(2);
                });
            });

            // Update all rows' dates when voucher date changes
            entryDate.addEventListener('change', function () {
                const newDate = this.value;
                document.querySelectorAll('#entriesBody .entry-date').forEach(cell => {
                    cell.textContent = newDate;
                });
            });
        });
    </script>

@endsection
