import React, { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Ledger({
    trndtls = [],
    openingBalance = 0,
    totalDebit = 0,
    totalCredit = 0,
    closingBalance = 0,
    customers = [],
    suppliers = [],
    contractors = [],
    accounts = [],
    accountId = null
}) {
    const [selectedAccount, setSelectedAccount] = useState(accountId || '');
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [searchTerm, setSearchTerm] = useState('');

    // Compute running balance for each row
    const rowsWithBalance = useMemo(() => {
        let currentBalance = Number(openingBalance || 0);

        return trndtls.map((row) => {
            const debit = Number(row.debit || 0);
            const credit = Number(row.credit || 0);

            // In ERP standard ledger: Balance = Previous + (Credit - Debit) or (Debit - Credit)
            currentBalance += (credit - debit);

            return {
                ...row,
                runningBalance: currentBalance
            };
        });
    }, [trndtls, openingBalance]);

    const filteredRows = useMemo(() => {
        if (!searchTerm) return rowsWithBalance;
        return rowsWithBalance.filter(row =>
            (row.v_no && String(row.v_no).includes(searchTerm)) ||
            (row.v_type && row.v_type.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (row.particulars && row.particulars.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (row.remarks && row.remarks.toLowerCase().includes(searchTerm.toLowerCase()))
        );
    }, [rowsWithBalance, searchTerm]);

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/ledger', {
            account_title: selectedAccount,
            start_date: startDate,
            end_date: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleClear = () => {
        setSelectedAccount('');
        setStartDate('');
        setEndDate('');
        setSearchTerm('');
        router.get('/ledger');
    };

    return (
        <AuthenticatedLayout header="General Ledger Statement">
            <Head title="Ledger - GST ERP" />

            {/* Filter Bar */}
            <div className="card shadow-sm border-0 mb-3 no-print">
                <div className="card-body">
                    <form onSubmit={handleFilterSubmit} className="row g-2 align-items-end">
                        <div className="col-md-4">
                            <label className="form-label small fw-bold text-muted">Select Account / Party</label>
                            <select
                                className="form-select form-select-sm"
                                value={selectedAccount}
                                onChange={(e) => setSelectedAccount(e.target.value)}
                                required
                            >
                                <option value="">-- Choose Account --</option>
                                <optgroup label="Customers">
                                    {customers.map(c => (
                                        <option key={`c-${c.id}`} value={c.acc_id}>{c.name}</option>
                                    ))}
                                </optgroup>
                                <optgroup label="Suppliers">
                                    {suppliers.map(s => (
                                        <option key={`s-${s.id}`} value={s.acc_id}>{s.name}</option>
                                    ))}
                                </optgroup>
                                <optgroup label="Contractors">
                                    {contractors.map(con => (
                                        <option key={`con-${con.id}`} value={con.acc_id || con.id}>{con.name || con.title}</option>
                                    ))}
                                </optgroup>
                                <optgroup label="General Accounts">
                                    {accounts.map(acc => (
                                        <option key={`a-${acc.id}`} value={acc.id}>{acc.title}</option>
                                    ))}
                                </optgroup>
                            </select>
                        </div>

                        <div className="col-md-2">
                            <label className="form-label small fw-bold text-muted">Start Date</label>
                            <input
                                type="date"
                                className="form-control form-control-sm"
                                value={startDate}
                                onChange={(e) => setStartDate(e.target.value)}
                            />
                        </div>

                        <div className="col-md-2">
                            <label className="form-label small fw-bold text-muted">End Date</label>
                            <input
                                type="date"
                                className="form-control form-control-sm"
                                value={endDate}
                                onChange={(e) => setEndDate(e.target.value)}
                            />
                        </div>

                        <div className="col-md-4 d-flex gap-2">
                            <button type="submit" className="btn btn-primary btn-sm flex-fill">
                                <i className="mdi mdi-filter me-1"></i> View Ledger
                            </button>
                            <button type="button" className="btn btn-secondary btn-sm flex-fill" onClick={handleClear}>
                                <i className="mdi mdi-refresh me-1"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {/* Balances Summary Cards */}
            <div className="row g-2 mb-3">
                <div className="col-md-3">
                    <div className="card shadow-sm border-0 bg-light p-3">
                        <small className="text-muted text-uppercase fw-bold">Opening Balance</small>
                        <h4 className="m-0 fw-bold text-dark">
                            {Number(openingBalance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card shadow-sm border-0 bg-light p-3">
                        <small className="text-muted text-uppercase fw-bold">Total Debit</small>
                        <h4 className="m-0 fw-bold text-danger">
                            {Number(totalDebit || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card shadow-sm border-0 bg-light p-3">
                        <small className="text-muted text-uppercase fw-bold">Total Credit</small>
                        <h4 className="m-0 fw-bold text-success">
                            {Number(totalCredit || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card shadow-sm border-0 bg-primary text-white p-3">
                        <small className="text-white-50 text-uppercase fw-bold">Closing Balance</small>
                        <h4 className="m-0 fw-bold text-white">
                            {Number(closingBalance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </h4>
                    </div>
                </div>
            </div>

            {/* Main Ledger Table */}
            <div className="card shadow-sm border-0">
                <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div className="d-flex align-items-center gap-2">
                        <button type="button" className="btn btn-outline-secondary btn-sm no-print" onClick={() => window.print()}>
                            <i className="mdi mdi-printer me-1"></i> Print Statement
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
                                placeholder="Search ledger entries..."
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
                                    <th>Date</th>
                                    <th>Voucher #</th>
                                    <th>Type</th>
                                    <th>Particulars / Remarks</th>
                                    <th className="text-end">Debit</th>
                                    <th className="text-end">Credit</th>
                                    <th className="text-end">Running Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                {/* Opening Balance Row */}
                                <tr className="table-info fw-semibold">
                                    <td>-</td>
                                    <td>-</td>
                                    <td><span className="badge bg-primary">OPENING</span></td>
                                    <td>Opening Balance Brought Forward</td>
                                    <td className="text-end">-</td>
                                    <td className="text-end">-</td>
                                    <td className="text-end">
                                        {Number(openingBalance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                    </td>
                                </tr>

                                {filteredRows.length > 0 ? (
                                    filteredRows.map((row, idx) => (
                                        <tr key={row.id || idx}>
                                            <td className="text-muted">{row.v_date || row.date || '-'}</td>
                                            <td className="fw-bold text-primary">{row.v_no || '-'}</td>
                                            <td>
                                                <span className="badge bg-secondary-lighten text-secondary">
                                                    {row.v_type || '-'}
                                                </span>
                                            </td>
                                            <td className="text-dark small text-truncate" style={{ maxWidth: '300px' }}>
                                                {row.particulars || row.remarks || row.description || '-'}
                                            </td>
                                            <td className="text-end text-danger fw-semibold">
                                                {row.debit ? Number(row.debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                            </td>
                                            <td className="text-end text-success fw-semibold">
                                                {row.credit ? Number(row.credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                            </td>
                                            <td className="text-end fw-bold text-dark">
                                                {row.runningBalance.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="7" className="text-center py-5 text-muted">
                                            {selectedAccount ? 'No transaction records found for this account in the specified date range.' : 'Please select an account above to generate ledger.'}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                            <tfoot className="table-light fw-bold">
                                <tr>
                                    <td colSpan="4" className="text-end">Closing Balance:</td>
                                    <td className="text-end text-danger">
                                        {Number(totalDebit || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                    </td>
                                    <td className="text-end text-success">
                                        {Number(totalCredit || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                    </td>
                                    <td className="text-end text-primary">
                                        {Number(closingBalance || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
