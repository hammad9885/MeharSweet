import React from 'react';
import { Link } from '@inertiajs/react';

export default function GuestLayout({ children, title = 'Sign In' }) {
    return (
        <div className="authentication-bg position-relative" style={{ minHeight: '100vh' }}>
            <div className="position-absolute start-0 end-0 top-0 bottom-0 w-100 h-100" style={{ pointerEvents: 'none' }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 800 800">
                    <g fillOpacity="0.22">
                        <circle style={{ fill: 'rgba(var(--ct-primary-rgb, 114, 124, 245), 0.1)' }} cx="400" cy="400" r="600" />
                        <circle style={{ fill: 'rgba(var(--ct-primary-rgb, 114, 124, 245), 0.2)' }} cx="400" cy="400" r="500" />
                        <circle style={{ fill: 'rgba(var(--ct-primary-rgb, 114, 124, 245), 0.3)' }} cx="400" cy="400" r="300" />
                        <circle style={{ fill: 'rgba(var(--ct-primary-rgb, 114, 124, 245), 0.4)' }} cx="400" cy="400" r="200" />
                        <circle style={{ fill: 'rgba(var(--ct-primary-rgb, 114, 124, 245), 0.5)' }} cx="400" cy="400" r="100" />
                    </g>
                </svg>
            </div>

            <div className="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xxl-4 col-lg-5">
                            <div className="card shadow-lg border-0" style={{ borderRadius: '12px', overflow: 'hidden' }}>
                                {/* Header / Logo */}
                                <div className="card-header py-4 text-center bg-primary text-white">
                                    <h3 className="m-0 fw-bold tracking-wide">Real ERP</h3>
                                    <p className="m-0 opacity-75 small">Enterprise Resource Planning</p>
                                </div>

                                <div className="card-body p-4">
                                    {title && (
                                        <div className="text-center w-75 m-auto mb-4">
                                            <h4 className="text-dark-50 text-center pb-0 fw-bold">{title}</h4>
                                        </div>
                                    )}
                                    {children}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
