<?php
// Garantir redirecionamento com barra final caso seja acessado sem ela (ex: /app/corel2 -> /app/corel2/)
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
                <span class="corel-app-icon"><img src="corelicon.png" alt="CorelClone"></span>
                <span class="window-title-text" id="windowTitleText">CorelClone Pro 2026 (64-Bit) — [Documento 1] @ 100%</span>
            </div>
            <div class="window-title-controls">
                <button type="button" class="btn-win-ctl" title="Minimizar"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn-win-ctl" title="Maximizar"><i class="far fa-square"></i></button>
                <button type="button" class="btn-win-ctl close" title="Fechar"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Main Menu Bar -->
        <nav class="menu-bar">
            <!-- Arquivo -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Arquivo</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="newDocument()"><i class="fas fa-file"></i> Novo <span class="shortcut">Ctrl+N</span></button>
                    <button type="button" class="dropdown-item" onclick="document.getElementById('importFileInput').click()"><i class="fas fa-folder-open text-emerald-600"></i> Abrir .CDR / .PDF / SVG... <span class="shortcut">Ctrl+O</span></button>
                    <button type="button" class="dropdown-item" onclick="saveProjectJSON()"><i class="fas fa-save"></i> Salvar Projeto <span class="shortcut">Ctrl+S</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="exportDocument('svg')"><i class="fas fa-file-code"></i> Exportar como SVG...</button>
                    <button type="button" class="dropdown-item" onclick="exportDocument('pdf')"><i class="fas fa-file-pdf"></i> Exportar como PDF...</button>
                    <button type="button" class="dropdown-item" onclick="exportDocument('png')"><i class="fas fa-file-image"></i> Exportar como PNG HD...</button>
                    <div class="dropdown-separator"></div>
                    <a href="http://127.0.0.1:54321/" target="_blank" class="dropdown-item"><i class="fas fa-desktop text-cyan-600"></i> Abrir no App Local (100% CDR)</a>
                </div>
            </div>

            <!-- Editar -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Editar</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="undo()"><i class="fas fa-undo"></i> Desfazer <span class="shortcut">Ctrl+Z</span></button>
                    <button type="button" class="dropdown-item" onclick="redo()"><i class="fas fa-redo"></i> Refazer <span class="shortcut">Ctrl+Y</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="duplicateSelected()"><i class="fas fa-clone"></i> Duplicar <span class="shortcut">Ctrl+D</span></button>
                    <button type="button" class="dropdown-item" onclick="deleteSelected()"><i class="fas fa-trash"></i> Excluir <span class="shortcut">Delete</span></button>
                    <button type="button" class="dropdown-item" onclick="selectAll()"><i class="fas fa-object-group"></i> Selecionar Tudo <span class="shortcut">Ctrl+A</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="openFountainFillDialog()"><i class="fas fa-fill text-purple-600"></i> Preenchimento Gradiente... <span class="shortcut">F11</span></button>
                </div>
            </div>

            <!-- Exibir -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Exibir</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="setZoom(1.0)"><i class="fas fa-search"></i> Tamanho Real (100%)</button>
                    <button type="button" class="dropdown-item" onclick="zoomFitPage()"><i class="fas fa-expand"></i> Ajustar à Página <span class="shortcut">F4</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="toggleRulers()"><i class="fas fa-ruler"></i> Mostrar Réguas</button>
                    <button type="button" class="dropdown-item" onclick="toggleGuidelines()"><i class="fas fa-border-all"></i> Mostrar Linhas-Guia</button>
                </div>
            </div>

            <!-- Objeto -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Objeto</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="groupSelected()"><i class="fas fa-object-group"></i> Agrupar <span class="shortcut">Ctrl+G</span></button>
                    <button type="button" class="dropdown-item" onclick="ungroupSelected()"><i class="fas fa-object-ungroup"></i> Desagrupar <span class="shortcut">Ctrl+U</span></button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="convertSelectedToCurves()"><i class="fas fa-bezier-curve"></i> Converter em Curvas <span class="shortcut">Ctrl+Q</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- CorelDRAW Align & Distribute -->
                    <button type="button" class="dropdown-item" onclick="alignSelected('P')"><i class="fas fa-crosshairs text-blue-600"></i> Centralizar na Página <span class="shortcut">P</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('C')"><i class="fas fa-arrows-alt-h text-cyan-600"></i> Centralizar Horizontal <span class="shortcut">C</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('E')"><i class="fas fa-arrows-alt-v text-cyan-600"></i> Centralizar Vertical <span class="shortcut">E</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('L')"><i class="fas fa-align-left"></i> Alinhar à Esquerda <span class="shortcut">L</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('R')"><i class="fas fa-align-right"></i> Alinhar à Direita <span class="shortcut">R</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('T')"><i class="fas fa-arrow-up"></i> Alinhar pelo Topo <span class="shortcut">T</span></button>
                    <button type="button" class="dropdown-item" onclick="alignSelected('B')"><i class="fas fa-arrow-down"></i> Alinhar pela Base <span class="shortcut">B</span></button>
                    <div class="dropdown-separator"></div>
                    <!-- PowerClip CorelDRAW -->
                    <button type="button" class="dropdown-item" onclick="applyPowerClip()"><i class="fas fa-sign-in-alt text-amber-600"></i> PowerClip: Colocar no Recipiente...</button>
                    <button type="button" class="dropdown-item" onclick="extractPowerClip()"><i class="fas fa-sign-out-alt text-amber-600"></i> PowerClip: Extrair Conteúdo</button>
                    <div class="dropdown-separator"></div>
                    <!-- CorelDRAW QR Code -->
                    <button type="button" class="dropdown-item" onclick="openQrCodeDialog()"><i class="fas fa-qrcode text-emerald-600"></i> Inserir Código QR Code...</button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="orderSelected('front')"><i class="fas fa-angle-double-up"></i> Trazer para Frente <span class="shortcut">Shift+PgUp</span></button>
                    <button type="button" class="dropdown-item" onclick="orderSelected('back')"><i class="fas fa-angle-double-down"></i> Enviar para Trás <span class="shortcut">Shift+PgDn</span></button>
                </div>
            </div>

            <!-- Efeitos / Modelagem -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Modelar</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="booleanOperation('weld')"><i class="fas fa-layer-group"></i> Soldar (Weld)</button>
                    <button type="button" class="dropdown-item" onclick="booleanOperation('trim')"><i class="fas fa-cut"></i> Aparar (Trim)</button>
                    <button type="button" class="dropdown-item" onclick="booleanOperation('intersect')"><i class="fas fa-circle-notch"></i> Interseção (Intersect)</button>
                    <div class="dropdown-separator"></div>
                    <button type="button" class="dropdown-item" onclick="openContourDialog()"><i class="fas fa-bullseye text-pink-600"></i> Contorno / Borda de Adesivo...</button>
                </div>
            </div>

            <!-- Bitmap -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Bitmap</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="openPowerTraceDialog()"><i class="fas fa-bolt text-amber-500"></i> Rastreamento PowerTRACE™...</button>
                    <button type="button" class="dropdown-item" onclick="toggleImportedBgImage()"><i class="fas fa-eye-slash"></i> Ocultar Fundo / Template</button>
                </div>
            </div>

            <!-- Ajuda -->
            <div class="menu-item">
                <button type="button" class="menu-btn">Ajuda</button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" onclick="showShortcutsModal()"><i class="fas fa-keyboard"></i> Atalhos de Teclado</button>
                    <a href="suporte.php" target="_blank" class="dropdown-item"><i class="fas fa-question-circle"></i> Suporte CorelClone</a>
                </div>
            </div>
        </nav>

        <!-- Level 2: Standard Toolbar -->
        <div class="standard-toolbar">
            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="newDocument()" title="Novo (Ctrl+N)"><i class="fas fa-file"></i></button>
                <button type="button" class="t-btn" onclick="document.getElementById('importFileInput').click()" title="Abrir .CDR / .PDF / SVG (Ctrl+O)"><i class="fas fa-folder-open text-amber-600"></i></button>
                <button type="button" class="t-btn" onclick="saveProjectJSON()" title="Salvar Projeto (Ctrl+S)"><i class="fas fa-save text-blue-600"></i></button>
                <button type="button" class="t-btn" onclick="window.print()" title="Imprimir (Ctrl+P)"><i class="fas fa-print"></i></button>
            </div>

            <div class="tool-sep"></div>

            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="cutSelected()" title="Recortar (Ctrl+X)"><i class="fas fa-cut"></i></button>
                <button type="button" class="t-btn" onclick="copySelected()" title="Copiar (Ctrl+C)"><i class="fas fa-copy"></i></button>
                <button type="button" class="t-btn" onclick="pasteSelected()" title="Colar (Ctrl+V)"><i class="fas fa-paste"></i></button>
            </div>

            <div class="tool-sep"></div>

            <div class="toolbar-group">
                <button type="button" class="t-btn" onclick="undo()" title="Desfazer (Ctrl+Z)"><i class="fas fa-undo"></i></button>
                <button type="button" class="t-btn" onclick="redo()" title="Refazer (Ctrl+Y)"><i class="fas fa-redo"></i></button>
            </div>

            <div class="tool-sep"></div>

            <!-- Import / Export Actions -->
            <div class="toolbar-group">
                <button type="button" class="t-btn-labeled" onclick="exportDocument('svg')" title="Exportar vetor SVG limpo">
                    <i class="fas fa-file-code text-orange-600"></i> SVG
                </button>
                <button type="button" class="t-btn-labeled" onclick="exportDocument('pdf')" title="Exportar PDF para impressão">
                    <i class="fas fa-file-pdf text-red-600"></i> PDF
                </button>
                <button type="button" class="t-btn-labeled" onclick="exportDocument('png')" title="Exportar imagem PNG HD">
                    <i class="fas fa-file-image text-emerald-600"></i> PNG
                </button>
            </div>

            <div class="tool-sep"></div>

            <!-- Zoom Box -->
            <div class="zoom-dropdown-box">
                <label for="zoomSelect" style="margin-right:4px; font-size:11px; color:#555;"><i class="fas fa-search"></i></label>
                <select id="zoomSelect" class="corel-select" onchange="changeZoomPreset(this.value)">
                    <option value="fit">Para Ajustar (F4)</option>
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
                <button type="button" class="t-btn" onclick="alignSelected('center')" title="Centralizar na Página (P)"><i class="fas fa-crosshairs"></i></button>
                <button type="button" class="t-btn" id="btnToggleDuplicateBg" onclick="toggleImportedBgImage()" style="display:none;" title="Ocultar Imagem de Fundo Duplicada do Modelo"><i class="fas fa-eye-slash text-amber-600"></i></button>
                <button type="button" class="t-btn" id="btnQuickPowerTrace" onclick="openPowerTraceDialog()" title="PowerTRACE™ — Vetorizar Bitmap"><i class="fas fa-bolt text-amber-500"></i></button>
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
                    <button type="button" class="t-btn active" id="btnOrientLandscape" onclick="setDocOrientation('landscape')" title="Paisagem"><i class="fas fa-file-alt fa-rotate-90"></i></button>
                    <button type="button" class="t-btn" id="btnOrientPortrait" onclick="setDocOrientation('portrait')" title="Retrato"><i class="fas fa-file-alt"></i></button>
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
                    <input type="number" id="objPropX" class="prop-input" value="0" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="prop-field">
                    <label>Y:</label>
                    <input type="number" id="objPropY" class="prop-input" value="0" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="tool-sep"></div>
                <div class="prop-field">
                    <label>L:</label>
                    <input type="number" id="objPropW" class="prop-input" value="100" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="prop-field">
                    <label>A:</label>
                    <input type="number" id="objPropH" class="prop-input" value="100" onchange="updateSelectedTransformFromProp()">
                </div>
                <div class="tool-sep"></div>
                <div class="prop-field">
                    <label><i class="fas fa-redo-alt"></i></label>
                    <input type="number" id="objPropRotate" class="prop-input" style="width:50px;" value="0" onchange="updateSelectedTransformFromProp()">
                    <span style="font-size:10px; color:#666;">°</span>
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
                    <button type="button" class="t-btn" onclick="alignSelected('L')" title="Alinhar à Esquerda (L)"><i class="fas fa-align-left"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('C')" title="Centralizar Horizontal (C)"><i class="fas fa-arrows-alt-h"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('R')" title="Alinhar à Direita (R)"><i class="fas fa-align-right"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('T')" title="Alinhar pelo Topo (T)"><i class="fas fa-arrow-up" style="font-size:10px;"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('E')" title="Centralizar Vertical (E)"><i class="fas fa-arrows-alt-v"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('B')" title="Alinhar pela Base (B)"><i class="fas fa-arrow-down" style="font-size:10px;"></i></button>
                    <button type="button" class="t-btn" onclick="alignSelected('P')" title="Centralizar na Página (P)"><i class="fas fa-crosshairs text-blue-600"></i></button>
                <div class="tool-sep"></div>
                <!-- PowerClip Quick Buttons -->
                <div class="toolbar-group">
                    <button type="button" class="t-btn" onclick="applyPowerClip()" title="PowerClip: Colocar no Recipiente"><i class="fas fa-sign-in-alt text-amber-600"></i></button>
                    <button type="button" class="t-btn" id="btnExtractPowerClip" onclick="extractPowerClip()" style="display:none;" title="PowerClip: Extrair Conteúdo"><i class="fas fa-sign-out-alt text-amber-600"></i></button>
                    <button type="button" class="t-btn" onclick="openContourDialog()" title="Contorno / Borda de Adesivo e Corte (Contour)"><i class="fas fa-bullseye text-pink-600"></i></button>
                    <button type="button" class="t-btn" onclick="openFountainFillDialog()" title="Preenchimento Gradiente / Degradê (F11)"><i class="fas fa-fill text-purple-600"></i></button>
                </div>
                <div class="tool-sep"></div>
                <!-- PowerTRACE Action Button for Selected Bitmaps -->
                <button type="button" class="t-btn-primary" id="btnTraceSelected" onclick="openPowerTraceDialog()" style="display:none; gap:5px; font-size:11px; padding:3px 9px; background:#d97706; border-color:#b45309;" title="PowerTRACE: Rastrear e Vetorizar esta Imagem">
                    <i class="fas fa-bolt text-amber-200"></i> <span>Rastrear Bitmap</span>
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
                <i class="fas fa-home"></i> <span>Tela Inicial</span>
            </div>
            <div class="doc-tab active" id="tabDoc1" onclick="switchDocumentTab('page0')">
                <i class="fas fa-vector-square text-cyan-600"></i> <span>Documento 1</span>
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
            <button type="button" class="tool-btn active" id="toolBtn_select" onclick="selectTool('select')" title="Ferramenta Seleção (Espaço)">
                <i class="fas fa-mouse-pointer"></i>
            </button>
            <!-- 2. Shape Tool (F10) -->
            <button type="button" class="tool-btn" id="toolBtn_node" onclick="selectTool('node')" title="Ferramenta Forma / Nós (F10)">
                <i class="fas fa-bezier-curve"></i>
            </button>
            <!-- 3. Crop / Knife -->
            <button type="button" class="tool-btn" id="toolBtn_crop" onclick="selectTool('crop')" title="Cortar / Faca (C)">
                <i class="fas fa-crop-alt"></i>
            </button>
            <!-- 4. Zoom / Pan -->
            <button type="button" class="tool-btn" id="toolBtn_zoom" onclick="selectTool('pan')" title="Pan / Mover Tela (H)">
                <i class="fas fa-hand-paper"></i>
            </button>
            <!-- 5. Freehand / Pen -->
            <button type="button" class="tool-btn" id="toolBtn_pen" onclick="selectTool('pen')" title="Caneta Bézier / Mão Livre">
                <i class="fas fa-pen-nib"></i>
            </button>
            <!-- 6. Artistic Media / Brush -->
            <button type="button" class="tool-btn" id="toolBtn_brush" onclick="selectTool('brush')" title="Mídia Artística / Pincel">
                <i class="fas fa-paint-brush"></i>
            </button>
            <!-- 7. Rectangle (F6) -->
            <button type="button" class="tool-btn" id="toolBtn_rect" onclick="selectTool('rect')" title="Retângulo (F6)">
                <i class="far fa-square"></i>
            </button>
            <!-- 8. Ellipse (F7) -->
            <button type="button" class="tool-btn" id="toolBtn_ellipse" onclick="selectTool('ellipse')" title="Elipse (F7)">
                <i class="far fa-circle"></i>
            </button>
            <!-- 9. Star / Polygon (Y) -->
            <button type="button" class="tool-btn" id="toolBtn_star" onclick="selectTool('star')" title="Polígono / Estrela (Y)">
                <i class="far fa-star"></i>
            </button>
            <!-- 10. Text (F8) -->
            <button type="button" class="tool-btn" id="toolBtn_text" onclick="selectTool('text')" title="Texto (F8)">
                <i class="fas fa-font"></i>
            </button>
            <!-- 11. Eyedropper -->
            <button type="button" class="tool-btn" id="toolBtn_eyedropper" onclick="selectTool('eyedropper')" title="Conta-gotas de Cor">
                <i class="fas fa-eye-dropper"></i>
            </button>
            <!-- 12. Fill Tool -->
            <button type="button" class="tool-btn" id="toolBtn_fill" onclick="selectTool('fill')" title="Preenchimento Interativo (G)">
                <i class="fas fa-fill-drip"></i>
            </button>
            <!-- 13. PowerTRACE (Vetorizar Bitmap) -->
            <button type="button" class="tool-btn" id="toolBtn_trace" onclick="openPowerTraceDialog()" title="PowerTRACE™ — Vetorizar Bitmap / Logo (Curvas)">
                <i class="fas fa-bolt text-amber-500"></i>
            </button>
            <!-- 14. Contour Tool (Borda de Adesivo / Corte) -->
            <button type="button" class="tool-btn" id="toolBtn_contour" onclick="openContourDialog()" title="Contorno / Borda de Adesivo e Corte (Contour)">
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
                <div class="docker-title"><i class="fas fa-layer-group text-blue-600"></i> Objetos / Camadas</div>
                <div class="docker-controls">
                    <button type="button" class="btn-win-ctl" onclick="toggleDockerCollapse()" title="Recolher / Expandir"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="docker-body">
                <!-- Search bar -->
                <div class="objects-search-bar">
                    <input type="text" id="objectsSearchInput" class="objects-search-input" placeholder="Pesquisar objetos..." oninput="filterObjectsTree(this.value)">
                </div>

                <!-- Blending Mode & Opacity -->
                <div class="objects-blend-row">
                    <select id="blendModeSelect" class="corel-select" style="width:110px;" onchange="changeSelectedBlendMode(this.value)">
                        <option value="normal">Normal</option>
                        <option value="multiply">Multiplicar</option>
                        <option value="screen">Tela</option>
                        <option value="overlay">Sobrepor</option>
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
                    <button type="button" class="t-btn" onclick="addNewLayer()" title="Nova Camada"><i class="fas fa-plus-square text-blue-600"></i></button>
                    <button type="button" class="t-btn" onclick="duplicateSelected()" title="Duplicar Objeto"><i class="fas fa-clone text-amber-600"></i></button>
                    <button type="button" class="t-btn" onclick="deleteSelected()" title="Excluir (Delete)"><i class="fas fa-trash text-red-600"></i></button>
                </div>
            </div>
        </aside>

        <!-- Collapsible Vertical Tabs on Far Right (Hints, Objects, etc.) -->
        <div class="docker-vertical-tabs">
            <button type="button" class="v-tab-btn" onclick="switchRightTab('hints')">Dicas</button>
            <button type="button" class="v-tab-btn active" onclick="switchRightTab('objects')">Objetos</button>
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
                Segure CTRL para restringir proporção, ALT para transformar pelo centro.
            </div>
        </div>

        <div class="status-right">
            <!-- Document Colors Used -->
            <div class="status-doc-palette">
                <span class="doc-palette-title">Cores do Documento:</span>
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
            <div class="modal-title"><i class="fas fa-bolt text-amber-500"></i> Corel PowerTRACE™ — Vetorizador de Bitmap</div>
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
            <button type="button" class="btn-secondary" onclick="closePowerTraceDialog()">Cancelar</button>
            <button type="button" class="btn-primary" onclick="runPowerTrace()" style="background:#d97706; border-color:#b45309; padding:7px 16px;">
                <i class="fas fa-magic"></i> Rastrear e Gerar Curvas Vetoriais
            </button>
        </div>
    </div>
</div>

<!-- ==================== Contour Tool (Borda / Linha de Corte) Modal ==================== -->
<div id="contourModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 480px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-bullseye text-pink-600"></i> Ferramenta Contorno / Borda de Corte (Contour)</div>
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
            <button type="button" class="btn-secondary" onclick="closeContourDialog()">Cancelar</button>
            <button type="button" class="btn-primary" onclick="applyContourFromModal()" style="background:#db2777; border-color:#be185d; padding:7px 16px;">
                <i class="fas fa-bullseye"></i> Aplicar Contorno
            </button>
        </div>
    </div>
</div>

<!-- ==================== Fountain Fill (Gradiente / Degradê) Modal ==================== -->
<div id="fountainFillModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 500px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-fill text-purple-600"></i> Preenchimento Gradiente (Fountain Fill — F11)</div>
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
            <button type="button" class="btn-secondary" onclick="closeFountainFillDialog()">Cancelar</button>
            <button type="button" class="btn-primary" onclick="applyFountainFillFromModal()" style="background:#7c3aed; border-color:#6d28d9; padding:7px 16px;">
                <i class="fas fa-fill"></i> Aplicar Gradiente
            </button>
        </div>
    </div>
</div>

<!-- ==================== QR Code Generator Modal ==================== -->
<div id="qrcodeModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="width: 480px; max-width:95vw;">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-qrcode text-emerald-600"></i> Inserir Código QR Code Vetorial</div>
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
            <button type="button" class="btn-secondary" onclick="closeQrCodeDialog()">Cancelar</button>
            <button type="button" class="btn-primary" onclick="generateAndInsertQrCode()" style="background:#059669; border-color:#047857; padding:7px 16px;">
                <i class="fas fa-qrcode"></i> Inserir na Prancheta
            </button>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Scripts -->
<script src="script.js?v=<?php echo $v; ?>"></script>
</body>
</html>
