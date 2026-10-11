import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function UserIndex({ users = [] }) {
    const [searchTerm, setSearchTerm] = useState('');

    const filteredUsers = users.filter(user =>
        !searchTerm ||
        (user.name && user.name.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (user.email && user.email.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    const handleDelete = (id, name) => {
        if (confirm(`Are you sure you want to delete user "${name}"?`)) {
            router.delete(`/users/${id}`, {
                preserveScroll: true,
            });
        }
    };

    return (
        <AuthenticatedLayout header="User Management">
            <Head title="User Management - Real ERP" />

            <div className="card shadow-sm border-0">
                <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div className="d-flex gap-2">
                        <a href="/users/create" className="btn btn-primary">
                            <i className="mdi mdi-account-plus me-1"></i> Add New User
                        </a>
                        <a href="/user_permission" className="btn btn-outline-secondary">
                            <i className="mdi mdi-shield-account me-1"></i> Permission Management
                        </a>
                    </div>

                    <div style={{ minWidth: '240px' }}>
                        <div className="input-group input-group-sm">
                            <span className="input-group-text bg-light border-end-0">
                                <i className="mdi mdi-magnify"></i>
                            </span>
                            <input
                                type="text"
                                className="form-control bg-light border-start-0"
                                placeholder="Search by name or email..."
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
                                    <th style={{ width: '60px' }}>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th className="text-center" style={{ width: '130px' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredUsers.length > 0 ? (
                                    filteredUsers.map((user, idx) => (
                                        <tr key={user.id}>
                                            <td className="text-muted small">{idx + 1}</td>
                                            <td>
                                                <div className="d-flex align-items-center gap-2">
                                                    <div className="rounded-circle bg-primary-lighten text-primary fw-bold d-flex align-items-center justify-content-center" style={{ width: '32px', height: '32px', fontSize: '13px' }}>
                                                        {user.name.charAt(0).toUpperCase()}
                                                    </div>
                                                    <span className="fw-semibold text-dark">{user.name}</span>
                                                </div>
                                            </td>
                                            <td className="text-muted">{user.email}</td>
                                            <td className="text-muted">{user.mobile || '-'}</td>
                                            <td className="text-center">
                                                <div className="btn-group btn-group-sm">
                                                    <a
                                                        href={`/users/${user.id}/edit`}
                                                        className="btn btn-outline-primary"
                                                        title="Edit User"
                                                    >
                                                        <i className="mdi mdi-pencil"></i>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        className="btn btn-outline-danger"
                                                        title="Delete User"
                                                        onClick={() => handleDelete(user.id, user.name)}
                                                    >
                                                        <i className="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="text-center py-5 text-muted">
                                            <i className="mdi mdi-account-off font-28 d-block mb-2 opacity-50"></i>
                                            No users found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center text-muted small">
                    <div>Showing <b>{filteredUsers.length}</b> of <b>{users.length}</b> users</div>
                    <div>ERP Staff Administration</div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
