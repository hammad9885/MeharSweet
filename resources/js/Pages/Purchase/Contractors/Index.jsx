import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({ contractors = [] }) {
    const [name, setName] = useState('');
    const [editingContractor, setEditingContractor] = useState(null);
    const [editName, setEditName] = useState('');

    const handleCreate = (e) => {
        e.preventDefault();
        router.post('/contractors', { name }, {
            onSuccess: () => setName('')
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        router.put(`/contractors/${editingContractor.id}`, { name: editName }, {
            onSuccess: () => setEditingContractor(null)
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this contractor?')) {
            router.delete(`/contractors/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Contractors</h3>
                        <p className="text-muted small mb-0">Manage third-party packaging, printing, and processing contractors</p>
                    </div>
                </div>

                <div className="row g-4">
                    <div className="col-md-4">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">
                                    {editingContractor ? 'Edit Contractor' : 'Add Contractor'}
                                </h5>
                            </div>
                            <div className="card-body">
                                <form onSubmit={editingContractor ? handleUpdate : handleCreate}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold text-muted">Contractor Name *</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder="e.g. Master Carton Packers"
                                            value={editingContractor ? editName : name}
                                            onChange={(e) => editingContractor ? setEditName(e.target.value) : setName(e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div className="d-flex gap-2">
                                        <button type="submit" className="btn btn-primary flex-grow-1">
                                            {editingContractor ? 'Update Contractor' : 'Save Contractor'}
                                        </button>
                                        {editingContractor && (
                                            <button
                                                type="button"
                                                onClick={() => setEditingContractor(null)}
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
                                <h5 className="card-title mb-0 fw-semibold">All Contractors</h5>
                            </div>
                            <div className="card-body p-0">
                                <div className="table-responsive">
                                    <table className="table table-hover align-middle mb-0">
                                        <thead className="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th className="ps-4" style={{ width: '80px' }}>#</th>
                                                <th>Contractor Name</th>
                                                <th className="text-center pe-4" style={{ width: '140px' }}>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {contractors.length > 0 ? (
                                                contractors.map((c, idx) => (
                                                    <tr key={c.id}>
                                                        <td className="ps-4 fw-semibold text-muted">{idx + 1}</td>
                                                        <td className="fw-semibold text-dark">{c.name || c.title}</td>
                                                        <td className="text-center pe-4">
                                                            <div className="d-flex justify-content-center gap-1">
                                                                <button
                                                                    onClick={() => {
                                                                        setEditingContractor(c);
                                                                        setEditName(c.name || c.title);
                                                                    }}
                                                                    className="btn btn-sm btn-outline-warning"
                                                                    title="Edit"
                                                                >
                                                                    <i className="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDelete(c.id)}
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
                                                        No contractors registered.
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
