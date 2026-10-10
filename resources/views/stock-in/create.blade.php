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
                        <li class="breadcrumb-item active">Create Stock In</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Stock In</h4>
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

    <form action="{{ route('stock-in.store') }}" method="POST">
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
                <label for="ref_name_id" class="form-label fw-bold">Loder Name</label>
                <select name="ref_name_id" id="ref_name_id" class="form-control select2">
                    <option value="">-- Select Reference Name --</option>
                    @foreach($referenceNames as $rn)
                        <option value="{{ $rn->id }}" {{ old('ref_name_id') == $rn->id ? 'selected' : '' }}>{{ $rn->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="dala_no" class="form-label fw-bold">Dala No</label>
                <input type="number" name="dala_no" required id="dala_no" class="form-control" value="{{ old('dala_no') }}">
            </div>
        </div>

        <div id="stockInTable"></div>

        <div class="border-top pt-2 mt-0 mb-0 bg-white total-row">
            <div class="row g-2 align-items-center">
                <div class="col-md-3"><strong>Total</strong></div>
                <div class="col-md-3"></div>
                <div class="col-md-2"></div>
                <div class="col-md-1"><strong id="footerQty">0</strong></div>
                <div class="col-md-1"></div>
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

/* Limit exceeded popup */
#limitWarningPopup {
    display: none;
    position: fixed;
    top: 28px;
    right: 28px;
    z-index: 999999;
    min-width: 300px;
    max-width: 380px;
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 10px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.18);
    padding: 16px 20px 14px 16px;
    font-family: inherit;
    animation: slideInRight 0.3s ease;
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(60px); }
    to   { opacity: 1; transform: translateX(0); }
}
#limitWarningPopup .lw-title {
    font-weight: 700;
    font-size: 15px;
    color: #856404;
    margin-bottom: 5px;
}
#limitWarningPopup .lw-msg {
    font-size: 13px;
    color: #664d03;
    margin-bottom: 10px;
}
#limitWarningPopup .lw-close {
    position: absolute;
    top: 8px;
    right: 12px;
    background: none;
    border: none;
    font-size: 18px;
    color: #856404;
    cursor: pointer;
    font-weight: bold;
    line-height: 1;
}

/* Qty box red when limit exceeded */
.qty-exceeded {
    background-color: #f8d7da !important;
    border: 2px solid #dc3545 !important;
    color: #842029 !important;
    font-weight: bold;
}
</style>

<!-- Limit Warning Popup -->
<div id="limitWarningPopup">
    <button class="lw-close" onclick="document.getElementById('limitWarningPopup').style.display='none'">&#x2715;</button>
    <div class="lw-title">&#9888; Limit Exceed Ho Gayi!</div>
    <div class="lw-msg" id="limitWarningMsg"></div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    $('#addRow').click(function() {
        const container = $('#stockInTable');
        const index = container.find('.stockin-row').length;
        const row = $(`
        <div class="stockin-row border rounded px-3 py-1 mb-3 bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select name="details[${index}][product_id]" class="form-control product-select select2">
                        <option value="">-- Select Product --</option>
                        @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contractor</label>
                    <select name="details[${index}][contractor_id]" class="form-control contractor-select select2">
                        <option value="">-- Select Contractor --</option>
                        @foreach ($contractors as $c)
                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lot No</label>
                    <input type="text" name="details[${index}][lot_no]" class="form-control lot-no">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Qty</label>
                    <input type="text" name="details[${index}][qty]" class="form-control qty" value="0" min="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Previous</label>
                    <input type="text" class="form-control previous" value="0" readonly>
                </div>
                <div class="col-md-1">
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
    });

    function recalcRow(row) {
        const prev = parseFloat(row.find('.previous').val()) || 0;
        const qty = parseFloat(row.find('.qty').val()) || 0;
        const total = prev + qty;
        row.find('.total').val(total);

        // Check limit for this row
        const limit = parseFloat(row.data('cartons-limit')) || 0;
        const $qtyInput = row.find('.qty');
        if (limit > 0 && total > limit) {
            $qtyInput.addClass('qty-exceeded');
            const productName = row.find('.product-select option:selected').text().trim() || 'Product';
            showLimitWarning(productName, total, limit);
        } else {
            $qtyInput.removeClass('qty-exceeded');
        }

        checkAllLimits();
    }

    // Check all rows — if any row exceeded, disable Add+Save buttons
    function checkAllLimits() {
        let anyExceeded = false;
        $('#stockInTable .stockin-row').each(function() {
            if ($(this).find('.qty').hasClass('qty-exceeded')) {
                anyExceeded = true;
                return false; // break
            }
        });

        if (anyExceeded) {
            $('#addRow').prop('disabled', true).addClass('disabled');
            $('button[type="submit"]').prop('disabled', true).addClass('disabled');
        } else {
            $('#addRow').prop('disabled', false).removeClass('disabled');
            $('button[type="submit"]').prop('disabled', false).removeClass('disabled');
        }
    }

    var limitWarnTimer = null;
    function showLimitWarning(productName, total, limit) {
        const msg = '<strong>' + productName + '</strong> ki Total (<strong>' + total + '</strong>) Consumption mein set limit (<strong>' + limit + '</strong>) se zyada ho gayi hai!';
        document.getElementById('limitWarningMsg').innerHTML = msg;
        const popup = document.getElementById('limitWarningPopup');
        popup.style.display = 'block';
        if (limitWarnTimer) clearTimeout(limitWarnTimer);
        limitWarnTimer = setTimeout(function() {
            popup.style.display = 'none';
        }, 6000);
    }

    function recalcFooter() {
        let qtyTotal = 0;
        let totalTotal = 0;
        $('#stockInTable .stockin-row').each(function() {
            qtyTotal += parseFloat($(this).find('.qty').val()) || 0;
            totalTotal += parseFloat($(this).find('.total').val()) || 0;
        });
        $('#footerQty').text(qtyTotal);
        $('#footerTotal').text(totalTotal);
    }

    function fetchProductData(row, explicitLot = null) {
        const pid = row.find('.product-select').val();
        const cid = row.find('.contractor-select').val() || '';
        const invNo = $('#inv_no').val() || '';

        if (!pid) {
            row.find('.lot-no').val('');
            row.find('.previous').val(0);
            recalcRow(row);
            recalcFooter();
            return;
        }

        const reqData = {
            product_id: pid,
            contractor_id: cid,
            inv_no: invNo
        };
        if (explicitLot !== null) {
            reqData.lot_no = explicitLot;
        }

        $.ajax({
            url: "{{ route('stock-in.get-product-data') }}",
            data: reqData,
            success: function(res) {
                if (explicitLot === null) {
                    row.find('.lot-no').val(res.lot_no);
                }
                row.find('.previous').val(res.previous);
                // Store total cartons limit on the row
                row.data('cartons-limit', res.total_cartons_in_lot || 0);
                recalcRow(row);
                recalcFooter();
            }
        });
    }

    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('.stockin-row');
        const pid = $(this).val();
        
        const contractorId = pid ? (contractorByProduct[pid] || '') : '';
        if (contractorId) {
            row.find('.contractor-select').val(contractorId).trigger('change.select2');
        }

        fetchProductData(row);
    });

    $(document).on('change', '.contractor-select', function() {
        const row = $(this).closest('.stockin-row');
        fetchProductData(row);
    });

    $(document).on('change', '.lot-no', function() {
        const row = $(this).closest('.stockin-row');
        const lot = $(this).val();
        fetchProductData(row, lot);
    });

    $(document).on('input', '.qty', function() {
        recalcRow($(this).closest('.stockin-row'));
        recalcFooter();
    });

    $(document).on('click', '.remove-row', function() {
        const container = $('#stockInTable');
        if (container.find('.stockin-row').length <= 1) {
            alert('At least one row is required.');
            return;
        }
        $(this).closest('.stockin-row').remove();
        recalcFooter();
        checkAllLimits();
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
