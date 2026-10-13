import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function ExpenseReport({
    result = [],
    level2Titles = [],
    filters = {}
}) {
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');
    const [status, setStatus] = useState(filters.status || '');
    const [level2Title, setLevel2Title] = useState(filters.level2_title || '');
    const [searchTerm, setSearchTerm] = useState('');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/expense/reports', {
            start_date: startDate,
            end_date: endDate,
            status: status,
            level2_title: level2Title,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setStartDate('');
        setEndDate('');
        setStatus('');
        setLevel2Title('');
        router.get('/expense/reports');
    };

    const filtered = result.filter(r =>
        !searchTerm ||
        (r.account_title && r.account_title.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (r.LEVEL2_TITLE && r.LEVEL2_TITLE.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    const totalExpense = filtered.reduce((sum, r) => sum + (parseFloat(r.total_amount) || 0), 0);

    return (
        <AuthenticatedLayout header="Expense Report">
            <Head title="Expense Report - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">Expense Report Summary</h3>
                        <p className="text-muted small mb-0">Overview of operational, administrative, and factory overhead expenditure</p>
                    </div>
                    <button
                        onClick={() => window.print()}
                        className="btn btn-outline-secondary btn-sm"
                    >
                        <i className="mdi mdi-printer me-1"></i> Print Report
                    </button>
                </div>

                {/* Filter Form Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-header bg-white border-bottom py-3">
                        <h5 className="card-title mb-0 fw-semibold">
                            <i className="mdi mdi-filter-variant me-1 text-primary"></i> Filter Expenditures
                        </h5>
                    </div>
                    <div className="card-body">
                        <form onSubmit={handleFilter}>
                            <div className="row g-3">
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Start Date</label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={startDate}
                                        onChange={(e) => setStartDate(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">End Date</label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={endDate}
                                        onChange={(e) => setEndDate(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Classification Category</label>
                                    <select
                                        className="form-select"
                                        value={level2Title}
                                        onChange={(e) => setLevel2Title(e.target.value)}
                                    >
                                        <option value="">-- All Titles --</option>
                                        {level2Titles.map((title, i) => (
                                            <option key={i} value={title}>{title}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Voucher Status</label>
                                    <select
                                        className="form-select"
                                        value={status}
                                        onChange={(e) => setStatus(e.target.value)}
                                    >
                                        <option value="">All Statuses</option>
                                        <option value="official">Official</option>
                                        <option value="unofficial">Unofficial</option>
                                    </select>
                                </div>
                                <div className="col-12 d-flex justify-content-end gap-2">
                                    <button type="button" onClick={handleReset} className="btn btn-light border">
                                        Reset
                                    </button>
                                    <button type="submit" className="btn btn-primary px-4">
                                        Filter Records
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Table Card */}
                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 className="card-title mb-0 fw-semibold">Expense Ledger Summary</h5>
                        <div style={{ minWidth: '220px' }}>
                            <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Search account..."
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
                                        <th className="ps-4" style={{ width: '80px' }}>#</th>
                                        <th style={{ width: '140px' }}>Last Date</th>
                                        <th>Expense Classification</th>
                                        <th>Account Title</th>
                                        <th className="text-end pe-4" style={{ width: '180px' }}>Total Amount (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filtered.length > 0 ? (
                                        filtered.map((r, idx) => (
                                            <tr key={idx}>
                                                <td className="ps-4 text-muted small">{idx + 1}</td>
                                                <td className="text-muted">{r.latest_date}</td>
                                                <td>
                                                    <span className="badge bg-secondary-lighten text-secondary">
                                                        {r.LEVEL2_TITLE}
                                                    </span>
                                                </td>
                                                <td className="fw-semibold text-dark">{r.account_title}</td>
                                                <td className="text-end pe-4 fw-bold text-danger">
                                                    {(parseFloat(r.total_amount) || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="text-center py-4 text-muted">
                                                No expense records found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot className="bg-light fw-bold">
                                    <tr>
                                        <td colSpan="4" className="text-end ps-4">Total Aggregate Expenses:</td>
                                        <td className="text-end pe-4 text-danger fs-6">
                                            {totalExpense.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
