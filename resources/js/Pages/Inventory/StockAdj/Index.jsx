import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ masters = [] }) {
    const [searchTerm, setSearchTerm] = useState('');

    const handleDelete = (id, vNo) => {
        if (confirm(`Are you sure you want to delete adjustment voucher #${vNo}?`)) {
            router.delete(`/stock-adj/${id}`);
        }
    };

    const filtered = masters.filter(m =>
        !searchTerm ||
        (m.v_no && m.v_no.toString().includes(searchTerm)) ||
        (m.v_date && m.v_date.includes(searchTerm))
    );

    return (
        <AuthenticatedLayout header="Stock Adjustments">
            <Head title="Stock Adjustments - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Stock Adjustments</h3>
                        <p className="text-muted small mb-0">Record physical inventory discrepancies, write-offs, or initial stock reconciliations</p>
                    </div>
                    <Link href="/stock-adj/create" className="btn btn-primary">
                        <i className="mdi mdi-plus me-1"></i> New Stock Adjustment
                    </Link>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 className="card-title mb-0 fw-semibold">Adjustment Records</h5>
                        <div style={{ minWidth: '240px' }}>
                            <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Search voucher # or date..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                        </div>
                    </div>

                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4" style={{ width: '120px' }}>Voucher #</th>
                                        <th>Date</th>
                                        <th>Prepared By</th>
                                        <th className="text-center" style={{ width: '120px' }}>Items</th>
                                        <th className="text-center pe-4" style={{ width: '180px' }}>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filtered.length > 0 ? (
                                        filtered.map((m) => (
                                            <tr key={m.id}>
                                                <td className="ps-4 fw-bold text-primary">#{m.v_no}</td>
                                                <td className="text-dark">{m.v_date}</td>
                                                <td className="text-muted">{m.prepared_by || 'Admin'}</td>
                                                <td className="text-center">
                                                    <span className="badge bg-secondary-lighten text-secondary px-2 py-1">
                                                        {m.details ? m.details.length : 0} items
                                                    </span>
                                                </td>
                                                <td className="text-center pe-4">
                                                    <div className="d-flex justify-content-center gap-1">
                                                        <Link
                                                            href={`/stock-adj/${m.id}`}
                                                            className="btn btn-sm btn-outline-info"
                                                            title="View Voucher"
                                                        >
                                                            <i className="mdi mdi-eye"></i>
                                                        </Link>
                                                        <Link
                                                            href={`/stock-adj/${m.id}/edit`}
                                                            className="btn btn-sm btn-outline-warning"
                                                            title="Edit Voucher"
                                                        >
                                                            <i className="mdi mdi-pencil"></i>
                                                        </Link>
                                                        <button
                                                            onClick={() => handleDelete(m.id, m.v_no)}
                                                            className="btn btn-sm btn-outline-danger"
                                                            title="Delete Voucher"
                                                        >
                                                            <i className="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="text-center py-4 text-muted">
                                                No stock adjustment vouchers recorded yet.
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
