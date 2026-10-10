@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Confectionery Billing</h4>
            </div>
        </div>
    </div>

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

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="voucherForm" action="{{ route('confect_billing.store') }}" method="POST">
                        @csrf
                        <div class="col-6">
                            <input type="hidden" id="invoice_type" name="v_type" value="PSN" readonly>
                            <input type="hidden" id="totalAmount" name="total_amount" value="0">
                            <input type="hidden" id="grandTotal" name="grand_total" value="0">

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

                            <!-- Party Selection -->
                            <div class="mb-3">
    <label for="entryParty" class="form-label">Party</label>
    <select name="account" class="form-control select2" id="entryParty" data-toggle="select2" required>
        <option value="">Select</option>
        @foreach ($accounts->where('level2_id', 7) as $account)
            <option value="{{ $account->id }}">{{ $account->title }}</option>
        @endforeach
    </select>
</div>


                            <!-- VO Selection -->
                           <div class="mb-3">
    <label for="vno" class="form-label">Voucher No</label>
    <select name="vno" class="form-control select2" id="vno" data-toggle="select2" required>
        <option value="">Select</option>
    </select>
</div>



                            <button type="button" id="loadEntry" class="btn btn-primary">Load</button>
                            <button type="submit" class="btn btn-success">Submit Voucher</button>
                        </div>

                        <!-- Entries Table -->
                        <div class="col-lg-12">
                            <table class="table mt-4" id="entriesTable">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Date</th>
                                        <th>Product Name</th>
                                        <th>Party</th>
                                        <th>Item</th>
                                        <th>Box</th>
                                        <th>Packing</th>
                                        <th>PO No</th>
                                        <th>Total</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="entriesBody">
                                    <!-- Entries will appear here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="10" class="text-end">Grand Total:</th>
                                        <th id="grandTotalRow">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function () {
    const loadedVnos = new Set(); // To track already loaded voucher numbers

    // Set today's date as the default value for the date input
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('entryDate');
    if (dateInput) {
        dateInput.value = today;
    }

    // Initialize select2 for the party dropdown
    const partySelect = $('#entryParty');
    if (partySelect.length) {
        partySelect.select2();
    }

    // Listen for changes on the party dropdown
    partySelect.on('change', function () {
        const accountId = $(this).val();

        // Clear the voucher dropdown and set default option
        const vnoSelect = $('#vno');
        vnoSelect.empty().append('<option value="">Select</option>');

        if (accountId) {
            // AJAX request to get voucher numbers for the selected party
            $.ajax({
                url: '/get-vnos/' + accountId,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        // Populate the voucher dropdown
                        response.vnos.forEach(function(vno) {
                            vnoSelect.append(`<option value="${vno}">${vno}</option>`);
                        });

                        // Mark used voucher numbers
                        response.used_vnos.forEach(function(vno) {
                            vnoSelect.append(`<option value="${vno}" disabled>${vno} (Already Selected)</option>`);
                        });
                    } else {
                        vnoSelect.append('<option value="">Not Available</option>');
                    }
                    vnoSelect.select2();
                },
                error: function() {
                    vnoSelect.append('<option value="">Error fetching vouchers</option>');
                    vnoSelect.select2();
                }
            });
        }
    });

    // Handle "Load" button click
    $('#loadEntry').on('click', function() {
        const vno = $('#vno').val();
        if (vno) {
            // if (loadedVnos.has(vno)) {
            //     alert("You already loaded this voucher.");
            //     return;
            // }

            // AJAX request to fetch data based on the selected vno
            $.ajax({
                url: '/get-entry-details/' + vno,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success' && response.entries.length > 0) {
                        response.entries.forEach(function(entry) {
                            const row = `
                                <tr data-old-vno="${entry.v_no}">
                                    <td><input type="number" name="old_vno[]" value="${entry.v_no || '0'}" class="form-control p-0" style="border: none;" readonly></td>
                                    <td>${entry.date}</td>
                                    <td>
    <span style="white-space: normal; display: block;">${entry.product_name || 'N/A'}</span>
    <input type="hidden" name="product_name[]" value="${entry.product_name || 'N/A'}">
    <input type="hidden" name="product_id[]" value="${entry.product_id || 'N/A'}">
</td>

                                    <td>${entry.party || 'N/A'}</td>
                                     <td>
        <span style="white-space: normal; display: block;">${entry.item_type || 'N/A'}</span>
        <input type="hidden" name="item[]" value="${entry.item_type || 'N/A'}">
        <input type="hidden" name="item_id[]" value="${entry.item_id || 'N/A'}">
    </td>
                                    <td><input type="text" name="box[]" value="${entry.box || 'N/A'}" class="form-control p-0" style="border: none;" readonly></td>
                                    <td><input type="text" name="packing[]" value="${entry.pack_qty || 'N/A'}" class="form-control p-0" style="border: none;" readonly></td>
                                    <td><input type="text" name="po_no[]" value="${entry.po_no || 'N/A'}" class="form-control p-0" style="border: none;" readonly></td>
                                    <td><input type="number" name="total[]" value="${entry.total || '0'}" class="form-control p-0 total-input" style="border: none;" readonly></td>
                                    <td><input type="number" name="rate[]" value="${entry.rate || '0'}" class="form-control p-0 rate-input" style="border: none;" readonly></td>
                                    <td><input type="number" name="total_rate[]" class="form-control total-rate-input p-0" style="border: none;" readonly></td>
                                    <td><button type="button" class="btn btn-danger remove-entry" data-vno="${entry.v_no}">Remove</button></td>
                                </tr>
                            `;
                            $('#entriesBody').append(row);
                        });

                        loadedVnos.add(vno); // Mark vno as loaded
                        alert("Load Successfully");
                        calculateAllTotals();
                    } else {
                        alert('No entries found for this voucher.');
                    }
                },
                error: function() {
                    alert('Error fetching entry details.');
                }
            });
        } else {
            alert("Please select a Voucher No.");
        }
    });

    // Event listener for removing entries
    $('#entriesTable').on('click', '.remove-entry', function() {
        const vno = $(this).data('vno');
        $(`tr[data-old-vno="${vno}"]`).remove();
        loadedVnos.delete(vno); // Remove vno from loaded set
        calculateAllTotals();
    });
});

function calculateAllTotals() {
    let grandTotal = 0;
    $('#entriesBody tr').each(function() {
        const totalInput = $(this).find('input[name="total[]"]');
        const rateInput = $(this).find('input[name="rate[]"]');
        const totalRateInput = $(this).find('input[name="total_rate[]"]');

        if (totalInput.length && rateInput.length && totalRateInput.length) {
            const total = parseFloat(totalInput.val()) || 0;
            const rate = parseFloat(rateInput.val()) || 0;
            const totalRate = total * rate;

            totalRateInput.val(totalRate.toFixed(2));
            grandTotal += totalRate;
        }
    });

    $('#grandTotalRow').text(grandTotal.toFixed(2)); // Update the Grand Total row
    $('#grandTotal').val(grandTotal.toFixed(2)); // Set the hidden input field
}


</script>

@endsection
