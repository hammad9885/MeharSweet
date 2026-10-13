import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function CreateEdit({
    posting = null,
    products = [],
    contractors = [],
    nextVoucherNo = '',
    previousLotByProduct = {},
    contractorByProduct = {},
    contractorByMold = {},
    molds = [],
    rawItems = [],
    isEdit = false
}) {
    const initialDetails = isEdit && posting?.details?.length > 0
        ? posting.details.map(d => ({
            contractor_id: d.contractor_id || '',
            item_id: d.item_id || '',
            qty: Number(d.qty || 0),
            unit: d.unit || '',
            mold_id: d.mold_id || '',
            mold_qty: Number(d.mold_qty || 0),
            wages: Number(d.wages || 0),
            wages_amount: Number(d.wages_amount || 0),
        }))
        : [{
            contractor_id: '',
            item_id: '',
            qty: 0,
            unit: '',
            mold_id: '',
            mold_qty: 0,
            wages: 0,
            wages_amount: 0,
        }];

    const [voucherNo, setVoucherNo] = useState(
        isEdit && posting ? (posting.voucher_no || '') : nextVoucherNo
    );
    const [date, setDate] = useState(
        isEdit && posting ? (posting.date || '') : new Date().toISOString().split('T')[0]
    );
    const [preparedBy, setPreparedBy] = useState(
        isEdit && posting ? (posting.prepared_by || '') : ''
    );
    const [productId, setProductId] = useState(
        isEdit && posting ? (posting.product_id || '') : ''
    );
    const [lotNo, setLotNo] = useState(
        isEdit && posting ? (posting.lot_no || '') : ''
    );
    const [prevLotNo, setPrevLotNo] = useState(
        isEdit && posting ? (posting.prev_lot_no || '') : ''
    );
    const [prevLotAmount, setPrevLotAmount] = useState(
        isEdit && posting ? (posting.prev_lot_amount || '') : ''
    );
    const [remarks, setRemarks] = useState(
        isEdit && posting ? (posting.remarks || '') : ''
    );
    const [details, setDetails] = useState(initialDetails);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});

    // When product changes, update prev lot info
    const handleProductChange = (val) => {
        setProductId(val);
        if (previousLotByProduct && previousLotByProduct[val]) {
            setPrevLotNo(previousLotByProduct[val].lot_no || '');
            setPrevLotAmount(previousLotByProduct[val].amount || '');
        } else {
            setPrevLotNo('');
            setPrevLotAmount('');
        }
    };

    const handleDetailChange = (index, field, value) => {
        const updated = [...details];
        updated[index][field] = value;

        if (field === 'item_id') {
            const raw = rawItems.find(r => String(r.id) === String(value));
            if (raw) {
                updated[index].unit = raw.unit?.name || raw.unit || '';
            }
        }

        if (field === 'mold_id') {
            const mold = molds.find(m => String(m.id) === String(value));
            if (mold) {
                const moldWages = Number(mold.total || 0) > 0
                    ? Number(mold.total)
                    : (Number(mold.wages || 0) + Number(mold.expense || 0));
                updated[index].wages = moldWages;
                if (contractorByMold && contractorByMold[value]) {
                    updated[index].contractor_id = contractorByMold[value];
                }
            }
        }

        if (field === 'qty' || field === 'wages' || field === 'mold_id') {
            const q = Number(updated[index].qty) || 0;
            const w = Number(updated[index].wages) || 0;
            updated[index].wages_amount = q * w;
        }

        setDetails(updated);
    };

    const addRow = () => {
        setDetails([
            ...details,
            {
                contractor_id: '',
                item_id: '',
                qty: 0,
                unit: '',
                mold_id: '',
                mold_qty: 0,
                wages: 0,
                wages_amount: 0,
            }
        ]);
    };

    const removeRow = (index) => {
        if (details.length === 1) {
            alert('At least one item is required.');
            return;
        }
        setDetails(details.filter((_, i) => i !== index));
    };

    const totalQty = details.reduce((sum, d) => sum + (Number(d.qty) || 0), 0);
    const totalWages = details.reduce((sum, d) => sum + (Number(d.wages_amount) || 0), 0);

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        const payload = {
            voucher_no: voucherNo,
            date: date,
            prepared_by: preparedBy,
            product_id: productId,
            lot_no: lotNo,
            prev_lot_no: prevLotNo,
            prev_lot_amount: prevLotAmount,
            remarks: remarks,
            details: details
        };

        if (isEdit) {
            router.put(`/contractor-lot-posting/${posting.id}`, payload, {
                onFinish: () => setProcessing(false),
                onError: (err) => setErrors(err)
            });
        } else {
            router.post('/contractor-lot-posting', payload, {
                onFinish: () => setProcessing(false),
                onError: (err) => setErrors(err)
            });
        }
    };

    return (
        <AuthenticatedLayout header={isEdit ? `Edit Contractor Lot Posting #${voucherNo}` : 'Create Contractor Lot Posting'}>
            <Head title={isEdit ? `Edit Contractor Lot Posting #${voucherNo}` : 'Create Contractor Lot Posting'} />

            <div className="container-fluid py-3">
                <div className="card shadow-sm border-0">
                    <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 className="mb-0 fw-bold text-dark">
                            <i className="mdi mdi-clipboard-text-outline text-primary me-2"></i>
                            {isEdit ? `Edit Contractor Lot Posting #${voucherNo}` : 'New Contractor Lot Posting'}
                        </h5>
                        <Link href="/contractor-lot-posting" className="btn btn-sm btn-outline-secondary">
                            <i className="mdi mdi-arrow-left me-1"></i> Back to List
                        </Link>
                    </div>

                    <form onSubmit={handleSubmit} className="card-body p-4">
                        {Object.keys(errors).length > 0 && (
                            <div className="alert alert-danger mb-4">
                                <ul className="mb-0">
                                    {Object.entries(errors).map(([k, err]) => (
                                        <li key={k}>{Array.isArray(err) ? err.join(', ') : err}</li>
                                    ))}
                                </ul>
                            </div>
                        )}

                        <div className="row g-3 mb-3">
                            <div className="col-md-3">
                                <label className="form-label fw-bold">Voucher No</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={voucherNo}
                                    onChange={(e) => setVoucherNo(e.target.value)}
                                    required
                                    readOnly={!isEdit}
                                />
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Date <span className="text-danger">*</span></label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={date}
                                    onChange={(e) => setDate(e.target.value)}
                                    required
                                />
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Prepared By</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={preparedBy}
                                    onChange={(e) => setPreparedBy(e.target.value)}
                                    placeholder="Enter preparer name"
                                />
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Product <span className="text-danger">*</span></label>
                                <select
                                    className="form-select"
                                    value={productId}
                                    onChange={(e) => handleProductChange(e.target.value)}
                                    required
                                >
                                    <option value="">-- Select Product --</option>
                                    {products.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.product_name || p.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <div className="row g-3 mb-4">
                            <div className="col-md-3">
                                <label className="form-label fw-bold">Lot No</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={lotNo}
                                    onChange={(e) => setLotNo(e.target.value)}
                                    placeholder="Enter current lot #"
                                />
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Previous Lot No</label>
                                <input
                                    type="text"
                                    className="form-control bg-light"
                                    value={prevLotNo}
                                    readOnly
                                />
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Previous Lot Amount</label>
                                <input
                                    type="text"
                                    className="form-control bg-light"
                                    value={prevLotAmount}
                                    readOnly
                                />
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Remarks</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={remarks}
                                    onChange={(e) => setRemarks(e.target.value)}
                                    placeholder="Optional notes"
                                />
                            </div>
                        </div>

                        {/* Details Table */}
                        <div className="mb-4">
                            <h6 className="fw-bold mb-3 text-secondary">Lot Posting Details</h6>
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle">
                                    <thead className="table-light small">
                                        <tr>
                                            <th style={{ width: '18%' }}>Contractor</th>
                                            <th style={{ width: '18%' }}>Item Description</th>
                                            <th style={{ width: '10%' }}>Calc Qty</th>
                                            <th style={{ width: '8%' }}>Unit</th>
                                            <th style={{ width: '16%' }}>Mold</th>
                                            <th style={{ width: '8%' }}>Mold Qty</th>
                                            <th style={{ width: '10%' }}>Wages</th>
                                            <th style={{ width: '10%' }}>Wages Amount</th>
                                            <th style={{ width: '2%' }}></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {details.map((row, idx) => (
                                            <tr key={idx}>
                                                <td>
                                                    <select
                                                        className="form-select form-select-sm"
                                                        value={row.contractor_id}
                                                        onChange={(e) => handleDetailChange(idx, 'contractor_id', e.target.value)}
                                                    >
                                                        <option value="">-- Contractor --</option>
                                                        {contractors.map((c) => (
                                                            <option key={c.id} value={c.id}>
                                                                {c.title || c.name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <select
                                                        className="form-select form-select-sm"
                                                        value={row.item_id}
                                                        onChange={(e) => handleDetailChange(idx, 'item_id', e.target.value)}
                                                    >
                                                        <option value="">-- Item --</option>
                                                        {rawItems.map((r) => (
                                                            <option key={r.id} value={r.id}>
                                                                {r.item_code} - {r.product_name || r.name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control form-control-sm"
                                                        value={row.qty}
                                                        onChange={(e) => handleDetailChange(idx, 'qty', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-sm bg-light"
                                                        value={row.unit}
                                                        readOnly
                                                    />
                                                </td>
                                                <td>
                                                    <select
                                                        className="form-select form-select-sm"
                                                        value={row.mold_id}
                                                        onChange={(e) => handleDetailChange(idx, 'mold_id', e.target.value)}
                                                    >
                                                        <option value="">-- Mold --</option>
                                                        {molds.map((m) => (
                                                            <option key={m.id} value={m.id}>
                                                                {m.mold_name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control form-control-sm"
                                                        value={row.mold_qty}
                                                        onChange={(e) => handleDetailChange(idx, 'mold_qty', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control form-control-sm"
                                                        value={row.wages}
                                                        onChange={(e) => handleDetailChange(idx, 'wages', e.target.value)}
                                                    />
                                                </td>
                                                <td className="text-end fw-semibold text-primary">
                                                    {(Number(row.wages_amount) || 0).toFixed(2)}
                                                </td>
                                                <td className="text-center">
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm btn-outline-danger p-1"
                                                        onClick={() => removeRow(idx)}
                                                    >
                                                        <i className="mdi mdi-close"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot className="table-light fw-bold">
                                        <tr>
                                            <td colSpan="2">Total</td>
                                            <td>{totalQty.toFixed(2)}</td>
                                            <td colSpan="4"></td>
                                            <td className="text-end text-primary">{totalWages.toFixed(2)}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <button type="button" onClick={addRow} className="btn btn-sm btn-outline-primary mt-2">
                                <i className="mdi mdi-plus me-1"></i> Add Row
                            </button>
                        </div>

                        <div className="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <Link href="/contractor-lot-posting" className="btn btn-secondary px-4">
                                Cancel
                            </Link>
                            <button type="submit" disabled={processing} className="btn btn-success px-4">
                                {processing ? 'Saving...' : (isEdit ? 'Update Posting' : 'Save Posting')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
