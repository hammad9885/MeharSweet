import React, { useState } from 'react';
import { Head } from '@inertiajs/react';

export default function Welcome({ laravelVersion = '10.x', phpVersion = '8.5' }) {
    const [counter, setCounter] = useState(0);

    return (
        <div style={{
            minHeight: '100vh',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'center',
            alignItems: 'center',
            backgroundColor: '#0f172a',
            color: '#f8fafc',
            fontFamily: 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            padding: '24px'
        }}>
            <Head title="Welcome - Laravel + React" />

            <div style={{
                maxWidth: '640px',
                width: '100%',
                backgroundColor: '#1e293b',
                padding: '40px',
                borderRadius: '16px',
                boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5)',
                border: '1px solid #334155',
                textAlign: 'center'
            }}>
                <div style={{
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '12px',
                    marginBottom: '20px'
                }}>
                    <span style={{ fontSize: '32px' }}>⚡</span>
                    <h1 style={{
                        fontSize: '28px',
                        fontWeight: '700',
                        margin: 0,
                        background: 'linear-gradient(135deg, #f43f5e 0%, #38bdf8 100%)',
                        WebkitBackgroundClip: 'text',
                        WebkitTextFillColor: 'transparent'
                    }}>
                        Laravel + React
                    </h1>
                </div>

                <p style={{ color: '#94a3b8', fontSize: '16px', lineHeight: '1.6', marginBottom: '24px' }}>
                    Welcome to your Laravel and React application! All existing ERP routes, controllers, and Blade views have been completely preserved.
                </p>

                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(3, 1fr)',
                    gap: '12px',
                    marginBottom: '28px'
                }}>
                    <div style={{ background: '#0f172a', padding: '16px', borderRadius: '10px', border: '1px solid #334155' }}>
                        <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Framework</div>
                        <div style={{ fontSize: '16px', fontWeight: '600', color: '#f43f5e', marginTop: '4px' }}>Laravel {laravelVersion}</div>
                    </div>
                    <div style={{ background: '#0f172a', padding: '16px', borderRadius: '10px', border: '1px solid #334155' }}>
                        <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Frontend</div>
                        <div style={{ fontSize: '16px', fontWeight: '600', color: '#38bdf8', marginTop: '4px' }}>React 19 + Vite</div>
                    </div>
                    <div style={{ background: '#0f172a', padding: '16px', borderRadius: '10px', border: '1px solid #334155' }}>
                        <div style={{ fontSize: '12px', color: '#64748b', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Database</div>
                        <div style={{ fontSize: '16px', fontWeight: '600', color: '#34d399', marginTop: '4px' }}>MySQL</div>
                    </div>
                </div>

                <div style={{
                    backgroundColor: '#0f172a',
                    padding: '20px',
                    borderRadius: '12px',
                    border: '1px solid #334155',
                    marginBottom: '24px'
                }}>
                    <p style={{ color: '#cbd5e1', fontSize: '14px', marginBottom: '12px' }}>
                        Interactive React State Test:
                    </p>
                    <button
                        onClick={() => setCounter((c) => c + 1)}
                        style={{
                            background: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                            color: '#ffffff',
                            border: 'none',
                            padding: '10px 24px',
                            borderRadius: '8px',
                            fontWeight: '600',
                            fontSize: '15px',
                            cursor: 'pointer',
                            boxShadow: '0 4px 12px rgba(37, 99, 235, 0.4)',
                            transition: 'transform 0.1s ease'
                        }}
                    >
                        Clicked {counter} {counter === 1 ? 'time' : 'times'}
                    </button>
                </div>

                <div style={{ display: 'flex', gap: '12px', justifyContent: 'center' }}>
                    <a
                        href="/login"
                        style={{
                            display: 'inline-block',
                            backgroundColor: '#334155',
                            color: '#e2e8f0',
                            padding: '10px 20px',
                            borderRadius: '8px',
                            textDecoration: 'none',
                            fontSize: '14px',
                            fontWeight: '500'
                        }}
                    >
                        Go to ERP Login
                    </a>
                </div>
            </div>
        </div>
    );
}
