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
                    <h4 class="page-title">Sale Type Details</h4>
                </div>
            </div>
        </div>

    <div class="card">
        <div class="card-body">
            <h4>{{ $saleType->sale_type }}</h4>
            <p><strong>ID:</strong> {{ $saleType->id }}</p>
            <p><strong>Company ID:</strong> {{ $saleType->cid }}</p>
        </div>
    </div>

    <a href="{{ route('saletype.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection
