import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useForm, Link } from '@inertiajs/react';

export default function CreateEdit({
    customer = null,
    categories = [],
    transportCompanies = [],
    next_id = null
}) {
    const isEdit = !!customer;

    const { data, setData, post, put, processing, errors } = useForm({
        seq_number: isEdit ? (customer?.seq_number || customer?.id || '') : (next_id || ''),
        name: customer?.name || '',
        urdu_title: customer?.urdu_title || '',
        customer_category_id: customer?.customer_category_id || '',
        company_title: customer?.company_title || '',
        mobile: customer?.mobile || '',
        phone: customer?.phone || '',
        city: customer?.city || '',
        address: customer?.address || '',
        cnic: customer?.cnic || '',
        ntn: customer?.ntn || '',
        email: customer?.email || '',
        cr_limit: customer?.cr_limit || '',
        cr_days: customer?.cr_days || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/customers/${customer.id}`);
        } else {
            post('/customers');
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">
                            {isEdit ? 'Edit Customer' : 'Add Customer'}
                        </h3>
                        <p className="text-muted small mb-0">
                            {isEdit ? 'Update customer details' : 'Register a new customer account'}
                        </p>
                    </div>
                    <Link href="/customers" className="btn btn-outline-secondary d-flex align-items-center gap-1">
                        <i className="mdi mdi-arrow-left"></i>
                        <span>Back to Customers</span>
                    </Link>
                </div>

                <div className="card border-0 shadow-sm">
                    <div className="card-body p-4">
                        <form onSubmit={handleSubmit}>
                            <div className="row g-3">
                                {/* Auto-generate ID */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">Auto-generate ID</label>
                                    <input
                                        type="text"
                                        className="form-control bg-light"
                                        value={data.seq_number}
                                        readOnly
                                    />
                                    <small className="text-muted">System assigned identifier</small>
                                </div>

                                {/* Customer Name */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        Customer Name <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        placeholder="e.g. Al-Madina Traders"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                    />
                                    {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                </div>

                                {/* Urdu Title */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">اردو نام (Urdu Name)</label>
                                    <input
                                        type="text"
                                        className="form-control text-end font-arabic"
                                        placeholder="المدینہ ٹریڈرز"
                                        value={data.urdu_title}
                                        onChange={(e) => setData('urdu_title', e.target.value)}
                                        dir="rtl"
                                    />
                                </div>

                                {/* Customer Category */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Customer Category</label>
                                    <select
                                        className="form-select"
                                        value={data.customer_category_id}
                                        onChange={(e) => setData('customer_category_id', e.target.value)}
                                    >
                                        <option value="">-- Select Category --</option>
                                        {categories.map((cat) => (
                                            <option key={cat.id} value={cat.id}>
                                                {cat.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Transport Company */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">
                                        Transport Company <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className={`form-select ${errors.company_title ? 'is-invalid' : ''}`}
                                        value={data.company_title}
                                        onChange={(e) => setData('company_title', e.target.value)}
                                        required
                                    >
                                        <option value="">-- Select Transport Company --</option>
                                        {transportCompanies.map((tc) => (
                                            <option key={tc.id} value={tc.name}>
                                                {tc.name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.company_title && <div className="invalid-feedback">{errors.company_title}</div>}
                                </div>

                                {/* Mobile */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        Mobile <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.mobile ? 'is-invalid' : ''}`}
                                        placeholder="0300-1234567"
                                        value={data.mobile}
                                        onChange={(e) => setData('mobile', e.target.value)}
                                        required
                                    />
                                    {errors.mobile && <div className="invalid-feedback">{errors.mobile}</div>}
                                </div>

                                {/* Phone */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">Phone / Landline</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="042-12345678"
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                    />
                                </div>

                                {/* City */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        City <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.city ? 'is-invalid' : ''}`}
                                        placeholder="e.g. Lahore, Karachi"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        required
                                    />
                                    {errors.city && <div className="invalid-feedback">{errors.city}</div>}
                                </div>

                                {/* Address */}
                                <div className="col-md-12">
                                    <label className="form-label fw-semibold">
                                        Address <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        rows="2"
                                        className={`form-control ${errors.address ? 'is-invalid' : ''}`}
                                        placeholder="Street address, shop number, area..."
                                        value={data.address}
                                        onChange={(e) => setData('address', e.target.value)}
                                        required
                                    ></textarea>
                                    {errors.address && <div className="invalid-feedback">{errors.address}</div>}
                                </div>

                                {/* CNIC */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        CNIC <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.cnic ? 'is-invalid' : ''}`}
                                        placeholder="35201-1234567-1"
                                        value={data.cnic}
                                        onChange={(e) => setData('cnic', e.target.value)}
                                        required
                                    />
                                    {errors.cnic && <div className="invalid-feedback">{errors.cnic}</div>}
                                </div>

                                {/* NTN */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        NTN <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.ntn ? 'is-invalid' : ''}`}
                                        placeholder="1234567-8"
                                        value={data.ntn}
                                        onChange={(e) => setData('ntn', e.target.value)}
                                        required
                                    />
                                    {errors.ntn && <div className="invalid-feedback">{errors.ntn}</div>}
                                </div>

                                {/* Email */}
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">
                                        Email <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                        placeholder="customer@domain.com"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />
                                    {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                </div>

                                {/* Credit Limit */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Credit Limit (Rs.)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        className="form-control"
                                        placeholder="0.00"
                                        value={data.cr_limit}
                                        onChange={(e) => setData('cr_limit', e.target.value)}
                                    />
                                </div>

                                {/* Credit Days */}
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Credit Days</label>
                                    <input
                                        type="number"
                                        className="form-control"
                                        placeholder="e.g. 30"
                                        value={data.cr_days}
                                        onChange={(e) => setData('cr_days', e.target.value)}
                                    />
                                </div>

                                {/* Submit buttons */}
                                <div className="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <Link href="/customers" className="btn btn-light px-4">
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        className="btn btn-primary px-4 fw-semibold"
                                        disabled={processing}
                                    >
                                        {processing ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                Saving...
                                            </>
                                        ) : (
                                            <>
                                                <i className="mdi mdi-content-save me-1"></i>
                                                {isEdit ? 'Update Customer' : 'Save Customer'}
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
