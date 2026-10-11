import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({
    productions = [],
    vNoList = [],
    items = [],
    filters = {}
}) {
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');
    const [vNo, setVNo] = useState(filters.v_no || '');
    const [itemId, setItemId] = useState(filters.item_id || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/production', {
            start_date: startDate,
            end_date: endDate,
            v_no: vNo,
            item_id: itemId
        }, { preserveState: true });
    };

    const handleReset = () => {
        setStartDate('');
        setEndDate('');
        setVNo('');
        setItemId('');
        router.get('/production');
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this production entry?')) {
            router.delete(`/production/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Production Records</h3>
                        <p className="text-muted small mb-0">Record manufactured batches, product quantities, and production voucher outputs</p>
                    </div>
                    <a href="/production/create" className="btn btn-primary d-flex align-items-center gap-1">
                        <i className="mdi mdi-plus-circle font-16"></i>
                        <span>Add New Production</span>
                    </a>
                </div>

                {/* Filters */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body">
                        <form onSubmit={handleFilter} className="row g-3 align-items-end">
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
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Voucher No</label>
                                <select
                                    className="form-select"
                                    value={vNo}
                                    onChange={(e) => setVNo(e.target.value)}
                                >
                                    <option value="">All Vouchers</option>
                                    {vNoList.map((vn, idx) => (
                                        <option key={idx} value={vn}>{vn}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Product</label>
                                <select
                                    className="form-select"
                                    value={itemId}
                                    onChange={(e) => setItemId(e.target.value)}
                                >
                                    <option value="">All Products</option>
                                    {items.map(item => (
                                        <option key={item.id} value={item.id}>
                                            {item.item_code}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2 d-flex gap-2">
                                <button type="submit" className="btn btn-primary flex-fill">
                                    <i className="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <button type="button" onClick={handleReset} className="btn btn-light border flex-fill">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Data Table */}
                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 className="card-title mb-0 fw-bold">Production Output History</h5>
                        <span className="badge bg-primary rounded-pill px-3 py-2">
                            Total Records: {productions.length}
                        </span>
                    </div>
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th style={{ width: '120px' }}>Voucher No</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th className="text-end">Total Amount</th>
                                    <th className="text-end" style={{ width: '180px' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {productions.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="text-center py-4 text-muted">
                                            No production records found.
                                        </td>
                                    </tr>
                                ) : (
                                    productions.map((prod) => (
                                        <tr key={prod.id}>
                                            <td>
                                                <span className="badge bg-light text-primary border font-13 fw-semibold">
                                                    #{prod.v_no}
                                                </span>
                                            </td>
                                            <td>{prod.v_date || '-'}</td>
                                            <td className="text-muted">{prod.descr || 'Standard Batch Production'}</td>
                                            <td className="text-end fw-bold text-success">
                                                Rs. {Number(prod.amount || 0).toLocaleString()}
                                            </td>
                                            <td className="text-end">
                                                <div className="btn-group btn-group-sm">
                                                    <a
                                                        href={`/production/${prod.id}/edit`}
                                                        className="btn btn-outline-warning"
                                                        title="Edit"
                                                    >
                                                        <i className="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a
                                                        href={`/production/${prod.id}/print`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="btn btn-outline-primary"
                                                        title="Print"
                                                    >
                                                        <i className="mdi mdi-printer"></i>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleDelete(prod.id)}
                                                        className="btn btn-outline-danger"
                                                        title="Delete"
                                                    >
                                                        <i className="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
