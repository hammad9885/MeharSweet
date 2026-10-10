@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- Start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Purchase Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Purchase Invoice</h4>
                </div>
            </div>
        </div>
        <!-- End page title -->

        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                <strong>Success - </strong> {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <form action="{{ route('payment_invoice.update', [$trndtl->id, $purchaseDetail->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <input type="date" name="date" value="{{ $trndtl->date }}">
                                        <input type="text" name="supplier" value="{{ $trndtl->account_id }}">
                                        <input type="text" name="prepared_by" value="{{ $trndtl->preparedby }}">
                                        <input type="text" name="cash" value="{{ $trndtl->cash_id }}">
                                        <input type="text" name="amount" value="{{ $trndtl->credit }}">

                                        <!-- Single entry for PurchaseDetail -->
                                        <input type="text" name="item" value="{{ $purchaseDetail->item_code }}">
                                        <input type="number" name="width" value="{{ $purchaseDetail->width }}">
                                        <input type="number" name="length" value="{{ $purchaseDetail->length }}">
                                        <input type="number" name="gramage" value="{{ $purchaseDetail->grammage }}">
                                        <input type="number" name="quantity" value="{{ $purchaseDetail->qty }}">
                                        <input type="number" name="rate" value="{{ $purchaseDetail->rate }}">
                                        <input type="number" name="amount" value="{{ $purchaseDetail->amount }}">
                                        <input type="number" name="weight" value="{{ $purchaseDetail->total_wt }}">

                                        <button type="submit">Update</button>
                                    </form>

                                </div>
                                <!-- End row-->
                            </div> <!-- End preview-->
                        </div> <!-- End tab-content-->
                    </div> <!-- End card-body -->
                </div> <!-- End card -->
            </div><!-- End col -->
        </div><!-- End row -->
    </div>


@endsection
