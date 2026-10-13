import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function FinishedGoodStock({
    items = {},
    allItems = [],
    filters = {}
}) {
    const [itemId, setItemId] = useState(filters.item_id || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');
    const [searchQuery, setSearchQuery] = useState('');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/finishgood', {
            item_id: itemId,
            start_date: startDate,
            end_date: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setItemId('');
        setStartDate('');
        setEndDate('');
        router.get('/finishgood');
    };

    const itemsList = Array.isArray(items) ? items : Object.values(items);
    const filteredList = itemsList.filter(item =>
        item && (!searchQuery || (item.item_code && item.item_code.toLowerCase().includes(searchQuery.toLowerCase())))
    );

    return (
        <AuthenticatedLayout header="Finished Goods Stock Report">
            <Head title="Finished Goods Stock Report - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Finished Goods Stock Report</h3>
                        <p className="text-muted small mb-0">Track stock balances for finished confectionery, bakery, and manufactured products</p>
                    </div>
                </div>

                {/* Filter Form Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">
                            <i className="mdi mdi-filter-variant me-1 text-primary"></i> Filter Options
                        </h5>
                    </div>
                    <div className="card-body">
                        <form onSubmit={handleFilter}>
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Select Finished Good</label>
                                    <select
                                        className="form-select"
                                        value={itemId}
                                        onChange={(e) => setItemId(e.target.value)}
                                    >
                                        <option value="">-- All Finished Goods --</option>
                                        {allItems.map(it => (
                                            <option key={it.id} value={it.id}>
                                                {it.item_code}
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
                                <div className="col-md-2 d-flex align-items-end gap-2">
                                    <button type="submit" className="btn btn-success flex-grow-1">
                                        Filter
                                    </button>
                                    <button type="button" onClick={handleReset} className="btn btn-light border">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Report Table Card */}
                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 className="card-title mb-0 fw-semibold">Finished Goods Balances</h5>
                        <div style={{ minWidth: '220px' }}>
                            <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Search product code..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover table-striped align-middle mb-0 text-center">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="text-start ps-4">Item Code</th>
                                        <th>Opening Qty</th>
                                        <th>Sale Qty</th>
                                        <th>Production</th>
                                        <th>Sale Return</th>
                                        <th>Adjustment</th>
                                        <th className="pe-4 text-primary bg-primary-lighten">Current Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredList.length > 0 ? (
                                        filteredList.map((item, idx) => (
                                            <tr key={idx}>
                                                <td className="text-start ps-4 fw-bold text-dark">{item.item_code}</td>
                                                <td>{item.opening_qty}</td>
                                                <td>{item.sale_qty}</td>
                                                <td>{item.production_qty}</td>
                                                <td>{item.sale_return_qty}</td>
                                                <td>{item.adjustment_qty}</td>
                                                <td className="pe-4 fw-bold text-primary bg-primary-lighten fs-6">
                                                    {item.current_stock}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="7" className="text-center py-4 text-muted">
                                                No finished goods stock records found.
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
