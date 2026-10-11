import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function CreateEdit({
    isEdit = false,
    order = {},
    customers = [],
    products = [],
    transportCompanies = [],
    referenceNames = []
}) {
    const [vDate, setVDate] = useState(
        isEdit ? (order.v_date || '') : new Date().toISOString().split('T')[0]
    );
    const [customerId, setCustomerId] = useState(order.customer_id || '');
    const [partyRef, setPartyRef] = useState(order.party_ref || '');
    const [refNameId, setRefNameId] = useState(order.ref_name_id || '');
    const [transportCompanyId, setTransportCompanyId] = useState(order.transport_company_id || '');
    const [remarks, setRemarks] = useState(order.remarks || '');

    const [rows, setRows] = useState(() => {
        if (isEdit && order.details && order.details.length > 0) {
            return order.details.map(d => ({
                id: d.id || Math.random(),
                item_id: d.item_id || '',
                qty: d.qty || 1,
                rate: d.rate || 0,
                remarks: d.remarks || ''
            }));
        }
        return [
            { id: 1, item_id: '', qty: 1, rate: 0, remarks: '' }
        ];
    });

    const handleRowChange = (index, field, value) => {
        const updated = [...rows];
        updated[index][field] = value;
        setRows(updated);
    };

    const addRow = () => {
        setRows([...rows, { id: Date.now(), item_id: '', qty: 1, rate: 0, remarks: '' }]);
    };

    const removeRow = (index) => {
        if (rows.length > 1) {
            setRows(rows.filter((_, i) => i !== index));
        }
    };

    const totalQty = rows.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0);

    const handleSubmit = (e) => {
        e.preventDefault();
        const payload = {
            v_date: vDate,
            customer_id: customerId,
            party_ref: partyRef,
            ref_name_id: refNameId,
            transport_company_id: transportCompanyId,
            remarks: remarks,
            items: rows
        };

        if (isEdit) {
            router.put(`/sale-order/${order.id}`, payload);
        } else {
            router.post('/sale-order', payload);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? `Edit Sale Order #${order.v_no}` : 'Create Sale Order'}
                        </h3>
                        <p className="text-muted small mb-0">Record and manage advance customer bookings and production requirements</p>
                    </div>
                    <a href="/sale-order" className="btn btn-light border">
                        <i className="mdi mdi-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3">
                            <h5 className="card-title mb-0 fw-semibold">Order Information</h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold small text-muted">Order Date *</label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={vDate}
                                        onChange={(e) => setVDate(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold small text-muted">Customer *</label>
                                    <select
                                        className="form-select"
                                        value={customerId}
                                        onChange={(e) => setCustomerId(e.target.value)}
                                        required
                                    >
                                        <option value="">-- Select Customer --</option>
                                        {customers.map((c) => (
                                            <option key={c.acc_id || c.id} value={c.acc_id || c.id}>
                                                {c.name} {c.urdu_title ? `(${c.urdu_title})` : ''}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold small text-muted">Reference Name</label>
                                    <select
                                        className="form-select"
                                        value={refNameId}
                                        onChange={(e) => setRefNameId(e.target.value)}
                                    >
                                        <option value="">-- Select Reference --</option>
                                        {referenceNames.map((rn) => (
                                            <option key={rn.id} value={rn.id}>
                                                {rn.name} {rn.name_urdu ? `(${rn.name_urdu})` : ''}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label fw-semibold small text-muted">Party Ref</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Party Reference #"
                                        value={partyRef}
                                        onChange={(e) => setPartyRef(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold small text-muted">Transport Company</label>
                                    <select
                                        className="form-select"
                                        value={transportCompanyId}
                                        onChange={(e) => setTransportCompanyId(e.target.value)}
                                    >
                                        <option value="">-- Select Transport --</option>
                                        {transportCompanies.map((tc) => (
                                            <option key={tc.id} value={tc.id}>{tc.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-8">
                                    <label className="form-label fw-semibold small text-muted">Remarks</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Order notes or delivery conditions"
                                        value={remarks}
                                        onChange={(e) => setRemarks(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">Order Items</h5>
                            <button type="button" onClick={addRow} className="btn btn-sm btn-outline-primary">
                                <i className="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="bg-light small text-muted text-uppercase">
                                        <tr>
                                            <th style={{ width: '50%' }}>Product / Item</th>
                                            <th style={{ width: '20%' }}>Quantity</th>
                                            <th style={{ width: '20%' }}>Remarks</th>
                                            <th style={{ width: '50px' }}></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.map((row, index) => (
                                            <tr key={row.id}>
                                                <td>
                                                    <select
                                                        className="form-select"
                                                        value={row.item_id}
                                                        onChange={(e) => handleRowChange(index, 'item_id', e.target.value)}
                                                        required
                                                    >
                                                        <option value="">-- Select Product --</option>
                                                        {products.map((p) => (
                                                            <option key={p.id} value={p.id}>
                                                                {p.product_name || p.name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control text-center"
                                                        value={row.qty}
                                                        onChange={(e) => handleRowChange(index, 'qty', e.target.value)}
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        placeholder="Item note"
                                                        value={row.remarks}
                                                        onChange={(e) => handleRowChange(index, 'remarks', e.target.value)}
                                                    />
                                                </td>
                                                <td className="text-center">
                                                    {rows.length > 1 && (
                                                        <button
                                                            type="button"
                                                            onClick={() => removeRow(index)}
                                                            className="btn btn-sm btn-link text-danger p-0"
                                                        >
                                                            <i className="mdi mdi-close-circle font-18"></i>
                                                        </button>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr className="bg-light">
                                            <th className="text-end">Total Quantity:</th>
                                            <th className="text-center">{totalQty}</th>
                                            <th colSpan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div className="d-flex justify-content-end gap-2">
                        <a href="/sale-order" className="btn btn-light border px-4">Cancel</a>
                        <button type="submit" className="btn btn-primary px-5 fw-semibold">
                            <i className="mdi mdi-content-save me-1"></i>
                            {isEdit ? 'Update Sale Order' : 'Save Sale Order'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
