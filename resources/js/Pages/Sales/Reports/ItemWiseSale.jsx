import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function ItemWiseSale({
    items = [],
    report = [],
    data = [],
    return: returnData = [],
    summarize = 0,
    filters = {}
}) {
    const [itemId, setItemId] = useState(filters.item_id || '');
    const [startDate, setStartDate] = useState(filters.start_date || new Date().toISOString().split('T')[0]);
    const [endDate, setEndDate] = useState(filters.end_date || new Date().toISOString().split('T')[0]);

    const handleFilter = (e, isSummary = 0) => {
        if (e) e.preventDefault();
        router.get('/itemwisesale', {
            item_id: itemId,
            start_date: startDate,
            end_date: endDate,
            summarize: isSummary
        }, { preserveState: true });
    };

    const handlePrint = () => {
        window.print();
    };

    const rows = Array.isArray(report) && report.length > 0 ? report : (Array.isArray(data) ? data : []);
    const returnRows = Array.isArray(returnData) ? returnData : [];

    const totalQty = rows.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0);
    const totalDis = rows.reduce((acc, curr) => acc + (Number(curr.discount) || 0), 0);
    const totalAmount = rows.reduce((acc, curr) => {
        const q = Number(curr.qty) || 0;
        const r = Number(curr.rate) || 0;
        const d = Number(curr.discount) || 0;
        return acc + ((q * r) - d);
    }, 0);

    const totalRQty = returnRows.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0);
    const totalRDis = returnRows.reduce((acc, curr) => acc + (Number(curr.discount) || 0), 0);
    const totalRAmount = returnRows.reduce((acc, curr) => {
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
                        <h3 className="fw-bold text-dark mb-1">Item Wise Sale Report</h3>
                        <p className="text-muted small mb-0">Detailed analysis of sales volume, turnover, and returns per product</p>
                    </div>
                    <button onClick={handlePrint} className="btn btn-outline-secondary d-flex align-items-center gap-2">
                        <i className="mdi mdi-printer"></i>
                        <span>Print Report</span>
                    </button>
                </div>

                {/* Filter Form */}
                <div className="card border-0 shadow-sm mb-4 d-print-none">
                    <div className="card-body">
                        <form onSubmit={(e) => handleFilter(e, 0)} className="row g-3">
                            <div className="col-md-4">
                                <label className="form-label small fw-semibold text-muted">Item / Product</label>
                                <select
                                    className="form-select"
                                    value={itemId}
                                    onChange={(e) => setItemId(e.target.value)}
                                >
                                    <option value="">All Items</option>
                                    {items.map((it) => (
                                        <option key={it.id} value={it.id}>
                                            {it.item_code ? `${it.item_code} - ` : ''}{it.product_name || it.name} {it.urdu_title ? `(${it.urdu_title})` : ''}
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
                                <button type="submit" className="btn btn-success flex-grow-1">
                                    Show
                                </button>
                                <button
                                    type="button"
                                    onClick={(e) => handleFilter(e, 1)}
                                    className="btn btn-outline-primary"
                                    title="Summarize Report"
                                >
                                    Summary
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Printable Header */}
                <div className="text-center mb-4 d-none d-print-block">
                    <h2 className="fw-bold mb-1">MEHAR SWEETS & BAKERS</h2>
                    <h4 className="text-muted mb-1">Item Wise Sale Report</h4>
                    <p className="small text-muted mb-0">Period: {startDate} to {endDate}</p>
                </div>

                {/* Sales Section */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold text-primary">Sales Details</h5>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-bordered table-hover align-middle mb-0">
                                <thead className="bg-light small text-muted text-uppercase">
                                    <tr>
                                        <th className="ps-3">Voucher No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Item Name</th>
                                        <th className="text-center">Qty</th>
                                        <th className="text-end">Rate</th>
                                        <th className="text-end">Discount</th>
                                        <th className="text-end pe-3">Net Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length > 0 ? (
                                        rows.map((r, i) => {
                                            const q = Number(r.qty) || 0;
                                            const rt = Number(r.rate) || 0;
                                            const disc = Number(r.discount) || 0;
                                            const net = (q * rt) - disc;

                                            return (
                                                <tr key={i}>
                                                    <td className="ps-3 fw-bold text-primary">#{r.v_no || r.voucher_no}</td>
                                                    <td className="small text-muted">{r.v_date || r.date || '-'}</td>
                                                    <td>{r.customer_name || r.customer?.name || '-'}</td>
                                                    <td>{r.item_name || r.item?.product_name || '-'}</td>
                                                    <td className="text-center fw-semibold">{q}</td>
                                                    <td className="text-end">{rt.toFixed(2)}</td>
                                                    <td className="text-end text-danger">{disc > 0 ? disc.toFixed(2) : '0.00'}</td>
                                                    <td className="text-end pe-3 fw-bold">{net.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="8" className="text-center py-4 text-muted">
                                                No sales recorded for the selected parameters.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot>
                                    <tr className="bg-light fw-bold">
                                        <td colSpan="4" className="text-end">Total Sales:</td>
                                        <td className="text-center">{totalQty}</td>
                                        <td></td>
                                        <td className="text-end text-danger">{totalDis.toFixed(2)}</td>
                                        <td className="text-end pe-3 text-success fs-6">
                                            {totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {/* Returns Section */}
                {returnRows.length > 0 && (
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3">
                            <h5 className="card-title mb-0 fw-semibold text-danger">Sales Returns</h5>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered table-hover align-middle mb-0">
                                    <thead className="bg-light small text-muted text-uppercase">
                                        <tr>
                                            <th className="ps-3">Voucher No</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Item Name</th>
                                            <th className="text-center">Qty</th>
                                            <th className="text-end">Rate</th>
                                            <th className="text-end">Discount</th>
                                            <th className="text-end pe-3">Net Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {returnRows.map((r, i) => {
                                            const q = Number(r.qty) || 0;
                                            const rt = Number(r.rate) || 0;
                                            const disc = Number(r.discount) || 0;
                                            const net = (q * rt) - disc;

                                            return (
                                                <tr key={i}>
                                                    <td className="ps-3 fw-bold text-danger">#{r.v_no || r.voucher_no}</td>
                                                    <td className="small text-muted">{r.v_date || r.date || '-'}</td>
                                                    <td>{r.customer_name || r.customer?.name || '-'}</td>
                                                    <td>{r.item_name || r.item?.product_name || '-'}</td>
                                                    <td className="text-center fw-semibold text-danger">{q}</td>
                                                    <td className="text-end">{rt.toFixed(2)}</td>
                                                    <td className="text-end">{disc.toFixed(2)}</td>
                                                    <td className="text-end pe-3 fw-bold text-danger">
                                                        {net.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                    <tfoot>
                                        <tr className="bg-light fw-bold text-danger">
                                            <td colSpan="4" className="text-end">Total Returns:</td>
                                            <td className="text-center">{totalRQty}</td>
                                            <td></td>
                                            <td className="text-end">{totalRDis.toFixed(2)}</td>
                                            <td className="text-end pe-3 fs-6">
                                                -{totalRAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                )}

                {/* Net Summary Box */}
                <div className="card border-0 shadow-sm bg-primary text-white">
                    <div className="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 className="fw-bold mb-1">Net Period Balance</h4>
                            <p className="mb-0 opacity-75">Gross Sales ({totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2 })}) minus Returns ({totalRAmount.toLocaleString(undefined, { minimumFractionDigits: 2 })})</p>
                        </div>
                        <div className="text-end">
                            <span className="fs-2 fw-bold">
                                {(totalAmount - totalRAmount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
