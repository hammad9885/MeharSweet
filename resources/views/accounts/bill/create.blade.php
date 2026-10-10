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

                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#input-types-preview" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                Preview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#input-types-code" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                Code
                            </a>
                        </li>
                    </ul> <!-- end nav-->

                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form action="{{route('bill.store')}}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Product Name</label>
                                            <input type="text" id="simpleinput" class="form-control" name="product_name">
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-email" class="form-label">Gatepass</label>
                                            <input type="text" id="example-email" class="form-control" placeholder="Department" name="gatepass_outno">
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-password" class="form-label">Rate</label>
                                            <input type="number" id="example-password" class="form-control" name="rate">
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Quantity</label>
                                            <input type="text" id="example-password" class="form-control" name="quantity">
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-palaceholder" class="form-label">Description</label>
                                            <input type="text" id="example-palaceholder" class="form-control" name="description" placeholder="Blood Group">
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-textarea" class="form-label">Amount</label>
                                            <input type="text" id="example-palaceholder" class="form-control" name="amount" placeholder="Salary">
                                        </div>


                                        <div class="mb-3">
                                            <label for="example-disable" class="form-label">Party Name</label>
                                            <select name="party_name" id="" class="form-control">
                                                <option value="official">select</option>
                                                @foreach ($parties as $party)
                                                <option value="{{$party->name}}">{{$party->name}}</option>
                                                @endforeach
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
