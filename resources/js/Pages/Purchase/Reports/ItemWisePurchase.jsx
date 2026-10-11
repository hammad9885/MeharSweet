import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function ItemWisePurchase({
    items = [],
    report = [],
    data = [],
    return: returnData = [],
    summarize = 0,
    filters = {}
}) {
    const [itemId, setItemId] = useState(filters.item_id || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');

    const handleFilter = (e, isSummary = 0) => {
        if (e) e.preventDefault();
        router.get('/Puritemwisesale', {
            item_id: itemId,
            start_date: startDate,
            end_date: endDate,
            summarize: isSummary
        }, { preserveState: true });
    };

    const rows = Array.isArray(report) && report.length > 0 ? report : (Array.isArray(data) ? data : []);
    const totalQty = rows.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0);
    const totalAmount = rows.reduce((acc, curr) => acc + ((Number(curr.qty) || 0) * (Number(curr.rate) || 0)), 0);

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 d-print-none">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Item Wise Purchase Report</h3>
                        <p className="text-muted small mb-0">Analysis of raw material purchases and volume per item</p>
                    </div>
                    <button onClick={() => window.print()} className="btn btn-outline-secondary d-flex align-items-center gap-2">
                        <i className="mdi mdi-printer"></i>
                        <span>Print</span>
                    </button>
                </div>

                <div className="card border-0 shadow-sm mb-4 d-print-none">
                    <div className="card-body">
                        <form onSubmit={(e) => handleFilter(e, 0)} className="row g-3">
                            <div className="col-md-4">
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
                            <div className="col-md-2 d-flex gap-2 align-items-end">
                                <button type="submit" className="btn btn-success flex-grow-1">Show</button>
                                <button
                                    type="button"
                                    onClick={(e) => handleFilter(e, 1)}
                                    className="btn btn-outline-primary"
                                >
                                    Summary
                                </button>
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
                                        <th>Item Name</th>
                                        <th className="text-center">Qty</th>
                                        <th className="text-end">Rate</th>
                                        <th className="text-end pe-3">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length > 0 ? (
                                        rows.map((r, i) => (
                                            <tr key={i}>
                                                <td className="ps-3 fw-bold text-primary">#{r.v_no || r.voucher_no}</td>
                                                <td className="small text-muted">{r.v_date || r.date || '-'}</td>
                                                <td>{r.supplier_name || r.supplier?.name || '-'}</td>
                                                <td>{r.item_name || r.item?.product_name || r.item?.item_code || '-'}</td>
                                                <td className="text-center fw-semibold">{r.qty || 0}</td>
                                                <td className="text-end">{Number(r.rate || 0).toFixed(2)}</td>
                                                <td className="text-end pe-3 fw-bold">
                                                    {((Number(r.qty) || 0) * (Number(r.rate) || 0)).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="7" className="text-center py-4 text-muted">
                                                No purchase records found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot>
                                    <tr className="bg-light fw-bold">
                                        <td colSpan="4" className="text-end">Total Purchases:</td>
                                        <td className="text-center">{totalQty}</td>
                                        <td></td>
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
