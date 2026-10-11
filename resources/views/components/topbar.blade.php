 <style>
        :root[data-bs-theme="light"] {
            --text: #040809;
            --background: #f7fbfb;
            --primary: #58a3b3;
            --secondary: #aa9dd2;
            --accent: #ae83c7;
        }
        :root[data-bs-theme="dark"]  {
            --text: #f4f9fa;
            --background: #030707;
            --primary: #4d97a8;
            --secondary: #3a2d62;
            --accent: #62377b;
        }
        body {
            background-color: var(--background);
            color: var(--text);
        }
        .page-title-box h3,
        .page-title-box h4,
        .header-title {
            color: var(--primary);
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .btn-primary:hover {
            background-color: var(--accent);
            border-color: var(--accent);
        }
        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: var(--accent);
            border-color: var(--accent);
        }
        .btn-danger {
            background-color: #d9534f;
            border-color: #d9534f;
            color: #fff;
        }
        .card {
            background-color: var(--background);
            border: 1px solid var(--secondary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        table th {
            background-color: var(--secondary);
            color: #fff;
        }
        table td {
            color: var(--text);
        }
        .breadcrumb-item a {
            color: var(--accent);
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: var(--primary);
        }
    </style>
<div class="navbar-custom new-topbar-ui">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo and Sidebar Toggle -->
            <div class="d-flex align-items-center">
                <a href="/" class="logo-topbar">
                    <span class="logo-lg">ERP</span>
                </a>
                <button class="btn btn-icon btn-sm button-toggle-menu" type="button" aria-label="Toggle sidebar">
                    <i class="mdi mdi-menu"></i>
                </button>
            </div>



            <!-- Icons, Show, and User Dropdown -->
            <ul class="list-unstyled d-flex align-items-center gap-2 gap-md-3 mb-0">
                <!-- Show Bar (Desktop, Right Side) -->
                <li class="d-none d-lg-block">
                    <form class="app-search topbar-search" method="GET" action="">
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                            <input type="search" class="form-control" name="q" placeholder="Search..." aria-label="Search">
                            <button class="btn btn-outline-secondary d-none" type="submit" tabindex="-1">Go</button>
                        </div>
                    </form>
                </li>
                <li>
                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" title="Settings">
                        <i class="ri-settings-3-line"></i>
                    </button>
                </li>
                <li>
                    <button class="btn btn-icon btn-sm" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Theme Mode">
                        <i class="ri-moon-line"></i>
                    </button>
                </li>
                <li>
                    <button class="btn btn-icon btn-sm d-none d-md-flex" type="button" data-toggle="fullscreen" title="Fullscreen">
                        <i class="ri-fullscreen-line"></i>
                    </button>
                </li>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-account-circle fs-3" style="color:#6c757d;"></i>
                        <span class="d-none d-lg-inline-block ms-2 fw-semibold">Admin!</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Welcome!</h6>
                        
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-logout me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<!-- It is recommended to keep your styles in a separate CSS file -->
<style>
    /* Light Theme Variables */
    :root {
        --topbar-bg: linear-gradient(to right, #ffffff, #f7fbfb);
        --topbar-border: #e2e8f0;
        --topbar-text: #334155;
        --topbar-icon-bg: #f8fafc;
        --topbar-icon-bg-hover: #f1f5f9;
        --topbar-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        --search-bg: #f1f5f9;
        --search-text: #475569;
        --search-focus-border: #3e60d5;
        --dropdown-bg: #ffffff;
        --dropdown-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }

    /* Dark Theme Overrides */
    [data-bs-theme="dark"] {
        --topbar-bg: linear-gradient(to right, #1e293b, #283446);
        --topbar-border: #334155;
        --topbar-text: #e2e8f0;
        --topbar-icon-bg: #334155;
        --topbar-icon-bg-hover: #475569;
        --topbar-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -2px rgba(0, 0, 0, 0.2);
        --search-bg: #334155;
        --search-text: #cbd5e1;
        --search-focus-border: #60a5fa;
        --dropdown-bg: #1e293b;
        --dropdown-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.25), 0 4px 6px -4px rgba(0, 0, 0, 0.25);
    }

    /* Topbar Styles */
    .new-topbar-ui {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1001;
        background: var(--topbar-bg);
        border-bottom: 1px solid var(--topbar-border);
        box-shadow: var(--topbar-shadow);
        padding: 0.09rem 2.5rem 0.09rem 2.5rem;
        color: var(--topbar-text);
    }

    .new-topbar-ui .logo-topbar {
        text-decoration: none;
        color: var(--topbar-text);
        font-weight: 700;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .new-topbar-ui .logo-sm { display: none; }

    .new-topbar-ui .button-toggle-menu {
        display: none; /* Hidden on desktop */
    }
    
    .new-topbar-ui .btn-icon {
        background-color: var(--topbar-icon-bg);
        color: var(--topbar-text);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease;
    }

    .new-topbar-ui .btn-icon:hover {
        background-color: var(--topbar-icon-bg-hover);
    }

    /* Search Bar */
    .app-search .input-group {
        border-radius: 2rem;
        background-color: var(--search-bg);
        overflow: hidden;
    }

    .app-search .form-control,
    .app-search .input-group-text {
        background-color: transparent;
        border: none;
        color: var(--search-text);
        box-shadow: none;
    }

    .app-search .form-control:focus {
        box-shadow: 0 0 0 2px var(--search-focus-border);
    }

    /* Make the topbar search smaller and right-aligned */
    .topbar-search {
        width: 180px;
        min-width: 0;
    }
    .topbar-search .form-control {
        font-size: 0.95rem;
        padding-top: 0.3rem;
        padding-bottom: 0.3rem;
        height: 32px;
    }

    /* User Dropdown */
    .new-topbar-ui .nav-link.dropdown-toggle {
        color: var(--topbar-text);
    }

    .new-topbar-ui .dropdown-menu {
        background-color: var(--dropdown-bg);
        border: 1px solid var(--topbar-border);
        box-shadow: var(--dropdown-shadow);
        border-radius: 0.75rem;
        margin-top: 0.5rem !important;
        padding: 0.5rem;
    }

    .new-topbar-ui .dropdown-item {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }
    
    [data-bs-theme="dark"] .dropdown-item {
        color: var(--topbar-text);
    }
    
    [data-bs-theme="dark"] .dropdown-item:hover {
        background-color: var(--topbar-icon-bg-hover);
    }


    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .new-topbar-ui {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        .new-topbar-ui .logo-lg { display: none; }
        .new-topbar-ui .logo-sm { display: inline-block; }
        .new-topbar-ui .button-toggle-menu { display: inline-flex; }
        .new-topbar-ui .btn-icon {
            margin-left: 0.25rem;
            margin-right: 0.25rem;
        }
        .new-topbar-ui ul.list-unstyled {
            gap: 0.5rem !important;
        }
    }
</style>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var menuBtn = document.querySelector('.button-toggle-menu');
    var body = document.body;
    var sidebar = document.getElementById('sidebar'); // Assuming sidebar has this ID
    var sidebarCloseBtn = document.getElementById('sidebarClose'); // Assuming the sidebar has a close button

    // Toggle sidebar on mobile
    if (menuBtn) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth <= 991.98) {
                body.classList.toggle('sidebar-enable');
            }
        });
    }

    // Close sidebar with close button
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', function() {
            body.classList.remove('sidebar-enable');
        });
    }

    // Close sidebar by clicking overlay
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98 && body.classList.contains('sidebar-enable')) {
            if (sidebar && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                body.classList.remove('sidebar-enable');
            }
        }
    });

    // Remove sidebar-enable on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991.98) {
            body.classList.remove('sidebar-enable');
        }
    });
});
</script>