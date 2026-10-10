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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Purchase Reports</li>
                    </ol>
                </div>
                <h3 class="page-title">Purchase Reports</h3>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif
    <!-- Show Form -->
    <div class="row">
        <div class="card mt-2">
            <div class="card-body">
                <div class="tab-content">
                    <div class="col-12">
                        <form action="{{ route('purchase.reports') }}" method="GET" class="form-inline"
                            id="search-form">
                            <div class="row">
                                <!-- Start Date -->
                                <div class="form-group col-xl-2">
                                    <label for="start_date" class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{$startDate}}">
                                </div>
                                <!-- End Date -->
                                <div class="form-group col-xl-2">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{$endDate}}">
                                </div>
                                <!-- Voucher No Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="product_type" class="sr-only">Select Purchase</label>
                                    <select name="product_type" class="form-control select2" data-toggle="select2"
                                        id="product_type">
                                        <option value="">Select</option>
                                        <option value="PIN">Purchase Boxboard</option>
                                        <option value="PRN">Purchase Return</option>
                                        <option value="PPN">Purchase Plate</option>
                                        <option value="GPN">Glue Purchase</option>
                                        <option value="IPN">Ink Purchase</option>
                                        <option value="LPN">Lamination Purchase</option>
                                        <option value="CPN">Corrugation Purchase</option>
                                        <option value="SPN">Shipper Purchase</option>
                                        <option value="DPN">Dye Purchase</option>
                                    </select>
                                </div>

                                <div class="form-group col-xl-2">
                                    <label for="account">Party</label>
                                    <select name="account" class="form-control select2" id="account"
                                        data-toggle="select2">
                                        <option value="">Select</option>
                                        @foreach($accounts as $product)
                                        <option value="{{ $product->account_id }}" {{ request()->get('account') ==
                                            $product->account_id ? 'selected' : '' }}>
                                            {{ $product->accounts->title ?? 'Select a Party' }}
                                            <!-- Display account title -->
                                        </option>
                                        @endforeach
                                    </select>
                                </div>



                                <!-- Show and Add New Buttons -->
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Data Table -->
    <div class="row">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print
                    Table</button>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="col-12">
                                @if(empty($productType))


                                <table id="combined-data-table-boxboard" class="table dt-responsive nowrap w-100 small-font-table ">
                                    <h4 for="boxboard">Purchase Boxboard Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal1 = 0;
                                        @endphp
                                
                                        @foreach($trndtl as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->purchasedetails->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal1 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal1, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                
                                <table id="combined-data-table-return" class="table dt-responsive nowrap w-100 small-font-table ">
                                    <h4 for="return">Purchase Return Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal2 = 0;
                                        @endphp
                                
                                        @foreach($trndtl1 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->purchasereturns->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal2 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal2, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                
                                <table id="combined-data-table-plate" class="table dt-responsive nowrap w-100 small-font-table ">
                                    <h4 for="plate">Purchase Plate Details</h4>
                                
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal3 = 0;
                                        @endphp
                                
                                        @foreach($trndtl2 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->purchaseplates->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal3 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal3, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                
                                <table id="combined-data-table-glue" class="table dt-responsive nowrap w-100 small-font-table ">
                                
                                    <h4 for="glue">Glue Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal4 = 0;
                                        @endphp
                                
                                        @foreach($trndtl3 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->gluepurchases->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal4 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal4, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                
                                <table id="combined-data-table-ink" class="table dt-responsive nowrap w-100 small-font-table ">
                                
                                    <h4 for="ink">Ink Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal5 = 0;
                                        @endphp
                                
                                        @foreach($trndtl4 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->inkpurchases->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal5 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal5, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                
                                <table id="combined-data-table-lamination" class="table dt-responsive nowrap w-100 small-font-table ">
                                
                                    <h4 for="lamination">Lamination Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal6 = 0;
                                        @endphp
                                
                                        @foreach($trndtl5 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->leminationpurchases->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal6 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal6, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                
                                <table id="combined-data-table-corrugation" class="table dt-responsive nowrap w-100 small-font-table ">
                                
                                    <h4 for="corrugation">Corrugation Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal7 = 0;
                                        @endphp
                                
                                        @foreach ($trndtl6 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->corrugationpurchases->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal7 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal7, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <table id="combined-data-table-shipper" class="table dt-responsive nowrap w-100 small-font-table ">
                                
                                    <h4 for="shipper">Shipper Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal8 = 0;
                                        @endphp
                                
                                        @foreach ($trndtl7 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->shipperpurchases->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal8 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal8, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <table id="combined-data-table-dye" class="table dt-responsive nowrap w-100 small-font-table ">
                                
                                    <h4 for="dye">Dye Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal9 = 0;
                                        @endphp
                                
                                        @foreach ($trndtl8 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->dyepurchases->amount ?? 0;
                                
                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }
                                
                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal9 += $amount;
                                        @endphp
                                        @endforeach
                                
                                        @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal9, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>






                                <!-- Now sum the grand totals from both tables -->
                                @php
                                $totalGrandTotal = $grandTotal1 + $grandTotal2 + $grandTotal3 + $grandTotal4 +
                                $grandTotal5 + $grandTotal6 + $grandTotal7 + $grandTotal8 + $grandTotal9;
                                @endphp

                                <!-- Display the overall grand total -->
                                <h4 style="text-align:right; ">Grand Total: <strong>{{ number_format($totalGrandTotal,
                                        2) }}</strong></h4>
                                

                                <!-- New Data Start from here -->

                                @elseif($productType == 'PIN')
                                <table id="combined-data-table-boxboard"
                                    class="table dt-responsive nowrap w-100 small-font-table ">
                                    <h4 for="boxboard">Purchase Boxboard Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal1 = 0;
                                        @endphp

                                        @foreach($trndtl as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->purchasedetails->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal1 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal1, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'PRN')
                                <table id="combined-data-table-return"
                                    class="table dt-responsive nowrap w-100 small-font-table ">
                                    <h4 for="return">Purchase Return Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal2 = 0;
                                        @endphp

                                        @foreach($trndtl1 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->purchasereturns->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal2 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal2, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'PPN')
                                <table id="combined-data-table-plate"
                                    class="table dt-responsive nowrap w-100 small-font-table ">
                                    <h4 for="plate">Purchase Plate Details</h4>

                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal3 = 0;
                                        @endphp

                                        @foreach($trndtl2 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->purchaseplates->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal3 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal3, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'GPN')
                                <table id="combined-data-table-glue"
                                    class="table dt-responsive nowrap w-100 small-font-table ">

                                    <h4 for="glue">Glue Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal4 = 0;
                                        @endphp

                                        @foreach($trndtl3 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->gluepurchases->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal4 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal4, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'IPN')
                                <table id="combined-data-table-ink"
                                    class="table dt-responsive nowrap w-100 small-font-table ">

                                    <h4 for="ink">Ink Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal5 = 0;
                                        @endphp

                                        @foreach($trndtl4 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->inkpurchases->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal5 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal5, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'LPN')
                                <table id="combined-data-table-lamination"
                                    class="table dt-responsive nowrap w-100 small-font-table ">

                                    <h4 for="lamination">Lamination Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal6 = 0;
                                        @endphp

                                        @foreach($trndtl5 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->leminationpurchases->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal6 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal6, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'CPN')
                                <table id="combined-data-table-corrugation"
                                    class="table dt-responsive nowrap w-100 small-font-table ">

                                    <h4 for="corrugation">Corrugation Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal7 = 0;
                                        @endphp

                                        @foreach ($trndtl6 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->corrugationpurchases->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal7 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal7, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>


                                @elseif($productType == 'SPN')
                                <table id="combined-data-table-shipper"
                                    class="table dt-responsive nowrap w-100 small-font-table ">

                                    <h4 for="shipper">Shipper Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal8 = 0;
                                        @endphp

                                        @foreach ($trndtl7 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->shipperpurchases->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal8 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal8, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>

                                @elseif($productType == 'DPN')
                                <table id="combined-data-table-dye"
                                    class="table dt-responsive nowrap w-100 small-font-table ">

                                    <h4 for="dye">Dye Purchase Details</h4>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>V. No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $groupedData = [];
                                        $grandTotal9 = 0;
                                        @endphp

                                        @foreach ($trndtl8 as $data)
                                        @php
                                        $key = $data->date . '_' . $data->v_type . '-' . $data->v_no;
                                        $amount = $data->dyepurchases->amount ?? 0;

                                        if (!isset($groupedData[$key])) {
                                        $groupedData[$key] = [
                                        'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                                        'v_no' => $data->v_type . '-' . $data->v_no,
                                        'amount' => 0
                                        ];
                                        }

                                        $groupedData[$key]['amount'] += $amount;
                                        $grandTotal9 += $amount;
                                        @endphp
                                        @endforeach

                                        @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['v_no'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($grandTotal9, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set default dates
  const today = new Date();
    const defaultEndDate = formatDate(today);
    const defaultStartDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));

    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    // Only apply JS default dates if server values are empty
    if (startInput && !startInput.value) {
        startInput.value = defaultStartDate;
    }
    if (endInput && !endInput.value) {
        endInput.value = defaultEndDate;
    }
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}


function printTable() {
    // Get the current page title
    const pageTitle = document.querySelector('.page-title').innerText;
    
    // Get search criteria
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const productType = document.getElementById('product_type').value;
    const account = document.getElementById('account').options[document.getElementById('account').selectedIndex].text;
    
    // Create print header with search criteria
    let printHeader = `
        <div style="text-align: center; margin-bottom: 10px;">
            <h2 style="margin: 5px 0; font-size: 18px;">${pageTitle}</h2>
            <div style="margin-bottom: 5px; font-size: 12px;">
                <strong>Date:</strong> ${startDate} to ${endDate}
            </div>
    `;
    
    if (productType) {
        const typeText = document.querySelector(`#product_type option[value="${productType}"]`).text;
        printHeader += `<div style="font-size: 12px;"><strong>Type:</strong> ${typeText}</div>`;
    }
    
    if (account && account !== 'Select') {
        printHeader += `<div style="font-size: 12px;"><strong>Party:</strong> ${account}</div>`;
    }
    
    printHeader += `</div>`;
    
    // Get all visible tables
    const visibleTables = document.querySelectorAll('table[id^="combined-data-table-"]');
    
    // Store original content
    const originalContents = document.body.innerHTML;
    
    // Prepare print content
    let printContent = '';
    
    // If no specific product type is selected, print all tables with their headings
    if (!productType) {
        visibleTables.forEach(table => {
            const heading = table.previousElementSibling;
            if (heading && heading.tagName === 'H4') {
                printContent += `<div class="section-title" style="font-size: 14px; margin: 5px 0;">${heading.innerText}</div>`;
            }
            printContent += `<div class="table-section">${table.outerHTML}</div>`;
        });
        
        // Add grand total if showing all tables
        const grandTotalElement = document.querySelector('h4[style*="text-align:right"]');
        if (grandTotalElement) {
            printContent += `<div class="section-title" style="font-size: 14px; text-align: right; margin: 5px 0;">${grandTotalElement.innerText}</div>`;
        }
    } else {
        // If specific product type is selected, print just that table
        const tableId = `combined-data-table-${productType.toLowerCase().substring(1)}`;
        const table = document.getElementById(tableId);
        if (table) {
            const heading = table.previousElementSibling;
            if (heading && heading.tagName === 'H4') {
                printContent += `<div class="section-title" style="font-size: 14px; margin: 5px 0;">${heading.innerText}</div>`;
            }
            printContent += `<div class="table-section">${table.outerHTML}</div>`;
        }
    }
    
    // Create the print document
    document.body.innerHTML = `
        <html>
            <head>
                <title>${pageTitle}</title>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 12px; /* Increased back to 12px */
                       
                        margin-bottom: 5px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 4px !important; /* Removed padding completely */
                    }
                    th {
                        background-color: #f2f2f2;
                        text-align: left;
                    }
                    .section-title {
                        font-weight: bold;
                    }
                    @page {
                        size: auto;
                        margin: 5mm;
                    }
                    body {
                        margin: 5px;
                        padding: 0;
                    }
                </style>
            </head>
            <body>
                ${printHeader}
                ${printContent}
            </body>
        </html>
    `;
    
    window.print();
    
    // Restore original content
    document.body.innerHTML = originalContents;
}
</script>
@endsection