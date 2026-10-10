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
                            <li class="breadcrumb-item active">Form Elements</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Add Level2</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <div class="col-lg-6">
                                       <form action="{{ route('process.store') }}" method="POST">
    @csrf
    <div class="mb-3">
    <label for="dept_id" class="form-label">Department</label>
    <select name="dept_id" class="form-control select2" data-toggle="select2" id="dept_id" required>
        <option value="">Select</option>
        @foreach ($departmentSections as $department)
            <option value="{{ $department->id }}">
                {{ $department->name }}
            </option>
        @endforeach
    </select>
</div>
    <div class="mb-3">
        <label for="type_title" class="form-label">Name</label>
        <input type="text" id="type_title" class="form-control" name="type_title"
               value="{{ old('type_title') }}" placeholder="Name" required>
    </div>
    <div class="mb-3" style="display:none;">
        <label for="rate" class="form-label">Rate</label>
        <input type="text" id="rate" class="form-control" name="rate"
               value="0" placeholder="Rate" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>


                                    </div>
                                </div>
                            </div>
                       </div>
                    </div> 
                </div> 
            </div>
        </div>
    </div>
@endsection
