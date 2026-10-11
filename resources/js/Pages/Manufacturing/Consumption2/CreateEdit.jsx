import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function CreateEdit({
    isEdit = false,
    consumption = {},
    items = [],
    raw = [],
    expenseAccounts = [],
    molds = [],
    contractors = [],
    productMaster = [],
    latestRates = {}
}) {
    const [date, setDate] = useState(
        isEdit ? (consumption.date || '') : new Date().toISOString().split('T')[0]
    );
    const [lotNo, setLotNo] = useState(consumption.lot_no || '');
    const [descr, setDescr] = useState(consumption.descr || '');
    const [product, setProduct] = useState(consumption.item_id || '');
    const [productQty, setProductQty] = useState(consumption.qty || 1);
    const [costPrice, setCostPrice] = useState(consumption.cost_price || 0);
    const [lotQty, setLotQty] = useState(consumption.lot_qty || '');
    const [qtyPerCarton, setQtyPerCarton] = useState(consumption.qty_per_carton || '');
    const [cartonsInLot, setCartonsInLot] = useState(consumption.cartons_in_lot || '');
    const [targetQty, setTargetQty] = useState(consumption.target_qty || '');

    useEffect(() => {
        const lQty = Number(lotQty) || 0;
        const qpc = Number(qtyPerCarton) || 0;
        if (lQty > 0 && qpc > 0) {
            setCartonsInLot(Math.round((lQty / qpc) * 100) / 100);
        }
    }, [lotQty, qtyPerCarton]);

    const [rows, setRows] = useState(() => {
        if (isEdit && consumption.details && consumption.details.length > 0) {
            return consumption.details.map((d, index) => ({
                id: d.id || index + 1,
                item_id: d.item_id || '',
                mold_id: d.mold_id || '',
                mold_qty: d.mold_qty || '',
                grams: d.grams || '',
                calc_qty: d.calc_qty || '',
                unit: d.unit || '',
                contractor_account_id: d.contractor_account_id || '',
                wages_rate: d.wages_rate || '',
                wages_amount: d.wages_amount || '',
                material_rate: d.material_rate || '',
                mat_amount: d.mat_amount || '',
                m_caps: d.m_caps || '',
                stroke: d.stroke || '',
                hatt: d.hatt || ''
            }));
        }
        return [
            {
                id: 1,
                item_id: '',
                mold_id: '',
                mold_qty: '',
                grams: '',
                calc_qty: '',
                unit: '',
                contractor_account_id: '',
                wages_rate: '',
                wages_amount: '',
                material_rate: '',
                mat_amount: '',
                m_caps: '',
                stroke: '',
                hatt: ''
            }
        ];
    });

    const handleAddRow = () => {
        setRows(prev => [
            ...prev,
            {
                id: Date.now(),
                item_id: '',
                mold_id: '',
                mold_qty: '',
                grams: '',
                calc_qty: '',
                unit: '',
                contractor_account_id: '',
                wages_rate: '',
                wages_amount: '',
                material_rate: '',
                mat_amount: '',
                m_caps: '',
                stroke: '',
                hatt: ''
            }
        ]);
    };

    const handleRemoveRow = (index) => {
        if (rows.length === 1) return;
        setRows(prev => prev.filter((_, i) => i !== index));
    };

    const handleRowChange = (index, field, value) => {
        const updated = [...rows];
        updated[index][field] = value;

        if (field === 'item_id') {
            const rawItem = raw.find(r => String(r.id) === String(value));
            if (rawItem) {
                updated[index].unit = rawItem.itemunits?.name || rawItem.unit || '';
                const rate = latestRates[rawItem.id] || rawItem.purchase || rawItem.cost_price || 0;
                updated[index].material_rate = rate;
                if (rawItem.mold_id) {
                    updated[index].mold_id = rawItem.mold_id;
                }
            }
        }

        if (field === 'mold_id') {
            const foundMold = molds.find(m => String(m.id) === String(value));
            if (foundMold) {
                const wages = (Number(foundMold.total) > 0)
                    ? Number(foundMold.total)
                    : (Number(foundMold.wages || 0) + Number(foundMold.expense || 0));
                updated[index].wages_rate = wages;
            }
        }

        const calcQ = Number(updated[index].calc_qty) || 0;
        const matR = Number(updated[index].material_rate) || 0;
        const wagesR = Number(updated[index].wages_rate) || 0;
        const moldQ = Number(updated[index].mold_qty) || 0;

        if (field === 'calc_qty' || field === 'material_rate') {
            updated[index].mat_amount = Math.round(calcQ * matR * 100) / 100;
        }

        if (field === 'mold_qty' || field === 'wages_rate') {
            updated[index].wages_amount = Math.round(moldQ * wagesR * 100) / 100;
        }

        setRows(updated);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const payload = {
            date,
            lot_no: lotNo,
            descr,
            product,
            product_qty: productQty,
            cost_price: costPrice,
            lot_qty: lotQty,
            qty_per_carton: qtyPerCarton,
            cartons_in_lot: cartonsInLot,
            target_qty: targetQty,
            details: rows
        };

        if (isEdit) {
            router.put(`/consumption2/${consumption.id}`, payload);
        } else {
            router.post('/consumption2', payload);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? 'Edit Consumption 2' : 'Add New Consumption 2'}
                        </h3>
                        <p className="text-muted small mb-0">Manage secondary consumption specifications and batch allocations</p>
                    </div>
                    <a href="/consumption2" className="btn btn-outline-secondary d-flex align-items-center gap-1">
                        <i className="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>

                <form onSubmit={handleSubmit}>
                    {/* Basic Info */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white py-3">
                            <h5 className="card-title mb-0 fw-bold">Master Information</h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3 mb-3">
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Date <span className="text-danger">*</span></label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={date}
                                        onChange={(e) => setDate(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Lot No</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Lot identifier"
                                        value={lotNo}
                                        onChange={(e) => setLotNo(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label small fw-semibold text-muted">Description</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Consumption details & production notes"
                                        value={descr}
                                        onChange={(e) => setDescr(e.target.value)}
                                    />
                                </div>
                            </div>

                            <div className="row g-3 mb-3">
                                <div className="col-md-6">
                                    <label className="form-label small fw-semibold text-muted">Product (Finished Good) <span className="text-danger">*</span></label>
                                    <select
                                        className="form-select"
                                        value={product}
                                        onChange={(e) => setProduct(e.target.value)}
                                        required
                                    >
                                        <option value="">Select Finished Product</option>
                                        {productMaster.map(item => (
                                            <option key={item.id} value={item.id}>
                                                {item.product_name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Product Qty <span className="text-danger">*</span></label>
                                    <input
                                        type="number"
                                        step="any"
                                        className="form-control"
                                        value={productQty}
                                        onChange={(e) => setProductQty(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Cost Price</label>
                                    <input
                                        type="number"
                                        step="any"
                                        className="form-control"
                                        value={costPrice}
                                        onChange={(e) => setCostPrice(e.target.value)}
                                    />
                                </div>
                            </div>

                            <div className="row g-3">
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Lot Qty</label>
                                    <input
                                        type="number"
                                        step="any"
                                        className="form-control"
                                        value={lotQty}
                                        onChange={(e) => setLotQty(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Qty Per Carton</label>
                                    <input
                                        type="number"
                                        step="any"
                                        className="form-control"
                                        value={qtyPerCarton}
                                        onChange={(e) => setQtyPerCarton(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Cartons In Lot</label>
                                    <input
                                        type="number"
                                        step="any"
                                        className="form-control bg-light"
                                        value={cartonsInLot}
                                        readOnly
                                    />
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label small fw-semibold text-muted">Target Qty</label>
                                    <input
                                        type="number"
                                        step="any"
                                        className="form-control"
                                        value={targetQty}
                                        onChange={(e) => setTargetQty(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Details */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-bold">Consumption Details</h5>
                            <button
                                type="button"
                                onClick={handleAddRow}
                                className="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                            >
                                <i className="mdi mdi-plus"></i> Add Row
                            </button>
                        </div>
                        <div className="table-responsive">
                            <table className="table table-bordered table-sm align-middle mb-0">
                                <thead className="table-light">
                                    <tr>
                                        <th style={{ width: '40px' }}>#</th>
                                        <th style={{ minWidth: '200px' }}>Raw Material</th>
                                        <th style={{ minWidth: '160px' }}>Mold</th>
                                        <th style={{ minWidth: '100px' }}>Mold Qty</th>
                                        <th style={{ minWidth: '90px' }}>Calc Qty</th>
                                        <th style={{ minWidth: '80px' }}>Unit</th>
                                        <th style={{ minWidth: '180px' }}>Contractor</th>
                                        <th style={{ minWidth: '100px' }}>Wages Rate</th>
                                        <th style={{ minWidth: '100px' }}>Wages Amt</th>
                                        <th style={{ minWidth: '100px' }}>Mat Rate</th>
                                        <th style={{ minWidth: '100px' }}>Mat Amt</th>
                                        <th style={{ minWidth: '80px' }}>M.Caps</th>
                                        <th style={{ minWidth: '80px' }}>Stroke</th>
                                        <th style={{ minWidth: '80px' }}>Hatt</th>
                                        <th style={{ width: '50px' }}></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((row, index) => (
                                        <tr key={row.id}>
                                            <td className="text-center text-muted small">{index + 1}</td>
                                            <td>
                                                <select
                                                    className="form-select form-select-sm"
                                                    value={row.item_id}
                                                    onChange={(e) => handleRowChange(index, 'item_id', e.target.value)}
                                                    required
                                                >
                                                    <option value="">Select Raw Material</option>
                                                    {raw.map(r => (
                                                        <option key={r.id} value={r.id}>
                                                            {r.item_code} - {r.item_name || ''}
                                                        </option>
                                                    ))}
                                                </select>
                                            </td>
                                            <td>
                                                <select
                                                    className="form-select form-select-sm"
                                                    value={row.mold_id}
                                                    onChange={(e) => handleRowChange(index, 'mold_id', e.target.value)}
                                                >
                                                    <option value="">Select Mold</option>
                                                    {molds.map(m => (
                                                        <option key={m.id} value={m.id}>
                                                            {m.mold_name || m.name}
                                                        </option>
                                                    ))}
                                                </select>
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control form-select-sm"
                                                    value={row.mold_qty}
                                                    onChange={(e) => handleRowChange(index, 'mold_qty', e.target.value)}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control form-select-sm"
                                                    value={row.calc_qty}
                                                    onChange={(e) => handleRowChange(index, 'calc_qty', e.target.value)}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    className="form-control form-select-sm bg-light"
                                                    value={row.unit}
                                                    readOnly
                                                />
                                            </td>
                                            <td>
                                                <select
                                                    className="form-select form-select-sm"
                                                    value={row.contractor_account_id}
                                                    onChange={(e) => handleRowChange(index, 'contractor_account_id', e.target.value)}
                                                >
                                                    <option value="">Select Contractor</option>
                                                    {contractors.map(c => (
                                                        <option key={c.id} value={c.id}>
                                                            {c.title || c.account_name}
                                                        </option>
                                                    ))}
                                                </select>
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control form-select-sm"
                                                    value={row.wages_rate}
                                                    onChange={(e) => handleRowChange(index, 'wages_rate', e.target.value)}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control form-select-sm bg-light"
                                                    value={row.wages_amount}
                                                    readOnly
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control form-select-sm"
                                                    value={row.material_rate}
                                                    onChange={(e) => handleRowChange(index, 'material_rate', e.target.value)}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control form-select-sm bg-light"
                                                    value={row.mat_amount}
                                                    readOnly
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    className="form-control form-select-sm"
                                                    value={row.m_caps}
                                                    onChange={(e) => handleRowChange(index, 'm_caps', e.target.value)}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    className="form-control form-select-sm"
                                                    value={row.stroke}
                                                    onChange={(e) => handleRowChange(index, 'stroke', e.target.value)}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    className="form-control form-select-sm"
                                                    value={row.hatt}
                                                    onChange={(e) => handleRowChange(index, 'hatt', e.target.value)}
                                                />
                                            </td>
                                            <td className="text-center">
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemoveRow(index)}
                                                    className="btn btn-sm btn-outline-danger p-1"
                                                    title="Remove Row"
                                                    disabled={rows.length === 1}
                                                >
                                                    <i className="mdi mdi-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div className="d-flex justify-content-end gap-2">
                        <a href="/consumption2" className="btn btn-light border px-4">Cancel</a>
                        <button type="submit" className="btn btn-primary px-4 fw-semibold">
                            {isEdit ? 'Update Consumption 2' : 'Save Consumption 2'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
