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
                        <li class="breadcrumb-item"><a href="{{ route('stock-in.index') }}">Stock In</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Stock In #{{ $stockIn->inv_no }}</h4>
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

    <form action="{{ route('stock-in.update', $stockIn->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-2">
                <label for="inv_no" class="form-label fw-bold">Inv No</label>
                <input type="number" name="inv_no" id="inv_no" class="form-control" value="{{ $stockIn->inv_no }}" readonly>
            </div>
            <div class="col-md-2">
                <label for="date" class="form-label fw-bold">Date</label>
                <input type="date" name="date" required id="date" class="form-control" value="{{ $stockIn->date }}">
            </div>
            <div class="col-md-3">
                <label for="ref_name_id" class="form-label fw-bold">Loder Name</label>
                <select name="ref_name_id" id="ref_name_id" class="form-control select2">
                    <option value="">-- Select Reference Name --</option>
                    @foreach($referenceNames as $rn)
                        <option value="{{ $rn->id }}" {{ $stockIn->ref_name_id == $rn->id ? 'selected' : '' }}>{{ $rn->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="dala_no" class="form-label fw-bold">Dala No</label>
                <input type="number" name="dala_no" required id="dala_no" class="form-control" value="{{ $stockIn->dala_no }}">
            </div>
        </div>

        <div id="stockInTable">
            @forelse ($stockIn->details as $index => $detail)
            <div class="stockin-row border rounded px-3 py-1 mb-3 bg-light">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Product</label>
                        <select name="details[{{ $index }}][product_id]" class="form-control product-select select2">
                            <option value="">-- Select Product --</option>
                            @foreach ($products as $p)
                            <option value="{{ $p->id }}" {{ $detail->product_id == $p->id ? 'selected' : '' }}>{{ $p->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contractor</label>
                        <select name="details[{{ $index }}][contractor_id]" class="form-control contractor-select select2">
                            <option value="">-- Select Contractor --</option>
                            @foreach ($contractors as $c)
                            <option value="{{ $c->id }}" {{ $detail->contractor_id == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lot No</label>
                        <input type="text" name="details[{{ $index }}][lot_no]" class="form-control lot-no" value="{{ $detail->lot_no }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Qty</label>
                        <input type="text" name="details[{{ $index }}][qty]" class="form-control qty" value="{{ $detail->qty }}" min="0">
                    </div>
                    @php
                        $detailLot = trim((string)($detail->lot_no ?? ''));
                        $detailPrevKey = $detail->product_id . '_' . $detailLot;
                        $detailPrevBal = $previousBalances[$detailPrevKey] ?? 0;
                    @endphp
                    <div class="col-md-1">
                        <label class="form-label">Previous</label>
                        <input type="text" class="form-control previous" value="{{ $detailPrevBal }}" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control total" value="{{ $detailPrevBal + $detail->qty }}" readonly>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                    </div>
                </div>
            </div>
            @empty
            <div class="stockin-row border rounded px-3 py-1 mb-3 bg-light">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Product</label>
                        <select name="details[0][product_id]" class="form-control product-select select2">
                            <option value="">-- Select Product --</option>
                            @foreach ($products as $p)
                            <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contractor</label>
                        <select name="details[0][contractor_id]" class="form-control contractor-select select2">
                            <option value="">-- Select Contractor --</option>
                            @foreach ($contractors as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lot No</label>
                    <input type="text" name="details[0][lot_no]" class="form-control lot-no">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Qty</label>
                        <input type="text" name="details[0][qty]" class="form-control qty" value="0" min="0">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Previous</label>
                        <input type="text" class="form-control previous" value="0" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control total" value="0" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <div class="border-top pt-2 mt-0 mb-0 bg-white total-row">
            <div class="row g-2 align-items-center">
                <div class="col-md-3"><strong>Total</strong></div>
                <div class="col-md-3"></div>
                <div class="col-md-2"></div>
                <div class="col-md-1"><strong id="footerQty">0</strong></div>
                <div class="col-md-1"></div>
                <div class="col-md-1"></div>
                {{-- <div class="col-md-1"><strong id="footerTotal">0</strong></div> --}}
                <div class="col-md-1"></div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
            <div>
                <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
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
        row.find('.total').val(prev + qty);
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
        const invNo = '{{ $stockIn->inv_no }}';

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
    });

    const previousBalances = @json($previousBalances);
    const contractorByProduct = @json($contractorByProduct ?? []);

    const rowCount = $('#stockInTable .stockin-row').length;
    for (let i = rowCount; i < 2; i++) {
        $('#addRow').trigger('click');
    }
    recalcFooter();
});
</script>
@endsection
