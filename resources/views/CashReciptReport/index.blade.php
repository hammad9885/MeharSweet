<div>
    <!-- Smile, breathe, and go slowly. - Thich Nhat Hanh -->
</div>
@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h4 class="page-title">Cash Report</h4>
                </div>
            </div>
        </div>

      

        <!-- Filter Form -->
        <form method="GET" action="{{ route('cashreport.users') }}" class="mb-4">
            
            <div class="row">
                <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
    

            <div class="col-md-3">
                <label for="customer_id" class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control select2" data-toggle="select2">
                    <option value="">Select Customer</option>
                    @foreach (App\Models\Customers::with('accountMaster')->where('cid', auth()->user()->cid)->get() as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ str_replace('\\', '/', $customer->name) }} .
                            {{ $customer->accountMaster->full_code ?? 'N/A' }}</option>
                    @endforeach
                    
                </select>
            </div>

            <div class="col-md-3">
                <label for="user_id" class="form-label">Users</label>
                <select name="user_id" id="user_id" class="form-control select2" data-toggle="select2">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ str_replace('\\', '/', $user->name) }}</option>
                    @endforeach
                </select>
            </div>

            </div>
 

    <div class="row mt-3">
        <div class="col-md-12 text-end">
            <button type="submit" class="btn btn-success">Show</button>
            <a href="{{ route('cashreport.users') }}" class="btn btn-secondary">Reset</a>
        </div>
    </div>
    </form>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Voucher</th>
                <th>User</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        @php
        $total=0
        @endphp
        <tbody>
            @foreach ($trndtl as $master)
                @php
                    $show = false;
                    $total += $master->credit ?? 0;
                @endphp

                <tr>
                    <td>{{ date('d-m-Y', strtotime($master->date)) }}</td>
                    <td>{{ 'CRV-' . $master->v_no }}</td>


                    <td>{{ $master->user->name ?? 'N/A' }}</td>
                    <td>{{ $master->customer->name ?? 'N/A' }}</td>
                    <td>{{ $master->credit ?? 0 }}</td>

                    <td>
                        <a href="{{ route('printInvoice', ['id' => $master->v_no, 'type' => $master->v_type]) }}"
                           target="_blank" class="btn show-btn btn-sm">
                           Print Invoice
                        </a>
                    </td>
                </tr>
              
                
            @endforeach
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end">Total:</td>
                    <td>{{ $total }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </tbody>
    </table>
    </div>
@endsection
