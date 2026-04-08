<?php
session_start();
include 'conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo_acesso = $_POST['tipo_acesso']; // 'publico', 'reporter' ou 'admin'

    if (!empty($email) && !empty($senha) && !empty($tipo_acesso)) {
        
        // Define a tabela e o redirecionamento conforme o tipo selecionado
        switch($tipo_acesso) {
            case 'admin':
                $tabela = 'admins';
                $pagina_destino = 'indexAdmin.php';
                break;
            case 'reporter':
                $tabela = 'reporters';
                $pagina_destino = 'indexReporter.php';
                break;
            default: // publico
                $tabela = 'usuarios';
                $pagina_destino = 'indexPublico.php';
        }

        // Busca usuário na tabela correta
        $stmt = $conn->prepare("SELECT id, nome, email, senha FROM $tabela WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_assoc();
            
            // Verifica a senha criptografada
            if (password_verify($senha, $usuario['senha'])) {
                // ✅ LOGIN SUCESSO - Cria a sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo'] = $tipo_acesso;
                
                header("Location: $pagina_destino");
                exit();
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "E-mail não encontrado como $tipo_acesso!";
        }
        $stmt->close();
    } else {
        $erro = "Preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Regionais RS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: linear-gradient(135deg, #1a1a2e, #16213e);
        }
        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
            width: 400px;
        }
        .logo {
            text-align: center;
            color: #b71c1c;
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 30px;
        }
        h2 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 25px;
        }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #555;
        }
        input, select { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            font-size: 15px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #b71c1c;
        }
        button { 
            width: 100%; 
            padding: 15px; 
            background: linear-gradient(135deg, #b71c1c, #c62828); 
            color: white; 
            border: none; 
            border-radius: 8px;
            cursor: pointer; 
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            transition: 0.3s;
        }
        button:hover { 
            background: linear-gradient(135deg, #8b0000, #b71c1c);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(183, 28, 28, 0.4);
        }
        .erro { 
            background: #ffebee; 
            color: #b71c1c; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center;
            border-left: 4px solid #b71c1c;
        }
        .links {
            margin-top: 25px;
            text-align: center;
            font-size: 0.9em;
        }
        .links a {
            color: #b71c1c;
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">📰 Regionais RS</div>
        <h2>🔐 Acessar Conta</h2>
        
        <?php if(!empty($erro)) { echo "<div class='erro'>$erro</div>"; } ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Tipo de Acesso:</label>
                <select name="tipo_acesso" required>
                    <option value="">Selecione...</option>
                    <option value="publico">👤 Leitor (Público)</option>
                    <option value="reporter">✍️ Repórter</option>
                    <option value="admin">⚙️ Administrador</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>E-mail:</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>

             <!-- Botão de voltar -->
       
            
            <button type="submit">🚀 Entrar</button>
        </form>

         <!-- Botão de voltar -->
        <a href="index.php" class="voltar">← Voltar</a>
        
        <div class="links">
            <p>Não tem conta?</p>
            <a href="cadPublico.php">Cadastre-se como Leitor</a> | 
            <a href="cadReporter.php">Cadastre-se como Repórter</a> | 
            <a href="cadAdmin.php">Cadastre-se como Admin</a>
        </div>
    </div>
</body>
</html>