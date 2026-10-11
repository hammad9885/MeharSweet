import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function CategoryWisePurchase({
    itemsTypes = [],
    typeId = '',
    report = [],
    data = [],
    return: returnData = [],
    summarize = 0,
    filters = {}
}) {
    const [selectedType, setSelectedType] = useState(typeId || filters.type_id || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');

    const handleFilter = (e, isSummary = 0) => {
        if (e) e.preventDefault();
        router.get('/typewisePurchase', {
            type_id: selectedType,
            start_date: startDate,
            end_date: endDate,
            summarize: isSummary
        }, { preserveState: true });
    };

    const rows = Array.isArray(report) && report.length > 0 ? report : (Array.isArray(data) ? data : []);
    const returnRows = Array.isArray(returnData) ? returnData : [];

    const totalQty = rows.reduce((acc, curr) => acc + (Number(curr.qty || curr.sale_qty || curr.Purchase_qty) || 0), 0);
    const totalAmount = rows.reduce((acc, curr) => {
        if (curr.sale_amount !== undefined) return acc + Number(curr.sale_amount || 0);
        if (curr.Purchase_amount !== undefined) return acc + Number(curr.Purchase_amount || 0);
        const q = Number(curr.qty) || 0;
        const r = Number(curr.rate) || 0;
        const d = Number(curr.discount) || 0;
        return acc + ((q * r) - d);
    }, 0);

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 d-print-none">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Category Wise Purchase Report</h3>
                        <p className="text-muted small mb-0">Purchases volume and spending broken down by category / item brand</p>
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
                                <label className="form-label small fw-semibold text-muted">Item Category</label>
                                <select
                                    className="form-select"
                                    value={selectedType}
                                    onChange={(e) => setSelectedType(e.target.value)}
                                >
                                    <option value="">All Categories</option>
                                    {itemsTypes.map(t => (
                                        <option key={t.id} value={t.id}>{t.type_title || t.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">From Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">To Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" className="btn btn-primary w-100">
                                    <i className="mdi mdi-filter me-1"></i> Detail
                                </button>
                                <button
                                    type="button"
                                    onClick={(e) => handleFilter(e, 1)}
                                    className="btn btn-secondary w-100"
                                >
                                    Summary
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 className="card-title mb-0 fw-bold">Purchase Summary Table</h5>
                        <span className="badge bg-primary rounded-pill px-3 py-2">
                            Total Records: {rows.length}
                        </span>
                    </div>
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Category / Item</th>
                                    <th className="text-end">Quantity</th>
                                    <th className="text-end">Rate</th>
                                    <th className="text-end">Net Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="text-center py-4 text-muted">
                                            No purchase records found matching criteria.
                                        </td>
                                    </tr>
                                ) : (
                                    rows.map((row, idx) => (
                                        <tr key={idx}>
                                            <td className="text-muted small">{idx + 1}</td>
                                            <td className="fw-medium text-dark">
                                                {row.type || row.item?.item_name || row.item_name || 'N/A'}
                                            </td>
                                            <td className="text-end fw-semibold">
                                                {(Number(row.qty || row.sale_qty || row.Purchase_qty) || 0).toLocaleString()}
                                            </td>
                                            <td className="text-end">
                                                {row.rate ? `Rs. ${Number(row.rate).toLocaleString()}` : '-'}
                                            </td>
                                            <td className="text-end fw-bold text-success">
                                                Rs. {(Number(row.net_amount || row.sale_amount || row.Purchase_amount || ((row.qty || 0) * (row.rate || 0))) || 0).toLocaleString()}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                            {rows.length > 0 && (
                                <tfoot className="table-light fw-bold">
                                    <tr>
                                        <td colSpan="2">Total</td>
                                        <td className="text-end">{totalQty.toLocaleString()}</td>
                                        <td></td>
                                        <td className="text-end text-success">Rs. {totalAmount.toLocaleString()}</td>
                                    </tr>
                                </tfoot>
                            )}
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
