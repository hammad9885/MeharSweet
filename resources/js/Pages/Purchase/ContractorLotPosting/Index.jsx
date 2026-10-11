import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({
    postings = [],
    products = [],
    contractors = [],
    filters = {}
}) {
    const [voucherNo, setVoucherNo] = useState(filters.voucher_no || '');
    const [productId, setProductId] = useState(filters.product_id || '');
    const [contractorId, setContractorId] = useState(filters.contractor_id || '');
    const [startDate, setStartDate] = useState(filters.startDate || '');
    const [endDate, setEndDate] = useState(filters.endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/contractor-lot-posting', {
            voucher_no: voucherNo,
            product_id: productId,
            contractor_id: contractorId,
            startDate: startDate,
            endDate: endDate
        }, { preserveState: true });
    };

    const handleReset = () => {
        setVoucherNo('');
        setProductId('');
        setContractorId('');
        setStartDate('');
        setEndDate('');
        router.get('/contractor-lot-posting');
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this lot posting?')) {
            router.delete(`/contractor-lot-posting/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Contractor Lot Posting</h3>
                        <p className="text-muted small mb-0">Record and track finished lots and raw material issuances to contractors</p>
                    </div>
                    <a href="/contractor-lot-posting/create" className="btn btn-primary d-flex align-items-center gap-1">
                        <i className="mdi mdi-plus-circle font-16"></i>
                        <span>Create Lot Posting</span>
                    </a>
                </div>

                <div className="card border-0 shadow-sm mb-4">
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
                                <label className="form-label small fw-semibold text-muted">Product</label>
                                <select
                                    className="form-select"
                                    value={productId}
                                    onChange={(e) => setProductId(e.target.value)}
                                >
                                    <option value="">All Products</option>
                                    {products.map((p) => (
                                        <option key={p.id} value={p.id}>{p.product_name || p.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Contractor</label>
                                <select
                                    className="form-select"
                                    value={contractorId}
                                    onChange={(e) => setContractorId(e.target.value)}
                                >
                                    <option value="">All Contractors</option>
                                    {contractors.map((c) => (
                                        <option key={c.id} value={c.id}>{c.title || c.name}</option>
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
                                <button type="submit" className="btn btn-success px-4">
                                    <i className="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <button type="button" onClick={handleReset} className="btn btn-light border px-4">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">Voucher No</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Contractor</th>
                                        <th className="text-center">Quantity</th>
                                        <th>Lot No</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {postings.length > 0 ? (
                                        postings.map((p) => (
                                            <tr key={p.id}>
                                                <td className="ps-4 fw-bold text-primary">#{p.v_no}</td>
                                                <td className="small text-muted">{p.v_date || '-'}</td>
                                                <td className="fw-semibold text-dark">{p.product?.product_name || '-'}</td>
                                                <td>{p.contractor?.title || p.contractor?.name || '-'}</td>
                                                <td className="text-center fw-bold">{p.qty || '-'}</td>
                                                <td>
                                                    <span className="badge bg-light text-dark border">
                                                        {p.lot_no || '-'}
                                                    </span>
                                                </td>
                                                <td className="text-center pe-4">
                                                    <div className="d-flex justify-content-center gap-1">
                                                        <a
                                                            href={`/contractor-lot-posting/${p.id}/edit`}
                                                            className="btn btn-sm btn-outline-warning"
                                                            title="Edit"
                                                        >
                                                            <i className="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button
                                                            onClick={() => handleDelete(p.id)}
                                                            className="btn btn-sm btn-outline-danger"
                                                            title="Delete"
                                                        >
                                                            <i className="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="7" className="text-center py-5 text-muted">
                                                <i className="mdi mdi-clipboard-text-outline font-36 d-block mb-2 text-muted"></i>
                                                No contractor lot postings found.
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
