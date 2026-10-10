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
                            <li class="breadcrumb-item active">Wastage Sale</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Wastage Sale</h4>
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
                                    <form id="voucherForm" action="{{ route('wastage_sale.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <meta name="csrf-token" content="{{ csrf_token() }}">

                                        <div class="col-6">
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="WSN" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number"
                                                required>
                                            <input type="hidden" id="totalAmount" name="total_amount" value="0">
                                            <input type="hidden" id="totalWeight" name="total_weight" value="0">
                                            
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
                                                    @foreach ($accounts->where('level2_id', 7) as $accountSupplie)
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
                                                      @if ($item->type_id == 10)
                                                        <option value="{{ $item->id }}">{{ $item->item_code }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                 <textarea type="text" id="description" class="form-control" name="description"></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="weight" class="form-label">Weight</label>
                                                <input type="number" id="weight" class="form-control" name="weight" step="any">
                                            </div>
                                            

                                            <div class="mb-3" hidden>
                                                <label for="rate" class="form-label">Rate</label>
                                                <input type="number" id="rate" class="form-control" name="rate" step="any">
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
                                        <h3 class="mt-4">Invoice <span id="invoiceDisplay"></span></h3>

                                        <!-- Entries Table -->
                                        <div class="col-lg-12">
                                            <table class="table mt-4" id="entriesTable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr No</th>
                                                        <th>Date</th>
                                                        <th>Supplier</th>
                                                        <th>Item</th>
                                                        <th>Description</th>
                                                        <th>weight</th>
                                                        <th>Img</th>
                                                        <th hidden>Rate</th>
                                                        <th hidden>Amount</th>
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
document.addEventListener('DOMContentLoaded', function () {
   const today = new Date();
const offset = today.getTimezoneOffset();
const localDate = new Date(today.getTime() - (offset * 60 * 1000)).toISOString().split('T')[0];
document.getElementById('entryDate').value = localDate;

    const entriesTable = document.getElementById('entriesBody');
    const addEntryButton = document.getElementById('addEntry');
    const invoiceInput = document.getElementById('invoice');
    const entryDateInput = document.getElementById('entryDate');
    const fileInput = document.getElementById('uploadFile');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileNamePreview = document.getElementById('fileNamePreview');
    const removeFileButton = document.getElementById('removeFile');
    const voucherForm = document.getElementById('voucherForm');
    let invoiceCounter = 1;

    invoiceInput.value = invoiceCounter;

    // Handle file input change
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                fileNamePreview.textContent = file.name;
                filePreviewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle file removal
    removeFileButton.addEventListener('click', function () {
        fileInput.value = '';  // Clear the file input
        filePreviewContainer.style.display = 'none';  // Hide the preview container
        imagePreview.src = '';  // Clear the image preview
        fileNamePreview.textContent = '';  // Clear the file name preview
    });

    // Handle adding new entry
    // Handle adding new entry
addEntryButton.addEventListener('click', function () {
    const date = entryDateInput.value;
    const weight = document.getElementById('weight').value;
    const description = document.getElementById('description').value;
    const rate = document.getElementById('rate').value;
    const prepared = document.getElementById('preparedBy').value;
    const item = document.getElementById('itemTitle');
    let selectedOption = item.options[item.selectedIndex];
    let itemTitleValue = selectedOption.text;
    let itemIdValue = selectedOption.value;
    const supplier = document.getElementById('entryParty');
    let selectedSupplier = supplier.options[supplier.selectedIndex];
    let supplierTitleValue = selectedSupplier.text;
    let supplierIdValue = selectedSupplier.value;
    const file = fileInput.files[0];

    const parsedWeight = parseFloat(weight); // Convert to float
const parsedRate = parseFloat(rate); // Convert to float

const total = parsedWeight * parsedRate; // Perform multiplication
const amount = parseFloat(total.toFixed(2)); // Round to 2 decimal places

    // Validate that all required fields are filled and a file is uploaded
    if (!date || !weight || !rate || isNaN(amount) || !file) {
        alert('Please fill all fields correctly and upload an image.');
        return;
    }

    const rowId = Date.now();
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${invoiceCounter}</td>
        <td>${date}</td>
        <td>${supplierTitleValue}</td>
        <td>${itemTitleValue}</td>
        <td>${description}</td>
        <td>${weight}</td>
        <td><img src="${URL.createObjectURL(file)}" alt="Image Preview" style="max-width: 50px; max-height: 50px;"></td>
        <td hidden>${rate}</td>
        <td hidden>${(amount)}</td>
        <td>
            <button type="button" class="btn btn-danger delete-entry">Delete</button>
            <input type="hidden" name="entries[${rowId}][date]" value="${date}">
            <input type="hidden" name="entries[${rowId}][supplier]" value="${supplierIdValue}">
            <input type="hidden" name="entries[${rowId}][item]" value="${itemIdValue}">
            <input type="hidden" name="entries[${rowId}][description]" value="${description}">
            <input type="hidden" name="entries[${rowId}][weight]" value="${weight}">
            <input type="hidden" name="entries[${rowId}][rate]" value="${rate}">
            <input type="hidden" name="entries[${rowId}][amount]" value="${(amount)}">
            <input type="hidden" name="entries[${rowId}][file_name]" value="${file.name}">
        </td>
    `;

    // Append the file input to the form but keep it hidden
    const fileInputClone = fileInput.cloneNode(true);
    fileInputClone.name = `entries[${rowId}][file]`;
    fileInputClone.style.display = 'none';  // Hide the file input
    newRow.appendChild(fileInputClone);

    entriesTable.appendChild(newRow);
    invoiceCounter++;
    invoiceInput.value = invoiceCounter;

    // Reset form fields after adding entry
    document.getElementById('weight').value = '';
    document.getElementById('description').value = '';
    fileInput.value = '';  // Clear the file input
    filePreviewContainer.style.display = 'none';  // Hide the preview container
    imagePreview.src = '';  // Clear the image preview
    fileNamePreview.textContent = '';  // Clear the file name preview
});
    // Delete row functionality
    entriesTable.addEventListener('click', function (event) {
        if (event.target.classList.contains('delete-entry')) {
            event.target.closest('tr').remove();
        }
    });

    // Initialize select2 for dropdowns
    $('.select2').select2();

    // Fetch item details on item change
    $('#itemTitle').on('change', function() {
        const selectedItemId = $(this).val();
        console.log("Selected Item ID:", selectedItemId);

        if (selectedItemId) {
            $.ajax({
                url: '/get-item-details/' + selectedItemId,
                method: 'GET',
                success: function(response) {
                    console.log("Item Details:", response);
                    if (response && response.purchase) {
                        $('#rate').val(response.purchase);
                    } else {
                        console.log("Purchase (rate) not found in the response.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching item details:", error);
                    alert("Failed to fetch item details. Please try again.");
                }
            });
        } else {
            console.log("No item selected.");
        }
    });
});
   
</script>

@endsection