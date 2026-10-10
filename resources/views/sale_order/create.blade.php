@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Sale</a></li>
                        <li class="breadcrumb-item active">Create Sale Order</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Sale Order</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('sale_order.store') }}" method="POST" id="saleOrderForm">
        @csrf

        <div class="row g-3 mb-3 align-items-end">
            <div class="col-md-4">
                <label for="customer_id" class="form-label fw-bold">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control select2" required>
                    <option value="">-- Select Customer --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->acc_id }}" data-transport="{{ $customer->company_title ?? '' }}">{{ $customer->name }} @if($customer->urdu_title) - {{ $customer->urdu_title }} @endif</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="v_date" class="form-label fw-bold">Date</label>
                <input type="date" name="v_date" id="v_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Prepared By</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
            </div>
            <div class="col-md-2">
                <label for="ref_name_id" class="form-label fw-bold">Party Reference</label>
                <select name="ref_name_id" id="ref_name_id" class="form-control custom-select2" data-custom-select2="true">
                    <option value="">-- Select Reference --</option>
                    @foreach ($referenceNames as $rn)
                        <option value="{{ $rn->id }}" 
                                data-search="{{ $rn->search_terms }}" 
                                data-name-en="{{ $rn->en_name }}" 
                                data-name-urdu="{{ $rn->urdu_name }}" 
                                {{ old('ref_name_id') == $rn->id ? 'selected' : '' }}>
                            {{ $rn->urdu_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="transport_company_id" class="form-label fw-bold">Transport Company</label>
                <select name="transport_company_id" id="transport_company_id" class="form-control select2">
                    <option value="">-- Select Transport Company --</option>
                    @foreach ($transportCompanies as $tc)
                        <option value="{{ $tc->id }}">{{ $tc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="bilty_no" class="form-label fw-bold">Bilty No</label>
                <input type="text" name="bilty_no" id="bilty_no" class="form-control" placeholder="e.g. BL-001">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Sale Orders</h5>
                    </div>
                    <div class="card-body p-2" id="customerOrdersContainer">
                        <p class="text-muted mb-0">Select a customer to see their sale orders.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3 mb-3">
            <button type="submit" class="btn btn-success">Save Sale Order</button>
        </div>

        <div id="itemTable">
            {{-- @php
                $demoProducts = $products->take(3)->values();
                $demoQuantities = [5, 3, 2];
            @endphp --}}
            @for ($i = 0; $i < 15; $i++)
            <div class="item-row border rounded px-3 py-1 mb-1 bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label">Product</label>
                        <select name="items[{{ $i }}][item_id]" class="form-control item-select select2">
                            <option value="">-- Select Finished Product --</option>
                            @foreach ($products as $product)
                                @php
                                    $isDemo = $i < 3 && isset($demoProducts[$i]) && $demoProducts[$i]->id == $product->id;
                                @endphp
                                <option value="{{ $product->id }}" {{ $isDemo ? 'selected' : '' }}>{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" name="items[{{ $i }}][qty]" class="form-control qty" step="0.01" value="{{ $i < 3 && isset($demoQuantities[$i]) ? $demoQuantities[$i] : '' }}">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-danger btn-sm mt-4 remove-row" tabindex="-1">X</button>
                    </div>
                </div>
            </div>
            @endfor
            <div class="border-top pt-2 mt-0 mb-0 bg-white total-row">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <strong>Total</strong>
                    </div>
                    <div class="col-md-2">
                        <strong id="totalQty">0</strong>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button type="button" id="addRow" class="btn btn-primary">+ Add Item</button>
            <button type="submit" class="btn btn-success">Save Sale Order</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single { height: calc(1.5em + 0.9rem + 2px); border: 1px solid #dee2e6; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + 0.9rem); padding-left: 0.9rem; }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function urduToEnglish(text) {
    if (!text) return '';
    var wordMap = {
        'شیراز': 'shiraz sheeraz', 'جہانگیرہ': 'jahangira jahangirah', 'عمر': 'umar omer omar',
        'ٹوائز': 'toys', 'ٹوایز': 'toys', 'رحیم': 'rahim', 'یار': 'yar',
        'خان': 'khan', 'خاں': 'khan', 'الحمد': 'alhamd al-hamd', 'ملتان': 'multan',
        'حاجی': 'haji', 'ندیم': 'nadeem nadim', 'چکوال': 'chakwal', 'محمود': 'mahmood mehmood mahmud',
        'اوکاڑہ': 'okara', 'افضل': 'afzal', 'دریا': 'darya', 'سلمان': 'salman',
        'صوابی': 'swabi swaby', 'مٹھو': 'mithu mitthu mithoo', 'اسامہ': 'usama osama', 'تلہ': 'tala',
        'گنگ': 'gang talagang tallagang', 'برکت': 'barkat', 'ڈیرہ': 'dera dg', 'غازی': 'ghazi dg',
        'اقبال': 'iqbal', 'خضرو': 'hazro khazro', 'ابراہیم': 'ibrahim', 'بٹ': 'bat but',
        'خیلہ': 'khela batkhela', 'بلال': 'bilal', 'قریشی': 'qureshi kureshi', 'وسیم': 'waseem wasim',
        'چوک': 'chowk chouk', 'اعظم': 'azam', 'سعید': 'saeed said', 'شاہ': 'shah',
        'عالم': 'alam', 'الیاس': 'ilyas elias', 'الکمال': 'alkamal al kamal', 'مقصود': 'maqsood',
        'احمد': 'ahmed ahmad', 'ہڑپہ': 'harappa harapa', 'پیر': 'pir peer', 'نواب': 'nawab',
        'علی': 'ali', 'بنوں': 'bannu', 'نوید': 'naveed navid', 'قدیر': 'qadeer qadir kadeer',
        'طارق': 'tariq tarik tareeq', 'قاسم': 'qasim kasim', 'حمزہ': 'hamza humza', 'خرم': 'khurram khuram',
        'خود': 'khud self', 'منور': 'munawar munawwar', 'گوشی': 'goshi', 'ارسلان': 'arslan arsalan',
        'یوسف': 'yousaf yousuf yusuf', 'عنصر': 'anser ansar', 'احسان': 'ahsan ehsan', 'لاڑکانہ': 'larkana',
        'لاہور': 'lahore', 'کراچی': 'karachi', 'راولپنڈی': 'rawalpindi pindi', 'اسلام': 'islam',
        'آباد': 'abad islamabad', 'پشاور': 'peshawar', 'فیصل': 'faisal'
    };

    var charMap = {
        'آ': 'a', 'ا': 'a', 'ب': 'b', 'پ': 'p', 'ت': 't', 'ٹ': 't',
        'ث': 's', 'ج': 'j', 'چ': 'ch', 'ح': 'h', 'خ': 'kh', 'د': 'd',
        'ڈ': 'd', 'ذ': 'z', 'ر': 'r', 'ڑ': 'r', 'ز': 'z', 'ژ': 'zh',
        'س': 's', 'ش': 'sh', 'ص': 's', 'ض': 'z', 'ط': 't', 'ظ': 'z',
        'ع': 'a', 'غ': 'gh', 'ف': 'f', 'ق': 'q', 'ک': 'k', 'گ': 'g',
        'ل': 'l', 'م': 'm', 'ن': 'n', 'ں': 'n', 'و': 'o', 'ہ': 'h',
        'ۂ': 'h', 'ۃ': 't', 'ھ': 'h', 'ی': 'i', 'ے': 'e', 'ئ': 'i'
    };

    var output = text;
    for (var w in wordMap) {
        if (wordMap.hasOwnProperty(w)) {
            output = output.split(w).join(' ' + wordMap[w] + ' ');
        }
    }

    var phonetic = '';
    for (var i = 0; i < text.length; i++) {
        var ch = text[i];
        phonetic += (charMap[ch] !== undefined ? charMap[ch] : ch);
    }

    return (output + ' ' + phonetic).toLowerCase();
}

function select2SearchMatcher(params, data) {
    if ($.trim(params.term) === '') {
        return data;
    }
    if (!data.text && !data.element) {
        return null;
    }

    if (data.children && data.children.length > 0) {
        var match = $.extend(true, {}, data);
        for (var c = data.children.length - 1; c >= 0; c--) {
            var child = data.children[c];
            var matches = select2SearchMatcher(params, child);
            if (!matches) {
                match.children.splice(c, 1);
            }
        }
        if (match.children.length > 0) {
            return match;
        }
        return select2SearchMatcher(params, match);
    }

    var term = params.term.toLowerCase().trim();
    var text = (data.text || '').toLowerCase().trim();
    var searchAttr = '';
    var enName = '';
    var urduAttr = '';

    if (data.element) {
        searchAttr = (data.element.getAttribute('data-search') || '').toLowerCase().trim();
        enName = (data.element.getAttribute('data-name-en') || '').toLowerCase().trim();
        urduAttr = (data.element.getAttribute('data-name-urdu') || '').toLowerCase().trim();
    } else if (data.search) {
        searchAttr = data.search.toString().toLowerCase().trim();
    }

    var transliterated = urduToEnglish(text + ' ' + urduAttr);
    var pool = (text + ' ' + urduAttr + ' ' + searchAttr + ' ' + enName + ' ' + transliterated).toLowerCase();

    if (pool.indexOf(term) > -1) {
        return data;
    }

    var words = term.split(/\s+/).filter(Boolean);
    if (words.length > 1) {
        var allMatch = words.every(function(w) {
            return pool.indexOf(w) > -1;
        });
        if (allMatch) {
            return data;
        }
    }

    return null;
}

function initPartyReferenceSelect2() {
    var $el = $('#ref_name_id');
    if (!$el.length) return;

    if ($el.data('select2')) {
        $el.select2('destroy');
    }

    $el.select2({
        placeholder: "-- Select Reference --",
        allowClear: true,
        width: '100%',
        matcher: select2SearchMatcher
    });
}

$(document).ready(function() {
    $('.select2:not(#ref_name_id)').select2({ placeholder: "-- Select --", width: '100%', matcher: select2SearchMatcher });
    initPartyReferenceSelect2();
    setTimeout(initPartyReferenceSelect2, 200);
    setTimeout(initPartyReferenceSelect2, 600);

    updateTotalQty();

    function initSelect2(container) {
        container.find('.select2:not(#ref_name_id)').each(function() {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });
        container.find('.select2:not(#ref_name_id)').select2({ placeholder: "-- Select --", width: '100%', matcher: select2SearchMatcher });
    }

    $('#customer_id').on('change', function() {
        const cid = $(this).val();

        // Default Transport Company to the selected customer's transport company (by name)
        const transportName = $('#customer_id option:selected').data('transport') || '';
        $('#transport_company_id').val('');
        if (transportName) {
            $('#transport_company_id option').each(function () {
                if ($(this).text().trim().toLowerCase() === String(transportName).trim().toLowerCase()) {
                    $('#transport_company_id').val($(this).val());
                    return false;
                }
            });
        }
        $('#transport_company_id').trigger('change');

        const $container = $('#customerOrdersContainer');
        if (!cid) {
            $container.html('<p class="text-muted mb-0">Select a customer to see their sale orders.</p>');
            return;
        }
        $container.html('<p class="text-muted mb-0">Loading...</p>');
        $.get('{{ route("sale_order.customer_orders", "") }}/' + cid, function(orders) {
            if (!orders.length) {
                $container.html('<p class="text-muted mb-0">No previous sale orders for this customer.</p>');
                return;
            }
            let html = '<table class="table table-sm table-bordered mb-0"><thead><tr><th>#</th><th>Voucher</th><th>Date</th><th>Party Ref</th><th>Items</th></tr></thead><tbody>';
            $.each(orders, function(i, o) {
                html += '<tr>' +
                    '<td>' + (i+1) + '</td>' +
                    '<td>' + o.v_no + '</td>' +
                    '<td>' + o.v_date + '</td>' +
                    '<td>' + (o.party_ref || '-') + '</td>' +
                    '<td>' + o.items + '</td>' +
                    '</tr>';
            });
            html += '</tbody></table>';
            $container.html(html);
        });
    });

    function updateTotalQty() {
        var total = 0;
        $('.qty').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalQty').text(total);
    }

    $(document).on('input', '.qty', updateTotalQty);

    $('#addRow').click(function() {
        const container = $('#itemTable');
        const index = container.find('.item-row').not('.total-row').length;
        const newRow = $(`
            <div class="item-row border rounded px-3 py-1 mb-3 bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label">Product (Finished)</label>
                        <select name="items[${index}][item_id]" class="form-control item-select select2">
                            <option value="">-- Select Finished Product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" name="items[${index}][qty]" class="form-control qty" step="0.01">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-danger btn-sm mt-4 remove-row" tabindex="-1">X</button>
                    </div>
                </div>
            </div>`);
        container.append(newRow);
        initSelect2(newRow);
        newRow.find('.item-select').select2('open');
    });

    $(document).on('click', '.remove-row', function() {
        const $row = $(this).closest('.item-row');
        const $nextRow = $row.next('.item-row');
        const $prevRow = $row.prev('.item-row');
        $row.remove();
        if ($nextRow.length) {
            $nextRow.find('.item-select').select2('focus');
        } else if ($prevRow.length) {
            $prevRow.find('.qty').focus();
        } else {
            $('#addRow').focus();
        }
    });

    $(document).on('keydown', '.remove-row', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });

    $(document).on('select2:open', function(e) {
        const $sel = $(e.target);
        setTimeout(function() {
            const $search = $sel.data('select2').dropdown.$search;
            if ($search.length) {
                $search.focus();
            }
        }, 100);
    });

    $(document).on('focus', '.select2-selection--single', function(e) {
        const $select = $(this).closest('.select2-container').siblings('select.item-select');
        if ($select.length && !$select.data('select2').isOpen()) {
            $select.select2('open');
        }
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'F9') {
            e.preventDefault();
            $('#saleOrderForm').submit();
        }
    });
});
</script>
@endsection
