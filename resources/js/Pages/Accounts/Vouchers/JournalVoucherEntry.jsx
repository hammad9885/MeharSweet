import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function JournalVoucherEntry({
    title = 'Journal Voucher (JV)',
    accounts = [],
    voucher = null, // array of existing TRNDTL entries if in edit mode
    v_no = null,
    storeRoute = '/journal_voucher',
    updateRoute = null,
    reportsRoute = '/journal_voucher/reports',
}) {
    const isEdit = Array.isArray(voucher) && voucher.length > 0;
    const initialVoucher = isEdit ? voucher[0] : null;

    const today = new Date().toISOString().split('T')[0];
    const [voucherDate, setVoucherDate] = useState(
        initialVoucher?.date || initialVoucher?.v_date || today
    );

    const createEmptyRow = () => ({
        id: null,
        account_title: '',
        description: '',
        debit: '',
        credit: '',
    });

    const [entries, setEntries] = useState(() => {
        if (isEdit) {
            return voucher.map(v => ({
                id: v.id,
                account_title: String(v.account_id || ''),
                description: v.description || v.remarks || '',
                debit: v.debit && Number(v.debit) !== 0 ? String(v.debit) : '',
                credit: v.credit && Number(v.credit) !== 0 ? String(v.credit) : '',
            }));
        }
        return [createEmptyRow(), createEmptyRow()];
    });

    const [deletedIds, setDeletedIds] = useState([]);
    const [processing, setProcessing] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');

    const handleRowChange = (index, field, value) => {
        setEntries(prev => {
            const next = [...prev];
            const updated = { ...next[index], [field]: value };

            // If user inputs a debit, clear credit on that same line (or vice versa)
            if (field === 'debit' && value !== '' && parseFloat(value) > 0) {
                updated.credit = '';
            } else if (field === 'credit' && value !== '' && parseFloat(value) > 0) {
                updated.debit = '';
            }

            next[index] = updated;
            return next;
        });
    };

    const addRow = () => {
        setEntries(prev => [...prev, createEmptyRow()]);
    };

    const removeRow = (index) => {
        if (entries.length <= 2) {
            alert('A Journal Voucher must have at least two entries.');
            return;
        }
        const row = entries[index];
        if (row.id) {
            setDeletedIds(prev => [...prev, row.id]);
        }
        setEntries(prev => prev.filter((_, idx) => idx !== index));
    };

    // Calculate totals
    const totalDebit = entries.reduce(
        (sum, entry) => sum + (parseFloat(entry.debit) || 0),
        0
    );
    const totalCredit = entries.reduce(
        (sum, entry) => sum + (parseFloat(entry.credit) || 0),
        0
    );
    const difference = Math.abs(totalDebit - totalCredit);
    const isBalanced = difference < 0.01 && totalDebit > 0;

    const handleSubmit = (e) => {
        e.preventDefault();
        setErrorMessage('');

        if (entries.length < 2) {
            setErrorMessage('A Journal Voucher must have at least 2 entries.');
            return;
        }

        // Validate entries
        for (let i = 0; i < entries.length; i++) {
            const row = entries[i];
            if (!row.account_title) {
                setErrorMessage(`Line #${i + 1}: Please select an Account.`);
                return;
            }
            const d = parseFloat(row.debit) || 0;
            const c = parseFloat(row.credit) || 0;
            if (d <= 0 && c <= 0) {
                setErrorMessage(`Line #${i + 1}: Either Debit or Credit must be greater than 0.`);
                return;
            }
        }

        if (!isBalanced) {
            setErrorMessage(
                `Voucher is not balanced! Total Debit (${totalDebit.toFixed(2)}) must equal Total Credit (${totalCredit.toFixed(2)}). Difference: ${difference.toFixed(2)}`
            );
            return;
        }

        setProcessing(true);

        if (isEdit && updateRoute) {
            const payload = {
                voucher_date: voucherDate,
                _method: 'PUT',
                delete_entry_ids: deletedIds,
                entries: entries.map(row => ({
                    id: row.id,
                    date: voucherDate,
                    account_title: row.account_title,
                    description: row.description || '',
                    debit: parseFloat(row.debit) || 0,
                    credit: parseFloat(row.credit) || 0,
                })),
            };

            router.post(updateRoute, payload, {
                onFinish: () => setProcessing(false),
            });
        } else {
            // For store: provide both standard array and traditional arrays to guarantee backend acceptance
            const payload = {
                voucher_date: voucherDate,
                v_type: 'JV',
                total_debit: totalDebit,
                total_credit: totalCredit,
                entry_account_title: entries.map(r => r.account_title),
                entry_debit: entries.map(r => parseFloat(r.debit) || 0),
                entry_credit: entries.map(r => parseFloat(r.credit) || 0),
                entry_description: entries.map(r => r.description || ''),
                entries: entries.map(row => ({
                    date: voucherDate,
                    account_title: row.account_title,
                    description: row.description || '',
                    debit: parseFloat(row.debit) || 0,
                    credit: parseFloat(row.credit) || 0,
                })),
            };

            router.post(storeRoute, payload, {
                onFinish: () => setProcessing(false),
            });
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={`${title} - Mehar Sweets ERP`} />

            <div className="container-fluid py-4">
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <div className="d-flex align-items-center gap-2">
                            <h3 className="fw-bold text-dark mb-0">{title}</h3>
                            <span className="badge bg-purple fs-6" style={{ backgroundColor: '#6f42c1', color: '#fff' }}>
                                JV
                            </span>
                            {isEdit && (
                                <span className="badge bg-warning text-dark fs-6">
                                    Edit #{v_no || initialVoucher?.v_no}
                                </span>
                            )}
                        </div>
                        <p className="text-muted small mb-0 mt-1">
                            Double-entry journal voucher with real-time debit and credit balance verification.
                        </p>
                    </div>
                    <div className="d-flex gap-2">
                        <Link href={reportsRoute} className="btn btn-outline-secondary">
                            <i className="mdi mdi-arrow-left me-1"></i> Back to Reports
                        </Link>
                    </div>
                </div>

                {errorMessage && (
                    <div className="alert alert-danger alert-dismissible fade show" role="alert">
                        <i className="mdi mdi-alert-circle me-2"></i>
                        {errorMessage}
                        <button
                            type="button"
                            className="btn-close"
                            onClick={() => setErrorMessage('')}
                            aria-label="Close"
                        ></button>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    {/* Header Controls */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-header bg-white py-3 border-bottom">
                            <h5 className="card-title mb-0 fw-semibold text-secondary">
                                General Details
                            </h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold text-muted small text-uppercase">
                                        Voucher Date <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={voucherDate}
                                        onChange={(e) => setVoucherDate(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold text-muted small text-uppercase">
                                        Voucher Type
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control bg-light"
                                        value="JV (Journal Voucher)"
                                        readOnly
                                    />
                                </div>
                                {isEdit && (
                                    <div className="col-md-4">
                                        <label className="form-label fw-semibold text-muted small text-uppercase">
                                            Voucher Number
                                        </label>
                                        <input
                                            type="text"
                                            className="form-control bg-light"
                                            value={`JV-${v_no || initialVoucher?.v_no}`}
                                            readOnly
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Entries Table */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold text-secondary">
                                Journal Transactions
                            </h5>
                            <button
                                type="button"
                                className="btn btn-outline-primary btn-sm"
                                onClick={addRow}
                            >
                                <i className="mdi mdi-plus me-1"></i> Add Entry Line
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-hover align-middle mb-0">
                                    <thead className="table-light">
                                        <tr>
                                            <th style={{ width: '4%' }} className="text-center">#</th>
                                            <th style={{ width: '38%' }}>
                                                Account Title <span className="text-danger">*</span>
                                            </th>
                                            <th style={{ width: '28%' }}>Narration / Description</th>
                                            <th style={{ width: '13%' }} className="text-end">
                                                Debit (Rs.)
                                            </th>
                                            <th style={{ width: '13%' }} className="text-end">
                                                Credit (Rs.)
                                            </th>
                                            <th style={{ width: '4%' }} className="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {entries.map((entry, idx) => (
                                            <tr key={idx}>
                                                <td className="text-center text-muted fw-bold">{idx + 1}</td>
                                                <td>
                                                    <select
                                                        className="form-select"
                                                        value={entry.account_title}
                                                        onChange={(e) => handleRowChange(idx, 'account_title', e.target.value)}
                                                        required
                                                    >
                                                        <option value="">-- Select Account Title --</option>
                                                        {accounts.map(acc => (
                                                            <option key={acc.id} value={acc.id}>
                                                                {acc.title || acc.account_title}
                                                                {acc.code ? ` (${acc.code})` : ''}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        placeholder="Description / Narration"
                                                        value={entry.description}
                                                        onChange={(e) => handleRowChange(idx, 'description', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        className="form-control text-end font-monospace"
                                                        placeholder="0.00"
                                                        value={entry.debit}
                                                        onChange={(e) => handleRowChange(idx, 'debit', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        className="form-control text-end font-monospace"
                                                        placeholder="0.00"
                                                        value={entry.credit}
                                                        onChange={(e) => handleRowChange(idx, 'credit', e.target.value)}
                                                    />
                                                </td>
                                                <td className="text-center">
                                                    <button
                                                        type="button"
                                                        className="btn btn-outline-danger btn-sm border-0"
                                                        onClick={() => removeRow(idx)}
                                                        title="Delete row"
                                                        disabled={entries.length <= 2}
                                                    >
                                                        <i className="mdi mdi-delete-outline fs-5"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot className="table-light border-top">
                                        <tr className="fw-bold fs-6">
                                            <td colSpan="3" className="text-end text-uppercase">Totals:</td>
                                            <td className="text-end text-danger font-monospace">
                                                Rs. {totalDebit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td className="text-end text-success font-monospace">
                                                Rs. {totalCredit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {/* Balance verification bar */}
                        <div className="card-footer bg-white py-3 border-top">
                            <div className="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <button
                                    type="button"
                                    className="btn btn-outline-primary btn-sm"
                                    onClick={addRow}
                                >
                                    <i className="mdi mdi-plus me-1"></i> Add Another Row
                                </button>

                                <div className="d-flex align-items-center gap-3">
                                    {isBalanced ? (
                                        <span className="badge bg-success-lighten text-success fs-6 py-2 px-3">
                                            <i className="mdi mdi-check-circle me-1"></i>
                                            Voucher is Balanced (Rs. {totalDebit.toFixed(2)})
                                        </span>
                                    ) : (
                                        <span className="badge bg-danger-lighten text-danger fs-6 py-2 px-3">
                                            <i className="mdi mdi-alert-circle me-1"></i>
                                            Difference: Rs. {difference.toFixed(2)} (Must be 0.00)
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="d-flex justify-content-end gap-2">
                        <Link href={reportsRoute} className="btn btn-light px-4">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            className="btn btn-primary px-4 fw-semibold"
                            disabled={processing || !isBalanced}
                        >
                            {processing ? (
                                <>
                                    <span className="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <i className="mdi mdi-content-save me-1"></i>
                                    {isEdit ? 'Update Journal Voucher' : 'Save Journal Voucher'}
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
