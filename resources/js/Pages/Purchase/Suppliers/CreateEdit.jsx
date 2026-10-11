import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useForm, Link } from '@inertiajs/react';

export default function CreateEdit({ supplier = null, next_id = null }) {
    const isEdit = !!supplier;

    // Detect legacy Urdu title stored in mobile column
    const initialUrdu = supplier?.urdu_title || (supplier?.mobile && /[\u0600-\u06FF]/.test(supplier.mobile) ? supplier.mobile : '');
    const initialMobile = supplier?.mobile && /[\u0600-\u06FF]/.test(supplier.mobile) ? '' : (supplier?.mobile || '');

    const { data, setData, post, put, processing, errors } = useForm({
        seq_number: isEdit ? (supplier?.seq_number || supplier?.id || '') : (next_id || ''),
        name: supplier?.name || '',
        urdu_title: initialUrdu,
        mobile: initialMobile,
        city: supplier?.city || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/suppliers/${supplier.id}`);
        } else {
            post('/suppliers');
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? 'Edit Supplier' : 'Add Supplier'}
                        </h3>
                        <p className="text-muted small mb-0">
                            {isEdit ? 'Update supplier details' : 'Create a new supplier'}
                        </p>
                    </div>
                    <Link href="/suppliers" className="btn btn-outline-secondary d-flex align-items-center gap-1">
                        <i className="mdi mdi-arrow-left"></i>
                        <span>Back to Suppliers</span>
                    </Link>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-4">
                        <form onSubmit={handleSubmit}>
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
                                    <small className="text-muted">System assigned ID</small>
                                </div>

                                {/* 2. Name */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">
                                        Name <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        placeholder="Enter supplier name"
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

                                {/* 4. Number */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Number</label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.mobile ? 'is-invalid' : ''}`}
                                        placeholder="Enter phone/mobile number"
                                        value={data.mobile}
                                        onChange={(e) => setData('mobile', e.target.value)}
                                    />
                                    {errors.mobile && <div className="invalid-feedback">{errors.mobile}</div>}
                                </div>

                                {/* 5. City */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">City</label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.city ? 'is-invalid' : ''}`}
                                        placeholder="Enter city"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                    />
                                    {errors.city && <div className="invalid-feedback">{errors.city}</div>}
                                </div>
                            </div>

                            <div className="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <Link href="/suppliers" className="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    className="btn btn-primary px-4 d-flex align-items-center gap-1"
                                    disabled={processing}
                                >
                                    <i className="mdi mdi-check"></i>
                                    <span>{isEdit ? 'Update Supplier' : 'Save Supplier'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
