import React, { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function PayablesReceivables({
    title = 'Accounts Summary',
    type = 'payables',
    balances = [],
    endDate = ''
}) {
    const [searchTerm, setSearchTerm] = useState('');
    const [filterDate, setFilterDate] = useState(endDate || '');

    const filtered = useMemo(() => {
        return balances.filter(row =>
            !searchTerm ||
            (row.title && row.title.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (row.acc_codes?.full_code && row.acc_codes.full_code.includes(searchTerm))
        );
    }, [balances, searchTerm]);

    const totalDebit = filtered.reduce((s, r) => s + Number(r.total_debit || 0), 0);
    const totalCredit = filtered.reduce((s, r) => s + Number(r.total_credit || 0), 0);
    const totalBalance = filtered.reduce((s, r) => s + Math.abs(Number(r.balance || 0)), 0);

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get(window.location.pathname, {
            end_date: filterDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout header={title}>
            <Head title={`${title} - GST ERP`} />

            {/* Filter Bar */}
            <div className="card shadow-sm border-0 mb-3 no-print">
                <div className="card-body">
                    <form onSubmit={handleFilterSubmit} className="row g-2 align-items-end">
                        <div className="col-md-3">
                            <label className="form-label small fw-bold text-muted">Up to End Date</label>
                            <input
                                type="date"
                                className="form-control form-control-sm"
                                value={filterDate}
                                onChange={(e) => setFilterDate(e.target.value)}
                            />
                        </div>

                        <div className="col-md-3 d-flex gap-2">
                            <button type="submit" className="btn btn-primary btn-sm flex-fill">
                                <i className="mdi mdi-filter me-1"></i> View Report
                            </button>
                            <button type="button" className="btn btn-outline-secondary btn-sm" onClick={() => window.print()}>
                                <i className="mdi mdi-printer me-1"></i> Print
                            </button>
                        </div>

                        <div className="col-md-6">
                            <div className="input-group input-group-sm">
                                <span className="input-group-text bg-light border-end-0">
                                    <i className="mdi mdi-magnify"></i>
                                </span>
                                <input
                                    type="text"
                                    className="form-control bg-light border-start-0"
                                    placeholder="Search by account title or code..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {/* Summary Metrics */}
            <div className="row g-2 mb-3">
                <div className="col-md-4">
                    <div className="card shadow-sm border-0 bg-light p-3">
                        <small className="text-muted text-uppercase fw-bold">Total Debit</small>
                        <h4 className="m-0 fw-bold text-danger">
                            {totalDebit.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="card shadow-sm border-0 bg-light p-3">
                        <small className="text-muted text-uppercase fw-bold">Total Credit</small>
                        <h4 className="m-0 fw-bold text-success">
                            {totalCredit.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className={`card shadow-sm border-0 ${type === 'payables' ? 'bg-danger text-white' : 'bg-success text-white'} p-3`}>
                        <small className="text-white-50 text-uppercase fw-bold">Net {type === 'payables' ? 'Payable' : 'Receivable'} Balance</small>
                        <h4 className="m-0 fw-bold text-white">
                            {totalBalance.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
            </div>

            {/* Balances Table */}
            <div className="card shadow-sm border-0">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover table-striped align-middle mb-0 font-13">
                            <thead className="table-light text-uppercase small text-muted">
                                <tr>
                                    <th>A/C Code</th>
                                    <th>Account Title</th>
                                    <th className="text-end">Total Debit</th>
                                    <th className="text-end">Total Credit</th>
                                    <th className="text-end">Net Outstanding Balance</th>
                                    <th className="text-center no-print" style={{ width: '100px' }}>Ledger</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length > 0 ? (
                                    filtered.map((row, idx) => (
                                        <tr key={row.id || idx}>
                                            <td className="fw-semibold text-primary">{row.acc_codes?.full_code || row.id}</td>
                                            <td className="fw-medium text-dark">{row.title}</td>
                                            <td className="text-end">{Number(row.total_debit || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                            <td className="text-end">{Number(row.total_credit || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                            <td className={`text-end fw-bold ${type === 'payables' ? 'text-danger' : 'text-success'}`}>
                                                {Math.abs(Number(row.balance || 0)).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                            </td>
                                            <td className="text-center no-print">
                                                <a
                                                    href={`/ledger?account_title=${row.id}`}
                                                    className="btn btn-outline-primary btn-sm"
                                                    title="View Detailed Ledger"
                                                >
                                                    <i className="mdi mdi-book-open-page-variant"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="text-center py-5 text-muted">
                                            No {type} records found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                            {filtered.length > 0 && (
                                <tfoot className="table-light fw-bold">
                                    <tr>
                                        <td colSpan="2" className="text-end">Grand Total:</td>
                                        <td className="text-end text-danger">{totalDebit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td className="text-end text-success">{totalCredit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td className="text-end text-primary">{totalBalance.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            )}
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
