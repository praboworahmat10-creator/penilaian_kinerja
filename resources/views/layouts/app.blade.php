<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Penilaian Kinerja Pegawai')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --page: #f1f3ef;
            --surface: #ffffff;
            --surface-muted: #e8eeea;
            --text: #203238;
            --muted: #53686c;
            --line: #d7e0dc;
            --navy: #18343b;
            --navy-raised: #24464b;
            --teal: #287164;
            --teal-hover: #205b52;
            --accent: #bd744a;
        }
        :root[data-theme="dark"] {
            color-scheme: dark;
            --page: #152528;
            --surface: #1d3033;
            --surface-muted: #263b3d;
            --text: #e9efeb;
            --muted: #b6c5c0;
            --line: #3a5050;
            --navy: #102124;
            --navy-raised: #1d393b;
            --teal: #6eb6a5;
            --teal-hover: #8bc7b8;
            --accent: #e4a77e;
        }
        body {
            min-height: 100vh;
            background: var(--page);
            color: var(--text);
            font-family: "Segoe UI", system-ui, sans-serif;
        }
        a, button, select, label { cursor: pointer; }
        button:disabled, select:disabled { cursor: not-allowed; }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 3px solid var(--accent);
            outline-offset: 2px;
        }
        .app-shell { min-height: 100vh; }
        .sidebar {
            min-height: 100vh;
            width: 258px;
            flex: 0 0 258px;
            padding: 26px 16px;
            background: var(--navy);
            color: #f5f7f4;
        }
        .sidebar-brand { padding: 0 10px 24px; }
        .sidebar-brand strong { display: block; font-size: 1.05rem; }
        .sidebar-brand span { color: #b8cbc4; font-size: .75rem; }
        .sidebar-user {
            margin: 0 4px 22px;
            padding: 13px 12px;
            border: 1px solid rgba(255,255,255,.13);
            border-radius: 8px;
            background: rgba(255,255,255,.045);
        }
        .sidebar-user span { display: block; color: #b8cbc4; font-size: .75rem; }
        .sidebar-user strong { display: block; margin-top: 3px; }
        .sidebar-nav { display: grid; gap: 5px; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            min-height: 46px;
            padding: 10px 12px;
            border-radius: 6px;
            color: #e7eeea;
            text-decoration: none;
            transition: background-color .15s ease, color .15s ease;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,.09); color: #fff; }
        .sidebar-nav a.active { background: var(--navy-raised); color: #fff; }
        .sidebar-nav i { width: 20px; text-align: center; }
        .main-content { min-width: 0; }
        .header {
            min-height: 68px;
            padding: 12px 30px;
            border-bottom: 1px solid var(--line);
            background: var(--surface);
            color: var(--text);
        }
        .header-title { font-size: 1rem; font-weight: 650; }
        .header-actions { display: flex; align-items: center; gap: 10px; }
        .theme-toggle {
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--surface);
            color: var(--text);
            padding: 8px 11px;
        }
        .theme-toggle:hover { background: var(--surface-muted); }
        .page-content { max-width: 1600px; margin: 0 auto; }
        .page-footer { max-width: 1600px; margin: 0 auto; border-top: 1px solid var(--line); }
        .app-panel, .stat-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            color: var(--text);
        }
        .stat-card { height: 100%; padding: 19px 20px; }
        .stat-label { color: var(--muted); font-size: .88rem; }
        .stat-value { margin-top: 8px; font-size: 1.8rem; font-weight: 700; line-height: 1.1; }
        .stat-note { margin-top: 8px; color: var(--muted); font-size: .82rem; }
        .dashboard-intro {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            padding: 25px 28px;
            border-radius: 8px;
            background: var(--navy);
            color: #f5f7f4;
        }
        .dashboard-intro p { max-width: 620px; margin: 8px 0 0; color: #c6d6d0; }
        .dashboard-intro h1 { margin: 5px 0 0; font-size: clamp(1.45rem, 3vw, 2rem); }
        .period-label { color: #d4e4dc; font-size: .9rem; }
        .app-table {
            --bs-table-bg: var(--surface);
            --bs-table-color: var(--text);
            --bs-table-border-color: var(--line);
            --bs-table-striped-bg: var(--surface-muted);
            --bs-table-striped-color: var(--text);
            --bs-table-hover-bg: var(--surface-muted);
            --bs-table-hover-color: var(--text);
        }
        .app-table thead th { background: var(--surface-muted); color: var(--text); font-weight: 650; white-space: nowrap; }
        .app-table > tbody > tr.app-total-row > td { background: var(--surface-muted); }
        .app-detail-table { min-width: 900px; table-layout: fixed; }
        .app-detail-table th:nth-child(1) { width: 23%; }
        .app-detail-table th:nth-child(2) { width: 43%; }
        .app-detail-table th:nth-child(3),
        .app-detail-table th:nth-child(4) { width: 10%; }
        .app-detail-table th:nth-child(5) { width: 14%; }
        .app-detail-table td:nth-child(2) { white-space: normal; overflow-wrap: break-word; }
        .app-detail-table td:not(:nth-child(2)) { white-space: nowrap; }
        .assessment-summary { padding: 18px 22px 22px; border-top: 1px solid var(--line); }
        .assessment-meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px 22px; }
        .assessment-meta > div { min-width: 0; }
        .assessment-meta dt { margin-bottom: 5px; color: var(--muted); font-size: .82rem; font-weight: 650; }
        .assessment-meta dd { margin: 0; color: var(--text); overflow-wrap: anywhere; }
        .assessment-meta .assessment-note { grid-column: 1 / -1; padding-top: 13px; border-top: 1px solid var(--line); }
        .form-control, .form-select {
            border-color: var(--line);
            background-color: var(--surface);
            color: var(--text);
        }
        .form-control::placeholder { color: var(--muted); opacity: 1; }
        .btn-primary { border-color: var(--teal); background: var(--teal); }
        .btn-primary:hover, .btn-primary:active { border-color: var(--teal-hover); background: var(--teal-hover); }
        :root[data-theme="dark"] .btn-primary { color: #102124; }
        :root[data-theme="dark"] .btn-primary:hover,
        :root[data-theme="dark"] .btn-primary:active { color: #102124; }
        .btn-outline-secondary { border-color: var(--line); color: var(--text); }
        .btn-outline-secondary:hover { border-color: var(--teal); background: var(--surface-muted); color: var(--text); }
        .text-muted { color: var(--muted) !important; }
        .empty-state { padding: 34px 20px; color: var(--muted); text-align: center; }
        @media (max-width: 767.98px) {
            .app-shell { display: block !important; }
            .sidebar { min-height: 0; width: 100%; padding: 14px 14px 10px; }
            .sidebar-brand { padding: 0 5px 10px; }
            .sidebar-user { display: none; }
            .sidebar-nav { display: flex; gap: 5px; overflow-x: auto; padding-bottom: 3px; }
            .sidebar-nav a { flex: 0 0 auto; min-height: 44px; padding: 9px 12px; }
            .header { min-height: 60px; padding: 10px 16px; }
            .header-actions { gap: 7px; }
            .theme-toggle { padding: 8px 9px; }
            .theme-toggle span { display: none; }
            .page-content { padding: 20px 14px !important; }
            .dashboard-intro { align-items: flex-start; flex-direction: column; padding: 21px 19px; }
            .assessment-summary { padding: 16px; }
            .assessment-meta { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
        @media print {
            .sidebar, .header, .no-print { display: none !important; }
            .app-shell { display: block !important; }
            body, .app-panel, .stat-card { background: white !important; color: black !important; }
        }
    </style>
</head>
<body>
<div class="app-shell d-flex">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <strong>Penilaian Kinerja</strong>
            <span>Pengelolaan kinerja pegawai</span>
        </div>
        <div class="sidebar-user">
            <span>Masuk sebagai</span>
            <strong>{{ session('username') }}</strong>
        </div>
        <nav class="sidebar-nav" aria-label="Navigasi utama">
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-house" aria-hidden="true"></i> Ringkasan</a>
            <a class="{{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}"><i class="bi bi-person-badge" aria-hidden="true"></i> Data Pegawai</a>
            <a class="{{ request()->routeIs('performance.index', 'performance.create', 'performance.store', 'performance.show', 'performance.destroy') ? 'active' : '' }}" href="{{ route('performance.index') }}"><i class="bi bi-clipboard-data" aria-hidden="true"></i> Penilaian Kinerja</a>
            <a class="{{ request()->routeIs('performance.categories') ? 'active' : '' }}" href="{{ route('performance.categories') }}"><i class="bi bi-tags" aria-hidden="true"></i> Kategori Kinerja</a>
        </nav>
    </aside>

    <main class="main-content flex-grow-1 min-vh-100">
        <header class="header d-flex justify-content-between align-items-center">
            <div class="header-title">@yield('heading', 'Penilaian Kinerja Pegawai')</div>
            <div class="header-actions">
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema terang atau gelap" aria-pressed="false">
                    <i class="bi bi-moon-stars" aria-hidden="true"></i>
                    <span>Tema gelap</span>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="no-print">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Keluar</button>
                </form>
            </div>
        </header>

        <div class="container-fluid page-content py-4 px-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
        <footer class="page-footer px-4 py-3 small text-muted">Sistem Penilaian Kinerja Pegawai</footer>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const root = document.documentElement;
        const button = document.querySelector('[data-theme-toggle]');
        const savedTheme = localStorage.getItem('kkp-theme');

        const applyTheme = (theme) => {
            root.dataset.theme = theme;
            root.dataset.bsTheme = theme;
            button.setAttribute('aria-pressed', String(theme === 'dark'));
            button.querySelector('span').textContent = theme === 'dark' ? 'Tema terang' : 'Tema gelap';
            button.querySelector('i').className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        };

        applyTheme(savedTheme === 'dark' ? 'dark' : 'light');
        button.addEventListener('click', () => {
            const theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('kkp-theme', theme);
            applyTheme(theme);
        });
    })();
</script>
@yield('scripts')
</body>
</html>
