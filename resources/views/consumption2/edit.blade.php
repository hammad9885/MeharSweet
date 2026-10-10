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
                        <li class="breadcrumb-item"><a href="{{ url('/dexter-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('consumption2.index') }}">Consumption 2</a></li>
                        <li class="breadcrumb-item active">Edit Consumption 2</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Consumption 2</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="editConsumptionForm" action="{{ route('consumption2.update', $consumption->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Date <span class="text-danger">*</span></label>
                <input type="date" name="date" id="entryDate" class="form-control" value="{{ $consumption->date }}" required>
            </div>
            <div class="col-md-3">
                <label>Lot No</label>
                <input type="text" name="lot_no" id="lot_no" class="form-control" value="{{ $consumption->lot_no }}" placeholder="Lot number">
            </div>
            <div class="col-md-6">
                <label>Description</label>
                <input type="text" name="descr" id="entryDescr" class="form-control" value="{{ $consumption->descr }}" placeholder="Description">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Product (Finished Good) <span class="text-danger">*</span></label>
                <select name="product" id="product" class="form-control select2" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $item)
                        <option value="{{ $item->id }}" {{ $consumption->item_id == $item->id ? 'selected' : '' }}>{{ $item->product_name }}</option>
                    @endforeach
                </select>
                @php $defaultItemTitle = $consumption->item_title ?? ($consumption->details->first()->item_id ?? ''); @endphp
                <input type="hidden" name="item_title" value="{{ $defaultItemTitle }}">
            </div>
            <div class="col-md-3">
                <label>Product Qty <span class="text-danger">*</span></label>
                <input type="number" name="product_qty" id="product_qty" class="form-control" value="{{ $consumption->qty }}" required step="any">
            </div>
            <!-- <div class="col-md-3">
                <label>Cost Price</label>
                <input type="number" name="cost_price" id="cost_price" class="form-control" value="{{ $consumption->cost_price }}" step="any">
            </div> -->
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Lot Qty</label>
                <input type="number" name="lot_qty" id="lot_qty" class="form-control" value="{{ $consumption->lot_qty }}" step="any" placeholder="Lot quantity">
            </div>
            <div class="col-md-3">
                <label>Qty Per Carton</label>
                <input type="number" name="qty_per_carton" id="qty_per_carton" class="form-control" value="{{ $consumption->qty_per_carton }}" step="any" placeholder="e.g. 500">
            </div>
            <div class="col-md-3">
                <label>Cartons In Lot</label>
                <input type="number" name="cartons_in_lot" id="cartons_in_lot" class="form-control" value="{{ $consumption->cartons_in_lot }}" step="any" placeholder="Auto-calculated" readonly>
            </div>
            <div class="col-md-3">
                <label>Target Qty</label>
                <input type="number" name="target_qty" id="target_qty" class="form-control" value="{{ $consumption->target_qty }}" step="any" placeholder="Target quantity">
            </div>
        </div>

        {{-- Add Row Button --}}
        <div class="mb-3">
            <button type="button" id="addRowBtn" class="btn btn-primary">+ Add Row</button>
        </div>

        {{-- Entries Table with ALL fields --}}
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
                        <!-- <th style="min-width:110px">Wages Amt</th> -->
                        <th style="min-width:100px">Mat Rate</th>
                        <!-- <th style="min-width:110px">Mat Amt</th> -->
                        <th style="min-width:90px">M.Caps</th>
                        <th style="min-width:100px">Stroke</th>
                        <th style="min-width:110px">Hatt</th>
                        <th style="min-width:80px">Action</th>
                    </tr>
                </thead>
                <tbody id="entriesBody">
                    @foreach ($consumption->details as $index => $detail)
                    @php
                        $rowIndex = $loop->iteration;
                        $detailItemId = $detail->item_id;
                        $detailMoldQty = $detail->qty;
                        $detailWagesRate = $detail->rate;
                        $detailWagesAmount = $detail->amount;
                        // Per-row fields from detail table; fallback to master for old records (null in details)
                        $detailMoldId = $detail->mold_id ?? $consumption->mold_id;
                        $detailGrams = $detail->grams ?? $consumption->grams;
                        $detailCalcQty = $detail->calc_qty ?? $consumption->calc_qty;
                        $detailUnit = $detail->unit ?? $consumption->unit;
                        $detailContractorId = $detail->contractor_account_id ?? $consumption->contractor_account_id;
                        $latestRate = $latestRates[$detailItemId] ?? null;
                        $detailMaterialRate = $latestRate ?? ($detail->material_rate ?? $consumption->material_rate);
                        $detailMatAmount = $detail->mat_amount ?? $consumption->mat_amount;
                        $detailMachineCapacity = $detail->machine_capacity ?? $consumption->machine_capacity;
                        $detailStroke = $detail->stroke ?? $consumption->stroke;
                        $detailHatt = $detail->hatt ?? $consumption->hatt;
                    @endphp
                    <tr id="row_{{ $rowIndex }}" @if($loop->first) data-master-row="1" @endif>
                        <td>{{ $rowIndex }}</td>
                        <td>
                            <select class="form-control select2 raw-material-select" style="width:100%;">
                                <option value="">Select Raw Material</option>
                                @foreach ($raw as $item)
                                    <option value="{{ $item->id }}"
                                        data-rate="{{ $item->purchase ?? 0 }}"
                                        data-purchase-rate="{{ $latestRates[$item->id] ?? '' }}"
                                        data-mold="{{ $item->mold_id }}"
                                        data-cost="{{ $item->cost_price ?? 0 }}"
                                        data-unit-name="{{ $item->itemunits->name ?? '' }}"
                                        {{ $detailItemId == $item->id ? 'selected' : '' }}>
                                        {{ $item->item_code }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="details[{{ $rowIndex }}][item_id]" class="raw-material-hidden" value="{{ $detailItemId }}">
                        </td>
                        <td>
                            <select class="form-control select2 mold-select" style="width:100%;">
                                <option value="">Select Mold</option>
                                @foreach($molds ?? [] as $mold)
                                    <option value="{{ $mold->id }}" data-wages="{{ ($mold->total ?? 0) > 0 ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)) }}" data-weight="{{ $mold->weight ?? 0 }}"
                                        {{ $detailMoldId == $mold->id ? 'selected' : '' }}>
                                        {{ $mold->mold_name ?? $mold->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="details[{{ $rowIndex }}][mold_id]" class="mold-hidden" value="{{ $detailMoldId }}">
                        </td>
                        <td><input type="text" name="details[{{ $rowIndex }}][mold_qty]" class="form-control mold_qty" value="{{ $detailMoldQty }}" step="any"></td>
                        <td><input type="text" name="details[{{ $rowIndex }}][grams]" style="background-color: '#58a3b3' !important" class="form-control grams" readonly value="{{ $detailGrams }}" step="any"></td>
                        <td><input type="text" name="details[{{ $rowIndex }}][calc_qty]" class="form-control calc_qty" value="{{ $detailCalcQty }}" step="any"></td>
                        <td><input type="text" name="details[{{ $rowIndex }}][unit]" class="form-control unit" value="{{ $detailUnit }}" readonly></td>
                        <td>
                            <select class="form-control select2 contractor-select" style="width:100%;">
                                <option value="">Select Contractor</option>
                                @foreach($contractors ?? [] as $contractor)
                                <option value="{{ $contractor->id }}"
                                    {{ $detailContractorId == $contractor->id ? 'selected' : '' }}>
                                    {{ $contractor->title ?? $contractor->account_name }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="details[{{ $rowIndex }}][contractor_account_id]" class="contractor-hidden" value="{{ $detailContractorId }}">
                        </td>
                        <td><input type="text" name="details[{{ $rowIndex }}][wages_rate]" style="background-color: '#58a3b3' !important" class="form-control wages_rate" value="{{ $detailWagesRate }}" step="any"></td>
                        <!-- <td><input type="text" name="details[{{ $rowIndex }}][wages_amount]" class="form-control wages_amount" value="{{ intval($detailWagesAmount) }}" step="1" readonly></td> -->
                        <td><input type="text" name="details[{{ $rowIndex }}][material_rate]" class="form-control material_rate" value="{{ $detailMaterialRate }}" step="any"></td>
                        <!-- <td><input type="text" name="details[{{ $rowIndex }}][mat_amount]" class="form-control mat_amount" value="{{ intval($detailMatAmount) }}" step="1" readonly></td> -->
                        <td><input type="text" name="details[{{ $rowIndex }}][machine_capacity]" class="form-control machine_capacity" value="{{ $detailMachineCapacity }}"></td>
                        <td><input type="text" name="details[{{ $rowIndex }}][stroke]" class="form-control stroke" value="{{ $detailStroke }}"></td>
                        <td><input type="text" name="details[{{ $rowIndex }}][hatt]" class="form-control hatt" value="{{ $detailHatt }}"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove_row"><i class="fa fa-close"></i> Remove</button></td>
                    </tr>
                    @endforeach
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
                        <!-- <td><strong id="total_wages_amt">0</strong></td> -->
                        <td></td>
                        <!-- <td><strong id="total_mat_amt">0</strong></td> -->
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Update Consumption</button>
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
            let nextRowId = {{ $consumption->details->count() }};

            // Init Select2 on current selects
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

            $('#product_qty, #qty_per_carton').on('input', function () {
                const productQty = parseFloat($('#product_qty').val()) || 0;
                const qtyPerCarton = parseFloat($('#qty_per_carton').val()) || 0;
                if (qtyPerCarton > 0) {
                    $('#cartons_in_lot').val(Math.floor(productQty / qtyPerCarton));
                } else {
                    $('#cartons_in_lot').val('');
                }
                recalcCostPrice();
            });

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

            // Initialize existing rows: fill blank unit/mold/material_rate from selected raw material
            $('#entriesBody tr').each(function() {
                const $row = $(this);
                const $rawSelect = $row.find('.raw-material-select');
                if ($rawSelect.val()) {
                    const opt = $rawSelect.find(':selected');
                    $row.find('.raw-material-hidden').val($rawSelect.val());
                    // Only fill if blank (saved per-row value takes priority)
                    if (!$row.find('.unit').val()) {
                        $row.find('.unit').val(opt.data('unit-name') || '');
                    }
                    const moldId = opt.data('mold');
                    if (moldId) {
                        $row.find('.mold-select').val(moldId).trigger('change');
                        $row.find('.mold-hidden').val(moldId);
                    }
                    const purchaseRate = opt.data('purchase-rate');
                    if (purchaseRate !== undefined && purchaseRate !== '' && parseFloat(purchaseRate) > 0) {
                        $row.find('.material_rate').val(parseFloat(purchaseRate).toFixed(2));
                    } else {
                        const currentMatRate = $row.find('.material_rate').val();
                        if ((!currentMatRate || parseFloat(currentMatRate) <= 0) && $rawSelect.val()) {
                            const itemId = $rawSelect.val();
                            $.ajax({
                                url: '{{ url("/consumption2/material-rate") }}/' + itemId,
                                type: 'GET',
                                data: { current_v_no: '{{ $consumption->v_no ?? "" }}' },
                                success: function (res) {
                                    if (res && res.rate !== null && res.rate !== undefined && parseFloat(res.rate) > 0) {
                                        $row.find('.material_rate').val(parseFloat(res.rate).toFixed(2));
                                    } else {
                                        $row.find('.material_rate').val('');
                                    }
                                    recalcRow($row);
                                }
                            });
                        }
                    }
                }
                // Sync grams from mold weight for ALL rows (including master)
                const $moldSelect = $row.find('.mold-select');
                if ($moldSelect.val()) {
                    const weight = $moldSelect.find(':selected').data('weight');
                    if (weight) {
                        $row.find('.grams').val(weight);
                    }
                    // Wages Rate = mold form's Total value (with 2 decimals)
                    const moldWages = parseFloat($moldSelect.find(':selected').attr('data-wages')) || 0;
                    if (moldWages > 0) {
                        $row.find('.wages_rate').val(moldWages.toFixed(2));
                    }
                    // Recalc dependent fields without overwriting saved calc_qty
                    const moldQty = parseFloat($row.find('.mold_qty').val()) || 0;
                    const grams = parseFloat($row.find('.grams').val()) || 0;
                    const wagesRate = parseFloat($row.find('.wages_rate').val()) || 0;
                    $row.find('.wages_amount').val(Math.round(moldQty * wagesRate));
                    let calcQtyVal = parseFloat($row.find('.calc_qty').val()) || 0;
                    // When Mold's grams is 0, Calc Qty = Mold Qty (overwrite saved value)
                    if (moldQty > 0 && grams === 0) {
                        calcQtyVal = moldQty;
                        $row.find('.calc_qty').val(moldQty);
                    }
                    const materialRate = parseFloat($row.find('.material_rate').val()) || 0;
                    $row.find('.mat_amount').val((materialRate * calcQtyVal).toFixed(2));
                }
            });
            recalcCostPrice();

            function recalcRow(row) {
                const moldQty = parseFloat($(row).find('.mold_qty').val()) || 0;
                const grams = parseFloat($(row).find('.grams').val()) || 0;
                const wagesRate = parseFloat($(row).find('.wages_rate').val()) || 0;
                const materialRate = parseFloat($(row).find('.material_rate').val()) || 0;

                if (moldQty > 0 && grams > 0) {
                    $(row).find('.calc_qty').val(((moldQty * grams) / 25000).toFixed(2));
                } else if (moldQty > 0) {
                    $(row).find('.calc_qty').val(moldQty);
                }
                $(row).find('.wages_amount').val(Math.round(moldQty * wagesRate));

                const calcQty = parseFloat($(row).find('.calc_qty').val()) || 0;
                $(row).find('.mat_amount').val((materialRate * calcQty).toFixed(2));
                recalcCostPrice();
            }

            function recalcCostPrice() {
                let totalWagesAmt = 0;
                let totalMatAmt = 0;
                $('#entriesBody tr').each(function () {
                    totalWagesAmt += parseFloat($(this).find('.wages_amount').val()) || 0;
                    totalMatAmt += parseFloat($(this).find('.mat_amount').val()) || 0;
                });
                $('#total_wages_amt').text(totalWagesAmt.toFixed(2));
                $('#total_mat_amt').text(totalMatAmt.toFixed(2));
                const productQty = parseFloat($('#product_qty').val()) || 0;
                if (productQty > 0) {
                    $('#cost_price').val(((totalWagesAmt + totalMatAmt) / productQty).toFixed(2));
                }
            }

            // Raw material change → fill mold, unit, fetch previous material rate
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
                        recalcRow($row);
                    } else {
                        $.ajax({
                            url: '{{ url("/consumption2/material-rate") }}/' + itemId,
                            type: 'GET',
                            data: { current_v_no: '{{ $consumption->v_no ?? "" }}' },
                            success: function (res) {
                                if (res && res.rate !== null && res.rate !== undefined && parseFloat(res.rate) > 0) {
                                    $row.find('.material_rate').val(parseFloat(res.rate).toFixed(2));
                                } else {
                                    $row.find('.material_rate').val('');
                                }
                                recalcRow($row);
                            },
                            error: function () {
                                $row.find('.material_rate').val('');
                                recalcRow($row);
                            }
                        });
                    }
                } else {
                    $row.find('.material_rate').val('');
                    recalcRow($row);
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

            // mold_qty/grams → full recalc (calc_qty, wages_amount, mat_amount)
            $(document).on('input', '.mold_qty, .grams', function () {
                recalcRow($(this).closest('tr'));
            });
            // wages_rate → always show mold form's Total value, ignore user input
            $(document).on('input', '.wages_rate', function () {
                const $row = $(this).closest('tr');
                const moldWages = parseFloat($row.find('.mold-select :selected').attr('data-wages')) || 0;
                if (moldWages > 0) {
                    $row.find('.wages_rate').val(moldWages.toFixed(2));
                }
                const moldQty = parseFloat($row.find('.mold_qty').val()) || 0;
                const wagesRate = parseFloat($row.find('.wages_rate').val()) || 0;
                $row.find('.wages_amount').val(Math.round(moldQty * wagesRate));
                recalcCostPrice();
            });
            // calc_qty/material_rate → only recalc mat_amount (don't overwrite calc_qty)
            $(document).on('input', '.calc_qty, .material_rate', function () {
                const $row = $(this).closest('tr');
                const calcQty = parseFloat($row.find('.calc_qty').val()) || 0;
                const materialRate = parseFloat($row.find('.material_rate').val()) || 0;
                $row.find('.mat_amount').val((materialRate * calcQty).toFixed(2));
                recalcCostPrice();
            });

            // Remove row
            $(document).on('click', '.remove_row', function () {
                $(this).closest('tr').remove();
                recalcCostPrice();
            });

            // Add Row
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
                    '<td><input type="number" name="details[' + nextRowId + '][wages_amount]" class="form-control wages_amount" step="1" readonly></td>',
                    '<td><input type="number" name="details[' + nextRowId + '][material_rate]" class="form-control material_rate" step="any"></td>',
                    '<td><input type="number" name="details[' + nextRowId + '][mat_amount]" class="form-control mat_amount" step="any" readonly></td>',
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