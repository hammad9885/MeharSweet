@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Stock Management</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('packing-lot-posting.index') }}">Packing Lot Posting</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Packing Lot Posting</h4>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('packing-lot-posting.store') }}" method="POST">
        @csrf

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-2">
                <label for="voucher_no" class="form-label fw-bold">Voucher No</label>
                <input type="number" name="voucher_no" id="voucher_no" class="form-control" value="{{ old('voucher_no', $nextVoucherNo) }}" readonly>
            </div>
            <div class="col-md-2">
                <label for="date" class="form-label fw-bold">Date</label>
                <input type="date" name="date" required id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label for="remarks" class="form-label fw-bold">Remarks</label>
                <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks') }}">
            </div>
            <div class="col-md-2">
                <label for="prepared_by" class="form-label fw-bold">Prepared By</label>
                <input type="text" name="prepared_by" id="prepared_by" class="form-control" value="{{ old('prepared_by') }}">
            </div>
            <div class="col-md-3">
                <label for="product_id" class="form-label fw-bold">Product</label>
                <select name="product_id" id="product_id" class="form-control select2">
                    <option value="">-- Select Product --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->product_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label for="lot_no" class="form-label fw-bold">Lot No</label>
                <input type="text" name="lot_no" id="lot_no" class="form-control" value="{{ old('lot_no') }}">
            </div>
            <div class="col-md-3">
                <label for="prev_lot_no" class="form-label fw-bold">Previous Lot Number</label>
                <input type="text" name="prev_lot_no" id="prev_lot_no" class="form-control" value="{{ old('prev_lot_no') }}" readonly>
            </div>
            <div class="col-md-3">
                <label for="prev_lot_amount" class="form-label fw-bold">Previous Lot Amount</label>
                <input type="text" name="prev_lot_amount" id="prev_lot_amount" class="form-control" value="{{ old('prev_lot_amount') }}" readonly>
            </div>
        </div>

        <div id="postingTable"></div>

        <div class="border-top pt-2 mt-0 mb-0 bg-white total-row">
            <div class="row g-2 align-items-center">
                <div class="col-md-3"><strong>Total</strong></div>
                <div class="col-md-2"></div>
                <div class="col-md-1"><strong id="footerQty">0</strong></div>
                <div class="col-md-1"></div>
                <div class="col-md-2"></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"><strong id="footerWagesAmount">0</strong></div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container--default .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5;
    padding-left: 0;
    padding-right: 1.25rem;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    top: 0;
    right: 0.5rem;
}
</style>

<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    $('#addRow').click(function() {
        const container = $('#postingTable');
        const index = container.find('.posting-row').length;
        const row = $(`
        <div class="posting-row border rounded px-3 py-1 mb-3 bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Contractor</label>
                    <select name="details[${index}][contractor_id]" class="form-control contractor-select select2">
                        <option value="">-- Select Contractor --</option>
                        @foreach($contractors as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Item Description</label>
                    <select name="details[${index}][item_id]" class="form-control item-select select2">
                        <option value="">-- Select Item --</option>
                        @foreach($packingItems as $item)
                            <option value="{{ $item->id }}" data-unit="{{ $item->itemunits->name ?? '' }}">{{ $item->item_code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Calc Qty</label>
                    <input type="text" name="details[${index}][qty]" class="form-control qty" value="0" min="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Unit</label>
                    <input type="text" name="details[${index}][unit]" class="form-control unit" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mold</label>
                    <select name="details[${index}][mold_id]" class="form-control mold-select select2">
                        <option value="">-- Select Mold --</option>
                        @foreach($molds as $mold)
                            <option value="{{ $mold->id }}" data-wages="{{ (($mold->total ?? 0) > 0) ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)) }}">{{ $mold->mold_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Mold Quantity</label>
                    <input type="text" name="details[${index}][mold_qty]" class="form-control mold-qty" value="0" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Wages</label>
                    <input type="text" name="details[${index}][wages]" class="form-control wages" value="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Wages Amount</label>
                    <input type="text" name="details[${index}][wages_amount]" class="form-control wages-amount" value="0" readonly>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                </div>
            </div>
        </div>
        `);
        container.append(row);
        row.find('.select2').select2({ width: '100%' });
        if ($('#product_id').val()) {
            populateRowItems(row, currentItems);
        }
    });

    function populateRowItems(row, items) {
        const $select = row.find('.item-select');
        const selected = $select.val();
        $select.empty().append('<option value="">-- Select Item --</option>');
        items.forEach(function(item) {
            $select.append('<option value="' + item.id + '"' +
                ' data-unit="' + (item.unit || '') + '"' +
                ' data-mold-id="' + (item.mold_id || '') + '"' +
                ' data-mold-name="' + (item.mold_name || '') + '"' +
                ' data-mold-qty="' + (item.mold_qty || 0) + '"' +
                ' data-wages="' + (item.wages || 0) + '">' + item.item_code + '</option>');
        });
        $select.val(selected && items.some(function(it) { return String(it.id) === String(selected); }) ? selected : '').trigger('change');
        $select.trigger('change.select2');
    }

    function autoFillFromItem(row) {
        const opt = row.find('.item-select').find(':selected');
        if (!opt.val()) return;
        row.find('.unit').val(opt.data('unit') || '');
        row.find('.mold-qty').val(opt.data('mold-qty') !== undefined ? parseFloat(opt.data('mold-qty')) : 0);
        const moldId = opt.data('mold-id');
        if (moldId) {
            row.find('.mold-select').val(String(moldId)).trigger('change');
        }
        const wages = parseFloat(opt.data('wages')) || 0;
        if (wages > 0) {
            row.find('.wages').val(wages.toFixed(2));
        }
    }

    function recalcRow(row) {
        const moldQty = parseFloat(row.find('.mold-qty').val()) || 0;
        const wages = parseFloat(row.find('.wages').val()) || 0;
        row.find('.wages-amount').val((moldQty * wages).toFixed(2));
    }

    function recalcFooter() {
        let qtyTotal = 0;
        let wagesAmountTotal = 0;
        $('#postingTable .posting-row').each(function() {
            qtyTotal += parseFloat($(this).find('.qty').val()) || 0;
            wagesAmountTotal += parseFloat($(this).find('.wages-amount').val()) || 0;
        });
        $('#footerQty').text(qtyTotal);
        $('#footerWagesAmount').text(wagesAmountTotal.toFixed(2));
    }

    $(document).on('change', '.item-select', function() {
        const row = $(this).closest('.posting-row');
        const productId = $('#product_id').val();
        const contractorId = productId ? (contractorByProduct[productId] || '') : '';
        if (contractorId) {
            row.find('.contractor-select').val(String(contractorId)).trigger('change');
        }
        autoFillFromItem(row);
        recalcRow(row);
        recalcFooter();
    });

    $(document).on('change', '.mold-select', function() {
        const row = $(this).closest('.posting-row');
        const wages = $(this).find(':selected').attr('data-wages');
        if (wages) {
            row.find('.wages').val(parseFloat(wages).toFixed(2));
        }
        recalcRow(row);
        recalcFooter();
    });

    let currentItems = [];

    function addRowWithData(item) {
        const container = $('#postingTable');
        const index = container.find('.posting-row').length;
        const productId = $('#product_id').val();
        const defaultContractorId = item.contractor_id || (productId ? (contractorByProduct[productId] || '') : '');

        let itemOptions = '';
        currentItems.forEach(function(it) {
            const isSelected = String(it.id) === String(item.id) ? 'selected' : '';
            itemOptions += '<option value="' + it.id + '"' +
                ' data-unit="' + (it.unit || '') + '"' +
                ' data-mold-id="' + (it.mold_id || '') + '"' +
                ' data-mold-name="' + (it.mold_name || '') + '"' +
                ' data-mold-qty="' + (it.mold_qty || 0) + '"' +
                ' data-wages="' + (it.wages || 0) + '"' + isSelected + '>' + it.item_code + '</option>';
        });

        const wagesAmount = (parseFloat(item.mold_qty || 0) * parseFloat(item.wages || 0)).toFixed(2);

        const row = $(`
        <div class="posting-row border rounded px-3 py-1 mb-3 bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Contractor</label>
                    <select name="details[${index}][contractor_id]" class="form-control contractor-select select2">
                        <option value="">-- Select Contractor --</option>
                        @foreach($contractors as $c)
                            <option value="{{ $c->id }}" ${String(defaultContractorId) === String('{{ $c->id }}') ? 'selected' : ''}>{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Item Description</label>
                    <select name="details[${index}][item_id]" class="form-control item-select select2">
                        <option value="">-- Select Item --</option>
                        ${itemOptions}
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Calc Qty</label>
                    <input type="text" name="details[${index}][qty]" class="form-control qty" value="${item.calc_qty || 0}" min="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Unit</label>
                    <input type="text" name="details[${index}][unit]" class="form-control unit" value="${item.unit || ''}" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mold</label>
                    <select name="details[${index}][mold_id]" class="form-control mold-select select2">
                        <option value="">-- Select Mold --</option>
                        @foreach($molds as $mold)
                            <option value="{{ $mold->id }}" data-wages="{{ (($mold->total ?? 0) > 0) ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)) }}" ${String(item.mold_id) === String('{{ $mold->id }}') ? 'selected' : ''}>{{ $mold->mold_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Mold Quantity</label>
                    <input type="text" name="details[${index}][mold_qty]" class="form-control mold-qty" value="${item.mold_qty || 0}" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Wages</label>
                    <input type="text" name="details[${index}][wages]" class="form-control wages" value="${parseFloat(item.wages || 0).toFixed(2)}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Wages Amount</label>
                    <input type="text" name="details[${index}][wages_amount]" class="form-control wages-amount" value="${wagesAmount}" readonly>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                </div>
            </div>
        </div>
        `);
        container.append(row);
        row.find('.select2').select2({ width: '100%' });
    }

    $(document).on('change', '#product_id', function() {
        const pid = $(this).val();
        const prevLot = pid ? (previousLotByProduct[pid] || null) : null;
        if (prevLot) {
            $('#prev_lot_no').val(prevLot.lot_no || '');
            $('#prev_lot_amount').val(prevLot.lot_amount !== undefined ? parseFloat(prevLot.lot_amount).toFixed(2) : '');

            const prevStr = String(prevLot.lot_no || '').trim();
            if (prevStr) {
                const match = prevStr.match(/\d+$/);
                if (match) {
                    const num = parseInt(match[0]);
                    const nextNum = num + 1;
                    const paddedNext = String(nextNum).padStart(match[0].length, '0');
                    const nextLot = prevStr.slice(0, -match[0].length) + paddedNext;
                    $('#lot_no').val(nextLot);
                } else {
                    $('#lot_no').val(prevStr + ' 1');
                }
            } else {
                $('#lot_no').val('1');
            }
        } else {
            $('#prev_lot_no').val('');
            $('#prev_lot_amount').val('');
            $('#lot_no').val('1');
        }

        $('#postingTable').empty();
        currentItems = [];

        if (pid) {
            $.getJSON('/packing-lot-posting/items-by-product/' + pid, function(items) {
                currentItems = items;
                if (items.length > 0) {
                    items.forEach(function(item) {
                        addRowWithData(item);
                    });
                } else {
                    $('#addRow').trigger('click');
                }
                recalcFooter();
            });
        } else {
            for (let i = 0; i < 2; i++) {
                $('#addRow').trigger('click');
            }
            recalcFooter();
        }
    });

    $(document).on('input', '.qty, .wages', function() {
        recalcRow($(this).closest('.posting-row'));
        recalcFooter();
    });

    $(document).on('click', '.remove-row', function() {
        const container = $('#postingTable');
        if (container.find('.posting-row').length <= 1) {
            alert('At least one row is required.');
            return;
        }
        $(this).closest('.posting-row').remove();
        recalcFooter();
    });

    const previousLotByProduct = @json($previousLotByProduct);
    const contractorByProduct = @json($contractorByProduct ?? []);

    for (let i = 0; i < 2; i++) {
        $('#addRow').trigger('click');
    }
    recalcFooter();

    const existingProduct = $('#product_id').val();
    if (existingProduct) {
        $('#product_id').trigger('change');
    }
});
</script>
@endsection
