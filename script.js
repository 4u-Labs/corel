// =====================================================
// CorelClone Pro 2026 Engine — Studio Vector Engine
// =====================================================

const state = {
    activeTool: 'select',
    activeLayer: 'layer1',
    selectedElements: [],
    zoom: 100,
    panX: 0,
    panY: 0,
    isPanning: false,
    panStart: { x: 0, y: 0 },
    isDrawing: false,
    drawStartPos: { x: 0, y: 0 },
    currentElement: null,
    history: [],
    historyIndex: -1,
    docWidth: 1122,
    docHeight: 793,
    aspectLocked: false,
    activeTransform: null, // { type: 'drag'|'nw'|'n'|'ne'|'e'|'se'|'s'|'sw'|'w'|'rot', startPt, bbox, origTransforms }
    freehandPoints: [],
    penPoints: [],
    cdrPages: [],
    activePageIndex: 0,
    // Linhas-Guia & Réguas Interativas
    guidelines: [],
    selectedGuideline: null,
    // Ferramenta Forma (F10) & Edição de Nós Bézier
    nodeEdit: {
        element: null,
        commands: [],
        activeNodeIndex: -1,
        activeHandleType: null // 'node' | 'cp1' | 'cp2'
    },
    nodeDrag: null
};

// Swatches Paleta CMYK / RGB estilo CorelDRAW
const paletteColors = [
    '#000000', '#1a1a1a', '#333333', '#4d4d4d', '#666666', '#808080', '#999999', '#b3b3b3', '#cccccc', '#e6e6e6', '#ffffff',
    '#ed1c24', '#f26522', '#f7941e', '#ffc20e', '#fff200', '#8dc63f', '#39b54a', '#00a651', '#00a99d', '#00aeef', '#0072bc',
    '#0054a6', '#2e3192', '#662d91', '#92278f', '#9e1f63', '#d4145a', '#ed1e79', '#c2185b', '#7b1fa2', '#512da8', '#303f9f',
    '#1976d2', '#0288d1', '#0097a7', '#00796b', '#388e3c', '#689f38', '#afb42b', '#fbc02d', '#ffa000', '#f57c00', '#e64a19',
    '#795548', '#5d4037', '#4e342e', '#3e2723', '#263238', '#37474f', '#455a64', '#546e7a', '#78909c', '#90a4ae', '#b0bec5'
];

document.addEventListener('DOMContentLoaded', () => {
    initPaperEngine();
    initPalette();
    initToolPalette();
    initCanvasEvents();
    initPropertyBar();
    initImportExport();
    drawRulers();
    initRulerGuidelineDrag();
    updateLayersTree();
    saveState('Início do Projeto');
    setTimeout(() => fitToScreen(), 100);
});

// ==================== Paleta de Cores ====================
function initPalette() {
    const container = document.getElementById('paletteSwatches');
    if (!container) return;
    container.innerHTML = '';
    paletteColors.forEach(color => {
        const swatch = document.createElement('div');
        swatch.className = 'swatch-item';
        swatch.style.backgroundColor = color;
        swatch.title = `Cor: ${color} (Esq: Preencher / Dir: Contorno)`;
        
        // Left click = Fill color
        swatch.addEventListener('click', (e) => {
            e.preventDefault();
            applyColorToSelected(color, 'fill');
        });

        // Right click = Stroke color
        swatch.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            applyColorToSelected(color, 'stroke');
        });

        container.appendChild(swatch);
    });
}

function applyColorToSelected(color, target = 'fill') {
    if (target === 'fill') {
        const fillInput = document.getElementById('propFillColor');
        if (fillInput) fillInput.value = color;
        if (state.selectedElements.length > 0) {
            state.selectedElements.forEach(el => el.setAttribute('fill', color));
            saveState('Alterar Preenchimento');
            renderSelectionOverlay();
        }
    } else {
        const strokeInput = document.getElementById('propStrokeColor');
        if (strokeInput) strokeInput.value = color;
        if (state.selectedElements.length > 0) {
            state.selectedElements.forEach(el => {
                el.setAttribute('stroke', color);
                if (!el.getAttribute('stroke-width') || el.getAttribute('stroke-width') === '0') {
                    el.setAttribute('stroke-width', '2');
                }
            });
            saveState('Alterar Contorno');
            renderSelectionOverlay();
        }
    }
}

// ==================== Ferramentas e Atalhos ====================
function initToolPalette() {
    const buttons = document.querySelectorAll('.tool-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            state.activeTool = btn.dataset.tool;
            updatePropertyBarVisibility();
            
            // Finish active pen drawing if switching tools
            if (state.activeTool !== 'pen' && state.penPoints.length > 0) {
                finishPenPath();
            }

            // Shape Tool (F10) activation / deactivation
            if (state.activeTool === 'node') {
                initNodeEditingForSelected();
            } else {
                exitNodeEditing();
            }
        });
    });

    // Atalhos de teclado universais estilo CorelDRAW
    document.addEventListener('keydown', (e) => {
        const active = document.activeElement;
        const isTyping = active && (active.tagName === 'INPUT' || active.tagName === 'SELECT' || active.tagName === 'TEXTAREA');
        if (isTyping) return;

        if (e.ctrlKey || e.metaKey) {
            if (e.key.toLowerCase() === 'z') {
                e.preventDefault();
                if (e.shiftKey) redo(); else undo();
                return;
            }
            if (e.key.toLowerCase() === 'y') {
                e.preventDefault();
                redo();
                return;
            }
            if (e.key.toLowerCase() === 'q') {
                e.preventDefault();
                convertToCurvesSelected();
                return;
            }
            if (e.key.toLowerCase() === 'g') {
                e.preventDefault();
                groupSelected();
                return;
            }
            if (e.key.toLowerCase() === 'u') {
                e.preventDefault();
                if (e.shiftKey || e.altKey) {
                    ungroupAll();
                } else {
                    ungroupSelected();
                }
                return;
            }
            if (e.key.toLowerCase() === 'd') {
                e.preventDefault();
                duplicateSelected();
                return;
            }
            if (e.key.toLowerCase() === 's') {
                e.preventDefault();
                exportDocument('json');
                return;
            }
        }

        switch(e.key.toUpperCase()) {
            case 'F4': e.preventDefault(); fitToScreen(); break;
            case 'V': selectTool('select'); break;
            case 'F10': e.preventDefault(); selectTool('node'); break;
            case 'P':
                if (state.selectedElements.length > 0) {
                    centerSelectedToPage();
                } else {
                    selectTool('pen');
                }
                break;
            case 'F5': e.preventDefault(); selectTool('freehand'); break;
            case 'F6': e.preventDefault(); selectTool('rect'); break;
            case 'F7': e.preventDefault(); selectTool('ellipse'); break;
            case 'Y': selectTool('star'); break;
            case 'F8': e.preventDefault(); selectTool('text'); break;
            case 'I': selectTool('eyedropper'); break;
            case 'H': selectTool('zoom'); break;
            case 'PAGEUP':
                if (e.shiftKey) { e.preventDefault(); orderSelected('front'); }
                break;
            case 'PAGEDOWN':
                if (e.shiftKey) { e.preventDefault(); orderSelected('back'); }
                break;
            case 'DELETE':
            case 'BACKSPACE':
                e.preventDefault();
                if (state.selectedGuideline) {
                    deleteSelectedGuideline();
                } else if (state.activeTool === 'node' && state.nodeEdit.activeNodeIndex > -1) {
                    deleteSelectedNode();
                } else {
                    deleteSelected();
                }
                break;
            case 'ESCAPE':
                if (state.penPoints.length > 0) {
                    cancelPenPath();
                } else {
                    deselectAll();
                }
                break;
            case 'ENTER':
                if (state.penPoints.length > 0) {
                    finishPenPath();
                }
                break;
        }
    });
}

function selectTool(name) {
    const btn = document.querySelector(`.tool-btn[data-tool="${name}"]`);
    if (btn) btn.click();
}

// ==================== Interatividade do Canvas SVG ====================
function initCanvasEvents() {
    const svg = document.getElementById('mainSvgCanvas');
    const viewport = document.getElementById('canvasViewport');
    const layerGroup = document.getElementById('layerGroupMain');

    if (!svg || !viewport) return;

    // Mouse Down
    svg.addEventListener('mousedown', (e) => {
        if (e.button !== 0 && e.button !== 1) return; // Left or Middle click
        
        // Pan tool or Middle Click or Space Key
        if (state.activeTool === 'zoom' || e.button === 1 || e.spaceKey) {
            state.isPanning = true;
            state.panStart = { x: e.clientX - viewport.scrollLeft, y: e.clientY - viewport.scrollTop };
            viewport.style.cursor = 'grabbing';
            return;
        }

        const pt = getSvgCoords(e);
        state.drawStartPos = pt;

        // Check if clicked on a transform handle in selectionOverlay
        const handle = e.target.closest('[data-handle]');
        if (handle) {
            const handleType = handle.dataset.handle;
            startTransform(handleType, pt);
            return;
        }

        // Eyedropper Tool
        if (state.activeTool === 'eyedropper') {
            if (e.target !== svg && e.target.id !== 'bgSheet') {
                const fill = e.target.getAttribute('fill') || '#ffffff';
                const stroke = e.target.getAttribute('stroke') || '#ffffff';
                document.getElementById('propFillColor').value = fill.startsWith('#') ? fill : '#6366f1';
                document.getElementById('propStrokeColor').value = stroke.startsWith('#') ? stroke : '#ffffff';
                toast(`Cor capturada: ${fill}`, 'ok');
                selectTool('select');
            }
            return;
        }

        // Shape Tool (F10) Node / Handle or Object click
        if (state.activeTool === 'node') {
            const anchor = e.target.closest('.node-anchor');
            if (anchor) {
                const idx = parseInt(anchor.dataset.nodeIndex, 10);
                startNodeDrag(idx, 'node', pt);
                return;
            }
            const ctrl = e.target.closest('.node-control-point');
            if (ctrl) {
                const idx = parseInt(ctrl.dataset.nodeIndex, 10);
                const handleType = ctrl.dataset.handleType;
                startNodeDrag(idx, handleType, pt);
                return;
            }

            const clickedObject = (e.target !== svg && e.target.id !== 'bgSheet' && !e.target.closest('#selectionOverlay') && !e.target.closest('#nodeEditOverlay') && !e.target.closest('#guidelinesGroup')) ? e.target.closest('#layerGroupMain > *') : null;
            if (clickedObject) {
                selectElement(clickedObject, false);
                initNodeEditingForSelected();
            } else {
                deselectAll();
            }
            return;
        }

        // Selection / Pick Tool (V)
        if (state.activeTool === 'select') {
            const clickedObject = (e.target !== svg && e.target.id !== 'bgSheet' && !e.target.closest('#selectionOverlay') && !e.target.closest('#guidelinesGroup')) ? e.target.closest('#layerGroupMain > *') : null;
            if (clickedObject) {
                selectElement(clickedObject, e.shiftKey);
                startTransform('drag', pt);
            } else {
                deselectAll();
            }
            return;
        }

        // Pen Tool (Bézier Point-by-Point)
        if (state.activeTool === 'pen') {
            addPenPoint(pt);
            return;
        }

        // Freehand / Brush Tool
        if (state.activeTool === 'freehand') {
            state.isDrawing = true;
            state.freehandPoints = [pt];
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', `M ${pt.x} ${pt.y}`);
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke', document.getElementById('propStrokeColor')?.value || '#ffffff');
            path.setAttribute('stroke-width', document.getElementById('propStrokeWidth')?.value || '3');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('stroke-linejoin', 'round');
            layerGroup.appendChild(path);
            state.currentElement = path;
            return;
        }

        // Shape Tools
        state.isDrawing = true;
        const fill = document.getElementById('propFillColor')?.value || '#6366f1';
        const stroke = document.getElementById('propStrokeColor')?.value || '#ffffff';
        const strokeW = document.getElementById('propStrokeWidth')?.value || '2';

        if (state.activeTool === 'rect') {
            const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', pt.x.toString());
            rect.setAttribute('y', pt.y.toString());
            rect.setAttribute('width', '1');
            rect.setAttribute('height', '1');
            rect.setAttribute('rx', document.getElementById('propCorner')?.value || '0');
            rect.setAttribute('fill', fill);
            rect.setAttribute('stroke', stroke);
            rect.setAttribute('stroke-width', strokeW);
            layerGroup.appendChild(rect);
            state.currentElement = rect;
        } else if (state.activeTool === 'ellipse') {
            const ellipse = document.createElementNS('http://www.w3.org/2000/svg', 'ellipse');
            ellipse.setAttribute('cx', pt.x.toString());
            ellipse.setAttribute('cy', pt.y.toString());
            ellipse.setAttribute('rx', '1');
            ellipse.setAttribute('ry', '1');
            ellipse.setAttribute('fill', fill);
            ellipse.setAttribute('stroke', stroke);
            ellipse.setAttribute('stroke-width', strokeW);
            layerGroup.appendChild(ellipse);
            state.currentElement = ellipse;
        } else if (state.activeTool === 'star') {
            const polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
            polygon.setAttribute('points', generateStarPoints(pt.x, pt.y, 5, 2, 1));
            polygon.setAttribute('fill', fill);
            polygon.setAttribute('stroke', stroke);
            polygon.setAttribute('stroke-width', strokeW);
            layerGroup.appendChild(polygon);
            state.currentElement = polygon;
        } else if (state.activeTool === 'text') {
            const textVal = prompt('Digite o texto a ser inserido na prancheta:', 'CorelClone Pro 2026');
            if (textVal) {
                const textEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                textEl.setAttribute('x', pt.x.toString());
                textEl.setAttribute('y', pt.y.toString());
                textEl.setAttribute('font-family', document.getElementById('propFontFamily')?.value || 'Plus Jakarta Sans');
                textEl.setAttribute('font-size', document.getElementById('propFontSize')?.value || '32');
                textEl.setAttribute('fill', fill);
                textEl.textContent = textVal;
                layerGroup.appendChild(textEl);
                selectElement(textEl, false);
                saveState('Inserir Texto');
            }
            state.isDrawing = false;
        }
    });

    // Mouse Move
    window.addEventListener('mousemove', (e) => {
        // Pan
        if (state.isPanning) {
            viewport.scrollLeft = state.panStart.x - (e.clientX - viewport.scrollLeft);
            viewport.scrollTop = state.panStart.y - (e.clientY - viewport.scrollTop);
            return;
        }

        const pt = getSvgCoords(e);
        updateRulerCrosshairs(e);

        // Active Node Drag in Shape Tool (F10)
        if (state.nodeDrag) {
            handleNodeDragMove(pt, e);
            return;
        }

        // Active Transform (Drag or Resize Handle or Rotate)
        if (state.activeTransform) {
            handleTransformMove(pt, e);
            return;
        }

        // Active Freehand Drawing
        if (state.isDrawing && state.activeTool === 'freehand' && state.currentElement) {
            state.freehandPoints.push(pt);
            let d = `M ${state.freehandPoints[0].x} ${state.freehandPoints[0].y}`;
            for (let i = 1; i < state.freehandPoints.length; i++) {
                d += ` L ${state.freehandPoints[i].x} ${state.freehandPoints[i].y}`;
            }
            state.currentElement.setAttribute('d', d);
            return;
        }

        // Active Shape Drag Drawing
        if (state.isDrawing && state.currentElement) {
            const w = Math.abs(pt.x - state.drawStartPos.x);
            const h = Math.abs(pt.y - state.drawStartPos.y);
            const x = Math.min(pt.x, state.drawStartPos.x);
            const y = Math.min(pt.y, state.drawStartPos.y);

            if (state.activeTool === 'rect') {
                state.currentElement.setAttribute('x', x.toString());
                state.currentElement.setAttribute('y', y.toString());
                state.currentElement.setAttribute('width', Math.max(w, 2).toString());
                state.currentElement.setAttribute('height', Math.max(h, 2).toString());
            } else if (state.activeTool === 'ellipse') {
                const rx = w / 2;
                const ry = h / 2;
                state.currentElement.setAttribute('cx', (x + rx).toString());
                state.currentElement.setAttribute('cy', (y + ry).toString());
                state.currentElement.setAttribute('rx', Math.max(rx, 1).toString());
                state.currentElement.setAttribute('ry', Math.max(ry, 1).toString());
            } else if (state.activeTool === 'star') {
                const radius = Math.max(w, h) / 2;
                const cx = (x + radius);
                const cy = (y + radius);
                state.currentElement.setAttribute('points', generateStarPoints(cx, cy, 5, radius, radius * 0.45));
            }
        }
    });

    // Mouse Up
    window.addEventListener('mouseup', () => {
        if (state.isPanning) {
            state.isPanning = false;
            viewport.style.cursor = 'default';
        }

        if (state.nodeDrag) {
            finishNodeDrag();
            return;
        }

        if (state.activeTransform) {
            state.activeTransform = null;
            saveState('Transformar Objeto');
            renderSelectionOverlay();
            updatePropertyBarValues();
        }

        if (state.isDrawing && state.currentElement) {
            selectElement(state.currentElement, false);
            saveState(`Criar ${state.activeTool}`);
            state.isDrawing = false;
            state.currentElement = null;
            selectTool('select');
        }
    });

    // Double Click (Editar Texto ou Adicionar/Excluir Nós na Ferramenta Forma)
    svg.addEventListener('dblclick', (e) => {
        if (state.activeTool === 'node') {
            const anchor = e.target.closest('.node-anchor');
            if (anchor) {
                const idx = parseInt(anchor.dataset.nodeIndex, 10);
                state.nodeEdit.activeNodeIndex = idx;
                deleteSelectedNode();
                return;
            }
            const pt = getSvgCoords(e);
            addNodeAtPoint(pt);
            return;
        }

        const textNode = e.target.closest('text');
        if (textNode) {
            const newText = prompt('Editar texto:', textNode.textContent);
            if (newText !== null) {
                textNode.textContent = newText;
                saveState('Editar Texto');
                renderSelectionOverlay();
                updateLayersTree();
            }
        }
    });
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
    } catch { }
    return { x: e.clientX, y: e.clientY };
}

// ==================== Transform & Selection Handles Engine ====================
function selectElement(el, multi = false) {
    if (!el || el.id === 'bgSheet' || el.id === 'selectionOverlay' || el.id === 'guidelinesGroup' || el.id === 'nodeEditOverlay') return;

    if (!multi) {
        state.selectedElements = [el];
    } else {
        const idx = state.selectedElements.indexOf(el);
        if (idx > -1) state.selectedElements.splice(idx, 1);
        else state.selectedElements.push(el);
    }

    if (state.activeTool === 'node') {
        initNodeEditingForSelected();
    } else {
        renderSelectionOverlay();
    }

    updatePropertyBarValues();
    updateLayersTree();
}

function deselectAll() {
    state.selectedElements = [];
    state.selectedGuideline = null;
    exitNodeEditing();
    renderSelectionOverlay();
    renderGuidelines();
    updatePropertyBarValues();
    updateLayersTree();
}

function deleteSelected() {
    if (state.selectedElements.length === 0) return;
    state.selectedElements.forEach(el => el.remove());
    state.selectedElements = [];
    exitNodeEditing();
    renderSelectionOverlay();
    saveState('Excluir Objetos');
    updateLayersTree();
    toast('Objeto(s) excluído(s)', 'ok');
}

function duplicateSelected() {
    if (state.selectedElements.length === 0) return;
    const newSelected = [];
    state.selectedElements.forEach(el => {
        const clone = el.cloneNode(true);
        // Offset clone by 20px
        if (clone.hasAttribute('x')) clone.setAttribute('x', (parseFloat(clone.getAttribute('x')) + 20).toString());
        if (clone.hasAttribute('y')) clone.setAttribute('y', (parseFloat(clone.getAttribute('y')) + 20).toString());
        if (clone.hasAttribute('cx')) clone.setAttribute('cx', (parseFloat(clone.getAttribute('cx')) + 20).toString());
        if (clone.hasAttribute('cy')) clone.setAttribute('cy', (parseFloat(clone.getAttribute('cy')) + 20).toString());
        el.parentNode.appendChild(clone);
        newSelected.push(clone);
    });
    state.selectedElements = newSelected;
    renderSelectionOverlay();
    saveState('Duplicar Objeto');
    updateLayersTree();
    toast('Objeto duplicado com sucesso!', 'ok');
}

function getTransformedBBox(el) {
    try {
        const mainSvg = document.getElementById('mainSvgCanvas');
        if (!mainSvg || typeof el.getBBox !== 'function') return el.getBBox ? el.getBBox() : { x: 0, y: 0, width: 0, height: 0 };
        const bbox = el.getBBox();
        
        const elCTM = el.getScreenCTM ? el.getScreenCTM() : null;
        const svgCTM = mainSvg.getScreenCTM ? mainSvg.getScreenCTM() : null;
        if (!elCTM || !svgCTM) return bbox;

        const matrix = svgCTM.inverse().multiply(elCTM);
        const pt = mainSvg.createSVGPoint();
        const corners = [
            { x: bbox.x, y: bbox.y },
            { x: bbox.x + bbox.width, y: bbox.y },
            { x: bbox.x + bbox.width, y: bbox.y + bbox.height },
            { x: bbox.x, y: bbox.y + bbox.height }
        ].map(p => {
            pt.x = p.x;
            pt.y = p.y;
            return pt.matrixTransform(matrix);
        });

        const xs = corners.map(p => p.x);
        const ys = corners.map(p => p.y);
        const minX = Math.min(...xs);
        const maxX = Math.max(...xs);
        const minY = Math.min(...ys);
        const maxY = Math.max(...ys);
        return {
            x: minX,
            y: minY,
            width: maxX - minX,
            height: maxY - minY
        };
    } catch {
        return el.getBBox ? el.getBBox() : { x: 0, y: 0, width: 0, height: 0 };
    }
}

function renderSelectionOverlay() {
    const overlay = document.getElementById('selectionOverlay');
    if (!overlay) return;
    overlay.innerHTML = '';

    if (state.selectedElements.length === 0) return;

    // If Shape Tool (F10) is active, render node anchors instead of bounding box handles
    if (state.activeTool === 'node') {
        renderNodeEditOverlay();
        return;
    }

    // Get combined Bounding Box in canvas coordinates
    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
    state.selectedElements.forEach(el => {
        try {
            const bbox = getTransformedBBox(el);
            minX = Math.min(minX, bbox.x);
            minY = Math.min(minY, bbox.y);
            maxX = Math.max(maxX, bbox.x + bbox.width);
            maxY = Math.max(maxY, bbox.y + bbox.height);
        } catch { }
    });

    if (minX === Infinity || isNaN(minX)) return;

    const w = maxX - minX;
    const h = maxY - minY;
    const pad = 4;
    const bx = minX - pad;
    const by = minY - pad;
    const bw = w + pad * 2;
    const bh = h + pad * 2;

    // Bounding Box Rect
    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    rect.setAttribute('class', 'transform-bounding-rect');
    rect.setAttribute('x', bx.toString());
    rect.setAttribute('y', by.toString());
    rect.setAttribute('width', bw.toString());
    rect.setAttribute('height', bh.toString());
    overlay.appendChild(rect);

    // 8 Resize Handles
    const handles = [
        { id: 'nw', x: bx, y: by, cursor: 'nwse-resize' },
        { id: 'n',  x: bx + bw / 2, y: by, cursor: 'ns-resize' },
        { id: 'ne', x: bx + bw, y: by, cursor: 'nesw-resize' },
        { id: 'e',  x: bx + bw, y: by + bh / 2, cursor: 'ew-resize' },
        { id: 'se', x: bx + bw, y: by + bh, cursor: 'nwse-resize' },
        { id: 's',  x: bx + bw / 2, y: by + bh, cursor: 'ns-resize' },
        { id: 'sw', x: bx, y: by + bh, cursor: 'nesw-resize' },
        { id: 'w',  x: bx, y: by + bh / 2, cursor: 'ew-resize' },
    ];

    const handleSize = 8;
    handles.forEach(hPos => {
        const handleRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        handleRect.setAttribute('class', 'transform-handle');
        handleRect.setAttribute('data-handle', hPos.id);
        handleRect.setAttribute('x', (hPos.x - handleSize / 2).toString());
        handleRect.setAttribute('y', (hPos.y - handleSize / 2).toString());
        handleRect.setAttribute('width', handleSize.toString());
        handleRect.setAttribute('height', handleSize.toString());
        handleRect.style.cursor = hPos.cursor;
        overlay.appendChild(handleRect);
    });

    // Rotation Handle (Top stem)
    const rotLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    rotLine.setAttribute('class', 'transform-rot-line');
    rotLine.setAttribute('x1', (bx + bw / 2).toString());
    rotLine.setAttribute('y1', by.toString());
    rotLine.setAttribute('x2', (bx + bw / 2).toString());
    rotLine.setAttribute('y2', (by - 24).toString());
    overlay.appendChild(rotLine);

    const rotCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    rotCircle.setAttribute('class', 'transform-handle-rot');
    rotCircle.setAttribute('data-handle', 'rot');
    rotCircle.setAttribute('cx', (bx + bw / 2).toString());
    rotCircle.setAttribute('cy', (by - 24).toString());
    rotCircle.setAttribute('r', '5');
    overlay.appendChild(rotCircle);
}

function startTransform(type, pt) {
    if (state.selectedElements.length === 0) return;

    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
    state.selectedElements.forEach(el => {
        const bbox = getTransformedBBox(el);
        minX = Math.min(minX, bbox.x);
        minY = Math.min(minY, bbox.y);
        maxX = Math.max(maxX, bbox.x + bbox.width);
        maxY = Math.max(maxY, bbox.y + bbox.height);
    });

    const origProps = state.selectedElements.map(el => {
        const b = getTransformedBBox(el);
        
        let tx = 0, ty = 0, sx = 1, sy = 1;
        const transform = el.getAttribute('transform') || '';
        const mTrans = transform.match(/translate\(\s*([\d.-]+)[\s,]+([\d.-]+)\s*\)/);
        if (mTrans) {
            tx = parseFloat(mTrans[1]) || 0;
            ty = parseFloat(mTrans[2]) || 0;
        }
        const mScale = transform.match(/scale\(\s*([\d.-]+)(?:[\s,]+([\d.-]+))?\s*\)/);
        if (mScale) {
            sx = parseFloat(mScale[1]) || 1;
            sy = mScale[2] ? parseFloat(mScale[2]) : sx;
        }

        return {
            el: el,
            bbox: b,
            x: parseFloat(el.getAttribute('x') || el.getAttribute('cx') || b.x),
            y: parseFloat(el.getAttribute('y') || el.getAttribute('cy') || b.y),
            w: parseFloat(el.getAttribute('width') || (parseFloat(el.getAttribute('rx') || 0) * 2) || b.width),
            h: parseFloat(el.getAttribute('height') || (parseFloat(el.getAttribute('ry') || 0) * 2) || b.height),
            tx: tx,
            ty: ty,
            sx: sx,
            sy: sy,
            fontSize: parseFloat(el.getAttribute('font-size') || 28),
            transform: transform
        };
    });

    state.activeTransform = {
        type: type,
        startPt: pt,
        bbox: { x: minX, y: minY, w: maxX - minX, h: maxY - minY, cx: minX + (maxX - minX) / 2, cy: minY + (maxY - minY) / 2 },
        origProps: origProps
    };
}

function handleTransformMove(pt, e) {
    const t = state.activeTransform;
    if (!t) return;

    const dx = pt.x - t.startPt.x;
    const dy = pt.y - t.startPt.y;

    if (t.type === 'drag') {
        let actualDx = dx;
        let actualDy = dy;

        // Snapping Magnético às Linhas-Guia (Estilo CorelDRAW)
        if (state.guidelines && state.guidelines.length > 0 && !e.altKey) {
            const snapThreshold = 8;
            state.guidelines.forEach(guide => {
                if (guide.orientation === 'h') {
                    const testTop = t.bbox.y + dy;
                    const testMid = t.bbox.cy + dy;
                    const testBot = t.bbox.y + t.bbox.h + dy;
                    if (Math.abs(testTop - guide.pos) < snapThreshold) actualDy = guide.pos - t.bbox.y;
                    else if (Math.abs(testMid - guide.pos) < snapThreshold) actualDy = guide.pos - t.bbox.cy;
                    else if (Math.abs(testBot - guide.pos) < snapThreshold) actualDy = guide.pos - (t.bbox.y + t.bbox.h);
                } else {
                    const testLeft = t.bbox.x + dx;
                    const testMid = t.bbox.cx + dx;
                    const testRight = t.bbox.x + t.bbox.w + dx;
                    if (Math.abs(testLeft - guide.pos) < snapThreshold) actualDx = guide.pos - t.bbox.x;
                    else if (Math.abs(testMid - guide.pos) < snapThreshold) actualDx = guide.pos - t.bbox.cx;
                    else if (Math.abs(testRight - guide.pos) < snapThreshold) actualDx = guide.pos - (t.bbox.x + t.bbox.w);
                }
            });
        }

        t.origProps.forEach(item => {
            const hasTransform = Boolean(item.transform && item.transform.trim().length > 0);
            if (item.el.tagName.toLowerCase() === 'path' || item.el.tagName.toLowerCase() === 'g' || hasTransform) {
                let origTx = item.tx;
                let origTy = item.ty;
                const withoutTranslate = item.transform.replace(/translate\([^)]*\)/g, '').trim();
                const newTx = Math.round(origTx + actualDx);
                const newTy = Math.round(origTy + actualDy);
                item.el.setAttribute('transform', `translate(${newTx}, ${newTy}) ${withoutTranslate}`.trim());
            } else {
                if (item.el.hasAttribute('x')) item.el.setAttribute('x', Math.round(item.x + actualDx).toString());
                if (item.el.hasAttribute('y')) item.el.setAttribute('y', Math.round(item.y + actualDy).toString());
                if (item.el.hasAttribute('cx')) item.el.setAttribute('cx', Math.round(item.x + actualDx).toString());
                if (item.el.hasAttribute('cy')) item.el.setAttribute('cy', Math.round(item.y + actualDy).toString());
            }
        });
    } else if (t.type === 'rot') {
        const currentAngle = Math.atan2(pt.y - t.bbox.cy, pt.x - t.bbox.cx) * (180 / Math.PI);
        const startAngle = Math.atan2(t.startPt.y - t.bbox.cy, t.startPt.x - t.bbox.cx) * (180 / Math.PI);
        let deltaAngle = Math.round(currentAngle - startAngle);
        if (e.shiftKey) deltaAngle = Math.round(deltaAngle / 15) * 15; // 15° snap

        t.origProps.forEach(item => {
            item.el.setAttribute('transform', `rotate(${deltaAngle} ${t.bbox.cx} ${t.bbox.cy})`);
        });
        const propAngle = document.getElementById('propAngle');
        if (propAngle) propAngle.value = deltaAngle;
    } else {
        // Resize Handles (nw, n, ne, e, se, s, sw, w)
        const bw = Math.max(t.bbox.w, 1);
        const bh = Math.max(t.bbox.h, 1);

        let anchorX = t.bbox.x;
        let anchorY = t.bbox.y;

        let scaleX = 1;
        let scaleY = 1;

        if (t.type === 'se') {
            anchorX = t.bbox.x; anchorY = t.bbox.y;
            scaleX = (bw + dx) / bw; scaleY = (bh + dy) / bh;
        } else if (t.type === 'e') {
            anchorX = t.bbox.x; anchorY = t.bbox.cy;
            scaleX = (bw + dx) / bw; scaleY = 1;
        } else if (t.type === 's') {
            anchorX = t.bbox.cx; anchorY = t.bbox.y;
            scaleX = 1; scaleY = (bh + dy) / bh;
        } else if (t.type === 'nw') {
            anchorX = t.bbox.x + bw; anchorY = t.bbox.y + bh;
            scaleX = (bw - dx) / bw; scaleY = (bh - dy) / bh;
        } else if (t.type === 'w') {
            anchorX = t.bbox.x + bw; anchorY = t.bbox.cy;
            scaleX = (bw - dx) / bw; scaleY = 1;
        } else if (t.type === 'n') {
            anchorX = t.bbox.cx; anchorY = t.bbox.y + bh;
            scaleX = 1; scaleY = (bh - dy) / bh;
        } else if (t.type === 'ne') {
            anchorX = t.bbox.x; anchorY = t.bbox.y + bh;
            scaleX = (bw + dx) / bw; scaleY = (bh - dy) / bh;
        } else if (t.type === 'sw') {
            anchorX = t.bbox.x + bw; anchorY = t.bbox.y;
            scaleX = (bw - dx) / bw; scaleY = (bh + dy) / bh;
        }

        if (state.aspectLocked || e.shiftKey) {
            const uniScale = Math.max(scaleX, scaleY);
            if (scaleX !== 1) scaleX = uniScale;
            if (scaleY !== 1) scaleY = uniScale;
        }

        scaleX = Math.max(scaleX, 0.05);
        scaleY = Math.max(scaleY, 0.05);

        t.origProps.forEach(item => {
            const tag = item.el.tagName.toLowerCase();
            const hasTransform = Boolean(item.transform && item.transform.trim().length > 0);

            if (tag === 'g' || tag === 'path' || hasTransform) {
                const newTx = anchorX + (item.tx - anchorX) * scaleX;
                const newTy = anchorY + (item.ty - anchorY) * scaleY;
                const newSx = item.sx * scaleX;
                const newSy = item.sy * scaleY;
                const otherTransforms = item.transform
                    .replace(/translate\([^)]*\)/g, '')
                    .replace(/scale\([^)]*\)/g, '')
                    .trim();
                const transStr = `translate(${Math.round(newTx * 100) / 100}, ${Math.round(newTy * 100) / 100}) scale(${newSx.toFixed(6)}, ${newSy.toFixed(6)}) ${otherTransforms}`.trim();
                item.el.setAttribute('transform', transStr);
            } else if (tag === 'image' || tag === 'rect') {
                const newX = anchorX + (item.x - anchorX) * scaleX;
                const newY = anchorY + (item.y - anchorY) * scaleY;
                const newW = item.w * scaleX;
                const newH = item.h * scaleY;
                item.el.setAttribute('x', Math.round(newX).toString());
                item.el.setAttribute('y', Math.round(newY).toString());
                item.el.setAttribute('width', Math.max(Math.round(newW), 2).toString());
                item.el.setAttribute('height', Math.max(Math.round(newH), 2).toString());
            } else if (tag === 'ellipse') {
                const newCx = anchorX + (item.x - anchorX) * scaleX;
                const newCy = anchorY + (item.y - anchorY) * scaleY;
                item.el.setAttribute('cx', Math.round(newCx).toString());
                item.el.setAttribute('cy', Math.round(newCy).toString());
                item.el.setAttribute('rx', Math.max(Math.round((item.w * scaleX) / 2), 1).toString());
                item.el.setAttribute('ry', Math.max(Math.round((item.h * scaleY) / 2), 1).toString());
            } else if (tag === 'text') {
                const newX = anchorX + (item.x - anchorX) * scaleX;
                const newY = anchorY + (item.y - anchorY) * scaleY;
                item.el.setAttribute('x', Math.round(newX).toString());
                item.el.setAttribute('y', Math.round(newY).toString());
                item.el.setAttribute('font-size', Math.max(Math.round(item.fontSize * Math.min(scaleX, scaleY)), 8).toString());
            }
        });
    }

    renderSelectionOverlay();
    updatePropertyBarValues();
}

// ==================== Pen Tool (Bézier Engine) ====================
function addPenPoint(pt) {
    const layerGroup = document.getElementById('layerGroupMain');
    state.penPoints.push(pt);

    let path = document.getElementById('activePenPath');
    if (!path) {
        path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.id = 'activePenPath';
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', document.getElementById('propStrokeColor')?.value || '#ffffff');
        path.setAttribute('stroke-width', document.getElementById('propStrokeWidth')?.value || '2');
        layerGroup.appendChild(path);
    }

    let d = `M ${state.penPoints[0].x} ${state.penPoints[0].y}`;
    for (let i = 1; i < state.penPoints.length; i++) {
        d += ` L ${state.penPoints[i].x} ${state.penPoints[i].y}`;
    }
    path.setAttribute('d', d);
}

function finishPenPath() {
    const path = document.getElementById('activePenPath');
    if (path && state.penPoints.length > 1) {
        path.removeAttribute('id');
        path.setAttribute('fill', document.getElementById('propFillColor')?.value || 'none');
        selectElement(path, false);
        saveState('Criar Vetor Caneta');
    } else if (path) {
        path.remove();
    }
    state.penPoints = [];
}

function cancelPenPath() {
    const path = document.getElementById('activePenPath');
    if (path) path.remove();
    state.penPoints = [];
    selectTool('select');
}

// Helper: Generate Star Polygon Points
function generateStarPoints(cx, cy, spikes, outerRadius, innerRadius) {
    let rot = Math.PI / 2 * 3;
    let x = cx, y = cy;
    const step = Math.PI / spikes;
    let points = '';

    for (let i = 0; i < spikes; i++) {
        x = cx + Math.cos(rot) * outerRadius;
        y = cy + Math.sin(rot) * outerRadius;
        points += `${Math.round(x)},${Math.round(y)} `;
        rot += step;

        x = cx + Math.cos(rot) * innerRadius;
        y = cy + Math.sin(rot) * innerRadius;
        points += `${Math.round(x)},${Math.round(y)} `;
        rot += step;
    }
    return points.trim();
}

// ==================== Property Bar Helpers ====================
function initPropertyBar() {
    const inputs = ['propX', 'propY', 'propW', 'propH', 'propAngle', 'propCorner', 'propFontSize', 'propFontFamily', 'propFillColor', 'propStrokeColor', 'propStrokeWidth'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateSelectedFromPropertyBar);
            el.addEventListener('change', updateSelectedFromPropertyBar);
        }
    });
}

function updateSelectedFromPropertyBar() {
    if (state.selectedElements.length === 0) return;
    const x = document.getElementById('propX')?.value;
    const y = document.getElementById('propY')?.value;
    const w = document.getElementById('propW')?.value;
    const h = document.getElementById('propH')?.value;
    const fill = document.getElementById('propFillColor')?.value;
    const stroke = document.getElementById('propStrokeColor')?.value;
    const strokeW = document.getElementById('propStrokeWidth')?.value;
    const corner = document.getElementById('propCorner')?.value;
    const font = document.getElementById('propFontFamily')?.value;
    const fontSize = document.getElementById('propFontSize')?.value;
    const angle = document.getElementById('propAngle')?.value;

    state.selectedElements.forEach(el => {
        if (x !== undefined && el.hasAttribute('x')) el.setAttribute('x', x);
        if (y !== undefined && el.hasAttribute('y')) el.setAttribute('y', y);
        if (w !== undefined && el.hasAttribute('width')) el.setAttribute('width', w);
        if (h !== undefined && el.hasAttribute('height')) el.setAttribute('height', h);
        if (fill) el.setAttribute('fill', fill);
        if (stroke) el.setAttribute('stroke', stroke);
        if (strokeW) el.setAttribute('stroke-width', strokeW);
        if (corner && el.tagName.toLowerCase() === 'rect') el.setAttribute('rx', corner);
        if (font && el.tagName.toLowerCase() === 'text') el.setAttribute('font-family', font);
        if (fontSize && el.tagName.toLowerCase() === 'text') el.setAttribute('font-size', fontSize);
        if (angle) {
            const bbox = el.getBBox();
            el.setAttribute('transform', `rotate(${angle} ${bbox.x + bbox.width / 2} ${bbox.y + bbox.height / 2})`);
        }
    });

    renderSelectionOverlay();
    saveState('Alterar Propriedades');
}

function updatePropertyBarVisibility() {
    const cornerGroup = document.getElementById('propCornerGroup');
    const textGroup = document.getElementById('propTextGroup');
    const booleanGroup = document.getElementById('propBooleanGroup');
    const booleanDivider = document.getElementById('propBooleanDivider');
    const traceGroup = document.getElementById('propTraceGroup');
    const traceDivider = document.getElementById('propTraceDivider');
    const nodeGroup = document.getElementById('propNodeGroup');
    const nodeDivider = document.getElementById('propNodeDivider');

    const hasText = state.selectedElements.some(el => el.tagName.toLowerCase() === 'text');
    const hasRect = state.selectedElements.some(el => el.tagName.toLowerCase() === 'rect');
    const hasImage = state.selectedElements.some(el => el.tagName.toLowerCase() === 'image') || (document.querySelector('#layerGroupMain image') !== null);
    const multiSelected = state.selectedElements.length >= 2;
    const isNodeTool = state.activeTool === 'node';

    if (cornerGroup) cornerGroup.style.display = (hasRect || state.activeTool === 'rect') ? 'flex' : 'none';
    if (textGroup) textGroup.style.display = (hasText || state.activeTool === 'text') ? 'flex' : 'none';
    if (booleanGroup) booleanGroup.style.display = multiSelected ? 'flex' : 'none';
    if (booleanDivider) booleanDivider.style.display = multiSelected ? 'block' : 'none';
    if (traceGroup) traceGroup.style.display = hasImage ? 'flex' : 'none';
    if (traceDivider) traceDivider.style.display = hasImage ? 'block' : 'none';
    if (nodeGroup) nodeGroup.style.display = isNodeTool ? 'flex' : 'none';
    if (nodeDivider) nodeDivider.style.display = isNodeTool ? 'block' : 'none';

    const btnToggleBg = document.getElementById('btnToggleDuplicateBg');
    if (btnToggleBg) {
        btnToggleBg.style.display = hasImage ? 'inline-flex' : 'none';
    }
}

function updatePropertyBarValues() {
    updatePropertyBarVisibility();
    if (state.selectedElements.length === 0) return;
    const first = state.selectedElements[0];

    try {
        const bbox = getTransformedBBox(first);
        const setVal = (id, val) => { const el = document.getElementById(id); if (el && val !== undefined) el.value = val; };

        setVal('propX', Math.round(bbox.x));
        setVal('propY', Math.round(bbox.y));
        setVal('propW', Math.round(bbox.width));
        setVal('propH', Math.round(bbox.height));

        const fill = first.getAttribute('fill');
        if (fill && fill.startsWith('#')) setVal('propFillColor', fill);

        const stroke = first.getAttribute('stroke');
        if (stroke && stroke.startsWith('#')) setVal('propStrokeColor', stroke);

        const strokeW = first.getAttribute('stroke-width');
        if (strokeW) setVal('propStrokeWidth', strokeW);

        const rx = first.getAttribute('rx');
        if (rx) setVal('propCorner', rx);

        if (first.tagName.toLowerCase() === 'text') {
            setVal('propFontFamily', first.getAttribute('font-family') || 'Plus Jakarta Sans');
            setVal('propFontSize', first.getAttribute('font-size') || '32');
        }
    } catch { }
}

function toggleAspectLock() {
    state.aspectLocked = !state.aspectLocked;
    const icon = document.getElementById('lockIcon');
    if (icon) {
        icon.className = state.aspectLocked ? 'fas fa-lock text-emerald-400' : 'fas fa-lock-open';
    }
    toast(state.aspectLocked ? '🔒 Proporção de tamanho travada' : '🔓 Proporção destravada', 'ok');
}

function setNoFill() {
    if (state.selectedElements.length > 0) {
        state.selectedElements.forEach(el => el.setAttribute('fill', 'none'));
        saveState('Remover Preenchimento');
        renderSelectionOverlay();
    }
}

function setNoStroke() {
    if (state.selectedElements.length > 0) {
        state.selectedElements.forEach(el => el.setAttribute('stroke', 'none'));
        saveState('Remover Contorno');
        renderSelectionOverlay();
    }
}

function toggleTextBold() {
    state.selectedElements.forEach(el => {
        if (el.tagName.toLowerCase() === 'text') {
            const current = el.getAttribute('font-weight') || 'normal';
            el.setAttribute('font-weight', current === 'bold' ? 'normal' : 'bold');
        }
    });
    saveState('Formatar Texto Negrito');
}

function toggleTextItalic() {
    state.selectedElements.forEach(el => {
        if (el.tagName.toLowerCase() === 'text') {
            const current = el.getAttribute('font-style') || 'normal';
            el.setAttribute('font-style', current === 'italic' ? 'normal' : 'italic');
        }
    });
    saveState('Formatar Texto Itálico');
}

// ==================== Camadas (Layers Inspector) ====================
function updateLayersTree() {
    const tree = document.getElementById('layersTreeList');
    if (!tree) return;
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    tree.innerHTML = '';
    const children = Array.from(layerGroup.children).reverse();

    if (children.length === 0) {
        tree.innerHTML = '<div style="color: #64748b; font-size: 0.78rem; padding: 14px; text-align: center;">Nenhum objeto na prancheta.<br>Desenhe ou importe um arquivo.</div>';
        return;
    }

    children.forEach((child, index) => {
        const isSelected = state.selectedElements.includes(child);
        const item = document.createElement('div');
        item.className = `layer-item ${isSelected ? 'active' : ''}`;

        const tagName = child.tagName.toLowerCase();
        let icon = 'fas fa-vector-square';
        let label = `Objeto ${children.length - index}`;

        if (tagName === 'rect') { icon = 'far fa-square'; label = 'Retângulo'; }
        else if (tagName === 'ellipse') { icon = 'far fa-circle'; label = 'Elipse'; }
        else if (tagName === 'text') { icon = 'fas fa-font'; label = `Texto: "${child.textContent.slice(0, 14)}..."`; }
        else if (tagName === 'polygon') { icon = 'far fa-star'; label = 'Estrela'; }
        else if (tagName === 'path') {
            const stroke = (child.getAttribute('stroke') || '').toLowerCase();
            const fill = (child.getAttribute('fill') || '').toLowerCase();
            if (stroke.includes('0,158,224') || stroke.includes('cyan') || stroke.includes('#009ee0') || stroke.includes('#00d2ff')) {
                icon = 'fas fa-cut';
                label = 'Linha de Corte (Ciano)';
            } else if (fill.includes('251,143,173') || fill.includes('255,148,175') || fill.includes('pink') || fill.includes('#fb8fad')) {
                icon = 'fas fa-font';
                label = 'Texto em Curvas (Rosa)';
            } else {
                icon = 'fas fa-bezier-curve';
                label = 'Caminho Vetorial';
            }
        }
        else if (tagName === 'image') { icon = 'fas fa-image'; label = 'Imagem / Fundo Bitmap'; }
        else if (tagName === 'g') {
            if (child.classList && child.classList.contains('imported-svg-group')) {
                icon = 'fas fa-object-group';
                label = 'Grupo do Desenho (Ctrl+U)';
            } else {
                icon = 'fas fa-object-group';
                label = 'Grupo de Objetos';
            }
        }

        const isHidden = child.style.display === 'none' || child.getAttribute('visibility') === 'hidden';

        item.innerHTML = `
            <div style="display:flex; align-items:center; gap:8px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex:1;">
                <i class="${icon}" style="color: #a855f7; width: 14px;"></i>
                <span>${label}</span>
            </div>
            <div class="layer-item-actions" style="display:flex; gap:4px;">
                <button type="button" class="btn-icon btn-toggle-vis ${isHidden ? 'text-slate-500' : ''}" style="width:22px; height:22px; font-size:10px;" title="Alternar Visibilidade">
                    <i class="fas ${isHidden ? 'fa-eye-slash' : 'fa-eye'}"></i>
                </button>
                <button type="button" class="btn-icon btn-delete-layer text-rose-400" style="width:22px; height:22px; font-size:10px;" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        const btnVis = item.querySelector('.btn-toggle-vis');
        if (btnVis) {
            btnVis.onclick = (e) => {
                e.stopPropagation();
                toggleElementVisibility(btnVis, child);
            };
        }

        const btnDel = item.querySelector('.btn-delete-layer');
        if (btnDel) {
            btnDel.onclick = (e) => {
                e.stopPropagation();
                child.remove();
                deselectAll();
                saveState('Excluir Objeto');
                updateLayersTree();
            };
        }

        item.onclick = () => selectElement(child, false);
        tree.appendChild(item);
    });
}

function toggleElementVisibility(btn, target) {
    if (!target) return;
    const isHidden = target.style.display === 'none' || target.getAttribute('visibility') === 'hidden';
    if (isHidden) {
        target.style.display = '';
        target.removeAttribute('visibility');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-eye"></i>';
            btn.classList.remove('text-slate-500');
        }
        toast('Camada visível', 'ok');
    } else {
        target.style.display = 'none';
        if (state.selectedElements.includes(target)) {
            deselectAll();
        }
        if (btn) {
            btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
            btn.classList.add('text-slate-500');
        }
        toast('Camada ocultada', 'ok');
    }
    renderSelectionOverlay();
    saveState('Alternar Visibilidade de Camada');
}

function toggleImportedBgImage() {
    const images = Array.from(document.querySelectorAll('#layerGroupMain image'));
    if (images.length === 0) {
        toast('Nenhuma imagem bitmap encontrada no projeto.', 'warn');
        return;
    }
    const anyVisible = images.some(img => img.style.display !== 'none' && img.getAttribute('visibility') !== 'hidden');
    images.forEach(img => {
        if (anyVisible) {
            img.style.display = 'none';
        } else {
            img.style.display = '';
            img.removeAttribute('visibility');
        }
    });

    const btn = document.getElementById('btnToggleDuplicateBg');
    if (btn) {
        if (anyVisible) {
            btn.innerHTML = '<i class="fas fa-eye"></i> Mostrar Fundo';
            btn.classList.add('bg-purple-900/50');
            toast('Imagem de fundo ocultada! Texto duplicado do modelo removido.', 'ok');
        } else {
            btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar Fundo';
            btn.classList.remove('bg-purple-900/50');
            toast('Imagem de fundo reexibida.', 'ok');
        }
    }
    renderSelectionOverlay();
    updateLayersTree();
    saveState(anyVisible ? 'Ocultar Fundo / Texto Duplicado' : 'Exibir Fundo');
}

function clearAllObjects() {
    if (confirm('Deseja realmente limpar todos os objetos da prancheta?')) {
        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup) layerGroup.innerHTML = '';
        deselectAll();
        saveState('Limpar Prancheta');
        toast('Prancheta limpa!', 'ok');
    }
}

function deleteSingleObject(index) {
    const layerGroup = document.getElementById('layerGroupMain');
    if (layerGroup && layerGroup.children[index]) {
        layerGroup.children[index].remove();
        deselectAll();
        saveState('Excluir Objeto');
        updateLayersTree();
    }
}

// ==================== Alinhamento e Ordem ====================
function alignSelected(type) {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto para alinhar', 'err');
        return;
    }

    state.selectedElements.forEach(el => {
        try {
            const bbox = el.getBBox();
            if (type === 'left') {
                if (el.hasAttribute('x')) el.setAttribute('x', '0');
                if (el.hasAttribute('cx')) el.setAttribute('cx', (bbox.width / 2).toString());
            } else if (type === 'center') {
                const cx = (state.docWidth - bbox.width) / 2;
                if (el.hasAttribute('x')) el.setAttribute('x', cx.toString());
                if (el.hasAttribute('cx')) el.setAttribute('cx', (state.docWidth / 2).toString());
            } else if (type === 'right') {
                const rx = state.docWidth - bbox.width;
                if (el.hasAttribute('x')) el.setAttribute('x', rx.toString());
                if (el.hasAttribute('cx')) el.setAttribute('cx', (state.docWidth - bbox.width / 2).toString());
            } else if (type === 'top') {
                if (el.hasAttribute('y')) el.setAttribute('y', '0');
                if (el.hasAttribute('cy')) el.setAttribute('cy', (bbox.height / 2).toString());
            } else if (type === 'middle') {
                const cy = (state.docHeight - bbox.height) / 2;
                if (el.hasAttribute('y')) el.setAttribute('y', cy.toString());
                if (el.hasAttribute('cy')) el.setAttribute('cy', (state.docHeight / 2).toString());
            } else if (type === 'bottom') {
                const by = state.docHeight - bbox.height;
                if (el.hasAttribute('y')) el.setAttribute('y', by.toString());
                if (el.hasAttribute('cy')) el.setAttribute('cy', (state.docHeight - bbox.height / 2).toString());
            }
        } catch { }
    });

    renderSelectionOverlay();
    saveState(`Alinhar ${type}`);
    toast(`Alinhado ao ${type}`, 'ok');
}

function orderSelected(order) {
    if (state.selectedElements.length === 0) return;
    const parent = state.selectedElements[0].parentNode;
    state.selectedElements.forEach(el => {
        if (order === 'front') parent.appendChild(el);
        if (order === 'back') parent.insertBefore(el, parent.firstChild);
        if (order === 'forward' && el.nextElementSibling) parent.insertBefore(el.nextElementSibling, el);
        if (order === 'backward' && el.previousElementSibling) parent.insertBefore(el, el.previousElementSibling);
    });
    renderSelectionOverlay();
    saveState('Alterar Ordem');
    updateLayersTree();
}

function groupSelected() {
    if (state.selectedElements.length < 2) {
        toast('Selecione pelo menos 2 objetos para agrupar (Ctrl+G)', 'err');
        return;
    }
    const layerGroup = document.getElementById('layerGroupMain');
    const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    state.selectedElements.forEach(el => g.appendChild(el));
    layerGroup.appendChild(g);
    selectElement(g, false);
    saveState('Agrupar Objetos');
    toast('Objetos agrupados (Ctrl+G)', 'ok');
}

function ungroupSelected() {
    if (state.selectedElements.length === 0) return;
    const layerGroup = document.getElementById('layerGroupMain');
    const newItems = [];

    state.selectedElements.forEach(el => {
        if (el.tagName.toLowerCase() === 'g') {
            const parentTransform = el.getAttribute('transform') || '';
            Array.from(el.children).forEach(child => {
                if (['defs', 'style', 'metadata'].includes(child.tagName.toLowerCase())) return;

                // Propagar o transform do grupo para os filhos para preservar posição e escala
                const childTransform = child.getAttribute('transform') || '';
                const combinedTransform = `${parentTransform} ${childTransform}`.trim();
                if (combinedTransform) {
                    child.setAttribute('transform', combinedTransform);
                }

                layerGroup.appendChild(child);
                newItems.push(child);
            });
            el.remove();
        }
    });

    state.selectedElements = newItems;
    renderSelectionOverlay();
    saveState('Desagrupar Objetos');
    updateLayersTree();
    toast('Objetos desagrupados (Ctrl+U) — camadas individuais liberadas!', 'ok');
}

function ungroupAll() {
    if (state.selectedElements.length === 0) {
        const allGroups = Array.from(document.querySelectorAll('#layerGroupMain > g'));
        if (allGroups.length > 0) {
            state.selectedElements = allGroups;
        } else {
            toast('Nenhum grupo selecionado para desagrupar.', 'warn');
            return;
        }
    }

    let hasGroups = true;
    let iterations = 0;

    while (hasGroups && iterations < 15) {
        hasGroups = false;
        iterations++;
        const currentGroups = state.selectedElements.filter(el => el.tagName.toLowerCase() === 'g');
        if (currentGroups.length > 0) {
            hasGroups = true;
            ungroupSelected();
        }
    }
    toast('Todos os grupos foram desagrupados em camadas individuais!', 'ok');
}

// ==================== Histórico & Undo / Redo ====================
function saveState(actionName = 'Ação') {
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    // Prune forward history
    if (state.historyIndex < state.history.length - 1) {
        state.history = state.history.slice(0, state.historyIndex + 1);
    }

    state.history.push({
        name: actionName,
        svg: layerGroup.innerHTML,
        docWidth: state.docWidth,
        docHeight: state.docHeight,
        time: new Date().toLocaleTimeString()
    });

    // Limit history stack
    if (state.history.length > 40) state.history.shift();
    state.historyIndex = state.history.length - 1;

    renderHistoryTab();
}

function undo() {
    if (state.historyIndex > 0) {
        state.historyIndex--;
        restoreHistoryState(state.history[state.historyIndex]);
        toast('Desfazer: ' + state.history[state.historyIndex].name, 'ok');
    }
}

function redo() {
    if (state.historyIndex < state.history.length - 1) {
        state.historyIndex++;
        restoreHistoryState(state.history[state.historyIndex]);
        toast('Refazer: ' + state.history[state.historyIndex].name, 'ok');
    }
}

function restoreHistoryState(snap) {
    const layerGroup = document.getElementById('layerGroupMain');
    if (layerGroup && snap) {
        layerGroup.innerHTML = snap.svg;
        deselectAll();
        updateLayersTree();
        renderHistoryTab();
    }
}

function renderHistoryTab() {
    const list = document.getElementById('historyList');
    if (!list) return;
    list.innerHTML = '';
    state.history.forEach((snap, idx) => {
        const item = document.createElement('div');
        item.style.cssText = `padding: 6px 10px; font-size: 0.75rem; border-radius: 6px; cursor: pointer; display: flex; justify-content: space-between; margin-bottom: 4px; ${idx === state.historyIndex ? 'background: rgba(168,85,247,0.25); color: #fff; font-weight: bold;' : 'color: #94a3b8;'}`;
        item.innerHTML = `<span>${snap.name}</span><span style="opacity:0.6;">${snap.time}</span>`;
        item.onclick = () => {
            state.historyIndex = idx;
            restoreHistoryState(state.history[idx]);
        };
        list.appendChild(item);
    });
}

// ==================== Presets de Documento ====================
function changeDocPreset(val) {
    const presets = {
        'A4-Landscape': { w: 1122, h: 793 },
        'A4-Portrait': { w: 793, h: 1122 },
        'Instagram-Post': { w: 1080, h: 1080 },
        'Instagram-Story': { w: 1080, h: 1920 },
        'Cartao-Visita': { w: 1050, h: 600 },
        'Banner-Web': { w: 1920, h: 1080 },
        'Banner-4K': { w: 3840, h: 2160 }
    };

    if (presets[val]) {
        state.docWidth = presets[val].w;
        state.docHeight = presets[val].h;
        applyDocDimensions();
        fitToScreen();
        saveState(`Formato ${val}`);
        toast(`Formato alterado: ${val}`, 'ok');
    }
}

function applyDocDimensions() {
    const svg = document.getElementById('mainSvgCanvas');
    const board = document.getElementById('workspaceBoard');
    if (svg && board) {
        svg.setAttribute('width', state.docWidth.toString());
        svg.setAttribute('height', state.docHeight.toString());
        svg.setAttribute('viewBox', `0 0 ${state.docWidth} ${state.docHeight}`);
        board.style.width = `${state.docWidth}px`;
        board.style.height = `${state.docHeight}px`;
    }
}

function newDocument() {
    if (confirm('Deseja criar um novo documento em branco?')) {
        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup) layerGroup.innerHTML = '';
        state.history = [];
        state.historyIndex = -1;
        state.cdrPages = [];
        state.activePageIndex = -1;
        renderPageTabBar();
        deselectAll();
        saveState('Novo Documento');
        fitToScreen();
        toast('Novo documento criado!', 'ok');
    }
}

// ==================== Importação (.CDR, SVG, Imagem, JSON) ====================
function initImportExport() {
    const fileInput = document.getElementById('importFileInput');
    const dropZone = document.getElementById('canvasViewport');

    if (fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (file) handleImportFile(file);
        });
    }

    // Drag & Drop onto viewport
    if (dropZone) {
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.outline = '2px dashed #a855f7'; });
        dropZone.addEventListener('dragleave', () => { dropZone.style.outline = 'none'; });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.outline = 'none';
            if (e.dataTransfer.files.length > 0) handleImportFile(e.dataTransfer.files[0]);
        });
    }
}

async function handleImportFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    toast(`Importando "${file.name}"...`, 'ok');

    if (ext === 'cdr' || ext === 'pdf') {
        // 1. Tentar conversão direta de Alta Fidelidade com o Bridge Vetorial
        let convertedSvg = null;
        try {
            toast(`Conectando ao conversor vetorial para .${ext.toUpperCase()}...`, 'ok');
            const formData = new FormData();
            formData.append('file', file);
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 20000);

            const isLocalHost = window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost';
            const bridgeUrl = isLocalHost ? '/convert' : 'http://127.0.0.1:54321/convert';

            const bridgeRes = await fetch(bridgeUrl, {
                method: 'POST',
                body: formData,
                signal: controller.signal
            });
            clearTimeout(timeoutId);

            if (bridgeRes.ok) {
                const svgText = await bridgeRes.text();
                if (svgText && svgText.includes('<svg')) {
                    convertedSvg = svgText;
                }
            }
        } catch (bridgeErr) {
            console.log('CorelClone Bridge local não conectado:', bridgeErr);
        }

        if (convertedSvg) {
            state.cdrPages = [];
            state.activePageIndex = -1;
            renderPageTabBar();
            importSVGContent(convertedSvg);
            toast(`⚡ Arquivo .${ext.toUpperCase()} aberto com 100% de PRECISÃO VETORIAL NATIVA!`, 'ok');
            return;
        }

        // 2. Fallback quando o Bridge local não responder
        if (ext === 'pdf') {
            await parsePDFFile(file);
        } else {
            await parseCDRFile(file);
        }
    } else if (ext === 'svg') {
        state.cdrPages = [];
        state.activePageIndex = -1;
        renderPageTabBar();
        const text = await file.text();
        importSVGContent(text);
    } else if (ext === 'json') {
        state.cdrPages = [];
        state.activePageIndex = -1;
        renderPageTabBar();
        const text = await file.text();
        importJSONProject(text);
    } else if (['png', 'jpg', 'jpeg', 'webp'].includes(ext)) {
        state.cdrPages = [];
        state.activePageIndex = -1;
        renderPageTabBar();
        const url = URL.createObjectURL(file);
        importImageURL(url);
    } else {
        alert('Formato não suportado. Use .CDR, .PDF, .SVG, .PNG, .JPG ou .JSON.');
    }
}

async function parsePDFFile(file) {
    try {
        if (typeof pdfjsLib === 'undefined') {
            toast('Carregando motor PDF.js...', 'ok');
            await new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;

        state.cdrPages = [];
        state.activePageIndex = -1;
        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 2.0 });
            const canvas = document.createElement('canvas');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            const ctx = canvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            state.cdrPages.push({ id: i - 1, name: `Página ${i}`, url: canvas.toDataURL('image/png'), svgContent: null });
        }

        renderPageTabBar();
        if (state.cdrPages.length > 0) {
            switchCDRPage(0);
            toast(`PDF com ${pdf.numPages} página(s) carregado com sucesso!`, 'ok');
        }
    } catch (err) {
        console.error('Erro ao ler PDF:', err);
        alert('Erro ao carregar PDF: ' + err.message);
    }
}

async function parseCDRFile(file) {
    try {
        const zip = new JSZip();
        const zipContent = await zip.loadAsync(file);
        const pageEntries = [];

        zipContent.forEach((relativePath, zipEntry) => {
            if (relativePath.match(/(previews|metadata|pages|thumbnails)\/.*\.(png|bmp|jpg|jpeg)/i) ||
                relativePath.match(/thumbnail.*\.(png|bmp|jpg|jpeg)/i) ||
                relativePath.match(/page.*\.(png|bmp|jpg|jpeg)/i)) {
                pageEntries.push({ path: relativePath, entry: zipEntry });
            }
        });

        pageEntries.sort((a, b) => a.path.localeCompare(b.path, undefined, { numeric: true, sensitivity: 'base' }));

        // Se existirem páginas específicas (ex: page1.png, page2.png), filtrar para não duplicar com thumbnail geral
        let filteredEntries = pageEntries;
        const pageSpecific = pageEntries.filter(p => p.path.match(/page\d+/i) || p.path.match(/page[_\-]\d+/i));
        if (pageSpecific.length > 0) {
            filteredEntries = pageSpecific;
        }

        if (filteredEntries.length > 0) {
            state.cdrPages = [];
            state.activePageIndex = -1;
            for (let i = 0; i < filteredEntries.length; i++) {
                const blob = await filteredEntries[i].entry.async("blob");
                const url = URL.createObjectURL(blob);
                state.cdrPages.push({ id: i, name: `Página ${i + 1}`, url: url, svgContent: null });
            }
            renderPageTabBar();
            switchCDRPage(0);
            toast(`Arquivo .CDR com ${filteredEntries.length} página(s) aberto!`, 'ok');
            setTimeout(() => {
                if (confirm('Arquivo CorelDRAW (.CDR) carregado!\n\n💡 Dica de Qualidade:\n• Para 100% de precisão vetorial nativa instantânea, use o CorelClone Desktop Local (http://127.0.0.1:54321).\n\nDeseja vetorizar agora com o PowerTRACE™ Suavizado?')) {
                    openPowerTraceDialog();
                }
            }, 700);
        } else {
            alert("Nenhuma pré-visualização de imagem encontrada no arquivo .CDR.");
        }
    } catch (err) {
        console.error(err);
        alert("Erro ao decodificar arquivo .CDR: " + err.message);
    }
}

function renderPageTabBar() {
    const bar = document.getElementById('cdrPageTabBar');
    if (!bar) return;
    if (!state.cdrPages || state.cdrPages.length <= 1) {
        bar.style.display = 'none';
        return;
    }
    bar.style.display = 'flex';
    bar.innerHTML = state.cdrPages.map((page, idx) => `
        <button type="button" class="page-tab-item ${state.activePageIndex === idx ? 'active' : ''}" onclick="switchCDRPage(${idx})">
            <i class="fas fa-file-alt"></i> ${page.name}
        </button>
    `).join('') + `
        <button type="button" class="page-tab-item page-tab-add" onclick="addNewPage()" title="Adicionar Nova Página">
            <i class="fas fa-plus"></i>
        </button>
    `;
}

function switchCDRPage(index) {
    if (!state.cdrPages || !state.cdrPages[index]) return;
    const layerGroup = document.getElementById('layerGroupMain');
    if (!layerGroup) return;

    // 1. Salvar conteúdo e dimensões da página anterior antes da troca
    if (state.activePageIndex >= 0 && state.activePageIndex !== index && state.cdrPages[state.activePageIndex]) {
        state.cdrPages[state.activePageIndex].svgContent = layerGroup.innerHTML;
        state.cdrPages[state.activePageIndex].docWidth = state.docWidth;
        state.cdrPages[state.activePageIndex].docHeight = state.docHeight;
    }

    // 2. Desmarcar todos os elementos para não sobrar caixas de seleção da página anterior
    deselectAll();

    // 3. Atualizar índice ativo e barra de abas
    state.activePageIndex = index;
    renderPageTabBar();

    const targetPage = state.cdrPages[index];

    // 4. Se a página já foi visitada e possui conteúdo salvo, restaurar com exclusividade!
    if (targetPage.svgContent !== undefined && targetPage.svgContent !== null) {
        layerGroup.innerHTML = targetPage.svgContent;
        if (targetPage.docWidth && targetPage.docHeight) {
            state.docWidth = targetPage.docWidth;
            state.docHeight = targetPage.docHeight;
            applyDocDimensions();
        }
        updateLayersTree();
        updatePropertyBarVisibility();
        saveState(`Mudar para ${targetPage.name}`);
        toast(`Exibindo ${targetPage.name}`, 'ok');
        return;
    }

    // 5. Primeira vez abrindo esta página: Limpar prancheta e carregar somente o conteúdo desta aba!
    layerGroup.innerHTML = '';

    if (targetPage.svgText) {
        importSVGContent(targetPage.svgText, true);
    } else if (targetPage.url) {
        loadPageImage(targetPage.url, targetPage.name);
    }
}

function loadPageImage(url, pageName) {
    const tempImg = new Image();
    tempImg.onload = () => {
        const layerGroup = document.getElementById('layerGroupMain');
        if (!layerGroup) return;
        layerGroup.innerHTML = ''; // Garante prancheta 100% limpa para a página atual

        let imgW = tempImg.naturalWidth || 800;
        let imgH = tempImg.naturalHeight || 600;

        let targetW = imgW;
        let targetH = imgH;
        if (targetW > state.docWidth * 0.95 || targetH > state.docHeight * 0.95) {
            const fitScale = Math.min((state.docWidth * 0.85) / targetW, (state.docHeight * 0.85) / targetH);
            targetW = Math.round(targetW * fitScale);
            targetH = Math.round(targetH * fitScale);
        }

        const posX = Math.round((state.docWidth - targetW) / 2);
        const posY = Math.round((state.docHeight - targetH) / 2);

        const img = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        img.setAttribute('href', url);
        img.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        img.setAttribute('x', posX.toString());
        img.setAttribute('y', posY.toString());
        img.setAttribute('width', targetW.toString());
        img.setAttribute('height', targetH.toString());

        layerGroup.appendChild(img);
        selectElement(img, false);
        updateLayersTree();
        updatePropertyBarVisibility();
        saveState(`Carregar ${pageName || 'Página'}`);
        toast(`${pageName || 'Página'} carregada com sucesso!`, 'ok');
    };
    tempImg.src = url;
}

function addNewPage() {
    const layerGroup = document.getElementById('layerGroupMain');
    if (state.activePageIndex >= 0 && state.cdrPages[state.activePageIndex] && layerGroup) {
        state.cdrPages[state.activePageIndex].svgContent = layerGroup.innerHTML;
    }
    const newIdx = state.cdrPages.length;
    state.cdrPages.push({
        id: newIdx,
        name: `Página ${newIdx + 1}`,
        svgContent: '',
        docWidth: state.docWidth,
        docHeight: state.docHeight
    });
    switchCDRPage(newIdx);
    toast(`Página ${newIdx + 1} criada!`, 'ok');
}

function importImageURL(url) {
    const tempImg = new Image();
    tempImg.onload = () => {
        const layerGroup = document.getElementById('layerGroupMain');
        if (!layerGroup) return;

        let imgW = tempImg.naturalWidth || 800;
        let imgH = tempImg.naturalHeight || 600;

        let targetW = imgW;
        let targetH = imgH;
        if (targetW > state.docWidth * 0.95 || targetH > state.docHeight * 0.95) {
            const fitScale = Math.min((state.docWidth * 0.85) / targetW, (state.docHeight * 0.85) / targetH);
            targetW = Math.round(targetW * fitScale);
            targetH = Math.round(targetH * fitScale);
        }

        const posX = Math.round((state.docWidth - targetW) / 2);
        const posY = Math.round((state.docHeight - targetH) / 2);

        const img = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        img.setAttribute('href', url);
        img.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        img.setAttribute('x', posX.toString());
        img.setAttribute('y', posY.toString());
        img.setAttribute('width', targetW.toString());
        img.setAttribute('height', targetH.toString());

        layerGroup.appendChild(img);
        selectElement(img, false);
        updateLayersTree();
        updatePropertyBarVisibility();
        saveState('Importar Imagem');
        toast('Imagem inserida na prancheta!', 'ok');
    };
    tempImg.src = url;
}

function importSVGContent(svgText) {
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(svgText, 'image/svg+xml');
        const svgEl = doc.querySelector('svg');
        if (!svgEl) {
            alert('SVG inválido.');
            return;
        }

        const layerGroup = document.getElementById('layerGroupMain');

        // Copiar defs / clipPath / filtros se existirem no SVG importado
        const defs = svgEl.querySelector('defs');
        if (defs) {
            const mainDefs = document.querySelector('#mainSvgCanvas defs');
            if (mainDefs) {
                Array.from(defs.children).forEach(d => mainDefs.appendChild(d.cloneNode(true)));
            }
        }

        // Analisar viewBox e dimensões do SVG
        let vbW = 0, vbH = 0, vbMinX = 0, vbMinY = 0;
        const vb = svgEl.getAttribute('viewBox');
        if (vb) {
            const parts = vb.trim().split(/[\s,]+/).map(Number);
            if (parts.length === 4) {
                vbMinX = parts[0];
                vbMinY = parts[1];
                vbW = parts[2];
                vbH = parts[3];
            }
        }

        // Limpar elementos fora do viewBox ou vazios (BoundingBox dummy do LibreOffice)
        if (vbW > 0 && vbH > 0) {
            const removeList = [];
            svgEl.querySelectorAll('*').forEach(el => {
                const tag = el.tagName.toLowerCase();
                const cls = el.getAttribute('class') || '';
                const id = el.getAttribute('id') || '';
                
                // Remover dummy rects e slide master
                if (tag === 'rect' && (cls.includes('BoundingBox') || (el.getAttribute('stroke') === 'none' && el.getAttribute('fill') === 'none'))) {
                    removeList.push(el);
                    return;
                }
                if (cls.includes('Master_Slide') || id === 'id2') {
                    removeList.push(el);
                    return;
                }
                
                // Elementos posicionados fora da prancheta (mesa de trabalho)
                const xVal = parseFloat(el.getAttribute('x'));
                if (!isNaN(xVal) && (xVal >= vbW * 1.02 || xVal < vbMinX - 10)) {
                    removeList.push(el);
                    return;
                }
            });
            removeList.forEach(el => { try { el.remove(); } catch(e) {} });
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

        let targetW = parseDim(svgEl.getAttribute('width'), vbW || 600);
        let targetH = parseDim(svgEl.getAttribute('height'), vbH || 600);

        let scaleFactor = 1;
        if (vbW > 0 && vbH > 0) {
            scaleFactor = targetW / vbW;
        }

        // Se o tamanho for desproporcional à prancheta, ajustar proporcionalmente
        if (targetW > state.docWidth * 0.95 || targetH > state.docHeight * 0.95) {
            const fitScale = Math.min((state.docWidth * 0.85) / targetW, (state.docHeight * 0.85) / targetH);
            scaleFactor *= fitScale;
            targetW *= fitScale;
            targetH *= fitScale;
        }

        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('class', 'imported-svg-group');

        // Centralizar na prancheta
        const posX = Math.round((state.docWidth - targetW) / 2);
        const posY = Math.round((state.docHeight - targetH) / 2);

        if (Math.abs(scaleFactor - 1) > 0.001) {
            g.setAttribute('transform', `translate(${posX}, ${posY}) scale(${scaleFactor})`);
        } else {
            g.setAttribute('transform', `translate(${posX}, ${posY})`);
        }

        // Desempacotar wrappers transparentes de nível único
        function extractVisualNodes(node) {
            const list = Array.from(node.children).filter(el => !['defs', 'style', 'metadata'].includes(el.tagName.toLowerCase()));
            const result = [];
            list.forEach(item => {
                let curr = item;
                while (curr.tagName.toLowerCase() === 'g' && curr.children.length === 1 && curr.firstElementChild.tagName.toLowerCase() === 'g' && !curr.getAttribute('transform')) {
                    curr = curr.firstElementChild;
                }
                if (curr.classList && curr.classList.contains('Page')) {
                    Array.from(curr.children).forEach(pch => {
                        if (pch.tagName.toLowerCase() === 'g' && pch.children.length === 1 && !pch.getAttribute('transform')) {
                            const inner = pch.firstElementChild;
                            if (['path', 'image', 'text', 'rect', 'ellipse', 'polygon'].includes(inner.tagName.toLowerCase())) {
                                result.push(inner);
                                return;
                            }
                        }
                        result.push(pch);
                    });
                } else if (curr.tagName.toLowerCase() === 'g' && curr.children.length === 1 && !curr.getAttribute('transform')) {
                    const inner = curr.firstElementChild;
                    if (['path', 'image', 'text', 'rect', 'ellipse', 'polygon'].includes(inner.tagName.toLowerCase())) {
                        result.push(inner);
                    } else {
                        result.push(curr);
                    }
                } else {
                    result.push(curr);
                }
            });
            return result;
        }

        const visualNodes = extractVisualNodes(svgEl);
        visualNodes.forEach(child => g.appendChild(child.cloneNode(true)));

        layerGroup.appendChild(g);
        selectElement(g, false);

        // Se contiver imagem bitmap e vetores sobrepostos, ativar atalho de ocultar fundo
        const hasImg = g.querySelector('image') !== null;
        const hasPaths = g.querySelector('path') !== null;
        if (hasImg && hasPaths) {
            const btnBg = document.getElementById('btnToggleDuplicateBg');
            if (btnBg) btnBg.style.display = 'inline-flex';
            toast('Vetor importado! Se houver texto duplicado do modelo original na imagem de fundo, use o botão "Ocultar Fundo" na barra superior.', 'info');
        } else {
            toast('Vetor importado com sucesso em 100% de qualidade!', 'ok');
        }

        saveState('Importar Vetores (SVG/CDR/PDF)');
        updateLayersTree();
    } catch (err) {
        console.error(err);
        alert('Erro ao importar SVG: ' + err.message);
    }
}

function importJSONProject(jsonText) {
    try {
        const data = JSON.parse(jsonText);
        if (data.docWidth) state.docWidth = data.docWidth;
        if (data.docHeight) state.docHeight = data.docHeight;
        applyDocDimensions();

        const layerGroup = document.getElementById('layerGroupMain');
        if (layerGroup && data.svgContent) {
            layerGroup.innerHTML = data.svgContent;
        }
        deselectAll();
        saveState('Abrir Projeto JSON');
        fitToScreen();
        toast('Projeto carregado!', 'ok');
    } catch (err) {
        alert('Erro ao carregar projeto JSON: ' + err.message);
    }
}

// ==================== Exportação ====================
function exportDocument(format) {
    const svg = document.getElementById('mainSvgCanvas');
    if (!svg) return;

    // Deselect to remove selection handles from export
    deselectAll();

    if (format === 'svg') {
        const svgBlob = new Blob([svg.outerHTML], { type: 'image/svg+xml;charset=utf-8' });
        downloadFile(svgBlob, 'arte_corelclone_pro.svg');
        toast('SVG exportado com sucesso!', 'ok');
    } else if (format === 'png') {
        const canvas = document.createElement('canvas');
        canvas.width = state.docWidth;
        canvas.height = state.docHeight;
        const ctx = canvas.getContext('2d');

        const img = new Image();
        const svgBlob = new Blob([svg.outerHTML], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        img.onload = () => {
            ctx.drawImage(img, 0, 0);
            canvas.toBlob(blob => {
                downloadFile(blob, 'arte_corelclone_hd.png');
                toast('PNG HD exportado!', 'ok');
            });
        };
        img.src = url;
    } else if (format === 'json') {
        const projectData = {
            version: 'CorelClone Pro 2026',
            docWidth: state.docWidth,
            docHeight: state.docHeight,
            svgContent: document.getElementById('layerGroupMain')?.innerHTML || ''
        };
        const blob = new Blob([JSON.stringify(projectData, null, 2)], { type: 'application/json' });
        downloadFile(blob, 'projeto_corelclone.json');
        toast('Arquivo de projeto (.JSON) salvo!', 'ok');
    }
}

function downloadFile(blob, filename) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
}

// ==================== Zoom & Pan ====================
function fitToScreen() {
    const viewport = document.getElementById('canvasViewport');
    const board = document.getElementById('workspaceBoard');
    if (!viewport || !board) return;

    const availableW = viewport.clientWidth - 100;
    const availableH = viewport.clientHeight - 100;

    const scale = Math.min(availableW / state.docWidth, availableH / state.docHeight, 1.2);
    state.zoom = Math.round(scale * 100);

    board.style.transform = `scale(${scale})`;
    board.style.transformOrigin = 'center center';

    const zoomText = document.getElementById('zoomValText');
    if (zoomText) zoomText.textContent = `${state.zoom}%`;
}

function zoomIn() {
    state.zoom = Math.min(state.zoom + 20, 500);
    applyZoom();
}

function zoomOut() {
    state.zoom = Math.max(state.zoom - 20, 10);
    applyZoom();
}

function applyZoom() {
    const board = document.getElementById('workspaceBoard');
    if (!board) return;
    const scale = state.zoom / 100;
    board.style.transform = `scale(${scale})`;
    board.style.transformOrigin = 'center center';
    const zoomText = document.getElementById('zoomValText');
    if (zoomText) zoomText.textContent = `${state.zoom}%`;
}

// Mouse Wheel Zoom Support (Ctrl + Scroll)
document.addEventListener('wheel', (e) => {
    if (e.ctrlKey) {
        e.preventDefault();
        if (e.deltaY < 0) zoomIn(); else zoomOut();
    }
}, { passive: false });

// ==================== Réguas Interativas ====================
function drawRulers() {
    const canvasH = document.getElementById('canvasRulerH');
    const canvasV = document.getElementById('canvasRulerV');
    if (!canvasH || !canvasV) return;

    canvasH.width = window.innerWidth;
    canvasH.height = 24;
    canvasV.width = 24;
    canvasV.height = window.innerHeight;

    const ctxH = canvasH.getContext('2d');
    const ctxV = canvasV.getContext('2d');

    ctxH.fillStyle = '#0f172a'; ctxH.fillRect(0, 0, canvasH.width, 24);
    ctxV.fillStyle = '#0f172a'; ctxV.fillRect(0, 0, 24, canvasV.height);

    ctxH.fillStyle = '#64748b'; ctxH.font = '9px Inter';
    ctxV.fillStyle = '#64748b'; ctxV.font = '9px Inter';

    for (let x = 0; x < canvasH.width; x += 50) {
        ctxH.beginPath();
        ctxH.moveTo(x, 14); ctxH.lineTo(x, 24);
        ctxH.strokeStyle = '#334155'; ctxH.stroke();
        ctxH.fillText(x.toString(), x + 2, 12);
    }

    for (let y = 0; y < canvasV.height; y += 50) {
        ctxV.beginPath();
        ctxV.moveTo(14, y); ctxV.lineTo(24, y);
        ctxV.strokeStyle = '#334155'; ctxV.stroke();
        ctxV.fillText(y.toString(), 2, y + 10);
    }
}

function updateRulerCrosshairs(e) {
    // Optional mouse tracker on rulers
}

// ==================== Tabs do Inspector ====================
function switchInspectorTab(tab) {
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.inspector-content');

    tabs.forEach(t => t.classList.remove('active'));
    contents.forEach(c => c.classList.remove('active'));

    if (tab === 'layers') {
        tabs[0]?.classList.add('active');
        document.getElementById('tabContentLayers')?.classList.add('active');
    } else if (tab === 'align') {
        tabs[1]?.classList.add('active');
        document.getElementById('tabContentAlign')?.classList.add('active');
    } else if (tab === 'history') {
        tabs[2]?.classList.add('active');
        document.getElementById('tabContentHistory')?.classList.add('active');
    }
}

// ==================== Toast Notifications ====================
function toast(msg, type = 'ok') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const item = document.createElement('div');
    item.className = 'toast-item';
    const icon = type === 'ok' ? '<i class="fas fa-check-circle text-emerald-400"></i>' : '<i class="fas fa-exclamation-circle text-rose-400"></i>';
    item.innerHTML = `${icon} <span>${msg}</span>`;
    container.appendChild(item);
    setTimeout(() => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(10px)';
        item.style.transition = 'all 0.3s';
        setTimeout(() => item.remove(), 300);
    }, 2500);
}

// ==================== Motor Geométrico Paper.js & Operações Booleanas ====================
function initPaperEngine() {
    if (window.paper) {
        try {
            const virtualCanvas = document.createElement('canvas');
            virtualCanvas.width = state.docWidth || 1122;
            virtualCanvas.height = state.docHeight || 793;
            paper.setup(virtualCanvas);
        } catch (e) {
            console.warn('Paper.js init fallback:', e);
        }
    }
}

function weldSelected() {
    if (state.selectedElements.length < 2) {
        toast('Selecione pelo menos 2 objetos para soldar (Weld)', 'err');
        return;
    }
    executePaperBoolean('unite');
}

function trimSelected() {
    if (state.selectedElements.length < 2) {
        toast('Selecione pelo menos 2 objetos para aparar (Trim)', 'err');
        return;
    }
    executePaperBoolean('subtract');
}

function intersectSelected() {
    if (state.selectedElements.length < 2) {
        toast('Selecione pelo menos 2 objetos para interseção', 'err');
        return;
    }
    executePaperBoolean('intersect');
}

function excludeSelected() {
    if (state.selectedElements.length < 2) {
        toast('Selecione pelo menos 2 objetos para excluir sobreposição', 'err');
        return;
    }
    executePaperBoolean('exclude');
}

function executePaperBoolean(op) {
    if (!window.paper) {
        alert('Motor geométrico Paper.js não disponível.');
        return;
    }
    try {
        const layerGroup = document.getElementById('layerGroupMain');
        const elements = [...state.selectedElements];
        
        // Import elements into Paper.js project
        const paperItems = elements.map(el => {
            return paper.project.importSVG(el, { expandShapes: true });
        });

        let resultItem = paperItems[0];
        
        for (let i = 1; i < paperItems.length; i++) {
            const nextItem = paperItems[i];
            let combined = null;
            if (op === 'unite') {
                combined = resultItem.unite(nextItem);
            } else if (op === 'subtract') {
                combined = resultItem.subtract(nextItem);
            } else if (op === 'intersect') {
                combined = resultItem.intersect(nextItem);
            } else if (op === 'exclude') {
                combined = resultItem.exclude(nextItem);
            }
            if (combined) {
                resultItem.remove();
                nextItem.remove();
                resultItem = combined;
            }
        }

        const exportedSvg = resultItem.exportSVG({ asString: false });
        resultItem.remove();

        const baseEl = elements[0];
        const fill = baseEl.getAttribute('fill') || '#6366f1';
        const stroke = baseEl.getAttribute('stroke') || '#ffffff';
        const strokeWidth = baseEl.getAttribute('stroke-width') || '2';

        exportedSvg.setAttribute('fill', fill);
        exportedSvg.setAttribute('stroke', stroke);
        exportedSvg.setAttribute('stroke-width', strokeWidth);

        // Replace original elements
        elements.forEach(el => el.remove());
        layerGroup.appendChild(exportedSvg);

        selectElement(exportedSvg, false);

        const opLabels = { unite: 'Soldar', subtract: 'Aparar', intersect: 'Interseção', exclude: 'Excluir Sobreposição' };
        saveState(`${opLabels[op] || 'Modelagem'} de Objetos`);
        toast(`Operação "${opLabels[op]}" concluída com sucesso!`, 'ok');
    } catch (err) {
        console.error('Erro na operação booleana:', err);
        toast('Erro ao processar modelagem: ' + err.message, 'err');
    }
}

// ==================== PowerTRACE™ (Vetorizador de Bitmap & Previews CDR) ====================
function openPowerTraceDialog() {
    const modal = document.getElementById('powertraceModal');
    if (modal) modal.style.display = 'flex';
}

function closePowerTraceDialog() {
    const modal = document.getElementById('powertraceModal');
    if (modal) modal.style.display = 'none';
}

function runPowerTrace() {
    if (!window.ImageTracer) {
        alert('Biblioteca ImageTracerJS não disponível.');
        return;
    }

    let targetImage = null;
    if (state.selectedElements.length > 0 && state.selectedElements[0].tagName.toLowerCase() === 'image') {
        targetImage = state.selectedElements[0];
    } else {
        targetImage = document.querySelector('#layerGroupMain image');
    }

    if (!targetImage) {
        toast('Selecione uma imagem ou preview CDR para rastrear com PowerTRACE.', 'err');
        closePowerTraceDialog();
        return;
    }

    const imgUrl = targetImage.getAttribute('href') || targetImage.getAttribute('xlink:href');
    if (!imgUrl) {
        toast('URL da imagem não encontrada.', 'err');
        closePowerTraceDialog();
        return;
    }

    const presetRadio = document.querySelector('input[name="tracePreset"]:checked');
    const presetVal = presetRadio ? presetRadio.value : 'posterized2';
    const removeOrig = document.getElementById('traceRemoveOriginal')?.checked ?? true;

    toast('⚡ PowerTRACE vetorizando arte...', 'ok');
    closePowerTraceDialog();

    let traceOptions = {
        corsenabled: true,
        ltres: 0.8,
        qtres: 0.8,
        pathomit: 4,
        colorsampling: 2,
        numberofcolors: 16,
        colorquantcycles: 4,
        blurradius: 1,
        blurdelta: 15,
        scale: 1,
        viewbox: false
    };

    if (presetVal === 'detailed') {
        traceOptions.numberofcolors = 32;
        traceOptions.pathomit = 2;
        traceOptions.ltres = 0.4;
        traceOptions.qtres = 0.4;
        traceOptions.blurradius = 0;
    } else if (presetVal === 'posterized1') {
        traceOptions.numberofcolors = 4;
        traceOptions.pathomit = 12;
        traceOptions.ltres = 1.2;
        traceOptions.qtres = 1.2;
        traceOptions.blurradius = 2;
    }

    try {
        const prepImg = new Image();
        prepImg.crossOrigin = 'Anonymous';
        prepImg.onload = () => {
            let traceSource = imgUrl;
            // Supersampling / Anti-aliasing para imagens pequenas (evita serrilhado)
            if (prepImg.naturalWidth < 800 || prepImg.naturalHeight < 800) {
                const factor = Math.max(2, Math.min(4, Math.round(1600 / Math.max(prepImg.naturalWidth, 1))));
                const offCanvas = document.createElement('canvas');
                offCanvas.width = prepImg.naturalWidth * factor;
                offCanvas.height = prepImg.naturalHeight * factor;
                const offCtx = offCanvas.getContext('2d');
                offCtx.imageSmoothingEnabled = true;
                offCtx.imageSmoothingQuality = 'high';
                offCtx.drawImage(prepImg, 0, 0, offCanvas.width, offCanvas.height);
                traceSource = offCanvas.toDataURL('image/png');
            }

            ImageTracer.imageToSVG(traceSource, (svgstr) => {
                if (!svgstr) {
                    toast('PowerTRACE: não foi possível gerar curvas.', 'err');
                    return;
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(svgstr, 'image/svg+xml');
                const tracedSvg = doc.querySelector('svg');
                if (!tracedSvg) {
                    toast('Erro ao analisar SVG gerado.', 'err');
                    return;
                }

                const imgX = parseFloat(targetImage.getAttribute('x') || 0);
                const imgY = parseFloat(targetImage.getAttribute('y') || 0);
                const imgW = parseFloat(targetImage.getAttribute('width') || 800);
                const imgH = parseFloat(targetImage.getAttribute('height') || 600);

                const origTraceW = parseFloat(tracedSvg.getAttribute('width')) || imgW;
                const origTraceH = parseFloat(tracedSvg.getAttribute('height')) || imgH;
                const scaleX = imgW / origTraceW;
                const scaleY = imgH / origTraceH;

                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                g.setAttribute('class', 'traced-vector-group');
                g.setAttribute('transform', `translate(${imgX}, ${imgY}) scale(${scaleX}, ${scaleY})`);

                Array.from(tracedSvg.children).forEach(child => {
                    if (!['defs', 'metadata', 'style'].includes(child.tagName.toLowerCase())) {
                        g.appendChild(child.cloneNode(true));
                    }
                });

                const layerGroup = document.getElementById('layerGroupMain');
                layerGroup.appendChild(g);

                if (removeOrig) {
                    targetImage.remove();
                }

                selectElement(g, false);
                saveState('PowerTRACE — Vetorização Suavizada');
                toast('⚡ Imagem vetorizada com suavização anti-serrilhado!', 'ok');
            }, traceOptions);
        };
        prepImg.onerror = () => {
            // Fallback direto
            ImageTracer.imageToSVG(imgUrl, (svgstr) => {
                if (!svgstr) return;
                const parser = new DOMParser();
                const doc = parser.parseFromString(svgstr, 'image/svg+xml');
                const tracedSvg = doc.querySelector('svg');
                if (!tracedSvg) return;
                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                Array.from(tracedSvg.children).forEach(child => g.appendChild(child.cloneNode(true)));
                document.getElementById('layerGroupMain').appendChild(g);
                selectElement(g, false);
                saveState('PowerTRACE');
            }, traceOptions);
        };
        prepImg.src = imgUrl;
    } catch (err) {
        console.error('PowerTRACE Error:', err);
        toast('Erro no PowerTRACE: ' + err.message, 'err');
    }
}

// ==================== Ferramenta Forma (F10) & Motor de Nós Bézier ====================
function convertToCurvesSelected() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto para converter em curvas (Ctrl+Q)', 'err');
        return;
    }

    let count = 0;
    const newSelected = [];

    state.selectedElements.forEach(el => {
        const tag = el.tagName.toLowerCase();
        if (tag === 'path') {
            newSelected.push(el);
            return;
        }
        const path = convertElementToPath(el);
        if (path) {
            el.replaceWith(path);
            newSelected.push(path);
            count++;
        } else {
            newSelected.push(el);
        }
    });

    state.selectedElements = newSelected;

    if (count > 0) {
        saveState('Converter em Curvas (Ctrl+Q)');
        if (state.activeTool === 'node') {
            initNodeEditingForSelected();
        } else {
            renderSelectionOverlay();
        }
        updateLayersTree();
        toast(`${count} objeto(s) convertido(s) em Curvas (Ctrl+Q)!`, 'ok');
    }
}

function convertElementToPath(el) {
    const tag = el.tagName.toLowerCase();
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

    ['fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'transform', 'filter', 'opacity', 'class'].forEach(attr => {
        if (el.hasAttribute(attr)) path.setAttribute(attr, el.getAttribute(attr));
    });

    if (tag === 'rect') {
        const x = parseFloat(el.getAttribute('x') || 0);
        const y = parseFloat(el.getAttribute('y') || 0);
        const w = parseFloat(el.getAttribute('width') || 100);
        const h = parseFloat(el.getAttribute('height') || 100);
        const rx = parseFloat(el.getAttribute('rx') || 0);
        if (rx > 0) {
            const r = Math.min(rx, w / 2, h / 2);
            path.setAttribute('d', `M ${x + r} ${y} L ${x + w - r} ${y} Q ${x + w} ${y} ${x + w} ${y + r} L ${x + w} ${y + h - r} Q ${x + w} ${y + h} ${x + w - r} ${y + h} L ${x + r} ${y + h} Q ${x} ${y + h} ${x} ${y + h - r} L ${x} ${y + r} Q ${x} ${y} ${x + r} ${y} Z`);
        } else {
            path.setAttribute('d', `M ${x} ${y} L ${x + w} ${y} L ${x + w} ${y + h} L ${x} ${y + h} Z`);
        }
        return path;
    } else if (tag === 'ellipse' || tag === 'circle') {
        const cx = parseFloat(el.getAttribute('cx') || 0);
        const cy = parseFloat(el.getAttribute('cy') || 0);
        const rx = parseFloat(el.getAttribute('rx') || el.getAttribute('r') || 50);
        const ry = parseFloat(el.getAttribute('ry') || el.getAttribute('r') || 50);
        const k = 0.552284749831;
        const kx = rx * k, ky = ry * k;
        path.setAttribute('d', `M ${cx} ${cy - ry} C ${cx + kx} ${cy - ry} ${cx + rx} ${cy - ky} ${cx + rx} ${cy} C ${cx + rx} ${cy + ky} ${cx + kx} ${cy + ry} ${cx} ${cy + ry} C ${cx - kx} ${cy + ry} ${cx - rx} ${cy + ky} ${cx - rx} ${cy} C ${cx - rx} ${cy - ky} ${cx - kx} ${cy - ry} ${cx} ${cy - ry} Z`);
        return path;
    } else if (tag === 'polygon') {
        const pts = (el.getAttribute('points') || '').trim().split(/[\s,]+/);
        if (pts.length >= 4) {
            let d = `M ${pts[0]} ${pts[1]}`;
            for (let i = 2; i < pts.length; i += 2) {
                d += ` L ${pts[i]} ${pts[i + 1]}`;
            }
            d += ' Z';
            path.setAttribute('d', d);
            return path;
        }
    }
    return null;
}

function parseSvgPathToNodes(d) {
    if (!d) return [];
    const tokens = d.match(/[a-df-z]|[-+]?(?:\d*\.\d+|\d+)(?:[eE][-+]?\d+)?/gi) || [];
    const nodes = [];
    let i = 0;
    let currX = 0, currY = 0;
    let startX = 0, startY = 0;

    while (i < tokens.length) {
        const token = tokens[i];
        if (/^[a-df-z]$/i.test(token)) {
            const cmd = token;
            i++;
            if (cmd === 'M' || cmd === 'm') {
                let x = parseFloat(tokens[i++]);
                let y = parseFloat(tokens[i++]);
                if (cmd === 'm') { x += currX; y += currY; }
                currX = x; currY = y;
                startX = x; startY = y;
                nodes.push({ cmd: 'M', x, y });
                while (i < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let lx = parseFloat(tokens[i++]);
                    let ly = parseFloat(tokens[i++]);
                    if (cmd === 'm') { lx += currX; ly += currY; }
                    currX = lx; currY = ly;
                    nodes.push({ cmd: 'L', x: lx, y: ly });
                }
            } else if (cmd === 'L' || cmd === 'l') {
                while (i < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x = parseFloat(tokens[i++]);
                    let y = parseFloat(tokens[i++]);
                    if (cmd === 'l') { x += currX; y += currY; }
                    currX = x; currY = y;
                    nodes.push({ cmd: 'L', x, y });
                }
            } else if (cmd === 'H' || cmd === 'h') {
                while (i < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x = parseFloat(tokens[i++]);
                    if (cmd === 'h') x += currX;
                    currX = x;
                    nodes.push({ cmd: 'L', x, y: currY });
                }
            } else if (cmd === 'V' || cmd === 'v') {
                while (i < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let y = parseFloat(tokens[i++]);
                    if (cmd === 'v') y += currY;
                    currY = y;
                    nodes.push({ cmd: 'L', x: currX, y });
                }
            } else if (cmd === 'C' || cmd === 'c') {
                while (i + 5 < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x1 = parseFloat(tokens[i++]);
                    let y1 = parseFloat(tokens[i++]);
                    let x2 = parseFloat(tokens[i++]);
                    let y2 = parseFloat(tokens[i++]);
                    let x  = parseFloat(tokens[i++]);
                    let y  = parseFloat(tokens[i++]);
                    if (cmd === 'c') {
                        x1 += currX; y1 += currY;
                        x2 += currX; y2 += currY;
                        x  += currX; y  += currY;
                    }
                    currX = x; currY = y;
                    nodes.push({ cmd: 'C', x, y, cp1: { x: x1, y: y1 }, cp2: { x: x2, y: y2 } });
                }
            } else if (cmd === 'Q' || cmd === 'q') {
                while (i + 3 < tokens.length && !/^[a-df-z]$/i.test(tokens[i])) {
                    let x1 = parseFloat(tokens[i++]);
                    let y1 = parseFloat(tokens[i++]);
                    let x  = parseFloat(tokens[i++]);
                    let y  = parseFloat(tokens[i++]);
                    if (cmd === 'q') {
                        x1 += currX; y1 += currY;
                        x  += currX; y  += currY;
                    }
                    let cx1 = currX + (2/3) * (x1 - currX);
                    let cy1 = currY + (2/3) * (y1 - currY);
                    let cx2 = x + (2/3) * (x1 - x);
                    let cy2 = y + (2/3) * (y1 - y);
                    currX = x; currY = y;
                    nodes.push({ cmd: 'C', x, y, cp1: { x: cx1, y: cy1 }, cp2: { x: cx2, y: cy2 } });
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
        if (n.cmd === 'M') {
            d += `M ${Math.round(n.x * 10) / 10} ${Math.round(n.y * 10) / 10} `;
        } else if (n.cmd === 'L') {
            d += `L ${Math.round(n.x * 10) / 10} ${Math.round(n.y * 10) / 10} `;
        } else if (n.cmd === 'C') {
            d += `C ${Math.round(n.cp1.x * 10) / 10} ${Math.round(n.cp1.y * 10) / 10} ${Math.round(n.cp2.x * 10) / 10} ${Math.round(n.cp2.y * 10) / 10} ${Math.round(n.x * 10) / 10} ${Math.round(n.y * 10) / 10} `;
        } else if (n.cmd === 'Z') {
            d += 'Z ';
        }
    });
    return d.trim();
}

function initNodeEditingForSelected() {
    if (state.selectedElements.length === 0) {
        exitNodeEditing();
        return;
    }

    let el = state.selectedElements[0];
    if (el.tagName.toLowerCase() !== 'path') {
        const path = convertElementToPath(el);
        if (path) {
            el.replaceWith(path);
            el = path;
            state.selectedElements = [el];
            saveState('Converter em Curvas (F10)');
        } else {
            return;
        }
    }

    state.nodeEdit.element = el;
    state.nodeEdit.commands = parseSvgPathToNodes(el.getAttribute('d') || '');
    state.nodeEdit.activeNodeIndex = 0;

    renderNodeEditOverlay();
}

function exitNodeEditing() {
    state.nodeEdit.element = null;
    state.nodeEdit.commands = [];
    state.nodeEdit.activeNodeIndex = -1;
    const overlay = document.getElementById('nodeEditOverlay');
    if (overlay) overlay.innerHTML = '';
}

function renderNodeEditOverlay() {
    const overlay = document.getElementById('nodeEditOverlay');
    if (!overlay) return;
    overlay.innerHTML = '';

    if (!state.nodeEdit.element || state.nodeEdit.commands.length === 0) return;

    const selOverlay = document.getElementById('selectionOverlay');
    if (selOverlay) selOverlay.innerHTML = '';

    const nodes = state.nodeEdit.commands;

    // Render tangent lines and handles for cubic bezier curves
    nodes.forEach((n, idx) => {
        if (n.cmd === 'C' && (idx === state.nodeEdit.activeNodeIndex || idx === state.nodeEdit.activeNodeIndex + 1)) {
            const prevNode = nodes[idx - 1] || nodes[0];
            
            // Tangent Line 1: from prevNode to cp1
            const line1 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line1.setAttribute('class', 'node-handle-line');
            line1.setAttribute('x1', prevNode.x.toString());
            line1.setAttribute('y1', prevNode.y.toString());
            line1.setAttribute('x2', n.cp1.x.toString());
            line1.setAttribute('y2', n.cp1.y.toString());
            overlay.appendChild(line1);

            // Control Handle 1
            const c1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            c1.setAttribute('class', 'node-control-point');
            c1.setAttribute('data-node-index', idx.toString());
            c1.setAttribute('data-handle-type', 'cp1');
            c1.setAttribute('cx', n.cp1.x.toString());
            c1.setAttribute('cy', n.cp1.y.toString());
            c1.setAttribute('r', '4');
            overlay.appendChild(c1);

            // Tangent Line 2: from node to cp2
            const line2 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line2.setAttribute('class', 'node-handle-line');
            line2.setAttribute('x1', n.x.toString());
            line2.setAttribute('y1', n.y.toString());
            line2.setAttribute('x2', n.cp2.x.toString());
            line2.setAttribute('y2', n.cp2.y.toString());
            overlay.appendChild(line2);

            // Control Handle 2
            const c2 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            c2.setAttribute('class', 'node-control-point');
            c2.setAttribute('data-node-index', idx.toString());
            c2.setAttribute('data-handle-type', 'cp2');
            c2.setAttribute('cx', n.cp2.x.toString());
            c2.setAttribute('cy', n.cp2.y.toString());
            c2.setAttribute('r', '4');
            overlay.appendChild(c2);
        }
    });

    // Render anchor squares
    nodes.forEach((n, idx) => {
        if (n.cmd === 'Z') return;
        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        const isSel = idx === state.nodeEdit.activeNodeIndex;
        rect.setAttribute('class', `node-anchor ${isSel ? 'selected' : ''}`);
        rect.setAttribute('data-node-index', idx.toString());
        rect.setAttribute('x', (n.x - 4).toString());
        rect.setAttribute('y', (n.y - 4).toString());
        rect.setAttribute('width', '8');
        rect.setAttribute('height', '8');
        overlay.appendChild(rect);
    });
}

function startNodeDrag(nodeIndex, handleType, pt) {
    state.nodeEdit.activeNodeIndex = nodeIndex;
    const cmd = state.nodeEdit.commands[nodeIndex];
    if (!cmd) return;

    state.nodeDrag = {
        nodeIndex: nodeIndex,
        handleType: handleType,
        startPt: pt,
        origX: cmd.x,
        origY: cmd.y,
        origCp1: cmd.cp1 ? { ...cmd.cp1 } : null,
        origCp2: cmd.cp2 ? { ...cmd.cp2 } : null
    };

    renderNodeEditOverlay();
}

function handleNodeDragMove(pt, e) {
    const d = state.nodeDrag;
    if (!d) return;

    const dx = pt.x - d.startPt.x;
    const dy = pt.y - d.startPt.y;
    const cmd = state.nodeEdit.commands[d.nodeIndex];
    if (!cmd) return;

    if (d.handleType === 'node') {
        cmd.x = Math.round(d.origX + dx);
        cmd.y = Math.round(d.origY + dy);
        if (cmd.cp2 && d.origCp2) {
            cmd.cp2.x = Math.round(d.origCp2.x + dx);
            cmd.cp2.y = Math.round(d.origCp2.y + dy);
        }
        const next = state.nodeEdit.commands[d.nodeIndex + 1];
        if (next && next.cmd === 'C' && next.cp1) {
            next.cp1.x = Math.round(next.cp1.x + dx);
            next.cp1.y = Math.round(next.cp1.y + dy);
        }
    } else if (d.handleType === 'cp1' && cmd.cp1 && d.origCp1) {
        cmd.cp1.x = Math.round(d.origCp1.x + dx);
        cmd.cp1.y = Math.round(d.origCp1.y + dy);
    } else if (d.handleType === 'cp2' && cmd.cp2 && d.origCp2) {
        cmd.cp2.x = Math.round(d.origCp2.x + dx);
        cmd.cp2.y = Math.round(d.origCp2.y + dy);
    }

    const newD = rebuildSvgPathFromNodes(state.nodeEdit.commands);
    state.nodeEdit.element.setAttribute('d', newD);
    renderNodeEditOverlay();
}

function finishNodeDrag() {
    if (state.nodeDrag) {
        state.nodeDrag = null;
        saveState('Mover Nó Bézier (F10)');
        renderNodeEditOverlay();
    }
}

function addNodeAtPoint(pt) {
    if (!state.nodeEdit.element || state.nodeEdit.commands.length === 0) return;
    const nodes = state.nodeEdit.commands;
    let closestIdx = 0;
    let minDist = Infinity;

    nodes.forEach((n, idx) => {
        const dist = Math.hypot(n.x - pt.x, n.y - pt.y);
        if (dist < minDist) {
            minDist = dist;
            closestIdx = idx;
        }
    });

    const newNode = { cmd: 'L', x: Math.round(pt.x), y: Math.round(pt.y) };
    nodes.splice(closestIdx + 1, 0, newNode);
    state.nodeEdit.activeNodeIndex = closestIdx + 1;

    const newD = rebuildSvgPathFromNodes(nodes);
    state.nodeEdit.element.setAttribute('d', newD);
    renderNodeEditOverlay();
    saveState('Adicionar Nó');
    toast('Nó adicionado ao caminho!', 'ok');
}

function addNodeToActiveSegment() {
    if (!state.nodeEdit.element || state.nodeEdit.commands.length === 0) {
        toast('Selecione um objeto com a ferramenta Forma (F10)', 'err');
        return;
    }
    const idx = state.nodeEdit.activeNodeIndex;
    const nodes = state.nodeEdit.commands;
    const curr = nodes[idx] || nodes[0];
    const next = nodes[idx + 1] || nodes[0];

    const midX = Math.round((curr.x + next.x) / 2);
    const midY = Math.round((curr.y + next.y) / 2);

    const newNode = { cmd: 'L', x: midX, y: midY };
    nodes.splice(idx + 1, 0, newNode);
    state.nodeEdit.activeNodeIndex = idx + 1;

    const newD = rebuildSvgPathFromNodes(nodes);
    state.nodeEdit.element.setAttribute('d', newD);
    renderNodeEditOverlay();
    saveState('Adicionar Nó');
    toast('Nó adicionado no ponto médio!', 'ok');
}

function deleteSelectedNode() {
    if (!state.nodeEdit.element || state.nodeEdit.commands.length <= 2) {
        toast('Um caminho precisa de pelo menos 2 nós.', 'err');
        return;
    }
    const idx = state.nodeEdit.activeNodeIndex;
    if (idx < 0 || idx >= state.nodeEdit.commands.length) return;

    state.nodeEdit.commands.splice(idx, 1);
    state.nodeEdit.activeNodeIndex = Math.max(0, idx - 1);

    const newD = rebuildSvgPathFromNodes(state.nodeEdit.commands);
    state.nodeEdit.element.setAttribute('d', newD);
    renderNodeEditOverlay();
    saveState('Excluir Nó');
    toast('Nó excluído!', 'ok');
}

function toggleNodeSmoothness() {
    if (!state.nodeEdit.element || state.nodeEdit.commands.length === 0) return;
    const idx = state.nodeEdit.activeNodeIndex;
    const cmd = state.nodeEdit.commands[idx];
    if (!cmd) return;

    if (cmd.cmd === 'C') {
        cmd.cmd = 'L';
        delete cmd.cp1;
        delete cmd.cp2;
        toast('Nó convertido em Canto Cúspide (Linha)', 'ok');
    } else if (cmd.cmd === 'L') {
        cmd.cmd = 'C';
        const prev = state.nodeEdit.commands[idx - 1] || { x: cmd.x - 40, y: cmd.y };
        cmd.cp1 = { x: Math.round(prev.x + (cmd.x - prev.x) * 0.33), y: Math.round(prev.y - 20) };
        cmd.cp2 = { x: Math.round(prev.x + (cmd.x - prev.x) * 0.66), y: Math.round(cmd.y + 20) };
        toast('Nó convertido em Curva Suave Bézier', 'ok');
    }

    const newD = rebuildSvgPathFromNodes(state.nodeEdit.commands);
    state.nodeEdit.element.setAttribute('d', newD);
    renderNodeEditOverlay();
    saveState('Alternar Tipo de Nó');
}

// ==================== Linhas-Guia & Réguas Interativas ====================
function initRulerGuidelineDrag() {
    const rulerH = document.getElementById('rulerHorizontal');
    const rulerV = document.getElementById('rulerVertical');

    if (rulerH) {
        rulerH.addEventListener('mousedown', (e) => {
            e.preventDefault();
            startNewGuideline('h', e);
        });
    }

    if (rulerV) {
        rulerV.addEventListener('mousedown', (e) => {
            e.preventDefault();
            startNewGuideline('v', e);
        });
    }
}

function startNewGuideline(orientation, e) {
    const pt = getSvgCoords(e);
    const newGuide = {
        id: 'guide_' + Date.now(),
        orientation: orientation,
        pos: orientation === 'h' ? Math.round(pt.y) : Math.round(pt.x)
    };
    state.guidelines.push(newGuide);
    state.selectedGuideline = newGuide.id;
    renderGuidelines();

    const onMouseMove = (moveEvent) => {
        const curPt = getSvgCoords(moveEvent);
        newGuide.pos = orientation === 'h' ? Math.round(curPt.y) : Math.round(curPt.x);
        renderGuidelines();
    };

    const onMouseUp = () => {
        window.removeEventListener('mousemove', onMouseMove);
        window.removeEventListener('mouseup', onMouseUp);
        saveState('Adicionar Linha-Guia');
        toast(`Linha-guia fixada em ${newGuide.pos}px`, 'ok');
    };

    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', onMouseUp);
}

function renderGuidelines() {
    const group = document.getElementById('guidelinesGroup');
    if (!group) return;
    group.innerHTML = '';

    state.guidelines.forEach(guide => {
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        const isSel = state.selectedGuideline === guide.id;
        line.setAttribute('class', `guideline-line guideline-${guide.orientation} ${isSel ? 'guideline-active' : ''}`);
        line.setAttribute('data-guide-id', guide.id);

        if (guide.orientation === 'h') {
            line.setAttribute('x1', '0');
            line.setAttribute('y1', guide.pos.toString());
            line.setAttribute('x2', state.docWidth.toString());
            line.setAttribute('y2', guide.pos.toString());
        } else {
            line.setAttribute('x1', guide.pos.toString());
            line.setAttribute('y1', '0');
            line.setAttribute('x2', guide.pos.toString());
            line.setAttribute('y2', state.docHeight.toString());
        }

        line.addEventListener('mousedown', (e) => {
            e.stopPropagation();
            state.selectedGuideline = guide.id;
            renderGuidelines();

            const moveHandler = (me) => {
                const pt = getSvgCoords(me);
                guide.pos = guide.orientation === 'h' ? Math.round(pt.y) : Math.round(pt.x);
                renderGuidelines();
            };

            const upHandler = () => {
                window.removeEventListener('mousemove', moveHandler);
                window.removeEventListener('mouseup', upHandler);
                saveState('Mover Linha-Guia');
            };

            window.addEventListener('mousemove', moveHandler);
            window.addEventListener('mouseup', upHandler);
        });

        group.appendChild(line);
    });
}

function deleteSelectedGuideline() {
    if (!state.selectedGuideline) return;
    state.guidelines = state.guidelines.filter(g => g.id !== state.selectedGuideline);
    state.selectedGuideline = null;
    renderGuidelines();
    saveState('Excluir Linha-Guia');
    toast('Linha-guia excluída!', 'ok');
}

function centerSelectedToPage() {
    if (state.selectedElements.length === 0) {
        toast('Selecione um objeto para centralizar na prancheta (P)', 'err');
        return;
    }
    alignSelected('center');
    alignSelected('middle');
    toast('Objeto centralizado na página (P)', 'ok');
}

// Expose globals for HTML inline event handlers
window.newDocument = newDocument;
window.changeDocPreset = changeDocPreset;
window.exportDocument = exportDocument;
window.undo = undo;
window.redo = redo;
window.groupSelected = groupSelected;
window.ungroupSelected = ungroupSelected;
window.duplicateSelected = duplicateSelected;
window.deleteSelected = deleteSelected;
window.fitToScreen = fitToScreen;
window.zoomIn = zoomIn;
window.zoomOut = zoomOut;
window.toggleAspectLock = toggleAspectLock;
window.setNoFill = setNoFill;
window.setNoStroke = setNoStroke;
window.toggleTextBold = toggleTextBold;
window.toggleTextItalic = toggleTextItalic;
window.alignSelected = alignSelected;
window.orderSelected = orderSelected;
window.switchInspectorTab = switchInspectorTab;
window.switchCDRPage = switchCDRPage;
window.clearAllObjects = clearAllObjects;
window.deleteSingleObject = deleteSingleObject;

// New CorelDRAW Killer Tools exports
window.weldSelected = weldSelected;
window.trimSelected = trimSelected;
window.intersectSelected = intersectSelected;
window.excludeSelected = excludeSelected;
window.openPowerTraceDialog = openPowerTraceDialog;
window.closePowerTraceDialog = closePowerTraceDialog;
window.runPowerTrace = runPowerTrace;
window.convertToCurvesSelected = convertToCurvesSelected;
window.addNodeToActiveSegment = addNodeToActiveSegment;
window.deleteSelectedNode = deleteSelectedNode;
window.toggleNodeSmoothness = toggleNodeSmoothness;
window.centerSelectedToPage = centerSelectedToPage;
window.deleteSelectedGuideline = deleteSelectedGuideline;
window.renderGuidelines = renderGuidelines;
window.toggleElementVisibility = toggleElementVisibility;
window.toggleImportedBgImage = toggleImportedBgImage;
window.ungroupAll = ungroupAll;
window.addNewPage = addNewPage;

