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
                <a href="{{ route('dashboard.admin') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title">Apps</li>


            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarLayouts" aria-expanded="false" aria-controls="sidebarLayouts"
                    class="side-nav-link">
                    <i class="uil-window"></i>
                    <span> Accounts</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarLayouts">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('level1.list') }}">Level1</a>
                        </li>
                        <li>
                            <a href="{{ route('level2.list') }}">Level2</a>
                        </li>
                        <li>
                        <li>
                            <a href="{{ route('amaster.list') }}">Chart Of Account</a>
                        </li>

                        <li>
                            <a href="{{ route('cash.reports') }}">Cash Receipt</a>
                        </li>
                        <li>
                            <a href="{{ route('cheque_receipts.reports') }}">Cheque Receipts</a>
                        </li>
                        <li>
                            <a href="{{ route('payment.reports') }}">Cash Payment</a>
                        </li>
                        <li>
                            <a href="{{ route('bank_recipt.reports') }}">Bank Receipt</a>
                        </li>
                        <li>
                            <a href="{{ route('bank_payment.reports') }}">Bank Payment</a>
                        </li>
                        <li>
                            <a href="{{ route('ledger.list') }}">Ledger</a>
                        </li>
                  
                   <li>
                            <a href="{{ route('expense.reports') }}">Expense Report</a>
                        </li>
                        

                        <li>
                            <a href="{{ route('payables.list') }}">Payables</a>
                        </li>
                        <li>
                            <a href="{{ route('recieveables.list') }}">Recieveable</a>
                        </li>
                        <li>
                            <a href="{{ route('journal_voucher.reports') }}">Journal Voucher</a>
                        </li>
                        <li>
                            <a href="{{ route('open_bal.reports') }}">Opening Balance</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmails" aria-expanded="false" aria-controls="sidebarEmails"
                    class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Billing </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmails">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('pharma_billing.reports') }}">Pharmaceutical Billing</a>
                        </li>
                        <li>
                            <a href="{{ route('confect_billing.reports') }}">Confectionery Billing</a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmao1" aria-expanded="false" aria-controls="sidebarEmao1"
                    class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span>Delivery Challan</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmao1">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('delivery_challan.reports') }}">Pharmaceutical</a>
                        </li>
                        <li>
                            <a href="{{ route('confectionery.reports') }}">Confectionery</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('wastage_sale.reports') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span> Wastage Sale </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm1" aria-expanded="false"
                    aria-controls="sidebarRegistrationForm1" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span>Gate Pass</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm1">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('gate_pass_in.reports') }}">Gate-Pass In</a>
                        </li>
                        <li>
                            <a href="{{ route('gate_pass_out.reports') }}">Gate-Pass Out</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmai" aria-expanded="false" aria-controls="sidebarEmai"
                    class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Purchase</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmai">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('contractors.index') }}">Contractors</a>
                        </li>
                        <li>
                            <a href="{{ route('payment_invoice.reports') }}">Boxboard Purchase </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('plate_purchase.reports') }}">Plate Purchase </a>
                        </li>
                        <li>
                            <a href="{{ route('glue_purchase.reports') }}">Glue Purchase</a>
                        </li>
                        <li>
                            <a href="{{ route('ink_purchase.reports') }}">Ink Purchase</a>
                        </li>
                        <li>
                            <a href="{{ route('lemination_purchase.reports') }}">Lamination Purchase</a>
                        </li>
                        <li>
                            <a href="{{ route('corrugation_purchase.reports') }}">Corrugation Purchase</a>
                        </li>
                        <li>
                            <a href="{{ route('shipper_purchases.reports') }}">Shipper Purchase</a>
                        </li>
                        <li>
                            <a href="{{ route('purchase_return.reports') }}">Purchase Return</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmail" aria-expanded="false" aria-controls="sidebarEmail"
                    class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Inventory </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmail">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('inventory.itemtype.list') }}">Item Type</a>
                        </li>
                        <li>
                            <a href="{{ route('inventory.itemmaster.list') }}">Item Registration</a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('registration_form.reports') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span> Product Registration </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm" aria-expanded="false"
                    aria-controls="sidebarRegistrationForm" class="side-nav-link">
                    <i class="uil-window"></i>
                    <span>Set-up</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('country.index') }}">Country Registration</a>
                        </li>
                        <li>
                            <a href="{{ route('erp_param.list') }}">ERP Parameters</a>
                        </li>
                        <li>
                            <a href="{{ route('product_log.report') }}">Product Log</a>
                        </li>
                        <li>
                            <a href="{{ route('inventory.item_log') }}">Item Registration Log</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('employee.list') }}" class="side-nav-link">
                    <i class="uil-calender"></i>
                    <span> Employee </span>
                </a>
            </li>


            <li class="side-nav-item">
                <a href="{{ route('department.list') }}" class="side-nav-link">
                    <i class="uil-rss"></i>
                    <span> Department </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('category.list') }}" class="side-nav-link">
                    <i class="uil-rss"></i>
                    <span> Category </span>
                </a>
            </li>

            <!-- Help Box -->
            <div class="help-box text-white text-center">
                <a href="javascript: void(0);" class="float-end close-btn text-white">
                    <i class="mdi mdi-close"></i>
                </a>
                <img src="{{ asset('/public/assets/images/svg/help-icon.svg') }}" height="90"
                    alt="Helper Icon Image" />
                <h5 class="mt-3">Unlimited Access</h5>
                <p class="mb-3">Upgrade to plan to get access to unlimited reports</p>
                <a href="javascript: void(0);" class="btn btn-secondary btn-sm">Upgrade</a>
            </div>
            <!-- end Help Box -->


        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>