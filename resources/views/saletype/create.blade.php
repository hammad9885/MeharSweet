@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                            <li class="breadcrumb-item active">Data Tables</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Add Sale Type</h4>
                </div>
            </div>
        </div>

    <form action="{{ route('saletype.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="sale_type" class="form-label">Sale Type</label>
            <input type="text" id="sale_type" name="sale_type" class="form-control" value="{{ old('sale_type') }}" required>
            @error('sale_type')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('saletype.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
