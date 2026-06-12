<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ config('app.name', 'Ma Boutique') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-bg-top : #0f172a;
            --sidebar-bg-bot : #1e3a5f;
            --sidebar-width  : 240px;
            --accent         : #3b82f6;
            --accent-hover   : #2563eb;
            --text-sidebar   : #cbd5e1;
            --bg-page        : #f1f5f9;
            --topbar-h       : 56px;
            --bottom-nav-h   : 60px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--bg-page);
            margin: 0;
            /* Évite le rebond iOS */
            overscroll-behavior-y: none;
        }

        /* Supprime le délai 300ms sur tous les éléments interactifs */
        a, button, input, select, textarea, [role="button"] {
            touch-action: manipulation;
        }

        /* ══════════════════════════════════════
           SIDEBAR — desktop
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: linear-gradient(180deg, var(--sidebar-bg-top), var(--sidebar-bg-bot));
            color: white;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 300;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            text-align: center;
            padding: 22px 16px 14px;
            font-size: 1.15rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 6px;
            flex-shrink: 0;
        }

        .sidebar-section {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 10px 20px 3px;
        }

        .sidebar a {
            color: var(--text-sidebar);
            padding: 11px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            border-radius: 8px;
            margin: 2px 10px;
            font-size: 0.9rem;
            transition: background 0.2s, color 0.2s, transform 0.15s;
        }

        .sidebar a:hover  { background: rgba(59,130,246,0.2); color: white; transform: translateX(4px); }
        .sidebar a.active { background: var(--accent); color: white; font-weight: 600; }

        .sidebar-divider { border-color: rgba(255,255,255,0.1); margin: 8px 16px; }

        .btn-logout {
            display: flex; align-items: center; gap: 10px;
            width: calc(100% - 20px); margin: 4px 10px 16px;
            padding: 10px 16px;
            background: rgba(239,68,68,0.15); color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.25); border-radius: 8px;
            font-size: 0.9rem; cursor: pointer; text-align: left;
            transition: background 0.2s, color 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.35); color: white; }

        .sidebar-user {
            font-size: 0.75rem; color: #64748b;
            text-align: center; padding: 6px 8px;
        }

        /* ══════════════════════════════════════
           TOPBAR MOBILE
        ══════════════════════════════════════ */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: linear-gradient(90deg, var(--sidebar-bg-top), var(--sidebar-bg-bot));
            color: white;
            align-items: center;
            justify-content: space-between;
            padding: 0 12px;
            padding-top: env(safe-area-inset-top);
            z-index: 400;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }

        .mobile-topbar .brand {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hamburger {
            background: none; border: none; color: white;
            font-size: 1.4rem; cursor: pointer;
            padding: 8px; line-height: 1;
            border-radius: 8px;
            -webkit-tap-highlight-color: transparent;
        }
        .hamburger:active { background: rgba(255,255,255,0.15); }

        /* Avatar utilisateur dans la topbar */
        .topbar-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.25);
        }

        /* Overlay sombre quand sidebar ouverte */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 250;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }

        /* ══════════════════════════════════════
           CONTENU PRINCIPAL
        ══════════════════════════════════════ */
        .content {
            margin-left: var(--sidebar-width);
            padding: 28px 32px;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .topbar-title { font-size: 1.05rem; font-weight: 600; color: #1e293b; }
        .topbar-user  {
            font-size: 0.82rem; color: #64748b;
            background: white; padding: 5px 12px;
            border-radius: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        /* ── Cards ── */
        .card {
            border: none; border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }

        /* ── Boutons ── */
        .btn { border-radius: 8px; }
        .btn-primary { background: linear-gradient(135deg, var(--accent), #6366f1); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, var(--accent-hover), #4f46e5); }

        /* ── Tables ── */
        .table { border-radius: 10px; overflow: hidden; }
        .table thead th { background: #0f172a; color: white; font-weight: 500; font-size: 0.85rem; }
        .table-responsive { -webkit-overflow-scrolling: touch; }

        /* Indicateur de scroll horizontal sur les tables */
        .table-scroll-wrap {
            position: relative;
        }
        .table-scroll-wrap::after {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0;
            width: 28px;
            background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
            pointer-events: none;
            border-radius: 0 14px 14px 0;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .table-scroll-wrap.has-overflow::after { opacity: 1; }

        /* ── Flash ── */
        .flash-message {
            position: fixed; top: 20px; right: 20px;
            z-index: 9999; min-width: 260px;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity:0; transform: translateX(30px); }
            to   { opacity:1; transform: translateX(0); }
        }

        /* ══════════════════════════════════════
           BARRE DE NAV MOBILE EN BAS
        ══════════════════════════════════════ */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            z-index: 400;
            /* Safe area iOS (barre d'accueil) */
            padding-bottom: env(safe-area-inset-bottom);
            box-shadow: 0 -2px 16px rgba(0,0,0,0.1);
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: var(--bottom-nav-h);
        }

        .bottom-nav-item {
            display: flex; flex-direction: column;
            align-items: center; gap: 3px;
            text-decoration: none;
            color: #94a3b8;
            font-size: 0.58rem;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 10px;
            min-width: 52px;
            min-height: 44px; /* Taille minimale pour le tap */
            justify-content: center;
            transition: color 0.15s, background 0.15s;
            -webkit-tap-highlight-color: transparent;
            position: relative;
        }

        .bottom-nav-item .icon {
            font-size: 1.35rem;
            line-height: 1;
            transition: transform 0.15s;
        }

        /* Indicateur actif : barre bleue en haut */
        .bottom-nav-item.active {
            color: var(--accent);
        }
        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0; left: 20%; right: 20%;
            height: 3px;
            background: var(--accent);
            border-radius: 0 0 3px 3px;
        }
        .bottom-nav-item.active .icon { transform: scale(1.15); }

        /* Feedback tactile */
        .bottom-nav-item:active {
            background: rgba(59,130,246,0.08);
        }

        /* Bouton POS central surélevé */
        .bottom-nav-pos {
            background: var(--accent);
            color: white !important;
            border-radius: 18px;
            width: 54px; height: 54px;
            min-height: 54px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            margin-top: -18px;
            box-shadow: 0 4px 16px rgba(59,130,246,0.5);
            border: 3px solid white;
            padding: 0;
            gap: 0;
        }
        .bottom-nav-pos::before { display: none !important; }
        .bottom-nav-pos .icon { font-size: 1.4rem; }
        .bottom-nav-pos:active { background: var(--accent-hover); transform: scale(0.95); }

        /* ══════════════════════════════════════
           RESPONSIVE — MOBILE (< 768px)
        ══════════════════════════════════════ */
        @media (max-width: 767px) {

            /* Sidebar cachée, glisse depuis la gauche */
            .sidebar {
                transform: translateX(-100%);
                width: min(280px, 85vw);
            }
            .sidebar.open { transform: translateX(0); }

            /* Topbar mobile visible */
            .mobile-topbar { display: flex; }

            /* Bottom nav visible */
            .bottom-nav { display: block; }

            /* Contenu sous la topbar, au-dessus de la bottom nav */
            .content {
                margin-left: 0;
                padding: 12px 12px 0;
                padding-top: calc(var(--topbar-h) + 12px);
                padding-bottom: calc(var(--bottom-nav-h) + env(safe-area-inset-bottom) + 12px);
            }

            /* Topbar desktop cachée */
            .topbar { display: none; }

            /* Flash au-dessus du contenu, sous la topbar */
            .flash-message {
                left: 10px; right: 10px;
                top: calc(var(--topbar-h) + 8px);
                min-width: unset;
            }

            /* Disable card hover animation sur mobile (reste collée sinon) */
            .card:hover { transform: none; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
            .card:active { transform: scale(0.99); }

            /* Formulaires : annuler max-width */
            .card[style*="max-width"] { max-width: 100% !important; }

            /* Tables */
            .table td, .table th { font-size: 0.78rem; padding: 8px 6px; }
            /* Targets tactiles dans les tables */
            .table .btn { min-height: 36px; min-width: 36px; padding: 4px 8px; }

            /* Cacher colonnes secondaires */
            .d-mobile-none { display: none !important; }

            /* Typographie compacte */
            h2, h2.fw-bold { font-size: 1.1rem !important; }
            h3 { font-size: 1rem !important; }
            h4 { font-size: 0.95rem !important; }
            h5 { font-size: 0.88rem !important; }

            /* Badges */
            .badge { font-size: 0.65rem; padding: 3px 6px; }

            /* KPI cards : icônes plus petites */
            .card-body .fs-1 { font-size: 1.6rem !important; }
            .card-body .fs-5 { font-size: 0.95rem !important; }

            /* Groupes de boutons : wrap proprement */
            .d-flex.gap-2.flex-wrap .btn { flex: 1 1 auto; min-width: 0; }

            /* Input groups : s'adaptent à la largeur */
            .input-group { flex-wrap: nowrap; }

            /* Pagination plus compacte */
            .pagination { font-size: 0.8rem; }
            .page-link { padding: 6px 10px; }

            /* Row avec 2 colonnes sur mobile : une seule colonne pour les formulaires */
            .row-mobile-stack > [class*="col-md"] { width: 100%; }
        }

        /* ══════════════════════════════════════
           TRÈS PETITS ÉCRANS (< 380px)
        ══════════════════════════════════════ */
        @media (max-width: 380px) {
            .content { padding-left: 8px; padding-right: 8px; }
            .bottom-nav-item { min-width: 44px; padding: 6px 6px; font-size: 0.52rem; }
            .bottom-nav-pos { width: 48px; height: 48px; min-height: 48px; }
        }
    </style>
</head>

<body>

<!-- ════════ TOPBAR MOBILE ════════ -->
<div class="mobile-topbar">
    <button class="hamburger" id="hamburger-btn" aria-label="Ouvrir le menu">
        ☰
    </button>

    <span class="brand">🛒 {{ config('app.name', 'Boutique') }}</span>

    <div class="topbar-avatar" title="{{ auth()->user()->name }}">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="d-flex">

    <!-- ════════════ SIDEBAR ════════════ -->
    <div class="sidebar shadow" id="sidebar">

        <div class="sidebar-brand">🛒 Boutique</div>

        @php $role = auth()->user()->role; @endphp

        {{-- ══════════════════════════════════
             CAISSIÈRE : accès POS uniquement
        ══════════════════════════════════ --}}
        @if($role === 'caissiere')

            <div class="sidebar-section">Caisse</div>
            <a href="/pos" class="{{ request()->is('pos') ? 'active' : '' }}" onclick="closeSidebar()">
                🖥️ Point de vente
            </a>
            <a href="/pos/historique" class="{{ request()->is('pos/historique') ? 'active' : '' }}" onclick="closeSidebar()">
                🧾 Mes ventes
            </a>

        {{-- ══════════════════════════════════
             VENDEUR & ADMIN
        ══════════════════════════════════ --}}
        @else

            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}" onclick="closeSidebar()">
                📊 Dashboard
            </a>

            <div class="sidebar-section">Caisse</div>
            <a href="/pos"           class="{{ request()->is('pos*') ? 'active' : '' }}" onclick="closeSidebar()">🖥️ Point de vente</a>
            <a href="/ventes"        class="{{ request()->is('ventes*') && !request()->is('ventes/create') ? 'active' : '' }}" onclick="closeSidebar()">💰 Historique ventes</a>
            <a href="/ventes/create" class="{{ request()->is('ventes/create') ? 'active' : '' }}" onclick="closeSidebar()">➕ Nouvelle vente</a>

            <div class="sidebar-section">Clients</div>
            <a href="/clients"          class="{{ request()->is('clients*') && !request()->is('clients/debiteurs') ? 'active' : '' }}" onclick="closeSidebar()">👥 Liste des clients</a>
            <a href="/clients/debiteurs"class="{{ request()->is('clients/debiteurs') ? 'active' : '' }}" onclick="closeSidebar()">🔴 Débiteurs</a>

            @if($role === 'admin')
                <hr class="sidebar-divider">
                <div class="sidebar-section">Stock</div>
                <a href="/produits"    class="{{ request()->is('produits*')    ? 'active' : '' }}" onclick="closeSidebar()">📦 Produits</a>
            <a href="/categories"  class="{{ request()->is('categories*')  ? 'active' : '' }}" onclick="closeSidebar()">🏷️ Catégories</a>
                <a href="/achats"      class="{{ request()->is('achats*')      ? 'active' : '' }}" onclick="closeSidebar()">🛍️ Achats</a>
                <a href="/fournisseurs"class="{{ request()->is('fournisseurs*')? 'active' : '' }}" onclick="closeSidebar()">🚚 Fournisseurs</a>

                <hr class="sidebar-divider">
                <div class="sidebar-section">Administration</div>
                <a href="/rapports" class="{{ request()->is('rapports*') ? 'active' : '' }}" onclick="closeSidebar()">📈 Rapports</a>
                <a href="/users"    class="{{ request()->is('users*')    ? 'active' : '' }}" onclick="closeSidebar()">👤 Utilisateurs</a>
            @endif

        @endif

        <hr class="sidebar-divider" style="margin-top:auto;">
        <div class="sidebar-user">
            {{ auth()->user()->name }}
            @if($role === 'caissiere')
                <span class="badge bg-info text-dark d-block mt-1" style="font-size:.6rem;">Caissière</span>
            @endif
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">🚪 Déconnexion</button>
        </form>

    </div>

    <!-- ════════════ CONTENU ════════════ -->
    <div class="content w-100">

        <!-- Topbar desktop -->
        <div class="topbar">
            <span class="topbar-title">Bienvenue, {{ auth()->user()->name }} 👋</span>
            <span class="topbar-user">🔑 {{ ucfirst(auth()->user()->role) }}</span>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="flash-message alert alert-success alert-dismissible fade show shadow-sm">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="flash-message alert alert-danger alert-dismissible fade show shadow-sm">
                ❌ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

</div>

<!-- ════════ BARRE DE NAV MOBILE EN BAS ════════ -->
<nav class="bottom-nav" aria-label="Navigation principale">
    <div class="bottom-nav-items">

        @if(auth()->user()->role === 'caissiere')
            {{-- Navigation caissière : POS + historique --}}
            <a href="/pos/historique"
               class="bottom-nav-item {{ request()->is('pos/historique') ? 'active' : '' }}">
                <span class="icon">🧾</span>
                <span>Mes ventes</span>
            </a>

            <a href="/pos"
               class="bottom-nav-item bottom-nav-pos {{ request()->is('pos') ? 'active' : '' }}"
               aria-label="Point de vente">
                <span class="icon">🖥️</span>
            </a>

            <button class="bottom-nav-item" onclick="toggleSidebar()" aria-label="Menu"
                    style="background:none; border:none; cursor:pointer;">
                <span class="icon">☰</span>
                <span>Menu</span>
            </button>

        @else
            {{-- Navigation complète pour admin et vendeur --}}
            <a href="/dashboard"
               class="bottom-nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span>
                <span>Accueil</span>
            </a>

            <a href="/ventes"
               class="bottom-nav-item {{ request()->is('ventes*') ? 'active' : '' }}">
                <span class="icon">💰</span>
                <span>Ventes</span>
            </a>

            <a href="/pos"
               class="bottom-nav-item bottom-nav-pos {{ request()->is('pos*') ? 'active' : '' }}"
               aria-label="Point de vente">
                <span class="icon">🖥️</span>
            </a>

            <a href="/clients"
               class="bottom-nav-item {{ request()->is('clients*') ? 'active' : '' }}">
                <span class="icon">👥</span>
                <span>Clients</span>
            </a>

            <button class="bottom-nav-item" onclick="toggleSidebar()" aria-label="Menu"
                    style="background:none; border:none; cursor:pointer;">
                <span class="icon">☰</span>
                <span>Menu</span>
            </button>
        @endif

    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar ───────────────────────────────────────────────────
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebar-overlay');
const hamburger = document.getElementById('hamburger-btn');

function toggleSidebar() {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}

function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
}

hamburger?.addEventListener('click', toggleSidebar);
overlay.addEventListener('click', closeSidebar);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

// ── Swipe pour fermer la sidebar ─────────────────────────────
let touchStartX = 0;
document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
document.addEventListener('touchend', e => {
    const dx = touchStartX - e.changedTouches[0].clientX;
    if (dx > 60 && sidebar.classList.contains('open')) closeSidebar();
}, { passive: true });

// ── Indicateur de scroll sur les tables ──────────────────────
document.querySelectorAll('.table-responsive').forEach(el => {
    const wrap = el.closest('.table-scroll-wrap');
    if (!wrap) return;
    const check = () => wrap.classList.toggle('has-overflow', el.scrollWidth > el.clientWidth);
    check();
    el.addEventListener('scroll', check, { passive: true });
    window.addEventListener('resize', check, { passive: true });
});

// ── Flash auto-close ─────────────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.flash-message').forEach(el => {
        bootstrap.Alert.getOrCreateInstance(el)?.close();
    });
}, 4000);
</script>

</body>
</html>
