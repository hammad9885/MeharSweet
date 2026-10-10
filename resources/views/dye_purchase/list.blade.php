@extends('layouts.condition')
@section('content')
    <div class="container-fluid">
        <!-- Start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Dye Purchase</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Dye Purchase</h4>
                </div>
            </div>
        </div>
        <!-- End page title -->

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
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <form id="voucherForm" action="{{ route('dye_purchase.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <meta name="csrf-token" content="{{ csrf_token() }}">

                                        <div class="col-6">
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="DPN" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number"
                                                required>
                                            
                                            <div class="mb-3">
                                                <label for="entryDate" class="form-label">Date</label>
                                                <input type="date" id="entryDate" class="form-control" name="date">
                                            </div>

                                            <div class="mb-3">
                                                <label for="preparedBy" class="form-label">Prepared By</label>
                                                <input type="text" id="preparedBy" class="form-control"
                                                    value="{{$loggedInUser->name}}" name="prepared_by" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Party</label>
                                                <select name="account" class="form-control select2" data-toggle="select2"
                                                    id="entryParty" required>
                                                    <option value="">Select</option>
                                                    @foreach ($accounts->whereIn('level2_id', [4, 23]) as $accountSupplie)
                                                        <option value="{{ $accountSupplie->id }}">
                                                            {{ $accountSupplie->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="itemTitle" class="form-label">Item Title</label>
                                                <select name="item" class="form-control select2" data-toggle="select2"
                                                    id="itemTitle" required>
                                                    <option value="">Select</option>
                                                    @foreach ($items as $item)
                                                        @if ($item->type_id == 16)
                                                            <option value="{{ $item->id }}" data-purchase="{{ $item->purchase }}">
                                                                {{ $item->item_code }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            
                                             <div class="mb-3">
                                                <label for="qty" class="form-label">Qty</label>
                                                <input type="number" id="qty" class="form-control" name="qty" step="any">
                                            </div>
                                            

                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea type="text" id="description" class="form-control" name="description"></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="amount" class="form-label">Amount</label>
                                                <input type="number" id="amount" class="form-control" name="amount" step="any">
                                            </div>

                                            <div class="mb-3">
                                                <label for="uploadFile" class="form-label">Upload File</label>
                                                <input type="file" id="uploadFile" class="form-control" name="file" accept="image/*">
                                                <div id="filePreviewContainer" class="mt-2" style="display:none;">
                                                    <img id="imagePreview" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px; display:none;">
                                                    <span id="fileNamePreview" style="font-size:14px;"></span>
                                                    <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
                                                </div>
                                            </div>

                                            <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
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
                                                        <th>Supplier</th>
                                                        <th>Item</th>
                                                        <th>Qty</th>
                                                        <th>Description</th>
                                                        <th>Amount</th>
                                                        <th>Img</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="entriesBody">
                                                    <!-- Entries will appear here -->
                                                </tbody>
                                            </table>
                                        </div>

                                    </form>
                                </div>
                                <!-- End row-->
                            </div> <!-- End preview-->
                        </div> <!-- End tab-content-->
                    </div> <!-- End card-body -->
                </div> <!-- End card -->
            </div><!-- End col -->
        </div><!-- End row -->
    </div>
<script>
const today = new Date().toISOString().split('T')[0];
document.getElementById('entryDate').value = today;

document.addEventListener('DOMContentLoaded', function() {
    const entriesTable = document.getElementById('entriesBody');
    const addEntryButton = document.getElementById('addEntry');
    const invoiceInput = document.getElementById('invoice');
    const entryDateInput = document.getElementById('entryDate');
    const fileInput = document.getElementById('uploadFile');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileNamePreview = document.getElementById('fileNamePreview');
    const removeFileButton = document.getElementById('removeFile');
    
    let invoiceCounter = 1;
    invoiceInput.value = invoiceCounter;

    // Initialize select2
    $('.select2').select2();

    // Set amount when item is selected
    $('#itemTitle').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const purchaseAmount = selectedOption.data('purchase');
        if (purchaseAmount) {
            $('#amount').val(purchaseAmount);
        } else {
            $('#amount').val('');
        }
    });

    // File upload preview - Integrated from first script
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                fileNamePreview.textContent = file.name;
                filePreviewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove file - Integrated from first script
    removeFileButton.addEventListener('click', function() {
        fileInput.value = '';
        filePreviewContainer.style.display = 'none';
        imagePreview.src = '';
        fileNamePreview.textContent = '';
    });

    addEntryButton.addEventListener('click', function() {
        const date = entryDateInput.value;
        const preparedBy = document.getElementById('preparedBy').value;
        const description = document.getElementById('description').value;
        const amount = parseFloat(document.getElementById('amount').value);
        const qty = parseFloat(document.getElementById('qty').value);
        
        const itemSelect = document.getElementById('itemTitle');
        const selectedItem = itemSelect.options[itemSelect.selectedIndex];
        const itemTitleValue = selectedItem.text;
        const itemIdValue = selectedItem.value;
        
        const supplierSelect = document.getElementById('entryParty');
        const selectedSupplier = supplierSelect.options[supplierSelect.selectedIndex];
        const supplierTitleValue = selectedSupplier.text;
        const supplierIdValue = selectedSupplier.value;
        
        const file = fileInput.files[0];

        if (!date || !supplierIdValue || !itemIdValue || isNaN(amount) || !file || isNaN(qty)) {
            alert('Please fill all required fields with valid values and upload a file.');
            return;
        }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${invoiceCounter}</td>
            <td>${date}</td>
            <td>${supplierTitleValue}</td>
            <td>${itemTitleValue}</td>
            <td>${qty}</td>
            <td>${description}</td>
            <td>${amount.toFixed(2)}</td>
            <td><img src="${URL.createObjectURL(file)}" alt="Image Preview" style="max-width: 50px; max-height: 50px;"></td>
            <td>
                <button type="button" class="btn btn-danger delete-entry">Delete</button>
                <input type="hidden" name="entries[${invoiceCounter}][date]" value="${date}">
                <input type="hidden" name="entries[${invoiceCounter}][account]" value="${supplierIdValue}">
                <input type="hidden" name="entries[${invoiceCounter}][item]" value="${itemIdValue}">
                <input type="hidden" name="entries[${invoiceCounter}][prepared_by]" value="${preparedBy}">
                <input type="hidden" name="entries[${invoiceCounter}][description]" value="${description}">
                <input type="hidden" name="entries[${invoiceCounter}][amount]" value="${amount.toFixed(2)}">
                <input type="hidden" name="entries[${invoiceCounter}][qty]" value="${qty}">
                <input type="hidden" name="entries[${invoiceCounter}][file_name]" value="${file.name}">
            </td>
        `;

        // Append the file input to the form but keep it hidden
        const fileInputClone = fileInput.cloneNode(true);
        fileInputClone.name = `entries[${invoiceCounter}][file]`;
        fileInputClone.style.display = 'none';
        newRow.appendChild(fileInputClone);

        entriesTable.appendChild(newRow);
        invoiceCounter++;
        invoiceInput.value = invoiceCounter;

        // Reset form fields after adding entry
        document.getElementById('description').value = '';
        document.getElementById('amount').value = '';
        fileInput.value = '';
        filePreviewContainer.style.display = 'none';
        imagePreview.src = '';
        fileNamePreview.textContent = '';

        // Add delete functionality
        newRow.querySelector('.delete-entry').addEventListener('click', function() {
            entriesTable.removeChild(newRow);
        });
    });
});
</script>
@endsection