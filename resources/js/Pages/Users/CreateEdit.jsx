import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function CreateEdit({ user = null }) {
    const isEdit = !!user;

    const { data, setData, post, put, processing, errors } = useForm({
        name: user?.name || '',
        email: user?.email || '',
        mobile: user?.mobile || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/users/${user.id}`);
        } else {
            post('/users');
        }
    };

    return (
        <AuthenticatedLayout header={isEdit ? 'Edit User' : 'Add New User'}>
            <Head title={`${isEdit ? 'Edit' : 'Add'} User - Real ERP`} />

            <div className="container-fluid py-4">
                <div className="row justify-content-center">
                    <div className="col-lg-7 col-md-9">
                        <div className="card shadow-sm border-0">
                            <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h5 className="card-title mb-0 fw-semibold">
                                    <i className={`mdi ${isEdit ? 'mdi-account-edit' : 'mdi-account-plus'} me-2 text-primary`}></i>
                                    {isEdit ? 'Edit User Details' : 'Create New User'}
                                </h5>
                                <Link href="/users" className="btn btn-outline-secondary btn-sm">
                                    <i className="mdi mdi-arrow-left me-1"></i> Back to Users
                                </Link>
                            </div>

                            <form onSubmit={handleSubmit}>
                                <div className="card-body p-4">
                                    <div className="mb-3">
                                        <label className="form-label fw-semibold">
                                            Full Name <span className="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                            placeholder="Enter user full name"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                        />
                                        {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label fw-semibold">
                                            Email Address <span className="text-danger">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                            placeholder="user@example.com"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            required
                                        />
                                        {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label fw-semibold">Mobile Number</label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.mobile ? 'is-invalid' : ''}`}
                                            placeholder="03001234567"
                                            value={data.mobile}
                                            onChange={(e) => setData('mobile', e.target.value)}
                                        />
                                        {errors.mobile && <div className="invalid-feedback">{errors.mobile}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label fw-semibold">
                                            Password {isEdit ? <span className="text-muted fw-normal">(Leave blank to keep unchanged)</span> : <span className="text-danger">*</span>}
                                        </label>
                                        <input
                                            type="password"
                                            className={`form-control ${errors.password ? 'is-invalid' : ''}`}
                                            placeholder={isEdit ? '••••••••' : 'Minimum 6 characters'}
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            required={!isEdit}
                                        />
                                        {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                                    </div>

                                    {!isEdit && (
                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">
                                                Confirm Password <span className="text-danger">*</span>
                                            </label>
                                            <input
                                                type="password"
                                                className={`form-control ${errors.password_confirmation ? 'is-invalid' : ''}`}
                                                placeholder="Repeat password"
                                                value={data.password_confirmation}
                                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                                required
                                            />
                                            {errors.password_confirmation && (
                                                <div className="invalid-feedback">{errors.password_confirmation}</div>
                                            )}
                                        </div>
                                    )}
                                </div>

                                <div className="card-footer bg-light border-top py-3 d-flex justify-content-end gap-2">
                                    <Link href="/users" className="btn btn-secondary">
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        className="btn btn-primary px-4"
                                        disabled={processing}
                                    >
                                        {processing ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                Saving...
                                            </>
                                        ) : (
                                            <>
                                                <i className="mdi mdi-check me-1"></i>
                                                {isEdit ? 'Update User' : 'Create User'}
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
