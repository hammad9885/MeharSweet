@extends('layouts.app')

@section('content')
    <div class="container-fluid pt-4">
        <div class="row">
            <div class="col-12">
                <h4 class="page-title">Create Erp Param</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-none">
                    <div class="card-body">
                        <form action="{{ route('erp_param.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bankLevel" class="form-label">Bank Level</label>
                                        <select id="bankLevel" class="form-control select2" name="bank_level">
                                            <option value="">Select Account</option>
                                            @foreach ($level2s as $level2)
                                                <option value="{{ $level2->id }}"
                                                    {{ old('bank_level') == $level2->id ? 'selected' : '' }}>
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
                                                    {{ old('cash_level') == $level2->id ? 'selected' : '' }}>
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
                                                    {{ old('customer_level') == $level2->id ? 'selected' : '' }}>
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
                                                    {{ old('employee_level') == $level2->id ? 'selected' : '' }}>
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
                                                    {{ old('supplier_level') == $level2->id ? 'selected' : '' }}>
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
                                                    {{ old('purchase_account') == $accountMaster->id ? 'selected' : '' }}>
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
                                                    {{ old('sale_ac') == $accountMaster->id ? 'selected' : '' }}>
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
                                                    {{ old('cash_acc') == $accountMaster->id ? 'selected' : '' }}>
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
                                                    {{ old('capital_account') == $accountMaster->id ? 'selected' : '' }}>
                                                    {{ $accountMaster->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Create</button>
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
            $('#customer_level').select2({
                width: '100%'
            });
            $('#employee_level').select2({
                width: '100%'
            });
            $('#supplier_level').select2({
                width: '100%'
            });
            $('#purchaseAccount').select2({
                width: '100%'
            });
            $('#saleAc').select2({
                width: '100%'
            });
            $('#cashAccount').select2({
                width: '100%'
            });
            $('#capitalAccount').select2({
                width: '100%'
            });
        });
    </script>
@endsection
