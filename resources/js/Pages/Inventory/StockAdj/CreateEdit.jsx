import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function CreateEdit({
    stock_adj = null,
    nextVNo = 1,
    preparedByCid = '',
    items = []
}) {
    const isEdit = !!stock_adj;

    const initialDetails = isEdit && stock_adj.details && stock_adj.details.length > 0
        ? stock_adj.details.map(d => ({
            item_id: d.item_id || '',
            qty: d.qty || 0,
            rate: d.rate || 0,
        }))
        : [{ item_id: '', qty: 0, rate: 0 }];

    const { data, setData, post, put, processing, errors } = useForm({
        v_no: isEdit ? stock_adj.v_no : nextVNo,
        v_date: isEdit ? stock_adj.v_date : new Date().toISOString().split('T')[0],
        prepared_by: isEdit ? stock_adj.prepared_by : preparedByCid,
        details: initialDetails,
    });

    const handleItemChange = (index, itemId) => {
        const selectedItem = items.find(it => String(it.id) === String(itemId));
        const newDetails = [...data.details];
        newDetails[index] = {
            ...newDetails[index],
            item_id: itemId,
            rate: selectedItem ? (selectedItem.purchase || selectedItem.rate || 0) : newDetails[index].rate
        };
        setData('details', newDetails);
    };

    const handleFieldChange = (index, field, value) => {
        const newDetails = [...data.details];
        newDetails[index] = {
            ...newDetails[index],
            [field]: value
        };
        setData('details', newDetails);
    };

    const addRow = () => {
        setData('details', [...data.details, { item_id: '', qty: 0, rate: 0 }]);
    };

    const removeRow = (index) => {
        if (data.details.length === 1) return;
        setData('details', data.details.filter((_, i) => i !== index));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/stock-adj/${stock_adj.id}`);
        } else {
            post('/stock-adj');
        }
    };

    const totalQty = data.details.reduce((sum, d) => sum + (parseFloat(d.qty) || 0), 0);
    const grandTotal = data.details.reduce((sum, d) => sum + ((parseFloat(d.qty) || 0) * (parseFloat(d.rate) || 0)), 0);

    return (
        <AuthenticatedLayout header={isEdit ? `Edit Stock Adjustment #${stock_adj.v_no}` : 'New Stock Adjustment'}>
            <Head title={`${isEdit ? 'Edit' : 'New'} Stock Adjustment - Real ERP`} />

            <div className="container-fluid py-4">
                <form onSubmit={handleSubmit}>
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">
                                <i className="mdi mdi-clipboard-text-outline me-2 text-primary"></i>
                                {isEdit ? `Edit Adjustment Voucher #${stock_adj.v_no}` : 'Adjustment Voucher Information'}
                            </h5>
                            <Link href="/stock-adj" className="btn btn-outline-secondary btn-sm">
                                <i className="mdi mdi-arrow-left me-1"></i> Back to List
                            </Link>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Voucher No.</label>
                                    <input
                                        type="text"
                                        className="form-control bg-light"
                                        value={data.v_no}
                                        readOnly
                                    />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Voucher Date *</label>
                                    <input
                                        type="date"
                                        className={`form-control ${errors.v_date ? 'is-invalid' : ''}`}
                                        value={data.v_date}
                                        onChange={(e) => setData('v_date', e.target.value)}
                                        required
                                    />
                                    {errors.v_date && <div className="invalid-feedback">{errors.v_date}</div>}
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Prepared By</label>
                                    <input
                                        type="text"
                                        className="form-control bg-light"
                                        value={data.prepared_by || 'Admin'}
                                        readOnly
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">
                                <i className="mdi mdi-format-list-numbered me-2 text-success"></i>
                                Adjustment Item Entries
                            </h5>
                            <button type="button" onClick={addRow} className="btn btn-outline-primary btn-sm">
                                <i className="mdi mdi-plus me-1"></i> Add Item Row
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th style={{ width: '50px' }}>#</th>
                                            <th>Select Item *</th>
                                            <th style={{ width: '180px' }}>Adjusted Qty (+/-) *</th>
                                            <th style={{ width: '180px' }}>Unit Rate</th>
                                            <th style={{ width: '180px' }}>Subtotal</th>
                                            <th style={{ width: '60px' }} className="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {data.details.map((row, idx) => {
                                            const subtotal = (parseFloat(row.qty) || 0) * (parseFloat(row.rate) || 0);
                                            return (
                                                <tr key={idx}>
                                                    <td className="text-center text-muted fw-semibold">{idx + 1}</td>
                                                    <td>
                                                        <select
                                                            className="form-select form-select-sm"
                                                            value={row.item_id}
                                                            onChange={(e) => handleItemChange(idx, e.target.value)}
                                                            required
                                                        >
                                                            <option value="">-- Select Item --</option>
                                                            {items.map(it => (
                                                                <option key={it.id} value={it.id}>
                                                                    {it.item_code} {it.name ? `- ${it.name}` : ''}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            step="any"
                                                            className="form-control form-control-sm"
                                                            placeholder="+ Add / - Reduce"
                                                            value={row.qty}
                                                            onChange={(e) => handleFieldChange(idx, 'qty', e.target.value)}
                                                            required
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            className="form-control form-control-sm"
                                                            placeholder="0.00"
                                                            value={row.rate}
                                                            onChange={(e) => handleFieldChange(idx, 'rate', e.target.value)}
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            className="form-control form-control-sm bg-light"
                                                            value={subtotal.toFixed(2)}
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
                                            );
                                        })}
                                    </tbody>
                                    <tfoot className="bg-light fw-bold">
                                        <tr>
                                            <td colSpan="2" className="text-end">Total Adjustments:</td>
                                            <td className="text-primary">{totalQty.toFixed(2)}</td>
                                            <td className="text-end">Total Value:</td>
                                            <td className="text-success">{grandTotal.toFixed(2)}</td>
                                            <td></td>
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
                                <Link href="/stock-adj" className="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button type="submit" className="btn btn-success px-4" disabled={processing}>
                                    {processing ? 'Saving...' : (isEdit ? 'Update Adjustment' : 'Save Adjustment Voucher')}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
