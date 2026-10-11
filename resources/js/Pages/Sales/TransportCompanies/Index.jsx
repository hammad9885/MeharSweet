import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({ transportCompanies = [] }) {
    const [name, setName] = useState('');
    const [editingCompany, setEditingCompany] = useState(null);
    const [editName, setEditName] = useState('');

    const handleCreate = (e) => {
        e.preventDefault();
        router.post('/transport_companies', { name }, {
            onSuccess: () => setName('')
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        router.put(`/transport_companies/${editingCompany.id}`, { name: editName }, {
            onSuccess: () => setEditingCompany(null)
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this transport company?')) {
            router.delete(`/transport_companies/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Transport Companies</h3>
                        <p className="text-muted small mb-0">Manage freight carriers, logistics services, and bilty handlers</p>
                    </div>
                </div>

                <div className="row g-4">
                    <div className="col-md-4">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">
                                    {editingCompany ? 'Edit Transport Company' : 'Add Transport Company'}
                                </h5>
                            </div>
                            <div className="card-body">
                                <form onSubmit={editingCompany ? handleUpdate : handleCreate}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold text-muted">Company Name *</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder="e.g. Faisal Movers, Daewoo Express"
                                            value={editingCompany ? editName : name}
                                            onChange={(e) => editingCompany ? setEditName(e.target.value) : setName(e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div className="d-flex gap-2">
                                        <button type="submit" className="btn btn-primary flex-grow-1">
                                            {editingCompany ? 'Update Company' : 'Save Company'}
                                        </button>
                                        {editingCompany && (
                                            <button
                                                type="button"
                                                onClick={() => setEditingCompany(null)}
                                                className="btn btn-light border"
                                            >
                                                Cancel
                                            </button>
                                        )}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-8">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">All Transport Companies</h5>
                            </div>
                            <div className="card-body p-0">
                                <div className="table-responsive">
                                    <table className="table table-hover align-middle mb-0">
                                        <thead className="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th className="ps-4" style={{ width: '80px' }}>Sr No.</th>
                                                <th>Transport Company</th>
                                                <th className="text-center pe-4" style={{ width: '140px' }}>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {transportCompanies.length > 0 ? (
                                                transportCompanies.map((tc, idx) => (
                                                    <tr key={tc.id}>
                                                        <td className="ps-4 fw-semibold text-muted">{idx + 1}</td>
                                                        <td className="fw-semibold text-dark">{tc.name}</td>
                                                        <td className="text-center pe-4">
                                                            <div className="d-flex justify-content-center gap-1">
                                                                <button
                                                                    onClick={() => {
                                                                        setEditingCompany(tc);
                                                                        setEditName(tc.name);
                                                                    }}
                                                                    className="btn btn-sm btn-outline-warning"
                                                                    title="Edit"
                                                                >
                                                                    <i className="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDelete(tc.id)}
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
                                                    <td colSpan="3" className="text-center py-4 text-muted">
                                                        No transport companies registered yet.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
