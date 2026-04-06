<?php
session_start();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $senha = $_POST['senha'] ?? '';

    if ($senha === 'Bb1234567') {
        $_SESSION['admin_access_granted'] = true;
        header('Location: cadAdmin.php');
        exit();
    } else {
        $erro = 'Senha incorreta. Tente novamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Admin - Regionais RS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; min-height: 100vh; display: flex; justify-content: center; align-items: center; background: linear-gradient(135deg, #b71c1c, #d32f2f); color: white; }
        .box { width: 100%; max-width: 420px; background: rgba(255,255,255,0.95); border-radius: 16px; padding: 35px 30px; box-shadow: 0 15px 45px rgba(0,0,0,0.25); }
        h1 { color: #b71c1c; font-size: 2rem; margin-bottom: 18px; text-align: center; }
        p { color: #333; margin-bottom: 24px; text-align: center; }
        .erro { background: #ffcdd2; color: #b71c1c; padding: 12px 14px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        input { width: 100%; padding: 14px 16px; margin-bottom: 18px; border-radius: 10px; border: 1px solid #ddd; font-size: 1rem; }
        input:focus { outline: none; border-color: #b71c1c; }
        button { width: 100%; padding: 14px 16px; border: none; border-radius: 10px; background: #b71c1c; color: white; font-size: 1rem; font-weight: bold; cursor: pointer; transition: 0.2s; }
        button:hover { background: #8b0000; }
        .voltar { display: block; margin-top: 18px; text-align: center; color: #b71c1c; text-decoration: none; }
        .voltar:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔐 Acesso Admin</h1>
        <p>Digite a senha de acesso para cadastrar um novo administrador.</p>

        <?php if (!empty($erro)): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="post" action="admin_auth.php">
            <input type="password" name="senha" placeholder="Senha de acesso" required autofocus>
            <button type="submit">Entrar</button>
        </form>

        <a href="index.php" class="voltar">← Voltar à página inicial</a>
    </div>
</body>
</html>
