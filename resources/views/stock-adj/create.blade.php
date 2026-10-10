@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>Add Stock Adjustment</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('stock-adj.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            {{-- hidden voucher no and prepared_by (set server-side) --}}
            <input type="hidden" name="v_no" value="{{ $nextVNo ?? '' }}">
            <input type="hidden" name="prepared_by" value="{{ $preparedByCid ?? '' }}">

            <div class="col-md-4">
                <label>Voucher Date</label>
                <input type="date" name="v_date" required class="form-control" required>
            </div>
        </div>

        <h5>Item Details</h5>
        <table class="table table-bordered" id="detailsTable">
            <thead class="table-secondary">
                <tr>
                    <th>Item</th>
                    <th>Qty (+/-)</th>
                    <th>Rate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added dynamically -->
            </tbody>
        </table>

        <button type="button" id="addRow" class="btn btn-sm btn-primary mb-3">+ Add Row</button>
        <br>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('stock-adj.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

{{-- ✅ Include Select2 (for searchable dropdowns) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// ✅ Build item options dynamically from PHP
const itemOptionsHtml = `@foreach($items as $item)
    <option value="{{ $item->id }}" data-rate="{{ $item->purchase ?? 0 }}">
        {{ $item->item_code ?? $item->id }} - {{ $item->description ?? '' }}
    </option>
@endforeach`;

let rowIndex = 0;

// 🔧 create new table row
function createRow(index) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="details[${index}][item_id]" class="form-control item-select select2" required>
                <option value="">-- Select Item --</option>
                ${itemOptionsHtml}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="-999999" 
                   name="details[${index}][qty]" 
                   class="form-control qty-input" 
                   required placeholder="+ Add / - Reduce">
        </td>
        <td>
            <input type="number" step="0.01" 
                   name="details[${index}][rate]" 
                   class="form-control rate-input" 
                   required placeholder="Editable Rate">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger removeRow">X</button>
        </td>
    `;
    return tr;
}

// 🔧 set rate automatically when item changes
function setRateForRow(selectElement) {
    const tr = selectElement.closest('tr');
    const rateInput = tr.querySelector('.rate-input');
    const selectedOpt = selectElement.selectedOptions[0];
    const rate = selectedOpt ? selectedOpt.getAttribute('data-rate') : '';
    rateInput.value = rate || '';
}

// ✅ Add row
document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.querySelector('#detailsTable tbody');
    const row = createRow(rowIndex++);
    tbody.appendChild(row);

    // reinitialize select2
    $(row).find('.select2').select2({
        width: '100%',
        placeholder: '-- Select Item --',
        allowClear: true
    }).on('change', function() {
        setRateForRow(this);
    });
});

// ✅ Remove row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('tr').remove();
    }
});

// ✅ Initialize any existing rows (if preloaded)
document.addEventListener('DOMContentLoaded', function() {
    $('.select2').select2({
        width: '100%',
        placeholder: '-- Select Item --',
        allowClear: true
    });
});
</script>
@endsection
