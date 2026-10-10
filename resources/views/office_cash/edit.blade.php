@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Edit Office Cash</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Office Cash</h4>
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
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <form id="voucherForm" action="{{ route('office_cash.update', $voucher->first()->v_no) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="col-xl-6">
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type" value="OC" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number" required>

                                            <!-- Total Amount -->
                                            <input type="hidden" id="totalAmount" name="total_amount">

                                            <!-- Date -->
                                            <div class="mb-3">
                                                <label for="entryDate" class="form-label">Date</label>
                                                <input type="date" id="entryDate" class="form-control" name="date">
                                            </div>

                                            <!-- Cash Account -->
                                           <div class="mb-3">
    <label for="entryParty" class="form-label">Cash</label>
    <select name="account" class="form-control select2" data-toggle="select2" id="entryCash" disabled>
        <option value="">Select</option>
        @foreach ($accountMasters as $account)
            <option value="{{ $account->id }}" {{ $account->id == 126 ? 'selected' : '' }}>
                {{ $account->title }}
            </option>
        @endforeach
    </select>
</div>

                                            <!-- Account Title -->
                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Account Title</label>
                                                <select name="account_id" id="entryParty" class="form-control select2" data-toggle="select2">
                                                    <option value="">Select</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Description -->
                                            <div class="mb-3">
                                                <label for="entryDescription" class="form-label">Description</label>
                                                <textarea id="entryDescription" class="form-control" name="description"></textarea>
                                            </div>

                                            <!-- Amount -->
                                            <div class="mb-3">
                                                <label for="entryAmount" class="form-label">Amount</label>
                                                <input type="number" id="entryAmount" class="form-control" name="amount" step="any">
                                            </div>

                                            <!-- File Upload -->
                                            <div class="mb-3">
                                                <label for="uploadFile" class="form-label">Upload File</label>
                                                <input type="file" id="uploadFile" class="form-control" name="file">
                                                <div id="filePreviewContainer" class="mt-2" style="display:none;">
                                                    <img id="imagePreview" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px; display:none;">
                                                    <span id="fileNamePreview" style="font-size:14px;"></span>
                                                    <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
                                                </div>
                                            </div>

                                            <button type="submit" id="addEntry" class="btn btn-primary">Add Entry</button>
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
                                                    @if ($voucher && count($voucher) > 0)
                                                    @foreach ($voucher as $entry)
                                                        <!-- Loop through all entries in the $voucher collection -->
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $entry->date }}</td>
                                                            <td>{{ $entry->cashes->title ?? 'N/A' }}</td> <!-- Adjust based on your relationships -->
                                                            <td>{{ $entry->accounts->title ?? 'N/A' }}</td> <!-- Adjust based on your relationships -->
                                                            <td>{{ $entry->description }}</td>
                                                            <td>{{ $entry->debit }}</td> <!-- Change this to the correct amount field, e.g. debit or credit -->
                                                            <td>
                                                                <!-- Delete Entry Button -->
                                                                <a href="{{ route('office_cash.destroy', $entry->id) }}"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="event.preventDefault();
                                                                    if(confirm('Are you sure you want to delete this transaction?')) {
                                                                        window.location.href='{{ route('office_cash.destroy', $entry->id) }}';
                                                                    }">Delete</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <p>No entries found for this voucher.</p>
                                                @endif

                                                </tbody>
                                            </table>
                                        </div>

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
        const today = new Date().toISOString().split('T')[0];

        // Set the value of the input field to the current date
        document.getElementById('entryDate').value = today;
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('uploadFile');
            const imagePreview = document.getElementById('imagePreview');
            const fileNamePreview = document.getElementById('fileNamePreview');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const removeFileButton = document.getElementById('removeFile');

            // Handle file preview
            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const fileType = file.type;
                    if (fileType.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';  // Show the image preview
                            fileNamePreview.style.display = 'none'; // Hide the file name for images
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.style.display = 'none';  // Hide image preview
                        fileNamePreview.textContent = file.name; // Display file name
                        fileNamePreview.style.display = 'block';  // Show the file name
                    }
                    filePreviewContainer.style.display = 'block'; // Show the preview container
                }
            });

            // Remove file preview
            removeFileButton.addEventListener('click', function () {
                fileInput.value = '';  // Clear the input field
                imagePreview.src = ''; // Clear the image preview
                fileNamePreview.textContent = '';  // Clear the file name preview
                filePreviewContainer.style.display = 'none';  // Hide the preview container
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
    const addEntryButton = document.getElementById('addEntry');
    const entriesTable = document.getElementById('entriesBody');
    let totalAmount = 0;

    // Function to update total amount
    function updateTotalAmount() {
        document.getElementById('totalAmount').value = totalAmount;
    }

    // Add entry event
    addEntryButton.addEventListener('click', function () {
        const date = document.getElementById('entryDate').value;
        const cashId = document.getElementById('entryCash').value;
        const accountId = document.getElementById('entryParty').value;
        const description = document.getElementById('entryDescription').value;
        const amount = parseFloat(document.getElementById('entryAmount').value);

        if (!date || !cashId || !accountId || !description || isNaN(amount)) {
            alert('Please fill in all fields.');
            return;
        }

        // Add new entry to the table
        const newRow = entriesTable.insertRow();
        newRow.innerHTML = `
            <td>${entriesTable.rows.length}</td>
            <td>${date}</td>
            <td>${document.getElementById('entryCash').options[document.getElementById('entryCash').selectedIndex].text}</td>
            <td>${document.getElementById('entryParty').options[document.getElementById('entryParty').selectedIndex].text}</td>
            <td>${description}</td>
            <td>${amount.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove-entry">Delete</button></td>
            <td>
                <input type="hidden" name="entries[${Date.now()}][date]" value="${date}">
                <input type="hidden" name="entries[${Date.now()}][cash]" value="${cashId}">
                <input type="hidden" name="entries[${Date.now()}][account]" value="${accountId}">
                <input type="hidden" name="entries[${Date.now()}][description]" value="${description}">
                <input type="hidden" name="entries[${Date.now()}][debit]" value="${amount.toFixed(2)}">
            </td>

        `;

        totalAmount += amount;
        updateTotalAmount();

       
        document.getElementById('entryDescription').value = '';
        document.getElementById('entryAmount').value = '';

        // Remove entry functionality
        newRow.querySelector('.remove-entry').addEventListener('click', function () {
            const amountToRemove = amount;
            totalAmount -= amountToRemove;
            updateTotalAmount();
            newRow.remove();

            // Update Sr No for each remaining entry
            Array.from(entriesTable.rows).forEach((r, index) => {
                r.cells[0].textContent = index + 1;
            });
        });
    });
});

    </script>
@endsection
