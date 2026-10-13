import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function ProductionStock({
    items = {},
    allitems = [],
    filters = {}
}) {
    const [itemId, setItemId] = useState(filters.item_id || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');
    const [searchQuery, setSearchQuery] = useState('');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/stocks', {
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
        router.get('/stocks');
    };

    // Convert items object/array to array
    const itemsList = Array.isArray(items) ? items : Object.values(items);
    const filteredList = itemsList.filter(item =>
        !searchQuery ||
        (item.item_code && item.item_code.toLowerCase().includes(searchQuery.toLowerCase()))
    );

    return (
        <AuthenticatedLayout header="Production Stock Summary">
            <Head title="Production Stock Summary - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Production Stock Summary</h3>
                        <p className="text-muted small mb-0">Detailed material inventory movements across opening, purchase, adjustment, production, and consumption</p>
                    </div>
                </div>

                {/* Filter Form Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">
                            <i className="mdi mdi-filter-variant me-1 text-primary"></i> Filter Criteria
                        </h5>
                    </div>
                    <div className="card-body">
                        <form onSubmit={handleFilter}>
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Select Item</label>
                                    <select
                                        className="form-select"
                                        value={itemId}
                                        onChange={(e) => setItemId(e.target.value)}
                                    >
                                        <option value="">-- All Items --</option>
                                        {allitems.map(it => (
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
                                    <button type="submit" className="btn btn-primary flex-grow-1">
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
                        <h5 className="card-title mb-0 fw-semibold">Stock Movements Ledger</h5>
                        <div style={{ minWidth: '220px' }}>
                            <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Search item code..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-bordered table-hover align-middle mb-0 text-center">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th rowSpan="2" className="text-start ps-3" style={{ minWidth: '160px' }}>Item Code</th>
                                        <th colSpan="2">Opening Stock</th>
                                        <th colSpan="2">Net Purchase</th>
                                        <th colSpan="2">Adjustment</th>
                                        <th colSpan="2">Net Sale</th>
                                        <th colSpan="2">Production</th>
                                        <th colSpan="2">Consumed</th>
                                        <th colSpan="2" className="bg-primary-lighten text-primary">Closing Stock</th>
                                    </tr>
                                    <tr>
                                        <th>Qty</th><th>Amt</th>
                                        <th>Qty</th><th>Amt</th>
                                        <th>Qty</th><th>Amt</th>
                                        <th>Qty</th><th>Amt</th>
                                        <th>Qty</th><th>Amt</th>
                                        <th>Qty</th><th>Amt</th>
                                        <th className="bg-primary-lighten text-primary">Qty</th>
                                        <th className="bg-primary-lighten text-primary">Amt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredList.length > 0 ? (
                                        filteredList.map((st, idx) => {
                                            const netPurQty = (parseFloat(st.purchase_qty) || 0) + (parseFloat(st.purchase_return_qty) || 0);
                                            const netPurAmt = (parseFloat(st.purchase_amount) || 0) + (parseFloat(st.purchase_return_amount) || 0);
                                            const netSaleQty = (parseFloat(st.sale_qty) || 0) + (parseFloat(st.sale_return_qty) || 0);
                                            const netSaleAmt = (parseFloat(st.sale_amount) || 0) + (parseFloat(st.sale_return_amount) || 0);

                                            return (
                                                <tr key={idx}>
                                                    <td className="text-start ps-3 fw-bold text-dark">{st.item_code}</td>
                                                    <td>{st.opening_qty}</td>
                                                    <td>{(parseFloat(st.opening_amount) || 0).toFixed(2)}</td>
                                                    <td>{netPurQty}</td>
                                                    <td>{netPurAmt.toFixed(2)}</td>
                                                    <td>{st.adjustment_qty}</td>
                                                    <td>{(parseFloat(st.adjustment_amount) || 0).toFixed(2)}</td>
                                                    <td>{netSaleQty}</td>
                                                    <td>{netSaleAmt.toFixed(2)}</td>
                                                    <td>{st.prod_qty}</td>
                                                    <td>{(parseFloat(st.prod_amount) || 0).toFixed(2)}</td>
                                                    <td>{st.consumed_qty}</td>
                                                    <td>{(parseFloat(st.consumed_amount) || 0).toFixed(2)}</td>
                                                    <td className="fw-bold text-primary bg-primary-lighten">{st.closing_qty}</td>
                                                    <td className="fw-bold text-primary bg-primary-lighten">{(parseFloat(st.closing_amt) || 0).toFixed(2)}</td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="15" className="text-center py-4 text-muted">
                                                No stock movements found matching filter criteria.
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
