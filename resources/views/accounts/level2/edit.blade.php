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
                    <h4 class="page-title">Edit Level2</h4>
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
                                        <form action="{{ route('level2.update', $level2->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Level2 Title</label>
                                                <input type="text" id="simpleinput" class="form-control" name="title"
                                                    value="{{ old('title', $level2->title) }}" placeholder="Level2 Title"
                                                    required>
                                                @if ($errors->has('title'))
                                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label for="department_id" class="form-label">Level1</label>
                                                <select name="level1_id" class="form-control select2" data-toggle="select2"
                                                    required>
                                                    <option>Select</option>
                                                    @foreach ($level1s as $level1)
                                                        <option value="{{ $level1->id }}"
                                                            {{ old('level1_id', $level2->level1_id) == $level1->id ? 'selected' : '' }}>
                                                            {{ $level1->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('level1_id'))
                                                    <span class="text-danger">{{ $errors->first('level1_id') }}</span>
                                                @endif
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
