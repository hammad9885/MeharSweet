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
                    <h4 class="page-title">Sales Invoices</h4>
                </div>
            </div>
        </div>

        <a href="{{ route('sale_invoices.create') }}" class="btn btn-primary mb-3">Create New Invoice</a>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('sale_invoices.index') }}" class="mb-10 ">
            <div class="row">
                <div class="col-md-3">
                    <label for="voucher_no">Voucher No</label>
                    <input type="text" name="voucher_no" class="form-control" placeholder="Voucher No"
                        value="{{ request('voucher_no') }}">
                </div>
                <div class="col-md-3">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" class="form-control">
                        <option value="">Select Customer</option>
                        @foreach (App\Models\Customers::where('cid',auth()->user()->cid)->get() as $customer)
                            <option value="{{ $customer->acc_id }}"
                                {{ request('customer_id') == $customer->acc_id ? 'selected' : '' }}>{{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                     <label for="item_id">Item</label>
                    <select name="item_id" class="form-control">
                        <option value="">Select Item</option>
                        @foreach (App\Models\ItemMaster::where('cid',auth()->user()->cid)->get() as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                     <label for="date">Start Date</label>
                    <input type="date" name="startDate" class="form-control" value="{{ request('startDate') }}">
                </div>
                <div class="col-md-3">
                     <label for="date">End Date</label>
                    <input type="date" name="endDate" class="form-control" value="{{ request('endDate') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="{{ route('sale_invoices.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Voucher No</th>
                    <th>Customer</th>
                    <th>Party Ref</th>
                    <th>Total Items</th>
                    <th>Total Amount</th>
                    <th>Discount</th>
                    <th>Freight</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $master)
                {{-- {{ dd($master->items->first()->customer_id) }} --}}
                @php
                    $item = $master->items->first();
                    $custId = $master->customer_id ?? ($item ? $item->customer_id : null);
                    $cust = $custId
                        ? App\Models\Customers::firstWhere('acc_id', $custId)
                        : null;
                    $itemsTotal = $master->items->sum(function($i) {
                        return ($i->qty * $i->rate) - ($i->discount ?? 0);
                    });
                    $totalAmount = $itemsTotal;
                    $discount = (float)($master->discount ?? 0);
                    $freight = (float)($master->freight ?? 0);
                    $total = $totalAmount - $discount + $freight;
                @endphp
                {{-- {{ $master->items->first()->customer->name ?? ' ' }} --}}
                    <tr>
                        <td>{{ $master->v_no }}</td>
                        <td>{{ $cust->name ?? ' ' }} @if($cust && $cust->urdu_title)<br><span dir="rtl" class="text-muted">{{ $cust->urdu_title }}</span>@endif</td>
                        <td style="font-size: 18px; font-weight:bold;">
                            @if ($master->referenceName)
                                {{ $master->referenceName->name }}
                                @if ($master->referenceName->name_urdu)
                                    <br><span dir="rtl" class="text-muted">{{ $master->referenceName->name_urdu }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $master->items->count() }}</td>
                        <td>{{ number_format($totalAmount, 2) }}</td>
                        <td>{{ number_format($discount, 2) }}</td>
                        <td>{{ number_format($freight, 2) }}</td>
                        <td>{{ number_format($total, 2) }}</td>
                        <td>
                            <a href="{{ route('sale_invoices.edit', $master->v_no) }}"
                                class="btn btn-sm btn-warning" style="width: 80px">Edit</a>
                            <a href="{{ route('sale_invoices.print', $master->v_no) }}"
                                class="btn btn-sm btn-outline-primary" style="width: 80px" target="_blank">
                                <i class="bi bi-printer"></i> Print
                            </a>
                            <form action="{{ route('sale_invoices.destroy', $master->v_no) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" style="width: 80px"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
