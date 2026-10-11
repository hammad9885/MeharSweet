import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';

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
        <div className="position-relative" style={{ minHeight: '100vh', backgroundColor: '#fafbfe' }}>
            <Head title="Sign In" />

            {/* Background SVG circles */}
            <div className="position-absolute start-0 end-0 top-0 bottom-0 w-100 h-100" style={{ pointerEvents: 'none', overflow: 'hidden' }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 800 800">
                    <g fillOpacity="0.12">
                        <circle style={{ fill: 'rgba(114, 124, 245, 0.1)' }} cx="400" cy="400" r="600" />
                        <circle style={{ fill: 'rgba(114, 124, 245, 0.15)' }} cx="400" cy="400" r="450" />
                        <circle style={{ fill: 'rgba(114, 124, 245, 0.2)' }} cx="400" cy="400" r="300" />
                        <circle style={{ fill: 'rgba(114, 124, 245, 0.25)' }} cx="400" cy="400" r="150" />
                    </g>
                </svg>
            </div>

            <div className="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xxl-4 col-lg-5">
                            <div className="card shadow-sm border-0" style={{ borderRadius: '8px' }}>
                                <div className="card-body p-4">
                                    <div className="text-center w-75 m-auto mb-4">
                                        <h4 className="text-dark-50 text-center pb-0 fw-bold">Sign In</h4>
                                        <p className="text-muted mb-4 small">Enter your email address and password to access panel.</p>
                                    </div>

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
                                                className="btn btn-primary"
                                                type="submit"
                                                disabled={processing}
                                            >
                                                {processing ? (
                                                    <span>
                                                        <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                        Signing In...
                                                    </span>
                                                ) : (
                                                    'Log In'
                                                )}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <footer className="footer footer-alt text-center mt-3 text-muted small">
                                2018 - {new Date().getFullYear()} © Real ERP
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
