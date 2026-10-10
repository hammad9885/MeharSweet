@extends('layouts.app')

@section('content')
<style>
    .report-header {
        display: none;
    }
    .report-header-border {
        padding-top: 8px;
    }
    .report-table th, .report-table td {
        vertical-align: middle;
        padding: 6px 8px;
    }
    .col-nowrap {
        white-space: nowrap;
    }
    @media print {
        .no-print { display: none !important; }
        .page-title-box { display: none !important; }
        .report-header { display: block !important; }
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .table {
            color: #000 !important;
            border-color: #000 !important;
            font-size: 13px;
        }
        .report-table th, .report-table td {
            padding: 4px 6px !important;
        }
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase</a></li>
                        <li class="breadcrumb-item active">Item Based Purchase</li>
                    </ol>
                </div>
                <h4 class="page-title">Item Based Purchase</h4>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('purchase_invoices.report') }}" class="mb-4 no-print">
        <div class="row">
            <div class="col-md-3">
                <label for="supplier_id">Supplier</label>
                <select name="supplier_id" class="form-control">
                    <option value="">Select an option</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->acc_id }}" {{ request('supplier_id') == $sup->acc_id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="item_id">Item</label>
                <select name="item_id" class="form-control">
                    <option value="">Select an option</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}" {{ request('item_id') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->item_code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="startDate">Start Date</label>
                <input type="date" name="startDate" class="form-control" value="{{ request('startDate') }}">
            </div>
            <div class="col-md-3">
                <label for="endDate">End Date</label>
                <input type="date" name="endDate" class="form-control" value="{{ request('endDate') }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-success">Filter</button>
                <a href="{{ route('purchase_invoices.report') }}" class="btn btn-secondary">Reset</a>
                <button type="button" class="btn btn-warning" onclick="window.print()">Print</button>
            </div>
        </div>
    </form>

    @php
        $selectedSupplier = request('supplier_id') ? $suppliers->firstWhere('acc_id', request('supplier_id')) : null;
        $startDateFormatted = request('startDate') ? \Carbon\Carbon::parse(request('startDate'))->format('d-m-y') : '01-01-24';
        $endDateFormatted = request('endDate') ? \Carbon\Carbon::parse(request('endDate'))->format('d-m-y') : \Carbon\Carbon::now()->format('d-m-y');
    @endphp

    <!-- Print Only Report Header -->
    <div class="report-header report-header-border mb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div class="header-left" style="font-size: 14px; line-height: 1.4;">
                <div>Start Date: &nbsp;{{ $startDateFormatted }}</div>
                <div>End Date: &nbsp;&nbsp;{{ $endDateFormatted }}</div>
            </div>
            <div class="header-center text-center">
                <h2 class="m-0 font-weight-bold" style="font-size: 26px; color: #000;">Item Based Purchase</h2>
                <div style="font-size: 14px; color: #333;">{{ \Carbon\Carbon::now()->format('d F, Y') }}</div>
            </div>
            <div class="header-right text-right" style="font-size: 14px;">
                <div>Page 1 of 1</div>
            </div>
        </div>
        <div class="mt-3" style="font-size: 16px; font-weight: bold; color: #000;">
            Supplier: &nbsp;{{ $selectedSupplier ? $selectedSupplier->name : 'All Suppliers' }}
        </div>
    </div>

    <table class="table table-bordered report-table">
        <thead class="thead-light">
            <tr>
                <th class="col-nowrap text-center">V.no</th>
                <th class="col-nowrap text-center">Date</th>
                <th>Supplier</th>
                <th>Contractor</th>
                <th>Item</th>
                <th class="col-nowrap text-center">Unit</th>
                <th class="col-nowrap text-center">Lot No</th>
                <th class="col-nowrap text-right">Qty</th>
                <th class="col-nowrap text-right">Rate</th>
                <th class="col-nowrap text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
            <tr>
                <td class="col-nowrap text-center">{{ $inv->v_no }}</td>
                <td class="col-nowrap text-center">{{ $inv->master ? \Carbon\Carbon::parse($inv->master->v_date)->format('d-m-Y') : '-' }}</td>
                <td>{{ $inv->supplier?->name ?? '-' }}</td>
                <td>{{ $inv->contractor?->title ?? '-' }}</td>
                <td>{{ $inv->item?->item_code ?? '-' }}</td>
                <td class="col-nowrap text-center">{{ $inv->item?->itemunits?->name ?? $inv->item?->itemunits?->unit ?? '-' }}</td>
                <td class="col-nowrap text-center">{{ $inv->lot_no ?? '-' }}</td>
                <td class="col-nowrap text-right">{{ $inv->qty }}</td>
                <td class="col-nowrap text-right">{{ number_format($inv->rate, 2) }}</td>
                <td class="col-nowrap text-right">{{ number_format($inv->exclusive, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
