import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useForm, Link } from '@inertiajs/react';

export default function CreateEdit({ item = null, nextSeq = '', itemtypes = [], itemUnits = [] }) {
    const isEdit = !!item;

    const initialRate = item
        ? (item.sale_rate || item.purchase || item.cost_price || '')
        : '';

    const { data, setData, post, processing, errors } = useForm({
        seq_number: isEdit ? (item.seq_number || item.id || '') : (nextSeq || ''),
        name: isEdit ? (item.item_code || '') : '',
        urdu_title: item?.urdu_title || '',
        type_id: item?.type_id || '',
        item_type: item?.item_type || '',
        unit_id: item?.unit_id || '',
        rate: initialRate,
    });

    const handleTypeChange = (e) => {
        const typeId = e.target.value;
        const selectedType = itemtypes.find(t => String(t.id) === String(typeId));
        setData(prev => ({
            ...prev,
            type_id: typeId,
            item_type: selectedType ? selectedType.item_type : '',
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            post(`/inventory/itemmaster/${item.id}`);
        } else {
            post('/inventory/itemmaster');
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? 'Edit Item' : 'Add Item'}
                        </h3>
                        <p className="text-muted small mb-0">
                            {isEdit ? 'Update Item Master details' : 'Register a new item in Item Master'}
                        </p>
                    </div>
                    <Link
                        href="/inventory/itemmaster"
                        className="btn btn-outline-secondary d-flex align-items-center gap-1"
                    >
                        <i className="mdi mdi-arrow-left"></i>
                        <span>Back to Items</span>
                    </Link>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-4">
                        <form onSubmit={handleSubmit}>
                            <input type="hidden" name="item_type" value={data.item_type} />

                            <div className="row g-3">
                                {/* 1. Auto-generate ID */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Auto-generate ID</label>
                                    <input
                                        type="text"
                                        className="form-control bg-light"
                                        value={data.seq_number}
                                        readOnly
                                    />
                                    <small className="text-muted">System assigned code</small>
                                </div>

                                {/* 2. Name */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">
                                        Name <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        placeholder="Enter item name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                        autoFocus
                                    />
                                    {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                </div>

                                {/* 3. Urdu Name */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Urdu Name</label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.urdu_title ? 'is-invalid' : ''}`}
                                        placeholder="اردو نام درج کریں"
                                        dir="rtl"
                                        value={data.urdu_title}
                                        onChange={(e) => setData('urdu_title', e.target.value)}
                                    />
                                    {errors.urdu_title && <div className="invalid-feedback">{errors.urdu_title}</div>}
                                </div>

                                {/* 4. Type */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">
                                        Type <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className={`form-select ${errors.type_id ? 'is-invalid' : ''}`}
                                        value={data.type_id}
                                        onChange={handleTypeChange}
                                        required
                                    >
                                        <option value="">Select Type</option>
                                        {itemtypes.map((type) => (
                                            <option key={type.id} value={type.id}>
                                                {type.type_title}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.type_id && <div className="invalid-feedback">{errors.type_id}</div>}
                                </div>

                                {/* 5. Unit */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">
                                        Unit <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className={`form-select ${errors.unit_id ? 'is-invalid' : ''}`}
                                        value={data.unit_id}
                                        onChange={(e) => setData('unit_id', e.target.value)}
                                        required
                                    >
                                        <option value="">Select Unit</option>
                                        {itemUnits.map((u) => (
                                            <option key={u.id} value={u.id}>
                                                {u.name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.unit_id && <div className="invalid-feedback">{errors.unit_id}</div>}
                                </div>

                                {/* 6. Rate */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Rate</label>
                                    <input
                                        type="number"
                                        step="any"
                                        min="0"
                                        className={`form-control ${errors.rate ? 'is-invalid' : ''}`}
                                        placeholder="Enter rate"
                                        value={data.rate}
                                        onChange={(e) => setData('rate', e.target.value)}
                                    />
                                    {errors.rate && <div className="invalid-feedback">{errors.rate}</div>}
                                </div>
                            </div>

                            <div className="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <Link href="/inventory/itemmaster" className="btn btn-secondary px-4">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    className="btn btn-primary px-4 d-flex align-items-center gap-1"
                                    disabled={processing}
                                >
                                    <i className="mdi mdi-check"></i>
                                    <span>{isEdit ? 'Update Item' : 'Save Item'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
