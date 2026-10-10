@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Sale</a></li>
                            <li class="breadcrumb-item active">Delivery Challans</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Delivery Challans</h4>
                </div>
            </div>
        </div>

        <a href="{{ route('sale_order_dc.create') }}" class="btn btn-primary mb-3">Create New Delivery Challan</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('sale_order_dc.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Voucher No</label>
                    <input type="text" name="voucher_no" class="form-control" placeholder="Search by Voucher No"
                        value="{{ request('voucher_no') }}">
                </div>
                <div class="col-md-3">
                    <label>Customer</label>
                    <select name="customer_id" class="form-control">
                        <option value="">All Customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->acc_id }}" {{ request('customer_id') == $customer->acc_id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Start Date</label>
                    <input type="date" name="startDate" class="form-control" value="{{ request('startDate', $startDate ?? now()->subMonths(2)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label>End Date</label>
                    <input type="date" name="endDate" class="form-control" value="{{ request('endDate', $endDate ?? now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="paid_status" class="form-control">
                        <option value="">All</option>
                        <option value="paid" {{ request('paid_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('paid_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-12 mt-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success me-1">Search</button>
                    <a href="{{ route('sale_order_dc.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Voucher No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Sale Order</th>
                    <th>Prepared By</th>
                    <th>Party Ref</th>
                    <th>Bilty No</th>
                    <th>Transport</th>
                    <th>Status</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dcs as $dc)
                    @php
                        $firstDetail = $dc->details->first();
                        $sorVno = $firstDetail ? $firstDetail->sale_order_vno : null;
                        $isPaid = in_array($dc->v_no, $paidDcs);
                        $invNo = $paidInvoices[$dc->v_no] ?? null;
                    @endphp
                    <tr>
                        <td>{{ $dc->v_no }}</td>
                        <td>{{ $dc->v_date }}</td>
                        <td style="font-size: 18px; font-weight:bold;">{{ $dc->customer->name ?? 'N/A' }} @if($dc->customer && $dc->customer->urdu_title)<br><span dir="rtl" class="text-muted">{{ $dc->customer->urdu_title }}</span>@endif</td>
                        <td>
                            @if ($sorVno)
                                <span class="{{ in_array($sorVno, $sorExists) ? '' : 'text-danger fw-bold' }}">{{ $sorVno }}{{ in_array($sorVno, $sorExists) ? '' : ' (Deleted)' }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $dc->prepared_by }}</td>
                        <td style="font-size: 18px; font-weight:bold;">
                            @if ($dc->referenceName)
                                {{ $dc->referenceName->name }}
                                @if ($dc->referenceName->name_urdu)
                                    <br><span dir="rtl" class="text-muted">{{ $dc->referenceName->name_urdu }}</span>
                                @endif
                            @else
                                {{ $dc->ref_no ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $dc->bilty_no }}</td>
                        <td>{{ $dc->transportCompany->name ?? '' }}</td>
                        <td>
                            @if ($isPaid)
                                <span class="badge bg-success">Paid</span> <small class="text-muted">{{ $invNo }}</small>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td class="small">{{ $dc->updated_at ? $dc->updated_at->format('d-m-Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('sale_order_dc.edit', $dc->id) }}" class="btn btn-sm btn-warning edit-dc-btn mb-1" style="width: 105px" data-paid="{{ $isPaid ? 'true' : 'false' }}">Edit</a>
                            <a href="{{ route('sale_invoices.create', ['supplier_id' => $dc->customer_id, 'dc_vno' => $dc->v_no]) }}" class="btn btn-sm btn-success mb-1">Sale Invoice</a>
                            <a href="{{ route('sale_order_dc.print', $dc->id) }}" class="btn btn-sm btn-info" style="width: 105px" target="_blank">Print</a>
                            @if (!$isPaid)
                            <form action="{{ route('sale_order_dc.destroy', $dc->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this Delivery Challan? This will restore SO quantities.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="width: 105px">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-dc-btn').forEach(function(btn) {
        if (btn.getAttribute('data-paid') === 'true') {
            var span = document.createElement('span');
            span.className = 'text-muted small';
            span.title = 'Must delete Sale Invoice first';
            span.textContent = 'Edit locked';
            btn.parentNode.replaceChild(span, btn);
        }
    });
});
</script>
@endsection