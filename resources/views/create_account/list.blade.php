@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Create Account</h4>
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
                <form id="voucherForm" action="{{ route('create_account.store') }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf
                    <div class="col-6">

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Gmail</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" minlength="8"
                                    required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    Show
                                </button>
                            </div>
                            <div id="passwordHelp" class="form-text">Password must be at least 8 characters long.</div>
                        </div>

                        <div class="mb-3">
                            <label for="conformpassword" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="conformpassword" name="conformpassword" class="form-control"
                                    required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    Show
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Select Role</label>
                            <select name="role" class="form-control select2" data-toggle="select2" id="role">
                                <option value="">Select</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div id="matchMessage" class="form-text"></div>

                        <div class="card p-3 mt-3" id="checkboxContainer">
                            <h5 class="mb-3">Select Navigation Option</h5>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="account" name="navigationOptions[]"
                                    value="Account">
                                <label class="form-check-label" for="account">Account</label>
                            </div>
                            <hr>

                            <!-- Account Options -->
                            <div id="account-options" style="display: none;">
                                <!-- Level 1 -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="level1" name="permissions[0][level]" value="Level1">
                                    <label for="level1">Level 1</label>
                                </div>

                                <div id="level1-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-level1" name="permissions[0][add]" value="1">
                                    <label for="add-level1">Add</label><br>
                                    <input type="checkbox" id="edit-level1" name="permissions[0][edit]" value="1">
                                    <label for="edit-level1">Edit</label><br>
                                    <input type="checkbox" id="del-level1" name="permissions[0][del]" value="1">
                                    <label for="del-level1">Delete</label><br>
                                </div>

                                <!-- Level 2 -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="level2" name="permissions[2][level]" value="Level2">
                                    <label for="level2">Level 2</label>
                                </div>
                                <div id="level2-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-level2" name="permissions[2][add]" value="1">
                                    <label for="add-level2">Add</label><br>
                                    <input type="checkbox" id="edit-level2" name="permissions[2][edit]" value="1">
                                    <label for="edit-level2">Edit</label><br>
                                    <input type="checkbox" id="del-level2" name="permissions[2][del]" value="1">
                                    <label for="del-level2">Delete</label><br>
                                </div>

                                <!-- Chart of Account -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="chartOfAccount" name="permissions[3][level]"
                                        value="ChartOfAccount">
                                    <label for="chartOfAccount">Chart of Account</label>
                                </div>
                                <div id="chartOfAccount-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-chart" name="permissions[3][add]" value="1">
                                    <label for="add-chart">Add</label><br>
                                    <input type="checkbox" id="edit-chart" name="permissions[3][edit]" value="1">
                                    <label for="edit-chart">Edit</label><br>
                                    <input type="checkbox" id="del-chart" name="permissions[3][del]" value="1">
                                    <label for="del-chart">Delete</label><br>
                                </div>

                                <!-- Cash Receipt -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="cashReceipt" name="permissions[4][level]"
                                        value="CashReceipt">
                                    <label for="cashReceipt">Cash Receipt</label>
                                </div>
                                <div id="cashReceipt-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-cash" name="permissions[4][add]" value="1">
                                    <label for="add-cash">Add</label><br>
                                    <input type="checkbox" id="edit-cash" name="permissions[4][edit]" value="1">
                                    <label for="edit-cash">Edit</label><br>
                                    <input type="checkbox" id="del-cash" name="permissions[4][del]" value="1">
                                    <label for="del-cash">Delete</label><br>
                                </div>

                                <!-- Cheque Receipt -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="chequeReceipt" name="permissions[5][level]"
                                        value="ChequeReceipt">
                                    <label for="chequeReceipt">Cheque Receipt</label>
                                </div>
                                <div id="chequeReceipt-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-cheque" name="permissions[5][add]" value="1">
                                    <label for="add-cheque">Add</label><br>
                                    <input type="checkbox" id="edit-cheque" name="permissions[5][edit]" value="1">
                                    <label for="edit-cheque">Edit</label><br>
                                    <input type="checkbox" id="del-cheque" name="permissions[5][del]" value="1">
                                    <label for="del-cheque">Delete</label><br>
                                </div>

                                <!-- Cash Payment -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="cashPayment" name="permissions[6][level]"
                                        value="CashPayment">
                                    <label for="cashPayment">Cash Payment</label>
                                </div>
                                <div id="cashPayment-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-cash-payment" name="permissions[6][add]" value="1">
                                    <label for="add-cash-payment">Add</label><br>
                                    <input type="checkbox" id="edit-cash-payment" name="permissions[6][edit]" value="1">
                                    <label for="edit-cash-payment">Edit</label><br>
                                    <input type="checkbox" id="del-cash-payment" name="permissions[6][del]" value="1">
                                    <label for="del-cash-payment">Delete</label><br>
                                </div>

                                <!-- Bank Receipt -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="bankReceipt" name="permissions[7][level]"
                                        value="BankReceipt">
                                    <label for="bankReceipt">Bank Receipt</label>
                                </div>
                                <div id="bankReceipt-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-bank" name="permissions[7][add]" value="1">
                                    <label for="add-bank">Add</label><br>
                                    <input type="checkbox" id="edit-bank" name="permissions[7][edit]" value="1">
                                    <label for="edit-bank">Edit</label><br>
                                    <input type="checkbox" id="del-bank" name="permissions[7][del]" value="1">
                                    <label for="del-bank">Delete</label><br>
                                </div>


                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="bankPayment" name="permissions[39][level]"
                                        value="BankPayment">
                                    <label for="bankPayment">Bank Payment</label>
                                </div>
                                <div id="bankPayment-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-bank-Payment" name="permissions[39][add]" value="1">
                                    <label for="add-bank-Payment">Add</label><br>
                                    <input type="checkbox" id="edit-bank-Payment" name="permissions[39][edit]"
                                        value="1">
                                    <label for="edit-bank-Payment">Edit</label><br>
                                    <input type="checkbox" id="del-bank-Payment" name="permissions[39][del]" value="1">
                                    <label for="del-bank-Payment">Delete</label><br>
                                </div>


                                <!-- Ledger -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="ledger" name="permissions[8][level]" value="Ledger">
                                    <label for="ledger">Ledger</label>
                                </div>

                                <!-- Office Cash -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="officeCash" name="permissions[41][level]"
                                        value="officeCash">
                                    <label for="officeCash">Office Cash</label>
                                </div>
                                <div id="officeCash-options" style="display: none; margin-left: 50px;">

                                    <input type="checkbox" id="add-officeCash" name="permissions[41][add]" value="1">
                                    <label for="add-officeCash">Add</label><br>

                                    <input type="checkbox" id="edit-officeCash" name="permissions[41][edit]" value="1">
                                    <label for="edit-officeCash">Edit</label><br>

                                    <input type="checkbox" id="del-officeCash" name="permissions[41][del]" value="1">
                                    <label for="del-officeCash">Delete</label><br>

                                </div>


                                <!-- Payables -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="payables" name="permissions[9][level]" value="Payables">
                                    <label for="payables">Payables</label>
                                </div>
                                <div id="payables-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-payables" name="permissions[9][add]" value="1">
                                    <label for="add-payables">Add</label><br>
                                    <input type="checkbox" id="edit-payables" name="permissions[9][edit]" value="1">
                                    <label for="edit-payables">Edit</label><br>
                                    <input type="checkbox" id="del-payables" name="permissions[9][del]" value="1">
                                    <label for="del-payables">Delete</label><br>
                                </div>

                                <!-- Receivables -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="receivables" name="permissions[10][level]"
                                        value="Receivables">
                                    <label for="receivables">Receivables</label>
                                </div>
                                <div id="receivables-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-receivables" name="permissions[10][add]" value="1">
                                    <label for="add-receivables">Add</label><br>
                                    <input type="checkbox" id="edit-receivables" name="permissions[10][edit]" value="1">
                                    <label for="edit-receivables">Edit</label><br>
                                    <input type="checkbox" id="del-receivables" name="permissions[10][del]" value="1">
                                    <label for="del-receivables">Delete</label><br>
                                </div>

                                <!-- Journal Voucher -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="journalVoucher" name="permissions[11][level]"
                                        value="JournalVoucher">
                                    <label for="journalVoucher">Journal Voucher</label>
                                </div>
                                <div id="journalVoucher-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-journal" name="permissions[11][add]" value="1">
                                    <label for="add-journal">Add</label><br>
                                    <input type="checkbox" id="edit-journal" name="permissions[11][edit]" value="1">
                                    <label for="edit-journal">Edit</label><br>
                                    <input type="checkbox" id="del-journal" name="permissions[11][del]" value="1">
                                    <label for="del-journal">Delete</label><br>
                                </div>

                                <!-- Opening Balance -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="openingBalance" name="permissions[12][level]"
                                        value="OpeningBalance">
                                    <label for="openingBalance">Opening Balance</label>
                                </div>
                                <div id="openingBalance-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-opening" name="permissions[12][add]" value="1">
                                    <label for="add-opening">Add</label><br>
                                    <input type="checkbox" id="edit-opening" name="permissions[12][edit]" value="1">
                                    <label for="edit-opening">Edit</label><br>
                                    <input type="checkbox" id="del-opening" name="permissions[12][del]" value="1">
                                    <label for="del-opening">Delete</label><br>
                                </div>
                            </div>

                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="report" name="navigationOptions[]"
                                    value="Report">
                                <label class="form-check-label" for="report">Reports</label>
                            </div>
                            <hr>

                            <!-- Account Options -->
                            <div id="report-options" style="display: none;">
                                <!-- Level 1 -->

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="ExpenseReports" name="permissions[50][level]"
                                        value="ExpenseReports">
                                    <label for="ExpenseReports">Expense Reports</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="PurchaseReports" name="permissions[36][level]"
                                        value="PurchaseReports">
                                    <label for="PurchaseReports">Purchase Reports</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="SaleReports" name="permissions[37][level]"
                                        value="SaleReports">
                                    <label for="SaleReports">Sale Reports</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="DailyStatement" name="permissions[38][level]"
                                        value="DailyStatement">
                                    <label for="DailyStatement">Daily Statement</label>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="StockReports" name="permissions[51][level]"
                                        value="StockReports">
                                    <label for="StockReports">Stock Reports</label>
                                </div>

                            </div>



                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="billing" name="navigationOptions[]"
                                    value="Billing">
                                <label class="form-check-label" for="billing">Billing</label>
                            </div>
                            <hr>


                            <!-- Pharmaceutical Billing -->
                            <div id="billing-options" style="display: none;">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="pharmaceuticalbilling" name="permissions[13][level]"
                                        value="pharmaceuticalbilling">
                                    <label for="pharmaceuticalbilling">Pharmaceutical Billing</label>
                                </div>
                                <div id="pharmaceuticalbilling-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-pharmaceutical" name="permissions[13][add]"
                                        value="1">
                                    <label for="add-pharmaceutical">Add</label><br>
                                    <input type="checkbox" id="edit-pharmaceutical" name="permissions[13][edit]"
                                        value="1">
                                    <label for="edit-pharmaceutical">Edit</label><br>
                                    <input type="checkbox" id="del-pharmaceutical" name="permissions[13][del]"
                                        value="1">
                                    <label for="del-pharmaceutical">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="confectionerybilling" name="permissions[14][level]"
                                        value="confectionerybilling">
                                    <label for="confectionerybilling">Confectionery Billing</label>
                                </div>
                                <div id="confectionerybilling-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-confectionery" name="permissions[14][add]" value="1">
                                    <label for="add-confectionery">Add</label><br>
                                    <input type="checkbox" id="edit-confectionery" name="permissions[14][edit]"
                                        value="1">
                                    <label for="edit-confectionery">Edit</label><br>
                                    <input type="checkbox" id="del-confectionery" name="permissions[14][del]" value="1">
                                    <label for="del-confectionery">Delete</label><br>
                                </div>





                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="generalbilling" name="permissions[14][level]"
                                        value="generalbilling">
                                    <label for="generalbilling">General Billing</label>
                                </div>
                                <div id="generalbilling-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-general" name="permissions[14][add]" value="1">
                                    <label for="add-general">Add</label><br>
                                    <input type="checkbox" id="edit-general" name="permissions[14][edit]" value="1">
                                    <label for="edit-general">Edit</label><br>
                                    <input type="checkbox" id="del-general" name="permissions[14][del]" value="1">
                                    <label for="del-general">Delete</label><br>
                                </div>








                            </div>

                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="wagecalculator"
                                    name="navigationOptions[]" value="Wage Calculator">
                                <label class="form-check-label" for="wagecalculator">Wage Calculator</label>
                            </div>
                            <hr>


                            <!-- Wage calculator -->
                            <div id="calculator-options" style="display: none;">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="boxboardcalculator" name="permissions[52][level]"
                                        value="boxboardcalculator">
                                    <label for="boxboardcalculator">Boxboard</label>
                                </div>
                                <div id="boxboardcalculator-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-boxboardcalculator" name="permissions[52][add]"
                                        value="1">
                                    <label for="add-boxboardcalculator">Add</label><br>
                                    <input type="checkbox" id="edit-boxboardcalculator" name="permissions[52][edit]"
                                        value="1">
                                    <label for="edit-boxboardcalculator">Edit</label><br>
                                    <input type="checkbox" id="del-boxboardcalculator" name="permissions[52][del]"
                                        value="1">
                                    <label for="del-boxboardcalculator">Delete</label><br>
                                </div>
                            </div>

                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="deliveryChallan"
                                    name="navigationOptions[]" value="Delivery Challen">
                                <label class="form-check-label" for="deliveryChallan">Delivery Challen</label>
                            </div>
                            <hr>

                            <!-- Delivery Challen -->
                            <div id="delivery-options" style="display: none;">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="pharmaceuticaldelivery" name="permissions[15][level]"
                                        value="pharmaceuticaldelivery">
                                    <label for="pharmaceuticaldelivery">Pharmaceutical Delivery</label>
                                </div>
                                <div id="pharmaceuticaldelivery-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-pharmaceutical" name="permissions[15][add]"
                                        value="1">
                                    <label for="add-pharmaceutical-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-pharmaceutical" name="permissions[15][edit]"
                                        value="1">
                                    <label for="edit-pharmaceutical-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-pharmaceutical" name="permissions[15][del]"
                                        value="1">
                                    <label for="del-pharmaceutical-delivery">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="confectionerydelivery" name="permissions[16][level]"
                                        value="confectionerydelivery">
                                    <label for="confectionerydelivery">Confectionery Delivery</label>
                                </div>
                                <div id="confectionerydelivery-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-confectionery-delivery" name="permissions[16][add]"
                                        value="1">
                                    <label for="add-confectionery-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-confectionery-delivery" name="permissions[16][edit]"
                                        value="1">
                                    <label for="edit-confectionery-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-confectionery-delivery" name="permissions[16][del]"
                                        value="1">
                                    <label for="del-confectionery-delivery">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="generaldelivery" name="permissions[54][level]"
                                        value="generaldelivery">
                                    <label for="generaldelivery">General Delivery</label>
                                </div>
                                <div id="generaldelivery-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-general-delivery" name="permissions[54][add]"
                                        value="1">
                                    <label for="add-general-delivery">Add</label><br>
                                    <input type="checkbox" id="edit-general-delivery" name="permissions[54][edit]"
                                        value="1">
                                    <label for="edit-general-delivery">Edit</label><br>
                                    <input type="checkbox" id="del-general-delivery" name="permissions[54][del]"
                                        value="1">
                                    <label for="del-general-delivery">Delete</label><br>
                                </div>





                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="wasteSale"
                                    name="navigationOptions[]" value="Waste Sale">
                                <label class="form-check-label" for="wasteSale">Waste Sale</label>
                            </div>
                            <hr>

                            <!-- Waste Sale Section -->
                            <div style="margin-left: 25px; display: none;">
                                <input type="checkbox" id="waste" name="permissions[35][level]" value="waste">
                                <label for="waste">Waste Sale</label>
                            </div>
                            <div id="wasteSale-options" style="display: none; margin-left: 25px;">
                                <input type="checkbox" id="add-wasteSale-delivery" name="permissions[35][add]"
                                    value="1">
                                <label for="add-wasteSale-delivery">Add</label><br>
                                <input type="checkbox" id="edit-wasteSale-delivery" name="permissions[35][edit]"
                                    value="1">
                                <label for="edit-wasteSale-delivery">Edit</label><br>
                                <input type="checkbox" id="del-wasteSale-delivery" name="permissions[35][del]"
                                    value="1">
                                <label for="del-wasteSale-delivery">Delete</label><br>
                            </div>


                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gateEx" name="navigationOptions[]"
                                    value="Gate Ex">
                                <label class="form-check-label" for="gateEx">Gate Ex</label>
                            </div>
                            <hr>

                            <!-- Gate Ex Section -->
                            <div style="margin-left: 25px; display: none;">
                                <input type="checkbox" id="gate" name="permissions[40][level]" value="gateEx">
                                <label for="gate">Gate Ex</label>
                            </div>
                            <div id="gateEx-options" style="display: none; margin-left: 25px;">

                                <input type="checkbox" id="add-gateEx-delivery" name="permissions[40][add]" value="1">
                                <label for="add-gateEx-delivery">Add</label><br>

                                <input type="checkbox" id="edit-gateEx-delivery" name="permissions[40][edit]" value="1">
                                <label for="edit-gateEx-delivery">Edit</label><br>

                                <input type="checkbox" id="del-gateEx-delivery" name="permissions[40][del]" value="1">
                                <label for="del-gateEx-delivery">Delete</label><br>

                            </div>


                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gatePass" name="navigationOptions[]"
                                    value="Gate Pass">
                                <label class="form-check-label" for="gatePass">Gate Pass</label>
                            </div>
                            <hr>

                            <div id="gatePass-options" style="display: none;">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="gatePassin" name="permissions[17][level]"
                                        value="gatePassin">
                                    <label for="gatePassin">Gate-Pass In</label>
                                </div>
                                <div id="gatePassin-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-gatePassin" name="permissions[17][add]" value="1">
                                    <label for="add-gatePassin">Add</label><br>
                                    <input type="checkbox" id="edit-gatePassin" name="permissions[17][edit]" value="1">
                                    <label for="edit-gatePassin">Edit</label><br>
                                    <input type="checkbox" id="del-gatePassin" name="permissions[17][del]" value="1">
                                    <label for="del-gatePassin">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="gatePassout" name="permissions[18][level]"
                                        value="gatePassout">
                                    <label for="gatePassout">Gate-Pass Out</label>
                                </div>
                                <div id="gatePassout-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-gatePassout" name="permissions[18][add]" value="1">
                                    <label for="add-gatePassout">Add</label><br>
                                    <input type="checkbox" id="edit-gatePassout" name="permissions[18][edit]" value="1">
                                    <label for="edit-gatePassout">Edit</label><br>
                                    <input type="checkbox" id="del-gatePassout" name="permissions[18][del]" value="1">
                                    <label for="del-gatePassout">Delete</label><br>
                                </div>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="purchase" name="navigationOptions[]"
                                    value="Purchase">
                                <label class="form-check-label" for="purchase">Purchase</label>
                            </div>
                            <hr>

                            <!-- Purchase Options -->
                            <div id="purchase-options" style="display: none;">
                                <!-- Boxboard -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="boxboard" name="permissions[19][level]" value="Boxboard">
                                    <label for="boxboard">Boxboard</label>
                                </div>
                                <div id="boxboard-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-boxboard" name="permissions[19][add]" value="1">
                                    <label for="add-boxboard">Add</label><br>
                                    <input type="checkbox" id="edit-boxboard" name="permissions[19][edit]" value="1">
                                    <label for="edit-boxboard">Edit</label><br>
                                    <input type="checkbox" id="del-boxboard" name="permissions[19][del]" value="1">
                                    <label for="del-boxboard">Delete</label><br>
                                </div>

                                <!-- Purchase Return -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="purchaseReturn" name="permissions[20][level]"
                                        value="PurchaseReturn">
                                    <label for="purchaseReturn">Purchase Return</label>
                                </div>
                                <div id="purchaseReturn-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-purchaseReturn" name="permissions[20][add]"
                                        value="1">
                                    <label for="add-purchaseReturn">Add</label><br>
                                    <input type="checkbox" id="edit-purchaseReturn" name="permissions[20][edit]"
                                        value="1">
                                    <label for="edit-purchaseReturn">Edit</label><br>
                                    <input type="checkbox" id="del-purchaseReturn" name="permissions[20][del]"
                                        value="1">
                                    <label for="del-purchaseReturn">Delete</label><br>
                                </div>

                                <!-- Purchase Plate -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="purchasePlate" name="permissions[21][level]"
                                        value="PurchasePlate">
                                    <label for="purchasePlate">Purchase Plate</label>
                                </div>
                                <div id="purchasePlate-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-purchasePlate" name="permissions[21][add]" value="1">
                                    <label for="add-purchasePlate">Add</label><br>
                                    <input type="checkbox" id="edit-purchasePlate" name="permissions[21][edit]"
                                        value="1">
                                    <label for="edit-purchasePlate">Edit</label><br>
                                    <input type="checkbox" id="del-purchasePlate" name="permissions[21][del]" value="1">
                                    <label for="del-purchasePlate">Delete</label><br>
                                </div>

                                <!-- Glue Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="gluePurchase" name="permissions[22][level]"
                                        value="GluePurchase">
                                    <label for="gluePurchase">Glue Purchase</label>
                                </div>
                                <div id="gluePurchase-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-gluePurchase" name="permissions[22][add]" value="1">
                                    <label for="add-gluePurchase">Add</label><br>
                                    <input type="checkbox" id="edit-gluePurchase" name="permissions[22][edit]"
                                        value="1">
                                    <label for="edit-gluePurchase">Edit</label><br>
                                    <input type="checkbox" id="del-gluePurchase" name="permissions[22][del]" value="1">
                                    <label for="del-gluePurchase">Delete</label><br>
                                </div>

                                <!-- Ink Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="inkPurchase" name="permissions[23][level]"
                                        value="InkPurchase">
                                    <label for="inkPurchase">Ink Purchase</label>
                                </div>
                                <div id="inkPurchase-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-inkPurchase" name="permissions[23][add]" value="1">
                                    <label for="add-inkPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-inkPurchase" name="permissions[23][edit]" value="1">
                                    <label for="edit-inkPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-inkPurchase" name="permissions[23][del]" value="1">
                                    <label for="del-inkPurchase">Delete</label><br>
                                </div>

                                <!-- Lamination Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="laminationPurchase" name="permissions[24][level]"
                                        value="LaminationPurchase">
                                    <label for="laminationPurchase">Lamination Purchase</label>
                                </div>
                                <div id="laminationPurchase-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-laminationPurchase" name="permissions[24][add]"
                                        value="1">
                                    <label for="add-laminationPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-laminationPurchase" name="permissions[24][edit]"
                                        value="1">
                                    <label for="edit-laminationPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-laminationPurchase" name="permissions[24][del]"
                                        value="1">
                                    <label for="del-laminationPurchase">Delete</label><br>
                                </div>

                                <!-- Corrugation Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="corrugationPurchase" name="permissions[25][level]"
                                        value="CorrugationPurchase">
                                    <label for="corrugationPurchase">Corrugation Purchase</label>
                                </div>
                                <div id="corrugationPurchase-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-corrugationPurchase" name="permissions[25][add]"
                                        value="1">
                                    <label for="add-corrugationPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-corrugationPurchase" name="permissions[25][edit]"
                                        value="1">
                                    <label for="edit-corrugationPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-corrugationPurchase" name="permissions[25][del]"
                                        value="1">
                                    <label for="del-corrugationPurchase">Delete</label><br>
                                </div>

                                <!-- Shipper Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="shipperPurchase" name="permissions[26][level]"
                                        value="ShipperPurchase">
                                    <label for="shipperPurchase">Shipper Purchase</label>
                                </div>
                                <div id="shipperPurchase-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-shipperPurchase" name="permissions[26][add]"
                                        value="1">
                                    <label for="add-shipperPurchase">Add</label><br>
                                    <input type="checkbox" id="edit-shipperPurchase" name="permissions[26][edit]"
                                        value="1">
                                    <label for="edit-shipperPurchase">Edit</label><br>
                                    <input type="checkbox" id="del-shipperPurchase" name="permissions[26][del]"
                                        value="1">
                                    <label for="del-shipperPurchase">Delete</label><br>
                                </div>



                                <!-- Dye Purchase -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="dyePurchase" name="permissions[52][level]"
                                        value="dyePurchase">
                                    <label for="dyePurchase">Dye Purchase</label>
                                </div>
                                <div id="dyePurchase-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-dyePurchase" name="permissions[52][add]" value="1">
                                    <label for="add-dyePurchase">Add</label><br>
                                    <input type="checkbox" id="edit-dyePurchase" name="permissions[52][edit]" value="1">
                                    <label for="edit-dyePurchase">Edit</label><br>
                                    <input type="checkbox" id="del-dyePurchase" name="permissions[52][del]" value="1">
                                    <label for="del-dyePurchase">Delete</label><br>
                                </div>
                            </div>








                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="inventory"
                                    name="navigationOptions[]" value="Inventory">
                                <label class="form-check-label" for="inventory">Inventory</label>
                            </div>
                            <hr>

                            <!-- Inventory Options -->
                            <div id="inventory-options" style="display: none;">
                                <!-- Item Type -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="itemType" name="permissions[27][level]" value="ItemType">
                                    <label for="itemType">Item Type</label>
                                </div>
                                <div id="itemType-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-itemType" name="permissions[27][add]" value="1">
                                    <label for="add-itemType">Add</label><br>
                                    <input type="checkbox" id="edit-itemType" name="permissions[27][edit]" value="1">
                                    <label for="edit-itemType">Edit</label><br>
                                    <input type="checkbox" id="del-itemType" name="permissions[27][del]" value="1">
                                    <label for="del-itemType">Delete</label><br>
                                </div>

                                <!-- Item Registration -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="itemRegistration" name="permissions[28][level]"
                                        value="ItemRegistration">
                                    <label for="itemRegistration">Item Registration</label>
                                </div>
                                <div id="itemRegistration-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-itemRegistration" name="permissions[28][add]"
                                        value="1">
                                    <label for="add-itemRegistration">Add</label><br>
                                    <input type="checkbox" id="edit-itemRegistration" name="permissions[28][edit]"
                                        value="1">
                                    <label for="edit-itemRegistration">Edit</label><br>
                                    <input type="checkbox" id="del-itemRegistration" name="permissions[28][del]"
                                        value="1">
                                    <label for="del-itemRegistration">Delete</label><br>
                                </div>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="productRegistration"
                                    name="navigationOptions[]" value="Product Registration">
                                <label class="form-check-label" for="productRegistration">Product Registration</label>
                            </div>
                            <hr>

                            <div id="productRegistration-options" style="display: none; margin-left: 25px;">
                                <input type="checkbox" id="productRegistrations" name="permissions[34][level]"
                                    value="productRegistrations">
                                <label for="productRegistrations">Product Registration</label>
                            </div>

                            <!-- Product Registration Options -->
                            <div id="productRegistration-sub-options" style="display: none; margin-left: 50px;">
                                <input type="checkbox" id="add-productRegistration" name="permissions[34][add]"
                                    value="1">
                                <label for="add-productRegistration">Add</label><br>
                                <input type="checkbox" id="edit-productRegistration" name="permissions[34][edit]"
                                    value="1">
                                <label for="edit-productRegistration">Edit</label><br>
                                <input type="checkbox" id="del-productRegistration" name="permissions[34][del]"
                                    value="1">
                                <label for="del-productRegistration">Delete</label><br>
                            </div>





                            <hr>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="jobSheet" name="navigationOptions[]" value="Job Sheet">
                                <label class="form-check-label" for="jobSheet">Job Sheet</label>
                            </div>
                            <hr>
                            
                            <!-- Job Sheet Section -->
                            <div id="jobSheet-options" style="display: none;">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="job" name="permissions[42][level]" value="jobSheet">
                                    <label for="job">Job Sheet</label>
                                </div>
                                <div id="job-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-jobSheet" name="permissions[42][add]" value="1">
                                    <label for="add-jobSheet">Add</label><br>
                                    <input type="checkbox" id="edit-jobSheet" name="permissions[42][edit]" value="1">
                                    <label for="edit-jobSheet">Edit</label><br>
                                    <input type="checkbox" id="del-jobSheet" name="permissions[42][del]" value="1">
                                    <label for="del-jobSheet">Delete</label><br>
                                </div>
                            
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="generaljob" name="permissions[53][level]" value="generaljobSheet">
                                    <label for="generaljob">General Job Sheet</label>
                                </div>
                                <div id="generaljob-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-generaljobSheet" name="permissions[53][add]" value="1">
                                    <label for="add-generaljobSheet">Add</label><br>
                                    <input type="checkbox" id="edit-generaljobSheet" name="permissions[53][edit]" value="1">
                                    <label for="edit-generaljobSheet">Edit</label><br>
                                    <input type="checkbox" id="del-generaljobSheet" name="permissions[53][del]" value="1">
                                    <label for="del-generaljobSheet">Delete</label><br>
                                </div>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="attendanceSystem"
                                    name="navigationOptions[]" value="Attendance System">
                                <label class="form-check-label" for="attendanceSystem">Attendance System</label>
                            </div>
                            <hr>

                            <!-- Attendance System Section -->
                            <div style="margin-left: 25px; display: none;">
                                <input type="checkbox" id="attendance" name="permissions[43][level]"
                                    value="attendanceSystem">
                                <label for="attendance">Attendance System</label>
                            </div>
                            <div id="attendanceSystem-options" style="display: none; margin-left: 25px;">

                                <input type="checkbox" id="add-attendanceSystem-delivery" name="permissions[43][add]"
                                    value="1">
                                <label for="add-attendanceSystem-delivery">Add</label><br>

                                <input type="checkbox" id="edit-attendanceSystem-delivery" name="permissions[43][edit]"
                                    value="1">
                                <label for="edit-attendanceSystem-delivery">Edit</label><br>

                                <input type="checkbox" id="del-attendanceSystem-delivery" name="permissions[43][del]"
                                    value="1">
                                <label for="del-attendanceSystem-delivery">Delete</label><br>

                            </div>


















                            <hr>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="setup" name="navigationOptions[]"
                                    value="Set up">
                                <label class="form-check-label" for="setup">Set up</label>
                            </div>
                            <hr>

                            <!-- Set up Options -->
                            <div id="setup-options" style="display: none;">
                                <!-- Country Registration -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="countryRegistration" name="permissions[29][level]"
                                        value="CountryRegistration">
                                    <label for="countryRegistration">Country Registration</label>
                                </div>
                                <div id="countryRegistration-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-countryRegistration" name="permissions[29][add]"
                                        value="1">
                                    <label for="add-countryRegistration">Add</label><br>
                                    <input type="checkbox" id="update-countryRegistration" name="permissions[29][editl]"
                                        value="1">
                                    <label for="update-countryRegistration">Edit</label><br>
                                    <input type="checkbox" id="del-countryRegistration" name="permissions[29][del]"
                                        value="1">
                                    <label for="del-countryRegistration">Delete</label><br>
                                </div>

                                <!-- ERP Parameters -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="erpParameters" name="permissions[30][level]"
                                        value="ERPParameters">
                                    <label for="erpParameters">ERP Parameters</label>
                                </div>
                                <div id="erpParameters-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="update-erpParameters" name="permissions[30][edit]"
                                        value="1">
                                    <label for="update-erpParameters">Edit</label><br>
                                </div>

                                <!-- Product Log (removed View option) -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="productLog" name="permissions[31][level]"
                                        value="ProductLog">
                                    <label for="productLog">Product Log</label>
                                </div>

                                <!-- Item Registration Log (removed View option) -->
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="itemRegistrationLog" name="permissions[32][level]"
                                        value="ItemRegistrationLog">
                                    <label for="itemRegistrationLog">Item Registration Log</label>
                                </div>
                            </div>


                            <div style="margin-left: 25px; display: none;" class="form-check">
                                <input class="form-check-input" type="checkbox" id="employee" name="navigationOptions[]"
                                    value="Employee">
                                <label class="form-check-label" for="employee">Employee</label>
                            </div>

                            <div style="margin-left: 25px; display: none;" id="employeeLogOptions">
                                <input type="checkbox" id="employeeLog" name="permissions[1][level]"
                                    value="employeeLog">
                                <label for="employeeLog">Employee</label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="setupDepartment"
                                    name="navigationOptions[]" value="Set-Up Department">
                                <label class="form-check-label" for="setupDepartment">Set-Up Department</label>
                            </div>
                            <hr>

                            <!-- Set-Up Department -->
                            <div id="setup-department-options" style="display: none;">
                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="departmentsetup" name="permissions[44][level]"
                                        value="departmentsetup">
                                    <label for="departmentsetup">Department</label>
                                </div>
                                <div id="departmentsetup-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-department-setup" name="permissions[44][add]"
                                        value="1">
                                    <label for="add-department-setup">Add</label><br>
                                    <input type="checkbox" id="edit-department-setup" name="permissions[44][edit]"
                                        value="1">
                                    <label for="edit-department-setup">Edit</label><br>
                                    <input type="checkbox" id="del-department-setup" name="permissions[44][del]"
                                        value="1">
                                    <label for="del-department-setup">Delete</label><br>
                                </div>

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="levelsetup" name="permissions[45][level]"
                                        value="levelsetup">
                                    <label for="levelsetup">Level 2</label>
                                </div>
                                <div id="levelsetup-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-level-setup" name="permissions[45][add]" value="1">
                                    <label for="add-level-setup">Add</label><br>
                                    <input type="checkbox" id="edit-level-setup" name="permissions[45][edit]" value="1">
                                    <label for="edit-level-setup">Edit</label><br>
                                    <input type="checkbox" id="del-level-setup" name="permissions[45][del]" value="1">
                                    <label for="del-level-setup">Delete</label><br>
                                </div>
                            </div>



                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="employeeDepartment"
                                    name="navigationOptions[]" value="Employee Department">
                                <label class="form-check-label" for="employeeDepartment">Employee</label>
                            </div>
                            <hr>

                            <!-- Set-Up Department -->
                            <div id="employee-department-options" style="display: none;">

                                <!-- Add Employee -->

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="addemployee" name="permissions[46][level]"
                                        value="addemployee">
                                    <label for="addemployee">Add Employee</label>
                                </div>
                                <div id="addemployee-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-addemployee" name="permissions[46][add]" value="1">
                                    <label for="add-addemployee">Add</label><br>
                                    <input type="checkbox" id="edit-addemployee" name="permissions[46][edit]" value="1">
                                    <label for="edit-addemployee">Edit</label><br>
                                    <input type="checkbox" id="del-addemployee" name="permissions[46][del]" value="1">
                                    <label for="del-addemployee">Delete</label><br>
                                </div>

                                <!-- Register Employee -->

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="registeremployee" name="permissions[47][level]"
                                        value="registeremployee">
                                    <label for="registeremployee">Register Employee</label>
                                </div>
                                <div id="registeremployee-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-registeremployee" name="permissions[47][add]"
                                        value="1">
                                    <label for="add-registeremployee">Add</label><br>
                                    <input type="checkbox" id="edit-registeremployee" name="permissions[47][edit]"
                                        value="1">
                                    <label for="edit-registeremployee">Edit</label><br>
                                    <input type="checkbox" id="del-registeremployee" name="permissions[47][del]"
                                        value="1">
                                    <label for="del-registeremployee">Delete</label><br>
                                </div>

                                <!-- Add Designation -->

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="adddesignation" name="permissions[48][level]"
                                        value="adddesignation">
                                    <label for="adddesignation">Add Designation</label>
                                </div>
                                <div id="adddesignation-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-adddesignation" name="permissions[48][add]"
                                        value="1">
                                    <label for="add-adddesignation">Add</label><br>
                                    <input type="checkbox" id="edit-adddesignation" name="permissions[48][edit]"
                                        value="1">
                                    <label for="edit-adddesignation">Edit</label><br>
                                    <input type="checkbox" id="del-adddesignation" name="permissions[48][del]"
                                        value="1">
                                    <label for="del-adddesignation">Delete</label><br>
                                </div>

                                <!-- Add Bonus -->

                                <div style="margin-left: 25px;">
                                    <input type="checkbox" id="bonustype" name="permissions[49][level]"
                                        value="bonustype">
                                    <label for="bonustype">Bonus Type</label>
                                </div>
                                <div id="bonustype-options" style="display: none; margin-left: 50px;">
                                    <input type="checkbox" id="add-bonustype" name="permissions[49][add]" value="1">
                                    <label for="add-bonustype">Add</label><br>
                                    <input type="checkbox" id="edit-bonustype" name="permissions[49][edit]" value="1">
                                    <label for="edit-bonustype">Edit</label><br>
                                    <input type="checkbox" id="del-bonustype" name="permissions[49][del]" value="1">
                                    <label for="del-bonustype">Delete</label><br>
                                </div>



                            </div>














                        </div>
                        <br>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionId) {
        var section = document.getElementById(sectionId);
        if (section) {
            section.addEventListener('change', function () {
                var options = document.getElementById(optionId);
                if (options) {
                    options.style.display = this.checked ? 'block' : 'none';
                }
            });
        }
    }

    toggleOptions('report', 'report-options');


    document.getElementById('wasteSale').addEventListener('change', function () {
        var wasteSection = document.getElementById('waste')
            .parentElement; // Get the parent element containing the second waste checkbox
        wasteSection.style.display = this.checked ? 'block' : 'none';
    });

    // Show or hide wasteSale-options based on the second checkbox (waste)
    document.getElementById('waste').addEventListener('change', function () {
        var wasteSaleOptions = document.getElementById('wasteSale-options');
        wasteSaleOptions.style.display = this.checked ? 'block' : 'none';
    });


    document.getElementById('waste').addEventListener('change', function () {
        var accountOptions = document.getElementById('wasteSale-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // If you want to use the toggleOptions function for future toggles
    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Correct usage with the appropriate ids
    toggleOptions('waste', 'wasteSale-options');

    //Job Sheet

    
// Toggle main Job Sheet section
document.getElementById('jobSheet').addEventListener('change', function() {
    var jobSheetOptions = document.getElementById('jobSheet-options');
    jobSheetOptions.style.display = this.checked ? 'block' : 'none';
});

// Toggle each section options when checkboxes are checked or unchecked
function toggleOptions(sectionId, optionsId) {
    document.getElementById(sectionId).addEventListener('change', function() {
        var options = document.getElementById(optionsId);
        options.style.display = this.checked ? 'block' : 'none';
    });
}

// Set up toggle for all job sheet options
toggleOptions('job', 'job-options');
toggleOptions('generaljob', 'generaljob-options');
    //Attendance System

    document.getElementById('attendanceSystem').addEventListener('change', function () {
        var attendanceSection = document.getElementById('attendance').parentElement;
        attendanceSection.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('attendance').addEventListener('change', function () {
        var attendanceSaleOptions = document.getElementById('attendanceSystem-options');
        attendanceSaleOptions.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('attendance').addEventListener('change', function () {
        var accountOptions = document.getElementById('attendanceSystem-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }
    toggleOptions('attendance', 'attendanceSystem-options');

    //Gate Ex

    document.getElementById('gateEx').addEventListener('change', function () {
        var gateSection = document.getElementById('gate').parentElement;
        gateSection.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('gate').addEventListener('change', function () {
        var gateSaleOptions = document.getElementById('gateEx-options');
        gateSaleOptions.style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('gate').addEventListener('change', function () {
        var accountOptions = document.getElementById('gateEx-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }
    toggleOptions('gate', 'gateEx-options');


    document.getElementById('productRegistration').addEventListener('change', function () {
        var setupOptions = document.getElementById('productRegistration-options');
        setupOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle the sub-options when 'Product Registration' inside the first options div is checked
    document.getElementById('productRegistrations').addEventListener('change', function () {
        var subOptions = document.getElementById('productRegistration-sub-options');
        subOptions.style.display = this.checked ? 'block' : 'none';
    });






    function toggleOptions(sectionId, optionId) {
        var section = document.getElementById(sectionId);
        if (section) {
            section.addEventListener('change', function () {
                var options = document.getElementById(optionId);
                if (options) {
                    options.style.display = this.checked ? 'block' : 'none';
                }
            });
        }
    }

    // Initialize toggle for 'employee' checkbox to show/hide 'employeeLogOptions'
    toggleOptions('employee', 'employeeLogOptions');







    document.getElementById('setup').addEventListener('change', function () {
        var setupOptions = document.getElementById('setup-options');
        setupOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('countryRegistration', 'countryRegistration-options');
    toggleOptions('erpParameters', 'erpParameters-options');


    document.getElementById('inventory').addEventListener('change', function () {
        var inventoryOptions = document.getElementById('inventory-options');
        inventoryOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('itemType', 'itemType-options');
    toggleOptions('itemRegistration', 'itemRegistration-options');




    document.getElementById('account').addEventListener('change', function () {
        var accountOptions = document.getElementById('account-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('level1', 'level1-options');
    toggleOptions('level2', 'level2-options');
    toggleOptions('chartOfAccount', 'chartOfAccount-options');
    toggleOptions('cashReceipt', 'cashReceipt-options');
    toggleOptions('chequeReceipt', 'chequeReceipt-options');
    toggleOptions('cashPayment', 'cashPayment-options');
    toggleOptions('bankReceipt', 'bankReceipt-options');
    toggleOptions('bankPayment', 'bankPayment-options');
    toggleOptions('ledger', 'ledger-options');
    toggleOptions('officeCash', 'officeCash-options');
    toggleOptions('payables', 'payables-options');
    toggleOptions('receivables', 'receivables-options');
    toggleOptions('journalVoucher', 'journalVoucher-options');
    toggleOptions('openingBalance', 'openingBalance-options');


    document.getElementById('billing').addEventListener('change', function () {
        var accountOptions = document.getElementById('billing-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('pharmaceuticalbilling', 'pharmaceuticalbilling-options');
    toggleOptions('confectionerybilling', 'confectionerybilling-options');
    toggleOptions('generalbilling', 'generalbilling-options');


document.getElementById('wagecalculator').addEventListener('change', function () {
    var accountOptions = document.getElementById('calculator-options');
    accountOptions.style.display = this.checked ? 'block' : 'none';
});

// Toggle each section options when checkboxes are checked or unchecked
function toggleOptions(sectionId, optionsId) {
    document.getElementById(sectionId).addEventListener('change', function () {
        var options = document.getElementById(optionsId);
        options.style.display = this.checked ? 'block' : 'none';
    });
}

// This should be called for nested options if needed
// For example, to show boxboard options when boxboardcalculator is checked:
document.getElementById('boxboardcalculator').addEventListener('change', function() {
    var options = document.getElementById('boxboardcalculator-options');
    options.style.display = this.checked ? 'block' : 'none';
});


    document.getElementById('deliveryChallan').addEventListener('change', function () {
        var accountOptions = document.getElementById('delivery-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('pharmaceuticaldelivery', 'pharmaceuticaldelivery-options');
    toggleOptions('confectionerydelivery', 'confectionerydelivery-options');
    toggleOptions('generaldelivery', 'generaldelivery-options');




    document.getElementById('setupDepartment').addEventListener('change', function () {
        var accountOptions = document.getElementById('setup-department-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('departmentsetup', 'departmentsetup-options');
    toggleOptions('levelsetup', 'levelsetup-options');



    document.getElementById('employeeDepartment').addEventListener('change', function () {
        var accountOptions = document.getElementById('employee-department-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('addemployee', 'addemployee-options');
    toggleOptions('registeremployee', 'registeremployee-options');
    toggleOptions('adddesignation', 'adddesignation-options');
    toggleOptions('bonustype', 'bonustype-options');




    document.getElementById('gatePass').addEventListener('change', function () {
        var accountOptions = document.getElementById('gatePass-options');
        accountOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionsId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionsId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('gatePassin', 'gatePassin-options');
    toggleOptions('gatePassout', 'gatePassout-options');


    document.getElementById('purchase').addEventListener('change', function () {
        var purchaseOptions = document.getElementById('purchase-options');
        purchaseOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle each section options when checkboxes are checked or unchecked
    function toggleOptions(sectionId, optionId) {
        document.getElementById(sectionId).addEventListener('change', function () {
            var options = document.getElementById(optionId);
            options.style.display = this.checked ? 'block' : 'none';
        });
    }

    toggleOptions('boxboard', 'boxboard-options');
    toggleOptions('purchaseReturn', 'purchaseReturn-options');
    toggleOptions('purchasePlate', 'purchasePlate-options');
    toggleOptions('gluePurchase', 'gluePurchase-options');
    toggleOptions('inkPurchase', 'inkPurchase-options');
    toggleOptions('laminationPurchase', 'laminationPurchase-options');
    toggleOptions('corrugationPurchase', 'corrugationPurchase-options');
    toggleOptions('shipperPurchase', 'shipperPurchase-options');
    toggleOptions('dyePurchase', 'dyePurchase-options');












    document.addEventListener('DOMContentLoaded', function () {
        var checkboxContainer = document.getElementById('checkboxContainer');
        var roleSelect = document.getElementById('role');

        // Initially hide the checkbox container
        checkboxContainer.style.display = 'none';

        // Initialize Select2 on role select after DOM is ready
        $('#role').select2();

        // When the value of the role changes, show or hide the checkbox container
        $('#role').on('change', function () {
            if ($(this).val() === 'user') {
                checkboxContainer.style.display = 'block';
            } else {
                checkboxContainer.style.display = 'none';
            }
        });

        // Trigger the change event to set the initial state correctly
        if ($('#role').val() === 'user') {
            checkboxContainer.style.display = 'block';
        }
    });


    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('conformpassword');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const matchMessage = document.getElementById('matchMessage');

    // Toggle visibility for password
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    // Toggle visibility for confirm password
    toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        this.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    // Check if passwords match
    confirmPassword.addEventListener('input', function () {
        if (password.value === confirmPassword.value) {
            matchMessage.textContent = 'Passwords match';
            matchMessage.style.color = 'green';
        } else {
            matchMessage.textContent = 'Passwords do not match';
            matchMessage.style.color = 'red';
        }
    });

    // Check if password meets minimum length
    password.addEventListener('input', function () {
        if (password.value.length >= 8) {
            password.setCustomValidity('');
        } else {
            password.setCustomValidity('Password must be at least 8 characters long');
        }
    });
</script>
@endsection