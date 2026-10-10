import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        post('/login', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout title="Sign In">
            <Head title="Log in - GST ERP" />

            {status && (
                <div className="alert alert-success alert-dismissible fade show" role="alert">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div className="mb-3">
                    <label htmlFor="email" className="form-label">Email Address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                        autoComplete="username"
                        autoFocus
                        required
                        placeholder="Enter your email"
                        onChange={(e) => setData('email', e.target.value)}
                    />
                    {errors.email && (
                        <div className="invalid-feedback d-block">
                            <strong>{errors.email}</strong>
                        </div>
                    )}
                </div>

                <div className="mb-3">
                    <label htmlFor="password" className="form-label">Password</label>
                    <div className="input-group input-group-merge">
                        <input
                            id="password"
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            value={data.password}
                            className={`form-control ${errors.password ? 'is-invalid' : ''}`}
                            autoComplete="current-password"
                            required
                            placeholder="Enter your password"
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        <div
                            className="input-group-text cursor-pointer"
                            style={{ cursor: 'pointer' }}
                            onClick={() => setShowPassword(!showPassword)}
                        >
                            <i className={`mdi ${showPassword ? 'mdi-eye-off' : 'mdi-eye'}`}></i>
                        </div>
                    </div>
                    {errors.password && (
                        <div className="invalid-feedback d-block">
                            <strong>{errors.password}</strong>
                        </div>
                    )}
                </div>

                <div className="mb-3">
                    <div className="form-check">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            checked={data.remember}
                            className="form-check-input"
                            onChange={(e) => setData('remember', e.target.checked)}
                        />
                        <label className="form-check-label" htmlFor="remember">
                            Remember Me
                        </label>
                    </div>
                </div>

                <div className="d-grid mb-0 text-center">
                    <button
                        className="btn btn-primary btn-lg"
                        type="submit"
                        disabled={processing}
                    >
                        {processing ? (
                            <span>
                                <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Signing In...
                            </span>
                        ) : (
                            'Sign In'
                        )}
                    </button>
                </div>
            </form>
        </GuestLayout>
    );
}
