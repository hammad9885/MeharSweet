@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Employee Registration</h4>
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
                <form id="voucherForm" action="{{ route('employee_type.update', $employeeType->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="col-6">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cnic_no" class="form-label">CNIC</label>
                                <select name="cnic_no" id="cnic_no" class="form-control select2" data-toggle="select2" required>
                                    <option value="">Select</option>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $employeeType->cnic_no == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->cnic_no }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">Name</label>
                                <input type="text" id="fname" class="form-control" name="fname" value="{{ $employeeType->fname }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select name="department_id" id="department_id" class="form-control select2" data-toggle="select2" required>
                                    <option value="">Select</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ $employeeType->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="designation_id" class="form-label">Designation</label>
                                <select name="designation_id" id="designation_id" class="form-control select2" data-toggle="select2" required>
                                    <option value="">Select</option>
                                    @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}" {{ $employeeType->designation_id == $designation->id ? 'selected' : '' }}>
                                        {{ $designation->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="salary_type" class="form-label">Salary Type</label>
                            <select name="salary_type" class="form-control" id="salary_type">
                                <option value="">Select</option>
                                <option value="salary" {{ $employeeType->salary_type == 'salary' ? 'selected' : '' }}>Salary</option>
                                <option value="waje" {{ $employeeType->salary_type == 'waje' ? 'selected' : '' }}>Wage</option>
                            </select>
                        </div>

                        <!-- Salary Section -->
                        <div id="salary_section" style="display: none;">
                            <div class="mb-3">
                                <label for="salary_amount" class="form-label">Salary Amount</label>
                                <input type="number" id="salary_amount" class="form-control" name="salary_amount" step="any" value="{{ $employeeType->salary_amount }}">
                            </div>

                            <div class="mb-3">
    <label class="form-label">Shift Time</label>
    <div class="row">
        <div class="col-6">
            <label for="shift_from" class="form-label">From</label>
            <input type="time" id="shift_from" class="form-control" name="shift_from" 
                   value="{{ $employeeType->shift_from ? \Carbon\Carbon::parse($employeeType->shift_from)->format('H:i') : '' }}">
        </div>
        <div class="col-6">
            <label for="shift_to" class="form-label">To</label>
            <input type="time" id="shift_to" class="form-control" name="shift_to" 
                   value="{{ $employeeType->shift_to ? \Carbon\Carbon::parse($employeeType->shift_to)->format('H:i') : '' }}">
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Break Time</label>
    <div class="row">
        <div class="col-6">
            <label for="break_from" class="form-label">From</label>
            <input type="time" id="break_from" class="form-control" name="break_from" 
                   value="{{ $employeeType->break_from ? \Carbon\Carbon::parse($employeeType->break_from)->format('H:i') : '' }}">
        </div>
        <div class="col-6">
            <label for="break_to" class="form-label">To</label>
            <input type="time" id="break_to" class="form-control" name="break_to" 
                   value="{{ $employeeType->break_to ? \Carbon\Carbon::parse($employeeType->break_to)->format('H:i') : '' }}">
        </div>
    </div>
</div>
                        </div>

                        <!-- Waje Section for Departments 23, 25, 29, 26, etc. -->
                        <div id="waje_section_solna" style="display: none;">
                            <div class="mb-3">
                                <label for="basic_salary" class="form-label">Basic salary</label>
                                <input type="number" id="basic_salary" class="form-control" name="basic_salary" step="any" value="{{ $employeeType->basic_salary }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="fix_impression_day" class="form-label">Fix Impression per Day</label>
                                <input type="number" id="fix_impression_day" class="form-control" name="fix_impression_day" step="any" value="{{ $employeeType->fix_impression_day }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="lam_working_days" class="form-label">Working day in a Month</label>
                                <input type="number" id="lam_working_days" class="form-control" name="lam_working_days" step="any" value="{{ $employeeType->lam_working_days }}">
                            </div>

                            <div class="mb-3">
                                <label for="over_time" class="form-label">Over Time salary per Month</label>
                                <input type="number" id="over_time" class="form-control" name="over_time" step="any" value="{{ $employeeType->over_time }}">
                            </div>
                        </div>

                        <!-- Waje Section for Department 14 -->
    <div id="waje_section_box" style="display: none;">
    <div class="mb-3">
        <label class="form-label">Process Details</label>
        <div id="process-container">
            <!-- Initial process row -->
            <div class="row process-row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="process_name[]" placeholder="Process Name">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="process_rate[]" placeholder="Process Rate">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary add-process">+</button>
                </div>
            </div>
        </div>
    </div>
</div>

                        <!-- Waje Section for Departments 13, 18, 19 and Designation 10 -->
                        <div id="waje_section_cor" style="display: none;">
                            <div class="mb-3">
                                <p>Do not Need - you're already registered</p>
                            </div>
                        </div>

                        <!-- Additional field for Department 21 -->
                        <div class="mb-3" id="per_sheet_rate_container" style="display: none;">
                            <label for="per_sheet_rate" class="form-label">Per sheet rate</label>
                            <input type="number" id="per_sheet_rate" class="form-control" name="per_sheet_rate" step="any" value="{{ $employeeType->per_sheet_rate }}">
                        </div>
                        
                        <div class="mb-3">
                                <label for="salary_cal" class="form-label">Salary Calculate At</label>
                                <select name="salary_cal" class="form-control select2" data-toggle="select2" id="salary_cal">
                                    <option value="">Select</option>
                                    <option value="monthly" {{ old('salary_cal', $employeeType->salary_cal) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="weekly" {{ old('salary_cal', $employeeType->salary_cal) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="daily" {{ old('salary_cal', $employeeType->salary_cal) == 'daily' ? 'selected' : '' }}>Daily</option>
                                </select>
                            </div>
                            
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2 dropdowns first
    $('.select2').select2();

    // Get all form elements using jQuery for consistency
    const salaryType = $('#salary_type');
    const departmentSelect = $('#department_id');
    const designationSelect = $('#designation_id');
    
    // Get all section containers using jQuery
    const salarySection = $('#salary_section');
    const wajeSectionSolna = $('#waje_section_solna');
    const wajeSectionBox = $('#waje_section_box');
    const wajeSectionCor = $('#waje_section_cor');
    const perSheetRateContainer = $('#per_sheet_rate_container');

    // Define department groups
    const SOLNA_DEPTS = ['23', '25', '26', '29', '22', '28', '31'];
    const BOX_DEPT = '14';
    const SHEET_RATE_DEPT = '21';
    const COR_DEPTS = ['13', '18', '19', '20', '33'];

    // Function to completely reset all sections
    function resetAllSections() {
        salarySection.hide();
        wajeSectionSolna.hide();
        wajeSectionBox.hide();
        wajeSectionCor.hide();
        perSheetRateContainer.hide();
    }

    // Function to handle waje section visibility
    function updateWajeSections() {
        resetAllSections();
        
        const deptId = departmentSelect.val();
        const desigId = designationSelect.val();
        
        if (SOLNA_DEPTS.includes(deptId)) {
            wajeSectionSolna.show();
        } 
        else if (deptId === BOX_DEPT) {
            wajeSectionBox.show();
            initializeProcessContainer(); // Initialize process container when box section is shown
        } 
        else if (COR_DEPTS.includes(deptId) && desigId === '10') {
            wajeSectionCor.show();
        }
        else if (deptId === SHEET_RATE_DEPT) {
            perSheetRateContainer.show();
        }
    }

    // Function to initialize the process container
    function initializeProcessContainer() {
        function parseProcessData(data) {
            if (typeof data === 'string') {
                try {
                    return JSON.parse(data);
                } catch (e) {
                    return data.split(',').filter(item => item.trim() !== '');
                }
            }
            return data || [];
        }

        const processNames = parseProcessData('<?php echo $employeeType->process_name ?? "[]"; ?>');
        const processRates = parseProcessData('<?php echo $employeeType->process_rate ?? "[]"; ?>');
        
        // Clear container
        $('#process-container').empty();

        // Add rows for each process
        processNames.forEach((name, i) => {
            const rate = processRates[i] || '';
            $('#process-container').append(`
            <div class="row process-row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="process_name[]" 
                           placeholder="Process Name" value="${name}">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control process-rate-input" 
                           name="process_rate[]" placeholder="Process Rate" value="${rate}">
                </div>
                <div class="col-md-2">
                    ${i === 0 ? 
                      '<button type="button" class="btn btn-primary add-process">+</button>' : 
                      '<button type="button" class="btn btn-danger remove-process">-</button>'}
                </div>
            </div>`);
        });

        // If no processes exist, add one default row
        if (processNames.length === 0) {
            $('#process-container').html(`
            <div class="row process-row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="process_name[]" placeholder="Process Name">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="process_rate[]" placeholder="Process Rate">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary add-process">+</button>
                </div>
            </div>`);
        }
    }

    // Main update function
    function updateFormSections() {
        resetAllSections();
        
        const salaryValue = salaryType.val();
        
        if (salaryValue === 'salary') {
            salarySection.show();
        } 
        else if (salaryValue === 'waje') {
            updateWajeSections();
        }
    }

    // Event listeners for adding/removing process rows
    $(document).on('click', '.add-process', function() {
        $('#process-container').append(`
        <div class="row process-row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control" name="process_name[]" placeholder="Process Name">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control process-rate-input" 
                       name="process_rate[]" placeholder="Process Rate">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-process">-</button>
            </div>
        </div>`);
    });

    $(document).on('click', '.remove-process', function() {
        $(this).closest('.process-row').remove();
    });

    // Force numeric input for rates
    $(document).on('input', '.process-rate-input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ((this.value.match(/\./g) || []).length > 1) {
            this.value = this.value.substring(0, this.value.lastIndexOf('.'));
        }
    });

    // Event listeners
    salaryType.on('change', updateFormSections);
    departmentSelect.on('change', function() {
        if (salaryType.val() === 'waje') {
            updateWajeSections();
        }
    });
    designationSelect.on('change', function() {
        if (salaryType.val() === 'waje') {
            updateWajeSections();
        }
    });
    
    // Initialize sections
    updateFormSections();
    
    // CNIC change handler
    $('#cnic_no').change(function() {
        var employeeId = $(this).val();
        if (employeeId) {
            $.ajax({
                url: '/get-employee-details/' + employeeId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#fname').val(data.fname);
                },
                error: function() {
                    $('#fname').val('');
                    alert('Error fetching employee details');
                }
            });
        } else {
            $('#fname').val('');
        }
    });
    
    if ($('#cnic_no').val()) {
        $('#cnic_no').trigger('change');
    }
});
</script>
@endsection