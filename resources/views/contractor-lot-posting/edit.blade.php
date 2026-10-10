@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Sale</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('contractor-lot-posting.index') }}">Contractor Lot Posting</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Contractor Lot Posting #{{ $posting->voucher_no }}</h4>
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

    <form action="{{ route('contractor-lot-posting.update', $posting->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-2">
                <label for="voucher_no" class="form-label fw-bold">Voucher No</label>
                <input type="number" name="voucher_no" id="voucher_no" class="form-control" value="{{ $posting->voucher_no }}" readonly>
            </div>
            <div class="col-md-2">
                <label for="date" class="form-label fw-bold">Date</label>
                <input type="date" name="date" required id="date" class="form-control" value="{{ $posting->date }}">
            </div>
            <div class="col-md-3">
                <label for="remarks" class="form-label fw-bold">Remarks</label>
                <input type="text" name="remarks" id="remarks" class="form-control" value="{{ $posting->remarks }}">
            </div>
            <div class="col-md-2">
                <label for="prepared_by" class="form-label fw-bold">Prepared By</label>
                <input type="text" name="prepared_by" id="prepared_by" class="form-control" value="{{ $posting->prepared_by }}">
            </div>
            <div class="col-md-3">
                <label for="product_id" class="form-label fw-bold">Product</label>
                <select name="product_id" id="product_id" class="form-control select2">
                    <option value="">-- Select Product --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $posting->product_id == $p->id ? 'selected' : '' }}>{{ $p->product_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label for="lot_no" class="form-label fw-bold">Lot No</label>
                <input type="text" name="lot_no" id="lot_no" class="form-control" value="{{ $posting->lot_no }}">
            </div>
            <div class="col-md-4">
                <label for="prev_lot_no" class="form-label fw-bold">Previous Lot No</label>
                <input type="text" name="prev_lot_no" id="prev_lot_no" class="form-control" value="{{ $posting->prev_lot_no }}" readonly>
            </div>
            <div class="col-md-3">
                <label for="prev_lot_amount" class="form-label fw-bold">Previous Lot Amount</label>
                <input type="text" name="prev_lot_amount" id="prev_lot_amount" class="form-control" value="{{ $posting->prev_lot_amount }}" readonly>
            </div>
        </div>

        <div id="postingTable">
            @forelse ($posting->details as $index => $detail)
            <div class="posting-row border rounded px-3 py-1 mb-3 bg-light">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Contractor</label>
                        <select name="details[{{ $index }}][contractor_id]" class="form-control contractor-select select2">
                            <option value="">-- Select Contractor --</option>
                            @foreach($contractors as $c)
                                <option value="{{ $c->id }}" {{ $detail->contractor_id == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Item Description</label>
                        <select name="details[{{ $index }}][item_id]" class="form-control item-select select2" data-selected="{{ $detail->item_id }}">
                            <option value="">-- Select Item --</option>
                            @foreach($rawItems ?? [] as $ri)
                                <option value="{{ $ri->id }}" {{ $detail->item_id == $ri->id ? 'selected' : '' }}>{{ $ri->item_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Calc Qty</label>
                        <input type="text" name="details[{{ $index }}][qty]" class="form-control qty" value="{{ $detail->qty }}" min="0">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Unit</label>
                        <input type="text" name="details[{{ $index }}][unit]" class="form-control unit" value="{{ $detail->unit }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mold</label>
                        <select name="details[{{ $index }}][mold_id]" class="form-control mold-select select2">
                            <option value="">-- Select Mold --</option>
                            @foreach($molds as $mold)
                                <option value="{{ $mold->id }}" data-wages="{{ (($mold->total ?? 0) > 0) ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)) }}" {{ $detail->mold_id == $mold->id ? 'selected' : '' }}>{{ $mold->mold_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Mold Quantity</label>
                        <input type="text" name="details[{{ $index }}][mold_qty]" class="form-control mold-qty" value="{{ $detail->mold_qty }}" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Wages</label>
                        <input type="text" name="details[{{ $index }}][wages]" class="form-control wages" value="{{ $detail->wages }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Wages Amount</label>
                        <input type="text" name="details[{{ $index }}][wages_amount]" class="form-control wages-amount" value="{{ $detail->wages_amount }}" readonly>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                    </div>
                </div>
            </div>
            @empty
            <div class="posting-row border rounded px-3 py-1 mb-3 bg-light">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Contractor</label>
                        <select name="details[0][contractor_id]" class="form-control contractor-select select2">
                            <option value="">-- Select Contractor --</option>
                            @foreach($contractors as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Item Description</label>
                        <select name="details[0][item_id]" class="form-control item-select select2">
                            <option value="">-- Select Item --</option>
                            @foreach($rawItems ?? [] as $ri)
                                <option value="{{ $ri->id }}">{{ $ri->item_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Calc Qty</label>
                        <input type="text" name="details[0][qty]" class="form-control qty" value="0" min="0">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Unit</label>
                        <input type="text" name="details[0][unit]" class="form-control unit" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mold</label>
                        <select name="details[0][mold_id]" class="form-control mold-select select2">
                            <option value="">-- Select Mold --</option>
                            @foreach($molds as $mold)
                                <option value="{{ $mold->id }}" data-wages="{{ (($mold->total ?? 0) > 0) ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)) }}">{{ $mold->mold_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Mold Quantity</label>
                        <input type="text" name="details[0][mold_qty]" class="form-control mold-qty" value="0" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Wages</label>
                        <input type="text" name="details[0][wages]" class="form-control wages" value="0">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Wages Amount</label>
                        <input type="text" name="details[0][wages_amount]" class="form-control wages-amount" value="0" readonly>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

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
            <div>
                <a href="{{ route('contractor-lot-posting.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
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

    const previousLotByProduct = @json($previousLotByProduct);
    const contractorByProduct = @json($contractorByProduct ?? []);
    const contractorByMold = @json($contractorByMold ?? []);
    let currentItems = [];

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
                        @foreach($rawItems ?? [] as $ri)
                            <option value="{{ $ri->id }}">{{ $ri->item_code }}</option>
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
        if ($('#product_id').val() && currentItems.length > 0) {
            populateRowItems(row, currentItems);
        }
    });

    function populateRowItems(row, items, selectedValue, isInitialLoad) {
        const $select = row.find('.item-select');
        const selected = selectedValue !== undefined ? selectedValue : $select.val();
        $select.empty().append('<option value="">-- Select Item --</option>');
        const seenItemIds = {};
        items.forEach(function(item) {
            if (!seenItemIds[item.id]) {
                seenItemIds[item.id] = true;
                $select.append('<option value="' + item.id + '"' +
                    ' data-unit="' + (item.unit || '') + '"' +
                    ' data-mold-id="' + (item.mold_id || '') + '"' +
                    ' data-mold-name="' + (item.mold_name || '') + '"' +
                    ' data-mold-qty="' + (item.mold_qty || 0) + '"' +
                    ' data-wages="' + (item.wages || 0) + '"' +
                    ' data-contractor-id="' + (item.contractor_id || '') + '">' + item.item_code + '</option>');
            }
        });
        if (selected) {
            $select.val(selected);
        }
        $select.trigger('change.select2');
        if (!isInitialLoad) {
            $select.trigger('change');
        }
    }

    function autoFillFromItem(row) {
        const opt = row.find('.item-select').find(':selected');
        if (!opt.val()) return;

        if (opt.data('unit')) {
            row.find('.unit').val(opt.data('unit'));
        }
        const moldId = opt.data('mold-id');
        if (moldId && !row.find('.mold-select').val()) {
            row.find('.mold-select').val(String(moldId)).trigger('change.select2');
            const wages = opt.data('wages');
            if (wages) {
                row.find('.wages').val(parseFloat(wages).toFixed(2));
            }
            const moldQty = opt.data('mold-qty');
            if (moldQty !== undefined && moldQty !== '' && (!row.find('.mold-qty').val() || row.find('.mold-qty').val() == '0')) {
                row.find('.mold-qty').val(parseFloat(moldQty) || 0);
            }
        }
        const itemContractorId = opt.data('contractor-id') || (moldId ? contractorByMold[moldId] : null);
        if (itemContractorId && !row.find('.contractor-select').val()) {
            row.find('.contractor-select').val(String(itemContractorId)).trigger('change.select2');
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
        autoFillFromItem(row);
        recalcRow(row);
        recalcFooter();
    });

    $(document).on('change', '.mold-select', function() {
        const row = $(this).closest('.posting-row');
        const moldId = $(this).val();
        const wages = $(this).find(':selected').attr('data-wages');
        if (wages) {
            row.find('.wages').val(parseFloat(wages).toFixed(2));
        }
        if (moldId) {
            let contractorId = null;
            if (currentItems && currentItems.length > 0) {
                const match = currentItems.find(it => String(it.mold_id) === String(moldId));
                if (match && match.contractor_id) {
                    contractorId = match.contractor_id;
                }
            }
            if (!contractorId && contractorByMold && contractorByMold[moldId]) {
                contractorId = contractorByMold[moldId];
            }
            if (contractorId) {
                row.find('.contractor-select').val(String(contractorId)).trigger('change.select2');
            }
        }
        recalcRow(row);
        recalcFooter();
    });

    function addRowWithData(item) {
        const container = $('#postingTable');
        const index = container.find('.posting-row').length;
        const defaultContractorId = item.contractor_id || (item.mold_id && contractorByMold[item.mold_id]) || '';

        let itemOptions = '<option value="">-- Select Item --</option>';
        const seenItemIds = {};
        currentItems.forEach(function(it) {
            if (!seenItemIds[it.id]) {
                seenItemIds[it.id] = true;
                const isSelected = String(it.id) === String(item.id) ? 'selected' : '';
                itemOptions += '<option value="' + it.id + '"' +
                    ' data-unit="' + (it.unit || '') + '"' +
                    ' data-mold-id="' + (it.mold_id || '') + '"' +
                    ' data-mold-name="' + (it.mold_name || '') + '"' +
                    ' data-mold-qty="' + (it.mold_qty || 0) + '"' +
                    ' data-wages="' + (it.wages || 0) + '"' +
                    ' data-contractor-id="' + (it.contractor_id || '') + '"' + isSelected + '>' + it.item_code + '</option>';
            }
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
        } else {
            $('#prev_lot_no').val('');
            $('#prev_lot_amount').val('');
        }

        $('#postingTable').empty();
        currentItems = [];

        if (pid) {
            $.getJSON('/contractor-lot-posting/items-by-product/' + pid, function(items) {
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

    const rowCount = $('#postingTable .posting-row').length;
    for (let i = rowCount; i < 2; i++) {
        $('#addRow').trigger('click');
    }
    recalcFooter();

    const existingProduct = $('#product_id').val();
    if (existingProduct) {
        $.getJSON('/contractor-lot-posting/items-by-product/' + existingProduct, function(items) {
            currentItems = items;
        });
    }
});
</script>
@endsection