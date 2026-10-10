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
                            <li class="breadcrumb-item active">Edit Ink Purchase</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Ink Purchase</h4>
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
                                    <form id="voucherForm" action="{{ route('ink_purchase.update', $voucher->first()->v_no ?? 'N/A') }}"
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
                                                    @if ($item->type_id == 6)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->item_code }}
                                                        </option>
                                                        @endif
                                                    @endforeach
                                                </select>
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
                                                <input type="number" id="freight" class="form-control" name="freight" value="0" readonly>
                                            </div>
<button type="submit" id="updateInvoice" class="btn btn-primary">Add Entry</button>

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
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>Freight</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="entriesBody">
                                                    @php
                                                        $totalEntries = 0; // Initialize a counter for rows
                                                    @endphp

                                                    {{-- @foreach ($purchaseDetails as $purchase) --}}
                                                        {{-- @php
                                                            // Get all related trndtl records for this purchase detail by matching voucher number (v_no)
                                                            $relatedTrndtls = $trndtls->where('v_no', $purchase->vorcher_no);
                                                        @endphp --}}

                                                        @if ($voucher->isNotEmpty())
                                                            @foreach ($voucher as $trndtl)
                                                                <tr>
                                                                    <td>{{ ++$totalEntries }}</td>
                                                                    <td>{{ $trndtl->date ?? 'N/A' }}</td> <!-- Show trndtl date -->
                                                                    <td>{{ $trndtl->accounts->title ?? 'N/A' }}</td> <!-- Account title -->
                                                                    <td>{{ $trndtl->inkpurchases->items->item_code }}</td>
                                                                    <td>{{ $trndtl->inkpurchases->qty }}</td>
                                                                    <td>{{ $trndtl->inkpurchases->rate }}</td>
                                                                    <td>{{ $trndtl->inkpurchases->amount }}</td>
                                                                    <td>{{ $trndtl->inkpurchases->freight }}</td>
                                                                    <td>
                                                                        <!-- Delete Entry Button -->
                                                                        <a href="{{ route('ink_purchase.destroy', $trndtl->id) }}"
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="event.preventDefault();
                                                                            if(confirm('Are you sure you want to delete this transaction?')) {
                                                                                window.location.href='{{ route('ink_purchase.destroy', $trndtl->id) }}';
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
    var getItemDetailsUrl = "{{ route('getItemDetails', '') }}";
    const today = new Date().toISOString().split('T')[0];

    // Set the value of the input field to the current date
    document.getElementById('entryDate').value = today;

    document.addEventListener('DOMContentLoaded', function() {
        const entriesTable = document.getElementById('entriesBody');
        const addEntryButton = document.getElementById('addEntry');
        const entryDateInput = document.getElementById('entryDate');
        const updateInvoiceButton = document.getElementById('updateInvoice');
        let srNoCounter = entriesTable.children.length + 1; // Initialize counter based on existing rows
        let availableSrNumbers = new Set(); // To track deleted Sr No
        let firstEntryAdded = false; // Track if the first entry has been added

        function addEntry() {
            const date = entryDateInput.value;
            const quantity = document.getElementById('quantity').value;
            const rate = document.getElementById('rate').value;
            const freight = document.getElementById('freight').value;
            const prepared = document.getElementById('preparedBy').value;
            const item = document.getElementById('itemTitle');
            const cash = document.getElementById('entryCash').value;
            const selectedItem = item.options[item.selectedIndex];
            const itemTitleValue = selectedItem.text;
            const itemIdValue = selectedItem.value;
            const supplier = document.getElementById('entryParty');
            const selectedSupplier = supplier.options[supplier.selectedIndex];
            const supplierTitleValue = selectedSupplier.text;
            const supplierIdValue = selectedSupplier.value;

            // Validate inputs
            if (!date || !quantity || !rate) {
                alert('Please fill all fields.');
                return;
            }

            // Determine the Sr No to use
            let srNoToUse;
            if (availableSrNumbers.size > 0) {
                srNoToUse = Math.min(...availableSrNumbers); // Get the smallest available Sr No
                availableSrNumbers.delete(srNoToUse); // Remove it from available numbers
            } else {
                srNoToUse = srNoCounter; // Use the current Sr No
                srNoCounter++; // Increment for the next entry
            }

            // Create new row
            const amount = quantity * rate; // Calculate amount
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${srNoToUse}</td> <!-- Sr No -->
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

            // Disable date input after adding the first entry
            if (!firstEntryAdded) {
                entryDateInput.disabled = true;
                firstEntryAdded = true; // Mark that the first entry has been added
            }

            // Delete button functionality
            const deleteButton = newRow.querySelector('.delete-entry');
            deleteButton.addEventListener('click', function() {
                entriesTable.removeChild(newRow);
                availableSrNumbers.add(srNoToUse); // Add the deleted Sr No back to available numbers
                // Enable the date input if no entries are left
                if (entriesTable.children.length === 0) {
                    entryDateInput.disabled = false; // Re-enable date input if all entries are deleted
                    firstEntryAdded = false; // Reset flag
                }
            });

            // Clear input fields after adding entry
            document.getElementById('quantity').value = '';
            document.getElementById('freight').value = '0'; 
        }

        // Attach addEntry function to the Update Invoice button
        updateInvoiceButton.addEventListener('click', function(event) {
            event.preventDefault();  // Prevent form submission

            // Trigger add entry before updating invoice
            addEntry();

            // Submit the form using AJAX to avoid page reload
            var form = document.getElementById('voucherForm');
            var formData = new FormData(form);
            $.ajax({
                url: form.action,  // Form action URL
                method: form.method, // POST method
                data: formData, // The form data
                processData: false, // Don't process the data
                contentType: false, // Don't set content type
                success: function(response) {
                    // Do nothing on success, as no message should be shown
                },
                error: function(xhr, status, error) {
                    // Do nothing on error, as no message should be shown
                }
            });
        });
    });
</script>








@endsection
