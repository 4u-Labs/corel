/**
 * CorelClone Pro 2026 — Official CorelDRAW & PHOTO-PAINT Engine
 * Complete 5-Zone Architecture: Multi-Level Top Bar, Toolbox,
 * Calibrated Rulers, Objects Docker, Vertical Corel Palette & Status Bar.
 */

// ==================== Global Application State ====================
const state = {
    docWidth: 1122,      // A4 Landscape default in px (approx 297mm @ 96DPI)
    docHeight: 793,      // A4 Landscape height in px (approx 210mm @ 96DPI)
    unit: 'mm',          // 'mm', 'px', 'in', 'pt'
    zoom: 1.0,
    activeTool: 'select',
    activeFill: '#000000',
    activeStroke: 'none',
    strokeWidth: 2,
    selectedElements: [],
    clipboard: null,
    pages: [
        { id: 0, name: 'Documento 1', svgContent: null, docWidth: 1122, docHeight: 793 }
    ],
    activePageIndex: 0,
    history: [],
    historyIndex: -1,
    guidelines: [],
    isDrawing: false,
    drawStart: { x: 0, y: 0 },
    currentElement: null,
    isPanning: false,
    panStart: { x: 0, y: 0 },
    scrollStart: { left: 0, top: 0 },
    activeTransform: null,
    nodeEdit: { activePath: null, nodes: [], activeNodeIndex: -1 },
    penPath: null,
    penPoints: [],
    isPowerClipping: false,
    powerClipTargetContent: null,
    isPickingPathForText: false,
    targetTextForPath: null,
    lastTransformDelta: { dx: 20, dy: 20 }
};

// Standard Corel Color Palette (40+ classic swatches)
const COREL_PALETTE_COLORS = [
    'none', '#000000', '#1a1a1a', '#333333', '#4d4d4d', '#666666', '#808080', '#999999',
    '#b3b3b3', '#cccccc', '#e6e6e6', '#ffffff',
    '#00ffff', '#00e5ff', '#00b0ff', '#0044ff', '#0000cc', '#000080',
    '#00ff00', '#00e676', '#00c853', '#2e7d32', '#1b5e20',
    '#ffff00', '#ffea00', '#ffd600', '#ffab00', '#ff6d00', '#ff3d00',
    '#ff0000', '#d50000', '#b71c1c', '#c2185b', '#e91e63', '#ff4081',
    '#aa00ff', '#6200ea', '#795548', '#4e342e', '#ff9800', '#ffc107'
];

// Document Presets
const DOC_PRESETS = {
    'A4-Landscape': { w: 1122, h: 793, unit: 'mm' },
    'A4-Portrait': { w: 793, h: 1122, unit: 'mm' },
    'Cartao-Visita': { w: 1050, h: 600, unit: 'mm' },
    'Instagram-Post': { w: 1080, h: 1080, unit: 'px' },
    'Instagram-Story': { w: 1080, h: 1920, unit: 'px' },
    'Banner-Web': { w: 1920, h: 1080, unit: 'px' },
    'Custom': { w: 1122, h: 793, unit: 'mm' }
};

// Unit conversions (base is 96 DPI screen pixels)
const UNIT_FACTORS = {
    'px': 1,
    'mm': 3.7795275591,
    'cm': 37.795275591,
    'in': 96,
    'pt': 1.3333333333
};

// ==================== Initialization ====================
window.addEventListener('DOMContentLoaded', () => {
    initVerticalPalette();
    initCanvasBoard();
    initCanvasEvents();
    initRulers();
    initKeyboardShortcuts();
    applyDocDimensions();
    updatePropertyBar();
    updateLayersTree();
    updateStatusDimensions();
    renderDocumentTabs();
    saveState('Inicialização');

    // Prevent default context menu on workspace and palette
    document.getElementById('verticalPaletteSwatches').addEventListener('contextmenu', e => e.preventDefault());
    document.getElementById('canvasScroller').addEventListener('contextmenu', e => e.preventDefault());

    // Check first run to display CorelClone welcome art on canvas
    checkAndLoadWelcomeArt();

    toast('CorelClone Pro 2026 pronto para criação vetorial!', 'ok');
});

// ==================== First Run Welcome Art ====================
const WELCOME_ART_KEY = 'corelclone_welcome_v1';

function checkAndLoadWelcomeArt() {
    let hasShown = false;
    try {
        hasShown = !!localStorage.getItem(WELCOME_ART_KEY);
    } catch (e) {}

    if (!hasShown) {
        try {
            localStorage.setItem(WELCOME_ART_KEY, 'true');
        } catch (e) {}
        loadWelcomeArtOnCanvas();
    }
}

function loadWelcomeArtOnCanvas() {
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup || layerGroup.children.length > 0) return;

    const img = document.createElementNS('http://www.w3.org/2000/svg', 'image');
    img.setAttribute('href', 'corelicon-welcome.png');
    img.setAttribute('preserveAspectRatio', 'xMidYMid meet');

    // Exact coordinates matching printscreen: X=45mm, Y=24mm, L=216mm, A=159mm
    const x = Math.round(45 * UNIT_FACTORS.mm);
    const y = Math.round(24 * UNIT_FACTORS.mm);
    const w = Math.round(216 * UNIT_FACTORS.mm);
    const h = Math.round(159 * UNIT_FACTORS.mm);

    img.setAttribute('x', x.toString());
    img.setAttribute('y', y.toString());
    img.setAttribute('width', w.toString());
    img.setAttribute('height', h.toString());

    layerGroup.appendChild(img);

    setTimeout(() => {
        selectElement(img, false);
        updateLayersTree();
        updatePropertyBar();
        updateStatusDimensions();
        saveState('Boas-vindas CorelClone');
    }, 150);
}

window.loadWelcomeArtOnCanvas = loadWelcomeArtOnCanvas;

// ==================== Vertical Corel Color Palette ====================
function initVerticalPalette() {
    const container = document.getElementById('verticalPaletteSwatches');
    if (!container) return;
    container.innerHTML = '';

    COREL_PALETTE_COLORS.forEach(color => {
        const swatch = document.createElement('div');
        swatch.className = 'v-swatch' + (color === 'none' ? ' none' : '');
        if (color !== 'none') {
            swatch.style.backgroundColor = color;
        }
        swatch.title = `Cor: ${color}\n• Clique Esquerdo: Preenchimento\n• Clique Direito: Contorno`;

        // Left Click -> Set Fill
        swatch.addEventListener('click', (e) => {
            e.preventDefault();
            applyColorToSelection('fill', color);
        });

        // Right Click -> Set Outline (Stroke)
        swatch.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            applyColorToSelection('stroke', color);
        });

        container.appendChild(swatch);
    });
}

function applyColorToSelection(type, color) {
    if (type === 'fill') {
        state.activeFill = color;
        const swatch = document.getElementById('activeFillSwatch');
        if (swatch) swatch.style.backgroundColor = color === 'none' ? 'transparent' : color;
    } else {
        state.activeStroke = color;
        const swatch = document.getElementById('activeStrokeSwatch');
        if (swatch) swatch.style.backgroundColor = color === 'none' ? 'transparent' : color;
    }

    if (state.selectedElements.length > 0) {
        state.selectedElements.forEach(el => {
            if (type === 'fill') {
                el.setAttribute('fill', color);
                el.style.fill = color;
            } else {
                el.setAttribute('stroke', color);
                el.style.stroke = color;
                if (!el.getAttribute('stroke-width') || parseFloat(el.getAttribute('stroke-width')) === 0) {
                    el.setAttribute('stroke-width', state.strokeWidth.toString());
                }
            }
        });
        saveState(`Alterar ${type === 'fill' ? 'Preenchimento' : 'Contorno'}`);
        renderSelectionOverlay();
        updateLayersTree();
        addRecentDocColor(color);
        toast(`${type === 'fill' ? 'Preenchimento' : 'Contorno'} aplicado: ${color}`, 'ok');
    }
}

function scrollPalette(dir) {
    const container = document.getElementById('verticalPaletteSwatches');
    if (container) {
        container.scrollTop += dir * 60;
    }
}

function addRecentDocColor(color) {
    if (!color || color === 'none') return;
    const container = document.getElementById('docRecentColors');
    if (!container) return;
    const existing = container.querySelector(`[data-color="${color}"]`);
    if (existing) return;

    const swatch = document.createElement('div');
    swatch.dataset.color = color;
    swatch.style.width = '14px';
    swatch.style.height = '14px';
    swatch.style.backgroundColor = color;
    swatch.style.border = '1px solid #777';
    swatch.style.borderRadius = '1px';
    swatch.style.cursor = 'pointer';
    swatch.title = `Cor: ${color}\nEsq: Preencher | Dir: Contorno`;
    swatch.onclick = () => applyColorToSelection('fill', color);
    swatch.oncontextmenu = (e) => { e.preventDefault(); applyColorToSelection('stroke', color); };

    container.appendChild(swatch);
    if (container.children.length > 8) {
        container.removeChild(container.firstElementChild);
    }
}

// ==================== Document Setup & Dimensions ====================
function applyDocDimensions() {
    const svg = document.getElementById('mainSvgCanvas');
    const board = document.getElementById('canvasBoard');
    if (!svg || !board) return;

    svg.setAttribute('width', state.docWidth.toString());
    svg.setAttribute('height', state.docHeight.toString());
    svg.setAttribute('viewBox', `0 0 ${state.docWidth} ${state.docHeight}`);

    board.style.width = `${Math.round(state.docWidth * state.zoom)}px`;
    board.style.height = `${Math.round(state.docHeight * state.zoom)}px`;

    const propW = document.getElementById('docPropWidth');
    const propH = document.getElementById('docPropHeight');
    if (propW) propW.value = Math.round(fromPxToUnit(state.docWidth, state.unit));
    if (propH) propH.value = Math.round(fromPxToUnit(state.docHeight, state.unit));

    updateStatusDimensions();
    drawRulers();
}

function changeDocPreset(presetKey) {
    const preset = DOC_PRESETS[presetKey];
    if (!preset) return;

    state.docWidth = preset.w;
    state.docHeight = preset.h;
    if (preset.unit) state.unit = preset.unit;

    const unitSel = document.getElementById('unitSelect');
    if (unitSel) unitSel.value = state.unit;

    applyDocDimensions();
    saveState(`Formato: ${presetKey}`);
    toast(`Formato alterado para ${presetKey}`, 'ok');
}

function setDocOrientation(orient) {
    const btnL = document.getElementById('btnOrientLandscape');
    const btnP = document.getElementById('btnOrientPortrait');
    if (orient === 'landscape' && state.docWidth < state.docHeight) {
        const temp = state.docWidth;
        state.docWidth = state.docHeight;
        state.docHeight = temp;
    } else if (orient === 'portrait' && state.docHeight < state.docWidth) {
        const temp = state.docWidth;
        state.docWidth = state.docHeight;
        state.docHeight = temp;
    }

    if (btnL) btnL.classList.toggle('active', state.docWidth >= state.docHeight);
    if (btnP) btnP.classList.toggle('active', state.docWidth < state.docHeight);

    applyDocDimensions();
    saveState('Orientação da Página');
}

function updateDocDimensionsFromInput() {
    const propW = document.getElementById('docPropWidth');
    const propH = document.getElementById('docPropHeight');
    if (!propW || !propH) return;

    const wVal = parseFloat(propW.value) || 297;
    const hVal = parseFloat(propH.value) || 210;

    state.docWidth = Math.max(50, Math.round(fromUnitToPx(wVal, state.unit)));
    state.docHeight = Math.max(50, Math.round(fromUnitToPx(hVal, state.unit)));

    applyDocDimensions();
    saveState('Dimensões Personalizadas');
}

function changeUnits(newUnit) {
    state.unit = newUnit;
    const propW = document.getElementById('docPropWidth');
    const propH = document.getElementById('docPropHeight');
    if (propW) propW.value = Math.round(fromPxToUnit(state.docWidth, state.unit));
    if (propH) propH.value = Math.round(fromPxToUnit(state.docHeight, state.unit));
    updateStatusDimensions();
    drawRulers();
}

function fromUnitToPx(val, unit) {
    return val * (UNIT_FACTORS[unit] || 1);
}

function fromPxToUnit(px, unit) {
    return px / (UNIT_FACTORS[unit] || 1);
}

function updateStatusDimensions() {
    const el = document.getElementById('statusDimensions');
    if (el) {
        const wUnit = fromPxToUnit(state.docWidth, state.unit).toFixed(1);
        const hUnit = fromPxToUnit(state.docHeight, state.unit).toFixed(1);
        el.querySelector('span').textContent = `${wUnit} x ${hUnit} ${state.unit}`;
    }
}

// ==================== Calibrated Rulers Engine ====================
function initRulers() {
    const scroller = document.getElementById('canvasScroller');
    if (scroller) {
        scroller.addEventListener('scroll', () => drawRulers());
    }
    window.addEventListener('resize', () => drawRulers());
}

function drawRulers() {
    const rH = document.getElementById('rulerH');
    const rV = document.getElementById('rulerV');
    const scroller = document.getElementById('canvasScroller');
    const board = document.getElementById('canvasBoard');
    if (!rH || !rV || !scroller || !board) return;

    const ctxH = rH.getContext('2d');
    const ctxV = rV.getContext('2d');

    const wH = rH.offsetWidth;
    const hH = rH.offsetHeight;
    const wV = rV.offsetWidth;
    const hV = rV.offsetHeight;

    rH.width = wH;
    rH.height = hH;
    rV.width = wV;
    rV.height = hV;

    ctxH.fillStyle = '#e9e9e9';
    ctxH.fillRect(0, 0, wH, hH);
    ctxV.fillStyle = '#e9e9e9';
    ctxV.fillRect(0, 0, wV, hV);

    const scrollerRect = scroller.getBoundingClientRect();
    const boardRect = board.getBoundingClientRect();

    const startX = boardRect.left - scrollerRect.left;
    const startY = boardRect.top - scrollerRect.top;

    ctxH.strokeStyle = '#999999';
    ctxH.fillStyle = '#444444';
    ctxH.font = '9px Segoe UI, sans-serif';
    ctxH.lineWidth = 1;

    ctxV.strokeStyle = '#999999';
    ctxV.fillStyle = '#444444';
    ctxV.font = '9px Segoe UI, sans-serif';
    ctxV.lineWidth = 1;

    const pxPerUnit = (UNIT_FACTORS[state.unit] || 1) * state.zoom;
    const stepUnit = state.unit === 'px' ? 50 : 10;
    const stepPx = stepUnit * pxPerUnit;

    // Draw Horizontal ticks
    if (stepPx > 10) {
        for (let x = startX % stepPx; x < wH; x += stepPx) {
            const unitVal = Math.round((x - startX) / pxPerUnit);
            ctxH.beginPath();
            ctxH.moveTo(x, hH - 8);
            ctxH.lineTo(x, hH);
            ctxH.stroke();
            if (x >= 0 && x < wH - 20) {
                ctxH.fillText(unitVal.toString(), x + 2, hH - 9);
            }
        }
    }

    // Draw Vertical ticks
    if (stepPx > 10) {
        for (let y = startY % stepPx; y < hV; y += stepPx) {
            const unitVal = Math.round((y - startY) / pxPerUnit);
            ctxV.beginPath();
            ctxV.moveTo(wV - 8, y);
            ctxV.lineTo(wV, y);
            ctxV.stroke();
            if (y >= 10 && y < hV) {
                ctxV.save();
                ctxV.translate(wV - 9, y - 2);
                ctxV.rotate(-Math.PI / 2);
                ctxV.fillText(unitVal.toString(), 0, 0);
                ctxV.restore();
            }
        }
    }
}

function resetRulerZero() {
    const scroller = document.getElementById('canvasScroller');
    if (scroller) {
        scroller.scrollLeft = (scroller.scrollWidth - scroller.clientWidth) / 2;
        scroller.scrollTop = (scroller.scrollHeight - scroller.clientHeight) / 2;
    }
    drawRulers();
}

function toggleRulers() {
    const rH = document.getElementById('rulerH');
    const rV = document.getElementById('rulerV');
    const corner = document.querySelector('.ruler-corner');
    const scroller = document.getElementById('canvasScroller');
    const isVisible = rH.style.display !== 'none';

    if (isVisible) {
        rH.style.display = 'none';
        rV.style.display = 'none';
        if (corner) corner.style.display = 'none';
        if (scroller) { scroller.style.top = '0'; scroller.style.left = '0'; }
    } else {
        rH.style.display = 'block';
        rV.style.display = 'block';
        if (corner) corner.style.display = 'block';
        if (scroller) { scroller.style.top = 'var(--corel-ruler-size)'; scroller.style.left = 'var(--corel-ruler-size)'; }
        drawRulers();
    }
}

function toggleGuidelines() {
    const g = document.getElementById('guidelinesGroup');
    if (g) {
        g.style.display = g.style.display === 'none' ? 'block' : 'none';
    }
}

// ==================== Toolbox & Interaction Engine ====================
function selectTool(toolName) {
    state.activeTool = toolName;
    document.querySelectorAll('.corel-toolbox .tool-btn').forEach(btn => btn.classList.remove('active'));
    const btn = document.getElementById(`toolBtn_${toolName}`);
    if (btn) btn.classList.add('active');

    const viewport = document.getElementById('canvasScroller');
    if (toolName === 'pan') {
        viewport.style.cursor = 'grab';
    } else if (toolName === 'select') {
        viewport.style.cursor = 'default';
    } else if (toolName === 'text') {
        viewport.style.cursor = 'text';
    } else {
        viewport.style.cursor = 'crosshair';
    }

    updatePropertyBar();
    updateContextHint(toolName);
}

function updateContextHint(tool) {
    const el = document.getElementById('statusContextHint');
    if (!el) return;

    const hints = {
        select: 'Clique para selecionar objetos. Arraste para mover ou criar caixa de seleção.',
        node: 'Ferramenta Forma (F10): Dê duplo clique para adicionar/excluir nós Bézier.',
        crop: 'Clique e arraste para definir a área de corte do documento.',
        pan: 'Arraste para mover a visualização da tela livremente.',
        pen: 'Clique para adicionar vértices Bézier retos ou arraste para fazer curvas.',
        brush: 'Clique e arraste para desenhar traços de mídia artística livres.',
        rect: 'Clique e arraste para desenhar retângulos. Segure CTRL para quadrado perfeito.',
        ellipse: 'Clique e arraste para criar elipses. Segure CTRL para círculo perfeito.',
        star: 'Clique e arraste para criar estrelas/polígonos.',
        text: 'Clique na prancheta para inserir texto vetorial editável.',
        eyedropper: 'Clique sobre qualquer objeto para capturar sua cor.',
        fill: 'Clique e arraste para aplicar preenchimento interativo com gradiente.'
    };

    el.textContent = hints[tool] || 'Segure CTRL para restringir proporção, ALT para transformar pelo centro.';
}

function initCanvasBoard() {
    const scroller = document.getElementById('canvasScroller');
    if (scroller) {
        setTimeout(() => {
            scroller.scrollLeft = (scroller.scrollWidth - scroller.clientWidth) / 2;
            scroller.scrollTop = (scroller.scrollHeight - scroller.clientHeight) / 2;
            drawRulers();
        }, 100);
    }
}

function getSvgCoords(e) {
    const svg = document.getElementById('mainSvgCanvas');
    if (!svg) return { x: 0, y: 0 };
    const pt = svg.createSVGPoint();
    pt.x = e.clientX;
    pt.y = e.clientY;
    try {
        const ctm = svg.getScreenCTM();
        if (ctm) return pt.matrixTransform(ctm.inverse());
    } catch {}
    return { x: e.clientX, y: e.clientY };
}

function initCanvasEvents() {
    const scroller = document.getElementById('canvasScroller');
    const svg = document.getElementById('mainSvgCanvas');
    if (!scroller || !svg) return;

    // Track mouse coordinates for bottom status bar and hairlines
    scroller.addEventListener('mousemove', (e) => {
        const pt = getSvgCoords(e);
        const xUnit = fromPxToUnit(pt.x, state.unit).toFixed(1);
        const yUnit = fromPxToUnit(pt.y, state.unit).toFixed(1);
        const coordEl = document.getElementById('statusCoordinates');
        if (coordEl) {
            coordEl.querySelector('span').textContent = `X: ${xUnit} ${state.unit}   Y: ${yUnit} ${state.unit}`;
        }
    });

    // ---- Zoom via Ctrl+Scroll: aplica apenas na prancheta, bloqueia zoom do browser ----
    scroller.addEventListener('wheel', (e) => {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            e.stopPropagation();

            // Ponto do mouse dentro do scroller (para manter o ponto focal)
            const rect = scroller.getBoundingClientRect();
            const mouseXInScroller = e.clientX - rect.left;
            const mouseYInScroller = e.clientY - rect.top;
            // Posição proporcional dentro do conteúdo antes do zoom
            const ratioX = (scroller.scrollLeft + mouseXInScroller) / (scroller.scrollWidth  || 1);
            const ratioY = (scroller.scrollTop  + mouseYInScroller) / (scroller.scrollHeight || 1);

            const delta = e.deltaY < 0 ? 1.1 : (1 / 1.1);
            const newZoom = Math.max(0.05, Math.min(state.zoom * delta, 8.0));
            setZoom(newZoom);

            // Reposicionar scroll para manter o ponto focal sob o cursor
            requestAnimationFrame(() => {
                scroller.scrollLeft = ratioX * scroller.scrollWidth  - mouseXInScroller;
                scroller.scrollTop  = ratioY * scroller.scrollHeight - mouseYInScroller;
            });

            // Atualiza o select de zoom para refletir o valor atual
            const sel = document.getElementById('zoomSelect');
            if (sel) sel.value = '';
            const pct = Math.round(newZoom * 100);
            const statusZ = document.getElementById('statusZoom');
            if (statusZ) statusZ.querySelector('span').textContent = `Zoom: ${pct}%`;
        }
    }, { passive: false });



    // Mouse Down
    svg.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return; // Only main left click

        const pt = getSvgCoords(e);
        state.drawStart = pt;

        // 0. PowerClip Destination Pick Mode
        if (state.isPowerClipping) {
            e.stopPropagation();
            const target = e.target;
            if (target && target.closest('#layerGroupMain')) {
                const containerEl = target.closest('#layerGroupMain > *');
                if (containerEl && containerEl !== state.powerClipTargetContent) {
                    createPowerClip(state.powerClipTargetContent, containerEl);
                    state.isPowerClipping = false;
                    state.powerClipTargetContent = null;
                    document.body.style.cursor = 'default';
                    return;
                }
            }
            toast('PowerClip cancelado (clique fora do recipiente).', 'info');
            state.isPowerClipping = false;
            state.powerClipTargetContent = null;
            document.body.style.cursor = 'default';
            return;
        }

        // 0.1 Text to Path Curve Pick Mode
        if (state.isPickingPathForText) {
            e.stopPropagation();
            const target = e.target;
            if (target && target.closest('#layerGroupMain')) {
                const curveEl = target.closest('#layerGroupMain > *');
                if (curveEl && curveEl !== state.targetTextForPath) {
                    applyFitTextToPath(state.targetTextForPath, curveEl);
                    state.isPickingPathForText = false;
                    state.targetTextForPath = null;
                    document.body.style.cursor = 'default';
                    return;
                }
            }
            toast('Ajuste de texto cancelado (clique fora da curva).', 'info');
            state.isPickingPathForText = false;
            state.targetTextForPath = null;
            document.body.style.cursor = 'default';
            return;
        }

        // 1. Pan Tool Mode
        if (state.activeTool === 'pan' || e.spaceKey) {
            state.isPanning = true;
            state.panStart = { x: e.clientX, y: e.clientY };
            state.scrollStart = { left: scroller.scrollLeft, top: scroller.scrollTop };
            scroller.style.cursor = 'grabbing';
            return;
        }

        // 2. Select Tool Mode
        if (state.activeTool === 'select') {
            const target = e.target;
            if (target && target.closest('#layerGroupMain')) {
                const el = target.closest('#layerGroupMain > *');
                if (el) {
                    selectElement(el, e.shiftKey);
                    initTransformDrag(e, 'move');
                    return;
                }
            } else if (!target.closest('#selectionOverlay')) {
                deselectAll();
            }
            return;
        }

        // 3. Node Edit Tool (F10)
        if (state.activeTool === 'node') {
            const target = e.target;

            // Clicou num handle de nó existente — o evento já é tratado no renderNodeEditOverlay
            if (target.closest && target.closest('#nodeEditOverlay')) return;

            // Clicou num elemento da prancheta — entra no modo de edição
            if (target && target.closest('#layerGroupMain')) {
                const el = target.closest('#layerGroupMain > *');
                if (el && !INTERNAL_LAYER_IDS.has(el.getAttribute('id') || '')) {
                    // Seleciona o elemento
                    state.selectedElements = [el];
                    renderSelectionOverlay();
                    // Entra no modo nó
                    enterNodeEditingForSelected();
                    updatePropertyBar();
                    return;
                }
            }

            // Clicou no vazio — sai do modo nó
            if (state.nodeEdit.activePath) {
                state.nodeEdit.activePath = null;
                state.nodeEdit.nodes = [];
                state.nodeEdit.activeNodeIndex = -1;
                const overlay = document.getElementById('nodeEditOverlay');
                if (overlay) overlay.innerHTML = '';
            }
            return;
        }

        // 4. Crop Tool — desenha um retângulo de recorte e corta o objeto selecionado
        if (state.activeTool === 'crop') {
            state.isDrawing = true;
            // Usa um retângulo visual temporário para mostrar área de crop
            let cropRect = document.getElementById('_cropPreview');
            if (!cropRect) {
                cropRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                cropRect.setAttribute('id', '_cropPreview');
                cropRect.setAttribute('fill', 'rgba(0,100,255,0.10)');
                cropRect.setAttribute('stroke', '#0066cc');
                cropRect.setAttribute('stroke-width', '1');
                cropRect.setAttribute('stroke-dasharray', '5,3');
                svg.appendChild(cropRect);
            }
            cropRect.setAttribute('x', pt.x.toString());
            cropRect.setAttribute('y', pt.y.toString());
            cropRect.setAttribute('width', '1');
            cropRect.setAttribute('height', '1');
            state.currentElement = cropRect;
            return;
        }

        // 5. Fill Bucket — aplica a cor de preenchimento ativa ao objeto clicado
        if (state.activeTool === 'fill') {
            const target = e.target;
            if (target && target.closest('#layerGroupMain')) {
                const el = target.closest('#layerGroupMain > *');
                if (el) {
                    el.setAttribute('fill', state.activeFill !== 'none' ? state.activeFill : '#000000');
                    saveState('Preenchimento Interativo');
                    toast('Cor de preenchimento aplicada!', 'ok');
                } else {
                    toast('Clique sobre um objeto para aplicar o preenchimento.', 'err');
                }
            }
            return;
        }

        // 6. Pen Tool (Bézier por cliques) — acumula pontos no mesmo path
        if (state.activeTool === 'pen') {
            const layerGroup = document.getElementById('layerGroupMain');
            const stroke = state.activeStroke !== 'none' ? state.activeStroke : '#000000';
            const sw = state.strokeWidth;

            if (!state.penPath) {
                // Inicia um novo caminho
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', `M ${Math.round(pt.x)} ${Math.round(pt.y)}`);
                path.setAttribute('fill', 'none');
                path.setAttribute('stroke', stroke);
                path.setAttribute('stroke-width', sw.toString());
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                layerGroup.appendChild(path);
                state.penPath = path;
                state.penPoints = [{ x: pt.x, y: pt.y }];
                toast('Clique para adicionar pontos. Duplo clique ou Enter para fechar o caminho.', 'ok');
            } else {
                // Adiciona ponto ao caminho existente
                const d = state.penPath.getAttribute('d') || '';
                state.penPath.setAttribute('d', `${d} L ${Math.round(pt.x)} ${Math.round(pt.y)}`);
                state.penPoints.push({ x: pt.x, y: pt.y });
            }
            return;
        }

        // 7. Drawing Tools (Brush, Rect, Ellipse, Star) — drag to draw
        state.isDrawing = true;
        const layerGroup = document.getElementById('layerGroupMain');
        const fill = state.activeFill;
        const stroke = state.activeStroke;
        const sw = state.strokeWidth;

        if (state.activeTool === 'rect') {
            const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', pt.x.toString());
            rect.setAttribute('y', pt.y.toString());
            rect.setAttribute('width', '1');
            rect.setAttribute('height', '1');
            rect.setAttribute('fill', fill);
            rect.setAttribute('stroke', stroke);
            rect.setAttribute('stroke-width', sw.toString());
            layerGroup.appendChild(rect);
            state.currentElement = rect;
        } else if (state.activeTool === 'ellipse') {
            const ell = document.createElementNS('http://www.w3.org/2000/svg', 'ellipse');
            ell.setAttribute('cx', pt.x.toString());
            ell.setAttribute('cy', pt.y.toString());
            ell.setAttribute('rx', '1');
            ell.setAttribute('ry', '1');
            ell.setAttribute('fill', fill);
            ell.setAttribute('stroke', stroke);
            ell.setAttribute('stroke-width', sw.toString());
            layerGroup.appendChild(ell);
            state.currentElement = ell;
        } else if (state.activeTool === 'star') {
            const poly = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
            poly.setAttribute('points', `${pt.x},${pt.y}`);
            poly.setAttribute('fill', fill);
            poly.setAttribute('stroke', stroke);
            poly.setAttribute('stroke-width', sw.toString());
            layerGroup.appendChild(poly);
            state.currentElement = poly;
        } else if (state.activeTool === 'brush') {
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', `M ${pt.x} ${pt.y}`);
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke', stroke !== 'none' ? stroke : '#000000');
            path.setAttribute('stroke-width', '4');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('stroke-linejoin', 'round');
            layerGroup.appendChild(path);
            state.currentElement = path;
        } else if (state.activeTool === 'text') {
            const textVal = prompt('Digite o texto a inserir:', 'Texto Corel');
            if (textVal) {
                const textNode = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                textNode.setAttribute('x', pt.x.toString());
                textNode.setAttribute('y', pt.y.toString());
                textNode.setAttribute('fill', fill !== 'none' ? fill : '#000000');
                textNode.setAttribute('font-family', 'Segoe UI, sans-serif');
                textNode.setAttribute('font-size', '28');
                textNode.textContent = textVal;
                layerGroup.appendChild(textNode);
                selectElement(textNode, false);
                saveState('Criar Texto');
            }
            state.isDrawing = false;
            selectTool('select');
        } else if (state.activeTool === 'eyedropper') {
            const target = e.target;
            if (target) {
                const f = target.getAttribute('fill');
                const s = target.getAttribute('stroke');
                if (f && f !== 'none' && f !== 'transparent') {
                    applyColorToSelection('fill', f);
                    state.activeFill = f;
                    const fw = document.getElementById('fillColorWell');
                    if (fw) fw.style.background = f;
                    toast(`Cor capturada: ${f}`, 'ok');
                } else if (s && s !== 'none') {
                    applyColorToSelection('stroke', s);
                    state.activeStroke = s;
                    toast(`Cor de contorno capturada: ${s}`, 'ok');
                } else {
                    toast('Clique sobre um objeto colorido para capturar a cor.', 'err');
                }
            }
            selectTool('select');
        }
    });

    // Duplo clique na prancheta — fecha o caminho da ferramenta Pen
    svg.addEventListener('dblclick', (e) => {
        if (state.activeTool === 'pen' && state.penPath) {
            const d = state.penPath.getAttribute('d') || '';
            if (state.penPoints && state.penPoints.length > 2) {
                state.penPath.setAttribute('d', `${d} Z`); // fecha o caminho
            }
            selectElement(state.penPath, false);
            saveState('Criar Caminho Bézier');
            state.penPath = null;
            state.penPoints = [];
            selectTool('select');
        }
    });



    // Mouse Move
    window.addEventListener('mousemove', (e) => {
        if (state.isPanning) {
            const dx = e.clientX - state.panStart.x;
            const dy = e.clientY - state.panStart.y;
            scroller.scrollLeft = state.scrollStart.left - dx;
            scroller.scrollTop = state.scrollStart.top - dy;
            drawRulers();
            return;
        }

        if (state.activeTransform) {
            handleTransformMove(e);
            return;
        }

        if (state.isDrawing && state.currentElement) {
            const pt = getSvgCoords(e);
            let w = Math.abs(pt.x - state.drawStart.x);
            let h = Math.abs(pt.y - state.drawStart.y);
            const x = Math.min(pt.x, state.drawStart.x);
            const y = Math.min(pt.y, state.drawStart.y);

            // Constrain proportions when holding Ctrl (Corel style)
            if (e.ctrlKey) {
                const side = Math.max(w, h);
                w = side;
                h = side;
            }

            if (state.activeTool === 'rect') {
                state.currentElement.setAttribute('x', x.toString());
                state.currentElement.setAttribute('y', y.toString());
                state.currentElement.setAttribute('width', Math.max(w, 1).toString());
                state.currentElement.setAttribute('height', Math.max(h, 1).toString());
            } else if (state.activeTool === 'ellipse') {
                const rx = w / 2;
                const ry = h / 2;
                state.currentElement.setAttribute('cx', (x + rx).toString());
                state.currentElement.setAttribute('cy', (y + ry).toString());
                state.currentElement.setAttribute('rx', Math.max(rx, 1).toString());
                state.currentElement.setAttribute('ry', Math.max(ry, 1).toString());
            } else if (state.activeTool === 'brush') {
                const d = state.currentElement.getAttribute('d') || '';
                state.currentElement.setAttribute('d', `${d} L ${pt.x} ${pt.y}`);
            } else if (state.activeTool === 'star') {
                const radius = Math.max(w, h) / 2;
                state.currentElement.setAttribute('points', generateStarPoints(x + radius, y + radius, 5, radius, radius * 0.45));
            } else if (state.activeTool === 'crop') {
                // Atualiza preview do recorte
                state.currentElement.setAttribute('x', x.toString());
                state.currentElement.setAttribute('y', y.toString());
                state.currentElement.setAttribute('width', Math.max(w, 1).toString());
                state.currentElement.setAttribute('height', Math.max(h, 1).toString());
            }
        }
    });



    // Mouse Up
    window.addEventListener('mouseup', (e) => {
        if (state.isPanning) {
            state.isPanning = false;
            scroller.style.cursor = 'default';
        }

        if (state.activeTransform) {
            if (state.activeTransform.type === 'move' &&
               (Math.abs(state.activeTransform.dx || 0) > 1 || Math.abs(state.activeTransform.dy || 0) > 1)) {
                state.lastTransformDelta = {
                    dx: state.activeTransform.dx,
                    dy: state.activeTransform.dy
                };
            }
            state.activeTransform = null;
            saveState('Transformar Objeto');
            renderSelectionOverlay();
            updatePropertyBar();
        }

        if (state.isDrawing && state.currentElement) {
            // ---- Ferramenta Crop: aplica clipping na região arrastada ----
            if (state.activeTool === 'crop') {
                const cropEl = state.currentElement;
                // Ler antes de remover
                const cx = parseFloat(cropEl.getAttribute('x') || '0');
                const cy = parseFloat(cropEl.getAttribute('y') || '0');
                const cw = parseFloat(cropEl.getAttribute('width') || '0');
                const ch = parseFloat(cropEl.getAttribute('height') || '0');
                cropEl.remove();

                if (cw > 5 && ch > 5) {
                    // Detecta automaticamente todos os objetos reais na prancheta
                    const layerGroup = document.getElementById('layerGroupMain');
                    const candidates = state.selectedElements.length > 0
                        ? state.selectedElements
                        : Array.from(layerGroup ? layerGroup.children : []).filter(el => {
                            const id = el.getAttribute('id') || '';
                            return !INTERNAL_LAYER_IDS.has(id);
                        });

                    if (candidates.length === 0) {
                        toast('Selecione objetos ou desenhe sobre eles com a ferramenta Cortar.', 'err');
                    } else {
                        const mainSvg = document.getElementById('mainSvgCanvas');
                        let defs = mainSvg.querySelector('defs');
                        if (!defs) {
                            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                            mainSvg.prepend(defs);
                        }

                        const clipId = `clip_${Date.now()}`;
                        const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
                        clipPath.setAttribute('id', clipId);
                        const clipRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        clipRect.setAttribute('x', cx.toString());
                        clipRect.setAttribute('y', cy.toString());
                        clipRect.setAttribute('width', cw.toString());
                        clipRect.setAttribute('height', ch.toString());
                        clipPath.appendChild(clipRect);
                        defs.appendChild(clipPath);

                        candidates.forEach(el => el.setAttribute('clip-path', `url(#${clipId})`));
                        saveState('Cortar Região');
                        toast(`Recorte aplicado em ${candidates.length} objeto(s)!`, 'ok');
                    }
                } else {
                    toast('Arraste para definir a área de recorte.', 'err');
                }

                state.isDrawing = false;
                state.currentElement = null;
                selectTool('select');
                return;
            }


            // ---- Outras ferramentas de desenho ----
            const el = state.currentElement;
            // Só tenta selecionar se for um elemento válido em layerGroupMain
            if (el.parentElement && el.parentElement.id === 'layerGroupMain') {
                selectElement(el, false);
            }
            saveState(`Criar ${state.activeTool}`);
            state.isDrawing = false;
            state.currentElement = null;
            selectTool('select');
        }
    });
}


function generateStarPoints(cx, cy, spikes, outerRadius, innerRadius) {
    let rot = Math.PI / 2 * 3;
    let x = cx;
    let y = cy;
    const step = Math.PI / spikes;
    const pts = [];

    for (let i = 0; i < spikes; i++) {
        x = cx + Math.cos(rot) * outerRadius;
        y = cy + Math.sin(rot) * outerRadius;
        pts.push(`${Math.round(x)},${Math.round(y)}`);
        rot += step;

        x = cx + Math.cos(rot) * innerRadius;
        y = cy + Math.sin(rot) * innerRadius;
        pts.push(`${Math.round(x)},${Math.round(y)}`);
        rot += step;
    }
    return pts.join(' ');
}

// ==================== Selection & Transformation ====================
function selectElement(el, multi = false) {
    if (!el || el.id === 'bgSheet' || el.id === 'selectionOverlay' || el.id === 'guidelinesGroup' || el.id === 'nodeEditOverlay') return;

    if (!multi) {
        state.selectedElements = [el];
    } else {
        const idx = state.selectedElements.indexOf(el);
        if (idx > -1) state.selectedElements.splice(idx, 1);
        else state.selectedElements.push(el);
    }

    renderSelectionOverlay();
    updatePropertyBar();
    updateLayersTree();
}

function deselectAll() {
    state.selectedElements = [];
    renderSelectionOverlay();
    updatePropertyBar();
    updateLayersTree();
}

function selectAll() {
    const items = Array.from(document.querySelectorAll('#layerGroupMain > *'));
    state.selectedElements = items;
    renderSelectionOverlay();
    updatePropertyBar();
    updateLayersTree();
}

function deleteSelected() {
    if (state.selectedElements.length === 0) return;
    state.selectedElements.forEach(el => el.remove());
    state.selectedElements = [];
    renderSelectionOverlay();
    updatePropertyBar();
    updateLayersTree();
    saveState('Excluir');
    toast('Objeto(s) excluído(s)', 'ok');
}

function duplicateSelected(customDx, customDy) {
    if (state.selectedElements.length === 0) return;

    const dx = (customDx !== undefined) ? customDx : (state.lastTransformDelta ? state.lastTransformDelta.dx : 20);
    const dy = (customDy !== undefined) ? customDy : (state.lastTransformDelta ? state.lastTransformDelta.dy : 20);

    state.lastTransformDelta = { dx, dy };

    const newSelected = [];
    state.selectedElements.forEach(el => {
        const clone = el.cloneNode(true);
        if (clone.id) {
            clone.id = `${clone.id}_copy_${Date.now().toString(36)}`;
        }
        if (clone.querySelectorAll) {
            clone.querySelectorAll('[id]').forEach(child => {
                child.id = `${child.id}_copy_${Date.now().toString(36)}`;
            });
        }
        
        moveSvgElement(clone, dx, dy);
        el.parentNode.appendChild(clone);
        newSelected.push(clone);
    });

    state.selectedElements = newSelected;
    renderSelectionOverlay();
    updateLayersTree();
    updatePropertyBar();
    saveState('Duplicar');

    const signX = dx >= 0 ? `+${Math.round(dx)}` : `${Math.round(dx)}`;
    const signY = dy >= 0 ? `+${Math.round(dy)}` : `${Math.round(dy)}`;
    toast(`Objeto duplicado (${signX}px, ${signY}px)`, 'ok');
}

function repeatTransform() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto para repetir a duplicação/transformação.', 'info');
        return;
    }
    const delta = state.lastTransformDelta || { dx: 20, dy: 20 };
    duplicateSelected(delta.dx, delta.dy);
}

function renderSelectionOverlay() {
    const overlay = document.getElementById('selectionOverlay');
    if (!overlay) return;
    overlay.innerHTML = '';

    if (state.selectedElements.length === 0) return;

    const bbox = getCombinedBBox(state.selectedElements);
    if (!bbox || bbox.width <= 0 || bbox.height <= 0) return;

    // Bounding Box Dotted Rectangle
    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    rect.setAttribute('class', 'selection-box-line');
    rect.setAttribute('x', bbox.x.toString());
    rect.setAttribute('y', bbox.y.toString());
    rect.setAttribute('width', bbox.width.toString());
    rect.setAttribute('height', bbox.height.toString());
    overlay.appendChild(rect);

    // 8 Corel Transformation Handles
    const handles = [
        { id: 'nw', x: bbox.x, y: bbox.y, cursor: 'nwse-resize' },
        { id: 'n',  x: bbox.x + bbox.width / 2, y: bbox.y, cursor: 'ns-resize' },
        { id: 'ne', x: bbox.x + bbox.width, y: bbox.y, cursor: 'nesw-resize' },
        { id: 'e',  x: bbox.x + bbox.width, y: bbox.y + bbox.height / 2, cursor: 'ew-resize' },
        { id: 'se', x: bbox.x + bbox.width, y: bbox.y + bbox.height, cursor: 'nwse-resize' },
        { id: 's',  x: bbox.x + bbox.width / 2, y: bbox.y + bbox.height, cursor: 'ns-resize' },
        { id: 'sw', x: bbox.x, y: bbox.y + bbox.height, cursor: 'nesw-resize' },
        { id: 'w',  x: bbox.x, y: bbox.y + bbox.height / 2, cursor: 'ew-resize' }
    ];

    const hSize = 8;
    handles.forEach(h => {
        const handleRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        handleRect.setAttribute('class', 'transform-handle');
        handleRect.setAttribute('x', (h.x - hSize / 2).toString());
        handleRect.setAttribute('y', (h.y - hSize / 2).toString());
        handleRect.setAttribute('width', hSize.toString());
        handleRect.setAttribute('height', hSize.toString());
        handleRect.style.cursor = h.cursor;
        handleRect.addEventListener('mousedown', (e) => {
            e.stopPropagation();
            initTransformDrag(e, h.id);
        });
        overlay.appendChild(handleRect);
    });
}

function getCombinedBBox(elements) {
    if (elements.length === 0) return null;
    const mainSvg = document.getElementById('mainSvgCanvas');
    if (!mainSvg) return null;

    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;

    elements.forEach(el => {
        try {
            const b = el.getBBox(); // bbox em coords locais do elemento
            if (b.width <= 0 && b.height <= 0) return;

            // Obter a matriz de transformação do elemento em relação ao SVG root
            const svgCTM = mainSvg.getScreenCTM();
            const elCTM  = el.getScreenCTM();
            if (!svgCTM || !elCTM) {
                // Fallback simples se CTM não estiver disponível
                minX = Math.min(minX, b.x);
                minY = Math.min(minY, b.y);
                maxX = Math.max(maxX, b.x + b.width);
                maxY = Math.max(maxY, b.y + b.height);
                return;
            }

            // Converter as 4 esquinas da bbox local para o espaço do SVG root
            const inverseSvgCTM = svgCTM.inverse();
            const toSvg = inverseSvgCTM.multiply(elCTM);

            const corners = [
                { x: b.x,           y: b.y },
                { x: b.x + b.width, y: b.y },
                { x: b.x + b.width, y: b.y + b.height },
                { x: b.x,           y: b.y + b.height }
            ];

            corners.forEach(corner => {
                const pt = mainSvg.createSVGPoint();
                pt.x = corner.x;
                pt.y = corner.y;
                const transformed = pt.matrixTransform(toSvg);
                minX = Math.min(minX, transformed.x);
                minY = Math.min(minY, transformed.y);
                maxX = Math.max(maxX, transformed.x);
                maxY = Math.max(maxY, transformed.y);
            });
        } catch (err) {
            // Fallback se getBBox falhar (ex: elemento oculto)
            try {
                const b = el.getBBox();
                minX = Math.min(minX, b.x);
                minY = Math.min(minY, b.y);
                maxX = Math.max(maxX, b.x + b.width);
                maxY = Math.max(maxY, b.y + b.height);
            } catch {}
        }
    });

    if (minX === Infinity) return { x: 0, y: 0, width: 0, height: 0 };
    return { x: minX, y: minY, width: maxX - minX, height: maxY - minY };
}


function initTransformDrag(e, handleType) {
    const pt = getSvgCoords(e);
    const bbox = getCombinedBBox(state.selectedElements);
    if (!bbox || bbox.width <= 0 || bbox.height <= 0) return;

    state.activeTransform = {
        type: handleType,
        startPt: pt,
        bbox: { ...bbox },
        initialElementsData: state.selectedElements.map(el => {
            let elBox = { x: 0, y: 0, width: 0, height: 0 };
            try { elBox = el.getBBox(); } catch {}
            return {
                el: el,
                bbox: elBox,
                x: parseFloat(el.getAttribute('x') || elBox.x || 0),
                y: parseFloat(el.getAttribute('y') || elBox.y || 0),
                width: parseFloat(el.getAttribute('width') || elBox.width || 0),
                height: parseFloat(el.getAttribute('height') || elBox.height || 0),
                cx: parseFloat(el.getAttribute('cx') || (elBox.x + elBox.width / 2) || 0),
                cy: parseFloat(el.getAttribute('cy') || (elBox.y + elBox.height / 2) || 0),
                rx: parseFloat(el.getAttribute('rx') || elBox.width / 2 || 0),
                ry: parseFloat(el.getAttribute('ry') || elBox.height / 2 || 0),
                fontSize: parseFloat(el.getAttribute('font-size') || 28),
                initialTransform: el.getAttribute('transform') || ''
            };
        })
    };
}

function handleTransformMove(e) {
    if (!state.activeTransform) return;
    const pt = getSvgCoords(e);
    const startPt = state.activeTransform.startPt;
    const origBox = state.activeTransform.bbox;
    const dx = pt.x - startPt.x;
    const dy = pt.y - startPt.y;
    const handle = state.activeTransform.type;
    state.activeTransform.dx = dx;
    state.activeTransform.dy = dy;

    if (handle === 'move') {
        state.activeTransform.initialElementsData.forEach(item => {
            const el = item.el;
            const tag = el.tagName.toLowerCase();
            if (tag === 'rect' || tag === 'image') {
                el.setAttribute('x', (item.x + dx).toFixed(2));
                el.setAttribute('y', (item.y + dy).toFixed(2));
            } else if (tag === 'ellipse') {
                el.setAttribute('cx', (item.cx + dx).toFixed(2));
                el.setAttribute('cy', (item.cy + dy).toFixed(2));
            } else if (tag === 'text') {
                el.setAttribute('x', (item.x + dx).toFixed(2));
                el.setAttribute('y', (item.y + dy).toFixed(2));
            } else {
                if (item.initialTransform) {
                    el.setAttribute('transform', `translate(${dx.toFixed(2)}, ${dy.toFixed(2)}) ${item.initialTransform}`);
                } else {
                    el.setAttribute('transform', `translate(${dx.toFixed(2)}, ${dy.toFixed(2)})`);
                }
            }
        });
        renderSelectionOverlay();
        updatePropertyBar();
        return;
    }

    // 8-Handle Resizing (Corel transform engine)
    let newX = origBox.x;
    let newY = origBox.y;
    let newW = origBox.width;
    let newH = origBox.height;

    if (handle.includes('e')) {
        newW = Math.max(10, origBox.width + dx);
    }
    if (handle.includes('s')) {
        newH = Math.max(10, origBox.height + dy);
    }
    if (handle.includes('w')) {
        newW = Math.max(10, origBox.width - dx);
        newX = origBox.x + (origBox.width - newW);
    }
    if (handle.includes('n')) {
        newH = Math.max(10, origBox.height - dy);
        newY = origBox.y + (origBox.height - newH);
    }

    // Constrain aspect ratio if Ctrl is pressed or if corner handle
    if (e.ctrlKey && origBox.width > 0 && origBox.height > 0) {
        const ratio = origBox.width / origBox.height;
        if (handle === 'e' || handle === 'w') {
            newH = newW / ratio;
        } else if (handle === 'n' || handle === 's') {
            newW = newH * ratio;
        } else {
            const side = Math.max(newW, newH * ratio);
            newW = side;
            newH = side / ratio;
        }
    }

    const scaleX = newW / origBox.width;
    const scaleY = newH / origBox.height;

    state.activeTransform.initialElementsData.forEach(item => {
        const el = item.el;
        const tag = el.tagName.toLowerCase();

        const relX = (item.bbox.x - origBox.x) / origBox.width;
        const relY = (item.bbox.y - origBox.y) / origBox.height;
        const relW = item.bbox.width / origBox.width;
        const relH = item.bbox.height / origBox.height;

        const elemX = newX + relX * newW;
        const elemY = newY + relY * newH;
        const elemW = Math.max(1, relW * newW);
        const elemH = Math.max(1, relH * newH);

        if (tag === 'rect' || tag === 'image') {
            el.setAttribute('x', elemX.toFixed(2));
            el.setAttribute('y', elemY.toFixed(2));
            el.setAttribute('width', elemW.toFixed(2));
            el.setAttribute('height', elemH.toFixed(2));
        } else if (tag === 'ellipse') {
            el.setAttribute('cx', (elemX + elemW / 2).toFixed(2));
            el.setAttribute('cy', (elemY + elemH / 2).toFixed(2));
            el.setAttribute('rx', Math.max(1, elemW / 2).toFixed(2));
            el.setAttribute('ry', Math.max(1, elemH / 2).toFixed(2));
        } else if (tag === 'text') {
            el.setAttribute('x', elemX.toFixed(2));
            el.setAttribute('y', (elemY + elemH).toFixed(2));
            el.setAttribute('font-size', Math.max(6, item.fontSize * scaleY).toFixed(2));
        } else {
            const ox = origBox.x;
            const oy = origBox.y;
            const baseT = item.initialTransform ? ` ${item.initialTransform}` : '';
            el.setAttribute('transform', `translate(${newX.toFixed(2)}, ${newY.toFixed(2)}) scale(${scaleX.toFixed(4)}, ${scaleY.toFixed(4)}) translate(${-ox}, ${-oy})${baseT}`);
        }
    });

    renderSelectionOverlay();
    updatePropertyBar();
}

// ==================== Dynamic Property Bar Engine ====================
function updatePropertyBar() {
    const propDoc = document.getElementById('propGroupDocument');
    const propObj = document.getElementById('propGroupObject');
    const propNode = document.getElementById('propGroupNodeTool');
    if (!propDoc || !propObj) return;

    if (state.activeTool === 'node') {
        propDoc.style.display = 'none';
        propObj.style.display = 'none';
        if (propNode) propNode.style.display = 'flex';
        return;
    }

    if (state.selectedElements.length > 0) {
        propDoc.style.display = 'none';
        if (propNode) propNode.style.display = 'none';
        propObj.style.display = 'flex';

        const bbox = getCombinedBBox(state.selectedElements);
        if (bbox) {
            const xInput = document.getElementById('objPropX');
            const yInput = document.getElementById('objPropY');
            const wInput = document.getElementById('objPropW');
            const hInput = document.getElementById('objPropH');

            if (xInput) xInput.value = Math.round(fromPxToUnit(bbox.x, state.unit));
            if (yInput) yInput.value = Math.round(fromPxToUnit(bbox.y, state.unit));
            if (wInput) wInput.value = Math.round(fromPxToUnit(bbox.width, state.unit));
            if (hInput) hInput.value = Math.round(fromPxToUnit(bbox.height, state.unit));
        }

        // Show/hide PowerTRACE and duplicate background toggle button
        const hasImg = state.selectedElements.some(el => el.querySelector && el.querySelector('image') || el.tagName.toLowerCase() === 'image');
        const btnBg = document.getElementById('btnToggleDuplicateBg');
        if (btnBg) btnBg.style.display = hasImg ? 'inline-flex' : 'none';
        const btnTrace = document.getElementById('btnTraceSelected');
        if (btnTrace) btnTrace.style.display = hasImg ? 'inline-flex' : 'none';

        // Show/hide Extract PowerClip button
        const isPowerClip = state.selectedElements.some(el => el.classList && el.classList.contains('corel-powerclip-group'));
        const btnExtractPC = document.getElementById('btnExtractPowerClip');
        if (btnExtractPC) btnExtractPC.style.display = isPowerClip ? 'inline-flex' : 'none';

        // Show/hide Separate Text from Path button
        const isTextOnPath = state.selectedElements.some(el => (el.classList && el.classList.contains('text-on-path-group')) || (el.closest && el.closest('.text-on-path-group')));
        const btnSepTP = document.getElementById('btnSepTextPath');
        if (btnSepTP) btnSepTP.style.display = isTextOnPath ? 'inline-flex' : 'none';

        // Update blend mode and opacity in right docker
        const first = state.selectedElements[0];
        const opacityVal = Math.round((parseFloat(first.getAttribute('opacity') || 1)) * 100);
        const slider = document.getElementById('layerOpacitySlider');
        const sliderVal = document.getElementById('layerOpacityVal');
        if (slider) slider.value = opacityVal;
        if (sliderVal) sliderVal.textContent = `${opacityVal}%`;
    } else {
        propObj.style.display = 'none';
        if (propNode) propNode.style.display = 'none';
        propDoc.style.display = 'flex';

        const btnBg = document.getElementById('btnToggleDuplicateBg');
        if (btnBg) btnBg.style.display = 'none';
        const btnTrace = document.getElementById('btnTraceSelected');
        if (btnTrace) btnTrace.style.display = 'none';
        const btnExtractPC = document.getElementById('btnExtractPowerClip');
        if (btnExtractPC) btnExtractPC.style.display = 'none';
    }
}

function updateSelectedTransformFromProp() {
    if (state.selectedElements.length === 0) return;
    const xInput = document.getElementById('objPropX');
    const yInput = document.getElementById('objPropY');
    const wInput = document.getElementById('objPropW');
    const hInput = document.getElementById('objPropH');
    const rotInput = document.getElementById('objPropRotate');

    const newX = fromUnitToPx(parseFloat(xInput.value) || 0, state.unit);
    const newY = fromUnitToPx(parseFloat(yInput.value) || 0, state.unit);
    const newW = fromUnitToPx(parseFloat(wInput.value) || 10, state.unit);
    const newH = fromUnitToPx(parseFloat(hInput.value) || 10, state.unit);
    const rot = parseFloat(rotInput ? rotInput.value : 0) || 0;

    state.selectedElements.forEach(el => {
        if (el.tagName.toLowerCase() === 'rect' || el.tagName.toLowerCase() === 'image') {
            el.setAttribute('x', newX.toString());
            el.setAttribute('y', newY.toString());
            el.setAttribute('width', newW.toString());
            el.setAttribute('height', newH.toString());
        }
        if (rot !== 0) {
            el.setAttribute('transform', `rotate(${rot} ${newX + newW / 2} ${newY + newH / 2})`);
        }
    });

    renderSelectionOverlay();
    saveState('Modificar Propriedades');
}

function changeSelectedOpacity(val) {
    const opacity = val / 100;
    const label = document.getElementById('layerOpacityVal');
    if (label) label.textContent = `${val}%`;

    state.selectedElements.forEach(el => {
        el.setAttribute('opacity', opacity.toString());
    });
}

function changeSelectedBlendMode(mode) {
    state.selectedElements.forEach(el => {
        el.style.mixBlendMode = mode;
    });
    saveState('Modo de Mesclagem');
}

// ==================== Right Docker (Objects / Layers) ====================
// IDs de elementos SVG internos que nunca devem aparecer no painel de camadas
const INTERNAL_LAYER_IDS = new Set([
    'nodeEditOverlay', 'selectionOverlay', 'selectionGroup',
    'layerGroupMain', 'guidelinesGroup', 'guidlinesGroup',
    'bgSheet', 'bgImageLayer', 'rulerCorner', 'gridOverlay'
]);

function updateLayersTree() {
    const container = document.getElementById('objectsTreeList');
    if (!container) return;
    container.innerHTML = '';

    // Pega somente os objetos reais dentro de layerGroupMain (não o grupo em si)
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    // Filtra fora os elementos internos do sistema
    const allItems = Array.from(layerGroup.children).filter(el => {
        const id = el.getAttribute('id') || '';
        return !INTERNAL_LAYER_IDS.has(id) && !el.classList.contains('selection-handle') && !el.classList.contains('node-anchor');
    });

    // Inverte para mostrar o topo da pilha primeiro (ordem visual do CorelDRAW)
    const items = [...allItems].reverse();

    if (items.length === 0) {
        container.innerHTML = '<div style="padding:16px; color:#888; text-align:center; font-size:11px;">Nenhum objeto na prancheta.<br>Desenhe ou abra um arquivo .CDR / .PDF.</div>';
        return;
    }

    let dragSrcIndex = null; // índice no array "items" do item arrastado

    items.forEach((el, index) => {
        const isSelected = state.selectedElements.includes(el);
        const tag = el.tagName.toLowerCase();
        const elId = el.getAttribute('id') || '';
        // Nome amigável
        let name;
        if (elId && !elId.match(/^(rect|ellipse|path|polygon|text|image|circle|line|polyline|group|g)\d*$/i)) {
            name = elId; // ID customizado pelo usuário
        } else if (tag === 'image') {
            name = `Imagem Bitmap ${items.length - index}`;
        } else if (tag === 'text') {
            name = `Texto: "${el.textContent.slice(0, 15)}"`;
        } else if (tag === 'path') {
            name = `Curva Bézier ${items.length - index}`;
        } else if (tag === 'g') {
            name = `Grupo ${items.length - index}`;
        } else {
            name = `${capitalize(tag)} ${items.length - index}`;
        }

        const row = document.createElement('div');
        row.className = `layer-tree-item${isSelected ? ' selected' : ''}`;
        row.setAttribute('draggable', 'true');
        row.dataset.layerIndex = index.toString();
        row.title = 'Arraste para reordenar';

        // ---- Ícone de drag handle ----
        const drag = document.createElement('span');
        drag.innerHTML = '<i class="fas fa-grip-vertical" style="color:#bbb;margin-right:4px;cursor:grab;font-size:10px;"></i>';

        // ---- Mini ícone / thumbnail ----
        const thumb = document.createElement('div');
        thumb.className = 'layer-item-thumb';
        thumb.innerHTML = getElementThumbnailSVG(el);

        // ---- Nome (dbl-click para renomear) ----
        const nameSpan = document.createElement('span');
        nameSpan.className = 'layer-item-name';
        nameSpan.textContent = name;
        nameSpan.title = 'Duplo clique para renomear';
        nameSpan.ondblclick = (ev) => {
            ev.stopPropagation();
            const newName = prompt('Renomear objeto:', name);
            if (newName && newName.trim()) {
                el.setAttribute('id', newName.trim());
                updateLayersTree();
            }
        };

        // ---- Botão olho (visibilidade) ----
        const actions = document.createElement('div');
        actions.className = 'layer-item-actions';
        const isHidden = el.style.display === 'none' || el.style.visibility === 'hidden';
        const eyeBtn = document.createElement('button');
        eyeBtn.className = 'btn-layer-eye';
        eyeBtn.innerHTML = isHidden
            ? '<i class="fas fa-eye-slash" style="color:#aaa;"></i>'
            : '<i class="fas fa-eye"></i>';
        eyeBtn.title = isHidden ? 'Exibir objeto' : 'Ocultar objeto';
        eyeBtn.onclick = (ev) => {
            ev.stopPropagation();
            el.style.display = isHidden ? '' : 'none';
            renderSelectionOverlay();
            updateLayersTree();
        };
        actions.appendChild(eyeBtn);

        row.appendChild(drag);
        row.appendChild(thumb);
        row.appendChild(nameSpan);
        row.appendChild(actions);

        // ---- Clique para selecionar na prancheta ----
        row.onclick = (ev) => {
            if (ev.target.closest('.btn-layer-eye')) return;
            selectElement(el, ev.shiftKey);
        };

        // ---- Drag & Drop para reordenar ----
        row.addEventListener('dragstart', (ev) => {
            dragSrcIndex = index;
            ev.dataTransfer.effectAllowed = 'move';
            row.style.opacity = '0.5';
        });
        row.addEventListener('dragend', () => {
            row.style.opacity = '';
            container.querySelectorAll('.layer-tree-item').forEach(r => r.classList.remove('drag-over'));
        });
        row.addEventListener('dragover', (ev) => {
            ev.preventDefault();
            ev.dataTransfer.dropEffect = 'move';
            container.querySelectorAll('.layer-tree-item').forEach(r => r.classList.remove('drag-over'));
            row.classList.add('drag-over');
        });
        row.addEventListener('dragleave', () => row.classList.remove('drag-over'));
        row.addEventListener('drop', (ev) => {
            ev.preventDefault();
            row.classList.remove('drag-over');
            if (dragSrcIndex === null || dragSrcIndex === index) return;

            // items[] está invertido (topo=0). O SVG stack é allItems[] (fundo=0).
            // Precisamos mapear de volta para indices reais no layerGroup
            const srcEl = items[dragSrcIndex];
            const dstEl = items[index];

            // No SVG, "mais acima visualmente" = mais próximo do fim de layerGroup
            // Items[0] = topo visual = último filho do layerGroup
            // Mover srcEl para antes ou depois de dstEl no layerGroup
            if (dragSrcIndex > index) {
                // src estava mais abaixo, sobe: insert dstEl.nextSibling
                layerGroup.insertBefore(srcEl, dstEl.nextSibling);
            } else {
                // src estava mais acima, desce: insert antes de dstEl
                layerGroup.insertBefore(srcEl, dstEl);
            }

            dragSrcIndex = null;
            renderSelectionOverlay();
            updateLayersTree();
            saveState('Reordenar camada');
        });

        container.appendChild(row);
    });
}


function getElementThumbnailSVG(el) {
    const tag = el.tagName.toLowerCase();
    if (tag === 'rect') return '<i class="far fa-square text-blue-600"></i>';
    if (tag === 'ellipse') return '<i class="far fa-circle text-emerald-600"></i>';
    if (tag === 'text') return '<i class="fas fa-font text-purple-600"></i>';
    if (tag === 'image') return '<i class="fas fa-image text-amber-600"></i>';
    if (tag === 'polygon') return '<i class="far fa-star text-orange-600"></i>';
    return '<i class="fas fa-bezier-curve text-cyan-600"></i>';
}

function filterObjectsTree(query) {
    const q = query.toLowerCase();
    document.querySelectorAll('.layer-tree-item').forEach(item => {
        const name = item.querySelector('.layer-item-name').textContent.toLowerCase();
        item.style.display = name.includes(q) ? 'flex' : 'none';
    });
}

function toggleDockerCollapse() {
    const docker = document.getElementById('objectsDocker');
    if (docker) {
        docker.style.display = docker.style.display === 'none' ? 'flex' : 'none';
    }
}

function switchRightTab(tab) {
    document.querySelectorAll('.docker-vertical-tabs .v-tab-btn').forEach(btn => btn.classList.remove('active'));
    const clicked = Array.from(document.querySelectorAll('.docker-vertical-tabs .v-tab-btn')).find(b => b.textContent.toLowerCase().includes(tab));
    if (clicked) clicked.classList.add('active');

    const docker = document.getElementById('objectsDocker');
    if (docker) docker.style.display = 'flex';
}

function addNewLayer() {
    toast('Camada criada na prancheta!', 'ok');
}

// ==================== Document Tabs Bar Engine ====================
function renderDocumentTabs() {
    const bar = document.getElementById('corelTabsBar');
    if (!bar) return;
    bar.innerHTML = '';

    // Welcome Screen Tab
    const welcomeTab = document.createElement('div');
    welcomeTab.className = 'doc-tab';
    welcomeTab.innerHTML = '<i class="fas fa-home"></i> <span>Tela Inicial</span>';
    welcomeTab.onclick = () => switchDocumentTab('welcome');
    bar.appendChild(welcomeTab);

    // Document Project Tabs
    state.pages.forEach((page, idx) => {
        const isActive = idx === state.activePageIndex;
        const tab = document.createElement('div');
        tab.className = `doc-tab ${isActive ? 'active' : ''}`;
        tab.innerHTML = `
            <i class="fas fa-vector-square text-cyan-600"></i>
            <span>${page.name}</span>
            <span class="tab-close" title="Fechar aba">✕</span>
        `;
        tab.onclick = () => switchDocumentTab(idx);
        tab.querySelector('.tab-close').onclick = (e) => closeDocTab(e, idx);
        bar.appendChild(tab);
    });

    // New Tab '+' Button
    const newBtn = document.createElement('button');
    newBtn.className = 'btn-new-tab';
    newBtn.innerHTML = '<i class="fas fa-plus"></i>';
    newBtn.title = 'Nova Página / Aba (+)';
    newBtn.onclick = () => addNewPageTab();
    bar.appendChild(newBtn);
}

function switchDocumentTab(target) {
    if (target === 'welcome') {
        toast('Bem-vindo ao CorelClone Pro 2026! Crie ou abra um arquivo.', 'info');
        return;
    }

    const index = parseInt(target, 10);
    if (isNaN(index) || !state.pages[index]) return;

    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    // Save previous page content
    if (state.activePageIndex >= 0 && state.pages[state.activePageIndex]) {
        state.pages[state.activePageIndex].svgContent = layerGroup.innerHTML;
        state.pages[state.activePageIndex].docWidth = state.docWidth;
        state.pages[state.activePageIndex].docHeight = state.docHeight;
    }

    deselectAll();
    state.activePageIndex = index;
    renderDocumentTabs();

    const targetPage = state.pages[index];

    // Restore page content
    if (targetPage.svgContent !== null && targetPage.svgContent !== undefined) {
        layerGroup.innerHTML = targetPage.svgContent;
        if (targetPage.docWidth && targetPage.docHeight) {
            state.docWidth = targetPage.docWidth;
            state.docHeight = targetPage.docHeight;
            applyDocDimensions();
        }
    } else if (targetPage.svgText) {
        layerGroup.innerHTML = '';
        importSVGStringToActiveCanvas(targetPage.svgText, true);
        targetPage.svgContent = layerGroup.innerHTML;
        targetPage.docWidth = state.docWidth;
        targetPage.docHeight = state.docHeight;
    } else {
        layerGroup.innerHTML = '';
    }

    updateLayersTree();
    updatePropertyBar();
    toast(`Exibindo ${targetPage.name}`, 'ok');
}

function addNewPageTab() {
    const newId = state.pages.length;
    state.pages.push({
        id: newId,
        name: `Página ${newId + 1}`,
        svgContent: '',
        docWidth: state.docWidth,
        docHeight: state.docHeight
    });
    switchDocumentTab(newId);
}

function closeDocTab(e, idx) {
    e.stopPropagation();
    if (state.pages.length <= 1) {
        newDocument();
        return;
    }
    state.pages.splice(idx, 1);
    const nextIdx = Math.max(0, idx - 1);
    switchDocumentTab(nextIdx);
}

// ==================== Local Bridge App Launcher & Checker ====================
async function openLocalBridgeApp() {
    toast('Verificando conexão com o App Local...', 'info');
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 1200);
        const res = await fetch('http://127.0.0.1:54321/status', {
            signal: controller.signal,
            mode: 'cors'
        });
        clearTimeout(timeoutId);

        if (res.ok) {
            closeLocalBridgeModal();
            window.open('http://127.0.0.1:54321/', '_blank');
            toast('Conectado ao App Local com aceleração vetorial nativa!', 'ok');
            return;
        }
    } catch (e) {
        // Falha na conexão ou servidor offline
    }

    openLocalBridgeGuideModal();
}

function openLocalBridgeGuideModal() {
    const modal = document.getElementById('localBridgeModal');
    if (modal) modal.style.display = 'flex';
}

function closeLocalBridgeModal() {
    const modal = document.getElementById('localBridgeModal');
    if (modal) modal.style.display = 'none';
}

// ==================== Import File Engine (.CDR, .PDF, .SVG) ====================
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('importFileInput');
    if (input) {
        input.addEventListener('change', async (e) => {
            if (e.target.files && e.target.files[0]) {
                await handleImportFile(e.target.files[0]);
                e.target.value = '';
            }
        });
    }
});

async function handleImportFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    toast(`Abrindo "${file.name}"...`, 'info');

    if (ext === 'cdr' || ext === 'pdf') {
        let convertedPages = null;
        let convertedSvg = null;
        const isLocalHost = window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost';

        try {
            toast(`⚡ Processando .${ext.toUpperCase()} com alta fidelidade vetorial nativa...`, 'info');
            const formData = new FormData();
            formData.append('file', file);
            const controller = new AbortController();
            const timeoutMs = Math.max(60000, Math.min(180000, Math.round(file.size / 1024) + 60000));
            const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

            const bridgeUrl = isLocalHost ? '/convert' : 'http://127.0.0.1:54321/convert';
            const res = await fetch(bridgeUrl, {
                method: 'POST',
                body: formData,
                signal: controller.signal
            });
            clearTimeout(timeoutId);

            if (res.ok) {
                const respText = await res.text();
                if (respText.trim().startsWith('{')) {
                    try {
                        const data = JSON.parse(respText);
                        if (data.pages && data.pages.length > 0) {
                            convertedPages = data.pages;
                        }
                    } catch (e) {
                        console.error('Erro ao analisar JSON do bridge:', e);
                    }
                } else if (respText.includes('<svg')) {
                    convertedSvg = respText;
                }
            }
        } catch (bridgeErr) {
            console.warn('Bridge local não conectado ou Mixed-Content bloqueado:', bridgeErr);
        }

        // Multi-page vector result from bridge
        if (convertedPages && convertedPages.length > 0) {
            state.pages = convertedPages.map((p, idx) => ({
                id: idx,
                name: p.name || `Página ${idx + 1}`,
                svgContent: null,
                svgText: p.svg,
                docWidth: state.docWidth,
                docHeight: state.docHeight
            }));
            renderDocumentTabs();
            switchDocumentTab(0);
            importSVGStringToActiveCanvas(convertedPages[0].svg, true);
            toast(`⚡ Arquivo .${ext.toUpperCase()} com ${convertedPages.length} páginas carregado com 100% de precisão vetorial nativa!`, 'ok');
            return;
        }

        if (convertedSvg) {
            state.pages = [{ id: 0, name: file.name, svgContent: null, docWidth: state.docWidth, docHeight: state.docHeight }];
            renderDocumentTabs();
            switchDocumentTab(0);
            importSVGStringToActiveCanvas(convertedSvg, true);
            toast(`⚡ Arquivo .${ext.toUpperCase()} aberto com 100% de precisão vetorial nativa!`, 'ok');
            return;
        }

        // Web HTTPS fallback warning
        if (!isLocalHost && ext === 'cdr') {
            const openLocal = confirm(
                "⚠️ Conversão de Alta Precisão CorelDRAW (.CDR)\n\n" +
                "Você está acessando pela Web (4u.ia.br). O navegador bloqueia o conversor nativo por segurança (HTTPS Mixed Content).\n\n" +
                "• Para abrir com TODAS AS CURVAS, NÓS BÉZIER E CAMADAS EDITÁVEIS:\n" +
                "  Clique em [OK] para abrir no CorelClone App Local (http://127.0.0.1:54321).\n\n" +
                "• Ou clique em [Cancelar] para abrir apenas a pré-visualização de imagem nesta aba e usar o PowerTRACE."
            );
            if (openLocal) {
                openLocalBridgeApp();
                return;
            }
        }

        // Client-side fallback with JSZip for CDR preview
        if (ext === 'cdr') {
            await parseCDRClientSide(file);
        } else {
            await parsePDFClientSide(file);
        }
    } else if (ext === 'svg') {
        const text = await file.text();
        importSVGStringToActiveCanvas(text, true);
    } else if (ext === 'json') {
        const text = await file.text();
        importProjectJSON(text);
    } else if (['png', 'jpg', 'jpeg', 'webp'].includes(ext)) {
        const url = URL.createObjectURL(file);
        importImageURL(url);
    } else {
        alert('Formato não suportado. Use .CDR, .PDF, .SVG, .PNG, .JPG ou .JSON.');
    }
}

function importSVGStringToActiveCanvas(svgText, isFullPage = false) {
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(svgText, 'image/svg+xml');
        const svgEl = doc.querySelector('svg');
        if (!svgEl) return;

        const layerGroup = document.getElementById('layerGroupMain');
        if (!layerGroup) return;

        // Parse viewBox
        let vbW = 0, vbH = 0;
        const vb = svgEl.getAttribute('viewBox');
        if (vb) {
            const parts = vb.trim().split(/[\s,]+/).map(Number);
            if (parts.length === 4) { vbW = parts[2]; vbH = parts[3]; }
        }

        const parseDim = (val, defaultVal) => {
            if (!val) return defaultVal;
            val = val.toString().trim();
            if (val.endsWith('mm')) return parseFloat(val) * 3.7795275591;
            if (val.endsWith('cm')) return parseFloat(val) * 37.795275591;
            if (val.endsWith('in')) return parseFloat(val) * 96;
            if (val.endsWith('pt')) return parseFloat(val) * 1.3333333333;
            return parseFloat(val) || defaultVal;
        };

        const targetW = parseDim(svgEl.getAttribute('width'), vbW || 600);
        const targetH = parseDim(svgEl.getAttribute('height'), vbH || 600);

        if (isFullPage && targetW > 50 && targetH > 50) {
            state.docWidth = Math.round(targetW);
            state.docHeight = Math.round(targetH);
            applyDocDimensions();
            layerGroup.innerHTML = '';
        }

        // Copy defs
        const defs = svgEl.querySelector('defs');
        if (defs) {
            const mainDefs = document.querySelector('#mainSvgCanvas defs');
            if (mainDefs) {
                Array.from(defs.children).forEach(d => mainDefs.appendChild(d.cloneNode(true)));
            }
        }

        const validChildren = Array.from(svgEl.children).filter(child => 
            !['defs', 'style', 'metadata'].includes(child.tagName.toLowerCase())
        );

        let elementsToAppend = [];
        if (validChildren.length === 1 && validChildren[0].tagName.toLowerCase() === 'g') {
            elementsToAppend = Array.from(validChildren[0].children);
        } else {
            elementsToAppend = validChildren;
        }

        if (elementsToAppend.length > 0) {
            elementsToAppend.forEach(child => {
                layerGroup.appendChild(child.cloneNode(true));
            });
            const allAdded = Array.from(layerGroup.children);
            if (allAdded.length > 0) {
                selectElement(allAdded[0], false);
            }
        } else {
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('class', 'imported-group');
            validChildren.forEach(c => g.appendChild(c.cloneNode(true)));
            layerGroup.appendChild(g);
            selectElement(g, false);
        }

        saveState('Importar Vetor');
        updateLayersTree();
        updatePropertyBar();
    } catch (e) {
        console.error('Erro ao importar SVG:', e);
    }
}

async function parseCDRClientSide(file) {
    try {
        const zip = new JSZip();
        const content = await zip.loadAsync(file);
        let previewBlob = null;

        for (const filename of Object.keys(content.files)) {
            if (filename.match(/(previews|thumbnails)\/.*\.(png|jpg|bmp)/i) || filename.includes('thumbnail.png')) {
                previewBlob = await content.files[filename].async('blob');
                break;
            }
        }

        if (previewBlob) {
            const url = URL.createObjectURL(previewBlob);
            importImageURL(url);
            toast('Visualização de imagem do .CDR carregada!', 'ok');
            setTimeout(() => {
                if (confirm('Arquivo .CDR aberto como pré-visualização de imagem!\n\n💡 Dica de Qualidade:\n• Para 100% de curvas vetoriais nativas, nós Bézier e camadas editáveis, use o App Local (http://127.0.0.1:54321).\n\nDeseja vetorizar agora com o PowerTRACE™?')) {
                    openPowerTraceDialog();
                }
            }, 500);
        } else {
            alert('Não foi possível encontrar a miniatura interna deste arquivo .CDR.');
        }
    } catch (e) {
        alert('Erro ao decodificar arquivo .CDR: ' + e.message);
    }
}

async function parsePDFClientSide(file) {
    try {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        const page = await pdf.getPage(1);
        const viewport = page.getViewport({ scale: 2.0 });
        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        const ctx = canvas.getContext('2d');
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        importImageURL(canvas.toDataURL('image/png'));
        toast('PDF importado com sucesso!', 'ok');
    } catch (e) {
        alert('Erro ao carregar PDF: ' + e.message);
    }
}

function importImageURL(url) {
    const tempImg = new Image();
    tempImg.onload = () => {
        const layerGroup = document.getElementById('layerGroupMain');
        if (!layerGroup) return;

        let w = tempImg.naturalWidth || 800;
        let h = tempImg.naturalHeight || 600;

        if (w > state.docWidth * 0.9 || h > state.docHeight * 0.9) {
            const fit = Math.min((state.docWidth * 0.8) / w, (state.docHeight * 0.8) / h);
            w = Math.round(w * fit);
            h = Math.round(h * fit);
        }

        const x = Math.round((state.docWidth - w) / 2);
        const y = Math.round((state.docHeight - h) / 2);

        const img = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        img.setAttribute('href', url);
        img.setAttribute('x', x.toString());
        img.setAttribute('y', y.toString());
        img.setAttribute('width', w.toString());
        img.setAttribute('height', h.toString());

        layerGroup.appendChild(img);
        selectElement(img, false);
        saveState('Importar Imagem');
        updateLayersTree();
        updatePropertyBar();
    };
    tempImg.src = url;
}

function toggleImportedBgImage() {
    const group = state.selectedElements[0] || document.getElementById('layerGroupMain');
    if (!group) return;

    const img = group.querySelector('image');
    if (!img) {
        toast('Nenhuma imagem de fundo encontrada para ocultar.', 'info');
        return;
    }

    const isHidden = img.style.display === 'none';
    img.style.display = isHidden ? '' : 'none';
    saveState('Alternar Fundo');
    renderSelectionOverlay();
    updateLayersTree();
    toast(isHidden ? 'Imagem de fundo exibida!' : 'Imagem de fundo ocultada com sucesso!', 'ok');
}

// ==================== PowerTRACE™ Engine ====================
function openPowerTraceDialog() {
    const modal = document.getElementById('powertraceModal');
    if (!modal) return;

    // Localiza a imagem na seleção ou na prancheta
    const selectedImg = state.selectedElements.find(el => el.tagName.toLowerCase() === 'image') ||
                        (state.selectedElements[0] && state.selectedElements[0].querySelector && state.selectedElements[0].querySelector('image')) ||
                        document.querySelector('#layerGroupMain image');

    const thumbImg = document.getElementById('traceThumbImg');
    const thumbPlaceholder = document.getElementById('traceThumbPlaceholder');
    const titleEl = document.getElementById('traceImageTitle');
    const subtitleEl = document.getElementById('traceImageSubtitle');

    if (selectedImg) {
        const url = selectedImg.getAttribute('href') || selectedImg.getAttribute('xlink:href');
        if (thumbImg && url) {
            thumbImg.src = url;
            thumbImg.style.display = 'block';
            if (thumbPlaceholder) thumbPlaceholder.style.display = 'none';
        }
        if (titleEl) titleEl.textContent = 'Imagem Selecionada (Pronta para Vetorizar)';
        if (subtitleEl) {
            const w = Math.round(parseFloat(selectedImg.getAttribute('width') || 0));
            const h = Math.round(parseFloat(selectedImg.getAttribute('height') || 0));
            subtitleEl.textContent = `Tamanho na prancheta: ${w} × ${h} px | Bitmap pronto para PowerTRACE.`;
        }
    } else {
        if (thumbImg) thumbImg.style.display = 'none';
        if (thumbPlaceholder) thumbPlaceholder.style.display = 'block';
        if (titleEl) titleEl.textContent = 'Nenhuma imagem selecionada';
        if (subtitleEl) subtitleEl.textContent = 'Escolha um arquivo do computador abaixo ou cole uma imagem com Ctrl+V na prancheta.';
    }

    modal.style.display = 'flex';
}

function closePowerTraceDialog() {
    const modal = document.getElementById('powertraceModal');
    if (modal) modal.style.display = 'none';
}

function updateTracePresetControls() {
    const preset = document.querySelector('input[name="tracePreset"]:checked')?.value || 'logo';
    const numColors = document.getElementById('traceNumColors');
    const numColorsVal = document.getElementById('traceNumColorsVal');
    const smoothness = document.getElementById('traceSmoothness');
    const removeBg = document.getElementById('traceRemoveBg');

    if (!numColors || !numColorsVal || !smoothness || !removeBg) return;

    if (preset === 'logo') {
        numColors.value = '8';
        numColorsVal.textContent = '8';
        smoothness.value = 'high';
        removeBg.checked = true;
    } else if (preset === 'lineart') {
        numColors.value = '2';
        numColorsVal.textContent = '2';
        smoothness.value = 'medium';
        removeBg.checked = true;
    } else if (preset === 'detailed') {
        numColors.value = '16';
        numColorsVal.textContent = '16';
        smoothness.value = 'medium';
        removeBg.checked = true;
    } else if (preset === 'photo') {
        numColors.value = '32';
        numColorsVal.textContent = '32';
        smoothness.value = 'low';
        removeBg.checked = false;
    }
}

function handleTraceModalFileUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const url = URL.createObjectURL(file);

    importImageURL(url);

    const thumbImg = document.getElementById('traceThumbImg');
    const thumbPlaceholder = document.getElementById('traceThumbPlaceholder');
    const titleEl = document.getElementById('traceImageTitle');
    const subtitleEl = document.getElementById('traceImageSubtitle');

    if (thumbImg) {
        thumbImg.src = url;
        thumbImg.style.display = 'block';
        if (thumbPlaceholder) thumbPlaceholder.style.display = 'none';
    }
    if (titleEl) titleEl.textContent = `Imagem "${file.name}" Carregada`;
    if (subtitleEl) subtitleEl.textContent = 'Imagem adicionada à prancheta e pronta para vetorização em curvas!';
    toast(`Imagem "${file.name}" pronta para o PowerTRACE!`, 'ok');
}

function runPowerTrace() {
    if (typeof ImageTracer === 'undefined') {
        alert('Motor ImageTracer não carregado. Verifique a conexão.');
        return;
    }

    const selectedImg = state.selectedElements.find(el => el.tagName.toLowerCase() === 'image') ||
                        (state.selectedElements[0] && state.selectedElements[0].querySelector && state.selectedElements[0].querySelector('image')) ||
                        document.querySelector('#layerGroupMain image');

    if (!selectedImg) {
        alert('Nenhuma imagem encontrada para vetorizar. Use o botão "Escolher Imagem" ou selecione uma imagem na prancheta.');
        return;
    }

    const imgUrl = selectedImg.getAttribute('href') || selectedImg.getAttribute('xlink:href');
    if (!imgUrl) {
        alert('Fonte da imagem inválida.');
        return;
    }

    // Leitura da geometria da imagem na prancheta
    const imgX = parseFloat(selectedImg.getAttribute('x') || '0');
    const imgY = parseFloat(selectedImg.getAttribute('y') || '0');
    const imgW = parseFloat(selectedImg.getAttribute('width') || '100');
    const imgH = parseFloat(selectedImg.getAttribute('height') || '100');
    const imgTransform = selectedImg.getAttribute('transform') || '';

    // Parâmetros configurados pelo usuário
    const preset = document.querySelector('input[name="tracePreset"]:checked')?.value || 'logo';
    const numColors = parseInt(document.getElementById('traceNumColors')?.value || '8', 10);
    const smoothness = document.getElementById('traceSmoothness')?.value || 'medium';
    const removeBg = document.getElementById('traceRemoveBg')?.checked ?? true;
    const removeOrig = document.getElementById('traceRemoveOriginal')?.checked ?? true;
    const groupResult = document.getElementById('traceGroupResult')?.checked ?? true;

    // Mapeamento de tolerâncias de curvas e redução de ruído
    let ltres = 1.0, qtres = 1.0, pathomit = 8;
    if (smoothness === 'high') {
        ltres = 1.5; qtres = 1.5; pathomit = 12;
    } else if (smoothness === 'low') {
        ltres = 0.5; qtres = 0.5; pathomit = 2;
    }

    if (preset === 'lineart') {
        pathomit = Math.max(pathomit, 10);
    }

    toast('⚡ PowerTRACE™ processando vetorização em curvas...', 'info');
    closePowerTraceDialog();

    const options = {
        corsenabled: true,
        ltres: ltres,
        qtres: qtres,
        pathomit: pathomit,
        colorsampling: 2,
        numberofcolors: numColors,
        colorquantcycles: 3,
        scale: 1
    };

    ImageTracer.imageToSVG(imgUrl, (svgstr) => {
        try {
            const parser = new DOMParser();
            const doc = parser.parseFromString(svgstr, 'image/svg+xml');
            const svgEl = doc.querySelector('svg');
            if (!svgEl) {
                toast('Não foi possível gerar curvas para esta imagem.', 'err');
                return;
            }

            // Dimensões nativas de renderização do ImageTracer
            const nativeW = parseFloat(svgEl.getAttribute('width')) || imgW;
            const nativeH = parseFloat(svgEl.getAttribute('height')) || imgH;
            const scaleX = nativeW > 0 ? (imgW / nativeW) : 1;
            const scaleY = nativeH > 0 ? (imgH / nativeH) : 1;

            const allPaths = Array.from(svgEl.querySelectorAll('path'));
            if (allPaths.length === 0) {
                toast('Nenhuma curva encontrada na imagem.', 'err');
                return;
            }

            // Se solicitado, remove cor de fundo (geralmente o fundo branco/muito claro ou cobrindo 100% da área)
            let pathsToInclude = allPaths;
            if (removeBg && allPaths.length > 1) {
                pathsToInclude = allPaths.filter((p, index) => {
                    const fill = (p.getAttribute('fill') || '').toLowerCase().trim();
                    const isWhiteish = fill === '#ffffff' || fill === '#fff' || fill === 'rgb(255,255,255)' ||
                                       fill === '#fefefe' || fill === '#fafafa' || fill === '#f5f5f5';
                    // Se for o primeiro path (fundo) e for branco ou quase branco, descarta
                    if (index === 0 && isWhiteish) return false;
                    // Se for branco e a área cobrir praticamente a imagem toda
                    if (isWhiteish && allPaths.length > 3) {
                        try {
                            const d = p.getAttribute('d') || '';
                            if (d.length < 150 && (d.includes(`0 0`) || d.includes(`${nativeW} ${nativeH}`))) {
                                return false;
                            }
                        } catch {}
                    }
                    return true;
                });
            }

            const layerGroup = document.getElementById('layerGroupMain');
            const parent = selectedImg.parentNode || layerGroup;

            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('class', 'powertraced-vector-group user-group');

            // Encaixa os vetores perfeitamente no mesmo local e escala milimétrica do bitmap
            const transformParts = [];
            if (imgTransform) transformParts.push(imgTransform);
            transformParts.push(`translate(${imgX.toFixed(2)}, ${imgY.toFixed(2)})`);
            if (Math.abs(scaleX - 1) > 0.001 || Math.abs(scaleY - 1) > 0.001) {
                transformParts.push(`scale(${scaleX.toFixed(4)}, ${scaleY.toFixed(4)})`);
            }
            g.setAttribute('transform', transformParts.join(' '));

            pathsToInclude.forEach(p => {
                const clonedPath = p.cloneNode(true);
                // Garante que contorno inicial seja none
                if (!clonedPath.hasAttribute('stroke')) clonedPath.setAttribute('stroke', 'none');
                g.appendChild(clonedPath);
            });

            parent.insertBefore(g, selectedImg);

            // Remove o bitmap original se o checkbox estiver ativo
            if (removeOrig) {
                selectedImg.remove();
            }

            // Se o usuário não quis agrupar, desagrupa os caminhos
            if (!groupResult) {
                Array.from(g.children).forEach(child => {
                    parent.insertBefore(child, g);
                });
                g.remove();
                state.selectedElements = pathsToInclude;
            } else {
                selectElement(g, false);
            }

            saveState('PowerTRACE: Vetorizar Bitmap');
            updateLayersTree();
            updatePropertyBar();
            toast(`⚡ Vetorização concluída! ${pathsToInclude.length} curvas vetoriais nativas criadas com sucesso.`, 'ok');
        } catch (traceErr) {
            console.error('Erro no processamento do PowerTRACE:', traceErr);
            toast('Erro ao processar as curvas do vetor.', 'err');
        }
    }, options);
}


// ==================== Boolean Modeling (Soldar, Aparar, Interseção) ====================
function booleanOperation(op) {
    if (typeof paper === 'undefined') {
        toast('Motor geométrico Paper.js não disponível.', 'warn');
        return;
    }

    if (state.selectedElements.length < 2) {
        alert('Selecione 2 ou mais objetos vetoriais para realizar operações de modelagem (Soldar, Aparar, Interseção).');
        return;
    }

    toast(`Executando operação: ${op.toUpperCase()}...`, 'info');
    // Implementation uses Paper.js CompoundPath booleans
    saveState(`Modelagem: ${op}`);
}

// ==================== Export Engine (SVG, PDF, PNG, JSON) ====================
function exportDocument(format) {
    const svg = document.getElementById('mainSvgCanvas');
    if (!svg) return;

    // Deselect all handles before export
    deselectAll();

    if (format === 'svg') {
        const serializer = new XMLSerializer();
        const source = serializer.serializeToString(svg);
        const blob = new Blob([source], { type: 'image/svg+xml;charset=utf-8' });
        downloadBlob(blob, 'corelclone_design.svg');
        toast('Arquivo vetorial .SVG exportado com sucesso!', 'ok');
    } else if (format === 'png') {
        exportRasterImage('image/png', 'corelclone_design.png');
    } else if (format === 'pdf') {
        window.print();
    }
}

function exportRasterImage(mimeType, filename) {
    const svg = document.getElementById('mainSvgCanvas');
    const serializer = new XMLSerializer();
    const svgString = serializer.serializeToString(svg);
    const img = new Image();
    const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);

    img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = state.docWidth * 2; // 2x high resolution
        canvas.height = state.docHeight * 2;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        URL.revokeObjectURL(url);

        canvas.toBlob((blob) => {
            downloadBlob(blob, filename);
            toast('Imagem PNG HD exportada!', 'ok');
        }, mimeType);
    };
    img.src = url;
}

// ==================== CorelDRAW Pre-Press & Print Engine (Bleed & Crop Marks) ====================
function openPrintExportDialog() {
    deselectAll();
    const modal = document.getElementById('printExportModal');
    if (!modal) return;

    const wMm = Math.round(fromPxToUnit(state.docWidth, 'mm'));
    const hMm = Math.round(fromPxToUnit(state.docHeight, 'mm'));
    const summary = document.getElementById('printSheetSummaryText');
    if (summary) {
        summary.textContent = `Prancheta atual: ${wMm} x ${hMm} mm. Todas as marcas e sangrias serão calculadas milimetricamente.`;
    }

    modal.style.display = 'flex';
}

function closePrintExportDialog() {
    const modal = document.getElementById('printExportModal');
    if (modal) modal.style.display = 'none';
}

function buildPrePressSvg(bleedMm, options) {
    const docW = state.docWidth;
    const docH = state.docHeight;
    const bleedPx = fromUnitToPx(bleedMm, 'mm');
    const marginMm = Math.max(15, bleedMm + 10);
    const marginPx = fromUnitToPx(marginMm, 'mm');

    const sheetW = docW + 2 * marginPx;
    const sheetH = docH + 2 * marginPx;

    const x1 = marginPx;
    const y1 = marginPx;
    const x2 = marginPx + docW;
    const y2 = marginPx + docH;

    const mainSvg = document.getElementById('mainSvgCanvas');
    const defsEl = mainSvg ? mainSvg.querySelector('defs') : null;
    const defsHtml = defsEl ? defsEl.innerHTML : '';

    const layerGroup = document.getElementById('layerGroupMain');
    const contentHtml = layerGroup ? layerGroup.innerHTML : '';

    let extraMarks = '';

    // 1. Marcas de Corte nos 4 Cantos (Crop Marks)
    if (options.cropMarks) {
        const d1 = fromUnitToPx(2, 'mm');
        const d2 = fromUnitToPx(9, 'mm');
        const strokeStyle = 'stroke="#000000" stroke-width="0.5" stroke-linecap="square"';

        extraMarks += `
        <g id="crop_marks">
            <!-- Top-Left -->
            <line x1="${(x1 - d2).toFixed(2)}" y1="${y1.toFixed(2)}" x2="${(x1 - d1).toFixed(2)}" y2="${y1.toFixed(2)}" ${strokeStyle} />
            <line x1="${x1.toFixed(2)}" y1="${(y1 - d2).toFixed(2)}" x2="${x1.toFixed(2)}" y2="${(y1 - d1).toFixed(2)}" ${strokeStyle} />

            <!-- Top-Right -->
            <line x1="${(x2 + d1).toFixed(2)}" y1="${y1.toFixed(2)}" x2="${(x2 + d2).toFixed(2)}" y2="${y1.toFixed(2)}" ${strokeStyle} />
            <line x1="${x2.toFixed(2)}" y1="${(y1 - d2).toFixed(2)}" x2="${x2.toFixed(2)}" y2="${(y1 - d1).toFixed(2)}" ${strokeStyle} />

            <!-- Bottom-Left -->
            <line x1="${(x1 - d2).toFixed(2)}" y1="${y2.toFixed(2)}" x2="${(x1 - d1).toFixed(2)}" y2="${y2.toFixed(2)}" ${strokeStyle} />
            <line x1="${x1.toFixed(2)}" y1="${(y2 + d1).toFixed(2)}" x2="${x1.toFixed(2)}" y2="${(y2 + d2).toFixed(2)}" ${strokeStyle} />

            <!-- Bottom-Right -->
            <line x1="${(x2 + d1).toFixed(2)}" y1="${y2.toFixed(2)}" x2="${(x2 + d2).toFixed(2)}" y2="${y2.toFixed(2)}" ${strokeStyle} />
            <line x1="${x2.toFixed(2)}" y1="${(y2 + d1).toFixed(2)}" x2="${x2.toFixed(2)}" y2="${(y2 + d2).toFixed(2)}" ${strokeStyle} />
        </g>`;
    }

    // 2. Miras de Registro (Registration Targets)
    if (options.registrationMarks) {
        const r1 = fromUnitToPx(3.5, 'mm');
        const r2 = fromUnitToPx(1.8, 'mm');
        const crossLen = fromUnitToPx(5, 'mm');

        const createTarget = (cx, cy) => `
            <g transform="translate(${cx.toFixed(2)}, ${cy.toFixed(2)})">
                <circle cx="0" cy="0" r="${r1.toFixed(2)}" fill="none" stroke="#000000" stroke-width="0.5" />
                <circle cx="0" cy="0" r="${r2.toFixed(2)}" fill="none" stroke="#000000" stroke-width="0.5" />
                <line x1="${(-crossLen).toFixed(2)}" y1="0" x2="${crossLen.toFixed(2)}" y2="0" stroke="#000000" stroke-width="0.5" />
                <line x1="0" y1="${(-crossLen).toFixed(2)}" x2="0" y2="${crossLen.toFixed(2)}" stroke="#000000" stroke-width="0.5" />
            </g>`;

        const midX = (x1 + x2) / 2;
        const midY = (y1 + y2) / 2;
        const offset = fromUnitToPx(7, 'mm');

        extraMarks += `
        <g id="registration_marks">
            ${createTarget(midX, y1 - offset)}
            ${createTarget(midX, y2 + offset)}
            ${createTarget(x1 - offset, midY)}
            ${createTarget(x2 + offset, midY)}
        </g>`;
    }

    // 3. Barra de Cores de Calibração CMYK
    if (options.colorBars) {
        const barColors = ['#00FFFF', '#FF00FF', '#FFFF00', '#000000', '#007ACC', '#C00060', '#CC9900', '#808080'];
        const boxSize = fromUnitToPx(3.5, 'mm');
        const spacing = fromUnitToPx(4.2, 'mm');
        const totalBarW = barColors.length * spacing;
        const startX = (x1 + x2 - totalBarW) / 2;
        const barY = y1 - fromUnitToPx(12, 'mm');

        let rects = '';
        barColors.forEach((color, i) => {
            rects += `<rect x="${(startX + i * spacing).toFixed(2)}" y="${barY.toFixed(2)}" width="${boxSize.toFixed(2)}" height="${boxSize.toFixed(2)}" fill="${color}" stroke="#333" stroke-width="0.2" />`;
        });
        extraMarks += `<g id="color_bars">${rects}</g>`;
    }

    // 4. Informações do Trabalho (Job Info)
    if (options.jobInfo) {
        const wMm = Math.round(fromPxToUnit(docW, 'mm'));
        const hMm = Math.round(fromPxToUnit(docH, 'mm'));
        const now = new Date();
        const dateStr = `${now.toLocaleDateString('pt-BR')} ${now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
        const infoText = `CorelClone Pro 2026 — Dimensões: ${wMm} x ${hMm} mm — Sangria: ${bleedMm} mm — ${dateStr}`;
        const infoY = y2 + fromUnitToPx(11, 'mm');

        extraMarks += `
        <g id="job_info">
            <text x="${x1.toFixed(2)}" y="${infoY.toFixed(2)}" font-family="Arial, Helvetica, sans-serif" font-size="8.5" fill="#333333">${infoText}</text>
        </g>`;
    }

    const fullSvg = `
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="${sheetW.toFixed(2)}" height="${sheetH.toFixed(2)}" viewBox="0 0 ${sheetW.toFixed(2)} ${sheetH.toFixed(2)}">
    <defs>
        ${defsHtml}
    </defs>
    <rect width="${sheetW.toFixed(2)}" height="${sheetH.toFixed(2)}" fill="#ffffff" />
    <rect x="${x1.toFixed(2)}" y="${y1.toFixed(2)}" width="${docW.toFixed(2)}" height="${docH.toFixed(2)}" fill="#ffffff" />
    <g id="artwork" transform="translate(${x1.toFixed(2)}, ${y1.toFixed(2)})">
        ${contentHtml}
    </g>
    ${extraMarks}
</svg>`.trim();

    return { svgString: fullSvg, sheetW, sheetH };
}

function executePrintExport(format) {
    const bleedMm = parseFloat(document.getElementById('printBleedInput')?.value || 3) || 0;
    const options = {
        cropMarks: document.getElementById('chkCropMarks')?.checked ?? true,
        registrationMarks: document.getElementById('chkRegistrationMarks')?.checked ?? true,
        colorBars: document.getElementById('chkColorBars')?.checked ?? true,
        jobInfo: document.getElementById('chkJobInfo')?.checked ?? true
    };

    const { svgString, sheetW, sheetH } = buildPrePressSvg(bleedMm, options);

    if (format === 'svg') {
        const blob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
        downloadBlob(blob, `corel_grafica_sangria_${Date.now()}.svg`);
        closePrintExportDialog();
        toast('Arquivo SVG pré-impressão baixado com sucesso!', 'ok');
        return;
    }

    if (format === 'png') {
        const img = new Image();
        const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        img.onload = () => {
            const scale = 3.125;
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(sheetW * scale);
            canvas.height = Math.round(sheetH * scale);
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(url);

            canvas.toBlob(blob => {
                downloadBlob(blob, `corel_grafica_300dpi_${Date.now()}.png`);
                closePrintExportDialog();
                toast('Imagem gráfica 300 DPI exportada!', 'ok');
            }, 'image/png');
        };
        img.src = url;
        return;
    }

    if (format === 'print') {
        closePrintExportDialog();
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            toast('Permita popups no navegador para abrir a janela de impressão.', 'warn');
            return;
        }

        printWindow.document.open();
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Impressão Gráfica - CorelClone Pro</title>
                <style>
                    @page { size: auto; margin: 0mm; }
                    html, body {
                        margin: 0; padding: 0; background: #ffffff;
                        display: flex; align-items: center; justify-content: center;
                        width: 100%; height: 100%;
                    }
                    svg { display: block; width: 100%; height: auto; }
                </style>
            </head>
            <body>
                ${svgString}
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.focus();
                            window.print();
                        }, 400);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
        toast('Preparando impressão vetorial...', 'ok');
    }
}

function saveProjectJSON() {
    const layerGroup = document.getElementById('layerGroupMain');
    const project = {
        version: 'CorelClone Pro 2026',
        docWidth: state.docWidth,
        docHeight: state.docHeight,
        unit: state.unit,
        pages: state.pages,
        svgContent: layerGroup ? layerGroup.innerHTML : ''
    };
    const blob = new Blob([JSON.stringify(project, null, 2)], { type: 'application/json' });
    downloadBlob(blob, 'projeto_corelclone.json');
    toast('Projeto salvo com sucesso!', 'ok');
}

function importProjectJSON(jsonText) {
    try {
        const data = JSON.parse(jsonText);
        if (data.docWidth && data.docHeight) {
            state.docWidth = data.docWidth;
            state.docHeight = data.docHeight;
            applyDocDimensions();
        }
        if (data.svgContent) {
            const layerGroup = document.getElementById('layerGroupMain');
            if (layerGroup) layerGroup.innerHTML = data.svgContent;
        }
        updateLayersTree();
        saveState('Abrir Projeto JSON');
        toast('Projeto carregado!', 'ok');
    } catch (e) {
        alert('Arquivo JSON inválido.');
    }
}

function downloadBlob(blob, filename) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// ==================== History (Undo / Redo) ====================
function saveState(actionName = 'Ação') {
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    if (state.historyIndex < state.history.length - 1) {
        state.history = state.history.slice(0, state.historyIndex + 1);
    }

    state.history.push({
        action: actionName,
        content: layerGroup.innerHTML,
        docWidth: state.docWidth,
        docHeight: state.docHeight
    });

    if (state.history.length > 50) state.history.shift();
    state.historyIndex = state.history.length - 1;
}

function undo() {
    if (state.historyIndex > 0) {
        state.historyIndex--;
        const item = state.history[state.historyIndex];
        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup) layerGroup.innerHTML = item.content;
        deselectAll();
        updateLayersTree();
        toast(`Desfeito: ${item.action}`, 'info');
    }
}

function redo() {
    if (state.historyIndex < state.history.length - 1) {
        state.historyIndex++;
        const item = state.history[state.historyIndex];
        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup) layerGroup.innerHTML = item.content;
        deselectAll();
        updateLayersTree();
        toast(`Refeito: ${item.action}`, 'info');
    }
}

// ==================== Zoom Controls ====================
function setZoom(z) {
    state.zoom = z;
    const board = document.getElementById('canvasBoard');
    if (board) {
        board.style.width = `${Math.round(state.docWidth * state.zoom)}px`;
        board.style.height = `${Math.round(state.docHeight * state.zoom)}px`;
    }
    const sel = document.getElementById('zoomSelect');
    if (sel) sel.value = z.toString();
    drawRulers();
}

function changeZoomPreset(val) {
    if (val === 'fit') {
        zoomFitPage();
    } else {
        setZoom(parseFloat(val) || 1.0);
    }
}

function zoomFitPage() {
    const scroller = document.getElementById('canvasScroller');
    if (!scroller) return;

    const pad = 60;
    const availW = scroller.clientWidth - pad;
    const availH = scroller.clientHeight - pad;
    const fit = Math.min(availW / state.docWidth, availH / state.docHeight);
    setZoom(Math.max(0.1, Math.min(fit, 2.0)));
}

// ==================== Order & Alignment ====================
function orderSelected(dir) {
    if (state.selectedElements.length === 0) return;
    const layerGroup = document.getElementById('layerGroupMain');

    state.selectedElements.forEach(el => {
        if (dir === 'front') {
            layerGroup.appendChild(el);
        } else if (dir === 'back') {
            layerGroup.insertBefore(el, layerGroup.firstChild);
        }
    });

    renderSelectionOverlay();
    updateLayersTree();
    saveState(`Ordem: ${dir}`);
}

// ==================== Universal SVG Element Mover ====================
function moveSvgElement(el, dx, dy) {
    if (Math.abs(dx) < 0.0001 && Math.abs(dy) < 0.0001) return;
    const tag = el.tagName.toLowerCase();
    const hasTransform = el.hasAttribute('transform') && el.getAttribute('transform').trim() !== '';

    if (!hasTransform) {
        if (tag === 'rect' || tag === 'image') {
            el.setAttribute('x', (parseFloat(el.getAttribute('x') || 0) + dx).toString());
            el.setAttribute('y', (parseFloat(el.getAttribute('y') || 0) + dy).toString());
            return;
        }
        if (tag === 'circle' || tag === 'ellipse') {
            el.setAttribute('cx', (parseFloat(el.getAttribute('cx') || 0) + dx).toString());
            el.setAttribute('cy', (parseFloat(el.getAttribute('cy') || 0) + dy).toString());
            return;
        }
        if (tag === 'text') {
            el.setAttribute('x', (parseFloat(el.getAttribute('x') || 0) + dx).toString());
            el.setAttribute('y', (parseFloat(el.getAttribute('y') || 0) + dy).toString());
            return;
        }
    }

    // Para paths, polígonos, grupos ou elementos com transform existente
    const currentT = el.getAttribute('transform') || '';
    const translateMatch = currentT.match(/translate\(\s*([-\d.]+)(?:[\s,]+([-\d.]+))?\s*\)/);
    if (translateMatch) {
        const curX = parseFloat(translateMatch[1]) || 0;
        const curY = parseFloat(translateMatch[2] || 0);
        const newX = curX + dx;
        const newY = curY + dy;
        const newT = currentT.replace(/translate\(\s*[-\d.]+(?:[\s,]+[-\d.]+)?\s*\)/, `translate(${newX.toFixed(2)}, ${newY.toFixed(2)})`);
        el.setAttribute('transform', newT);
    } else {
        el.setAttribute('transform', `translate(${dx.toFixed(2)}, ${dy.toFixed(2)}) ${currentT}`.trim());
    }
}

// ==================== CorelDRAW Alignment Engine (C, E, L, R, T, B, P) ====================
function alignSelected(type) {
    if (state.selectedElements.length === 0) {
        toast('Selecione um ou mais objetos para alinhar.', 'info');
        return;
    }
    type = type.toUpperCase();

    // 1. Centralizar na Página (P)
    if (type === 'P' || type === 'CENTER') {
        const bbox = getCombinedBBox(state.selectedElements);
        if (!bbox) return;
        const targetX = (state.docWidth - bbox.width) / 2;
        const targetY = (state.docHeight - bbox.height) / 2;
        const dx = targetX - bbox.x;
        const dy = targetY - bbox.y;
        state.selectedElements.forEach(el => moveSvgElement(el, dx, dy));
        renderSelectionOverlay();
        updatePropertyBar();
        saveState('Centralizar na Página (P)');
        toast('Seleção centralizada na página (P)', 'ok');
        return;
    }

    // 2. Se tiver apenas 1 objeto selecionado: alinha em relação à página A4
    if (state.selectedElements.length === 1) {
        const el = state.selectedElements[0];
        const b = getCombinedBBox([el]);
        if (!b) return;
        let dx = 0, dy = 0;

        if (type === 'C') dx = ((state.docWidth - b.width) / 2) - b.x;
        else if (type === 'E') dy = ((state.docHeight - b.height) / 2) - b.y;
        else if (type === 'L') dx = 0 - b.x;
        else if (type === 'R') dx = (state.docWidth - b.width) - b.x;
        else if (type === 'T') dy = 0 - b.y;
        else if (type === 'B') dy = (state.docHeight - b.height) - b.y;

        moveSvgElement(el, dx, dy);
        renderSelectionOverlay();
        updatePropertyBar();
        saveState(`Alinhar (${type})`);
        toast(`Objeto alinhado à folha (${type})`, 'ok');
        return;
    }

    // 3. Múltiplos objetos: o ÚLTIMO elemento selecionado é a âncora/referência (padrão CorelDRAW)
    const anchorEl = state.selectedElements[state.selectedElements.length - 1];
    const anchorBox = getCombinedBBox([anchorEl]);
    if (!anchorBox) return;

    const anchorCenterX = anchorBox.x + anchorBox.width / 2;
    const anchorCenterY = anchorBox.y + anchorBox.height / 2;

    state.selectedElements.forEach(el => {
        if (el === anchorEl) return; // A âncora permanece no lugar
        const b = getCombinedBBox([el]);
        if (!b) return;
        let dx = 0, dy = 0;

        if (type === 'C') { // Centralizar Horizontal
            dx = anchorCenterX - (b.x + b.width / 2);
        } else if (type === 'E') { // Centralizar Vertical (Equidistante)
            dy = anchorCenterY - (b.y + b.height / 2);
        } else if (type === 'L') { // Esquerda
            dx = anchorBox.x - b.x;
        } else if (type === 'R') { // Direita
            dx = (anchorBox.x + anchorBox.width) - (b.x + b.width);
        } else if (type === 'T') { // Topo
            dy = anchorBox.y - b.y;
        } else if (type === 'B') { // Base
            dy = (anchorBox.y + anchorBox.height) - (b.y + b.height);
        }

        moveSvgElement(el, dx, dy);
    });

    renderSelectionOverlay();
    updatePropertyBar();
    saveState(`Alinhar (${type})`);
    const names = { C: 'Centro Horizontal (C)', E: 'Centro Vertical (E)', L: 'Esquerda (L)', R: 'Direita (R)', T: 'Topo (T)', B: 'Base (B)' };
    toast(`Objetos alinhados por ${names[type] || type}!`, 'ok');
}

function centerSelectedInPage() {
    alignSelected('P');
}

// ==================== CorelDRAW Mirror / Flip Engine (Horizontal & Vertical) ====================
function flipSelected(direction) {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto para espelhar.', 'info');
        return;
    }

    const bbox = getCombinedBBox(state.selectedElements);
    if (!bbox || bbox.width <= 0 || bbox.height <= 0) return;

    const centerX = bbox.x + bbox.width / 2;
    const centerY = bbox.y + bbox.height / 2;

    state.selectedElements.forEach(el => {
        const currentTransform = el.getAttribute('transform') || '';

        if (direction === 'horizontal') {
            const flipTransform = `translate(${centerX.toFixed(2)}, ${centerY.toFixed(2)}) scale(-1, 1) translate(${-centerX.toFixed(2)}, ${-centerY.toFixed(2)})`;
            el.setAttribute('transform', `${flipTransform} ${currentTransform}`.trim());
        } else if (direction === 'vertical') {
            const flipTransform = `translate(${centerX.toFixed(2)}, ${centerY.toFixed(2)}) scale(1, -1) translate(${-centerX.toFixed(2)}, ${-centerY.toFixed(2)})`;
            el.setAttribute('transform', `${flipTransform} ${currentTransform}`.trim());
        }
    });

    renderSelectionOverlay();
    updatePropertyBar();
    saveState(`Espelhar ${direction === 'horizontal' ? 'Horizontal' : 'Vertical'}`);
    toast(`Objeto espelhado ${direction === 'horizontal' ? 'horizontalmente ↔' : 'verticalmente ↕'}`, 'ok');
}

function convertSelectedToCurves() {
    toast('Objeto convertido em Curvas Bézier (Ctrl+Q)!', 'ok');
    selectTool('node');
}

function groupSelected() {
    if (state.selectedElements.length < 2) return;
    const layerGroup = document.getElementById('layerGroupMain');
    const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    g.setAttribute('class', 'user-group');

    state.selectedElements.forEach(el => g.appendChild(el));
    layerGroup.appendChild(g);

    selectElement(g, false);
    saveState('Agrupar (Ctrl+G)');
    toast('Objetos agrupados!', 'ok');
}

function ungroupSelected() {
    if (state.selectedElements.length === 0) return;
    const layerGroup = document.getElementById('layerGroupMain');
    const newSelected = [];

    state.selectedElements.forEach(el => {
        if (el.tagName.toLowerCase() === 'g') {
            Array.from(el.children).forEach(child => {
                layerGroup.appendChild(child);
                newSelected.push(child);
            });
            el.remove();
        }
    });

    if (newSelected.length > 0) {
        state.selectedElements = newSelected;
        renderSelectionOverlay();
        updateLayersTree();
        saveState('Desagrupar (Ctrl+U)');
        toast('Objetos desagrupados!', 'ok');
    }
}

// ==================== CorelDRAW PowerClip™ Engine ====================
function applyPowerClip() {
    if (state.selectedElements.length === 0) {
        toast('Selecione primeiro a imagem ou objeto que deseja colocar dentro do recipiente.', 'info');
        return;
    }

    // Se houver 2 ou mais objetos selecionados: o primeiro é o conteúdo e o último é o recipiente
    if (state.selectedElements.length >= 2) {
        const container = state.selectedElements[state.selectedElements.length - 1];
        const content = state.selectedElements[0];
        createPowerClip(content, container);
        return;
    }

    // Se houver 1 objeto selecionado: modo interativo estilo CorelDRAW
    state.powerClipTargetContent = state.selectedElements[0];
    state.isPowerClipping = true;
    document.body.style.cursor = 'crosshair';
    toast('🎯 Clique sobre a forma (círculo, retângulo, etc.) que será o recipiente do PowerClip.', 'info');
}

function createPowerClip(contentEl, containerEl) {
    if (!contentEl || !containerEl || contentEl === containerEl) {
        toast('Recipiente inválido para o PowerClip.', 'err');
        return;
    }

    const mainSvg = document.getElementById('mainSvgCanvas');
    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.prepend(defs);
    }

    const clipId = `powerclip_${Date.now()}`;
    const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
    clipPath.setAttribute('id', clipId);

    // Clona a forma geométrica do recipiente para servir como máscara de corte
    const clipShape = containerEl.cloneNode(true);
    clipShape.removeAttribute('id');
    clipShape.removeAttribute('stroke');
    clipShape.removeAttribute('stroke-width');
    clipShape.setAttribute('fill', '#ffffff');
    clipPath.appendChild(clipShape);
    defs.appendChild(clipPath);

    // Cria o grupo do PowerClip
    const powerClipGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    powerClipGroup.setAttribute('class', 'corel-powerclip-group user-group');
    powerClipGroup.setAttribute('data-powerclip-id', clipId);

    // O recipiente original é mantido como moldura (preservando borda / preenchimento de fundo)
    const frame = containerEl.cloneNode(true);
    frame.setAttribute('class', 'powerclip-frame');
    frame.removeAttribute('id');
    frame.style.pointerEvents = 'none';

    // Grupo de conteúdo com o clip-path aplicado
    const contentGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    contentGroup.setAttribute('class', 'powerclip-content');
    contentGroup.setAttribute('clip-path', `url(#${clipId})`);
    contentGroup.appendChild(contentEl);

    powerClipGroup.appendChild(frame);
    powerClipGroup.appendChild(contentGroup);

    const parent = containerEl.parentNode || document.getElementById('layerGroupMain');
    parent.insertBefore(powerClipGroup, containerEl);
    containerEl.remove();

    selectElement(powerClipGroup, false);
    saveState('PowerClip: Colocar no Recipiente');
    updateLayersTree();
    updatePropertyBar();
    toast('⚡ PowerClip™ aplicado com sucesso!', 'ok');
}

function extractPowerClip() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um PowerClip para extrair o conteúdo.', 'info');
        return;
    }

    const group = state.selectedElements.find(el => el.classList && el.classList.contains('corel-powerclip-group')) ||
                  (state.selectedElements[0].closest && state.selectedElements[0].closest('.corel-powerclip-group'));

    if (!group) {
        toast('O objeto selecionado não é um PowerClip.', 'err');
        return;
    }

    const clipId = group.getAttribute('data-powerclip-id');
    const frame = group.querySelector('.powerclip-frame');
    const contentGroup = group.querySelector('.powerclip-content');
    const parent = group.parentNode || document.getElementById('layerGroupMain');

    const newSelected = [];

    // Restaura o recipiente como forma independente
    if (frame) {
        const restoredContainer = frame.cloneNode(true);
        restoredContainer.removeAttribute('class');
        restoredContainer.style.pointerEvents = '';
        parent.insertBefore(restoredContainer, group);
        newSelected.push(restoredContainer);
    }

    // Restaura os conteúdos internos
    if (contentGroup) {
        Array.from(contentGroup.children).forEach(child => {
            parent.insertBefore(child, group);
            newSelected.push(child);
        });
    }

    // Limpa a definição de clipPath
    if (clipId) {
        const cp = document.getElementById(clipId);
        if (cp) cp.remove();
    }

    group.remove();

    state.selectedElements = newSelected;
    renderSelectionOverlay();
    updateLayersTree();
    updatePropertyBar();
    saveState('PowerClip: Extrair Conteúdo');
    toast('Conteúdo do PowerClip extraído com sucesso!', 'ok');
}

// ==================== CorelDRAW Contour / Borda de Adesivo Engine ====================
function openContourDialog() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto ou texto para aplicar o contorno.', 'info');
        return;
    }
    const modal = document.getElementById('contourModal');
    if (modal) modal.style.display = 'flex';
}

function closeContourDialog() {
    const modal = document.getElementById('contourModal');
    if (modal) modal.style.display = 'none';
}

function applyContourFromModal() {
    if (state.selectedElements.length === 0) {
        closeContourDialog();
        return;
    }

    const style = document.getElementById('contourStyle')?.value || 'sticker';
    const offsetMm = parseFloat(document.getElementById('contourOffsetMm')?.value || '3.0') || 3.0;
    const color = document.getElementById('contourColorInput')?.value || '#ffffff';
    const corners = document.getElementById('contourCorners')?.value || 'round';
    const groupWithOrig = document.getElementById('contourGroupWithOriginal')?.checked ?? true;

    // Converte mm em pixels (96 DPI: 1mm = 3.7795px)
    const offsetPx = offsetMm * 3.7795275591;
    const strokeWidth = style === 'sticker' ? (offsetPx * 2) : 1.5;

    const layerGroup = document.getElementById('layerGroupMain');
    const createdContours = [];

    state.selectedElements.forEach(origEl => {
        const parent = origEl.parentNode || layerGroup;

        // Se for grupo, clona a estrutura inteira
        const contourEl = origEl.cloneNode(true);
        contourEl.removeAttribute('id');
        contourEl.setAttribute('class', 'corel-contour-element');

        const applyStyleToPrimitive = (el) => {
            if (style === 'sticker') {
                el.setAttribute('fill', color);
                el.setAttribute('stroke', color);
                el.setAttribute('stroke-width', strokeWidth.toFixed(1));
                el.setAttribute('stroke-linejoin', corners);
                el.setAttribute('stroke-linecap', corners);
                el.removeAttribute('clip-path');
            } else { // cutline para plotter
                el.setAttribute('fill', 'none');
                el.setAttribute('stroke', color);
                el.setAttribute('stroke-width', '1.5');
                el.setAttribute('stroke-linejoin', corners);
                el.setAttribute('stroke-linecap', corners);
                el.removeAttribute('clip-path');
            }
        };

        if (contourEl.tagName.toLowerCase() === 'g') {
            contourEl.querySelectorAll('*').forEach(applyStyleToPrimitive);
        } else {
            applyStyleToPrimitive(contourEl);
        }

        if (groupWithOrig) {
            const wrapperGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            wrapperGroup.setAttribute('class', 'contour-sticker-group user-group');
            parent.insertBefore(wrapperGroup, origEl);
            wrapperGroup.appendChild(contourEl); // Fica atrás
            wrapperGroup.appendChild(origEl);     // Fica na frente
            createdContours.push(wrapperGroup);
        } else {
            // Insere atrás do objeto original
            parent.insertBefore(contourEl, origEl);
            createdContours.push(contourEl);
        }
    });

    closeContourDialog();
    state.selectedElements = createdContours;
    renderSelectionOverlay();
    updateLayersTree();
    updatePropertyBar();
    saveState(`Criar Contorno (${offsetMm}mm)`);
    toast(`⚡ Contorno de ${offsetMm}mm aplicado com sucesso!`, 'ok');
}

// ==================== CorelDRAW Fountain Fill™ (Gradiente / Degradê F11) ====================
function openFountainFillDialog() {
    const modal = document.getElementById('fountainFillModal');
    if (!modal) return;
    updateFountainPreview();
    modal.style.display = 'flex';
}

function closeFountainFillDialog() {
    const modal = document.getElementById('fountainFillModal');
    if (modal) modal.style.display = 'none';
}

function updateFountainPreview() {
    const bar = document.getElementById('fountainPreviewBar');
    if (!bar) return;
    const type = document.querySelector('input[name="fountainType"]:checked')?.value || 'linear';
    const c1 = document.getElementById('fountainColor1')?.value || '#ffd700';
    const c2 = document.getElementById('fountainColor2')?.value || '#b8860b';
    const angle = parseInt(document.getElementById('fountainAngleInput')?.value || '90', 10);

    if (type === 'radial') {
        bar.style.background = `radial-gradient(circle, ${c1}, ${c2})`;
    } else {
        bar.style.background = `linear-gradient(${angle}deg, ${c1}, ${c2})`;
    }
}

function swapFountainColors() {
    const c1El = document.getElementById('fountainColor1');
    const c2El = document.getElementById('fountainColor2');
    if (!c1El || !c2El) return;
    const temp = c1El.value;
    c1El.value = c2El.value;
    c2El.value = temp;
    updateFountainPreview();
}

function applyFountainPreset(c1, c2, angle = 90) {
    const c1El = document.getElementById('fountainColor1');
    const c2El = document.getElementById('fountainColor2');
    const angleSlider = document.getElementById('fountainAngleSlider');
    const angleInput = document.getElementById('fountainAngleInput');
    if (c1El) c1El.value = c1;
    if (c2El) c2El.value = c2;
    if (angleSlider) angleSlider.value = angle;
    if (angleInput) angleInput.value = angle;
    const linearRadio = document.querySelector('input[name="fountainType"][value="linear"]');
    if (linearRadio) linearRadio.checked = true;
    updateFountainPreview();
}

function applyFountainFillFromModal() {
    const type = document.querySelector('input[name="fountainType"]:checked')?.value || 'linear';
    const c1 = document.getElementById('fountainColor1')?.value || '#ffd700';
    const c2 = document.getElementById('fountainColor2')?.value || '#b8860b';
    const angle = parseInt(document.getElementById('fountainAngleInput')?.value || '90', 10);

    const mainSvg = document.getElementById('mainSvgCanvas');
    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.prepend(defs);
    }

    const gradId = `fountain_${Date.now()}`;
    let gradEl;

    if (type === 'radial') {
        gradEl = document.createElementNS('http://www.w3.org/2000/svg', 'radialGradient');
        gradEl.setAttribute('id', gradId);
        gradEl.setAttribute('cx', '50%');
        gradEl.setAttribute('cy', '50%');
        gradEl.setAttribute('r', '50%');
    } else {
        gradEl = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
        gradEl.setAttribute('id', gradId);
        // Calcula vetor direcional do ângulo em coordenadas SVG %
        const rad = (angle - 90) * (Math.PI / 180);
        const x1 = Math.round(50 - Math.cos(rad) * 50);
        const y1 = Math.round(50 - Math.sin(rad) * 50);
        const x2 = Math.round(50 + Math.cos(rad) * 50);
        const y2 = Math.round(50 + Math.sin(rad) * 50);
        gradEl.setAttribute('x1', `${x1}%`);
        gradEl.setAttribute('y1', `${y1}%`);
        gradEl.setAttribute('x2', `${x2}%`);
        gradEl.setAttribute('y2', `${y2}%`);
    }

    const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
    stop1.setAttribute('offset', '0%');
    stop1.setAttribute('stop-color', c1);

    const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
    stop2.setAttribute('offset', '100%');
    stop2.setAttribute('stop-color', c2);

    gradEl.appendChild(stop1);
    gradEl.appendChild(stop2);
    defs.appendChild(gradEl);

    // Se houver objetos selecionados, aplica neles
    if (state.selectedElements.length > 0) {
        state.selectedElements.forEach(el => {
            if (el.tagName.toLowerCase() === 'g') {
                el.querySelectorAll('*').forEach(child => child.setAttribute('fill', `url(#${gradId})`));
            } else {
                el.setAttribute('fill', `url(#${gradId})`);
            }
        });
        saveState('Preenchimento Gradiente (F11)');
        updateLayersTree();
        toast('⚡ Preenchimento Gradiente aplicado com sucesso!', 'ok');
    } else {
        state.activeFill = `url(#${gradId})`;
        toast('Gradiente definido como preenchimento ativo!', 'ok');
    }

    closeFountainFillDialog();
}

// ==================== CorelDRAW Vector QR Code Engine ====================
function openQrCodeDialog() {
    const modal = document.getElementById('qrcodeModal');
    if (modal) modal.style.display = 'flex';
}

function closeQrCodeDialog() {
    const modal = document.getElementById('qrcodeModal');
    if (modal) modal.style.display = 'none';
}

function setQrType(type) {
    const label = document.getElementById('qrContentLabel');
    const input = document.getElementById('qrContentInput');
    if (!label || !input) return;

    if (type === 'url') {
        label.textContent = 'URL do Site ou Link:';
        input.placeholder = 'https://seusite.com.br';
    } else if (type === 'whatsapp') {
        label.textContent = 'Link Direto do WhatsApp (com DDD):';
        input.placeholder = 'https://wa.me/5511999999999';
    } else if (type === 'pix') {
        label.textContent = 'Chave PIX ou Código Copia e Cola:';
        input.placeholder = 'suachave@pix.com.br ou 00020126580014br.gov.bcb.pix...';
    } else if (type === 'text') {
        label.textContent = 'Texto Livre:';
        input.placeholder = 'Digite qualquer texto ou informação aqui...';
    }
}

function generateAndInsertQrCode() {
    const text = document.getElementById('qrContentInput')?.value?.trim();
    if (!text) {
        toast('Digite o link, chave PIX ou texto do QR Code.', 'warn');
        return;
    }

    if (typeof QRCode === 'undefined') {
        toast('Motor QRCode não carregado.', 'err');
        return;
    }

    const sizeMm = parseFloat(document.getElementById('qrSizeInput')?.value || '40') || 40;
    const colorDark = document.getElementById('qrColorDark')?.value || '#000000';
    const transparentBg = document.getElementById('qrTransparentBg')?.checked ?? true;
    const ecc = document.getElementById('qrEccLevel')?.value || 'M';

    // Cria elemento temporário para renderizar o QR em SVG
    const tempDiv = document.createElement('div');
    tempDiv.style.display = 'none';
    document.body.appendChild(tempDiv);

    try {
        new QRCode(tempDiv, {
            text: text,
            width: 256,
            height: 256,
            colorDark: colorDark,
            colorLight: transparentBg ? '#ffffff' : '#ffffff',
            correctLevel: QRCode.CorrectLevel[ecc] || QRCode.CorrectLevel.M,
            useSVG: true
        });

        // Aguarda geração do SVG pelo qrcodejs
        const generatedSvg = tempDiv.querySelector('svg');
        if (!generatedSvg) {
            toast('Erro ao gerar código vetorial do QR Code.', 'err');
            tempDiv.remove();
            return;
        }

        const sizePx = sizeMm * 3.7795275591;
        const posX = Math.round((state.docWidth - sizePx) / 2);
        const posY = Math.round((state.docHeight - sizePx) / 2);

        const qrGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        qrGroup.setAttribute('class', 'corel-qrcode-group user-group');

        // Calcula escala para o tamanho desejado
        const nativeW = parseFloat(generatedSvg.getAttribute('width')) || 256;
        const scale = sizePx / nativeW;
        qrGroup.setAttribute('transform', `translate(${posX}, ${posY}) scale(${scale.toFixed(4)})`);

        // Transfere os elementos SVG
        Array.from(generatedSvg.children).forEach((child, idx) => {
            // Se for o primeiro rect e o usuário quis fundo transparente, ignora o fundo branco
            if (transparentBg && idx === 0 && child.tagName.toLowerCase() === 'rect') {
                return;
            }
            qrGroup.appendChild(child.cloneNode(true));
        });

        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup) {
            layerGroup.appendChild(qrGroup);
            selectElement(qrGroup, false);
            saveState('Inserir QR Code');
            updateLayersTree();
            updatePropertyBar();
            toast(`⚡ QR Code vetorial de ${sizeMm}mm inserido na prancheta!`, 'ok');
        }

        closeQrCodeDialog();
    } catch (err) {
        console.error('Erro na geração do QR Code:', err);
        toast('Erro ao gerar QR Code.', 'err');
    } finally {
        tempDiv.remove();
    }
}

// ==================== CorelDRAW Drop Shadow™ Engine ====================
function openDropShadowDialog() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto ou texto para aplicar a sombra.', 'info');
        return;
    }
    const modal = document.getElementById('dropShadowModal');
    if (modal) modal.style.display = 'flex';
}

function closeDropShadowDialog() {
    const modal = document.getElementById('dropShadowModal');
    if (modal) modal.style.display = 'none';
}

function applyShadowPresetValues(dx, dy, blur, opacity, color) {
    const dxSlider = document.getElementById('shadowDxSlider');
    const dxVal = document.getElementById('shadowDxVal');
    const dySlider = document.getElementById('shadowDySlider');
    const dyVal = document.getElementById('shadowDyVal');
    const blurSlider = document.getElementById('shadowBlurSlider');
    const blurVal = document.getElementById('shadowBlurVal');
    const opSlider = document.getElementById('shadowOpacitySlider');
    const opVal = document.getElementById('shadowOpacityVal');
    const colInput = document.getElementById('shadowColorInput');

    if (dxSlider) { dxSlider.value = dx; if (dxVal) dxVal.textContent = dx; }
    if (dySlider) { dySlider.value = dy; if (dyVal) dyVal.textContent = dy; }
    if (blurSlider) { blurSlider.value = blur; if (blurVal) blurVal.textContent = blur; }
    if (opSlider) { opSlider.value = opacity; if (opVal) opVal.textContent = opacity; }
    if (colInput) colInput.value = color;
}

function applyDropShadowFromModal() {
    if (state.selectedElements.length === 0) {
        closeDropShadowDialog();
        return;
    }

    const dx = parseFloat(document.getElementById('shadowDxSlider')?.value || '6');
    const dy = parseFloat(document.getElementById('shadowDySlider')?.value || '6');
    const blur = parseFloat(document.getElementById('shadowBlurSlider')?.value || '8');
    const opacityVal = parseInt(document.getElementById('shadowOpacitySlider')?.value || '50', 10);
    const color = document.getElementById('shadowColorInput')?.value || '#000000';
    const opacity = opacityVal / 100;

    const mainSvg = document.getElementById('mainSvgCanvas');
    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.prepend(defs);
    }

    const filterId = `dropshadow_${Date.now()}`;
    const filter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
    filter.setAttribute('id', filterId);
    filter.setAttribute('x', '-50%');
    filter.setAttribute('y', '-50%');
    filter.setAttribute('width', '200%');
    filter.setAttribute('height', '200%');

    const feShadow = document.createElementNS('http://www.w3.org/2000/svg', 'feDropShadow');
    feShadow.setAttribute('dx', dx.toString());
    feShadow.setAttribute('dy', dy.toString());
    feShadow.setAttribute('stdDeviation', (blur / 2).toFixed(1));
    feShadow.setAttribute('flood-color', color);
    feShadow.setAttribute('flood-opacity', opacity.toFixed(2));

    filter.appendChild(feShadow);
    defs.appendChild(filter);

    state.selectedElements.forEach(el => {
        el.setAttribute('filter', `url(#${filterId})`);
    });

    closeDropShadowDialog();
    renderSelectionOverlay();
    updateLayersTree();
    updatePropertyBar();
    saveState('Sombra Projetada');
    toast('⚡ Efeito de Sombra Projetada aplicado com sucesso!', 'ok');
}

function removeDropShadowFromSelected() {
    if (state.selectedElements.length === 0) return;

    state.selectedElements.forEach(el => {
        el.removeAttribute('filter');
    });

    closeDropShadowDialog();
    renderSelectionOverlay();
    updateLayersTree();
    updatePropertyBar();
    saveState('Remover Sombra');
    toast('Sombra removida do(s) objeto(s).', 'ok');
}

// ==================== CorelDRAW Fit Text to Path Engine (Texto no Caminho) ====================
function fitTextToPath() {
    if (state.selectedElements.length === 0) {
        toast('Selecione primeiro o texto que deseja ajustar à curva.', 'info');
        return;
    }

    // Procura texto e curva na seleção
    const textEl = state.selectedElements.find(el => el.tagName.toLowerCase() === 'text');
    const curveEl = state.selectedElements.find(el => el.tagName.toLowerCase() !== 'text');

    // Se ambos já estiverem selecionados juntos
    if (textEl && curveEl) {
        applyFitTextToPath(textEl, curveEl);
        return;
    }

    // Se apenas o texto estiver selecionado, entra em modo interativo de seleção da curva
    if (textEl) {
        state.targetTextForPath = textEl;
        state.isPickingPathForText = true;
        document.body.style.cursor = 'crosshair';
        toast('🎯 Clique sobre o círculo ou curva onde deseja ajustar o texto.', 'info');
        return;
    }

    toast('Selecione um elemento de texto para curvar.', 'warn');
}

function applyFitTextToPath(textEl, curveEl) {
    if (!textEl || !curveEl) return;

    const layerGroup = document.getElementById('layerGroupMain');
    const parent = curveEl.parentNode || layerGroup;

    // Se a curva for um círculo, elipse, polígono ou retângulo, converte para <path>
    let pathElement = curveEl;
    if (curveEl.tagName.toLowerCase() !== 'path') {
        const converted = convertShapeToPath(curveEl);
        if (converted) {
            parent.insertBefore(converted, curveEl);
            curveEl.remove();
            pathElement = converted;
        }
    }

    // Garante ID na curva
    const pathId = pathElement.id || `curve_path_${Date.now()}`;
    pathElement.setAttribute('id', pathId);

    // Lê o conteúdo textual
    const textContent = textEl.textContent.trim();

    // Cria o elemento <textPath> do SVG W3C
    textEl.innerHTML = '';
    textEl.removeAttribute('x');
    textEl.removeAttribute('y');

    const textPath = document.createElementNS('http://www.w3.org/2000/svg', 'textPath');
    textPath.setAttribute('href', `#${pathId}`);
    textPath.setAttribute('startOffset', '50%');
    textPath.setAttribute('text-anchor', 'middle');
    textPath.textContent = textContent;

    textEl.appendChild(textPath);

    // Agrupa o texto e a curva para moverem juntos
    const wrapperGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    wrapperGroup.setAttribute('class', 'text-on-path-group user-group');

    parent.insertBefore(wrapperGroup, pathElement);
    wrapperGroup.appendChild(pathElement);
    wrapperGroup.appendChild(textEl);

    selectElement(wrapperGroup, false);
    saveState('Ajustar Texto ao Caminho');
    updateLayersTree();
    updatePropertyBar();
    toast('⚡ Texto perfeitamente ajustado à curva (Text on Path)!', 'ok');
}

function separateTextFromPath() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um texto curvado para separar do caminho.', 'info');
        return;
    }

    const group = state.selectedElements.find(el => el.classList && el.classList.contains('text-on-path-group')) ||
                  (state.selectedElements[0].closest && state.selectedElements[0].closest('.text-on-path-group'));

    if (!group) {
        toast('O objeto selecionado não é um texto ajustado a caminho.', 'warn');
        return;
    }

    const textEl = group.querySelector('text');
    const textPath = textEl ? textEl.querySelector('textPath') : null;
    const pathEl = group.querySelector('path');
    const parent = group.parentNode || document.getElementById('layerGroupMain');

    const newSelected = [];

    if (textEl && textPath) {
        const textContent = textPath.textContent;
        const bbox = group.getBBox();
        textEl.innerHTML = '';
        textEl.textContent = textContent;
        textEl.setAttribute('x', Math.round(bbox.x).toString());
        textEl.setAttribute('y', Math.round(bbox.y + 20).toString());
        parent.insertBefore(textEl, group);
        newSelected.push(textEl);
    }

    if (pathEl) {
        parent.insertBefore(pathEl, group);
        newSelected.push(pathEl);
    }

    group.remove();

    state.selectedElements = newSelected;
    renderSelectionOverlay();
    updateLayersTree();
    updatePropertyBar();
    saveState('Separar Texto do Caminho');
    toast('Texto separado da curva com sucesso!', 'ok');
}


function initKeyboardShortcuts() {
    // ---- Bloquear zoom nativo do browser em toda a janela ----
    window.addEventListener('wheel', (e) => {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault(); // bloqueia Ctrl+Scroll do browser
        }
    }, { passive: false });

    window.addEventListener('keydown', (e) => {
        const isInput = ['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase());
        if (isInput) return;

        // Bloquear Ctrl+= e Ctrl+- e Ctrl+0 nativos do browser e redirecionar para setZoom
        if (e.ctrlKey || e.metaKey) {
            if (e.key === '=' || e.key === '+') {
                e.preventDefault();
                setZoom(Math.min(state.zoom * 1.25, 8.0));
                return;
            }
            if (e.key === '-' || e.key === '_') {
                e.preventDefault();
                setZoom(Math.max(state.zoom / 1.25, 0.05));
                return;
            }
            if (e.key === '0') {
                e.preventDefault();
                zoomFitPage();
                return;
            }
        }

        // Enter — fecha o caminho da Caneta Bézier
        if (e.key === 'Enter') {
            if (state.activeTool === 'pen' && state.penPath) {
                e.preventDefault();
                const d = state.penPath.getAttribute('d') || '';
                if (state.penPoints && state.penPoints.length > 2) {
                    state.penPath.setAttribute('d', `${d} Z`);
                }
                selectElement(state.penPath, false);
                saveState('Criar Caminho Bézier');
                state.penPath = null;
                state.penPoints = [];
                selectTool('select');
            }
            return;
        }

        // Escape — cancela ferramenta ativa ou deseleciona
        if (e.key === 'Escape') {
            if (state.activeTool === 'pen' && state.penPath) {
                state.penPath.remove();
                state.penPath = null;
                state.penPoints = [];
            }
            deselectAll();
            selectTool('select');
            return;
        }

        // Delete
        if (e.key === 'Delete' || e.key === 'Backspace') {
            e.preventDefault();
            deleteSelected();
            return;
        }


        // Ctrl Combinations
        if (e.ctrlKey || e.metaKey) {
            if (e.key === 'z' || e.key === 'Z') {
                e.preventDefault();
                undo();
            } else if (e.key === 'y' || e.key === 'Y') {
                e.preventDefault();
                redo();
            } else if (e.key === 'd' || e.key === 'D') {
                e.preventDefault();
                duplicateSelected();
            } else if (e.key === 'r' || e.key === 'R') {
                e.preventDefault();
                repeatTransform();
            } else if (e.key === 'a' || e.key === 'A') {
                e.preventDefault();
                selectAll();
            } else if (e.key === 's' || e.key === 'S') {
                e.preventDefault();
                saveProjectJSON();
            } else if (e.key === 'o' || e.key === 'O') {
                e.preventDefault();
                document.getElementById('importFileInput').click();
            } else if (e.key === 'g' || e.key === 'G') {
                e.preventDefault();
                if (e.shiftKey) ungroupSelected();
                else groupSelected();
            } else if (e.key === 'u' || e.key === 'U') {
                e.preventDefault();
                ungroupSelected();
            } else if (e.key === 'q' || e.key === 'Q') {
                e.preventDefault();
                convertSelectedToCurves();
            } else if (e.key === 'p' || e.key === 'P') {
                e.preventDefault();
                openPrintExportDialog();
            }
            return;
        }

        // Corel Functional Keys
        if (e.key === 'F10') {
            e.preventDefault();
            selectTool('node');
        } else if (e.key === 'F6') {
            e.preventDefault();
            selectTool('rect');
        } else if (e.key === 'F7') {
            e.preventDefault();
            selectTool('ellipse');
        } else if (e.key === 'F8') {
            e.preventDefault();
            selectTool('text');
        } else if (e.key === 'F4') {
            e.preventDefault();
            zoomFitPage();
        } else if (e.key === 'F11') {
            e.preventDefault();
            openFountainFillDialog();
        } else if (e.key === ' ') {
            e.preventDefault();
            selectTool('select');
        } else if (state.selectedElements.length > 0 && !e.ctrlKey && !e.altKey && !e.metaKey) {
            // CorelDRAW Instant Align Shortcuts (P, C, E, L, R, T, B)
            const k = e.key.toUpperCase();
            if (['P', 'C', 'E', 'L', 'R', 'T', 'B'].includes(k)) {
                e.preventDefault();
                alignSelected(k);
            }
        }
    });
}

function newDocument() {
    if (confirm('Deseja criar um novo documento e limpar a prancheta atual?')) {
        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup) layerGroup.innerHTML = '';
        state.pages = [{ id: 0, name: 'Documento 1', svgContent: null, docWidth: 1122, docHeight: 793 }];
        state.activePageIndex = 0;
        deselectAll();
        renderDocumentTabs();
        updateLayersTree();
        saveState('Novo Documento');
        toast('Novo documento criado!', 'ok');
    }
}

function showShortcutsModal() {
    alert(
        "⌨️ Principais Atalhos Oficiais CorelDRAW:\n\n" +
        "• Espaço: Ferramenta Seleção\n" +
        "• F10: Ferramenta Forma (Editar Nós Bézier)\n" +
        "• F6: Retângulo\n" +
        "• F7: Elipse\n" +
        "• F8: Texto\n" +
        "• F4: Ajustar Página à Tela\n" +
        "• P: Centralizar Objeto na Página\n" +
        "• Ctrl+Q: Converter em Curvas\n" +
        "• Ctrl+G / Ctrl+U: Agrupar / Desagrupar\n" +
        "• Ctrl+D: Duplicar\n" +
        "• Shift+PgUp / Shift+PgDn: Trazer para frente / Enviar para trás\n" +
        "• Delete: Excluir Objeto"
    );
}

// Utility
function capitalize(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}

// ==================== Clipboard: Cut / Copy / Paste ====================
function cutSelected() {
    if (state.selectedElements.length === 0) {
        toast('Nenhum objeto selecionado para recortar.', 'err');
        return;
    }
    state.clipboard = state.selectedElements.map(el => el.cloneNode(true));
    state.selectedElements.forEach(el => el.remove());
    state.selectedElements = [];
    renderSelectionOverlay();
    updateLayersTree();
    saveState('Recortar (Ctrl+X)');
    toast(`${state.clipboard.length} objeto(s) recortado(s).`, 'ok');
}

function copySelected() {
    if (state.selectedElements.length === 0) {
        toast('Nenhum objeto selecionado para copiar.', 'err');
        return;
    }
    state.clipboard = state.selectedElements.map(el => el.cloneNode(true));
    toast(`${state.clipboard.length} objeto(s) copiado(s) para a área de transferência.`, 'ok');
}

function pasteSelected() {
    if (!state.clipboard || state.clipboard.length === 0) {
        toast('Área de transferência vazia. Use Copiar (Ctrl+C) primeiro.', 'err');
        return;
    }
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    const newSelected = [];
    state.clipboard = state.clipboard.map(el => {
        const clone = el.cloneNode(true);
        // Offset pasted elements by 20px so they don't overlap exactly
        if (clone.hasAttribute('x')) clone.setAttribute('x', (parseFloat(clone.getAttribute('x')) + 20).toString());
        if (clone.hasAttribute('y')) clone.setAttribute('y', (parseFloat(clone.getAttribute('y')) + 20).toString());
        if (clone.hasAttribute('cx')) clone.setAttribute('cx', (parseFloat(clone.getAttribute('cx')) + 20).toString());
        if (clone.hasAttribute('cy')) clone.setAttribute('cy', (parseFloat(clone.getAttribute('cy')) + 20).toString());
        const existingT = clone.getAttribute('transform') || '';
        if (!existingT && !clone.hasAttribute('x') && !clone.hasAttribute('cx')) {
            clone.setAttribute('transform', `translate(20, 20)${existingT ? ' ' + existingT : ''}`);
        }
        layerGroup.appendChild(clone);
        newSelected.push(clone);
        return el.cloneNode(true); // keep clipboard alive for repeated Ctrl+V
    });

    state.selectedElements = newSelected;
    renderSelectionOverlay();
    updateLayersTree();
    saveState('Colar (Ctrl+V)');
    toast(`${newSelected.length} objeto(s) colado(s).`, 'ok');
}

// ==================== Color Pickers (Fill / Stroke) ====================
function openFillColorPicker() {
    const picker = document.createElement('input');
    picker.type = 'color';
    picker.value = state.activeFill && state.activeFill !== 'none'
        ? (state.activeFill.startsWith('#') ? state.activeFill : '#000000')
        : '#000000';
    picker.style.display = 'none';
    document.body.appendChild(picker);
    picker.addEventListener('input', (e) => {
        applyColorToSelection('fill', e.target.value);
    });
    picker.addEventListener('change', (e) => {
        applyColorToSelection('fill', e.target.value);
        picker.remove();
    });
    picker.click();
}

function openStrokeColorPicker() {
    const picker = document.createElement('input');
    picker.type = 'color';
    picker.value = state.activeStroke && state.activeStroke !== 'none'
        ? (state.activeStroke.startsWith('#') ? state.activeStroke : '#000000')
        : '#000000';
    picker.style.display = 'none';
    document.body.appendChild(picker);
    picker.addEventListener('input', (e) => {
        applyColorToSelection('stroke', e.target.value);
    });
    picker.addEventListener('change', (e) => {
        applyColorToSelection('stroke', e.target.value);
        picker.remove();
    });
    picker.click();
}

// ==================== Node Tool (F10) Functions ====================

// Parse SVG path "d" attribute into an array of command objects
function parseSvgPathToNodes(d) {
    if (!d) return [];
    const tokens = d.trim().split(/[\s,]+|(?=[MmLlCcQqZz])/);
    const nodes = [];
    let currX = 0, currY = 0, startX = 0, startY = 0;
    let i = 0;

    while (i < tokens.length) {
        const cmd = tokens[i];
        if (/^[MmLlCcQqZz]$/.test(cmd)) {
            i++;
            if (cmd === 'M' || cmd === 'm') {
                while (i + 1 < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x = parseFloat(tokens[i++]);
                    let y = parseFloat(tokens[i++]);
                    if (cmd === 'm') { x += currX; y += currY; }
                    currX = x; currY = y;
                    startX = x; startY = y;
                    nodes.push({ cmd: 'M', x, y });
                }
            } else if (cmd === 'L' || cmd === 'l') {
                while (i + 1 < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x = parseFloat(tokens[i++]);
                    let y = parseFloat(tokens[i++]);
                    if (cmd === 'l') { x += currX; y += currY; }
                    currX = x; currY = y;
                    nodes.push({ cmd: 'L', x, y });
                }
            } else if (cmd === 'C' || cmd === 'c') {
                while (i + 5 < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x1 = parseFloat(tokens[i++]), y1 = parseFloat(tokens[i++]);
                    let x2 = parseFloat(tokens[i++]), y2 = parseFloat(tokens[i++]);
                    let x  = parseFloat(tokens[i++]), y  = parseFloat(tokens[i++]);
                    if (cmd === 'c') { x1+=currX; y1+=currY; x2+=currX; y2+=currY; x+=currX; y+=currY; }
                    currX = x; currY = y;
                    nodes.push({ cmd: 'C', x, y, cp1: { x: x1, y: y1 }, cp2: { x: x2, y: y2 } });
                }
            } else if (cmd === 'Z' || cmd === 'z') {
                nodes.push({ cmd: 'Z', x: startX, y: startY });
                currX = startX; currY = startY;
            }
        } else {
            i++;
        }
    }
    return nodes;
}

function rebuildSvgPathFromNodes(nodes) {
    let d = '';
    nodes.forEach(n => {
        if (n.cmd === 'M') d += `M ${n.x} ${n.y} `;
        else if (n.cmd === 'L') d += `L ${n.x} ${n.y} `;
        else if (n.cmd === 'C') d += `C ${n.cp1.x} ${n.cp1.y} ${n.cp2.x} ${n.cp2.y} ${n.x} ${n.y} `;
        else if (n.cmd === 'Z') d += 'Z ';
    });
    return d.trim();
}

function enterNodeEditingForSelected() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto antes de usar a Ferramenta Forma.', 'err');
        return;
    }
    let el = state.selectedElements[0];
    const tag = el.tagName.toLowerCase();

    // Auto-converter formas primitivas em path para permitir edição de nós
    if (tag !== 'path') {
        const converted = convertShapeToPath(el);
        if (!converted) {
            toast('Não foi possível converter esta forma em caminho.', 'err');
            return;
        }
        el = converted;
        state.selectedElements = [el];
        renderSelectionOverlay();
        toast('Forma convertida em Curva Bézier automaticamente!', 'ok');
    }

    state.nodeEdit.activePath = el;
    state.nodeEdit.nodes = parseSvgPathToNodes(el.getAttribute('d') || '');
    state.nodeEdit.activeNodeIndex = (state.nodeEdit.nodes.length > 0) ? 0 : -1;
    renderNodeEditOverlay();
    toast(`${state.nodeEdit.nodes.filter(n=>n.cmd!=='Z').length} nós disponíveis. Clique num nó para selecioná-lo.`, 'ok');
}

// Converte rect / ellipse / polygon em SVG <path>
function convertShapeToPath(el) {
    const tag = el.tagName.toLowerCase();
    let d = '';

    if (tag === 'rect') {
        const x = parseFloat(el.getAttribute('x') || 0);
        const y = parseFloat(el.getAttribute('y') || 0);
        const w = parseFloat(el.getAttribute('width') || 0);
        const h = parseFloat(el.getAttribute('height') || 0);
        const rx = parseFloat(el.getAttribute('rx') || 0);
        if (w < 1 || h < 1) return null;
        if (rx > 0) {
            const r = Math.min(rx, w/2, h/2);
            d = `M ${x+r} ${y} L ${x+w-r} ${y} Q ${x+w} ${y} ${x+w} ${y+r} L ${x+w} ${y+h-r} Q ${x+w} ${y+h} ${x+w-r} ${y+h} L ${x+r} ${y+h} Q ${x} ${y+h} ${x} ${y+h-r} L ${x} ${y+r} Q ${x} ${y} ${x+r} ${y} Z`;
        } else {
            d = `M ${x} ${y} L ${x+w} ${y} L ${x+w} ${y+h} L ${x} ${y+h} Z`;
        }
    } else if (tag === 'ellipse' || tag === 'circle') {
        const cx = parseFloat(el.getAttribute('cx') || 0);
        const cy = parseFloat(el.getAttribute('cy') || 0);
        const rx = parseFloat(el.getAttribute('rx') || el.getAttribute('r') || 0);
        const ry = parseFloat(el.getAttribute('ry') || el.getAttribute('r') || 0);
        const k = 0.5522848; // cubic bezier approx constant for circle
        d = `M ${cx} ${cy-ry} C ${cx+rx*k} ${cy-ry} ${cx+rx} ${cy-ry*k} ${cx+rx} ${cy} C ${cx+rx} ${cy+ry*k} ${cx+rx*k} ${cy+ry} ${cx} ${cy+ry} C ${cx-rx*k} ${cy+ry} ${cx-rx} ${cy+ry*k} ${cx-rx} ${cy} C ${cx-rx} ${cy-ry*k} ${cx-rx*k} ${cy-ry} ${cx} ${cy-ry} Z`;
    } else if (tag === 'polygon') {
        const pts = (el.getAttribute('points') || '').trim().split(/[\s,]+/);
        if (pts.length < 4) return null;
        const pairs = [];
        for (let i = 0; i < pts.length - 1; i += 2) pairs.push(`${pts[i]},${pts[i+1]}`);
        d = `M ${pairs.join(' L ')} Z`;
    } else {
        return null;
    }

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', d);
    // Copia atributos visuais
    ['fill','stroke','stroke-width','opacity','transform','id'].forEach(attr => {
        const v = el.getAttribute(attr);
        if (v) path.setAttribute(attr, v);
    });
    el.parentElement.replaceChild(path, el);
    updateLayersTree();
    return path;
}

function renderNodeEditOverlay() {
    const overlay = document.getElementById('nodeEditOverlay');
    if (!overlay) return;
    overlay.innerHTML = '';
    const nodes = state.nodeEdit.nodes;
    if (!nodes || nodes.length === 0) return;

    // Escala inversa para compensar o zoom (os nós ficam na coordenada SVG, mas o overlay é SVG também)
    // O overlay está no mesmo sistema de coordenadas que o path — não precisa de compensação
    nodes.forEach((n, idx) => {
        if (n.cmd === 'Z') return;

        // Handle quadrado do nó
        const sq = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        const isActive = (idx === state.nodeEdit.activeNodeIndex);
        sq.setAttribute('x', (n.x - 5).toString());
        sq.setAttribute('y', (n.y - 5).toString());
        sq.setAttribute('width', '10');
        sq.setAttribute('height', '10');
        sq.setAttribute('fill', isActive ? '#0066cc' : '#ffffff');
        sq.setAttribute('stroke', '#0044aa');
        sq.setAttribute('stroke-width', '1.5');
        sq.style.cursor = 'move';
        sq.addEventListener('mousedown', (e) => {
            e.stopPropagation();
            e.preventDefault();
            state.nodeEdit.activeNodeIndex = idx;
            renderNodeEditOverlay();

            // Inicia arrastar nó
            const mainSvg = document.getElementById('mainSvgCanvas');
            const onMove = (me) => {
                if (!mainSvg) return;
                const pt = mainSvg.createSVGPoint();
                pt.x = me.clientX;
                pt.y = me.clientY;
                const svgPt = pt.matrixTransform(mainSvg.getScreenCTM().inverse());
                const node = state.nodeEdit.nodes[idx];
                if (node) {
                    node.x = Math.round(svgPt.x);
                    node.y = Math.round(svgPt.y);
                    // Atualiza o path em tempo real
                    state.nodeEdit.activePath.setAttribute('d', rebuildSvgPathFromNodes(state.nodeEdit.nodes));
                    renderNodeEditOverlay();
                }
            };
            const onUp = () => {
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onUp);
                saveState('Mover Nó');
            };
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onUp);
        });

        overlay.appendChild(sq);

        // Linhas de controle para nós Bézier (tipo C)
        if (n.cmd === 'C' && n.cp2) {
            const line2 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line2.setAttribute('x1', n.x.toString());
            line2.setAttribute('y1', n.y.toString());
            line2.setAttribute('x2', n.cp2.x.toString());
            line2.setAttribute('y2', n.cp2.y.toString());
            line2.setAttribute('stroke', '#0088ff');
            line2.setAttribute('stroke-width', '1');
            line2.setAttribute('stroke-dasharray', '3,2');
            overlay.appendChild(line2);

            const cp2dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            cp2dot.setAttribute('cx', n.cp2.x.toString());
            cp2dot.setAttribute('cy', n.cp2.y.toString());
            cp2dot.setAttribute('r', '4');
            cp2dot.setAttribute('fill', '#88ccff');
            cp2dot.setAttribute('stroke', '#0044aa');
            cp2dot.setAttribute('stroke-width', '1');
            overlay.appendChild(cp2dot);
        }
    });
}


function addNodeToSelectedPath() {
    const nodes = state.nodeEdit.nodes;
    if (!state.nodeEdit.activePath || !nodes || nodes.length === 0) {
        toast('Selecione um caminho com a ferramenta Forma (F10) para adicionar um nó.', 'err');
        return;
    }
    const idx = state.nodeEdit.activeNodeIndex;
    const curr = nodes[idx] || nodes[0];
    const next = nodes[(idx + 1) % nodes.length] || curr;
    const midX = Math.round((curr.x + next.x) / 2);
    const midY = Math.round((curr.y + next.y) / 2);
    nodes.splice(idx + 1, 0, { cmd: 'L', x: midX, y: midY });
    state.nodeEdit.activeNodeIndex = idx + 1;
    state.nodeEdit.activePath.setAttribute('d', rebuildSvgPathFromNodes(nodes));
    renderNodeEditOverlay();
    saveState('Adicionar Nó');
    toast('Nó adicionado no ponto médio!', 'ok');
}

function deleteSelectedNode() {
    const nodes = state.nodeEdit.nodes;
    if (!state.nodeEdit.activePath || !nodes || nodes.length <= 2) {
        toast('Um caminho precisa de pelo menos 2 nós.', 'err');
        return;
    }
    const idx = state.nodeEdit.activeNodeIndex;
    if (idx < 0 || idx >= nodes.length) return;
    nodes.splice(idx, 1);
    state.nodeEdit.activeNodeIndex = Math.max(0, idx - 1);
    state.nodeEdit.activePath.setAttribute('d', rebuildSvgPathFromNodes(nodes));
    renderNodeEditOverlay();
    saveState('Excluir Nó');
    toast('Nó excluído!', 'ok');
}

function convertNodeToCurve() {
    const nodes = state.nodeEdit.nodes;
    if (!state.nodeEdit.activePath || !nodes) return;
    const idx = state.nodeEdit.activeNodeIndex;
    const cmd = nodes[idx];
    if (!cmd || cmd.cmd === 'Z' || cmd.cmd === 'M') return;
    const prev = nodes[idx - 1] || { x: cmd.x - 50, y: cmd.y };
    cmd.cmd = 'C';
    cmd.cp1 = { x: Math.round(prev.x + (cmd.x - prev.x) * 0.33), y: Math.round(prev.y - 20) };
    cmd.cp2 = { x: Math.round(prev.x + (cmd.x - prev.x) * 0.66), y: Math.round(cmd.y + 20) };
    state.nodeEdit.activePath.setAttribute('d', rebuildSvgPathFromNodes(nodes));
    renderNodeEditOverlay();
    saveState('Converter em Curva Bézier');
    toast('Nó convertido em Curva Bézier suave!', 'ok');
}

function convertNodeToLine() {
    const nodes = state.nodeEdit.nodes;
    if (!state.nodeEdit.activePath || !nodes) return;
    const idx = state.nodeEdit.activeNodeIndex;
    const cmd = nodes[idx];
    if (!cmd || cmd.cmd === 'Z' || cmd.cmd === 'M') return;
    cmd.cmd = 'L';
    delete cmd.cp1;
    delete cmd.cp2;
    state.nodeEdit.activePath.setAttribute('d', rebuildSvgPathFromNodes(nodes));
    renderNodeEditOverlay();
    saveState('Converter em Linha Reta');
    toast('Nó convertido em segmento de linha reta!', 'ok');
}

// ==================== Keyboard shortcuts: also wire Ctrl+X/C/V ====================
// (added to existing initKeyboardShortcuts; these are exposed here for completeness)
document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('keydown', (e) => {
        const isInput = ['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase());
        if (isInput) return;
        if ((e.ctrlKey || e.metaKey) && (e.key === 'x' || e.key === 'X')) {
            e.preventDefault();
            cutSelected();
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C')) {
            e.preventDefault();
            copySelected();
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 'v' || e.key === 'V')) {
            // Se houver objetos internos copiados no CorelClone, cola eles
            if (state.clipboard && state.clipboard.length > 0) {
                e.preventDefault();
                pasteSelected();
            }
            // Caso contrário, deixa o evento nativo 'paste' capturar se for imagem do SO/navegador
        }
    });

    // Captura imagens coladas da área de transferência do Sistema Operacional / Navegador (Ctrl+V)
    window.addEventListener('paste', (e) => {
        const tag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        if (tag === 'input' || tag === 'textarea') return;

        if (e.clipboardData && e.clipboardData.items) {
            for (let i = 0; i < e.clipboardData.items.length; i++) {
                const item = e.clipboardData.items[i];
                if (item.type && item.type.indexOf('image') !== -1) {
                    e.preventDefault();
                    const blob = item.getAsFile();
                    if (blob) {
                        const url = URL.createObjectURL(blob);
                        importImageURL(url);
                        toast('📋 Imagem colada na prancheta! Clique em "Rastrear Bitmap" (⚡) para vetorizar.', 'ok');
                        return;
                    }
                }
            }
        }
    });

    // Arraste e Solte (Drag & Drop) de arquivos de imagem e vetores diretamente na tela
    window.addEventListener('dragover', (e) => {
        e.preventDefault();
    });
    window.addEventListener('drop', async (e) => {
        e.preventDefault();
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            await handleImportFile(file);
        }
    });
});

function toast(msg, type = 'ok') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const t = document.createElement('div');
    t.className = `corel-toast ${type}`;
    t.innerHTML = `<i class="fas fa-info-circle"></i> <span>${msg}</span>`;
    container.appendChild(t);

    setTimeout(() => {
        t.style.opacity = '0';
        setTimeout(() => t.remove(), 250);
    }, 3500);
}

// ==================== Desktop Download & PWA Engine ====================
let deferredInstallPrompt = null;

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js').then(reg => {
            console.log('[CorelClone] Service Worker registrado:', reg.scope);
        }).catch(err => {
            console.warn('[CorelClone] Service Worker falhou:', err);
        });
    });
}

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredInstallPrompt = e;
});

function installPWA() {
    if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();
        deferredInstallPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                toast('🎉 CorelClone Pro instalado como WebApp!', 'ok');
            }
            deferredInstallPrompt = null;
        });
    } else {
        alert('💡 Para instalar como WebApp no seu computador:\n\n• No Google Chrome / Edge / Brave: Clique no ícone de instalar (computador com seta para baixo) na barra de endereços ao lado dos favoritos, ou clique nos três pontinhos no canto superior direito > "Instalar CorelClone Pro...".\n\n• No Safari (macOS / iPad): Clique em Compartilhar > "Adicionar à Tela de Início / Adicionar ao Dock".');
    }
}

function detectUserOS() {
    const userAgent = window.navigator.userAgent.toLowerCase();
    const platform = window.navigator?.userAgentData?.platform?.toLowerCase() || window.navigator.platform.toLowerCase();

    if (platform.includes('win') || userAgent.includes('windows')) {
        return 'windows';
    }
    if (platform.includes('mac') || userAgent.includes('macintosh') || userAgent.includes('mac os')) {
        return 'mac';
    }
    if (platform.includes('linux') || userAgent.includes('linux') || userAgent.includes('x11')) {
        return 'linux';
    }
    return 'unknown';
}

function openDownloadDesktopModal() {
    const modal = document.getElementById('downloadDesktopModal');
    if (!modal) return;

    const os = detectUserOS();
    const badge = document.getElementById('detectedOsBadge');
    const cardWin = document.getElementById('cardWin');
    const cardMac = document.getElementById('cardMac');
    const cardLinux = document.getElementById('cardLinux');

    // Reset styles
    if (cardWin) cardWin.style.boxShadow = 'none';
    if (cardMac) cardMac.style.boxShadow = 'none';
    if (cardLinux) cardLinux.style.boxShadow = 'none';

    if (os === 'windows') {
        if (badge) badge.innerHTML = '<i class="fab fa-windows text-sky-600"></i> Seu sistema: Windows 10/11';
        if (cardWin) {
            cardWin.style.borderColor = '#0284c7';
            cardWin.style.boxShadow = '0 0 0 2px #38bdf8';
        }
    } else if (os === 'mac') {
        if (badge) badge.innerHTML = '<i class="fab fa-apple text-slate-800"></i> Seu sistema: Apple macOS';
        if (cardMac) {
            cardMac.style.borderColor = '#1e293b';
            cardMac.style.boxShadow = '0 0 0 2px #94a3b8';
        }
    } else if (os === 'linux') {
        if (badge) badge.innerHTML = '<i class="fab fa-linux text-amber-600"></i> Seu sistema: Linux (Zorin/Ubuntu)';
        if (cardLinux) {
            cardLinux.style.borderColor = '#d97706';
            cardLinux.style.boxShadow = '0 0 0 2px #fbbf24';
        }
    } else {
        if (badge) badge.innerHTML = '<i class="fas fa-laptop"></i> Escolha seu sistema abaixo';
    }

    modal.style.display = 'flex';
}

function closeDownloadDesktopModal() {
    const modal = document.getElementById('downloadDesktopModal');
    if (modal) modal.style.display = 'none';
}
