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
                <h4 class="page-title">Add Employee</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">




                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form action="{{route('employee.store')}}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Name</label>
                                            <input type="text" id="simpleinput" class="form-control" placeholder="Name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-disable" class="form-label">Department Name</label>
                                            <select name="department_id" class="form-control select2" data-toggle="select2" required>
                                                <option>Select</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{$department->id}}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{$department->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('department_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-password" class="form-label">Phone Number</label>
                                            <input type="number" id="example-password" class="form-control" name="number" value="{{ old('number') }}" placeholder="Phone Number" required>
                                            @error('number')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Address</label>
                                            <input type="text" id="example-password" class="form-control" name="address" value="{{ old('address') }}" placeholder="Address" required>
                                            @error('address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-palaceholder" class="form-label">Blood Group</label>
                                            <input type="text" id="example-palaceholder" class="form-control" name="blood_group" placeholder="Blood Group" value="{{ old('blood_group') }}" required>
                                            @error('blood_group')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-textarea" class="form-label">Salary</label>
                                            <input type="number" id="example-palaceholder" class="form-control" name="salary" placeholder="Salary" value="{{ old('salary') }}" required>
                                            @error('salary')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-readonly" class="form-label">Shift Time</label>
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <label for="example-readonly" class="form-label">From</label>
                                                    <input type="time" id="example-readonly" class="form-control" name="shift_time1" value="{{ old('shift_time1') }}">
                                                    @error('shift_time1')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-xl-6">
                                                    <label for="example-readonly" class="form-label">To</label>
                                                    <input type="time" id="example-readonly" class="form-control" name="shift_time2" value="{{ old('shift_time2') }}" required>
                                                    @error('shift_time2')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-disable" class="form-label">Employee registration</label>
                                            <select name="registered" class="form-control" required>
                                                <option value="official" {{ old('registered') == 'official' ? 'selected' : '' }}>Official</option>
                                                <option value="unofficial" {{ old('registered') == 'unofficial' ? 'selected' : '' }}>Unofficial</option>
                                            </select>
                                            @error('registered')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>

                                </div> <!-- end col -->


                            </div>
                            <!-- end row-->
                        </div> <!-- end preview-->


                    </div> <!-- end tab-content-->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->



</div>
@endsection
