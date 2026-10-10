@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Namo</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Form Elements</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Level1</h4>
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
                                        <form action="{{ route('level1.update', $level1->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Level1 Title</label>
                                                <input type="text" id="simpleinput" class="form-control" name="title"
                                                    value="{{ old('title', $level1->title) }}" required>
                                                @if ($errors->has('title'))
                                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label for="group_id" class="form-label">Group</label>
                                                <select name="group_id" class="form-control select2" data-toggle="select2"
                                                    required>
                                                    <option value="">Select</option>
                                                    @foreach ($groups as $group)
                                                        <option value="{{ $group->id }}"
                                                            {{ old('group_id', $level1->group_id) == $group->id ? 'selected' : '' }}>
                                                            {{ $group->title }}
                                                            <!-- Ensure that the group title is displayed -->
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('group_id'))
                                                    <span class="text-danger">{{ $errors->first('group_id') }}</span>
                                                @endif
                                            </div>


                                            <button type="submit" class="btn show-btn">Submit</button>
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
