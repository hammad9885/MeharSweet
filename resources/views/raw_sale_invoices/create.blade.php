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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Inventory</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Raw Material</a></li>
                            <li class="breadcrumb-item active">Create Raw Sale Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Create Raw Sale Invoice</h4>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('raw_sale_invoices.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-4 mb-1">
                    <label for="supplier_id" class="form-label fw-bold">Customer</label>
                    <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->acc_id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="v_date" class="form-label fw-bold">Date</label>
                    <input type="date" name="v_date" id="v_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label for="gatepass_no" class="form-label">Gate Pass #</label>
                    <input type="text" name="gatepass_no" id="gatepass_no" class="form-control">
                </div>
            </div>

            <!-- Dynamic Items Section -->
            <div id="saleTable">
                <div class="sale-row border rounded p-3 mb-3 bg-light">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Item (Raw)</label>
                            <select name="items[0][item_id]" class="form-control item-select select2" required>
                                <option value="" data-rate="0" data-tax="0">-- Select --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" data-rate="{{ $item->cost_price ?? $item->sale_rate }}"
                                        data-tax="{{ $item->tax_out ?? 0 }}">
                                        {{ $item->item_code }} - {{ $item->item_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Qty</label>
                            <input type="number" name="items[0][qty]" class="form-control qty" value="1" step="0.01">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Rate</label>
                            <input type="number" name="items[0][rate]" class="form-control rate" value="0" step="0.01">
                        </div>

                        <input type="hidden" name="items[0][discount]" class="form-control discount" value="0">

                        <div class="col-md-1">
                            <label class="form-label">Tax %</label>
                            <input type="text" name="items[0][tax_rate]" class="form-control tax_rate" value="0">
                        </div>

                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-danger btn-sm mt-4 remove-row">X</button>
                        </div>
                    </div>

                    <div class="row g-2 mt-3">
                        <div class="col-md-2">
                            <label class="form-label">Exclusive</label>
                            <input type="text" class="form-control exclusive" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tax Amt</label>
                            <input type="text" class="form-control tax_amount" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Inclusive</label>
                            <input type="text" class="form-control inclusive" readonly>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Disc %</label>
                            <input type="number" name="items[0][discount_perc]" class="form-control discount-perc" value="0" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control final_after_row_discount" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totals -->
            <div class="row align-items-center justify-content-between mt-4">
                <div class=" col-md-4 mb-3">
                    <label for="uploadFile" class="form-label">Upload File</label>
                    <input type="file" id="uploadFile" class="form-control" name="file">
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Grand Discount</label>
                        <input type="number" id="grand_discount" class="form-control" value="0" name="grand_discount" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grand Total</label>
                        <input type="text" id="grand_total" class="form-control" readonly name="grand_total">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
                <button type="submit" class="btn btn-success">Save Raw Invoice</button>
            </div>
        </form>

        <div class="row mt-4">
            <div class="col-12">
                <div class="box-history"></div>
            </div>
        </div>
    </div>

    <!-- jQuery + Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select --",
                width: '100%'
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

            function recalcRow(row) {
                const qty = parseFloat($(row).find('.qty').val()) || 0;
                const rate = parseFloat($(row).find('.rate').val()) || 0;
                const taxRate = parseFloat($(row).find('.tax_rate').val()) || 0;
                const discPerc = parseFloat($(row).find('.discount-perc').val()) || 0;

                const exclusive = qty * rate;
                const taxAmount = exclusive * (taxRate / 100);
                const inclusive = exclusive + taxAmount;
                const discount = (inclusive * discPerc) / 100;
                const final = inclusive - discount;

                $(row).find('.exclusive').val(exclusive.toFixed(2));
                $(row).find('.tax_amount').val(taxAmount.toFixed(2));
                $(row).find('.inclusive').val(inclusive.toFixed(2));
                $(row).find('.discount').val(discount.toFixed(2));
                $(row).find('.final_after_row_discount').val(final.toFixed(2));

                recalcGrandTotal();
            }

            function recalcGrandTotal() {
                let total = 0;
                $('.final_after_row_discount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                const grandDisc = parseFloat($('#grand_discount').val()) || 0;
                $('#grand_total').val((total - grandDisc).toFixed(2));
            }

            $(document).on('input', '.qty, .rate, .tax_rate, .discount-perc', function() {
                recalcRow($(this).closest('.sale-row'));
            });

            $(document).on('change', '.item-select', function() {
                const row = $(this).closest('.sale-row');
                const opt = $(this).find(':selected');
                row.find('.rate').val(opt.data('rate') || 0);
                row.find('.tax_rate').val(opt.data('tax') || 0);
                recalcRow(row);
            });

            $('#grand_discount').on('input', recalcGrandTotal);

            $('#addRow').click(function() {
                const index = $('.sale-row').length;
                const newRow = $('.sale-row:first').clone();
                
                newRow.find('input').val(0);
                newRow.find('.qty').val(1);
                newRow.find('.select2-container').remove();
                newRow.find('select').attr('name', `items[${index}][item_id]`).val('').select2();
                newRow.find('input[name*="[qty]"]').attr('name', `items[${index}][qty]`);
                newRow.find('input[name*="[rate]"]').attr('name', `items[${index}][rate]`);
                newRow.find('input[name*="[tax_rate]"]').attr('name', `items[${index}][tax_rate]`);
                newRow.find('input[name*="[discount]"]').attr('name', `items[${index}][discount]`);
                newRow.find('input[name*="[discount_perc]"]').attr('name', `items[${index}][discount_perc]`);
                
                $('#saleTable').append(newRow);
            });

            $(document).on('click', '.remove-row', function() {
                if ($('.sale-row').length > 1) {
                    $(this).closest('.sale-row').remove();
                    recalcGrandTotal();
                }
            });
        });
    </script>
@endsection
