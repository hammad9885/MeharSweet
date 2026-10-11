import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({
    invoices = [],
    suppliers = [],
    items = [],
    filters = {}
}) {
    const [voucherNo, setVoucherNo] = useState(filters.voucher_no || '');
    const [supplierId, setSupplierId] = useState(filters.supplier_id || '');
    const [itemId, setItemId] = useState(filters.item_id || '');
    const [startDate, setStartDate] = useState(filters.startDate || '');
    const [endDate, setEndDate] = useState(filters.endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/purchase_invoices', {
            voucher_no: voucherNo,
            supplier_id: supplierId,
            item_id: itemId,
            startDate: startDate,
            endDate: endDate
        }, { preserveState: true });
    };

    const handleReset = () => {
        setVoucherNo('');
        setSupplierId('');
        setItemId('');
        setStartDate('');
        setEndDate('');
        router.get('/purchase_invoices');
    };

    const handleDelete = (v_no) => {
        if (confirm('Are you sure you want to delete this purchase invoice?')) {
            router.delete(`/purchase_invoices/${v_no}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Purchase Invoices</h3>
                        <p className="text-muted small mb-0">Manage incoming vendor bills, raw materials, supplier billing, and freight charges</p>
                    </div>
                    <div className="d-flex gap-2">
                        <a href="/item-based-purchase" className="btn btn-outline-primary d-flex align-items-center gap-1">
                            <i className="mdi mdi-chart-box-outline"></i> Item Based Report
                        </a>
                        <a href="/purchase_invoices/create" className="btn btn-primary d-flex align-items-center gap-1">
                            <i className="mdi mdi-plus-circle font-16"></i>
                            <span>Create New Invoice</span>
                        </a>
                    </div>
                </div>

                {/* Filter Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body">
                        <form onSubmit={handleFilter} className="row g-3">
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">Voucher No</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search by Voucher #"
                                    value={voucherNo}
                                    onChange={(e) => setVoucherNo(e.target.value)}
                                />
                            </div>
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">Supplier</label>
                                <select
                                    className="form-select"
                                    value={supplierId}
                                    onChange={(e) => setSupplierId(e.target.value)}
                                >
                                    <option value="">All Suppliers</option>
                                    {suppliers.map((s) => (
                                        <option key={s.acc_id || s.id} value={s.acc_id || s.id}>
                                            {s.name} {s.urdu_title ? `(${s.urdu_title})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Raw Material Item</label>
                                <select
                                    className="form-select"
                                    value={itemId}
                                    onChange={(e) => setItemId(e.target.value)}
                                >
                                    <option value="">All Items</option>
                                    {items.map((it) => (
                                        <option key={it.id} value={it.id}>
                                            {it.item_code} - {it.product_name || it.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Start Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">End Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                />
                            </div>
                            <div className="col-12 d-flex gap-2 justify-content-end">
                                <button type="submit" className="btn btn-success px-4">
                                    <i className="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <button type="button" onClick={handleReset} className="btn btn-light border px-4">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Table Card */}
                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">Voucher No</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Contractor</th>
                                        <th>Ref No</th>
                                        <th className="text-center">Items</th>
                                        <th className="text-end">Grand Total</th>
                                        <th className="text-end">Discount</th>
                                        <th className="text-end">Freight</th>
                                        <th className="text-end">Net Amount</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {invoices.length > 0 ? (
                                        invoices.map((inv) => {
                                            const totalItems = (inv.items || []).length;
                                            const grandTotal = Number(inv.grand_total) || 0;
                                            const discount = Number(inv.discount) || 0;
                                            const freight = Number(inv.freight) || 0;
                                            const netAmount = Number(inv.net_amount) || (grandTotal - discount + freight);

                                            return (
                                                <tr key={inv.id || inv.v_no}>
                                                    <td className="ps-4 fw-bold text-primary">#{inv.v_no}</td>
                                                    <td className="small text-muted">{inv.v_date || '-'}</td>
                                                    <td>
                                                        <div className="fw-semibold text-dark">{inv.supplier?.name || '-'}</div>
                                                        {inv.supplier?.urdu_title && (
                                                            <div className="small text-muted" dir="rtl">{inv.supplier.urdu_title}</div>
                                                        )}
                                                    </td>
                                                    <td className="small">{inv.contractor?.title || inv.contractor?.name || '-'}</td>
                                                    <td className="small text-muted">{inv.ref_no || '-'}</td>
                                                    <td className="text-center">
                                                        <span className="badge bg-secondary-lighten text-secondary px-2">
                                                            {totalItems}
                                                        </span>
                                                    </td>
                                                    <td className="text-end fw-semibold">
                                                        {grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="text-end text-danger small">
                                                        {discount > 0 ? `-${discount.toLocaleString(undefined, { minimumFractionDigits: 2 })}` : '0.00'}
                                                    </td>
                                                    <td className="text-end text-muted small">
                                                        {freight.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="text-end fw-bold text-success">
                                                        {netAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="text-center pe-4">
                                                        <div className="d-flex justify-content-center gap-1">
                                                            <a
                                                                href={`/purchase_invoices/${inv.v_no}/edit`}
                                                                className="btn btn-sm btn-outline-warning"
                                                                title="Edit Invoice"
                                                            >
                                                                <i className="mdi mdi-pencil"></i>
                                                            </a>
                                                            <a
                                                                href={`/purchase-invoices/${inv.v_no}/print`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="btn btn-sm btn-outline-primary"
                                                                title="Print Invoice"
                                                            >
                                                                <i className="mdi mdi-printer"></i>
                                                            </a>
                                                            <button
                                                                onClick={() => handleDelete(inv.v_no)}
                                                                className="btn btn-sm btn-outline-danger"
                                                                title="Delete Invoice"
                                                            >
                                                                <i className="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="11" className="text-center py-5 text-muted">
                                                <i className="mdi mdi-cart-arrow-down font-36 d-block mb-2 text-muted"></i>
                                                No purchase invoices found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
