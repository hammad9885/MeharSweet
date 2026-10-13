import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Show({ stock_adj }) {
    const handleDelete = () => {
        if (confirm(`Are you sure you want to delete adjustment voucher #${stock_adj.v_no}?`)) {
            router.delete(`/stock-adj/${stock_adj.id}`);
        }
    };

    const details = stock_adj.details || [];
    const grandTotal = details.reduce((sum, d) => sum + ((parseFloat(d.qty) || 0) * (parseFloat(d.rate) || 0)), 0);
    const totalQty = details.reduce((sum, d) => sum + (parseFloat(d.qty) || 0), 0);

    return (
        <AuthenticatedLayout header={`Stock Adjustment #${stock_adj.v_no}`}>
            <Head title={`Adjustment #${stock_adj.v_no} - Real ERP`} />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Stock Adjustment Voucher #{stock_adj.v_no}</h3>
                        <p className="text-muted small mb-0">Recorded on {stock_adj.v_date}</p>
                    </div>
                    <div className="d-flex gap-2">
                        <Link href="/stock-adj" className="btn btn-outline-secondary btn-sm">
                            <i className="mdi mdi-arrow-left me-1"></i> Back to List
                        </Link>
                        <Link href={`/stock-adj/${stock_adj.id}/edit`} className="btn btn-warning btn-sm">
                            <i className="mdi mdi-pencil me-1"></i> Edit
                        </Link>
                        <button onClick={handleDelete} className="btn btn-danger btn-sm">
                            <i className="mdi mdi-delete me-1"></i> Delete
                        </button>
                    </div>
                </div>

                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">Voucher Overview</h5>
                    </div>
                    <div className="card-body">
                        <div className="row g-3">
                            <div className="col-md-4">
                                <span className="text-muted small d-block">Voucher Number</span>
                                <span className="fw-bold text-primary fs-5">#{stock_adj.v_no}</span>
                            </div>
                            <div className="col-md-4">
                                <span className="text-muted small d-block">Voucher Date</span>
                                <span className="fw-semibold text-dark">{stock_adj.v_date}</span>
                            </div>
                            <div className="col-md-4">
                                <span className="text-muted small d-block">Prepared By</span>
                                <span className="fw-semibold text-dark">{stock_adj.prepared_by || 'Admin'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">Adjusted Item Details</h5>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-bordered align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4" style={{ width: '60px' }}>#</th>
                                        <th>Item Code / Title</th>
                                        <th className="text-end" style={{ width: '150px' }}>Adjusted Qty</th>
                                        <th className="text-end" style={{ width: '150px' }}>Rate</th>
                                        <th className="text-end pe-4" style={{ width: '180px' }}>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {details.length > 0 ? (
                                        details.map((d, idx) => {
                                            const subtotal = (parseFloat(d.qty) || 0) * (parseFloat(d.rate) || 0);
                                            return (
                                                <tr key={d.id || idx}>
                                                    <td className="ps-4 text-muted">{idx + 1}</td>
                                                    <td>
                                                        <span className="fw-semibold text-dark">
                                                            {d.item ? d.item.item_code : `Item #${d.item_id}`}
                                                        </span>
                                                        {d.item?.name && (
                                                            <div className="text-muted small">{d.item.name}</div>
                                                        )}
                                                    </td>
                                                    <td className={`text-end fw-semibold ${parseFloat(d.qty) < 0 ? 'text-danger' : 'text-success'}`}>
                                                        {parseFloat(d.qty) > 0 ? `+${d.qty}` : d.qty}
                                                    </td>
                                                    <td className="text-end text-muted">
                                                        {parseFloat(d.rate || 0).toFixed(2)}
                                                    </td>
                                                    <td className="text-end pe-4 fw-semibold text-dark">
                                                        {subtotal.toFixed(2)}
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="text-center py-4 text-muted">
                                                No item details for this voucher.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot className="bg-light fw-bold">
                                    <tr>
                                        <td colSpan="2" className="text-end">Summary:</td>
                                        <td className="text-end text-primary">{totalQty.toFixed(2)}</td>
                                        <td className="text-end">Grand Total:</td>
                                        <td className="text-end pe-4 text-success">{grandTotal.toFixed(2)}</td>
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
