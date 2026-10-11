import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function CreateEdit({
    isEdit = false,
    dc = {},
    transportCompanies = [],
    customers = [],
    referenceNames = [],
    preselectCustomerId = '',
    preselectSorVno = '',
    sorVno = '',
    sorDetails = []
}) {
    const [vDate, setVDate] = useState(
        isEdit ? (dc.v_date || '') : new Date().toISOString().split('T')[0]
    );
    const [customerId, setCustomerId] = useState(
        isEdit ? (dc.customer_id || '') : (preselectCustomerId || '')
    );
    const [saleOrderVno, setSaleOrderVno] = useState(
        isEdit ? (sorVno || '') : (preselectSorVno || '')
    );
    const [refNameId, setRefNameId] = useState(dc.ref_name_id || '');
    const [biltyNo, setBiltyNo] = useState(dc.bilty_no || '');
    const [transportCompanyId, setTransportCompanyId] = useState(dc.transport_company_id || '');
    const [remarks, setRemarks] = useState(dc.remarks || '');

    const [rows, setRows] = useState(() => {
        if (isEdit && dc.details && dc.details.length > 0) {
            return dc.details.map(d => ({
                id: d.id || Math.random(),
                item_id: d.item_id || '',
                cartons: d.cartons || 0,
                qty_per_carton: d.qty_per_carton || 0,
                qty: d.qty || 1,
                lot_no: d.lot_no || ''
            }));
        }
        return [
            { id: 1, item_id: '', cartons: 0, qty_per_carton: 0, qty: 1, lot_no: '' }
        ];
    });

    const handleRowChange = (index, field, value) => {
        const updated = [...rows];
        updated[index][field] = value;
        if (field === 'cartons' || field === 'qty_per_carton') {
            const c = Number(updated[index].cartons) || 0;
            const q = Number(updated[index].qty_per_carton) || 0;
            if (c > 0 && q > 0) {
                updated[index].qty = c * q;
            }
        }
        setRows(updated);
    };

    const addRow = () => {
        setRows([...rows, { id: Date.now(), item_id: '', cartons: 0, qty_per_carton: 0, qty: 1, lot_no: '' }]);
    };

    const removeRow = (index) => {
        if (rows.length > 1) {
            setRows(rows.filter((_, i) => i !== index));
        }
    };

    const totalQty = rows.reduce((acc, curr) => acc + (Number(curr.qty) || 0), 0);
    const totalCartons = rows.reduce((acc, curr) => acc + (Number(curr.cartons) || 0), 0);

    const handleSubmit = (e) => {
        e.preventDefault();
        const payload = {
            v_date: vDate,
            customer_id: customerId,
            sale_order_vno: saleOrderVno,
            ref_name_id: refNameId,
            bilty_no: biltyNo,
            transport_company_id: transportCompanyId,
            remarks: remarks,
            items: rows
        };

        if (isEdit) {
            router.put(`/sale-order-dc/${dc.id}`, payload);
        } else {
            router.post('/sale-order-dc', payload);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? `Edit Delivery Challan #${dc.v_no}` : 'Create Delivery Challan'}
                        </h3>
                        <p className="text-muted small mb-0">Record dispatched cartons, quantity per carton, lot numbers, and transport</p>
                    </div>
                    <a href="/sale-order-dc" className="btn btn-light border">
                        <i className="mdi mdi-arrow-left me-1"></i> Back to Challans
                    </a>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3">
                            <h5 className="card-title mb-0 fw-semibold">Challan Header</h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold small text-muted">Challan Date *</label>
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
                                    <label className="form-label fw-semibold small text-muted">Sale Order #</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="SO Voucher #"
                                        value={saleOrderVno}
                                        onChange={(e) => setSaleOrderVno(e.target.value)}
                                    />
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label fw-semibold small text-muted">Bilty No</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Bilty #"
                                        value={biltyNo}
                                        onChange={(e) => setBiltyNo(e.target.value)}
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
                                <div className="col-md-4">
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
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold small text-muted">Remarks</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Optional notes"
                                        value={remarks}
                                        onChange={(e) => setRemarks(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">Dispatched Items</h5>
                            <button type="button" onClick={addRow} className="btn btn-sm btn-outline-primary">
                                <i className="mdi mdi-plus me-1"></i> Add Row
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="bg-light small text-muted text-uppercase">
                                        <tr>
                                            <th style={{ width: '35%' }}>Item ID / Name</th>
                                            <th style={{ width: '15%' }}>Cartons</th>
                                            <th style={{ width: '15%' }}>Qty / Carton</th>
                                            <th style={{ width: '15%' }}>Total Qty</th>
                                            <th style={{ width: '15%' }}>Lot No</th>
                                            <th style={{ width: '50px' }}></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.map((row, index) => (
                                            <tr key={row.id}>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        placeholder="Item ID / Title"
                                                        value={row.item_id}
                                                        onChange={(e) => handleRowChange(index, 'item_id', e.target.value)}
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        className="form-control text-center"
                                                        value={row.cartons}
                                                        onChange={(e) => handleRowChange(index, 'cartons', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control text-center"
                                                        value={row.qty_per_carton}
                                                        onChange={(e) => handleRowChange(index, 'qty_per_carton', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control text-center fw-bold"
                                                        value={row.qty}
                                                        onChange={(e) => handleRowChange(index, 'qty', e.target.value)}
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        placeholder="Lot #"
                                                        value={row.lot_no}
                                                        onChange={(e) => handleRowChange(index, 'lot_no', e.target.value)}
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
                                            <th className="text-end">Totals:</th>
                                            <th className="text-center">{totalCartons} Cartons</th>
                                            <th></th>
                                            <th className="text-center">{totalQty} Total Qty</th>
                                            <th colSpan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div className="d-flex justify-content-end gap-2">
                        <a href="/sale-order-dc" className="btn btn-light border px-4">Cancel</a>
                        <button type="submit" className="btn btn-primary px-5 fw-semibold">
                            <i className="mdi mdi-content-save me-1"></i>
                            {isEdit ? 'Update Challan' : 'Save Delivery Challan'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
