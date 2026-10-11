import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function OpeningBalance({ openBals = [], startDate = '', endDate = '', vNoList = [] }) {
    const [searchTerm, setSearchTerm] = useState('');

    const filtered = openBals.filter(item =>
        !searchTerm ||
        (item.v_no && String(item.v_no).includes(searchTerm)) ||
        (item.accounts?.title && item.accounts.title.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    const totalDebit = filtered.reduce((sum, item) => sum + Number(item.debit || 0), 0);
    const totalCredit = filtered.reduce((sum, item) => sum + Number(item.credit || 0), 0);

    return (
        <AuthenticatedLayout header="Opening Balance">
            <Head title="Opening Balance - Real ERP" />

            <div className="card shadow-sm border-0">
                <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div className="d-flex gap-2">
                        <a href="/open_bal" className="btn btn-primary btn-sm no-print">
                            <i className="mdi mdi-plus-circle me-1"></i> Add / Edit Opening Balances
                        </a>
                        <button type="button" className="btn btn-outline-secondary btn-sm no-print" onClick={() => window.print()}>
                            <i className="mdi mdi-printer me-1"></i> Print
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
                                placeholder="Search account..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                        </div>
                    </div>
                </div>

                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover table-striped align-middle mb-0 font-13">
                            <thead className="table-light text-uppercase small text-muted">
                                <tr>
                                    <th>Voucher #</th>
                                    <th>Date</th>
                                    <th>Account Title</th>
                                    <th className="text-end">Debit</th>
                                    <th className="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length > 0 ? (
                                    filtered.map((item, idx) => (
                                        <tr key={item.id || idx}>
                                            <td className="fw-semibold text-primary">{item.v_no || '-'}</td>
                                            <td className="text-muted">{item.date || '-'}</td>
                                            <td className="fw-medium text-dark">{item.accounts?.title || 'Account #' + item.account_id}</td>
                                            <td className="text-end text-danger fw-semibold">
                                                {item.debit ? Number(item.debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                            </td>
                                            <td className="text-end text-success fw-semibold">
                                                {item.credit ? Number(item.credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="text-center py-5 text-muted">
                                            No opening balance entries found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                            {filtered.length > 0 && (
                                <tfoot className="table-light fw-bold">
                                    <tr>
                                        <td colSpan="3" className="text-end">Totals:</td>
                                        <td className="text-end text-danger">
                                            {totalDebit.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                        </td>
                                        <td className="text-end text-success">
                                            {totalCredit.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                        </td>
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
