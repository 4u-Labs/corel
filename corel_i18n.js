/**
 * CorelClone Pro 2026 — Internationalization (i18n) Engine
 * Full bilingual support: Portuguese (PT) & English (EN)
 * Version: 2.1.0
 */

const COREL_I18N = {
    pt: {
        // App Title & Meta
        app_title: 'CorelClone Pro 2026 (64-Bit)',
        app_title_template: 'CorelClone Pro 2026 (64-Bit) — [{name}] @ {zoom}%',
        doc_default_name: 'Documento 1',
        tab_start_page: 'Tela Inicial',
        btn_download_app: 'Baixar App',
        btn_download_app_title: 'Baixar aplicativo para Computador (Windows, Mac ou Linux)',
        welcome_toast: 'CorelClone Pro 2026 pronto para criação vetorial!',
        lang_switched: 'Idioma alterado para Português.',

        // Top Menu - Arquivo / File
        menu_file: 'Arquivo',
        menu_new: 'Novo...',
        menu_open: 'Abrir...',
        menu_open_cdr: 'Importar Corel (.CDR)...',
        menu_open_pdf: 'Importar PDF...',
        menu_save_svg: 'Salvar',
        menu_export_svg: 'Exportar SVG...',
        menu_export_pdf: 'Exportar PDF...',
        menu_export_png: 'Exportar PNG...',
        menu_prepress: 'Pré-impressão e Sangria...',
        menu_print: 'Imprimir...',
        menu_download_desktop: 'Baixar App para Computador...',

        // Top Menu - Editar / Edit
        menu_edit: 'Editar',
        menu_undo: 'Desfazer',
        menu_redo: 'Refazer',
        menu_cut: 'Recortar',
        menu_copy: 'Copiar',
        menu_paste: 'Colar',
        menu_delete: 'Excluir',
        menu_duplicate: 'Duplicar',
        menu_repeat: 'Repetir com Passo',
        menu_select_all: 'Selecionar Todos',

        // Top Menu - Exibir / View
        menu_view: 'Exibir',
        menu_zoom_in: 'Aumentar Zoom (+)',
        menu_zoom_out: 'Diminuir Zoom (-)',
        menu_zoom_100: 'Zoom Real 100% (1:1)',
        menu_zoom_page: 'Enquadrar Prancheta na Tela',
        menu_rulers: 'Exibir Réguas Calibradas',
        menu_guidelines: 'Exibir Linhas-Guia Magnéticas',
        menu_clear_guides: 'Limpar Todas as Linhas-Guia',

        // Top Menu - Objeto / Object
        menu_object: 'Objeto',
        menu_group: 'Agrupar Objetos',
        menu_ungroup: 'Desagrupar Objeto',
        menu_order: 'Ordenar Camada',
        menu_bring_front: 'Trazer para Frente',
        menu_send_back: 'Enviar para Trás',
        menu_forward: 'Avançar Um Nível',
        menu_backward: 'Recuar Um Nível',
        menu_align: 'Alinhar & Distribuir',
        menu_align_center_h: 'Centralizar Horizontal',
        menu_align_center_v: 'Centralizar Vertical',
        menu_align_left: 'Alinhar à Esquerda',
        menu_align_right: 'Alinhar à Direita',
        menu_align_top: 'Alinhar pelo Topo',
        menu_align_bottom: 'Alinhar pela Base',
        menu_align_page: 'Centralizar na Folha',
        menu_flip_h: 'Espelhar Horizontalmente',
        menu_flip_v: 'Espelhar Verticalmente',
        menu_powerclip: 'Recipiente PowerClip™',
        menu_pc_place: 'Colocar no Recipiente...',
        menu_pc_extract: 'Extrair Conteúdo do Recipiente',

        // Top Menu - Modelar / Effects
        menu_effects: 'Modelar',
        menu_contour: 'Ferramenta de Contorno (Linha de Corte)...',
        menu_shadow: 'Sombra Projetada Suave (Drop Shadow)...',
        menu_gradient: 'Preenchimento Gradiente (Fountain Fill)...',
        menu_text_path: 'Ajustar Texto ao Caminho...',
        menu_qrcode: 'Inserir QR Code Vetorial SVG...',

        // Top Menu - Bitmap
        menu_bitmap: 'Bitmap',
        menu_trace: 'PowerTRACE: Vetorizar Imagem...',
        menu_toggle_bg: 'Ocultar / Exibir Imagem de Fundo',

        // Top Menu - Ajuda / Help
        menu_help: 'Ajuda',
        menu_tutorial: 'Tutorial & Guia Completo...',
        menu_shortcuts: 'Guia de Teclas de Atalho Corel',
        menu_support: 'Suporte Técnico & FAQ',
        menu_terms: 'Termos de Serviço',
        menu_privacy: 'Política de Privacidade',
        menu_about: 'Sobre o CorelClone Pro',

        // Action Toolbar (Level 3)
        act_new_doc: 'Novo Documento (Ctrl+N)',
        act_open: 'Abrir Arquivo (.CDR, .PDF, .SVG, Imagem)',
        act_save_svg: 'Salvar / Baixar SVG Vetorial',
        act_print_doc: 'Imprimir / Pré-impressão (Ctrl+P)',
        act_cut: 'Recortar Seleção (Ctrl+X)',
        act_copy: 'Copiar Seleção (Ctrl+C)',
        act_paste: 'Colar da Área de Transferência (Ctrl+V)',
        act_undo: 'Desfazer Última Ação (Ctrl+Z)',
        act_redo: 'Refazer Ação (Ctrl+Y)',
        act_exp_svg: 'Baixar Vetor SVG',
        act_exp_pdf: 'Exportar Documento PDF',
        act_exp_png: 'Exportar PNG 300 DPI',
        act_exp_prepress: 'Pré-impressão com Sangria & Marcas de Corte (Ctrl+P)',
        act_zoom_in: 'Aumentar Zoom (Ctrl +)',
        act_zoom_out: 'Diminuir Zoom (Ctrl -)',
        act_zoom_100: 'Zoom 1:1 (100%)',
        act_zoom_fit: 'Enquadrar Folha Inteira na Tela',

        // Property Bar (Level 4)
        prop_landscape: 'Orientação Paisagem (Horizontal)',
        prop_portrait: 'Orientação Retrato (Vertical)',
        prop_pos_x: 'Posição Horizontal X',
        prop_pos_y: 'Posição Vertical Y',
        prop_width: 'Largura do Objeto',
        prop_height: 'Altura do Objeto',
        prop_angle: 'Ângulo de Rotação (Graus)',
        prop_flip_h: 'Espelhar Horizontalmente',
        prop_flip_v: 'Espelhar Verticalmente',
        prop_group_btn: 'Agrupar Objetos Selecionados (Ctrl+G)',
        prop_ungroup_btn: 'Desagrupar Objeto Selecionado (Ctrl+U)',
        prop_align_c: 'Centralizar Horizontalmente (C)',
        prop_align_e: 'Centralizar Verticalmente (E)',
        prop_align_p: 'Centralizar na Folha / Página (P)',
        prop_fountain_btn: 'Editar Degradê / Preenchimento Gradiente (F11)',
        prop_contour_btn: 'Criar Contorno / Borda de Corte Adesivo',
        prop_shadow_btn: 'Aplicar Sombra Projetada Suave (Drop Shadow)',
        prop_qrcode_btn: 'Gerar QR Code Vetorial SVG',
        prop_text_path_btn: 'Ajustar Texto ao Caminho (Curvar Texto)',
        prop_sep_path_btn: 'Separar Texto do Caminho',
        prop_dup_btn: 'Duplicar Objeto (Ctrl+D)',
        prop_repeat_btn: 'Repetir / Duplicar com Passo (Ctrl+R)',
        prop_trace_selected: 'Rastrear Bitmap',
        prop_trace_title: 'PowerTRACE: Rastrear e Vetorizar esta Imagem',

        // Toolbox (Left)
        tool_pick: 'Ferramenta Seleção (V / Espaço)',
        tool_shape: 'Ferramenta Forma / Edição de Nós (F10)',
        tool_zoom: 'Ferramenta Zoom (Z)',
        tool_pen: 'Ferramenta Caneta Bézier Curvas (P)',
        tool_rect: 'Ferramenta Retângulo (F6)',
        tool_ellipse: 'Ferramenta Elipse / Círculo (F7)',
        tool_poly: 'Ferramenta Polígono / Estrela (Y)',
        tool_text: 'Ferramenta Texto Artístico (F8)',
        tool_fountain: 'Preenchimento Gradiente Interativo (F11)',
        tool_contour: 'Ferramenta de Contorno / Borda de Adesivo',
        tool_shadow: 'Sombra Projetada Suave (Drop Shadow)',
        tool_qrcode: 'Gerador de QR Code Vetorial SVG',
        tool_pan: 'Ferramenta Panorâmica / Mão (H)',

        // Right Docker - Objects & Layers
        docker_title: 'Objetos / Camadas',
        docker_search_ph: 'Pesquisar objetos...',
        docker_blend_normal: 'Normal',
        docker_blend_multiply: 'Multiplicar',
        docker_blend_screen: 'Tela',
        docker_blend_overlay: 'Sobrepor',
        docker_btn_up: 'Mover camada para cima',
        docker_btn_down: 'Mover camada para baixo',
        docker_btn_lock: 'Bloquear / Destravar Objeto',
        docker_btn_del: 'Excluir Objeto',

        // Right Docker - Hints
        hints_title: 'Dicas do Corel',
        hints_default: 'Selecione qualquer ferramenta na caixa lateral para visualizar instruções rápidas e atalhos de produção.',

        // Bottom Status Bar
        status_help_text: 'Arraste as quinas para redimensionar mantendo proporção (SHIFT para distorcer livremente).',
        status_color_palette_name: 'Cores do Documento: CorelClone Pro 2026 (4u-labs)',

        // Layers Default Names
        layer_img_bitmap: 'Imagem Bitmap',
        layer_bezier_curve: 'Curva Bézier',
        layer_group: 'Grupo',
        layer_text_prefix: 'Texto',

        // Dialogs: PowerTRACE
        trace_modal_title: 'PowerTRACE™ — Rastreamento e Vetorização de Bitmap',
        trace_source_title: 'Imagem de Origem:',
        trace_preset_label: 'Modo de Rastreamento (Preset):',
        trace_opt_logo: 'Logotipo Detalhado (Curvas Suaves & Alta Nitidez)',
        trace_opt_clipart: 'Clipart / Silhueta (Formas Limpas & Sem Ruído)',
        trace_opt_fast: 'Traçado Rápido (Menor quantidade de nós)',
        trace_opt_hi_fi: 'Alta Fidelidade (Máxima precisão de detalhes e gradientes)',
        trace_colors_label: 'Quantidade de Cores do Vetor:',
        trace_noise_label: 'Redução de Ruído / Agrupamento de Pixels:',
        trace_corners_label: 'Suavização de Cantos & Curvas Bézier:',
        trace_remove_bg_label: 'Remover fundo branco da imagem automaticamente',
        trace_keep_orig_label: 'Manter imagem original embaixo do vetor gerado',
        trace_btn_cancel: 'Cancelar',
        trace_btn_apply: 'Vetorizar e Inserir na Prancheta',

        // Dialogs: Fountain Fill (F11)
        fountain_modal_title: 'Preenchimento Gradiente / Degradê (Fountain Fill — F11)',
        fountain_type_label: 'Tipo de Gradiente:',
        fountain_type_linear: 'Linear (Direcional)',
        fountain_type_radial: 'Radial (Circular do Centro)',
        fountain_angle_label: 'Ângulo da Direção:',
        fountain_color1_label: 'Cor Inicial:',
        fountain_color2_label: 'Cor Final:',
        fountain_color_mid_label: 'Cor Intermediária (Opcional):',
        fountain_presets_title: 'Predefinições de Estilo Corel:',
        preset_gold: 'Ouro Real',
        preset_silver: 'Prata Metálico',
        preset_cyan: 'Céu Tropical',
        preset_sunset: 'Sunset Laranja',
        preset_neon: 'Neon Ciberpunk',
        preset_fire: 'Fogo Escarlate',
        btn_apply_gradient: 'Aplicar Preenchimento Gradiente',

        // Dialogs: Contour Tool
        contour_modal_title: 'Ferramenta de Contorno & Linha de Corte (Contour Tool)',
        contour_dist_label: 'Deslocamento / Expansão:',
        contour_color_label: 'Cor de Preenchimento da Borda:',
        contour_corner_label: 'Cantos do Contorno:',
        contour_corner_round: 'Arredondados (Round)',
        contour_corner_miter: 'Em Esquadria (Miter / Canto Vivo)',
        contour_cutline_label: 'Gerar apenas Linha de Recorte Vermelha (Plotter / Vinil)',
        btn_apply_contour: 'Gerar Contorno Externo',

        // Dialogs: Drop Shadow
        shadow_modal_title: 'Sombra Projetada Suave (Drop Shadow)',
        shadow_dx_label: 'Deslocamento X (Horizontal):',
        shadow_dy_label: 'Deslocamento Y (Vertical):',
        shadow_blur_label: 'Suavidade / Desfoque (Blur):',
        shadow_opacity_label: 'Opacidade:',
        shadow_color_label: 'Cor da Sombra:',
        shadow_presets_title: 'Predefinições Rápidas:',
        shadow_pre_classic: 'Padrão Corel',
        shadow_pre_soft: 'Suave / Flutuante',
        shadow_pre_hard: 'Dura / Vinil',
        shadow_pre_cyan: 'Brilho Ciano Neon',
        shadow_pre_gold: 'Brilho Dourado',
        btn_remove_shadow: 'Remover Sombra',
        btn_apply_shadow: 'Aplicar Sombra Projetada',

        // Dialogs: QR Code
        qr_modal_title: 'Gerador de QR Code Vetorial SVG',
        qr_type_link: 'Link / Site (URL)',
        qr_type_pix: 'Chave PIX',
        qr_type_whatsapp: 'WhatsApp Direto',
        qr_type_text: 'Texto Livre',
        qr_color_label: 'Cor dos Módulos:',
        qr_bg_transp_label: 'Fundo Transparente (Sem quadrado branco)',
        btn_insert_qr: 'Inserir QR Code na Prancheta',

        // Dialogs: Prepress & Export
        prepress_modal_title: 'Pré-impressão Gráfica com Sangria & Marcas de Corte',
        prepress_bleed_label: 'Sangria Externa (Bleed):',
        prepress_marks_title: 'Marcas de Acabamento & Impressão:',
        prepress_opt_crop: 'Marcas de Corte (Crop Marks nos cantos para guilhotina)',
        prepress_opt_reg: 'Miras de Registro de Cores (Cruzes de alinhamento CMYK)',
        prepress_opt_bars: 'Barra de Calibração de Cores e Densidade CMYK',
        prepress_opt_job: 'Informações do Trabalho (Nome, dimensões e data na borda)',
        btn_print_pdf: 'Imprimir / Gerar PDF de Alta Resolução',
        btn_download_svg_prepress: 'Baixar SVG Gráfica',
        btn_download_png_300: 'Baixar PNG 300 DPI',

        // Dialogs: Download Desktop Modal
        desktop_modal_title: 'Escolha a melhor opção para seu trabalho',
        desktop_compare_title: 'Comparativo rápido entre as versões:',
        desktop_app_badge: 'Recomendado para Gráfica',
        desktop_app_heading: 'Aplicativo para Computador (Desktop)',
        desktop_app_desc: 'Possui o motor nativo completo: abre arquivos .CDR com 100% de nós Bézier e camadas originais prontas para edição, sem perda de qualidade.',
        desktop_app_feat1: 'Abre .CDR original em curvas (nós Bézier e camadas).',
        desktop_app_feat2: 'Vetoriza imagens PNG/JPG com PowerTRACE™.',
        desktop_app_feat3: '100% Offline e sem limites de arquivo.',
        desktop_download_exe: 'Baixar .EXE',
        desktop_download_dmg: 'Baixar .DMG',
        desktop_download_appimage: 'Baixar .AppImage',
        pwa_app_badge: 'Sem Download',
        pwa_app_heading: 'Ou instale como WebApp (PWA) direto no navegador',
        pwa_app_desc: 'Instalação instantânea com 1 clique (sem arquivos .exe). Cria artes, degradês, QR Code e vetoriza imagens PNG/JPG com o PowerTRACE™ normalmente.',
        pwa_app_feat1: 'Vetoriza imagens PNG/JPG com PowerTRACE™ (100% funcional).',
        pwa_app_feat2: 'Cria desenhos, formas, textos e exporta PDF/SVG.',
        pwa_app_feat3: 'Não lê curvas de .CDR (abre como imagem prévia).',
        pwa_note_text: 'Nota técnica: Arquivos .CDR abrem apenas como prévia visual no navegador. Se você precisa editar nós e curvas do .CDR original, use o App de Computador acima.',
        btn_install_pwa_now: 'Instalar WebApp',
        btn_close: 'Fechar',

        // Common Toasts & Feedback
        toast_copied: 'Objeto copiado para a área de transferência!',
        toast_pasted: 'Objeto colado na prancheta!',
        toast_deleted: 'Objeto excluído.',
        toast_saved: 'Estado do documento salvo.',
        toast_aligned: 'Alinhamento aplicado aos objetos selecionados.',
        toast_grouped: 'Objetos agrupados com sucesso (Ctrl+G).',
        toast_ungrouped: 'Objeto desagrupado (Ctrl+U).',
        toast_trace_success: 'Imagem vetorizada com sucesso pelo PowerTRACE™!',
        toast_repeat_done: 'Ação repetida com sucesso (Ctrl+R)!',

        // Modal keys
        trace_title: 'Corel PowerTRACE™ — Vetorizador de Bitmap',
        trace_no_image: 'Nenhuma imagem selecionada',
        trace_subtitle: 'Selecione um bitmap na tela ou escolha uma imagem do seu computador para vetorizar em curvas.',
        trace_choose_image: 'Escolher Imagem (PNG/JPG/WEBP)...',
        trace_mode_label: 'Modo de Rastreamento (Presets Corel):',
        trace_cancel: 'Cancelar',
        trace_run: 'Rastrear e Gerar Curvas Vetoriais',
        contour_title: 'Ferramenta Contorno / Borda de Corte (Contour)',
        contour_cancel: 'Cancelar',
        contour_apply: 'Aplicar Contorno',
        fountain_title: 'Preenchimento Gradiente (Fountain Fill — F11)',
        fountain_cancel: 'Cancelar',
        fountain_apply: 'Aplicar Gradiente',
        qr_title: 'Inserir Código QR Code Vetorial',
        qr_cancel: 'Cancelar',
        qr_insert: 'Inserir na Prancheta',
        shadow_title: 'Sombra Projetada (Drop Shadow)',
        shadow_cancel: 'Cancelar',
        shadow_apply: 'Aplicar Sombra',
        shadow_remove: 'Remover Sombra',
        prepress_title: 'Preparar para Impressão / Gráfica (Pré-impressão)',
        prepress_close: 'Fechar',
        prepress_svg: 'Baixar SVG',
        prepress_png: 'Baixar PNG 300 DPI',
        prepress_print: 'Imprimir / PDF Vetorial',
        bridge_title: 'CorelClone App Local (100% Vetorial)',
        bridge_continue: 'Continuar na Web',
        bridge_connect: 'Tentar Conectar',
        desktop_title: 'Escolha a melhor opção para seu trabalho',
        desktop_close: 'Fechar',
        pwa_install: 'Instalar WebApp',
        desktop_comp_title: 'Comparativo rápido entre as versões:',
        desktop_comp_desk_title: '🖥️ App de Computador (Desktop):',
        desktop_comp_desk_1: 'Abre .CDR original em curvas',
        desktop_comp_desk_1_sub: '(nós Bézier e camadas).',
        desktop_comp_desk_2: 'Vetoriza imagens PNG/JPG',
        desktop_comp_desk_2_sub: 'com PowerTRACE™.',
        desktop_comp_desk_3: '100% Offline',
        desktop_comp_desk_3_sub: 'e sem limites de arquivo.',
        desktop_comp_web_title: '🌐 WebApp / PWA (Navegador):',
        desktop_comp_web_1: 'Vetoriza imagens PNG/JPG',
        desktop_comp_web_1_sub: 'com PowerTRACE™ (100% funcional).',
        desktop_comp_web_2: 'Cria desenhos, formas, textos e exporta PDF/SVG.',
        desktop_comp_web_3: 'Não lê curvas de .CDR',
        desktop_comp_web_3_sub: '(abre como imagem prévia).',
        desktop_rec_tag: 'Recomendado para Gráfica',
        desktop_sec1_title: 'Aplicativo para Computador (Desktop)',
        desktop_sec1_desc: 'Possui o motor nativo completo: abre <code>.CDR</code> com <strong>100% de nós Bézier e camadas originais prontas para edição</strong>, sem perda de qualidade.',
        desktop_win_sub: 'Windows 10 / 11 (64-Bit)',
        desktop_win_btn: 'Baixar .EXE',
        desktop_mac_sub: 'Apple M1/M2/M3 & Intel',
        desktop_mac_btn: 'Baixar .DMG',
        desktop_linux_sub: 'Ubuntu, Zorin, Mint, Debian',
        desktop_linux_btn: 'Baixar .AppImage',
        desktop_sec2_tag: 'Sem Download',
        desktop_sec2_title: 'Ou instale como WebApp (PWA) direto no navegador',
        desktop_sec2_desc: 'Instalação instantânea com 1 clique (sem arquivos .exe). Cria artes, degradês, QR Code e <strong>vetoriza imagens PNG/JPG com o PowerTRACE™ normalmente</strong>.',
        desktop_sec2_note: 'ℹ️ <em>Nota técnica:</em> Arquivos <code>.CDR</code> abrem apenas como prévia visual no navegador. Se você precisa editar nós e curvas do <code>.CDR</code> original, use o <strong>App de Computador</strong> acima.'
    },

    en: {
        // App Title & Meta
        app_title: 'CorelClone Pro 2026 (64-Bit)',
        app_title_template: 'CorelClone Pro 2026 (64-Bit) — [{name}] @ {zoom}%',
        doc_default_name: 'Document 1',
        tab_start_page: 'Start Screen',
        btn_download_app: 'Get Desktop App',
        btn_download_app_title: 'Download Desktop Application for Computer (Windows, Mac or Linux)',
        welcome_toast: 'CorelClone Pro 2026 ready for vector creation!',
        lang_switched: 'Language switched to English.',

        // Top Menu - Arquivo / File
        menu_file: 'File',
        menu_new: 'New...',
        menu_open: 'Open...',
        menu_open_cdr: 'Import Corel (.CDR)...',
        menu_open_pdf: 'Import PDF...',
        menu_save_svg: 'Save',
        menu_export_svg: 'Export SVG...',
        menu_export_pdf: 'Export PDF...',
        menu_export_png: 'Export PNG...',
        menu_prepress: 'Prepress & Bleed...',
        menu_print: 'Print...',
        menu_download_desktop: 'Download Desktop App...',

        // Top Menu - Editar / Edit
        menu_edit: 'Edit',
        menu_undo: 'Undo',
        menu_redo: 'Redo',
        menu_cut: 'Cut',
        menu_copy: 'Copy',
        menu_paste: 'Paste',
        menu_delete: 'Delete',
        menu_duplicate: 'Duplicate',
        menu_repeat: 'Repeat Step / Step and Repeat',
        menu_select_all: 'Select All',

        // Top Menu - Exibir / View
        menu_view: 'View',
        menu_zoom_in: 'Zoom In (+)',
        menu_zoom_out: 'Zoom Out (-)',
        menu_zoom_100: 'Actual Size 100% (1:1)',
        menu_zoom_page: 'Fit Page to Window',
        menu_rulers: 'Show Calibrated Rulers',
        menu_guidelines: 'Show Magnetic Guidelines',
        menu_clear_guides: 'Clear All Guidelines',

        // Top Menu - Objeto / Object
        menu_object: 'Object',
        menu_group: 'Group Objects',
        menu_ungroup: 'Ungroup Object',
        menu_order: 'Layer Order',
        menu_bring_front: 'Bring to Front',
        menu_send_back: 'Send to Back',
        menu_forward: 'Forward One',
        menu_backward: 'Back One',
        menu_align: 'Align & Distribute',
        menu_align_center_h: 'Center Horizontally',
        menu_align_center_v: 'Center Vertically',
        menu_align_left: 'Align Left',
        menu_align_right: 'Align Right',
        menu_align_top: 'Align Top',
        menu_align_bottom: 'Align Bottom',
        menu_align_page: 'Center to Page',
        menu_flip_h: 'Mirror Horizontally',
        menu_flip_v: 'Mirror Vertically',
        menu_powerclip: 'PowerClip™ Container',
        menu_pc_place: 'Place Inside Container...',
        menu_pc_extract: 'Extract Content from Container',

        // Top Menu - Modelar / Effects
        menu_effects: 'Effects',
        menu_contour: 'Contour Tool (Sticker Cutline)...',
        menu_shadow: 'Soft Drop Shadow Filter...',
        menu_gradient: 'Fountain Fill / Gradient Editor...',
        menu_text_path: 'Fit Text to Path...',
        menu_qrcode: 'Insert Vector SVG QR Code...',

        // Top Menu - Bitmap
        menu_bitmap: 'Bitmap',
        menu_trace: 'PowerTRACE: Vectorize Bitmap...',
        menu_toggle_bg: 'Toggle / Hide Background Image',

        // Top Menu - Ajuda / Help
        menu_help: 'Help',
        menu_tutorial: 'Tutorial & Comprehensive Guide...',
        menu_shortcuts: 'Corel Keyboard Shortcuts Reference',
        menu_support: 'Technical Support & FAQ',
        menu_terms: 'Terms of Service',
        menu_privacy: 'Privacy Policy',
        menu_about: 'About CorelClone Pro',

        // Action Toolbar (Level 3)
        act_new_doc: 'New Document (Ctrl+N)',
        act_open: 'Open File (.CDR, .PDF, .SVG, Image)',
        act_save_svg: 'Save / Download Vector SVG',
        act_print_doc: 'Print / Prepress (Ctrl+P)',
        act_cut: 'Cut Selection (Ctrl+X)',
        act_copy: 'Copy Selection (Ctrl+C)',
        act_paste: 'Paste from Clipboard (Ctrl+V)',
        act_undo: 'Undo Last Action (Ctrl+Z)',
        act_redo: 'Redo Action (Ctrl+Y)',
        act_exp_svg: 'Download Vector SVG',
        act_exp_pdf: 'Export PDF Document',
        act_exp_png: 'Export High-Resolution PNG',
        act_exp_prepress: 'Prepress Output with Bleed & Marks (Ctrl+P)',
        act_zoom_in: 'Zoom In (Ctrl +)',
        act_zoom_out: 'Zoom Out (Ctrl -)',
        act_zoom_100: 'Zoom 1:1 (100%)',
        act_zoom_fit: 'Fit Entire Page on Screen',

        // Property Bar (Level 4)
        prop_landscape: 'Landscape Orientation (Horizontal)',
        prop_portrait: 'Portrait Orientation (Vertical)',
        prop_pos_x: 'Object X Position',
        prop_pos_y: 'Object Y Position',
        prop_width: 'Object Width',
        prop_height: 'Object Height',
        prop_angle: 'Rotation Angle (Degrees)',
        prop_flip_h: 'Mirror Horizontally',
        prop_flip_v: 'Mirror Vertically',
        prop_group_btn: 'Group Selected Objects (Ctrl+G)',
        prop_ungroup_btn: 'Ungroup Selected Object (Ctrl+U)',
        prop_align_c: 'Center Horizontally (C)',
        prop_align_e: 'Center Vertically (E)',
        prop_align_p: 'Center to Page (P)',
        prop_fountain_btn: 'Edit Fountain Fill / Gradient (F11)',
        prop_contour_btn: 'Create Contour / Sticker Cutline',
        prop_shadow_btn: 'Apply Soft Drop Shadow',
        prop_qrcode_btn: 'Generate Vector SVG QR Code',
        prop_text_path_btn: 'Fit Text to Path (Curved Text)',
        prop_sep_path_btn: 'Separate Text from Path',
        prop_dup_btn: 'Duplicate Object (Ctrl+D)',
        prop_repeat_btn: 'Step & Repeat / Multiply (Ctrl+R)',
        prop_trace_selected: 'Trace Bitmap',
        prop_trace_title: 'PowerTRACE: Trace and Vectorize this Bitmap Image',

        // Toolbox (Left)
        tool_pick: 'Pick Tool (V / Space)',
        tool_shape: 'Shape Tool / Node Edit (F10)',
        tool_zoom: 'Zoom Tool (Z)',
        tool_pen: 'Bézier Pen Tool (P)',
        tool_rect: 'Rectangle Tool (F6)',
        tool_ellipse: 'Ellipse / Circle Tool (F7)',
        tool_poly: 'Polygon / Star Tool (Y)',
        tool_text: 'Text Tool (F8)',
        tool_fountain: 'Interactive Fountain Fill (F11)',
        tool_contour: 'Contour Tool / Sticker Cutline',
        tool_shadow: 'Drop Shadow Tool',
        tool_qrcode: 'Vector SVG QR Code Generator',
        tool_pan: 'Pan / Hand Tool (H)',

        // Right Docker - Objects & Layers
        docker_title: 'Objects / Layers',
        docker_search_ph: 'Search objects...',
        docker_blend_normal: 'Normal',
        docker_blend_multiply: 'Multiply',
        docker_blend_screen: 'Screen',
        docker_blend_overlay: 'Overlay',
        docker_btn_up: 'Move layer up',
        docker_btn_down: 'Move layer down',
        docker_btn_lock: 'Lock / Unlock Object',
        docker_btn_del: 'Delete Object',

        // Right Docker - Hints
        hints_title: 'Corel Hints',
        hints_default: 'Select any tool in the toolbox to view real-time hints and production shortcuts.',

        // Bottom Status Bar
        status_help_text: 'Drag corners to resize while keeping aspect ratio (SHIFT to freely stretch).',
        status_color_palette_name: 'Document Colors: CorelClone Pro 2026 (4u-labs)',

        // Layers Default Names
        layer_img_bitmap: 'Bitmap Image',
        layer_bezier_curve: 'Bézier Curve',
        layer_group: 'Group',
        layer_text_prefix: 'Text',

        // Dialogs: PowerTRACE
        trace_modal_title: 'PowerTRACE™ — Bitmap Tracing & Vectorization',
        trace_source_title: 'Source Image:',
        trace_preset_label: 'Tracing Preset:',
        trace_opt_logo: 'Detailed Logo (Smooth Curves & Sharp Edges)',
        trace_opt_clipart: 'Clipart / Silhouette (Clean Shapes, No Noise)',
        trace_opt_fast: 'Quick Trace (Low Node Count)',
        trace_opt_hi_fi: 'High Fidelity (Maximum Precision & Rich Gradients)',
        trace_colors_label: 'Vector Color Count:',
        trace_noise_label: 'Noise Reduction / Pixel Grouping:',
        trace_corners_label: 'Corner & Bézier Curve Smoothing:',
        trace_remove_bg_label: 'Automatically remove white background',
        trace_keep_orig_label: 'Keep original bitmap beneath generated vector',
        trace_btn_cancel: 'Cancel',
        trace_btn_apply: 'Vectorize & Insert on Canvas',

        // Dialogs: Fountain Fill (F11)
        fountain_modal_title: 'Fountain Fill / Gradient Editor (F11)',
        fountain_type_label: 'Gradient Type:',
        fountain_type_linear: 'Linear (Directional)',
        fountain_type_radial: 'Radial (Circular from Center)',
        fountain_angle_label: 'Direction Angle:',
        fountain_color1_label: 'Starting Color:',
        fountain_color2_label: 'Ending Color:',
        fountain_color_mid_label: 'Midpoint Color (Optional):',
        fountain_presets_title: 'Corel Style Presets:',
        preset_gold: 'Royal Gold',
        preset_silver: 'Metallic Silver',
        preset_cyan: 'Tropical Sky',
        preset_sunset: 'Orange Sunset',
        preset_neon: 'Cyberpunk Neon',
        preset_fire: 'Scarlet Flame',
        btn_apply_gradient: 'Apply Fountain Fill',

        // Dialogs: Contour Tool
        contour_modal_title: 'Contour Tool & Vinyl Cutline Generator',
        contour_dist_label: 'Offset Distance / Spread:',
        contour_color_label: 'Contour Fill Color:',
        contour_corner_label: 'Contour Corner Joins:',
        contour_corner_round: 'Round Joins',
        contour_corner_miter: 'Miter Joins (Sharp Corners)',
        contour_cutline_label: 'Generate Red Hairline Cutline only (Plotter / Vinyl)',
        btn_apply_contour: 'Generate Outer Contour',

        // Dialogs: Drop Shadow
        shadow_modal_title: 'Soft Drop Shadow Filter',
        shadow_dx_label: 'X Offset (Horizontal):',
        shadow_dy_label: 'Y Offset (Vertical):',
        shadow_blur_label: 'Blur Softness (Feather):',
        shadow_opacity_label: 'Opacity:',
        shadow_color_label: 'Shadow Color:',
        shadow_presets_title: 'Quick Presets:',
        shadow_pre_classic: 'Corel Classic',
        shadow_pre_soft: 'Soft / Floating',
        shadow_pre_hard: 'Hard / Vinyl Decal',
        shadow_pre_cyan: 'Neon Cyan Glow',
        shadow_pre_gold: 'Golden Glow',
        btn_remove_shadow: 'Remove Shadow',
        btn_apply_shadow: 'Apply Drop Shadow',

        // Dialogs: QR Code
        qr_modal_title: 'Vector SVG QR Code Generator',
        qr_type_link: 'Link / Website (URL)',
        qr_type_pix: 'PIX Key (Brazil Instant Pay)',
        qr_type_whatsapp: 'Direct WhatsApp',
        qr_type_text: 'Free Text',
        qr_color_label: 'Module / Barcode Color:',
        qr_bg_transp_label: 'Transparent Background (No white box)',
        btn_insert_qr: 'Insert QR Code on Canvas',

        // Dialogs: Prepress & Export
        prepress_modal_title: 'Commercial Prepress Output with Bleed & Crop Marks',
        prepress_bleed_label: 'Outer Bleed:',
        prepress_marks_title: 'Finishing & Printer Marks:',
        prepress_opt_crop: 'Crop Marks (Corner trim lines for guillotine cutter)',
        prepress_opt_reg: 'Color Registration Targets (CMYK Alignment Crosshairs)',
        prepress_opt_bars: 'Color Calibration & Density Calibration Bars',
        prepress_opt_job: 'Job Information (Name, dimensions & timestamp outside trim)',
        btn_print_pdf: 'Print / Generate High-Resolution PDF',
        btn_download_svg_prepress: 'Download Prepress SVG',
        btn_download_png_300: 'Download 300 DPI PNG',

        // Dialogs: Download Desktop Modal
        desktop_modal_title: 'Choose the best option for your workflow',
        desktop_compare_title: 'Quick comparison between versions:',
        desktop_app_badge: 'Recommended for Print Shops',
        desktop_app_heading: 'Desktop Computer Application',
        desktop_app_desc: 'Features the complete native engine: opens .CDR files directly with 100% Bézier nodes and original layers ready for editing, without quality loss.',
        desktop_app_feat1: 'Opens original .CDR in native curves and layers.',
        desktop_app_feat2: 'Vectorizes PNG/JPG bitmaps with PowerTRACE™.',
        desktop_app_feat3: '100% Offline with no file size limits.',
        desktop_download_exe: 'Download .EXE',
        desktop_download_dmg: 'Download .DMG',
        desktop_download_appimage: 'Download .AppImage',
        pwa_app_badge: 'No Download',
        pwa_app_heading: 'Or install as WebApp (PWA) directly in browser',
        pwa_app_desc: 'Instant 1-click install (no .exe files). Create artwork, gradients, QR codes, and vectorize PNG/JPG images with PowerTRACE™ normally.',
        pwa_app_feat1: 'Vectorizes PNG/JPG images with PowerTRACE™ (100% functional).',
        pwa_app_feat2: 'Draw, style, edit text, and export PDF/SVG.',
        pwa_app_feat3: 'Does not read .CDR vector curves (opens thumbnail preview).',
        pwa_note_text: 'Technical note: .CDR files open only as image previews in the browser. If you need to edit curves and nodes of original .CDR files, use the Desktop App above.',
        btn_install_pwa_now: 'Install WebApp',
        btn_close: 'Close',

        // Common Toasts & Feedback
        toast_copied: 'Object copied to clipboard!',
        toast_pasted: 'Object pasted onto canvas!',
        toast_deleted: 'Object deleted.',
        toast_saved: 'Document state saved.',
        toast_aligned: 'Alignment applied to selected objects.',
        toast_grouped: 'Objects grouped successfully (Ctrl+G).',
        toast_ungrouped: 'Object ungrouped (Ctrl+U).',
        toast_trace_success: 'Bitmap successfully vectorized with PowerTRACE™!',
        toast_repeat_done: 'Step and repeat executed successfully (Ctrl+R)!',

        // Modal keys
        trace_title: 'Corel PowerTRACE™ — Bitmap Vectorizer',
        trace_no_image: 'No image selected',
        trace_subtitle: 'Select a bitmap on the canvas or choose an image from your computer to vectorize into curves.',
        trace_choose_image: 'Choose Image (PNG/JPG/WEBP)...',
        trace_mode_label: 'Tracing Mode (Corel Presets):',
        trace_cancel: 'Cancel',
        trace_run: 'Trace and Generate Vector Curves',
        contour_title: 'Contour / Cut Border Tool',
        contour_cancel: 'Cancel',
        contour_apply: 'Apply Contour',
        fountain_title: 'Fountain Fill (Gradient — F11)',
        fountain_cancel: 'Cancel',
        fountain_apply: 'Apply Gradient',
        qr_title: 'Insert Vector QR Code',
        qr_cancel: 'Cancel',
        qr_insert: 'Insert on Canvas',
        shadow_title: 'Drop Shadow',
        shadow_cancel: 'Cancel',
        shadow_apply: 'Apply Shadow',
        shadow_remove: 'Remove Shadow',
        prepress_title: 'Prepare for Print / Prepress',
        prepress_close: 'Close',
        prepress_svg: 'Download SVG',
        prepress_png: 'Download PNG 300 DPI',
        prepress_print: 'Print / Vector PDF',
        bridge_title: 'CorelClone Local App (100% Vector)',
        bridge_continue: 'Continue on Web',
        bridge_connect: 'Try to Connect',
        desktop_title: 'Choose the best option for your work',
        desktop_close: 'Close',
        pwa_install: 'Install WebApp',
        desktop_comp_title: 'Quick comparison between versions:',
        desktop_comp_desk_title: '🖥️ Desktop App (Computer):',
        desktop_comp_desk_1: 'Opens original .CDR in curves',
        desktop_comp_desk_1_sub: '(Bézier nodes and layers).',
        desktop_comp_desk_2: 'Vectorizes PNG/JPG images',
        desktop_comp_desk_2_sub: 'with PowerTRACE™.',
        desktop_comp_desk_3: '100% Offline',
        desktop_comp_desk_3_sub: 'with no file size limits.',
        desktop_comp_web_title: '🌐 WebApp / PWA (Browser):',
        desktop_comp_web_1: 'Vectorizes PNG/JPG images',
        desktop_comp_web_1_sub: 'with PowerTRACE™ (100% functional).',
        desktop_comp_web_2: 'Creates drawings, shapes, text & exports PDF/SVG.',
        desktop_comp_web_3: 'Cannot edit .CDR curves',
        desktop_comp_web_3_sub: '(opens as visual preview only).',
        desktop_rec_tag: 'Recommended for Print Shops',
        desktop_sec1_title: 'Computer Desktop Application',
        desktop_sec1_desc: 'Features the complete native engine: opens <code>.CDR</code> with <strong>100% original Bézier nodes and editable layers</strong>, with zero quality loss.',
        desktop_win_sub: 'Windows 10 / 11 (64-Bit)',
        desktop_win_btn: 'Download .EXE',
        desktop_mac_sub: 'Apple M1/M2/M3 & Intel',
        desktop_mac_btn: 'Download .DMG',
        desktop_linux_sub: 'Ubuntu, Zorin, Mint, Debian',
        desktop_linux_btn: 'Download .AppImage',
        desktop_sec2_tag: 'No Download Required',
        desktop_sec2_title: 'Or install as WebApp (PWA) directly in your browser',
        desktop_sec2_desc: 'Instant 1-click installation (no .exe needed). Create artwork, gradients, QR codes and <strong>vectorize PNG/JPG bitmaps with PowerTRACE™ normally</strong>.',
        desktop_sec2_note: 'ℹ️ <em>Technical note:</em> <code>.CDR</code> files open as visual previews only in the browser. If you need to edit original <code>.CDR</code> nodes and curves, please use the <strong>Desktop App</strong> above.'
    }
};

class CorelI18nManager {
    constructor() {
        this.currentLang = 'pt';
    }

    init() {
        let saved = 'pt';
        try {
            saved = localStorage.getItem('corelclone_lang');
            if (!saved) {
                const navLang = navigator.language || navigator.userLanguage || 'pt';
                saved = navLang.startsWith('en') ? 'en' : 'pt';
            }
        } catch (e) {}

        this.setLanguage(saved, false);
    }

    t(key, fallback = '') {
        const dict = COREL_I18N[this.currentLang] || COREL_I18N.pt;
        return dict[key] || COREL_I18N.pt[key] || fallback || key;
    }

    setLanguage(lang, triggerToast = true) {
        if (lang !== 'pt' && lang !== 'en') return;
        this.currentLang = lang;
        try {
            localStorage.setItem('corelclone_lang', lang);
        } catch (e) {}

        document.documentElement.lang = lang === 'pt' ? 'pt-BR' : 'en-US';
        document.documentElement.setAttribute('data-lang', lang);

        this.applyToDOM();

        const btnPt = document.getElementById('btnLangPT');
        const btnEn = document.getElementById('btnLangEN');
        if (btnPt) btnPt.classList.toggle('active', lang === 'pt');
        if (btnEn) btnEn.classList.toggle('active', lang === 'en');

        if (triggerToast && typeof window.toast === 'function') {
            window.toast(this.t('lang_switched'), 'ok');
        }
    }

    applyToDOM() {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = this.t(key);
            if (translation) {
                if (translation.includes('<') && translation.includes('>')) {
                    el.innerHTML = translation;
                } else {
                    el.textContent = translation;
                }
            }
        });

        document.querySelectorAll('[data-i18n-title]').forEach(el => {
            const key = el.getAttribute('data-i18n-title');
            const translation = this.t(key);
            if (translation) el.title = translation;
        });

        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            const translation = this.t(key);
            if (translation) el.placeholder = translation;
        });

        // Update document title if applicable
        const titleEl = document.getElementById('windowTitleText');
        if (titleEl && window.state) {
            const activePage = (window.state.pages && window.state.pages[window.state.activePageIndex]) || { name: this.t('doc_default_name') };
            const zoomPct = Math.round((window.state.zoom || 1.0) * 100);
            titleEl.textContent = `${this.t('app_title')} — [${activePage.name}] @ ${zoomPct}%`;
        }
    }
}

if (typeof window !== 'undefined') {
    window.corelI18n = new CorelI18nManager();
    window.t = (key, fallback) => window.corelI18n.t(key, fallback);
    window.setLanguage = (lang) => window.corelI18n.setLanguage(lang, true);
}

document.addEventListener('DOMContentLoaded', function() {
    let lang = 'pt';
    try {
        const urlLang = new URLSearchParams(window.location.search).get('lang');
        const saved = localStorage.getItem('corelclone_lang');
        if (urlLang === 'en' || urlLang === 'pt') lang = urlLang;
        else if (saved === 'en') lang = 'en';
    } catch(e) {}
    if (window.corelI18n) {
        window.corelI18n.setLanguage(lang, false);
    }
});
