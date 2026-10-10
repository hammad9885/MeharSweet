@php
    // Preload user permissions ONCE at the top to avoid repeated queries
    $userRights = auth()->check() 
        ? \App\Models\Right::where('user_id', auth()->id())->pluck('app_name')->toArray() 
        : [];
@endphp

<div class="leftside-menu">
    <!-- Brand Logo Light -->
    <a href="index.html" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset('/public/assets/images/logo.png') }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('/public/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset('/public/assets/images/logo-dark.png') }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('/public/assets/images/logo-dark-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="pages-profile.html">
                <img src="{{ asset('/public/assets/images/users/avatar-1.jpg') }}" alt="user-image"
                    height="42" class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">Dominic Keller</span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">
            <li class="side-nav-title">Navigation</li>

            <li class="side-nav-item">
                <a href="{{ route('dashboard.user') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title">Apps</li>

            @if(auth()->check() && auth()->user()->account == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarLayouts" aria-expanded="false" aria-controls="sidebarLayouts" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span> Accounts</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarLayouts">
                    <ul class="side-nav-second-level">
                        @if(in_array('Level1', $userRights))
                            <li><a href="{{ route('level1.list') }}">Level1</a></li>
                        @endif
                        @if(in_array('Level2', $userRights))
                            <li><a href="{{ route('level2.list') }}">Level2</a></li>
                        @endif
                        @if(in_array('ChartOfAccount', $userRights))
                            <li><a href="{{ route('amaster.list') }}">Chart Of Account</a></li>
                        @endif
                        @if(in_array('CashReceipt', $userRights))
                            <li><a href="{{ route('cash.reports') }}">Cash Receipt</a></li>
                        @endif
                        @if(in_array('ChequeReceipt', $userRights))
                            <li><a href="{{ route('cheque_receipts.reports') }}">Cheque Receipts</a></li>
                        @endif
                        @if(in_array('CashPayment', $userRights))
                            <li><a href="{{ route('payment.reports') }}">Cash Payment</a></li>
                        @endif
                        @if(in_array('BankReceipt', $userRights))
                            <li><a href="{{ route('bank_recipt.reports') }}">Bank Receipt</a></li>
                        @endif
                        @if(in_array('BankPayment', $userRights))
                            <li><a href="{{ route('bank_payment.reports') }}">Bank Payment</a></li>
                        @endif
                        @if(in_array('Ledger', $userRights))
                            <li><a href="{{ route('ledger.list') }}">Ledger</a></li>
                        @endif
                        @if(in_array('officeCash', $userRights))
                         <li><a href="{{ route('office_cash.reports') }}">Office Cash</a></li>
                        @endif
                        @if(in_array('Payables', $userRights))
                            <li><a href="{{ route('payables.list') }}">Payables</a></li>
                        @endif
                        @if(in_array('Receivables', $userRights))
                            <li><a href="{{ route('recieveables.list') }}">Receivables</a></li>
                        @endif
                        @if(in_array('JournalVoucher', $userRights))
                            <li><a href="{{ route('journal_voucher.reports') }}">Journal Voucher</a></li>
                        @endif
                        @if(in_array('OpeningBalance', $userRights))
                            <li><a href="{{ route('open_bal.reports') }}">Opening Balance</a></li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->report == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmails2" aria-expanded="false" aria-controls="sidebarEmails2"
                    class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Reports </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmails2">
                    <ul class="side-nav-second-level">
                        @if(in_array('ExpenseReports', $userRights))
                        <li>
                            <a href="{{ route('expense.reports') }}">Expense Report</a>
                        </li> 
                        @endif
                        @if(in_array('PurchaseReports', $userRights))
                        <li>
                            <a href="{{ route('purchase.reports') }}">Purchase Reports</a>
                        </li>
                        @endif
                        @if(in_array('SaleReports', $userRights))
                        <li>
                            <a href="{{ route('sale.reports') }}">Sale Reports</a>
                        </li>
                        @endif
                        @if(in_array('DailyStatement', $userRights))
                        <li>
                             <a href="{{ route('daily_statement.reports') }}">Daily Statement</a>
                        </li>
                        @endif
                        @if(in_array('StockReports', $userRights))
                        <li>
                            <a href="{{ route('report.stock') }}">Stock Reports</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->billing == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmails" aria-expanded="false" aria-controls="sidebarEmails" class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Billing </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmails">
                    <ul class="side-nav-second-level">
                        @if(in_array('pharmaceuticalbilling', $userRights))
                            <li><a href="{{ route('pharma_billing.reports') }}">Pharmaceutical Billing</a></li>
                        @endif
                        @if(in_array('confectionerybilling', $userRights))
                            <li><a href="{{ route('confect_billing.reports') }}">Confectionery Billing</a></li>
                        @endif
                         @if(in_array('generalbilling', $userRights))
                         <li>
                            <a href="{{ route('general_billing.report') }}">General Billing</a>
                        </li>
                         @endif
                    </ul>
                </div>
            </li>
            @endif
            
            
            
            @if(auth()->check() && auth()->user()->wage_calculator == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmails26" aria-expanded="false" aria-controls="sidebarEmails26"
                    class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Wage Calculator </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmails26">
                    <ul class="side-nav-second-level">
                          @if(in_array('boxboardcalculator', $userRights))
                        <li>
                            <a href="{{ route('boxboard_wage.report') }}">Boxboard</a>
                        </li>
                         @endif
                       

                    </ul>
                </div>
            </li>
            @endif
            
            

            @if(auth()->check() && auth()->user()->delivery_challan == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmao1" aria-expanded="false" aria-controls="sidebarEmao1" class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Delivery Challan </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmao1">
                    <ul class="side-nav-second-level">
                        @if(in_array('pharmaceuticaldelivery', $userRights))
                            <li><a href="{{ route('delivery_challan.reports') }}">Pharmaceutical</a></li>
                        @endif
                        @if(in_array('confectionerydelivery', $userRights))
                            <li><a href="{{ route('confectionery.reports') }}">Confectionery</a></li>
                        @endif
                         @if(in_array('generaldelivery', $userRights))
                          <li>
                            <a href="{{ route('general_delivery_challan.report') }}">General</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->waste_sale == 1)
                <li class="side-nav-item">
                    <a href="{{ route('wastage_sale.reports') }}" class="side-nav-link">
                        <i class="uil-calender"></i>
                        <span> Wastage Sale </span>
                    </a>
                </li>
            @endif

            @if(auth()->check() && auth()->user()->gate_ex == 1)
            <li class="side-nav-item">
                <a href="{{ route('gate_ex.reports') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span> Gate Ex </span>
                </a>
            </li>
            @endif    
            
            @if(auth()->check() && auth()->user()->gate_pass == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm1" aria-expanded="false" aria-controls="sidebarRegistrationForm1" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span> Gate Pass </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm1">
                    <ul class="side-nav-second-level">
                        @if(in_array('gatePassin', $userRights))
                            <li><a href="{{ route('gate_pass_in.reports') }}">Gate-Pass In</a></li>
                        @endif
                        @if(in_array('gatePassout', $userRights))
                            <li><a href="{{ route('gate_pass_out.reports') }}">Gate-Pass Out</a></li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->purchase == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmai" aria-expanded="false" aria-controls="sidebarEmai" class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Purchase </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmai">
                    <ul class="side-nav-second-level">
                        @if(in_array('Contractor', $userRights))
                            <li><a href="{{ route('contractors.index') }}">Contractors</a></li>
                        @endif
                        @if(in_array('Boxboard', $userRights))
                            <li><a href="{{ route('payment_invoice.reports') }}">Boxboard Purchase </a></li>
                        @endif
                        @if(in_array('PurchaseReturn', $userRights))
                            <li><a href="{{ route('purchase_return.reports') }}">Purchase Return</a></li>
                        @endif
                        @if(in_array('PurchasePlate', $userRights))
                            <li><a href="{{ route('plate_purchase.reports') }}">Plate Purchase </a></li>
                        @endif
                        @if(in_array('GluePurchase', $userRights))
                            <li><a href="{{ route('glue_purchase.reports') }}">Glue Purchase</a></li>
                        @endif
                        @if(in_array('InkPurchase', $userRights))
                            <li><a href="{{ route('ink_purchase.reports') }}">Ink Purchase</a></li>
                        @endif
                        @if(in_array('LaminationPurchase', $userRights))
                            <li><a href="{{ route('lemination_purchase.reports') }}">Lamination Purchase</a></li>
                        @endif
                        @if(in_array('CorrugationPurchase', $userRights))
                            <li><a href="{{ route('corrugation_purchase.reports') }}">Corrugation Purchase</a></li>
                        @endif
                        @if(in_array('ShipperPurchase', $userRights))
                            <li><a href="{{ route('shipper_purchases.reports') }}">Shipper Purchase</a></li>
                        @endif
                        @if(in_array('dyePurchase', $userRights))
                         <li>
                            <a href="{{ route('dye_purchases.reports') }}">Dye Purchase</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->inventory == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmail" aria-expanded="false" aria-controls="sidebarEmail" class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Inventory </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmail">
                    <ul class="side-nav-second-level">
                        @if(in_array('ItemType', $userRights))
                            <li><a href="{{ route('inventory.itemtype.list') }}">Item Type</a></li>
                        @endif
                        @if(in_array('ItemRegistration', $userRights))
                            <li><a href="{{ route('inventory.itemmaster.list') }}">Item Registration</a></li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->product_registration == 1)
                <li class="side-nav-item">
                    <a href="{{ route('registration_form.reports') }}" class="side-nav-link">
                        <i class="uil-calender"></i>
                        <span> Product Registration </span>
                    </a>
                </li>
            @endif
            
            @if(auth()->check() && auth()->user()->job_sheet == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationFormj" aria-expanded="false"
                    aria-controls="sidebarRegistrationFormj" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span>Job Sheet</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationFormj">
                    <ul class="side-nav-second-level">
                        @if(in_array('jobSheet', $userRights))
                        <li>
                            <a href="{{ route('job.report') }}">Job Sheet</a>
                        </li>
                         @endif
                        @if(in_array('generaljobSheet', $userRights))
                        <li>
                            <a href="{{ route('general_job_sheet.report') }}">General Job Sheet</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            
            @if(auth()->check() && auth()->user()->attendance_system == 1)
             <li class="side-nav-item">
                <a href="{{ route('attendence_form.reports') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span>Attendance System</span>
                </a>
            </li> 
            @endif

            @if(auth()->check() && auth()->user()->setup == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm" aria-expanded="false" aria-controls="sidebarRegistrationForm" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span> Set-up </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm">
                    <ul class="side-nav-second-level">
                        @if(in_array('CountryRegistration', $userRights))
                            <li><a href="{{ route('country.index') }}">Country Registration</a></li>
                        @endif
                        @if(in_array('ERPParameters', $userRights))
                            <li><a href="{{ route('erp_param.list') }}">ERP Parameters</a></li>
                        @endif
                        @if(in_array('ProductLog', $userRights))
                            <li><a href="{{ route('product_log.report') }}">Product Log</a></li>
                        @endif
                        @if(in_array('ItemRegistrationLog', $userRights))
                            <li><a href="{{ route('inventory.item_log') }}">Item Registration Log</a></li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->employee == 1)
                <li class="side-nav-item">
                    <a href="{{ route('employee.list') }}" class="side-nav-link">
                        <i class="uil-calender"></i>
                        <span> Employee </span>
                    </a>
                </li>
            @endif
            
            @if(auth()->check() && auth()->user()->is_admin == 1)
            <li class="side-nav-item">
                <a href="{{ route('category.list') }}" class="side-nav-link">
                    <i class="uil-rss"></i>
                    <span> Category </span>
                </a>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->setup_department == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm2" aria-expanded="false"
                    aria-controls="sidebarRegistrationForm2" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span>Set-Up Department</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm2">
                    <ul class="side-nav-second-level">
                        @if(in_array('departmentsetup', $userRights))
                        <li>
                            <a href="{{ route('print.index') }}">Department</a>
                        </li>
                        @endif
                        @if(in_array('levelsetup', $userRights))
                         <li>
                            <a href="{{ route('process.index') }}">Level2</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            
            @if(auth()->check() && auth()->user()->employee_department == 1)
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm236" aria-expanded="false"
                    aria-controls="sidebarRegistrationForm236" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span>Employee</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm236">
                    <ul class="side-nav-second-level">
                        @if(in_array('addemployee', $userRights))
                        <li>
                            <a href="{{ route('employees.reports') }}">Add Employee</a>
                        </li> 
                        @endif
                        @if(in_array('registeremployee', $userRights))
                         <li>
                            <a href="{{ route('employee_type.reports') }}">Register Employee in New Department</a>
                        </li> 
                        @endif
                        @if(in_array('adddesignation', $userRights))
                         <li>
                            <a href="{{ route('designation.index') }}">Add Designation</a>
                        </li> 
                        @endif
                        @if(in_array('bonustype', $userRights))
                        <li>
                            <a href="{{ route('extra_time.index') }}">Add Bonus Type</a>
                        </li> 
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        </ul>

        <div class="clearfix"></div>
    </div>
</div>