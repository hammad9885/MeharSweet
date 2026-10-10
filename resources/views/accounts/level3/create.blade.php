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
                <h4 class="page-title">Form Elements</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Input Types</h4>
                    <p class="text-muted font-14">
                        Most common form control, text-based input fields. Includes support for all HTML5 types: <code>text</code>, <code>password</code>, <code>datetime</code>, <code>datetime-local</code>, <code>date</code>, <code>month</code>, <code>time</code>, <code>week</code>, <code>number</code>, <code>email</code>, <code>url</code>, <code>search</code>, <code>tel</code>, and <code>color</code>.
                    </p>



                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form action="{{route('level3.store')}}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Level3 Title</label>
                                            <input type="text" id="simpleinput" class="form-control" name="title">
                                        </div>
                                        <div class="mb-3">
                                            <label for="example-disable" class="form-label">Level1 Title</label>
                                            <select name="level2_id" class="form-control select2" data-toggle="select2">
                                                <option>Select</option>
                                                {{-- <optgroup label="Alaskan/Hawaiian Time Zone"> --}}
                                                    @foreach ($level2s as $level2)
                                                    <option value="{{$level2->id}}">{{$level2->title}}</option>
                                                    @endforeach
                                                {{-- </optgroup> --}}

                                            </select>

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
