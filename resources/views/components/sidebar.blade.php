<div class="leftside-menu">

    <a href="index.html" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset('/public/assets/images/logo.png') }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('/public/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset('/public/assets/images/logo-dark.png') }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('/public/assets/images/logo-dark-sm.png') }}" alt="small logo">
        </span>
    </a>

    <div class="h-100" id="leftside-menu-container" data-simplebar>

        <ul class="side-nav">

            <li class="side-nav-title">Apps</li>

            {{-- ACCOUNTS --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#accountsMenu" class="side-nav-link">
                    <i class="uil-wallet"></i>
                    <span> Accounts</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="accountsMenu">
                    <ul class="side-nav-second-level">

                        @if (in_array('Level1', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('level1.list') }}">Level1</a></li>
                        @endif

                        @if (in_array('Level2', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('level2.list') }}">Level2</a></li>
                        @endif

                        @if (in_array('Chart Of Account', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('amaster.list') }}">Chart Of Account</a></li>
                        @endif

                        @if (in_array('Cash Payment', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('payment.reports') }}">Cash Payment</a></li>
                        @endif

                        @if (in_array('Cash Receipt', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('cash.reports') }}">Cash Receipt</a></li>
                        @endif

                        @if (in_array('Bank Payment', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('bank_payment.reports') }}">Bank Payment</a></li>
                        @endif

                        @if (in_array('Bank Receipt', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('bank_recipt.reports') }}">Bank Receipt</a></li>
                        @endif

                        @if (in_array('Journal Voucher', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('journal_voucher.reports') }}">Journal Voucher</a></li>
                        @endif

                        @if (in_array('Opening Balance', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('open_bal.reports') }}">Opening Balance</a></li>
                        @endif

                        @if (in_array('Ledger', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('ledger.list') }}">Ledger</a></li>
                        @endif

                        @if (in_array('Cash Book', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('cashbooks.list') }}">Cash Book</a></li>
                        @endif

                        @if (in_array('Payables', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('payables.list') }}">Payables</a></li>
                        @endif

                        @if (in_array('Recieveable', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('recieveables.list') }}">Recieveable</a></li>
                        @endif

                        @if (in_array('Trial Balance', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('trialbalance.index') }}">Trial Balance</a></li>
                        @endif

                    </ul>
                </div>
            </li>


            {{-- SALES --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#saleMenu" class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Sale</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="saleMenu">
                    <ul class="side-nav-second-level">

                        @if (in_array('Sale Invoice', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('sale_invoices.index') }}">Sale Invoice</a></li>
                        @endif
                        @if (in_array('Sale Order', $permissions) || in_array('Sale Invoice', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('sale_order.index') }}">Sale Order</a></li>
                        @endif
                        @if (in_array('Delivery Challan (SOR)', $permissions) || in_array('Sale Invoice', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('sale_order_dc.index') }}">Delivery Challan (SOR)</a></li>
                        @endif
                        @if (auth()->user()->is_admin)
                            <li><a href="{{ route('transport_companies.index') }}">Transport Company</a></li>
                        @endif
                        @if (auth()->user()->is_admin && Route::has('ReferenceNames.index'))
                            <li><a href="{{ route('ReferenceNames.index') }}">Reference Name</a></li>
                        @endif

                        @if (in_array('Sale Return', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('sale_return_invoices.index') }}">Sale Return</a></li>
                        @endif

                        @if (in_array('Item Master', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('raw_sale_invoices.index') }}">Raw Sale Invoice</a></li>
                            <li><a href="{{ route('raw_sale_return_invoices.index') }}">Raw Return Invoice</a></li>
                        @endif
                        @if (in_array('Customer', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('customers.index') }}">Customer</a></li>
                            <li><a href="{{ route('customer-categories.index') }}">Customer Category</a></li>
                        @endif

                        @if (in_array('Item Wise Sale', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('ItemReportbysale.index') }}">Item Wise Sale</a></li>
                        @endif

                        @if (in_array('Category Wise Sale', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('ItemReportbysale.type') }}">Category Wise Sale</a></li>
                        @endif

                        @if (in_array('Customer Wise Sale', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('customerwise.report') }}">Customer Wise Sale</a></li>
                        @endif

                        @if (in_array('Sale Report', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('saleWise.report') }}">Sale Report</a></li>
                        @endif

                    </ul>
                </div>
            </li>


            {{-- PURCHASE --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#purchaseMenu" class="side-nav-link">
                    <i class="uil-envelope"></i>
                    <span> Purchase</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="purchaseMenu">
                    <ul class="side-nav-second-level">

                        @if (in_array('Purchase Invoice', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('purchase_invoices.index') }}">Purchase Invoice</a></li>
                            <li><a href="{{ route('purchase_invoices.report') }}">Item Based Purchase</a></li>
                        @endif

                        @if (in_array('Purchase Return', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('purchase_return_invoices.index') }}">Purchase Return</a></li>
                        @endif

                        @if (in_array('Item Wise Purchase', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('ItemReportbysalePur.index') }}">Item Wise Purchase</a></li>
                        @endif

                        @if (in_array('Category Wise Purchase', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('ItemReportbyPurchase.type') }}">Category Wise Purchase</a></li>
                        @endif

                        @if (in_array('Contractor', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('contractors.index') }}">Contractor</a></li>
                        @endif

                        @if (in_array('Supplier', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                        @endif

                        @if (in_array('Supplier Wise Report', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('Supplierwise.report') }}">Supplier Wise Report</a></li>
                        @endif

                        @if (in_array('Purchase Report', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('PurchaseWise.report') }}">Purchase Report</a></li>
                        @endif

                        @if (in_array('Contractor Lot Posting', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('contractor-lot-posting.index') }}">Contractor Lot Posting</a></li>
                        @endif

                    </ul>
                </div>
            </li>


            {{-- MANUFACTURING --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#manufacturingMenu" class="side-nav-link">
                    <i class="uil-cog"></i>
                    <span> Manufacturing</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="manufacturingMenu">
                    <ul class="side-nav-second-level">

                        @if (in_array('Consumption', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('consumption.index') }}">Consumption</a></li>
                            <li><a href="{{ route('consumption2.index') }}">Consumption 2</a></li>
                        @endif

                        @if (in_array('Production', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('production.index') }}">Production</a></li>
                        @endif

                    </ul>
                </div>
            </li>


            {{-- RAW MATERIAL --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#rawMaterialMenu" class="side-nav-link">
                    <i class="uil-layer-group"></i>
                    <span> Raw Material</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="rawMaterialMenu">
                    <ul class="side-nav-second-level">
                        @if (in_array('Item Category', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('inventory.itemtype.list') }}">Item Types</a></li>
                        @endif

                        @if (in_array('Item Units', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('units.index') }}">Units</a></li>
                        @endif

                        @if (in_array('Item Master', $permissions) || auth()->user()->is_admin)  

                            <li><a href="{{ route('inventory.itemmaster.list') }}">Item Registration</a></li>
                        @endif

                        @if (in_array('Raw Material Report', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('current') }}">Raw Material Report</a></li>
                        @endif
                    </ul>
                </div>
            </li>


            {{-- FINISHED PRODUCT --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#finishedProductMenu" class="side-nav-link">
                    <i class="uil-package"></i>
                    <span> Finished Product</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="finishedProductMenu">
                    <ul class="side-nav-second-level">
                        @if (in_array('Item Category', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('inventory.producttype.list') }}">Product Type</a></li>
                        @endif

                        @if (in_array('Product Groups', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('product-groups.index') }}">Product Group</a></li>
                        @endif

                        @if (in_array('Product Master', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('product.master.list') }}">Product Registration</a></li>
                        @endif

                        @if (in_array('Mold Registration', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('mold.index') }}">Mold Registration</a></li>
                        @endif

                        @if (in_array('Finished Product Report', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('currentp') }}">Finished Product Report</a></li>
                        @endif
                    </ul>
                </div>
            </li>
            
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#stockManagementMenu" class="side-nav-link">
                    <i class="uil-box"></i>
                    <span> Stock Management</span>
                    <span class="menu-arrow"></span>
                </a>

                <div class="collapse" id="stockManagementMenu">
                    <ul class="side-nav-second-level">
                        @if (in_array('Sale Type', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('saletype.index') }}">Sale Type</a></li>
                        @endif

                        @if (in_array('Stock Adjustment', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('stock-adj.index') }}">Stock Adjustment</a></li>
                        @endif

                        @if (in_array('Opening Stock', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('openingStock.index') }}">Opening Stock</a></li>
                        @endif
                        @if (in_array('Stock In', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('stock-in.index') }}">Stock in</a></li>
                            <li><a href="{{ route('lot-in-report.index') }}">Lot In Report</a></li>
                        @endif
                        @if (in_array('Stock Issue', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('stock-issue.index') }}">Stock Issue</a></li>
                        @endif

                        @if (in_array('Stock Value', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('stocks.index') }}">Stock Value</a></li>
                        @endif

                        @if (in_array('Packing Lot Posting', $permissions) || auth()->user()->is_admin)
                            <li><a href="{{ route('packing-lot-posting.index') }}">Packing Lot Posting</a></li>
                        @endif
                    </ul>
                </div>
            </li>

            @if (auth()->user()->is_admin)
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#setupMenu" class="side-nav-link">
                        <i class="uil-schedule"></i>
                        <span>Set-up</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse" id="setupMenu">
                        <ul class="side-nav-second-level">
                            <li><a href="{{ route('erp_param.list') }}">ERP Parameters</a></li>
                            <li><a href="{{ route('userssingle.index') }}">User Management</a></li>
                        </ul>
                    </div>
                </li>
            @endif

        </ul>

    </div>
</div>