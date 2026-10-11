import React, { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ChartOfAccounts({
    account_masters = [],
    groups = [],
    titles = [],
    level2s = [],
    filters = {}
}) {
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedTitle, setSelectedTitle] = useState(filters.title || '');
    const [selectedLevel2, setSelectedLevel2] = useState(filters.level2_id || '');
    const [activeTab, setActiveTab] = useState('table'); // 'table' or 'tree'

    const filteredAccounts = useMemo(() => {
        return account_masters.filter(account => {
            const matchesSearch =
                !searchTerm ||
                (account.title && account.title.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (account.full_code && account.full_code.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (account.level2s?.title && account.level2s.title.toLowerCase().includes(searchTerm.toLowerCase()));

            const matchesTitle = !selectedTitle || account.title === selectedTitle;
            const matchesLevel2 = !selectedLevel2 || String(account.level2_id) === String(selectedLevel2);

            return matchesSearch && matchesTitle && matchesLevel2;
        });
    }, [account_masters, searchTerm, selectedTitle, selectedLevel2]);

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/account/a_master', {
            title: selectedTitle,
            level2_id: selectedLevel2,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleClear = () => {
        setSelectedTitle('');
        setSelectedLevel2('');
        setSearchTerm('');
        router.get('/account/a_master');
    };

    const handleDelete = (id, title) => {
        if (confirm(`Are you sure you want to delete account "${title}"?`)) {
            router.delete(`/account/a_master/${id}`, {
                preserveScroll: true,
            });
        }
    };

    return (
        <AuthenticatedLayout header="Chart of Accounts (COA)">
            <Head title="Chart of Accounts - Real ERP" />

            {/* Filter Bar */}
            <div className="card shadow-sm border-0 mb-3 no-print">
                <div className="card-body">
                    <form onSubmit={handleFilterSubmit} className="row g-3 align-items-end">
                        <div className="col-md-4">
                            <label className="form-label small fw-bold text-muted">Account Title</label>
                            <select
                                className="form-select"
                                value={selectedTitle}
                                onChange={(e) => setSelectedTitle(e.target.value)}
                            >
                                <option value="">All Account Titles</option>
                                {titles.map(acc => (
                                    <option key={acc.id} value={acc.title}>
                                        {acc.title} ({acc.full_code})
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-4">
                            <label className="form-label small fw-bold text-muted">Level 2 Category</label>
                            <select
                                className="form-select"
                                value={selectedLevel2}
                                onChange={(e) => setSelectedLevel2(e.target.value)}
                            >
                                <option value="">All Level 2</option>
                                {level2s.map(l2 => (
                                    <option key={l2.id} value={l2.id}>{l2.title}</option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-4 d-flex gap-2">
                            <button type="submit" className="btn btn-primary">
                                <i className="mdi mdi-filter me-1"></i> Show
                            </button>
                            <button type="button" className="btn btn-secondary" onClick={handleClear}>
                                <i className="mdi mdi-refresh me-1"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {/* Main Content Card */}
            <div className="card shadow-sm border-0">
                <div className="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div className="d-flex align-items-center gap-2">
                        <a href="/account/a_master/create" className="btn btn-primary no-print">
                            <i className="mdi mdi-plus-circle me-1"></i> Add Account Master
                        </a>
                        <div className="btn-group btn-group-sm no-print">
                            <button
                                className={`btn ${activeTab === 'table' ? 'btn-dark' : 'btn-outline-secondary'}`}
                                onClick={() => setActiveTab('table')}
                            >
                                <i className="mdi mdi-table me-1"></i> Table View
                            </button>
                            <button
                                className={`btn ${activeTab === 'tree' ? 'btn-dark' : 'btn-outline-secondary'}`}
                                onClick={() => setActiveTab('tree')}
                            >
                                <i className="mdi mdi-file-tree me-1"></i> Tree View
                            </button>
                        </div>
                        <button type="button" className="btn btn-outline-secondary btn-sm no-print" onClick={() => window.print()}>
                            <i className="mdi mdi-printer me-1"></i> Print
                        </button>
                    </div>

                    <div className="no-print" style={{ minWidth: '240px' }}>
                        <div className="input-group input-group-sm">
                            <span className="input-group-text bg-light border-end-0">
                                <i className="mdi mdi-magnify"></i>
                            </span>
                            <input
                                type="text"
                                className="form-control bg-light border-start-0"
                                placeholder="Search accounts..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                        </div>
                    </div>
                </div>

                <div className="card-body p-0">
                    {activeTab === 'table' ? (
                        <div className="table-responsive">
                            <table className="table table-hover table-striped align-middle mb-0">
                                <thead className="table-light text-uppercase small text-muted">
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Title</th>
                                        <th>Level 2</th>
                                        <th className="text-end">Opening Balance</th>
                                        <th>Opening Date</th>
                                        <th className="text-center no-print" style={{ width: '130px' }}>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredAccounts.length > 0 ? (
                                        filteredAccounts.map(account => (
                                            <tr key={account.id}>
                                                <td className="fw-semibold text-primary">{account.full_code || account.code}</td>
                                                <td className="fw-medium text-dark">{account.title}</td>
                                                <td>
                                                    <span className="badge bg-secondary-lighten text-secondary">
                                                        {account.level2s?.title || 'N/A'}
                                                    </span>
                                                </td>
                                                <td className="text-end fw-semibold">
                                                    {account.open_bal ? Number(account.open_bal).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '0.00'}
                                                </td>
                                                <td>{account.opening_date || '-'}</td>
                                                <td className="text-center no-print">
                                                    <div className="btn-group btn-group-sm">
                                                        <a
                                                            href={`/account/a_master/${account.id}/edit`}
                                                            className="btn btn-outline-primary"
                                                            title="Edit"
                                                        >
                                                            <i className="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button
                                                            type="button"
                                                            className="btn btn-outline-danger"
                                                            title="Delete"
                                                            onClick={() => handleDelete(account.id, account.title)}
                                                        >
                                                            <i className="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="6" className="text-center py-5 text-muted">
                                                <i className="mdi mdi-wallet-outline font-28 d-block mb-2 opacity-50"></i>
                                                No accounts found matching the criteria.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        /* Tree View */
                        <div className="p-4">
                            {groups.map(group => (
                                <div key={group.id} className="mb-4">
                                    <h5 className="text-primary fw-bold mb-2">
                                        <i className="mdi mdi-folder-outline me-2"></i>{group.name}
                                    </h5>
                                    <div className="ms-3 border-start ps-3">
                                        {group.level1s?.map(l1 => (
                                            <div key={l1.id} className="mb-3">
                                                <h6 className="text-dark fw-semibold">
                                                    <i className="mdi mdi-folder-open-outline me-2 text-warning"></i>{l1.title}
                                                </h6>
                                                <div className="ms-3 border-start ps-3">
                                                    {l1.level2s?.map(l2 => (
                                                        <div key={l2.id} className="mb-2">
                                                            <div className="text-muted small fw-bold">
                                                                <i className="mdi mdi-chevron-right me-1 text-info"></i>{l2.title}
                                                            </div>
                                                            <ul className="list-unstyled ms-3 small my-1">
                                                                {l2.account_masters?.map(acc => (
                                                                    <li key={acc.id} className="py-1 d-flex justify-content-between align-items-center border-bottom border-light">
                                                                        <span>
                                                                            <span className="text-primary fw-medium me-2">[{acc.full_code}]</span>
                                                                            {acc.title}
                                                                        </span>
                                                                        <span className="fw-semibold">
                                                                            Bal: {acc.open_bal || '0.00'}
                                                                        </span>
                                                                    </li>
                                                                ))}
                                                            </ul>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <div className="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center text-muted small">
                    <div>Showing <b>{filteredAccounts.length}</b> of <b>{account_masters.length}</b> accounts</div>
                    <div>ERP Financial System</div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
