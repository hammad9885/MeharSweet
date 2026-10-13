import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function CreateEdit({
    purchaseMaster = null,
    invoiceItems = [],
    items = [],
    suppliers = [],
    maxVoucherNo = null,
    isEdit = false
}) {
    const initialItems = isEdit && invoiceItems.length > 0
        ? invoiceItems.map(item => ({
            item_id: item.item_id || '',
            qty: Number(item.qty || 1),
            rate: Number(item.rate || 0),
            tax_rate: Number(item.tax_rate || 0),
            discount_perc: Number(item.discount_perc || 0),
            discount: Number(item.discount || 0),
        }))
        : [{
            item_id: '',
            qty: 1,
            rate: 0,
            tax_rate: 0,
            discount_perc: 0,
            discount: 0,
        }];

    const [formItems, setFormItems] = useState(initialItems);
    const [supplierId, setSupplierId] = useState(
        isEdit && purchaseMaster ? (purchaseMaster.supplier_id || '') : ''
    );
    const [vDate, setVDate] = useState(
        isEdit && purchaseMaster ? (purchaseMaster.v_date || '') : new Date().toISOString().split('T')[0]
    );
    const [gatepassNo, setGatepassNo] = useState(
        isEdit && purchaseMaster ? (purchaseMaster.gate_pass_no || purchaseMaster.gatepass_no || '') : ''
    );
    const [grandDiscount, setGrandDiscount] = useState(
        isEdit && purchaseMaster ? Number(purchaseMaster.grand_discount || 0) : 0
    );
    const [file, setFile] = useState(null);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});

    const calculateRow = (row) => {
        const qty = Number(row.qty) || 0;
        const rate = Number(row.rate) || 0;
        const exclusive = qty * rate;
        const taxRate = Number(row.tax_rate) || 0;
        const taxAmt = (exclusive * taxRate) / 100;
        const inclusive = exclusive + taxAmt;
        const discPerc = Number(row.discount_perc) || 0;
        const discAmt = (inclusive * discPerc) / 100;
        const total = inclusive - discAmt;
        return { exclusive, taxAmt, inclusive, discAmt, total };
    };

    const handleItemChange = (index, field, value) => {
        const updated = [...formItems];
        updated[index][field] = value;

        if (field === 'item_id') {
            const selected = items.find(i => String(i.id) === String(value));
            if (selected) {
                updated[index].rate = Number(selected.purchase || selected.cost_price || 0);
                updated[index].tax_rate = Number(selected.sale_in || 0);
            }
        }

        setFormItems(updated);
    };

    const addRow = () => {
        setFormItems([
            ...formItems,
            { item_id: '', qty: 1, rate: 0, tax_rate: 0, discount_perc: 0, discount: 0 }
        ]);
    };

    const removeRow = (index) => {
        if (formItems.length === 1) {
            alert('At least one item is required.');
            return;
        }
        setFormItems(formItems.filter((_, i) => i !== index));
    };

    const subtotal = formItems.reduce((acc, row) => acc + calculateRow(row).total, 0);
    const grandTotal = Math.max(0, subtotal - (Number(grandDiscount) || 0));

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        const formData = new FormData();
        formData.append('supplier_id', supplierId);
        formData.append('v_date', vDate);
        formData.append('gatepass_no', gatepassNo);
        formData.append('grand_discount', grandDiscount);
        formData.append('grand_total', grandTotal);

        if (file) {
            formData.append('file', file);
        }

        formItems.forEach((row, idx) => {
            const calc = calculateRow(row);
            formData.append(`items[${idx}][item_id]`, row.item_id);
            formData.append(`items[${idx}][qty]`, row.qty);
            formData.append(`items[${idx}][rate]`, row.rate);
            formData.append(`items[${idx}][tax_rate]`, row.tax_rate);
            formData.append(`items[${idx}][discount_perc]`, row.discount_perc);
            formData.append(`items[${idx}][discount]`, calc.discAmt);
        });

        if (isEdit) {
            formData.append('_method', 'PUT');
            router.post(`/purchase_return_invoices/${purchaseMaster.v_no}`, formData, {
                onFinish: () => setProcessing(false),
                onError: (err) => setErrors(err)
            });
        } else {
            router.post('/purchase_return_invoices', formData, {
                onFinish: () => setProcessing(false),
                onError: (err) => setErrors(err)
            });
        }
    };

    return (
        <AuthenticatedLayout header={isEdit ? `Edit Purchase Return #${purchaseMaster?.v_no}` : 'Create Purchase Return'}>
            <Head title={isEdit ? `Edit Purchase Return #${purchaseMaster?.v_no}` : 'Create Purchase Return'} />

            <div className="container-fluid py-3">
                <div className="card shadow-sm border-0">
                    <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 className="mb-0 fw-bold text-dark">
                            <i className="mdi mdi-keyboard-return text-warning me-2"></i>
                            {isEdit ? `Edit Purchase Return #${purchaseMaster?.v_no}` : 'New Purchase Return Invoice'}
                        </h5>
                        <Link href="/purchase_return_invoices" className="btn btn-sm btn-outline-secondary">
                            <i className="mdi mdi-arrow-left me-1"></i> Back to List
                        </Link>
                    </div>

                    <form onSubmit={handleSubmit} className="card-body p-4">
                        {Object.keys(errors).length > 0 && (
                            <div className="alert alert-danger mb-4">
                                <ul className="mb-0">
                                    {Object.entries(errors).map(([key, err]) => (
                                        <li key={key}>{Array.isArray(err) ? err.join(', ') : err}</li>
                                    ))}
                                </ul>
                            </div>
                        )}

                        <div className="row g-3 mb-4">
                            <div className="col-md-5">
                                <label className="form-label fw-bold">Supplier <span className="text-danger">*</span></label>
                                <select
                                    className="form-select"
                                    value={supplierId}
                                    onChange={(e) => setSupplierId(e.target.value)}
                                    required
                                >
                                    <option value="">-- Select Supplier --</option>
                                    {suppliers.map((s) => (
                                        <option key={s.acc_id || s.id} value={s.acc_id || s.id}>
                                            {s.name} {s.urdu_title ? `(${s.urdu_title})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="col-md-3">
                                <label className="form-label fw-bold">Date <span className="text-danger">*</span></label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={vDate}
                                    onChange={(e) => setVDate(e.target.value)}
                                    required
                                />
                            </div>

                            <div className="col-md-4">
                                <label className="form-label fw-bold">Gate Pass #</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={gatepassNo}
                                    onChange={(e) => setGatepassNo(e.target.value)}
                                    placeholder="Optional gatepass reference"
                                />
                            </div>
                        </div>

                        {/* Items Section */}
                        <div className="mb-4">
                            <h6 className="fw-bold mb-3 text-secondary">Return Items</h6>
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle">
                                    <thead className="table-light small">
                                        <tr>
                                            <th style={{ width: '28%' }}>Item</th>
                                            <th style={{ width: '10%' }}>Qty</th>
                                            <th style={{ width: '11%' }}>Rate</th>
                                            <th style={{ width: '11%' }}>Exclusive</th>
                                            <th style={{ width: '9%' }}>Tax %</th>
                                            <th style={{ width: '10%' }}>Tax Amt</th>
                                            <th style={{ width: '8%' }}>Disc %</th>
                                            <th style={{ width: '11%' }}>Total</th>
                                            <th style={{ width: '4%' }}></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {formItems.map((row, idx) => {
                                            const calc = calculateRow(row);
                                            return (
                                                <tr key={idx}>
                                                    <td>
                                                        <select
                                                            className="form-select form-select-sm"
                                                            value={row.item_id}
                                                            onChange={(e) => handleItemChange(idx, 'item_id', e.target.value)}
                                                            required
                                                        >
                                                            <option value="">-- Select Item --</option>
                                                            {items.map((it) => (
                                                                <option key={it.id} value={it.id}>
                                                                    {it.item_code} - {it.product_name || it.name}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            min="0.01"
                                                            step="any"
                                                            className="form-control form-select-sm"
                                                            value={row.qty}
                                                            onChange={(e) => handleItemChange(idx, 'qty', e.target.value)}
                                                            required
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="any"
                                                            className="form-control form-select-sm"
                                                            value={row.rate}
                                                            onChange={(e) => handleItemChange(idx, 'rate', e.target.value)}
                                                            required
                                                        />
                                                    </td>
                                                    <td className="text-end fw-semibold">
                                                        {calc.exclusive.toFixed(2)}
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="any"
                                                            className="form-control form-select-sm"
                                                            value={row.tax_rate}
                                                            onChange={(e) => handleItemChange(idx, 'tax_rate', e.target.value)}
                                                        />
                                                    </td>
                                                    <td className="text-end text-muted">
                                                        {calc.taxAmt.toFixed(2)}
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="any"
                                                            className="form-control form-select-sm"
                                                            value={row.discount_perc}
                                                            onChange={(e) => handleItemChange(idx, 'discount_perc', e.target.value)}
                                                        />
                                                    </td>
                                                    <td className="text-end fw-bold text-success">
                                                        {calc.total.toFixed(2)}
                                                    </td>
                                                    <td className="text-center">
                                                        <button
                                                            type="button"
                                                            className="btn btn-sm btn-outline-danger p-1"
                                                            onClick={() => removeRow(idx)}
                                                            title="Remove Row"
                                                        >
                                                            <i className="mdi mdi-close"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" onClick={addRow} className="btn btn-sm btn-outline-primary mt-2">
                                <i className="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>

                        {/* Summary & Totals */}
                        <div className="row g-3 justify-content-between align-items-start pt-3 border-top">
                            <div className="col-md-5">
                                <label className="form-label fw-bold">Attach Document / Bill Photo</label>
                                <input
                                    type="file"
                                    className="form-control"
                                    onChange={(e) => setFile(e.target.files[0] || null)}
                                />
                                {isEdit && purchaseMaster?.file && (
                                    <div className="small text-muted mt-2">
                                        Current file: <a href={`/${purchaseMaster.file}`} target="_blank" rel="noreferrer">View Attachment</a>
                                    </div>
                                )}
                            </div>

                            <div className="col-md-5">
                                <div className="card bg-light border-0 p-3">
                                    <div className="d-flex justify-content-between mb-2">
                                        <span className="text-muted">Subtotal:</span>
                                        <span className="fw-semibold">Rs. {subtotal.toFixed(2)}</span>
                                    </div>
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <span className="text-muted">Grand Discount:</span>
                                        <div style={{ width: '130px' }}>
                                            <input
                                                type="number"
                                                min="0"
                                                step="any"
                                                className="form-control form-control-sm text-end"
                                                value={grandDiscount}
                                                onChange={(e) => setGrandDiscount(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <hr className="my-2" />
                                    <div className="d-flex justify-content-between fs-5 fw-bold text-primary">
                                        <span>Grand Total:</span>
                                        <span>Rs. {grandTotal.toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <Link href="/purchase_return_invoices" className="btn btn-secondary px-4">
                                Cancel
                            </Link>
                            <button type="submit" disabled={processing} className="btn btn-success px-4">
                                {processing ? 'Saving...' : (isEdit ? 'Update Invoice' : 'Save Invoice')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
