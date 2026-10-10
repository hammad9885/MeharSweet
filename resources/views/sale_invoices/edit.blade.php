@extends('layouts.app')

@section('content')
    <style>
        .box-history {
            width: 100%;
            min-height: 260px;
            max-height: 350px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 16px;
            margin-top: 25px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.2s ease;
        }

        .box-history:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        .box-history table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .box-history table th {
            background: #f7fbfb;
            font-weight: 600;
            padding: 8px;
            border-bottom: 2px solid #dee2e6;
            color: #58a3b3;
        }

        .box-history table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .box-history table tbody tr:nth-child(even) {
            background: #f6f6f6;
        }

        .box-history table tbody tr:hover {
            background: #e2f0ff;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Sale</a></li>
                            <li class="breadcrumb-item active">Edit Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Sale Invoice</h4>
                </div>
            </div>
        </div>

        <form action="{{ route('sale_invoices.update', $purchaseMaster->v_no) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @php
                $initTotal = 0;
                foreach ($invoiceItems as $it) {
                    $prodQpc = $productQpc[$it->item_id] ?? 1;
                    $qpcVal = $prodQpc > 0 ? $prodQpc : 1;
                    $cartonVal = $dcCartonData[$it->item_id] ?? ($qpcVal > 1 ? round($it->qty / $qpcVal) : $it->qty);
                    if ($cartonVal == 0 && $it->qty > 0) {
                        $cartonVal = $qpcVal > 1 ? round($it->qty / $qpcVal) : $it->qty;
                    }
                    $totalQty = $it->qty;
                    $initTotal += ($totalQty * $it->rate);
                }
                $initDiscount = (float)($purchaseMaster->discount ?? 0);
                $initDiscPct = 0;
                if ($initTotal > 0 && $initDiscount > 0) {
                    $initDiscPct = round(($initDiscount / $initTotal) * 100, 2);
                } else {
                    $customerObj = $suppliers->firstWhere('acc_id', $purchaseMaster->customer_id);
                    $initDiscPct = $customerObj ? (float)($customerObj->disc ?? 0) : 0;
                }
            @endphp

            <div class="row mb-3 align-items-end">
                
                <div class="col-md-2">
                    <label for="v_date" class="form-label fw-bold">Date</label>
                    <input type="date" name="v_date" id="v_date" class="form-control" required
                        value="{{ $purchaseMaster->v_date }}">
                </div>
                
                <div class="col-md-4 mb-1">
                    <label for="supplier_id" class="form-label fw-bold">Customer</label>
                    <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                        <option value="">-- Select Customer --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->acc_id }}" data-disc="{{ $supplier->disc ?? 0 }}" data-transport-id="{{ $custTransportMap[$supplier->acc_id] ?? '' }}" data-bilty="{{ $custBiltyMap[$supplier->acc_id] ?? '' }}" {{ $supplier->acc_id == $purchaseMaster->customer_id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-3">
                    <label for="gatepass_no" class="form-label">Gate Pass #</label>
                    <input type="text" name="gatepass_no" id="gatepass_no" class="form-control"
                        value="{{ $purchaseMaster->gate_pass_no }}">
                </div>

                <div class="col-md-2"></div>
            </div>

            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-3">
                    <label for="dc_vno" class="form-label fw-bold">From DC</label>
                    <select name="dc_vno" id="dc_vno" class="form-control select2">
                        <option value="">-- Select DC --</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Prepared By</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                </div>

                <div class="col-md-2">
                    <label for="ref_name_id" class="form-label">Reference Name</label>
                    <select name="ref_name_id" id="ref_name_id" class="form-control select2">
                        <option value="">-- Select Reference --</option>
                        @foreach ($referenceNames as $rn)
                            <option value="{{ $rn->id }}" {{ $purchaseMaster->ref_name_id == $rn->id ? 'selected' : '' }}>{{ $rn->name }}@if($rn->name_urdu) - {{ $rn->name_urdu }}@endif</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="bilty_no" class="form-label">Bilty No</label>
                    <input type="text" name="bilty_no" id="bilty_no" class="form-control" value="{{ old('bilty_no', $purchaseMaster->bilty_no) }}">
                </div>

                <div class="col-md-3">
                    <label for="transport_company_id" class="form-label">Transport Company</label>
                    <select name="transport_company_id" id="transport_company_id" class="form-control select2">
                        <option value="">-- Select Transport Company --</option>
                        @foreach ($transportCompanies as $tc)
                            <option value="{{ $tc->id }}" {{ (old('transport_company_id', $purchaseMaster->transport_company_id) == $tc->id) ? 'selected' : '' }}>
                                {{ $tc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            
                <div id="saleTable">
                    @foreach ($invoiceItems as $index => $item)
                        @php
                            $prodQpc = $productQpc[$item->item_id] ?? 1;
                            $qpcVal = $prodQpc > 0 ? $prodQpc : 1;
                            $cartonVal = $dcCartonData[$item->item_id] ?? ($qpcVal > 1 ? round($item->qty / $qpcVal) : $item->qty);
                            if ($cartonVal == 0 && $item->qty > 0) {
                                $cartonVal = $qpcVal > 1 ? round($item->qty / $qpcVal) : $item->qty;
                            }
                            $totalQty = $item->qty;
                        @endphp
                        @if ($cartonVal != 0)
                        <div class="sale-row border rounded px-3 py-1 mb-1 bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label">Product</label>
                                    <select name="items[{{ $index }}][item_id]" class="form-control item-select select2">
                                        <option value="" data-rate="0" data-qpc="0">-- Select --</option>
                                        @foreach ($items as $i)
                                            <option value="{{ $i->id }}"
                                                data-rate="{{ $i->cost_price ?? $i->sale_rate }}"
                                                data-qpc="{{ $i->consumption_qpc ?? 0 }}"
                                                {{ $i->id == $item->item_id ? 'selected' : '' }}>
                                                {{ $i->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Ctn</label>
                                    <input type="number" class="form-control cartons" value="{{ $cartonVal }}" min="0">
                                    <input type="hidden" name="items[{{ $index }}][qty]" class="qty-hidden" value="{{ $totalQty }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">QPC</label>
                                    <input type="number" class="form-control qpc" value="{{ $qpcVal }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">T.Qty</label>
                                    <input type="number" class="form-control total-qty" value="{{ $totalQty }}" readonly>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Rate</label>
                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control rate"
                                        value="{{ $item->rate }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Amount</label>
                                    <input type="text" class="form-control row-amount" readonly
                                        value="{{ (float)($totalQty * $item->rate) }}">
                                </div>
                                <input type="hidden" name="items[{{ $index }}][tax_rate]" value="{{ $item->tax_rate ?? 0 }}">
                                <input type="hidden" name="items[{{ $index }}][discount]" value="{{ $item->discount ?? 0 }}">
                                <input type="hidden" name="items[{{ $index }}][discount_perc]" value="{{ $item->discount_perc ?? 0 }}">
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-danger btn-sm mt-4 remove-row">X</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    {{-- Total Row --}}
                    <div class="sale-row border rounded px-3 py-1 mb-1 bg-light total-row">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3"><strong>Total</strong></div>
                            <div class="col-md-1"><strong id="total_cartons">0</strong></div>
                            <div class="col-md-1"></div>
                            <div class="col-md-1"></div>
                            <div class="col-md-1"></div>
                            <div class="col-md-1"><strong id="total_amount">0</strong></div>
                            <div class="col-md-1 text-center"></div>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center justify-content-between mt-4">
                    <div class="col-md-4 mb-3">
                        <label for="uploadFile" class="form-label">Upload File</label>
                        <input type="file" id="uploadFile" class="form-control" name="file">
                        <div id="filePreviewContainer" class="mt-2">
                            @if (!empty($maxVoucherNo->file_id))
                                <img id="imagePreview" src="{{ asset('storage/' . $maxVoucherNo->file_id) }}"
                                    style="max-width:150px; max-height:150px;">
                                <br>
                                <span id="fileNamePreview">{{ $maxVoucherNo->file_id }}</span>
                                <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
                                <input type="hidden" name="old_file" value="{{ $maxVoucherNo->file_id }}">
                            @else
                                <img id="imagePreview" src="" style="max-width:150px; max-height:150px; display:none;">
                                <span id="fileNamePreview"></span>
                                <button type="button" id="removeFile" class="btn btn-sm btn-danger" style="display:none;">X</button>
                                <input type="hidden" name="old_file" value="">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row mb-2 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Total</label></div>
                            <div class="col-8"><input type="text" id="row_total" class="form-control" readonly></div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Discount %</label></div>
                            <div class="col-8">
                                <div class="input-group">
                                    <input type="number" step="any" id="discount_percentage" name="discount_percentage" class="form-control" placeholder="0" value="{{ $initDiscPct > 0 ? $initDiscPct : '0' }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Discount</label></div>
                            <div class="col-8"><input type="number" step="any" id="grand_discount" name="grand_discount" class="form-control"
                                value="{{ $purchaseMaster->discount }}"></div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Freight</label></div>
                            <div class="col-8"><input type="number" step="any" id="freight" name="freight" class="form-control" value="{{ (float)($purchaseMaster->freight ?? 0) }}"></div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Total Amt</label></div>
                            <div class="col-8"><input type="text" id="total_plus_discount" class="form-control" readonly></div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Pre . Bal</label></div>
                            <div class="col-8"><input type="number" id="pre_bal" name="pre_bal" class="form-control" value="{{ $preBal ?? 0 }}"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4"><label class="form-label fw-bold mb-0">Net Amount</label></div>
                            <div class="col-8"><input type="text" id="grand_total" name="grand_total" class="form-control"
                                value="{{ $purchaseMaster->grand_total }}" readonly></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-5">Update Invoice</button>
                </div>

            </form>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="box-history"></div>
                </div>
            </div>

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
                window.onerror = function(msg, url, line) {
                console.error('[GLOBAL] JS Error:', msg, 'at', url, 'line', line);
            };
            $(document).ready(function () {

                console.log('[DEBUG] Document ready fired on edit page');

                var currentVno = '{{ $purchaseMaster->v_no }}';
                var savedDcVno = '{{ $purchaseMaster->dc_vno ?? '' }}';
                console.log('[DEBUG] currentVno =', JSON.stringify(currentVno), 'savedDcVno =', JSON.stringify(savedDcVno));

                $('.select2').each(function () {
                    $(this).data('no-ajax', true);
                });
                $('.select2').select2({ placeholder: '-- Select --', width: '100%' });

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

                // ====== HELPERS ======
                function recalcRow(row) {
                    const cartons  = parseFloat($(row).find('.cartons').val()) || 0;
                    const qpc      = parseFloat($(row).find('.qpc').val())     || 1;
                    const rate     = parseFloat($(row).find('.rate').val())    || 0;
                    const totalQty = cartons * qpc;
                    const amount   = totalQty * rate;
                    $(row).find('.total-qty').val(totalQty);
                    $(row).find('.qty-hidden').val(totalQty);
                    $(row).find('.row-amount').val(parseFloat(amount.toFixed(2)));
                    recalcGrandTotal();
                }

                let customerDiscPct = {{ $initDiscPct }}; // customer default discount (%), converted to amount when set
                let discInitDone = false; // skip conversion on the initial auto-load (preserve saved discount)

                function recalcGrandTotal() {
                    let total = 0;
                    let totalCartons = 0;
                    let totalQty = 0;
                    $('.sale-row').each(function () {
                        total += parseFloat($(this).find('.row-amount').val()) || 0;
                        totalCartons += parseFloat($(this).find('.cartons').val()) || 0;
                        totalQty += parseFloat($(this).find('.total-qty').val()) || 0;
                    });
                    $('#total_cartons').text(totalCartons);
                    $('#total_qty').text(totalQty);
                    $('#total_amount').text(total.toFixed(2));
                    $('#row_total').val(total.toFixed(2));

                    let discAmt = parseFloat($('#grand_discount').val()) || 0;
                    if (discInitDone && customerDiscPct > 0 && total > 0) {
                        discAmt = total * customerDiscPct / 100;
                        $('#grand_discount').val(discAmt.toFixed(2));
                    }
                    const freight = parseFloat($('#freight').val())        || 0;
                    const preBal  = parseFloat($('#pre_bal').val())        || 0;
                    $('#total_plus_discount').val((total - discAmt + freight).toFixed(2));
                    $('#grand_total').val((parseFloat($('#total_plus_discount').val()) + preBal).toFixed(2));
                }

                
                $('#supplier_id').on('change', function () {
                    const customerId = $(this).val();
                    const $dc = $('#dc_vno');
                    $dc.empty().append('<option value="">-- Select DC --</option>');
                    if (!customerId) return;

                    const $opt = $('#supplier_id :selected');
                    const optTransportId = $opt.data('transport-id');
                    const optBilty = $opt.data('bilty');

                    if (discInitDone) {
                        if (optTransportId) {
                            $('#transport_company_id').val(optTransportId).trigger('change');
                        }
                        if (optBilty) {
                            $('#bilty_no').val(optBilty);
                        }

                        const optDisc = parseFloat($('#supplier_id :selected').data('disc')) || 0;
                        customerDiscPct = optDisc;
                        $('#discount_percentage').val(customerDiscPct > 0 ? customerDiscPct : 0);
                        const total = parseFloat($('#row_total').val()) || 0;
                        const discAmt = (total * customerDiscPct) / 100;
                        $('#grand_discount').val(discAmt.toFixed(2));
                        recalcGrandTotal();

                        axios.get('/customers-by-acc/' + customerId).then(function (res) {
                            if (res.data && res.data.disc !== undefined && res.data.disc !== null) {
                                customerDiscPct = parseFloat(res.data.disc) || 0;
                                $('#discount_percentage').val(customerDiscPct > 0 ? customerDiscPct : 0);
                                const curTotal = parseFloat($('#row_total').val()) || 0;
                                $('#grand_discount').val(((curTotal * customerDiscPct) / 100).toFixed(2));
                                recalcGrandTotal();
                            }
                        });
                        axios.get('/open_bal/get-balance/' + customerId).then(function (res) {
                            $('#pre_bal').val(res.data.balance || 0);
                            recalcGrandTotal();
                        });
                    } else {
                        discInitDone = true; 
                    }
                    axios.get('/get-dcs-by-customer/' + customerId).then(function (res) {
                        const dcList = Array.isArray(res.data) ? res.data : (res.data.dcs || []);
                        dcList.forEach(function (dc) {
                            $dc.append('<option value="' + dc.v_no + '">' + dc.v_no + ' (' + dc.v_date + ')</option>');
                        });
                        $dc.trigger('change.select2');
                        if (savedDcVno) {
                            $dc.val(savedDcVno).trigger('change.select2');
                            savedDcVno = '';
                        }
                        if (res.data.default_transport_company_id && !$('#transport_company_id').val()) {
                            $('#transport_company_id').val(res.data.default_transport_company_id).trigger('change');
                        }
                        if (res.data.default_bilty_no && !$('#bilty_no').val()) {
                            $('#bilty_no').val(res.data.default_bilty_no);
                        }
                    });
                });

                // ====== DC → replace items ======
                $('#dc_vno').on('change', function () {
                    const dcVno = $(this).val();
                    if (!dcVno) return;

                    axios.get('/get-dc-items/' + dcVno)
                        .then(function (res) {
                            const dcMaster = res.data.master;
                            const dcItems  = res.data.items;
                            if (!dcItems || dcItems.length === 0) {
                                alert('No items found in this DC.');
                                return;
                            }

                            if (dcMaster && dcMaster.ref_name_id) {
                                $('#ref_name_id').val(dcMaster.ref_name_id).trigger('change');
                            }

                            if (dcMaster && dcMaster.bilty_no) {
                                $('#bilty_no').val(dcMaster.bilty_no);
                            }

                            if (dcMaster && dcMaster.transport_company_id) {
                                $('#transport_company_id').val(dcMaster.transport_company_id).trigger('change');
                            }

                            if (dcMaster && dcMaster.freight !== undefined && dcMaster.freight !== null) {
                                const dcf = parseFloat(dcMaster.freight) || 0;
                                if (dcf > 0 || !$('#freight').val() || parseFloat($('#freight').val()) === 0) {
                                    $('#freight').val(dcf);
                                }
                            }

                            const qpc = parseFloat(dcMaster?.qty_per_carton) || 0;

                            const grouped = {};
                            dcItems.forEach(function (dcItem) {
                                const id = dcItem.item_id;
                                if (!grouped[id]) grouped[id] = {
                                    item_id: id,
                                    load_qty: 0,
                                    item: dcItem.item,
                                    consumption_qpc: parseFloat(dcItem.consumption_qpc) || qpc
                                };
                                grouped[id].load_qty += parseFloat(dcItem.load_qty) || 0;
                            });

                            $('#saleTable').empty();

                            Object.values(grouped).forEach(function (ag, idx) {
                                const cartons   = Math.round(ag.load_qty);
                                const productQpc = ag.consumption_qpc;
                                const totalQty  = cartons * productQpc;
                                const productId = ag.item_id;
                                const saleRate  = ag.item?.cost_price || ag.item?.sale_rate || 0;

                                const row = $(`
                                    <div class="sale-row border rounded px-3 py-1 mb-3 bg-light">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-3">
                                                <label class="form-label">Product</label>
                                                <select name="items[${idx}][item_id]" class="form-control item-select select2">
                                                    <option value="" data-rate="0" data-qpc="0">-- Select --</option>
                                                    @foreach ($items as $i)
                                                        <option value="{{ $i->id }}"
                                                            data-rate="{{ $i->cost_price ?? $i->sale_rate }}"
                                                            data-qpc="{{ $i->consumption_qpc ?? 0 }}"
                                                            ${productId == {{ $i->id }} ? 'selected' : ''}>
                                                            {{ $i->product_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Ctn</label>
                                                <input type="number" class="form-control cartons" value="${cartons}" min="0">
                                                <input type="hidden" name="items[${idx}][qty]" class="qty-hidden" value="${totalQty}">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">QPC</label>
                                                <input type="number" class="form-control qpc" value="${productQpc}">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">T.Qty</label>
                                                <input type="number" class="form-control total-qty" value="${totalQty}" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Rate</label>
                                                <input type="number" name="items[${idx}][rate]" class="form-control rate" value="${saleRate}">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Amount</label>
                                                <input type="text" class="form-control row-amount" readonly>
                                            </div>
                                            <input type="hidden" name="items[${idx}][tax_rate]" value="0">
                                            <input type="hidden" name="items[${idx}][discount]" value="0">
                                            <input type="hidden" name="items[${idx}][discount_perc]" value="0">
                                            <div class="col-md-1 text-center">
                                                <button type="button" class="btn btn-danger btn-sm mt-4 remove-row">X</button>
                                            </div>
                                        </div>
                                    </div>
                                `);

                                $('#saleTable').append(row);
                                row.find('.select2').select2({ placeholder: '-- Select --', width: '100%' });
                                row.find('.select2').data('no-ajax', true);
                                recalcRow(row);
                            });

                            recalcGrandTotal();

                            // Trigger rate fetch for every populated row
                            $('.item-select').each(function () {
                                if ($(this).val()) {
                                    $(this).trigger('change');
                                }
                            });
                        })
                        .catch(function (err) {
                            console.error(err);
                            alert('Error loading DC items.');
                        });
                });

                // ====== ITEM SELECT — rate + history (scoped to row) ======
                $(document).on('change', '.item-select', function (e) {
                    const $row = $(this).closest('.sale-row');
                    const $opt = $(this).find(':selected');
                    const itemName = $opt.data('english') || $opt.text();
                    const saleRate = parseFloat($opt.data('rate')) || 0;
                    const optQpc = parseFloat($opt.data('qpc')) || 0;
                    if (optQpc > 0) {
                        $row.find('.qpc').val(optQpc);
                    }
                    const itemId = $(this).val();
                    const customerId = $('#supplier_id').val() || 0;

                    const origRate = $row.find('.rate').val();
                    console.log('[CHANGE] Handler fired:', { itemId, customerId, origRate, saleRate, currentVno, supplierIdRaw: JSON.stringify($('#supplier_id').val()) });
                    recalcRow($row);

                    if (!itemId) { console.log('[CHANGE] No itemId, returning'); return; }
                    if (!customerId || customerId === '0') { console.log('[CHANGE] WARNING: customerId is 0 or empty!'); }

                    console.log('[CHANGE] Making axios GET to:', `/itemSelected/${itemId}/${customerId}`, { exclude_vno: currentVno });

                    axios.get(`/itemSelected/${itemId}/${customerId}`, {
                        params: { _: Date.now(), exclude_vno: currentVno }
                    })
                        .then(function (response) {
                            const records    = response.data;
                            const balanceQty = records.balanced?.length ? records.balanced[0].balance_qty : 0;

                            if (records.qty_per_carton && parseFloat(records.qty_per_carton) > 0) {
                                $row.find('.qpc').val(parseFloat(records.qty_per_carton));
                            }

                            console.log('[CHANGE] AXIOS SUCCESS. Full response:', JSON.stringify(records, null, 2));
                            console.log('[CHANGE] lastRate =', records.lastRate, '(type:', typeof records.lastRate, ')');

                            if (records.lastRate !== null && records.lastRate !== undefined) {
                                var newRate = parseFloat(records.lastRate);
                                console.log('[CHANGE] Setting rate to', newRate, 'from lastRate');
                                $row.find('.rate').val(newRate > 0 ? newRate : '');
                            } else {
                                console.log('[CHANGE] lastRate is null/undefined, using saleRate =', saleRate);
                                $row.find('.rate').val(saleRate > 0 ? saleRate : '');
                            }
                            console.log('[CHANGE] Rate after set =', $row.find('.rate').val());
                            recalcRow($row);

                            $('.box-history').html(`
                                <table>
                                    <thead>
                                        <tr><th colspan="3">Item Name</th><th colspan="3">${itemName}</th></tr>
                                        <tr>
                                            <th>Selling Price</th><th colspan="2">${saleRate}</th>
                                            <th>Balanced Qty</th><th colspan="2">${balanceQty}</th>
                                        </tr>
                                        <tr>
                                            <th>#</th><th>Invoice Date</th><th>Customer</th>
                                            <th>Qty</th><th>Rate</th><th>Discount %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${records.sale.map(s => `
                                            <tr>
                                                <td>${s.v_no}</td>
                                                <td>${new Date(s.created_at).toLocaleDateString()}</td>
                                                <td>${s.customer?.name || ''}</td>
                                                <td>${s.qty}</td>
                                                <td>${s.rate}</td>
                                                <td>${s.discount_perc ?? 0}</td>
                                            </tr>`).join('')}
                                    </tbody>
                                </table>`);
                        })
                        .catch(function (err) {
                            console.error(err);
                            $('.box-history').html('<p>Error loading item details.</p>');
                        });
                });

                // ====== CARTONS / QPC / RATE input ======
                $(document).on('input', '.cartons, .qpc, .rate', function () {
                    recalcRow($(this).closest('.sale-row'));
                });

                // ====== DISCOUNT % / GRAND DISCOUNT / FREIGHT ======
                $('#discount_percentage').on('input', function () {
                    discInitDone = true;
                    customerDiscPct = parseFloat($(this).val()) || 0;
                    const total = parseFloat($('#row_total').val()) || 0;
                    const discAmt = (total * customerDiscPct) / 100;
                    $('#grand_discount').val(discAmt.toFixed(2));
                    recalcGrandTotal();
                });

                $('#grand_discount').on('input', function () {
                    discInitDone = true;
                    const discAmt = parseFloat($(this).val()) || 0;
                    const total = parseFloat($('#row_total').val()) || 0;
                    if (total > 0) {
                        customerDiscPct = parseFloat(((discAmt / total) * 100).toFixed(2));
                        $('#discount_percentage').val(customerDiscPct);
                    } else {
                        customerDiscPct = 0;
                        $('#discount_percentage').val(0);
                    }
                    recalcGrandTotal();
                });
                $('#freight, #pre_bal').on('input', recalcGrandTotal);

                // ====== REMOVE ROW ======
                $(document).on('click', '.remove-row', function () {
                    $(this).closest('.sale-row').remove();
                    recalcGrandTotal();
                });

                // ====== FILE PREVIEW ======
                $('#uploadFile').on('change', function () {
                    const file = this.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        $('#imagePreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                    $('#fileNamePreview').text(file.name);
                });

                $('#removeFile').on('click', function () {
                    $('#uploadFile').val('');
                    $('#imagePreview').attr('src', '').hide();
                    $('#fileNamePreview').text('');
                    const $old = $('input[name="old_file"]');
                    if ($old.length) $old.val('');
                });

                // ====== INITIAL CALC on page load ======
                recalcGrandTotal();

                // Populate DC dropdown for the pre-selected customer
                if ($('#supplier_id').val()) {
                    $('#supplier_id').trigger('change');
                }

                // Trigger historical rate fetch for all pre-selected items
                console.log('[DEBUG] About to trigger change on .item-select elements, count:', $('.item-select').length);
                $('.item-select').each(function (idx) {
                    var v = $(this).val();
                    console.log('[DEBUG] .item-select[' + idx + '] val=' + JSON.stringify(v) + ' willTrigger=' + (!!v));
                    if (v) {
                        $(this).trigger('change');
                        console.log('[DEBUG] .item-select[' + idx + '] trigger change done');
                    }
                });

            });
        </script>
@endsection