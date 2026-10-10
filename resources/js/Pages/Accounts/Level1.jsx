import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Level1({ level1s = [] }) {
    const [searchTerm, setSearchTerm] = useState('');

    const filtered = level1s.filter(item =>
        !searchTerm ||
        (item.title && item.title.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (item.groups?.title && item.groups.title.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (item.level1_code && String(item.level1_code).includes(searchTerm))
    );

    const handleDelete = (id, title) => {
        if (confirm(`Are you sure you want to delete Level 1 account "${title}"?`)) {
            router.delete(`/account/level1/${id}`, {
                preserveScroll: true,
            });
        }
    };

    return (
        <AuthenticatedLayout header="Level 1 Accounts">
            <Head title="Level 1 Accounts - GST ERP" />

            <div className="card shadow-sm border-0">
                <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div className="d-flex gap-2">
                        <a href="/account/level1/create" className="btn btn-primary no-print">
                            <i className="mdi mdi-plus-circle me-1"></i> Add Level 1
                        </a>
                        <button type="button" className="btn btn-outline-secondary no-print" onClick={() => window.print()}>
                            <i className="mdi mdi-printer me-1"></i> Print Table
                        </button>
                    </div>

                    <div className="no-print" style={{ minWidth: '240px' }}>
                        <div className="input-group input-group-sm">
                            <span className="input-group-text bg-light border-end-0">
                                <i className="mdi mdi-magnify"></i>
                            </span>
                            <input
                                type="text"
                                className="form-control bg-light border-start-0"
                                placeholder="Search Level 1 accounts..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                        </div>
                    </div>
                </div>

                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover table-striped align-middle mb-0">
                            <thead className="table-light text-uppercase small text-muted">
                                <tr>
                                    <th>A/C Code</th>
                                    <th>Level 1 Title</th>
                                    <th>Group Title</th>
                                    <th className="text-center no-print" style={{ width: '130px' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length > 0 ? (
                                    filtered.map((item) => (
                                        <tr key={item.id}>
                                            <td className="fw-semibold text-primary">
                                                {item.groups?.group_code}-{item.level1_code}-000-0000
                                            </td>
                                            <td className="fw-medium text-dark">{item.title}</td>
                                            <td>
                                                <span className="badge bg-secondary-lighten text-secondary">
                                                    {item.groups?.title || 'N/A'}
                                                </span>
                                            </td>
                                            <td className="text-center no-print">
                                                <div className="btn-group btn-group-sm">
                                                    <a
                                                        href={`/account/level1/${item.id}/edit`}
                                                        className="btn btn-outline-primary"
                                                        title="Edit"
                                                    >
                                                        <i className="mdi mdi-pencil"></i>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        className="btn btn-outline-danger"
                                                        title="Delete"
                                                        onClick={() => handleDelete(item.id, item.title)}
                                                    >
                                                        <i className="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="4" className="text-center py-5 text-muted">
                                            No Level 1 accounts found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center text-muted small">
                    <div>Showing <b>{filtered.length}</b> of <b>{level1s.length}</b> accounts</div>
                    <div>ERP Financial Structure</div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
