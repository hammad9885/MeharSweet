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
                            <li class="breadcrumb-item active">Edit Lamination Purchase</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Lamination Purchase</h4>
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

        <!-- Example for displaying purchase details -->
     
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <form id="voucherForm"
                                        action="{{ route('lemination_purchase.update', $voucher->first()->v_no) }}"
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
                                            <input type="hidden" id="entryCash" class="form-control" name="cash"
                                                value="{{ $purchaseAccount ? $purchaseAccount->id : '' }}">
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
                                                    name="prepared_by" value="{{ $loggedInUser->name }}" readonly>
                                            </div>

                                           <div class="mb-3">
    <label for="entryParty" class="form-label">Supplier</label>
    <select name="account" class="form-control select2" data-toggle="select2" id="entryParty" required>
        <option value="">Select</option>
        @foreach($accountSuppliers->whereIn('level2_id', [4, 23]) as $accountSupplie)
            <option value="{{ $accountSupplie->id }}">
                {{ $accountSupplie->title }}
            </option>
        @endforeach
    </select>
</div>

                                            <div class="mb-3">
                                    <label for="item_id" class="form-label">lamination Type</label>
                                    <select name="item_id" class="form-control select2" data-toggle="select2" id="item_id">
                                        <option value="">Select</option>
                                        @foreach ($items as $item)
                                        @if ($item->type_id == 4)
                                        <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}" data-grammage="{{ $item->grammage }}">
                                            {{ $item->item_code }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                           <div class="mb-3">
                                                <label for="size" class="form-label">Size</label>
                                                <input type="number" id="size" class="form-control" name="size" step="any">
                                            </div>



                                            <div class="mb-3">
                                                <label for="qty" class="form-label">Quantity</label>
                                                <input type="number" id="qty" class="form-control" name="qty" step="any">
                                            </div>

                                            <div class="mb-3">
                                                <label for="rate" class="form-label">Rate</label>
                                                <input type="number" id="rate" class="form-control"
                                                    name="rate">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="freight" class="form-label">Freight</label>
                                                <input type="number" id="freight" class="form-control" name="freight" value="0" readonly>
                                            </div>
                                          
                                            <button type="submit" id="addUpdateEntry" class="btn btn-primary">Add Entry</button>
                                        </div>

                                        <!-- Entries Table -->
                                        <div class="col-lg-12">
                                            <table class="table mt-4" id="entriesTable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr No</th>
                                                        <th>Date</th>
                                                        <th>Supplier</th>
                                                        <th>lamination Type</th>
                                                        <th>Quantity</th>
                                                        <th>Size</th>
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


                                                    @if ($voucher->isNotEmpty())
                                                        @foreach ($voucher as $trndtl)
                                                            <tr>
                                                                <td>{{ ++$totalEntries }}</td>
                                                                <td>{{ $trndtl->date ?? 'N/A' }}</td>
                                                                <!-- Show trndtl date -->
                                                                <td>{{ $trndtl->accounts->title ?? 'N/A' }}</td>
                                                                <!-- Account title -->
                                                                   <td>{{ $trndtl->leminationpurchases->item->item_code ?? 'N/A' }}</td>
                                                                <td>{{ $trndtl->leminationpurchases->qty }}</td>
                                                                <td>{{ $trndtl->leminationpurchases->size }}</td>
                                                                
                                                                <td>{{ $trndtl->leminationpurchases->rate }}</td>
                                                                <td>{{ $trndtl->leminationpurchases->amount }}</td>
                                                                <td>{{ $trndtl->leminationpurchases->freight }}</td>
                                                                <td>
                                                                    <!-- Delete Entry Button -->
                                                                    <a href="{{ route('lemination_purchase.destroy', $trndtl->id) }}"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="event.preventDefault();
                                                                            if(confirm('Are you sure you want to delete this transaction?')) {
                                                                                window.location.href='{{ route('lemination_purchase.destroy', $trndtl->id) }}';
                                                                            }">Delete</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="13">No transaction details available for this
                                                                purchase.</td>
                                                        </tr>
                                                    @endif
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Listen for changes to the item_id dropdown
        $('#item_id').change(function() {
            // Get the selected option
            var selectedOption = $('#item_id option:selected');
            
            // Get the rate from the data-rate attribute of the selected option
            var rate = selectedOption.data('rate');
            
            // Set the rate input field value to the selected rate
            $('#rate').val(rate);
        });
    });
    
    var getItemDetailsUrl = "{{ route('getItemDetails', '') }}";
    const today = new Date().toISOString().split('T')[0];

    // Set the value of the input field to the current date
    document.getElementById('entryDate').value = today;

    document.addEventListener('DOMContentLoaded', function() {
        const entriesTable = document.getElementById('entriesBody');
        const addUpdateButton = document.getElementById('addUpdateEntry');  // Combined button
        const entryDateInput = document.getElementById('entryDate');
        let isDateDisabled = false; // Flag to track date input state

        addUpdateButton.addEventListener('click', function() {
            // Add Entry logic
            const date = entryDateInput.value;
            const qty = document.getElementById('qty').value;
            const size = document.getElementById('size').value;
            const freight = document.getElementById('freight').value;
            const prepared = document.getElementById('preparedBy').value;
            const rate = document.getElementById('rate').value;
            const cash = document.getElementById('entryCash').value;
            const item = document.getElementById('item_id');
            let selectedOption = item.options[item.selectedIndex];
            let itemTitleValue = selectedOption.text;
            let itemIdValue = selectedOption.value;
            const supplier = document.getElementById('entryParty');
            let selectedSupplier = supplier.options[supplier.selectedIndex];
            let supplierTitleValue = selectedSupplier.text;
            let supplierIdValue = selectedSupplier.value;

            const amount = qty * rate * size;

            if (!date || !size || !qty || !rate || isNaN(amount)) {
                alert('Please fill all fields.');
                return;
            }

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td></td> <!-- Placeholder for Sr No -->
                <td>${date}</td>
                <td>${supplierTitleValue}</td>
                <td>${itemTitleValue}</td>
                <td>${size}</td>
                <td>${qty}</td>
                <td>${rate}</td>
                <td>${Math.round(amount)}</td>
                <td>${freight}</td>
                <td>
                    <button type="button" class="btn btn-danger delete-entry">Delete</button>
                    <input type="hidden" name="entries[${Date.now()}][date]" value="${date}">
                    <input type="hidden" name="entries[${Date.now()}][supplier]" value="${supplierIdValue}">
                    <input type="hidden" name="entries[${Date.now()}][item]" value="${itemIdValue}">
                    <input type="hidden" name="entries[${Date.now()}][prepared_by]" value="${prepared}">
                    <input type="hidden" name="entries[${Date.now()}][size]" value="${size}">
                    <input type="hidden" name="entries[${Date.now()}][cash]" value="${cash}">
                    <input type="hidden" name="entries[${Date.now()}][qty]" value="${qty}">
                    <input type="hidden" name="entries[${Date.now()}][rate]" value="${rate}">
                    <input type="hidden" name="entries[${Date.now()}][amount]" value="${Math.round(amount)}">
                    <input type="hidden" name="entries[${Date.now()}][freight]" value="${freight}">
                </td>
            `;

            entriesTable.appendChild(newRow);
            updateSerialNumbers(); // Update Sr No after adding a new entry
            document.getElementById('totalAmount').value = parseFloat(document.getElementById('totalAmount').value) + amount;
            document.getElementById('totalWeight').value = parseFloat(document.getElementById('totalWeight').value) + parseFloat(width) * parseFloat(length);

            // Reset input fields
            document.getElementById('size').value = '';
            document.getElementById('qty').value = '';
            document.getElementById('freight').value = '0';

            // Disable the date input after the first entry
            if (!isDateDisabled) {
                entryDateInput.disabled = true; // Disable date input
                isDateDisabled = true; // Update the flag
            }

            // Add delete functionality to the newly created delete button
            const deleteButton = newRow.querySelector('.delete-entry');
            deleteButton.addEventListener('click', function() {
                // Remove the row from the table
                entriesTable.removeChild(newRow);
                // Update Sr No for remaining entries
                updateSerialNumbers();
                // Update the total amount by subtracting the row's amount
                const rowAmount = parseFloat(newRow.children[8].innerText);
                document.getElementById('totalAmount').value = parseFloat(document.getElementById('totalAmount').value) - rowAmount;

                // Check if there are no entries left in the table
                if (entriesTable.rows.length === 0) {
                    entryDateInput.disabled = false; // Enable date input if no entries are present
                    isDateDisabled = false; // Reset the flag
                }
            });

            // After adding entry, submit the form using AJAX to update the invoice
            var form = document.getElementById('voucherForm');
            var formData = new FormData(form);
            $.ajax({
                url: form.action,  // Form action URL
                method: form.method, // POST method
                data: formData, // The form data
                processData: false, // Don't process the data
                contentType: false, // Don't set content type
                success: function(response) {
                    // Do nothing on success
                },
                error: function(xhr, status, error) {
                    // Do nothing on error
                }
            });
        });
    });

    // Function to update serial numbers after a row is added or deleted
    function updateSerialNumbers() {
        const rows = entriesTable.rows;
        for (let i = 0; i < rows.length; i++) {
            rows[i].cells[0].innerText = i + 1; // Update Sr No
        }
    }
</script>



@endsection
