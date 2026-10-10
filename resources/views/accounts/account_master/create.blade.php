@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h4 class="page-title">Chart Of Account</h4>
                </div>
            </div>

        </div>
       
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-none">
                    <div class="card-body">




                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <form action="{{ route('amaster.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Account Title</label>
                                                <input type="text" id="simpleinput" class="form-control" name="title"
                                                    placeholder="Account Title" required>
                                                @if ($errors->has('title'))
                                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Opening Date</label>
                                                <input type="date" id="entryDate" class="form-control"
                                                    name="opening_date"    required>
                                                @if ($errors->has('opening_date'))
                                                    <span class="text-danger">{{ $errors->first('opening_date') }}</span>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label for="example-disable" class="form-label">Level2 Title</label>
                                                <select name="level2_id" class="form-control select2" data-toggle="select2"
                                                    required>
                                                    <option value="">Select</option>
                                                    @foreach ($level2s as $level)
                                                        <option value="{{ $level->id }}">{{ $level->title }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('level2_id'))
                                                    <span class="text-danger">{{ $errors->first('level2_id') }}</span>
                                                @endif
                                            </div>

                                            <button type="submit" class="btn show-btn">Submit</button>
                                        </form>

                                    </div> <!-- end col -->
                                    {{-- @dd($level2s) --}}

                                </div>
                                <!-- end row-->
                            </div> <!-- end preview-->


                        </div> <!-- end tab-content-->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->



    </div>
    <script>
           const today = new Date().toISOString().split('T')[0];

    // Set the value of the input field to the current date
    document.getElementById('entryDate').value = today;
    </script>
@endsection
