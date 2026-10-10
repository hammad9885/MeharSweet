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
                        <li class="breadcrumb-item active">Purchase Boxboard Invoice</li>
                    </ol>
                </div>
                <h4 class="page-title">Job Sheet</h4>
            </div>
        </div>
    </div>
    <!-- End page title -->

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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <form id="voucherForm" action="{{ route('job.store') }}" method="POST">
                                    @csrf
                               
                                    <div class="col-9">
                                          <div class="row">
<div class="col-md-4 mb-3">
    <label class="form-label">Job Sheet No:</label>
    <input type="text" 
           class="form-control" 
           value="JS-{{ $nextVNo }}" 
           readonly>
</div>


                                        
                                      
                                            <div class="col-md-4 mb-3">
                                                <label for="entryDate" class="form-label">Date</label>
                                                <input type="date" id="entryDate" class="form-control" name="date">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="preparedBy" class="form-label">Prepared By</label>
                                                <input type="text" id="preparedBy" class="form-control"
                                                    value="{{$loggedInUser->name}}" name="prepared_by" readonly>
                                            </div>
                                        </div>

 
                                        <div class="mb-3">
                                            <label for="job_type" class="form-label">Job Sheet Type</label>
                                            <select name="job_type" class="form-control select2" data-toggle="select2"
                                                id="job_type">
                                                <option value="">Select</option>
                                                <option value="Pharmaceutical">Pharmaceutical</option>
                                                <option value="Confectionery">Confectionery</option>
                                            </select>
                                        </div>
<div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="aid" class="form-label">Customer</label>
                                            <select name="aid" class="form-control select2" data-toggle="select2"
                                                id="aid" required>
                                                <option value="">Select</option>
                                                @foreach ($productMasters2 as $productMaster)
                                                <option value="{{ $productMaster->aid }}">
                                                    {{ $productMaster->title }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class=" col-md-6 mb-3">
                                            <label for="entryParty" class="form-label">Product Name</label>
                                            <select name="account" class="form-control select2" data-toggle="select2"
                                                id="entryParty" required>
                                                <option value="">Select</option>
                                                {{-- The options here will be updated dynamically --}}
                                            </select>
                                        </div>
                                        </div>

 <div class="row">
                                        <div class="col-md-7 mb-3">
                                            <label for="item_id" class="form-label">Item Type</label>
                                            <input type="text" id="item_id" class="form-control" name="item_id"
                                                readonly>
                                        </div>

                                       
                                            <div class="col-md-5 mb-3">
                                                <label for="packet_size" class="form-label">Packet Size</label>
                                                <input type="text" id="packet_size" class="form-control"
                                                    name="packet_size" readonly>
                                            </div>
                                            </div>
                                             <div class="row">
                                                 
                                            <div class="col-md-2 mb-3">
                                                <label for="ups" class="form-label">No of Ups</label>
                                                <input type="text" id="ups" class="form-control" name="ups" readonly>
                                            </div>
                                        

                                        <div class="col-md-2 mb-3">
                                            <label for="lam_size" class="form-label">Lamination Size</label>
                                            <input type="text" id="lam_size" class="form-control" name="lam_size"
                                                readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="curr_size" class="form-label">Corrugatin Size</label>
                                            <input type="text" id="curr_size" class="form-control" name="curr_size"
                                                readonly>
                                        </div>
                                        

                                        <div class="col-md-2 mb-3">
                                            <label for="uv" class="form-label">UV</label>
                                            <input type="text" id="uv" class="form-control" name="uv" readonly>
                                        </div>
                                        

                                        <div class="col-md-2 mb-3">
                                            <label for="color_no" class="form-label">Color</label>
                                            <input type="text" id="color_no" class="form-control" name="color_no"
                                                readonly>
                                        </div>

                                            <div class="col-md-2 mb-3">
                                                <label for="simple" class="form-label">UV Simple</label>
                                                <input type="text" id="simple" class="form-control" name="simple"
                                                    readonly>
                                            </div>

                                            <div class="col-md-2 mb-3">
                                                <label for="spot" class="form-label">UV Spot</label>
                                                <input type="text" id="spot" class="form-control" name="spot" readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3" id="image-container" style="display: none;">
                                            <label class="form-label">Preview</label>
                                            <div>
                                                <img id="product-image" src="" alt="Product Image"
                                                    style="max-width: 200px; max-height: 200px;">
                                                <a id="image-link" href="" target="_blank" class="ms-2">View Full
                                                    Image</a>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descr" class="form-label">Description</label>
                                            <textarea id="descr" class="form-control" name="descr" rows="3"
                                                readonly></textarea>
                                        </div>

                                        <hr>
                                        <h3>Enter New Data</h3>
                                        <br>

<div class="row" id="item-row-template" style="display: none;">
    <div class="col-md-6 mb-3">
        <label class="form-label">Item</label>
        <select class="form-control select2 item-selection" >
            <option value="">Select Item</option>
            @foreach($boxboardData as $item)
                <option value="{{ $item->item_id }}_{{ $item->width }}_{{ $item->length }}"
                    data-remain-qty="{{ $item->remain_qty }}"
                    data-item-code="{{ $item->item_code }}">
                    {{ $item->item_code }} (L:{{ $item->length }} x W:{{ $item->width }})
                </option>
            @endforeach
        </select>
        <!-- NO HIDDEN INPUTS HERE -->
    </div>
    
    <div class="col-md-1 mb-3" style="display:none;">
        <label class="form-label">Length</label>
        <input type="number" class="form-control box-length" readonly> <!-- Removed name -->
    </div>
    
    <div class="col-md-1 mb-3" style="display:none;">
        <label class="form-label">Width</label>
        <input type="number" class="form-control box-width" readonly> <!-- Removed name -->
    </div>
    
    <div class="col-md-2 mb-3">
        <label class="form-label">T.Stock</label>
        <input type="number" class="form-control box-total-stock" readonly>
    </div>
    
    <div class="col-md-2 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" class="form-control box-stock"> <!-- Removed name -->
    </div>
    
    <div class="col-md-2 mb-3 d-flex align-items-end justify-content-between">
        <button type="button" class="btn btn-success add-item-row">+</button>
        <button type="button" class="btn btn-danger remove-row">×</button>
    </div>
</div>

<!-- Container where rows will appear (starts with 1 default row) -->
<div id="items-container">
    <!-- Default row will be added via JavaScript -->
</div>


                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="packets" class="form-label">No of Packets to be Used</label>
                                                <input type="text" id="packets" class="form-control" name="packets" readonly>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="product_qty" class="form-label">Box Qty from Packets</label>
                                                <input type="text" id="product_qty" class="form-control"
                                                    name="product_qty" readonly>
                                            </div>
                                        </div>
                                        
                                        

                                        <div id="batch-container">
                                            <div class="row batch-row">
                                                <div class="col-md-1 mb-1 d-flex align-items-center">
                                                    <span class="batch-number">1</span>
                                                </div>
                                                <div class="col-md-5 mb-3">
                                                    <label for="batch_no" class="form-label">Batch No</label>
                                                    <input type="text" class="form-control batch-no" name="batch_no[]">
                                                </div>
                                                <div class="col-md-5 mb-3">
                                                    <label for="batch_qty" class="form-label">Batch Qty</label>
                                                    <input type="text" class="form-control batch-qty"
                                                        name="batch_qty[]">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end mb-3">
                                                    <button type="button" class="btn btn-primary add-batch">+</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="sum_batch_no" class="form-label">Sum of Batch Qty</label>
                                            <input type="text" class="form-control" name="sum_batch_no"
                                                id="sum_batch_no" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="delivery_date" class="form-label">Delivery Date</label>
                                            <input type="date" id="delivery_date" class="form-control"
                                                name="delivery_date">
                                        </div>

                                        <div class="department-container">
    <!-- Initial Department Row -->
    <div class="row department-row">
        <div class="col-md-4 mb-3">
    <label for="department_name" class="form-label">Department</label>
    <select name="department_name[]" class="form-control select2 department_name" data-toggle="select2">
        <option value="">Select</option>
        @php
        $orderedDepartments = [
            'Boxboard Cutting',
            'Solna',
            'Hydelburge',
            'Two colour',
            'Five Color',
            'Lamination',
            'Dye Manual',
            'Dye Automatic',
            'Breaking Department',
            'Pasting Manual',
            'Pasting Automatic',
            'General Staff'
        ];
        
        $uniqueDepartments = $employeeTypes->unique('department_id');
        $remainingDepartments = $uniqueDepartments->filter(function ($item) use ($orderedDepartments) {
            return !in_array($item->department_name, $orderedDepartments);
        });
        @endphp
        
        @foreach ($orderedDepartments as $deptName)
            @foreach ($uniqueDepartments as $department)
                @if ($department->department_name === $deptName)
                    <option value="{{ $department->department_id }}">
                        {{ $department->department_name }}
                    </option>
                    @break
                @endif
            @endforeach
        @endforeach
        
        @foreach ($remainingDepartments as $department)
            <option value="{{ $department->department_id }}">
                {{ $department->department_name }}
            </option>
        @endforeach
    </select>
</div>

        <div class="col-md-3 mb-3">
            <label for="designation_sup" class="form-label">Designation</label>
            <select name="designation_sup[]" class="form-control select2 designation_sup" data-toggle="select2" required>
                <option value="">Select</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label for="employee_sup" class="form-label">Name</label>
            <select name="employee_sup[]" class="form-control select2 employee_sup" data-toggle="select2" required>
                <option value="">Select</option>
            </select>
        </div>

        <div class="col-md-1 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-success add-department">
                <i class="fas fa-plus"></i> +
            </button>
        </div>

        <!-- Process and Dimensions Container (initially hidden) -->
        <div class="col-12 process-dimensions-container" style="display: none; margin-top: 15px;">
            <div class="process-container">
                <div class="row process-row">
                    <div class="col-md-8 mb-3">
                        <label for="department_Process" class="form-label">Process</label>
                        <select name="department_Process[]" class="form-control select2" data-toggle="select2">
                            <option value="">Select</option>
                            @foreach ($employeeProcess->unique('process_name') as $process)
                            <option value="{{ $process->process_name }}">
                                {{ $process->process_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-success btn-sm add-process">
                            <i class="fas fa-plus"></i> +
                        </button>
                    </div>
                </div>
            </div>

            <div class="dimensions-container mt-3">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="length" class="form-label">Length</label>
                        <input type="text" class="form-control length" name="length[]">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="width" class="form-label">Width</label>
                        <input type="text" class="form-control width" name="width[]">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_of_cut" class="form-label">No of sheets from Packets</label>
                        <input type="text" class="form-control no_of_cut" name="no_of_cut[]">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Template for Process Rows -->
<template id="process-template">
    <div class="row process-row">
        <div class="col-md-8 mb-3">
            <label class="form-label">Process</label>
            <select name="department_Process[]" class="form-control select2" data-toggle="select2">
                <option value="">Select</option>
                @foreach ($employeeProcess->unique('process_name') as $process)
                <option value="{{ $process->process_name }}">
                    {{ $process->process_name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-process">
                <i class="fas fa-minus"></i> -
            </button>
        </div>
    </div>
</template>
                                      
                                        <div class="mb-3">
                                            <label for="custom_descr" class="form-label">Description</label>
                                            <textarea id="custom_descr" class="form-control" name="custom_descr"
                                                rows="3"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success">Submit Voucher</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    let today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
    document.getElementById("entryDate").value = today;
    document.getElementById("delivery_date").value = today;
});
        
     
    
  $(document).ready(function(){
    $('#aid').on('change', function(){
        var customerId = $(this).val();
        console.log("Selected aid:", customerId); // Print aid in console

        if(customerId) {
            $.ajax({
                url: '/get-products/' + customerId,  // Ensure correct path
                type: 'GET',
                dataType: 'json',
                success: function(data){
                    console.log("Received products:", data); // Print products in console

                    $('#entryParty').empty();
                    $('#entryParty').append('<option value="">Select</option>');
                    $.each(data, function(key, product){
                        $('#entryParty').append('<option value="'+product.id+'">'+product.prod_name+'</option>');
                    });
                },
                error: function(xhr, status, error){
                    console.log("Error:", xhr.responseText);  // Debugging
                    alert('Unable to retrieve products.');
                }
            });
        } else {
            $('#entryParty').empty();
            $('#entryParty').append('<option value="">Select</option>');
        }
    });
});

//BAtch

//department 

$(document).ready(function () {
    // Initialize all select2 elements
    $('.select2').select2();

    // Convert PHP collection to JS array with employees
    var employees = @json($employeeTypes);
    var departmentContainer = $('.department-container');
    
    // Product selection change handler
    $('#entryParty').change(function () {
        var productId = $(this).val();

        if (productId) {
            $.ajax({
                url: "{{ route('get.product.details') }}",
                type: "GET",
                data: { id: productId },
                dataType: "json",
                success: function (data) {
                    if (data.error) {
                        console.log("Error: " + data.error);
                    } else {
                        // Populate form fields
                        $('#item_id').val(data.item_code);
                        $('#ups').val(data.ups);
                        $('#lam_size').val(data.lam_size);
                        $('#curr_size').val(data.curr_size);
                        $('#uv').val(data.uv);
                        $('#simple').val(data.simple);
                        $('#spot').val(data.spot);
                        $('#color_no').val(data.color_no);
                        $('#descr').val(data.descr);
                        $('#file_path').val(data.file_path);
                        $('#packet_size').val(data.packet_size);
                        $('#product_qty').val(data.product_qty);

                        // Handle image display
                        if (data.file_path) {
                            var imageUrl = "{{ asset('/storage/') }}" + '/' + data.file_path;
                            $('#product-image').attr('src', imageUrl);
                            $('#image-link').attr('href', imageUrl);
                            $('#image-container').show();
                        } else {
                            $('#product-image').attr('src', '');
                            $('#image-link').attr('href', '');
                            $('#image-container').hide();
                        }

                        // Check the UV value
                        if (data.uv == 0) {
                            $('#simple').closest('.mb-3').hide();
                            $('#spot').closest('.mb-3').hide();
                            $('#uv').closest('.mb-3').hide();
                        } else {
                            $('#simple').closest('.mb-3').show();
                            $('#spot').closest('.mb-3').show();
                            $('#uv').closest('.mb-3').show();
                        }
                        
                        if (data.lam_size == null) {
                            $('#lam_size').closest('.mb-3').hide();
                        } else {
                            $('#lam_size').closest('.mb-3').show();
                        }
                        
                        if (data.curr_size == null) {
                            $('#curr_size').closest('.mb-3').hide();
                        } else {
                            $('#curr_size').closest('.mb-3').show();
                        }
                        
                        if (data.color_no == null) {
                            $('#color_no').closest('.mb-3').hide();
                        } else {
                            $('#color_no').closest('.mb-3').show();
                        }
                        
                        // After setting values, check and add departments
                        checkAndAddDepartments();
                    }
                },
                error: function (xhr, status, error) {
                    console.log("AJAX Error:", error);
                    console.log("Response:", xhr.responseText);
                }
            });
        } else {
            // Clear form fields
            $('#item_id, #ups, #lam_size, #curr_size, #uv, #simple, #spot, #file_path, #color_no, #descr, #packet_size, #product_qty').val('');
            $('#product-image').attr('src', '');
            $('#image-link').attr('href', '');
            $('#image-container').hide();
            
            // Ensure UV-related fields are visible
            $('.mb-3').show();
        }
    });
    // Function to check and add departments based on input values
    function checkAndAddDepartments() {
        var lamSize = $('#lam_size').val();
        var currSize = $('#curr_size').val();
        
        if (lamSize && lamSize.trim() !== '') {
            addEmptyDepartmentRow(22); // Lamination
        }
        
        if (currSize && currSize.trim() !== '') {
            addEmptyDepartmentRow(13); // Corrugation
        }
    }
    
    // Add an empty department row
    function addEmptyDepartmentRow(suggestedDeptId) {
        var exists = $('.department_name').filter(function() {
            return $(this).val() == suggestedDeptId;
        }).length > 0;
        
        if (exists) return;
        
        var newRow = `
        <div class="row department-row mt-2">
            <div class="col-md-4 mb-3">
                <select name="department_name[]" class="form-control select2 department_name" data-toggle="select2">
                    <option value="">Select Department</option>
                    @foreach ($uniqueDepartments as $department)
                    <option value="{{ $department->department_id }}" ${'{{ $department->department_id }}' == suggestedDeptId ? 'selected' : ''}>
                        {{ $department->department_name }}
                    </option>
                    @endforeach
                </select>
            </div>
                    
            <div class="col-md-3 mb-3">
                <select name="designation_sup[]" class="form-control select2 designation_sup" data-toggle="select2" required>
                    <option value="">Select</option>
                </select>
            </div>
            
            <div class="col-md-4 mb-3">
                <select name="employee_sup[]" class="form-control select2 employee_sup" data-toggle="select2" required>
                    <option value="">Select</option>
                </select>
            </div>
            
            <div class="col-md-1 mb-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-department">
                    <i class="fas fa-minus"></i> -
                </button>
            </div>
            
            <!-- Process and Dimensions Container -->
            <div class="col-12 process-dimensions-container" style="display: none; margin-top: 15px;">
                <div class="process-container">
                    <div class="row process-row">
                        <div class="col-md-8 mb-3">
                            <label for="department_Process" class="form-label">Process</label>
                            <select name="department_Process[]" class="form-control select2" data-toggle="select2">
                                <option value="">Select</option>
                                @foreach ($employeeProcess->unique('process_name') as $process)
                                <option value="{{ $process->process_name }}">
                                    {{ $process->process_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-success btn-sm add-process">
                                <i class="fas fa-plus"></i> +
                            </button>
                        </div>
                    </div>
                </div>

                <div class="dimensions-container mt-3">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="length" class="form-label">Length</label>
                            <input type="text" class="form-control length" name="length[]">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="width" class="form-label">Width</label>
                            <input type="text" class="form-control width" name="width[]">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="no_of_cut" class="form-label">No of sheets Cuts</label>
                            <input type="text" class="form-control no_of_cut" name="no_of_cut[]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        departmentContainer.append(newRow);
        
        // Initialize Select2 for the new elements
        departmentContainer.find('.department-row:last .select2').select2();
        
        // Trigger change if department is pre-selected
        var select = departmentContainer.find('.department-row:last .department_name');
        if (select.val() == suggestedDeptId) {
            select.trigger('change');
        }
    }
    
    // Toggle process and dimensions containers
    function toggleProcessAndDimensions(deptId, row) {
        var container = row.find('.process-dimensions-container');
        container.toggle(deptId == 14); // Only show for Cutting Department (ID 14)
        
        if (deptId != 14) {
            container.find('input.length, input.width, input.no_of_cut').val('');
            container.find('.process-row:not(:first)').remove();
        }
    }
    
    // Department change handler
    $(document).on('change', '.department_name', function() {
        var deptId = parseInt($(this).val());
        var row = $(this).closest('.department-row');
        var designationSelect = row.find('.designation_sup');
        var employeeSelect = row.find('.employee_sup');
        
        // Reset downstream selects
        designationSelect.empty().append('<option value="">Select</option>');
        employeeSelect.empty().append('<option value="">Select</option>');
        
        // Show/hide process and dimensions
        toggleProcessAndDimensions(deptId, row);
        
        if (deptId) {
            // Filter employees by department
            var deptEmployees = employees.filter(emp => emp.department_id == deptId);
            
            // Apply department-specific rules
            if ([14,13,18,19,21].includes(deptId)) {
                // Only show designation 10
                var designation = deptEmployees.find(emp => emp.designation_id == 10);
                if (designation) {
                    designationSelect.append(
                        $('<option></option>')
                            .val(10)
                            .text(designation.designation_name)
                    ).val(10).trigger('change');
                }
            }
            else if ([28,31,22,20].includes(deptId)) {
                // Only show designation 6
                var designation = deptEmployees.find(emp => emp.designation_id == 6);
                if (designation) {
                    designationSelect.append(
                        $('<option></option>')
                            .val(6)
                            .text(designation.designation_name)
                    ).val(6).trigger('change');
                }
            }
            else if ([25,26,29,23].includes(deptId)) {
                // Show designations 6 and 5
                var designations = [];
                deptEmployees.forEach(emp => {
                    if ((emp.designation_id == 6 || emp.designation_id == 5) && 
                        !designations.some(d => d.id === emp.designation_id)) {
                        designations.push({
                            id: emp.designation_id,
                            name: emp.designation_name
                        });
                    }
                });
                
                designations.forEach(designation => {
                    designationSelect.append(
                        $('<option></option>')
                            .val(designation.id)
                            .text(designation.name)
                    );
                });
            }
            else {
                // Show all designations
                var designations = [];
                deptEmployees.forEach(emp => {
                    if (!designations.some(d => d.id === emp.designation_id)) {
                        designations.push({
                            id: emp.designation_id,
                            name: emp.designation_name
                        });
                    }
                });
                
                designations.forEach(designation => {
                    designationSelect.append(
                        $('<option></option>')
                            .val(designation.id)
                            .text(designation.name)
                    );
                });
            }
        }
        
        // Refresh select2
        designationSelect.trigger('change.select2');
        employeeSelect.trigger('change.select2');
    });
    
    // Designation change handler
    $(document).on('change', '.designation_sup', function() {
        var designationId = parseInt($(this).val());
        var row = $(this).closest('.department-row');
        var deptId = parseInt(row.find('.department_name').val());
        var employeeSelect = row.find('.employee_sup');
        
        employeeSelect.empty().append('<option value="">Select</option>');
        
        if (deptId && designationId) {
            // Filter employees based on department and designation
            var filteredEmployees = employees.filter(emp => 
                emp.department_id == deptId && 
                emp.designation_id == designationId
            );
            
            // Populate employees
            filteredEmployees.forEach(emp => {
                employeeSelect.append(
                    $('<option></option>')
                        .val(emp.cnic_no)
                        .text(emp.employee_name)
                );
            });
        }
        
        employeeSelect.trigger('change.select2');
    });
    
    // Add new department row
    $(document).on('click', '.add-department', function() {
        addEmptyDepartmentRow();
    });
    
    // Remove department row
    $(document).on('click', '.remove-department', function() {
        $(this).closest('.department-row').remove();
    });
    
    // Add process row
    $(document).on('click', '.add-process', function() {
        var template = $('#process-template').html();
        var container = $(this).closest('.process-container');
        container.append(template);
        
        // Initialize Select2 for the new element
        container.find('.select2').last().select2();
    });
    
    // Remove process row
    $(document).on('click', '.remove-process', function() {
        $(this).closest('.process-row').remove();
    });
    
    // Initial check when page loads
    checkAndAddDepartments();
});
// po and batch

$(document).ready(function() {
    // Hide batch container initially
    $('#batch-container').hide();
    $('#sum_batch_no').closest('.mb-3').hide();

    // When job type changes
    $('#job_type').change(function() {
        const selectedType = $(this).val();
        
        if (selectedType === 'Pharmaceutical') {
            // Show pharmaceutical fields (Batch)
            $('#batch-container').html(`
                <div class="row batch-row">
                    <div class="col-md-1 mb-1 d-flex align-items-center">
                        <span class="batch-number">1</span>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batch_no" class="form-label">Batch No</label>
                        <input type="text" class="form-control batch-no" name="batch_no[]">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batch_qty" class="form-label">Batch Qty</label>
                        <input type="text" class="form-control batch-qty" name="batch_qty[]">
                    </div>
                    <div class="col-md-1 d-flex align-items-end mb-3">
                        <button type="button" class="btn btn-primary add-batch">+</button>
                    </div>
                </div>
            `);
            
            $('#sum_batch_no').closest('.mb-3').find('label').text('Sum of Batch Qty');
            $('#batch-container').show();
            $('#sum_batch_no').closest('.mb-3').show();
            
        } else if (selectedType === 'Confectionery') {
            // Show confectionery fields (PO)
            $('#batch-container').html(`
                <div class="row batch-row">
                    <div class="col-md-1 mb-1 d-flex align-items-center">
                        <span class="batch-number">1</span>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batch_no" class="form-label">PO No</label>
                        <input type="text" class="form-control batch-no" name="batch_no[]">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batch_qty" class="form-label">PO Qty</label>
                        <input type="text" class="form-control batch-qty" name="batch_qty[]">
                    </div>
                    <div class="col-md-1 d-flex align-items-end mb-3">
                        <button type="button" class="btn btn-primary add-batch">+</button>
                    </div>
                </div>
            `);
            
            // Hide the sum field for PO mode
            $('#sum_batch_no').closest('.mb-3').hide();
            $('#batch-container').show();
            
        } else {
            // Hide if nothing selected
            $('#batch-container').hide();
            $('#sum_batch_no').closest('.mb-3').hide();
        }
        
        // Reinitialize any event handlers
        initAddBatchHandler();
        initBatchQtyHandlers();
    });
    
    function initAddBatchHandler() {
        $('.add-batch').off('click').on('click', function() {
            const container = $('#batch-container');
            const rowCount = container.find('.batch-row').length;
            const isPharmaceutical = $('#job_type').val() === 'Pharmaceutical';
            
            const newRow = $(`
                <div class="row batch-row">
                    <div class="col-md-1 mb-1 d-flex align-items-center">
                        <span class="batch-number">${rowCount + 1}</span>
                    </div>
                    <div class="col-md-5 mb-3">
                        <input type="text" class="form-control batch-no" name="batch_no[]">
                    </div>
                    <div class="col-md-5 mb-3">
                        <input type="text" class="form-control batch-qty" name="batch_qty[]">
                    </div>
                    <div class="col-md-1 d-flex align-items-end mb-3">
                        <button type="button" class="btn btn-danger remove-batch">-</button>
                    </div>
                </div>
            `);
            container.append(newRow);
            
            // Add handler for remove button
            newRow.find('.remove-batch').click(function() {
                $(this).closest('.batch-row').remove();
                updateBatchNumbers();
                // Only calculate total if in Pharmaceutical mode
                if ($('#job_type').val() === 'Pharmaceutical') {
                    calculateTotalBatchQty();
                }
            });
            
            // Initialize quantity change handler for the new row
            newRow.find('.batch-qty').on('input', function() {
                // Only calculate total if in Pharmaceutical mode
                if ($('#job_type').val() === 'Pharmaceutical') {
                    calculateTotalBatchQty();
                }
            });
        });
    }
    
    function initBatchQtyHandlers() {
        // Initialize quantity change handlers for existing batch quantity inputs
        $('#batch-container').on('input', '.batch-qty', function() {
            // Only calculate total if in Pharmaceutical mode
            if ($('#job_type').val() === 'Pharmaceutical') {
                calculateTotalBatchQty();
            }
        });
    }
    
    function updateBatchNumbers() {
        $('#batch-container .batch-row').each(function(index) {
            $(this).find('.batch-number').text(index + 1);
        });
    }
    
    function calculateTotalBatchQty() {
        let total = 0;
        $('.batch-qty').each(function() {
            const value = parseFloat($(this).val()) || 0;
            total += value;
        });
        $('#sum_batch_no').val(total);
    }
    
    // Initialize handlers on page load
    initAddBatchHandler();
    initBatchQtyHandlers();
});    

// Boxboard

$(document).ready(function() {
    // Initialize the first row
    addItemRow();

    // Add row button
    $(document).on('click', '.add-item-row', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        addItemRow();
    });

    // Remove row button
    $(document).on('click', '.remove-row', function(e) {
        e.preventDefault();
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        } else {
            alert("You must keep at least 1 row.");
        }
    });

    // Item selection change
    $(document).on('change', '.item-selection', function() {
        const row = $(this).closest('.item-row');
        const value = $(this).val();
        
        if (value) {
            const parts = value.split('_');

            // Add name attributes
            row.find('.box-length').attr('name', 'box_length[]');
            row.find('.box-width').attr('name', 'box_width[]');
            row.find('.box-stock').attr('name', 'box_qty[]');

            // Remove old hidden input if exists
            row.find('input[name="box_item[]"]').remove();

            // Add hidden input for item ID
            row.append(`<input type="hidden" name="box_item[]" value="${parts[0]}">`);

            // Set values
            row.find('.box-width').val(parts[1]);
            row.find('.box-length').val(parts[2]);
            row.find('.box-total-stock').val($(this).find('option:selected').data('remain-qty'));
        } else {
            // Remove name attributes and values if no item selected
            row.find('.box-length, .box-width, .box-stock').removeAttr('name');
            row.find('input[name="box_item[]"]').remove();
            row.find('.box-width, .box-length, .box-total-stock, .box-stock').val('');
        }
    });

    function addItemRow() {
        // Step 1: Clone the template
        const tempRow = $('#item-row-template').clone()
            .removeAttr('id')
            .removeAttr('style')
            .addClass('item-row');

        tempRow.find('select').val('').trigger('change');
        tempRow.find('input').val('');

        // Initialize select2 on first add
        tempRow.find('.item-selection').select2({
            placeholder: "Select Item",
            allowClear: true
        });

        // Append it once
        $('#items-container').append(tempRow);

        // Step 2: Remove and re-add to avoid double dropdown bug
        setTimeout(() => {
            tempRow.remove();

            const cleanRow = $('#item-row-template').clone()
                .removeAttr('id')
                .removeAttr('style')
                .addClass('item-row');

            cleanRow.find('select').val('').trigger('change');
            cleanRow.find('input').val('');

            cleanRow.find('.item-selection').select2({
                placeholder: "Select Item",
                allowClear: true
            });

            $('#items-container').append(cleanRow);
        }, 10);
    }

    // Stock validation
    $(document).on('input', '.box-stock', function() {
        const row = $(this).closest('.item-row');
        const stockInput = $(this);
        const totalStock = parseFloat(row.find('.box-total-stock').val()) || 0;
        const enteredStock = parseFloat(stockInput.val()) || 0;

        if (enteredStock > totalStock) {
            alert("You cannot enter more than total available stock.");
            stockInput.val(totalStock);
        }
         updatePacketSum();
    });
});

function updatePacketSum() {
    let totalStock = 0;

    $('.box-stock').each(function() {
        const val = parseFloat($(this).val());
        if (!isNaN(val)) {
            totalStock += val;
        }
    });

    $('#packets').val(totalStock);

    // Optional: If you want to auto-recalculate product quantity too
    calculateProductQty();
}


function calculateProductQty() {
        let packets = parseFloat(document.getElementById('packets').value) || 0;
        let ups = parseFloat(document.getElementById('ups').value) || 0;
        document.getElementById('product_qty').value = packets * ups * 100;
    }

    document.getElementById('packets').addEventListener('input', calculateProductQty);
    document.getElementById('ups').addEventListener('input', calculateProductQty);


$('form').on('submit', function(e) {
    $('.item-row').each(function() {
        const $row = $(this);
        const $select = $row.find('.item-selection');
        
        if (!$select.val()) {
            $row.find('input, select').remove();
        }
    });
});
</script>
@endsection