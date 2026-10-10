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
                            <li class="breadcrumb-item active">Edit Purchase Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Purchase Return</h4>
                </div>
            </div>
        </div>
        <!-- End page title -->

        <!-- Display any error messages -->
        @if (session('error'))
            <div class="alert alert-danger">
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
                                    <form id="voucherForm" action="{{ route('purchase_return.update', $voucher->first()->v_no) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-6">
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="PIN" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number"
                                                 required>
                                            <input type="hidden" id="totalAmount" name="total_amount">
                                            <input type="hidden" id="totalWeight" name="total_weight">
                                            <input type="hidden" id="entryCash" class="form-control" name="cash" value="{{ $purchaseAccount ? $purchaseAccount->id : '' }}">
                                            <div class="mb-3">
                                                <label>Voucher ID: {{ $v_no }}</label>
                                            </div><hr>
                                            <div class="mb-3">
                                                <label for="entryDate" class="form-label">Date</label>
                                                <input type="date" id="entryDate" class="form-control" name="date">
                                            </div>

                                            <div class="mb-3">
                                                <label for="preparedBy" class="form-label">Prepared By</label>
                                                <input type="text" id="preparedBy" class="form-control"
                                                    name="prepared_by" value="{{$loggedInUser->name}}" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label for="entryParty" class="form-label">Supplier</label>
                                                <select name="account" class="form-control select2" data-toggle="select2"
                                                    id="entryParty" required>
                                                    <option value="">Select</option>
                                                    @foreach  ($accountSuppliers->whereIn('level2_id', [4, 23]) as $accountSupplie)
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
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->item_code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="width" class="form-label">Width</label>
                                                <input type="number" id="width" class="form-control" name="width" step="any">
                                            </div>

                                            <div class="mb-3">
                                                <label for="length" class="form-label">Length</label>
                                                <input type="number" id="length" class="form-control" name="length" step="any">
                                            </div>

                                            <div class="mb-3">
                                                <label for="gramage" class="form-label">Gramage</label>
                                                <input type="number" id="gramage" class="form-control" name="gramage" step="any">
                                            </div>

                                            <div class="mb-3">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" id="quantity" class="form-control" name="quantity" step="any">
                                            </div>

                                            <div class="mb-3">
                                                <label for="rate" class="form-label">Rate</label>
                                                <input type="number" id="rate" class="form-control" name="rate" step="any">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="freight" class="form-label">Freight</label>
                                                <input type="number" id="freight" class="form-control" name="freight" step="any">
                                            </div>
                                            <button type="button" id="addUpdateEntry" class="btn btn-primary">Add Entry</button>
                                            <!--<button type="button" id="addEntry" class="btn btn-primary">Add-->
                                            <!--    Entry</button>-->
                                            <!--<button type="submit" class="btn btn-success">Update Invoice</button>-->
                                        </div>

                                        <!-- Entries Table -->
                                        <div class="col-lg-12">
                                            <table class="table mt-4" id="entriesTable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr No</th>
                                                        <th>Date</th>
                                                        <th>Supplier</th>
                                                        <th>Item</th>
                                                        <th>Width</th>
                                                        <th>Length</th>
                                                        <th>Gramage</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Weight</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="entriesBody">
                                                    @php
                                                        $totalEntries = 0; // Initialize a counter for rows
                                                    @endphp

                                            
                                                        @if ($voucher->isNotEmpty())
                                                            @foreach ($voucher as $trndtl)
                                                                <tr>
                                                                    <td>{{ ++$totalEntries }}</td>
                                                                    <td>{{ $trndtl->date ?? 'N/A' }}</td> <!-- Show trndtl date -->
                                                                    <td>{{ $trndtl->accounts->title ?? 'N/A' }}</td> <!-- Account title -->
                                                                    <td>{{ $trndtl->purchasereturns->items->item_code }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->width }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->lenght }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->grammage }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->qty }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->rate }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->total_wt }}</td>
                                                                    <td>{{ $trndtl->purchasereturns->amount }}</td>
                                                                    <td>
                                                                        <!-- Delete Entry Button -->
                                                                        <a href="{{ route('purchase_return.destroy', $trndtl->id) }}"
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="event.preventDefault();
                                                                            if(confirm('Are you sure you want to delete this transaction?')) {
                                                                                window.location.href='{{ route('purchase_return.destroy', $trndtl->id) }}';
                                                                            }">Delete</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="13">No transaction details available for this purchase.</td>
                                                            </tr>
                                                        @endif
                                                    {{-- @endforeach --}}
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
    </div> <!-- End container -->
    <script>
    const today = new Date().toISOString().split('T')[0];

    // Set the value of the input field to the current date
    document.getElementById('entryDate').value = today;

    document.addEventListener('DOMContentLoaded', function() {
        const entriesTable = document.getElementById('entriesBody');
        const addUpdateButton = document.getElementById('addUpdateEntry');
        const entryDateInput = document.getElementById('entryDate');
        let invoiceCounter = entriesTable.children.length + 1; // Initialize counter based on existing rows
        let availableInvoiceNumbers = new Set(); // To track deleted invoice numbers
        let firstEntryAdded = false; // Track if the first entry has been added

        addUpdateButton.addEventListener('click', function() {
            // Add Entry logic (same as before)
            const date = entryDateInput.value;
            const width = document.getElementById('width').value;
            const length = document.getElementById('length').value;
            const gramage = document.getElementById('gramage').value;
            const quantity = document.getElementById('quantity').value;
            const rate = document.getElementById('rate').value;
            const cash = document.getElementById('entryCash').value;
            const prepared = document.getElementById('preparedBy').value;
            const item = document.getElementById('itemTitle');
            let selectedOption = item.options[item.selectedIndex];
            let itemTitleValue = selectedOption.text; // Account title text
            let itemIdValue = selectedOption.value; // Account ID
            const supplier = document.getElementById('entryParty');
            let selectedSupplier = supplier.options[supplier.selectedIndex];
            let supplierTitleValue = selectedSupplier.text; // Account title text
            let supplierIdValue = selectedSupplier.value; // Account ID

            const weight = ((length * width * gramage) / 15500) * quantity;
            const amount = weight * rate;

            if (!date || !width || !length || !gramage || !quantity || !rate || isNaN(weight) || isNaN(amount)) {
                return; // No alert message, just return if fields are not filled correctly
            }

            // Determine the invoice number to use
            let invoiceNumberToUse;
            if (availableInvoiceNumbers.size > 0) {
                invoiceNumberToUse = Math.min(...availableInvoiceNumbers); // Get the smallest available number
                availableInvoiceNumbers.delete(invoiceNumberToUse); // Remove it from available numbers
            } else {
                invoiceNumberToUse = invoiceCounter; // Use the current counter
                invoiceCounter++; // Increment for the next entry
            }

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${invoiceNumberToUse}</td>
                <td>${date}</td>
                <td>${supplierTitleValue}</td>
                <td>${itemTitleValue}</td>
                <td>${width}</td>
                <td>${length}</td>
                <td>${gramage}</td>
                <td>${quantity}</td>
                <td>${rate}</td>
                <td>${Math.round(weight)}</td>
                <td>${Math.round(amount)}</td>
                <td>
                    <button type="button" class="btn btn-danger delete-entry">Delete</button>
                    <input type="hidden" name="entries[${Date.now()}][date]" value="${date}">
                    <input type="hidden" name="entries[${Date.now()}][cash]" value="${cash}">
                    <input type="hidden" name="entries[${Date.now()}][supplier]" value="${supplierIdValue}">
                    <input type="hidden" name="entries[${Date.now()}][item]" value="${itemIdValue}">
                    <input type="hidden" name="entries[${Date.now()}][prepared_by]" value="${prepared}">
                    <input type="hidden" name="entries[${Date.now()}][width]" value="${width}">
                    <input type="hidden" name="entries[${Date.now()}][length]" value="${length}">
                    <input type="hidden" name="entries[${Date.now()}][gramage]" value="${gramage}">
                    <input type="hidden" name="entries[${Date.now()}][quantity]" value="${quantity}">
                    <input type="hidden" name="entries[${Date.now()}][rate]" value="${rate}">
                    <input type="hidden" name="entries[${Date.now()}][weight]" value="${Math.round(weight)}">
                    <input type="hidden" name="entries[${Date.now()}][amount]" value="${Math.round(amount)}">
                </td>
            `;
            entriesTable.appendChild(newRow);

            // Disable the date input after adding the first entry
            if (!firstEntryAdded) {
                entryDateInput.disabled = true;
                firstEntryAdded = true; // Mark that the first entry has been added
            }

            // Delete button functionality without confirmation
            const deleteButton = newRow.querySelector('.delete-entry');
            deleteButton.addEventListener('click', function() {
                entriesTable.removeChild(newRow);
                availableInvoiceNumbers.add(invoiceNumberToUse); // Add the deleted number back to available numbers
                // Enable the date input if no entries are left
                if (entriesTable.children.length === 0) {
                    entryDateInput.disabled = false; // Re-enable date input if all entries are deleted
                    firstEntryAdded = false; // Reset flag
                } 
            });

            // Clear input fields after adding entry
            document.getElementById('width').value = '';
            document.getElementById('length').value = '';
            document.getElementById('gramage').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('rate').value = '';

            // After adding entry, submit the form using AJAX to avoid page reload
            var form = document.getElementById('voucherForm');
            var formData = new FormData(form);
            $.ajax({
                url: form.action,  // Form action URL
                method: form.method, // POST method
                data: formData, // The form data
                processData: false, // Don't process the data
                contentType: false, // Don't set content type
                success:function(response) {
        // Reload the page on success
        location.reload();
                },
                error: function(xhr, status, error) {
                    // Do nothing on error, as no message should be shown
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
</script>



@endsection
