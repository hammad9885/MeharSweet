import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({ categories = [], editingCategory: initialEditing = null }) {
    const [name, setName] = useState('');
    const [editingCategory, setEditingCategory] = useState(initialEditing);
    const [editName, setEditName] = useState(initialEditing ? initialEditing.name : '');

    const handleCreate = (e) => {
        e.preventDefault();
        router.post('/customer-categories', { name }, {
            onSuccess: () => setName('')
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        router.put(`/customer-categories/${editingCategory.id}`, { name: editName }, {
            onSuccess: () => setEditingCategory(null)
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this category?')) {
            router.delete(`/customer-categories/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Customer Categories</h3>
                        <p className="text-muted small mb-0">Organize customer accounts into commercial classifications</p>
                    </div>
                </div>

                <div className="row g-4">
                    {/* Add Category Form */}
                    <div className="col-md-4">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">
                                    {editingCategory ? 'Edit Category' : 'Add New Category'}
                                </h5>
                            </div>
                            <div className="card-body">
                                <form onSubmit={editingCategory ? handleUpdate : handleCreate}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold text-muted">Category Name *</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder="e.g. Wholesaler, Retailer, Corporate"
                                            value={editingCategory ? editName : name}
                                            onChange={(e) => editingCategory ? setEditName(e.target.value) : setName(e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div className="d-flex gap-2">
                                        <button type="submit" className="btn btn-primary flex-grow-1">
                                            {editingCategory ? 'Update Category' : 'Save Category'}
                                        </button>
                                        {editingCategory && (
                                            <button
                                                type="button"
                                                onClick={() => setEditingCategory(null)}
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

                    {/* Table */}
                    <div className="col-md-8">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">All Categories</h5>
                            </div>
                            <div className="card-body p-0">
                                <div className="table-responsive">
                                    <table className="table table-hover align-middle mb-0">
                                        <thead className="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th className="ps-4" style={{ width: '80px' }}>#</th>
                                                <th>Category Name</th>
                                                <th className="text-center pe-4" style={{ width: '140px' }}>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {categories.length > 0 ? (
                                                categories.map((cat, idx) => (
                                                    <tr key={cat.id}>
                                                        <td className="ps-4 fw-semibold text-muted">{idx + 1}</td>
                                                        <td className="fw-semibold text-dark">{cat.name}</td>
                                                        <td className="text-center pe-4">
                                                            <div className="d-flex justify-content-center gap-1">
                                                                <button
                                                                    onClick={() => {
                                                                        setEditingCategory(cat);
                                                                        setEditName(cat.name);
                                                                    }}
                                                                    className="btn btn-sm btn-outline-warning"
                                                                    title="Edit"
                                                                >
                                                                    <i className="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDelete(cat.id)}
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
                                                        No customer categories found.
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
