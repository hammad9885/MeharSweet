@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Purchase</a></li>
                        <li class="breadcrumb-item active">Create Invoice</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Purchase Invoice</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('purchase_invoices.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-4">
                <label for="supplier_id" class="form-label fw-bold">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->acc_id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="v_date" class="form-label fw-bold">Date</label>
                <input type="date" name="v_date" required id="v_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="ref_no" class="form-label">Ref no</label>
                <input type="text" name="ref_no" id="ref_no" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Prepared By</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
            </div>
        </div>

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="1"></textarea>
            </div>
        </div>

        <div id="purchaseTable"></div>

        <div class="purchase-row border rounded px-3 py-1 mb-3 bg-light fw-bold" id="total-row">
            <div class="row g-2 align-items-center">
                <div class="col-md-3"><strong>Total</strong></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"><strong id="total_qty">0</strong></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"><strong id="total_amount">0</strong></div>
                <div class="col-md-3"></div>
                <div class="col-md-1"></div>
            </div>
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-4">
                <div class="mb-2">
                    <label class="form-label fw-bold">Discount</label>
                    <input type="number" id="grand_discount" class="form-control" value="0" name="grand_discount">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Freight</label>
                    <input type="number" id="freight" class="form-control" value="0" name="freight">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Net Amount</label>
                    <input type="text" id="net_amount" class="form-control" readonly name="net_amount">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
            <button type="submit" class="btn btn-success">Save Invoice</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
/* Make select2 single-select dropdowns the same height as Bootstrap form-control inputs */
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

    // Update rates when supplier changes
    $('#supplier_id').change(function() {
        $('.item-select').each(function() {
            if ($(this).val()) {
                $(this).trigger('change');
            }
        });
    });


    // Tab key: focus the dropdown's search box whenever it opens
    $(document).on('select2:open', function(e) {
        const $sel = $(e.target);
        setTimeout(function() {
            const $search = $sel.data('select2').dropdown.$search;
            if ($search.length) {
                $search.focus();
            }
        }, 100);
    });

    // Tab key: auto-open the dropdown when focus reaches a select2 field
    $(document).on('focus', '.select2-selection--single', function(e) {
        const $select = $(this).closest('.select2-container').siblings('select.select2').first();
        if ($select.length && $select.data('select2') && !$select.data('select2').isOpen()) {
            $select.select2('open');
        }
    });

    function recalcAll() {
        let total = 0;
        let totalQty = 0;
        $('.purchase-row').each(function() {
            const qty = parseFloat($(this).find('.qty').val()) || 0;
            const rate = parseFloat($(this).find('.rate').val()) || 0;
            const amount = qty * rate;
            $(this).find('.amount').val(amount);
            total += amount;
            totalQty += qty;
        });
        const discount = parseFloat($('#grand_discount').val()) || 0;
        const freight = parseFloat($('#freight').val()) || 0;
        const afterDiscount = total - discount;
        $('#grand_total').val(afterDiscount);
        $('#net_amount').val(afterDiscount + freight);
        $('#total_qty').text(totalQty);
        $('#total_amount').text(total.toFixed(2));
    }

    function fetchLotNo(row, itemId, typeTitle) {
        if (!itemId) return;
        if (typeTitle && typeTitle.includes('dana')) {
            row.find('.lot-no').val(0);
            row.find('.lot-no-hidden').val(0);
            return;
        }
        axios.get('/get-next-purchase-lot-no/' + itemId)
            .then(function(res) {
                row.find('.lot-no').val(res.data.lot_no);
                row.find('.lot-no-hidden').val(res.data.lot_no);
            });
    }

    function toggleContractorRequired(row, typeTitle) {
        const contractorSelect = row.find('.contractor-select');
        if (typeTitle.includes('dana')) {
            contractorSelect.attr('required', true);
            contractorSelect.closest('.col-md-3').find('.form-label').html('Contractor <span class="text-danger">*</span>');
        } else {
            contractorSelect.removeAttr('required');
            contractorSelect.closest('.col-md-3').find('.form-label').html('Contractor');
        }
    }

    $(document).on('input', '.qty, .rate', function() {
        recalcAll();
    });

    $('#grand_discount, #freight').on('input', recalcAll);

    // Last rate used per item in previous purchase invoices
    const lastPurchaseRates = @json($lastPurchaseRates ?? []);

    $(document).on('change', '.item-select', function() {
        const row = $(this).closest('.purchase-row');
        const selected = $(this).find(':selected');
        const itemId = $(this).val();
        const supplierId = $('#supplier_id').val() || 0;

        const typeTitle = (selected.attr('data-type-title') || '').toString().trim().toLowerCase();
        const unit = selected.data('unit') || '';
        const setQty = selected.data('set-qty') || '';
        row.find('.unit').val(unit);
        if (setQty) {
            row.find('.qty').val(setQty);
        }
        fetchLotNo(row, itemId, typeTitle);
        toggleContractorRequired(row, typeTitle);

        if (itemId && supplierId && supplierId !== '0') {
            axios.get('/itemSelectedP/' + itemId + '/' + supplierId)
                .then(function(res) {
                    let rate = 0;
                    if (res.data && res.data.lastRate !== undefined && res.data.lastRate !== null) {
                        rate = parseFloat(res.data.lastRate) || 0;
                    } else {
                        rate = parseFloat(selected.data('rate')) || 0;
                    }
                    row.find('.rate').val(rate);
                    recalcAll();
                })
                .catch(function(err) {
                    let rate = parseFloat(selected.data('rate')) || 0;
                    row.find('.rate').val(rate);
                    recalcAll();
                });
        } else {
            let rate = parseFloat(selected.data('rate')) || 0;
            row.find('.rate').val(rate);
            recalcAll();
        }
    });

    $('#addRow').click(function() {
        const container = $('#purchaseTable');
        const index = container.find('.purchase-row').length;
        const row = $(`
        <div class="purchase-row border rounded px-3 py-1 mb-3 bg-light">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label">Item</label>
                    <select name="items[${index}][item_id]" class="form-control item-select select2">
                        <option value="" data-rate="0">-- Select --</option>
                        @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}" data-set-qty="{{ $item->set_qty }}" data-type-title="{{ $item->itemtypes->type_title ?? '' }}" data-unit="{{ $item->itemunits->name ?? '' }}">{{ $item->item_code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Lot No</label>
                    <input type="text" class="form-control lot-no">
                    <input type="hidden" name="items[${index}][lot_no]" class="lot-no-hidden">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Qty</label>
                    <input type="text" name="items[${index}][qty]" class="form-control qty" value="1" min="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Unit</label>
                    <input type="text" class="form-control unit" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Rate</label>
                    <input type="text" name="items[${index}][rate]" class="form-control rate" step="any">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Amount</label>
                    <input type="text" class="form-control amount" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contractor</label>
                    <select name="items[${index}][contractor_id]" class="form-control select2 contractor-select">
                        <option value="">-- Select Contractor --</option>
                        @foreach ($contractors as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-danger btn-sm mt-4 remove-row">X</button>
                </div>
            </div>
        </div>
        `);
        container.append(row);
        row.find('.select2').select2({ width: '100%' });
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('.purchase-row').remove();
        recalcAll();
    });

    
    for (let i = 0; i < 3; i++) {
        $('#addRow').trigger('click');
    }
});
</script>
@endsection
