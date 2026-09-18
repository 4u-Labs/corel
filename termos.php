<?php
$v = time();
$baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso — CorelClone Pro (4U.IA.BR)</title>
    <base href="<?php echo htmlspecialchars($baseDir); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .inst-page { max-width: 800px; margin: 40px auto; padding: 40px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(16px); border: 1px solid rgba(168, 85, 247, 0.2); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .inst-page h1 { font-size: 2rem; color: #fff; margin-bottom: 20px; background: linear-gradient(135deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .inst-page h2 { font-size: 1.2rem; color: #a855f7; margin: 25px 0 10px; }
        .inst-page p { color: #cbd5e1; font-size: 0.95rem; line-height: 1.7; margin-bottom: 12px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; color: #c084fc; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body class="dark-theme">
    <div class="inst-page">
        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar ao CorelClone Pro</a>
        <h1>Termos de Uso</h1>
        <p>Ao utilizar o <strong>CorelClone Pro 2026</strong>, você concorda com os seguintes termos e condições de uso da suíte vetorial da <strong>4U.IA.BR</strong>.</p>
        <h2>1. Licença de Uso</h2>
        <p>A aplicação é fornecida para criação, edição e exportação de artes gráficas, vetores e logotipos para uso pessoal ou comercial.</p>
        <h2>2. Propriedade Intelectual</h2>
        <p>As marcas registradas CorelDRAW® pertencem à Corel Corporation. O CorelClone Pro é uma ferramenta de edição gráfica web independente desenvolvida pela 4uLabs.</p>
    </div>
</body>
</html>
