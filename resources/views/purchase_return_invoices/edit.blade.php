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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Purchase Return</a></li>
                            <li class="breadcrumb-item active">Edit Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Purchase Return </h4>
                </div>
            </div>
        </div>

        <form action="{{ route('purchase_return_invoices.update', $purchaseMaster->v_no) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Supplier -->
            <div class="row align-items-end">

                <div class="col-md-3 mb-3">
                    <label for="supplier_id" class="form-label fw-bold">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">-- Select Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->acc_id }}"
                                {{ $supplier->acc_id == $purchaseMaster->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="v_date" class="form-label fw-bold">Date</label>
                    <input type="date" name="v_date" id="v_date" class="form-control"
                        value="{{ $purchaseMaster->v_date }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="gatepass_no" class="form-label">Gate Pass #</label>
                    <input type="text" name="gatepass_no" id="gatepass_no" class="form-control"
                        value="{{ $purchaseMaster->gate_pass_no }}">
                </div>

            </div>
            <!-- Invoice Items -->
            <div id="itemsContainer">
                @foreach ($invoiceItems as $index => $item)
                    <div class="card mb-3 item-block">
                        <div class="purchase_return-row card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="m-0">Item {{ $index + 1 }}</h6>
                                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                            </div>
                            <div class="purchase_return-row row g-2">
                                <div class="col-md-4">
                                    <label>Item</label>
                                    <select name="items[{{ $index }}][item_id]" class="form-control item-select"
                                        required>
                                        <option value="">-- Select --</option>
                                        @foreach ($items as $i)
                                            <option value="{{ $i->id }}" data-rate="{{ $i->purchase }}"
                                                data-tax="{{ $i->sale_in }}"
                                                {{ $i->id == $item->item_id ? 'selected' : '' }}>
                                                {{ $i->item_code }}
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
                                <div class="col-md-1">
                                    <label>Tax %</label>
                                    <input type="text" name="items[{{ $index }}][tax_rate]"
                                        class="form-control tax_rate" value="{{ $item->tax_rate }}">
                                </div>
                                <input type="hidden" name="items[{{ $index }}][discount]"
                                    class="form-control discount" value="{{ $item->discount ?? 0 }}">
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

                                <div class="col-md-2">
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
                    <div class="row g-3 justify-content-end">
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
                                    value="{{ $purchaseMaster->discount }}">
                            </div>
                            <div class="col-md-3 offset-md-9">
                                <label>Grand Total</label>
                                <input type="text" id="grand_total" class="form-control"
                                    value="{{ $purchaseMaster->grand_total }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function recalcRow(block) {
            // console.log(block);
            let discount = 0;
            const qty = parseFloat(block.querySelector('.qty')?.value) || 0;
            const discount_perc = parseFloat(block.querySelector('.discount-perc')?.value) || 0;

            const itemSelect = block.querySelector('.item-select');
            const option = itemSelect?.options[itemSelect.selectedIndex];

            const rate = parseFloat(block.querySelector('.rate')?.value) || 0;
            const taxRate = parseFloat(block.querySelector('.tax_rate')?.value) || 0;

            const exclusive = qty * rate;
            // console.log(exclusive);
            const taxAmount = exclusive * (taxRate / 100);
            const inclusive = exclusive + taxAmount;
            discount = (inclusive * discount_perc) / 100;
            const finalAfterDiscount = inclusive - discount;
            //  console.log(discount);
            block.querySelector('.exclusive').value = exclusive.toFixed(2);
            block.querySelector('.tax_amount').value = taxAmount.toFixed(2);
            block.querySelector('.inclusive').value = inclusive.toFixed(2);
            block.querySelector('.final_after_discount').value = finalAfterDiscount.toFixed(2);

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
            const newRow = document.createElement('div');


            newRow.classList.add('item-block');
            newRow.classList.add('sale-row', 'border', 'rounded', 'p-3', 'mb-3', 'bg-light');
            newRow.innerHTML = `
        <div class="row purchase_return-row g-2 align-items-center">
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
            <div class="col-md-2 w-10">
                <label class="form-label">Qty</label>
                <input type="number" name="items[${index}][qty]" class="form-control qty " value="1">
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
        $(document).on('change', '.item-select', function() {

        });

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
                const $select = $(this).closest('.select2-container').siblings('select').filter(function() {
                    return typeof $(this).data('select2') !== 'undefined';
                }).first();
                if ($select.length && $select.data('select2') && !$select.data('select2').isOpen()) {
                    $select.select2('open');
                }
            });

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
                    <th colspan=2>${records.balanced?.[0]?.balance_qty ?? 0}</th>
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


            $(document).on('change', '.item-select', function() {
                const row = $(this).closest('.item-block'); // ← correct class
                const selected = $(this).find(':selected');
                const rate = parseFloat(selected.data('rate')) || 0;
                const taxRate = parseFloat(selected.data('tax')) || 0;
                //   console.log(rate);
                row.find('.rate').val(rate.toFixed(2)); // now will set the input
                row.find('.tax_rate').val(taxRate.toFixed(2));
                recalcRow(row[0]);
            });
        });
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
