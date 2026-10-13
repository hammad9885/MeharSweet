import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function VoucherEntry({
    title = 'Voucher Entry',
    vType = 'BRV',
    isPayment = false,
    accountMasters = [],
    accounts = [],
    customerUrduTitles = {},
    supplierUrduTitles = {},
    storeRoute = '/bank_recipt',
    updateRoute = null,
    reportsRoute = '/bank_recipt/reports',
    voucher = null, // array of existing entries if edit mode
    v_no = null,
}) {
    const isEdit = Array.isArray(voucher) && voucher.length > 0;
    const initialVoucher = isEdit ? voucher[0] : null;

    // Default entry template
    const createEmptyEntry = () => ({
        id: null,
        account: '',
        description: '',
        amount: '',
    });

    // Initial state setup
    const today = new Date().toISOString().split('T')[0];
    const [voucherDate, setVoucherDate] = useState(
        initialVoucher?.date || initialVoucher?.v_date || today
    );
    const [selectedCashAccount, setSelectedCashAccount] = useState(
        initialVoucher?.cash_id || (accountMasters.length > 0 ? accountMasters[0].id : '')
    );
    const [file, setFile] = useState(null);
    const [processing, setProcessing] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');

    // Pre-populate entries if editing, else 1 empty entry
    const [entries, setEntries] = useState(() => {
        if (isEdit) {
            return voucher.map(v => ({
                id: v.id,
                account: String(v.account_id || ''),
                description: v.description || v.remarks || '',
                amount: String(isPayment ? (v.debit || '') : (v.credit || '')),
            }));
        }
        return [createEmptyEntry()];
    });

    // Row management
    const handleEntryChange = (index, field, value) => {
        setEntries(prev => {
            const next = [...prev];
            next[index] = { ...next[index], [field]: value };
            return next;
        });
    };

    const addRow = () => {
        setEntries(prev => [...prev, createEmptyEntry()]);
    };

    const removeRow = (index) => {
        if (entries.length <= 1) {
            alert('A voucher must have at least one entry.');
            return;
        }
        setEntries(prev => prev.filter((_, idx) => idx !== index));
    };

    // Calculate total amount
    const totalAmount = entries.reduce(
        (sum, entry) => sum + (parseFloat(entry.amount) || 0),
        0
    );

    // Form Submission
    const handleSubmit = (e) => {
        e.preventDefault();
        setErrorMessage('');

        if (!selectedCashAccount) {
            setErrorMessage('Please select a Cash or Bank account.');
            return;
        }

        // Validate entries
        for (let i = 0; i < entries.length; i++) {
            const entry = entries[i];
            if (!entry.account) {
                setErrorMessage(`Entry #${i + 1}: Please select an Account.`);
                return;
            }
            const amt = parseFloat(entry.amount);
            if (isNaN(amt) || amt <= 0) {
                setErrorMessage(`Entry #${i + 1}: Amount must be greater than 0.`);
                return;
            }
        }

        setProcessing(true);

        // Build FormData
        const formData = new FormData();
        formData.append('v_type', vType);
        formData.append('voucher_date', voucherDate);
        formData.append('entryDate', voucherDate); // Compatible with BankPayment
        formData.append('account', selectedCashAccount);
        formData.append('total_amount', totalAmount);

        if (file) {
            formData.append('file', file);
        }

        // Add entries array
        entries.forEach((entry, idx) => {
            if (entry.id) {
                formData.append(`entries[${idx}][id]`, entry.id);
            }
            formData.append(`entries[${idx}][cash]`, selectedCashAccount);
            formData.append(`entries[${idx}][account]`, entry.account);
            formData.append(`entries[${idx}][description]`, entry.description || (vType === 'OC' ? 'Office Cash Voucher' : ''));
            formData.append(`entries[${idx}][date]`, voucherDate);

            if (isPayment) {
                formData.append(`entries[${idx}][debit]`, entry.amount);
                formData.append(`entries[${idx}][credit]`, '0');
            } else {
                formData.append(`entries[${idx}][credit]`, entry.amount);
                formData.append(`entries[${idx}][debit]`, '0');
            }
        });

        if (isEdit && updateRoute) {
            // Laravel PUT via FormData spoofing
            formData.append('_method', 'PUT');
            router.post(updateRoute, formData, {
                onFinish: () => setProcessing(false),
            });
        } else {
            router.post(storeRoute, formData, {
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
                            <span className="badge bg-primary fs-6">{vType}</span>
                            {isEdit && (
                                <span className="badge bg-warning text-dark fs-6">
                                    Edit #{v_no || initialVoucher?.v_no}
                                </span>
                            )}
                        </div>
                        <p className="text-muted small mb-0 mt-1">
                            {isPayment
                                ? 'Record outgoing payment transactions with ledger posting'
                                : 'Record incoming receipt transactions with ledger posting'}
                        </p>
                    </div>

                    <Link
                        href={reportsRoute}
                        className="btn btn-outline-secondary d-flex align-items-center gap-1"
                    >
                        <i className="mdi mdi-arrow-left"></i>
                        <span>Back to Reports</span>
                    </Link>
                </div>

                {/* Error Banner */}
                {errorMessage && (
                    <div className="alert alert-danger alert-dismissible fade show" role="alert">
                        <i className="mdi mdi-alert-circle me-1"></i>
                        {errorMessage}
                        <button
                            type="button"
                            className="btn-close"
                            onClick={() => setErrorMessage('')}
                        ></button>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    {/* Voucher Header Card */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white py-3 border-bottom">
                            <h5 className="card-title mb-0 fw-semibold">Voucher Details</h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                {/* Voucher Date */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        Date <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={voucherDate}
                                        onChange={(e) => setVoucherDate(e.target.value)}
                                        required
                                    />
                                </div>

                                {/* Bank / Cash Account */}
                                <div className="col-md-5">
                                    <label className="form-label fw-semibold">
                                        {isPayment ? 'Paid From (Bank / Cash)' : 'Deposit To (Bank / Cash)'}{' '}
                                        <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className="form-select"
                                        value={selectedCashAccount}
                                        onChange={(e) => setSelectedCashAccount(e.target.value)}
                                        required
                                    >
                                        <option value="">-- Select Bank/Cash Account --</option>
                                        {accountMasters.map((acc) => (
                                            <option key={acc.id} value={acc.id}>
                                                {acc.title}
                                                {acc.account_code ? ` [${acc.account_code}]` : ''}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* File Upload */}
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold">
                                        Attachment (Optional)
                                    </label>
                                    <input
                                        type="file"
                                        className="form-control"
                                        accept="image/*,.pdf"
                                        onChange={(e) => setFile(e.target.files[0] || null)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Entries Table Card */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">Transaction Entries</h5>
                            <button
                                type="button"
                                className="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                onClick={addRow}
                            >
                                <i className="mdi mdi-plus"></i>
                                <span>Add Row</span>
                            </button>
                        </div>

                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="table-light small text-muted text-uppercase">
                                        <tr>
                                            <th style={{ width: '45px' }} className="text-center">#</th>
                                            <th style={{ width: '38%' }}>Account Title <span className="text-danger">*</span></th>
                                            <th>Description / Remarks</th>
                                            <th style={{ width: '22%' }} className="text-end">
                                                Amount ({isPayment ? 'Debit' : 'Credit'}) <span className="text-danger">*</span>
                                            </th>
                                            <th style={{ width: '60px' }} className="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {entries.map((entry, index) => {
                                            const urdu = customerUrduTitles[entry.account] || supplierUrduTitles[entry.account] || '';
                                            return (
                                                <tr key={index}>
                                                    <td className="text-center fw-bold text-muted">
                                                        {index + 1}
                                                    </td>
                                                    <td>
                                                        <select
                                                            className="form-select form-select-sm"
                                                            value={entry.account}
                                                            onChange={(e) => handleEntryChange(index, 'account', e.target.value)}
                                                            required
                                                        >
                                                            <option value="">-- Select Account Title --</option>
                                                            {accounts.map((acc) => {
                                                                const urduTitle = customerUrduTitles[acc.id] || supplierUrduTitles[acc.id] || '';
                                                                return (
                                                                    <option key={acc.id} value={acc.id}>
                                                                        {acc.title} {urduTitle ? `(${urduTitle})` : ''}
                                                                    </option>
                                                                );
                                                            })}
                                                        </select>
                                                        {urdu && (
                                                            <small className="text-muted d-block mt-1" dir="rtl">
                                                                {urdu}
                                                            </small>
                                                        )}
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            className="form-control form-control-sm"
                                                            placeholder="Remarks / Description..."
                                                            value={entry.description}
                                                            onChange={(e) => handleEntryChange(index, 'description', e.target.value)}
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            step="any"
                                                            min="0"
                                                            className="form-control form-control-sm text-end fw-semibold"
                                                            placeholder="0.00"
                                                            value={entry.amount}
                                                            onChange={(e) => handleEntryChange(index, 'amount', e.target.value)}
                                                            required
                                                        />
                                                    </td>
                                                    <td className="text-center">
                                                        <button
                                                            type="button"
                                                            className="btn btn-outline-danger btn-sm"
                                                            title="Remove Row"
                                                            onClick={() => removeRow(index)}
                                                            disabled={entries.length <= 1}
                                                        >
                                                            <i className="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                    <tfoot className="table-light">
                                        <tr>
                                            <td colSpan="3" className="text-end fw-bold">
                                                Total Amount:
                                            </td>
                                            <td className="text-end fw-bold text-primary fs-5">
                                                Rs. {totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div className="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
                            <button
                                type="button"
                                className="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                                onClick={addRow}
                            >
                                <i className="mdi mdi-plus"></i>
                                <span>Add Another Row</span>
                            </button>

                            <div className="d-flex gap-2">
                                <Link href={reportsRoute} className="btn btn-secondary px-4">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    className="btn btn-success px-4 d-flex align-items-center gap-1"
                                    disabled={processing}
                                >
                                    <i className="mdi mdi-check"></i>
                                    <span>{processing ? 'Saving...' : (isEdit ? 'Update Voucher' : 'Submit Voucher')}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
