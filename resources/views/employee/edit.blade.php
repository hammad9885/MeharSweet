@extends('layouts.app')
@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                        <li class="breadcrumb-item active">Form Elements</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Employee</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="col-lg-6">
                            <form action="{{ route('employee.update', $employee->id) }}" method="POST">
                                @csrf
                                @method('PUT') <!-- Include PUT method for updating -->

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $employee->name) }}" required>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select name="department_id" class="form-control select2" data-toggle="select2" required>
                                        <option>Select</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="number" class="form-label">Phone Number</label>
                                    <input type="text" id="number" class="form-control" name="number" value="{{ old('number', $employee->number) }}" required>
                                    @error('number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" id="address" class="form-control" name="address" value="{{ old('address', $employee->address) }}" required>
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="blood_group" class="form-label">Blood Group</label>
                                    <input type="text" id="blood_group" class="form-control" name="blood_group" value="{{ old('blood_group', $employee->blood_group) }}" required>
                                    @error('blood_group')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="salary" class="form-label">Salary</label>
                                    <input type="text" id="salary" class="form-control" name="salary" value="{{ old('salary', $employee->salary) }}" required>
                                    @error('salary')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="shift_time" class="form-label">Shift Time</label>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <label for="shift_time" class="form-label">From</label>
                                            <input type="text" id="shift_time" class="form-control" name="shift_time1" value="{{ old('shift_time1', $employee->shift_time1) }}" required>
                                            @error('shift_time1')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-xl-6">
                                            <label for="shift_time" class="form-label">To</label>
                                            <input type="text" id="shift_time" class="form-control" name="shift_time2" value="{{ old('shift_time2', $employee->shift_time2) }}" required>
                                            @error('shift_time2')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                        <div class="mb-3">
                                            <label for="example-disable" class="form-label">Employee registration</label>
                                            <select name="registered" class="form-control" required>
                                                <option value="official" {{ old('registered', $employee->registered) == 'official' ? 'selected' : '' }}>Official</option>
                                                <option value="unofficial" {{ old('registered', $employee->registered) == 'unofficial' ? 'selected' : '' }}>Unofficial</option>
                                            </select>
                                            @error('registered')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>





                                <button type="submit" class="btn btn-primary">Update Employee</button>
                            </form>

                        </div> <!-- end col -->
                    </div> <!-- end row-->

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

</div>
@endsection
