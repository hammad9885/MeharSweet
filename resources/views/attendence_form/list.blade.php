@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Attendance Management System</h4>
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
                <form id="voucherForm" action="{{ route('attendence_form.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="cnic_no" class="form-label">Employee Name/CNIC</label>
                            <select name="cnic_no" id="cnic_no" class="form-control select2" data-toggle="select2" required>
                                <option value="">Select</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('cnic_no') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->fname }} | {{ $employee->cnic_no }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="employee_date" class="form-label">Date</label>
                            <input type="date" id="employee_date" class="form-control" name="employee_date"
                                value="{{ old('employee_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3" id="time_in_container">
                            <label for="employee_time" class="form-label">In Time</label>
                            <input type="time" id="employee_time" class="form-control" name="employee_time"
                                value="{{ old('employee_time') }}">
                        </div>
                        
                        <div class="mb-3" id="time_out_container">
                            <label for="employee_time_out" class="form-label">Out Time</label>
                            <input type="time" id="employee_time_out" class="form-control" name="employee_time_out"
                                value="{{ old('employee_time_out') }}">
                        </div>

                        <div id="button_container">
                            <button type="submit" class="btn btn-success" id="time_in_btn" name="action" value="time_in">Time In</button>
                            <button type="submit" class="btn btn-success" id="time_out_btn" name="action" value="time_out">Time Out</button>
                        </div>
                        <div id="already_registered_msg" class="alert alert-info" style="display: none;">
                            You have already registered both Time In and Time Out for today.
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Set current time when page loads
        function setCurrentTime() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            
            // Format hours and minutes to two digits
            hours = hours.toString().padStart(2, '0');
            minutes = minutes.toString().padStart(2, '0');
            
            const currentTime = `${hours}:${minutes}`;
            $('#employee_time').val(currentTime);
            $('#employee_time_out').val(currentTime);
        }
        
        // Call the function initially
        setCurrentTime();
        
        // Update time every minute (optional)
        setInterval(setCurrentTime, 60000);

        // Check attendance status when employee is selected or date changes
        function checkAttendanceStatus() {
            var employeeId = $('#cnic_no').val();
            var date = $('#employee_date').val();
            
            if (employeeId && date) {
                $.ajax({
                    url: '/check-attendance-status',
                    type: 'GET',
                    data: {
                        employee_id: employeeId,
                        date: date
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.hasBoth) {
                            // Both time in and time out exist
                            $('#time_in_container').show();
                            $('#time_out_container').show();
                            $('#employee_time').val(response.time_in);
                            $('#employee_time_out').val(response.time_out);
                            $('#button_container').hide();
                            $('#already_registered_msg').show();
                        } else if (response.hasTimeIn) {
                            // Only time in exists
                            $('#time_in_container').show();
                            $('#time_out_container').show();
                            $('#employee_time').val(response.time_in);
                            $('#time_in_btn').hide();
                            $('#time_out_btn').show();
                            $('#already_registered_msg').hide();
                        } else {
                            // No attendance record exists
                            $('#time_in_container').show();
                            $('#time_out_container').hide();
                            $('#time_in_btn').show();
                            $('#time_out_btn').hide();
                            $('#already_registered_msg').hide();
                        }
                    },
                    error: function() {
                        console.log('Error checking attendance status');
                    }
                });
            }
        }

        // Check status when employee changes
        $('#cnic_no').change(function() {
            checkAttendanceStatus();
        });

        // Check status when date changes
        $('#employee_date').change(function() {
            checkAttendanceStatus();
        });

        // Initial check when page loads
        checkAttendanceStatus();
    });
</script>
@endsection