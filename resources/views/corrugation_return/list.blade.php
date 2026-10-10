@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Corrugation Return</h4>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <form id="voucherForm" action="{{ route('corrugation_return.store') }}" method="POST">
                                @csrf
                                <div class="col-6">
                                    <input type="hidden" id="invoice_type" name="v_type" value="CPN" readonly>
                                    <input type="hidden" id="invoice" name="invoice_number">
                                    <input type="hidden" id="totalAmount" name="total_amount" value="0">
                                    <input type="hidden" id="entryCash" class="form-control" name="cash"
                                        value="{{ $purchaseAccount ? $purchaseAccount->id : '' }}">

                                    <!-- Date Field -->
                                    <div class="mb-3">
                                        <label for="entryDate" class="form-label">Date</label>
                                        <input type="date" id="entryDate" class="form-control" name="date">
                                    </div>

                                    <div class="mb-3">
                                        <label for="preparedBy" class="form-label">Prepared By</label>
                                        <input type="text" id="preparedBy" class="form-control"
                                            value="{{ $loggedInUser->name }}" name="prepared_by" readonly>
                                    </div>

                                    <!-- Supplier Selection -->
                                    <div class="mb-3">
                                        <label for="entryParty" class="form-label">Supplier</label>
                                        <select name="account" class="form-control select2" data-toggle="select2" id="entryParty" required>
                                            <option value="">Select</option>
                                            @foreach  ($accountSuppliers->whereIn('level2_id', [4, 23]) as $accountSupplie)
                                                <option value="{{ $accountSupplie->id }}">{{ $accountSupplie->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="item_id" class="form-label">Corrugation Type</label>
                                        <select name="item_id" class="form-control select2" data-toggle="select2" id="item_id">
                                            <option value="">Select</option>
                                            @foreach ($items as $item)
                                            @if ($item->type_id == 2)
                                            <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}">
                                                {{ $item->item_code }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity, Size, and Rate Fields -->
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" id="quantity" class="form-control" name="quantity" step="any">
                                    </div>

                                    <div class="mb-3">
                                        <label for="size" class="form-label">Size</label>
                                        <input type="number" id="size" class="form-control" name="size" step="any">
                                    </div>

                                    <div class="mb-3">
                                        <label for="rate" class="form-label">Rate</label>
                                        <input type="number" id="rate" class="form-control" name="rate" step="any">
                                    </div>

                                            
                                    <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
                                    <button type="submit" class="btn btn-success">Submit Voucher</button>
                                </div>

                                <!-- Entries Table -->
                                <div class="col-lg-12">
                                    <table class="table mt-4" id="entriesTable">
                                        <thead>
                                            <tr>
                                                <th>Sr No</th>
                                                <th>Date</th>
                                                <th>Supplier</th>
                                                <th>Type</th>
                                                
                                                <th>Size</th>
                                                <th>Quantity</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        document.getElementById('entryDate').value = today;

        document.addEventListener('DOMContentLoaded', function () {
            const entriesTable = document.getElementById('entriesBody');
            const addEntryButton = document.getElementById('addEntry');
            const entryDateInput = document.getElementById('entryDate');
            let invoiceCounter = 1;

            // Automatically set the date to today
            addEntryButton.addEventListener('click', function () {
                const date = entryDateInput.value;
                const quantity = parseFloat(document.getElementById('quantity').value);
                const size = parseFloat(document.getElementById('size').value);
                const rate = parseFloat(document.getElementById('rate').value);
                const prepared = document.getElementById('preparedBy').value;
                const cash = document.getElementById('entryCash').value;

                const supplier = document.getElementById('entryParty');
                const supplierText = supplier.options[supplier.selectedIndex]?.text || '';
                const supplierValue = supplier.value;

                const item = document.getElementById('item_id');
                const itemText = item.options[item.selectedIndex]?.text || '';
                const itemValue = item.value;

                // Calculate amount
                const amount = quantity * rate * size;

                // Validate inputs
                if (!date || !quantity || !size || !rate || isNaN(amount) || !supplierValue || !itemValue) {
                    alert('Please fill all required fields, including selecting a Supplier and Corrugation Type.');
                    return;
                }

                // Create a new row
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${invoiceCounter}</td>
                    <td>${date}</td>
                    <td>${supplierText}</td>
                    <td>${itemText}</td>
                   
                    <td>${size}</td>
                     <td>${quantity}</td>
                    <td>${rate}</td>
                    <td>${Math.round(amount)}</td>
                    <td>
                        <button type="button" class="btn btn-danger delete-entry">Delete</button>
                        <input type="hidden" name="entries[${invoiceCounter}][date]" value="${date}">
                        <input type="hidden" name="entries[${invoiceCounter}][supplier]" value="${supplierValue}">
                        <input type="hidden" name="entries[${invoiceCounter}][prepared_by]" value="${prepared}">
                        <input type="hidden" name="entries[${invoiceCounter}][quantity]" value="${quantity}">
                        <input type="hidden" name="entries[${invoiceCounter}][size]" value="${size}">
                        <input type="hidden" name="entries[${invoiceCounter}][cash]" value="${cash}">
                        <input type="hidden" name="entries[${invoiceCounter}][rate]" value="${rate}">
                        <input type="hidden" name="entries[${invoiceCounter}][item]" value="${itemValue}">
                        <input type="hidden" name="entries[${invoiceCounter}][amount]" value="${Math.round(amount)}">
                    </td>
                `;

                entriesTable.appendChild(newRow);
                invoiceCounter++;

                // Disable the date field after adding the first entry
                entryDateInput.disabled = true;

                // Update total amount
                const totalAmountField = document.getElementById('totalAmount');
                totalAmountField.value = parseFloat(totalAmountField.value) + amount;

                // Reset input fields
                document.getElementById('quantity').value = '';
                document.getElementById('size').value = '';

                // Add delete functionality
                newRow.querySelector('.delete-entry').addEventListener('click', function () {
                    const rowAmount = parseFloat(newRow.querySelector('td:nth-child(8)').innerText);
                    totalAmountField.value = parseFloat(totalAmountField.value) - rowAmount;
                    entriesTable.removeChild(newRow);

                    // Enable the date field if no entries are left
                    if (entriesTable.children.length === 0) {
                        entryDateInput.disabled = false;
                    }
                });
            });
        });

        // Autofill rate based on selected Corrugation Type (item id)
        document.getElementById('item_id').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const rate = selectedOption.getAttribute('data-rate');
            document.getElementById('rate').value = rate;
        });
        
         document.getElementById('freight').addEventListener('change', function() {
    this.disabled = true;
});
    </script>

@endsection
