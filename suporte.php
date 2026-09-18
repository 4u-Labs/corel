<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
$v = time();
$msg_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = htmlspecialchars(trim($_POST['nome']     ?? ''));
    $email    = filter_var(trim($_POST['email']    ?? ''), FILTER_SANITIZE_EMAIL);
    $assunto  = htmlspecialchars(trim($_POST['assunto']  ?? 'Suporte CorelClone'));
    $mensagem = htmlspecialchars(trim($_POST['mensagem'] ?? ''));
    if (!empty($nome) && !empty($email) && !empty($mensagem) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $log_dir = __DIR__ . '/uploads';
        if (!is_dir($log_dir)) { @mkdir($log_dir, 0755, true); }
        @file_put_contents($log_dir . '/messages_log.json',
            json_encode(['timestamp'=>date('c'),'app'=>'corelclone','nome'=>$nome,'email'=>$email,'assunto'=>$assunto,'mensagem'=>$mensagem,'ip'=>$_SERVER['REMOTE_ADDR']??''],
                JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND);
        $to = 'contato@4u.ia.br';
        $headers = "From: contato@4u.ia.br\nReply-To: {$email}\nContent-Type: text/plain; charset=UTF-8";
        $body = "Suporte CorelClone\nNome: {$nome}\nE-mail: {$email}\nAssunto: {$assunto}\n\n{$mensagem}";
        @mail($to, 'Suporte CorelClone: '.$assunto, $body, $headers);
        $msg_status = 'success';
    } else {
        $msg_status = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Suporte &amp; FAQ — CorelClone</title>
    <meta name="description" content="Central de Suporte e FAQ do CorelClone. Duvidas sobre CDR, PowerTRACE, Fountain Fill, Prepress e mais.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0b0f19; --card: #131b2e; --card2: #1a2540;
            --accent: #38bdf8; --accent2: #818cf8;
            --green: #34d399; --red: #f87171; --amber: #fbbf24;
            --text: #e2e8f0; --muted: #94a3b8; --border: #1e3a5f;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; min-height: 100vh; }
        nav { background: var(--card); border-bottom: 1px solid var(--border); padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 56px; position: sticky; top: 0; z-index: 100; }
        .nav-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; color: var(--accent); text-decoration: none; }
        .nav-brand img { height: 28px; }
        .nav-links { display: flex; align-items: center; gap: 16px; }
        .nav-links a { color: var(--muted); text-decoration: none; font-size: 13px; font-weight: 500; transition: color .2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--accent); }
        .lang-box { display: flex; gap: 4px; background: #0b0f19; border: 1px solid var(--border); border-radius: 20px; padding: 3px; }
        .lang-btn { border: none; background: transparent; color: var(--muted); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 14px; cursor: pointer; transition: all .2s; }
        .lang-btn:hover { color: var(--text); }
        .lang-btn.active { background: var(--accent); color: #000; }
        html[data-lang="pt"] [data-lang="en"] { display: none !important; }
        html[data-lang="en"] [data-lang="pt"] { display: none !important; }
        .hero { padding: 60px 24px 40px; text-align: center; }
        .hero h1 { font-size: clamp(28px, 5vw, 44px); font-weight: 800; color: var(--accent); margin-bottom: 12px; }
        .hero p { color: var(--muted); font-size: 16px; max-width: 620px; margin: 0 auto; }
        .container { max-width: 860px; margin: 0 auto; padding: 0 20px 80px; }
        .section-title { font-size: 13px; font-weight: 700; color: var(--accent2); text-transform: uppercase; letter-spacing: .08em; margin: 40px 0 16px; display: flex; align-items: center; gap: 8px; }
        .section-title::after { content:''; flex:1; height:1px; background: var(--border); }
        .faq-item { background: var(--card); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 8px; overflow: hidden; }
        .faq-q { padding: 14px 18px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 12px; user-select: none; transition: background .2s; }
        .faq-q:hover { background: var(--card2); }
        .faq-q .icon { color: var(--accent); font-size: 13px; transition: transform .25s; flex-shrink: 0; }
        .faq-item.open .faq-q .icon { transform: rotate(180deg); }
        .faq-a { display: none; padding: 0 18px 16px; font-size: 13.5px; color: var(--muted); border-top: 1px solid var(--border); line-height: 1.65; }
        .faq-a.visible { display: block; }
        .faq-a p { margin-top: 12px; }
        .faq-a ul { margin: 10px 0 0 18px; }
        .faq-a li { margin-bottom: 6px; }
        .faq-a code { background: #0b0f19; color: var(--accent); padding: 1px 6px; border-radius: 4px; font-family: monospace; font-size: 12.5px; }
        .faq-a .tip { background: #0f2a1a; border: 1px solid #34d399; border-radius: 6px; padding: 10px 14px; margin-top: 12px; color: #86efac; font-size: 12.5px; }
        .faq-a .warn { background: #2a1a0f; border: 1px solid var(--amber); border-radius: 6px; padding: 10px 14px; margin-top: 12px; color: #fde68a; font-size: 12.5px; }
        .contact-box { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 32px; margin-top: 50px; }
        .contact-box h2 { font-size: 20px; font-weight: 700; color: var(--accent); margin-bottom: 6px; }
        .contact-box p { color: var(--muted); font-size: 14px; margin-bottom: 20px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; background: #0b0f19; border: 1px solid var(--border); border-radius: 6px;
            color: var(--text); padding: 9px 12px; font-size: 13.5px; font-family: 'Inter', sans-serif; outline: none; transition: border-color .2s;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--accent); }
        .form-group textarea { min-height: 110px; resize: vertical; }
        .btn-submit { background: var(--accent); color: #000; border: none; border-radius: 7px; padding: 11px 28px; font-size: 14px; font-weight: 700; cursor: pointer; transition: opacity .2s; }
        .btn-submit:hover { opacity: .85; }
        .msg-success { background: #0f2a1a; border: 1px solid var(--green); color: var(--green); border-radius: 7px; padding: 12px 16px; margin-bottom: 16px; font-size: 14px; }
        .msg-error { background: #2a0f0f; border: 1px solid var(--red); color: var(--red); border-radius: 7px; padding: 12px 16px; margin-bottom: 16px; font-size: 14px; }
        footer { text-align: center; padding: 24px; font-size: 12px; color: var(--muted); border-top: 1px solid var(--border); }
        footer a { color: var(--accent); text-decoration: none; }
    </style>
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">
        <img src="corelicon.png" alt="CorelClone">
        CorelClone
    </a>
    <div class="nav-links">
        <a href="index.php">App</a>
        <a href="tutorial.php">Tutorial</a>
        <a href="suporte.php" class="active"><span data-lang="pt">Suporte</span><span data-lang="en">Support</span></a>
        <div class="lang-box">
            <button class="lang-btn" id="btnLangPT" onclick="setPageLang('pt')">PT</button>
            <button class="lang-btn" id="btnLangEN" onclick="setPageLang('en')">EN</button>
        </div>
    </div>
</nav>

<div class="hero">
    <h1>
        <span data-lang="pt"><i class="fas fa-life-ring"></i> Suporte &amp; FAQ</span>
        <span data-lang="en"><i class="fas fa-life-ring"></i> Support &amp; FAQ</span>
    </h1>
    <p data-lang="pt">Respostas para as duvidas mais comuns sobre o CorelClone — vetorizacao, arquivos CDR, prepress e muito mais.</p>
    <p data-lang="en">Answers to the most common questions about CorelClone — vectorization, CDR files, prepress and much more.</p>
</div>

<div class="container">

    <div class="section-title">
        <i class="fas fa-file-code"></i>
        <span data-lang="pt">Arquivos CDR e Compatibilidade</span>
        <span data-lang="en">CDR Files &amp; Compatibility</span>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">O CorelClone abre arquivos .CDR do CorelDRAW?</span>
                <span data-lang="en">Does CorelClone open .CDR files from CorelDRAW?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Depende da versao que voce esta usando:</p>
            <p data-lang="en">It depends on which version you are using:</p>
            <ul data-lang="pt">
                <li><strong>App de Computador (Desktop):</strong> Abre <code>.CDR</code> com 100% de nos Bezier, curvas vetoriais e camadas reais prontas para edicao.</li>
                <li><strong>WebApp / PWA (Navegador):</strong> Abre o <code>.CDR</code> apenas como <strong>previa visual (imagem)</strong>. Os nos e curvas nao ficam disponiveis para edicao vetorial.</li>
            </ul>
            <ul data-lang="en">
                <li><strong>Desktop App:</strong> Opens <code>.CDR</code> with 100% Bezier nodes, original vector curves and real layers ready for editing.</li>
                <li><strong>WebApp / PWA (Browser):</strong> Opens <code>.CDR</code> as a <strong>visual preview (image)</strong> only. Nodes and curves are not available for vector editing.</li>
            </ul>
            <div class="tip" data-lang="pt">&#128161; Se voce precisa editar nos e camadas de um <code>.CDR</code>, use o <strong>App de Computador</strong> (Windows, Mac e Linux).</div>
            <div class="tip" data-lang="en">&#128161; If you need to edit nodes and layers of a <code>.CDR</code> file, use the <strong>Desktop App</strong> (Windows, Mac and Linux).</div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">O CorelClone abre SVG, PDF e outros formatos?</span>
                <span data-lang="en">Does CorelClone open SVG, PDF and other formats?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Sim! O CorelClone suporta abertura de:</p>
            <p data-lang="en">Yes! CorelClone supports opening:</p>
            <ul data-lang="pt">
                <li><code>.SVG</code> — Abertura completa com edicao vetorial de nos, grupos e texto</li>
                <li><code>.PNG / .JPG / .WEBP</code> — Importacao como bitmap (use o PowerTRACE para vetorizar)</li>
                <li><code>.CDR</code> — Previa visual no WebApp; edicao completa no App Desktop</li>
            </ul>
            <ul data-lang="en">
                <li><code>.SVG</code> — Full opening with vector node, group and text editing</li>
                <li><code>.PNG / .JPG / .WEBP</code> — Import as bitmap (use PowerTRACE to vectorize)</li>
                <li><code>.CDR</code> — Visual preview in WebApp; full editing in Desktop App</li>
            </ul>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como exportar meu trabalho do CorelClone?</span>
                <span data-lang="en">How do I export my work from CorelClone?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <ul data-lang="pt">
                <li><code>Ctrl+S</code> — Salvar como <code>.SVG</code> (vetorial, editavel em qualquer software)</li>
                <li><code>Ctrl+E</code> — Exportar como PNG em alta resolucao</li>
                <li><code>Ctrl+P</code> — Preparar para impressao: PDF vetorial com sangria e marcas de corte</li>
            </ul>
            <ul data-lang="en">
                <li><code>Ctrl+S</code> — Save as <code>.SVG</code> (vector, editable in any software)</li>
                <li><code>Ctrl+E</code> — Export as high-resolution PNG</li>
                <li><code>Ctrl+P</code> — Prepare for print: vector PDF with bleed and crop marks</li>
            </ul>
        </div>
    </div>

    <div class="section-title">
        <i class="fas fa-bolt"></i>
        <span data-lang="pt">PowerTRACE — Vetorizacao de Imagem</span>
        <span data-lang="en">PowerTRACE — Image Vectorization</span>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">O PowerTRACE funciona no WebApp (navegador)?</span>
                <span data-lang="en">Does PowerTRACE work in the WebApp (browser)?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt"><strong>Sim, 100% funcional!</strong> O PowerTRACE vetoriza imagens PNG, JPG e WEBP diretamente no navegador, sem instalacao. Todo o processamento e feito localmente no seu computador.</p>
            <p data-lang="en"><strong>Yes, 100% functional!</strong> PowerTRACE vectorizes PNG, JPG and WEBP images directly in the browser, without any installation. All processing is done locally on your computer.</p>
            <div class="tip" data-lang="pt">&#128161; O unico recurso que requer o App Desktop e a abertura de <code>.CDR</code> com nos e curvas editaveis.</div>
            <div class="tip" data-lang="en">&#128161; The only feature requiring the Desktop App is opening <code>.CDR</code> files with editable nodes and curves.</div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Qual preset do PowerTRACE devo usar para minha logo?</span>
                <span data-lang="en">Which PowerTRACE preset should I use for my logo?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <ul data-lang="pt">
                <li><strong>Logotipo / Clipart</strong> — Cores solidas e contornos nitidos. Ideal para plotagem e serigrafia.</li>
                <li><strong>Arte de Linha (P&amp;B)</strong> — Silhuetas, carimbos e assinaturas em preto e branco.</li>
                <li><strong>Logotipo Detalhado</strong> — Logos com mais cores e formas complexas.</li>
                <li><strong>Alta Fidelidade (Foto)</strong> — Ilustracoes e fotos multicoloridas com muitos detalhes.</li>
            </ul>
            <ul data-lang="en">
                <li><strong>Logo / Clipart</strong> — Solid colors and sharp outlines. Ideal for plotters and screen printing.</li>
                <li><strong>Line Art (B&amp;W)</strong> — Silhouettes, stamps and signatures in black and white.</li>
                <li><strong>Detailed Logo</strong> — Logos with more colors and complex shapes.</li>
                <li><strong>High Fidelity (Photo)</strong> — Colorful illustrations and photos with many details.</li>
            </ul>
            <div class="tip" data-lang="pt">&#128161; Para melhores resultados, use imagens com fundo branco ou transparente e pelo menos 300px de resolucao.</div>
            <div class="tip" data-lang="en">&#128161; For best results, use images with a white or transparent background and at least 300px resolution.</div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">O resultado da vetorizacao ficou com muitos nos. Como resolver?</span>
                <span data-lang="en">The vectorization result has too many nodes. How to fix?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <ul data-lang="pt">
                <li>Escolha o preset <strong>Logotipo / Clipart</strong> (menos nos)</li>
                <li>Aumente o <strong>Nivel de Suavizacao</strong> para "Alta (Linhas Suaves)"</li>
                <li>Reduza o <strong>Numero Maximo de Cores</strong> (ex.: 4-6 cores)</li>
                <li>Ative <strong>Remover cor de fundo</strong> para eliminar ruidos externos</li>
            </ul>
            <ul data-lang="en">
                <li>Choose the <strong>Logo / Clipart</strong> preset (fewer nodes)</li>
                <li>Increase <strong>Smoothness Level</strong> to "High (Smooth Lines)"</li>
                <li>Reduce <strong>Max Colors</strong> (e.g. 4-6 colors)</li>
                <li>Enable <strong>Remove background color</strong> to eliminate noise</li>
            </ul>
        </div>
    </div>

    <div class="section-title">
        <i class="fas fa-tools"></i>
        <span data-lang="pt">Ferramentas — Fountain Fill, Contorno, QR Code</span>
        <span data-lang="en">Tools — Fountain Fill, Contour, QR Code</span>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como aplicar um preenchimento gradiente (Fountain Fill)?</span>
                <span data-lang="en">How do I apply a gradient fill (Fountain Fill)?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Selecione o objeto e pressione <code>F11</code> ou acesse <strong>Objeto &gt; Fountain Fill</strong>. Escolha o tipo (Linear ou Radial), o angulo, as cores inicial e final, e clique em <strong>Aplicar Gradiente</strong>.</p>
            <p data-lang="en">Select the object and press <code>F11</code> or go to <strong>Object &gt; Fountain Fill</strong>. Choose the type (Linear or Radial), the angle, the start and end colors, and click <strong>Apply Gradient</strong>.</p>
            <div class="tip" data-lang="pt">&#128161; Use os presets rapidos (Ouro Real, Prata, Esmeralda...) para resultados profissionais em 1 clique.</div>
            <div class="tip" data-lang="en">&#128161; Use quick presets (Royal Gold, Silver, Emerald...) for professional results in 1 click.</div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como criar uma linha de corte para ploter (Contour)?</span>
                <span data-lang="en">How do I create a plotter cut line (Contour)?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Selecione o objeto, acesse <strong>Efeitos &gt; Contorno (Borda de Corte)</strong>. Escolha o estilo <strong>"Linha de Corte / Plotter"</strong>, defina a espessura em mm, selecione a cor Magenta e clique em Aplicar.</p>
            <p data-lang="en">Select the object, go to <strong>Effects &gt; Contour (Cut Border)</strong>. Choose style <strong>"Cut Line / Plotter"</strong>, set thickness in mm, select Magenta color and click Apply.</p>
            <div class="warn" data-lang="pt">&#9888; A cor Magenta (<code>#ff00ff</code>) e o padrao de linha de corte reconhecido pela maioria das plotadoras e softwares RIP.</div>
            <div class="warn" data-lang="en">&#9888; Magenta (<code>#ff00ff</code>) is the cut line standard recognized by most cutting plotters and RIP software.</div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como inserir um QR Code vetorial no design?</span>
                <span data-lang="en">How do I insert a vector QR Code in the design?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Acesse <strong>Objeto &gt; Inserir QR Code Vetorial</strong>. Selecione o tipo de conteudo (URL, WhatsApp, PIX ou Texto), preencha os dados, configure o tamanho e a cor, e clique em <strong>Inserir na Prancheta</strong>. O QR Code e gerado como vetor SVG 100% escalavel.</p>
            <p data-lang="en">Go to <strong>Object &gt; Insert Vector QR Code</strong>. Select the content type (URL, WhatsApp, PIX or Text), fill in the data, configure size and color, and click <strong>Insert on Canvas</strong>. The QR Code is generated as a 100% scalable SVG vector.</p>
        </div>
    </div>

    <div class="section-title">
        <i class="fas fa-print"></i>
        <span data-lang="pt">Pre-impressao, Sangria e Grafica</span>
        <span data-lang="en">Prepress, Bleed &amp; Print</span>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">O que e sangria (bleed) e por que preciso dela?</span>
                <span data-lang="en">What is bleed and why do I need it?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Sangria e uma expansao da area de impressao alem dos limites do arquivo. Garante que, apos o corte na guilhotina, nao aparecam bordas brancas no produto final. O padrao e 3mm para impressao offset e ate 5mm para grande formato.</p>
            <p data-lang="en">Bleed is an extension of the print area beyond the file boundaries. It ensures that, after guillotine cutting, no unwanted white edges appear on the final product. Standard is 3mm for offset printing and up to 5mm for large format.</p>
            <div class="tip" data-lang="pt">&#128161; Use <code>Ctrl+P</code> para abrir o painel de Pre-impressao e configurar sangria e marcas de corte automaticamente.</div>
            <div class="tip" data-lang="en">&#128161; Use <code>Ctrl+P</code> to open the Prepress panel and configure bleed and crop marks automatically.</div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como exportar o arquivo para a grafica com sangria e marcas?</span>
                <span data-lang="en">How do I export the file for the printer with bleed and marks?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Acesse <strong>Arquivo &gt; Pre-impressao</strong> ou pressione <code>Ctrl+P</code>. Configure a sangria (3mm padrao), marcas de corte, miras de registro CMYK e barra de cores. Depois clique em <strong>Imprimir / PDF Vetorial</strong>.</p>
            <p data-lang="en">Go to <strong>File &gt; Prepress</strong> or press <code>Ctrl+P</code>. Configure bleed (3mm standard), crop marks, CMYK registration marks and color bar. Then click <strong>Print / Vector PDF</strong>.</p>
        </div>
    </div>

    <div class="section-title">
        <i class="fas fa-mobile-alt"></i>
        <span data-lang="pt">Instalacao como PWA / App de Computador</span>
        <span data-lang="en">PWA Installation / Desktop App</span>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como instalar o CorelClone como PWA no navegador?</span>
                <span data-lang="en">How do I install CorelClone as a PWA in the browser?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">No Chrome ou Edge, abra <strong>4u.ia.br/app/corel/</strong> e clique no icone de instalacao na barra de endereco. Ou clique em <strong>"Baixar / Instalar App"</strong> no canto superior direito e escolha <strong>"Instalar WebApp"</strong>. O PowerTRACE funciona normalmente na versao PWA!</p>
            <p data-lang="en">In Chrome or Edge, open <strong>4u.ia.br/app/corel/</strong> and click the install icon in the address bar. Or click <strong>"Download / Install App"</strong> in the top-right corner and choose <strong>"Install WebApp"</strong>. PowerTRACE works normally in the PWA version!</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
            <span>
                <span data-lang="pt">Como baixar e instalar o App de Computador (Desktop)?</span>
                <span data-lang="en">How do I download and install the Desktop App?</span>
            </span>
            <i class="fas fa-chevron-down icon"></i>
        </div>
        <div class="faq-a">
            <p data-lang="pt">Clique em <strong>"Baixar / Instalar App"</strong> no CorelClone e escolha a versao do seu sistema:</p>
            <p data-lang="en">Click <strong>"Download / Install App"</strong> in CorelClone and choose your system version:</p>
            <ul data-lang="pt">
                <li><strong>Windows:</strong> Baixar <code>CorelClone-Setup.exe</code> e instalar normalmente</li>
                <li><strong>macOS:</strong> Baixar <code>CorelClone.dmg</code>, montar e arrastar para Aplicativos</li>
                <li><strong>Linux:</strong> Baixar <code>CorelClone.AppImage</code>, dar permissao <code>chmod +x</code> e executar</li>
            </ul>
            <ul data-lang="en">
                <li><strong>Windows:</strong> Download <code>CorelClone-Setup.exe</code> and install normally</li>
                <li><strong>macOS:</strong> Download <code>CorelClone.dmg</code>, mount and drag to Applications</li>
                <li><strong>Linux:</strong> Download <code>CorelClone.AppImage</code>, run <code>chmod +x</code> and execute</li>
            </ul>
        </div>
    </div>

    <!-- CONTACT FORM -->
    <div class="contact-box">
        <h2 data-lang="pt"><i class="fas fa-envelope"></i> Nao encontrou sua resposta? Fale conosco</h2>
        <h2 data-lang="en"><i class="fas fa-envelope"></i> Didn't find your answer? Contact us</h2>
        <p data-lang="pt">Nossa equipe responde em ate 24h uteis.</p>
        <p data-lang="en">Our team responds within 24 business hours.</p>
<?php if ($msg_status === 'success'): ?>
        <div class="msg-success">
            <span data-lang="pt">Mensagem enviada com sucesso! Responderemos em breve.</span>
            <span data-lang="en">Message sent successfully! We will respond shortly.</span>
        </div>
<?php elseif ($msg_status === 'error'): ?>
        <div class="msg-error">
            <span data-lang="pt">Preencha todos os campos corretamente.</span>
            <span data-lang="en">Please fill in all fields correctly.</span>
        </div>
<?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label data-lang="pt">Nome completo</label><label data-lang="en">Full name</label>
                <input type="text" name="nome" required placeholder="Seu nome / Your name">
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>
            <div class="form-group">
                <label data-lang="pt">Assunto</label><label data-lang="en">Subject</label>
                <input type="text" name="assunto" placeholder="Ex: Duvida sobre PowerTRACE / PowerTRACE question">
            </div>
            <div class="form-group">
                <label data-lang="pt">Mensagem</label><label data-lang="en">Message</label>
                <textarea name="mensagem" required placeholder="Descreva sua duvida / Describe your question..."></textarea>
            </div>
            <button type="submit" class="btn-submit">
                <span data-lang="pt"><i class="fas fa-paper-plane"></i> Enviar Mensagem</span>
                <span data-lang="en"><i class="fas fa-paper-plane"></i> Send Message</span>
            </button>
        </form>
    </div>

</div>

<footer>
    <p>
        <a href="index.php">CorelClone</a> &nbsp;&middot;&nbsp;
        <a href="tutorial.php">Tutorial</a> &nbsp;&middot;&nbsp;
        <a href="termos.php"><span data-lang="pt">Termos</span><span data-lang="en">Terms</span></a> &nbsp;&middot;&nbsp;
        <a href="privacidade.php"><span data-lang="pt">Privacidade</span><span data-lang="en">Privacy</span></a>
    </p>
    <p style="margin-top:6px;" data-lang="pt">&copy; <?php echo date('Y'); ?> 4u Labs &nbsp;&middot;&nbsp; CorelClone e independente e nao afiliado a Corel Corporation.</p>
    <p style="margin-top:6px;" data-lang="en">&copy; <?php echo date('Y'); ?> 4u Labs &nbsp;&middot;&nbsp; CorelClone is independent and not affiliated with Corel Corporation.</p>
</footer>

<script>
function toggleFaq(el) {
    const item = el.closest('.faq-item');
    const ans = item.querySelector('.faq-a');
    const open = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => {
        i.classList.remove('open');
        i.querySelector('.faq-a').classList.remove('visible');
    });
    if (!open) { item.classList.add('open'); ans.classList.add('visible'); }
}
function setPageLang(lang) {
    document.documentElement.setAttribute('data-lang', lang);
    try { localStorage.setItem('corelclone_lang', lang); } catch(e) {}
    document.getElementById('btnLangPT').classList.toggle('active', lang === 'pt');
    document.getElementById('btnLangEN').classList.toggle('active', lang === 'en');
}
(function() {
    let lang = 'pt';
    try {
        const url = new URLSearchParams(window.location.search).get('lang');
        const saved = localStorage.getItem('corelclone_lang');
        if (url === 'en' || url === 'pt') lang = url;
        else if (saved === 'en') lang = 'en';
    } catch(e) {}
    setPageLang(lang);
})();
</script>
</body>
</html>