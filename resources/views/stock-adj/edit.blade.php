@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>Edit Stock Adjustment — {{ $stock_adj->v_no }}</h4>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('stock-adj.update', $stock_adj->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Master Fields --}}
        <div class="row mb-3">
            <input type="hidden" name="v_no" value="{{ $stock_adj->v_no }}">
            <input type="hidden" name="prepared_by" value="{{ $stock_adj->prepared_by }}">
            
            <div class="col-md-4">
                <label>Voucher Date</label>
                <input type="date" name="v_date" required class="form-control" value="{{ $stock_adj->v_date }}" required>
            </div>
        </div>

        <h5>Item Details</h5>
        <table class="table table-bordered" id="detailsTable">
            <thead class="table-secondary">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stock_adj->details as $i => $detail)
                <tr>
                    <td>
                        <select name="details[{{ $i }}][item_id]" class="form-control item-select" required>
                            <option value="">-- Select Item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}"
                                    data-rate="{{ $item->purchase ?? 0 }}"
                                    {{ $item->id == $detail->item_id ? 'selected' : '' }}>
                                    {{ $item->item_code ?? $item->id }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" step="0.01" min="-999999" name="details[{{ $i }}][qty]" class="form-control qty-input" value="{{ $detail->qty }}" required></td>
                    <td><input type="number" step="0.01" name="details[{{ $i }}][rate]" class="form-control rate-input" value="{{ $detail->rate }}" required></td>
                    <td><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" id="addRow" class="btn btn-sm btn-primary mb-3">+ Add Row</button>
        <br>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('stock-adj.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

{{-- Include jQuery and Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
const itemsData = @json($items->mapWithKeys(fn($item) => [$item->id => ['rate' => $item->purchase ?? 0]]));

const itemOptionsHtml = `@foreach($items as $item)
    <option value="{{ $item->id }}" data-rate="{{ $item->purchase ?? 0 }}">{{ $item->item_code ?? $item->id }}</option>
@endforeach`;

let rowIndex = {{ $stock_adj->details->count() }};

// Function: Create new row
function makeItemSelectHTML(index) {
    return `
        <select name="details[${index}][item_id]" class="form-control item-select" required>
            <option value="">-- Select Item --</option>
            ${itemOptionsHtml}
        </select>
    `;
}

function createRow(index) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${makeItemSelectHTML(index)}</td>
        <td><input type="number" step="0.01" min="-999999" name="details[${index}][qty]" class="form-control qty-input" required></td>
        <td><input type="number" step="0.01" name="details[${index}][rate]" class="form-control rate-input" required></td>
        <td><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
    `;
    return tr;
}

// Add new row
$('#addRow').on('click', function() {
    const tbody = $('#detailsTable tbody');
    const newRow = createRow(rowIndex);
    tbody.append(newRow);
    rowIndex++;
    initSelect2();
});

// Remove row
$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
});

// Auto-fill rate when item changes
$(document).on('change', '.item-select', function() {
    const rate = $(this).find(':selected').data('rate') || '';
    $(this).closest('tr').find('.rate-input').val(rate);
});

// Initialize Select2
function initSelect2() {
    $('.item-select').select2({
        placeholder: "-- Select Item --",
        width: '100%'
    });
}

// Initialize on load
$(document).ready(function() {
    initSelect2();
});
</script>
@endsection
