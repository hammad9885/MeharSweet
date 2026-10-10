@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Stock Adjustment — {{ $stock_adj->v_no }}</h4>
        <div>
            <a href="{{ route('stock-adj.edit', $stock_adj->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('stock-adj.destroy', $stock_adj->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Delete this voucher ({{ $stock_adj->v_no }})?')">Delete</button>
            </form>
            <a href="{{ route('stock-adj.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    {{-- Master Info --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Voucher No:</strong> {{ $stock_adj->v_no }}
                </div>
                <div class="col-md-4">
                    <strong>Voucher Date:</strong>
                    {{ \Carbon\Carbon::parse($stock_adj->v_date)->format('d-M-Y') }}
                </div>
                <div class="col-md-4">
                    <strong>Prepared By:</strong> {{ $stock_adj->preparedByUser->name ?? $stock_adj->prepared_by }}
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0 align-middle">
                <thead class="table-secondary">
                    <tr class="text-center">
                        <th style="width: 10%">#</th>
                        <th style="width: 25%">Item</th>
                        <th style="width: 15%">Qty</th>
                        <th style="width: 15%">Rate</th>
                        <th style="width: 20%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($stock_adj->details as $i => $detail)
                        @php
                            $lineTotal = $detail->qty * $detail->rate;
                            $grandTotal += $lineTotal;
                        @endphp
                        <tr class="text-center">
                            <td>{{ $i + 1 }}</td>
                            <td>
                                {{ $detail->item->item_code ?? 'Item #' . $detail->item_id }}
                                @if(!empty($detail->item->name))
                                    <div class="text-muted small">{{ $detail->item->name }}</div>
                                @endif
                            </td>
                            <td>{{ number_format($detail->qty, 2) }}</td>
                            <td>{{ number_format($detail->rate, 2) }}</td>
                            <td>{{ number_format($lineTotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No items found for this voucher.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($stock_adj->details->count())
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Grand Total:</th>
                        <th class="text-center">{{ number_format($grandTotal, 2) }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
