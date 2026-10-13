import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function CreateEdit({
    stockIn = null,
    referenceNames = [],
    products = [],
    contractors = [],
    nextInvNo = 1,
    previousBalances = {},
    contractorByProduct = {}
}) {
    const isEdit = !!stockIn;

    const initialDetails = isEdit && stockIn.details && stockIn.details.length > 0
        ? stockIn.details.map(d => ({
            id: d.id,
            product_id: d.product_id || '',
            contractor_id: d.contractor_id || '',
            lot_no: d.lot_no || '',
            qty: d.qty || 0,
            previous: d.previous || 0,
            total: (parseFloat(d.previous) || 0) + (parseFloat(d.qty) || 0)
        }))
        : [{ product_id: '', contractor_id: '', lot_no: '', qty: 0, previous: 0, total: 0 }];

    const { data, setData, post, put, processing, errors } = useForm({
        inv_no: isEdit ? stockIn.inv_no : nextInvNo,
        date: isEdit ? stockIn.date : new Date().toISOString().split('T')[0],
        ref_name_id: isEdit ? stockIn.ref_name_id : '',
        dala_no: isEdit ? stockIn.dala_no : '',
        details: initialDetails
    });

    const handleProductChange = (index, prodId) => {
        const newDetails = [...data.details];
        const assignedContractor = contractorByProduct[prodId] || '';
        const prevBalKey = prodId + '_' + (newDetails[index].lot_no || '');
        const prevBal = previousBalances[prevBalKey] || 0;
        const qty = parseFloat(newDetails[index].qty) || 0;

        newDetails[index] = {
            ...newDetails[index],
            product_id: prodId,
            contractor_id: assignedContractor || newDetails[index].contractor_id,
            previous: prevBal,
            total: prevBal + qty
        };
        setData('details', newDetails);
    };

    const handleLotChange = (index, lot) => {
        const newDetails = [...data.details];
        const prodId = newDetails[index].product_id;
        const prevBalKey = prodId + '_' + lot;
        const prevBal = previousBalances[prevBalKey] || 0;
        const qty = parseFloat(newDetails[index].qty) || 0;

        newDetails[index] = {
            ...newDetails[index],
            lot_no: lot,
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

    const handleContractorChange = (index, cId) => {
        const newDetails = [...data.details];
        newDetails[index] = {
            ...newDetails[index],
            contractor_id: cId
        };
        setData('details', newDetails);
    };

    const addRow = () => {
        setData('details', [...data.details, {
            product_id: '',
            contractor_id: '',
            lot_no: '',
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
            put(`/stock-in/${stockIn.id}`);
        } else {
            post('/stock-in');
        }
    };

    const totalQty = data.details.reduce((sum, d) => sum + (parseFloat(d.qty) || 0), 0);

    return (
        <AuthenticatedLayout header={isEdit ? `Edit Stock In #${stockIn.inv_no}` : 'Create Stock In'}>
            <Head title={`${isEdit ? 'Edit' : 'Create'} Stock In - Real ERP`} />

            <div className="container-fluid py-4">
                <form onSubmit={handleSubmit}>
                    {/* Header Info */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">
                                <i className="mdi mdi-arrow-down-bold-box-outline me-2 text-primary"></i>
                                {isEdit ? `Edit Stock In Challan #${stockIn.inv_no}` : 'Inward Challan Header'}
                            </h5>
                            <Link href="/stock-in" className="btn btn-outline-secondary btn-sm">
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
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Loader / Reference Name</label>
                                    <select
                                        className="form-select"
                                        value={data.ref_name_id}
                                        onChange={(e) => setData('ref_name_id', e.target.value)}
                                    >
                                        <option value="">-- Select Reference Name --</option>
                                        {referenceNames.map(rn => (
                                            <option key={rn.id} value={rn.id}>{rn.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Dala No / Vehicle No</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="e.g. 102"
                                        value={data.dala_no}
                                        onChange={(e) => setData('dala_no', e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Inward Items Table */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">
                                <i className="mdi mdi-format-list-bulleted me-2 text-success"></i> Inward Product Entries
                            </h5>
                            <button type="button" onClick={addRow} className="btn btn-outline-primary btn-sm">
                                <i className="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th style={{ width: '40px' }}>#</th>
                                            <th>Product *</th>
                                            <th style={{ width: '220px' }}>Contractor</th>
                                            <th style={{ width: '130px' }}>Lot No</th>
                                            <th style={{ width: '120px' }}>Qty</th>
                                            <th style={{ width: '120px' }}>Previous</th>
                                            <th style={{ width: '120px' }}>Total</th>
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
                                                        onChange={(e) => handleProductChange(idx, e.target.value)}
                                                        required
                                                    >
                                                        <option value="">-- Select Product --</option>
                                                        {products.map(p => (
                                                            <option key={p.id} value={p.id}>{p.product_name}</option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <select
                                                        className="form-select form-select-sm"
                                                        value={row.contractor_id}
                                                        onChange={(e) => handleContractorChange(idx, e.target.value)}
                                                    >
                                                        <option value="">-- Select Contractor --</option>
                                                        {contractors.map(c => (
                                                            <option key={c.id} value={c.id}>{c.title}</option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-sm"
                                                        value={row.lot_no}
                                                        onChange={(e) => handleLotChange(idx, e.target.value)}
                                                    />
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
                                            <td colSpan="4" className="text-end">Total Received Quantity:</td>
                                            <td className="text-success fs-6">{totalQty}</td>
                                            <td colSpan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                            <button type="button" onClick={addRow} className="btn btn-sm btn-light border">
                                <i className="mdi mdi-plus me-1"></i> Add Another Item
                            </button>
                            <div className="d-flex gap-2">
                                <Link href="/stock-in" className="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button type="submit" className="btn btn-success px-4" disabled={processing}>
                                    {processing ? 'Saving...' : (isEdit ? 'Update Stock In' : 'Save Stock In Challan')}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
