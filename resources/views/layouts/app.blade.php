<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Roll Off Management')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Prevent flash of wrong theme -->
    <script>
        (function() {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px; font-size: 0.84rem; font-weight: 500;
            transition: all 0.2s; cursor: pointer; border: none; text-decoration: none;
        }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; box-shadow: 0 2px 8px rgba(59,130,246,0.3); }
        .btn-ghost { background: #fff; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-ghost:hover { color: #1e293b; background: #f8fafc; border-color: #cbd5e1; }
        .btn-delete { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 10px; }
        .btn-delete:hover { background: #fee2e2; }

        /* Topbar */
        .topbar { background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid #e2e8f0; }

        /* Footer */
        .footer-bar { border-top: 1px solid #e2e8f0; }

        /* Modal */
        .modal-card { background: #fff; border: 1px solid #e2e8f0; }

        /* Alerts */
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

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

        /* Theme toggle */
        #themeToggle:hover { background: rgba(148,163,184,0.1); }
        #moonIcon { color: #6b7280; }
        #sunIcon { color: #fbbf24; display: none; }
        html.dark #moonIcon { display: none; }
        html.dark #sunIcon { display: inline; }

        /* Theme transition */
        body, .card, .mobile-card, .input-field, .select-field, .info-box,
        .topbar, .footer-bar, .page-btn, .btn-ghost, .modal-card, .alert-success, .alert-error,
        .data-table thead th, .data-table tbody td {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.15s ease, box-shadow 0.25s ease;
        }

        /* ============================================= */
        /* ===== DARK MODE ===== */
        /* ============================================= */
        html.dark { color-scheme: dark; }
        html.dark body { background: #0f172a; color: #e2e8f0; }

        /* Cards */
        html.dark .card { background: #1e293b; border-color: #334155; }
        html.dark .mobile-card { background: #1e293b; border-color: #334155; }
        html.dark .mobile-card:hover { border-color: #60a5fa; box-shadow: 0 4px 12px rgba(59,130,246,0.15); }
        html.dark .stat-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.3); }

        /* Table */
        html.dark .data-table thead th { background: #0f172a; color: #94a3b8; border-color: #334155; }
        html.dark .data-table tbody tr:hover { background: rgba(59,130,246,0.06); }
        html.dark .data-table tbody td { border-color: #1e293b; color: #cbd5e1; }

        /* Tags */
        html.dark .tag-blue { background: rgba(59,130,246,0.15); color: #60a5fa; }
        html.dark .tag-green { background: rgba(34,197,94,0.15); color: #4ade80; }
        html.dark .tag-red { background: rgba(239,68,68,0.15); color: #f87171; }
        html.dark .tag-yellow { background: rgba(234,179,8,0.15); color: #fbbf24; }
        html.dark .tag-purple { background: rgba(139,92,246,0.15); color: #a78bfa; }
        html.dark .tag-gray { background: #334155; color: #94a3b8; }
        html.dark .tag-teal { background: rgba(20,184,166,0.15); color: #2dd4bf; }

        /* Inputs */
        html.dark .input-field { background: #1e293b; border-color: #334155; color: #e2e8f0; }
        html.dark .input-field:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(96,165,250,0.15); }
        html.dark .input-field::placeholder { color: #64748b; }
        html.dark .select-field { background: #1e293b; border-color: #334155; color: #e2e8f0; }
        html.dark .select-field:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(96,165,250,0.15); }

        /* Buttons */
        html.dark .btn-ghost { background: #1e293b; color: #94a3b8; border-color: #334155; }
        html.dark .btn-ghost:hover { color: #e2e8f0; background: #334155; border-color: #475569; }
        html.dark .btn-delete { background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.3); }
        html.dark .btn-delete:hover { background: rgba(239,68,68,0.25); }

        /* Pagination */
        html.dark .page-btn { color: #94a3b8; background: #1e293b; border-color: #334155; }
        html.dark .page-btn:hover { color: #60a5fa; background: #334155; border-color: #60a5fa; }
        html.dark .page-btn.active { color: #fff; background: #3b82f6; border-color: #3b82f6; }
        html.dark .page-btn.disabled { color: #475569; }

        /* Info box */
        html.dark .info-box { background: #1e293b; border-color: #334155; }

        /* Timeline */
        html.dark .timeline::before { background: #334155; }
        html.dark .timeline-item::before { border-color: #1e293b; box-shadow: 0 0 0 2px #334155; }
        html.dark .timeline-item.filled::before { box-shadow: 0 0 0 2px rgba(34,197,94,0.3); }

        /* Topbar / Footer / Modal */
        html.dark .topbar { background: rgba(15,23,42,0.8); border-bottom-color: #334155; }
        html.dark .footer-bar { border-top-color: #334155; }
        html.dark .modal-card { background: #1e293b; border-color: #334155; }

        /* Alerts */
        html.dark .alert-success { background: rgba(34,197,94,0.1); border-color: rgba(34,197,94,0.3); color: #4ade80; }
        html.dark .alert-error { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #f87171; }

        /* Notifications panel */
        html.dark #notifPanel { background: #1e293b; border-color: #334155; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        html.dark #notifPanel .bg-white { background: #1e293b !important; }

        /* Overlay */
        html.dark .sidebar-overlay { background: rgba(0,0,0,0.6); }

        /* Scrollbar */
        html.dark ::-webkit-scrollbar-thumb { background: #475569; }
        html.dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* ===== Dark: Tailwind utility overrides ===== */
        /* Text colors */
        html.dark .text-gray-900 { color: #f1f5f9; }
        html.dark .text-gray-800 { color: #e2e8f0; }
        html.dark .text-gray-700 { color: #cbd5e1; }
        html.dark .text-gray-600 { color: #94a3b8; }
        html.dark .text-gray-500 { color: #94a3b8; }
        html.dark .text-gray-400 { color: #64748b; }

        /* Background colors */
        html.dark .bg-white { background-color: #1e293b !important; }
        html.dark .bg-gray-50 { background-color: #1e293b !important; }
        html.dark .bg-gray-100 { background-color: #334155 !important; }
        html.dark .bg-blue-50 { background-color: rgba(59,130,246,0.15) !important; }
        html.dark .bg-red-50 { background-color: rgba(239,68,68,0.15) !important; }
        html.dark .bg-green-50 { background-color: rgba(34,197,94,0.15) !important; }
        html.dark .bg-teal-50 { background-color: rgba(20,184,166,0.15) !important; }
        html.dark .bg-purple-50 { background-color: rgba(139,92,246,0.15) !important; }
        html.dark .bg-amber-50 { background-color: rgba(234,179,8,0.15) !important; }

        /* Border colors */
        html.dark .border-gray-200 { border-color: #334155 !important; }
        html.dark .border-gray-100 { border-color: #334155 !important; }

        /* Hover backgrounds */
        html.dark .hover\:bg-gray-50:hover { background-color: #1e293b !important; }
        html.dark .hover\:bg-gray-100:hover { background-color: #334155 !important; }

        /* Print: always force light mode */
        @media print {
            html { color-scheme: light !important; }
            body { background: #fff !important; color: #1e293b !important; }
            .sidebar, .sidebar-overlay, header, footer, .btn, button[onclick*="toggleSidebar"], button[onclick*="toggleTheme"], #themeToggle { display: none !important; }
            main { padding: 0 !important; }
            html.dark .card, html.dark .mobile-card { background: #fff !important; border-color: #ddd !important; box-shadow: none !important; page-break-inside: avoid; }
            html.dark .data-table thead th { background: #f8fafc !important; color: #64748b !important; border-color: #e2e8f0 !important; }
            html.dark .data-table tbody td { color: #1e293b !important; border-color: #f1f5f9 !important; }
            html.dark .data-table tbody tr:hover { background: transparent !important; }
            html.dark .tag { border: 1px solid #ccc !important; background: #f9f9f9 !important; color: #374151 !important; }
            html.dark .info-box { background: #f8fafc !important; border-color: #e2e8f0 !important; }
            html.dark .input-field, html.dark .select-field { background: #fff !important; border-color: #e2e8f0 !important; color: #1e293b !important; }
            html.dark .text-gray-900, html.dark .text-gray-800, html.dark .text-gray-700 { color: #1e293b !important; }
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
                <a href="{{ route('items.import') }}" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-file-import"></i></span>
                    <span class="nav-label">Smart Sync Import</span>
                </a>
                <div class="px-5 mt-4 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider text-white/30">Quality</span>
                </div>
                <a href="{{ route('defects.index') }}" class="nav-item {{ request()->routeIs('defects.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-triangle-exclamation"></i></span>
                    <span class="nav-label">Barang Bermasalah</span>
                </a>
                <div class="px-5 mt-4 mb-2">
                    <span class="nav-label text-[10px] font-semibold uppercase tracking-wider text-white/30">Logistik</span>
                </div>
                <a href="{{ route('delivery.index') }}" class="nav-item {{ request()->routeIs('delivery.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-truck"></i></span>
                    <span class="nav-label">Delivery Order</span>
                    @if(isset($unassignedCount) && $unassignedCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-4 px-1 flex items-center justify-center leading-none">
                            {{ $unassignedCount > 99 ? '99+' : $unassignedCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('mobil.index') }}" class="nav-item {{ request()->routeIs('mobil.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-truck-moving"></i></span>
                    <span class="nav-label">Kendaraan</span>
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
            <header class="topbar sticky top-0 z-30 px-4 lg:px-6 py-3 flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-gray-100 transition">
                    <i class="fas fa-bars text-sm text-gray-500"></i>
                </button>
                <button onclick="toggleSidebar()" class="hidden lg:flex w-9 h-9 rounded-lg items-center justify-center hover:bg-gray-100 transition" title="Toggle sidebar">
                    <i class="fas fa-bars text-sm text-gray-500"></i>
                </button>
                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-1">
                    <div class="hidden sm:flex items-center gap-2 text-xs text-gray-400 mr-2">
                        <i class="fas fa-clock"></i>
                        <span id="liveTime"></span>
                    </div>

                    <!-- Notification Bell -->
                    <div class="relative" id="notifContainer">
                        <button onclick="toggleNotifPanel()" class="w-9 h-9 rounded-lg flex items-center justify-center transition hover:bg-gray-100 relative" title="Notifikasi">
                            <i class="fas fa-bell text-sm text-gray-500"></i>
                            <span id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center leading-none"></span>
                        </button>

                        <!-- Notification Dropdown Panel -->
                        <div id="notifPanel" class="hidden absolute right-0 top-12 w-80 sm:w-96 rounded-xl border border-gray-200 shadow-xl z-50 overflow-hidden">
                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-white">
                                <h3 class="text-sm font-semibold text-gray-800">
                                    <i class="fas fa-bell mr-1.5 text-blue-500"></i>Notifikasi
                                </h3>
                                <span id="notifTotalBadge" class="text-xs text-gray-400"></span>
                            </div>

                            <!-- Body -->
                            <div id="notifBody" class="max-h-80 overflow-y-auto bg-white">
                                <div class="p-6 text-center">
                                    <i class="fas fa-spinner fa-spin text-gray-300 text-lg"></i>
                                    <p class="text-xs text-gray-400 mt-2">Memuat notifikasi...</p>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div id="notifFooter" class="hidden px-4 py-2.5 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                                <button onclick="markAllRead()" class="text-xs text-blue-600 hover:text-blue-700 font-medium" id="markAllReadBtn">
                                    <i class="fas fa-check-double mr-1"></i>Tandai Semua
                                </button>
                                <div class="flex items-center gap-3">
                                    <button onclick="loadNotif()" class="text-xs text-gray-400 hover:text-gray-600 font-medium">
                                        <i class="fas fa-arrows-rotate"></i>
                                    </button>
                                    <a href="/notifications/page" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Lihat Semua <i class="fas fa-arrow-right ml-0.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button onclick="toggleTheme()" id="themeToggle" class="w-9 h-9 rounded-lg flex items-center justify-center transition" title="Toggle tema">
                        <i class="fas fa-moon text-sm" id="moonIcon"></i>
                        <i class="fas fa-sun text-sm" id="sunIcon"></i>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 lg:p-6">
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium animate-in alert-success">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium animate-in alert-error">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>

            <footer class="footer-bar px-6 py-3 text-center">
                <span class="text-xs text-gray-400">&copy; {{ date('Y') }} Roll Off Management</span>
            </footer>
        </div>
    </div>

    <script>
        /* ===== Theme Toggle ===== */
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateChartTheme();
        }

        /* ===== Chart Theme ===== */
        window.charts = window.charts || [];
        function getChartColors() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                text: isDark ? '#94a3b8' : '#64748b',
                grid: isDark ? '#1e293b' : '#f1f5f9',
                doughnutBorder: isDark ? '#1e293b' : '#fff',
            };
        }
        function updateChartTheme() {
            const c = getChartColors();
            Chart.defaults.color = c.text;
            window.charts.forEach(function(chart) {
                if (chart.options && chart.options.scales) {
                    Object.values(chart.options.scales).forEach(function(scale) {
                        if (scale.grid) scale.grid.color = c.grid;
                    });
                }
                if (chart.config.type === 'doughnut' && chart.data.datasets && chart.data.datasets[0]) {
                    chart.data.datasets[0].borderColor = c.doughnutBorder;
                }
                chart.update('none');
            });
        }
        updateChartTheme();

        /* ===== Sidebar ===== */
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
        document.querySelectorAll('.nav-item').forEach(function(item) {
            item.addEventListener('click', function() { if (window.innerWidth < 1024) toggleSidebar(); });
        });

        /* ===== Notifications ===== */
        var notifPanelOpen = false;
        var notifLoaded = false;

        function toggleNotifPanel() {
            var panel = document.getElementById('notifPanel');
            notifPanelOpen = !notifPanelOpen;
            if (notifPanelOpen) {
                panel.classList.remove('hidden');
                if (!notifLoaded) loadNotif();
            } else {
                panel.classList.add('hidden');
            }
        }

        // Close on click outside
        document.addEventListener('click', function(e) {
            var container = document.getElementById('notifContainer');
            if (notifPanelOpen && container && !container.contains(e.target)) {
                notifPanelOpen = false;
                document.getElementById('notifPanel').classList.add('hidden');
            }
        });

        function loadNotif() {
            var body = document.getElementById('notifBody');
            var footer = document.getElementById('notifFooter');
            body.innerHTML = '<div class="p-6 text-center"><i class="fas fa-spinner fa-spin text-gray-300 text-lg"></i><p class="text-xs text-gray-400 mt-2">Memuat notifikasi...</p></div>';

            fetch('/notifications')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    renderNotif(data);
                    notifLoaded = true;
                })
                .catch(function(err) {
                    body.innerHTML = '<div class="p-6 text-center"><i class="fas fa-exclamation-triangle text-red-300 text-lg"></i><p class="text-xs text-gray-400 mt-2">Gagal memuat notifikasi</p></div>';
                });
        }

        function renderNotif(data) {
            var body = document.getElementById('notifBody');
            var badge = document.getElementById('notifBadge');
            var totalBadge = document.getElementById('notifTotalBadge');
            var footer = document.getElementById('notifFooter');

            // Badge count
            var total = data.total_count || 0;
            if (total > 0) {
                badge.textContent = total > 99 ? '99+' : total;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
            totalBadge.textContent = total + ' notifikasi';

            var html = '';

            // No location section
            var nl = data.no_location;
            if (nl && nl.count > 0) {
                html += '<div class="px-4 py-2.5 bg-red-50 border-b border-red-100">';
                html += '<div class="flex items-center justify-between mb-1">';
                html += '<span class="text-xs font-semibold text-red-700"><i class="fas fa-map-pin mr-1"></i>Item Tanpa Lokasi</span>';
                html += '<span class="text-[10px] font-bold bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full">' + nl.count + '</span>';
                html += '</div>';
                html += '<p class="text-[10px] text-red-500">Roll item tanpa lokasi tracking</p>';
                html += '</div>';
                html += buildNotifItems(nl.items, 'no_location');
            }

            // Recent defects section
            var rd = data.recent_defects;
            if (rd && rd.count > 0) {
                if (nl.count > 0) html += '<div class="h-px bg-gray-100"></div>';
                html += '<div class="px-4 py-2.5 bg-amber-50 border-b border-amber-100">';
                html += '<div class="flex items-center justify-between mb-1">';
                html += '<span class="text-xs font-semibold text-amber-700"><i class="fas fa-triangle-exclamation mr-1"></i>Defect Baru (7 hari)</span>';
                html += '<span class="text-[10px] font-bold bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded-full">' + rd.count + '</span>';
                html += '</div>';
                html += '<p class="text-[10px] text-amber-500">Barang bermasalah baru ditambahkan</p>';
                html += '</div>';
                html += buildNotifItems(rd.items, 'recent_defects');
            }

            // Empty state
            if (total === 0) {
                html = '<div class="p-8 text-center">';
                html += '<i class="fas fa-bell-slash text-2xl text-gray-200 mb-2"></i>';
                html += '<p class="text-xs text-gray-400 font-medium">Semua aman!</p>';
                html += '<p class="text-[10px] text-gray-300 mt-0.5">Tidak ada notifikasi baru</p>';
                html += '</div>';
            }

            body.innerHTML = html;
            footer.classList.remove('hidden');
        }

        function buildNotifItems(items, type) {
            var html = '';
            if (!items || items.length === 0) return html;

            items.forEach(function(item) {
                html += '<a href="';
                if (type === 'no_location') {
                    html += '/items/' + item.id;
                } else {
                    html += '/defects';
                }
                html += '" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition border-b border-gray-50 text-decoration-none">';
                html += '<div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ';
                if (type === 'no_location') {
                    html += 'bg-red-100"><i class="fas fa-map-pin text-red-400 text-xs"></i>';
                } else {
                    html += 'bg-amber-100"><i class="fas fa-triangle-exclamation text-amber-400 text-xs"></i>';
                }
                html += '</div>';
                html += '<div class="flex-1 min-w-0">';
                html += '<p class="text-xs font-medium text-gray-800 truncate">' + item.lot_id;
                if (item.paper_type) html += ' · ' + item.paper_type;
                html += '</p>';
                if (type === 'no_location') {
                    html += '<p class="text-[10px] text-gray-400 truncate">';
                    if (item.gsm) html += item.gsm;
                    if (item.width) html += (item.gsm ? ' · ' : '') + item.width + 'mm';
                    if (!item.gsm && !item.width) html += 'Belum ada lokasi tracking';
                    html += '</p>';
                } else {
                    html += '<p class="text-[10px] text-gray-400 truncate">';
                    if (item.reason) html += item.reason;
                    else html += item.gsm || '-';
                    html += '</p>';
                }
                html += '</div>';
                html += '<span class="text-[10px] text-gray-300 flex-shrink-0">' + formatNotifDate(item.created_at) + '</span>';
                html += '</a>';
            });

            return html;
        }

        function formatNotifDate(dateStr) {
            var d = new Date(dateStr);
            var now = new Date();
            var diff = now - d;
            var mins = Math.floor(diff / 60000);
            var hours = Math.floor(diff / 3600000);
            var days = Math.floor(diff / 86400000);
            if (mins < 1) return 'Baru';
            if (mins < 60) return mins + 'm';
            if (hours < 24) return hours + 'j';
            if (days < 7) return days + 'h';
            return d.getDate() + '/' + (d.getMonth() + 1);
        }

        /* ===== Mark as Read ===== */
        function markAllRead() {
            var btn = document.getElementById('markAllReadBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menandai...';

            fetch('/notifications/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ type: 'all' })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderNotif(data);
                notifLoaded = true;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i>Sudah Dibaca';
                btn.classList.remove('text-blue-600', 'hover:text-blue-700');
                btn.classList.add('text-green-600');
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double mr-1"></i>Tandai Semua';
            });
        }

        function markSingleRead(type, refId) {
            fetch('/notifications/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ type: type, reference_id: refId })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderNotif(data);
                notifLoaded = true;
            })
            .catch(function() {});
        }

        // Auto-load badge count on page load
        fetch('/notifications')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var badge = document.getElementById('notifBadge');
                var total = data.total_count || 0;
                if (total > 0) {
                    badge.textContent = total > 99 ? '99+' : total;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                }
                notifLoaded = true;
                // Pre-render body so it's ready when panel opens
                renderNotif(data);
            })
            .catch(function() {});
    </script>
    @stack('scripts')
</body>
</html>
