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
                    <h4 class="page-title">Add Mold</h4>
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
                                      <form action="{{ route('inventory.storemold') }}" method="POST">
    @csrf

    <div class="row">
        {{-- Mold ID --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Mold ID</label>
            <input type="text" class="form-control" name="mold_id"
                   value="{{ $moldId ?? '' }}" readonly>
        </div>

        {{-- Mold Name --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Mold Name</label>
            <input type="text" name="mold_name" class="form-control"
                   value="{{ old('mold_name') }}" required>
        </div>
    </div>

    <div class="row">
      
        {{-- Weight --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Weight</label>
            <input type="number" step="0.01" name="weight"
                   class="form-control" value="{{ old('weight') }}">
        </div>

         {{-- Wages --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Wages</label>
            <input type="number" step="0.01" name="wages"
                   class="form-control" value="{{ old('wages') }}">
        </div>

    </div>

    <div class="row">
       
        {{-- Expense --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Expense</label>
            <input type="number" step="0.01" name="expense"
                   class="form-control" value="{{ old('expense') }}">
                  
        </div>
            <div class="col-md-6 mb-3">
            <label class="form-label">Total</label>
            <input type="number" step="0.01" name="total"
                   class="form-control" value="{{ old('total') }}">
        </div>
    </div>

    <div class="row">
        {{-- Total --}}
     

        {{-- Remarks --}}
        <div class="col-md-12 mb-3">
            <label class="form-label">Remarks</label>
            <input type="text" name="remarks"
                   class="form-control" value="{{ old('remarks') }}">
        </div>
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
        </div>

    </div>
@endsection
