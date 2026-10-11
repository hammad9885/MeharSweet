import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({
    consumptions = [],
    products = {},
    filters = {}
}) {
    const [itemId, setItemId] = useState(filters.item_id || '');

    // Convert products to array if it is an object
    const productList = Array.isArray(products)
        ? products
        : Object.values(products || {});

    const getProductName = (id) => {
        if (!id) return '-';
        if (products && products[id]) {
            return products[id].product_name || `Product #${id}`;
        }
        const found = productList.find(p => String(p.id) === String(id));
        return found ? found.product_name : `Product #${id}`;
    };

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/consumption', { item_id: itemId }, { preserveState: true });
    };

    const handleReset = () => {
        setItemId('');
        router.get('/consumption');
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this consumption record?')) {
            router.delete(`/consumption/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Consumption Records</h3>
                        <p className="text-muted small mb-0">Track raw material formulas, mold parameters, and contractor lot issuances</p>
                    </div>
                    <a href="/consumption/create" className="btn btn-primary d-flex align-items-center gap-1">
                        <i className="mdi mdi-plus-circle font-16"></i>
                        <span>Add New Consumption</span>
                    </a>
                </div>

                {/* Filter Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body">
                        <form onSubmit={handleFilter} className="row g-3 align-items-end">
                            <div className="col-md-6 col-lg-4">
                                <label className="form-label small fw-semibold text-muted">Finished Product</label>
                                <select
                                    className="form-select"
                                    value={itemId}
                                    onChange={(e) => setItemId(e.target.value)}
                                >
                                    <option value="">All Products</option>
                                    {productList.map(product => (
                                        <option key={product.id} value={product.id}>
                                            {product.product_name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-6 col-lg-3 d-flex gap-2">
                                <button type="submit" className="btn btn-primary flex-fill">
                                    <i className="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <button type="button" onClick={handleReset} className="btn btn-light border flex-fill">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Data Table */}
                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 className="card-title mb-0 fw-bold">Consumption Formulas</h5>
                        <span className="badge bg-primary rounded-pill px-3 py-2">
                            Total Records: {consumptions.length}
                        </span>
                    </div>
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th style={{ width: '60px' }}>#</th>
                                    <th>Finished Product</th>
                                    <th>Date</th>
                                    <th>Lot No</th>
                                    <th className="text-end">Product Qty</th>
                                    <th className="text-end">Qty Per Ctn</th>
                                    <th className="text-end">Cartons in Lot</th>
                                    <th className="text-end" style={{ width: '180px' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {consumptions.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="text-center py-4 text-muted">
                                            No consumption records found.
                                        </td>
                                    </tr>
                                ) : (
                                    consumptions.map((item, index) => (
                                        <tr key={item.id || index}>
                                            <td className="text-muted small">{index + 1}</td>
                                            <td className="fw-semibold text-dark">
                                                {getProductName(item.item_id)}
                                            </td>
                                            <td>{item.date || '-'}</td>
                                            <td>
                                                <span className="badge bg-light text-dark border">
                                                    {item.lot_no || 'N/A'}
                                                </span>
                                            </td>
                                            <td className="text-end fw-medium">{Number(item.qty || 0).toLocaleString()}</td>
                                            <td className="text-end">{item.qty_per_carton || '-'}</td>
                                            <td className="text-end fw-semibold text-info">
                                                {item.cartons_in_lot || '-'}
                                            </td>
                                            <td className="text-end">
                                                <div className="btn-group btn-group-sm">
                                                    <a
                                                        href={`/consumption/${item.id}/edit`}
                                                        className="btn btn-outline-warning"
                                                        title="Edit"
                                                    >
                                                        <i className="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a
                                                        href={`/consumption/print/${item.id}`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="btn btn-outline-info"
                                                        title="Print"
                                                    >
                                                        <i className="mdi mdi-printer"></i>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleDelete(item.id)}
                                                        className="btn btn-outline-danger"
                                                        title="Delete"
                                                    >
                                                        <i className="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
