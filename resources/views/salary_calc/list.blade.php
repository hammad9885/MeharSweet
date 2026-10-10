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
                        <li class="breadcrumb-item active">Salary Calculator</li>
                    </ol>
                </div>
                <h4 class="page-title">Salary Calculator</h4>
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
                            <form id="voucherForm" action="#" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <!-- Start Date Field -->
                                        <div class="mb-3">
                                            <label for="startDate" class="form-label">Start Date</label>
                                            <input type="date" id="startDate" class="form-control" name="start_date" required>
                                        </div>

                                        <!-- End Date Field -->
                                        <div class="mb-3">
                                            <label for="endDate" class="form-label">End Date</label>
                                            <input type="date" id="endDate" class="form-control" name="end_date" required>
                                        </div>

                                        <!-- Prepared By Field -->
                                        <div class="mb-3">
                                            <label for="preparedBy" class="form-label">Prepared By</label>
                                            <input type="text" id="preparedBy" class="form-control" name="prepared_by" value="{{ $loggedInUser->name }}" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <!-- Department Selection -->
                                        <div class="mb-3">
                                            <label for="department_id" class="form-label">Department</label>
                                            <select name="department_id" class="form-control select2" id="department_id" data-toggle="select2" required>
                                                <option value="">Select</option>
                                                @foreach ($departmentSections as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Designation Selection -->
                                        <div class="mb-3">
                                            <label for="designation_id" class="form-label">Designation</label>
                                            <select name="designation_id" class="form-control select2" id="designation_id" data-toggle="select2" required>
                                                <option value="">Select</option>
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Employee Selection -->
                                        <div class="mb-3">
                                            <label for="employee_id" class="form-label">Employee Name</label>
                                            <select name="employee_id" class="form-control select2" id="employee_id" data-toggle="select2" required>
                                                <option value="">Select</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->fname }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="button" id="loadEntry" class="btn btn-primary">Load Data</button>
                                            <button type="submit" class="btn btn-success">Submit Voucher</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employee Summary Card -->
                                <div class="row mt-3" id="employeeSummary" style="display: none;">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Employee Summary</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <p><strong>Employee:</strong> <span id="summaryEmployee">-</span></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><strong>Department:</strong> <span id="summaryDepartment">-</span></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><strong>Designation:</strong> <span id="summaryDesignation">-</span></p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p><strong>Monthly Salary:</strong> <span id="summarySalary">-</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendance Table -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Attendance Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover" id="entriesTable">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Shift</th>
                                                                <th>Break</th>
                                                                <th>Time In/Out</th>
                                                                <th>Total Hours</th>
                                                                <th>Duty Hours</th>
                                                                <th>Status</th>
                                                                <th>Daily Salary</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="entriesBody">
                                                            <!-- Entries will appear here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Section -->
                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">Salary Summary</h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td><strong>Standard Monthly Hours:</strong></td>
                                                        <td id="standardHours">0:00</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Actual Worked Hours:</strong></td>
                                                        <td id="totalDutyHours">0:00</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Extra Hours Needed:</strong></td>
                                                        <td>
                                                            <input type="number" step="0.01" id="extraHours" class="form-control form-control-sm" value="0" style="width: 100px;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Bonus Title:</strong></td>
                                                        <td id="bonusTitle">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Bonus Rate:</strong></td>
                                                        <td id="bonusRate">-</td>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <td><strong>Base Salary:</strong></td>
                                                        <td id="grandTotalSalary">0.00</td>
                                                    </tr>
                                                    <tr class="table-success">
                                                        <td><strong>Final Salary (with extra hours):</strong></td>
                                                        <td id="finalTotalSalary">0.00</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
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
$(document).ready(function() {
    // Set default dates
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set default values
    $('#startDate').val(formatDate(firstDayOfMonth));
    $('#endDate').val(formatDate(today));

    // Helper function to format time from 24h to 12h with AM/PM
    function formatTime(timeString) {
        if (!timeString) return ''; // Handle empty/null values
        
        // Split the time string into hours, minutes, seconds
        const parts = timeString.split(':');
        if (parts.length < 2) return timeString; // If not in expected format, return as-is
        
        let hours = parseInt(parts[0]);
        const minutes = parts[1];
        const ampm = hours >= 12 ? 'pm' : 'am';
        
        // Convert 24h to 12h format
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        
        return hours + ':' + minutes + ampm;
    }

    // Function to calculate time difference in hours
    function calculateTimeDifference(start, end) {
        if (!start || !end) return 0;
        
        const startParts = start.split(':');
        const endParts = end.split(':');
        
        const startHours = parseInt(startParts[0]) + (parseInt(startParts[1]) / 60);
        const endHours = parseInt(endParts[0]) + (parseInt(endParts[1]) / 60);
        
        // Handle case where end time is next day (e.g., night shift)
        let diff = endHours - startHours;
        if (diff < 0) {
            diff += 24; // Add 24 hours if end is next day
        }
        
        return diff;
    }

    // Function to calculate duty hours
    function calculateDutyHours(timeIn, timeOut, breakFrom, breakTo) {
        if (!timeIn || !timeOut) return 0;
        
        // Calculate total time between timeIn and timeOut
        const totalTime = calculateTimeDifference(timeIn, timeOut);
        
        // Calculate break time if break periods are provided
        let breakTime = 0;
        if (breakFrom && breakTo) {
            breakTime = calculateTimeDifference(breakFrom, breakTo);
        }
        
        // Calculate actual duty hours (total time minus break time)
        return totalTime - breakTime;
    }

    // Function to format hours to HH:MM format
    function formatHours(decimalHours) {
        const hours = Math.floor(decimalHours);
        const minutes = Math.round((decimalHours - hours) * 60);
        return `${hours}:${minutes.toString().padStart(2, '0')}`;
    }

    // Function to parse formatted hours back to decimal
    function parseHours(formattedHours) {
        if (!formattedHours) return 0;
        const parts = formattedHours.split(':');
        return parseFloat(parts[0]) + (parseFloat(parts[1]) / 60);
    }

    // Function to calculate daily salary
    function calculateDailySalary(monthlySalary, dutyHours, standardDailyHours) {
        // Calculate hourly rate based on standard monthly hours
        const hourlyRate = monthlySalary / (standardDailyHours * 30); // 30 days in month
        return (hourlyRate * dutyHours).toFixed(2);
    }

    // Function to get all dates between start and end date
    function getDatesInRange(startDate, endDate) {
        const dates = [];
        let currentDate = new Date(startDate);
        
        while (currentDate <= new Date(endDate)) {
            dates.push(new Date(currentDate));
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        return dates;
    }

    // Function to calculate standard monthly hours (treating all days equally)
    function calculateStandardHours(daysInRange) {
        // Assuming 8 working hours per day for all days
        return daysInRange * 8;
    }

    // Function to update totals including extra hours
    function updateTotalsWithExtra() {
        const extraHours = parseFloat($('#extraHours').val()) || 0;
        const actualHours = parseHours($('#totalDutyHours').text());
        const standardHours = parseHours($('#standardHours').text());
        
        // Calculate required extra hours to reach standard
        const requiredExtra = Math.max(0, standardHours - actualHours);
        
        // If no extra hours entered yet, show the required value as placeholder
        if (extraHours === 0 && requiredExtra > 0) {
            $('#extraHours').attr('placeholder', requiredExtra.toFixed(2));
        }
        
        // Calculate grand total of daily salaries
        let dailySalaryTotal = 0;
        $('#entriesBody tr').each(function() {
            const dailySalary = parseFloat($(this).find('td:last').text()) || 0;
            dailySalaryTotal += dailySalary;
        });
        
        // Calculate hourly rate based on actual worked hours and total earned
        const hourlyRate = actualHours > 0 ? (dailySalaryTotal / actualHours) : 0;
        
        // Calculate final salary including extra hours
        const finalSalary = (dailySalaryTotal + (hourlyRate * extraHours)).toFixed(2);
        
        // Update the display
        $('#grandTotalSalary').text(dailySalaryTotal.toFixed(2));
        $('#finalTotalSalary').text(finalSalary);
    }

    $('#loadEntry').click(function() {
        // Get form values
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const departmentId = $('#department_id').val();
        const designationId = $('#designation_id').val();
        const employeeId = $('#employee_id').val();

        // Validate required fields
        if (!startDate || !endDate || !departmentId || !designationId || !employeeId) {
            alert('Please fill all required fields');
            return;
        }

        // Get all dates in range
        const dates = getDatesInRange(startDate, endDate);
        const daysInRange = dates.length;
        
        // Calculate standard hours (treating all days equally)
        const standardHours = calculateStandardHours(daysInRange);
        const standardDailyHours = 8; // Assuming 8 hours per day

        // Show loading state
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

        // Make AJAX request
        $.ajax({
            url: "{{ route('salary_calc.get_data') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                start_date: startDate,
                end_date: endDate,
                department_id: departmentId,
                designation_id: designationId,
                employee_id: employeeId
            },
            success: function(response) {
                // Clear previous entries
                $('#entriesBody').empty();
                $('#grandTotalSalary').text('0.00');
                $('#finalTotalSalary').text('0.00');
                
                let totalDutyHours = 0;
                let monthlySalary = 0;
                let bonusTitle = '-';
                let bonusRate = '0';
                
                // Show employee summary
                $('#employeeSummary').show();

                // Update standard hours display with dynamic calculation
                $('#standardHours').text(formatHours(standardHours));

                // Create a map of dates with attendance data for easy lookup
                const attendanceMap = {};
                if (response.length > 0) {
                    response.forEach(entry => {
                        attendanceMap[entry.date] = entry;
                        // Store the monthly salary from the first entry
                        if (monthlySalary === 0 && entry.salary_amount) {
                            monthlySalary = parseFloat(entry.salary_amount);
                        }
                        // Store bonus info from the first entry
                        if (bonusTitle === '-' && entry.bonus_title) {
                            bonusTitle = entry.bonus_title;
                            bonusRate = entry.bonus_rate || '0';
                        }
                    });

                    // Update employee summary
                    $('#summaryEmployee').text(response[0].employee);
                    $('#summaryDepartment').text(response[0].department);
                    $('#summaryDesignation').text(response[0].designation);
                    $('#summarySalary').text(monthlySalary.toFixed(2));
                    
                    // Update bonus info in summary
                    $('#bonusTitle').text(bonusTitle);
                    $('#bonusRate').text(bonusRate);
                }

                // Add rows for all dates in range
                dates.forEach(date => {
                    const dateStr = formatDate(date);
                    const entry = attendanceMap[dateStr];
                    const isAbsent = !entry;
                    
                    if (isAbsent) {
                        // Add row for absent day
                        $('#entriesBody').append(`
                            <tr class="table-danger">
                                <td>${dateStr}</td>
                                <td colspan="6" class="text-center">Absent</td>
                                <td>0.00</td>
                            </tr>
                        `);
                    } else if (entry) {
                        // Calculate total hours (shift time minus break time)
                        const shiftHours = calculateTimeDifference(entry.shift_from, entry.shift_to);
                        const breakHours = calculateTimeDifference(entry.break_from, entry.break_to);
                        const totalHours = shiftHours - breakHours;
                        
                        // Calculate duty hours (actual working time)
                        const dutyHours = calculateDutyHours(entry.time_in, entry.time_out, entry.break_from, entry.break_to);
                        
                        // Calculate daily salary
                        const dailySalary = calculateDailySalary(monthlySalary, dutyHours, standardDailyHours);
                        
                        // Update totals
                        totalDutyHours += dutyHours;

                        // Determine status
                        let status = 'Present';
                        let statusClass = 'table-success';
                        
                        if (dutyHours < (shiftHours - breakHours) * 0.9) {
                            status = 'Short Hours';
                            statusClass = 'table-warning';
                        }

                        $('#entriesBody').append(`
                            <tr class="${statusClass}">
                                <td>${entry.date}</td>
                                <td>${formatTime(entry.shift_from)} - ${formatTime(entry.shift_to)}</td>
                                <td>${formatTime(entry.break_from)} - ${formatTime(entry.break_to)}</td>
                                <td>${formatTime(entry.time_in)} / ${formatTime(entry.time_out)}</td>
                                <td>${formatHours(totalHours)}</td>
                                <td>${formatHours(dutyHours)}</td>
                                <td>${status}</td>
                                <td>${dailySalary}</td>
                            </tr>
                        `);
                    }
                });

                // Update totals
                $('#totalDutyHours').text(formatHours(totalDutyHours));
                
                // Calculate required extra hours
                const requiredExtra = Math.max(0, standardHours - totalDutyHours);
                $('#extraHours').attr('placeholder', requiredExtra.toFixed(2));
                
                // Update totals with extra hours
                updateTotalsWithExtra();
            },
            error: function(xhr) {
                alert('Error loading data: ' + xhr.responseText);
            },
            complete: function() {
                // Reset button state
                $('#loadEntry').prop('disabled', false).text('Load Data');
            }
        });
    });

    // Handle extra hours input change
    $('#extraHours').on('input', function() {
        updateTotalsWithExtra();
    });

    // Auto-fill required extra hours when clicked
    $('#extraHours').on('click', function() {
        const placeholder = parseFloat($(this).attr('placeholder')) || 0;
        if (placeholder > 0 && $(this).val() === '') {
            $(this).val(placeholder);
            updateTotalsWithExtra();
        }
    });

    // Optional: You can add change events to department/designation to filter employees
    $('#department_id, #designation_id').change(function() {
        // You might want to implement employee filtering based on department/designation
    });
});
</script>
@endsection