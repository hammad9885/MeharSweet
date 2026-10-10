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
                <form id="voucherForm" action="{{ route('employee_type.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-6">
                     
                       <div class="row">
                           
                           <div class="col-md-6 mb-3">
        <label for="cnic_no" class="form-label">CNIC</label>
        <select name="cnic_no" id="cnic_no" class="form-control select2" data-toggle="select2" required>
            <option value="">Select</option>
            @foreach ($employees as $employee)
            <option value="{{ $employee->id }}" {{ old('id')==$employee->id ? 'selected' : '' }}>
                {{ $employee->cnic_no }}
            </option>
            @endforeach
        </select>
    </div>
    
   <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">Name</label>
                                <input type="text" id="fname" class="form-control" name="fname" readonly>
                            </div>

</div>

                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select name="department_id" id="department_id" class="form-control select2" data-toggle="select2" required>
                                    <option value="">Select</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id')==$department->id ? 'selected' : '' }}>
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
                                    <option value="{{ $designation->id }}" {{ old('designation_id')==$designation->id ? 'selected' : '' }}>
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
                                <option value="salary" {{ old('salary_type')=='salary' ? 'selected' : '' }}>Salary</option>
                                <option value="waje" {{ old('salary_type')=='waje' ? 'selected' : '' }}>Wage</option>
                            </select>
                        </div>

                        <!-- Salary Section -->
                        <div id="salary_section" style="display: none;">
                            <div class="mb-3">
                                <label for="salary_amount" class="form-label">Salary Amount</label>
                                <input type="number" id="salary_amount" class="form-control" name="salary_amount" step="any" value="{{ old('salary_amount') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Shift Time</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="shift_from" class="form-label">From</label>
                                        <input type="time" id="shift_from" class="form-control" name="shift_from" value="{{ old('shift_from') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="shift_to" class="form-label">To</label>
                                        <input type="time" id="shift_to" class="form-control" name="shift_to" value="{{ old('shift_to') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Break Time</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="break_from" class="form-label">From</label>
                                        <input type="time" id="break_from" class="form-control" name="break_from" value="{{ old('break_from') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="break_to" class="form-label">To</label>
                                        <input type="time" id="break_to" class="form-control" name="break_to" value="{{ old('break_to') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Waje Section for Departments 23, 25, 29, 26, etc. -->
                        <div id="waje_section_solna" style="display: none;">
                            <div class="mb-3">
                                <label for="basic_salary" class="form-label">Basic salary</label>
                                <input type="number" id="basic_salary" class="form-control" name="basic_salary" step="any" value="{{ old('basic_salary') }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="fix_impression_day" class="form-label">Fix Impression per Day</label>
                                <input type="number" id="fix_impression_day" class="form-control" name="fix_impression_day" step="any" value="{{ old('fix_impression_day') }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="lam_working_days" class="form-label">Working day in a Month</label>
                                <input type="number" id="lam_working_days" class="form-control" name="lam_working_days" step="any" value="{{ old('lam_working_days') }}">
                            </div>

                            <div class="mb-3">
                                <label for="over_time" class="form-label">Over Time salary per Month</label>
                                <input type="number" id="over_time" class="form-control" name="over_time" step="any" value="{{ old('over_time') }}">
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
                    <input type="number" class="form-control" name="process_rate[]" placeholder="Process Rate">
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
                            <input type="number" id="per_sheet_rate" class="form-control" name="per_sheet_rate" step="any" value="{{ old('per_sheet_rate') }}">
                        </div>
                        
                        <div class="mb-3">
                                <label for="salary_cal" class="form-label">Salary Calculate At</label>
                                <select name="salary_cal" class="form-control select2" data-toggle="select2" id="salary_cal">
                                    <option value="">Select</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="daily">Daily</option>
                                </select>
                            </div>
                            
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2 dropdowns first
    $('.select2').select2();

    // Get all form elements
    const salaryType = document.getElementById('salary_type');
    const departmentSelect = document.getElementById('department_id');
    const designationSelect = document.getElementById('designation_id');
    
    // Get all section containers
    const salarySection = document.getElementById('salary_section');
    const wajeSectionSolna = document.getElementById('waje_section_solna');
    const wajeSectionBox = document.getElementById('waje_section_box');
    const wajeSectionCor = document.getElementById('waje_section_cor');
    const perSheetRateContainer = document.getElementById('per_sheet_rate_container');

    // Define department groups
    const SOLNA_DEPTS = ['23', '25', '26', '29', '22', '28', '31'];
    const BOX_DEPT = '14';
    const SHEET_RATE_DEPT = '21';
    const COR_DEPTS = ['13', '18', '19', '20', '33'];

    // Function to completely reset all sections
    function resetAllSections() {
        salarySection.style.display = 'none';
        wajeSectionSolna.style.display = 'none';
        wajeSectionBox.style.display = 'none';
        wajeSectionCor.style.display = 'none';
        perSheetRateContainer.style.display = 'none';
    }

    // Function to handle waje section visibility
    function updateWajeSections() {
        resetAllSections();
        
        const deptId = $(departmentSelect).val(); // Use jQuery to get select2 value
        const desigId = $(designationSelect).val(); // Use jQuery to get select2 value
        
        if (SOLNA_DEPTS.includes(deptId)) {
            wajeSectionSolna.style.display = 'block';
        } 
        else if (deptId === BOX_DEPT) {
            wajeSectionBox.style.display = 'block';
        } 
        else if (COR_DEPTS.includes(deptId) && desigId === '10') {
            wajeSectionCor.style.display = 'block';
        }
        else if (deptId === SHEET_RATE_DEPT) {
            perSheetRateContainer.style.display = 'block';
        }
    }

    // Main update function
    function updateFormSections() {
        resetAllSections();
        
        const salaryValue = $(salaryType).val(); // Use jQuery to get select2 value
        
        if (salaryValue === 'salary') {
            salarySection.style.display = 'block';
        } 
        else if (salaryValue === 'waje') {
            updateWajeSections();
        }
    }

    // Event listeners using jQuery to work with select2
    $(salaryType).on('change', updateFormSections);
    
    $(departmentSelect).on('change', function() {
        if ($(salaryType).val() === 'waje') {
            updateWajeSections();
        }
    });
    
    $(designationSelect).on('change', function() {
        if ($(salaryType).val() === 'waje') {
            updateWajeSections();
        }
    });
    
    // Initialize on page load
    updateFormSections();
});

// name and cnic fetch

$(document).ready(function() {
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
});

// multiple process
$(document).ready(function() {
    // Add new process row
    $(document).on('click', '.add-process', function() {
        var newRow = `
        <div class="row process-row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control" name="process_name[]" placeholder="Process Name">
            </div>
            <div class="col-md-5">
                <input type="number" class="form-control" name="process_rate[]" placeholder="Process Rate">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-process">-</button>
            </div>
        </div>`;
        $('#process-container').append(newRow);
    });

    // Remove process row
    $(document).on('click', '.remove-process', function() {
        $(this).closest('.process-row').remove();
    });
});
</script>
@endsection