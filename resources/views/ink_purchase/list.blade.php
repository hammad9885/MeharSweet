@extends('layouts.app')
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
                            <li class="breadcrumb-item active">Ink Purchase</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Ink Purchase</h4>
                </div>
            </div>
        </div>
        <!-- End page title -->

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
                                    <form id="voucherForm" action="{{ route('ink_purchase.store') }}" method="POST">
                                        @csrf
                                        <div class="col-6">
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="GPN" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number"
                                                required>
                                            <input type="hidden" id="totalAmount" name="total_amount" value="0">
                                            <input type="hidden" id="totalWeight" name="total_weight" value="0">
                                            <input type="hidden" id="entryCash" class="form-control" name="cash"
                                                value="{{ $purchaseAccount ? $purchaseAccount->id : '' }}">

                                            <div class="mb-3">
                                                <label for="entryDate" class="form-label">Date</label>
                                                <input type="date" id="entryDate" class="form-control" name="date">
                                            </div>

                                            <div class="mb-3">
                                                <label for="preparedBy" class="form-label">Prepared By</label>
                                                <input type="text" id="preparedBy" class="form-control"
                                                    value="{{ $loggedInUser->name }}" name="prepared_by" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Supplier</label>
                                                <select name="account" class="form-control select2" data-toggle="select2"
                                                    id="entryParty" required>
                                                    <option value="">Select</option>
                                                    @foreach ($accountSuppliers->whereIn('level2_id', [4, 23]) as $accountSupplier)
    <option value="{{ $accountSupplier->id }}">
        {{ $accountSupplier->title }}
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
                                                    @if ($item->type_id == 6)
                                                        <option value="{{ $item->id }}">{{ $item->item_code }}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>



                                            <div class="mb-3">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" id="quantity" class="form-control" name="quantity">
                                            </div>

                                            <div class="mb-3">
                                                <label for="rate" class="form-label">Rate</label>
                                                <input type="number" id="rate" class="form-control" name="rate">
                                            </div>

                                            <div class="mb-3">
                                                <label for="freight" class="form-label">Freight</label>
                                                <input type="number" id="freight" class="form-control" name="freight" value="0" readonly>
                                            </div>
                                            
                                            <button type="button" id="addEntry" class="btn btn-primary">Add
                                                Entry</button>
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

                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>Freight</th>
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

        // Set the value of the input field to the current date
        document.getElementById('entryDate').value = today;

        document.addEventListener('DOMContentLoaded', function() {
    const entriesTable = document.getElementById('entriesBody');
    const addEntryButton = document.getElementById('addEntry');
    const invoiceInput = document.getElementById('invoice');
    const entryDateInput = document.getElementById('entryDate');
    let invoiceCounter = 1;

    invoiceInput.value = invoiceCounter;

    addEntryButton.addEventListener('click', function() {
        const date = entryDateInput.value;
        const quantity = document.getElementById('quantity').value;
        const rate = document.getElementById('rate').value;
        const freight = document.getElementById('freight').value;
        const cash = document.getElementById('entryCash').value;
        const prepared = document.getElementById('preparedBy').value;
        const item = document.getElementById('itemTitle');
        const selectedItem = item.options[item.selectedIndex];
        const itemTitleValue = selectedItem.text;
        const itemIdValue = selectedItem.value;
        const supplier = document.getElementById('entryParty');
        const selectedSupplier = supplier.options[supplier.selectedIndex];
        const supplierTitleValue = selectedSupplier.text;
        const supplierIdValue = selectedSupplier.value;
        const amount = quantity * rate;

        if (!date || !quantity || !rate || isNaN(amount)) {
            alert('Please fill all fields.');
            return;
        }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${invoiceCounter}</td>
            <td>${date}</td>
            <td>${supplierTitleValue}</td>
            <td>${itemTitleValue}</td>
            <td>${quantity}</td>
            <td>${rate}</td>
            <td>${Math.round(amount)}</td>
            <td>${freight}</td>
            <td>
                <button type="button" class="btn btn-danger delete-entry">Delete</button>
                <input type="hidden" name="entries[${Date.now()}][date]" value="${date}">
                <input type="hidden" name="entries[${Date.now()}][cash]" value="${cash}">
                <input type="hidden" name="entries[${Date.now()}][supplier]" value="${supplierIdValue}">
                <input type="hidden" name="entries[${Date.now()}][item]" value="${itemIdValue}">
                <input type="hidden" name="entries[${Date.now()}][prepared_by]" value="${prepared}">
                <input type="hidden" name="entries[${Date.now()}][quantity]" value="${quantity}">
                <input type="hidden" name="entries[${Date.now()}][rate]" value="${rate}">
                <input type="hidden" name="entries[${Date.now()}][freight]" value="${freight}">
                <input type="hidden" name="entries[${Date.now()}][amount]" value="${Math.round(amount)}">
            </td>
        `;

        entriesTable.appendChild(newRow);
        invoiceCounter++;
        document.getElementById('totalAmount').value = parseFloat(document.getElementById('totalAmount').value) + amount;

        // Disable the date input after adding an entry
        entryDateInput.disabled = true;

        // Reset input fields
        document.getElementById('quantity').value = '';
        document.getElementById('freight').value = '0';

        // Add delete functionality to the newly created delete button
        const deleteButton = newRow.querySelector('.delete-entry');
        deleteButton.addEventListener('click', function() {
            const rowAmount = parseFloat(newRow.children[6].innerText);
            document.getElementById('totalAmount').value = parseFloat(document.getElementById('totalAmount').value) - rowAmount;
            entriesTable.removeChild(newRow);

            // Re-enable the date input if no entries remain
            if (entriesTable.children.length === 0) {
                entryDateInput.disabled = false;
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
        // Initialize select2 after DOM is fully loaded
        $('.select2').select2();

        // Listen for change event on the item dropdown
        $('#itemTitle').on('change', function() {
            // Get the selected value (item ID)
            var selectedItemId = $(this).val();
            
            // Log the selected value to the console
            console.log("Selected Item ID:", selectedItemId);

            // Perform AJAX request to fetch purchase details for the selected item
            if (selectedItemId) {
                $.ajax({
                    url: '/get-item-details/' + selectedItemId,
                    method: 'GET',
                    success: function(response) {
                        // Log the response to the console
                        console.log("Item Details:", response);

                        // Set the rate input field with the purchase value (if exists)
                        if (response && response.purchase) {
                            $('#rate').val(response.purchase);
                        } else {
                            console.log("Purchase (rate) not found in the response.");
                        }

                        // Set the gramage input field with the gramage value (if exists)
                        if (response && response.gramage) {
                            // Assuming you have a field for gramage, set its value here
                            $('#gramage').val(response.gramage);  // Replace #gramage with your actual input ID
                        } else {
                            console.log("Gramage not found in the response.");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error("Error fetching item details:", error);
                    }
                });
            } else {
                console.log("No item selected.");
            }
        });
    });

 document.getElementById('freight').addEventListener('change', function() {
    this.disabled = true;
});
    </script>
@endsection
