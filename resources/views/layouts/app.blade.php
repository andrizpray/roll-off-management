<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Roll Off Management')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        surface: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 700: '#1e293b', 800: '#161b22', 850: '#131920', 900: '#0d1117', 950: '#090c10' },
                        brand: { 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb' },
                    }
                }
            }
        }
    </script>

    <style>
        /* Smooth scrolling */
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #0d1117; color: #e6edf3; }

        /* Sidebar transition */
        .sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar.collapsed { transform: translateX(-100%); }
        @media (min-width: 1024px) {
            .sidebar.collapsed { transform: translateX(0); width: 72px; }
            .sidebar.collapsed .nav-label, .sidebar.collapsed .sidebar-title, .sidebar.collapsed .sidebar-subtitle { display: none; }
            .sidebar.collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
            .sidebar.collapsed .nav-icon { margin-right: 0; }
        }

        /* Nav item */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px; margin: 2px 8px;
            color: rgba(255,255,255,0.55); font-size: 0.85rem; font-weight: 500;
            transition: all 0.2s ease; cursor: pointer; text-decoration: none;
        }
        .nav-item:hover { color: rgba(255,255,255,0.9); background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(59,130,246,0.15); }
        .nav-item.active .nav-icon { color: #60a5fa; }
        .nav-icon { width: 20px; text-align: center; font-size: 0.95rem; flex-shrink: 0; }

        /* Glass card */
        .glass {
            background: rgba(22, 27, 34, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px;
        }

        /* Stat card glow */
        .stat-card { position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            border-radius: 14px 14px 0 0;
        }
        .stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stat-card.red::before { background: linear-gradient(90deg, #ef4444, #f87171); }
        .stat-card.green::before { background: linear-gradient(90deg, #22c55e, #4ade80); }
        .stat-card.teal::before { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

        /* Modern table */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table thead th {
            background: rgba(30, 41, 59, 0.8); color: #8b949e;
            font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.06);
            position: sticky; top: 0; z-index: 2;
        }
        .data-table thead th:first-child { border-radius: 10px 0 0 0; }
        .data-table thead th:last-child { border-radius: 0 10px 0 0; }
        .data-table tbody tr {
            transition: background 0.15s ease; cursor: pointer;
        }
        .data-table tbody tr:hover { background: rgba(59, 130, 246, 0.04); }
        .data-table tbody td {
            padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.04);
            font-size: 0.82rem; color: #c9d1d9; vertical-align: middle;
        }

        /* Mobile card */
        .mobile-card {
            background: rgba(22, 27, 34, 0.8);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px; padding: 14px; margin-bottom: 8px;
            transition: border-color 0.2s, transform 0.15s;
        }
        .mobile-card:hover { border-color: rgba(59, 130, 246, 0.3); transform: translateY(-1px); }

        /* Badge */
        .tag {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.7rem; font-weight: 600; padding: 3px 8px;
            border-radius: 6px; white-space: nowrap;
        }
        .tag-blue { background: rgba(59,130,246,0.12); color: #60a5fa; }
        .tag-green { background: rgba(34,197,94,0.12); color: #4ade80; }
        .tag-red { background: rgba(239,68,68,0.12); color: #f87171; }
        .tag-yellow { background: rgba(234,179,8,0.12); color: #facc15; }
        .tag-purple { background: rgba(139,92,246,0.12); color: #a78bfa; }
        .tag-gray { background: rgba(148,163,184,0.1); color: #94a3b8; }
        .tag-teal { background: rgba(20,184,166,0.12); color: #2dd4bf; }

        /* Input */
        .input-field {
            background: rgba(13, 17, 23, 0.8); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 8px 14px; color: #e6edf3; font-size: 0.84rem;
            transition: border-color 0.2s, box-shadow 0.2s; outline: none; width: 100%;
        }
        .input-field:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .input-field::placeholder { color: #484f58; }

        /* Select */
        .select-field {
            background: rgba(13, 17, 23, 0.8); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 8px 14px; color: #e6edf3; font-size: 0.84rem;
            transition: border-color 0.2s; outline: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238b949e' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
        }
        .select-field:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }

        /* Pagination */
        .page-btn {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; padding: 0 8px;
            border-radius: 8px; font-size: 0.8rem; font-weight: 500;
            color: #8b949e; background: transparent; border: 1px solid transparent;
            transition: all 0.2s; cursor: pointer; text-decoration: none;
        }
        .page-btn:hover { color: #e6edf3; background: rgba(255,255,255,0.06); }
        .page-btn.active { color: #fff; background: #3b82f6; border-color: #3b82f6; }
        .page-btn.disabled { color: #30363d; cursor: not-allowed; }

        /* Timeline */
        .timeline { position: relative; padding-left: 28px; }
        .timeline::before { content: ''; position: absolute; left: 8px; top: 8px; bottom: 8px; width: 2px; background: rgba(255,255,255,0.06); border-radius: 1px; }
        .timeline-item { position: relative; padding: 8px 0; }
        .timeline-item::before { content: ''; position: absolute; left: -24px; top: 14px; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; border: 2px solid #0d1117; }
        .timeline-item.filled::before { background: #22c55e; }

        /* Info cell */
        .info-box {
            background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255,255,255,0.04);
            border-radius: 10px; padding: 12px 14px;
        }

        /* Button */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px; font-size: 0.84rem; font-weight: 500;
            transition: all 0.2s; cursor: pointer; border: none; text-decoration: none;
        }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; }
        .btn-ghost { background: transparent; color: #8b949e; border: 1px solid rgba(255,255,255,0.1); }
        .btn-ghost:hover { color: #e6edf3; background: rgba(255,255,255,0.06); }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 40; opacity: 0; pointer-events: none; transition: opacity 0.3s;
        }
        .sidebar-overlay.show { opacity: 1; pointer-events: auto; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #30363d; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #484f58; }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeIn 0.4s ease-out; }

        /* Truncate */
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
    @stack('styles')
</head>
<body class="antialiased">

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay lg:hidden" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="sidebar fixed lg:sticky top-0 left-0 h-screen w-64 z-50 flex flex-col collapsed lg:transform-none"
               id="sidebar"
               style="background: linear-gradient(180deg, #111827 0%, #0d1117 100%); border-right: 1px solid rgba(255,255,255,0.06);">
            
            <!-- Brand -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-5 no-underline">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
                    <i class="fas fa-boxes-stacked text-white text-sm"></i>
                </div>
                <div class="sidebar-title">
                    <div class="text-white font-bold text-sm leading-tight">Roll Off</div>
                    <div class="sidebar-subtitle text-xs" style="color: #484f58;">Management</div>
                </div>
            </a>

            <div class="px-5 mb-2"><div style="height: 1px; background: rgba(255,255,255,0.06);"></div></div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-1">
                <div class="px-5 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider" style="color: #30363d;">Menu</span>
                </div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="nav-label">Dashboard</span>
                </a>

                <div class="px-5 mt-4 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider" style="color: #30363d;">Inventory</span>
                </div>
                <a href="{{ route('items.index') }}" class="nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-box"></i></span>
                    <span class="nav-label">Roll Items</span>
                </a>

                <div class="px-5 mt-4 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider" style="color: #30363d;">Quality</span>
                </div>
                <a href="{{ route('defects.index') }}" class="nav-item {{ request()->routeIs('defects.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-triangle-exclamation"></i></span>
                    <span class="nav-label">Barang Bermasalah</span>
                </a>
            </nav>

            <!-- Sidebar footer -->
            <div class="px-5 py-4" style="border-top: 1px solid rgba(255,255,255,0.06);">
                <div class="sidebar-title flex items-center gap-2 text-xs" style="color: #30363d;">
                    <i class="fas fa-database"></i>
                    <span>{{ number_format(\App\Models\RollItem::count()) }} rolls</span>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">

            <!-- Topbar -->
            <header class="sticky top-0 z-30 px-4 lg:px-6 py-3 flex items-center gap-3"
                    style="background: rgba(13,17,23,0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.04);">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-white/5 transition">
                    <i class="fas fa-bars text-sm" style="color: #8b949e;"></i>
                </button>
                <button onclick="toggleSidebar()" class="hidden lg:flex w-9 h-9 rounded-lg items-center justify-center hover:bg-white/5 transition" title="Toggle sidebar">
                    <i class="fas fa-bars text-sm" style="color: #8b949e;"></i>
                </button>
                <div class="flex-1">
                    <h1 class="text-sm font-semibold" style="color: #e6edf3;">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-xs" style="color: #484f58;">
                    <i class="fas fa-clock"></i>
                    <span id="liveTime"></span>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 lg:p-6">
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium animate-in"
                         style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80;">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium animate-in"
                         style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171;">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="px-6 py-3 text-center" style="border-top: 1px solid rgba(255,255,255,0.04);">
                <span class="text-xs" style="color: #30363d;">&copy; {{ date('Y') }} Roll Off Management</span>
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            const ov = document.getElementById('sidebarOverlay');
            sb.classList.toggle('collapsed');
            ov.classList.toggle('show');
        }

        // Live clock
        function updateClock() {
            const now = new Date();
            document.getElementById('liveTime').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 30000);

        // Close sidebar on nav (mobile)
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 1024) toggleSidebar();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
