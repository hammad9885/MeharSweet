@extends('layouts.app')
@section('content')
<div class="container">


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
                    <h4 class="page-title">Production Records</h4>
                </div>
            </div>
        </div>

    <!-- Filter Form -->
         <form action="{{ route('production.index') }}" method="GET" id="search-form">
                        <div class="row g-2 align-items-end">

                            {{-- Start Date --}}
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ request()->get('start_date') }}">
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ request()->get('end_date') }}">
                            </div>

                            {{-- Voucher Number --}}
                            <div class="col-md-3">
                                <label for="v_no" class="form-label">Voucher Number</label>
                                <select name="v_no" class="form-select select2" data-toggle="select2">
                                    <option value="">Select Voucher</option>
                                    @foreach ($vNoList as $vNo)
                                        <option value="{{ $vNo }}" {{ request()->get('v_no') == $vNo ? 'selected' : '' }}>
                                            {{ $vNo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Voucher Number --}}
                            <div class="col-md-3">
                                <label for="item_id" class="form-label">Product</label>
                                <select name="item_id" class="form-select select2" data-toggle="select2">
                                    <option value="">Select Product</option>
                                    @foreach ($items as $vNo)
                                        <option value="{{ $vNo->id }}" {{ request()->get('item_id') == $vNo ? 'selected' : '' }}>
                                            {{ $vNo->item_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                     

                         

                            {{-- Buttons (aligned in same row as description) --}}
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Show</button>
                                <a class="btn btn-success" href="{{ route('production.create') }}" onclick="return checkPermission()">Add New</a>
                            </div>

                        </div>
                    </form>

    <table class="table table-bordered mt-10" style="margin-top: 100px">
        <thead>
            <tr>
                <th>Voucher No</th>
                <th>Date</th>
                <th>Description</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productions as $prod)
                <tr>
                    <td>{{ $prod->v_no }}</td>
                    <td>{{ $prod->v_date }}</td>
                    <td>{{ $prod->descr }}</td>
                    <td>{{ $prod->amount }}</td>
                    <td>
                        <a href="{{ route('production.edit', $prod->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <a href="{{ route('production.print', $prod->id) }}" class="btn btn-sm btn-primary">Print</a>
                        <form action="{{ route('production.destroy', $prod->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this record?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
