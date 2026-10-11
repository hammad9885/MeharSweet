import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({ invoices = [], customers = [], items = [], filters = {} }) {
    const [voucherNo, setVoucherNo] = useState(filters.voucher_no || '');
    const [customerId, setCustomerId] = useState(filters.customer_id || '');
    const [startDate, setStartDate] = useState(filters.startDate || '');
    const [endDate, setEndDate] = useState(filters.endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/sale_return_invoices', {
            voucher_no: voucherNo,
            customer_id: customerId,
            startDate: startDate,
            endDate: endDate
        }, { preserveState: true });
    };

    const handleReset = () => {
        setVoucherNo('');
        setCustomerId('');
        setStartDate('');
        setEndDate('');
        router.get('/sale_return_invoices');
    };

    const handleDelete = (v_no) => {
        if (confirm('Are you sure you want to delete this sale return invoice?')) {
            router.delete(`/sale_return_invoices/${v_no}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Sale Returns</h3>
                        <p className="text-muted small mb-0">Manage customer returned goods, restocking, and credit invoices</p>
                    </div>
                    <a href="/sale_return_invoices/create" className="btn btn-primary d-flex align-items-center gap-2">
                        <i className="mdi mdi-plus-circle font-16"></i>
                        <span>Create Sale Return</span>
                    </a>
                </div>

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
                                <label className="form-label small fw-semibold text-muted">Customer</label>
                                <select
                                    className="form-select"
                                    value={customerId}
                                    onChange={(e) => setCustomerId(e.target.value)}
                                >
                                    <option value="">All Customers</option>
                                    {customers.map((c) => (
                                        <option key={c.acc_id || c.id} value={c.acc_id || c.id}>
                                            {c.name} {c.urdu_title ? `(${c.urdu_title})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">Start Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-3">
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

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">Voucher No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th className="text-center">Items</th>
                                        <th className="text-end">Total Amount</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {invoices.length > 0 ? (
                                        invoices.map((inv) => {
                                            const itemsTotal = (inv.items || []).reduce((acc, curr) => {
                                                return acc + ((Number(curr.qty) || 0) * (Number(curr.rate) || 0)) - (Number(curr.discount) || 0);
                                            }, 0);
                                            const cust = inv.customer || (inv.items && inv.items[0]?.customer) || {};

                                            return (
                                                <tr key={inv.id || inv.v_no}>
                                                    <td className="ps-4 fw-bold text-primary">#{inv.v_no}</td>
                                                    <td className="small text-muted">{inv.v_date || inv.date || '-'}</td>
                                                    <td>
                                                        <div className="fw-semibold text-dark">{cust.name || 'N/A'}</div>
                                                        {cust.urdu_title && (
                                                            <div className="small text-muted" dir="rtl">{cust.urdu_title}</div>
                                                        )}
                                                    </td>
                                                    <td className="text-center">
                                                        <span className="badge bg-secondary-lighten text-secondary px-2">
                                                            {(inv.items || []).length}
                                                        </span>
                                                    </td>
                                                    <td className="text-end fw-bold text-danger">
                                                        {itemsTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="text-center pe-4">
                                                        <div className="d-flex justify-content-center gap-1">
                                                            <a
                                                                href={`/sale_return_invoices/${inv.v_no}/edit`}
                                                                className="btn btn-sm btn-outline-warning"
                                                                title="Edit"
                                                            >
                                                                <i className="mdi mdi-pencil"></i>
                                                            </a>
                                                            <a
                                                                href={`/sale-return-invoices/${inv.v_no}/print`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="btn btn-sm btn-outline-info"
                                                                title="Print"
                                                            >
                                                                <i className="mdi mdi-printer"></i>
                                                            </a>
                                                            <button
                                                                onClick={() => handleDelete(inv.v_no)}
                                                                className="btn btn-sm btn-outline-danger"
                                                                title="Delete"
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
                                            <td colSpan="6" className="text-center py-5 text-muted">
                                                <i className="mdi mdi-keyboard-return font-36 d-block mb-2 text-muted"></i>
                                                No sale return invoices found.
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
