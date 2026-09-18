<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
$v = time();
?>
<!DOCTYPE html>
<html lang="pt-BR" data-lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Tutorial &amp; Guia Completo — CorelClone</title>
    <meta name="description" content="Guia completo e bilíngue do CorelClone: aprenda interface, atalhos, PowerTRACE, contorno, PowerClip, Fountain Fill, texto em caminho, QR Code, pré-impressão e a diferença entre WebApp e Desktop.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="192x192" href="corelicon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <style>
        :root {
            --cad-bg: #0b0f19;
            --cad-card: #131b2e;
            --cad-card-border: rgba(56, 189, 248, 0.18);
            --cad-primary: #38bdf8;
            --cad-primary-glow: rgba(56, 189, 248, 0.25);
            --cad-accent: #ef4444;
            --cad-text: #f1f5f9;
            --cad-text-muted: #94a3b8;
            --cad-highlight: #38bdf8;
            --cad-success: #10b981;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--cad-bg);
            color: var(--cad-text);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 15px;
            line-height: 1.7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image:
                radial-gradient(circle at 12% 10%, rgba(56, 189, 248, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 88% 85%, rgba(99, 102, 241, 0.06) 0%, transparent 45%);
        }

        html[data-lang="pt"] [data-lang="en"] { display: none !important; }
        html[data-lang="en"] [data-lang="pt"] { display: none !important; }

        /* ── Header ── */
        .header-bar {
            background: rgba(11, 15, 25, 0.94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 14px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 1.15rem;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .lang-switch-box {
            display: inline-flex;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 2px;
            gap: 2px;
        }
        .lang-pill-btn {
            border: none;
            background: transparent;
            color: var(--cad-text-muted);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .lang-pill-btn:hover { color: #fff; }
        .lang-pill-btn.active {
            background: var(--cad-primary);
            color: #0b0f19;
            box-shadow: 0 1px 4px rgba(56,189,248,0.4);
        }
        .btn-nav-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--cad-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 6px 13px;
            background: rgba(56,189,248,0.1);
            border: 1px solid rgba(56,189,248,0.25);
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-nav-action:hover {
            background: rgba(56,189,248,0.2);
            border-color: var(--cad-primary);
            transform: translateY(-1px);
        }

        /* ── Main ── */
        .main-container {
            flex: 1;
            max-width: 1100px;
            width: 100%;
            margin: 28px auto 48px auto;
            padding: 0 20px;
        }

        /* ── Hero ── */
        .tutorial-hero {
            background: linear-gradient(135deg, rgba(14,165,233,0.15) 0%, rgba(99,102,241,0.12) 50%, rgba(19,27,46,0.9) 100%);
            border: 1px solid var(--cad-card-border);
            border-radius: 16px;
            padding: 36px 36px 28px 36px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.45);
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
        }
        .tutorial-hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -15%;
            width: 340px; height: 340px;
            background: radial-gradient(circle, rgba(56,189,248,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-badge-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(56,189,248,0.18);
            border: 1px solid rgba(56,189,248,0.35);
            color: var(--cad-primary);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .hero-title {
            font-size: 2.1rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 10px;
        }
        .hero-subtitle {
            color: #cbd5e1;
            font-size: 1.05rem;
            line-height: 1.6;
            max-width: 820px;
            margin-bottom: 24px;
        }

        /* ── Search & Filter ── */
        .search-filter-box { display: flex; flex-direction: column; gap: 14px; }
        .search-input-wrap { position: relative; width: 100%; }
        .search-input-wrap i {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: var(--cad-primary);
            font-size: 1.1rem;
        }
        .search-input {
            width: 100%;
            background: rgba(11,15,25,0.85);
            border: 1px solid rgba(56,189,248,0.3);
            color: #fff;
            padding: 14px 16px 14px 48px;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
        }
        .search-input:focus {
            border-color: var(--cad-primary);
            box-shadow: 0 0 0 3px rgba(56,189,248,0.25), 0 6px 20px rgba(0,0,0,0.3);
        }
        .filter-pills { display: flex; flex-wrap: wrap; gap: 8px; }
        .filter-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #cbd5e1;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.18s ease;
        }
        .filter-btn:hover {
            background: rgba(56,189,248,0.15);
            border-color: rgba(56,189,248,0.35);
            color: #fff;
        }
        .filter-btn.active {
            background: var(--cad-primary);
            border-color: var(--cad-primary);
            color: #0b0f19;
            box-shadow: 0 2px 8px rgba(56,189,248,0.4);
            font-weight: 700;
        }

        /* ── Sections ── */
        .tutorial-section { margin-bottom: 38px; }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .section-header h2 {
            font-size: 1.45rem;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .section-header h2 i { color: var(--cad-primary); }
        .section-count { color: var(--cad-text-muted); font-size: 0.85rem; font-weight: 600; }

        /* ── Cards ── */
        .feature-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 18px;
        }
        .feature-card {
            background: var(--cad-card);
            border: 1px solid var(--cad-card-border);
            border-radius: 12px;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card:hover {
            border-color: rgba(56,189,248,0.45);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }
        .card-title-group { display: flex; align-items: center; gap: 10px; }
        .card-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: rgba(56,189,248,0.12);
            border: 1px solid rgba(56,189,248,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cad-primary);
            font-size: 1rem;
            flex-shrink: 0;
        }
        .card-name { font-size: 1.15rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .cmd-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
            justify-content: flex-end;
        }
        kbd, .cmd-kbd {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(11,15,25,0.9);
            border: 1px solid rgba(56,189,248,0.35);
            color: var(--cad-primary);
            padding: 3px 7px;
            border-radius: 5px;
            font-size: 0.78rem;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            white-space: nowrap;
        }
        .card-desc { color: #cbd5e1; font-size: 0.92rem; line-height: 1.6; }
        .steps-box {
            background: rgba(11,15,25,0.6);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            padding: 12px 14px;
        }
        .steps-box-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--cad-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .steps-list {
            padding-left: 18px;
            color: var(--cad-text-muted);
            font-size: 0.88rem;
            line-height: 1.6;
            margin: 0;
        }
        .steps-list li { margin-bottom: 4px; }
        .steps-list li strong { color: #f1f5f9; }
        .pro-tip {
            background: rgba(16,185,129,0.1);
            border-left: 3px solid var(--cad-success);
            padding: 8px 12px;
            border-radius: 0 6px 6px 0;
            font-size: 0.85rem;
            color: #a7f3d0;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.5;
        }
        .pro-tip i { margin-top: 2px; color: var(--cad-success); }
        .warn-tip {
            background: rgba(239,68,68,0.08);
            border-left: 3px solid #ef4444;
            padding: 8px 12px;
            border-radius: 0 6px 6px 0;
            font-size: 0.85rem;
            color: #fca5a5;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.5;
        }
        .warn-tip i { margin-top: 2px; color: #ef4444; }

        /* ── Cheatsheet Table ── */
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid var(--cad-card-border);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .cheatsheet-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
            background: var(--cad-card);
        }
        .cheatsheet-table th {
            background: rgba(11,15,25,0.9);
            color: #fff;
            padding: 12px 16px;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .cheatsheet-table td {
            padding: 11px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #cbd5e1;
        }
        .cheatsheet-table tr:hover td { background: rgba(56,189,248,0.04); }
        .badge-cat {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            background: rgba(56,189,248,0.15);
            color: var(--cad-primary);
            border: 1px solid rgba(56,189,248,0.25);
        }

        /* ── CTA Block ── */
        .cta-block {
            background: linear-gradient(135deg, rgba(56,189,248,0.12) 0%, rgba(99,102,241,0.1) 100%);
            border: 1px solid rgba(56,189,248,0.3);
            border-radius: 16px;
            padding: 36px;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 38px;
            position: relative;
            overflow: hidden;
        }
        .cta-block::before {
            content: '';
            position: absolute;
            top: -60%; left: -20%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(56,189,248,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-block h3 {
            font-size: 1.55rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 10px;
        }
        .cta-block p {
            color: #94a3b8;
            font-size: 1rem;
            margin-bottom: 22px;
            max-width: 560px;
            margin-left: auto;
            margin-right: auto;
        }
        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--cad-primary);
            color: #0b0f19;
            text-decoration: none;
            font-weight: 800;
            font-size: 1rem;
            padding: 13px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(56,189,248,0.4);
            transition: all 0.2s ease;
        }
        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 28px rgba(56,189,248,0.6);
        }

        /* ── Footer ── */
        .footer-clean {
            background: #070b12;
            border-top: 1px solid rgba(255,255,255,0.08);
            padding: 26px 20px;
            text-align: center;
            margin-top: auto;
        }
        .footer-links-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .footer-link:hover, .footer-link.active { color: var(--cad-primary); }
        .footer-clean span.sep { color: rgba(255,255,255,0.2); }
        .footer-copyright { font-size: 0.8rem; color: #64748b; }
        .footer-copyright a { color: inherit; text-decoration: none; }
        .footer-copyright a:hover { color: var(--cad-primary); }

        /* ── Scroll-to-top ── */
        .btn-top {
            position: fixed;
            bottom: 24px; right: 24px;
            width: 42px; height: 42px;
            background: var(--cad-primary);
            color: #0b0f19;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(56,189,248,0.4);
            transition: all 0.2s;
            opacity: 0; visibility: hidden;
            z-index: 90;
        }
        .btn-top.visible { opacity: 1; visibility: visible; }
        .btn-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(56,189,248,0.6);
        }

        /* ── Comparison Table ── */
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            background: var(--cad-card);
        }
        .comparison-table th {
            background: rgba(11,15,25,0.9);
            color: #fff;
            padding: 12px 16px;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .comparison-table td {
            padding: 10px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #cbd5e1;
        }
        .comparison-table tr:hover td { background: rgba(56,189,248,0.04); }
        .check-yes { color: #10b981; font-weight: 700; }
        .check-no  { color: #ef4444; font-weight: 700; }
        .check-partial { color: #f59e0b; font-weight: 700; }

        @media (max-width: 768px) {
            .hero-title { font-size: 1.6rem; }
            .tutorial-hero { padding: 24px; }
            .feature-cards-grid { grid-template-columns: 1fr; }
            .header-container { flex-direction: column; gap: 12px; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
            .cta-block { padding: 24px; }
        }
    </style>
</head>
<body>

    <header class="header-bar">
        <div class="header-container">
            <a href="index.php" class="brand-logo">
                <img src="corelicon-192.png" alt="CorelClone" style="width:26px;height:26px;border-radius:5px;object-fit:cover;box-shadow:0 0 8px rgba(56,189,248,0.4);">
                <span>CorelClone <?= date('Y') ?></span>
            </a>
            <div class="header-actions">
                <div class="lang-switch-box">
                    <button type="button" class="lang-pill-btn active" id="btnLangPt" onclick="setPageLang('pt')">PT</button>
                    <button type="button" class="lang-pill-btn" id="btnLangEn" onclick="setPageLang('en')">EN</button>
                </div>
                <a href="suporte.php" class="btn-nav-action">
                    <i class="fa-solid fa-headset"></i>
                    <span data-lang="pt">Suporte &amp; FAQ</span>
                    <span data-lang="en">Support &amp; FAQ</span>
                </a>
                <a href="index.php" class="btn-nav-action" style="background:rgba(56,189,248,0.2);border-color:var(--cad-primary);">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span data-lang="pt">Voltar ao App</span>
                    <span data-lang="en">Back to App</span>
                </a>
            </div>
        </div>
    </header>

    <main class="main-container">

        <!-- ═══════════════════════ HERO ═══════════════════════ -->
        <section class="tutorial-hero">
            <div class="hero-badge-row">
                <span class="badge-pill">
                    <i class="fa-solid fa-book-open"></i>
                    <span data-lang="pt">GUIA OFICIAL &amp; MANUAL RÁPIDO</span>
                    <span data-lang="en">OFFICIAL MANUAL &amp; QUICK GUIDE</span>
                </span>
                <span class="badge-pill" style="background:rgba(16,185,129,0.15);border-color:rgba(16,185,129,0.35);color:#34d399;">
                    <i class="fa-solid fa-bolt"></i>
                    <span>10 Capítulos</span>
                </span>
            </div>
            <h1 class="hero-title">
                <span data-lang="pt">Guia Completo de Todos os Recursos do CorelClone</span>
                <span data-lang="en">Master Every Feature in CorelClone</span>
            </h1>
            <p class="hero-subtitle">
                <span data-lang="pt">Aprenda a usar a interface, atalhos profissionais, PowerTRACE™ para vetorização, contorno de adesivo, PowerClip, Fountain Fill, texto em caminho, QR Code vetorial, pré-impressão e entenda a diferença entre WebApp e Desktop.</span>
                <span data-lang="en">Learn the interface, professional shortcuts, PowerTRACE™ for vectorization, sticker contour, PowerClip, Fountain Fill, text on path, vector QR Code, prepress settings, and the difference between WebApp and Desktop editions.</span>
            </p>

            <div class="search-filter-box">
                <div class="search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="tutorialSearch" class="search-input"
                        data-placeholder-pt="Pesquisar ferramenta, atalho ou recurso (ex: atalho, vetor, gradiente, sticker, QR)..."
                        data-placeholder-en="Search tool, shortcut, or feature (e.g. shortcut, vector, gradient, sticker, QR)..."
                        placeholder="Pesquisar ferramenta, atalho ou recurso (ex: atalho, vetor, gradiente, sticker, QR)..."
                        oninput="filterTutorialCards()">
                </div>
                <div class="filter-pills" id="filterPillsBar">
                    <button type="button" class="filter-btn active" onclick="setCategoryFilter('all', this)">
                        <i class="fa-solid fa-border-all"></i>
                        <span data-lang="pt">Todos os Recursos</span><span data-lang="en">All Features</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('basics', this)">
                        <i class="fa-solid fa-compass-drafting"></i>
                        <span data-lang="pt">Interface</span><span data-lang="en">Interface</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('shortcuts', this)">
                        <i class="fa-solid fa-keyboard"></i>
                        <span data-lang="pt">Atalhos</span><span data-lang="en">Shortcuts</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('trace', this)">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span data-lang="pt">PowerTRACE™</span><span data-lang="en">PowerTRACE™</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('contour', this)">
                        <i class="fa-solid fa-pen-nib"></i>
                        <span data-lang="pt">Contorno</span><span data-lang="en">Contour</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('powerclip', this)">
                        <i class="fa-solid fa-scissors"></i>
                        <span data-lang="pt">PowerClip</span><span data-lang="en">PowerClip</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('fill', this)">
                        <i class="fa-solid fa-fill-drip"></i>
                        <span data-lang="pt">Fountain Fill</span><span data-lang="en">Fountain Fill</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('textpath', this)">
                        <i class="fa-solid fa-font"></i>
                        <span data-lang="pt">Texto em Caminho</span><span data-lang="en">Text on Path</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('qr', this)">
                        <i class="fa-solid fa-qrcode"></i>
                        <span data-lang="pt">QR Code</span><span data-lang="en">QR Code</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('prepress', this)">
                        <i class="fa-solid fa-print"></i>
                        <span data-lang="pt">Pré-impressão</span><span data-lang="en">Prepress</span>
                    </button>
                    <button type="button" class="filter-btn" onclick="setCategoryFilter('compare', this)">
                        <i class="fa-solid fa-laptop"></i>
                        <span data-lang="pt">Desktop vs WebApp</span><span data-lang="en">Desktop vs WebApp</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- ═══════════ 1. PRIMEIROS PASSOS / INTERFACE ═══════════ -->
        <section id="interface" class="tutorial-section" data-category="basics">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-compass-drafting"></i>
                    <span data-lang="pt">1. Primeiros Passos — Interface de 5 Zonas</span>
                    <span data-lang="en">1. Getting Started — The 5-Zone Interface</span>
                </h2>
                <span class="section-count">5 <span data-lang="pt">zonas</span><span data-lang="en">zones</span></span>
            </div>
            <div class="feature-cards-grid">

                <!-- Barra de Título e Menus -->
                <div class="feature-card" data-keywords="barra titulo menus arquivo editar objeto efeitos texto title bar menu bar">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-bars"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Barra de Título &amp; Menus</span>
                                <span data-lang="en">Title Bar &amp; Menu Bar</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Topo</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Localizada no topo da janela. Contém o nome do documento ativo e os menus principais: <strong>Arquivo, Editar, Exibir, Layout, Objeto, Efeitos, Bitmap, Texto, Ferramentas, Janela e Ajuda</strong>.</span>
                        <span data-lang="en">Located at the very top. Contains the active document name and main menus: <strong>File, Edit, View, Layout, Object, Effects, Bitmap, Text, Tools, Window, and Help</strong>.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Menus principais</span><span data-lang="en">Key menus</span></div>
                        <ul class="steps-list">
                            <li><strong>Objeto</strong> — <span data-lang="pt">PowerClip, Agrupar, Texto em Caminho, QR Code</span><span data-lang="en">PowerClip, Group, Fit Text to Path, QR Code</span></li>
                            <li><strong>Efeitos</strong> — <span data-lang="pt">Contorno, Sombra, Perspectiva</span><span data-lang="en">Contour, Drop Shadow, Perspective</span></li>
                            <li><strong>Bitmap</strong> — <span data-lang="pt">Converter para Bitmap, PowerTRACE™</span><span data-lang="en">Convert to Bitmap, PowerTRACE™</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Barra de Ferramentas Padrão -->
                <div class="feature-card" data-keywords="barra ferramentas padrao standard toolbar novo abrir salvar desfazer refazer zoom">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-toolbox"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Barra de Ferramentas Padrão</span>
                                <span data-lang="en">Standard Toolbar</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>2ª linha</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Segunda linha abaixo dos menus. Atalhos rápidos para <strong>Novo, Abrir, Salvar, Imprimir, Cortar, Copiar, Colar, Desfazer, Refazer</strong> e controle de zoom da prancheta.</span>
                        <span data-lang="en">Second row below the menus. Quick-access buttons for <strong>New, Open, Save, Print, Cut, Copy, Paste, Undo, Redo</strong>, and canvas zoom controls.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Ações principais</span><span data-lang="en">Main actions</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt">Clique no ícone de <strong>pasta</strong> para abrir arquivo SVG ou CDR.</span><span data-lang="en">Click the <strong>folder</strong> icon to open an SVG or CDR file.</span></li>
                            <li><span data-lang="pt">Clique no <strong>disco</strong> para salvar em SVG (Ctrl+S).</span><span data-lang="en">Click the <strong>disk</strong> to save as SVG (Ctrl+S).</span></li>
                            <li><span data-lang="pt">Use o seletor de <strong>zoom</strong> para ir de 10% a 800%.</span><span data-lang="en">Use the <strong>zoom</strong> dropdown to go from 10% to 800%.</span></li>
                        </ol>
                    </div>
                </div>

                <!-- Barra de Propriedades -->
                <div class="feature-card" data-keywords="barra propriedades property bar contexto objeto selecionado posicao tamanho x y w h largura altura">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-sliders"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Barra de Propriedades</span>
                                <span data-lang="en">Property Bar</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Contextual</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Muda dinamicamente conforme a ferramenta ativa. Com a <strong>Ferramenta Pick</strong> selecionada, exibe posição X/Y, tamanho W/H, ângulo e opções de espelhamento do objeto selecionado.</span>
                        <span data-lang="en">Changes dynamically based on the active tool. With the <strong>Pick Tool</strong> active it shows the selected object's X/Y position, W/H dimensions, rotation angle, and mirror options.</span>
                    </p>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Clique no cadeado <strong>🔒</strong> entre W e H na Barra de Propriedades para travar a proporção antes de redimensionar.</span>
                        <span data-lang="en">Click the <strong>🔒</strong> lock between W and H in the Property Bar to maintain aspect ratio when resizing.</span>
                    </div>
                </div>

                <!-- Caixa de Ferramentas -->
                <div class="feature-card" data-keywords="caixa ferramentas toolbox pick selection bezier rectangle ellipse text zoom hand gradient fill pen tool">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-pen-ruler"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Caixa de Ferramentas (Toolbox)</span>
                                <span data-lang="en">Toolbox</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Lateral</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Painel vertical à esquerda com todas as ferramentas de desenho e edição. Clique simples ativa a ferramenta; mantenha pressionado para revelar subferramentas.</span>
                        <span data-lang="en">Vertical panel on the left with all drawing and editing tools. Single click activates a tool; long-press reveals sub-tools in a flyout.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-keyboard"></i> <span data-lang="pt">Atalhos de ferramenta</span><span data-lang="en">Tool shortcuts</span></div>
                        <ul class="steps-list">
                            <li><kbd>P</kbd> — <span data-lang="pt">Pick (Seleção)</span><span data-lang="en">Pick (Selection)</span></li>
                            <li><kbd>N</kbd> — <span data-lang="pt">Bézier / Nó</span><span data-lang="en">Bézier / Node</span></li>
                            <li><kbd>R</kbd> — <span data-lang="pt">Retângulo</span><span data-lang="en">Rectangle</span></li>
                            <li><kbd>E</kbd> — <span data-lang="pt">Elipse</span><span data-lang="en">Ellipse</span></li>
                            <li><kbd>T</kbd> — <span data-lang="pt">Texto</span><span data-lang="en">Text</span></li>
                            <li><kbd>G</kbd> — <span data-lang="pt">Degradê (Fountain Fill)</span><span data-lang="en">Gradient (Fountain Fill)</span></li>
                            <li><kbd>Z</kbd> — Zoom &nbsp;|&nbsp; <kbd>H</kbd> — <span data-lang="pt">Mão (Pan)</span><span data-lang="en">Hand (Pan)</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Prancheta / Canvas -->
                <div class="feature-card" data-keywords="prancheta canvas page pagina area trabalho desenho artboard background white">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-regular fa-square"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Prancheta (Canvas)</span>
                                <span data-lang="en">Canvas (Artboard)</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Centro</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Área central branca que representa o documento imprimível. Objetos fora da prancheta existem no projeto mas não são exportados ou impressos por padrão. Você pode criar <strong>múltiplas páginas</strong> com Ctrl+W.</span>
                        <span data-lang="en">The central white area represents the printable document. Objects outside the canvas exist in the project but are not exported or printed by default. Create <strong>multiple pages</strong> with Ctrl+W.</span>
                    </p>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Use o <strong>Zoom Total</strong> (duplo clique no scroll do mouse) para ver a prancheta inteira na tela sempre que se perder.</span>
                        <span data-lang="en">Use <strong>Zoom Extents</strong> (double-click the mouse scroll wheel) to fit the entire canvas on screen whenever you get lost.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 2. ATALHOS DE TECLADO ═══════════ -->
        <section id="shortcuts" class="tutorial-section" data-category="shortcuts">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-keyboard"></i>
                    <span data-lang="pt">2. Atalhos de Teclado — Cheat Sheet Completo</span>
                    <span data-lang="en">2. Keyboard Shortcuts — Full Cheat Sheet</span>
                </h2>
                <span class="section-count">20 <span data-lang="pt">atalhos</span><span data-lang="en">shortcuts</span></span>
            </div>

            <div class="table-responsive">
                <table class="cheatsheet-table">
                    <thead>
                        <tr>
                            <th><span data-lang="pt">Atalho</span><span data-lang="en">Shortcut</span></th>
                            <th><span data-lang="pt">Ação</span><span data-lang="en">Action</span></th>
                            <th><span data-lang="pt">Categoria</span><span data-lang="en">Category</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><kbd>Ctrl+Z</kbd></td>
                            <td><span data-lang="pt">Desfazer</span><span data-lang="en">Undo</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Edição</span><span data-lang="en">Edit</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+Y</kbd></td>
                            <td><span data-lang="pt">Refazer</span><span data-lang="en">Redo</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Edição</span><span data-lang="en">Edit</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+D</kbd></td>
                            <td><span data-lang="pt">Duplicar objeto (com deslocamento)</span><span data-lang="en">Duplicate object (with offset)</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Edição</span><span data-lang="en">Edit</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+G</kbd></td>
                            <td><span data-lang="pt">Agrupar objetos selecionados</span><span data-lang="en">Group selected objects</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Objetos</span><span data-lang="en">Objects</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Delete</kbd></td>
                            <td><span data-lang="pt">Apagar seleção</span><span data-lang="en">Delete selection</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Edição</span><span data-lang="en">Edit</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+A</kbd></td>
                            <td><span data-lang="pt">Selecionar tudo</span><span data-lang="en">Select all</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Seleção</span><span data-lang="en">Selection</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Esc</kbd></td>
                            <td><span data-lang="pt">Desselecionar / Cancelar operação</span><span data-lang="en">Deselect / Cancel operation</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Seleção</span><span data-lang="en">Selection</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>F11</kbd></td>
                            <td><span data-lang="pt">Abrir diálogo Fountain Fill (Gradiente)</span><span data-lang="en">Open Fountain Fill dialog (Gradient)</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Preenchimento</span><span data-lang="en">Fill</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>F12</kbd></td>
                            <td><span data-lang="pt">Abrir diálogo de Contorno (Outline Pen)</span><span data-lang="en">Open Outline Pen dialog</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Contorno</span><span data-lang="en">Outline</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+Q</kbd></td>
                            <td><span data-lang="pt">Converter para curvas (Convert to Curves)</span><span data-lang="en">Convert to Curves</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Vetorial</span><span data-lang="en">Vector</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+E</kbd></td>
                            <td><span data-lang="pt">Exportar (PNG, SVG, PDF…)</span><span data-lang="en">Export (PNG, SVG, PDF…)</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Arquivo</span><span data-lang="en">File</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+P</kbd></td>
                            <td><span data-lang="pt">Imprimir / Pré-impressão / Sangria</span><span data-lang="en">Print / Prepress / Bleed settings</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Impressão</span><span data-lang="en">Print</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+S</kbd></td>
                            <td><span data-lang="pt">Salvar como SVG</span><span data-lang="en">Save as SVG</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Arquivo</span><span data-lang="en">File</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+O</kbd></td>
                            <td><span data-lang="pt">Abrir arquivo (SVG, CDR)</span><span data-lang="en">Open file (SVG, CDR)</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Arquivo</span><span data-lang="en">File</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Ctrl+W</kbd></td>
                            <td><span data-lang="pt">Nova Página / Nova Aba</span><span data-lang="en">New Page / New Tab</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Arquivo</span><span data-lang="en">File</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>P</kbd></td>
                            <td><span data-lang="pt">Ferramenta Pick (Seleção)</span><span data-lang="en">Pick Tool (Selection)</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Ferramenta</span><span data-lang="en">Tool</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>N</kbd></td>
                            <td><span data-lang="pt">Ferramenta Nó / Bézier</span><span data-lang="en">Node / Bézier Tool</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Ferramenta</span><span data-lang="en">Tool</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>R</kbd> &nbsp;/&nbsp; <kbd>E</kbd></td>
                            <td><span data-lang="pt">Retângulo / Elipse</span><span data-lang="en">Rectangle / Ellipse</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Ferramenta</span><span data-lang="en">Tool</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>T</kbd></td>
                            <td><span data-lang="pt">Ferramenta Texto</span><span data-lang="en">Text Tool</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Ferramenta</span><span data-lang="en">Tool</span></span></td>
                        </tr>
                        <tr>
                            <td><kbd>Z</kbd> &nbsp;/&nbsp; <kbd>H</kbd></td>
                            <td><span data-lang="pt">Zoom / Mão (Pan)</span><span data-lang="en">Zoom / Hand (Pan)</span></td>
                            <td><span class="badge-cat"><span data-lang="pt">Navegação</span><span data-lang="en">Navigation</span></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ═══════════ 3. POWERTRACE ═══════════ -->
        <section id="trace" class="tutorial-section" data-category="trace">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span data-lang="pt">3. PowerTRACE™ — Vetorizar Imagem Bitmap</span>
                    <span data-lang="en">3. PowerTRACE™ — Vectorize a Bitmap Image</span>
                </h2>
                <span class="section-count">5 <span data-lang="pt">etapas</span><span data-lang="en">steps</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="powertrace vetorizar bitmap png jpg logo lineart vetor trace autotracing">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-image"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Como Vetorizar com PowerTRACE™</span>
                                <span data-lang="en">How to Vectorize with PowerTRACE™</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Bitmap &gt; PowerTRACE</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Converte imagens PNG/JPG/BMP em vetores editáveis com curvas Bézier, preservando as cores e suavizando bordas automaticamente.</span>
                        <span data-lang="en">Converts PNG/JPG/BMP raster images into editable vector paths with Bézier curves, preserving colors and smoothing edges automatically.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Passo a Passo</span><span data-lang="en">Step by Step</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt"><strong>Importe</strong> a imagem via <em>Arquivo &gt; Importar</em> ou arraste para a prancheta.</span><span data-lang="en"><strong>Import</strong> the image via <em>File &gt; Import</em> or drag it onto the canvas.</span></li>
                            <li><span data-lang="pt"><strong>Selecione</strong> a imagem com a ferramenta Pick (P).</span><span data-lang="en"><strong>Select</strong> the image with the Pick Tool (P).</span></li>
                            <li><span data-lang="pt">Vá em <strong>Bitmap &gt; PowerTRACE™</strong>. O painel de configurações abrirá.</span><span data-lang="en">Go to <strong>Bitmap &gt; PowerTRACE™</strong>. The settings panel will open.</span></li>
                            <li><span data-lang="pt">Escolha o <strong>preset</strong>: Logo, Line Art, Detalhado ou Foto.</span><span data-lang="en">Choose the <strong>preset</strong>: Logo, Line Art, Detailed, or Photo.</span></li>
                            <li><span data-lang="pt">Ajuste <strong>Cores</strong> (1–32) e <strong>Suavização</strong>. Marque <em>Remover fundo</em> se necessário.</span><span data-lang="en">Adjust <strong>Colors</strong> (1–32) and <strong>Smoothing</strong>. Check <em>Remove background</em> if needed.</span></li>
                            <li><span data-lang="pt">Clique em <strong>OK</strong>. O resultado é um grupo de vetores — use Ctrl+U para desagrupar e editar cada cor.</span><span data-lang="en">Click <strong>OK</strong>. The result is a vector group — use Ctrl+U to ungroup and edit each color.</span></li>
                        </ol>
                    </div>
                </div>

                <div class="feature-card" data-keywords="powertrace preset logo lineart detailed photo colors smoothing background remove">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-sliders"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Presets e Configurações</span>
                                <span data-lang="en">Presets &amp; Settings</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Logo</kbd><kbd>Line Art</kbd><kbd>Photo</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Cada preset é otimizado para um tipo de imagem diferente. Escolher o correto é o passo mais importante para um bom resultado.</span>
                        <span data-lang="en">Each preset is optimized for a different image type. Choosing the right one is the most important step for a good result.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Guia de Presets</span><span data-lang="en">Preset Guide</span></div>
                        <ul class="steps-list">
                            <li><strong>Logo</strong> — <span data-lang="pt">Poucas cores, bordas nítidas. Ideal para logos e ícones.</span><span data-lang="en">Few colors, crisp edges. Best for logos and icons.</span></li>
                            <li><strong>Line Art</strong> — <span data-lang="pt">Preto e branco, linhas e sketches.</span><span data-lang="en">Black & white, lines and sketches.</span></li>
                            <li><strong>Detailed</strong> — <span data-lang="pt">Muitas cores, ilustrações ricas em detalhes.</span><span data-lang="en">Many colors, detail-rich illustrations.</span></li>
                            <li><strong>Photo</strong> — <span data-lang="pt">Fotografias — gera resultado estilizado, não fotorrealista.</span><span data-lang="en">Photographs — produces a stylized, non-photorealistic result.</span></li>
                        </ul>
                    </div>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Para logos corporativos, comece com <strong>4–8 cores</strong> e suavização em 80%. Aumente gradualmente se quiser mais detalhe.</span>
                        <span data-lang="en">For corporate logos, start with <strong>4–8 colors</strong> and smoothing at 80%. Increase gradually for more detail.</span>
                    </div>
                </div>

                <div class="feature-card" data-keywords="webapp desktop powertrace diferenca limitation cdr online offline">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-code-compare"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">WebApp vs Desktop — PowerTRACE™</span>
                                <span data-lang="en">WebApp vs Desktop — PowerTRACE™</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>WebApp</kbd><kbd>Desktop</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O PowerTRACE™ funciona normalmente em ambas as versões para PNG e JPG. A diferença está no suporte a arquivos <strong>.CDR</strong>.</span>
                        <span data-lang="en">PowerTRACE™ works normally in both versions for PNG and JPG. The key difference is <strong>.CDR</strong> file support.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Diferenças</span><span data-lang="en">Differences</span></div>
                        <ul class="steps-list">
                            <li><span data-lang="pt"><strong>WebApp/PWA:</strong> Vetoriza PNG/JPG normalmente. Sem instalação. Roda no Chrome/Edge/Firefox.</span><span data-lang="en"><strong>WebApp/PWA:</strong> Vectorizes PNG/JPG normally. No installation needed. Runs in Chrome/Edge/Firefox.</span></li>
                            <li><span data-lang="pt"><strong>Desktop:</strong> Vetoriza E abre <strong>.CDR nativamente</strong> com nós, curvas e camadas preservadas.</span><span data-lang="en"><strong>Desktop:</strong> Vectorizes AND opens <strong>.CDR natively</strong> with nodes, curves, and layers preserved.</span></li>
                            <li><span data-lang="pt"><strong>Limitação WebApp:</strong> Arquivos <strong>.CDR</strong> abrem apenas como prévia de imagem no WebApp.</span><span data-lang="en"><strong>WebApp limitation:</strong> <strong>.CDR</strong> files open only as image preview in the WebApp.</span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 4. CONTORNO E LINHA DE CORTE ═══════════ -->
        <section id="contour" class="tutorial-section" data-category="contour">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-pen-nib"></i>
                    <span data-lang="pt">4. Contorno &amp; Linha de Corte — Sticker Border</span>
                    <span data-lang="en">4. Contour &amp; Cut Line — Sticker Border</span>
                </h2>
                <span class="section-count">4 <span data-lang="pt">estilos</span><span data-lang="en">styles</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="contorno sticker border adesivo linha corte plotter offset espessura cor arredondado vivo cut line">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-vector-square"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Criar Borda de Adesivo (Sticker Border)</span>
                                <span data-lang="en">Create Sticker Border</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Efeitos &gt; Contorno</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Gera automaticamente uma borda uniforme ao redor de qualquer objeto vetorial ou grupo, ideal para adesivos e recortes de vinil.</span>
                        <span data-lang="en">Automatically generates a uniform border around any vector object or group, ideal for stickers and vinyl cutting.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Passo a Passo</span><span data-lang="en">Step by Step</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt"><strong>Selecione</strong> o objeto ou grupo com a ferramenta Pick.</span><span data-lang="en"><strong>Select</strong> the object or group with the Pick Tool.</span></li>
                            <li><span data-lang="pt">Acesse <strong>Efeitos &gt; Contorno</strong> (ou painel lateral de Contorno).</span><span data-lang="en">Go to <strong>Effects &gt; Contour</strong> (or the Contour docker).</span></li>
                            <li><span data-lang="pt">Escolha o estilo: <strong>Borda Preenchida</strong> (com cor sólida) ou <strong>Linha de Corte</strong> (linha fina para plotter).</span><span data-lang="en">Choose the style: <strong>Filled Border</strong> (solid color) or <strong>Cut Line</strong> (thin line for plotters).</span></li>
                            <li><span data-lang="pt">Defina o <strong>Offset</strong> (distância da borda ao objeto): 2mm a 5mm é comum para adesivos.</span><span data-lang="en">Set the <strong>Offset</strong> (distance from object to border): 2mm to 5mm is common for stickers.</span></li>
                            <li><span data-lang="pt">Selecione a <strong>cor</strong> da borda e o tipo de <strong>canto</strong>: Arredondado (stickers) ou Vivo (corte exato).</span><span data-lang="en">Select the border <strong>color</strong> and <strong>corner type</strong>: Rounded (stickers) or Sharp (exact cut).</span></li>
                            <li><span data-lang="pt">Clique em <strong>Aplicar</strong>. A borda é gerada como vetor separado.</span><span data-lang="en">Click <strong>Apply</strong>. The border is generated as a separate vector.</span></li>
                        </ol>
                    </div>
                </div>

                <div class="feature-card" data-keywords="linha corte plotter mira magenta cut contour style color spot color cmyk cyan magenta yellow black">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-cut"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Linha de Corte para Plotter</span>
                                <span data-lang="en">Plotter Cut Line</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Mira Magenta</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">A Linha de Corte é uma linha vetorial fina usada pelo plotter de corte (silhouette, graphtec, mimaki) para saber exatamente onde recortar o vinil.</span>
                        <span data-lang="en">The Cut Line is a thin vector line used by the cutting plotter (Silhouette, Graphtec, Mimaki) to know exactly where to cut the vinyl.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Configuração profissional</span><span data-lang="en">Professional setup</span></div>
                        <ul class="steps-list">
                            <li><span data-lang="pt">Use a cor <strong>Magenta (Spot Color)</strong> para a linha de corte — RIPs e plotters reconhecem esta convenção.</span><span data-lang="en">Use <strong>Magenta (Spot Color)</strong> for the cut line — RIPs and plotters recognize this convention.</span></li>
                            <li><span data-lang="pt">Espessura da linha: <strong>0,001mm</strong> (hairline) — o plotter ignora a espessura e corta pelo centro.</span><span data-lang="en">Line thickness: <strong>0.001mm</strong> (hairline) — the plotter ignores thickness and cuts along the center.</span></li>
                            <li><span data-lang="pt">Cantos <strong>Arredondados</strong> evitam que as pontas do vinil levantem com o tempo.</span><span data-lang="en"><strong>Rounded</strong> corners prevent vinyl tips from lifting over time.</span></li>
                            <li><span data-lang="pt">Alinhe as <strong>Miras</strong> (registration marks) para corte e impressão coincidirem.</span><span data-lang="en">Align <strong>Registration Marks</strong> so cut and print align precisely.</span></li>
                        </ul>
                    </div>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Agrupe o arte-final com a linha de corte (Ctrl+G) antes de exportar para garantir que o arquivo PDF/AI preserve a relação entre os dois layers.</span>
                        <span data-lang="en">Group the artwork with the cut line (Ctrl+G) before exporting to ensure the PDF/AI file preserves the relationship between both layers.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 5. POWERCLIP ═══════════ -->
        <section id="powerclip" class="tutorial-section" data-category="powerclip">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-scissors"></i>
                    <span data-lang="pt">5. PowerClip — Imagem Dentro de Forma</span>
                    <span data-lang="en">5. PowerClip — Image Inside a Shape</span>
                </h2>
                <span class="section-count">1 <span data-lang="pt">recurso</span><span data-lang="en">feature</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="powerclip imagem dentro forma recortar bitmap vetor clip mask mascara objeto">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-object-intersect"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">PowerClip: Imagem no Interior de Forma</span>
                                <span data-lang="en">PowerClip: Image Inside a Shape</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Objeto &gt; PowerClip</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O PowerClip recorta um bitmap (foto, ilustração) dentro de um vetor (círculo, estrela, texto, forma livre), criando um efeito de máscara de forma não-destrutivo.</span>
                        <span data-lang="en">PowerClip masks a bitmap (photo, illustration) inside a vector shape (circle, star, text, freeform), creating a non-destructive clipping mask effect.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Passo a Passo</span><span data-lang="en">Step by Step</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt"><strong>Crie</strong> a forma que servirá de máscara (ex: um círculo com a ferramenta Elipse).</span><span data-lang="en"><strong>Create</strong> the shape that will serve as the mask (e.g., a circle with the Ellipse Tool).</span></li>
                            <li><span data-lang="pt"><strong>Selecione</strong> a imagem bitmap (a foto ou ilustração que ficará dentro).</span><span data-lang="en"><strong>Select</strong> the bitmap image (the photo or illustration that will go inside).</span></li>
                            <li><span data-lang="pt">Acesse <strong>Objeto &gt; PowerClip &gt; Colocar no Interior do Frame</strong>.</span><span data-lang="en">Go to <strong>Object &gt; PowerClip &gt; Place Inside Frame</strong>.</span></li>
                            <li><span data-lang="pt">O cursor vira uma seta — <strong>clique na forma</strong> que você criou.</span><span data-lang="en">The cursor changes to an arrow — <strong>click the shape</strong> you created.</span></li>
                            <li><span data-lang="pt">A imagem fica recortada dentro da forma. Para <strong>mover o conteúdo</strong>, clique duas vezes para entrar no modo de edição do PowerClip.</span><span data-lang="en">The image is now clipped inside the shape. To <strong>reposition the content</strong>, double-click to enter PowerClip edit mode.</span></li>
                            <li><span data-lang="pt">Clique fora do objeto para <strong>sair do modo de edição</strong> e ver o resultado final.</span><span data-lang="en">Click outside the object to <strong>exit edit mode</strong> and see the final result.</span></li>
                        </ol>
                    </div>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Você pode usar <strong>texto como forma</strong> de PowerClip — converta o texto em curvas com Ctrl+Q antes de usá-lo como container.</span>
                        <span data-lang="en">You can use <strong>text as a PowerClip frame</strong> — convert the text to curves with Ctrl+Q before using it as the container.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 6. FOUNTAIN FILL ═══════════ -->
        <section id="fill" class="tutorial-section" data-category="fill">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-fill-drip"></i>
                    <span data-lang="pt">6. Fountain Fill — Preenchimento Gradiente</span>
                    <span data-lang="en">6. Fountain Fill — Gradient Fill</span>
                </h2>
                <span class="section-count">6 <span data-lang="pt">presets</span><span data-lang="en">presets</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="fountain fill gradiente linear radial angulo cor inicial final preenchimento F11">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-circle-half-stroke"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Como Usar o Fountain Fill</span>
                                <span data-lang="en">How to Use Fountain Fill</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>F11</kbd><kbd>G (ferramenta)</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O Fountain Fill cria preenchimentos gradientes de alta qualidade com suporte a múltiplas paradas de cor. Abre com a tecla <strong>F11</strong> ou pela ferramenta Gradê (G) na Toolbox.</span>
                        <span data-lang="en">Fountain Fill creates high-quality gradient fills with multi-stop color support. Open with the <strong>F11</strong> key or the Gradient Tool (G) in the Toolbox.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Passo a Passo</span><span data-lang="en">Step by Step</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt"><strong>Selecione</strong> o objeto a ser preenchido com a ferramenta Pick.</span><span data-lang="en"><strong>Select</strong> the object to fill with the Pick Tool.</span></li>
                            <li><span data-lang="pt">Pressione <strong>F11</strong> para abrir o diálogo de Fountain Fill.</span><span data-lang="en">Press <strong>F11</strong> to open the Fountain Fill dialog.</span></li>
                            <li><span data-lang="pt">Escolha o <strong>tipo</strong>: Linear (faixas) ou Radial (círculos concêntricos).</span><span data-lang="en">Choose the <strong>type</strong>: Linear (bands) or Radial (concentric circles).</span></li>
                            <li><span data-lang="pt">Clique nos marcadores de cor para definir a <strong>cor inicial</strong> e a <strong>cor final</strong>.</span><span data-lang="en">Click on the color stops to set the <strong>start color</strong> and <strong>end color</strong>.</span></li>
                            <li><span data-lang="pt">Ajuste o <strong>Ângulo</strong> (0°–360°) para girar o gradiente linear.</span><span data-lang="en">Adjust the <strong>Angle</strong> (0°–360°) to rotate the linear gradient.</span></li>
                            <li><span data-lang="pt">Clique em <strong>OK</strong> para aplicar.</span><span data-lang="en">Click <strong>OK</strong> to apply.</span></li>
                        </ol>
                    </div>
                </div>

                <div class="feature-card" data-keywords="fountain fill preset ouro prata por do sol azul royal esmeralda gradiente classico gold silver sunset royal blue emerald">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-palette"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Presets Clássicos de Gradiente</span>
                                <span data-lang="en">Classic Gradient Presets</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Presets</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O CorelClone inclui presets profissionais de gradiente prontos para uso imediato em logotipos, embalagens e sinalização.</span>
                        <span data-lang="en">CorelClone includes professional gradient presets ready for immediate use in logos, packaging, and signage.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-star"></i> <span data-lang="pt">Presets disponíveis</span><span data-lang="en">Available presets</span></div>
                        <ul class="steps-list">
                            <li>🥇 <strong><span data-lang="pt">Ouro Real</span><span data-lang="en">Real Gold</span></strong> — <span data-lang="pt">#B8860B → #FFD700 → #B8860B (Linear 90°)</span><span data-lang="en">#B8860B → #FFD700 → #B8860B (Linear 90°)</span></li>
                            <li>🥈 <strong><span data-lang="pt">Prata</span><span data-lang="en">Silver</span></strong> — <span data-lang="pt">#808080 → #E8E8E8 → #808080 (Linear 90°)</span><span data-lang="en">#808080 → #E8E8E8 → #808080 (Linear 90°)</span></li>
                            <li>🌅 <strong><span data-lang="pt">Pôr do Sol</span><span data-lang="en">Sunset</span></strong> — <span data-lang="pt">#FF512F → #DD2476 (Linear 135°)</span><span data-lang="en">#FF512F → #DD2476 (Linear 135°)</span></li>
                            <li>💎 <strong><span data-lang="pt">Azul Royal</span><span data-lang="en">Royal Blue</span></strong> — <span data-lang="pt">#1a1a2e → #16213e → #0f3460 (Radial)</span><span data-lang="en">#1a1a2e → #16213e → #0f3460 (Radial)</span></li>
                            <li>🌿 <strong><span data-lang="pt">Esmeralda</span><span data-lang="en">Emerald</span></strong> — <span data-lang="pt">#11998e → #38ef7d (Linear 45°)</span><span data-lang="en">#11998e → #38ef7d (Linear 45°)</span></li>
                            <li>🔥 <strong><span data-lang="pt">Fogo</span><span data-lang="en">Fire</span></strong> — <span data-lang="pt">#f7971e → #ffd200 (Linear 180°)</span><span data-lang="en">#f7971e → #ffd200 (Linear 180°)</span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 7. TEXTO EM CAMINHO ═══════════ -->
        <section id="textpath" class="tutorial-section" data-category="textpath">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-font"></i>
                    <span data-lang="pt">7. Texto em Caminho (Text on Path)</span>
                    <span data-lang="en">7. Text on Path</span>
                </h2>
                <span class="section-count">1 <span data-lang="pt">recurso</span><span data-lang="en">feature</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="texto caminho text path curva bezier circular arco seguir curva ajustar fit text">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-bezier-curve"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Ajustar Texto ao Caminho</span>
                                <span data-lang="en">Fit Text to Path</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Objeto &gt; Ajustar Texto ao Caminho</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Faz o texto seguir o traçado de uma curva Bézier, elipse ou qualquer caminho aberto/fechado, ideal para selos, carimbos e logos circulares.</span>
                        <span data-lang="en">Makes text follow a Bézier curve, ellipse, or any open/closed path — perfect for stamps, seals, and circular logos.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Passo a Passo</span><span data-lang="en">Step by Step</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt"><strong>Escreva</strong> o texto com a ferramenta Texto (T). Clique na prancheta para criar texto artístico (não de parágrafo).</span><span data-lang="en"><strong>Type</strong> the text with the Text Tool (T). Click on the canvas to create artistic text (not paragraph text).</span></li>
                            <li><span data-lang="pt"><strong>Crie</strong> a curva: use a Elipse (E) para círculo ou a ferramenta Bézier (N) para caminho livre.</span><span data-lang="en"><strong>Create</strong> the path: use the Ellipse Tool (E) for a circle or the Bézier Tool (N) for a freeform path.</span></li>
                            <li><span data-lang="pt"><strong>Selecione</strong> o texto e a curva juntos (Shift+Clique ou Ctrl+A).</span><span data-lang="en"><strong>Select</strong> both the text and the path together (Shift+Click or Ctrl+A).</span></li>
                            <li><span data-lang="pt">Acesse <strong>Objeto &gt; Ajustar Texto ao Caminho</strong>.</span><span data-lang="en">Go to <strong>Object &gt; Fit Text to Path</strong>.</span></li>
                            <li><span data-lang="pt">O texto se encaixará na curva. Use a ferramenta Pick para <strong>reposicionar</strong> o texto ao longo do caminho arrastando o nó de início.</span><span data-lang="en">The text snaps to the path. Use the Pick Tool to <strong>reposition</strong> the text along the path by dragging the start node.</span></li>
                            <li><span data-lang="pt">Para <strong>inverter</strong> o texto para o lado interno da curva, acesse <em>Texto &gt; Reverter Texto no Caminho</em>.</span><span data-lang="en">To <strong>flip</strong> the text to the inside of the curve, use <em>Text &gt; Reverse Text on Path</em>.</span></li>
                        </ol>
                    </div>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Para <strong>remover o contorno</strong> da curva-guia sem apagar o caminho, selecione a curva, pressione F12 e defina espessura 0 (None). O texto continuará no caminho.</span>
                        <span data-lang="en">To <strong>hide the guide path outline</strong> without deleting it, select the curve, press F12 and set thickness to None. The text stays on the path.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 8. QR CODE ═══════════ -->
        <section id="qr" class="tutorial-section" data-category="qr">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-qrcode"></i>
                    <span data-lang="pt">8. QR Code Vetorial</span>
                    <span data-lang="en">8. Vector QR Code</span>
                </h2>
                <span class="section-count">4 <span data-lang="pt">tipos</span><span data-lang="en">types</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="qr code vetorial url whatsapp pix texto vetor modulo fundo transparente ecc error correction qrcode objeto">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-qrcode"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Inserir QR Code Vetorial</span>
                                <span data-lang="en">Insert Vector QR Code</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Objeto &gt; QR Code</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Gera um QR Code diretamente na prancheta como objeto vetorial puro — escalável sem perda de qualidade. Ideal para rótulos, embalagens e brindes.</span>
                        <span data-lang="en">Generates a QR Code directly on the canvas as a pure vector object — scalable without quality loss. Ideal for labels, packaging, and promotional items.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Passo a Passo</span><span data-lang="en">Step by Step</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt">Acesse <strong>Objeto &gt; QR Code</strong> na barra de menus.</span><span data-lang="en">Go to <strong>Object &gt; QR Code</strong> in the menu bar.</span></li>
                            <li><span data-lang="pt">Escolha o <strong>tipo de conteúdo</strong>: URL, WhatsApp, PIX ou Texto Livre.</span><span data-lang="en">Choose the <strong>content type</strong>: URL, WhatsApp, PIX, or Free Text.</span></li>
                            <li><span data-lang="pt">Digite o <strong>conteúdo</strong> (ex: https://4u.ia.br ou seu número de WhatsApp).</span><span data-lang="en">Enter the <strong>content</strong> (e.g., https://4u.ia.br or your WhatsApp number).</span></li>
                            <li><span data-lang="pt">Defina o <strong>Tamanho</strong> do QR na prancheta (mm ou px) e a <strong>cor dos módulos</strong>.</span><span data-lang="en">Set the QR <strong>size</strong> on the canvas (mm or px) and the <strong>module color</strong>.</span></li>
                            <li><span data-lang="pt">Marque <strong>Fundo Transparente</strong> para usar o QR sobre imagens coloridas.</span><span data-lang="en">Check <strong>Transparent Background</strong> to place the QR over colored images.</span></li>
                            <li><span data-lang="pt">Selecione o <strong>Nível de Correção de Erro (ECC)</strong>: L (7%), M (15%), Q (25%) ou H (30%). Use H quando o QR for colocado sobre ilustrações.</span><span data-lang="en">Select the <strong>Error Correction Level (ECC)</strong>: L (7%), M (15%), Q (25%), or H (30%). Use H when placing the QR over illustrations.</span></li>
                            <li><span data-lang="pt">Clique em <strong>Gerar</strong>. O QR é inserido como vetor escalável.</span><span data-lang="en">Click <strong>Generate</strong>. The QR is inserted as a scalable vector.</span></li>
                        </ol>
                    </div>
                </div>

                <div class="feature-card" data-keywords="qr code tipo url whatsapp pix texto tipo conteudo formato">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-list-check"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Tipos de Conteúdo para QR Code</span>
                                <span data-lang="en">QR Code Content Types</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>URL</kbd><kbd>WhatsApp</kbd><kbd>PIX</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O CorelClone oferece quatro formatos otimizados de QR Code para os casos de uso mais comuns no Brasil e no mundo.</span>
                        <span data-lang="en">CorelClone offers four optimized QR Code formats for the most common use cases in Brazil and worldwide.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Tipos disponíveis</span><span data-lang="en">Available types</span></div>
                        <ul class="steps-list">
                            <li>🌐 <strong>URL</strong> — <span data-lang="pt">Link para site, portfólio, loja virtual. Ex: <code>https://4u.ia.br</code></span><span data-lang="en">Link to website, portfolio, online store. E.g., <code>https://4u.ia.br</code></span></li>
                            <li>💬 <strong>WhatsApp</strong> — <span data-lang="pt">Abre conversa direta. Formato: <code>+5511999999999</code></span><span data-lang="en">Opens a direct chat. Format: <code>+5511999999999</code></span></li>
                            <li>💰 <strong>PIX</strong> — <span data-lang="pt">Chave PIX (CPF, e-mail, telefone, aleatória) para recebimento de pagamento.</span><span data-lang="en">PIX key (CPF, e-mail, phone, random key) for payment receipt.</span></li>
                            <li>📝 <strong><span data-lang="pt">Texto Livre</span><span data-lang="en">Free Text</span></strong> — <span data-lang="pt">Qualquer texto (endereço, instruções, código de produto).</span><span data-lang="en">Any text (address, instructions, product code).</span></li>
                        </ul>
                    </div>
                    <div class="warn-tip">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span data-lang="pt">QR Codes com ECC L têm menor redundância — se impressos sobre fundo branco limpo, funcionam bem. Para QR sobre foto ou gradiente, use <strong>ECC H</strong> obrigatoriamente.</span>
                        <span data-lang="en">QR Codes with ECC L have lower redundancy — fine when printed on a clean white background. For QRs placed over photos or gradients, always use <strong>ECC H</strong>.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 9. PRÉ-IMPRESSÃO E SANGRIA ═══════════ -->
        <section id="prepress" class="tutorial-section" data-category="prepress">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-print"></i>
                    <span data-lang="pt">9. Pré-impressão &amp; Sangria (Prepress &amp; Bleed)</span>
                    <span data-lang="en">9. Prepress &amp; Bleed Settings</span>
                </h2>
                <span class="section-count">3 <span data-lang="pt">opções</span><span data-lang="en">options</span></span>
            </div>
            <div class="feature-cards-grid">

                <div class="feature-card" data-keywords="prepress sangria bleed corte marcas cmyk barra cores impressao pdf exportar ctrl+p print">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-crop-simple"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Configurar Sangria (Bleed)</span>
                                <span data-lang="en">Configure Bleed</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Ctrl+P</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">A sangria estende a impressão além da borda de corte para evitar bordas brancas após o guilhotinamento. Abre o painel com <strong>Ctrl+P</strong>.</span>
                        <span data-lang="en">Bleed extends the printing area beyond the cut edge to avoid white borders after trimming. Open the panel with <strong>Ctrl+P</strong>.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Configuração de Sangria</span><span data-lang="en">Bleed Configuration</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt">Pressione <strong>Ctrl+P</strong> para abrir o painel de Pré-impressão.</span><span data-lang="en">Press <strong>Ctrl+P</strong> to open the Prepress panel.</span></li>
                            <li><span data-lang="pt">Em <strong>Sangria</strong>, escolha: <strong>0mm</strong> (sem sangria), <strong>3mm</strong> (padrão) ou <strong>5mm</strong> (grande formato).</span><span data-lang="en">Under <strong>Bleed</strong>, choose: <strong>0mm</strong> (no bleed), <strong>3mm</strong> (standard), or <strong>5mm</strong> (large format).</span></li>
                            <li><span data-lang="pt">Ative <strong>Marcas de Corte</strong> para que a gráfica saiba onde guilhotinar.</span><span data-lang="en">Enable <strong>Crop Marks</strong> so the printer knows where to trim.</span></li>
                            <li><span data-lang="pt">Ative as <strong>Miras CMYK</strong> para controle de registro de cores.</span><span data-lang="en">Enable <strong>CMYK Registration Marks</strong> for color register control.</span></li>
                            <li><span data-lang="pt">Ative a <strong>Barra de Cores</strong> para calibração do equipamento de impressão.</span><span data-lang="en">Enable the <strong>Color Bar</strong> for press calibration.</span></li>
                        </ol>
                    </div>
                </div>

                <div class="feature-card" data-keywords="exportar svg png 300 dpi pdf prepress formato arquivo output resolution">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-file-export"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Exportar para Impressão</span>
                                <span data-lang="en">Export for Print</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>SVG</kbd><kbd>PNG 300dpi</kbd><kbd>PDF</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">Três formatos recomendados para gráficas, cada um com suas vantagens e casos de uso específicos.</span>
                        <span data-lang="en">Three recommended formats for print shops, each with specific advantages and use cases.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Formatos de exportação</span><span data-lang="en">Export formats</span></div>
                        <ul class="steps-list">
                            <li>
                                <strong>SVG</strong> — <span data-lang="pt">Vetorial puro. Ideal para plotters, corte a laser e impressoras wide-format. Use <kbd>Ctrl+S</kbd>.</span><span data-lang="en">Pure vector. Ideal for plotters, laser cutting, and wide-format printers. Use <kbd>Ctrl+S</kbd>.</span>
                            </li>
                            <li>
                                <strong>PNG 300 DPI</strong> — <span data-lang="pt">Raster de alta resolução com fundo transparente. Use <kbd>Ctrl+E</kbd> → PNG → 300 DPI.</span><span data-lang="en">High-resolution raster with transparent background. Use <kbd>Ctrl+E</kbd> → PNG → 300 DPI.</span>
                            </li>
                            <li>
                                <strong>PDF</strong> — <span data-lang="pt">Padrão universal para gráficas. Inclui sangria, marcas de corte e perfis de cor embutidos.</span><span data-lang="en">Universal print shop standard. Includes bleed, crop marks, and embedded color profiles.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Sempre peça à gráfica qual formato ela prefere antes de exportar. A maioria aceita PDF/X-1a com sangria de 3mm como padrão universal.</span>
                        <span data-lang="en">Always ask the print shop which format they prefer before exporting. Most accept PDF/X-1a with 3mm bleed as the universal standard.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ═══════════ 10. DESKTOP VS WEBAPP ═══════════ -->
        <section id="compare" class="tutorial-section" data-category="compare">
            <div class="section-header">
                <h2>
                    <i class="fa-solid fa-laptop"></i>
                    <span data-lang="pt">10. Desktop vs WebApp — Comparativo Completo</span>
                    <span data-lang="en">10. Desktop vs WebApp — Full Comparison</span>
                </h2>
                <span class="section-count">2 <span data-lang="pt">versões</span><span data-lang="en">editions</span></span>
            </div>

            <div class="table-responsive" style="margin-bottom: 22px;">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th><span data-lang="pt">Recurso</span><span data-lang="en">Feature</span></th>
                            <th><i class="fa-solid fa-globe"></i> WebApp / PWA</th>
                            <th><i class="fa-solid fa-desktop"></i> Desktop</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span data-lang="pt">Instalação necessária</span><span data-lang="en">Installation required</span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Não (roda no browser)</span><span data-lang="en">No (runs in browser)</span></span></td>
                            <td><span class="check-partial">~ <span data-lang="pt">Sim (instalador .exe/.deb)</span><span data-lang="en">Yes (.exe/.deb installer)</span></span></td>
                        </tr>
                        <tr>
                            <td><span data-lang="pt">Uso offline</span><span data-lang="en">Offline use</span></td>
                            <td><span class="check-partial">~ <span data-lang="pt">Parcial (PWA cache)</span><span data-lang="en">Partial (PWA cache)</span></span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">100% offline</span><span data-lang="en">100% offline</span></span></td>
                        </tr>
                        <tr>
                            <td><span data-lang="pt">Vetorizar PNG/JPG (PowerTRACE™)</span><span data-lang="en">Vectorize PNG/JPG (PowerTRACE™)</span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                        </tr>
                        <tr>
                            <td><span data-lang="pt">Abrir .CDR nativamente (nós + camadas)</span><span data-lang="en">Open .CDR natively (nodes + layers)</span></td>
                            <td><span class="check-no">✗ <span data-lang="pt">Apenas prévia de imagem</span><span data-lang="en">Preview only</span></span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Completo: nós Bézier, camadas e grupos preservados</span><span data-lang="en">Full: Bézier nodes, layers, groups preserved</span></span></td>
                        </tr>
                        <tr>
                            <td><span data-lang="pt">Fountain Fill, PowerClip, Contorno</span><span data-lang="en">Fountain Fill, PowerClip, Contour</span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                        </tr>
                        <tr>
                            <td>QR Code vetorial</td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                        </tr>
                        <tr>
                            <td><span data-lang="pt">Pré-impressão &amp; Sangria</span><span data-lang="en">Prepress &amp; Bleed</span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim</span><span data-lang="en">Yes</span></span></td>
                        </tr>
                        <tr>
                            <td><span data-lang="pt">Instalável como PWA</span><span data-lang="en">Installable as PWA</span></td>
                            <td><span class="check-yes">✓ <span data-lang="pt">Sim (Chrome/Edge)</span><span data-lang="en">Yes (Chrome/Edge)</span></span></td>
                            <td><span class="check-no">✗ <span data-lang="pt">N/A</span><span data-lang="en">N/A</span></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="feature-cards-grid">
                <div class="feature-card" data-keywords="instalar pwa webapp chrome edge progressive web app icone desktop atalho install button address bar">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Como Instalar como PWA</span>
                                <span data-lang="en">How to Install as PWA</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>Chrome</kbd><kbd>Edge</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O CorelClone WebApp pode ser instalado como Progressive Web App no seu computador ou celular, funcionando como um app nativo com ícone na área de trabalho.</span>
                        <span data-lang="en">CorelClone WebApp can be installed as a Progressive Web App on your computer or mobile, working as a native app with a desktop icon.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-list-ol"></i> <span data-lang="pt">Instalar no Chrome/Edge</span><span data-lang="en">Install on Chrome/Edge</span></div>
                        <ol class="steps-list">
                            <li><span data-lang="pt">Acesse <strong>https://4u.ia.br/app/corel/</strong> no Chrome ou Edge.</span><span data-lang="en">Open <strong>https://4u.ia.br/app/corel/</strong> in Chrome or Edge.</span></li>
                            <li><span data-lang="pt">Procure o <strong>ícone de instalação</strong> (⊕ ou tela com seta) na barra de endereço.</span><span data-lang="en">Look for the <strong>install icon</strong> (⊕ or screen with arrow) in the address bar.</span></li>
                            <li><span data-lang="pt">Clique em <strong>"Instalar"</strong> na mensagem de confirmação.</span><span data-lang="en">Click <strong>"Install"</strong> in the confirmation prompt.</span></li>
                            <li><span data-lang="pt">Um ícone do CorelClone aparecerá na sua área de trabalho. Clique para abrir como app nativo.</span><span data-lang="en">A CorelClone icon will appear on your desktop. Click it to open as a native app.</span></li>
                        </ol>
                    </div>
                    <div class="pro-tip">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span data-lang="pt">Em dispositivos <strong>Android</strong>, acesse o site e toque em <em>Menu &gt; Adicionar à tela inicial</em>. No <strong>iPhone/iPad</strong>, use Safari e toque em <em>Compartilhar &gt; Adicionar à Tela de Início</em>.</span>
                        <span data-lang="en">On <strong>Android</strong>, visit the site and tap <em>Menu &gt; Add to Home Screen</em>. On <strong>iPhone/iPad</strong>, use Safari and tap <em>Share &gt; Add to Home Screen</em>.</span>
                    </div>
                </div>

                <div class="feature-card" data-keywords="cdr arquivo corel draw nativo abrir desktop nodes bezier layers grupos originais">
                    <div class="card-top">
                        <div class="card-title-group">
                            <div class="card-icon"><i class="fa-solid fa-file-code"></i></div>
                            <div class="card-name">
                                <span data-lang="pt">Abrindo Arquivos .CDR no Desktop</span>
                                <span data-lang="en">Opening .CDR Files in Desktop</span>
                            </div>
                        </div>
                        <div class="cmd-badges"><kbd>.CDR</kbd><kbd>Desktop Only</kbd></div>
                    </div>
                    <p class="card-desc">
                        <span data-lang="pt">O formato .CDR (CorelDRAW nativo) é totalmente suportado apenas na versão Desktop, com todos os nós Bézier, camadas e grupos originais preservados e editáveis.</span>
                        <span data-lang="en">The .CDR (native CorelDRAW) format is fully supported only in the Desktop version, with all Bézier nodes, layers, and groups preserved and editable.</span>
                    </p>
                    <div class="steps-box">
                        <div class="steps-box-title"><i class="fa-solid fa-circle-info"></i> <span data-lang="pt">Compatibilidade .CDR</span><span data-lang="en">.CDR Compatibility</span></div>
                        <ul class="steps-list">
                            <li><span data-lang="pt"><strong>Desktop:</strong> Abre .CDR com edição completa dos nós Bézier, grupos, camadas e estilos de texto originais.</span><span data-lang="en"><strong>Desktop:</strong> Opens .CDR with full Bézier node editing, groups, layers, and original text styles.</span></li>
                            <li><span data-lang="pt"><strong>WebApp:</strong> Abre .CDR como <em>prévia</em> (imagem não editável). Para editar, exporte o arquivo como SVG no CorelDRAW original primeiro.</span><span data-lang="en"><strong>WebApp:</strong> Opens .CDR as a <em>preview</em> (non-editable image). To edit, export the file as SVG from original CorelDRAW first.</span></li>
                            <li><span data-lang="pt"><strong>Solução:</strong> No CorelDRAW original, use <em>Arquivo &gt; Exportar</em> e salve como <strong>.SVG</strong> para compatibilidade total com o WebApp.</span><span data-lang="en"><strong>Workaround:</strong> In original CorelDRAW, use <em>File &gt; Export</em> and save as <strong>.SVG</strong> for full WebApp compatibility.</span></li>
                        </ul>
                    </div>
                    <div class="warn-tip">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span data-lang="pt">Nunca tente editar um .CDR aberto como prévia no WebApp — as alterações não serão preservadas. Sempre use o Desktop para arquivos .CDR originais.</span>
                        <span data-lang="en">Never try to edit a .CDR opened as preview in the WebApp — changes will not be preserved. Always use the Desktop for original .CDR files.</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════ CTA BLOCK ═══════════ -->
        <div class="cta-block">
            <h3>
                <span data-lang="pt">🚀 Pronto para criar no CorelClone?</span>
                <span data-lang="en">🚀 Ready to create with CorelClone?</span>
            </h3>
            <p>
                <span data-lang="pt">Abra o editor agora e aplique tudo o que você aprendeu. Vetorize, crie stickers, gradientes, QR Codes e muito mais — sem instalar nada.</span>
                <span data-lang="en">Open the editor now and apply everything you learned. Vectorize, create stickers, gradients, QR Codes and much more — without installing anything.</span>
            </p>
            <a href="https://4u.ia.br/app/corel/" class="cta-btn" target="_blank" rel="noopener">
                <i class="fa-solid fa-pen-ruler"></i>
                <span data-lang="pt">Abrir CorelClone Agora</span>
                <span data-lang="en">Open CorelClone Now</span>
            </a>
        </div>

    </main>

    <!-- ═══════════ FOOTER ═══════════ -->
    <footer class="footer-clean">
        <div class="footer-links-row">
            <a href="index.php" class="footer-link">
                <i class="fa-solid fa-house"></i>
                <span data-lang="pt">Início</span><span data-lang="en">Home</span>
            </a>
            <span class="sep">•</span>
            <a href="suporte.php" class="footer-link">
                <i class="fa-solid fa-headset"></i>
                <span data-lang="pt">Suporte</span><span data-lang="en">Support</span>
            </a>
            <span class="sep">•</span>
            <a href="privacidade.php" class="footer-link">
                <i class="fa-solid fa-shield-halved"></i>
                <span data-lang="pt">Privacidade</span><span data-lang="en">Privacy</span>
            </a>
            <span class="sep">•</span>
            <a href="termos.php" class="footer-link">
                <i class="fa-solid fa-file-contract"></i>
                <span data-lang="pt">Termos</span><span data-lang="en">Terms</span>
            </a>
            <span class="sep">•</span>
            <a href="tutorial.php" class="footer-link active">
                <i class="fa-solid fa-book-open"></i>
                <span data-lang="pt">Tutorial &amp; Guia</span><span data-lang="en">Tutorial &amp; Guide</span>
            </a>
        </div>
        <div class="footer-copyright">
            <span data-lang="pt">&copy; <?= date('Y') ?> <a href="https://4u.ia.br" target="_blank">4U.IA.BR</a> • CorelClone • Todos os direitos reservados.</span>
            <span data-lang="en">&copy; <?= date('Y') ?> <a href="https://4u.ia.br" target="_blank">4U.IA.BR</a> • CorelClone • All rights reserved.</span>
        </div>
    </footer>

    <!-- Scroll-to-top button -->
    <button class="btn-top" id="btnScrollTop" onclick="scrollToTop()" title="Voltar ao topo">
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <script>
        // ── Language Toggle ──────────────────────────────────────────────
        function setPageLang(lang) {
            if (lang !== 'pt' && lang !== 'en') lang = 'pt';
            document.documentElement.setAttribute('data-lang', lang);
            try { localStorage.setItem('corelclone_lang', lang); } catch (e) {}
            const ptBtn = document.getElementById('btnLangPt');
            const enBtn = document.getElementById('btnLangEn');
            if (ptBtn) ptBtn.classList.toggle('active', lang === 'pt');
            if (enBtn) enBtn.classList.toggle('active', lang === 'en');

            const searchInput = document.getElementById('tutorialSearch');
            if (searchInput) {
                const ph = lang === 'en'
                    ? searchInput.getAttribute('data-placeholder-en')
                    : searchInput.getAttribute('data-placeholder-pt');
                if (ph) searchInput.setAttribute('placeholder', ph);
            }
        }

        // ── Category Filter ──────────────────────────────────────────────
        let currentCategory = 'all';

        function setCategoryFilter(cat, btn) {
            currentCategory = cat;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const sections = document.querySelectorAll('.tutorial-section');
            sections.forEach(sec => {
                const secCat = sec.getAttribute('data-category');
                sec.style.display = (cat === 'all' || secCat === cat) ? 'block' : 'none';
            });

            if (cat !== 'all') {
                const targetSec = document.querySelector(`.tutorial-section[data-category="${cat}"]`);
                if (targetSec) targetSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // ── Live Search ──────────────────────────────────────────────────
        function filterTutorialCards() {
            const query = (document.getElementById('tutorialSearch').value || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.feature-card');
            const rows  = document.querySelectorAll('.cheatsheet-table tbody tr, .comparison-table tbody tr');

            if (!query) {
                cards.forEach(c => c.style.display = 'flex');
                rows.forEach(r => r.style.display = '');
                setCategoryFilter(currentCategory);
                return;
            }

            // Show all sections while searching
            document.querySelectorAll('.tutorial-section').forEach(sec => sec.style.display = 'block');

            cards.forEach(c => {
                const keywords = (c.getAttribute('data-keywords') || '').toLowerCase();
                const text = c.textContent.toLowerCase();
                c.style.display = (keywords.includes(query) || text.includes(query)) ? 'flex' : 'none';
            });

            rows.forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        }

        // ── Scroll to Top ────────────────────────────────────────────────
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        window.addEventListener('scroll', () => {
            const btn = document.getElementById('btnScrollTop');
            if (btn) btn.classList.toggle('visible', window.scrollY > 300);
        });

        // ── Initialize Language on Load ──────────────────────────────────
        (function () {
            let initialLang = 'pt';
            try {
                const params  = new URLSearchParams(window.location.search);
                const urlLang = params.get('lang');
                const saved   = localStorage.getItem('corelclone_lang');
                if (urlLang === 'en' || urlLang === 'pt') {
                    initialLang = urlLang;
                } else if (saved === 'en') {
                    initialLang = 'en';
                }
            } catch (e) {}
            setPageLang(initialLang);
        })();
    </script>
</body>
</html>
