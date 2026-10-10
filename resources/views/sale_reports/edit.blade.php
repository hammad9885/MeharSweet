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
                            <li class="breadcrumb-item active">Edit Purchase Invoice</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Pharmaceutical</h4>
                </div>
            </div>
        </div>
        <!-- End page title -->

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div id="clientAlert" class="alert alert-danger d-none"></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="input-types-preview">
                                <div class="row">
                                    <form id="voucherForm"
                                          action="{{ route('delivery_challan.update', $voucher->first()->v_no) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="col-6 mb-2">
                                            <input type="hidden" name="v_type" value="PIN">
                                            <input type="hidden" name="invoice_number">
                                            <input type="hidden" id="totalAmount" name="total_amount">
                                            <input type="hidden" id="totalWeight" name="total_weight">

                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
                                                <button type="submit" class="btn btn-success">Submit Voucher</button>
                                                <div class="ms-3">
                                                    <label class="form-label mb-0 me-1">Voucher Date:</label>
                                                    <input type="date" id="voucherDate" class="form-control d-inline-block" style="width:auto;"
                                                           value="{{ optional($voucher->first())->date ? \Carbon\Carbon::parse(optional($voucher->first())->date)->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Entries Table -->
                                        <div class="col-lg-12">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-sm table-bordered align-middle mt-4"
                                                       id="entriesTable"
                                                       style="min-width:1200px; font-size: 0.92rem;">
                                                    <thead>
                                                        <tr style="white-space:nowrap;">
                                                            <th>Sr No</th>
                                                            <th>Date</th>
                                                            <th>Product Name</th>
                                                            <th>Account Title</th>
                                                            <th style="min-width:70px;">Item Title</th>
                                                            <th style="min-width:110px;">CTN</th>
                                                            <th style="min-width:110px;">Pack Qty</th>
                                                            <th style="min-width:110px;">Batch No</th>
                                                            <th style="min-width:110px;">Total</th>
                                                            <th style="min-width:110px;">Amount</th>
                                                            <th style="min-width:70px;">Freight</th>
                                                            <th style="min-width:110px;">Rate</th>
                                                            <th style="min-width:70px;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="entriesBody">
                                                        @php $totalEntries = 0; @endphp
                                                        @if ($voucher->isNotEmpty())
                                                            @foreach ($voucher as $trndtl)
                                                                @php 
                                                                    $entryId = $trndtl->deliverydetails->id ?? null; 
                                                                    $isBilled = isset($billed[$entryId]) && $billed[$entryId];
                                                                @endphp
                                                                <tr data-entry-id="{{ $entryId }}" data-billed="{{ $isBilled ? '1' : '0' }}">
                                                                    <td>{{ ++$totalEntries }}</td>
                                                                    <td>
                                                                        <input type="date"
                                                                               class="form-control"
                                                                               value="{{ isset($trndtl->date) ? \Carbon\Carbon::parse($trndtl->date)->format('Y-m-d') : '' }}"
                                                                               readonly>
                                                                        <input type="hidden"
                                                                               name="entries[{{ $entryId }}][date]"
                                                                               value="{{ isset($trndtl->date) ? \Carbon\Carbon::parse($trndtl->date)->format('Y-m-d') : '' }}">
                                                                        <input type="hidden"
                                                                               name="entries[{{ $entryId }}][id]"
                                                                               value="{{ $entryId }}">
                                                                    </td>
                                                                    <td>
                                                                        <select name="entries[{{ $entryId }}][product]"
                                                                                class="form-control select2 rate-select"
                                                                                data-rate-input="rate_{{ $entryId }}">
                                                                            <option value="">Select</option>
                                                                            @foreach ($product as $prod)
                                                                                <option value="{{ $prod->id }}"
                                                                                    data-rate="{{ $prod->sale_rate ?? $prod->rate ?? 0 }}"
                                                                                    @if(($trndtl->deliverydetails->products->id ?? null) == $prod->id) selected @endif>
                                                                                    {{ $prod->prod_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control select2" disabled>
                                                                            <option value="">Select</option>
                                                                            @foreach ($accounts->where('level2_id', 7) as $account)
                                                                                <option value="{{ $account->id }}"
                                                                                    @if(($trndtl->accounts->id ?? null) == $account->id) selected @endif>
                                                                                    {{ $account->title }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <input type="hidden"
                                                                               name="entries[{{ $entryId }}][supplier]"
                                                                               value="{{ $trndtl->accounts->id ?? '' }}">
                                                                    </td>
                                                                    <td>
                                                                        <select name="entries[{{ $entryId }}][item]"
                                                                                class="form-control select2">
                                                                            <option value="">Select</option>
                                                                            @foreach ($items as $item)
                                                                                <option value="{{ $item->id }}"
                                                                                    @if(($trndtl->deliverydetails->itemType->id ?? null) == $item->id) selected @endif>
                                                                                    {{ $item->type_title }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="number" name="entries[{{ $entryId }}][box]" class="form-control"
                                                                               value="{{ $trndtl->deliverydetails->box ?? '' }}"></td>
                                                                    <td><input type="number" name="entries[{{ $entryId }}][packing]" class="form-control"
                                                                               value="{{ $trndtl->deliverydetails->pack_qty ?? '' }}"></td>
                                                                    <td><input type="text" name="entries[{{ $entryId }}][batchNo]" class="form-control"
                                                                               value="{{ $trndtl->deliverydetails->batch_no ?? '' }}"></td>
                                                                    <td><input type="number" step="0.01" name="entries[{{ $entryId }}][total]" class="form-control entry-total"
                                                                               value="{{ $trndtl->deliverydetails->total ?? '' }}" readonly></td>
                                                                    <td><input type="number" step="0.01" name="entries[{{ $entryId }}][amount]" class="form-control entry-amount"
                                                                               value="{{ $trndtl->deliverydetails->box * $trndtl->deliverydetails->pack_qty }}" readonly></td>
                                                                    <td><input type="number" name="entries[{{ $entryId }}][freight]" class="form-control"
                                                                               value="{{ $trndtl->deliverydetails->freight ?? 0 }}"></td>
                                                                    <td>
                                                                        <input type="number" step="0.01" name="entries[{{ $entryId }}][rate]"
                                                                               class="form-control entry-rate"
                                                                               id="rate_{{ $entryId }}"
                                                                               value="{{ $trndtl->deliverydetails->products->sale_rate ?? $trndtl->deliverydetails->products->rate ?? 0 }}" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger btn-sm delete-entry"
                                                                            @if($isBilled) disabled title="This entry has a related bill and cannot be deleted" @endif>
                                                                            Delete
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="8" class="text-end"><strong>Grand Total:</strong></td>
                                                            <td id="grandTotal">0</td>
                                                            <td colspan="4"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <!-- jQuery (Required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const entriesTable = document.getElementById('entriesBody');
        const addEntryButton = document.getElementById('addEntry');
        let deletedIds = [];

        function showTopAlert(message) {
            const banner = document.getElementById('clientAlert');
            if (!banner) return;
            banner.textContent = message;
            banner.classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function initializeDeleteButtons() {
            entriesTable.querySelectorAll('.delete-entry').forEach(function(btn) {
                btn.onclick = function() {
                    const row = btn.closest('tr');
                    if (row && row.getAttribute('data-billed') === '1') {
                        showTopAlert('To delete this voucher entry, delete its billing first.');
                        return;
                    }
                    const entryId = row.getAttribute('data-entry-id');
                    if (entryId) {
                        deletedIds.push(entryId);
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'deleted_ids[]';
                        input.value = entryId;
                        document.getElementById('voucherForm').appendChild(input);
                    }
                    row.remove();
                    recalcAll();
                };
            });
        }

        // Extract global values (from first row)
        function getGlobalValues() {
            let firstRow = document.querySelector('#entriesBody tr');
            if (!firstRow) return {};
            let date = firstRow.querySelector('input[name$="[date]"]')?.value || '';
            let supplier = firstRow.querySelector('input[name$="[supplier]"]')?.value || '';
            return { date, supplier };
        }

        if (addEntryButton) {
            addEntryButton.addEventListener('click', function() {
                const uniqueKey = 'new_' + Date.now();
                const globals = getGlobalValues();

                // Get supplier id and title from the first entry
                let supplierId = globals.supplier || '';
                let supplierTitle = '';
                if (supplierId) {
                    // Try to get the title from the first row's Account Title select
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

                // Build the Account Title select HTML with the correct option selected
                let accountSelectHtml = `<select class="form-control select2" disabled>`;
                accountSelectHtml += `<option value="">Select</option>`;
                @foreach ($accounts->where('level2_id', 7) as $account)
                    accountSelectHtml += `<option value="{{ $account->id }}"` + (supplierId == '{{ $account->id }}' ? ' selected' : '') + `>{{ $account->title }}</option>`;
                @endforeach
                accountSelectHtml += `</select>`;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>New</td>
                    <td>
                        <input type="date" class="form-control" value="${globals.date}" readonly>
                        <input type="hidden" name="entries[${uniqueKey}][date]" value="${globals.date}">
                    </td>
                    <td>
                        <select name="entries[${uniqueKey}][product]" class="form-control select2 rate-select" data-rate-input="rate_${uniqueKey}">
                            <option value="">Select</option>
                            @foreach ($product as $prod)
                                <option value="{{ $prod->id }}" data-rate="{{ $prod->sale_rate ?? $prod->rate ?? 0 }}">{{ $prod->prod_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        ${accountSelectHtml}
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
                    <td><input type="text" name="entries[${uniqueKey}][batchNo]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="entries[${uniqueKey}][total]" class="form-control entry-total" readonly></td>
                    <td><input type="number" step="0.01" name="entries[${uniqueKey}][amount]" class="form-control entry-amount" value="0" readonly></td>
                    <td><input type="number" name="entries[${uniqueKey}][freight]" class="form-control" value="0"></td>
                    <td><input type="number" step="0.01" name="entries[${uniqueKey}][rate]" class="form-control entry-rate" id="rate_${uniqueKey}" value="0" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm delete-entry">Delete</button></td>
                `;
                entriesTable.appendChild(newRow);
                initializeDeleteButtons();
                $(newRow).find('.select2').select2();
            });
        }

        function recalcRowAmount(row) {
            const box = parseFloat($(row).find('input[name$="[box]"]').val()) || 0;
            const pack = parseFloat($(row).find('input[name$="[packing]"]').val()) || 0;
            const rate = parseFloat($(row).find('.entry-rate').val()) || 0;
            const total = box * pack;
            const amount = rate * total;
            $(row).find('.entry-total').val(total.toFixed(2));
            $(row).find('.entry-amount').val(amount.toFixed(2));
        }

        function recalcAll() {
            let grand = 0;
            $('#entriesBody tr').each(function() {
                recalcRowAmount(this);
                const amt = parseFloat($(this).find('.entry-amount').val()) || 0;
                grand += amt;
            });
            $('#grandTotal').text(grand.toFixed(2));
        }

        $('#entriesBody').on('input', 'input[name$="[box]"], input[name$="[packing]"], .entry-rate', function() {
            const row = $(this).closest('tr');
            recalcRowAmount(row);
            recalcAll();
        });

        initializeDeleteButtons();
        recalcAll();
    });

    $(document).ready(function () {
        $('.select2').select2();
    });

    $(document).on('change', '.rate-select', function() {
        var rate = $(this).find('option:selected').data('rate') || 0;
        var rateInputId = $(this).data('rate-input');
        $('#' + rateInputId).val(rate).trigger('input');
    });

    $(document).on('change', '#voucherDate', function(){
        var val = $(this).val();
        if (!val) return;
        $('#entriesBody input[name$="[date]"]').each(function(){
            $(this).val(val);
        });
    });
    </script>
@endsection
