import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Index({
    itemtypes = [],
    itemType = 'fp', // 'fp' for Raw Material Category, 'rp' for Finished Product Type
    editingType: initialEditing = null
}) {
    const isRawMaterial = itemType === 'fp';
    const title = isRawMaterial ? 'Item Categories (Raw Material)' : 'Product Types (Finished Product)';

    const [typeTitle, setTypeTitle] = useState('');
    const [loadPosting, setLoadPosting] = useState(false);
    const [editingType, setEditingType] = useState(initialEditing);
    const [editTitle, setEditTitle] = useState(initialEditing ? initialEditing.type_title : '');
    const [editLoadPosting, setEditLoadPosting] = useState(initialEditing ? !!initialEditing.load_posting : false);
    const [searchTerm, setSearchTerm] = useState('');

    const handleCreate = (e) => {
        e.preventDefault();
        router.post('/inventory/itemtype', {
            type_title: typeTitle,
            item_type: itemType,
            load_posting: loadPosting ? 1 : 0
        }, {
            onSuccess: () => {
                setTypeTitle('');
                setLoadPosting(false);
            }
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        router.post(`/inventory/itemtype/${editingType.id}`, {
            type_title: editTitle,
            item_type: editingType.item_type || itemType,
            load_posting: editLoadPosting ? 1 : 0
        }, {
            onSuccess: () => setEditingType(null)
        });
    };

    const handleDelete = (id, title) => {
        if (confirm(`Are you sure you want to delete "${title}"?`)) {
            router.delete(`/inventory/itemtype/${id}`);
        }
    };

    const filtered = itemtypes.filter(it =>
        !searchTerm || (it.type_title && it.type_title.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    return (
        <AuthenticatedLayout header={title}>
            <Head title={`${title} - Real ERP`} />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">{title}</h3>
                        <p className="text-muted small mb-0">
                            {isRawMaterial
                                ? 'Manage categories and classifications for raw material stock items'
                                : 'Manage finished goods and product item classifications'}
                        </p>
                    </div>
                </div>

                <div className="row g-4">
                    {/* Add / Edit Form Card */}
                    <div className="col-md-4">
                        <div className="card border-0 shadow-sm">
                            <div className="card-header bg-white border-bottom py-3">
                                <h5 className="card-title mb-0 fw-semibold">
                                    {editingType ? `Edit ${isRawMaterial ? 'Category' : 'Type'}` : `Add New ${isRawMaterial ? 'Category' : 'Type'}`}
                                </h5>
                            </div>
                            <div className="card-body">
                                <form onSubmit={editingType ? handleUpdate : handleCreate}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold text-muted">Title / Name *</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder={isRawMaterial ? "e.g. Plastic, Carton, Foil, Sugar" : "e.g. Sweets, Bakery, Dry Fruits"}
                                            value={editingType ? editTitle : typeTitle}
                                            onChange={(e) => editingType ? setEditTitle(e.target.value) : setTypeTitle(e.target.value)}
                                            required
                                        />
                                    </div>

                                    {isRawMaterial && (
                                        <div className="mb-3 form-check">
                                            <input
                                                type="checkbox"
                                                className="form-check-input"
                                                id="loadPostingCheck"
                                                checked={editingType ? editLoadPosting : loadPosting}
                                                onChange={(e) => editingType ? setEditLoadPosting(e.target.checked) : setLoadPosting(e.target.checked)}
                                            />
                                            <label className="form-check-label small text-muted" htmlFor="loadPostingCheck">
                                                Load Posting (Include in lot posting routines)
                                            </label>
                                        </div>
                                    )}

                                    <div className="d-flex gap-2">
                                        <button type="submit" className="btn btn-primary flex-grow-1">
                                            {editingType ? 'Update' : 'Save'}
                                        </button>
                                        {editingType && (
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setEditingType(null);
                                                    setEditTitle('');
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
                                <h5 className="card-title mb-0 fw-semibold">Available Records</h5>
                                <div style={{ minWidth: '220px' }}>
                                    <input
                                        type="text"
                                        className="form-control form-control-sm"
                                        placeholder="Search title..."
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
                                                <th>Title</th>
                                                {isRawMaterial && <th style={{ width: '130px' }}>Load Posting</th>}
                                                <th className="text-center pe-4" style={{ width: '140px' }}>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {filtered.length > 0 ? (
                                                filtered.map((item, idx) => (
                                                    <tr key={item.id}>
                                                        <td className="ps-4 fw-semibold text-muted">{idx + 1}</td>
                                                        <td className="fw-semibold text-dark">{item.type_title}</td>
                                                        {isRawMaterial && (
                                                            <td>
                                                                <span className={`badge ${item.load_posting ? 'bg-success-lighten text-success' : 'bg-secondary-lighten text-secondary'}`}>
                                                                    {item.load_posting ? 'Yes' : 'No'}
                                                                </span>
                                                            </td>
                                                        )}
                                                        <td className="text-center pe-4">
                                                            <div className="d-flex justify-content-center gap-1">
                                                                <button
                                                                    onClick={() => {
                                                                        setEditingType(item);
                                                                        setEditTitle(item.type_title);
                                                                        setEditLoadPosting(!!item.load_posting);
                                                                    }}
                                                                    className="btn btn-sm btn-outline-warning"
                                                                    title="Edit"
                                                                >
                                                                    <i className="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDelete(item.id, item.type_title)}
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
                                                    <td colSpan={isRawMaterial ? 4 : 3} className="text-center py-4 text-muted">
                                                        No records found.
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
