@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- Start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Softix</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Edit Confectionery</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Confectionery</h4>
                </div>
            </div>
        </div>
        <!-- End page title -->

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div id="clientAlert" class="alert alert-danger d-none"></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">

                                <form id="voucherForm"
                                    action="{{ route('confectionery.update', $voucher->first()->v_no) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
                                        <button type="submit" class="btn btn-success">Submit Voucher</button>
                                        <div class="ms-3">
                                            <label class="form-label mb-0 me-1">Voucher Date:</label>
                                            <input type="date" id="voucherDate" class="form-control d-inline-block" style="width:auto;"
                                                value="{{ optional($voucher->first())->date ? \Carbon\Carbon::parse(optional($voucher->first())->date)->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-sm table-bordered align-middle mt-4" id="entriesTable" style="min-width:1200px; font-size: 0.92rem;">
                                            <thead>
                                                <tr style="white-space:nowrap;">
                                                    <th style="min-width:40px;">Sr No</th>
                                                    <th style="min-width:110px;">Date</th>
                                                    <th style="min-width:180px;">Product Name</th>
                                                    <th style="min-width:180px;">Account Title</th>
                                                    <th style="min-width:160px;">Item Title</th>
                                                    <th style="min-width:110px;">CTN</th>
                                                    <th style="min-width:110px;">Pack Qty</th>
                                                    <th style="min-width:110px;">PO No</th>
                                                    <th style="min-width:110px;">Rate</th>
                                                    <th style="min-width:110px;">Total</th>
                                                    <th style="min-width:110px;">Freight</th>
                                                    <th style="min-width:90px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="entriesBody">
@if ($voucher->isNotEmpty())
    @foreach ($voucher->sortBy('sequence_no') as $index => $trndtl)
        @php 
            $sequenceNo = $trndtl->confectioneryDetails->sequence_no ?? ($index + 1);
            $entryId = $trndtl->confectionerydetails->id ?? '';
        @endphp
        <tr data-entry-id="{{ $entryId }}">
            <td class="sequence-display">{{ $sequenceNo }}</td>
            <td>
                <!-- LOCKED DATE - Visible readonly input -->
                <input type="date" class="form-control" 
                       value="{{ isset($trndtl->date) ? \Carbon\Carbon::parse($trndtl->date)->format('Y-m-d') : '' }}" 
                       readonly>
                <!-- Hidden inputs for form submission -->
                <input type="hidden" name="entries[{{ $entryId }}][date]" 
                       value="{{ isset($trndtl->date) ? \Carbon\Carbon::parse($trndtl->date)->format('Y-m-d') : '' }}">
                <input type="hidden" name="entries[{{ $entryId }}][id]" value="{{ $entryId }}">
                <input type="hidden" name="entries[{{ $entryId }}][sequence_no]" value="{{ $sequenceNo }}" class="sequence-input">
                <input type="hidden" name="entries[{{ $entryId }}][prepared_by]" value="{{ $loggedInUser->name ?? '' }}">
            </td>
            <td>
                <select name="entries[{{ $entryId }}][product]" class="form-control select2">
                    <option value="">Select</option>
                    @foreach ($products as $prod)
                        <option value="{{ $prod->id }}" @if(($trndtl->confectionerydetails->products->id ?? null) == $prod->id) selected @endif>{{ $prod->prod_name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="entries[{{ $entryId }}][orig_product]" value="{{ $trndtl->confectionerydetails->products->id ?? '' }}">
            </td>
            <td>
                <!-- LOCKED SUPPLIER - Visible disabled select -->
                <select class="form-control select2" disabled>
                    <option value="">Select</option>
                    @foreach ($accounts->where('level2_id', 7) as $account)
                        <option value="{{ $account->id }}" @if(($trndtl->accounts->id ?? null) == $account->id) selected @endif>{{ $account->title }}</option>
                    @endforeach
                </select>
                <!-- Hidden input for form submission -->
                <input type="hidden" name="entries[{{ $entryId }}][supplier]" value="{{ $trndtl->accounts->id ?? '' }}">
            </td>
            <td>
                <select name="entries[{{ $entryId }}][item]" class="form-control select2">
                    <option value="">Select</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @if(($trndtl->confectionerydetails->itemType->id ?? null) == $item->id) selected @endif>{{ $item->type_title }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="entries[{{ $entryId }}][orig_item]" value="{{ $trndtl->confectionerydetails->itemType->id ?? '' }}">
            </td>
            <td><input type="number" name="entries[{{ $entryId }}][box]" class="form-control" value="{{ $trndtl->confectionerydetails->box ?? '' }}"></td>
            <td><input type="number" name="entries[{{ $entryId }}][packing]" class="form-control" value="{{ $trndtl->confectionerydetails->pack_qty ?? '' }}"></td>
            <td><input type="text" name="entries[{{ $entryId }}][po_no]" class="form-control" value="{{ $trndtl->confectionerydetails->po_no ?? '' }}"></td>
            <td>
                <input type="number" step="0.01" name="entries[{{ $entryId }}][rate]" class="form-control rate-input" value="{{ $rates[$trndtl->confectionerydetails->id] ?? 0 }}" readonly>
            </td>
            <td><input type="number" step="0.01" name="entries[{{ $entryId }}][total]" class="form-control entry-total" value="{{ $trndtl->confectionerydetails->total ?? '' }}" readonly></td>
            <td><input type="number" name="entries[{{ $entryId }}][freight]" class="form-control" value="{{ $trndtl->confectionerydetails->freight ?? 0 }}"></td>
            <td>
                @if (isset($billed[$trndtl->confectionerydetails->id]) && $billed[$trndtl->confectionerydetails->id])
                    <button type="button" class="btn btn-danger btn-sm" disabled title="Remove billings first">Delete</button>
                @else
                    <button type="button" class="btn btn-danger btn-sm delete-entry">Delete</button>
                @endif
            </td>
        </tr>
    @endforeach
@endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="9" style="text-align: right;"><strong>Grand Total:</strong></td>
                                                    <td id="grandTotal">0</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </form>
                            </div> <!-- End preview-->
                        </div> <!-- End tab-content-->
                    </div> <!-- End card-body -->
                </div> <!-- End card -->
            </div><!-- End col -->
        </div><!-- End row -->
    </div> <!-- End container -->

    <!-- jQuery (Required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const entriesTable = document.getElementById('entriesBody');
        const addEntryButton = document.getElementById('addEntry');
        let deletedIds = [];
        let newEntryCounter = 0;

        // Build ProductMaster rate maps for quick lookup
        const productRates = @json($products->mapWithKeys(function($p){ return [$p->id => (float)($p->rate ?? 0)]; })->toArray());
        const itemRates = @json($products->groupBy('item_id')->map(function($grp){ return (float)($grp->first()->rate ?? 0); })->toArray());

        function showTopAlert(message) {
            const banner = document.getElementById('clientAlert');
            if (!banner) return;
            banner.textContent = message;
            banner.classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Extract global values from first row (LOCK AND CARRY MECHANISM)
        function getGlobalValues() {
            let firstRow = document.querySelector('#entriesBody tr');
            if (!firstRow) return {};
            
            let date = firstRow.querySelector('input[name$="[date]"]')?.value || '';
            let supplier = firstRow.querySelector('input[name$="[supplier]"]')?.value || '';
            
            return { date, supplier };
        }

        function renumberSequences() {
            const rows = entriesTable.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const sequenceNo = index + 1;
                const sequenceDisplay = row.querySelector('.sequence-display');
                if (sequenceDisplay) {
                    sequenceDisplay.textContent = sequenceNo;
                }
                const sequenceInput = row.querySelector('.sequence-input');
                if (sequenceInput) {
                    sequenceInput.value = sequenceNo;
                }
            });
        }

        function updateRateForRow(row) {
            const $row = $(row);
            const productId = $row.find('select[name^="entries"][name$="[product]"]').val();
            const itemId = $row.find('select[name^="entries"][name$="[item]"]').val();
            let rate = 0;
            if (productId && productRates[productId] !== undefined) {
                rate = productRates[productId] || 0;
            } else if (itemId && itemRates[itemId] !== undefined) {
                rate = itemRates[itemId] || 0;
            }
            $row.find('input[name^="entries"][name$="[rate]"]').val(rate);
        }

        function initializeDeleteButtons() {
            entriesTable.querySelectorAll('.delete-entry').forEach(function(btn) {
                btn.onclick = function() {
                    @if (isset($hasBilling) && $hasBilling)
                        showTopAlert('Please remove the associated billings first before deleting entries.');
                        return;
                    @else
                        const row = btn.closest('tr');
                        const entryId = row.getAttribute('data-entry-id');
                        if (entryId && entryId !== 'new') {
                            deletedIds.push(entryId);
                            let input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'deleted_ids[]';
                            input.value = entryId;
                            document.getElementById('voucherForm').appendChild(input);
                        }
                        row.remove();
                        renumberSequences();
                        recalcAll();
                    @endif
                };
            });
        }

        if (addEntryButton) {
            addEntryButton.addEventListener('click', function() {
                const uniqueKey = 'new_' + Date.now() + '_' + (++newEntryCounter);
                const nextSequence = entriesTable.querySelectorAll('tr').length + 1;
                
                // Get global values to carry forward (LOCK AND CARRY MECHANISM)
                const globals = getGlobalValues();
                
                // Get supplier title for display
                let supplierTitle = '';
                let supplierId = globals.supplier || '';
                if (supplierId) {
                    let firstRow = document.querySelector('#entriesBody tr');
                    if (firstRow) {
                        let select = firstRow.querySelector('select[disabled]');
                        if (select) {
                            let selectedOption = select.querySelector('option:checked');
                            if (selectedOption) {
                                supplierTitle = selectedOption.textContent;
                            }
                        }
                    }
                }

                // Build supplier select HTML with locked selection
                let supplierSelectHtml = `<select class="form-control select2" disabled>`;
                supplierSelectHtml += `<option value="">Select</option>`;
                @foreach ($accounts->where('level2_id', 7) as $account)
                    supplierSelectHtml += `<option value="{{ $account->id }}"` + (supplierId == '{{ $account->id }}' ? ' selected' : '') + `>{{ $account->title }}</option>`;
                @endforeach
                supplierSelectHtml += `</select>`;
                
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-entry-id', 'new');
                newRow.innerHTML = `
                    <td class="sequence-display">${nextSequence}</td>
                    <td>
                        <!-- LOCKED DATE for new entry -->
                        <input type="date" class="form-control" value="${globals.date}" readonly>
                        <input type="hidden" name="entries[${uniqueKey}][date]" value="${globals.date}">
                        <input type="hidden" name="entries[${uniqueKey}][sequence_no]" value="${nextSequence}" class="sequence-input">
                        <input type="hidden" name="entries[${uniqueKey}][prepared_by]" value="{{ $loggedInUser->name ?? '' }}">
                    </td>
                    <td>
                        <select name="entries[${uniqueKey}][product]" class="form-control select2">
                            <option value="">Select</option>
                            @foreach ($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->prod_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <!-- LOCKED SUPPLIER for new entry -->
                        ${supplierSelectHtml}
                        <input type="hidden" name="entries[${uniqueKey}][supplier]" value="${supplierId}">
                    </td>
                    <td>
                        <select name="entries[${uniqueKey}][item]" class="form-control select2">
                            <option value="">Select</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->type_title }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="entries[${uniqueKey}][box]" class="form-control"></td>
                    <td><input type="number" name="entries[${uniqueKey}][packing]" class="form-control"></td>
                    <td><input type="text" name="entries[${uniqueKey}][po_no]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="entries[${uniqueKey}][rate]" class="form-control rate-input" value="0" readonly></td>
                    <td><input type="number" step="0.01" name="entries[${uniqueKey}][total]" class="form-control entry-total" value="0" readonly></td>
                    <td><input type="number" name="entries[${uniqueKey}][freight]" class="form-control" value="0"></td>
                    <td><button type="button" class="btn btn-danger btn-sm delete-entry">Delete</button></td>
                `;
                entriesTable.appendChild(newRow);
                initializeDeleteButtons();
                $(newRow).find('.select2').select2();
                updateRateForRow(newRow);
                recalcRowTotal(newRow);
                recalcAll();
            });
        }

        function recalcRowTotal(row) {
            const box = parseFloat($(row).find('input[name^="entries"][name$="[box]"]').val()) || 0;
            const pack = parseFloat($(row).find('input[name^="entries"][name$="[packing]"]').val()) || 0;
            const rate = parseFloat($(row).find('input[name^="entries"][name$="[rate]"]').val()) || 0;
            const total = box * pack * rate;
            $(row).find('.entry-total').val(total.toFixed(2));
        }

        function recalcAll() {
            let grand = 0;
            $('#entriesBody tr').each(function() {
                recalcRowTotal(this);
                const total = parseFloat($(this).find('.entry-total').val()) || 0;
                grand += total;
            });
            $('#grandTotal').text(grand.toFixed(2));
        }

        function bindCalcHandlers() {
            $('#entriesBody').on('input', 'input[name$="[box]"], input[name$="[packing]"], input[name$="[rate]"]', function() {
                const row = $(this).closest('tr');
                recalcRowTotal(row);
                recalcAll();
            });
            $('#entriesBody').on('change', 'select[name$="[product]"], select[name$="[item]"]', function() {
                const row = $(this).closest('tr');
                updateRateForRow(row);
                recalcRowTotal(row);
                recalcAll();
            });
        }

        // Global date update handler (LOCK AND CARRY MECHANISM)
        const voucherDate = document.getElementById('voucherDate');
        if (voucherDate) {
            voucherDate.addEventListener('change', function() {
                const val = this.value;
                if (!val) return;
                // Update all hidden date inputs
                $('#entriesBody input[name$="[date]"]').each(function(){
                    this.value = val;
                });
                // Update all visible readonly date inputs
                $('#entriesBody input[type="date"][readonly]').each(function(){
                    this.value = val;
                });
            });
        }

        // Initialize everything
        initializeDeleteButtons();
        bindCalcHandlers();
        $('#entriesBody tr').each(function(){
            updateRateForRow(this);
        });
        recalcAll();
        renumberSequences();
    });

    $(document).ready(function () {
        $('.select2').select2();
    });
    </script>
@endsection