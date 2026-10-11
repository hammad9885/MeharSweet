import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function SupplierWisePurchase({
    data = [],
    startDate = '',
    endDate = '',
    suppliers = [],
    filters = {}
}) {
    const [supplierId, setSupplierId] = useState(filters.supplier_id || '');
    const [sDate, setSDate] = useState(filters.start_date || startDate || '');
    const [eDate, setEDate] = useState(filters.end_date || endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/Supplierwise-report', {
            supplier_id: supplierId,
            start_date: sDate,
            end_date: eDate
        }, { preserveState: true });
    };

    const rows = Array.isArray(data) ? data : [];
    const totalQty = rows.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0);
    const totalAmount = rows.reduce((acc, curr) => acc + (Number(curr.amount) || ((Number(curr.qty) || 0) * (Number(curr.rate) || 0))), 0);

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 d-print-none">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Supplier Wise Purchase Report</h3>
                        <p className="text-muted small mb-0">Purchases and bills categorized by vendor account</p>
                    </div>
                    <button onClick={() => window.print()} className="btn btn-outline-secondary d-flex align-items-center gap-2">
                        <i className="mdi mdi-printer"></i>
                        <span>Print</span>
                    </button>
                </div>

                <div className="card border-0 shadow-sm mb-4 d-print-none">
                    <div className="card-body">
                        <form onSubmit={handleFilter} className="row g-3">
                            <div className="col-md-4">
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
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">Start Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={sDate}
                                    onChange={(e) => setSDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">End Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={eDate}
                                    onChange={(e) => setEDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-2 d-flex align-items-end">
                                <button type="submit" className="btn btn-success w-100">Show</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-bordered table-hover align-middle mb-0">
                                <thead className="bg-light small text-muted text-uppercase">
                                    <tr>
                                        <th className="ps-3">Voucher #</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th className="text-center">Total Qty</th>
                                        <th className="text-end pe-3">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length > 0 ? (
                                        rows.map((r, i) => (
                                            <tr key={i}>
                                                <td className="ps-3 fw-bold text-primary">#{r.v_no || r.voucher_no}</td>
                                                <td className="small text-muted">{r.v_date || r.date || '-'}</td>
                                                <td className="fw-semibold">{r.supplier_name || r.name || '-'}</td>
                                                <td className="text-center fw-semibold">{r.qty || 0}</td>
                                                <td className="text-end pe-3 fw-bold text-success">
                                                    {(Number(r.amount) || ((Number(r.qty) || 0) * (Number(r.rate) || 0))).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="text-center py-4 text-muted">
                                                No supplier records found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot>
                                    <tr className="bg-light fw-bold">
                                        <td colSpan="3" className="text-end">Grand Total:</td>
                                        <td className="text-center">{totalQty}</td>
                                        <td className="text-end pe-3 text-success fs-6">
                                            {totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
