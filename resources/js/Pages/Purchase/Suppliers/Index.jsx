import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({ suppliers = [] }) {
    const [search, setSearch] = useState('');

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this supplier?')) {
            router.delete(`/suppliers/${id}`);
        }
    };

    const filtered = suppliers.filter(s => {
        const term = search.toLowerCase();
        return (
            (s.name && s.name.toLowerCase().includes(term)) ||
            (s.city && s.city.toLowerCase().includes(term)) ||
            (s.mobile && s.mobile.toLowerCase().includes(term)) ||
            (s.urdu_title && s.urdu_title.toLowerCase().includes(term))
        );
    });

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Suppliers</h3>
                        <p className="text-muted small mb-0">Manage raw material vendors, order numbers, contact details, and locations</p>
                    </div>
                    <a href="/suppliers/create" className="btn btn-primary d-flex align-items-center gap-1">
                        <i className="mdi mdi-account-plus font-16"></i>
                        <span>Add Supplier</span>
                    </a>
                </div>

                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body">
                        <div className="row g-2 align-items-center">
                            <div className="col-md-5">
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search by supplier name, city, or phone..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">#</th>
                                        <th>Order #</th>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>Mobile / Phone</th>
                                        <th>Email</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filtered.length > 0 ? (
                                        filtered.map((s, idx) => (
                                            <tr key={s.id}>
                                                <td className="ps-4 fw-semibold text-muted">{idx + 1}</td>
                                                <td>
                                                    <span className="badge bg-light text-dark border">
                                                        {s.order_number || '-'}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div className="fw-semibold text-dark">{s.name || '-'}</div>
                                                    {s.urdu_title && (
                                                        <div className="small text-muted" dir="rtl">{s.urdu_title}</div>
                                                    )}
                                                </td>
                                                <td>{s.city || '-'}</td>
                                                <td>{s.mobile || '-'}</td>
                                                <td className="small text-muted">{s.email || '-'}</td>
                                                <td className="text-center pe-4">
                                                    <div className="d-flex justify-content-center gap-1">
                                                        <a
                                                            href={`/suppliers/${s.id}/edit`}
                                                            className="btn btn-sm btn-outline-warning"
                                                            title="Edit"
                                                        >
                                                            <i className="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button
                                                            onClick={() => handleDelete(s.id)}
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
                                                <i className="mdi mdi-account-off font-36 d-block mb-2 text-muted"></i>
                                                No suppliers found.
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
