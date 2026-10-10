@extends('layouts.app')

@section('content')
    <style>
        /* History container */
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

        /* soft hover */
        .box-history:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }


        /* ===== Table styling ===== */

        .box-history table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        /* header */
        .box-history table th {
            background: #f7fbfb;
            font-weight: 600;
            padding: 8px;
            border-bottom: 2px solid #dee2e6;
            color: #58a3b3;

        }

        /* cells */
        .box-history table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        /* zebra rows */
        .box-history table tbody tr:nth-child(even) {
            background: #f6f6f6;


        }

        /* hover row */
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">purchase Return</a></li>
                            <li class="breadcrumb-item active">Create Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Create purchase Return </h4>
                </div>
            </div>
        </div>

        <form action="{{ route('purchase_return_invoices.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Supplier -->
            <div class="row align-items-end">

                <div class="col-md-3 mb-3">
                    <label for="supplier_id" class="form-label fw-bold">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                        <option value="">-- Select Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->acc_id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="v_date" class="form-label fw-bold">Date</label>
                    <input type="date" name="v_date" id="v_date" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="gatepass_no" class="form-label">Gate Pass #</label>
                    <input type="text" name="gatepass_no" id="gatepass_no" class="form-control">
                </div>
                <div class="col-md-1"></div>
            </div>

            <!-- Dynamic Items Section -->
            <div id="purchase_returnTable">
                <div class="purchase_return-row border rounded p-3 mb-3 bg-light">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Item</label>
                            <select name="items[0][item_id]" class="form-control item-select select2" required>
                                <option value="" data-rate="0" data-tax="0">-- Select --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}"
                                        data-tax="{{ $item->sale_in ?? 0 }}">
                                        {{ $item->item_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Qty</label>
                            <input type="number" name="items[0][qty]" class="form-control qty" value="1">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Rate</label>
                            <input type="number" name="items[0][rate]" class="form-control rate" value="0">
                        </div>


                        <input type="hidden" name="items[0][discount]" class="form-control discount" value="0">


                        <div class="col-md-1">
                            <label class="form-label">Tax %</label>
                            <input type="text" name="items[0][tax_rate]" class="form-control tax_rate">
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
                            <label class="form-label">Discount %</label>
                            <input type="number" name="items[0][discount_perc]" class="form-control discount-perc"
                                value="0">
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
                    <div id="filePreviewContainer" class="mt-2" style="display:none;">
                        <img id="imagePreview" src="" alt="Image Preview"
                            style="max-width: 150px; max-height: 150px; display:none;">
                        <span id="fileNamePreview" style="font-size:14px;"></span>
                        <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Grand Discount</label>
                        <input type="number" id="grand_discount" class="form-control" value="0"
                            name="grand_discount">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grand Total</label>
                        <input type="text" id="grand_total" class="form-control" readonly name="grand_total">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
                <button type="submit" class="btn btn-success">Save Invoice</button>
            </div>
        </form>
        <div class="row mt-4">
            <div class="col-12">
                <div class="box-history"></div>
            </div>
        </div>
    </div>

    <!-- jQuery + Select2 must be loaded -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        console.log("sjdxn");
        $(document).ready(function() {
            // Initialize Select2
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

            // console.log("asa");
  $(document).on('change', '.item-select,#supplier_id', function() {
                 let row = $(this).closest('.purchase_return-row'); // get current row
                let itemId = row.find('.item-select').val();
                let supplierId = $('#supplier_id').val(); // <-- Item ID
                if (!supplierId) {
                    supplierId = 0;
                }
                const selectedOption = $('.item-select').find(':selected');
                const itemName = selectedOption.data('english') || selectedOption.text();
                const itemRate = selectedOption.data('rate') ?? 0;
                row.find('.rate').val(parseFloat(itemRate).toFixed(2));
                axios.get(`/itemSelectedPR/${itemId}/${supplierId}`) // Removed extra }
                    .then(function(response) {
                        const records = response.data; // assuming response.data is an array of items
                        if (response.data.lastRate) {
                            const lastRate = response.data.lastRate;

                           row.find('.rate').val(parseFloat(lastRate).toFixed(2));
                        }
                        const tableHtml = `
            <table>
                <thead>
                    <tr>
                        <th colspan=3>Item Name</th>
                        <th colspan=3>${itemName}</th>
                       
                    </tr>
             
                    <tr>
                    <th>Purchase Price</th>
                    <th colspan=2>${itemRate}</th>
                    <th>Balanced Qty</th>
                    <th colspan=2>${records.balanced[0].balance_qty}</th>
                    </tr>
                  
                 
                    <tr>
                        <th>#</th>
                        <th>Invoice Date</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Discount %</th>
                    </tr>
                </thead>
                <tbody>
                    ${records.sale.map(map => `
                                <tr>
                                <td>${map.v_no}</td>
                             <td>${new Date(map.created_at).toLocaleDateString()}</td>
                                    <td>${map.supplier?.name || ''}</td>
                                    <td>${map.qty}</td>
                                    <td>${map.rate}</td>
                                    <td>${map.discount_perc}</td>
                                </tr>
                            `).join('')}
                </tbody>
            </table>
        `;

                        $('.box-history').html(tableHtml); // Replace content instead of append
                    })
                    .catch(function(error) {
                        console.error(error);
                        $('.box-history').html('<p>Error loading item details.</p>');
                    });
            });
            
            // ====== FUNCTIONS ======
            function recalcRow(row) {
                let discount = 0;
                const qty = parseFloat($(row).find('.qty').val()) || 0;
                const discount_perc = parseFloat($(row).find('.discount-perc').val()) || 0;
                const itemSelect = $(row).find('.item-select')[0];
                const option = itemSelect.options[itemSelect.selectedIndex];
                const rate = parseFloat($(row).find('.rate').val()) || 0;
                // const taxRate = parseFloat(option.getAttribute('data-tax')) || 0;
                const taxRate = parseFloat($(row).find('.tax_rate').val()) || 0;

                // $(row).find('.tax_rate').val(taxRate.toFixed(2));

                const exclusive = qty * rate;
                const taxAmount = exclusive * (taxRate / 100);
                const inclusive = exclusive + taxAmount;
                discount = (inclusive * discount_perc) / 100;
                const finalAfterRowDiscount = inclusive - discount;

                $(row).find('.exclusive').val(exclusive.toFixed(2));
                $(row).find('.tax_amount').val(taxAmount.toFixed(2));
                $(row).find('.inclusive').val(inclusive.toFixed(2));
                $(row).find('.final_after_row_discount').val(finalAfterRowDiscount.toFixed(2));

                recalcGrandTotal();
            }

            function recalcGrandTotal() {
                let total = 0;
                $('.purchase_return-row').each(function() {
                    total += parseFloat($(this).find('.final_after_row_discount').val()) || 0;
                });
                console.log(total);
                const grandDiscount = parseFloat($('#grand_discount').val()) || 0;
                $('#grand_total').val((total - grandDiscount).toFixed(2));
            }

            // ====== EVENT LISTENERS ======
            $(document).on('input', '.qty, .discount-perc,.rate,.tax_rate', function() {

                const row = $(this).closest('.purchase_return-row');
                recalcRow(row);
            });

            // $(document).on('change', '.item-select', function () {
            //     const row = $(this).closest('.purchase_return-row');
            //     const selected = $(this).find(':selected');
            //     const rate = parseFloat(selected.data('rate')) || 0;
            //     console.log(rate);
            //     $(row).find('.rate').val(rate.toFixed(2));
            //     recalcRow(row);
            // });

            // $(document).on('select2:select', '.item-select', function () {
            //     const row = $(this).closest('.purchase_return-row');
            //      const selected = $(this).find(':selected');
            //     const rate = parseFloat(selected.data('rate')) || 0;
            //     // console.log(rate);
            //     $(row).find('.rate').val(rate.toFixed(2));
            //     recalcRow(row);
            // });
            $(document).on('select2:select', '.item-select', function() {
                console.log("zsxJh");
                const row = $(this).closest('.purchase_return-row');
                const selected = $(this).find(':selected');

                const rate = parseFloat(selected.data('rate')) || 0;

                const taxRate = parseFloat(selected.data('tax')) || 0;
                // ALWAYS update when item changes (correct behavior)
                $(row).find('.rate').val(rate.toFixed(2));
                $(row).find('.tax_rate').val(taxRate.toFixed(2));


                recalcRow(row);
            });
            // added

            $('#grand_discount').on('input', function() {
                recalcGrandTotal();
            });

            $('#addRow').click(function() {
                const container = $('#purchase_returnTable');
                const index = container.find('.purchase_return-row').length;
                const newRow = $(`
            <div class="purchase_return-row border rounded p-3 mb-3 bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Item</label>
                        <select name="items[${index}][item_id]" class="form-control item-select select2" required>
                            <option value="" data-rate="0" data-tax="0">-- Select --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}" data-tax="{{ $item->sale_in ?? 0 }}">
                                    {{ $item->item_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" name="items[${index}][qty]" class="form-control qty" value="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rate</label>
                        <input type="number" name="items[${index}][rate]" class="form-control rate" value="0" >
                    </div>
                     
                        <input type="hidden" name="items[${index}][discount]" class="form-control discount" value="0">
                 
                 
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-danger btn-sm mt-4 remove-row">X</button>
                    </div>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-md-2"><label class="form-label">Exclusive</label><input type="text" class="form-control exclusive" readonly></div>
                    <div class="col-md-2"><label class="form-label">Tax Amt</label><input type="text" class="form-control tax_amount" readonly></div>
                    <div class="col-md-2"><label class="form-label">Inclusive</label><input type="text" class="form-control inclusive" readonly></div>
                     <div class="col-md-1">
                            <label class="form-label">Discount %</label>
                            <input type="number" name="items[${index}][discount_perc]" class="form-control discount-perc" value="0">
                        </div>
                    <div class="col-md-2"><label class="form-label">Total</label><input type="text" class="form-control final_after_row_discount" readonly></div>
                </div>
            </div>
        `);

                container.append(newRow);

                // Reinitialize Select2 for new row
                newRow.find('.select2').select2({
                    placeholder: "-- Select --",
                    width: '100%'
                });
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('.purchase_return-row').remove();
                recalcGrandTotal();
            });

            // Initial calculation
            $('.purchase_return-row').each(function() {
                recalcRow(this);
            });

            document.getElementById('uploadFile').addEventListener('change', function(e) {

                let file = e.target.files[0];

                if (file) {

                    let reader = new FileReader();

                    reader.onload = function(event) {

                        document.getElementById('imagePreview').src = event.target.result;
                        document.getElementById('imagePreview').style.display = 'block';

                    }

                    reader.readAsDataURL(file);

                    document.getElementById('fileNamePreview').innerText = file.name;
                }

            });


            document.getElementById('removeFile').addEventListener('click', function() {

                document.getElementById('uploadFile').value = '';
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('fileNamePreview').innerText = '';

                // remove old file reference
                document.querySelector('input[name="old_file"]').value = '';
            });
        });
    </script>
@endsection
