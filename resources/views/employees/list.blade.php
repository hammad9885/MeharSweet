@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Employee Registration</h4>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="voucherForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-6">
                         <div class="mb-3">
                                                <label for="joining_date" class="form-label">Joining Date</label>
                                                <input type="date" id="joining_date" class="form-control" name="joining_date">
                                            </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">Name</label>
                                <input type="text" id="fname" class="form-control" name="fname">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lname" class="form-label">Father Name</label>
                                <input type="text" id="lname" class="form-control" name="lname">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone_no" class="form-label">Phone No</label>
                                <input type="text" id="phone_no" class="form-control" name="phone_no">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <input type="text" id="blood_group" class="form-control" name="blood_group">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" class="form-control" name="address">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="employee" class="form-label">Employee</label>
                                <select name="employee" class="form-control select2" data-toggle="select2" id="employee">
                                    <option value="">Select</option>
                                    <option value="offcial">Official</option>
                                    <option value="unoffcial">Un-Official</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
    <label for="cnic_no" class="form-label">CNIC</label>
    <input type="text" id="cnic_no" class="form-control" name="cnic_no" 
           maxlength="13" pattern="[0-9]{13}" 
           oninput="this.value = this.value.replace(/[^0-9]/g, '');"
           title="Please enter exactly 13 digits (numbers only)">
</div>

                        <div class="mb-3">
                            <label for="uploadFileFront" class="form-label">CNIC Front Side</label>
                            <input type="file" id="uploadFileFront" class="form-control" name="fileFront" accept="image/*">
                            <div id="filePreviewContainerFront" class="mt-2" style="display:none;">
                                <img id="imagePreviewFront" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px; display:none;">
                                <span id="fileNamePreviewFront" style="font-size:14px;"></span>
                                <button type="button" id="removeFileFront" class="btn btn-sm btn-danger">X</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="uploadFileBack" class="form-label">CNIC Back Side</label>
                            <input type="file" id="uploadFileBack" class="form-control" name="fileBack" accept="image/*">
                            <div id="filePreviewContainerBack" class="mt-2" style="display:none;">
                                <img id="imagePreviewBack" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px; display:none;">
                                <span id="fileNamePreviewBack" style="font-size:14px;"></span>
                                <button type="button" id="removeFileBack" class="btn btn-sm btn-danger">X</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bonus_id" class="form-label">Bonus Type</label>
                                <select name="bonus_id" id="bonus_id" class="form-control select2" data-toggle="select2" required>
                                    <option value="">Select</option>
                                    @foreach ($extratimes as $extratime)
                                    <option value="{{ $extratime->id }}" {{ old('id')==$extratime->id ? 'selected' : '' }}>
                                        {{ $extratime->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col-md-6 mb-3">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="number" id="rate" class="form-control" name="rate">
                            </div>
                        </div>
                        
                        
                        
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- jQuery (must come first) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('joining_date').value = today;

$(document).ready(function() {
    console.log("1. Document ready");
    
    // Test jQuery is working
    if (typeof jQuery == 'undefined') {
        console.error("jQuery not loaded!");
        return;
    }
    
    // Test Select2 is available
    if (typeof $.fn.select2 == 'undefined') {
        console.error("Select2 not loaded!");
        return;
    }
    
    // Initialize Select2
    try {
        $('#bonus_id').select2({
            placeholder: "Select Bonus Type",
            allowClear: true,
            width: '100%'
        });
        console.log("2. Select2 initialized");
    } catch (e) {
        console.error("Select2 init error:", e);
        return;
    }
    
    // Change event handler
    $('#bonus_id').on('change', function(e) {
        console.log("3. Change event triggered", e);
        
        var bonusId = $(this).val();
        console.log("4. Selected ID:", bonusId);
        
        if (!bonusId) {
            console.log("5. No ID selected - clearing rate");
            $('#rate').val('');
            return;
        }
        
        console.log("6. Making AJAX request...");
        
        $.ajax({
            url: '/extra-times/' + bonusId,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                console.log("7. AJAX request started");
            },
            success: function(response) {
                console.log("8. AJAX success:", response);
                if (response && response.success) {
                    $('#rate').val(response.rate);
                    console.log("9. Rate updated to:", response.rate);
                } else {
                    console.error("10. Invalid response format");
                }
            },
            error: function(xhr, status, error) {
                console.error("11. AJAX error:", status, error);
                console.log("12. Full response:", xhr.responseText);
            },
            complete: function() {
                console.log("13. AJAX request completed");
            }
        });
    });
    
    // Initial trigger
    @if(old('bonus_id'))
        console.log("14. Triggering initial change for old value");
        $('#bonus_id').trigger('change');
    @endif
});
</script>
@endsection

