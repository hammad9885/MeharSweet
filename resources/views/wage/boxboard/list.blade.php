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
                        <li class="breadcrumb-item active">Boxboard Department</li>
                    </ol>
                </div>
                <h4 class="page-title">Boxboard Department</h4>
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
                            <form id="voucherForm" action="{{ route('boxboard_wage.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-6">

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
    <label for="account_id" class="form-label">Employee</label>
    <select name="account_id" class="form-control select2" id="account_id" data-toggle="select2">
        <option value="">Select</option>
        @php
            $uniqueEmployees = [];
            foreach ($employees as $employee) {
                if (!isset($uniqueEmployees[$employee->employee_id])) {
                    $uniqueEmployees[$employee->employee_id] = $employee;
                }
            }
        @endphp
        @foreach ($uniqueEmployees as $employee)
            <option value="{{ $employee->employee_id }}">{{ $employee->employee_name }}</option>
        @endforeach
    </select>
</div>

<!-- Voucher No Selection -->
<div class="mb-3">
    <label for="v_no" class="form-label">JS No</label>
    <select name="v_no" class="form-control select2" data-toggle="select2" id="v_no">
        <option value="">Select Employee First</option>
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
            <th>JS No</th>
            <th>Employee</th>
            <th>Process Name</th>
            <th>Process Rate</th>
            <th>Packets</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="entriesBody">
        <!-- Entries will appear here -->
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" class="text-end">Grand Totals:</th>
            <th id="grandTotalAmount">0.00</th>
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
      document.getElementById("entryDate").value = new Date().toISOString().split('T')[0];
$(document).ready(function() {
    // Function to calculate and update grand total
    function updateGrandTotal() {
        let total = 0;
        $('#entriesBody tr').each(function() {
            const amount = parseFloat($(this).find('td:eq(5)').text()) || 0;
            total += amount;
        });
        $('#grandTotalAmount').text(total.toFixed(2));
    }

    // Initialize Select2
    $('.select2').select2();
    
    // When Party changes, load Voucher Nos
    $('#account_id').change(function() {
        const employeeId = $(this).val();
        const vNoDropdown = $('#v_no');

        vNoDropdown.empty().append('<option value="">Loading...</option>');
        vNoDropdown.prop('disabled', true);

        if (employeeId) {
            const url = "{{ route('boxboard_wage.vouchers', ['employee_id' => 'PLACEHOLDER']) }}"
                .replace('PLACEHOLDER', employeeId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    vNoDropdown.empty().append('<option value="">Select Voucher No</option>');
                    $.each(data, function(key, voucher) {
                        // Check if this voucher is already loaded
                        const alreadyLoaded = $(`#entriesBody tr[data-vno="${voucher.v_no}"]`).length > 0;
                        if (!alreadyLoaded) {
                            vNoDropdown.append(`<option value="${voucher.v_no}">${voucher.v_no}</option>`);
                        }
                    });
                    vNoDropdown.prop('disabled', false);
                },
                error: function() {
                    vNoDropdown.empty().append('<option value="">Failed to load vouchers</option>');
                    vNoDropdown.prop('disabled', false);
                }
            });
        } else {
            vNoDropdown.empty().append('<option value="">Select Party First</option>');
            vNoDropdown.prop('disabled', true);
        }
    });

    // When Load button is clicked
    $('#loadEntry').click(function() {
        const employeeId = $('#account_id').val();
        const vNo = $('#v_no').val();
        const entriesBody = $('#entriesBody');
        
        if (!employeeId || !vNo) {
            alert('Please select both Party and Voucher No');
            return;
        }

        // Check if this voucher is already loaded
        if ($(`#entriesBody tr[data-vno="${vNo}"]`).length > 0) {
            alert(`Voucher No ${vNo} is already loaded!`);
            return;
        }

        // Show loading for this specific row
        entriesBody.append(`<tr data-vno="${vNo}"><td colspan="7" class="text-center">Loading Voucher ${vNo}...</td></tr>`);

        const url = "{{ route('boxboard_wage.details', ['employee_id' => 'EMPLOYEE_ID', 'v_no' => 'V_NO']) }}"
            .replace('EMPLOYEE_ID', employeeId)
            .replace('V_NO', vNo);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                // Remove the loading row
                $(`#entriesBody tr[data-vno="${vNo}"]`).remove();
                
                if (data.length === 0) {
                    entriesBody.append(`<tr data-vno="${vNo}"><td colspan="7" class="text-center">No records found for Voucher ${vNo}</td></tr>`);
                    return;
                }

                let html = '';
                data.forEach(function(item) {
                    html += `
                    <tr data-vno="${item.v_no}">
    <td><input type="hidden" name="v_no[]" value="${item.v_no}">${item.v_no}</td>
    <td><input type="hidden" name="employee_name[]" value="${item.employee_name}">${item.employee_name}</td>
    <td><input type="hidden" name="process_name[]" value="${item.process_name}">${item.process_name}</td>
    <td><input type="hidden" name="process_rate[]" value="${item.process_rate}">${item.process_rate}</td>
    <td><input type="hidden" name="packets[]" value="${item.packets}">${item.packets}</td>
    <td><input type="hidden" name="amount[]" value="${item.boxboard_wage}">${item.boxboard_wage}</td>
    <td><button class="btn btn-sm btn-danger remove-row">Remove</button></td>
</tr>`;
                });

                entriesBody.append(html);
                alert(`Voucher No ${vNo} loaded successfully!`);
                
                // Update grand total
                updateGrandTotal();
                
                // Add event listener for remove buttons
                $('.remove-row').off('click').on('click', function() {
                    const vNoToRemove = $(this).closest('tr').data('vno');
                    $(this).closest('tr').remove();
                    // Re-enable this voucher in dropdown
                    $(`#v_no option[value="${vNoToRemove}"]`).remove();
                    $('#v_no').append(`<option value="${vNoToRemove}">${vNoToRemove}</option>`);
                    // Update grand total after removal
                    updateGrandTotal();
                });
            },
            error: function() {
                $(`#entriesBody tr[data-vno="${vNo}"]`).remove();
                entriesBody.append(`<tr data-vno="${vNo}"><td colspan="7" class="text-center">Error loading Voucher ${vNo}</td></tr>`);
            }
        });
    });
});
</script>
@endsection