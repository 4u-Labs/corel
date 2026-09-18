<?php
$v = time();
$baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
$msg_sent = false;
$msg_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $assunto = trim($_POST['assunto'] ?? 'Dúvida - CorelClone Pro');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (!empty($nome) && !empty($email) && !empty($mensagem)) {
        $logDir = __DIR__ . '/uploads';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/messages_log.json';
        $existing = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
        if (!is_array($existing)) $existing = [];

        $newMsg = [
            'timestamp' => date('Y-m-d H:i:s'),
            'nome' => $nome,
            'email' => $email,
            'assunto' => $assunto,
            'mensagem' => $mensagem,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        $existing[] = $newMsg;
        file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $to = 'contato@4u.ia.br';
        $headers = "From: contato@4u.ia.br\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body = "Nova mensagem de Suporte - CorelClone Pro 2026\n\nNome: $nome\nE-mail: $email\nAssunto: $assunto\n\nMensagem:\n$mensagem\n";

        @mail($to, "Suporte CorelClone: $assunto", $body, $headers);
        $msg_sent = true;
    } else {
        $msg_error = 'Por favor, preencha todos os campos obrigatórios.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Suporte & Central de Dúvidas — CorelClone Pro (4U.IA.BR)</title>
    <base href="<?php echo htmlspecialchars($baseDir); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .inst-page { max-width: 800px; margin: 40px auto; padding: 40px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(16px); border: 1px solid rgba(168, 85, 247, 0.2); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .inst-page h1 { font-size: 2rem; color: #fff; margin-bottom: 20px; background: linear-gradient(135deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .inst-page h2 { font-size: 1.3rem; color: #a855f7; margin: 25px 0 10px; }
        .inst-page p, .inst-page li { color: #cbd5e1; font-size: 0.95rem; line-height: 1.7; margin-bottom: 12px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; color: #c084fc; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
        .btn-back:hover { color: #fff; transform: translateX(-4px); }
        .form-contact { margin-top: 25px; display: flex; flex-direction: column; gap: 15px; }
        .form-contact input, .form-contact textarea { background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(168, 85, 247, 0.3); color: #fff; padding: 14px 18px; border-radius: 12px; font-family: inherit; font-size: 0.95rem; outline: none; }
        .form-contact input:focus, .form-contact textarea:focus { border-color: #a855f7; box-shadow: 0 0 15px rgba(168, 85, 247, 0.3); }
        .alert-success { background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #10b981; padding: 15px; border-radius: 12px; font-weight: 600; margin-bottom: 20px; }
        .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 12px; font-weight: 600; margin-bottom: 20px; }
        .faq-item { background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255, 255, 255, 0.05); padding: 18px 22px; border-radius: 16px; margin-bottom: 12px; }
        .faq-item h3 { color: #6366f1; font-size: 1.05rem; margin-bottom: 6px; }
    </style>
</head>
<body class="dark-theme">
    <div class="inst-page">
        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar ao CorelClone Pro</a>
        <h1><i class="fas fa-headset"></i> Suporte & Central de Ajuda</h1>
        
        <h2>Perguntas Frequentes (FAQ)</h2>
        <div class="faq-item">
            <h3> Como abrir arquivos .CDR do CorelDRAW?</h3>
            <p>Clique no botão <strong>"Abrir .CDR / SVG"</strong> no topo da página ou simplesmente arraste o arquivo do CorelDRAW (.cdr) diretamente para a área de trabalho.</p>
        </div>
        <div class="faq-item">
            <h3> Em quais formatos posso exportar meus trabalhos?</h3>
            <p>Você pode exportar seus arquivos em <strong>SVG Vetorizado</strong>, <strong>PNG HD com transparência</strong> e <strong>PDF para impressão</strong>.</p>
        </div>

        <h2>Fale Conosco</h2>
        <?php if ($msg_sent): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Sua mensagem foi enviada com sucesso! Nossa equipe responderá pelo e-mail <strong>contato@4u.ia.br</strong>.
            </div>
        <?php elseif (!empty($msg_error)): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($msg_error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-contact">
            <input type="text" name="nome" placeholder="Seu Nome Completo" required>
            <input type="email" name="email" placeholder="Seu E-mail de Contato" required>
            <input type="text" name="assunto" placeholder="Assunto (ex: Dúvida sobre arquivos CDR)">
            <textarea name="mensagem" rows="5" placeholder="Digite sua mensagem detalhada..." required></textarea>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Mensagem</button>
        </form>
    </div>
</body>
</html>
