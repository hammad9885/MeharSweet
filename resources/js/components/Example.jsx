import React, { useState } from 'react';

export default function Example({ title = 'Laravel + React' }) {
    const [count, setCount] = useState(0);

    return (
        <div style={{
            fontFamily: 'system-ui, -apple-system, sans-serif',
            padding: '24px',
            borderRadius: '12px',
            backgroundColor: '#f8fafc',
            border: '1px solid #e2e8f0',
            maxWidth: '480px',
            margin: '20px auto',
            textAlign: 'center',
            boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
        }}>
            <h2 style={{ color: '#1e293b', marginBottom: '8px' }}>{title}</h2>
            <p style={{ color: '#64748b', fontSize: '14px', marginBottom: '16px' }}>
                React is running seamlessly alongside Laravel & MySQL!
            </p>
            <div style={{ display: 'flex', gap: '8px', justifyContent: 'center', alignItems: 'center' }}>
                <button
                    onClick={() => setCount((c) => c + 1)}
                    style={{
                        backgroundColor: '#ef4444',
                        color: '#fff',
                        border: 'none',
                        padding: '8px 16px',
                        borderRadius: '6px',
                        fontWeight: '600',
                        cursor: 'pointer'
                    }}
                >
                    Count: {count}
                </button>
            </div>
        </div>
    );
}
