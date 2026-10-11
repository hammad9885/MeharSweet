import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function TrialBalance({
    trialBalance = [],
    startDate = '',
    endDate = '',
    totals = {}
}) {
    const [start, setStart] = useState(startDate || '');
    const [end, setEnd] = useState(endDate || '');
    const [searchTerm, setSearchTerm] = useState('');

    const filtered = trialBalance.filter(row =>
        !searchTerm ||
        (row.account_title && row.account_title.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (row.code && String(row.code).includes(searchTerm))
    );

    const totalOpenDebit = filtered.reduce((s, r) => s + Number(r.opening_debit || 0), 0);
    const totalOpenCredit = filtered.reduce((s, r) => s + Number(r.opening_credit || 0), 0);
    const totalTransDebit = filtered.reduce((s, r) => s + Number(r.trans_debit || 0), 0);
    const totalTransCredit = filtered.reduce((s, r) => s + Number(r.trans_credit || 0), 0);
    const totalCloseDebit = filtered.reduce((s, r) => s + Number(r.closing_debit || 0), 0);
    const totalCloseCredit = filtered.reduce((s, r) => s + Number(r.closing_credit || 0), 0);

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/trialbalance', {
            start_date: start,
            end_date: end,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout header="Trial Balance Statement">
            <Head title="Trial Balance - Real ERP" />

            {/* Filter Bar */}
            <div className="card shadow-sm border-0 mb-3 no-print">
                <div className="card-body">
                    <form onSubmit={handleFilterSubmit} className="row g-2 align-items-end">
                        <div className="col-md-3">
                            <label className="form-label small fw-bold text-muted">Start Date</label>
                            <input
                                type="date"
                                className="form-control form-control-sm"
                                value={start}
                                onChange={(e) => setStart(e.target.value)}
                                required
                            />
                        </div>

                        <div className="col-md-3">
                            <label className="form-label small fw-bold text-muted">End Date</label>
                            <input
                                type="date"
                                className="form-control form-control-sm"
                                value={end}
                                onChange={(e) => setEnd(e.target.value)}
                                required
                            />
                        </div>

                        <div className="col-md-3 d-flex gap-2">
                            <button type="submit" className="btn btn-primary btn-sm flex-fill">
                                <i className="mdi mdi-filter me-1"></i> Generate Report
                            </button>
                            <button type="button" className="btn btn-outline-secondary btn-sm" onClick={() => window.print()}>
                                <i className="mdi mdi-printer me-1"></i> Print
                            </button>
                        </div>

                        <div className="col-md-3">
                            <div className="input-group input-group-sm">
                                <span className="input-group-text bg-light border-end-0">
                                    <i className="mdi mdi-magnify"></i>
                                </span>
                                <input
                                type="text"
                                className="form-control bg-light border-start-0"
                                placeholder="Search accounts..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {/* Trial Balance Table */}
            <div className="card shadow-sm border-0">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover table-bordered align-middle mb-0 font-13 text-center">
                            <thead className="table-light text-uppercase small text-muted">
                                <tr>
                                    <th rowSpan="2" className="align-middle text-start" style={{ width: '80px' }}>Code</th>
                                    <th rowSpan="2" className="align-middle text-start">Account Title</th>
                                    <th colSpan="2" className="border-bottom">Opening Balance</th>
                                    <th colSpan="2" className="border-bottom">Transactions (Period)</th>
                                    <th colSpan="2" className="border-bottom">Closing Balance</th>
                                </tr>
                                <tr>
                                    <th className="text-end text-danger" style={{ width: '110px' }}>Debit</th>
                                    <th className="text-end text-success" style={{ width: '110px' }}>Credit</th>
                                    <th className="text-end text-danger" style={{ width: '110px' }}>Debit</th>
                                    <th className="text-end text-success" style={{ width: '110px' }}>Credit</th>
                                    <th className="text-end text-danger" style={{ width: '110px' }}>Debit</th>
                                    <th className="text-end text-success" style={{ width: '110px' }}>Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length > 0 ? (
                                    filtered.map((row, idx) => (
                                        <tr key={row.aid || idx}>
                                            <td className="text-start fw-semibold text-primary">{row.code || row.aid}</td>
                                            <td className="text-start fw-medium text-dark">{row.account_title || '-'}</td>
                                            <td className="text-end">{row.opening_debit ? Number(row.opening_debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}</td>
                                            <td className="text-end">{row.opening_credit ? Number(row.opening_credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}</td>
                                            <td className="text-end">{row.trans_debit ? Number(row.trans_debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}</td>
                                            <td className="text-end">{row.trans_credit ? Number(row.trans_credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}</td>
                                            <td className="text-end fw-bold text-danger">{row.closing_debit ? Number(row.closing_debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}</td>
                                            <td className="text-end fw-bold text-success">{row.closing_credit ? Number(row.closing_credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}</td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="8" className="text-center py-5 text-muted">
                                            {startDate ? 'No ledger activity found for the selected dates.' : 'Please select start and end dates to generate Trial Balance.'}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                            {filtered.length > 0 && (
                                <tfoot className="table-light fw-bold text-end">
                                    <tr>
                                        <td colSpan="2" className="text-end">Grand Totals:</td>
                                        <td>{totalOpenDebit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td>{totalOpenCredit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td>{totalTransDebit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td>{totalTransCredit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td className="text-danger">{totalCloseDebit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                                        <td className="text-success">{totalCloseCredit.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
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
