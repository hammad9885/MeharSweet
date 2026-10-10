@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                        <li class="breadcrumb-item active">Data Tables</li>
                    </ol>
                </div>
                <h4 class="page-title">Employees Department</h4>
            </div>
        </div>
    </div>

    <!-- end page title -->

    <div class="row">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Success - </strong> {{ session('success') }}
        </div>
        @endif

<div class="row">

            <div class="card mt-2">
                <div class="card-body">

                    <div class="tab-content">
                        <div class="col-10">
                          <form action="{{ route('employee_type.reports') }}" method="GET" class="form-inline" id="search-form">
    <div class="row">
        <div class="form-group col-xl-3">
            <label for="start_date" class="sr-only">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date"
                   value="{{ request()->get('start_date') }}">
        </div>
        <div class="form-group col-xl-3">
            <label for="end_date" class="sr-only">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date"
                   value="{{ request()->get('end_date') }}">
        </div>
        <div class="form-group col-xl-3">
            <label for="employee" class="sr-only">Status</label>
            <select name="employee" class="form-control select2">
                <option value="">All</option>
                <option value="official" {{ request()->get('employee') == 'official' ? 'selected' : '' }}>
                    Official
                </option>
                <option value="unofficial" {{ request()->get('employee') == 'unofficial' ? 'selected' : '' }}>
                    Unofficial
                </option>
            </select>
        </div>

        <div class="form-group col-xl-3">
    <label for="fname" class="sr-only">Name</label>
    <select name="fname" class="form-control select2" data-toggle="select2" data-placeholder="Select Name">
        <option value=""></option>
        @foreach($dropdownEmployees->filter(function($type) { return $type->employee && $type->employee->fname; })->unique(function($type) { return $type->employee->fname; }) as $type)
            <option value="{{ $type->employee->fname }}" {{ request()->get('fname') == $type->employee->fname ? 'selected' : '' }}>
                {{ $type->employee->fname }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group col-xl-3 mt-2">
    <label for="cnic_no" class="sr-only">CNIC</label>
    <select name="cnic_no" class="form-control select2" data-toggle="select2" data-placeholder="Select CNIC">
        <option value=""></option>
        @foreach($dropdownEmployees->filter(function($type) { return $type->employee && $type->employee->cnic_no; })->unique(function($type) { return $type->employee->cnic_no; }) as $type)
            <option value="{{ $type->employee->cnic_no }}" {{ request()->get('cnic_no') == $type->employee->cnic_no ? 'selected' : '' }}>
                {{ $type->employee->cnic_no }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group col-xl-3 mt-2">
    <label for="salary_type" class="sr-only">Salary Type</label>
    <select name="salary_type" class="form-control select2" data-toggle="select2" data-placeholder="Select Salary Type">
        <option value=""></option>
        @foreach($salaryTypes as $type)
        <option value="{{ $type->salary_type }}" {{ request()->get('salary_type') == $type->salary_type ? 'selected' : '' }}>
            @if($type->salary_type == 'salary')
                Salary
            @elseif($type->salary_type == 'waje')
                Wage
            @else
                {{ $type->salary_type }} {{-- fallback for other values --}}
            @endif
        </option>
        @endforeach
    </select>
</div>

<div class="form-group col-xl-3 mt-2">
    <label for="department_id" class="sr-only">Department</label>
    <select name="department_id" class="form-control select2" data-toggle="select2" data-placeholder="Select Department">
        <option value="">All Departments</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}" {{ request()->get('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->name }} {{-- Assuming there's a 'name' field --}}
            </option>
        @endforeach
    </select>
</div>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Show</button>
            <a class="btn btn-success" href="{{ route('employee_type.list') }}" 
               role="button" onclick="return checkPermission()">Add New</a>
        </div>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="col-12">
            
         
            <!-- Print Button -->
            <div class="card mt-2">
                <div class="card-body">
                     <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="basic-datatable-preview">
                            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                          <th>Sr</th>
                                <th>Name</th>
                                <th style="display:none">CNIC</th>
                                
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Salary Type</th>
                                <th>Salary/Wage Rate</th>
                                
                                        <th class="no-print">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($employeeTypes as $employeeType)
                                    <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employeeType->employee->fname ?? 'N/A'}}</td>
                                <td style="display:none">{{ $employeeType->employee->cnic_no ?? 'N/A'}}</td>
                                
                                <td>{{ $employeeType->department->name ?? 'N/A'}}</td>
                                <td>{{ $employeeType->designation->name ?? 'N/A' }}</td>
                                <td>
    @if(isset($employeeType->salary_type))
        {{ $employeeType->salary_type == 'salary' ? 'Salary' : ($employeeType->salary_type == 'waje' ? 'Wage' : $employeeType->salary_type) }}
    @else
        N/A
    @endif
</td>
                            @if(isset($employeeType->salary_type))
    @if($employeeType->salary_type == 'waje')
        {{-- Handle 'waje' salary type --}}
        @if(in_array($employeeType->department_id, ['23', '25', '26', '29', '22', '28', '31']))
            <td>R1: {{ $employeeType->fix_impression_day && $employeeType->lam_working_days ? ($employeeType->basic_salary / ($employeeType->fix_impression_day * $employeeType->lam_working_days)) : 'N/A' }} <br> R2: {{ $employeeType->fix_impression_day && $employeeType->lam_working_days ? ($employeeType->over_time / ($employeeType->fix_impression_day * $employeeType->lam_working_days)) : 'N/A' }}</td>
        @elseif($employeeType->department_id == '14')
          <td>
@php
    // First, let's properly decode the input if it's JSON
    $names = is_string($employeeType->process_name) ? json_decode($employeeType->process_name, true) : $employeeType->process_name;
    $rates = is_string($employeeType->process_rate) ? json_decode($employeeType->process_rate, true) : $employeeType->process_rate;

    // Ensure we have arrays
    $names = (array)$names;
    $rates = (array)$rates;

    // Generate the output
    $output = [];
    for ($i = 0; $i < count($names); $i++) {
        $name = is_array($names[$i]) ? json_encode($names[$i]) : '"'.$names[$i].'"';
        $rate = isset($rates[$i]) ? (is_array($rates[$i]) ? json_encode($rates[$i]) : '"'.$rates[$i].'"') : '[]';
        $output[] = '['.$name.' | '.$rate.']';
    }
@endphp

{!! implode("<br>", $output) !!}
</td>
          
          
          
          
        @elseif($employeeType->department_id == '21')
            <td>{{ $employeeType->per_sheet_rate }}</td>
        @elseif(in_array($employeeType->department_id, ['13', '18', '19', '20']) && $employeeType->designation_id == 10)
            <td>Per Box Rate</td>
        @elseif(in_array($employeeType->department_id, [ '33']) && $employeeType->designation_id == 10)
            <td>Per Sheet Rate</td>
        @else
            <td>N/A</td>
        @endif
    @elseif($employeeType->salary_type == 'salary')
        {{-- Handle 'salary' type --}}
        <td>{{ $employeeType->salary_amount ?? 'N/A' }}</td>
    @else
        {{-- Handle unknown salary types --}}
        <td>N/A</td>
    @endif
@else
    {{-- Handle case where salary_type is not set --}}
    <td>N/A</td>
@endif
                                
                                        <td class="no-print">
                                            <div class="d-flex">
                                                <a href="{{ route('employee_type.edit', $employeeType->id) }}">
                                                    <button type="button" class="btn btn-primary" onclick="return checkPermissionEdit()">Edit</button>
                                                </a>
                                                <form action="{{ route('employee_type.destroy', $employeeType->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        style="margin-left: 2px;" onclick="return checkPermissionDel()">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end preview-->
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div> <!-- end row-->
</div>
<!-- Print Function -->
<script>

function checkPermission() {
        @php
        $isAdmin = auth()->user()->is_admin;
        $canAdd = true;

        if ($isAdmin == 0) {
            $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                ->where('app_name', 'registeremployee')
                ->first();
            $canAdd = $userRights && $userRights->add == 1;
        }
    @endphp
        
        if (!@json($canAdd)) {
            alert('You do not have Permission to Add');
            return false; // Prevent the default action (navigation)
        }
        return true; // Allow navigation
    }
    
    
    function checkPermissionEdit() {
        @php
        $isAdmin = auth()->user()->is_admin;
        $canAdd = true;

        if ($isAdmin == 0) {
            $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                ->where('app_name', 'registeremployee')
                ->first();
            $canAdd = $userRights && $userRights->edit == 1;
        }
    @endphp
        
        if (!@json($canAdd)) {
            alert('You do not have Permission to Edit');
            return false; // Prevent the default action (navigation)
        }
        return true; // Allow navigation
    }
    
    function checkPermissionDel() {
      
        
        
        
        @php
        $isAdmin = auth()->user()->is_admin;
        $canAdd = true;

        if ($isAdmin == 0) {
            $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                ->where('app_name', 'registeremployee')
                ->first();
            $canAdd = $userRights && $userRights->del == 1;
        }
    @endphp
        if (!@json($canAdd)) {
            alert('You do not have Permission to Delete');
            return false; // Prevent the default action (navigation)
        }
        return true; // Allow navigation
    }
    
const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('end_date').value = today;


$(document).ready(function() {
    // Reinitialize Select2 dropdowns
    $('.select2').select2({
        width: '100%'
    });
});

    function printTable() {
        // Hide elements with 'no-print' class
        const elementsToHide = document.querySelectorAll('.no-print');
        elementsToHide.forEach(el => el.style.display = 'none');

        const printContents = document.getElementById('basic-datatable').outerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 8px;
                            }
                            th {
                                background-color: #f2f2f2;
                                text-align: left;
                            }
                            .no-print{
                                background-color: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // Reload to restore the original page content
    }
</script>
@endsection