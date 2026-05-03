<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Roll Off Management')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, #1e3a5f 0%, #1e40af 50%, #1d4ed8 100%);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), width 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar.collapsed { transform: translateX(-100%); }
        @media (min-width: 1024px) {
            .sidebar.collapsed { transform: translateX(0); width: 72px; }
            .sidebar.collapsed .nav-label, .sidebar.collapsed .sidebar-title, .sidebar.collapsed .sidebar-subtitle { display: none; }
            .sidebar.collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
            .sidebar.collapsed .nav-icon { margin-right: 0; }
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px; margin: 2px 8px;
            color: rgba(255,255,255,0.7); font-size: 0.85rem; font-weight: 500;
            transition: all 0.2s ease; cursor: pointer; text-decoration: none;
        }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.12); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.18); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .nav-icon { width: 20px; text-align: center; font-size: 0.95rem; flex-shrink: 0; }

        /* Card */
        .card {
            background: #fff; border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* Stat card */
        .stat-card { position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            border-radius: 14px 14px 0 0;
        }
        .stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stat-card.red::before { background: linear-gradient(90deg, #ef4444, #f87171); }
        .stat-card.green::before { background: linear-gradient(90deg, #22c55e, #4ade80); }
        .stat-card.teal::before { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .stat-card.amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

        /* Table */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table thead th {
            background: #f8fafc; color: #64748b;
            font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
            position: sticky; top: 0; z-index: 2;
        }
        .data-table tbody tr { transition: background 0.15s ease; cursor: pointer; }
        .data-table tbody tr:hover { background: #eff6ff; }
        .data-table tbody td {
            padding: 10px 12px; border-bottom: 1px solid #f1f5f9;
            font-size: 0.82rem; color: #334155; vertical-align: middle;
        }

        /* Mobile card */
        .mobile-card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 12px; padding: 14px; margin-bottom: 8px;
            transition: border-color 0.2s, transform 0.15s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .mobile-card:hover { border-color: #93c5fd; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.08); }

        /* Tags */
        .tag {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.7rem; font-weight: 600; padding: 3px 8px;
            border-radius: 6px; white-space: nowrap;
        }
        .tag-blue { background: #eff6ff; color: #2563eb; }
        .tag-green { background: #f0fdf4; color: #16a34a; }
        .tag-red { background: #fef2f2; color: #dc2626; }
        .tag-yellow { background: #fefce8; color: #ca8a04; }
        .tag-purple { background: #f5f3ff; color: #7c3aed; }
        .tag-gray { background: #f1f5f9; color: #64748b; }
        .tag-teal { background: #f0fdfa; color: #0d9488; }

        /* Input */
        .input-field {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 8px 14px; color: #1e293b; font-size: 0.84rem;
            transition: border-color 0.2s, box-shadow 0.2s; outline: none; width: 100%;
        }
        .input-field:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .input-field::placeholder { color: #94a3b8; }

        /* Select */
        .select-field {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 8px 14px; color: #1e293b; font-size: 0.84rem;
            transition: border-color 0.2s; outline: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
        }
        .select-field:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

        /* Pagination */
        .page-btn {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; padding: 0 8px;
            border-radius: 8px; font-size: 0.8rem; font-weight: 500;
            color: #64748b; background: #fff; border: 1px solid #e2e8f0;
            transition: all 0.2s; cursor: pointer; text-decoration: none;
        }
        .page-btn:hover { color: #2563eb; background: #eff6ff; border-color: #bfdbfe; }
        .page-btn.active { color: #fff; background: #3b82f6; border-color: #3b82f6; }
        .page-btn.disabled { color: #cbd5e1; cursor: not-allowed; }

        /* Timeline */
        .timeline { position: relative; padding-left: 28px; }
        .timeline::before { content: ''; position: absolute; left: 8px; top: 8px; bottom: 8px; width: 2px; background: #e2e8f0; border-radius: 1px; }
        .timeline-item { position: relative; padding: 8px 0; }
        .timeline-item::before { content: ''; position: absolute; left: -24px; top: 14px; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; border: 2px solid #fff; box-shadow: 0 0 0 2px #e2e8f0; }
        .timeline-item.filled::before { background: #22c55e; box-shadow: 0 0 0 2px #bbf7d0; }

        /* Info box */
        .info-box {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 12px 14px;
        }

        /* Button */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px; font-size: 0.84rem; font-weight: 500;
            transition: all 0.2s; cursor: pointer; border: none; text-decoration: none;
        }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; box-shadow: 0 2px 8px rgba(59,130,246,0.3); }
        .btn-ghost { background: #fff; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-ghost:hover { color: #1e293b; background: #f8fafc; border-color: #cbd5e1; }

        /* Overlay */
        .sidebar-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.3);
            z-index: 40; opacity: 0; pointer-events: none; transition: opacity 0.3s;
        }
        .sidebar-overlay.show { opacity: 1; pointer-events: auto; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeIn 0.4s ease-out; }

        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Print */
        @media print {
            body { background: #fff !important; }
            .sidebar, .sidebar-overlay, header, footer, .btn, button[onclick*="toggleSidebar"] { display: none !important; }
            main { padding: 0 !important; }
            .card { border: 1px solid #ddd !important; box-shadow: none !important; page-break-inside: avoid; }
            .data-table tbody tr:hover { background: transparent !important; }
            .tag { border: 1px solid #ccc !important; background: #f9f9f9 !important; }
            .animate-in { animation: none !important; }
            @page { margin: 15mm; }
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased">

    <div class="sidebar-overlay lg:hidden" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="sidebar fixed lg:sticky top-0 left-0 h-screen w-64 z-50 flex flex-col collapsed lg:transform-none"
               id="sidebar">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-5 no-underline">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-white/20">
                    <i class="fas fa-boxes-stacked text-white text-sm"></i>
                </div>
                <div class="sidebar-title">
                    <div class="text-white font-bold text-sm leading-tight">Roll Off</div>
                    <div class="sidebar-subtitle text-xs text-white/50">Management</div>
                </div>
            </a>
            <div class="px-5 mb-2"><div class="h-px bg-white/10"></div></div>

            <nav class="flex-1 overflow-y-auto py-1">
                <div class="px-5 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider text-white/30">Menu</span>
                </div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <div class="px-5 mt-4 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider text-white/30">Inventory</span>
                </div>
                <a href="{{ route('items.index') }}" class="nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-box"></i></span>
                    <span class="nav-label">Roll Items</span>
                </a>
                <div class="px-5 mt-4 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider text-white/30">Quality</span>
                </div>
                <a href="{{ route('defects.index') }}" class="nav-item {{ request()->routeIs('defects.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-triangle-exclamation"></i></span>
                    <span class="nav-label">Barang Bermasalah</span>
                </a>
            </nav>

            <div class="px-5 py-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
                <div class="sidebar-title flex items-center gap-2 text-xs text-white/40">
                    <i class="fas fa-database"></i>
                    <span>{{ number_format(\App\Models\RollItem::count()) }} rolls</span>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">

            <!-- Topbar -->
            <header class="sticky top-0 z-30 px-4 lg:px-6 py-3 flex items-center gap-3 bg-white/80 backdrop-blur-lg" style="border-bottom: 1px solid #e2e8f0;">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-gray-100 transition">
                    <i class="fas fa-bars text-sm text-gray-500"></i>
                </button>
                <button onclick="toggleSidebar()" class="hidden lg:flex w-9 h-9 rounded-lg items-center justify-center hover:bg-gray-100 transition" title="Toggle sidebar">
                    <i class="fas fa-bars text-sm text-gray-500"></i>
                </button>
                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-400">
                    <i class="fas fa-clock"></i>
                    <span id="liveTime"></span>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 lg:p-6">
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium animate-in" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a;">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium animate-in" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>

            <footer class="px-6 py-3 text-center" style="border-top: 1px solid #e2e8f0;">
                <span class="text-xs text-gray-400">&copy; {{ date('Y') }} Roll Off Management</span>
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
        function updateClock() {
            document.getElementById('liveTime').textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        updateClock(); setInterval(updateClock, 30000);
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => { if (window.innerWidth < 1024) toggleSidebar(); });
        });
    </script>
    @stack('scripts')
</body>
</html>
