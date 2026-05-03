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

    <style>
        /* ===== DARK THEME ===== */
        :root {
            --bg-primary: #0d1117;
            --bg-card: #161b22;
            --bg-card-header: #1c2128;
            --bg-input: #0d1117;
            --border: #30363d;
            --text-primary: #f0f6fc;
            --text-secondary: #c9d1d9;
            --text-muted: #8b949e;
            --text-dim: #484f58;
            --accent-blue: #58a6ff;
            --accent-green: #238636;
            --accent-teal: #39d2c0;
            --accent-yellow: #d29922;
            --accent-red: #f85149;
            --accent-purple: #bc8cff;
            --accent-orange: #ff7b72;
            --hover-bg: #1c2128;
        }
        * { box-sizing: border-box; }
        body { background-color: var(--bg-primary); color: var(--text-secondary); font-family: 'Nunito', sans-serif; }
        #content-wrapper { background-color: var(--bg-primary); }

        /* Cards */
        .card { background-color: var(--bg-card) !important; border: 1px solid var(--border) !important; border-radius: 8px !important; }
        .card-header { background-color: var(--bg-card-header) !important; border-bottom: 1px solid var(--border) !important; border-radius: 8px 8px 0 0 !important; }
        .card-body { background-color: var(--bg-card) !important; }

        /* Text */
        .text-gray-100, .text-gray-800 { color: var(--text-primary) !important; }
        .text-gray-900 { color: var(--text-primary) !important; }
        .text-gray-600, .text-gray-500 { color: var(--text-muted) !important; }
        .text-gray-400 { color: var(--text-muted) !important; }
        .text-gray-300 { color: var(--text-secondary) !important; }
        .text-primary { color: var(--accent-blue) !important; }
        .text-success { color: var(--accent-green) !important; }
        .text-info { color: var(--accent-teal) !important; }
        .text-warning { color: var(--accent-yellow) !important; }
        .text-danger { color: var(--accent-red) !important; }

        /* Borders */
        .border-bottom, .border-top { border-color: var(--border) !important; }

        /* Topbar */
        .topbar { background-color: var(--bg-card) !important; border-bottom: 1px solid var(--border) !important; }

        /* Footer */
        footer.sticky-footer { background-color: var(--bg-card) !important; border-top: 1px solid var(--border); }

        /* Sidebar */
        .sidebar .nav-link { color: rgba(255,255,255,0.75); transition: all 0.2s; }
        .sidebar .nav-link:hover { color: #fff; background-color: rgba(255,255,255,0.08); }
        .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.12); }
        .sidebar-heading { color: rgba(255,255,255,0.4); }

        /* Tables */
        .table-bordered { border-color: var(--border) !important; }
        .table-bordered td, .table-bordered th { border-color: var(--border) !important; }
        .table thead th { background-color: var(--bg-card-header); color: var(--text-secondary); border-bottom: 2px solid var(--border) !important; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { color: var(--text-secondary); border-top: 1px solid #21262d; vertical-align: middle; font-size: 0.875rem; }
        .table-hover tbody tr { transition: background-color 0.15s; cursor: pointer; }
        .table-hover tbody tr:hover { background-color: var(--hover-bg); }

        /* Forms */
        .form-control, .custom-select, .form-control-sm {
            background-color: var(--bg-input); color: var(--text-secondary); border: 1px solid var(--border);
            border-radius: 6px; font-size: 0.85rem; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .custom-select:focus {
            background-color: var(--bg-input); color: var(--text-secondary);
            border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(88,166,255,0.15);
        }
        .form-control::placeholder { color: var(--text-dim); }
        .form-group label, label { color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Buttons */
        .btn-primary { background-color: var(--accent-green); border-color: var(--accent-green); }
        .btn-primary:hover { background-color: #2ea043; }
        .btn-outline-primary { color: var(--accent-blue); border-color: var(--border); }
        .btn-outline-primary:hover { background-color: var(--hover-bg); color: var(--accent-blue); border-color: var(--accent-blue); }
        .btn-outline-secondary { color: var(--text-muted); border-color: var(--border); }
        .btn-outline-secondary:hover { background-color: var(--hover-bg); }

        /* Pagination */
        .page-link { background-color: var(--bg-card); color: var(--accent-blue); border: 1px solid var(--border); border-radius: 4px; margin: 0 2px; }
        .page-link:hover { background-color: var(--hover-bg); border-color: var(--accent-blue); }
        .page-item.active .page-link { background-color: var(--accent-green); border-color: var(--accent-green); }
        .page-item.disabled .page-link { color: var(--text-dim); background-color: var(--bg-card); }

        /* Border-left cards */
        .border-left-primary { border-left: 0.25rem solid var(--accent-blue) !important; }
        .border-left-success { border-left: 0.25rem solid var(--accent-green) !important; }
        .border-left-info { border-left: 0.25rem solid var(--accent-teal) !important; }
        .border-left-warning { border-left: 0.25rem solid var(--accent-yellow) !important; }
        .border-left-danger { border-left: 0.25rem solid var(--accent-red) !important; }

        /* Badges */
        .bg-light { background-color: var(--bg-card-header) !important; }
        .shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important; }
        .badge { font-size: 0.7rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        .badge-good { background-color: var(--accent-green); color: #fff; }
        .badge-hold { background-color: var(--accent-yellow); color: #000; }
        .badge-problem { background-color: var(--accent-red); color: #fff; }
        .badge-na { background-color: #484f58; color: #fff; }
        .badge-loc { background-color: rgba(88,166,255,0.15); color: var(--accent-blue); border: 1px solid rgba(88,166,255,0.3); }
        .badge-so { background-color: rgba(188,140,255,0.15); color: var(--accent-purple); border: 1px solid rgba(188,140,255,0.3); }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .stat-card .card-body .h5 { font-size: 1.2rem !important; }
            .stat-card .card-body .text-xs { font-size: 0.65rem !important; }
            .container-fluid { padding: 0 10px !important; }
            .card-body { padding: 12px !important; }
            .table { font-size: 0.78rem; }
            .table thead th { font-size: 0.7rem; padding: 6px 4px; }
            .table td { padding: 6px 4px; }
            .btn { font-size: 0.78rem; }
            .h3, h1.h3 { font-size: 1.1rem !important; }
            .h5 { font-size: 1rem !important; }
            h6 { font-size: 0.85rem !important; }
        }

        /* ===== MOBILE CARD VIEW FOR TABLES ===== */
        .mobile-cards { display: none; }
        .desktop-table { display: block; }
        @media (max-width: 768px) {
            .desktop-table { display: none; }
            .mobile-cards { display: block; }
        }

        .mobile-item-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 10px;
            transition: border-color 0.2s;
        }
        .mobile-item-card:hover { border-color: var(--accent-blue); }
        .mobile-item-card .lot-id {
            color: var(--accent-blue);
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 8px;
            display: block;
        }
        .mobile-item-card .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 0.8rem;
        }
        .mobile-item-card .info-label { color: var(--text-muted); }
        .mobile-item-card .info-value { color: var(--text-secondary); font-weight: 600; text-align: right; max-width: 60%; }
        .mobile-item-card .loc-tags { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 4px; }
        .mobile-item-card .loc-tag {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            background-color: rgba(88,166,255,0.1);
            color: var(--accent-blue);
            border: 1px solid rgba(88,166,255,0.2);
        }

        /* Filter bar mobile */
        @media (max-width: 768px) {
            .filter-row { flex-direction: column; }
            .filter-row .col-lg-3, .filter-row .col-lg-2, .filter-row .col-md-6, .filter-row .col-md-4 {
                width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important;
                margin-bottom: 8px !important;
            }
        }

        /* Quick search bar */
        .quick-search {
            background-color: var(--bg-card-header);
            border-bottom: 1px solid var(--border);
            padding: 10px 16px;
        }
        .quick-search input {
            background-color: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 0.85rem;
            width: 100%;
            max-width: 400px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .quick-search input:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,166,255,0.15);
        }
        .quick-search input::placeholder { color: var(--text-dim); }

        /* Info grid on show page */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
        @media (max-width: 768px) {
            .info-grid { grid-template-columns: 1fr; }
        }
        .info-cell {
            background-color: var(--bg-card-header);
            border-radius: 6px;
            padding: 12px;
            border: 1px solid var(--border);
        }
        .info-cell .info-label { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-cell .info-value { font-size: 0.95rem; color: var(--text-primary); font-weight: 600; }

        /* Location timeline */
        .loc-timeline { position: relative; padding-left: 20px; }
        .loc-timeline::before { content: ''; position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background-color: var(--border); }
        .loc-timeline-item { position: relative; padding: 8px 0 8px 20px; }
        .loc-timeline-item::before { content: ''; position: absolute; left: -18px; top: 14px; width: 10px; height: 10px; border-radius: 50%; background-color: var(--accent-blue); border: 2px solid var(--bg-card); }
        .loc-timeline-item .loc-period { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; }
        .loc-timeline-item .loc-value { font-size: 0.9rem; color: var(--text-primary); font-weight: 600; margin-top: 2px; }

        /* Chart containers */
        .chart-container { position: relative; width: 100%; }
        .chart-container canvas { max-height: 250px; }
        @media (max-width: 768px) {
            .chart-container canvas { max-height: 200px; }
        }

        /* Alert styling */
        .alert-success { background-color: rgba(35,134,54,0.15); border-color: rgba(35,134,54,0.3); color: #56d364; }
        .alert-danger { background-color: rgba(248,81,73,0.15); border-color: rgba(248,81,73,0.3); color: var(--accent-red); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-primary); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-dim); }

        /* Truncate */
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
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Inventory</div>
            <li class="nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('items.index') }}">
                    <i class="fas fa-fw fa-box"></i><span>Roll Items</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Quality</div>
            <li class="nav-item {{ request()->routeIs('defects.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('defects.index') }}">
                    <i class="fas fa-fw fa-exclamation-triangle"></i><span>Barang Bermasalah</span>
                </a>
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

                    <h6 class="mb-0 d-none d-sm-block" style="color: var(--text-muted); font-weight: 600;">
                        @yield('page-title', 'Dashboard')
                    </h6>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item d-none d-sm-block">
                            <span class="nav-link" style="color: var(--text-dim); font-size: 0.8rem;">
                                <i class="fas fa-database mr-1"></i>{{ number_format(\App\Models\RollItem::count()) }} rolls
                            </span>
                        </li>
                    </ul>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid pb-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>

            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span style="color: var(--text-dim); font-size: 0.75rem;">&copy; {{ date('Y') }} Roll Off Management</span>
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
