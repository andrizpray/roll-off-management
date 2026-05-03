<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Roll Off Management')</title>

    <!-- Font Awesome -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- Chart.js -->
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <!-- Tailwind CSS CDN (preflight disabled to coexist with Bootstrap sidebar) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        dark: { 900: '#0d1117', 800: '#161b22', 700: '#1c2128', 600: '#21262d', 500: '#30363d' },
                        txt: { primary: '#f0f6fc', secondary: '#c9d1d9', muted: '#8b949e', dim: '#484f58' },
                        accent: { blue: '#58a6ff', green: '#238636', teal: '#39d2c0', yellow: '#d29922', red: '#f85149', purple: '#bc8cff', orange: '#ff7b72' }
                    }
                }
            }
        }
    </script>

    <style>
        /* ===== SB ADMIN 2 DARK THEME ===== */
        :root {
            --bg-primary: #0d1117; --bg-card: #161b22; --bg-card-header: #1c2128; --bg-input: #0d1117;
            --border: #30363d; --text-primary: #f0f6fc; --text-secondary: #c9d1d9; --text-muted: #8b949e; --text-dim: #484f58;
            --accent-blue: #58a6ff; --accent-green: #238636; --accent-teal: #39d2c0;
            --accent-yellow: #d29922; --accent-red: #f85149; --accent-purple: #bc8cff;
        }
        body { background: var(--bg-primary); color: var(--text-secondary); font-family: 'Nunito', sans-serif; }
        #content-wrapper { background: var(--bg-primary); }
        .card { background: var(--bg-card) !important; border: 1px solid var(--border) !important; border-radius: 10px !important; overflow: hidden; }
        .card-header { background: var(--bg-card-header) !important; border-bottom: 1px solid var(--border) !important; }
        .card-body { background: var(--bg-card) !important; }

        /* SB Admin text overrides */
        .text-gray-100, .text-gray-800, .text-gray-900 { color: var(--text-primary) !important; }
        .text-gray-600, .text-gray-500, .text-gray-400 { color: var(--text-muted) !important; }
        .text-gray-300 { color: var(--text-secondary) !important; }
        .text-primary { color: var(--accent-blue) !important; }
        .text-success { color: var(--accent-green) !important; }
        .text-info { color: var(--accent-teal) !important; }
        .text-warning { color: var(--accent-yellow) !important; }
        .text-danger { color: var(--accent-red) !important; }

        /* Topbar / Footer */
        .topbar { background: var(--bg-card) !important; border-bottom: 1px solid var(--border) !important; }
        footer.sticky-footer { background: var(--bg-card) !important; border-top: 1px solid var(--border); }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); transition: all 0.2s; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.12); }
        .sidebar-heading { color: rgba(255,255,255,0.4); }

        /* Tables */
        .table-bordered { border-color: var(--border) !important; }
        .table-bordered td, .table-bordered th { border-color: var(--border) !important; }
        .table thead th { background: var(--bg-card-header); color: var(--text-muted); border-bottom: 2px solid var(--border) !important; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 6px; }
        .table td { color: var(--text-secondary); border-top: 1px solid #21262d; vertical-align: middle; font-size: 0.8rem; }
        .table-hover tbody tr { transition: background 0.15s; cursor: pointer; }
        .table-hover tbody tr:hover { background: var(--bg-card-header); }

        /* Forms */
        .form-control, .custom-select, .form-control-sm {
            background: var(--bg-input); color: var(--text-secondary); border: 1px solid var(--border);
            border-radius: 8px; font-size: 0.82rem; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .custom-select:focus {
            background: var(--bg-input); color: var(--text-secondary);
            border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(88,166,255,0.12);
        }
        .form-control::placeholder { color: var(--text-dim); }
        .form-group label, label { color: var(--text-muted); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Buttons */
        .btn-primary { background: var(--accent-green); border-color: var(--accent-green); }
        .btn-primary:hover { background: #2ea043; }
        .btn-outline-primary { color: var(--accent-blue); border-color: var(--border); }
        .btn-outline-primary:hover { background: var(--bg-card-header); border-color: var(--accent-blue); }
        .btn-outline-secondary { color: var(--text-muted); border-color: var(--border); }
        .btn-outline-secondary:hover { background: var(--bg-card-header); }

        /* Pagination */
        .page-link { background: var(--bg-card); color: var(--accent-blue); border: 1px solid var(--border); border-radius: 6px; margin: 0 2px; }
        .page-link:hover { background: var(--bg-card-header); }
        .page-item.active .page-link { background: var(--accent-green); border-color: var(--accent-green); }
        .page-item.disabled .page-link { color: var(--text-dim); }

        /* Border-left cards */
        .border-left-primary { border-left: 0.25rem solid var(--accent-blue) !important; }
        .border-left-success { border-left: 0.25rem solid var(--accent-green) !important; }
        .border-left-info { border-left: 0.25rem solid var(--accent-teal) !important; }
        .border-left-warning { border-left: 0.25rem solid var(--accent-yellow) !important; }
        .border-left-danger { border-left: 0.25rem solid var(--accent-red) !important; }
        .bg-light { background: var(--bg-card-header) !important; }
        .shadow { box-shadow: 0 2px 12px rgba(0,0,0,0.25) !important; }

        /* Badges */
        .badge { font-size: 0.68rem; padding: 3px 8px; border-radius: 5px; font-weight: 600; }
        .badge-good { background: var(--accent-green); color: #fff; }
        .badge-hold { background: var(--accent-yellow); color: #000; }
        .badge-problem { background: var(--accent-red); color: #fff; }
        .badge-na { background: #484f58; color: #fff; }
        .badge-loc { background: rgba(88,166,255,0.12); color: var(--accent-blue); border: 1px solid rgba(88,166,255,0.25); }
        .badge-so { background: rgba(188,140,255,0.12); color: var(--accent-purple); border: 1px solid rgba(188,140,255,0.25); }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .container-fluid { padding: 0 8px !important; }
            .card-body { padding: 10px !important; }
        }

        /* Mobile card view */
        .mobile-cards { display: none; }
        .desktop-table { display: block; }
        @media (max-width: 768px) {
            .desktop-table { display: none; }
            .mobile-cards { display: block; }
        }
        .mobile-item-card {
            background: var(--bg-card); border: 1px solid var(--border); border-radius: 10px;
            padding: 14px; margin-bottom: 8px; transition: border-color 0.2s;
        }
        .mobile-item-card:hover { border-color: var(--accent-blue); }
        .mobile-item-card .lot-id { color: var(--accent-blue); font-weight: 700; font-size: 0.9rem; margin-bottom: 6px; display: block; }
        .mobile-item-card .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 0.78rem; }
        .mobile-item-card .info-label { color: var(--text-muted); }
        .mobile-item-card .info-value { color: var(--text-secondary); font-weight: 600; text-align: right; max-width: 65%; }
        .mobile-item-card .loc-tags { margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px; }
        .mobile-item-card .loc-tag { font-size: 0.62rem; padding: 2px 6px; border-radius: 5px; background: rgba(88,166,255,0.1); color: var(--accent-blue); border: 1px solid rgba(88,166,255,0.2); }
        @media (max-width: 768px) {
            .filter-row .col-lg-3, .filter-row .col-lg-2, .filter-row .col-md-6, .filter-row .col-md-4 {
                width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; margin-bottom: 6px !important;
            }
        }

        /* Quick search */
        .quick-search { background: var(--bg-card-header); border-bottom: 1px solid var(--border); padding: 10px 16px; }
        .quick-search input { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-secondary); border-radius: 20px; padding: 6px 16px; font-size: 0.85rem; }
        .quick-search input:focus { outline: none; border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(88,166,255,0.12); }
        .quick-search input::placeholder { color: var(--text-dim); }

        /* Info grid (detail page) */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
        @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr 1fr; } }
        .info-cell { background: var(--bg-card-header); border-radius: 8px; padding: 12px; border: 1px solid var(--border); }
        .info-cell .info-label { font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-cell .info-value { font-size: 0.9rem; color: var(--text-primary); font-weight: 600; }

        /* Location timeline */
        .loc-timeline { position: relative; padding-left: 24px; }
        .loc-timeline::before { content: ''; position: absolute; left: 7px; top: 10px; bottom: 10px; width: 2px; background: var(--border); border-radius: 1px; }
        .loc-timeline-item { position: relative; padding: 6px 0 6px 24px; }
        .loc-timeline-item::before { content: ''; position: absolute; left: -21px; top: 12px; width: 10px; height: 10px; border-radius: 50%; background: var(--accent-blue); border: 2px solid var(--bg-card); }
        .loc-timeline-item .loc-period { font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px; }
        .loc-timeline-item .loc-value { font-size: 0.88rem; color: var(--text-primary); font-weight: 600; margin-top: 2px; }

        /* Chart containers */
        .chart-container { position: relative; width: 100%; }
        .chart-container canvas { max-height: 220px; }
        @media (max-width: 768px) { .chart-container canvas { max-height: 180px; } }

        /* Alerts */
        .alert-success { background: rgba(35,134,54,0.12); border-color: rgba(35,134,54,0.25); color: #56d364; }
        .alert-danger { background: rgba(248,81,73,0.12); border-color: rgba(248,81,73,0.25); color: var(--accent-red); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-primary); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-dim); }

        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
    @stack('styles')
</head>
<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon"><i class="fas fa-boxes-stacked"></i></div>
                <div class="sidebar-brand-text mx-3">Roll Off</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Inventory</div>
            <li class="nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('items.index') }}"><i class="fas fa-fw fa-box"></i><span>Roll Items</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Quality</div>
            <li class="nav-item {{ request()->routeIs('defects.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('defects.index') }}"><i class="fas fa-fw fa-exclamation-triangle"></i><span>Barang Bermasalah</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar static-top shadow-sm">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars" style="color: var(--text-muted)"></i>
                    </button>
                    <h6 class="mb-0 d-none d-sm-block" style="color: var(--text-muted); font-weight: 600; font-size: 0.85rem;">
                        @yield('page-title', 'Dashboard')
                    </h6>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item d-none d-sm-block">
                            <span class="nav-link" style="color: var(--text-dim); font-size: 0.78rem;">
                                <i class="fas fa-database mr-1"></i>{{ number_format(\App\Models\RollItem::count()) }} rolls
                            </span>
                        </li>
                    </ul>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid tw-pb-6">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>

            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span style="color: var(--text-dim); font-size: 0.72rem;">&copy; {{ date('Y') }} Roll Off Management</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
