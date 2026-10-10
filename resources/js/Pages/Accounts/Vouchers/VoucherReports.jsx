import React, { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function VoucherReports({
    title = 'Voucher Reports',
    vType = 'CPV',
    trndtls = [],
    accountMasters = [],
    vNoList = [],
    accountIdList = [],
    createRoute = '/payment/create',
    editRoutePrefix = '/payment',
    deleteRoutePrefix = '/payment-delete',
    filters = {}
}) {
    const [searchTerm, setSearchTerm] = useState('');
    const [startDate, setStartDate] = useState(filters.startDate || '');
    const [endDate, setEndDate] = useState(filters.endDate || '');
    const [selectedVNo, setSelectedVNo] = useState(filters.v_no || '');
    const [selectedAccount, setSelectedAccount] = useState(filters.account_id || '');
    const [selectedStatus, setSelectedStatus] = useState(filters.status || '');

    // Client-side quick filter
    const filteredRecords = useMemo(() => {
        return trndtls.filter(item => {
            const matchesSearch =
                !searchTerm ||
                (item.v_no && String(item.v_no).includes(searchTerm)) ||
                (item.remarks && item.remarks.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.description && item.description.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.accounts?.title && item.accounts.title.toLowerCase().includes(searchTerm.toLowerCase()));

            const matchesVNo = !selectedVNo || String(item.v_no) === String(selectedVNo);
            const matchesAccount = !selectedAccount || String(item.account_id) === String(selectedAccount);
            const matchesStatus = !selectedStatus || String(item.status) === String(selectedStatus);

            return matchesSearch && matchesVNo && matchesAccount && matchesStatus;
        });
    }, [trndtls, searchTerm, selectedVNo, selectedAccount, selectedStatus]);

    // Calculate totals
    const totalDebit = useMemo(() => {
        return filteredRecords.reduce((sum, item) => sum + Number(item.debit || 0), 0);
    }, [filteredRecords]);

    const totalCredit = useMemo(() => {
        return filteredRecords.reduce((sum, item) => sum + Number(item.credit || 0), 0);
    }, [filteredRecords]);

    const totalAmount = useMemo(() => {
        return filteredRecords.reduce((sum, item) => sum + Number(item.amount || item.debit || item.credit || 0), 0);
    }, [filteredRecords]);

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get(window.location.pathname, {
            start_date: startDate,
            end_date: endDate,
            v_no: selectedVNo,
            account_id: selectedAccount,
            status: selectedStatus,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleClear = () => {
        setStartDate('');
        setEndDate('');
        setSelectedVNo('');
        setSelectedAccount('');
        setSelectedStatus('');
        setSearchTerm('');
        router.get(window.location.pathname);
    };

    const handleDelete = (id, vNo) => {
        if (confirm(`Are you sure you want to delete Voucher #${vNo}?`)) {
            router.delete(`${deleteRoutePrefix}/${id}`, {
                preserveScroll: true,
            });
        }
    };

    return (
        <AuthenticatedLayout header={title}>
            <Head title={`${title} - GST ERP`} />

            {/* Filter Bar */}
            <div className="card shadow-sm border-0 mb-3 no-print">
                <div className="card-body">
                    <form onSubmit={handleFilterSubmit} className="row g-2 align-items-end">
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

                        <div className="col-md-2">
                            <label className="form-label small fw-bold text-muted">Voucher #</label>
                            <select
                                className="form-select form-select-sm"
                                value={selectedVNo}
                                onChange={(e) => setSelectedVNo(e.target.value)}
                            >
                                <option value="">All Vouchers</option>
                                {vNoList.map(v => (
                                    <option key={v} value={v}>Voucher #{v}</option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-3">
                            <label className="form-label small fw-bold text-muted">Account</label>
                            <select
                                className="form-select form-select-sm"
                                value={selectedAccount}
                                onChange={(e) => setSelectedAccount(e.target.value)}
                            >
                                <option value="">All Accounts</option>
                                {accountMasters.map(acc => (
                                    <option key={acc.id} value={acc.id}>{acc.title}</option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-3 d-flex gap-2">
                            <button type="submit" className="btn btn-primary btn-sm flex-fill">
                                <i className="mdi mdi-filter me-1"></i> Filter
                            </button>
                            <button type="button" className="btn btn-secondary btn-sm flex-fill" onClick={handleClear}>
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
                        {createRoute && (
                            <a href={createRoute} className="btn btn-primary btn-sm no-print">
                                <i className="mdi mdi-plus-circle me-1"></i> New {vType} Voucher
                            </a>
                        )}
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
                                placeholder="Search by description, remarks..."
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
                                    <th>Narration / Remarks</th>
                                    <th className="text-end">Debit</th>
                                    <th className="text-end">Credit</th>
                                    <th>Status</th>
                                    <th className="text-center no-print" style={{ width: '130px' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredRecords.length > 0 ? (
                                    filteredRecords.map((item, idx) => (
                                        <tr key={item.id || idx}>
                                            <td className="fw-bold text-primary">
                                                {item.v_type || vType}-{item.v_no}
                                            </td>
                                            <td className="text-muted">{item.date || item.v_date || '-'}</td>
                                            <td className="fw-medium text-dark">
                                                {item.accounts?.title || item.account_title || item.account?.title || 'Account #' + item.account_id}
                                            </td>
                                            <td className="text-muted small text-truncate" style={{ maxWidth: '280px' }}>
                                                {item.remarks || item.description || '-'}
                                            </td>
                                            <td className="text-end fw-semibold text-danger">
                                                {item.debit ? Number(item.debit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                            </td>
                                            <td className="text-end fw-semibold text-success">
                                                {item.credit ? Number(item.credit).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                            </td>
                                            <td>
                                                <span className={`badge ${item.status === 'Cancel' ? 'bg-danger-lighten text-danger' : 'bg-success-lighten text-success'}`}>
                                                    {item.status || 'Active'}
                                                </span>
                                            </td>
                                            <td className="text-center no-print">
                                                <div className="btn-group btn-group-sm">
                                                    <a
                                                        href={`${editRoutePrefix}/${item.v_no}/edit`}
                                                        className="btn btn-outline-primary"
                                                        title="Edit Voucher"
                                                    >
                                                        <i className="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a
                                                        href={`/invoicing/${item.v_no}/${item.v_type || vType}`}
                                                        className="btn btn-outline-secondary"
                                                        title="Print Invoice"
                                                        target="_blank"
                                                        rel="noreferrer"
                                                    >
                                                        <i className="mdi mdi-printer"></i>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        className="btn btn-outline-danger"
                                                        title="Delete Voucher"
                                                        onClick={() => handleDelete(item.id, item.v_no)}
                                                    >
                                                        <i className="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="8" className="text-center py-5 text-muted">
                                            <i className="mdi mdi-receipt font-28 d-block mb-2 opacity-50"></i>
                                            No {title} records found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                            {filteredRecords.length > 0 && (
                                <tfoot className="table-light fw-bold">
                                    <tr>
                                        <td colSpan="4" className="text-end">Total Summary:</td>
                                        <td className="text-end text-danger">
                                            {totalDebit > 0 ? totalDebit.toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                        </td>
                                        <td className="text-end text-success">
                                            {totalCredit > 0 ? totalCredit.toLocaleString('en-US', { minimumFractionDigits: 2 }) : '-'}
                                        </td>
                                        <td colSpan="2"></td>
                                    </tr>
                                </tfoot>
                            )}
                        </table>
                    </div>
                </div>

                <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center text-muted small">
                    <div>Showing <b>{filteredRecords.length}</b> of <b>{trndtls.length}</b> entries</div>
                    <div>ERP Financial Audit</div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
