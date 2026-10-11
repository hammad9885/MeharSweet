import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function ItemBasedPurchase({
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
        router.get('/item-based-purchase', {
            voucher_no: voucherNo,
            supplier_id: supplierId,
            item_id: itemId,
            startDate: startDate,
            endDate: endDate
        }, { preserveState: true });
    };

    const handlePrint = () => {
        window.print();
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 d-print-none">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Item Based Purchase Report</h3>
                        <p className="text-muted small mb-0">Detailed breakdown of purchased items across supplier invoices</p>
                    </div>
                    <button onClick={handlePrint} className="btn btn-outline-secondary d-flex align-items-center gap-2">
                        <i className="mdi mdi-printer"></i>
                        <span>Print Report</span>
                    </button>
                </div>

                <div className="card border-0 shadow-sm mb-4 d-print-none">
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
                                <label className="form-label small fw-semibold text-muted">Item</label>
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
                                <button type="submit" className="btn btn-success px-4">Filter</button>
                                <button type="button" onClick={() => router.get('/item-based-purchase')} className="btn btn-light border px-4">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-bordered table-hover align-middle mb-0">
                                <thead className="bg-light small text-muted text-uppercase">
                                    <tr>
                                        <th className="ps-3">Voucher #</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Contractor</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th className="text-center">Qty</th>
                                        <th className="text-end">Rate</th>
                                        <th className="text-end pe-3">Net Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {invoices.length > 0 ? (
                                        invoices.flatMap((inv) =>
                                            (inv.items || []).map((it, idx) => (
                                                <tr key={`${inv.v_no}-${idx}`}>
                                                    <td className="ps-3 fw-bold text-primary">#{inv.v_no}</td>
                                                    <td className="small text-muted">{inv.v_date || '-'}</td>
                                                    <td>{inv.supplier?.name || '-'}</td>
                                                    <td className="small">{inv.contractor?.title || inv.contractor?.name || '-'}</td>
                                                    <td><code>{it.item?.item_code || '-'}</code></td>
                                                    <td>{it.item?.product_name || it.item?.name || '-'}</td>
                                                    <td className="text-center fw-semibold">{it.qty || 0}</td>
                                                    <td className="text-end">{Number(it.rate || 0).toFixed(2)}</td>
                                                    <td className="text-end pe-3 fw-bold">
                                                        {((Number(it.qty) || 0) * (Number(it.rate) || 0) - (Number(it.discount) || 0)).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                                    </td>
                                                </tr>
                                            ))
                                        )
                                    ) : (
                                        <tr>
                                            <td colSpan="9" className="text-center py-4 text-muted">
                                                No item-based purchase records found.
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
