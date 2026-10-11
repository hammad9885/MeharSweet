import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { router } from '@inertiajs/react';

export default function Index({
    orders = [],
    totalOrders = 0,
    customers = [],
    products = [],
    dcTotals = {},
    dcRefs = {},
    startDate = '',
    endDate = '',
    totalItemQty = 0,
    selectedItemName = '',
    filters = {}
}) {
    const [voucherNo, setVoucherNo] = useState(filters.voucher_no || '');
    const [customerId, setCustomerId] = useState(filters.customer_id || '');
    const [itemId, setItemId] = useState(filters.item_id || '');
    const [sDate, setSDate] = useState(filters.startDate || startDate || '');
    const [eDate, setEDate] = useState(filters.endDate || endDate || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/sale-order', {
            voucher_no: voucherNo,
            customer_id: customerId,
            item_id: itemId,
            startDate: sDate,
            endDate: eDate
        }, { preserveState: true });
    };

    const handleReset = () => {
        setVoucherNo('');
        setCustomerId('');
        setItemId('');
        setSDate('');
        setEDate('');
        router.get('/sale-order');
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this Sale Order?')) {
            router.delete(`/sale-order/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div className="d-flex align-items-center gap-2">
                            <h3 className="fw-bold text-dark mb-1">Sale Orders</h3>
                            <span className="badge bg-primary rounded-pill px-3 py-1 font-14">
                                {totalOrders || orders.length} Orders
                            </span>
                        </div>
                        <p className="text-muted small mb-0">Manage customer booking, pending items, and delivery status</p>
                    </div>
                    <a href="/sale-order/create" className="btn btn-primary d-flex align-items-center gap-2">
                        <i className="mdi mdi-plus-circle font-16"></i>
                        <span>Create Sale Order</span>
                    </a>
                </div>

                {/* Filter Card */}
                <div className="card border-0 shadow-sm mb-4">
                    <div className="card-body">
                        <form onSubmit={handleFilter} className="row g-3">
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">Voucher No</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search by Voucher #"
                                    value={voucherNo}
                                    onChange={(e) => setVoucherNo(e.target.value)}
                                />
                            </div>
                            <div className="col-md-3">
                                <label className="form-label small fw-semibold text-muted">Customer</label>
                                <select
                                    className="form-select"
                                    value={customerId}
                                    onChange={(e) => setCustomerId(e.target.value)}
                                >
                                    <option value="">All Customers</option>
                                    {customers.map((c) => (
                                        <option key={c.acc_id || c.id} value={c.acc_id || c.id}>
                                            {c.name} {c.urdu_title ? `(${c.urdu_title})` : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Item</label>
                                <select
                                    className="form-select"
                                    value={itemId}
                                    onChange={(e) => setItemId(e.target.value)}
                                >
                                    <option value="">All Items</option>
                                    {products.map((p) => (
                                        <option key={p.id} value={p.id}>
                                            {p.product_name || p.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">Start Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={sDate}
                                    onChange={(e) => setSDate(e.target.value)}
                                />
                            </div>
                            <div className="col-md-2">
                                <label className="form-label small fw-semibold text-muted">End Date</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={eDate}
                                    onChange={(e) => setEDate(e.target.value)}
                                />
                            </div>
                            <div className="col-12 d-flex gap-2 justify-content-end">
                                <button type="submit" className="btn btn-success px-4">
                                    <i className="mdi mdi-magnify me-1"></i> Search
                                </button>
                                <button type="button" onClick={handleReset} className="btn btn-light border px-4">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {selectedItemName && totalItemQty > 0 && (
                    <div className="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
                        <i className="mdi mdi-information font-20"></i>
                        <span>
                            Total <strong>{selectedItemName}</strong> quantity across filtered orders: <strong>{totalItemQty}</strong>
                        </span>
                    </div>
                )}

                {/* Table Card */}
                <div className="card border-0 shadow-sm">
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th className="ps-4">Voucher No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Party Ref</th>
                                        <th>Items & Quantities</th>
                                        <th>Delivery Challans</th>
                                        <th className="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {orders.length > 0 ? (
                                        orders.map((order) => {
                                            const dcList = dcRefs[order.v_no] || [];
                                            return (
                                                <tr key={order.id || order.v_no}>
                                                    <td className="ps-4 fw-bold text-primary">#{order.v_no}</td>
                                                    <td className="small text-muted">{order.v_date || '-'}</td>
                                                    <td>
                                                        <div className="fw-semibold text-dark">{order.customer?.name || 'N/A'}</div>
                                                        {order.customer?.urdu_title && (
                                                            <div className="small text-muted" dir="rtl">{order.customer.urdu_title}</div>
                                                        )}
                                                    </td>
                                                    <td className="fw-medium">
                                                        {order.reference_name?.name || order.party_ref || '-'}
                                                    </td>
                                                    <td>
                                                        <div className="d-flex flex-column gap-1">
                                                            {(order.details || []).map((detail, idx) => (
                                                                <div key={idx} className="small d-flex justify-content-between border-bottom pb-1">
                                                                    <span className="fw-semibold text-dark">
                                                                        {detail.item?.product_name || detail.item?.name || 'Item'}
                                                                    </span>
                                                                    <span className="badge bg-light text-dark border">
                                                                        Rem: {Number(detail.qty) || 0}
                                                                    </span>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {Array.isArray(dcList) && dcList.length > 0 ? (
                                                            dcList.map((dc, i) => (
                                                                <span key={i} className="badge bg-info-lighten text-info me-1">
                                                                    {dc}
                                                                </span>
                                                            ))
                                                        ) : (
                                                            <span className="text-muted small">-</span>
                                                        )}
                                                    </td>
                                                    <td className="text-center pe-4">
                                                        <div className="d-flex justify-content-center gap-1">
                                                            <a
                                                                href={`/sale-order/${order.id}/edit`}
                                                                className="btn btn-sm btn-outline-warning"
                                                                title="Edit Sale Order"
                                                            >
                                                                <i className="mdi mdi-pencil"></i>
                                                            </a>
                                                            <a
                                                                href={`/sale-order/${order.id}/print`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="btn btn-sm btn-outline-info"
                                                                title="Print"
                                                            >
                                                                <i className="mdi mdi-printer"></i>
                                                            </a>
                                                            <a
                                                                href={`/sale-order-dc/create?customer_id=${order.customer_id}&sor_vno=${order.v_no}`}
                                                                className="btn btn-sm btn-outline-success"
                                                                title="Create Delivery Challan"
                                                            >
                                                                <i className="mdi mdi-truck-delivery"></i> DC
                                                            </a>
                                                            <button
                                                                onClick={() => handleDelete(order.id)}
                                                                className="btn btn-sm btn-outline-danger"
                                                                title="Delete"
                                                            >
                                                                <i className="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="7" className="text-center py-5 text-muted">
                                                <i className="mdi mdi-cart-remove font-36 d-block mb-2 text-muted"></i>
                                                No sale orders found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
