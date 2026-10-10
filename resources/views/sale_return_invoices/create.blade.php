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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">sale Return</a></li>
                            <li class="breadcrumb-item active">Create Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Create Sale Return </h4>
                </div>
            </div>
        </div>

        <form action="{{ route('sale_return_invoices.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Customer -->
            <div class="row g-3 mb-3">

                <div class="col-md-4 mb-1">
                    <label class="form-label fw-bold">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control select2" required>
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->acc_id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="v_date" id="v_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="gatepass_no" class="form-label">Gate Pass #</label>
                    <input type="text" name="gatepass_no" id="gatepass_no" class="form-control">
                </div>
                <div class="col-md-2"></div>

            </div>

            <!-- Dynamic Items Section -->
            <div id="sale_returnTable">
                <div class="sale_return-row border rounded p-3 mb-3 bg-light">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Item</label>
                            <select name="items[0][item_id]" class="form-control item-select select2" required>
                                <option value="" data-rate="0" data-tax="0">-- Select --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}"
                                        data-rate="{{ $item->cost_price ?? $item->sale_rate }}"
                                        data-tax="{{ $item->sale_out }}">
                                        {{ $item->product_name }}
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


                        <input type="hidden" name="items[0][discount]" class="form-control discount" value="">

                        <div class="col-md-2">
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
            <div class="row">

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
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
            document.getElementById('uploadFile').addEventListener('change', function(e) {

                // console.log("dnxb");

                let file = e.target.files[0];

                if (file) {

                    let reader = new FileReader();

                    reader.onload = function(event) {

                        document.getElementById('imagePreview').src = event.target.result;
                        document.getElementById('imagePreview').style.display = 'block';

                        // show container
                        document.getElementById('filePreviewContainer').style.display = 'block';

                    }

                    reader.readAsDataURL(file);

                    document.getElementById('fileNamePreview').innerText = file.name;
                }

            });
            document.getElementById('removeFile').addEventListener('click', function() {

                document.getElementById('uploadFile').value = '';
                document.getElementById('imagePreview').src = '';
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('fileNamePreview').innerText = '';

                document.getElementById('filePreviewContainer').style.display = 'none';

            });
            $(document).on('change', '.item-select, #customer_id', function() {

                let itemId = $(this).val(); // <-- Item ID

                const selectedOption = $(this).find(':selected');
                const itemName = selectedOption.data('english') || selectedOption.text();
                const itemRate = selectedOption.data('rate') ?? 0;
                let customerId = $('select[name="customer_id"]').val() ?? 0;
                if(!customerId){
                    customerId=0;
                }
                // console.log(itemId);
                axios.get(`/itemSelectedR/${itemId}/${customerId}`) // Removed extra }
                    .then(function(response) {
                        const records = response.data; // assuming response.data is an array of items
                        if (records.sale.length > 0) {
                            const balanceQty =
                                records.balanced && records.balanced.length ?
                                records.balanced[0].balance_qty :
                                0;
                            const tableHtml = `
            <table>
                <thead>
                    <tr>
                        <th colspan=3>Item Name</th>
                        <th colspan=3>${itemName}</th>
                       
                    </tr>
             
                    <tr>
                    <th>Selling Price</th>
                    <th colspan=2>${itemRate}</th>
                    <th>Balanced Qty</th>
                    <th colspan=2>${balanceQty}</th>
                    </tr>
                  
                 
                    <tr>
                        <th>#</th>
                        <th>Invoice Date</th>
                        <th>Customer</th>
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
                                        <td>${map.customer?.name || ''}</td>
                                        <td>${map.qty}</td>
                                        <td>${map.rate}</td>
                                        <td>${map.discount_perc ?? 0}</td>
                                    </tr>
                                `).join('')}
                </tbody>
            </table>
        `;

                            $('.box-history').html(tableHtml); // Replace content instead of append
                        } else if (records.balanced.length > 0) {
                            const tableHtml = `
            <table>
                <thead>
                    <tr>
                        <th colspan=3>Item Name</th>
                        <th colspan=3>${itemName}</th>
                       
                    </tr>
             
                    <tr>
                    <th>Selling Price</th>
                    <th colspan=2>${itemRate}</th>
                    <th>Balanced Qty</th>
                    <th colspan=2>${records.balanced[0].balance_qty}</th>
                    </tr>
                  
                 
                    <tr>
                        <th>#</th>
                        <th>Invoice Date</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Discount</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        `;

                            $('.box-history').html(tableHtml); // Replace content instead of append
                        } else {
                            $('.box-history').html('<p>Not Found Previous Record.</p>');
                        }
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
                const rate = parseFloat($(row).find('.rate').val()) || 0;
                const taxRate = parseFloat($(row).find('.tax_rate').val()) || 0;




                const exclusive = qty * rate;
                const taxAmount = exclusive * (taxRate / 100);
                const inclusive = exclusive + taxAmount;
                discount = (inclusive * discount_perc) / 100;
                const finalAfterRowDiscount = inclusive - discount;

                $(row).find('.discount').val(discount.toFixed(2));
                $(row).find('.exclusive').val(exclusive.toFixed(2));
                $(row).find('.tax_amount').val(taxAmount.toFixed(2));
                $(row).find('.inclusive').val(inclusive.toFixed(2));
                $(row).find('.final_after_row_discount').val(finalAfterRowDiscount.toFixed(2));

                recalcGrandTotal();
            }

            function recalcGrandTotal() {
                let total = 0;
                $('.sale_return-row').each(function() {
                    total += parseFloat($(this).find('.final_after_row_discount').val()) || 0;
                });
                const grandDiscount = parseFloat($('#grand_discount').val()) || 0;
                $('#grand_total').val((total - grandDiscount).toFixed(2));
            }

            // ====== EVENT LISTENERS ======
            $(document).on('input', '.qty, .discount-perc,.rate,.tax_rate', function() {
                const row = $(this).closest('.sale_return-row');
                recalcRow(row);
            });

            $(document).on('change', '.item-select', function() {
                // const row = $(this).closest('.sale_return-row');
                //         const itemSelect = $(row).find('.item-select')[0];
                // const option = itemSelect.options[itemSelect.selectedIndex];
                // const rate = parseFloat(option.getAttribute('data-rate')) || 0;
                // // const taxRate = parseFloat(option.getAttribute('tax_rate')) || 0;
                //  const taxRate = option.data('tax') || 0;
                // $(row).find('.rate').val(rate.toFixed(2));
                // $(row).find('.tax_rate').val(taxRate.toFixed(2));

                // recalcRow(row);
                const row = $(this).closest('.sale_return-row'); // ✅ correct container

                const option = $(this).find(':selected'); // ✅ safer with Select2

                const rate = Number(option.data('rate')) || 0;
                const taxRate = Number(option.data('tax')) || 0;

                row.find('.rate').val(rate.toFixed(2));
                row.find('.tax_rate').val(taxRate.toFixed(2));

                recalcRow(row[0]);
            });

            $(document).on('select2:select', '.item-select', function() {
                const row = $(this).closest('.sale_return-row');
                recalcRow(row);
            });

            $('#grand_discount').on('input', function() {
                recalcGrandTotal();
            });

            $('#addRow').click(function() {
                const container = $('#sale_returnTable');
                const index = container.find('.sale_return-row').length;
                const newRow = $(`
            <div class="sale_return-row border rounded p-3 mb-3 bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Item</label>
                        <select name="items[${index}][item_id]" class="form-control item-select select2" required>
                            <option value="" data-rate="0" data-tax="0">-- Select --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" data-rate="{{ $item->cost_price ?? $item->sale_rate }}" data-tax="{{ $item->sale_out }}">
                                    {{ $item->product_name }}
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
                    <div class="col-md-1">
                        <label class="form-label">Tax %</label>
                        <input type="text" name="items[${index}][tax_rate]" class="form-control tax_rate" >
                    </div>
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
                $(this).closest('.sale_return-row').remove();
                recalcGrandTotal();
            });

            // Initial calculation
            $('.sale_return-row').each(function() {
                recalcRow(this);
            });
        });
    </script>
@endsection
