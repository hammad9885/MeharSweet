@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Stock Report</h4>
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
                    <form id="voucherForm" action="{{ route('stock_report.store') }}" method="POST">
                        @csrf
                        <div class="col-6">
                            <input type="hidden" id="invoice" name="invoice_number">
                            <input type="hidden" id="totalAmount" name="total_amount" value="0">

                            <!-- Date Field -->
                            <div class="mb-3">
                                <label for="entryDate" class="form-label">Date</label>
                                <input type="date" id="entryDate" class="form-control" name="date">
                            </div>

                            <!-- Prepared By Field -->
                            <div class="mb-3">
                                <label for="preparedBy" class="form-label">Prepared By</label>
                                <input type="text" id="preparedBy" class="form-control" name="prepared_by" value="{{ $loggedInUser->name }}" readonly>
                            </div>
                            
                            <div class="mb-3">Consumed Items</div>
                            <div class="mb-3">
                                                <label for="citemTitle" class="form-label">Item Title</label>
                                                <select name="citem" class="form-control select2" data-toggle="select2"
                                                    id="citemTitle" required>
                                                    <option value="">Select</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->item_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                            <div class="mb-3">
                                                <label for="cquantity" class="form-label">Quantity</label>
                                                <input type="number" id="cquantity" class="form-control"
                                                    name="cquantity">
                                            </div>
                         
                            <div class="mb-3">Produced Items</div>
                            <div class="mb-3">
                                                <label for="pitemTitle" class="form-label">Item Title</label>
                                                <select name="pitem" class="form-control select2" data-toggle="select2"
                                                    id="pitemTitle" required>
                                                    <option value="">Select</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->item_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                            <div class="mb-3">
                                                <label for="pquantity" class="form-label">Quantity</label>
                                                <input type="number" id="pquantity" class="form-control"
                                                    name="pquantity">
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
                                        <th>Consumed Items</th>
                                        <th>Consumed Qty</th>
                                        <th>Produced Items</th>
                                        <th>Produced Qty</th>
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

<script>

document.addEventListener('DOMContentLoaded', function() {
    const entriesTable = document.getElementById('entriesBody');
    const addEntryButton = document.getElementById('addEntry');
    const entryDateInput = document.getElementById('entryDate');
    const pquantity = document.getElementById('pquantity');
    const cquantity = document.getElementById('cquantity');
    let invoiceCounter = 1;

    // Automatically set the date to today
    entryDateInput.value = new Date().toISOString().split('T')[0];

    addEntryButton.addEventListener('click', function() {
        const date = entryDateInput.value;
        const prepared = document.getElementById('preparedBy').value;
        const citem = document.getElementById('citemTitle');
        const pitem = document.getElementById('pitemTitle');
        const citemText = citem.options[citem.selectedIndex]?.text || ''; 
        const pitemText = pitem.options[pitem.selectedIndex]?.text || ''; 
        const citemValue = citem.value;
        const pitemValue = pitem.value;

        if (!date || isNaN(parseFloat(pquantity.value)) || isNaN(parseFloat(cquantity.value))) {
            alert('Please fill all fields with valid data.');
            return;
        }

        // Create a new row
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${invoiceCounter}</td>
            <td>${date}</td>
            <td>${citemText}</td>
            <td>${cquantity.value}</td>
            <td>${pitemText}</td>
            <td>${pquantity.value}</td>
            <td>
                <button type="button" class="btn btn-danger delete-entry">Delete</button>
                <input type="hidden" name="entries[${Date.now()}][date]" value="${date}">
                <input type="hidden" name="entries[${Date.now()}][sr_no]" value="${invoiceCounter}">
                <input type="hidden" name="entries[${Date.now()}][citem]" value="${citemValue}">
                <input type="hidden" name="entries[${Date.now()}][cquantity]" value="${cquantity.value}">
                <input type="hidden" name="entries[${Date.now()}][pitem]" value="${pitemValue}">
                <input type="hidden" name="entries[${Date.now()}][pquantity]" value="${pquantity.value}">
            </td>
        `;

        entriesTable.appendChild(newRow);
        invoiceCounter++;

        // Reset input fields
        cquantity.value = '';
        pquantity.value = '';

        // Disable date field if an entry is added
        entryDateInput.disabled = true;

        // Add delete functionality
        newRow.querySelector('.delete-entry').addEventListener('click', function() {
            entriesTable.removeChild(newRow);

            // Enable the date field if no entries are left
            if (entriesTable.children.length === 0) {
                entryDateInput.disabled = false;
            }
        });
    });
});

</script>

@endsection
