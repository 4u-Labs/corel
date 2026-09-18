<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$v = time();
?><!DOCTYPE html>
<html lang="pt-BR" data-lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso — CorelClone</title>
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
        .section { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 28px 32px; margin-bottom: 16px; }
        .section h2 { font-size: 16px; font-weight: 700; color: var(--accent2); margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .section p { color: var(--muted); font-size: 14px; margin-bottom: 10px; }
        .section ul { color: var(--muted); font-size: 14px; margin: 8px 0 10px 18px; }
        .section li { margin-bottom: 6px; }
        .section code { background: #0b0f19; color: var(--accent); padding: 1px 6px; border-radius: 4px; font-family: monospace; font-size: 12.5px; }
        .highlight { background: #0f2a1a; border: 1px solid var(--green); border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; }
        .highlight p { color: #86efac; margin: 0; font-size: 14px; }
        .highlight strong { color: var(--green); }
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
        <a href="termos.php" class="active"><span data-lang="pt">Termos</span><span data-lang="en">Terms</span></a>
        <div class="lang-box">
            <button class="lang-btn" id="btnLangPT" onclick="setPageLang('pt')">PT</button>
            <button class="lang-btn" id="btnLangEN" onclick="setPageLang('en')">EN</button>
        </div>
    </div>
</nav>

<div class="hero">
    <h1>
        <span data-lang="pt"><i class="fas fa-file-contract"></i> Termos de Uso</span>
        <span data-lang="en"><i class="fas fa-file-contract"></i> Terms of Use</span>
    </h1>
    <p class="updated" data-lang="pt">Ultima atualizacao: <?php echo date('d/m/Y'); ?></p>
    <p class="updated" data-lang="en">Last updated: <?php echo date('Y-m-d'); ?></p>
</div>

<div class="container">

    <div class="highlight">
        <p data-lang="pt"><strong>Resumo executivo:</strong> O CorelClone e um software gratuito e de codigo aberto (licenca MIT). Voce pode usar, copiar e modificar livremente. Nenhum dado seu e enviado a nossos servidores — todo processamento ocorre no seu proprio navegador.</p>
        <p data-lang="en"><strong>Executive summary:</strong> CorelClone is free and open-source software (MIT license). You may freely use, copy and modify it. No data is sent to our servers — all processing happens in your own browser.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-code-branch"></i> <span data-lang="pt">1. Licenca de Software (MIT)</span><span data-lang="en">1. Software License (MIT)</span></h2>
        <p data-lang="pt">O CorelClone e distribuido sob a <strong>Licenca MIT</strong>. Isso significa que voce tem permissao para:</p>
        <p data-lang="en">CorelClone is distributed under the <strong>MIT License</strong>. This means you have permission to:</p>
        <ul data-lang="pt">
            <li>Usar o software para qualquer finalidade, comercial ou nao</li>
            <li>Copiar, modificar e distribuir o codigo-fonte</li>
            <li>Criar trabalhos derivados sem restricoes</li>
            <li>Sublicenciar ou vender copias do software</li>
        </ul>
        <ul data-lang="en">
            <li>Use the software for any purpose, commercial or not</li>
            <li>Copy, modify and distribute the source code</li>
            <li>Create derivative works without restriction</li>
            <li>Sublicense or sell copies of the software</li>
        </ul>
        <p data-lang="pt">A unica exigencia e manter o aviso de copyright original nas copias distribuidas.</p>
        <p data-lang="en">The only requirement is to retain the original copyright notice in distributed copies.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-shield-alt"></i> <span data-lang="pt">2. Arquitetura Zero-Knowledge</span><span data-lang="en">2. Zero-Knowledge Architecture</span></h2>
        <p data-lang="pt">O CorelClone foi projetado com privacidade em primeiro lugar:</p>
        <p data-lang="en">CorelClone was designed with privacy first:</p>
        <ul data-lang="pt">
            <li><strong>Nenhum arquivo e enviado a servidores:</strong> Toda vetorizacao (PowerTRACE), criacao de QR Codes, Fountain Fill e geracao de PDF ocorre exclusivamente no seu navegador.</li>
            <li><strong>Sem conta, sem login:</strong> Nao e necessario criar conta ou fazer login para usar o CorelClone.</li>
            <li><strong>Sem telemetria:</strong> Nao coletamos dados de uso, historico de arquivos ou metricas de comportamento.</li>
            <li><strong>Dados locais:</strong> O unico dado armazenado localmente e a preferencia de idioma (<code>corelclone_lang</code> no localStorage).</li>
        </ul>
        <ul data-lang="en">
            <li><strong>No files are sent to servers:</strong> All vectorization (PowerTRACE), QR Code creation, Fountain Fill and PDF generation happens exclusively in your browser.</li>
            <li><strong>No account, no login:</strong> No account creation or login is required to use CorelClone.</li>
            <li><strong>No telemetry:</strong> We do not collect usage data, file history or behavioral metrics.</li>
            <li><strong>Local data only:</strong> The only data stored locally is the language preference (<code>corelclone_lang</code> in localStorage).</li>
        </ul>
    </div>

    <div class="section">
        <h2><i class="fas fa-trademark"></i> <span data-lang="pt">3. Aviso de Marca Registrada</span><span data-lang="en">3. Trademark Disclaimer</span></h2>
        <p data-lang="pt">O CorelClone e um projeto independente e <strong>nao e afiliado, endossado ou patrocinado pela Corel Corporation</strong>. Os nomes "CorelDRAW" e "Corel" sao marcas registradas da Corel Corporation. O uso desses nomes neste contexto e meramente descritivo, indicando compatibilidade de formato de arquivo.</p>
        <p data-lang="en">CorelClone is an independent project and is <strong>not affiliated with, endorsed by, or sponsored by Corel Corporation</strong>. The names "CorelDRAW" and "Corel" are registered trademarks of Corel Corporation. Their use in this context is purely descriptive, indicating file format compatibility.</p>
        <p data-lang="pt">O nome "PowerTRACE" e usado para descrever funcionalidade de vetorizacao de bitmap similar a ferramenta homologa do CorelDRAW, nao implica afiliacao.</p>
        <p data-lang="en">The name "PowerTRACE" is used to describe bitmap vectorization functionality similar to the homologous CorelDRAW tool; it does not imply affiliation.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-user-check"></i> <span data-lang="pt">4. Uso Aceitavel</span><span data-lang="en">4. Acceptable Use</span></h2>
        <p data-lang="pt">Ao usar o CorelClone, voce concorda em:</p>
        <p data-lang="en">By using CorelClone, you agree to:</p>
        <ul data-lang="pt">
            <li>Usar o software de forma legal e etica</li>
            <li>Nao usar o CorelClone para criar, distribuir ou promover conteudo ilegal, ofensivo ou que viole direitos de terceiros</li>
            <li>Respeitar os direitos autorais de imagens e arquivos de terceiros que voce importar</li>
            <li>Nao tentar comprometer a seguranca ou integridade do servico</li>
        </ul>
        <ul data-lang="en">
            <li>Use the software legally and ethically</li>
            <li>Not use CorelClone to create, distribute or promote illegal, offensive or rights-infringing content</li>
            <li>Respect copyright of third-party images and files you import</li>
            <li>Not attempt to compromise the security or integrity of the service</li>
        </ul>
    </div>

    <div class="section">
        <h2><i class="fas fa-exclamation-triangle"></i> <span data-lang="pt">5. Limitacao de Responsabilidade</span><span data-lang="en">5. Limitation of Liability</span></h2>
        <p data-lang="pt">O CorelClone e fornecido <strong>"como esta"</strong> (as is), sem garantias de nenhum tipo, expressas ou implicitas. Os autores nao se responsabilizam por:</p>
        <p data-lang="en">CorelClone is provided <strong>"as is"</strong>, without warranties of any kind, express or implied. The authors are not liable for:</p>
        <ul data-lang="pt">
            <li>Perda de dados ou arquivos durante o uso</li>
            <li>Incompatibilidade com versoes especificas de CorelDRAW</li>
            <li>Danos diretos ou indiretos decorrentes do uso do software</li>
            <li>Interrupcoes de servico ou indisponibilidade</li>
        </ul>
        <ul data-lang="en">
            <li>Loss of data or files during use</li>
            <li>Incompatibility with specific versions of CorelDRAW</li>
            <li>Direct or indirect damages resulting from software use</li>
            <li>Service interruptions or unavailability</li>
        </ul>
    </div>

    <div class="section">
        <h2><i class="fas fa-balance-scale"></i> <span data-lang="pt">6. Conformidade com LGPD / GDPR</span><span data-lang="en">6. LGPD / GDPR Compliance</span></h2>
        <p data-lang="pt">Por nao coletarmos dados pessoais identificaveis durante o uso normal do CorelClone, a conformidade com a LGPD (Lei n 13.709/2018) e o GDPR europeu e alcancada pela propria arquitetura Zero-Knowledge do sistema. O formulario de contato coleta apenas nome e e-mail voluntariamente fornecidos pelo usuario, usados exclusivamente para responder a mensagem.</p>
        <p data-lang="en">As we do not collect personally identifiable data during normal CorelClone use, compliance with LGPD (Law No. 13,709/2018) and European GDPR is achieved by the system's own Zero-Knowledge architecture. The contact form collects only name and email voluntarily provided by the user, used exclusively to respond to their message.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-sync-alt"></i> <span data-lang="pt">7. Alteracoes nestes Termos</span><span data-lang="en">7. Changes to These Terms</span></h2>
        <p data-lang="pt">Podemos atualizar estes Termos de Uso periodicamente. A data de ultima atualizacao sera sempre exibida no topo desta pagina. O uso continuado do CorelClone apos alteracoes implica aceitacao dos novos termos.</p>
        <p data-lang="en">We may update these Terms of Use periodically. The last update date will always be shown at the top of this page. Continued use of CorelClone after changes implies acceptance of the new terms.</p>
    </div>

    <div class="section">
        <h2><i class="fas fa-envelope"></i> <span data-lang="pt">8. Contato</span><span data-lang="en">8. Contact</span></h2>
        <p data-lang="pt">Para duvidas sobre estes Termos de Uso, entre em contato pelo e-mail <strong>contato@4u.ia.br</strong> ou acesse nossa <a href="suporte.php" style="color:var(--accent);">Central de Suporte</a>.</p>
        <p data-lang="en">For questions about these Terms of Use, contact us at <strong>contato@4u.ia.br</strong> or visit our <a href="suporte.php" style="color:var(--accent);">Support Center</a>.</p>
    </div>

</div>

<footer>
    <p>
        <a href="index.php">CorelClone</a> &nbsp;&middot;&nbsp;
        <a href="tutorial.php">Tutorial</a> &nbsp;&middot;&nbsp;
        <a href="suporte.php"><span data-lang="pt">Suporte</span><span data-lang="en">Support</span></a> &nbsp;&middot;&nbsp;
        <a href="privacidade.php"><span data-lang="pt">Privacidade</span><span data-lang="en">Privacy</span></a>
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