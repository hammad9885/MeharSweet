import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router, usePage } from '@inertiajs/react';

export default function CreateEdit({
    isEdit = false,
    purchaseMaster = {},
    invoiceItems = [],
    items = [],
    suppliers = [],
    transportCompanies = [],
    referenceNames = [],
    preselectedSupplier = '',
    preselectedDc = '',
    preselectedDcDate = '',
    preselectedDcRefNameId = '',
    preselectedDcFreight = '',
    preselectedDcBiltyNo = '',
    preselectedDcTransportCompanyId = '',
    custTransportMap = {},
    custBiltyMap = {}
}) {
    const { auth } = usePage().props;

    const [vDate, setVDate] = useState(
        isEdit ? (purchaseMaster.v_date || '') : (preselectedDcDate || new Date().toISOString().split('T')[0])
    );
    const [supplierId, setSupplierId] = useState(
        isEdit ? (purchaseMaster.supplier_id || '') : (preselectedSupplier || '')
    );
    const [gatepassNo, setGatepassNo] = useState(purchaseMaster.gatepass_no || '');
    const [dcVno, setDcVno] = useState(purchaseMaster.dc_vno || preselectedDc || '');
    const [refNameId, setRefNameId] = useState(
        purchaseMaster.ref_name_id || preselectedDcRefNameId || ''
    );
    const [biltyNo, setBiltyNo] = useState(
        purchaseMaster.bilty_no || preselectedDcBiltyNo || ''
    );
    const [transportCompanyId, setTransportCompanyId] = useState(
        purchaseMaster.transport_company_id || preselectedDcTransportCompanyId || ''
    );
    const [freight, setFreight] = useState(purchaseMaster.freight || preselectedDcFreight || 0);
    const [discountPercentage, setDiscountPercentage] = useState(purchaseMaster.discount_percentage || 0);
    const [grandDiscount, setGrandDiscount] = useState(purchaseMaster.discount || 0);

    // Initial items rows
    const [rows, setRows] = useState(() => {
        if (isEdit && invoiceItems && invoiceItems.length > 0) {
            return invoiceItems.map(item => ({
                id: item.id || Math.random(),
                item_id: item.item_id || '',
                qty: item.qty || 1,
                cartons: item.cartons || 0,
                rate: item.rate || 0,
                discount: item.discount || 0,
                amount: (Number(item.qty) || 0) * (Number(item.rate) || 0) - (Number(item.discount) || 0)
            }));
        }
        return [
            { id: 1, item_id: '', qty: 1, cartons: 0, rate: 0, discount: 0, amount: 0 }
        ];
    });

    const handleCustomerChange = (e) => {
        const selectedId = e.target.value;
        setSupplierId(selectedId);
        if (custTransportMap[selectedId]) {
            setTransportCompanyId(custTransportMap[selectedId]);
        }
        if (custBiltyMap[selectedId]) {
            setBiltyNo(custBiltyMap[selectedId]);
        }
    };

    const handleRowChange = (index, field, value) => {
        const updated = [...rows];
        updated[index][field] = value;

        if (field === 'item_id') {
            const foundItem = items.find(i => String(i.id) === String(value));
            if (foundItem) {
                updated[index].rate = foundItem.selling_price || foundItem.rate || 0;
            }
        }

        const qty = Number(updated[index].qty) || 0;
        const rate = Number(updated[index].rate) || 0;
        const disc = Number(updated[index].discount) || 0;
        updated[index].amount = (qty * rate) - disc;

        setRows(updated);
    };

    const addRow = () => {
        setRows([...rows, { id: Date.now(), item_id: '', qty: 1, cartons: 0, rate: 0, discount: 0, amount: 0 }]);
    };

    const removeRow = (index) => {
        if (rows.length > 1) {
            setRows(rows.filter((_, i) => i !== index));
        }
    };

    const subTotal = rows.reduce((acc, curr) => acc + (Number(curr.amount) || 0), 0);
    const netTotal = subTotal - (Number(grandDiscount) || 0) + (Number(freight) || 0);

    const handleSubmit = (e) => {
        e.preventDefault();

        const payload = {
            v_date: vDate,
            supplier_id: supplierId,
            gatepass_no: gatepassNo,
            dc_vno: dcVno,
            ref_name_id: refNameId,
            bilty_no: biltyNo,
            transport_company_id: transportCompanyId,
            freight: freight,
            discount: grandDiscount,
            discount_percentage: discountPercentage,
            items: rows
        };

        if (isEdit) {
            router.put(`/sale_invoices/${purchaseMaster.v_no}`, payload);
        } else {
            router.post('/sale_invoices', payload);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? `Edit Sale Invoice #${purchaseMaster.v_no}` : 'Create Sale Invoice'}
                        </h3>
                        <p className="text-muted small mb-0">Record invoice details, products, discount, and transport</p>
                    </div>
                    <a href="/sale_invoices" className="btn btn-light border">
                        <i className="mdi mdi-arrow-left me-1"></i> Back to Invoices
                    </a>
                </div>

                <form onSubmit={handleSubmit}>
                    {/* Header Details Card */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3">
                            <h5 className="card-title mb-0 fw-semibold">Invoice Information</h5>
                        </div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold small text-muted">Invoice Date *</label>
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
                                        value={supplierId}
                                        onChange={handleCustomerChange}
                                        required
                                    >
                                        <option value="">-- Select Customer --</option>
                                        {suppliers.map((s) => (
                                            <option key={s.acc_id || s.id} value={s.acc_id || s.id}>
                                                {s.name} {s.urdu_title ? `(${s.urdu_title})` : ''}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-2">
                                    <label className="form-label fw-semibold small text-muted">Gate Pass #</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Optional"
                                        value={gatepassNo}
                                        onChange={(e) => setGatepassNo(e.target.value)}
                                    />
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
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold small text-muted">Bilty No</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Bilty Number"
                                        value={biltyNo}
                                        onChange={(e) => setBiltyNo(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Invoice Items Card */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 className="card-title mb-0 fw-semibold">Invoice Items</h5>
                            <button type="button" onClick={addRow} className="btn btn-sm btn-outline-primary">
                                <i className="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div className="card-body p-0">
                            <div className="table-responsive">
                                <table className="table table-bordered align-middle mb-0">
                                    <thead className="bg-light small text-muted text-uppercase">
                                        <tr>
                                            <th style={{ width: '40%' }}>Item / Product</th>
                                            <th style={{ width: '12%' }}>Cartons</th>
                                            <th style={{ width: '12%' }}>Quantity</th>
                                            <th style={{ width: '15%' }}>Rate</th>
                                            <th style={{ width: '12%' }}>Discount</th>
                                            <th style={{ width: '15%' }}>Amount</th>
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
                                                        {items.map((it) => (
                                                            <option key={it.id} value={it.id}>
                                                                {it.product_name || it.name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        className="form-control"
                                                        value={row.cartons}
                                                        onChange={(e) => handleRowChange(index, 'cartons', e.target.value)}
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control"
                                                        value={row.qty}
                                                        onChange={(e) => handleRowChange(index, 'qty', e.target.value)}
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control"
                                                        value={row.rate}
                                                        onChange={(e) => handleRowChange(index, 'rate', e.target.value)}
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        className="form-control"
                                                        value={row.discount}
                                                        onChange={(e) => handleRowChange(index, 'discount', e.target.value)}
                                                    />
                                                </td>
                                                <td className="fw-semibold text-end">
                                                    {(Number(row.amount) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
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
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Summary Card */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-body">
                            <div className="row justify-content-end">
                                <div className="col-md-5">
                                    <div className="d-flex justify-content-between mb-2">
                                        <span className="text-muted fw-semibold">Sub Total:</span>
                                        <span className="fw-bold">{subTotal.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                                    </div>
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <span className="text-muted fw-semibold">Discount:</span>
                                        <div style={{ width: '150px' }}>
                                            <input
                                                type="number"
                                                step="any"
                                                className="form-control text-end"
                                                value={grandDiscount}
                                                onChange={(e) => setGrandDiscount(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <span className="text-muted fw-semibold">Freight:</span>
                                        <div style={{ width: '150px' }}>
                                            <input
                                                type="number"
                                                step="any"
                                                className="form-control text-end"
                                                value={freight}
                                                onChange={(e) => setFreight(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <hr />
                                    <div className="d-flex justify-content-between align-items-center mb-3">
                                        <span className="fs-5 fw-bold text-dark">Net Amount:</span>
                                        <span className="fs-5 fw-bold text-success">
                                            {netTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                        </span>
                                    </div>
                                    <button type="submit" className="btn btn-primary w-100 py-2 fw-semibold">
                                        <i className="mdi mdi-content-save me-1"></i>
                                        {isEdit ? 'Update Invoice' : 'Save Invoice'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
