<?php
// Garantir redirecionamento com barra final caso seja acessado sem ela (ex: /app/corel -> /app/corel/)
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($reqUri, PHP_URL_PATH);
if (!str_ends_with($path, '/') && !str_ends_with($path, '.php')) {
    $queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: " . $path . '/' . $queryString, true, 301);
    exit;
}
$v = time();
$baseDir = './';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>CorelClone Pro 2026 (64-Bit) — [Documento 1] @ 100%</title>
    <base href="<?php echo htmlspecialchars($baseDir); ?>">

    <!-- PWA & Mobile Web App Meta (Imagem com nome CorelClone) -->
    <link rel="manifest" href="manifest.json?v=<?php echo $v; ?>">
    <meta name="theme-color" content="#1e2124">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CorelClone">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    
    <!-- Favicon Oficial (Imagem 1 sem texto, otimizada para abas) -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="icon" type="image/png" href="favicon.png">

    <!-- Styles & Fonts -->
    <link rel="stylesheet" href="style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- JSZip for CDR unzipping & PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- Vectorization & Geometry Engines -->
    <script src="libs/imagetracer.js"></script>
    <script src="libs/paper-full.min.js"></script>
    <script src="libs/qrcode.min.js"></script>
</head>
<body>

<div class="corel-app">

    <!-- ==================== 1. Top Area (4 Levels) ==================== -->
    <header class="top-header-area">
        <!-- Level 1: App Title & Main Menu Bar -->
        <div class="window-title-bar">
            <div class="window-title-left">
                <span class="corel-app-icon"><img src="favicon-32x32.png" alt="CorelClone"></span>
                <span class="window-title-text" id="windowTitleText">CorelClone Pro 2026 (64-Bit) — [Documento 1] @ 100%</span>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <button type="button" class="btn-download-desktop" onclick="openDownloadDesktopModal()" style="display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg, #059669, #047857); color:#fff; border:none; border-radius:4px; padding:3px 10px; font-size:11px; font-weight:700; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,0.2);" data-i18n-title="btn_download_app_title" title="Baixar aplicativo para Computador (Windows, Mac ou Linux)">
                    <i class="fas fa-download"></i> <span data-i18n="btn_download_app">Baixar App</span>
                </button>
                <div class="lang-switch-box" id="langSwitchBox" title="Mudar Idioma / Switch Language">
                    <button type="button" class="lang-btn active" id="btnLangPT" data-lang="pt" onclick="setLanguage('pt')">PT</button>
                    <button type="button" class="lang-btn" id="btnLangEN" data-lang="en" onclick="setLanguage('en')">EN</button>
                </div>
                <div class="window-title-controls">
                    <button type="button" class="btn-win-ctl" title="Minimizar"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn-win-ctl" title="Maximizar"><i class="far fa-square"></i></button>
                    <button type="button" class="btn-win-ctl close" title="Fechar"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>

        <!-- Main Menu Bar -->
        <nav class="menu-bar">
            <!-- Arquivo -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_file">Arquivo</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="newDocument()"><i class="fas fa-file"></i> <span class="menu-label" data-i18n="menu_new">Novo...</span> <span class="shortcut">Ctrl+N</span></button>
                    <button type="button" class="dropdown-item" onclick="document.getElementById('importFileInput').click()"><i class="fas fa-folder-open text-emerald-600"></i> <span class="menu-label" data-i18n="menu_open">Abrir...</span> <span class="shortcut">Ctrl+O</span></button>
                    <button type="button" class="dropdown-item" onclick="saveProjectJSON()"><i class="fas fa-save"></i> <span class="menu-label" data-i18n="menu_save_svg">Salvar</span> <span class="shortcut">Ctrl+S</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="exportDocument('svg')"><i class="fas fa-file-code"></i> <span class="menu-label" data-i18n="menu_export_svg">Exportar SVG...</span></button>
                    <button type="button" class="dropdown-item" onclick="exportDocument('pdf')"><i class="fas fa-file-pdf"></i> <span class="menu-label" data-i18n="menu_export_pdf">Exportar PDF...</span></button>
                    <button type="button" class="dropdown-item" onclick="exportDocument('png')"><i class="fas fa-file-image"></i> <span class="menu-label" data-i18n="menu_export_png">Exportar PNG...</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="openPrintExportDialog()"><i class="fas fa-print text-red-600"></i> <span class="menu-label" data-i18n="menu_prepress">Pré-impressão e Sangria...</span> <span class="shortcut">Ctrl+P</span></button>
                    <button type="button" class="dropdown-item" onclick="openLocalBridgeApp()"><i class="fas fa-desktop text-cyan-600"></i> <span class="menu-label" data-i18n="menu_open_cdr">Importar Corel (.CDR)...</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="openDownloadDesktopModal()"><i class="fas fa-download text-emerald-600"></i> <span class="menu-label" data-i18n="menu_download_desktop">Baixar App para Computador...</span></button>
                </div>
            </div>

            <!-- Editar -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_edit">Editar</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="undo()"><i class="fas fa-undo"></i> <span class="menu-label" data-i18n="menu_undo">Desfazer</span> <span class="shortcut">Ctrl+Z</span></button>
                    <button type="button" class="dropdown-item" onclick="redo()"><i class="fas fa-redo"></i> <span class="menu-label" data-i18n="menu_redo">Refazer</span> <span class="shortcut">Ctrl+Y</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="duplicateSelected()"><i class="fas fa-clone"></i> <span class="menu-label" data-i18n="menu_duplicate">Duplicar</span> <span class="shortcut">Ctrl+D</span></button>
                    <button type="button" class="dropdown-item" onclick="repeatTransform()"><i class="fas fa-redo text-amber-500"></i> <span class="menu-label" data-i18n="menu_repeat">Repetir com Passo</span> <span class="shortcut">Ctrl+R</span></button>
                    <button type="button" class="dropdown-item" onclick="deleteSelected()"><i class="fas fa-trash"></i> <span class="menu-label" data-i18n="menu_delete">Excluir</span> <span class="shortcut">Delete</span></button>
                    <button type="button" class="dropdown-item" onclick="selectAll()"><i class="fas fa-object-group"></i> <span class="menu-label" data-i18n="menu_select_all">Selecionar Todos</span> <span class="shortcut">Ctrl+A</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="openFountainFillDialog()"><i class="fas fa-fill text-purple-600"></i> <span class="menu-label" data-i18n="menu_gradient">Preenchimento Gradiente...</span> <span class="shortcut">F11</span></button>
                </div>
            </div>

            <!-- Exibir -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_view">Exibir</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="setZoom(1.0)"><i class="fas fa-search"></i> <span class="menu-label" data-i18n="menu_zoom_100">Tamanho Real (100%)</span></button>
                    <button type="button" class="dropdown-item" onclick="zoomFitPage()"><i class="fas fa-expand"></i> <span class="menu-label" data-i18n="menu_zoom_page">Ajustar à Página</span> <span class="shortcut">F4</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="toggleRulers()"><i class="fas fa-ruler"></i> <span class="menu-label" data-i18n="menu_rulers">Mostrar Réguas</span></button>
                    <button type="button" class="dropdown-item" onclick="toggleGuidelines()"><i class="fas fa-border-all"></i> <span class="menu-label" data-i18n="menu_guidelines">Mostrar Linhas-Guia</span></button>
                </div>
            </div>

            <!-- Objeto -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_object">Objeto</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="groupSelected()"><i class="fas fa-object-group"></i> <span class="menu-label" data-i18n="menu_group">Agrupar</span> <span class="shortcut">Ctrl+G</span></button>
                    <button type="button" class="dropdown-item" onclick="ungroupSelected()"><i class="fas fa-object-ungroup"></i> <span class="menu-label" data-i18n="menu_ungroup">Desagrupar</span> <span class="shortcut">Ctrl+U</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="convertSelectedToCurves()"><i class="fas fa-bezier-curve"></i> <span class="menu-label" data-i18n="layer_bezier_curve">Converter em Curvas</span> <span class="shortcut">Ctrl+Q</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- CorelDRAW Align & Distribute -->
                    <button type="button" class="dropdown-item" onclick="alignSelected('P')"><i class="fas fa-crosshairs text-blue-600"></i> <span class="menu-label" data-i18n="menu_align_page">Centralizar na Página</span> <span class="shortcut">P</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('C')"><i class="fas fa-arrows-alt-h text-cyan-600"></i> <span class="menu-label" data-i18n="menu_align_center_h">Centralizar Horizontal</span> <span class="shortcut">C</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('E')"><i class="fas fa-arrows-alt-v text-cyan-600"></i> <span class="menu-label" data-i18n="menu_align_center_v">Centralizar Vertical</span> <span class="shortcut">E</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('L')"><i class="fas fa-align-left"></i> <span class="menu-label" data-i18n="menu_align_left">Alinhar à Esquerda</span> <span class="shortcut">L</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('R')"><i class="fas fa-align-right"></i> <span class="menu-label" data-i18n="menu_align_right">Alinhar à Direita</span> <span class="shortcut">R</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('T')"><i class="fas fa-arrow-up"></i> <span class="menu-label" data-i18n="menu_align_top">Alinhar pelo Topo</span> <span class="shortcut">T</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('B')"><i class="fas fa-arrow-down"></i> <span class="menu-label" data-i18n="menu_align_bottom">Alinhar pela Base</span> <span class="shortcut">B</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- CorelDRAW Espelhar / Flip -->
                    <button type="button" class="dropdown-item" onclick="flipSelected('horizontal')"><i class="fas fa-arrows-alt-h text-indigo-600"></i> <span class="menu-label" data-i18n="menu_flip_h">Espelhar Horizontalmente</span></button>
                    <button type="button" class="dropdown-item" onclick="flipSelected('vertical')"><i class="fas fa-arrows-alt-v text-indigo-600"></i> <span class="menu-label" data-i18n="menu_flip_v">Espelhar Verticalmente</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- PowerClip CorelDRAW -->
                    <button type="button" class="dropdown-item" onclick="applyPowerClip()"><i class="fas fa-sign-in-alt text-amber-600"></i> <span class="menu-label" data-i18n="menu_pc_place">PowerClip: Inserir...</span></button>
                    <button type="button" class="dropdown-item" onclick="extractPowerClip()"><i class="fas fa-sign-out-alt text-amber-600"></i> <span class="menu-label" data-i18n="menu_pc_extract">PowerClip: Extrair</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- CorelDRAW QR Code -->
                    <button type="button" class="dropdown-item" onclick="openQrCodeDialog()"><i class="fas fa-qrcode text-emerald-600"></i> <span class="menu-label" data-i18n="menu_qrcode">Inserir QR Code...</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- CorelDRAW Text on Path -->
                    <button type="button" class="dropdown-item" onclick="fitTextToPath()"><i class="fas fa-italic text-sky-600"></i> <span class="menu-label" data-i18n="menu_text_path">Ajustar Texto ao Caminho...</span></button>
                    <button type="button" class="dropdown-item" onclick="separateTextFromPath()"><i class="fas fa-unlink text-sky-600"></i> <span class="menu-label" data-i18n="prop_sep_path_btn">Separar Texto do Caminho</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="orderSelected('front')"><i class="fas fa-angle-double-up"></i> <span class="menu-label" data-i18n="menu_bring_front">Trazer para Frente</span> <span class="shortcut">Shift+PgUp</span></button>
                    <button type="button" class="dropdown-item" onclick="orderSelected('back')"><i class="fas fa-angle-double-down"></i> <span class="menu-label" data-i18n="menu_send_back">Enviar para Trás</span> <span class="shortcut">Shift+PgDn</span></button>
                </div>
            </div>

            <!-- Efeitos / Modelagem -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_effects">Modelar</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="booleanOperation('weld')"><i class="fas fa-layer-group"></i> <span class="menu-label">Soldar (Weld)</span></button>
                    <button type="button" class="dropdown-item" onclick="booleanOperation('trim')"><i class="fas fa-cut"></i> <span class="menu-label">Aparar (Trim)</span></button>
                    <button type="button" class="dropdown-item" onclick="booleanOperation('intersect')"><i class="fas fa-circle-notch"></i> <span class="menu-label">Interseção (Intersect)</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="openContourDialog()"><i class="fas fa-bullseye text-pink-600"></i> <span class="menu-label" data-i18n="menu_contour">Contorno (Borda de Corte)...</span></button>
                    <button type="button" class="dropdown-item" onclick="openDropShadowDialog()"><i class="fas fa-cloud-moon text-indigo-600"></i> <span class="menu-label" data-i18n="menu_shadow">Sombra Projetada (Drop Shadow)...</span></button>
                </div>
            </div>

            <!-- Bitmap -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_bitmap">Bitmap</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="openPowerTraceDialog()"><i class="fas fa-bolt text-amber-500"></i> <span class="menu-label" data-i18n="menu_trace">Rastreamento PowerTRACE™...</span></button>
                    <button type="button" class="dropdown-item" onclick="toggleImportedBgImage()"><i class="fas fa-eye-slash"></i> <span class="menu-label" data-i18n="menu_toggle_bg">Ocultar Imagem de Fundo</span></button>
                </div>
            </div>

            <!-- Ajuda -->
            <div class="menu-item">
                <button type="button" class="menu-btn" data-i18n="menu_help">Ajuda</button>
                <div class="dropdown-menu">
                    <a href="tutorial.php" target="_blank" class="dropdown-item"><i class="fas fa-book-open text-emerald-600"></i> <span class="menu-label" data-i18n="menu_tutorial">Tutorial & Guia Completo...</span></a>
                    <button type="button" class="dropdown-item" onclick="showShortcutsModal()"><i class="fas fa-keyboard text-sky-600"></i> <span class="menu-label" data-i18n="menu_shortcuts">Atalhos de Teclado</span></button>
                    <a href="suporte.php" target="_blank" class="dropdown-item"><i class="fas fa-question-circle text-purple-600"></i> <span class="menu-label" data-i18n="menu_support">Suporte CorelClone & FAQ</span></a>
                    <div class="dropdown-separator"></div>
                    <a href="termos.php" target="_blank" class="dropdown-item"><i class="fas fa-file-contract"></i> <span class="menu-label" data-i18n="menu_terms">Termos de Serviço</span></a>
                    <a href="privacidade.php" target="_blank" class="dropdown-item"><i class="fas fa-user-shield"></i> <span class="menu-label" data-i18n="menu_privacy">Política de Privacidade</span></a>
                </div>
            </div>
        </nav>

        <!-- Level 2: Standard Toolbar -->
        <div class="standard-toolbar">
            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="newDocument()" data-i18n-title="act_new_doc" title="Novo (Ctrl+N)"><i class="fas fa-file"></i></button>
                <button type="button" class="t-btn" onclick="document.getElementById('importFileInput').click()" data-i18n-title="act_open" title="Abrir .CDR / .PDF / SVG (Ctrl+O)"><i class="fas fa-folder-open text-amber-600"></i></button>
                <button type="button" class="t-btn" onclick="saveProjectJSON()" data-i18n-title="act_save_svg" title="Salvar Projeto (Ctrl+S)"><i class="fas fa-save text-blue-600"></i></button>
                <button type="button" class="t-btn" onclick="window.print()" data-i18n-title="act_print_doc" title="Imprimir (Ctrl+P)"><i class="fas fa-print"></i></button>
            </div>

            <div class="tool-sep"></div>

            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="cutSelected()" data-i18n-title="act_cut" title="Recortar (Ctrl+X)"><i class="fas fa-cut"></i></button>
                <button type="button" class="t-btn" onclick="copySelected()" data-i18n-title="act_copy" title="Copiar (Ctrl+C)"><i class="fas fa-copy"></i></button>
                <button type="button" class="t-btn" onclick="pasteSelected()" data-i18n-title="act_paste" title="Colar (Ctrl+V)"><i class="fas fa-paste"></i></button>
            </div>

            <div class="tool-sep"></div>

            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="undo()" data-i18n-title="act_undo" title="Desfazer (Ctrl+Z)"><i class="fas fa-undo"></i></button>
                <button type="button" class="t-btn" onclick="redo()" data-i18n-title="act_redo" title="Refazer (Ctrl+Y)"><i class="fas fa-redo"></i></button>
            </div>

            <div class="tool-sep"></div>

            <!-- Import / Export Actions -->
            <div class="toolbar-group">
                <button type="button" class="t-btn-labeled" onclick="exportDocument('svg')" data-i18n-title="act_exp_svg" title="Exportar vetor SVG limpo">
                    <i class="fas fa-file-code text-orange-600"></i> SVG
                </button>
                <button type="button" class="t-btn-labeled" onclick="exportDocument('pdf')" data-i18n-title="act_exp_pdf" title="Exportar PDF para impressão">
                    <i class="fas fa-file-pdf text-red-600"></i> PDF
                </button>
                <button type="button" class="t-btn-labeled" onclick="exportDocument('png')" data-i18n-title="act_exp_png" title="Exportar imagem PNG HD">
                    <i class="fas fa-file-image text-emerald-600"></i> PNG
                </button>
                <button type="button" class="t-btn-labeled" onclick="openPrintExportDialog()" data-i18n-title="act_exp_prepress" title="Pré-impressão com Sangria e Marcas de Corte (Ctrl+P)">
                    <i class="fas fa-print text-purple-700"></i> <span data-i18n="menu_prepress">Gráfica</span>
                </button>
            </div>

            <div class="tool-sep"></div>

            <!-- Zoom Box -->
            <div class="zoom-dropdown-box">
                <label for="zoomSelect" style="margin-right:4px; font-size:11px; color:#555;"><i class="fas fa-search"></i></label>
                <select id="zoomSelect" class="corel-select" onchange="changeZoomPreset(this.value)">
                    <option value="fit" data-i18n="act_zoom_fit">Para Ajustar (F4)</option>
                    <option value="width">Largura</option>
                    <option value="0.25">25%</option>
                    <option value="0.5">50%</option>
                    <option value="0.75">75%</option>
                    <option value="1.0" selected>100%</option>
                    <option value="1.5">150%</option>
                    <option value="2.0">200%</option>
                    <option value="4.0">400%</option>
                </select>
            </div>

            <div class="tool-sep"></div>

            <!-- Align & Power Actions -->
            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="alignSelected('center')" data-i18n-title="prop_align_p" title="Centralizar na Página (P)"><i class="fas fa-crosshairs"></i></button>
                <button type="button" class="t-btn" id="btnToggleDuplicateBg" onclick="toggleImportedBgImage()" style="display:none;" data-i18n-title="menu_toggle_bg" title="Ocultar Imagem de Fundo Duplicada do Modelo"><i class="fas fa-eye-slash text-amber-600"></i></button>
                <button type="button" class="t-btn" id="btnQuickPowerTrace" onclick="openPowerTraceDialog()" data-i18n-title="prop_trace_title" title="PowerTRACE™ — Vetorizar Bitmap"><i class="fas fa-bolt text-amber-500"></i></button>
            </div>

            <!-- Hidden File Input for .CDR, .PDF, .SVG, Images -->
            <input type="file" id="importFileInput" accept=".cdr,.pdf,.svg,.png,.jpg,.jpeg,.webp,.json" style="display:none;">
        </div>

        <!-- Level 3: Dynamic Property Bar (Barra de Propriedades Corel) -->
        <div class="property-bar" id="corelPropertyBar">
            <!-- Default Document Properties -->
            <div id="propGroupDocument" class="toolbar-group">
                <div class="prop-field">
                    <label for="docPresetSelect">Formato:</label>
                    <select id="docPresetSelect" class="corel-select" onchange="changeDocPreset(this.value)">
                        <option value="A4-Landscape">A4 Deitado (297 x 210 mm)</option>
                        <option value="A4-Portrait">A4 Em Pé (210 x 297 mm)</option>
                        <option value="Cartao-Visita">Cartão de Visita (90 x 50 mm)</option>
                        <option value="Instagram-Post">Post Instagram (1080 x 1080 px)</option>
                        <option value="Instagram-Story">Story / Reels (1080 x 1920 px)</option>
                        <option value="Banner-Web">Banner Full HD (1920 x 1080 px)</option>
                        <option value="Custom">Personalizado</option>
                    </select>
                </div>
                <div class="tool-sep"></div>
                <div class="prop-field">
                    <label>L:</label>
                    <input type="number" id="docPropWidth" class="prop-input" value="1122" onchange="updateDocDimensionsFromInput()">
                </div>
                <div class="prop-field">
                    <label>A:</label>
                    <input type="number" id="docPropHeight" class="prop-input" value="793" onchange="updateDocDimensionsFromInput()">
                </div>
                <div class="toolbar-group" style="margin-left:4px;">
                    <button type="button" class="t-btn active" id="btnOrientLandscape" onclick="setDocOrientation('landscape')" data-i18n-title="prop_landscape" title="Paisagem"><i class="fas fa-file-alt fa-rotate-90"></i></button>
                    <button type="button" class="t-btn" id="btnOrientPortrait" onclick="setDocOrientation('portrait')" data-i18n-title="prop_portrait" title="Retrato"><i class="fas fa-file-alt"></i></button>
                </div>
                <div class="tool-sep"></div>
                <div class="prop-field">
                    <label>Unidades:</label>
                    <select id="unitSelect" class="corel-select" onchange="changeUnits(this.value)">
                        <option value="mm" selected>Milímetros (mm)</option>
                        <option value="px">Pixels (px)</option>
                        <option value="in">Polegadas (in)</option>
                        <option value="pt">Pontos (pt)</option>
                    </select>
                </div>
            </div>

            <!-- Object Selection Properties -->
            <div id="propGroupObject" class="toolbar-group" style="display:none;">
                <div class="prop-field">
                    <label>X:</label>
                    <input type="number" id="objPropX" class="prop-input" value="0" data-i18n-title="prop_pos_x" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="prop-field">
                    <label>Y:</label>
                    <input type="number" id="objPropY" class="prop-input" value="0" data-i18n-title="prop_pos_y" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="tool-sep"></div>
                <div class="prop-field">
                    <label>L:</label>
                    <input type="number" id="objPropW" class="prop-input" value="100" data-i18n-title="prop_width" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="prop-field">
                    <label>A:</label>
                    <input type="number" id="objPropH" class="prop-input" value="100" data-i18n-title="prop_height" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="tool-sep"></div>
                <div class="prop-field">
                    <label><i class="fas fa-redo-alt"></i></label>
                    <input type="number" id="objPropRotate" class="prop-input" style="width:50px;" value="0" data-i18n-title="prop_angle" onchange="updateSelectedTransformFromProp()">
                    <span style="font-size:10px; color:#666;">°</span>
                </div>
                <div class="toolbar-group" title="Espelhamento CorelDRAW">
                    <button type="button" class="t-btn" onclick="flipSelected('horizontal')" data-i18n-title="prop_flip_h" title="Espelhar Horizontalmente"><i class="fas fa-arrows-alt-h text-indigo-600"></i></button>
                    <button type="button" class="t-btn" onclick="flipSelected('vertical')" data-i18n-title="prop_flip_v" title="Espelhar Verticalmente"><i class="fas fa-arrows-alt-v text-indigo-600"></i></button>
                </div>
                <div class="tool-sep"></div>
                <!-- Boolean Modeling -->
                <div class="toolbar-group" id="booleanButtonsGroup">
                    <button type="button" class="t-btn" onclick="booleanOperation('weld')" title="Soldar (Weld)"><i class="fas fa-layer-group text-blue-600"></i></button>
                    <button type="button" class="t-btn" onclick="booleanOperation('trim')" title="Aparar (Trim)"><i class="fas fa-cut text-purple-600"></i></button>
                    <button type="button" class="t-btn" onclick="booleanOperation('intersect')" title="Interseção (Intersect)"><i class="fas fa-circle-notch text-emerald-600"></i></button>
                </div>
                <div class="tool-sep"></div>
                <!-- Corel Align Buttons -->
                <div class="toolbar-group" title="Alinhamento Rápido Corel (Atalhos: C, E, L, R, T, B, P)">
                    <button type="button" class="t-btn" onclick="alignSelected('L')" data-i18n-title="menu_align_left" title="Alinhar à Esquerda (L)"><i class="fas fa-align-left"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('C')" data-i18n-title="menu_align_center_h" title="Centralizar Horizontal (C)"><i class="fas fa-arrows-alt-h"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('R')" data-i18n-title="menu_align_right" title="Alinhar à Direita (R)"><i class="fas fa-align-right"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('T')" data-i18n-title="menu_align_top" title="Alinhar pelo Topo (T)"><i class="fas fa-arrow-up" style="font-size:10px;"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('E')" data-i18n-title="menu_align_center_v" title="Centralizar Vertical (E)"><i class="fas fa-arrows-alt-v"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('B')" data-i18n-title="menu_align_bottom" title="Alinhar pela Base (B)"><i class="fas fa-arrow-down" style="font-size:10px;"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('P')" data-i18n-title="prop_align_p" title="Centralizar na Página (P)"><i class="fas fa-crosshairs text-blue-600"></i></button>
                </div>
                <div class="tool-sep"></div>
                <!-- PowerClip Quick Buttons -->
                <div class="toolbar-group">
                    <button type="button" class="t-btn" onclick="applyPowerClip()" data-i18n-title="menu_pc_place" title="PowerClip: Colocar no Recipiente"><i class="fas fa-sign-in-alt text-amber-600"></i></button>
                    <button type="button" class="t-btn" id="btnExtractPowerClip" onclick="extractPowerClip()" style="display:none;" data-i18n-title="menu_pc_extract" title="PowerClip: Extrair Conteúdo"><i class="fas fa-sign-out-alt text-amber-600"></i></button>
                    <button type="button" class="t-btn" onclick="openContourDialog()" data-i18n-title="prop_contour_btn" title="Contorno / Borda de Adesivo e Corte (Contour)"><i class="fas fa-bullseye text-pink-600"></i></button>
                    <button type="button" class="t-btn" onclick="openFountainFillDialog()" data-i18n-title="prop_fountain_btn" title="Preenchimento Gradiente / Degradê (F11)"><i class="fas fa-fill text-purple-600"></i></button>
                    <button type="button" class="t-btn" onclick="openDropShadowDialog()" data-i18n-title="prop_shadow_btn" title="Sombra Projetada (Drop Shadow)"><i class="fas fa-cloud-moon text-indigo-600"></i></button>
                    <button type="button" class="t-btn" onclick="fitTextToPath()" data-i18n-title="prop_text_path_btn" title="Ajustar Texto ao Caminho (Curvar Texto)"><i class="fas fa-italic text-sky-600"></i></button>
                    <button type="button" class="t-btn" id="btnSepTextPath" onclick="separateTextFromPath()" style="display:none;" data-i18n-title="prop_sep_path_btn" title="Separar Texto do Caminho"><i class="fas fa-unlink text-sky-600"></i></button>
                    <button type="button" class="t-btn" onclick="duplicateSelected()" data-i18n-title="prop_dup_btn" title="Duplicar Objeto (Ctrl+D)"><i class="fas fa-clone text-slate-700"></i></button>
                    <button type="button" class="t-btn" onclick="repeatTransform()" data-i18n-title="prop_repeat_btn" title="Repetir / Duplicar com Passo (Ctrl+R)"><i class="fas fa-redo text-amber-500"></i></button>
                </div>
                <div class="tool-sep"></div>
                <!-- PowerTRACE Action Button for Selected Bitmaps -->
                <button type="button" class="t-btn-primary" id="btnTraceSelected" onclick="openPowerTraceDialog()" style="display:none; gap:5px; font-size:11px; padding:3px 9px; background:#d97706; border-color:#b45309;" data-i18n-title="prop_trace_title" title="PowerTRACE: Rastrear e Vetorizar esta Imagem">
                    <i class="fas fa-bolt text-amber-200"></i> <span data-i18n="prop_trace_selected">Rastrear Bitmap</span>
                </button>
            </div>

            <!-- Node Tool (F10) Properties -->
            <div id="propGroupNodeTool" class="toolbar-group" style="display:none;">
                <button type="button" class="t-btn" onclick="addNodeToSelectedPath()" title="Adicionar Nó (+)"><i class="fas fa-plus"></i></button>
                <button type="button" class="t-btn" onclick="deleteSelectedNode()" title="Excluir Nó (-)"><i class="fas fa-minus"></i></button>
                <div class="tool-sep"></div>
                <button type="button" class="t-btn" onclick="convertNodeToCurve()" title="Converter em Curva"><i class="fas fa-bezier-curve"></i></button>
                <button type="button" class="t-btn" onclick="convertNodeToLine()" title="Converter em Linha"><i class="fas fa-slash"></i></button>
            </div>
        </div>

        <!-- Level 4: Document Tabs Bar (Abas Superiores Corel) -->
        <div class="document-tabs-bar" id="corelTabsBar">
            <div class="doc-tab" onclick="switchDocumentTab('welcome')">
                <i class="fas fa-home"></i> <span data-i18n="tab_start_page">Tela Inicial</span>
            </div>
            <div class="doc-tab active" id="tabDoc1" onclick="switchDocumentTab('page0')">
                <i class="fas fa-vector-square text-cyan-600"></i> <span data-i18n="doc_default_name">Documento 1</span>
                <span class="tab-close" onclick="closeDocTab(event, 0)">✕</span>
            </div>
            <button type="button" class="btn-new-tab" onclick="addNewPageTab()" title="Nova Página / Aba (+)"><i class="fas fa-plus"></i></button>
        </div>
    </header>

    <!-- ==================== 2. Main Workspace Layout Grid ==================== -->
    <div class="main-workspace-grid">

        <!-- Left Vertical Toolbox (Caixa de Ferramentas Corel) -->
        <aside class="corel-toolbox">
            <!-- 1. Pick Tool -->
            <button type="button" class="tool-btn active" id="toolBtn_select" onclick="selectTool('select')" data-i18n-title="tool_pick" title="Ferramenta Seleção (Espaço)">
                <i class="fas fa-mouse-pointer"></i>
            </button>
            <!-- 2. Shape Tool (F10) -->
            <button type="button" class="tool-btn" id="toolBtn_node" onclick="selectTool('node')" data-i18n-title="tool_shape" title="Ferramenta Forma / Nós (F10)">
                <i class="fas fa-bezier-curve"></i>
            </button>
            <!-- 3. Crop / Knife -->
            <button type="button" class="tool-btn" id="toolBtn_crop" onclick="selectTool('crop')" data-i18n-title="tool_crop" title="Cortar / Faca (C)">
                <i class="fas fa-crop-alt"></i>
            </button>
            <!-- 4. Zoom / Pan -->
            <button type="button" class="tool-btn" id="toolBtn_zoom" onclick="selectTool('pan')" data-i18n-title="tool_pan" title="Pan / Mover Tela (H)">
                <i class="fas fa-hand-paper"></i>
            </button>
            <!-- 5. Freehand / Pen -->
            <button type="button" class="tool-btn" id="toolBtn_pen" onclick="selectTool('pen')" data-i18n-title="tool_pen" title="Caneta Bézier / Mão Livre">
                <i class="fas fa-pen-nib"></i>
            </button>
            <!-- 6. Artistic Media / Brush -->
            <button type="button" class="tool-btn" id="toolBtn_brush" onclick="selectTool('brush')" data-i18n-title="tool_brush" title="Mídia Artística / Pincel">
                <i class="fas fa-paint-brush"></i>
            </button>
            <!-- 7. Rectangle (F6) -->
            <button type="button" class="tool-btn" id="toolBtn_rect" onclick="selectTool('rect')" data-i18n-title="tool_rect" title="Retângulo (F6)">
                <i class="far fa-square"></i>
            </button>
            <!-- 8. Ellipse (F7) -->
            <button type="button" class="tool-btn" id="toolBtn_ellipse" onclick="selectTool('ellipse')" data-i18n-title="tool_ellipse" title="Elipse (F7)">
                <i class="far fa-circle"></i>
            </button>
            <!-- 9. Star / Polygon (Y) -->
            <button type="button" class="tool-btn" id="toolBtn_star" onclick="selectTool('star')" data-i18n-title="tool_poly" title="Polígono / Estrela (Y)">
                <i class="far fa-star"></i>
            </button>
            <!-- 10. Text (F8) -->
            <button type="button" class="tool-btn" id="toolBtn_text" onclick="selectTool('text')" data-i18n-title="tool_text" title="Texto (F8)">
                <i class="fas fa-font"></i>
            </button>
            <!-- 11. Eyedropper -->
            <button type="button" class="tool-btn" id="toolBtn_eyedropper" onclick="selectTool('eyedropper')" data-i18n-title="tool_eyedropper" title="Conta-gotas de Cor">
                <i class="fas fa-eye-dropper"></i>
            </button>
            <!-- 12. Fill Tool -->
            <button type="button" class="tool-btn" id="toolBtn_fill" onclick="selectTool('fill')" data-i18n-title="tool_fountain" title="Preenchimento Interativo (G)">
                <i class="fas fa-fill-drip"></i>
            </button>
            <!-- 13. PowerTRACE (Vetorizar Bitmap) -->
            <button type="button" class="tool-btn" id="toolBtn_trace" onclick="openPowerTraceDialog()" data-i18n-title="prop_trace_title" title="PowerTRACE™ — Vetorizar Bitmap / Logo (Curvas)">
                <i class="fas fa-bolt text-amber-500"></i>
            </button>
            <!-- 14. Contour Tool (Borda de Adesivo / Corte) -->
            <button type="button" class="tool-btn" id="toolBtn_contour" onclick="openContourDialog()" data-i18n-title="prop_contour_btn" title="Contorno / Borda de Adesivo e Corte (Contour)">
                <i class="fas fa-bullseye text-pink-600"></i>
            </button>

            <!-- Toolbox Color Swatches (Base da Barra de Ferramentas Corel) -->
            <div class="toolbox-color-well" title="Cores Ativas: Clique para alterar preenchimento ou contorno">
                <div class="color-swatch-stroke" id="activeStrokeSwatch" onclick="openStrokeColorPicker()"></div>
                <div class="color-swatch-fill" id="activeFillSwatch" onclick="openFillColorPicker()"></div>
            </div>
        </aside>

        <!-- Central Viewport with Calibrated Rulers & Board -->
        <main class="viewport-wrapper" id="viewportWrapper">
            <!-- Top-Left Corner Box between Rulers -->
            <div class="ruler-corner" onclick="resetRulerZero()" title="Ponto Zero (0,0)"></div>

            <!-- Horizontal Ruler -->
            <canvas id="rulerH" class="ruler-horizontal"></canvas>

            <!-- Vertical Ruler -->
            <canvas id="rulerV" class="ruler-vertical"></canvas>

            <!-- Canvas Workspace Scroller -->
            <div class="canvas-scroller" id="canvasScroller">
                <div class="canvas-board" id="canvasBoard">
                    <svg id="mainSvgCanvas" xmlns="http://www.w3.org/2000/svg" width="1122" height="793" viewBox="0 0 1122 793">
                        <defs>
                            <!-- Grid Background Pattern -->
                            <pattern id="gridPattern" width="20" height="20" patternUnits="userSpaceOnUse">
                                <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="1"/>
                            </pattern>
                        </defs>

                        <!-- White Page Sheet -->
                        <rect id="bgSheet" width="100%" height="100%" fill="#ffffff"/>

                        <!-- Interactive Magnetic Guidelines (Linhas-Guia) -->
                        <g id="guidelinesGroup"></g>

                        <!-- Main Vectors Layer Group -->
                        <g id="layerGroupMain"></g>

                        <!-- Interactive Bounding Box / Selection Overlay -->
                        <g id="selectionOverlay" pointer-events="all"></g>

                        <!-- Interactive Bezier Node Editing Overlay (Shape Tool F10) -->
                        <g id="nodeEditOverlay" pointer-events="all"></g>
                    </svg>
                </div>
            </div>
        </main>

        <!-- Right Side Docker: Objects (Camadas e Objetos) -->
        <aside class="docker-container" id="objectsDocker">
            <div class="docker-header">
                <div class="docker-title"><i class="fas fa-layer-group text-blue-600"></i> <span data-i18n="docker_title">Objetos / Camadas</span></div>
                <div class="docker-controls">
                    <button type="button" class="btn-win-ctl" onclick="toggleDockerCollapse()" title="Recolher / Expandir"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="docker-body">
                <!-- Search bar -->
                <div class="objects-search-bar">
                    <input type="text" id="objectsSearchInput" class="objects-search-input" data-i18n-placeholder="docker_search_ph" placeholder="Pesquisar objetos..." oninput="filterObjectsTree(this.value)">
                </div>

                <!-- Blending Mode & Opacity -->
                <div class="objects-blend-row">
                    <select id="blendModeSelect" class="corel-select" style="width:110px;" onchange="changeSelectedBlendMode(this.value)">
                        <option value="normal" data-i18n="docker_blend_normal">Normal</option>
                        <option value="multiply" data-i18n="docker_blend_multiply">Multiplicar</option>
                        <option value="screen" data-i18n="docker_blend_screen">Tela</option>
                        <option value="overlay" data-i18n="docker_blend_overlay">Sobrepor</option>
                        <option value="darken">Escurecer</option>
                        <option value="lighten">Clarear</option>
                    </select>

                    <div class="opacity-slider-box">
                        <input type="range" id="layerOpacitySlider" min="0" max="100" value="100" oninput="changeSelectedOpacity(this.value)">
                        <span id="layerOpacityVal" style="font-size:10.5px; width:32px; text-align:right;">100%</span>
                    </div>
                </div>

                <!-- Layers Tree List -->
                <div class="objects-tree" id="objectsTreeList">
                    <!-- Dynamic Layer Items Injected by JS -->
                </div>

                <!-- Docker Bottom Actions Bar -->
                <div class="docker-bottom-bar">
                    <button type="button" class="t-btn" onclick="addNewLayer()" data-i18n-title="docker_btn_up" title="Nova Camada"><i class="fas fa-plus-square text-blue-600"></i></button>
                    <button type="button" class="t-btn" onclick="duplicateSelected()" data-i18n-title="prop_dup_btn" title="Duplicar Objeto"><i class="fas fa-clone text-amber-600"></i></button>
                    <button type="button" class="t-btn" onclick="deleteSelected()" data-i18n-title="docker_btn_del" title="Excluir (Delete)"><i class="fas fa-trash text-red-600"></i></button>
                </div>
            </div>
        </aside>

        <!-- Collapsible Vertical Tabs on Far Right (Hints, Objects, etc.) -->
        <div class="docker-vertical-tabs">
            <button type="button" class="v-tab-btn" onclick="switchRightTab('hints')" data-i18n="hints_title">Dicas</button>
            <button type="button" class="v-tab-btn active" onclick="switchRightTab('objects')" data-i18n="docker_title">Objetos</button>
            <button type="button" class="v-tab-btn" onclick="switchRightTab('media')">Mídia</button>
        </div>

        <!-- Far Right: Official Vertical Corel Color Palette (Clique Esq: Fill | Clique Dir: Stroke) -->
        <aside class="corel-vertical-palette" id="corelVerticalPalette">
            <button type="button" class="palette-arrow-btn" onclick="scrollPalette(-1)" title="Rolar Paleta Acima"><i class="fas fa-chevron-up"></i></button>
            <div class="vertical-swatches-scroll" id="verticalPaletteSwatches">
                <!-- Swatches injected by JS -->
            </div>
            <button type="button" class="palette-arrow-btn" onclick="scrollPalette(1)" title="Rolar Paleta Abaixo"><i class="fas fa-chevron-down"></i></button>
        </aside>
    </div>

    <!-- ==================== 3. Bottom Status Bar ==================== -->
    <footer class="corel-status-bar">
        <div class="status-left">
            <div class="status-item" id="statusDimensions">
                <i class="fas fa-ruler-combined"></i> <span>297.0 x 210.0 mm</span>
            </div>
            <div class="status-item" id="statusCoordinates">
                <i class="fas fa-mouse-pointer"></i> <span>X: 0.0 mm &nbsp; Y: 0.0 mm</span>
            </div>
            <div class="status-item" id="statusContextHint" style="color:#2563eb; font-weight:500;">
                <span data-i18n="status_help_text">Arraste as quinas para redimensionar mantendo proporção (SHIFT para distorcer livremente).</span>
            </div>
        </div>

        <div class="status-right">
            <!-- Document Colors Used -->
            <div class="status-doc-palette">
                <span class="doc-palette-title" data-i18n="status_color_palette_name">Cores do Documento:</span>
                <div id="docRecentColors" style="display:flex; gap:2px;"></div>
            </div>
            <span style="opacity:0.6;">CorelClone Pro 2026 (4uLabs)</span>
        </div>
    </footer>
</div>

<!-- ==================== PowerTRACE™ Modal Dialog ==================== -->
<div id="powertraceModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 580px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-bolt text-amber-500"></i> <span data-i18n="trace_title">Corel PowerTRACE™ — Vetorizador de Bitmap</span></div>
            <button type="button" class="btn-win-ctl" onclick="closePowerTraceDialog()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Área de Status / Miniatura da Imagem -->
            <div id="traceImagePreviewArea" style="display:flex; gap:12px; align-items:center; padding:10px; border:1px solid #e0e0e0; border-radius:4px; background:#f9f9f9; margin-bottom:12px;">
                <div id="traceThumbContainer" style="width:68px; height:68px; border:1px solid #ccc; background:#fff; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:3px; flex-shrink:0;">
                    <img id="traceThumbImg" src="" style="max-width:100%; max-height:100%; object-fit:contain; display:none;">
                    <i id="traceThumbPlaceholder" class="fas fa-image text-gray-400" style="font-size:26px;"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div id="traceImageTitle" style="font-weight:700; font-size:12px; color:#222;">Nenhuma imagem selecionada</div>
                    <div id="traceImageSubtitle" style="font-size:11px; color:#666; margin-top:2px;">Selecione um bitmap na tela ou escolha uma imagem do seu computador para vetorizar em curvas.</div>
                    <div style="margin-top:6px; display:flex; gap:8px;">
                        <button type="button" class="btn-secondary" style="font-size:11px; padding:3px 9px;" onclick="document.getElementById('traceModalFileInput').click()">
                            <i class="fas fa-folder-open text-amber-600"></i> Escolher Imagem (PNG/JPG/WEBP)...
                        </button>
                        <input type="file" id="traceModalFileInput" accept="image/*" style="display:none;" onchange="handleTraceModalFileUpload(this)">
                    </div>
                </div>
            </div>

            <!-- Presets de Rastreamento CorelDRAW -->
            <div style="font-size:11.5px; font-weight:700; color:#333; margin-bottom:6px;">Modo de Rastreamento (Presets Corel):</div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; margin-bottom:12px;">
                <label style="display:flex; gap:8px; align-items:flex-start; padding:8px; border:1px solid #ddd; border-radius:3px; background:#fafafa; cursor:pointer;">
                    <input type="radio" name="tracePreset" value="logo" checked style="margin-top:2px;" onchange="updateTracePresetControls()">
                    <div>
                        <strong style="display:block; font-size:11.5px; color:#222;">Logotipo / Clipart</strong>
                        <span style="font-size:10.5px; color:#666;">Contornos nítidos e cores sólidas. Ideal para plotagem de recorte, serigrafia e logos.</span>
                    </div>
                </label>
                <label style="display:flex; gap:8px; align-items:flex-start; padding:8px; border:1px solid #ddd; border-radius:3px; background:#fafafa; cursor:pointer;">
                    <input type="radio" name="tracePreset" value="lineart" style="margin-top:2px;" onchange="updateTracePresetControls()">
                    <div>
                        <strong style="display:block; font-size:11.5px; color:#222;">Arte de Linha (P&B)</strong>
                        <span style="font-size:10.5px; color:#666;">Alto contraste preto e branco. Para silhuetas, carimbos, desenhos e assinaturas.</span>
                    </div>
                </label>
                <label style="display:flex; gap:8px; align-items:flex-start; padding:8px; border:1px solid #ddd; border-radius:3px; background:#fafafa; cursor:pointer;">
                    <input type="radio" name="tracePreset" value="detailed" style="margin-top:2px;" onchange="updateTracePresetControls()">
                    <div>
                        <strong style="display:block; font-size:11.5px; color:#222;">Logotipo Detalhado</strong>
                        <span style="font-size:10.5px; color:#666;">Maior precisão de curvas e formas para logos complexas com mais cores.</span>
                    </div>
                </label>
                <label style="display:flex; gap:8px; align-items:flex-start; padding:8px; border:1px solid #ddd; border-radius:3px; background:#fafafa; cursor:pointer;">
                    <input type="radio" name="tracePreset" value="photo" style="margin-top:2px;" onchange="updateTracePresetControls()">
                    <div>
                        <strong style="display:block; font-size:11.5px; color:#222;">Alta Fidelidade (Foto)</strong>
                        <span style="font-size:10.5px; color:#666;">Mais camadas de cores para ilustrações e fotos multicoloridas.</span>
                    </div>
                </label>
            </div>

            <!-- Controles Adicionais -->
            <div style="background:#f5f7fa; border:1px solid #e1e4e8; border-radius:4px; padding:10px; margin-bottom:12px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                    <label style="font-size:11px; font-weight:600; color:#333;">Número Máximo de Cores:</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="range" id="traceNumColors" min="2" max="32" value="8" style="width:130px;" oninput="document.getElementById('traceNumColorsVal').textContent = this.value">
                        <span id="traceNumColorsVal" style="font-size:11px; font-weight:700; width:22px; text-align:right;">8</span>
                    </div>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <label style="font-size:11px; font-weight:600; color:#333;">Nível de Suavização das Curvas:</label>
                    <select id="traceSmoothness" class="corel-select" style="width:160px; font-size:11px;">
                        <option value="high">Alta (Linhas Suaves / Plotter)</option>
                        <option value="medium" selected>Média (Equilibrada)</option>
                        <option value="low">Baixa (Mais Detalhes)</option>
                    </select>
                </div>
            </div>

            <!-- Opções CorelDRAW de Finalização -->
            <div style="display:flex; flex-direction:column; gap:6px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:11px; color:#222; cursor:pointer;">
                    <input type="checkbox" id="traceRemoveBg" checked> <strong>Remover cor de fundo</strong> (descarta automaticamente fundo branco/claro da logo)
                </label>
                <label style="display:flex; align-items:center; gap:6px; font-size:11px; color:#222; cursor:pointer;">
                    <input type="checkbox" id="traceRemoveOriginal" checked> Remover imagem bitmap original após vetorização
                </label>
                <label style="display:flex; align-items:center; gap:6px; font-size:11px; color:#222; cursor:pointer;">
                    <input type="checkbox" id="traceGroupResult" checked> Agrupar vetores gerados em um único objeto (Ctrl+G)
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closePowerTraceDialog()"><span data-i18n="trace_cancel">Cancelar</span></button>
            <button type="button" class="btn-primary" onclick="runPowerTrace()" style="background:#d97706; border-color:#b45309; padding:7px 16px;">
                <i class="fas fa-magic"></i> <span data-i18n="trace_run">Rastrear e Gerar Curvas Vetoriais</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== Contour Tool (Borda / Linha de Corte) Modal ==================== -->
<div id="contourModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 480px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-bullseye text-pink-600"></i> <span data-i18n="contour_title">Ferramenta Contorno / Borda de Corte (Contour)</span></div>
            <button type="button" class="btn-win-ctl" onclick="closeContourDialog()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:11.5px; color:#444; margin-bottom:12px;">Crie uma borda de sangria, base para adesivo (*sticker*) ou linha de corte para plotter ao redor dos objetos selecionados.</p>
            
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:4px; padding:10px; margin-bottom:12px; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Estilo da Borda:</label>
                    <select id="contourStyle" class="corel-select" style="width:200px; font-size:11px;">
                        <option value="sticker" selected>Borda de Adesivo (Preenchida)</option>
                        <option value="cutline">Linha de Corte / Plotter (Apenas Contorno)</option>
                    </select>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Espessura / Offset (mm):</label>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="number" id="contourOffsetMm" class="prop-input" value="3.0" min="0.5" max="50" step="0.5" style="width:70px;">
                        <span style="font-size:11px; color:#666;">mm</span>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Cor da Borda / Linha:</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="color" id="contourColorInput" value="#ffffff" style="width:36px; height:24px; border:1px solid #ccc; border-radius:3px; cursor:pointer;">
                        <select id="contourQuickColors" class="corel-select" style="width:140px; font-size:10.5px;" onchange="document.getElementById('contourColorInput').value = this.value">
                            <option value="#ffffff" selected>Branco (Adesivo)</option>
                            <option value="#ff00ff">Magenta (Linha de Corte)</option>
                            <option value="#000000">Preto</option>
                            <option value="#ffd600">Amarelo Ouro</option>
                            <option value="#00e5ff">Ciano</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Cantos:</label>
                    <select id="contourCorners" class="corel-select" style="width:200px; font-size:11px;">
                        <option value="round" selected>Arredondados (Para Plotter / Lâmina)</option>
                        <option value="miter">Cantos Vivos (Retos)</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:6px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:11px; color:#222; cursor:pointer;">
                    <input type="checkbox" id="contourGroupWithOriginal" checked> Agrupar borda gerada com o objeto original (Ctrl+G)
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeContourDialog()"><span data-i18n="contour_cancel">Cancelar</span></button>
            <button type="button" class="btn-primary" onclick="applyContourFromModal()" style="background:#db2777; border-color:#be185d; padding:7px 16px;">
                <i class="fas fa-bullseye"></i> <span data-i18n="contour_apply">Aplicar Contorno</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== Fountain Fill (Gradiente / Degradê) Modal ==================== -->
<div id="fountainFillModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 500px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-fill text-purple-600"></i> <span data-i18n="fountain_title">Preenchimento Gradiente (Fountain Fill — F11)</span></div>
            <button type="button" class="btn-win-ctl" onclick="closeFountainFillDialog()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Barra de Pré-Visualização Dinâmica do Gradiente -->
            <div style="margin-bottom:12px;">
                <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:4px;">Pré-visualização:</label>
                <div id="fountainPreviewBar" style="height:36px; border-radius:4px; border:1px solid #ccc; box-shadow:inset 0 1px 3px rgba(0,0,0,0.15); background:linear-gradient(90deg, #ffd700, #b8860b);"></div>
            </div>

            <!-- Tipo de Gradiente e Ângulo -->
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:4px; padding:10px; margin-bottom:12px; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Tipo de Gradiente:</label>
                    <div style="display:flex; gap:12px;">
                        <label style="font-size:11.5px; cursor:pointer; display:flex; align-items:center; gap:4px;">
                            <input type="radio" name="fountainType" value="linear" checked onchange="updateFountainPreview()"> Linear
                        </label>
                        <label style="font-size:11.5px; cursor:pointer; display:flex; align-items:center; gap:4px;">
                            <input type="radio" name="fountainType" value="radial" onchange="updateFountainPreview()"> Radial (Circular)
                        </label>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Ângulo:</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="range" id="fountainAngleSlider" min="0" max="360" value="90" style="width:130px;" oninput="document.getElementById('fountainAngleInput').value = this.value; updateFountainPreview();">
                        <input type="number" id="fountainAngleInput" value="90" min="0" max="360" style="width:55px;" class="prop-input" oninput="document.getElementById('fountainAngleSlider').value = this.value; updateFountainPreview();">
                        <span style="font-size:11px; color:#666;">°</span>
                    </div>
                </div>

                <!-- Cores Inicial e Final -->
                <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px; border-top:1px dashed #ddd;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <label style="font-size:11px; font-weight:600; color:#333;">De (Inicial):</label>
                        <input type="color" id="fountainColor1" value="#ffd700" style="width:32px; height:24px; border:1px solid #ccc; border-radius:3px; cursor:pointer;" onchange="updateFountainPreview()">
                    </div>
                    <button type="button" class="btn-secondary" style="padding:2px 8px; font-size:11px;" onclick="swapFountainColors()" title="Inverter Cores"><i class="fas fa-exchange-alt"></i></button>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <label style="font-size:11px; font-weight:600; color:#333;">Para (Final):</label>
                        <input type="color" id="fountainColor2" value="#b8860b" style="width:32px; height:24px; border:1px solid #ccc; border-radius:3px; cursor:pointer;" onchange="updateFountainPreview()">
                    </div>
                </div>
            </div>

            <!-- Presets Clássicos Corel -->
            <div>
                <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:6px;">Estilos Clássicos do CorelDRAW:</label>
                <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:6px;">
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#ffd700', '#b8860b', 90)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #ffd700, #b8860b);"></span> Ouro Real
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#ffffff', '#808080', 90)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #ffffff, #808080);"></span> Prata
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#ff512f', '#dd2476', 45)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #ff512f, #dd2476);"></span> Pôr do Sol
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#00c6ff', '#0072ff', 90)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #00c6ff, #0072ff);"></span> Azul Royal
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#11998e', '#38ef7d', 90)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #11998e, #38ef7d);"></span> Esmeralda
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#f857a6', '#ff5858', 45)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #f857a6, #ff5858);"></span> Rubro
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#ff8c00', '#e52d27', 90)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #ff8c00, #e52d27);"></span> Fogo
                    </button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:4px; display:flex; align-items:center; gap:4px;" onclick="applyFountainPreset('#434343', '#000000', 90)">
                        <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:linear-gradient(135deg, #434343, #000000);"></span> Carbono
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeFountainFillDialog()"><span data-i18n="fountain_cancel">Cancelar</span></button>
            <button type="button" class="btn-primary" onclick="applyFountainFillFromModal()" style="background:#7c3aed; border-color:#6d28d9; padding:7px 16px;">
                <i class="fas fa-fill"></i> <span data-i18n="fountain_apply">Aplicar Gradiente</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== QR Code Generator Modal ==================== -->
<div id="qrcodeModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 480px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-qrcode text-emerald-600"></i> <span data-i18n="qr_title">Inserir Código QR Code Vetorial</span></div>
            <button type="button" class="btn-win-ctl" onclick="closeQrCodeDialog()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:10px;">
                <label style="font-size:11.5px; font-weight:600; color:#333; display:block; margin-bottom:4px;">Tipo de Conteúdo:</label>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn-secondary" style="font-size:11px; padding:3px 8px;" onclick="setQrType('url')"><i class="fas fa-link text-blue-600"></i> Link / Site</button>
                    <button type="button" class="btn-secondary" style="font-size:11px; padding:3px 8px;" onclick="setQrType('whatsapp')"><i class="fab fa-whatsapp text-green-600"></i> WhatsApp</button>
                    <button type="button" class="btn-secondary" style="font-size:11px; padding:3px 8px;" onclick="setQrType('pix')"><i class="fas fa-money-bill-wave text-teal-600"></i> Chave PIX</button>
                    <button type="button" class="btn-secondary" style="font-size:11px; padding:3px 8px;" onclick="setQrType('text')"><i class="fas fa-font"></i> Texto</button>
                </div>
            </div>

            <div style="margin-bottom:10px;">
                <label id="qrContentLabel" style="font-size:11.5px; font-weight:600; color:#333; display:block; margin-bottom:4px;">URL do Site ou Link:</label>
                <textarea id="qrContentInput" class="objects-search-input" style="height:54px; padding:6px; resize:none; font-family:monospace; font-size:11.5px;" placeholder="https://seusite.com.br"></textarea>
            </div>

            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:4px; padding:10px; margin-bottom:10px; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Tamanho Inicial na Prancheta:</label>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="number" id="qrSizeInput" class="prop-input" value="40" min="10" max="300" style="width:60px;">
                        <span style="font-size:11px; color:#666;">mm</span>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Cor dos Módulos (QR):</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="color" id="qrColorDark" value="#000000" style="width:32px; height:24px; border:1px solid #ccc; border-radius:3px; cursor:pointer;">
                        <label style="font-size:11px; color:#555; display:flex; align-items:center; gap:4px; cursor:pointer;">
                            <input type="checkbox" id="qrTransparentBg" checked> Fundo Transparente (Sem quadrado branco)
                        </label>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Correção de Erro (ECC):</label>
                    <select id="qrEccLevel" class="corel-select" style="width:160px; font-size:11px;">
                        <option value="M" selected>Médio (M - 15%)</option>
                        <option value="L">Baixo (L - 7%)</option>
                        <option value="Q">Alto (Q - 25%)</option>
                        <option value="H">Máximo (H - 30% p/ Logos)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeQrCodeDialog()"><span data-i18n="qr_cancel">Cancelar</span></button>
            <button type="button" class="btn-primary" onclick="generateAndInsertQrCode()" style="background:#059669; border-color:#047857; padding:7px 16px;">
                <i class="fas fa-qrcode"></i> <span data-i18n="qr_insert">Inserir na Prancheta</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== Drop Shadow Modal ==================== -->
<div id="dropShadowModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 480px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-cloud-moon text-indigo-600"></i> <span data-i18n="shadow_title">Sombra Projetada (Drop Shadow)</span></div>
            <button type="button" class="btn-win-ctl" onclick="closeDropShadowDialog()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:11.5px; color:#444; margin-bottom:12px;">Aplique uma sombra suave, perspectiva ou efeito de brilho nos objetos ou textos selecionados.</p>

            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:4px; padding:10px; margin-bottom:12px; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Deslocamento Horizontal (X):</label>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="range" id="shadowDxSlider" min="-40" max="40" value="6" style="width:110px;" oninput="document.getElementById('shadowDxVal').textContent = this.value">
                        <span id="shadowDxVal" style="font-size:11px; font-weight:700; width:26px; text-align:right;">6</span>px
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Deslocamento Vertical (Y):</label>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="range" id="shadowDySlider" min="-40" max="40" value="6" style="width:110px;" oninput="document.getElementById('shadowDyVal').textContent = this.value">
                        <span id="shadowDyVal" style="font-size:11px; font-weight:700; width:26px; text-align:right;">6</span>px
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Desfoque / Nevoamento (Blur):</label>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="range" id="shadowBlurSlider" min="0" max="30" value="8" style="width:110px;" oninput="document.getElementById('shadowBlurVal').textContent = this.value">
                        <span id="shadowBlurVal" style="font-size:11px; font-weight:700; width:26px; text-align:right;">8</span>px
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Opacidade da Sombra:</label>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="range" id="shadowOpacitySlider" min="10" max="100" value="50" style="width:110px;" oninput="document.getElementById('shadowOpacityVal').textContent = this.value">
                        <span id="shadowOpacityVal" style="font-size:11px; font-weight:700; width:26px; text-align:right;">50</span>%
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:11.5px; font-weight:600; color:#333;">Cor da Sombra:</label>
                    <input type="color" id="shadowColorInput" value="#000000" style="width:36px; height:24px; border:1px solid #ccc; border-radius:3px; cursor:pointer;">
                </div>
            </div>

            <!-- Presets Rápidos -->
            <div>
                <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:6px;">Estilos Prontos:</label>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:3px 8px;" onclick="applyShadowPresetValues(6, 6, 8, 50, '#000000')">Padrão Corel</button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:3px 8px;" onclick="applyShadowPresetValues(0, 8, 16, 40, '#000000')">Suave / Flutuante</button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:3px 8px;" onclick="applyShadowPresetValues(3, 3, 2, 75, '#000000')">Dura / Vinil</button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:3px 8px;" onclick="applyShadowPresetValues(0, 0, 12, 80, '#00e5ff')">Brilho Ciano (Glow)</button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:3px 8px;" onclick="applyShadowPresetValues(0, 0, 12, 80, '#ffd700')">Brilho Dourado</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="removeDropShadowFromSelected()" style="color:#dc2626; margin-right:auto;"><i class="fas fa-trash"></i> <span data-i18n="shadow_remove">Remover Sombra</span></button>
            <button type="button" class="btn-secondary" onclick="closeDropShadowDialog()"><span data-i18n="shadow_cancel">Cancelar</span></button>
            <button type="button" class="btn-primary" onclick="applyDropShadowFromModal()" style="background:#4f46e5; border-color:#4338ca; padding:7px 16px;">
                <i class="fas fa-check"></i> <span data-i18n="shadow_apply">Aplicar Sombra</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== Print / Pre-press Export Modal ==================== -->
<div id="printExportModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 520px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-print text-red-600"></i> <span data-i18n="prepress_title">Preparar para Impressão / Gráfica (Pré-impressão)</span></div>
            <button type="button" class="btn-win-ctl" onclick="closePrintExportDialog()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:11.5px; color:#444; margin-bottom:12px;">
                Adicione sangria de segurança, marcas de corte de guilhotina e miras de registro padrão da indústria gráfica (CorelDRAW Pre-press).
            </p>

            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-radius:4px; padding:12px; margin-bottom:12px; display:flex; flex-direction:column; gap:10px;">
                <!-- Sangria -->
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <label style="font-size:11.5px; font-weight:600; color:#333; display:block;">Sangria de Corte (Bleed):</label>
                        <span style="font-size:10.5px; color:#777;">Expansão externa para corte perfeito na guilhotina</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <input type="number" id="printBleedInput" class="prop-input" value="3" min="0" max="30" step="1" style="width:55px; text-align:right;">
                        <span style="font-size:11px; font-weight:600; color:#444;">mm</span>
                    </div>
                </div>

                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:2px 7px;" onclick="document.getElementById('printBleedInput').value = '0'">0 mm (Sem Sangria)</button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:2px 7px;" onclick="document.getElementById('printBleedInput').value = '3'">3 mm (Padrão Gráfica)</button>
                    <button type="button" class="btn-secondary" style="font-size:10.5px; padding:2px 7px;" onclick="document.getElementById('printBleedInput').value = '5'">5 mm (Grande Formato)</button>
                </div>

                <div style="height:1px; background:#e5e7eb; margin:2px 0;"></div>

                <!-- Opções de Marcas Gráficas -->
                <label style="font-size:11.5px; font-weight:600; color:#333;">Marcas e Guias de Impressão:</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    <label style="font-size:11px; color:#444; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" id="chkCropMarks" checked> Marcas de Corte (Cantos)
                    </label>
                    <label style="font-size:11px; color:#444; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" id="chkRegistrationMarks" checked> Miras de Registro (Alvos CMYK)
                    </label>
                    <label style="font-size:11px; color:#444; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" id="chkColorBars" checked> Barra de Cores (Calibração)
                    </label>
                    <label style="font-size:11px; color:#444; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" id="chkJobInfo" checked> Informações do Arquivo (Data/Hora)
                    </label>
                </div>
            </div>

            <!-- Preview Box / Info -->
            <div id="printSheetPreviewInfo" style="font-size:11px; color:#555; background:#eff6ff; border:1px solid #bfdbfe; border-radius:4px; padding:8px 12px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-info-circle text-blue-600" style="font-size:14px;"></i>
                <span id="printSheetSummaryText">Tamanho da prancheta atual. Todas as marcas e sangrias serão calculadas milimetricamente.</span>
            </div>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <button type="button" class="btn-secondary" onclick="closePrintExportDialog()"><span data-i18n="prepress_close">Fechar</span></button>
            <div style="display:flex; gap:6px;">
                <button type="button" class="btn-secondary" onclick="executePrintExport('svg')" title="Baixar arquivo SVG vetorial limpo com sangria e marcas">
                    <i class="fas fa-file-code text-orange-600"></i> <span data-i18n="prepress_svg">Baixar SVG</span>
                </button>
                <button type="button" class="btn-secondary" onclick="executePrintExport('png')" title="Baixar imagem em 300 DPI">
                    <i class="fas fa-file-image text-emerald-600"></i> <span data-i18n="prepress_png">Baixar PNG 300 DPI</span>
                </button>
                <button type="button" class="btn-primary" onclick="executePrintExport('print')" style="background:#dc2626; border-color:#b91c1c; padding:7px 14px;">
                    <i class="fas fa-print"></i> <span data-i18n="prepress_print">Imprimir / PDF Vetorial</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== Local Bridge Modal ==================== -->
<div id="localBridgeModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 500px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-desktop text-cyan-600"></i> <span data-i18n="bridge_title">CorelClone App Local (100% Vetorial)</span></div>
            <button type="button" class="btn-win-ctl" onclick="closeLocalBridgeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:6px; padding:12px; margin-bottom:12px; display:flex; gap:10px; align-items:flex-start;">
                <i class="fas fa-info-circle text-sky-600" style="font-size:18px; margin-top:2px;"></i>
                <div style="font-size:12px; color:#0369a1; line-height:1.45;">
                    <strong>Para que serve o App Local?</strong><br>
                    Permite abrir arquivos proprietários <strong>.CDR do CorelDRAW</strong> com 100% de nós Bézier, curvas vetoriais nativas e camadas reais, usando o conversor do seu próprio computador.
                </div>
            </div>

            <p style="font-size:11.5px; color:#444; margin-bottom:10px;">
                O servidor local (porta 54321) não foi detectado em execução no momento nesta máquina.
            </p>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:10px; margin-bottom:12px;">
                <div style="font-size:11px; font-weight:700; color:#334155; margin-bottom:4px;">Como iniciar no seu computador:</div>
                <div style="font-size:11px; color:#64748b; margin-bottom:6px;">No terminal (Linux / Mac / Windows), execute:</div>
                <code style="display:block; background:#1e293b; color:#38bdf8; padding:7px 10px; border-radius:4px; font-size:11px; font-family:monospace;">python3 corel_bridge.py</code>
            </div>

            <div style="font-size:11px; color:#666;">
                💡 <em>Dica: Você pode continuar usando o CorelClone normalmente aqui na Web. Para imagens, cliparts e logos, o <strong>PowerTRACE™</strong> vetoriza direto no navegador sem precisar do App Local!</em>
            </div>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
            <button type="button" class="btn-secondary" onclick="closeLocalBridgeModal()"><span data-i18n="bridge_continue">Continuar na Web</span></button>
            <button type="button" class="btn-primary" onclick="openLocalBridgeApp()" style="background:#0284c7; border-color:#0369a1; padding:7px 16px;">
                <i class="fas fa-sync-alt"></i> <span data-i18n="bridge_connect">Tentar Conectar</span>
            </button>
        </div>
    </div>
</div>

<!-- ==================== Download Desktop App Modal ==================== -->
<div id="downloadDesktopModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 680px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-desktop text-emerald-600"></i> <span data-i18n="desktop_title">Escolha a melhor opção para seu trabalho</span></div>
            <button type="button" class="btn-win-ctl" onclick="closeDownloadDesktopModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="padding:16px;">

            <!-- Comparativo Rápido e Objetivo -->
            <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:10px 14px; margin-bottom:14px;">
                <div style="font-size:12px; font-weight:700; color:#1e293b; margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-info-circle text-sky-600"></i> <span data-i18n="desktop_comp_title">Comparativo rápido entre as versões:</span>
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:10px; font-size:11px; line-height:1.45;">
                    <div style="background:#ffffff; border:1px solid #bae6fd; border-radius:6px; padding:8px 10px;">
                        <span style="font-weight:700; color:#0369a1; display:block; margin-bottom:4px;" data-i18n="desktop_comp_desk_title">🖥️ App de Computador (Desktop):</span>
                        <div style="color:#0f172a;"><i class="fas fa-check text-emerald-600"></i> <strong data-i18n="desktop_comp_desk_1">Abre .CDR original em curvas</strong> <span data-i18n="desktop_comp_desk_1_sub">(nós Bézier e camadas).</span></div>
                        <div style="color:#0f172a;"><i class="fas fa-check text-emerald-600"></i> <strong data-i18n="desktop_comp_desk_2">Vetoriza imagens PNG/JPG</strong> <span data-i18n="desktop_comp_desk_2_sub">com PowerTRACE™.</span></div>
                        <div style="color:#0f172a;"><i class="fas fa-check text-emerald-600"></i> <strong data-i18n="desktop_comp_desk_3">100% Offline</strong> <span data-i18n="desktop_comp_desk_3_sub">e sem limites de arquivo.</span></div>
                    </div>
                    <div style="background:#ffffff; border:1px solid #bbf7d0; border-radius:6px; padding:8px 10px;">
                        <span style="font-weight:700; color:#15803d; display:block; margin-bottom:4px;" data-i18n="desktop_comp_web_title">🌐 WebApp / PWA (Navegador):</span>
                        <div style="color:#0f172a;"><i class="fas fa-check text-emerald-600"></i> <strong data-i18n="desktop_comp_web_1">Vetoriza imagens PNG/JPG</strong> <span data-i18n="desktop_comp_web_1_sub">com PowerTRACE™ (100% funcional).</span></div>
                        <div style="color:#0f172a;"><i class="fas fa-check text-emerald-600"></i> <span data-i18n="desktop_comp_web_2">Cria desenhos, formas, textos e exporta PDF/SVG.</span></div>
                        <div style="color:#b91c1c;"><i class="fas fa-exclamation-triangle text-amber-600"></i> <strong data-i18n="desktop_comp_web_3">Não lê curvas de .CDR</strong> <span data-i18n="desktop_comp_web_3_sub">(abre como imagem prévia).</span></div>
                    </div>
                </div>
            </div>

            <!-- Seção 1: App de Computador (Máxima Fidelidade CDR) -->
            <div style="background:#f8fafc; border:2px solid #0284c7; border-radius:8px; padding:14px; margin-bottom:14px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; gap:10px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="background:#0284c7; color:#fff; font-size:10px; font-weight:800; padding:2px 7px; border-radius:3px; text-transform:uppercase;" data-i18n="desktop_rec_tag">Recomendado para Gráfica</span>
                            <h3 style="font-size:13px; font-weight:700; color:#0f172a; margin:0;" data-i18n="desktop_sec1_title">Aplicativo para Computador (Desktop)</h3>
                        </div>
                        <p style="font-size:11.5px; color:#475569; margin:4px 0 0 0;" data-i18n="desktop_sec1_desc">
                            Possui o motor nativo completo: abre <code>.CDR</code> com <strong>100% de nós Bézier e camadas originais prontas para edição</strong>, sem perda de qualidade.
                        </p>
                    </div>
                    <span id="detectedOsBadge" style="font-size:10px; font-weight:700; background:#dcfce7; color:#15803d; border:1px solid #86efac; border-radius:20px; padding:2px 8px; white-space:nowrap;">
                        <i class="fas fa-check-circle"></i> Detectando SO...
                    </span>
                </div>

                <!-- Cards Grid -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:10px;">
                    <!-- Card Windows -->
                    <div id="cardWin" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:10px; text-align:center; display:flex; flex-direction:column; justify-content:space-between;">
                        <div>
                            <i class="fab fa-windows text-sky-500" style="font-size:24px; margin-bottom:4px;"></i>
                            <div style="font-size:12px; font-weight:700; color:#1e293b;">Windows</div>
                            <span style="font-size:10px; color:#64748b; display:block; margin-bottom:8px;" data-i18n="desktop_win_sub">Windows 10 / 11 (64-Bit)</span>
                        </div>
                        <a href="https://github.com/4u-Labs/corel/releases/latest/download/CorelClone-Setup.exe" target="_blank" class="btn btn-primary" style="background:#0284c7; border-color:#0369a1; text-decoration:none; font-size:11px; padding:6px 8px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; gap:5px; color:#fff; font-weight:600;">
                            <i class="fas fa-download"></i> <span data-i18n="desktop_win_btn">Baixar .EXE</span>
                        </a>
                    </div>

                    <!-- Card macOS -->
                    <div id="cardMac" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:10px; text-align:center; display:flex; flex-direction:column; justify-content:space-between;">
                        <div>
                            <i class="fab fa-apple text-slate-800" style="font-size:24px; margin-bottom:4px;"></i>
                            <div style="font-size:12px; font-weight:700; color:#1e293b;">macOS</div>
                            <span style="font-size:10px; color:#64748b; display:block; margin-bottom:8px;" data-i18n="desktop_mac_sub">Apple M1/M2/M3 & Intel</span>
                        </div>
                        <a href="https://github.com/4u-Labs/corel/releases/latest/download/CorelClone.dmg" target="_blank" class="btn btn-primary" style="background:#334155; border-color:#1e293b; text-decoration:none; font-size:11px; padding:6px 8px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; gap:5px; color:#fff; font-weight:600;">
                            <i class="fas fa-download"></i> <span data-i18n="desktop_mac_btn">Baixar .DMG</span>
                        </a>
                    </div>

                    <!-- Card Linux -->
                    <div id="cardLinux" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:10px; text-align:center; display:flex; flex-direction:column; justify-content:space-between;">
                        <div>
                            <i class="fab fa-linux text-amber-500" style="font-size:24px; margin-bottom:4px;"></i>
                            <div style="font-size:12px; font-weight:700; color:#1e293b;">Linux</div>
                            <span style="font-size:10px; color:#64748b; display:block; margin-bottom:8px;" data-i18n="desktop_linux_sub">Ubuntu, Zorin, Mint, Debian</span>
                        </div>
                        <a href="https://github.com/4u-Labs/corel/releases/latest/download/CorelClone.AppImage" target="_blank" class="btn btn-primary" style="background:#d97706; border-color:#b45309; text-decoration:none; font-size:11px; padding:6px 8px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; gap:5px; color:#fff; font-weight:600;">
                            <i class="fas fa-download"></i> <span data-i18n="desktop_linux_btn">Baixar .AppImage</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Seção 2: WebApp / PWA (Versão Rápida sem Instalador) -->
            <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div style="flex:1; min-width:260px;">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:3px;">
                        <span style="background:#059669; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:3px; text-transform:uppercase;" data-i18n="desktop_sec2_tag">Sem Download</span>
                        <strong style="font-size:12.5px; color:#166534;" data-i18n="desktop_sec2_title">Ou instale como WebApp (PWA) direto no navegador</strong>
                    </div>
                    <div style="font-size:11.5px; color:#15803d; line-height:1.4;" data-i18n="desktop_sec2_desc">
                        Instalação instantânea com 1 clique (sem arquivos .exe). Cria artes, degradês, QR Code e <strong>vetoriza imagens PNG/JPG com o PowerTRACE™ normalmente</strong>.
                    </div>
                    <div style="font-size:10.5px; color:#991b1b; margin-top:6px; background:#fee2e2; border:1px solid #fecaca; border-radius:4px; padding:4px 8px; display:inline-block; line-height:1.35;" data-i18n="desktop_sec2_note">
                        ℹ️ <em>Nota técnica:</em> Arquivos <code>.CDR</code> abrem apenas como prévia visual no navegador. Se você precisa editar nós e curvas do <code>.CDR</code> original, use o <strong>App de Computador</strong> acima.
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="installPWA()" style="background:#059669; border-color:#047857; font-size:12px; padding:9px 16px; font-weight:700; cursor:pointer; white-space:nowrap; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                    <i class="fas fa-plus-circle"></i> <span data-i18n="pwa_install">Instalar WebApp</span>
                </button>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeDownloadDesktopModal()"><span data-i18n="desktop_close">Fechar</span></button>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Scripts -->
<script src="corel_i18n.js?v=<?php echo $v; ?>"></script>
<script src="script.js?v=<?php echo $v; ?>"></script>
</body>
</html>
