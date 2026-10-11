import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function CreateEdit({
    isEdit = false,
    production = {},
    items = []
}) {
    const [vDate, setVDate] = useState(
        isEdit ? (production.v_date || '') : new Date().toISOString().split('T')[0]
    );
    const [descr, setDescr] = useState(production.descr || '');

    const [rows, setRows] = useState(() => {
        if (isEdit && production.details && production.details.length > 0) {
            return production.details.map((d, index) => ({
                id: d.id || index + 1,
                item_id: d.item_id || '',
                qty: d.qty || 1,
                rate: d.rate || 0,
                amount: (Number(d.qty) || 0) * (Number(d.rate) || 0)
            }));
        }
        return [
            { id: 1, item_id: '', qty: 1, rate: 0, amount: 0 }
        ];
    });

    const handleAddRow = () => {
        setRows(prev => [
            ...prev,
            { id: Date.now(), item_id: '', qty: 1, rate: 0, amount: 0 }
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
            const selectedItem = items.find(i => String(i.id) === String(value));
            if (selectedItem) {
                const rate = (selectedItem.cost_price && Number(selectedItem.cost_price) > 0)
                    ? Number(selectedItem.cost_price)
                    : Number(selectedItem.purchase || 0);
                updated[index].rate = rate;
            }
        }

        const qty = Number(updated[index].qty) || 0;
        const rate = Number(updated[index].rate) || 0;
        updated[index].amount = Math.round(qty * rate * 100) / 100;

        setRows(updated);
    };

    const totalAmount = rows.reduce((acc, curr) => acc + (Number(curr.amount) || 0), 0);

    const handleSubmit = (e) => {
        e.preventDefault();

        if (isEdit) {
            const payload = {
                v_date: vDate,
                descr,
                details: rows.map(r => ({
                    item_id: r.item_id,
                    qty: r.qty,
                    rate: r.rate
                }))
            };
            router.put(`/production/${production.id}`, payload);
        } else {
            const payload = {
                v_date: vDate,
                descr,
                items: rows.map(r => ({
                    item_id: r.item_id,
                    qty: r.qty,
                    rate: r.rate
                }))
            };
            router.post('/production', payload);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? 'Edit Production Voucher' : 'Add New Production Voucher'}
                        </h3>
                        <p className="text-muted small mb-0">Record manufactured finished goods quantities and batch valuation</p>
                    </div>
                    <a href="/production" className="btn btn-outline-secondary d-flex align-items-center gap-1">
                        <i className="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>

                <form onSubmit={handleSubmit}>
                    {/* Header Details */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white py-3">
                            <h5 className="card-title mb-0 fw-bold">Voucher Header</h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-4">
                                    <label className="form-label small fw-semibold text-muted">Production Date <span className="text-danger">*</span></label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={vDate}
                                        onChange={(e) => setVDate(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="col-md-8">
                                    <label className="form-label small fw-semibold text-muted">Description / Remarks</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Batch remarks or manufacturing details"
                                        value={descr}
                                        onChange={(e) => setDescr(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Finished Goods Table */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-bold">Finished Goods Output</h5>
                            <button
                                type="button"
                                onClick={handleAddRow}
                                className="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                            >
                                <i className="mdi mdi-plus"></i> Add Item
                            </button>
                        </div>
                        <div className="table-responsive">
                            <table className="table table-bordered align-middle mb-0">
                                <thead className="table-light">
                                    <tr>
                                        <th style={{ width: '50px' }}>#</th>
                                        <th style={{ minWidth: '300px' }}>Finished Product</th>
                                        <th style={{ minWidth: '150px' }} className="text-end">Quantity</th>
                                        <th style={{ minWidth: '150px' }} className="text-end">Cost / Rate</th>
                                        <th style={{ minWidth: '180px' }} className="text-end">Amount</th>
                                        <th style={{ width: '60px' }}></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((row, index) => (
                                        <tr key={row.id}>
                                            <td className="text-center text-muted small">{index + 1}</td>
                                            <td>
                                                <select
                                                    className="form-select"
                                                    value={row.item_id}
                                                    onChange={(e) => handleRowChange(index, 'item_id', e.target.value)}
                                                    required
                                                >
                                                    <option value="">Select Finished Item</option>
                                                    {items.map(item => (
                                                        <option key={item.id} value={item.id}>
                                                            {item.item_code} - {item.item_name || ''}
                                                        </option>
                                                    ))}
                                                </select>
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control text-end"
                                                    value={row.qty}
                                                    onChange={(e) => handleRowChange(index, 'qty', e.target.value)}
                                                    required
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    className="form-control text-end"
                                                    value={row.rate}
                                                    onChange={(e) => handleRowChange(index, 'rate', e.target.value)}
                                                    required
                                                />
                                            </td>
                                            <td className="text-end fw-bold text-success font-14">
                                                Rs. {Number(row.amount || 0).toLocaleString()}
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
                                <tfoot className="table-light fw-bold font-15">
                                    <tr>
                                        <td colSpan="4" className="text-end">Total Valuation:</td>
                                        <td className="text-end text-success">
                                            Rs. {totalAmount.toLocaleString()}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="d-flex justify-content-end gap-2">
                        <a href="/production" className="btn btn-light border px-4">Cancel</a>
                        <button type="submit" className="btn btn-primary px-4 fw-semibold">
                            {isEdit ? 'Update Production' : 'Save Production'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
