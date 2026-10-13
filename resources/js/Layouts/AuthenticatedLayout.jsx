import React, { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';

export default function AuthenticatedLayout({ header, children }) {
    const { auth = {}, permissions = [] } = usePage().props;
    const user = auth.user || {};

    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [openMenus, setOpenMenus] = useState({});

    const toggleMenu = (menuKey) => {
        setOpenMenus(prev => ({
            ...prev,
            [menuKey]: !prev[menuKey]
        }));
    };

    const hasPermission = (permissionName) => {
        if (user.is_admin) return true;
        return permissions.includes(permissionName);
    };

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <div className="wrapper" style={{ minHeight: '100vh', backgroundColor: '#f5f7fb' }}>
            {/* Topbar */}
            <div className="navbar-custom" style={{
                position: 'fixed',
                top: 0,
                left: sidebarOpen ? '260px' : '70px',
                right: 0,
                height: '70px',
                zIndex: 1001,
                backgroundColor: '#ffffff',
                borderBottom: '1px solid #eef2f7',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                padding: '0 24px',
                transition: 'left 0.3s ease'
            }}>
                <div className="d-flex align-items-center gap-3">
                    <button
                        className="btn btn-sm btn-light border"
                        onClick={() => setSidebarOpen(!sidebarOpen)}
                        title="Toggle Sidebar"
                    >
                        <i className="mdi mdi-menu font-18"></i>
                    </button>
                    <h5 className="m-0 fw-semibold text-dark d-none d-sm-block">
                        {header || 'Dashboard'}
                    </h5>
                </div>

                <div className="d-flex align-items-center gap-3">
                    <span className="badge bg-success-lighten text-success p-2 px-3 fw-semibold">
                        <i className="mdi mdi-database me-1"></i> MySQL Connected
                    </span>

                    {/* User profile dropdown */}
                    <div className="dropdown">
                        <button
                            className="btn btn-light d-flex align-items-center gap-2 border"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <div
                                className="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                style={{ width: '32px', height: '32px', fontSize: '14px' }}
                            >
                                {(user.name || 'User').charAt(0).toUpperCase()}
                            </div>
                            <span className="fw-medium text-dark d-none d-md-inline">{user.name || 'User'}</span>
                            <i className="mdi mdi-chevron-down"></i>
                        </button>
                        <ul className="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <div className="dropdown-header text-muted">
                                    <small>Role: {user.is_admin ? 'Super Admin' : 'Staff'}</small>
                                </div>
                            </li>
                            <li><hr className="dropdown-divider" /></li>
                            <li>
                                <a className="dropdown-item" href="/profile">
                                    <i className="mdi mdi-account-circle me-1"></i> Profile
                                </a>
                            </li>
                            <li>
                                <button className="dropdown-item text-danger" onClick={handleLogout}>
                                    <i className="mdi mdi-logout me-1"></i> Sign Out
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Left Sidebar */}
            <div className="leftside-menu" style={{
                position: 'fixed',
                top: 0,
                bottom: 0,
                left: 0,
                width: sidebarOpen ? '260px' : '70px',
                zIndex: 1002,
                backgroundColor: '#313a46',
                color: '#8391a2',
                overflowY: 'auto',
                transition: 'width 0.3s ease'
            }}>
                {/* Logo */}
                <div style={{
                    height: '70px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: sidebarOpen ? 'flex-start' : 'center',
                    padding: sidebarOpen ? '0 24px' : '0',
                    borderBottom: '1px solid rgba(255,255,255,0.08)',
                    backgroundColor: '#2b333e'
                }}>
                    <Link href="/admin/dashboard" className="text-white text-decoration-none fw-bold fs-5">
                        {sidebarOpen ? 'MEHAR SWEETS ERP' : 'ERP'}
                    </Link>
                </div>

                {/* Navigation items */}
                <div className="p-3">
                    <ul className="nav flex-column gap-1">
                        {/* Dashboard Link */}
                        <li className="nav-item">
                            <Link
                                href={user.is_admin ? '/admin/dashboard' : '/user/dashboard'}
                                className="nav-link text-white-50 px-2 py-2 rounded d-flex align-items-center gap-2 hover-light"
                                style={{ transition: 'all 0.2s' }}
                            >
                                <i className="mdi mdi-view-dashboard font-18 text-primary"></i>
                                {sidebarOpen && <span>Dashboard</span>}
                            </Link>
                        </li>

                        {/* Accounts Section */}
                        {sidebarOpen && (
                            <li className="nav-item mt-2">
                                <div
                                    className="d-flex align-items-center justify-content-between text-white-50 px-2 py-2 cursor-pointer"
                                    onClick={() => toggleMenu('accounts')}
                                    style={{ cursor: 'pointer' }}
                                >
                                    <span><i className="mdi mdi-cash-multiple me-2 text-warning"></i> Accounts</span>
                                    <i className={`mdi ${openMenus.accounts ? 'mdi-chevron-down' : 'mdi-chevron-right'}`}></i>
                                </div>
                                {openMenus.accounts && (
                                    <ul className="nav flex-column ms-3 gap-1 border-start ps-2">
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/account/level1">Level 1</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/account/level2">Level 2</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/account/a_master">Chart of Account</a></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/paymentreports">Cash Payment</Link></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/cash/reports">Cash Receipt</Link></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/bank_payment/reports">Bank Payment</Link></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/bank_recipt/reports">Bank Receipt</Link></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/journal_voucher/reports">Journal Voucher</Link></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/office_cash/reports">Office Cash</Link></li>
                                    </ul>
                                )}
                            </li>
                        )}

                        {/* Inventory Section */}
                        {sidebarOpen && (
                            <li className="nav-item">
                                <div
                                    className="d-flex align-items-center justify-content-between text-white-50 px-2 py-2 cursor-pointer"
                                    onClick={() => toggleMenu('inventory')}
                                    style={{ cursor: 'pointer' }}
                                >
                                    <span><i className="mdi mdi-package-variant-closed me-2 text-info"></i> Inventory</span>
                                    <i className={`mdi ${openMenus.inventory ? 'mdi-chevron-down' : 'mdi-chevron-right'}`}></i>
                                </div>
                                {openMenus.inventory && (
                                    <ul className="nav flex-column ms-3 gap-1 border-start ps-2">
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/inventory/itemmaster">Item Master</Link></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/inventory/itemtype">Item Type</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/units">Units</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/stocks">Production Stock</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/finishgood">Finished Good</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/stock-adj">Stock Adjustment</a></li>
                                    </ul>
                                )}
                            </li>
                        )}

                        {/* Sales Section */}
                        {sidebarOpen && (
                            <li className="nav-item">
                                <div
                                    className="d-flex align-items-center justify-content-between text-white-50 px-2 py-2 cursor-pointer"
                                    onClick={() => toggleMenu('sales')}
                                    style={{ cursor: 'pointer' }}
                                >
                                    <span><i className="mdi mdi-cart-outline me-2 text-success"></i> Sales</span>
                                    <i className={`mdi ${openMenus.sales ? 'mdi-chevron-down' : 'mdi-chevron-right'}`}></i>
                                </div>
                                {openMenus.sales && (
                                    <ul className="nav flex-column ms-3 gap-1 border-start ps-2">
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/sale_invoices">Sale Invoice</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/sale-order">Sale Order</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/sale-order-dc">Delivery Challan (SOR)</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/sale_return_invoices">Sale Return</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/customers">Customers</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/customer-categories">Customer Categories</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/transport_companies">Transport Companies</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/wastage_sale">Wastage Sale</a></li>
                                    </ul>
                                )}
                            </li>
                        )}

                        {/* Purchase Section */}
                        {sidebarOpen && (
                            <li className="nav-item">
                                <div
                                    className="d-flex align-items-center justify-content-between text-white-50 px-2 py-2 cursor-pointer"
                                    onClick={() => toggleMenu('purchase')}
                                    style={{ cursor: 'pointer' }}
                                >
                                    <span><i className="mdi mdi-cart-arrow-down me-2 text-warning"></i> Purchase</span>
                                    <i className={`mdi ${openMenus.purchase ? 'mdi-chevron-down' : 'mdi-chevron-right'}`}></i>
                                </div>
                                {openMenus.purchase && (
                                    <ul className="nav flex-column ms-3 gap-1 border-start ps-2">
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/purchase_invoices">Purchase Invoice</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/item-based-purchase">Item Based Purchase</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/purchase_return_invoices">Purchase Return</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/Puritemwisesale">Item Wise Purchase</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/typewisePurchase">Category Wise Purchase</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/contractors">Contractor</a></li>
                                        <li className="nav-item"><Link className="nav-link py-1 text-white-50 small" href="/suppliers">Supplier</Link></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/supplierwise-report">Supplier Wise Report</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/Purchasewise-report">Purchase Report</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/contractor-lot-posting">Contractor Lot Posting</a></li>
                                    </ul>
                                )}
                            </li>
                        )}

                        {/* Manufacturing Section */}
                        {sidebarOpen && (
                            <li className="nav-item">
                                <div
                                    className="d-flex align-items-center justify-content-between text-white-50 px-2 py-2 cursor-pointer"
                                    onClick={() => toggleMenu('manufacturing')}
                                    style={{ cursor: 'pointer' }}
                                >
                                    <span><i className="mdi mdi-cog-outline me-2 text-info"></i> Manufacturing</span>
                                    <i className={`mdi ${openMenus.manufacturing ? 'mdi-chevron-down' : 'mdi-chevron-right'}`}></i>
                                </div>
                                {openMenus.manufacturing && (
                                    <ul className="nav flex-column ms-3 gap-1 border-start ps-2">
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/consumption">Consumption</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/consumption2">Consumption 2</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/production">Production</a></li>
                                    </ul>
                                )}
                            </li>
                        )}

                        {/* Reports Section */}
                        {sidebarOpen && (
                            <li className="nav-item">
                                <div
                                    className="d-flex align-items-center justify-content-between text-white-50 px-2 py-2 cursor-pointer"
                                    onClick={() => toggleMenu('reports')}
                                    style={{ cursor: 'pointer' }}
                                >
                                    <span><i className="mdi mdi-file-chart-outline me-2 text-primary"></i> Reports</span>
                                    <i className={`mdi ${openMenus.reports ? 'mdi-chevron-down' : 'mdi-chevron-right'}`}></i>
                                </div>
                                {openMenus.reports && (
                                    <ul className="nav flex-column ms-3 gap-1 border-start ps-2">
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/itemwisesale">Item Wise Sale</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/typewisesale">Category Wise Sale</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/customerwise-report">Customer Wise Sale</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/salewise-report">Sale Report</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/trialreport">Trial Balance</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/ledger">General Ledger</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/daily_statement/reports">Daily Statement</a></li>
                                        <li className="nav-item"><a className="nav-link py-1 text-white-50 small" href="/expense/reports">Expense Reports</a></li>
                                    </ul>
                                )}
                            </li>
                        )}

                        {/* React Showcase */}
                        <li className="nav-item mt-3 pt-3 border-top border-secondary">
                            <a
                                href="/react"
                                className="nav-link text-white-50 px-2 py-2 rounded d-flex align-items-center gap-2"
                            >
                                <i className="mdi mdi-react font-18 text-info"></i>
                                {sidebarOpen && <span>React Overview</span>}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {/* Main Content Page */}
            <div className="content-page" style={{
                marginLeft: sidebarOpen ? '260px' : '70px',
                paddingTop: '70px',
                minHeight: '100vh',
                transition: 'margin-left 0.3s ease',
                display: 'flex',
                flexDirection: 'column'
            }}>
                <div className="content p-4" style={{ flex: 1 }}>
                    {children}
                </div>

                {/* Footer */}
                <footer className="footer bg-white border-top py-3 px-4 text-muted small text-center text-sm-start d-flex justify-content-between">
                    <div>© {new Date().getFullYear()} Real ERP. All rights reserved.</div>
                    <div>Powered by Laravel + React</div>
                </footer>
            </div>
        </div>
    );
}
