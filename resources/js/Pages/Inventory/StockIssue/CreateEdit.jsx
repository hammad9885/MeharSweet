import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function CreateEdit({
    stockIssue = null,
    products = [],
    contractors = [],
    nextInvNo = 1,
    previousBalances = {},
    contractorByProduct = {}
}) {
    const isEdit = !!stockIssue;

    const initialDetails = isEdit && stockIssue.details && stockIssue.details.length > 0
        ? stockIssue.details.map(d => ({
            id: d.id,
            product_id: d.product_id || '',
            qty: d.qty || 0,
            previous: d.previous || 0,
            total: (parseFloat(d.previous) || 0) + (parseFloat(d.qty) || 0)
        }))
        : [{ product_id: '', qty: 0, previous: 0, total: 0 }];

    const { data, setData, post, put, processing, errors } = useForm({
        inv_no: isEdit ? stockIssue.inv_no : nextInvNo,
        date: isEdit ? stockIssue.date : new Date().toISOString().split('T')[0],
        product_id: isEdit ? stockIssue.product_id : '',
        contractor_id: isEdit ? stockIssue.contractor_id : '',
        lot_no: isEdit ? stockIssue.lot_no : '',
        details: initialDetails
    });

    const handleHeaderProductChange = (prodId) => {
        const autoContractor = contractorByProduct[prodId] || data.contractor_id;
        setData(prev => ({
            ...prev,
            product_id: prodId,
            contractor_id: autoContractor
        }));
    };

    const handleRowProductChange = (index, prodId) => {
        const newDetails = [...data.details];
        const prevBal = previousBalances[prodId] || 0;
        const qty = parseFloat(newDetails[index].qty) || 0;

        newDetails[index] = {
            ...newDetails[index],
            product_id: prodId,
            previous: prevBal,
            total: prevBal + qty
        };
        setData('details', newDetails);
    };

    const handleQtyChange = (index, qtyVal) => {
        const newDetails = [...data.details];
        const qty = parseFloat(qtyVal) || 0;
        const prev = parseFloat(newDetails[index].previous) || 0;

        newDetails[index] = {
            ...newDetails[index],
            qty: qtyVal,
            total: prev + qty
        };
        setData('details', newDetails);
    };

    const addRow = () => {
        setData('details', [...data.details, {
            product_id: '',
            qty: 0,
            previous: 0,
            total: 0
        }]);
    };

    const removeRow = (index) => {
        if (data.details.length === 1) return;
        setData('details', data.details.filter((_, i) => i !== index));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/stock-issue/${stockIssue.id}`);
        } else {
            post('/stock-issue');
        }
    };

    const totalQty = data.details.reduce((sum, d) => sum + (parseFloat(d.qty) || 0), 0);

    return (
        <AuthenticatedLayout header={isEdit ? `Edit Stock Issue #${stockIssue.inv_no}` : 'Create Stock Issue'}>
            <Head title={`${isEdit ? 'Edit' : 'Create'} Stock Issue - Real ERP`} />

            <div className="container-fluid py-4">
                <form onSubmit={handleSubmit}>
                    {/* Header Info */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">
                                <i className="mdi mdi-arrow-up-bold-box-outline me-2 text-primary"></i>
                                {isEdit ? `Edit Stock Issue Challan #${stockIssue.inv_no}` : 'Outward Issue Challan Header'}
                            </h5>
                            <Link href="/stock-issue" className="btn btn-outline-secondary btn-sm">
                                <i className="mdi mdi-arrow-left me-1"></i> Back to Register
                            </Link>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-2">
                                    <label className="form-label small fw-semibold text-muted">Inv No.</label>
                                    <input
                                        type="number"
                                        className="form-control bg-light"
                                        value={data.inv_no}
                                        readOnly
                                    />
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label small fw-semibold text-muted">Date *</label>
                                    <input
                                        type="date"
                                        className={`form-control ${errors.date ? 'is-invalid' : ''}`}
                                        value={data.date}
                                        onChange={(e) => setData('date', e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Parent Product</label>
                                    <select
                                        className="form-select"
                                        value={data.product_id}
                                        onChange={(e) => handleHeaderProductChange(e.target.value)}
                                    >
                                        <option value="">-- Select Product --</option>
                                        {products.map(p => (
                                            <option key={p.id} value={p.id}>{p.product_name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Contractor</label>
                                    <select
                                        className="form-select"
                                        value={data.contractor_id}
                                        onChange={(e) => setData('contractor_id', e.target.value)}
                                    >
                                        <option value="">-- Select Contractor --</option>
                                        {contractors.map(c => (
                                            <option key={c.id} value={c.id}>{c.title}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label small fw-semibold text-muted">Lot No.</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Lot #"
                                        value={data.lot_no}
                                        onChange={(e) => setData('lot_no', e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Outward Items Table */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">
                                <i className="mdi mdi-format-list-bulleted me-2 text-warning"></i> Issued Items & Products
                            </h5>
                            <button type="button" onClick={addRow} className="btn btn-outline-primary btn-sm">
                                <i className="mdi mdi-plus me-1"></i> Add Row
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th style={{ width: '40px' }}>#</th>
                                            <th>Product / Material Item *</th>
                                            <th style={{ width: '160px' }}>Quantity *</th>
                                            <th style={{ width: '160px' }}>Previous</th>
                                            <th style={{ width: '160px' }}>Total</th>
                                            <th style={{ width: '50px' }} className="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {data.details.map((row, idx) => (
                                            <tr key={idx}>
                                                <td className="text-center text-muted fw-semibold">{idx + 1}</td>
                                                <td>
                                                    <select
                                                        className="form-select form-select-sm"
                                                        value={row.product_id}
                                                        onChange={(e) => handleRowProductChange(idx, e.target.value)}
                                                        required
                                                    >
                                                        <option value="">-- Select Product --</option>
                                                        {products.map(p => (
                                                            <option key={p.id} value={p.id}>{p.product_name}</option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control form-control-sm fw-bold"
                                                        value={row.qty}
                                                        onChange={(e) => handleQtyChange(idx, e.target.value)}
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-sm bg-light"
                                                        value={row.previous}
                                                        readOnly
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-sm bg-light fw-bold text-primary"
                                                        value={row.total}
                                                        readOnly
                                                    />
                                                </td>
                                                <td className="text-center">
                                                    <button
                                                        type="button"
                                                        onClick={() => removeRow(idx)}
                                                        className="btn btn-sm btn-outline-danger p-1"
                                                        title="Remove Row"
                                                        disabled={data.details.length === 1}
                                                    >
                                                        <i className="mdi mdi-close"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot className="bg-light fw-bold">
                                        <tr>
                                            <td colSpan="2" className="text-end">Total Issued Quantity:</td>
                                            <td className="text-warning fs-6">{totalQty}</td>
                                            <td colSpan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                            <button type="button" onClick={addRow} className="btn btn-sm btn-light border">
                                <i className="mdi mdi-plus me-1"></i> Add Another Row
                            </button>
                            <div className="d-flex gap-2">
                                <Link href="/stock-issue" className="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button type="submit" className="btn btn-success px-4" disabled={processing}>
                                    {processing ? 'Saving...' : (isEdit ? 'Update Stock Issue' : 'Save Stock Issue Challan')}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
