import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({
    masters = [],
    products = [],
    contractors = [],
    filters = {}
}) {
    const [invNo, setInvNo] = useState(filters.inv_no || '');
    const [productId, setProductId] = useState(filters.product_id || '');
    const [contractorId, setContractorId] = useState(filters.contractor_id || '');
    const [startDate, setStartDate] = useState(filters.startDate || '');
    const [endDate, setEndDate] = useState(filters.endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/stock-issue', {
            inv_no: invNo,
            product_id: productId,
            contractor_id: contractorId,
            startDate: startDate,
            endDate: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setInvNo('');
        setProductId('');
        setContractorId('');
        setStartDate('');
        setEndDate('');
        router.get('/stock-issue');
    };

    const handleDelete = (id, number) => {
        if (confirm(`Are you sure you want to delete Stock Issue invoice #${number}?`)) {
            router.delete(`/stock-issue/${id}`);
        }
    };

    return (
        <AuthenticatedLayout header="Stock Issue Management">
            <Head title="Stock Issue - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Stock Issue</h3>
                        <p className="text-muted small mb-0">Record outward issuance of materials, packing items, and lot products to contractors</p>
                    </div>
                    <Link href="/stock-issue/create" className="btn btn-primary">
                        <i className="mdi mdi-plus me-1"></i> New Stock Issue
                    </Link>
                </div>

                {/* Filter Form Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">
                            <i className="mdi mdi-filter-variant me-1 text-primary"></i> Filter Issuance Records
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
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Contractor</label>
                                    <select
                                        className="form-select"
                                        value={contractorId}
                                        onChange={(e) => setContractorId(e.target.value)}
                                    >
                                        <option value="">-- All Contractors --</option>
                                        {contractors.map(c => (
                                            <option key={c.id} value={c.id}>{c.title}</option>
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
                        <h5 className="card-title mb-0 fw-semibold">Stock Issue Register</h5>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4" style={{ width: '120px' }}>Inv No</th>
                                        <th style={{ width: '140px' }}>Date</th>
                                        <th>Product</th>
                                        <th>Contractor</th>
                                        <th className="text-center" style={{ width: '130px' }}>Total Items</th>
                                        <th className="text-center" style={{ width: '130px' }}>Total Qty</th>
                                        <th className="text-center pe-4" style={{ width: '160px' }}>Actions</th>
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
                                                    <td className="fw-semibold text-dark">{m.product?.product_name || '-'}</td>
                                                    <td className="text-muted">{m.contractor?.title || '-'}</td>
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
                                                                href={`/stock-issue/${m.id}/edit`}
                                                                className="btn btn-sm btn-outline-warning"
                                                                title="Edit Challan"
                                                            >
                                                                <i className="mdi mdi-pencil"></i>
                                                            </Link>
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
                                            <td colSpan="7" className="text-center py-4 text-muted">
                                                No stock issue records found.
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
