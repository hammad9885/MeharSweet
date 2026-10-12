import React, { useState, useMemo } from 'react';
import { Head, router, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ItemMaster({ itemmasters = [], items = {}, itemTypes = {}, filters = {} }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCode, setSelectedCode] = useState(filters.item_code || '');
    const [selectedType, setSelectedType] = useState(filters.type_id || '');

    // Filter records client-side or server-side
    const filteredRecords = useMemo(() => {
        return itemmasters.filter(item => {
            const matchesSearch =
                !searchTerm ||
                (item.item_code && item.item_code.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.seq_number && String(item.seq_number).includes(searchTerm)) ||
                (item.itemtypes?.type_title && item.itemtypes.type_title.toLowerCase().includes(searchTerm.toLowerCase()));

            const matchesCode = !selectedCode || item.item_code === selectedCode;
            const matchesType = !selectedType || String(item.type_id) === String(selectedType);

            return matchesSearch && matchesCode && matchesType;
        });
    }, [itemmasters, searchTerm, selectedCode, selectedType]);

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/inventory/itemmaster', {
            item_code: selectedCode,
            type_id: selectedType,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleClear = () => {
        setSelectedCode('');
        setSelectedType('');
        setSearchTerm('');
        router.get('/inventory/itemmaster');
    };

    const handleDelete = (id, title) => {
        if (confirm(`Are you sure you want to delete item "${title || id}"?`)) {
            router.delete(`/inventory/itemmaster/${id}`, {
                preserveScroll: true,
            });
        }
    };

    const handlePrint = () => {
        window.print();
    };

    return (
        <AuthenticatedLayout header="Registered Items (Item Master)">
            <Head title="Item Master - Real ERP" />

            {/* Filter Bar */}
            <div className="card shadow-sm border-0 mb-3 no-print">
                <div className="card-body">
                    <form onSubmit={handleFilterSubmit} className="row g-3 align-items-end">
                        <div className="col-md-4">
                            <label className="form-label small fw-bold text-muted">Item Title / Code</label>
                            <select
                                className="form-select"
                                value={selectedCode}
                                onChange={(e) => setSelectedCode(e.target.value)}
                            >
                                <option value="">All Item Titles</option>
                                {Object.entries(items).map(([code, title]) => (
                                    <option key={code} value={code}>{title}</option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-4">
                            <label className="form-label small fw-bold text-muted">Item Type</label>
                            <select
                                className="form-select"
                                value={selectedType}
                                onChange={(e) => setSelectedType(e.target.value)}
                            >
                                <option value="">All Item Types</option>
                                {Object.entries(itemTypes).map(([id, title]) => (
                                    <option key={id} value={id}>{title}</option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-4 d-flex gap-2">
                            <button type="submit" className="btn btn-primary">
                                <i className="mdi mdi-filter me-1"></i> Filter
                            </button>
                            <button type="button" className="btn btn-secondary" onClick={handleClear}>
                                <i className="mdi mdi-refresh me-1"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {/* Main Table Card */}
            <div className="card shadow-sm border-0">
                <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div className="d-flex align-items-center gap-2">
                        <Link href="/inventory/create/itemmaster" className="btn btn-primary no-print">
                            <i className="mdi mdi-plus-circle me-1"></i> Add New Item
                        </Link>
                        <button type="button" className="btn btn-outline-secondary no-print" onClick={handlePrint}>
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
                                placeholder="Live instant search..."
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
                                    <th>Item Code</th>
                                    <th>Item Title</th>
                                    <th>Item Type</th>
                                    <th className="text-end">Purchase Rate</th>
                                    <th className="text-end">Sale Rate</th>
                                    <th>Unit</th>
                                    <th className="text-center no-print" style={{ width: '140px' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredRecords.length > 0 ? (
                                    filteredRecords.map((item) => (
                                        <tr key={item.id}>
                                            <td className="fw-semibold text-primary">{item.seq_number || item.id}</td>
                                            <td className="fw-medium text-dark">{item.item_code}</td>
                                            <td>
                                                <span className="badge bg-info-lighten text-info">
                                                    {item.itemtypes?.type_title || 'N/A'}
                                                </span>
                                            </td>
                                            <td className="text-end">{item.purchase || '0.00'}</td>
                                            <td className="text-end">{item.sale_rate || '0.00'}</td>
                                            <td>{item.itemunits?.name || 'N/A'}</td>
                                            <td className="text-center no-print">
                                                <div className="btn-group btn-group-sm">
                                                    <Link
                                                        href={`/inventory/itemmaster/${item.id}/edit`}
                                                        className="btn btn-outline-primary"
                                                        title="Edit"
                                                    >
                                                        <i className="mdi mdi-pencil"></i>
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        className="btn btn-outline-danger"
                                                        title="Delete"
                                                        onClick={() => handleDelete(item.id, item.item_code)}
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
                                            <i className="mdi mdi-database-off font-28 d-block mb-2 opacity-50"></i>
                                            No registered items found matching the selected criteria.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center text-muted small">
                    <div>Showing <b>{filteredRecords.length}</b> of <b>{itemmasters.length}</b> items</div>
                    <div>ERP Real-Time Data</div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
