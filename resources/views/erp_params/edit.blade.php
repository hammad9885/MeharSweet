@extends('layouts.app')

@section('content')
    <div class="container-fluid pt-4">
        <div class="row">
            <div class="col-12">
                <h4 class="page-title">Edit Erp Param</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-none">
                    <div class="card-body">
                        <form action="{{ route('erp_param.update', $erpParam->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bankLevel" class="form-label">Bank Level</label>
                                        <select id="bankLevel" class="form-control select2" name="bank_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ $erpParam->bank_level == $level2->id ? 'selected' : '' }}>
                                                    {{ $level2->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cashLevel" class="form-label">Cash Level</label>
                                        <select id="cashLevel" class="form-control select2" name="cash_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ $erpParam->cash_level == $level2->id ? 'selected' : '' }}>
                                                    {{ $level2->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="mb-3">
                                        <label for="customer_level" class="form-label">Customer Level</label>
                                        <select id="customer_level" class="form-control select2" name="customer_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ $erpParam->customer_level == $level2->id ? 'selected' : '' }}>
                                                    {{ $level2->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contractor_level" class="form-label">Contractor Level</label>
                                        <select id="contractor_level" class="form-control select2" name="contractor_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ $erpParam->contractor_level == $level2->id ? 'selected' : '' }}>
                                                    {{ $level2->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="employee_level" class="form-label">Employee Level</label>
                                        <select id="employee_level" class="form-control select2" name="employee_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ $erpParam->employee_level == $level2->id ? 'selected' : '' }}>
                                                    {{ $level2->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="supplier_level" class="form-label">Supplier Level</label>
                                        <select id="supplier_level" class="form-control select2" name="supplier_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ $erpParam->supplier->id == $level2->id ? 'selected' : '' }}>
                                                    {{ $level2->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="purchaseAccount" class="form-label">Stock Account</label>
                                        <select id="purchaseAccount" class="form-control select2" name="purchase_account">
                                            <option value="">Select Account</option>
                                            @foreach ($accountMasters as $accountMaster)
                                                <option value="{{ $accountMaster->id }}"
                                                    {{ $erpParam->purchase_account == $accountMaster->id ? 'selected' : '' }}>
                                                    {{ $accountMaster->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="saleAc" class="form-label">Sale Account</label>
                                        <select id="saleAc" class="form-control select2" name="sale_ac">
                                            <option value="">Select Account</option>
                                            @foreach ($accountMasters as $accountMaster)
                                                <option value="{{ $accountMaster->id }}"
                                                    {{ $erpParam->sale_ac == $accountMaster->id ? 'selected' : '' }}>
                                                    {{ $accountMaster->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cashAccount" class="form-label">Cash Acount</label>
                                        <select id="cashAccount" class="form-control select2" name="cash_acc">
                                            <option value="">Select Account</option>
                                            @foreach ($accountMasters as $accountMaster)
                                                <option value="{{ $accountMaster->id }}"
                                                    {{ $erpParam->cash_acc == $accountMaster->id ? 'selected' : '' }}>
                                                    {{ $accountMaster->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="capitalAccount" class="form-label">Capital Account</label>
                                        <select id="capitalAccount" class="form-control select2" name="capital_account">
                                            <option value="">Select Account</option>
                                            @foreach ($accountMasters as $accountMaster)
                                                <option value="{{ $accountMaster->id }}"
                                                    {{ $erpParam->capital_account == $accountMaster->id ? 'selected' : '' }}>
                                                    {{ $accountMaster->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Include jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
        $(document).ready(function() {

            $('#bankLevel').select2({
                width: '100%'
            });
            $('#cashLevel').select2({
                width: '100%'
            });
            $('#employeeLevel').select2({
                width: '100%'
            });
            $('#salary_level').select2({
                width: '100%'
            });
            $('#supplierLevel').select2({
                width: '100%'
            });
            $('#purchaseAccount').select2({
                width: '100%'
            });
            $('#purchaseReturnAccount').select2({
                width: '100%'
            });
            $('#saleAc').select2({
                width: '100%'
            });
            $('#employeeAdvance').select2({
                width: '100%'
            });
            $('#customer_advance').select2({
                width: '100%'
            });
            $('#pur_freight').select2({
                width: '100%'
            });
            $('#pur_freight_exp').select2({
                width: '100%'
            });
            $('#sale_freight').select2({
                width: '100%'
            });
            $('#sale_freight_exp').select2({
                width: '100%'
            });
            $('#cashAccount').select2({
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
        });
    </script>
@endsection
