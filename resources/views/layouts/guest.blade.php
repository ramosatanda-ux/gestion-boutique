<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ma Boutique') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
        }

        /* ── Panneau gauche (branding) ── */
        .auth-brand {
            width: 420px;
            flex-shrink: 0;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 60%, #1e40af 100%);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .auth-brand::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }

        .auth-brand .logo-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 16px rgba(0,0,0,0.3));
        }

        .auth-brand h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }

        .auth-brand p {
            color: #94a3b8;
            font-size: .9rem;
            text-align: center;
            line-height: 1.6;
        }

        .auth-brand .feature {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            font-size: .85rem;
            color: #cbd5e1;
        }
        .auth-brand .feature span.icon {
            font-size: 1.1rem;
            width: 28px;
            text-align: center;
        }

        /* ── Panneau droit (formulaire) ── */
        .auth-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 40px 24px;
        }

        .auth-card {
            background: white;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        /* ── Responsive ── */
        @media (max-width: 767px) {
            body { flex-direction: column; }
            .auth-brand {
                width: 100%;
                padding: 32px 24px 24px;
                flex-direction: row;
                gap: 16px;
                justify-content: flex-start;
            }
            .auth-brand .logo-icon { font-size: 2.2rem; margin-bottom: 0; }
            .auth-brand h1 { font-size: 1.2rem; margin-bottom: 2px; text-align: left; }
            .auth-brand p, .auth-brand .feature { display: none; }
            .auth-brand::before, .auth-brand::after { display: none; }
            .auth-form-panel { padding: 24px 16px; align-items: flex-start; }
            .auth-card { padding: 28px 24px; border-radius: 14px; }
        }
    </style>
</head>
<body>

    <!-- Panneau branding -->
    <div class="auth-brand">
        <div class="logo-icon">🛒</div>
        <h1>{{ config('app.name', 'Ma Boutique') }}</h1>
        <p>Gérez vos ventes, stocks et clients<br>en toute simplicité.</p>

        <div class="feature"><span class="icon">🖥️</span> Point de vente rapide</div>
        <div class="feature"><span class="icon">📦</span> Gestion des stocks</div>
        <div class="feature"><span class="icon">📊</span> Rapports & statistiques</div>
        <div class="feature"><span class="icon">👥</span> Suivi des clients & dettes</div>
    </div>

    <!-- Formulaire -->
    <div class="auth-form-panel">
        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
