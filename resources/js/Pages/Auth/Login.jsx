import React, { useState } from 'react';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: 'demo@gmail.com',
        password: 'password',
        remember: true,
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        post('/login', {
            onFinish: () => reset('password'),
        });
    };

    const fillCredentials = (email, password) => {
        setData(prev => ({
            ...prev,
            email,
            password
        }));
    };

    return (
        <div style={{
            minHeight: '100vh',
            background: 'radial-gradient(circle at 10% 20%, rgba(212, 175, 55, 0.08) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(243, 198, 79, 0.06) 0%, transparent 40%), #0B0F19',
            fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            color: '#E5E7EB',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            padding: '24px 16px',
            position: 'relative',
            overflow: 'hidden'
        }}>
            <Head title="Sign In — Mehar Sweets & Bakers ERP" />

            {/* Ambient luxury glow rings */}
            <div style={{
                position: 'absolute',
                top: '-120px',
                right: '-120px',
                width: '450px',
                height: '450px',
                background: 'radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%)',
                filter: 'blur(60px)',
                pointerEvents: 'none'
            }} />
            <div style={{
                position: 'absolute',
                bottom: '-150px',
                left: '-150px',
                width: '500px',
                height: '500px',
                background: 'radial-gradient(circle, rgba(184, 134, 11, 0.12) 0%, transparent 70%)',
                filter: 'blur(80px)',
                pointerEvents: 'none'
            }} />

            {/* Main Luxury Container */}
            <div style={{
                width: '100%',
                maxWidth: '1080px',
                background: 'rgba(17, 24, 39, 0.75)',
                backdropFilter: 'blur(20px)',
                WebkitBackdropFilter: 'blur(20px)',
                borderRadius: '24px',
                border: '1px solid rgba(212, 175, 55, 0.25)',
                boxShadow: '0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 40px rgba(212, 175, 55, 0.08)',
                overflow: 'hidden',
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(340px, 1fr))',
                zIndex: 1
            }}>
                {/* Left Brand Showcase Column */}
                <div style={{
                    padding: '48px 40px',
                    background: 'linear-gradient(145deg, rgba(26, 32, 48, 0.9) 0%, rgba(13, 17, 28, 0.95) 100%)',
                    borderRight: '1px solid rgba(212, 175, 55, 0.15)',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                    position: 'relative'
                }}>
                    <div>
                        {/* Luxury Crown/Sweets Emblem */}
                        <div style={{ display: 'flex', alignItems: 'center', gap: '14px', marginBottom: '28px' }}>
                            <div style={{
                                width: '56px',
                                height: '56px',
                                borderRadius: '16px',
                                background: 'linear-gradient(135deg, #F5D061 0%, #D4AF37 50%, #996515 100%)',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                boxShadow: '0 8px 24px rgba(212, 175, 55, 0.35)',
                                color: '#111827',
                                fontSize: '28px',
                                fontWeight: 'bold'
                            }}>
                                👑
                            </div>
                            <div>
                                <h1 style={{
                                    margin: 0,
                                    fontSize: '24px',
                                    fontWeight: '800',
                                    letterSpacing: '-0.5px',
                                    background: 'linear-gradient(135deg, #FFFDF0 0%, #F5D061 60%, #D4AF37 100%)',
                                    WebkitBackgroundClip: 'text',
                                    WebkitTextFillColor: 'transparent',
                                    lineHeight: '1.2'
                                }}>
                                    Mehar Sweets & Bakers
                                </h1>
                                <span style={{
                                    fontSize: '12px',
                                    color: '#D4AF37',
                                    textTransform: 'uppercase',
                                    letterSpacing: '2px',
                                    fontWeight: '600'
                                }}>
                                    Enterprise GST ERP Suite
                                </span>
                            </div>
                        </div>

                        {/* Gold Badge */}
                        <div style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '8px',
                            padding: '6px 14px',
                            borderRadius: '30px',
                            background: 'rgba(212, 175, 55, 0.12)',
                            border: '1px solid rgba(212, 175, 55, 0.3)',
                            color: '#F3C64F',
                            fontSize: '12px',
                            fontWeight: '600',
                            marginBottom: '28px'
                        }}>
                            <span style={{ fontSize: '10px' }}>✦</span> Premium Confectionery Management System
                        </div>

                        <h2 style={{
                            fontSize: '28px',
                            fontWeight: '700',
                            color: '#FFFFFF',
                            lineHeight: '1.3',
                            marginBottom: '16px'
                        }}>
                            Artisanal Tradition Meets <br />
                            <span style={{ color: '#F5D061' }}>Intelligent Enterprise Control</span>
                        </h2>

                        <p style={{
                            fontSize: '14px',
                            color: '#9CA3AF',
                            lineHeight: '1.6',
                            marginBottom: '32px'
                        }}>
                            Complete operations control designed for Mehar Sweets: Recipe formulations, contractor lot postings, GST compliance, real-time sales & financial audits.
                        </p>

                        {/* Feature Pills */}
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px', marginBottom: '32px' }}>
                            <div style={{
                                padding: '12px 14px',
                                background: 'rgba(255, 255, 255, 0.03)',
                                border: '1px solid rgba(255, 255, 255, 0.07)',
                                borderRadius: '12px'
                            }}>
                                <div style={{ color: '#F5D061', fontSize: '18px', marginBottom: '4px' }}>🍰</div>
                                <div style={{ fontSize: '12px', fontWeight: '600', color: '#F3F4F6' }}>Production & Recipe</div>
                                <div style={{ fontSize: '11px', color: '#9CA3AF' }}>Sweet batch calculation</div>
                            </div>
                            <div style={{
                                padding: '12px 14px',
                                background: 'rgba(255, 255, 255, 0.03)',
                                border: '1px solid rgba(255, 255, 255, 0.07)',
                                borderRadius: '12px'
                            }}>
                                <div style={{ color: '#F5D061', fontSize: '18px', marginBottom: '4px' }}>📦</div>
                                <div style={{ fontSize: '12px', fontWeight: '600', color: '#F3F4F6' }}>Contractor Lots</div>
                                <div style={{ fontSize: '11px', color: '#9CA3AF' }}>Raw material tracking</div>
                            </div>
                            <div style={{
                                padding: '12px 14px',
                                background: 'rgba(255, 255, 255, 0.03)',
                                border: '1px solid rgba(255, 255, 255, 0.07)',
                                borderRadius: '12px'
                            }}>
                                <div style={{ color: '#F5D061', fontSize: '18px', marginBottom: '4px' }}>🧾</div>
                                <div style={{ fontSize: '12px', fontWeight: '600', color: '#F3F4F6' }}>Sales & Invoices</div>
                                <div style={{ fontSize: '11px', color: '#9CA3AF' }}>GST FBR tax invoices</div>
                            </div>
                            <div style={{
                                padding: '12px 14px',
                                background: 'rgba(255, 255, 255, 0.03)',
                                border: '1px solid rgba(255, 255, 255, 0.07)',
                                borderRadius: '12px'
                            }}>
                                <div style={{ color: '#F5D061', fontSize: '18px', marginBottom: '4px' }}>📊</div>
                                <div style={{ fontSize: '12px', fontWeight: '600', color: '#F3F4F6' }}>Accounts & Ledgers</div>
                                <div style={{ fontSize: '11px', color: '#9CA3AF' }}>Trial balance & reports</div>
                            </div>
                        </div>
                    </div>

                    {/* Quick Demo Credentials Assistant */}
                    <div style={{
                        padding: '16px',
                        background: 'rgba(212, 175, 55, 0.08)',
                        borderRadius: '14px',
                        border: '1px dashed rgba(212, 175, 55, 0.3)'
                    }}>
                        <div style={{ fontSize: '12px', color: '#F3C64F', fontWeight: '600', marginBottom: '8px' }}>
                            ⚡ 1-Click Quick Demo Credentials:
                        </div>
                        <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                            <button
                                type="button"
                                onClick={() => fillCredentials('demo@gmail.com', '12345678')}
                                style={{
                                    fontSize: '11px',
                                    padding: '6px 10px',
                                    background: 'rgba(0, 0, 0, 0.4)',
                                    color: '#E5E7EB',
                                    border: '1px solid rgba(212, 175, 55, 0.4)',
                                    borderRadius: '8px',
                                    cursor: 'pointer'
                                }}
                            >
                                👤 demo@gmail.com
                            </button>
                            <button
                                type="button"
                                onClick={() => fillCredentials('admin@example.com', 'password')}
                                style={{
                                    fontSize: '11px',
                                    padding: '6px 10px',
                                    background: 'rgba(0, 0, 0, 0.4)',
                                    color: '#E5E7EB',
                                    border: '1px solid rgba(212, 175, 55, 0.4)',
                                    borderRadius: '8px',
                                    cursor: 'pointer'
                                }}
                            >
                                👑 admin@example.com
                            </button>
                        </div>
                    </div>
                </div>

                {/* Right Form Column */}
                <div style={{
                    padding: '48px 40px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    background: 'rgba(15, 20, 32, 0.95)'
                }}>
                    <div style={{ marginBottom: '32px' }}>
                        <div style={{
                            display: 'inline-block',
                            padding: '4px 10px',
                            background: 'rgba(212, 175, 55, 0.1)',
                            borderRadius: '6px',
                            color: '#D4AF37',
                            fontSize: '11px',
                            fontWeight: '700',
                            letterSpacing: '1px',
                            textTransform: 'uppercase',
                            marginBottom: '10px'
                        }}>
                            Security Portal
                        </div>
                        <h2 style={{
                            fontSize: '26px',
                            fontWeight: '800',
                            color: '#FFFFFF',
                            margin: '0 0 6px 0'
                        }}>
                            Sign In to Dashboard
                        </h2>
                        <p style={{ fontSize: '13px', color: '#9CA3AF', margin: 0 }}>
                            Enter your credentials to manage sweet production and finances.
                        </p>
                    </div>

                    {status && (
                        <div style={{
                            padding: '12px 16px',
                            borderRadius: '10px',
                            background: 'rgba(16, 185, 129, 0.15)',
                            border: '1px solid rgba(16, 185, 129, 0.3)',
                            color: '#34D399',
                            fontSize: '13px',
                            marginBottom: '20px'
                        }}>
                            {status}
                        </div>
                    )}

                    <form onSubmit={submit}>
                        {/* Email Input */}
                        <div style={{ marginBottom: '22px' }}>
                            <label style={{
                                display: 'block',
                                fontSize: '12px',
                                fontWeight: '600',
                                color: '#D1D5DB',
                                marginBottom: '8px',
                                letterSpacing: '0.3px'
                            }}>
                                Email Address
                            </label>
                            <div style={{ position: 'relative' }}>
                                <span style={{
                                    position: 'absolute',
                                    left: '14px',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    color: '#D4AF37',
                                    fontSize: '16px'
                                }}>
                                    ✉
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="your-email@mehar.com"
                                    required
                                    autoFocus
                                    style={{
                                        width: '100%',
                                        padding: '12px 14px 12px 42px',
                                        background: 'rgba(255, 255, 255, 0.04)',
                                        border: errors.email ? '1px solid #EF4444' : '1px solid rgba(212, 175, 55, 0.25)',
                                        borderRadius: '12px',
                                        color: '#FFFFFF',
                                        fontSize: '14px',
                                        outline: 'none',
                                        transition: 'all 0.2s',
                                        boxSizing: 'border-box'
                                    }}
                                    onFocus={(e) => e.target.style.borderColor = '#D4AF37'}
                                    onBlur={(e) => e.target.style.borderColor = 'rgba(212, 175, 55, 0.25)'}
                                />
                            </div>
                            {errors.email && (
                                <div style={{ color: '#F87171', fontSize: '12px', marginTop: '6px' }}>
                                    {errors.email}
                                </div>
                            )}
                        </div>

                        {/* Password Input */}
                        <div style={{ marginBottom: '20px' }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' }}>
                                <label style={{
                                    fontSize: '12px',
                                    fontWeight: '600',
                                    color: '#D1D5DB',
                                    letterSpacing: '0.3px',
                                    margin: 0
                                }}>
                                    Password
                                </label>
                                {canResetPassword && (
                                    <span style={{ fontSize: '11px', color: '#D4AF37', cursor: 'pointer' }}>
                                        Forgot Password?
                                    </span>
                                )}
                            </div>
                            <div style={{ position: 'relative' }}>
                                <span style={{
                                    position: 'absolute',
                                    left: '14px',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    color: '#D4AF37',
                                    fontSize: '16px'
                                }}>
                                    🔒
                                </span>
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    name="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="Enter your security password"
                                    required
                                    style={{
                                        width: '100%',
                                        padding: '12px 42px 12px 42px',
                                        background: 'rgba(255, 255, 255, 0.04)',
                                        border: errors.password ? '1px solid #EF4444' : '1px solid rgba(212, 175, 55, 0.25)',
                                        borderRadius: '12px',
                                        color: '#FFFFFF',
                                        fontSize: '14px',
                                        outline: 'none',
                                        transition: 'all 0.2s',
                                        boxSizing: 'border-box'
                                    }}
                                    onFocus={(e) => e.target.style.borderColor = '#D4AF37'}
                                    onBlur={(e) => e.target.style.borderColor = 'rgba(212, 175, 55, 0.25)'}
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    style={{
                                        position: 'absolute',
                                        right: '12px',
                                        top: '50%',
                                        transform: 'translateY(-50%)',
                                        background: 'none',
                                        border: 'none',
                                        color: '#9CA3AF',
                                        cursor: 'pointer',
                                        fontSize: '14px'
                                    }}
                                >
                                    {showPassword ? '👁️' : '🔒'}
                                </button>
                            </div>
                            {errors.password && (
                                <div style={{ color: '#F87171', fontSize: '12px', marginTop: '6px' }}>
                                    {errors.password}
                                </div>
                            )}
                        </div>

                        {/* Remember Me */}
                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '26px' }}>
                            <input
                                type="checkbox"
                                id="rememberMe"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                style={{
                                    accentColor: '#D4AF37',
                                    width: '16px',
                                    height: '16px',
                                    cursor: 'pointer'
                                }}
                            />
                            <label htmlFor="rememberMe" style={{ fontSize: '13px', color: '#9CA3AF', cursor: 'pointer', margin: 0 }}>
                                Keep me signed in for 30 days
                            </label>
                        </div>

                        {/* Luxury Submit Button */}
                        <button
                            type="submit"
                            disabled={processing}
                            style={{
                                width: '100%',
                                padding: '14px',
                                borderRadius: '12px',
                                border: 'none',
                                background: 'linear-gradient(135deg, #F5D061 0%, #D4AF37 50%, #996515 100%)',
                                color: '#111827',
                                fontSize: '14px',
                                fontWeight: '700',
                                letterSpacing: '0.5px',
                                cursor: processing ? 'not-allowed' : 'pointer',
                                boxShadow: '0 8px 24px rgba(212, 175, 55, 0.35)',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                gap: '8px',
                                transition: 'all 0.2s',
                                opacity: processing ? 0.8 : 1
                            }}
                            onMouseOver={(e) => !processing && (e.currentTarget.style.filter = 'brightness(1.1)')}
                            onMouseOut={(e) => (e.currentTarget.style.filter = 'none')}
                        >
                            {processing ? (
                                <span>Authenticating with Database...</span>
                            ) : (
                                <>
                                    <span>Sign In to Mehar Portal</span>
                                    <span>→</span>
                                </>
                            )}
                        </button>
                    </form>

                    {/* Direct React Dashboard / Showcase bypass */}
                    <div style={{
                        marginTop: '32px',
                        paddingTop: '20px',
                        borderTop: '1px solid rgba(255, 255, 255, 0.08)',
                        textAlign: 'center'
                    }}>
                        <div style={{ fontSize: '12px', color: '#9CA3AF', marginBottom: '12px' }}>
                            Direct Frontend Exploration:
                        </div>
                        <div style={{ display: 'flex', gap: '10px', justifyContent: 'center' }}>
                            <a
                                href="/admin/dashboard"
                                style={{
                                    fontSize: '12px',
                                    padding: '7px 14px',
                                    borderRadius: '8px',
                                    background: 'rgba(255, 255, 255, 0.05)',
                                    color: '#F5D061',
                                    textDecoration: 'none',
                                    border: '1px solid rgba(212, 175, 55, 0.2)'
                                }}
                            >
                                📊 Direct Dashboard
                            </a>
                            <a
                                href="/auto-login-debug"
                                style={{
                                    fontSize: '12px',
                                    padding: '7px 14px',
                                    borderRadius: '8px',
                                    background: 'rgba(255, 255, 255, 0.05)',
                                    color: '#9CA3AF',
                                    textDecoration: 'none',
                                    border: '1px solid rgba(255, 255, 255, 0.1)'
                                }}
                            >
                                ⚡ 1-Click Auto Login
                            </a>
                        </div>
                    </div>

                    <div style={{ marginTop: '24px', textAlign: 'center', fontSize: '11px', color: '#6B7280' }}>
                        © 2026 Mehar Sweets & Bakers • All Rights Reserved
                    </div>
                </div>
            </div>
        </div>
    );
}
