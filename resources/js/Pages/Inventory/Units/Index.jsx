import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Index({ units = [], editingUnit: initialEditing = null }) {
    const [name, setName] = useState('');
    const [editingUnit, setEditingUnit] = useState(initialEditing);
    const [editName, setEditName] = useState(initialEditing ? initialEditing.name : '');
    const [searchTerm, setSearchTerm] = useState('');

    const handleCreate = (e) => {
        e.preventDefault();
        router.post('/units', { name }, {
            onSuccess: () => setName('')
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        router.put(`/units/${editingUnit.id}`, { name: editName }, {
            onSuccess: () => setEditingUnit(null)
        });
    };

    const handleDelete = (id, unitName) => {
        if (confirm(`Are you sure you want to delete unit "${unitName}"?`)) {
            router.delete(`/units/${id}`);
        }
    };

    const filteredUnits = units.filter(u =>
        !searchTerm || (u.name && u.name.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    return (
        <AuthenticatedLayout header="Units of Measurement">
            <Head title="Units - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Units of Measurement</h3>
                        <p className="text-muted small mb-0">Manage metric, imperial, and packaging measurement units (KG, PCS, BOX, CTN, etc.)</p>
                    </div>
                </div>

                <div className="row g-4">
                    {/* Add / Edit Form Card */}
                    <div className="col-md-4">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">
                                    {editingUnit ? 'Edit Unit' : 'Add New Unit'}
                                </h5>
                            </div>
                            <div className="card-body">
                                <form onSubmit={editingUnit ? handleUpdate : handleCreate}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold text-muted">Unit Name *</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder="e.g. KG, PCS, BOX, LTR, MTR"
                                            value={editingUnit ? editName : name}
                                            onChange={(e) => editingUnit ? setEditName(e.target.value) : setName(e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div className="d-flex gap-2">
                                        <button type="submit" className="btn btn-primary flex-grow-1">
                                            {editingUnit ? 'Update Unit' : 'Save Unit'}
                                        </button>
                                        {editingUnit && (
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setEditingUnit(null);
                                                    setEditName('');
                                                }}
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

                    {/* Table Card */}
                    <div className="col-md-8">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h5 className="card-title mb-0 fw-semibold">All Measurement Units</h5>
                                <div style={{ minWidth: '220px' }}>
                                    <input
                                        type="text"
                                        className="form-control form-control-sm"
                                        placeholder="Search unit..."
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
                                                <th className="ps-4" style={{ width: '80px' }}>Sr No.</th>
                                                <th>Unit Name</th>
                                                <th className="text-center pe-4" style={{ width: '140px' }}>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {filteredUnits.length > 0 ? (
                                                filteredUnits.map((u, idx) => (
                                                    <tr key={u.id}>
                                                        <td className="ps-4 fw-semibold text-muted">{idx + 1}</td>
                                                        <td className="fw-semibold text-dark">{u.name}</td>
                                                        <td className="text-center pe-4">
                                                            <div className="d-flex justify-content-center gap-1">
                                                                <button
                                                                    onClick={() => {
                                                                        setEditingUnit(u);
                                                                        setEditName(u.name);
                                                                    }}
                                                                    className="btn btn-sm btn-outline-warning"
                                                                    title="Edit"
                                                                >
                                                                    <i className="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDelete(u.id, u.name)}
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
                                                        No measurement units found.
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
