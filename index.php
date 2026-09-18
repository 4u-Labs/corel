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
    <title>CorelClone Pro 2026 — Editor Gráfico Vetorial (4U.IA.BR)</title>
    <base href="<?php echo htmlspecialchars($baseDir); ?>">

    <!-- Style & Fonts -->
    <link rel="stylesheet" href="style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Roboto:wght@400;700&family=Montserrat:wght@400;700;900&family=Playfair+Display:wght@700&family=Fira+Code:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- JSZip for CDR unzipping -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- PDF.js for client-side PDF rendering -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- Vectorization & Geometry Engines (Local with CDN Fallback) -->
    <script src="libs/imagetracer.js"></script>
    <script src="libs/paper-full.min.js"></script>
</head>
<body class="dark-theme">

    <!-- Top App Header -->
    <header class="app-header">
        <div class="brand">
            <a href="index.php" class="brand-logo" title="CorelClone Pro 2026">
                <div class="logo-icon"><i class="fas fa-vector-square"></i></div>
                <div class="brand-text">
                    <span class="brand-name">CorelClone <span class="badge-pro">PRO 2026</span></span>
                    <span class="brand-sub">Editor Vetorial Profissional</span>
                </div>
            </a>
        </div>

        <!-- Document Presets & Quick Actions -->
        <div class="header-actions">
            <!-- File Drop / Open -->
            <div class="file-drop-target" id="fileDropZone">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('importFileInput').click()" title="Abrir arquivo CDR, PDF, SVG, Imagem ou Projeto JSON">
                    <i class="fas fa-folder-open text-emerald-400"></i> <span>Abrir .CDR / .PDF / SVG</span>
                </button>
                <input type="file" id="importFileInput" accept=".cdr,.pdf,.svg,.png,.jpg,.jpeg,.webp,.json" style="display:none;">
            </div>

            <!-- Bridge Local (100% CDR/PDF) -->
            <a href="http://127.0.0.1:54321/" target="_blank" class="btn btn-secondary btn-bridge" id="btnLocalBridge" title="CorelClone Local (100% Precisão Vetorial Nativa para arquivos .CDR e .PDF)">
                <i class="fas fa-microchip text-cyan-400"></i> <span class="hidden-mobile">App Local (100% CDR/PDF)</span>
            </a>

            <!-- New / Clear -->
            <button type="button" class="btn btn-secondary btn-icon-only" onclick="newDocument()" title="Novo Documento (Limpar)"><i class="fas fa-file"></i></button>

            <!-- Undo / Redo -->
            <div class="history-quick-btns">
                <button type="button" class="btn-icon" id="btnQuickUndo" onclick="undo()" title="Desfazer (Ctrl+Z)"><i class="fas fa-undo"></i></button>
                <button type="button" class="btn-icon" id="btnQuickRedo" onclick="redo()" title="Refazer (Ctrl+Y)"><i class="fas fa-redo"></i></button>
            </div>

            <div class="header-separator"></div>

            <div class="preset-dropdown">
                <label for="docPresetSelect"><i class="fas fa-ruler-combined"></i> Formato:</label>
                <select id="docPresetSelect" onchange="changeDocPreset(this.value)">
                    <option value="A4-Landscape">A4 Deitado (1122 x 793 px / 297x210 mm)</option>
                    <option value="A4-Portrait">A4 Em Pé (793 x 1122 px / 210x297 mm)</option>
                    <option value="Instagram-Post">Post Instagram (1080 x 1080 px)</option>
                    <option value="Instagram-Story">Story / Reels (1080 x 1920 px)</option>
                    <option value="Cartao-Visita">Cartão de Visita (1050 x 600 px)</option>
                    <option value="Banner-Web">Banner Full HD (1920 x 1080 px)</option>
                    <option value="Banner-4K">Ultra HD 4K (3840 x 2160 px)</option>
                    <option value="Custom">Personalizado</option>
                </select>
            </div>

            <div class="header-separator"></div>

            <!-- Export Buttons -->
            <div class="export-group">
                <button type="button" class="btn btn-primary" onclick="exportDocument('svg')" title="Exportar vetor SVG limpo">
                    <i class="fas fa-file-code"></i> SVG
                </button>
                <button type="button" class="btn btn-secondary" onclick="exportDocument('png')" title="Exportar imagem PNG em alta resolução">
                    <i class="fas fa-file-image"></i> PNG HD
                </button>
                <button type="button" class="btn btn-secondary" onclick="exportDocument('json')" title="Salvar arquivo de projeto editável">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </div>
    </header>

    <!-- Contextual Top Property Bar (Dinâmica estilo CorelDRAW) -->
    <div class="property-bar" id="propertyBar">
        <!-- Position -->
        <div class="prop-group">
            <span class="prop-label" title="Posição X e Y"><i class="fas fa-crosshairs"></i> Pos:</span>
            <input type="number" id="propX" class="prop-input" value="0" step="1" title="Posição X">
            <input type="number" id="propY" class="prop-input" value="0" step="1" title="Posição Y">
        </div>
        <div class="prop-divider"></div>

        <!-- Size & Aspect Ratio -->
        <div class="prop-group">
            <span class="prop-label" title="Largura e Altura"><i class="fas fa-expand-alt"></i> Tam:</span>
            <input type="number" id="propW" class="prop-input" value="200" step="1" min="1" title="Largura (W)">
            <input type="number" id="propH" class="prop-input" value="200" step="1" min="1" title="Altura (H)">
            <button type="button" class="btn-icon" id="propAspectLock" onclick="toggleAspectLock()" title="Travar Proporção (W/H)"><i class="fas fa-lock-open" id="lockIcon"></i></button>
        </div>
        <div class="prop-divider"></div>

        <!-- Rotation Angle -->
        <div class="prop-group">
            <span class="prop-label" title="Ângulo de Rotação"><i class="fas fa-sync-alt"></i> Giro:</span>
            <input type="number" id="propAngle" class="prop-input" value="0" step="1" min="-360" max="360">°
        </div>
        <div class="prop-divider"></div>

        <!-- Shape specific props (Corner Radius) -->
        <div class="prop-group" id="propCornerGroup">
            <span class="prop-label" title="Arredondamento dos cantos"><i class="fas fa-square"></i> Canto:</span>
            <input type="number" id="propCorner" class="prop-input" value="0" min="0" max="200"> px
        </div>

        <!-- Text specific props -->
        <div class="prop-group" id="propTextGroup" style="display:none;">
            <select id="propFontFamily" class="prop-select" title="Família da Fonte">
                <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
                <option value="Inter">Inter</option>
                <option value="Montserrat">Montserrat</option>
                <option value="Roboto">Roboto</option>
                <option value="Playfair Display">Playfair Display</option>
                <option value="Fira Code">Fira Code (Mono)</option>
                <option value="Arial">Arial</option>
                <option value="Impact">Impact</option>
                <option value="Times New Roman">Times New Roman</option>
            </select>
            <input type="number" id="propFontSize" class="prop-input" value="32" min="8" max="300" title="Tamanho da Fonte"> pt
            <button type="button" class="btn-icon" id="propBold" onclick="toggleTextBold()" title="Negrito"><i class="fas fa-bold"></i></button>
            <button type="button" class="btn-icon" id="propItalic" onclick="toggleTextItalic()" title="Itálico"><i class="fas fa-italic"></i></button>
        </div>
        <div class="prop-divider"></div>

        <!-- Colors & Stroke -->
        <div class="prop-group">
            <span class="prop-label" title="Cor de Preenchimento">Preench:</span>
            <input type="color" id="propFillColor" value="#6366f1" class="color-picker-input" title="Cor de Preenchimento">
            <button type="button" class="btn-icon" id="propNoFill" onclick="setNoFill()" title="Sem Preenchimento (Transparente)"><i class="fas fa-ban text-rose-400"></i></button>
        </div>
        <div class="prop-group">
            <span class="prop-label" title="Cor e Espessura do Contorno">Contorno:</span>
            <input type="color" id="propStrokeColor" value="#ffffff" class="color-picker-input" title="Cor do Contorno">
            <input type="number" id="propStrokeWidth" class="prop-input" value="2" min="0" max="60" title="Espessura do Contorno em pixels"> px
            <button type="button" class="btn-icon" id="propNoStroke" onclick="setNoStroke()" title="Sem Contorno"><i class="fas fa-ban text-rose-400"></i></button>
        </div>
        <div class="prop-divider"></div>

        <!-- Grouping & Ordering -->
        <div class="prop-group">
            <button type="button" class="btn-tool-sm" onclick="centerSelectedToPage()" title="Centralizar na Prancheta (Tecla P)"><i class="fas fa-bullseye text-amber-400"></i> P (Centro)</button>
            <button type="button" class="btn-tool-sm" onclick="groupSelected()" title="Agrupar Objetos (Ctrl+G)"><i class="fas fa-object-group"></i> Agrupar</button>
            <button type="button" class="btn-tool-sm" onclick="ungroupSelected()" title="Desagrupar Objetos (Ctrl+U)"><i class="fas fa-object-ungroup"></i> Desagrupar</button>
            <button type="button" class="btn-tool-sm" onclick="ungroupAll()" title="Desagrupar Tudo (Ctrl+Alt+U)"><i class="fas fa-cubes-stacked"></i> Desagrupar Tudo</button>
            <button type="button" class="btn-tool-sm text-purple-300" id="btnToggleDuplicateBg" onclick="toggleImportedBgImage()" style="display:none;" title="Ocultar imagem de fundo para remover texto duplicado"><i class="fas fa-eye-slash"></i> Ocultar Fundo</button>
            <button type="button" class="btn-tool-sm" onclick="duplicateSelected()" title="Duplicar Objeto (Ctrl+D)"><i class="fas fa-clone"></i> Duplicar</button>
            <button type="button" class="btn-tool-sm text-rose-300" onclick="deleteSelected()" title="Excluir Selecionado (Del)"><i class="fas fa-trash-alt"></i></button>
        </div>
        <div class="prop-divider"></div>

        <!-- Boolean Shaping Operations (Soldar, Aparar, Interseção) -->
        <div class="prop-group" id="propBooleanGroup" style="display:none;">
            <button type="button" class="btn-tool-sm btn-weld" onclick="weldSelected()" title="Soldar Objetos Selecionados (União)"><i class="fas fa-layer-group text-emerald-400"></i> Soldar</button>
            <button type="button" class="btn-tool-sm btn-trim" onclick="trimSelected()" title="Aparar (Cortar objeto de baixo pelo de cima)"><i class="fas fa-cut text-sky-400"></i> Aparar</button>
            <button type="button" class="btn-tool-sm btn-intersect" onclick="intersectSelected()" title="Interseção (Manter apenas área comum)"><i class="fas fa-vector-square text-amber-400"></i> Interseção</button>
            <button type="button" class="btn-tool-sm" onclick="excludeSelected()" title="Excluir Sobreposição"><i class="fas fa-object-ungroup text-rose-400"></i> Excluir</button>
        </div>
        <div class="prop-divider" id="propBooleanDivider" style="display:none;"></div>

        <!-- PowerTRACE (Vetorizador de Bitmap) -->
        <div class="prop-group" id="propTraceGroup" style="display:none;">
            <button type="button" class="btn-tool-sm btn-powertrace" onclick="openPowerTraceDialog()" title="PowerTRACE™ — Vetorizar Bitmap em Curvas SVG">
                <i class="fas fa-bolt text-amber-400"></i> <strong>PowerTRACE™</strong>
            </button>
        </div>
        <div class="prop-divider" id="propTraceDivider" style="display:none;"></div>

        <!-- Ferramenta Forma (F10) & Edição de Nós -->
        <div class="prop-group" id="propNodeGroup" style="display:none;">
            <button type="button" class="btn-tool-sm" onclick="convertToCurvesSelected()" title="Converter em Curvas (Ctrl+Q)">
                <i class="fas fa-bezier-curve text-indigo-400"></i> Converter Curvas (Ctrl+Q)
            </button>
            <button type="button" class="btn-tool-sm" onclick="addNodeToActiveSegment()" title="Adicionar Nó no Segmento Selecionado">
                <i class="fas fa-plus-circle text-emerald-400"></i> Adicionar Nó
            </button>
            <button type="button" class="btn-tool-sm" onclick="deleteSelectedNode()" title="Excluir Nó Selecionado">
                <i class="fas fa-minus-circle text-rose-400"></i> Excluir Nó
            </button>
            <button type="button" class="btn-tool-sm" onclick="toggleNodeSmoothness()" title="Alternar Nó Suave / Canto Cúspide">
                <i class="fas fa-route text-amber-400"></i> Suave / Canto
            </button>
        </div>
        <div class="prop-divider" id="propNodeDivider" style="display:none;"></div>

        <!-- Zoom Bar -->
        <div class="prop-group">
            <span class="prop-label"><i class="fas fa-search-plus"></i> Zoom:</span>
            <button type="button" class="btn-tool-sm" onclick="zoomOut()" title="Diminuir Zoom (-)"><i class="fas fa-minus"></i></button>
            <span id="zoomValText" class="zoom-val-indicator">100%</span>
            <button type="button" class="btn-tool-sm" onclick="zoomIn()" title="Aumentar Zoom (+)"><i class="fas fa-plus"></i></button>
            <button type="button" class="btn-tool-sm" onclick="fitToScreen()" title="Ajustar ao Tamanho da Tela (F4)"><i class="fas fa-compress-arrows-alt"></i> Ajustar (F4)</button>
        </div>
    </div>

    <!-- Main Workspace Container -->
    <div class="main-workspace">
        
        <!-- Left Vertical Tool Palette (Estilo CorelDRAW 2026) -->
        <aside class="tool-palette" id="toolPalette">
            <button type="button" class="tool-btn active" data-tool="select" title="Seleção / Mover / Redimensionar (Pick Tool - V)">
                <i class="fas fa-mouse-pointer"></i>
                <span class="tool-shortcut">V</span>
            </button>
            <button type="button" class="tool-btn" data-tool="node" title="Forma / Nós / Cantos (Shape Tool - F10)">
                <i class="fas fa-bezier-curve"></i>
                <span class="tool-shortcut">F10</span>
            </button>
            <button type="button" class="tool-btn" data-tool="pen" title="Caneta Vetorial Bézier (P)">
                <i class="fas fa-pen-nib"></i>
                <span class="tool-shortcut">P</span>
            </button>
            <button type="button" class="tool-btn" data-tool="freehand" title="Desenho Livre / Lápis (F5)">
                <i class="fas fa-paint-brush"></i>
                <span class="tool-shortcut">F5</span>
            </button>
            <div class="tool-separator"></div>
            <button type="button" class="tool-btn" data-tool="rect" title="Retângulo / Quadrado (F6)">
                <i class="far fa-square"></i>
                <span class="tool-shortcut">F6</span>
            </button>
            <button type="button" class="tool-btn" data-tool="ellipse" title="Elipse / Círculo (F7)">
                <i class="far fa-circle"></i>
                <span class="tool-shortcut">F7</span>
            </button>
            <button type="button" class="tool-btn" data-tool="star" title="Estrela / Polígono (Y)">
                <i class="far fa-star"></i>
                <span class="tool-shortcut">Y</span>
            </button>
            <button type="button" class="tool-btn" data-tool="text" title="Texto Artístico (F8)">
                <i class="fas fa-font"></i>
                <span class="tool-shortcut">F8</span>
            </button>
            <div class="tool-separator"></div>
            <button type="button" class="tool-btn" data-tool="eyedropper" title="Conta-Gotas (Copiar Cor)">
                <i class="fas fa-eye-dropper"></i>
                <span class="tool-shortcut">I</span>
            </button>
            <button type="button" class="tool-btn" data-tool="zoom" title="Pan / Mover Tela (Espaço / Z)">
                <i class="fas fa-hand-paper"></i>
                <span class="tool-shortcut">H</span>
            </button>
        </aside>

        <!-- Center SVG Canvas Viewport with Interactive Rulers -->
        <main class="canvas-viewport" id="canvasViewport">
            
            <!-- Horizontal Ruler -->
            <div class="ruler ruler-horizontal" id="rulerHorizontal">
                <canvas id="canvasRulerH"></canvas>
            </div>
            <!-- Vertical Ruler -->
            <div class="ruler ruler-vertical" id="rulerVertical">
                <canvas id="canvasRulerV"></canvas>
            </div>

            <!-- Workspace Board (Prancheta Corel) -->
            <div class="workspace-board" id="workspaceBoard">
                <svg id="mainSvgCanvas" xmlns="http://www.w3.org/2000/svg" width="1122" height="793" viewBox="0 0 1122 793">
                    <defs>
                        <!-- Grid Background -->
                        <pattern id="gridPattern" width="20" height="20" patternUnits="userSpaceOnUse">
                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
                        </pattern>
                        <!-- Drop Shadow Filter -->
                        <filter id="objShadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="6" stdDeviation="8" flood-color="#000000" flood-opacity="0.4"/>
                        </filter>
                    </defs>

                    <!-- Sheet Background Page -->
                    <rect id="bgSheet" width="100%" height="100%" fill="#ffffff"/>

                    <!-- Interactive Guidelines (Linhas-Guia) -->
                    <g id="guidelinesGroup"></g>

                    <!-- Dynamic Layer Group -->
                    <g id="layerGroupMain"></g>

                    <!-- Interactive Bounding Box / Transform Handles Overlay -->
                    <g id="selectionOverlay" pointer-events="all"></g>

                    <!-- Interactive Node Editing Overlay (Shape Tool F10) -->
                    <g id="nodeEditOverlay" pointer-events="all"></g>
                </svg>
            </div>

            <!-- Multi-Page CDR Tab Bar (Floating) -->
            <div id="cdrPageTabBar" class="cdr-page-tab-bar" style="display:none;"></div>
        </main>

        <!-- Right Side Inspector Panel (Camadas, Alinhamento, Propriedades) -->
        <aside class="inspector-panel">
            <!-- Tabs -->
            <div class="inspector-tabs">
                <button type="button" class="tab-btn active" onclick="switchInspectorTab('layers')"><i class="fas fa-layer-group"></i> Camadas</button>
                <button type="button" class="tab-btn" onclick="switchInspectorTab('align')"><i class="fas fa-align-center"></i> Alinhar</button>
                <button type="button" class="tab-btn" onclick="switchInspectorTab('history')"><i class="fas fa-history"></i> Histórico</button>
            </div>

            <!-- Layers Tab Content -->
            <div class="inspector-content active" id="tabContentLayers">
                <div class="section-title">
                    <span>Objetos na Prancheta</span>
                    <button type="button" class="btn-micro" onclick="clearAllObjects()"><i class="fas fa-trash"></i> Limpar</button>
                </div>
                <div class="layers-tree-list" id="layersTreeList">
                    <!-- Dynamic Objects Tree -->
                </div>
            </div>

            <!-- Alignment Tab Content -->
            <div class="inspector-content" id="tabContentAlign">
                <div class="section-title"><span>Alinhar à Prancheta</span></div>
                <div class="align-grid">
                    <button type="button" class="btn-align" onclick="alignSelected('left')" title="Alinhar à Esquerda"><i class="fas fa-align-left"></i></button>
                    <button type="button" class="btn-align" onclick="alignSelected('center')" title="Centralizar Horizontalmente"><i class="fas fa-align-center"></i></button>
                    <button type="button" class="btn-align" onclick="alignSelected('right')" title="Alinhar à Direita"><i class="fas fa-align-right"></i></button>
                    <button type="button" class="btn-align" onclick="alignSelected('top')" title="Alinhar ao Topo"><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="btn-align" onclick="alignSelected('middle')" title="Centralizar Verticalmente"><i class="fas fa-arrows-alt-v"></i></button>
                    <button type="button" class="btn-align" onclick="alignSelected('bottom')" title="Alinhar à Base"><i class="fas fa-arrow-down"></i></button>
                </div>

                <div class="section-title" style="margin-top:20px;"><span>Ordem dos Objetos</span></div>
                <div class="order-actions">
                    <button type="button" class="btn btn-secondary btn-block" onclick="orderSelected('front')"><i class="fas fa-angle-double-up"></i> Trazer para Frente (Ctrl+Home)</button>
                    <button type="button" class="btn btn-secondary btn-block" onclick="orderSelected('forward')"><i class="fas fa-angle-up"></i> Avançar 1 Camada</button>
                    <button type="button" class="btn btn-secondary btn-block" onclick="orderSelected('backward')"><i class="fas fa-angle-down"></i> Recuar 1 Camada</button>
                    <button type="button" class="btn btn-secondary btn-block" onclick="orderSelected('back')"><i class="fas fa-angle-double-down"></i> Enviar para Trás (Ctrl+End)</button>
                </div>
            </div>

            <!-- History Tab Content -->
            <div class="inspector-content" id="tabContentHistory">
                <div class="section-title"><span>Linha do Tempo</span></div>
                <div class="history-list" id="historyList">
                    <!-- History items -->
                </div>
            </div>
        </aside>
    </div>

    <!-- Bottom Palette Bar (Paleta CMYK / RGB CorelDRAW com 40+ Cores) -->
    <div class="color-palette-bar">
        <span class="palette-label"><i class="fas fa-palette"></i> Paleta:</span>
        <div class="palette-swatches" id="paletteSwatches">
            <!-- Dynamic Color Swatches -->
        </div>
        <div class="palette-tip" title="Botão Esquerdo: Preenchimento | Botão Direito: Contorno">
            <span class="badge-tip">Dica</span> Clique esq: Preencher • dir: Contorno
        </div>
    </div>

    <!-- Institutional Footer -->
    <footer class="app-footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> <strong>CorelClone Pro (4U.IA.BR)</strong>. Todos os direitos reservados. Desenvolvido por <strong>4uLabs</strong>.</p>
            <div class="footer-links">
                <a href="privacidade.php"><i class="fas fa-shield-alt"></i> Política de Privacidade</a>
                <a href="termos.php"><i class="fas fa-file-contract"></i> Termos de Uso</a>
                <a href="suporte.php"><i class="fas fa-headset"></i> Central de Suporte & FAQ</a>
            </div>
        </div>
    </footer>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- PowerTRACE Modal Dialog -->
    <div id="powertraceModal" class="modal-overlay" style="display:none;">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title"><i class="fas fa-bolt text-amber-400"></i> PowerTRACE™ — Vetorizador de Bitmap</div>
                <button type="button" class="btn-icon" onclick="closePowerTraceDialog()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p class="modal-desc">Converta a imagem selecionada ou visualização de arquivo CorelDRAW (.CDR) em curvas vetoriais nativas SVG totalmente editáveis.</p>
                <div class="trace-presets">
                    <label class="trace-preset-option active">
                        <input type="radio" name="tracePreset" value="posterized2" checked>
                        <div class="preset-info">
                            <strong>Logotipo / Clipart (Recomendado)</strong>
                            <span>Vetoriza preservando cores primárias e contornos suaves, perfeito para logos e artes Corel.</span>
                        </div>
                    </label>
                    <label class="trace-preset-option">
                        <input type="radio" name="tracePreset" value="detailed">
                        <div class="preset-info">
                            <strong>Alta Fidelidade (Mais Cores)</strong>
                            <span>Gera mais camadas de cores para ilustrações e fotos mais detalhadas.</span>
                        </div>
                    </label>
                    <label class="trace-preset-option">
                        <input type="radio" name="tracePreset" value="posterized1">
                        <div class="preset-info">
                            <strong>Silhueta / Rápido (Poucas Cores)</strong>
                            <span>Curvas ultraleves e limpas, ideal para recorte e estampas monocromáticas.</span>
                        </div>
                    </label>
                </div>
                <div class="trace-options" style="margin-top:14px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:#cbd5e1; cursor:pointer;">
                        <input type="checkbox" id="traceRemoveOriginal" checked> Remover imagem bitmap original após vetorização
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closePowerTraceDialog()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnRunTrace" onclick="runPowerTrace()">
                    <i class="fas fa-magic"></i> Rastrear e Gerar Vetores
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="script.js?v=<?php echo $v; ?>"></script>
</body>
</html>
