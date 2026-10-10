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
                        <li class="breadcrumb-item active">Create Stock Issue</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Stock Issue</h4>
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

    <form action="{{ route('stock-issue.store') }}" method="POST">
        @csrf

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-2">
                <label for="inv_no" class="form-label fw-bold">Inv No</label>
                <input type="number" name="inv_no" id="inv_no" class="form-control" value="{{ old('inv_no', $nextInvNo) }}" readonly>
            </div>
            <div class="col-md-2">
                <label for="date" class="form-label fw-bold">Date</label>
                <input type="date" name="date" required id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}">
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
            <div class="col-md-3">
                <label for="contractor_id" class="form-label fw-bold">Contractor</label>
                <select name="contractor_id" id="contractor_id" class="form-control select2">
                    <option value="">-- Select Contractor --</option>
                    @foreach($contractors as $c)
                        <option value="{{ $c->id }}" {{ old('contractor_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="lot_no" class="form-label fw-bold">Lot No</label>
                <input type="text" name="lot_no" id="lot_no" class="form-control" value="{{ old('lot_no') }}">
            </div>
        </div>

        <div id="stockIssueTable"></div>

        <div class="border-top pt-2 mt-0 mb-0 bg-white total-row">
            <div class="row g-2 align-items-center">
                <div class="col-md-3"><strong>Total</strong></div>
                <div class="col-md-3"></div>
                <div class="col-md-2"><strong id="footerQty">0</strong></div>
                <div class="col-md-2"></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"></div>
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

    $('#addRow').click(function() {
        const container = $('#stockIssueTable');
        const index = container.find('.stockissue-row').length;
        const row = $(`
        <div class="stockissue-row border rounded px-3 py-1 mb-3 bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Item</label>
                    <select name="details[${index}][product_id]" class="form-control product-select select2">
                        <option value="">-- Select Item --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lot Quantity</label>
                    <input type="text" class="form-control lot-qty" value="0" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="text" name="details[${index}][qty]" class="form-control qty" value="0" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Previous</label>
                    <input type="text" class="form-control previous" value="0" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control total" value="0" readonly>
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
        const $select = row.find('.product-select');
        const selected = $select.val();
        $select.empty().append('<option value="">-- Select Item --</option>');
        items.forEach(function(item) {
            $select.append('<option value="' + item.id + '">' + item.item_code + '</option>');
        });
        $select.val(selected && items.some(function(it) { return String(it.id) === String(selected); }) ? selected : '').trigger('change');
        $select.trigger('change.select2');
    }

    function recalcRow(row) {
        const prev = parseFloat(row.find('.previous').val()) || 0;
        const qty = parseFloat(row.find('.qty').val()) || 0;
        row.find('.total').val(prev + qty);
    }

    function recalcFooter() {
        let qtyTotal = 0;
        let totalTotal = 0;
        $('#stockIssueTable .stockissue-row').each(function() {
            qtyTotal += parseFloat($(this).find('.qty').val()) || 0;
            totalTotal += parseFloat($(this).find('.total').val()) || 0;
        });
        $('#footerQty').text(qtyTotal);
        $('#footerTotal').text(totalTotal);
    }

    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('.stockissue-row');
        const pid = $(this).val();
        const productId = $('#product_id').val();
        const prev = pid ? (previousBalances[pid] || 0) : 0;
        row.find('.previous').val(prev);

        if (pid && productId) {
            $.getJSON('/stock-issue/get-mold-qty/' + productId + '/' + pid, function(res) {
                row.find('.lot-qty').val(res.mold_qty ? parseFloat(res.mold_qty) : 0);
            }).fail(function() {
                row.find('.lot-qty').val(0);
            });
        } else {
            row.find('.lot-qty').val(0);
        }

        recalcRow(row);
        recalcFooter();
    });

    let currentItems = [];

    $(document).on('change', '#product_id', function() {
        const pid = $(this).val();
        const contractorId = pid ? (contractorByProduct[pid] || '') : '';
        if (contractorId) {
            $('#contractor_id').val(contractorId).trigger('change');
        }
        currentItems = [];
        if (pid) {
            $.getJSON('/stock-issue/items-by-product/' + pid, function(items) {
                currentItems = items;
                $('#stockIssueTable').find('.stockissue-row').each(function() {
                    populateRowItems($(this), items);
                });
            });
        } else {
            $('#stockIssueTable').find('.stockissue-row').each(function() {
                populateRowItems($(this), []);
            });
        }
    });

    $(document).on('input', '.qty', function() {
        recalcRow($(this).closest('.stockissue-row'));
        recalcFooter();
    });

    $(document).on('click', '.remove-row', function() {
        const container = $('#stockIssueTable');
        if (container.find('.stockissue-row').length <= 1) {
            alert('At least one row is required.');
            return;
        }
        $(this).closest('.stockissue-row').remove();
        recalcFooter();
    });

    const previousBalances = @json($previousBalances);
    const contractorByProduct = @json($contractorByProduct ?? []);

    for (let i = 0; i < 2; i++) {
        $('#addRow').trigger('click');
    }
    recalcFooter();
});
</script>
@endsection
