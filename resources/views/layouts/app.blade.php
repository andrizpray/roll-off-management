<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Roll Off Management') — Roll Off Management</title>

    <!-- Font Awesome -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- Chart.js -->
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <style>
        /* Dark theme overrides for SB Admin 2 */
        body { background-color: #0d1117; color: #c9d1d9; }
        #content-wrapper { background-color: #0d1117; }
        .bg-white, .card, .topbar, footer.sticky-footer { background-color: #161b22 !important; }
        .text-gray-800 { color: #c9d1d9 !important; }
        .text-gray-900 { color: #f0f6fc !important; }
        .text-gray-600 { color: #8b949e !important; }
        .text-gray-500 { color: #8b949e !important; }
        .text-gray-300 { color: #c9d1d9 !important; }
        .text-gray-400 { color: #8b949e !important; }
        .text-gray-100 { color: #f0f6fc !important; }
        .border-bottom { border-color: #30363d !important; }
        .table-bordered { border-color: #30363d !important; }
        .table-bordered td, .table-bordered th { border-color: #30363d !important; }
        .table thead th { background-color: #1c2128; color: #c9d1d9; border-bottom: 2px solid #30363d; }
        .table td { color: #c9d1d9; border-top: 1px solid #21262d; }
        .table-hover tbody tr:hover { background-color: #1c2128; }
        .form-control, .custom-select {
            background-color: #0d1117; color: #c9d1d9; border-color: #30363d;
        }
        .form-control:focus, .custom-select:focus {
            background-color: #0d1117; color: #c9d1d9; border-color: #58a6ff; box-shadow: 0 0 0 0.2rem rgba(88,166,255,0.25);
        }
        .form-control::placeholder { color: #484f58; }
        .input-group-append .btn { border-color: #30363d; }
        .card-header { background-color: #1c2128; border-bottom: 1px solid #30363d; }
        .card-body { background-color: #161b22; }
        .dropdown-menu { background-color: #1c2128; border-color: #30363d; }
        .dropdown-item { color: #c9d1d9; }
        .dropdown-item:hover { background-color: #21262d; }
        .page-link { background-color: #161b22; color: #58a6ff; border-color: #30363d; }
        .page-link:hover { background-color: #1c2128; color: #79c0ff; }
        .page-item.active .page-link { background-color: #238636; border-color: #238636; color: #fff; }
        .page-item.disabled .page-link { background-color: #161b22; color: #484f58; }
        .badge { font-size: 0.75rem; }
        .btn-primary { background-color: #238636; border-color: #238636; }
        .btn-primary:hover { background-color: #2ea043; }
        .btn-outline-primary { color: #58a6ff; border-color: #30363d; }
        .btn-outline-primary:hover { background-color: #1c2128; color: #79c0ff; }
        .border-left-primary { border-left: 0.25rem solid #58a6ff !important; }
        .border-left-success { border-left: 0.25rem solid #238636 !important; }
        .border-left-info { border-left: 0.25rem solid #39d2c0 !important; }
        .border-left-warning { border-left: 0.25rem solid #d29922 !important; }
        .border-left-danger { border-left: 0.25rem solid #f85149 !important; }
        .text-primary { color: #58a6ff !important; }
        .text-success { color: #238636 !important; }
        .text-info { color: #39d2c0 !important; }
        .text-warning { color: #d29922 !important; }
        .text-danger { color: #f85149 !important; }
        .bg-light { background-color: #1c2128 !important; }
        .shadow { box-shadow: 0 0.15rem 1.75rem 0 rgba(0,0,0,0.3) !important; }
        /* Sidebar already dark by default */
        .topbar { background-color: #161b22 !important; }
        footer.sticky-footer { border-top: 1px solid #30363d; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); }
        .sidebar .nav-link:hover { color: #fff; }
        .sidebar .nav-link.active { color: #fff; }
        .sidebar-heading { color: rgba(255,255,255,0.4); }
        .text-xs { font-size: 0.75rem; }
        .text-uppercase { text-transform: uppercase; }
        .font-weight-bold { font-weight: 700 !important; }
        /* Status badges */
        .badge-good { background-color: #238636; }
        .badge-hold { background-color: #d29922; }
        .badge-problem { background-color: #f85149; }
        .badge-na { background-color: #484f58; }
    </style>
    @stack('styles')
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Roll Off</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Dashboard -->
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Inventory</div>

            <!-- Roll Items -->
            <li class="nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('items.index') }}">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Roll Items</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Quality</div>

            <!-- Defect Items -->
            <li class="nav-item {{ request()->routeIs('defects.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('defects.index') }}">
                    <i class="fas fa-fw fa-exclamation-triangle"></i>
                    <span>Barang Bermasalah</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Title -->
                    <h5 class="mb-0 text-gray-300 d-none d-sm-block">
                        @yield('page-title', 'Dashboard')
                    </h5>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <span class="nav-link text-gray-500">
                                <i class="fas fa-database mr-1"></i>
                                {{ number_format(App\Models\RollItem::count()) }} rolls
                            </span>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span class="text-gray-500">&copy; {{ date('Y') }} Roll Off Management</span>
                    </div>
                </div>
            </footer>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="#">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JS -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JS -->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- SB Admin 2 JS -->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
