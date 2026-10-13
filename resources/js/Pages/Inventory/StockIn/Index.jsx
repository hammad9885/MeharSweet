import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({
    masters = [],
    referenceNames = [],
    products = [],
    filters = {}
}) {
    const [invNo, setInvNo] = useState(filters.inv_no || '');
    const [refNameId, setRefNameId] = useState(filters.ref_name_id || '');
    const [productId, setProductId] = useState(filters.product_id || '');
    const [startDate, setStartDate] = useState(filters.startDate || '');
    const [endDate, setEndDate] = useState(filters.endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/stock-in', {
            inv_no: invNo,
            ref_name_id: refNameId,
            product_id: productId,
            startDate: startDate,
            endDate: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setInvNo('');
        setRefNameId('');
        setProductId('');
        setStartDate('');
        setEndDate('');
        router.get('/stock-in');
    };

    const handleDelete = (id, number) => {
        if (confirm(`Are you sure you want to delete Stock In invoice #${number}?`)) {
            router.delete(`/stock-in/${id}`);
        }
    };

    return (
        <AuthenticatedLayout header="Stock In Management">
            <Head title="Stock In - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Stock In</h3>
                        <p className="text-muted small mb-0">Track raw material intake, contractor goods receiving, and inward challans</p>
                    </div>
                    <Link href="/stock-in/create" className="btn btn-primary">
                        <i className="mdi mdi-plus me-1"></i> New Stock In
                    </Link>
                </div>

                {/* Filter Form Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">
                            <i className="mdi mdi-filter-variant me-1 text-primary"></i> Filter Challans
                        </h5>
                    </div>
                    <div className="card-body">
                        <form onSubmit={handleFilter}>
                            <div className="row g-3">
                                <div className="col-md-2">
                                    <label className="form-label small fw-semibold text-muted">Invoice No.</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Inv #"
                                        value={invNo}
                                        onChange={(e) => setInvNo(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Reference / Loader</label>
                                    <select
                                        className="form-select"
                                        value={refNameId}
                                        onChange={(e) => setRefNameId(e.target.value)}
                                    >
                                        <option value="">-- All References --</option>
                                        {referenceNames.map(r => (
                                            <option key={r.id} value={r.id}>{r.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Product</label>
                                    <select
                                        className="form-select"
                                        value={productId}
                                        onChange={(e) => setProductId(e.target.value)}
                                    >
                                        <option value="">-- All Products --</option>
                                        {products.map(p => (
                                            <option key={p.id} value={p.id}>{p.product_name}</option>
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
                                <div className="col-12 d-flex justify-content-end gap-2">
                                    <button type="button" onClick={handleReset} className="btn btn-light border">
                                        Reset
                                    </button>
                                    <button type="submit" className="btn btn-primary px-4">
                                        Filter Records
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Table Card */}
                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">Stock In Inward Register</h5>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4" style={{ width: '120px' }}>Inv No</th>
                                        <th style={{ width: '140px' }}>Date</th>
                                        <th>Reference / Loader</th>
                                        <th className="text-center" style={{ width: '130px' }}>Total Items</th>
                                        <th className="text-center" style={{ width: '130px' }}>Total Qty</th>
                                        <th className="text-center pe-4" style={{ width: '220px' }}>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {masters.length > 0 ? (
                                        masters.map((m) => {
                                            const totalQty = m.details ? m.details.reduce((sum, d) => sum + (parseFloat(d.qty) || 0), 0) : 0;
                                            return (
                                                <tr key={m.id}>
                                                    <td className="ps-4 fw-bold text-primary">#{m.inv_no}</td>
                                                    <td className="text-dark">{m.date}</td>
                                                    <td className="fw-semibold text-dark">{m.ref_name?.name || m.ref_name_id || '-'}</td>
                                                    <td className="text-center">
                                                        <span className="badge bg-secondary-lighten text-secondary px-2 py-1">
                                                            {m.details ? m.details.length : 0} items
                                                        </span>
                                                    </td>
                                                    <td className="text-center fw-bold text-success">
                                                        {totalQty}
                                                    </td>
                                                    <td className="text-center pe-4">
                                                        <div className="d-flex justify-content-center gap-1">
                                                            <Link
                                                                href={`/stock-in/${m.id}/edit`}
                                                                className="btn btn-sm btn-outline-warning"
                                                                title="Edit Challan"
                                                            >
                                                                <i className="mdi mdi-pencil"></i>
                                                            </Link>
                                                            <a
                                                                href={`/stock-in/${m.id}/print`}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="btn btn-sm btn-outline-primary"
                                                                title="Print Slip"
                                                            >
                                                                <i className="mdi mdi-printer"></i>
                                                            </a>
                                                            <button
                                                                onClick={() => handleDelete(m.id, m.inv_no)}
                                                                className="btn btn-sm btn-outline-danger"
                                                                title="Delete Challan"
                                                            >
                                                                <i className="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="6" className="text-center py-4 text-muted">
                                                No stock in records found.
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
