<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$v = time();
?><!DOCTYPE html>
<html lang="pt-BR" data-lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politica de Privacidade — CorelClone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#0b0f19; --card:#131b2e; --accent:#38bdf8; --accent2:#818cf8; --green:#34d399; --text:#e2e8f0; --muted:#94a3b8; --border:#1e3a5f; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.7; min-height: 100vh; }
        nav { background: var(--card); border-bottom: 1px solid var(--border); padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 56px; position: sticky; top: 0; z-index: 100; }
        .nav-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; color: var(--accent); text-decoration: none; }
        .nav-brand img { height: 28px; }
        .nav-links { display: flex; align-items: center; gap: 16px; }
        .nav-links a { color: var(--muted); text-decoration: none; font-size: 13px; font-weight: 500; transition: color .2s; }
        .nav-links a:hover, .nav-links a.active { color: var(--accent); }
        .lang-box { display: flex; gap: 4px; background: #0b0f19; border: 1px solid var(--border); border-radius: 20px; padding: 3px; }
        .lang-btn { border: none; background: transparent; color: var(--muted); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 14px; cursor: pointer; transition: all .2s; }
        .lang-btn.active { background: var(--accent); color: #000; }
        html[data-lang="pt"] [data-lang="en"] { display: none !important; }
        html[data-lang="en"] [data-lang="pt"] { display: none !important; }
        .hero { padding: 60px 24px 32px; text-align: center; }
        .hero h1 { font-size: clamp(26px, 4vw, 40px); font-weight: 800; color: var(--accent); margin-bottom: 8px; }
        .hero .updated { color: var(--muted); font-size: 13px; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 20px 80px; }
        .badge-privacy { display: flex; align-items: center; gap: 12px; background: #0f2a1a; border: 1px solid var(--green); border-radius: 10px; padding: 18px 24px; margin-bottom: 24px; }
        .badge-privacy i { font-size: 32px; color: var(--green); flex-shrink: 0; }
        .badge-privacy p { color: #86efac; font-size: 14px; margin: 0; }
        .badge-privacy strong { color: var(--green); }
        .section { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 28px 32px; margin-bottom: 16px; }
        .section h2 { font-size: 16px; font-weight: 700; color: var(--accent2); margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .section p { color: var(--muted); font-size: 14px; margin-bottom: 10px; }
        .section ul { color: var(--muted); font-size: 14px; margin: 8px 0 10px 18px; }
        .section li { margin-bottom: 6px; }
        .section code { background: #0b0f19; color: var(--accent); padding: 1px 6px; border-radius: 4px; font-family: monospace; font-size: 12.5px; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; margin: 12px 0; }
        th { background: #0b0f19; color: var(--accent2); font-weight: 700; padding: 10px 14px; text-align: left; border: 1px solid var(--border); }
        td { padding: 9px 14px; border: 1px solid var(--border); color: var(--muted); }
        tr:nth-child(even) td { background: #0d1624; }
        .no-collect { color: var(--green); font-weight: 600; }
        footer { text-align: center; padding: 24px; font-size: 12px; color: var(--muted); border-top: 1px solid var(--border); }
        footer a { color: var(--accent); text-decoration: none; }
    </style>
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">
        <img src="corelicon.png" alt="CorelClone">CorelClone
    </a>
    <div class="nav-links">
        <a href="index.php">App</a>
        <a href="tutorial.php">Tutorial</a>
        <a href="suporte.php"><span data-lang="pt">Suporte</span><span data-lang="en">Support</span></a>
        <a href="privacidade.php" class="active"><span data-lang="pt">Privacidade</span><span data-lang="en">Privacy</span></a>
        <div class="lang-box">
            <button class="lang-btn" id="btnLangPT" onclick="setPageLang('pt')">PT</button>
            <button class="lang-btn" id="btnLangEN" onclick="setPageLang('en')">EN</button>
        </div>
    </div>
</nav>

<div class="hero">
    <h1>
        <span data-lang="pt"><i class="fas fa-user-shield"></i> Politica de Privacidade</span>
        <span data-lang="en"><i class="fas fa-user-shield"></i> Privacy Policy</span>
    </h1>
    <p class="updated" data-lang="pt">Ultima atualizacao: <?php echo date('d/m/Y'); ?></p>
    <p class="updated" data-lang="en">Last updated: <?php echo date('Y-m-d'); ?></p>
</div>

<div class="container">

    <div class="badge-privacy">
        <i class="fas fa-lock"></i>
        <p data-lang="pt"><strong>Arquitetura Zero-Knowledge:</strong> O CorelClone nao coleta, nao armazena e nao transmite seus arquivos ou dados pessoais a nenhum servidor. Todo o processamento de imagens, vetorizacao e geracao de documentos ocorre 100% localmente no seu navegador.</p>
        <p data-lang="en"><strong>Zero-Knowledge Architecture:</strong> CorelClone does not collect, store or transmit your files or personal data to any server. All image processing, vectorization and document generation happens 100% locally in your browser.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-database"></i> <span data-lang="pt">1. Dados que NÃO Coletamos</span><span data-lang="en">1. Data We Do NOT Collect</span></h2>
        <p data-lang="pt">Durante o uso normal do CorelClone, nao coletamos:</p>
        <p data-lang="en">During normal CorelClone use, we do not collect:</p>
        <table data-lang="pt">
            <tr><th>Tipo de Dado</th><th>Coletado?</th></tr>
            <tr><td>Arquivos abertos ou criados (CDR, SVG, PNG, JPG)</td><td class="no-collect">NÃO</td></tr>
            <tr><td>Historico de arquivos ou projetos</td><td class="no-collect">NÃO</td></tr>
            <tr><td>Dados de imagens vetorizadas pelo PowerTRACE</td><td class="no-collect">NÃO</td></tr>
            <tr><td>Nome, email ou dados de identificacao pessoal</td><td class="no-collect">NÃO (exceto formulario de contato voluntario)</td></tr>
            <tr><td>Endereco IP ou localizacao geografica</td><td class="no-collect">NÃO</td></tr>
            <tr><td>Cookies de rastreamento ou publicidade</td><td class="no-collect">NÃO</td></tr>
            <tr><td>Telemetria de uso ou metricas de comportamento</td><td class="no-collect">NÃO</td></tr>
        </table>
        <table data-lang="en">
            <tr><th>Data Type</th><th>Collected?</th></tr>
            <tr><td>Files opened or created (CDR, SVG, PNG, JPG)</td><td class="no-collect">NO</td></tr>
            <tr><td>File or project history</td><td class="no-collect">NO</td></tr>
            <tr><td>Image data vectorized by PowerTRACE</td><td class="no-collect">NO</td></tr>
            <tr><td>Name, email or personal identification data</td><td class="no-collect">NO (except voluntary contact form)</td></tr>
            <tr><td>IP address or geographic location</td><td class="no-collect">NO</td></tr>
            <tr><td>Tracking or advertising cookies</td><td class="no-collect">NO</td></tr>
            <tr><td>Usage telemetry or behavioral metrics</td><td class="no-collect">NO</td></tr>
        </table>
    </div>

    <div class="section">
        <h2><i class="fas fa-hdd"></i> <span data-lang="pt">2. Armazenamento Local (localStorage)</span><span data-lang="en">2. Local Storage (localStorage)</span></h2>
        <p data-lang="pt">O CorelClone utiliza apenas o <strong>localStorage</strong> do seu navegador para armazenar a preferencia de idioma. Isso e armazenado exclusivamente no seu dispositivo e nunca e transmitido a nenhum servidor.</p>
        <p data-lang="en">CorelClone uses only your browser's <strong>localStorage</strong> to store the language preference. This is stored exclusively on your device and is never transmitted to any server.</p>
        <table data-lang="pt">
            <tr><th>Chave localStorage</th><th>Dado Armazenado</th><th>Finalidade</th></tr>
            <tr><td><code>corelclone_lang</code></td><td>"pt" ou "en"</td><td>Lembrar o idioma preferido da interface</td></tr>
        </table>
        <table data-lang="en">
            <tr><th>localStorage Key</th><th>Stored Data</th><th>Purpose</th></tr>
            <tr><td><code>corelclone_lang</code></td><td>"pt" or "en"</td><td>Remember preferred interface language</td></tr>
        </table>
        <p data-lang="pt" style="margin-top:10px;">Voce pode limpar esses dados a qualquer momento nas configuracoes do seu navegador.</p>
        <p data-lang="en" style="margin-top:10px;">You can clear this data at any time in your browser settings.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-envelope-open-text"></i> <span data-lang="pt">3. Formulario de Contato</span><span data-lang="en">3. Contact Form</span></h2>
        <p data-lang="pt">Ao preencher o formulario de contato na pagina de Suporte, voce nos fornece voluntariamente:</p>
        <p data-lang="en">When filling out the contact form on the Support page, you voluntarily provide us with:</p>
        <ul data-lang="pt">
            <li><strong>Nome</strong> — para personalizar nossa resposta</li>
            <li><strong>E-mail</strong> — para responder a sua mensagem</li>
            <li><strong>Mensagem</strong> — o conteudo da sua duvida ou sugestao</li>
        </ul>
        <ul data-lang="en">
            <li><strong>Name</strong> — to personalize our response</li>
            <li><strong>Email</strong> — to reply to your message</li>
            <li><strong>Message</strong> — the content of your question or suggestion</li>
        </ul>
        <p data-lang="pt">Esses dados sao usados <strong>exclusivamente</strong> para responder a sua mensagem e nao sao compartilhados com terceiros.</p>
        <p data-lang="en">This data is used <strong>exclusively</strong> to respond to your message and is not shared with third parties.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-server"></i> <span data-lang="pt">4. Processamento Local — Como Funciona</span><span data-lang="en">4. Local Processing — How It Works</span></h2>
        <p data-lang="pt">Todos os recursos principais do CorelClone funcionam 100% localmente no seu navegador:</p>
        <p data-lang="en">All main CorelClone features work 100% locally in your browser:</p>
        <ul data-lang="pt">
            <li><strong>PowerTRACE:</strong> A vetorizacao de imagens PNG/JPG e realizada por algoritmos JavaScript no seu computador. Nenhuma imagem e enviada a servidores externos.</li>
            <li><strong>QR Code Vetorial:</strong> Os QR Codes sao gerados localmente como SVG. O conteudo (URL, WhatsApp, PIX) nao e transmitido.</li>
            <li><strong>Fountain Fill / Gradientes:</strong> Processamento 100% local no canvas do navegador.</li>
            <li><strong>Exportacao PDF / SVG / PNG:</strong> Os arquivos sao gerados localmente e baixados diretamente para o seu computador.</li>
        </ul>
        <ul data-lang="en">
            <li><strong>PowerTRACE:</strong> PNG/JPG image vectorization is performed by JavaScript algorithms on your computer. No image is sent to external servers.</li>
            <li><strong>Vector QR Code:</strong> QR Codes are generated locally as SVG. The content (URL, WhatsApp, PIX) is not transmitted.</li>
            <li><strong>Fountain Fill / Gradients:</strong> 100% local processing in the browser canvas.</li>
            <li><strong>PDF / SVG / PNG Export:</strong> Files are generated locally and downloaded directly to your computer.</li>
        </ul>
    </div>

    <div class="section">
        <h2><i class="fas fa-balance-scale"></i> <span data-lang="pt">5. Conformidade LGPD e GDPR</span><span data-lang="en">5. LGPD and GDPR Compliance</span></h2>
        <p data-lang="pt">O CorelClone esta em conformidade com a <strong>Lei Geral de Protecao de Dados (LGPD) — Lei n 13.709/2018</strong> e o <strong>Regulamento Geral de Protecao de Dados da Uniao Europeia (GDPR)</strong>. Por nao coletarmos dados pessoais durante o uso normal, a privacidade e garantida pela propria arquitetura do sistema.</p>
        <p data-lang="en">CorelClone complies with the <strong>Brazilian General Data Protection Law (LGPD) — Law No. 13,709/2018</strong> and the <strong>European Union General Data Protection Regulation (GDPR)</strong>. As we do not collect personal data during normal use, privacy is guaranteed by the system's own architecture.</p>
        <p data-lang="pt">Seus direitos como titular de dados incluem: acesso, correcao, exclusao e portabilidade dos dados eventualmente fornecidos via formulario de contato. Para exercer esses direitos, entre em contato: <strong>contato@4u.ia.br</strong>.</p>
        <p data-lang="en">Your data subject rights include: access, correction, deletion and portability of data eventually provided via the contact form. To exercise these rights, contact: <strong>contato@4u.ia.br</strong>.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-link"></i> <span data-lang="pt">6. Links Externos e Terceiros</span><span data-lang="en">6. External Links and Third Parties</span></h2>
        <p data-lang="pt">O CorelClone carrega recursos de terceiros para funcionamento:</p>
        <p data-lang="en">CorelClone loads third-party resources for operation:</p>
        <ul data-lang="pt">
            <li><strong>Google Fonts</strong> (fonts.googleapis.com) — Carregamento da fonte Inter</li>
            <li><strong>Font Awesome CDN</strong> (cdnjs.cloudflare.com) — Icones da interface</li>
        </ul>
        <ul data-lang="en">
            <li><strong>Google Fonts</strong> (fonts.googleapis.com) — Inter font loading</li>
            <li><strong>Font Awesome CDN</strong> (cdnjs.cloudflare.com) — Interface icons</li>
        </ul>
        <p data-lang="pt">Esses servicos podem registrar seu endeco IP ao carregar os recursos. Consulte as politicas de privacidade do <a href="https://policies.google.com/privacy" target="_blank" style="color:var(--accent);">Google</a> e da <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" style="color:var(--accent);">Cloudflare</a> para mais detalhes.</p>
        <p data-lang="en">These services may log your IP address when loading resources. See the privacy policies of <a href="https://policies.google.com/privacy" target="_blank" style="color:var(--accent);">Google</a> and <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" style="color:var(--accent);">Cloudflare</a> for more details.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-envelope"></i> <span data-lang="pt">7. Contato e DPO</span><span data-lang="en">7. Contact and DPO</span></h2>
        <p data-lang="pt">Para questoes relacionadas a privacidade e protecao de dados, entre em contato:</p>
        <p data-lang="en">For privacy and data protection questions, contact:</p>
        <ul>
            <li>E-mail: <strong>contato@4u.ia.br</strong></li>
            <li><a href="suporte.php" style="color:var(--accent);" data-lang="pt">Central de Suporte CorelClone</a><a href="suporte.php" style="color:var(--accent);" data-lang="en">CorelClone Support Center</a></li>
        </ul>
    </div>

</div>

<footer>
    <p>
        <a href="index.php">CorelClone</a> &nbsp;&middot;&nbsp;
        <a href="tutorial.php">Tutorial</a> &nbsp;&middot;&nbsp;
        <a href="suporte.php"><span data-lang="pt">Suporte</span><span data-lang="en">Support</span></a> &nbsp;&middot;&nbsp;
        <a href="termos.php"><span data-lang="pt">Termos</span><span data-lang="en">Terms</span></a>
    </p>
    <p style="margin-top:6px;" data-lang="pt">&copy; <?php echo date('Y'); ?> 4u Labs &nbsp;&middot;&nbsp; CorelClone e independente e nao afiliado a Corel Corporation.</p>
    <p style="margin-top:6px;" data-lang="en">&copy; <?php echo date('Y'); ?> 4u Labs &nbsp;&middot;&nbsp; CorelClone is independent and not affiliated with Corel Corporation.</p>
</footer>

<script>
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