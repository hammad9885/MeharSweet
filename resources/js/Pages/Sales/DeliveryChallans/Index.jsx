import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({
    dcs = [],
    customers = [],
    sorExists = [],
    paidDcs = [],
    paidInvoices = {},
    startDate = '',
    endDate = '',
    filters = {}
}) {
    const [voucherNo, setVoucherNo] = useState(filters.voucher_no || '');
    const [customerId, setCustomerId] = useState(filters.customer_id || '');
    const [sDate, setSDate] = useState(filters.startDate || startDate || '');
    const [eDate, setEDate] = useState(filters.endDate || endDate || '');
    const [paidStatus, setPaidStatus] = useState(filters.paid_status || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/sale-order-dc', {
            voucher_no: voucherNo,
            customer_id: customerId,
            startDate: sDate,
            endDate: eDate,
            paid_status: paidStatus
        }, { preserveState: true });
    };

    const handleReset = () => {
        setVoucherNo('');
        setCustomerId('');
        setSDate('');
        setEDate('');
        setPaidStatus('');
        router.get('/sale-order-dc');
    };

    const handleDelete = (id) => {
        if (confirm('Delete this Delivery Challan? This will restore Sale Order quantities.')) {
            router.delete(`/sale-order-dc/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Delivery Challans (SOR)</h3>
                        <p className="text-muted small mb-0">Dispatch management, tracking bilty numbers, and billing clearance</p>
                    </div>
                    <a href="/sale-order-dc/create" className="btn btn-primary d-flex align-items-center gap-2">
                        <i className="mdi mdi-plus-circle font-16"></i>
                        <span>Create Delivery Challan</span>
                    </a>
                </div>

                {/* Filters */}
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
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Start Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={sDate}
                                    onChange={(e) => setSDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">End Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={eDate}
                                    onChange={(e) => setEDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Status</label>
                                <select
                                    className="form-select"
                                    value={paidStatus}
                                    onChange={(e) => setPaidStatus(e.target.value)}
                                >
                                    <option value="">All Statuses</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                </select>
                            </div>
                            <div className="col-12 d-flex gap-2 justify-content-end">
                                <button type="submit" className="btn btn-success px-4">
                                    <i className="mdi mdi-magnify me-1"></i> Search
                                </button>
                                <button type="button" onClick={handleReset} className="btn btn-light border px-4">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Table */}
                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">Voucher No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Sale Order</th>
                                        <th>Prepared By</th>
                                        <th>Party Ref</th>
                                        <th>Bilty No</th>
                                        <th>Transport</th>
                                        <th>Status</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {dcs.length > 0 ? (
                                        dcs.map((dc) => {
                                            const firstDetail = (dc.details || [])[0];
                                            const sorVno = firstDetail ? firstDetail.sale_order_vno : null;
                                            const isPaid = (paidDcs || []).includes(dc.v_no);
                                            const invNo = paidInvoices[dc.v_no] || null;

                                            return (
                                                <tr key={dc.id || dc.v_no}>
                                                    <td className="ps-4 fw-bold text-primary">#{dc.v_no}</td>
                                                    <td className="small text-muted">{dc.v_date || '-'}</td>
                                                    <td>
                                                        <div className="fw-semibold text-dark">{dc.customer?.name || 'N/A'}</div>
                                                        {dc.customer?.urdu_title && (
                                                            <div className="small text-muted" dir="rtl">{dc.customer.urdu_title}</div>
                                                        )}
                                                    </td>
                                                    <td>
                                                        {sorVno ? (
                                                            <span className={sorExists.includes(sorVno) ? 'fw-medium text-dark' : 'text-danger fw-bold'}>
                                                                {sorVno}
                                                                {!sorExists.includes(sorVno) && ' (Deleted)'}
                                                            </span>
                                                        ) : (
                                                            <span className="text-muted small">-</span>
                                                        )}
                                                    </td>
                                                    <td className="small text-muted">{dc.prepared_by || '-'}</td>
                                                    <td className="fw-medium">
                                                        {dc.reference_name?.name || dc.ref_no || '-'}
                                                    </td>
                                                    <td>{dc.bilty_no || '-'}</td>
                                                    <td className="small">{dc.transport_company?.name || '-'}</td>
                                                    <td>
                                                        {isPaid ? (
                                                            <div>
                                                                <span className="badge bg-success-lighten text-success">Paid</span>
                                                                {invNo && <small className="d-block text-muted">{invNo}</small>}
                                                            </div>
                                                        ) : (
                                                            <span className="badge bg-danger-lighten text-danger">Unpaid</span>
                                                        )}
                                                    </td>
                                                    <td className="text-center pe-4">
                                                        <div className="d-flex justify-content-center gap-1">
                                                            {!isPaid ? (
                                                                <a
                                                                    href={`/sale-order-dc/${dc.id}/edit`}
                                                                    className="btn btn-sm btn-outline-warning"
                                                                    title="Edit DC"
                                                                >
                                                                    <i className="mdi mdi-pencil"></i>
                                                                </a>
                                                            ) : (
                                                                <span className="badge bg-light text-muted border py-2" title="Delete Sale Invoice first to edit">
                                                                    Locked
                                                                </span>
                                                            )}
                                                            <a
                                                                href={`/sale_invoices/create?supplier_id=${dc.customer_id}&dc_vno=${dc.v_no}`}
                                                                className="btn btn-sm btn-outline-success"
                                                                title="Create Sale Invoice from this DC"
                                                            >
                                                                <i className="mdi mdi-receipt"></i> Invoice
                                                            </a>
                                                            <a
                                                                href={`/sale-order-dc/${dc.id}/print`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="btn btn-sm btn-outline-info"
                                                                title="Print DC"
                                                            >
                                                                <i className="mdi mdi-printer"></i>
                                                            </a>
                                                            {!isPaid && (
                                                                <button
                                                                    onClick={() => handleDelete(dc.id)}
                                                                    className="btn btn-sm btn-outline-danger"
                                                                    title="Delete DC"
                                                                >
                                                                    <i className="mdi mdi-delete"></i>
                                                                </button>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="10" className="text-center py-5 text-muted">
                                                <i className="mdi mdi-truck-delivery-outline font-36 d-block mb-2 text-muted"></i>
                                                No delivery challans found.
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
