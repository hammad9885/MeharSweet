@extends('layouts.app')
@section('content')
<style>
    .card-body { overflow: visible !important; }
    .select2-container { z-index: 9999; }
    .remove_row { background: #dc3545; color: white; border: none; border-radius: 4px; padding: 0.25rem 0.5rem; cursor: pointer; }
    .remove_row:hover { background: #c82333; }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Tables</a></li>
                        <li class="breadcrumb-item active">Add Consumption 2</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Consumption 2</h4>
            </div>
        </div>
    </div>

    <form id="consumptionForm" action="{{ route('consumption2.store') }}" method="POST">
        @csrf

        {{-- Basic Info --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Date <span class="text-danger">*</span></label>
                <input type="date" name="date" id="entryDate" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Lot No</label>
                <input type="text" name="lot_no" id="lot_no" class="form-control" placeholder="Lot number">
            </div>
            <div class="col-md-6">
                <label>Description</label>
                <input type="text" name="descr" id="entryDescr" class="form-control" placeholder="Description">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Product (Finished Good) <span class="text-danger">*</span></label>
                <select name="product" id="product" class="form-control select2" required>
                    <option value="">Select Product</option>
                    @foreach ($productMaster as $item)
                        <option value="{{ $item->id }}">{{ $item->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Product Qty <span class="text-danger">*</span></label>
                <input type="number" name="product_qty" id="product_qty" class="form-control" required step="any">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Lot Qty</label>
                <input type="number" name="lot_qty" id="lot_qty" class="form-control" step="any" placeholder="Lot quantity">
            </div>
            <div class="col-md-3">
                <label>Qty Per Carton</label>
                <input type="number" name="qty_per_carton" id="qty_per_carton" class="form-control" step="any" placeholder="e.g. 500">
            </div>
            <div class="col-md-3">
                <label>Cartons In Lot</label>
                <input type="number" name="cartons_in_lot" id="cartons_in_lot" class="form-control" step="any" placeholder="Auto-calculated" readonly>
            </div>
            <div class="col-md-3">
                <label>Target Qty</label>
                <input type="number" name="target_qty" id="target_qty" class="form-control" step="any" placeholder="Target quantity">
            </div>
        </div>
        <div class="mb-3">
            <button type="button" id="addRowBtn" class="btn btn-primary">+ Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="entriesTable">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:25px">#</th>
                        <th style="min-width:210px">Item (Raw Material)</th>
                        <th style="min-width:190px">Mold</th>
                        <th style="min-width:115px">Mold Qty</th>
                        <th style="min-width:100px; background-color: '#58a3b3' !important">Grams</th>
                        <th style="min-width:110px">Calc Qty</th>
                        <th style="min-width:90px">Unit</th>
                        <th style="min-width:200px">Contractor</th>
                        <th style="min-width:110px; background-color: '#58a3b3' !important">Wages Rate</th>
                        <th style="min-width:100px">Mat Rate</th>
                        <th style="min-width:90px">M.Caps</th>
                        <th style="min-width:100px">Stroke</th>
                        <th style="min-width:110px">Hatt</th>
                        <th style="min-width:80px">Action</th>
                    </tr>
                </thead>
                <tbody id="entriesBody">
                    <tr id="row_1">
                        <td>1</td>
                        <td>
                            <select class="form-control select2 raw-material-select" style="width:100%;">
                                <option value="">Select Raw Material</option>
                                @foreach ($raw as $item)
                                    <option value="{{ $item->id }}"
                                        data-rate="{{ $item->purchase ?? 0 }}"
                                        data-purchase-rate="{{ $latestRates[$item->id] ?? '' }}"
                                        data-mold="{{ $item->mold_id }}"
                                        data-cost="{{ $item->cost_price ?? 0 }}"
                                        data-unit-name="{{ $item->itemunits->name ?? '' }}">
                                        {{ $item->item_code }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="details[1][item_id]" class="raw-material-hidden">
                        </td>
                        <td>
                            <select class="form-control select2 mold-select" style="width:100%;">
                                <option value="">Select Mold</option>
                                @foreach($molds ?? [] as $mold)
                                    <option value="{{ $mold->id }}" data-wages="{{ ($mold->total ?? 0) > 0 ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)) }}" data-weight="{{ $mold->weight ?? 0 }}">{{ $mold->mold_name ?? $mold->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="details[1][mold_id]" class="mold-hidden">
                        </td>
                        <td><input type="text" name="details[1][mold_qty]" class="form-control mold_qty" step="any"></td>
                        <td><input type="text" name="details[1][grams]" class="form-control grams" style="background-color: '#58a3b3' !important" readonly step="any"></td>
                        <td><input type="text" name="details[1][calc_qty]" class="form-control calc_qty" step="any"></td>
                        <td><input type="text" name="details[1][unit]" class="form-control unit" readonly></td>
                        <td>
                            <select class="form-control select2 contractor-select" style="width:100%;">
                                <option value="">Select Contractor</option>
                                @foreach($contractors ?? [] as $contractor)
                                <option value="{{ $contractor->id }}">{{ $contractor->title ?? $contractor->account_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="details[1][contractor_account_id]" class="contractor-hidden">
                        </td>
                        <td><input type="text" name="details[1][wages_rate]" style="background-color: '#58a3b3' !important" class="form-control wages_rate" step="any"></td>
                        <td><input type="text" name="details[1][material_rate]" class="form-control material_rate" step="any"></td>
                        <td><input type="text" name="details[1][machine_capacity]" class="form-control machine_capacity"></td>
                        <td><input type="text" name="details[1][stroke]" class="form-control stroke"></td>
                        <td><input type="text" name="details[1][hatt]" class="form-control hatt"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove_row"><i class="fa fa-close"></i> Remove</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td><strong>Total</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td  style="background-color: '#58a3b3' !important"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td  style="background-color: '#58a3b3' !important"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('consumption2.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(function() {
    function whenReady() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
            setTimeout(whenReady, 30);
            return;
        }
        jQuery(document).ready(function($) {
            let nextRowId = 1;

            $('.select2').select2({ width: '100%', placeholder: 'Search...', allowClear: true });

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

            function recalcRow(row) {
                const moldQty = parseFloat($(row).find('.mold_qty').val()) || 0;
                const grams = parseFloat($(row).find('.grams').val()) || 0;

                if (moldQty > 0 && grams > 0) {
                    $(row).find('.calc_qty').val(((moldQty * grams) / 25000).toFixed(2));
                }
            }

            function syncDescription() {
                const productText = $('#product option:selected').text().trim();
                const lotNo = $('#lot_no').val().trim();
                if (productText && productText !== 'Select Product') {
                    let text = productText;
                    if (lotNo) {
                        text += ' - Lot # ' + lotNo;
                    }
                    const $desc = $('#entryDescr');
                    if (!$desc.val() || $desc.data('auto') === '1') {
                        $desc.val(text).data('auto', '1');
                    }
                }
            }

            $('#product').on('change', syncDescription);
            $('#lot_no').on('input', syncDescription);
            $('#entryDescr').on('input', function() {
                $(this).data('auto', '0');
            });

            $(document).on('change', '.raw-material-select', function () {
                const $row = $(this).closest('tr');
                const opt = $(this).find(':selected');
                const itemId = $(this).val();
                $row.find('.raw-material-hidden').val(itemId);
                const unitName = opt.data('unit-name');
                const moldId = opt.data('mold');

                $row.find('.unit').val(unitName || '');
                if (moldId) {
                    const $moldSelect = $row.find('.mold-select');
                    $moldSelect.val(moldId).trigger('change');
                    $row.find('.mold-hidden').val(moldId);
                }

                if (itemId) {
                    const purchaseRate = opt.data('purchase-rate');
                    if (purchaseRate !== undefined && purchaseRate !== '' && parseFloat(purchaseRate) > 0) {
                        $row.find('.material_rate').val(parseFloat(purchaseRate).toFixed(2));
                    } else {
                        $.ajax({
                            url: '{{ url("/consumption2/material-rate") }}/' + itemId,
                            type: 'GET',
                            success: function (res) {
                                if (res && res.rate !== null && res.rate !== undefined && parseFloat(res.rate) > 0) {
                                    $row.find('.material_rate').val(parseFloat(res.rate).toFixed(2));
                                } else {
                                    $row.find('.material_rate').val('');
                                }
                            },
                            error: function () {
                                $row.find('.material_rate').val('');
                            }
                        });
                    }
                } else {
                    $row.find('.material_rate').val('');
                }
            });

            $(document).on('change', '.mold-select', function () {
                const $row = $(this).closest('tr');
                const wages = $(this).find(':selected').attr('data-wages');
                const weight = $(this).find(':selected').data('weight');
                $row.find('.mold-hidden').val($(this).val());
                if (wages) {
                    $row.find('.wages_rate').val(parseFloat(wages).toFixed(2));
                }
                if (weight) {
                    $row.find('.grams').val(weight);
                }
                recalcRow($row);
            });

            $(document).on('change', '.contractor-select', function () {
                const $row = $(this).closest('tr');
                $row.find('.contractor-hidden').val($(this).val());
            });

            $(document).on('input', '.mold_qty, .grams', function () {
                recalcRow($(this).closest('tr'));
            });

            $(document).on('input', '.wages_rate', function () {
                const $row = $(this).closest('tr');
                // Always show mold form's Total value, ignore user input
                const moldWages = parseFloat($row.find('.mold-select :selected').attr('data-wages')) || 0;
                if (moldWages > 0) {
                    $row.find('.wages_rate').val(moldWages.toFixed(2));
                }
            });

            $(document).on('click', '.remove_row', function () {
                $(this).closest('tr').remove();
            });

            $('#product_qty, #qty_per_carton').on('input', function () {
                const productQty = parseFloat($('#product_qty').val()) || 0;
                const qtyPerCarton = parseFloat($('#qty_per_carton').val()) || 0;
                if (qtyPerCarton > 0) {
                    $('#cartons_in_lot').val(Math.floor(productQty / qtyPerCarton));
                } else {
                    $('#cartons_in_lot').val('');
                }
            });

            $('#addRowBtn').on('click', function () {
                nextRowId++;
                const rawOptions = @json($raw);
                const moldOptions = @json($molds ?? []);
                const contractorOptions = @json($contractors ?? []);
                const latestRatesMap = @json($latestRates ?? []);

                let rawHtml = '<option value="">Select Raw Material</option>';
                rawOptions.forEach(function(item) {
                    const pRate = (latestRatesMap[item.id] !== undefined && latestRatesMap[item.id] !== null) ? latestRatesMap[item.id] : '';
                    rawHtml += '<option value="' + item.id + '" data-rate="' + (item.purchase ?? 0) + '" data-purchase-rate="' + pRate + '" data-mold="' + (item.mold_id ?? '') + '" data-cost="' + (item.cost_price ?? 0) + '" data-unit-name="' + (item.itemunits?.name ?? '') + '">' + item.item_code + '</option>';
                });

                let moldHtml = '<option value="">Select Mold</option>';
                moldOptions.forEach(function(m) {
                    moldHtml += '<option value="' + m.id + '" data-wages="' + ((parseFloat(m.total) > 0 ? parseFloat(m.total) : ((parseFloat(m.wages) || 0) + (parseFloat(m.expense) || 0)))) + '" data-weight="' + (m.weight ?? 0) + '">' + (m.mold_name ?? m.name) + '</option>';
                });

                let contractorHtml = '<option value="">Select Contractor</option>';
                contractorOptions.forEach(function(c) {
                    contractorHtml += '<option value="' + c.id + '">' + (c.title ?? c.account_name) + '</option>';
                });

                const newRow = [
                    '<tr id="row_' + nextRowId + '">',
                    '<td>' + nextRowId + '</td>',
                    '<td>',
                    '<select class="form-control select2 raw-material-select" style="width:100%;">' + rawHtml + '</select>',
                    '<input type="hidden" name="details[' + nextRowId + '][item_id]" class="raw-material-hidden">',
                    '</td>',
                    '<td>',
                    '<select class="form-control select2 mold-select" style="width:100%;">' + moldHtml + '</select>',
                    '<input type="hidden" name="details[' + nextRowId + '][mold_id]" class="mold-hidden">',
                    '</td>',
                    '<td><input type="number" name="details[' + nextRowId + '][mold_qty]" class="form-control mold_qty" step="any"></td>',
                    '<td><input type="text" name="details[' + nextRowId + '][machine_capacity]" class="form-control machine_capacity"></td>',
                    '<td><input type="text" name="details[' + nextRowId + '][stroke]" class="form-control stroke"></td>',
                    '<td><input type="number" name="details[' + nextRowId + '][grams]" class="form-control grams" step="any"></td>',
                    '<td><input type="number" name="details[' + nextRowId + '][calc_qty]" class="form-control calc_qty" step="any"></td>',
                    '<td><input type="text" name="details[' + nextRowId + '][unit]" class="form-control unit" readonly></td>',
                    '<td>',
                    '<select class="form-control select2 contractor-select" style="width:100%;">' + contractorHtml + '</select>',
                    '<input type="hidden" name="details[' + nextRowId + '][contractor_account_id]" class="contractor-hidden">',
                    '</td>',
                    '<td><input type="number" name="details[' + nextRowId + '][wages_rate]" class="form-control wages_rate" step="any"></td>',
                    '<td><input type="number" name="details[' + nextRowId + '][material_rate]" class="form-control material_rate" step="any"></td>',
                    '<td><input type="text" name="details[' + nextRowId + '][hatt]" class="form-control hatt"></td>',
                    '<td><button type="button" class="btn btn-danger btn-sm remove_row"><i class="fa fa-close"></i></button></td>',
                    '</tr>'
                ].join('');
                $('#entriesTable tbody').append(newRow);
                $('.select2').select2({ width: '100%', placeholder: 'Search...', allowClear: true });
            });
        });
    }
    whenReady();
})();
</script>
@endsection