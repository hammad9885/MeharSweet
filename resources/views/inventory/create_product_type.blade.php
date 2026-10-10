@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('inventory.itemtype.list') }}">Product Types</a></li>
                            <li class="breadcrumb-item active">Add Product Type</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Add Product Type (Finished Product)</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <form action="{{ route('inventory.itemtype') }}" method="POST">
                                    @csrf

                                    {{-- Hidden: always Product Registration for this page --}}
                                    <input type="hidden" name="item_type" value="rp">

                                    <div class="mb-3">
                                        <label for="type_title" class="form-label">Type Title <span class="text-danger">*</span></label>
                                        <input type="text" id="type_title" class="form-control" name="type_title"
                                            value="{{ old('type_title') }}" placeholder="e.g. Electronics, Clothing" required>
                                        @if ($errors->has('type_title'))
                                            <span class="text-danger">{{ $errors->first('type_title') }}</span>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('inventory.itemtype.list') }}" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
