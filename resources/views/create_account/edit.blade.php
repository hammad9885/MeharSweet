@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Account</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="voucherForm" action="{{ route('create_account.update', $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ old('name', $user->name) }}">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" minlength="8">
                                <button class="btn btn-outline-secondary" type="button"
                                    id="togglePassword">Show</button>
                            </div>
                            <div id="passwordHelp" class="form-text">Leave empty if you do not wish to change the
                                password.</div>
                        </div>

                        <div class="mb-3">
                            <label for="conformpassword" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="conformpassword" name="password_confirmation"
                                    class="form-control">
                                <button class="btn btn-outline-secondary" type="button"
                                    id="toggleConfirmPassword">Show</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Select Role</label>
                            <select name="role" class="form-control select2" data-toggle="select2" id="role">
                                <option value="">Select</option>
                                <option value="admin" {{ old('role', $user->is_admin) == '1' ? 'selected' : '' }}>Admin
                                </option>
                                <option value="user" {{ old('role', $user->is_admin) == '0' ? 'selected' : '' }}>User
                                </option>
                            </select>
                        </div>

                        <div id="matchMessage" class="form-text"></div>

                        <div class="card p-3 mt-3" id="checkboxContainer"
                            style="{{ old('role', $user->is_admin) == '0' ? 'display: block;' : 'display: none;' }}">
                            <h5 class="mb-3">Select Navigation Option</h5>

                            @php
                            use App\Models\Right; // Add this at the top

                            // Get user permissions from database
                            $userPermissions = [];
                            $rights = Right::where('user_id', $user->id)->get();
                            foreach ($rights as $right) {
                            $userPermissions[$right->app_name] = [
                            'add' => $right->add,
                            'edit' => $right->edit,
                            'del' => $right->del
                            ];
                            }

                            $navigationOptions = $user->navigation_options ? json_decode($user->navigation_options,
                            true) : [];
                            @endphp
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="account" name="account" value="1" {{
                                    old('account', $user->account) ? 'checked' : '' }}>
                                <label class="form-check-label" for="account">Account</label>
                            </div>
                            <hr>



                            <div id="account-options"
                                style="display: {{ in_array('Account', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <!-- Level 1 -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="level1" name="permissions[0][level]" value="Level1" {{
                                        isset($userPermissions['Level1']) ? 'checked' : '' }}>
                                    <label for="level1">Level 1</label>
                                </div>

                                <div id="level1-options"
                                    style="display: {{ isset($userPermissions['Level1']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-level1" name="permissions[0][add]" value="1" {{
                                        $userPermissions['Level1']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-level1">Add</label><br>
                                    <input type="checkbox" id="edit-level1" name="permissions[0][edit]" value="1" {{
                                        $userPermissions['Level1']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-level1">Edit</label><br>
                                    <input type="checkbox" id="del-level1" name="permissions[0][del]" value="1" {{
                                        $userPermissions['Level1']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-level1">Delete</label><br>
                                </div>

                                <!-- Level 2 -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="level2" name="permissions[1][level]" value="Level2" {{
                                        isset($userPermissions['Level2']) ? 'checked' : '' }}>
                                    <label for="level2">Level 2</label>
                                </div>
                                <div id="level2-options"
                                    style="display: {{ isset($userPermissions['Level2']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-level2" name="permissions[1][add]" value="1" {{
                                        $userPermissions['Level2']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-level2">Add</label><br>
                                    <input type="checkbox" id="edit-level2" name="permissions[1][edit]" value="1" {{
                                        $userPermissions['Level2']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-level2">Edit</label><br>
                                    <input type="checkbox" id="del-level2" name="permissions[1][del]" value="1" {{
                                        $userPermissions['Level2']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-level2">Delete</label><br>
                                </div>

                                <!-- Chart of Account -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="chartOfAccount" name="permissions[2][level]"
                                        value="ChartOfAccount" {{ isset($userPermissions['ChartOfAccount']) ? 'checked'
                                        : '' }}>
                                    <label for="chartOfAccount">Chart of Account</label>
                                </div>
                                <div id="chartOfAccount-options"
                                    style="display: {{ isset($userPermissions['ChartOfAccount']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-chart" name="permissions[2][add]" value="1" {{
                                        $userPermissions['ChartOfAccount']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-chart">Add</label><br>
                                    <input type="checkbox" id="edit-chart" name="permissions[2][edit]" value="1" {{
                                        $userPermissions['ChartOfAccount']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-chart">Edit</label><br>
                                    <input type="checkbox" id="del-chart" name="permissions[2][del]" value="1" {{
                                        $userPermissions['ChartOfAccount']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-chart">Delete</label><br>
                                </div>

                                <!-- Cash Receipt -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="cashReceipt" name="permissions[3][level]"
                                        value="CashReceipt" {{ isset($userPermissions['CashReceipt']) ? 'checked' : ''
                                        }}>
                                    <label for="cashReceipt">Cash Receipt</label>
                                </div>
                                <div id="cashReceipt-options"
                                    style="display: {{ isset($userPermissions['CashReceipt']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-cash" name="permissions[3][add]" value="1" {{
                                        $userPermissions['CashReceipt']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-cash">Add</label><br>
                                    <input type="checkbox" id="edit-cash" name="permissions[3][edit]" value="1" {{
                                        $userPermissions['CashReceipt']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-cash">Edit</label><br>
                                    <input type="checkbox" id="del-cash" name="permissions[3][del]" value="1" {{
                                        $userPermissions['CashReceipt']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-cash">Delete</label><br>
                                </div>

                                <!-- Cheque Receipt -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="chequeReceipt" name="permissions[4][level]"
                                        value="ChequeReceipt" {{ isset($userPermissions['ChequeReceipt']) ? 'checked'
                                        : '' }}>
                                    <label for="chequeReceipt">Cheque Receipt</label>
                                </div>
                                <div id="chequeReceipt-options"
                                    style="display: {{ isset($userPermissions['ChequeReceipt']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-cheque" name="permissions[4][add]" value="1" {{
                                        $userPermissions['ChequeReceipt']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-cheque">Add</label><br>
                                    <input type="checkbox" id="edit-cheque" name="permissions[4][edit]" value="1" {{
                                        $userPermissions['ChequeReceipt']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-cheque">Edit</label><br>
                                    <input type="checkbox" id="del-cheque" name="permissions[4][del]" value="1" {{
                                        $userPermissions['ChequeReceipt']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-cheque">Delete</label><br>
                                </div>

                                <!-- Cash Payment -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="cashPayment" name="permissions[5][level]"
                                        value="CashPayment" {{ isset($userPermissions['CashPayment']) ? 'checked' : ''
                                        }}>
                                    <label for="cashPayment">Cash Payment</label>
                                </div>
                                <div id="cashPayment-options"
                                    style="display: {{ isset($userPermissions['CashPayment']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-cash-payment" name="permissions[5][add]" value="1" {{
                                        $userPermissions['CashPayment']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-cash-payment">Add</label><br>
                                    <input type="checkbox" id="edit-cash-payment" name="permissions[5][edit]" value="1"
                                        {{ $userPermissions['CashPayment']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-cash-payment">Edit</label><br>
                                    <input type="checkbox" id="del-cash-payment" name="permissions[5][del]" value="1" {{
                                        $userPermissions['CashPayment']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-cash-payment">Delete</label><br>
                                </div>

                                <!-- Bank Receipt -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="bankReceipt" name="permissions[6][level]"
                                        value="BankReceipt" {{ isset($userPermissions['BankReceipt']) ? 'checked' : ''
                                        }}>
                                    <label for="bankReceipt">Bank Receipt</label>
                                </div>
                                <div id="bankReceipt-options"
                                    style="display: {{ isset($userPermissions['BankReceipt']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-bank" name="permissions[6][add]" value="1" {{
                                        $userPermissions['BankReceipt']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-bank">Add</label><br>
                                    <input type="checkbox" id="edit-bank" name="permissions[6][edit]" value="1" {{
                                        $userPermissions['BankReceipt']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-bank">Edit</label><br>
                                    <input type="checkbox" id="del-bank" name="permissions[6][del]" value="1" {{
                                        $userPermissions['BankReceipt']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-bank">Delete</label><br>
                                </div>

                                <!-- Bank Payment -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="bankPayment" name="permissions[7][level]"
                                        value="BankPayment" {{ isset($userPermissions['BankPayment']) ? 'checked' : ''
                                        }}>
                                    <label for="bankPayment">Bank Payment</label>
                                </div>
                                <div id="bankPayment-options"
                                    style="display: {{ isset($userPermissions['BankPayment']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-bank-Payment" name="permissions[7][add]" value="1" {{
                                        $userPermissions['BankPayment']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-bank-Payment">Add</label><br>
                                    <input type="checkbox" id="edit-bank-Payment" name="permissions[7][edit]" value="1"
                                        {{ $userPermissions['BankPayment']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-bank-Payment">Edit</label><br>
                                    <input type="checkbox" id="del-bank-Payment" name="permissions[7][del]" value="1" {{
                                        $userPermissions['BankPayment']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-bank-Payment">Delete</label><br>
                                </div>

                                <!-- Ledger -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="ledger" name="permissions[8][level]" value="Ledger" {{
                                        isset($userPermissions['Ledger']) ? 'checked' : '' }}>
                                    <label for="ledger">Ledger</label>
                                </div>

                                <!-- Office Cash -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="officeCash" name="permissions[9][level]"
                                        value="officeCash" {{ isset($userPermissions['officeCash']) ? 'checked' : '' }}>
                                    <label for="officeCash">Office Cash</label>
                                </div>
                                <div id="officeCash-options"
                                    style="display: {{ isset($userPermissions['officeCash']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-officeCash" name="permissions[9][add]" value="1" {{
                                        $userPermissions['officeCash']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-officeCash">Add</label><br>
                                    <input type="checkbox" id="edit-officeCash" name="permissions[9][edit]" value="1" {{
                                        $userPermissions['officeCash']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-officeCash">Edit</label><br>
                                    <input type="checkbox" id="del-officeCash" name="permissions[9][del]" value="1" {{
                                        $userPermissions['officeCash']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-officeCash">Delete</label><br>
                                </div>

                                <!-- Payables -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="payables" name="permissions[10][level]" value="Payables"
                                        {{ isset($userPermissions['Payables']) ? 'checked' : '' }}>
                                    <label for="payables">Payables</label>
                                </div>
                                <div id="payables-options"
                                    style="display: {{ isset($userPermissions['Payables']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-payables" name="permissions[10][add]" value="1" {{
                                        $userPermissions['Payables']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-payables">Add</label><br>
                                    <input type="checkbox" id="edit-payables" name="permissions[10][edit]" value="1" {{
                                        $userPermissions['Payables']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-payables">Edit</label><br>
                                    <input type="checkbox" id="del-payables" name="permissions[10][del]" value="1" {{
                                        $userPermissions['Payables']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-payables">Delete</label><br>
                                </div>

                                <!-- Receivables -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="receivables" name="permissions[11][level]"
                                        value="Receivables" {{ isset($userPermissions['Receivables']) ? 'checked' : ''
                                        }}>
                                    <label for="receivables">Receivables</label>
                                </div>
                                <div id="receivables-options"
                                    style="display: {{ isset($userPermissions['Receivables']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-receivables" name="permissions[11][add]" value="1" {{
                                        $userPermissions['Receivables']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-receivables">Add</label><br>
                                    <input type="checkbox" id="edit-receivables" name="permissions[11][edit]" value="1"
                                        {{ $userPermissions['Receivables']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-receivables">Edit</label><br>
                                    <input type="checkbox" id="del-receivables" name="permissions[11][del]" value="1" {{
                                        $userPermissions['Receivables']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-receivables">Delete</label><br>
                                </div>

                                <!-- Journal Voucher -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="journalVoucher" name="permissions[12][level]"
                                        value="JournalVoucher" {{ isset($userPermissions['JournalVoucher']) ? 'checked'
                                        : '' }}>
                                    <label for="journalVoucher">Journal Voucher</label>
                                </div>
                                <div id="journalVoucher-options"
                                    style="display: {{ isset($userPermissions['JournalVoucher']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-journal" name="permissions[12][add]" value="1" {{
                                        $userPermissions['JournalVoucher']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-journal">Add</label><br>
                                    <input type="checkbox" id="edit-journal" name="permissions[12][edit]" value="1" {{
                                        $userPermissions['JournalVoucher']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-journal">Edit</label><br>
                                    <input type="checkbox" id="del-journal" name="permissions[12][del]" value="1" {{
                                        $userPermissions['JournalVoucher']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-journal">Delete</label><br>
                                </div>

                                <!-- Opening Balance -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="openingBalance" name="permissions[13][level]"
                                        value="OpeningBalance" {{ isset($userPermissions['OpeningBalance']) ? 'checked'
                                        : '' }}>
                                    <label for="openingBalance">Opening Balance</label>
                                </div>
                                <div id="openingBalance-options"
                                    style="display: {{ isset($userPermissions['OpeningBalance']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-opening" name="permissions[13][add]" value="1" {{
                                        $userPermissions['OpeningBalance']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-opening">Add</label><br>
                                    <input type="checkbox" id="edit-opening" name="permissions[13][edit]" value="1" {{
                                        $userPermissions['OpeningBalance']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-opening">Edit</label><br>
                                    <input type="checkbox" id="del-opening" name="permissions[13][del]" value="1" {{
                                        $userPermissions['OpeningBalance']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-opening">Delete</label><br>
                                </div>
                            </div>
                            <!-- Reports Section -->
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="report" name="report" value="1" {{
                                    old('report', $user->report) ? 'checked' : '' }}>
                                <label class="form-check-label" for="report">Reports</label>
                            </div>
                            <hr>

                            <div id="report-options"
                                style="display: {{ in_array('Report', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="ExpenseReports" name="permissions[50][level]"
                                        value="ExpenseReports" {{ isset($userPermissions['ExpenseReports']) ? 'checked'
                                        : '' }}>
                                    <label for="ExpenseReports">Expense Reports</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="PurchaseReports" name="permissions[33][level]"
                                        value="PurchaseReports" {{ isset($userPermissions['PurchaseReports'])
                                        ? 'checked' : '' }}>
                                    <label for="PurchaseReports">Purchase Reports</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="SaleReports" name="permissions[15][level]"
                                        value="SaleReports" {{ isset($userPermissions['SaleReports']) ? 'checked' : ''
                                        }}>
                                    <label for="SaleReports">Sale Reports</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="DailyStatement" name="permissions[16][level]"
                                        value="DailyStatement" {{ isset($userPermissions['DailyStatement']) ? 'checked'
                                        : '' }}>
                                    <label for="DailyStatement">Daily Statement</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="StockReports" name="permissions[51][level]"
                                        value="StockReports" {{ isset($userPermissions['StockReports']) ? 'checked' : ''
                                        }}>
                                    <label for="StockReports">Stock Reports</label>
                                </div>
                            </div>

                            <!-- Billing Section -->
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="billing" name="billing" value="1" {{
                                    old('billing', $user->billing) ? 'checked' : '' }}>
                                <label class="form-check-label" for="billing">Billing</label>
                            </div>
                            <hr>

                            <div id="billing-options"
                                style="display: {{ in_array('Billing', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="pharmaceuticalbilling" name="permissions[17][level]"
                                        value="pharmaceuticalbilling" {{
                                        isset($userPermissions['pharmaceuticalbilling']) ? 'checked' : '' }}>
                                    <label for="pharmaceuticalbilling">Pharmaceutical Billing</label>
                                </div>
                                <div id="pharmaceuticalbilling-options"
                                    style="display: {{ isset($userPermissions['pharmaceuticalbilling']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-pharmaceutical" name="permissions[17][add]" value="1"
                                        {{ $userPermissions['pharmaceuticalbilling']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-pharmaceutical">Add</label><br>
                                    <input type="checkbox" id="edit-pharmaceutical" name="permissions[17][edit]"
                                        value="1" {{ $userPermissions['pharmaceuticalbilling']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-pharmaceutical">Edit</label><br>
                                    <input type="checkbox" id="del-pharmaceutical" name="permissions[17][del]" value="1"
                                        {{ $userPermissions['pharmaceuticalbilling']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-pharmaceutical">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="confectionerybilling" name="permissions[18][level]"
                                        value="confectionerybilling" {{ isset($userPermissions['confectionerybilling'])
                                        ? 'checked' : '' }}>
                                    <label for="confectionerybilling">Confectionery Billing</label>
                                </div>
                                <div id="confectionerybilling-options"
                                    style="display: {{ isset($userPermissions['confectionerybilling']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-confectionery" name="permissions[18][add]" value="1"
                                        {{ $userPermissions['confectionerybilling']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-confectionery">Add</label><br>
                                    <input type="checkbox" id="edit-confectionery" name="permissions[18][edit]"
                                        value="1" {{ $userPermissions['confectionerybilling']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-confectionery">Edit</label><br>
                                    <input type="checkbox" id="del-confectionery" name="permissions[18][del]" value="1"
                                        {{ $userPermissions['confectionerybilling']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-confectionery">Delete</label><br>
                                </div>





                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="generalbilling" name="permissions[14][level]"
                                        value="generalbilling" {{ isset($userPermissions['generalbilling']) ? 'checked'
                                        : '' }}>
                                    <label for="generalbilling">General Billing</label>
                                </div>
                                <div id="generalbilling-options"
                                    style="display: {{ isset($userPermissions['generalbilling']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-general" name="permissions[14][add]" value="1" {{
                                        $userPermissions['generalbilling']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-general">Add</label><br>
                                    <input type="checkbox" id="edit-general" name="permissions[14][edit]" value="1" {{
                                        $userPermissions['generalbilling']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-general">Edit</label><br>
                                    <input type="checkbox" id="del-general" name="permissions[14][del]" value="1" {{
                                        $userPermissions['generalbilling']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-general">Delete</label><br>
                                </div>


                            </div>



                            <hr>
                            <div class="form-check">
                                <!-- Remove the extra " after the checked condition -->
                                <input class="form-check-input" type="checkbox" id="wagecalculator"
                                    name="wage_calculator" value="1" {{ old('wagecalculator', $user->wage_calculator) ?
                                'checked' : '' }}>


                                <label class="form-check-label" for="wagecalculator">Calculator</label>
                            </div>
                            <hr>

                            <!-- Wage calculator -->
                            <div id="calculator-options"
                                style="display: {{ old('wagecalculator', $user->wage_calculator) ? 'block' : 'none' }};">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="boxboardcalculator" name="permissions[61][level]"
                                        value="boxboardcalculator" {{ isset($userPermissions['boxboardcalculator'])
                                        ? 'checked' : '' }}>
                                    <label for="boxboardcalculator">Boxboard</label>
                                </div>
                                <div id="boxboardcalculator-options"
                                    style="display: {{ isset($userPermissions['boxboardcalculator']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-boxboardcalculator" name="permissions[61][add]"
                                        value="1" {{ $userPermissions['boxboardcalculator']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-boxboardcalculator">Add</label><br>
                                    <input type="checkbox" id="edit-boxboardcalculator" name="permissions[61][edit]"
                                        value="1" {{ $userPermissions['boxboardcalculator']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-boxboardcalculator">Edit</label><br>
                                    <input type="checkbox" id="del-boxboardcalculator" name="permissions[61][del]"
                                        value="1" {{ $userPermissions['boxboardcalculator']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-boxboardcalculator">Delete</label><br>
                                </div>
                            </div>

                            <!-- Delivery Challan Section -->
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="deliveryChallan"
                                    name="deliveryChallan" value="1" {{ old('deliveryChallan', $user->delivery_challan)
                                ? 'checked' : '' }}>
                                <label class="form-check-label" for="deliveryChallan">Delivery Challan</label>
                            </div>
                            <hr>
                            <div id="delivery-options"
                                style="display: {{ in_array('Delivery Challen', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <!-- Pharmaceutical Delivery -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="pharmaceuticaldelivery" name="permissions[55][level]"
                                        value="pharmaceuticaldelivery" {{
                                        isset($userPermissions['pharmaceuticaldelivery']) ? 'checked' : '' }}>
                                    <label for="pharmaceuticaldelivery">Pharmaceutical Delivery</label>
                                </div>
                                <div id="pharmaceuticaldelivery-options"
                                    style="display: {{ isset($userPermissions['pharmaceuticaldelivery']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-pharmaceutical-delivery" name="permissions[55][add]"
                                        value="1" {{ $userPermissions['pharmaceuticaldelivery']['add'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="add-pharmaceutical-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-pharmaceutical-delivery"
                                        name="permissions[55][edit]" value="1" {{
                                        $userPermissions['pharmaceuticaldelivery']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-pharmaceutical-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-pharmaceutical-delivery" name="permissions[55][del]"
                                        value="1" {{ $userPermissions['pharmaceuticaldelivery']['del'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="del-pharmaceutical-delivery">Delete</label><br>
                                </div>

                                <!-- Confectionery Delivery -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="confectionerydelivery" name="permissions[56][level]"
                                        value="confectionerydelivery" {{
                                        isset($userPermissions['confectionerydelivery']) ? 'checked' : '' }}>
                                    <label for="confectionerydelivery">Confectionery Delivery</label>
                                </div>
                                <div id="confectionerydelivery-options"
                                    style="display: {{ isset($userPermissions['confectionerydelivery']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-confectionery-delivery" name="permissions[56][add]"
                                        value="1" {{ $userPermissions['confectionerydelivery']['add'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="add-confectionery-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-confectionery-delivery" name="permissions[56][edit]"
                                        value="1" {{ $userPermissions['confectionerydelivery']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-confectionery-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-confectionery-delivery" name="permissions[56][del]"
                                        value="1" {{ $userPermissions['confectionerydelivery']['del'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="del-confectionery-delivery">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="generaldelivery" name="permissions[54][level]"
                                        value="generaldelivery" {{ isset($userPermissions['generaldelivery'])
                                        ? 'checked' : '' }}>
                                    <label for="generaldelivery">General Delivery</label>
                                </div>
                                <div id="generaldelivery-options"
                                    style="display:  {{ isset($userPermissions['generaldelivery']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-general-delivery" name="permissions[54][add]"
                                        value="1" {{ $userPermissions['generaldelivery']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-general-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-general-delivery" name="permissions[54][edit]"
                                        value="1" {{ $userPermissions['generaldelivery']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-general-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-general-delivery" name="permissions[54][del]"
                                        value="1" {{ $userPermissions['generaldelivery']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-general-delivery">Delete</label><br>
                                </div>
                            </div>

                            <!-- Waste Sale Section -->
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="wasteSale" name="wasteSale"
                                    value="1" {{ old('wasteSale', $user->waste_sale) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wasteSale">Waste Sale</label>
                            </div>
                            <hr>

                            <div id="wasteSale-options"
                                style="display: {{ old('wasteSale', $user->waste_sale) ? 'block' : 'none' }}; margin-left: 25px;">
                                <div>
                                    <input type="checkbox" id="waste" name="permissions[57][level]" value="waste" {{
                                        isset($userPermissions['waste']) ? 'checked' : '' }}>
                                    <label for="waste">Waste Sale</label>
                                </div>
                                <div id="waste-options"
                                    style="display: {{ isset($userPermissions['waste']) ? 'block' : 'none' }}; margin-left: 25px;">
                                    <input type="checkbox" id="add-wasteSale-delivery" name="permissions[57][add]"
                                        value="1" {{ $userPermissions['waste']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-wasteSale-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-wasteSale-delivery" name="permissions[57][edit]"
                                        value="1" {{ $userPermissions['waste']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-wasteSale-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-wasteSale-delivery" name="permissions[57][del]"
                                        value="1" {{ $userPermissions['waste']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-wasteSale-delivery">Delete</label><br>
                                </div>
                            </div>

                            <!-- Gate Ex Section -->
                            <!-- Gate Ex Section -->
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gateEx" name="gateEx" value="1" {{
                                    old('gateEx', $user->gate_ex)
                                ? 'checked' : '' }}>
                                <label class="form-check-label" for="gateEx">Gate Ex</label>
                            </div>
                            <hr>

                            <div id="gateExx-options"
                                style="display: {{ old('GateEx', $user->gate_ex) ? 'block' : 'none' }}; margin-left: 25px; ">
                                <div>
                                    <input type="checkbox" id="gate" name="permissions[58][level]" value="gateEx" {{
                                        isset($userPermissions['gateEx']) ? 'checked' : '' }}>
                                    <label for="gate">Gate Ex</label>
                                </div>
                                <div id="gateEx-options"
                                    style="display: {{ isset($userPermissions['gateEx']) ? 'block' : 'none' }}; margin-left: 25px;">
                                    <input type="checkbox" id="add-gateEx-delivery" name="permissions[58][add]"
                                        value="1" {{ $userPermissions['gateEx']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-gateEx-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-gateEx-delivery" name="permissions[58][edit]"
                                        value="1" {{ $userPermissions['gateEx']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-gateEx-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-gateEx-delivery" name="permissions[58][del]"
                                        value="1" {{ $userPermissions['gateEx']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-gateEx-delivery">Delete</label><br>
                                </div>
                            </div>

                            <!-- Gate Pass Section -->
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gatePass" name="gatePass" value="1"
                                    {{ old('gatePass', $user->gate_pass) ? 'checked' : '' }}>
                                <label class="form-check-label" for="gatePass">Gate Pass</label>
                            </div>
                            <hr>

                            <div id="gatePass-options"
                                style="display: {{ in_array('Gate Pass', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="gatePassin" name="permissions[59][level]"
                                        value="gatePassin" {{ isset($userPermissions['gatePassin']) ? 'checked' : '' }}>
                                    <label for="gatePassin">Gate-Pass In</label>
                                </div>
                                <div id="gatePassin-options"
                                    style="display: {{ isset($userPermissions['gatePassin']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-gatePassin" name="permissions[59][add]" value="1" {{
                                        $userPermissions['gatePassin']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-gatePassin">Add</label><br>
                                    <input type="checkbox" id="edit-gatePassin" name="permissions[59][edit]" value="1"
                                        {{ $userPermissions['gatePassin']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-gatePassin">Edit</label><br>
                                    <input type="checkbox" id="del-gatePassin" name="permissions[59][del]" value="1" {{
                                        $userPermissions['gatePassin']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-gatePassin">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="gatePassout" name="permissions[60][level]"
                                        value="gatePassout" {{ isset($userPermissions['gatePassout']) ? 'checked' : ''
                                        }}>
                                    <label for="gatePassout">Gate-Pass Out</label>
                                </div>
                                <div id="gatePassout-options"
                                    style="display: {{ isset($userPermissions['gatePassout']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-gatePassout" name="permissions[60][add]" value="1" {{
                                        $userPermissions['gatePassout']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-gatePassout">Add</label><br>
                                    <input type="checkbox" id="edit-gatePassout" name="permissions[60][edit]" value="1"
                                        {{ $userPermissions['gatePassout']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-gatePassout">Edit</label><br>
                                    <input type="checkbox" id="del-gatePassout" name="permissions[60][del]" value="1" {{
                                        $userPermissions['gatePassout']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-gatePassout">Delete</label><br>
                                </div>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="purchase" name="purchase" value="1"
                                    {{ old('purchase', $user->purchase) ? 'checked' : '' }}>
                                <label class="form-check-label" for="purchase">Purchase</label>
                            </div>
                            <hr>

                            <!-- Purchase Options -->
                            <div id="purchase-options"
                                style="display: {{ in_array('Purchase', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <!-- Boxboard -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="boxboard" name="permissions[19][level]" value="Boxboard"
                                        {{ isset($userPermissions['Boxboard']) ? 'checked' : '' }}>
                                    <label for="boxboard">Boxboard</label>
                                </div>
                                <div id="boxboard-options"
                                    style="display: {{ isset($userPermissions['Boxboard']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-boxboard" name="permissions[19][add]" value="1" {{
                                        $userPermissions['Boxboard']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-boxboard">Add</label><br>
                                    <input type="checkbox" id="edit-boxboard" name="permissions[19][edit]" value="1" {{
                                        $userPermissions['Boxboard']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-boxboard">Edit</label><br>
                                    <input type="checkbox" id="del-boxboard" name="permissions[19][del]" value="1" {{
                                        $userPermissions['Boxboard']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-boxboard">Delete</label><br>
                                </div>

                                <!-- Purchase Return -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="purchaseReturn" name="permissions[20][level]"
                                        value="PurchaseReturn" {{ isset($userPermissions['PurchaseReturn']) ? 'checked'
                                        : '' }}>
                                    <label for="purchaseReturn">Purchase Return</label>
                                </div>
                                <div id="purchaseReturn-options"
                                    style="display: {{ isset($userPermissions['PurchaseReturn']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-purchaseReturn" name="permissions[20][add]" value="1"
                                        {{ $userPermissions['PurchaseReturn']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-purchaseReturn">Add</label><br>
                                    <input type="checkbox" id="edit-purchaseReturn" name="permissions[20][edit]"
                                        value="1" {{ $userPermissions['PurchaseReturn']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-purchaseReturn">Edit</label><br>
                                    <input type="checkbox" id="del-purchaseReturn" name="permissions[20][del]" value="1"
                                        {{ $userPermissions['PurchaseReturn']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-purchaseReturn">Delete</label><br>
                                </div>

                                <!-- Purchase Plate -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="purchasePlate" name="permissions[21][level]"
                                        value="PurchasePlate" {{ isset($userPermissions['PurchasePlate']) ? 'checked'
                                        : '' }}>
                                    <label for="purchasePlate">Purchase Plate</label>
                                </div>
                                <div id="purchasePlate-options"
                                    style="display: {{ isset($userPermissions['PurchasePlate']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-purchasePlate" name="permissions[21][add]" value="1"
                                        {{ $userPermissions['PurchasePlate']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-purchasePlate">Add</label><br>
                                    <input type="checkbox" id="edit-purchasePlate" name="permissions[21][edit]"
                                        value="1" {{ $userPermissions['PurchasePlate']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-purchasePlate">Edit</label><br>
                                    <input type="checkbox" id="del-purchasePlate" name="permissions[21][del]" value="1"
                                        {{ $userPermissions['PurchasePlate']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-purchasePlate">Delete</label><br>
                                </div>

                                <!-- Glue Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="gluePurchase" name="permissions[22][level]"
                                        value="GluePurchase" {{ isset($userPermissions['GluePurchase']) ? 'checked' : ''
                                        }}>
                                    <label for="gluePurchase">Glue Purchase</label>
                                </div>
                                <div id="gluePurchase-options"
                                    style="display: {{ isset($userPermissions['GluePurchase']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-gluePurchase" name="permissions[22][add]" value="1"
                                        {{ $userPermissions['GluePurchase']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-gluePurchase">Add</label><br>
                                    <input type="checkbox" id="edit-gluePurchase" name="permissions[22][edit]" value="1"
                                        {{ $userPermissions['GluePurchase']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-gluePurchase">Edit</label><br>
                                    <input type="checkbox" id="del-gluePurchase" name="permissions[22][del]" value="1"
                                        {{ $userPermissions['GluePurchase']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-gluePurchase">Delete</label><br>
                                </div>

                                <!-- Ink Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="inkPurchase" name="permissions[23][level]"
                                        value="InkPurchase" {{ isset($userPermissions['InkPurchase']) ? 'checked' : ''
                                        }}>
                                    <label for="inkPurchase">Ink Purchase</label>
                                </div>
                                <div id="inkPurchase-options"
                                    style="display: {{ isset($userPermissions['InkPurchase']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-inkPurchase" name="permissions[23][add]" value="1" {{
                                        $userPermissions['InkPurchase']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-inkPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-inkPurchase" name="permissions[23][edit]" value="1"
                                        {{ $userPermissions['InkPurchase']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-inkPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-inkPurchase" name="permissions[23][del]" value="1" {{
                                        $userPermissions['InkPurchase']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-inkPurchase">Delete</label><br>
                                </div>

                                <!-- Lamination Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="laminationPurchase" name="permissions[24][level]"
                                        value="LaminationPurchase" {{ isset($userPermissions['LaminationPurchase'])
                                        ? 'checked' : '' }}>
                                    <label for="laminationPurchase">Lamination Purchase</label>
                                </div>
                                <div id="laminationPurchase-options"
                                    style="display: {{ isset($userPermissions['LaminationPurchase']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-laminationPurchase" name="permissions[24][add]"
                                        value="1" {{ $userPermissions['LaminationPurchase']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-laminationPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-laminationPurchase" name="permissions[24][edit]"
                                        value="1" {{ $userPermissions['LaminationPurchase']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-laminationPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-laminationPurchase" name="permissions[24][del]"
                                        value="1" {{ $userPermissions['LaminationPurchase']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-laminationPurchase">Delete</label><br>
                                </div>

                                <!-- Corrugation Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="corrugationPurchase" name="permissions[25][level]"
                                        value="CorrugationPurchase" {{ isset($userPermissions['CorrugationPurchase'])
                                        ? 'checked' : '' }}>
                                    <label for="corrugationPurchase">Corrugation Purchase</label>
                                </div>
                                <div id="corrugationPurchase-options"
                                    style="display: {{ isset($userPermissions['CorrugationPurchase']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-corrugationPurchase" name="permissions[25][add]"
                                        value="1" {{ $userPermissions['CorrugationPurchase']['add'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="add-corrugationPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-corrugationPurchase" name="permissions[25][edit]"
                                        value="1" {{ $userPermissions['CorrugationPurchase']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-corrugationPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-corrugationPurchase" name="permissions[25][del]"
                                        value="1" {{ $userPermissions['CorrugationPurchase']['del'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="del-corrugationPurchase">Delete</label><br>
                                </div>

                                <!-- Shipper Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="shipperPurchase" name="permissions[26][level]"
                                        value="ShipperPurchase" {{ isset($userPermissions['ShipperPurchase'])
                                        ? 'checked' : '' }}>
                                    <label for="shipperPurchase">Shipper Purchase</label>
                                </div>
                                <div id="shipperPurchase-options"
                                    style="display: {{ isset($userPermissions['ShipperPurchase']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-shipperPurchase" name="permissions[26][add]"
                                        value="1" {{ $userPermissions['ShipperPurchase']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-shipperPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-shipperPurchase" name="permissions[26][edit]"
                                        value="1" {{ $userPermissions['ShipperPurchase']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-shipperPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-shipperPurchase" name="permissions[26][del]"
                                        value="1" {{ $userPermissions['ShipperPurchase']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-shipperPurchase">Delete</label><br>
                                </div>



                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="dyePurchase" name="permissions[52][level]"
                                        value="dyePurchase" {{ isset($userPermissions['dyePurchase']) ? 'checked' : ''
                                        }}>
                                    <label for="dyePurchase">Dye Purchase</label>
                                </div>
                                <div id="dyePurchase-options"
                                    style="display: {{ isset($userPermissions['dyePurchase']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-dyePurchase" name="permissions[52][add]" value="1" {{
                                        $userPermissions['dyePurchase']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-dyePurchase">Add</label><br>
                                    <input type="checkbox" id="edit-dyePurchase" name="permissions[52][edit]" value="1"
                                        {{ $userPermissions['dyePurchase']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-dyePurchase">Edit</label><br>
                                    <input type="checkbox" id="del-dyePurchase" name="permissions[52][del]" value="1" {{
                                        $userPermissions['dyePurchase']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-dyePurchase">Delete</label><br>
                                </div>


                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="inventory" name="inventory"
                                    value="1" {{ old('inventory', $user->inventory) ? 'checked' : '' }}>
                                <label class="form-check-label" for="inventory">Inventory</label>
                            </div>
                            <hr>

                            <!-- Inventory Options -->
                            <div id="inventory-options"
                                style="display: {{ in_array('Inventory', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <!-- Item Type -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="itemType" name="permissions[27][level]" value="ItemType"
                                        {{ isset($userPermissions['ItemType']) ? 'checked' : '' }}>
                                    <label for="itemType">Item Type</label>
                                </div>
                                <div id="itemType-options"
                                    style="display: {{ isset($userPermissions['ItemType']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-itemType" name="permissions[27][add]" value="1" {{
                                        $userPermissions['ItemType']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-itemType">Add</label><br>
                                    <input type="checkbox" id="edit-itemType" name="permissions[27][edit]" value="1" {{
                                        $userPermissions['ItemType']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-itemType">Edit</label><br>
                                    <input type="checkbox" id="del-itemType" name="permissions[27][del]" value="1" {{
                                        $userPermissions['ItemType']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-itemType">Delete</label><br>
                                </div>

                                <!-- Item Registration -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="itemRegistration" name="permissions[28][level]"
                                        value="ItemRegistration" {{ isset($userPermissions['ItemRegistration'])
                                        ? 'checked' : '' }}>
                                    <label for="itemRegistration">Item Registration</label>
                                </div>
                                <div id="itemRegistration-options"
                                    style="display: {{ isset($userPermissions['ItemRegistration']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-itemRegistration" name="permissions[28][add]"
                                        value="1" {{ $userPermissions['ItemRegistration']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-itemRegistration">Add</label><br>
                                    <input type="checkbox" id="edit-itemRegistration" name="permissions[28][edit]"
                                        value="1" {{ $userPermissions['ItemRegistration']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-itemRegistration">Edit</label><br>
                                    <input type="checkbox" id="del-itemRegistration" name="permissions[28][del]"
                                        value="1" {{ $userPermissions['ItemRegistration']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-itemRegistration">Delete</label><br>
                                </div>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="productRegistration"
                                    name="productRegistration" value="1" {{ old('productRegistration',
                                    $user->product_registration) ? 'checked' : '' }}>
                                <label class="form-check-label" for="productRegistration">Product Registration</label>
                            </div>
                            <hr>

                            <div id="productRegistration-options"
                                style="display: {{ in_array('Product Registration', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }}; margin-left: 25px;">
                                <input type="checkbox" id="productRegistrations" name="permissions[34][level]"
                                    value="productRegistrations" {{ isset($userPermissions['productRegistrations'])
                                    ? 'checked' : '' }}>
                                <label for="productRegistrations">Product Registration</label>


                                <!-- Product Registration Options -->
                                <div id="productRegistration-sub-options"
                                    style="display: {{ isset($userPermissions['productRegistrations']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-productRegistration" name="permissions[34][add]"
                                        value="1" {{ $userPermissions['productRegistrations']['add'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="add-productRegistration">Add</label><br>
                                    <input type="checkbox" id="edit-productRegistration" name="permissions[34][edit]"
                                        value="1" {{ $userPermissions['productRegistrations']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="edit-productRegistration">Edit</label><br>
                                    <input type="checkbox" id="del-productRegistration" name="permissions[34][del]"
                                        value="1" {{ $userPermissions['productRegistrations']['del'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="del-productRegistration">Delete</label><br>
                                </div>
                            </div>

                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="jobSheet" name="job_sheet" value="1"
                                    {{ old('job_sheet', $user->job_sheet) ? 'checked' : '' }}>
                                <label class="form-check-label" for="jobSheet">Job Sheet</label>
                            </div>
                            <hr>

                            <!-- Job Sheet Section -->
                            <div id="jobSheet-options"
                                style="display: {{ in_array('job_sheet', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="job" name="permissions[42][level]" value="jobSheet" {{
                                        isset($userPermissions['jobSheet']) ? 'checked' : '' }}>
                                    <label for="job">Job Sheet</label>
                                </div>
                                <div id="job-options"
                                    style="display: {{ isset($userPermissions['jobSheet']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-jobSheet" name="permissions[42][add]" value="1" {{
                                        $userPermissions['jobSheet']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-jobSheet">Add</label><br>
                                    <input type="checkbox" id="edit-jobSheet" name="permissions[42][edit]" value="1" {{
                                        $userPermissions['jobSheet']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-jobSheet">Edit</label><br>
                                    <input type="checkbox" id="del-jobSheet" name="permissions[42][del]" value="1" {{
                                        $userPermissions['jobSheet']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-jobSheet">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="generaljob" name="permissions[53][level]"
                                        value="generaljobSheet" {{ isset($userPermissions['generaljobSheet'])
                                        ? 'checked' : '' }}>
                                    <label for="generaljob">General Job Sheet</label>
                                </div>
                                <div id="generaljob-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-generaljobSheet" name="permissions[53][add]"
                                        value="1" {{ $userPermissions['generaljobSheet']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-generaljobSheet">Add</label><br>
                                    <input type="checkbox" id="edit-generaljobSheet" name="permissions[53][edit]"
                                        value="1" {{ $userPermissions['generaljobSheet']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-generaljobSheet">Edit</label><br>
                                    <input type="checkbox" id="del-generaljobSheet" name="permissions[53][del]"
                                        value="1" {{ $userPermissions['generaljobSheet']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-generaljobSheet">Delete</label><br>
                                </div>
                            </div>



















                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="attendanceSystem"
                                    name="attendanceSystem" value="1" {{ old('attendanceSystem',
                                    $user->attendance_system) ? 'checked' : '' }}>
                                <label class="form-check-label" for="attendanceSystem">Attendance System</label>
                            </div>
                            <hr>

                            <!-- Attendance System Section -->
                            <div id="attendanceSystem-container"
                                style="display: {{ old('attendanceSystem', $user->attendance_system) ? 'block' : 'none' }};">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="attendance" name="permissions[43][level]"
                                        value="attendanceSystem" {{ isset($userPermissions['attendanceSystem'])
                                        ? 'checked' : '' }}>
                                    <label for="attendance">Attendance System</label>

                                    <div id="attendanceSystem-options"
                                        style="display: {{ isset($userPermissions['attendanceSystem']) ? 'block' : 'none' }}; margin-left: 25px;">
                                        <input type="checkbox" id="add-attendanceSystem-delivery"
                                            name="permissions[43][add]" value="1" {{
                                            $userPermissions['attendanceSystem']['add'] ?? 0 ? 'checked' : '' }}>
                                        <label for="add-attendanceSystem-delivery">Add</label><br>
                                        <input type="checkbox" id="edit-attendanceSystem-delivery"
                                            name="permissions[43][edit]" value="1" {{
                                            $userPermissions['attendanceSystem']['edit'] ?? 0 ? 'checked' : '' }}>
                                        <label for="edit-attendanceSystem-delivery">Edit</label><br>
                                        <input type="checkbox" id="del-attendanceSystem-delivery"
                                            name="permissions[43][del]" value="1" {{
                                            $userPermissions['attendanceSystem']['del'] ?? 0 ? 'checked' : '' }}>
                                        <label for="del-attendanceSystem-delivery">Delete</label><br>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="setup" name="setup" value="1" {{
                                    old('setup', $user->setup) ? 'checked' : '' }}>
                                <label class="form-check-label" for="setup">Set up</label>
                            </div>
                            <hr>

                            <!-- Set up Options -->
                            <div id="setup-options"
                                style="display: {{ in_array('Set up', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <!-- Country Registration -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="countryRegistration" name="permissions[29][level]"
                                        value="CountryRegistration" {{ isset($userPermissions['CountryRegistration'])
                                        ? 'checked' : '' }}>
                                    <label for="countryRegistration">Country Registration</label>
                                </div>
                                <div id="countryRegistration-options"
                                    style="display: {{ isset($userPermissions['CountryRegistration']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-countryRegistration" name="permissions[29][add]"
                                        value="1" {{ $userPermissions['CountryRegistration']['add'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="add-countryRegistration">Add</label><br>
                                    <input type="checkbox" id="update-countryRegistration" name="permissions[29][edit]"
                                        value="1" {{ $userPermissions['CountryRegistration']['edit'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="update-countryRegistration">Edit</label><br>
                                    <input type="checkbox" id="del-countryRegistration" name="permissions[29][del]"
                                        value="1" {{ $userPermissions['CountryRegistration']['del'] ?? 0 ? 'checked'
                                        : '' }}>
                                    <label for="del-countryRegistration">Delete</label><br>
                                </div>

                                <!-- ERP Parameters -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="erpParameters" name="permissions[30][level]"
                                        value="ERPParameters" {{ isset($userPermissions['ERPParameters']) ? 'checked'
                                        : '' }}>
                                    <label for="erpParameters">ERP Parameters</label>
                                </div>
                                <div id="erpParameters-options"
                                    style="display: {{ isset($userPermissions['ERPParameters']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="update-erpParameters" name="permissions[30][edit]"
                                        value="1" {{ $userPermissions['ERPParameters']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="update-erpParameters">Edit</label><br>
                                </div>

                                <!-- Product Log -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="productLog" name="permissions[31][level]"
                                        value="ProductLog" {{ isset($userPermissions['ProductLog']) ? 'checked' : '' }}>
                                    <label for="productLog">Product Log</label>
                                </div>

                                <!-- Item Registration Log -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="itemRegistrationLog" name="permissions[32][level]"
                                        value="ItemRegistrationLog" {{ isset($userPermissions['ItemRegistrationLog'])
                                        ? 'checked' : '' }}>
                                    <label for="itemRegistrationLog">Item Registration Log</label>
                                </div>
                            </div>

                            <div style="margin-left: 25px; display: none;" class="form-check">
                                <input class="form-check-input" type="checkbox" id="employee" name="navigationOptions[]"
                                    value="Employee" {{ in_array('Employee', old('navigationOptions',
                                    $navigationOptions)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="employee">Employee</label>
                            </div>

                            <div style="margin-left: 25px; display: none;" id="employeeLogOptions">
                                <input type="checkbox" id="employeeLog" name="permissions[1][level]" value="employeeLog"
                                    {{ isset($userPermissions['employeeLog']) ? 'checked' : '' }}>
                                <label for="employeeLog">Employee</label>
                            </div>



                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="setupDepartment"
                                    name="setupDepartment" value="1" {{ old('setupDepartment', $user->setup_department)
                                ? 'checked' : '' }}>
                                <label class="form-check-label" for="setupDepartment">Set-Up Department</label>
                            </div>
                            <hr>

                            <!-- Set-Up Department -->
                            <div id="setup-department-options"
                                style="display: {{ in_array('Set-Up Department', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="departmentsetup" name="permissions[44][level]"
                                        value="departmentsetup" {{ isset($userPermissions['departmentsetup'])
                                        ? 'checked' : '' }}>
                                    <label for="departmentsetup">Department</label>
                                </div>
                                <div id="departmentsetup-options"
                                    style="display: {{ isset($userPermissions['departmentsetup']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-department-setup" name="permissions[44][add]"
                                        value="1" {{ $userPermissions['departmentsetup']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-department-setup">Add</label><br>
                                    <input type="checkbox" id="edit-department-setup" name="permissions[44][edit]"
                                        value="1" {{ $userPermissions['departmentsetup']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-department-setup">Edit</label><br>
                                    <input type="checkbox" id="del-department-setup" name="permissions[44][del]"
                                        value="1" {{ $userPermissions['departmentsetup']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-department-setup">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="levelsetup" name="permissions[45][level]"
                                        value="levelsetup" {{ isset($userPermissions['levelsetup']) ? 'checked' : '' }}>
                                    <label for="levelsetup">Level 2</label>
                                </div>
                                <div id="levelsetup-options"
                                    style="display: {{ isset($userPermissions['levelsetup']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-level-setup" name="permissions[45][add]" value="1" {{
                                        $userPermissions['levelsetup']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-level-setup">Add</label><br>
                                    <input type="checkbox" id="edit-level-setup" name="permissions[45][edit]" value="1"
                                        {{ $userPermissions['levelsetup']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-level-setup">Edit</label><br>
                                    <input type="checkbox" id="del-level-setup" name="permissions[45][del]" value="1" {{
                                        $userPermissions['levelsetup']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-level-setup">Delete</label><br>
                                </div>
                            </div>

                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="employeeDepartment"
                                    name="employeeDepartment" value="1" {{ old('employeeDepartment',
                                    $user->employee_department) ? 'checked' : '' }}>
                                <label class="form-check-label" for="employeeDepartment">Employee Department</label>
                            </div>
                            <hr>

                            <!-- Employee Department Options -->
                            <div id="employee-department-options"
                                style="display: {{ in_array('Employee Department', old('navigationOptions', $navigationOptions)) ? 'block' : 'none' }};">
                                <!-- Add Employee -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="addemployee" name="permissions[46][level]"
                                        value="addemployee" {{ isset($userPermissions['addemployee']) ? 'checked' : ''
                                        }}>
                                    <label for="addemployee">Add Employee</label>
                                </div>
                                <div id="addemployee-options"
                                    style="display: {{ isset($userPermissions['addemployee']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-addemployee" name="permissions[46][add]" value="1" {{
                                        $userPermissions['addemployee']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-addemployee">Add</label><br>
                                    <input type="checkbox" id="edit-addemployee" name="permissions[46][edit]" value="1"
                                        {{ $userPermissions['addemployee']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-addemployee">Edit</label><br>
                                    <input type="checkbox" id="del-addemployee" name="permissions[46][del]" value="1" {{
                                        $userPermissions['addemployee']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-addemployee">Delete</label><br>
                                </div>

                                <!-- Register Employee -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="registeremployee" name="permissions[47][level]"
                                        value="registeremployee" {{ isset($userPermissions['registeremployee'])
                                        ? 'checked' : '' }}>
                                    <label for="registeremployee">Register Employee</label>
                                </div>
                                <div id="registeremployee-options"
                                    style="display: {{ isset($userPermissions['registeremployee']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-registeremployee" name="permissions[47][add]"
                                        value="1" {{ $userPermissions['registeremployee']['add'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="add-registeremployee">Add</label><br>
                                    <input type="checkbox" id="edit-registeremployee" name="permissions[47][edit]"
                                        value="1" {{ $userPermissions['registeremployee']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-registeremployee">Edit</label><br>
                                    <input type="checkbox" id="del-registeremployee" name="permissions[47][del]"
                                        value="1" {{ $userPermissions['registeremployee']['del'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="del-registeremployee">Delete</label><br>
                                </div>

                                <!-- Add Designation -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="adddesignation" name="permissions[48][level]"
                                        value="adddesignation" {{ isset($userPermissions['adddesignation']) ? 'checked'
                                        : '' }}>
                                    <label for="adddesignation">Add Designation</label>
                                </div>
                                <div id="adddesignation-options"
                                    style="display: {{ isset($userPermissions['adddesignation']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-adddesignation" name="permissions[48][add]" value="1"
                                        {{ $userPermissions['adddesignation']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-adddesignation">Add</label><br>
                                    <input type="checkbox" id="edit-adddesignation" name="permissions[48][edit]"
                                        value="1" {{ $userPermissions['adddesignation']['edit'] ?? 0 ? 'checked' : ''
                                        }}>
                                    <label for="edit-adddesignation">Edit</label><br>
                                    <input type="checkbox" id="del-adddesignation" name="permissions[48][del]" value="1"
                                        {{ $userPermissions['adddesignation']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-adddesignation">Delete</label><br>
                                </div>

                                <!-- Bonus Type -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="bonustype" name="permissions[49][level]"
                                        value="bonustype" {{ isset($userPermissions['bonustype']) ? 'checked' : '' }}>
                                    <label for="bonustype">Bonus Type</label>
                                </div>
                                <div id="bonustype-options"
                                    style="display: {{ isset($userPermissions['bonustype']) ? 'block' : 'none' }}; margin-left: 50px;">
                                    <input type="checkbox" id="add-bonustype" name="permissions[49][add]" value="1" {{
                                        $userPermissions['bonustype']['add'] ?? 0 ? 'checked' : '' }}>
                                    <label for="add-bonustype">Add</label><br>
                                    <input type="checkbox" id="edit-bonustype" name="permissions[49][edit]" value="1" {{
                                        $userPermissions['bonustype']['edit'] ?? 0 ? 'checked' : '' }}>
                                    <label for="edit-bonustype">Edit</label><br>
                                    <input type="checkbox" id="del-bonustype" name="permissions[49][del]" value="1" {{
                                        $userPermissions['bonustype']['del'] ?? 0 ? 'checked' : '' }}>
                                    <label for="del-bonustype">Delete</label><br>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to initialize all toggle functionality
    function initializeToggles() {
        // Main sections that directly control their options containers
        const mainSections = [
            { id: 'account', options: 'account-options' },
            { id: 'report', options: 'report-options' },
            { id: 'billing', options: 'billing-options' },
            { id: 'wagecalculator', options: 'calculator-options' },
            { id: 'deliveryChallan', options: 'delivery-options' },
            { id: 'wasteSale', options: 'wasteSale-options' },
            { id: 'gateEx', options: 'gateExx-options' },
            { id: 'gatePass', options: 'gatePass-options' },
            { id: 'purchase', options: 'purchase-options' },
            { id: 'inventory', options: 'inventory-options' },
            { id: 'productRegistration', options: 'productRegistration-options' },
            { id: 'jobSheet', options: 'jobSheet-options' },
            { id: 'attendanceSystem', options: 'attendanceSystem-container' },
            { id: 'setup', options: 'setup-options' },
            { id: 'setupDepartment', options: 'setup-department-options' },
            { id: 'employeeDepartment', options: 'employee-department-options' }
        ];

        // Initialize main sections
        mainSections.forEach(section => {
            const element = document.getElementById(section.id);
            const options = document.getElementById(section.options);

            if (element && options) {
                // Set initial state based on checkbox status
                options.style.display = element.checked ? 'block' : 'none';

                // Add change listener
                element.addEventListener('change', function () {
                    options.style.display = this.checked ? 'block' : 'none';

                    // If unchecking the main section, uncheck all child options
                    if (!this.checked) {
                        const allChildCheckboxes = options.querySelectorAll('input[type="checkbox"]');
                        allChildCheckboxes.forEach(child => {
                            child.checked = false;
                            // Hide any nested options
                            const nestedOptionsId = child.id + '-options';
                            const nestedOptions = document.getElementById(nestedOptionsId);
                            if (nestedOptions) {
                                nestedOptions.style.display = 'none';
                            }
                        });
                    }
                });
            }
        });

        // Nested options that are controlled by checkboxes within the main sections
        const nestedOptions = [
            // Account section
            ['level1', 'level1-options'],
            ['level2', 'level2-options'],
            ['chartOfAccount', 'chartOfAccount-options'],
            ['cashReceipt', 'cashReceipt-options'],
            ['chequeReceipt', 'chequeReceipt-options'],
            ['cashPayment', 'cashPayment-options'],
            ['bankReceipt', 'bankReceipt-options'],
            ['bankPayment', 'bankPayment-options'],
            ['officeCash', 'officeCash-options'],
            ['payables', 'payables-options'],
            ['receivables', 'receivables-options'],
            ['journalVoucher', 'journalVoucher-options'],
            ['openingBalance', 'openingBalance-options'],

            // Billing section
            ['pharmaceuticalbilling', 'pharmaceuticalbilling-options'],
            ['confectionerybilling', 'confectionerybilling-options'],
            ['generalbilling', 'generalbilling-options'],

            // Delivery section
            ['pharmaceuticaldelivery', 'pharmaceuticaldelivery-options'],
            ['confectionerydelivery', 'confectionerydelivery-options'],
            ['generaldelivery', 'generaldelivery-options'],

            // Waste Sale section
            ['waste', 'waste-options'],

            // Gate Ex section
            ['gate', 'gateEx-options'],

            // Gate Pass section
            ['gatePassin', 'gatePassin-options'],
            ['gatePassout', 'gatePassout-options'],

            // Purchase section
            ['boxboard', 'boxboard-options'],
            ['purchaseReturn', 'purchaseReturn-options'],
            ['purchasePlate', 'purchasePlate-options'],
            ['gluePurchase', 'gluePurchase-options'],
            ['inkPurchase', 'inkPurchase-options'],
            ['laminationPurchase', 'laminationPurchase-options'],
            ['corrugationPurchase', 'corrugationPurchase-options'],
            ['shipperPurchase', 'shipperPurchase-options'],
            ['dyePurchase', 'dyePurchase-options'],

            // Inventory section
            ['itemType', 'itemType-options'],
            ['itemRegistration', 'itemRegistration-options'],

            // Product Registration section
            ['productRegistrations', 'productRegistration-sub-options'],

            // Job Sheet section
            ['job', 'job-options'],
            ['generaljob', 'generaljob-options'],

            // Attendance System section
            ['attendance', 'attendanceSystem-options'],

            // Setup section
            ['countryRegistration', 'countryRegistration-options'],
            ['erpParameters', 'erpParameters-options'],

            // Setup Department section
            ['departmentsetup', 'departmentsetup-options'],
            ['levelsetup', 'levelsetup-options'],

            // Employee Department section
            ['addemployee', 'addemployee-options'],
            ['registeremployee', 'registeremployee-options'],
            ['adddesignation', 'adddesignation-options'],
            ['bonustype', 'bonustype-options'],

            // Calculator section
            ['boxboardcalculator', 'boxboardcalculator-options']
        ];

        // Initialize nested options
        nestedOptions.forEach(([checkboxId, optionsId]) => {
            const checkbox = document.getElementById(checkboxId);
            const options = document.getElementById(optionsId);

            if (checkbox && options) {
                // Set initial state
                options.style.display = checkbox.checked ? 'block' : 'none';

                // Add change listener
                checkbox.addEventListener('change', function () {
                    options.style.display = this.checked ? 'block' : 'none';

                    // If unchecking the parent, uncheck all child options
                    if (!this.checked) {
                        const childCheckboxes = options.querySelectorAll('input[type="checkbox"]');
                        childCheckboxes.forEach(child => {
                            child.checked = false;
                            // Hide any further nested options
                            const nestedOptionsId = child.id + '-options';
                            const nestedOptions = document.getElementById(nestedOptionsId);
                            if (nestedOptions) {
                                nestedOptions.style.display = 'none';
                            }
                        });
                    }
                });
            }
        });

        // Special handling for sections that might need it
        const jobSheetMain = document.getElementById('jobSheet');
        const jobSheetOptions = document.getElementById('jobSheet-options');
        const jobCheckbox = document.getElementById('job');
        const jobOptions = document.getElementById('job-options');
        const generalJobCheckbox = document.getElementById('generaljob');
        const generalJobOptions = document.getElementById('generaljob-options');

        if (jobSheetMain && jobSheetOptions) {
            // Set initial state
            jobSheetOptions.style.display = jobSheetMain.checked ? 'block' : 'none';

            // Add change listener
            jobSheetMain.addEventListener('change', function () {
                jobSheetOptions.style.display = this.checked ? 'block' : 'none';

                if (!this.checked) {
                    // Uncheck all child checkboxes when main is unchecked
                    if (jobCheckbox) jobCheckbox.checked = false;
                    if (jobOptions) jobOptions.style.display = 'none';
                    if (generalJobCheckbox) generalJobCheckbox.checked = false;
                    if (generalJobOptions) generalJobOptions.style.display = 'none';

                    // Uncheck all permission checkboxes
                    const permissionCheckboxes = jobSheetOptions.querySelectorAll('input[type="checkbox"]');
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });
        }

        if (jobCheckbox && jobOptions) {
            // Set initial state
            jobOptions.style.display = jobCheckbox.checked ? 'block' : 'none';

            // Add change listener
            jobCheckbox.addEventListener('change', function () {
                jobOptions.style.display = this.checked ? 'block' : 'none';

                if (!this.checked) {
                    // Uncheck all child permission checkboxes
                    const permissionCheckboxes = jobOptions.querySelectorAll('input[type="checkbox"]');
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });
        }

        if (generalJobCheckbox && generalJobOptions) {
            // Set initial state
            generalJobOptions.style.display = generalJobCheckbox.checked ? 'block' : 'none';

            // Add change listener
            generalJobCheckbox.addEventListener('change', function () {
                generalJobOptions.style.display = this.checked ? 'block' : 'none';

                if (!this.checked) {
                    // Uncheck all child permission checkboxes
                    const permissionCheckboxes = generalJobOptions.querySelectorAll('input[type="checkbox"]');
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize all toggle functionality
        initializeToggles();

        // Role selection handling
        var checkboxContainer = document.getElementById('checkboxContainer');
        var roleSelect = document.getElementById('role');

        // Initialize Select2 on role select after DOM is ready
        $('#role').select2();

        // When the value of the role changes, show or hide the checkbox container
        $('#role').on('change', function () {
            checkboxContainer.style.display = $(this).val() === 'user' ? 'block' : 'none';

            // If role is changed to admin, uncheck all permission checkboxes
            if ($(this).val() !== 'user') {
                const allCheckboxes = checkboxContainer.querySelectorAll('input[type="checkbox"]');
                allCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    // Hide any options containers
                    const optionsId = checkbox.id + '-options';
                    const options = document.getElementById(optionsId);
                    if (options) {
                        options.style.display = 'none';
                    }
                });
            }
        });

        // Trigger the change event to set the initial state correctly
        if ($('#role').val() === 'user') {
            checkboxContainer.style.display = 'block';
        } else {
            checkboxContainer.style.display = 'none';
        }

        // Password visibility toggle
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('conformpassword');
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const matchMessage = document.getElementById('matchMessage');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        }

        if (toggleConfirmPassword && confirmPassword) {
            toggleConfirmPassword.addEventListener('click', function () {
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        }

        if (confirmPassword && matchMessage && password) {
            confirmPassword.addEventListener('input', function () {
                if (password.value && confirmPassword.value) {
                    if (password.value === confirmPassword.value) {
                        matchMessage.textContent = 'Passwords match';
                        matchMessage.style.color = 'green';
                    } else {
                        matchMessage.textContent = 'Passwords do not match';
                        matchMessage.style.color = 'red';
                    }
                } else {
                    matchMessage.textContent = '';
                }
            });
        }
    });
</script>
@endsection