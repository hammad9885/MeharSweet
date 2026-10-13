import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router, Link } from '@inertiajs/react';

export default function Index({
    customers = [],
    customerNames = [],
    categories = [],
    filters = {}
}) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/customers', { search: search }, { preserveState: true });
    };

    const handleReset = () => {
        setSearch('');
        router.get('/customers');
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this customer?')) {
            router.delete(`/customers/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Customers</h3>
                        <p className="text-muted small mb-0">Manage customer accounts, contact details, transport routing, and cities</p>
                    </div>
                    <Link href="/customers/create" className="btn btn-primary d-flex align-items-center gap-2">
                        <i className="mdi mdi-account-plus font-16"></i>
                        <span>Add Customer</span>
                    </Link>
                </div>

                {/* Filter */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body">
                        <form onSubmit={handleSearch} className="row g-2 align-items-center">
                            <div className="col-md-6">
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search customer by name, Urdu title, or mobile..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                            </div>
                            <div className="col-auto">
                                <button type="submit" className="btn btn-success px-4">Search</button>
                            </div>
                            <div className="col-auto">
                                <button type="button" onClick={handleReset} className="btn btn-light border px-3">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Table */}
                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">Acc ID</th>
                                        <th>Name</th>
                                        <th>Urdu Title</th>
                                        <th>City</th>
                                        <th>Mobile</th>
                                        <th>Transport Company</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {customers.length > 0 ? (
                                        customers.map((c) => (
                                            <tr key={c.id}>
                                                <td className="ps-4 fw-bold text-primary">#{c.acc_id || c.id}</td>
                                                <td className="fw-semibold text-dark">{c.name}</td>
                                                <td dir="rtl" className="text-muted fw-bold">{c.urdu_title || '-'}</td>
                                                <td>{c.city || '-'}</td>
                                                <td>{c.mobile || c.phone || '-'}</td>
                                                <td className="small text-muted">{c.company_title || c.transport_company || '-'}</td>
                                                <td className="text-center pe-4">
                                                    <div className="d-flex justify-content-center gap-1">
                                                        <Link
                                                            href={`/customers/${c.id}/edit`}
                                                            className="btn btn-sm btn-outline-warning"
                                                            title="Edit Customer"
                                                        >
                                                            <i className="mdi mdi-pencil"></i>
                                                        </Link>
                                                        <button
                                                            onClick={() => handleDelete(c.id)}
                                                            className="btn btn-sm btn-outline-danger"
                                                            title="Delete Customer"
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
                                                <i className="mdi mdi-account-search font-36 d-block mb-2 text-muted"></i>
                                                No customers found.
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
