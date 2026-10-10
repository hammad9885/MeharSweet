@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                        <li class="breadcrumb-item active">General Billing</li>
                    </ol>
                </div>
                <h4 class="page-title">General Billing</h4>
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
                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <form id="voucherForm" action="{{ route('general_billing.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <input type="hidden" id="invoice_type" name="v_type" value="GB" readonly>
                                        <input type="hidden" id="totalAmount" name="total_amount" value="0">
                                        <input type="hidden" id="totalWeight" name="total_weight" value="0">

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
                                            <select name="account" class="form-control select2" id="entryParty" data-toggle="select2" >
                                                <option value="">Select</option>
                                                @foreach ($accounts->whereIn('level2_id', [4, 7]) as $account)
                                                    <option value="{{ $account->id }}">{{ $account->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Voucher No Selection -->
                                        <div class="mb-3">
                                            <label for="v_no" class="form-label">Voucher No</label>
                                            <select name="v_no" class="form-control select2" data-toggle="select2" id="v_no" >
                                                <option value="">Select</option>
                                            </select>
                                        </div>

                                        <button type="button" id="loadEntry" class="btn btn-primary">Load</button>
                                        <button type="submit" class="btn btn-success">Submit Voucher</button>
                                    </div>
                                </div>

                                <!-- Entries Table -->
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <table class="table" id="entriesTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%">Sr No</th>
                                                    <th style="width: 8%">V No</th>
                                                    <th style="width: 8%">Date</th>
                                                    <th style="width: 15%">Party Name</th>
                                                    <th style="width: 8%">JS No</th>
                                                    <th style="width: 15%">Product Type</th>
                                                    <th style="width: 15%">Item Name</th>
                                                    <th style="width: 5%">Qty</th>
                                                    <th style="width: 5%">Rate</th>
                                                    <th style="width: 5%">Freight</th>
                                                    <th style="width: 8%">Amount</th>
                                                    <th style="width: 8%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="entriesBody">
                                                <!-- Entries will appear here -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7" class="text-end">Grand Totals:</th>
                                                    <th id="displayGrandTotalQty">0.00</th>
                                                    <th id="displayGrandTotalRate">0.00</th>
                                                    <th id="displayGrandTotalFreight">0.00</th>
                                                    <th id="displayGrandTotalAmount">0.00</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Set today's date as the default value for the date input
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('entryDate').value = today;

    // Initialize select2
    $('.select2').select2();

    let rowCounter = 1; // Counter for serial numbers
    const loadedVouchers = new Map(); // Track loaded vouchers and their row counts

    // Listen for changes on the party dropdown
    $('#entryParty').on('change', function () {
        const accountId = $(this).val();
        const vnoSelect = $('#v_no');
        
        vnoSelect.empty().append('<option value="">Select</option>');
        
        if (!accountId) {
            vnoSelect.trigger('change');
            return;
        }
        
        vnoSelect.prop('disabled', true);
        
        $.ajax({
            url: '/get-voucher-numbers/' + accountId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.length > 0) {
                    // First get all voucher numbers that are already in general_billings for this party
                    $.ajax({
                        url: '/check-existing-billings',
                        type: 'GET',
                        data: {
                            account_id: accountId
                        },
                        success: function(existingData) {
                            data.forEach(function(voucherNo) {
                                // Check if this voucherNo already exists in general_billings
                                const exists = existingData.some(item => item.v_no == voucherNo);
                                
                                if (exists) {
                                    vnoSelect.append($('<option></option>')
                                        .val(voucherNo)
                                        .text(voucherNo + ' (Already Billed)')
                                        .prop('disabled', true)
                                        .addClass('text-danger'));
                                } else {
                                    vnoSelect.append($('<option></option>')
                                        .val(voucherNo)
                                        .text(voucherNo));
                                }
                            });
                        },
                        error: function() {
                            // If we can't check existing billings, just show all options
                            data.forEach(function(voucherNo) {
                                vnoSelect.append($('<option></option>').val(voucherNo).text(voucherNo));
                            });
                        }
                    });
                } else {
                    vnoSelect.append('<option value="">No vouchers available</option>');
                }
            },
            error: function() {
                vnoSelect.append('<option value="">Error loading data</option>');
            },
            complete: function() {
                vnoSelect.prop('disabled', false).trigger('change');
            }
        });
    });
    
    // Handle load button click to fetch voucher details
    $('#loadEntry').on('click', function() {
        const voucherNo = $('#v_no').val();
        const accountId = $('#entryParty').val();
        
        if (!voucherNo) {
            alert('Please select a voucher number first');
            return;
        }
        
        // First check if this voucher is already in general_billings
        $.ajax({
            url: '/check-existing-billings',
            type: 'GET',
            data: {
                account_id: accountId,
                v_no: voucherNo
            },
            success: function(existingData) {
                if (existingData.length > 0) {
                    alert('This voucher has already been billed and cannot be loaded again.');
                    return;
                }
                
                // If not already billed, proceed to load the details
                loadVoucherDetails(voucherNo);
            },
            error: function() {
                alert('Error checking existing billings. Please try again.');
            }
        });
    });
    
    function loadVoucherDetails(voucherNo) {
        $.ajax({
            url: '/get-voucher-details/' + voucherNo,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#loadEntry').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    // Track loaded voucher
                    if (!loadedVouchers.has(voucherNo)) {
                        loadedVouchers.set(voucherNo, 0);
                    }
                    
                    // Add new entries
                    response.data.forEach(function(item) {
                        const date = new Date(item.updated_at);
                        const formattedDate = date.toISOString().split('T')[0];
                        const amount = (parseFloat(item.qty) || 0) * (parseFloat(item.rate) || 0);
                        
                        const row = `
                            <tr data-voucher="${item.v_no}">
                                <td>${rowCounter++}</td>
                                <td style="white-space: normal;">${item.v_no || ''}</td>
                                <td style="white-space: normal;">${formattedDate}</td>
                                <td style="white-space: normal;">${item.party_name || ''}</td>
                                <td style="white-space: normal;">${item.gjs_no || ''}</td>
                                <td style="white-space: normal;">${item.product_type || ''}</td>
                                <td style="white-space: normal;">${item.item_name || ''}</td>
                                <td style="white-space: normal;">${item.qty || '0'}</td>
                                <td style="white-space: normal;">${item.rate || '0'}</td>
                                <td style="white-space: normal;">${item.freight || '0'}</td>
                                <td style="white-space: normal;">${amount.toFixed(2)}</td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-danger btn-sm delete-row">Delete</button>
                                    <input type="hidden" name="entries[${Date.now()}][v_no]" value="${item.v_no}">
                                    <input type="hidden" name="entries[${Date.now()}][date]" value="${formattedDate}">
                                    <input type="hidden" name="entries[${Date.now()}][party_name]" value="${item.party_name}">
                                    <input type="hidden" name="entries[${Date.now()}][gjs_no]" value="${item.gjs_no}">
                                    <input type="hidden" name="entries[${Date.now()}][product_type]" value="${item.product_type}">
                                    <input type="hidden" name="entries[${Date.now()}][item_name]" value="${item.item_name}">
                                    <input type="hidden" name="entries[${Date.now()}][qty]" value="${item.qty}">
                                    <input type="hidden" name="entries[${Date.now()}][rate]" value="${item.rate}">
                                    <input type="hidden" name="entries[${Date.now()}][freight]" value="${item.freight}">
                                    <input type="hidden" name="entries[${Date.now()}][amount]" value="${amount.toFixed(2)}">
                                </td>
                            </tr>
                        `;
                        
                        $('#entriesBody').append(row);
                        loadedVouchers.set(voucherNo, loadedVouchers.get(voucherNo) + 1);
                    });
                    
                    // Calculate and update grand totals
                    calculateGrandTotals();
                    
                    // Show success alert
                    alert('Data loaded successfully!');
                    
                    // Reset the voucher dropdown
                    $('#v_no').val('').trigger('change');
                } else {
                    alert('No data found for this voucher number');
                }
            },
            error: function() {
                alert('Error loading voucher details');
            },
            complete: function() {
                $('#loadEntry').prop('disabled', false).html('Load');
            }
        });
    }
    
    // Handle delete row button click
    $(document).on('click', '.delete-row', function() {
        const row = $(this).closest('tr');
        const voucherNo = row.data('voucher');
        
        // Remove the row
        row.remove();
        
        // Update loaded vouchers count
        if (loadedVouchers.has(voucherNo)) {
            const currentCount = loadedVouchers.get(voucherNo) - 1;
            if (currentCount <= 0) {
                loadedVouchers.delete(voucherNo);
            } else {
                loadedVouchers.set(voucherNo, currentCount);
            }
        }
        
        // Re-number the remaining rows
        $('#entriesBody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
        rowCounter = $('#entriesBody tr').length + 1;
        
        calculateGrandTotals();
    });
    
    // Function to calculate grand totals
    function calculateGrandTotals() {
        let totalQty = 0;
        let totalRate = 0;
        let totalFreight = 0;
        let totalAmount = 0;
        
        $('#entriesBody tr').each(function() {
            const qty = parseFloat($(this).find('td:eq(7)').text()) || 0;
            const rate = parseFloat($(this).find('td:eq(8)').text()) || 0;
            const freight = parseFloat($(this).find('td:eq(9)').text()) || 0;
            const amount = parseFloat($(this).find('td:eq(10)').text()) || 0;
            
            totalQty += qty;
            totalRate += rate;
            totalFreight += freight;
            totalAmount += amount;
        });
        
        // Update display
        $('#displayGrandTotalQty').text(totalQty.toFixed(2));
        $('#displayGrandTotalRate').text(totalRate.toFixed(2));
        $('#displayGrandTotalFreight').text(totalFreight.toFixed(2));
        $('#displayGrandTotalAmount').text(totalAmount.toFixed(2));
        
        // Update hidden fields
        $('#totalAmount').val(totalAmount.toFixed(2));
    }
    
    // Form submission handler to prevent duplicate submissions
    $('#voucherForm').on('submit', function(e) {
        const accountId = $('#entryParty').val();
        const voucherNos = [];
        
        // Collect all voucher numbers in the table
        $('#entriesBody tr').each(function() {
            const vNo = $(this).data('voucher');
            if (vNo) voucherNos.push(vNo);
        });
        
        if (voucherNos.length === 0) {
            alert('Please add at least one voucher before submitting');
            e.preventDefault();
            return;
        }
        
        // Check if any of the vouchers are already in general_billings
        $.ajax({
            url: '/check-existing-billings',
            type: 'GET',
            async: false, // Make synchronous to wait for response before submitting
            data: {
                account_id: accountId,
                v_nos: voucherNos
            },
            success: function(existingData) {
                if (existingData.length > 0) {
                    const existingVouchers = existingData.map(item => item.v_no).join(', ');
                    alert(`The following vouchers have already been billed and cannot be submitted again: ${existingVouchers}`);
                    e.preventDefault();
                }
            },
            error: function() {
                alert('Error verifying vouchers. Please try again.');
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection