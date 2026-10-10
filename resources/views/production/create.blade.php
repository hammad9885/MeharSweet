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
                    <h4 class="page-title">Add Production</h4>
                </div>
            </div>
        </div>


    <form action="{{ route('production.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Date</label>
            <input type='date' name='v_date' class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <input type="text" name="descr" class="form-control">
        </div>

        <h4>Items</h4>
        <table class="table table-bordered" id="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary" id="add-row">+ Add Row</button>
        <button type="submit" class="btn btn-success ">Save</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let index = 1;

// ✅ Add new row
$('#add-row').on('click', function() {
    let row = `
    <tr>
        <td>
            <select name="items[${index}][item_id]" class="form-control item-select select2" required>
                <option value="">Select</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}" data-cost="{{ $item->cost_price }}">
                        {{ $item->item_code }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[${index}][qty]" class="form-control qty" required step="any"></td>
        <td><input type="number" name="items[${index}][rate]" class="form-control rate" required step="any"></td>
        <td><input type="text" name="items[${index}][amount]" class="form-control amount" readonly></td>
        <td><button type="button" class="btn btn-danger remove">x</button></td>
    </tr>`;
    $('#items-table tbody').append(row);
    initSelect2();
    index++;
});
function initSelect2() {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Search...',
        allowClear: true
    }).on('change', function() {
        const selected = $(this).find(':selected');
        console.log(selected.data());
        const rate = selected.data('cost')==0 || selected.data('cost')==null ? selected.data('rate') ?? 0 : selected.data('cost');
        const row = $(this).closest('tr');
        if (row.length) {
            row.find('.rate').val(rate);
            recalcRow(row);
        }
    });
}
// ✅ Remove row
$(document).on('click', '.remove', function() {
    $(this).closest('tr').remove();
});

// ✅ Set rate when item selected (works for both first & new rows)
$(document).on('change', '.item-select', function() {
    const cost = $(this).find(':selected').data('cost') ?
    $(this).find(':selected').data('cost') : $(this).find(':selected').data('rate') ? $(this).find(':selected').data('rate') : 0;

    let row = $(this).closest('tr');
    row.find('.rate').val(cost).trigger('input'); // recalc amount
});

// ✅ Auto-calculate amount
$(document).on('input', '.qty, .rate', function() {
    let row = $(this).closest('tr');
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let rate = parseFloat(row.find('.rate').val()) || 0;
    row.find('.amount').val((qty * rate).toFixed(2));
});
</script>
@endsection
