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
                            <li class="breadcrumb-item active">Purchase Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Purchase Return</h4>
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
                                    <form id="voucherForm" action="{{ route('purchase_return.store') }}" method="POST">
                                        @csrf
                                        <div class="col-6">
                                            <input type="hidden" id="invoice_type" class="form-control" name="v_type"
                                                value="PRN" required readonly>
                                            <input type="hidden" id="invoice" class="form-control" name="invoice_number"
                                                required>
                                            <input type="hidden" id="totalAmount" name="total_amount" value="0">
                                            <input type="hidden" id="totalWeight" name="total_weight" value="0">
                                            <input type="hidden" id="entryCash" class="form-control" name="cash" value="{{ $purchaseAccount ? $purchaseAccount->id : '' }}">
                                     
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
                                                    @foreach ($accountSuppliers->where('level2_id', 4) as $accountSupplie)
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
                                                        <option value="{{ $item->id }}">{{ $item->item_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="width" class="form-label">Width</label>
                                                <input type="number" id="width" class="form-control" name="width">
                                            </div>

                                            <div class="mb-3">
                                                <label for="length" class="form-label">Length</label>
                                                <input type="number" id="length" class="form-control" name="length">
                                            </div>

                                            <div class="mb-3">
                                                <label for="gramage" class="form-label">Gramage</label>
                                                <input type="number" id="gramage" class="form-control" name="gramage" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" id="quantity" class="form-control"
                                                    name="quantity">
                                            </div>

                                            <div class="mb-3">
                                                <label for="rate" class="form-label">Rate</label>
                                                <input type="number" id="rate" class="form-control"
                                                    name="rate" readonly>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Set current date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('entryDate').value = today;

            // Initialize select2
            $('.select2').select2();

            const entriesTable = document.getElementById('entriesBody');
            const addEntryButton = document.getElementById('addEntry');
            const invoiceInput = document.getElementById('invoice');
            const entryDateInput = document.getElementById('entryDate');
            let invoiceCounter = 1;

            invoiceInput.value = invoiceCounter;

            // Item change event handler
            $('#itemTitle').on('change', function() {
                var selectedItemId = $(this).val();
                
                if (selectedItemId) {
                    $.ajax({
                        url: '/get-item-details/' + selectedItemId,
                        method: 'GET',
                        success: function(response) {
                            if (response && response.purchase) {
                                $('#rate').val(response.purchase);
                            }
                            if (response && response.gramage) {
                                $('#gramage').val(response.gramage);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching item details:", error);
                        }
                    });
                }
            });

            // Add entry button click handler
            addEntryButton.addEventListener('click', function() {
                const date = entryDateInput.value;
                const width = parseFloat(document.getElementById('width').value) || 0;
                const length = parseFloat(document.getElementById('length').value) || 0;
                const gramage = parseFloat(document.getElementById('gramage').value) || 0;
                const quantity = parseFloat(document.getElementById('quantity').value) || 0;
                const rate = parseFloat(document.getElementById('rate').value) || 0;
                const cash = document.getElementById('entryCash').value;
                const prepared = document.getElementById('preparedBy').value;
                
                const item = document.getElementById('itemTitle');
                const selectedOption = item.options[item.selectedIndex];
                const itemTitleValue = selectedOption.text;
                const itemIdValue = selectedOption.value;
                
                const supplier = document.getElementById('entryParty');
                const selectedSupplier = supplier.options[supplier.selectedIndex];
                const supplierTitleValue = selectedSupplier.text;
                const supplierIdValue = selectedSupplier.value;

                const weight = ((length * width * gramage) / 15500) * quantity;
                const amount = weight * rate;

               
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${invoiceCounter}</td>
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
                invoiceCounter++;

                // Disable the date input after adding the first entry
                entryDateInput.disabled = true;

                // Clear input fields after adding entry
                document.getElementById('width').value = '';
                document.getElementById('length').value = '';
                document.getElementById('quantity').value = '';

                // Add delete event handler to the new button
                newRow.querySelector('.delete-entry').addEventListener('click', function() {
                    this.closest('tr').remove();
                    
                    // Re-enable date input if no entries left
                    if (entriesTable.rows.length === 0) {
                        entryDateInput.disabled = false;
                    }
                });
            });
        });
    </script>
@endsection