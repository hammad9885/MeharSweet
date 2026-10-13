import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Permissions({
    users = [],
    accountsModules = [],
    saleModules = [],
    purchaseModules = [],
    manufacturingModules = [],
    rawMaterialModules = [],
    finishedProductModules = [],
    stockModules = [],
    setupModules = []
}) {
    const [selectedUserId, setSelectedUserId] = useState('');
    const [activeTab, setActiveTab] = useState('accounts');
    const [permissions, setPermissions] = useState({});
    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);

    const tabs = [
        { id: 'accounts', label: 'Accounts', modules: accountsModules },
        { id: 'sale', label: 'Sale', modules: saleModules },
        { id: 'purchase', label: 'Purchase', modules: purchaseModules },
        { id: 'manufacturing', label: 'Manufacturing', modules: manufacturingModules },
        { id: 'rawmaterial', label: 'Raw Material', modules: rawMaterialModules },
        { id: 'finishedproduct', label: 'Finished Product', modules: finishedProductModules },
        { id: 'stock', label: 'Stock Management', modules: stockModules },
        { id: 'setup', label: 'Set-up', modules: setupModules },
    ];

    useEffect(() => {
        if (!selectedUserId) {
            setPermissions({});
            return;
        }

        setLoading(true);
        fetch(`/get-user-permissions/${selectedUserId}`)
            .then(res => res.json())
            .then(data => {
                const map = {};
                data.forEach(perm => {
                    map[perm.module_id] = {
                        read: perm.read === 1 || perm.read === true,
                        write: perm.write === 1 || perm.write === true,
                        update: perm.update === 1 || perm.update === true,
                        delete: perm.delete === 1 || perm.delete === true,
                    };
                });
                setPermissions(map);
            })
            .catch(err => {
                console.error('Failed to load user permissions', err);
            })
            .finally(() => {
                setLoading(false);
            });
    }, [selectedUserId]);

    const handleCheckboxChange = (moduleId, field, checked) => {
        setPermissions(prev => ({
            ...prev,
            [moduleId]: {
                ...(prev[moduleId] || { read: false, write: false, update: false, delete: false }),
                [field]: checked
            }
        }));
    };

    const handleSelectAllColumn = (modules, field, checked) => {
        setPermissions(prev => {
            const next = { ...prev };
            modules.forEach(m => {
                next[m.id] = {
                    ...(next[m.id] || { read: false, write: false, update: false, delete: false }),
                    [field]: checked
                };
            });
            return next;
        });
    };

    const handleSave = (e) => {
        e.preventDefault();
        if (!selectedUserId) {
            alert('Please select a user first.');
            return;
        }

        setSaving(true);
        // Format payload: permissions[moduleId][read] = 1 or 0
        const formattedPerms = {};
        Object.entries(permissions).forEach(([modId, perms]) => {
            formattedPerms[modId] = {
                read: perms.read ? 1 : 0,
                write: perms.write ? 1 : 0,
                update: perms.update ? 1 : 0,
                delete: perms.delete ? 1 : 0,
            };
        });

        router.post('/user_permissions', {
            userid: selectedUserId,
            permissions: formattedPerms
        }, {
            onFinish: () => setSaving(false)
        });
    };

    const currentTabModules = tabs.find(t => t.id === activeTab)?.modules || [];

    return (
        <AuthenticatedLayout header="User Permission Management">
            <Head title="User Permissions - Real ERP" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h3 className="fw-bold text-dark mb-1">User Permission Management</h3>
                        <p className="text-muted small mb-0">Configure role-based access control (Read, Write, Update, Delete) per ERP module</p>
                    </div>
                    <Link href="/users" className="btn btn-outline-secondary btn-sm">
                        <i className="mdi mdi-arrow-left me-1"></i> Back to Users
                    </Link>
                </div>

                <form onSubmit={handleSave}>
                    {/* User Selection Card */}
                    <div className="card border-0 shadow-sm mb-4">
                        <div className="card-body">
                            <div className="row align-items-end g-3">
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold text-muted small">Select User *</label>
                                    <select
                                        className="form-select"
                                        value={selectedUserId}
                                        onChange={(e) => setSelectedUserId(e.target.value)}
                                        required
                                    >
                                        <option value="">-- Choose User --</option>
                                        {users.map(u => (
                                            <option key={u.id} value={u.id}>
                                                {u.name} ({u.email})
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-md-6 d-flex gap-2">
                                    <button
                                        type="submit"
                                        className="btn btn-primary px-4"
                                        disabled={!selectedUserId || saving || loading}
                                    >
                                        {saving ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                Saving Permissions...
                                            </>
                                        ) : (
                                            <>
                                                <i className="mdi mdi-content-save me-1"></i> Save Permissions
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Module Permissions Card */}
                    <div className="card border-0 shadow-sm">
                        <div className="card-header bg-white border-bottom p-0">
                            <ul className="nav nav-tabs card-header-tabs m-0 border-0">
                                {tabs.map(tab => (
                                    <li className="nav-item" key={tab.id}>
                                        <button
                                            type="button"
                                            className={`nav-link border-0 py-3 px-4 fw-semibold ${activeTab === tab.id ? 'active text-primary border-bottom border-primary border-2' : 'text-muted'}`}
                                            onClick={() => setActiveTab(tab.id)}
                                        >
                                            {tab.label} ({tab.modules.length})
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <div className="card-body p-0">
                            {loading ? (
                                <div className="text-center py-5">
                                    <div className="spinner-border text-primary" role="status">
                                        <span className="visually-hidden">Loading...</span>
                                    </div>
                                    <p className="mt-2 text-muted small">Loading user permissions...</p>
                                </div>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table table-bordered table-hover align-middle mb-0">
                                        <thead className="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th className="ps-4">Module Name</th>
                                                <th className="text-center" style={{ width: '130px' }}>
                                                    <div>Read ALL</div>
                                                    <input
                                                        type="checkbox"
                                                        className="form-check-input mt-1"
                                                        onChange={(e) => handleSelectAllColumn(currentTabModules, 'read', e.target.checked)}
                                                    />
                                                </th>
                                                <th className="text-center" style={{ width: '130px' }}>
                                                    <div>Write ALL</div>
                                                    <input
                                                        type="checkbox"
                                                        className="form-check-input mt-1"
                                                        onChange={(e) => handleSelectAllColumn(currentTabModules, 'write', e.target.checked)}
                                                    />
                                                </th>
                                                <th className="text-center" style={{ width: '130px' }}>
                                                    <div>Update ALL</div>
                                                    <input
                                                        type="checkbox"
                                                        className="form-check-input mt-1"
                                                        onChange={(e) => handleSelectAllColumn(currentTabModules, 'update', e.target.checked)}
                                                    />
                                                </th>
                                                <th className="text-center pe-4" style={{ width: '130px' }}>
                                                    <div>Delete ALL</div>
                                                    <input
                                                        type="checkbox"
                                                        className="form-check-input mt-1"
                                                        onChange={(e) => handleSelectAllColumn(currentTabModules, 'delete', e.target.checked)}
                                                    />
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {currentTabModules.length > 0 ? (
                                                currentTabModules.map(mod => {
                                                    const perm = permissions[mod.id] || { read: false, write: false, update: false, delete: false };
                                                    return (
                                                        <tr key={mod.id}>
                                                            <td className="ps-4 fw-semibold text-dark">{mod.name}</td>
                                                            <td className="text-center">
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={!!perm.read}
                                                                    onChange={(e) => handleCheckboxChange(mod.id, 'read', e.target.checked)}
                                                                />
                                                            </td>
                                                            <td className="text-center">
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={!!perm.write}
                                                                    onChange={(e) => handleCheckboxChange(mod.id, 'write', e.target.checked)}
                                                                />
                                                            </td>
                                                            <td className="text-center">
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={!!perm.update}
                                                                    onChange={(e) => handleCheckboxChange(mod.id, 'update', e.target.checked)}
                                                                />
                                                            </td>
                                                            <td className="text-center pe-4">
                                                                <input
                                                                    type="checkbox"
                                                                    className="form-check-input"
                                                                    checked={!!perm.delete}
                                                                    onChange={(e) => handleCheckboxChange(mod.id, 'delete', e.target.checked)}
                                                                />
                                                            </td>
                                                        </tr>
                                                    );
                                                })
                                            ) : (
                                                <tr>
                                                    <td colSpan="5" className="text-center py-4 text-muted">
                                                        No modules defined in this section.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>

                        <div className="card-footer bg-light border-top py-3 d-flex justify-content-between align-items-center">
                            <span className="text-muted small">
                                {selectedUserId ? 'Permissions will apply immediately upon saving.' : 'Please select a user to configure permissions.'}
                            </span>
                            <button
                                type="submit"
                                className="btn btn-primary px-4"
                                disabled={!selectedUserId || saving || loading}
                            >
                                <i className="mdi mdi-content-save me-1"></i> Save Permissions
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
