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
                        <li class="breadcrumb-item active">Create Delivery Challan</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Delivery Challan</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('sale_order_dc.store') }}" method="POST" id="dcForm">
        @csrf

        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-3">
                <label for="customer_id" class="form-label fw-bold">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control select2" required>
                    <option value="">-- Select Customer --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->acc_id }}" 
                                data-transport="{{ $customer->company_title ?? '' }}" 
                                data-ref-no="{{ $customer->ntn ?? '' }}">
                            {{ $customer->name }} @if($customer->urdu_title) - {{ $customer->urdu_title }} @endif @if($customer->ntn) [Ref: {{ $customer->ntn }}] @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="v_date" class="form-label fw-bold">Date</label>
                <input type="date" name="v_date" id="v_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Prepared By</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
            </div>
            <div class="col-md-2">
                <label for="sor_vno" class="form-label fw-bold">Sale Order</label>
                <select name="sor_vno" id="sor_vno" class="form-control select2" required>
                    <option value="">-- Select SOR --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="global_lot_no" class="form-label fw-bold">Lot no</label>
                <input type="text" name="global_lot_no" id="global_lot_no" class="form-control" value="1" placeholder="Lot no">
            </div>
        </div>

        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-3">
                <label for="ref_no" class="form-label">Party Reference</label>
                <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="Party Reference" value="{{ old('ref_no', $preselectedPartyRef ?? '') }}">
            </div>
            <div class="col-md-3">
                <label for="transport_company_id" class="form-label">Transport Company</label>
                <select name="transport_company_id" id="transport_company_id" class="form-control select2">
                    <option value="">-- Select Transport Company --</option>
                    @foreach ($transportCompanies as $tc)
                        <option value="{{ $tc->id }}">{{ $tc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="bilty_no" class="form-label">Bilty No</label>
                <input type="text" name="bilty_no" id="bilty_no" class="form-control" placeholder="Bilty No">
            </div>
            <div class="col-md-2">
                <label for="ref_number" class="form-label">Reference Number</label>
                <input type="text" name="ref_number" id="ref_number" class="form-control" placeholder="Reference Number" value="{{ old('ref_number', $preselectedRefNumber ?? '') }}" readonly>
                <input type="hidden" name="ref_name_id" id="ref_name_id" value="{{ old('ref_name_id', $preselectedRefNameId ?? '') }}">
            </div>
            <div class="col-md-2">
                <label for="qty_per_carton" class="form-label">Qty Per Carton</label>
                <input type="number" name="qty_per_carton" id="qty_per_carton" class="form-control" step="any" placeholder="e.g. 500">
            </div>
        </div>

        <div id="itemsContainer">
            <p class="text-muted">Select a customer and sale order to load items.</p>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <div></div>
            <div>
                <button type="button" class="btn btn-info" onclick="window.print()">Print</button>
                <button type="submit" class="btn btn-success" id="saveBtn" disabled>Save Delivery Challan</button>
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
    var preselCid = '{{ $preselectCustomerId ?? '' }}';
    var preselSor = '{{ $preselectSorVno ?? '' }}';

    $('.select2').select2({ placeholder: "-- Select --", width: '100%' });

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

    let sorItems = [];
    let qpc = 0;      
    let cil = 50;    

    $('#customer_id').on('change', function() {
        const customerId = $(this).val();
        const $sorSelect = $('#sor_vno');
        $sorSelect.empty().append('<option value="">-- Select SOR --</option>');
        $('#itemsContainer').html('<p class="text-muted">Select a customer and sale order to load items.</p>');
        $('#saveBtn').prop('disabled', true);
        sorItems = [];

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

        if (customerId) {
            axios.get('/get-sors-by-customer/' + customerId)
                .then(function(res) {
                    res.data.forEach(function(sor) {
                        $sorSelect.append('<option value="' + sor.v_no + '">' + sor.v_no + '</option>');
                    });
                    if (preselSor) {
                        $sorSelect.val(preselSor).trigger('change');
                        preselSor = '';
                    }
                });
        }
    });

    function render() {
        qpc = parseFloat($('#qty_per_carton').val()) || 0;
        var $tbody = $('#itemsBody');
        $tbody.empty();
        if (sorItems.length === 0) { $('#totalCartons').text('0'); return; }

        var idx = 0;
        sorItems.forEach(function(item) {
            var itemIdx = sorItems.indexOf(item);
            var cartons = parseInt(item.cartons_input) || 0;
            var maxCtnPerLot = item.cartons_in_lot || cil || 50;
            var lots = [];
            var remaining = cartons;

            if (cartons > 0 && item.has_consumption) {
                var lastLotNo = item.last_lot_no;
                var remCap = item.remaining_capacity || 0;
                if (lastLotNo && remCap > 0) {
                    var fillCtn = Math.min(remaining, remCap);
                    lots.push({
                        load_qty: fillCtn,
                        lot_no: lastLotNo,
                        cartons: fillCtn
                    });
                    remaining -= fillCtn;
                }
                var lotNo = item.next_lot_no;
                while (remaining > 0) {
                    var ctn = Math.min(remaining, maxCtnPerLot);
                    lots.push({
                        load_qty: ctn,
                        lot_no: lotNo,
                        cartons: ctn
                    });
                    remaining -= ctn;
                    lotNo++;
                }
            } else if (cartons > 0) {
                lots.push({
                    load_qty: cartons,
                    lot_no: '',
                    cartons: cartons
                });
            } else {
                lots.push({
                    load_qty: cartons,
                    lot_no: item.next_lot_no,
                    cartons: cartons
                });
            }

            var availCtn = item.available_qty;
            var totalCtns = lots.reduce(function(s, l) { return s + l.cartons; }, 0);
            var startHidden = lots.length > 3;

            var lotRangeText = '';
            if (lots.length > 0) {
                var validLotNos = lots.map(function(l){ return l.lot_no; }).filter(Boolean);
                if (validLotNos.length === 1) {
                    lotRangeText = validLotNos[0];
                } else if (validLotNos.length > 1) {
                    lotRangeText = validLotNos[0] + ' - ' + validLotNos[validLotNos.length - 1];
                }
            }

            var qpc = parseFloat($('#qty_per_carton').val()) || 0;
            var rowTotalQty = (cartons && qpc > 0) ? (cartons * qpc) : cartons;

            var sr = '<tr class="lot-summary" data-item-idx="' + itemIdx + '">';
            sr += '<td>';
            sr += '<button type="button" class="btn btn-sm text-danger border-0 p-0 me-1 remove-item" data-item-idx="' + itemIdx + '" title="Remove">&times;</button>';
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
            sr += '<td><input type="number" class="form-control cartons-input" data-item-idx="' + itemIdx + '" value="' + cartons + '" min="0" step="1"></td>';
            sr += '<td class="text-center fw-bold">' + rowTotalQty + '</td>';
            sr += '<td class="text-center">' + availCtn + '</td>';
            sr += '<td><span class="lot-range text-muted">' + lots.length + ' lot(s)</span></td>';
            sr += '</tr>';
            $tbody.append(sr);
        });

        // Update total cartons
        var totalCtns = 0;
        var totalQty = 0;
        var qpc = parseFloat($('#qty_per_carton').val()) || 0;
        sorItems.forEach(function(it) {
            var c = parseInt(it.cartons_input) || 0;
            totalCtns += c;
            totalQty += (qpc > 0 ? (c * qpc) : c);
        });
        $('#totalCartons').text(totalCtns);
        $('#totalQty').text(totalQty);

        // Remove product
        $('.remove-item').off('click').on('click', function() {
            var ix = parseInt($(this).data('item-idx'));
            if (confirm('Remove this product?')) {
                sorItems.splice(ix, 1);
                render();
            }
        });

        // Cartons input change on each summary row
        $('.cartons-input').off('change').on('change', function() {
            var itemIdx = parseInt($(this).data('item-idx'));
            if (isNaN(itemIdx) || !sorItems[itemIdx]) return;
            sorItems[itemIdx].cartons_input = parseInt($(this).val()) || 0;
            render();
        });
    }

    function lotExpandIcon(collapsed) {
        return collapsed ? '&#9654;' : '&#9660;';
    }

    $('#sor_vno').on('change', function() {
        var sorVno = $(this).val();
        var $container = $('#itemsContainer');
        $container.empty();
        $('#saveBtn').prop('disabled', true);
        sorItems = [];
        $('#qty_per_carton').prop('readonly', false).val('');

        if (sorVno) {
            axios.get('/get-sor-items/' + sorVno)
                .then(function(res) {
                    if (res.data.length === 0) {
                        $container.html('<p class="text-muted">No items with remaining qty.</p>');
                        return;
                    }
                    sorItems = res.data.map(function(item) {
                        return {
                            item_id: item.item_id,
                            product_name: item.item?.product_name ?? 'N/A',
                            original_qty: item.original_qty ?? item.qty,
                            available_qty: item.qty,
                            cartons_input: item.qty,
                            next_lot_no: 1,
                            last_lot_no: null,
                            remaining_capacity: 0,
                            cartons_in_lot: 50,
                            has_consumption: false
                        };
                    });

                    // Build table shell
                    var html = '<div class="table-responsive">';
                    html += '<table class="table table-bordered table-sm">';
                    html += '<thead class="table-light"><tr>';
                    html += '<th>Product</th>';
                    html += '<th style="width:90px">Lot no</th>';
                    html += '<th style="width:90px">Cartons</th>';
                    html += '<th style="width:90px">Total</th>';
                    html += '<th style="width:90px">Remaining<br><span class="small text-muted">(ctns)</span></th>';
                    html += '<th style="width:100px">Lot Stock</th>';
                    html += '</tr></thead><tbody id="itemsBody"></tbody>';
                    html += '<tfoot><tr><td><strong>Total</strong></td><td></td><td><strong id="totalCartons">0</strong></td><td><strong id="totalQty">0</strong></td><td></td><td></td></tr></tfoot>';
                    html += '</table></div>';
                    html += '<p class="text-muted small">&#8593; Enter cartons per product. Auto-splits by cartons_in_lot.</p>';
                    $container.html(html);

                    var firstId = sorItems[0].item_id;
                    var uniqueIds = [...new Set(sorItems.map(function(i) { return i.item_id; }))];

                    // Fetch qpc for first product + lot info for each product
                    Promise.all([
                        axios.get('/get-consumption-qpc/' + firstId),
                        ...uniqueIds.map(function(id) { return axios.get('/get-next-lot-no/' + id); })
                    ]).then(function(results) {
                        var qpcRes = results[0];
                        if (qpcRes.data.qty_per_carton) {
                            $('#qty_per_carton').val(qpcRes.data.qty_per_carton);
                        }
                        // Assign per-product lot info from getNextLotNo
                        uniqueIds.forEach(function(id, i) {
                            var lotRes = results[i + 1];
                            sorItems.forEach(function(si) {
                                if (si.item_id === id) {
                                    si.next_lot_no = lotRes.data.next_lot_no;
                                    si.last_lot_no = lotRes.data.last_lot_no;
                                    si.remaining_capacity = lotRes.data.remaining_capacity || 0;
                                    si.cartons_in_lot = lotRes.data.cartons_in_lot || 50;
                                    si.has_consumption = lotRes.data.has_consumption;
                                }
                            });
                        });
                        render();
                        $('#saveBtn').prop('disabled', false);
                    }).catch(function() {
                        render();
                        $('#saveBtn').prop('disabled', false);
                    });
                })
                .catch(function() {
                    $container.html('<p class="text-danger">Error loading items.</p>');
                });

            axios.get('/get-sor-master/' + sorVno).then(function(res) {
                if (res.data) {
                    if (res.data.ref_name_id) {
                        $('#ref_name_id').val(res.data.ref_name_id).trigger('change');
                    }
                    if (res.data.party_ref) {
                        $('#ref_no').val(res.data.party_ref);
                    }
                }
            });
        }
    });

    $(document).on('input', '#qty_per_carton', function() {
        render();
    });

    if (preselCid) {
        $('#customer_id').val(preselCid).trigger('change');
    }
});
</script>
@endsection
