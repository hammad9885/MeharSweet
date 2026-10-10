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
                            <li class="breadcrumb-item active">Edit Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit sale Return Invoice</h4>
                </div>
            </div>
        </div>
        {{-- {{ dd($saleMaster->items->first()) }} --}}
        <form action="{{ route('sale_return_invoices.update', $saleMaster->v_no) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row mb-3 align-items-end">
                <!-- Customer -->
                <div class="col-md-4 mb-1">

                    <label for="customer_id" class="form-label">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control" required>
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $supplier)
                            <option value="{{ $supplier->acc_id }}"
                                {{ $supplier->acc_id == $saleMaster->items->first()->customer_id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>

                </div>
                <div class="col-md-2 ">

                    <label for="v_date" class="form-label">Date</label>
                    <input type="date" class="form-control" name="v_date" id="v_date" required
                        value="{{ $saleMaster->v_date ?? '' }}">

                </div>
                <div class="col-md-3">
                    <label for="gatepass_no" class="form-label">Gate Pass #</label>
                    <input type="text" name="gatepass_no" id="gatepass_no" class="form-control"
                        value="{{ $saleMaster->gate_pass_no }}">
                </div>
                <div class="col-md-3"></div>
            </div>

            {{-- {{ dd($items) }} --}}
            <!-- Invoice Items -->
            <div id="itemsContainer">
                @foreach ($invoiceItems as $index => $item)
                    <div class="card mb-3 item-block">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="m-0">Item {{ $index + 1 }}</h6>
                                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label>Item</label>
                                    <select name="items[{{ $index }}][item_id]" class="form-control item-select"
                                        required>
                                        <option value="">-- Select --</option>
                                        @foreach ($items as $i)
                                            <option value="{{ $i->id }}"
                                                data-rate="{{ $i->cost_price ?? $i->sale_rate }}"
                                                data-tax="{{ $i->sale_in }}"
                                                {{ $i->id == $item->item_id ? 'selected' : '' }}>
                                                {{ $i->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 w-10">
                                    <label>Qty</label>
                                    <input type="number" name="items[{{ $index }}][qty]" class="form-control qty"
                                        value="{{ $item->qty }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Rate</label>
                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control rate"
                                        value="{{ $item->rate }}">
                                </div>

                                <input type="hidden" name="items[{{ $index }}][discount]"
                                    class="form-control discount" value="{{ $item->discount ?? 0 }}">

                                <div class="col-md-1">
                                    <label>Tax %</label>
                                    <input type="text" name="items[{{ $index }}][tax_rate]"
                                        class="form-control tax_rate" value="{{ $item->tax_rate }}">
                                </div>
                                <div class="col-md-2"></div>

                                <div class="col-md-2">
                                    <label>Exclusive</label>
                                    <input type="text" class="form-control exclusive" value="{{ $item->exclusive }}"
                                        readonly>
                                </div>
                                <div class="col-md-2">
                                    <label>Tax Amt</label>
                                    <input type="text" class="form-control tax_amount" value="{{ $item->tax_amount }}"
                                        readonly>
                                </div>
                                <div class="col-md-2">
                                    <label>Inclusive</label>
                                    <input type="text" class="form-control inclusive" value="{{ $item->inclusive }}"
                                        readonly>
                                </div>
                                <div class="col-md-1">
                                    <label>Discount %</label>
                                    <input type="number" name="items[{{ $index }}][discount_perc]"
                                        class="form-control discount-perc" value="{{ $item->discount_perc ?? 0 }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Total</label>
                                    <input type="text" class="form-control final_after_discount"
                                        value="{{ $item->inclusive }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Totals -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">

                            <label for="uploadFile" class="form-label">Upload File</label>

                            <input type="file" id="uploadFile" class="form-control" name="file">

                            <div id="filePreviewContainer" class="mt-2">

                                @if (!empty($maxVoucherNo->file_id))
                                    <!-- Existing Image -->
                                    <img id="imagePreview" src="{{ asset('storage/' . $maxVoucherNo->file_id) }}"
                                        style="max-width:150px; max-height:150px;">

                                    <br>

                                    <span id="fileNamePreview">{{ $maxVoucherNo->file_id }}</span>

                                    <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>

                                    <!-- Hidden field to track old file -->
                                    <input type="hidden" name="old_file" value="{{ $maxVoucherNo->file_id }}">
                                @else
                                    <img id="imagePreview" src=""
                                        style="max-width:150px; max-height:150px; display:none;">

                                    <span id="fileNamePreview"></span>
                                @endif

                            </div>


                        </div>
                        <div class="col">
                            <div class="col-md-3 offset-md-9">
                                <label>Grand Discount</label>
                                <input type="number" id="grand_discount" name="grand_discount" class="form-control"
                                    value="{{ $saleMaster->discount }}">
                            </div>
                            <div class="col-md-3 offset-md-9">
                                <label>Grand Total</label>
                                <input type="text" id="grand_total" class="form-control"
                                    value="{{ $saleMaster->grand_total }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                </div>
                <!-- Actions -->
                <div class="mt-3 d-flex gap-2">
                    <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
                    <button type="submit" class="btn btn-success">Update Invoice</button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $(document).ready(function() {

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

            $(document).on('change', '.item-select, #customer_id', function() {
                // console.log("skdjdh");

                let itemId = $(this).val(); // <-- Item ID

                const selectedOption = $(this).find(':selected');
                const itemName = selectedOption.data('english') || selectedOption.text();
                const itemRate = selectedOption.data('rate') ?? 0;
                const customerId = $('select[name="customer_id"]').val() ?? 0;
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
            // ====== Item change recalculation ======
            // Normal select
            // $(document).on('change', '.item-select', function () {
            //     console.log("item changed");
            //     const row = $(this).closest('.item-block')[0];
            //     recalcRow(row);
            // });

            // // Select2 select
            // $(document).on('change', '.item-select', function () {
            //   console.log("nxcn");
            //     const row = $(this).closest('.sale_return-row');
            //             const itemSelect = $(row).find('.item-select')[0];
            //     const option = itemSelect.options[itemSelect.selectedIndex];
            //     const rate = parseFloat(option.getAttribute('data-rate')) || 0;
            //       console.log(rate);
            //     const taxRate = parseFloat(option.getAttribute('tax_rate')) || 0;
            //     $(row).find('.rate').val(rate.toFixed(2));
            //     $(row).find('.tax_rate').val(taxRate.toFixed(2));

            //     recalcRow(row);
            // });
            $(document).on('change', '.item-select', function() {

                console.log("item changed");

                const row = $(this).closest('.item-block');

                const option = this.options[this.selectedIndex];

                const rate = parseFloat(option.dataset.rate) || 0;
                const taxRate = parseFloat(option.dataset.tax) || 0;

                row.find('.rate').val(rate.toFixed(2));
                row.find('.tax_rate').val(taxRate.toFixed(2));

                recalcRow(row[0]);
            });
        });

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
            $(row).find('.final_after_discount').val(finalAfterRowDiscount.toFixed(2));

            recalcGrandTotal();
        }

        function recalcGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-block').forEach(block => {
                const val = parseFloat(block.querySelector('.final_after_discount').value) || 0;
                total += val;
            });
            const grandDiscount = parseFloat(document.getElementById('grand_discount').value) || 0;
            document.getElementById('grand_total').value = (total - grandDiscount).toFixed(2);
        }

        document.addEventListener('input', e => {
            if (e.target.closest('.item-block')) recalcRow(e.target.closest('.item-block'));
            if (e.target.id === 'grand_discount') recalcGrandTotal();
        });

        document.getElementById('addRow').addEventListener('click', function() {
            const container = document.getElementById('itemsContainer');
            const index = container.querySelectorAll('.item-block').length;
            // const index = $('#saleTable tbody tr').length;
            const newRow = document.createElement('div');
            newRow.classList.add('item-block', 'border', 'rounded', 'p-3', 'mb-3', 'bg-light');
            newRow.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label">Item</label>
                <select name="items[${index}][item_id]" class="form-control item-select select2" required>
                    <option value="" data-rate="0" data-tax="0">-- Select --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-rate="{{ $item->cost_price ?? $item->sale_rate }}" data-tax="{{ $item->sale_in ?? 0 }}">
                            {{ $item->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 w-10">
                <label class="form-label">Qty</label>
                <input type="number" name="items[${index}][qty]" class="form-control qty " value="1">
            </div>
            <div class="col-md-2">
                <label class="form-label">Rate</label>
                <input type="number" name="items[${index}][rate]" class="form-control rate" value="0" >
            </div>
            
           
                <input type="hidden" name="items[${index}][discount]" class="form-control discount" value="0">
          
            <div class="col-md-2">
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
            <div class="col-md-2"><label class="form-label">Total</label><input type="text" class="form-control final_after_discount" readonly></div>
        </div>`;
            container.appendChild(newRow);

            // init select2 for the newly added select and focus search input
            if (window.jQuery && jQuery().select2) {
                const $sel = $(newRow).find('.select2');
                $sel.select2({
                    width: '100%',
                    placeholder: '-- Select --',
                    allowClear: true
                });
                // open dropdown after adding for quick selection (optional)
                $sel.select2('open');
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.item-block').remove();
                recalcGrandTotal();
            }
        });

        // ====== EVENT LISTENERS ======
        //    $(document).on('change', '.item-select', function () {
        //     console.log("item changed");
        //     const row = $(this).closest('.item-block')[0];
        //     recalcRow(row);
        // });

        // $(document).on('select2:select', '.item-select', function () {
        //     console.log("item select2 changed");
        //     const row = $(this).closest('.item-block')[0];
        //     recalcRow(row);
        // });
        $(document).on('input', '.qty, .rate,.discount-perc,.tax_rate', function() {
            console.log("qty/discount changed");
            const row = $(this).closest('.item-block')[0];
            recalcRow(row);
        });




        // console.log("sjzxn");

        // Initialize on load
        document.querySelectorAll('.item-block').forEach(block => recalcRow(block));


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
    </script>
@endsection
