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
                        <li class="breadcrumb-item active">Edit Delivery Challan</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Delivery Challan - {{ $dc->v_no }}</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('sale_order_dc.update', $dc->id) }}" method="POST" id="dcForm">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-3">
                <label for="customer_id" class="form-label fw-bold">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control select2" required>
                    <option value="">-- Select Customer --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->acc_id }}" 
                                data-transport="{{ $customer->company_title ?? '' }}" 
                                data-ref-no="{{ $customer->ntn ?? '' }}" 
                                {{ $customer->acc_id == $dc->customer_id ? 'selected' : '' }}>
                            {{ $customer->name }} @if($customer->urdu_title) - {{ $customer->urdu_title }} @endif @if($customer->ntn) [Ref: {{ $customer->ntn }}] @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="v_date" class="form-label fw-bold">Date</label>
                <input type="date" name="v_date" id="v_date" class="form-control" required value="{{ $dc->v_date }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Prepared By</label>
                <input type="text" class="form-control" value="{{ $dc->prepared_by }}" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Sale Order</label>
                <input type="text" class="form-control" value="{{ $sorVno ?? 'N/A' }}" readonly>
                <input type="hidden" name="sor_vno" value="{{ $sorVno }}">
            </div>
            <div class="col-md-2">
                <label for="global_lot_no" class="form-label fw-bold">Lot no</label>
                <input type="text" name="global_lot_no" id="global_lot_no" class="form-control" value="1" placeholder="Lot no">
            </div>
        </div>

        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-3">
                <label for="ref_no" class="form-label">Party Reference</label>
                <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="Party Reference" value="{{ old('ref_no', $dc->ref_no ?? '') }}">
            </div>
            <div class="col-md-3">
                <label for="transport_company_id" class="form-label">Transport Company</label>
                <select name="transport_company_id" id="transport_company_id" class="form-control select2">
                    <option value="">-- Select Transport Company --</option>
                    @foreach ($transportCompanies as $tc)
                        <option value="{{ $tc->id }}" {{ $tc->id == $dc->transport_company_id ? 'selected' : '' }}>{{ $tc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="bilty_no" class="form-label">Bilty No</label>
                <input type="text" name="bilty_no" id="bilty_no" class="form-control" value="{{ $dc->bilty_no }}">
            </div>
            <div class="col-md-2">
                <label for="ref_number" class="form-label">Reference Number</label>
                <input type="text" name="ref_number" id="ref_number" class="form-control" placeholder="Reference Number" value="{{ old('ref_number', $dc->customer->ntn ?? '') }}" readonly>
                <input type="hidden" name="ref_name_id" id="ref_name_id" value="{{ $dc->ref_name_id }}">
            </div>
            <div class="col-md-2">
                <label for="qty_per_carton" class="form-label">Qty Per Carton</label>
                <input type="number" name="qty_per_carton" id="qty_per_carton" class="form-control" value="{{ $dc->qty_per_carton }}" step="any" placeholder="e.g. 500">
            </div>
        </div>

        <div id="itemsContainer">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th style="width:90px">Lot no</th>
                        <th style="width:90px">Cartons</th>
                        <th style="width:90px">Total</th>
                        <th style="width:90px">Remaining<br><span class="small text-muted">(ctns)</span></th>
                        <th style="width:100px">Lot Stock</th>
                    </tr>
                </thead>
                <tbody id="itemsBody"></tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td></td>
                        <td><strong id="totalCartonsEdit">0</strong></td>
                        <td><strong id="totalQtyEdit">0</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <p class="text-muted small">&#8593; Enter cartons per product. Auto-splits by cartons_in_lot.</p>
        </div>

        @php
            $editItems = [];
            $details = $dc->details()->orderBy('sort_order')->get() ?? collect();
            if ($details->isEmpty()) {
                $details = \App\Models\DeliveryChallanDetail::where('v_no', $dc->v_no)->where('cid', Auth::user()->cid)->orderBy('sort_order')->get();
            }
            $dcItemIds = $details->pluck('item_id')->toArray();
            foreach ($details as $detail) {
                $item = $detail->item;
                $productName = $item ? $item->product_name : 'N/A';
                $sod = $sorDetails[$detail->item_id] ?? null;
                $currentQty = $sod ? $sod->qty : 0;
                $originalQty = $sod ? $sod->original_qty : $currentQty;
                $availableForEdit = $currentQty + $detail->load_qty;
                $editItems[] = [
                    'item_id' => $detail->item_id,
                    'product_name' => $productName,
                    'original_qty' => $originalQty,
                    'available_qty' => $availableForEdit,
                    'so_remaining' => $currentQty > 0 ? $currentQty : $availableForEdit,
                    'load_qty' => $detail->load_qty,
                    'lot_no' => $detail->lot_no,
                ];
            }
            foreach ($sorDetails as $itemId => $sod) {
                if (!in_array($itemId, $dcItemIds) && $sod->qty > 0) {
                    $editItems[] = [
                        'item_id' => $itemId,
                        'product_name' => $sod->item->product_name ?? 'N/A',
                        'original_qty' => $sod->original_qty,
                        'available_qty' => $sod->qty,
                        'so_remaining' => $sod->qty,
                        'load_qty' => 0,
                        'lot_no' => null,
                    ];
                }
            }
        @endphp
        <script>var editItemsData = @json($editItems);</script>

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('sale_order_dc.index') }}" class="btn btn-secondary">Cancel</a>
            <div>
                @if ($sorId)
                    <a href="{{ route('sale_order.edit', $sorId) }}" target="_blank" class="btn btn-warning">Edit Sale Order</a>
                @endif
                <button type="button" class="btn btn-info" onclick="window.print()">Print</button>
                <button type="submit" class="btn btn-success">Update Delivery Challan</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single { height: calc(1.5em + 0.9rem + 2px); border: 1px solid #dee2e6; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + 0.9rem); padding-left: 0.9rem; }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
$(document).ready(function() {
    var editItems = [];

    try { $('.select2').select2({ placeholder: "-- Select --", width: '100%' }); } catch(e) {}

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

    // Default Transport Company to the selected customer's transport company (by name)
    $('#customer_id').on('change', function() {
        const transportName = $('#customer_id option:selected').data('transport') || '';
        $('#transport_company_id').val('');
        if (transportName) {
            $('#transport_company_id option').each(function () {
                if ($(this).text().trim().toLowerCase() === String(transportName).trim().toLowerCase()) {
                    $('#transport_company_id').val($(this).val());
                    return false;
                }
            });
        }
        $('#transport_company_id').trigger('change');

        const refNo = $('#customer_id option:selected').data('ref-no') || '';
        $('#ref_number').val(refNo);
    });

    function initEditItems() {
        var map = new Map();
        editItemsData.forEach(function(d) {
            if (!map.has(d.item_id)) {
                map.set(d.item_id, {
                    item_id: d.item_id,
                    product_name: d.product_name,
                    available_qty: parseFloat(d.available_qty) || 0,
                    so_remaining: parseFloat(d.so_remaining) ?? (parseFloat(d.available_qty) || 0),
                    cartons_input: 0,
                    next_lot_no: 1,
                    last_lot_no: null,
                    remaining_capacity: 0,
                    cartons_in_lot: 50,
                    has_consumption: false,
                    saved_lot_nos: []
                });
            }
            map.get(d.item_id).cartons_input += parseFloat(d.load_qty) || 0;
            if (d.lot_no) {
                map.get(d.item_id).saved_lot_nos.push(parseInt(d.lot_no));
            }
        });
        editItems = Array.from(map.values());
    }

    function renderEdit() {
        var $tbody = $('#itemsBody');
        $tbody.empty();
        var idx = 0;

        var grandTotalCartons = 0;
        var grandTotalQty = 0;
        var qpc = parseFloat($('#qty_per_carton').val()) || 0;

        editItems.forEach(function(item) {
            var itemIdx = editItems.indexOf(item);
            var cartons = parseInt(item.cartons_input) || 0;
            grandTotalCartons += cartons;
            var maxCtnPerLot = item.cartons_in_lot || 50;
            var lots = [];
            var remaining = cartons;

            var startLotNo = item.saved_lot_nos.length > 0 ? Math.min.apply(null, item.saved_lot_nos) : item.next_lot_no;

            if (cartons > 0 && item.has_consumption) {
                var lastLotNo = item.last_lot_no;
                var remCap = item.remaining_capacity || 0;
                if (startLotNo > 1 && lastLotNo && remCap > 0) {
                    var fillCtn = Math.min(remaining, remCap);
                    lots.push({ load_qty: fillCtn, lot_no: lastLotNo, cartons: fillCtn });
                    remaining -= fillCtn;
                }
                var lotNo = startLotNo;
                while (remaining > 0) {
                    var ctn = Math.min(remaining, maxCtnPerLot);
                    lots.push({ load_qty: ctn, lot_no: lotNo, cartons: ctn });
                    remaining -= ctn;
                    lotNo++;
                }
            } else if (cartons > 0) {
                lots.push({ load_qty: cartons, lot_no: '', cartons: cartons });
            } else {
                lots.push({ load_qty: cartons, lot_no: startLotNo, cartons: cartons });
            }

            var displayCtns = cartons > 0 ? cartons : item.so_remaining;
            var displayRemaining = (item.so_remaining && item.so_remaining > 0) ? item.so_remaining : displayCtns;

            var lotRangeText = '';
            if (lots.length > 0) {
                var validLotNos = lots.map(function(l){ return l.lot_no; }).filter(Boolean);
                if (validLotNos.length === 1) {
                    lotRangeText = validLotNos[0];
                } else if (validLotNos.length > 1) {
                    lotRangeText = validLotNos[0] + ' - ' + validLotNos[validLotNos.length - 1];
                }
            }

            var rowTotalQty = (displayCtns && qpc > 0) ? (displayCtns * qpc) : displayCtns;
            grandTotalQty += (qpc > 0 ? (displayCtns * qpc) : displayCtns);

            var sr = '<tr class="lot-summary" data-item-idx="' + itemIdx + '">';
            sr += '<td>';
            sr += item.product_name;

            lots.forEach(function(lot) {
                sr += '<input type="hidden" name="items[' + idx + '][item_id]" value="' + item.item_id + '">';
                sr += '<input type="hidden" name="items[' + idx + '][available_qty]" value="' + item.available_qty + '">';
                sr += '<input type="hidden" name="items[' + idx + '][load_qty]" value="' + lot.load_qty + '">';
                if (lot.lot_no) {
                    sr += '<input type="hidden" name="items[' + idx + '][lot_no]" value="' + lot.lot_no + '">';
                }
                idx++;
            });

            sr += '</td>';
            sr += '<td class="text-center">' + lotRangeText + '</td>';
            sr += '<td><input type="number" class="form-control edit-cartons-input" data-item-idx="' + itemIdx + '" value="' + displayCtns + '" min="0" step="1"></td>';
            sr += '<td class="text-center fw-bold">' + rowTotalQty + '</td>';
            sr += '<td class="text-center">' + displayRemaining + '</td>';
            sr += '<td><span class="text-muted">' + lots.length + ' lot(s)</span></td>';
            sr += '</tr>';
            $tbody.append(sr);
        });

        // Update total cartons and qty
        $('#totalCartonsEdit').text(grandTotalCartons);
        $('#totalQtyEdit').text(grandTotalQty);

        $('.edit-cartons-input').off('change').on('change', function() {
            var itemIdx = parseInt($(this).data('item-idx'));
            if (isNaN(itemIdx) || !editItems[itemIdx]) return;
            editItems[itemIdx].cartons_input = parseInt($(this).val()) || 0;
            renderEdit();
        });
    }

    function initAndRender() {
        initEditItems();
        var firstId = editItems.length > 0 ? editItems[0].item_id : null;
        if (!firstId) { renderEdit(); return; }
        if (typeof axios === 'undefined') { renderEdit(); return; }
        var uniqueIds = [...new Set(editItems.map(function(i) { return i.item_id; }))];
        var qpcPromise = !$('#qty_per_carton').val()
            ? axios.get('/get-consumption-qpc/' + firstId) : Promise.resolve({ data: {} });
        Promise.all([
            qpcPromise,
            ...uniqueIds.map(function(id) { return axios.get('/get-next-lot-no/' + id); })
        ]).then(function(results) {
            var qpcRes = results[0];
            if (qpcRes.data && qpcRes.data.qty_per_carton) {
                $('#qty_per_carton').val(qpcRes.data.qty_per_carton);
            }
            uniqueIds.forEach(function(id, i) {
                var lotRes = results[i + 1];
                editItems.forEach(function(ei) {
                    if (ei.item_id === id) {
                        ei.next_lot_no = lotRes.data.next_lot_no;
                        ei.last_lot_no = lotRes.data.last_lot_no;
                        ei.remaining_capacity = lotRes.data.remaining_capacity || 0;
                        ei.cartons_in_lot = lotRes.data.cartons_in_lot || 50;
                        ei.has_consumption = lotRes.data.has_consumption;
                    }
                });
            });
            renderEdit();
        }).catch(function() {
            renderEdit();
        });
    }

    initAndRender();

    $(document).on('input', '#qty_per_carton', function() { renderEdit(); });
});
</script>
@endsection
